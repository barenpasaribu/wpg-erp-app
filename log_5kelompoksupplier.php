<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/klsupplier.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$tipe = 'SUPPLIER';
$str1 = 'select max(kode) as kode from ' . $dbname . '.log_5klsupplier where tipe=\'' . $tipe . '\'';
$res1 = mysql_query($str1);

while ($bar1 = mysql_fetch_object($res1)) {
	$kode = $bar1->kode;
}

$kode = substr($kode, 1, 5);
$newkode = $kode + 1;

switch ($newkode) {
case $newkode < 10:
	$newkode = '00' . $newkode;
	break;
case $newkode > 10:
	$newkode = '0' . $newkode;
	break;
case open_body():
	$newkode = '0' . $newkode;
	break;
}

$newkode = 'S' . $newkode;
echo '<u><b><font face="Verdana" size="4" color="#000080">';
echo $_SESSION['lang']['suppliergroup'];
echo '</font></b></u>' . "\r\n" . '<fieldset>' . "\r\n" . '        <legend>' . "\r\n" . '                ';
echo $_SESSION['lang']['input'] . ' ' . $_SESSION['lang']['suppliergroup'];
echo '        </legend>' . "\r\n" . '<table>' . "\r\n" . '    <tr><td>';
echo $_SESSION['lang']['Type'];
echo '</td><td><select id=tipe onchange="getCodeNumber(this.options[this.selectedIndex].value)"><option value=SUPPLIER>Supplier</option><option value=KONTRAKTOR>Contractor</option></select></td></tr>' . "\t\r\n" . '        <tr><td>';
echo $_SESSION['lang']['kode'];
echo '</td><td><input type=text disabled value=\'';
echo $newkode;
echo '\' class=myinputtext id=kodespl onkeypress="return tanpa_kutip(event);" maxlength=10 size=10></td></tr>' . "\r\n" . '        <tr><td>';
echo $_SESSION['lang']['namakelompok'];
echo '</td><td><input type=text class=myinputtext id=kelompok onkeypress="return tanpa_kutip(event);" maxlength=40 size=40></td></tr>' . "\r\n";

if ($_SESSION['language'] == 'EN') {
	$zz = 'namaakun1 as namaakun';
}
else {
	$zz = 'namaakun';
}

$str = 'select noakun,' . $zz . ' from ' . $dbname . '.keu_5akun where detail=1 and (kasbank=2 or kasbank=3)';
$res = mysql_query($str);
$opt = '';

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->noakun . '\'>' . $bar->namaakun . '</option>';
}

echo ' <tr><td>' . $_SESSION['lang']['noakun'] . '</td><td><select id=akun>' . $opt . '</select></td></tr>';
echo '<input type=hidden value=\'insert\' id=method>' . "\r\n" . '</table>' . "\r\n" . '<button class=mybutton onclick=saveKelSup()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '<button class=mybutton onclick=cancelKelSup()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '        <legend>';
echo $_SESSION['lang']['list'] . ' ' . $_SESSION['lang']['suppliergroup'];
echo '</legend>' . "\r\n" . '        <div style=\'width:100%;overflow:scroll;height:300px;\'>' . "\r\n" . '        <table class=sortable cellspacing=1 border=0>' . "\r\n" . '                <thead>' . "\r\n" . '                        <tr>' . "\r\n" . '                                <td>';
echo $_SESSION['lang']['no'];
echo '.</td>' . "\r\n" . '                                <td>';
echo $_SESSION['lang']['kode'];
echo '</td>' . "\r\n" . '                                <td>';
echo $_SESSION['lang']['namakelompok'];
echo '</td>' . "\r\n" . '                                <td>';
echo $_SESSION['lang']['Type'];
echo '</td>' . "\r\n" . '                                <td>';
echo $_SESSION['lang']['noakun'];
echo '</td>' . "\r\n" . '                                <td></td>' . "\r\n" . '                        </tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody id=container>' . "\r\n";
$str = ' select * from ' . $dbname . '.log_5klsupplier where tipe=\'' . $tipe . '\' order by kelompok';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '                      <td>' . $no . '</td>' . "\r\n" . '                      <td>' . $bar->kode . '</td>' . "\r\n" . '                          <td>' . $bar->kelompok . '</td>' . "\r\n" . '                          <td>' . $bar->tipe . '</td>' . "\r\n" . '                          <td>' . $bar->noakun . '</td>' . "\r\n" . '                          <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delKlSupplier(\'' . $bar->kode . '\');"></td>' . "\r\n" . '                          <td><img src=images/application/application_edit.png class=resicon  title=\'Update\' onclick="editKlSupplier(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->tipe . '\',\'' . $bar->noakun . '\');"></td>' . "\r\n" . '                         </tr>';
}

echo "\t\t\t\r\n" . '                </tbody>' . "\r\n" . '                <tfoot>\'</tfoot>' . "\r\n" . '        </table>' . "\r\n" . '        </div>' . "\r\n" . '</fieldset>' . "\t\r\n";
CLOSE_BOX();
echo close_body();

?>
