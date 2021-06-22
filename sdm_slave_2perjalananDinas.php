<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
('' != $_POST['kdOrg'] ? ($kdPt = $_POST['kdOrg']) : ($kdPt = $_GET['kdOrg']));
('' != $_POST['periode'] ? ($periode = $_POST['periode']) : ($periode = $_GET['periode']));
('' != $_POST['bagId'] ? ($bagian = $_POST['bagId']) : ($bagian = $_GET['bagId']));
('' != $_POST['karyawanId'] ? ($karyawanId = $_POST['karyawanId']) : ($karyawanId = $_GET['karyawanId']));
('' != $_POST['stat'] ? ($stat = $_POST['stat']) : ($stat = $_GET['stat']));
if(strlen($kdPt) > 3){
	$kdPt = substr($kdPt, 0, 4);
}
$sKd = 'select kodeorganisasi from '.$dbname.".organisasi where induk='".$kdPt."'";
//echo $sKd;
$qKd = mysql_query($sKd);
while ($rKd = mysql_fetch_assoc($qKd)) {
    ++$aro;
    if (1 == $aro) {
        $kodear = "'".$rKd['kodeorganisasi']."'";
    } else {
        $kodear .= ",'".$rKd['kodeorganisasi']."'";
    }
}
if ('' == $kdPt) {
    exit('Error: Working unit required');
}

//$where .= ' karyawanid in (select distinct karyawanid from '.$dbname.'.sdm_pjdinasht where kodeorg in('.$kodear.')) and tipekaryawan=5';
$where .= ' karyawanid in (select distinct karyawanid from '.$dbname.'.sdm_pjdinasht where kodeorg in('.$kodear.')) ';
$add .= ' and kodeorg in ('.$kodear.')';
if ('' != $karyawanId) {
    $where .= " and karyawanid='".$karyawanId."'";
}

if ('' != $bagian) {
    $where .= " and bagian='".$bagian."'";
}

$sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan,a.lokasitugas from '.$dbname.".datakaryawan a \r\n           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where \r\n            ".$where.' order by namakaryawan asc';
//echo $sGetKary;
$rGetkary = fetchData($sGetKary);
foreach ($rGetkary as $row => $kar) {
    $resData[$kar['karyawanid']][] = $kar['karyawanid'];
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
    $lokTugas[$kar['karyawanid']] = $kar['lokasitugas'];
}
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,kodeorganisasi');
switch ($proses) {
    case 'getKaryawan':
        if ('' == $kdPt) {
            exit('Error:Working unit required');
        }

        $optKary = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sKary = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan where ".$where.' order by namakaryawan asc';
        $qKary = mysql_query($sKary);
        while ($rKary = mysql_fetch_assoc($qKary)) {
            $optKary .= '<option value='.$rKary['karyawanid'].'>'.$rKary['namakaryawan'].'</option>';
        }
        echo $optKary;

        break;
    case 'preview':
        if ('' != $periode) {
            $add = " and substring(tglpertanggungjawaban,1,7)='".$periode."'";
        }

        if ('' != $stat) {
            $add .= " and lunas='".$stat."'";
        }

        $stro = 'select notransaksi,bytiket,dibayar from '.$dbname.'.sdm_pjdinasht where 1=1 '.$add;
		//echo $stro;
        $bytiket = [];
        $reso = mysql_query($stro);
        while ($baro = mysql_fetch_object($reso)) {
            $bytiket[$baro->notransaksi] = $baro->bytiket;
            $byum[$baro->notransaksi] = $baro->dibayar;
        }
        if (empty($reso)) {
            exit('Error: Not found');
        }

        $tab .= "<table cellspacing='1' border='0' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td>".$_SESSION['lang']['nama']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td>".$_SESSION['lang']['tanggaldinas']."</td>\r\n        <td>".$_SESSION['lang']['tanggalkembali']."</td>\r\n\r\n        <td>".$_SESSION['lang']['tanggalRelease']."</td>\r\n\r\n        <td>".$_SESSION['lang']['tujuan']." 1</td>\r\n        <td>".$_SESSION['lang']['tujuan']." 2</td>\r\n        <td>".$_SESSION['lang']['tujuan']." 3</td>\r\n        <td>".$_SESSION['lang']['tujuan'].' '.$_SESSION['lang']['uangmuka']."</td> \r\n        <td>".$_SESSION['lang']['uangmuka']."</td>\r\n        <td>".$_SESSION['lang']['sudahdipakai']."</td>\r\n        <td>".$_SESSION['lang']['biaya'].' Ticket</td>';
        $tab .= "</tr></thead>\r\n        <tbody>";

        foreach ($resData as $brsDt => $rData) {
            $sPjd = 'select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from '.$dbname.'.sdm_pjdinasdt b left join '.$dbname.".sdm_pjdinasht a on a.notransaksi=b.notransaksi where statushrd=1   ".$add." and a.karyawanid='".$rData[0]."' group by notransaksi";
            $qPjd = mysql_query($sPjd);
            while ($rPjd = mysql_fetch_assoc($qPjd)) {
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$namakar[$rData[0]].'</td>';
                $tab .= '<td>'.$nmJabatan[$rData[0]].'</td>';
                $tab .= '<td>'.$rPjd['notransaksi'].'</td>';
                $tab .= '<td>'.tanggalnormal($rPjd['tanggalperjalanan']).'</td>';
                $tab .= '<td>'.tanggalnormal($rPjd['tanggalkembali']).'</td>';
                $tab .= '<td>'.tanggalnormal($rPjd['tglpertanggungjawaban']).'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan1']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan2']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan3']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuanlain']].'</td>';
                $tab .= '<td align=right>'.number_format($byum[$rPjd['notransaksi']], 2).'</td>';
                $tab .= '<td align=right>'.number_format($rPjd['jmlhSetuju'], 2).'</td>';
                $tab .= '<td align=right>'.number_format($bytiket[$rPjd['notransaksi']], 2).'</td>';
                $tab .= '</tr>';
                $jmlTot[$rPjd['karyawanid']] += $rPjd['jmlhSetuju'];
                $jmlTiket[$rPjd['karyawanid']] += $bytiket[$rPjd['notransaksi']];
                $jmlhUm[$rPjd['karyawanid']] += $byum[$rPjd['notransaksi']];
            }
            if ('' != $jmlTot[$rData[0]] || 0 != $jmlTot[$rData[0]]) {
                $tab .= "<tr class=rowcontent style='font-weight:bold;'><td colspan=10>Total ".$namakar[$rData[0]].'</td><td  align=right>'.number_format($jmlhUm[$rData[0]], 2).'</td><td  align=right>'.number_format($jmlTot[$rData[0]], 2).'</td><td  align=right>'.number_format($jmlTiket[$rData[0]], 2).'</td></tr>';
            }

            $grandTot += $jmlTot[$rData[0]];
            $grandUm += $jmlhUm[$rData[0]];
            $grandTi += $jmlTiket[$rData[0]];
        }
        $tab .= '<tr class=rowcontent><td colspan=10>Grand Total </td><td  align=right>'.number_format($grandUm, 2).'</td><td  align=right>'.number_format($grandTot, 2).'</td><td  align=right>'.number_format($grandTi, 2).'</td></tr>';
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'pdf':
        if ('' != $periode) {
            $add = " and substring(tglpertanggungjawaban,1,7)='".$periode."'";
        }

        if ('' != $stat) {
            $add .= " and lunas='".$stat."'";
        }

        $stro = 'select notransaksi,bytiket from '.$dbname.'.sdm_pjdinasht where 1=1 '.$add;
        $bytiket = [];
        $reso = mysql_query($stro);
        while ($baro = mysql_fetch_object($reso)) {
            $bytiket[$baro->notransaksi] = $baro->bytiket;
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
        global $period;
        global $periode;
        global $kdOrg;
        global $kdeOrg;
        global $tgl1;
        global $tgl2;
        global $where;
        global $jmlHari;
        global $test;
        global $klmpkAbsn;
        global $tipeKary;
        global $resData;
        global $byum;
        $jmlHari = $jmlHari * 1.5;
        $cols = 247.5;
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
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['lapPjd'], '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(11 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['notransaksi'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tanggaldinas'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tanggalkembali'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tanggalRelease'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tujuan'].'1', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tujuan'].'2', 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['tujuan'].'3', 1, 0, 'C', 1);
        $this->Cell(13 / 100 * $width, $height, $_SESSION['lang']['tujuan'].'  '.$_SESSION['lang']['lain'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['uangmuka'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['sudahdipakai'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Ticket', 1, 1, 'C', 1);
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
        $stro = 'select notransaksi,bytiket,dibayar from '.$dbname.'.sdm_pjdinasht where 1=1 '.$add;
        $bytiket = [];
        $reso = mysql_query($stro);
        while ($baro = mysql_fetch_object($reso)) {
            $bytiket[$baro->notransaksi] = $baro->bytiket;
            $byum[$baro->notransaksi] = $baro->dibayar;
        }
        foreach ($resData as $brsDt => $rData) {
            $sPjd = 'select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from '.$dbname.'.sdm_pjdinasht a left join '.$dbname.".sdm_pjdinasdt b on a.notransaksi=b.notransaksi\r\n                where  statushrd=1  ".$add." and a.karyawanid='".$rData[0]."'group by notransaksi";
            $qPjd = mysql_query($sPjd);
            while ($rPjd = mysql_fetch_assoc($qPjd)) {
                ++$no;
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(11 / 100 * $width, $height, $namakar[$rData[0]], 1, 0, 'L', 1);
                $pdf->Cell(10 / 100 * $width, $height, $rPjd['notransaksi'], 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, tanggalnormal($rPjd['tanggalperjalanan']), 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, tanggalnormal($rPjd['tanggalkembali']), 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, tanggalnormal($rPjd['tglpertanggungjawaban']), 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, $rPjd['tujuan1'], 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, $rPjd['tujuan2'], 1, 0, 'C', 1);
                $pdf->Cell(6 / 100 * $width, $height, $rPjd['tujuan3'], 1, 0, 'C', 1);
                $pdf->Cell(13 / 100 * $width, $height, $rPjd['tujuanlain'], 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($byum[$rPjd['notransaksi']], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rPjd['jmlhSetuju'], 2), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($bytiket[$rPjd['notransaksi']], 2), 1, 1, 'R', 1);
                $jmlTot[$rPjd['karyawanid']] += $rPjd['jmlhSetuju'];
                $jmlhUm[$rPjd['karyawanid']] += $byum[$rPjd['notransaksi']];
                $jmlTiket[$rPjd['karyawanid']] += $bytiket[$rPjd['notransaksi']];
            }
            if ('' != $jmlTot[$rData[0]] || 0 != $jmlTot[$rData[0]]) {
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(78 / 100 * $width, $height, 'Total '.$namakar[$rData[0]], 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($jmlhUm[$rData[0]], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($jmlTot[$rData[0]], 2), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($jmlTiket[$rData[0]], 2), 1, 1, 'R', 1);
                $no = 0;
            }

            $grandTot += $jmlTot[$rData[0]];
            $grandUm += $jmlhUm[$rData[0]];
            $grandTi += $jmlTiket[$rData[0]];
        }
        $pdf->Cell(78 / 100 * $width, $height, 'Grand Total ', 1, 0, 'R', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($grandUm, 2), 1, 0, 'R', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($grandTot, 2), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($grandTi, 2), 1, 1, 'R', 1);
        $pdf->Output();

        break;
    case 'excel':
        if ('' != $periode) {
            $add = " and substring(tglpertanggungjawaban,1,7)='".$periode."'";
        }

        if ('' != $stat) {
            $add .= " and lunas='".$stat."'";
        }

        $stro = 'select notransaksi,bytiket,dibayar from '.$dbname.'.sdm_pjdinasht where 1=1 '.$add;
        $bytiket = [];
        $reso = mysql_query($stro);
        while ($baro = mysql_fetch_object($reso)) {
            $bytiket[$baro->notransaksi] = $baro->bytiket;
            $byum[$baro->notransaksi] = $baro->dibayar;
        }
        if ('' != $periode) {
            $add = " and substring(tglpertanggungjawaban,1,7)='".$periode."'";
        }

        if (empty($resData)) {
            exit('Error: Not Found');
        }

        $tab .= "\r\n                   <table cellspacing='1' border='1' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggaldinas']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggalkembali']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggalRelease']."</td>\r\n\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 1</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 2</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 3</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan'].' '.$_SESSION['lang']['lain']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['uangmuka']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dipakai']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biaya'].' Ticket</td>';
        $tab .= "</tr></thead>\r\n        <tbody>";
        foreach ($resData as $brsDt => $rData) {
            $sPjd = 'select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from '.$dbname.'.sdm_pjdinasht a left join '.$dbname.".sdm_pjdinasdt b on a.notransaksi=b.notransaksi\r\n                where  statushrd=1 ".$add." and a.karyawanid='".$rData[0]."'group by notransaksi";
            $qPjd = mysql_query($sPjd);
            while ($rPjd = mysql_fetch_assoc($qPjd)) {
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$namakar[$rData[0]].'</td>';
                $tab .= '<td>'.$nmJabatan[$rData[0]].'</td>';
                $tab .= '<td>'.$rPjd['notransaksi'].'</td>';
                $tab .= '<td>'.$rPjd['tanggalperjalanan'].'</td>';
                $tab .= '<td>'.$rPjd['tanggalkembali'].'</td>';
                $tab .= '<td>'.$rPjd['tglpertanggungjawaban'].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan1']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan2']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuan3']].'</td>';
                $tab .= '<td>'.$optOrg[$rPjd['tujuanlain']].'</td>';
                $tab .= '<td align=right>'.number_format($byum[$rPjd['notransaksi']], 2).'</td>';
                $tab .= '<td align=right>'.number_format($rPjd['jmlhSetuju'], 2).'</td>';
                $tab .= '<td align=right>'.number_format($bytiket[$rPjd['notransaksi']], 2).'</td>';
                $tab .= '</tr>';
                $jmlTot[$rPjd['karyawanid']] += $rPjd['jmlhSetuju'];
                $jmlTiket[$rPjd['karyawanid']] += $bytiket[$rPjd['notransaksi']];
                $jmlhUm[$rPjd['karyawanid']] += $byum[$rPjd['notransaksi']];
            }
            if ('' != $jmlTot[$rData[0]] || 0 != $jmlTot[$rData[0]]) {
                $tab .= "<tr style='font-weight:bold;' bgcolor=#DEDEDE ><td colspan=10>Total ".$namakar[$rData[0]].'</td><td  align=right>'.number_format($jmlhUm[$rData[0]], 2).'</td><td  align=right>'.number_format($jmlTot[$rData[0]], 2).'</td><td  align=right>'.number_format($jmlTiket[$rData[0]], 2).'</td></tr>';
            }

            $grandTot += $jmlTot[$rData[0]];
            $grandUm += $jmlhUm[$rData[0]];
            $grandTi += $jmlTiket[$rData[0]];
        }
        $tab .= '<tr bgcolor=#DEDEDE ><td colspan=10>Grand Total </td><td  align=right>'.number_format($grandUm, 2).'</td><td  align=right>'.number_format($grandTot, 2).'</td><td  align=right>'.number_format($grandTi, 2).'</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'rekapPerjalananDinas__'.$kdPt;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
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
    case 'getTgl':
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
            }
        }

        if ('' == $kdUnit) {
            $kdUnit = $_SESSION['empl']['lokasitugas'];
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit, 0, 4)."' and periode='".$tanggal."' ";
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
    case 'getKry':
        $optKry = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if (4 < strlen($kdeOrg)) {
            $where = " subbagian='".$kdeOrg."'";
        } else {
            $where = " lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null)";
        }

        $sKry = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
        }
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
        }
        echo $optKry.'###'.$optPeriode;

        break;
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$kdUnit."'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
        }
        echo $optPeriode;

        break;
    default:
        break;
}

?>