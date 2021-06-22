<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>Pengeluaran Barang MRIS :</b><br />');
$frm[0] = '';
$frm[1] = '';
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/log_pengeluaran_mris.js\" /></script>\r\n<script>\r\n pild='";
echo '<option value="">'.$_SESSION['lang']['pilihdata'].'</option>';
echo "';\r\n</script><br />\r\n";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKbn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optPrd = $optAfd = $optKbn;
$skbn = 'select distinct left(untukunit,4) as kodeorg from '.$dbname.".log_mrisht \r\n       where left(untukunit,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') and untukunit like '".$_SESSION['empl']['lokasitugas']."%'";
$qkbn = mysql_query($skbn);
while ($rkbn = mysql_fetch_assoc($qkbn)) {
    $optKbn .= "<option value='".$rkbn['kodeorg']."'>".$optNmOrg[$rkbn['kodeorg']].'</option>';
}
$frm[0] .= "<fieldset style=float:left>\r\n    <legend>".$_SESSION['lang']['form'].'</legend>';
/*
$frm[0] .= "<table>\r\n    <tr><td>".$_SESSION['lang']['kebun'].'</td><td><select id=kbnId style=width:150px onchange=getAfd()>'.$optKbn."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['afdeling'].'</td><td><select id=afdId style=width:150px onchange=getPrd()>'.$optAfd."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['periode'].'</td><td><select id=periodeId style=width:150px >'.$optPrd."</select></td></tr>\r\n    </table>";
*/
$frm[0] .= "<table>\r\n    <tr><td>".'Unit'.'</td><td><select id=kbnId style=width:150px onchange=getAfd()>'.$optKbn."</select></td></tr>\r\n    <tr><td>".'Sub Unit'.'</td><td><select id=afdId style=width:150px onchange=getPrd()>'.$optAfd."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['periode'].'</td><td><select id=periodeId style=width:150px >'.$optPrd."</select></td></tr>\r\n    </table>";
$frm[0] .= '<button class=mybutton onclick=prevData()>'.$_SESSION['lang']['find']."</button>&nbsp;\r\n          <button class=mybutton onclick=hapusForm()>".$_SESSION['lang']['reset'].'</button>';
$frm[0] .= '</fieldset>';
$frm[0] .= "<fieldset  style=float:left>\r\n          <legend>".$_SESSION['lang']['find']."</legend>\r\n            ".$_SESSION['lang']['nomris']." <input type=text onkeypress='return tanpa_kutip(event)' id=crDataMris style=width:150px />\r\n           <button class=mybutton onclick=prevData()>".$_SESSION['lang']['find']."</button>\r\n           \r\n          </fieldset>\r\n          ";
echo "\r\n\r\n";
$frm[0] .= "<div style=clear:both;></div>\r\n    <div id=formPertama style=display:none;float:left;overflow:auto;height:50%;max-width:100%;><table><tr><td valign=top>";
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['data'].'</legend>';
$frm[0] .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>';
$frm[0] .= '<td>'.$_SESSION['lang']['nomris'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
//$frm[0] .= '<td>'.$_SESSION['lang']['kebun'].'</td>';
//$frm[0] .= '<td>'.$_SESSION['lang']['afdeling'].'</td>';
$frm[0] .= '<td>'.'Unit'.'</td>';
$frm[0] .= '<td>'.'Sub Unit'.'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['dibuat']."</td>\r\n     <td>".$_SESSION['lang']['action']."</td>\r\n    </tr><tbody id=detailContainer>";
$frm[0] .= '</tbody></table>';
$frm[0] .= "</fieldset>\r\n          </td><td  valign=top><div id=formKedua style='display:none;'><fieldset><legend>".$_SESSION['lang']['detail'].'</legend>';
$frm[0] .= '<table><tr><td>'.$_SESSION['lang']['tanggal'].'</td><td>:</td><td><span id=tglPermintaan></span></td>';
$frm[0] .= '<td>'.$_SESSION['lang']['tanggalkeluarbarang'].'</td><td>:</td><td><input type=text class=myinputtext id=tglKeluar onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 onblur=getPost() /></td></tr>';
$frm[0] .= '<tr><td>'.$_SESSION['lang']['nomris'].'</td><td>:</td><td><span id=nomris></span></td>';
$frm[0] .= '<td>'.$_SESSION['lang']['afdeling'].'</td><td>:</td><td><span id=kbnId2></span></td></tr>';
$frm[0] .= '<tr><td>'.$_SESSION['lang']['sloc'].'</td><td>:</td><td><span id=gudangId></span></td>';
$frm[0] .= '<td>'.$_SESSION['lang']['periode']."</td><td>:</td><td><span id=periodeStr></span> - <span id=periodeEnd></span>\r\n     <input type=hidden id=tglMulai value='' /><input type=hidden id=tglSelesai value='' />\r\n     </td></tr>";
$frm[0] .= '</table>';
$frm[0] .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>';
$frm[0] .= '<td>'.$_SESSION['lang']['kodebarang'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['satuan'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['kodeblok'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['kodevhc']."</td>\r\n     <td>".$_SESSION['lang']['jumlah'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['realisasisblmnya'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['realisasi'].'</td>';
$frm[0] .= '<td>'.$_SESSION['lang']['action'].'</td>';
$frm[0] .= '</tr><tbody id=detailContainer2>';
$frm[0] .= "</tbody>\r\n     </table>\r\n    <button class=mybutton onclick=donePengeluaran()>".$_SESSION['lang']['done']."</button>\r\n     </fieldset></div></td></tr></table>";
$frm[0] .= '</div>';
$optGdngCr = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sGdng = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where induk in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') and tipe in ('GUDANG','GUDANGTEMP')";
$qGng = mysql_query($sGdng);
while ($rGdng = mysql_fetch_assoc($qGng)) {
    $optGdngCr .= "<option value='".$rGdng['kodeorganisasi']."'>".$rGdng['namaorganisasi'].'</option>';
}
$frm[1] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n          <table><tr><td>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."</td><td>:</td><td>\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12></td>\r\n          <td>\r\n\t  ".$_SESSION['lang']['sloc']."</td><td>:</td>\r\n          <td><select id=gdngCr style=width:100px>".$optGdngCr."</select></td></tr></table>\r\n\t  <button class=mybutton onclick=cariBast(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t <div id=containerlist></div>\r\n         <script>cariBast(0)</script>\r\n\t </fieldset>\t \r\n\t ";
$hfrm[0] = $_SESSION['lang']['pengeluaranbarang'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 200, 1150);
CLOSE_BOX();
echo close_body();

?>
