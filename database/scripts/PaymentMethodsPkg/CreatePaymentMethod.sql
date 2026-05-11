-- Procedimiento para crear un nuevo método de pago
CREATE OR REPLACE PROCEDURE "PaymentMethodsPkg"."CreatePaymentMethod"(
    "P_MethodName" VARCHAR,
    "P_CreatedBy" BIGINT,
    INOUT "P_ResultSet" REFCURSOR DEFAULT 'rs_PaymentMethod'
)
AS $$
BEGIN
    -- 1. Validar duplicado de Nombre
    IF EXISTS (SELECT 1 FROM "PaymentMethods" WHERE "MethodName" = "P_MethodName") THEN
        OPEN "P_ResultSet" FOR SELECT 1 AS "ErrorId", 'El nombre del método de pago ya existe.' AS "ErrorMessage";
        RETURN;
    END IF;

    -- 2. Éxito: Insertar y devolver el registro
    OPEN "P_ResultSet" FOR
    WITH "Inserted" AS (
        INSERT INTO "PaymentMethods" (
            "MethodName",
            "IsActive",
            "CreatedBy",
            "UpdatedBy",
            "UpdatedAt"
        )
        VALUES (
            "P_MethodName",
            TRUE,
            "P_CreatedBy",
            "P_CreatedBy",
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
