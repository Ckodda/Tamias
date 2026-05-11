-- Procedimiento para obtener métodos de pago con filtros y paginación
CREATE OR REPLACE PROCEDURE "PaymentMethodsPkg"."GetPaymentMethods"(
    "P_Id" BIGINT DEFAULT NULL,
    "P_MethodName" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_PageSize" INTEGER DEFAULT 10,
    "P_PageNumber" INTEGER DEFAULT 1,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_PaymentMethods'
)
AS $$
BEGIN
    OPEN "P_ResultSet" FOR
    SELECT
        COUNT(*) OVER() AS "TotalCount",
        "Id",
        "MethodName",
        "IsActive",
        "CreatedBy",
        "UpdatedBy",
        "CreatedAt",
        "UpdatedAt"
    FROM "PaymentMethods"
    WHERE ("P_Id" IS NULL OR "Id" = "P_Id")
      AND ("P_MethodName" IS NULL OR "MethodName" ILIKE '%' || "P_MethodName" || '%')
      AND ("P_IsActive" IS NULL OR "IsActive" = "P_IsActive")
    ORDER BY "MethodName" ASC
    LIMIT "P_PageSize"
    OFFSET ("P_PageNumber" - 1) * "P_PageSize";
END;
$$ LANGUAGE plpgsql;
