# API Auth

Documentación para el manejo de autenticación de usuarios y gestión de sesiones.

**URL Base:** `/api/auth`

---

## 1. Login

Maneja el inicio de sesión de los usuarios y devuelve el token JWT necesario para las peticiones autenticadas.

*   **URL:** `/login`
*   **Método:** `POST`
*   **Cuerpo de la Petición (JSON):**

| Campo | Tipo | Requerido | Descripción |
| :--- | :--- | :--- | :--- |
| `Email` | string | Sí | Correo electrónico del usuario. |
| `Password` | string | Sí | Contraseña del usuario. |

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Inicio de sesión exitoso",
  "Content": {
    "AccessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "TokenType": "bearer",
    "ExpiresIn": 3600,
    "User": {
      "FullName": "Admin Principal",
      "Email": "admin@tamias.com"
    }
  }
}
```

---

## 2. Logout

Cierra la sesión del usuario invalidando el token actual. Requiere autenticación mediante el middleware `auth:api`.

*   **URL:** `/logout`
*   **Método:** `POST`
*   **Headers:** `Authorization: Bearer {token}`

*   **Respuesta Exitosa (Código 200):**

```json
{
  "Code": 200,
  "Message": "Sesión cerrada exitosamente",
  "Content": null
}
```

---

## Errores Comunes
*   **422 (Validation Error):** Credenciales incorrectas o datos mal formateados.
*   **500 (Server Error):** Error interno al procesar la autenticación.