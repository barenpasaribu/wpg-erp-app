<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/rencanaGis.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['input'] . ' ' . $_SESSION['lang']['jenis']);
echo '<fieldset style=\'width:500px;\'><table>' . "\r\n" . '     <tr><td>' . $_SESSION['lang']['kode'] . '</td><td><input type=text id=kode size=3  maxlength5 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['nama'] . '</td><td><input type=text id=nama size=45 maxlength=45 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\r\n" . '     </table>' . "\r\n\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t" . ' <button class=mybutton onclick=simpanDep()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelDep()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo open_theme($_SESSION['lang']['avaidepartement']);
$str1 = 'select * from ' . $dbname . '.rencana_gis_jenis order by kode';
$res1 = mysql_query($str1);
echo '<table class=sortable cellspacing=1 border=0 style=\'width:500px;\'>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader><td style=\'width:150px;\'>' . $_SESSION['lang']['kode'] . '</td><td>' . $_SESSION['lang']['nama'] . '</td><td style=\'width:30px;\'>*</td></tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>';

while ($bar1 = mysql_fetch_object($res1)) {
	echo '<tr class=rowcontent><td align=center>' . $bar1->kode . '</td><td>' . $bar1->namajenis . '</td><td><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->kode . '\',\'' . $bar1->namajenis . '\');"></td></tr>';
}

echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
