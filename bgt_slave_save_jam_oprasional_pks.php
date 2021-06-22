<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$thnbudget = $_POST['thnbudget'];
$kdpks = $_POST['kdpks'];
$jamo = $_POST['jamo'];
$jamb = $_POST['jamb'];
$arrEnum = getEnum($dbname, 'bgt_jam_operasioal_pks', 'jamolah,breakdown');
$method = $_POST['method'];

switch ($method) {
case 'update':
	$str = 'update ' . $dbname . '.bgt_jam_operasioal_pks set jamolah=\'' . $jamo . '\',breakdown=\'' . $jamb . '\'' . "\r\n\t" . '       where tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	$str = 'select * from ' . $dbname . '.bgt_jam_operasioal_pks ' . "\r\n\t" . '       where tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\'' . "\r\n" . '            limit 0,1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$sudahada = '1';
		$pesan = $bar->tahunbudget . '-' . $bar->millcode . '-' . $bar->jamolah . '-' . $bar->breakdown;
	}

	if ($sudahada == '1') {
		echo ' Gagal, data sudah ada: ' . $pesan;
		exit();
	}

	$str = 'insert into ' . $dbname . '.bgt_jam_operasioal_pks (`tahunbudget`,`millcode`,`jamolah`,`breakdown`)' . "\r\n\t\t" . 'values (\'' . $thnbudget . '\',\'' . $kdpks . '\',\'' . $jamo . '\',\'' . $jamb . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.bgt_jam_operasioal_pks ' . "\r\n\t" . '       where tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

$str1 = ($thnbudget = $_POST['thnbudget']) . '.bgt_jam_operasioal_pks order by tahunbudget';

if ($res1 = mysql_query($str1)) {
	while ($bar1 = mysql_fetch_object($res1)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td align=center>' . $no . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->tahunbudget . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $bar1->millcode . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->jamolah . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->breakdown . '</td>' . "\t\t\t\r\n\t\t" . '<td align=center><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->tahunbudget . '\',\'' . $bar1->millcode . '\',\'' . $bar1->jamolah . '\',\'' . $bar1->breakdown . '\');"></td></tr>';
	}
}

echo "\r\n\r\n\r\n\r\n\r\n";

?>
