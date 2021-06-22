<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
require_once 'lib/devLibrary.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$periode = $_POST['periode'];
$tipeIntex = $_POST['tipeIntex'];
$unit = $_POST['unit'];
$kodeOrg = $_POST['kodeOrg'];
$brsKe = $_POST['brsKe'];
$tgl = tanggalsystem($_POST['tgl']);
$tglAfd = $_POST['tglAfd'];
$kdBlok = $_POST['kdBlok'];
$endKe = $_POST['endKe'];
$nospb = $_POST['nospb'];
$kodePabrik = $_POST['kodePabrik'];
$kodeUnit = $_POST['kodeUnit'];
switch ($proses) {
    case 'getKdorg':
        $optorg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        if (2 === $tipeIntex) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk = '".$_SESSION['empl']['induklokasitugas']."' order by namaorganisasi asc";
        } else {
            if (0 === $tipeIntex) {
                $sOrg = 'SELECT namasupplier,`kodetimbangan` FROM '.$dbname.".log_5supplier WHERE substring(kodekelompok,1,4)='S003' and kodetimbangan!='NULL' order by namasupplier asc";
            }
        }

        $qOrg = mysql_query($sOrg) ;
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            if (0 !== $tipeIntex) {
                $optorg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
            } else {
                $optorg .= '<option value='.$rOrg['kodetimbangan'].'>'.$rOrg['namasupplier'].'</option>';
            }
        }
        echo $optorg;

        break;
    case 'getAfdeling':
        echo "\r\n\t<img onclick=\"closeAfd(".$brsKe.");\" title=\"Tutup\" class=\"resicon\" src=\"images/close.gif\">\r\n\t<table cellspacing=1 border=0 class=sortable width=100%>\r\n\t\t<thead>\r\n\t\t\t<tr class=rowheader>\r\n\t\t\t\t<td>".$_SESSION['lang']['nospb']."</td>\r\n\t\t\t\t<td>Kode ".$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t</tr>\r\n\t\t</thead><tbody>";
        $sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where induk='".$kodeOrg."' and tipe='AFDELING'";
        $qOrg = mysql_query($sOrg) ;
        while ($rData = mysql_fetch_assoc($qOrg)) {
            $rDataOrg[$rData['kodeorganisasi']] = $rData;
        }
        $sGet = 'select substr(a.nospb,9,6) as kodeorganisasi,a.nospb from '.$dbname.'.kebun_spbdt a left join '.$dbname.".kebun_spbht b on a.nospb=b.nospb where a.nospb like '%".$kodeOrg."%' and b.tanggal ='".$tglAfd."' group by nospb";
        $qGet = mysql_query($sGet) ;
        while ($rGet = mysql_fetch_assoc($qGet)) {
            ++$no;
            echo '<tr class=rowcontent id=detail_'.$brsKe.'_'.$no.' onclick=detailBlok('.$brsKe.','.$no.") style=\"cursor: pointer;\" >\r\n\t\t\t\t<td id=nospb_".$brsKe.'_'.$no.'>'.$rGet['nospb']."</td>\r\n\t\t\t\t<td id=kdBlok_".$brsKe.'_'.$no.'>'.$rGet['kodeorganisasi']."</td>\r\n\t\t\t\t<td>".$rDataOrg[$rGet['kodeorganisasi']]['namaorganisasi']."</td>\r\n\t\t\t</tr><tr><td colspan=2><div id=detailBlok_".$brsKe.'_'.$no.'></div></td></tr>';
        }
        echo "\t\t</tbody>\r\n\t\t</table>";

        break;
    case 'getUnit':
        $sOrg = 'select distinct kodeorg from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%".$kodePabrik."%' order by kodeorg";
        $qOrg = mysql_query($sOrg) ;
        echo "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($rData = mysql_fetch_assoc($qOrg)) {
            echo '<option value='.$rData['kodeorg'].'>'.$rData['kodeorg'].'</option>';
        }

        break;
    case 'getAfdeling2':
        $sOrg2 = 'select distinct kodeorg from '.$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%".$kodePabrik."%' order by kodeorg";
        $qOrg2 = mysql_query($sOrg2) ;
        $unitintimbangan = '(';
        while ($rData2 = mysql_fetch_assoc($qOrg2)) {
            $unitintimbangan .= "'".$rData2['kodeorg']."',";
        }
        $unitintimbangan = substr($unitintimbangan, 0, -1);
        $unitintimbangan .= ')';
        if (')' === $unitintimbangan) {
            $unitintimbangan = "('')";
        }

        if ('' === $kodeUnit) {
            $sOrg = 'select kodeorganisasi from '.$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan.' order by kodeorganisasi';
        } else {
            $sOrg = 'select kodeorganisasi from '.$dbname.".organisasi where tipe = 'AFDELING' and induk like '%".$kodeUnit."%' order by kodeorganisasi";
        }

        $qOrg = mysql_query($sOrg) ;
        echo "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($rData = mysql_fetch_assoc($qOrg)) {
            echo '<option value='.$rData['kodeorganisasi'].'>'.$rData['kodeorganisasi'].'</option>';
        }

        break;
    case 'getPrestasi':
        echo "<table cellspacing=1 border=0 class=sortable>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>".$_SESSION['lang']['blok']."</td>\r\n\t<td>".$_SESSION['lang']['brondolan']."</td>\r\n\t<td>".$_SESSION['lang']['kgbjr']."</td>\r\n\t<td>".$_SESSION['lang']['janjang']."</td>\r\n\t</tr>\r\n\t</thead><tbody>\r\n\t";
        $sPrestasi = 'select * from '.$dbname.".kebun_spbdt where nospb='".$nospb."'";
        $qPrestasi = mysql_query($sPrestasi) ;
        $bPrestasi = mysql_num_rows($qPrestasi);
        if (0 < $bPrestasi) {
            while ($rPrestasi = mysql_fetch_assoc($qPrestasi)) {
                ++$no;
                echo '<tr class=rowcontent onclick="closeBlok('.$brsKe.','.$endKe.")\">\r\n\t\t\t<td>".$rPrestasi['blok']."</td>\r\n\t\t\t<td>".$rPrestasi['kgbjr']."</td>\r\n\t\t\t<td>".$rPrestasi['brondolan']."</td>\r\n\t\t\t<td align=right>".$rPrestasi['jjg']."</td>\r\n\t\t\t</tr>";
                $total += $rPrestasi['jjg'];
            }
            echo '<tr class="rowcontent"><td colspan=3>Total</td><td align=right>'.number_format($total, 2).'</td></tr>';
        } else {
            echo '<tr class=rowcontent onclick="closeBlok('.$brsKe.','.$endKe.')"><td colspan=5>Data Kosong</td></tr>';
        }

        echo '</tbody></table>';

        break;
    case 'preview':
       // if ('' !== $tipeIntex) {
            $where .= " and intex='".$tipeIntex."'";
            if ('' !== $unit) {
                if (0 === $tipeIntex) {
                    $where .= " and kodecustomer='".$unit."'";
                } else {
                    if (0 !== $tipeIntex) {
                        $where .= " and kodeorg='".$unit."' ";
                    }
                }
            }

            if ('' !== $periode) {
                $where .= " and tanggal like '%".$periode."%'";
            }

            echo "<table cellspacing=1 border=0 class=sortable>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td>".$_SESSION['lang']['janjang']."</td>\r\n\t\t<td>".$_SESSION['lang']['beratnormal']." (KG)</td>\r\n\t</tr>\r\n\t</thead>\r\n\t<tbody>";
