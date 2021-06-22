<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$keterangan = $_POST['keterangan'];
$jumlahhk = $_POST['jumlahhk'];
$group = $_POST['grup'];
$method = $_POST['method'];

switch ($method) {
case 'update':
	$sCek = 'select distinct * from ' . $dbname . '.keu_5akunbank where namabank like \'%' . $jumlahhk . '%\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek != 0) {
		exit('Error:Data Sudah Ada');
	}

	$str = 'update ' . $dbname . '.keu_5akunbank set namabank=\'' . $jumlahhk . '\'' . "\r\n\t" . '       where noakun=\'' . $group . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	$sCek = 'select distinct * from ' . $dbname . '.keu_5akunbank where namabank like \'%' . $jumlahhk . '%\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek != 0) {
		exit('Error:Data Sudah Ada');
	}

	$str = 'insert into ' . $dbname . '.keu_5akunbank' . "\r\n\t" . '      (noakun,namabank)' . "\r\n\t" . '      values(\'' . $group . '\',\'' . $jumlahhk . '\')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.keu_5akunbank' . "\r\n\t" . 'where namabank=\'' . $jumlahhk . '\' and noakun=\'' . $group . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	$str1 = 'select * from ' . $dbname . '.keu_5akunbank order by namabank';

	if ($res1 = mysql_query($str1)) {
		while ($bar1 = mysql_fetch_object($res1)) {
			echo '<tr class=rowcontent>' . "\r\n" . '                       <td align=center>' . $bar1->noakun . '</td>' . "\r\n" . '                               <td>' . $bar1->namabank . '</td>' . "\r\n" . '                               <td align=center><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->noakun . '\',\'' . $bar1->namabank . '\');"></td></tr>';
		}
	}

	break;
}

?>
