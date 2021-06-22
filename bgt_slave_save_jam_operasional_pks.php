<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$oldtahunbudget = $_POST['oldtahunbudget'];
$oldkodeorg = $_POST['oldkodeorg'];
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = $_POST['kodeorg'];
$jamo = $_POST['jamo'];
$jamb = $_POST['jamb'];
$method = $_POST['method'];

switch ($method) {
case 'insert':
	$oldtahunbudget == '' ? $oldtahunbudget = $_POST['tahunbudget'] : $oldtahunbudget = $_POST['oldtahunbudget'];
	$oldkodeorg == '' ? $oldkodeorg = $_POST['kodeorg'] : $oldkodeorg = $_POST['oldkodeorg'];

	if (strlen($tahunbudget) < 4) {
		exit('Error:tahun budget belum sesuai');
	}

	$sRicek = 'select * from ' . $dbname . '.bgt_jam_operasioal_pks where tahunbudget=\'' . $oldtahunbudget . '\' and millcode=\'' . $oldkodeorg . '\' ';

	#exit(mysql_error($conn));
	($qRicek = mysql_query($sRicek)) || true;
	$rRicek = mysql_num_rows($qRicek);

	if (0 < $rRicek) {
		$sDel = 'delete from ' . $dbname . '.bgt_jam_operasioal_pks' . "\r\n\t\t\t\t" . 'where tahunbudget=\'' . $oldtahunbudget . '\' and millcode=\'' . $oldkodeorg . '\'  ';

		if (mysql_query($sDel)) {
			$sDel2 = 'insert into ' . $dbname . '.bgt_jam_operasioal_pks (`tahunbudget`,`millcode`,`jamolah`,`breakdown`)' . "\r\n\t\t" . 'values (\'' . $tahunbudget . '\',\'' . $kodeorg . '\',\'' . $jamo . '\',\'' . $jamb . '\')';

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
		$sDel2 = 'insert into ' . $dbname . '.bgt_jam_operasioal_pks (`tahunbudget`,`millcode`,`jamolah`,`breakdown`)' . "\r\n\t\t" . 'values (\'' . $tahunbudget . '\',\'' . $kodeorg . '\',\'' . $jamo . '\',\'' . $jamb . '\')';

		if (mysql_query($sDel2)) {
			echo '';
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}

	break;

case 'loadData':
	$str1 = 'select * from ' . $dbname . '.bgt_jam_operasioal_pks where millcode=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by tahunbudget desc';
	$no = 0;
	$res1 = mysql_query($str1);

	while ($bar1 = mysql_fetch_object($res1)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td align=center>' . $no . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->tahunbudget . '</td>' . "\r\n\t\t\t" . '<td align=left>' . $bar1->millcode . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->jamolah . '</td>' . "\r\n\t\t\t" . '<td align=right>' . $bar1->breakdown . '</td>' . "\t\r\n\t\t\t" . '<td align=center><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->tahunbudget . '\',\'' . $bar1->millcode . '\',\'' . $bar1->jamolah . '\',\'' . $bar1->breakdown . '\');"></td></tr>';
	}

	break;
}

?>
