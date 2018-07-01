#!/usr/bin/env bash

# Apply Magento configuration for AWS cloud server
/var/www/html/bin/magento setup:config:set \
--backend-frontname="stm"  \
--session-save="files" \
--db-host="mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com" \
--db-name="MagentoQuickstartDB" \
--db-user="admin" \
--db-password="Rootroot$"

# Enable Magento modules
/var/www/html/bin/magento module:enable --all

# Compile Magento class files and inject dependencies
/var/www/html/bin/magento setup:di:compile