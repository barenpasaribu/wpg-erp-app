<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$tipe = $_POST['tipe'];
$str = 'select kode,kelompok from ' . $dbname . '.log_5klsupplier where tipe=\'' . $tipe . '\'';
$res = mysql_query($str);
$opt = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->kode . '\'>' . $bar->kelompok . '</option>';
}

echo $opt;

?>
