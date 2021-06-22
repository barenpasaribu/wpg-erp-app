<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/zMaster.js'></script> \r\n<script language=javascript src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<!--<script language=javascript src='js/zReport.js'></script>-->\r\n<script language=javascript src='js/lbm_main_procurement.js'></script>\r\n<script language=javascript src='js/log_2kalkulasi_stock.js'></script>\r\n\r\n<script language=javascript>\r\nfunction lempar(dest,title){\r\n    \tparam='judul='+title;\r\n\ttujuan=dest+'.php';\r\n        post_response_text(tujuan, param, respog);\r\n\tfunction respog()\r\n\t{\r\n          if(con.readyState==4)\r\n          {\r\n            if (con.status == 200) {\r\n                busy_off();\r\n                if (!isSaveResponse(con.responseText)) {\r\n                        alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                }\r\n                else {\r\n                        document.getElementById('formcontainer').innerHTML=con.responseText;\r\n                        document.getElementById('reportcontainer').innerHTML='';\r\n                        document.getElementById('isiJdlBawah').innerHTML=title;\r\n                        document.getElementById('lyrPertama').style.display='none';\r\n                        document.getElementById('lyrKedua').style.display='none';\r\n                        document.getElementById('mainPrint').style.display='block';\r\n                }\r\n            }\r\n            else {\r\n                    busy_off();\r\n                    error_catch(con.status);\r\n            }\r\n          }\t\r\n\t }        \r\n}\r\nfunction ubah(obj)\r\n{\r\n    if(obj.style.backgroundColor=='darkgreen'){\r\n      obj.style.backgroundColor='#FFFFFF';\r\n      obj.style.color='#000000';\r\n      obj.style.fontWeight='normal';\r\n    }\r\n    else{\r\n       obj.style.backgroundColor='darkgreen'; \r\n       obj.style.color='#FFFFFF';\r\n       obj.style.fontWeight='bolder';\r\n    }\r\n}\r\n\r\n</script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n<table>\r\n     <thead>\r\n     </thead>\r\n        <tbody>\r\n        <tr>\r\n            <td valign='top'>";
OPEN_BOX('', 'LBM-PROCUREMENT');
echo '<fieldset><legend>'.$_SESSION['lang']['navigasi']."</legend>\r\n                 <div id='navcontainer' style='width:285px;height:500px;overflow:scroll;background-color:#FFFFFF;'>";
$x = readCountry('config/lbm_proc.lst');
foreach ($x as $bar => $val) {
    echo "<a onmouseover=ubah(this) onmouseout=ubah(this) style='font-size:10px;cursor:pointer;' onclick=\"lempar('".$val[1]."','".$val[2]."');\" title='".$val[2]."'>".$val[0].'</a><br>';
}
echo "</div>\r\n                    </fieldset>";
CLOSE_BOX();
echo '</td><td>';
OPEN_BOX('', '');
echo '<fieldset><legend>'.$_SESSION['lang']['form']."</legend>\r\n                 <div id='formcontainer' style='width:900px;height:120px;overflow:scroll'></div> \r\n                 </fieldset>";
CLOSE_BOX();
OPEN_BOX('', '');
echo "<div id=mainPrint><fieldset><legend><span id=isiJdlBawah></span></legend>\r\n                 <div id='reportcontainer' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n                 </fieldset></div><div id=lyrPertama style=display:none;>\r\n                 <fieldset><legend><span id=isiJdlBawah1></span></legend>\r\n                 <div id='reportcontainer1' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n                 </fieldset>\r\n                 </div><div id=lyrKedua style=display:none;>\r\n                 <fieldset><legend><span id=isiJdlBawah2></span></legend>\r\n                 <div id='reportcontainer2' style='width:900px;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> \r\n                 </fieldset>\r\n                 </div>";
CLOSE_BOX();
echo "</td>\r\n        </tr>\r\n        </tbody>\r\n     <tfoot>\r\n     </tfoot>\r\n     </table>";
echo close_body();

?>