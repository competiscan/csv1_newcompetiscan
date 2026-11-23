#!/bin/bash
set -e

# Usage:
# Local:  ./APPLY_ALL_PHP_COMPATIBILITY_FIXES.sh
# Server: ./APPLY_ALL_PHP_COMPATIBILITY_FIXES.sh https://your-domain.example/csv1_newcompetiscan

ROOT="$(cd "$(dirname "$0")" && pwd)"
SITE_BASE_URL="${1:-http://localhost/csv1_newcompetiscan}"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$ROOT/php85-compat-backup-$STAMP"
mkdir -p "$BACKUP_DIR"

backup_file() {
    rel="$1"
    src="$ROOT/$rel"
    if [ -f "$src" ]; then
        mkdir -p "$BACKUP_DIR/$(dirname "$rel")"
        cp -p "$src" "$BACKUP_DIR/$rel"
    fi
}

cd "$ROOT"

# 1. PHP 8.5 compatibility for legacy Pear Auth library.
backup_file auth_config.php
python3 - <<'PY'
from pathlib import Path
p = Path('auth_config.php')
s = p.read_text()
marker = "if (!function_exists('get_magic_quotes_gpc')) {"
if marker not in s:
    pos = s.find('<?php')
    polyfill = "<?php\n// PHP 8.5 compatibility: removed get_magic_quotes_gpc().\n" + \
        "if (!function_exists('get_magic_quotes_gpc')) {\n" + \
        "    function get_magic_quotes_gpc() {\n" + \
        "        return 0;\n" + \
        "    }\n" + \
        "}\n"
    s = polyfill + s[pos + 5:].lstrip('\n')
    p.write_text(s)
PY

# 2. AWS SDK credential providers must be positional on PHP 8.
backup_file aws-api/aws-vendor/aws/aws-sdk-php/src/Credentials/CredentialProvider.php
python3 - <<'PY'
from pathlib import Path
p = Path('aws-api/aws-vendor/aws/aws-sdk-php/src/Credentials/CredentialProvider.php')
s = p.read_text()
s = s.replace(
    '                array_merge($localCredentialProviders, $remoteCredentialProviders)\n',
    '                array_values(array_merge($localCredentialProviders, $remoteCredentialProviders))\n'
)
p.write_text(s)
PY

# 3. Visitor counter guest ID must be numeric for strict integer columns.
backup_file header_top_test.php
python3 - <<'PY'
from pathlib import Path
p = Path('header_top_test.php')
if p.exists():
    s = p.read_text().replace("    $loggin_user_id=''; \n", "    $loggin_user_id=0;\n")
    p.write_text(s)
PY

# 4. Optional root Composer autoloader guards.
backup_file includes/functions.php
python3 - <<'PY'
from pathlib import Path
p = Path('includes/functions.php')
s = p.read_text()
old = "require_once __DIR__ . '/../vendor/autoload.php';"
new = "$composer_autoload = __DIR__ . '/../vendor/autoload.php';\nif (is_file($composer_autoload)) {\n    require_once $composer_autoload;\n}"
if old in s:
    s = s.replace(old, new)
    p.write_text(s)
PY

backup_file includes/globalSession.php
python3 - <<'PY'
from pathlib import Path
p = Path('includes/globalSession.php')
if p.exists():
    s = p.read_text()
    direct = "require_once('vendor/autoload.php');"
    guarded = "$composer_autoload = __DIR__ . '/../vendor/autoload.php';\nif (is_file($composer_autoload)) {\n\trequire_once($composer_autoload);\n}"
    if direct in s:
        s = s.replace(direct, guarded)
        p.write_text(s)
PY

# 5. Temporary root Composer fallback; Composer packages are normally installed later.
mkdir -p vendor
cat > vendor/autoload.php <<'PHP'
<?php
// Temporary fallback created by APPLY_ALL_PHP_COMPATIBILITY_FIXES.sh.
PHP

# 6. ACF PSR-4 fallback for incomplete plugin packages.
ACF_DIR="$ROOT/wordpress/wp-content/plugins/advanced-custom-fields-pro"
if [ -d "$ACF_DIR" ] || [ -d "$ACF_DIR.disabled" ]; then
    if [ -d "$ACF_DIR.disabled" ]; then mv "$ACF_DIR.disabled" "$ACF_DIR"; fi
    mkdir -p "$ACF_DIR/vendor"
    cat > "$ACF_DIR/vendor/autoload.php" <<'PHP'
<?php
spl_autoload_register(function ($class) {
    $prefix = 'ACF\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    $file = dirname(__DIR__) . '/src/' . $relative;
    if (is_file($file)) {
        require $file;
    }
});
PHP
fi

# 7. WordPress local/server URLs.
backup_file wordpress/wp-config.php
python3 - "$SITE_BASE_URL" <<'PY'
from pathlib import Path
import sys
p = Path('wordpress/wp-config.php')
base = sys.argv[1].rstrip('/')
s = p.read_text()
lines = [
    "define('WP_HOME',    '%s');" % base,
    "define('WP_SITEURL', '%s/wordpress');" % base,
]
import re
s = re.sub(r"define\s*\(\s*['\"]WP_HOME['\"]\s*,.*?\);", lines[0], s)
s = re.sub(r"define\s*\(\s*['\"]WP_SITEURL['\"]\s*,.*?\);", lines[1], s)
p.write_text(s)
PY

# 8. Syntax and smoke checks.
php -l auth_config.php
php -l includes/functions.php
php -l includes/globalSession.php
php -l header_top_test.php
php -l aws-api/aws-vendor/aws/aws-sdk-php/src/Credentials/CredentialProvider.php
php -l wordpress/wp-config.php
php -l wordpress/wp-content/plugins/advanced-custom-fields-pro/acf.php 2>/dev/null || true

printf '\nCompatibility fixes applied.\n'
printf 'Backup directory: %s\n' "$BACKUP_DIR"
printf 'Site base URL: %s\n' "$SITE_BASE_URL"
