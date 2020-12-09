#!/usr/bin/env bash

# Unmount the pub/media folder prior to removal
if [ -d "/var/www/html/pub/media" ]; then
  umount /var/www/html/pub/media || true
fi

# Remove all web files
rm -rf /var/www/html/* || true
rm -rf /var/www/html/.* || true
