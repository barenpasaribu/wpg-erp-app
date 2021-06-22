<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$sKlmpk = 'select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$thn = explode('-', $periode);
$sCurah = 'select distinct * from ' . $dbname . '.kebun_curahhujan where substr(tanggal,1,7)=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\'';
$koko = $kdUnit;

if ($afdId != '') {
	$koko = $afdId;
	$sCurah = 'select distinct * from ' . $dbname . '.kebun_curahhujan where substr(tanggal,1,7)=\'' . $periode . '\' and kodeorg=\'' . $afdId . '\'';
}

$str = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where kodeorganisasi like \'' . $koko . '%\' and tipe = \'AFDELING\'';

#exit(mysql_error());
($qCurah = mysql_query($str)) || true;
$jumlahdiv = mysql_num_rows($qCurah);

#exit(mysql_error());
($qCurah = mysql_query($sCurah)) || true;

while ($rCurah = mysql_fetch_assoc($qCurah)) {
	$tgl = substr($rCurah['tanggal'], 8, 2);
	$dtHarPagi += $tgl;
	$dtHarSore += $tgl;
	@$jmlh[$tgl] = $dtHarPagi[$tgl] + $dtHarSore[$tgl];
	$dtTgl[] = $tgl;
}

$dtThn5 = $thn[0] - 4;

while ($dtThn5 <= $thn[0]) {
	$awaBln = 1;

	while ($awaBln < 13) {
		if ($awaBln != 1) {
			if (strlen($awaBln) < 2) {
				$kebrp2 = '0' . $awaBln;
			}
			else {
				$kebrp2 = $awaBln;
			}
		}
		else {
			$kebrp2 = '01';
		}

		if (strlen($awaBln) < 2) {
			$kebrp25 = '0' . $awaBln;
		}
		else {
			$kebrp25 = $awaBln;
		}

		$sDtMm = 'select distinct sum(pagi+sore)/' . $jumlahdiv . ' as mm, substr(tanggal,6,2) as bulan from ' . $dbname . '.kebun_curahhujan ' . "\r\n" . '               where substr(tanggal,1,7)=\'' . $dtThn5 . '-' . $kebrp25 . '\' and  kodeorg like \'' . $kdUnit . '%\' ';

		if ($afdId != '') {
			$sDtMm = 'select distinct sum(pagi+sore)/' . $jumlahdiv . ' as mm, substr(tanggal,6,2) as bulan from ' . $dbname . '.kebun_curahhujan ' . "\r\n" . '               where substr(tanggal,1,7)=\'' . $dtThn5 . '-' . $kebrp25 . '\' and  kodeorg=\'' . $afdId . '\' ';
		}

		#exit(mysql_error());
		($qDtMm = mysql_query($sDtMm)) || true;
		$rDtMm = mysql_fetch_assoc($qDtMm);
		$blnananawal = intval($rDtMm['bulan']);

		if ($rDtMm['mm'] != 0) {
			$dtMm[$dtThn5] += $blnananawal;
		}

		$dtHh[$dtThn5][$awaBln] = 0;
		$sDtCurah = 'select distinct sum(pagi+sore) as hh,  tanggal   from ' . $dbname . '.kebun_curahhujan ' . "\r\n" . '                      where substr(tanggal,1,7)=\'' . $dtThn5 . '-' . $kebrp25 . '\'  and kodeorg like \'' . $kdUnit . '%\'' . "\r\n" . '                      group by tanggal order by tanggal desc';

		if ($afdId != '') {
			$sDtCurah = 'select distinct sum(pagi+sore) as hh,  tanggal   from ' . $dbname . '.kebun_curahhujan ' . "\r\n" . '                      where substr(tanggal,1,7)=\'' . $dtThn5 . '-' . $kebrp25 . '\'  and kodeorg=\'' . $afdId . '\'' . "\r\n" . '                      group by tanggal order by tanggal desc';
		}

		#exit(mysql_error());
		($qDtCurah = mysql_query($sDtCurah)) || true;

		while ($rDtCurah = mysql_fetch_assoc($qDtCurah)) {
			$blnananawal = intval(substr($rDtCurah['tanggal'], 5, 2));

			if (0 < $rDtCurah['hh']) {
				$dtHh[$dtThn5] += $blnananawal;
			}
		}

		++$awaBln;
	}

	++$dtThn5;
}

