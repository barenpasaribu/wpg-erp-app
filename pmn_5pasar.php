<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<link rel="stylesheet" type="text/css" href="style/zTable.css">' . "\n" . '<script language=javascript1.2 src=\'js/pmn_5pasar.js\'></script>' . "\n";
include 'master_mainMenu.php';
OPEN_BOX('', '');
echo '<fieldset style=\'width:500px;\'>' . "\n" . '    <legend><b>Pasar <span id=modeStr>: Add Mode</span></b></legend>' . "\n\t" . '<input type=hidden id=idPasar>' . "\n\t" . '<input type=hidden id=mode value=insert>' . "\n" . '    <table>' . "\n" . '        <tr><td>Nama Pasar</td>' . "\n" . '        <td><input type=text id=namapasar size=45 maxlength=45 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\n" . '    </table>' . "\n" . '    <input type=hidden id=method value=\'insert\'>' . "\n" . '    <button class=mybutton onclick=simpanDep()>' . $_SESSION['lang']['save'] . '</button>' . "\n\t" . '<button class=mybutton onclick=addMode() id=addModeBtn disabled>' . $_SESSION['lang']['addmode'] . '</button>' . "\n" . '</fieldset>';
echo open_theme($_SESSION['lang']['list']);
$str1 = 'select * from ' . $dbname . '.pmn_5pasar' . "\n" . '        order by namapasar';
$res1 = mysql_query($str1);
echo '<table class=sortable cellspacing=1 border=0 style=\'width:300px;\'>' . "\n\t" . '     <thead>' . "\n\t\t" . ' <tr class=rowheader><td>Pasar</td><td></td></tr>' . "\n\t\t" . ' </thead>' . "\n\t\t" . ' <tbody id=container>';

while ($bar1 = mysql_fetch_object($res1)) {
	echo '<tr class=rowcontent>' . "\n" . '            <td align=center>' . $bar1->namapasar . '</td>' . "\n" . '            <td>' . "\n" . '                   <img src=images/skyblue/edit.png class=zImgBtn  caption=\'Edit\' onclick="editField(' . $bar1->id . ',\'' . $bar1->namapasar . '\');">' . "\n" . '                   </td></tr>';
}

echo "\t" . ' ' . "\n\t\t" . ' </tbody>' . "\n\t\t" . ' <tfoot>' . "\n\t\t" . ' </tfoot>' . "\n\t\t" . ' </table>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
