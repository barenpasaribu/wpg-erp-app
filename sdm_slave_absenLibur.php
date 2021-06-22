<?php


session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$jnlibur = $_POST['jnlibur'];
$tgllibur = tanggalsystem($_POST['tgllibur']);
$t = substr($tgllibur, 0, 4) . '-' . substr($tgllibur, 4, 2) . '-' . substr($tgllibur, 6, 2);
$hari = date('D', strtotime($t));
$sLbr = 'select distinct * from ' . $dbname . '.sdm_5harilibur ' . "\r\n" . '       where regional=\'' . $_SESSION['empl']['regional'] . '\' and tanggal=\'' . $t . '\'';

#exit(mysql_error($conn));
($qLbr = mysql_query($sLbr)) || true;
$rLbr = mysql_num_rows($qLbr);

if (($rLbr == 0) && ($jnlibur != 'MG')) {
	exit('error: The date is not in holiday list');
}

if (($jnlibur == 'MG') && ($hari != 'Sun')) {
	exit('Error: Date ' . $_POST['tgllibur'] . ' is not Sunday, absence code incorrect');
}
else if (($hari == 'Sun') && ($jnlibur != 'MG')) {
	exit('Error: Date ' . $_POST['tgllibur'] . ' is Sunday, absence code incorrect');
}

$str = 'select periode from ' . $dbname . '.sdm_5periodegaji where \'' . $t . '\'<=tanggalsampai and   \'' . $t . '\'>=tanggalmulai and jenisgaji=\'H\' ' . "\r\n" . '          and kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$periode = $bar->periode;
}

if ($periode == '') {
	exit('Error: Payroll period required');
}

$sgaji = 'select * from ' . $dbname . '.sdm_gaji where periodegaji=\'' . $periode . '\' and kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';

#exit(mysql_error($conn));
($qgaji = mysql_query($sgaji)) || true;
$rgaji = mysql_num_rows($qgaji);

if (0 < $rgaji) {
	exit('Error: Payroll period already closed');
}

$str = 'select distinct subbagian,lokasitugas,karyawanid from ' . $dbname . '.datakaryawan where tipekaryawan in(1,2,3,4) and ' . "\r\n" . '    lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' and ' . "\r\n" . '    (tanggalkeluar>=\'' . $t . '\' or tanggalkeluar is NULL) and alokasi=0' . "\r\n" . '    and ( tanggalmasuk<=\'' . $t . '\' or tanggalmasuk=\'0000-00-00\' or tanggalmasuk is null) order by subbagian asc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if (($bar->subbagian == '') || is_null($bar->subbagian == '')) {
		$sub[$bar->lokasitugas] = $_SESSION['empl']['lokasitugas'];
		$subbagian[$bar->karyawanid] = $_SESSION['empl']['lokasitugas'];
	}
	else {
		$sub[$bar->subbagian] = $bar->subbagian;
		$subbagian[$bar->karyawanid] = $bar->subbagian;
	}

	$karyawanid[$bar->karyawanid] = $bar->karyawanid;
}

$itunganmasukdt = 0;

