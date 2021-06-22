<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' != $_POST['proses'] ? ($proses = $_POST['proses']) : ($proses = $_GET['proses']));
('' != $_POST['pt'] ? ($pt = $_POST['pt']) : ($pt = $_GET['pt']));
('' != $_POST['unit'] ? ($unit = $_POST['unit']) : ($unit = $_GET['unit']));
('' != $_POST['kepada'] ? ($kepada = $_POST['kepada']) : ($kepada = $_GET['kepada']));
('' != $_POST['tipe'] ? ($tipe = $_POST['tipe']) : ($tipe = $_GET['tipe']));
('' != $_POST['tanggal'] ? ($tanggal = $_POST['tanggal']) : ($tanggal = $_GET['tanggal']));
('' != $_POST['sd'] ? ($sd = $_POST['sd']) : ($sd = $_GET['sd']));
$tanggal = tanggalsystem($tanggal);
$tgldari = substr($tanggal, 0, 4).'-'.substr($tanggal, 4, 2).'-'.substr($tanggal, 6, 2);
$sd = tanggalsystem($sd);
$tglsd = substr($sd, 0, 4).'-'.substr($sd, 4, 2).'-'.substr($sd, 6, 2);
if ('preview' == $proses || 'excel' == $proses || 'pdf' == $proses) {
    if ('' == $tanggal || '' == $sd) {
        echo 'Error: Date is obligatory.';
        exit();
    }

    if ($tglsd < $tgldari) {
        echo 'Error: First date must smaller than the secon date.';
        exit();
    }
}

$listakun = '(';
$no = 0;
$s_rk = 'select akunhutang,akunpiutang from '.$dbname.".keu_5caco where kodeorg='".$kepada."'";
$q_rk = mysql_query($s_rk);
while ($r_rk = mysql_fetch_assoc($q_rk)) {
    ++$no;
    $listakun .= "'".$r_rk['akunhutang']."',";
    $listakun .= "'".$r_rk['akunpiutang']."',";
}
$listakun = substr($listakun, 0, -1);
$listakun .= ')';
if (0 == $no) {
    $listakun = "('')";
}

if ('Kredit Note' == $tipe) {
    $kolom = 'kredit';
} else {
    $kolom = 'debet';
}

$s_transaksi = 'select tanggal,noreferensi,keterangan,'.$kolom.' as kolom from '.$dbname.".keu_jurnaldt_vw \r\n                where noakun in ".$listakun." and tanggal between '".$tgldari."' and '".$tglsd."'\r\n                and kodeorg='".$unit."' ";
$q_transaksi = mysql_query($s_transaksi);
if ('excel' == $proses) {
    $bg = ' bgcolor=#DEDEDE';
    $brdr = 1;
} else {
    $bg = '';
    $brdr = 0;
}

if ('excel' == $proses) {
    $bgcoloraja = 'bgcolor=#DEDEDE ';
    $brdr = 1;
    $stream .= "\r\n    <table border=0>\r\n    <tr><td align=center colspan=5><b>Laporan ".$_SESSION['lang']['debetkreditnote']."</b></td></tr>\r\n    <tr>\r\n        <td align=left>".$_SESSION['lang']['namapt']."</td>\r\n        <td>:".$pt."</td>\r\n        <td colspan=3 align=center>".$tipe."</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=left>".$_SESSION['lang']['unitkerja']."</td>\r\n        <td colspan=4>:".$unit."</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=left>".$_SESSION['lang']['kepada']."</td>\r\n        <td colspan=4>:".$kepada."</td>\r\n    </tr>\r\n    <tr>\r\n        <td align=left>".$_SESSION['lang']['periode']."</td>\r\n        <td colspan=4>:".substr($tanggal, 6, 2).'-'.substr($tanggal, 4, 2).'-'.substr($tanggal, 0, 4)." \r\n        s/d ".substr($sd, 6, 2).'-'.substr($sd, 4, 2).'-'.substr($sd, 0, 4)."</td>\r\n    </tr><tr><td colspan=5></td></tr>\r\n    </table>";
}

