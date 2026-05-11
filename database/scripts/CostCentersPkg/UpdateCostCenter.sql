-- Procedimiento para actualizar un centro de costo (campos opcionales)
CREATE OR REPLACE PROCEDURE "CostCentersPkg"."UpdateCostCenter"(
    "P_Id" BIGINT,
    "P_CenterName" VARCHAR DEFAULT NULL,
    "P_CodeCostCenter" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdateCostCenter'
)
AS $$
BEGIN
    -- 1. Validar si el registro existe
    IF NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El centro de costo no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar duplicado de Nombre (solo si se envió un nuevo nombre)
    IF "P_CenterName" IS NOT NULL AND EXISTS (SELECT 1 FROM "CostCenters" WHERE "CenterName" = "P_CenterName" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre del centro de costo ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar duplicado de Código (solo si se envió un nuevo código)
    IF "P_CodeCostCenter" IS NOT NULL AND EXISTS (SELECT 1 FROM "CostCenters" WHERE "CodeCostCenter" = "P_CodeCostCenter" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El código del centro de costo ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "CostCenters"
        SET
            "CenterName" = COALESCE("P_CenterName", "CenterName"),
            "CodeCostCenter" = COALESCE("P_CodeCostCenter", "CodeCostCenter"),
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
