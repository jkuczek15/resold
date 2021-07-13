#!/usr/bin/env bash
# Script must be ran as root user or using sudo

# Set custom bash profile
mv /var/www/html/app/code/etc/prod_bashrc ~/.bashrc
mv /var/www/html/app/code/etc/prod_bashrc /home/ec2-user/.bashrc
source ~/.bashrc

# Install latest PHP version
chmod +x /var/www/html/scripts/upgrade_php.sh
source /var/www/html/scripts/upgrade_php.sh || true

# Grant permissions to the php session folder
chmod -R 777 /var/lib/php/7.1/session/

# Grant permissions to all web files
chmod -R 674 /var/www/html

# Grant write permissions to cache files
chmod -R 677 /var/www/html/var

# Remove execute permissions from all files
chmod -R -x+X * /var/www/html

# Grant write permissions to cache files
chmod -R 777 /var/www/html/var
chmod -R 777 /var/www/html/pub
chmod -R u+w /var/www/html/var /var/www/html/vendor /var/www/html/app/etc /var/www/html/pub/media

# Grant all permissions to potato compressor cache folders
chmod -R 777 /var/www/html/var/cache /var/www/html/var/tmp /var/www/html/var/page_cache /var/www/html/pub/static

# Make the Magento command line tool executable
chmod u+x /var/www/html/bin/magento

# Solve conflicts between epel and ami packages
yum-config-manager --disable epel
yum clean all
yum update -y
yum-config-manager --enable epel

# Install gcc and C++ 
yum -y install gcc72 gcc72-c++

# Install PECL and gRPC extension 
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
yum -y install php71-devel php7-pear zlib-devel
pecl7 install grpc
pecl7 install protobuf
cp /var/www/html/app/etc/prod_php.ini /etc/php.ini

# Update and Install Magento dependencies using composer
/bin/composer.phar install -d /var/www/html

# Copy merge vendor override Filesystem
rsync -a /var/www/html/vendor/resold/* /var/www/html/vendor/

# Enable Prod environment settings
php /var/www/html/scripts/enable_env.php

# Install Magento and apply configuration for AWS cloud server
/var/www/html/bin/magento setup:install \
  --backend-frontname="<your custom admin uri>"  \
  --db-host="<production db host>" \
  --db-name="MagentoQuickstartDB" \
  --db-user="<your db user>" \
  --db-password="<production password>" \
  --admin-firstname="<your first name>" \
  --admin-lastname="<your last name>" \
  --admin-email="<your email>" \
  --admin-user="<your username>" \
  --admin-password="<your password>" \
  --base-url="http://<your custom domain>" \
  --base-url-secure="https://<your custom domain>" \
  --use-secure="1" \
  --use-secure-admin="1" \
  --use-rewrites="1" \
  --language="en_US" \
  --currency="USD" \
  --timezone="America/Chicago"

# Grant permissions to all web files
chmod -R 674 /var/www/html

# Remove execute permissions from all files
chmod -R -x+X * /var/www/html

# Grant write permissions to cache files
chmod -R 777 /var/www/html/var

# Make the Magento command line tool executable
chmod u+x /var/www/html/bin/magento

# Write permissions to pub/media folder
chmod -R 777 /var/www/html/pub/media/

# Upgrade Magento module schema
/var/www/html/bin/magento setup:upgrade

# Deploy Magento static content
cp /var/www/html/app/etc/prod_php_no_ext.ini /etc/php.ini
/var/www/html/bin/magento setup:static-content:deploy

# Grant execute permissions to all scripts
chmod +x /var/www/html/scripts/*

# Enable production mode
/var/www/html/bin/magento deploy:mode:set production
cp /var/www/html/app/etc/prod_php.ini /etc/php.ini

# Create the potato compressor image cache folders
rm -rf /var/www/html/pub/static/_po_compressor
mkdir /var/www/html/pub/static/_po_compressor
mkdir /var/www/html/pub/static/_po_compressor/po_cmp_image_merge

# Grant permissions to all web files
chmod -R 674 /var/www/html

# Remove execute permissions from all files
chmod -R -x+X * /var/www/html

# Grant write permissions to cache files
chmod -R 777 /var/www/html/var
chmod -R 777 /var/www/html/pub
chmod -R u+w /var/www/html/var /var/www/html/vendor /var/www/html/app/etc /var/www/html/pub/media

# Grant all permissions to potato compressor cache folders
chmod -R 777 /var/www/html/var/cache /var/www/html/var/tmp /var/www/html/var/page_cache /var/www/html/pub/static

# Make the Magento command line tool executable
chmod u+x /var/www/html/bin/magento

# Grant execute permissions to all scripts
chmod +x /var/www/html/scripts/*

# Grant all permissions to the tmp directory and catalog
chmod -R 777 /var/www/html/pub/media/tmp /var/www/html/pub/media/catalog

# Disable maintenance mode
/var/www/html/bin/magento maintenance:disable

# Cache bust CDN
/var/www/html/bin/magento absolute:cache-bust:all

# Grant permissions to the media folder
chmod -R 777 /var/www/html/pub/media

# Create aws credentials directory
mkdir /var/lib/nginx/.aws

# Copy AWS credentials
cp /var/www/html/credentials /var/lib/nginx/.aws/credentials

# Copy prod php.ini
cp /var/www/html/app/etc/prod_php.ini /etc/php.ini

# Grant permissions to the php session folder
chmod -R 777 /var/lib/php/7.1/session/

# Restart the PHP service
service php-fpm restart

# Restart nginx
nginx -s reload
