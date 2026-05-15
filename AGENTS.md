# Guía de Contexto del Proyecto - Tamias

Este archivo sirve como referencia central para que cualquier agente de IA comprenda la arquitectura, estándares y estado actual del proyecto, evitando la sobrecarga del chat y facilitando una interacción más eficiente y precisa.

## 🏗️ Arquitectura del Sistema: Un Enfoque por Capas en Laravel 11

El proyecto Tamias sigue una arquitectura de capas limpia y bien definida, basada en Laravel 11, que promueve la separación de responsabilidades, la mantenibilidad y la escalabilidad. Cada componente tiene un rol específico:

-   **Routing**:
    -   **Propósito**: Define cómo las solicitudes HTTP entrantes son dirigidas a la lógica de la aplicación.
    -   **Ubicación**: Las rutas principales se registran en `bootstrap/app.php` dentro del array `api`. Para mantener la modularidad, cada módulo (ej. `Currency`, `Event`) tiene su propio archivo de rutas dedicado en `routes/api/{Modulo}.php`.
    -   **Ejemplo**: Una ruta para obtener todas las monedas podría definirse en `routes/api/Currency.php` como:
        ```php
        use App\Http\Controllers\Api\Currency\CurrencyController;
        Route::get('/currencies', [CurrencyController::class, 'index']);
        ```

-   **Controllers (`app/Http/Controllers/Api/`)**:
    -   **Propósito**: Actúan como la "puerta de entrada" de la API. Su responsabilidad principal es recibir solicitudes HTTP, delegar la lógica de negocio a las `Actions` correspondientes y devolver una respuesta HTTP formateada.
    -   **Principio**: Deben ser lo más "delgados" posible, sin lógica de negocio compleja.
    -   **Ejemplo**: Un método `index` en `CurrencyController` inyectaría y llamaría a una `GetCurrenciesAction`.
        ```php
        namespace App\Http\Controllers\Api\Currency;

        use App\Actions\Currency\GetCurrenciesAction;
        use App\Http\Controllers\Controller;
        use App\Data\Currency\CurrencyResponseData; // DTO de respuesta
        use Illuminate\Http\JsonResponse;

        class CurrencyController extends Controller
        {
            public function index(GetCurrenciesAction $action): JsonResponse
            {
                $currencies = $action->execute(); // La acción devuelve una colección de DTOs
                return response()->json(CurrencyResponseData::collection($currencies));
            }
        }
        ```

-   **Actions (`app/Actions/{Modulo}/`)**:
    -   **Propósito**: Contienen la lógica de negocio central de la aplicación. Coordinan la interacción entre los datos de entrada (a menudo validados por DTOs de Request) y la capa de persistencia (Repositories).
    -   **Principio**: Cada `Action` debe tener una única responsabilidad bien definida (ej. `CreateCurrencyAction`, `GetCurrenciesAction`).
    -   **Ejemplo**: Una `CreateCurrencyAction` recibiría un DTO de Request, lo pasaría al repositorio y devolvería un DTO de Response.
        ```php
        namespace App\Actions\Currency;

        use App\Data\Currency\CreateCurrencyRequestData; // DTO de Request
        use App\Data\Currency\CurrencyResponseData;     // DTO de Response
        use App\Repositories\Contracts\CurrencyRepositoryInterface;

        class CreateCurrencyAction
        {
            public function __construct(
                protected CurrencyRepositoryInterface $repository
            ) {}

            public function execute(CreateCurrencyRequestData $data): CurrencyResponseData
            {
                $currency = $this->repository->create($data);
                return CurrencyResponseData::fromModel($currency);
            }
        }
        ```

