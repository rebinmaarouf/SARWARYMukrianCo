#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.deploy.env"
BACKUP_DIR="$ROOT_DIR/backups"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "❌ Error: .deploy.env not found at $ENV_FILE"
  exit 1
fi

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
REMOTE_PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
SSH_CMD="ssh -p ${SSH_PORT} -o BatchMode=yes -o ConnectTimeout=10 -o SendEnv=none -i ${SSH_KEY}"

LATEST_FILES_BAK="${1:-$(ls -1t "$BACKUP_DIR"/prod-files-*.tar.gz 2>/dev/null | head -n 1 || true)}"

if [[ -z "$LATEST_FILES_BAK" || ! -f "$LATEST_FILES_BAK" ]]; then
  echo "❌ Error: No file backup archive found in $BACKUP_DIR"
  exit 1
fi

echo "========================================================"
echo "⏪ Rolling back server ${SSH_HOST} from backup:"
echo "   Archive: ${LATEST_FILES_BAK}"
echo "========================================================"

# Stream backup archive to server, never overwriting .env or .htaccess security locks
cat "$LATEST_FILES_BAK" | $SSH_CMD "${SSH_USER}@${SSH_HOST}" "
export LC_ALL=C
set -e
tar -xzf - -C ${REMOTE_WEB_ROOT} \
  --exclude='SarwarCo/.env' \
  --exclude='SarwarCo/storage' \
  --exclude='.htaccess' \
  --exclude='SarwarCo/.htaccess'

cd ${REMOTE_CORE}
${REMOTE_PHP} artisan optimize:clear
${REMOTE_PHP} artisan config:cache
${REMOTE_PHP} artisan route:cache
${REMOTE_PHP} artisan view:cache
chmod -R 775 storage bootstrap/cache
"

ENV_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/SarwarCo/.env")"
HOME_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/")"

echo "   - Security /SarwarCo/.env -> HTTP ${ENV_CODE} (Expected: 403)"
echo "   - Homepage /              -> HTTP ${HOME_CODE} (Expected: 200)"
echo "========================================================"
echo "✅ Rollback completed successfully."
echo "========================================================"
