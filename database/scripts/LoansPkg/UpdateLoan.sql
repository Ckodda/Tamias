-- Procedimiento para actualizar un evento (campos opcionales)
CREATE OR REPLACE PROCEDURE "LoansPkg"."UpdateLoan"(
    IN "P_Id" BIGINT,
    IN "P_LenderName" VARCHAR,
    IN "P_PrincipalAmount" DECIMAL,
    IN "P_InterestAmount" DECIMAL,
    IN "P_TotalToRepay" DECIMAL,
    IN "P_RepaymentDueDate" DATE,
    IN "P_LoanStatus" VARCHAR,
    IN "P_IsActive" BOOLEAN,
    IN "P_CurrencyId" BIGINT,
    IN "P_UpdatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdateLoan'
)
AS $$
BEGIN
    -- 1. Validar si el registro existe
    IF NOT EXISTS (SELECT 1 FROM "Loans" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'La deuda no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    IF "P_PrincipalAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'PrincipalAmount must be greater than zero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar si la moneda existe (si se proporciona)
    IF "P_CurrencyId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "Currencies" WHERE "Id" = "P_CurrencyId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'La moneda seleccionada no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "Loans"
        SET
            "LenderName" = COALESCE("P_LenderName", "LenderName"),
            "PrincipalAmount" = COALESCE("P_PrincipalAmount", "PrincipalAmount"),
            "InterestAmount" = COALESCE("P_InterestAmount", "InterestAmount"),
            "TotalToRepay" = COALESCE("P_TotalToRepay", "TotalToRepay"),
            "RepaymentDueDate" = COALESCE("P_RepaymentDueDate", "RepaymentDueDate"),
            "LoanStatus" = COALESCE("P_LoanStatus", "LoanStatus"),
            "IsActive" = COALESCE("P_IsActive", "IsActive"),
            "CurrencyId" = COALESCE("P_CurrencyId", "CurrencyId"),
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
