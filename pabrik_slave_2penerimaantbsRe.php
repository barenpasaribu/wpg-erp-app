<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

('' == $_POST['tipeIntexRe'] ? ($tipeIntex = $_GET['tipeIntexRe']) : ($tipeIntex = $_POST['tipeIntexRe']));
('' == $_POST['unitRe'] ? ($unit = $_GET['unitRe']) : ($unit = $_POST['unitRe']));
('' == $_POST['tglRe'] ? ($tgl_1 = $_GET['tglRe']) : ($tgl_1 = $_POST['tglRe']));
('' == $_POST['tglRe'] ? ($tanggl = $_GET['tglRe']) : ($tanggl = $_POST['tglRe']));
('' == $_POST['kdPabrikRe'] ? ($kdPabrik = $_GET['kdPabrikRe']) : ($kdPabrik = $_POST['kdPabrikRe']));
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$XA = tanggalsystem($tanggl);
$thnA = substr($XA, 0, 4);
$blnA = substr($XA, 4, 2);
$tglA = substr($XA, 6, 2);
if ('getKodeorg' != $proses) {
    for ($x = 7; 0 <= $x; --$x) {
        $tm = mktime(0, 0, 0, $blnA, $tglA - $x, $thnA);
        $TGL[] = date('Y-m-d', $tm);
        if (7 == $x) {
            $listTGL = "'".date('Y-m-d', $tm)."'";
        } else {
            $listTGL .= ",'".date('Y-m-d', $tm)."'";
        }
    }
    $kebunPlasma = ['H11E', 'H12E', 'H13E', 'H14E', 'H15E', 'H16E', 'H17E', 'H18E', 'H19E', 'H20E', 'H21E', 'H22E', 'H23E', 'H24E', 'H25E', 'H26E', 'H30E', 'H31E'];
    $str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($kdPabrik, 0, 4)."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $ptPks = $bar->induk;
    }
    $str = 'select kodeorganisasi from '.$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi not in('H11E','H12E','H13E','H14E','H15E','H16E','H17E','H18E','H19E','H20E','H21E','H22E','H23E','H24E','H25E','H26E','H30E','H31E')";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $kebunInternal[] = $bar->kodeorganisasi;
    }
    $kebunAffiliasi = [];
    if ('SULAWESI' != $_SESSION['empl']['regional']) {
        $str = 'select kodeorganisasi from '.$dbname.".organisasi where induk!='".substr($ptPks, 0, 3)."' and tipe='KEBUN'\r\n              and kodeorganisasi not in('H11E','H12E','H13E','H14E','H15E','H16E','H17E','H18E','H19E','H20E','H21E','H22E','H23E','H24E','H25E','H26E','H30E','H31E')";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $kebunAffiliasi[] = $bar->kodeorganisasi;
        }
    }

    $str = 'select distinct kodecustomer from '.$dbname.".pabrik_timbangan where (kodeorg is null or kodeorg='') \r\n           and left(tanggal,10) in (".$listTGL.") and kodebarang='40000003'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $costomer[] = $bar->kodecustomer;
    }
    $str1 = "select kodeorg,left(tanggal,10) as tanggal,substr(nospb,9,6) as afd, sum(beratbersih-kgpotsortasi) as netto\r\n          from ".$dbname.".pabrik_timbangan where millcode='".$kdPabrik."' and length(kodeorg)=4 and\r\n          left(tanggal,10) in (".$listTGL.") and kodebarang='40000003' group by  left(tanggal,10),substr(nospb,9,6)\r\n          order by kodeorg, substr(nospb,9,6),left(tanggal,10) ";
    $resInternal = mysql_query($str1);
    while ($bar = mysql_fetch_object($resInternal)) {
        $tbsInt[$bar->kodeorg][$bar->afd][$bar->tanggal] = $bar->netto;
    }
    $str1 = "select kodeorg,left(tanggal,10) as tanggal,substr(nospb,9,6) as afd, sum(beratbersih-kgpotsortasi) as netto\r\n          from ".$dbname.".pabrik_timbangan where millcode='".$kdPabrik."' and length(kodeorg)=4 and\r\n          left(tanggal,10) between '".$thnA.'-'.$blnA."-01' and '".$thnA.'-'.$blnA.'-'.$tglA."' and kodebarang='40000003' group by  left(tanggal,10),substr(nospb,9,6)\r\n          order by kodeorg, substr(nospb,9,6),left(tanggal,10) ";
    $resInternal = mysql_query($str1);
    while ($bar = mysql_fetch_object($resInternal)) {
        $tottbsInt[$bar->kodeorg][$bar->afd] += $bar->netto;
    }
    $str2 = "select kodecustomer,left(tanggal,10) as tanggal, sum(beratbersih-kgpotsortasi) as netto\r\n          from ".$dbname.".pabrik_timbangan where millcode='".$kdPabrik."' and (kodeorg is null or kodeorg='') and\r\n          left(tanggal,10) in (".$listTGL.") and kodebarang='40000003' group by  left(tanggal,10),kodecustomer\r\n          order by left(tanggal,10),kodecustomer";
    $resExternal = mysql_query($str2);
    while ($bar = mysql_fetch_object($resExternal)) {
        $tbsExt[$bar->kodecustomer][$bar->tanggal] = $bar->netto;
    }
    $str2 = "select kodecustomer,left(tanggal,10) as tanggal, sum(beratbersih-kgpotsortasi) as netto\r\n          from ".$dbname.".pabrik_timbangan where millcode='".$kdPabrik."' and (kodeorg is null or kodeorg='') and\r\n          left(tanggal,10) between '".$thnA.'-'.$blnA."-01' and '".$thnA.'-'.$blnA.'-'.$tglA."' and kodebarang='40000003' group by  left(tanggal,10),kodecustomer\r\n          order by left(tanggal,10),kodecustomer";
    $resExternal = mysql_query($str2);
    while ($bar = mysql_fetch_object($resExternal)) {
        $tottbsExt[$bar->kodecustomer] += $bar->netto;
    }
    if ('preview' == $proses) {
        $border = 0;
    } else {
        $border = 1;
    }

    if ('EN' == $_SESSION['language']) {
        $stream = 'FFB Receive month '.$blnA.' to '.tanggalnormal($TGL[count($TGL) - 1]).' PKS:'.$kdPabrik." <br>\r\n                 Netto not include deduction";
    } else {
        $stream = 'Penerimaan TBS  Bulan '.$blnA.' s/d Tanggal '.tanggalnormal($TGL[count($TGL) - 1]).' PKS:'.$kdPabrik." <br>\r\n                 Berat yang tampil adalah berat bersih (sudah dikurangi potongan sortasi)";
    }

    $stream .= ' <table class=sortable cellspacing=1 border='.$border.'><thead><tr class=rowheader><td colspan=3 rowspan=2 align=center>SUPPLIER</td>     <td colspan=9 align=center>TANGGAL</td></tr><tr>';
    foreach ($TGL as $key => $tg) {
        $stream .= '<td width=50px align=center>'.substr($tg, 8, 2).'</td>';
    }
    $stream .= '<td>'.$_SESSION['lang']['total']."</td></tr>\r\n                    </thead>\r\n                  <tbody>";
    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2 bgcolor=#dedede>A.Internal</td><td colspan=10 bgcolor=#dedede></td></tr>";
    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td bgcolor=#dedede></td><td  colspan=2 bgcolor=#dedede>A.1. Inti</td><td colspan=9 bgcolor=#dedede></td></tr>";
    foreach ($kebunInternal as $key => $kodekebun) {
        if (isset($ttang)) {
            unset($ttang);
        }

        $no = 0;
        if (isset($tbsInt[$kodekebun])) {
            foreach ($tbsInt[$kodekebun] as $afd => $art) {
                ++$no;
                $stream .= '<tr class=rowcontent>';
//                $stream .= '<td></td><td>'.$no.'</td><td>'.$optNm[$afd].'</td>';
                $stream .= '<td></td><td>'.$no.'</td><td>'.$afd.'</td>';
                $tt = 0;
                foreach ($TGL as $kei => $tang) {
                    $bgwarna = '';
                    $scek = 'select distinct * from '.$dbname.".kebun_spb_vw where\r\n                           left(blok,6)='".$afd."' and tanggal='".$tang."' \r\n                           and substr(nospb,9,6)<>left(blok,6)";
                    $qcek = mysql_query($scek);
                    $rcek = mysql_num_rows($qcek);
                    if (1 == $rcek) {
                        $bgwarna = 'bgcolor=yellow';
                    }

                    $stream .= '<td align=right '.$bgwarna.'>'.number_format($art[$tang]).'</td>';
                    $tt += $art[$tang];
                    $ttang[$tang] += $art[$tang];
                }
                $stream .= '<td align=right>'.number_format($tottbsInt[$kodekebun][$afd]).'</td></tr>';
                $tkebun[$kodekebun] += $tottbsInt[$kodekebun][$afd];
            }
            $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2></td><td bgcolor=#dedede>Total ".$kodekebun.'</td>';
            foreach ($ttang as $keei => $jum) {
                $stream .= '<td align=right bgcolor=#dedede>'.number_format($jum).'</td>';
                $tinti[$keei] += $jum;
            }
            $stream .= '<td align=right bgcolor=#dedede>'.number_format($tkebun[$kodekebun]).'</td></tr>';
            $ttinti += $tkebun[$kodekebun];
        }
    }
    if (!empty($tinti)) {
        foreach ($tinti as $keei => $jum) {
        }
        $ttinternal += $ttinti;
    }

