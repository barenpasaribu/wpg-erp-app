<?php

require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zLib.php';

$ngapain = $_POST['ngapain'];

$kodeorg = $_POST['kodeorg'];
$tanggal = $_POST['tanggal'];

$tanggal = tanggalsystem($_POST['tanggal']);

$data=$param;
//$str = 'select sisaakhir from `log_transaksi_mill` where kodeorg="'. $kodeorg .'" AND tanggal="'. $tanggal.'"- INTERVAL 1 DAY ORDER BY id DESC LIMIT 1'  ;
$str = 'select SUM(sisaakhir) as sisaakhir from `log_transaksi_mill` where kodeorg="'. $kodeorg .'" AND tanggal="'. $tanggal.'"- INTERVAL 1 DAY GROUP BY tanggal  DESC LIMIT 1'  ;

	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {

		$max = $bar->sisaakhir;

    }
 
    $table .= $max;
    echo $table;



?>

