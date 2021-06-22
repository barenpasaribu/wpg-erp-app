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

$str = 'SELECT kodeorg, substr(tanggal,1,10) as tanggal, substr(nospb,9,6) as afdeling, sum(beratbersih) as beratbersih FROM ' . $dbname . '.pabrik_timbangan ' . "\r\n" . '    WHERE substr(tanggal,1,10) = \'' . $kemarin . '\' and kodeorg != \'\' and kodebarang = \'40000003\'' . "\r\n" . '    GROUP BY substr(nospb,9,6), substr(tanggal,1,10)' . "\r\n" . '    ORDER BY substr(nospb,9,6)';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$arey[$bar->afdeling] += 'kgreamaren';
		$totalsub[$bar->kodeorg] += 'kgreamaren';
		$total += 'kgreamaren';
	}
}

$str = 'SELECT substr(afdeling,1,4) as kodeorg, tanggal, afdeling, sum(jjgmasak*bjr) as beratbersih, sum(hkdigunakan) as hk FROM ' . $dbname . '.kebun_taksasi ' . "\r\n" . '    WHERE tanggal between \'' . $kemarin . '\' and \'' . $hariini . '\' and afdeling not like \'1%\'' . "\r\n" . '    GROUP BY afdeling, tanggal' . "\r\n" . '    ORDER BY afdeling';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $hariini) {
		$arey[$bar->afdeling] += 'hktak';
		$totalsub[$bar->kodeorg] += 'hktak';
		$total += 'hktak';
	}

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$arey[$bar->afdeling] += 'hktakmaren';
		$totalsub[$bar->kodeorg] += 'hktakmaren';
		$total += 'hktakmaren';
	}
}

$str = 'SELECT unit as kodeorg, tanggal, substr(kodeorg,1,6) as afdeling, sum(hasilkerjakg) as beratbersih, count(*) as hk FROM ' . $dbname . '.kebun_prestasi_vw' . "\r\n" . '    WHERE tanggal = \'' . $hariini . '\'' . "\r\n" . '    GROUP BY afdeling, tanggal' . "\r\n" . '    ORDER BY afdeling';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $hariini) {
		$arey[$bar->afdeling] += 'hkrea';
		$totalsub[$bar->kodeorg] += 'hkrea';
		$total += 'hkrea';
	}
}

$str = 'SELECT unit as kodeorg, tanggal, substr(kodeorg,1,6) as afdeling, sum(hasilkerjakg) as beratbersih, count(*) as hk FROM ' . $dbname . '.kebun_prestasi_vw' . "\r\n" . '    WHERE tanggal = \'' . $kemarin . '\'' . "\r\n" . '    GROUP BY afdeling, tanggal' . "\r\n" . '    ORDER BY afdeling';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->afdeling] = $bar->afdeling;
	$kebun[$bar->kodeorg] = $bar->kodeorg;

	if (substr($bar->tanggal, 0, 10) == $kemarin) {
		$arey[$bar->afdeling] += 'hkreamaren';
		$totalsub[$bar->kodeorg] += 'hkreamaren';
		$total += 'hkreamaren';
		$arey[$bar->afdeling] += 'kgpanmaren';
		$totalsub[$bar->kodeorg] += 'kgpanmaren';
		$total += 'kgpanmaren';
	}
}

$qwe = 'Taksasi Panen ' . $tanggal . '';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tr class=rowcontent>' . "\r\n" . '    <td>' . $qwe . '</td>' . "\r\n" . '    <td align=right width=1% nowrap>' . $updatetime . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </table>';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:60px;\'>Unit</td>' . "\r\n" . '        <td align=center colspan=2>Hari Ini</td>' . "\r\n" . '        <td align=center colspan=5>Kemarin</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center style=\'width:60px;\'>HK Taks.</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>HK Real.</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>HK Taks.</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>HK Real.</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>KG Pabrik (T)</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>KG Kebun (T)</td>' . "\r\n" . '        <td align=center style=\'width:60px;\'>KG Selisih (T)</td>' . "\r\n" . '        <!--<td align=center>Restan</td>-->' . "\r\n" . '    </tr>  ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody></tbody></table>';
echo '<marquee height=150 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>' . "\r\n" . '    <table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tbody>';

if (!empty($kebun)) {
	foreach ($kebun as $buu) {
		echo '<tr class=rowtitle>';
		echo '<td style=\'width:60px;\'>' . $buu . '</td>';
		@$qwein = $totalsub[$buu]['hktak'];
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein) . '</td>';
		@$qwein = $totalsub[$buu]['hkrea'];
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein) . '</td>';
		@$qwein = $totalsub[$buu]['hktakmaren'];
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein) . '</td>';
		@$qwein = $totalsub[$buu]['hkreamaren'];
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein) . '</td>';
		@$qwein = $totalsub[$buu]['kgreamaren'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $totalsub[$buu]['kgpanmaren'] / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = ($totalsub[$buu]['kgreamaren'] - $totalsub[$buu]['kgpanmaren']) / 1000;
		echo '<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>';
		@$qwein = $totalsub[$buu]['kgres'] / 1000;
		echo '<!--<td align=right style=\'width:60px;\'>' . number_format($qwein, 2) . '</td>-->';
		echo '</tr>';

		if (!empty($unit)) {
			foreach ($unit as $uun) {
				if (substr($uun, 0, 4) == $buu) {
					echo '<tr class=rowcontent>';
					echo '<td>&nbsp; &nbsp;' . $uun . '</td>';
					@$qwein = $arey[$uun]['hktak'];
					echo '<td align=right>' . number_format($qwein) . '</td>';
					@$qwein = $arey[$uun]['hkrea'];
					echo '<td align=right>' . number_format($qwein) . '</td>';
					@$qwein = $arey[$uun]['hktakmaren'];
					echo '<td align=right>' . number_format($qwein) . '</td>';
					@$qwein = $arey[$uun]['hkreamaren'];
					echo '<td align=right>' . number_format($qwein) . '</td>';
					@$qwein = $arey[$uun]['kgreamaren'] / 1000;
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qwein = $arey[$uun]['kgpanmaren'] / 1000;
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qwein = ($arey[$uun]['kgreamaren'] - $arey[$uun]['kgpanmaren']) / 1000;
					echo '<td align=right>' . number_format($qwein, 2) . '</td>';
					@$qwein = $arey[$uun]['kgres'] / 1000;
					echo '<!--<td align=right>' . number_format($qwein, 2) . '</td>-->';
					echo '</tr>';
				}
			}
		}
	}
}

echo '<tr class=rowtitle>';
echo '<td>Total</td>';
@$qwein = $total['hktak'];
echo '<td align=right>' . number_format($qwein) . '</td>';
@$qwein = $total['hkrea'];
echo '<td align=right>' . number_format($qwein) . '</td>';
@$qwein = $total['hktakmaren'];
echo '<td align=right>' . number_format($qwein) . '</td>';
@$qwein = $total['hkreamaren'];
echo '<td align=right>' . number_format($qwein) . '</td>';
@$qwein = $total['kgreamaren'] / 1000;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qwein = $total['kgpanmaren'] / 1000;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qwein = ($total['kgreamaren'] - $total['kgpanmaren']) / 1000;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
@$qwein = $total['kgres'] / 1000;
echo '<!--<td align=right>' . number_format($qwein, 2) . '</td>-->';
echo '</tr>';
echo '</tbody>' . "\r\n" . '    </table>' . "\r\n" . '    * sumber data: taksasi + panen + timbangan' . "\r\n" . '    </marquee>';

?>
