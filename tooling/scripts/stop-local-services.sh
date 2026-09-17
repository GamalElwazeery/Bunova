#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
COMPOSE_FILE="${REPO_ROOT}/tooling/docker/docker-compose.yml"

echo "=== Stopping Bunova Local Dependencies ==="

if command -v docker >/dev/null 2>&1; then
    docker compose -f "${COMPOSE_FILE}" down
    echo "Containers stopped."
else
    echo "Docker not present; no container teardown required."
fi
