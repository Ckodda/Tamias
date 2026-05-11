-- Procedimiento para actualizar una moneda (campos opcionales)
CREATE OR REPLACE PROCEDURE "CurrenciesPkg"."UpdateCurrency"(
    "P_Id" BIGINT,
    "P_CurrencyName" VARCHAR DEFAULT NULL,
    "P_CurrencyCode" VARCHAR DEFAULT NULL,
    "P_CurrencySymbol" VARCHAR DEFAULT NULL,
    "P_ExchangeRate" DECIMAL DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdateCurrency'
)
AS $$
BEGIN
    -- 1. Validar si el registro existe
    IF NOT EXISTS (SELECT 1 FROM "Currencies" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'La moneda no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar duplicado de Nombre (solo si se envió un nuevo nombre)
    IF "P_CurrencyName" IS NOT NULL AND EXISTS (SELECT 1 FROM "Currencies" WHERE "CurrencyName" = "P_CurrencyName" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre de la moneda ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar duplicado de Código (solo si se envió un nuevo código)
    IF "P_CurrencyCode" IS NOT NULL AND EXISTS (SELECT 1 FROM "Currencies" WHERE "CurrencyCode" = "P_CurrencyCode" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El código de la moneda ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "Currencies"
        SET
            "CurrencyName" = COALESCE("P_CurrencyName", "CurrencyName"),
            "CurrencyCode" = COALESCE("P_CurrencyCode", "CurrencyCode"),
            "CurrencySymbol" = COALESCE("P_CurrencySymbol", "CurrencySymbol"),
            "ExchangeRate" = COALESCE("P_ExchangeRate", "ExchangeRate"),
            "IsActive" = COALESCE("P_IsActive", "IsActive"),
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
