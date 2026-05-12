CREATE OR REPLACE PROCEDURE "LoansPkg"."CreateLoan"(
    IN "P_LenderName" VARCHAR,
    IN "P_PrincipalAmount" DECIMAL,
    IN "P_InterestAmount" DECIMAL,
    IN "P_TotalToRepay" DECIMAL,
    IN "P_RepaymentDueDate" DATE,
    IN "P_LoanStatus" VARCHAR,
    IN "P_IsActive" BOOLEAN,
    IN "P_CurrencyId" BIGINT,
    IN "P_CreatedBy" BIGINT,
    IN "P_UpdatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Loan'
)
AS $$
BEGIN
    -- Validaciones básicas
    IF "P_LenderName" IS NULL OR "P_LenderName" = '' THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'LenderName cannot be empty.' AS "ErrorMessage";
        RETURN;
    END IF;

    IF "P_PrincipalAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'PrincipalAmount must be greater than zero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- Validar si la moneda existe
    IF NOT EXISTS (SELECT 1 FROM "Currencies" WHERE "Id" = "P_CurrencyId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'La moneda seleccionada no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- Insertar el nuevo préstamo
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Loans" (
            "LenderName",
            "PrincipalAmount",
            "InterestAmount",
            "TotalToRepay",
            "RepaymentDueDate",
            "LoanStatus",
            "IsActive",
            "CurrencyId",
            "CreatedBy",
            "UpdatedBy",
            "CreatedAt",
            "UpdatedAt"
        ) VALUES (
            "P_LenderName",
            "P_PrincipalAmount",
            "P_InterestAmount",
            "P_TotalToRepay",
            "P_RepaymentDueDate",
            COALESCE("P_LoanStatus", 'Pending'),
            COALESCE("P_IsActive", TRUE),
            "P_CurrencyId",
            "P_CreatedBy",
            "P_UpdatedBy",
            NOW(),
            NOW()
        )
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", "Id", "LenderName", "PrincipalAmount", "InterestAmount", "TotalToRepay", "RepaymentDueDate", "LoanStatus", "IsActive", "CurrencyId", "CreatedBy", "UpdatedBy", "CreatedAt", "UpdatedAt" FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
