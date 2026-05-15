# API Loans

Documentación para el manejo de préstamos (Loans). Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/loans`

---

## 1. Listar Préstamos (Paginado)

Obtiene un listado paginado de préstamos con filtros opcionales.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `Id` (integer, opcional)
    *   `LenderName` (string, opcional)
    *   `CurrencyId` (integer, opcional)
    *   `RepaymentDueDate` (date Y-m-d, opcional)
    *   `IsActive` (boolean, opcional)
    *   `LoanStatus` (string: `Pending`, `Paid`, opcional)
    *   `PageSize` (integer, default: 10)
    *   `PageNumber` (integer, default: 1)

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Evento registrado exitosamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "LenderName": "Banco Central",
        "PrincipalAmount": 5000.0,
        "InterestAmount": 250.0,
        "TotalToRepay": 5250.0,
        "RepaymentDueDate": "2024-12-31T00:00:00Z",
        "CurrentBalance": 5250.0,
        "LoanStatus": "Pending",
        "IsActive": true,
        "CurrencyId": 1,
        "CreatedAt": "2024-05-15T10:00:00Z",
        "UpdatedAt": "2024-05-15T10:00:00Z"
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

## 2. Registrar Préstamo

Crea un nuevo registro de préstamo en el sistema.

*   **URL:** `/`
*   **Método:** `POST`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `LenderName` | string | Sí | Nombre del prestamista (máx 255). |
| `PrincipalAmount` | float | Sí | Monto principal (mín 0). |
| `InterestAmount` | float | Sí | Monto de intereses (mín 0). |
| `TotalToRepay` | float | Sí | Total a pagar (mín 0). |
| `RepaymentDueDate` | date | Sí | Fecha límite (Formato: YYYY-MM-DD). |
| `CurrencyId` | integer | Sí | ID de la moneda asociada. |
| `LoanStatus` | string | No | `Pending` o `Paid`. |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Evento registrado exitosamente",
  "Content": {
    "Id": 2,
    "LenderName": "Inversiones ABC",
    "PrincipalAmount": 1000.0,
    "InterestAmount": 50.0,
    "TotalToRepay": 1050.0,
    "RepaymentDueDate": "2024-06-30T00:00:00Z",
    "CurrentBalance": 1050.0,
    "LoanStatus": "Pending",
    "IsActive": true,
    "CurrencyId": 1,
    "CreatedAt": "2024-05-15T10:05:00Z",
    "UpdatedAt": "2024-05-15T10:05:00Z"
  }
}
```

---

## 3. Actualizar Préstamo

Actualiza los datos de un préstamo existente mediante su ID.

*   **URL:** `/`
*   **Método:** `PUT`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `Id` | integer | Sí | ID del préstamo a modificar. |
| `LenderName` | string | No | Nuevo nombre del prestamista. |
| `PrincipalAmount` | float | No | Nuevo monto principal. |
| `InterestAmount` | float | No | Nuevo monto de intereses. |
| `TotalToRepay` | float | No | Nuevo total a pagar. |
| `RepaymentDueDate` | date | No | Nueva fecha límite (YYYY-MM-DD). |
| `CurrencyId` | integer | No | Nuevo ID de moneda. |
| `LoanStatus` | string | No | `Pending` o `Paid`. |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Loan actualizado exitosamente",
  "Content": { ...objeto actualizado... }
}
```

---

## Errores Comunes

*   **422 (Validation Error):** Los datos no cumplen con las reglas (ej. `PrincipalAmount` negativo).
*   **500 (Server Error):** Error interno durante la ejecución de la acción.