//            $sData = "select kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal ".
//                "from $dbname.pabrik_timbangan ".
//                "where kodebarang='40000003' ".$where.' group by substr(tanggal,1,10)';
//            echoMessage(" data ",$sData,true);
            $sData = "SELECT  kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal,intex,
case 
	when intex=2 then (select namaorganisasi from organisasi where kodeorganisasi=kodeorg)
	when intex=0 then (select namasupplier from log_5supplier where kodetimbangan=kodecustomer)
END AS nama
from fastenvi_erpneodb_dev.pabrik_timbangan 
where   kodebarang='40000003' " .$where ." group by substr(tanggal,1,10)";
            $qData = mysql_query($sData) ;
            $brs = mysql_num_rows($qData);
            if (0 < $brs) {
                while ($rData = mysql_fetch_assoc($qData)) {
                    ++$no; $isi = '';
//                    if (0 !== $tipeIntex) {
//                        $sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
//                        $qNm = mysql_query($sNm) ;
//                        $rNm = mysql_fetch_assoc($qNm);
//                        $nm = $rNm['namaorganisasi'];
//                        $kd = $rData['kodeorg'];
//                        $isi = ' value='.$kd.'';
//                    } else {
//                        $sNm = 'select namasupplier from '.$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
//                        $qNm = mysql_query($sNm) ;
//                        $rNm = mysql_fetch_assoc($qNm);
//                        $nm = $rNm['namasupplier'];
//                        $stat = '';
//                        $isi = '';
//                    }
                    $nm=$rData['nama'];
                    echo "\r\n\t\t\t<tr class=rowcontent id=row_".$no.' '.$stat.">\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td id=kdOrg_".$no.' '.$isi.'>'.$nm."</td>\r\n\t\t\t<td id=tanggal_".$no.' value='.$rData['tanggal'].'>'.tanggalnormal($rData['tanggal'])."</td>\r\n\t\t\t<td  align=right>".number_format($rData['jjg'],2)."</td>\r\n\t\t\t<td align=right>".number_format($rData['netto'], 2)."</td>\r\n\t\t\t</tr>";
                    $subtota += $rData['netto'];
                }
                echo '<tr class=rowcontent ><td colspan=4 align=right>Total (KG)</td><td align=right>'.number_format($subtota, 2).'</td></tr>';
            } else {
                echo '<tr class=rowcontent><td colspan=5 align=center>Data Kosong</td></tr>';
            }

            break;
       // }

