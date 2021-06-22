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

$kodeorg = $param[kodeorg];
$periode_bulan = $param[periode_bulan];
$periode_tahun = $param[periode_tahun];
$currTahun = $tahun = $param['periode_tahun'];
$currBulan = $bulan = $param['periode_bulan'];
$currPeriod = $currTahun.'-'.addZero($currBulan, 2);
switch ($level) {
    case '0':
        $cols = 'a.tanggal,sum(jamstagnasi),sum(jamdinasbruto),sum(jumlahlori),sum(a.tbsdiolah),oer,oerpk,nopengolahan';
        $cols2 = 'nomor,tanggal,jamstagnasi,jamoperasional,jumlahlori,tbsdiolah,cpo,oerpk,detail';
        $cols2e = 'nomor,tanggal,jamstagnasi,jamoperasional,jumlahlori,tbsdiolah,cpo,oerpk';
        $where = "a.kodeorg='".$param['kodeorg']."' and left(a.tanggal,7)='".$currPeriod."'";
        $query = 'select distinct '.$cols.' from '.$dbname.'.pabrik_pengolahan a left join '.$dbname.".pabrik_produksi b \r\n                 on (a.kodeorg=b.kodeorg and a.tanggal=b.tanggal) where ".$where.' group by a.tanggal';
        $tmpRes = fetchData($query);
        if (empty($tmpRes)) {
            echo 'Warning : Data empty';
            exit();
        }

        $data = $tmpRes;
        $dataShow = $dataExcel = $data;
        foreach ($data as $key => $row) {
            $dataShow[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
            $dataShow[$key]['jumlahlori'] = number_format($row['jumlahlori'], 0);
            $dataShow[$key]['tbsdiolah'] = number_format($row['tbsdiolah'], 0);
        }
        $theCols = [];
        if ('excel' !== $mode) {
            $tmpCol = explode(',', $cols2);
        } else {
            $tmpCol = explode(',', $cols2e);
        }

        foreach ($tmpCol as $row) {
            $theCols[] = $_SESSION['lang'][$row];
        }
        $align = explode(',', 'R,R,R,R,R,R,R,R,R');

        break;
    default:
        break;
}
switch ($mode) {
    case 'pdf':
        $colPdf = explode(',', $cols2e);
        $title = $_SESSION['lang']['pabrik'].' '.$kodeorg;
        $length = explode(',', '10,10,10,10,10,10,10,10');
        $pdf = new zPdfMaster('L', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFont('Arial', '', 9);
        foreach ($data as $key => $row) {
            $i = 0;
            ++$j;
            $pdf->Cell($length[$i] / 100 * $width, $height, $j, 1, 0, $align[$i], 1);
            foreach ($row as $head => $cont) {
                if (0 === $i) {
                    $tanggal = $cont;
                    $qwe = date('D', strtotime($tanggal));
                    if ('Sun' === $qwe) {
                        $pdf->SetFillColor(255, 192, 192);
                    }

                    $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                } else {
                    if (1 === $i) {
                        $pdf->SetFillColor(255, 255, 255);
                        $jamstag = $cont;
                        if ('0:00' === $jamstag) {
                            $jamstag = '';
                        }

                        $pdf->Cell($length[$i] / 100 * $width, $height, $jamstag, 1, 0, $align[$i], 1);
                    } else {
                        if (2 === $i) {
                            $pdf->SetFillColor(255, 255, 255);
                            $jambrut = $cont;
                            list($hoursb, $minutesb) = preg_split('/:/D', $jambrut);
                            list($hourss, $minutess) = preg_split('/:/D', $jamstag);
                            $minutes = $minutesb - $minutess;
                            $hours = $hoursb - $hourss;
                            if ($minutes < 0) {
                                $minutes = 60 + $minutes;
                                --$hours;
                            }

                            $minutes = addZero($minutes, 2);
                            $jamop = $hours.':'.$minutes;
                            if ('0:00' === $jambrut && '' === $jamstag) {
                                $jamop = '';
                            }

                            $pdf->Cell($length[$i] / 100 * $width, $height, $jamop, 1, 0, $align[$i], 1);
                        } else {
                            if (5 === $i) {
                                $pdf->SetFillColor(255, 255, 255);
                            } else {
                                $pdf->SetFillColor(255, 255, 255);
                                $jumlah = number_format($cont, 0);
                                if (0 === $jumlah) {
                                    $jumlah = '';
                                }

                                $pdf->Cell($length[$i] / 100 * $width, $height, $jumlah, 1, 0, $align[$i], 1);
                            }
                        }
                    }
                }

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
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='laporanpengolahan' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }

        foreach ($theCols as $head) {
            $tab .= '<td>'.$head.'</td>';
        }
        $tab .= '</tr></thead>';
        $tab .= '<tbody>';
        $j = 0;
        foreach ($data as $key => $row) {
            $tab .= "<tr class='rowcontent'>";
            $i = 0;
            ++$j;
            $tab .= "<td align='right'>".$j.'</td>';
            foreach ($row as $head => $cont) {
                if (0 === $i) {
                    $tab .= "<td align='".$alignPrev[$i]."'>";
                    $tanggal = $dataShow[$key][$head];
                    $qwe = date('D', strtotime($tanggal));
                    if ('Sun' === $qwe) {
                        $tab .= '<font color=red>'.$tanggal.'</font>';
                    } else {
                        $tab .= $tanggal;
                    }

                    $tab .= '</td>';
                } else {
                    if (1 === $i) {
                        $jamstag = $dataShow[$key][$head];
                        if ('0:00' === $jamstag) {
                            $jamstag = '';
                        }

                        $tab .= "<td align='".$alignPrev[$i]."'>".$jamstag.'</td>';
                    } else {
                        if (2 === $i) {
                            $jambrut = $dataShow[$key][$head];
                            if ('0:00' === $jambrut) {
                                $jambrut = '';
                            }

                            $tab .= "<td align='".$alignPrev[$i]."'>".$jambrut.'</td>';
                        } else {
                            if (7 === $i) {
                                if ('excel' !== $mode) {
                                    $tab .= "<td align='".$alignPrev[$i]."'><img onclick=\"viewDetail('".$dataShow[$key][$head]."','".$tanggal."','".$kodeorg."','".$periode_tahun."','".$periode_bulan."',event);\" title='".$_SESSION['lang']['klikdetail']."' class=\"resicon\" src=\"images/icons/clipboard_sign.png\"></td>";
                                }
                            } else {
                                $jumlah = number_format($dataShow[$key][$head], 0);
                                if (0 === $jumlah) {
                                    $jumlah = '';
                                }

                                $tab .= "<td align='".$alignPrev[$i]."'>".$jumlah.'</td>';
                            }
                        }
                    }
                }

                ++$i;
            }
            $tab .= '</tr>';
        }
        $tab .= '</tbody>';
        $tab .= '</table>';
        if ('excel' === $mode) {
            $stream = $tab;
            $nop_ = 'LaporanPengolahan_'.$kodeorg.'_'.$periode_tahun.'-'.$periode_bulan;
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
function fixHours($hours)
{
    if (false !== strpos($hours, '.')) {
        list($hours, $minutes) = explode('.', $hours);
        $minutes = substr($minutes, 0, 2);
        if (1 === strlen($minutes)) {
            $minutes = $minutes * 10;
        }
    }

    if (60 <= $minutes) {
        $minutes = $minutes - 60;
        $hours = $hours + 1;
    }

    return sprintf('%d:%02.0f', $hours, $minutes);
}

?>