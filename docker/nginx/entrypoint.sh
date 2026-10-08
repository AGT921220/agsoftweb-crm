#!/bin/sh
set -e

if [ ! -f /etc/nginx/certs/localhost.crt ] || [ ! -f /etc/nginx/certs/localhost.key ]; then
    apk add --no-cache openssl
    openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
        -keyout /etc/nginx/certs/localhost.key \
        -out /etc/nginx/certs/localhost.crt \
        -subj "/CN=localhost" \
        -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"
fi

exec /docker-entrypoint.sh "$@"
