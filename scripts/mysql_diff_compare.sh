##########################################################################
##########################################################################
# Runs a difference database comparison between localhost and AWS servers
##########################################################################
##########################################################################
mysqldiff --server1=root:"Rootroot$"@localhost:3306:/var/run/mysqld/mysqld.sock --server2=admin:"Rootroot$"@"mm6imdf4u5ak4w.czqsdryzxcba.us-west-2.rds.amazonaws.com":3306:/var/run/mysqld/mysqld.sock MagentoQuickstartDB:MagentoQuickstartDB
