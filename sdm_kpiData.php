<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/kpiData.js'></script>\r\n<script language=javascript1.2 src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/zReport.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['kpi'].' '.$_SESSION['lang']['input'].' '.$_SESSION['lang']['data']);
echo "<fieldset style='width:300px;'>\r\n             <legend>".$_SESSION['lang']['form']."</legend>\r\n              ".$_SESSION['lang']['tahun'].":<input type=text id=tahun class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlenngth=4 size=8>\r\n              <button class=mybutton onclick=getKPIdata()>".$_SESSION['lang']['preview']."</button>  \r\n               <button onclick=\"zExcel(event,'sdm_slave_kpiData.php','##tahun')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>   \r\n         </fieldset>";
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n          <div id=container></div>\r\n          </fieldset>";
CLOSE_BOX();
echo close_body();

?>