<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/bgt_departemen.js"></script>' . "\r\n";
$optdepartemen = '';
$optdepartemen .= '<option value=\'' . $_SESSION['empl']['bagian'] . '\'>' . $_SESSION['empl']['bagian'] . '</option>';
$str = 'select kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '        where length(kodeorganisasi) = 4 order by kodeorganisasi' . "\r\n" . '        ';
$optalokasi = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optalokasi .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->kodeorganisasi . '</option>';
}

$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_dept' . "\r\n" . '        where departemen = \'' . $_SESSION['empl']['bagian'] . '\'' . "\r\n" . '            order by tahunbudget desc' . "\r\n" . '        ';
$opttahunbudget = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opttahunbudget .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

if ($_SESSION['language'] == 'EN') {
	$dd = 'namaakun1 as namaakun';
}
else {
	$dd = 'namaakun as namaakun';
}

$str = 'select noakun,' . $dd . ' from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1 order by noakun' . "\r\n" . '                    ';
$optnoakun = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optnoakun .= '<option value=\'' . $bar->noakun . '\'>' . $bar->noakun . ' - ' . $bar->namaakun . '</option>';
	$noakun[$bar->noakun] = $bar->namaakun;
}

OPEN_BOX('', '<b>' . $_SESSION['lang']['budget'] . ' ' . $_SESSION['lang']['departemen'] . '</b>');
echo '<table cellspacing=1 border=0>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td>:</td><td>' . "\r\n" . '        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress="return angka_doang(event);" maxlength=4 style=width:150px; /></td></tr>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['departemen'] . '</td><td>:</td><td>' . "\r\n" . '        <select id=departemen name=departemen style=\'width:150px;\'><option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>' . $optdepartemen . '</select></td></tr>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['noakun'] . '</td><td>:</td><td>' . "\r\n" . '        <select id=noakun name=noakun style=\'width:150px;\'><option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>' . $optnoakun . '</select></td></tr>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['keterangan'] . ' </td><td>:</td><td>' . "\r\n" . '        <input type=text class=myinputtext id=keterangan name=keterangan maxlength=100 style=width:150px; value="' . $keterangan . '"/></td></tr>' . "\r\n\r\n" . '   <tr><td>' . $_SESSION['lang']['fisik'] . ' </td><td>:</td><td>' . "\r\n" . '        <input type=text class=myinputtextnumber id=fisik name=fisik  style=width:150px; value=\'0\' okeypress="return angka_doang(event);"></td></tr>' . "\r\n" . '            ' . "\r\n" . '   <tr><td>' . $_SESSION['lang']['satuan'] . ' </td><td>:</td><td>' . "\r\n" . '        <input type=text class=myinputtext id=satuanf name=satuanf maxlength=10 style=width:150px; onkeypress="return tanpa_kutip(event);"></td></tr>' . "\r\n\r\n" . '    <tr><td>' . $_SESSION['lang']['alokasibiaya'] . '</td><td>:</td><td>' . "\r\n" . '        <select name=alokasi id=alokasi style=\'width:150px;\'><option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>' . $optalokasi . '</select></td></tr>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['jumlahpertahun'] . ' </td><td>:</td><td>' . "\r\n" . '        <input type=text class=myinputtext id=jumlahbiaya name=jumlahbiaya onkeypress="return angka_doang(event);" maxlength=20 style=width:150px; />(Rp)</td></tr>' . "\r\n\r\n\r\n" . '    <tr><td colspan=3>' . "\r\n" . '        <button class=mybutton id=simpan name=simpan onclick=simpan()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n" . '        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >' . "\r\n" . '    </td></tr></table><br>';
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 0;
$frm .= 1;
$frm .= 1;
$frm .= 1;
$hfrm[0] = $_SESSION['lang']['list'];
$hfrm[1] = $_SESSION['lang']['tutup'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
