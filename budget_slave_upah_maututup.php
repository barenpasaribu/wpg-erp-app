<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('' === $tahunbudget) {
    echo 'WARNING: silakan memilih tahun budget.';
    exit();
}

$str2 = 'select a.golongan, a.jumlah, b.nama as namagolongan from '.$dbname.".bgt_upah a\r\n    left join ".$dbname.".bgt_kode b on a.golongan=b.kodebudget\r\n    where a.tahunbudget = '".$tahunbudget."' and a.kodeorg = '".$kodeorg."'\r\n    order by a.golongan";
$res2 = mysql_query($str2);
while ($bar2 = mysql_fetch_object($res2)) {
    $isidata[$bar2->golongan][kodegolongan] = $bar2->golongan;
    $isidata[$bar2->golongan][upah] = $bar2->jumlah;
    $isidata[$bar2->golongan][namagolongan] = $bar2->namagolongan;
}
echo '<button class=mybutton id=tutup onclick=tutupHarga(1)>'.$_SESSION['lang']['close'].'</button>';
echo "<table cellspacing=1 border=0 class=sortable>\r\n    <thead>\r\n    <tr class=\"rowheader\">\r\n    <td>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\r\n    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n    <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n    <td>".$_SESSION['lang']['levelname']."</td>\r\n    <td>".$_SESSION['lang']['upahkerja']."</td>\r\n    </tr></thead><tbody>";
foreach ($isidata as $baris) {
    ++$no;
    echo '<tr id=baris2_'.$no.' class=rowcontent>';
    echo '<td>'.$no.'</td>';
    echo '<td><label id=kodeorg2_'.$no.'>'.$kodeorg.'</td>';
    echo '<td><label id=kodegolongan2_'.$no.'>'.$baris[kodegolongan].'</td>';
    echo '<td>'.$baris[namagolongan].'</td>';
    echo '<td align=right><label id=upah2_'.$no.'>'.number_format($baris[upah]).'</td>';
    echo '</tr>';
}
echo '</tbody></table>';

?>