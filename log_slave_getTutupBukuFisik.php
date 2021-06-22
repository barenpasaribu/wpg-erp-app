<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$gudang = $_POST['gudang'];

if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
	$str = 'select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai' . "\r\n" . '    from ' . $dbname . '.organisasi a left join ' . $dbname . '.setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg' . "\r\n" . '    where a.kodeorganisasi =\'' . $gudang . '\' and a.tipe like \'GUDANG%\' and b.tutupbuku=0';
}
else {
	$str = 'select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai' . "\r\n" . '    from ' . $dbname . '.organisasi a left join ' . $dbname . '.setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg' . "\r\n" . '    where a.kodeorganisasi like \'' . $gudang . '%\' and a.tipe like \'GUDANG%\' and b.tutupbuku=0';
}

if (($_SESSION['empl']['lokasitugas'] == 'MRKE') || ($_SESSION['empl']['lokasitugas'] == 'SKSE')) {
	$str = 'select a.kodeorganisasi,a.namaorganisasi,b.periode,b.tanggalmulai,b.tanggalsampai' . "\r\n" . '    from ' . $dbname . '.organisasi a left join ' . $dbname . '.setup_periodeakuntansi b on a.kodeorganisasi=b.kodeorg' . "\r\n" . '    where a.kodeorganisasi like \'' . $gudang . '%\' and a.tipe like \'GUDANGTEMP%\' and b.tutupbuku=0';
}

$stream = 'Please choose storage location(warehouse):' . "\r\n" . '              <table class=sortable cellspacing=1 border=0>' . "\r\n" . '              <thead>' . "\r\n" . '               <tr class=rowheader>' . "\r\n" . '               <td>' . $_SESSION['lang']['kodegudang'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['namaorganisasi'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['tanggalmulai'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['tanggalsampai'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['pilih'] . '</td>     ' . "\r\n" . '               </tr>    ' . "\r\n" . '               </thead>';
$stream2 = 'Please recalculate(material):' . "\r\n" . '              <table class=sortable cellspacing=1 border=0>' . "\r\n" . '              <thead>' . "\r\n" . '               <tr class=rowheader>' . "\r\n" . '               <td>' . $_SESSION['lang']['gudang'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['saldoakhir'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['action'] . '</td>     ' . "\r\n" . '               </tr>    ' . "\r\n" . '               </thead>';
$no = 0;
$no2 = 0;
$res = mysql_query($str);
$maxRow = mysql_num_rows($res);
$adaerror = 0;

while ($bar = mysql_fetch_object($res)) {
	$str2 = 'SELECT *, (saldoawalqty+qtymasuk-qtykeluar) as pembanding' . "\r\n" . '        FROM ' . $dbname . '.log_5saldobulanan' . "\r\n" . '        WHERE kodegudang = \'' . $bar->kodeorganisasi . '\' and periode = \'' . $bar->periode . '\'' . "\r\n" . '        AND ( saldoawalqty + qtymasuk - qtykeluar - saldoakhirqty) != 0';
	$res2 = mysql_query($str2);

	while ($bar2 = mysql_fetch_object($res2)) {
		if (number_format($bar2->pembanding, 2) != number_format($bar2->saldoakhirqty, 2)) {
			$adaerror = 1;
			$no2 += 1;
			$stream2 .= '<tr class=rowcontent  id=guaikutaja_' . $no2 . '>' . "\r\n" . '               <td id=kodegud' . $no2 . '>' . $bar2->kodegudang . '</td>' . "\r\n" . '               <td id=kodebar' . $no2 . '>' . $bar2->kodebarang . '</td>' . "\r\n" . '               <td id=kodeper' . $no2 . '>' . $bar2->periode . '</td>' . "\r\n" . '               <td id=sawal_' . $no2 . '>' . $bar2->saldoawalqty . '</td>' . "\r\n" . '               <td id=qtymsk_' . $no2 . '>' . $bar2->qtymasuk . '</td>' . "\r\n" . '               <td id=qtyklr_' . $no2 . '>' . $bar2->qtykeluar . '</td>' . "\r\n" . '               <td id=salak_' . $no2 . '>' . $bar2->saldoakhirqty . '</td>' . "\r\n" . '               <td><button class=mybutton onclick=reklasDt(\'' . $bar2->kodebarang . '\',\'' . $bar2->kodegudang . '\',\'' . $bar2->periode . '\',\'' . $no2 . '\') >' . $_SESSION['lang']['rekalkulasi'] . '</button></td>    ' . "\r\n" . '               </tr>';
		}
	}

	$no += 1;
	$stream .= '<tr class=rowcontent  id=row' . $no . '>' . "\r\n" . '               <td id=kodeorg' . $no . '>' . $bar->kodeorganisasi . '</td>' . "\r\n" . '               <td>' . $bar->namaorganisasi . '</td>' . "\r\n" . '               <td id=periode' . $no . '>' . $bar->periode . '</td>' . "\r\n" . '               <td id=tanggalmulai' . $no . '>' . $bar->tanggalmulai . '</td>' . "\r\n" . '               <td id=tanggalsampai' . $no . '>' . $bar->tanggalsampai . '</td>' . "\r\n" . '               <td><input type=checkbox  id=pilihan' . $no . ' checked></td>    ' . "\r\n" . '               </tr>';
}

$stream .= '</tbody><tfoot></tfoot></table>' . "\r\n" . '<button onclick=saveSaldoFisik(' . $maxRow . ',this)>Proses</button>';
$stream2 .= '</tbody><tfoot></tfoot></table>' . "\r\n" . 'Please refresh after all material has been recalculated correctly (green).<br/><br/>' . "\r\n" . '<button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>Refresh</button>' . "\r\n";

if ($adaerror == 1) {
	echo $stream2;
}
else {
	echo $stream;
}

?>
