-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "CurrenciesPkg";

-- Procedimiento para registrar una nueva moneda (incluyendo Code)
CREATE OR REPLACE PROCEDURE "CurrenciesPkg"."CreateCurrency"(
    "P_CurrencyName" VARCHAR,
    "P_CurrencyCode" VARCHAR,
    "P_CurrencySymbol" VARCHAR,
    "P_ExchangeRate" DECIMAL(10,4),
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Currency'
)
AS $$
BEGIN
    -- 1. Validar duplicados de nombre
    IF EXISTS (SELECT 1 FROM "Currencies" WHERE "CurrencyName" = "P_CurrencyName") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre de la moneda ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar duplicados de código
    IF EXISTS (SELECT 1 FROM "Currencies" WHERE "CurrencyCode" = "P_CurrencyCode") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El código de la moneda (ISO) ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Éxito: Insertar y retornar
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Currencies" ("CurrencyName", "CurrencyCode", "CurrencySymbol", "ExchangeRate", "IsActive", "CreatedBy", "CreatedAt", "UpdatedAt")
        VALUES ("P_CurrencyName", "P_CurrencyCode", "P_CurrencySymbol", "P_ExchangeRate", TRUE, "P_CreatedBy", NOW(), NOW())
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
