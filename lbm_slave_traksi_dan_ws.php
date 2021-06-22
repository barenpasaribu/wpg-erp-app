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
$dzArr = array();
$kegiatan = 'SELECT jenisvhc, namajenisvhc FROM ' . $dbname . '.vhc_5jenisvhc order by jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($kegiatan)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']][kode] = $res['jenisvhc'];
	$listvhc[$res['jenisvhc']] = $res['jenisvhc'];
	$kamusvhc[$res['jenisvhc']] = $res['namajenisvhc'];
}

$str = 'SELECT jenisvhc, count(*) as jumlah FROM ' . $dbname . '.vhc_5master ' . "\r\n" . '    WHERE kodetraksi like \'' . $unit . '%\' ' . "\r\n" . '    GROUP BY jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['a'] = $res['jumlah'];
}

$str = 'SELECT a.jenisvhc , count(distinct a.tanggal) as jumlahhari FROM ' . $dbname . '.vhc_runht a' . "\r\n\t" . '  LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '      WHERE kodetraksi like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\' ' . "\r\n" . '      GROUP BY a.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['b'] = $res['jumlahhari'];
}

$str = 'SELECT  a.jenisvhc, count(distinct a.tanggal) as jumlahhari FROM ' . $dbname . '.vhc_runht a' . "\r\n" . ' ' . "\t" . '  LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '      WHERE kodetraksi like \'' . $unit . '%\' and (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\'))' . "\r\n" . '      GROUP BY a.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['c'] = $res['jumlahhari'];
}

$str = 'SELECT b.jenisvhc, sum(a.downtime) as jam, count(*) as freq  FROM ' . $dbname . '.vhc_penggantianht a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and a.tanggal like \'' . $periode . '%\'' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['d'] = $res['jam'];
	$dzArr[$res['jenisvhc']]['e'] = $res['freq'];
}

$str = 'SELECT b.jenisvhc, sum(a.downtime) as jam, count(*) as freq  FROM ' . $dbname . '.vhc_penggantianht a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and (a.tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\'))' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['f'] = $res['jam'];
	$dzArr[$res['jenisvhc']]['g'] = $res['freq'];
}

$str = 'SELECT b.jenisvhc, sum(a.beratmuatan) as muatan, sum(a.jumlah) as hmkm  FROM ' . $dbname . '.vhc_rundt a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_runht c ON a.notransaksi=c.notransaksi' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON b.kodevhc=c.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and c.tanggal like \'' . $periode . '%\'' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['h'] = $res['muatan'];
	$dzArr[$res['jenisvhc']]['j'] = $res['hmkm'];
}

$str = 'SELECT b.jenisvhc, sum(a.beratmuatan) as muatan, sum(a.jumlah) as hmkm  FROM ' . $dbname . '.vhc_rundt a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_runht c ON a.notransaksi=c.notransaksi' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON b.kodevhc=c.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and (c.tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\'))' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$dzArr[$res['jenisvhc']]['i'] = $res['muatan'];
	$dzArr[$res['jenisvhc']]['k'] = $res['hmkm'];
}

$str = 'SELECT a.jenisvhc, sum(a.jlhbbm) as jlhbbm FROM ' . $dbname . '.vhc_runht a ' . "\r\n" . '      left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc' . "\r\n" . '      WHERE kodetraksi like \'' . $unit . '%\' and tanggal like \'' . $periode . '%\'' . "\r\n" . '      GROUP BY a.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	@$dzArr[$res['jenisvhc']]['l'] = $res['jlhbbm'] / $dzArr[$res['jenisvhc']]['j'];
}

$str = 'SELECT a.jenisvhc, sum(a.jlhbbm) as jlhbbm FROM ' . $dbname . '.vhc_runht a' . "\r\n" . '      left join ' . $dbname . '.vhc_5master b on a.kodevhc=b.kodevhc' . "\r\n" . '      WHERE kodetraksi like \'' . $unit . '%\' and (tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\'))' . "\r\n" . '      GROUP BY a.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	@$dzArr[$res['jenisvhc']]['m'] = $res['jlhbbm'] / $dzArr[$res['jenisvhc']]['k'];
}

$strh = 'select distinct noakundebet,sampaidebet  from ' . $dbname . '.keu_5parameterjurnal where  jurnalid=\'LPVHC\'';
$resh = mysql_query($strh);

while ($barh = mysql_fetch_object($resh)) {
	$akunkdari = $barh->noakundebet;
	$akunksampai = $barh->sampaidebet;
}

$str = 'SELECT b.jenisvhc, sum(a.debet) as jumlah FROM ' . $dbname . '.keu_jurnaldt_vw a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and a.tanggal like \'' . $periode . '%\' and a.kodevhc is not null and a.kodevhc != \'\'' . "\r\n" . '        and (a.noakun between \'' . $akunkdari . '\' and \'' . $akunksampai . '\') and a.noreferensi not like \'%ALK_KERJA_AB%\'' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	@$dzArr[$res['jenisvhc']]['n'] = $res['jumlah'] / $dzArr[$res['jenisvhc']]['j'];
}

