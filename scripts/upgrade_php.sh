#!/usr/bin/env bash
# Upgrade an Amazon Linux EC2 to PHP 7.1
#
# Last tested w/ PHP 7.1 AWS Linux version 2.8.5
#
# Must be ran as sudo:
#     sudo bash upgrade-php7.sh
#
# Can be added to ./.ebextensions/20_php.config like so:
#  container_commands:
#    20_php7_upgrade:
#      command: sudo bash scripts/upgrade-php7.sh

# We will remove instances of PHP matching:
remove=( php56 php70 php72 php73 php74 )

# Prep dependencies.
yum install --nogpgcheck -y https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
yum install --nogpgcheck -y http://rpms.remirepo.net/enterprise/remi-release-7.rpm
yum install --nogpgcheck -y https://rpmfind.net/linux/centos/7.5.1804/os/x86_64/Packages/scl-utils-20130529-18.el7_4.x86_64.rpm
yum install --nogpgcheck -y https://rpmfind.net/linux/centos/7.5.1804/os/x86_64/Packages/libedit-3.0-12.20121213cvs.el7.x86_64.rpm

set -e
yum-config-manager --enable epel
yum-config-manager --enable remi-php71

# Clean previous versions of PHP for determinism!
set +e
if [[ ! -z "$remove" ]]
  then
  for ver in "${remove[@]}"
  do
    modules=$( rpm -qa --queryformat "%{name}\n" | grep "$ver" )
    if [[ ! -z "$modules" ]]
    then
      echo "Removing $ver"
      for module in "${modules[@]}"
      do
        yum remove $module -y
      done
    fi
  done
fi

# Install PHP 7.1 - Uncommon needs commented out. Uncomment what you need and add dependencies as needed.
set -e
yum install --nogpgcheck -y php71 php71-mcrypt php71-curl php71-intl php71-zip php71-gd php71-mysql php71-dom php71-cli php71-json php71-common php71-mbstring php71-opcache php71-readline php71-bcmath php71-xml php71-soap php71-mysqlnd

# Not yet supportable by this version of Amazon Linux (without significant risk)
# yum install --nogpgcheck -y php71-php-pecl-ssh2
yum install --nogpgcheck -y php71-gd
# yum install --nogpgcheck -y php71-php-pecl-imagick
# yum install --nogpgcheck -y php71-php-pecl-imagick-devel

