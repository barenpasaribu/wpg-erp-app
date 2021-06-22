<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['penggantianKomponen'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n\r\n<script type=\"application/javascript\" src=\"js/vhc_penggantianKomponen.js\"></script>\r\n<script>\r\n jdl_ats_0='";
echo $_SESSION['lang']['find'];
echo "';\r\n// alert(jdl_ats_0);\r\n jdl_ats_1='";
echo $_SESSION['lang']['findBrg'];
echo "';\r\n content_0='<fieldset><legend>";
echo $_SESSION['lang']['findnoBrg'];
echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Go</button><div id=container></div>';\r\n\r\ntmblNew='";
echo $_SESSION['lang']['new'];
echo "';\r\n\r\ntmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\ntmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\ntmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\ntmblCancelDetail='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n</script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
echo "<table align=center border=0>\r\n     <tr >\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n           <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n         <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['notransaksi'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariTransaksi()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n         </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"list_ganti\">\r\n<script>load_new_data();</script>\r\n</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
$svhc = 'select kodevhc,jenisvhc,tahunperolehan from '.$dbname.'.vhc_5master  order by kodevhc';
$qvhc = mysql_query($svhc);
while ($rvhc = mysql_fetch_assoc($qvhc)) {
    $optVhc .= "<option value='".$rvhc['kodevhc']."'>".$rvhc['kodevhc'].'['.$rvhc['tahunperolehan'].']</option>';
}
$svhc2 = 'select kodeorg from '.$dbname.'.vhc_5master group by kodeorg';
$qvhc2 = mysql_query($svhc2);
while ($rvhc2 = mysql_fetch_assoc($qvhc2)) {
    $optOrg .= "<option value='".$rvhc2['kodeorg']."'>".$rvhc2['kodeorg'].'</option>';
}
for ($t = 0; $t < 24; ++$t) {
    if (strlen($t) < 2) {
        $t = '0'.$t;
    }

    $jm .= '<option value='.$t.' '.((0 === $t ? 'selected' : '')).'>'.$t.'</option>';
}
for ($y = 0; $y < 60; ++$y) {
    if (strlen($y) < 2) {
        $y = '0'.$y;
    }

    $mnt .= '<option value='.$y.' '.((0 === $y ? 'selected' : '')).'>'.$y.'</option>';
}
$optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select karyawanid,namakaryawan,nik,kodejabatan from '.$dbname.".datakaryawan where \r\n\tlokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')\r\n\tand tipekaryawan!='5' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Kepala Mekanik%' or alias like '%Pelaksana Bengkel%' or alias like '%Mekanik%' or alias like '%Asisten Bengkel%') ";
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    $optKar .= "<option value='".$d['karyawanid']."'>".$d['nik'].' - '.$d['namakaryawan'].'</option>';
}
echo "\r\n";
echo "<fieldset>\r\n<legend>".$_SESSION['lang']['header']."</legend>\r\n
<table cellspacing=1 border=0>\r\n
<tr>\r\n\t
	<td>".$_SESSION['lang']['unit']."</td>\r\n\t
	<td>:</td>\r\n\t
	<td><select id=codeOrg name=codeOrg style=width:150px; onchange=getNotrans(0)><option value=''></option>".$optOrg."</select></td>\r\n
</tr>\r\n
<tr>\r\n\t
	<td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t
	<td>:</td>\r\n\t<td><input type=text  id=trans_no name=trans_no class=myinputtext style=width:150px; /></td>\r\n
</tr>\r\n
<tr>\r\n\t
	<td>".$_SESSION['lang']['kodevhc']."</td>\r\n\t
	<td>:</td>\r\n\t
	<td><select id=vhc_code name=vhc_code style=width:150px;>".$optVhc."</select></td>\r\n
</tr>\r\n
<tr>\r\n\t
	<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['perbaikan']."</td>\r\n\t
	<td>:</td>\r\n\t
	<td><input type=text class=myinputtext id=tgl_ganti name=tgl_ganti onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>\r\n
</tr>\r\n\r\n\r\n
<tr>\r\n\t
	<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['masuk']."</td>\r\n\t
	<td>:</td>\r\n\t<td><input type=text class=myinputtext id=tglMasuk name=tglMasuk onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>\r\n
