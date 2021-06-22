<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
$totRow = $_POST['totRow'];
$keyId = $_POST['keyId'];
$noakunCari = $_POST['noakunCari'];
$kdBlok = $_POST['kdBlok'];
$kegId = $_POST['kegId'];
$thnBudget = $_POST['thnBudget'];
$noAkun = $_POST['noAkun'];
$tpBudget = $_POST['tpBudget'];
$rotThn = $_POST['rotThn'];
$volKeg = $_POST['volKeg'];
$satuan = $_POST['satuan'];
$afd = $_POST['afd'];
$where = ' tahunbudget=\'' . $thnBudget . '\' and kodeorg=\'' . $kdBlok . '\' and tipebudget=\'' . $tpBudget . '\' and kegiatan=\'' . $kegId . '\'';
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optNmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optKdBgt = makeOption($dbname, 'bgt_kode', 'kodebudget,nama');
$where2 = ' kodeorg like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and tipebudget=\'ESTATE\' and tahunbudget=\'' . $thnBudget . '\'';
$jmlhPerson = $_POST['jmlhPerson'];
$kdGol = $_POST['kdGol'];
$hkEfektif = $_POST['hkEfektif'];
$tipeBudget = $_POST['tipeBudget'];
$totBiaya = $_POST['totBiaya'];
$kdBudget = $_POST['kdBudget'];
$kdBrg = $_POST['kdBrg'];
$jmlhBrg = $_POST['jmlhBrg'];
$satuanBrg = $_POST['satuanBrg'];
$totHarga = $_POST['totHarga'];
$nmBrg = $_POST['nmBrg'];
$klmpkBrg = $_POST['klmpkBrg'];
$nmBrgL = $_POST['nmBrgL'];
$kdBrgL = $_POST['kdBrgL'];
$jmlhBrgL = $_POST['jmlhBrgL'];
$kdBudgetL = $_POST['kdBudgetL'];
$totHargaL = $_POST['totHargaL'];
$satuanBrgL = $_POST['satuanBrgL'];
$kdBudgetK = $_POST['kdBudgetK'];
$volKontrak = $_POST['volKontrak'];
$satKontrak = $_POST['satKontrak'];
$totBiayaK = $_POST['totBiayaK'];
$kdVhc = $_POST['kdVhc'];
$jmlhJam = $_POST['jmlhJam'];
$totBiayaKend = $_POST['totBiayaKend'];
$kdBudgetV = $_POST['kdBudgetV'];
$satVhc = $_POST['satVhc'];
$thnbudgetHeader = $_POST['thnbudgetHeader'];

if ($proses == '') {
	$proses = $_GET['proses'];
	echo '<script language=javascript1.2 src="js/generic.js.js?v='.date('YmdHis').'></script>' . "\r\n" . '    <script language=javascript1.2 src="js/bgt_budget_kebun.js"></script>' . "\r\n" . '    <link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n" . '    ';
	$tahunbudget = $_GET['tahunbudget'];
	$kodeblok = $_GET['kodeblok'];
	$tipebudget = $_GET['tipebudget'];
	$noakun = $_GET['noakun'];
	$kegiatan = $_GET['kegiatan'];
	$volume = $_GET['volume'];
	$satuanvolume = $_GET['satuanvolume'];
	$rotasi = $_GET['rotasi'];
}

switch ($proses) {
case 'copyblok':
	$bloktujuan = $_POST['bloktujuan'];
	$tahunbudget = $_POST['tahunbudget'];
	$bloksumber = $_POST['bloksumber'];
	$kegiatan = $_POST['kegiatan'];
	$tipebudget = $_POST['tipebudget'];
	$str = 'select * from ' . $dbname . '.bgt_blok' . "\r\n" . '            where tahunbudget = \'' . $tahunbudget . '\' and kodeblok = \'' . $bloksumber . '\'';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$luassumber = $bar->hathnini;
	}

	$str = 'select * from ' . $dbname . '.bgt_blok' . "\r\n" . '            where tahunbudget = \'' . $tahunbudget . '\' and kodeblok = \'' . $bloktujuan . '\'';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$luastujuan = $bar->hathnini;
	}

	$str = 'select * from ' . $dbname . '.bgt_budget ' . "\r\n" . '            where tahunbudget = \'' . $tahunbudget . '\' and kodeorg = \'' . $bloktujuan . '\' and kegiatan = \'' . $kegiatan . '\' and tipebudget = \'' . $tipebudget . '\'' . "\r\n" . '            limit 1';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		echo 'error: The same activity on this block already exist...';
		exit();
	}

	$str = 'select * from ' . $dbname . '.bgt_budget ' . "\r\n" . '            where tahunbudget = \'' . $tahunbudget . '\' and kodeorg = \'' . $bloksumber . '\' and kegiatan = \'' . $kegiatan . '\' and tipebudget = \'' . $tipebudget . '\'' . "\r\n" . '            and kodebudget = \'VHC\'' . "\r\n" . '            order by kodebudget';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$dzArr[$bar->kunci]['kodevhc'] = $bar->kodevhc;
		$dzArr[$bar->kunci]['jumlah'] = $bar->jumlah;
	}

	if (!empty($dzArr)) {
		foreach ($dzArr as $arey) {
			$kodevhc = $arey['kodevhc'];
			@$jumlah = ($luastujuan / $luassumber) * $arey['jumlah'];

			if (!is_null($kodevhc)) {
				$str = 'select * from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi' . "\r\n" . '                    where tahunbudget = \'' . $tahunbudget . '\' and kodevhc = \'' . $kodevhc . '\'';
				$res = mysql_query($str);
				$no = 0;

				while ($bar = mysql_fetch_object($res)) {
					$teralokasi = $bar->teralokasi;
					$jamsetahun = $bar->jamsetahun;
				}

				$setelahditambah = $teralokasi + $jumlah;

				if ($jamsetahun < $setelahditambah) {
					echo 'error: Vehicle allocation ' . $kodevhc . ' greater than capacity allowed' . "\n\r\n" . '                        Allocated: ' . number_format($teralokasi) . ' + ' . number_format($jumlah) . ' = ' . number_format($setelahditambah) . "\n\r\n" . '                        Maximum: ' . number_format($jamsetahun) . '.';
					exit();
				}
			}
		}
	}

	$dzArr = array();
	$str = 'select * from ' . $dbname . '.bgt_budget ' . "\r\n" . '            where tahunbudget = \'' . $tahunbudget . '\' and kodeorg = \'' . $bloksumber . '\' and kegiatan = \'' . $kegiatan . '\' and tipebudget = \'' . $tipebudget . '\'' . "\r\n" . '            and kodebudget != \'SUPERVISI\'' . "\r\n" . '            order by kodebudget';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$dzArr[$bar->kunci]['tahunbudget'] = $bar->tahunbudget;
		$dzArr[$bar->kunci]['kodeorg'] = $bar->kodeorg;
		$dzArr[$bar->kunci]['tipebudget'] = $bar->tipebudget;
		$dzArr[$bar->kunci]['kodebudget'] = $bar->kodebudget;
		$dzArr[$bar->kunci]['kegiatan'] = $bar->kegiatan;
		$dzArr[$bar->kunci]['noakun'] = $bar->noakun;
		$dzArr[$bar->kunci]['volume'] = $bar->volume;
		$dzArr[$bar->kunci]['satuanv'] = $bar->satuanv;
		$dzArr[$bar->kunci]['rupiah'] = $bar->rupiah;
		$dzArr[$bar->kunci]['kodevhc'] = $bar->kodevhc;
		$dzArr[$bar->kunci]['kodebarang'] = $bar->kodebarang;
		$dzArr[$bar->kunci]['rotasi'] = $bar->rotasi;
		$dzArr[$bar->kunci]['regional'] = $bar->regional;
		$dzArr[$bar->kunci]['jumlah'] = $bar->jumlah;
		$dzArr[$bar->kunci]['satuanj'] = $bar->satuanj;
		$dzArr[$bar->kunci]['keterangan'] = $bar->keterangan;
		$dzArr[$bar->kunci]['tutup'] = $bar->tutup;
	}

	if (!empty($dzArr)) {
		foreach ($dzArr as $arey) {
			$tahunbudget = $tahunbudget;
			$kodeorg = $bloktujuan;
			$tipebudget = $tipebudget;
			$kodebudget = $arey['kodebudget'];
			$kegiatan = $kegiatan;
			$noakun = $arey['noakun'];
			@$volume = ($luastujuan / $luassumber) * $arey['volume'];
			$satuanv = $arey['satuanv'];
			@$rupiah = ($luastujuan / $luassumber) * $arey['rupiah'];
			$kodevhc = $arey['kodevhc'];
			$kodebarang = $arey['kodebarang'];
			$rotasi = $arey['rotasi'];
			$regional = $arey['regional'];
			@$jumlah = ($luastujuan / $luassumber) * $arey['jumlah'];
			$satuanj = $arey['satuanj'];
			$keterangan = $arey['keterangan'];
			$tutup = $arey['tutup'];
			$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, ' . "\r\n" . '                rupiah, kodevhc, kodebarang, rotasi, regional, updateby, jumlah, satuanj,' . "\r\n" . '                keterangan, tutup) values' . "\r\n" . '            (\'' . $tahunbudget . '\',\'' . $kodeorg . '\',\'' . $tipebudget . '\',\'' . $kodebudget . '\',\'' . $kegiatan . '\',\'' . $noakun . '\',\'' . $volume . '\',\'' . $satuanv . '\',' . "\r\n" . '                \'' . $rupiah . '\',\'' . $kodevhc . '\',\'' . $kodebarang . '\',\'' . $rotasi . '\',\'' . $regional . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $jumlah . '\',\'' . $satuanj . '\',' . "\r\n" . '                \'' . $keterangan . '\',\'' . $tutup . '\')';

			if (mysql_query($sIns)) {
				echo '';
			}
			else {
				echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
			}
		}
	}

	break;

