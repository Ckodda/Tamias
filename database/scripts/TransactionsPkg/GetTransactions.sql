-- Crear el esquema si no existe (Actúa como el Paquete)
CREATE SCHEMA IF NOT EXISTS "TransactionsPkg";

-- Procedimiento para obtener transacciones con filtros
CREATE OR REPLACE PROCEDURE "TransactionsPkg"."GetTransactions"(
    "P_StartDate" DATE,
    "P_EndDate" DATE,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_CurrencyId" BIGINT DEFAULT NULL,
    "P_PaymentMethodId" BIGINT DEFAULT NULL,
    "P_UserId" BIGINT DEFAULT NULL,
    "P_UserFullName" VARCHAR DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_transactions' -- Cursor para retornar los datos
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        T."Id",
        T."UserId",
        T."CostCenterId",
        T."EventId",
        T."PendingExpenseId",
        T."LoanId",
        T."CurrencyId",
        T."PaymentMethodId",
        T."TransactionAmount",
        T."TransactionType"::VARCHAR,
        T."AppliedExchangeRate",
        T."AccountingPeriod",
        T."TransactionDescription",
        T."ReceiptImagePath",
        T."CreatedAt",
        T."UpdatedAt"
    FROM "Transactions" T
    LEFT JOIN "Users" U ON T."UserId" = U."Id"
    WHERE T."CreatedAt"::DATE >= "P_StartDate"
      AND T."CreatedAt"::DATE <= "P_EndDate"
      AND ("P_CostCenterId" IS NULL OR T."CostCenterId" = "P_CostCenterId")
      AND ("P_CurrencyId" IS NULL OR T."CurrencyId" = "P_CurrencyId")
      AND ("P_PaymentMethodId" IS NULL OR T."PaymentMethodId" = "P_PaymentMethodId")
      AND ("P_UserId" IS NULL OR T."UserId" = "P_UserId")
      AND ("P_UserFullName" IS NULL OR U."FullName" ILIKE '%' || "P_UserFullName" || '%');
END;
$$ LANGUAGE plpgsql;
