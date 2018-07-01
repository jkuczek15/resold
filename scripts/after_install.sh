#!/usr/bin/env bash

# Install base Magento dependencies using composer
php /bin/composer.phar install -d /var/www/html

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html

# Install Magento and apply configuration for AWS cloud server
/var/www/html/bin/magento setup:install \
--backend-frontname="stm"  \
--session-save="files" \
--db-host="mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com" \
--db-name="MagentoQuickstartDB" \
--db-user="admin" \
--db-password="Rootroot$" \
--admin-firstname="Joe" \
--admin-lastname="Kuczek" \
--admin-email="joe.kuczek@gmail.com" \
--admin-user="joe" \
--admin-password="Bigjoe3092$" \
--base-url="https://resold.us" \
--base-url-secure="https://resold.us" \
--use-secure="1" \
--use-secure-admin="1" \
--use-rewrites="1" \
--language="en_US" \
--currency="USD" \
--timezone="America/Chicago"

# Remove cache folders
rm -rf /var/www/html/pub/static
rm -rf /var/www/html/pub/media
rm -rf /var/www/html/var

# Create new folders for static resources
mkdir /var/www/html/pub/static
mkdir /var/www/html/pub/media

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html

# Enable all modules
/var/www/html/bin/magento module:enable --all --clear-static-content

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html

# Upgrade Magento module schema
/var/www/html/bin/magento setup:upgrade || true

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html

# Compile Magento class files and inject dependencies
/var/www/html/bin/magento setup:di:compile || true

# Deploy Magento static content
/var/www/html/bin/magento setup:static-content:deploy || true

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html
