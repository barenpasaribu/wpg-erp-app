<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$karyawanid = $_POST['karyawanid'];
if ('' == $karyawanid) {
    echo "<option value=''></option";
} else {
    $str = "select nomor,nama,ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as umur,jeniskelamin,hubungankeluarga\r\n\t\t  from ".$dbname.".sdm_karyawankeluarga where \r\n\t\t  karyawanid=".$karyawanid.' and tanggungan=1';
    $res = mysql_query($str);
    $no = 0;
    $optKel = '<option value=0>Ybs/PIC</option>';
    while ($bar = mysql_fetch_object($res)) {
        if (23 < $bar->umur && 'Pasangan' != $bar->hubungankeluarga) {
            $optKel .= "<option value='".$bar->nomor."' style='background-color:red;'>".$bar->nama.'('.$bar->umur.'Th)-'.$bar->jeniskelamin.'</option>';
        } else {
            $optKel .= "<option value='".$bar->nomor."'>".$bar->nama.'('.$bar->umur.'Th)-'.$bar->jeniskelamin.'</option>';
        }
    }
    $sMedId = 'select distinct idmedical from '.$dbname.".datakaryawan where karyawanid='".$karyawanid."'";
    $qMedId = mysql_query($sMedId);
    $rMedId = mysql_fetch_assoc($qMedId);
    echo $optKel.'####'.$rMedId['idmedical'];
}

?>