#!/usr/bin/env bash

# Install base Magento dependencies
php /bin/composer.phar install -d /var/www/html

# Grant read/write/execute permissions to all web files
chmod -R 777 /var/www/html
chmod +x /var/www/html/bin/magento

# Apply Magento configuration for AWS cloud server
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
--admin-password="Rootroot$" \
--base-url="https://resold.us" \
--use-secure-admin="1" \
--base-url-secure="1" \
--language="en_US" \
--currency="USD" \
--timezone="America/Chicago"

# Enable Magento modules
/var/www/html/bin/magento module:enable --all

# Upgrade Magento schema and modules
/var/www/html/bin/magento setup:upgrade

# Compile Magento class files and inject dependencies
/var/www/html/bin/magento setup:di:compile

# Grant read/write/execute permissions to extra folders
chmod -R 777 /var/www/html/var/
chmod -R 777 /var/www/html/app/etc/config.php /var/www/html/app/etc/env.php

# Generate the Magento static content files
/var/www/html/bin/magento setup:static-content:deploy