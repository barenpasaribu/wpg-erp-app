<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tanggalsk = tanggalsystem($_POST['tanggalsk']);
$tanggalberlaku = tanggalsystem($_POST['tanggalberlaku']);
$oldgaji = $_POST['oldgaji'];
$newgaji = $_POST['newgaji'];
$penandatangan = $_POST['penandatangan'];
$tembusan1 = $_POST['tembusan1'];
$tembusan2 = $_POST['tembusan2'];
$tembusan3 = $_POST['tembusan3'];
$tembusan4 = $_POST['tembusan4'];
$tembusan5 = $_POST['tembusan5'];
$tipetransaksi = $_POST['tipetransaksi'];
$karyawanid = $_POST['karyawanid'];
$oldokasitugas = $_POST['oldokasitugas'];
$oldjabatan = $_POST['oldjabatan'];
$oldtipekaryawan = $_POST['oldtipekaryawan'];
$oldgolongan = $_POST['oldgolongan'];
$newlokasitugas = $_POST['newlokasitugas'];
$newjabatan = $_POST['newjabatan'];
$newgolongan = $_POST['newgolongan'];
$newtipekaryawan = $_POST['newtipekaryawan'];
$method = $_POST['method'];
$atasanbaru = $_POST['atasanbaru'];
if ('' == $atasanbaru) {
    $atasanbaru = 0;
}

