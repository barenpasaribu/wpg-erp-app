<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . ' HKE</legend><table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n\t" . '   <td>Tahun Budget</td>' . "\r\n\t" . '   <td>Jlh.hari.Setahun</td>' . "\r\n\t" . '   <td>Jlh.hari.Minggu</td>' . "\r\n\t" . '   <td>Jlh.Hari.Libur</td>' . "\r\n\t" . '   <td>Jlh.HaliLiburMinggu</td>' . "\r\n\t" . '   <td>HK.Effektif</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
$str = 'select * from ' . $dbname . '.bgt_hk  order by tahunbudget desc';

#exit(mysql_error($conn));
($res = mysql_query($str)) || true;

while ($bar = mysql_fetch_assoc($res)) {
	$a[$bar['tahunbudget']] = intval($bar['harisetahun']);
	$b[$bar['tahunbudget']] = intval($bar['hrminggu']);
	$c[$bar['tahunbudget']] = intval($bar['hrlibur']);
	$d[$bar['tahunbudget']] = intval($bar['hrliburminggu']);
	$hasil[$bar['tahunbudget']] = $a[$bar['tahunbudget']] - ($b[$bar['tahunbudget']] + $c[$bar['tahunbudget']]) - $d[$bar['tahunbudget']];
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['tahunbudget'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['harisetahun'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrminggu'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrlibur'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrliburminggu'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $hasil[$bar['tahunbudget']] . '</td>' . "\r\n\t\t" . '</tr>';
}

echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
