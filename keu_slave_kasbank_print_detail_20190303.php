<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
include_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$param = $_GET;
$cols = [];
$whereH = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
$queryH = selectQuery($dbname, 'keu_kasbankht', '*', $whereH);
$resH = fetchData($queryH);
$userId = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "karyawanid='".$resH[0]['userid']."'");
$namaakunhutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', "noakun='".$resH[0]['noakunhutang']."'");
$col1 = 'noakun,sum(jumlah) as jumlah,noaruskas,matauang,kode,nik,kodesupplier';
$cols = ['nomor', 'noakun', 'namaakun', 'matauang', 'debet', 'kredit'];
$where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
$query = 'select '.$col1.' from '.$dbname.'.keu_kasbankdt where '.$where.' group by noakun ';
$res = fetchData($query);

$queryditerima = 'select * from '.$dbname.'.datakaryawan a join '.$dbname.'.keu_kasbankht b on a.karyawanid = b.diterima where '.$whereH;
$resditerima = fetchData($queryditerima);
$DiterimaId = $resditerima[0]['namakaryawan'];

$diperiksa = 'select * from '.$dbname.'.datakaryawan a join '.$dbname.'.keu_kasbankht b on a.karyawanid = b.diperiksa where '.$whereH;
$resdiperiksa = fetchData($diperiksa);
$DiperiksaId = $resdiperiksa[0]['namakaryawan'];

$querydisetujui = 'select * from '.$dbname.'.datakaryawan a join '.$dbname.'.keu_kasbankht b on a.karyawanid = b.disetujui where '.$whereH;
$resdisetujui = fetchData($querydisetujui);
$DisetujuiId = $resdisetujui[0]['namakaryawan'];

$kary = $supp = [];
foreach ($res as $row) {
    if (!empty($row['nik'])) {
        $kary[$row['nik']] = $row['nik'];
    }

    if (!empty($row['kodesupplier'])) {
        $supp[$row['kodesupplier']] = $row['kodesupplier'];
    }
}
if (empty($res)) {
    echo 'Data Empty';
    exit();
}

$whereAkun = 'noakun in (';
$whereAkun .= "'".$resH[0]['noakun']."'";
$whereAkun .= ",'".$resH[0]['noakunhutang']."'";
$whereKary = $whereSupp = '';
foreach ($res as $key => $row) {
    if (!empty($whereKary)) {
        $whereKary .= ',';
    }

    if (!empty($whereSupp)) {
        $whereSupp .= ',';
    }

    $whereAkun .= ",'".$row['noakun']."'";
    $whereKary .= "'".$row['nik']."'";
    $whereSupp .= "'".$row['kodesupplier']."'";
}
$whereAkun .= ')';
$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'karyawanid in ('.$whereKary.')');
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'supplierid in ('.$whereSupp.')');
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
$optHutangUnit = ['Tidak', 'Ya'];
$data = [];
$totalDebet = 0;
$totalKredit = 0;
$i = 1;
$data[$i] = ['nomor' => $i, 'noakun' => $resH[0]['noakun'], 'namaakun' => $optAkun[$resH[0]['noakun']], 'matauang' => $resH[0]['matauang'], 'debet' => 0, 'kredit' => 0];
if ('M' == $param['tipetransaksi']) {
    $data[$i]['debet'] = $resH[0]['jumlah'];
    $totalDebet += $resH[0]['jumlah'];
} else {
    $data[$i]['kredit'] = $resH[0]['jumlah'];
    $totalKredit += $resH[0]['jumlah'];
}

++$i;
foreach ($res as $row) {
    $data[$i] = ['nomor' => $i, 'noakun' => $row['noakun'], 'namaakun' => (isset($optAkun[$row['noakun']]) ? $optAkun[$row['noakun']] : ''), 'matauang' => $row['matauang'], 'debet' => 0, 'kredit' => 0];
    if ($param['tipetransaksi'] == 'M' && 0 < $row['jumlah']) {
        $data[$i]['kredit'] = $row['jumlah'];
        $totalKredit += $row['jumlah'];
    } else {
        if ($param['tipetransaksi' == 'K'] && $row['jumlah'] < 0) {
            $data[$i]['kredit'] = $row['jumlah'] * -1;
            $totalKredit += $row['jumlah'] * -1;
        } else {
            if ($param['tipetransaksi' == 'M'] && $row['jumlah'] < 0) {
                $data[$i]['debet'] = $row['jumlah'] * -1;
                $totalDebet += $row['jumlah'] * -1;
            } else {
                $data[$i]['debet'] = $row['jumlah'];
                $totalDebet += $row['jumlah'];
            }
        }
    }

    $i++;
}
if (!empty($data)) {
    foreach ($data as $c => $key) {
        $sort_debet[] = $key['debet'];
        $sort_kredit[] = $key['kredit'];
    }
}

