-- Procedimiento para actualizar un evento (campos opcionales)
CREATE OR REPLACE PROCEDURE "EventsPkg"."UpdateEvent"(
    "P_Id" BIGINT,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_CurrencyId" BIGINT DEFAULT NULL,
    "P_EventName" VARCHAR DEFAULT NULL,
    "P_TargetAmount" DECIMAL DEFAULT NULL,
    "P_EventStatus" VARCHAR DEFAULT NULL,
    "P_StartDate" DATE DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdateEvent'
)
AS $$
BEGIN
    -- 1. Validar si el registro existe
    IF NOT EXISTS (SELECT 1 FROM "Events" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'El evento no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el centro de costo existe (si se proporciona)
    IF "P_CostCenterId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar si la moneda existe (si se proporciona)
    IF "P_CurrencyId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "Currencies" WHERE "Id" = "P_CurrencyId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'La moneda seleccionada no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Validar duplicado de Nombre en el mismo Centro de Costo (si se cambia el nombre o el centro de costo)
    IF EXISTS (
        SELECT 1 FROM "Events"
        WHERE "EventName" = COALESCE("P_EventName", "Events"."EventName")
          AND "CostCenterId" = COALESCE("P_CostCenterId", "Events"."CostCenterId")
          AND "Id" <> "P_Id"
    ) THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'Ya existe un evento con este nombre en el centro de costo seleccionado.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "Events"
        SET
            "CostCenterId" = COALESCE("P_CostCenterId", "CostCenterId"),
            "CurrencyId" = COALESCE("P_CurrencyId", "CurrencyId"),
            "EventName" = COALESCE("P_EventName", "EventName"),
            "TargetAmount" = COALESCE("P_TargetAmount", "TargetAmount"),
            "EventStatus" = COALESCE("P_EventStatus", "EventStatus"),
            "StartDate" = COALESCE("P_StartDate", "StartDate"),
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
