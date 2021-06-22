<?php
include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_GET;
$cols = [];
$col1 = 'kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp';
$cols = explode(',', $col1);
$cols[0] = 'subunit';
$where = "notransaksi='".$param['notransaksi']."'";
$query = selectQuery($dbname, 'log_spkdt', $col1, $where);
$data = fetchData($query);
$align = explode(',', 'L,L,L,R,L,R');
$length = explode(',', '20,20,10,20,10,20');
if (empty($data)) {
    echo 'Data Kosong';
    exit();
}

$whereOrg = 'kodeorganisasi in (';
$whereKeg = 'kodekegiatan in (';
foreach ($data as $key => $row) {
    if (0 === $key) {
        $whereOrg .= "'".$row['kodeblok']."'";
        $whereKeg .= "'".$row['kodekegiatan']."'";
    } else {
        $whereOrg .= ",'".$row['kodeblok']."'";
        $whereKeg .= ",'".$row['kodekegiatan']."'";
    }
}
$whereOrg .= ",'".$param['kodeorg']."')";
$whereKeg .= ')';
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '0', true);
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $whereKeg, '0', true);
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', "supplierid='".$param['koderekanan']."'");
$optProj = makeOption($dbname, 'project', 'kode,nama');
$optProjker = makeOption($dbname, 'project_dt', 'kegiatan,namakegiatan');
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['kodeblok'] = $optOrg[$row['kodeblok']];
    if ('' === $optOrg[$row['kodeblok']]) {
        $dataShow[$key]['kodeblok'] = $optProj[$row['kodeblok']];
    }

    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
    if ('' === $optKeg[$row['kodekegiatan']]) {
        $dataShow[$key]['kodekegiatan'] = $optProjker[$row['kodekegiatan']];
    }
}
$col2 = 'tanggal,kodeblok,hkrealisasi,hasilkerjarealisasi,jumlahrealisasi';
$cols2 = explode(',', $col2);
$cols[1] = 'subunit';
$where2 = "notransaksi='".$param['notransaksi']."' and kodekegiatan in (";
foreach ($data as $key => $row) {
    if (0 === $key) {
        $where2 .= "'".$row['kodekegiatan']."'";
    } else {
        $where2 .= ",'".$row['kodekegiatan']."'";
    }
}
$where2 .= ')';
$query2 = selectQuery($dbname, 'v_spk_blm_realiasisi', $col2, $where2);
$data2 = fetchData($query2);
$align2 = explode(',', 'L,L,R,R,R');
$length2 = explode(',', '20,20,10,20,30');
/*
if (empty($data2)) {
    echo 'Data Realisasi belum ada';
    exit();
}
*/

$whereOrg2 = 'kodeorganisasi in (';
foreach ($data2 as $key => $row) {
    if (0 === $key) {
        $whereOrg2 .= "'".$row['kodeblok']."'";
    } else {
        $whereOrg2 .= ",'".$row['kodeblok']."'";
    }
}
$whereOrg2 .= ",'".$param['kodeorg']."')";
$optOrg2 = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg2, '0', true);
$dataShow2 = $data2;
foreach ($dataShow2 as $key => $row) {
    $dataShow2[$key]['kodeblok'] = $optOrg2[$row['kodeblok']];
}
$title = $_SESSION['lang']['spk'];
$titleDetail = [''];
switch ($proses) {
    case 'pdf':
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->_noThead = true;
        $pdf->setAttr1($title, $align, $length, []);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $_SESSION['lang']['notransaksi'].' : '.$param['notransaksi'], 0, 1, 'L', 1);
        $pdf->Cell($width, $height, $_SESSION['lang']['kodeorg'].' : '.$optOrg[$param['kodeorg']], 0, 1, 'L', 1);
        $pdf->Cell($width, $height, $_SESSION['lang']['koderekanan'].' : '.$optSupp[$param['koderekanan']], 0, 1, 'L', 1);
        $pdf->Ln();
        $pdf->Cell($width, $height, 'Rencana', 0, 0, 'L', 1);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $titleDetail[0], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $i = 0;

        $pdf->Cell($length[0] / 100 * $width, $height, 'Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Sub Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Hari Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Volume Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Satuan', 1, 0, 'C', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, 'Jumlah (Rp.)', 1, 0, 'C', 1);
/*        foreach ($cols as $column) {
            $pdf->Cell($length[$i] / 100 * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
            ++$i;
        }
*/        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        foreach ($dataShow as $key => $row) {
            $i = 0;
            foreach ($row as $cont) {
                $pdf->Cell($length[$i] / 100 * $width, $height, number_format($cont, 2, '.', ','), 1, 0, $align[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Cell($width, $height, 'Realisasi', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($width, $height, $titleDetail[0], 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $i = 0;
        foreach ($cols2 as $column) {
            $pdf->Cell($length2[$i] / 100 * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
            ++$i;
        }
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        foreach ($dataShow2 as $key => $row) {
            $i = 0;
            foreach ($row as $cont) {
                $pdf->Cell($length2[$i] / 100 * $width, $height, number_format($cont, 2, '.', ','), 1, 0, $align2[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Ln();

         $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(1 / 5 * $width, $height, 'Dibuat Oleh ('.date("d-m-Y").')', 'LTR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Pemborong','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Diperiksa','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Disetujui','TR',0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Dibayar','TR', 0, 'C', 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, $height, '', 'LBR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);

        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}



?>
