<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    
    $jenissp = $_POST['jenissp'];
    $karyawanid = $_POST['karyawanid'];
    $masaberlaku = $_POST['masaberlaku'];
    $tanggalsp = tanggalsystem($_POST['tanggalsp']);
    $tanggalberlaku = tanggalsystem($_POST['tanggalberlaku']);
    $paragraf1 = $_POST['paragraf1'];
    $paragraf3 = $_POST['paragraf3'];
    
    $paragraf4 = $_POST['paragraf4'];
    $pelanggaran = $_POST['pelanggaran'];
    $penandatangan = $_POST['penandatangan'];
    $jabatan = $_POST['jabatan'];
    $tembusan1 = $_POST['tembusan1'];
    $tembusan2 = $_POST['tembusan2'];
    $tembusan3 = $_POST['tembusan3'];
    $tembusan4 = $_POST['tembusan4'];
    $method = $_POST['method'];
    $kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $verifikasi = $_POST['verifikasi'];
    $dibuat = $_POST['dibuat'];
    $jabatan1 = $_POST['jabatan1'];
    $jabatan2 = $_POST['jabatan2'];
    $t = mktime(0, 0, 0, substr($tanggalberlaku, 4, 2) + $masaberlaku, substr($tanggalberlaku, 6, 2), substr($tanggalberlaku, 0, 4));
    $sampai = date('Ymd', $t);
    $paragraf3 = str_replace("[JENIS_SURAT]",$_POST['jenissp'],$paragraf3);
    $paragraf3 = str_replace("[MULAI_BERLAKU]",$_POST['tanggalberlaku'],$paragraf3);
    $paragraf3 = str_replace("[SAMPAI_BERLAKU]",tanggalnormal($sampai),$paragraf3);
    $paragraf3 = str_replace("[JUMLAH_MASA]",$masaberlaku,$paragraf3);
    
    
    if ('insert' == $method) {
        $kodeSP = str_replace(" ", "", $_POST['jenissp']);
        $bulan = substr($_POST['tanggalsp'], 3, 2);
        $tahun = substr($_POST['tanggalsp'], 6, 4);
        $bulanRomawi = getBulanRomawi($bulan);

        

        $str = 'SELECT jenissp FROM '.$dbname.".sdm_suratperingatan
                where 
                    karyawanid=".$karyawanid." 
                and 
                    jenissp='".$jenissp."' 
                AND 
                    ".$tanggalsp.' 
                BETWEEN tanggal and sampai';
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            echo 'Surat Peringatan untuk karyawan ini sudah dibuat dan belum berakhir.';
            exit();
        }
        
        $potSK = "/" . $bulanRomawi . "/" . $tahun . "/" . $kodeSP . "/" . $kodeorg;
        $numberRunning = 0;
        $str = 'select 
                    nomor 
                from 
                    '.$dbname.".sdm_suratperingatan
                where  
                    nomor like '%".$potSK."'";
                    // print_r($str);
                    // die();
        $notrx = 0;
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $noDB = (int) substr($bar->nomor, 0, 4);
            if ($noDB >= $numberRunning) {
                $numberRunning = $noDB;
            }
        }
        $numberRunning += 1;
        $numberRunning = str_pad($numberRunning, 3, '0', STR_PAD_LEFT);

        $notrx = $numberRunning . "/" . $bulanRomawi . "/" . $tahun . "/" . $kodeSP . "/" . $kodeorg;
        // print_r($notrx);
        // die();
        
        $str = 'insert into '.$dbname.".sdm_suratperingatan (
            `nomor`,`jenissp`,`karyawanid`,
            `pelanggaran`,`tanggal`,`masaberlaku`,
            `sampai`,`tembusan1`,`tembusan2`,
            `tembusan4`,`tembusan3`,
            `kodeorg`, `penandatangan`,`jabatan`,
            `updateby`,`paragraf1`,`paragraf3`,
            `paragraf4`,`verifikasi`,`dibuat`,`jabatanverifikasi`,`jabatandibuat`
            ) values(
                '".$notrx."','".$jenissp."',".$karyawanid.",
                '".$pelanggaran."',".$tanggalsp.','.$masaberlaku.",
                ".$sampai.",'".$tembusan1."','".$tembusan2."',
                '".$tembusan4."','".$tembusan3."','".$kodeorg."',
                '".$penandatangan."','".$jabatan."',".$_SESSION['standard']['userid'].",
                '".$paragraf1."','".$paragraf3."','".$paragraf4."','".$verifikasi."','".$dibuat."','".$jabatan1."','".$jabatan2."'
                )";
    } else {
        if ('delete' == $method) {
            $nosp = $_POST['nosp'];
            $str = 'delete from '.$dbname.".sdm_suratperingatan
            where nomor='".$nosp."'";
        } else {
            if ('update' == $method) {
                $nosp = $_POST['nosp'];
                $str = 'update '.$dbname.".sdm_suratperingatan set
                `jenissp`='".$jenissp."',
                `pelanggaran`='".$pelanggaran."',
                `tanggal`=".$tanggalsp.",
                `tanggalberlaku`=".$tanggalberlaku.",
                `masaberlaku`=".$masaberlaku.",
                `sampai`=".$sampai.",
                `tembusan1`='".$tembusan1."',
                `tembusan2`='".$tembusan2."',
                `tembusan4`='".$tembusan4."',
                `tembusan3`='".$tembusan3."',
                `kodeorg`='".$kodeorg."', 
                `penandatangan`='".$penandatangan."',
                `jabatan`='".$jabatan."',
                `updateby`=".$_SESSION['standard']['userid'].",
                `paragraf1`='".$paragraf1."',
                `paragraf3`='".$paragraf3."',
                `paragraf4`='".$paragraf4."',
                `verifikasi`='".$verifikasi."',
                `dibuat`='".$dibuat."',
                `jabatanverifikasi`='".$jabatan1."',
                `karyawanid`='".$karyawanid."',
                `jabatandibuat`='".$jabatan2."'
                where nomor='".$nosp."'";
            }
        }
    }

    if (mysql_query($str)) {
        echo 'SP '. $method . ' success.';
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn));
    }

    function validatePeriod()
    {
    }

    function getBulanRomawi ($bulan){
        $bulanRomawi = 'XX'; 
        switch ($bulan) {
            case 1:
                $bulanRomawi = 'I';
                break;
            case 2:
                $bulanRomawi = 'II';
                break;
            case 3:
                $bulanRomawi = 'III';
                break;
            case 4:
                $bulanRomawi = 'IV';
                break;
            case 5:
                $bulanRomawi = 'V';
                break;
            case 6:
                $bulanRomawi = 'VI';
                break;
            case 7:
                $bulanRomawi = 'VII';
                break;
            case 8:
                $bulanRomawi = 'VIII';
                break;
            case 9:
                $bulanRomawi = 'IX';
                break;
            case 10:
                $bulanRomawi = 'X';
                break;
            case 11:
                $bulanRomawi = 'XI';
                break;
            case 12:
                $bulanRomawi = 'XII';
                break;
            default:
                $bulanRomawi = 'XX';
                break;
        }

        return $bulanRomawi; 
    }

?>