</tr>\r\n<tr>\r\n\t
	<td>".$_SESSION['lang']['jam'].' '.$_SESSION['lang']['masuk']."</td> \r\n\t
	<td>:</td>\r\n\t
	<td>\r\n\t\t<select id=jm1 name=jmId >".$jm.'</select>:<select id=mn1>'.$mnt."</select>\r\n\t</td>\r\n
</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['selesai']."</td>\r\n\t<td>:</td>\r\n\t<td><input type=text class=myinputtext id=tglSelesai name=tglSelesai onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['jamselesai']."</td> \r\n\t<td>:</td>\r\n\t\r\n\t<td>\t\t\r\n\t\t<select id=jm2 name=jmId2 >".$jm.'</select>:<select id=mn2>'.$mnt."</select>\r\n\t</td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['diambil']."</td>\r\n\t<td>:</td>\r\n\t<td><input type=text class=myinputtext id=tglAmbil name=tglAmbil onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=width:150px; /></td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n\t<td>".$_SESSION['lang']['downtime']."</td>\r\n\t<td>:</td>\r\n\t<td><input type=text class=myinputtextnumber id=dwnTime name=dwnTime onkeypress=return angka_doang(event);  value=0  maxlength=10 style=width:150px; />".$_SESSION['lang']['jmlhJam']."</td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['descDamage']."</td>\r\n\t<td>:</td>\r\n\t\r\n\t<td><textarea id=descDmg cols=30 rows=5   onkeypress=\"return tanpa_kutip(event);\" ></textarea></td>\r\n</tr>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<tr>\r\n\t<td>".$_SESSION['lang']['kmhmmasuk']."</td>\r\n\t<td>:</td>\r\n\t<td><input type=text class=myinputtextnumber id=kmhmMasuk name=kmhmMasuk onkeypress=return angka_doang(event);  value=0  maxlength=10 style=width:150px; /></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['namamekanik']." 1</td>\r\n\t<td>:</td>\r\n\t<td><select id=namaMekanik1 name=namaMekanik1 style=width:150px;>".$optKar."</select></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['namamekanik']." 2</td>\r\n\t<td>:</td>\r\n\t<td><select id=namaMekanik2 name=namaMekanik2 style=width:150px;>".$optKar."</select></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['namamekanik']." 3</td>\r\n\t<td>:</td>\r\n\t<td><select id=namaMekanik3 name=namaMekanik3 style=width:150px;>".$optKar."</select></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['namamekanik']." 4</td>\r\n\t<td>:</td>\r\n\t<td><select id=namaMekanik4 name=namaMekanik4 style=width:150px;>".$optKar."</select></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['namamekanik']." 5</td>\r\n\t<td>:</td>\r\n\t<td><select id=namaMekanik5 name=namaMekanik5 style=width:150px;>".$optKar."</select></td>\r\n</tr>\r\n<tr>\r\n\t<td>".$_SESSION['lang']['notransaksi'].' '.$_SESSION['lang']['gudang']."</td>\r\n\t<td>:</td>\r\n\t<td><input type=text id=noTranGudang disabled class=myinputtext size=25 onkeypress=\"return tanpa_kutip(event);\">\r\n\t    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariNoGudang class=resicon onclick=cariNoGudang('".$_SESSION['lang']['find']."',event)>\r\n\t </td>\r\n</tr>\r\n\r\n\r\n\r\n\r\n\r\n\r\n<tr>\r\n<td colspan=3 id=tmblHeader>\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>";
echo "\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detail_ganti\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<div id=\"addRow_table\">\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tbody id=\"detail_isi\">\r\n";
echo '<b>'.$_SESSION['lang']['notransaksi']."</b> : <input type=\"text\" id='detail_kode' name='detail_kode' disabled=\"disabled\" style=\"width:150px\" />";
echo "<div  id=\"tmblDetail\">\r\n</div>\r\n<table id=\"ppDetailTable\" >\r\n</table>\r\n</tbody>\r\n<tr><td>\r\n\r\n</td></tr>\r\n</table>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>