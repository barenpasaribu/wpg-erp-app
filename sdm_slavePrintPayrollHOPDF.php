<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
require_once 'config/connection.php';
$periode = $_GET['periode'];
$tipe = $_GET['tipe'];
$username = $_GET['username'];
if ('' == $username) {
    $username = $_SESSION['standard']['username'];
} else {
    $username = $username;
}

class PDF extends FPDF
{
    public $col = 0;

    public function SetCol($col)
    {
        $this->col = $col;
        $x = 10 + $col * 100;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    public function AcceptPageBreak()
    {
        if ($this->col < 1) {
            $this->SetCol($this->col + 1);
            $this->SetY(10);

            return false;
        }

        $this->SetCol(0);

        return true;
    }

    public function Header()
    {
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 5);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 5);
$arrMinusId = [];
$arrMinusName = [];
$str = 'select distinct id,name from '.$dbname.".sdm_ho_component  a\r\n        left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen\r\n        where plus=0 and jumlah!=0 and b.periodegaji='".$periode."'\r\n        order by id";
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    array_push($arrMinusId, $bar->id);
    array_push($arrMinusName, $bar->name);
}
$arrPlusId = $arrMinusId;
$arrPlusName = $arrMinusName;
for ($r = 0; $r < count($arrMinusId); ++$r) {
    $arrPlusId[$r] = '';
    $arrPlusName[$r] = '';
}
$str = 'select distinct id,name from '.$dbname.".sdm_ho_component  a\r\n        left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen\r\n        where plus=1 and jumlah!=0 and b.periodegaji='".$periode."'\r\n        order by id";
$res = mysql_query($str, $conn);
$n = -1;
while ($bar = mysql_fetch_object($res)) {
    ++$n;
    $arrPlusId[$n] = $bar->id;
    $arrPlusName[$n] = $bar->name;
}
if ('thr' == $tipe) {
    $arrPlusName[0] = 'Tunj. Hari Raya/THR';
}

if ('jaspro' == $tipe) {
    $arrPlusName[0] = 'Jasa Produksi/Bonus';
}

