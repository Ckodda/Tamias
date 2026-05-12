-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "PendingExpensesPkg";

-- Procedimiento para actualizar un gasto pendiente (campos opcionales)
CREATE OR REPLACE PROCEDURE "PendingExpensesPkg"."UpdatePendingExpense"(
    "P_Id" BIGINT,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_ExpenseDescription" TEXT DEFAULT NULL,
    "P_TotalAmount" NUMERIC DEFAULT NULL,
    "P_DueDate" DATE DEFAULT NULL,
    "P_ProviderName" VARCHAR DEFAULT NULL,
    "P_PaymentStatus" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdatePendingExpense'
)
AS $$
BEGIN
    -- 1. Validar si el gasto pendiente existe
    IF NOT EXISTS (SELECT 1 FROM "PendingExpenses" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 5 AS "ErrorId", 'El gasto pendiente no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el centro de costo existe (si se proporciona)
    IF "P_CostCenterId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar que el monto sea positivo (si se proporciona)
    IF "P_TotalAmount" IS NOT NULL AND "P_TotalAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El monto total debe ser mayor a cero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Validar el estado del pago (si se proporciona)
    IF "P_PaymentStatus" IS NOT NULL AND "P_PaymentStatus" NOT IN ('Pending', 'Paid', 'Cancelled') THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El estado de pago debe ser Pending, Paid o Cancelled.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Validar que la descripción no sea una cadena vacía (si se proporciona)
    IF "P_ExpenseDescription" IS NOT NULL AND TRIM("P_ExpenseDescription") = '' THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'La descripción del gasto no puede estar vacía.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 6. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "PendingExpenses"
        SET
            "CostCenterId" = COALESCE("P_CostCenterId", "CostCenterId"),
            "ExpenseDescription" = COALESCE("P_ExpenseDescription", "ExpenseDescription"),
            "TotalAmount" = COALESCE("P_TotalAmount", "TotalAmount"),
            "DueDate" = COALESCE("P_DueDate", "DueDate"),
            "ProviderName" = COALESCE("P_ProviderName", "ProviderName"),
            "PaymentStatus" = COALESCE("P_PaymentStatus", "PaymentStatus"),
            "IsActive" = COALESCE("P_IsActive", "IsActive"),
            "UpdatedBy" = "P_UpdatedBy",
            "UpdatedAt" = NOW()
        WHERE "Id" = "P_Id"
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Updated";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
