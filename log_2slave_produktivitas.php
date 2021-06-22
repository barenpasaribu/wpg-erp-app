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
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');
$where2 = 'tipetransaksi=1 and substr(tanggal,1,7)>=\'' . $periode . '\'';
$arrTanggal = makeOption($dbname, 'log_transaksiht', 'nopo,tanggal', $where2);
$arrTanggal = makeOption($dbname, 'log_transaksiht', 'nopo,notransaksi', $where2);
$arrBln = array('01' => $_SESSION['lang']['jan'], '02' => $_SESSION['lang']['peb'], '03' => $_SESSION['lang']['mar'], '04' => $_SESSION['lang']['apr'], '05' => $_SESSION['lang']['mei'], '06' => $_SESSION['lang']['jun'], '07' => $_SESSION['lang']['jul'], '08' => $_SESSION['lang']['agt'], '09' => $_SESSION['lang']['sep'], 10 => $_SESSION['lang']['okt'], 11 => $_SESSION['lang']['nov'], 12 => $_SESSION['lang']['dec']);
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['purId'] == '' ? $purId = $_GET['purId'] : $purId = $_POST['purId'];
$tglAwal = $_GET['tglAwal'];
$tglAkhir = $_GET['tglAkhir'];
$purchasing = $_GET['purchasing'];
$thn = explode('-', $periode);
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode != '') {
	$whered = ' substr(tanggal,1,7)=\'' . $periode . '\'';
	$whereb .= ' and substr(tanggal,1,7)=\'' . $periode . '\'';
}
else {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' tidak boleh kosong' . $periode);
}

if ($kdUnit != '') {
	$where .= ' and kodeorg=\'' . $kdUnit . '\'';
	$whered .= ' and kodeorg=\'' . $kdUnit . '\'';
	$whereb .= ' and kodeorg=\'' . $kdUnit . '\'';
	$unitId = $optNmOrg[$kdUnit];
}

$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=17 align=left><b> ' . $_SESSION['lang']['lapproduktivitaspur'] . ' </b></td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['pt'] . ' : ' . $unitId . '</td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['periode'] . ' : ' . $periode . '</td></tr>' . "\r\n" . '    </table>';
}

$jumHari = cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$mingguke = 1;
$tanggaldlm = 1;
$jmlhPo = array();
$purdt = array();
$ard = 1;

while ($ard <= $jumHari) {
	$tanggal = $periode . '-' . $ard;
	$query = 'SELECT datediff(\'' . $tanggal . '\', CURDATE()) as selisih';
	$hasil = mysql_query($query);
	$data = mysql_fetch_array($hasil);
	$selisih = $data['selisih'];
	$x = mktime(0, 0, 0, date('m'), date('d') + $selisih, date('Y'));
	$namahari = date('l', $x);

	if ($namahari == 'Sunday') {
		$awalitung = 1;
	}
	else if ($namahari == 'Monday') {
		$awalitung = 2;
	}
	else if ($namahari == 'Tuesday') {
		$awalitung = 3;
	}
	else if ($namahari == 'Wednesday') {
		$awalitung = 4;
	}
	else if ($namahari == 'Thursday') {
		$awalitung = 5;
	}
	else if ($namahari == 'Friday') {
		$awalitung = 6;
	}
	else if ($namahari == 'Saturday') {
		$awalitung = 7;
	}

	$tglAwal[$mingguke] = $ard;
	$awal = $awalitung;

	while ($awal <= 7) {
		$tglan = $tanggaldlm;

		if ($tanggaldlm < 10) {
			$tglan = '0' . $tanggaldlm;
		}

		$sCount = 'select distinct purchaser,count(nopo) as jmlhpo from' . "\r\n" . '        ' . $dbname . '.log_poht where  tanggal=\'' . $periode . '-' . $tglan . '\'  ' . $where . '  group by purchaser';

		#exit(mysql_error($conn));
		($qCount = mysql_query($sCount)) || true;

		while ($rCount = mysql_fetch_assoc($qCount)) {
			if ($rCount['purchaser'] != '') {
				$jmlhPo[$rCount['purchaser']][$mingguke] += (int) ($rCount['jmlhpo']);
				$purdt[$rCount['purchaser']] = $rCount['purchaser'];
			}
		}

		$tanggaldlm += 1;
		++$awal;
	}

	if ($jumHari < $tanggaldlm) {
		$tglAkhir[$mingguke] = $jumHari;
	}
	else {
		$tglAkhir[$mingguke] = $tanggaldlm - 1;
	}

	$ard = $tanggaldlm;

	if ($ard < $jumHari) {
		$mingguke += 1;
	}
}

$sTotalPo = 'select distinct count(nopo) as jmlhpo,purchaser from ' . $dbname . '.log_poht where ' . $whered . ' group by purchaser';

#exit(mysql_error($conn));
($qTotalPo = mysql_query($sTotalPo)) || true;

while ($rTotalPo = mysql_fetch_assoc($qTotalPo)) {
	$totalPo[$rTotalPo['purchaser']] = $rTotalPo['jmlhpo'];
}

$sDrPP = 'select distinct a.nopp,purchaser from ' . $dbname . '.log_prapoht a' . "\r\n" . '         left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '         where close=2 and purchaser!=\'0000000000\' and status!=3  ' . $whereb . '  ' . "\r\n" . '         group by nopp,purchaser order by nopp asc';

