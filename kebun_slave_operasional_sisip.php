<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/kebun_operasional.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$notrans = $_GET['notransaksi'];
$kodeorg = $_GET['kodeorg'];
OPEN_BOX();
echo '<fieldset style="float: left;">';
echo '<legend><b>'.$kodeorg.' - '.$notrans.'</b></legend>';
echo '<table cellspacing="1" border="0" >';
echo "<tr class=myinputtext>\r\n        <td>".$_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['sisip']."</td>\r\n        <td><input type='text' id='jumlah' name='jumlah' class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=30 style='height:20px; width:150px;' /></td>\r\n      </tr>    \r\n      <tr class=myinputtext>\r\n        <td><label>".$_SESSION['lang']['penyebab'].' '.$_SESSION['lang']['sisip']."</label></td>\r\n        <td><textarea rows=2 style='width:150px' id='penyebab' name='penyebab' onkeypress=\"return tanpa_kutip(event);\" /></textarea></td>\r\n      </tr>    \r\n        <tr class=myinputtext>\r\n        <td colspan=2>\r\n            <hidden id='notrans' name='notrans' value='".$notrans."'/>\r\n            <hidden id='kodeorg' name='kodeorg' value='".$kodeorg."'/>\r\n            <hidden id='progress' name='progress' value=''/>\r\n            <button class=mybutton id='simpan' onclick=saveSisip()>".$_SESSION['lang']['save']."</button>\r\n        </td>\r\n        </tr>      ";
echo '</table>';
CLOSE_BOX();

?>