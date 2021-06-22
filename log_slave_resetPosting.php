<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

	$notrans=$_POST['notrans'];

	$str = "update ". $dbname . ".log_transaksiht SET post='0' where notransaksi='". $notrans ."' and post=1" ;
	saveLog($str);
	$res = mysql_query($str);

?>
