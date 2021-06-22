<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zMaster.js></script> \r\n<script language=javascript src=js/zSearch.js></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/lbm_main_pks.js'></script>\r\n<script language=javascript>\r\n\r\n</script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n<table>\r\n     <thead>\r\n     </thead>\r\n        <tbody>\r\n        <tr>\r\n            <td valign='top'>";
OPEN_BOX('', 'LBM-PKS');
echo '<fieldset><legend>'.$_SESSION['lang']['navigasi']."</legend>\r\n                 <div id='navcontainer' style='width:200px;height:500px;overflow:scroll;background-color:#FFFFFF;'>";
if ('ID' === $_SESSION['language']) {
    $x = readCountry('config/lbm_pks.lst');
} else {
    $x = readCountry('config/lbm_pks_en.lst');
}

foreach ($x as $bar => $val) {
    echo "<a onmouseover=ubah(this) onmouseout=ubah(this) style='font-size:10px;cursor:pointer;' onclick=\"lempar('".$val[1]."','".$val[2]."');\" title='".$val[2]."'>".$val[0].'</a><br>';
}
echo "</div>\r\n                    </fieldset>";
CLOSE_BOX();
echo '</td><td>';
OPEN_BOX('', '');
echo '<fieldset><legend>'.$_SESSION['lang']['form']."</legend>\r\n                 <div id='formcontainer' style='width:900px;height:150px;overflow:scroll'></div> \r\n                 </fieldset>";
CLOSE_BOX();
OPEN_BOX('', '');
echo '<fieldset><legend>'.$_SESSION['lang']['list']." <span id=isiJdlBawah></span></legend>\r\n                 <div id='reportcontainer' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n                    <div id='lyrSatu' style='overflow:auto; height:50%; max-width:100%;'>\r\n                    </div>\r\n                    <div id='lyrDua' style='overflow:auto; height:50%; max-width:100%;'>\r\n                    <div>\r\n                 </fieldset>";
CLOSE_BOX();
echo "</td>\r\n        </tr>\r\n        </tbody>\r\n     <tfoot>\r\n     </tfoot>\r\n     </table>";
echo close_body();

?>