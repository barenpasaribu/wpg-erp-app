<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/pdf_keu1.php';
$proses = $_GET['proses'];
if (empty($_POST)) {
    $param = $_GET;
    unset($param['proses']);
} else {
    $param = $_POST;
}

$arrOrg = [new Org($param['kodeorg'], null)];
$tmpOrg = [$param['kodeorg']];
while (!empty($tmpOrg)) {
    foreach ($tmpOrg as $key => $tOrg) {
        unset($tmpOrg[$key]);
        $cols = 'kodeorganisasi,namaorganisasi,tipe';
        $query = selectQuery($dbname, 'organisasi', $cols, "induk='".$tOrg."'");
        $data = fetchData($query);
        foreach ($data as $row) {
            $contOrg[$row['tipe']][$row['kodeorganisasi']] = $row['namaorganisasi'];
            $tmpOrg[] = $row['kodeorganisasi'];
            $arrOrg[] = new Org($row['kodeorganisasi'], $tOrg);
        }
    }
}
$maskKebun = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$param['kodeorg']."'");
$namaKebun = $maskKebun[$param['kodeorg']];
$listOrg = '';
$i = 0;
foreach ($arrOrg as $key => $row) {
    if (0 === $i) {
        $listOrg .= "kodeorg='".$row->_name."'";
    } else {
        $listOrg .= " or kodeorg='".$row->_name."'";
    }

    ++$i;
}
$where = 'tahun='.$param['tahun'].' and revisi='.$param['revisi'].' and ('.$listOrg.')';
$cols1 = 'kodeanggaran,tipeanggaran,tahun,kodeorg';
$query1 = selectQuery($dbname, 'keu_anggaran', $cols1, $where);
$resHead = fetchData($query1);
$tipeBud = [];
foreach ($resHead as $row) {
    $tipeBud[$row['kodeorg']] = $row['tipeanggaran'];
}
$query2 = selectQuery($dbname, 'keu_anggarandt', '*', $where);
$resDetail = fetchData($query2);
$whereBarang = '';
foreach ($resDetail as $key => $row) {
    if (0 === $key) {
        $whereBarang .= "kodebarang='".$row['kodebarang']."'";
    } else {
        $whereBarang .= " or kodebarang='".$row['kodebarang']."'";
    }
}
if ('' !== $whereBarang) {
    $queryBar = selectQuery($dbname, 'log_5masterbarang', 'kodebarang,namabarang,kelompokbarang,satuan', $whereBarang);
    $tmpBar = fetchData($queryBar);
} else {
    $tmpBar = [];
}

$whereKlp = '';
foreach ($tmpBar as $key => $row) {
    if (0 === $key) {
        $whereKlp .= "kode='".$row['kelompokbarang']."'";
    } else {
        $whereKlp .= " or kode='".$row['kelompokbarang']."'";
    }
}
if ('' !== $whereKlp) {
    $klBarang = makeOption($dbname, 'log_5klbarang', 'kode,kelompok', $whereKlp);
} else {
    $klBarang = [];
}

