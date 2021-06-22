<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';

class PDF extends FPDF_keu1 extends FPDF
{
    public $_addsHeader = [];
    public $_theadTable = false;
    public $_width;
    public $_height;

    public function addAddsHeader($name, $value)
    {
        $this->_addsHeader[] = ['name' => $name, 'value' => $value];
    }

    public function setThead($cols, $width)
    {
        $this->_theadTable = [];
        foreach ($cols as $key => $col) {
            $this->_theadTable[] = ['col' => $col, 'width' => $width[$key]];
        }
    }

    public function Header()
    {
        global $conn;
        global $dbname;
        $this->_width = $width = $this->w - $this->lMargin - $this->rMargin;
        $this->_height = $height = 15;
        $this->SetFont('Arial', '', 8);
        $this->Cell(1, $height, $_SESSION['org']['namaorganisasi'], '', 0, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($width - 1, $height, $this->title, '', 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(1, $height, $this->_addsHeader[0]['name'].' : '.$this->_addsHeader[0]['value'], '', 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($width - 2, $height, $this->_addsHeader[1]['name'].' : '.$this->_addsHeader[1]['value'], '', 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(1, $height, $_SESSION['lang']['user'].' : '.$_SESSION['standard']['username'], '', 1, 'R');
        $this->Cell(1, $height, $this->_addsHeader[2]['name'].' : '.$this->_addsHeader[2]['value'], '', 0, 'L');
        $this->Cell($width - 1, $height, 'Waktu : '.date('d-m-Y H:i:s'), '', 1, 'R');
        if (false !== $this->_theadTable) {
            foreach ($this->_theadTable as $cols) {
                $this->Cell($cols['width'] / 100 * $width, $height, $cols['col'], 'TBLR', 0, 'R');
            }
        }

        $this->Ln();
    }
}

?>