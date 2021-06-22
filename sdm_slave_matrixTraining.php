<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$matrixid = $_POST['matrixid'];
$method = $_POST['method'];
$karyawanid = $_POST['karyawanid'];
$tanggal1 = $_POST['tanggal1'];
$tanggal2 = $_POST['tanggal2'];
$catatan = $_POST['catatan'];
if ('' == $method) {
    $method = $_GET['method'];
    $karyawanid = $_GET['karyawanid'];
}

$updateby = ''.$_SESSION['standard']['userid'];
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
}
$sJabat = 'select distinct * from '.$dbname.'.datakaryawan where tipekaryawan = 0';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusNama[$rJabat['karyawanid']] = $rJabat['namakaryawan'];
    $kamusJabatan[$rJabat['karyawanid']] = $rJabat['kodejabatan'];
    $kamusLokasi[$rJabat['karyawanid']] = $rJabat['lokasitugas'];
    $kamusDept[$rJabat['karyawanid']] = $rJabat['bagian'];
    $kamusTMK[$rJabat['karyawanid']] = $rJabat['tanggalmasuk'];
}
$sJabat = 'select * from '.$dbname.".sdm_5matriktraining where matrixid = '".$matrixid."'";
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $jabatan = $rJabat['kodejabatan'];
}
$sJabat = 'select * from '.$dbname.'.sdm_matriktraining where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $key = $rJabat['karyawanid'].$rJabat['matrikxid'];
    $udahambiltraining[$key] = '1';
    $catatatan[$key] = $rJabat['catatan'];
}
$sJabat = 'select * from '.$dbname.'.sdm_5matriktraining where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusKategori[$rJabat['matrixid']] = $rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']] = $rJabat['topik'];
}
$sJabat = 'select * from '.$dbname.'.log_5supplier where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusSup[$rJabat['supplierid']] = $rJabat['namasupplier'];
}
switch ($method) {
    case 'centang':
        $str = 'insert into '.$dbname.".sdm_matriktraining (karyawanid,matrikxid,tanggaltraining,sampaitanggal,updateby,catatan)\r\n        values('".$karyawanid."','".$matrixid."','".puter_tanggal($tanggal1)."','".puter_tanggal($tanggal2)."','".$updateby."','".$catatan."')";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'uncentang':
        $str = 'delete from '.$dbname.".sdm_matriktraining\r\n    where karyawanid='".$karyawanid."' and matrikxid='".$matrixid."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'pilihkaryawan':
        $str1 = 'select * from '.$dbname.".datakaryawan where kodejabatan = '".$jabatan."' and tipekaryawan = 0 and (tanggalkeluar is NULL or tanggalkeluar is null or tanggalkeluar > '".$_SESSION['org']['period']['start']."') order by namakaryawan";
        $res1 = mysql_query($str1);
        echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['remark']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody id=container>";
        $no = 0;
        while ($bar1 = mysql_fetch_object($res1)) {
            $key = $bar1->karyawanid.$matrixid;
            if ('1' == $udahambiltraining[$key]) {
                $ceket = ' checked';
            } else {
                $ceket = '';
            }

            ++$no;
            echo "<tr class=rowcontent>\r\n        <td>".$bar1->namakaryawan."</td>\r\n        <td><input type=text class=myinputtext id=remark".$bar1->karyawanid." onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30 value='".$catatatan[$key]."'></td>\r\n        <td align=center>\r\n            <input type=checkbox name=cekbok value=cekbok id=cek".$bar1->karyawanid." onchange=klikbok('".$bar1->karyawanid."') ".$ceket.">\r\n        </td>\r\n    </tr>";
        }
        echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";

        break;
    case 'rilotlis':
        $str1 = 'select * from '.$dbname.'.sdm_matriktraining where 1 order by karyawanid';
        $res1 = mysql_query($str1);
        echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td>".$_SESSION['lang']['departemen']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
        $no = 0;
        while ($bar1 = mysql_fetch_object($res1)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td>".$kamusNama[$bar1->karyawanid]."</td>\r\n        <td>".$kamusLokasi[$bar1->karyawanid]."</td>\r\n        <td>".$kamusDept[$bar1->karyawanid]."</td>\r\n        <td>".$kamusJabat[$kamusJabatan[$bar1->karyawanid]]."</td>\r\n        <td align=center>\r\n            <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixTraining.php','".$bar1->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>\r\n        </td>\r\n    </tr>";
        }
        echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";

        break;
    case 'pdf':

class PDF extends FPDF
{
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(185, 6, 'DAFTAR TRAINING', 0, 1, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        $str = 'select * from '.$dbname.".datakaryawan \r\n        where karyawanid = '".$karyawanid."'\r\n        ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $pdf->Cell(50, 6, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
            $pdf->Cell(100, 6, ': '.$bar->namakaryawan, 0, 1, 'L');
            $pdf->Cell(50, 6, $_SESSION['lang']['jabatan'], 0, 0, 'L');
            $pdf->Cell(100, 6, ': '.$kamusJabat[$bar->kodejabatan], 0, 1, 'L');
            $pdf->Cell(50, 6, $_SESSION['lang']['lokasitugas'], 0, 0, 'L');
            $pdf->Cell(100, 6, ': '.$bar->lokasitugas, 0, 1, 'L');
            $pdf->Cell(50, 6, $_SESSION['lang']['tmk'], 0, 0, 'L');
            $pdf->Cell(100, 6, ': '.puter_tanggal($bar->tanggalmasuk), 0, 1, 'L');
            $pdf->Ln();
            $jabatanku = $bar->kodejabatan;
        }
        $pdf->Ln();
        $pdf->Cell(185, 6, 'Training yang sudah diikuti:', 0, 1, 'L');
        $pdf->Cell(40, 6, $_SESSION['lang']['kategori'], 1, 0, 'C');
        $pdf->Cell(40, 6, $_SESSION['lang']['topik'], 1, 0, 'C');
        $pdf->Cell(30, 6, $_SESSION['lang']['tanggalmulai'], 1, 0, 'C');
        $pdf->Cell(30, 6, $_SESSION['lang']['tanggalsampai'], 1, 0, 'C');
        $pdf->Cell(50, 6, $_SESSION['lang']['catatan'], 1, 0, 'C');
        $pdf->Ln();
        $str = 'select * from '.$dbname.".sdm_matriktraining\r\n        where karyawanid = '".$karyawanid."'\r\n        ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $pdf->Cell(40, 6, $kamusKategori[$bar->matrikxid], 0, 0, 'L');
            $pdf->Cell(40, 6, $kamusTopik[$bar->matrikxid], 0, 0, 'L');
            $pdf->Cell(30, 6, puter_tanggal($bar->tanggaltraining), 0, 0, 'L');
            $pdf->Cell(30, 6, puter_tanggal($bar->sampaitanggal), 0, 0, 'L');
            $pdf->MultiCell(50, 6, $bar->catatan, 0, 'L', false);
            $pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Cell(185, 6, 'Training yang harusnya diikuti:', 0, 1, 'L');
        $pdf->Cell(40, 6, $_SESSION['lang']['jabatan'], 1, 0, 'C');
        $pdf->Cell(40, 6, $_SESSION['lang']['kategori'], 1, 0, 'C');
        $pdf->Cell(40, 6, $_SESSION['lang']['topik'], 1, 0, 'C');
        $pdf->Ln();
        $str = 'select * from '.$dbname.".sdm_5matriktraining\r\n        where kodejabatan = '".$jabatanku."'\r\n        ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $pdf->Cell(40, 6, $kamusJabat[$bar->kodejabatan], 0, 0, 'L');
            $pdf->Cell(40, 6, $bar->kategori, 0, 0, 'L');
            $pdf->Cell(40, 6, $bar->topik, 0, 0, 'L');
            $pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Cell(185, 6, 'Additional Training yang sudah diikuti:', 0, 1, 'L');
        $pdf->Cell(70, 6, $_SESSION['lang']['namatraining'], 1, 0, 'C');
        $pdf->Cell(60, 6, $_SESSION['lang']['penyelenggara'], 1, 0, 'C');
        $pdf->Cell(30, 6, $_SESSION['lang']['tanggalmulai'], 1, 0, 'C');
        $pdf->Cell(30, 6, $_SESSION['lang']['tanggalsampai'], 1, 0, 'C');
        $pdf->Ln();
        $str = 'select * from '.$dbname.".sdm_5training\r\n        where karyawanid = '".$karyawanid."' and sthrd = '1'\r\n        ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $pdf->Cell(70, 6, $bar->namatraining, 0, 0, 'L');
            $pdf->Cell(60, 6, $kamusSup[$bar->penyelenggara], 0, 0, 'L');
            $pdf->Cell(30, 6, puter_tanggal($bar->tglmulai), 0, 0, 'L');
            $pdf->Cell(30, 6, puter_tanggal($bar->tglselesai), 0, 0, 'L');
            $pdf->Ln();
        }
        $pdf->Output();

        break;
    default:
        break;
}
function puter_tanggal($tanggal)
{
    $tgl = explode('-', $tanggal);

    return $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
}

?>