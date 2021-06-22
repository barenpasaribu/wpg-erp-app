<?php
/*
Versi MIG, jurnal panen tidak dipakai
FA-20190703
*/

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$queryH = selectQuery($dbname, 'kebun_aktifitas', '*', "notransaksi='".$param['notransaksi']."'");
$dataH = fetchData($queryH);
$tgl = str_replace('-', '', $dataH[0]['tanggal']);
if ($tgl < $_SESSION['org']['period']['start']) {
    exit('Error:Tanggal diluar periode aktif');
}

$queryD = selectQuery($dbname, 'kebun_prestasi', '*', "notransaksi='".$param['notransaksi']."'");
$dataD = fetchData($queryD);
$error0 = '';
if (1 == $dataH[0]['jurnal']) {
    $error0 .= $_SESSION['lang']['errisposted'];
}

if ('' != $error0) {
    echo "Data Error :\n".$error0;
    exit();
}

$error1 = '';
if (0 == count($dataH)) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}

if (0 == count($dataD)) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}

if ('' != $error1) {
    echo "Data Error :\n".$error1;
    exit();
}

$nikKary = '';
foreach ($dataD as $row) {
    if ('' != $nikKary) {
        $nikKary .= ',';
    }

    $nikKary .= $row['nik'];
}
$karyList = makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas', 'karyawanid in ('.$nikKary.')');
$kodeJurnal = 'PNN01';
$queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit,noakundebet', " jurnalid='".$kodeJurnal."'");
$resParam = fetchData($queryParam);
$akunkredit = $resParam[0]['noakunkredit'];
$akundebet = $resParam[0]['noakundebet'];
$kodekegiatan = $akundebet.'01';
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$tmpNoJurnal = explode('/', $param['notransaksi']);
$nojurnal = $tmpNoJurnal[0].'/'.$tmpNoJurnal[1].'/'.$kodeJurnal.'/'.$konter;
$dataRes['header'] = [];
$dataRes['detail'] = [];
$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
$i = 0;
$noUrut = 1;
$totalJumlah = 0;
$totalDetail = [];
foreach ($dataD as $row) {
    $tmpJumlah = $row['jumlahhk'] * $row['umr'] + $row['upahpremi'] + $row['upahkerja'];
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'Potong Buah', 'jumlah' => $tmpJumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodeorg'], 0, 4), 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $row['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeorg'], 'revisi' => '0'];
    $totalJumlah += $row['jumlahhk'] * $row['umr'] + $row['upahpremi'] + $row['upahkerja'];
    if (!isset($totalDetail[$karyList[$row['nik']]])) {
        $totalDetail[$karyList[$row['nik']]] = $tmpJumlah;
    } else {
        $totalDetail[$karyList[$row['nik']]] += $tmpJumlah;
    }

    ++$noUrut;
}
$kebunList = '';
foreach ($totalDetail as $kebun => $cost) {
    if ('' != $kebunList) {
        $kebunList .= ',';
    }

    $kebunList .= "'".$kebun."'";
}
$whereAkun = "jenis='intra' and kodeorg in (".$kebunList.')';
$queryAkun = selectQuery($dbname, 'keu_5caco', 'kodeorg,akunpiutang,akunhutang', $whereAkun);
$akunIntraco = fetchData($queryAkun);
$optAkunIntra = [];
foreach ($akunIntraco as $row) {
    $optAkunIntra[$row['kodeorg']] = ['piutang' => $row['akunpiutang'], 'hutang' => $row['akunhutang']];
}
foreach ($totalDetail as $kebun => $cost) {
    if ($kebun == $_SESSION['empl']['lokasitugas']) {
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'Potong Buah', 'jumlah' => $cost * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => '', 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    } else {
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $optAkunIntra[$kebun]['hutang'], 'keterangan' => 'Potong Buah', 'jumlah' => $cost * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => '', 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    }

    ++$noUrut;
}
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;
foreach ($totalDetail as $kebun => $cost) {
    if ($kebun != $_SESSION['empl']['lokasitugas'] && !isset($optAkunIntra[$kebun])) {
        exit('Warning, Account Intraco for '.$kebun." is not set.\nData couldn't be posted");
    }
}
$errorDB = '';
// Jurnal panen header - tdk dipakai
/*
$queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
if (!mysql_query($queryH)) {
    $errorDB .= 'Header :'.mysql_error()."\n test";
}
*/
if ('' == $errorDB) {
    foreach ($dataRes['detail'] as $key => $dataDet) {
        // Jurnal panen detail - tdk dipakai
		/*
		$queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
        if (!mysql_query($queryD)) {
            $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n ini yang error";
        }
		*/
    }
    $queryJ = selectQuery($dbname, 'kebun_aktifitas', 'jurnal', "notransaksi='".$param['notransaksi']."'");
    $isJ = fetchData($queryJ);
    if (1 == $isJ[0]['jurnal']) {
        $errorDB .= 'Data posted by another user';
    } else {
        $queryToJ = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 1], "notransaksi='".$dataH[0]['notransaksi']."'");
        if (!mysql_query($queryToJ)) {
            $errorDB .= 'Posting Mark Error :'.mysql_error()."\n";
        }
		
		/*
        $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if (!mysql_query($queryKonter)) {
            $errorDB .= 'Update Counter Error :'.mysql_error()."\n".$errorDB.'___'.$queryKonter;
        }
		*/
    }
}

