<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$regional = $_POST['regional'];
$str = 'select regional, tahunbudget, kodebarang, hargasatuan, sumberharga, variant, kodeorg, hargalalu from '.$dbname.".bgt_masterbarang\r\n      where tahunbudget = '".$tahunbudget."' and regional = '".$regional."' and kodeorg='".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."' order by regional";
$res = mysql_query($str);
$kobar = '';
while ($bar = mysql_fetch_object($res)) {
    $isidata[$bar->kodebarang][regional] = $bar->regional;
    $isidata[$bar->kodebarang][tahunbudget] = $bar->tahunbudget;
    $isidata[$bar->kodebarang][kodeorg] = $bar->kodeorg;
    $isidata[$bar->kodebarang][kodebarang] = $bar->kodebarang;
    $isidata[$bar->kodebarang][hargasatuan] = $bar->hargasatuan;
    $isidata[$bar->kodebarang][sumberharga] = $bar->sumberharga;
    $isidata[$bar->kodebarang][variant] = $bar->variant;
    $isidata[$bar->kodebarang][hargalalu] = $bar->hargalalu;
    $kobar .= "'".$bar->kodebarang."',";
}
$kobar = substr($kobar, 0, -1);
$str = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang\r\n where kodebarang in (".$kobar.')';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $isidata[$bar->kodebarang][namabarang] = $bar->namabarang;
    $isidata[$bar->kodebarang][satuan] = $bar->satuan;
}
if (empty($isidata)) {
} else {
    foreach ($isidata as $baris) {
        ++$no;
        echo '<tr id=barisl_'.$no.' class=rowcontent>';
        echo '<td>'.$no.'</td>';
        echo '<td><label id=tahun_'.$no.'>'.$baris[tahunbudget].'</td>';
        echo '<td><label id=reg_'.$no.'>'.$baris[regional].'</td>';
        echo '<td><label id=reg_'.$no.'>'.$baris[kodeorg].'</td>';
        echo '<td><label id=kode_'.$no.'>'.$baris[kodebarang].'</label></td>';
        echo '<td>'.$baris[namabarang].'</td>';
        echo '<td>'.$baris[satuan].'</td>';
        echo '<td><label id=sumber_'.$no.'>'.$baris[sumberharga].'</td>';
        echo '<td align=right><label id=lalu_'.$no.'>'.number_format($baris[hargalalu], 2).'</td>';
        echo '<td align=right><label id=var_'.$no.'>'.number_format($baris[variant], 2).'</td>';
        echo '<td align=right><label id=harga_'.$no.'>'.number_format($baris[hargasatuan], 2).'</td>';
        echo '</tr>';
    }
}

?>