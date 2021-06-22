<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zLib.php';
include 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
echo "<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<script language=javascript1.2 src='js/vhc_acara_laka.js'></script>\r\n<script language=javascript1.2 src='js/zMaster.js'></script>\r\n";
// $str = "select d.nik, d.karyawanid , d.namakaryawan,s.namajabatan  from $dbname.datakaryawan d ".
//     "inner join $dbname.sdm_5jabatan s on s.kodejabatan=d.kodejabatan ".
//     "where d.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and s.alias like '%Security%' ". //kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Security%') order by namakaryawan asc";
//     "order by d.namakaryawan"; 
$str = sdmJabatanQuery("lokasitugas='".$_SESSION['empl']['lokasitugas']."' and lower(alias) like '%security%'");
$res = mysql_query($str);
$optkarsecurity = '';
while ($bar = mysql_fetch_object($res)) {
    $optkarsecurity .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' ('.$bar->namajabatan.')</option>';
}
// $str = 'select nik, karyawanid , namakaryawan  from '.$dbname.".datakaryawan 
// where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in 
// (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Manager%') order by namakaryawan asc";
$str = sdmJabatanQuery("lokasitugas='".$_SESSION['empl']['lokasitugas']."' and lower(alias) like '%manager%'"); 
$res = mysql_query($str);
$optkarmanager = '';
while ($bar = mysql_fetch_object($res)) {
    $optkarmanager .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' ('.$bar->namajabatan.')</option>';
} 
// $str = 'select nik, karyawanid , namakaryawan  from '.$dbname.".datakaryawan 
// where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in 
// (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Mekanik%' or alias like '%Bengkel%') order by namakaryawan asc";
$str = sdmJabatanQuery("lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (lower(alias) like '%mekanik%' or lower(alias) like '%bengkel%')");
$res = mysql_query($str);
$optkarmekanik = '';
while ($bar = mysql_fetch_object($res)) {
    $optkarmekanik .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' ('.$bar->namajabatan.')</option>';
}
// $str = 'select nik, karyawanid , namakaryawan  from '.$dbname.".datakaryawan 
// where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in 
// (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Kepala Bengkel%') order by namakaryawan asc";
$str = sdmJabatanQuery("lokasitugas='".$_SESSION['empl']['lokasitugas']."' and lower(alias) like '%kepala bengkel%'");
$res = mysql_query($str);
$optkarworkshop = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optkarworkshop .= "<option value='".$bar->karyawanid."'>".$bar->nik.'-'.$bar->namakaryawan.' ('.$bar->namajabatan.')</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='TRAKSI' order by namaorganisasi desc";
$res = mysql_query($str);
$opttraksi = '';
while ($bar = mysql_fetch_object($res)) {
    $opttraksi .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$optkegiatan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('ID' === $_SESSION['language']) {
    $fild = 'namaakun';
} else {
    $fild = 'namaakun1';
}

$i = 'select distinct a.noakun,'.$fild.' from '.$dbname.".setup_kegiatan a left join \r\n                    ".$dbname.".keu_5akun b on a.noakun=b.noakun \r\n                    where kelompok in (select distinct kodeklp from ".$dbname.'.setup_klpkegiatan order by kodeklp) order by noakun asc';
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    $optkegiatan .= "<option value='".$d['noakun']."'>".$d['noakun'].' - '.$d[$fild].'</option>';
}
echo "\r\n\r\n";
OPEN_BOX();
echo "<fieldset style='float:left;'>";
echo '<legend>'.$_SESSION['lang']['vhc_kegiatan'].'</legend>';
echo "<table border=0 cellpadding=1 cellspacing=1>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><input type=text class=myinputtext id=notransaksi disabled style=width:150px; /></td></tr>\r\n\t\t\t\t\t\r\n\t\t\t\t</tr>\r\n                    \r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><input type=text class=myinputtext id=tanggal  name=tanggal onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>\r\n\t\t\t\t\t\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kodetraksi']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><select id=kodetraksi  name=kodetraksi style=width:150px; onchange=\"get_kd('','')\"><option value=>".$_SESSION['lang']['all'].'</option>'.$opttraksi."</select></td>\r\n                                            \r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kendaraan']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        <!--<td><select id=kde_vhc name=kde_vhc style=width:150px;><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td>-->\r\n                                        <td><select id=kde_vhc  name=kde_vhc style=width:150px; onchange=\"get_kendaraan('','','','','','')\"><option value=>".$_SESSION['lang']['all'].'</option>'.$_SESSION['lang']['pilihdata']."</select></td>\r\n\t\t\t\t</tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['operator']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><select id=operator  name=operator style=width:150px;><option value=>".$_SESSION['lang']['all'].'</option>'.$_SESSION['lang']['pilihdata']."</select></td>\r\n                                </tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['security']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                       <td><select id=security  style=\"width:150px;\">".$optkarsecurity."</select></td>\r\n                                        \r\n                                </tr>\r\n\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['mekanik']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><select id=karymekanik  style=\"width:150px;\">".$optkarmekanik."</select></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['managerunit']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        
<td><select id=managerunit  style=\"width:150px;\">".$optkarmanager."</select></td>\r\n                                </tr>\r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kaworkshop']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><select id=karyworkshop  style=\"width:150px;\">".$optkarworkshop."</select></td>\r\n                                    </tr>   \r\n                                <tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['kronologiskejadian']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><textarea id=kronologiskejadian  onkeypress=\"return tanpa_kutip(event);\"></textarea></td>\r\n\t\t\t\t</tr>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<td>".$_SESSION['lang']['akibatkejadian']."</td>\r\n\t\t\t\t\t<td>:</td>\r\n                                        <td><textarea id=akibatkejadian  onkeypress=\"return tanpa_kutip(event);\"></textarea></td>\r\n                                </tr>\r\n\t\t\t\t<tr><td colspan=2></td>\r\n\t\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n                                                <button class=mybutton onclick=new_acara_laka()>New</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t\r\n\t\t\t</table></fieldset>\r\n\t\t\t\t\t<input type=hidden id=method value='insert'>\r\n                                        <input type=hidden id=notransaksi value='insert'>";
CLOSE_BOX();
echo "\r\n\r\n\r\n";
OPEN_BOX();
echo '<fieldset  style=float:left;clear:both;><legend>'.$_SESSION['lang']['find'].' '.$_SESSION['lang']['data'].'</legend>';
echo '<table>';
echo '<tr><td>'.$_SESSION['lang']['notransaksi']."</td><td><input type=text maxlength=45 id=noTransCr onkeypress=\"return tanpa_kutip(event);\" onkeyup='var key=getKey(event);if(key==13){loadData(0);}' class=myinputtext style=\"width:150px;\"></td>";
echo '<tr><td colspan=4><button class=mybutton onclick=loadData(0)>'.$_SESSION['lang']['find'].'</button></td></tr>';
echo '</table></fieldset>';
echo "<fieldset style=float:left;clear:both;>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData(0)</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>