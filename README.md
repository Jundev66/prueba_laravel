# API RESTful de Gestión de Productos y Precios Multi-Divisa

API RESTful empresarial desarrollada con **Laravel 11 / 12**, **PHP 8.4** y **MySQL 8.0**, implementando **Arquitectura Hexagonal (Puertos y Adaptadores)**, principios de **Domain-Driven Design (DDD)** y separación de comandos y consultas (**CQS**).

El sistema administra un catálogo de productos con estructura de costos (`tax_cost`, `manufacturing_cost`), cálculo automatizado de **Margen Bruto**, precios secundarios en múltiples divisas (`USD`, `EUR`, `VES`), autenticación mediante tokens revocables con **Laravel Sanctum**, suite de pruebas automatizadas y documentación interactiva con **OpenAPI / Swagger**.

---

## 🏛️ Arquitectura del Sistema (DDD & Hexagonal)

La solución está organizada en módulos acotados (*Bounded Contexts*) desacoplados de los detalles del framework:

```text
app/
├── Modules/
│   ├── Product/
│   │   ├── Domain/
│   │   │   ├── Product.php                  # Entidad raíz del Agregado con cálculo de Margen Bruto
│   │   │   ├── ProductPrice.php             # Entidad de Dominio para precios secundarios
│   │   │   ├── Repositories/                # Puertos de Dominio (Interfaces)
│   │   │   └── Exceptions/                  # Excepciones puras de Dominio
│   │   │
│   │   ├── Application/                     # Casos de uso aislados bajo CQS (void en Commands)
│   │   │   ├── Create/ProductCreator.php
│   │   │   ├── Find/ProductFinder.php
│   │   │   ├── SearchAll/ProductsSearcher.php
│   │   │   ├── Update/ProductUpdater.php
│   │   │   ├── Delete/ProductDeleter.php
│   │   │   ├── AssignPrice/ProductPriceAssigner.php
│   │   │   └── FindPrices/ProductPricesFinder.php
│   │   │
│   │   └── Infrastructure/
│   │       ├── Persistence/Eloquent/        # Adaptadores de base de datos
│   │       │   ├── Filters/                 # Filtros dedicados (ProductEloquentFilter)
│   │       │   ├── Models/                  # Modelos Eloquent
│   │       │   └── Repositories/            # Repositorios con transacciones atómicas
│   │       └── Http/                        # Controladores de acción única (__invoke), Requests y Resources
│   │
│   ├── Currency/
│   │   ├── Domain/                          # Entidad Currency y RepositoryInterface
│   │   ├── Application/SearchAll/           # Caso de uso CurrenciesSearcher y SearchCurrenciesQuery
│   │   └── Infrastructure/                  # CurrencyEloquentFilter, Repositorio Eloquent y Controller
│   │
│   └── Auth/                                # Autenticación y revocación de tokens Sanctum
│
└── Shared/
    └── Infrastructure/
        ├── Middleware/                      # Headers de seguridad HTTP y rate limiting
        └── Providers/                       # Enlace de interfaces con implementaciones (DIP)
```

### Reglas de Diseño Aplicadas
1. **Dominio Puro**: Las entidades de dominio no extienden de Eloquent ni dependen de Laravel. Protegen sus invariantes mediante métodos y *guard clauses*.
2. **CQS Estricto (Command Query Separation)**: Las mutaciones (`Commands`) no devuelven representación de estado (`void`). Las lecturas (`Queries`) retornan DTOs inmutables sin efectos secundarios.
3. **Filtros Desacoplados**: Los filtros de consulta residen en clases especializadas en infraestructura (`ProductEloquentFilter`), respetando el principio de Responsabilidad Única (SRP).
4. **Tipado Estricto**: `declare(strict_types=1);` en todos los archivos del proyecto.
5. **Cero Comentarios de Código**: Código autodocumentado apoyado en tipado, nombres semánticos y atributos nativos de PHP 8.

---

## 🚀 Despliegue Rápido con Docker

### 1. Clonar el Repositorio y Configurar Variables de Entorno
```bash
git clone https://github.com/Jundev66/prueba_laravel.git
cd prueba_laravel
cp .env.example .env
```
*(En Windows PowerShell: `copy .env.example .env`).*

