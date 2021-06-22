<?php


require_once 'master_validation.php';
include 'lib/nangkoelib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$arr0 = '##tanggal';
echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script type="text/javascript" src="js/pad_petagrtt.js"></script>' . "\r\n" . '<script type="text/javascript" language="JavaScript1.2" src="js/biGraph_map.js"></script>' . "\r\n" . '<script>' . "\r\n\r\n\r\n" . '</script>' . "\r\n\r\n" . '<link rel=\'stylesheet\' type=\'text/css\' href=\'style/zTable.css\'>' . "\r\n\r\n";
$title[0] = $_SESSION['lang']['peta'] . ' ' . $_SESSION['lang']['grtt'];
$sorg = 'select kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '    where tipe = \'KEBUN\'' . "\r\n" . '    order by kodeorganisasi asc';

#exit(mysql_error());
($qorg = mysql_query($sorg)) || true;

while ($rorg = mysql_fetch_assoc($qorg)) {
	$optorg .= '<option value=\'' . $rorg['kodeorganisasi'] . '\'>' . $rorg['namaorganisasi'] . '</option>';
}

$frm .= 0;
$hfrm[0] = $title[0];
drawTab('FRM', $hfrm, $frm, 200, 1100);
CLOSE_BOX();
echo close_body();

?>
