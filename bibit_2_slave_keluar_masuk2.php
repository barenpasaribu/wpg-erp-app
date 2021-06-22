<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/devLibrary.php';



$proses = $_GET['proses'];

if (empty($_POST)) {
    $pt1 = $_GET['pt1'];
    $kebun1 = $_GET['kebun1'];
    $tanggal1 = $_GET['tanggal1'];
    $tanggal11 = $_GET['tanggal11'];
    $kdUnit = $_GET['kdUnit'];
    $kdBatch = $_GET['kdBatch'];
} else {
    $pt1 = $_POST['pt1'];
    $kebun1 = $_POST['kebun1'];
    $tanggal1 = $_POST['tanggal1'];
    $tanggal11 = $_POST['tanggal11'];
    $kdUnit = $_POST['kdUnit'];
    $kdBatch = $_POST['kdBatch'];
}

$optnmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
switch ($proses) {
    case 'preview':
        if ('' == $kdUnit) {
            exit('Error: Unit code required');
        }

        break;
    default:
        break;
}
if ('all' == $kebun1) {
    $kebun1 = 'Seluruhnya';
}

if ('excel' == $proses) {
    $hider = $_SESSION['lang']['laporanStockBIbit'].'<br>PT: '.$pt1.'<br>'.$_SESSION['lang']['kebun'].': '.$kebun1.'<br>'.$_SESSION['lang']['sampai'].': '.$tanggal1;
    $bgcoloz = 'bgcolor=#dedede';
    $boder = 1;
} else {
    $hider = '';
    $bgcoloz = '';
    $boder = 0;
}

if ('Seluruhnya' == $kebun1) {
    $kebun1 = '';
}

$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
$stab = $hider;
$stab .= '<table cellpadding=1 cellspacing=1 border='.$boder." class=sortable>\r\n<thead>\r\n<tr class=rowheader>\r\n    <td align=center ".$bgcoloz.">Batch</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['tgltanam']."</td>   \r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['jenisbibit']."</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['diterima']."</td>\r\n    <td align=center ".$bgcoloz.'>Seleksi Awal('.$_SESSION['lang']['afkirbibit'].")</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['ditanam']."</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['afkirbibit']." PN</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['pindahpnmn']."</td>\r\n    <td align=center ".$bgcoloz.'>PN'.$_SESSION['lang']['stock']."</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['afkirbibit']." MN</td>\r\n    <td align=center ".$bgcoloz.'>Total '.$_SESSION['lang']['afkirbibit']."</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['afkirbibit']."(%)</td>\r\n    <td align=center ".$bgcoloz.">Doubletone</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['pengiriman']."</td>\r\n    <td align=center ".$bgcoloz.'>MN'.$_SESSION['lang']['stock']."</td>\r\n    <td align=center ".$bgcoloz.">Grand Total</td>\r\n    <td align=center ".$bgcoloz.'>'.$_SESSION['lang']['umur']."</td>\r\n</tr>\r\n</thead><tbody>";
$stab .= "<tr>\r\n    <td align=center ".$bgcoloz.">a</td>\r\n    <td align=center ".$bgcoloz."></td>   \r\n    <td align=center ".$bgcoloz."></td>\r\n    <td align=center ".$bgcoloz.">b</td>\r\n    <td align=center ".$bgcoloz.">c</td>\r\n    <td align=center ".$bgcoloz.">d=b-c</td>\r\n    <td align=center ".$bgcoloz.">e</td>\r\n    <td align=center ".$bgcoloz.">f</td>\r\n    <td align=center ".$bgcoloz.">g=d-e-f</td>\r\n    <td align=center ".$bgcoloz.">h</td>\r\n    <td align=center ".$bgcoloz.">i=e+h</td>\r\n    <td align=center ".$bgcoloz.">j=i/d*100</td>\r\n    <td align=center ".$bgcoloz.">k</td>\r\n    <td align=center ".$bgcoloz.">l</td>\r\n    <td align=center ".$bgcoloz.">m=f-h+k-l</td>\r\n    <td align=center ".$bgcoloz.">n=g+m</td>\r\n    <td align=center ".$bgcoloz.">o</td>\r\n</tr>";
if ($tanggal11=='') {  $tanggal11="a.tanggaltanam";}
else {$tanggal11 = "'".$tanggal11."'";}
$sData = "select distinct b.kodeorg, a.*,COALESCE(ROUND(DATEDIFF(".$tanggal11.",a.tanggaltanam)/365.25,2),0)*12 as umurbulan ".
    "from $dbname.bibitan_batch a ".
    "left join $dbname.bibitan_mutasi b on a.batch = b.batch ";
