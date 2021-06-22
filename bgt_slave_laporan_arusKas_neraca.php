<?php


function artinya($nomer)
{
	if ($nomer == '1') {
		$tabz = 'Aktiva';
	}

	if ($nomer == '11') {
		$tabz = 'Aktiva Lancar';
	}

	if ($nomer == '12') {
		$tabz = 'Aktiva Tidak Lancar';
	}

	if ($nomer == '2') {
		$tabz = 'Kewajiban';
	}

	if ($nomer == '21') {
		$tabz = 'Kewajiban Lancar';
	}

	if ($nomer == '22') {
		$tabz = 'Kewajiban Tidak Lancar';
	}

	if ($nomer == '3') {
		$tabz = 'Ekuitas';
	}

	return $tabz;
}

function printline($depannya)
{
	$tabz = '';
	$tabz .= '<tr class=rowcontent>';
	$tabz .= '<td align=left ' . $bg . ' >' . $depannya . '</td>';
	$tabz .= '<td align=left ' . $bg . ' >' . strtoupper(artinya($depannya)) . '</td>';
	$i = 1;

	while ($i <= 14) {
		$tabz .= '<td align=left ' . $bg . ' ></td>';
		++$i;
	}

	$tabz .= '</tr>';
	return $tabz;
}

function printline2($depannya)
{
	global $lastsum;
	global $isidata;
	global $optAkun;
	$tabz = '';
	$berapa = strlen($depannya);

	if (!empty($isidata)) {
		foreach ($isidata as $isi) {
			$dimana = substr($isi['noakun'], 0, $berapa);

			if ($dimana == $depannya) {
				$tabz .= '<tr class=rowcontent>';
				$tabz .= '<td align=left ' . $bg . ' >' . $isi['noakun'] . '</td>';
				$tabz .= '<td align=left ' . $bg . ' >' . $optAkun[$isi['noakun']] . '</td>';
				$tabz .= '<td align=left ' . $bg . ' ></td>';
				$i = 1;

				while ($i <= 12) {
					if (strlen($i) == 1) {
						$ii = '0' . $i;
					}
					else {
						$ii = $i;
					}

					$tabz .= '<td align=right ' . $bg . ' >' . number_format($isi[$ii]) . '</td>';
					$total += $isi['noakun'];
					++$i;
				}

				$tabz .= '<td align=right ' . $bg . ' >' . number_format($total[$isi['noakun']]) . '</td>';
				$tabz .= '</tr>';
			}
		}
	}

	return $tabz;
}

function printline3($depannya)
{
	global $isidata;
	global $lastsum;
	$tabz = '';
	$jumlah = array();
	$berapa = strlen($depannya);

	if (!empty($isidata)) {
		$dimana = substr($isi['noakun'], 0, $berapa);
		$jumlah += 0;
		$i = 1;

		if (strlen($i) == 1) {
			$ii = '0' . $i;
		}
		else {
			$ii = $i;
		}

		$jumlah += $ii;
		++$i;
	}

	$tabz .= '<tr class=rowcontent>';
	$tabz .= '<td align=left ' . $bg . ' ></td>';
	$tabz .= '<td align=left ' . $bg . ' >Jumlah ' . artinya($depannya) . '</td>';
	$tabz .= '<td align=left ' . $bg . ' ></td>';
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = '0' . $i;
		}
		else {
			$ii = $i;
		}

		$tabz .= '<td align=right ' . $bg . ' >' . number_format($jumlah[$ii]) . '</td>';
		$otal += $jumlah[$ii];
		++$i;
	}

	$tabz .= '<td align=right ' . $bg . ' >' . number_format($otal) . '</td>';
	$tabz .= '</tr>';
	return $tabz;
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_POST['proses'];