-   **Repositories**:
    -   **Propósito**: Abstraen la capa de persistencia de datos, desacoplando la lógica de negocio de los detalles específicos de la base de datos.
    -   **Interfaces (`app/Repositories/Contracts/`)**: Definen el "contrato" o "qué" operaciones se pueden realizar (ej. `create`, `getAll`, `update`).
        -   **Ejemplo**: `CurrencyRepositoryInterface.php`
            ```php
            namespace App\Repositories\Contracts;

            use App\Data\Currency\CreateCurrencyRequestData;
            use App\Data\Currency\UpdateCurrencyRequestData;
            use App\Models\Currency;
            use Illuminate\Support\Collection;

            interface CurrencyRepositoryInterface
            {
                public function create(CreateCurrencyRequestData $data): Currency;
                public function getAll(): Collection;
                public function update(int $id, UpdateCurrencyRequestData $data): Currency;
            }
            ```
    -   **Implementaciones (`app/Repositories/Eloquent/`)**: Contienen la lógica específica para interactuar con la base de datos (en este caso, usando procedimientos almacenados y Eloquent). Heredan de `BaseRepository` para funcionalidades comunes.
        -   **Ejemplo**: `CurrencyRepository.php` implementaría los métodos definidos en la interfaz, utilizando `callProcedure` para interactuar con los SPs.
            ```php
            namespace App\Repositories\Eloquent;

            use App\Data\Currency\CreateCurrencyRequestData;
            use App\Data\Currency\UpdateCurrencyRequestData;
            use App\Models\Currency;
            use App\Repositories\Contracts\CurrencyRepositoryInterface;
            use Illuminate\Support\Collection;
            use Illuminate\Validation\ValidationException; // Para mapeo de errores

            class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
            {
                public function create(CreateCurrencyRequestData $data): Currency
                {
                    $result = $this->callProcedure('CurrencyPkg.CreateCurrency', [
                        'p_name' => $data->name,
                        'p_symbol' => $data->symbol,
                        'p_created_by' => $data->createdBy,
                    ]);

                    if (isset($result[0]->ErrorId) && $result[0]->ErrorId > 0) {
                        throw ValidationException::withMessages(['general' => $result[0]->ErrorMessage]);
                    }
                    return $this->mapResultToModel($result[0], new Currency());
                }
                // ... otros métodos
            }
            ```

-   **Data Transfer Objects (DTOs) (`spatie/laravel-data`)**:
    -   **Propósito**: Garantizan la validación de entrada y el formateo consistente de salida.
    -   **Requests**: Reemplazan los `FormRequests` de Laravel para la validación de datos de entrada. Utilizan atributos de validación de PHP 8.
        -   **Ejemplo**: `CreateCurrencyRequestData.php`
            ```php
            namespace App\Data\Currency;

            use Spatie\LaravelData\Data;
            use Illuminate\Validation\Rule;

            class CreateCurrencyRequestData extends Data
            {
                public function __construct(
                    public string $name,
                    public string $symbol,
                    public int $createdBy, // Campo de auditoría
                ) {}

                public static function rules(): array
                {
                    return [
                        'name' => ['required', 'string', 'max:255', Rule::unique('Currencies', 'Name')],
                        'symbol' => ['required', 'string', 'max:5', Rule::unique('Currencies', 'Symbol')],
                        'createdBy' => ['required', 'integer', 'exists:Users,Id'],
                    ];
                }
            }
            ```
    -   **Responses**: Formatean los datos de salida de manera consistente, reemplazando los `Resources` de Laravel.
        -   **Seguridad**: Permiten ocultar campos sensibles (ej. `CreatedBy`, `UpdatedBy`) si el usuario no tiene los permisos adecuados (ej. `SuperAdmin`, `Admin`) usando `$data->except(...)`.
        -   **Ejemplo**: `CurrencyResponseData.php`
            ```php
            namespace App\Data\Currency;

            use App\Models\Currency;
            use Spatie\LaravelData\Data;

            class CurrencyResponseData extends Data
            {
                public function __construct(
                    public int $id,
                    public string $name,
                    public string $symbol,
                    public string $createdAt,
                    public string $updatedAt,
                    public int $createdBy,
                    public int $updatedBy,
                ) {}

                public static function fromModel(Currency $currency): self
                {
                    return new self(
                        id: $currency->Id,
                        name: $currency->Name,
                        symbol: $currency->Symbol,
                        createdAt: $currency->CreatedAt->toDateTimeString(),
                        updatedAt: $currency->UpdatedAt->toDateTimeString(),
                        createdBy: $currency->CreatedBy,
                        updatedBy: $currency->UpdatedBy,
                    );
                }
                // Ocultar campos de auditoría si el usuario no es admin
                public function with(): array
                {
                    if (!auth()->user()?->isAdmin()) { // Suponiendo un método isAdmin() en el modelo User
                        return $this->except('createdBy', 'updatedBy')->toArray();
                    }
                    return $this->toArray();
                }
            }
            ```

