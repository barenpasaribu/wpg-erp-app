<?php


function numberformat($qwe, $asd)
{
	if ($qwe == 0) {
		$zxc = '0';
	}
	else {
		$zxc = number_format($qwe, $asd);
	}

	return $zxc;
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['unit'] == '' ? $unit = $_GET['unit'] : $unit = $_POST['unit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['afdId'] == '' ? $afdId = $_GET['afdId'] : $afdId = $_POST['afdId'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (($unit == '') || ($periode == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
$addTmb = ' lokasitugas=\'' . $unit . '\'';
$awalr = 'kodeorg = \'' . $unit . '\' and tahunpembuatan <= \'' . $tahun . '\'';
$kkr = ' and kompleks = \'' . $unit . '\'';

if ($afdId != '') {
	$addTmb = ' subbagian=\'' . $afdId . '\'';
	$awalr = 'kodeorg = \'' . $afdId . '\' and tahunpembuatan <= \'' . $tahun . '\'';
	$kkr = ' and kompleks = \'' . $afdId . '\'';
}

$awal = '' . $addTmb . ' and tanggalmasuk <= \'' . $periode . '-15\' and (tanggalkeluar is NULL or tanggalkeluar>\'' . $periode . '-15\')';

if ($afdId == '') {
	$kk = ' and subbagian = \'\'';
}

$sOrg = 'select * from ' . $dbname . '.datakaryawan where ' . $awal . '' . $kk;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$karyawankk = mysql_num_rows($qOrg);
$totalkaryawan = $karyawankk;
$huni = ' and status is not NULL';
$kosong = ' and status is NULL';
$bbd = ' and kondisi = \'b-bd\'';
$btd = ' and kondisi = \'b-td\'';
$rbd = ' and kondisi = \'r-bd\'';
$rtd = ' and kondisi = \'r-td\'';
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$pintukk = mysql_num_rows($qOrg);
$totalpintu = $pintukk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $huni;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$hunikk = mysql_num_rows($qOrg);
$totalhuni = $hunikk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $kosong;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$kosongkk = mysql_num_rows($qOrg);
$totalkosong = $kosongkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $huni . '' . $bbd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$hunibbdkk = mysql_num_rows($qOrg);
$totalhunibbd = $hunibbdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $huni . '' . $btd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$hunibtdkk = mysql_num_rows($qOrg);
$totalhunibtd = $hunibtdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $huni . '' . $rbd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$hunirbdkk = mysql_num_rows($qOrg);
$totalhunirbd = $hunirbdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $huni . '' . $rtd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$hunirtdkk = mysql_num_rows($qOrg);
$totalhunirtd = $hunirtdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $kosong . '' . $bbd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$kosongbbdkk = mysql_num_rows($qOrg);
$totalkosongbbd = $kosongbbdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $kosong . '' . $btd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$kosongbtdkk = mysql_num_rows($qOrg);
$totalkosongbtd = $kosongbtdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $kosong . '' . $rbd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$kosongrbdkk = mysql_num_rows($qOrg);
$totalkosongrbd = $kosongrbdkk;
$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . '' . $kkr . '' . $kosong . '' . $rtd;

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$kosongrtdkk = mysql_num_rows($qOrg);
$totalkosongrtd = $kosongrtdkk;
$sDivisi = 'SELECT * FROM ' . $dbname . '.organisasi WHERE `kodeorganisasi` LIKE \'' . $unit . '%\' AND tipe = \'afdeling\'';

if ($afdId != '') {
	$sDivisi = 'SELECT * FROM ' . $dbname . '.organisasi WHERE `kodeorganisasi`=\'' . $afdId . '\'';
}

#exit(mysql_error($conn));
($query = mysql_query($sDivisi)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$divisi[$res['kodeorganisasi']] = $res['kodeorganisasi'];
	$namadivisi[$res['kodeorganisasi']] = $res['namaorganisasi'];
}

if (!empty($divisi)) {
	foreach ($divisi as $divs) {
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\'';

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$pintu[$divs] = mysql_num_rows($qOrg);
		$totalpintu += $pintu[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $huni;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$anuhuni[$divs] = mysql_num_rows($qOrg);
		$totalhuni += $anuhuni[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $kosong;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$anukosong[$divs] = mysql_num_rows($qOrg);
		$totalkosong += $anukosong[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $huni . '' . $bbd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$hunibbd[$divs] = mysql_num_rows($qOrg);
		$totalhunibbd += $hunibbd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $huni . '' . $btd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$hunibtd[$divs] = mysql_num_rows($qOrg);
		$totalhunibtd += $hunibtd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $huni . '' . $rbd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$hunirbd[$divs] = mysql_num_rows($qOrg);
		$totalhunirbd += $hunirbd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $huni . '' . $rtd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$hunirtd[$divs] = mysql_num_rows($qOrg);
		$totalhunirtd += $hunirtd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $kosong . '' . $bbd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$kosongbbd[$divs] = mysql_num_rows($qOrg);
		$totalkosongbbd += $kosongbbd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $kosong . '' . $btd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$kosongbtd[$divs] = mysql_num_rows($qOrg);
		$totalkosongbtd += $kosongbtd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $kosong . '' . $rbd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$kosongrbd[$divs] = mysql_num_rows($qOrg);
		$totalkosongrbd += $kosongrbd[$divs];
		$sOrg = 'select * from ' . $dbname . '.sdm_penghuni2_vw where ' . $awalr . ' and kompleks = \'' . $divs . '\' ' . $kosong . '' . $rtd;

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$kosongrtd[$divs] = mysql_num_rows($qOrg);
		$totalkosongrtd += $kosongrtd[$divs];
	}
}

if (!empty($divisi)) {
	foreach ($divisi as $divs) {
		$sOrg = 'select * from ' . $dbname . '.datakaryawan where ' . $awal . ' and subbagian = \'' . $divs . '\'';

		#exit(mysql_error($conn));
		($qOrg = mysql_query($sOrg)) || true;
		$anu[$divs] = mysql_num_rows($qOrg);
		$totalkaryawan += $anu[$divs];
	}
}

$sOrg = 'select * from ' . $dbname . '.sdm_perumahanht where kodeorg = \'' . $unit . '\' and tahunpembuatan <= \'' . $tahun . '\' and kondisi <>\'2\'';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;
$rumah = mysql_num_rows($qOrg);
@$rharumah = (100 * $rumah) / $karyawan;

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=5 align=left><font size=3>05. RUMAH</font></td>' . "\r\n" . '        <td colspan=16 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=21 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr> ';

	if ($afdId != '') {
		$tab .= '<tr><td colspan=21 align=left>' . $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')</td></tr> ';
	}

	$tab .= '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td rowspan=4 align=center ' . $bg . '>' . $_SESSION['lang']['status'] . '</td>';
$tab .= '<td rowspan=4 align=center ' . $bg . '>' . $_SESSION['lang']['jumlahkaryawan'] . '</td>';
$tab .= '<td rowspan=4 align=center ' . $bg . '>' . $_SESSION['lang']['jumlahpintu'] . '</td>';
$tab .= '<td rowspan=2 colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jumlahpintu'] . '</td>';
$tab .= '<td colspan=16 align=center ' . $bg . '>' . $_SESSION['lang']['kondisi'] . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td colspan=8 align=center ' . $bg . '>' . $_SESSION['lang']['dihuni'] . '</td>';
$tab .= '<td colspan=8 align=center ' . $bg . '>' . $_SESSION['lang']['kosong'] . '</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['dihuni'] . '</td>';
$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kosong'] . '</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>B-BD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>B-TD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>R-BD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>R-TD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>B-BD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>B-TD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>R-BD</td>';
$tab .= '<td colspan=2 align=center ' . $bg . '>R-TD</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['pintu'] . '</td>';
$tab .= '<td align=center ' . $bg . '>%</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td align=center ' . $bg . '><font size=-3><font size=-3>1</font></font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>2</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>3</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>4</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>5</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>6</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>7=(6/4)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>8</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>9=(8/4)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>10</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>11=(10/4)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>12</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>13=(12/4)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>14</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>15=(14/5)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>16</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>17=(16/5)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>18</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>19=(18/5)*100</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>20</font></td>';
$tab .= '<td align=center ' . $bg . '><font size=-3>21=(20/5)*100</font></td>';
$tab .= '</tr>';
$tab .= '</thead><tbody>';
@$phunibbdkk = ($hunibbdkk / $hunikk) * 100;
@$phunibtdkk = ($hunibtdkk / $hunikk) * 100;
@$phunirbdkk = ($hunirbdkk / $hunikk) * 100;
@$phunirtdkk = ($hunirtdkk / $hunikk) * 100;
@$pkosongbbdkk = ($kosongbbdkk / $kosongkk) * 100;
@$pkosongbtdkk = ($kosongbtdkk / $kosongkk) * 100;
@$pkosongrbdkk = ($kosongrbdkk / $kosongkk) * 100;
@$pkosongrtdkk = ($kosongrtdkk / $kosongkk) * 100;
$tab .= '<tr class=rowcontent>';
$tab .= '<td nowrap align=left>I. Kantor Kebun</td>';
$tab .= '<td align=right>' . number_format($karyawankk) . '</td>';
$tab .= '<td align=right>' . number_format($pintukk) . '</td>';
$tab .= '<td align=right>' . number_format($hunikk) . '</td>';
$tab .= '<td align=right>' . number_format($kosongkk) . '</td>';
$tab .= '<td align=right>' . number_format($hunibbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($phunibbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($hunibtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($phunibtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($hunirbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($phunirbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($hunirtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($phunirtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($kosongbbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($pkosongbbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($kosongbtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($pkosongbtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($kosongrbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($pkosongrbdkk) . '</td>';
$tab .= '<td align=right>' . number_format($kosongrtdkk) . '</td>';
$tab .= '<td align=right>' . number_format($pkosongrtdkk) . '</td>';
$tab .= '</tr>';

if (!empty($divisi)) {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td nowrap align=left>II. Divisi</td>';
	$tab .= '<td colspan=20 align=right></td>';
	$tab .= '</tr>';
}

if (!empty($divisi)) {
	foreach ($divisi as $divs) {
		@$phunibbd[$divs] = ($hunibbd[$divs] / $anuhuni[$divs]) * 100;
		@$phunibtd[$divs] = ($hunibtd[$divs] / $anuhuni[$divs]) * 100;
		@$phunirbd[$divs] = ($hunirbd[$divs] / $anuhuni[$divs]) * 100;
		@$phunirtd[$divs] = ($hunirtd[$divs] / $anuhuni[$divs]) * 100;
		@$pkosongbbd[$divs] = ($kosongbbd[$divs] / $anukosong[$divs]) * 100;
		@$pkosongbtd[$divs] = ($kosongbtd[$divs] / $anukosong[$divs]) * 100;
		@$pkosongrbd[$divs] = ($kosongrbd[$divs] / $anukosong[$divs]) * 100;
		@$pkosongrtd[$divs] = ($kosongrtd[$divs] / $anukosong[$divs]) * 100;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td nowrap align=left>' . $namadivisi[$divs] . '</td>';
		$tab .= '<td align=right>' . number_format($anu[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($pintu[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($anuhuni[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($anukosong[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($hunibbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($phunibbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($hunibtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($phunibtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($hunirbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($phunirbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($hunirtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($phunirtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($kosongbbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($pkosongbbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($kosongbtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($pkosongbtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($kosongrbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($pkosongrbd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($kosongrtd[$divs]) . '</td>';
		$tab .= '<td align=right>' . number_format($pkosongrtd[$divs]) . '</td>';
		$tab .= '</tr>';
	}
}

@$totalphunibbd = ($totalhunibbd / $totalhuni) * 100;
@$totalphunibtd = ($totalhunibtd / $totalhuni) * 100;
@$totalphunirbd = ($totalhunirbd / $totalhuni) * 100;
@$totalphunirtd = ($totalhunirtd / $totalhuni) * 100;
@$totalpkosongbbd = ($totalkosongbbd / $totalkosong) * 100;
@$totalpkosongbtd = ($totalkosongbtd / $totalkosong) * 100;
@$totalpkosongrbd = ($totalkosongrbd / $totalkosong) * 100;
@$totalpkosongrtd = ($totalkosongrtd / $totalkosong) * 100;
$tab .= '<tr class=rowcontent>';
$tab .= '<td nowrap align=left>Total</td>';
$tab .= '<td align=right>' . number_format($totalkaryawan) . '</td>';
$tab .= '<td align=right>' . number_format($totalpintu) . '</td>';
$tab .= '<td align=right>' . number_format($totalhuni) . '</td>';
$tab .= '<td align=right>' . number_format($totalkosong) . '</td>';
$tab .= '<td align=right>' . number_format($totalhunibbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalphunibbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalhunibtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalphunibtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalhunirbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalphunirbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalhunirtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalphunirtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalkosongbbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalpkosongbbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalkosongbtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalpkosongbtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalkosongrbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalpkosongrbd) . '</td>';
$tab .= '<td align=right>' . number_format($totalkosongrtd) . '</td>';
$tab .= '<td align=right>' . number_format($totalpkosongrtd) . '</td>';
$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= '<br>Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.<br><br>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lbm_perumahan_' . $unit . $periode;

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
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;

case 'pdf':
	if (($unit == '') || ($periode == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $unit;
			global $optNm;
			global $optBulan;
			global $tahun;
			global $bulan;
			global $dbname;
			global $luas;
			global $afdId;
			global $w1;
			global $w2;
			global $w3;
			global $w4;
			global $w5;
			global $w6;
			global $w7;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($w1 + $w2 + $w3 + $w4 + $w5, $height, '04.6 PERUMAHAN', NULL, 0, 'L', 1);
			$this->Cell(($w6 + $w7) * 8, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($w1 + $w2 + $w3 + $w4 + $w5 + (($w6 + $w7) * 8), $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);
			$this->Ln();

			if ($afdId != '') {
				$this->Cell($w1 + $w2 + $w3 + $w4 + $w5 + (($w6 + $w7) * 8), $height, $_SESSION['lang']['afdeling'] . ' : ' . $optNm[$afdId] . ' (' . $afdId . ')', NULL, 0, 'L', 1);
			}

			$this->Ln();
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($w1, $height, '', TRL, 0, 'C', 1);
			$this->Cell($w2, $height, '', TRL, 0, 'C', 1);
			$this->Cell($w3, $height, '', TRL, 0, 'C', 1);
			$this->Cell($w4 + $w5, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($w6 + $w7) * 8, $height, $_SESSION['lang']['kondisi'], 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell($w1, $height, $_SESSION['lang']['status'], RL, 0, 'C', 1);
			$this->Cell($w2, $height, $_SESSION['lang']['jumlah'], RL, 0, 'C', 1);
			$this->Cell($w3, $height, $_SESSION['lang']['jumlah'], RL, 0, 'C', 1);
			$this->Cell($w4 + $w5, $height, $_SESSION['lang']['jumlahpintu'], BRL, 0, 'C', 1);
			$this->Cell(($w6 + $w7) * 4, $height, $_SESSION['lang']['dihuni'], 1, 0, 'C', 1);
			$this->Cell(($w6 + $w7) * 4, $height, $_SESSION['lang']['kosong'], 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell($w1, $height, '', RL, 0, 'C', 1);
			$this->Cell($w2, $height, $_SESSION['lang']['karyawan'], RL, 0, 'C', 1);
			$this->Cell($w3, $height, $_SESSION['lang']['pintu'], RL, 0, 'C', 1);
			$this->Cell($w4, $height, $_SESSION['lang']['dihuni'], TRL, 0, 'C', 1);
			$this->Cell($w5, $height, $_SESSION['lang']['kosong'], TRL, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'B-BD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'B-TD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'R-BD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'R-TD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'B-BD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'B-TD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'R-BD', 1, 0, 'C', 1);
			$this->Cell($w6 + $w7, $height, 'R-TD', 1, 0, 'C', 1);
			$this->Ln();
			$this->Cell($w1, $height, '', BRL, 0, 'C', 1);
			$this->Cell($w2, $height, '', BRL, 0, 'C', 1);
			$this->Cell($w3, $height, '', BRL, 0, 'C', 1);
			$this->Cell($w4, $height, '', BRL, 0, 'C', 1);
			$this->Cell($w5, $height, '', BRL, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Cell($w6, $height, $_SESSION['lang']['pintu'], 1, 0, 'C', 1);
			$this->Cell($w7, $height, '%', 1, 0, 'C', 1);
			$this->Ln();
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$w1 = 140;
	$w2 = 50;
	$w3 = 50;
	$w4 = 35;
	$w5 = 35;
	$w6 = 30;
	$w7 = 30;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell($w1, $height, 'I. Kantor Kebun', 1, 0, 'L', 1);
	$pdf->Cell($w2, $height, number_format($karyawankk), 1, 0, 'R', 1);
	$pdf->Cell($w3, $height, number_format($pintukk), 1, 0, 'R', 1);
	$pdf->Cell($w4, $height, number_format($hunikk), 1, 0, 'R', 1);
	$pdf->Cell($w5, $height, number_format($kosongkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($hunibbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($phunibbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($hunibtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($phunibtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($hunirbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($phunirbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($hunirtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($phunirtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($kosongbbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($pkosongbbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($kosongbtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($pkosongbtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($kosongrbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($pkosongrbdkk), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($kosongrtdkk), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($pkosongrtdkk), 1, 0, 'R', 1);
	$pdf->Ln();

	if (!empty($divisi)) {
		$pdf->Cell($w1, $height, 'II. Divisi', 1, 0, 'L', 1);
		$pdf->Cell($w2 + $w3 + $w4 + $w5 + (($w6 + $w7) * 8), $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
	}

	if (!empty($divisi)) {
		foreach ($divisi as $divs) {
			$pdf->Cell($w1, $height, '.' . $namadivisi[$divs] . '.', 1, 0, 'L', 1);
			$pdf->Cell($w2, $height, number_format($anu[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w3, $height, number_format($pintu[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w4, $height, number_format($anuhuni[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w5, $height, number_format($anukosong[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($hunibbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($phunibbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($hunibtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($phunibtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($hunirbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($phunirbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($hunirtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($phunirtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($kosongbbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($pkosongbbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($kosongbtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($pkosongbtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($kosongrbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($pkosongrbd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w6, $height, number_format($kosongrtd[$divs]), 1, 0, 'R', 1);
			$pdf->Cell($w7, $height, number_format($pkosongrtd[$divs]), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell($w1, $height, 'Total', 1, 0, 'L', 1);
	$pdf->Cell($w2, $height, number_format($totalkaryawan), 1, 0, 'R', 1);
	$pdf->Cell($w3, $height, number_format($totalpintu), 1, 0, 'R', 1);
	$pdf->Cell($w4, $height, number_format($totalhuni), 1, 0, 'R', 1);
	$pdf->Cell($w5, $height, number_format($totalkosong), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalhunibbd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalphunibbd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalhunibtd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalphunibtd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalhunirbd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalphunirbd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalhunirtd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalphunirtd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalkosongbbd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalpkosongbbd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalkosongbtd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalpkosongbtd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalkosongrbd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalpkosongrbd), 1, 0, 'R', 1);
	$pdf->Cell($w6, $height, number_format($totalkosongrtd), 1, 0, 'R', 1);
	$pdf->Cell($w7, $height, number_format($totalpkosongrtd), 1, 0, 'R', 1);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell($width, $height, 'Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.', 0, 0, 'L', 1);
	$pdf->Output();
	break;
}

?>
