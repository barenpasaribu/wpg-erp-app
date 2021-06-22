<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript1.2 src='js/pabrik_5ketetapansuhu.js'></script>\r\n\r\n";
OPEN_BOX('', 'Sub Unit Analisa');
$optPabrik = '<option value="">'.$_SESSION['lang']['pilihdata'].'</option>>';
$sOpt = selectQuery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='PABRIK' AND kodeorganisasi LIKE'".$_SESSION['empl']['lokasitugas']."'");
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    $optPabrik .= '<option value='.$rOpt['kodeorganisasi'].'>'.$rOpt['namaorganisasi'].'</option>';
}
echo "<fieldset style='width:500px'>";
if ('ID' === $_SESSION['language']) {
    echo '<legend>'.$_SESSION['lang']['form'].'</legend>';
} else {
    echo '<legend>Tank Temperature</legend>';
}

echo "<table border=0 cellpadding=1 cellspacing=1>\r\n 
                
            <tr>\r\n                    <td>Nama Sub Unit</td> \r\n                    <td>:</td>\r\n                    <td><input type=text id=subunit value=''><input type=hidden id=id value=''></td>\r\n            </tr>\r\n\r\n           
            <tr><td colspan=2></td>\r\n                    <td colspan=3>\r\n                            <button class=mybutton onclick=simpanSubUnit()>Simpan</button>\r\n                    </td>\r\n            </tr>\r\n\r\n    </table></fieldset>\r\n                    <input type=hidden id=method value='insert'>";
echo "<fieldset style='width:500px'>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadDataSubunit()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>