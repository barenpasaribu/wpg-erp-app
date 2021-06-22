<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/devLibrary.php';
//('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
//('' === $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
//('' === $_POST['kodeorg'] ? ($kodeorg = $_GET['kodeorg']) : ($kodeorg = $_POST['kodeorg']));
$periode='';
$kodeorg='';
$proses = $_GET['proses'];
if (!empty($_POST)) {
    $periode = $_POST['periode'];
    $kodeorg = $_POST['kodeorg'];
} else {
    $periode = $_GET['periode'];
    $kodeorg = $_GET['kodeorg'];
}

$str = "select kodeblok,tahuntanam,tahun,bulan,jumlah,bjr,kgsensus,jumlahpokok,jumlahha,jumlahpremi ".
        "from kebun_rencanapanen_vw ".
        "where substr(kodeorg,1,4)='$kodeorg' and substr(tanggal,1,7)='$periode' ".
        "order by kodeblok,bulan,tahuntanam";
$query = mysql_query($str) ;
while ($res = mysql_fetch_assoc($query)) {
    $kodeblok[] = $res['kodeblok'];
    $tt[$res['kodeblok']] = $res['tahuntanam'];
    $thnprd[$res['kodeblok']] = $res['tahun'];
    $bln[$res['kodeblok']] = $res['bulan'];
    $jjg[$res['kodeblok']] = $res['jumlah'];
    $bjr[$res['kodeblok']] = $res['bjr'];
    $kgsensus[$res['kodeblok']] = $res['kgsensus'];
    $jmlpkk[$res['kodeblok']] = $res['jumlahpokok'];
    $luas[$res['kodeblok']] = $res['jumlahha'];
    $espremi[$res['kodeblok']] = $res['jumlahpremi'];
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
    $tab .= "\r\n    <table>\r\n    <tr><td colspan=5 align=center><b>Laporan Sensus Produksi</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['periode'].' : '.$periode."</b></td></tr>\r\n    <tr><td colspan=5 align=left>&nbsp;</td></tr>\r\n    </table>";
}

$tab .= "<table class=sortable cellspacing=1 border=0 width=100%>\r\n        <thead>\r\n            <tr>\r\n                <td align=center>No.</td>\r\n                <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n                <td align=center>".$_SESSION['lang']['tahuntanam']."</td>\r\n                <td align=center>".$_SESSION['lang']['tahunproduksi']."</td>\r\n                <td align=center>".$_SESSION['lang']['bulan']."</td>\r\n                <td align=center>".$_SESSION['lang']['jjg']."</td>\r\n                <td align=center>".$_SESSION['lang']['bjr']."</td>\r\n                <td align=center>".$_SESSION['lang']['kgsensus']."</td>\r\n                <td align=center>".$_SESSION['lang']['jmlpkk']."</td>\r\n                <td align=center>".$_SESSION['lang']['luas']."</td>\r\n                <td align=center>".$_SESSION['lang']['estimasipremi']."</td>\r\n             </tr>\r\n        </thead>\r\n        <tbody id=container>";
$i = 0;
if (!empty($kodeblok)) {
    foreach ($kodeblok as $blok => $lsblok) {
        ++$i;
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td align=center>'.$i.'</td>';
        $tab .= '<td align=left>'.$lsblok.'</td>';
        $tab .= '<td align=center>'.$tt[$lsblok].'</td>';
        $tab .= '<td align=center>'.$thnprd[$lsblok].'</td>';
        $tab .= '<td align=center>'.$bln[$lsblok].'</td>';
        $tab .= '<td align=right>'.number_format($jjg[$lsblok], 2).'</td>';
        $tab .= '<td align=right>'.number_format($bjr[$lsblok], 2).'</td>';
        $tab .= '<td align=right>'.number_format($kgsensus[$lsblok], 2).'</td>';
        $tab .= '<td align=right>'.number_format($jmlpkk[$lsblok], 2).'</td>';
        $tab .= '<td align=right>'.number_format($luas[$lsblok], 2).'</td>';
        $tab .= '<td align=right>'.number_format($espremi[$lsblok], 2).'</td>';
        $totalbjr += $bjr[$lsblok];
        $x = $totalbjr / $i;
        $tab .= '</tr>';
    }
}

