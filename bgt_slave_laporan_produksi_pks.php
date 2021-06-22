<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$tahun = $_POST['tahun'];
$pabrik = $_POST['pabrik'];

if ($tahun == '') {
	echo 'WARNING: silakan mengisi tahun.';
	exit();
}

if ($pabrik == '') {
	echo 'WARNING: silakan mengisi pabrik.';
	exit();
}

$isidata = array();
$str = 'select * from ' . $dbname . '.bgt_produksi_pks_vw where tahunbudget = \'' . $tahun . '\' and millcode = \'' . $pabrik . '\' order by kodeunit';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$isidata[$bar->kodeunit][tbstotal] = $bar->kgolah;
	$isidata[$bar->kodeunit][tbs01] = $bar->olah01;
	$isidata[$bar->kodeunit][tbs02] = $bar->olah02;
	$isidata[$bar->kodeunit][tbs03] = $bar->olah03;
	$isidata[$bar->kodeunit][tbs04] = $bar->olah04;
	$isidata[$bar->kodeunit][tbs05] = $bar->olah05;
	$isidata[$bar->kodeunit][tbs06] = $bar->olah06;
	$isidata[$bar->kodeunit][tbs07] = $bar->olah07;
	$isidata[$bar->kodeunit][tbs08] = $bar->olah08;
	$isidata[$bar->kodeunit][tbs09] = $bar->olah09;
	$isidata[$bar->kodeunit][tbs10] = $bar->olah10;
	$isidata[$bar->kodeunit][tbs11] = $bar->olah11;
	$isidata[$bar->kodeunit][tbs12] = $bar->olah12;
	$isidata[$bar->kodeunit][cpototal] = $bar->kgcpo;
	$isidata[$bar->kodeunit][cpo01] = $bar->kgcpo01;
	$isidata[$bar->kodeunit][cpo02] = $bar->kgcpo02;
	$isidata[$bar->kodeunit][cpo03] = $bar->kgcpo03;
	$isidata[$bar->kodeunit][cpo04] = $bar->kgcpo04;
	$isidata[$bar->kodeunit][cpo05] = $bar->kgcpo05;
	$isidata[$bar->kodeunit][cpo06] = $bar->kgcpo06;
	$isidata[$bar->kodeunit][cpo07] = $bar->kgcpo07;
	$isidata[$bar->kodeunit][cpo08] = $bar->kgcpo08;
	$isidata[$bar->kodeunit][cpo09] = $bar->kgcpo09;
	$isidata[$bar->kodeunit][cpo10] = $bar->kgcpo10;
	$isidata[$bar->kodeunit][cpo11] = $bar->kgcpo11;
	$isidata[$bar->kodeunit][cpo12] = $bar->kgcpo12;
	$isidata[$bar->kodeunit][kertotal] = $bar->kgkernel;
	$isidata[$bar->kodeunit][ker01] = $bar->kgker01;
	$isidata[$bar->kodeunit][ker02] = $bar->kgker02;
	$isidata[$bar->kodeunit][ker03] = $bar->kgker03;
	$isidata[$bar->kodeunit][ker04] = $bar->kgker04;
	$isidata[$bar->kodeunit][ker05] = $bar->kgker05;
	$isidata[$bar->kodeunit][ker06] = $bar->kgker06;
	$isidata[$bar->kodeunit][ker07] = $bar->kgker07;
	$isidata[$bar->kodeunit][ker08] = $bar->kgker08;
	$isidata[$bar->kodeunit][ker09] = $bar->kgker09;
	$isidata[$bar->kodeunit][ker10] = $bar->kgker10;
	$isidata[$bar->kodeunit][ker11] = $bar->kgker11;
	$isidata[$bar->kodeunit][ker12] = $bar->kgker12;
}

