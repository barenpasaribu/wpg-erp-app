<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = $_POST['kodeorg'];
$hrsetahun = $_POST['hrsetahun'];
$hrminggu = $_POST['hrminggu'];
$hrlibur = $_POST['hrlibur'];
$hrliburminggu = $_POST['hrliburminggu'];
$hkeffektif = $_POST['hkeffektif'];
$method = $_POST['method'];
$oldtahunbudget = $_POST['oldtahunbudget'];
$oldkodeorg = $_POST['oldkodeorg'];

switch ($method) {
case 'insert':
	$oldtahunbudget == '' ? $oldtahunbudget = $_POST['tahunbudget'] : $oldtahunbudget = $_POST['oldtahunbudget'];
	$sCek = 'select tahunbudget from ' . $dbname . '.bgt_hk where tahunbudget=\'' . $oldtahunbudget . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if (strlen($tahunbudget) < 4) {
		exit('Error:Panjang Karakter Kurang');
	}

	if ($tahunbudget == '') {
		echo 'warning : Tahun Budget masih kosong';
		exit();
	}
	else if ($hrsetahun == '') {
		echo 'warning : Hari dalam satu tahun masih kosong';
		exit();
	}
	else if ($hrminggu == '') {
		echo 'warning : Hari dalam satu minggu masih kosong';
		exit();
	}
	else if ($hrlibur == '') {
		echo 'warning : Hari libur masih kosong';
		exit();
	}
	else if ($hrliburminggu == '') {
		echo 'warning : Hari libur minggu masih kosong';
		exit();
	}

	if (0 < $rCek) {
		$sDel = 'delete from ' . $dbname . '.bgt_hk' . "\r\n\t\t\t\t\t\t" . 'where tahunbudget=\'' . $oldtahunbudget . '\' ';

		if (mysql_query($sDel)) {
			$sDel2 = 'insert into ' . $dbname . '.bgt_hk (`tahunbudget`,`harisetahun`,`hrminggu`,`hrlibur`,`hrliburminggu`,`updatedby`) ' . "\r\n" . '                                            values (\'' . $tahunbudget . '\',\'' . $hrsetahun . '\',\'' . $hrminggu . '\',\'' . $hrlibur . '\',\'' . $hrliburminggu . '\',\'' . $_SESSION['standard']['userid'] . '\')';

			if (mysql_query($sDel2)) {
				echo '';
			}
			else {
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		$sIns = 'insert into ' . $dbname . '.bgt_hk (`tahunbudget`,`harisetahun`,`hrminggu`,`hrlibur`,`hrliburminggu`,`updatedby`) ' . "\r\n" . '                            values (\'' . $tahunbudget . '\',\'' . $hrsetahun . '\',\'' . $hrminggu . '\',\'' . $hrlibur . '\',\'' . $hrliburminggu . '\',\'' . $_SESSION['standard']['userid'] . '\')';

		if (!mysql_query($sIns)) {
			echo 'Gagal' . mysql_error($conn);
		}
	}

	break;

case 'loadData':
	$str = 'select * from ' . $dbname . '.bgt_hk  order by tahunbudget desc';

	#exit(mysql_error($conn));
	($res = mysql_query($str)) || true;

	while ($bar = mysql_fetch_assoc($res)) {
		$a[$bar['tahunbudget']] = intval($bar['harisetahun']);
		$b[$bar['tahunbudget']] = intval($bar['hrminggu']);
		$c[$bar['tahunbudget']] = intval($bar['hrlibur']);
		$d[$bar['tahunbudget']] = intval($bar['hrliburminggu']);
		$hasil[$bar['tahunbudget']] = $a[$bar['tahunbudget']] - ($b[$bar['tahunbudget']] + $c[$bar['tahunbudget']]) - $d[$bar['tahunbudget']];
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['tahunbudget'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['harisetahun'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrminggu'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrlibur'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $bar['hrliburminggu'] . '</td>' . "\r\n\t\t" . '<td align=right>' . $hasil[$bar['tahunbudget']] . '</td>' . "\r\n\t\t" . '<td align=center><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['tahunbudget'] . '\');"></td>' . "\r\n\t\t" . '</tr>';
	}

	break;

case 'getData':
	$sDt = 'select * from ' . $dbname . '.bgt_hk where tahunbudget=\'' . $tahunbudget . '\'  order by tahunbudget desc';

	#exit(mysql_error($conn));
	($qDt = mysql_query($sDt)) || true;
	$rDet = mysql_fetch_assoc($qDt);
	echo $rDet['tahunbudget'] . '###' . $rDet['harisetahun'] . '###' . $rDet['hrminggu'] . '###' . $rDet['hrlibur'] . '###' . $rDet['hrliburminggu'];
	break;
}

?>
