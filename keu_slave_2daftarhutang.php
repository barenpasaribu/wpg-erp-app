<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
('' == $_POST['kdOrg'] ? ($kodeOrg = $_GET['kdOrg']) : ($kodeOrg = $_POST['kdOrg']));
('' == $_POST['tgl1'] ? ($tgl1 = tanggalsystem($_GET['tgl1'])) : ($tgl1 = tanggalsystem($_POST['tgl1'])));
('' == $_POST['tgl2'] ? ($tgl2 = tanggalsystem($_GET['tgl2'])) : ($tgl2 = tanggalsystem($_POST['tgl2'])));
('' == $_POST['statTagihan'] ? ($statTagihan = $_GET['statTagihan']) : ($statTagihan = $_POST['statTagihan']));
('' == $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
('' == $_POST['periode2'] ? ($periode2 = $_GET['periode2']) : ($periode2 = $_POST['periode2']));
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optMatauang = makeOption($dbname, 'log_poht', 'nopo,matauang');
if ('' == $periode || '' == $statTagihan || '' == $periode2) {
    exit('Error:Field Tidak Boleh Kosong');
}

if ('excel' == $_GET['proses']) {
    $bg = ' bgcolor=#DEDEDE';
    $brdr = 1;
    $tab .= "<table>\r\n             <tr><td colspan=5 align=left><font size=3>".strtoupper($_SESSION['lang']['daftarHutang'])."</font></td></tr> \r\n             <tr><td colspan=5 align=left>".$optNm[$kodeOrg]."</td></tr>   \r\n             <tr><td colspan=5>".$_SESSION['lang']['periode'].' '.$_SESSION['lang']['tagihan'].' '.$_SESSION['lang']['dari'].' '.$periode.' '.$_SESSION['lang']['sampai'].' '.$periode2."</td></tr>   \r\n             </table>";
} else {
    $bg = '';
    $brdr = 0;
}

if ('' != $kodeOrg) {
    $where5 = " and kodeorg = '".$kodeOrg."'";
    $where2 = " and kodeorg='".$kodeOrg."'";
}

if (1 == $statTagihan) {
    $where = " noinvoice\r\nIN (select distinct noinvoice from ".$dbname.'.aging_sch_vw where (dibayar!=0) '.$where2.')';
} else {
    if (0 == $statTagihan) {
        $where = " noinvoice \r\nIN (select distinct noinvoice from ".$dbname.'.aging_sch_vw where (dibayar is null or dibayar=0) '.$where2.')';
    }
}

if ('' != $periode || '' != $periode2) {
    if (strlen($periode) < 7) {
        if ($peridode == $periode2) {
            $where .= " and substring(a.tanggal,1,4) between  '".$periode."' and '".$periode2."' ";
        } else {
            exit('error:Harus Di Tahun Yang Sama');
        }
    } else {
        $where .= " and substring(a.tanggal,1,7)  between '".$periode."' and '".$periode2."'";
    }
}

$sByr = "select distinct sum(a.jumlah) as jumlah,b.tanggal,a.notransaksi,a.keterangan1\r\n                  from ".$dbname.". keu_kasbankdt a\r\n                  left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi \r\n                  INNER JOIN ".$dbname.".keu_tagihanht c ON a.keterangan1 = c.noinvoice\r\n                  where a.keterangan1!='' \r\n                  and a.tipetransaksi='K' and b.posting=1\r\n                  group by a.keterangan1,a.tipetransaksi";
$qByr = mysql_query($sByr);
while ($rByr = mysql_fetch_assoc($qByr)) {
    $penambah[$rByr['keterangan1']] = $rByr['jumlah'];
    $ntrKasBank[$rByr['keterangan1']] = $rByr['notransaksi'];
    $tglKasBank[$rByr['keterangan1']] = $rByr['tanggal'];
}
$sByr = 'select distinct sum(a.jumlah) as jumlah,a.keterangan1  from '.$dbname.". keu_kasbankdt a\r\n      left join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi \r\n      INNER JOIN ".$dbname.".keu_tagihanht c ON a.keterangan1 = c.noinvoice\r\n      where a.keterangan1!='' \r\n      and a.tipetransaksi='M'  and b.posting=1 group by a.keterangan1,a.tipetransaksi";
$qByr = mysql_query($sByr);
while ($rByr2 = mysql_fetch_assoc($qByr)) {
    $pengurang[$rByr2['keterangan1']] = $rByr2['jumlah'];
}
if ('pdf' !== $_GET['proses']) {
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brdr.' class=sortable><thead>';
    $tab .= '<tr class=rowheader>';
    $tab .= '<td align=center '.$bg.'>No.</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['pt'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['noinvoice'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['tagihan'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['notransaksi'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['dibayar'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['nopo'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['namasupplier'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['nilaiinvoice'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['terbayar'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['jatuhtempo'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['nofp'].'</td>';
    $tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['dokumen'].'</td>';
    $tab .= '</tr></thead><tbody>';
    $sKodeOrg = "SELECT a.`noinvoice`,a.`nopo`,a.`tanggal`,a.`tanggalposting`,a.kodesupplier,a.`jatuhtempo`,a.`nofp`,a.`keterangan`,\r\n                      sum((a.`nilaiinvoice`+a.`nilaippn`)-a.`uangmuka`) as nilvoice,a.`kodeorg`,posting\r\n            FROM      ".$dbname.'.`keu_tagihanht` a WHERE '.$where.' '.$where5.' group by a.noinvoice order by a.tanggal asc';
	#echo $sKodeOrg;
    $qKodeOrg = mysql_query($sKodeOrg);
    $brsCek = mysql_num_rows($qKodeOrg);
    if (0 !== $brsCek) {
        while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
            ++$no;
            $dibayarsmp[$rKode['noinvoice']] = $penambah[$rKode['noinvoice']] - $pengurang[$rKode['noinvoice']];
            if ('' == $rByr['tanggal']) {
                $rByr['tanggal'] = '0000-00-00';
            }

            if (1 == $statTagihan) {
                if (0 !== $dibayarsmp) {
                    $tab .= '<tr class=rowcontent>';
                    $tab .= '<td align=center>'.$no.'</td>';
                    $tab .= '<td align=left>'.$rKode['kodeorg'].'</td>';
                    $tab .= '<td align=left>'.$rKode['noinvoice'].'</td>';
                    $tab .= '<td align=center>'.tanggalnormal($rKode['tanggalposting']).'</td>';
                    $tab .= '<td align=left>'.$ntrKasBank[$rKode['noinvoice']].'</td>';
                    $tab .= '<td align=center>'.tanggalnormal($tglKasBank[$rKode['noinvoice']]).'</td>';
                    $tab .= '<td align=left>'.$rKode['nopo'].'</td>';
                    $tab .= '<td align=left>'.$optNmSupp[$rKode['kodesupplier']].'</td>';
                    $tab .= '<td align=right>'.number_format($rKode['nilvoice'], 2).'</td>';
                    $tab .= '<td align=right>'.number_format($dibayarsmp[$rKode['noinvoice']], 2).'</td>';
                    $tab .= '<td align=center>'.tanggalnormal($rKode['jatuhtempo']).'</td>';
                    $tab .= '<td align=left>'.$rKode['nofp'].'</td>';
                    if (0 == $rKode['posting']) {
                        $dok = $_SESSION['lang']['belumlengkap'];
                    }

                    if (1 == $rKode['posting']) {
                        $dok = $_SESSION['lang']['lengkap'];
                    }

                    $tab .= '<td align=left>'.$dok.'</td>';
                    $tab .= '</tr>';
                    $totInvoice += $rKode['nilvoice'];
                }
            } else {
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td align=center>'.$no.'</td>';
                $tab .= '<td align=left>'.$rKode['kodeorg'].'</td>';
                $tab .= '<td align=left>'.$rKode['noinvoice'].'</td>';
                $tab .= '<td align=center>'.tanggalnormal($rKode['tanggalposting']).'</td>';
                $tab .= '<td align=left>'.$ntrKasBank[$rKode['noinvoice']].'</td>';
                $tab .= '<td align=center>'.tanggalnormal($tglKasBank[$rKode['noinvoice']]).'</td>';
                $tab .= '<td align=left>'.$rKode['nopo'].'</td>';
                $tab .= '<td align=left>'.$optNmSupp[$rKode['kodesupplier']].'</td>';
                $tab .= '<td align=right>'.number_format($rKode['nilvoice'], 2).'</td>';
                $tab .= '<td align=right>'.number_format($dibayarsmp[$rKode['noinvoice']], 2).'</td>';
                $tab .= '<td align=center>'.tanggalnormal($rKode['jatuhtempo']).'</td>';
                $tab .= '<td align=left>'.$rKode['nofp'].'</td>';
                if (0 == $rKode['posting']) {
                    $dok = $_SESSION['lang']['belumlengkap'];
                }

                if (1 == $rKode['posting']) {
                    $dok = $_SESSION['lang']['lengkap'];
                }

                $tab .= '<td align=left>'.$dok.'</td>';
                $tab .= '</tr>';
                $totInvoice += $rKode['nilvoice'];
            }
        }
        $tab .= '</tbody><thead><tr class=rowheader>';
        $tab .= '<td  align=right colspan=8>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right >'.number_format($totInvoice, 2).'</td>';
        $tab .= '<td align=left colspan=7>'.kekata($totInvoice).'</td>';
        $tab .= '</tr>';
        $tab .= '</thead>';
    } else {
        $tab .= '<tr class=rowcontent align=center><td colspan=15>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
    }

    $tab .= '</table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        if ('' == $periode || '' == $statTagihan) {
            exit('Error:Field Tidak Boleh Kosong');
        }

        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHis');
        $nop_ = 'daftarHutang'.$dte;
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
                echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
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
        global $tgl1;
        global $where;
        global $tgl2;
        global $kodeOrg;
        global $statTagihan;
        global $dbname;
        global $optNm;
        global $optNmSupp;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
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
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['daftarHutang'], '', 0, 'L');
        $this->Ln();
        $this->Ln();
        if (4 < strlen($_GET['periode'])) {
            $periode = substr(tanggalnormal($_GET['periode']), 1, 7);
            $periode2 = substr(tanggalnormal($_GET['periode2']), 1, 7);
        } else {
            $periode = $_GET['periode'];
            $periode2 = $_GET['periode2'];
        }

        $this->Cell($width, $height, $_SESSION['lang']['periode'].' '.$_SESSION['lang']['tagihan'].' '.$_SESSION['lang']['dari'].' '.$periode.' '.$_SESSION['lang']['sampai'].' '.$periode2, '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['pt'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['noinvoice'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['tagihan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['dibayar'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['notransaksi'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['nopo'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['namasupplier'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['nilaiinvoice'], 1, 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['nilaippn'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['jatuhtempo'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['nofp'], 1, 1, 'C', 1);
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
        $pdf->SetFont('Arial', '', 6);
        $sKodeOrg = "SELECT a.`noinvoice`,a.`nopo`,a.`tanggal`,a.`tanggalposting`,a.kodesupplier,a.`nilaiinvoice`,a.`nilaippn`,a.`jatuhtempo`,a.`nofp`,a.`keterangan`,a.`matauang`,a.`uangmuka`,a.`kodeorg`\r\nFROM ".$dbname.'.`keu_tagihanht` a WHERE '.$where.' order by a.tanggal asc';
        $qKodeOrg = mysql_query($sKodeOrg);
        $brsCek = mysql_num_rows($qKodeOrg);
        if (0 !== $brsCek) {
            while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
                ++$no;
                $sTglByr = 'SELECT DISTINCT a.tanggal AS tglByr, a.notransaksi FROM '.$dbname.".`keu_kasbankht` a \r\n                        LEFT JOIN ".$dbname.".keu_kasbankdt b ON a.`notransaksi` = b.`notransaksi`WHERE `keterangan1` = '".$rKode['noinvoice']."'";
                $qTglByr = mysql_query($sTglByr);
                $rTglByr = mysql_fetch_assoc($qTglByr);
                if ('' == $rTglByr['tglByr']) {
                    $rTglByr['tglByr'] = '0000-00-00';
                }

                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(15 / 100 * $width, $height, $optNm[$rKode['kodeorg']], 1, 0, 'L', 1);
                $pdf->Cell(10 / 100 * $width, $height, $rKode['noinvoice'], 1, 0, 'L', 1);
                $pdf->Cell(7 / 100 * $width, $height, tanggalnormal($rKode['tanggalposting']), 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, tanggalnormal($tglKasBank[$rKode['noinvoice']]), 1, 0, 'C', 1);
                $pdf->Cell(8 / 100 * $width, $height, $ntrKasBank[$rKode['noinvoice']], 1, 0, 'L', 1);
                $pdf->Cell(10 / 100 * $width, $height, $rKode['nopo'], 1, 0, 'L', 1);
                $pdf->Cell(12 / 100 * $width, $height, $optNmSupp[$rKode['kodesupplier']], 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rKode['nilaiinvoice'], 2), 1, 0, 'R', 1);
                $pdf->Cell(5 / 100 * $width, $height, $rKode['nilaippn'], 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, tanggalnormal($rKode['jatuhtempo']), 1, 0, 'C', 1);
                $pdf->Cell(9 / 100 * $width, $height, $rKode['nofp'], 1, 1, 'L', 1);
                $totInvoice += $rKode['nilaiinvoice'];
            }
            $pdf->Cell(72 / 100 * $width, $height, $_SESSION['lang']['total'].': '.kekata($totInvoice), 1, 0, 'L', 1);
            $pdf->Cell(28 / 100 * $width, $height, number_format($totInvoice, 2), 1, 1, 'L', 1);
            $pdf->Output();

            break;
        }

        exit('Error: Data Kosong');
    default:
        break;
}

?>