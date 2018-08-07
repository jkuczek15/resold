#!/usr/bin/env bash

# Grant permissions to all web files
sudo chmod -R 777 /var/www/html

# Update and Install Magento dependencies using composer
composer update -d /var/www/html

# Make the Magento command line tool executable
sudo chmod +x /var/www/html/bin/magento

# Install Magento and apply configuration for AWS cloud server
/var/www/html/bin/magento setup:install \
--backend-frontname="stm"  \
--session-save="files" \
--db-host="localhost" \
--db-name="MagentoQuickstartDB" \
--db-user="root" \
--db-password="Rootroot$" \
--admin-firstname="Joe" \
--admin-lastname="Kuczek" \
--admin-email="joe.kuczek@gmail.com" \
--admin-user="joe" \
--admin-password="Bigjoe3092$" \
--base-url="https://localhost" \
--base-url-secure="https://localhost" \
--use-secure="1" \
--use-secure-admin="1" \
--use-rewrites="1" \
--language="en_US" \
--currency="USD" \
--timezone="America/Chicago"

# Upgrade Magento module schema
/var/www/html/bin/magento setup:upgrade

# Compile Magento class files and inject dependencies
/var/www/html/bin/magento setup:di:compile

# Deploy Magento static content
/var/www/html/bin/magento setup:static-content:deploy

# Copy merge vendor override Filesystem
rsync -a /var/www/html/vendor/resold/* /var/www/html/vendor/

# Grant permissions to all web files
sudo chmod -R 777 /var/www/html
