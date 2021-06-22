<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$kdPabrik = $_POST['kdPabrik'];
$kdCust = $_POST['kdCust'];
$nkntrak = $_POST['nkntrak'];
$kdBrg = $_POST['kdBrg'];
$tgl_1 = tanggalsystem($_POST['tgl_1']);
$tgl_2 = tanggalsystem($_POST['tgl_2']);
$tgl1 = tanggalsystem($_POST['tgl1']);
$tgl2 = tanggalsystem($_POST['tgl2']);
$kdCustomer = $_POST['kdCustomer'];
$wr = "kodekelompok='S003'";
$optSupp = makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier', $wr);
switch ($proses) {
    case 'preview':
        if ('' === $kdPabrik) {
            echo 'warning: Please choose mill';
            exit();
        }

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }

        $whr = '';
        if ('' !== $kdBrg) {
            $whr .= " and kodebarang ='".$kdBrg."'";
        }

        if ('' !== $nkntrak) {
            $whr .= " and nokontrak ='".$nkntrak."'";
        }

        if ('' !== $kdCust) {
            $whr .= " and kodecustomer ='".$kdCust."'";
        }

        $sTimbangan = "select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,jammasuk,jamkeluar,beratmasuk,\r\n                     substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli,beratbersih,beratkeluar,nosipb from ".$dbname.".pabrik_timbangan\r\n                     where  millcode='".$kdPabrik."' and kodebarang!='40000003' ".$whr.' and  tanggal >= '.$tgl_1.'000001 and tanggal<='.$tgl_2."235959\r\n\t\t\t\t\t order by tanggal asc";
        echo "<table cellspacing=1 border=0 class=sortable>\r\n        <thead class=rowheader>\r\n        <tr>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['materialname']."</td>\r\n                <td>".$_SESSION['lang']['tanggal']."</td>\r\n                <td>".$_SESSION['lang']['transporter']."</td>\r\n                <td>".$_SESSION['lang']['vendor']."</td>\r\n                <td>".$_SESSION['lang']['noTiket']."</td>\r\n                <td>".$_SESSION['lang']['kodenopol']."</td>\r\n                <td>".$_SESSION['lang']['beratnormal']."</td>\r\n                <td>".$_SESSION['lang']['beratMasuk']."</td>\r\n                <td>".$_SESSION['lang']['beratKeluar']."</td>\r\n                <td>".$_SESSION['lang']['beratnormal'].' '.substr($_SESSION['lang']['kodecustomer'], 5)."</td>\r\n                <td>".$_SESSION['lang']['jammasuk']."</td>\r\n                <td>".$_SESSION['lang']['jamkeluar']."</td>\r\n                <td>".$_SESSION['lang']['sopir']."</td>\r\n                <td>".$_SESSION['lang']['nodo']."</td>\r\n                <td>".$_SESSION['lang']['NoKontrak']."</td>\r\n\r\n        </tr>\r\n        </thead>\r\n        <tbody>";
        $qData = mysql_query($sTimbangan);
        $brs = mysql_num_rows($qData);
        if (0 < $brs) {
            while ($rData = mysql_fetch_assoc($qData)) {
                ++$no;
                $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
                $qBrg = mysql_query($sBrg);
                $rBrg = mysql_fetch_assoc($qBrg);
                $sKntrk = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
                $qKntrk = mysql_query($sKntrk);
                $rKntrak = mysql_fetch_assoc($qKntrk);
                $sNama = 'select namacustomer from '.$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
                $qNama = mysql_query($sNama);
                $rNama = mysql_fetch_assoc($qNama);
                $sTrans = 'select TRPCODE from '.$dbname.".pabrik_mssipb where SIPBNO='".$rData['nosipb']."'";
                $qTrans = mysql_query($sTrans);
                $rTrans = mysql_fetch_assoc($qTrans);
                echo "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$rBrg['namabarang']."</td>\r\n                        <td>".tanggalnormal($rData['tanggal'])."</td>\r\n                        <td>".$optSupp[$rData['kodecustomer']]."</td>\r\n                        <td>".$rNama['namacustomer']."</td>\r\n                        <td>".$rData['notransaksi']."</td>\r\n                        <td>".$rData['nokendaraan']."</td>\r\n                        <td  align=right>".number_format($rData['netto'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['beratmasuk'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['beratkeluar'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['kgpembeli'], 2)."</td>\r\n                        <td>".$rData['jammasuk']."</td>\r\n                        <td>".$rData['jamkeluar']."</td>\r\n                        <td>".$rData['supir']."</td>\r\n                        <td>".$rData['nodo']."</td>\r\n                        <td>".$rData['nokontrak']."</td>\r\n                        </tr>";
                $subtota += $rData['netto'];
            }
            echo '<tr class=rowcontent ><td colspan=7 align=right>Total (KG)</td><td align=right>'.number_format($subtota, 2).'</td><td colspan=8 align=right>&nbsp;</td></tr>';
        } else {
            echo '<tr class=rowcontent><td colspan=13 align=center>Data empty</td></tr>';
        }

        echo '</tbody></table>';

        break;
    case 'pdf':
        $kdCust = $_GET['kdCust'];
        $nkntrak = $_GET['nkntrak'];
        $kdBrg = $_GET['kdBrg'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
        $kdPabrik = $_GET['kdPabrik'];
        $rNmBrg = [];

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
        global $kdCust;
        global $nkntrak;
        global $kdBrg;
        global $kdPabrik;
        global $tgl_2;
        global $tgl_1;
        global $tglPeriode;
        global $tanggal;
        global $rNamaSupp;
        global $rNmBrg;
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qAlamat = mysql_query($sAlmat);
        $rAlamat = mysql_fetch_assoc($qAlamat);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 11;
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
        $this->Cell($width, $height, $_SESSION['lang']['rPengiriman'], 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $sNm = 'select namasupplier,kodetimbangan from '.$dbname.'.log_5supplier order by namasupplier asc';
        $qNm = mysql_query($sNm);
        while ($rNm = mysql_fetch_assoc($qNm)) {
            $rNamaSupp[$rNm['kodetimbangan']] = $rNm;
        }
        $sBrg = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%'";
        $qBrg = mysql_query($sBrg);
        while ($rBrg = mysql_fetch_assoc($qBrg)) {
            $rNmBrg[$rBrg['kodebarang']] = $rBrg;
        }
        if ('' !== $kdPabrik && '' !== $unit) {
            $this->Cell($width, $height, $_SESSION['lang']['rPengiriman'].' : '.$kdPabrik.' atas '.$rNmBrg[$kdBrg]['namabarang'].' '.$_SESSION['lang']['ke'].' '.$rNamaSupp[$unit]['namasupplier'].' '.$_SESSION['lang']['periode'].' :'.$tgl_1.'-'.$tgl_2, 0, 1, 'C');
        } else {
            $this->Cell($width, $height, $_SESSION['lang']['rPengiriman'].' : '.$kdPabrik.' atas '.$rNmBrg[$kdBrg]['namabarang'].' '.$_SESSION['lang']['ke'].' : '.$_SESSION['lang']['all'].', '.$_SESSION['lang']['periode'].' :'.tanggalnormal($tgl_1).' - '.tanggalnormal($tgl_2), 0, 1, 'C');
        }

        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['materialname'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['vendor'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['noTiket'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['kodenopol'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['beratnormal'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['sopir'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['nodo'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['NoKontrak'], 1, 1, 'C', 1);
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
        $height = 9;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        if ('' === $kdPabrik) {
            echo 'warning: Please choose mill';
            exit();
        }

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }

        $whr = '';
        if ('' !== $kdBrg) {
            $whr .= " and kodebarang ='".$kdBrg."'";
        }

        if ('' !== $nkntrak) {
            $whr .= " and nokontrak ='".$nkntrak."'";
        }

        if ('' !== $kdCust) {
            $whr .= " and kodecustomer ='".$kdCust."'";
        }

        $sTimbangan = "select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,jammasuk,jamkeluar,beratmasuk,\r\n                     substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli,beratbersih,beratkeluar,nosipb from ".$dbname.".pabrik_timbangan\r\n                     where  millcode='".$kdPabrik."' and kodebarang!='40000003' ".$whr.' and  tanggal >= '.$tgl_1.'000001 and tanggal<='.$tgl_2."235959\r\n\t\t\t\t\t \r\n\t\t\t\t\t order by tanggal asc";
        $qList = mysql_query($sTimbangan);
        while ($rData = mysql_fetch_assoc($qList)) {
            $sKntrk = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
            $qKntrk = mysql_query($sKntrk);
            $rKntrak = mysql_fetch_assoc($qKntrk);
            $sNama = 'select namacustomer from '.$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
            $qNama = mysql_query($sNama);
            $rNama = mysql_fetch_assoc($qNama);
            $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
            $qBrg = mysql_query($sBrg);
            $rBrg = mysql_fetch_assoc($qBrg);
            ++$no;
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
            $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($rData['tanggal']), 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $rNama['namacustomer'], 1, 0, 'L', 1);
            $pdf->Cell(8 / 100 * $width, $height, $rData['notransaksi'], 1, 0, 'L', 1);
            $pdf->Cell(12 / 100 * $width, $height, $rData['nokendaraan'], 1, 0, 'L', 1);
            $pdf->Cell(9 / 100 * $width, $height, number_format($rData['netto'], 2), 1, 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rData['supir'], 1, 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $rData['nodo'], 1, 0, 'L', 1);
            $pdf->Cell(12 / 100 * $width, $height, $rData['nokontrak'], 1, 1, 'L', 1);
            $subtota += $rData['netto'];
            $subjjg += $rData['jjg'];
        }
        $pdf->Cell(61 / 100 * $width, $height, 'Total', 1, 0, 'R', 1);
        $pdf->Cell(9 / 100 * $width, $height, number_format($subtota, 2), 1, 0, 'R', 1);
        $pdf->Cell(32 / 100 * $width, $height, '', 1, 1, 'C', 1);
        $pdf->Output();

        break;
    case 'excel':
        $kdCust = $_GET['kdCust'];
        $nkntrak = $_GET['nkntrak'];
        $kdBrg = $_GET['kdBrg'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
        $kdPabrik = $_GET['kdPabrik'];
        $sNm = 'select namasupplier,kodetimbangan from '.$dbname.'.log_5supplier order by namasupplier asc';
        $qNm = mysql_query($sNm);
        while ($rNm = mysql_fetch_assoc($qNm)) {
            $rNamaSupp[$rNm['kodetimbangan']] = $rNm;
        }
        $sBrg = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%'";
        $qBrg = mysql_query($sBrg);
        while ($rBrg = mysql_fetch_assoc($qBrg)) {
            $rNmBrg[$rBrg['kodebarang']] = $rBrg;
        }
        $tab .= '<table cellspacing="1" border=0><tr><td colspan=10 align=center>'.$_SESSION['lang']['rPengiriman']."</td></tr>\r\n        ";
        if ('' !== $kdPabrik && '' !== $kdCust) {
            $tab .= '<tr><td colspan=2 align=right>'.$_SESSION['lang']['pengirimanBrg'].'</td><td colspan=8>'.$kdPabrik.' atas '.$rNmBrg[$kdBrg]['namabarang'].' '.$_SESSION['lang']['ke'].' '.$rNamaSupp[$kdCust]['namasupplier'].' '.$_SESSION['lang']['periode'].' :'.$tgl_1.'-'.$tgl_2.'</td></tr>';
        } else {
            $tab .= '<tr><td colspan=2 align=right>'.$_SESSION['lang']['pengirimanBrg'].'</td><td colspan=8>'.$kdPabrik.' atas '.$rNmBrg[$kdBrg]['namabarang'].' '.$_SESSION['lang']['ke'].' '.$_SESSION['lang']['all'].' '.$_SESSION['lang']['periode'].' :'.tanggalnormal($tgl_1).'-'.tanggalnormal($tgl_2).'</td></tr>';
        }

        $tab .= '</table>';
        if ('' === $kdPabrik) {
            echo 'warning: Please choose mill';
            exit();
        }

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';
        if ('' !== $kdBrg) {
            $whr .= " and kodebarang ='".$kdBrg."'";
        }

        if ('' !== $nkntrak) {
            $whr .= " and nokontrak ='".$nkntrak."'";
        }

        if ('' !== $kdCust) {
            $whr .= " and kodecustomer ='".$kdCust."'";
        }

        $sTimbangan = "select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,jammasuk,jamkeluar,beratmasuk,\r\n                     substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli,beratbersih,beratkeluar,nosipb from ".$dbname.".pabrik_timbangan\r\n                     where  millcode='".$kdPabrik."' and kodebarang!='40000003' ".$whr.' and  tanggal >= '.$tgl_1.'000001 and tanggal<='.$tgl_2."235959\r\n\t\t\t\t\t order by tanggal asc";
        $tab .= "<table cellspacing=1 border=1 class=sortable>\r\n        <thead class=rowheader>\r\n        <tr>\r\n                <td bgcolor=#DEDEDE> No.</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['materialname']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['transporter']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['vendor']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['noTiket']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['kodenopol']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratnormal']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratMasuk']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratKeluar']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratnormal'].' '.substr($_SESSION['lang']['kodecustomer'], 5)."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['jammasuk']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['jamkeluar']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['sopir']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['nodo']."</td>\r\n                <td bgcolor=#DEDEDE>".$_SESSION['lang']['NoKontrak']."</td>\r\n        </tr>\r\n        </thead>\r\n        <tbody>";
        $sData = 'select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from '.$dbname.".pabrik_timbangan where nokontrak!='' ".$where;
        $qData = mysql_query($sTimbangan);
        $brs = mysql_num_rows($qData);
        if (0 < $brs) {
            while ($rData = mysql_fetch_assoc($qData)) {
                ++$no;
                $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
                $qBrg = mysql_query($sBrg);
                $rBrg = mysql_fetch_assoc($qBrg);
                $sKntrk = 'select koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
                $qKntrk = mysql_query($sKntrk);
                $rKntrak = mysql_fetch_assoc($qKntrk);
                $sNama = 'select namacustomer from '.$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
                $qNama = mysql_query($sNama);
                $rNama = mysql_fetch_assoc($qNama);
                $sTrans = 'select TRPCODE from '.$dbname.".pabrik_mssipb where SIPBNO='".$rData['nosipb']."'";
                $qTrans = mysql_query($sTrans);
                $rTrans = mysql_fetch_assoc($qTrans);
                $tab .= "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$rBrg['namabarang']."</td>    \r\n                        <td>".tanggalnormal($rData['tanggal'])."</td>\r\n                        <td>".$optSupp[$rData['kodecustomer']]."</td>\r\n                        <!--<td>".$optSupp[$rTrans['TRPCODE']]."</td>-->\r\n                        <td>".$rNama['namacustomer']."</td>\r\n                        <td>".$rData['notransaksi']."</td>\r\n                        <td>".$rData['nokendaraan']."</td>\r\n                        <td  align=right>".number_format($rData['netto'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['beratmasuk'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['beratkeluar'], 2)."</td>\r\n                        <td  align=right>".number_format($rData['kgpembeli'], 2)."</td>\r\n                        <td>".$rData['jammasuk']."</td>\r\n                        <td>".$rData['jamkeluar']."</td>\r\n                        <td>".$rData['supir']."</td>\r\n                        <td>".$rData['nodo']."</td>\r\n                        <td>".$rData['nokontrak']."</td>\r\n                        </tr>";
                $subtota += $rData['netto'];
            }
            $tab .= '<tr class=rowcontent ><td colspan=7 align=right>Total (KG)</td><td align=right>'.number_format($subtota, 2).'</td><td colspan=8 align=right>&nbsp;</td></tr>';
        } else {
            $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';
        }

        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'LaporanPengiriman'.$tglSkrg;
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
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
    case 'getKontrakData':
        $sChek = 'select nokontrak from '.$dbname.".pmn_kontrakjual where koderekanan='".$kdCustomer."' order by nokontrak desc";
        $qChek = mysql_query($sChek);
        $brs = mysql_num_rows($qChek);
        if (0 < $brs) {
            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';
            while ($rCheck = mysql_fetch_assoc($qChek)) {
                $optKontrak .= '<option value='.$rCheck['nokontrak'].'>'.$rCheck['nokontrak'].'</option>';
            }
            echo $optKontrak;
        } else {
            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';
            echo $optKontrak;
        }

        break;
    case 'getCust':
        $rt = explode('-', $_POST['tgl1']);
        $rt2 = explode('-', $_POST['tgl2']);
        $tgl1 = $rt[2].'-'.$rt[1].'-'.$rt[0];
        $tgl2 = $rt2[2].'-'.$rt2[1].'-'.$rt2[0];
        $optCust = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sCust = 'select distinct a.kodecustomer,namacustomer from '.$dbname.".pabrik_timbangan a left join\r\n                ".$dbname.".pmn_4customer b on a.kodecustomer=b.kodetimbangan where \r\n                left(tanggal,10) between '".$tgl1."' and '".$tgl2."' and millcode='".$_POST['kdPabrik']."'\r\n                and kodebarang='".$_POST['kdBrg']."'\r\n                order by b.namacustomer asc";
        $qCust = mysql_query($sCust);
        while ($rCust = mysql_fetch_assoc($qCust)) {
            $optCust .= '<option value='.$rCust['kodecustomer'].'>'.$rCust['namacustomer'].' ['.$rCust['kodecustomer'].']</option>';
        }
        $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';
        $sChek = 'select distinct nokontrak from '.$dbname.".pabrik_timbangan where  \r\n                 left(tanggal,10) between '".$tgl1."' and '".$tgl2."'  and millcode='".$_POST['kdPabrik']."' \r\n                 and kodebarang='".$_POST['kdBrg']."'\r\n                 order by tanggal asc";
        $qChek = mysql_query($sChek);
        $brs = mysql_num_rows($qChek);
        if (0 < $brs) {
            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';
            while ($rCheck = mysql_fetch_assoc($qChek)) {
                $optKontrak .= '<option value='.$rCheck['nokontrak'].'>'.$rCheck['nokontrak'].'</option>';
            }
        }

        echo $optCust.'####'.$optKontrak;

        break;
}

?>