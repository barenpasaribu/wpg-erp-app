<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=\"js/zTools.js\"></script>\r\n<script language=javascript1.2>\r\nfunction hapusGaji()\r\n{\r\n    periode=getValue('periode');\r\n    karyawanid=getValue('karyawanid');\r\n    komponen=getValue('komponen');\r\n    tipekaryawan=getValue('tipekaryawan');\r\n    param='periode='+periode+'&karyawanid='+karyawanid+'&komponen='+komponen+'&tipekaryawan='+tipekaryawan;\r\n    tujuan='sdm_slave_hapusSlyip.php';\r\n    if(confirm(\"Delete ?\"))\r\n       post_response_text(tujuan, param, respog);\t\t\r\n\r\n\t\r\n\tfunction respog()\r\n\t{\r\n              if(con.readyState==4)\r\n              {\r\n                if (con.status == 200) {\r\n                        busy_off();\r\n                        if (!isSaveResponse(con.responseText)) {\r\n                                alert('ERROR TRANSACTION,\\n' + con.responseText);\r\n                        }\r\n                        else {\r\n                                alert(con.responseText);\r\n\r\n                        }\r\n                    }\r\n                    else {\r\n                            busy_off();\r\n                           error_catch(con.status);\r\n                    }\r\n              }\t \r\n        }\r\n}\r\n</script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('');
$str = 'select distinct periode from '.$dbname.".sdm_5periodegaji \r\n     where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
$str = 'select namakaryawan,karyawanid,subbagian from '.$dbname.".datakaryawan \r\n    where lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";
$res = mysql_query($str);
$optkar = '<option value=all>'.$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optkar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' ['.$bar->subbagian.']</option>';
}
$str = 'select id,name from '.$dbname.'.sdm_ho_component order by name';
$res = mysql_query($str);
$optkom = '<option value=all>'.$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optkom .= "<option value='".$bar->id."'>".$bar->name.'</option>';
}
echo "<fieldset style='width:500px;'><legend>Delete Slyp</legend>\r\n    <table>\r\n     <tr><td>".$_SESSION['lang']['namakaryawan'].'</td><td><select id=karyawanid>'.$optkar."</select></td></tr>\r\n     <tr><td>".$_SESSION['lang']['periode'].'</td><td><select id=periode>'.$optper."</select></td></tr>\r\n     <tr><td>".$_SESSION['lang']['namakomponen'].'</td><td><select id=komponen>'.$optkom."</select></td></tr>  \r\n     <tr><td>".$_SESSION['lang']['sistemgaji']."</td><td><select id=tipekaryawan><option value='all'>".$_SESSION['lang']['all']."</option><option value='Bulanan'>".$_SESSION['lang']['bulanan']."</option><option value='Harian'>".$_SESSION['lang']['harian']."</option></select></td></tr>    \r\n     </table>\r\n\t\r\n\t <button class=mybutton onclick=hapusGaji()>".$_SESSION['lang']['delete']."</button>\r\n\t </fieldset>";
CLOSE_BOX();
echo close_body();

?>