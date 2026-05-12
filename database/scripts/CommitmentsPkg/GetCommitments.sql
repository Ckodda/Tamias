CREATE SCHEMA IF NOT EXISTS "CommitmentsPkg";
-- Procedimiento para obtener compromisos con filtros y paginación
CREATE OR REPLACE PROCEDURE "CommitmentsPkg"."GetCommitments"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_UserId" BIGINT DEFAULT NULL,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_EventId" BIGINT DEFAULT NULL,
    "P_CurrentStatus" VARCHAR DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_Commitments'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount", -- Total de registros filtrados para el frontend
        C."Id",
        C."UserId",
        U."FullName" AS "UserFullName", -- Join informativo para evitar consultas extra en Laravel
        C."CostCenterId",
        CC."CenterName" AS "CostCenterName",
        C."EventId",
        E."EventName" AS "EventName",
        C."CommitmentAmount",
        C."FrequencyType",
        C."CurrentStatus",
        C."CreatedAt",
        C."UpdatedAt"
    FROM "Commitments" C
    INNER JOIN "Users" U ON C."UserId" = U."Id"
    INNER JOIN "CostCenters" CC ON C."CostCenterId" = CC."Id"
    LEFT JOIN "Events" E ON C."EventId" = E."Id"
    WHERE ("P_Id" IS NULL OR C."Id" = "P_Id")
      AND ("P_UserId" IS NULL OR C."UserId" = "P_UserId")
      AND ("P_CostCenterId" IS NULL OR C."CostCenterId" = "P_CostCenterId")
      AND ("P_EventId" IS NULL OR C."EventId" = "P_EventId")
      AND ("P_CurrentStatus" IS NULL OR C."CurrentStatus" = "P_CurrentStatus")
    ORDER BY C."CreatedAt" DESC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