if ($proses == 'getUnit') {
	$kodePt = $_POST['kodePt'];

	switch ($proses) {
	case 'getUnit':
		if ($kodePt == '') {
			echo '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		}
		else {
			$sOrg = 'select kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi where induk = \'' . $kodePt . '\' order by kodeorganisasi';

			#exit(mysql_error());
			($qOrg = mysql_query($sOrg)) || true;
			echo '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

			while ($rData = mysql_fetch_assoc($qOrg)) {
				echo '<option value=' . $rData['kodeorganisasi'] . '>' . $rData['namaorganisasi'] . '</option>';
			}
		}

		break;
	}
}

$proses = $_GET['proses'];
$_POST['kdPt1'] == '' ? $kodePt = $_GET['kdPt1'] : $kodePt = $_POST['kdPt1'];
$_POST['kdUnit1'] == '' ? $kodeOrg = $_GET['kdUnit1'] : $kodeOrg = $_POST['kdUnit1'];
$_POST['thnBudget1'] == '' ? $thnBudget = $_GET['thnBudget1'] : $thnBudget = $_POST['thnBudget1'];
$dates = explode('-', $thnBudget);
$lastyear = $dates[0] - 1;
$sOrg = 'select kodeorganisasi from ' . $dbname . '.organisasi where induk = \'' . $kodePt . '\' order by kodeorganisasi';

#exit(mysql_error());
($qOrg = mysql_query($sOrg)) || true;
$where3 = '(';

while ($rData = mysql_fetch_assoc($qOrg)) {
	$where3 .= '\'' . $rData['kodeorganisasi'] . '\',';
}

$where3 = substr($where3, 0, -1);
$where3 .= ')';
$where = ' unit=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'';

if ($kodeOrg == '') {
	$where = ' unit in ' . $where3 . ' and tahunbudget = \'' . $thnBudget . '\'';
}

$where2 = ' kodeorg=\'' . $kodeOrg . '\' and periode = \'' . $lastyear . '12\' group by noakun';

if ($kodeOrg == '') {
	$where2 = ' kodeorg in ' . $where3 . ' and periode = \'' . $lastyear . '12\' group by noakun';
}

$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$sLastsum = 'select noakun,sum(awal12+debet12-kredit12) as lastsum, kodeorg from ' . $dbname . '.keu_saldobulanan where  ' . $where2 . '';

#exit(mysql_error($conn));
($qLastsum = mysql_query($sLastsum)) || true;

while ($hasil = mysql_fetch_assoc($qLastsum)) {
	$lastsum[$hasil['noakun']] = $hasil['lastsum'];
}

$sLastsum = 'select * from ' . $dbname . '.bgt_summary_biaya_vw where ' . $where . ' order by noakun';
$isidata = array();

#exit(mysql_error($conn));
($qLastsum = mysql_query($sLastsum)) || true;

while ($hasil = mysql_fetch_assoc($qLastsum)) {
	$noakun = $hasil['noakun'];
	$isidata[$noakun]['noakun'] = $noakun;
	$isidata[$noakun] += '01';
	$isidata[$noakun] += '02';
	$isidata[$noakun] += '03';
	$isidata[$noakun] += '04';
	$isidata[$noakun] += '05';
	$isidata[$noakun] += '06';
	$isidata[$noakun] += '07';
	$isidata[$noakun] += '08';
	$isidata[$noakun] += '09';
	$isidata[$noakun] += 10;
	$isidata[$noakun] += 11;
	$isidata[$noakun] += 12;
}

if (($kodePt == '') || ($thnBudget == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab = '<table>' . "\r\n" . ' <tr><td colspan=5 align=left><font size=5>' . strtoupper($_SESSION['lang']['aruskas']) . ' Neraca</font></td></tr>';

	if ($kodePt != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $optNm[$kodePt] . '</td></tr>';
	}

	if ($kodeOrg != '') {
		$tab .= '<tr><td colspan=5 align=left>' . $optNm[$kodeOrg] . '</td></tr>';
	}

	$tab .= '<tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td colspan=2 align=left>' . $thnBudget . '</td></tr>   ' . "\r\n" . ' </table>';
}
else {
	$bg = ' ';
	$brdr = 0;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable width=100%><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['nourut'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['uraian'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['catatan'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['jan'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['peb'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['mar'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['apr'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['mei'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['jun'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['jul'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['agt'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['sep'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['okt'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['nov'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['dec'] . '</td>';
$tab .= '<td align=center ' . $bg . ' >' . $_SESSION['lang']['total'] . '</td>';
$tab .= '</tr></thead><tbody>';
$tab .= printline('1');
$tab .= printline('11');
$tab .= printline2('11');
$tab .= printline3('11');
$tab .= printline('12');
$tab .= printline2('12');
$tab .= printline3('12');
$tab .= printline3('1');
$tab .= printline('2');
$tab .= printline('21');
$tab .= printline2('21');
$tab .= printline3('21');
$tab .= printline('22');
$tab .= printline2('22');
$tab .= printline3('22');
$tab .= printline3('2');
$tab .= printline('3');
$tab .= printline2('3');
$tab .= printline3('3');
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'laparuskasbudget_' . $kodeOrg . $thnBudget . '_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                        </script>';
	break;
}

?>
