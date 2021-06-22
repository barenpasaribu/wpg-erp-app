<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['pengirimanBibit'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\njdlExcel='";
echo $_SESSION['lang']['pengirimanBibit'];
echo "';\r\n\r\ntmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\ntmblCancelDetail='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n</script>\r\n<script type=\"application/javascript\" src=\"js/kebun_pengirimanBibit.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
echo "<table>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['notransaksi'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariTransaksi()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"list_ganti\">\r\n<script>loadData();</script>\r\n</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
$svhc = 'select kodevhc,jenisvhc,tahunperolehan from '.$dbname.'.vhc_5master  order by kodevhc';
$qvhc = mysql_query($svhc) ;
while ($rvhc = mysql_fetch_assoc($qvhc)) {
    $optVhc .= '<option value='.$rvhc['kodevhc'].'>'.$rvhc['kodevhc'].'['.$rvhc['tahunperolehan'].']</option>';
}
$sOrg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='BIBITAN' order by namaorganisasi asc";
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$sOrg2 = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe IN ('KEBUN','AFDELING') order by namaorganisasi asc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optOrg2 .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$sCust = 'select kodecustomer,namacustomer  from '.$dbname.'.pmn_4customer order by namacustomer';
$qCust = mysql_query($sCust) || exit(mysql_error($sCust));
while ($rCust = mysql_fetch_assoc($qCust)) {
    $optCust .= '<option value='.$rCust['kodecustomer'].' >'.$rCust['namacustomer'].'</option>';
}
$sKeg = 'select kodekegiatan,namakegiatan,kelompok,noakun from '.$dbname.'.setup_kegiatan order by noakun asc';
$qKeg = mysql_query($sKeg) ;
while ($rKeg = mysql_fetch_assoc($qKeg)) {
    $optKeg .= '<option value='.$rKeg['kodekegiatan'].' >'.$rKeg['noakun'].' ['.$rKeg['kelompok'].'] ['.$rKeg['namakegiatan'].']</option>';
}
$sBibit = 'select jenisbibit  from '.$dbname.'.setup_jenisbibit order by jenisbibit  asc';
$qBibit = mysql_query($sBibit) ;
while ($rBibit = mysql_fetch_assoc($qBibit)) {
    $optBibit .= '<option value='.$rBibit['jenisbibit'].' >'.$rBibit['jenisbibit'].'</option>';
}
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['entryForm'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"codeOrg\" name=\"codeOrg\" style=\"width:150px;\" onchange=\"getNotrans()\"><option value=\"\"></option>";
echo $optOrg;
echo "</select></td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['notransaksi'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\"  id=\"trans_no\" name=\"trans_no\" class=\"myinputtext\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"tgl\" name=\"tgl\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['jenisbibit'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"jnsBibit\" name=\"jnsBibit\" style=\"width:150px;\" ><option value=\"\"></option>";
echo $optBibit;
echo "</select></td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['jumlah'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtextnumber\" id=\"jmlh\" name=\"jmlh\" onkeypress=\"return angka_doang(event);\"  value=\"0\"  maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['OrgTujuan'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"OrgTujuan\" name=\"OrgTujuan\" style=\"width:150px;\" onChange=\"knciForm()\"  ><option value=\"\"></option>";
echo $optOrg2;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['nmcust'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"custId\" name=\"custId\" style=\"width:150px;\" onChange=\"knciForm()\"  ><option value=\"\"></option>";
echo $optCust;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['namakegiatan'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"kegCode\" name=\"kegCode\" style=\"width:150px;\" ><option value=\"\"></option>";
echo $optKeg;
echo "</select></td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td colspan=\"3\" id=\"tmblHeader\">\r\n<button class=mybutton id='dtl_pem' onclick='saveData()'>";
echo $_SESSION['lang']['save'];
echo "</button><button class=mybutton id='cancel_gti' onclick='cancelSave()'>";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n";
echo close_body();

?>