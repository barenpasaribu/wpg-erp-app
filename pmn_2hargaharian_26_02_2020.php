<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo '<script type="text/javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=\'js/pmn_2hargaharian.js\'></script>' . "\r\n\r\n";
$arr = '##periodePsr##barang';
$arr2 = '##komodoti##periodePsr2';
$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optBrg = $optPeriode;
$str = 'select distinct substr(tanggal,1,7) as periode, kodeproduk from ' . $dbname . '.pmn_hargapasar order by tanggal desc';
$res = mysql_query($str);
$listBarang = '';
$period = array();

while ($bar = mysql_fetch_object($res)) {
	if (!empty($listBarang)) {
		$listBarang .= ',';
	}

	$listBarang .= '\'' . $bar->kodeproduk . '\'';
	$period[$bar->periode] = $bar->periode;
}

foreach ($period as $p) {
	$optPeriode .= '<option value=\'' . $p . '\'>' . $p . '</option>';
}

$sBrng = 'select distinct kodebarang,namabarang from ' . $dbname . '.log_5masterbarang where kodebarang in (' . $listBarang . ') order by namabarang asc';

#exit(mysql_error($conn));
($qBrng = mysql_query($sBrng)) || true;

while ($rBarang = mysql_fetch_assoc($qBrng)) {
	$optBrg .= '<option value=\'' . $rBarang['kodebarang'] . '\'>' . $rBarang['namabarang'] . '</option>';
}

OPEN_BOX('', '<b>Daily Price</b><br>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = 'Trend Harga Harian';
$hfrm[1] = 'Trend Harga Bulanan';
drawTab('FRM', $hfrm, $frm, 220, 930);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