$jmlHari = cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$varCek = count($dtTgl);

if ($varCek < 1) {
	exit('Error:Data Kosong');
}

$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=8 align=left><b>05.3  ' . $_SESSION['lang']['curahHujan'] . ' </b></td><td colspan=8 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=8 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= "\r\n" . '    <tr><td colspan=8 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table border=0><tr>' . "\r\n" . '            <td rowspan=2 valign=top>';
$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=4>' . strtoupper($_SESSION['lang']['curahHujan']) . '  ( MM )</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . '</td><td ' . $bgcoloraja . '>' . $_SESSION['lang']['pagi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['sore'] . '</td>';
$tab .= ' <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlah'] . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>06:30</td><td ' . $bgcoloraja . '>16:00</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$tglMula = 1;

while ($tglMula <= $jmlHari) {
	if (strlen($tglMula) < 2) {
		$lstTgl = '0' . $tglMula;
	}
	else {
		$lstTgl = $tglMula;
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=center>' . $lstTgl . '</td>';
	$tab .= '<td align=right >' . number_format($dtHarPagi[$lstTgl], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($dtHarSore[$lstTgl], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jmlh[$lstTgl], 2) . '</td>';
	$tab .= '</tr>';
	$totPagi += $dtHarPagi[$lstTgl];
	$totSore += $dtHarSore[$lstTgl];
	$totSemua += $jmlh[$lstTgl];
	++$tglMula;
}

$hariHujan = 0;

foreach ($jmlh as $hahu) {
	if (0 < $hahu) {
		$hariHujan += 1;
	}
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '<td align=right>' . $totPagi . '</td>';
$tab .= '<td align=right>' . $totSore . '</td>';
$tab .= '<td align=right>' . $totSemua . '</td>';
$tab .= '</tr>';
@$rataPagi = $totPagi / $jmlHari;
@$rataSore = $totSore / $jmlHari;
@$rataSemua = $totSemua / $jmlHari;
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $_SESSION['lang']['ratarata'] . '</td>';
$tab .= '<td align=right>' . number_format($rataPagi, 2) . '</td>';
$tab .= '<td align=right>' . number_format($rataSore, 2) . '</td>';
$tab .= '<td align=right>' . number_format($rataSemua, 2) . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $_SESSION['lang']['curahHujan'] . '(' . $_SESSION['lang']['hari'] . ')</td>';
$tab .= '<td align=right colspan=2>&nbsp;</td>';
$tab .= '<td align=right>' . number_format($hariHujan) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';
$tab .= '</td><td rowpspan=2>&nbsp;</td><td valign=top>';
$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3>' . $_SESSION['lang']['bulan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=10>HISTORY</td></tr>';
$tab .= '<tr>';
$dtThn = $thn[0] - 4;

while ($dtThn <= $thn[0]) {
	$tab .= '<td colspan=2 ' . $bgcoloraja . ' align=center>' . $dtThn . '</td>';
	++$dtThn;
}

$tab .= '</tr>';
$tab .= '<tr>';
$dtThn2aja = $thn[0] - 4;

while ($dtThn2aja <= $thn[0]) {
	$tab .= '<td ' . $bgcoloraja . ' align=center>HH</td><td ' . $bgcoloraja . ' align=center>MM</td>';
	++$dtThn2aja;
}

$tab .= '</tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$arrBulan = array(1 => 'JANUARI', 2 => 'PEBRUARI', 3 => 'MARET', 4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER');
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[1] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][1] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][1] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[1] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][1] = $dtHh[$dtThn8][1] + 0;
	$mmdt[$dtThn8][1] = $dtMm[$dtThn8][1] + 0;
	$tab .= '<td align=right>' . $hhdt[$dtThn8][1] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][1] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[2] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][2] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][2] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[2] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][2] = $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][2] = $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][2] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][2] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[3] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][3] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][3] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[3] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][3] = $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][3] = $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][3] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][3] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[4] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][4] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][4] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[4] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][4] = $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][4] = $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][4] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][4] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[5] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][5] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][5] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[5] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][5] = $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][5] = $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][5] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][5] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[6] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][6] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][6] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[6] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][6] = $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][6] = $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][6] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][6] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[7] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][7] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][7] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[7] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][7] = $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][7] = $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][7] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][7] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[8] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][8] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][8] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[8] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][8] = $dtHh[$dtThn8][8] + $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][8] = $dtMm[$dtThn8][8] + $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][8] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][8] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[9] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][9] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][9] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[9] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][9] = $dtHh[$dtThn8][9] + $dtHh[$dtThn8][8] + $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][9] = $dtMm[$dtThn8][9] + $dtMm[$dtThn8][8] + $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][9] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][9] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[10] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][10] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][10] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[10] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][10] = $dtHh[$dtThn8][10] + $dtHh[$dtThn8][9] + $dtHh[$dtThn8][8] + $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][10] = $dtMm[$dtThn8][10] + $dtMm[$dtThn8][9] + $dtMm[$dtThn8][8] + $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][10] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][10] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[11] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][11] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][11] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[11] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][11] = $dtHh[$dtThn8][11] + $dtHh[$dtThn8][10] + $dtHh[$dtThn8][9] + $dtHh[$dtThn8][8] + $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][11] = $dtMm[$dtThn8][11] + $dtMm[$dtThn8][10] + $dtMm[$dtThn8][9] + $dtMm[$dtThn8][8] + $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][11] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][11] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>' . $arrBulan[12] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$tab .= '<td align=right>' . $dtHh[$dtThn8][12] . '</td>';
	$tab .= '<td align=right>' . $dtMm[$dtThn8][12] . '</td>';
	$totHh += $dtThn8;
	$totMm += $dtThn8;
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '<tr class=rowcontent>';
$tab .= '<td>s.d ' . $arrBulan[12] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	$hhdt[$dtThn8][12] = $dtHh[$dtThn8][12] + $dtHh[$dtThn8][11] + $dtHh[$dtThn8][10] + $dtHh[$dtThn8][9] + $dtHh[$dtThn8][8] + $dtHh[$dtThn8][7] + $dtHh[$dtThn8][6] + $dtHh[$dtThn8][5] + $dtHh[$dtThn8][4] + $dtHh[$dtThn8][3] + $dtHh[$dtThn8][2] + $dtHh[$dtThn8][1];
	$mmdt[$dtThn8][12] = $dtHh[$dtThn8][12] + $dtMm[$dtThn8][11] + $dtMm[$dtThn8][10] + $dtMm[$dtThn8][9] + $dtMm[$dtThn8][8] + $dtMm[$dtThn8][7] + $dtMm[$dtThn8][6] + $dtMm[$dtThn8][5] + $dtMm[$dtThn8][4] + $dtMm[$dtThn8][3] + $dtMm[$dtThn8][2] + $dtMm[$dtThn8][1];
	$tab .= '<td align=right>' . $hhdt[$dtThn8][12] . '</td>';
	$tab .= '<td align=right>' . $mmdt[$dtThn8][12] . '</td>';
	$totHhsd += $dtThn8;
	$totMmsd += $dtThn8;
	++$dtThn8;
}

