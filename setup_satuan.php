<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/satuan.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend><b>' . $_SESSION['lang']['satuan'] . '</b></legend>' . "\r\n\t" . ' Note:' . $_SESSION['lang']['uomnote'] . '<br>' . "\r\n\t" . ' <br>' . $_SESSION['lang']['satuan'] . '<b id=old></b><input type=text class=myinputtext id=satuan onkeypress="return tanpa_kutip(event);" size=10 maxlength=10>' . "\r\n\t" . '  ' . "\r\n\t" . '  <input type=hidden id=method value=insert>' . "\r\n\t" . '  <button class=mybutton onclick=saveSatuan()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '  <button class=mybutton onclick=cancelSatuan()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '       <legend><b>' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['list'] . '</b></legend>' . "\r\n" . '       <div style=\'width:100%; height:400px;overflow:auto;\'>';
$str = 'select * from ' . $dbname . '.setup_satuan order by satuan';
$res = mysql_query($str);
echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '      <thead>' . "\r\n\t" . '    <tr class=rowheader>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t" . ' ' . "\t" . 'No' . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t" . '    ' . $_SESSION['lang']['satuan'] . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . '</tr>' . "\r\n\t" . '  </thead>' . "\r\n\t" . '  <tbody id=container>' . "\r\n\t" . '  ';
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t" . ' ' . "\t" . $no . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . ' <td>' . "\r\n\t\t" . '    ' . $bar->satuan . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . '  <td>' . "\r\n\t\t" . '      <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->satuan . '\');"> ' . "\r\n\t\t\t" . '  <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delSatuan(\'' . $bar->satuan . '\');">' . "\r\n\t\t" . '  </td>' . "\t\t" . ' ' . "\r\n\t\t" . '</tr>';
}

echo '</tbody><tfoot></tfoot></table></div></fieldset>';
CLOSE_BOX();
echo close_body();

?>
