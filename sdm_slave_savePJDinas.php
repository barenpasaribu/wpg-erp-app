<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    
    $karyawanid = $_POST['karyawanid'];
    $tipe_perjalanan_dinas = $_POST['tipe_perjalanan_dinas'];
    $kodeorg = $_POST['kodeorg'];
    $persetujuan = $_POST['persetujuan'];
    $persetujuan2 = $_POST['persetujuan2'];
    $hrd = $_POST['hrd'];
    $tujuan3 = $_POST['tujuan3'];
    $tujuan2 = $_POST['tujuan2'];
    $tujuan1 = $_POST['tujuan1'];
    $tanggalperjalanan = tanggalsystem($_POST['tanggalperjalanan']);
    $tanggalkembali = tanggalsystem($_POST['tanggalkembali']);
    $uangmuka = $_POST['uangmuka'];
    $tugas1 = $_POST['tugas1'];
    $tugas2 = $_POST['tugas2'];
    $tugas3 = $_POST['tugas3'];
    $tujuanlain = $_POST['tujuanlain'];
    $tugaslain = $_POST['tugaslain'];
    $pesawat = $_POST['pesawat'];
    $darat = $_POST['darat'];
    $laut = $_POST['laut'];
    $mess = $_POST['mess'];
    $hotel = $_POST['hotel'];
    $mobilsewa = $_POST['mobilsewa'];
    $mobildinas = $_POST['mobildinas'];
    $method = $_POST['method'];
    $ket = $_POST['ket'];
    if ('' == $persetujuan) {
        $persetujuan = '0000000000';
    }

    if ('' == $uangmuka) {
        $uangmuka = 0;
    }

    if ('insert' == $method) {
        // 001/03/2020/SPPD/SSPH
        $tahun = date('Y');
        $bulan = date('m');

        $potSK = substr($_SESSION['empl']['lokasitugas'], 0, 4);
        $str = 'select notransaksi from '.$dbname.".sdm_pjdinasht
                where  notransaksi like '%/".$tahun."/SPPD/".$potSK."'
                order by notransaksi desc limit 1";
        $notrx = 0;
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $notrx = substr($bar->notransaksi, 0, 3);
        }
        $notrx = (int) $notrx;
        $notrx = $notrx + 1;
        $notrx = str_pad($notrx, 3, '0', STR_PAD_LEFT);
        
        $notrx = $notrx."/".$bulan."/".$tahun."/SPPD/".$potSK;

        $dari = substr($tanggalperjalanan, 0, 4) . "-" . substr($tanggalperjalanan, 4, 2) . "-" . substr($tanggalperjalanan, 6, 2);
        $sampai = substr($tanggalperjalanan, 0, 4) . "-" . substr($tanggalkembali, 4, 2) . "-" . substr($tanggalkembali, 6, 2);
        $hasilCekAbsen = cekAbsen($kodeorg, $karyawanid, $dari, $sampai);
        if ($hasilCekAbsen == "Cuti") {
            exit('Error: Pada tanggal yang dipilih sudah melakukan cuti, lakukan batal cuti');
        }elseif ($hasilCekAbsen == "Perjalanan Dinas") {
            exit('Error: Pada tanggal yang dipilih sudah melakukan perjalanan dinas');
        }

        $str = 'insert into '.$dbname.".sdm_pjdinasht (
                `notransaksi`, `karyawanid`, `created_by`,`tipe_perjalanan_dinas`,`tanggalbuat`,
                `tanggalperjalanan`,`kodeorg`,`tujuan1`,
                `tugas1`,`tujuan2`,`tugas2`,`tujuan3`,
                `tugas3`,`tugaslain`,`tujuanlain`,
                `pesawat`,`darat`,`laut`,
                `mess`,`hotel`,`mobilsewa`,`mobildinas`,`tanggalkembali`,`uangmuka`,
                `hrd`,`persetujuan`,`persetujuan2`,`keterangan`
                ) values(
                    '".$notrx."',
                    '".$karyawanid."',
                    '".$_SESSION['empl']['karyawanid']."',
                    '".$tipe_perjalanan_dinas."',
                    '".date('Ymd')."',
                    '".$tanggalperjalanan."',
                    '".$kodeorg."',
                    '".$tujuan1."',
                    '".$tugas1."','".$tujuan2."','".$tugas2."','".$tujuan3."',
                    '".$tugas3."','".$tugaslain."','".$tujuanlain."',
                    ".$pesawat.','.$darat.','.$laut.",
                    ".$mess.','.$hotel.','.$mobilsewa.','.$mobildinas.','.$tanggalkembali.','.$uangmuka.",
                    ".$hrd.','.$persetujuan.','.$persetujuan2." ,'".$ket."'
                    )";
                    
    } else {
        if ('delete' == $method) {
            $notransaksi = $_POST['notransaksi'];
            $str = 'delete from '.$dbname.".sdm_pjdinasht\r\n\t      where karyawanid=".$karyawanid." and notransaksi='".$notransaksi."'";
        } else {
            if ('update' == $method) {
                $notransaksi = $_POST['notransaksi'];
                $str = 'update '.$dbname.".sdm_pjdinasht set
                `tanggalperjalanan`=".$tanggalperjalanan.",
                `kodeorg`='".$kodeorg."',
                `tujuan1`='".$tujuan1."',
                `tugas1`='".$tugas1."',
                `tujuan2`='".$tujuan2."',
                `tugas2`='".$tugas2."',
                `tujuan3`='".$tujuan3."',
                `tugas3`='".$tugas3."',
                `tugaslain`='".$tugaslain."',
                `tujuanlain`='".$tujuanlain."',
                `pesawat`=".$pesawat.",
                `darat`=".$darat.",
                `laut`=".$laut.",
                `mess`=".$mess.",
                `hotel`=".$hotel.",
                `mobildinas`=".$mobildinas.",
                `mobilsewa`=".$mobilsewa.",
                `tanggalkembali`=".$tanggalkembali.",
                `uangmuka`=".$uangmuka.",
                `hrd`=".$hrd.",
                `persetujuan`=".$persetujuan.', `persetujuan2`='.$persetujuan2.",
                `keterangan`='".$ket."'
                where karyawanid=".$karyawanid." and notransaksi='".$notransaksi."'";
            }
        }
    }

    if (mysql_query($str)) {
        if ('update' == $method || 'insert' == $method) {
            $to = getUserEmail($persetujuan.','.$persetujuan2.','.$hrd);
            $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
            $subject = '[Notifikasi]Persetujuan Perjalanan Dinas a/n '.$namakaryawan;
            $body = "<html>\r\n                 <head>\r\n                 <body>\r\n                   <dd>Dengan Hormat,</dd><br>\r\n                   <br>\r\n                   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan." mengajukan surat perjalanan dinas\r\n                   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan login ke dalam aplikasi <b>'e-Agro Plantation Management Software'</b> dengan menggunakan Username & Password yang sudah diberikan.\r\n                   <br>\r\n                   <br>\r\n                   <br>\r\n                   Regards,<br>\r\n                   eAgro Plantation Management Software.\r\n                 </body>\r\n                 </head>\r\n               </html>\r\n               ";
            $kirim = kirimEmailWindows($to, $subject, $body);
        }
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn));
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