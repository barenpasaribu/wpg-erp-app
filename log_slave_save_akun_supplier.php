<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$noakun = $_POST['noakun'];
$akunpajak = $_POST['akunpajak'];
$idsupplier = $_POST['idsupplier'];
$an = $_POST['an'];
$bank = $_POST['bank'];
$rek = $_POST['rek'];
$noseripajak = $_POST['noseripajak'];
$nilaihutang = $_POST['nilaihutang'];
$method = trim($_POST['method']);

if ($nilaihutang == '') {
	$nilaihutang = 0;
}

$strx = 'select 1=1';

switch ($method) {
case 'update':
	$strx = 'update ' . $dbname . '.log_5supplier set' . "\r\n" . '                   noakun=\'' . $noakun . '\',' . "\r\n\t\t\t\t" . '   akunpajak=\'' . $akunpajak . '\',' . "\r\n\t\t\t\t" . '   an=\'' . $an . '\',' . "\r\n\t\t\t\t" . '   bank=\'' . $bank . '\',' . "\r\n\t\t\t\t" . '   rekening=\'' . $rek . '\',' . "\r\n\t\t\t\t" . '   noseripajak=\'' . $noseripajak . '\',' . "\r\n\t\t\t\t" . '   nilaihutang=' . $nilaihutang . "\r\n\t\t\t\t" . '   where supplierid=\'' . $idsupplier . '\'' . "\r\n\t\t\t\t" . '  ';
		$resX = mysql_query($strx);
	break;
}

if (($strx)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

if (isset($_POST['txt'])) {
	$txt = $_POST['txt'];
	$str = ' select * from ' . $dbname . '.log_5supplier where namasupplier like \'%' . $txt . '%\' order by supplierid';
}
else {
	$str = ' select * from ' . $dbname . '.log_5supplier where supplierid=\'' . $idsupplier . '\' order by supplierid';
}

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '     <td>' . $bar->kodekelompok . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->supplierid . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->namasupplier . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->alamat . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->kontakperson . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->kota . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->telepon . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td>' . $bar->fax . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td>' . $bar->email . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td>' . $bar->npwp . '</td>' . "\t" . ' ' . "\r\n\t\t\t" . ' <td align=right>' . number_format($bar->plafon, 0, ',', '.') . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->noakun . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->akunpajak . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->noseripajak . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->bank . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->rekening . '</td>' . "\r\n\t\t\t" . ' <td>' . $bar->an . '</td>' . "\r\n\t\t\t" . ' <td align=right>' . number_format($bar->nilaihutang, 0, ',', '.') . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editAkunSupplier(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\',\'' . $bar->noakun . '\',\'' . $bar->nilaihutang . '\',\'' . $bar->noseripajak . '\',\'' . $bar->akunpajak . '\',\'' . $bar->bank . '\',\'' . $bar->rekening . '\',\'' . $bar->an . '\');"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
