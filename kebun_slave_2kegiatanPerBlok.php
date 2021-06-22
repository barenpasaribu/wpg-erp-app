<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdOrg = $_POST['kodeorg'];
$kegiatan = $_POST['kegiatan'];
$tgl1_ = $_POST['tgl1'];
$tgl2_ = $_POST['tgl2'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kdOrg = $_GET['kodeorg'];
    $kegiatan = $_GET['kegiatan'];
    $tgl1_ = $_GET['tgl1'];
    $tgl2_ = $_GET['tgl2'];
}

$tgl1_ = tanggalsystem($tgl1_);
$tgl1 = substr($tgl1_, 0, 4).'-'.substr($tgl1_, 4, 2).'-'.substr($tgl1_, 6, 2);
$tgl2_ = tanggalsystem($tgl2_);
$tgl2 = substr($tgl2_, 0, 4).'-'.substr($tgl2_, 4, 2).'-'.substr($tgl2_, 6, 2);
if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $tgl1_ || '' === $tgl2_) {
        echo 'Error: Date required.';
        exit();
    }

    if ($tgl2 < $tgl1) {
        echo 'Error: First date must lower than the second.';
        exit();
    }
}

$tahuntanam = [];
$str = 'select kodeorg,tahuntanam from '.$dbname.".setup_blok where kodeorg like '".$kdOrg."%'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tahuntanam[$bar->kodeorg] = $bar->tahuntanam;
}
$namakegiatan = [];
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun as namaakun';
}

$str = 'select noakun,'.$zz.' from '.$dbname.'.keu_5akun where length(noakun)=7';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namakegiatan[$bar->noakun] = $bar->namaakun;
}
$satuan = [];
$str = 'select kodekegiatan,satuan from '.$dbname.'.setup_kegiatan order by kodekegiatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $satuan[$bar->kodekegiatan] = $bar->satuan;
}
$str = 'select noakun,sum(debet) as biaya,kodeblok from '.$dbname.".keu_jurnaldt_vw \r\n      where tanggal between '".$tgl1."' and '".$tgl2."' and noakun like '".substr($kegiatan, 0, 7)."%' and kodeorg='".$kdOrg."' and kodekegiatan='".$kegiatan."'\r\n      group by noakun,kodeblok";
$res = mysql_query($str);
if ('6110101' === substr($kegiatan, 0, 7)) {
    $kegiatanx = '0';
} else {
    $kegiatanx = $kegiatan;
}

$str1 = "SELECT a.kodeorg,case a.kodekegiatan\r\n   when '' then ".$kegiatan." \r\n   when '0' then ".$kegiatan."\r\n   else a.kodekegiatan end as kegiatan,\r\n   sum(a.hasilkerja) as hasil,sum(a.hasilkerjakg) as kg FROM ".$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b\r\n   on a.notransaksi=b.notransaksi where tanggal between '".$tgl1."' and '".$tgl2."' and a.notransaksi like '%".$kdOrg."%'\r\n   and kodekegiatan='".$kegiatanx."'   \r\n    group by kodeorg,kegiatan";
$res1 = mysql_query($str1);
while ($bar = mysql_fetch_array($res1)) {
    $pres[$bar['kodeorg']][$bar['kegiatan']] = $bar['hasil'];
    $kg[$bar['kodeorg']][$bar['kegiatan']] = $bar['kg'];
}
$stream = 'UNIT:'.$kdOrg."<br>\r\n                RANGE:".tanggalnormal($tgl1).' - '.tanggalnormal($tgl2).'';
if ('excel' === $proses) {
    $stream .= "<table cellspacing='1' border='1' class='sortable'>";
} else {
    $stream .= "<table cellspacing='1' border='0' class='sortable'>";
}

$stream .= "<thead class=rowheader>\r\n                <tr>\r\n                <td>No</td>\r\n                <td>".$_SESSION['lang']['noakun']."</td>\r\n                <td>".$_SESSION['lang']['kegiatan']."</td>    \r\n                <td>".$_SESSION['lang']['blok']."</td>\r\n                <td>".$_SESSION['lang']['tahuntanam']."</td> \r\n                <td>".$_SESSION['lang']['hasilkerjajumlah']."</td>\r\n                <td>".$_SESSION['lang']['satuan']."</td>                \r\n                <td>".$_SESSION['lang']['panen']."(Kg)</td>    \r\n                <td>".$_SESSION['lang']['jumlah']."(Rp.)</td>\r\n                </tr></thead>\r\n                <tbody>";
$no = 0;
$ttl = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$bar->noakun."</td>    \r\n                <td>".$namakegiatan[$bar->noakun]."</td>  \r\n                <td>".$bar->kodeblok."</td>\r\n                <td>".$tahuntanam[$bar->kodeblok]."</td>\r\n                <td align=right>".number_format($pres[$bar->kodeblok][$kegiatan])."</td>\r\n                 <td>".$satuan[$kegiatan]."</td>                     \r\n                <td align=right>".number_format($kg[$bar->kodeblok][$kegiatan])."</td>    \r\n                <td align=right>".number_format($bar->biaya)."</td>\r\n              </tr>";
    $ttl += $bar->biaya;
    $tths += $pres[$bar->kodeblok][$kegiatan];
    $ttkg += $kg[$bar->kodeblok][$kegiatan];
}
$stream .= "<tr class=rowcontent>\r\n                <td colspan=5>Total</td>\r\n                <td align=right>".number_format($tths)."</td>\r\n                <td></td>    \r\n                <td align=right>".number_format($ttkg)."</td>    \r\n                <td align=right>".number_format($ttl)."</td>\r\n              </tr>";
$stream .= '</tbody><tfoot></tfoot></table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $qwe = date('YmdHms');
        $nop_ = 'Laporan Biaya Kegiatan Per Blok '.$kdOrg.'_'.$kegiatan.'_'.$qwe;
        if (0 < strlen($stream)) {
            $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
            gzwrite($gztralala, $stream);
            gzclose($gztralala);
            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls.gz';\r\n                    </script>";
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
        global $kdOrg;
        global $kdAfd;
        global $tgl1;
        global $tgl2;
        global $where;
        global $nmOrg;
        global $lok;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
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
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($width, $height, 'Laporan Biaya Kegiatan Per Blok '.$kdOrg.' '.$kegiatan, '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2), '', 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['nomor'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
        $this->Cell(30 / 100 * $width, $height, $_SESSION['lang']['kegiatan'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['tahuntanam'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['jumlah'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'pt', 'Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 13;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 10);
        $res = mysql_query($str);
        $no = 0;
        $ttl = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pdf->Cell(8 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(12 / 100 * $width, $height, $bar->noakun, 1, 0, 'C', 1);
            $pdf->Cell(30 / 100 * $width, $height, $namakegiatan[$bar->noakun], 1, 0, 'L', 1);
            $pdf->Cell(15 / 100 * $width, $height, $bar->kodeblok, 1, 0, 'L', 1);
            $pdf->Cell(15 / 100 * $width, $height, $tahuntanam[$bar->kodeblok], 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, number_format($bar->biaya), 1, 1, 'R', 1);
            $ttl += $bar->biaya;
        }
        $pdf->Cell(80 / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
        $pdf->Cell(15 / 100 * $width, $height, number_format($ttl), 1, 1, 'R', 1);
        $pdf->Output();

        break;
    default:
        break;
}

?>