<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetransaksi = 6;

if (isTransactionPeriod()) {
	$nodok = $_POST['nodok'];
	$nomorlama = $_POST['nomorlama'];
	$tanggal = tanggalsystem($_POST['tanggal']);
	$nofaktur = '';
	$nosj = '';
	$qty = $_POST['jlhretur'];
	$kodebarang = $_POST['kodebarang'];
	$kodegudang = $_POST['gudang'];
	$kodept = $_POST['kodept'];
	$untukunit = $_POST['untukunit'];
	$hargasatuan = $_POST['hargasatuan'];
	$post = 0;
	$keterangan = $_POST['keterangan'];
	$user = $_SESSION['standard']['userid'];
	$satuan = $_POST['satuan'];
	$supplierid = $_POST['supplierid'];
	$nopo = $_POST['nopo'];

	if ($hargasatuan == '') {
		$hargasatuan = 0;
	}

	$stro = 'select * from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '                where kodeorg=\'' . $kodegudang . '\' and periode=\'' . substr($tanggal, 0, 7) . '\'' . "\r\n" . '                and tutupbuku=1';
	$reso = mysql_query($stro);

	if (0 < mysql_num_rows($reso)) {
		$status = 7;
		echo ' Error :' . $_SESSION['lang']['tanggaltutup'];
		exit(0);
	}

	$str = "insert into ".$dbname.".log_transaksiht (tipetransaksi, notransaksi, tanggal, kodept, nopo, keterangan, statusjurnal, kodegudang, user, idsupplier, post, postedby, notransaksireferensi) values(".$tipetransaksi.",'".$nodok."', '".$tanggal."','".$kodept."','".$nopo."','".$keterangan."', 0,'".$kodegudang."','".$user."','".$supplierid."',0,0,'".$nomorlama."');";
	
	$str2 = "insert into ".$dbname.".log_transaksidt (notransaksi, kodebarang, satuan, jumlah, jumlahlalu, hargasatuan, updateby, statussaldo, hargarata) values('".$nodok."', '".$kodebarang."', '".$satuan."', '".$qty."', 0,'".$hargasatuan."', '".$user."', 0,0)";

	if (mysql_query($str)) {
		if (mysql_query($str2)) {
		}
		else {
			echo 'Error detail ' . addslashes(mysql_error($conn));
			$str = 'delete from ' . $dbname . '.log_transaksiht where notransaksi=\'' . $nodok . '\'';
			mysql_query($str);
		}
	}
	else {
		echo 'Error header ' . addslashes(mysql_error($conn)) . $str;
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
