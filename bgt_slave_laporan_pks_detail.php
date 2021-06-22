<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo '<link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n";
$station = $_GET['station'];
$kdbudget = $_GET['kdbudget'];
$tahun = $_GET['tahun'];
$str = 'select a.*,b.namabarang,c.nama from ' . $dbname . '.bgt_budget_detail a left join ' . "\r\n" . '      ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang left join ' . "\r\n" . '      ' . $dbname . '.bgt_kode c on a.kodebudget=c.kodebudget' . "\r\n" . '      where a.kodeorg like \'' . $station . '%\' and a.kodebudget like \'' . $kdbudget . '%\' ' . "\r\n" . '      and a.tahunbudget=' . $tahun;
echo 'Unit:' . $station . ' Tahun Budget:' . $tahun . "\r\n" . '     <table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['mesin'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['kodeabs'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>    ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jumlah'] . '</td> ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['satuan'] . '</td>                ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>     ' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
$no = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $no . '</td>' . "\r\n" . '           <td>' . $bar->kodeorg . '</td>' . "\r\n" . '           <td>' . $bar->nama . '</td>' . "\r\n" . '           <td>' . $bar->namabarang . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->jumlah, 0, '.', ',') . '</td>' . "\r\n" . '           <td>' . $bar->satuanj . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>   ' . "\r\n" . '         </tr>';
}

echo '</tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';

?>