$arrayFilters = [];
if ($kdUnit!='') {
    $arrayFilters[] = " substr(b.kodeorg,1,4) ='" . $kdUnit. "' ";
}
if ($kdBatch!='') {
    $arrayFilters[] = " b.batch ='" . $kdBatch. "' ";
}
$filter = generateFilter($arrayFilters);
$sData.=$filter;
//if ($tanggal11!='') $sData.= " and a.tanggal<='".$tanggal11."' ";
$qData = mysql_query($sData) ;//|| exit(mysql_error($conns));
while ($rData = mysql_fetch_assoc($qData)) {
    $batches[$rData['batch']] = $rData['batch'];
    $dzArr[$rData['batch']]['batch'] = $rData['batch'];
    $dzArr[$rData['batch']]['tanggaltanam'] = $rData['tanggaltanam'];
    $dzArr[$rData['batch']]['jenisbibit'] = $rData['jenisbibit'];
    $dzArr[$rData['batch']]['kecambahterima'] = $rData['jumlahterima'];
    $dzArr[$rData['batch']]['seleksiawal'] = $rData['jumlahafkir'];
    $dzArr[$rData['batch']]['umurbibit'] = $rData['umurbulan'];
}
$sData = 'select * from '.$dbname.".bibitan_mutasi \r\n    where substr(kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk = '".$pt1."') \r\n        and substr(kodeorg,1,4) like '".$kebun1."%' and tanggal<='".$tanggal1."' and post='1'\r\n    ";
$qData = mysql_query($sData);// || exit(mysql_error($conns));
while ($rData = mysql_fetch_assoc($qData)) {
    $batches[$rData['batch']] = $rData['batch'];
    $dzArr[$rData['batch']]['batch'] = $rData['batch'];
    if ('TMB' == $rData['kodetransaksi']) {
        $dzArr[$rData['batch']]['kecambahtanam'] += $rData['jumlah'];
    }

    if ('TPB' == $rData['kodetransaksi'] && 'PN' == substr($rData['kodeorg'], 6, 2)) {
        $dzArr[$rData['batch']]['kecambahtanam'] += $rData['jumlah'];
    }

    if ('AFB' == $rData['kodetransaksi']) {
        if ('PN' == substr($rData['kodeorg'], 6, 2)) {
            $dzArr[$rData['batch']]['seleksibibitpn'] += $rData['jumlah'];
        }

        if ('MN' == substr($rData['kodeorg'], 6, 2)) {
            $dzArr[$rData['batch']]['seleksibibitmn'] += $rData['jumlah'];
        }
    }

    if ('TMB' == $rData['kodetransaksi'] && 'MN' == substr($rData['kodeorg'], 6, 2)) {
        $dzArr[$rData['batch']]['pindahbibitpnmn'] += $rData['jumlah'] * -1;
    }

    if ('DBT' == $rData['kodetransaksi']) {
        $dzArr[$rData['batch']]['bibitdoubletone'] += $rData['jumlah'];
    }

    if ('PNB' == $rData['kodetransaksi']) {
        $dzArr[$rData['batch']]['kirimbibit'] += $rData['jumlah'];
    }
}
$dzTot=[];

