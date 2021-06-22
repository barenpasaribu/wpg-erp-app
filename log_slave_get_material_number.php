<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$num = 0;
$mayor = $_POST['mayor'];
$str = 'select max(kodebarang) as x from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '      kelompokbarang=\'' . $mayor . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$num = $bar->x;
}
$n= $num;
$num = intval($num) + 1;

/*
switch ($num) {
case $num < 10:
	$n = $mayor . '0000' . $num;
	break;

case $mayor = $_POST['mayor']:
	$n = $mayor . '000' . $num;
	break;

case $mayor = $_POST['mayor']:
	$n = $mayor . '00' . $num;
	break;

case $mayor = $_POST['mayor']:
	$n = $mayor . '0' . $num;
	break;

default:
}
*/

// 32.101.042
switch ($num) {
case $num < 10:
	$n = $mayor . '0000' . $num;
	break;

case $num < 100:
	$n = $mayor . '000' . $num;
	break;

case $num < 1000:
	$n = $mayor . '00' . $num;
	break;

case $num < 10000:
	$n = $mayor . '0' . $num;
	break;

default:
	$n = $num;
}

//echo str_pad($n, 8, '0', STR_PAD_LEFT);
echo $n;
?>