-   **Models (`app/Models/`)**:
    -   **Propósito**: Representan las tablas de la base de datos y sus relaciones.
    -   **Principio**: Extienden de `BaseModel` para heredar funcionalidades comunes (ej. manejo de `PascalCase` para campos) y definen `$table` y `casts`.
    -   **Ejemplo**: `Currency.php`
        ```php
        namespace App\Models;

        use Illuminate\Database\Eloquent\Factories\HasFactory;

        class Currency extends BaseModel
        {
            use HasFactory;

            protected $table = 'Currencies'; // Nombre de la tabla en PascalCase
            protected $primaryKey = 'Id';    // Clave primaria en PascalCase

            protected $fillable = [
                'Name',
                'Symbol',
                'CreatedBy',
                'UpdatedBy',
            ];

            protected $casts = [
                'CreatedAt' => 'datetime',
                'UpdatedAt' => 'datetime',
            ];
        }
        ```

## 🛠️ Estándar de Flujo CRUD (Store, Index, Update): Guía Paso a Paso

Para cada nuevo concepto o entidad en el sistema, se debe seguir estrictamente este orden de implementación para garantizar consistencia, seguridad y rendimiento:

### 1. Base de Datos y Persistencia

-   **Scripts SQL**:
    -   **Ubicación**: `database/scripts/{Modulo}Pkg/`.
    -   **Propósito**: Contienen la lógica de negocio y persistencia que se ejecuta directamente en la base de datos a través de procedimientos almacenados (SPs).
    -   `Create{Concepto}.sql`:
        -   **Función**: Inserta un nuevo registro.
        -   **Requisito**: Debe insertar `CreatedBy` y `UpdatedBy` con el mismo ID de usuario proporcionado.
        -   **Ejemplo (simplificado)**:
            ```sql
            -- database/scripts/CurrencyPkg/CreateCurrency.sql
            CREATE PROCEDURE CurrencyPkg.CreateCurrency (
                p_name VARCHAR(255),
                p_symbol VARCHAR(5),
                p_created_by INT
            )
            BEGIN
                INSERT INTO Currencies (Name, Symbol, CreatedBy, UpdatedBy)
                VALUES (p_name, p_symbol, p_created_by, p_created_by);
                SELECT Id, Name, Symbol, CreatedAt, UpdatedAt, CreatedBy, UpdatedBy FROM Currencies WHERE Id = LAST_INSERT_ID();
            END;
            ```
    -   `Get{Concepto}s.sql`:
        -   **Función**: Recupera uno o varios registros.
        -   **Requisito**: Debe incluir filtros opcionales (usando `COALESCE` o `IS NULL`) y paginación (`LIMIT/OFFSET`). Debe retornar `TotalCount` usando `COUNT(*) OVER()` para la paginación.
        -   **Ejemplo (simplificado)**:
            ```sql
            -- database/scripts/CurrencyPkg/GetCurrencies.sql
            CREATE PROCEDURE CurrencyPkg.GetCurrencies (
                p_search_term VARCHAR(255) DEFAULT NULL,
                p_limit INT DEFAULT 10,
                p_offset INT DEFAULT 0
            )
            BEGIN
                SELECT
                    Id, Name, Symbol, CreatedAt, UpdatedAt, CreatedBy, UpdatedBy,
                    COUNT(*) OVER() AS TotalCount
                FROM Currencies
                WHERE (COALESCE(p_search_term, '') = '' OR Name LIKE CONCAT('%', p_search_term, '%'))
                LIMIT p_limit OFFSET p_offset;
            END;
            ```
    -   `Update{Concepto}.sql`:
        -   **Función**: Actualiza un registro existente.
        -   **Requisito**: Debe usar `COALESCE` para permitir actualizaciones parciales, donde un valor `NULL` en el parámetro significa "no cambiar este campo".
        -   **Ejemplo (simplificado)**:
            ```sql
            -- database/scripts/CurrencyPkg/UpdateCurrency.sql
            CREATE PROCEDURE CurrencyPkg.UpdateCurrency (
                p_id INT,
                p_name VARCHAR(255) DEFAULT NULL,
                p_symbol VARCHAR(5) DEFAULT NULL,
                p_updated_by INT
            )
            BEGIN
                UPDATE Currencies
                SET
                    Name = COALESCE(p_name, Name),
                    Symbol = COALESCE(p_symbol, Symbol),
                    UpdatedBy = p_updated_by,
                    UpdatedAt = NOW()
                WHERE Id = p_id;
                SELECT Id, Name, Symbol, CreatedAt, UpdatedAt, CreatedBy, UpdatedBy FROM Currencies WHERE Id = p_id;
            END;
            ```

