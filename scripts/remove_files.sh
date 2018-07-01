#!/usr/bin/env bash

if [ -d "/var/www/html/pub/media" ]; then
  umount /var/www/html/pub/media || true
fi

rm -rf /var/www/html/* || true
rm -rf /var/www/html/.* || true