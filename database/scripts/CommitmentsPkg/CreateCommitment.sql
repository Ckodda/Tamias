-- Procedimiento para crear un nuevo compromiso (Commitment)
CREATE OR REPLACE PROCEDURE "CommitmentsPkg"."CreateCommitment"(
    "P_UserId" BIGINT,
    "P_CostCenterId" BIGINT,
    "P_EventId" BIGINT,
    "P_CommitmentAmount" DECIMAL,
    "P_FrequencyType" VARCHAR,
    "P_CurrentStatus" VARCHAR,
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Commitment'
)
AS $$
BEGIN
    -- 1. Validar si el usuario existe
    IF NOT EXISTS (SELECT 1 FROM "Users" WHERE "Id" = "P_UserId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El usuario proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el centro de costo existe
    IF NOT EXISTS (SELECT 1 FROM "CostCenters" WHERE "Id" = "P_CostCenterId") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El centro de costo proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Validar si el evento existe (si se proporciona)
    IF "P_EventId" IS NOT NULL AND NOT EXISTS (SELECT 1 FROM "Events" WHERE "Id" = "P_EventId") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El evento proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 4. Validar que el monto del compromiso sea positivo
    IF "P_CommitmentAmount" <= 0 THEN
        OPEN "P_ResultSet" FOR SELECT 4 AS "ErrorId", 'CommitmentAmount debe ser mayor que cero.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 5. Validar que el tipo de frecuencia sea válido
    IF "P_FrequencyType" NOT IN ('Monthly', 'OneTime') THEN
        OPEN "P_ResultSet" FOR SELECT 5 AS "ErrorId", 'FrequencyType debe ser "Monthly" o "OneTime".' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 6. Validar que el estado del compromiso sea válido
    IF "P_CurrentStatus" NOT IN ('Active', 'Fulfilled', 'Cancelled') THEN
        OPEN "P_ResultSet" FOR SELECT 6 AS "ErrorId", 'CurrentStatus debe ser "Active", "Fulfilled" o "Cancelled".' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 7. Éxito: Insertar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Commitments" (
            "UserId",
            "CostCenterId",
            "EventId",
            "CommitmentAmount",
            "FrequencyType",
            "CurrentStatus",
            "IsActive",
            "CreatedBy",
            "UpdatedBy",
            "CreatedAt",
            "UpdatedAt"
        )
        VALUES (
            "P_UserId",
            "P_CostCenterId",
            "P_EventId",
            "P_CommitmentAmount",
            "P_FrequencyType",
            "P_CurrentStatus",
            TRUE,
            "P_CreatedBy",
            "P_CreatedBy", -- El usuario que crea también es el último que actualiza
            NOW(),
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
