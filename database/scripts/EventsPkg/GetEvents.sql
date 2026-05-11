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
        "Id",
        "CostCenterId",
        "CurrencyId",
        "EventName",
        "TargetAmount",
        "EventStatus",
        "StartDate",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt"
    FROM "Events"
    WHERE ("P_Id" IS NULL OR "Id" = "P_Id")
      AND ("P_EventName" IS NULL OR "EventName" ILIKE '%' || "P_EventName" || '%')
      AND ("P_CurrencyId" IS NULL OR "CurrencyId" = "P_CurrencyId")
      AND ("P_StartDate" IS NULL OR "StartDate" = "P_StartDate")
      AND ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
    ORDER BY "CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
