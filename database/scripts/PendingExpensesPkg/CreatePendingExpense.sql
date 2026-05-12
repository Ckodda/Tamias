-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "PendingExpensesPkg";

-- Procedimiento para registrar un gasto pendiente
CREATE OR REPLACE PROCEDURE "PendingExpensesPkg"."CreatePendingExpense"(
    "P_CostCenterId" BIGINT,
    "P_ExpenseDescription" TEXT,
    "P_TotalAmount" NUMERIC,
    "P_DueDate" DATE,
    "P_ProviderName" VARCHAR,
    "P_PaymentStatus" VARCHAR,
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_PendingExpense'
)
AS $$
BEGIN
    -- 1. Validar si el centro de costo existe
    IF NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar que el monto sea positivo
    IF "P_TotalAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El monto total debe ser mayor a cero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar el estado del pago (Constraint de la tabla)
    IF "P_PaymentStatus" NOT IN ('Pending', 'Paid', 'Cancelled') THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El estado de pago debe ser Pending, Paid o Cancelled.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Validar que la descripción no esté vacía
    IF TRIM("P_ExpenseDescription") = '' THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'La descripción del gasto es obligatoria.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Éxito: Insertar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "PendingExpenses" (
            "CostCenterId",
            "ExpenseDescription",
            "TotalAmount",
            "DueDate",
            "ProviderName",
            "PaymentStatus",
            "IsActive",
            "CreatedBy",
            "UpdatedBy",
            "CreatedAt",
            "UpdatedAt"
        )
        VALUES (
            "P_CostCenterId",
            "P_ExpenseDescription",
            "P_TotalAmount",
            "P_DueDate",
            "P_ProviderName",
            "P_PaymentStatus",
            TRUE,
            "P_CreatedBy",
            "P_CreatedBy",
            NOW(),
            NOW()
        )
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
