#!/usr/bin/env bash
set -euo pipefail

PROJECT_NAME="patchstash"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="$ROOT_DIR/infra/docker-compose.yml"

usage() {
  cat <<'EOF'
Usage: ./deploy.sh [command] [args...]

Commands:
  build        Build all containers
  up           Start containers in detached mode
  down         Stop and remove containers
  reset        Stop containers, remove volumes, rebuild from scratch
  refresh      Rebuild php + nginx without touching database
  logs         Tail logs for all containers
  shell        Open a bash shell inside the php container
  composer …   Run Composer inside the php container
  migrate      Run Doctrine migrations inside the php container
EOF
}

command -v docker >/dev/null 2>&1 || { echo "Docker is not installed or not on PATH."; exit 1; }

if docker compose version >/dev/null 2>&1; then
  COMPOSE_BIN="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  COMPOSE_BIN="docker-compose"
else
  echo "Docker Compose not found. Install Docker Desktop or docker-compose."
  exit 1
fi

run_compose() {
  $COMPOSE_BIN -p "$PROJECT_NAME" -f "$COMPOSE_FILE" "$@"
}

[ $# -gt 0 ] || { usage; exit 1; }

case "$1" in
  build)
    run_compose build --pull
    ;;
  up)
    run_compose up -d
    ;;
  down)
    run_compose down
    ;;
  reset)
    run_compose down -v --remove-orphans
    run_compose build --no-cache
    ;;
  refresh)
    run_compose build --no-cache php nginx
    run_compose up -d php nginx
    ;;
  logs)
    run_compose logs -f
    ;;
  shell)
    run_compose exec php bash
    ;;
  composer)
    shift
    run_compose exec php composer "$@"
    ;;
  migrate)
    run_compose exec php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
    ;;
  *)
    usage
    exit 1
    ;;
esac
