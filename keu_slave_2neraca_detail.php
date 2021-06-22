<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
echo $_GET['nourut'];
exit();
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
$namapt = 'COMPANY NAME';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namapt = strtoupper($bar->namaorganisasi);
}

if (($periode == '') && ($gudang == '')) {
	$str = 'select a.*,c.induk from ' . $dbname . '.keu_5mesinlaporandt a' . "\r\n\t\t" . 'left join ' . $dbname . '.organisasi c' . "\r\n\t\t" . 'on substr(a.kodeorg,1,4)=c.kodeorganisasi' . "\r\n\t\t" . 'where a.namalaporan=\'BALANCE SHEET\'' . "\r\n\t\t" . 'order by a.nourut ' . "\r\n\t\t";
	$str1 = 'select *,b.namaakun from ' . $dbname . '.keu_jurnalsum_vw a' . "\r\n\t\t" . 'left join ' . $dbname . '.keu_5akun b' . "\r\n\t\t" . 'on a.noakun=b.noakun' . "\r\n\t\t" . 'order by a.noakun, a.periode ' . "\r\n\t\t";
}
else if (($periode == '') && ($gudang != '')) {
	$str = 'select a.*,c.induk from ' . $dbname . '.keu_5mesinlaporandt a' . "\r\n\t\t" . 'left join ' . $dbname . '.organisasi c' . "\r\n\t\t" . 'on substr(a.kodeorg,1,4)=c.kodeorganisasi' . "\r\n\t\t" . 'where a.namalaporan=\'BALANCE SHEET\'' . "\r\n\t\t" . 'order by a.nourut ' . "\r\n\t\t";
	$str1 = 'select *,b.namaakun from ' . $dbname . '.keu_jurnalsum_vw a' . "\r\n\t\t" . 'left join ' . $dbname . '.keu_5akun b' . "\r\n\t\t" . 'on a.noakun=b.noakun' . "\r\n\t\t" . 'order by a.noakun, a.periode ' . "\r\n\t\t";
}
else if ($gudang == '') {
	$str = 'select a.*,c.induk from ' . $dbname . '.keu_5mesinlaporandt a' . "\r\n\t\t" . 'left join ' . $dbname . '.organisasi c' . "\r\n\t\t" . 'on substr(a.kodeorg,1,4)=c.kodeorganisasi' . "\r\n\t\t" . 'where a.namalaporan=\'BALANCE SHEET\'' . "\r\n\t\t" . 'order by a.nourut ' . "\r\n\t\t";
	$str1 = 'select * from ' . $dbname . '.keu_jurnalsum_vw ' . "\r\n\t\t";
}
else {
	$str = 'select a.*,c.induk from ' . $dbname . '.keu_5mesinlaporandt a' . "\r\n\t\t" . 'left join ' . $dbname . '.organisasi c' . "\r\n\t\t" . 'on substr(a.kodeorg,1,4)=c.kodeorganisasi' . "\r\n\t\t" . 'where a.namalaporan=\'BALANCE SHEET\'' . "\r\n\t\t" . 'order by a.nourut ' . "\r\n\t\t";
	$str1 = 'select * from ' . $dbname . '.keu_jurnalsum_vw a ' . "\r\n\t\t" . 'where substr(a.kodeorg,1,4)=\'' . $gudang . '\'' . "\r\n\t\t";
}

$salakqty = 0;
$masukqty = 0;
$keluarqty = 0;
$sawalQTY = 0;
$t1balance = $t2balance = $t3balance = $t4balance = $t5balance = $t6balance = $t7balance = $t8balance = 0;
$t1ebalance = $t2ebalance = $t3ebalance = $t4ebalance = $t5ebalance = $t6ebalance = $t7ebalance = $t8ebalance = $t9ebalance = 0;
$res = mysql_query($str);
$res1 = mysql_query($str1);
$no = $counter = 0;
$stawal = $stdebet = $stkredit = $stakhir = $sawal = 0;
$tawal = $tdebet = $tkredit = $takhir = 0;
$noakun1 = $namaakun1 = ' ';