case 'otherblok':
	$stream = '';
	$str = 'select * from ' . $dbname . '.bgt_blok' . "\r\n" . '            where kodeblok = \'' . $kodeblok . '\'';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$statusblok = $bar->statusblok;
		$tahuntanam = $bar->thntnm;
		$luas = $bar->hathnini;
	}

	$stream .= $_SESSION['lang']['budgetyear'] . ': ' . $tahunbudget;
	$stream .= '<br>' . $_SESSION['lang']['kodeblok'] . ': ' . $kodeblok . ' ' . $statusblok . ' ' . $tahuntanam . ' ' . $luas . ' Ha';
	$stream .= '<table class=sortable border=0cellspacing=1>';
	$stream .= '<thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['rotasi'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['volume'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['kendaraan'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['material'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['satuan'] . '</td>';
	$stream .= '</tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody>';
	$str = 'select a.*, b.namabarang from ' . $dbname . '.bgt_budget a' . "\r\n" . '            left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '            where a.tahunbudget = \'' . $tahunbudget . '\' and a.kodeorg = \'' . $kodeblok . '\' and a.kegiatan = \'' . $kegiatan . '\' and a.tipebudget = \'ESTATE\'' . "\r\n" . '            and a.kodebudget != \'SUPERVISI\'' . "\r\n" . '            order by a.kodebudget';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$stream .= '<tr class=rowcontent><td align=center>' . $bar->kodebudget . '</td>' . "\r\n" . '               <td align=center>' . $bar->rotasi . '</td>' . "\r\n" . '               <td align=right>' . $bar->volume . '</td>' . "\r\n" . '               <td align=left>' . $bar->satuanv . '</td>' . "\r\n" . '               <td align=left>' . $bar->kodevhc . '</td>' . "\r\n" . '               <td align=left>' . $bar->namabarang . '</td>' . "\r\n" . '               <td align=right>' . $bar->jumlah . '</td>               ' . "\r\n" . '               <td align=left>' . $bar->satuanj . '</td></tr>';
	}

	$stream .= '</tbody></table>';
	$stream .= '</br>';
	$stream .= '<table class=sortable border=0cellspacing=1>';
	$stream .= '<thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td>No</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['kodeblok'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['statusblok'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['thntanam'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['luasareal'] . '</td>' . "\r\n" . '            <td>Copy</td>';
	$stream .= '</tr>' . "\r\n" . '        </thead>' . "\r\n" . '        <tbody>';
	$wherestatus = '';
	if ($statusblok == 'TB' || 'TBM' || 'TM') {
		$wherestatus = 'and statusblok in (\'TB\',\'TBM\',\'TM\')';
	}

	if ($statusblok == 'BBT') {
		$wherestatus = 'and statusblok in (\'BBT\')';
	}

	$str = 'select * from ' . $dbname . '.bgt_blok' . "\r\n" . '            where kodeblok like \'' . substr($kodeblok, 0, 4) . '%\' ' . $wherestatus . ' and kodeblok != \'' . $kodeblok . '\' and tahunbudget = \'' . $tahunbudget . '\'' . "\r\n" . '            order by kodeblok';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '               <td align=right>' . $no . '</td>' . "\r\n" . '               <td align=center>' . $bar->kodeblok . '</td>' . "\r\n" . '               <td align=center>' . $bar->statusblok . '</td>' . "\r\n" . '               <td align=center>' . $bar->thntnm . '</td>' . "\r\n" . '               <td align=right>' . $bar->hathnini . '</td>' . "\r\n" . '               ';
		$stream .= '<td><input type="checkbox" name="copy" value="copy" onclick="copybudget(\'' . $bar->kodeblok . '\',\'' . $tahunbudget . '\',\'' . $kodeblok . '\',\'' . $kegiatan . '\',\'ESTATE\');"></td>';
		$stream .= '</tr>';
	}

	$stream .= '</tbody></table>';
	echo $stream;
	echo '<div id=\'progress\' style=\'display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;\'>' . "\r\n" . 'Please wait.....! <br>' . "\r\n" . '<img src=\'images/progress.gif\'>' . "\r\n" . '</div>';
	echo '<div id=\'alternatelock\' style=\'width:800px;height:600px;background-color:#999999;position:fixed;top:0px;left:0px;text-align:center;display:none;\'>' . "\r\n" . '    <img src=\'images/progress.gif\'><br><b>P l e a s e &nbsp  w a i t . ...!</b>' . "\r\n" . '    </div>';
	break;

case 'getBlok':
	$optBlok = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sVhc = 'select distinct kodeblok from ' . $dbname . '.bgt_blok where tahunbudget=\'' . $thnBudget . '\' and kodeblok like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and closed=1';

	#exit(mysql_error($conn));
	($qVhc = mysql_query($sVhc)) || true;
	$brs = mysql_num_rows($qVhc);

	if (0 < $brs) {
		while ($rVhc = mysql_fetch_assoc($qVhc)) {
			if ($kdBlok != '') {
				$optBlok .= '<option value=\'' . $rVhc['kodeblok'] . '\' ' . ($kdBlok == $rVhc['kodeblok'] ? 'selected' : '') . '>' . $rVhc['kodeblok'] . '</option>';
			}
			else {
				$optBlok .= '<option value=\'' . $rVhc['kodeblok'] . '\'>' . $rVhc['kodeblok'] . '</option>';
			}
		}

		echo $optBlok;
	}
	else {
		exit('Error: Budget block does not exist');
	}

	break;

case 'getKegiatan':
	if ($kdBlok == '') {
		exit('Error:Block code required');
	}

	$sStatus = 'select distinct statusblok from ' . $dbname . '.bgt_blok where kodeblok=\'' . $kdBlok . '\'';

	#exit(mysql_error($conn));
	($qStatus = mysql_query($sStatus)) || true;
	$rStatus = mysql_fetch_assoc($qStatus);
	$optKeg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sKeg = 'select distinct kodekegiatan,namakegiatan,kelompok from ' . $dbname . '.setup_kegiatan where  kelompok in (\'PNN\',\'TBM\',\'TM\',\'BBT\',\'TB\')  order by kodekegiatan asc';

	#exit(mysql_error());
	($qKeg = mysql_query($sKeg)) || true;

	while ($rKeg = mysql_fetch_assoc($qKeg)) {
		if ($kegId != '') {
			$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . ' ' . ($rKeg['kodekegiatan'] == $kegId ? 'selected' : '') . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
		}
		else {
			$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
		}
	}

	echo $optKeg;
	break;