if (!empty($batches)) {
    foreach ($batches as $bat) {
        $stab .= '<tr class=rowcontent>';
        $stab .= '<td align=center>'.$dzArr[$bat]['batch'].'</td>';
        $stab .= '<td align=center>'.$dzArr[$bat]['tanggaltanam'].'</td>';
        $stab .= '<td align=left>'.$dzArr[$bat]['jenisbibit'].'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['kecambahterima']).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['seleksiawal']).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['kecambahtanam']).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['seleksibibitpn'] * -1).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['pindahbibitpnmn'] * -1).'</td>';
        $saldobibitpn = $dzArr[$bat]['kecambahtanam'] + $dzArr[$bat]['seleksibibitpn'] + $dzArr[$bat]['pindahbibitpnmn'];
        $stab .= '<td align=right>'.number_format($saldobibitpn).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['seleksibibitmn'] * -1).'</td>';
        $totalseleksi = $dzArr[$bat]['seleksibibitpn'] + $dzArr[$bat]['seleksibibitmn'];
        $stab .= '<td align=right>'.number_format($totalseleksi * -1).'</td>';
        $persenseleksi = ($totalseleksi * -1) / $dzArr[$bat]['kecambahtanam'] * 100;
        $stab .= '<td align=right>'.number_format($persenseleksi, 2).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['bibitdoubletone']).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['kirimbibit'] * -1).'</td>';
        $saldobibitmn = $dzArr[$bat]['pindahbibitpnmn'] * -1 + $dzArr[$bat]['seleksibibitmn'] + $dzArr[$bat]['bibitdoubletone'] + $dzArr[$bat]['kirimbibit'];
        $stab .= '<td align=right>'.number_format($saldobibitmn).'</td>';
        $saldobibit = $saldobibitpn + $saldobibitmn;
        $stab .= '<td align=right>'.number_format($saldobibit).'</td>';
        $stab .= '<td align=right>'.number_format($dzArr[$bat]['umurbibit'], 2).'</td>';
        $stab .= '</tr>';
        $dzTot['kecambahterima'] += $dzArr[$bat]['kecambahterima'];
        $dzTot['seleksiawal'] += $dzArr[$bat]['seleksiawal'];
        $dzTot['kecambahtanam'] += $dzArr[$bat]['kecambahtanam'];
        $dzTot['seleksibibitpn'] += $dzArr[$bat]['seleksibibitpn'];
        $dzTot['pindahbibitpnmn'] += $dzArr[$bat]['pindahbibitpnmn'];
        $dzTot['saldobibitpn'] += $saldobibitpn;
        $dzTot['seleksibibitmn'] += $dzArr[$bat]['seleksibibitmn'];
        $dzTot['totalseleksi'] += $totalseleksi;
        $dzTot['bibitdoubletone'] += $dzArr[$bat]['bibitdoubletone'];
        $dzTot['kirimbibit'] += $dzArr[$bat]['kirimbibit'];
        $dzTot['saldobibitmn'] += $saldobibitmn;
        $dzTot['saldobibit'] += $saldobibit;
    }
}

$stab .= '<tr class=rowcontent>';
$stab .= '<td align=center colspan=3 '.$bgcoloz.'>Total</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['kecambahterima']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['seleksiawal']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['kecambahtanam']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['seleksibibitpn'] * -1).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['pindahbibitpnmn'] * -1).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['saldobibitpn']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['seleksibibitmn'] * -1).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['totalseleksi'] * -1).'</td>';
$persenseleksiTot = ($dzTot['totalseleksi'] * -1) / $dzTot['kecambahtanam'] * 100;
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($persenseleksiTot, 2).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['bibitdoubletone']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['kirimbibit'] * -1).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['saldobibitmn']).'</td>';
$stab .= '<td align=right '.$bgcoloz.'>'.number_format($dzTot['saldobibit']).'</td>';
$stab .= '<td '.$bgcoloz.'></td>';
$stab .= '</tr>';
$stab .= '</tbody></table>';

