<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=\'JavaScript1.2\' src=\'js/supplier.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['supplier'] . '/' . $_SESSION['lang']['kontraktor'] . '</b>');
echo '<br>' . $_SESSION['lang']['nama'] . ':<input type=text class=myinputtext id=cari size=30 maxlength=30 onkeypress="return tanpa_kutip(event)">' . "\r\n\t" . '      <button class=mybutton onclick=findSupplier()>' . $_SESSION['lang']['find'] . '</button>';
echo '<fieldset>' . "\r\n\t" . '     <legend>' . $_SESSION['lang']['pilih'] . '</legend>' . "\r\n\t\t" . ' <div style=\'width=100%; height:200px;overflow:scroll\'>' . "\r\n\t" . '     <table class=sortable cellspacing=1 border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=header>' . "\r\n\t" . '     <td>' . $_SESSION['lang']['kodekelompok'] . '</td>' . "\r\n\t\t" . ' <td>Id.' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['alamat'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['cperson'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['kota'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['telp'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['fax'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['email'] . '</td>' . "\t\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['npwp'] . '</td>' . "\t" . ' ' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['plafon'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['akunpajak'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['noseripajak'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['namabank'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['norekeningbank'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['atasnama'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['nilaihutang'] . '</td>' . "\r\n\t\t" . ' </tr>' . "\r\n\t\t" . ' <tbody id=container>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot></tfoot>' . "\r\n\t\t" . ' </table>' . "\r\n\t\t" . ' </div>' . "\r\n\t\t" . ' </fieldset>' . "\r\n\t\t" . ' ';
CLOSE_BOX();
OPEN_BOX('', 'SUPPLIER/CONTRACTOR BANK ACCOUNTs');

if ($_SESSION['language'] == 'EN') {
	$zz = 'namaakun1 as namaakun';
}
else {
	$zz = 'namaakun';
}

//$str = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun where detail=1 and (noakun like \'211%\')';
$str = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun where detail=1 and kasbank=2'; //hutang umum - FA 20191006 utk CDS/LIBO
$res = mysql_query($str);
$opt = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->noakun . '\'>' . $bar->namaakun . '</option>';
}

//$str1 = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun where detail=1 and (noakun like \'212%\')';
$str1 = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun where detail=1 and kasbank=3'; //hutang pajak - FA 20191006 utk CDS/LIBO
$res1 = mysql_query($str1);
$opt1 = '<option value=\'\'></option>';

while ($bar1 = mysql_fetch_object($res1)) {
	$opt1 .= '<option value=\'' . $bar1->noakun . '\'>' . $bar1->namaakun . '</option>';
}

echo '<fieldset>' . "\r\n" . '      <legend>Form</legend>' . "\r\n\t" . '  <table>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['kode'] . '</td><td><input type=text class=myinputtext disabled id=idsupplier></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['namabank'] . '</td><td><input type=text class=myinputtext id=bank onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\t" . '  ' . "\r\n\t" . '  <tr>    ' . "\r\n\t" . '     <td>' . $_SESSION['lang']['noakun'] . '</td><td><select id=noakun>' . $opt . '</select></td> ' . "\r\n" . '                          <td>Bank Acc.No</td><td><input type=text class=myinputtext id=rek onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\t" . '  ' . "\r\n" . ' ' . "\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['namasupplier'] . '</td><td><input type=text class=myinputtext id=namasupplier onkeypress=' . "\r" . 'eturn tanpa_kutip(event);" size=30 maxlength=30 disabled></td>' . "\r\n\t" . '      <td>A/c on Bhf (Bank.A/N)</td><td><input type=text class=myinputtext id=an onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['noakun'] . '.' . $_SESSION['lang']['pajak'] . '</td><td><select id=akunpajak>' . $opt1 . '</select></td>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['noseripajak'] . '</td><td><input type=text class=myinputtext id=noseripajak onkeypress="return tanpa_kutip(event);" size=30 maxlength=30></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  <tr>' . "\r\n\t" . '      <td>' . $_SESSION['lang']['nilaihutang'] . '</td><td colspan=3><input type=text  onblur="change_number(this);"class=myinputtextnumber id=nilaihutang onkeypress="return angka_doang(event);" size=15 maxlength=15 value=0></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  </table>' . "\r\n\t" . '<button class=mybutton onclick=saveAkunSupplier()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cancelAkunSupplier()>' . $_SESSION['lang']['cancel'] . '</button>' . "\t" . '  ' . "\r\n\t" . '  </fieldset>';
CLOSE_BOX();
echo close_body();

?>
