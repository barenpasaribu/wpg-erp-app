<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$tahun = $_POST['thn'];
$kdVhc = $_POST['kdVhc'];
switch ($proses) {
    case 'preview':
        echo "<table cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr><td>".$_SESSION['lang']['anggaranTraksiDetail'].'</td><td>'.$_SESSION['lang']['anggaranTraksiAlokasi'].'</td></tr></thead><tbody>';
        echo "<td valign=top><table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t</tr></thead><tbody id=containDetailTraksi>";
        $str = 'select * from '.$dbname.".keu_anggaranvhcdt where tahun='".$tahun."' and kodevhc='".$kdVhc."'  order by `tahun` desc";
        if ($res = mysql_query($str)) {
            while ($bar = mysql_fetch_assoc($res)) {
                $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$bar['kodebarang']."'";
                $qBrg = mysql_query($sBrg);
                $rBrg = mysql_fetch_assoc($qBrg);
                ++$no;
                echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$rBrg['namabarang']."</td>\r\n\t\t\t<td>".number_format($bar['jumlah'], 2).'</td></tr>';
            }
        }

        echo '</tbody></table></td>';
        echo "<td valign=top>  <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['jmlhMeter']."</td>\r\n\t\t<td>Jan</td>\r\n\t\t<td>Feb</td>\r\n\t\t<td>Mar</td>\r\n\t\t<td>Apr</td>\r\n\t\t<td>Mei</td>\r\n\t\t<td>Jun</td>\r\n\t\t<td>Jul</td>\r\n\t\t<td>Aug</td>\r\n\t\t<td>Sep</td>\r\n\t\t<td>Okt</td>\r\n\t\t<td>Nov</td>\r\n\t\t<td>Des</td>\r\n\t\t</tr></thead><tbody id=containAlokasi>";
        $sql = 'select * from '.$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'  order by `tahun` desc ";
        if ($query = mysql_query($sql)) {
            while ($bar = mysql_fetch_assoc($query)) {
                ++$nor;
                echo "\r\n\t\t\t<tr class=rowcontent>\r\n\t\t\t<td>".$nor."</td>\r\n\t\t\t<td>".$bar['kodeorg']."</td>\r\n\t\t\t<td>".$bar['jlhmeter']."</td>\r\n\t\t\t<td>".$bar['jan']."</td>\r\n\t\t\t<td>".$bar['feb']."</td>\r\n\t\t\t<td>".$bar['mar']."</td>\r\n\t\t\t<td>".$bar['apr']."</td>\r\n\t\t\t<td>".$bar['mei']."</td>\r\n\t\t\t<td>".$bar['jun']."</td>\r\n\t\t\t<td>".$bar['jul']."</td>\r\n\t\t\t<td>".$bar['agu']."</td>\r\n\t\t\t<td>".$bar['sep']."</td>\r\n\t\t\t<td>".$bar['okt']."</td>\r\n\t\t\t<td>".$bar['nov']."</td>\r\n\t\t\t<td>".$bar['des']."</td>\r\n\t\t\t</tr>";
            }
        }

        echo '</tbody></table></td></tbody></table>';

        break;
    case 'pdf':
        $tahun = $_GET['thn'];
        $kdVhc = $_GET['kdVhc'];

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
        global $tahun;
        global $kdVhc;
        global $kdOrg;
        global $tkdOperasi;
        global $jmlhHariOperasi;
        global $meter;
        $sql = 'select * from '.$dbname.".keu_anggaranvhcht where kodevhc='".$kdVhc."' and tahun='".$tahun."'";
        $query = mysql_query($sql);
        $res = mysql_fetch_assoc($query);
        $tkdOperasi = $res['jlhharitdkoperasi'];
        $jmlhHariOperasi = $res['jlhharioperasi'];
        $meter = $res['merterperhari'];
        $kdOrg = $res['orgdata'];
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
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
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['anggaranTraksi'], '', 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tahunanggaran'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $kdVhc, '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['jmlhHariOperasi'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $jmlhHariOperasi, 0, 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['pemakaianHmKm'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $meter, '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['jmlhHariTdkOpr'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $tkdOperasi, 0, 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, date('d-m-Y H:i:s'), '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(15 / 100 * $width, $height, $_SESSION['standard']['username'], 0, 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $kdOrg, '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->Cell($width, $height, $_SESSION['lang']['anggaranTraksiDetail'], 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(25 / 100 * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['grnd_total'], 1, 1, 'C', 1);
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
        $sDet = 'select * from '.$dbname.".keu_anggaranvhcdt where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
        $qDet = mysql_query($sDet);
        while ($rDet = mysql_fetch_assoc($qDet)) {
            ++$no;
            $sCust = 'select namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$rDet['kodebarang']."'";
            $qCust = mysql_query($sCust);
            $rCust = mysql_fetch_assoc($qCust);
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(25 / 100 * $width, $height, $rCust['namabarang'], 1, 0, 'L', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format($rDet['jumlah'], 2).' '.$rCust['satuan'], 1, 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, number_format($rDet['hargatotal'], 2), 1, 1, 'R', 1);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', 'U', 12);
        $pdf->Cell($width, $height, $_SESSION['lang']['anggaranTraksiAlokasi'], 0, 1, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $pdf->Cell(12 / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, $_SESSION['lang']['jmlhMeter'], 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'Jan', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'FEB', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'MAR', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'APR', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'MEI', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'JUN', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'JUL', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'AGU', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'SEP', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'OKT', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'NOV', 1, 0, 'C', 1);
        $pdf->Cell(5 / 100 * $width, $height, 'Des', 1, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $sDetBuged = 'select * from '.$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
        $qDetBudged = mysql_query($sDetBuged);
        while ($rDetBugdeg = mysql_fetch_assoc($qDetBudged)) {
            ++$no;
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(12 / 100 * $width, $height, $rDetBugdeg['kodeorg'], 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $rDetBugdeg['jlhmeter'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['jan'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['feb'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['mar'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['apr'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['mei'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['jun'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['jul'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['agu'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['sep'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['okt'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['nov'], 1, 0, 'C', 1);
            $pdf->Cell(5 / 100 * $width, $height, $rDetBugdeg['des'], 1, 1, 'C', 1);
        }
        $pdf->Output();

        break;
    case 'excel':
        $tahun = $_GET['thn'];
        $kdVhc = $_GET['kdVhc'];
        $sHeader = 'select * from '.$dbname.".keu_anggaranvhcht where tahun='".$tahun."' and kodevhc ='".$kdVhc."'";
        $qHeader = mysql_query($sHeader);
        $rHeader = mysql_fetch_assoc($qHeader);
        $strx = 'select * from '.$dbname.".keu_anggaranvhcdt a  where a.tahun='".$tahun."' and a.kodevhc='".$kdVhc."' order by a.tahun desc";
        $stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td colspan=15 align=center>".$_SESSION['lang']['anggaranTraksi']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['tahunanggaran'].'</td><td>'.$tahun."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['jmlhHariOperasi'].'</td><td>'.$rHeader['jlhharioperasi']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['pemakaianHmKm'].'</td><td>'.$rHeader['jlhharioperasi']."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['jmlhHariTdkOpr'].'</td><td>'.$rHeader['merterperhari']."</td></tr>\r\n\t\t\t<tr><td colspan=3></td><td></td></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t<tr><td colspan=5 align=center>".$_SESSION['lang']['anggaranTraksiDetail']."</td></tr>\r\n\t\t\t<tr>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['grnd_total']."</td>\t\t\r\n\t\t\t</tr>";
        $resx = mysql_query($strx);
        $row = mysql_fetch_row($resx);
        if ($row < 1) {
            $stream .= "\t<tr class=rowcontent>\r\n\t\t\t<td colspan=5 align=center>Not Avaliable</td></tr>\r\n\t\t\t";
        } else {
            $no = 0;
            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_assoc($resx)) {
                ++$no;
                $sKdBrg = 'select  a.namabarang,b.hargasatuan from '.$dbname.'.log_5masterbarang a inner join  '.$dbname.".log_5masterbaranganggaran b on a.kodebarang=b.kodebarang where a.kodebarang='".$barx['kodebarang']."'";
                $qKdBrg = mysql_query($sKdBrg);
                $rKdBrg = mysql_fetch_assoc($qKdBrg);
                $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$rKdBrg['namabarang']."</td>\r\n\t\t\t\t<td>".number_format($barx['jumlah'], 2)."</td>\r\n\t\t\t\t<td>".'Rp. '.number_format($rKdBrg['hargasatuan'], 2)."</td>\r\n\t\t\t\t<td>".'Rp. '.number_format($barx['hargatotal'], 2)."</td>\t\r\n\t\t\t\t</tr>";
            }
        }

        $stream .= '</table>';
        $sql = 'select * from '.$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
        $stream .= "\r\n\t\t\t<br />\r\n\t\t\t<table border=1>\r\n\t\t\t<tr>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhMeter']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>Jan</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>FEB</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>MAR</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>APR</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>MEI</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>JUN</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>JUL</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>AGU</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>SEP</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>OKT</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>NOV</td>\t\t\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>DES</td>\r\n\t\t\t</tr>";
        $res = mysql_query($sql);
        $rowx = mysql_fetch_row($res);
        if ($rowx < 1) {
            $stream .= "\t<tr class=rowcontent>\r\n\t\t\t<td colspan=15 align=center>Not Avaliable</td></tr>\r\n\t\t\t";
        } else {
            $no = 0;
            $res = mysql_query($sql);
            while ($barx = mysql_fetch_assoc($res)) {
                ++$nox;
                $stream .= "\t<tr class=rowcontent>\r\n\t\t\t\t<td>".$nox."</td>\r\n\t\t\t\t<td>".$barx['kodeorg']."</td>\r\n\t\t\t\t<td>".number_format($barx['jlhmeter'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['jan'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['feb'], 2)."</td>\t\r\n\t\t\t\t<td>".number_format($barx['mar'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['apr'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['mei'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['jun'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['jul'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['agu'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['sep'], 2)."</td>\t\r\n\t\t\t\t<td>".number_format($barx['okt'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['nov'], 2)."</td>\r\n\t\t\t\t<td>".number_format($barx['des'], 2)."</td>\r\n\t\t\t\t</tr>";
            }
        }

        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'AnggaranTraksi';
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
                echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>