echo '<table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td rowspan=2 align=center>No.</td>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['asaltbs'] . '</td>' . "\r\n" . '             <td rowspan=1 colspan=2 align=center>OER(%)</td>   ' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '            <td colspan=12 align=center>Distribusi Produksi</td>';
echo '<td rowspan=2 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        </tr>';
echo '<tr>' . "\r\n" . '           <td align=center>CPO</td>' . "\r\n" . '           <td align=center>KER</td>           ' . "\r\n" . '           <td align=center>Jan</td>' . "\r\n" . '           <td align=center>Feb</td>' . "\r\n" . '           <td align=center>Mar</td>' . "\r\n" . '           <td align=center>Apr</td>' . "\r\n" . '           <td align=center>May</td>' . "\r\n" . '           <td align=center>Jun</td>' . "\r\n" . '           <td align=center>Jul</td>' . "\r\n" . '           <td align=center>Aug</td>' . "\r\n" . '           <td align=center>Sep</td>' . "\r\n" . '           <td align=center>Oct</td>' . "\r\n" . '           <td align=center>Nov</td>' . "\r\n" . '           <td align=center>Dec</td>' . "\r\n" . '       </tr>';
echo '</thead>' . "\r\n" . '    <tbody>';
$str = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk<>\'' . $_SESSION['org']['kodeorganisasi'] . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$afiliasi[$bar->kodeorganisasi] = $bar->kodeorganisasi;
}

$str = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$internal[$bar->kodeorganisasi] = $bar->kodeorganisasi;
}

$str = 'select distinct supplierid from ' . $dbname . '.log_5supplier' . "\r\n" . '                  order by supplierid';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$eksternal[$bar->supplierid] = $bar->supplierid;
}

$no = 1;

if (!empty($internal)) {
	$olahdata[internal] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[internal] += $ii;
		++$i;
	}

	$olahdata[internal] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[internal] += $ii;
	++$i;
}

if (!empty($afiliasi)) {
	$olahdata[afiliasi] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[afiliasi] += $ii;
		++$i;
	}

	$olahdata[afiliasi] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[afiliasi] += $ii;
	++$i;
}

if (!empty($eksternal)) {
	$olahdata[eksternal] += tbstotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += cpototal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += kertotal;
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$olahdata[eksternal] += $ii;
		++$i;
	}

	$olahdata[eksternal] += paltotal;
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
		$jj = 'cpo0' . $i;
		$kk = 'ker0' . $i;
	}
	else {
		$ii = 'pal' . $i;
		$jj = 'cpo' . $i;
		$kk = 'ker' . $i;
	}

	$olahdata[eksternal] += $ii;
	++$i;
}

$olahdata[all][tbstotal] = $olahdata[internal][tbstotal] + $olahdata[afiliasi][tbstotal] + $olahdata[eksternal][tbstotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'tbs0' . $i;
	}
	else {
		$ii = 'tbs' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][cpototal] = $olahdata[internal][cpototal] + $olahdata[afiliasi][cpototal] + $olahdata[eksternal][cpototal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'cpo0' . $i;
	}
	else {
		$ii = 'cpo' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][kertotal] = $olahdata[internal][kertotal] + $olahdata[afiliasi][kertotal] + $olahdata[eksternal][kertotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'ker0' . $i;
	}
	else {
		$ii = 'ker' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

$olahdata[all][paltotal] = $olahdata[internal][paltotal] + $olahdata[afiliasi][paltotal] + $olahdata[eksternal][paltotal];
$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = 'pal0' . $i;
	}
	else {
		$ii = 'pal' . $i;
	}

	$olahdata[all] += $ii;
	++$i;
}

