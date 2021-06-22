<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$proses = $_POST['proses'];
$statInput = $_POST['statInput'];
$nopo = $_POST['nopo'];
$notransaksi = $_POST['notransaksi'];
$nodok = $_POST['nodok'];
$idsupplier = $_POST['idsupplier'];
$tanggal = tanggalsystem($_POST['tanggal']);
$nopo = $_POST['nopo'];
$penerimaId = $_POST['penerimaId'];
$mengetahuiId = $_POST['mengetahuiId'];
$qty = $_POST['qty'];
$kodebarang = $_POST['kodebarang'];
$kodegudang = $_POST['kodegudang'];
$post = 0;
$user = $_SESSION['standard']['userid'];
$satuan = $_POST['satuan'];
$arrStatus = array('Diterima', 'Dikirim');
$optPt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'tipe=\'PT\'');
$optSupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'kodekelompok=\'S001\'');
$optNama = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\'');
$tex = $_POST['tex'];

switch ($proses) {
case 'postingData':
	$sUpdate = 'update ' . $dbname . '.log_lpbht set post=\'1\',tipetransaksi=\'1\',postedby=\'' . $_SESSION['standard']['userid'] . '\' ' . "\r\n" . '                      where notransaksi=\'' . $notransaksi . '\'';

	if (mysql_query($sUpdate)) {
	}

	break;

case 'listData':
	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;

	if ($tex != '') {
		$dddCari = ' and notransaksi like \'%' . $tex . '%\'';
	}

	$sql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_lpbht where gudangx=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $dddCari . ' order by notransaksi desc ';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$sData = 'select distinct * from ' . $dbname . '.log_lpbht where gudangx=\'' . $_SESSION['empl']['lokasitugas'] . '\'  ' . $dddCari . ' ' . "\r\n" . '                order by notransaksi desc limit ' . $offset . ',20';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		++$dr;
		$namaposting = 'Not Posted';

		if (intval($rData['postedby']) != 0) {
			$stry = 'select namauser from ' . $dbname . '.user where karyawanid=\'' . $rData['postedby'] . '\'';
			$resy = mysql_query($stry);
			$bary = mysql_fetch_object($resy);
			$namaposting = $bary->namauser;
		}

		if (($namaposting == 'Not Posted') && ($rData['post'] == 1)) {
			$namaposting = ' Posted By ???';
		}

		if ($rData['post'] < 1) {
			$add = '<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editBapb(\'' . $rData['notransaksi'] . '\',\'' . $rData['nopo'] . '\',\'' . tanggalnormal($rData['tanggal']) . '\',\'' . $rData['idsupplier'] . '\');">';
			$add .= '&nbsp <img src=images/application/application_delete.png class=resicon  title=\'delete\' onclick="delBapb(\'' . $rData['notransaksi'] . '\');">';
			$add .= '&nbsp <img src=images/hot.png class=resicon  title=\'posting\' onclick="postData(\'' . $rData['notransaksi'] . '\');">';
		}
		else {
			$add = '';
		}

		$tab .= '<tr class=rowcontent><td>' . $dr . '</td>';
		$tab .= '<td>' . $arrStatus[$rData['tipetransaksi']] . '</td>';
		$tab .= '<td>' . $rData['notransaksi'] . '</td>';
		$tab .= '<td>' . tanggalnormal($rData['tanggal']) . '</td>';
		$tab .= '<td>' . $rData['nopo'] . '</td>';
		$tab .= '<td>' . $optSupplier[$rData['idsupplier']] . '</td>';
		$tab .= '<td>' . $optNama[$rData['user']] . '</td>';
		$tab .= '<td>' . $namaposting . '</td>';
		$tab .= '<td align=center>' . "\r\n" . '             ' . $add . "\r\n" . '             <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="previewBapb(\'' . $rData['notransaksi'] . '\',event);"> ' . "\r\n" . '          </td>';
		$tab .= '</tr>';
	}

	$tab .= '<tr><td colspan=11 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '           <br>' . "\r\n" . '       <button class=mybutton onclick=cariBapb(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '           <button class=mybutton onclick=cariBapb(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '           </td>' . "\r\n" . '           </tr>';
	echo $tab;
	break;

case 'getPo':
	if ($statInput == 0) {
		$arrBln = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII');
		$bln = intval(date('m'));
		$thnskrng = date('Y');
		$ntrans = '/' . $arrBln[$bln] . '/' . date('Y') . '/BAPB/MA/' . $_SESSION['empl']['lokasitugas'];
		$sCek = 'select distinct notransaksi from ' . $dbname . '.log_lpbht where notransaksi like \'%' . $ntrans . '%\' order by notransaksi desc';

		#exit(mysql_error($conn));
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_fetch_assoc($qCek);
		$awal = substr($rCek['notransaksi'], 0, 3);
		$awal = intval($awal);
		$thn = substr($rCek['notransaksi'], -17, 4);

		if ($thn != $thnskrng) {
			$awal = 1;
		}
		else {
			$awal += 1;
		}

		$counter = addZero($awal, 3);
		$notrans = $counter . '/' . $arrBln[$bln] . '/' . date('Y') . '/BAPB/MA/' . $_SESSION['empl']['lokasitugas'];
		$sSupplier = 'select distinct kodesupplier from  ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

		#exit(mysql_error($conn));
		($qSupplier = mysql_query($sSupplier)) || true;
		$rSupplier = mysql_fetch_assoc($qSupplier);
	}

	$tab .= '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '             <thead>' . "\r\n" . '                 <tr class=rowheader>' . "\r\n" . '                   <td>No.</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['sudahditerima'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['kuantitaspo'] . '</td>' . "\t\t" . '   ' . "\r\n" . '                   <td>' . $_SESSION['lang']['diterima'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                   <td></td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </thead><tbody>' . "\r\n" . '                 ';
	$no = 0;
	$str = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $nopo . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$qtypo = $bar->jumlahpesan;
		$jumlah = $qtypo;
		$namabarang = '';
		$satuan = '';
		$str2 = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';
		$res2 = mysql_query($str2);

		while ($bar1 = mysql_fetch_object($res2)) {
			$namabarang = $bar1->namabarang;
			$satuan = $bar1->satuan;
		}

		if ($satuan != $bar->satuan) {
			$str1 = 'select jumlah from ' . $dbname . '.log_5stkonversi ' . "\r\n" . '                               where darisatuan=\'' . $satuan . '\' and satuankonversi=\'' . $bar->satuan . '\'' . "\r\n" . '                               and kodebarang=\'' . $bar->kodebarang . '\'';
			$res3 = mysql_query($str1);

			while ($bar2 = mysql_fetch_object($res3)) {
				$jumlah = round($qtypo / $bar2->jumlah);
			}
		}

		$jumlahlalu = 0;
		$sddt = '';
		$jumlahedit = 0;
		$strh = 'select jumlah from ' . $dbname . '.log_lpbdt where ' . "\r\n" . '                notransaksi=\'' . $notransaksi . '\'' . "\r\n" . '                        and kodebarang=\'' . $bar->kodebarang . '\'';
		$resh = mysql_query($strh);
		$barh = mysql_fetch_object($resh);
		$jumlahedit = $barh->jumlah;
		$strx = 'select sum(a.jumlah) as jumlah,a.kodebarang as kodebarang ' . "\r\n" . '            from ' . $dbname . '.log_lpbdt a,' . "\r\n" . '                 ' . $dbname . '.log_lpbht b' . "\r\n" . '                   where a.notransaksi=b.notransaksi ' . "\r\n" . '                   and b.nopo=\'' . $nopo . '\' ' . "\r\n" . '               and a.kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n" . '                   ' . $sddt . "\r\n" . '                   group by kodebarang';
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_object($resx)) {
			$jumlahlalu = $barx->jumlah;
		}

		$sisa = $jumlah - $jumlahlalu;

		if (($notransaksi != '') && ($jumlahedit == 0)) {
			$disab = 'disabled';
		}
		else if ($sisa <= 0) {
			$disab = 'disabled';
		}
		else {
			$disab = '';
		}

		$tab .= '<tr class=rowcontent>' . "\r\n" . '                   <td>' . $no . '</td>' . "\r\n" . '                   <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                   <td>' . $namabarang . '</td>' . "\r\n" . '                   <td id=\'sat' . $bar->kodebarang . '\'>' . $satuan . '</td>' . "\r\n" . '                   <td align=right>' . number_format($jumlahlalu, 2, '.', ',') . '</td><input type=hidden value=' . $jumlahlalu . ' id=\'jumlal' . $bar->kodebarang . '\'>' . "\r\n" . '                   <td align=right>' . number_format($jumlah, 2, '.', ',') . '</td><input type=hidden value=' . $jumlah . ' id=\'jumsek' . $bar->kodebarang . '\'>' . "\r\n" . '                   <td><input type=text ' . $disab . ' class=myinputtextnumber id=\'qty' . $bar->kodebarang . '\' onkeypress="return angka_doang(event);" value=\'' . $sisa . '\' size=7 maxlength=12 onblur=cekButton(this,\'btn' . $bar->kodebarang . '\')></td>' . "\r\n" . '                   <td>' . $bar->catatan . '</td>' . "\r\n" . '                   <td><button class=mybutton id=\'btn' . $bar->kodebarang . '\' onclick=saveItemPo(\'' . $bar->kodebarang . '\') ' . $disab . '>' . $_SESSION['lang']['save'] . '</button>';
	}

	$optmengetahui = '<option value=\'\'></option>';
	$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' or lokasitugas=\'' . $_SESSION['org']['induk'] . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$optmengetahui .= '<option value=\'' . $bar->karyawanid . '\'>' . $bar->namakaryawan . '</option>';
	}

	$tab .= '</tbody>' . "\r\n" . '             <tfoot>' . "\r\n" . '                 <tr>' . "\r\n" . '                   <td colspan=8 align=center>' . "\r\n" . '                   <button onclick=selesaiBapb() class=mybutton>' . $_SESSION['lang']['done'] . '</button>' . "\r\n" . '                   </td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </tfoot>' . "\r\n" . '                 </table>' . "\r\n" . '                 ';

	if ($statInput == '0') {
		echo $notrans . '###' . $tab . '###' . $rSupplier['kodesupplier'];
	}
	else {
		$sData = 'select distinct namapenerima,mengetahui from ' . $dbname . '.log_lpbht where notransaksi=\'' . $notransaksi . '\'';

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;
		$rData = mysql_fetch_assoc($qData);
		echo $tab . '###' . $rData['namapenerima'] . '###' . $rData['mengetahui'];
	}

	break;

