# API User Capabilities

Documentación para la consulta de roles y permisos (capacidades) de los usuarios. Estos endpoints permiten al frontend configurar dinámicamente el menú y la visibilidad de elementos de la interfaz según los permisos efectivos del usuario. Todos los endpoints requieren autenticación mediante el middleware `auth:api`.

**URL Base:** `/api/users`

---

## 1. Obtener Capacidades del Usuario

Recupera los roles asignados y los permisos efectivos (directos y heredados de roles) de un usuario específico. Los permisos se devuelven agrupados por módulo para facilitar el filtrado en el cliente.

*   **URL:** `/{id}/capabilities`
*   **Método:** `GET`
*   **Parámetros de Ruta:**
    *   `id` (integer, requerido): ID del usuario a consultar.

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Capacidades del usuario obtenidas correctamente",
  "Content": {
    "Roles": [
      "Admin"
    ],
    "Permissions": {
      "Loan": [
        "Create",
        "Read",
        "Update"
      ],
      "Currency": [
        "Read"
      ],
      "Transaction": [
        "Create",
        "Read"
      ],
      "User": [
        "Read"
      ]
    }
  }
}
```

---

## Uso en el Frontend
El objeto `Permissions` está diseñado para optimizar el rendimiento del lado del cliente:
*   **Menú Dinámico:** Se pueden iterar las llaves de `Permissions` para mostrar solo los módulos donde el usuario tiene al menos una acción permitida.
*   **Visibilidad de Botones:** Permite realizar comprobaciones directas sin iterar listas planas. Ejemplo: `if (content.Permissions.Loan.includes('Update')) { ... }`.

---

## Errores Comunes
*   **401 (Unauthorized):** Token de acceso inválido o expirado.
*   **404 (Not Found):** El usuario solicitado no existe.
*   **500 (Server Error):** Error interno al ejecutar la lógica de base de datos o procesamiento de permisos.