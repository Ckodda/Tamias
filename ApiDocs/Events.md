# Documentación API: Eventos

**Base URL:** `/api/events`  
**Auth:** `Bearer Token`

---

## 1. Listado Paginado
`GET /api/events`

**Filtros (Query):**
- `Id` (int), `EventName` (string), `CurrencyId` (int), `StartDate` (date), `IsActive` (bool)
- `PageSize` (default: 10), `PageNumber` (default: 1)

**Respuesta (200):**
```json
{
  "Code": 200,
  "Message": "Listado de eventos obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "CostCenterId": 1,
        "EventName": "Concierto Benéfico",
        "TargetAmount": 5000.0,
        "EventStatus": "Active",
        "StartDate": "2024-05-20",
        "IsActive": true,
        "CreatedBy": 1,
        "UpdatedBy": 1,
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

## 2. Crear Evento
`POST /`

Registra un nuevo evento.

**Cuerpo (JSON):**
- `CostCenterId`: integer, **requerido**.
- `CurrencyId`: integer, **requerido**.
- `EventName`: string (min 3), **requerido**.
- `TargetAmount`: numeric (min 0), **requerido**.
- `EventStatus`: string (`Active`, `Completed`, `Cancelled`), **requerido**.
- `StartDate`: date (YYYY-MM-DD), **requerido**.

**Respuesta Exitosa (201):**
```json
{
  "Code": 201,
  "Message": "Evento registrado exitosamente",
  "Content": {
    "Id": 1,
    "CostCenterId": 1,
    "EventName": "Concierto Benéfico",
    "TargetAmount": 5000.0,
    "EventStatus": "Active",
    "StartDate": "2024-05-20",
    "IsActive": true,
    "CreatedBy": 1,
    "UpdatedBy": 1,
    "CreatedAt": "2024-05-15T10:00:00Z",
    "UpdatedAt": "2024-05-15T10:00:00Z"
  }
}
```

---

## 3. Actualizar Evento
`PUT /`

Actualiza datos por ID.

**Cuerpo (JSON):**
- `Id`: integer, **requerido**.
- `CostCenterId`, `CurrencyId`, `EventName`, `TargetAmount`, `EventStatus`, `StartDate`: opcionales.
- `IsActive`: boolean, opcional.

**Respuesta:** `200 OK` si es exitoso o `422` si hay error de validación.