<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$per = $_POST['per'];
$tipe = $_POST['tipe'];
if ('excel' == $proses || 'pdf' == $proses) {
    $kdorg = $_GET['kdorg'];
    $per = $_GET['per'];
    $tipe = $_GET['tipe'];
}

if ('excel' == $proses) {
    $bgcolor = 'bgcolor=#CCCCCC';
    $border = "border='1'";
}

$tglOption = $per.'-01';
$nmAng = makeOption($dbname, 'sdm_ho_component', 'id,name');
$stream = "<table cellspacing='1'  class='sortable' ".$bgcolor.' '.$border.'><thead>';
$stream .= "\r\n                <tr class=rowheader>\r\n                            <td align=center>No.</td>\r\n                                <td align=center>".$_SESSION['lang']['karyawanid']."</td>\r\n                            \t<td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>\r\n                                <td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>\r\n                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>\r\n                                <td align=center>".$_SESSION['lang']['sampai']."</td>\r\n                                <td align=center>".$_SESSION['lang']['jumlah'].'<br>('.$_SESSION['lang']['bulan'].")</td>\r\n                                <td align=center>".$_SESSION['lang']['potongan'].'/'.$_SESSION['lang']['bulan'].".<br>(Rp.)</td>\t\t\r\n\t\t\t\t\t\t\t\t<td align=center>Terbayar<br>(Rp.)</td>\t\t\r\n\t\t\t\t\t\t\t\t<td align=center>SisaTerbayar<br>(Rp.)</td>\t\t\r\n                                <td align=center>".$_SESSION['lang']['status']."</td>\r\n                          </tr> \r\n                          </thead>";
if ('lunas' == $tipe) {
    $str = "\tselect a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\twhere  u.lokasitugas='".$kdorg."'  and a.end< '".$per."' \r\n\t\t\torder by namakaryawan";
} else {
    if ('blmlunas' == $tipe) {
        $str = "\tselect a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\twhere  u.lokasitugas='".$kdorg."'    and a.end > '".$per."'\r\n\t\t\torder by namakaryawan";
    } else {
        if ('active' == $tipe) {
            $str = "\tselect a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\twhere  u.lokasitugas='".$kdorg."'    and a.active=1\r\n\t\t\torder by namakaryawan";
        } else {
            if ('notactive' == $tipe) {
                $str = "\tselect a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.'.sdm_angsuran a left join '.$dbname.".datakaryawan u on a.karyawanid=u.karyawanid\r\n\t\t\twhere  u.lokasitugas='".$kdorg."'   and a.active=0\r\n\t\t\torder by namakaryawan";
            }
        }
    }
}

$qry = mysql_query($str) || exit('SQL ERR : '.mysql_error($conn));
while ($bar = mysql_fetch_assoc($qry)) {
    $a = 'select sum(jumlah) as jumlah from '.$dbname.".sdm_gaji where karyawanid='".$bar['karyawanid']."' and idkomponen='".$bar['jenis']."' \r\n\t\tand periodegaji between '".$bar['start']."' and '".$per."' group by karyawanid";
    $b = mysql_query($a);
    $c = mysql_fetch_assoc($b);
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t<td>".$bar['nik']."</td>\r\n\t\t\t\t<td>".$bar['namakaryawan']."</td>\r\n\t\t\t\t<td>".$nmAng[$bar['jenis']]."</td>\r\n\t\t\t\t<td align=right>".number_format($bar['total'], 2, '.', ',')."</td>\r\n\t\t\t\t<td align=center>".$bar['start']."</td>\r\n\t\t\t\t<td align=center>".$bar['end']."</td>\r\n\t\t\t\t<td align=right>".$bar['jlhbln']."</td>\r\n\t\t\t\t<td align=right>".number_format($bar['bulanan'], 2, '.', ',')."</td>\t\r\n\t\t\t\t<td align=right>".$c['jumlah']."</td>\t\t\r\n\t\t\t\t<td align=right>".number_format($bar['total'] - $c['jumlah'], 2, '.', ',')."</td>\t\t\t\t\t\r\n\t\t\t\t<td align=center>".((1 == $bar['active'] ? 'Active' : 'Not Active'))."</td>\r\n\t\t\t\t\t\t  </tr>";
    $ttl += $bar['bulanan'];
}
$stream .= '<tbody></table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_Riwayat_Potongan_Angsuran_Karyawan'.$tglSkrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t</script>";
            closedir($handle);
        }

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
        global $kdorg;
        global $kdAfd;
        global $tgl1;
        global $tgl2;
        global $where;
        global $nmOrg;
        global $lok;
        global $notrans;
        global $bulan;
        global $ang;
        global $kar;
        global $namaang;
        global $namakar;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
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

        $this->Image($path, 30, 15, 55);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(90);
        $this->Cell($width - 80, 12, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(90);
        $this->SetFont('Arial', '', 9);
        $height = 15;
        $this->Cell($width - 80, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(90);
        $this->Cell($width - 80, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Ln();
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        $height = 15;
        $this->Cell($width, $height, 'Laporan Stock Opname', '', 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, 15, substr($_SESSION['lang']['nomor'], 0, 2), 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, 15, 'Kode Barang', 1, 0, 'C', 1);
        $this->Cell(35 / 100 * $width, 15, 'Nama Barang', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, 15, 'Saldo Fisik e-Agro', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, 15, 'Saldo Fisik Gudang', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, 15, 'Selisih', 1, 1, 'C', 1);
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
        $qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
        $no = 0;
        while ($bar = mysql_fetch_assoc($qry)) {
            ++$no;
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $bar['kodebarang'], 1, 0, 'R', 1);
            $pdf->Cell(35 / 100 * $width, $height, $nmbarang[$bar['kodebarang']], 1, 0, 'L', 1);
            $pdf->Cell(15 / 100 * $width, $height, number_format($bar['saldoqty']), 1, 0, 'R', 1);
            $pdf->Cell(15 / 100 * $width, $height, '', 1, 0, 'R', 1);
            $pdf->Cell(15 / 100 * $width, $height, '', 1, 1, 'R', 1);
        }
        $pdf->Output();

        break;
}
echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";

?>