if ('' != $errorDB) {
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

    echo "DB Error :\n".$errorDB.'___'.$queryRB2;
    exit();
}

/*
jurnalIntraco($dbname, $param, $totalDetail, $optAkunIntra, $kodekegiatan, $akundebet, 'Potong Buah', $dataRes);
function jurnalIntraco($dbname, $param, $costDetail, $optAkunIntra, $kodeKeg, $akunKeg, $nameKeg, $dataRes)
{
    $dataIntraco = [];
    $i = 0;
    foreach ($costDetail as $kebun => $cost) {
        if ($kebun != $_SESSION['empl']['lokasitugas']) {
            $dataIntraco[$kebun]['header'] = $dataRes['header'];
            $kodeJurnal = 'PNN01';
            $queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
            $resParam = fetchData($queryParam);
            $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter'] + 1 + $i, 3);
            $tmpNoJurnal = explode('/', $param['notransaksi']);
            $nojurnal = $tmpNoJurnal[0].'/'.$kebun.'/'.$kodeJurnal.'/'.$konter;
            $dataIntraco[$kebun]['header']['nojurnal'] = $nojurnal;
            $dataIntraco[$kebun]['header']['totaldebet'] = $cost;
            $dataIntraco[$kebun]['header']['totalkredit'] = $cost;
            $dataIntraco[$kebun]['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataIntraco[$kebun]['header']['tanggal'], 'nourut' => 1, 'noakun' => $optAkunIntra[$kebun]['piutang'], 'keterangan' => $nameKeg.' '.$_SESSION['empl']['lokasitugas'], 'jumlah' => $cost, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kebun, 'kodekegiatan' => $kodeKeg, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $param['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            $dataIntraco[$kebun]['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataIntraco[$kebun]['header']['tanggal'], 'nourut' => 2, 'noakun' => $akunKeg, 'keterangan' => $nameKeg.' '.$_SESSION['empl']['lokasitugas'], 'jumlah' => $cost * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kebun, 'kodekegiatan' => $kodeKeg, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $param['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            ++$i;
        }
    }
    $errorDB = '';
    foreach ($dataIntraco as $dataRes) {
        $queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
        if (!mysql_query($queryH)) {
            $errorDB .= 'Header :'.mysql_error()."\n".$queryH;
        }

        if ('' == $errorDB) {
            foreach ($dataRes['detail'] as $key => $dataDet) {
                $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
                if (!mysql_query($queryD)) {
                    $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
                }
            }
            $queryToJ = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 1], "notransaksi='".$param['notransaksi']."'");
            if (!mysql_query($queryToJ)) {
                $errorDB .= 'Posting Mark Error :'.mysql_error()."\n";
            }

            $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + $i], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if (!mysql_query($queryKonter)) {
                $errorDB .= 'Update Counter Error :'.mysql_error()."\n";
            }
        }
    }
    echo $errorDB;
}
*/

?>