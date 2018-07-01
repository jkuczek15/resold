#!/usr/bin/env bash

# Install base Magento dependencies
php /bin/composer.phar install

# Apply base magento configuration
magento setup:config:set /
--backend-frontname="stm"  /
--session-save="files" /
--db-host="mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com" /
--db-name="MagentoQuickstartDB" /
--db-user="admin" /
--db-password="Rootroot$"

# Enable Magento modules
magento module:enable --all

# Compile Magento class files and inject dependencies
magento setup:di:compile