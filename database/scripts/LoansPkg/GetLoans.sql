-- Procedimiento para obtener préstamos con filtros y paginación
CREATE OR REPLACE PROCEDURE "LoansPkg"."GetLoans"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_LenderName" VARCHAR DEFAULT NULL,
    "P_CurrencyId" BIGINT DEFAULT NULL,
    "P_RepaymentDueDate" DATE DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_LoanStatus" VARCHAR DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Loans'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        "Id",
        "CurrencyId",
        "LenderName",
        "PrincipalAmount",
        "InterestAmount",
        "TotalToRepay",
        "LoanStatus",
        "RepaymentDueDate",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt"
    FROM "Loans"
    WHERE ("P_Id" IS NULL OR "Id" = "P_Id")
      AND ("P_LenderName" IS NULL OR "LenderName" ILIKE '%' || "P_LenderName" || '%')
      AND ("P_CurrencyId" IS NULL OR "CurrencyId" = "P_CurrencyId")
      AND ("P_RepaymentDueDate" IS NULL OR "RepaymentDueDate" = "P_RepaymentDueDate")
      AND ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
      AND ("P_LoanStatus" IS NULL OR "LoanStatus" = "P_LoanStatus")
    ORDER BY "CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
