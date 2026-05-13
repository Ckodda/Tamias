-- Esquema de Transacciones
CREATE SCHEMA IF NOT EXISTS "TransactionsPkg";

-- Procedimiento para listar transacciones con filtros, búsqueda por ID y paginación
CREATE OR REPLACE PROCEDURE "TransactionsPkg"."GetTransactions"(
    "P_Id" BIGINT DEFAULT NULL, -- Nuevo filtro por ID
    "P_StartDate" DATE DEFAULT NULL,
    "P_EndDate" DATE DEFAULT NULL,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_TransactionType" VARCHAR DEFAULT NULL,
    "P_UserId" BIGINT DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Transactions'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        T.*,
        U."FullName" AS "UserFullName",
        CC."Name" AS "CostCenterName",
        C."Symbol" AS "CurrencySymbol",
        PM."Name" AS "PaymentMethodName"
    FROM "Transactions" T
    LEFT JOIN "Users" U ON T."UserId" = U."Id"
    INNER JOIN "CostCenters" CC ON T."CostCenterId" = CC."Id"
    INNER JOIN "Currencies" C ON T."CurrencyId" = C."Id"
    INNER JOIN "PaymentMethods" PM ON T."PaymentMethodId" = PM."Id"
    WHERE
        ("P_Id" IS NULL OR T."Id" = "P_Id") AND -- Filtro prioritario
        ("P_StartDate" IS NULL OR T."AccountingPeriod" >= "P_StartDate") AND
        ("P_EndDate" IS NULL OR T."AccountingPeriod" <= "P_EndDate") AND
        ("P_CostCenterId" IS NULL OR T."CostCenterId" = "P_CostCenterId") AND
        ("P_TransactionType" IS NULL OR T."TransactionType" = "P_TransactionType") AND
        ("P_UserId" IS NULL OR T."UserId" = "P_UserId") AND
        ("P_IsActive" IS NULL OR T."IsActive" = "P_IsActive")
    ORDER BY T."AccountingPeriod" DESC, T."CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
