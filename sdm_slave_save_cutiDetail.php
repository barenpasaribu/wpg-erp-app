<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    $kodeorg = $_POST['kodeorgJ'];
    $karyawanid = $_POST['karyawanidJ'];
    $periode = $_POST['periodeJ'];
    $dari = tanggalsystem($_POST['dariJ']);
    $sampai = tanggalsystem($_POST['sampaiJ']);
    $diambil = $_POST['diambilJ'];
    $keterangan = $_POST['keteranganJ'];
    $method = $_POST['method'];
    if ('insert' == $method) {
        $strc = '   select * from '.$dbname.".sdm_cutidt
                    where karyawanid = '".$karyawanid."' and ((daritanggal>=".$dari.' and daritanggal<='.$sampai.")
                    or (sampaitanggal>=".$dari.' and sampaitanggal<='.$sampai.")
                    or (daritanggal<=".$dari.' and sampaitanggal>='.$sampai.'))';
        if (0 < mysql_num_rows(mysql_query($strc))) {
            echo ' Error '.$_SESSION['lang']['irisan'];
            exit(0);
        }

        if ($sampai < $dari) {
            echo ' Error < >';
            exit(0);
        }
    }

    if ('' == $diambil) {
        $diambil = 0;
    }

    switch ($method) {
        case 'delete':
            $str = 'delete from '.$dbname.".sdm_cutidt
                    where kodeorg='".$kodeorg."'
                    and karyawanid=".$karyawanid."
                    and periodecuti='".$periode."'
                    and daritanggal='".$_POST['dariJ']."'";

            break;
        case 'insert':
            $str = 'insert into '.$dbname.".sdm_cutidt 
                    (kodeorg,karyawanid,periodecuti,daritanggal,
                    sampaitanggal,jumlahcuti,keterangan, tipeijin)
                    values('".$kodeorg."',".$karyawanid.",
                    '".$periode."','".$dari."','".$sampai."',
                    ".$diambil.",'".$keterangan."', 'CT'
                    )";

            break;
        default:
            break;
    }
    if (mysql_query($str)) {
        $strx = '   select sum(jumlahcuti) as diambil from '.$dbname.".sdm_cutidt
                    where kodeorg='".$kodeorg."'
                    and karyawanid=".$karyawanid."
                    and periodecuti='".$periode."'";
        $diambil = 0;
        $resx = mysql_query($strx);
        while ($barx = mysql_fetch_object($resx)) {
            $diambil = $barx->diambil;
        }
        if ('' == $diambil) {
            $diambil = 0;
        }

        $strup = '  update '.$dbname.'.sdm_cutiht set diambil='.$diambil.',sisa=(hakcuti-'.$diambil.")
                    where kodeorg='".$kodeorg."'
                    and karyawanid=".$karyawanid."\r\n\t\t\t   and periodecuti='".$periode."'";
        mysql_query($strup);

        $queryKodeAbsensi ="select kodeabsen from ".$dbname.".sdm_5absensi 
                            where pengurang = 1";
        $queryActKA     = mysql_query($queryKodeAbsensi);
        $where = "";
        while($hasilKA = mysql_fetch_assoc($queryActKA)){
            if(empty($where)){
                $where .= "tipeijin = '".$hasilKA['kodeabsen']."'";
            }else{
                $where .= " OR tipeijin = '".$hasilKA['kodeabsen']."'";
            }
        }
        if(empty($where)){
            $sisa = 0;
        }else{
            $strx= "SELECT sum(jumlahcuti) as diambil 
                    FROM ".$dbname.".sdm_cutidt
                    WHERE 
                    karyawanid=".$karyawanid."
                    AND 
                    periodecuti='".$periode."'
                    AND
                    (".$where.")
                    ";
            
            $diambil = 0;

            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_object($resx)) {
                $diambil = $barx->diambil;
            }
            if($diambil > 0){
                $sisa = $diambil;
            }else{
                $sisa = 0;
            }
        }

        $str = 'update '.$dbname.".sdm_cutiht 
                set           
                diambil=".$sisa.",
                sisa=hakcuti-".$sisa."
                where 
                kodeorg='".$kodeorg."'
                and karyawanid=".$karyawanid."
                and periodecuti='".$periode."'";
        mysql_query($str);

    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }

?>