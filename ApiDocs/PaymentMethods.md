# API Payment Methods

Documentación para la gestión de métodos de pago. Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/payment-methods`

---

## 1. Listar Métodos de Pago (Paginado)

Obtiene un listado paginado de los métodos de pago disponibles, con filtros opcionales.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `Id` (integer, opcional): Filtrar por ID específico.
    *   `MethodName` (string, opcional): Filtrar por nombre del método.
    *   `IsActive` (boolean, opcional): Filtrar por estado activo/inactivo.
    *   `PageSize` (integer, default: 10): Registros por página.
    *   `PageNumber` (integer, default: 1): Número de página.

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Listado de metodos de pago obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "MethodName": "Efectivo",
        "IsActive": true,
        "CreatedAt": "2024-05-15T12:00:00Z",
        "UpdatedAt": "2024-05-15T12:00:00Z"
      }
    ],
    "TotalCount": 1,
    "PageNumber": 1,
    "PageSize": 10,
    "TotalPages": 1
  }
}
```
> **Nota:** Los campos `CreatedBy` y `UpdatedBy` son visibles únicamente para usuarios con roles `Admin` o `SuperAdmin`.

---

## 2. Registrar Método de Pago

Crea un nuevo método de pago en el sistema.

*   **URL:** `/`
*   **Método:** `POST`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `MethodName` | string | Sí | Nombre del método (mín 3, máx 50 caracteres). |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Metodo de pago registrado exitosamente",
  "Content": {
    "Id": 2,
    "MethodName": "Transferencia",
    "IsActive": true,
    "CreatedAt": "2024-05-15T12:10:00Z",
    "UpdatedAt": "2024-05-15T12:10:00Z"
  }
}
```

---

## 3. Actualizar Método de Pago

Actualiza la información de un método de pago existente.

*   **URL:** `/`
*   **Método:** `PUT`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `Id` | integer | Sí | ID del registro a actualizar. |
| `MethodName` | string | No | Nuevo nombre (mín 3 caracteres). |
| `IsActive` | boolean | No | Cambiar estado de activación. |

*   **Respuesta Exitosa (Código 200):** (Similar al objeto de respuesta de creación).

---

## Errores Comunes
*   **422 (Validation Error):** Los datos no cumplen con las reglas (ej. nombre duplicado o muy corto).
*   **500 (Server Error):** Error inesperado en el servidor.