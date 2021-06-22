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



        $persbl = substr(tanggalsystem($periode1), 0, 6);

        $cols2 = 'sum(jumlah) as jumlah,tipetransaksi,noakun';

        $where2 = "tanggal<'".tanggalsystem($periode2)."' and tanggal>='".tanggalsystem($periode1)."' and noakun>='".$param['noakun']."' and noakun<='".$param['noakunsmp']."' and "."kodeorg='".$kodeorg."' and posting=1 group by tipetransaksi,noakun";

        $query2 = selectQuery($dbname, 'keu_kasbankht', $cols2, $where2, 'tanggal,a.tipetransaksi DESC');



        $res = mysql_query($query2);

        while ($bar = mysql_fetch_object($res)) {

            if ('M' === $bar->tipetransaksi) {

                $saldoAwal[$bar->noakun] += $bar->jumlah;

                $grndTotalSawal += $bar->jumlah;

            } else {

                $saldoAwal[$bar->noakun] -= $bar->jumlah;

                $grndTotalSawal -= $bar->jumlah;

            }

        }



        $query1 = 'select b.jumlah, b.tipetransaksi,b.noakun from '.$dbname.".keu_kasbankdt b left join ".$dbname.".keu_kasbankht a on b.notransaksi=a.notransaksi where b.noakun >= '".$param['noakun']."' and b.noakun <= '".$param['noakunsmp']."' and b.kodeorg='".$kodeorg."' and a.posting=1 and a.tanggal>='".$persbl.'01'."' and a.tanggal<'".tanggalsystem($periode1)."' group by b.tipetransaksi,b.noakun order by a.tanggal,a.tipetransaksi DESC";

        $res = mysql_query($query1);



        while ($bar = mysql_fetch_object($res)) {

            if ('K' === $bar->tipetransaksi) {

                $saldoAwal[$bar->noakun] += $bar->jumlah;

                $grndTotalSawal += $bar->jumlah;

            } else {

                $saldoAwal[$bar->noakun] -= $bar->jumlah;

                $grndTotalSawal -= $bar->jumlah;

            }

        }
      

        $thnawl = substr($persbl, 0, 4);

        $blnawl = substr($persbl, 4, 2);

        $queryx = 'select sum(awal'.$blnawl.') as jumlah,noakun from '.$dbname.".keu_saldobulanan where noakun>='".$param['noakun']."' and noakun<='".$param['noakunsmp']."' and "."kodeorg='".$kodeorg."' and periode='".$persbl."' group by noakun order by noakun asc";

        $res = mysql_query($queryx);

        

        $bar = mysql_fetch_array($res);

        $saldoawalakun=$bar['jumlah'];

 /*       while ($bar = mysql_fetch_object($res)) {

            $saldoAwal[$bar->noakun] += $bar->jumlah;

            $grndTotalSawal += $bar->jumlah;

        }

*/      $wherd = "kasbank=1 and (pemilik='HOLDING' or pemilik='".$param['kodeorg']."' or pemilik='GLOBAL')";

        $cols = 'noakun,notransaksi,tipetransaksi,tanggal as tanggal,jumlah,keterangan,matauang,disetujui,diperiksa,userid';



        $where = "tanggal>='".tanggalsystem($periode1)."' and tanggal<='".tanggalsystem($periode2)."' and noakun>='".$param['noakun']."' and noakun<='".$param['noakunsmp']."' and noakun in (select distinct noakun from  ".$dbname.'.keu_5akun where '.$wherd.') and '."kodeorg='".$kodeorg."' and posting=1";

        $sQue = 'select '.$cols.' from '.$dbname.'.keu_kasbankht where '.$where.' order by tanggal asc, tipetransaksi DESC';

        $query = mysql_query($sQue);

 //       echo "warning :".$sQue;

        while ($res = mysql_fetch_assoc($query)) {

            $resH[] = $res;

            $nikditerima=$res['userid'];

            $nikdisetujui=$res['disetujui'];

            $nikdiperiksa=$res['diperiksa'];

        }



        if(isset($nikditerima)){

        $queryditerima = 'select * from '.$dbname.'.datakaryawan where karyawanid='.$nikditerima;

        $resditerima = fetchData($queryditerima);

        $DiterimaId = $resditerima[0]['namakaryawan'];

        }

    

        if(isset($nikdisetujui)){

        $disetujui = 'select * from '.$dbname.'.datakaryawan where karyawanid='.$nikdisetujui;

        $resdisetujui = fetchData($disetujui);

        $DisetujuiId = $resdisetujui[0]['namakaryawan'];

        }

    

        if(isset($nikdiperiksa)){

        $querydiperiksa = 'select * from '.$dbname.'.datakaryawan where karyawanid='.$nikdiperiksa;

        $resdiperiksa = fetchData($querydiperiksa);

        $DiperiksaId = $resdiperiksa[0]['namakaryawan'];

        }

    



        $sMtUang = "select matauang,noakun,notransaksi from ".$dbname.'.keu_kasbankht where '.$where.'';

        $qMtUang = mysql_query($sMtUang);

        while ($rMtUang = mysql_fetch_assoc($qMtUang)) {

            $lstMtuang[$rMtUang['matauang']] = $rMtUang['matauang'];

            $lstNoakun[$rMtUang['noakun']] = $rMtUang['noakun'];

            if ('IDR' === $rMtUang['matauang']) {

                $notExclude[$rMtUang['notransaksi']] = $rMtUang['notransaksi'];

            }

        }

        if (empty($resH)) {

            echo 'Warning : No data found';

            exit();

        }



        if (1 < count($lstMtuang)) {

            echo 'List notransaction IDR Currency<pre>';

            print_r($notExclude);

            echo '</pre>';

            exit("error: Sorry can't display, the selected account number has multiple currency");

        }



        $tglGnti = '01-'.$blnawl.'-'.$thnawl;

        $currBln = $thnawl.'-'.$blnawl;

        $jmlhhari = 1;

        $stat = 0;

        $tglLalu = nambahHari($tglGnti, $jmlhhari, $stat);

        $itungkosong = 0;

        foreach ($lstMtuang as $dtMtuang) {

            if ('IDR' !== $dtMtuang) {

                $whr = "periode='".substr($tglLalu, 0, 7)."' and matauang='".$dtMtuang."'";

                $optKurs = makeOption($dbname, 'keu_5kursbulanan', 'matauang,kurs', $whr);

                $dtKurs = $optKurs[$dtMtuang];

                $dtMatauang = $dtMtuang;

                if ('' === $dtKurs) {

                    $whr = "periode='".$currBln."' and matauang='".$dtMtuang."'";

                    $optKurs = makeOption($dbname, 'keu_5kursbulanan', 'matauang,kurs', $whr);

                    $dtKurs = $optKurs[$dtMtuang];

                    $dtMatauang = $dtMtuang;

                }



                if ('' === $dtKurs) {

                    ++$itungkosong;

                }

            }

        }

        if (0 !== $itungkosong) {

            exit('error: Please insert monthly rate for this month :'.$currBln);

        }



        $saldoKK = [];

        $saldoKM = [];

        $data = [];

        $varTemp = '';

        foreach ($resH as $key => $row) {

            if ($row['noakun'] !== $varTemp) {

                $nodt = 1;

                $varTemp = $row['noakun'];

                $lstZkey[$row['noakun']] = $nodt;

            } else {

                ++$nodt;

                $lstZkey[$row['noakun']] = $nodt;

            }



            $data[$row['noakun']][$nodt] = ['no' => $nodt, 'tanggal' => tanggalnormal($row['tanggal']), 'keterangan' => $row['keterangan'], 'km' => '', 'saldokm' => '', 'kk' => '', 'saldokk' => '', 'matauang' => $row['matauang']];

            if ('' !== $row['jumlah'] || 0 !== $row['jumlah']) {

                $sort_noakun[$row['noakun']] = $row['noakun'];

                if ('K' === $row['tipetransaksi']) {

                    $data[$row['noakun']][$nodt]['kk'] = $row['notransaksi'];

                    $data[$row['noakun']][$nodt]['saldokk'] = $row['jumlah'];

                    $saldoKK[$row['noakun']] += $row['jumlah'];

                    $grndTotalKK += $row['jumlah'];

                } else {

                    $data[$row['noakun']][$nodt]['km'] = $row['notransaksi'];

                    $data[$row['noakun']][$nodt]['saldokm'] = $row['jumlah'];

                    $saldoKM[$row['noakun']] += $row['jumlah'];

                    $grndTotalKM += $row['jumlah'];

                }

            }

        }

        $query1 = 'select b.jumlah, b.tipetransaksi,b.keterangan2,b.notransaksi,a.tanggal as tanggal ,b.noakun,a.matauang from '.$dbname.".keu_kasbankdt b  left join ".$dbname.".keu_kasbankht a on b.notransaksi=a.notransaksi where b.noakun>='".$param['noakun']."' and b.noakun<='".$param['noakunsmp']."' and b.noakun in (select noakun from ".$dbname.'.keu_5akun where '.$wherd.") and b.kodeorg='".$kodeorg."' and a.tanggal>='".tanggalsystem($periode1)."' and a.tanggal<='".tanggalsystem($periode2)."'";

        $resH1 = fetchData($query1);

        foreach ($resH1 as $key => $row) {

            if ($row['noakun'] !== $varTemp) {

                $nodt = $lstZkey[$row['noakun']];

                if ('' === $nodt) {

                    $nodt = 1;

                } else {
                    ++$nodt;

                }



                $varTemp = $row['noakun'];

            } else {

                ++$nodt;

                $lstZkey[$row['noakun']] = $nodt;

            }



            $data[$row['noakun']][$nodt] = ['no' => $nodt, 'tanggal' => tanggalnormal($row['tanggal']), 'keterangan' => $row['keterangan2'], 'km' => '', 'saldokm' => '', 'kk' => '', 'saldokk' => '', 'matauang' => $row['matauang']];

            if ('' !== $row['jumlah'] || '0' !== $row['jumlah']) {

                $sort_noakun[$row['noakun']] = $row['noakun'];

                if ('M' === $row['tipetransaksi']) {

                    $data[$row['noakun']][$nodt]['kk'] = $row['notransaksi'];

                    $data[$row['noakun']][$nodt]['saldokk'] = $row['jumlah'];

                    $saldoKK[$row['noakun']] += $row['jumlah'];

                    $grndTotalKK += $row['jumlah'];

                } else {

                    $data[$row['noakun']][$nodt]['km'] = $row['notransaksi'];

                    $data[$row['noakun']][$nodt]['saldokm'] = $row['jumlah'];

                    $saldoKM[$row['noakun']] += $row['jumlah'];

                    $grndTotalKM += $row['jumlah'];

                }

            }

        }

        if (!empty($data)) {

            foreach ($data as $c => $key) {

                $sort_tangg[] = $key['tanggal'];

                $sort_debet[] = $key['saldokm'];

            }

        }



        array_multisort($sort_noakun, SORT_ASC);

        $theCols = [$_SESSION['lang']['nomor'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['keterangan'], $_SESSION['lang']['kasmasuk'], $_SESSION['lang']['penerimaan'], $_SESSION['lang']['kaskeluar'], $_SESSION['lang']['pengeluaran']];

        $align = explode(',', 'L,R,L,R,R,R,R');



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

        $length = explode(',', '5,10,12,28,15,15,15');

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

        foreach ($sort_noakun as $lstNoakun) {

            if ($erts !== $lstNoakun) {

                $pdf->SetFont('Arial', 'B', 8);

                $pdf->Cell(15 / 100 * $width, $height, $lstNoakun, 'TLR', 0, 'R', 1);

                $pdf->Cell(85 / 100 * $width, $height, $otNmAkun[$lstNoakun], 'TLR', 0, 'L', 1);

                $pdf->Ln();

                $pdf->Cell(15 / 100 * $width, $height, '', 'TLR', 0, 'R', 1);

                $pdf->Cell(70 / 100 * $width, $height, 'Saldo Awal '.$periode1, 'TLR', 0, 'C', 1);

                $saldoAwal[$lstNoakun] = $saldoAwal[$lstNoakun] / $dtKurs;

                $pdf->Cell(15/ 100 * $width, $height, number_format($saldoawalakun, 2), 'TLR', 0, 'R', 1);

                $pdf->Ln();

                $erts = $lstNoakun;

            }



            $pdf->SetFont('Arial', '', 7);

            for ($key = 1; $key <= $lstZkey[$lstNoakun]; ++$key) {

                $pdf->Cell($length[0] / 100 * $width, $height, $data[$lstNoakun][$key][no], 'TLR', 0, 'L', 1);

                $pdf->Cell($length[1] / 100 * $width, $height, $data[$lstNoakun][$key][tanggal], 'TLR', 0, 'C', 1);

                if($data[$lstNoakun][$key][saldokm]>0){
                $pdf->Cell($length[2] / 100 * $width, $height, substr($data[$lstNoakun][$key][km],6), 'TLR', 0, 'R', 1);
                } else {
                $pdf->Cell($length[2] / 100 * $width, $height, substr($data[$lstNoakun][$key][kk],6), 'TLR', 0, 'R', 1);
                }


                $pdf->Cell($length[3] / 100 * $width, $height, $data[$lstNoakun][$key][keterangan], 'TLR', 0, 'L', 1);

                $pdf->Cell($length[4] / 100 * $width, $height, number_format($data[$lstNoakun][$key][saldokm], 2), 'TLR', 0, 'R', 1);
                $pdf->Cell($length[5] / 100 * $width, $height, number_format($data[$lstNoakun][$key][saldokk], 2), 'TLR', 0, 'R', 1);
                $saldoawalakun=$saldoawalakun+$data[$lstNoakun][$key][saldokm]-$data[$lstNoakun][$key][saldokk];
                $pdf->Cell($length[6] / 100 * $width, $height, number_format($saldoawalakun, 2), 'TLR', 0, 'R', 1);

                $pdf->Ln();

            }

            $lenJudul = $length[0] + $length[1] + $length[2] + $length[3];

            $pdf->Cell($length[0] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[1] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[2] / 100 * $width, $height, 'Jumlah', 1, 0, 'C', 1);

            $pdf->Cell($length[3] / 100 * $width, $height, '', 1, 'B', 'L', 1);

            $pdf->Cell($length[4] / 100 * $width, $height, number_format($saldoKM[$lstNoakun],2), 1, 0, $align[3], 1);

            $pdf->Cell($length[5] / 100 * $width, $height, number_format($saldoKK[$lstNoakun],2), 1, 0, $align[4], 1);

            $pdf->Cell($length[6] / 100 * $width, $height, '', 1, 0, $align[5], 1);

            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 8);

            $pdf->Cell($length[0] / 100 * $width, $height, '', 1, 0, 0, 1);

            $pdf->Cell($length[1] / 100 * $width, $height, '', 1, 0, 0, 1);
            
             $pdf->Cell( 70/ 100 * $width, $height,  'Saldo Akhir '.$periode2, 1, 0, 'C', 1);

            $pdf->Cell($length[6] / 100 * $width, $height, number_format($saldoawalakun, 2), 1, 0, $align[5], 1);

            $pdf->Ln();

            $saldoTerbilang += $saldoKM[$lstNoakun];

        }




        $pdf->SetFont('Arial', 'I', 9);
        $optMt = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $sen=explode(".",number_format($saldoawalakun,2));
        if($sen[1]>0){
          $nilaisen=terbilang($sen[1])." Sen ";
        }

        $pdf->MultiCell($width, $height, $_SESSION['lang']['terbilang'].' : '.terbilang($saldoawalakun, 2).' Rupiah '.$nilaisen , 'LR');
   //     $pdf->MultiCell($width, $height, 'Terbilang : [ '.terbilang($saldoawalakun, 0).' '.strtolower($dtMatauang).'. ]', 'LR', 'L');



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
        
        $tab .= '<td>No.</td>';
        $tab .= '<td>Tanggal</td>';
        $tab .= '<td>No. Transaksi</td>';
        $tab .= '<td>Keterangan</td>';
        $tab .= '<td>Debet</td>';
        $tab .= '<td>Kredit</td>';
        $tab .= '<td>Saldo</td>';

        $tab .= '</tr></thead>';

        $tab .= '<tbody>';


        foreach ($sort_noakun as $lstNoakun) {

            if ($erts !== $lstNoakun) {

                $tab .= "<tr class='rowcontent'>";
                $tab .= '<td></td><td></td>';
                $tab .= "<td><b>".$lstNoakun."</b></td><td colspan='4'><b>".$otNmAkun[$lstNoakun]."</b></td>";
                $tab .= '</tr>';

                $tab .= "<tr class='rowcontent'>";

                $tab .= "<td></td><td></td><td align='center' colspan='4'>Saldo Awal ".$periode1.'</td>';

                $saldoAwal[$lstNoakun] = $saldoAwal[$lstNoakun] / $dtKurs;

                $tab .= "<td align='right'>".number_format($saldoawalakun, 2).'</td>';

                $tab .= '</tr>';

                $erts = $lstNoakun;

            }



            $subsaldo=0;
            $totdebet=0;
            $totkredit=0;
            for ($key = 1; $key <= $lstZkey[$lstNoakun]; ++$key) {

                $tab .= "<tr class='rowcontent'>";

                $tab .= '<td '.$alignPrev[$key].'>'.$data[$lstNoakun][$key][no].'</td>';

                $tab .= '<td '.$alignPrev[$key].'>'.$data[$lstNoakun][$key][tanggal].'</td>';
                if($data[$lstNoakun][$key][saldokm]>0){
                    $tab .= '<td '.$alignPrev[$key].'>'.$data[$lstNoakun][$key][km].'</td>';
                }else{
                    $tab .= '<td '.$alignPrev[$key].'>'.$data[$lstNoakun][$key][kk].'</td>';
                }
                $tab .= '<td '.$alignPrev[$key].'>'.$data[$lstNoakun][$key][keterangan].'</td>';
                $tab .= '<td '.$alignPrev[$key].' align=right>'.number_format($data[$lstNoakun][$key][saldokm], 2).'</td>';
                $tab .= '<td  align=right>'.number_format($data[$lstNoakun][$key][saldokk], 2).'</td>';
                $saldoawalakun=$saldoawalakun+$data[$lstNoakun][$key][saldokm]-$data[$lstNoakun][$key][saldokk];
                $tab .= '<td  align=right>'.number_format($saldoawalakun, 2).'</td>';

                $tab .= '</tr>';
                $totdebet=$totdebet+$data[$lstNoakun][$key][saldokm];
                $totkredit=$totkredit+$data[$lstNoakun][$key][saldokk];

            }

            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='4' align='right'>".$_SESSION['lang']['jumlah']."</td>";
            $tab .= "<td align='right'>".number_format($totdebet, 2).'</td>';
            $tab .= "<td align='right'>".number_format($totkredit, 2).'</td>';
            $tab .= '<td  align=right>'.number_format($saldoawalakun, 2).'</td>';
            $tab .= '</tr>';
        }

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