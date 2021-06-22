<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahun = $_GET['tahun'];
$pabrik = $_GET['pabrik'];

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

$stream = '';
$warnalatar = '#77ff77';
$stream .= '<table class=sortable cellspacing=1 border=1 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>No.</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['asaltbs'] . '</td>' . "\r\n" . '             <td bgcolor="' . $warnalatar . '" colspan=2 align=center>ORE(%)</td>   ' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" colspan=12 align=center>Distribusi Produksi</td>';
$stream .= '<td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        </tr>';
$stream .= '<tr>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>CPO</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" align=center>KER</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jan</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Feb</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Mar</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Apr</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>May</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jun</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jul</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Aug</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Sep</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Oct</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Nov</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Dec</td>' . "\r\n" . '       </tr>';
$stream .= '</thead>' . "\r\n" . '    <tbody>';
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
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td rowspan=4 valign=middle align=right>1</td>';
	$stream .= '<td rowspan=4 valign=middle align=left>Internal</td>';
	$RCPO = number_format(($olahdata[internal][cpototal] / $olahdata[internal][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[internal][kertotal] / $olahdata[internal][tbstotal]) * 100, 2);
	$stream .= '<td align=left rowspan=4>' . $RCPO . '</td>';
	$stream .= '<td align=left rowspan=4>' . $RKER . '</td>';
	$stream .= '<td align=left>TBS</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[internal][tbstotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[internal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[internal][tbstotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>CPO</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[internal][cpototal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[internal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[internal][cpototal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Kernel</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[internal][kertotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[internal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[internal][kertotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Produk</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[internal][paltotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[internal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[internal][paltotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td rowspan=4 valign=middle align=right>2</td>';
	$stream .= '<td rowspan=4 valign=middle align=left>Afiliasi</td>';
	$RCPO = number_format(($olahdata[afiliasi][cpototal] / $olahdata[afiliasi][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[afiliasi][kertotal] / $olahdata[afiliasi][tbstotal]) * 100, 2);
	$stream .= '<td align=left rowspan=4>' . $RCPO . '</td>';
	$stream .= '<td align=left rowspan=4>' . $RKER . '</td>';
	$stream .= '<td align=left>TBS</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][tbstotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[afiliasi][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][tbstotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>CPO</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][cpototal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[afiliasi][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][cpototal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Kernel</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][kertotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[afiliasi][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][kertotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Produk</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][paltotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[afiliasi][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[afiliasi][paltotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td rowspan=4 valign=middle align=right>3</td>';
	$stream .= '<td rowspan=4 valign=middle align=left>Eksternal</td>';
	$RCPO = number_format(($olahdata[eksternal][cpototal] / $olahdata[eksternal][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[eksternal][kertotal] / $olahdata[eksternal][tbstotal]) * 100, 2);
	$stream .= '<td align=left rowspan=4>' . $RCPO . '</td>';
	$stream .= '<td align=left rowspan=4>' . $RKER . '</td>';
	$stream .= '<td align=left>TBS</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[eksternal][tbstotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[eksternal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[eksternal][tbstotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>CPO</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[eksternal][cpototal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[eksternal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[eksternal][cpototal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Kernel</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[eksternal][kertotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[eksternal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[eksternal][kertotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Produk</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[eksternal][paltotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[eksternal][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[eksternal][paltotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td rowspan=4 valign=middle align=right>&nbsp;</td>';
	$stream .= '<td rowspan=4 valign=middle align=left>Grand Total</td>';
	$RCPO = number_format(($olahdata[all][cpototal] / $olahdata[all][tbstotal]) * 100, 2);
	$RKER = number_format(($olahdata[all][kertotal] / $olahdata[all][tbstotal]) * 100, 2);
	$stream .= '<td align=left rowspan=4>' . $RCPO . '</td>';
	$stream .= '<td align=left rowspan=4>' . $RKER . '</td>';
	$stream .= '<td align=left>TBS</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][tbstotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'tbs0' . $i;
		}
		else {
			$ii = 'tbs' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][tbstotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>CPO</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][cpototal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'cpo0' . $i;
		}
		else {
			$ii = 'cpo' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][cpototal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Kernel</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][kertotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'ker0' . $i;
		}
		else {
			$ii = 'ker' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][kertotal]) . '</td>';
	$stream .= '</tr>';
	$stream .= '<tr class=rowcontent>';
	$stream .= '<td align=left>Produk</td>';
	$stream .= '<td align=left>Kg</td>';
	$stream .= '<td align=right>' . number_format($olahdata[all][paltotal]) . '</td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'pal0' . $i;
		}
		else {
			$ii = 'pal' . $i;
		}

		$stream .= '<td align=right>' . number_format($olahdata[all][$ii]) . '</td>';
		++$i;
	}

	$stream .= '<td align=right>' . number_format($olahdata[all][paltotal]) . '</td>';
	$stream .= '</tr>';
}

if (empty($olahdata)) {
	$stream .= '<tr class=rowcontent><td colspan=18>Data tidak tersedia.</td></tr>';
}

$stream .= '    </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\t\t" . ' ' . "\r\n" . '   </table>';
$stream .= 'Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'bgt_produksi_' . $tahun . ' ' . $pabrik;

if (0 < strlen($stream)) {
	if ($handle = opendir('tempExcel')) {
		while (false !== $file = readdir($handle)) {
			if (($file != '.') && ($file != '..')) {
				@unlink('tempExcel/' . $file);
			}
		}

		closedir($handle);
	}

	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

	if (!fwrite($handle, $stream)) {
		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
		exit();
	}
	else {
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
	}

	closedir($handle);
}

?>
