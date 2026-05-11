-- Procedimiento para obtener centros de costo con filtros y paginación
CREATE OR REPLACE PROCEDURE "CostCentersPkg"."GetCostCenters"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_CenterName" VARCHAR DEFAULT NULL,
    "P_CodeCostCenter" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_CostCenters'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount", -- Devuelve el total de registros filtrados sin el límite
        "Id",
        "CodeCostCenter",
        "CenterName",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt"
    FROM "CostCenters"
    WHERE ("P_Id" IS NULL OR "Id" = "P_Id")
      AND ("P_CenterName" IS NULL OR "CenterName" ILIKE '%' || "P_CenterName" || '%')
      AND ("P_CodeCostCenter" IS NULL OR "CodeCostCenter" = "P_CodeCostCenter")
      AND ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
    ORDER BY "CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