if (mysql_num_rows($res) < 1) {
	echo $_SESSION['lang']['tidakditemukan'];
}
else {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$tanggal = $bar->tanggal;
		$noakun = $bar->noakun;
		$nourut = $bar->nourut;
		$nojurnal = $bar->nojurnal;
		$namaakun = $bar->namaakun;
		$noakundari = $bar->noakundari;
		$noakunsampai = $bar->noakunsampai;
		$tipe = $bar->tipe;
		$keterangandisplay = $bar->keterangandisplay;
		$kodeorg = $bar->kodeorg;
		$variableoutput = $bar->variableoutput;

		if ($periode == $bar->periode) {
			$stdebet += $bar->debet;
			$stkredit += $bar->kredit;
		}
		else {
			$stawal += $bar->debet - $bar->kredit;
		}

		$stakhir = ($stawal + $stdebet) - $stkredit;

		if ('2' <= substr($nourut, 0, 1)) {
			$counter += 1;

			if ($counter == 1) {
			}
		}

		if ($tipe == 'Total') {
			echo '<tr class=rowcontent>';
			echo '<td></td>';
			echo '<td align=right>------------------------------</td>';
			echo '<td align=right>------------------------------</td></tr>';

			if ($variableoutput == '1') {
				echo '<tr class=rowcontent>';
				echo '<td align=right>' . $keterangandisplay . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t1balance, 2, '.', ',') . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t1ebalance, 2, '.', ',') . '</td></tr>';
				$t1balance = $t1ebalance = 0;
			}

			if ($variableoutput == '2') {
				echo '<tr class=rowcontent>';
				echo '<td align=right>' . $keterangandisplay . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t2balance, 2, '.', ',') . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t2ebalance, 2, '.', ',') . '</td></tr>';
				$t1balance = $t1ebalance = 0;
				$t2balance = $t2ebalance = 0;
			}

			if ($variableoutput == '9') {
				echo '<tr class=rowcontent>';
				echo '<td align=right >' . $keterangandisplay . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t9balance, 2, '.', ',') . '</td>' . "\r\n\t\t\t\t" . '     <td align=right>' . number_format($t9ebalance, 2, '.', ',') . '</td></tr>';
				$t1balance = $t1ebalance = $t2balance = $t2ebalance = $t3balance = $t3ebalance = 0;
				$t4balance = $t4ebalance = $t5balance = $t5ebalance = $t6balance = $t6ebalance = 0;
				$t7balance = $t7ebalance = $t8balance = $t8ebalance = $t9balance = $t9ebalance = 0;
			}
		}

		if ($tipe == 'Header') {
			echo '<tr><td>' . $keterangandisplay;
			echo '</td><td><td></tr>';
		}

		if ($tipe == 'Detail') {
			$res1 = mysql_query($str1);
			$balance = $endbalance = 0;

			while ($bar1 = mysql_fetch_object($res1)) {
				$noakun1 = $bar1->noakun;
				$debet1 = $bar1->debet;
				$kredit1 = $bar1->kredit;
				$kodeorg1 = $bar1->kodeorg;

				if (($noakundari <= $noakun1) && ($noakun1 <= $noakunsampai)) {
					$balance += $debet1;
					$balance -= $kredit1;
					$endbalance += $debet1;
					$endbalance -= $kredit1;
				}
			}

			echo '<tr onclick="showDetail(\'' . $nourut . '\',\'' . $keterangandisplay . '\',event)" class=rowcontent>';
			echo '<td>' . $keterangandisplay . '</td>';
			echo '<td align=right>' . number_format($balance, 2, '.', ',') . '</td>';
			echo '<td align=right>' . number_format($endbalance, 2, '.', ',') . '</td></tr>';
			$t1balance += $balance;
			$t2balance += $balance;
			$t3balance += $balance;
			$t4balance += $balance;
			$t5balance += $balance;
			$t6balance += $balance;
			$t7balance += $balance;
			$t8balance += $balance;
			$t9balance += $balance;
			$t1ebalance += $endbalance;
			$t2ebalance += $endbalance;
			$t3ebalance += $endbalance;
			$t4ebalance += $endbalance;
			$t5ebalance += $endbalance;
			$t6ebalance += $endbalance;
			$t7ebalance += $endbalance;
			$t8ebalance += $endbalance;
			$t9ebalance += $endbalance;
		}
	}

	if (($stawal != 0) || ($stdebet != 0) || ($stkredit != 0)) {
		$tawal += $stawal;
		$tdebet += $stdebet;
		$tkredit += $stkredit;
		$takhir += $stakhir;
	}
}

?>
