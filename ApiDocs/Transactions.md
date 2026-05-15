# API Transactions

Documentación para la gestión de transacciones financieras (Ingresos y Egresos). Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/transactions`

---

## 1. Listar Transacciones (Paginado)

Obtiene un listado paginado de transacciones con diversos filtros de búsqueda.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `Id` (integer, opcional): Filtrar por ID de transacción.
    *   `StartDate` (date Y-m-d, opcional): Fecha de inicio del periodo.
    *   `EndDate` (date Y-m-d, opcional): Fecha de fin del periodo.
    *   `CostCenterId` (integer, opcional): ID del centro de costo.
    *   `TransactionType` (string: `Income`, `Expense`, opcional).
    *   `UserId` (integer, opcional): ID del usuario que realizó la transacción.
    *   `IsActive` (boolean, opcional).
    *   `PageSize` (integer, default: 10).
    *   `PageNumber` (integer, default: 1).

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Listado obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 10,
        "UserId": 5,
        "CostCenterId": 1,
        "TransactionAmount": 150.50,
        "TransactionType": "Expense",
        "AccountingPeriod": "2024-05-15",
        "TransactionDescription": "Pago de servicios básicos",
        "UserFullName": "Juan Pérez",
        "CostCenterName": "Sede Principal",
        "IsActive": true,
        "CreatedAt": "2024-05-15T10:00:00Z"
      }
    ],
    "TotalCount": 1,
    "PageNumber": 1,
    "PageSize": 10,
    "TotalPages": 1
  }
}
```

---

## 2. Registrar Transacción

Crea una nueva transacción y actualiza los saldos correspondientes. Soporta subida de imagen de comprobante.

*   **URL:** `/`
*   **Método:** `POST`
*   **Cuerpo de la Petición (Multipart/Form-Data):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `UserId` | integer | Sí | ID del usuario responsable. |
| `CostCenterId` | integer | Sí | ID del centro de costo. |
| `CurrencyId` | integer | Sí | ID de la moneda. |
| `PaymentMethodId` | integer | Sí | ID del método de pago. |
| `TransactionAmount` | float | Sí | Monto (mín 0.01). |
| `TransactionType` | string | Sí | `Income` o `Expense`. |
| `AccountingPeriod` | date | Sí | Fecha contable (YYYY-MM-DD). |
| `TransactionDescription` | string | Sí | Mínimo 5 caracteres. |
| `AppliedExchangeRate` | float | No | Tipo de cambio (default 1.0). |
| `ReceiptImage` | file | No | Imagen del comprobante (max 2MB). |
| `EventId` | integer | No | ID de evento vinculado. |
| `PendingExpenseId` | integer | No | ID de gasto pendiente vinculado. |
| `LoanId` | integer | No | ID de préstamo vinculado. |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Transaction creado exitosamente",
  "Content": {
    "Id": 11,
    "TransactionAmount": 200.00,
    "ReceiptImagePath": "receipts/abc123.jpg",
    "CreatedAt": "2024-05-15T10:05:00Z"
  }
}
```

---

## 3. Anular Transacción

Revierte los efectos de una transacción específica mediante su ID.

*   **URL:** `/{id}/void`
*   **Método:** `POST`

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Transacción anulada y saldos revertidos exitosamente."
}
```

---

## Errores Comunes
*   **422 (Validation Error):** Datos inválidos o imposibilidad de anular la transacción.
*   **500 (Server Error):** Error interno al procesar la lógica de negocio o base de datos.