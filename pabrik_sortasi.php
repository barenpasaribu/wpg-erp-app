<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['sortasi'].' Buah</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script>\r\ntmblPilih='";
echo $_SESSION['lang']['proses'];
echo "';\r\ncanForm='";
echo $_SESSION['lang']['done'];
echo "';\r\n</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\" src=\"js/pabrik_sortasi.js\"></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<div id=\"action_list\">\r\n";
$optFraksi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sFraksi = 'select * from '.$dbname.'.pabrik_5fraksi order by kode';
$qFraksi = mysql_query($sFraksi);
while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
    $optFraksi .= '<option value='.$rFraksi['kode'].'>'.$rFraksi['keterangan'].'</option>';
}
echo "<table cellspacing=1 border=0 align=center>\r\n     <tr valign=middle>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n           <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n         <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['noTiket'].':<input type=text id=noTiketcr name=noTiketcr class=myinputtext onkeypress=return(tanpa_kutip)  style=width:150px; />&nbsp;';
echo '<button class=mybutton onclick=cariTiket()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n\r\n         </tr>\r\n         </table> </div>\r\n";
echo "<div id=\"listData\">\r\n";
echo "<fieldset style=width:auto;>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<div id=\"contain\" style=width:auto;>\r\n<script>loadData()</script>\r\n</div>\r\n</fieldset>\r\n\r\n";

echo "</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['form'];
echo "</legend>\r\n<div id=\"pilih\">\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['pilihTanggal'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false; \" size=\"10\" maxlength=\"4\" style=\"width:150px;\" /></td>\r\n\r\n<td colspan=\"3\"><div id=\"tmblPilih\"><button class=\"mybutton\" id=\"dtlAbn\" onclick=\"addData(0,0)\">";
echo $_SESSION['lang']['save'];
echo "</button></div></td>\r\n</tr>\r\n</table>\r\n</div>\r\n\r\n<div id=\"formInput\" style=\"display:none\">\r\n";
echo $_SESSION['lang']['noTiket'];
echo $_SESSION['lang']['cancel'];
echo "</div>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<br />\r\n<div id=\"showFormBwh\" style=\"display:none;\">\r\n    <fieldset>\r\n        <legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n        <div id=\"formDetail\" style=\" width:auto;overflow:auto;\"></div>\r\n    </fieldset>\r\n    <br />\r\n    <fieldset>\r\n        <legend>";
echo $_SESSION['lang']['data'];
echo " : <span id=\"tanggalForm\"></span></legend>\r\n        <div id=\"isiDetail\" style=\" width:auto;height:auto;overflow:auto;\">\r\n\r\n        </div>\r\n    </fieldset>\r\n</div>\r\n\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>