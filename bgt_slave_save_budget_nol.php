<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorg'];
$kodebudget = $_POST['kodebudget'];
$tahunbudget = $_POST['tahunbudget'];

if ($kodebudget == 'KAPITAL') {
	$str = 'select kunci,hargatotal as rupiah,jumlah from ' . $dbname . '.bgt_kapital' . "\r\n" . '          where kodeunit like \'' . $kodeorg . '%\' and tahunbudget=\'' . $tahunbudget . '\'';
}
else {
	if ($kodebudget != '') {
		$where = ' where kodeorg like \'' . $kodeorg . '%\' and tahunbudget=\'' . $tahunbudget . '\' and kodebudget=\'' . $kodebudget . '\'';
	}
	else {
		$where = ' where kodeorg like \'' . $kodeorg . '%\' and tahunbudget=\'' . $tahunbudget . '\'';
	}

	$str = 'select kunci,rupiah,jumlah from ' . $dbname . '.bgt_budget ' . $where;
}

$res = mysql_query($str);
$res1 = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$rupiah = $bar->rupiah;
	$fisik = $bar->jumlah;

	if ($kodebudget == 'KAPITAL') {
		$dcek = 'select distinct sum(k01+k02+k03+k04+k05+k06+k07+k08+k09+k10+k11+k12) as total from ' . $dbname . '.bgt_kapital where' . "\r\n" . '              kunci=\'' . $bar->kunci . '\'';

		#exit(mysql_error($conn));
		($qdcek = mysql_query($dcek)) || true;
		$rcek = mysql_fetch_assoc($qdcek);

		if ($rcek['total'] == 0) {
			$strins = 'update ' . $dbname . '.bgt_kapital set' . "\r\n" . '                      k01=' . @$rupiah / 12 . ',k02=' . @$rupiah / 12 . ',k03=' . @$rupiah / 12 . ',k04=' . @$rupiah / 12 . ',k05=' . @$rupiah / 12 . ',k06=' . @$rupiah / 12 . ',' . "\r\n" . '                      k07=' . @$rupiah / 12 . ',k08=' . @$rupiah / 12 . ',k09=' . @$rupiah / 12 . ',k10=' . @$rupiah / 12 . ',k11=' . @$rupiah / 12 . ',k12=' . @$rupiah / 12 . ',' . "\r\n" . '                      updateby=\'' . $_SESSION['standard']['userid'] . '\'' . "\r\n" . '                      where kunci=\'' . $bar->kunci . '\'';

			if (mysql_query($strins)) {
			}
			else {
				echo ' Gagal(delete): ' . addslashes(mysql_error($conn));
				exit();
			}
		}
	}
	else {
		$strdel = 'select distinct kunci from ' . $dbname . '.bgt_distribusi where kunci=' . $bar->kunci;
		$res5 = mysql_query($strdel);
		$row = mysql_num_rows($res5);

		if (0 < $row) {
			continue;
		}

		$strins = 'insert into ' . $dbname . '.bgt_distribusi (kunci, rp01, fis01, rp02, fis02, rp03, fis03, rp04, fis04,' . "\r\n" . '                  rp05, fis05, rp06, fis06, rp07, fis07, rp08, fis08, rp09, fis09, rp10, fis10, rp11, fis11, rp12,' . "\r\n" . '                  fis12, updateby)' . "\r\n" . '                  values(' . $bar->kunci . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . @$rupiah / 12 . ',' . @$fisik / 12 . ',' . "\r\n" . '                      ' . $_SESSION['standard']['userid'] . "\r\n" . '                  )';

		if (mysql_query($strins)) {
		}
		else {
			echo ' Gagal(delete): ' . addslashes(mysql_error($conn));
			exit();
		}
	}
}

?>