$stream .= '<div style=overflow:auto; height:300px;>';
$stream .= "<table cellspacing='1' border='".$brdr."' class='sortable'>\r\n<thead>\r\n<tr class=rowheader>\r\n<td align=center id=no>No.</td>\r\n<td align=center id=tgl>".$_SESSION['lang']['tanggal']."</td>\r\n<td align=center id=noref>".$_SESSION['lang']['noreferensi']."</td>\r\n<td align=center id=ket>".$_SESSION['lang']['keterangan']."</td>    \r\n<td align=center id=kolom>".$_SESSION['lang']['jumlah']."</td>\r\n</tr></thead>\r\n<tbody>";
$no = 0;
while ($r_transaksi = mysql_fetch_assoc($q_transaksi)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n                  <td align=center id=no>".$no."</td>\r\n                  <td align=center id=tgl>".$r_transaksi['tanggal']."</td>\r\n                  <td align=left id=noref>".$r_transaksi['noreferensi']."</td>\r\n                  <td align=left id=ket>".$r_transaksi['keterangan']."</td>\r\n                  <td align=right id=kolom>".number_format($r_transaksi['kolom'], 0)."</td>\r\n              </tr>";
    $jumlah += $r_transaksi['kolom'];
}
$stream .= '<tr><td colspan=4 align=center><b>'.$_SESSION['lang']['jumlah']."</b></td>\r\n          <td><b>".number_format($jumlah, 0).'</b></td></tr>';
$stream .= '</tbody></table>';
switch ($proses) {
    case 'load_unit_kpd':
        $opt_unit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $s_unit = 'select * from '.$dbname.".organisasi where induk='".$pt."' order by kodeorganisasi asc";
        $q_unit = mysql_query($s_unit);
        while ($r_unit = mysql_fetch_assoc($q_unit)) {
            $opt_unit .= "<option value='".$r_unit['kodeorganisasi']."'>".$r_unit['namaorganisasi'].'</option>';
        }
        echo $opt_unit;

        break;
    case 'load_kpd':
        $opt_kepada = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $s_kepada = 'select * from '.$dbname.".organisasi \r\n               where length(kodeorganisasi)=4 and kodeorganisasi != '".$unit."' \r\n               order by namaorganisasi asc";
        $q_kepada = mysql_query($s_kepada);
        while ($r_kepada = mysql_fetch_assoc($q_kepada)) {
            $opt_kepada .= "<option value='".$r_kepada['kodeorganisasi']."'>".$r_kepada['namaorganisasi'].'</option>';
        }
        echo $opt_kepada;

        break;
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHis');
        $nop_ = 'DebetKreditNote_'.$kepada;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls';\r\n            </script>";
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
        global $pt;
        global $unit;
        global $kepada;
        global $tanggal;
        global $sd;
        global $tipe;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
        $cols = 247.5;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 3, $this->lMargin + $width, $this->tMargin + $height * 3);
        $s_pt = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
        $q_pt = mysql_query($s_pt);
        $r_pt = mysql_fetch_assoc($q_pt);
        $s_unit = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$unit."'";
        $q_unit = mysql_query($s_unit);
        $r_unit = mysql_fetch_assoc($q_unit);
        $s_kpd = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$kepada."'";
        $q_kpd = mysql_query($s_kpd);
        $r_kpd = mysql_fetch_assoc($q_kpd);
        $this->Ln();
        $this->SetFont('Arial', '', 7);
        $this->SetX(100);
        $this->Cell(28, $height, $_SESSION['lang']['namapt'], '', 0, 'L', 1);
        $this->Cell(5, $height, ':', '', 0, 'C', 1);
        $this->Cell(200, $height, $r_pt['namaorganisasi'], '', 0, 'L', 1);
        $this->Cell(157, $height, $tipe, '', 1, 'L', 1);
        $this->SetX(100);
        $this->Cell(28, $height, $_SESSION['lang']['unit'], '', 0, 'L', 1);
        $this->Cell(5, $height, ':', '', 0, 'C', 1);
        $this->Cell(200, $height, $r_unit['namaorganisasi'], '', 0, 'L', 1);
        $this->Cell(157, $height, '', '', 1, 'R', 1);
        $this->SetX(100);
        $this->Cell(28, $height, $_SESSION['lang']['kepada'], '', 0, 'L', 1);
        $this->Cell(5, $height, ':', '', 0, 'C', 1);
        $this->Cell(200, $height, $r_kpd['namaorganisasi'], '', 0, 'L', 1);
        $this->Cell(157, $height, '', '', 1, 'R', 1);
        $this->SetX(100);
        $this->Cell(28, $height, ucfirst($_SESSION['lang']['periode']), '', 0, 'L', 1);
        $this->Cell(5, $height, ':', '', 0, 'C', 1);
        $this->Cell(200, $height, substr($tanggal, 6, 2).'-'.substr($tanggal, 4, 2).'-'.substr($tanggal, 0, 4).' s/d '.substr($sd, 6, 2).'-'.substr($sd, 4, 2).'-'.substr($sd, 0, 4), B, 0, 'L', 1);
        $this->Cell(157, $height, '', '', 1, 'R', 1);
        $this->SetFont('Arial', '', 7);
        $this->SetFillColor(220, 220, 220);
        $this->SetX(100);
        $this->Cell(20, $height, 'No.', 1, 0, 'C', 1);
        $this->Cell(50, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(70, $height, $_SESSION['lang']['noreferensi'], 1, 0, 'C', 1);
        $this->Cell(200, $height, $_SESSION['lang']['keterangan'], 1, 0, 'C', 1);
        $this->Cell(70, $height, $_SESSION['lang']['jumlah'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 7);
        $q_transaksi = mysql_query($s_transaksi);
        $no = 0;
        while ($r_transaksi = mysql_fetch_assoc($q_transaksi)) {
            ++$no;
            $pdf->SetX(100);
            $pdf->Cell(20, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(50, $height, $r_transaksi['tanggal'], TBLR, 0, 'C', 1);
            $pdf->Cell(70, $height, $r_transaksi['noreferensi'], TBLR, 0, 'L', 1);
            $pdf->Cell(200, $height, $r_transaksi['keterangan'], TBLR1, 0, 'L', 1);
            $pdf->Cell(70, $height, number_format($r_transaksi['kolom']), TBLR, 1, 'R', 1);
        }
        $pdf->SetX(100);
        $pdf->Cell(340, $height, $_SESSION['lang']['jumlah'], TBLR, 0, 'C', 1);
        $pdf->Cell(70, $height, number_format($jumlah, 0), TBLR, 1, 'R', 1);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetX(100);
        $pdf->Cell(20, $height, 'CC:', TL, 0, 'L', 1);
        $pdf->Cell(180, $height, '', TR, 0, 'L', 1);
        $pdf->Cell(70, $height, $_SESSION['lang']['disetujui'], TBLR, 0, 'C', 1);
        $pdf->Cell(70, $height, $_SESSION['lang']['diperiksa'], TBLR, 0, 'C', 1);
        $pdf->Cell(70, $height, $_SESSION['lang']['diperiksa'], TBLR, 1, 'C', 1);
        $pdf->SetX(100);
        $pdf->Cell(20, $height, '', L, 0, 'L', 1);
        $pdf->Cell(180, $height, '- Accounting HO', R, 0, 'L', 1);
        $pdf->Cell(70, $height, '', TLR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', TLR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', TLR, 1, 'C', 1);
        $pdf->SetX(100);
        $pdf->Cell(20, $height, '', L, 0, 'L', 1);
        $pdf->Cell(180, $height, '- Arsip', R, 0, 'L', 1);
        $pdf->Cell(70, $height, '', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', LR, 1, 'C', 1);
        $pdf->SetX(100);
        $pdf->Cell(20, $height, '', L, 0, 'L', 1);
        $pdf->Cell(180, $height, '', R, 0, 'L', 1);
        $pdf->Cell(70, $height, '', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '', LR, 1, 'C', 1);
        $pdf->SetX(100);
        $pdf->Cell(20, $height, '', L, 0, 'L', 1);
        $pdf->Cell(180, $height, '', R, 0, 'L', 1);
        $pdf->Cell(70, $height, '---------------', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '---------------', LR, 0, 'C', 1);
        $pdf->Cell(70, $height, '---------------', LR, 1, 'C', 1);
        $pdf->SetX(100);
        $pdf->Cell(20, $height, '', BL, 0, 'L', 1);
        $pdf->Cell(180, $height, '', BR, 0, 'L', 1);
        $pdf->Cell(70, $height, 'Manager', TBLR, 0, 'C', 1);
        $pdf->Cell(70, $height, 'KTU/Kasie', TBLR, 0, 'C', 1);
        $pdf->Cell(70, $height, 'Accountant', TBLR, 1, 'C', 1);
        $pdf->Output();

        break;
    default:
        break;
}

?>