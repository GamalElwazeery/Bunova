# Tooling & Development Automation

This directory houses development automation, environment orchestration helpers, and local validation scripts for Bunova.

## Components
- **`docker/`**: Compose configurations for local dependency services (PostgreSQL 16, Redis 7, MinIO/S3 object storage, and Laravel Reverb).
- **`scripts/`**: Developer automation for environment bootstrap, database seeding, schema validation, and health checks.

## Operating Principles
- **No Unnecessary Microservices**: Bunova is built as a modular monolith first. All core domains reside in `backend/` with clean boundaries.
- **Reproducible Local Dev**: Dependencies can be run natively on the host or via standard container tooling without proprietary infrastructure dependencies.
