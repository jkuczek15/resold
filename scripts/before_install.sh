#!/usr/bin/env bash

# Unmount the pub/media folder prior to removal
if [ -d "/var/www/html/pub/media" ]; then
  umount /var/www/html/pub/media || true
fi

# Remove all web files
rm -rf /var/www/html/* || true
rm -rf /var/www/html/.* || true

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
yum -y install php70-devel php7-pear zlib-devel
pecl7 install grpc > /dev/null 2> /dev/null < /dev/null &
pecl7 install protobuf > /dev/null 2> /dev/null < /dev/null &