<?php


require_once 'config/connection.php';
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/setup_fingerprint.js\'></script>' . "\r\n";
$optKar = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKar = 'select  karyawanid, namakaryawan from ' . $dbname . '.datakaryawan where lokasitugas like \'%HO\' and ((tanggalkeluar is NULL) or (tanggalkeluar is NULL))' . "\r\n" . '    order by namakaryawan';

#exit(mysql_error($conn));
($qKar = mysql_query($sKar)) || true;

while ($rKar = mysql_fetch_assoc($qKar)) {
	$optKar .= '<option value=\'' . $rKar['karyawanid'] . '\'>' . $rKar['namakaryawan'] . '</option>';
}

include 'master_mainMenu.php';
OPEN_BOX('');
echo '<fieldset style=\'width:300px;\'><table>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['namakaryawan'] . '</td><td><select id=karyawanid style=width:150px>' . $optKar . '</select></td></tr>' . "\r\n" . '     <tr><td>PIN Fingerprint</td><td><input type=text id=pin maxlength=80 style=width:150px onkeypress=\'return angka_doang(event);\' class=myinputtext></td></tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t" . ' <button class=mybutton onclick=simpanJ()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelJ()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo open_theme('');
echo '<div>';
echo '<table class=sortable cellspacing=1 border=0 style=\'width:300px;\'>' . "\r\n" . '    <thead>' . "\r\n" . '        <tr class=rowheader>' . "\r\n" . '        <td style=\'width:150px;\'>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n" . '        <td>PIN Fingerprint</td>' . "\r\n" . '        <td style=\'width:30px;\'>*</td></tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody id=container>';
echo '<script>loadData()</script>';
echo ' </tbody>' . "\r\n" . '    <tfoot>' . "\r\n" . '    </tfoot>' . "\r\n" . '    </table>';
echo '</div>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