if (!empty($data)) {
    array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);
}

$align = explode(',', 'R,R,L,L,R,R');
$length = explode(',', '7,12,35,10,18,18');
$title = $_SESSION['lang']['kasbank'];
$titleDetail = 'Detail';
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
        $pdf->Cell(350, $height, $_SESSION['lang']['notransaksi'].' : '.$res[0]['kode'].'/'.$param['notransaksi'], 0, 0, 'L', 1);
        if (!empty($resH[0]['nobayar'])) {
            $nmbyr = $resH[0]['nobayar'];
        } else {
            $nmbyr = '';
        }

        $pdf->Cell($width, $height, 'No. Pembayaran : '.$nmbyr, 0, 1, 'L', 1);
        if (!empty($optKary)) {
            if ($resH[0]['tipetransaksi'] == 'K') {
                $pdf->Cell(350, $height, 'Dibayar kepada : '.$optKary[key($optKary)], 0, 0, 'L', 1);
            } else {
                $pdf->Cell(350, $height, 'Dibayar oleh : '.$optKary[key($optKary)], 0, 0, 'L', 1);
            }
        } else {
            if (!empty($optSupp)) {
                if ($resH[0]['tipetransaksi'] == 'K') {
                    $pdf->Cell(350, $height, 'Dibayar kepada : '.$optSupp[key($optSupp)], 0, 0, 'L', 1);
                } else {
                    $pdf->Cell(350, $height, 'Diterima Oleh : '.$optSupp[key($optSupp)], 0, 0, 'L', 1);
                }
            }
        }

        if ($resH[0]['tanggalposting'] == '0000-00-00') {
            $tglPost = '';
        } else {
            $tglPost = tanggalnormal($resH[0]['tanggalposting']);
        }

        $pdf->Cell($width, $height, $_SESSION['lang']['tanggalposting'].' : '.$tglPost, 0, 1, 'L', 1);
        if (!empty($resH[0]['nogiro'])) {
            $nogiro = ', '.$resH[0]['nogiro'];
        }

        if ($resH[0]['tipetransaksi'] == 'K') {
            $byr = $_SESSION['lang']['cgttu'];
        } else {
            $byr = 'Diterima Dengan';
        }

        $pdf->Cell($width, $height, $byr.' : '.$resH[0]['cgttu'].$nogiro, 0, 1, 'L', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $optMt = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $pdf->MultiCell($width, $height, $_SESSION['lang']['terbilang'].' : '.terbilang($resH[0]['jumlah'], 2).' '.strtolower($optMt[$resH[0]['matauang']]), 0);
        $pdf->SetFillColor(220, 220, 220);
        $i = 0;
        foreach ($cols as $column) {
            $pdf->Cell($length[$i] / 100 * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
            ++$i;
        }
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $nyomor = 0;
        $flaghutangunit = 0;
        foreach ($data as $key => $row) {
            $nyomor++;
            $i = 0;
            foreach ($row as $key => $cont) {
                if ($key == 'nomor') {
                    $pdf->Cell($length[$i] / 100 * $width, $height, $nyomor, 1, 0, $align[$i], 1);
                } else {
                    if ($key == 'debet' || $key == 'kredit') {
                        $pdf->Cell($length[$i] / 100 * $width, $height, number_format($cont, 2), 1, 0, $align[$i], 1);
                    } else {
                        if ($key == 'noakun') {
                            if (substr($cont, 0, 3) == '121' && $resH[0]['hutangunit'] == 1) {
                                $cont = $resH[0]['noakunhutang'];
                                $flaghutangunit = 1;
                            }

                            $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                        } else {
                            if ($key == 'namaakun') {
                                if ($flaghutangunit == 1) {
                                    $cont = $optAkun[$resH[0]['noakunhutang']];
                                    $flaghutangunit = 0;
                                }

                                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                            } else {
                                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                            }
                        }
                    }
                }

                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->SetFont('Arial', 'B', 9);
        $lenTotal = $length[0] + $length[1] + $length[2] + $length[3];
        $pdf->Cell($lenTotal / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($totalDebet, 2), 1, 0, 'R', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, number_format($totalKredit, 2), 1, 0, 'R', 1);
        $pdf->Ln();
        $pdf->MultiCell($width, $height, $_SESSION['lang']['remark'].' : '.$resH[0]['keterangan']);
        if ($resH[0]['hutangunit'] == 1) {
            $pdf->MultiCell($width, $height, 'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']]);
        }

        $pdf->Ln();
        $pdf->SetFillColor(220, 220, 220);
        if ($param['tipetransaksi'] == 'M') {
            $pdf->Cell(33 / 100 * $width, $height, $_SESSION['lang']['disetujui'], 1, 0, 'C', 1);
             $pdf->Cell(33 / 100 * $width, $height, $_SESSION['lang']['diperiksa'], 1, 0, 'C', 1);
            $pdf->Cell(34 / 100 * $width, $height, $_SESSION['lang']['diterima'], 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            for ($i = 0; $i < 3; ++$i) {
                $pdf->Cell(33 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Cell(33 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Cell(34 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Ln();
            }
            $pdf->Cell(33 / 100 * $width, $height, $resdisetujui[0]['namakaryawan'], 'BLR', 0, 'C', 1);
            $pdf->Cell(33 / 100 * $width, $height, $resdiperiksa[0]['namakaryawan'], 'BLR', 0, 'C', 1);
            $pdf->Cell(34 / 100 * $width, $height, $resditerima[0]['namakaryawan'], 'BLR', 0, 'C', 1);
        } else {
            
            $pdf->Cell(33 / 100 * $width, $height, $_SESSION['lang']['disetujui'], 1, 0, 'C', 1);
            $pdf->Cell(33 / 100 * $width, $height, $_SESSION['lang']['diperiksa'], 1, 0, 'C', 1);
            $pdf->Cell(34 / 100 * $width, $height, "Dibayarkan", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            for ($i = 0; $i < 3; ++$i) {
                $pdf->Cell(33 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Cell(33 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Cell(34 / 100 * $width, $height, '', 'LR', 0, 'C', 1);
                $pdf->Ln();
            }
            $pdf->Cell(33 / 100 * $width, $height, $resdisetujui[0]['namakaryawan'], 'BLR', 0, 'C', 1);
            $pdf->Cell(33 / 100 * $width, $height, $resdiperiksa[0]['namakaryawan'], 'BLR', 0, 'C', 1);
            $pdf->Cell(34 / 100 * $width, $height, $resditerima[0]['namakaryawan'], 'BLR', 0, 'C', 1);

            // $pdf->Cell(20 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
            // $pdf->Cell(20 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
            // $pdf->Cell(20 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
            // $pdf->Cell(20 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
            // $pdf->Cell(20 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
        }

        $pdf->Output();

        break;
    case 'excel':
        break;
    case 'html':
        $whereH = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $queryH = selectQuery($dbname, 'keu_kasbankht', '*', $whereH);
        $resH = fetchData($queryH);
        $userId = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "karyawanid='".$resH[0]['userid']."'");
        $namaakunhutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', "noakun='".$resH[0]['noakunhutang']."'");
        $col1 = 'noakun,jumlah,noaruskas,matauang,kode,nik,keterangan2,kodesupplier';
        $cols = ['nomor', 'noakun', 'namaakun', 'matauang', 'keterangan', 'kodesupplier', 'debet', 'kredit'];
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $query = selectQuery($dbname, 'keu_kasbankdt', $col1, $where);
        $res = fetchData($query);
        $kary = $supp = [];
        foreach ($res as $row) {
            if (!empty($row['nik'])) {
                $kary[$row['nik']] = $row['nik'];
            }

            if (!empty($row['kodesupplier'])) {
                $supp[$row['kodesupplier']] = $row['kodesupplier'];
            }
        }
        if (empty($res)) {
            echo 'Data Empty';
            exit();
        }

        $whereAkun = 'noakun in (';
        $whereAkun .= "'".$resH[0]['noakun']."'";
        $whereAkun .= ",'".$resH[0]['noakunhutang']."'";
        $whereKary = $whereSupp = '';
        foreach ($res as $key => $row) {
            if (!empty($whereKary)) {
                $whereKary .= ',';
            }

            if (!empty($whereSupp)) {
                $whereSupp .= ',';
            }

            $whereAkun .= ",'".$row['noakun']."'";
            $whereKary .= "'".$row['nik']."'";
            $whereSupp .= "'".$row['kodesupplier']."'";
        }
        $whereAkun .= ')';
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'karyawanid in ('.$whereKary.')');
        $optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'supplierid in ('.$whereSupp.')');
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
        $optHutangUnit = ['Tidak', 'Ya'];
        $data = [];
        $totalDebet = 0;
        $totalKredit = 0;
        $i = 1;
        $data[$i] = ['nomor' => $i, 'noakun' => $resH[0]['noakun'], 'namaakun' => $optAkun[$resH[0]['noakun']], 'matauang' => $resH[0]['matauang'], '-' => '', '- ' => '', 'debet' => 0, 'kredit' => 0];
        if ($param['tipetransaksi'] == 'M') {
            $data[$i]['debet'] = $resH[0]['jumlah'];
            $totalDebet += $resH[0]['jumlah'];
        } else {
            $data[$i]['kredit'] = $resH[0]['jumlah'];
            $totalKredit += $resH[0]['jumlah'];
        }

        $i++;
        foreach ($res as $row) {
            $data[$i] = ['nomor' => $i, 'noakun' => $row['noakun'], 'namaakun' => (isset($optAkun[$row['noakun']]) ? $optAkun[$row['noakun']] : ''), 'matauang' => $row['matauang'], 'keterangan2' => $row['keterangan2'], 'kodesupplier' => $row['kodesupplier'], 'debet' => 0, 'kredit' => 0];
            if ($param['tipetransaksi'] == 'M' && 0 < $row['jumlah'] ) {
                $data[$i]['kredit'] = $row['jumlah'];
                $totalKredit += $row['jumlah'];
            } else {
                if ($param['tipetransaksi'] == 'K' && $row['jumlah'] < 0) {
                    $data[$i]['kredit'] = $row['jumlah'] * -1;
                    $totalKredit += $row['jumlah'] * -1;
                } else {
                    if ($param['tipetransaksi'] == 'M' && $row['jumlah'] < 0) {
                        $data[$i]['debet'] = $row['jumlah'] * -1;
                        $totalDebet += $row['jumlah'] * -1;
                    } else {
                        $data[$i]['debet'] = $row['jumlah'];
                        $totalDebet += $row['jumlah'];
                    }
                }
            }

            $i++;
        }
        if (!empty($data)) {
            foreach ($data as $c => $key) {
                $sort_debet[] = $key['debet'];
                $sort_kredit[] = $key['kredit'];
            }
        }

        if (!empty($data)) {
            array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);
        }

        $align = explode(',', 'R,R,L,L,R,R');
        $length = explode(',', '7,12,35,10,18,18');
        $title = $_SESSION['lang']['kasbank'];
        $titleDetail = 'Detail';
        $tab .= '<link rel=stylesheet type=text/css href=style/generic.css>';
        $tab .= '<fieldset><legend>'.$title.'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><tbody class=rowcontent>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeorganisasi'].'</td><td> :</td><td> '.$_SESSION['empl']['lokasitugas'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['notransaksi'].'</td><td> :</td><td> '.$res[0]['kode'].'/'.$param['notransaksi'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['cgttu'].'</td><td> :</td><td> '.$resH[0]['cgttu'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['terbilang'].'</td><td> :</td><td> '.terbilang($resH[0]['jumlah'], 2).' rupiah'.'</td></tr>';
        if ($resH[0]['hutangunit'] == 1) {
            $tab .= '<tr><td>'.$_SESSION['lang']['hutangunit'].'</td><td> :</td><td> '.'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']].'</td></tr>';
        }

        $tab .= '</tbody></table><br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><thead><tr class=rowheader>';
        foreach ($cols as $column) {
            $tab .= '<td>'.$_SESSION['lang'][$column].'</td>';
        }
        $tab .= '</tr></thead><tbody class=rowcontent>';
        $nyomor = 0;
        foreach ($data as $key => $row) {
            ++$nyomor;
            $tab .= '<tr>';
            foreach ($row as $key => $cont) {
                if ($key == 'nomor') {
                    $tab .= '<td>'.$nyomor.'</td>';
                } else {
                    if ($key == 'kodesupplier') {
                        $tab .= '<td>'.$optSupp[$cont].'</td>';
                    } else {
                        if ($key == 'debet' || $key == 'kredit') {
                            $tab .= '<td align=right>'.number_format($cont, 0).'</td>';
                        } else {
                            $tab .= '<td>'.$cont.'</td>';
                        }
                    }
                }
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr><td colspan=6 align=center>Total</td><td align=right>'.number_format($totalDebet, 0).'</td><td align=right>'.number_format($totalKredit, 0).'</td></tr>';
        $tab .= '</tbody></table> <br />';
        echo $tab;

        break;
    default:
        break;
}

?>