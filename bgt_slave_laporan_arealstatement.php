<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahun = $_POST['tahun'];
$kebun = $_POST['kebun'];

if ($tahun == '') {
	echo 'WARNING: silakan mengisi tahun.';
	exit();
}

if ($kebun == '') {
	echo 'WARNING: silakan mengisi kebun.';
	exit();
}

$isidata = array();
$str = 'select sum(hathnini) as hathnini,sum(hanonproduktif) as hanonproduktif,sum(pokokproduksi) as pokokproduksi,' . "\r\n" . '      thntnm,substr(kodeblok,1,6) as afdeling,statusblok,sum(pokokthnini) as pokokthnini from ' . $dbname . '.bgt_blok where' . "\r\n" . '      substr(kodeblok,1,4)=\'' . $kebun . '\' and tahunbudget = \'' . $tahun . '\' and statusblok != \'BBT\' group by substr(kodeblok,1,6),thntnm,statusblok' . "\r\n" . '      order by substr(kodeblok,1,6),thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if (($bar->thntnm + 3) < $tahun) {
		if ($bar->statusblok != 'CADANGAN') {
			$isidata[$bar->thntnm . $bar->statusblok] += $bar->afdeling;
			$totalrowdata[$bar->thntnm . $bar->statusblok] += total;
			$totalcolumndata[$bar->afdeling . $bar->statusblok] += total;
			$total += $bar->statusblok;
			$rowdata0[$bar->thntnm . $bar->statusblok] = $bar->thntnm;
		}
	}
	else if ($bar->statusblok != 'CADANGAN') {
		if ($bar->statusblok == 'TB') {
			$bar->statusblok = 'TBM';
		}

		$isidata1[$bar->thntnm . $bar->statusblok] += $bar->afdeling;
		$totalrowdata1[$bar->thntnm . $bar->statusblok] += total;
		$totalcolumndata1[$bar->afdeling . $bar->statusblok] += total;
		$total1 += $bar->statusblok;
		$rowdata1[$bar->thntnm . $bar->statusblok] = $bar->thntnm;
	}

	if ($bar->statusblok == 'CADANGAN') {
		$bar->hanonproduktif = $bar->hathnini;
	}

	$unplanted += $bar->afdeling;
	$totalunplanted += $bar->hanonproduktif;
	$kadaster += $bar->afdeling;
	$totalkadaster += $bar->hathnini + $bar->hanonproduktif;
	$isidata2[$bar->thntnm] += $bar->afdeling;
	$totalrowdata2[$bar->thntnm] += total;
	$totalcolumndata2[$bar->afdeling] += total;
	$total2 += $bar->pokokthnini;
	$pkkProduktif[$bar->thntnm] += $bar->afdeling;
	$totPkkProduktif += $bar->pokokproduksi;
	$totPerthnPkk[$bar->thntnm] += total;
	$totAfdPkkProduktif[$bar->afdeling] += total;
	$headerdata[$bar->afdeling] = $bar->afdeling;
	$rowdata[$bar->thntnm] = $bar->thntnm;
}

0 < count($headerdata) ? sort($headerdata) : false;
0 < count($rowdata) ? sort($rowdata) : false;
0 < count($rowdata0) ? sort($rowdata0) : false;
0 < count($rowdata1) ? sort($rowdata1) : false;
$jumlahafdeling = 0;

if (!empty($headerdata)) {
	foreach ($headerdata as $baris1) {
		$jumlahafdeling += 1;
	}
}

$jumlahrow = 0;

if (!empty($rowdata)) {
	foreach ($rowdata as $baris2) {
		$jumlahrow += 1;
	}
}

$jumlahafdeling = $jumlahafdeling * 2;
echo '<table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '            <td colspan=' . $jumlahafdeling . ' align=center>Data per Afdeling</td>';
echo '<td rowspan=2 align=center  colspan=2>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        </tr>';

if (!empty($headerdata)) {
	foreach ($headerdata as $baris) {
		echo '<td align=center  colspan=2>' . $baris . '</td>';
	}
}

echo '</thead>' . "\r\n" . '    <tbody>';
$statTm = 'TM';
$countdown = $jumlahrow;

if (!empty($rowdata0)) {
	foreach ($rowdata0 as $tt) {
		if ($tt != 0) {
			echo '<tr style="cursor:pointer;" title="Click untuk melihat detail" onclick="detail(\'A\',' . $tahun . ',\'' . $kebun . '\',' . $tt . ',\'' . $statTm . '\',event)"; class=rowcontent>';

			if ($countdown == $jumlahrow) {
				echo '<td align=left>A. Luas Areal TM(ha)</td>';
			}
			else {
				echo '<td align=center>&nbsp;</td>';
			}

			echo '<td align=center>' . $tt . '</td>';

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					echo '<td align=right colspan=2>' . number_format($isidata[$tt . $statTm][$af], 2) . '</td>';
					$totalplanted_tm += $af;
				}
			}

			echo '<td align=right  colspan=2>' . number_format($totalrowdata[$tt . $statTm][total], 2) . '</td>';
			echo '</tr>';
			$countdown -= 1;
		}
	}
}

if (!empty($rowdata0)) {
	echo '<tr class=rowcontent>';
	echo '<td align=center>&nbsp;</td>';
	echo '<td align=center>Subtotal TM</td>';

	if (!empty($headerdata)) {
		foreach ($headerdata as $af) {
			echo '<td align=right colspan=2>' . number_format($totalcolumndata[$af . $statTm][total], 2) . '</td>';
		}
	}

	echo '<td align=right  colspan=2>' . number_format($total[$statTm], 2) . '</td>';
	echo '</tr>';
}

