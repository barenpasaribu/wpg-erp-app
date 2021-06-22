<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
if (empty($_POST)) {
    $param = $_GET;
    unset($param['proses']);
} else {
    $param = $_POST;
}

$tmpPeriod = explode('-', $param['periode']);
list($bulan, $tahun) = $tmpPeriod;
$unit = $param['unit'];
if ('' === $unit) {
    $unit = makeOption($dbname, 'organisasi', 'kodeorganisasi,kodeorganisasi', "induk='".$param['pt']."'");
}

$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$param['pt']."' or induk='".$param['pt']."'");
$where = "left(h.tanggal,7)='".$tahun.'-'.$bulan."' and h.kodejurnal='M'";
if (is_array($unit)) {
    $i = 0;
    $where .= ' and (';
    foreach ($unit as $row) {
        if (0 === $i) {
            $where .= "substr(h.nojurnal,10,4)='".$row."'";
        } else {
            $where .= " or substr(h.nojurnal,10,4)='".$row."'";
        }

        ++$i;
    }
    $where .= ')';
} else {
    $where .= " and substr(h.nojurnal,10,4)='".$unit."'";
}

if ('all' === $param['revisi']) {
    $where .= '';
} else {
    $where .= " and h.revisi = '".$param['revisi']."'";
}

if ('EN' === $_SESSION['language']) {
    $zz = ' a.namaakun1 as namaakun';
} else {
    $zz = 'a.namaakun';
}

$query = 'select h.nojurnal,d.noakun,'.$zz.',d.keterangan,d.jumlah,h.revisi from `'.$dbname."`.`keu_jurnaldt` as d\r\n     left  join  `".$dbname."`.`keu_jurnalht` as h on h.nojurnal=d.nojurnal \r\n     left join ".$dbname.'.keu_5akun a on d.noakun=a.noakun where '.$where;
$tmpRes = fetchData($query);
$data = [];
$i = 1;
$totalDebet = 0;
$totalKredit = 0;
foreach ($tmpRes as $key => $row) {
    $data[$i - 1] = ['no' => $i, 'nojurnal' => $row['nojurnal'], 'unit' => $optOrg[substr($row['nojurnal'], 9, 4)], 'akun' => $row['noakun'], 'namaakun' => $row['namaakun'], 'ket' => $row['keterangan'], 'debet' => 0, 'kredit' => 0];
    if ($row['jumlah'] < 0) {
        $data[$i - 1]['kredit'] = $row['jumlah'] * -1;
        $totalKredit += $row['jumlah'] * -1;
    } else {
        $data[$i - 1]['debet'] = $row['jumlah'];
        $totalDebet += $row['jumlah'];
    }

    $data[$i - 1]['kredit'] = number_format($data[$i - 1]['kredit'], 0);
    $data[$i - 1]['debet'] = number_format($data[$i - 1]['debet'], 0);
    ++$i;
}
$cols = 'nourut,nojurnal,unit,noakun,namaakun,keterangan,debet,kredit';
$colArr = explode(',', $cols);
$align = explode(',', 'L,L,L,L,L,L,R,R');
$length = explode(',', '4,15,15,10,17,16,10,10');
$title = 'Summary Memorial Journal';
$table = "<table class='sortable' border=0 cellspacing=1>";
if ('excel' === $proses) {
    $table = "<table class='sortable' border=1 cellspacing=1>";
}

$table .= "<thead><tr class='rowheader'>";
foreach ($colArr as $head) {
    $table .= '<td align=center>'.$_SESSION['lang'][$head].'</td>';
}
$table .= '</tr></thead>';
$table .= '<tbody>';
foreach ($data as $key => $row) {
    $table .= "<tr class='rowcontent'>";
    $i = 0;
    foreach ($row as $cont) {
        switch ($align[$i]) {
            case 'L':
                $tmpAlign = 'left';

                break;
            case 'R':
                $tmpAlign = 'right';

                break;
            case 'C':
                $tmpAlign = 'center';

                break;
            default:
                break;
        }
        $table .= "<td align='".$tmpAlign."'>".$cont.'</td>';
        ++$i;
    }
    $table .= '</tr>';
}
$table .= "<tr class='rowcontent'><td colspan='6' align='center'>TOTAL</td>";
$table .= "<td align='right'>".number_format($totalDebet, 0).'</td>';
$table .= "<td align='right'>".number_format($totalKredit, 0).'</td>';
$table .= '</tr>';
$table .= '</tbody>';
$table .= '</table>';
switch ($proses) {
    case 'preview':
        echo $table;

        break;
    case 'excel':
        $nop_ = 'Summary_JMemorial '.$unit.' '.$tahun.'-'.$bulan;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $table);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                                     window.location='tempExcel/".$nop_.".xls.gz';\r\n                                     </script>";

        break;
    case 'pdf':
        $pdf = new zPdfMaster('L', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        foreach ($data as $key => $row) {
            $i = 0;
            foreach ($row as $cont) {
                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Cell(77 / 100 * $width, $height, 'TOTAL', 1, 0, 'C', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($totalDebet, 0), 1, 0, 'R', 1);
        $pdf->Cell(10 / 100 * $width, $height, number_format($totalKredit, 0), 1, 0, 'R', 1);
        $pdf->Output();

        break;
    default:
        break;
}

?>