//echoMessage(' tabel ',$proses,true);
switch ($proses) {
    case 'getkebun':
        $opt = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $opt .= "<option value='all'>".$_SESSION['lang']['all']."</option>";
        $sData = "select kodeorganisasi, namaorganisasi from $dbname.organisasi ".
            "where induk = '".$pt1."' and tipe = 'KEBUN' order by namaorganisasi desc";
        $opt = makeOption2( $sData,
            array("valueinit" => 'all', "captioninit" => $_SESSION['lang']['all']),
            array("valuefield" => 'kodeorganisasi', "captionfield" => 'namaorganisasi')
        );
//        $qData = mysql_query($sData);// || exit(mysql_error($conns));
//        while ($rData = mysql_fetch_assoc($qData)) {
//            $opt .= "<option value='".$rData['kodeorganisasi']."'>".$rData['namaorganisasi']."</option>";
//        }
//        echoMessage(' title ',$opt,true);
        echo "<option value=''>".$_SESSION['lang']['pilihdata']."</option>". $opt;
        break;
    case 'preview':
        echo $stab ;

        break;
    case 'excel':
        $stab .= 'Print Time:'.date('Y-m-d H:i:s').' By:'.$_SESSION['empl']['name'];
        $nop_ = 'RekapStokBibit_'.$pt1.'_'.$kebun1.'_sd_'.$tanggal1;
        if (0 < strlen($stab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stab)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can not convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'preview':
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\r\n            <td>".$_SESSION['lang']['batch']."</td>\r\n            <td>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td>".$_SESSION['lang']['saldo']."</td>\r\n            <td>".$_SESSION['lang']['jenisbibit']."</td>\r\n             <td>".$_SESSION['lang']['tgltanam']."</td>   \r\n            <td>".$_SESSION['lang']['umur'].' '.substr($_SESSION['lang']['afkirbibit'], 5)."</td>\r\n            </tr>\r\n            </thead><tbody id=containDataStock>";
        if ('' !== $kdUnit) {
            $where = "  kodeorg like '%".$kdUnit."%'";
        }

        if ('' !== $kdBatch) {
            $where .= " and batch='".$kdBatch."'";
        }
        $total=0;
        $sData = 'select distinct batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.'.bibitan_mutasi where '.$where.' group by batch,kodeorg order by tanggal desc ';
        $qData = mysql_query($sData);// || exit(mysql_error($conns));
        while ($rData = mysql_fetch_assoc($qData)) {
            $data = '';
            $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
            $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
            $rDataBatch = mysql_fetch_assoc($qDataBatch);
            $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
            $starttime = strtotime($rDataBatch['tanggaltanam']);
            $endtime = time();
            $jmlHari = ($endtime - $starttime) / (60 * 60 * 24 * 30);
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['batch'].'</td>';
            $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
            $tab .= '<td align=right>'.number_format($rData['jumlah'], 0).'</td>';
            $tab .= '<td>'.$rDataBatch['jenisbibit'].'</td>';
            $tab .= '<td>'.tanggalnormal($rDataBatch['tanggaltanam']).'</td>';
            $tab .= '<td align=right>'.number_format($jmlHari, 2).'</td>';
            $tab .= '</tr>';
            $total += $rData['jumlah'];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($total).'</td><td colspan=3></td></tr>';
        $tab .= '</tbody></table>';
        echo $tab;

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
                global $kdUnit;
                global $kdBatch;
                global $rData;
                global $optNm;
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
                $this->SetFont('Arial', 'B', 12);
                $this->SetFont('Arial', '', 8);
                $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['unit'], '', 0, 'L');
                $this->Cell(5, $height, ':', '', 0, 'L');
                $this->Cell(45 / 100 * $width, $height, $optNm[$kdUnit], '', 0, 'L');
                $this->Ln();
                if ('' == $kdBatch) {
                    $kdBatchdt = $_SESSION['lang']['all'];
                } else {
                    $kdBatchdt = $kdBatch;
                }

                $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['batch'], '', 0, 'L');
                $this->Cell(5, $height, ':', '', 0, 'L');
                $this->Cell(45 / 100 * $width, $height, $kdBatchdt, '', 0, 'L');
                $this->Ln();
                $this->SetFont('Arial', 'U', 12);
                $this->Cell($width, $height, $_SESSION['lang']['laporanStockBIbit'], 0, 1, 'C');
                $this->Ln();
                $this->SetFont('Arial', 'B', 7);
                $this->SetFillColor(220, 220, 220);
                $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
                $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['batch'], 1, 0, 'C', 1);
                $this->Cell(17 / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
                $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['saldo'], 1, 0, 'C', 1);
                $this->Cell(11 / 100 * $width, $height, $_SESSION['lang']['jenisbibit'], 1, 0, 'C', 1);
                $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tgltanam'].' '.substr($_SESSION['lang']['afkirbibit'], 5), 1, 0, 'C', 1);
                $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['umur'], 1, 1, 'C', 1);
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
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 7);
        if ('' !== $kdBatch) {
            $where = " and batch='".$kdBatch."'";
        }

        $sData = 'select distinct batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where.' group by batch,kodeorg order by tanggal desc ';
        $qData = mysql_query($sData);// || exit(mysql_error($conns));
        $total=0;
        while ($rData = mysql_fetch_assoc($qData)) {
            $data = '';
            $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
            $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
            $rDataBatch = mysql_fetch_assoc($qDataBatch);
            $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
            $starttime = strtotime($rDataBatch['tanggaltanam']);
            $endtime = strtotime($tglSkrng);
            $timediffSecond = abs($endtime - $starttime);
            $base_year = min(date('Y', $thnData), date('Y', $thnSkrng));
            $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
            $jmlHari = date('j', $diff) - 1;
            ++$no;
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(8 / 100 * $width, $height, $rData['batch'], 1, 0, 'C', 1);
            $pdf->Cell(17 / 100 * $width, $height, $optNm[$rData['kodeorg']], 1, 0, 'C', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format($rData['jumlah'], 0), 1, 0, 'R', 1);
            $pdf->Cell(11 / 100 * $width, $height, $rDataBatch['jenisbibit'], 1, 0, 'C', 1);
            $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($rDataBatch['tanggaltanam']), 1, 0, 'C', 1);
            $pdf->Cell(8 / 100 * $width, $height, $jmlHari, 1, 1, 'C', 1);
            $total += $rData['jumlah'];
        }
        $pdf->Cell(28 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($total), 1, 0, 'R', 1);
        $pdf->Cell(27 / 100 * $width, $height, '', 1, 1, 'R', 1);
        $pdf->Output();

        break;
