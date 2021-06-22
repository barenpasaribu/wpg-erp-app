<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_rapel_kebun.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['rapel']);
$optPeriode = '<option value=""></option>';
$sGp = 'select DISTINCT periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0 order by periode desc limit 0,6";
$qGp = mysql_query($sGp) ;
while ($rGp = mysql_fetch_assoc($qGp)) {
    $optPeriode .= '<option value='.$rGp['periode'].'>'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
}
if ('HOLDING' == $_SESSION['org'][tipelokasitugas]) {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where ((tanggalkeluar is NULL) or tanggalkeluar >'".date('Y').'-01-01'."')\r\n\t  and tipekaryawan!=5 and lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n\t  order by namakaryawan";
} else {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where ((tanggalkeluar is NULL) or tanggalkeluar >'".date('Y').'-01-01'."')\r\n\t  and tipekaryawan!=5 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t  order by namakaryawan";
}

$res1 = mysql_query($str1, $conn);
$optIdKaryawan = '<option value=""></option>';
while ($bar1 = mysql_fetch_object($res1)) {
    $optIdKaryawan .= '<option value='.$bar1->karyawanid.'>'.$bar1->namakaryawan.'</option>';
    $nama[$bar1->karyawanid] = $bar1->namakaryawan;
}
$strKom = 'select * from '.$dbname.".sdm_ho_component where id in('14','24')";
$resKom = mysql_query($strKom, $conn);
$optKom = '';
while ($bar1 = mysql_fetch_object($resKom)) {
    $optKomponen .= '<option value='.$bar1->id.'>'.$bar1->name.'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n     <tr>\r\n\t \t<td>".$_SESSION['lang']['periodegaji']."</td>\r\n\t \t<td><select id=\"periodegaji\" name=\"periodegaji\" style=\"width:150px;\" onchange=showPremi1(this.options[this.selectedIndex].value)>".$optPeriode."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t \t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t \t<td><select id=\"idkaryawan\" name=\"idkaryawan\" style=\"width:200px\">".$optIdKaryawan."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t \t<td>".$_SESSION['lang']['rapel']."</td>\r\n\t\t<td><input type=text id=upahpremi size=10 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=10 value=0></td>\r\n\t </tr>\r\n     <tr>\r\n\t \t<td>".$_SESSION['lang']['komponenpayroll']."</td>\r\n\t \t<td><select id=\"komponenpayroll\" name=\"komponenpayroll\" style=\"width:150px\">".$optKomponen."</select></td>\r\n\t </tr>\r\n\t </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['list']);
$strJ = 'select * from '.$dbname.'.sdm_5jabatan';
$resJ = mysql_query($strJ, $conn);
while ($barJ = mysql_fetch_object($resJ)) {
    $jab[$barJ->kodejabatan] = $barJ->namajabatan;
}
echo '<div>';
$strRes = 'select a.*, b.kodejabatan, b.lokasitugas from '.$dbname.".sdm_gaji a \r\n\tleft join ".$dbname.".datakaryawan b\r\n\ton a.karyawanid = b.karyawanid\r\n\twhere a.idkomponen in ('14','24') and  b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'\r\n\torder by a.karyawanid";
$resRes = mysql_query($strRes);
echo ''.$_SESSION['lang']['periode'].' : '."<select id=periodegaji2 style='width:200px;' onchange=showPremi2(this.options[this.selectedIndex].value)>".$optPeriode.'</select>';
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jabatan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['upahpremi']."</td>\r\n\t\t\t<td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>