-   **Migración de SPs**:
    -   **Propósito**: Asegurar que los procedimientos almacenados estén versionados y se desplieguen automáticamente.
    -   **Requisito**: Una migración que cree el esquema `"{Modulo}Pkg"` (si no existe) y cargue los archivos `.sql` correspondientes.

#### Convenciones de Nomenclatura en Migraciones:

-   **Claves Primarias**: Siempre se nombran `Id` (PascalCase).
    -   **Ejemplo**: `Schema::create('Currencies', function (Blueprint $table) { $table->id('Id'); ... });`
-   **Columnas de Auditoría**:
    -   `CreatedAt`, `UpdatedAt` (PascalCase): Usan `useCurrent()` para establecer la fecha y hora automáticamente.
    -   `CreatedBy`, `UpdatedBy` (PascalCase): Son `foreignId` que referencian `Users.Id`.
    -   **Ejemplo**:
        ```php
        $table->timestamp('CreatedAt')->useCurrent();
        $table->timestamp('UpdatedAt')->useCurrent()->useCurrentOnUpdate();
        $table->foreignId('CreatedBy')->constrained('Users', 'Id');
        $table->foreignId('UpdatedBy')->constrained('Users', 'Id');
        ```
-   **Nombres de Tablas**: Generalmente en plural y PascalCase (ej. `Users`, `Currencies`). Algunas migraciones heredadas pueden usar minúsculas (ej. `loans`), pero la convención preferida es PascalCase.
-   **Claves Foráneas**:
    -   Para `CreatedBy` y `UpdatedBy`, se usa `foreignId`.
    -   Para otras claves foráneas, se usa `unsignedBigInteger` seguido de `foreign()` y `references('Id')`.
    -   **Nomenclatura**: Suelen ser `[NombreTablaRelacionada]Id` (ej. `CurrencyId`, `UserId`).
    -   **Ejemplo**: `$table->unsignedBigInteger('CurrencyId')->foreign('CurrencyId')->references('Id')->on('Currencies');`

### 2. Capa de Datos (Eloquent)

-   **Modelo**:
    -   **Requisito**: Extender `BaseModel`.
    -   **Definición**: Definir `$table` (nombre de la tabla en PascalCase) y `casts` para los tipos de datos.
