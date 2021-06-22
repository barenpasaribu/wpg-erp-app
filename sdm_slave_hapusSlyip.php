<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$karyawanid = $_POST['karyawanid'];
$komponen = $_POST['komponen'];
$tipekaryawan = $_POST['tipekaryawan'];
$str = 'select periode from '.$dbname.".setup_periodeakuntansi \r\n      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."'\r\n      and tutupbuku=1";
$res = mysql_query($str);
if (0 < mysql_numrows($res)) {
    echo 'Error:Periode Akuntansi sudah tutup buku';
} else {
    $addwhere = '';
    if ('all' != $komponen) {
        $addwhere .= ' and idkomponen='.$komponen;
    }

    if ('all' != $karyawanid) {
        $addwhere .= " and karyawanid='".$karyawanid."'";
    } else {
        if ('all' != $tipekaryawan) {
            $addwhere .= ' and karyawanid in(select karyawanid from '.$dbname.".datakaryawan \r\n               where sistemgaji='".$tipekaryawan."')";
        }
    }

    $str = 'delete from '.$dbname.".sdm_gaji where  periodegaji='".$periode."' and\r\n        kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$addwhere;
    if (mysql_query($str)) {
        echo 'Done';
    } else {
        echo mysql_error($conn);
    }
}

?>