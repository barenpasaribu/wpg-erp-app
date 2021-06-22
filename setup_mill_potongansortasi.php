<?php

require_once 'master_validation.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zLib.php';


//require_once 'config/connection.php';
/*
$dbport   = '3306';
$dbserver = 'localhost';
$dbname   = 'wbssp';
$uname    = 'root';
$passwd   = '';

@$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Error/Gagal :Unable to Connect to database ".mysql_error());
mysql_select_db($dbname);
$mysqli = new mysqli($dbserver, $uname, $passwd, $dbname);

*/

$dbport   = '3306';
$dbserver = '202.52.147.83';
$dbname   = 'fastenvi_wb';
$uname    = 'fastenvi_fesdb';
$passwd   = 'Chelsea!@#';


$kodeorg = $_POST['kodeorg'];
$tanggal = $_POST['tanggal'];

//$tanggal = tanggalsystem($_POST['tanggal']);
$data=$param;


if($_POST['tanggal']!='')

{

        $txtTgl=tanggalsystem($_POST['tanggal']);

        $txt_tgl_a=substr($txtTgl,0,4);

        $txt_tgl_b=substr($txtTgl,4,2);

        $txt_tgl_c=substr($txtTgl,6,2);

        $txtTgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;


}

$txtTgl="2017-08-28";

$data=$param;

$dbname2='fastenvi_wb';

$productcode="40000003";
$no_trans="0000001/BJUE05/08/2017";

//$str = 'select KGPOTSORTASI from ' . $dbname2 . '.`mstrxtbs` where PRODUCTCODE="'. $productcode .'" AND SPBNO="'. $no_trans .'" AND DATEOUT LIKE"%'. $txtTgl.'%"';
$str = 'select SUM(KGPOTSORTASI) AS KGPOTSORTASI from ' . $dbname2 . '.`mstrxtbs` where PRODUCTCODE="'. $productcode .'" AND SPBNO="'. $no_trans .'" AND DATEOUT LIKE"%'. $txtTgl.'%" GROUP BY DATEOUT';


	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {

		$max = $bar->KGPOTSORTASI;

    }
   
    $table .= $max;

   // echo $str;
    echo $table;

?>

