<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST)) {
    $param = $_POST;
}

if ('pdf' == $_GET['proses']) {
    $param = $_GET;
}

$optNmkar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optKdJbtn = makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan');
$optNmJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$brd = 0;
$bgclr = '';
if ('excel' == $_GET['proses']) {
    $brd = 1;
    $bgclr = ' bgcolor=#DEDEDE';
}

if ('' != $param['deptId']) {
    $whr .= "and departemen='".$param['deptId']."' ";
}

if ('' != $param['periode'] || '' != $param['periodesmp']) {
    $whr .= "and left(tanggal,7) between '".$param['periode']."' and  '".$param['periodesmp']."'";
    $whr2 = " and left(tanggal,7) between '".$param['periode']."' and  '".$param['periodesmp']."'";
    $sdata = 'select * from '.$dbname.".sdm_permintaansdm\r\n        where notransaksi!='' ".$whr.' order by tanggal asc';
    $qData = mysql_query($sdata);
    while ($rdata = mysql_fetch_assoc($qData)) {
        $dtDept[$rdata['notransaksi']] = $rdata['departemen'];
        $dtNmLowongan[$rdata['notransaksi']] = $rdata['namalowongan'];
        $dtTglPermintaan[$rdata['notransaksi']] = $rdata['tanggal'];
        $dtOrgDbthkn[$rdata['notransaksi']] = $rdata['jumlah_kebutuhan'];
        $dtTransaksi[$rdata['notransaksi']] = $rdata['notransaksi'];
    }
    $sorg = 'select distinct count(email) as jmlhorg,idpermintaan from '.$dbname.".sdm_testcalon a left join \r\n      ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi \r\n      where idpermintaan!='' ".$whr.' group by idpermintaan ';
    $qorg = mysql_query($sorg);
    while ($rorg = mysql_fetch_assoc($qorg)) {
        $jmlhdipngl[$rorg['idpermintaan']] = $rorg['jmlhorg'];
    }
    $sorg = 'select distinct count(email) as jmlhorg,idpermintaan from '.$dbname.".sdm_testcalon a left join \r\n      ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi \r\n      where hasiliview IS NOT NULL ".$whr.' group by idpermintaan ';
    $qorg = mysql_query($sorg);
    while ($rorg = mysql_fetch_assoc($qorg)) {
        $jmlhdiInterview[$rorg['idpermintaan']] = $rorg['jmlhorg'];
    }
    $sorg = 'select distinct count(email) as jmlhorg,idpermintaan from '.$dbname.".sdm_testcalon a left join \r\n      ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi \r\n      where hasilpsy IS NOT NULL ".$whr.' group by idpermintaan ';
    $qorg = mysql_query($sorg);
    while ($rorg = mysql_fetch_assoc($qorg)) {
        $jmlhdiPsikot[$rorg['idpermintaan']] = $rorg['jmlhorg'];
    }
    $sorg = 'select distinct count(email) as jmlhorg,idpermintaan from '.$dbname.".sdm_testcalon a left join \r\n      ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi \r\n      where hasilmedical IS NOT NULL ".$whr.' group by idpermintaan ';
    $qorg = mysql_query($sorg);
    while ($rorg = mysql_fetch_assoc($qorg)) {
        $jmlhdiMedical[$rorg['idpermintaan']] = $rorg['jmlhorg'];
    }
    $sorg = 'select distinct count(email) as jmlhorg,idpermintaan,email,catatanakhir from '.$dbname.".sdm_testcalon a left join \r\n      ".$dbname.".sdm_permintaansdm b on a.idpermintaan=b.notransaksi \r\n      where hasilakhir='Hired' ".$whr.' group by idpermintaan,email ';
    $qorg = mysql_query($sorg);
    while ($rorg = mysql_fetch_assoc($qorg)) {
        $jmlhdiHired[$rorg['idpermintaan']] = $rorg['jmlhorg'];
        $dtEmail[$rorg['idpermintaan']] = $rorg['email'];
        $dtCttn[$rorg['idpermintaan'].$rorg['email']] = $rorg['catatanakhir'];
        $dtTglSPk[$rorg['idpermintaan'].$rorg['email']] = $rorg['tanggalspk'];
    }
    $saks = 'select distinct * from '.$dbname.".setup_remotetimbangan \r\nwhere lokasi='HRDJKRT'";
    $qaks = mysql_query($saks);
    $jaks = mysql_fetch_assoc($qaks);
    $uname2 = $jaks['username'];
    $passwd2 = $jaks['password'];
    $dbserver2 = $jaks['ip'];
    $dbport2 = $jaks['port'];
    $dbdt = $jaks['dbname'];
    $conn2 = mysql_connect($dbserver2, $uname2, $passwd2);
    if (!$conn2) {
        exit('Could not connect: '.mysql_error());
    }

    $sdt = "select a.email,nopermintaan,(year(curdate())-year(tanggallahir)) as umur,tanggallahir,namacalon,jeniskelamin\r\n       from ".$dbdt.".datacalon a left join \r\n       ".$dbdt.".sdm_apply_dt b on a.email=b.email left join \r\n       ".$dbdt.".sdm_lowongan c on b.notransaksi=c.notransaksi\r\n       where nopermintaan!='' and a.status=0";
    $qdt = mysql_query($sdt, $conn2);
    while ($rdt = mysql_fetch_assoc($qdt)) {
        $dtUmur[$rdt['email']] = $rdt['umur'];
        $dtNama[$rdt['email']] = $rdt['namacalon'];
        $dtTglLahir[$rdt['email']] = $rdt['tanggallahir'];
        $dtJnsKelamin[$rdt['email']] = $rdt['jeniskelamin'];
    }
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable>';
    $tab .= '<thead><tr '.$bgclr.'>';
    $tab .= '<td>'.$_SESSION['lang']['departemen'].'</td>';
    $tab .= '<td>'.$_SESSION['lang']['namalowongan'].'</td>';
    $tab .= '<td>'.$_SESSION['lang']['tanggal'].' Permintaan</td>';
    $tab .= '<td>Jumlah Kebutuhan</td>';
    $tab .= '<td>Dipanggil</td>';
    $tab .= '<td>Interview</td>';
    $tab .= '<td>Psikotes</td>';
    $tab .= '<td>Medical</td>';
    $tab .= '<td>Hired</td>';
    $tab .= '<td>Atas Nama</td>';
    $tab .= '<td>Tanggal SPK</td>';
    $tab .= '<td>Keterangan</td>';
    $tab .= '</tr></thead><tbody>';
    foreach ($dtTransaksi as $lstTransaksi) {
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$dtDept[$lstTransaksi].'</td>';
        $tab .= '<td>'.$dtNmLowongan[$lstTransaksi].'</td>';
        $tab .= '<td>'.$dtTglPermintaan[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$dtOrgDbthkn[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$jmlhdipngl[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$jmlhdiInterview[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$jmlhdiPsikot[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$jmlhdiMedical[$lstTransaksi].'</td>';
        $tab .= '<td align=right>'.$jmlhdiHired[$lstTransaksi].'</td>';
        $tab .= '<td>'.$dtNama[$dtEmail[$lstTransaksi]].'</td>';
        $tab .= '<td>'.$dtTglSPk[$lstTransaksi.$dtEmail[$lstTransaksi]].'</td>';
        $tab .= '<td>'.$dtCttn[$lstTransaksi.$dtEmail[$lstTransaksi]].'</td>';
        $tab .= '</tr></tbody>';
    }
    $tab .= '</tbody></table>';
    switch ($_GET['proses']) {
        case 'preview':
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
        global $param;
        global $dtinterviwer;
        $jmlrow = count($dtinterviwer);
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = mysql_query($query, $conn);
        $rOrgData = mysql_fetch_assoc($orgData);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 13;
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
        $this->Cell($width - 100, $height, $rOrgData['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$rOrgData['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($width, $height, strtoupper('Laporan Progress Rekruitment'), '', 0, 'C');
        $this->Ln();
        $dert = $param['periode'].' s.d. '.$param['periodesmp'];
        if ('' == $param['periodesmp']) {
            $dert = $_SESSION['lang']['all'];
        } else {
            if ('' == $param['periode']) {
                $dert = $_SESSION['lang']['all'];
            }
        }

        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' : '.$dert, '', 1, 'C');
        $this->Ln(18);
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 'TRL', 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 'TRL', 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nama'], 'TRL', 0, 'C', 1);
        $this->Cell(9 / 100 * $width, $height, $_SESSION['lang']['tanggallahir'], 'TRL', 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['umur'], 'TRL', 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['jeniskelamin'], 'TRL', 0, 'C', 1);
        $this->Cell(13 / 100 * $width, $height, $_SESSION['lang']['nama'], 'TRL', 0, 'C', 1);
        foreach ($dtinterviwer as $lstInterViewer) {
            ++$ert;
            $det = 0;
            if ($jmlrow == $ert) {
                $det = 1;
            }

            $this->Cell(10 / 100 * $width, $height, $optNmkar[$lstInterViewer], 'TRL', $det, 'C', 1);
        }
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
            $no = 0;
            $pdf->Output();

            break;
        case 'excel':
            $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
            $nop_ = 'progressRekruitment';
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
                    echo "<script language=javascript1.2>\r\n            parent.window.alert('Can't convert to excel format');\r\n            </script>";
                    exit();
                }

                echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls';\r\n            </script>";
                closedir($handle);
            }

            break;
    }
} else {
    exit("error:\n Periode tidak boleh kosong");
}

?>