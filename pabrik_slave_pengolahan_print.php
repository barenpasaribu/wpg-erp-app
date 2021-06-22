<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$param = $_POST;
$cols = 'nopengolahan,kodeorg,tanggal,shift,jammulai,jamselesai,mandor,'.'asisten,jamdinasbruto,jamstagnasi,jumlahlori,tbsdiolah';
$colArr = explode(',', $cols);
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$query = selectQuery($dbname, 'pabrik_pengolahan', $cols, $where);
$data = fetchData($query);
$title = $_SESSION['lang']['operasipabrik'];
$align = explode(',', 'L,L,L,R,L,L,L,L,L,L,L,L');
$length = explode(',', '10,10,7,7,7,7,10,10,7,7,9,9');
$whereKar = 'karyawanid in (';
$isFirst = true;
foreach ($data as $key => $row) {
    if (0 === $key) {
        if ('' === $row['mandor']) {
            if ($row['asisten' !== '']) {
                $whereKar .= $row['asisten'];
                $isFirst = false;
            }
        } else {
            $whereKar .= $row['mandor'];
            $isFirst = false;
            if ('' !== $row['asisten']) {
                $whereKar .= ','.$row['asisten'];
            }
        }
    } else {
        if ('' === $row['mandor']) {
            if ('' !== $row['asisten']) {
                if (true === $isFirst) {
                    $whereKar .= $row['asisten'];
                    $isFirst = false;
                } else {
                    $whereKar .= ','.$row['asisten'];
                }
            }
        } else {
            if (true === $isFirst) {
                $whereKar .= $row['mandor'];
                $isFirst = false;
                if ($row['asisten' !== '']) {
                    $whereKar .= ','.$row['asisten'];
                }
            } else {
                $whereKar .= ','.$row['mandor'];
                if ('' !== $row['asisten']) {
                    $whereKar .= ','.$row['asisten'];
                }
            }
        }
    }
}
$whereKar .= ')';
$optKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKar, '0', true);
$whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '0', true);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
    $dataShow[$key]['mandor'] = $optKar[$row['mandor']];
    $dataShow[$key]['asisten'] = $optKar[$row['asisten']];
    $dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
}
switch ($proses) {
    case 'pdf':

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        $optBulan = $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        if ('SSP' === $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' === $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' === $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' === $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $_SESSION['empl']['lokasitugas'], '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, numToMonth($_SESSION['org']['period']['bulan'], 'I', 'long').' '.$_SESSION['org']['period']['tahun'], 0, 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $_SESSION['standard']['username'], '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $title, 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        foreach ($colArr as $key => $head) {
            $this->Cell($length[$key] / 100 * $width, $height, $_SESSION['lang'][$head], 1, 0, 'C', 1);
        }
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('L', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        foreach ($dataShow as $key => $row) {
            $i = 0;
            foreach ($row as $cont) {
                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}

?>