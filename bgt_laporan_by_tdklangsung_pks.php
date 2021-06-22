<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/bgt_btl_kebun.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['btlpks']);
$str = 'select distinct(tahunbudget) as tahunbudget from  ' . $dbname . '.bgt_budget order by tahunbudget desc';
$res = mysql_query($str);
$opttahun = '<option value=\'\'>Pilih..</option>';

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select kodeorganisasi as kodeorg from  ' . $dbname . '.organisasi where tipe=\'PABRIK\' order by kodeorganisasi';
$res = mysql_query($str);
$optunit = '<option value=\'\'>Pilih..</option>';

while ($bar = mysql_fetch_object($res)) {
	$optunit .= '<option value=\'' . $bar->kodeorg . '\'>' . $bar->kodeorg . '</option>';
}

echo '<fieldset style=\'width:500px;\'><table>' . "\r\n" . '     <tr><td>' . $_SESSION['lang']['tahunanggaran'] . '</td><td><select id=thnbudget style=\'width:200px\'>' . $opttahun . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['kodeorganisasi'] . '</td><td><select id=kodeunit style=\'width:200px\'>' . $optunit . '</select></td></tr>' . "\r\n" . '     </table>' . "\r\n\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t" . ' <button class=mybutton onclick=tampilkanBTLPks()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo '<div id=container>' . "\r\n" . '</div>';
CLOSE_BOX();
echo close_body();

?>