case 'saveData':
	$status = 0;
	$str = 'select * from ' . $dbname . '.log_lpbht where notransaksi=\'' . $nodok . '\'';
	$res = mysql_query($str);

	if (mysql_num_rows($res) == 1) {
		$status = 1;
	}

	$str = 'select * from ' . $dbname . '.log_lpbdt where notransaksi=\'' . $nodok . '\'' . "\r\n" . '               and kodebarang=\'' . $kodebarang . '\'';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 2;
	}

	$str = 'select * from ' . $dbname . '.log_lpbht where notransaksi=\'' . $nodok . '\'' . "\r\n" . '               and post=1';

	if (0 < mysql_num_rows(mysql_query($str))) {
		$status = 3;
	}

	$sCek = 'select distinct a.notransaksi from ' . $dbname . '.log_transaksidt a ' . "\r\n" . '                    left join ' . $dbname . '.log_transaksiht b on a.notransaksi=b.notransaksi ' . "\r\n" . '                    where kodebarang=\'' . $kodebarang . '\' and b.nopo=\'' . $nopo . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if (0 < $rCek) {
		$status = 0;
	}

	$kurs = 1;
	$kodept = '';
	$str = 'select kodeorg,kurs from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$kodept = $bar->kodeorg;
		$kurs = $bar->kurs;
	}

	$str = 'select hargasatuan,jumlahpesan,satuan,matauang,kodebarang from ' . $dbname . '.log_podt where ' . "\r\n" . '              nopo=\'' . $nopo . '\' and kodebarang=\'' . $kodebarang . '\'';
	$res = mysql_query($str);
	$jumlahpesan = '';
	$hargasatuan = 0;
	$matauang = '';

	while ($bar = mysql_fetch_object($res)) {
		$matauang = $bar->matauang;
		$jumlahpesan = $bar->jumlahpesan;
		$hargasatuan = $bar->hargasatuan;

		if ($satuan != $bar->satuan) {
			$jlhkonversi = 1;
			$str1 = 'select jumlah from ' . $dbname . '.log_5stkonversi ' . "\r\n" . '                               where darisatuan=\'' . $satuan . '\' and satuankonversi=\'' . $bar->satuan . '\'' . "\r\n" . '                               and kodebarang=\'' . $bar->kodebarang . '\'';
			$res3 = mysql_query($str1);

			if (0 < mysql_num_rows($res3)) {
				while ($bar2 = mysql_fetch_object($res3)) {
					$jlhkonversi = $bar2->jumlah;
				}
			}

			if ($jlhkonversi != 0) {
				$hargasatuan = $bar->hargasatuan * $jlhkonversi;
			}
		}
	}

	if (($kurs == 0) || ($matauang == 'IDR')) {
		$kurs = 1;
	}

	$hargasatuan = $hargasatuan * $kurs;
	$jumlahlalu = 0;
	$str = 'select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi ' . "\r\n" . '            from ' . $dbname . '.log_lpbdt a,' . "\r\n" . '                 ' . $dbname . '.log_lpbht b' . "\r\n" . '                   where a.notransaksi=b.notransaksi and  ' . "\r\n" . '                   b.nopo=\'' . $nopo . '\' ' . "\r\n" . '                   and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                   and a.notransaksi=\'' . $nodok . '\'' . "\r\n" . '                   order by notransaksi desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jumlahlalu = $bar->jumlah;
	}

	if (($status == 0) || ($status == 1) || ($status == 2)) {
		$stro = 'select a.post from ' . $dbname . '.log_lpbht a' . "\r\n" . '               left join ' . $dbname . '.log_lpbdt b' . "\r\n" . '                   on a.notransaksi=b.notransaksi' . "\r\n" . '               where a.tanggal>' . $tanggal . ' and a.kodept=\'' . $kodept . '\'' . "\r\n" . '                   and b.kodebarang=\'' . $kodebarang . '\' and gudangx=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '                   and a.post=1';
		$reso = mysql_query($stro);

		if (0 < mysql_num_rows($reso)) {
			$status = 7;
			echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
			exit(0);
		}
	}

	if ($status == 0) {
		$str = 'insert into ' . $dbname . '.log_lpbht (' . "\r\n" . '                        `tipetransaksi`,`notransaksi`,`tanggal`,' . "\r\n" . '                        `kodept`,`nopo`,`gudangx`,`user`,' . "\r\n" . '                        `idsupplier`,`post`,`namapenerima`,`mengetahui`)' . "\r\n" . '                values(\'0\',\'' . $nodok . '\',' . $tanggal . ',' . "\r\n" . '                     \'' . $kodept . '\',\'' . $nopo . '\',\'' . $_SESSION['empl']['lokasitugas'] . '\',' . $user . ',' . "\r\n" . '                         \'' . $idsupplier . '\',' . $post . ',\'' . $penerimaId . '\',\'' . $mengetahuiId . '\'' . "\r\n" . '                )';

		if (mysql_query($str)) {
			$str = 'insert into ' . $dbname . '.log_lpbdt (' . "\r\n" . '                          `notransaksi`,`kodebarang`,' . "\r\n" . '                          `satuan`,`jumlah`,`jumlahlalu`)' . "\r\n" . '                          values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                          \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ')';

			if (mysql_query($str)) {
			}
		}
		else {
			echo ' Gagal,  (insert header on status 0)' . addslashes(mysql_error($conn));
		}
	}
	else if ($status == 1) {
		$str = 'insert into ' . $dbname . '.log_lpbdt (' . "\r\n" . '                          `notransaksi`,`kodebarang`,' . "\r\n" . '                          `satuan`,`jumlah`,`jumlahlalu`)' . "\r\n" . '                          values(\'' . $nodok . '\',\'' . $kodebarang . '\',' . "\r\n" . '                          \'' . $satuan . '\',' . $qty . ',' . $jumlahlalu . ')';

		if (mysql_query($str)) {
		}
		else {
			echo ' Gagal, (insert detail on status 1)' . addslashes(mysql_error($conn));
		}
	}
	else if ($status == 2) {
		$str = 'update ' . $dbname . '.log_lpbdt set' . "\r\n" . '                              `jumlah`=' . $qty . ',' . "\r\n" . '                                  `updateby`=' . $user . "\r\n" . '                                  where `notransaksi`=\'' . $nodok . '\'' . "\r\n" . '                                  and `kodebarang`=\'' . $kodebarang . '\'';
		mysql_query($str);

		if (mysql_affected_rows($conn) < 1) {
			echo ' Gagal, (update detail on status 2)' . addslashes(mysql_error($conn));
		}
		else {
			$notrxnext = '';
			$strc = 'select a.notransaksi as notrx from ' . $dbname . '.log_lpbdt a, ' . $dbname . '.log_lpbht b' . "\r\n" . '                                      where a.notransaksi= b.notransaksi ' . "\r\n" . '                                          and b.nopo=\'' . $nopo . '\'' . "\r\n" . '                                          and a.notransaksi>\'' . $nodok . '\'' . "\r\n" . '                                          and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                                          order by notrx asc limit 1';
			$resc = mysql_query($strc);

			while ($barc = mysql_fetch_object($resc)) {
				$notrxnext = $barc->notrx;
			}

			if ($notrxnext != '') {
				$str = 'update ' . $dbname . '.log_lpbdt set' . "\r\n" . '                                      `jumlahlalu`=' . $qty . ',' . "\r\n" . '                                          `updateby`=' . $user . "\r\n" . '                                          where `notransaksi`=\'' . $notrxnext . '\'' . "\r\n" . '                                          and `kodebarang`=\'' . $kodebarang . '\'';
				mysql_query($str);

				if (mysql_affected_rows($conn) < 1) {
				}
			}
		}
	}

	if ($status == 3) {
		echo ' Gagal: Data has been posted';
	}

	break;

case 'deleteData':
	$sDel = 'delete from ' . $dbname . '.log_lpbht where notransaksi=\'' . $notransaksi . '\'';

	if (mysql_query($sDel)) {
	}
	else {
		echo ' Gagal, Hapus Header ' . addslashes(mysql_error($conn));
	}

	break;
}

?>
