echo "====================================="
/var/www/html/scripts/set_all_permissions.sh
/var/www/html/bin/magento cache:flush
echo "====================================="
/var/www/html/bin/magento cache:clean
echo "====================================="
/var/www/html/scripts/set_all_permissions.sh
