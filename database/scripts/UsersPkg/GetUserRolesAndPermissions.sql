-- Crear el esquema si no existe (por seguridad)
CREATE SCHEMA IF NOT EXISTS "UsersPkg";

-- Procedimiento para obtener Roles y Permisos consolidados de un usuario
CREATE OR REPLACE PROCEDURE "UsersPkg"."GetUserRolesAndPermissions"(
    "P_UserId" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_GetUserRolesAndPermissions'
)
AS $$
BEGIN
    -- 1. Validar si el usuario existe en la tabla Users
    IF NOT EXISTS (SELECT 1 FROM "Users" WHERE "Id" = "P_UserId") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El usuario proporcionado no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Retornar Roles y Permisos en un único conjunto de resultados
    OPEN "P_ResultSet" FOR
    -- Sección de Roles
    SELECT 
        0 AS "ErrorId", 
        NULL AS "ErrorMessage", 
        'Role' AS "Type", 
        r."name" AS "Name"
    FROM "Roles" r
    INNER JOIN "ModelHasRoles" mhr ON r."Id" = mhr."RoleId"
    WHERE mhr."ModelId" = "P_UserId" 
      AND mhr."model_type" = 'App\Models\User'
    
    UNION ALL
    
    -- Sección de Permisos Efectivos (Directos + Heredados de Roles)
    SELECT 
        0 AS "ErrorId", 
        NULL AS "ErrorMessage", 
        'Permission' AS "Type", 
        "name" AS "Name"
    FROM (
        -- Permisos asignados directamente al usuario
        SELECT p."name"
        FROM "Permissions" p
        INNER JOIN "ModelHasPermissions" mhp ON p."Id" = mhp."PermissionId"
        WHERE mhp."ModelId" = "P_UserId" AND mhp."model_type" = 'App\Models\User'
        
        UNION -- UNION elimina duplicados si un permiso está directo y por rol
        
        -- Permisos obtenidos a través de los roles del usuario
        SELECT p."name"
        FROM "Permissions" p
        INNER JOIN "RoleHasPermissions" rhp ON p."Id" = rhp."PermissionId"
        INNER JOIN "ModelHasRoles" mhr ON rhp."RoleId" = mhr."RoleId"
        WHERE mhr."ModelId" = "P_UserId" AND mhr."model_type" = 'App\Models\User'
    ) AS "EffectivePermissions"
    ORDER BY "Type" DESC, "Name" ASC;

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage";
END;
$$ LANGUAGE plpgsql;