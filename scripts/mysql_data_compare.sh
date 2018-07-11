##########################################################################
##########################################################################
# Runs a full database comparison between localhost and AWS servers
##########################################################################
##########################################################################
mysqldump --skip-comments --skip-extended-insert -u root -p -h localhost MagentoQuickstartDB > /var/www/html/dumps/source.sql
mysqldump --skip-comments --skip-extended-insert -u admin -p -h "mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com" MagentoQuickstartDB > /var/www/html/dumps/destination.sql
diff /var/www/html/dumps/source.sql /var/www/html/dumps/destination.sql | less
