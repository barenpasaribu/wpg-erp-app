<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$unit = $_POST['unit'];
$periode = $_POST['periode'];
if ('' === $periode) {
    echo 'Warning: Period is obligatory';
    exit();
}

$str = 'select induk from '.$dbname.".organisasi\r\n      where kodeorganisasi ='".$unit."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $induk = $bar->induk;
    $hasil .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
$str = 'select tanggalmulai, tanggalsampai from '.$dbname.".setup_periodeakuntansi\r\n      where kodeorg ='".$unit."' and periode='".$periode."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $tanggalmulai = $bar->tanggalmulai;
    $tanggalsampai = $bar->tanggalsampai;
}
if ('EN' === $_SESSION['language']) {
    $zz = ' b.namaakun1';
} else {
    $zz = 'b.namaakun';
}

$str = 'select a.nojurnal as nojurnal, a.tanggal as tanggal, a.keterangan as keterangan, a.noakun as noakun, '.$zz." as namaakun, a.debet as debet, a.kredit as kredit, a.kodeblok as kodeorg, a.kodevhc as kodevhc  \r\n                  from ".$dbname.".keu_jurnaldt_vw a\r\n                  left join ".$dbname.".keu_5akun b on a.noakun = b.noakun\r\n                  where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.noreferensi in ('ALK_KERJA_AB') and a.kodeorg = '".$unit."' \r\n                  order by a.tanggal";
$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=17>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $total = 0;
        $total = $bar->jumlah * $bar->hargasatuan;
        echo "<tr class=rowcontent>\r\n                                  <td align=right>".$no."</td>\r\n                                  <td>".$bar->nojurnal."</td>\r\n                                  <td align=right>".tanggalnormal($bar->tanggal)."</td>\r\n                                  <td>".$bar->keterangan."</td>\r\n                                  <td align=right>".$bar->noakun."</td>\r\n                                  <td>".$bar->namaakun."</td>\r\n                                  <td align=right>".number_format($bar->debet)."</td>\r\n                                  <td align=right>".number_format($bar->kredit)."</td>\r\n                                  <td>".$bar->kodeorg."</td>\r\n                                  <td>".$bar->kodevhc."</td>\r\n                                </tr>";
        $gtotal += $total;
    }
}

?>