$str1 = 'select distinct e.karyawanid,e.name,e.bank,e.bankaccount from '.$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji m\r\n       where e.operator='".$username."'\r\n\t   and e.karyawanid=m.karyawanid and periodegaji='".$periode."' and e.karyawanid not in (0999999999,0888888888)\r\n       order by e.name";
$res1 = mysql_query($str1, $conn);
$jml = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$jml;
    $arrValPlus = [];
    $arrValMinus = [];
    for ($x = 0; $x < count($arrPlusId); ++$x) {
        $arrValPlus[$x] = 0;
        $arrValMinus[$x] = 0;
    }
    $terbilang = '';
    $strg = 'select terbilang from '.$dbname.".sdm_ho_payrollterbilang\r\n\t       where userid=".$bar1->karyawanid." and periode='".$periode."'\r\n\t\t   and `type`='".$tipe."'";
    $resg = mysql_query($strg, $conn);
    while ($barg = mysql_fetch_object($resg)) {
        $terbilang = $barg->terbilang;
    }
    $stg = 'select a.kodejabatan,a.tanggalmasuk,a.bagian,b.namajabatan from '.$dbname.".datakaryawan a\r\n         left join ".$dbname.".sdm_5jabatan b\r\n\t\t on a.kodejabatan=b.kodejabatan\r\n         where a.karyawanid=".$bar1->karyawanid;
    $reg = mysql_query($stg, $conn);
    $tglmasuk = '';
    $title = '';
    $dept = '';
    while ($barg = mysql_fetch_object($reg)) {
        $tglmasuk = tanggalnormal($barg->tanggalmasuk);
        $dept = $barg->bagian;
        $title = $barg->namajabatan;
    }
    $str3 = 'select a.idkomponen,a.jumlah,b.id as komponen, case b.plus when 0 then -1 else b.plus end as pengali,b.name as nakomp from '.$dbname.'.sdm_gaji a, '.$dbname.'.sdm_ho_component b where a.idkomponen=b.id and a.karyawanid='.$bar1->karyawanid." and a.periodegaji='".$periode."' order by a.idkomponen";
    $res3 = mysql_query($str3, $conn);
    while ($bar3 = mysql_fetch_object($res3)) {
        for ($g = 0; $g < count($arrPlusId); ++$g) {
            if ($bar3->idkomponen == $arrPlusId[$g]) {
                $arrValPlus[$g] = $bar3->jumlah;
            }

            if ($bar3->idkomponen == $arrMinusId[$g]) {
                $arrValMinus[$g] = $bar3->jumlah;
            }
        }
    }
    if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
        $pdf->Image('images/SSP_logo.jpg', $pdf->GetX(), $pdf->GetY() - 7, 15);
    } else {
        if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
            $pdf->Image('images/MI_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
        } else {
            if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                $pdf->Image('images/HS_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
            } else {
                $pdf->Image('images/BM_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
            }
        }
    }

    $pdf->SetX($pdf->getX() + 15);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(70, 5, 'PT.SWADAYA SAPTA PUTRA', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 5);
    $pdf->Cell(60, 3, 'PAYROLL SLYP/SLIP GAJI : '.numToMonth(substr($periode, 5, 2), 'I', 'short').' '.substr($periode, 0, 4), 'T', 0, 'L');
    $pdf->SetFont('Arial', '', 4);
    $pdf->Cell(20, 3, 'Printed on: '.date('d-m-Y: H:i:s'), 'T', 1, 'R');
    $pdf->SetFont('Arial', '', 5);
    $pdf->Cell(10, 3, 'NIP/TMK', 0, 0, 'L');
    $pdf->Cell(30, 3, ': '.$bar1->karyawanid.' / '.$tglmasuk, 0, 0, 'L');
    $pdf->Cell(15, 3, 'UNIT/BAGIAN', 0, 0, 'L');
    $pdf->Cell(25, 3, ': '.$dept, 0, 1, 'L');
    $pdf->Cell(10, 3, 'NAMA', 0, 0, 'L');
    $pdf->Cell(30, 3, ': '.$bar1->name, 0, 0, 'L');
    $pdf->Cell(15, 3, 'JABATAN', 0, 0, 'L');
    $pdf->Cell(25, 3, ':'.$title, 0, 1, 'L');
    $pdf->Cell(40, 3, 'PENAMBAH', 'TB', 0, 'C');
    $pdf->Cell(40, 3, 'PENGURANG', 'TB', 1, 'C');
    for ($mn = 0; $mn < count($arrPlusId); ++$mn) {
        $pdf->Cell(20, 3, $arrPlusName[$mn], 0, 0, 'L');
        if ('' == $arrPlusName[$mn]) {
            $pdf->Cell(2, 3, '', 0, 0, 'L');
            $pdf->Cell(18, 3, '', 'R', 0, 'R');
        } else {
            $pdf->Cell(2, 3, ':Rp.', 0, 0, 'L');
            $pdf->Cell(18, 3, number_format($arrValPlus[$mn], 2, '.', ','), 'R', 0, 'R');
        }

        $pdf->Cell(20, 3, $arrMinusName[$mn], 0, 0, 'L');
        if ('' == $arrMinusName[$mn]) {
            $pdf->Cell(2, 3, '', 0, 0, 'L');
            $pdf->Cell(18, 3, '', 0, 1, 'R');
        } else {
            $pdf->Cell(2, 3, ':Rp.', 0, 0, 'L');
            $pdf->Cell(18, 3, number_format($arrValMinus[$mn] * -1, 2, '.', ','), 0, 1, 'R');
        }
    }
    $pdf->Cell(20, 3, 'Total.Pendapatan', 'TB', 0, 'L');
    $pdf->Cell(2, 3, ':Rp.', 'TB', 0, 'L');
    $pdf->Cell(18, 3, number_format(array_sum($arrValPlus), 2, '.', ','), 'TB', 0, 'R');
    $pdf->Cell(20, 3, 'Total.Pengurangan', 'TB', 0, 'L');
    $pdf->Cell(2, 3, ':Rp.', 'TB', 0, 'L');
    $pdf->Cell(18, 3, number_format(array_sum($arrValMinus) * -1, 2, '.', ','), 'TB', 1, 'R');
    $pdf->SetFont('Arial', 'B', 5);
    $pdf->Cell(20, 3, 'Gaji.Bersih', 0, 0, 'L');
    $pdf->Cell(2, 3, ':Rp.', 0, 0, 'L');
    $pdf->Cell(18, 3, number_format(array_sum($arrValPlus) - array_sum($arrValMinus), 2, '.', ','), 0, 0, 'R');
    $pdf->Cell(42, 3, '', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 5);
    $pdf->Cell(20, 3, 'Terbilang', 0, 0, 'L');
    $pdf->Cell(2, 3, ':', 0, 0, 'L');
    $pdf->MultiCell(58, 3, $terbilang.' rupiah', 0, 'L');
    $pdf->SetFont('Arial', 'I', 4);
    $pdf->Cell(80, 3, 'Note: This is computer generated system, signature is not required', 'T', 1, 'L');
    $pdf->SetFont('Arial', '', 5);
    $pdf->Ln(10);
    if (225 < $pdf->GetY() && $pdf->col < 1) {
        $pdf->AcceptPageBreak();
    }

    if (225 < $pdf->GetY() && 0 < $pdf->col) {
        $r = 275 - $pdf->GetY();
        $pdf->Cell(80, $r, '', 0, 1, 'L');
    }
}
$pdf->Output();

?>