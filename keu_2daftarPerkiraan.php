<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
echo open_body();
require_once 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
echo "<tr><td colspan=2></td>\r\n\t\t<td colspan=4>\r\n\t\t<button onclick=zPreview('keu_slave_2daftarPerkiraan','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>\r\n\t\t<button onclick=zExcel(event,'keu_slave_2daftarPerkiraan.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>\r\n\t\t<button onclick=zPdf('keu_slave_2daftarPerkiraan','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>\r\n\t\t</td>\r\n\t</tr>";
echo '</table></fieldset>';
CLOSE_BOX();
echo "\r\n\r\n";
OPEN_BOX();
echo "\r\n<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:400px;max-width:1220px'; >\r\n</div></fieldset>";
CLOSE_BOX();
echo close_body();

?>