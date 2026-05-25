-- Procedimiento para obtener monedas con filtros y paginación
CREATE OR REPLACE PROCEDURE "CurrenciesPkg"."GetCurrencies"(
     "PO_Id" INTEGER DEFAULT NULL,
    "P_CurrencyName" VARCHAR DEFAULT NULL,
    "P_CurrencyCode" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Currencies'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        "Id",
        "CurrencyName",
        "CurrencyCode",
        "CurrencySymbol",
        "ExchangeRate",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt"
    FROM "Currencies"
    WHERE ("P_CurrencyName" IS NULL OR "CurrencyName" ILIKE '%' || "P_CurrencyName" || '%')
      AND ("P_CurrencyCode" IS NULL OR "CurrencyCode" = "P_CurrencyCode")
      AND ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
      AND ("P_Id" IS NULL OR "Id" = "P_Id")
    ORDER BY "CurrencyName" ASC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