#exit(mysql_error($conn));
($qDrPP = mysql_query($sDrPP)) || true;

while ($rDrPP = mysql_fetch_assoc($qDrPP)) {
	$totPP[$rDrPP['purchaser']] += 1;
}

$sOutPp = 'select distinct a.nopp,purchaser from ' . $dbname . '.log_prapoht a' . "\r\n" . '         left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '         where close=2 and purchaser!=\'0000000000\' and create_po!=1 and status!=3 ' . $whereb . '  ' . "\r\n" . '         group by nopp,purchaser order by nopp asc';

#exit(mysql_error($sOutPp));
($qOutpp = mysql_query($sOutPp)) || true;

while ($rOutpp = mysql_fetch_assoc($qOutpp)) {
	$outPP[$rOutpp['purchaser']] += 1;
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2  align=center>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2  align=center>' . $_SESSION['lang']['purchaser'] . '</td><td ' . $bgcoloraja . ' colspan=' . $mingguke . '  align=center>' . $arrBln[substr($periode, 5, 2)] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['total'] . '</td>';
$tab .= '</tr>';
$tab .= '<tr>';
$ard = 1;

while ($ard <= $mingguke) {
	$tab .= '<td ' . $bgcoloraja . ' align=center>' . $ard . '</td>';
	++$ard;
}

$tab .= '<td ' . $bgcoloraja . ' align=center>PO</td>';
$tab .= '</tr></thead>' . "\r\n\t" . '<tbody>';
$dtcek = count($purdt);

if ($dtcek != 0) {
	foreach ($purdt as $dtPur) {
		++$not;
		$tab .= '<tr class=rowcontent><td>' . $not . '</td>';
		$tab .= '<td>' . $optNmOrang[$dtPur] . '</td>';
		$awalmngg = 1;

		while ($awalmngg <= $mingguke) {
			$tab .= '<td align=right onclick=detailData(event,\'log_2slave_produktivitas.php\',\'' . $tglAwal[$awalmngg] . '\',\'' . $tglAkhir[$awalmngg] . '\',\'' . $dtPur . '\')' . "\r\n" . '                   style=cursor:pointer>' . $jmlhPo[$dtPur][$awalmngg] . '</td>';
			++$awalmngg;
		}

		$tab .= '<td  align=right onclick=detailData(event,\'log_2slave_produktivitas.php\',\'\',\'\',\'' . $dtPur . '\')' . "\r\n" . '                   style=cursor:pointer>' . $totalPo[$dtPur] . '</td>';
		
		$tab .= '</tr>';
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=31>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'getKdorg':
	$optorg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sOrg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\'';

	#exit(mysql_error());
	($qOrg = mysql_query($sOrg)) || true;

	while ($rOrg = mysql_fetch_assoc($qOrg)) {
		$optorg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
	}

	echo $optorg;
	break;

case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHms');
	$nop_ = 'permintaanPembeliaan_' . $purId . '_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'getDetail':
#echo "A";
	if (($_GET['tglAwal'] != '') && ($_GET['tglAkhir'] != '')) {
		if ($_GET['tglAwal'] < 10) {
			$_GET['tglAwal'] = '0' . $_GET['tglAwal'];
		}

		if ($_GET['tglAkhir'] < 10) {
			$_GET['tglAkhir'] = '0' . $_GET['tglAkhir'];
		}

		$wheredy .= ' and a.tanggal between \'' . $periode . '-' . $_GET['tglAwal'] . '\' and \'' . $periode . '-' . $_GET['tglAkhir'] . '\'';
		$tglawal = $periode . '-' . $_GET['tglAwal'];
		$tglakhir = $periode . '-' . $_GET['tglAkhir'];
		$dttglaja = $_SESSION['lang']['tanggal'] . ':' . $tglawal . ' s.d. ' . $tglakhir;
	}
	else {
		$wheredy .= ' and substr(a.tanggal,1,7)=\'' . $periode . '\'';
		$dttglaja = $_SESSION['lang']['periode'] . ':' . $_GET['periode'];
	}

	if ($kdUnit != '') {
		$wheredy .= ' and c.kodeorg=\'' . $kdUnit . '\'';
	}

	$tab2 .= '<link rel=stylesheet type=text/css href=style/generic.css>' . "\r\n" . '            <script language=javascript1.2 src=\'js/generic.js\'></script>' . "\r\n" . '            <script language=javascript1.2 src=\'js/log_2produktivitas.js\'></script>';
	$tab2 .= '<fieldset><legend>' . $_SESSION['lang']['detail'] . '</legend>';
	$tab2 .= '' . $_SESSION['lang']['namakaryawan'] . ':' . $optNmOrang[$_GET['purchasing']] . '<br />';
	$tab2 .= $dttglaja . '<br />';
	$tab2 .= '<input type=hidden id=kdUnit value=\'' . $kdUnit . '\' /><input type=hidden id=periode value=\'' . $periode . '\' />';
	$tab2 .= '<br /><img onclick=fisikKeExcel2(event,\'log_2slave_produktivitas.php\',\'' . $_GET['tglAwal'] . '\',\'' . $_GET['tglAkhir'] . '\',\'' . $_GET['purchasing'] . '\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ';
	$sListData = 'select distinct a.nopp,namabarang,a.kodebarang,satuan,a.hargasatuan,namasupplier,b.tanggal as tglpp,a.nopo,c.tgledit,a.tanggal,a.statuspo,c.tanggalkirim,' . "\r\n" . '                    c.idFranco,c.lokasipengiriman,c.purchaser,e.tglAlokasi ,a.jumlahpesan,a.matauang ' . "\r\n" . '                    from ' . $dbname . '.log_po_vw a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp ' . "\r\n" . '                    left join ' . $dbname . '.log_poht c on a.nopo=c.nopo' . "\r\n" . '                    left join ' . $dbname . '.log_prapodt e on a.nopp=e.nopp' . "\r\n" . '                    where a.nopo!=\'\'  ' . $wheredy . ' and e.status!=\'3\' and c.purchaser=\'' . $_GET['purchasing'] . '\' ' . "\r\n" . '                    group by a.kodebarang,a.nopo order by a.nopo asc';
	$tab2 .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['alokasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>O.std</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jmlhPesan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['totalharga'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['tandatangan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=6 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=5 align=center>' . $_SESSION['lang']['pengiriman'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '</tr>';
	$tab2 .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['tipe'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['syaratPem'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jatuhtempo'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['noinvoice'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalbayar'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['franco'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tgl_kirim'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglterima'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['biaya'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Copy</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Original</td>';
	$tab2 .= '</tr></thead>' . "\r\n\t" . '<tbody>';

	#echo $sListData;
	($qListData = mysql_query($sListData)) || true;
	$rAdaData = mysql_num_rows($qListData);

	if (0 < $rAdaData) {
		while ($rListData = mysql_fetch_assoc($qListData)) {
			$tglTerima = '';
			$tglEdit = '';

			if ($klmpkBarang != $rListData['nopo']) {
				$brs = 1;
			}

			if ($brs == 1) {
				$no = 0;
				$nopodtr += 1;
				$klmpkBarang = $rListData['nopo'];
				$tab2 .= '<tr class=\'rowcontent\'>';
				$tab2 .= '<td><b>' . $nopodtr . '</b></td><td colspan=5><b>' . $klmpkBarang . '</b></td>';
				$tab2 .= '<td colspan=25>&nbsp;</td>';
				$tab2 .= '</tr>';
				$brs = 0;
			}

			$sRealisasi = 'select distinct realisasi from ' . $dbname . '.log_prapodt where nopp=\'' . $rListData['nopp'] . '\' and kodebarang=\'' . $rListData['kodebarang'] . '\'';

			#exit(mysql_error());
			($qRealisai = mysql_query($sRealisasi)) || true;
			$rRealisasi = mysql_fetch_assoc($qRealisai);

			if ($statId == '1') {
				if ($rListData['nopo'] != '') {
					$tanggalData = '';
					$sTagihan = 'select distinct noinvoice,tanggal from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTagihan = mysql_query($sTagihan)) || true;
					$rTagihan = mysql_fetch_assoc($qTagihan);
					$tglTerima = tanggalnormal($rTagihan['tglterima']);

					if ($rTagihan['tanggal'] != '') {
						$tanggalData = tanggalnormal($rTagihan['tanggal']);
					}

					$sTransaksi = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTransaksi = mysql_query($sTransaksi)) || true;
					$rTransaksi = mysql_fetch_assoc($qTransaksi);
					$tglTerima = tanggalnormal($rTransaksi['tanggal']);
				}
			}

			if ($rListData['idFranco'] != '') {
				$lokasi = $optFranco[$rListData['idFranco']];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}
			else {
				$lokasi = $rListData['lokasipengiriman'];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}

			if ($rListData['tgledit'] != '') {
				$tglEdit = tanggalnormal($rListData['tgledit']);
			}

			if (strlen($tglKirim) < 10) {
				$tglKirim = '';
			}

			if (strlen($tglTerima) < 10) {
				$tglTerima = '';
			}

			$no += 1;
			$hargaBarang = 0;

			if ($rListData['jumlahpesan'] != '') {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'];
			}

			$month1 = substr($rListData['tglAlokasi'], 5, 2);
			$date1 = substr($rListData['tglAlokasi'], 8, 2);
			$year1 = substr($rListData['tglAlokasi'], 0, 4);
			$month2 = substr($rListData['tanggal'], 5, 2);
			$date2 = substr($rListData['tanggal'], 8, 2);
			$year2 = substr($rListData['tanggal'], 0, 4);
			$jd1 = GregorianToJD($month1, $date1, $year1);
			$jd2 = GregorianToJD($month2, $date2, $year2);
			$jmlHari = $jd2 - $jd1;
			$tab2 .= '<tr class=\'rowcontent\'>';
			$tab2 .= '<td>' . $no . '</td>';
			$tab2 .= '<td>' . $rListData['nopp'] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglpp']) . '</td>';
			$tab2 .= '<td>' . $rListData['kodebarang'] . '</td>';
			$tab2 .= '<td>' . $optNmBarang[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $optSatuan[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $rListData['nopo'] . '</td>';
			$tab2 .= '<td>' . $rListData['tanggal'] . '</td>';
			$tab2 .= '<td>' . $optNmOrang[$rListData['purchaser']] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglAlokasi']) . '</td>';
			$tab2 .= '<td align=right>' . $jmlHari . '</td>';
			$tab2 .= '<td align=right>' . number_format($rRealisasi['realisasi'], 0) . '</td>';
			$tab2 .= '<td align=right>' . number_format($rListData['jumlahpesan'], 0) . '</td>';
			$tab2 .= '<td>' . $rListData['matauang'] . '</td>';
			$tab2 .= '<td align=right>' . number_format($hargaBarang, 0) . '</td>';
			$tab2 .= '<td>' . $rListData['namasupplier'] . '</td>';
			$tab2 .= '<td>' . $tglEdit . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTagihan['noinvoice'] . '</td>';
			$tab2 .= '<td>' . $tanggalData . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $lokasi . '</td>';
			$tab2 .= '<td>' . $tglKirim . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTransaksi['notransaksi'] . '</td>';
			$tab2 .= '<td>' . $tglTerima . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '</tr>';
		}
	}
	else {
		$tab2 .= '<tr class=rowcontent><td colspan=31>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}

	$tab2 .= '</tbody></table>';
	$tab2 .= '</fieldset>';
	echo $tab2;
	break;

case 'excelDetail':
	$bgcoloraja = 'bgcolor=#DEDEDE';
	$brdr = 1;

	if (($_GET['tglAwal'] != '') && ($_GET['tglAkhir'] != '')) {
		if ($_GET['tglAwal'] < 10) {
			$_GET['tglAwal'] = '0' . $_GET['tglAwal'];
		}

		if ($_GET['tglAkhir'] < 10) {
			$_GET['tglAkhir'] = '0' . $_GET['tglAkhir'];
		}

		$wheredy .= ' and a.tanggal between \'' . $periode . '-' . $_GET['tglAwal'] . '\' and \'' . $periode . '-' . $_GET['tglAkhir'] . '\'';
		$tglawal = $periode . '-' . $_GET['tglAwal'];
		$tglakhir = $periode . '-' . $_GET['tglAkhir'];
		$dttglaja = $_SESSION['lang']['tanggal'] . ':' . $tglawal . ' s.d. ' . $tglakhir;
	}
	else {
		$wheredy .= ' and substr(a.tanggal,1,7)=\'' . $periode . '\'';
		$dttglaja = $_SESSION['lang']['periode'] . ':' . $_GET['periode'];
	}

	if ($kdUnit != '') {
		$wheredy .= ' and c.kodeorg=\'' . $kdUnit . '\'';
	}

	$tab2 .= $_SESSION['lang']['detail'];
	$tab2 .= '' . $_SESSION['lang']['namakaryawan'] . ':' . $optNmOrang[$_GET['purchasing']] . '<br />';
	$tab2 .= $dttglaja . '<br />';
	$sListData = 'select distinct a.nopp,namabarang,a.kodebarang,satuan,a.hargasatuan,namasupplier,b.tanggal as tglpp,a.nopo,c.tgledit,a.tanggal,a.statuspo,c.tanggalkirim,' . "\r\n" . '                    c.idFranco,c.lokasipengiriman,c.purchaser,e.tglAlokasi ,a.jumlahpesan,a.matauang ' . "\r\n" . '                    from ' . $dbname . '.log_po_vw a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp ' . "\r\n" . '                    left join ' . $dbname . '.log_poht c on a.nopo=c.nopo' . "\r\n" . '                    left join ' . $dbname . '.log_prapodt e on a.nopp=e.nopp' . "\r\n" . '                    where a.nopo!=\'\'  ' . $wheredy . ' and e.status!=\'3\' and c.purchaser=\'' . $_GET['purchasing'] . '\' ' . "\r\n" . '                    group by a.kodebarang,a.nopo order by a.nopo asc';
	$tab2 .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['alokasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>O.std</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jmlhPesan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['totalharga'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['tandatangan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=6 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=5 align=center>' . $_SESSION['lang']['pengiriman'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '</tr>';
	$tab2 .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['tipe'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['syaratPem'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jatuhtempo'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['noinvoice'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalbayar'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['franco'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tgl_kirim'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglterima'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['biaya'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Copy</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Original</td>';
	$tab2 .= '</tr></thead>' . "\r\n\t" . '<tbody>';

	#exit(mysql_error());
	($qListData = mysql_query($sListData)) || true;
	$rAdaData = mysql_num_rows($qListData);

	if (0 < $rAdaData) {
		while ($rListData = mysql_fetch_assoc($qListData)) {
			$tglTerima = '';
			$tglEdit = '';

			if ($klmpkBarang != $rListData['nopo']) {
				$brs = 1;
			}

			if ($brs == 1) {
				$no = 0;
				$nopodtr += 1;
				$klmpkBarang = $rListData['nopo'];
				$tab2 .= '<tr class=\'rowcontent\'>';
				$tab2 .= '<td><b>' . $nopodtr . '</b></td><td colspan=5><b>' . $klmpkBarang . '</b></td>';
				$tab2 .= '<td colspan=25>&nbsp;</td>';
				$tab2 .= '</tr>';
				$brs = 0;
			}

			$sRealisasi = 'select distinct realisasi from ' . $dbname . '.log_prapodt where nopp=\'' . $rListData['nopp'] . '\' and kodebarang=\'' . $rListData['kodebarang'] . '\'';

			#exit(mysql_error());
			($qRealisai = mysql_query($sRealisasi)) || true;
			$rRealisasi = mysql_fetch_assoc($qRealisai);

			if ($statId == '1') {
				if ($rListData['nopo'] != '') {
					$tanggalData = '';
					$sTagihan = 'select distinct noinvoice,tanggal from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTagihan = mysql_query($sTagihan)) || true;
					$rTagihan = mysql_fetch_assoc($qTagihan);
					$tglTerima = tanggalnormal($rTagihan['tglterima']);

					if ($rTagihan['tanggal'] != '') {
						$tanggalData = tanggalnormal($rTagihan['tanggal']);
					}

					$sTransaksi = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTransaksi = mysql_query($sTransaksi)) || true;
					$rTransaksi = mysql_fetch_assoc($qTransaksi);
					$tglTerima = tanggalnormal($rTransaksi['tanggal']);
				}
			}

			if ($rListData['idFranco'] != '') {
				$lokasi = $optFranco[$rListData['idFranco']];
				$tglKirim = substr($rListData['tanggalkirim'], 0, 10);
			}
			else {
				$lokasi = $rListData['lokasipengiriman'];
				$tglKirim = substr($rListData['tanggalkirim'], 0, 10);
			}

			if ($rListData['tgledit'] != '') {
				$tglEdit = $rListData['tgledit'];
			}

			if (strlen($tglKirim) < 10) {
				$tglKirim = '';
			}

			if (strlen($tglTerima) < 10) {
				$tglTerima = '';
			}

			$no += 1;
			$hargaBarang = 0;

			if ($rListData['jumlahpesan'] != '') {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'];
			}

			$month1 = substr($rListData['tglAlokasi'], 5, 2);
			$date1 = substr($rListData['tglAlokasi'], 8, 2);
			$year1 = substr($rListData['tglAlokasi'], 0, 4);
			$month2 = substr($rListData['tanggal'], 5, 2);
			$date2 = substr($rListData['tanggal'], 8, 2);
			$year2 = substr($rListData['tanggal'], 0, 4);
			$jd1 = GregorianToJD($month1, $date1, $year1);
			$jd2 = GregorianToJD($month2, $date2, $year2);
			$jmlHari = $jd2 - $jd1;
			$tab2 .= '<tr class=\'rowcontent\'>';
			$tab2 .= '<td>' . $no . '</td>';
			$tab2 .= '<td>' . $rListData['nopp'] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglpp']) . '</td>';
			$tab2 .= '<td>' . $rListData['kodebarang'] . '</td>';
			$tab2 .= '<td>' . $optNmBarang[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $optSatuan[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $rListData['nopo'] . '</td>';
			$tab2 .= '<td>' . $rListData['tanggal'] . '</td>';
			$tab2 .= '<td>' . $optNmOrang[$rListData['purchaser']] . '</td>';
			$tab2 .= '<td>' . $rListData['tglAlokasi'] . '</td>';
			$tab2 .= '<td align=right>' . $jmlHari . '</td>';
			$tab2 .= '<td align=right>' . number_format($rRealisasi['realisasi'], 0) . '</td>';
			$tab2 .= '<td align=right>' . number_format($rListData['jumlahpesan'], 0) . '</td>';
			$tab2 .= '<td>' . $rListData['matauang'] . '</td>';
			$tab2 .= '<td align=right>' . number_format($hargaBarang, 0) . '</td>';
			$tab2 .= '<td>' . $rListData['namasupplier'] . '</td>';
			$tab2 .= '<td>' . $tglEdit . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTagihan['noinvoice'] . '</td>';
			$tab2 .= '<td>' . $tanggalData . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $lokasi . '</td>';
			$tab2 .= '<td>' . $tglKirim . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTransaksi['notransaksi'] . '</td>';
			$tab2 .= '<td>' . $tglTerima . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '</tr>';
		}
	}
	else {
		$tab2 .= '<tr class=rowcontent><td colspan=31>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}

	$tab2 .= '</tbody>';
	$tab2 .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$nop_ = 'detailProduktivitas_' . $optNmOrang[$_GET['purchasing']];

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
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;

case 'getPP':
	$tab2 .= '<link rel=stylesheet type=text/css href=style/generic.css>' . "\r\n" . '            <script language=javascript1.2 src=\'js/generic.js\'></script>' . "\r\n" . '            <script language=javascript1.2 src=\'js/log_2produktivitas.js\'></script>';
	$tab2 .= '<fieldset><legend>' . $_SESSION['lang']['detail'] . '</legend>';
	$tab2 .= '' . $_SESSION['lang']['namakaryawan'] . ':' . $optNmOrang[$_GET['purchasing']] . '<br />';
	$tab2 .= $dttglaja . '<br />';
	$tab2 .= '<br /><img onclick=dataPPexcel(event,\'log_2slave_produktivitas.php\',\'' . $_GET['purchasing'] . '\',\'' . $_GET['statSql'] . '\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ';
	$tab2 .= '<input type=hidden id=kdUnit value=\'' . $kdUnit . '\' /><input type=hidden id=periode value=\'' . $periode . '\' />';
	$tab2 .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['alokasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>O.std</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jmlhPesan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['totalharga'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['tandatangan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=6 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=5 align=center>' . $_SESSION['lang']['pengiriman'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '</tr>';
	$tab2 .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['tipe'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['syaratPem'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jatuhtempo'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['noinvoice'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalbayar'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['franco'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tgl_kirim'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglterima'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['biaya'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Copy</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Original</td>';
	$tab2 .= '</tr></thead>' . "\r\n\t" . '<tbody>';

	if ($_GET['statSql'] == 0) {
		$sNnopp = 'select distinct a.nopp from ' . $dbname . '.log_prapoht a ' . "\r\n" . '                 left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '                 where substr(tanggal,1,7)=\'' . $periode . '\' and purchaser=\'' . $_GET['purchasing'] . '\' and status!=3  group by a.nopp';
	}
	else if ($_GET['statSql'] == 1) {
		$sNnopp = 'select distinct a.nopp from ' . $dbname . '.log_prapoht a ' . "\r\n" . '                 left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '                 where substr(tanggal,1,7)=\'' . $periode . '\' ' . "\r\n" . '                 and purchaser=\'' . $_GET['purchasing'] . '\' and create_po!=1 and status!=3  group by a.nopp';
	}

	#exit(mysql_error($conn));
	($qNopp = mysql_query($sNnopp)) || true;

	while ($rNopp = mysql_fetch_assoc($qNopp)) {
		if ($_GET['statSql'] == 0) {
			$sListData = 'select distinct b.nopp,namabarang,e.kodebarang,satuan,a.hargasatuan,namasupplier,b.tanggal as tglpp,a.nopo,c.tgledit,a.tanggal,a.statuspo,c.tanggalkirim,' . "\r\n" . '        c.idFranco,c.lokasipengiriman,c.purchaser,e.tglAlokasi ,a.jumlahpesan,a.matauang ' . "\r\n" . '        from ' . $dbname . '.log_po_vw a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp ' . "\r\n" . '        left join ' . $dbname . '.log_poht c on a.nopo=c.nopo' . "\r\n" . '        left join ' . $dbname . '.log_prapodt e on a.nopp=e.nopp' . "\r\n" . '        where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '        group by a.kodebarang,a.nopo order by a.nopo asc';
		}
		else if ($_GET['statSql'] == 1) {
			$sListData = 'select distinct a.*,b.*,tanggal as tglpp from ' . $dbname . '.log_prapoht a' . "\r\n" . '            left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '            where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '            group by a.nopp,purchaser order by a.nopp asc';
		}

		#exit(mysql_error());
		($qListData = mysql_query($sListData)) || true;
		$baris = mysql_num_rows($qListData);

		if ($baris == 0) {
			$sdata = 'select distinct a.*,b.*,tanggal as tglpp from ' . $dbname . '.log_prapoht a' . "\r\n" . '         left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '         where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '         group by a.nopp,purchaser order by a.nopp asc';

			#exit(mysql_error($conn));
			($qListData = mysql_query($sdata)) || true;
		}

		while ($rListData = mysql_fetch_assoc($qListData)) {
			$tglTerima = '';
			$tglEdit = '';

			if ($klmpkBarang != $rListData['nopp']) {
				$brs = 1;
			}

			if ($brs == 1) {
				$no = 0;
				$nopodtr += 1;
				$klmpkBarang = $rListData['nopp'];
				$tab2 .= '<tr class=\'rowcontent\'>';
				$tab2 .= '<td><b>' . $nopodtr . '</b></td><td colspan=5><b>' . $klmpkBarang . '</b></td>';
				$tab2 .= '<td colspan=25>&nbsp;</td>';
				$tab2 .= '</tr>';
				$brs = 0;
			}

			$sRealisasi = 'select distinct realisasi from ' . $dbname . '.log_prapodt where nopp=\'' . $rListData['nopp'] . '\' and kodebarang=\'' . $rListData['kodebarang'] . '\'';

			#exit(mysql_error());
			($qRealisai = mysql_query($sRealisasi)) || true;
			$rRealisasi = mysql_fetch_assoc($qRealisai);

			if ($statId == '1') {
				if ($rListData['nopo'] != '') {
					$tanggalData = '';
					$sTagihan = 'select distinct noinvoice,tanggal from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTagihan = mysql_query($sTagihan)) || true;
					$rTagihan = mysql_fetch_assoc($qTagihan);
					$tglTerima = tanggalnormal($rTagihan['tglterima']);

					if ($rTagihan['tanggal'] != '') {
						$tanggalData = tanggalnormal($rTagihan['tanggal']);
					}

					$sTransaksi = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTransaksi = mysql_query($sTransaksi)) || true;
					$rTransaksi = mysql_fetch_assoc($qTransaksi);
					$tglTerima = tanggalnormal($rTransaksi['tanggal']);
				}
			}

			if ($rListData['idFranco'] != '') {
				$lokasi = $optFranco[$rListData['idFranco']];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}
			else {
				$lokasi = $rListData['lokasipengiriman'];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}

			if ($rListData['tgledit'] != '') {
				$tglEdit = tanggalnormal($rListData['tgledit']);
			}

			if (strlen($tglKirim) < 10) {
				$tglKirim = '';
			}

			if (strlen($tglTerima) < 10) {
				$tglTerima = '';
			}

			$no += 1;
			$hargaBarang = 0;

			if ($rListData['jumlahpesan'] != '') {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'];
			}

			$jmlHari = 0;

			if ($rListData['close'] == '') {
				$month1 = substr($rListData['tglAlokasi'], 5, 2);
				$date1 = substr($rListData['tglAlokasi'], 8, 2);
				$year1 = substr($rListData['tglAlokasi'], 0, 4);
				$month2 = substr($rListData['tanggal'], 5, 2);
				$date2 = substr($rListData['tanggal'], 8, 2);
				$year2 = substr($rListData['tanggal'], 0, 4);
				$jd1 = GregorianToJD($month1, $date1, $year1);
				$jd2 = GregorianToJD($month2, $date2, $year2);
				$jmlHari = $jd2 - $jd1;
			}

			$tab2 .= '<tr class=\'rowcontent\'>';
			$tab2 .= '<td>' . $no . '</td>';
			$tab2 .= '<td>' . $rListData['nopp'] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglpp']) . '</td>';
			$tab2 .= '<td>' . $rListData['kodebarang'] . '</td>';
			$tab2 .= '<td>' . $optNmBarang[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $optSatuan[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $rListData['nopo'] . '</td>';
			$tab2 .= '<td>' . $rListData['tanggal'] . '</td>';
			$tab2 .= '<td>' . $optNmOrang[$rListData['purchaser']] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglAlokasi']) . '</td>';
			$tab2 .= '<td align=right>' . $jmlHari . '</td>';
			$tab2 .= '<td align=right>' . number_format($rRealisasi['realisasi'], 0) . '</td>';
			$tab2 .= '<td align=right>' . number_format($rListData['jumlahpesan'], 0) . '</td>';
			$tab2 .= '<td>' . $rListData['matauang'] . '</td>';
			$tab2 .= '<td align=right>' . number_format($hargaBarang, 0) . '</td>';
			$tab2 .= '<td>' . $rListData['namasupplier'] . '</td>';
			$tab2 .= '<td>' . $tglEdit . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTagihan['noinvoice'] . '</td>';
			$tab2 .= '<td>' . $tanggalData . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $lokasi . '</td>';
			$tab2 .= '<td>' . $tglKirim . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTransaksi['notransaksi'] . '</td>';
			$tab2 .= '<td>' . $tglTerima . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '</tr>';
		}
	}

	$tab2 .= '</tbody></table>';
	$tab2 .= '</fieldset>';
	echo $tab2;
	break;

case 'getPPExcel':
	$bgcoloraja = 'bgcolor=#DEDEDE';
	$brdr = 1;
	$tab2 .= '' . $_SESSION['lang']['detail'] . '';
	$tab2 .= '' . $_SESSION['lang']['namakaryawan'] . ':' . $optNmOrang[$_GET['purchasing']] . '<br />';
	$tab2 .= $dttglaja . '<br />';
	$tab2 .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['alokasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>O.std</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jmlhPesan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['totalharga'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['tandatangan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=6 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=5 align=center>' . $_SESSION['lang']['pengiriman'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '</tr>';
	$tab2 .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['tipe'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['syaratPem'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jatuhtempo'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['noinvoice'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalbayar'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['franco'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tgl_kirim'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglterima'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['biaya'] . '</td>';
	$tab2 .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Copy</td>';
	$tab2 .= '<td  ' . $bgcoloraja . '>Original</td>';
	$tab2 .= '</tr></thead>' . "\r\n\t" . '<tbody>';

	if ($_GET['statSql'] == 0) {
		$sNnopp = 'select distinct a.nopp from ' . $dbname . '.log_prapoht a ' . "\r\n" . '                 left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '                 where substr(tanggal,1,7)=\'' . $periode . '\' and purchaser=\'' . $_GET['purchasing'] . '\' and status!=3  group by a.nopp';
	}
	else if ($_GET['statSql'] == 1) {
		$sNnopp = 'select distinct a.nopp from ' . $dbname . '.log_prapoht a ' . "\r\n" . '                 left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '                 where substr(tanggal,1,7)=\'' . $periode . '\' ' . "\r\n" . '                 and purchaser=\'' . $_GET['purchasing'] . '\' and create_po!=1 and status!=3  group by a.nopp';
	}

	#exit(mysql_error($conn));
	($qNopp = mysql_query($sNnopp)) || true;

	while ($rNopp = mysql_fetch_assoc($qNopp)) {
		if ($_GET['statSql'] == 0) {
			$sListData = 'select distinct b.nopp,namabarang,e.kodebarang,satuan,a.hargasatuan,namasupplier,b.tanggal as tglpp,a.nopo,c.tgledit,a.tanggal,a.statuspo,c.tanggalkirim,' . "\r\n" . '        c.idFranco,c.lokasipengiriman,c.purchaser,e.tglAlokasi ,a.jumlahpesan,a.matauang ' . "\r\n" . '        from ' . $dbname . '.log_po_vw a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp ' . "\r\n" . '        left join ' . $dbname . '.log_poht c on a.nopo=c.nopo' . "\r\n" . '        left join ' . $dbname . '.log_prapodt e on a.nopp=e.nopp' . "\r\n" . '        where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '        group by a.kodebarang,a.nopo order by a.nopo asc';
		}
		else if ($_GET['statSql'] == 1) {
			$sListData = 'select distinct a.*,b.*,tanggal as tglpp from ' . $dbname . '.log_prapoht a' . "\r\n" . '            left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '            where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '            group by a.nopp,purchaser order by a.nopp asc';
		}

		#exit(mysql_error());
		($qListData = mysql_query($sListData)) || true;
		$baris = mysql_num_rows($qListData);

		if ($baris == 0) {
			$sdata = 'select distinct a.*,b.*,tanggal as tglpp from ' . $dbname . '.log_prapoht a' . "\r\n" . '         left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '         where a.nopp=\'' . $rNopp['nopp'] . '\'' . "\r\n" . '         group by a.nopp,purchaser order by a.nopp asc';

			#exit(mysql_error($conn));
			($qListData = mysql_query($sdata)) || true;
		}

		while ($rListData = mysql_fetch_assoc($qListData)) {
			$tglTerima = '';
			$tglEdit = '';

			if ($klmpkBarang != $rListData['nopp']) {
				$brs = 1;
			}

			if ($brs == 1) {
				$no = 0;
				$nopodtr += 1;
				$klmpkBarang = $rListData['nopp'];
				$tab2 .= '<tr class=\'rowcontent\'>';
				$tab2 .= '<td><b>' . $nopodtr . '</b></td><td colspan=5><b>' . $klmpkBarang . '</b></td>';
				$tab2 .= '<td colspan=25>&nbsp;</td>';
				$tab2 .= '</tr>';
				$brs = 0;
			}

			$sRealisasi = 'select distinct realisasi from ' . $dbname . '.log_prapodt where nopp=\'' . $rListData['nopp'] . '\' and kodebarang=\'' . $rListData['kodebarang'] . '\'';

			#exit(mysql_error());
			($qRealisai = mysql_query($sRealisasi)) || true;
			$rRealisasi = mysql_fetch_assoc($qRealisai);

			if ($statId == '1') {
				if ($rListData['nopo'] != '') {
					$tanggalData = '';
					$sTagihan = 'select distinct noinvoice,tanggal from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTagihan = mysql_query($sTagihan)) || true;
					$rTagihan = mysql_fetch_assoc($qTagihan);
					$tglTerima = tanggalnormal($rTagihan['tglterima']);

					if ($rTagihan['tanggal'] != '') {
						$tanggalData = tanggalnormal($rTagihan['tanggal']);
					}

					$sTransaksi = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $rListData['nopo'] . '\'';

					#exit(mysql_error());
					($qTransaksi = mysql_query($sTransaksi)) || true;
					$rTransaksi = mysql_fetch_assoc($qTransaksi);
					$tglTerima = tanggalnormal($rTransaksi['tanggal']);
				}
			}

			if ($rListData['idFranco'] != '') {
				$lokasi = $optFranco[$rListData['idFranco']];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}
			else {
				$lokasi = $rListData['lokasipengiriman'];
				$tglKirim = tanggalnormal(substr($rListData['tanggalkirim'], 0, 10));
			}

			if ($rListData['tgledit'] != '') {
				$tglEdit = tanggalnormal($rListData['tgledit']);
			}

			if (strlen($tglKirim) < 10) {
				$tglKirim = '';
			}

			if (strlen($tglTerima) < 10) {
				$tglTerima = '';
			}

			$no += 1;
			$hargaBarang = 0;

			if ($rListData['jumlahpesan'] != '') {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'];
			}

			$jmlHari = 0;

			if ($rListData['close'] == '') {
				$month1 = substr($rListData['tglAlokasi'], 5, 2);
				$date1 = substr($rListData['tglAlokasi'], 8, 2);
				$year1 = substr($rListData['tglAlokasi'], 0, 4);
				$month2 = substr($rListData['tanggal'], 5, 2);
				$date2 = substr($rListData['tanggal'], 8, 2);
				$year2 = substr($rListData['tanggal'], 0, 4);
				$jd1 = GregorianToJD($month1, $date1, $year1);
				$jd2 = GregorianToJD($month2, $date2, $year2);
				$jmlHari = $jd2 - $jd1;
			}

			$tab2 .= '<tr class=\'rowcontent\'>';
			$tab2 .= '<td>' . $no . '</td>';
			$tab2 .= '<td>' . $rListData['nopp'] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglpp']) . '</td>';
			$tab2 .= '<td>' . $rListData['kodebarang'] . '</td>';
			$tab2 .= '<td>' . $optNmBarang[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $optSatuan[$rListData['kodebarang']] . '</td>';
			$tab2 .= '<td>' . $rListData['nopo'] . '</td>';
			$tab2 .= '<td>' . $rListData['tanggal'] . '</td>';
			$tab2 .= '<td>' . $optNmOrang[$rListData['purchaser']] . '</td>';
			$tab2 .= '<td>' . tanggalnormal($rListData['tglAlokasi']) . '</td>';
			$tab2 .= '<td align=right>' . $jmlHari . '</td>';
			$tab2 .= '<td align=right>' . number_format($rRealisasi['realisasi'], 0) . '</td>';
			$tab2 .= '<td align=right>' . number_format($rListData['jumlahpesan'], 0) . '</td>';
			$tab2 .= '<td>' . $rListData['matauang'] . '</td>';
			$tab2 .= '<td align=right>' . number_format($hargaBarang, 0) . '</td>';
			$tab2 .= '<td>' . $rListData['namasupplier'] . '</td>';
			$tab2 .= '<td>' . $tglEdit . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTagihan['noinvoice'] . '</td>';
			$tab2 .= '<td>' . $tanggalData . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $lokasi . '</td>';
			$tab2 .= '<td>' . $tglKirim . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>' . $rTransaksi['notransaksi'] . '</td>';
			$tab2 .= '<td>' . $tglTerima . '</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '<td>&nbsp;</td>';
			$tab2 .= '</tr>';
		}
	}

	$tab2 .= '</tbody>';
	$tab2 .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$nop_ = 'detailProduktivitasPP_' . $optNmOrang[$_GET['purchasing']];

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
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;
}

?>