foreach ($sub as $key => $bagian) {
	$strim = '(\'' . $t . '\',\'' . $bagian . '\',\'' . $periode . '\')';

	if (substr($bagian, 0, 4) != $_SESSION['empl']['lokasitugas']) {
		continue;
	}

	$sData = 'select distinct * from ' . $dbname . '.sdm_absensiht where tanggal=\'' . $t . '\' and kodeorg=\'' . $bagian . '\'';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;
	$rData = mysql_num_rows($qData);

	if ($rData == 0) {
		$strim2 = 'insert into ' . $dbname . '.sdm_absensiht(tanggal,kodeorg,periode) values ' . $strim . '';

		if (mysql_query($strim2)) {
			$itunganmasukdt = 0;

			foreach ($karyawanid as $karid => $id) {
				$scek = 'select distinct absensi from ' . $dbname . '.sdm_absensidt where ' . "\r\n" . '                               tanggal=\'' . $t . '\' and karyawanid=\'' . $id . '\'';

				#exit(mysql_error($conn));
				($qcek = mysql_query($scek)) || true;
				$rcek = mysql_num_rows($qcek);

				if (0 < $rcek) {
					continue;
				}

				$dptUph = 0;
				$where = 'karyawanid=\'' . $id . '\'';
				$tpKary = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $where);
				if (($tpKary[$id] == 4) || ($jnlibur != 'MG')) {
					$sUmr = 'select sum(jumlah) as jumlah from ' . $dbname . '.sdm_5gajipokok ' . "\r\n" . '                                    where karyawanid=\'' . $id . '\' and tahun=\'' . substr($t, 0, 4) . '\'  and idkomponen =\'1\'';

					#exit(mysql_error());
					($qUmr = mysql_query($sUmr)) || true;
					$rUmr = mysql_fetch_assoc($qUmr);
					$umr = $rUmr['jumlah'] / 25;
					$kbnhadir = 'select count(absensi) as hadir from ' . $dbname . '.kebun_kehadiran_vw ' . 'where karyawanid=\'' . $id . '\' and tanggal like \'' . substr($t, 0, 7) . '%\' and absensi=\'H\'';

					#exit(mysql_error($conn));
					($qkbnhadir = mysql_query($kbnhadir)) || true;
					$rkbhadir = mysql_fetch_assoc($qkbnhadir);
					$sItung = 'select count(absensi) as hadir from ' . $dbname . '.sdm_absensidt ' . "\r\n" . '                                             where karyawanid=\'' . $id . '\' and tanggal like \'' . substr($t, 0, 7) . '%\' and absensi=\'H\'';

					#exit(mysql_error($conn));
					($qItung = mysql_query($sItung)) || true;
					$rItung = mysql_fetch_assoc($qItung);
					$kehadiran = $rItung['hadir'] + $rkbhadir['hadir'];

					if (16 < $kehadiran) {
						$dptUph = $umr;
					}
					else {
						$dptUph = 0;
					}
				}

				$itunganmasukdt += 1;

				if (isset($strix)) {
					$strix .= ',(\'' . $subbagian[$id] . '\',\'' . $t . '\',\'' . $id . '\',\'' . $jnlibur . '\',\'00:00:00\',\'00:00:00\',0,\'' . $dptUph . '\')';
				}
				else {
					$strix .= '(\'' . $subbagian[$id] . '\',\'' . $t . '\',\'' . $id . '\',\'' . $jnlibur . '\',\'00:00:00\',\'00:00:00\',0,\'' . $dptUph . '\')';
				}
			}

			if ($itunganmasukdt != 0) {
				$sdel = 'delete from ' . $dbname . '.sdm_absensidt where kodeorg=\'' . $bagian . '\' and tanggal=\'' . $t . '\'';

				if (mysql_query($sdel)) {
					$strix2 = 'insert into ' . $dbname . '.sdm_absensidt(kodeorg,tanggal,karyawanid,absensi,jam,jamPlg,catu,insentif) values ' . $strix . ';';

					if (mysql_query($strix2)) {
					}
					else {
						echo 'Error:' . mysql_error($conn) . '____' . $strix2;
					}
				}
			}
		}
		else {
			continue;
		}
	}
	else {
		continue;
		$itunganmasukdt = 0;

		foreach ($karyawanid as $karid => $id) {
			$dptUph = 0;
			$scek = 'select distinct absensi from ' . $dbname . '.sdm_absensidt where ' . "\r\n" . '                               tanggal=\'' . $t . '\' and karyawanid=\'' . $id . '\'';

			#exit(mysql_error($conn));
			($qcek = mysql_query($scek)) || true;
			$rcek = mysql_num_rows($qcek);

			if (0 < $rcek) {
				continue;
			}

			$where = 'karyawanid=\'' . $id . '\'';
			$tpKary = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $where);

			if (($tpKary[$id] == 4) && ($jnlibur != 'MG')) {
				$sUmr = 'select sum(jumlah) as jumlah from ' . $dbname . '.sdm_5gajipokok ' . "\r\n" . '                                    where karyawanid=\'' . $id . '\' and tahun=\'' . substr($t, 0, 4) . '\'  and idkomponen =\'1\'';

				#exit(mysql_error());
				($qUmr = mysql_query($sUmr)) || true;
				$rUmr = mysql_fetch_assoc($qUmr);
				$umr = $rUmr['jumlah'] / 25;
				$kbnhadir = 'select count(absensi) as hadir from ' . $dbname . '.kebun_kehadiran_vw ' . 'where karyawanid=\'' . $id . '\' and tanggal like \'' . substr($t, 0, 7) . '%\' and absensi=\'H\'';

				#exit(mysql_error($conn));
				($qkbnhadir = mysql_query($kbnhadir)) || true;
				$rkbhadir = mysql_fetch_assoc($qkbnhadir);
				$sItung = 'select count(absensi) as hadir from ' . $dbname . '.sdm_absensidt ' . "\r\n" . '                                             where karyawanid=\'' . $id . '\' and tanggal like \'' . substr($t, 0, 7) . '%\' and absensi=\'H\'';

				#exit(mysql_error($conn));
				($qItung = mysql_query($sItung)) || true;
				$rItung = mysql_fetch_assoc($qItung);
				$kehadiran = $rItung['hadir'] + $rkbhadir['hadir'];

				if (16 < $kehadiran) {
					$dptUph = $umr;
				}
				else {
					$dptUph = 0;
				}
			}

			$itunganmasukdt += 1;

			if (isset($strix)) {
				$strix .= ',(\'' . $subbagian[$id] . '\',\'' . $t . '\',\'' . $id . '\',\'' . $jnlibur . '\',\'00:00:00\',\'00:00:00\',0,\'' . $dptUph . '\')';
			}
			else {
				$strix .= '(\'' . $subbagian[$id] . '\',\'' . $t . '\',\'' . $id . '\',\'' . $jnlibur . '\',\'00:00:00\',\'00:00:00\',0,\'' . $dptUph . '\')';
			}
		}

		if ($itunganmasukdt != 0) {
			$sdel = 'delete from ' . $dbname . '.sdm_absensidt where kodeorg=\'' . $bagian . '\' and tanggal=\'' . $t . '\'';

			if (mysql_query($sdel)) {
				$strix2 = 'insert into ' . $dbname . '.sdm_absensidt(kodeorg,tanggal,karyawanid,absensi,jam,jamPlg,catu,insentif) values ' . $strix . ';';

				if (mysql_query($strix2)) {
				}
				else {
					echo 'Error:' . mysql_error($conn) . '____' . $strix2;
				}
			}
		}
	}
}

?>
