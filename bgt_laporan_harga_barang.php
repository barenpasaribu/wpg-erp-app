<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/bgt_laporan_harga_barang.js"></script>' . "\r\n";
$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '        where (tipe=\'WORKSHOP\') and kodeorganisasi like \'' . $_SESSION['empl']['lokasitugas'] . '%\'' . "\r\n" . '        ';
$optws = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optws .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select kodebudget,nama from ' . $dbname . '.bgt_kode' . "\r\n" . '        where kodebudget like \'SDM%\'' . "\r\n" . '        ';
$optkodebudget0 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optkodebudget0 .= '<option value=\'' . $bar->kodebudget . '\'>' . $bar->nama . '</option>';
}

$str = 'select kodebudget,nama from ' . $dbname . '.bgt_kode' . "\r\n" . '        where kodebudget like \'M%\'' . "\r\n" . '        ';
$optmaterial1 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optmaterial1 .= '<option value=\'' . $bar->kodebudget . '\'>' . $bar->nama . '</option>';
}

$str = 'select kodebudget,nama from ' . $dbname . '.bgt_kode' . "\r\n" . '        where kodebudget like \'TOOL%\'' . "\r\n" . '        ';
$opttool2 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opttool2 .= '<option value=\'' . $bar->kodebudget . '\'>' . $bar->nama . '</option>';
}

$str = 'select kodebudget,nama from ' . $dbname . '.bgt_kode' . "\r\n" . '                    where kodebudget like \'TRANSIT%\'' . "\r\n" . '                    ';
$opttransit3 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opttransit3 .= '<option value=\'' . $bar->kodebudget . '\'>' . $bar->nama . '</option>';
}

$str = 'select noakun,namaakun from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1 and tipeakun = \'Biaya\' order by noakun' . "\r\n" . '                    ';
$optakun3 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optakun3 .= '<option value=\'' . $bar->noakun . '\'>' . $bar->noakun . ' - ' . $bar->namaakun . '</option>';
}

$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget ' . "\r\n" . '                    order by tahunbudget desc' . "\r\n" . '                    ';
$opttahunbudget0 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opttahunbudget0 .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select regional, nama from ' . $dbname . '.bgt_regional' . "\r\n" . '                    order by nama desc' . "\r\n" . '                    ';
$optregional0 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optregional0 .= '<option value=\'' . $bar->regional . '\'>' . $bar->nama . '</option>';
}

$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '                    order by kode ' . "\r\n" . '                    ';
$optkelompokbarang0 = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optkelompokbarang0 .= '<option value=\'' . $bar->kode . '\'>' . $bar->kode . ' - ' . $bar->kelompok . '</option>';
}

OPEN_BOX('', '<b>' . $_SESSION['lang']['hargabarang'] . '</b>');
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = 'per ' . $_SESSION['lang']['kelompokbarang'];
$hfrm[1] = $_SESSION['lang']['caribarang'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
