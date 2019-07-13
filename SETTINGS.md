##########################################################
##########################################################
##########################################################
# Local Environment Setup
#
# Technology Stack:
#  - Ubuntu Desktop 18.04 (Bionic Beaver)
#  - Apache Version 2.4.29
#  - MySQL Version 5.6
#  - PHP Version 7.0.3
#  - Magento Community Edition 2.1
#  - And much more...
#
# DB Connection Information:
#  - AWS
#     Host: mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com
#     User: admin
#     Pass: HjBu4a7RdxnwBzfKvqAQp5Xp22665T8tP2sLP8m6
#  - Local
#     Host: localhost
#     User: root
#     Pass: Rootroot$
#
# Backend URL: https://server-domain/stm
# support@resold.us
# password: ShootTheMoon$
#
# Things might not work as expected, just bear with it.
##########################################################
##########################################################
##########################################################
Resold Light blue: #41B8EA

##########################################################
# Install Web Server
##########################################################
sudo apt-get update
sudo apt-get install apache2

##########################################################
# Install PHP and Required Extensions
##########################################################
sudo add-apt-repository ppa:ondrej/php
sudo apt-get install php7.0 php7.0-mcrypt php7.0-curl php7.0-intl php7.0-zip php7.0-gd php7.0-mysql php7.0-dom php7.0-cli php7.0-json php7.0-common php7.0-mbstring php7.0-opcache php7.0-readline php7.0-bcmath

##########################################################
# Enable SSL/HTTPS in Apache
##########################################################
sudo a2enmod ssl
sudo service apache2 restart
sudo mkdir /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/apache.key -out /etc/apache2/ssl/apache.crt

# This will open a file for editing
sudo gedit /etc/apache2/sites-available/default-ssl.conf

