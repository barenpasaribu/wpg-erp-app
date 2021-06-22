<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
if ('resetData' === $_POST['proses']) {
    $b = 0;
    $kodePt = $_POST['kodePt'];
    $sCek = 'select tutupbuku from '.$dbname.".setup_periodeakuntansi where \r\n        kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$kodePt."')";
    $qCek = mysql_query($sCek);
    $brs = mysql_num_rows($qCek);
    for ($a = 0; $a < $brs; ++$a) {
        $rBrs = mysql_fetch_row($qCek);
        if (0 === $rBrs[$a]) {
            ++$b;
        }
    }
    if (0 !== $b) {
        echo 'warning:Organisasi di Sub '.$kodePt.',belum tutup Buku ';
        exit();
    }

    if (0 === $b) {
        $sUp = 'update '.$dbname.".keu_5kelompokjurnal set nokounter=0 where kodeorg='".$kodePt."'";
        if (mysql_query($sUp)) {
            echo '1';
        } else {
            echo ' Gagal'.addslashes(mysql_error($conn));
            exit(0);
        }
    }
}

?>