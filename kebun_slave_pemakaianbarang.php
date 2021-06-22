<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kodeorg'];
$tgl1_ = $_POST['tgl1'];
$tgl2_ = $_POST['tgl2'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kdorg = $_GET['kodeorg'];
    $tgl1_ = $_GET['tgl1'];
    $tgl2_ = $_GET['tgl2'];
}

$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$tgl1_ = tanggalsystem($tgl1_);
$tgl1 = substr($tgl1_, 0, 4).'-'.substr($tgl1_, 4, 2).'-'.substr($tgl1_, 6, 2);
$tgl2_ = tanggalsystem($tgl2_);
$tgl2 = substr($tgl2_, 0, 4).'-'.substr($tgl2_, 4, 2).'-'.substr($tgl2_, 6, 2);
if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $tgl1_ || '' === $tgl2_) {
        echo 'Error: Tanggal tidak boleh kosong.';
        exit();
    }

    if ($tgl2 < $tgl1) {
        echo 'Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua.';
        exit();
    }
}

$str = "select distinct kodebarang \r\n       from ".$dbname.'.kebun_pakai_material_vw order by kodebarang asc';
$query = mysql_query($str);
while ($res = mysql_fetch_assoc($query)) {
    $kodebarang[$res['kodebarang']] = $res['kodebarang'];
}
$str_b = "select distinct kodebarang \r\n          from ".$dbname.'.log_pengeluaran_gudang_vw order by kodebarang';
$query_b = mysql_query($str_b) ;
while ($res_b = mysql_fetch_assoc($query_b)) {
    $kodebarang[$res_b['kodebarang']] = $res_b['kodebarang'];
}
$str1 = "select left(kodeorg,4) as kodeorg,kodebarang,sum(kwantitas) as kwantitas,tanggal \r\n       from ".$dbname.".kebun_pakai_material_vw\r\n       where left(kodeorg,4)='".$kdorg."' and tanggal between '".$tgl1_."' and '".$tgl2_."'\r\n       group by left(kodeorg,4),kodebarang,tanggal";
$query1 = mysql_query($str1);
while ($res1 = mysql_fetch_assoc($query1)) {
    $tgl[$res1['tanggal']] = $res1['tanggal'];
    if (0 !== $res1['kwantitas']) {
        $kwantitas1[$res1['tanggal']][$res1['kodebarang']] += $res1['kwantitas'];
    }
}
$str2 = "select left(kodeblok,4) as kodeorg,kodebarang,sum(kwantitas) as kwantitas,tanggal \r\n       from ".$dbname.".log_pengeluaran_gudang_vw\r\n       where left(kodeblok,4)='".$kdorg."' and tanggal between '".$tgl1_."' and '".$tgl2_."'\r\n       group by left(kodeblok,4),kodebarang,tanggal";
$query2 = mysql_query($str2);
while ($res2 = mysql_fetch_assoc($query2)) {
    $tgl[$res2['tanggal']] = $res2['tanggal'];
    if (0 !== $res2['kwantitas']) {
        $kwantitas2[$res2['tanggal']][$res2['kodebarang']] += $res2['kwantitas'];
    }
}
if ('excel' === $proses) {
    $bg = ' bgcolor=#DEDEDE';
    $brdr = 1;
} else {
    $bg = '';
    $brdr = 0;
}

if ('excel' === $proses) {
    $bgcoloraja = 'bgcolor=#DEDEDE ';
    $brdr = 1;
    $stream .= "\r\n    <table>\r\n    <tr><td colspan=8 align=center><b>Laporan Pemakaian Barang vs CU</b></td></tr>\r\n    <tr>\r\n        <td colspan=4 align=left><b>".$_SESSION['lang']['kodeorg'].' : '.$kdorg."</b></td>\r\n        <td colspan=4 align=right><b>".ucfirst($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2)."</b></td>\r\n    </tr>\r\n    </table>";
}

$stream .= "<table cellspacing='1' border='0' class='sortable'>";
$stream .= "<thead class=rowheader>\r\n<tr>\r\n<td align=center>No</td>\r\n<td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n<td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n<td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n<td align=center>".$_SESSION['lang']['namabarang']."</td>    \r\n<td align=center>".$_SESSION['lang']['satuan']."</td>\r\n<td align=center>".$_SESSION['lang']['jmlaplikasi']."</td>\r\n<td align=center>".$_SESSION['lang']['jmlcugudang']."</td>\r\n</tr></thead>\r\n<tbody>";
$no = 0;
foreach ($tgl as $dtTgl) {
    foreach ($kodebarang as $brng) {
        if (0 !== $kwantitas1[$dtTgl][$brng] || 0 !== $kwantitas2[$dtTgl][$brng]) {
            ++$no;
            $stream .= "<tr class=rowcontent>\r\n                        <td align=center>".$no."</td>\r\n                        <td align=center>".$kdorg.'</td>';
            $stream .= '<td align=center>'.tanggalnormal($dtTgl)."</td> \r\n                        <td align=center>".$brng."</td> \r\n                        <td align=left>".$optNmBarang[$brng]."</td> \r\n                        <td align=left>".$optSatuan[$brng]."</td> \r\n                        <td align=right>".number_format($kwantitas1[$dtTgl][$brng], 2)."</td> \r\n                        <td align=right>".number_format($kwantitas2[$dtTgl][$brng], 2).'</td> ';
            $stream .= '</tr>';
        }
    }
}
$stream .= '</tbody><tfoot></tfoot></table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHis');
        $nop_ = 'Laporan Pemakaian Barang vs CU_'.$kdorg;
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
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
            closedir($handle);
        }

        break;
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
        global $kdorg;
        global $tgl1;
        global $tgl2;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
        $cols = 247.5;
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
        $this->Line($this->lMargin, $this->tMargin + $height * 3, $this->lMargin + $width, $this->tMargin + $height * 3);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($width, $height, 'Laporan Pemakaian Barang vs CU '.$kdorg, '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, ucfirst($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2), '', 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(5 / 100 * $width, $height, 'No.', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, 'Kode Org.', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
        $this->Cell(27 / 100 * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['jmlaplikasi'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['jmlcugudang'], 1, 1, 'C', 1);
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
        $height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 7);
        $i = 0;
        foreach ($tgl as $dtTgl) {
            foreach ($kodebarang as $brng) {
                if (0 !== $kwantitas1[$dtTgl][$brng] || 0 !== $kwantitas2[$dtTgl][$brng]) {
                    ++$i;
                    $pdf->Cell(5 / 100 * $width, $height, $i, 1, 0, 'C', 1);
                    $pdf->Cell(10 / 100 * $width, $height, $kdorg, 1, 0, 'C', 1);
                    $pdf->Cell(10 / 100 * $width, $height, tanggalnormal($dtTgl), 1, 0, 'C', 1);
                    $pdf->Cell(10 / 100 * $width, $height, $brng, 1, 0, 'C', 1);
                    $pdf->Cell(27 / 100 * $width, $height, $optNmBarang[$brng], 1, 0, 'L', 1);
                    $pdf->Cell(8 / 100 * $width, $height, $optSatuan[$brng], 1, 0, 'L', 1);
                    $pdf->Cell(15 / 100 * $width, $height, number_format($kwantitas1[$dtTgl][$brng], 2), 1, 0, 'R', 1);
                    $pdf->Cell(15 / 100 * $width, $height, number_format($kwantitas2[$dtTgl][$brng], 2), 1, 1, 'R', 1);
                }
            }
        }
        $pdf->Output();

        break;
    default:
        break;
}

?>