$tab .= "<tr class=rowcontent>\r\n          <td colspan=6 align=center><b>".$_SESSION['lang']['total']."</b></td>\r\n          <td align=right><b>".number_format($x, 2)."</b></td>\r\n          <td colspan=4></td>\r\n       </tr>\r\n       </tbody></table>";
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        if ('' === $kodeorg && '' === $periode) {
            exit('Error:Field Tidak Boleh Kosong');
        }

        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHis');
        $nop_ = 'Laporan_Sensus_Produksi_'.$kodeorg.'_'.$periode;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n            parent.window.alert('Can't convert to excel format');\r\n            </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls';\r\n            </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        if ('' === $kodeorg && '' === $periode) {
            exit('Error:Field Tidak Boleh Kosong');
        }

        $cols = 247.5;
        $wkiri = 50;
        $wlain = 11;

class PDF extends FPDF
{
    public function Header()
    {
        global $kodeorg;
        global $periode;
        global $dbname;
        global $wkiri;
        global $wlain;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($width, $height, strtoupper('Laporan Sensus Produksi'), 0, 1, 'L');
        $this->Cell($width, $height, $_SESSION['lang']['periode'].' : '.$periode, 0, 1, 'R');
        $tinggiAkr = $this->GetY();
        $ksamping = $this->GetX();
        $this->SetY($tinggiAkr + 20);
        $this->SetX($ksamping);
        $this->Cell(790, $height, ' ', 0, 1, 'R');
        $height = 15;
        $this->SetFillColor(220, 220, 220);
        $this->SetFont('Arial', 'B', 8);
        $tinggiAkr = $this->GetY();
        $ksamping = $this->GetX();
        $this->SetY($tinggiAkr + 20);
        $this->SetX($ksamping);
        $this->Cell(15, $height, 'No.', TLR, 0, 'C', 1);
        $this->Cell(80, $height, 'Kode Blok', TLR, 0, 'C', 1);
        $this->Cell(60, $height, 'Tahun Tanam', TLR, 0, 'C', 1);
        $this->Cell(70, $height, 'Tahun Produksi', TLR, 0, 'C', 1);
        $this->Cell(30, $height, 'Bulan', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'JJG', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'BJR', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Kg. Sensus', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Jumlah Pokok', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Luas', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Estimasi Premi', TLR, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 11);
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
        if (!empty($kodeblok)) {
            foreach ($kodeblok as $blok => $lsblok) {
                ++$i;
                $pdf->Cell(15, $height, $i, TBLR, 0, 'L', 1);
                $pdf->Cell(80, $height, $lsblok, TBLR, 0, 'L', 1);
                $pdf->Cell(60, $height, $tt[$lsblok], TBLR, 0, 'C', 1);
                $pdf->Cell(70, $height, $thnprd[$lsblok], TBLR, 0, 'C', 1);
                $pdf->Cell(30, $height, $bln[$lsblok], TBLR, 0, 'C', 1);
                $pdf->Cell(90, $height, number_format($jjg[$lsblok], 2), TBLR, 0, 'R', 1);
                $pdf->Cell(90, $height, number_format($bjr[$lsblok], 2), TBLR, 0, 'R', 1);
                $pdf->Cell(90, $height, number_format($kgsensus[$lsblok], 2), TBLR, 0, 'R', 1);
                $pdf->Cell(90, $height, number_format($jmlpkk[$lsblok], 2), TBLR, 0, 'R', 1);
                $pdf->Cell(90, $height, number_format($luas[$lsblok], 2), TBLR, 0, 'R', 1);
                $pdf->Cell(90, $height, number_format($espremi[$lsblok], 2), TBLR, 1, 'R', 1);
            }
        }

        $totalbjr += $bjr[$lsblok] - $bjr[$lsblok];
        $x = $totalbjr / $i;
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(345, $height, 'Total', TBLR, 0, 'C', 1);
        $pdf->Cell(90, $height, number_format($x, 2), TBLR, 0, 'R', 1);
        $pdf->Cell(360, $height, '', TBLR, 0, 'C', 1);
        $pdf->Output();

        break;
    default:
        break;
}

?>