# Products & Multi-Currency REST API

Production-ready, Clean Architecture (Hexagonal / ADR) RESTful API developed with Laravel 13, PHP 8.4, and MySQL 8.0, featuring multi-currency product management, Sanctum API authentication, automated testing with Object Mothers, and complete OpenAPI/Swagger documentation.

---

## 🏛️ Architecture Overview

The system implements **Hexagonal Architecture (Ports and Adapters)** decoupled into Bounded Modules:

```text
app/
├── Modules/
│   ├── Product/
│   │   ├── Domain/
│   │   │   ├── Repositories/        # Port interfaces (Pure PHP contracts)
│   │   │   └── Exceptions/          # Domain exceptions with custom HTTP rendering
│   │   ├── Application/
│   │   │   ├── Actions/             # Atomic use cases
│   │   │   └── DTOs/                # Strongly-typed immutable Data Transfer Objects
│   │   └── Infrastructure/
│   │       ├── Persistence/Eloquent/ # Eloquent models and repository implementations
│   │       └── Http/                # Single Action Controllers (__invoke), FormRequests, Resources
│   ├── Currency/                    # Multi-currency domain models, repositories & seeders
│   └── Auth/                        # Sanctum Bearer token authentication
└── Shared/
    └── Infrastructure/
        ├── Middleware/              # Cyber-security headers & rate limiters
        └── Providers/               # Dependency injection container bindings
```

### Key Architectural Principles
- **Single Responsibility Principle (SRP)**: Each endpoint is served by an atomic Single Action Controller (`__invoke()`).
- **Dependency Inversion Principle (DIP)**: Application Actions depend solely on domain repository interfaces (`ProductRepositoryInterface`), not Eloquent directly.
- **Self-Documenting Code**: Strict type declarations (`declare(strict_types=1);`), explicit parameter/return types, and zero clutter comments.
- **Clean Exception Handling**: Domain exceptions map directly to semantic HTTP status codes (`404 Not Found`, `422 Unprocessable Content`).

---

## 🔒 Cybersecurity Measures

1. **Authentication & Token Revocation**: Laravel Sanctum with hashed bearer tokens.
2. **Rate Limiting**: Throttling configured at 60 requests per minute per IP/user (`throttle:60,1`).
3. **HTTP Security Headers**:
   - `X-Frame-Options: DENY`
   - `X-Content-Type-Options: nosniff`
   - `X-XSS-Protection: 1; mode=block`
   - `Referrer-Policy: strict-origin-when-cross-origin`
   - `Permissions-Policy: geolocation=(), camera=(), microphone=()`
   - Removal of `X-Powered-By` header.
4. **Input Sanitization & Mass-Assignment Prevention**: Strict `FormRequest` validation rules and guarded model attributes.
5. **SQL Injection Defense**: Prepared statements via PDO and Eloquent parameter binding.

---

## 🚀 Getting Started with Docker

### 1. Prerequisites
- [Docker Engine & Docker Compose](https://www.docker.com/)

### 2. Start Containers
From the project root:
```bash
docker compose up -d
```
Services initialized:
- `laravel_web`: Nginx (`http://localhost:8000`)
- `laravel_app`: PHP 8.4 FPM
- `laravel_db`: MySQL 8.0 (Port `3306`)

### 3. Run Migrations & Seeders
```bash
docker compose exec app php artisan migrate --seed
```
Pre-seeded data includes:
- **Currencies**:
  - `USD`: US Dollar (`$`, exchange rate `1.0000`)
  - `EUR`: Euro (`€`, exchange rate `0.9200`)
  - `VES`: Venezuelan Bolivar (`Bs.`, exchange rate `36.5000`)
- **Products & Prices**: Sample products with multi-currency records.
- **Admin User**:
  - Email: `admin@example.com`
  - Password: `password123`
  - Pre-generated static token: `test-token-backend-senior-2026`

---

## 🧪 Automated Testing (ATDD / Unit & Feature)

The test suite covers 100% of the domain rules, repository mocks, and HTTP integration contracts:

```bash
docker compose exec app php artisan test
```

### Testing Strategy
- **Unit Tests**: Domain Actions tested in complete isolation using Mockery mocks and **Object Mothers** (`ProductMother`, `CurrencyMother`).
- **Feature Tests**: End-to-end HTTP lifecycle tests against SQLite in-memory database verifying status codes, JSON structures, validations, and security headers.

---

## 📖 API Documentation & Postman / Insomnia

### Interactive Swagger UI
Open in your browser:
👉 **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**

Click **Authorize** and input:
```text
test-token-backend-senior-2026
```

### Ready-to-use Collections
- **Postman**: Import `docs/postman_collection.json`
- **Insomnia**: Import `docs/insomnia_collection.json`

---

## 📡 Endpoints Summary

| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `POST` | `/api/v1/auth/login` | Authenticate and retrieve Bearer token | No |
| `POST` | `/api/v1/auth/logout` | Revoke current Bearer token | Yes |
| `GET` | `/api/v1/products` | Paginated catalog list with gross margin | Yes |
| `POST` | `/api/v1/products` | Create product with base currency | Yes |
| `GET` | `/api/v1/products/{id}` | Retrieve product details | Yes |
| `PUT` | `/api/v1/products/{id}` | Update product attributes | Yes |
| `DELETE` | `/api/v1/products/{id}` | Delete product and associated prices | Yes |
| `GET` | `/api/v1/products/{id}/prices` | List secondary currency prices for product | Yes |
| `POST` | `/api/v1/products/{id}/prices` | Assign new currency price to product | Yes |

*(Canonical aliases without the `/v1` prefix like `/api/products` are also supported).*

---

## 🔭 Architectural Evolution: Path to Multi-Tenancy (SaaS)

To scale this application into a Multi-Tenant SaaS platform:
1. **Tenant Resolution**: Implement a `TenantResolutionMiddleware` resolving tenant identity via subdomain (`{tenant}.domain.com`) or header `X-Tenant-ID`.
2. **Data Isolation Strategy**:
   - *Soft Isolation*: Add a global Eloquent scope (`TenantScope`) filtering queries by `tenant_id`.
   - *Strict Isolation*: Implement dynamic database connection switching per tenant request via `DB::purge()` and `Config::set('database.connections.tenant.database', ...)`.
3. **Decoupled Domain**: Because our Application Actions depend on `ProductRepositoryInterface`, the domain layer remains 100% untouched; only the infrastructure repository adapter needs tenant scoping.