/*    foreach ($kebunAffiliasi as $key => $kodekebun) {
        if (isset($ttang)) {
            unset($ttang);
        }

        $no = 0;
        if (isset($tbsInt[$kodekebun])) {
            foreach ($tbsInt[$kodekebun] as $afd => $art) {
                ++$no;
                $stream .= '<tr class=rowcontent>';
                $stream .= '<td></td><td>'.$no.'</td><td>'.$optNm[$afd].'</td>';
                $tt = 0;
                foreach ($TGL as $kei => $tang) {
                    $bgwarna = '';
                    $scek = 'select distinct * from '.$dbname.".kebun_spb_vw where\r\n                           left(blok,6)='".$afd."' and tanggal='".$tang."' \r\n                           and substr(nospb,9,6)<>left(blok,6)";
                    $qcek = mysql_query($scek);
                    $rcek = mysql_num_rows($qcek);
                    if (1 == $rcek) {
                        $bgwarna = 'bgcolor=yellow';
                    }

                    $stream .= '<td align=right '.$bgwarna.'>'.number_format($art[$tang]).'</td>';
                    $tt += $art[$tang];
                    $ttang[$tang] += $art[$tang];
                }
                $stream .= '<td align=right>'.number_format($tottbsInt[$kodekebun][$afd]).'</td></tr>';
                $tkebun[$kodekebun] += $tottbsInt[$kodekebun][$afd];
            }
            $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2></td><td bgcolor=#dedede>Total ".$kodekebun.'</td>';
            foreach ($ttang as $keei => $jum) {
                $stream .= '<td align=right bgcolor=#dedede>'.number_format($jum).'</td>';
                $tafiliasi[$keei] += $jum;
            }
            $stream .= '<td align=right bgcolor=#dedede>'.number_format($tkebun[$kodekebun]).'</td></tr>';
            $ttafiliasi += $tkebun[$kodekebun];
        }
    }
    if (!empty($tafiliasi)) {
        foreach ($tafiliasi as $keei => $jum) {
            $stream .= '<td align=right bgcolor=#dedede>'.number_format($jum).'</td>';
        }
        $stream .= '<td align=right bgcolor=#dedede>'.number_format($ttafiliasi).'</td></tr>';
        $ttinternal += $ttafiliasi;
    }

    foreach ($kebunPlasma as $key => $kodekebun) {
        if (isset($ttang)) {
            unset($ttang);
        }

        $no = 0;
        if (isset($tbsInt[$kodekebun])) {
            foreach ($tbsInt[$kodekebun] as $afd => $art) {
                ++$no;
                $stream .= '<tr class=rowcontent>';
                $stream .= '<td></td><td>'.$no.'</td><td>'.$optNm[$afd].'</td>';
                $tt = 0;
                foreach ($TGL as $kei => $tang) {
                    $bgwarna = '';
                    $scek = 'select distinct * from '.$dbname.".kebun_spb_vw where\r\n                       left(blok,6)='".$afd."' and tanggal='".$tang."' \r\n                       and substr(nospb,9,6)<>left(blok,6)";
                    $qcek = mysql_query($scek);
                    $rcek = mysql_num_rows($qcek);
                    if (1 == $rcek) {
                        $bgwarna = "bgcolor=yellow title='Ada Buah Dari Afdeling Lain'";
                    }

                    $stream .= '<td align=right '.$bgwarna.'>'.number_format($art[$tang]).'</td>';
                    $tt += $art[$tang];
                    $ttang[$tang] += $art[$tang];
                }
                $stream .= '<td align=right>'.number_format($tottbsInt[$kodekebun][$afd]).'</td></tr>';
                $tkebun[$kodekebun] += $tottbsInt[$kodekebun][$afd];
            }
            $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2></td><td bgcolor=#dedede>Total ".$kodekebun.'</td>';
            foreach ($ttang as $keei => $jum) {
                $stream .= '<td align=right bgcolor=#dedede>'.number_format($jum).'</td>';
                $tplasma[$keei] += $jum;
            }
            $stream .= '<td align=right bgcolor=#dedede>'.number_format($tkebun[$kodekebun]).'</td></tr>';
            $ttplasma += $tkebun[$kodekebun];
        }
    }
    if (!empty($tplasma)) {
        $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td></td><td colspan=2 bgcolor=#dedede>Total Plasma</td>";
        foreach ($tplasma as $keei => $jum) {
            $stream .= '<td align=right bgcolor=#dedede>'.number_format($jum).'</td>';
        }
        $stream .= '<td align=right bgcolor=#dedede>'.number_format($ttplasma).'</td></tr>';
        $ttinternal += $ttplasma;
    }
*/
    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=3 bgcolor=#dedede>Total Internal (A)</td>";
    foreach ($TGL as $key => $tg) {
        $stream .= '<td align=right bgcolor=#dedede>'.number_format($tinti[$tg] + $tafiliasi[$tg] + $tplasma[$tg]).'</td>';
        $tinternal[$tg] += $tinti[$tg] + $tafiliasi[$tg] + $tplasma[$tg];
    }
    $stream .= '<td align=right bgcolor=#dedede>'.number_format($ttinternal).'</td></tr>';
    $gtt += $ttinternal;
    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2 bgcolor=#dedede>B.External</td><td colspan=10 bgcolor=#dedede></td></tr>";
    $no = 0;
    if (!empty($tbsExt)) {
        foreach ($tbsExt as $suppid => $art) {
            ++$no;
            $stream .= '<tr class=rowcontent>';
            $stream .= '<td></td><td>'.$no.'</td><td>'.$optSupp[$suppid].'</td>';
//            $stream .= '<td></td><td>'.$no.'</td><td>'.$suppid.'</td>';
            $tt = 0;
            foreach ($TGL as $kei => $tang) {
                $stream .= '<td align=right>'.number_format($art[$tang]).'</td>';
                $tt += $art[$tang];
                $tExt[$tang] += $art[$tang];
            }
            $stream .= '<td align=right>'.number_format($tottbsExt[$suppid]).'</td></tr>';
            $ttExt += $tottbsExt[$suppid];
        }
    }

    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=3 bgcolor=#dedede>Total External (B)</td>";
    foreach ($TGL as $key => $tg) {
        $stream .= '<td align=right bgcolor=#dedede>'.number_format($tExt[$tg]).'</td>';
    }
    $stream .= '<td align=right bgcolor=#dedede>'.number_format($ttExt).'</td></tr>';
    $gtt += $ttExt;
    $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=3 bgcolor=#dedede>Grand Total (A+B)</td>";
    foreach ($TGL as $key => $tg) {
        $stream .= '<td align=right bgcolor=#dedede>'.number_format($tExt[$tg] + $tinternal[$tg]).'</td>';
    }
    $stream .= '<td align=right bgcolor=#dedede>'.number_format($gtt).'</td></tr>';
    $stream .= '</tbody><tfoot></tfoot></table>';
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $qwe = date('YmdHms');
        $nop_ = 'Laporan_penerimaan_TBS_Tanggal '.tanggalnormal($TGL[0]).' sd Tanggal '.tanggalnormal($TGL[count($TGL) - 1]).'PKS'.$kdPabrik._.$qwe;
        if (0 < strlen($stream)) {
            $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
            gzwrite($gztralala, $stream);
            gzclose($gztralala);
            echo "<script language=javascript1.2>\r\n                                window.location='tempExcel/".$nop_.".xls.gz';\r\n                                </script>";
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
        global $tipeIntex;
        global $periode;
        global $unit;
        global $kdPabrik;
        global $tgl_2;
        global $tgl_1;
        global $tglPeriode;
        global $TGL;
        global $optSupp;
        global $optNm;
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qAlamat = mysql_query($sAlmat);
        $rAlamat = mysql_fetch_assoc($qAlamat);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 11;
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
        $this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$rAlamat['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($width, $height, $_SESSION['lang']['rePenerimaanTbs'], 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln(5);
        if ('EN' == $_SESSION['language']) {
            $this->Cell($width, $height, 'FFB receive month '.$blnA.' to '.tanggalnormal($TGL[count($TGL) - 1]).' PKS:'.$kdPabrik.' (not include grading deduction)', 0, 1, 'C');
        } else {
            $this->Cell($width, $height, 'Penerimaan TBS  Bulan '.$blnA.' s/d Tanggal '.tanggalnormal($TGL[count($TGL) - 1]).' PKS:'.$kdPabrik.' (belum dikurangi potongan sortasi)', 0, 1, 'C');
        }

        $this->Ln(10);
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
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', '', 7);
        $totPdf = 0;
        $nor = 0;
        $no = 0;
        $coldt = count($TGL) + 1;
        $coldt = 9 * $coldt;
        $pdf->Cell(20 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        foreach ($TGL as $key => $tg) {
            $pdf->Cell(9 / 100 * $width, $height, substr($tg, 8, 2), 1, 0, 'C', 1);
        }
        $pdf->Cell(9 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
        $pdf->Cell(20 / 100 * $width, $height, 'A.Internal', 1, 0, 'L', 1);
        $pdf->Cell($coldt / 100 * $width, $height, ' ', 1, 1, 'L', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
        $pdf->Cell(15 / 100 * $width, $height, 'A.1. Inti', 1, 0, 'L', 1);
        $pdf->Cell($coldt / 100 * $width, $height, ' ', 1, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 6);
        foreach ($kebunInternal as $key => $kodekebun) {
            if (isset($ttang)) {
                unset($ttang);
            }

            $pdf->SetFillColor(255, 255, 255);
            $no = 0;
            if (isset($tbsInt[$kodekebun])) {
                foreach ($tbsInt[$kodekebun] as $afd => $art) {
                    ++$no;
                    $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                    $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'L', 1);
                    $pdf->Cell(13 / 100 * $width, $height, $optNm[$afd], 1, 0, 'L', 1);
                    $tt = 0;
                    foreach ($TGL as $kei => $tang) {
                        $pdf->Cell(9 / 100 * $width, $height, number_format($art[$tang]), 1, 0, 'R', 1);
                        $ttang[$tang] += $art[$tang];
                    }
                    $pdf->Cell(9 / 100 * $width, $height, number_format($tottbsInt[$kodekebun][$afd]), 1, 1, 'R', 1);
                }
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                $pdf->Cell(13 / 100 * $width, $height, 'Total '.$kodekebun, 1, 0, 'L', 1);
                foreach ($ttang as $keei => $jum) {
                    $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
                }
                $pdf->Cell(9 / 100 * $width, $height, number_format($tkebun[$kodekebun]), 1, 1, 'R', 1);
            }
        }
        if (!empty($tinti)) {
            $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
            $pdf->Cell(13 / 100 * $width, $height, 'Total Inti', 1, 0, 'L', 1);
            foreach ($tinti as $keei => $jum) {
                $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
            }
            $pdf->Cell(9 / 100 * $width, $height, number_format($ttinti), 1, 1, 'R', 1);
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
        $pdf->Cell(15 / 100 * $width, $height, 'A.2. Afiliasi', 1, 0, 'L', 1);
        $pdf->Cell($coldt / 100 * $width, $height, ' ', 1, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 6);
        foreach ($kebunAffiliasi as $key => $kodekebun) {
            if (isset($ttang)) {
                unset($ttang);
            }

            $pdf->SetFillColor(255, 255, 255);
            $no = 0;
            if (isset($tbsInt[$kodekebun])) {
                foreach ($tbsInt[$kodekebun] as $afd => $art) {
                    ++$no;
                    $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                    $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'L', 1);
                    $pdf->Cell(13 / 100 * $width, $height, $optNm[$afd], 1, 0, 'L', 1);
                    $tt = 0;
                    foreach ($TGL as $kei => $tang) {
                        $pdf->Cell(9 / 100 * $width, $height, number_format($art[$tang]), 1, 0, 'R', 1);
                        $ttang[$tang] += $art[$tang];
                    }
                    $pdf->Cell(9 / 100 * $width, $height, number_format($tottbsInt[$kodekebun][$afd]), 1, 1, 'R', 1);
                }
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                $pdf->Cell(13 / 100 * $width, $height, 'Total '.$kodekebun, 1, 0, 'L', 1);
                foreach ($ttang as $keei => $jum) {
                    $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
                }
                $pdf->Cell(9 / 100 * $width, $height, number_format($tkebun[$kodekebun]), 1, 1, 'R', 1);
            }
        }
        if (!empty($tafiliasi)) {
            $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
            $pdf->Cell(13 / 100 * $width, $height, 'Total Afiliasi', 1, 0, 'L', 1);
            foreach ($tafiliasi as $keei => $jum) {
                $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
            }
            $pdf->Cell(9 / 100 * $width, $height, number_format($ttafiliasi), 1, 1, 'R', 1);
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
        $pdf->Cell(15 / 100 * $width, $height, 'A.3. Plasma', 1, 0, 'L', 1);
        $pdf->Cell($coldt / 100 * $width, $height, ' ', 1, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 6);
        foreach ($kebunPlasma as $key => $kodekebun) {
            if (isset($ttang)) {
                unset($ttang);
            }

            $pdf->SetFillColor(255, 255, 255);
            $no = 0;
            if (isset($tbsInt[$kodekebun])) {
                foreach ($tbsInt[$kodekebun] as $afd => $art) {
                    ++$no;
                    $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                    $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'L', 1);
                    $pdf->Cell(13 / 100 * $width, $height, $optNm[$afd], 1, 0, 'L', 1);
                    $tt = 0;
                    foreach ($TGL as $kei => $tang) {
                        $pdf->Cell(9 / 100 * $width, $height, number_format($art[$tang]), 1, 0, 'R', 1);
                        $ttang[$tang] += $art[$tang];
                    }
                    $pdf->Cell(9 / 100 * $width, $height, number_format($tottbsInt[$kodekebun][$afd]), 1, 1, 'R', 1);
                }
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                $pdf->Cell(13 / 100 * $width, $height, 'Total '.$kodekebun, 1, 0, 'L', 1);
                foreach ($ttang as $keei => $jum) {
                    $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
                }
                $pdf->Cell(9 / 100 * $width, $height, number_format($tkebun[$kodekebun]), 1, 1, 'R', 1);
            }
        }
        if (!empty($tplasma)) {
            $pdf->Cell(7 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
            $pdf->Cell(13 / 100 * $width, $height, 'Total Afiliasi', 1, 0, 'L', 1);
            foreach ($tplasma as $keei => $jum) {
                $pdf->Cell(9 / 100 * $width, $height, number_format($jum), 1, 0, 'R', 1);
            }
            $pdf->Cell(9 / 100 * $width, $height, number_format($ttplasma), 1, 1, 'R', 1);
        }

        $pdf->Cell(20 / 100 * $width, $height, 'Total Internal (A)', 1, 0, 'L', 1);
        foreach ($TGL as $key => $tg) {
            $pdf->Cell(9 / 100 * $width, $height, number_format($tinti[$tg] + $tafiliasi[$tg] + $tplasma[$tg]), 1, 0, 'R', 1);
        }
        $pdf->Cell(9 / 100 * $width, $height, number_format($ttinternal), 1, 1, 'R', 1);
        $stream .= "<tr class=rowcontent style='font-weight:bolder;'><td colspan=2 bgcolor=#dedede>B.External</td><td colspan=10 bgcolor=#dedede></td></tr>";
        $no = 0;
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(20 / 100 * $width, $height, 'B.External', 1, 0, 'L', 1);
        $pdf->Cell($coldt / 100 * $width, $height, ' ', 1, 1, 'L', 1);
        $pdf->SetFont('Arial', '', 6);
        if (!empty($tbsExt)) {
            $pdf->SetFillColor(255, 255, 255);
            foreach ($tbsExt as $suppid => $art) {
                ++$no;
                $pdf->Cell(5 / 100 * $width, $height, ' ', 1, 0, 'L', 1);
                $pdf->Cell(2 / 100 * $width, $height, $no, 1, 0, 'L', 1);
                $pdf->Cell(13 / 100 * $width, $height, $optSupp[$suppid], 1, 0, 'L', 1);
                $tt = 0;
                foreach ($TGL as $kei => $tang) {
                    $pdf->Cell(9 / 100 * $width, $height, number_format($art[$tang]), 1, 0, 'R', 1);
                }
                $pdf->Cell(9 / 100 * $width, $height, number_format($tottbsExt[$suppid]), 1, 1, 'R', 1);
            }
        }

        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(20 / 100 * $width, $height, 'Total External (B)', 1, 0, 'L', 1);
        foreach ($TGL as $key => $tg) {
            $pdf->Cell(9 / 100 * $width, $height, number_format($tExt[$tg]), 1, 0, 'R', 1);
        }
        $pdf->Cell(9 / 100 * $width, $height, number_format($ttExt), 1, 1, 'R', 1);
        $pdf->Cell(20 / 100 * $width, $height, 'Grand Total (A+B)', 1, 0, 'L', 1);
        foreach ($TGL as $key => $tg) {
            $pdf->Cell(9 / 100 * $width, $height, number_format($tExt[$tg] + $tinternal[$tg]), 1, 0, 'R', 1);
        }
        $pdf->Cell(9 / 100 * $width, $height, number_format($gtt), 1, 1, 'R', 1);
        $pdf->Output();

        break;
    case 'getKodeorg':
        $pabrik = $_POST['kdPabrik'];
        $ptS = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
        $ptX = $ptS[$pabrik];
        $optorg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        if (2 == $tipeIntex) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk ='".$ptX."' order by namaorganisasi asc";
        } else {
            if (0 == $tipeIntex) {
                $sOrg = 'SELECT namasupplier,`kodetimbangan` FROM '.$dbname.".log_5supplier WHERE kodekelompok='S003' and kodetimbangan!='NULL' order by namasupplier asc";
            } else {
                if (1 == $tipeIntex) {
                    $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk <>'".$ptX."' order by namaorganisasi asc";
                }
            }
        }

        if (3 != $tipeIntex) {
            $qOrg = mysql_query($sOrg);
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                if (0 != $tipeIntex) {
                    $optorg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
                } else {
                    $optorg .= '<option value='.$rOrg['kodetimbangan'].'>'.$rOrg['namasupplier'].'</option>';
                }
            }
        }

        echo $optorg;

        break;
    default:
        break;
}

?>