case 'gantiKegiatan':
	if ($kdBlok == '') {
		exit('Error:Block code required');
	}

	$sStatus = 'select distinct statusblok from ' . $dbname . '.bgt_blok where kodeblok=\'' . $kdBlok . '\' and tahunbudget=\'' . $thnBudget . '\'';

	#exit(mysql_error($conn));
	($qStatus = mysql_query($sStatus)) || true;
	$rStatus = mysql_fetch_assoc($qStatus);
	$optKeg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

	if ($_SESSION['language'] == 'ID') {
		$dd = 'namakegiatan as namakegiatan';
	}
	else {
		$dd = 'namakegiatan1 as namakegiatan';
	}

	if ($rStatus['statusblok'] == 'TM') {
		$sKeg = 'select distinct kodekegiatan,' . $dd . ',kelompok from ' . $dbname . '.setup_kegiatan where  kelompok in (\'' . $rStatus['statusblok'] . '\',\'PNN\')  order by kodekegiatan asc';
	}
	else {
		$sKeg = 'select distinct kodekegiatan,' . $dd . ',kelompok from ' . $dbname . '.setup_kegiatan where  kelompok=\'' . $rStatus['statusblok'] . '\'  order by kodekegiatan asc';
	}

	if ($rStatus['statusblok'] == 'TB') {
		$sKeg = 'select distinct kodekegiatan,' . $dd . ',kelompok from ' . $dbname . '.setup_kegiatan where  kelompok in (\'TB\',\'TBM\')  order by kodekegiatan asc';
	}

	#exit(mysql_error());
	($qKeg = mysql_query($sKeg)) || true;

	while ($rKeg = mysql_fetch_assoc($qKeg)) {
		if ($kegId != '') {
			$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . ' ' . ($rKeg['kodekegiatan'] == $kegId ? 'selected' : '') . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
		}
		else {
			$optKeg .= '<option value=' . $rKeg['kodekegiatan'] . '>' . $rKeg['kodekegiatan'] . ' [' . $rKeg['namakegiatan'] . '][' . $rKeg['kelompok'] . ']</option>';
		}
	}

	echo $optKeg;
	break;

case 'getSatuan':
	if ($kegId == '') {
		exit('Error:Activity code required');
	}

	$sKegiata = 'select distinct satuan,noakun from ' . $dbname . '.setup_kegiatan where kodekegiatan=\'' . $kegId . '\'';

	#exit(mysql_error($conn));
	($qKegiatan = mysql_query($sKegiata)) || true;
	$row = mysql_num_rows($qKegiatan);

	if (0 < $row) {
		$rKegiatan = mysql_fetch_assoc($qKegiatan);

		if ($rKegiatan['satuan'] == 'NULL') {
			$rKegiatan['satuan'] = '';
		}

		echo $rKegiatan['satuan'] . '###' . $rKegiatan['noakun'];
	}
	else {
		exit('Error:Block status does not match');
	}

	break;

case 'cekSave':
	if (($thnBudget == '') || ($kegId == '') || ($kdBlok == '') || ($tpBudget == '') || ($noAkun == '') || ($volKeg == '') || ($satuan == '') || ($rotThn == '')) {
		exit('Error:Field required');
	}

	if (strlen($thnBudget) < 4) {
		exit('Error:Budget year incorrect');
	}

	$sCek2 = 'select distinct tutup from ' . $dbname . '.bgt_budget where ' . $where2 . '';

	#exit(mysql_error($conn));
	($qCek2 = mysql_query($sCek2)) || true;
	$rCek2 = mysql_fetch_assoc($qCek2);

	if (0 < $rCek2['tutup']) {
		exit('Error:  Budget ' . $thnBudget . ' has been closed, could not modify');
	}

	if ($hkEfektip == '') {
		$sHk = 'select distinct * from ' . $dbname . '.bgt_hk where tahunbudget=\'' . $thnBudget . '\'';

		#exit(mysql_error($conn));
		($qHk = mysql_query($sHk)) || true;
		$rHk = mysql_fetch_assoc($qHk);
		$hkEfektip = (intval($rHk['harisetahun']) - intval($rHk['hrminggu']) - intval($rHk['hrlibur'])) + intval($rHk['hrliburminggu']);
		$optVhc = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$sVhc = 'select distinct kodetraksi ,kodevhc from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi where tahunbudget=\'' . $thnBudget . '\' order by kodevhc asc';

		#exit(mysql_error($conn));
		($qVhc = mysql_query($sVhc)) || true;

		while ($rVhc = mysql_fetch_assoc($qVhc)) {
			$optVhc .= '<option value=\'' . $rVhc['kodevhc'] . '\'>' . $rVhc['kodevhc'] . ' [' . $rVhc['kodetraksi'] . ']</option>';
		}

		$optupah = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
		$supah = 'select distinct golongan from ' . $dbname . '.bgt_upah where ' . "\r\n" . '                        kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . "\r\n" . '                        and tahunbudget=\'' . $thnBudget . '\' and jumlah!=0';

		#exit(mysql_error($conn));
		($qupah = mysql_query($supah)) || true;

		while ($rupah = mysql_fetch_assoc($qupah)) {
			$optupah .= '<option value=\'' . $rupah['golongan'] . '\'>' . $optKdBgt[$rupah['golongan']] . '</option>';
		}

		echo $hkEfektip . '###' . $optVhc . '###' . $optupah;
	}

	break;

case 'getUpah':
	if ($kdGol == '') {
		exit('Error: Budget code required');
	}

	$sUpah = 'select jumlah from ' . $dbname . '.bgt_upah where tahunbudget=\'' . $thnBudget . '\' and kodeorg=\'' . substr($kdBlok, 0, 4) . '\' and golongan=\'' . $kdGol . '\' and closed=1';

	#exit(mysql_error($conn));
	($qUpah = mysql_query($sUpah)) || true;
	$row = mysql_num_rows($qUpah);

	if ($row != 0) {
		$rUpah = mysql_fetch_assoc($qUpah);

		if ($rUpah['jumlah'] == '') {
			exit('Error:Data upah belum ada, silahkan cek kembali');
		}
		else {
			$totalUpah = floatval($rUpah['jumlah']) * floatval($jmlhPerson);
			echo $totalUpah;
		}
	}
	else {
		exit('Error: Data does not close yet, please verify');
	}

	break;

case 'saveSdm':
	if (($kdGol == '') || ($totBiaya == 0) || ($jmlhPerson == '')) {
		exit('Error:Fields are required');
	}

	$sCek = 'select * from ' . $dbname . '.bgt_budget where ' . $where . ' and kodebudget=\'' . $kdGol . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$volume = $volKeg * $rotThn;
		$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,updateby,jumlah,satuanj) values' . "\r\n" . '                  (\'' . $thnBudget . '\',\'' . $kdBlok . '\',\'' . $tpBudget . '\',\'' . $kdGol . '\',\'' . $kegId . '\',\'' . $noAkun . '\',\'' . $volume . '\',\'' . $satuan . '\',\'' . $totBiaya . '\',\'' . $rotThn . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $jmlhPerson . '\',\'HK\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
		}
	}
	else {
		exit('Error:Data already exist');
	}

	break;

case 'saveMat':
	if (($kdBudget == '') || ($totHarga == 0) || ($jmlhBrg == '') || ($kdBrg == '')) {
		exit('Error:Fields are required');
	}

	$sCek = 'select * from ' . $dbname . '.bgt_budget where ' . $where . ' and kodebarang=\'' . $kdBrg . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sRegion = 'select distinct regional from ' . $dbname . '.bgt_regional_assignment where kodeunit=\'' . substr($kdBlok, 0, 4) . '\'';

		#exit(mysql_error($conn));
		($qRegion = mysql_query($sRegion)) || true;
		$rRegion = mysql_fetch_assoc($qRegion);
		$volume = $volKeg * $rotThn;
		$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv,rupiah,rotasi, kodebarang, regional, updateby,jumlah,satuanj) values' . "\r\n" . '                  (\'' . $thnBudget . '\',\'' . $kdBlok . '\',\'' . $tpBudget . '\',\'' . $kdBudget . '\',\'' . $kegId . '\',\'' . $noAkun . '\',\'' . $volume . '\',\'' . $satuan . '\',\'' . $totHarga . '\',\'' . $rotThn . '\',\'' . $kdBrg . '\',\'' . $rRegion['regional'] . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $jmlhBrg . '\',\'' . $satuanBrg . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
		}
	}
	else {
		exit('Error:Data already exist');
	}

	break;

