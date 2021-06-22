<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$kar = $_POST['kar'];
$kdorg = $_POST['kdorg'];
$method = $_POST['method'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$lokKar = makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
echo "\r\n";

switch ($method) {
case 'update':
	$i = 'UPDATE  ' . $dbname . '.`setup_temp_lokasitugas` SET  ' . "\r\n\t\t\t" . '`kodeorg` =  \'' . $kdorg . '\' WHERE  `karyawanid` =\'' . $kar . '\'';

	if (mysql_query($i)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	$i = 'insert into ' . $dbname . '.setup_temp_lokasitugas (karyawanid,kodeorg)' . "\r\n\t\t" . 'values (\'' . $kar . '\',\'' . $kdorg . '\')';

	if (mysql_query($i)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	echo "\r\n\t" . '<div id=container>' . "\r\n\t\t" . '<table class=sortable cellspacing=1 border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t\t" . ' <tr class=rowheader>' . "\r\n\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['lokasitugas'] . ' Temp</td>' . "\r\n\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['lokasitugas'] . ' Datakaryawan</td>' . "\r\n\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['action'] . '</td>' . "\r\n\t\t\t" . ' </tr>' . "\r\n\t\t" . '</thead>' . "\r\n\t\t" . '<tbody>';
	$limit = 30;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$maxdisplay = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_temp_lokasitugas where kodeorg in ' . "\r\n\t\t\t" . '(select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['empl']['kodeorganisasi'] . '\')';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$i = 'select * from ' . $dbname . '.setup_temp_lokasitugas where kodeorg in ' . "\r\n\t\t\t" . '(select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['empl']['kodeorganisasi'] . '\') limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($n = mysql_query($i)) || true;
	$no = $maxdisplay;

	while ($d = mysql_fetch_assoc($n)) {
		$no += 1;
		echo '<tr class=rowcontent>';
		echo '<td align=center>' . $no . '</td>';
		echo '<td align=left>' . $nmKar[$d['karyawanid']] . '</td>';
		echo '<td align=left>' . $nmOrg[$d['kodeorg']] . '</td>';
		echo '<td align=left>' . $nmOrg[$lokKar[$d['karyawanid']]] . '</td>';
		echo '<td align=center>' . "\r\n\t\t\t" . '<img src=images/application/application_edit.png class=resicon  caption=\'Edit\' ' . "\r\n\t\t\t" . 'onclick="edit(\'' . $d['karyawanid'] . '\',\'' . $d['kodeorg'] . '\');">' . "\r\n\t\t\t" . '<img src=images/application/application_delete.png class=resicon  caption=\'Delete\' onclick="del(\'' . $d['karyawanid'] . '\');"></td>';
		echo '</tr>';
	}

	echo "\r\n\t\t" . '<tr class=rowheader><td colspan=18 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	echo '</tbody></table>';
	break;

case 'delete':
	$i = 'delete from ' . $dbname . '.setup_temp_lokasitugas where karyawanid=\'' . $kar . '\' ';

	if (mysql_query($i)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
