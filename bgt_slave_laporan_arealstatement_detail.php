<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo '<link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n";
$type = $_GET['type'];
$what = $_GET['what'];
$tahun = $_GET['tahun'];
$kebun = $_GET['kebun'];
$statBlok = $_GET['statBlok'];
$tt = $_GET['tt'];
$str = 'select * from ' . $dbname . '.setup_topografi';
$res = mysql_query($str);
$opttahun = '';

while ($bar = mysql_fetch_object($res)) {
	$topo[$bar->topografi] = $bar->keterangan;
}

if ($statBlok == 'TBM') {
	$statBlok = '\'TBM\',\'TB\'';
}
else {
	$statBlok = '\'TM\'';
}

$str = 'select * from ' . $dbname . '.bgt_blok' . "\r\n" . '             where tahunbudget =\'' . $tahun . '\' and statusblok in (' . $statBlok . ')' . "\r\n" . '             and kodeblok like \'' . $kebun . '%\' and thntnm =\'' . $tt . '\'';

if ($type == 'pdf') {
	echo 'PDF';
}

echo '<fieldset><legend>Print Excel</legend>' . "\r\n" . '     <img onclick="parent.detailKeExcel(event,\'bgt_slave_laporan_arealstatement_detail.php?type=excel&what=' . $what . '&tahun=' . $tahun . '&kebun=' . $kebun . '&tt=' . $tt . '\')" src=images/excel.jpg class=resicon title=\'MS.Excel\'>' . "\r\n" . '     </fieldset>';

if ($what == 'A') {
	$stream = $_SESSION['lang']['detail'] . ' ' . $_SESSION['lang']['luas'] . ' ' . $kebun . '<br>' . $_SESSION['lang']['tahuntanam'] . ' ' . $tt;
}

if ($what == 'B') {
	$stream = $_SESSION['lang']['detail'] . ' ' . $_SESSION['lang']['populasi'] . ' ' . $kebun . '<br>' . $_SESSION['lang']['tahuntanam'] . ' ' . $tt;
}

if ($_GET['type'] == 'excel') {
	$stream .= '<table class=sortable border=1 cellspacing=1>';
}
else {
	$stream .= '<table class=sortable border=0 cellspacing=1>';
}

$stream .= '<thead>' . "\r\n" . '        <tr class=rowcontent>' . "\r\n" . '          <td>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['kodeblok'] . '</td>';

if ($what == 'A') {
	$stream .= '<td>' . $_SESSION['lang']['luas'] . ' ' . $_SESSION['lang']['tahun'] . ' ' . $_SESSION['lang']['lalu'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['Mutasi1'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['luas'] . ' ' . $_SESSION['lang']['tahun'] . ' ' . $_SESSION['lang']['ini'] . '</td>';
}

if ($what == 'B') {
	$stream .= '<td>' . $_SESSION['lang']['pokok'] . ' ' . $_SESSION['lang']['tahun'] . ' ' . $_SESSION['lang']['lalu'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['Mutasi1'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['pokok'] . ' ' . $_SESSION['lang']['tahun'] . ' ' . $_SESSION['lang']['ini'] . '</td>';
}

$stream .= '<td>' . $_SESSION['lang']['status'] . ' ' . $_SESSION['lang']['blok'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['topografi'] . '</td>' . "\r\n" . '          <td>' . $_SESSION['lang']['lama'] . '/' . $_SESSION['lang']['baru'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '      </thead>' . "\r\n" . '      <tbody>';
$res = mysql_query($str);
$no = 0;
$totallalu = 0;
$totalmutasi = 0;
$totalini = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;

	if ($what == 'A') {
		$totallalu += $bar->hathnlalu;
		$totalmutasi += $bar->hamutasi;
		$totalini += $bar->hathnini;
	}

	if ($what == 'B') {
		$totallalu += $bar->pokokthnlalu;
		$totalmutasi += $bar->pokokmutasi;
		$totalini += $bar->pokokthnini;
	}

	$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $no . '</td>' . "\r\n" . '           <td>' . $bar->kodeblok . '</td>';

	if ($what == 'A') {
		$stream .= '<td align=right>' . number_format($bar->hathnlalu, 2) . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->hamutasi, 2) . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->hathnini, 2) . '</td>';
	}

	if ($what == 'B') {
		$stream .= '<td align=right>' . number_format($bar->pokokthnlalu) . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->pokokmutasi) . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->pokokthnini) . '</td>';
	}

	$stream .= '<td>' . $bar->statusblok . '</td>' . "\r\n" . '           <td>' . $topo[$bar->topografi] . '</td>' . "\r\n" . '           <td>' . $bar->sumber . '</td>' . "\r\n" . '         </tr>';
	$tdebet += $debet;
	$tkredit += $kredit;
}

$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td colspan=2>' . $_SESSION['lang']['total'] . '</td>';

if ($what == 'A') {
	$stream .= '<td align=right>' . number_format($totallalu, 2) . '</td>' . "\r\n" . '           <td align=right>' . number_format($totalmutasi, 2) . '</td>' . "\r\n" . '           <td align=right>' . number_format($totalini, 2) . '</td>';
}

if ($what == 'B') {
	$stream .= '<td align=right>' . number_format($totallalu) . '</td>' . "\r\n" . '           <td align=right>' . number_format($totalmutasi) . '</td>' . "\r\n" . '           <td align=right>' . number_format($totalini) . '</td>';
}

$stream .= '<td colspan=3>&nbsp;</td>' . "\r\n" . '         </tr>';
$stream .= '</tbody><tfoot></tfoot></table>';

if ($_GET['type'] == 'excel') {
	$stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$nop_ = 'Detail_arealstatement_' . $_GET['what'] . '_' . $_GET['kebun'] . '_' . $_GET['tt'];

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
			echo '<script language=javascript1.2>' . "\r\n" . '                parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                </script>';
		}

		closedir($handle);
	}
}
else {
	echo $stream;
}

?>
