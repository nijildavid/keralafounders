#!/bin/bash
set -euo pipefail

# Only needed in Claude Code on the web — local machines already have their
# own MySQL/MariaDB + PHP set up per README.md.
if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
  exit 0
fi

PROJECT_DIR="${CLAUDE_PROJECT_DIR:-$(pwd)}"
DB_NAME="keralafounders"
DB_USER="keralafounders"
DB_PASS="keralafounders_dev"
ADMIN_DEV_PASSWORD="kfdev123"

sql() {
  mysql -uroot "$@"
}

# 1. Install MariaDB if this container doesn't have it yet.
if ! command -v mysqld >/dev/null 2>&1 && ! command -v mariadbd >/dev/null 2>&1; then
  apt-get update -qq
  DEBIAN_FRONTEND=noninteractive apt-get install -qq -y mariadb-server >/dev/null
fi

# 2. Start MariaDB if it isn't already running (containers have no init system,
# so the package install alone does not start the service).
if ! mysqladmin ping --silent 2>/dev/null; then
  service mariadb start >/dev/null 2>&1 || mysqld_safe --skip-syslog &
  for _ in $(seq 1 30); do
    mysqladmin ping --silent 2>/dev/null && break
    sleep 1
  done
fi

# 3. Create the app database/user (idempotent).
sql <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

# 4. Load schema.sql only on a fresh database — it contains non-idempotent
# INSERTs (unique slugs) so re-running it against an already-seeded DB errors.
TABLE_COUNT=$(sql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")
if [ "$TABLE_COUNT" -eq 0 ]; then
  sql "$DB_NAME" < "$PROJECT_DIR/schema.sql"
fi

# 5. Generate local-only config files from the committed .example.php templates.
# config/db.php and config/auth.php are gitignored on purpose (see .gitignore) —
# never commit real values, these are throwaway dev credentials for this
# container only.
if [ ! -f "$PROJECT_DIR/config/db.php" ]; then
  sed \
    -e "s/your_db_name/$DB_NAME/" \
    -e "s/your_db_user/$DB_USER/" \
    -e "s/your_db_password/$DB_PASS/" \
    "$PROJECT_DIR/config/db.example.php" > "$PROJECT_DIR/config/db.php"
fi

if [ ! -f "$PROJECT_DIR/config/auth.php" ]; then
  HASH=$(php -r "echo password_hash('$ADMIN_DEV_PASSWORD', PASSWORD_DEFAULT);")
  sed "s#paste-your-generated-hash-here#$HASH#" \
    "$PROJECT_DIR/config/auth.example.php" > "$PROJECT_DIR/config/auth.php"
fi

# 6. Start the PHP dev server in the background if nothing is already listening.
if ! curl -s -o /dev/null "http://127.0.0.1:8000/"; then
  mkdir -p /tmp/keralafounders
  nohup php -S 127.0.0.1:8000 -t "$PROJECT_DIR/public" \
    > /tmp/keralafounders/php-server.log 2>&1 &
  disown
fi

echo "Kerala Founders dev environment ready: http://127.0.0.1:8000/ (admin dev password: $ADMIN_DEV_PASSWORD)"