$tjjabatan = $_POST['tjjabatan'];
$ketjjabatan = $_POST['ketjjabatan'];
$olddepartemen = $_POST['olddepartemen'];
$newdepartemen = $_POST['newdepartemen'];
$statustransaksi = $_POST['statustransaksi'];
$tjsdaerah = $_POST['tjsdaerah'];
$ketjsdaerah = $_POST['ketjsdaerah'];
$tjmahal = $_POST['tjmahal'];
$ketjmahal = $_POST['ketjmahal'];
$tjpembantu = $_POST['tjpembantu'];
$ketjpembantu = $_POST['ketjpembantu'];
$tjkota = $_POST['tjkota'];
$ketjkota = $_POST['ketjkota'];
$tjtransport = $_POST['tjtransport'];
$ketjtransport = $_POST['ketjtransport'];
$tjmakan = $_POST['tjmakan'];
$ketjmakan = $_POST['ketjmakan'];
$noskedit = $_POST['nosk'];
$paragraf1 = $_POST['paragraf1'];
$paragraf2 = $_POST['paragraf2'];
$namajabatan = $_POST['namajabatan'];
if ('insert' == $method) {
    $potSK = substr($_SESSION['empl']['lokasitugas'], 0, 4).strtoupper(substr($tipetransaksi, 0, 2)).substr($tanggalsk, 0, 4);
    $str = 'select nomorsk from '.$dbname.".sdm_riwayatjabatan\r\n      where  nomorsk like '".$potSK."%'\r\n\t  order by nomorsk desc limit 1";
    $notrx = 0;
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $notrx = substr($bar->nomorsk, 10, 5);
    }
    $notrx = (int) $notrx;
    $notrx = $notrx + 1;
    $notrx = str_pad($notrx, 5, '0', STR_PAD_LEFT);
    $notrx = $potSK.$notrx;
    $str = 'insert into '.$dbname.".sdm_riwayatjabatan (\r\n\t\t  `karyawanid`,`nomorsk`,`tanggalsk`,\r\n\t\t  `mulaiberlaku`,`darikodeorg`,\r\n\t\t  `darikodejabatan`,`daritipe`,`tipesk`,\r\n\t\t  `darikodegolongan`,`kekodeorg`,`kekodejabatan`,\r\n\t\t  `ketipekaryawan`,`kekodegolongan`,`darigaji`,\r\n\t\t  `kegaji`,`namadireksi`,`tembusan1`,`tembusan2`,\r\n\t\t  `tembusan3`,`tembusan4`,`updateby`,\r\n\t\t  `tjjabatan`,`ketjjabatan`,`tjsdaerah`,\r\n\t\t  `ketjsdaerah`,`tjmahal`,`ketjmahal`,\r\n                  `tjpembantu`, `ketjpembantu`,\r\n                  `tjkota`, `ketjkota`, \r\n                  `tjtransport`, `ketjtransport`, \r\n                  `tjmakan`, `ketjmakan`,                   \r\n                  `tembusan5`,`atasanbaru`,\r\n\t\t  `namajabatan`,`pg1`,`pg2`,`bagian`,`kebagian`,`statussk`\r\n\t\t  ) values(\r\n\t\t   ".$karyawanid.",'".$notrx."',".$tanggalsk.",\r\n\t\t   ".$tanggalberlaku.",'".$oldokasitugas."',\r\n\t\t   ".$oldjabatan.','.$oldtipekaryawan.",'".$tipetransaksi."',\r\n\t\t   '".$oldgolongan."','".$newlokasitugas."',".$newjabatan.",\r\n\t\t   ".$newtipekaryawan.",'".$newgolongan."',".$oldgaji.",\r\n\t\t   ".$newgaji.",'".$penandatangan."','".$tembusan1."','".$tembusan2."',\r\n\t\t   '".$tembusan3."','".$tembusan4."',".$_SESSION['standard']['userid'].",\r\n\t\t   ".$tjjabatan.','.$ketjjabatan.",\r\n                   ".$tjsdaerah.','.$ketjsdaerah.','.$tjmahal.','.$ketjmahal.",\r\n                   ".$tjpembantu.','.$ketjpembantu.",\r\n                   ".$tjkota.','.$ketjkota.",\r\n                   ".$tjtransport.','.$ketjtransport.", \r\n                   ".$tjmakan.','.$ketjmakan.",   \r\n                   '".$tembusan5."',\r\n\t\t   ".$atasanbaru.",'".$namajabatan."','".$paragraf1."','".$paragraf2."',\r\n                   '".$olddepartemen."','".$newdepartemen."','".$statustransaksi."'    \r\n\t\t  )";
} else {
    if ('delete' == $method) {
        $nosk = $_POST['nosk'];
        $str = 'delete from '.$dbname.".sdm_riwayatjabatan\r\n\t      where karyawanid=".$karyawanid." and nomorsk='".$nosk."'";
    } else {
        if ('update' == $method) {
            $str = 'update '.$dbname.".sdm_riwayatjabatan set\r\n\t\t  `tanggalsk`=".$tanggalsk.",\r\n\t\t  `mulaiberlaku`=".$tanggalberlaku.",\r\n\t\t  `darikodeorg`='".$oldokasitugas."',\r\n\t\t  `darikodejabatan`=".$oldjabatan.",\r\n\t\t  `daritipe`=".$oldtipekaryawan.",\r\n\t\t  `tipesk`='".$tipetransaksi."',\r\n\t\t  `statussk`='".$statustransaksi."',\r\n\t\t  `darikodegolongan`='".$oldgolongan."',\r\n\t\t  `kekodeorg`='".$newlokasitugas."',\r\n\t\t  `kekodejabatan`=".$newjabatan.",\r\n\t\t  `ketipekaryawan`=".$newtipekaryawan.",\r\n\t\t  `kekodegolongan`='".$newgolongan."',\r\n\t\t  `darigaji`=".$oldgaji.",\r\n\t\t  `kegaji`=".$newgaji.",\r\n\t\t  `namadireksi`='".$penandatangan."',\r\n\t\t  `tembusan1`='".$tembusan1."',\r\n\t\t  `tembusan2`='".$tembusan2."',\r\n\t\t  `tembusan3`='".$tembusan3."',\r\n\t\t  `tembusan4`='".$tembusan4."',\r\n\t\t  `updateby`=".$_SESSION['standard']['userid'].",\r\n\t\t  `bagian`='".$olddepartemen."',\r\n\t\t  `kebagian`='".$newdepartemen."',                      \r\n\t\t  `tjjabatan`=".$tjjabatan.",\r\n\t\t  `ketjjabatan`=".$ketjjabatan.",\r\n                      \r\n                  `tjsdaerah`=".$tjsdaerah.",\r\n                  `ketjsdaerah`=".$ketjsdaerah.",\r\n                  `tjmahal`=".$tjmahal.",\r\n                  `ketjmahal`=".$ketjmahal.",\r\n                  `tjpembantu`=".$tjpembantu.",\r\n                  `ketjpembantu`=".$ketjpembantu.",\r\n                  `tjkota`=".$tjkota.",\r\n                  `ketjkota`=".$ketjkota.",\r\n                  `tjtransport`=".$tjtransport.",\r\n                  `ketjtransport`=".$ketjtransport.", \r\n                  `tjmakan`=".$tjmakan.",\r\n                  `ketjmakan`=".$ketjmakan.",\r\n                      \r\n\t\t  `tembusan5`='".$tembusan5."',\r\n\t\t  `atasanbaru`=".$atasanbaru.",\r\n\t\t  `namajabatan`='".$namajabatan."',\r\n                  `pg1`='".$paragraf1."',\r\n                  `pg2`='".$paragraf2."'    \r\n\t\t  where `karyawanid`=".$karyawanid." and `nomorsk`='".$noskedit."'";
        }
    }
}

if (mysql_query($str)) {
} else {
    echo ' Gagal:'.addslashes(mysql_error($conn));
}

?>