if (!empty($olahdata)) {
	echo '<tr class=rowcontent>';
	echo '<td rowspan=4 valign=middle align=right>1</td>';
	echo '<td rowspan=4 valign=middle align=left>Internal</td>';
	$RCPO = number_format(($olahdata[internal][cpototal] / $olahdata[internal][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[internal][kertotal] / $olahdata[internal][tbstotal]) * 100, 2);
	echo '<td align=left rowspan=4>' . $RCPO . '</td>';
	echo '<td align=left rowspan=4>' . $RKER . '</td>';
	echo '<td align=left>TBS</td>';
	echo '<td align=left>Ton</td>';
	@$tonTbs[internal][tbstotal] = $olahdata[internal][tbstotal] / 1000;
	echo '<td align=right>' . number_format($tonTbs[internal][tbstotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$tbsOl[internal][$ii] = $olahdata[internal][$ii] / 1000;
		echo '<td align=right>' . number_format($tbsOl[internal][$ii], 2) . '</td>';
		$totTbs[internal] += tbstotal;
		++$i;
	}

	@$toNtotTbs[internal][tbstotal] = $totTbs[internal][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNtotTbs[internal][tbstotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>CPO</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[internal][cpototal] = $olahdata[internal][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[internal][cpototal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[internal][$ii], 2) . '</td>';
		$tot[internal] += cpototal;
		++$i;
	}

	@$toNtot[internal][cpototal] = $tot[internal][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNtot[internal][cpototal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Kernel</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[internal][kertotal] = $olahdata[internal][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[internal][kertotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[internal][$ii], 2) . '</td>';
		$totAll[internal] += kertotal;
		++$i;
	}

	@$toNtotAll[internal][kertotal] = $totAll[internal][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNtotAll[internal][kertotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Produk</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[internal][paltotal] = $olahdata[internal][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[internal][paltotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[internal][$ii] = $olahdata[internal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[internal][$ii], 2) . '</td>';
		$jmlhSma[internal] += paltotal;
		++$i;
	}

	@$toNjmlhSma[internal][paltotal] = $jmlhSma[internal][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[internal][paltotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td rowspan=4 valign=middle align=right>2</td>';
	echo '<td rowspan=4 valign=middle align=left>Afiliasi</td>';
	$RCPO = number_format(($olahdata[afiliasi][cpototal] / $olahdata[afiliasi][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[afiliasi][kertotal] / $olahdata[afiliasi][tbstotal]) * 100, 2);
	echo '<td align=left rowspan=4>' . $RCPO . '</td>';
	echo '<td align=left rowspan=4>' . $RKER . '</td>';
	echo '<td align=left>TBS</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[afiliasi][tbstotal] = $olahdata[afiliasi][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[afiliasi][tbstotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[afiliasi][$ii], 2) . '</td>';
		$jmlhSma[afiliasi] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][tbstotal] = $jmlhSma[afiliasi][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[afiliasi][tbstotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>CPO</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[afiliasi][cpototal] = $olahdata[afiliasi][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[afiliasi][cpototal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[afiliasi][$ii], 2) . '</td>';
		$jmlhSma[afiliasi] += cpototal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][cpototal] = $jmlhSma[afiliasi][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[afiliasi][cpototal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Kernel</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[afiliasi][kertotal] = $olahdata[afiliasi][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[afiliasi][kertotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[afiliasi][$ii], 2) . '</td>';
		$jmlhSma[afiliasi] += kertotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][kertotal] = $jmlhSma[afiliasi][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[afiliasi][kertotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Produk</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[afiliasi][paltotal] = $olahdata[afiliasi][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[afiliasi][paltotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[afiliasi][$ii] = $olahdata[afiliasi][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[afiliasi][$ii], 2) . '</td>';
		$jmlhSma[afiliasi] += paltotal;
		++$i;
	}

	@$toNjmlhSma[afiliasi][paltotal] = $jmlhSma[afiliasi][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[afiliasi][paltotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td rowspan=4 valign=middle align=right>3</td>';
	echo '<td rowspan=4 valign=middle align=left>Eksternal</td>';
	$RCPO = number_format(($olahdata[eksternal][cpototal] / $olahdata[eksternal][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[eksternal][kertotal] / $olahdata[eksternal][tbstotal]) * 100, 2);
	echo '<td align=left rowspan=4>' . $RCPO . '</td>';
	echo '<td align=left rowspan=4>' . $RKER . '</td>';
	echo '<td align=left>TBS</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[eksternal][tbstotal] = $olahdata[eksternal][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[eksternal][tbstotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[eksternal][$ii], 2) . '</td>';
		$jmlhSma[eksternal] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][tbstotal] = $jmlhSma[eksternal][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[eksternal][tbstotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>CPO</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[eksternal][cpototal] = $olahdata[eksternal][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[eksternal][cpototal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[eksternal][$ii], 2) . '</td>';
		$jmlhSma[eksternal] += cpototal;
		++$i;
	}

	@$toNjmlhSma[eksternal][cpototal] = $jmlhSma[eksternal][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[eksternal][cpototal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Kernel</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[eksternal][kertotal] = $olahdata[eksternal][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[eksternal][kertotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[eksternal][$ii], 2) . '</td>';
		$jmlhSma[eksternal] += kertotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][kertotal] = $jmlhSma[eksternal][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[eksternal][kertotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Produk</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[eksternal][paltotal] = $olahdata[eksternal][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[eksternal][paltotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[eksternal][$ii] = $olahdata[eksternal][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[eksternal][$ii], 2) . '</td>';
		$jmlhSma[eksternal] += paltotal;
		++$i;
	}

	@$toNjmlhSma[eksternal][paltotal] = $jmlhSma[eksternal][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[eksternal][paltotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td rowspan=4 valign=middle align=right>&nbsp;</td>';
	echo '<td rowspan=4 valign=middle align=left>Grand Total</td>';
	$RCPO = number_format(($olahdata[all][cpototal] / $olahdata[all][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[all][kertotal] / $olahdata[all][tbstotal]) * 100, 2);
	echo '<td align=left rowspan=4>' . $RCPO . '</td>';
	echo '<td align=left rowspan=4>' . $RKER . '</td>';
	echo '<td align=left>TBS</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[all][tbstotal] = $olahdata[all][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[all][tbstotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[all][$ii], 2) . '</td>';
		$jmlhSma[all] += tbstotal;
		++$i;
	}

	@$toNjmlhSma[all][tbstotal] = $jmlhSma[all][tbstotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[all][tbstotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>CPO</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[all][cpototal] = $olahdata[all][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[all][cpototal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[all][$ii], 2) . '</td>';
		$jmlhSma[all] += cpototal;
		++$i;
	}

	@$toNjmlhSma[all][cpototal] = $jmlhSma[all][cpototal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[all][cpototal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Kernel</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[all][kertotal] = $olahdata[all][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[all][kertotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[all][$ii], 2) . '</td>';
		$jmlhSma[all] += kertotal;
		++$i;
	}

	@$toNjmlhSma[all][kertotal] = $jmlhSma[all][kertotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[all][kertotal], 2) . '</td>';
	echo '</tr>';
	echo '<tr class=rowcontent>';
	echo '<td align=left>Produk</td>';
	echo '<td align=left>Ton</td>';
	@$toNolahdata[all][paltotal] = $olahdata[all][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNolahdata[all][paltotal], 2) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		@$toNolahdata[all][$ii] = $olahdata[all][$ii] / 1000;
		echo '<td align=right>' . number_format($toNolahdata[all][$ii], 2) . '</td>';
		$jmlhSma[all] += paltotal;
		++$i;
	}

	@$toNjmlhSma[all][paltotal] = $jmlhSma[all][paltotal] / 1000;
	echo '<td align=right>' . number_format($toNjmlhSma[all][paltotal], 2) . '</td>';
	echo '</tr>';
}

if (empty($olahdata)) {
	echo '<tr class=rowcontent><td colspan=18>Data tidak tersedia.</td></tr>';
}

echo '    </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\t\t" . ' ' . "\r\n" . '   </table>';

?>
