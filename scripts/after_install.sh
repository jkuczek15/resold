#!/usr/bin/env bash

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html

# Update Magento dependencies using composer
composer update -d /var/www/html

# Install Magento dependencies using composer
composer install -d /var/www/html

# Make the Magento cli tool executable
chmod +x /var/www/html/bin/magento

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

# Upgrade Magento module schema
magento setup:upgrade

# Compile Magento class files and inject dependencies
magento setup:di:compile

# Deploy Magento static content
magento setup:static-content:deploy

# Overwrite default vendor files
rsync -a /var/www/html/vendor_override/ /var/www/html/vendor/ || true
