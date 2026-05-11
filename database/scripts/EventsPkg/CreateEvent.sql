-- Procedimiento para crear un nuevo evento
CREATE OR REPLACE PROCEDURE "EventsPkg"."CreateEvent"(
    "P_CostCenterId" BIGINT,
    "P_CurrencyId" BIGINT,
    "P_EventName" VARCHAR,
    "P_TargetAmount" DECIMAL,
    "P_EventStatus" VARCHAR,
    "P_StartDate" DATE,
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Event'
)
AS $$
BEGIN
    -- 1. Validar si el centro de costo existe
    IF NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si la moneda existe
    IF NOT EXISTS (SELECT 1 FROM "Currencies" WHERE "Id" = "P_CurrencyId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'La moneda seleccionada no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar duplicado de Nombre en el mismo Centro de Costo
    IF EXISTS (SELECT 1 FROM "Events" WHERE "EventName" = "P_EventName" AND "CostCenterId" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'Ya existe un evento con este nombre en el centro de costo seleccionado.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Éxito: Insertar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Events" (
            "CostCenterId",
            "CurrencyId",
            "EventName",
            "TargetAmount",
            "EventStatus",
            "StartDate",
            "IsActive",
            "CreatedBy",
            "UpdatedBy",
            "UpdatedAt"
        )
        VALUES (
            "P_CostCenterId",
            "P_CurrencyId",
            "P_EventName",
            "P_TargetAmount",
            "P_EventStatus",
            "P_StartDate",
            TRUE,
            "P_CreatedBy",
            "P_CreatedBy", -- El usuario que crea también es el último que actualiza
            NOW()
        )
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage", * FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
