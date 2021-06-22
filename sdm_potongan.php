<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['potongan'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\">\r\nfunction add_new_data(){\r\n                document.getElementById('headher').style.display=\"block\";\r\n                document.getElementById('listData').style.display=\"none\";\r\n                document.getElementById('detailEntry').style.display=\"none\";\r\n                unlockForm();\t\r\n                document.getElementById('contentDetail').innerHTML='';\r\n                statFrm=0;\r\n}\r\nnmTmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n</script>\r\n<script language=\"javascript\" src=\"js/sdm_potongan.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
$optTipePot = $optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$idOrg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ($_SESSION['empl']['tipelokasitugas']=='KANWIL') {
    $sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where \r\n                  kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') \r\n                  ORDER BY `namaorganisasi` ASC";
    $sGet = 'select distinct periode from '.$dbname.".sdm_5periodegaji \r\n                   where kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') \r\n                   and sudahproses=0 and jenisgaji='H' order by periode desc";
} else {
    $sql = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY `namaorganisasi` ASC";
    $sGet = 'select distinct periode from '.$dbname.".sdm_5periodegaji \r\n                   where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
}

$query = mysql_query($sql);
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
$sTipePot = 'select distinct id,name from '.$dbname.".sdm_ho_component where name like 'Pot%' and id not in('5','8','9','55','56','66') order by name asc";
$qTipePot = mysql_query($sTipePot);
while ($rTipePot = mysql_fetch_assoc($qTipePot)) {
    $optTipePot .= "<option value='".$rTipePot['id']."'>".$rTipePot['name'].'</option>';
}
$optPeriode .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$qGet = mysql_query($sGet);
while ($rGet = mysql_fetch_assoc($qGet)) {
    $optPeriode .= '<option value='.$rGet['periode'].'>'.$rGet['periode'].'</option>';
}
echo "<table cellspacing=1 border=0>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['unit'].':<select id=kdOrgCr>'.$optOrg.'</select>&nbsp;';
echo $_SESSION['lang']['periode'].":<input type=text class=myinputtext id=tgl_cari onkeypress='return tanpa_kutip(event)'  size=10 maxlength=10 />";
echo $_SESSION['lang']['potongan'].':<select id=tpPotCr>'.$optTipePot.'</select>&nbsp;';
echo '<button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n\t </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"listData\">\r\n";
OPEN_BOX();
echo "<fieldset style=\"float:left;\">\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<!--display data-->\r\n<div id=\"contain\">\r\n<script>loadData();</script>\r\n</div>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<fieldset style=\"float:left\">\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr><td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td><td>\r\n<select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px;\"  >";
echo $optOrg;
echo "</select></td>\r\n</tr>\r\n<tr><td>";
echo $_SESSION['lang']['periode'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"tglAbsen\" style=\"width:150px\">";
echo $optPeriode;
echo "</select></td>\r\n</tr>\r\n<tr><td>";
echo $_SESSION['lang']['potongan'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"tpPotongan\" name=\"tpPotongan\" style=\"width:150px;\" >";
echo $optTipePot;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\">\r\n    <div id=\"tombolHeader\">\r\n        <button class=mybutton id=dtlAbn onclick=add_detail()>";
echo $_SESSION['lang']['save'];
echo "</button>\r\n        <button class=mybutton id=cancelAbn onclick=cancelAbsn()>";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n    </div>\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detailEntry\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<div id=\"addRow_table\">\r\n<fieldset  style=\"float:left\">\r\n<legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n<div id=\"detailIsi\">\r\n</div>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr><td id=\"tombol\">\r\n\r\n</td></tr>\r\n</table>\r\n</fieldset  style=\"float:left;\">\r\n</div><br />\r\n<br />\r\n<div style=\"overflow:auto; height:300px; clear:both;\">\r\n<fieldset  style=\"float:left;\">\r\n<legend>";
echo $_SESSION['lang']['datatersimpan'];
echo "</legend>\r\n<table cellspacing='1' border='0' class='sortable'>\r\n<thead>\r\n <tr class=\"rowheader\">\r\n    <td>No</td>\r\n    <td>";
echo $_SESSION['lang']['namakaryawan'];
echo "</td>\r\n    <td>";
echo $_SESSION['lang']['potongan'];
echo "</td>\r\n    <td>";
echo $_SESSION['lang']['keterangan'];
echo "</td>\r\n    <td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contentDetail\">\r\n\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();
echo "\r\n";

?>