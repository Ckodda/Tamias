-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "CommitmentsPkg";

-- Procedimiento para actualizar un compromiso con validaciones de integridad
CREATE OR REPLACE PROCEDURE "CommitmentsPkg"."UpdateCommitment"(
    "P_Id" BIGINT,
    "P_UserId" BIGINT DEFAULT NULL,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_EventId" BIGINT DEFAULT NULL,
    "P_CommitmentAmount" DECIMAL DEFAULT NULL,
    "P_FrequencyType" VARCHAR DEFAULT NULL,
    "P_CurrentStatus" VARCHAR DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdateCommitment'
)
AS $$
BEGIN
    -- 1. Validar si el compromiso existe
    IF NOT EXISTS (SELECT 1 FROM "Commitments" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 7 AS "ErrorId", 'El compromiso no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el usuario existe (si se proporciona)
    IF "P_UserId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "Users" WHERE "Id" = "P_UserId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El usuario proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar si el centro de costo existe (si se proporciona)
    IF "P_CostCenterId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Validar si el evento existe (si se proporciona)
    IF "P_EventId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "Events" WHERE "Id" = "P_EventId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El evento proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Validar que el monto sea positivo (si se proporciona)
    IF "P_CommitmentAmount" IS NOT NULL AND "P_CommitmentAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'CommitmentAmount debe ser mayor que cero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 6. Validar que el tipo de frecuencia sea válido (si se proporciona)
    IF "P_FrequencyType" IS NOT NULL AND "P_FrequencyType" NOT IN ('Monthly', 'OneTime') THEN
        OPEN "P_ResultSet" FOR SELECT 5 AS "ErrorId", 'FrequencyType debe ser "Monthly" o "OneTime".' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 7. Validar que el estado del compromiso sea válido (si se proporciona)
    IF "P_CurrentStatus" IS NOT NULL AND "P_CurrentStatus" NOT IN ('Active', 'Fulfilled', 'Cancelled') THEN
        OPEN "P_ResultSet" FOR SELECT 6 AS "ErrorId", 'CurrentStatus debe ser "Active", "Fulfilled" o "Cancelled".' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 8. Éxito: Actualizar solo los campos no nulos
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "Commitments"
        SET
            "UserId" = COALESCE("P_UserId", "UserId"),
            "CostCenterId" = COALESCE("P_CostCenterId", "CostCenterId"),
            "EventId" = "P_EventId",
            "CommitmentAmount" = COALESCE("P_CommitmentAmount", "CommitmentAmount"),
            "FrequencyType" = COALESCE("P_FrequencyType", "FrequencyType"),
            "CurrentStatus" = COALESCE("P_CurrentStatus", "CurrentStatus"),
            "UpdatedBy" = COALESCE("P_UpdatedBy", "UpdatedBy"),
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
