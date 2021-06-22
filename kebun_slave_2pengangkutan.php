<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];
$idKebun = $_POST['idKebun'];
switch ($proses) {
    case 'preview':
        if ('' === $periode) {
            echo 'warning : Periode masih kosong';
            exit();
        }

        echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>".substr($_SESSION['lang']['nomor'], 0, 2).".</td>\r\n\t<td>".$_SESSION['lang']['nospb']."</td>\r\n\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t<td>".$_SESSION['lang']['status']."</td>\r\n\t<td>".$_SESSION['lang']['bjr']."</td>\r\n\t<td>".$_SESSION['lang']['janjang']."</td>\r\n\t<td>".$_SESSION['lang']['brondolan']."</td>\r\n\t<td>".$_SESSION['lang']['mentah']."</td>\r\n\t<td>".$_SESSION['lang']['busuk']."</td>\r\n\t<td>".$_SESSION['lang']['matang']."</td>\r\n\t<td>".$_SESSION['lang']['lewatmatang']."</td>\r\n\t<td>".$_SESSION['lang']['kgbjr']."</td>\r\n\t<td>".$_SESSION['lang']['kgwb']."</td>\r\n\t<td>".$_SESSION['lang']['totalkg']."</td>\r\n\t</tr>\r\n\t</thead><tbody>";
        $sql = 'select a.nospb,a.tanggal,a.posting from '.$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc";
        $query = mysql_query($sql) ;
        $row = mysql_num_rows($query);
        if (0 < $row) {
            while ($res = mysql_fetch_assoc($query)) {
                ++$no;
                $sSpbDet = 'select sum(bjr) as Bjr,sum(jjg) as Janjang,sum(brondolan) as Brondolan,sum(mentah) as Mentah,sum(busuk) as Busuk,sum(matang) as Matang,sum(lewatmatang) as Lewatmatang,sum(kgbjr) as kgBjr,sum(kgwb) as kGwb,sum(totalkg) as totaLkg from '.$dbname.".kebun_spbdt where nospb='".$res['nospb']."'";
                $qSpbDet = mysql_query($sSpbDet) ;
                $rSpbDet = mysql_fetch_assoc($qSpbDet);
                $srow = 'select blok from '.$dbname.".kebun_spbdt where nospb='".$res['nospb']."'";
                $qrow = mysql_query($srow) ;
                $rRow = mysql_num_rows($qrow);
                $bjrR = $rSpbDet['Bjr'] / $rRow;
                $arrPost = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $arr = 'nospb'.'##'.$res['nospb'];
                echo "<tr class=rowcontent onclick=\"zDetail(event,'kebun_slave_2pengangkutan.php','".$arr."')\" style='cursor:pointer;'>\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$res['nospb']."</td>\r\n\t\t\t<td>".tanggalnormal($res['tanggal'])."</td>\r\n\t\t\t<td>".$arrPost[$res['posting']]."</td>\t\t\r\n\t\t\t<td align=\"right\">".number_format($bjrR, 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Janjang'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Brondolan'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Mentah'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Busuk'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Matang'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Lewatmatang'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['kgBjr'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['kGwb'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['totaLkg'], 2)."</td>\r\n\t\t\t</tr>\r\n\t\t\t";
            }
        } else {
            echo '<tr class=rowcontent align=center><td colspan=14>Not Found</td></tr>';
        }

        echo '</tbody></table>';

        break;
    case 'pdf':
        $periode = $_GET['periode'];
        $idKebun = $_GET['idKebun'];
        if ('' === $periode) {
            echo 'warning : Periode masih kosong';
            exit();
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
        global $periode;
        global $kdBrg;
        global $idKebun;
        $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$idKebun."'";
        $qOrg = mysql_query($sOrg) ;
        $rOrg = mysql_fetch_assoc($qOrg);
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
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
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', '', 7);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $periode, '', 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $idKebun.'-'.$rOrg['namaorganisasi'], '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'U', 9);
        $this->Cell($width, $height, $_SESSION['lang']['laporanPengangkutan'], 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nospb'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['status'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['bjr'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['janjang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['brondolan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['mentah'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['busuk'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['matang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['lewatmatang'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['kgbjr'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['kgwb'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['totalkg'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'pt', 'A4');
        $pdf->lMargin = 10;
        $pdf->rMargin = 10;
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $sDet = 'select a.nospb,a.tanggal,a.posting from '.$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc ";
        $qDet = mysql_query($sDet) ;
        $row = mysql_num_rows($qDet);
        if (0 < $row) {
            while ($rDet = mysql_fetch_assoc($qDet)) {
                ++$no;
                $arrPost = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $sSpbDet = 'select sum(bjr) as Bjr,sum(jjg) as Janjang,sum(brondolan) as Brondolan,sum(mentah) as Mentah,sum(busuk) as Busuk,sum(matang) as Matang,sum(lewatmatang) as Lewatmatang,sum(kgbjr) as kgBjr,sum(kgwb) as kGwb,sum(totalkg) as totaLkg from '.$dbname.".kebun_spbdt where nospb='".$rDet['nospb']."'";
                $qSpbDet = mysql_query($sSpbDet) ;
                $rSpbDet = mysql_fetch_assoc($qSpbDet);
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(15 / 100 * $width, $height, $rDet['nospb'], 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($rDet['tanggal']), 1, 0, 'C', 1);
                $pdf->Cell(8 / 100 * $width, $height, $arrPost[$rDet['posting']], 1, 0, 'L', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['Bjr'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['Janjang'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['Brondolan'], 2), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($rSpbDet['Mentah'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['Busuk'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['Matang'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['Lewatmatang'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['kgBjr'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['kGwb'], 2), 1, 0, 'R', 1);
                $pdf->Cell(6 / 100 * $width, $height, number_format($rSpbDet['totaLkg'], 2), 1, 1, 'R', 1);
            }
        } else {
            $pdf->Cell(96 / 100 * $width, $height, 'Not Found', 1, 1, 'C', 1);
        }

        $pdf->Output();

        break;
    case 'excel':
        $periode = $_GET['periode'];
        $idKebun = $_GET['idKebun'];
        if ('' === $periode) {
            echo 'warning : Periode masih kosong';
            exit();
        }

        $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$idKebun."'";
        $qOrg = mysql_query($sOrg) ;
        $rOrg = mysql_fetch_assoc($qOrg);
        $stream .= "\r\n\t\t\t<table>\r\n\t\t\t<tr><td colspan=14 align=center><b>".$_SESSION['lang']['laporanPengangkutan']."</b></td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['periode'].'</td><td>'.$periode."</td></tr>\r\n\t\t\t<tr><td colspan=3>".$_SESSION['lang']['kodeorg'].'</td><td>'.$idKebun.'-'.$rOrg['namaorganisasi']."</td></tr>\r\n\t\t\t<tr><td colspan=3></td><td></td></tr>\r\n\t\t\t</table>\r\n\t\t\t<table border=1>\r\n\t\t\t<tr>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>No.</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nospb']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['status']."</td>\t\t\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bjr']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['janjang']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mentah']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['busuk']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['matang']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lewatmatang']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kgbjr']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kgwb']."</td>\r\n\t\t\t\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['totalkg']."</td>\t\t\t\r\n\t\t\t</tr>";
        $strx = 'select a.nospb,a.tanggal,a.posting from '.$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc";
        $resx = mysql_query($strx) ;
        $row = mysql_fetch_row($resx);
        if ($row < 1) {
            $stream .= "\t<tr class=rowcontent>\r\n\t\t\t<td colspan=14 align=center>Not Found</td></tr>\r\n\t\t\t";
        } else {
            $no = 0;
            $resx = mysql_query($strx);
            while ($barx = mysql_fetch_assoc($resx)) {
                ++$no;
                $arrPost = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $arr = 'nospb'.'##'.$res['nospb'];
                $stream .= '<tr class=rowcontent>';
                $sSpbDet = 'select sum(bjr) as Bjr,sum(jjg) as Janjang,sum(brondolan) as Brondolan,sum(mentah) as Mentah,sum(busuk) as Busuk,sum(matang) as Matang,sum(lewatmatang) as Lewatmatang,sum(kgbjr) as kgBjr,sum(kgwb) as kGwb,sum(totalkg) as totaLkg from '.$dbname.".kebun_spbdt where nospb='".$barx['nospb']."'";
                $qSpbDet = mysql_query($sSpbDet) ;
                $rSpbDet = mysql_fetch_assoc($qSpbDet);
                $arrPost = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $arr = 'nospb'.'##'.$res['nospb'];
                $stream .= "\r\n\t\t\t<td>".$no."</td>\r\n\t\t\t<td>".$barx['nospb']."</td>\r\n\t\t\t<td>".$barx['tanggal']."</td>\r\n\t\t\t<td>".$arrPost[$barx['posting']]."</td>\t\t\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Bjr'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Janjang'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Brondolan'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Mentah'], 2)."</td>\r\n\t\t\t<td align=\"right\">".number_format($rSpbDet['Busuk'], 2)."</td>\r\n\t\t\t<td  align=\"right\">".number_format($rSpbDet['Matang'], 2)."</td>\r\n\t\t\t<td  align=\"right\">".number_format($rSpbDet['Lewatmatang'], 2)."</td>\r\n\t\t\t<td  align=\"right\">".number_format($rSpbDet['kgBjr'], 2)."</td>\r\n\t\t\t<td  align=\"right\">".number_format($rSpbDet['kGwb'], 2)."</td>\r\n\t\t\t<td  align=\"right\">".number_format($rSpbDet['totaLkg'], 2)."</td>\r\n\t\t\t</tr>\r\n\t\t\t";
            }
        }

        $stream .= '</table>';
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'laporanPengangkutanPanen';
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
                echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
            closedir($handle);
        }

        break;
    case 'getDetail':
        $nospb = $_GET['nospb'];

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
        global $nospb;
        $sHed = 'select  a.nospb,a.kodeorg,a.tanggal,a.posting from '.$dbname.".kebun_spbht a where a.nospb='".$nospb."'";
        $qHead = mysql_query($sHed) ;
        $rHead = mysql_fetch_assoc($qHead);
        $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rHead['kodeorg']."'";
        $qOrg = mysql_query($sOrg) ;
        $rOrg = mysql_fetch_assoc($qOrg);
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        $this->SetFont('Arial', '', 8);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['nospb'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $rHead['nospb'], '', 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, tanggalnormal($rHead['tanggal']), '', 0, 'L');
        $this->Ln();
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(45 / 100 * $width, $height, $rOrg['namaorganisasi'], '', 0, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'U', 9);
        $this->Cell($width, $height, $_SESSION['lang']['laporanPengangkutan'], 0, 1, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['bjr'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['janjang'], 1, 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['brondolan'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['mentah'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['busuk'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['matang'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['lewatmatang'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['kgbjr'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['kgwb'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['totalkg'], 1, 1, 'C', 1);
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
        $height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $sDet = 'select * from '.$dbname.".kebun_spbdt where nospb='".$nospb."'";
        $qDet = mysql_query($sDet) ;
        $row = mysql_num_rows($qDet);
        if (0 < $row) {
            while ($rSpbDet = mysql_fetch_assoc($qDet)) {
                ++$no;
                $arrPost = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(10 / 100 * $width, $height, $rSpbDet['blok'], 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['bjr'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['jjg'], 2), 1, 0, 'R', 1);
                $pdf->Cell(9 / 100 * $width, $height, number_format($rSpbDet['brondolan'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['mentah'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['busuk'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['matang'], 2), 1, 0, 'R', 1);
                $pdf->Cell(10 / 100 * $width, $height, number_format($rSpbDet['lewatmatang'], 2), 1, 0, 'R', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['kgbjr'], 2), 1, 0, 'C', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['kgwb'], 2), 1, 0, 'C', 1);
                $pdf->Cell(8 / 100 * $width, $height, number_format($rSpbDet['totalkg'], 2), 1, 1, 'C', 1);
            }
        } else {
            $pdf->Cell(68 / 100 * $width, $height, 'Not Found', 1, 1, 'C', 1);
        }

        $pdf->Output();

        break;
    default:
        break;
}

?>