case 'saveKontrak':
	if (($kdBudgetK == '') || ($totBiayaK == 0) || ($volKontrak == '')) {
		exit('Error: fields are required');
	}

	$sCek = 'select * from ' . $dbname . '.bgt_budget where ' . $where . ' and kodebudget like \'%KONTRAK%\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$volume = $volKeg * $rotThn;
		$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv,rupiah,rotasi,  updateby,jumlah,satuanj) values' . "\r\n" . '                  (\'' . $thnBudget . '\',\'' . $kdBlok . '\',\'' . $tpBudget . '\',\'' . $kdBudgetK . '\',\'' . $kegId . '\',\'' . $noAkun . '\',\'' . $volume . '\',\'' . $satuan . '\',\'' . $totBiayaK . '\',\'' . $rotThn . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $volKontrak . '\',\'' . $satKontrak . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
		}
	}
	else {
		exit('Error:Data already exist');
	}

	break;

case 'saveTool':
	if (($kdBudgetL == '') || ($totHargaL == 0) || ($jmlhBrgL == '') || ($kdBrgL == '')) {
		exit('Error:fields are required');
	}

	$sCek = 'select * from ' . $dbname . '.bgt_budget where ' . $where . ' and kodebudget=\'' . $kdBudgetL . '\' and kodebarang=\'' . $kdBrgL . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$volume = $volKeg * $rotThn;
		$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv,rupiah,rotasi, kodebarang, regional, updateby,jumlah,satuanj) values' . "\r\n" . '                  (\'' . $thnBudget . '\',\'' . $kdBlok . '\',\'' . $tpBudget . '\',\'' . $kdBudgetL . '\',\'' . $kegId . '\',\'' . $noAkun . '\',\'' . $volume . '\',\'' . $satuan . '\',\'' . $totHargaL . '\',\'' . $rotThn . '\',\'' . $kdBrgL . '\',\'' . $rRegion['regional'] . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $jmlhBrgL . '\',\'' . $satuanBrgL . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
		}
	}
	else {
		exit('Error:Data already exist');
	}

	break;

case 'saveKendaran':
	if (($kdBudgetV == '') || ($totBiayaKend == 0) || ($jmlhJam == '')) {
		exit('Error:Fields are required');
	}

	$sCekJam = 'select * from ' . $dbname . '.bgt_biaya_jam_ken_vs_alokasi where tahunbudget=\'' . $thnBudget . '\' and kodevhc=\'' . $kdVhc . '\'';

	#exit(mysql_error($conn));
	($qCekJam = mysql_query($sCekJam)) || true;
	$rCekJam = mysql_fetch_assoc($qCekJam);
	$sisa = $rCekJam['jamsetahun'] - $rCekJam['teralokasi'];

	if ($sisa < $jmlhJam) {
		exit('Error: Vehicle ' . $kdVhc . ' has been allocated: ' . $rCekJam['teralokasi'] . ' from total housr :' . $rCekJam['jamsetahun'] . ' can only allocate as remains:' . $sisa . '');
	}

	$sCek = 'select * from ' . $dbname . '.bgt_budget where ' . $where . ' and kodebudget like \'%VHC%\' and kodevhc=\'' . $kdVhc . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$volume = $volKeg * $rotThn;
		$sIns = 'insert into ' . $dbname . '.bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv,rupiah,rotasi, kodevhc, updateby,jumlah,satuanj) values' . "\r\n" . '                  (\'' . $thnBudget . '\',\'' . $kdBlok . '\',\'' . $tpBudget . '\',\'' . $kdBudgetV . '\',\'' . $kegId . '\',\'' . $noAkun . '\',\'' . $volume . '\',\'' . $satuan . '\',\'' . $totBiayaKend . '\',\'' . $rotThn . '\',\'' . $kdVhc . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $jmlhJam . '\',\'' . $satVhc . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . $sIns . "\n" . mysql_error($conn);
		}
	}
	else {
		exit('Error:Data already exist');
	}

	break;

