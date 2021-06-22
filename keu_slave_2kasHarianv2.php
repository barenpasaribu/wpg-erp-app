<?php


require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';

include_once 'lib/biReport.php';

include_once 'lib/zPdfMaster.php';

include_once 'lib/terbilang.php';

$otNmAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

$param = $_POST;

$level = $_GET['level'];

if (isset($_GET['mode'])) {

    $mode = $_GET['mode'];

} else {

    $mode = 'preview';

}



if ('pdf' === $mode) {

    $param = $_GET;

    unset($param['mode'], $param['level']);

} else {

    $param = $_POST;

}



$periode1 = $param['periode_from'];

$periode2 = $param['periode_until'];

if ('getNoakun' !== $param['proses'] && ('' === $periode1 || '' === $periode2)) {

    echo 'Warning : Transaction period required';

    exit();

}



if (tanggalsystem($periode2) < tanggalsystem($periode1)) {

    $tmp = $periode1;

    $periode1 = $periode2;

    $periode2 = $tmp;

}



if ($param['noakunsmp'] < $param['noakun']) {

    $tmp = $param['noakunsmp'];

    $param['noakunsmp'] = $param['noakun'];

    $param['noakun'] = $tmp;

}



if ('' === $param['noakunsmp']) {

    $param['noakunsmp'] = $param['noakun'];

}



switch ($level) {

    case '0':

        if (isset($param['kodeorg'])) {

            $kodeorg = $param['kodeorg'];

        } else {

            $kodeorg = $_SESSION['empl']['lokasitugas'];

        }


        $persbl = substr(tanggalsystem($periode1), 0, 8);

        $thnawl = substr($persbl, 0, 4);
        $blnawl = substr($persbl, 4, 2);
        $tglawl = substr($persbl, 6, 2);

        $quer = "SELECT a.*, b.tanggal as tanggaltrans FROM keu_kasbankdt a inner join keu_kasbankht b ON a.notransaksi=b.notransaksi AND b.tanggal<='".tanggalsystem($periode2)."' AND b.tanggal>='".tanggalsystem($periode1)."' and b.noakun='".$param['noakunsmp']."' and b.kodeorg='".$kodeorg."' and posting='1' ORDER BY  b.tanggal, FORMAT(right(a.notransaksi,6),0), a.tipetransaksi DESC ";
        $res = mysql_query($quer);

         $quer1 = "SELECT awal".$blnawl." as saldoawal, namaakun FROM keu_saldobulanan a inner join keu_5akun b on a.noakun=b.noakun where periode='".$thnawl.$blnawl."' AND a.noakun='".$param['noakunsmp']."' and a.kodeorg='".$kodeorg."'";
          $res1 = mysql_query($quer1);
          $hasil=mysql_fetch_assoc($res1);

        $saldoawal=$hasil['saldoawal'];
        
        if($tglawl!='01'){
            $tglawal=$thnawl.$blnawl.'01';
            $quer2 = "SELECT a.*, b.tanggal as tanggaltrans FROM keu_kasbankdt a inner join keu_kasbankht b ON a.notransaksi=b.notransaksi WHERE b.tanggal<'".tanggalsystem($periode1)."' AND b.tanggal>='".$tglawal."' and b.noakun='".$param['noakunsmp']."' and b.kodeorg='".$kodeorg."' and posting='1' ORDER BY  b.tanggal, a.tipetransaksi DESC ";
            $res2 = mysql_query($quer2);
            echo $quer2;
            while ($dat = mysql_fetch_object($res2)) {
            if($dat->tipetransaksi=='M'){
                    $saldoawal=$saldoawal+$dat->jumlah;
                } else {
                    $saldoawal=$saldoawal-$dat->jumlah;
                }
            }

        }
    
        break;

}

if (1 === $param['pildt']) {

    $brd = 0;

    $bgclr = '';

    $mode = 'totalAkun';

    if ('excel' === $_GET['mode']) {

        $mode = '';

        $mode = 'totalAkun';

        $isiDari = 'cetakexcel';

        $brd = 1;

        $bgclr = 'bgcolor:#DEDEDE';

    } else {

        if ('pdf' === $_GET['mode']) {

            $mode = '';

            $mode = 'cetakpdf';

        }

    }

}



