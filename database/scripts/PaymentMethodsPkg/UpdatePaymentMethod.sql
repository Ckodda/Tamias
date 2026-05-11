-- Procedimiento para actualizar un método de pago (campos opcionales)
CREATE OR REPLACE PROCEDURE "PaymentMethodsPkg"."UpdatePaymentMethod"(
    "P_Id" BIGINT,
    "P_MethodName" VARCHAR DEFAULT NULL,
    "P_IsActive" BOOLEAN DEFAULT NULL,
    "P_UpdatedBy" BIGINT DEFAULT NULL,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_UpdatePaymentMethod'
)
AS $$
BEGIN
    -- 1. Validar si el registro existe
    IF NOT EXISTS (SELECT 1 FROM "PaymentMethods" WHERE "Id" = "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 3 AS "ErrorId", 'El método de pago no existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Validar duplicado de Nombre (solo si se envió un nuevo nombre)
    IF "P_MethodName" IS NOT NULL AND EXISTS (SELECT 1 FROM "PaymentMethods" WHERE "MethodName" = "P_MethodName" AND "Id" <> "P_Id") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre del método de pago ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 3. Éxito: Actualizar solo los campos proporcionados
    OPEN "P_ResultSet" FOR
    WITH "Updated" AS (
        UPDATE "PaymentMethods"
        SET
            "MethodName" = COALESCE("P_MethodName", "MethodName"),
            "IsActive" = COALESCE("P_IsActive", "IsActive"),
            "UpdatedBy" = "P_UpdatedBy",
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
