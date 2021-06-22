<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
('' != $_POST['pt'] ? ($pt = $_POST['pt']) : ($pt = $_GET['pt']));
('' != $_POST['unit'] ? ($unit = $_POST['unit']) : ($unit = $_GET['unit']));
$tanggaldr = tanggalsystem($_POST['tanggaldari']);
$tanggaldari = substr($tanggaldr, 0, 4).'-'.substr($tanggaldr, 4, 2).'-'.substr($tanggaldr, 6, 2);
$tanggalsd = tanggalsystem($_POST['tanggalsampai']);
$tanggalsampai = substr($tanggalsd, 0, 4).'-'.substr($tanggalsd, 4, 2).'-'.substr($tanggalsd, 6, 2);
if ('' == $tanggaldr) {
    $tanggaldari = tanggalsystem($_GET['tanggaldari']);
    $tanggalsampai = tanggalsystem($_GET['tanggalsampai']);
}

if ('' == $_GET['type'] && ('' == $tanggaldari || '' == $tanggalsampai)) {
    echo 'Warning: silakan mengisi tanggal';
    exit();
}

if ('EN' == $_SESSION['language']) {
    $zz = 'namakegiatan1 as namakegiatan';
} else {
    $zz = 'namakegiatan';
}

$strh = 'SELECT kodekegiatan, '.$zz.', satuan FROM '.$dbname.'.setup_kegiatan';
$resh = mysql_query($strh);
while ($barh = mysql_fetch_object($resh)) {
    $kamusnama[$barh->kodekegiatan] = $barh->namakegiatan;
    $kamussatuan[$barh->kodekegiatan] = $barh->satuan;
}
if ('' != $unit) {
    $where = "and substr(nopp,16,4)='".$unit."'";
    $whr = "and b.kodeorg='".$unit."'";
} else {
    $where = 'and substr(nopp,16,4) in (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."'\r\n            and length(kodeorganisasi)=4)";
    $whr = 'and b.kodeorg in (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."'\r\n          and length(kodeorganisasi)=4 )";
}

$str = "SELECT tanggal, nopo, nopp, jumlahpesan, hargasatuan, satuan, namasupplier, namabarang\r\n      FROM ".$dbname.".log_po_vw\r\n      WHERE tanggal between '".$tanggaldari."' and '".$tanggalsampai."' and kodeorg = '".$pt."' \r\n      ".$where.'  ';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = $bar->tanggal.$bar->nopo.$bar->namabarang;
    $isidata[$qwe]['tang'] = $bar->tanggal;
    $isidata[$qwe]['tipe'] = 'PO';
    $isidata[$qwe]['nopo'] = $bar->nopo;
    $isidata[$qwe]['supp'] = $bar->namasupplier;
    $isidata[$qwe]['nopp'] = $bar->nopp;
    $isidata[$qwe]['bara'] = $bar->namabarang;
    $isidata[$qwe]['satu'] = $bar->satuan;
    $isidata[$qwe]['juml'] = $bar->jumlahpesan;
    $isidata[$qwe]['harg'] = $bar->hargasatuan;
    $isidata[$qwe]['tota'] = $bar->jumlahpesan * $bar->hargasatuan;
}
$str = "SELECT a.tanggal, a.notransaksi, a. kodekegiatan, sum(a.hasilkerjarealisasi) as jumlah, sum(a.jumlahrealisasi) as total, c.namasupplier \r\n    FROM ".$dbname.".log_baspk a\r\n    LEFT JOIN ".$dbname.".log_spkht b on a.notransaksi=b.notransaksi\r\n    LEFT JOIN ".$dbname.".log_5supplier c on b.koderekanan=c.supplierid\r\n    WHERE a.tanggal between '".$tanggaldari."' and '".$tanggalsampai."' ".$whr." \r\n    GROUP BY a.tanggal, a.notransaksi";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwe = $bar->tanggal.$bar->notransaksi;
    $isidata[$qwe]['tang'] = $bar->tanggal;
    $isidata[$qwe]['tipe'] = 'SPK';
    $isidata[$qwe]['nopo'] = $bar->notransaksi;
    $isidata[$qwe]['supp'] = $bar->namasupplier;
    $isidata[$qwe]['nopp'] = '';
    $isidata[$qwe]['bara'] = $kamusnama[$bar->kodekegiatan];
    $isidata[$qwe]['satu'] = $kamussatuan[$bar->kodekegiatan];
    $isidata[$qwe]['juml'] = $bar->jumlah;
    $harga = $bar->total / $bar->jumlah;
    $isidata[$qwe]['harg'] = $harga;
    $isidata[$qwe]['tota'] = $bar->total;
}
if (!empty($isidata)) {
    foreach ($isidata as $c => $key) {
        $sort_tang[] = $key['tang'];
        $sort_nopo[] = $key['nopo'];
        $sort_bara[] = $key['bara'];
    }
}

