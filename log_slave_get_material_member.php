<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kelompok = $_POST['mayor'];
$kodebarang = $_POST['kodebarang'];
$namabarangx = mb_convert_encoding($_POST['namabarang'], 'UTF-8', 'WINDOWS-1252');
$namabarang = htmlentities($namabarangx);
$satuan = $_POST['satuan'];
$minstok = $_POST['minstok'];
$konversi = $_POST['konversi'];
$nokartu = $_POST['nokartu'];
$method = $_POST['method'];
$strx = 'select 1=1';

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $kodebarang . '\' and kelompokbarang=\'' . $kelompok . '\'';
	break;

case 'update':
	$strx = 'update ' . $dbname . '.log_5masterbarang set ' . "\r\n\t\t\t" . '       namabarang=\'' . $namabarang . '\',' . "\r\n\t\t\t" . '       satuan=\'' . $satuan . '\',minstok=' . $minstok . ',' . "\r\n\t\t\t\t" . '   nokartubin=\'' . $nokartu . '\',' . "\r\n" . '                                   konversi=\'' . $konversi . '\'' . "\r\n\t\t\t\t" . '   where kelompokbarang=\'' . $kelompok . '\' ' . "\r\n\t\t\t\t" . '   and kodebarang=\'' . $kodebarang . '\'';
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.log_5masterbarang(' . "\r\n\t\t\t" . '       kelompokbarang,kodebarang,namabarang,satuan,minstok,' . "\r\n\t\t\t\t" . '   nokartubin,konversi)' . "\r\n\t\t\t" . 'values(\'' . $kelompok . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $satuan . '\',' . $minstok . ',' . "\r\n\t\t\t\t\t" . ' \'' . $nokartubin . '\',' . $konversi . ')';
	break;
}

if (($strx)) {
	mysql_query($strx);
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str = 'select kodebarang, spesifikasi from ' . $dbname . '.log_5photobarang' . "\r\n" . '    where kodebarang like \'' . $kelompok . '%\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$spek[$bar->kodebarang] = $bar->spesifikasi;
}

$txtfind = trim($_POST['txtcari']);

if (isset($_POST['txtcari']) && ($txtfind != '') && ($kelompok != 'All')) {
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' and kelompokbarang=\'' . $kelompok . '\' order by kodebarang';
}
else if (isset($_POST['txtcari']) && ($txtfind !== '') && ($kelompok == 'All')) {
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' order by kodebarang';
}
else {
	$str = 'select * from ' . $dbname . '.log_5masterbarang where kelompokbarang=\'' . $kelompok . '\' order by kodebarang';
}

$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$stru = 'select * from ' . $dbname . '.log_5photobarang where kodebarang=\'' . $bar->kodebarang . '\'';

	if (0 < mysql_num_rows(mysql_query($stru))) {
		$adx = '<img src=images/zoom.png class=resicon height=16px title=\'View detail\'  onclick=viewDetailbarang(\'' . $bar->kodebarang . '\',event)> <img src=images/tool.png class=resicon height=16px title=\'Edit Detail\'  onclick=editDetailbarang(\'' . $bar->kodebarang . '\',event)>';
	}

	$no += 1;
	$isikon = 'NO';

	if ($bar->konversi == 1) {
		$isikon = 'YES';
	}

	echo '<tr class=rowcontent>' . "\r\n\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t" . '  <td>' . $bar->kelompokbarang . '</td>' . "\r\n\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t" . '  <td>' . $spek[$bar->kodebarang] . '</td>' . "\r\n\t\t" . '  <td align=right>' . $bar->minstok . '</td>' . "\r\n\t\t" . '  <td>' . $bar->nokartubin . '</td>' . "\r\n\t\t" . '  <td>' . $isikon . '</td>' . "\r\n\t\t" . '  <td align=center><input type=checkbox id=\'br' . $bar->kodebarang . '\' value=\'' . $bar->kodebarang . '\' ' . ($bar->inactive == 0 ? '' : ' checked') . ' onclick=setInactive(this.value);></td>' . "\r\n\t\t" . '  <td align=center>' . $adx . '</td>' . "\r\n\t\t" . '  <td>' . "\r\n\t\t" . '      <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kelompokbarang . '\',\'' . $bar->kodebarang . '\',\'' . htmlspecialchars($bar->namabarang, ENT_IGNORE) . '\',\'' . $bar->satuan . '\',\'' . $bar->minstok . '\',\'' . $bar->nokartubin . '\',\'' . $bar->konversi . '\');"> ' . "\r\n\t\t\t" . '  <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delBarang(\'' . $bar->kodebarang . '\',\'' . $bar->kelompokbarang . '\');">' . "\r\n\t\t" . '  </td>' . "\r\n\t\t" . '  </tr>';
}

?>
