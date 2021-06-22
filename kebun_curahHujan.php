<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['curahHujan'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\" src=\"js/kebun_curahHujan.js\"></script>\r\n\r\n<div id=\"action_list\">\r\n";
for ($t = 0; $t < 13; ++$t) {
    if (strlen($t) < 2) {
        $t = '0'.$t;
    }

    $jm .= '<option value='.$t.' '.((0 === $t ? 'selected' : '')).'>'.$t.'</option>';
}
for ($tt = 13; $tt < 24; ++$tt) {
    if (strlen($tt) < 2) {
        $tt = '0'.$tt;
    }

    $jmt .= '<option value='.$tt.' '.((0 === $tt ? 'selected' : '')).'>'.$tt.'</option>';
}
for ($y = 0; $y < 60; ++$y) {
    if (strlen($y) < 2) {
        $y = '0'.$y;
    }

    $mnt .= '<option value='.$y.' '.((0 === $y ? 'selected' : '')).'>'.$y.'</option>';
}
for ($x = 0; $x <= 24; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
$lokasi = $_SESSION['empl']['lokasitugas'];
$sql = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe in ('AFDELING','KEBUN') and kodeorganisasi like '".$lokasi."%'";
$query = mysql_query($sql) ;
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
echo "<table cellspacing=1 border=0>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['kodeorg'].":<select id=unitOrg name=unitOrg><option value=''></option>".$optOrg.'</select>&nbsp;';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariCurah()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n\t\r\n\t </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=\"listData\">\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n<thead>\r\n<tr class=\"rowheader\">\r\n<td>No.</td>\r\n<td>";
echo $_SESSION['lang']['kebun'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td> \r\n<td>";
echo $_SESSION['lang']['pagi'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['sore'];
echo "</td>\t \r\n<td>";
echo $_SESSION['lang']['note'];
echo "</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n";

$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
$limit = 10;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_curahhujan where `kodeorg` like '".$lokasi."%'  order by `tanggal` desc";
$query2 = mysql_query($ql2) ;
while ($jsl = mysql_fetch_object($query2)) {
    $jlhbrs = $jsl->jmlhrow;
}
$str = 'select * from '.$dbname.".kebun_curahhujan where `kodeorg` like  '".$lokasi."%' order by tanggal desc  limit ".$offset.','.$limit.'';
if (mysql_query($str)) {
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $spr = 'select namaorganisasi from  '.$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."'";
        $rep = mysql_query($spr) ;
        $bas = mysql_fetch_object($rep);
        ++$no;
        $sGp = 'select DISTINCT sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$bar->kodeorg."' and `periode`='".substr($bar->tanggal, 0, 7)."'";
        $qGp = mysql_query($sGp) ;
        $rGp = mysql_fetch_assoc($qGp);
        echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t<td>".$no."</td>\r\n\t\t<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>\r\n\t\t<td id='kpsits_".$no."'>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t<td id='strt_".$no."'>".$bar->pagi."</td>\r\n\t\t<td id='end_".$no."'>".$bar->sore."</td>\r\n\t\t<td id='tglex_".$no."'>".$bar->catatan.'</td><td>';
        //if (substr($bar->tanggal, 7) === $periodeAkutansi || 0 === $rGp['sudahproses']) {
            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."');\"><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"printPDF('".$bar->kodeorg."','".tanggalnormal($bar->tanggal)."',event);\">";
        //}

        echo '</td></tr>';
    }
    echo "\r\n\t\t<tr><td colspan=7 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
} else {
    echo ' Gagal,'.mysql_error($conn);
}

echo "\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n\r\n<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
for ($x = 0; $x <= 12; ++$x) {
    $dte = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPrd .= '<option value='.date('Y-m', $dte).'>'.date('Y-m', $dte).'</option>';
}
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['entryForm'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:150px;\" ><option value=\"\"></option>";
echo $optOrg;
echo "</select>\r\n<!--<input type=\"text\"  id=\"noSpb\" name=\"noSpb\" class=\"myinputtext\" style=\"width:120px;\" disabled=\"disabled\" />--></td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tgl\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false; \" size=\"10\" maxlength=\"10\" style=\"width:150px;\" />\r\n</td>\r\n</tr>\r\n\r\n";
echo "\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['jam'].' '.$_SESSION['lang']['mulai'].' '.$_SESSION['lang']['pagi']."</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><select id=jmp>".$jm.'</select>:<select id=mmp>'.$mnt."</select></td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['jam'].' '.$_SESSION['lang']['selesai'].' '.$_SESSION['lang']['pagi']."</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><select id=jsp>".$jm.'</select>:<select id=msp>'.$mnt."</select></td>\r\n\t\t</tr>\r\n\t\r\n\t";
echo "\r\n\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['pagi'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtextnumber\" id=\"pg\"  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"10\" value=\"0\" style=\"width:150px;\" /> mm\r\n</td>\r\n</tr>\r\n\r\n";
echo "\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['jam'].' '.$_SESSION['lang']['mulai'].' '.$_SESSION['lang']['sore']."</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><select id=jms>".$jmt.'</select>:<select id=mms>'.$mnt."</select></td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['jam'].' '.$_SESSION['lang']['selesai'].' '.$_SESSION['lang']['sore']."</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><select id=jss>".$jmt.'</select>:<select id=mss>'.$mnt."</select></td>\r\n\t\t</tr>\r\n\t\r\n\t";
echo "\r\n\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['sore'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtextnumber\" id=\"sr\"  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"10\" value=\"0\" style=\"width:150px;\" /> mm</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['note'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"cttn\" name=\"cttn\" onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px;\" maxlength=\"45\" /></td>\r\n</tr>";

//echo '<tr><td>Jumlah Bayaran';
//echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"jmlbayar\" name=\"jmlbayar\" onkeypress=\"return angka_doang(event)\" style=\"width:150px;\" maxlength=\"45\" /></td>\r\n</tr>";

echo "<tr>
<input type=\"hidden\" class=\"myinputtext\" id=\"jmlbayar\" name=\"jmlbayar\" value=0/>
<td colspan=\"3\" id=\"tmbLheader\">\r\n<button class=\"mybutton\" id=\"dtlAbn\" onclick=\"saveData()\">";

//echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"cttn\" name=\"cttn\" onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px;\" maxlength=\"45\" /></td>\r\n</tr>\r\n<tr>\r\n\r\n\r\n\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n<button class=\"mybutton\" id=\"dtlAbn\" onclick=\"saveData()\">";
echo $_SESSION['lang']['save'];
echo '</button><button class="mybutton" id="cancelAbn" onclick="cancelSave()">';
echo $_SESSION['lang']['cancel'];
echo "</button><input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>