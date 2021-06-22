<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/biReport.php';
include_once 'lib/zPdfMaster.php';
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

$currPeriod = $currTahun.'-'.$currBulan;
$arrTopografo = ['D1' => 'DATAR', 'D2' => 'BERGELOMBANG', 'B1' => 'BUKIT'];
switch ($level) {
    case '0':
        $optBelow = getOrgBelow($dbname, $param['unit']);
        $cols = 'kodeorg,tahuntanam,kodeorg,bloklama,statusblok,luasareanonproduktif,luasareaproduktif,jumlahpokok,cadangan,okupasi,rendahan,sungai,rumah,kantor,pabrik,jalan,kolam,umum,tanggalpengakuan,topografi';

        $where .= "1 and bloklama='' ";

 /*       if ('' === $param['unit']) {
            $where = "tanggalpengakuan<'".$tanggalM."'";
        } else {
            $where = "tanggalpengakuan<'".$tanggalM."' and left(kodeorg,4)='".$param['unit']."'";
        }
*/
        if ('' !== $param['tahuntanam']) {
            $where .= ' and tahuntanam='.$param['tahuntanam'];
        }

        if ('' !== $param['afdeling']) {
            $where .= " and left(kodeorg,5)='".$param['afdeling']."'";
        }

        $where .= ' and (luasareaproduktif+luasareanonproduktif > 0)';

        $query = selectQuery($dbname, 'setup_blok', $cols, $where, 'tahuntanam asc,kodeorg');
        $tmpBlok = fetchData($query);
        if (empty($tmpBlok)) {
            echo 'Warning : No data found';
            exit();
        }

        $resBlok = [];
        foreach ($tmpBlok as $row) {
            if (!isset($resBlok[$row['tahuntanam']][$row['kodeorg']])) {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal'] = ['luas' => 0, 'luasareaproduktif' => 0, 'luasareanonproduktif' => 0, 'pokok' => 0];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi'] = ['luas' => 0, 'luasareaproduktif' => 0, 'luasareanonproduktif' => 0, 'pokok' => 0];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kodeorg'] = $row['kodeorg'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['bloklama'] = $row['bloklama'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['statusblok'] = $row['statusblok'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['cadangan'] = $row['cadangan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['okupasi'] = $row['okupasi'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['rendahan'] = $row['rendahan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['sungai'] = $row['sungai'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['rumah'] = $row['rumah'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kantor'] = $row['kantor'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['pabrik'] = $row['pabrik'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['jalan'] = $row['jalan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kolam'] = $row['kolam'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['umum'] = $row['umum'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['luasareaproduktif'] = $row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['luasareanonproduktif'] = $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['topografi'] = $row['topografi'];
            }

            if ($currPeriod === substr($row['tanggalpengakuan'], 0, 7)) {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luas'] += $row['luasareaproduktif'] + $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luasareaproduktif'] += $row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luasareanonproduktif'] += $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['pokok'] += $row['jumlahpokok'];
            } else {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luas'] += $row['luasareaproduktif'] + $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luasareaproduktif'] += $row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luasareanonproduktif'] += $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['pokok'] += $row['jumlahpokok'];
            }
        }
        $data = [];
        $i = 1;
        foreach ($resBlok as $tt => $rowH) {
            foreach ($rowH as $org => $row) {
                if (0 === $row['awal']['luas'] + $row['mutasi']['luas']) {
                    $rapat = 0;
                } else {
                    $rapat = $row['awal']['pokok'] / $row['awal']['luasareaproduktif'];
                }

                $data[$tt][$org] = ['kodeorg' => $row['kodeorg'], 'bloklama' => $row['bloklama'], 'statusblok' => $row['statusblok'], 'topografi' => $row['topografi'], 'awal' => $row['awal']['luas'], 'luasareaproduktif' => $row['mutasi']['luasareaproduktif'] + $row['awal']['luasareaproduktif'], 'luasareanonproduktif' => $row['mutasi']['luasareanonproduktif'] + $row['awal']['luasareanonproduktif'], 'pokok' => $row['mutasi']['pokok'] + $row['awal']['pokok'], 'cadangan' => $row['cadangan'], 'okupasi' => $row['okupasi'], 'rendahan' => $row['rendahan'], 'sungai' => $row['sungai'], 'rumah' => $row['rumah'], 'kantor' => $row['kantor'], 'pabrik' => $row['pabrik'], 'jalan' => $row['jalan'], 'kolam' => $row['kolam'], 'umum' => $row['umum'], 'kerapatan' => $rapat];
                ++$i;
            }
        }
        $dataShow = [];
        $total = [];
        $gTotal = ['awal' => 0, 'luasareaproduktif' => 0, 'luasareanonproduktif' => 0, 'pokok' => 0];
        foreach ($data as $tt => $rowH) {
            foreach ($rowH as $blok => $row) {
                if (isset($total[$tt])) {
                    $total[$tt]['awal'] += $row['awal'];
                    $total[$tt]['luasareaproduktif'] += $row['luasareaproduktif'];
                    $total[$tt]['luasareanonproduktif'] += $row['luasareanonproduktif'];
                    $total[$tt]['pokok'] += $row['pokok'];
                    if ('pdf' !== $mode) {
                        $total[$tt]['cadangan'] += $row['cadangan'];
                        $total[$tt]['okupasi'] += $row['okupasi'];
                        $total[$tt]['rendahan'] += $row['rendahan'];
                        $total[$tt]['sungai'] += $row['sungai'];
                        $total[$tt]['rumah'] += $row['rumah'];
                        $total[$tt]['kantor'] += $row['kantor'];
                        $total[$tt]['pabrik'] += $row['pabrik'];
                        $total[$tt]['jalan'] += $row['jalan'];
                        $total[$tt]['kolam'] += $row['kolam'];
                        $total[$tt]['umum'] += $row['umum'];
                    }
                } else {
                    $total[$tt]['awal'] = $row['awal'];
                    $total[$tt]['luasareaproduktif'] = $row['luasareaproduktif'];
                    $total[$tt]['luasareanonproduktif'] = $row['luasareanonproduktif'];
                    $total[$tt]['pokok'] = $row['pokok'];
                    if ('pdf' !== $mode) {
                        $total[$tt]['cadangan'] += $row['cadangan'];
                        $total[$tt]['okupasi'] += $row['okupasi'];
                        $total[$tt]['rendahan'] += $row['rendahan'];
                        $total[$tt]['sungai'] += $row['sungai'];
                        $total[$tt]['rumah'] += $row['rumah'];
                        $total[$tt]['kantor'] += $row['kantor'];
                        $total[$tt]['pabrik'] += $row['pabrik'];
                        $total[$tt]['jalan'] += $row['jalan'];
                        $total[$tt]['kolam'] += $row['kolam'];
                        $total[$tt]['umum'] += $row['umum'];
                    }
                }

                $gTotal['awal'] += $row['awal'];
                $gTotal['luasareaproduktif'] += $row['luasareaproduktif'];
                $gTotal['luasareanonproduktif'] += $row['luasareanonproduktif'];
                $gTotal['pokok'] += $row['pokok'];
                if ('pdf' !== $mode) {
                    $gTotal['cadangan'] += $row['cadangan'];
                    $gTotal['okupasi'] += $row['okupasi'];
                    $gTotal['rendahan'] += $row['rendahan'];
                    $gTotal['sungai'] += $row['sungai'];
                    $gTotal['rumah'] += $row['rumah'];
                    $gTotal['kantor'] += $row['kantor'];
                    $gTotal['pabrik'] += $row['pabrik'];
                    $gTotal['jalan'] += $row['jalan'];
                    $gTotal['kolam'] += $row['kolam'];
                    $gTotal['umum'] += $row['umum'];
                }

                $dataShow[$tt][$blok]['kodeorg'] = $row['kodeorg'];
                $dataShow[$tt][$blok]['bloklama'] = $row['bloklama'];
                $dataShow[$tt][$blok]['statusblok'] = $row['statusblok'];
                $dataShow[$tt][$blok]['topografi'] = $row['topografi'];
                $dataShow[$tt][$blok]['awal'] = number_format($row['awal'], 2);
                $dataShow[$tt][$blok]['luasareaproduktif'] = number_format($row['luasareaproduktif'], 2);
                $dataShow[$tt][$blok]['luasareanonproduktif'] = number_format($row['luasareanonproduktif'], 2);
                $dataShow[$tt][$blok]['pokok'] = number_format($row['pokok'], 2);
                if ('pdf' !== $mode) {
                    $dataShow[$tt][$blok]['cadangan'] = number_format($row['cadangan'], 2);
                    $dataShow[$tt][$blok]['okupasi'] = number_format($row['okupasi'], 2);
                    $dataShow[$tt][$blok]['rendahan'] = number_format($row['rendahan'], 2);
                    $dataShow[$tt][$blok]['sungai'] = number_format($row['sungai'], 2);
                    $dataShow[$tt][$blok]['rumah'] = number_format($row['rumah'], 2);
                    $dataShow[$tt][$blok]['kantor'] = number_format($row['kantor'], 2);
                    $dataShow[$tt][$blok]['pabrik'] = number_format($row['pabrik'], 2);
                    $dataShow[$tt][$blok]['jalan'] = number_format($row['jalan'], 2);
                    $dataShow[$tt][$blok]['kolam'] = number_format($row['kolam'], 2);
                    $dataShow[$tt][$blok]['umum'] = number_format($row['umum'], 2);
                }

                if (0 === $row['awal']) {
                    $dataShow[$tt][$blok]['kerapatan'] = 0;
                } else {
                    $dataShow[$tt][$blok]['kerapatan'] = @number_format($row['pokok'] / $row['luasareaproduktif'], 0);
                }
            }
        }
        foreach ($data as $tt => $rowH) {
            if (0 === $total[$tt]['awal']) {
                $total[$tt]['kerapatan'] = 0;
            } else {
                $total[$tt]['kerapatan'] = $total[$tt]['pokok'] / $total[$tt]['luasareaproduktif'];
            }
        }
        if (0 === $gTotal['awal']) {
            $gTotal['kerapatan'] = 0;
        } else {
            $gTotal['kerapatan'] = $gTotal['pokok'] / $gTotal['luasareaproduktif'];
        }

        $theCols = [$_SESSION['lang']['thntnm'], $_SESSION['lang']['blok'], $_SESSION['lang']['kodeorg'], $_SESSION['lang']['bloklama'], $_SESSION['lang']['statusblok'], $_SESSION['lang']['topografi'], $_SESSION['lang']['luaskerangka'], $_SESSION['lang']['luasareaproduktif'], $_SESSION['lang']['luasareanonproduktif'], $_SESSION['lang']['jumlahpokok'], $_SESSION['lang']['cadangan'], $_SESSION['lang']['okupasi'], $_SESSION['lang']['rendahan'], $_SESSION['lang']['sungai'], $_SESSION['lang']['rumah'], $_SESSION['lang']['kantor'], $_SESSION['lang']['pabrik'], $_SESSION['lang']['jalan'], $_SESSION['lang']['kolam'], $_SESSION['lang']['umum'], $_SESSION['lang']['kerapatan']];

        break;
    default:
        break;
}
switch ($mode) {
    case 'pdf':
        $colPdf = ['thntnm', 'blok', 'kodeorg', 'statusblok', 'topografi', 'luasawal', 'luasareaproduktif', 'luasareanonproduktif', 'jumlahpokok', 'kerapatan'];
        $title = $_SESSION['lang']['arealstatement'];
        $align = explode(',', 'R,L,L,L,L,R,R,R,R,R');
        $length = explode(',', '5,15,10,10,5,5,5,5,5,5,5');
        $pdf = new zPdfMaster('L', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell($length[0] / 100 * $width, $height, 'Tahun Tanam', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Blok', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Kode Organisasi', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Blok Lama', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Status Blok', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[5] / 100 * $width, $height, 'Topografi', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[6] / 100 * $width, $height, 'Luas Kerangka', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[7] / 100 * $width, $height, 'Luas Planted', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[8] / 100 * $width, $height, 'Luas Unplanted', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[9] / 100 * $width, $height, 'Jumlah Pokok', 'TLR', 0, $align[1], 1);
        $pdf->Cell($length[10] / 100 * $width, $height, 'Kerapatan', 'TLR', 1, $align[1], 1);

        foreach ($dataShow as $afd => $rowH) {
            $i = 0;
            $afdC = false;
            if (false === $afdC) {
                $pdf->Cell($length[$i] / 100 * $width, $height, $afd, 'TLR', 0, $align[$i], 1);
            }

            ++$i;
            foreach ($rowH as $blok => $row) {
                if (true === $afdC) {
                    $i = 0;
                    $pdf->Cell($length[$i] / 100 * $width, $height, '', 'LR', $align[$i], 1);
                    ++$i;
                } else {
                    $afdC = true;
                }

                $pdf->Cell($length[$i] / 100 * $width, $height, $optBelow[$blok], 1, 0, $align[$i], 1);
                ++$i;
                foreach ($row as $cont) {
                    if ('EN' === $_SESSION['language']) {
                        if ('TM' === $cont) {
                            $cont = 'Mature';
                        }

                        if ('TBM' === $cont) {
                            $cont = 'Imature';
                        }

                        if ('TB' === $cont) {
                            $cont = 'NewPlanting';
                        }

                        if ('CADANGAN' === $cont) {
                            $cont = 'Reserved';
                        }

                        if ('BBT' === $cont) {
                            $cont = 'Nursery';
                        }
                    }

                    $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                    ++$i;
                }
                $pdf->Ln();
            }
            $lenJudul = $length[0] + $length[1] + $length[2] + $length[3]+ $length[4] + $length[5];
            $pdf->Cell($lenJudul / 100 * $width, $height, 'Sub Total Tahun Tanam '.$afd, 1, 0, 'L', 1);
            $i = 4;
            foreach ($total[$afd] as $head => $val) {
                if ('kerapatan' === $head) {
                    $pdf->Cell($length[$i] / 100 * $width, $height, number_format($val, 0), 1, 0, $align[$i], 1);
                } else {
                    $pdf->Cell($length[$i] / 100 * $width, $height, number_format($val, 2), 1, 0, $align[$i], 1);
                }

                ++$i;
            }
            $pdf->Ln();
        }
        $lenJudul = $length[0] + $length[1] + $length[2] + $length[3]+ $length[4] + $length[5];;
        $pdf->Cell($lenJudul / 100 * $width, $height, 'Grand Total', 1, 0, 'L', 1);
        $i = 4;
        foreach ($gTotal as $head => $val) {
            if ('kerapatan' === $head) {
                $pdf->Cell($length[$i] / 100 * $width, $height, number_format($val, 0), 1, 0, $align[$i], 1);
            } else {
                $pdf->Cell($length[$i] / 100 * $width, $height, number_format($val, 0), 1, 0, $align[$i], 1);
            }

            ++$i;
        }
        $pdf->Ln();
        $pdf->Output();

        break;
    default:
        if ('excel' === $mode) {
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='arealstatement' class='sortable' cellspacing=1 boreder=0>";
            $tab .= "<thead><tr class='rowheader'>";
        }

        foreach ($theCols as $head) {
            $tab .= '<td>'.$head.'</td>';
        }
        $tab .= '</tr></thead>';
        $tab .= '<tbody>';
        foreach ($data as $afd => $row) {
            $tmpRow = count($row) - 1;
            $i = 0;
            $afdC = false;
            $blankC = false;
            foreach ($row as $blok => $row2) {
                $tab .= "<tr class='rowcontent'>";
                if (false === $afdC) {
                    $tab .= "<td id='afd_".$i."' value='".$afd."'>".$afd.'</td>';
                    $afdC = true;
                } else {
                    if (false === $blankC) {
                        $tab .= "<td id='afd_".$i."' rowspan='".$tmpRow."'></td>";
                        $blankC = true;
                    }
                }

                $tab .= "<td id='blok_".$i."' value='".$blok."'>".$optBelow[$blok].'</td>';
                foreach ($row2 as $field => $cont) {
                    if ('EN' === $_SESSION['language']) {
                        if ('TM' === $dataShow[$afd][$blok][$field]) {
                            $dataShow[$afd][$blok][$field] = 'Mature';
                        }

                        if ('TBM' === $dataShow[$afd][$blok][$field]) {
                            $dataShow[$afd][$blok][$field] = 'Imature';
                        }

                        if ('TB' === $dataShow[$afd][$blok][$field]) {
                            $dataShow[$afd][$blok][$field] = 'NewPlanting';
                        }

                        if ('CADANGAN' === $dataShow[$afd][$blok][$field]) {
                            $dataShow[$afd][$blok][$field] = 'Reserved';
                        }

                        if ('BBT' === $dataShow[$afd][$blok][$field]) {
                            $dataShow[$afd][$blok][$field] = 'Nursery';
                        }
                    }

                    if ('topografi' === $field) {
                        $tab .= '<td><b>'.$arrTopografo[$dataShow[$afd][$blok][$field]].'</b></td>';
                    } else {
                        $tab .= "<td id='".$field.'_'.$i."' value='".$cont."' align='right'>".$dataShow[$afd][$blok][$field].'</td>';
                    }
                }
                ++$i;
                $tab .= '</tr>';
            }
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='6' align='right'><b>Sub Total Tahun Tanam ".$afd.'</b></td>';
            foreach ($total[$afd] as $head => $val) {
                if ('kerapatan' === $head) {
                    $tab .= "<td align='right'><b>".number_format($val, 0).'</b></td>';
                } else {
                    $tab .= "<td align='right'><b>".number_format($val, 2).'</b></td>';
                }
            }
            $tab .= '</tr>';
        }
        $tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='6' align='right'><b>Grand Total</b></td>";
        foreach ($gTotal as $head => $val) {
            if ('kerapatan' === $head) {
                $tab .= "<td align='right'><b>".number_format($val, 0).'</b></td>';
            } else {
                $tab .= "<td align='right'><b>".number_format($val, 2).'</b></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= '</tbody>';
        $tab .= '</table>';
        if ('excel' === $mode) {
            $stream = $tab;
            $nop_ = 'ArealStatement';
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
                    echo 'Error : Tidak bisa menulis ke format excel';
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