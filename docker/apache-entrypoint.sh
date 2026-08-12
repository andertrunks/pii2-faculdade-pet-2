#!/bin/sh
set -eu

port="${PORT:-8080}"

case "$port" in
  ''|*[!0-9]*)
    echo "PORT deve ser um número inteiro." >&2
    exit 1
    ;;
esac

sed -ri "s/^Listen [0-9]+/Listen ${port}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
  attempt=1
  while ! php /opt/adota-pet/scripts/migrate.php; do
    if [ "$attempt" -ge 30 ]; then
      echo "O banco não ficou disponível para migração." >&2
      exit 1
    fi
    attempt=$((attempt + 1))
    sleep 2
  done
fi

exec docker-php-entrypoint "$@"
