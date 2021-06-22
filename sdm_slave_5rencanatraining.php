<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$kamar = $_POST['kamar'];
if ('' == $kamar) {
    $kamar = $_GET['kamar'];
    $karyawanid = $_GET['karyawanid'];
    $kodetraining = $_GET['kodetraining'];
} else {
    $karyawanid = $_POST['karyawanid'];
    $tahunbudget = $_POST['tahunbudget'];
    $listtahun = $_POST['listtahun'];
    $kodetraining = $_POST['kodetraining'];
    $namatraining = $_POST['namatraining'];
    $levelpeserta = $_POST['levelpeserta'];
    $levelpeserta = $_POST['levelpeserta'];
    $penyelenggara = $_POST['penyelenggara'];
    $hargaperpeserta = $_POST['hargaperpeserta'];
    $tanggal1 = $_POST['tanggal1'];
    $tanggal2 = $_POST['tanggal2'];
    $persetujuan = $_POST['persetujuan'];
    $hrd = $_POST['hrd'];
    $deskripsitraining = $_POST['deskripsitraining'];
    $hasildiharapkan = $_POST['hasildiharapkan'];
}

if ('' != $tanggal1) {
    $tanggal1 = putertanggal($tanggal1);
}

if ('' != $tanggal2) {
    $tanggal2 = putertanggal($tanggal2);
}

$str = 'select * from '.$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $host[$bar->supplierid] = $bar->namasupplier;
}
$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jab[$bar->kodejabatan] = $bar->namajabatan;
}
$str = 'select namakaryawan,karyawanid,kodejabatan,bagian from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 order by namakaryawan";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $nam[$bar->karyawanid] = $bar->namakaryawan;
    $jabjab[$bar->karyawanid] = $bar->kodejabatan;
    $depdep[$bar->karyawanid] = $bar->bagian;
}
$stat[0] = '';
$stat[1] = $_SESSION['lang']['disetujui'];
$stat[2] = $_SESSION['lang']['ditolak'];
if ('list' == $kamar) {
    $str = 'select * from '.$dbname.".sdm_5training where karyawanid = '".$karyawanid."'\r\n      ";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        echo "<tr class=rowcontent>\r\n    <td>".$no."</td>\r\n    <td>".$bar->tahunbudget."</td>\r\n    <td>".$bar->kode."</td>\r\n    <td>".$bar->namatraining."</td>\r\n    <td>".$jab[$bar->kodejabatan]."</td>\r\n    <td>".$host[$bar->penyelenggara]."</td>\r\n\r\n    <td align=right>".number_format($bar->hargasatuan, 0, '.', ',')."</td>\r\n    <td align=center>".tanggalnormal($bar->tglmulai)."</td>\r\n    <td align=center>".tanggalnormal($bar->tglselesai)."</td>\r\n    <td>".$nam[$bar->persetujuan1]."</td>\r\n    <td>".$stat[$bar->stpersetujuan1]."</td>\r\n    <td>".$nam[$bar->persetujuanhrd]."</td>\r\n    <td>".$stat[$bar->sthrd]."</td>\r\n    <td>";
        if (0 == $bar->stpersetujuan1 && 0 == $bar->sthrd) {
            echo "<img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->kodejabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".tanggalnormal($bar->tglmulai)."','".tanggalnormal($bar->tglselesai)."','".$bar->persetujuan1."','".$bar->persetujuanhrd."','".str_replace("\n", '\\n', $bar->desctraining)."','".str_replace("\n", '\\n', $bar->output)."');\">";
        }

        echo "</td>\r\n    <td>";
        if (0 == $bar->stpersetujuan1 && 0 == $bar->sthrd) {
            echo "<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">";
        }

        echo "</td>\r\n    <td>";
        echo "<img class=resicon src=images/pdf.jpg title='PDF' onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."')\">";
        echo "</td>\r\n    </tr>";
        ++$no;
    }
}

