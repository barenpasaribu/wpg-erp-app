<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$notransaksi = $_POST['notransaksi'];
$bayar = $_POST['bayar'];
$tglbayar = tanggalsystem($_POST['tglbayar']);
$karIddt = makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,karyawanid');
$whr = "karyawanid='".$karIddt[$notransaksi]."'";
$optTipe = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $whr);
$optLokasitugas = makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas', $whr);
$whrtorg = "kodeorganisasi='".$optLokasitugas[$karIddt[$notransaksi]]."'";
$optOrgCekTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe', $whrtorg);
if ('HOLDING' == $optOrgCekTipe[$optLokasitugas[$karIddt[$notransaksi]]]) {
    $kodeJurnal = 'MED03';
} else {
    if (0 == $optTipe[$karIddt[$notransaksi]]) {
        $kodeJurnal = 'MED01';
    } else {
        $kodeJurnal = 'MED02';
    }
}

$hre = "notransaksi='".$notransaksi."'";
$optTipe = makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,klaimoleh', $hre);
$optCekPost = makeOption($dbname, 'sdm_pengobatanht', 'notransaksi,posting', $hre);
$hre2 = "noreferensi='".$notransaksi."'";
$optCekPost2 = makeOption($dbname, 'keu_jurnalht', 'noreferensi,nojurnal', $hre2);
if ('1' == $optCekPost[$notransaksi]) {
    exit('error: This Transaction Number '.$notransaksi.' already posted');
}

if ('' != $optCekPost2[$notransaksi]) {
    exit('error: This Transaction Number '.$notransaksi.' already posted');
}

if ('0' == $optTipe[$notransaksi] || '1' == $optTipe[$notransaksi]) {
    $sJurnal = 'select distinct noakundebet,noakunkredit from '.$dbname.".keu_5parameterjurnal \r\n          where kodeaplikasi='MED' and jurnalid='".$kodeJurnal."'";
    $qJurnal = mysql_query($sJurnal);
    $rJurnal = mysql_fetch_assoc($qJurnal);
    $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodekelompok='".$kodeJurnal."'");
    $tmpKonter = fetchData($queryJ);
    $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
    $tmpNoJurnal = tanggalsystem($_POST['tglbayar']);
    $nojurnal = $tmpNoJurnal.'/'.substr($notransaksi, 0, 4).'/'.$kodeJurnal.'/'.$konter;
    $sInsJnr = 'insert into '.$dbname.".keu_jurnalht (nojurnal,kodejurnal,tanggal,tanggalentry,posting,totaldebet,totalkredit,amountkoreksi,noreferensi,autojurnal,matauang,kurs,revisi) \r\n              values ('".$nojurnal."','".$kodeJurnal."','".$tglbayar."','".date('Ymd')."','1','".$bayar."','".$bayar * -1 ."','0','".$notransaksi."','1','IDR','1','0')";
    if (mysql_query($sInsJnr)) {
        $sInsJnr2 = 'insert into '.$dbname.".keu_jurnaldt (nojurnal,tanggal,nourut,noakun,keterangan,jumlah,matauang,kurs,kodeorg,noreferensi,noaruskas,revisi,nik) \r\n              values ('".$nojurnal."','".$tglbayar."','1','".$rJurnal['noakundebet']."','Klaim Pengobatan ".$kodeJurnal.' Notransaksi '.$notransaksi."','".$bayar."','IDR','1','".substr($notransaksi, 0, 4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."')";
        $sInsJnr2 .= ",('".$nojurnal."','".$tglbayar."','2','".$rJurnal['noakunkredit']."','Klaim Pengobatan ".$kodeJurnal.' Notransaksi '.$notransaksi."','".$bayar * -1 ."','IDR','1','".substr($notransaksi, 0, 4)."','".$notransaksi."','','0','".$karIddt[$notransaksi]."');";
        if (!mysql_query($sInsJnr2)) {
            exit('error:'.mysql_error($conn).'___'.$sInsJnr2);
        }

        $supdteCounter = 'update '.$dbname.".keu_5kelompokjurnal set nokounter='".((int) ($tmpKonter[0]['nokounter']) + 1)."' \r\n                            where kodekelompok='".$kodeJurnal."'";
        if (!mysql_query($supdteCounter)) {
            exit('error:'.mysql_error($conn).'___'.$supdteCounter);
        }
    } else {
        exit('error:'.mysql_error($conn).'___'.$sInsJnr);
    }
}

$str = 'update '.$dbname.'.sdm_pengobatanht set jlhbayar='.$bayar.",\r\n      tanggalbayar=".$tglbayar.",\r\n\t  posting=1\r\n\t  where notransaksi='".$notransaksi."'";
if (mysql_query($str)) {
} else {
    echo ' Gagal '.addslashes(mysql_error($conn));
}

?>