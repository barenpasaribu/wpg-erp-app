<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$_POST['method'] == '' ? $method = $_GET['method'] : $method = $_POST['method'];
$notran = $_POST['notran'];
$pt = $_POST['pt'];
$tgl = tanggalsystem($_POST['tgl']);
$ket = $_POST['ket'];
$peti = $_POST['peti'];
$serah = $_POST['serah'];
$terima = $_POST['terima'];
$noPo = $_POST['noPo'];
$txtBarang = $_POST['txtBarang'];
$kdOrg = $_POST['kdOrg'];
$satuan = $_POST['satuan'];
$nobpb = $_POST['nobpb'];
$nopo = $_POST['nopo'];
$nopp = $_POST['nopp'];
$kodebarang = $_POST['kodebarang'];
$jumlah = $_POST['jumlah'];
$satuanpo = $_POST['satuanpo'];
$matauang = $_POST['matauang'];
$kurs = $_POST['kurs'];
$hargasatuan = $_POST['hargasatuan'];
$keteranganpp = $_POST['keteranganpp'];
$tampung = $_POST['tampung'];
$notranDet = $_POST['notranDet'];
$nobpbDet = $_POST['nobpbDet'];
$nopoDet = $_POST['nopoDet'];
$kodebarangDet = $_POST['kodebarangDet'];
$txtBarang = $_POST['txtBarang'];
$arrSt = array('X', 'V');
$perSch = $_POST['perSch'];
$kdPtSch = $_POST['kdPtSch'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$nmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmTranp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$i = 'select kode,matauang from ' . $dbname . '.setup_matauang';

#exit(mysql_error($conn));
($j = mysql_query($i)) || true;

while ($k = mysql_fetch_assoc($j)) {
	$optMt .= '<option value=\'' . $k['kode'] . '\'>' . $k['matauang'] . '</option>';
}

echo "\r\n";

switch ($method) {
case 'updateAll':
	$i = 'update ' . $dbname . '.`log_packingdt`  set jumlah=\'' . $jumlah . '\' where notransaksi=\'' . $notranDet . '\' and nobpb=\'' . $nobpbDet . '\' and nopo=\'' . $nopoDet . '\' and kodebarang=\'' . $kodebarangDet . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'update':
	$i = 'update ' . $dbname . '.`log_packinght`  set kodept=\'' . $pt . '\',tanggal=\'' . $tgl . '\',ukuranpeti=\'' . $peti . '\',keterangan=\'' . $ket . '\',menyerahkan=\'' . $serah . '\',menerima=\'' . $terima . '\',createby=\'' . $_SESSION['standard']['userid'] . '\' where notransaksi=\'' . $notran . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'goCariBarang':
	echo "\r\n\t\t\t\t\t\t" . '<table cellspacing=1 border=0 class=data>' . "\r\n\t\t\t\t\t\t" . '<thead>' . "\r\n\t\t\t\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t\t\t\t" . '<td>No</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t" . '</thead>' . "\r\n\t\t\t\t\t" . '</tbody>';
	$i = 'select * from ' . $dbname . '.log_5masterbarang where kodebarang like \'%' . $txtBarang . '%\' or namabarang like \'%' . $txtBarang . '%\'';

	#exit(mysql_error($conn));
	($n = mysql_query($i)) || true;

	while ($d = mysql_fetch_assoc($n)) {
		$no += 1;
		echo "\r\n\t\t\t\t\t\t" . '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' onclick="goPickBarang(\'' . $d['kodebarang'] . '\',\'' . $d['namabarang'] . '\',\'' . $d['satuan'] . '\')">' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $d['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $d['namabarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $d['satuan'] . '</td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t";
	}

	break;

case 'getFormBarang':
	echo '<fieldset>' . "\r\n\t\t\t\t" . '<legend>' . $_SESSION['lang']['form'] . '</legend>' . "\r\n\t\t\t\t\t" . '<table cellspacing=1 border=0>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['notransaksi'] . '</td> ' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=notran value=\'' . $notran . '\' onkeypress="return tanpa_kutip(event);" class=myinputtext disabled style="width:150px;"></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>No. BPB</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=nobpb  class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=nopo  class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=nopp  class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . "\r\n\t\t\t\t\t\t\t\t" . '<input type=text id=kodebarang disabled class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'>' . "\r\n\t\t\t\t\t\t\t\t" . '<img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\'  class=resicon onclick=cariBarang(\'' . $_SESSION['lang']['find'] . '\',event)>' . "\r\n\t\t\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . "\r\n\t\t\t\t\t\t\t\t" . '<input type=text id=namabarang disabled class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'>' . "\r\n\t\t\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=jumlah class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=satuan disabled class=myinputtext maxlength=100 onkeypress="return tanpa_kutip(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><select id=matauang = style="width:150px;">' . $optMt . '</select></td>' . "\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=kurs  class=myinputtext maxlength=100 onkeypress="return angka_doang(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td>:</td>' . "\r\n\t\t\t\t\t\t\t" . '<td><input type=text id=hargasatuan  class=myinputtextnumber maxlength=100 onkeypress="return angka_doang(event);" style=\'width:100px;\'></td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t\t" . '<td>' . "\r\n\t\t\t\t\t\t\t\t" . '<button class=mybutton onclick=saveFormBarang()>Simpan</button>' . "\r\n\t\t\t\t\t\t\t\t" . '<button class=mybutton onclick=cancelFormBarang()>Hapus</button>' . "\r\n\t\t\t\t\t\t\t\t" . '<button class=mybutton onclick=closeDialog()>' . $_SESSION['lang']['selesai'] . '</button>' . "\r\n\t\t\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t" . '</table>' . "\r\n\t\t\t\t" . '</fieldset>' . "\t";
	break;

case 'goCariPo':
	echo "\r\n\t\t\t" . '<table cellspacing=1 border=0 class=data>' . "\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nomor'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>No. BPB</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . ' BPB</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . ' Terkirim</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuan'] . ' PO</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t" . '</thead>' . "\r\n\t\t" . '</tbody>';
	$i = 'select * from ' . $dbname . '.log_po_vw where  statuspo=\'3\' and nopo like \'%' . $noPo . '%\' and kodeorg=\'' . $pt . '\'  ';

	#exit(mysql_error($conn));
	($n = mysql_query($i)) || true;

	while ($d = mysql_fetch_assoc($n)) {
		$whbpb = 'post=1 and nopo=\'' . $d['nopo'] . '\'';
		$nobpb = makeOption($dbname, 'log_transaksi_vw', 'nopo,notransaksi', $whbpb);
		$whi = 'nopo=\'' . $d['nopo'] . '\' and kodebarang=\'' . $d['kodebarang'] . '\' and tipetransaksi=1 and post=1';
		$jumlah = makeOption($dbname, 'log_transaksi_vw', 'notransaksi,jumlah', $whi);
		$whn = 'kodebarang=\'' . $d['kodebarang'] . '\' ';
		$ket = makeOption($dbname, 'log_prapodt', 'nopp,keterangan', $whn);
		$aCek = "\t" . 'select a.nopo,a.kodebarang,sum(a.jumlah) as jumlah from ' . $dbname . '.log_packingdt a' . "\r\n\t\t\t\t\t" . 'where a.nopo=\'' . $d['nopo'] . '\' and a.kodebarang=\'' . $d['kodebarang'] . '\'' . "\r\n\t\t\t\t\t" . 'union' . "\r\n\t\t\t\t\t" . 'select b.nopo,b.kodebarang,sum(b.jumlah) as jumlah from ' . $dbname . '.log_suratjalandt b' . "\r\n\t\t\t\t\t" . 'where b.jenis=\'PO\' and b.nopo=\'' . $d['nopo'] . '\' and b.kodebarang=\'' . $d['kodebarang'] . '\'' . "\r\n\t\t\t\t\t" . 'union' . "\r\n\t\t\t\t\t" . 'select c.nopo,c.kodebarang,sum(c.jumlah) as jumlah from ' . $dbname . '.log_konosemendt c' . "\r\n\t\t\t\t\t" . 'where c.jenis=\'PO\' and c.nopo=\'' . $d['nopo'] . '\' and c.kodebarang=\'' . $d['kodebarang'] . '\' ';

		#exit(mysql_error($conn));
		($bCek = mysql_query($aCek)) || true;
		$cCek = mysql_fetch_assoc($bCek);
		$jSelisih = $jumlah[$nobpb[$d['nopo']]] - $cCek['jumlah'];
		if (($jSelisih == '') || ($jSelisih == '0')) {
			$jSelisih = '0';
		}
		else {
			$jSelisih = $jSelisih;
		}

		if (($cCek['jumlah'] == '') || ($cCek['jumlah'] == '0')) {
			$cCek['jumlah'] = '0';
		}
		else {
			$cCek['jumlah'] = $cCek['jumlah'];
		}

		if (($jSelisih == '') || ($jSelisih == '0') || ($jSelisih <= '0')) {
			$trKlik = '<tr class=rowcontent style=\'background-color:orange;\'>';
		}
		else {
			$trKlik = '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' ' . "\r\n\t\t\t\t" . 'onclick="saveDetail(\'' . $tampung . '\',\'' . $nobpb[$d['nopo']] . '\',\'' . $d['nopo'] . '\',\'' . $d['nopp'] . '\',\'' . $d['kodebarang'] . '\',\'' . $jSelisih . '\',' . "\r\n\t\t\t\t" . '\'' . $d['satuan'] . '\',\'' . $d['matauang'] . '\',\'' . $d['kurs'] . '\',\'' . $d['hargasatuan'] . '\',\'' . $ket[$d['nopp']] . '\');">';
		}

		if (($jumlah[$nobpb[$d['nopo']]] == 0) || ($jumlah[$nobpb[$d['nopo']]] == '')) {
		}
		else {
			echo $trKlik;
			$no += 1;
			echo "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t\r\n\t\t\t\t" . '<td>' . $nobpb[$d['nopo']] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['nopo'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['nopp'] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $d['kodebarang'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['namabarang'] . '</td>' . "\r\n\t\t\t\t\r\n\t\t\t\t" . '<td align=right>' . $jumlah[$nobpb[$d['nopo']]] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $cCek['jumlah'] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $jSelisih . '</td>' . "\r\n\t\t\t\t\r\n\t\t\t\t" . '<td align=right>' . $d['satuan'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $d['matauang'] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $d['kurs'] . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . $d['hargasatuan'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $ket[$d['nopp']] . '</td>' . "\r\n\t\t\t" . '</tr>';
		}
	}

	break;

case 'insert':
	$i = 'INSERT INTO ' . $dbname . '.`log_packinght` (`notransaksi`, `kodept`, `tanggal`, `ukuranpeti`, `keterangan`, `createby`, `menyerahkan`, `menerima`)' . "\t\r\n\t\t" . 'values (\'' . $notran . '\',\'' . $pt . '\',\'' . $tgl . '\',\'' . $peti . '\',\'' . $ket . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $serah . '\',\'' . $terima . '\')';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'saveFormBarang':
	$i = 'INSERT INTO ' . $dbname . '.`log_packingdt` (`notransaksi`, `nobpb`, `nopo`, `nopp`, `kodebarang`, `jumlah`, `satuanpo`, `matauang`, `kurs`, `harga`)' . "\r\n\t\t" . 'values (\'' . $notran . '\',\'' . $nobpb . '\',\'' . $nopo . '\',\'' . $nopp . '\',' . "\r\n\t\t" . '\'' . $kodebarang . '\',\'' . $jumlah . '\',\'' . $satuan . '\',\'' . $matauang . '\',\'' . $kurs . '\',\'' . $hargasatuan . '\')';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'saveDetail':
	$i = 'INSERT INTO ' . $dbname . '.`log_packingdt` (`notransaksi`, `nobpb`, `nopo`, `nopp`, `kodebarang`, `jumlah`, `satuanpo`, `matauang`, `kurs`, `harga`, `keteranganpp`)' . "\r\n\t\t" . 'values (\'' . $notran . '\',\'' . $nobpb . '\',\'' . $nopo . '\',\'' . $nopp . '\',' . "\r\n\t\t" . '\'' . $kodebarang . '\',\'' . $jumlah . '\',\'' . $satuanpo . '\',\'' . $matauang . '\',\'' . $kurs . '\',\'' . $hargasatuan . '\',\'' . $keteranganpp . '\')';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'updateDetail':
	$i = 'update ' . $dbname . '.`log_packingdt`  set jumlah=\'' . $jumlah . '\' where notransaksi=\'' . $notran . '\' and nobpb=\'' . $nobpb . '\' and nopo=\'' . $nopo . '\' and kodebarang=\'' . $kodebarang . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadDetail':
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t\t" . ' <thead>' . "\r\n\t\t\t\t" . ' <tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>No. BPB</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['satuan'] . ' PO</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>*</td>' . "\r\n\t\t\t\t" . ' </tr>' . "\r\n\t\t\t" . '</thead>' . "\r\n\t\t\t" . '<tbody></fieldset>';
	$no = 0;
	$a = 'select * from ' . $dbname . '.log_packingdt where notransaksi=\'' . $notran . '\' ';

	#exit(mysql_error());
	($b = mysql_query($a)) || true;

	while ($c = mysql_fetch_assoc($b)) {
		$no += 1;
		$xCek = "\t" . 'select a.nopo,a.kodebarang,sum(a.jumlah) as jumlah from ' . $dbname . '.log_packingdt a' . "\r\n\t\t\t\t\t" . 'where a.nopo=\'' . $c['nopo'] . '\' and a.kodebarang=\'' . $c['kodebarang'] . '\'' . "\r\n\t\t\t\t\t" . 'union' . "\r\n\t\t\t\t\t" . 'select b.nopo,b.kodebarang,sum(b.jumlah) as jumlah from ' . $dbname . '.log_suratjalandt b' . "\r\n\t\t\t\t\t" . 'where b.jenis=\'PO\' and b.nopo=\'' . $c['nopo'] . '\' and b.kodebarang=\'' . $c['kodebarang'] . '\'' . "\r\n\t\t\t\t\t" . 'union' . "\r\n\t\t\t\t\t" . 'select c.nopo,c.kodebarang,sum(c.jumlah) as jumlah from ' . $dbname . '.log_konosemendt c' . "\r\n\t\t\t\t\t" . 'where c.jenis=\'PO\' and c.nopo=\'' . $c['nopo'] . '\' and c.kodebarang=\'' . $c['kodebarang'] . '\' ';

		#exit(mysql_error($conn));
		($yCek = mysql_query($xCek)) || true;
		$zCek = mysql_fetch_assoc($yCek);
		$i = 'select * from ' . $dbname . '.log_po_vw where  statuspo=\'3\' and nopo=\'' . $c['nopo'] . '\' and kodebarang=\'' . $c['kodebarang'] . '\'  ';

		#exit(mysql_error($conn));
		($n = mysql_query($i)) || true;
		$d = mysql_fetch_assoc($n);
		$nobpb = makeOption($dbname, 'log_transaksi_vw', 'nopo,notransaksi');
		$whi = 'nopo=\'' . $d['nopo'] . '\' and kodebarang=\'' . $d['kodebarang'] . '\' and tipetransaksi=1 ';
		$jumlah = makeOption($dbname, 'log_transaksi_vw', 'notransaksi,jumlah', $whi);
		$jumlahSimpan = $zCek['jumlah'] - $c['jumlah'];
		echo '<tr class=rowcontent  id=row' . $no . '>' . "\r\n\t\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t\t" . '<td id=notranDet' . $no . '>' . $c['notransaksi'] . '</td>' . "\r\n\t\t\t\t\t" . '<td id=nobpbDet' . $no . '>' . $c['nobpb'] . '</td>' . "\r\n\t\t\t\t\t" . '<td id=nopoDet' . $no . '>' . $c['nopo'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['nopp'] . '</td>' . "\r\n\t\t\t\t\t" . '<td id=kodebarangDet' . $no . '>' . $c['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t\r\n\t\t\t\t\t" . '<td>' . $nmBarang[$c['kodebarang']] . '</td>' . "\r\n\t\t\t\t\t\r\n\t\t\t\t\t" . '<td><input type=text id=jumlah' . $no . ' value=' . $c['jumlah'] . ' onkeypress="return angka_doang(event);" class=myinputtextnumber style="width:100px;"></td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['satuanpo'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['matauang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['kurs'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['harga'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $c['keteranganpp'] . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . "\r\n\t\t\t\t\t\t" . '<img src=images/icons/Grey/PNG/save.png class=resicon  title=\'update\' onclick="updateDetail(\'' . $c['notransaksi'] . '\',\'' . $c['nobpb'] . '\',\'' . $c['nopo'] . '\',\'' . $c['kodebarang'] . '\',' . $no . ',\'' . $jumlah[$nobpb[$d['nopo']]] . '\',\'' . $jumlahSimpan . '\');" >' . "\r\n\t\t\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="DelDetail(\'' . $c['notransaksi'] . '\',\'' . $c['nobpb'] . '\',\'' . $c['nopo'] . '\',\'' . $c['kodebarang'] . '\');" >' . "\t\t\t\t\t\r\n\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>';
	}

	echo '<tr>' . "\r\n\t\t\t\t" . '<td colspan=14 align=center>' . "\r\n\t\t\t\t\t" . '<button class=mybutton id=editAll onclick=editAll(' . $no . ')>' . $_SESSION['lang']['edit'] . '</button>' . "\r\n\t\t\t\t\t" . '<button class=mybutton id=cancelDetail onclick=cancel()>' . $_SESSION['lang']['selesai'] . '</button>' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t\t" . ' </tr>';
	echo '</table>';
	break;

case 'loadData':
	$kdPtLoad = 'kodept!=\'\' ';

	if ($kdPtSch != '') {
		$kdPtLoad = 'kodept like \'%' . $kdPtSch . '%\'';
	}

	$perLoad = '';

	if ($perSch != '') {
		$perLoad = 'and tanggal like \'%' . $perSch . '%\'';
	}

	if ($_POST['notransCari'] != '') {
		$perLoad .= ' and notransaksi like \'%' . $_POST['notransCari'] . '%\'';
	}

	echo "\r\n\t\t\t" . '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t\t" . ' <thead>' . "\r\n\t\t\t\t" . ' <tr class=rowheader>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['kodept'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['dibuatoleh'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['menyerahkan'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['menerima'] . '</td>' . "\r\n\t\t\t\t\t" . ' <td align=center>' . $_SESSION['lang']['action'] . '</td>' . "\r\n\t\t\t\t" . ' </tr>' . "\r\n\t\t\t" . '</thead>' . "\r\n\t\t\t" . '<tbody>';
	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$maxdisplay = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_packinght where ' . $kdPtLoad . '  ' . $perLoad . '  order by tanggal desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$i = 'select * from ' . $dbname . '.log_packinght where ' . $kdPtLoad . '  ' . $perLoad . '  order by tanggal desc  limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($n = mysql_query($i)) || true;
	$no = $maxdisplay;

	while ($d = mysql_fetch_assoc($n)) {
		$no += 1;
		echo '<tr class=rowcontent>';
		echo '<td align=center>' . $no . '</td>';
		echo '<td align=left>' . $d['notransaksi'] . '</td>';
		echo '<td align=left>' . tanggalnormal($d['tanggal']) . '</td>';
		echo '<td align=left>' . $d['kodept'] . '</td>';
		echo '<td align=left>' . $nmKar[$d['createby']] . '</td>';
		echo '<td>' . $nmKar[$d['menyerahkan']] . '</td>';
		echo '<td>' . $d['menerima'] . '</td>';

		if ($d['posting'] == '0') {
			$post = '<td align=center>' . "\r\n\t\t\t\t\t\t\t\t\t" . '<img src=images/application/application_edit.png  title=\'update\' class=resicon  caption=\'Edit\' onclick="edit(\'' . $d['notransaksi'] . '\',\'' . $d['kodept'] . '\',\'' . tanggalnormal($d['tanggal']) . '\',\'' . $d['ukuranpeti'] . '\',\'' . $d['keterangan'] . '\',\'' . $d['menyerahkan'] . '\',\'' . $d['menerima'] . '\');">' . "\r\n\t\t\t\t\t\t\t\t\t" . '<img src=images/application/application_delete.png  title=\'delete\' class=resicon caption=\'Delete\' onclick="delHead(\'' . $d['notransaksi'] . '\');">' . "\r\n\t\t\t\t\t\t\t\t\t" . '<img src=images/hot.png  title=\'Posting\' class=zImgBtn caption=\'Posting\' onclick="posting(\'' . $d['notransaksi'] . '\');">' . "\r\n\t\t\t\t\t\t\t\t\t" . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'sdm_splht\',\'' . $d['notransaksi'] . '\',\'\',\'log_slave_packing_pdf\',event)">' . "\r\n\t\t\t\t\t\t\t\t" . '</td>';
		}
		else {
			$post = '<td align=center>' . "\r\n\t\t\t\t\t\t\t\t" . '<img src=images/buttongreen.png class=zImgBtn>' . "\r\n\t\t\t\t\t\t\t\t" . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'sdm_splht\',\'' . $d['notransaksi'] . '\',\'\',\'log_slave_packing_pdf\',event)">' . "\r\n\t\t\t\t\t\t\t" . '   </td>';
		}

		echo $post;
		echo '</tr>';
	}

	echo "\r\n\t\t\t" . '<tr class=rowheader><td colspan=43 align=center>' . "\r\n\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t" . '</td>' . "\r\n\t\t\t" . '</tr>';
	echo '</tbody></table>';
	break;

case 'delHead':
	$i = 'delete from ' . $dbname . '.log_packinght where notransaksi=\'' . $notran . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'posting':
	$sekarang = date('Y-m-d');
	$i = 'update  ' . $dbname . '.log_packinght set posting=1,postingdate=\'' . $sekarang . '\' where notransaksi=\'' . $notran . '\'';

	if (mysql_query($i)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'deleteDetail':
	$i = 'delete from ' . $dbname . '.log_packingdt where notransaksi=\'' . $notran . '\' and nobpb=\'' . $nobpb . '\' and nopo=\'' . $nopo . '\' and kodebarang=\'' . $kodebarang . '\'';

	if (mysql_query($i)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
