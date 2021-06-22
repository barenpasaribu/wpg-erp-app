<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$karyawanid = $_POST['karyawanid'];
$lokasitugas = $_POST['lokasitugas'];
$periode = $_POST['periode'];
$dari = $_POST['dari'];
$sampai = $_POST['sampai'];
$hak = $_POST['hak'];
$periodelalu = $periode - 1;
$str = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$karyawanid." and periodecuti='".$periodelalu."'";
$sisalalu = 0;
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $sisalalu = $bar->sisa;
}
if ($sisalalu < 0) {
    $hak += $sisalalu;
}

$strx = 'select sum(jumlahcuti) as diambil from '.$dbname.".sdm_cutidt\r\n           where karyawanid=".$karyawanid."\r\n               and periodecuti='".$periode."'";
$diambil = 0;
$resx = mysql_query($strx);
while ($barx = mysql_fetch_object($resx)) {
    $diambil = $barx->diambil;
}
$hak -= $diambil;
$str = 'update '.$dbname.".sdm_cutiht \r\n      set dari=".$dari.",\r\n\t  sampai=".$sampai.",\r\n\t  hakcuti=".$hak."\r\n     where \r\n      kodeorg='".$lokasitugas."'\r\n\t  and karyawanid=".$karyawanid."\r\n\t  and periodecuti='".$periode."'";
mysql_query($str);
if (mysql_affected_rows($conn) < 1) {
    $str = 'insert into '.$dbname.".sdm_cutiht(kodeorg,`karyawanid`,\r\n      `periodecuti`,`dari`,`sampai`,`hakcuti`,`sisa`)\r\n\t  values(\r\n\t  '".$lokasitugas."',".$karyawanid.",'".$periode."',\r\n\t  ".$dari.','.$sampai.','.$hak.','.$hak."\r\n\t  )";
    if (mysql_query($str)) {
    } else {
        echo addslashes(mysql_error($conn));
    }
}

?>