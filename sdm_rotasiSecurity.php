<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/datakaryawan.js></script>\r\n<script language=javascript src='js/sdm_rotasiSecurity.js'></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
OPEN_BOX('', $_SESSION['lang']['rotasisecurity']);
echo '<fieldset><legend>'.$_SESSION['lang']['rotasisecurity']."</legend>\r\n        <fieldset>Apply only at the beginning of month</fieldset>\r\n      ".$_SESSION['lang']['caripadanama'].":<input type=text id=txtnama class=myinputtext onclick=\"return tanpa_kutip(event);\" size=25>\r\n\t  \r\n\t  ".$_SESSION['lang']['nik'].":<input type=text id=nik class=myinputtext onclick=\"return tanpa_kutip(event);\" size=10>\r\n\t  <button class=mybutton onclick=cariNama()>".$_SESSION['lang']['find']."</button>\r\n\t  <br>\r\n\t  <fieldset style='width:500px'>\r\n\t  <div id=container style='width:480px; height:400px; overflow:scroll'>\r\n\t  </div>\r\n\t  </fieldset>\r\n\t  </fieldset>";
CLOSE_BOX();
echo close_body();

?>