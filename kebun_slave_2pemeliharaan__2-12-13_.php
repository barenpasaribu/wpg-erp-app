<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdOrg'];
$kdAfd = $_POST['kdAfd'];
$tgl1_ = $_POST['tgl1'];
$tgl2_ = $_POST['tgl2'];
$kegiatan = $_POST['kegiatan'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kdOrg = $_GET['kdOrg'];
    $kdAfd = $_GET['kdAfd'];
    $tgl1_ = $_GET['tgl1'];
    $tgl2_ = $_GET['tgl2'];
    $kegiatan = $_GET['kegiatan'];
}

if ('' === $kdAfd) {
    $kdAfd = $kdOrg;
}

$tgl1_ = tanggalsystem($tgl1_);
$tgl1 = substr($tgl1_, 0, 4).'-'.substr($tgl1_, 4, 2).'-'.substr($tgl1_, 6, 2);
$tgl2_ = tanggalsystem($tgl2_);
$tgl2 = substr($tgl2_, 0, 4).'-'.substr($tgl2_, 4, 2).'-'.substr($tgl2_, 6, 2);
if ('EN' === $_SESSION['language']) {
    $zz = 'namakegiatan1 as namakegiatan';
} else {
    $zz = 'namakegiatan';
}

$str = 'select kodekegiatan, '.$zz.", satuan\r\n        from ".$dbname.".setup_kegiatan\r\n        ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kamusKeg[$bar->kodekegiatan] = $bar->namakegiatan;
}
if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $kdOrg) {
        echo 'Error: Estate code and afdeling code required.';
        exit();
    }

    if ('' === $tgl1_ || '' === $tgl2_) {
        echo 'Error: Date required.';
        exit();
    }

    if ($tgl2 < $tgl1) {
        echo 'Error: First date must lower than the second.';
        exit();
    }
}

