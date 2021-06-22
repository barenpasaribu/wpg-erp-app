<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$nopp = $_POST['nopp'];
$sql = 'select a.*,b.*,c.kodeanggaran from ' . $dbname . '.log_prapodt a inner join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang inner join ' . $dbname . '.keu_anggaran c on a.kd_anggran=c.kodeanggaran where `nopp`=\'' . $nopp . '\'';

exit(mysql_error() . 'rror');
($op = mysql_query($sql)) || true;
$sr = '';

while ($res = mysql_fetch_object($op)) {
	$srtm = $res->kodebarang . '#' . $res->namabarang . '#' . $res->satuan . '#' . $res->kodeanggaran . '#' . $res->jumlah . '#' . tanggalnormal($res->tgl_sdt) . '#' . $res->keterangan;

	if ($sr != '') {
		$sr .= '##' . $srtm;
	}
	else {
		$sr .= $srtm;
	}
}

echo $sr;

?>
