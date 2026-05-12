-- Crear el esquema si no existe
CREATE SCHEMA IF NOT EXISTS "PendingExpensesPkg";

-- Procedimiento para obtener gastos pendientes con filtros y paginación
CREATE OR REPLACE PROCEDURE "PendingExpensesPkg"."GetPendingExpenses"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_CostCenterId" BIGINT DEFAULT NULL,
    "P_PaymentStatus" VARCHAR DEFAULT NULL,
    "P_ProviderName" VARCHAR DEFAULT NULL,
    "P_StartDate" DATE DEFAULT NULL,
    "P_EndDate" DATE DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_PendingExpenses'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount", -- Total de registros para paginación
        PE."Id",
        PE."CostCenterId",
        CC."CenterName" AS "CostCenterName", -- Join para mostrar en el listado
        PE."ExpenseDescription",
        PE."TotalAmount",
        PE."DueDate",
        PE."ProviderName",
        PE."PaymentStatus",
        PE."IsActive",
        PE."CreatedAt",
        PE."UpdatedAt",
        U."FullName" AS "CreatedByName" -- Join para auditoría visual
    FROM "PendingExpenses" PE
    INNER JOIN "CostCenters" CC ON PE."CostCenterId" = CC."Id"
    LEFT JOIN "Users" U ON PE."CreatedBy" = U."Id"
    WHERE ("P_Id" IS NULL OR PE."Id" = "P_Id")
      AND ("P_CostCenterId" IS NULL OR PE."CostCenterId" = "P_CostCenterId")
      AND ("P_PaymentStatus" IS NULL OR PE."PaymentStatus" = "P_PaymentStatus")
      AND ("P_ProviderName" IS NULL OR PE."ProviderName" ILIKE '%' || "P_ProviderName" || '%')
      AND ("P_StartDate" IS NULL OR PE."DueDate" >= "P_StartDate")
      AND ("P_EndDate" IS NULL OR PE."DueDate" <= "P_EndDate")
    ORDER BY PE."DueDate" ASC -- Ordenado por vencimiento más próximo
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
