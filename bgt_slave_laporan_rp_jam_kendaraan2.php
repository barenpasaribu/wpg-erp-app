<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['kdUnit2'] == '' ? $kodeOrg = $_GET['kdUnit2'] : $kodeOrg = $_POST['kdUnit2'];
$_POST['thnBudget2'] == '' ? $thnBudget = $_GET['thnBudget2'] : $thnBudget = $_POST['thnBudget2'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmbrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where karyawanid=' . $_SESSION['standard']['userid'] . '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namakar[$bar->karyawanid] = $bar->namakaryawan;
}

$daftarmobil = '(';
$where = ' kodetraksi=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'';
$sKodeOrg = 'select * from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi where  ' . $where . ' order by tahunbudget asc';

#exit(mysql_error($conn));
($qKodeOrg = mysql_query($sKodeOrg)) || true;

while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
	$dtKdtraksi[] = $rKode['kodetraksi'];
	$dtKdvhc[] = $rKode['kodevhc'];
	$daftarmobil .= '\'' . $rKode['kodevhc'] . '\',';
	$dtRpSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['rpsetahun'];
	$dtJamSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['jamsetahun'];
	$dtRpJam[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['rpperjam'];
	$dtAlokasi[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']] = $rKode['teralokasi'];
}

$daftarmobil = substr($daftarmobil, 0, -1);
$daftarmobil .= ')';

if ($daftarmobil == ')') {
	$daftarmobil = '(\'\')';
}

$sKodeOrg = 'select substr(kodeorg,1,4) as kodeorg, kodevhc, jumlah from ' . $dbname . '.bgt_budget where kodevhc in ' . $daftarmobil . ' and tahunbudget = \'' . $thnBudget . '\' AND tipebudget != \'TRK\'';

#exit(mysql_error($conn));
($qKodeOrg = mysql_query($sKodeOrg)) || true;

while ($rKode = mysql_fetch_assoc($qKodeOrg)) {
	$daftaruser[$rKode['kodeorg']] = $rKode['kodeorg'];
	$penggunaan[$rKode['kodevhc']] += $rKode['kodeorg'];
}

$cek = count($dtKdtraksi);
if (($kodeOrg == '') || ($thnBudget == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

if ($cek == 0) {
	exit('Error: Data Kosong');
}

if ($proses == 'preview') {
	$tab = '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
}
else {
	$tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
}

$tab .= '<tr class=rowheader>';
$tab .= '<td align=center>No.</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['kodetraksi'] . '</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['kodevhc'] . '</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['rpperthn'] . '</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['kmperthn'] . '</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['jamperthn'] . '</td>';
$tab .= '<td align=center>' . $_SESSION['lang']['alokasijam'] . '</td>';

if (!empty($daftaruser)) {
	foreach ($daftaruser as $user) {
		$tab .= '<td align=center>' . $user . '</td>';
	}
}

$tab .= '</tr></thead><tbody>';
$terAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] = $dtAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]] * $dtRpJam[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]];

foreach ($dtKdvhc as $lisTraksi) {
	$no += 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=center>' . $no . '</td>';
	$tab .= '<td align=center>' . $kodeOrg . '</td>';
	$tab .= '<td align=center>' . $lisTraksi . '</td>';
	$tab .= '<td align=right>' . number_format($dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtRpJam[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi], 2) . '</td>';

	if (!empty($daftaruser)) {
		foreach ($daftaruser as $user) {
			$tab .= '<td align=right>' . number_format($penggunaan[$lisTraksi][$user], 2) . '</td>';
			$total += $user;
		}
	}

	$tab .= '</tr>';
	$totJam += $dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi];
	$totRup += $dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi];
	$totKmThn += $dtRpJam[$thnBudget][$kodeOrg][$lisTraksi];
	$totAlokasiJam += $dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
}

$tab .= '</tbody><thead><tr class=rowheader>';
$tab .= '<td align=center  colspan=3 align=center>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td align=right>' . number_format($totRup, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totKmThn, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totJam, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totAlokasiJam, 2) . '</td>';

if (!empty($daftaruser)) {
	foreach ($daftaruser as $user) {
		$tab .= '<td align=right>' . number_format($total[$user], 2) . '</td>';
	}
}

$tab .= '</tr>';
$tab .= '</thead></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tglSkrg = date('Ymd');
	$nop_ = 'Laporan_Alokasi_Exel_' . $tglSkrg;

	if (0 < strlen($tab)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab)) {
			echo '<script language=javascript1.2>' . "\n\t\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\n\t\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\n\t\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\n\t\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;
}

?>