### 2. Iniciar Contenedores Docker
```bash
docker compose up -d --build
```
Servicios inicializados:
* `laravel_web`: Servidor Web Nginx (`http://localhost:8000`).
* `laravel_app`: Contenedor PHP 8.4 FPM.
* `laravel_db`: Base de datos MySQL 8.0 (Puerto `3306`).

### 3. Instalar Dependencias, Generar Clave y Ejecutar Migraciones
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Esto generará automáticamente:
* **Divisas del catálogo**:
  * `USD`: Dólar Estadounidense (`$`, tipo de cambio base `1.0000`).
  * `EUR`: Euro (`€`, tipo de cambio `0.9200`).
  * `VES`: Bolívar Venezolano (`Bs.`, tipo de cambio `36.5000`).
* **Productos de demostración** con sus respectivos costos y precios multi-divisa.
* **Usuario Administrador**:
  * **Email**: `admin@example.com`
  * **Password**: `password123`
  * **Token estático predeterminado**: `1|catalog_access_token`

### 4. Ejecutar la Suite de Pruebas
```bash
docker compose exec app php artisan test
```
*Ejecutará los 51 tests unitarios y de integración pasando al 100% (166 aserciones).*

---

## 🔑 Autenticación y Uso del Token Bearer

Todos los endpoints protegidos requieren el encabezado HTTP:
```http
Authorization: Bearer <TU_TOKEN_AQUI>
Accept: application/json
```

### Flujo de Autenticación

#### 1. Iniciar Sesión (`POST /api/v1/auth/login`)
* **Endpoint**: `http://localhost:8000/api/v1/auth/login`
* **Método**: `POST`
* **Headers**: `Content-Type: application/json`, `Accept: application/json`
* **Payload**:
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```
* **Respuesta (200 OK)**:
```json
{
  "token_type": "Bearer",
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "Lead Architect",
    "email": "admin@example.com"
  }
}
```

#### 2. Usar el Token en Peticiones Posteriores
Copia el valor de `access_token` y envíalo en el encabezado `Authorization`:
```bash
curl -X GET http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

*(También puedes utilizar directamente el token predeterminado: `1|catalog_access_token`).*

#### 3. Cerrar Sesión y Revocar Token (`POST /api/v1/auth/logout`)
Invalida el token activo en la base de datos de forma inmediata:
```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer <TU_TOKEN>" \
  -H "Accept: application/json"
```
* **Respuesta**: HTTP `200 OK` con `{"message": "Token revoked successfully."}`. Cualquier intento posterior de reutilizar ese token será rechazado con `401 Unauthorized`.

---

## 📡 Catálogo Completo de Endpoints

### 1. Módulo de Divisas (`Currencies`)

#### `GET /api/v1/currencies`
Obtiene el catálogo de monedas admitidas y sus tasas de conversión con respecto a la divisa base.
* **Parámetros Query**:
  * `q` *(opcional, string)*: Búsqueda por coincidencia de nombre (ej. `?q=Dollar` o `?q=Euro`).
* **Respuesta (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "US Dollar",
      "symbol": "$",
      "exchange_rate": 1.0
    },
    {
      "id": 2,
      "name": "Euro",
      "symbol": "€",
      "exchange_rate": 0.92
    },
    {
      "id": 3,
      "name": "Bolivar",
      "symbol": "Bs.",
      "exchange_rate": 36.5
    }
  ]
}
```

---

### 2. Módulo de Productos (`Products`)

#### `GET /api/v1/products`
Listado paginado de productos con filtros combinables y cálculo dinámico de margen bruto.
* **Parámetros Query**:
  * `q` *(opcional, string)*: Filtro por nombre del producto.
  * `currency_id` *(opcional, int)*: Filtro por ID de la moneda base.
  * `min_price` *(opcional, float)*: Precio base mínimo.
  * `max_price` *(opcional, float)*: Precio base máximo.
  * `per_page` *(opcional, int, default: 15, max: 100)*: Límite por página (protegido contra saturación de memoria).
  * `page` *(opcional, int, default: 1)*: Número de página.
* **Ejemplo**: `GET /api/v1/products?q=Laptop&currency_id=1&min_price=1000&max_price=2000`
* **Respuesta (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Laptop Pro 16",
      "description": "Workstation laptop with 32GB RAM",
      "price": 1500.0,
      "currency": {
        "id": 1,
        "name": "US Dollar",
        "symbol": "$",
        "exchange_rate": 1.0
      },
      "tax_cost": 240.0,
      "manufacturing_cost": 850.0,
      "gross_margin": 410.0,
      "prices": [
        {
          "id": 1,
          "product_id": 1,
          "currency_id": 2,
          "price": 1380.0,
          "currency": {
            "id": 2,
            "name": "Euro",
            "symbol": "€",
            "exchange_rate": 0.92
          }
        }
      ]
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  },
  "links": {
    "first": "http://localhost:8000/api/v1/products?page=1",
    "last": "http://localhost:8000/api/v1/products?page=1"
  }
}
```

