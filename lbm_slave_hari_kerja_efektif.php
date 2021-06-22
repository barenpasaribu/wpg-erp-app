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

$addTmb = ' lokasitugas=\'' . $kdUnit . '\'';
$ert = 'substr(a.kodeorg,1,4)=\'' . $kdUnit . '\'';
$nit = 'unit=\'' . $kdUnit . '\'';

if ($afdId != '') {
	$addTmb = ' subbagian=\'' . $afdId . '\'';
	$ert = ' a.kodeorg=\'' . $afdId . '\'';
	$nit = ' unit=\'' . $afdId . '\'';
}

$sTotal = 'select distinct count(karyawanid) as jmlKary,jeniskelamin from ' . $dbname . '.datakaryawan where ' . "\r\n" . '         ' . $addTmb . ' and  tipekaryawan in (3,4) and substr(tanggalmasuk,1,7)<\'' . $periode . '\'  ' . "\r\n" . '         and (substr(tanggalkeluar,1,7)>=\'' . $periode . '\' or substr(tanggalkeluar,1,7)=\'0000-00\')' . "\r\n" . '         group by jeniskelamin order by karyawanid asc';

#exit(mysql_error());
($qTotal = mysql_query($sTotal)) || true;

while ($rTotal = mysql_fetch_assoc($qTotal)) {
	$jmlhTotal += $rTotal['jeniskelamin'];
}

$totalSma = $jmlhTotal[L] + $jmlhTotal[P];
@$persenLaki = ($jmlhTotal[L] / $totalSma) * 100;
@$persenPerem = ($jmlhTotal[P] / $totalSma) * 100;
$persenSma = $persenLaki + $persenPerem;
$thn = explode('-', $periode);
$sHari = 'select distinct hkefektif  from ' . $dbname . '.sdm_hk_efektif where periode=\'' . $thn[0] . '' . $thn[1] . '\'';

#exit(mysql_error());
($qHari = mysql_query($sHari)) || true;
$rHari = mysql_fetch_assoc($qHari);
$jmlHari = cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$hkEfektif = $rHari['hkefektif'];
$sAbsensi = 'select distinct count(absensi) as jmlhAbsen,b.jeniskelamin,absensi from ' . $dbname . '.sdm_absensidt a left join ' . "\r\n" . '           ' . $dbname . '.datakaryawan b ON a.karyawanid=b.karyawanid where substr(a.tanggal,1,7)=\'' . $periode . '\' and ' . $ert . "\r\n" . '           and b.tipekaryawan in (3,4) and absensi not in (\'AS\',\'BK\',\'DT\',\'PC\')' . "\r\n" . '           group by absensi,b.jeniskelamin';

#exit(mysql_error());
($qAbsensi = mysql_query($sAbsensi)) || true;

while ($rAbsensi = mysql_fetch_assoc($qAbsensi)) {
	$dtAbsensi += $rAbsensi['jeniskelamin'];
	$lstDataAbsn[$rAbsensi['jeniskelamin']] += $rAbsensi['absensi'];
}

$sKehadiran = 'select distinct count(absensi) as jmlhAbsen,b.jeniskelamin,absensi from ' . $dbname . '.kebun_kehadiran_vw a left join ' . "\r\n" . '           ' . $dbname . '.datakaryawan b ON a.karyawanid=b.karyawanid where substr(a.tanggal,1,7)=\'' . $periode . '\' and ' . $nit . "\r\n" . '           and b.tipekaryawan in (3,4)  group by absensi,b.jeniskelamin';

#exit(mysql_error());
($qKehadiran = mysql_query($sKehadiran)) || true;

while ($rKehadiran = mysql_fetch_assoc($qKehadiran)) {
	$dtAbsensi += $rKehadiran['jeniskelamin'];
	$lstDataAbsn[$rKehadiran['jeniskelamin']] += $rKehadiran['absensi'];
}