$str = 'SELECT b.jenisvhc, sum(a.debet) as jumlah FROM ' . $dbname . '.keu_jurnaldt_vw a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.vhc_5master b ON a.kodevhc=b.kodevhc' . "\r\n" . '    WHERE b.kodetraksi like \'' . $unit . '%\' and (a.tanggal between \'' . $tahun . '-01-01\' and LAST_DAY(\'' . $periode . '-15\')) ' . "\r\n" . '        and a.kodevhc is not null and a.kodevhc != \'\'' . "\r\n" . '        and (a.noakun between \'' . $akunkdari . '\' and \'' . $akunksampai . '\') and a.noreferensi not like \'%ALK_KERJA_AB%\'' . "\r\n" . '    GROUP BY b.jenisvhc';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	@$dzArr[$res['jenisvhc']]['o'] = $res['jumlah'] / $dzArr[$res['jenisvhc']]['k'];
}

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=12 align=left><font size=3>25.1 TRAKSI DAN WORKSHOP - KENDARAAN, ALAT BERAT DAN MESIN PENUNJANG</font></td>' . "\r\n" . '        <td colspan=4 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr> ' . "\r\n" . '     <tr><td colspan=16 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')</td></tr>   ' . "\r\n" . '</table>';
}
else {
	$bg = '';
	$brdr = 0;
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=4 ' . $bg . '>' . $_SESSION['lang']['jenis'] . ' ' . $_SESSION['lang']['kendaraan'] . '</td>' . "\r\n" . '    <td align=center rowspan=4 ' . $bg . '>' . $_SESSION['lang']['jumlah'] . ' ' . $_SESSION['lang']['unit'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=2 ' . $bg . '>' . $_SESSION['lang']['hk'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=4 ' . $bg . '>' . $_SESSION['lang']['pemeliharaan'] . '</td>' . "\r\n" . '    <td align=center colspan=4 ' . $bg . '>Produktifitas/Unit</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=2 ' . $bg . '>Konsumsi BBM (MH-KM-Jam/Liter)</td>' . "\r\n" . '    <td align=center rowspan=2 colspan=2 ' . $bg . '>' . $_SESSION['lang']['biaya'] . ' (Rp./HM-KM-Jam)</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>Ton</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>HM-KM-Jam</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>Jam</td>' . "\r\n" . '    <td align=center ' . $bg . '>Freq.</td>' . "\r\n" . '    <td align=center ' . $bg . '>Jam</td>' . "\r\n" . '    <td align=center ' . $bg . '>Freq.</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
$dummy = '';

if (empty($dzArr)) {
	$tab .= '<tr class=rowcontent><td colspan=16>Data Empty.<td></tr>';
}
else if (!empty($listvhc)) {
	foreach ($listvhc as $keg) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $kamusvhc[$keg] . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['a'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['b'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['c'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['d'], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['e'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['f'], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['g'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['h'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['i'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['j'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['k'], 0) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['l'], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['m'], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['n'], 2) . '</td>';
		$tab .= '<td align=right>' . numberformat($dzArr[$keg]['o'], 2) . '</td>';
		$tab .= '</tr>';
	}
}

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

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lbm_traksi_workshop_' . $unit . $periode;

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
			global $wkiri;
			global $wlain;
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width * 0.75, $height, '25.1 TRAKSI DAN WORKSHOP - KENDARAAN, ALAT BERAT DAN MESIN PENUNJANG', NULL, 0, 'L', 1);
			$this->Cell($width * 0.25, $height, $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun, NULL, 0, 'R', 1);
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$unit] . ' (' . $unit . ')', NULL, 0, 'L', 1);
			$this->Ln();
			$this->Ln();
			$height = 15;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(($wkiri / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, '', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, 'Produktifitas/Unit', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Konsumsi BBM', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Cost/Unit', TRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['jenis'], RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['jumlah'], RL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['hk'], BRL, 0, 'C', 1);
			$this->Cell((($wlain * 4) / 100) * $width, $height, $_SESSION['lang']['perawatan'], BRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'Ton', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'HM-KM-Jam', 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '(HM-KM-Jam/Liter)', BRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, '(Rp./HM-KM-Jam)', BRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, $_SESSION['lang']['kendaraan'], RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['unit'], RL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', TRL, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, $_SESSION['lang']['bulanini'], 1, 0, 'C', 1);
			$this->Cell((($wlain * 2) / 100) * $width, $height, 'sd BI', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, $_SESSION['lang']['bulanini'], TRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'sd BI', TRL, 0, 'C', 1);
			$this->Ln();
			$this->Cell(($wkiri / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Jam', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Freq', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Jam', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, 'Freq', 1, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Cell(($wlain / 100) * $width, $height, '', BRL, 0, 'C', 1);
			$this->Ln();
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$cols = 247.5;
	$wkiri = 10;
	$wlain = 6;
	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);

	if (!empty($listvhc)) {
		foreach ($listvhc as $keg) {
			$pdf->Cell(($wkiri / 100) * $width, $height, $kamusvhc[$keg], 1, 0, 'L', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['a'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['b'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['c'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['d'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['e'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['f'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['g'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['h'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['i'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['j'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['k'], 0), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['l'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['m'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['n'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($wlain / 100) * $width, $height, numberformat($dzArr[$keg]['o'], 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}
	}

	$pdf->Output();
	break;
}

?>
