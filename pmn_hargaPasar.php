<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zLib.php';
include 'lib/zFunction.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/pmn_hargapasar.js\'></script>' . "\r\n";
$arr = '##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses##status##catatan';
include 'master_mainMenu.php';
OPEN_BOX();
$optBrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optKodeSat = $optKode = $optGoldar = $optBrg;
$sBrng = 'select distinct kodebarang,namabarang from ' . $dbname . '.log_5masterbarang where kelompokbarang like \'400%\' order by namabarang asc';

#exit(mysql_error($conn));
($qBrng = mysql_query($sBrng)) || true;

while ($rBarang = mysql_fetch_assoc($qBrng)) {
	$optBrg .= '<option value=\'' . $rBarang['kodebarang'] . '\'>' . $rBarang['namabarang'] . '</option>';
}

$sData = 'select distinct kode  from ' . $dbname . '.setup_matauang order by kode asc';

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$optKode .= '<option value=\'' . $rData['kode'] . '\'>' . $rData['kode'] . '</option>';
}

$arrenum = makeOption($dbname, 'pmn_5pasar', 'id,namapasar');

foreach ($arrenum as $key => $val) {
	$optGoldar .= '<option value=\'' . $key . '\'>' . $val . '</option>';
}

$arrSatuan = array('KG', 'TON');

foreach ($arrSatuan as $der) {
	$optKodeSat .= '<option value=\'' . $der . '\'>' . $der . '</option>';
}

$optStatus = getEnum($dbname, 'pmn_hargapasar', 'status');

foreach ($optStatus as $key => $val) {
	$optStatus[$key] = ucfirst($val);
}

echo '<fieldset style=width:250px>' . "\r\n" . '     <legend>' . $_SESSION['lang']['hargapasar'] . '</legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=tglHarga onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style="width:150px;" /></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n\t" . '   <td><select id=kdBarang style="width:150px;">' . $optBrg . '</select></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '   <td><select id=satuan style="width:150px;">' . $optKodeSat . '</select></td>' . "\r\n\t" . ' </tr>' . "\t\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['pasar'] . '</td>' . "\r\n\t" . '   <td><select id=idPasar style="width:150px;">' . $optGoldar . '</select></td>' . "\r\n\t" . ' </tr>' . "\t" . ' ' . "\r\n\t" . '  <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t" . '   <td><select id=idMatauang style="width:150px;">' . $optKode . '</select></td>' . "\r\n\t" . ' </tr> ' . "\r\n" . '          <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=hrgPasar onkeypress="return angka_doang(event);" style="width:150px;"  /> </td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t\t" . '<td>' . makeElement('status', 'select', '', array('style' => 'width:300px'), $optStatus) . '</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['catatan'] . '</td>' . "\r\n\t\t" . '<td>' . makeElement('catatan', 'text', '', array('style' => 'width:300px')) . '</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden value=insert id=proses>' . "\r\n\t" . ' <button class=mybutton onclick=saveFranco(\'pmn_slave_hargapasar\',\'' . $arr . '\')>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelIsi()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset><input type=\'hidden\' id=idFranco name=idFranco />';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset  style=width:650px><legend>' . $_SESSION['lang']['list'] . '</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0><tr><td>' . $_SESSION['lang']['tanggal'] . ' : <input type=text class=myinputtext id=tglCri onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10  />';
echo '&nbsp;' . $_SESSION['lang']['komoditi'] . ' : <select id=kdBrgCari style="width:150px;">' . $optBrg . '</select>';
echo '&nbsp;' . $_SESSION['lang']['pasar'] . ' : <select id=idPsrCari style="width:150px;">' . $optGoldar . '</select><button class=mybutton onclick=cariTransaksi()>' . $_SESSION['lang']['find'] . '</button></td></tr></table>';
echo "\r\n" . '    <table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['pasar'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '       <td>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['catatan'] . '</td>' . "\r\n\t" . '   <td>&nbsp;</td>' . "\r\n\t" .'   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
