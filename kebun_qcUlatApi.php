<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['ulatapi'].'</b>');
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\">\r\nnmTmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nnmTmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nkdBlok='";
echo $_SESSION['lang']['kodeblok'];
echo "';\r\npilBlok=\"";
echo $optKary;
echo "\";\r\n</script>\r\n<script language=\"javascript\" src=\"js/kebun_qcUlatApi.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
$optOrg .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n       where induk='".$_SESSION['org']['kodeorganisasi']."' and tipe='KEBUN' order by namaorganisasi asc";
$qOrg = mysql_query($sOrg) ;
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= "<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi'].'</option>';
}
echo "<table cellspacing=1 border=0>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['kebun'].":<select id=kdOrgCari style='width:120px;' >".$optOrg."</select>\r\n                    <!--<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext onclick=\"cariOrg('".$_SESSION['lang']['find']."','<fieldset><legend>".$_SESSION['lang']['searchdata']."</legend>Find<input type=text class=myinputtext id=crOrg><button class=mybutton onclick=findOrg2()>Find</button></fieldset><div id=container></div>','event')\">-->&nbsp;";
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n\t </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"listData\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<div id=\"contain\">\r\n<script>loadData();</script>\r\n</div>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
$arrJenis = ['sebelum' => 'sebelum', 'pengendalian' => 'pengendalian', 'sesudah' => 'sesudah'];
foreach ($arrJenis as $lsJenis) {
    $optJns .= "<option value='".$lsJenis."'>".$lsJenis.'</option>';
}
$optMandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$j = 'select karyawanid,namakaryawan,nik from '.$dbname.'.datakaryawan  where lokasitugas in (select kodeunit from '.$dbname.".bgt_regional_assignment \r\n\t\t\twhere regional='".$_SESSION['empl']['regional']."' and kodeunit like '%RO%')  and bagian='QC'";
$k = mysql_query($j) ;
while ($l = mysql_fetch_assoc($k)) {
    $optMandor .= "<option value='".$l['karyawanid']."'>".$l['nik'].' - '.$l['namakaryawan'].'</option>';
}
$optAstn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$d = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where \r\n\t\t\tlokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment \r\n\t\t\twhere regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%') \r\n\t\t\tand kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  alias like '%PENGAWAS%' or \r\n\t\t\talias like '%KA. AFDELING%' or alias like '%recorder%' or alias like '%KASUB AFDELING%')";
$e = mysql_query($d) ;
while ($f = mysql_fetch_assoc($e)) {
    $optAstn .= "<option value='".$f['karyawanid']."'>".$f['nik'].' - '.$f['namakaryawan'].'</option>';
}
$optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select karyawanid,namakaryawan,nik from '.$dbname.'.datakaryawan  where lokasitugas in (select kodeunit from '.$dbname.".bgt_regional_assignment \r\n\t\t\twhere regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%')  and bagian='QC'";
$h = mysql_query($g) ;
while ($i = mysql_fetch_assoc($h)) {
    $optKadiv .= "<option value='".$i['karyawanid']."'>".$i['nik'].' - '.$i['namakaryawan'].'</option>';
}
echo "<fieldset style=\"float:left\"\">\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kebun'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"divisiId\" style=\"width:150px;\" >";
echo $optOrg;
echo "</select>\r\n</td>\r\n<td>";
echo $_SESSION['lang']['pengawas'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"pengawasId\" style=\"width:150px;\">";
echo $optMandor;
echo "</select>\r\n</td>\r\n\r\n</tr>\r\n\r\n <tr>\r\n<td>";
echo $_SESSION['lang']['kodeblok'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n    <input type=\"text\" class=\"myinputtext\" id=\"kodeBlok\" style=\"width:150px;\" readonly onclick=\"getBlok(kdBlok,event)\" />\r\n    <br/><span id=\"nmOrg\"></span>\r\n</td>\r\n<td>";
echo $_SESSION['lang']['pendamping'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"pendampingId\" style=\"width:150px;\">";
echo $optAstn;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tglsensus'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tglSensus\" name=\"tglSensus\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n</td>\r\n<td>";
echo $_SESSION['lang']['mengetahui'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"mengetahuiId\" style=\"width:150px;\">";
echo $optKadiv;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tglPengendalian'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tglPengendalian\" name=\"tglPengendalian\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n</td>\r\n<td>";
echo $_SESSION['lang']['catatan'];
echo "</td>\r\n<td>:</td>\r\n<td rowspan=\"2\">\r\n    <textarea id=\"catatan\" onkeypress=\"return tanpa_kutip(event)\"></textarea>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['jenis'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"jenisId\" style=\"width:150px;\">";
echo $optJns;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n    \r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset><input type=\"hidden\" id=\"proses\" value=\"insert\" />\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detailEntry\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<div id=\"addRow_table\">\r\n<fieldset style=\"clear:both;float:left;\">\r\n<legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n<div id=\"detailIsi\">\r\n</div>\r\n<table>\r\n<tr><td id=\"tombol\">\r\n\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div><br />\r\n<br />\r\n<div style=\"overflow:auto;height:300px;clear:both;\">\r\n<fieldset style=\"float:left;\">\r\n<legend>";
echo $_SESSION['lang']['datatersimpan'];
echo "</legend>\r\n    <table cellspacing=\"1\" border=\"0\">\r\n    <thead>\r\n        <tr class=\"rowheader\">\r\n            <td>No.</td>\r\n            ";
$table .= '<td>'.$_SESSION['lang']['pokokdiamati'].'</td>';
$table .= '<td>'.$_SESSION['lang']['luaspengamatan'].'</td>';
$table .= '<td>Darna Trima</td>';
$table .= '<td>Setothosea Asigna</td>';
$table .= '<td>Setora Nitens</td>';
$table .= '<td>Ulat Kantong</td>';
$table .= '<td>Keterangan</td>';
$table .= '<td>Action</td>';
echo $table;
echo "        </tr>\r\n    </thead>\r\n    <tbody id=\"contentDetail\">\r\n    \r\n    </tbody>\r\n    </table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>