$tab .= '<tr class=rowcontent>';
$tab .= '<td >' . $_SESSION['lang']['ratarata'] . '</td>';
$dtThn8 = $thn[0] - 4;

while ($dtThn8 <= $thn[0]) {
	@$bulan = $totHh[$dtThn8] / 12;
	@$hari = $totMm[$dtThn8] / 12;
	$tab .= '<td align=right>' . number_format($bulan, 2) . '</td>';
	$tab .= '<td align=right>' . number_format($hari, 2) . '</td>';
	++$dtThn8;
}

$tab .= '</tr>';
$tab .= '</tbody></table>';
$tab .= '</td></tr><tr><td>&nbsp;</td><td valign=top>';
$tab .= '</td></tr></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'curah_hujan_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $hhdt;
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('05.3  ' . $_SESSION['lang']['curahHujan']), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');

			if ($afdId != '') {
				$this->Cell(790, $height, ' ', 0, 1, 'R');
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId], 0, 1, 'L');
			}

			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(240, $height, $_SESSION['lang']['curahHujan'] . '  ( MM )', TBLR, 1, 'C', 1);
			$this->Cell(60, 30, 'TGL', TBLR, 0, 'C', 1);
			$this->Cell(60, 17, $_SESSION['lang']['pagi'], TBLR, 0, 'C', 1);
			$this->Cell(60, 17, $_SESSION['lang']['sore'], TBLR, 0, 'C', 1);
			$this->Cell(60, 30, $_SESSION['lang']['jumlah'], TBLR, 1, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr - 13);
			$this->SetX($ksamping + 60);
			$this->Cell(60, 13, '06:30', TBLR, 0, 'C', 1);
			$this->Cell(60, 13, '16:00', TBLR, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr - 32);
			$this->SetX($ksamping + 60);
			$this->Cell(60, 45, ' ', 0, 0, 'C', 0);
			$this->Cell(60, $height, ' ', TLR, 0, 'C', 1);
			$this->Cell(400, $height, $_SESSION['lang']['tahun'], TBLR, 1, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping + 300);
			$this->Cell(60, $height, $_SESSION['lang']['bulan'], LR, 0, 'C', 1);
			$dtThn8 = $thn[0] - 4;

			while ($dtThn8 <= $thn[0]) {
				if ($dtThn8 != $thn[0]) {
					$this->Cell(80, $height, $dtThn8, TLR, 0, 'C', 1);
				}
				else {
					$this->Cell(80, $height, $dtThn8, TLR, 1, 'C', 1);
				}

				++$dtThn8;
			}

			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr);
			$this->SetX($ksamping + 300);
			$this->Cell(60, $height, ' ', BLR, 0, 'C', 1);
			$dtThn8 = $thn[0] - 4;

			while ($dtThn8 <= $thn[0]) {
				if ($dtThn8 != $thn[0]) {
					$this->Cell(40, $height, 'HH', TBLR, 0, 'C', 1);
					$this->Cell(40, $height, 'MM', TBLR, 0, 'C', 1);
				}
				else {
					$this->Cell(40, $height, 'HH', TBLR, 0, 'C', 1);
					$this->Cell(40, $height, 'MM', TBLR, 1, 'C', 1);
				}

				++$dtThn8;
			}
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);
	$tglMula = 1;

	while ($tglMula <= $jmlHari) {
		if (strlen($tglMula) < 2) {
			$lstTgl = '0' . $tglMula;
		}
		else {
			$lstTgl = $tglMula;
		}

		$pdf->Cell(60, $height, $lstTgl, 1, 0, 'C', 1);
		$pdf->Cell(60, $height, number_format($dtHarPagi[$lstTgl], 2), 1, 0, 'R', 1);
		$pdf->Cell(60, $height, number_format($dtHarSore[$lstTgl], 2), 1, 0, 'R', 1);
		$pdf->Cell(60, $height, number_format($jmlh[$lstTgl], 2), 1, 1, 'R', 1);
		++$tglMula;
	}

	$pdf->Cell(60, $height, $_SESSION['lang']['total'], 1, 0, 'L', 1);
	$pdf->Cell(60, $height, number_format($totPagi, 2), 1, 0, 'R', 1);
	$pdf->Cell(60, $height, number_format($totSore, 2), 1, 0, 'R', 1);
	$pdf->Cell(60, $height, number_format($totSemua, 2), 1, 1, 'R', 1);
	$pdf->Cell(60, $height, $_SESSION['lang']['ratarata'], 1, 0, 'L', 1);
	$pdf->Cell(60, $height, number_format($rataPagi, 2), 1, 0, 'R', 1);
	$pdf->Cell(60, $height, number_format($rataSore, 2), 1, 0, 'R', 1);
	$pdf->Cell(60, $height, number_format($rataSemua, 2), 1, 1, 'R', 1);
	$pdf->Cell(120, $height, $_SESSION['lang']['curahHujan'] . ' (' . $_SESSION['lang']['hari'] . ')', 1, 0, 'L', 1);
	$pdf->Cell(60, $height, ' ', 1, 0, 'R', 1);
	$pdf->Cell(60, $height, number_format($hariHujan, 2), 1, 0, 'R', 1);
	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr - ($tnggi + 20));
	$pdf->SetX($ksamping);
	$pdf->Cell(60, $tnggi - 10, '   ', 0, 0, 'C', 0);
	$height = 10;
	$pdf->Cell(60, 10, 'JANUARI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][1], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][1], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d JANUARI', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][1], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][1], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'PEBRUARI', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][1], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][1], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][1], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's.d PEBRUARI', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][2], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][2], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][2], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][2], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'MARET', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][3], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][3], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][3], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][3], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d MARET', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][3], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][3], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][3], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][3], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'APRIL', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][4], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][4], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][4], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][4], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d APRIL', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][4], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][4], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][4], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][4], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'MEI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][5], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][5], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][5], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][5], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d MEI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][5], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][5], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][5], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][5], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'JUNI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][6], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][6], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][6], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][6], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d JUNI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][6], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][6], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][6], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][6], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'JULI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][7], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][7], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][7], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][7], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d JULI', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][7], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][7], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][7], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][7], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'AGUSTUS', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][8], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][8], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][8], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][8], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d AGUSTUS', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][8], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][8], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][8], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][8], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'SEPTEMBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][9], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][9], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][9], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][9], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d SEPTEMBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][9], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][9], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][9], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][9], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'OKTOBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][10], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][10], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][10], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][10], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d OKTOBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][10], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][10], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][10], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][10], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'NOVEMBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][11], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][11], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][11], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][11], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d NOVEMBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][11], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][11], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][11], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][11], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 'DESEMBER', TLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][12], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][12], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $dtHh[$dtThn8][12], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $dtMm[$dtThn8][12], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, 10, 's/d DESEMBER', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][12], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][12], TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, $hhdt[$dtThn8][12], TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, $mmdt[$dtThn8][12], TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$tinggiAkr = $pdf->GetY();
	$ksamping = $pdf->GetX();
	$pdf->SetY($tinggiAkr);
	$pdf->SetX($ksamping - ($jumRow + 222));
	$pdf->Cell(60, $height, 'RATA-RATA', TBLR, 0, 'L', 1);
	$dtThn8 = $thn[0] - 4;
	$jumRow = $thn[0] - $thn[0] - 4;
	$jumRow = $jumRow * 80;

	while ($dtThn8 <= $thn[0]) {
		@$bulan = $totHh[$dtThn8] / 12;
		@$hari = $totMm[$dtThn8] / 12;

		if ($dtThn8 != $thn[0]) {
			$pdf->Cell(40, $height, number_format($bulan, 2), TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, number_format($hari, 2), TBLR, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, $height, number_format($bulan, 2), TBLR, 0, 'R', 1);
			$pdf->Cell(40, $height, number_format($hari, 2), TBLR, 1, 'R', 1);
		}

		++$dtThn8;
	}

	$pdf->Output();
	break;
}

?>
