<?php

	require_once('config/connection.php');

    $filter = $_POST['filter'];
    $table  = $_POST['table'];
    $result = array();

    // Create connection
    $conn = new mysqli($dbserver, $uname, $passwd, $dbname,$dbport);

    // Check connection
    if ($conn->connect_error) {
        die("Koneksi Gagal..: " . $conn->connect_error);
    }

    switch ($table) {
        case 'UNIT':
            if (!isset($_POST['q'])) {
                $query = "SELECT kodeorganisasi,namaorganisasi FROM organisasi  WHERE induk='".$filter."' ORDER BY kodeorganisasi LIMIT 30";
            }else {
                $term = '%'.$_POST['q'].'%';
                $query = "SELECT kodeorganisasi,namaorganisasi FROM organisasi WHERE induk='".$filter."' AND namaorganisasi LIKE '".$term."' ORDER BY kodeorganisasi LIMIT 30";
            }
            $rs = $conn->query($query);
            if ($rs->num_rows > 0) {
                $atemp = array();
                while($key = $rs->fetch_assoc()) {
                    $bname = $key['kodeorganisasi'].' | '.$key['namaorganisasi'];
                    $atemp[] = ['id'=>$key['kodeorganisasi'], 'text'=>$bname];
                }
                $result['items'] = $atemp;
            }
            $conn->close();
            break;

        case 'KARYAWAN' :
            if (!isset($_POST['q'])) {
                $query = "SELECT karyawanid, nik, namakaryawan FROM datakaryawan  WHERE lokasitugas='".$filter."'  ORDER BY namakaryawan LIMIT 30";
            }else {
                $term = '%'.$_POST['q'].'%';
                $query = "SELECT karyawanid, nik, namakaryawan FROM datakaryawan WHERE lokasitugas='".$filter."' AND namakaryawan LIKE '".$term."'  ORDER BY namakaryawan LIMIT 30";
            }
            $rs = $conn->query($query);
            if ($rs->num_rows > 0) {
                $atemp = array();
                while($key = $rs->fetch_assoc()) {
                    $bname = $key['nik'].' | '.$key['namakaryawan'];
                    $atemp[] = ['id'=>$key['karyawanid'], 'text'=>$bname];
                }
                $result['items'] = $atemp;
            }
            $conn->close();
        break;    

        case 'ASSET' :
            if (!isset($_POST['q'])) {
                // $query = "SELECT kodeasset, namasset, keterangan FROM v_sdm_daftarasset_siap WHERE kodeorg='".$filter."' ORDER BY kodeasset LIMIT 30";
                $query = "SELECT kodeasset, namasset, keterangan FROM sdm_daftarasset WHERE kodeorg='".$filter."' ORDER BY kodeasset LIMIT 30";
            }else {
                $term = '%'.$_POST['q'].'%';
                // $query = "SELECT kodeasset, namasset, keterangan FROM v_sdm_daftarasset_siap WHERE kodeorg='".$filter."' AND keterangan LIKE '".$term."' ORDER BY kodeasset LIMIT 30";
                $query = "SELECT kodeasset, namasset, keterangan FROM sdm_daftarasset WHERE kodeorg='".$filter."' AND keterangan LIKE '".$term."' ORDER BY kodeasset LIMIT 30";
            }
            $rs = $conn->query($query);
            if ($rs->num_rows > 0) {
                $atemp = array();
                while($key = $rs->fetch_assoc()) {
                    $bname = $key['kodeasset'].' | '.$key['namasset'].' | '.$key['keterangan'];
                    $atemp[] = ['id'=>$key['kodeasset'], 'text'=>$bname];
                }
                $result['items'] = $atemp;
            }
            $conn->close();
        break;    

    }

    // $pdo = new PDO('mysql:host='.$dbserver.";port=".$dbport.';dbname='.$dbname, $uname, $passwd);

    // if ($pdo) {
    //     if (!isset($_POST['q'])) {
    //         $query = "SELECT  FROM organisasi  WHERE induk=:pfilter LIMIT 30";
	// 	    $param = [':pfilter' => $filter];
    //     }else {
    //         $term = '%'.$_POST['q'].'%';
    //         $query = "SELECT  FROM organisasi WHERE induk=:pfilter AND namaorganisasi LIKE :like LIMIT 30";
	// 	    $param = [':pfilter' => $filter, ':like' => $term];
    //     }

        
    //     $stmt = $pdo->fetch_all($query,$param);
    //     $atemp = array();
    //     foreach ($stmt as $key) {
    //         $bname = $key['bank_nm'].' | '.$key['acc_cd'].' | '.$key['acc_nm'].' | '.$key['curr_cd'];
    //         $atemp[] = ['id'=>$key['seq'], 'text'=>$bname];
    //     }
    //     $result['items'] = $atemp;
    // }

    echo json_encode($result);
    exit();
?>
