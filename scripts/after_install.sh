#!/usr/bin/env bash

# Switch to the standard user
su ec2-user

# Install base Magento dependencies
php /bin/composer.phar install