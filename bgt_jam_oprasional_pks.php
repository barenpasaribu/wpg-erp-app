<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n\r\n" . '<script language=javascript1.2 src=js/bgt_jam_oprasional_pks.js></script>' . "\r\n\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n\r\n";
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM ' . $dbname . '.organisasi where tipe=\'PABRIK\' and kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\' ORDER BY kodeorganisasi';

exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optOrg .= '<option value=' . $data['kodeorganisasi'] . '>' . $data['namaorganisasi'] . '</option>';
}

$optws = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
echo "\r\n";
echo '<fieldset style=\'width:500px;\'>' . "\r\n\t" . '  <legend>Jam Operasional PKS</legend><table>' . "\r\n" . '      <tr><td>Tahun Budget</td><td>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress="return angka_doang(event);" style="width:200px;" maxlength=4 /></td></tr>' . "\r\n\t\t" . ' <tr><td>Kode PKS</td><td>:</td><td><select id=kdpks name=kdpks style="width:200px;">' . $optOrg . '</select></td></tr>' . "\r\n\t\t" . ' <tr><td>Jam Olah/Tahun</td><td>:</td><td><input type=text class=myinputtextnumber id=jamo name=jmo onkeypress="return angka_doang(event);" style="width:200px;"  /></td></tr>' . "\r\n\t\t" . ' <tr><td>Jam Breakdown/Tahun</td><td>:</td><td><input type=text class=myinputtextnumber id=jamb name=jamb onkeypress="return angka_doang(event);" style="width:200px;"  /></td></tr>' . "\r\n" . '     </table> ' . "\r\n\t" . ' <table>' . "\r\n\t" . '  <tr>' . "\r\n\t\t" . ' <td style=\'width:130px;\'></td>' . "\r\n\t\t\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t\t" . ' <button class=mybutton onclick=simpanpks()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t\t\t" . ' <button class=mybutton onclick=batalpks()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t\t" . ' <td>' . "\r\n\t" . '  <tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' </fieldset>';
echo open_theme($_SESSION['lang']['datatersimpan']);
$str1 = 'select * from ' . $dbname . '.bgt_jam_operasioal_pks order by tahunbudget ';
$res1 = mysql_query($str1);
echo '<table class=sortable cellspacing=1 border=0 style=\'width:500px;\'>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n\t\t" . '     <td style=\'width:5px\'>No</td>' . "\r\n\t\t\t" . ' <td style=\'width:75px;\'>Tahun Budget</td>' . "\r\n\t\t\t" . ' <td style=\'width:75px\'>Kode PKS</td>' . "\r\n\t\t\t" . ' <td style=\'width:75px\'>Total Jam</td>' . "\r\n\t\t\t" . ' <td style=\'width:75px\'>Total Breakdown</td>' . "\r\n\t\t\t" . ' <td style=\'width:30px;\'>Aksi</td>' . "\r\n\t\t" . ' </tr>' . "\t\t" . ' ' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>';

while ($bar1 = mysql_fetch_object($res1)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td align=center>' . $no . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->tahunbudget . '</td>' . "\r\n\t\t\t" . '<td align=center>' . $bar1->millcode . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->jamolah . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->breakdown . '</td>' . "\t\t\t\r\n\t\t\t" . '<td align=center><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->tahunbudget . '\',\'' . $bar1->millcode . '\',\'' . $bar1->jamolah . '\',\'' . $bar1->breakdown . '\');"></td>' . "\r\n\t\t" . '</tr>';
}

echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
