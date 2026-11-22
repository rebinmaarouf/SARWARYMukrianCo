#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.deploy.env"
BACKUP_DIR="$ROOT_DIR/backups"
ALLOW_DIRTY="${1:-}"

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
REMOTE_PHP="/opt/cpanel/ea-php83/root/usr/bin/php"
SSH_CMD="ssh -p ${SSH_PORT} -o BatchMode=yes -o ConnectTimeout=10 -o SendEnv=none -i ${SSH_KEY}"
TS="$(date +%Y%m%d-%H%M%S)"

echo "========================================================"
echo "🚀 Starting Production Deploy to ${SSH_HOST} (${TS})"
echo "========================================================"

# (a) Check git status clean (ignoring generated backend/public/build output)
cd "$ROOT_DIR"
if [[ "$ALLOW_DIRTY" != "--allow-dirty" ]] && [[ -n "$(git status --porcelain -- ':!backend/public/build')" ]]; then
  echo "❌ Git working tree is not clean! Commit your changes first (or pass --allow-dirty)."
  git status -s -- ':!backend/public/build'
  exit 1
fi
echo "✅ Step (a): Git status verified."

# (b) Build frontend (Vue 3 + Vite -> backend/public/build)
echo "📦 Step (b): Building Vue 3 frontend (npm ci && npm run build)..."
export PATH="$HOME/.nvm/versions/node/v24.19.0/bin:/usr/local/bin:/opt/homebrew/bin:$PATH"
cd "$ROOT_DIR/frontend"
npm ci --prefer-offline --no-audit
npm run build
rm -f "$ROOT_DIR/backend/public/build/.DS_Store"
cd "$ROOT_DIR"
echo "✅ Step (b): Frontend built into backend/public/build."

# (c) Pre-deploy Automatic Backup (DB + Server Files)
echo "💾 Step (c): Taking automatic pre-deploy backup (DB + Server Files) into /backups..."
mkdir -p "$BACKUP_DIR"
DB_DUMP="$BACKUP_DIR/prod-db-${TS}.sql"
FILES_BAK="$BACKUP_DIR/prod-files-${TS}.tar.gz"

