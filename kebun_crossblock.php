<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo "<script>\r\npilh=\" ";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n<script>plh=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/kebun_crossblock.js\"></script>\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/zReport.js'></script>\r\n<script>\r\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n";
$sOrg2 = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi \r\n    where tipe ='kebun'\r\n    order by kodeorganisasi asc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optkodeorg1 .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$sOrg2 = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.". kebun_crossblock_ht \r\n    order by tanggal desc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optperiode1 .= '<option value='.$rOrg2['periode'].'>'.$rOrg2['periode'].'</option>';
}
$optkodeorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi \r\n    where tipe in ('blok', 'afdeling') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'\r\n    order by tipe desc, kodeorganisasi asc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optkodeorg .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['namaorganisasi'].'</option>';
}
$optkaryawan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select karyawanid,namakaryawan from '.$dbname.". datakaryawan\r\n    where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'\r\n        and (tanggalkeluar is NULL or tanggalkeluar= '0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n        and tipekaryawan<=3\r\n    order by namakaryawan";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optkaryawan .= '<option value='.$rOrg2['karyawanid'].'>'.$rOrg2['namakaryawan'].'</option>';
}
$optcek = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optcek .= "<option value='0'>Cek</option>";
$optcek .= "<option value='1'>Ricek</option>";
$optkelompok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optkelompok .= "<option value='APANEN'>Ancak Panen</option>";
$optkelompok .= "<option value='MUTUTPH'>Mutu TPH</option>";
$optkegiatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select id, nama from '.$dbname.".qc_5parameter \r\n    where tipe ='XBLOK'\r\n    order by nama asc";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optkegiatan .= '<option value='.$rOrg2['id'].'>'.$rOrg2['nama'].'</option>';
}
$arrjabatan = ['Manager', 'Askep', 'Asisten', 'Mandor'];
$optjabatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrjabatan as $asjab) {
    $optjabatan .= "<option value='".$asjab."'>".$asjab.'</option>';
}
$optqcid = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select id, tipe, nama from '.$dbname.".qc_5parameter\r\n    order by tipe, nama\r\n    ";
$qOrg2 = mysql_query($sOrg2) ;
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optqcid .= '<option value='.$rOrg2['id'].'>'.$rOrg2['tipe'].' - '.$rOrg2['nama'].'</option>';
}
OPEN_BOX('', '<b>Cross Block</b>');
$frm[0] .= "<fieldset style='width:800px;float:left'><legend>".$_SESSION['lang']['form'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr>\r\n    <td>".$_SESSION['lang']['tanggal']."</td>\r\n    <td>:</td>\r\n    <td><input type='hidden' id='proses0' value='savedata0'/><input type='hidden' id='id' value=''/><input type='text' class='myinputtext' id='tanggal' onmousemove='setCalendar(this.id)' onkeypress='return false;'  \r\n    size='10' maxlength='10' style=\"width:150px;\"/></td>\r\n</tr>\r\n<tr>\r\n    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n    <td>:</td>\r\n    <td><select id=kodeorg style=width:150px>".$optkodeorg."</select></td>\r\n</tr>\r\n<tr>\r\n    <td>".$_SESSION['lang']['jabatan']."</td>\r\n    <td>:</td>\r\n    <td><select id=jabatan style=width:150px>".$optjabatan."</select></td>\r\n</tr>\r\n<tr>\r\n    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n    <td>:</td>\r\n    <td><select id=karyawan style=width:150px>".$optkaryawan."</select></td>\r\n</tr>\r\n<tr>\r\n    <td>Check/Re-Check</td>\r\n    <td>:</td>\r\n    <td><select id=cek style=width:150px>".$optcek."</select></td>\r\n</tr>\r\n<tr>\r\n    <td style=width:200px;>".$_SESSION['lang']['kelompok']."</td>\r\n    <td>:</td>\r\n    <td><select id=kelompok style=width:150px onchange=openkegiatan()>".$optkelompok."</select></td>\r\n</tr>\r\n<tr>\r\n    <td colspan=3><input type='hidden' id='jumlahkegiatan' value='0'/><div id=container2></div></td>\r\n</tr>\r\n<tr>\r\n    <td>".$_SESSION['lang']['keterangan']."</td>\r\n    <td>:</td>\r\n    <td><input type='text' class='myinputtext' style='width:150px;' id='keterangan' onkeypress='return tanpa_kutip(event)' maxlength=100/></td>\r\n</tr>";
$frm[0] .= "<tr>\r\n    <td colspan=3>\r\n        <button class=mybutton id=save0 name=save0 onclick=savedata0()>".$_SESSION['lang']['save']."</button>\r\n        <button class=mybutton id=cancel0 name=cancel0 onclick=canceldata0()>".$_SESSION['lang']['cancel']."</button>\r\n    </td></tr>";
$frm[0] .= '</table></fieldset>';
$frm[0] .= '<fieldset style=width:800px;><legend>'.$_SESSION['lang']['datatersimpan'].'</legend>';
$frm[0] .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n    <thead>\r\n        <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['nomor']."</td>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td>".$_SESSION['lang']['kodeblok']."</td>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>Check/Re-Check</td>\r\n        <td>".$_SESSION['lang']['keterangan']."</td>\r\n        <td colspan=2>".$_SESSION['lang']['action']."</td>\r\n        </tr>\r\n    </thead>\r\n    <tbody id=container0><script>loaddata0()</script>\r\n    ";
$frm[0] .= '</tbody></table></fieldset>';
$arr = '##kodeorg1##periode1';
$frm[1] .= "<fieldset style='width:800px;float:left'>\r\n<legend>List</legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['kodeorg']."</label></td>\r\n    <td>:</td>\r\n    <td><select id=\"kodeorg1\" name=\"kodeorg1\" style=\"width:150px\" onchange=hideById('container1')>".$optkodeorg1."</select></td>\r\n</tr>\r\n<tr>\r\n    <td><label>".$_SESSION['lang']['periode']."</label></td>\r\n    <td>:</td>\r\n    <td><select id=\"periode1\" name=\"periode1\" style=\"width:150px\" onchange=hideById('container1')>".$optperiode1."</select></td>\r\n</tr>\r\n<tr>\r\n    <td colspan=\"3\">\r\n        <button onclick=\"zPreview('kebun_slave_crossblock','".$arr."','container1'); showById('container1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'kebun_slave_crossblock.php','".$arr."'); \" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>\r\n    </td>\r\n</tr>\r\n</table>\r\n</fieldset>";
$frm[1] .= "<fieldset style=width:800px;><legend>Print Area</legend>\r\n<div id='container1'>\r\n</div></fieldset>";
$hfrm[0] = 'Form';
$hfrm[1] = 'List';
drawTab('FRM', $hfrm, $frm, 150, 1240);
echo "\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>