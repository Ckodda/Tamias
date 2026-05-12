-- Esquema de Transacciones
CREATE SCHEMA IF NOT EXISTS "TransactionsPkg";

-- Procedimiento para anular transacciones con reversión de efectos
CREATE OR REPLACE PROCEDURE "TransactionsPkg"."VoidTransaction"(
    "P_TransactionId" BIGINT,
    "P_UpdatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_VoidTransaction'
)
AS $$
DECLARE
    "V_PendingExpenseId" BIGINT;
    "V_LoanId" BIGINT;
    "V_Amount" NUMERIC;
    "V_Type" VARCHAR;
    "V_IsActive" BOOLEAN;
BEGIN
    -- 1. Obtener datos de la transacción y verificar estado
    SELECT "PendingExpenseId", "LoanId", "TransactionAmount", "TransactionType", "IsActive"
    INTO "V_PendingExpenseId", "V_LoanId", "V_Amount", "V_Type", "V_IsActive"
    FROM "Transactions" WHERE "Id" = "P_TransactionId";

    -- Si la transacción no existe
    IF "V_IsActive" IS NULL THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'La transacción no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- Si ya está anulada
    IF NOT "V_IsActive" THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'La transacción ya se encuentra anulada.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. MARCAR COMO INACTIVA (Anulada)
    UPDATE "Transactions"
    SET "IsActive" = false,
        "UpdatedBy" = "P_UpdatedBy",
        "UpdatedAt" = NOW()
    WHERE "Id" = "P_TransactionId";

    -- 3. REVERTIR EFECTOS SECUNDARIOS

    -- A. Revertir Gasto Pendiente: Si estaba pagado, vuelve a pendiente
    IF "V_PendingExpenseId" IS NOT NULL THEN
        UPDATE "PendingExpenses"
        SET "PaymentStatus" = 'Pending',
            "UpdatedBy" = "P_UpdatedBy",
            "UpdatedAt" = NOW()
        WHERE "Id" = "V_PendingExpenseId";
    END IF;

    -- B. Revertir Préstamo (Loan): Ajustar el saldo restante
    IF "V_LoanId" IS NOT NULL THEN
        IF "V_Type" = 'Expense' THEN
            -- Era un pago realizado: la deuda aumenta de nuevo
            UPDATE "Loans"
            SET "RemainingBalance" = "RemainingBalance" + "V_Amount",
                "UpdatedBy" = "P_UpdatedBy",
                "UpdatedAt" = NOW()
            WHERE "Id" = "V_LoanId";
        ELSIF "V_Type" = 'Income' THEN
            -- Era un desembolso recibido: la deuda disminuye (se cancela el ingreso)
            UPDATE "Loans"
            SET "RemainingBalance" = "RemainingBalance" - "V_Amount",
                "UpdatedBy" = "P_UpdatedBy",
                "UpdatedAt" = NOW()
            WHERE "Id" = "V_LoanId";
        END IF;
    END IF;

    -- 4. RETORNO EXITOSO
    OPEN "P_ResultSet" FOR
    SELECT 0 AS "ErrorId", 'Transacción anulada y saldos revertidos exitosamente.' AS "ErrorMessage";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