$SSH_CMD "${SSH_USER}@${SSH_HOST}" "
export LC_ALL=C
cd ${REMOTE_CORE}
${REMOTE_PHP} -r '
require \"vendor/autoload.php\";
\$app = require_once \"bootstrap/app.php\";
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
\$c = config(\"database.connections.mysql\");
putenv(\"MYSQL_PWD=\" . \$c[\"password\"]);
\$cmd = sprintf(
    \"mysqldump --single-transaction --routines --triggers --no-tablespaces -h %s -P %s -u %s %s\",
    escapeshellarg(\$c[\"host\"]),
    escapeshellarg(\$c[\"port\"]),
    escapeshellarg(\$c[\"username\"]),
    escapeshellarg(\$c[\"database\"])
);
passthru(\$cmd, \$ret);
exit(\$ret);
'
" > "$DB_DUMP"
gzip -k -f "$DB_DUMP"

$SSH_CMD "${SSH_USER}@${SSH_HOST}" \
  "LC_ALL=C tar -czf - -C ${REMOTE_WEB_ROOT} \
    --exclude='SarwarCo/vendor' \
    --exclude='SarwarCo/storage/logs/*' \
    --exclude='__MACOSX' \
    --exclude='.DS_Store' \
    SarwarCo index.php .htaccess build logo.png favicon.ico" > "$FILES_BAK"

echo "✅ Step (c): Backups saved:"
echo "   - DB:    ${DB_DUMP}.gz"
echo "   - Files: ${FILES_BAK}"

# Check if composer.json or composer.lock changed compared to server
COMPOSER_CHANGED=0
if [[ -n "$(rsync -rcni --dry-run -e "$SSH_CMD" "$ROOT_DIR/backend/composer.json" "$ROOT_DIR/backend/composer.lock" "${SSH_USER}@${SSH_HOST}:${REMOTE_CORE}/" | grep '^>f' || true)" ]]; then
  COMPOSER_CHANGED=1
fi

# (d) Upload ONLY changed files (NEVER touching .env, storage/, .htaccess)
echo "📤 Step (d): Uploading changed files via rsync (no --delete on protected paths)..."
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

rsync -rcz -i \
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
  "${SSH_USER}@${SSH_HOST}:${REMOTE_CORE}/" | grep -v '^\.f\.\.T\.\.\.\.' || true

rsync -rcz -i \
  "${COMMON_EXCLUDES[@]}" \
  -e "$SSH_CMD" \
  "$ROOT_DIR/backend/public/build/" \
  "${SSH_USER}@${SSH_HOST}:${REMOTE_WEB_ROOT}/build/" | grep -v '^\.f\.\.T\.\.\.\.' || true

# Ensure /img/logo_green.png and /logo.png exist at web root too
if [[ -d "$ROOT_DIR/frontend/public/img" ]]; then
  rsync -rcz --exclude='.DS_Store' -e "$SSH_CMD" "$ROOT_DIR/frontend/public/img/" "${SSH_USER}@${SSH_HOST}:${REMOTE_WEB_ROOT}/img/"
fi

echo "✅ Step (d): Files uploaded safely."

# (e, f, g) Remote Composer (if changed), Migrations, Caches, Permissions
echo "⚙️  Step (e/f/g): Running remote PHP 8.3 tasks (migrations, caches, permissions)..."
$SSH_CMD "${SSH_USER}@${SSH_HOST}" "
export LC_ALL=C
set -e
cd ${REMOTE_CORE}

if [[ '${COMPOSER_CHANGED}' == '1' ]]; then
  echo '  -> composer.json/lock changed: running composer install --no-dev --optimize-autoloader...'
  ${REMOTE_PHP} /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction
fi

echo '  -> Running php artisan migrate --force...'
${REMOTE_PHP} artisan migrate --force

echo '  -> Clearing and rebuilding caches...'
${REMOTE_PHP} artisan optimize:clear
${REMOTE_PHP} artisan config:cache
${REMOTE_PHP} artisan route:cache
${REMOTE_PHP} artisan view:cache

echo '  -> Fixing permissions on storage and bootstrap/cache...'
chmod -R 775 storage bootstrap/cache
chmod 644 ${REMOTE_WEB_ROOT}/.htaccess ${REMOTE_CORE}/.htaccess
"

# (h) Verify Security (403) and Live Site (200)
echo "🛡️  Step (h): Post-Deploy Security & Health Verification..."
ENV_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/SarwarCo/.env")"
LOG_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/SarwarCo/storage/logs/laravel.log")"
HOME_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/")"
UP_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" "https://sarwary-mukiryan.com/up")"
API_CODE="$(/usr/bin/curl -k -s -o /dev/null -w "%{http_code}" -H "Accept: application/json" "https://sarwary-mukiryan.com/api/user")"

echo "   - Security /SarwarCo/.env        -> HTTP ${ENV_CODE} (Expected: 403)"
echo "   - Security /SarwarCo/.../log     -> HTTP ${LOG_CODE} (Expected: 403)"
echo "   - Homepage /                     -> HTTP ${HOME_CODE} (Expected: 200)"
echo "   - Healthcheck /up                -> HTTP ${UP_CODE} (Expected: 200)"
echo "   - API /api/user                  -> HTTP ${API_CODE} (Expected: 401)"

if [[ "$ENV_CODE" != "403" && "$ENV_CODE" != "404" ]]; then
  echo "🚨 CRITICAL ERROR: /SarwarCo/.env returned HTTP ${ENV_CODE}!"
  exit 1
fi

if [[ "$HOME_CODE" != "200" || "$UP_CODE" != "200" ]]; then
  echo "🚨 ERROR: Site health check failed (Homepage: ${HOME_CODE}, /up: ${UP_CODE}). Run ./scripts/rollback.sh to restore."
  exit 1
fi

echo "========================================================"
echo "🎉 DEPLOY SUCCESSFUL! Site is live and secured."
echo "========================================================"
