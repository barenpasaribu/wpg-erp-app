<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/fpdf.php';
// ('' !== $_GET['proses']  ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
// ('' === $_POST['kdUnit'] ? ($kdUnit = $_GET['kdUnit']) : ($kdUnit = $_POST['kdUnit']));
// ('' === $_POST['periodeUnit'] ? ($periodeUnit = $_GET['periodeUnit']) : ($periodeUnit = $_POST['periodeUnit']));

$proses = ($_GET['proses']===null || $_GET['proses']==='' ? $_POST['proses'] : $_GET['proses']);
$kdUnit = ($_POST['kdUnit']===null || $_POST['kdUnit']==='' ? $_GET['kdUnit'] : $_POST['kdUnit']); 
$periodeUnit = ($_POST['periodeUnit']===null || $_POST['periodeUnit']==='' ? $_GET['periodeUnit'] : $_POST['periodeUnit']);

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$kebun = substr($kdUnit, 0, 4);
$thn = explode('-', $periodeUnit);

$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$sInd = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$kebun."'";
$qInd = mysql_query($sInd);
$rInd = mysql_fetch_assoc($qInd);
// $sData = 'select distinct pagi,sore,tanggal from '.$dbname.".kebun_curahhujan\r\n        where kodeorg='".$kdUnit."' and substr(tanggal,1,7)  between '".$thn[0]."-01' and '".$periodeUnit."' group by tanggal";
$sData = 'select distinct pagi,sore,tanggal from '.$dbname.".kebun_curahhujan\r\n        where kodeorg='".$kdUnit."' and substr(tanggal,1,7)  between '".$thn[0]."-01' and '".$periodeUnit."' group by tanggal";
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
    $bln = explode('-', $rData['tanggal']);
    $bulan = (int) ($bln[1]);
    $tgl = (int) ($bln[2]);
    if (0 !== $rData['pagi'] || 0 !== $rData['sore']) {
        $dataCrh[$tgl.$bulan]['p'] = $rData['pagi'];
        $dataCrh[$tgl.$bulan]['s'] = $rData['sore'];
    }
}
if ('getPeriode' !== $proses && '' === $periodeUnit && '' === $kdUnit) {
    exit('Error: All field required');
}

