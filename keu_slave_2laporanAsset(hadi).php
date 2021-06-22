<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdOrg = $_POST['kdOrg'];
$kdAst = $_POST['kdAst'];
$tpAsset = $_POST['tpAsset'];
$unitCode = $_POST['unit'];
if ('' == $kdOrg) {
    $kdOrg = $_GET['kdOrg'];
}

if ('' == $kdAst) {
    $kdAst = $_GET['kdAst'];
}

$str = 'select namakaryawan,karyawanid from '.$dbname.'.datakaryawan where karyawanid='.$_SESSION['standard']['userid'].'';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namakar[$bar->karyawanid] = $bar->namakaryawan;
}
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";
$qOrg = mysql_query($sOrg);
$rOrg = mysql_fetch_assoc($qOrg);
$nmOrg = $rOrg['namaorganisasi'];
$brd = 0;
$bgBelakang = 'bgcolor=#00FF40 align=center';
if ('excel' == $proses) {
    $brd = 1;
    $bgBelakang = 'bgcolor=#00FF40 ';
}

if ($tpAsset = 'Seluruhnya' or empty($tpAsset)) {
	$where = "";
} else {
    $where = " and tipeasset='".$tpAsset."'";
	echo "warning: where: ".$where." /tipe aset: ".$tpAsset;
	exit();
}