# Replace file contents with the following text:
##########################
##### Begin Contents #####
##########################
<IfModule mod_ssl.c>
    <VirtualHost _default_:443>
        ServerAdmin <your email>
        ServerName localhost
        ServerAlias www.localhost
        DocumentRoot /var/www/html
        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
        #   SSL Engine Switch:
        #   Enable/Disable SSL for this virtual host.
        SSLEngine on
        #   A self-signed (snakeoil) certificate can be created by installing
        #   the ssl-cert package. See
        #   /usr/share/doc/apache2/README.Debian.gz for more info.
        #   If both key and certificate are stored in the same file, only the
        #   SSLCertificateFile directive is needed.
        SSLCertificateFile  /etc/apache2/ssl/apache.crt
        SSLCertificateKeyFile /etc/apache2/ssl/apache.key
        #   Server Certificate Chain:
        #   Point SSLCertificateChainFile at a file containing the
        #   concatenation of PEM encoded CA certificates which form the
        #   certificate chain for the server certificate. Alternatively
        #   the referenced file can be the same as SSLCertificateFile
        #   when the CA certificates are directly appended to the server
        #   certificate for convinience.
        #SSLCertificateChainFile /etc/apache2/ssl.crt/server-ca.crt
        #   Certificate Authority (CA):
        #   Set the CA certificate verification path where to find CA
        #   certificates for client authentication or alternatively one
        #   huge file containing all of them (file must be PEM encoded)
        #   Note: Inside SSLCACertificatePath you need hash symlinks
        #        to point to the certificate files. Use the provided
        #        Makefile to update the hash symlinks after changes.
        #SSLCACertificatePath /etc/ssl/certs/
        #SSLCACertificateFile /etc/apache2/ssl.crt/ca-bundle.crt
        #   Certificate Revocation Lists (CRL):
        #   Set the CA revocation path where to find CA CRLs for client
        #   authentication or alternatively one huge file containing all
        #   of them (file must be PEM encoded)
        #   Note: Inside SSLCARevocationPath you need hash symlinks
        #        to point to the certificate files. Use the provided
        #        Makefile to update the hash symlinks after changes.
        #SSLCARevocationPath /etc/apache2/ssl.crl/
        #SSLCARevocationFile /etc/apache2/ssl.crl/ca-bundle.crl
        #   Client Authentication (Type):
        #   Client certificate verification type and depth.  Types are
        #   none, optional, require and optional_no_ca.  Depth is a
        #   number which specifies how deeply to verify the certificate
        #   issuer chain before deciding the certificate is not valid.
        #SSLVerifyClient require
        #SSLVerifyDepth  10
        #   SSL Engine Options:
        #   Set various options for the SSL engine.
        #   o FakeBasicAuth:
        #    Translate the client X.509 into a Basic Authorisation.  This means that
        #    the standard Auth/DBMAuth methods can be used for access control.  The
        #    user name is the `one line' version of the client's X.509 certificate.
        #    Note that no password is obtained from the user. Every entry in the user
        #    file needs this password: `xxj31ZMTZzkVA'.
        #   o ExportCertData:
        #    This exports two additional environment variables: SSL_CLIENT_CERT and
        #    SSL_SERVER_CERT. These contain the PEM-encoded certificates of the
        #    server (always existing) and the client (only existing when client
        #    authentication is used). This can be used to import the certificates
        #    into CGI scripts.
        #   o StdEnvVars:
        #    This exports the standard SSL/TLS related `SSL_*' environment variables.
        #    Per default this exportation is switched off for performance reasons,
        #    because the extraction step is an expensive operation and is usually
        #    useless for serving static content. So one usually enables the
        #    exportation for CGI and SSI requests only.
        #   o OptRenegotiate:
        #    This enables optimized SSL connection renegotiation handling when SSL
        #    directives are used in per-directory context.
        #SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire
        <FilesMatch "\.(cgi|shtml|phtml|php)$">
                SSLOptions +StdEnvVars
        </FilesMatch>
        <Directory /usr/lib/cgi-bin>
                SSLOptions +StdEnvVars
                DirectoryIndex index.php
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>
        #   SSL Protocol Adjustments:
        #   The safe and default but still SSL/TLS standard compliant shutdown
        #   approach is that mod_ssl sends the close notify alert but doesn't wait for
        #   the close notify alert from client. When you need a different shutdown
        #   approach you can use one of the following variables:
        #   o ssl-unclean-shutdown:
        #    This forces an unclean shutdown when the connection is closed, i.e. no
        #    SSL close notify alert is send or allowed to received.  This violates
        #    the SSL/TLS standard but is needed for some brain-dead browsers. Use
        #    this when you receive I/O errors because of the standard approach where
        #    mod_ssl sends the close notify alert.
        #   o ssl-accurate-shutdown:
        #    This forces an accurate shutdown when the connection is closed, i.e. a
        #    SSL close notify alert is send and mod_ssl waits for the close notify
        #    alert of the client. This is 100% SSL/TLS standard compliant, but in
        #    practice often causes hanging connections with brain-dead browsers. Use
        #    this only for browsers where you know that their SSL implementation
        #    works correctly.
        #   Notice: Most problems of broken clients are also related to the HTTP
        #   keep-alive facility, so you usually additionally want to disable
        #   keep-alive for those clients, too. Use variable "nokeepalive" for this.
        #   Similarly, one has to force some clients to use HTTP/1.0 to workaround
        #   their broken HTTP/1.1 implementation. Use variables "downgrade-1.0" and
        #   "force-response-1.0" for this.
        BrowserMatch "MSIE [2-6]" \
                nokeepalive ssl-unclean-shutdown \
                downgrade-1.0 force-response-1.0
    </VirtualHost>
</IfModule>
# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
##########################
##### End Contents #######
##########################

##########################################################
# Finish Enabling SSL/HTTPS in Apache
##########################################################
sudo a2ensite default-ssl.conf
sudo service apache2 restart

