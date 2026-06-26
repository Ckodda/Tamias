-- Procedimiento para obtener eventos con filtros y paginación
CREATE OR REPLACE PROCEDURE "EventsPkg"."GetEvents"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_EventName" VARCHAR DEFAULT NULL,
    "P_CurrencyId" BIGINT DEFAULT NULL,
    "P_StartDate" DATE DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Events'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        "Events"."Id",
        "Events"."CostCenterId",
        "CostCenters"."CenterName",
        "Events"."CurrencyId",
        "Currencies"."CurrencyCode",
        "Currencies"."CurrencySymbol",
        "Currencies"."CurrencyName",
        "Events"."EventName",
        "Events"."TargetAmount",
        "Events"."EventStatus",
        "Events"."StartDate",
        "Events"."IsActive",
        "Events"."CreatedBy",
        "Events"."UpdatedBy",
        "Events"."CreatedAt",
        "Events"."UpdatedAt"
    FROM "Events"
    INNER JOIN "Currencies" ON "Events"."CurrencyId" = "Currencies"."Id"
    INNER JOIN "CostCenters" ON "Events"."CostCenterId" = "CostCenters"."Id"
    WHERE ("P_Id" IS NULL OR "Events"."Id" = "P_Id")
      AND ("P_EventName" IS NULL OR "Events"."EventName" ILIKE '%' || "P_EventName" || '%')
      AND ("P_CurrencyId" IS NULL OR "Events"."CurrencyId" = "P_CurrencyId")
      AND ("P_StartDate" IS NULL OR "Events"."StartDate" = "P_StartDate")
      AND ("P_IsActive" IS NULL OR "Events"."IsActive" = "P_IsActive")
    ORDER BY "Events"."CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
