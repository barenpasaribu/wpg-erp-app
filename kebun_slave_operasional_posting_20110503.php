<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$queryH = selectQuery($dbname, 'kebun_aktifitas', '*', "notransaksi='".$param['notransaksi']."'");
$dataH = fetchData($queryH);
$queryD = selectQuery($dbname, 'kebun_prestasi', '*', "notransaksi='".$param['notransaksi']."'");
$dataD = fetchData($queryD);
$queryAbs = selectQuery($dbname, 'kebun_kehadiran', 'jhk,umr,insentif', "notransaksi='".$param['notransaksi']."'");
$dataAbs = fetchData($queryAbs);
$error0 = '';
if (1 === $dataH[0]['jurnal']) {
    $error0 .= $_SESSION['lang']['errisposted'];
}

if ('' !== $error0) {
    echo "Data Error :\n".$error0;
    exit();
}

$error1 = '';
if (0 === count($dataH)) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}

if (0 === count($dataD)) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}

if ('' !== $error1) {
    echo "Data Error :\n".$error1;
    exit();
}

$costRawat = 0;
$totalHk = 0;
if (!empty($dataAbs)) {
    foreach ($dataAbs as $row) {
        $costRawat += $row['jhk'] * $row['umr'] + $row['insentif'];
        $totalHk += $row['jhk'];
    }
}

if (empty($dataAbs) || $totalHk !== $dataD[0]['jumlahhk']) {
    echo 'Warning : HK Prestasi belum teralokasi dengan lengkap';
    exit();
}

$kodeJurnal = 'M0';
$queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
$resParam = fetchData($queryParam);
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$tmpNoJurnal = explode('/', $param['notransaksi']);
$nojurnal = $tmpNoJurnal[0].'/'.$tmpNoJurnal[1].'/'.$kodeJurnal.'/'.$konter;
$dataRes['header'] = [];
$dataRes['detail'] = [];
$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => 'M0', 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
$i = 0;
$whereKeg = '';
foreach ($dataD as $row) {
    if (0 === $i) {
        $whereKeg .= "kodekegiatan='".$row['kodekegiatan']."'";
    } else {
        $whereKeg .= " or kodekegiatan='".$row['kodekegiatan']."'";
    }

    ++$i;
}
$queryKeg = selectQuery($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,noakun', $whereKeg);
$tmpRes = fetchData($queryKeg);
$resKeg = [];
foreach ($tmpRes as $row) {
    $resKeg[$row['kodekegiatan']]['nama'] = $row['namakegiatan'];
    $resKeg[$row['kodekegiatan']]['akun'] = $row['noakun'];
}
$noUrut = 1;
$totalJumlah = 0;
foreach ($dataD as $row) {
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $resKeg[$row['kodekegiatan']]['akun'], 'keterangan' => 'Pemeliharaan '.$resKeg[$row['kodekegiatan']]['nama'], 'jumlah' => $costRawat + $row['upahpremi'] + $row['upahkerja'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $row['kodeorg'], 'kodekegiatan' => $row['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => ''];
    $totalJumlah += $row['jumlahhk'] * $row['umr'] + $row['upahpremi'] + $row['upahkerja'];
    ++$noUrut;
}
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $resParam[0]['noakunkredit'], 'keterangan' => 'Pemeliharaan '.$dataH[0]['tipetransaksi'], 'jumlah' => $totalJumlah * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => '', 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => ''];
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;
$errorDB = '';
$queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
if (!mysql_query($queryH)) {
    $errorDB .= 'Header :'.mysql_error()."\n";
}

if ('' === $errorDB) {
    foreach ($dataRes['detail'] as $key => $dataDet) {
        $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
        if (!mysql_query($queryD)) {
            $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
        }
    }
    $queryJ = selectQuery($dbname, 'kebun_aktifitas', 'jurnal', "notransaksi='".$param['notransaksi']."'");
    $isJ = fetchData($queryJ);
    if (1 === $isJ[0]['jurnal']) {
        $errorDB .= 'Data posted by another user';
    } else {
        $queryToJ = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 1], "notransaksi='".$dataH[0]['notransaksi']."'");
        if (!mysql_query($queryToJ)) {
            $errorDB .= 'Posting Mark Error :'.mysql_error()."\n";
        }

        $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if (!mysql_query($queryKonter)) {
            $errorDB .= 'Update Counter Error :'.mysql_error()."\n";
        }
    }
}

if ('' !== $errorDB) {
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
    $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if (!mysql_query($queryRB)) {
        $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n";
    }

    if (!mysql_query($queryRB2)) {
        $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n";
    }

    if (!mysql_query($queryRBKonter)) {
        $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n";
    }

    echo "DB Error :\n".$errorDB;
    exit();
}

?>