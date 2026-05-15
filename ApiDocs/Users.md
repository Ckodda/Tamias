# API Users

Documentación para la gestión de usuarios del sistema. Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/users`

---

## 1. Listar Usuarios (Paginado)

Obtiene un listado paginado de usuarios con diversos filtros de búsqueda.

*   **URL:** `/`
*   **Método:** `GET`
*   **Parámetros de Consulta (Query Params):**
    *   `Id` (integer, opcional): Filtrar por ID de usuario.
    *   `FullName` (string, opcional): Filtrar por nombre.
    *   `Email` (string, opcional): Filtrar por correo electrónico.
    *   `CreatedBy` (integer, opcional): Filtrar por creador.
    *   `IsActive` (boolean, opcional): Filtrar por estado.
    *   `PageSize` (integer, default: 10).
    *   `PageNumber` (integer, default: 1).

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Listado de usuarios obtenido correctamente",
  "Content": {
    "Items": [
      {
        "Id": 1,
        "FullName": "Admin Principal",
        "Email": "admin@tamias.com",
        "IsActive": true,
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
> **Nota:** Los campos `CreatedBy` y `UpdatedBy` son visibles únicamente para usuarios con roles `Admin` o `SuperAdmin`.

---

## 2. Registrar Usuario

Crea un nuevo usuario en la plataforma.

*   **URL:** `/`
*   **Método:** `POST`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `FullName` | string | Sí | Nombre completo (máx 255). |
| `Email` | string | Sí | Correo electrónico único (máx 255). |
| `Password` | string | Sí | Contraseña (mín 8, máx 255). |

*   **Respuesta Exitosa (Código 201):**

```json
{
  "Code": 201,
  "Message": "Evento registrado exitosamente",
  "Content": {
    "Id": 2,
    "FullName": "Juan Pérez",
    "Email": "jperez@tamias.com",
    "IsActive": true,
    "CreatedAt": "2024-05-15T15:30:00Z",
    "UpdatedAt": "2024-05-15T15:30:00Z"
  }
}
```

---

## 3. Actualizar Usuario

Actualiza la información de un usuario existente.

*   **URL:** `/`
*   **Método:** `PUT`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `Id` | integer | No | ID del usuario a modificar (opcional en request, pero necesario para la acción). |
| `FullName` | string | No | Nuevo nombre completo. |
| `Email` | string | No | Nuevo correo electrónico. |
| `Password` | string | No | Nueva contraseña (mín 8). |
| `IsActive` | boolean | No | Cambiar estado de activación. |

*   **Respuesta Exitosa (Código 200):** (Similar al objeto de respuesta de creación).

---

## Errores Comunes
*   **422 (Validation Error):** Datos inválidos (ej. email duplicado o contraseña corta).
*   **500 (Server Error):** Error interno al procesar la solicitud.