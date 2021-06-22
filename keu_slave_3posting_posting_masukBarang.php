<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$queryH = selectQuery($dbname, 'log_transaksiht', '*', "notransaksi='".$param['notransaksi']."'");
$dataH = fetchData($queryH);
$query = selectQuery($dbname, 'log_transaksi_vw', '*', "notransaksi='".$param['notransaksi']."' and statussaldo=1");
$data = fetchData($query);
$whereBarang = 'kode in (';
$whereSupp = 'kode in (';
foreach ($data as $key => $row) {
    if (0 === $key) {
        $whereBarang .= "'".substr($row['kodebarang'], 0, 3)."'";
        $whereSupp .= "'".substr($row['idsupplier'], 0, 4)."'";
    } else {
        $whereBarang .= ",'".substr($row['kodebarang'], 0, 3)."'";
        $whereSupp .= ",'".substr($row['idsupplier'], 0, 4)."'";
    }
}
$whereBarang .= ')';
$whereSupp .= ')';
$optBarang = makeOption($dbname, 'log_5klbarang', 'kode,noakun', $whereBarang);
$optSupp = makeOption($dbname, 'log_5klsupplier', 'kode,noakun', $whereSupp);
$kodejurnal = 'INVM1';
$queryC = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal."'");
$tmpC = fetchData($queryC);
if (empty($tmpC)) {
    echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
    exit();
}

$konter = addZero($tmpC[0]['nokounter'] + 1, 3);
$tanggalJ = tanggalsystem(tanggalnormal($data[0]['tanggal']));
$nojurnal = $tanggalJ.'/'.substr($data[0]['notransaksi'], 14, 4).'/'.$kodejurnal.'/'.$konter;
$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $data[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $param['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
$noUrut = 1;
foreach ($data as $row) {
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $data[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $optBarang[substr($row['kodebarang'], 0, 3)], 'keterangan' => $dataH[0]['keterangan'], 'jumlah' => $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $data[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $optSupp[substr($row['idsupplier'], 0, 4)], 'keterangan' => $dataH[0]['keterangan'], 'jumlah' => -1 * $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $row['idsupplier'], 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
}
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

        $updTrans = updateQuery($dbname, 'log_transaksiht', ['statusjurnal' => 1], "notransaksi='".$param['notransaksi']."'");
        if (!mysql_query($updTrans)) {
            echo 'Update Status Jurnal Error : '.mysql_error()."\n";
            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
            if (!mysql_query($RBDet)) {
                echo 'Rollback Delete Header Error : '.mysql_error()."\n";
                exit();
            }

            $RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal."'");
            if (!mysql_query($RBJurnal)) {
                echo 'Rollback Update Jurnal Error : '.mysql_error()."\n";
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