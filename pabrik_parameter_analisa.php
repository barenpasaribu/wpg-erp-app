<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript1.2 src='js/pabrik_5ketetapansuhu.js'></script>\r\n\r\n";
OPEN_BOX('', 'Parameter Analisa');
$optSubunit = '<option value="">'.$_SESSION['lang']['pilihdata'].'</option>>';
$sOpt = selectQuery($dbname, 'pabrik_subunit_analisa', 'id,subunit', "");
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    $optSubunit .= '<option value='.$rOpt['id'].'>'.$rOpt['subunit'].'</option>';
}
echo "<fieldset style='width:500px'>";
if ('ID' === $_SESSION['language']) {
    echo '<legend>'.$_SESSION['lang']['form'].'</legend>';
} else {
    echo '<legend>Tank Temperature</legend>';
}

echo "<table border=0 cellpadding=1 cellspacing=1>\r\n 
                  <tr>\r\n                    <td>Sub Unit</td>\r\n                    <td>:</td>\r\n                    <td><select id=subunit name=subunit style=width:150px >".$optSubunit."</select></td>\r\n            </tr>\r\n 
            <tr>\r\n                    <td>Parameter</td> \r\n                    <td>:</td>\r\n                    <td><input type=text id=parameter value=''></td>\r\n            </tr>\r\n\r\n 
            <tr>\r\n                    <td>Satuan</td> \r\n                    <td>:</td>\r\n                    <td><input type=text id=satuan value=''></td>\r\n            </tr>\r\n\r\n 
            <tr>\r\n                    <td>Standar</td> \r\n                    <td>:</td>\r\n                    <td><input type=text id=standar value=''></td>\r\n            </tr>\r\n\r\n           
            <tr><td colspan=2></td>\r\n                    <td colspan=3>\r\n                            <button class=mybutton onclick=simpanParameter()>Simpan</button>\r\n                    </td>\r\n            </tr>\r\n\r\n    </table></fieldset>\r\n                    <input type=hidden id=id value=''><input type=hidden id=method value='insert'>";
echo "<fieldset style='width:500px'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadDataParameter()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>