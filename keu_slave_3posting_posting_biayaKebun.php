<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$pt = getPT($dbname, substr($param['param_kodeorg'], 0, 4));
if (false === $pt) {
    $pt = getHolding($dbname, substr($param['param_kodeorg'], 0, 4));
}

$qPeriod = selectQuery($dbname, 'setup_periodeakuntansi', 'tanggalsampai', "kodeorg='".substr($param['param_kodeorg'], 0, 4)."' and periode='".$param['param_periode']."'");
$resPeriod = fetchData($qPeriod);
$tanggalSampai = $resPeriod[0]['tanggalsampai'];
$kodejurnal = 'KBN1';
$qParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', "kodeaplikasi='KBN' and jurnalid='".$kodejurnal."'");
$tmpParam = fetchData($qParam);
$akunKredit = $tmpParam[0]['noakunkredit'];
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,noakun', "kodekegiatan='".$param['kodekegiatan']."'");
$queryC = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$pt['kode']."' and kodekelompok='".$kodejurnal."'");
$tmpC = fetchData($queryC);
if (empty($tmpC)) {
    echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
    exit();
}

$konter = addZero($tmpC[0]['nokounter'] + 1, 3);
$tanggalJ = tanggalsystem(tanggalnormal($tanggalSampai));
$nojurnal = $tanggalJ.'/'.substr($param['param_kodeorg'], 0, 4).'/'.$kodejurnal.'/'.$konter;
$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $tanggalSampai, 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $param['nik'].'-'.$param['kodekegiatan'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
$noUrut = 1;
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalSampai, 'nourut' => $noUrut, 'noakun' => $optKeg[$param['kodekegiatan']], 'keterangan' => 'Biaya Karyawan Kebun '.$param['nik'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($param['param_kodeorg'], 0, 4), 'kodekegiatan' => $param['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['nik'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => ''];
++$noUrut;
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggalSampai, 'nourut' => $noUrut, 'noakun' => $akunKredit, 'keterangan' => 'Biaya Karyawan Kebun '.$param['nik'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($param['param_kodeorg'], 0, 4), 'kodekegiatan' => $param['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => '', 'nik' => $param['nik'], 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => ''];
++$noUrut;
$headErr = '';
$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
if (!mysql_query($insHead)) {
    $headErr .= 'Insert Header Error : '.mysql_error()."\n";
}

if ('' === $headErr) {
    $detailErr = '';
    foreach ($dataRes['detail'] as $row) {
        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
        if (!mysql_query($insDet)) {
            $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

            break;
        }
    }
    if ('' === $detailErr) {
        $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal."'");
        if (!mysql_query($updJurnal)) {
            echo 'Update Kode Jurnal Error : '.mysql_error()."\n";
            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
            if (!mysql_query($RBDet)) {
                echo 'Rollback Delete Header Error : '.mysql_error()."\n";
                exit();
            }

            exit();
        }

        echo '1';
    } else {
        echo $detailErr;
        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
        if (!mysql_query($RBDet)) {
            echo 'Rollback Delete Header Error : '.mysql_error();
            exit();
        }
    }
} else {
    echo $headErr;
    exit();
}

?>