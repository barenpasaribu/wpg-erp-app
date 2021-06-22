<?php
session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".pabrik_5pot_fraksi set \r\n\t potongan=".$_POST['potongan']." \r\n\t where kodeorg='".$_POST['kodeorg']."' and kodefraksi='".$_POST['kode']."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5pot_fraksi where kodeorg="'.$_POST['kodeorg'].'" AND kodefraksi="'.$_POST['kode'].'"');
        $cek   = mysql_fetch_assoc($qcek);
        if($cek['total'] != 0) {
            echo 'DB error : data sudah ada';
            exit();
        }
        $str = 'insert into '.$dbname.".pabrik_5pot_fraksi (kodeorg,kodefraksi,potongan) values('".$_POST['kodeorg']."','".$_POST['kode']."',".$_POST['potongan'].');';
        if (mysql_query($str)) {
        } else {
            exit('Error:'.mysql_error($conn));
        }

        break;
    // case 'delete':
    //     $str = 'delete from '.$dbname.".sdm_5catu\r\n\t where kodeorg='".$kodeorg."' and kelompok='".$kode."'\r\n\t and tahun=".$tahun;
    //     if (mysql_query($str)) {
    //     } else {
    //         echo ' Gagal,'.addslashes(mysql_error($conn));
    //     }

    //     break;
    default:
        break;
}

$sFraksi = 'select distinct kode,keterangan,keterangan1 from '.$dbname.'.pabrik_5fraksi order by keterangan asc';
$qFraksi = mysql_query($sFraksi);
while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
    if ('EN' === $_SESSION['language']) {
        $kodeNama[$rFraksi['kode']] = $rFraksi['keterangan1'];
    } else {
        $kodeNama[$rFraksi['kode']] = $rFraksi['keterangan'];
    }
}
$str1 = 'select * from '.$dbname.".pabrik_5pot_fraksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodefraksi";
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td>'.$kodeNama[$bar1->kodefraksi].'</td><td align=right>'.$bar1->potongan."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodefraksi."','".$bar1->potongan."','update');\"></td></tr>";
}

?>