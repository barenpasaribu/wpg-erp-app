<?php

    require_once 'master_validation.php';
    require_once 'lib/eagrolib.php';
    require_once 'config/connection.php';

    $lokres = $_POST['lokres'];
    $proses = $_POST['proses'];
    
    // print_r($_POST);
    // die();
    switch ($proses) {
        case 'getPersentase':
            $sqper = 'select * from '.$dbname.".sdm_ho_hr_jms_porsi where lokasiresiko = '".$lokres."' ORDER BY id ASC";

            $qper = mysql_query($sqper);
            $angkaper = array();
            while ($rper = mysql_fetch_assoc($qper)) {
                $angkaper[] = $rper;
            } 

            echo json_encode($angkaper);
            break;
        case 'updbpjs':
            $perusahaan = $_POST['perusahaan'];
            $karyawan = $_POST['karyawan'];
            // $pphjms = $_POST['pphjms'];
            $usiapensiun = $_POST['usiapensiun'];
            $jhtkar = $_POST['jhtkar'];
            $jpkar = $_POST['jpkar'];
            $jhtpt = $_POST['jhtpt'];
            $jppt = $_POST['jppt'];
            $jkkpt = $_POST['jkkpt'];
            $jkmpt = $_POST['jkmpt'];
            $bpjspt = $_POST['bpjspt'];
            $bpjskar = $_POST['bpjskar'];
            $jmpk = $_POST['jmpk'];
            $jmppt = $_POST['jmppt'];
            $bpjsmk = $_POST['bpjsmk'];
            $bpjsmpt = $_POST['bpjsmpt'];

            $queryPerusahaan = "
            update ".$dbname.".sdm_ho_hr_jms_porsi 
            set 
            value=".$perusahaan.",
            jhtpt=".$jhtpt.",
            jppt=".$jppt.",
            jkkpt=".$jkkpt.",
            jkmpt=".$jkmpt.",
            bpjspt=".$bpjspt.",
            jmppt=".$jmppt.",
            bpjsmpt=".$bpjsmpt."
            where id='perusahaan' and lokasiresiko='".$lokres."'";
            
            $queryKaryawan = "
            update ".$dbname.".sdm_ho_hr_jms_porsi 
            set 
            value=".$karyawan.",
            jhtkar=".$jhtkar.",
            jpkar=".$jpkar.",
            bpjskar=".$bpjskar.",
            jmpk=".$jmpk.",
            bpjsmk=".$bpjsmk."
            where id='karyawan' and lokasiresiko='".$lokres."'";
            
            $queryPensiun = "
            update ".$dbname.".sdm_ho_hr_jms_porsi 
            set 
            value=".$usiapensiun."
            where id='usiapensiun' and lokasiresiko='".$lokres."'";

            mysql_query($queryPerusahaan);
            mysql_query($queryKaryawan);
            mysql_query($queryPensiun);

            $i = 1;
            $stre = 'select * from ' . $dbname . '.sdm_ho_hr_jms_porsi ORDER BY lokasiresiko, id ASC';
            $rese = mysql_query($stre);
            
            $listtable = "
            <tr>
                <td>No.</td>
                <td>Lokasi Kerja</td>
                <td>ID</td>
                <td>Nilai</td>
                <td>JHT Karyawan</td>
                <td>JP Karyawan</td>
                <td>BPJS Karyawan</td>
                <td>BPJSM Karyawan</td>
                <td>JMP Karyawan</td>
                <td>JHT Perusahaan</td>
                <td>JP Perusahaan</td>
                <td>JKK Perusahaan</td>
                <td>JKM Perusahaan</td>
                <td>BPJS Perusahaan</td>
                <td>BPJSM Perusahaan</td>
                <td>JMP Perusahaan</td>
            </tr>
            ";
            while ($bar = mysql_fetch_object($rese)) {
                $listtable .= "
                    <tr>
                        <td>" . $i . "</td>
                        <td>" . $bar->lokasiresiko . "</td>
                        <td>" . $bar->id . "</td>
                        <td>" . $bar->value . "</td>
                        <td>" . $bar->jhtkar . "</td>
                        <td>" . $bar->jpkar . "</td>
                        <td>" . $bar->bpjskar . "</td>
                        <td>" . $bar->bpjsmk . "</td>
                        <td>" . $bar->jmpk . "</td>
                        <td>" . $bar->jhtpt . "</td>
                        <td>" . $bar->jppt . "</td>
                        <td>" . $bar->jkkpt . "</td>
                        <td>" . $bar->jkmpt . "</td>
                        <td>" . $bar->bpjspt . "</td>
                        <td>" . $bar->bpjsmpt . "</td>
                        <td>" . $bar->jmppt . "</td>
                    </tr>
                ";
                $i++;
            }

            echo $listtable;
            die();

            break;
        default:
            break;
    }
?>