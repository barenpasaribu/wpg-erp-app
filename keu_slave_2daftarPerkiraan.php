<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$nmbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$satuanbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
if ('excel' == $proses) {
    $stream = '<table class=sortable cellspacing=1 border=1>';
} else {
    $stream = '<table class=sortable cellspacing=1>';
}

$stream .= "<thead class=rowheader>\r\n                 <tr  bgcolor=#CCCCCC class=rowheader>\r\n\t\t\t\t \t<td align=center>".$_SESSION['lang']['nomorperkiraan']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['namaperkiraan']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['tipe']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['level']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['matauang']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['tampilkan']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['detail']."</td>\r\n  \t\t\t\t</tr></thead>";
$sql = 'select * from '.$dbname.'.keu_5akun';
$qry = mysql_query($sql);
while ($bar = mysql_fetch_assoc($qry)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n\t\t<td>".$bar['noakun'].'</td>';
    if ('EN' == $_SESSION['language']) {
        $stream .= '<td>'.$bar['namaakun1'].'</td>';
    } else {
        $stream .= '<td>'.$bar['namaakun'].'</td>';
    }

    $stream .= "\r\n\t\t<td>".$bar['tipeakun']."</td>\r\n\t\t<td>".$bar['level']."</td>\r\n\t\t<td>".$bar['matauang']."</td>\r\n\t\t<td>".$bar['pemilik'].'</td>';
    if (1 == $bar['detail']) {
        $stream .= '<td>Y</td>';
    } else {
        $stream .= '<td></td>';
    }

    $stream .= '</tr>';
}
$stream .= '<tbody></table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_Riwayat_Potongan_Angsuran_Karyawan'.$tglSkrg;
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
                echo "<script language=javascript1.2>\r\n\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t</script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        $table = 'keu_5akun';
        $query = selectQuery($dbname, $table);
        $result = fetchData($query);
        $header = [];
        foreach ($result[0] as $key => $row) {
            $header[] = $key;
        }

class masterpdf extends FPDF
{
    public function Header()
    {
        global $table;
        global $header;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, $height, $_SESSION['org']['namaorganisasi'], '', 1, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['daftarperkiraan']), '', 1, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(420, $height, ' ', '', 0, 'R');
        $this->Cell(38, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(40, $height, date('d-m-Y H:i'), '', 1, 'L');
        $this->Cell(420, $height, ' ', '', 0, 'R');
        $this->Cell(38, $height, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(8, $height, ':', '', 0, 'L');
        $this->Cell(15, $height, $this->PageNo(), '', 1, 'L');
        $this->Cell(420, $height, ' ', '', 0, 'R');
        $this->Cell(38, $height, 'User', '', 0, 'L');
        $this->Cell(8, $height, ':', '', 0, 'L');
        $this->Cell(20, $height, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->Cell(60, 1.5 * $height, $_SESSION['lang']['nomorperkiraan'], 'TBLR', 0, 'C');
        $this->Cell(260, 1.5 * $height, $_SESSION['lang']['namaperkiraan'], 'TBLR', 0, 'C');
        $this->Cell(38, 1.5 * $height, $_SESSION['lang']['tipe'], 'TBLR', 0, 'C');
        $this->Cell(45, 1.5 * $height, $_SESSION['lang']['level'], 'TBLR', 0, 'C');
        $this->Cell(47, 1.5 * $height, $_SESSION['lang']['matauang'], 'TBLR', 0, 'C');
        $this->Cell(40, 1.5 * $height, $_SESSION['lang']['tampilkan'], 'TBLR', 0, 'C');
        $this->Cell(40, 1.5 * $height, $_SESSION['lang']['detail'], 'TBLR', 0, 'C');
        $this->Ln();
        $this->Ln();
    }
}

        $pdf = new masterpdf('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->SetFont('Arial', '', 8);
        $pdf->AddPage();
        foreach ($result as $data) {
            $pdf->Cell(60, $height, $data['noakun'], '', 0, 'L');
            if ('EN' == $_SESSION['language']) {
                $pdf->Cell(260, $height, $data['namaakun1'], '', 0, 'L');
            } else {
                $pdf->Cell(260, $height, $data['namaakun'], '', 0, 'L');
            }

            $pdf->Cell(40, $height, $data['tipeakun'], '', 0, 'C');
            $pdf->Cell(40, $height, $data['level'], '', 0, 'C');
            $pdf->Cell(60, $height, $data['matauang'], '', 0, 'C');
            $pdf->Cell(40, $height, $data['pemilik'], '', 0, 'C');
            if (1 == $data['detail']) {
                $pdf->Cell(40, $height, 'Y', '', 0, 'C');
            }

            $pdf->Ln();
        }
        $pdf->Output();

        break;
}
echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";

?>