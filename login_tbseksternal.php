<?php


echo '<link rel=stylesheet type=text/css href=\'style/generic.css\'>' . "\r\n";
require_once 'config/connection.php';
$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());
$updatetime = date('d M Y H:i:s', time());
$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt - 86400);
$str = 'SELECT kodetimbangan, namasupplier FROM ' . $dbname . '.log_5supplier' . "\r\n" . '    WHERE kodetimbangan is not null';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$kamuskodeorg[$bar->kodetimbangan] = $bar->namasupplier;
}

$str = 'SELECT kodecustomer as kodeorg, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) like \'' . $hariini . '%\' and kodeorg = \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY kodecustomer' . "\r\n" . '    ORDER BY kodecustomer';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['hi'] = $bar->beratbersih;
	$total += 'hi';
}

$str = 'SELECT kodecustomer as kodeorg, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) like \'' . $kemarin . '%\' and kodeorg = \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY kodecustomer' . "\r\n" . '    ORDER BY kodecustomer';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['maren'] = $bar->beratbersih;
	$total += 'maren';
}

$str = 'SELECT kodecustomer as kodeorg, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) between \'' . $tahun . '-' . $bulan . '-01\' and \'' . $hariini . '\' and kodeorg = \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY kodecustomer' . "\r\n" . '    ORDER BY kodecustomer';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['bi'] = $bar->beratbersih;
	$total += 'bi';
}

$str = 'SELECT kodecustomer as kodeorg, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) between \'' . $tahun . '-01-01\' and \'' . $hariini . '\' and kodeorg = \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY kodecustomer' . "\r\n" . '    ORDER BY kodecustomer';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['sdbi'] = $bar->beratbersih;
	$total += 'sdbi';
}

$str = 'SELECT afdeling as kodeorg, tanggal, sum(kg) as beratbersih FROM ' . $dbname . '.kebun_taksasi ' . "\r\n" . '    WHERE tanggal between \'' . $tahun . '-01-01\' and \'' . $hariini . '\' and afdeling like \'1%\'' . "\r\n" . '    GROUP BY afdeling, tanggal' . "\r\n" . '    ORDER BY afdeling';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $hariini) {
		$areytak[$bar->kodeorg] += 'hi';
		$totaltak += 'hi';
	}

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$areytak[$bar->kodeorg] += 'maren';
		$totaltak += 'maren';
	}
}

@$qwein = $total['hi'] / 1000;
@$qweintak = $totaltak['hi'] / 1000;
$qwe = 'TBS Eksternal ' . $tanggal . ' = ' . number_format($qwein, 2) . ' ton / Taksasi = ' . number_format($qweintak, 2) . ' ton';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tr class=rowcontent>' . "\r\n" . '    <td>' . $qwe . '</td>' . "\r\n" . '    <td align=right width=1% nowrap>' . $updatetime . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </table>';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:120px;\'>Unit</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:120px;\'>Hari Ini (T)</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:120px;\'>Kemarin (T)</td>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:60px;\'>Bulan Ini (T)</td>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:60px;\'>sd Bulan Ini (T)</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center>Taks.</td>' . "\r\n" . '        <td align=center>Real.</td>' . "\r\n" . '        <td align=center>Taks.</td>' . "\r\n" . '        <td align=center>Real.</td>' . "\r\n" . '    </tr> ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody></tbody></table>';
echo '<marquee height=100 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>' . "\r\n" . '    <table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tbody>';

if (!empty($unit)) {
	foreach ($unit as $uun) {
		echo '<tr class=rowcontent>';
		echo '<td style=\'width:120px;\'>' . $kamuskodeorg[$uun] . '</td>';
		@$qweintak = $areytak[$uun]['hi'] / 1000;
		@$qwein = $arey[$uun]['hi'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qweintak, 2) . '</td>';
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qweintak = $areytak[$uun]['maren'] / 1000;
		@$qwein = $arey[$uun]['maren'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qweintak, 2) . '</td>';
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $arey[$uun]['bi'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $arey[$uun]['sdbi'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		echo '</tr>';
	}
}

echo '<tr class=rowtitle>';
echo '<td>Total</td>';
@$qweintak = $totaltak['hi'] / 1000;
@$qwein = $total['hi'] / 1000;
echo '<td align=right>' . number_format($qweintak, 2) . '</td>';
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qweintak = $totaltak['maren'] / 1000;
@$qwein = $total['maren'] / 1000;
echo '<td align=right>' . number_format($qweintak, 2) . '</td>';
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qwein = $total['bi'] / 1000;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qwein = $total['sdbi'] / 1000;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
echo '</tr>';
echo '</tbody>' . "\r\n" . '    </table>' . "\r\n" . '    * sumber data: timbangan + taksasi eksternal' . "\r\n" . '    </marquee>';

?>
