<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$method = $_POST['method'];
$notran = $_POST['notran'];
$txt = $_POST['txt'];
$hrini = date('Ymd');
$jumlahditerima = $_POST['jumlahditerima'];
$kodebarang = $_POST['kodebarang'];
$nmFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

switch ($method) {
case 'posting':
	$sekarang = date('Y-m-d');
	$i = 'update  ' . $dbname . '.log_konosemenht set posting=1,postingby=\'' . $_SESSION['standard']['userid'] . '\',tanggalterima=\'' . $sekarang . '\' where nokonosemen=\'' . $notran . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'savePenerimaan':
	$i = 'update ' . $dbname . '.`log_konosemendt`  set jumlahditerima=\'' . $jumlahditerima . '\' where nokonosemen=\'' . $notran . '\' and kodebarang=\'' . $kodebarang . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	echo $notran;
	break;

case 'getIsi':
	echo "\r\n\t\t\t\r\n\t\t\t" . '<fieldset style=float:left><legend>Posting</legend>' . "\r\n\t\t\t" . '<button class=mybutton onclick=posting(\'' . $notran . '\')>' . $_SESSION['lang']['posting'] . '</button>' . "\r\n\t\t\t" . '</fieldset>' . "\r\n\t\t\t" . '<br />' . "\r\n\t\t\t" . '<fieldset style=float:left><legend>' . $_SESSION['lang']['list'] . '</legend>' . "\r\n\t\t\t" . '<table cellspacing=1 border=0 class=\'sortable\'>' . "\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nokonosemen'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodept'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jenis'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . ' PO</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['diterima'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['save'] . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t" . '</thead>' . "\r\n\t\t" . '</tbody>';
	$i = 'select * from ' . $dbname . '.log_konosemendt where nokonosemen=\'' . $notran . '\'';

	#exit(mysql_error($conn));
	($n = mysql_query($i)) || true;

	while ($d = mysql_fetch_assoc($n)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['nokonosemen'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['kodept'] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $d['kodebarang'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $nmBarang[$d['kodebarang']] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['jenis'] . '</td>' . "\r\n\t\t\t\t" . '<td id=jumlah' . $no . '>' . $d['jumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td><input type=text id=jumlahditerima' . $no . ' value=' . $d['jumlahditerima'] . ' onkeypress="return angka_doang(event);" class=myinputtextnumber style="width:50px;"></td>' . "\r\n\t\t\t\t" . '<td>' . $d['satuanpo'] . '</td>' . "\r\n\t\t\t\t" . '<td><img src=images/icons/Grey/PNG/save.png class=resicon  title=\'update\' onclick="savePenerimaan(\'' . $d['nokonosemen'] . '\',\'' . $d['kodebarang'] . '\',' . $no . ');" ></td>' . "\r\n\r\n\t\t\t" . '</tr>' . "\r\n\t\t";
	}

	echo '</fieldset>';
	break;

case 'loadData':
	echo "\r\n\t\t\r\n\t\t" . '<table cellspacing=\'1\' border=\'0\' class=\'sortable\'>' . "\r\n\t\t\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nokonosemen'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nokonosemen'] . ' EXP</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodept'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['tanggalberangkat'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['shipper'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['vessel'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['franco'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['asalbarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['daftar'] . '</td>' . "\r\n\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t" . '</thead>' . "\r\n\t\t" . '<tbody>';
	$limit = 30;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$maxdisplay = $page * $limit;

	if ($txt != '') {
		$txt = 'and nokonosemen like \'%' . $txt . '%\'';
	}
	else {
		$txt = '';
	}

	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_konosemenht where postingkirim=\'1\'  ' . $txt . '   order by tanggal desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$ha = 'SELECT * FROM ' . $dbname . '.log_konosemenht where postingkirim=\'1\' ' . $txt . ' order by tanggal desc  limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($hi = mysql_query($ha)) || true;
	$no = $maxdisplay;

	while ($hu = mysql_fetch_assoc($hi)) {
		$no += 1;
		echo "\r\n\t\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['nokonosemen'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['nokonosemenexp'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['kodept'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['tanggal'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . tanggalnormal($hu['tanggalberangkat']) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['shipper'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['vessel'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $nmFranco[$hu['franco']] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['asalbarang'] . '</td>';

		if ($hu['posting'] == '0') {
			$post = '<td align=center><img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\' id=a class=resicon onclick=listBarang(\'' . $hu['nokonosemen'] . '\',\'' . $_SESSION['lang']['find'] . '\',event)></td>';
		}
		else {
			$post = '<td align=center>' . $_SESSION['lang']['posting'] . '</td>';
		}

		echo $post;
		echo '</tr>';
	}

	echo "\r\n\t\t" . '<tr class=rowheader><td colspan=18 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	echo '</tbody></table>';
	break;
}

?>
