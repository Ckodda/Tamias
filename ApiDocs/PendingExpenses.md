# API Pending Expenses

Documentación para la gestión de gastos pendientes. Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/pending-expenses`

---

## 1. Listar Gastos Pendientes (Paginado)

Obtiene un listado paginado de gastos pendientes con filtros opcionales.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `Id` (integer, opcional): Filtrar por ID específico.
    *   `CostCenterId` (integer, opcional): Filtrar por ID de centro de costo.
    *   `PaymentStatus` (string, opcional): Filtrar por estado de pago (`Pending`, `Paid`, `Cancelled`).
    *   `ProviderName` (string, opcional): Filtrar por nombre del proveedor.
    *   `StartDate` (date Y-m-d, opcional): Fecha de inicio del rango de vencimiento.
    *   `EndDate` (date Y-m-d, opcional): Fecha de fin del rango de vencimiento.
    *   `PageSize` (integer, default: 10): Registros por página.
    *   `PageNumber` (integer, default: 1): Número de página.

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Listado obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "CostCenterId": 101,
        "ExpenseDescription": "Compra de material de oficina",
        "TotalAmount": 150.75,
        "DueDate": "2024-06-30T00:00:00Z",
        "ProviderName": "Papelería Central",
        "PaymentStatus": "Pending",
        "IsActive": true,
        "CreatedAt": "2024-05-15T14:00:00Z",
        "UpdatedAt": "2024-05-15T14:00:00Z"
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

## 2. Registrar Gasto Pendiente

Crea un nuevo registro de gasto pendiente.

*   **URL:** `/`
*   **Método:** `POST`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `CostCenterId` | integer | Sí | ID del centro de costo asociado. |
| `ExpenseDescription` | string | Sí | Descripción del gasto (mín 5 caracteres). |
| `TotalAmount` | float | Sí | Monto total del gasto (mín 0.01). |
| `DueDate` | date | Sí | Fecha de vencimiento (Formato: YYYY-MM-DD). |
| `ProviderName` | string | Sí | Nombre del proveedor (mín 3 caracteres). |
| `PaymentStatus` | string | Sí | Estado del pago (`Pending`, `Paid`, `Cancelled`). |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "PendingExpense creado exitosamente",
  "Content": {
    "Id": 2,
    "CostCenterId": 102,
    "ExpenseDescription": "Servicio de consultoría mensual",
    "TotalAmount": 500.00,
    "DueDate": "2024-07-15T00:00:00Z",
    "ProviderName": "Consultores Asociados",
    "PaymentStatus": "Pending",
    "IsActive": true,
    "CreatedAt": "2024-05-15T14:05:00Z",
    "UpdatedAt": "2024-05-15T14:05:00Z"
  }
}
```

---

## 3. Actualizar Gasto Pendiente

Actualiza la información de un gasto pendiente existente.

*   **URL:** `/`
*   **Método:** `PUT`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `Id` | integer | Sí | ID del gasto pendiente a modificar. |
| `CostCenterId` | integer | No | Nuevo ID del centro de costo. |
| `ExpenseDescription` | string | No | Nueva descripción del gasto (mín 5 caracteres). |
| `TotalAmount` | float | No | Nuevo monto total (mín 0.01). |
| `DueDate` | date | No | Nueva fecha de vencimiento (YYYY-MM-DD). |
| `ProviderName` | string | No | Nuevo nombre del proveedor (mín 3 caracteres). |
| `PaymentStatus` | string | No | Nuevo estado del pago (`Pending`, `Paid`, `Cancelled`). |
| `IsActive` | boolean | No | Cambiar estado de activación. |

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "PendingExpense actualizado exitosamente",
  "Content": {
    "Id": 1,
    "CostCenterId": 101,
    "ExpenseDescription": "Compra de material de oficina (revisado)",
    "TotalAmount": 160.00,
    "DueDate": "2024-06-30T00:00:00Z",
    "ProviderName": "Papelería Central",
    "PaymentStatus": "Paid",
    "IsActive": true,
    "CreatedAt": "2024-05-15T14:00:00Z",
    "UpdatedAt": "2024-05-15T14:15:00Z"
  }
}
```

---

## Errores Comunes
*   **422 (Validation Error):** Los datos proporcionados no son válidos (ej. `TotalAmount` menor a 0.01, `ExpenseDescription` muy corta).
*   **500 (Server Error):** Error interno inesperado en el servidor.