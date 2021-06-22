<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>Timbangan Pembeli</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\n jdl_ats_0='";
echo $_SESSION['lang']['find'];
echo "';\r\n// alert(jdl_ats_0);\r\n jdl_ats_1='";
echo $_SESSION['lang']['findBrg'];
echo "';\r\n content_0='<fieldset><legend>";
echo $_SESSION['lang']['findnoBrg'];
echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';\r\n\r\nnmSaveHeader='';\r\nnmCancelHeader='';\r\nnmDetialDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmDetailCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n\r\n</script>\r\n<script type=\"application/javascript\" src=\"js/pabrik_timbangan_pembeli.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"headher\">\r\n";
for ($i = 0; $i < 24; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $jm .= '<option value='.$i.'>'.$i.'</option>';
}
for ($i = 0; $i < 60; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $mnt .= '<option value='.$i.'>'.$i.'</option>';
}
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan where tipekaryawan='5' and karyawanid!='".$_SESSION['standard']['userid']."' order by namakaryawan asc";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optKary .= '<option value='.$rOrg['karyawanid'].'>'.$rOrg['namakaryawan'].'</option>';
}
$optBrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sBrg = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kodebarang like '4%'";
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= "<option value='".$rBrg['kodebarang']."'>".$rBrg['namabarang'].'</option>';
}
$optJenis = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "<fieldset style='width: 280px;'>\r\n<legend>";
echo $_SESSION['lang']['form'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['namabarang'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"kdBrg\" name=\"kdBrg\" style=\"width:150px\" onchange=\"getCustomer(0,0,0)\">";
echo $optBrg;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['nmcust'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"custId\" name=\"custId\" style=\"width:150px\" onchange=\"getKontrak(0,0)\">";
echo $optJenis;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['NoKontrak'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"noKontrak\" style=\"width:150px\">";
echo $optJenis;
echo "</select></td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td colspan=\"3\" id=\"tmblHeader\">\r\n    <button class=mybutton id=dtlFormAtas onclick=getForm()>Preview</button>\r\n   \r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n    <div id=\"formInputan\" style=\"display: none;\">\r\n        <fieldset>\r\n            <legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n            <div id=\"formTampil\">\r\n                \r\n            </div>\r\n        </fieldset>\r\n    </div>\r\n  \r\n    </div>\r\n";
echo "\r\n<div id=\"list_ganti\">\r\n";
echo "    <div id=\"action_list\">\r\n\r\n</div>\r\n    ";
echo "<table>\r\n     <tr valign=middle>\r\n\t \r\n\t <td></td><td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['notransaksi'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['NoKontrak'].':<input type=text id=txtsearchKntrk size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariTransaksi()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> <fieldset style='float:left;'>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n\r\n<div id=\"contain\">\r\n<script>loadNData()</script>\r\n\r\n</div>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n";
echo close_body();

?>