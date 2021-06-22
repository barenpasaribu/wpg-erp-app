<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/kebun_premiPengawas.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['pendapatanlainkaryawan']);

$optPeriode = '<option value=""></option>';
/*
$sGp = 'select DISTINCT periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0 order by periode desc limit 0,6";
*/
$sGp = 'select DISTINCT periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0 and tanggalmulai <= '".$_SESSION['org']['period']['start']."' and tanggalsampai >= '".$_SESSION['org']['period']['start']."'";
$qGp = mysql_query($sGp) ;
while ($rGp = mysql_fetch_assoc($qGp)) {
    $optPeriode .= '<option value='.$rGp['periode'].'>'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
}

// cek apakah periode penggajian sudah tutup, ambil tanggal mulai periode gajian
$tanggalbatas = '';
$sCekPeriode = 'select distinct * from '.$dbname.".sdm_5periodegaji where tanggalmulai <= '".$_SESSION['org']['period']['start']."' and tanggalsampai >= '".$_SESSION['org']['period']['start']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 and jenisgaji='H'";
$qCekPeriode = mysql_query($sCekPeriode, $conn);
if (mysql_num_rows($qCekPeriode)>0) {
	$aktif2 = 1;
} else {
	$aktif2 = 0;
}
if ($aktif2 == 0) {
	exit(' Payroll period has been closed');
} else {
	while ($bar1 = mysql_fetch_object($qCekPeriode)) {
		$tanggalbatas = $bar1->tanggalmulai;
	}
}

// date('Y').'-01-01'
if ('HOLDING' == $_SESSION['org'][tipelokasitugas]) {
    $str1 = 'select * from '.$dbname.".datakaryawan where ((tanggalkeluar='0000-00-00' or tanggalkeluar is NULL) or tanggalkeluar >'".$tanggalbatas."') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";
} else {
    $str1 = 'select * from '.$dbname.".datakaryawan\r\n      where ((tanggalkeluar='0000-00-00' or tanggalkeluar is NULL) or tanggalkeluar >'".$tanggalbatas."') and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' order by namakaryawan";
}

$res1 = mysql_query($str1, $conn);
$optIdKaryawan = '<option value=""></option>';
while ($bar1 = mysql_fetch_object($res1)) {
    $optIdKaryawan .= '<option value='.$bar1->karyawanid.'>'.$bar1->nik.' - '.$bar1->namakaryawan.'</option>';
    $nama[$bar1->karyawanid] = $bar1->namakaryawan;
}
//$strKom = 'select * from '.$dbname.".sdm_ho_component where plus='1' and type='additional' and id not in ('6','7','17','57') order by id"; 
// JKK,JKM,Lembur,Tunj.BPJS Kes
$strKom = 'select * from '.$dbname.".sdm_ho_component where ispendlain=1 order by id"; 
$resKom = mysql_query($strKom, $conn);
$optKom = '';
while ($bar1 = mysql_fetch_object($resKom)) {
    $optKomponen .= '<option value='.$bar1->id.'>'.$bar1->name.'</option>';
}
echo "<fieldset style='width:100%;'>\r\n    EN: Make sure the entire payroll process is carried out either daily or monthly based,if not then the data will be replaced<br>\r\n    ID:Pastikan proses penggajian sudah dilaksanakan keseluruhan baik yang berbasis harian maupun bulanan, jika belum maka data ini akan tertimpa.    \r\n<table>\r\n     <tr>\r\n                <td>".$_SESSION['lang']['periodegaji']."</td>\r\n                <td><select id=\"periodegaji\" name=\"periodegaji\" style=\"width:100%;\" onchange=showPremi1(this.options[this.selectedIndex].value)>".$optPeriode."</select></td>\r\n         </tr>\r\n         <tr>\r\n                <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                <td><select id=\"idkaryawan\" name=\"idkaryawan\" style=\"width:100%\">".$optIdKaryawan."</select></td>\r\n         </tr>\r\n         <tr>\r\n                <td>".$_SESSION['lang']['upahpremi']."</td>\r\n                <td><input type=text id=upahpremi size=10 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=10 value=0 style=\"width:100%\"></td>\r\n         </tr>\r\n     <tr>\r\n                <td>".$_SESSION['lang']['komponenpayroll']."</td>\r\n                <td><select id=\"komponenpayroll\" name=\"komponenpayroll\" style=\"width:100%\">".$optKomponen."</select></td>\r\n         </tr>\r\n         </table>\r\n         <input type=hidden id=method value='insert'>\r\n         <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n         <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n         </fieldset>";
echo open_theme($_SESSION['lang']['list']);
$strJ = 'select * from '.$dbname.'.sdm_5jabatan';
$resJ = mysql_query($strJ, $conn);
while ($barJ = mysql_fetch_object($resJ)) {
    $jab[$barJ->kodejabatan] = $barJ->namajabatan;
}
echo '<div>';
$strRes = 'select a.*, b.kodejabatan, b.lokasitugas from '.$dbname.".sdm_gaji a \r\n        left join ".$dbname.".datakaryawan b\r\n        on a.karyawanid = b.karyawanid\r\n        where a.idkomponen not in ('6','7','57') and  b.lokasitugas = '".$_SESSION['empl']['lokasitugas']."'\r\n        order by a.karyawanid";
$resRes = mysql_query($strRes);
echo ''.$_SESSION['lang']['periode'].' : '.'<select id=periodegaji2 width=100% onchange=showPremi2(this.options[this.selectedIndex].value)>'.$optPeriode.'</select>';
echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                 <tr class=rowheader>\r\n                    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                        <td>".$_SESSION['lang']['jabatan']."</td>\r\n                        <td>".$_SESSION['lang']['periode']."</td>\r\n                        <td>".$_SESSION['lang']['tipepremi']."</td>\r\n                        <td>".$_SESSION['lang']['upahpremi']."</td>\r\n                        <td>*</td></tr>\r\n                 </thead>\r\n                 <tbody id=container>";
echo "\t \r\n                 </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\r\n                 </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>