-   **Repository Interface**:
    -   **Definición**: Definir los métodos `create`, `getAll`, `update` (y otros CRUD necesarios) con sus tipos de datos de entrada (DTOs) y salida (Modelos o Colecciones).
-   **Repository Implementation**:
    -   **Interacción con SPs**: Utilizar el método `callProcedure` de `BaseRepository` para ejecutar los procedimientos almacenados.
    -   **Manejo de Errores**: Mapear errores devueltos por el SP (donde `ErrorId > 0`) a `ValidationException` o `Exception` de PHP para un manejo consistente.
    -   **Hidratación**: Utilizar `mapResultToModel` para convertir un resultado de SP a una instancia de `Model` o `mapResultsToCollection` para una colección de `Modelos`.

### 3. Capa de Negocio (Actions & Requests)

-   **Requests (DTOs)**:
    -   **Validación**: Clases `Data` (de `spatie/laravel-data`) con atributos de validación de PHP 8 para asegurar la integridad de los datos de entrada.
-   **Responses (DTOs)**:
    -   **Formateo**: Clases `Data` con un método estático `fromModel` para transformar instancias de `Model` en DTOs de respuesta.
    -   **Seguridad**: Implementar lógica en el método `with()` del DTO para ocultar campos de auditoría (`CreatedBy`, `UpdatedBy`) si el usuario autenticado no tiene roles específicos (ej. `SuperAdmin`, `Admin`).

-   **Actions**:
    -   `CreateAction`: Inyecta el `Repository` y llama a su método `create`, pasando el DTO de Request y devolviendo un DTO de Response.
    -   `GetAction`: Inyecta el `Repository` y llama a su método `getAll` (posiblemente con filtros y paginación), envolviendo el resultado en `PaginatedResponse::make()` si aplica.
    -   `UpdateAction`: Inyecta el `Repository` y llama a su método `update`, pasando el ID y el DTO de Request.

### 4. Capa de Entrada (API)

-   **Controller**:
    -   **Inyección**: Los métodos `store`, `index`, `update` (y otros) inyectan la `Action` correspondiente en sus parámetros.
    -   **Delegación**: Delegan la ejecución de la lógica de negocio a la `Action` inyectada.
-   **Ruta**:
    -   **Definición**: Un archivo dedicado en `routes/api/` para el módulo específico.
    -   **Registro**: El archivo de rutas del módulo se registra en `bootstrap/app.php`.

## 📌 Estado de Implementación

Esta sección detalla el progreso actual de los módulos, lo que permite a los asistentes de IA identificar rápidamente las áreas completadas y las que requieren desarrollo o revisión.

### Módulos Completados:
Estos módulos tienen su CRUD completo implementado, incluyendo scripts SQL, migraciones, modelos, interfaces y implementaciones de repositorio, acciones, DTOs de request y response, y controladores con rutas.

-   **CostCenter**: CRUD completo.
-   **Currency**: CRUD completo.
-   **Event**: CRUD completo (incluye `CurrencyId`).
-   **PaymentMethod**: CRUD completo.

### Módulos en Desarrollo:
Estos módulos han iniciado su implementación. Esto significa que es probable que existan las interfaces de repositorio, las implementaciones básicas y las acciones, pero la lógica de negocio completa, los procedimientos almacenados o los DTOs podrían estar aún en progreso o requerir ajustes.

-   **Commitment**: Implementación de repositorios y acciones iniciada.
-   **Transaction**: Implementación de repositorios y acciones iniciada.
-   **Loan**: Implementación de repositorios y acciones iniciada.
-   **MonthlyBalance**: Implementación de repositorios y acciones iniciada.
-   **PendingExpense**: Implementación de repositorios y acciones iniciada.

---
*Nota: Este estándar garantiza consistencia, seguridad de auditoría y rendimiento mediante la centralización de la lógica de persistencia en la base de datos y una clara separación de responsabilidades en el código PHP.*
