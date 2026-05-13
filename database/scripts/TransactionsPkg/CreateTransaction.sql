-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "TransactionsPkg";

-- Procedimiento para crear una transacción actualizado con CurrentBalance y LoanStatus
CREATE OR REPLACE PROCEDURE "TransactionsPkg"."CreateTransaction"(
    "P_UserId" BIGINT,
    "P_CostCenterId" BIGINT,
    "P_CurrencyId" BIGINT,
    "P_PaymentMethodId" BIGINT,
    "P_TransactionAmount" NUMERIC,
    "P_TransactionType" VARCHAR, -- 'Income' | 'Expense'
    "P_AccountingPeriod" DATE,
    "P_TransactionDescription" TEXT,
    "P_CreatedBy" BIGINT,
    "P_EventId" BIGINT DEFAULT NULL,
    "P_PendingExpenseId" BIGINT DEFAULT NULL,
    "P_LoanId" BIGINT DEFAULT NULL,
    "P_AppliedExchangeRate" NUMERIC DEFAULT 1,
    "P_ReceiptImagePath" VARCHAR DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Transaction'
)
AS $$
DECLARE
    "V_PendingStatus" VARCHAR;
    "V_LoanExists" BOOLEAN;
    "V_CurrentLoanBalance" NUMERIC;
BEGIN
    -- 1. VALIDACIONES DE INTEGRIDAD FÍSICA
    IF NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El centro de costo no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. VALIDACIÓN DE LOANS (Usando CurrentBalance de imagen_2.png)
    IF "P_LoanId" IS NOT NULL THEN
        SELECT EXISTS(SELECT 1 FROM "Loans" WHERE "Id" = "P_LoanId") INTO "V_LoanExists";
        IF NOT "V_LoanExists" THEN
            OPEN "P_ResultSet" FOR SELECT 8 AS "ErrorId", 'El préstamo no existe.' AS "ErrorMessage";
            RETURN;
        END IF;

        -- Si es un gasto (pago de deuda), validamos que no pague más de lo que debe
        IF "P_TransactionType" = 'Expense' THEN
            SELECT "CurrentBalance" INTO "V_CurrentLoanBalance" FROM "Loans" WHERE "Id" = "P_LoanId";
            IF "P_TransactionAmount" > "V_CurrentLoanBalance" THEN
                OPEN "P_ResultSet" FOR SELECT 9 AS "ErrorId", 'El monto excede el saldo pendiente del préstamo.' AS "ErrorMessage";
                RETURN;
            END IF;
        END IF;
    END IF;

    -- 3. VALIDACIÓN DE PENDING EXPENSES
    IF "P_PendingExpenseId" IS NOT NULL THEN
        SELECT "PaymentStatus" INTO "V_PendingStatus" FROM "PendingExpenses" WHERE "Id" = "P_PendingExpenseId";
        IF "V_PendingStatus" IS NULL THEN
            OPEN "P_ResultSet" FOR SELECT 6 AS "ErrorId", 'El gasto pendiente no existe.' AS "ErrorMessage";
            RETURN;
        ELSIF "V_PendingStatus" = 'Paid' THEN
            OPEN "P_ResultSet" FOR SELECT 7 AS "ErrorId", 'Este gasto ya fue pagado.' AS "ErrorMessage";
            RETURN;
        END IF;
    END IF;

    -- 4. INSERCIÓN DE LA TRANSACCIÓN
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Transactions" (
            "UserId", "CostCenterId", "EventId", "PendingExpenseId", "LoanId",
            "CurrencyId", "PaymentMethodId", "TransactionAmount", "TransactionType",
            "AppliedExchangeRate", "AccountingPeriod", "TransactionDescription",
            "ReceiptImagePath", "CreatedBy", "UpdatedBy", "CreatedAt", "UpdatedAt"
        )
        VALUES (
            "P_UserId", "P_CostCenterId", "P_EventId", "P_PendingExpenseId", "P_LoanId",
            "P_CurrencyId", "P_PaymentMethodId", "P_TransactionAmount", "P_TransactionType",
            "P_AppliedExchangeRate", "P_AccountingPeriod", "P_TransactionDescription",
            "P_ReceiptImagePath", "P_CreatedBy", "P_CreatedBy", NOW(), NOW()
        )
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Inserted";

    -- 5. EFECTOS SECUNDARIOS

    -- Actualizar Gasto Pendiente
    IF "P_PendingExpenseId" IS NOT NULL THEN
        UPDATE "PendingExpenses" SET "PaymentStatus" = 'Paid', "UpdatedAt" = NOW() WHERE "Id" = "P_PendingExpenseId";
    END IF;

    -- Actualizar Saldo de Préstamo y Estado
    IF "P_LoanId" IS NOT NULL THEN
        IF "P_TransactionType" = 'Expense' THEN
            UPDATE "Loans"
            SET "CurrentBalance" = "CurrentBalance" - "P_TransactionAmount",
                "UpdatedAt" = NOW()
            WHERE "Id" = "P_LoanId";
        ELSIF "P_TransactionType" = 'Income' THEN
            UPDATE "Loans"
            SET "CurrentBalance" = "CurrentBalance" + "P_TransactionAmount",
                "UpdatedAt" = NOW()
            WHERE "Id" = "P_LoanId";
        END IF;

        -- Lógica adicional: Si el saldo llega a 0, marcar como pagado
        UPDATE "Loans"
        SET "LoanStatus" = 'Paid'
        WHERE "Id" = "P_LoanId" AND "CurrentBalance" <= 0;
    END IF;

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