$statTbm = 'TBM';
$countdown = $jumlahrow;

if (!empty($rowdata1)) {
	foreach ($rowdata1 as $tt) {
		if ($tt != 0) {
			echo '<tr style="cursor:pointer;" title="Click untuk melihat detail" onclick="detail(\'A\',' . $tahun . ',\'' . $kebun . '\',' . $tt . ',\'' . $statTbm . '\',event)"; class=rowcontent>';

			if ($countdown == $jumlahrow) {
				echo '<td align=left>B. Luas Areal TBM(ha)</td>';
			}
			else {
				echo '<td align=center>&nbsp;</td>';
			}

			echo '<td align=center>' . $tt . '</td>';

			foreach ($headerdata as $af) {
				echo '<td align=right colspan=2>' . number_format($isidata1[$tt . $statTbm][$af], 2) . '</td>';
				$totalplanted_tbm += $af;
			}

			echo '<td align=right  colspan=2>' . number_format($totalrowdata1[$tt . $statTbm][total], 2) . '</td>';
			echo '</tr>';
			$countdown -= 1;
		}
	}
}

if (!empty($rowdata1)) {
	echo '<tr class=rowcontent>';
	echo '<td align=center>&nbsp;</td>';
	echo '<td align=center>Subtotal TBM</td>';

	foreach ($headerdata as $af) {
		echo '<td align=right colspan=2>' . number_format($totalcolumndata1[$af . $statTbm][total], 2) . '</td>';
	}

	echo '<td align=right  colspan=2>' . number_format($total1[$statTbm], 2) . '</td>';
	echo '</tr>';
}

echo '<tr class=rowcontent>';
echo '<td align=center>&nbsp;</td>';
echo '<td align=center>TOTAL PLANTED</td>';

foreach ($headerdata as $af) {
	$tp = $totalplanted_tbm[$af] + $totalplanted_tm[$af];
	echo '<td align=right colspan=2>' . number_format($tp, 2) . '</td>';
}

$ttp = $total1[$statTbm] + $total[$statTm];
echo '<td align=right  colspan=2>' . number_format($ttp, 2) . '</td>';
echo '</tr>';
echo '<tr class=rowcontent><td></td><td align=center>Unplanted</td>';

if (!empty($unplanted)) {
	foreach ($unplanted as $dat) {
		echo '<td align=right colspan=2>' . number_format($dat, 2) . '</td>';
	}

	echo '<td align=right  colspan=2>' . number_format($totalunplanted, 2) . '</td></tr>';
}

echo '<tr class=rowcontent>';
echo '<td align=center>&nbsp;</td>';
echo '<td align=center>GRAND TOTAL</td>';

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$gt = $totalplanted_tbm[$af] + $totalplanted_tm[$af] + $unplanted[$af];
		echo '<td align=right colspan=2>' . number_format($gt, 2) . '</td>';
	}

	$tgt = $ttp + $totalunplanted;
	echo '<td align=right  colspan=2>' . number_format($tgt, 2) . '</td>';
	echo '</tr>';
}

echo '<tr  class=rowcontent>';
echo '<td align=left>C. Populasi Tanaman (pkk)</td><td align=left>&nbsp;</td>';

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		echo '<td align=center>Jumlah Pokok</td><td align=center>Pokok Produktif</td>';
	}
}

echo '<td align=center>Jumlah Pokok</td><td align=center>Pokok Produktif</td></tr>';
$countdown = $jumlahrow;

if (!empty($rowdata)) {
	foreach ($rowdata as $tt) {
		if ($tt != 0) {
			echo '<tr style="cursor:pointer;" title="Click untuk melihat detail" onclick="detail(\'B\',' . $tahun . ',\'' . $kebun . '\',' . $tt . ',event)"; class=rowcontent>';

			if ($countdown == $jumlahrow) {
				echo '<td align=left>&nbsp;</td>';
			}
			else {
				echo '<td align=center>&nbsp;</td>';
			}

			echo '<td align=center>' . $tt . '</td>';

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					echo '<td align=right>' . number_format($isidata2[$tt][$af]) . '</td>';
					echo '<td align=right>' . number_format($pkkProduktif[$tt][$af]) . '</td>';
				}
			}

			echo '<td align=right>' . number_format($totalrowdata2[$tt][total]) . '</td>';
			echo '<td align=right>' . number_format($totPerthnPkk[$tt][total]) . '</td>';
			echo '</tr>';
			$countdown -= 1;
		}
	}
}

if (!empty($rowdata)) {
	echo '<tr class=rowcontent>';
	echo '<td align=center>&nbsp;</td>';
	echo '<td align=center>Total Pokok</td>';

	if (!empty($headerdata)) {
		foreach ($headerdata as $af) {
			echo '<td align=right>' . number_format($totalcolumndata2[$af][total]) . '</td>';
			echo '<td align=right>' . number_format($totAfdPkkProduktif[$af][total]) . '</td>';
		}
	}

	echo '<td align=right>' . number_format($total2) . '</td>';
	echo '<td align=right>' . number_format($totPkkProduktif) . '</td>';
	echo '</tr>';
}
else {
	echo '<tr class=rowcontent><td colspan=4>Data tidak tersedia.</td></tr>';
}

echo '    </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\t\t" . ' ' . "\r\n" . '   </table>';

?>