//        echo 'warning:Pilih salah satu Sumber TBS';
  //      exit();
    case 'pdf':
        $periode = $_GET['periode'];
        $tipeIntex = $_GET['tipeIntex'];
        $unit = $_GET['unit'];

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
        global $where;
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qAlamat = mysql_query($sAlmat) ;
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
        $this->Cell($width, $height, $_SESSION['lang']['rProdKebun'], 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell($width, $height, 'Periode : '.$tanggal, 0, 1, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(18 / 100 * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['janjang'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['beratnormal'].' (KG)', 1, 1, 'C', 1);
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
        $pdf->SetFont('Arial', '', 7);
        if ('' !== $tipeIntex) {
            $where .= " and intex='".$tipeIntex."'";
            if ('' !== $unit) {
                if (0 === $tipeIntex) {
                    $where .= " and kodecustomer='".$unit."'";
                } else {
                    if (0 !== $tipeIntex) {
                        $where .= " and kodeorg='".$unit."' ";
                    }
                }
            }

            if ('' !== $periode) {
                $where .= " and tanggal like '%".$periode."%'";
            }

            $sList = 'select kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal from '.$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' group by substr(tanggal,1,10)';
            $qList = mysql_query($sList) ;
            while ($rData = mysql_fetch_assoc($qList)) {
                if (0 !== $tipeIntex) {
                    $sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                    $qNm = mysql_query($sNm) ;
                    $rNm = mysql_fetch_assoc($qNm);
                    $nm = $rNm['namaorganisasi'];
                    $kd = $rData['kodeorg'];
                    $isi = ' value='.$kd.'';
                    $stat = ' onclick=getAfd('.$no.') style="cursor: pointer;"';
                } else {
                    $sNm = 'select namasupplier from '.$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                    $qNm = mysql_query($sNm) ;
                    $rNm = mysql_fetch_assoc($qNm);
                    $nm = $rNm['namasupplier'];
                    $stat = '';
                    $isi = '';
                }

                ++$no;
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(18 / 100 * $width, $height, $nm, 1, 0, 'C', 1);
                $pdf->Cell(12 / 100 * $width, $height, tanggalnormal($rData['tanggal']), 1, 0, 'C', 1);
                $pdf->Cell(10 / 100 * $width, $height, number_format($rData['jjg']), 1, 0, 'R', 1);
                $pdf->Cell(15 / 100 * $width, $height, number_format($rData['netto'], 2), 1, 1, 'R', 1);
                $subtota += $rData['netto'];
            }
            $pdf->Cell(43 / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, number_format($subtota, 2), 1, 1, 'R', 1);
            $pdf->Output();

            break;
        }

        echo 'warning:Pilih salah satu Sumber TBS';
        exit();
    case 'excel':
        $periode = $_GET['periode'];
        $tipeIntex = $_GET['tipeIntex'];
        $unit = $_GET['unit'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        if ('' !== $tipeIntex) {
            $where .= " and intex='".$tipeIntex."'";
            if ('' !== $unit) {
                if (0 === $tipeIntex) {
                    $where .= " and kodecustomer='".$unit."'";
                } else {
                    if (0 !== $tipeIntex) {
                        $where .= " and kodeorg='".$unit."' ";
                    }
                }
            }

            if ('' !== $periode) {
                $where .= " and tanggal like '%".$periode."%'";
            }

            $tab .= "<table cellspacing=1 border=0>\r\n\t<tr><td colspan=5 align=center>".$_SESSION['lang']['rProdKebun']."</td></tr>\r\n\t<tr><td colspan=2  align=left>Periode</td><td colspan=3 align=left>".$tanggal."</td></tr>\r\n\t</table>\r\n\t";
            $tab .= "<table cellspacing=1 border=1 class=sortable>\r\n\t<thead >\r\n\t<tr class=rowheader>\r\n\t\t<td bgcolor=#DEDEDE>No.</td>";
            $tab .= '<td bgcolor=#DEDEDE>'.$_SESSION['lang']['kodeorg'].'</td>';
            $tab .= '<td bgcolor=#DEDEDE>'.$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td bgcolor=#DEDEDE>".$_SESSION['lang']['janjang']."</td>\r\n\t\t<td bgcolor=#DEDEDE>".$_SESSION['lang']['beratnormal']." (KG)</td>\r\n\t</tr>\r\n\t</thead>\r\n\t<tbody>";
            $sData = 'select kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal from '.$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' group by substr(tanggal,1,10)';
            $qData = mysql_query($sData) ;
            $brs = mysql_num_rows($qData);
            if (0 < $brs) {
                while ($rData = mysql_fetch_assoc($qData)) {
                    ++$no;
                    if (0 !== $tipeIntex) {
                        $sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                        $qNm = mysql_query($sNm) ;
                        $rNm = mysql_fetch_assoc($qNm);
                        $nm = $rNm['namaorganisasi'];
                        $kd = $rData['kodeorg'];
                        $isi = ' value='.$kd.'';
                        $stat = ' onclick=getAfd('.$no.') style="cursor: pointer;"';
                    } else {
                        $sNm = 'select namasupplier from '.$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                        $qNm = mysql_query($sNm) ;
                        $rNm = mysql_fetch_assoc($qNm);
                        $nm = $rNm['namasupplier'];
                        $stat = '';
                        $isi = '';
                    }

                    $tab .= "\r\n\t\t\t<tr class=rowcontent id=row_".$no.' '.$stat.">\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td id=kdOrg_".$no.' '.$isi.'>'.$nm."</td>\r\n\t\t\t<td id=tanggal_".$no.'>'.tanggalnormal($rData['tanggal'])."</td>\r\n\t\t\t<td  align=right>".number_format($rData['jjg'])."</td>\r\n\t\t\t<td align=right>".number_format($rData['netto'], 2)."</td>\r\n\t\t\t</tr>";
                    $subtota += $rData['netto'];
                }
                $tab .= '<tr class=rowcontent ><td colspan=4 align=right>Total (KG)</td><td align=right>'.number_format($subtota, 2).'</td></tr>';
            } else {
                $tab .= '<tr class=rowcontent><td colspan=5 align=center>Data Kosong</td></tr>';
            }

            $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
            $tglSkrg = date('Ymd');
            $nop_ = 'LaporanProduksi_'.$unit.'_'.$periode;
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
                    echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                    exit();
                }

                echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
                closedir($handle);
            }

            break;
        }

        echo 'warning:Pilih salah satu Sumber TBS';
        exit();
    default:
        break;
}

?>