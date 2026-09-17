#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
COMPOSE_FILE="${REPO_ROOT}/tooling/docker/docker-compose.yml"

echo "=== Starting Bunova Local Dependencies ==="

if command -v docker >/dev/null 2>&1; then
    echo "Found docker. Starting containers via Docker Compose..."
    docker compose -f "${COMPOSE_FILE}" up -d
    echo "Waiting for services to become healthy..."
    docker compose -f "${COMPOSE_FILE}" ps
    echo "Containers are starting in background."
else
    echo "Notice: docker command not found in current environment."
    echo "For native workstation execution without Docker:"
    echo "  - Database: Use SQLite (default in .env.example) or native PostgreSQL on port 5432"
    echo "  - Cache/Queue: Use native Redis (redis-server) or database drivers"
    echo "  - Object storage: Local disk storage driver is active"
fi

echo ""
echo "Verifying application health via Artisan..."
if [ -f "${REPO_ROOT}/backend/artisan" ]; then
    (cd "${REPO_ROOT}/backend" && php artisan health:check || true)
fi

echo "=== Startup script completed ==="
