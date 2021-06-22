<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "\r\n\r\n" . '<script language=javascript1.2 src=js/borong_panen.js></script>' . "\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n\r\n";
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM ' . $dbname . '.organisasi where tipe=\'AFDELING\' and induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' ORDER BY kodeorganisasi';

exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optOrg .= '<option value=' . $data['kodeorganisasi'] . '>' . $data['namaorganisasi'] . '</option>';
}

$optws = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
echo "\r\n\r\n";
echo '<fieldset style=\'width:275px;\'>' . "\r\n\t" . '  <legend><b>' . $_SESSION['lang']['borongpanen'] . '</b></legend><table>' . "\r\n" . '      <tr><td style=\'width:90px;\'>' . $_SESSION['lang']['budgetyear'] . '<td style=\'width:10px;\'>:</td></td><td><input type=text id=tahunbudget size=10 onkeypress="return angka(event,\'0123456789\');validatefn(event);" class=myinputtext maxlength=4 style="width:150px;"></td></tr>' . "\r\n\t\t" . ' <tr><td>' . $_SESSION['lang']['afdeling'] . '</td><td>:</td><td><select id=kodeorg name=kodeorg style="width:150px;">' . $optOrg . '</select></td></tr>' . "\r\n\t\t" . ' <tr><td>' . $_SESSION['lang']['siapborong'] . '</td><td>:</td><td><input type=text class=myinputtextnumber id=sb name=sb onkeypress="return angka_doang(event);" style="width:150px;"  /></td></tr>' . "\r\n\t\t" . ' <tr><td>' . $_SESSION['lang']['lebihborong'] . '</td><td>:</td><td><input type=text class=myinputtextnumber id=lb name=lb onkeypress="return angka_doang(event);" style="width:150px;"  /></td></tr>' . "\r\n" . '     </table> ' . "\r\n\t" . ' <table>' . "\r\n\t" . '  <tr>' . "\r\n\t\t" . ' <td style=\'width:103px;\'></td>' . "\r\n\t\t\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t\t\t" . '<input type=hidden id=oldtahunbudget value=\'insert\'>' . "\r\n\t\t\t" . '<input type=hidden id=oldkodeorg value=\'insert\'>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t\t" . ' <button class=mybutton onclick=simpanbor()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t\t\t" . ' <button class=mybutton onclick=batalbor()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t\t" . ' <td>' . "\r\n\t" . '  <tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' </fieldset>';
echo open_theme($_SESSION['lang']['datatersimpan']);
echo '<div id=container>';
echo '<table class=sortable cellspacing=1 border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n\t\t" . '     <td style=\'width:5px\'>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n\t\t\t" . ' <td style=\'width:100px;\'>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n\t\t\t" . ' <td style=\'width:100px\'>' . $_SESSION['lang']['afdeling'] . '</td>' . "\r\n\t\t\t" . ' <td style=\'width:100px\'>' . $_SESSION['lang']['siapborong'] . '</td>' . "\r\n\t\t\t" . ' <td style=\'width:100px\'>' . $_SESSION['lang']['lebihborong'] . '</td>' . "\r\n\t\t\t" . ' <td style=\'width:30px;\'>' . $_SESSION['lang']['edit'] . '</td></tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=\'containerData\'><script>loadData()</script>';
echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo '</div>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