switch ($proses) {
    case 'preview':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td>'.$_SESSION['lang']['afdeling'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$kdUnit].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kebun'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$kebun].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['pt'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$rInd['induk']].'</td></tr></table><br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['tanggal'].'</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td colspan=2>'.$arrBln[$adf].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '<tr class=rowheader>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td>'.$_SESSION['lang']['pagi'].'</td>';
            $tab .= '<td>'.$_SESSION['lang']['sore'].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '</thead><tbody>';
        for ($adr = 1; $adr < 32; ++$adr) {
            $tab .= '<tr class=rowcontent><td>'.$adr.'</td>';
            for ($adf = 1; $adf <= $thn[1]; ++$adf) {
                $tab .= '<td align=right>'.$dataCrh[$adr.$adf]['p'].'</td>';
                $tab .= '<td align=right>'.$dataCrh[$adr.$adf]['s'].'</td>';
                $sumMM[$adf]['p'] += $dataCrh[$adr.$adf]['p'];
                $sumMM[$adf]['s'] += $dataCrh[$adr.$adf]['s'];
                if (0 !== $dataCrh[$adr.$adf]['p'] || 0 !== $dataCrh[$adr.$adf]['s']) {
                    ++$sumHr[$adf];
                }
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['jumlah'].' (mm)</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td align=right>'.$sumMM[$adf]['p'].'</td>';
            $tab .= '<td align=right>'.$sumMM[$adf]['s'].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['jumlah'].'   (hari hujan/rainy days)</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td align=right colspan=2>'.$sumHr[$adf].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'excel':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td colspan=5>'.$_SESSION['lang']['curahharian'].' '.$_SESSION['lang']['tahun'].' '.$thn[0].'</td></tr>';
        $tab .= '<tr><td colspan=3>'.$_SESSION['lang']['afdeling'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$kdUnit].'</td></tr>';
        $tab .= '<tr><td colspan=3>'.$_SESSION['lang']['kebun'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$kebun].'</td></tr>';
        $tab .= '<tr><td colspan=3>'.$_SESSION['lang']['pt'].'</td>';
        $tab .= '<td>:</td><td>'.$optNmOrg[$rInd['induk']].'</td></tr></table><br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['tanggal'].'</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td colspan=2>'.$arrBln[$adf].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '<tr class=rowheader>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td>'.$_SESSION['lang']['pagi'].'</td>';
            $tab .= '<td>'.$_SESSION['lang']['sore'].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '</thead><tbody>';
        for ($adr = 1; $adr < 32; ++$adr) {
            $tab .= '<tr class=rowcontent><td>'.$adr.'</td>';
            for ($adf = 1; $adf <= $thn[1]; ++$adf) {
                $tab .= '<td align=right>'.$dataCrh[$adr.$adf]['p'].'</td>';
                $tab .= '<td align=right>'.$dataCrh[$adr.$adf]['s'].'</td>';
                $sumMM[$adf]['p'] += $dataCrh[$adr.$adf]['p'];
                $sumMM[$adf]['s'] += $dataCrh[$adr.$adf]['s'];
                if (0 !== $dataCrh[$adr.$adf]['p'] || 0 !== $dataCrh[$adr.$adf]['s']) {
                    ++$sumHr[$adf];
                }
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['jumlah'].' (mm)</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td align=right>'.$sumMM[$adf]['p'].'</td>';
            $tab .= '<td align=right>'.$sumMM[$adf]['s'].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['jumlah'].'   (hari hujan/rainy days)</td>';
        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
            $tab .= '<td align=right colspan=2>'.$sumHr[$adf].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('His');
        $nop_ = 'curahHujanHarian_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls.gz';\r\n                        </script>";

        break;
    case 'getPeriode':
        $optper = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sTgl = "select distinct substr(tanggal,1,7) as periode from $dbname.kebun_curahhujan ".
            "where kodeorg like '".$kdUnit."%' order by tanggal desc";
            saveLog($sTgl);

        // $qTgl = mysql_query($sTgl);
        // while ($rTgl = mysql_fetch_assoc($qTgl)) {
        //     $optper .= "<option value='".$rTgl['periode']."'>".substr($rTgl['periode'], 8, 2).'-'.substr($rTgl['periode'], 5, 2).'-'.substr($rTgl['periode'], 0, 4).'</option>';
        // }
        // echo $optper;

        $qTgl = mysql_query($sTgl);
        while ($rTgl = mysql_fetch_assoc($qTgl)) {
            $optper .= "<option value='".$rTgl['periode']."'>".substr($rTgl['periode'], 5, 2).'-'.substr($rTgl['periode'], 0, 4).'</option>';
        }
        echo ($optper);
        // $optper = makeOption2( $sTgl,
        //     array("valueinit" => '', "captioninit" => $_SESSION['lang']['pilihdata']),
        //     array("valuefield" => 'periode', "captionfield" => 'periode')
        // );
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
        // echo ($optper);

//        $qTgl = mysql_query($sTgl);
//        while ($rTgl = mysql_fetch_assoc($qTgl)) {
//            $optper .= "<option value='".$rTgl['periode']."'>".substr($rTgl['periode'], 5, 2).'-'.substr($rTgl['periode'], 0, 4).'</option>';
//        }
        // $optper = makeOption2( $sTgl,
        //     array("valueinit" => '', "captioninit" => $_SESSION['lang']['pilihdata']),
        //     array("valuefield" => 'periode', "captionfield" => 'periode')
        // );
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
        // echo ($optper);

        break;
//    case 'pdf':
//
//        class PDF extends FPDF
//        {
//            public function Header()
//            {
//                global $optNmOrg;
//                global $dbname;
//                global $kdUnit;
//                global $periodeUnit;
//                global $rInd;
//                global $arrBln;
//                global $thn;
//                $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
//                $qAlamat = mysql_query($sAlmat);
//                $rAlamat = mysql_fetch_assoc($qAlamat);
//                $width = $this->w - $this->lMargin - $this->rMargin;
//                $height = 10;
//                if ('SSP' === $_SESSION['org']['kodeorganisasi']) {
//                    $path = 'images/SSP_logo.jpg';
//                } else {
//                    if ('MJR' === $_SESSION['org']['kodeorganisasi']) {
//                        $path = 'images/MI_logo.jpg';
//                    } else {
//                        if ('HSS' === $_SESSION['org']['kodeorganisasi']) {
//                            $path = 'images/HS_logo.jpg';
//                        } else {
//                            if ('BNM' === $_SESSION['org']['kodeorganisasi']) {
//                                $path = 'images/BM_logo.jpg';
//                            }
//                        }
//                    }
//                }
//
//                $this->Image($path, $this->lMargin, $this->tMargin, 70);
//                $this->SetFont('Arial', 'B', 9);
//                $this->SetFillColor(255, 255, 255);
//                $this->SetX(100);
//                $this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
//                $this->SetX(100);
//                $this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
//                $this->SetX(100);
//                $this->Cell($width - 100, $height, 'Tel: '.$rAlamat['telepon'], 0, 1, 'L');
//                $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
//                $this->Ln();
//                $this->Ln();
//                $this->Ln();
//                $kebun = substr($kdUnit, 0, 4);
//                $this->SetFont('Arial', 'B', 11);
//                $this->Cell($width, $height, strtoupper($_SESSION['lang']['curahharian'].' '.$_SESSION['lang']['tahun'].' '.$thn[0]), 0, 1, 'C');
//                $this->Ln();
//                $this->SetFont('Arial', '', 8);
//                $this->Cell(50, $height, $_SESSION['lang']['afdeling'], 0, 0, 'L');
//                $this->Cell(10, $height, ':', '', 0, 0, 'L');
//                $this->Cell(70, $height, $optNmOrg[$kdUnit], 0, 1, 'L');
//                $this->Cell(50, $height, $_SESSION['lang']['kebun'], 0, 0, 'L');
//                $this->Cell(10, $height, ':', '', 0, 0, 'L');
//                $this->Cell(70, $height, $optNmOrg[$kebun], 0, 1, 'L');
//                $this->Cell(50, $height, $_SESSION['lang']['pt'], 0, 0, 'L');
//                $this->Cell(10, $height, ':', '', 0, 0, 'R');
//                $this->Cell(70, $height, $optNmOrg[$rInd['induk']], 0, 1, 'L');
//                $this->Cell(50, $height, $_SESSION['lang']['page'], 0, 0, 'L');
//                $this->Cell(10, $height, ':', '', 0, 0, 'R');
//                $this->Cell(70, $height, $this->PageNo(), 0, 1, 'L');
//                $this->Cell(50, $height, 'User', 0, 0, 'L');
//                $this->Cell(10, $height, ':', '', 0, 0, 'L');
//                $this->Cell(70, $height, $_SESSION['standard']['username'], 0, 1, 'L');
//                $this->Ln(10);
//                $height = 15;
//                $this->SetFillColor(220, 220, 220);
//                $this->SetFont('Arial', 'B', 7);
//                $this->Cell(60, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
//                for ($adf = 1; $adf <= $thn[1]; ++$adf) {
//                    if ($adf !== $thn[1]) {
//                        $this->Cell(35, $height, $arrBln[$adf], 1, 0, 'C', 1);
//                    } else {
//                        $this->Cell(35, $height, $arrBln[$adf], 1, 1, 'C', 1);
//                    }
//                }
//                $this->Cell(60, $height, '', 1, 0, 'C', 1);
//                for ($adf = 1; $adf <= $thn[1]; ++$adf) {
//                    if ($adf !== $thn[1]) {
//                        $this->Cell(35, $height, $_SESSION['lang']['pagi'].' '.$_SESSION['lang']['sore'], 1, 0, 'C', 1);
//                    } else {
//                        $this->Cell(35, $height, $_SESSION['lang']['pagi'].' '.$_SESSION['lang']['sore'], 1, 1, 'C', 1);
//                    }
//                }
//            }
//
//            public function Footer()
//            {
//                $this->SetY(-15);
//                $this->SetFont('Arial', 'I', 8);
//                $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
//            }
//        }
//
//        $pdf = new PDF('P', 'pt', 'A4');
//        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
//        $height = 10;
//        $pdf->AddPage();
//        $pdf->SetFillColor(255, 255, 255);
//        $pdf->SetFont('Arial', '', 7);
//        for ($adr = 1; $adr < 32; ++$adr) {
//            $pdf->Cell(60, $height, $adr, 1, 0, 'C', 1);
//            for ($adf = 1; $adf <= $thn[1]; ++$adf) {
//                if ($adf !== $thn[1]) {
//                    $pdf->Cell(17.5, $height, $dataCrh[$adr.$adf]['p'], 1, 0, 'R', 1);
//                    $pdf->Cell(17.5, $height, $dataCrh[$adr.$adf]['s'], 1, 0, 'R', 1);
//                } else {
//                    $pdf->Cell(17.5, $height, $dataCrh[$adr.$adf]['p'], 1, 0, 'R', 1);
//                    $pdf->Cell(17.5, $height, $dataCrh[$adr.$adf]['s'], 1, 1, 'R', 1);
//                }
//
//                $sumMM[$adf]['p'] += $dataCrh[$adr.$adf]['p'];
//                $sumMM[$adf]['s'] += $dataCrh[$adr.$adf]['s'];
//                if (0 !== $dataCrh[$adr.$adf]['p'] || 0 !== $dataCrh[$adr.$adf]['s']) {
//                    ++$sumHr[$adf];
//                }
//            }
//        }
//        $pdf->SetFont('Arial', '', 6);
//        $pdf->Cell(60, $height, 'Jumlah (mm)', 1, 0, 'L', 1);
//        $pdf->SetFont('Arial', '', 7);
//        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
//            if ($adf !== $thn[1]) {
//                $pdf->Cell(17.5, $height, $sumMM[$adf]['p'], 1, 0, 'R', 1);
//                $pdf->Cell(17.5, $height, $sumMM[$adf]['s'], 1, 0, 'R', 1);
//            } else {
//                $pdf->Cell(17.5, $height, $sumMM[$adf]['p'], 1, 0, 'R', 1);
//                $pdf->Cell(17.5, $height, $sumMM[$adf]['s'], 1, 1, 'R', 1);
//            }
//        }
//        $pdf->SetFont('Arial', '', 6);
//        $pdf->Cell(60, $height, 'Jumlah (hari hujan)', 1, 0, 'L', 1);
//        $pdf->SetFont('Arial', '', 7);
//        for ($adf = 1; $adf <= $thn[1]; ++$adf) {
//            if ($adf !== $thn[1]) {
//                $pdf->Cell(35, $height, $sumHr[$adf], 1, 0, 'R', 1);
//            } else {
//                $pdf->Cell(35, $height, $sumHr[$adf], 1, 1, 'R', 1);
//            }
//        }
//        $pdf->Output();
//
//        break;
    default:
        break;
}

?>