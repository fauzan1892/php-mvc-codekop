#!/usr/bin/env bash
set -u

# Run against a real Apache/Nginx vhost, not php -S: php -S does not process
# .htaccess and therefore cannot verify deployment rules.
base_url="${BASE_URL:-http://127.0.0.1}"
base_url="${base_url%/}"
failed=0

protected_paths=(
    "/app/Config/Config.php"
    "/system/Core/App.php"
    "/storage/sessions/.gitkeep"
    "/storage/queue/.gitkeep"
    "/vendor/autoload.php"
    "/composer.json"
    "/composer.lock"
    "/README.md"
    "/.env"
    "/tests/Security/security_smoke.sh"
)

for path in "${protected_paths[@]}"; do
    status="$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' "${base_url}${path}")"
    case "$status" in
        403|404)
            printf 'PASS %s -> %s\n' "$path" "$status"
            ;;
        *)
            printf 'FAIL %s -> %s (expected 403 or 404)\n' "$path" "$status"
            failed=1
            ;;
    esac
done

status="$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' "${base_url}/index.php")"
case "$status" in
    200|301|302|400|404)
        printf 'PASS /index.php -> %s\n' "$status"
        ;;
    *)
        printf 'FAIL /index.php -> %s\n' "$status"
        failed=1
        ;;
esac

exit "$failed"
