<?php



include_once 'lib/fpdf.php';
include_once 'lib/zLib.php';

class zPdfMaster extends FPDF
{
    public $_align;
    public $_length;
    public $_colArr;
    public $_title;
    public $_subTitle;
    public $_noThead;
    public $_kopOnly;
    public $_kodeOrg;
    public $_orgName;
    public $_orgInfo;
    public $_logoOrg;

    public function zPdfMaster($ori, $unit, $format)
    {
        parent::FPDF($ori, $unit, $format);
        $this->_noThead = false;
        $this->_kopOnly = false;
        $this->_subTitle = null;
        $this->_kodeOrg = null;
        $this->_logoOrg = null;
    }

    public function Header()
    {
        global $conn;
        global $dbname;
        global $bulan;
        global $tahun;
        if (!empty($this->_kodeOrg)) {
            $query = selectQuery($dbname, 'organisasi', 'namaorganisasi,alamat,telepon', "kodeorganisasi='".$this->_kodeOrg."'");
            $orgData = fetchData($query);
            $this->_orgName = $namaOrg = $orgData[0]['namaorganisasi'];
            $this->_logoOrg = $this->_kodeOrg;
        } else {
            $query = selectQuery($dbname, 'organisasi', 'alamat,telepon,logo', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
            $orgData = fetchData($query);
            $this->_orgName = $namaOrg = $_SESSION['org']['namaorganisasi'];
            $this->_logoOrg = $_SESSION['org']['kodeorganisasi'];
        }

        $this->_orgInfo = $orgData[0];
        $sPeriode = 'select distinct periode from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qPeriode = mysql_query($sPeriode);
        $rPeriode = mysql_fetch_assoc($qPeriode);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
       
        
        if (!empty($orgData[0]['logo'])) {
            $this->Image($orgData[0]['logo'], $this->lMargin, $this->tMargin, 50);
        }
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $namaOrg, 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln($height * 2);
        if (true === $this->_kopOnly) {
            $this->SetFont('Arial', '', 8);
            $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
            $this->Cell(5, $height, ':', '', 0, 'L');
            $this->Cell(45 / 100 * $width, $height, $_SESSION['empl']['lokasitugas'], '', 0, 'L');
            $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
            $this->Cell(5, $height, ':', '', 0, 'L');
            $this->Cell(15 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
            $this->Cell(20 / 100 * $width - 5, $height, "Pembuat", '', 0, 'L');
            $this->Cell(5, $height, ':', '', 0, 'L');
            $queryGetNama = "SELECT namakaryawan from datakaryawan where karyawanid = '".$_SESSION['standard']['userid']."'";
            $dataGetNama = fetchData($queryGetNama);
            $this->Cell(45 / 100 * $width, $height, $dataGetNama[0]['namakaryawan'], '', 0, 'L');
            $this->Ln();
            $this->SetFont('Arial', 'U', 13);
            $this->Cell($width, $height, strtoupper($this->_title), 0, 1, 'C');
            if (null !== $this->_subTitle) {
                $this->SetFont('Arial', '', 9);
                $this->Cell($width, $height, strtoupper($this->_subTitle), 0, 1, 'C');
            }

            if (false === $this->_noThead) {
                $this->Ln();
                $this->SetFont('Arial', 'B', 9);
                $this->SetFillColor(220, 220, 220);
                foreach ($this->_colArr as $key => $head) {
                    if (isset($_SESSION['lang'][$head])) {
                        $this->Cell($this->_length[$key] / 100 * $width, $height, $_SESSION['lang'][$head], 1, 0, 'C', 1);
                    } else {
                        $this->Cell($this->_length[$key] / 100 * $width, $height, ucfirst($head), 1, 0, 'C', 1);
                    }
                }
                $this->Ln();
            }
        }

        $this->SetFont('Arial', 'U', 13);
        $this->Cell($width, $height, strtoupper($this->_title), 0, 1, 'C');
        if (null !== $this->_subTitle) {
            $this->SetFont('Arial', '', 9);
            $this->Cell($width, $height, strtoupper($this->_subTitle), 0, 1, 'C');
        }

        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }

    public function setAttr1($cTitle, $cAlign, $cLength, $cColArr)
    {
        $this->_align = $cAlign;
        $this->_length = $cLength;
        $this->_colArr = $cColArr;
        $this->_title = $cTitle;
    }
}

?>