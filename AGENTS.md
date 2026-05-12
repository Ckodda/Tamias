# Guía de Contexto del Proyecto - Tamias

Este archivo sirve como referencia central para que cualquier agente de IA comprenda la arquitectura, estándares y estado actual del proyecto, evitando la sobrecarga del chat.

## 🏗️ Arquitectura del Sistema

El proyecto sigue una arquitectura de capas limpia en Laravel 11:

-   **Routing**: Las rutas se registran en `bootstrap/app.php` dentro del array `api`. Cada módulo tiene su propio archivo en `routes/api/{Modulo}.php`.
-   **Controllers**: Ubicados en `app/Http/Controllers/Api/`. Manejan la entrada/salida HTTP estándar.
-   **Actions**: Ubicados en `app/Actions/{Modulo}/`. Unica responsabilidad: coordinar la lógica entre el Request y el Repository.
-   **Repositories**:
    -   **Interfaces**: En `app/Repositories/Contracts/`.
    -   **Implementaciones**: En `app/Repositories/Eloquent/`. Heredan de `BaseRepository`.
-   **Data Transfer Objects (DTOs)**: Usamos `spatie/laravel-data` para:
    -   **Requests**: Validación de entrada (reemplaza FormRequests).
    -   **Responses**: Formateo de salida (reemplaza Resources).
-   **Models**: En `app/Models/`. Extienden de `BaseModel` y usan PascalCase para campos.

## 🛠️ Estándar de Flujo CRUD (Store, Index, Update)

Para cada nuevo concepto, se debe seguir estrictamente este orden:

### 1. Base de Datos y Persistencia
-   **Scripts SQL**: Crear procedimientos almacenados (SPs) en `database/scripts/{Modulo}Pkg/`.
    -   `Create{Concepto}.sql`: Debe insertar `CreatedBy` y `UpdatedBy` con el mismo ID de usuario.
    -   `Get{Concepto}s.sql`: Debe incluir filtros opcionales (`COALESCE` o `IS NULL`) y paginación (`LIMIT/OFFSET`). Debe retornar `TotalCount` usando `COUNT(*) OVER()`.
    -   `Update{Concepto}.sql`: Debe usar `COALESCE` para permitir actualizaciones parciales.
-   **Migración de SPs**: Una migración que cree el esquema `"{Modulo}Pkg"` y cargue los archivos `.sql`.

#### Convenciones de Nomenclatura en Migraciones:
-   **Claves Primarias**: Se nombran `Id` (PascalCase).
-   **Columnas de Auditoría**: Se nombran `CreatedAt`, `UpdatedAt`, `CreatedBy`, `UpdatedBy` (PascalCase). `CreatedAt` y `UpdatedAt` usan `useCurrent()`. `CreatedBy` y `UpdatedBy` son `foreignId` que referencian `Users.Id`.
-   **Nombres de Tablas**: Generalmente en plural y PascalCase (ej. `Users`, `Currencies`), aunque algunas migraciones pueden usar nombres en minúsculas (ej. `loans`).
-   **Claves Foráneas**: Se usa `foreignId` para `CreatedBy` y `UpdatedBy`. Para otras claves foráneas, se usa `unsignedBigInteger` seguido de `foreign()` y `references('Id')`. Los nombres de las columnas de claves foráneas suelen ser `[NombreTablaRelacionada]Id` (ej. `CurrencyId`).

### 2. Capa de Datos (Eloquent)
-   **Modelo**: Extender `BaseModel`. Definir `$table` y `casts`.
-   **Repository Interface**: Definir métodos `create`, `getAll` y `update`.
-   **Repository Implementation**: 
    -   Usar `callProcedure` de `BaseRepository`.
    -   Mapear errores del SP (ErrorId > 0) a `ValidationException` o `Exception`.
    -   Hidratar modelos con `mapResultToModel` o `mapResultsToCollection`.

### 3. Capa de Negocio (Actions & Requests)
-   **Requests**: Clases `Data` con atributos de validación PHP 8.
-   **Responses**: Clases `Data` con método estático `fromModel`.
    -   **Seguridad**: Ocultar campos de auditoría (`CreatedBy`, `UpdatedBy`) si el usuario no es `SuperAdmin` o `Admin` usando `$data->except(...)`.
-   **Actions**: 
    -   `CreateAction`: Llama a `repository->create`.
    -   `GetAction`: Llama a `repository->getAll` y envuelve en `PaginatedResponse::make()`.
    -   `UpdateAction`: Llama a `repository->update`.

### 4. Capa de Entrada (API)
-   **Controller**: Métodos `store`, `index`, `update` que inyectan la Action correspondiente.
-   **Ruta**: Archivo dedicado en `routes/api/` y registro en `bootstrap/app.php`.

## 📌 Estado de Implementación

### Módulos Completados:
-   **CostCenter**: CRUD completo.
-   **Currency**: CRUD completo.
-   **Event**: CRUD completo (incluye `CurrencyId`).
-   **PaymentMethod**: CRUD completo.

### Tareas Pendientes:
-   Módulo de `Commitments`.
-   Módulo de `Transactions`.
-   Módulo de `Loans`.

---
*Nota: Este estándar garantiza consistencia, seguridad de auditoría y rendimiento mediante lógica en base de datos.*
