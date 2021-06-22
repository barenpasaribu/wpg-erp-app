<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$noakun = $_POST['noakun'];
$dibuat = $_POST['dibuat'];
$diperiksa = $_POST['diperiksa'];
$tgl = tanggalsystem($_POST['tgl']);
if ('excel' === $proses) {
    $kdorg = $_GET['kdorg'];
    $noakun = $_GET['noakun'];
    $tgl = tanggalsystem($_GET['tgl']);
    $dibuat = $_GET['dibuat'];
    $diperiksa = $_GET['diperiksa'];
}

$arr = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$nmKar = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$pt = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$nmPt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if ('excel' === $proses) {
    $border = 'border=1';
    $bgcol = 'bgcolor=#CCCCCC ';
} else {
    $border = 'border=0';
}

if (('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) && '' === $kdorg) {
    echo 'Error: Organisasi tidak boleh kosong';
    exit();
}

$thn = substr($tgl, 0, 4);
$thnKm = $thn - 1;
$tglAkhir = ''.$thnKm.'1231';
$stream = "<table>\r\n                        <tr>\r\n                                <td colspan=5><b>".$nmPt[$pt[$kdorg]]."</b></td>\r\n                        </tr>\r\n                        <tr>\r\n                                <td></td>\r\n                        </tr>\r\n                        <tr>\r\n                                <td><b>AGING SCHEDULE</b></td>\r\n                        </tr>\r\n                        <tr>\r\n                                <td><b>UANG MUKA ".strtoupper($arr[$noakun])." </b></td>\r\n                        </tr>\r\n                        <tr>\r\n                                <td><b>PER ".tanggalnormal($tgl)." </b></td>\r\n                        </tr>\r\n                        <tr>\r\n                                <td></td>\r\n                        </tr>\r\n</table>";
$stream .= "<table cellspacing='1' ".$border."  class='sortable'>\r\n<thead>\r\n          <tr class=rowheader>\r\n               <td rowspan=2 ".$bgcol." align=center>Tanggal</td>\r\n                <td rowspan=2 ".$bgcol." align=center><b>ID Supplier</b></td>\r\n                <td rowspan=2 ".$bgcol." align=center><b>Nama</b></td>\r\n                <td rowspan=2 ".$bgcol.' align=center><b>'.$_SESSION['lang']['kodebarang']."</b></td>\r\n                                        <td rowspan=2 ".$bgcol.' align=center><b>'.$_SESSION['lang']['keterangan']."</b></td>\r\n                <td rowspan=2 ".$bgcol.' align=center><b>Saldo 31/12/'.$thnKm."</b></td>\r\n                <td colspan=5 ".$bgcol." align=center><b>Umur Piutang Thn Berjalan</b></td>\r\n\r\n          </tr>\r\n          <tr>\r\n                <td align=center ".$bgcol."><b>1-30 Hari</b></td>\r\n                <td align=center ".$bgcol."><b>31-60 Hari</b></td>\r\n                <td align=center ".$bgcol."><b>61-90 Hari</b></td>\r\n                <td align=center ".$bgcol."><b>90-120 Hari</b></td>\r\n                <td align=center ".$bgcol."><b>120+ Hari</b></td>\r\n          </tr>\r\n</thead>\r\n<tbody>";

$data = [];
$i = 'select sum(jumlah) as jumlah,tanggal,kodesupplier,kodebarang,keterangan FROM '.$dbname.".keu_jurnaldt_vw\r\n where noakun='".$noakun."' and tanggal<='".$tgl."' and kodeorg like'".$kdorg."%' group by kodesupplier,tanggal";
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {

    $diff = strtotime($tgl) - strtotime($d['tanggal']);
    $outstd = floor($diff / (60 * 60 * 24));
    $kel = 0;
    if (1 <= $outstd && $outstd <= 30) {
        $kel = 1;
    }

    if (31 <= $outstd && $outstd <= 60) {
        $kel = 2;
    }

    if (61 <= $outstd && $outstd <= 90) {
        $kel = 3;
    }

    if (91 <= $outstd && $outstd <= 120) {
        $kel = 4;
    }

    if (120 < $outstd) {
        $kel = 5;
    }

    $data['jumlah'][][$kel] = $d['jumlah'];
    $data['kodebarang'][] = $d['kodebarang'];
    $data['keterangan'][] = $d['keterangan'];
    $data['tanggal'][] = $d['tanggal'];
    $data['kodesupplier'][] = $d['kodesupplier'];
}

$a = 'select sum(jumlah) as jumlah,kodesupplier,kodebarang,tanggal,keterangan FROM '.$dbname.".keu_jurnaldt_vw where tanggal<='".$tglAkhir."' "."and noakun='".$noakun."' and kodeorg='".$kdorg."' group by kodesupplier,tanggal";
$b = mysql_query($a);
while ($c = mysql_fetch_object($b)) {
    $dataJur['jumlah'][$c->kodesupplier][$c->tanggal][$c->kodebarang][$c->keterangan] = $c->jumlah;
}

$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

if (0 < count($data['jumlah'])) {
    foreach ($data['jumlah'] as $idx => $row) {

        $stream .= "<tr class=rowcontent>\r\n                        <td align=left>".tanggalnormal($data['tanggal'][$idx])."</td>\r\n                        <td align=left>".$data['kodesupplier'][$idx]."</td>\r\n                        <td align=left>".$nmKar[$data['kodesupplier'][$idx]]."</td>\r\n                        <td align=left>".$nmBrg[$data['kodebarang'][$idx]]."</td>\r\n                        <td align=left>".$data['keterangan'][$idx]."</td>\r\n                        <td align=right>".number_format($dataJur['jumlah'][$data['kodesupplier'][$idx]][$data['tanggal'][$idx]][$data['kodebarang'][$idx]][$data['keterangan'][$idx]])."</td>\r\n                        <td align=right>".number_format($row[1])."</td>\r\n                        <td align=right>".number_format($row[2])."</td>\r\n                        <td align=right>".number_format($row[3])."</td>\r\n                        <td align=right>".number_format($row[4])."</td>\r\n                        <td align=right>".number_format($row[5])."</td>\r\n                </tr>";
        $totalSaldo += $dataJur['jumlah'][$data['kodesupplier'][$idx]][$data['tanggal'][$idx]][$data['kodebarang'][$idx]][$data['keterangan'][$idx]];
        $total[1] += $row[1];
        $total[2] += $row[2];
        $total[3] += $row[3];
        $total[4] += $row[4];
        $total[5] += $row[5];
    }
    $stream .= "\r\n                        <tr class=rowcontent>\r\n                                <td align=right colspan=2></td>\r\n                                <td align=left>Jumlah</td>\r\n                                <td align=left colspan=2></td>\r\n                                <td align=right><B>".number_format($totalSaldo)."</b></td>\r\n                                <td align=right><B>".number_format($total[1])."</b></td>\r\n                                <td align=right><B>".number_format($total[2])."</b></td>\r\n                                <td align=right><B>".number_format($total[3])."</b></td>\r\n                                <td align=right><B>".number_format($total[4])."</b></td>\r\n                                <td align=right><B>".number_format($total[5])."</b></td>\r\n                        </tr>\r\n                        <tr class=rowcontent>\r\n                                <td align=right colspan=2></td>\r\n                                <td align=left>Jumlah per Neraca Percobaan</td>\r\n                                <td align=left colspan=2></td>\r\n                                <td align=right><B>".number_format($totalSaldo)."</b></td>\r\n                                <td align=right rowspan=3></td>\r\n                                <td align=right rowspan=3></td>\r\n                                <td align=right rowspan=3></td>\r\n                                <td align=right rowspan=3></td>\r\n                                <td align=right rowspan=3></td>\r\n                        </tr>\r\n                        <tr class=rowcontent>\r\n                                <td align=right rowspan=2 colspan=2></td>\r\n                                <td rowspan=2 align=left valign=top>Selisih</td>\r\n                                <td align=left colspan=2></td>\r\n                                <td align=right><font color=red><B>".number_format($totalSaldo - $totalSaldo)."</b></font></td>\r\n                        </tr></table>\r\n                        ";
    $stream .= "<table>\r\n                        <tr>\r\n                                <td>Dibuat Oleh :</td>\r\n                                <td></td>\r\n                                <td>Diperiksa Oleh :</td>\r\n                                <td colspan=3></td>\r\n                                <td colspan=2>Diketahui Oleh :</td>\r\n                        </tr>";
    for ($i = 1; $i <= 5; ++$i) {
    }
    $stream .= "<tr>\r\n                                        <td></td>\r\n                                        <td></td>\r\n                                        <td></td>\r\n                                        <td colspan=3></td>\r\n                                        <td colspan=2></td>\r\n                                        </tr>";
    $stream .= "<tr>\r\n                                <td>".$nmKar[$dibuat]."</td>\r\n                                <td></td>\r\n                                <td>".$nmKar[$diperiksa]."</td>\r\n                                <td colspan=3></td>\r\n                                <td colspan=2>Accounting Manager</td>\r\n                        </tr>\r\n\r\n</table>";
} else {
    $stream = 'No Data Found';
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_Aging_Pinjaman'.$tglSkrg;
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
                echo "<script language=javascript1.2>\r\n                                parent.window.alert('Can't convert to excel format');\r\n                                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                                window.location='tempExcel/".$nop_.".xls';\r\n                                </script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>