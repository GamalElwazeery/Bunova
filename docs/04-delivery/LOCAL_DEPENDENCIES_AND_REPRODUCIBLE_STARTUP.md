# Bunova Local Dependencies & Reproducible Startup Guide

This document establishes the reproducible setup, dependencies bootstrap, and health monitoring instructions for local development and staging verification of Bunova.

---

## 1. Infrastructure Services Overview

Bunova requires four primary infrastructure services for full-featured operation:
1. **Primary Database (PostgreSQL 16)**:
   - Port: `5432`
   - Default Database: `bunova`
   - Default User: `bunova` / `bunova_local_password`
   - Role: Core transactional storage for all 21 modular monolith bounded contexts.
2. **In-Memory Cache & Queue Broker (Redis 7)**:
   - Port: `6379`
   - Role: High-throughput caching, atomic locks, and Redis queue workers.
3. **Object Storage (MinIO / S3 compatible)**:
   - API Port: `9000`
   - Web Console Port: `9001`
   - Default Credentials: `bunova_admin` / `bunova_minio_password`
   - Default Bucket: `bunova-media` (auto-initialized by `minio-init` service)
   - Role: Storage for catalog product images, media assets, receipt snapshots, and fiscal evidence files.
4. **Realtime WebSocket Transport (Laravel Reverb)**:
   - Port: `8080`
   - Role: Low-latency local broadcast for KDS ticket updates, table status changes, and device session events.

---

## 2. Reproducible Startup: Docker Compose

For environments with Docker installed, a turnkey compose definition is provided at `tooling/docker/docker-compose.yml`.

### Quick Start
```bash
# Start all dependencies in the background
./tooling/scripts/start-local-services.sh

# Stop all dependencies
./tooling/scripts/stop-local-services.sh
```

### Direct Compose Commands
```bash
# Start services
docker compose -f tooling/docker/docker-compose.yml up -d

# View service health status
docker compose -f tooling/docker/docker-compose.yml ps

# View container logs
docker compose -f tooling/docker/docker-compose.yml logs -f postgres redis minio
```

---

## 3. Workstation Startup Without Docker

Bunova is designed to support lightweight development on developer workstations without container overhead:
- **Database**: SQLite is supported natively for local development. Simply set `DB_CONNECTION=sqlite` in `backend/.env`.
- **Cache & Session**: Can use `database` or native `redis-server` (port `6379`).
- **Storage**: `FILESYSTEM_DISK=local` stores files in `backend/storage/app/public`.

---

## 4. Health Probes & Verification

Bunova provides three tiers of automated health checks:

### 1. HTTP Heartbeat Probe (`/up`)
Standard Laravel 11 application liveness probe returning HTTP 200 when the framework kernel is responsive.
```bash
curl -I http://localhost:8000/up
```

### 2. Comprehensive Service Health API (`/api/v1/health`)
Structured JSON endpoint reporting individual status for database, cache, and storage:
```bash
curl -s http://localhost:8000/api/v1/health | jq .
```
Response:
```json
{
  "status": "healthy",
  "timestamp": "2026-09-17T00:00:00Z",
  "environment": "local",
  "services": {
    "database": { "status": "ok", "driver": "sqlite" },
    "cache": { "status": "ok", "store": "database" },
    "storage": { "status": "ok", "disk": "local" }
  }
}
```

### 3. Artisan CLI Probe (`php artisan health:check`)
Terminal health probe for deployment verification, CI pre-flight, and systemd/cron health audits:
```bash
cd backend
php artisan health:check
```
Exit Code `0` indicates all dependencies are reachable; exit code `1` indicates one or more degraded services.
