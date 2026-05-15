# Documentación API: Centros de Costo

**Base URL:** `/api/cost-centers`  
**Auth:** `Bearer Token`

---

## 1. Listado Paginado
`GET /api/cost-centers`

**Filtros (Query):**
- `Id` (int), `CenterName` (string), `CodeCostCenter` (string), `IsActive` (bool)
- `PageSize` (default: 10), `PageNumber` (default: 1)

**Respuesta (200):**
```json
{
  "Code": 200,
  "Message": "Listado obtenido correctamente",
  "Content": {
    "Items": [],
    "TotalCount": 0,
    "PageNumber": 1,
    "PageSize": 10,
    "TotalPages": 0
  }
}
```

---

## 2. Crear Centro de Costo
`POST /`

Registra un nuevo centro.

**Cuerpo (JSON):**
- `CodeCostCenter`: string (3-20), **requerido**.
- `CenterName`: string (3-100), **requerido**.

**Respuesta Exitosa (201):**
```json
{
  "Code": 201,
  "Message": "Centro de Costo creado exitosamente",
  "Content": { "Id": 1, "CodeCostCenter": "...", "CenterName": "..." }
}
```

---

## 3. Actualizar Centro de Costo
`PUT /`

Actualiza datos por ID.

**Cuerpo (JSON):**
- `Id`: integer, **requerido**.
- `CodeCostCenter`: string, opcional.
- `CenterName`: string, opcional.
- `IsActive`: boolean, opcional.

**Respuesta:** `200 OK` si es exitoso o `422` si hay error de validación.