if (!empty($isidata)) {
    array_multisort($sort_tang, SORT_ASC, $sort_nopo, SORT_ASC, $sort_bara, SORT_ASC, $isidata);
}

if ('excel' == $_GET['type']) {
    $stream .= '<table cellspacing=1 border=1 width=100%>';
    $bg = 'bgcolor=#DEDEDE';
    $stream .= 'Tax Planning : '.tanggalsistem($tanggaldari).' s.d.'.tanggalsistem($tanggalsampai).'';
} else {
    $stream .= '<table class=sortable cellspacing=1 border=0 width=100%>';
    $stream .= 'Tax Planning : '.tanggalsistem($tanggaldr).' s.d.'.tanggalsistem($tanggalsd).'';
}

$stream .= "\r\n    <thead>\r\n    <tr>\r\n        <td ".$bg." align=center>No.</td>\r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['tipe']."</td>\r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['nopo'].'/'.$_SESSION['lang']['kontrak']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['supplier']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['nopp']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['namabarang'].'/'.$_SESSION['lang']['pekerjaan']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['satuan']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['jumlahbarang'].'/'.$_SESSION['lang']['hasilkerjad']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['hargasatuan']."</td>  \r\n        <td ".$bg.' align=center>'.$_SESSION['lang']['total']."</td>  \r\n    </tr>  \r\n    </thead>\r\n    <tbody>";
$res = mysql_query($str);
$no = 0;
if (!empty($isidata)) {
    foreach ($isidata as $baris) {
        if ('excel' != $_GET['type']) {
            $tampiltanggal = tanggalnormal($baris['tang']);
        } else {
            $tampiltanggal = $baris['tang'];
        }

        ++$no;
        $total = 0;
        $stream .= "<tr class=rowcontent>\r\n            <td align=right>".$no."</td>\r\n            <td>".$tampiltanggal."</td>\r\n            <td>".$baris['tipe']."</td>\r\n            <td>".$baris['nopo']."</td>\r\n            <td>".$baris['supp']."</td>\r\n            <td>".$baris['nopp']."</td>\r\n            <td>".$baris['bara']."</td>\r\n            <td>".$baris['satu']."</td>\r\n            <td align=right>".number_format($baris['juml'], 2)."</td>\r\n            <td align=right>".number_format($baris['harg'], 0)."</td>\r\n            <td align=right>".number_format($baris['tota'], 0)."</td>\r\n            </tr>";
        $totalhasil += $bar->hasil;
        $totalkomunikasi += $bar->komunikasi;
        $totalwaktu += $bar->waktu;
    }
} else {
    $stream .= '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
}

$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\t\t \r\n    </table>";
if ('unit' == $_GET['type']) {
    $opt_unit = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    $s_unit = 'select * from '.$dbname.".organisasi where induk='".$pt."' order by kodeorganisasi asc";
    $q_unit = mysql_query($s_unit);
    while ($r_unit = mysql_fetch_assoc($q_unit)) {
        $opt_unit .= "<option value='".$r_unit['kodeorganisasi']."'>".$r_unit['namaorganisasi'].'</option>';
    }
    echo $opt_unit;
} else {
    if ('excel' == $_GET['type']) {
        $stream .= '<br>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'taxplan_'.$tanggaldari.'sd'.$tanggalsampai;
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
                echo "<script language=javascript1.2>\r\n            parent.window.alert('Can't convert to excel format');\r\n            </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }
    } else {
        echo $stream;
    }
}

function tanggalsistem($qwe)
{
    $tahun = substr($qwe, 0, 4);
    $bulan = substr($qwe, 4, 2);
    $tanggal = substr($qwe, 6, 2);

    return $tanggal.'-'.$bulan.'-'.$tahun;
}

?>