#### Fórmula del Margen Bruto (`gross_margin`)
Calculado automáticamente en la entidad de dominio:
$$\text{Margen Bruto} = \text{price} - (\text{tax\_cost} + \text{manufacturing\_cost})$$
*Ejemplo*: $1500 - (240 + 850) = 410.00$.

#### `POST /api/v1/products`
Registra un nuevo producto con su divisa base obligatoria.
* **Payload**:
```json
{
  "name": "Mechanical Keyboard Pro",
  "description": "Custom mechanical switch keyboard",
  "price": 120.00,
  "currency_id": 1,
  "tax_cost": 19.20,
  "manufacturing_cost": 45.00
}
```
* **Respuesta (201 Created)**:
```json
{
  "message": "Product created successfully."
}
```

#### `GET /api/v1/products/{id}`
Obtiene el detalle completo de un producto por su identificador numérico.
* **Respuesta (200 OK)**: Retorna la entidad producto con sus relaciones y su margen bruto calculado.
* **Respuesta si no existe (404 Not Found)**:
```json
{
  "error": {
    "code": "PRODUCT_NOT_FOUND",
    "message": "Product with ID <999> was not found."
  }
}
```

#### `PUT` o `PATCH /api/v1/products/{id}`
Actualiza los atributos de un producto existente. Permite actualización completa (`PUT`) o parcial de campos específicos (`PATCH`).
* **Payload (parcial o completo)**:
```json
{
  "price": 135.00,
  "manufacturing_cost": 40.00
}
```
* **Respuesta (200 OK)**:
```json
{
  "message": "Product updated successfully."
}
```

#### `DELETE /api/v1/products/{id}`
Elimina un producto del catálogo y remueve en cascada todos sus precios secundarios asociados.
* **Respuesta (204 No Content)**: Cuerpo vacío.

---

### 3. Módulo de Precios Multi-Divisa (`Product Prices`)