case 'loadDataSdm':
	$sLoad = 'select tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,updateby,jumlah,satuanj,kunci from ' . $dbname . '.bgt_budget where ' . "\r\n" . '            ' . $where . ' and kodebudget like \'%SDM%\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kunci'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tahunbudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodeorg'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tipebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kegiatan'] . '</td>';
		$tab .= '<td align=\'right\'>' . $res['noakun'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['rotasi'] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['volume'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanv'] . '</td>';
		$tab .= '<td align=\'right\'>' . number_format($res['rupiah'], 2) . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['jumlah'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanj'] . '</td>';
		$tab .= '<td align=center style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteSdm(' . $res['kunci'] . ',1)" src=\'images/application/application_delete.png\'/></td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'getBarang':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                        <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                        <thead>' . "\r\n" . '                        <tr class=rowheader>' . "\r\n" . '                        <td>No.</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                        </tr><tbody>' . "\r\n" . '                        ';

	if ($nmBrg == '') {
		$nmBrg = $kdBarang;
	}

	$sLoad = 'select kodebarang,namabarang,satuan from ' . $dbname . '.log_5masterbarang where  kelompokbarang=\'' . substr($klmpkBrg, 2, 3) . '\' and (kodebarang like \'%' . $nmBrg . '%\'' . "\r\n" . '            or namabarang like \'%' . $nmBrg . '%\')';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent onclick="setData(\'' . $res['kodebarang'] . '\',\'' . $res['namabarang'] . '\',\'' . $res['satuan'] . '\')">';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $res['kodebarang'] . '</td>';
		$tab .= '<td>' . $res['namabarang'] . '</td>';
		$tab .= '<td>' . $res['satuan'] . '</td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'getBarangL':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                        <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                        <thead>' . "\r\n" . '                        <tr class=rowheader>' . "\r\n" . '                        <td>No.</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                        </tr><tbody>' . "\r\n" . '                        ';
	$sLoad = 'select kodebarang,namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang like \'%' . $nmBrgL . '%\'' . "\r\n" . '            or namabarang like \'%' . $nmBrgL . '%\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent onclick="setDataL(\'' . $res['kodebarang'] . '\',\'' . $res['namabarang'] . '\',\'' . $res['satuan'] . '\')">';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $res['kodebarang'] . '</td>';
		$tab .= '<td>' . $res['namabarang'] . '</td>';
		$tab .= '<td>' . $res['satuan'] . '</td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'getHarga':
	if (($jmlhBrg == '') || ($jmlhBrg == '0')) {
		exit('Material volume required');
	}

	$sRegion = 'select distinct regional from ' . $dbname . '.bgt_regional_assignment where kodeunit=\'' . substr($kdBlok, 0, 4) . '\' ';

	#exit(mysql_error($conn));
	($qRegion = mysql_query($sRegion)) || true;
	$rRegion = mysql_fetch_assoc($qRegion);
	$sHrg = 'select distinct hargasatuan from ' . $dbname . '.bgt_masterbarang where regional=\'' . $rRegion['regional'] . '\' and kodebarang=\'' . $kdBrg . '\' and tahunbudget=\'' . $thnBudget . '\' and closed=1';

	#exit(mysql_error($conn));
	($qHrg = mysql_query($sHrg)) || true;
	$row = mysql_num_rows($qHrg);

	if ($row != 0) {
		$rHrg = mysql_fetch_assoc($qHrg);
		if (($rHrg['hargasatuan'] != '') || ($rHrg['hargasatuan'] != '0')) {
			$hasil = floatval($rHrg['hargasatuan']) * floatval($jmlhBrg);
			echo $hasil;
		}
		else {
			exit('Error:Material price not found, please contact Purchase Dept.');
		}
	}
	else {
		exit('Error:Material price not found, please contact Purchase Dept.');
	}

	break;

case 'getHargaL':
	if (($jmlhBrgL == '') || ($jmlhBrgL == '0')) {
		exit('Material volume required');
	}

	$sRegion = 'select distinct regional from ' . $dbname . '.bgt_regional_assignment where kodeunit=\'' . substr($kdBlok, 0, 4) . '\' ';

	#exit(mysql_error($conn));
	($qRegion = mysql_query($sRegion)) || true;
	$rRegion = mysql_fetch_assoc($qRegion);
	$sHrg = 'select distinct hargasatuan from ' . $dbname . '.bgt_masterbarang where regional=\'' . $rRegion['regional'] . '\' and kodebarang=\'' . $kdBrgL . '\' and tahunbudget=\'' . $thnBudget . '\' and closed=1';

	#exit(mysql_error($conn));
	($qHrg = mysql_query($sHrg)) || true;
	$row = mysql_num_rows($qHrg);

	if ($row != 0) {
		$rHrg = mysql_fetch_assoc($qHrg);
		if (($rHrg['hargasatuan'] != '') || ($rHrg['hargasatuan'] != '0')) {
			$hasil = floatval($rHrg['hargasatuan']) * floatval($jmlhBrgL);
			echo $hasil;
		}
		else {
			exit('Error:Material price not found, please contact Purchase Dept.');
		}
	}
	else {
		exit('Error:Material price not found, please contact Purchase Dept.');
	}

	break;

case 'getBiaya':
	$sHrg = 'select distinct rpperjam from ' . $dbname . '.bgt_biaya_ken_per_jam where tahunbudget=\'' . $thnBudget . '\' and kodevhc=\'' . $kdVhc . '\'';

	#exit(mysql_error($conn));
	($qHrg = mysql_query($sHrg)) || true;
	$rHrg = mysql_fetch_assoc($qHrg);
	if (($rHrg['rpperjam'] != '') || ($rHrg['rpperjam'] != '0')) {
		$hasil = $rHrg['rpperjam'] * floatval($jmlhJam);
		echo $hasil;
	}
	else {
		exit('Error: Vehicle cost does not exist, please contact vehicle organizer');
	}

	break;

case 'delKodeblok':
	$sDel = 'delete from ' . $dbname . '.bgt_budget where tahunbudget=\'' . $thnBudget . '\' and kodeorg = \'' . $kdBlok . '\' and kegiatan = \'' . $kegId . '\' and tipebudget = \'ESTATE\' and kodebudget != \'UMUM\'';

	if (mysql_query($sDel)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . $sDel . "\n" . mysql_error($conn);
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.bgt_budget where kunci=\'' . $idData . '\'';

	if (mysql_query($sDel)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . $sDel . "\n" . mysql_error($conn);
	}

	break;

case 'loadDataMat':
	$sLoad = 'select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj,kodebarang from ' . $dbname . '.bgt_budget where ' . "\r\n" . '           ' . $where . ' and substring(kodebudget,1,1)=\'M\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kunci'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tahunbudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodeorg'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tipebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kegiatan'] . '</td>';
		$tab .= '<td align=\'right\'>' . $res['noakun'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['rotasi'] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['volume'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanv'] . '</td>';
		$tab .= '<td align=\'right\'>' . number_format($res['rupiah'], 2) . '</td>';
		$tab .= '<td align=\'center\'>' . $optNmBrg[$res['kodebarang']] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['jumlah'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanj'] . '</td>';
		$tab .= '<td align=center  style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteSdm(' . $res['kunci'] . ',2)" src=\'images/application/application_delete.png\'/></td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'loadDtLain':
	$sLoad = 'select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj from ' . $dbname . '.bgt_budget where ' . "\r\n" . '            ' . $where . ' and kodebudget like \'%KONTRAK%\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kunci'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tahunbudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodeorg'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tipebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kegiatan'] . '</td>';
		$tab .= '<td align=\'right\'>' . $res['noakun'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['rotasi'] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['volume'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanv'] . '</td>';
		$tab .= '<td align=\'right\'>' . number_format($res['rupiah'], 2) . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['jumlah'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanj'] . '</td>';
		$tab .= '<td align=center  style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteSdm(' . $res['kunci'] . ',4)" src=\'images/application/application_delete.png\'/></td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'loadDataTool':
	$sLoad = 'select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj,kodebarang from ' . $dbname . '.bgt_budget where ' . "\r\n" . '           ' . $where . '  and kodebudget=\'TOOL\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kunci'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tahunbudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodeorg'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tipebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kegiatan'] . '</td>';
		$tab .= '<td align=\'right\'>' . $res['noakun'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['rotasi'] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['volume'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanv'] . '</td>';
		$tab .= '<td align=\'right\'>' . number_format($res['rupiah'], 2) . '</td>';
		$tab .= '<td align=\'center\'>' . $optNmBrg[$res['kodebarang']] . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['jumlah'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanj'] . '</td>';
		$tab .= '<td align=center  style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteSdm(' . $res['kunci'] . ',3)" src=\'images/application/application_delete.png\'/></td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'loadDataKend':
	$sLoad = 'select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, kodevhc,  rupiah,rotasi,jumlah, satuanj from ' . $dbname . '.bgt_budget where ' . "\r\n" . '           ' . $where . ' and kodebudget like \'%VHC%\'';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kunci'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tahunbudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodeorg'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['tipebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodebudget'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kegiatan'] . '</td>';
		$tab .= '<td align=\'right\'>' . $res['noakun'] . '</td>';
		$tab .= '<td align=\'center\'>' . $res['kodevhc'] . '</td>';
		$tab .= '<td align=\'right\'>' . number_format($res['rupiah'], 2) . '</td>';
		$tab .= '<td  align=\'right\'>' . $res['jumlah'] . '</td>';
		$tab .= '<td  align=\'center\'>' . $res['satuanj'] . '</td>';
		$tab .= '<td align=center  style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteSdm(' . $res['kunci'] . ',5)" src=\'images/application/application_delete.png\'/></td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'setKdBrg':
	echo substr($klmpkBrg, 2, 3);
	break;

case 'loadDetailTotal':
	$optBlok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sBlok = 'select distinct kodeblok from ' . $dbname . '.bgt_blok where kodeblok like \'' . $_SESSION['empl']['lokasitugas'] . '%\'order by kodeblok asc';

	#exit(mysql_error());
	($qBlok = mysql_query($sBlok)) || true;

	while ($rBlok = mysql_fetch_assoc($qBlok)) {
		if ($kdBlok != '') {
			$optBlok .= '<option value=\'' . $rBlok['kodeblok'] . '\' ' . ($rBlok['kodeblok'] == $kdBlok ? 'selected' : '') . '>' . $rBlok['kodeblok'] . '</option>';
		}
		else {
			$optBlok .= '<option value=\'' . $rBlok['kodeblok'] . '\'>' . $rBlok['kodeblok'] . '</option>';
		}
	}

	if ($thnbudgetHeader != '') {
		$whereCari .= ' and tahunbudget=\'' . $thnbudgetHeader . '\'';
	}

	if ($noakunCari != '') {
		$whereCari .= ' and noakun=\'' . $noakunCari . '\'';
	}

	if ($afd != '') {
		$whereCari .= ' and substr(kodeorg,1,6)=\'' . $afd . '\'';
		$optBlok = '';
		$optBlok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
		$sBlok = 'select distinct kodeblok from ' . $dbname . '.bgt_blok where kodeblok like \'' . $afd . '%\'order by kodeblok asc';

		#exit(mysql_error());
		($qBlok = mysql_query($sBlok)) || true;

		while ($rBlok = mysql_fetch_assoc($qBlok)) {
			$optBlok .= '<option value=\'' . $rBlok['kodeblok'] . '\'>' . $rBlok['kodeblok'] . '</option>';
		}
	}
	else if ($kdBlok != '') {
		$whereCari .= ' and kodeorg like \'' . $kdBlok . '%\'';
	}

	$optPage = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sLoad = 'select * from ' . $dbname . '.bgt_budget_detail where kodeorg like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and kodebudget!=\'UMUM\' and tipebudget=\'ESTATE\' ' . $whereCari . '';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;
	$rAngka = mysql_num_rows($qLoad);
	@$totalPage = $rAngka / 50;
	$starAwal = 1;

	while ($starAwal <= $totalPage) {
		$optPage .= '<option value=\'' . $starAwal . '\' ' . ($starAwal == $_POST['page'] ? 'selected' : '') . '>' . $starAwal . '</option>';
		++$starAwal;
	}

	if ($rAngka != 0) {
		$limit = 50;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;
		$sLoad = 'select * from ' . $dbname . '.bgt_budget_detail where kodeorg like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and kodebudget!=\'UMUM\' and tipebudget=\'ESTATE\' ' . $whereCari . ' limit ' . $offset . ',' . $limit . '';

		#exit(mysql_error($conn));
		($qLoad = mysql_query($sLoad)) || true;

		while ($res = mysql_fetch_assoc($qLoad)) {
			$no += 1;
			$dtClik = 'onclick="getForm(\'Sebaran\',\'<fieldset style=\\\'width:520px;height:400px;\\\'><legend>Sebaran Per Bulan</legend><div id=containerForm style=\\\'overflow:auto;height:450px;width:480px\\\'></div><input type=hidden id=keyId value=' . $res['kunci'] . ' /></fieldset>\',' . $res['rupiah'] . ',' . $res['jumlah'] . ',\'' . $res['kodebudget'] . '\',event);"';
			$tab .= '<tr class=rowcontent style=\'cursor:pointer;\' id=baris' . $no . '>';
			$tab .= '<td><input type=checkbox onclick=sebarkanBoo(\'' . $res['kunci'] . '\',' . $no . ',this,' . $res['rupiah'] . ',' . $res['jumlah'] . '); title=\'Sebarkan sesuai proporsi diatas\'></td>';
			$tab .= '<td >' . $no . '</td>';
			$tab .= '<td align=\'center\' ' . $dtClik . '>' . $res['kodeorg'] . '</td>';
			$tab .= '<td align=\'center\' ' . $dtClik . '>' . $res['kodebudget'] . '</td>';
			$tab .= '<td align=\'center\' ' . $dtClik . '>' . $optNmKeg[$res['kegiatan']] . '</td>';
			$tab .= '<td align=\'center\' ' . $dtClik . '>' . $optNmBrg[$res['kodebarang']] . '</td>';
			$tab .= '<td align=\'right\' ' . $dtClik . '>' . $res['kodevhc'] . '</td>';
			$tab .= '<td align=\'center\' ' . $dtClik . '>' . number_format($res['rupiah'], 2) . '</td>';

			foreach ($arrBln as $brsBln => $listData) {
				if (strlen($brsBln) < 2) {
					$b = '0' . $brsBln;
				}
				else {
					$b = $brsBln;
				}

				$tab .= '<td align=\'right\'>' . number_format($res['rp' . $b], 2) . '</td>';
			}

			$tab .= '<td align=center  style=\'cursor:pointer;\'><img src="images/zoom.png" class="resicon" title=\'sebarang_' . $res['kunci'] . '\' ' . $dtClik . ' /></td>';
			$tab .= '</tr>';
		}
	}
	else {
		$tab .= '<tr class=rowcontent style=\'cursor:pointer;\' id=baris' . $no . '>';
		$tab .= '<td colspan=21>' . $_SESSION['lang']['dataempty'] . '</td>';
		$tab .= '</tr>';
	}

	$page == 0 ? $page = 1 : $page = $page;
	$totalPage == 0 ? $totalPage = 1 : $totalPage = $totalPage;
	echo $tab . '<input type=hidden id=jmlhRow value=' . $no . ' />' . '###' . $optPage . '###' . number_format($totalPage, 0) . '###' . $page . '###' . $optBlok;
	break;

case 'getForm':
	$rupiah = $_POST['rupiah'];
	$jumlah = $_POST['jumlah'];
	$kodebudget = $_POST['kodebudget'];
	$sCek = 'select distinct rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12,fis01, fis02, fis03, fis04, fis05, fis06, fis07, fis08, fis09, fis10, fis11, fis12' . "\r\n" . '                from ' . $dbname . '.bgt_distribusi where kunci=\'' . $keyId . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sData = 'select rupiah,jumlah from ' . $dbname . '.bgt_budget_detail where kunci=\'' . $keyId . '\'';

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;
		$rData = mysql_fetch_assoc($qData);
		@$totRupiah = $rData['rupiah'] / 12;
		@$totFisik = $rData['jumlah'] / 12;
		$a = 1;

		while ($a <= 12) {
			$totRupiahArr[$a] = number_format($totRupiah, 2, '.', '');
			$totFisikArr[$a] = number_format($totFisik, 2, '.', '');
			++$a;
		}
	}
	else {
		$res = mysql_fetch_assoc($qCek);
		$a = 1;

		while ($a <= 12) {
			if (strlen($a) < 2) {
				$b = '0' . $a;
			}
			else {
				$b = $a;
			}

			$totRupiahArr[$a] = $res['rp' . $b];
			$totFisikArr[$a] = $res['fis' . $b];
			++$a;
		}

		$c = 1;

		while ($c <= 12) {
			$cekFisik += $totFisikArr[$c];
			$cekRupiah += $totRupiahArr[$c];
			++$c;
		}

		if ($cekFisik == 0) {
			$sData = 'select rupiah,jumlah from ' . $dbname . '.bgt_budget_detail where kunci=\'' . $keyId . '\'';

			#exit(mysql_error($conn));
			($qData = mysql_query($sData)) || true;
			$rData = mysql_fetch_assoc($qData);
			@$totFisik = $rData['jumlah'] / 12;
			$a = 1;

			while ($a <= 12) {
				$totFisikArr[$a] = number_format($totFisik, 2, '.', '');
				++$a;
			}
		}

		if ($cekRupiah == 0) {
			$sData = 'select rupiah,jumlah from ' . $dbname . '.bgt_budget_detail where kunci=\'' . $keyId . '\'';

			#exit(mysql_error($conn));
			($qData = mysql_query($sData)) || true;
			$rData = mysql_fetch_assoc($qData);
			@$totRupiah = $rData['rupiah'] / 12;
			$a = 1;

			while ($a <= 12) {
				$totRupiahArr[$a] = number_format($totRupiah, 2, '.', '');
				++$a;
			}
		}
	}

	$tot = count($arrBln);

	if ($tot == 0) {
		exit('Error: Data not found');
	}

	$tab .= '<p align=center><table width=100%><tr><td><fieldset><legend>Rupiah:' . $kodebudget . '</legend>' . "\r\n" . '                   <table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
	$tab .= '<tr class=rowheader><td>Rp/Thn</td></td><td>%</td>' . "\r\n" . '                   <td>' . number_format($rupiah, 0) . '</td></tr></thead><tbody>';

	foreach ($arrBln as $rpBln => $listRpBln) {
		@$hasilBlnan = $totRupiahArr[$rpBln] / $rupiah;
		$tab .= '<tr class=rowcontent><td>' . $listRpBln . '</td>' . "\r\n" . '                     <td><input type=text class=myinputtextnumber size=3 onkeypress="return angka_doang(event);" id=rppersen' . $rpBln . ' onblur=ubahNilai(this.value,\'' . $rupiah . '\',\'rupiah_\',\'' . $jumlah . '\') value=' . number_format($hasilBlnan * 100, 2, '.', '') . '></td>';
		$tab .= '<td><input type=\'text\' id=rupiah_' . $rpBln . ' class="myinputtextnumber" style="width:75px;" value=' . $totRupiahArr[$rpBln] . ' /></td>' . "\r\n" . '                      </tr>';
	}

	$tab .= '<tr class=rowcontent><td  colspan=3 align=center style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'Simpan\' class=zImgBtn onclick="saveRupiah(' . $tot . ')" src=\'images/save.png\'/>&nbsp;&nbsp;<img id=\'detail_add\' title=\'Clear Form\' class=zImgBtn  width=\'16\' height=\'16\'  onclick="clearRupiah()" src=\'images/clear.png\'/></td>';
	$tab .= '</tr></tbody></table></fieldset></td><td>';
	$tab .= '<fieldset><legend>Fisik:' . $kodebudget . '</legend>' . "\r\n" . '                <table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
	$tab .= '<tr class=rowheader><td>Fisik/Thn</td><td>%</td>' . "\r\n" . '                  <td>' . number_format($jumlah, 0) . '</td></tr></thead><tbody>';

	foreach ($arrBln as $fisikBln => $listFisikBln) {
		@$hsilFIsik = $totFisikArr[$fisikBln] / $jumlah;
		$tab .= '<tr class=rowcontent><td>' . $listFisikBln . '</td>' . "\r\n" . '                       <td><input type=text class=myinputtextnumber size=3 onkeypress="return angka_doang(event);" id=fispersen' . $fisikBln . ' onblur=ubahNilai(this.value,\'' . $jumlah . '\',\'fisik_\') value=' . number_format(@$hsilFIsik * 100, 2, '.', '') . '></td>';
		$tab .= '<td><input type=\'text\' id=fisik_' . $fisikBln . ' class="myinputtextnumber" style="width:65px;" value=' . $totFisikArr[$fisikBln] . ' /></td>' . "\r\n" . '                      </tr>';
	}

	$tab .= '<tr class=rowcontent><td colspan=3 align=center style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'Simpan\' class=zImgBtn onclick="saveFisik(' . $tot . ')" src=\'images/save.png\'/>&nbsp;&nbsp;<img id=\'detail_add\' title=\'Clear Form\' class=zImgBtn  width=\'16\' height=\'16\'  onclick="clearFisik()" src=\'images/clear.png\'/></td>';
	$tab .= '</tr></tbody></table></fieldset></td></table></p>';
	$tab .= '<p align=center><button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick=\'closForm()\'   >' . $_SESSION['lang']['close'] . '</button></p>';
	echo $tab;
	break;

case 'saveRupiah':
	$sCek = 'select distinct rupiah,jumlah from ' . $dbname . '.bgt_budget_detail where kunci=\'' . $keyId . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_assoc($qCek);
	$a = 1;

	while ($a <= $totRow) {
		if ($_POST['arrRup'][$a] == '') {
			$_POST['arrRup'][$a] = 0;
		}

		$totalSumRup += $_POST['arrRup'][$a];
		++$a;
	}

	if ($rCek['rupiah'] < $totalSumRup) {
		exit('Error:Total monthly greater than total a year');
	}

	if (($totalSumRup == 0) || ($totalSumRup == '')) {
		exit('Error: Total  must greater than 0');
	}

	$sCek = 'select distinct * from ' . $dbname . '.bgt_distribusi where kunci=\'' . $keyId . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sInsert = 'insert into ' . $dbname . '.bgt_distribusi  (kunci, updateby, rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12)';
		$sInsert .= ' values (\'' . $keyId . '\',\'' . $_SESSION['standard']['userid'] . '\'';
		$a = 1;

		while ($a <= $totRow) {
			$sInsert .= ',\'' . str_replace(',', '', $_POST['arrRup'][$a]) . '\'';
			++$a;
		}

		$sInsert .= ')';

		if (!mysql_query($sInsert)) {
			echo ' Gagal,_' . $sInsert . '__' . mysql_error($conn);
		}
	}
	else {
		$sUpdate = 'update ' . $dbname . '.bgt_distribusi set updateby=\'' . $_SESSION['standard']['userid'] . '\' ';
		$a = 1;

		while ($a <= $totRow) {
			if (strlen($a) == '1') {
				$c = '0' . $a;
			}
			else {
				$c = $a;
			}

			$sUpdate .= ' ,rp' . $c . '=\'' . $_POST['arrRup'][$a] . '\'';
			++$a;
		}

		$sUpdate .= ' where kunci=\'' . $keyId . '\'';

		if (!mysql_query($sUpdate)) {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}

	break;

case 'saveFisik':
	$sCek = 'select distinct rupiah,jumlah from ' . $dbname . '.bgt_budget_detail where kunci=\'' . $keyId . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_assoc($qCek);
	$a = 1;

	while ($a <= $totRow) {
		if ($_POST['arrFisik'][$a] == '') {
			$_POST['arrFisik'][$a] = 0;
		}

		$totalSumFisik += $_POST['arrFisik'][$a];
		++$a;
	}

	if (($totalSumFisik == 0) || ($totalSumFisik == '')) {
		exit('Error: Total  must greater than 0');
	}

	$sCek = 'select distinct * from ' . $dbname . '.bgt_distribusi where kunci=\'' . $keyId . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sInsert = 'insert into ' . $dbname . '.bgt_distribusi  (kunci, updateby, fis01, fis02, fis03, fis04, fis05, fis06, fis07, fis08, fis09, fis10, fis11, fis12)';
		$sInsert .= ' values (\'' . $keyId . '\',\'' . $_SESSION['standard']['userid'] . '\'';
		$a = 1;

		while ($a <= $totRow) {
			$sInsert .= ',\'' . str_replace(',', '', $_POST['arrFisik'][$a]) . '\'';
			++$a;
		}

		$sInsert .= ')';

		if (!mysql_query($sInsert)) {
			echo ' Gagal,_' . $sInsert . '__' . mysql_error($conn);
		}
	}
	else {
		$sUpdate = 'update ' . $dbname . '.bgt_distribusi set updateby=\'' . $_SESSION['standard']['userid'] . '\' ';
		$a = 1;

		while ($a <= $totRow) {
			if (strlen($a) == '1') {
				$c = '0' . $a;
			}
			else {
				$c = $a;
			}

			$sUpdate .= ' ,fis' . $c . '=\'' . $_POST['arrFisik'][$a] . '\'';
			++$a;
		}

		$sUpdate .= ' where kunci=\'' . $keyId . '\'';

		if (!mysql_query($sUpdate)) {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}

	break;

case 'getDetailData':
	$tmbh = '';

	if ($thnbudgetHeader != '') {
		$tmbh = ' and tahunbudget=\'' . $thnbudgetHeader . '\'';
	}

	if ($noakunCari != '') {
		$tmbh .= ' and noakun =\'' . $noakunCari . '\'';
	}

	if ($kdBlok != '') {
		$blok = 'kodeorg=\'' . $kdBlok . '\'';
	}
	else {
		$blok = 'kodeorg like \'%' . $_SESSION['empl']['lokasitugas'] . '%\'';
	}

	$tab = '<table cellspacing=1 cellpadding=1 class=sortable border=0><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td>No.</td>';
	$tab .= '<td>' . $_SESSION['lang']['budgetyear'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['tipe'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kegiatan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rotasi'] . '/' . $_SESSION['lang']['tahun'] . '</td>';
	$tab .= '<td colspan=3>Action</td>';
	$tab .= '</tr></thead>';
	$tab .= '<tbody>';
	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$sql2 = 'select * from ' . $dbname . '.bgt_budget where ' . $blok . ' and kodebudget!=\'UMUM\' ' . $tmbh . '  group by tahunbudget,kodeorg,tipebudget, kegiatan, noakun order by tahunbudget desc ';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;
	$jlhbrs = mysql_num_rows($query2);

	if ($jlhbrs != 0) {
		$sData = 'select tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv,rotasi,tutup from ' . $dbname . '.bgt_budget where ' . $blok . ' and tipebudget=\'ESTATE\'  and kodebudget!=\'UMUM\' ' . $tmbh . '  group by tahunbudget,kodeorg,tipebudget, kegiatan, noakun order by tahunbudget desc limit ' . $offset . ',' . $limit . ' ';

		#exit(mysql_error());
		($qData = mysql_query($sData)) || true;

		while ($rData = mysql_fetch_assoc($qData)) {
			$no += 1;
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $no . '</td>';
			$tab .= '<td>' . $rData['tahunbudget'] . '</td>';
			$tab .= '<td>' . $rData['tipebudget'] . '</td>';
			$tab .= '<td>' . $rData['kodeorg'] . '</td>';
			$tab .= '<td>' . $rData['noakun'] . '</td>';
			$tab .= '<td>' . $optNmKeg[$rData['kegiatan']] . '</td>';
			$tab .= '<td align=right>' . $rData['volume'] . '</td>';
			$tab .= '<td>' . $rData['satuanv'] . '</td>';
			$tab .= '<td align=right>' . $rData['rotasi'] . '</td>';

			if ($rData['tutup'] == 0) {
				$tab .= '<td  align=center style=\'cursor:pointer;\'>' . "\r\n" . '                            <img id=\'detail_edit\' title=\'Edit\' class=zImgBtn onclick="filFieldHead(\'' . $rData['tahunbudget'] . '\',\'' . $rData['kodeorg'] . '\',\'' . $rData['tipebudget'] . '\',\'' . $rData['noakun'] . '\',\'' . $rData['kegiatan'] . '\',\'' . $rData['volume'] . '\',\'' . $rData['satuanv'] . '\',\'' . $rData['rotasi'] . '\')" src=\'images/application/application_edit.png\'/>' . "\r\n" . '                        </td>';
				$tab .= '<td  align=center style=\'cursor:pointer;\'>' . "\r\n" . '                            <img id=\'detail_copy\' title=\'Copy\' class=zImgBtn onclick="viewOtherBlok(\'' . $rData['tahunbudget'] . '\',\'' . $rData['kodeorg'] . '\',\'' . $rData['tipebudget'] . '\',\'' . $rData['noakun'] . '\',\'' . $rData['kegiatan'] . '\',\'' . $rData['volume'] . '\',\'' . $rData['satuanv'] . '\',\'' . $rData['rotasi'] . '\',event);" src=\'images/application/application_cascade.png\'/>' . "\r\n" . '                        </td>';

				if ($rData['tutup'] != 1) {
					$tab .= '<td  align=center style=\'cursor:pointer;\'>' . "\r\n" . '                            <img id=\'detail_del\' title=\'Delete\' class=zImgBtn onclick="delFieldHead(\'' . $rData['tahunbudget'] . '\',\'' . $rData['kodeorg'] . '\',\'' . $rData['kegiatan'] . '\')" src=\'images/application/application_delete.png\'/>' . "\r\n" . '                        </td>';
				}
				else {
					$tab .= '<td  align=center style=\'cursor:pointer;\'>' . "\r\n" . '                            ' . $_SESSION['lang']['tutup'] . "\r\n" . '                        </td>';
				}
			}
			else {
				$tab .= '<td colspan=3>' . $_SESSION['lang']['tutup'] . '</td>';
			}

			$tab .= '</tr>';
		}

		$tab .= "\r\n\t\t" . '<tr class=rowheader><td colspan=10 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariTrans(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariTrans(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	}
	else {
		$tab .= '<tr class=rowcontent><td colspan=10>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}

	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'closeBudget':
	if ($thnBudget == '') {
		exit('Error: Budget year required');
	}

	$strx = 'select noakun,kodeorg' . "\r\n" . '                            FROM bgt_budget_detail where tahunbudget=' . $thnBudget . ' and kodeorg like \'' . $_SESSION['empl']['lokasitugas'] . '%\'' . "\r\n" . '                            and abs((rp01+rp02+rp03+rp04+rp05+rp06+rp07+rp08+rp09+rp10+rp11+rp12)-rupiah)>90';
	$res = mysql_query($strx);

	if (0 < mysql_num_rows($res)) {
		$cap = 'Sebaran masih salah untuk data dibawah:' . "\n";

		while ($bar = mysql_fetch_object($res)) {
			$cap += 'Noakun:' . $bar->noakun . ' | Kegiatan:' . $bar->kodeorg . "\n";
		}

		exit(' Error: ' . $cap);
	}

	$sQl = 'select distinct tutup from ' . $dbname . '.bgt_budget where ' . $where2 . ' and tutup=1';

	#exit(mysql_error($conn));
	($qQl = mysql_query($sQl)) || true;
	$row = mysql_num_rows($qQl);

	if ($row != 1) {
		$sUpdate = 'update ' . $dbname . '.bgt_budget set tutup=1 where ' . $where2 . '';

		if (mysql_query($sUpdate)) {
			echo '';
		}
		else {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}
	else {
		exit('Error: Data has been closed');
	}

	break;

case 'getThnBudget':
	$optThnTtp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sThn = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget where kodeorg like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and tipebudget=\'ESTATE\' and tutup=0 order by tahunbudget desc';

	#exit(mysql_error($conn));
	($qThn = mysql_query($sThn)) || true;

	while ($rThn = mysql_fetch_assoc($qThn)) {
		$optThnTtp .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
	}

	echo $optThnTtp;
	break;

case 'sebarDoong':
	$var1 = $_POST['var1'];
	$var2 = $_POST['var2'];
	$var3 = $_POST['var3'];
	$var4 = $_POST['var4'];
	$var5 = $_POST['var5'];
	$var6 = $_POST['var6'];
	$var7 = $_POST['var7'];
	$var8 = $_POST['var8'];
	$var9 = $_POST['var9'];
	$var10 = $_POST['var10'];
	$var11 = $_POST['var11'];
	$var12 = $_POST['var12'];
	$rupiah = $_POST['rupe'];
	$fis = $_POST['fis'];
	$kunci = $_POST['kunci'];
	$str = 'delete from ' . $dbname . '.bgt_distribusi where kunci=' . $kunci;
	mysql_query($str);
	$str = 'insert into ' . $dbname . '.bgt_distribusi (kunci, rp01, fis01, rp02, fis02, rp03, fis03, rp04, fis04, rp05, fis05, rp06, fis06, rp07, fis07, rp08, fis08, rp09, fis09, rp10, fis10, rp11, fis11, rp12, fis12, updateby)' . "\r\n" . '              values(' . $kunci . ',  ' . "\r\n" . '                       ' . ($var1 * $rupiah) . ',' . "\r\n" . '                       ' . ($var1 * $fis) . ',' . "\r\n" . '                       ' . ($var2 * $rupiah) . ',' . "\r\n" . '                       ' . ($var2 * $fis) . ',' . "\r\n" . '                       ' . ($var3 * $rupiah) . ',' . "\r\n" . '                       ' . ($var3 * $fis) . ',' . "\r\n" . '                       ' . ($var4 * $rupiah) . ',' . "\r\n" . '                       ' . ($var4 * $fis) . ',' . "\r\n" . '                       ' . ($var5 * $rupiah) . ',' . "\r\n" . '                       ' . ($var5 * $fis) . ',' . "\r\n" . '                       ' . ($var6 * $rupiah) . ',' . "\r\n" . '                       ' . ($var6 * $fis) . ',' . "\r\n" . '                       ' . ($var7 * $rupiah) . ',' . "\r\n" . '                       ' . ($var7 * $fis) . ',' . "\r\n" . '                       ' . ($var8 * $rupiah) . ',' . "\r\n" . '                       ' . ($var8 * $fis) . ',' . "\r\n" . '                       ' . ($var9 * $rupiah) . ',' . "\r\n" . '                       ' . ($var9 * $fis) . ',' . "\r\n" . '                       ' . ($var10 * $rupiah) . ',' . "\r\n" . '                       ' . ($var10 * $fis) . ',' . "\r\n" . '                       ' . ($var11 * $rupiah) . ',' . "\r\n" . '                       ' . ($var11 * $fis) . ',' . "\r\n" . '                       ' . ($var12 * $rupiah) . ',' . "\r\n" . '                       ' . ($var12 * $fis) . ',' . "\r\n" . '                       ' . $_SESSION['standard']['userid'] . ');';

	if (mysql_query($str)) {
	}
	else {
		echo 'Error;' . mysql_error($conn);
	}

	break;

case 'getLuas':
	$blok = $_POST['blok'];
	$str = 'select hathnini from ' . $dbname . '.bgt_blok where kodeblok=\'' . $blok . '\'';
	$res = mysql_query($str);
	$luas = 0;

	while ($bar = mysql_fetch_object($res)) {
		$luas = $bar->hathnini;
	}

	echo $luas;
	break;
}

?>
