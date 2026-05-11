-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "CostCentersPkg";

-- Procedimiento para registrar un nuevo centro de costo con retorno de estado
CREATE OR REPLACE PROCEDURE "CostCentersPkg"."CreateCostCenter"(
    "P_CenterName" VARCHAR,
    "P_CodeCostCenter" VARCHAR,
    "P_CreatedBy" BIGINT, -- Nuevo parámetro
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_CostCenter'
)
AS $$
BEGIN
    -- 1. Validar si el nombre ya existe
    IF EXISTS (SELECT 1 FROM "CostCenters" WHERE "CenterName" = "P_CenterName") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre del centro de costo ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el código ya existe
    IF EXISTS (SELECT 1 FROM "CostCenters" WHERE "CodeCostCenter" = "P_CodeCostCenter") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El código del centro de costo ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Éxito: Insertar y retornar
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "CostCenters" ("CenterName", "CodeCostCenter", "IsActive", "CreatedBy", "UpdatedAt", "CreatedAt")
        VALUES ("P_CenterName", "P_CodeCostCenter", TRUE, "P_CreatedBy", NOW(), NOW()) -- Usamos P_CreatedBy
        RETURNING "Id", "CodeCostCenter", "CenterName", "IsActive", "CreatedBy", "UpdatedAt", "CreatedAt"
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