$maskBarang = [];
$maskSatuan = [];
foreach ($tmpBar as $row) {
    $maskBarang[$row['kodebarang']] = $klBarang[$row['kelompokbarang']].', '.$row['namabarang'];
    $maskSatuan[$row['kodebarang']] = $row['satuan'];
}
$data = [];
$tmpDetail = [];
foreach ($resDetail as $row) {
    $jumlahsetahun = ($row['jan'] + $row['peb'] + $row['mar'] + $row['apr'] + $row['mei'] + $row['jun'] + $row['jul'] + $row['agt'] + $row['sep'] + $row['okt'] + $row['nov'] + $row['dec']) * $row['hargasatuan'];
    $tmpDetail[$row['kodeorg']][$row['kodekegiatan']] = ['kodeanggaran' => $row['kodeanggaran'], 'tipeanggaran' => $tipeBud[$row['kodeorg']], 'namabarang' => $maskBarang[$row['kodebarang']], 'jumlah' => $row['jumlah'], 'uom' => $maskSatuan[$row['kodebarang']], 'hargasatuan' => $row['hargasatuan'], 'jumlahsetahun' => $jumlahsetahun, 'jan' => $row['jan'] * $row['hargasatuan'], 'peb' => $row['peb'] * $row['hargasatuan'], 'mar' => $row['mar'] * $row['hargasatuan'], 'apr' => $row['apr'] * $row['hargasatuan'], 'mei' => $row['mei'] * $row['hargasatuan'], 'jun' => $row['jun'] * $row['hargasatuan'], 'jul' => $row['jul'] * $row['hargasatuan'], 'agt' => $row['agt'] * $row['hargasatuan'], 'sep' => $row['sep'] * $row['hargasatuan'], 'okt' => $row['okt'] * $row['hargasatuan'], 'nov' => $row['nov'] * $row['hargasatuan'], 'dec' => $row['dec'] * $row['hargasatuan'], 'biayaha' => $row['biayaha'], 'biayalain' => $row['biayalain']];
}
$detDone = false;
while ('false' === $detDone) {
    $detDone = true;
    foreach ($resDetail as $key => $row) {
        $tmp = search($row['kodeorg'], $arrOrg);
        if (null !== $tmp->getParent() || $tmp->getParent() !== $param['kodeorg']) {
            $resDetail[$key]['kodeorg'] = $tmp->getParent();
            $detDone = false;
        }
    }
}
foreach ($tmpDetail as $key => $row) {
    if ($key === $param['kodeorg']) {
        $data[$param['kodeorg']]['-'] = $row;
    } else {
        $data[$param['kodeorg']][$key] = $row;
    }
}
$jumlah = [];
$cols = [];
$first = true;
foreach ($data as $kebun => $row0) {
    foreach ($row0 as $div => $row1) {
        foreach ($row1 as $kKeg => $row2) {
            foreach ($row2 as $key => $row) {
                $jumlah[$kebun][$div][$kKeg][$key] += $row;
                $jumlah[$kebun][$div]['total'][$key] += $row;
                $jumlah[$kebun]['total'][$key] += $row;
                if (true === $first) {
                    $cols[] = $key;
                }
            }
            $first = false;
        }
    }
}
switch ($proses) {
    case 'preview':
        $table = "<table class='sortable' cellpadding='2' width='1500px'>";
        $table .= "<thead><tr class='rowheader'>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['kodeanggaran'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['tipeanggaran'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['namabarang'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['jumlah'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['satuan'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['hargasatuan'].'</td>';
        $table .= "<td rowspan='2'>".$_SESSION['lang']['jumlahsetahun'].'</td>';
        $table .= "<td colspan='12' align='center'>".$_SESSION['lang']['rincianbulanan'].'</td>';
        $table .= "<td colspan='2' align='center'>".$_SESSION['lang']['totalbiaya'].'</td></tr>';
        $table .= '<tr><td>'.$_SESSION['lang']['jan'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['peb'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['mar'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['apr'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['mei'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['jun'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['jul'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['agt'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['sep'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['okt'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['nov'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['dec'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['biayaha'].'</td>';
        $table .= '<td>'.$_SESSION['lang']['biayalain'].'</td>';
        $table .= '</tr></thead>';
        $table .= '<tbody>';
        foreach ($data as $kebun => $row0) {
            foreach ($row0 as $div => $row1) {
                foreach ($row1 as $kKeg => $row3) {
                    $table .= "<tr class='rowcontent'><td colspan='21'>";
                    $table .= "<div style='float:left;width:170px;'><b>Divisi</b> : ".$div.'</div>';
                    $table .= "<div style='float:left;width:170px;'><b>Kegiatan</b> : ".$kKeg.'</div></td></tr>';
                    $table .= "<tr class='rowcontent'>";
                    foreach ($row3 as $key => $row) {
                        $table .= '<td>'.$row.'</td>';
                    }
                    $table .= '</tr>';
                    $table .= "<tr class='rowcontent'>";
                    $table .= "<td colspan='3'>Subtotal</td>";
                    foreach ($row3 as $key => $row) {
                        if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                            if ('uom' === $key) {
                                $table .= '<td></td>';
                            } else {
                                $table .= '<td>'.$jumlah[$kebun][$div][$kKeg][$key].'</td>';
                            }
                        }
                    }
                    $table .= '</tr>';
                }
                $table .= "<tr class='rowcontent'>";
                $table .= "<td colspan='3'>Subtotal ".$div.'</td>';
                foreach ($cols as $key) {
                    if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                        if ('uom' === $key) {
                            $table .= '<td></td>';
                        } else {
                            $table .= '<td>'.$jumlah[$kebun][$div]['total'][$key].'</td>';
                        }
                    }
                }
                $table .= '</tr>';
            }
            $table .= "<tr class='rowcontent'>";
            $table .= "<td colspan='3'>Grand Total</td>";
            foreach ($cols as $key) {
                if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                    if ('uom' === $key) {
                        $table .= '<td></td>';
                    } else {
                        $table .= '<td>'.$jumlah[$kebun]['total'][$key].'</td>';
                    }
                }
            }
            $table .= '</tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        echo $table;

        break;
    case 'pdf':

class PDF extends FPDFBudget extends pdf_keu1
{
    public function Header()
    {
        parent::Header();
        $this->SetFont('Arial', 'B', 8);
        $width = $this->_width;
        $height = $this->_height;
        $this->MultiCell(5 / 100 * $width, $height, $_SESSION['lang']['kodeanggaran'], 'TBLR', 'C');
        $this->x = $this->x + 5 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(5 / 100 * $width, $height, $_SESSION['lang']['tipeanggaran'], 'TBLR', 'C');
        $this->x = $this->x + 10 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(20 / 100 * $width, $height * 2, $_SESSION['lang']['namabarang'], 'TBLR', 'C');
        $this->x = $this->x + 30 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(5 / 100 * $width, $height * 2, $_SESSION['lang']['jumlah'], 'TBLR', 'C');
        $this->x = $this->x + 35 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(3 / 100 * $width, $height, $_SESSION['lang']['satuan'], 'TBLR', 'C');
        $this->x = $this->x + 38 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(5 / 100 * $width, $height, $_SESSION['lang']['hargasatuan'], 'TBLR', 'C');
        $this->x = $this->x + 43 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->MultiCell(6 / 100 * $width, $height, $_SESSION['lang']['jumlahsetahun'], 'TBLR', 'C');
        $this->x = $this->x + 49 / 100 * $width;
        $this->y = $this->y - $height * 2;
        $this->Cell(42 / 100 * $width, $height, $_SESSION['lang']['rincianbulanan'], 'TBR', 0, 'C');
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['totalbiaya'], 'TBR', 1, 'C');
        $this->x = $this->x + 49 / 100 * $width;
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['jan'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['peb'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['mar'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['apr'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['mei'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['jun'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['jul'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['agt'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['sep'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['okt'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['nov'], 'TBR', 0, 'C');
        $this->Cell(3.5 / 100 * $width, $height, $_SESSION['lang']['dec'], 'TBR', 0, 'C');
        $this->Cell(4.5 / 100 * $width, $height, $_SESSION['lang']['biayaha'], 'TBR', 0, 'C');
        $this->Cell(4.5 / 100 * $width, $height, $_SESSION['lang']['biayalain'], 'TBR', 1, 'C');
    }
}

        $pdf = new pdfBudget('L', 'pt', 'A4');
        $pdf->addAddsHeader('Kebun', $namaKebun);
        $pdf->addAddsHeader('Periode', '1 Januari '.$param['tahun'].' - 31 Desember '.$param['tahun']);
        $pdf->addAddsHeader('Revisi', $param['revisi']);
        $pdf->SetTitle($_SESSION['lang']['budget']);
        $table = '';
        $widthArr = [5, 5, 20, 5, 3, 5, 6, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 3.5, 4.5, 4.5];
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 8);
        foreach ($data as $kebun => $row0) {
            foreach ($row0 as $div => $row1) {
                foreach ($row1 as $kKeg => $row3) {
                    $pdf->Cell(10 / 100 * $width, $height, 'Divisi : '.$div, 'TBL', 0, 'L');
                    $pdf->Cell(90 / 100 * $width, $height, 'Kegiatan : '.$kKeg, 'TBR', 1, 'L');
                    $i = 0;
                    foreach ($row3 as $key => $row) {
                        if ('namabarang' === $key) {
                            $row = substr($row, 0, 30);
                        }

                        if (3 === $i || 4 < $i) {
                            $pdf->Cell($widthArr[$i] / 100 * $width, $height, number_format($row, 0), 'TBLR', 0, 'R');
                        } else {
                            $pdf->Cell($widthArr[$i] / 100 * $width, $height, $row, 'TBLR', 0, 'L');
                        }

                        ++$i;
                    }
                    $pdf->Ln();
                    $pdf->Cell(30 / 100 * $width, $height, 'Subtotal '.$kKeg.' : ', 'TBLR', 0, 'L');
                    $i = 0;
                    foreach ($row3 as $key => $row) {
                        if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                            if ('uom' === $key) {
                                $pdf->Cell($widthArr[$i] / 100 * $width, $height, '', 'TBLR', 0, $tmpAlign);
                            } else {
                                $pdf->Cell($widthArr[$i] / 100 * $width, $height, number_format($jumlah[$kebun][$div][$kKeg][$key], 0), 'TBLR', 0, 'R');
                            }
                        }

                        ++$i;
                    }
                    $pdf->Ln();
                }
                $pdf->Cell(30 / 100 * $width, $height, 'Subtotal '.$div.' : ', 'TBLR', 0, 'L');
                $i = 0;
                foreach ($row3 as $key => $row) {
                    if (3 === $i || 4 < $i) {
                        $tmpAlign = 'R';
                    } else {
                        $tmpAlign = 'L';
                    }

                    if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                        if ('uom' === $key) {
                            $pdf->Cell($widthArr[$i] / 100 * $width, $height, '', 'TBLR', 0, $tmpAlign);
                        } else {
                            $pdf->Cell($widthArr[$i] / 100 * $width, $height, number_format($jumlah[$kebun][$div]['total'][$key], 0), 'TBLR', 0, 'R');
                        }
                    }

                    ++$i;
                }
                $pdf->Ln();
            }
            $pdf->Cell(30 / 100 * $width, $height, 'Grand Total : ', 'TBLR', 0, 'L');
            $i = 0;
            foreach ($row3 as $key => $row) {
                if (3 === $i || 4 < $i) {
                    $tmpAlign = 'R';
                } else {
                    $tmpAlign = 'L';
                }

                if ('kodeanggaran' !== $key && 'tipeanggaran' !== $key && 'namabarang' !== $key) {
                    if ('uom' === $key) {
                        $pdf->Cell($widthArr[$i] / 100 * $width, $height, '', 'TBLR', 0, $tmpAlign);
                    } else {
                        $pdf->Cell($widthArr[$i] / 100 * $width, $height, number_format($jumlah[$kebun]['total'][$key], 0), 'TBLR', 0, 'R');
                    }
                }

                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Output();
    default:
        break;
}

class Org
{
    public $_parent;
    public $_name;

    public function Org($orgName, $theParent)
    {
        $this->_name = $orgName;
        $this->_parent = $theParent;
    }

    public function getParent()
    {
        return $this->_parent;
    }
}

function search($oName, $tArr)
{
    $res = false;
    foreach ($tArr as $tOrg) {
        if ($tOrg->_name === $oName) {
            $res = $tOrg;

            break;
        }
    }

    return $res;
}

?>