$jmlhSemua4 = $dtAbsensi[L] + $dtAbsensi[P];
@$persen4laki = ($dtAbsensi[L] / $jmlhSemua4) * 100;
@$persen4perm = ($dtAbsensi[P] / $jmlhSemua4) * 100;
$persenSma4 = $persen4laki + $persen4perm;
$sKabsensi = 'select distinct kodeabsen from ' . $dbname . '.sdm_5absensi where kelompok=1 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

#exit(mysql_error());
($qKabsensi = mysql_query($sKabsensi)) || true;

while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
	$totAbsnDbyr += L;
	$totAbsnDbyr += P;
}

$jmlhSemuaDbyr = $totAbsnDbyr[L] + $totAbsnDbyr[P];
@$persenDbyrlaki = ($totAbsnDbyr[L] / $jmlhSemuaDbyr) * 100;
@$persenDbyrperm = ($totAbsnDbyr[P] / $jmlhSemuaDbyr) * 100;
$persenDbyr = $persenDbyrlaki + $persenDbyrperm;
$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;
$varCek = count($dtAbsensi);

if ($varCek == 0) {
	exit('Error:Data Kosong');
}

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=8 align=left><b>04.5 HARI KERJA EFEKTIF KHT</b></td><td colspan=3 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=8 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=8 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNmOrg[$afdId] . ' </td></tr>';
	}

	$tab .= '<tr><td colspan=8 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=4 colspan=5>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . '  rowspan=2 colspan=4>' . $_SESSION['lang']['karyawan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=3 colspan=2>' . $_SESSION['lang']['total'] . '</td><tr>';
$tab .= '<tr><td  ' . $bgcoloraja . '  colspan=2>' . $_SESSION['lang']['pria'] . '</td><td  ' . $bgcoloraja . ' colspan=2>' . $_SESSION['lang']['wanita'] . '</td></tr>';
$tab .= '<tr><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['jumlah'] . '</td><td ' . $bgcoloraja . ' >%</td><td ' . $bgcoloraja . ' >' . $_SESSION['lang']['jumlah'] . '</td><td ' . $bgcoloraja . ' >%</td>' . "\r\n" . '               <td ' . $bgcoloraja . ' >' . $_SESSION['lang']['jumlah'] . '</td><td ' . $bgcoloraja . ' >%</td></tr>';
$tab .= '</thead>' . "\r\n\t" . '<tbody>';
$tab .= '<tr class=rowcontent><td>1.</td><td colspan=4>Total Pekerja SKU Harian</td>';
$tab .= '<td align=right >' . number_format($jmlhTotal[L], 0) . '</td><td align=right>' . number_format($persenLaki, 2) . '</td>';
$tab .= '<td align=right>' . number_format($jmlhTotal[P], 0) . '</td><td align=right>' . number_format($persenPerem, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totalSma, 0) . '</td><td align=right>' . number_format($persenSma, 0) . '</td></tr>';
$tab .= '<tr class=rowcontent><td>2.</td><td colspan=4>Hari Kerja</td>';
$tab .= '<td align=right>' . number_format($hkEfektif, 0) . '</td><td colspan=3>&nbsp;</td>';
$tab .= '<td align=right>' . number_format($hkEfektif, 0) . '</td><td align=right>&nbsp;</td></tr>';
$jmlHrKerja[L] = $hkEfektif * $jmlhTotal[L];
$jmlHrKerja[P] = $hkEfektif * $jmlhTotal[P];
$totJmlHrKerja = $jmlHrKerja[L] + $jmlHrKerja[P];
@$perHkkerjaLak = ($jmlHrKerja[L] / $totJmlHrKerja) * 100;
@$perHkkerjaPer = ($jmlHrKerja[P] / $totJmlHrKerja) * 100;
$tab .= '<tr class=rowcontent><td>3.</td><td colspan=4>Hari Kerja Orang</td>';
$tab .= '<td align=right>' . number_format($jmlHrKerja[L], 0) . '</td><td align=right>' . number_format($perHkkerjaLak, 2) . '</td>';
$tab .= '<td align=right>' . number_format($jmlHrKerja[P], 0) . '</td><td align=right>' . number_format($perHkkerjaPer, 2) . '</td><td align=right>' . $totJmlHrKerja . '</td><td align=right>&nbsp;</td></tr>';
$tab .= '<tr class=rowcontent><td>4.</td><td colspan=4>Absensi Karyawan</td>';
$tab .= '<td align=right >' . number_format($dtAbsensi[L], 0) . '</td><td align=right>' . number_format($persen4laki, 2) . '</td>';
$tab .= '<td align=right>' . number_format($dtAbsensi[P], 0) . '</td><td align=right>' . number_format($persen4perm, 2) . '</td>';
$tab .= '<td align=right>' . number_format($jmlhSemua4, 0) . '</td><td align=right>' . number_format($persenSma4, 0) . '</td></tr>';
$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;&nbsp;&nbsp;</td>' . "\r\n" . '               <td colspan=2>A.  Absensi Dibayar</td><td>&nbsp;</td><td colspan=6>&nbsp;</td></tr>';
$sKabsensi = 'select distinct kodeabsen,keterangan from ' . $dbname . '.sdm_5absensi where kelompok=1 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

