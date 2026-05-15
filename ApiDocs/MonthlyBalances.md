# API Monthly Balances

Documentación para la consulta de balances mensuales por centro de costo. Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/monthly-balances`

---

## 1. Listar Saldos Mensuales (Paginado)

Obtiene un listado paginado de los balances mensuales, permitiendo filtrar por centro de costo y rangos de periodos.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `CostCenterId` (integer, opcional): ID del centro de costo a filtrar.
    *   `StartMonth` (date Y-m-d, opcional): Fecha de inicio del rango de búsqueda.
    *   `EndMonth` (date Y-m-d, opcional): Fecha de fin del rango de búsqueda.
    *   `PageSize` (integer, default: 12): Registros por página.
    *   `PageNumber` (integer, default: 1): Número de página actual.

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Listado obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "MonthPeriod": "2024-05",
        "TotalIncomes": 10500.00,
        "TotalExpenses": 4200.50,
        "ClosingBalance": 6299.50,
        "CostCenterId": 5,
        "CenterName": "Sede Central",
        "ProfitMarginPercentage": 60.00
      }
    ],
    "TotalCount": 1,
    "PageNumber": 1,
    "PageSize": 12,
    "TotalPages": 1
  }
}
```

---

## Otros Endpoints

De acuerdo a la definición de rutas, existen los siguientes métodos pendientes de implementación detallada en el controlador:

*   **POST** `/`: Registro de balance manual (Pendiente).
*   **PUT** `/`: Actualización de balance (Pendiente).

---

## Errores Comunes
*   **500 (Server Error):** Error interno al procesar la consulta o ejecución del Procedure.