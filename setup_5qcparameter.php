<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/setup_5qcparameter.js\'></script>' . "\r\n";
$arrTipe = array('ANCAK', 'BUAH', 'PUPUK', 'TANAM', 'TBM', 'TM');
$optTipe = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

foreach ($arrTipe as $dtTipe) {
	$optTipe .= '<option value=\'' . $dtTipe . '\'>' . $dtTipe . '</option>';
}

$arr = '##tipeDt##idData##nmQc##klmpkQc##satuan##method';
include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <legend>QC Parameter</legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n\t" . '   <td><select id=tipeDt style="width:150px;" onchange=getData() >' . $optTipe . '</select></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>Id</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=idData name=idData onkeypress="return angka_doang(event);" style="width:150px;" /> </td>' . "\r\n\t" . ' </tr>' . "\t\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['nama'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=nmQc name=nmQc  onkeypress="return tanpa_kutip(event);" style="width:150px;" maxlength=45></td>' . "\r\n\t" . ' </tr>' . "\t" . ' ' . "\r\n\t" . '  <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kelompok'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=klmpkQc name=klmpkQc  onkeypress="return tanpa_kutip(event);" style="width:150px;" maxlength=45></td>' . "\r\n\t" . ' </tr>' . "\r\n" . '         <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=satuan name=satuan  onkeypress="return tanpa_kutip(event);" style="width:150px;" maxlength=15></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden value=insert id=method>' . "\r\n\t" . ' <button class=mybutton onclick=saveFranco(\'setup_slave_5qcparameter\',\'' . $arr . '\')>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelIsi()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset><input type=\'hidden\' id=idFranco name=idFranco />';
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from ' . $dbname . '.setup_franco order by id_franco desc';
$res = mysql_query($str);
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . '</legend><table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n\t" . '   <td>ID</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['nama'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kelompok'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
