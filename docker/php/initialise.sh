#!/usr/bin/env bash

echo "Configuring instance"

mkdir -p -m 777 /var/www/html/application/cache
mkdir -p -m 777 /var/www/html/public/uploads

cp application/configs/config.ini application/configs/config.local.ini
cat application/configs/application.ini | sed -e 's/^\[docker \: default\]$//g' > application/configs/application.local.ini

cat >> application/configs/config.local.ini << EOF

[docker : production]

map.googleApiKey = ${GOOGLE_MAPS_KEY}

EOF

cat >> application/configs/application.local.ini << EOF

[docker : default]

resources.cachemanager.maincache.backend.name = File
resources.frontController.params.displayExceptions = 1
email.reportingEmail = "developer@localhost"

email.host = ${SMTP_HOST}
email.config.port = ${SMTP_PORT}
email.config.username = ${SMTP_USER}
email.config.password = ${SMTP_PASSWORD}
email.config.auth = plain

resources.db.adapter = ${DB_DRIVER}
resources.db.params.host = ${DB_HOST}
resources.db.params.port = ${DB_PORT}
resources.db.params.username = root
resources.db.params.password = ${DB_PASSWORD}
resources.db.params.dbname = ${DB_NAME}

phpSettings.display_startup_errors = Off
phpSettings.display_errors = Off

EOF

echo "Starting PHP-FPM"
php-fpm