if ('excel' === $proses || 'preview' === $proses) {
    $str = "select a.notransaksi,a.kwantitas,a.kodebarang, b.namabarang,b.satuan from\r\n          ".$dbname.'.kebun_pakai_material_vw a left join '.$dbname.".log_5masterbarang b\r\n          on a.kodebarang=b.kodebarang    \r\n          where  kodeorg like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and a.kodekegiatan like '%".$kegiatan."%'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $barang[$bar->notransaksi]['kodebarang'][] = $bar->kodebarang;
        $barang[$bar->notransaksi]['namabarang'][] = $bar->namabarang;
        $barang[$bar->notransaksi]['satuan'][] = $bar->satuan;
        $barang[$bar->notransaksi]['jumlah'][] = $bar->kwantitas;
    }
    $border = 0;
    if ('excel' === $proses) {
        $border = 1;
    }

    $str = 'select * from '.$dbname.".kebun_perawatan_dan_spk_vw where kodeorg like '".$kdAfd."%' \r\n             and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan like '%".$kegiatan."%'";
    $res = mysql_query($str);
    $stream .= "<table cellspacing='1' border='".$border."' class='sortable'>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n        <td>".$_SESSION['lang']['nomor']."</td>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>    \r\n\t<td>".$_SESSION['lang']['sumber']."</td>\r\n\t<td>".$_SESSION['lang']['tanggal']."</td>\r\n\t<td>".$_SESSION['lang']['lokasi']."</td>\r\n\t<td>".$_SESSION['lang']['kodekegiatan']."</td>            \r\n\t<td>".$_SESSION['lang']['kegiatan']."</td>\r\n\t<td>".$_SESSION['lang']['hasilkerjarealisasi']."</td>\r\n\t<td>".$_SESSION['lang']['satuan']."</td>\r\n        <td>".$_SESSION['lang']['jumlahhk']."</td>\r\n\t<td>".$_SESSION['lang']['upahkerja']."</td>\r\n\t<td>".$_SESSION['lang']['insentif']."</td>\r\n        <td>".$_SESSION['lang']['kodebarang']."</td> \r\n        <td>".$_SESSION['lang']['namabarang']."</td>\r\n        <td>".$_SESSION['lang']['jumlah']."</td>  \r\n        <td>".$_SESSION['lang']['satuan']."</td>     \r\n        </tr></thead>\r\n\t<tbody>";
    $no = 0;
    $oldnotrans = '';
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $notran = $bar->notransaksi;
        if ($notran !== $oldnotrans && 1 !== $no && is_array($barang[$oldnotrans]['kodebarang'])) {
            foreach ($barang[$oldnotrans]['kodebarang'] as $key => $val) {
                $stream .= "<tr class=rowcontent>\r\n                <td></td>\r\n                <td>".$oldnotrans."</td>    \r\n                <td>BKM</td>\r\n                <td></td>\r\n                <td></td>\r\n                <td></td>\r\n                <td></td>         \r\n                <td align=right></td>                 \r\n                <td></td>\r\n                <td align=right></td>\r\n                <td align=right></td>\r\n                <td align=right></td>\r\n                <td>".$barang[$oldnotrans]['kodebarang'][$key]."</td> \r\n                <td>".$barang[$oldnotrans]['namabarang'][$key]."</td>\r\n                <td>".$barang[$oldnotrans]['jumlah'][$key]."</td>  \r\n                <td>".$barang[$oldnotrans]['satuan'][$key]."</td>  \r\n                </tr>";
            }
        }

        if ('excel' === $proses) {
            $tampiltanggal = $bar->tanggal;
        } else {
            $tampiltanggal = tanggalnormal($bar->tanggal);
        }

        $stream .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>    \r\n            <td>".$bar->sumber."</td>\r\n            <td>".$tampiltanggal."</td>\r\n            <td>".$bar->kodeorg."</td>\r\n            <td>".$bar->kodekegiatan."</td>\r\n            <td>".$kamusKeg[$bar->kodekegiatan]."</td>         \r\n            <td align=right>".number_format($bar->hasilkerja, 2)."</td>                 \r\n            <td>".$bar->satuan."</td>\r\n            <td align=right>".number_format($bar->jumlahhk)."</td>\r\n            <td align=right>".number_format($bar->upah)."</td>\r\n            <td align=right>".number_format($bar->premi)."</td>\r\n            <td>-</td> \r\n            <td>-</td>\r\n            <td>-</td>  \r\n            <td>-</td>                  \r\n            </tr>";
        $oldnotrans = $notran;
        $thk += $bar->jumlahhk;
        $tupah += $bar->upah;
        $tpremi += $bar->premi;
    }
    if (is_array($barang[$oldnotrans]['kodebarang'])) {
        foreach ($barang[$oldnotrans]['kodebarang'] as $key => $val) {
            $stream .= "<tr class=rowcontent>\r\n                <td></td>\r\n                <td>".$oldnotrans."</td>    \r\n                <td>BKM</td>\r\n                <td></td>\r\n                <td></td>\r\n                <td></td>\r\n                <td></td>         \r\n                <td align=right></td>                 \r\n                <td></td>\r\n                <td align=right></td>\r\n                <td align=right></td>\r\n                <td align=right></td>\r\n                <td>".$barang[$oldnotrans]['kodebarang'][$key]."</td> \r\n                <td>".$barang[$oldnotrans]['namabarang'][$key]."</td>\r\n                <td>".$barang[$oldnotrans]['jumlah'][$key]."</td>  \r\n                <td>".$barang[$oldnotrans]['satuan'][$key]."</td>  \r\n                </tr>";
        }
    }

    $stream .= "\r\n\t<tr class=rowcontent>\r\n\t<td colspan=9>Total</td>\r\n\t<td align=right>".number_format($thk)."</td>\r\n\t<td align=right>".number_format($tupah)."</td>\r\n\t<td align=right>".number_format($tpremi)."</td>\r\n        <td>-</td> \r\n        <td>-</td>\r\n        <td>-</td>  \r\n        <td>-</td>  \r\n        </tbody></table>";
}

switch ($proses) {
    case 'getAfdAll':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n                where kodeorganisasi like '".$kdAfd."%' and length(kodeorganisasi)=6 and tipe in ('AFDELING','BIBITAN') order by namaorganisasi\r\n                ";
        $op = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $op .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
        echo $op;
        exit();
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHms');
        $nop_ = 'Laporan_perawatan'.$kdAfd.$tgl1_.'-'.$tgl2_.'_'.date('YmdHis');
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls.gz';\r\n                </script>";

        break;
}

?>