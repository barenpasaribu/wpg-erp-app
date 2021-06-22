<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';

    $proses = $_POST['proses'];

    switch ($proses) {
        // untuk pengopreasian pabrik
        case 'simpanTanggalPenyelesaian':
            
            $notransaksi = $_POST['notransaksi'];
            $tanggal = tanggalsystem($_POST['tanggal_penyelesaian']);
            $tanggal_penyelesaian = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $queryUpdateData = "UPDATE sdm_pjdinasht
                                SET tanggal_penyelesaian = '".$tanggal_penyelesaian."'
                                WHERE
                                notransaksi = '".$notransaksi."'
                                ";
            mysql_query($queryUpdateData);
            
            $queryGetData = "   SELECT tanggal_penyelesaian FROM sdm_pjdinasht
                                WHERE
                                notransaksi = '".$notransaksi."'
                                ";
                                
            $data = fetchData($queryGetData);
            $tanggal_json = null;
            if ($data[0]['tanggal_penyelesaian'] != null) {
                $tanggal_json = tanggalnormal($data[0]['tanggal_penyelesaian']);
            }
            echo json_encode($tanggal_json);
            break;
        case 'getAtasan':
            $newlokasitugas = $_POST['newlokasitugas'];
            $strAtasan = '  SELECT 
                            a.nik,a.karyawanid,a.namakaryawan,a.bagian 
                            FROM '.$dbname.".datakaryawan a
                            LEFT JOIN setup_approval b
                            ON a.karyawanid = b.karyawanid
                            where 
                            kodeunit = '".$newlokasitugas."'
                            and
                            applikasi like 'ATASAN%'
                            order by namakaryawan"; 
            $optAtasan = "<option value=''>- Pilih Atasan -</option>";
            $resAtasan = mysql_query($strAtasan);
            while ($bar = mysql_fetch_object($resAtasan)) {
                $optAtasan .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' - '.$bar->nik.' - ['.$bar->bagian.']</option>';
            }
            echo $optAtasan;
            break;
        case 'cekSP':
            $karyawanid = $_POST['karyawanid'];
            $tanggalberlaku = $_POST['tanggalberlaku'];
            $queryCek =     "   SELECT * FROM sdm_suratperingatan 
                                WHERE 
                                    karyawanid = '".$karyawanid."'
                                AND
                                    (
                                        tanggalberlaku <= '".tanggalsystem($tanggalberlaku)."'
                                        AND
                                        sampai >= '".tanggalsystem($tanggalberlaku)."'
                                    )
                            "; 
            $data = fetchData($queryCek);
            echo json_encode($data[0]);
            break;
        case 'batalPerjalananDinas':
            /*
                ALUR PEMBATALAN PERJALANAN DINAS by Awan
                urutan proses nya
                - GET ALL data transaksi sdm_pjdinasht
                - saya update sdm_pjdinasht isBatal nya jadi 1
                - sdm_pjdinasdt nya dihapus
                - sdm_absensidt nya dihapus
            */
            $queryGetPJDinasHT = "SELECT * FROM sdm_pjdinasht where notransaksi = '".$_POST['notransaksi']."'";
            $dataPJDinasHT = fetchData($queryGetPJDinasHT);
            if (!empty($dataPJDinasHT[0])) {
                if($dataPJDinasHT[0]['statuspersetujuan'] == 1 && $dataPJDinasHT[0]['statushrd'] == 1 && $dataPJDinasHT[0]['statuspersetujuan2'] == 1 && $dataPJDinasHT[0]['statuspertanggungjawaban'] == 0){
                    $queryUpdatePJDHT = "   UPDATE 
                                                sdm_pjdinasht
                                            SET 
                                                isBatal = 1,
                                                keterangan_batal = 'Perjalanan dinas dibatalkan.'
                                            WHERE
                                                notransaksi = '".$_POST['notransaksi']."'
                                            ";
                    mysql_query($queryUpdatePJDHT);
                    $queryDeletePJDDT = "   DELETE FROM 
                                                sdm_pjdinasdt
                                            WHERE 
                                                notransaksi = '".$_POST['notransaksi']."'
                                            ";
                    mysql_query($queryDeletePJDDT);
                    hapusAbsen($dataPJDinasHT[0]['notransaksi']);
                    echo "Pembatalan Perjalanan Dinas Berhasil!";
                }else{
                    echo "Pembatalan Perjalanan Dinas Gagal, PJD yang sudah di ACC Semua pihak dan belum melakukan pertanggung jawaban yang bisa di Batalkan";
                }
            }else{
                echo "Transaksi tidak ditemukan, coba lagi";
            }
            break;
    }

    function hapusAbsen($notransaksi)
    {
        // cek status sudah approvel semua
        $getDataPJDHT = ' select  * from '.$dbname.".sdm_pjdinasht 
                            where 
                            notransaksi='".$notransaksi."'
                            ";
        $queryAct = mysql_query($getDataPJDHT);
        $data = mysql_fetch_assoc($queryAct);
        
        if (empty($data)) {
            exit("Data tidak ditemukan");
        }
        $shift = "-";
        $jam_msk = "00:00:00";
        $jam_plg = "00:00:00";
        $insentif = 0;
        $penjelasan = "Perjalanan Dinas";
        $dari = $data['tanggalperjalanan'];
        $sampai = $data['tanggalkembali'];
        $kodeorg = $data['kodeorg'];
        $karyawanid = $data['karyawanid'];
        $tipe = $data['tipe_perjalanan_dinas'];
        if($data['isBatal'] == 1){
            // jika tanggal pergi dan kembali sama
            if ($dari == $sampai) {
                $queryDeleteAbsensiDT = "DELETE FROM sdm_absensidt 
                                        WHERE 
                                            kodeorg = '".$kodeorg."'
                                        AND
                                            karyawanid = '".$karyawanid."'
                                        AND
                                            penjelasan = '".$penjelasan."'
                                        AND
                                            absensi = '".$tipe."'
                                        AND
                                            tanggal = '".$dari."'
                                        ";
                mysql_query($queryDeleteAbsensiDT);
            // jika tanggal pergi dan kembali tidak sama
            } else {
                $tanggalAbsen = $dari;
                while($tanggalAbsen <= $sampai){

                    $queryDeleteAbsensiDT = "DELETE FROM sdm_absensidt 
                                        WHERE 
                                            kodeorg = '".$kodeorg."'
                                        AND
                                            karyawanid = '".$karyawanid."'
                                        AND
                                            penjelasan = '".$penjelasan."'
                                        AND
                                            absensi = '".$tipe."'
                                        AND
                                            tanggal = '".$tanggalAbsen."'
                                        ";
                                        
                    mysql_query($queryDeleteAbsensiDT);
                    $tanggalAbsen = date('Y-m-d', strtotime($tanggalAbsen . ' +1 day'));
                }
                
                
            }
        }
    }

?>