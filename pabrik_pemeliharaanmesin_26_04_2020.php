<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>Pemeliharaan Mesin</b>');
$namaKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
echo "\r\n\r\n\r\n<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\n jdl_ats_0='";
echo $_SESSION['lang']['find'];
echo "';\r\n// alert(jdl_ats_0);\r\n jdl_ats_1='";
echo $_SESSION['lang']['findBrg'];
echo "';\r\n content_0='<fieldset><legend>";
echo $_SESSION['lang']['findnoBrg'];
echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';\r\n\r\nnmSaveHeader='";
echo $_SESSION['lang']['save'];
echo "';\r\nnmCancelHeader='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nnmDetialDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmDetailCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n\r\n</script>\r\n<script type=\"application/javascript\" src=\"js/pabik_pemeliharaanmesin.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
echo "<table align='center'>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['notransaksi'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariTransaksi()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> </div>\r\n";
echo "<div id=\"list_ganti\">\r\n";

echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\" >\r\n<thead>\r\n<tr class=\"rowheader\">\r\n<td>No.</td>\r\n<td>";
echo $_SESSION['lang']['notransaksi'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['shift'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['statasiun'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['mesin'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['dari'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['sampai'];
echo "</td>\r\n<td>Update By</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n";
$userOnline = $_SESSION['standard']['userid'];
$userName = $_SESSION['standard']['username'];
$limit = 25;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from '.$dbname.'.pabrik_rawatmesinht   order by `tanggal` desc';
$query2 = mysql_query($ql2);
while ($jsl = mysql_fetch_object($query2)) {
    $jlhbrs = $jsl->jmlhrow;
}
$slvhc = 'select * from '.$dbname.'.pabrik_rawatmesinht   order by `tanggal` desc limit '.$offset.','.$limit.' ';
$qlvhc = mysql_query($slvhc);
$user_online = $_SESSION['standard']['userid'];
while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
    ++$no;
    $dtJamMulai = explode(' ', $rlvhc['jammulai']);
    $jamMulai = explode(':', $dtJamMulai[1]);
    $dtJamSlsi = explode(' ', $rlvhc['jamselesai']);
    $jamSlsi = explode(':', $dtJamSlsi[1]);
    echo "    <tr class=\"rowcontent\">\r\n    <td>";
    echo $no;
    echo "</td>\r\n    <td>";
    echo $rlvhc['notransaksi'];
    echo "</td>\r\n    <td>";
    echo tanggalnormal($rlvhc['tanggal']);
    echo "</td>\r\n    <td>";
    echo $rlvhc['shift'];
    echo "</td>\r\n    <td>";
    echo $rlvhc['statasiun'];
    echo "</td>\r\n    <td>";
    echo $rlvhc['mesin'];
    echo "</td>\r\n    <td>";
    echo tanggalnormald($rlvhc['jammulai']);
    echo "</td>\r\n    <td>";
    echo tanggalnormald($rlvhc['jamselesai']);
    echo "</td>\r\n    <td>";
    echo $namaKar[$rlvhc['updateby']];
    echo "</td><td>\r\n\r\n    ";
    if ($rlvhc['updateby'] === $userOnline) {
        echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['notransaksi']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['pabrik']."','".$rlvhc['shift']."','".$rlvhc['statasiun']."','".$rlvhc['mesin']."','".$rlvhc['kegiatan']."','".tanggalnormal($dtJamMulai[0])."','".tanggalnormal($dtJamSlsi[0])."','".$jamMulai[0]."','".$jamMulai[1]."','".$jamSlsi[0]."','".$jamSlsi[1]."','".$rlvhc['keterangan']."');\">\r\n        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."');\" >\r\n        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event);\">";
    } else {
        echo " <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_rawatmesinht','".$rlvhc['notransaksi']."','','pabrik_slavePemeliharaanPdf',event)\">";
    }
}
echo "</td></tr>\r\n\r\n";
echo "\r\n\t<tr class=rowheader><td colspan=9 align=center>\r\n\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t</td>\r\n\t</tr>";
echo "\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n";

echo "</div>\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";

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
$sOrg = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' AND kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optPabrik .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['pabrik'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"hidden\" id=\"trans_no\" name=\"trans_no\" class=\"myinputtext\" style=\"width:120px;\" />\r\n<select id=\"pbrkId\" name=\"pbrkId\" style=\"width:150px\" onchange=\"getStation(0,0,0)\"><option value=\"\"></option>";
echo $optPabrik;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['shift'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"shitId\" name=\"shitId\" style=\"width:150px\"></select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['statasiun'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"statId\" name=\"statId\" style=\"width:150px\" onchange=\"getMesin(0,0)\"></select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['mesin'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"msnId\" name=\"msnId\" style=\"width:150px\"></select></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kegiatan'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"kegtn\" name=\"kegtn\" onkeypress=\"return charAndNumAndStrip(event)\" maxlength=\"50\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tglCek\" name=\"tglCek\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['jammulai'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"jmAwal\" name=\"jmAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n    <select id=\"jamMulai\">";
echo $jm;
echo '</select>:<select id="mntMulai">';
echo $mnt;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['jamselesai'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"jmAkhir\" name=\"jmAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n     <select id=\"jamSlsi\">";
echo $jm;
echo '</select>:<select id="mntSlsi">';
echo $mnt;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['keterangan'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<textarea id=\"keterangan\" id=\"keterangan\" onkeypress=\"return charAndNumAndStrip(event);\" rows=\"4\"/></textarea></td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" id=\"tmblHeader\">\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
echo "</div>\r\n<div id=\"detail_ganti\" style=\"display:none\">\r\n";
echo "<div id=\"addRow_table\">\r\n<div id=\"detail_isi\" >\r\n</div>\r\n<div id=\"tmblDetail\">\r\n\r\n</div>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>