//    case 'excel':
//        $tab .= "\r\n            <table>\r\n            <tr><td colspan=7 align=center>".$_SESSION['lang']['laporanStockBIbit']."</td></tr>\r\n            ".$tbl."\r\n            <tr><td colspan=7></td><td></td></tr>\r\n            </table>\r\n            <table cellpadding=1 cellspacing=1 border=1 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td bgcolor=#DEDEDE align=center>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['batch']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldo']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisbibit']."</td>\r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tgltanam']."</td>   \r\n            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['umur'].' '.substr($_SESSION['lang']['afkirbibit'], 5)."</td>\r\n            </tr>\r\n            </thead><tbody id=containDataStock>";
//        if ('' !== $kdBatch) {
//            $where = " and batch='".$kdBatch."'";
//        }
//
//        $sData = 'select distinct batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where.' group by batch,kodeorg order by tanggal desc ';
//        $qData = mysql_query($sData);// || exit(mysql_error($conns));
//        $total=0;
//        while ($rData = mysql_fetch_assoc($qData)) {
//            $data = '';
//            $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
//            $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
//            $rDataBatch = mysql_fetch_assoc($qDataBatch);
//            $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
//            $starttime = strtotime($rDataBatch['tanggaltanam']);
//            $endtime = strtotime($tglSkrng);
//            $timediffSecond = abs($endtime - $starttime);
//            $base_year = min(date('Y', $thnData), date('Y', $thnSkrng));
//            $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
//            $jmlHari = date('j', $diff) - 1;
//            ++$no;
//            $tab .= '<tr class=rowcontent>';
//            $tab .= '<td>'.$no.'</td>';
//            $tab .= '<td>'.$rData['batch'].'</td>';
//            $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
//            $tab .= '<td align=right>'.number_format($rData['jumlah'], 0).'</td>';
//            $tab .= '<td>'.$rDataBatch['jenisbibit'].'</td>';
//            $tab .= '<td>'.$rDataBatch['tanggaltanam'].'</td>';
//            $tab .= '<td align=right>'.$jmlHari.'</td>';
//            $tab .= '</tr>';
//            $total += $rData['jumlah'];
//        }
//        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['total'].'</td>';
//        $tab .= '<td align=right>'.number_format($total).'</td><td colspan=3></td></tr>';
//        $tab .= '</tbody></table>';
//        $tab .= 'Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
//        $nop_ = 'laporanStock_'.$kdUnit;
//        if (0 < strlen($tab)) {
//            if ($handle = opendir('tempExcel')) {
//                while (false !== ($file = readdir($handle))) {
//                    if ('.' !== $file && '..' !== $file) {
//                        @unlink('tempExcel/'.$file);
//                    }
//                }
//                closedir($handle);
//            }
//
//            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
//            if (!fwrite($handle, $tab)) {
//                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
//                exit();
//            }
//
//            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
//            closedir($handle);
//        }
//
//        break;
    default:
        break;
}
function getMonths($start, $end)
{
    $startqwe = explode('-', $start);
    $endqwe = explode('-', $end);
    list($startYear, $startMonth) = $startqwe;
    list($endYear, $endMonth) = $endqwe;

    return ($endYear - $startYear) * 12 + $endMonth - $startMonth;
}

?>