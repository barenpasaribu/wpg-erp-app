<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $posting;
        global $notransaksi;
        global $kodePt;
        global $kdBrg;
        global $tgl;
        global $kdCust;
        global $nmBrg;
        global $wilKota;
        global $nama;
        $notransaksi = $_GET['column'];
        $str = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$notransaksi."' ";
        $res = mysql_query($str);
        $bar = mysql_fetch_assoc($res);
        $kodePt = $bar['millcode'];
        $kdBrg = $bar['kodebarang'];
        $tgl = tanggalnormal($bar['tanggal']);
        $kdCust = $bar['koderekanan'];
        $str1 = 'select * from '.$dbname.".organisasi where kodeorganisasi='PMO'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $nama = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
            $wilKota = $bar1->wilayahkota;
        }
        $sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $nmBrg = $rBrg['namabarang'];
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

        $this->Image($path, 15, 5, 35, 20);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(55);
        $this->Cell(60, 5, $nama, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, $alamatpt, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, 'Tel: '.$telp, 0, 1, 'L');
        $this->Line(10, 30, 200, 30);
        $this->Ln();
        $this->Ln();
        $this->SetX(85);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(35, 5, $_SESSION['lang']['pabrikTimbangan'], 0, 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 5, $_SESSION['lang']['notransaksi'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $notransaksi, '', 0, 'L');
        $this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $tgl, 0, 1, 'L');
        $this->Cell(35, 5, $_SESSION['lang']['kdpabrik'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $kodePt, '', 0, 'L');
        $this->Cell(15, 5, 'User', '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
if ('40000001' === $kdBrg || '40000005' === $kdBrg || '40000002' === $kdBrg || '40000003' === $kdBrg) {
    $arrStat = ['On', 'Off'];
    $sTrans = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$notransaksi."' ";
    $qTrans = mysql_query($sTrans);
    $rTrans = mysql_fetch_assoc($qTrans);
    $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rTrans['kodecustomer']."'";
    $qCust = mysql_query($sCust);
    $rCust = mysql_fetch_assoc($qCust);
    $pdf->SetFont('Arial', 'U', 10);
    $pdf->Cell(35, 5, 'List Data', '', 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('Arial', '', '10');
    $pdf->Cell(35, 5, $_SESSION['lang']['namabarang'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $nmBrg, '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['NoKontrak'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['nokontrak'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['nmcust'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rCust['namacustomer'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['nodo'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['nodo'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['statTimbangan'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $arrStat[$rTrans['timbangonoff']], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['nosipb'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['nosipb'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['kodenopol'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['nokendaraan'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['sopir'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, ucfirst($rTrans['supir']), '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['beratkosong'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['beratmasuk'].' KG', '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['beratnormal'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['beratbersih'].' KG', '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['beratKeluar'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['beratkeluar'].' KG', '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['jammasuk'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['jammasuk'], '', 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 5, $_SESSION['lang']['jamkeluar'], '', 0, 'L');
    $pdf->Cell(2, 5, ':', '', 0, 'L');
    $pdf->Cell(100, 5, $rTrans['jamkeluar'], '', 0, 'L');
    $pdf->Ln();
} else {
    if ('40000004' === $kdBrg) {
        $arrStat = ['On', 'Off'];
        $sTrans = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$notransaksi."' ";
        $qTrans = mysql_query($sTrans);
        $rTrans = mysql_fetch_assoc($qTrans);
        $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rTrans['kodecustomer']."'";
        $qCust = mysql_query($sCust);
        $rCust = mysql_fetch_assoc($qCust);
        $pdf->SetFont('Arial', 'U', 10);
        $pdf->Cell(35, 5, 'List Data', '', 0, 'L');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', '10');
        $pdf->Cell(35, 5, $_SESSION['lang']['namabarang'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $nmBrg, '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['nmcust'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rCust['namacustomer'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['statTimbangan'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $arrStat[$rTrans['timbangonoff']], '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['kodenopol'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['nokendaraan'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['sopir'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, ucfirst($rTrans['supir']), '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['beratkosong'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['beratmasuk'].' KG', '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['beratnormal'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['beratbersih'].' KG', '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['beratKeluar'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['beratkeluar'].' KG', '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['jammasuk'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['jammasuk'], '', 0, 'L');
        $pdf->Ln();
        $pdf->Cell(35, 5, $_SESSION['lang']['jamkeluar'], '', 0, 'L');
        $pdf->Cell(2, 5, ':', '', 0, 'L');
        $pdf->Cell(100, 5, $rTrans['jamkeluar'], '', 0, 'L');
        $pdf->Ln();
    } else {
        if ('40000003' === $kdBrg) {
            $arrStat = ['On', 'Off'];
            $arrOptIntex = ['External', 'Internal', 'Afiliasi'];
            $sTrans = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$notransaksi."' ";
            $qTrans = mysql_query($sTrans);
            $rTrans = mysql_fetch_assoc($qTrans);
            $pdf->SetFont('Arial', 'U', 10);
            $pdf->Cell(35, 5, 'List Data', '', 0, 'L');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', '10');
            $pdf->Cell(35, 5, $_SESSION['lang']['namabarang'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $nmBrg, '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['statTimbangan'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $arrStat[$rTrans['timbangonoff']], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['nospb'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['nospb'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['statusBuah'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $arrOptIntex[$rTrans['intex']], '', 0, 'L');
            $pdf->Ln();
            if ('0' === $rTrans['intex']) {
                $sSupp = 'select namasupplier  from '.$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'";
                $qSupp = mysql_query($sSupp);
                $rSupp = mysql_fetch_assoc($qSupp);
                $pdf->Cell(35, 5, $_SESSION['lang']['namasupplier '], '', 0, 'L');
                $pdf->Cell(2, 5, ':', '', 0, 'L');
                $pdf->Cell(100, 5, $rSupp['namasupplier'], '', 0, 'L');
                $pdf->Ln();
            } else {
                if ('1' === $rTrans['intex'] || '2' === $rTrans['intex']) {
                    $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rTrans['kodeorg']."'";
                    $qOrg = mysql_query($sOrg);
                    $rOrg = mysql_fetch_assoc($qOrg);
                    $pdf->Cell(35, 5, $_SESSION['lang']['kebun'], '', 0, 'L');
                    $pdf->Cell(2, 5, ':', '', 0, 'L');
                    $pdf->Cell(100, 5, $rOrg['namaorganisasi'], '', 0, 'L');
                    $pdf->Ln();
                }
            }

            $pdf->Cell(35, 5, $_SESSION['lang']['thntanam'].' 1', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['thntm1'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['jmlhTandan'].' 1', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['jumlahtandan1'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['thntanam'].' 2', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['thntm2'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['jmlhTandan'].' 2', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['jumlahtandan2'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['thntanam'].' 3', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['thntm3'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['jmlhTandan'].' 3', '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['jumlahtandan3'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['brondolan'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['brondolan'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['kodenopol'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['nokendaraan'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['sopir'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, ucfirst($rTrans['supir']), '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['beratkosong'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['beratmasuk'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['beratnormal'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['beratbersih'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['beratKeluar'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['beratkeluar'].' KG', '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['statusSortasi'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['statussortasi'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['petugasSortasi'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, ucfirst($rTrans['petugassortasi']), '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['jammasuk'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['jammasuk'], '', 0, 'L');
            $pdf->Ln();
            $pdf->Cell(35, 5, $_SESSION['lang']['jamkeluar'], '', 0, 'L');
            $pdf->Cell(2, 5, ':', '', 0, 'L');
            $pdf->Cell(100, 5, $rTrans['jamkeluar'], '', 0, 'L');
            $pdf->Ln();
        }
    }
}

$pdf->Output();

?>