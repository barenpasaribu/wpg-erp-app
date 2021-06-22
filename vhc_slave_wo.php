<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$param = $_GET;
$notransaksi = (isset($_POST['notransaksi']) ? $_POST['notransaksi'] : '');
$kodetraksi = (isset($_POST['kodetraksi']) ? $_POST['kodetraksi'] : '');
$kodealat = (isset($_POST['kodealat']) ? $_POST['kodealat'] : '');
$tanggal = (isset($_POST['tanggal']) ? $_POST['tanggal'] : '');
$jam = (isset($_POST['notransaksi']) ? $_POST['jam'].':'.$_POST['mnt'] : ':');
$kodealat = (isset($_POST['kodealat']) ? $_POST['kodealat'] : '');
$operator = (isset($_POST['operator']) ? $_POST['operator'] : '');
$posisihm = (isset($_POST['posisihm']) ? $_POST['posisihm'] : '');
$namapelapor = (isset($_POST['namapelapor']) ? $_POST['namapelapor'] : '');
$indikasikerusakan = (isset($_POST['indikasikerusakan']) ? $_POST['indikasikerusakan'] : '');
$penyebabrusak = (isset($_POST['penyebabrusak']) ? $_POST['penyebabrusak'] : '');
$noberitaacara = (isset($_POST['noberitaacara']) ? $_POST['noberitaacara'] : '');
$hedept = (isset($_POST['hedept']) ? $_POST['hedept'] : '');
$divmanager = (isset($_POST['divmanager']) ? $_POST['divmanager'] : '');
$workshop = (isset($_POST['workshop']) ? $_POST['workshop'] : '');
$perSch = (isset($_POST['perSch']) ? $_POST['perSch'] : '');
$method = (isset($_POST['method']) ? $_POST['method'] : '');
if (isset($_GET['method'])) {
    $method = $_GET['method'];
    $notransaksi = $_GET['notransaksi'];
}

switch ($method) {
    case 'insert':
        $i = 'insert into '.$dbname.".vhc_wo \r\n                (kodetraksi,tanggal,jam,kodealat,operator,posisihm,namapelapor,indikasikerusakan,penyebabrusak,noberitaacara,hedept,divmanager,workshop,updateby)\r\n                values ('".$kodetraksi."','".tanggalsystem($tanggal)."','".$jam."','".$kodealat."','".$operator."','".$posisihm."','".$namapelapor."','".$indikasikerusakan."','".$penyebabrusak."','".$noberitaacara."','".$hedept."','".$divmanager."','".$workshop."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".vhc_wo set kodetraksi='".$kodetraksi."',tanggal='".tanggalsystem($tanggal)."',jam='".$jam."',kodealat='".$kodealat."',operator='".$operator."',posisihm='".$posisihm."',namapelapor='".$namapelapor."',indikasikerusakan='".$indikasikerusakan."',penyebabrusak='".$penyebabrusak."',noberitaacara='".$noberitaacara."',hedept='".$hedept."',divmanager='".$divmanager."',workshop='".$workshop."',updateby='".$_SESSION['standard']['userid']."' WHERE notransaksi='".$notransaksi."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        if ('' !== $perSch) {
            $perLoad = "WHERE tanggal LIKE '%".$perSch."%'";
        } else {
            $perLoad = '';
        }

        echo "\r\n            <div style='height:220px;overflow:auto'>\r\n                    <table class=sortable cellspacing=1 border=0>\r\n                 <thead>\r\n                             <tr class=rowheader>\r\n                                    <td align=center>".$_SESSION['lang']['no.wo']."</td>\r\n                                    <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                                    <td align=center>".$_SESSION['lang']['kodealat']."</td>\r\n                                    <td align=center>".$_SESSION['lang']['kodetraksi']."</td>\r\n                                    <td align=center>".$_SESSION['lang']['action']."</td>\r\n                             </tr>\r\n                    </thead>\r\n                    <tbody>";
        $ql2 = 'SELECT * FROM '.$dbname.'.vhc_wo '.$perLoad.' ';
        $n = mysql_query($ql2);
        $data = [];
        while ($d = mysql_fetch_assoc($n)) {
            $data[] = $d;
        }
        if (!empty($data)) {
            $whereTraksiRow = "tipe='TRAKSI' AND kodeorganisasi IN ('";
            $notFirst = false;
            foreach ($data as $key => $row) {
                if ('' !== $row['kodetraksi']) {
                    if (false === $notFirst) {
                        $whereTraksiRow .= $row['kodetraksi'];
                        $notFirst = true;
                    } else {
                        $whereTraksiRow .= "','".$row['kodetraksi'];
                    }
                }
            }
            $whereTraksiRow .= "')";
            $optTraksiRow = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereTraksiRow, '0', true);
        }

        $dataShow = $data;
        foreach ($dataShow as $key => $d) {
            $jamDt = explode(':', $d['jam']);
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$d['notransaksi'].'</td>';
            echo '<td align=left>'.$d['tanggal'].'</td>';
            echo '<td align=right>'.$d['kodealat'].'</td>';
            echo '<td align=right>'.$optTraksiRow[$d['kodetraksi']].'</td>';
            echo "<td align=center>\r\n                        <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' \r\n                            onclick=\"fillField('".$d['notransaksi']."','".$d['kodetraksi']."','".tanggalnormal($d['tanggal'])."','".$jamDt[0]."','".$jamDt[1]."','".$d['kodealat']."','".$d['operator']."','".$d['posisihm']."','".$d['namapelapor']."','".$d['indikasikerusakan']."','".$d['penyebabrusak']."','".$d['noberitaacara']."','".$d['hedept']."','".$d['divmanager']."','".$d['workshop']."');\">\r\n                        <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$d['notransaksi']."');\">\r\n                        <img src=images/pdf.jpg class=resicon title='Print PDF' caption='Print PDF' onclick=\"printPdf('".$d['notransaksi']."',event);\"></td>\r\n                                ";
            echo '</tr>';
        }
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".vhc_wo where notransaksi='".$notransaksi."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getAlat':
        $optAlat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sGet = selectQuery($dbname, 'vhc_5master', 'kodevhc,detailvhc', "kodetraksi = '".$kodetraksi."'");
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optAlat .= "<option value='".$rGet['kodevhc']."'>".$rGet['kodevhc'].$rGet['detailvhc'].'</option>';
        }
        echo $optAlat;

        break;
    case 'getOperator':
        $optOperator = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sGet = selectQuery($dbname, 'vhc_5operator', 'karyawanid,nama', "vhc = '".$kodealat."'");
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optOperator .= '<option value='.$rGet['karyawanid'].'>'.$rGet['nama'].'</option>';
        }
        echo $optOperator;

        break;
    case 'getBA':
        $optBA = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sGet = selectQuery($dbname, 'vhc_balaka', 'notransaksi', "kodealat = '".$kodealat."'");
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optOperator .= '<option value='.$rGet['notransaksi'].'>'.$rGet['notransaksi'].'</option>';
        }
        echo $optOperator;

        break;
    case 'printPdf':

