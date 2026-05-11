-- Procedimiento para obtener usuarios con filtros y paginación
CREATE OR REPLACE PROCEDURE "UsersPkg"."GetUsers"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_FullName" VARCHAR DEFAULT NULL,
    "P_Email" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INT DEFAULT 10,
    "P_PageNumber" INT DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Users'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        0 AS "ErrorId",
        NULL AS "ErrorMessage",
        "Id",
        "FullName",
        "Email",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt",
        COUNT(*) OVER() AS "TotalCount"
    FROM "Users"
    WHERE
        ("P_Id" IS NULL OR "Id" = "P_Id") AND
        ("P_FullName" IS NULL OR "FullName" ILIKE '%' || "P_FullName" || '%') AND
        ("P_Email" IS NULL OR "Email" ILIKE '%' || "P_Email" || '%') AND
        ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
    ORDER BY "Id"
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";

EXCEPTION
    WHEN OTHERS THEN
        OPEN "P_ResultSet" FOR SELECT 99 AS "ErrorId", SQLERRM AS "ErrorMessage", 0 AS "TotalCount";
END;
$$ LANGUAGE plpgsql;
