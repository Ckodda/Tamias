# Documentación API: Compromisos (Commitments)

**Base URL:** `/api/commitments`  
**Auth:** `Bearer Token`

---

## 1. Listado Paginado
`GET /api/commitments`

**Filtros (Query):**
- `Id` (int), `UserId` (int), `CostCenterId` (int), `EventId` (int)
- `CurrentStatus` (string: `Active`, `Fulfilled`, `Cancelled`)
- `PageSize` (default: 10), `PageNumber` (default: 1)

**Respuesta (200):**
```json
{
  "Code": 200,
  "Message": "Listado obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "UserId": 10,
        "CostCenterId": 5,
        "EventId": null,
        "CommitmentAmount": 500.0,
        "FrequencyType": "Monthly",
        "CurrentStatus": "Active",
        "IsActive": true,
        "CreatedAt": "2023-10-27T10:00:00Z",
        "UpdatedAt": "2023-10-27T10:00:00Z"
      }
    ],
    "TotalCount": 0,
    "PageNumber": 1,
    "PageSize": 10,
    "TotalPages": 0
  }
}
```

---

## 2. Crear Registro
`POST /api/commitments`

**Nota:** Los campos `CreatedBy` y `UpdatedBy` solo son visibles para usuarios con rol `Admin` o `SuperAdmin`.

**Body (JSON):**
- `UserId`: int **[Req]**
- `CostCenterId`: int **[Req]**
- `EventId`: int **[Req]**
- `CommitmentAmount`: numeric (min: 0) **[Req]**
- `FrequencyType`: string (`Monthly`, `OneTime`) **[Req]**
- `CurrentStatus`: string (`Active`, `Fulfilled`, `Cancelled`) **[Req]**

**Respuesta (201):**
```json
{
  "Code": 201,
  "Message": "Commitment creado exitosamente",
  "Content": {
    "Id": 1,
    "UserId": 10,
    "CostCenterId": 5,
    "EventId": null,
    "CommitmentAmount": 500.0,
    "FrequencyType": "Monthly",
    "CurrentStatus": "Active",
    "IsActive": true,
    "CreatedBy": 1,
    "UpdatedBy": 1,
    "CreatedAt": "2023-10-27T10:00:00Z",
    "UpdatedAt": "2023-10-27T10:00:00Z"
  }
}
```

---

## 3. Actualizar Registro
`PUT /api/commitments`

**Body (JSON):**
- `Id`: int **[Req]**
- Todos los campos del **POST** son requeridos en la actualización según el Request.

**Respuesta:** `200 OK` (Éxito) o `422` (Validación).