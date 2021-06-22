<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=js/log_5masterfranco.js></script>' . "\r\n";
$arr = '##idFranco##nmFranco##almtFranco##cntcPerson##hdnPhn##method';
include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend>Master Franco</legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>Franco Name</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=nmFranco name=nmFranco onkeypress="return tanpa_kutip(event);" style="width:150px;" maxlength=100 /></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['alamat'] . '</td>' . "\r\n\t" . '   <td><textarea id=almtFranco name=almtFranco></textarea></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>Contac Person</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=cntcPerson name=cntcPerson onkeypress="return tanpa_kutip(event);" style="width:150px;" /> </td>' . "\r\n\t" . ' </tr>' . "\t\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['telp'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=hdnPhn name=hdnPhn  onkeypress="return angka_doang(event);" style="width:150px;" maxlength=20></td>' . "\r\n\t" . ' </tr>' . "\t" . ' ' . "\r\n\t" . '  <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '   <td><input type=\'checkbox\' id=statFr name=statFr />' . $_SESSION['lang']['tidakaktif'] . '</td>' . "\r\n\t" . ' </tr> ' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden value=insert id=method>' . "\r\n\t" . ' <button class=mybutton onclick=saveFranco(\'log_slave_5masterfranco\',\'' . $arr . '\')>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelIsi()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset><input type=\'hidden\' id=idFranco name=idFranco />';
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from ' . $dbname . '.setup_franco order by id_franco desc';
$res = mysql_query($str);
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . '</legend><table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n\t" . '   <td>Nama Franco</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['alamat'] . '</td>' . "\r\n\t" . '   <td>Kontak Person</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['telp'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