$data = $_SESSION['lang']['daftarasset'].':  '.$nmOrg.' '.$_SESSION['lang']['periode'].':'.$kdAst;
$data .= '<table class=sortable cellspacing=1 border='.$brd.' width=1800px><thead>';
$data .= ' <tr class=rowheader>';
$data .= '<td align=center '.$bgBelakang.'>No</td>';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['kodeorganisasi'].'</td>';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['kodeasset'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['thnperolehan'].'</td>';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['namaasset'].'</td>';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['status'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['hargaperolehan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['jumlahbulanpenyusutan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['usia'].' ('.$_SESSION['lang']['bulan'].')</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['sisa'].' ('.$_SESSION['lang']['bulan'].')</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['akumulasipenyusutan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['nilaibuku'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['keterangan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['awalpenyusutan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['bulanan'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>'.$_SESSION['lang']['persendecline'].'</td> ';
$data .= '<td align=center '.$bgBelakang.'>Leasing</td>';
$data .= '</tr> </thead><tbody>';

/*if (substr($where,strlen($where)-1,1)= '='){
	$where = "";
}*/

if (empty($unitCode) or $unitCode = 'Seluruhnya') {
    $sList = 'select * from '.$dbname.'.sdm_daftarasset where  kodeorg in (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$kdOrg."') ".$where.' order by kodeasset';
} else {
    $sList = 'select * from '.$dbname.".sdm_daftarasset where  kodeorg = '".$unitCode."' ".$where.' order by kodeasset';
}

$optMetode = makeOption($dbname, 'sdm_5tipeasset', 'kodetipe,metodepenyusutan');
$qList = mysql_query($sList);
$no = 0;
$totHarga = 0;
$totHargaAkumul = 0;
$totNilai = 0;
$bulanan = 0;
$tpengurang = 0;
$tpenambah = 0;
while ($bar = mysql_fetch_assoc($qList)) {
    ++$no;
    $tgl1 = $bar['awalpenyusutan'].'-01';
    $tgl2 = $kdAst.'-02';
    $tahun1 = substr($tgl1, 0, 4);
    $bulan1 = substr($tgl1, 5, 2);
    $tahun2 = substr($tgl2, 0, 4);
    $bulan2 = substr($tgl2, 5, 2);
    $selisih['months_total'] = ($tahun2 * 12 + $bulan2) - ($tahun1 * 12 + $bulan1) + 1;
    $data .= '<tr class=rowcontent>';
    $data .= '<td align=center>'.$no.'</td>';
    $data .= '<td>'.$bar['kodeorg'].'</td>';
    $data .= '<td>'.$bar['kodeasset'].'</td>';
    $data .= '<td align=right>'.$bar['tahunperolehan'].'</td>';
    $data .= '<td>'.$bar['namasset'].'</td>';
    if (0 == $bar['status']) {
        $data .= '<td>'.$_SESSION['lang']['tidakaktif'].'</td>';
    } else {
        if (1 == $bar['status']) {
            $data .= '<td>'.$_SESSION['lang']['aktif'].'</td>';
        } else {
            if (2 == $bar['status']) {
                $data .= '<td>'.$_SESSION['lang']['dlm_rusak_rmh'].'</td>';
            } else {
                $data .= '<td>No Status</td>';
            }
        }
    }
//	echo "warning: ".$data." /bar status: ".$bar['status'];
//	exit();
	
    $tgl1 = $bar['awalpenyusutan'].'-01';
    if ($bar['jlhblnpenyusutan'] < $selisih['months_total']) {
        $selisih['months_total'] = $bar['jlhblnpenyusutan'];
    }

    if ($tgl2 < $tgl1) {
        $selisih['months_total'] = 0;
    }

    $sisabln = $bar['jlhblnpenyusutan'] - $selisih['months_total'];
    if ('-' == substr($sisabln, 0, 1)) {
        $sisabln = 0;
    }

    $akumulasiBulanan = $bar['bulanan'] * $selisih['months_total'];
    if ($bar['hargaperolehan'] < $akumulasiBulanan) {
        $akumulasiBulanan = $bar['hargaperolehan'];
    }

    $nilai = $bar['hargaperolehan'] - $akumulasiBulanan;
    if ('0' < $bar['persendecline']) {
        $thnawal = substr($bar['awalpenyusutan'], 0, 4);
        $blnawal = substr($bar['awalpenyusutan'], 5, 2);
        $total = $thnawal * 12 + $blnawal;
        $thnNow = substr($kdAst, 0, 4);
        $blnNow = substr($kdAst, 5, 2);
        $totalNow = $thnNow * 12 + $blnNow + 1;
        $selisihNow = $totalNow - $total;
        $sekarang = 0;
        $out = 0;
        $akumNow = 0;
        $before = $sekarang = $bar['hargaperolehan'];
        $jmlTahun = floor($selisihNow / 12);
        $sisaBulan = $selisihNow % 12;
        if (0 < $jmlTahun) {
            for ($i = 0; $i < $jmlTahun; ++$i) {
                $akumNow += ($sekarang * $bar['persendecline']) / 100;
                $sekarang -= ($sekarang * $bar['persendecline']) / 100;
            }
        }

        $out = $sekarang / 12;
        if ($bar['jlhblnpenyusutan'] == $selisihNow) {
            $akumNow += $sekarang;
            $sekarang = 0;
        } else {
            if (0 < $sisaBulan) {
                $akumNow += $sisaBulan * $out;
                $sekarang -= $sisaBulan * $out;
            }
        }

        $akumulasiBulanan = $akumNow;
        $nilai = $sekarang;
        $bar['bulanan'] = $out;
    }

    $data .= '<td align=right>'.number_format($bar['hargaperolehan'], 2).'</td>';
    $data .= '<td align=right>'.$bar['jlhblnpenyusutan'].'</td>';
    $data .= '<td align=right>'.$selisih['months_total'].'</td>';
    $data .= '<td align=right>'.number_format($sisabln, 2).'</td>';
    $data .= '<td align=right>'.number_format($akumulasiBulanan, 2).'</td>';
    $data .= '<td align=right>'.number_format($nilai, 2).'</td>';
    $data .= '<td>'.$bar['keterangan'].'</td>';
    $data .= '<td>'.$bar['awalpenyusutan'].'</td>';
    $data .= '<td align=right>'.number_format($bar['bulanan'], 2).'</td>';
    $data .= '<td align=right>'.number_format($bar['persendecline'], 2).'</td>';
    $data .= '<td align=right>';
    ((0 == $bar['leasing'] ? ($data .= 'Not Leasing') : 1 == $bar['leasing']) ? ($data .= 'Leasing') : ($data .= 'Ex Leasing'));
    $data .= '</td>';
    $data .= '</tr>';
    $totHarga += $bar['hargaperolehan'];
    $totHargaAkumul += $akumulasiBulanan;
    $totNilai += $nilai;
    $bulanan += $bar['bulanan'];
    $tpengurang += $bar['pengurang'];
    $tpenambah += $bar['penambah'];
}
$data .= '<tr><td colspan=6>'.$_SESSION['lang']['total'].'</td>';
$data .= '<td align=right>'.number_format($totHarga, 2).'</td>';
$data .= '<td align=right>'.number_format($tpenambah, 2).'</td>';
$data .= '<td align=right>'.number_format($tpengurang, 2).'</td>';
$data .= '<td>&nbsp;</td>';
$data .= '<td align=right>'.number_format($totHargaAkumul, 2).'</td>';
$data .= '<td align=right>'.number_format($totNilai, 2).'</td>';
$data .= '<td colspan=2>&nbsp;</td>';
$data .= '<td align=right></td></tr>';
$data .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        if ('' == $kdOrg) {
            echo 'warning : Organization code is obligatory';
            exit();
        }

        if ('' == $kdAst) {
            echo 'warning : Asset type is obligatory';
            exit();
        }

        echo $data;

        break;
    case 'excel':
        if ('' == $kdOrg) {
            echo 'warning : Organization code is obligatory';
            exit();
        }

        if ('' == $kdAst) {
            echo 'warning : Asset type is obligatory';
            exit();
        }

        $data .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_Daftar_Asset_'.$tglSkrg;
        if (0 < strlen($data)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $data)) {
                echo "<script language=javascript1.2>\r\n                                parent.window.alert('Can't convert to excel format');\r\n                                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                                window.location='tempExcel/".$nop_.".xls';\r\n                                </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        if ('' == $kdOrg) {
            echo 'warning : Organization code is obligatory';
            exit();
        }

        if ('' == $kdAst) {
            echo 'warning : Asset type is obligatory';
            exit();
        }

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
        global $nmOrg;
        global $kdOrg;
        global $kdAst;
        global $nmAst;
        global $thnPer;
        global $nmAsst;
        global $namakar;
        global $selisih;
        global $where;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
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
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20 / 100 * $width - 5, $height, 'Asset List', '', 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(100 / 100 * $width - 5, $height, 'Printed By : '.$namakar[$_SESSION['standard']['userid']], '', 0, 'R');
        $this->Ln();
        $this->Cell(100 / 100 * $width - 5, $height, 'Date : '.date('d-m-Y'), '', 0, 'R');
        $this->Ln();
        $this->Cell(100 / 100 * $width - 5, $height, 'Time : '.date('h:i:s'), '', 0, 'R');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($width, $height, strtoupper('Asset List '.(string) $nmAst).' '.$_SESSION['lang']['periode'].':'.$kdAst, '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper((string) $nmOrg), '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(2 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['kodeorganisasi'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['kodeasset'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['thnperolehan'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['namaasset'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['hargaperolehan'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['jumlahbulanpenyusutan'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['usia'].' ('.$_SESSION['lang']['bulan'].')', 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['sisa'].' ('.$_SESSION['lang']['bulan'].')', 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['akumulasipenyusutan'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['nilaibuku'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['awalpenyusutan'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['bulanan'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('L', 'pt', 'Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 20;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $no = 0;
        $sql = 'select * from '.$dbname.'.sdm_daftarasset where kodeorg in (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$kdOrg."') ".$where.' order by kodeasset';
        $qDet = mysql_query($sql);
        while ($res = mysql_fetch_assoc($qDet)) {
            ++$no;
            $tgl1 = $res['awalpenyusutan'].'-01';
            $tgl2 = $kdAst.'-02';
            $selisih = datediff($tgl1, $tgl2);
            if ($res['jlhblnpenyusutan'] < $selisih[months_total]) {
                $selisih[months_total] = $res['jlhblnpenyusutan'];
            }

            if ($tgl2 < $tgl1) {
                $selisih[months_total] = 0;
            }

            $sisabln = $res['jlhblnpenyusutan'] - $selisih[months_total];
            if ('-' == substr($sisabln, 0, 1)) {
                $sisabln = 0;
            }

            $akumulasiBulanan = $res['bulanan'] * $selisih[months_total];
            if ($res['hargaperolehan'] < $akumulasiBulanan) {
                $akumulasiBulanan = $res['hargaperolehan'];
            }

            $nilai = $res['hargaperolehan'] - $akumulasiBulanan;
            $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(7 / 100 * $width, $height, $res['kodeorg'], 1, 0, 'L', 1);
            $pdf->Cell(7 / 100 * $width, $height, $res['kodeasset'], 1, 0, 'L', 1);
            $pdf->Cell(7 / 100 * $width, $height, $res['tahunperolehan'], 1, 0, 'R', 1);
            $pdf->Cell(15 / 100 * $width, $height, $res['namasset'], 1, 0, 'L', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($res['hargaperolehan'], 2), 1, 0, 'R', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($res['jlhblnpenyusutan'], 2), 1, 0, 'R', 1);
            $pdf->Cell(6 / 100 * $width, $height, $selisih[months_total], 1, 0, 'C', 1);
            $pdf->Cell(6 / 100 * $width, $height, $sisabln, 1, 0, 'C', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($akumulasiBulanan, 2), 1, 0, 'C', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($nilai, 2), 1, 0, 'C', 1);
            $pdf->Cell(9 / 100 * $width, $height, $res['awalpenyusutan'], 1, 0, 'L', 1);
            $pdf->Cell(6 / 100 * $width, $height, number_format($res['bulanan'], 2), 1, 1, 'R', 1);
            $totHarga += $res['hargaperolehan'];
            $totHargaAkumul += $akumulasiBulanan;
            $totNilai += $nilai;
            $bulanan += $res['bulanan'];
        }
        $pdf->Cell(38 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, number_format($totHarga, 2), 1, 0, 'R', 1);
        $pdf->Cell(21 / 100 * $width, $height, '', 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, number_format($totHargaAkumul, 2), 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, number_format($totNilai, 2), 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, '', 1, 0, 'R', 1);
        $pdf->Cell(6 / 100 * $width, $height, number_format($bulanan, 2), 1, 0, 'R', 1);
        $pdf->Output();

        break;
}
function datediff($tgl1, $tgl2)
{
    $tgl1 = strtotime($tgl1);
    $tgl2 = strtotime($tgl2);
    $diff_secs = abs($tgl1 - $tgl2);
    $base_year = min(date('Y', $tgl1), date('Y', $tgl2));
    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);

    return ['years' => date('Y', $diff) - $base_year, 'months_total' => (date('Y', $diff) - $base_year) * 12 + date('n', $diff), 'months' => date('n', $diff) - 1, 'days_total' => floor($diff_secs / (3600 * 24)), 'days' => date('j', $diff) - 1, 'hours_total' => floor($diff_secs / 3600), 'hours' => date('G', $diff), 'minutes_total' => floor($diff_secs / 60), 'minutes' => (int) date('i', $diff), 'seconds_total' => $diff_secs, 'seconds' => (int) date('s', $diff)];
}

?>