switch ($mode) {

    case 'pdf':

        $optJab = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan', "kodejabatan='".$_SESSION['empl']['kodejabatan']."'");

        $colPdf = ['nourut', 'tanggal', 'keterangan', 'kasmasuk', 'penerimaan', 'kaskeluar', 'pengeluaran'];

        $title = $_SESSION['lang']['kasharian'];

        $length = explode(',', '4,10,13,28,15,15,15');

        $pdf = new zPdfMaster('P', 'pt', 'A4');

        $pdf->setAttr1($title, $align, $length, $colPdf);

        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;

        $height = 15;

        $pdf->AddPage();

        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($length[0] / 100 * $width, $height, 'No', 'TLR', 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Tanggal', 'TLR', 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'No. Transaksi', 'TLR', 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Keterangan', 'TLR', 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Debet', 'TLR', 0, 'C', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, 'Kredit', 'TLR', 0, 'C', 1);        
        $pdf->Cell($length[6] / 100 * $width, $height, 'Saldo', 'TLR', 0, 'C', 1);
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(($length[0]+$length[1]) / 100 * $width, $height, $param['noakunsmp'], 'TLR', 0, 'R', 1);
        $pdf->Cell(($length[2]+$length[3]+$length[4]+$length[5]+$length[6]) / 100 * $width, $height, $hasil['namaakun'], 'TLR', 0, 'L', 1);
        $pdf->Ln();

        $pdf->Cell(($length[0]+$length[1]) / 100 * $width, $height, '', 'TLR', 0, 'R', 1);
        $pdf->Cell(($length[2]+$length[3]+$length[4]+$length[5]) / 100 * $width, $height, 'Saldo Awal '.$periode1, 'TLR', 0, 'C', 1);
        $pdf->Cell($length[6]/ 100 * $width, $height, number_format($saldoawal, 2), 'TLR', 0, 'R', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);

        $no=1;
        $saldo=$saldoawal;
        while ($bar = mysql_fetch_object($res)) {

                $pdf->Cell($length[0] / 100 * $width, $height, $no, 1, 0, 'L', 1);
                $pdf->Cell($length[1] / 100 * $width, $height, $bar->tanggaltrans, 1, 0, 'C', 1);
                $pdf->Cell($length[2] / 100 * $width, $height, substr($bar->notransaksi,6), 1, 0, 'L', 1);
                $pdf->Cell($length[3] / 100 * $width, $height, $bar->keterangan2, 1, 0, 'L', 1);

                if($bar->tipetransaksi=='M'){
                    $pdf->Cell($length[4] / 100 * $width, $height, number_format($bar->jumlah,2), 1, 0, 'R', 1);
                    $pdf->Cell($length[5] / 100 * $width, $height, '', 1, 0, 'R', 1);
                    $saldo=$saldo+$bar->jumlah;
                    $totmasuk+=$bar->jumlah;
                } else {
                    $pdf->Cell($length[4] / 100 * $width, $height, '', 1, 0, 'R', 1);
                    $pdf->Cell($length[5] / 100 * $width, $height, number_format($bar->jumlah,2), 1, 0, 'R', 1);
                    $saldo=$saldo-$bar->jumlah;
                    $totkeluar+=$bar->jumlah;
                }
                
                $pdf->Cell($length[6] / 100 * $width, $height, number_format($saldo, 2), 1, 0, 'R', 1);

                $pdf->Ln();
                $no++;

        }

            $lenJudul = $length[0] + $length[1] + $length[2] + $length[3];

            $pdf->Cell($length[0] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[1] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[2] / 100 * $width, $height, 'Jumlah', 1, 0, 'C', 1);

            $pdf->Cell($length[3] / 100 * $width, $height, '', 1, 'B', 'L', 1);

            $pdf->Cell($length[4] / 100 * $width, $height, number_format($totmasuk,2), 1, 0, 'R', 1);

            $pdf->Cell($length[5] / 100 * $width, $height, number_format($totkeluar,2), 1, 0, 'R', 1);

            $pdf->Cell($length[6] / 100 * $width, $height, '', 1, 0, $align[5], 1);

            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 8);

            $pdf->Cell($length[0] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[1] / 100 * $width, $height, '', 1, 0, 0, 1);
            
             $pdf->Cell( ($length[2]+$length[3]+$length[4]+$length[5])/ 100 * $width, $height,  'Saldo Akhir '.$periode2, 1, 0, 'R', 1);

            $pdf->Cell($length[6] / 100 * $width, $height, number_format($saldo, 2), 1, 0, 'R', 1);

            $pdf->Ln();


        $pdf->SetFont('Arial', 'I', 9);
        $sen=explode(".",number_format($saldo,2));
        if($sen[1]>0){
          $nilaisen=terbilang($sen[1])." Sen ";
        }
        $pdf->MultiCell($width, $height, $_SESSION['lang']['terbilang'].' : '.terbilang($saldo, 2).' Rupiah '.$nilaisen , 'LR',1);



        $pdf->Cell($width, $height, '', 'LR', 0, $align[4], 0);

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(1 / 3 * $width, $height, date("d-m-Y"), 'L', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', 'R', 0, 'C', 0);

        $pdf->Ln();

        $pdf->Cell(1 / 3 * $width, $height, $_SESSION['lang']['dibuatoleh'], 'L', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, $_SESSION['lang']['diperiksa'], '',0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, $_SESSION['lang']['disetujui'], 'R', 0, 'C', 0);

        $pdf->Ln();

        $pdf->Cell($width, $height, '', 'LR', 1, $align[4], 0);

        $pdf->Cell($width, $height, '', 'LR', 1, $align[4], 0);

        $pdf->Cell($width, $height, '', 'LR', 1, $align[4], 0);

        $pdf->SetFont('Arial', 'U', 9);

        $pdf->Cell(1 / 3 * $width, $height, '', 'L', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', '', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', 'R', 0, 'C', 0);



 //       $pdf->Cell(1 / 3 * $width, $height, $DiterimaId, 'L', 0, 'C', 0);

 //       $pdf->Cell(1 / 3 * $width, $height, $DiperiksaId, '', 0, 'C', 0);

 //       $pdf->Cell(1 / 3 * $width, $height, $DisetujuiId, 'R', 0, 'C', 0);

        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 9);

        $pdf->Cell(1 / 3 * $width, $height, '', 'LB', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', 'B', 0, 'C', 0);

        $pdf->Cell(1 / 3 * $width, $height, '', 'RB', 0, 'C', 0);

        $pdf->Output();



        break;

    case 'cetakpdf':



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

        global $dtKurs;

        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");

        $orgData = fetchData($query);

        $width = $this->w - $this->lMargin - $this->rMargin;

        $height = 20;

        $path = 'images/'.strtolower($_SESSION['org']['kodeorganisasi']).'_logo.jpg';

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

        $this->Ln(25);

        $this->SetFont('Arial', 'B', 10);

        $this->SetFont('Arial', '', 8);

        $this->Cell(100 / 100 * $width - 5, 10, 'Printed By : '.$_SESSION['standard']['username'], '', 1, 'L');

        $this->Cell(100 / 100 * $width - 5, 10, 'Date : '.date('d-m-Y'), '', 1, 'L');

        $this->Cell(100 / 100 * $width - 5, 10, 'Time : '.date('h:i:s'), '', 1, 'L');

        $this->Ln();

        $this->SetFont('Arial', 'B', 12);

        $this->Cell($width, $height, 'KAS BANK', '', 0, 'C');

        $this->Ln();

        $this->Ln();

        $this->SetFont('Arial', 'B', 8);

        $this->SetFillColor(220, 220, 220);

        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);

        $this->Cell(25 / 100 * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);

        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['saldoawal'], 1, 0, 'C', 1);

        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['kasmasuk'], 1, 0, 'C', 1);

        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['kaskeluar'], 1, 0, 'C', 1);

        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['saldoakhir'], 1, 1, 'C', 1);

    }



    public function Footer()

    {

        $this->SetY(-15);

        $this->SetFont('Arial', 'I', 8);

        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');

    }

}



        $pdf = new PDF('p', 'pt', 'Legal');

        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;

        $height = 20;

        $pdf->AddPage();

        $pdf->SetFillColor(255, 255, 255);

        $pdf->SetFont('Arial', '', 8);

        $no = 0;

        foreach ($sort_noakun as $lstNoakun) {

            $saldoAwal[$lstNoakun] = $saldoAwal[$lstNoakun] / $dtKurs;

            $saldoAkhir[$lstNoakun] = ($saldoKM[$lstNoakun] + $saldoAwal[$lstNoakun]) - $saldoKK[$lstNoakun];

            $pdf->Cell(15 / 100 * $width, $height, $lstNoakun, 1, 0, 'L', 1);

            $pdf->Cell(25 / 100 * $width, $height, $otNmAkun[$lstNoakun], 1, 0, 'L', 1);

            $pdf->Cell(15 / 100 * $width, $height, number_format($saldoAwal[$lstNoakun], 0), 1, 0, 'R', 1);

            $pdf->Cell(15 / 100 * $width, $height, number_format($saldoKM[$lstNoakun], 0), 1, 0, 'R', 1);

            $pdf->Cell(15 / 100 * $width, $height, number_format($saldoKK[$lstNoakun], 0), 1, 0, 'R', 1);

            $pdf->Cell(15 / 100 * $width, $height, number_format($saldoAkhir[$lstNoakun], 0), 1, 1, 'R', 1);

            $grSalKm += $saldoKM[$lstNoakun];

            $grSalKk += $saldoKK[$lstNoakun];

            $grSalAw += $saldoAwal[$lstNoakun];

        }

        $selisihTot = ($grSalAw + $grSalKm) - $grSalKk;

        $pdf->Cell(40 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'R', 1);

        $pdf->Cell(15 / 100 * $width, $height, number_format($grSalAw, 0), 1, 0, 'R', 1);

        $pdf->Cell(15 / 100 * $width, $height, number_format($grSalKm, 0), 1, 0, 'R', 1);

        $pdf->Cell(15 / 100 * $width, $height, number_format($grSalKk, 0), 1, 0, 'R', 1);

        $pdf->Cell(15 / 100 * $width, $height, number_format($selisihTot, 0), 1, 0, 'R', 1);

        $tab .= '<tr class=rowcontent>';

        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';

        $tab .= '<td>'.number_format($grSalAw, 0).'</td>';

        $tab .= '<td>'.number_format($grSalKm, 0).'</td>';

        $tab .= '<td>'.number_format($grSalKk, 0).'</td>';

        $tab .= '<td>'.number_format($selisihTot, 0).'</td>';

        $tab .= '</tr>';

        $tab .= '</tbody></table>';

        $pdf->Output();



        break;

    case 'totalAkun':

        $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.'>';

        $tab .= '<thead>';

        $tab .= '<tr class=rowheader '.$bgclr.'><td>'.$_SESSION['lang']['noakun'].'</td>';

        $tab .= '<td>'.$_SESSION['lang']['namaakun'].'</td>';

        $tab .= '<td>'.$_SESSION['lang']['saldoawal'].'</td>';

        $tab .= '<td>'.$_SESSION['lang']['kasmasuk'].'</td>';

        $tab .= '<td>'.$_SESSION['lang']['kaskeluar'].'</td>';

        $tab .= '<td>'.$_SESSION['lang']['saldoakhir'].'</td></tr></thead><tbody>';

        foreach ($sort_noakun as $lstNoakun) {

            $saldoAwal[$lstNoakun] = $saldoAwal[$lstNoakun] / $dtKurs;

            $tab .= '<tr class=rowcontent>';

            $tab .= '<td>'.$lstNoakun.'</td><td>'.$otNmAkun[$lstNoakun].'</td>';

            $tab .= '<td align=right>'.number_format($saldoAwal[$lstNoakun], 0).'</td>';

            $tab .= '<td align=right>'.number_format($saldoKM[$lstNoakun], 0).'</td>';

            $tab .= '<td align=right>'.number_format($saldoKK[$lstNoakun], 0).'</td>';

            $saldoAkhir[$lstNoakun] = ($saldoKM[$lstNoakun] + $saldoAwal[$lstNoakun]) - $saldoKK[$lstNoakun];

            $tab .= '<td align=right>'.number_format($saldoAkhir[$lstNoakun], 0).'</td>';

            $tab .= '</tr>';

            $grSalKm += $saldoKM[$lstNoakun];

            $grSalKk += $saldoKK[$lstNoakun];

            $grSalAw += $saldoAwal[$lstNoakun];

        }

        $tab .= '<tr class=rowcontent>';

        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';

        $tab .= '<td>'.number_format($grSalAw, 0).'</td>';

        $tab .= '<td>'.number_format($grSalKm, 0).'</td>';

        $tab .= '<td>'.number_format($grSalKk, 0).'</td>';

        $selisihTot = ($grSalAw + $grSalKm) - $grSalKk;

        $tab .= '<td>'.number_format($selisihTot, 0).'</td>';

        $tab .= '</tr>';

        $tab .= '</tbody></table>';

        if ('excel' === $_GET['mode']) {

            $stream = $tab;

            $nop_ = 'KasHarian_total'.$kodeorg;

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

                    echo 'Error : Tidak bisa menulis ke format excel';

                    exit();

                }



                echo $nop_;

                fclose($handle);

            }

        } else {

            echo $tab;

        }



        break;

    default:

        $alignPrev = [];

        foreach ($align as $key => $row) {

            switch ($row) {

                case 'L':

                    $alignPrev[$key] = 'left';



                    break;

                case 'R':

                    $alignPrev[$key] = 'right';



                    break;

                case 'C':

                    $alignPrev[$key] = 'center';



                    break;

            }

        }

        $bgclr = '';

        if ('excel' === $mode) {

            $tab = strtoupper($_SESSION['lang']['kasharian']).' : '.$namagudang.'<br>'.strtoupper($_SESSION['lang']['noakun']).' : '.$param['noakun'].'<br>'.strtoupper($_SESSION['lang']['periode']).' : '.$periode1.' s/d '.$periode2.($brd = 'border=1');

            $bgclr = 'bgcolor:#DEDEDE';

        }



        $tab .= "<table id='kasharian' class='sortable' ".$brd.'>';

        $tab .= "<thead><tr class='rowheader' ".$bgclr.'>';
        
        $tab .= "<td>No.</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>No. Transaksi</td>";
        $tab .= "<td>Keterangan</td>";
        $tab .= "<td>Debet</td>";
        $tab .= "<td>Kredit</td>";
        $tab .= "<td>Saldo</td>";

        $tab .= "</tr></thead>";

        $tab .= "<tbody>";

        $tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='2' align='Right'>".$param['noakunsmp']."</td>";
        $tab .= "<td colspan='5' >".$hasil['namaakun']."</td>";
        $tab .= "</tr>";

        $tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='4' align ='center'>Saldo Awal ".$periode1."</td>";
        $tab .= "<td></td>";
        $tab .= "<td></td>";
        $tab .= "<td align='Right'>".number_format($saldoawal, 2)."</td>";
        $tab .= "</tr>";

        $no=1;
        $saldo=$saldoawal;
       while ($bar = mysql_fetch_object($res)) {

                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td>".$no."</td>";
                $tab .= "<td>".$bar->tanggaltrans."</td>";
                $tab .= "<td>".substr($bar->notransaksi,6)."</td>";
                $tab .= "<td>".$bar->keterangan2."</td>";

                if($bar->tipetransaksi=='M'){
                    $tab .= "<td align='Right'>".number_format($bar->jumlah,2)."</td>";
                    $tab .= "<td></td>";
                    $saldo=$saldo+$bar->jumlah;
                    $totmasuk+=$bar->jumlah;
                } else {
                    $tab .= "<td></td>";
                    $tab .= "<td align='Right'>".number_format($bar->jumlah,2)."</td>";
                    $saldo=$saldo-$bar->jumlah;
                    $totkeluar+=$bar->jumlah;
                }
                
                $tab .= "<td align='Right'>".number_format($saldo, 2)."</td>";

               $tab .= "</tr>";


               $no++;

        }
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td colspan='4' align='center'>Jumlah</td>";
                $tab .= "<td align='right'>".number_format($totmasuk,2)."</td>";
                $tab .= "<td align='right'>".number_format($totkeluar,2)."</td>";
                $tab .= "<td ></td>";
                $tab .= "</tr>";
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td colspan='4' align='center'>Saldo Akhir ".$periode2."</td>";
                $tab .= "<td align='right'></td>";
                $tab .= "<td align='right'></td>";
                $tab .= "<td align='right'>".number_format($saldo, 2)."</td>";
                $tab .= "</tr>";
   

        $tab .= '</tbody>';

        $tab .= '</table>';

        if ('excel' === $mode) {

            $stream = $tab;

            $nop_ = 'KasHarian_'.$kodeorg;

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

                    echo 'Error : Tidak bisa menulis ke format excel';

                    exit();

                }



                echo $nop_;

                fclose($handle);

            }

        } else {

            echo $tab;

        }



        break;

}



?>