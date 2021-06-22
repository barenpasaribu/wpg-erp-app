<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['tipe'] == '' ? $tipe = $_GET['tipe'] : $tipe = $_POST['tipe'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['kdPt'] == '' ? $kdPt = $_GET['kdPt'] : $kdPt = $_POST['kdPt'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['klmpkbrg'] == '' ? $klmpkbrg = $_GET['klmpkbrg'] : $klmpkbrg = $_POST['klmpkbrg'];
$_POST['regDt'] == '' ? $regDt = $_GET['regDt'] : $regDt = $_POST['regDt'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
strlen($bulan) < 1 ? $bln = '0' . $bulan : $bln = $bulan;
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNamaBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKlmpbrg = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$arr = '##periode##judul##kdPt';
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

if ($periode == '') {
	exit('Error:Field Tidak Boleh Kosong adasd');
}

if ($regDt != '') {
	$whrtd = 'regional=\'' . $regDt . '\'';

	if ($regDt == 'SUMSEL') {
		$whrtd = ' regional in (\'SUMSEL\',\'LAMPUNG\')';
	}

	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where ' . $whrtd . '';
}
else {
	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment order by kodeunit';
}

$arte = '';
$ader = 0;

#exit(mysql_error($conn));
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$ader += 1;

	if ($ader == 1) {
		$arte .= '\'' . $rUnit['kodeunit'] . '\'';
	}
	else {
		$arte .= ',\'' . $rUnit['kodeunit'] . '\'';
	}
}

$whrbgt = ' and substr(kodeorg,1,4) in (' . $arte . ')';
$whrKapt = ' and substr(kodeunit,1,4) in (' . $arte . ')';
$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in (' . $arte . ')';

#exit(mysql_error($conn));
($qPt = mysql_query($sPt)) || true;

while ($rPt = mysql_fetch_assoc($qPt)) {
	$ert += 1;

	if ($ert == 1) {
		$dtPete .= '\'' . $rPt['induk'] . '\'';
	}
	else {
		$dtPete .= ',\'' . $rPt['induk'] . '\'';
	}
}

$whr = ' and kodeorg in (' . $dtPete . ')';

if ($kdPt != '') {
	$whr = ' and kodeorg=\'' . $kdPt . '\'';
	$sBgt = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$ater += 1;

		if ($ater == 1) {
			$aretd = '\'' . $rBgt['kodeorganisasi'] . '\'';
		}
		else {
			$aretd .= ',\'' . $rBgt['kodeorganisasi'] . '\'';
		}
	}

	$whrbgt = ' and substr(kodeorg,1,4) in (' . $aretd . ')';
	$whrKapt = ' and substr(kodeunit,1,4) in (' . $aretd . ')';
}

$sKap = 'select distinct kodetipe,namatipe,kelompokbarang from ' . $dbname . '.sdm_5tipeasset order by kodetipe';

#exit(mysql_error($conn));
($qKap = mysql_query($sKap)) || true;

while ($rKap = mysql_fetch_assoc($qKap)) {
	$dtTipe[$rKap['kodetipe']] = $rKap['kodetipe'];
	$dtBrg[$rKap['kodetipe']] = $rKap['kelompokbarang'];
	$dtNmTipe[$rKap['kodetipe']] = $rKap['namatipe'];
}

$sReal = 'select distinct sum(jumlahpesan) as total,matauang,substr(kodebarang,1,3) as klmpokBrg' . "\r\n" . '        from ' . $dbname . '.log_po_vw where ' . "\r\n" . '        substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset order by kodetipe)' . "\r\n" . '        and tanggal like \'' . $periode . '%\' ' . $whr . ' group by substr(kodebarang,1,3)';

#exit(mysql_error($conn));
($qReal = mysql_query($sReal)) || true;

while ($rReal = mysql_fetch_assoc($qReal)) {
	$totKapi += $rReal['klmpokBrg'];
	$mtUang[$rReal['matauang']] = $rReal['matauang'];
}

$sReal = 'select distinct sum(jumlahpesan) as total,matauang,substr(kodebarang,1,3) as klmpokBrg' . "\r\n" . '        from ' . $dbname . '.log_po_vw where ' . "\r\n" . '        substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset order by kodetipe)' . "\r\n" . '        and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'  ' . $whr . ' group by substr(kodebarang,1,3)';

#exit(mysql_error($conn));
($qReal = mysql_query($sReal)) || true;

while ($rReal = mysql_fetch_assoc($qReal)) {
	$totKapiSmp += $rReal['klmpokBrg'];
	$mtUang[$rReal['matauang']] = $rReal['matauang'];
}

$sKap = 'select distinct sum(k' . $bln . ') as rup,jeniskapital from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' ' . $whrKapt . ' group by jeniskapital';

#exit(mysql_error($conn));
($qKap = mysql_query($sKap)) || true;

while ($rKap = mysql_fetch_assoc($qKap)) {
	$dtKapBln[$rKap['jeniskapital']] = 0;
}

$addstr = '(';
$W = 1;

while ($W <= intval($bulan)) {
	if ($W < 10) {
		$jack = 'k0' . $W;
	}
	else {
		$jack = 'k' . $W;
	}

	if ($W < intval($bulan)) {
		$addstr .= $jack . '+';
	}
	else {
		$addstr .= $jack;
	}

	++$W;
}

$addstr .= ')';
$sKap = 'select distinct sum(' . $addstr . ') as rup,jeniskapital from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' ' . $whrKapt . ' group by jeniskapital';

#exit(mysql_error($conn));
($qKap = mysql_query($sKap)) || true;

while ($rKap = mysql_fetch_assoc($qKap)) {
	$dtKapSmpBln[$rKap['jeniskapital']] = 0;
}

$sYkap = 'select distinct sum(harga) as rup,jeniskapital from ' . "\r\n" . '        ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' ' . $whrKapt . ' group by jeniskapital';

#exit(mysql_error($conn));
($qYkap = mysql_query($sYkap)) || true;

while ($rYkap = mysql_fetch_assoc($qYkap)) {
	$dtKapThn[$rYkap['jeniskapital']] = 0;
}

$bg = '';
$brdr = 0;

if ($proses == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr>    ' . "\r\n" . '</table>';
}

switch ($proses) {
case 'getDetailKap':
	$tab .= $judul;
	$tab .= '<input type=hidden id=periodeDet value=\'' . $periode . '\' /><table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['jnsKapital'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['kapital'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>ANNUAL BUDGET</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n" . '    ';

	foreach ($dtTipe as $kdTipe) {
		$klmpBrg = $dtBrg[$kdTipe];
		$tab .= '<tr class=rowcontent style=\'cursor:pointer;\' onclick=getDetBrgKap2(\'' . $kdTipe . '\',\'' . $arr . '##regDt\')>';
		$tab .= '<td>' . $kdTipe . '</td>';
		$tab .= '<td>' . $dtNmTipe[$kdTipe] . '</td>';
		$tab .= '<td align=right>' . number_format($totKapi[$klmpBrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapBln[$kdTipe], 0) . '</td>';
		@$prsen[$kdTipe] = ($totKapi[$klmpBrg] / $dtKapBln[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsen[$kdTipe], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($totKapiSmp[$klmpBrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapSmpBln[$kdTipe], 0) . '</td>';
		@$prsenBln[$kdTipe] = ($totKapiSmp[$klmpBrg] / $dtKapSmpBln[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsenBln[$kdTipe], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapThn[$kdTipe], 0) . '</td>';
		@$prsenAnn[$kdTipe] = ($totKapiSmp[$klmpBrg] / $dtKapThn[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsenAnn[$kdTipe], 0) . '</td>';
		$tab .= '</tr>';
		$totRealisasi += $totKapi[$klmpBrg];
		$totBudget += $dtKapBln[$kdTipe];
		$totBlnReal += $totKapiSmp[$klmpBrg];
		$totBlnBgt += $dtKapSmpBln[$kdTipe];
		$totAnn += $dtKapThn[$kdTipe];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totRealisasi, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBudget, 0) . '</td>';
	@$prsenDt = ($totRealisasi / $totBudget) * 100;
	$tab .= '<td align=right>' . number_format($prsenDt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBlnReal, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBlnBgt, 0) . '</td>';
	@$prsenBlnDt = ($totBlnReal / $totBlnBgt) * 100;
	$tab .= '<td align=right>' . number_format($prsenBlnDt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totAnn, 0) . '</td>';
	@$prsenAnnDt = ($totBlnReal / $totAnn) * 100;
	$tab .= '<td align=right>' . number_format($prsenAnnDt, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '<tr><td colspan=10>' . "\r\n" . '           <button onclick="zBack()" class="mybutton">Back</button>' . "\r\n" . '           <button onclick="zExcel(event,\'log_slave_proc_brg_detail_kap2.php\',\'' . $arr . '\',\'reportcontainer1\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>' . "\r\n" . '           </td></tr>';
	$tab .= '</tbody></table>';
	echo $tab . '###' . $judul;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosongv ads');
	}

	$tab .= '<input type=hidden id=periodeDet value=\'' . $periode . '\' /><table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['jnsKapital'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>' . $_SESSION['lang']['kapital'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>ANNUAL BUDGET</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n" . '    ';

	foreach ($dtTipe as $kdTipe) {
		$klmpBrg = $dtBrg[$kdTipe];
		$tab .= '<tr class=rowcontent style=\'cursor:pointer;\' onclick=getDetBrg(\'' . $kdTipe . '\',\'' . $arr . '\')>';
		$tab .= '<td>' . $kdTipe . '</td>';
		$tab .= '<td>' . $dtNmTipe[$kdTipe] . '</td>';
		$tab .= '<td align=right>' . number_format($totKapi[$klmpBrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapBln[$kdTipe], 0) . '</td>';
		@$prsen[$kdTipe] = ($totKapi[$klmpBrg] / $dtKapBln[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsen[$kdTipe], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($totKapiSmp[$klmpBrg], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapSmpBln[$kdTipe], 0) . '</td>';
		@$prsenBln[$kdTipe] = ($totKapiSmp[$klmpBrg] / $dtKapSmpBln[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsenBln[$kdTipe], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($dtKapThn[$kdTipe], 0) . '</td>';
		@$prsenAnn[$kdTipe] = ($totKapiSmp[$klmpBrg] / $dtKapThn[$kdTipe]) * 100;
		$tab .= '<td align=right>' . number_format($prsenAnn[$kdTipe], 0) . '</td>';
		$tab .= '</tr>';
		$totRealisasi += $totKapi[$klmpBrg];
		$totBudget += $dtKapBln[$kdTipe];
		$totBlnReal += $totKapiSmp[$klmpBrg];
		$totBlnBgt += $dtKapSmpBln[$kdTipe];
		$totAnn += $dtKapThn[$kdTipe];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totRealisasi, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBudget, 0) . '</td>';
	@$prsenDt = ($totRealisasi / $totBudget) * 100;
	$tab .= '<td align=right>' . number_format($prsenDt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBlnReal, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totBlnBgt, 0) . '</td>';
	@$prsenBlnDt = ($totBlnReal / $totBlnBgt) * 100;
	$tab .= '<td align=right>' . number_format($prsenBlnDt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totAnn, 0) . '</td>';
	@$prsenAnnDt = ($totBlnReal / $totAnn) * 100;
	$tab .= '<td align=right>' . number_format($prsenAnnDt, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$nop_ = 'detailKapitalFis';

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

case 'getDetBrgKap':
	$sData = 'select sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ' . $dbname . '.log_po_vw where ' . "\r\n" . '                substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset  where kodetipe=\'' . $klmpkbrg . '\') ' . "\r\n" . '                and tanggal like \'' . $periode . '%\' ' . $whr . "\r\n" . '                group by kodebarang' . "\r\n" . '                order by kodebarang asc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtKdBrng[$rData['kodebarang']] = $rData['kodebarang'];
		$dtNmBrng[$rData['kodebarang']] = $rData['namabarang'];
		$dtSatuan[$rData['kodebarang']] = $rData['satuan'];
		$dtHarga[$rData['kodebarang']] = $rData['hargasatuan'];
	}

	$sData = 'select distinct sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ' . $dbname . '.log_po_vw where ' . "\r\n" . '                substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset  where kodetipe=\'' . $klmpkbrg . '\') ' . "\r\n" . '                and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\' ' . $whr . "\r\n" . '                group by kodebarang' . "\r\n" . '                order by kodebarang asc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtKdBrng[$rData['kodebarang']] = $rData['kodebarang'];
		$dtNmBrng[$rData['kodebarang']] = $rData['namabarang'];
		$dtSatuan[$rData['kodebarang']] = $rData['satuan'];
		$dtHargaSmp[$rData['kodebarang']] = $rData['hargasatuan'];
	}

	$sBgt = 'select distinct sum(k' . $bln . ') as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$bgtBlni = 0;
	}

	$sBgt = 'select distinct sum(' . $addstr . ') as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$bgtSmpBln = 0;
	}

	$aresta = 'select distinct sum(harga) as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qaresta = mysql_query($aresta)) || true;

	while ($raresta = mysql_fetch_assoc($qaresta)) {
		$bgtThnan = 0;
	}

	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead><tr class=rowheader>';
	$tab .= '<td  rowspan=2 ' . $bg . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td  rowspan=2 ' . $bg . '>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '        <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '        <td align=center rowspan=2 ' . $bg . '>ANNUAL BUDGET</td>' . "\r\n" . '        <td align=center rowspan=2 ' . $bg . '>%</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>%</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>%</td>' . "\r\n" . '        </tr></thead><tbody>';

	foreach ($dtKdBrng as $dtrBrg) {
		if ($drt != substr($dtrBrg, 0, 3)) {
			$drt = substr($dtrBrg, 0, 3);
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td colspan=2>' . $optKlmpbrg[$drt] . '</td>';
			$tab .= '<td colspan=10>&nbsp;</td>';
			$tab .= '</tr>';
			$klmpBrg = substr($dtrBrg, 0, 2);
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td title=\'' . $_SESSION['lang']['kodebarang'] . '\'>' . $dtrBrg . '</td>';
		$tab .= '<td title=\'' . $_SESSION['lang']['namabarang'] . '\'>' . $optNamaBrg[$dtrBrg] . '</td>';
		$tab .= '<td align=right title=\'' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . number_format($dtHarga[$dtrBrg], 0) . '</td>';
		$tab .= '<td align=right title=\'' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . number_format($bgtBlni, 0) . '</td>';
		$tab .= '<td  title=\'' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . $dtSatuan[$dtrBrg] . '</td>';
		@$prsen[$dtrBrg] = ($dtHarga[$dtrBrg] / $bgtBlni) * 100;
		$tab .= '<td align=right title=\'%\'>' . number_format($prsen[$dtrBrg], 0) . '</td>';
		$tab .= '<td align=right title=\'' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sdbulanini'] . '\'>' . number_format($dtHargaSmp[$dtrBrg], 0) . '</td>';
		$tab .= '<td align=right title=\'' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['sdbulanini'] . '\'>' . number_format($bgtSmpBln, 0) . '</td>';
		$tab .= '<td  title=\'' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . $dtSatuan[$dtrBrg] . '</td>';
		@$prsenSmp[$dtrBrg] = ($dtHargaSmp[$dtrBrg] / $bgtSmpBln) * 100;
		$tab .= '<td align=right title=\'%\'>' . number_format($prsenSmp[$dtrBrg], 0) . '</td>';
		$tab .= '<td align=right title=\'ANNUAL BUDGET\'>' . number_format($bgtThnan, 0) . '</td>';
		@$prsenThn[$dtrBrg] = ($dtHargaSmp[$dtrBrg] / $bgtThnan) * 100;
		$tab .= '<td align=right title=\'%\'>' . number_format($prsenThn[$dtrBrg], 0) . '</td>';
		$tab .= '</tr>';
	}

	$tab .= '<tr><td colspan=10>' . "\r\n" . '           <button onclick="zBack2()" class="mybutton">Back</button>' . "\r\n" . '           <button onclick="zExcelDet2(event,\'log_slave_proc_brg_detail_kap2.php\',\'' . $arr . '\',\'' . $klmpkbrg . '\',\'reportcontainer1\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button>';
	$tab .= '</tr></tbody></table>';
	echo $tab . '###' . $judul;
	break;

case 'exceLgetDetBarang':
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$sData = 'select sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ' . $dbname . '.log_po_vw where ' . "\r\n" . '                substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset  where kodetipe=\'' . $klmpkbrg . '\') ' . "\r\n" . '                and tanggal like \'' . $periode . '%\'' . "\r\n" . '                group by kodebarang' . "\r\n" . '                order by kodebarang asc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtKdBrng[$rData['kodebarang']] = $rData['kodebarang'];
		$dtNmBrng[$rData['kodebarang']] = $rData['namabarang'];
		$dtSatuan[$rData['kodebarang']] = $rData['satuan'];
		$dtHarga[$rData['kodebarang']] = $rData['hargasatuan'];
	}

	$sData = 'select distinct sum(hargasatuan*jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ' . $dbname . '.log_po_vw where ' . "\r\n" . '                substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset  where kodetipe=\'' . $klmpkbrg . '\') ' . "\r\n" . '                and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'' . "\r\n" . '                group by kodebarang' . "\r\n" . '                order by kodebarang asc';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$dtKdBrng[$rData['kodebarang']] = $rData['kodebarang'];
		$dtNmBrng[$rData['kodebarang']] = $rData['namabarang'];
		$dtSatuan[$rData['kodebarang']] = $rData['satuan'];
		$dtHargaSmp[$rData['kodebarang']] = $rData['hargasatuan'];
	}

	$sBgt = 'select distinct sum(k' . $bln . ') as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$bgtBlni = 0;
	}

	$sBgt = 'select distinct sum(' . $addstr . ') as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$bgtSmpBln = 0;
	}

	$aresta = 'select distinct sum(harga) as total from ' . "\r\n" . '       ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' and jeniskapital=\'' . $klmpkbrg . '\'';

	#exit(mysql_error($conn));
	($qaresta = mysql_query($aresta)) || true;

	while ($raresta = mysql_fetch_assoc($qaresta)) {
		$bgtThnan = 0;
	}

	$tab2 .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
	$tab2 .= '<thead><tr class=rowheader>';
	$tab2 .= '<td  rowspan=2 ' . $bg . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab2 .= '<td  rowspan=2 ' . $bg . '>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '        <td align=center colspan=4 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '        <td align=center rowspan=2 ' . $bg . '>ANNUAL BUDGET</td>' . "\r\n" . '        <td align=center rowspan=2 ' . $bg . '>%</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>%</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td align=center ' . $bg . '>%</td>' . "\r\n" . '        </tr></thead><tbody>';

	foreach ($dtKdBrng as $dtrBrg) {
		if ($drt != substr($dtrBrg, 0, 3)) {
			$drt = substr($dtrBrg, 0, 3);
			$tab2 .= '<tr class=rowcontent>';
			$tab2 .= '<td colspan=5>' . $optKlmpbrg[$drt] . '</td>';
			$tab2 .= '<td colspan=5>&nbsp;</td>';
			$tab2 .= '</tr>';
			$klmpBrg = substr($dtrBrg, 0, 2);
		}

		$tab2 .= '<tr class=rowcontent>';
		$tab2 .= '<td title=\'' . $_SESSION['lang']['kodebarang'] . '\'>' . $dtrBrg . '</td>';
		$tab2 .= '<td title=\'' . $_SESSION['lang']['namabarang'] . '\'>' . $optNamaBrg[$dtrBrg] . '</td>';
		$tab2 .= '<td align=right title=\'' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . number_format($dtHarga[$dtrBrg], 0) . '</td>';
		$tab2 .= '<td align=right title=\'' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . number_format($bgtBlni, 0) . '</td>';
		$tab .= '<td  title=\'' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . $dtSatuan[$dtrBrg] . '</td>';
		@$prsen[$dtrBrg] = ($dtHarga[$dtrBrg] / $bgtBlni) * 100;
		$tab2 .= '<td align=right title=\'%\'>' . number_format($prsen[$dtrBrg], 0) . '</td>';
		$tab2 .= '<td align=right title=\'' . $_SESSION['lang']['realisasi'] . ' ' . $_SESSION['lang']['sdbulanini'] . '\'>' . number_format($dtHargaSmp[$dtrBrg], 0) . '</td>';
		$tab2 .= '<td align=right title=\'' . $_SESSION['lang']['anggaran'] . ' ' . $_SESSION['lang']['sdbulanini'] . '\'>' . number_format($bgtSmpBln, 0) . '</td>';
		$tab .= '<td  title=\'' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['bulanini'] . '\'>' . $dtSatuan[$dtrBrg] . '</td>';
		@$prsenSmp[$dtrBrg] = ($dtHargaSmp[$dtrBrg] / $bgtSmpBln) * 100;
		$tab2 .= '<td align=right title=\'%\'>' . number_format($prsenSmp[$dtrBrg], 0) . '</td>';
		$tab2 .= '<td align=right title=\'ANNUAL BUDGET\'>' . number_format($bgtThnan, 0) . '</td>';
		@$prsenThn[$dtrBrg] = ($dtHargaSmp[$dtrBrg] / $bgtThnan) * 100;
		$tab2 .= '<td align=right title=\'%\'>' . number_format($prsenThn[$dtrBrg], 0) . '</td>';
		$tab2 .= '</tr>';
	}

	$tab2 .= '</tr></tbody></table>';
	$tab2 .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$nop_ = 'detailBrgKapFis';

	if (0 < strlen($tab2)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab2)) {
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
