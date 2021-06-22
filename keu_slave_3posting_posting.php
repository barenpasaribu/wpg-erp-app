<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$proses = $_GET['proses'];
switch ($proses) {
    case 'masukBarang':
        $query = selectQuery($dbname, 'log_transaksi_vw', '*', "notransaksi='".$param['notransaksi']."' and statussaldo=1");
        $data = fetchData($query);
        $whereBarang = 'kode in (';
        foreach ($data as $key => $row) {
        }
        $whereBarang .= ')';
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
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $data[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $data[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
        print_r($dataRes['header']);
        exit();
    case 'keluarBarang':
        $query = selectQuery($dbname, 'log_transaksi_vw', '*', "notransaksi='".$param['notransaksi']."' and statussaldo=1");
        $data = fetchData($query);
        $kodejurnal = 'INVK1';
        $queryC = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal."'");
        $tmpC = fetchData($queryC);
        $konter = addZero($tmpC[0]['nokounter'] + 1, 3);
        $tanggalJ = tanggalsystem(tanggalnormal($data[0]['tanggal']));
        $nojurnal = $tanggalJ.'/'.substr($data[0]['notransaksi'], 14, 4).'/'.$kodejurnal.'/'.$konter;
        echo $nojurnal;
        exit();
}

?>