##########################################################
# Enable URL Rewrites in Apache
##########################################################
sudo a2enmod rewrite
sudo service apache2 restart
Copy .htaccess file to root directory from existing production web server

# This will open a file for editing
sudo gedit /etc/apache2/apache2.conf

# Find the following text:
<Directory /var/www/>
	Options Indexes FollowSymLinks
	AllowOverride None
	Require all granted
</Directory>

# Replace the above text with:
<Directory /var/www/>
	Options Indexes FollowSymLinks
	AllowOverride All
	Require all granted
</Directory>

##########################################################
# Install MySQL Database Server
##########################################################
sudo apt install mysql-server

##########################################################
# Reset MySQL root password
##########################################################
sudo service mysql stop
sudo mkdir -p /var/run/mysqld
sudo chown mysql:mysql /var/run/mysqld
sudo /usr/sbin/mysqld --skip-grant-tables --skip-networking &
mysql -u root

# Logged in to MySQL prompt
mysql> FLUSH PRIVILEGES;
mysql> USE mysql;
mysql> UPDATE user SET authentication_string=PASSWORD("Rootroot$") WHERE User='root';
mysql> UPDATE user SET plugin="mysql_native_password" WHERE User='root';
myqsl> quit

# Back to standard shell prompt
sudo pkill mysqld
sudo service mysql start

# Restore local database from production
./scripts/mysql_prod_restore <ec2 host>
Enter production database password
Enter local database password

##########################################################
# Configure AWS SSH Keys
# Private key/configuration: 	     Ask Joe/Justin to send via Onetimesecret
# Private key file location: 	     ~/Downloads/magento_key_pair.pem
# SSH configuration file location:   ~/Downloads/config
##########################################################
mv ~/Downloads/magento_key_pair.pem ~/Downloads/config ~/.ssh

##########################################################
# Install AWS Client
# Public key: <New IAM User public key>
# Private key: <New IAM User private key>
##########################################################
sudo apt install awscli
aws configure

##########################################################
# Download Resold Repo
##########################################################
git config --global credential.helper '!aws codecommit credential-helper $@'
git config --global credential.UseHttpPath true
sudo chmod -R 777 /var/www/
cd /var/www/
git clone https://git-codecommit.us-west-2.amazonaws.com/v1/repos/Resold

##########################################################
# Place repo contents under webserver root
##########################################################
rm -rf /var/www/html/*
mv Resold/* html/
mv Resold/.* html/
rm -rf Resold

##########################################################
# Install Composer
##########################################################
sudo apt install composer
gedit ~/.bashrc

##########################################################
# Configure Magento bash command alias
##########################################################
alias magento="/var/www/html/bin/magento"
source ~/.bashrc

##########################################################
# Install dependencies and Magento framework
# Public key: 6fbc344485c32390fc61e3f022815dc4
# Private key: 6be2f9e73ea356d141e4f7e58d435ef1
##########################################################
cd /var/www/html/
./scripts/local_after_install.sh

##########################################################
##########################################################
# Extra
##########################################################
##########################################################

##########################################################
# Connect to AWS Web Server via SSH
##########################################################
ssh -i "~/.ssh/magento-key-pair.pem" ec2-user@ec2-54-190-254-30.us-west-2.compute.amazonaws.com

##########################################################
# Enable AWS S3 Storage for media files
##########################################################
Navigate to Stores > Configuration > General > Web
Change Base URL (Media) and Base URL Secure (Media) to: https://s3-us-west-2.amazonaws.com/resold-photos/

##########################################################
# Useful Magento commands
##########################################################
magento setup:upgrade 			          -- upgrade modules and application schema
magento setup:di:compile		          -- compile modules and code
magento setup:static-content:deploy 	-- deploy static web files to server
magento cache:clean			              -- clean the magento cache
magento cache:flush			              -- flush the magento cache

##########################################################
# Notes
##########################################################
The local_global attribute is hard coded all over the place, I know this sucks
