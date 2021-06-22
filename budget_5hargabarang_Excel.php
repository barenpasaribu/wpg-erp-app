<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_GET['tahunbudget'];
$regional = $_GET['regional'];
$str = 'select closed, regional, tahunbudget, kodebarang, hargasatuan, sumberharga, variant, hargalalu from '.$dbname.".bgt_masterbarang\r\n      where tahunbudget = '".$tahunbudget."' and regional = '".$regional."' order by regional";
$res = mysql_query($str);
$kobar = '';
$tutup = 0;
while ($bar = mysql_fetch_object($res)) {
    $isidata[$bar->kodebarang][regional] = $bar->regional;
    $isidata[$bar->kodebarang][tahunbudget] = $bar->tahunbudget;
    $isidata[$bar->kodebarang][kodebarang] = $bar->kodebarang;
    $isidata[$bar->kodebarang][hargasatuan] = $bar->hargasatuan;
    $isidata[$bar->kodebarang][sumberharga] = $bar->sumberharga;
    $isidata[$bar->kodebarang][variant] = $bar->variant;
    $isidata[$bar->kodebarang][hargalalu] = $bar->hargalalu;
    $kobar .= "'".$bar->kodebarang."',";
    if ('1' === $bar->closed) {
        $tutup = 1;
    }
}
$kobar = substr($kobar, 0, -1);
$str = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang\r\n      where kodebarang in (".$kobar.')';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $isidata[$bar->kodebarang][namabarang] = $bar->namabarang;
    $isidata[$bar->kodebarang][satuan] = $bar->satuan;
}
$stream = '<table border=1>';
$stream .= "<tr>\r\n    <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n    <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n    <td align=center>".$_SESSION['lang']['regional']."</td>\r\n    <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n    <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n    <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n    <td align=center>".$_SESSION['lang']['sumberHarga']."</td>\r\n    <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>\r\n    <td align=center>".$_SESSION['lang']['varian']."</td>\r\n    <td align=center>".$_SESSION['lang']['hargabudget']."</td>\r\n</tr>";
if (empty($isidata)) {
} else {
    foreach ($isidata as $baris) {
        ++$no;
        $stream .= '<tr id=barisl_'.$no.' class=rowcontent>';
        $stream .= '<td>'.$no.'</td>';
        $stream .= '<td><label id=tahun_'.$no.'>'.$baris[tahunbudget].'</td>';
        $stream .= '<td><label id=reg_'.$no.'>'.$baris[regional].'</td>';
        $stream .= '<td><label id=kode_'.$no.'>'.$baris[kodebarang].'</label></td>';
        $stream .= '<td>'.$baris[namabarang].'</td>';
        $stream .= '<td>'.$baris[satuan].'</td>';
        $stream .= '<td><label id=sumber_'.$no.'>'.$baris[sumberharga].'</td>';
        $stream .= '<td><label id=lalu_'.$no.'>'.number_format($baris[hargalalu], 2).'</td>';
        $stream .= '<td><label id=var_'.$no.'>'.number_format($baris[variant], 2).'</td>';
        $stream .= '<td><label id=harga_'.$no.'>'.number_format($baris[hargasatuan], 2).'</td>';
        $stream .= '</tr>';
    }
}

$stream .= '</table>';
if (1 === $tutup) {
    $stream .= 'price closed.';
} else {
    $stream .= 'price still open.';
}

$qwe = date('YmdHms');
$nop_ = 'Budget_HargaBarang_'.$tahunbudget.$regional.' '.$qwe;
if (0 < strlen($stream)) {
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>