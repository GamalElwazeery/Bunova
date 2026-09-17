# Bunova Environment & Configuration Strategy

This document establishes the official environment lifecycle, secret safety conventions, and validation strategy for Bunova across local development, testing, staging, and production.

---

## 1. Environment Topology

| Environment | Purpose | Database | Cache & Session | Queue | Realtime | Storage | Debug Mode |
|---|---|---|---|---|---|---|---|
| **`local`** | Workstation development | SQLite / Local Postgres | Database / Redis | Database / Redis | Log / Reverb | Local | `true` |
| **`testing`** | Automated CI / test execution | SQLite (`:memory:`) | Array | Sync | Null | Local (temp) | `true` |
| **`staging`** | Pre-production validation & pilot testing | PostgreSQL 16 | Redis 7 | Redis 7 | Reverb | S3 / MinIO | `false` |
| **`production`** | Live café operations & Bunova Cloud | PostgreSQL 16 (HA) | Redis 7 Cluster | Redis 7 | Reverb | S3 | `false` |

---

## 2. Secret Safety & Zero Leakage Policies

1. **Git Exclusions**:
   - All live environment files (`.env`, `.env.production`, `.env.staging`, `.env.backup`) are strictly ignored by `.gitignore`.
   - Only `.env.example` (template with empty secrets) and `.env.testing` (hermetic, non-production test keys) are tracked in git.
2. **Mandatory Secrets Validation**:
   - The CLI command `php artisan env:validate` executes in CI and deployment pre-flight checks.
   - In `production`:
     - `APP_DEBUG` must strictly be `false`.
     - `APP_KEY` must be a valid 32-byte base64 key.
     - `BUNOVA_DEVICE_TOKEN_SECRET` (used for signing operational hardware terminal tokens) must be set, 32+ characters, and free of generic placeholders.
     - `BUNOVA_PLATFORM_OPERATOR_SECRET` (used for Bunova Platform Admin sessions) must be set, 32+ characters, and free of generic placeholders.
     - Database password must be non-empty.
     - In-memory SQLite is rejected as a primary database.
3. **Template Integrity**:
   - `.env.example` is actively tested by `EnvironmentConfigTest::test_env_example_is_secret_safe` to ensure no live secrets or default passwords ever enter version control.

---

## 3. Bilingual Configuration Defaults

In accordance with Bunova Content OS and Arab region operational standards:
- **`APP_LOCALE`**: Default is `ar` (Arabic) for all café operational, POS, and fiscal displays.
- **`APP_FALLBACK_LOCALE`**: Default is `en` (English) for technical fallback.
- **`APP_TIMEZONE`**: Default is `UTC` at database level; operational displays convert dynamically based on branch business-day configurations.

---

## 4. Operational Validation Commands

```bash
# Validate local environment configuration
php artisan env:validate

# Generate application key
php artisan key:generate

# Run full test suite with hermetic test environment
php artisan test
```
