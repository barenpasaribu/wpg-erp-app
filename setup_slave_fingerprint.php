<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$pin = $_POST['pin'];
$karyawanid = $_POST['karyawanid'];
$method = $_POST['method'];

switch ($method) {
case 'update':
	$sCek = 'select distinct a.karyawanid, a.pin, b.namakaryawan from ' . $dbname . '.att_adaptor a' . "\r\n" . '        left join ' . $dbname . '.datakaryawan b on a.karyawanid = b.karyawanid' . "\r\n" . '        where a.pin = \'' . $pin . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);
	$karz = '';
	$pinz = '';

	while ($bar1 = mysql_fetch_object($qCek)) {
		$karz = $bar1->karyawanid;
		$namz = $bar1->namakaryawan;
		$pinz = $bar1->pin;
	}

	if ($rCek != 0) {
		exit('Error. Data Sudah Ada: ' . $namz . ' (' . $pinz . ')');
	}

	$str = 'update ' . $dbname . '.att_adaptor set pin=\'' . $pin . '\'' . "\r\n" . '        where karyawanid=\'' . $karyawanid . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	$sCek = 'select distinct a.karyawanid, a.pin, b.namakaryawan from ' . $dbname . '.att_adaptor a' . "\r\n" . '        left join ' . $dbname . '.datakaryawan b on a.karyawanid = b.karyawanid' . "\r\n" . '        where ((a.karyawanid like \'%' . $karyawanid . '%\') or (a.pin = \'' . $pin . '\'))';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);
	$karz = '';
	$pinz = '';

	while ($bar1 = mysql_fetch_object($qCek)) {
		$karz = $bar1->karyawanid;
		$namz = $bar1->namakaryawan;
		$pinz = $bar1->pin;
	}

	if ($rCek != 0) {
		exit('Error. Data Sudah Ada: ' . $namz . ' (' . $pinz . ')');
	}

	$str = 'insert into ' . $dbname . '.att_adaptor' . "\r\n" . '        (karyawanid,pin)' . "\r\n" . '        values(\'' . $karyawanid . '\',\'' . $pin . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.att_adaptor' . "\r\n" . '    where karyawanid=\'' . $karyawanid . '\' and pin=\'' . $pin . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	$str1 = 'select a.karyawanid, a.pin, b.namakaryawan' . "\r\n" . '        from ' . $dbname . '.att_adaptor a ' . "\r\n" . '        left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid ' . "\r\n" . '        order by b.namakaryawan';

	if ($res1 = mysql_query($str1)) {
		while ($bar1 = mysql_fetch_object($res1)) {
			echo '<tr class=rowcontent>' . "\r\n" . '            <td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '            <td>' . $bar1->pin . '</td>' . "\r\n" . '            <td align=center nowrap>' . "\r\n" . '            <img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->karyawanid . '\',\'' . $bar1->pin . '\');">' . "\r\n" . '            <img src=images/application/application_delete.png class=resicon  caption=\'Delete\' onclick="dedel(\'' . $bar1->karyawanid . '\',\'' . $bar1->pin . '\');">' . "\r\n" . '            </td></tr>';
		}
	}

	break;
}

?>
