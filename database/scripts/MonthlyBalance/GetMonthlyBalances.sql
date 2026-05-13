-- Esquema para reportes/balances
CREATE SCHEMA IF NOT EXISTS "ReportsPkg";

-- Procedimiento para obtener el historial de balances mensuales
CREATE OR REPLACE PROCEDURE "ReportsPkg"."GetMonthlyBalances"(
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_StartMonth" DATE DEFAULT NULL,
    "P_EndMonth" DATE DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_MonthlyBalances'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        MB."Id",
        MB."MonthPeriod",
        MB."TotalIncomes",
        MB."TotalExpenses",
        MB."ClosingBalance",
        MB."CostCenterId",
        CC."CenterName" AS "CenterName",
        -- Indicador de rendimiento (KPI)
        CASE
            WHEN MB."TotalIncomes" > 0
            THEN ((MB."TotalIncomes" - MB."TotalExpenses") / MB."TotalIncomes") * 100
            ELSE 0
        END AS "ProfitMarginPercentage"
    FROM "MonthlyBalances" MB
    INNER JOIN "CostCenters" CC ON MB."CostCenterId" = CC."Id"
    WHERE MB."IsActive" = true
      AND ("P_CostCenterId" IS NULL OR MB."CostCenterId" = "P_CostCenterId")
      AND ("P_StartMonth" IS NULL OR MB."MonthPeriod" >= "P_StartMonth")
      AND ("P_EndMonth" IS NULL OR MB."MonthPeriod" <= "P_EndMonth")
    ORDER BY MB."MonthPeriod" ASC -- Orden cronológico para gráficas
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
