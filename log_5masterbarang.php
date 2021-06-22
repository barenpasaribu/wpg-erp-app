<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/master_barang.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();

if ($_SESSION['language'] == 'EN') {
	$zz = 'kelompok1 as kelompok';
}
else {
	$zz = 'kelompok';
}

$str = 'select kode,' . $zz . ' from ' . $dbname . '.log_5klbarang order by kode asc';
$res = mysql_query($str);
$optkelompok = '<option value=\'\'></option>';
$optsearch = '<option value=All>' . $_SESSION['lang']['all'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optkelompok .= '<option value=\'' . $bar->kode . '\'>' . $bar->kelompok . ' [ ' . $bar->kode . ' ] </option>';
	$optsearch .= '<option value=\'' . $bar->kode . '\'>' . $bar->kelompok . ' [ ' . $bar->kode . ' ] </option>';
}

$str = 'select distinct satuan from ' . $dbname . '.setup_satuan order by satuan';
$res = mysql_query($str);
$optsatuan = '';

while ($bar = mysql_fetch_object($res)) {
	$optsatuan .= '<option value=\'' . $bar->satuan . '\'>' . $bar->satuan . '</option>';
}

echo '<fieldset>' . "\r\n\t" . '<legend>';
echo $_SESSION['lang']['materialmaster'];
echo '</legend>' . "\r\n" . '<table border="0" cellspacing="0">' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['materialgroupcode'];
echo '    </td>' . "\r\n" . '    <td><select id="kelompokbarang" onchange=getMaterialNumber(this.options[this.selectedIndex].value)>';
echo $optkelompok;
echo '</select></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['materialcode'];
echo '</td>' . "\r\n" . '    <td><input type=text  class=myinputtext  id="kodebarang" size=10></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['materialname'];
echo '</td>' . "\r\n\t" . '<td><input type="text"  class=myinputtext id="namabarang" size=45 maxlength=70 onkeypress="return tanpa_kutip(event)"></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n" . '    <td><select id="satuan">';
echo $optsatuan;
echo '</select></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['minstok'];
echo '</td>' . "\r\n\t" . '<td><input type="text"  class=myinputtextnumber id="minstok" value=0 size=4 maxlength=4 onkeypress="return angka_doang(event)"></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['nokartubin'];
echo '</td>' . "\r\n\t" . '<td><input type="text"  class=myinputtext id="nokartu" size=10 maxlength=10 onkeypress="return tanpa_kutip(event)"></td>' . "\r\n" . '  </tr>  ' . "\r\n" . '  <tr>' . "\r\n" . '    <td>';
echo $_SESSION['lang']['konversi'];
echo '</td>' . "\r\n" . '    <td><select id="konversi"><option value=1>Yes</option><option value=0>No</option></select></td>' . "\r\n" . '  </tr>  ' . "\r\n" . '  <input type=hidden value=\'insert\' id=method>' . "\r\n" . '</table>' . "\r\n" . '<button class=mybutton onclick=simpanBarangBaru()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '<button class=mybutton onclick=cancelBarang()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "\r\n" . '<img src=\'images/pdf.jpg\' title=\'PDF Format\' style=\'width:20px;height:20px;cursor:pointer\' onclick="masterbarangPDF(event)">&nbsp;' . "\r\n" . '<img src=\'images/printer.png\' title=\'Print Page\' style=\'width:20px;height:20px;cursor:pointer\' onclick=\'javascript:print()\'>' . "\r\n" . '<fieldset style=\'width:800px;background-color:#A9D4F4\'>' . "\r\n" . '<legend><b>' . $_SESSION['lang']['find'] . '</b></legend>' . "\r\n" . 'Text <input type=text id=txtcari class=myinputtext size=40 onkeypress="return tanpa_kutip(event);" maxlength=30>' . "\r\n" . $_SESSION['lang']['on'] . '\' <select id=optcari  onchange=getMaterialNumber(this.options[this.selectedIndex].value)>' . $optsearch . '</select>' . "\r\n" . '<button class=mybutton onclick=cariBarang()>' . $_SESSION['lang']['find'] . '</button>' . "\r\n" . '</fieldset>' . "\r\n" . '<div style=\'width:98%;overflow:scroll;height:230px;\'>' . "\r\n" . '<b id=caption></b>' . "\r\n" . '      <table cellspacing=1 border=0 class=sortable>' . "\r\n" . '      <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '  <td>No</td>' . "\r\n\t" . '  <td align=center>' . str_replace(' ', '<br>', $_SESSION['lang']['materialgroupcode']) . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['materialcode'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['materialname'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n\t" . '  <td align=center>' . str_replace(' ', '<br>', $_SESSION['lang']['minstok']) . '</td>' . "\r\n\t" . '  <td align=center>' . str_replace(' ', '<br>', $_SESSION['lang']['nokartubin']) . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['konversi'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['tidakaktif'] . '</td>' . "\t" . '  ' . "\r\n\t" . '  <td align=center>Detail<br>' . $_SESSION['lang']['photo'] . '</td>' . "\r\n\t" . '  <td></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . '  </thead>' . "\r\n\t" . '  <tbody id=container>' . "\r\n\t" . '  </tbody>' . "\r\n\t" . '  <tfoot>' . "\r\n\t" . '  </tfoot>' . "\r\n\t" . '  </table>' . "\r\n" . '</div>';
CLOSE_BOX();
echo close_body();

?>
