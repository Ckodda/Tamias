-- Procedimiento para actualizar un usuario existente
CREATE OR REPLACE PROCEDURE "UsersPkg"."UpdateUser"(
    "P_Id" BIGINT,
    "P_FullName" VARCHAR DEFAULT NULL,
    "P_Email" VARCHAR DEFAULT NULL,
    "P_Password" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_User'
)
AS $$
BEGIN
    -- 1. Validar si el usuario existe
    IF NOT EXISTS (SELECT 1 FROM "Users" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El usuario con el ID proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar si el email ya existe para otro usuario (si se está actualizando el email)
    IF "P_Email" IS NOT NULL AND EXISTS (SELECT 1 FROM "Users" WHERE "Email" = "P_Email" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 2 AS "ErrorId", 'El email proporcionado ya está registrado por otro usuario.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Éxito: Actualizar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "Users"
        SET
            "FullName" = COALESCE("P_FullName", "FullName"),
            "Email" = COALESCE("P_Email", "Email"),
            "Password" = COALESCE("P_Password", "Password"),
            "IsActive" = COALESCE("P_IsActive", "IsActive"),
            "UpdatedBy" = COALESCE("P_UpdatedBy", "UpdatedBy"),
            "UpdatedAt" = NOW()
        WHERE "Id" = "P_Id"
        RETURNING *
    )
    SELECT 0 AS "ErrorId", NULL AS "ErrorMessage",
           "Id", "FullName", "Email", "IsActive", "CreatedBy", "UpdatedBy", "CreatedAt", "updated_at" AS "UpdatedAt"
    FROM "Updated";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;
