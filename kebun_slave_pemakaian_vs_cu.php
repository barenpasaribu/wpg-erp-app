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
$str1 = "select notransaksi,left(kodeorg,4) as kodeorg,kodebarang,sum(kwantitas) as kwantitas,tanggal \r\n       from ".$dbname.".kebun_pakai_material_vw\r\n       where left(kodeorg,4)='".$kdorg."' and tanggal between '".$tgl1_."' and '".$tgl2_."'\r\n       group by kodebarang order by kodebarang";
$query1 = mysql_query($str1);
while ($res1 = mysql_fetch_assoc($query1)) {
    $notrans[$res1['kodebarang']] = $res1['notransaksi'];
    if (0 !== $res1['kwantitas']) {
        $kwantitas1[$res1['kodebarang']] += $res1['kwantitas'];
    }
}
$str2 = "select left(kodeblok,4) as kodeorg,kodebarang,sum(kwantitas) as kwantitas,tanggal \r\n       from ".$dbname.".log_pengeluaran_gudang_vw\r\n       where left(kodeblok,4)='".$kdorg."' and tanggal between '".$tgl1_."' and '".$tgl2_."'\r\n       group by kodebarang order by kodebarang";
$query2 = mysql_query($str2);
while ($res2 = mysql_fetch_assoc($query2)) {
    if (0 !== $res2['kwantitas']) {
        $kwantitas2[$res2['kodebarang']] += $res2['kwantitas'];
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
    $stream .= "\r\n    <table border=".$brdr.">\r\n    <tr><td colspan=7 align=center><b>Laporan Pemakaian Barang vs CU</b></td></tr>\r\n    <tr>\r\n        <td colspan=4 align=left><b>".$_SESSION['lang']['kodeorg'].' : '.$kdorg."</b></td>\r\n        <td colspan=3 align=right><b>".ucfirst($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2)."</b></td>\r\n    </tr>\r\n    </table>";
}

$stream .= '<div style=overflow:auto; height:300px;>';
$stream .= "<table cellspacing='1' border='".$brdr."' class='sortable'>\r\n    <thead>\r\n<tr class=rowheader>\r\n<td align=center>No</td>\r\n<td align=center id=kdorg>".$_SESSION['lang']['kodeorg']."</td>\r\n<td align=center id=kdbrng>".$_SESSION['lang']['kodebarang']."</td>\r\n<td align=center>".$_SESSION['lang']['namabarang']."</td>    \r\n<td align=center>".$_SESSION['lang']['satuan']."</td>\r\n<td align=center>".$_SESSION['lang']['jmlaplikasi']."</td>\r\n<td align=center>".$_SESSION['lang']['jmlcugudang']."</td>\r\n\r\n</tr></thead>\r\n<tbody>";
$no = 0;
if (!empty($kodebarang)) {
    foreach ($kodebarang as $brng) {
        if (0 !== $kwantitas1[$brng] || 0 !== $kwantitas2[$brng]) {
            ++$no;
            $stream .= "<tr class=rowcontent>\r\n                                <td align=center>".$no."</td>\r\n                                <td align=center id=kdorng>".$kdorg.'</td>';
            $stream .= '<td align=center id=kdbrng>'.$brng."</td> \r\n                                <td align=left>".$optNmBarang[$brng]."</td> \r\n                                <td align=left>".$optSatuan[$brng]."</td> \r\n                                <td align=right title=Click style=\"cursor:pointer;\" onclick=showAplikasi(event,".$brng.",'".$tgl1_."','".$tgl2_."','".$kdorg."')>".number_format($kwantitas1[$brng], 2)."</td> \r\n                                <td align=right title=Click style=\"cursor:pointer;\" onclick=showCu(event,".$brng.",'".$tgl1_."','".$tgl2_."','".$kdorg."')>".number_format($kwantitas2[$brng], 2)."</td>\r\n                                <input type=hidden id=tgl value=".$tgl[$dtTgl]." \\>\r\n                                <input type=hidden id=notrans\\>";
            $stream .= '</tr>';
        }
    }
} else {
    $stream .= "<tr class=rowcontent>\r\n            <td align=center colspan=8>Data Empty</td>";
    $stream .= '</tr>';
}

$stream .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $stream.'###'.tanggalnormal($tgl1_).'###'.tanggalnormal($tgl2_);

        break;
    case 'getForm':
        echo "<link rel=stylesheet type=text/css href=style/generic.css>\r\n        <script language=javascript1.2 src='js/generic.js'></script>";
        echo "<fieldset>\r\n            <legend>Detail Aplikasi</legend>\r\n            <table class=sortable cellspacing=1 cellpadding=2  border=0>\r\n              <thead>\r\n                 <tr class=rowheader>\r\n                 <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                 <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n                 <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n                 <td align=center>Hasil Kerja</td>\r\n                 </tr></thead></div>";
        $sAplikasi = "select a.notransaksi,a.tanggal,a.kodeorg,a.kwantitas,b.hasilkerja\r\n                   from ".$dbname.'.kebun_pakai_material_vw a left join '.$dbname.".kebun_perawatan_vw b on a.notransaksi=b.notransaksi\r\n                   where a.kodeorg like '%".$_GET['kdorg']."%' and a.kodebarang = ".$_GET['kdbrng']." \r\n                   and a.tanggal between '".$_GET['tgla']."' and '".$_GET['tglb']."'";
        $qAplikasi = mysql_query($sAplikasi) ;
        while ($rAplikasi = mysql_fetch_assoc($qAplikasi)) {
            $notrans[] = $rAplikasi['notransaksi'];
            $tgl[$rAplikasi['notransaksi']] = $rAplikasi['tanggal'];
            $blok[$rAplikasi['notransaksi']] = $rAplikasi['kodeorg'];
            $jml1[$rAplikasi['notransaksi']] = $rAplikasi['kwantitas'];
            $hsl[$rAplikasi['notransaksi']] = $rAplikasi['hasilkerja'];
        }
        if (!empty($notrans)) {
            foreach ($notrans as $transaksi => $id) {
                ++$no;
                $tab .= "<tr class=rowcontent>\r\n                        <td align=center>".tanggalnormal($tgl[$id])."</td>\r\n                        <td align=left>".$blok[$id]."</td> \r\n                        <td align=right>".number_format($jml1[$id], 2)."</td> \r\n                        <td align=right>".number_format($hsl[$id], 2).'</td>';
            }
            $tab .= '</tr></table>';
            echo $tab;
        }

        break;
    case 'getFormCu':
        echo "<link rel=stylesheet type=text/css href=style/generic.css>\r\n        <script language=javascript1.2 src='js/generic.js'></script>";
        echo "<fieldset>\r\n            <legend>Detail Pengeluaran Gudang</legend>\r\n           <table class=sortable cellspacing=1 cellpadding=2  border=0>\r\n              <thead>\r\n                 <tr class=rowheader>\r\n                 <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                 <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n                 <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n                 </tr></thead>";
        $sCu = 'SELECT notransaksi, tanggal, kodebarang, kodeblok, kwantitas FROM '.$dbname.".log_pengeluaran_gudang_vw\r\n                WHERE kodebarang = ".$_GET['kdbrng']." and tanggal between '".$_GET['tgla']."' and '".$_GET['tglb']."'\r\n                and kodeblok like '%".$_GET['kdorg']."%' ";
        $qCu = mysql_query($sCu) ;
        while ($rCu = mysql_fetch_assoc($qCu)) {
            $tab .= "<tr class=rowcontent>\r\n                <td align=center>".tanggalnormal($rCu['tanggal'])."</td>\r\n                <td align=left>".$rCu['kodeblok']."</td> \r\n                <td align=right>".number_format($rCu['kwantitas'], 2).'</td>';
        }
        $tab .= '</tr></table>';
        echo $tab;

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
        if (!empty($kodebarang)) {
            foreach ($kodebarang as $brng) {
                if (0 !== $kwantitas1[$brng] || 0 !== $kwantitas2[$brng]) {
                    ++$i;
                    $pdf->Cell(5 / 100 * $width, $height, $i, 1, 0, 'C', 1);
                    $pdf->Cell(10 / 100 * $width, $height, $kdorg, 1, 0, 'C', 1);
                    $pdf->Cell(10 / 100 * $width, $height, $brng, 1, 0, 'C', 1);
                    $pdf->Cell(27 / 100 * $width, $height, $optNmBarang[$brng], 1, 0, 'L', 1);
                    $pdf->Cell(8 / 100 * $width, $height, $optSatuan[$brng], 1, 0, 'L', 1);
                    $pdf->Cell(15 / 100 * $width, $height, number_format($kwantitas1[$brng], 2), 1, 0, 'R', 1);
                    $pdf->Cell(15 / 100 * $width, $height, number_format($kwantitas2[$brng], 2), 1, 1, 'R', 1);
                }
            }
        }

        $pdf->Output();

        break;
    default:
        break;
}

?>