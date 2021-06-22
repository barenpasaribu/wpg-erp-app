<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['lembur'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\">\r\n \r\nnmTmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nnmTmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n</script>\r\n<script language=\"javascript\" src=\"js/sdm_lembur.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n\r\n\r\n<div id=\"action_list\">\r\n";
for ($x = 0; $x <= 24; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$idOrg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$idOrg."' or induk='".$idOrg."' ORDER BY `namaorganisasi` ASC";
$query = mysql_query($sql);
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
echo "<table cellspacing=1 border=0>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['nm_perusahaan'].":<select id=kdOrgCr><option value=''></option>".$optOrg.'</select>&nbsp;';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariAsbn()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n\t </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"listData\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n\r\n<div id=\"contain\">\r\n<script>loadData();</script>\r\n</div>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px;\" ><option value=\"\">";
echo $_SESSION['lang']['pilihdata'];
echo '</option>';
echo $optOrg;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tglAbsen\" name=\"tglAbsen\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detailEntry\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<div id=\"addRow_table\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n<div id=\"detailIsi\">\r\n</div>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr><td id=\"tombol\">\r\n\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div><br />\r\n<br />\r\n<div style=\"overflow:auto; height:300px;\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['datatersimpan'];
echo "</legend>\r\n<table cellspacing='1' border='0' class='sortable'>\r\n<thead>\r\n <tr class=\"rowheader\">\r\n <td>No</td>\r\n    <td>";
echo $_SESSION['lang']['namakaryawan'];
echo "</td>\r\n \t<td>";
echo $_SESSION['lang']['tipelembur'];
echo "</td>\r\n  \t<td>";
echo $_SESSION['lang']['jamaktual'];
echo "</td>\r\n  \t<td style='display:none'>";
echo $_SESSION['lang']['uangmakan'];
echo "</td>\r\n    <td style='display:none'>";
echo $_SESSION['lang']['penggantiantransport'];
echo "</td>\r\n\t <td style='display:none'>";
echo $_SESSION['lang']['uangkelebihanjam'];
echo "</td>\r\n    <td>Action</td>\r\n    </tr>\r\n</thead>\r\n<tbody id=\"contentDetail\">\r\n\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();
echo "\r\n";

?>