<?php

$filename =  '/var/www/html/app/etc/aws_env.php';
$contents = file_get_contents ( $filename );
file_put_contents ( '/var/www/html/app/etc/env.php', $contents );
