<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/konversi.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend><b>' . $_SESSION['lang']['uomconversion'] . '</b></legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '    <td>' . $_SESSION['lang']['materialname'] . '</td><td><span id=kodebarang></span><input type=text id=namadisabled size=50 class=myinputtext disabled>' . "\r\n\t\t" . '<img src=images/search.png class=dellicon title=\'' . $_SESSION['lang']['find'] . '\' onclick="searchBarang(\'' . $_SESSION['lang']['findmaterial'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findmaterial'] . '</legend>Find<input type=text class=myinputtext id=namabrg><button class=mybutton onclick=findBarang()>Find</button></fieldset><div id=container></div>\',event);">' . "\r\n\t\t" . '</td>' . "\r\n\t" . ' </tr> ' . "\r\n\t" . ' </table>' . "\r\n" . '     </fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend><b>' . $_SESSION['lang']['newconversion'] . ':</b></legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['materialname'] . ': <b><span id=captionbarang></span></b><br>' . "\r\n\t" . ' ' . $_SESSION['lang']['smallestuom'] . ': <b><span id=captionsatuan></span></b><br>' . "\r\n\t" . ' ' . $_SESSION['lang']['uomsource'] . ' 1<input type=text class=myinputtext id=satuansource disabled size=10 maxlength=10 onkeypress="return tanpa_kutip(event);">' . "\r\n\t" . ' =<input type=text class=myinputtextnumber id=jumlah size=8 maxlength=8 onkeypress="return angka_doang(event);">' . "\r\n" . '         ' . $_SESSION['lang']['satuan'] . '<input type=text class=myinputtext id=satuandest size=10 maxlength=10 onkeypress="return tanpa_kutip(event);">' . "\r\n\t" . ' ' . $_SESSION['lang']['keterangan'] . ' <input type=text class=myinputtext id=keterangan size=25 maxlength=30 onkeypress="return tanpa_kutip(event);">' . "\r\n" . '     <input type=hidden value=insert id=method>' . "\r\n\t" . ' <button class=mybutton onclick=simpanKonversi()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=batalKonversi()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend><b>' . $_SESSION['lang']['conversionlist'] . '</b></legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['materialname'] . ': <b><span id=captionbarang1></span></b><br>' . "\r\n\t" . ' ' . $_SESSION['lang']['smallestuom'] . ': <b><span id=captionsatuan1></span></b>' . "\r\n\t" . ' <table class=sortable cellspacing=1 border=0>' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['uomsource'] . '</td>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['uomdestination'] . '</td>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t" . ' <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n\t" . ' <td></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=containersatuan>' . "\r\n\r\n\t" . ' </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>' . "\r\n" . '     </fieldset>';

if ($_SESSION['language'] == 'EN') {
	$zz = 'kelompok1 as kelompok';
}
else {
	$zz = 'kelompok';
}

$str = 'select kode,' . $zz . ' from ' . $dbname . '.log_5klbarang order by kelompok';
$res = mysql_query($str);
$optkelompok = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	$optkelompok .= '<option value=\'' . $bar->kode . '\'>' . $bar->kelompok . ' [ ' . $bar->kode . ' ] </option>';
}

echo '<fieldset>' . "\r\n" . '     <legend><b>' . $_SESSION['lang']['daftarbarang'] . '</b></legend>' . "\r\n" . '      ' . $_SESSION['lang']['pilihdata'] . ' <select id=kelompok onchange=ambilBarang(this.options[this.selectedIndex].value)>' . $optkelompok . '</select> ' . "\r\n" . '     <div style=\'height:300px;width:600px;overflow:scroll\'>' . "\r\n" . '     <table class=data cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td class=firsttd>No.</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['ke'] . ' ' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '         <td>Vol</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n\t" . ' <tbody id=containerdetail>';
$str = 'select a.*,b.namabarang,b.satuan as satuanori from ' . $dbname . '.log_5stkonversi a' . "\r\n" . '      left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '         <td class=firsttd>' . $no . '</td>' . "\r\n" . '         <td>' . $bar->kodebarang . '</td>' . "\r\n" . '         <td>' . $bar->namabarang . '</td>' . "\r\n" . '         <td>' . $bar->satuanori . '</td>' . "\r\n" . '         <td>' . $bar->satuankonversi . '</td>' . "\r\n" . '         <td align=right>' . $bar->jumlah . '</td>' . "\r\n" . '         </tr>';
}

echo '</tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></div>' . "\r\n" . '     </fieldset>';
CLOSE_BOX();
echo close_body();

?>
