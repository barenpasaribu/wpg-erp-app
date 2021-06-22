<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/biReport.php';
include_once 'lib/zPdfMaster.php';
include_once 'lib/devLibrary.php';
$optSat = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$level = $_GET['level'];
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}

if ('pdf' === $mode) {
    $param = $_GET;
    unset($param['mode'], $param['level']);
} else {
    $param = $_POST;
}

$currTahun = $tahun = $param['periode_tahun'];
$currBulan = $bulan = $param['periode_bulan'];
++$bulan;
if (12 < $bulan) {
    $bulan = 1;
    ++$tahun;
}

if ($bulan < 10) {
    $bulan = '0'.$bulan;
}

$tanggalM = $tahun.'-'.$bulan.'-01';
if ($currBulan < 10) {
    $currBulan = '0'.$currBulan;
}

$currPeriod = $currTahun.$currBulan;
switch ($level) {
    case '0':
        $afd = substr($param['kodeorg'], 0, 6);
        $kodeorg = substr($param['kodeorg'], 0, 4);
        $optBelow = getOrgBelow($dbname, $kodeorg);
        if ('EN' === $_SESSION['language']) {
            $zz = 'namakegiatan1 as namakegiatan';
        } else {
            $zz = 'namakegiatan';
        }

        $cols = 'mat.notransaksi,akt.tanggal,keg.'.$zz.',mat.kodeorg,mat.kodebarang,mat.kwantitas,mat.hargasatuan,per.hasilkerja,keg.kodekegiatan';
        $where = "left(mat.notransaksi,6)='".$currPeriod."' and "."left(mat.kodeorg,4)='".$kodeorg."' and akt.jurnal=1 ";
        $query = "select $cols from $dbname.kebun_pakaimaterial as mat ".
            "join $dbname.kebun_aktifitas as akt on akt.notransaksi=mat.notransaksi ".
            "join $dbname.kebun_perawatan_vw as per on per.notransaksi=mat.notransaksi ".
            "join $dbname.setup_kegiatan as keg on keg.kodekegiatan=per.kodekegiatan ".
            "where ".$where;
        $tmpRes = fetchData($query);
        if (empty($tmpRes)) {
            echo 'Warning : No data found';
            exit();
        }

        $whereBrg = '';
        foreach ($tmpRes as $key => $row) {
            if (0 === $key) {
                $whereBrg .= "kodebarang='".$row['kodebarang']."'";
            } else {
                $whereBrg .= "or kodebarang='".$row['kodebarang']."'";
            }
        }
        $optBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang', $whereBrg);
        $strJ = 'select induk from '.$dbname.".organisasi where kodeorganisasi = '".$kodeorg."'";
        $resJ = mysql_query($strJ, $conn);
        while ($barJ = mysql_fetch_object($resJ)) {
            $induk = $barJ->induk;
        }
        $data = $tmpRes;
        $dataShow = $dataExcel = $data;
        foreach ($data as $key => $row) {
            $dataShow[$key]['kodeorg'] = $optBelow[$row['kodeorg']];
            $dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $dataShow[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
            $dataShow[$key]['namakegiatan'] = $row['namakegiatan'];
            $dataShow[$key]['kwantitas'] = number_format($row['kwantitas'], 2);
            $dataShow[$key]['hargasatuan'] = number_format($row['hargasatuan']);
            $dataShow[$key]['kodekegiatan'] = $optSat[$row['kodekegiatan']];
            $dataExcel[$key]['kodeorg'] = $optBelow[$row['kodeorg']];
            $dataExcel[$key]['tanggal'] = $row['tanggal'];
            $dataExcel[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
            $dataExcel[$key]['kwantitas'] = number_format($row['kwantitas'], 2);
            $dataExcel[$key]['hargasatuan'] = number_format($optHrg[$row['kodebarang']], 2);
            $dataExcel[$key]['kodekegiatan'] = $optSat[$row['kodekegiatan']];
            $tharga += $row['hargasatuan'];
        }
        $theCols = [$_SESSION['lang']['notransaksi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['namakegiatan'], $_SESSION['lang']['kodeorg'], $_SESSION['lang']['kodebarang'], $_SESSION['lang']['kwantitas'], $_SESSION['lang']['hargasatuan'], substr($_SESSION['lang']['hasilkerja'], 0, 12), $_SESSION['lang']['satuan']];
        $align = explode(',', 'L,L,L,L,L,R,R,R,L');

        break;
    default:
        break;
}
switch ($mode) {
    case 'pdf':
        $colPdf = ['notransaksi', 'tanggal', 'kodekegiatan', 'kodeorg', 'kodebarang', 'kwantitas', 'hargasatuan'];
        $title = $_SESSION['lang']['lapmaterial'];
        $length = explode(',', '15,7,20,20,20,10,10');
        $pdf = new zPdfMaster('L', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFont('Arial', '', 9);
        foreach ($dataShow as $key => $row) {
            $i = 0;
            foreach ($row as $head => $cont) {
                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Output();

        break;
    default:
        $alignPrev = [];
        foreach ($align as $key => $row) {
            switch ($row) {
                case 'L':
                    $alignPrev[$key] = 'left';

                    break;
                case 'R':
                    $alignPrev[$key] = 'right';

                    break;
                case 'C':
                    $alignPrev[$key] = 'center';

                    break;
            }
        }
        if ('excel' === $mode) {
            $tab = "<table border='1' cellspacing=0 class=sortable>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='kasharian' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }

        foreach ($theCols as $head) {
            $tab .= '<td>'.$head.'</td>';
        }
        $tab .= '</tr></thead>';
        $tab .= '<tbody>';
        foreach ($data as $key => $row) {
            $tab .= "<tr class='rowcontent'>";
            $i = 0;
            foreach ($row as $head => $cont) {
                if ('excel' === $mode) {
                    $tab .= "<td align='".$alignPrev[$i]."'>".$dataExcel[$key][$head].'</td>';
                } else {
                    $tab .= "<td align='".$alignPrev[$i]."'>".$dataShow[$key][$head].'</td>';
                }

                ++$i;
            }
            $tab .= '</tr>';
        }
        $tab .= '</tbody>';
        $tab .= '<tfoot><tr class=rowcontent><td colspan=6>Total</td><td align=right>'.number_format($tharga).'</td><td colspan=2>&nbsp;</td></tr></tbody>';
        $tab .= '</table>';
        if ('excel' === $mode) {
            $stream = $tab;
            $nop_ = 'PakaiMaterial';
            if (0 < strlen($stream)) {
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ('.' !== $file && '..' !== $file) {
                            @unlink('tempExcel/'.$file);
                        }
                    }
                    closedir($handle);
                }

                $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
                if (!fwrite($handle, $stream)) {
                    echo 'Error : can not write file';
                    exit();
                }

                echo $nop_;
                fclose($handle);
            }
        } else {
            echo $tab;
        }

        break;
}

?>