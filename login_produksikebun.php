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
$str = 'SELECT kodeorganisasi, namaorganisasi FROM ' . $dbname . '.organisasi' . "\r\n" . '    WHERE tipe in (\'KEBUN\',\'AFDELING\')';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$kamuskodeorg[$bar->kodeorganisasi] = $bar->namaorganisasi;
}

$str = 'SELECT kodeorg, substr(tanggal,1,10) as tanggal, substr(nospb,9,6) as afdeling, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) between \'' . $tahun . '-01-01\' and \'' . $hariini . '\' and kodeorg != \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY substr(nospb,9,6), substr(tanggal,1,10)' . "\r\n" . '    ORDER BY substr(nospb,9,6)';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $hariini) {
		$arey[$bar->afdeling] += 'hi';
		$totalsub[$bar->kodeorg] += 'hi';
		$total += 'hi';
	}

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$arey[$bar->afdeling] += 'maren';
		$totalsub[$bar->kodeorg] += 'maren';
		$total += 'maren';
	}

	if ((substr($bar->tanggal, 0, 7) == $tahun . '-' . $bulan) && (substr($bar->tanggal, 0, 10) <= $hariini)) {
		$arey[$bar->afdeling] += 'bi';
		$totalsub[$bar->kodeorg] += 'bi';
		$total += 'bi';
	}

	if ((substr($bar->tanggal, 0, 4) == $tahun) && (substr($bar->tanggal, 0, 10) <= $hariini)) {
		$arey[$bar->afdeling] += 'sdbi';
		$totalsub[$bar->kodeorg] += 'sdbi';
		$total += 'sdbi';
	}
}

$str = 'SELECT substr(afdeling,1,4) as kodeorg, tanggal, afdeling, sum(jjgmasak*bjr) as beratbersih FROM ' . $dbname . '.kebun_taksasi ' . "\r\n" . '    WHERE tanggal between \'' . $tahun . '-01-01\' and \'' . $hariini . '\' and afdeling not like \'1%\'' . "\r\n" . '    GROUP BY afdeling, tanggal' . "\r\n" . '    ORDER BY afdeling';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $hariini) {
		$areytak[$bar->afdeling] += 'hi';
		$totalsubtak[$bar->kodeorg] += 'hi';
		$totaltak += 'hi';
	}

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$areytak[$bar->afdeling] += 'maren';
		$totalsubtak[$bar->kodeorg] += 'maren';
		$totaltak += 'maren';
	}
}

@$qwein = $total['hi'] / 1000;
@$qweintak = $totaltak['hi'] / 1000;
$qwe = 'Produksi Kebun ' . $tanggal . ' = ' . number_format($qwein, 2) . ' ton / Taksasi = ' . number_format($qweintak, 2) . ' ton';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tr class=rowcontent>' . "\r\n" . '    <td>' . $qwe . '</td>' . "\r\n" . '    <td align=right width=1% nowrap>' . $updatetime . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </table>';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:80px;\'>Unit</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:120px;\'>Hari Ini (T)</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:120px;\'>Kemarin (T)</td>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:80px;\'>Bulan Ini (T)</td>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:100px;\'>sd Bulan Ini (T)</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center>Taks.</td>' . "\r\n" . '        <td align=center>Real.</td>' . "\r\n" . '        <td align=center>Taks.</td>' . "\r\n" . '        <td align=center>Real.</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody></tbody></table>';
echo '<marquee height=180 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>' . "\r\n" . '    <table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tbody>';

if (!empty($kebun)) {
	foreach ($kebun as $buu) {
		echo '<tr class=rowtitle>';
		echo '<td style=\'width:80px;\'>' . $buu . '</td>';
		@$qweintak = $totalsubtak[$buu]['hi'] / 1000;
		@$qwein = $totalsub[$buu]['hi'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qweintak, 2) . '</td>';
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qweintak = $totalsubtak[$buu]['maren'] / 1000;
		@$qwein = $totalsub[$buu]['maren'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qweintak, 2) . '</td>';
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $totalsub[$buu]['bi'] / 1000;
		echo '<td align=right style=\'width:80px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $totalsub[$buu]['sdbi'] / 1000;
		echo '<td align=right style=\'width:100px;\'>' . number_format($qwein, 2) . '</td>';
		echo '</tr>';

		if (!empty($unit)) {
			foreach ($unit as $uun) {
				if (substr($uun, 0, 4) == $buu) {
					echo '<tr class=rowcontent>';
					echo '<td>&nbsp; &nbsp;' . $uun . '</td>';
					@$qweintak = $areytak[$uun]['hi'] / 1000;
					@$qwein = $arey[$uun]['hi'] / 1000;
					echo '<td align=right>' . number_format($qweintak, 2) . '</td>';
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qweintak = $areytak[$uun]['maren'] / 1000;
					@$qwein = $arey[$uun]['maren'] / 1000;
					echo '<td align=right>' . number_format($qweintak, 2) . '</td>';
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qwein = $arey[$uun]['bi'] / 1000;
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qwein = $arey[$uun]['sdbi'] / 1000;
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					echo '</tr>';
				}
			}
		}
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
echo '</tbody>' . "\r\n" . '    </table>' . "\r\n" . '    * sumber data: timbangan + taksasi kebun' . "\r\n" . '    </marquee>';

?>