class PDF extends FPDF 
{
    public function Header()
    {
        $this->SetY(10);
        $this->SetX(163);
        $this->SetFont('Arial', '', 6);
        $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetX(173);
        $this->SetFont('Arial', 'I', 6);
        $this->Cell(20, 10, 'Page '.$this->PageNo(), 0, 0, 'R');
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->SetFont('Arial', '', 6);
        $pdf->AddPage();
        $sGet = selectQuery($dbname, 'vhc_wo', '*', "notransaksi = '".$notransaksi."'");
        $qGet = mysql_query($sGet);
        $res = mysql_fetch_assoc($qGet);
        $whereKarRow = "karyawanid in ('".$res['operator']."','".$res['hedept']."','".$res['divmanager']."','".$res['workshop']."')";
        $optKarRow = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKarRow, '0', true);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetY(22);
        $pdf->Cell(180, 10, 'PT. SWADAYA SAPTA PUTRA ', 0, 1, 'C');
        $pdf->Line(10, 30, 200, 30);
        $pdf->SetY(30);
        $pdf->Cell(180, 10, 'WORK ORDER', 0, 1, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $pdf->Cell(0, 5, tanggalnormal($res['tanggal']), 0, 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['jam'], '', 0, 'L');
        $pdf->Cell(0, 5, $res['jam'], '', 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['kodealat'], '', 0, 'L');
        $pdf->Cell(0, 5, $res['kodealat'], '', 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['operator'], '', 0, 'L');
        $pdf->Cell(0, 5, (isset($optKarRow[$res['operator']]) ? $optKarRow[$res['operator']] : ''), '', 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['posisihm'], '', 0, 'L');
        $pdf->Cell(0, 5, $res['posisihm'], '', 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['indikasikerusakan'], '', 0, 'L');
        $pdf->MultiCell(0, 5, $res['indikasikerusakan'], 0, 'J');
        $pdf->Cell(50, 5, $_SESSION['lang']['penyebabrusak'], '', 0, 'L');
        $pdf->Cell(0, 5, $res['penyebabrusak'], '', 1, 'L');
        $pdf->Cell(50, 5, $_SESSION['lang']['noberitaacara'], '', 0, 'L');
        $pdf->Cell(0, 5, $res['noberitaacara'], '', 1, 'L');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(45, 10, $_SESSION['lang']['operator'], '', 0, 'C');
        $pdf->Cell(45, 10, $_SESSION['lang']['hedept'], '', 0, 'C');
        $pdf->Cell(45, 10, $_SESSION['lang']['divmanager'], '', 0, 'C');
        $pdf->Cell(45, 10, $_SESSION['lang']['workshop'], '', 1, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', 'U', 10);
        $pdf->Cell(45, 10, (isset($optKarRow[$res['operator']]) ? $optKarRow[$res['operator']] : ''), '', 0, 'C');
        $pdf->Cell(45, 10, $optKarRow[$res['hedept']], '', 0, 'C');
        $pdf->Cell(45, 10, $optKarRow[$res['divmanager']], '', 0, 'C');
        $pdf->Cell(45, 10, $optKarRow[$res['workshop']], '', 1, 'C');
        $pdf->Output();

        break;
}

?>