<?php

// exabytes - WPG
$dbport   = '3306';
$dbserver = '202.157.185.209';
$dbname   = 'fastenvi_wpg_live';
$uname    = 'admin';
$passwd   = 'WPG123!@#';



/*
// exabytes - anthesis
$dbport   = '3306';
$dbserver = '202.157.177.13';
$dbname   = 'fastenvi_wpg_live';
$uname    = 'admin';
$passwd   = 'MOONlight!@#';
*/

@$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Error/Gagal :Unable to Connect to database ".mysql_error());
mysql_select_db($dbname);
$mysqli = new mysqli($dbserver, $uname, $passwd, $dbname);

$PATH_FILE_UPLOAD_ABSENSI = "C:\xampp\htdocs\anthesis-erp\upload_cdslsp";
?>