# yum install --nogpgcheck -y php71-php-ast
yum install --nogpgcheck -y php71-php-bcmath
# yum install --nogpgcheck -y php71-php-brotli
yum install --nogpgcheck -y php71-php-cli
yum install --nogpgcheck -y php71-php-common
# yum install --nogpgcheck -y php71-php-componere
# yum install --nogpgcheck -y php71-php-dba
# yum install --nogpgcheck -y php71-php-dbg
yum install --nogpgcheck -y php71-php-devel
# yum install --nogpgcheck -y php71-php-embedded
yum install --nogpgcheck -y php71-php-enchant
# yum install --nogpgcheck -y php71-php-fpm
# Gd will require you to build libpng
# yum install --nogpgcheck -y php71-php-geos
# yum install --nogpgcheck -y php71-php-gmp
# yum install --nogpgcheck -y php71-php-horde-horde-lz4
yum install --nogpgcheck -y php71-php-imap
# yum install --nogpgcheck -y php71-php-interbase
yum install --nogpgcheck -y php71-php-intl
# yum install --nogpgcheck -y php71-php-ioncube-loader
yum install --nogpgcheck -y php71-php-json
# yum install --nogpgcheck -y php71-php-ldap
# yum install --nogpgcheck -y php71-php-libvirt
# yum install --nogpgcheck -y php71-php-libvirt-doc
# yum install --nogpgcheck -y php71-php-litespeed
# yum install --nogpgcheck -y php71-php-lz4
# yum install --nogpgcheck -y php71-php-maxminddb
yum install --nogpgcheck -y php71-php-mbstring
yum install --nogpgcheck -y php71-php-mysqlnd
# yum install --nogpgcheck -y php71-php-oci8
yum install --nogpgcheck -y php71-php-odbc
yum install --nogpgcheck -y php71-php-opcache
yum install --nogpgcheck -y php71-php-pdo
# yum install --nogpgcheck -y php71-php-pdo-dblib
yum install --nogpgcheck -y php71-php-pear
# yum install --nogpgcheck -y php71-php-pecl-ahocorasick
# yum install --nogpgcheck -y php71-php-pecl-amqp
yum install --nogpgcheck -y php71-php-pecl-apcu
yum install --nogpgcheck -y php71-php-pecl-apcu-bc
# yum install --nogpgcheck -y php71-php-pecl-apcu-devel
# yum install --nogpgcheck -y php71-php-pecl-apfd
# yum install --nogpgcheck -y php71-php-pecl-bitset
# yum install --nogpgcheck -y php71-php-pecl-cassandra
# yum install --nogpgcheck -y php71-php-pecl-cmark
# yum install --nogpgcheck -y php71-php-pecl-couchbase2
# yum install --nogpgcheck -y php71-php-pecl-crypto
# yum install --nogpgcheck -y php71-php-pecl-dbase
# yum install --nogpgcheck -y php71-php-pecl-decimal
# yum install --nogpgcheck -y php71-php-pecl-dio
# yum install --nogpgcheck -y php71-php-pecl-druid
# yum install --nogpgcheck -y php71-php-pecl-ds
# yum install --nogpgcheck -y php71-php-pecl-eio
# yum install --nogpgcheck -y php71-php-pecl-env
# yum install --nogpgcheck -y php71-php-pecl-event
# yum install --nogpgcheck -y php71-php-pecl-fann
# yum install --nogpgcheck -y php71-php-pecl-gearman
# yum install --nogpgcheck -y php71-php-pecl-gender
# yum install --nogpgcheck -y php71-php-pecl-geoip
# yum install --nogpgcheck -y php71-php-pecl-geospatial
# yum install --nogpgcheck -y php71-php-pecl-gmagick
# yum install --nogpgcheck -y php71-php-pecl-gnupg
# yum install --nogpgcheck -y php71-php-pecl-grpc
# yum install --nogpgcheck -y php71-php-pecl-handlebars
# yum install --nogpgcheck -y php71-php-pecl-hdr-histogram
# yum install --nogpgcheck -y php71-php-pecl-hprose
# yum install --nogpgcheck -y php71-php-pecl-hrtime
# yum install --nogpgcheck -y php71-php-pecl-http
# yum install --nogpgcheck -y php71-php-pecl-http-devel
yum install --nogpgcheck -y php71-php-pecl-igbinary
# yum install --nogpgcheck -y php71-php-pecl-igbinary-devel
# yum install --nogpgcheck -y php71-php-pecl-inotify
# yum install --nogpgcheck -y php71-php-pecl-ip2location
# yum install --nogpgcheck -y php71-php-pecl-json-post
# yum install --nogpgcheck -y php71-php-pecl-krb5
# yum install --nogpgcheck -y php71-php-pecl-krb5-devel
# yum install --nogpgcheck -y php71-php-pecl-leveldb
# yum install --nogpgcheck -y php71-php-pecl-lua
# yum install --nogpgcheck -y php71-php-pecl-luasandbox
# yum install --nogpgcheck -y php71-php-pecl-lzf
yum install --nogpgcheck -y php71-mcrypt
# yum install --nogpgcheck -y php71-php-pecl-memcache
# yum install --nogpgcheck -y php71-php-pecl-memcached
# yum install --nogpgcheck -y php71-php-pecl-memprof
# yum install --nogpgcheck -y php71-php-pecl-mogilefs
# yum install --nogpgcheck -y php71-php-pecl-molten
# yum install --nogpgcheck -y php71-php-pecl-mongodb
# yum install --nogpgcheck -y php71-php-pecl-mosquitto
# yum install --nogpgcheck -y php71-php-pecl-msgpack
# yum install --nogpgcheck -y php71-php-pecl-msgpack-devel
# yum install --nogpgcheck -y php71-php-pecl-mustache
# yum install --nogpgcheck -y php71-php-pecl-mysql
# yum install --nogpgcheck -y php71-php-pecl-mysql-xdevapi
# yum install --nogpgcheck -y php71-php-pecl-nsq
yum install --nogpgcheck -y php71-php-pecl-oauth
# yum install --nogpgcheck -y php71-php-pecl-oci8
# yum install --nogpgcheck -y php71-php-pecl-opencensus
# yum install --nogpgcheck -y php71-php-pecl-parle
# yum install --nogpgcheck -y php71-php-pecl-pdflib
# yum install --nogpgcheck -y php71-php-pecl-pq
# yum install --nogpgcheck -y php71-php-pecl-propro
# yum install --nogpgcheck -y php71-php-pecl-propro-devel
# yum install --nogpgcheck -y php71-php-pecl-psr
# yum install --nogpgcheck -y php71-php-pecl-psr-devel
# yum install --nogpgcheck -y php71-php-pecl-radius
# yum install --nogpgcheck -y php71-php-pecl-raphf
# yum install --nogpgcheck -y php71-php-pecl-raphf-devel
# yum install --nogpgcheck -y php71-php-pecl-rar
# yum install --nogpgcheck -y php71-php-pecl-rdkafka
# yum install --nogpgcheck -y php71-php-pecl-redis4
# yum install --nogpgcheck -y php71-php-pecl-request
# yum install --nogpgcheck -y php71-php-pecl-rpminfo
# yum install --nogpgcheck -y php71-php-pecl-rrd
# yum install --nogpgcheck -y php71-php-pecl-scrypt
# yum install --nogpgcheck -y php71-php-pecl-seaslog
# yum install --nogpgcheck -y php71-php-pecl-selinux
# yum install --nogpgcheck -y php71-php-pecl-solr2
# yum install --nogpgcheck -y php71-php-pecl-sphinx
# yum install --nogpgcheck -y php71-php-pecl-ssdeep
# yum install --nogpgcheck -y php71-php-pecl-stats
# yum install --nogpgcheck -y php71-php-pecl-stomp
# yum install --nogpgcheck -y php71-php-pecl-svm
# yum install --nogpgcheck -y php71-php-pecl-swoole4
# yum install --nogpgcheck -y php71-php-pecl-sync
# yum install --nogpgcheck -y php71-php-pecl-taint
# yum install --nogpgcheck -y php71-php-pecl-tcpwrap
# yum install --nogpgcheck -y php71-php-pecl-termbox
# yum install --nogpgcheck -y php71-php-pecl-timecop
# yum install --nogpgcheck -y php71-php-pecl-trace
# yum install --nogpgcheck -y php71-php-pecl-trader
# yum install --nogpgcheck -y php71-php-pecl-uploadprogress
yum install --nogpgcheck -y php71-php-pecl-uuid
# yum install --nogpgcheck -y php71-php-pecl-uv
# yum install --nogpgcheck -y php71-php-pecl-varnish
# yum install --nogpgcheck -y php71-php-pecl-vips
# yum install --nogpgcheck -y php71-php-pecl-vld
# yum install --nogpgcheck -y php71-php-pecl-xattr
# yum install --nogpgcheck -y php71-php-pecl-xdebug
# yum install --nogpgcheck -y php71-php-pecl-xdiff
# yum install --nogpgcheck -y php71-php-pecl-xlswriter
# yum install --nogpgcheck -y php71-php-pecl-xmldiff
# yum install --nogpgcheck -y php71-php-pecl-xmldiff-devel
# yum install --nogpgcheck -y php71-php-pecl-xxtea
# yum install --nogpgcheck -y php71-php-pecl-yac
# yum install --nogpgcheck -y php71-php-pecl-yaconf
# yum install --nogpgcheck -y php71-php-pecl-yaconf-devel
# yum install --nogpgcheck -y php71-php-pecl-yaf
# yum install --nogpgcheck -y php71-php-pecl-yaml
# yum install --nogpgcheck -y php71-php-pecl-yar
# yum install --nogpgcheck -y php71-php-pecl-yaz
# yum install --nogpgcheck -y php71-php-pecl-zip
# yum install --nogpgcheck -y php71-php-pecl-zmq
# yum install --nogpgcheck -y php71-php-pggi
# yum install --nogpgcheck -y php71-php-pgsql
# yum install --nogpgcheck -y php71-php-phalcon3
yum install --nogpgcheck -y php71-php-phpiredis
# yum install --nogpgcheck -y php71-php-pinba
yum install --nogpgcheck -y php71-php-process
# yum install --nogpgcheck -y php71-php-pspell
# yum install --nogpgcheck -y php71-php-recode
# yum install --nogpgcheck -y php71-php-smbclient
# yum install --nogpgcheck -y php71-php-snappy
# yum install --nogpgcheck -y php71-php-snmp
# yum install --nogpgcheck -y php71-php-snuffleupagus
yum install --nogpgcheck -y php71-php-soap
# yum install --nogpgcheck -y php71-php-sodium
# yum install --nogpgcheck -y php71-php-sqlsrv
# yum install --nogpgcheck -y php71-php-tidy
# yum install --nogpgcheck -y php71-php-wkhtmltox
yum install --nogpgcheck -y php71-php-xml
yum install --nogpgcheck -y php71-php-xmlrpc
# yum install --nogpgcheck -y php71-php-zephir-parser
# yum install --nogpgcheck -y php71-php-zephir-parser-devel
# yum install --nogpgcheck -y php71-php-zstd

# Link to bash
scl enable php71 bash

# Restart Apache.
service httpd restart