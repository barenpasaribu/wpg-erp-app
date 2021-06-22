<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['idPt'] == '' ? $idPt = $_GET['idPt'] : $idPt = $_POST['idPt'];
$_POST['klmpkBrg'] == '' ? $klmpkBrg = $_GET['klmpkBrg'] : $klmpkBrg = $_POST['klmpkBrg'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$_POST['pt'] == '' ? $pt = $_GET['pt'] : $pt = $_POST['pt'];
$_POST['periodeDt'] == '' ? $periodeDt = $_GET['periodeDt'] : $periodeDt = $_POST['periodeDt'];

if ($pt != '') {
	$idPt = $pt;
}

$dtNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$dtSat = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKlmpk = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');

if ($periode == '') {
	exit('Error:Field Tidak Boleh Kosong');
}

$arr = '##periode##judul';
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
$sData = 'select distinct sum(jumlahpesan*hargasatuan*kurs) as total,substr(tanggal,6,2) as bulan,' . "\r\n" . '        substr(kodebarang,1,3) as klmpkBrg,sum(jumlahpesan) as jumlah from ' . $dbname . '.log_po_vw where kodeorg=\'' . $idPt . '\'' . "\r\n" . '        and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'' . "\r\n" . '        group by substr(tanggal,6,2),substr(kodebarang,1,3) order by substr(kodebarang,1,3) asc';

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$dtBarang[$rData['klmpkBrg']] = $rData['klmpkBrg'];
	$dtHarga[$rData['klmpkBrg'] . $rData['bulan']] = $rData['total'];
	$dtJumlah[$rData['klmpkBrg'] . $rData['bulan']] = $rData['jumlah'];
	$dtPeriode[$rData['bulan']] = $rData['bulan'];
}

$cekDt = count($dtBarang);

if ($cekDt == 0) {
	exit('Error:Data Kosong');
}

$sKd = 'select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $idPt . '\'';

#exit(mysql_error($conn));
($qKd = mysql_query($sKd)) || true;

while ($rKd = mysql_fetch_assoc($qKd)) {
	$aro += 1;

	if ($aro == 1) {
		$kodear = '\'' . $rKd['kodeorganisasi'] . '\'';
	}
	else {
		$kodear .= ',\'' . $rKd['kodeorganisasi'] . '\'';
	}
}

$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'sum(rp0' . $W . ') as rp0' . $W . ',sum(fis0' . $W . ') as fis0' . $W . ',';
	}
	else {
		$jack = ',sum(rp' . $W . ') as rp' . $W . ',sum(fis' . $W . ') as fis' . $W . ',';
	}

	if ($W < intval($bulan)) {
		$addstr .= $jack;
	}
	else {
		$addstr .= $jack;
	}

	++$W;
}

$sBudget = 'select distinct ' . $addstr . 'substr(kodebarang,1,3) as klmpkBrg from ' . "\r\n" . '          ' . $dbname . '.bgt_budget_detail where substr(kodeorg,1,4) in (' . $kodear . ')' . "\r\n" . '          and substr(kodebudget,1,1)=\'M\' and tahunbudget=\'' . $tahun . '\'' . "\r\n" . '          group by substr(kodebarang,1,3)';

#exit(mysql_error($conn));
($qBudget = mysql_query($sBudget)) || true;

while ($rBudget = mysql_fetch_assoc($qBudget)) {
	$dtBarang[$rBudget['klmpkBrg']] = $rBudget['klmpkBrg'];
	$W = 1;

	while ($W <= intval($bulan)) {
		if ($W < 10) {
			$adr = '0' . $W;
		}

		$dtRupBgt += $rBudget['klmpkBrg'] . $adr;
		$dtFisBgt += $rBudget['klmpkBrg'] . $adr;
		++$W;
	}
}

$bg = '';
$brdr = 0;

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr>    ' . "\r\n" . '</table>';
}

