<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    $notransaksi = $_POST['notransaksi'];
    $karyawanid = $_POST['karyawanid'];
    $status = $_POST['status'];
    $kolom = $_POST['kolom'];
    $tanggal = date('Ymd');
    $kolomstatus = 'status'.$kolom;
    $kolomtanggal = 'tanggal'.$kolom;
    $i = 'select  * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
    $n = mysql_query($i);
    $d = mysql_fetch_assoc($n);
    if (0 != $d['persetujuan'] && 0 == $d['statuspersetujuan'] && 'statuspersetujuan2' == $kolomstatus) {
        $status1 = $d['statuspersetujuan2'];
        if ('1' != $status1) {
            exit("Error:Sorry you can't approve this document,  because the first approver has not given approval or the first approver has been rejected");
        }
    }

    if ('hrd' == $kolomstatus) {
        $i = 'select  * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $status1 = $d['statuspersetujuan2'];
        if ('0' == $status1) {
            exit("Error:Sorry you can't approve this document,  because the second approver has not given approval yet.");
        }
        $hasilCekAbsen = cekAbsen($d['kodeorg'], $d['karyawanid'], $d['tanggalperjalanan'], $d['tanggalkembali']);
        if ($hasilCekAbsen == "Cuti") {
            exit('Error: Pada tanggal yang dipilih sudah melakukan cuti, lakukan batal cuti');
        }elseif ($hasilCekAbsen == "Perjalanan Dinas") {
            exit('Error: Pada tanggal yang dipilih sudah melakukan perjalanan dinas');
        }
    }

    $str = 'update '.$dbname.'.sdm_pjdinasht set '.$kolomstatus.'='.$status.", \r\n      ".$kolomtanggal.'='.$tanggal." where notransaksi='".$notransaksi."'";
    if (mysql_query($str)) {
        if ('statuspersetujuan' == $kolomstatus) {
            $iEmail = 'select karyawanid,persetujuan2,persetujuan from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
            $nEmail = mysql_query($iEmail);
            $dEmail = mysql_fetch_assoc($nEmail);
            if ($dEmail['persetujuan'] == $dEmail['persetujuan2']) {
                $str = 'update '.$dbname.'.sdm_pjdinasht set statuspersetujuan2='.$status.", \r\n                        ".$kolomtanggal.'='.$tanggal." where notransaksi='".$notransaksi."'";
                mysql_query($str);
            } else {
                $to = getUserEmail($d['persetujuan2']);
                $namakaryawanPengaju = getNamaKaryawan($dEmail['karyawanid']);
                $namakaryawan = getNamaKaryawan($dEmail['persetujuan']);
                $nmpnlk = getNamaKaryawan($dEmail['persetujuan2']);
                $subject = '[Notifikasi]'.$_SESSION['lang']['persetujuan'].' Perjalanan Dinas';
                $body = "<html>\r\n                                     <head>\r\n                                     <body>\r\n                                       <dd>Dengan Hormat, Bapak./Ibu. ".$nmpnlk."</dd><br>\r\n                                       Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan.' mengajukan Persertujuan Perjalanan Dinas atas nama '.$namakaryawanPengaju."\r\n                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                                       <br>\r\n                                       Regards,<br>\r\n                                       eAgro Plantation Management Software.\r\n                                     </body>\r\n                                     </head>\r\n                               </html>";
                $kirim = kirimEmailWindows($to, $subject, $body);
            }
        } else {
            $str = 'select nilai from '.$dbname.".setup_parameterappl where kodeaplikasi='X2' limit 1";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $to = $bar->nilai;
            }
            if ('1' == $status && '' != $to) {
                $str = 'select a.tanggalperjalanan,a.kodeorg,a.tujuan1,a.tugas1,b.namakaryawan,b.bagian from '.$dbname.".sdm_pjdinasht a\r\n\t\t\t\t  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid \r\n\t\t\t\t  where a.notransaksi='".$notransaksi."'";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $nama = $bar->namakaryawan;
                    $tanggal = tanggalnormal($bar->tanggalperjalanan);
                    $tujuan = $bar->tujuan1;
                    $bagian = $bar->bagian;
                    $tugas = $bar->tugas1;
                }
                $subject = '[Notifikasi] Perjalanan Dinas';
                $body = "<html>\r\n\t\t\t\t\t <head>\r\n\t\t\t\t\t <body>\r\n\t\t\t\t\t   <dd>Dengan Hormat,</dd><br>\r\n\t\t\t\t\t   <br>\r\n\t\t\t\t\t   Telah disetujui perjalanan dinas  A/n: ".$nama.' ('.$bagian.")<br>\r\n\t\t\t\t\t   Tujuan:".$tujuan."<br>\r\n\t\t\t\t\t   Tugas :".$tugas."<br>\r\n\t\t\t\t\t   Tanggal:".$tanggal."\r\n\t\t\t\t\t   <br>\r\n\t\t\t\t\t   <br>\r\n\t\t\t\t\t   <br>\r\n\t\t\t\t\t   Regards,<br>\r\n\t\t\t\t\t   eAgro Plantation Management Software.\r\n\t\t\t\t\t </body>\r\n\t\t\t\t\t </head>\r\n\t\t\t\t   </html>\r\n\t\t\t\t   ";
                $kirim = kirimEmailWindows($to, $subject, $body);
            }
        }
    } else {
        echo addslashes(mysql_error($conn));
    }

    // cek status sudah approvel semua
    $queryCekAllApprove = 'select  * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
    $queryAct = mysql_query($queryCekAllApprove);
    $data = mysql_fetch_assoc($queryAct);

    $shift = "-";
    $jam_msk = "00:00:00";
    $jam_plg = "00:00:00";
    $insentif = 0;
    $penjelasan = "Perjalanan Dinas";
    
    if($data['statuspersetujuan'] == 1 && $data['statuspersetujuan2'] == 1 && $data['statushrd'] == 1){
        // jika tanggal pergi dan kembali sama
        if ($data['tanggalperjalanan'] == $data['tanggalkembali']) {

            $queryInsertAbsensiHT = "insert into ".$dbname.".sdm_absensiht 
                                    (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`) 
                                    values 
                                    ('".$data['kodeorg']."','".$data['tanggalperjalanan']."','".substr($data['tanggalperjalanan'],0,7)."',
                                    '0000000000','".date('Y-m-d H:i:s')."') 
                                    ON DUPLICATE KEY UPDATE 
                                    updatetime='".date('Y-m-d H:i:s')."';";
            mysql_query($queryInsertAbsensiHT);

            $queryInsertAbsensiDT = "insert into ".$dbname.".sdm_absensidt 
                                    (kodeorg, tanggal, karyawanid, shift, absensi ,jam , jamPlg , penjelasan, penaltykehadiran, premi, insentif) 
                                    values 
                                    ('".$data['kodeorg']."','".$data['tanggalperjalanan']."','".$data['karyawanid']."',
                                    '".$shift."','".$data['tipe_perjalanan_dinas']."',
                                    '".$jam_msk."','".$jam_plg."','".$penjelasan."',0,0,0) 
                                    ON DUPLICATE KEY UPDATE 
                                    absensi='".$data['tipe_perjalanan_dinas']."',
                                    penjelasan='".$penjelasan."';
                                    ";
            mysql_query($queryInsertAbsensiDT);
        // jika tanggal pergi dan kembali tidak sama
        } else {
            $tanggalAbsen = $data['tanggalperjalanan'];
            while($tanggalAbsen <= $data['tanggalkembali']){
                $queryInsertAbsensiHT = "insert into ".$dbname.".sdm_absensiht 
                                        (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`) 
                                        values 
                                        ('".$data['kodeorg']."','".$tanggalAbsen."','".substr($tanggalAbsen,0,7)."',
                                        '0000000000','".date('Y-m-d H:i:s')."') 
                                        ON DUPLICATE KEY UPDATE 
                                        updatetime='".date('Y-m-d H:i:s')."';";
                mysql_query($queryInsertAbsensiHT);

                $queryInsertAbsensiDT = "insert into ".$dbname.".sdm_absensidt 
                                        (kodeorg, tanggal, karyawanid, shift, absensi ,jam , jamPlg , penjelasan, penaltykehadiran, premi, insentif) 
                                        values 
                                        ('".$data['kodeorg']."','".$tanggalAbsen."','".$data['karyawanid']."',
                                        '".$shift."','".$data['tipe_perjalanan_dinas']."',
                                        '".$jam_msk."','".$jam_plg."','".$penjelasan."',0,0,0) 
                                        ON DUPLICATE KEY UPDATE 
                                        absensi='".$data['tipe_perjalanan_dinas']."',
                                        penjelasan='".$penjelasan."';
                                        ";
                mysql_query($queryInsertAbsensiDT);

                $tanggalAbsen = date('Y-m-d', strtotime($tanggalAbsen . ' +1 day'));
            }
        }
    }

    function cekAbsen($kodeorg, $karyawanid, $dari, $sampai)
    {
        if ($kodeorg == 1) {
            $stru = 'select lokasitugas from '.$dbname.'.datakaryawan where karyawanid='.$karyawanid;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $kodeorg = $baru->lokasitugas;
            }
        }
        $tanggalAbsen = $dari;
        $where = "";
        $i = 0;
        while($tanggalAbsen <= $sampai){
            if ($i == 0) {
                $where .= " tanggal='".$tanggalAbsen."' ";
            }else{
                $where .= " OR tanggal='".$tanggalAbsen."' ";
            }
            $i++;
            $tanggalAbsen = date('Y-m-d', strtotime($tanggalAbsen . ' +1 day'));
        }
        $queryCekData = "SELECT * FROM sdm_absensidt 
                        WHERE
                        kodeorg = '".$kodeorg."'
                        AND 
                        (penjelasan = 'Cuti' OR penjelasan = 'Perjalanan Dinas')
                        AND
                        karyawanid = '".$karyawanid."'
                        AND 
                        ( "
                        .$where. 
                        " )" ;
       
        $data = fetchData($queryCekData);
        $jumlah = mysql_num_rows(mysql_query($queryCekData));

        if ($jumlah > 0) {
            return $data[0]['penjelasan'];
        }else{
            return "kosong";
        } 
    }
?>