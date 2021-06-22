<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$kodeorg = (isset($_POST['kodeorg']) ? $_POST['kodeorg'] : '');
if (isset($_POST['kodekegiatan'])) {
    $kodekegiatan = $_POST['kodekegiatan'];
} else {
    $kodekegiatan = (isset($_POST['kegiatan']) ? $_POST['kegiatan'] : '');
}

$proses = (isset($_POST['proses']) ? $_POST['proses'] : '');
$notransaksi = (isset($_POST['notransaksi']) ? $_POST['notransaksi'] : '');
$tanggal = (isset($_POST['tanggal']) ? tanggalsystem($_POST['tanggal']) : '');
$jjg = (isset($_POST['jjg']) ? $_POST['jjg'] : '');
$jjgDis = (isset($_POST['jjgDisable']) ? $_POST['jjgDisable'] : '');
$hasilkerja = (isset($_POST['hasilkerja']) ? $_POST['hasilkerja'] : '');
$nik = (isset($_POST['nik']) ? $_POST['nik'] : '');
$jhk = (isset($_POST['jhk']) ? $_POST['jhk'] : '');
$umr = (isset($_POST['umr']) ? $_POST['umr'] : '');
$insentif = (isset($_POST['insentif']) ? $_POST['insentif'] : '');
$tahun = substr($tanggal, 0, 4);
switch ($proses) {
    case 'cekAll':
        if ('false' === $jjgDis) {
            $a = 'select kodeorg from '.$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
            $b = mysql_query($a);
            $c = mysql_fetch_assoc($b);
            $kdAfd = substr($c['kodeorg'], 0, 6);
            $x = 'select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM '.$dbname.'.kebun_spbdt a left join '.$dbname.".kebun_spbht b on a.nospb=b.nospb \r\n\t\t\t\twhere blok like '%".$kdAfd."%' and tanggal<='".$tanggal."' and tanggal='".$tanggal."'";
            $y = mysql_query($x) ;
            $z = mysql_fetch_assoc($y);
            $bjr = $z['bjr'];
            $hasilkerja = $bjr * $jjg;
        }

        $i = 'select rupiah,insentif from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $rupiah = $d['rupiah'];
        $insentif = $d['insentif'];
        $umr = $rupiah * $hasilkerja;
        $tahun = substr($tanggal, 0, 4);
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', "karyawanid='".$nik."' and tahun='".$tahun."' and idkomponen in (1,31)");
        $Umr = fetchData($qUMR);
        $zUmr = $Umr[0]['nilai'] / 25;
        if (empty($rupiah)) {
            $umr = $zUmr;
        }

        if ($zUmr <= $umr) {
            $jhk = 1;
        } else {
            $jhk = $umr / $zUmr;
        }

        $res = ['hasilkerja' => number_format($hasilkerja, 2), 'jhk' => number_format($jhk, 2), 'umr' => number_format($umr, 2), 'insentif' => number_format($insentif, 2)];
        echo json_encode($res);

        break;
    case 'cekKonversi':
        $i = 'select konversi from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $konversi = $d['konversi'];
        echo $konversi;

        break;
    case 'cekBJR':
        $w = 'select konversi from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $i = mysql_query($w) ;
        $b = mysql_fetch_assoc($i);
        $konversi = $b['konversi'];

        break;
    case 'getHasilKerja':
        $kdAfd = substr($kodeorg, 0, 6);
        $x = 'select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM '.$dbname.'.kebun_spbdt a left join '.$dbname.".kebun_spbht b on a.nospb=b.nospb \r\n\t\t\t\twhere blok like '%".$kdAfd."%' and tanggal<='".$tanggal."' and tanggal='".$tanggal."'";
        $y = mysql_query($x) ;
        $z = mysql_fetch_assoc($y);
        $bjr = $z['bjr'];
        $hasilKerja = number_format($bjr * $jjg, 2);
        echo $hasilKerja;

        break;
    case 'getUMR':
        $a = 'select kodekegiatan from '.$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
        $b = mysql_query($a);
        $c = mysql_fetch_assoc($b);
        $kodekegiatan = $c['kodekegiatan'];
        $i = 'select rupiah,insentif from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $rupiah = $d['rupiah'];
        $insentif = $d['insentif'];
        $umr = $rupiah * $hasilkerja;
        $tahun = substr($tanggal, 0, 4);
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', "karyawanid='".$nik."' and tahun='".$tahun."' and idkomponen in (1,31)");
        $Umr = fetchData($qUMR);
        $zUmr = $Umr[0]['nilai'] / 25;
        if ($zUmr < $umr) {
            $jhk = 1;
        } else {
            $jhk = $umr / $zUmr;
        }

        echo number_format($umr, 2).'###'.number_format($jhk, 2).'###'.number_format($insentif, 2);

        break;
    case 'getPremi':
        $a = 'select kodekegiatan from '.$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
        $b = mysql_query($a);
        $c = mysql_fetch_assoc($b);
        $kodekegiatan = $c['kodekegiatan'];
        $i = 'select insentif from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $rupiah = $d['rupiah'];
        $insentif = $d['insentif'];
        echo $insentif;

        break;
    case 'getAbsen':
        $a = 'select tanggal from '.$dbname.".sdm_5harilibur where tanggal='".$tanggal."'";
        $b = mysql_query($a);
        $c = mysql_fetch_assoc($b);
        $tanggal = $c['tanggal'];
        if ('' !== $tanggal) {
            $cekMG = date('D', strtotime($tanggal));
            if ('Sun' === $cekMG) {
                $optAbs .= "<option value='MG'>Hari Minggu</option>";
            } else {
                $optAbs .= "<option value='L'>Hari libur(diluar hari minggu)</option>";
            }
        }

        echo $optAbs;
}

?>