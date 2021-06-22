<?php
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    
    $karyawanid = $_POST['karyawanid'];
    $lokasitugas = $_POST['lokasitugas'];
    $periode = $_POST['periode'];
    
    $dari = date("Y-m-d", strtotime($_POST['dari']));
    $sampai = date("Y-m-d", strtotime($_POST['sampai']));
    $hak = $_POST['hak'];
    $sisa = 0;

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
            set dari='".$dari."',
            sampai='".$sampai."',
            hakcuti=".$hak.",            
            diambil=".$sisa.",
            sisa=hakcuti-".$sisa."
            where 
            kodeorg='".$lokasitugas."'
            and karyawanid=".$karyawanid."
            and periodecuti='".$periode."'";
    mysql_query($str);
    
    if (mysql_affected_rows($conn) < 1) {
        
        $str = 'insert into '.$dbname.".sdm_cutiht(
                kodeorg,`karyawanid`,`periodecuti`,
                `dari`,`sampai`,`hakcuti`,`diambil`,`sisa`)
                values
                ('".$lokasitugas."',".$karyawanid.",
                '".$periode."','".$dari."','".$sampai."',
                ".$hak.",".$sisa.",hakcuti-".$sisa.")";
        if (mysql_query($str)) {
        } else {
            echo addslashes(mysql_error($conn));
        }

    }
?>