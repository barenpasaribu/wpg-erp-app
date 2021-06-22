<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo open_body();
//echo '<script>\r\n'.
//'var \r\n'.
//'</script>';

echo '<script language=javascript1.2 src=\'js/log_laporan.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['laporanstok']) . '</b>');
OPEN_BOX('', '<b>LAPORAN PERSEDIAAN FISIK</b>');
$whr = 'namaorganisasi!=\'\'';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whr);
$str = 'select distinct periode from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '      order by periode desc';
$res = mysql_query($str);
$optper = '<option value=\'\'>' . $_SESSION['lang']['sekarang'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optper .= '<option value=\'' . $bar->periode . '\'>' . substr($bar->periode, 5, 2) . '-' . substr($bar->periode, 0, 4) . '</option>';
}

$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '      where tipe=\'PT\'' . "\r\n\t" . '  order by namaorganisasi desc';
$res = mysql_query($str);
echoMessage("pt ",mysql_fetch_object($res));
$optpt = '';

while ($bar = mysql_fetch_object($res)) {
	$optpt .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select distinct a.kodeorg,b.namaorganisasi from ' . $dbname . '.setup_periodeakuntansi a' . "\r\n" . '      left join ' . $dbname . '.organisasi b' . "\r\n\t" . '  on a.kodeorg=b.kodeorganisasi' . "\r\n" . '      where b.tipe=\'GUDANG\'' . "\r\n\t" . '  order by namaorganisasi desc';
$res = mysql_query($str);
echoMessage("gudang ",mysql_fetch_object($res));
$optgudang = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optgudang .= '<option value=\'' . $bar->kodeorg . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select distinct a.kodeorg,b.namaorganisasi from ' . $dbname . '.setup_periodeakuntansi a' . "\r\n" . '      left join ' . $dbname . '.organisasi b' . "\r\n\t" . '  on a.kodeorg=b.kodeorganisasi' . "\r\n" . '      where b.tipe=\'GUDANG\'' . "\r\n\t" . '  order by namaorganisasi desc';
$res = mysql_query($str);
$optgudang2 = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optgudang2 .= '<option value=\'' . $bar->kodeorg . '\'>' . $bar->namaorganisasi . '</option>';
}

$optUnit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optUnit2 = $optGdng = $optUnit;
$sUnit = 'select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ' . $dbname . '.organisasi where tipe like \'GUDANG%\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$optUnit .= '<option value=\'' . $rUnit['kodeorganisasi'] . '\'>' . $optNmOrg[$rUnit['kodeorganisasi']] . '</option>';
}

$sUnit2 = 'select distinct substr(kodeorganisasi,1,4) as kodeorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '        where tipe like \'GUDANG%\' and namaorganisasi!=\'\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qUnit2 = mysql_query($sUnit2)) || true;

while ($rUnit2 = mysql_fetch_assoc($qUnit2)) {
	$optUnit2 .= '<option value=\'' . $rUnit2['kodeorganisasi'] . '\'>' . $optNmOrg[$rUnit2['kodeorganisasi']] . '</option>';
}

$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$x = 0;

while ($x < 13) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optPeriode .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	$optPeriode2 .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	++$x;
}

echo '<br /><fieldset style=width:250px;float:left;>' . "\r\n" . '     <legend>' . $_SESSION['lang']['laporanstok'] . ' Per ' . $_SESSION['lang']['pt'] . '</legend>' . "\r\n" . '         <table border=0 cellpadding=1 cellspacing=1>' . "\r\n" . '         <tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['pt'] . '</td><td><select id=pt style=\'width:150px;\' onchange=hideById(\'printPanel\')>' . $optpt . '</select></td></tr><!--<tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['sloc'] . '</td><td><select id=gudang style=\'width:150px;\' onchange=hideById(\'printPanel\')>' . $optgudang . '</select></td></tr>--><tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['periode'] . '</td><td><select id=periode onchange=hideById(\'printPanel\')>' . $optper . '</select></td></tr><tr><td colspan=2 align=center>' . "\r\n\t" . ' <button class=mybutton onclick=getLaporanFisik()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table>' . "\r\n\t" . ' </fieldset>' . "\r\n" . '    <fieldset style=width:250px;float:left;>' . "\r\n" . '     <legend>' . $_SESSION['lang']['persediaanfisik'] . ' Per ' . $_SESSION['lang']['sloc'] . '</legend>' . "\r\n" . '         <table cellpadding=1 cellspacing=1 border=0>' . "\r\n" . '         <tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['unit'] . '</td><td><select id=unitDt style=\'width:150px;\' onchange=getGudangDt()>' . $optUnit . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['sloc'] . '</td><td><select id=gudang2 style=\'width:150px;\' onchange=hideById(\'printPanel2\')>' . $optGdng . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['periode'] . '</td><td><select id=periode2 onchange=hideById(\'printPanel2\')>' . $optPeriode . '</select></td></tr>' . "\r\n\t" . ' <tr><td colspan=2><button class=mybutton onclick=getLaporanFisik2()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table>' . "\r\n\t" . ' </fieldset>' . "\r\n" . '      <fieldset style=width:250px;>' . "\r\n" . '     <legend>' . $_SESSION['lang']['persediaanfisik'] . ' Per ' . $_SESSION['lang']['unit'] . '</legend>' . "\r\n" . '         <table cellpadding=1 cellspacing=1 border=0>' . "\r\n" . '         <tr><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['unit'] . '</td><td><select id=unitDt2 style=\'width:150px;\' onchange=hideById(\'printPanel3\')>' . $optUnit2 . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['periode'] . '</td><td><select id=periode3 onchange=hideById(\'printPanel3\')>' . $optPeriode2 . '</select></td></tr>' . "\r\n\t" . ' <tr><td colspan=2><button class=mybutton onclick=getLaporanFisik3()>' . $_SESSION['lang']['proses'] . '</button></td></tr></table>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel(event,\'log_laporanPersediaanFisik_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=fisikKePDF(event,\'log_laporanPersediaanFisik_pdf.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>   ' . "\r\n" . '         <span id=printPanel2 style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel2(event,\'log_slaveLaporanPersediaanFisikUnit.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=fisikKePDF2(event,\'log_slaveLaporanPersediaanFisikUnit.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>' . "\r\n" . '         <span id=printPanel3 style=\'display:none;\'>' . "\r\n" . '     <img onclick=fisikKeExcel3(event,\'log_slaveLaporanPersediaanFisikUnit2.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'>' . "\r\n\t" . ' <img onclick=fisikKePDF3(event,\'log_slaveLaporanPersediaanFisikUnit2.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>' . "\r\n\t" . ' <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '       <table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td align=center>No.</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n\t\t\t" . '</tr>  ' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\t\t" . ' ' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