if ($proses != 'excel') {
	$tab .= $judul;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>No.</td>' . "\r\n" . '    <td align=center rowspan=3 ' . $bg . '>' . $_SESSION['lang']['kelompokbarang'] . '</td>';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$adr = '0' . $W;
	}

	$tab .= '<td align=center  colspan=2 ' . $bg . '>' . $optBulan[$adr] . ' (Rp.)</td>';
	++$W;
}

$tab .= '</tr><tr>';
$W = 1;

while ($W <= intval($bulan)) {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>';
	++$W;
}

$tab .= '</tr>';
$tab .= '</thead><tbody>';

foreach ($dtBarang as $lstKlmpk) {
	$arto += 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $arto . '</td>';
	$tab .= '<td>' . $optNmKlmpk[$lstKlmpk] . '</td>';
	$W = 1;

	while ($W <= intval($bulan)) {
		if ($W < 10) {
			$adr = '0' . $W;
		}

		$tab .= '<td align=right ' . $bg . ' style=cursor:pointer onclick=getDetPt(\'lbm_slave_proc_brg_pt\',\'' . $lstKlmpk . '\',\'' . $tahun . '-' . $adr . '\',\'' . $idPt . '\',\'' . $periode . '\')>' . number_format($dtHarga[$lstKlmpk . $adr], 0) . '</td>';
		$tab .= '<td align=right ' . $bg . ' style=cursor:pointer onclick=getDetPt(\'lbm_slave_proc_brg_pt\',\'' . $lstKlmpk . '\',\'' . $tahun . '-' . $adr . '\',\'' . $idPt . '\',\'' . $periode . '\'>' . number_format($dtRupBgt[$lstKlmpk . $adr], 0) . '</td>';
		++$W;
	}

	$tab .= '</tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'totalPembelian_pt_' . $dte;

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

case 'getDetail':
	$lstMatauang2 = '';
	$drot = explode('-', $periodeDt);
	$bln = $drot[1];
	$W = 1;

	while ($W <= intval($bln)) {
		if ($W < 10) {
			$jack = 'sum(rp0' . $W . ') as rp0' . $W . ',sum(fis0' . $W . ') as fis0' . $W . ',';
		}
		else {
			$jack = ',sum(rp' . $W . ') as rp' . $W . ',sum(fis' . $W . ') as fis' . $W . ',';
		}

		if ($W < intval($bln)) {
			$addstr .= $jack;
		}
		else {
			$addstr .= $jack;
		}

		++$W;
	}

	$jrt = 1;

	while ($jrt <= intval($bulan)) {
		if ($jrt < 10) {
			$jack = 'sum(rp0' . $jrt . ') as rp0' . $jrt . ',sum(fis0' . $jrt . ') as fis0' . $jrt . ',';
		}
		else {
			$jack = ',sum(rp' . $jrt . ') as rp' . $jrt . ',sum(fis' . $jrt . ') as fis' . $jrt . ',';
		}

		if ($jrt < intval($bulan)) {
			$addstr2 .= $jack;
		}
		else {
			$addstr2 .= $jack;
		}

		++$jrt;
	}

	$judul = 'Detail Total Pembelian Barang per PT';
	$sData = 'select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,' . "\r\n" . '            namabarang,satuan from ' . $dbname . '.log_po_vw where substr(kodebarang,1,3)=\'' . $klmpkBrg . '\' and kodeorg=\'' . $idPt . '\'' . "\r\n" . '            and substr(tanggal,1,7)=\'' . $periodeDt . '\' group by substr(nopp,16,4),kodebarang';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtUnit[$rData['unit']] = $rData['unit'];
		$dtRp[$rData['kodebarang'] . $rData['unit']] = $rData['total'];
		$dtJuml[$rData['kodebarang'] . $rData['unit']] = $rData['jumlah'];
		$dtBrg[$rData['kodebarang']] = $rData['kodebarang'];
	}

	$sData = 'select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,' . "\r\n" . '            namabarang,satuan from ' . $dbname . '.log_po_vw where substr(kodebarang,1,3)=\'' . $klmpkBrg . '\' and kodeorg=\'' . $idPt . '\'' . "\r\n" . '            and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' group by substr(nopp,16,4),kodebarang';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtUnitSmp[$rData['unit']] = $rData['unit'];
		$dtRpSblnSmp[$rData['kodebarang'] . $rData['unit']] = $rData['total'];
		$dtJumlSmp[$rData['kodebarang'] . $rData['unit']] = $rData['jumlah'];
		$dtBrg[$rData['kodebarang']] = $rData['kodebarang'];
	}

	foreach ($dtUnit as $lstUnit) {
		$sBudget = 'select distinct ' . $addstr . 'kodebarang,satuanj,kodeorg from ' . "\r\n" . '                  ' . $dbname . '.bgt_budget_detail where substr(kodeorg,1,4)=\'' . $lstUnit . '\'' . "\r\n" . '                  and substr(kodebudget,1,1)=\'M\' and tahunbudget=\'' . $tahun . '\'' . "\r\n" . '                  group by kodebarang';

		#exit(mysql_error($conn));
		($qBudget = mysql_query($sBudget)) || true;

		while ($rBudget = mysql_fetch_assoc($qBudget)) {
			$dtBrg[$rBudget['kodebarang']] = $rBudget['kodebarang'];
			$dtSatuanBgt[$rBudget['kodebarang'] . $lstUnit] = $rBudget['satuanj'];
			$W = 1;

			while ($W <= intval($bln)) {
				if ($W < 10) {
					$adr = '0' . $W;
				}

				$dtRupBgt += $rBudget['kodebarang'] . $lstUnit;
				$dtFisBgt += $rBudget['kodebarang'] . $lstUnit;
				++$W;
			}
		}
	}

	foreach ($dtUnit as $lstUnit) {
		$sBudget = 'select distinct ' . $addstr2 . 'kodebarang,satuanj,kodeorg from ' . "\r\n" . '                  ' . $dbname . '.bgt_budget_detail where substr(kodeorg,1,4)=\'' . $lstUnit . '\'' . "\r\n" . '                  and substr(kodebudget,1,1)=\'M\' and tahunbudget=\'' . $tahun . '\'' . "\r\n" . '                  group by kodebarang';

		#exit(mysql_error($conn));
		($qBudget = mysql_query($sBudget)) || true;

		while ($rBudget = mysql_fetch_assoc($qBudget)) {
			$dtBrg[$rBudget['kodebarang']] = $rBudget['kodebarang'];
			$dtSatuanBgt[$rBudget['kodebarang'] . $lstUnit] = $rBudget['satuanj'];
			$W = 1;

			while ($W <= intval($bln)) {
				if ($W < 10) {
					$adr = '0' . $W;
				}

				$dtRupBgt += $rBudget['kodebarang'] . $lstUnit;
				$dtFisBgt += $rBudget['kodebarang'] . $lstUnit;
				++$W;
			}
		}
	}

	$dcol = (count($dtUnit) * 6) + 2;
	$cold = 3 * 2;
	$tabc = '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tabc .= '<tr><td rowspan=4  bgcolor=#DEDEDE>No.</td><td rowspan=4  bgcolor=#DEDEDE>' . $_SESSION['lang']['namabarang'] . '</td>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td colspan=' . $cold . '>' . $lstUnit . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $periodeDt . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $periode . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $_SESSION['lang']['realisasi'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $_SESSION['lang']['anggaran'] . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['fisik'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['rp'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['fisik'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['rp'] . '</td>';
	}

	$tabc .= '</tr><tbody>';

	foreach ($dtBrg as $lstbarang) {
		$artp += 1;
		$tabc .= '<tr class=rowcontent><td>' . $artp . '</td>';
		$tabc .= '<td>' . $dtNmBrg[$lstbarang] . '</td>';

		foreach ($dtUnit as $lstUnit) {
			$tabc .= '<td align=right>' . number_format($dtJuml[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=left>' . $dtSat[$lstbarang] . '</td>';
			$tabc .= '<td align=right>' . number_format($dtRp[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=right>' . number_format($dtFisBgt[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=left>' . $dtSat[$lstbarang] . '</td>';
			$tabc .= '<td align=right>' . number_format($dtRupBgt[$lstbarang . $lstUnit], 0) . '</td>';
		}

		$tabc .= '</tr>';
	}

	$tabc .= '<tr><td colspan=' . $dcol . '>';
	$tabc .= '<button class=mybutton onclick=zBack()>Back</button>';
	$tabc .= '<button onclick="zExcel3(event,\'lbm_slave_proc_brg_pt.php\',\'' . $idPt . '\',\'' . $klmpkBrg . '\',\'' . $periode . '\',\'' . $periodeDt . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . "\r\n" . '               ' . $_SESSION['lang']['excel'] . '</button></td></tr> ';
	$tabc .= '</tbody></table>';
	echo $tabc . '###' . $judul;
	break;

case 'getDetPtEx':
	$lstMatauang2 = '';
	$drot = explode('-', $periodeDt);
	$bln = $drot[1];
	$W = 1;

	while ($W <= intval($bln)) {
		if ($W < 10) {
			$jack = 'sum(rp0' . $W . ') as rp0' . $W . ',sum(fis0' . $W . ') as fis0' . $W . ',';
		}
		else {
			$jack = ',sum(rp' . $W . ') as rp' . $W . ',sum(fis' . $W . ') as fis' . $W . ',';
		}

		if ($W < intval($bln)) {
			$addstr .= $jack;
		}
		else {
			$addstr .= $jack;
		}

		++$W;
	}

	$jrt = 1;

	while ($jrt <= intval($bulan)) {
		if ($jrt < 10) {
			$jack = 'sum(rp0' . $jrt . ') as rp0' . $jrt . ',sum(fis0' . $jrt . ') as fis0' . $jrt . ',';
		}
		else {
			$jack = ',sum(rp' . $jrt . ') as rp' . $jrt . ',sum(fis' . $jrt . ') as fis' . $jrt . ',';
		}

		if ($jrt < intval($bulan)) {
			$addstr2 .= $jack;
		}
		else {
			$addstr2 .= $jack;
		}

		++$jrt;
	}

	$judul = 'Detail Total Pembelian Barang per PT';
	$sData = 'select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,' . "\r\n" . '            namabarang,satuan from ' . $dbname . '.log_po_vw where substr(kodebarang,1,3)=\'' . $klmpkBrg . '\' and kodeorg=\'' . $idPt . '\'' . "\r\n" . '            and substr(tanggal,1,7)=\'' . $periodeDt . '\' group by substr(nopp,16,4),kodebarang';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtUnit[$rData['unit']] = $rData['unit'];
		$dtRp[$rData['kodebarang'] . $rData['unit']] = $rData['total'];
		$dtJuml[$rData['kodebarang'] . $rData['unit']] = $rData['jumlah'];
		$dtBrg[$rData['kodebarang']] = $rData['kodebarang'];
	}

	$sData = 'select distinct sum(hargasatuan*jumlahpesan*kurs) as total,kodebarang,sum(jumlahpesan) as jumlah,substr(nopp,16,4) as unit,' . "\r\n" . '            namabarang,satuan from ' . $dbname . '.log_po_vw where substr(kodebarang,1,3)=\'' . $klmpkBrg . '\' and kodeorg=\'' . $idPt . '\'' . "\r\n" . '            and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' group by substr(nopp,16,4),kodebarang';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtUnitSmp[$rData['unit']] = $rData['unit'];
		$dtRpSblnSmp[$rData['kodebarang'] . $rData['unit']] = $rData['total'];
		$dtJumlSmp[$rData['kodebarang'] . $rData['unit']] = $rData['jumlah'];
		$dtBrg[$rData['kodebarang']] = $rData['kodebarang'];
	}

	foreach ($dtUnit as $lstUnit) {
		$sBudget = 'select distinct ' . $addstr . 'kodebarang,satuanj,kodeorg from ' . "\r\n" . '                  ' . $dbname . '.bgt_budget_detail where substr(kodeorg,1,4)=\'' . $lstUnit . '\'' . "\r\n" . '                  and substr(kodebudget,1,1)=\'M\' and tahunbudget=\'' . $tahun . '\'' . "\r\n" . '                  group by kodebarang';

		#exit(mysql_error($conn));
		($qBudget = mysql_query($sBudget)) || true;

		while ($rBudget = mysql_fetch_assoc($qBudget)) {
			$dtBrg[$rBudget['kodebarang']] = $rBudget['kodebarang'];
			$dtSatuanBgt[$rBudget['kodebarang'] . $lstUnit] = $rBudget['satuanj'];
			$W = 1;

			while ($W <= intval($bln)) {
				if ($W < 10) {
					$adr = '0' . $W;
				}

				$dtRupBgt += $rBudget['kodebarang'] . $lstUnit;
				$dtFisBgt += $rBudget['kodebarang'] . $lstUnit;
				++$W;
			}
		}
	}

	foreach ($dtUnit as $lstUnit) {
		$sBudget = 'select distinct ' . $addstr2 . 'kodebarang,satuanj,kodeorg from ' . "\r\n" . '                  ' . $dbname . '.bgt_budget_detail where substr(kodeorg,1,4)=\'' . $lstUnit . '\'' . "\r\n" . '                  and substr(kodebudget,1,1)=\'M\' and tahunbudget=\'' . $tahun . '\'' . "\r\n" . '                  group by kodebarang';

		#exit(mysql_error($conn));
		($qBudget = mysql_query($sBudget)) || true;

		while ($rBudget = mysql_fetch_assoc($qBudget)) {
			$dtBrg[$rBudget['kodebarang']] = $rBudget['kodebarang'];
			$dtSatuanBgt[$rBudget['kodebarang'] . $lstUnit] = $rBudget['satuanj'];
			$W = 1;

			while ($W <= intval($bln)) {
				if ($W < 10) {
					$adr = '0' . $W;
				}

				$dtRupBgt += $rBudget['kodebarang'] . $lstUnit;
				$dtFisBgt += $rBudget['kodebarang'] . $lstUnit;
				++$W;
			}
		}
	}

	$dcol = (count($dtUnit) * 6) + 2;
	$cold = 3 * 2;
	$tabc = '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
	$tabc .= '<tr><td rowspan=4  bgcolor=#DEDEDE>No.</td><td rowspan=4  bgcolor=#DEDEDE>' . $_SESSION['lang']['namabarang'] . '</td>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td colspan=' . $cold . '  bgcolor=#DEDEDE align=center>' . $lstUnit . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $periodeDt . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $periode . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $_SESSION['lang']['realisasi'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE colspan=3>' . $_SESSION['lang']['anggaran'] . '</td>';
	}

	$tabc .= '</tr><tr>';

	foreach ($dtUnit as $lstUnit) {
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['fisik'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['rp'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['fisik'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';
		$tabc .= '<td bgcolor=#DEDEDE>' . $_SESSION['lang']['rp'] . '</td>';
	}

	$tabc .= '</tr><tbody>';

	foreach ($dtBrg as $lstbarang) {
		$artp += 1;
		$tabc .= '<tr class=rowcontent><td>' . $artp . '</td>';
		$tabc .= '<td>' . $dtNmBrg[$lstbarang] . '</td>';

		foreach ($dtUnit as $lstUnit) {
			$tabc .= '<td align=right>' . number_format($dtJuml[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=left>' . $dtSat[$lstbarang] . '</td>';
			$tabc .= '<td align=right>' . number_format($dtRp[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=right>' . number_format($dtFisBgt[$lstbarang . $lstUnit], 0) . '</td>';
			$tabc .= '<td align=left>' . $dtSat[$lstbarang] . '</td>';
			$tabc .= '<td align=right>' . number_format($dtRupBgt[$lstbarang . $lstUnit], 0) . '</td>';
		}

		$tabc .= '</tr>';
	}

	$tabc .= '</tbody></table>';
	$tabc .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'dettotalPembelian_pt_' . $dte;

	if (0 < strlen($tabc)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tabc)) {
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;
}

?>
