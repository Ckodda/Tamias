-- Procedimiento para crear un nuevo usuario
CREATE OR REPLACE PROCEDURE "UsersPkg"."CreateUser"(
    "P_FullName" VARCHAR,
    "P_Email" VARCHAR,
    "P_Password" VARCHAR,
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_User'
)
AS $$
BEGIN
    -- 1. Validar si el email ya existe
    IF EXISTS (SELECT 1 FROM "Users" WHERE "Email" = "P_Email") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El email proporcionado ya está registrado.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Éxito: Insertar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "Users" (
            "FullName",
            "Email",
            "Password",
            "IsActive",
            "CreatedBy",
            "UpdatedBy",
            "CreatedAt",
            "UpdatedAt"
        )
        VALUES (
            "P_FullName",
            "P_Email",
            "P_Password",
            TRUE, -- Por defecto, el usuario se crea activo
            "P_CreatedBy",
            "P_CreatedBy", -- El usuario que crea también es el último que actualiza
            NOW(),
            NOW()
        )
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage",
           "Id", "FullName", "Email", "IsActive", "CreatedBy", "UpdatedBy", "CreatedAt", "UpdatedAt"
    FROM "Inserted";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