#### `GET /api/v1/products/{id}/prices`
Lista todos los precios secundarios registrados para el producto especificado.
* **Respuesta (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "currency_id": 2,
      "price": 1380.0,
      "currency": {
        "id": 2,
        "name": "Euro",
        "symbol": "€",
        "exchange_rate": 0.92
      }
    }
  ]
}
```

#### `POST /api/v1/products/{id}/prices`
Asigna un precio secundario en una divisa alternativa.
* **Payload**:
```json
{
  "currency_id": 2,
  "price": 1380.00
}
```
* **Respuesta (201 Created)**:
```json
{
  "message": "Product price assigned successfully."
}
```

#### Invariantes de Negocio Protegidas (Excepciones de Dominio)
1. **Conflicto con la divisa base**: No se permite registrar como precio secundario la misma moneda que ya tiene configurada como moneda base.
   * **Respuesta (422 Unprocessable Content)**:
   ```json
   {
     "error": {
       "code": "BASE_CURRENCY_CANNOT_BE_SECONDARY",
       "message": "Currency ID <1> is the base currency of this product and cannot be registered as a secondary price."
     }
   }
   ```
2. **Duplicidad de divisa secundaria**: No se permite registrar dos veces la misma divisa secundaria para un mismo producto.
   * **Respuesta (422 Unprocessable Content)**:
   ```json
   {
     "error": {
       "code": "CURRENCY_PRICE_ALREADY_EXISTS",
       "message": "A price for currency ID <2> is already assigned to this product."
     }
   }
   ```

---

## 🛡️ Medidas de Ciberseguridad Implementadas

1. **Tokens Hacheados en Reposo**: Laravel Sanctum almacena tokens cifrados con SHA-256.
2. **Protección DDoS y Fuerza Bruta**: Rate limiting activo a nivel de middleware (`throttle:60,1`) bloqueando excesos con código `429 Too Many Requests`.
3. **Encabezados HTTP de Seguridad Activos**:
   * `X-Frame-Options: DENY` (Anti Clickjacking).
   * `X-Content-Type-Options: nosniff` (Anti MIME Sniffing).
   * `X-XSS-Protection: 1; mode=block` (Filtro contra Cross-Site Scripting).
   * `Referrer-Policy: strict-origin-when-cross-origin`.
   * `Permissions-Policy: geolocation=(), camera=(), microphone=()`.
   * Eliminación del encabezado revelador `X-Powered-By`.
4. **Prevención de DoS por Paginación**: Parámetro `per_page` validado con tope máximo de 100 elementos.
5. **Defensa contra Inyecciones SQL**: Consultas parametrizadas mediante PDO y repositorios Eloquent desacoplados.

---

## 🧪 Pruebas Automatizadas (Unitarias y de Integración)

Para ejecutar la suite completa de pruebas:
```bash
docker compose exec app php artisan test
```

### Resultados de la Suite:
* **51 pruebas automatizadas pasando al 100%** (166 aserciones).
* **Unit Tests**: Casos de uso y entidades de dominio probados en memoria pura en milisegundos mediante mocks y *Object Mothers* (`ProductMother`, `CurrencyMother`, `ProductPriceMother`).
* **Feature Tests**: Pruebas de integración HTTP contra base de datos aislada evaluando contratos JSON, códigos de estado, encabezados de seguridad y restricciones de negocio.

---

## 📖 Documentación Interactiva y Colecciones

### Swagger UI Interactivo
Abre en tu navegador:
👉 **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**

**Pasos para autenticarte en Swagger UI**:
1. Haz clic en el botón verde **Authorize** (con el ícono de candado abierto 🔓) en la esquina superior derecha.
2. En la ventana modal que se abre (*Available authorizations*), ubica la sección `bearerAuth` y el campo de texto vacío **`Value:`**.
3. Pega el token directamente allí:
```text
1|catalog_access_token
```
*(Nota: No escribas la palabra `Bearer`, Swagger la añade automáticamente).*
4. Haz clic en el botón **Authorize** dentro del modal y luego en **Close**. El candado cambiará a cerrado 🔒.
5. ¡Listo! Cualquier endpoint que pruebes con los botones **"Try it out" ➔ "Execute"** enviará automáticamente el encabezado `Authorization: Bearer <token>`.

### Colecciones para Postman e Insomnia
En la carpeta `docs/` encontrarás las definiciones listas para importar:
* **Postman**: `docs/postman_collection.json`
* **Insomnia**: `docs/insomnia_collection.json`

Ambas colecciones incluyen variables de entorno preconfiguradas (`base_url: http://localhost:8000/api/v1` y `bearer_token`).

---

## 🔭 Evolución Arquitectónica: Camino a Multi-Tenancy (SaaS)

Para escalar este sistema a un modelo SaaS multi-empresa:
1. **Resolución de Inquilinos**: Implementar un middleware (`TenantResolutionMiddleware`) que determine el tenant a partir del subdominio (`tenant.app.com`) o un header `X-Tenant-ID`.
2. **Aislamiento de Datos**:
   * *Aislamiento Lógico*: Scope global de Eloquent filtrando por `tenant_id`.
   * *Aislamiento Estricto*: Conexión dinámica por inquilino mediante `DB::purge()` y selección de base de datos dedicada.
3. **Independencia del Dominio**: Debido a que los casos de uso dependen exclusivamente de interfaces (`ProductRepositoryInterface`), el Core Domain permanece intacto; únicamente el adaptador de infraestructura gestiona la partición de datos.