if ('save' == $kamar) {
    $strx = 'insert into '.$dbname.".sdm_5training\r\n        (kode,namatraining,penyelenggara,\r\n        hargasatuan,desctraining,output,\r\n        tahunbudget,tglmulai,tglselesai,karyawanid,\r\n        persetujuan1,persetujuanhrd,kodejabatan)\r\n\tvalues('".$kodetraining."','".$namatraining."','".$penyelenggara."',\r\n\t'".$hargaperpeserta."','".$deskripsitraining."','".$hasildiharapkan."',\r\n\t'".$tahunbudget."','".$tanggal1."','".$tanggal2."','".$karyawanid."',\r\n\t'".$persetujuan."','".$hrd."','".$levelpeserta."')";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('delete' == $kamar) {
    $strx = 'delete from '.$dbname.".sdm_5training \r\n    where kode='".$kodetraining."' and karyawanid = '".$karyawanid."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('edit' == $kamar) {
    $strx = 'update '.$dbname.".sdm_5training set\r\n        namatraining = '".$namatraining."',\r\n        penyelenggara = '".$penyelenggara."',\r\n        hargasatuan = '".$hargaperpeserta."',\r\n        desctraining = '".$deskripsitraining."',\r\n        output = '".$hasildiharapkan."',\r\n        tahunbudget = '".$tahunbudget."',   \r\n        tglmulai = '".$tanggal1."',\r\n        tglselesai = '".$tanggal2."',\r\n        persetujuan1 = '".$persetujuan."',\r\n        persetujuanhrd = '".$hrd."',            \r\n        kodejabatan = '".$levelpeserta."'\r\n        where kode = '".$kodetraining."' and karyawanid = '".$karyawanid."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('pdf' == $kamar) {
    class PDF extends FPDF
    {
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $str = 'select * from '.$dbname.".sdm_5training where karyawanid = '".$karyawanid."' and kode = '".$kodetraining."'\r\n      ";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $namatraining = $bar->namatraining;
        $penyelenggara = $host[$bar->penyelenggara];
        $tanggalmulai = $bar->tglmulai;
        $tanggalselesai = $bar->tglselesai;
        $hargaperpeserta = $bar->hargasatuan;
        $deskripsi = $bar->desctraining;
        $hasil = $bar->output;
        $atasan = $bar->persetujuan1;
        $atasanstatus = $bar->stpersetujuan1;
        $atasancatatan = $bar->catatan1;
        $hrd = $bar->persetujuanhrd;
        $hrdstatus = $bar->sthrd;
        $hrdcatatan = $bar->catatanhrd;
    }
    $pdf->Cell(185, 6, 'FORM PENGAJUAN TRAINING', 0, 1, 'C');
    $pdf->Ln();
    $pdf->Cell(50, 6, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.$nam[$karyawanid], 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['jabatan'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.$jab[$jabjab[$karyawanid]], 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['departemen'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.$depdep[$karyawanid], 0, 1, 'L');
    $pdf->Ln();
    $pdf->Cell(50, 6, $_SESSION['lang']['namatraining'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.$namatraining, 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['penyelenggara'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.$penyelenggara, 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['tanggalmulai'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.tanggalnormal($tanggalmulai), 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['tanggalsampai'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.tanggalnormal($tanggalselesai), 0, 1, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['hargaperpeserta'], 0, 0, 'L');
    $pdf->Cell(100, 6, ': '.number_format($hargaperpeserta), 0, 1, 'L');
    $pdf->Ln();
    $pdf->Cell(50, 6, $_SESSION['lang']['deskripsitraining'], 0, 0, 'L');
    $pdf->MultiCell(100, 6, ': '.$deskripsi, 0, 'L', false);
    $pdf->Ln();
    $pdf->Cell(50, 6, $_SESSION['lang']['hasildiharapkan'], 0, 0, 'L');
    $pdf->MultiCell(100, 6, ': '.$hasil, 0, 'L', false);
    $pdf->Ln();
    $pdf->Cell(40, 6, $_SESSION['lang']['persetujuan'], 0, 1, 'L');
    $pdf->Cell(40, 6, $_SESSION['lang']['namakaryawan'], 1, 0, 'L');
    $pdf->Cell(50, 6, $_SESSION['lang']['jabatan'], 1, 0, 'L');
    $pdf->Cell(20, 6, $_SESSION['lang']['status'], 1, 0, 'L');
    $pdf->Cell(80, 6, $_SESSION['lang']['catatan'], 1, 1, 'L');
    $pdf->Cell(40, 6, substr($nam[$atasan], 0, 30), 0, 0, 'L');
    $pdf->Cell(50, 6, substr($jab[$jabjab[$atasan]], 0, 30), 0, 0, 'L');
    $pdf->Cell(20, 6, $stat[$atasanstatus], 0, 0, 'L');
    $pdf->MultiCell(80, 6, $atasancatatan, 0, 'L', false);
    $pdf->Ln();
    $pdf->Cell(40, 6, $nam[$hrd], 0, 0, 'L');
    $pdf->Cell(50, 6, $jab[$jabjab[$hrd]], 0, 0, 'L');
    $pdf->Cell(20, 6, $stat[$hrdstatus], 0, 0, 'L');
    $pdf->MultiCell(80, 6, $hrdcatatan, 0, 'L', false);
    $pdf->Ln();
    $pdf->Output();
}

function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

?>