#exit(mysql_error());
($qKabsensi = mysql_query($sKabsensi)) || true;

while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
	$jmlhSemuaDbyr2[$rKabsensi['kodeabsen']] = $lstDataAbsn[L][$rKabsensi['kodeabsen']] + $lstDataAbsn[P][$rKabsensi['kodeabsen']];
	@$persenDbyrlaki2[$rKabsensi['kodeabsen']] = ($lstDataAbsn[L][$rKabsensi['kodeabsen']] / $jmlhSemuaDbyr2[$rKabsensi['kodeabsen']]) * 100;
	@$persenDbyrperm2[$rKabsensi['kodeabsen']] = ($lstDataAbsn[P][$rKabsensi['kodeabsen']] / $jmlhSemuaDbyr2[$rKabsensi['kodeabsen']]) * 100;
	$persenDbyr2[$rKabsensi['kodeabsen']] = $persenDbyrlaki2[$rKabsensi['kodeabsen']] + $persenDbyrperm2[$rKabsensi['kodeabsen']];
	$tab .= '<tr class=rowcontent><td colspan=4>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>' . $rKabsensi['kodeabsen'] . ' (' . $rKabsensi['keterangan'] . ') </td>';
	$tab .= '<td align=right >' . number_format($lstDataAbsn[L][$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyrlaki2[$rKabsensi['kodeabsen']], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($lstDataAbsn[P][$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyrperm2[$rKabsensi['kodeabsen']], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jmlhSemuaDbyr2[$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyr2[$rKabsensi['kodeabsen']], 0) . '</td></tr>';
}

$tab .= '<tr class=rowcontent><td colspan=4>&nbsp;&nbsp;&nbsp;</td><td>Sub Total Absensi Dibayar</td>';
$tab .= '<td align=right >' . number_format($totAbsnDbyr[L], 0) . '</td><td align=right>' . number_format($persenDbyrlaki, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totAbsnDbyr[P], 0) . '</td><td align=right>' . number_format($persenDbyrperm, 2) . '</td>';
$tab .= '<td align=right>' . number_format($jmlhSemuaDbyr, 0) . '</td><td align=right>' . number_format($persenDbyr, 0) . '</td></tr>';
$tab .= '<tr class=rowcontent><td colspan=2>&nbsp;&nbsp;&nbsp;</td>' . "\r\n" . '               <td colspan=2>B.  Absen Tidak Dibayar</td><td>&nbsp;</td><td colspan=6>&nbsp;</td></tr>';
$sKabsensi = 'select distinct kodeabsen,keterangan from ' . $dbname . '.sdm_5absensi where kelompok=0 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

#exit(mysql_error());
($qKabsensi = mysql_query($sKabsensi)) || true;

while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
	$jmlhSemuaDbyr2[$rKabsensi['kodeabsen']] = $lstDataAbsn[L][$rKabsensi['kodeabsen']] + $lstDataAbsn[P][$rKabsensi['kodeabsen']];
	@$persenDbyrlaki2[$rKabsensi['kodeabsen']] = ($lstDataAbsn[L][$rKabsensi['kodeabsen']] / $jmlhSemuaDbyr2[$rKabsensi['kodeabsen']]) * 100;
	@$persenDbyrperm2[$rKabsensi['kodeabsen']] = ($lstDataAbsn[P][$rKabsensi['kodeabsen']] / $jmlhSemuaDbyr2[$rKabsensi['kodeabsen']]) * 100;
	$persenDbyr2[$rKabsensi['kodeabsen']] = $persenDbyrlaki2[$rKabsensi['kodeabsen']] + $persenDbyrperm2[$rKabsensi['kodeabsen']];
	$tab .= '<tr class=rowcontent><td colspan=4>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>' . $rKabsensi['kodeabsen'] . ' (' . $rKabsensi['keterangan'] . ') </td>';
	$tab .= '<td align=right >' . number_format($lstDataAbsn[L][$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyrlaki2[$rKabsensi['kodeabsen']], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($lstDataAbsn[P][$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyrperm2[$rKabsensi['kodeabsen']], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($jmlhSemuaDbyr2[$rKabsensi['kodeabsen']], 0) . '</td><td align=right>' . number_format($persenDbyr2[$rKabsensi['kodeabsen']], 0) . '</td></tr>';
}

$sKabsensi = 'select distinct kodeabsen from ' . $dbname . '.sdm_5absensi where kelompok=0 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

#exit(mysql_error());
($qKabsensi = mysql_query($sKabsensi)) || true;

while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
	$totAbsnDbyr5 += L;
	$totAbsnDbyr5 += P;
}

$jmlhSemuaDbyr5 = $totAbsnDbyr5[L] + $totAbsnDbyr5[P];
@$persenDbyrlaki5 = ($totAbsnDbyr5[L] / $jmlhSemuaDbyr5) * 100;
@$persenDbyrperm5 = ($totAbsnDbyr5[P] / $jmlhSemuaDbyr5) * 100;
$persenDbyr5 = $persenDbyrlaki5 + $persenDbyrperm5;
$tab .= '<tr class=rowcontent><td colspan=4>&nbsp;&nbsp;&nbsp;</td><td>Sub Total Absensi Tidak Dibayar</td>';
$tab .= '<td align=right >' . number_format($totAbsnDbyr5[L], 0) . '</td><td align=right>' . number_format($persenDbyrlaki5, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totAbsnDbyr5[P], 0) . '</td><td align=right>' . number_format($persenDbyrperm5, 2) . '</td>';
$tab .= '<td align=right>' . number_format($jmlhSemuaDbyr5, 0) . '</td><td align=right>' . number_format($persenDbyr5, 0) . '</td></tr>';
$hkEfektif5[L] = $jmlHrKerja[L] - $totAbsnDbyr5[L];
$hkEfektif5[P] = $jmlHrKerja[P] - $totAbsnDbyr5[P];
$totHkEfektif = $hkEfektif5[L] + $hkEfektif5[P];
@$persenLak = ($hkEfektif5[L] / $totHkEfektif) * 100;
@$persenPer = ($hkEfektif5[P] / $totHkEfektif) * 100;
$persenHkSma = $persenLak + $persenPer;
$tab .= '<tr class=rowcontent><td>5.</td><td colspan=4>A.  Hari Efektif   3 - 4B</td>';
$tab .= '<td align=right >' . number_format($hkEfektif5[L], 0) . '</td><td align=right>' . number_format($persenLak, 2) . '</td>';
$tab .= '<td align=right>' . number_format($hkEfektif5[P], 0) . '</td><td align=right>' . number_format($persenPer, 2) . '</td>';
$tab .= '<td align=right>' . number_format($totHkEfektif, 0) . '</td><td align=right>' . number_format($persenHkSma, 0) . '</td></tr>';
$hkEfektif2[L] = $jmlHrKerja[L] - $dtAbsensi[L];
$hkEfektif2[P] = $jmlHrKerja[P] - $dtAbsensi[P];
$totHkEfektif2 = $hkEfektif2[L] + $hkEfektif2[P];
@$persenLak2 = ($hkEfektif2[L] / $totHkEfektif2) * 100;
@$persenPer2 = ($hkEfektif2[P] / $totHkEfektif2) * 100;
$persenHkSma2 = $persenLak2 + $persenPer2;
$tab .= '<tr class=rowcontent><td>&nbsp;</td><td colspan=4>B.  Hari Kerja Efektif   3-(4A + 4B)</td>';
$tab .= '<td align=right >' . number_format($hkEfektif2[L], 0) . '</td><td align=right>' . number_format($persenLak2, 0) . '</td>';
$tab .= '<td align=right>' . number_format($hkEfektif2[P], 0) . '</td><td align=right>' . number_format($persenPer2, 0) . '</td>';
$tab .= '<td align=right>' . number_format($totHkEfektif2, 0) . '</td><td align=right>' . number_format($persenHkSma2, 0) . '</td></tr>';
@$hkEfektif3[L] = ($hkEfektif5[L] / $jmlHrKerja[L]) * 100;
@$hkEfektif3[P] = ($hkEfektif5[P] / $jmlHrKerja[P]) * 100;
$tab .= '<tr class=rowcontent><td>6.</td><td colspan=4>% Hari Efektif  (5A/3)x100</td>';
$tab .= '<td align=right >' . number_format($hkEfektif3[L], 2) . '</td><td align=right>&nbsp;</td>';
$tab .= '<td align=right>' . number_format($hkEfektif3[P], 2) . '</td><td align=right>&nbsp;</td>';
$tab .= '<td align=right>&nbsp;</td><td align=right>&nbsp;</td></tr>';
@$hkEfektif7[L] = ($hkEfektif2[L] / $jmlHrKerja[L]) * 100;
@$hkEfektif7[P] = ($hkEfektif2[P] / $jmlHrKerja[P]) * 100;
$tab .= '<tr class=rowcontent><td>7.</td><td colspan=4>% Hari Efektif  (5B/3)x100</td>';
$tab .= '<td align=right >' . number_format($hkEfektif7[L], 2) . '</td><td align=right>&nbsp;</td>';
$tab .= '<td align=right>' . number_format($hkEfektif7[P], 2) . '</td><td align=right>&nbsp;</td>';
$tab .= '<td align=right>&nbsp;</td><td align=right>&nbsp;</td></tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'hari_kerja_' . $dte;
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
			global $afdId;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('04.5 HARI KERJA EFEKTIF KHT'), 0, 1, 'L');
			$this->Cell(790, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');

			if ($afdId != '') {
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
			$height = 80;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(180, $height, $_SESSION['lang']['uraian'], 1, 0, 'C', 1);
			$this->Cell(360, 30, $_SESSION['lang']['karyawan'], 1, 0, 'C', 1);
			$this->Cell(180, 60, $_SESSION['lang']['total'], 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 30);
			$this->SetX($ksamping - 540);
			$this->Cell(180, 30, $_SESSION['lang']['pria'], 1, 0, 'C', 1);
			$this->Cell(180, 30, $_SESSION['lang']['wanita'], 1, 0, 'C', 1);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 30);
			$this->SetX($ksamping - 360);
			$this->Cell(90, 20, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell(90, 20, '%', 1, 0, 'C', 1);
			$this->Cell(90, 20, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell(90, 20, '%', 1, 0, 'C', 1);
			$this->Cell(90, 20, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell(90, 20, '%', 1, 1, 'C', 1);
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
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(20, $height, '1.', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'Total Pekerja SKU Harian', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($jmlhTotal[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenLaki, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhTotal[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenPerem, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($totalSma, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenSma, 0), 1, 1, 'R', 1);
	$pdf->Cell(20, $height, '2.', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'Hari Kerja', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif, 0), 1, 0, 'R', 1);
	$pdf->Cell(270, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, '', 1, 1, 'R', 1);
	$pdf->Cell(20, $height, '3.', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'Hari Kerja Orang', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($jmlHrKerja[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($perHkkerjaLak, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlHrKerja[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($perHkkerjaPer, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($totJmlHrKerja, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, $perHkkerjaLak + $perHkkerjaPer, 1, 1, 'R', 1);
	$pdf->Cell(20, $height, '4.', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'Absensi Karyawan', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4laki, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4perm, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhSemua4, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenSma4, 0), 1, 1, 'R', 1);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'A.  Absensi Dibayar', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4laki, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4perm, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhSemua4, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenSma4, 0), 1, 1, 'R', 1);
	$sKabsensi = 'select distinct kodeabsen,keterangan from ' . $dbname . '.sdm_5absensi where kelompok=1 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

	#exit(mysql_error());
	($qKabsensi = mysql_query($sKabsensi)) || true;

	while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
		$pdf->Cell(50, $height, ' ', 1, 0, 'L', 1);
		$pdf->Cell(130, $height, $rKabsensi['kodeabsen'] . ' (' . $rKabsensi['keterangan'] . ')', 1, 0, 'L', 1);
		$pdf->Cell(90, 20, number_format($lstDataAbsn[L][$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyrlaki2[$rKabsensi['kodeabsen']], 2), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($lstDataAbsn[P][$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyrperm2[$rKabsensi['kodeabsen']], 2), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($jmlhSemuaDbyr2[$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyr2[$rKabsensi['kodeabsen']], 0), 1, 1, 'R', 1);
	}

	$pdf->Cell(50, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(130, $height, 'Sub Total Absensi Dibayar', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($totAbsnDbyr[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyrlaki, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($totAbsnDbyr[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyrperm, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhSemuaDbyr, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyr, 0), 1, 1, 'R', 1);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, 'B.  Absen Tidak Dibayar', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4laki, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($dtAbsensi[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persen4perm, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhSemua4, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenSma4, 0), 1, 1, 'R', 1);
	$sKabsensi = 'select distinct kodeabsen,keterangan from ' . $dbname . '.sdm_5absensi where kelompok=0 and kodeabsen not in (\'AS\',\'BK\',\'DT\',\'PC\') order by kodeabsen asc';

	#exit(mysql_error());
	($qKabsensi = mysql_query($sKabsensi)) || true;

	while ($rKabsensi = mysql_fetch_assoc($qKabsensi)) {
		$pdf->Cell(50, $height, ' ', 1, 0, 'L', 1);
		$pdf->Cell(130, $height, $rKabsensi['kodeabsen'] . ' (' . $rKabsensi['keterangan'] . ')', 1, 0, 'L', 1);
		$pdf->Cell(90, 20, number_format($lstDataAbsn[L][$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyrlaki2[$rKabsensi['kodeabsen']], 2), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($lstDataAbsn[P][$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyrperm2[$rKabsensi['kodeabsen']], 2), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($jmlhSemuaDbyr2[$rKabsensi['kodeabsen']], 0), 1, 0, 'R', 1);
		$pdf->Cell(90, 20, number_format($persenDbyr2[$rKabsensi['kodeabsen']], 0), 1, 1, 'R', 1);
	}

	$pdf->Cell(50, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(130, $height, 'Sub Total Absensi Tidak Dibayar', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($totAbsnDbyr5[L], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyrlaki5, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($totAbsnDbyr5[P], 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyrperm5, 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($jmlhSemuaDbyr5, 0), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($persenDbyr5, 0), 1, 1, 'R', 1);
	$pdf->Cell(20, $height, '5.', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, '% Hari Efektif  (5A/3)x100', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif3[L], 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif3[P], 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 1, 'R', 1);
	$pdf->Cell(20, $height, ' ', 1, 0, 'L', 1);
	$pdf->Cell(160, $height, '% Hari Efektif  (5A/3)x100', 1, 0, 'L', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif7[L], 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, number_format($hkEfektif7[P], 2), 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 0, 'R', 1);
	$pdf->Cell(90, 20, ' ', 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
