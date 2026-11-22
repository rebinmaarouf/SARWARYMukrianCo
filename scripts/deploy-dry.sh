#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.deploy.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "❌ Error: .deploy.env not found at $ENV_FILE"
  exit 1
fi

# Safely parse .deploy.env without shell variable expansion
get_env() {
  local key="$1"
  python3 - "$ENV_FILE" "$key" << 'PYEOF'
import sys
path, target = sys.argv[1], sys.argv[2]
with open(path) as f:
    for line in f:
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            if k.strip() == target:
                v = v.strip().strip('"').strip("'")
                print(v, end="")
                sys.exit(0)
PYEOF
}

SSH_HOST="$(get_env SSH_HOST)"
SSH_PORT="$(get_env SSH_PORT)"
SSH_USER="$(get_env SSH_USER)"
SSH_KEY="$(get_env SSH_KEY)"
SSH_KEY="${SSH_KEY/#\~/$HOME}"

REMOTE_WEB_ROOT="/home/${SSH_USER}/public_html"
REMOTE_CORE="${REMOTE_WEB_ROOT}/SarwarCo"
SSH_CMD="ssh -p ${SSH_PORT} -o BatchMode=yes -o ConnectTimeout=10 -o SendEnv=none -i ${SSH_KEY}"

COMMON_EXCLUDES=(
  --exclude='.env'
  --exclude='.env.*'
  --exclude='.deploy.env'
  --exclude='.htaccess'
  --exclude='.htaccess.*'
  --exclude='storage/'
  --exclude='vendor/'
  --exclude='node_modules/'
  --exclude='.git/'
  --exclude='.gitignore'
  --exclude='.gitattributes'
  --exclude='tests/'
  --exclude='docker/'
  --exclude='backups/'
  --exclude='.DS_Store'
  --exclude='__MACOSX'
  --exclude='database.sqlite'
)

echo "========================================================"
echo "🔍 [DRY-RUN] Checking what would change on ${SSH_HOST}"
echo "   (No files will be uploaded or modified on the server)"
echo "========================================================"
echo ""

echo "--- 1. Backend Core Changes (local backend/ -> ${REMOTE_CORE}/) ---"
rsync -rcni --dry-run \
  "${COMMON_EXCLUDES[@]}" \
  -e "$SSH_CMD" \
  "$ROOT_DIR/backend/app" \
  "$ROOT_DIR/backend/routes" \
  "$ROOT_DIR/backend/resources" \
  "$ROOT_DIR/backend/config" \
  "$ROOT_DIR/backend/database" \
  "$ROOT_DIR/backend/artisan" \
  "$ROOT_DIR/backend/composer.json" \
  "$ROOT_DIR/backend/composer.lock" \
  "${SSH_USER}@${SSH_HOST}:${REMOTE_CORE}/" | grep -v '^\.f\.\.T\.\.\.\.' || echo "  (No backend content changes detected)"

echo ""
echo "--- 2. Frontend Build Assets (local backend/public/build/ -> ${REMOTE_WEB_ROOT}/build/) ---"
if [[ -d "$ROOT_DIR/backend/public/build" ]]; then
  rsync -rcni --dry-run \
    "${COMMON_EXCLUDES[@]}" \
    -e "$SSH_CMD" \
    "$ROOT_DIR/backend/public/build/" \
    "${SSH_USER}@${SSH_HOST}:${REMOTE_WEB_ROOT}/build/" | grep -v '^\.f\.\.T\.\.\.\.' || echo "  (No build asset changes detected)"
else
  echo "  ⚠️ backend/public/build does not exist yet (run 'cd frontend && npm run build' first)"
fi

echo ""
echo "========================================================"
echo "✅ Dry-run complete. Zero changes were made on the server."
echo "========================================================"
