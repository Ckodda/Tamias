# Documentación API: Monedas (Currencies)

**Base URL:** `/api/currencies`  
**Auth:** `Bearer Token`

---

## 1. Listado Paginado
`GET /api/currencies`

**Filtros (Query):**
- `CurrencyName` (string), `CurrencyCode` (string), `IsActive` (bool)
- `PageSize` (default: 10), `PageNumber` (default: 1)

**Respuesta (200):**
```json
{
  "Code": 200,
  "Message": "Listado de monedas obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "CurrencyName": "Dólar Estadounidense",
        "CurrencySymbol": "$",
        "CurrencyCode": "USD",
        "ExchangeRate": 1.0,
        "IsActive": true,
        "CreatedAt": "2023-10-27T10:00:00Z",
        "UpdatedAt": "2023-10-27T10:00:00Z"
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

## 2. Crear Moneda
`POST /`

Registra una nueva moneda.

**Cuerpo (JSON):**
- `CurrencyName`: string (3-50), **requerido**.
- `CurrencyCode`: string (max 3), **requerido**.
- `CurrencySymbol`: string (max 5), **requerido**.
- `ExchangeRate`: numeric (min 0), **requerido**.

**Respuesta Exitosa (201):**
```json
{
  "Code": 201,
  "Message": "Moneda registrada exitosamente",
  "Content": { "Id": 1, "CurrencyName": "...", "CurrencyCode": "..." }
}
```

---

## 3. Actualizar Moneda
`PUT /`

Actualiza datos por ID.

**Cuerpo (JSON):**
- `Id`: integer, **requerido**.
- `CurrencyName`: string, opcional.
- `CurrencyCode`: string, opcional.
- `CurrencySymbol`: string, opcional.
- `ExchangeRate`: numeric, opcional.
- `IsActive`: boolean, opcional.

**Respuesta:** `200 OK` si es exitoso o `422` si hay error de validación.