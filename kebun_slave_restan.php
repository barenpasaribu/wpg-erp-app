<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$jjgKrm = $_POST['jjgKrm'];
$umrRestan = $_POST['umrRestan'];
$kdBlokRestan = $_POST['kdBlokRestan'];
$jjgPanen = $_POST['jjgPanen'];
$cttn = $_POST['cttn'];
$tglRestan = tanggalsystem($_POST['tglRestan']);
$optnmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmkaryawan = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$kdBlokCari = $_POST['kdBlokCari'];
$periodeCari = $_POST['periodeCari'];
('' === $_POST['kdUnit'] ? ($kdUnit = $_GET['kdUnit']) : ($kdUnit = $_POST['kdUnit']));
('' === $_POST['afdId'] ? ($afdId = $_GET['afdId']) : ($afdId = $_POST['afdId']));
('' === $_POST['BlokId'] ? ($BlokId = $_GET['BlokId']) : ($BlokId = $_POST['BlokId']));
('' === $_POST['periodeId'] ? ($periodeId = $_GET['periodeId']) : ($periodeId = $_POST['periodeId']));
switch ($proses) {
    case 'saveTab1':
        if ('' === $jjgKrm || '' === $umrRestan || '' === $kdBlokRestan || '' === $cttn || '' === $jjgPanen || '' === $tglRestan) {
            exit('Error: Field Tidak Boleh Kosong');
        }

        $scek2 = 'select distinct kodeorg from '.$dbname.".kebun_restan where kodeorg='".$kdBlokRestan."' and tanggal='".$tglRestan."'";
        $qcek2 = mysql_query($scek2) || exit(mysql_error($scek));
        $rcek2 = mysql_num_rows($qcek2);
        if ('1' === $rcek2) {
            exit('Error:Data Sudah Ada');
        }

        $sInsert = 'insert into '.$dbname.".kebun_restan (kodeorg, tanggal, jjgpanen, jjgkirim, umurrestan,catatan) \r\n                   values('".$kdBlokRestan."','".$tglRestan."','".$jjgPanen."','".$jjgKrm."','".$umrRestan."','".$cttn."')";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        break;
    case 'loadData1':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select distinct * from '.$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by tanggal desc ";
        $query2 = mysql_query($sql2) ;
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = 'select distinct * from '.$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by tanggal desc limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData) ;
            while ($rData = mysql_fetch_assoc($qData)) {
                $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rData['kodeorg']."'";
                $qKg = mysql_query($sKg) ;
                $rKg = mysql_fetch_assoc($qKg);
                ++$no;
                $kgPanen = $rKg['bjr'] * $rData['jjgpanen'];
                $kgKrm = $rKg['bjr'] * $rData['jjgkirim'];
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jjgpanen'].'</td>';
                $tab .= '<td align=right>'.$kgPanen.'</td>';
                $tab .= '<td align=right>'.$rData['jjgkirim'].'</td>';
                $tab .= '<td align=right>'.$kgKrm.'</td>';
                $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['kodeorg']."' class=zImgBtn \r\n                            onclick=\"filFieldHead('".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."','".$rData['jjgpanen']."','".$rData['jjgkirim']."','".$rData['umurrestan']."','".$rData['catatan']."')\" src='images/application/application_edit.png'/>";
                $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['kodeorg']."' class=zImgBtn onclick=\"delFieldHead('".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."')\" src='images/application/application_delete.png'/>";
                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'getCariData':
        if ('' !== $periodeCari) {
            $where .= " and tanggal like '".$periode."%'";
        }

        if ('' !== $kdBlokCari) {
            $where .= " and kodeorg like '".$kdBlokCari."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select distinct * from '.$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$where.' order by tanggal desc ';
        $query2 = mysql_query($sql2) ;
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $sData = 'select distinct * from '.$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ".$where.' order by tanggal desc limit '.$offset.','.$limit.' ';
            $qData = mysql_query($sData) ;
            while ($rData = mysql_fetch_assoc($qData)) {
                $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rData['kodeorg']."'";
                $qKg = mysql_query($sKg) ;
                $rKg = mysql_fetch_assoc($qKg);
                ++$no;
                $kgPanen = $rKg['bjr'] * $rData['jjgpanen'];
                $kgKrm = $rKg['bjr'] * $rData['jjgkirim'];
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
                $tab .= '<td align=right>'.$rData['jjgpanen'].'</td>';
                $tab .= '<td align=right>'.$kgPanen.'</td>';
                $tab .= '<td align=right>'.$rData['jjgkirim'].'</td>';
                $tab .= '<td align=right>'.$kgKrm.'</td>';
                $tab .= "<td  align=center colspan=2><img id='detail_edit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['kodeorg']."' class=zImgBtn \r\n                            onclick=\"filFieldHead('".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."','".$rData['jjgpanen']."','".$rData['jjgkirim']."','".$rData['umurrestan']."','".$rData['catatan']."')\" src='images/application/application_edit.png'/>";
                $tab .= "&nbsp;<img id='detail_del' style='cursor:pointer;' title='Delete ".$rData['kodeorg']."' class=zImgBtn onclick=\"delFieldHead('".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."')\" src='images/application/application_delete.png'/>";
                $tab .= '</tr>';
            }
            $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBastCr(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBastCr(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'update1':
        if ('' === $jjgKrm || '' === $umrRestan || '' === $kdBlokRestan || '' === $cttn || '' === $jjgPanen || '' === $tglRestan) {
            exit('Error: Field Tidak Boleh Kosong');
        }

        $sInsert = 'update '.$dbname.".kebun_restan set  jjgpanen='".$jjgPanen."', jjgkirim='".$jjgKrm."', umurrestan='".$umrRestan."',catatan='".$cttn."'\r\n                  where kodeorg='".$kdBlokRestan."' and  tanggal='".$tglRestan."'";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_restan where kodeorg='".$kdBlokRestan."' and  tanggal='".$tglRestan."'";
        if (!mysql_query($sDel)) {
            echo 'DB Error : '.$sDel."\n".mysql_error($conn);
        }

        break;
    case 'getAfd':
        $optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        if ('' !== $kdUnit) {
            $sAfd = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$kdUnit."' and tipe='AFDELING'";
            $qAfd = mysql_query($sAfd) ;
            while ($rAfd = mysql_fetch_assoc($qAfd)) {
                $optUnit .= "<option value='".$rAfd['kodeorganisasi']."'>".$rAfd['namaorganisasi'].'</option>';
            }
        }

        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".kebun_restan where kodeorg like '".$kdUnit."%'";
        $qPeriode = mysql_query($sPeriode) || exit(mysql_error($sPeriode));
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
        }
        echo $optUnit.'###'.$optPeriode;

        break;
    case 'getBlok':
        $optUnit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sAfd = 'select distinct kodeorg from '.$dbname.".setup_blok where kodeorg like '".$afdId."%'";
        $qAfd = mysql_query($sAfd) ;
        while ($rAfd = mysql_fetch_assoc($qAfd)) {
            $optUnit .= "<option value='".$rAfd['kodeorg']."'>".$optNm[$rAfd['kodeorg']].'</option>';
        }
        echo $optUnit;

        break;
    case 'preview':
        if ('' !== $kdUnit) {
            $where = "kodeorg like '".$kdUnit."%'";
            if ('' !== $BlokId) {
                $where .= " and kodeorg='".$BlokId."'";
            }

            if ('' !== $periodeId) {
                $where .= " and tanggal like '".$periodeId."%'";
                $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
                $tab .= '<thead><tr>';
                $tab .= '<td rowspan=2>'.$_SESSION['lang']['tanggal'].'</td>';
                $tab .= '<td rowspan=2>'.$_SESSION['lang']['blok'].'</td>';
                $tab .= '<td colspan=2 align=center>Panen</td>';
                $tab .= '<td colspan=2 align=center>Kirim</td>';
                $tab .= '<td colspan=2 align=center>Restan</td>';
                $tab .= '</tr><tr><td>Janjang</td><td>Kg</td><td>Janjang</td><td>Kg</td><td>Janjang</td><td>Kg</td>';
                $tab .= '</tr></thead><tbody>';
                $sData = 'select distinct * from '.$dbname.'.kebun_restan where '.$where.' order by tanggal asc';
                $qData = mysql_query($sData) ;
                $row = mysql_num_rows($qData);
                if (0 !== $row) {
                    while ($rdata = mysql_fetch_assoc($qData)) {
                        $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                        $qKg = mysql_query($sKg) ;
                        $rKg = mysql_fetch_assoc($qKg);
                        $panenKg = $rdata['jjgpanen'] * $rKg['bjr'];
                        $KirimKg = $rdata['jjgkirim'] * $rKg['bjr'];
                        $resJjg = $rdata['jjgpanen'] - $rdata['jjgkirim'];
                        $resKg = $resJjg * $rKg['bjr'];
                        $tab .= '<tr class=rowcontent>';
                        $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
                        $tab .= '<td align=right>'.$optNm[$rdata['kodeorg']].'</td>';
                        $tab .= '<td align=right>'.$rdata['jjgpanen'].'</td>';
                        $tab .= '<td align=right>'.$panenKg.'</td>';
                        $tab .= '<td align=right>'.$rdata['jjgkirim'].'</td>';
                        $tab .= '<td align=right>'.$KirimKg.'</td>';
                        $tab .= '<td align=right>'.$resJjg.'</td>';
                        $tab .= '<td align=right>'.$resKg.'</td>';
                    }
                } else {
                    $tab .= '<tr class=rowcontent><td colspan=8>'.$_SESSION['lang']['dataempty'].'</td></tr>';
                }

                $tab .= '</tbody></table>';
                echo $tab;

                break;
            }

            exit('Error:Periode Tidak Boleh Kosong');
        }

        exit('Error:Unit Tidak Boleh Kosong');
    case 'excel':
        if ('' !== $kdUnit) {
            $where = "kodeorg like '".$kdUnit."%'";
            if ('' !== $BlokId) {
                $where .= " and kodeorg='".$BlokId."'";
            }

            if ('' !== $periodeId) {
                $where .= " and tanggal like '".$periodeId."%'";
                $bgcoloraja = 'bgcolor=#DEDEDE align=center';
                $brdr = 1;
                $tab .= "\r\n            <table>\r\n            <tr><td colspan=5 align=left><b>LAPORAN RESTAN</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periode), 1, 7)."</b></td></tr>\r\n            <tr><td colspan=5 align=left>".$_SESSION['lang']['unit'].' : '.$optNm[$kdUnit]." </td></tr>\r\n            <tr><td colspan=5 align=left>&nbsp;</td></tr>\r\n            </table>";
                $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
                $tab .= '<thead><tr>';
                $tab .= '<td rowspan=2 '.$bgcoloraja.'>'.$_SESSION['lang']['tanggal'].'</td>';
                $tab .= '<td rowspan=2 '.$bgcoloraja.'>'.$_SESSION['lang']['blok'].'</td>';
                $tab .= '<td colspan=2 align=center  '.$bgcoloraja.'>Panen</td>';
                $tab .= '<td colspan=2 align=center  '.$bgcoloraja.'>Kirim</td>';
                $tab .= '<td colspan=2 align=center  '.$bgcoloraja.'>Restan</td>';
                $tab .= '</tr><tr><td  '.$bgcoloraja.'>Janjang</td><td  '.$bgcoloraja.'>Kg</td><td  '.$bgcoloraja.'>Janjang</td><td  '.$bgcoloraja.'>Kg</td><td  '.$bgcoloraja.'>Janjang</td><td  '.$bgcoloraja.'>Kg</td>';
                $tab .= '</tr></thead><tbody>';
                $sData = 'select distinct * from '.$dbname.'.kebun_restan where '.$where.' order by tanggal asc';
                $qData = mysql_query($sData) ;
                $row = mysql_num_rows($qData);
                if (0 !== $row) {
                    while ($rdata = mysql_fetch_assoc($qData)) {
                        $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                        $qKg = mysql_query($sKg) ;
                        $rKg = mysql_fetch_assoc($qKg);
                        $panenKg = $rdata['jjgpanen'] * $rKg['bjr'];
                        $KirimKg = $rdata['jjgkirim'] * $rKg['bjr'];
                        $resJjg = $rdata['jjgpanen'] - $rdata['jjgkirim'];
                        $resKg = $resJjg * $rKg['bjr'];
                        $tab .= '<tr class=rowcontent>';
                        $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
                        $tab .= '<td align=right>'.$optNm[$rdata['kodeorg']].'</td>';
                        $tab .= '<td align=right>'.$rdata['jjgpanen'].'</td>';
                        $tab .= '<td align=right>'.$panenKg.'</td>';
                        $tab .= '<td align=right>'.$rdata['jjgkirim'].'</td>';
                        $tab .= '<td align=right>'.$KirimKg.'</td>';
                        $tab .= '<td align=right>'.$resJjg.'</td>';
                        $tab .= '<td align=right>'.$resKg.'</td>';
                    }
                } else {
                    $tab .= '<tr class=rowcontent><td colspan=8>'.$_SESSION['lang']['dataempty'].'</td></tr>';
                }

                $tab .= '</tbody></table>';
                $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
                $dte = date('Hms');
                $nop_ = 'laporanRestan_'.$dte;
                $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
                gzwrite($gztralala, $tab);
                gzclose($gztralala);
                echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls.gz';\r\n            </script>";

                break;
            }

            exit('Error:Periode Tidak Boleh Kosong');
        }

        exit('Error:Unit Tidak Boleh Kosong');
    case 'pdf':

class PDF extends FPDF
{
    public function Header()
    {
        global $periodeId;
        global $dataAfd;
        global $kdUnit;
        global $optNm;
        global $dbname;
        global $where;
        global $optSatuan;
        global $optNmBrg;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(250, $height, strtoupper('LAPORAN RESTAN'), 0, 0, 'L');
        $this->Cell(270, $height, $_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeId), 1, 7), 0, 1, 'R');
        $tinggiAkr = $this->GetY();
        $ksamping = $this->GetX();
        $this->SetY($tinggiAkr);
        $this->SetX($ksamping);
        $this->Cell($width, $height, $_SESSION['lang']['unit'].' : '.$optNm[$kdUnit], 0, 1, 'L');
        $this->SetFillColor(220, 220, 220);
        $this->Cell(55, $height, 'Tanggal', TLR, 0, 'C', 1);
        $this->Cell(55, $height, $_SESSION['lang']['blok'], TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Panen', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Kirim', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Restan', TLR, 1, 'C', 1);
        $this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
        $this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 1, 'C', 1);
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
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 7);
        if ('' !== $kdUnit) {
            $where = "kodeorg like '".$kdUnit."%'";
            if ('' !== $BlokId) {
                $where .= " and kodeorg='".$BlokId."'";
            }

            if ('' !== $periodeId) {
                $where .= " and tanggal like '".$periodeId."%'";
                $sData = 'select distinct * from '.$dbname.'.kebun_restan where '.$where.' order by tanggal asc';
                $qData = mysql_query($sData) ;
                $row = mysql_num_rows($qData);
                if (0 !== $row) {
                    while ($rdata = mysql_fetch_assoc($qData)) {
                        $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                        $qKg = mysql_query($sKg) ;
                        $rKg = mysql_fetch_assoc($qKg);
                        $panenKg = $rdata['jjgpanen'] * $rKg['bjr'];
                        $KirimKg = $rdata['jjgkirim'] * $rKg['bjr'];
                        $resJjg = $rdata['jjgpanen'] - $rdata['jjgkirim'];
                        $resKg = $resJjg * $rKg['bjr'];
                        $pdf->Cell(55, $height, tanggalnormal($rdata['tanggal']), TBLR, 0, 'C', 1);
                        $pdf->Cell(55, $height, $rdata['kodeorg'], TBLR, 0, 'C', 1);
                        $pdf->Cell(45, $height, $rdata['jjgpanen'], TBLR, 0, 'R', 1);
                        $pdf->Cell(45, $height, $panenKg, TBLR, 0, 'R', 1);
                        $pdf->Cell(45, $height, $rdata['jjgkirim'], TBLR, 0, 'R', 1);
                        $pdf->Cell(45, $height, $KirimKg, TBLR, 0, 'R', 1);
                        $pdf->Cell(45, $height, $resJjg, TBLR, 0, 'R', 1);
                        $pdf->Cell(45, $height, $resKg, TBLR, 1, 'R', 1);
                    }
                } else {
                    $pdf->Cell(380, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'C', 1);
                }

                $pdf->Output();

                break;
            }

            exit('Error:Periode Tidak Boleh Kosong');
        }

        exit('Error:Unit Tidak Boleh Kosong');
    default:
        break;
}

?>