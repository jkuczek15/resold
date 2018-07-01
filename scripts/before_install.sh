#!/usr/bin/env bash

# Unmount the pub/media folder prior to removal
if [ -d "/var/www/html/pub/media" ]; then
  umount /var/www/html/pub/media || true
fi

# Remove all relevant web files
rm -rf /var/www/html/app || true
rm -rf /var/www/html/pub || true
rm -rf /var/www/html/var || true