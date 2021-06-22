<?php


echo '<link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n";
require_once 'config/connection.php';
$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());
$updatetime = date('d M Y H:i:s', time());
$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt - 86400);
$skaryawan = 'select a.tanggallahir, a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from ' . $dbname . '.datakaryawan a ' . "\r\n" . '    left join ' . $dbname . '.sdm_5jabatan b on a.kodejabatan=b.kodejabatan ' . "\r\n" . '    left join ' . $dbname . '.sdm_5departemen c on a.bagian=c.kode ' . "\r\n" . '    where a.lokasitugas like \'%HO\' and ((a.tanggalkeluar >= \'' . $tangsys1 . '\' and a.tanggalkeluar <= \'' . $tangsys2 . '\') or a.tanggalkeluar is NULL)' . "\r\n" . '    order by namakaryawan asc';
$res = mysql_query($skaryawan);

while ($bar = mysql_fetch_object($res)) {
	$karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
	$karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
	$karyawan[$bar->karyawanid]['lahir'] = $bar->tanggallahir;
}

$str = 'SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.keperluan ' . "\r\n" . '    FROM ' . $dbname . '.sdm_ijin a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.datakaryawan c on a.karyawanid=c.karyawanid        ' . "\r\n" . '    WHERE substr(a.darijam,1,10) <= \'' . $hariini . '\' and substr(a.sampaijam,1,10) >= \'' . $hariini . '\' and stpersetujuan1 = \'1\' and stpersetujuanhrd = \'1\'' . "\r\n" . '    ORDER BY a.darijam, a.sampaijam';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	if (substr($bar->lokasitugas, 2, 2) == 'HO') {
		$karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
		$karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
	}

	$presensi[$bar->karyawanid]['waktuijin'] = $bar->daritanggal . ' - ' . $bar->sampaitanggal;
	$presensi[$bar->karyawanid]['kehadiran'] = $bar->jenisijin;
	$presensi[$bar->karyawanid]['keterangan'] = $bar->keperluan;
}

$str = 'SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tugas1, a.tugas2, a.tugas3, c.namakaryawan, a.kodeorg FROM ' . $dbname . '.sdm_pjdinasht a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.datakaryawan c on a.karyawanid=c.karyawanid        ' . "\r\n" . '    WHERE a.tanggalperjalanan <= \'' . $hariini . '\' and a.tanggalkembali >= \'' . $hariini . '\' order by a.tanggalperjalanan, a.tanggalkembali';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	if ($bar->kodeorg == 'MJHO') {
		$karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
		$karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
	}

	$presensi[$bar->karyawanid]['waktudinas'] = $bar->tanggalperjalanan . ' - ' . $bar->tanggalkembali;
	$presensi[$bar->karyawanid]['kehadiran'] = 'DINAS';
	$presensi[$bar->karyawanid]['keterangan'] = $bar->tugas1 . ' ' . $bar->tugas2 . ' ' . $bar->tugas3;
}

$str = 'SELECT a.pin, a.scan_date, b.karyawanid, c.namakaryawan FROM ' . $dbname . '.att_log a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.att_adaptor b on a.pin=b.pin' . "\r\n" . '    LEFT JOIN ' . $dbname . '.datakaryawan c on b.karyawanid=c.karyawanid        ' . "\r\n" . '    WHERE scan_date like \'' . $hariini . '%\'  and scan_date < \'' . $hariini . ' 12:00:00\'' . "\r\n" . '    ORDER BY scan_date desc';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	if (!isset($bar->karyawanid)) {
	}
	else {
		$karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
		$karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
		$presensi[$bar->karyawanid]['waktumasuk'] = $bar->scan_date;
		$presensi[$bar->karyawanid]['kehadiran'] = 'MASUK';
	}
}

$str = 'SELECT a.pin, a.scan_date, b.karyawanid, c.namakaryawan FROM ' . $dbname . '.att_log a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.att_adaptor b on a.pin=b.pin' . "\r\n" . '    LEFT JOIN ' . $dbname . '.datakaryawan c on b.karyawanid=c.karyawanid        ' . "\r\n" . '    WHERE scan_date like \'' . $hariini . '%\' and scan_date >= \'' . $hariini . ' 12:00:00\'' . "\r\n" . '    ORDER BY scan_date asc';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	if (!isset($bar->karyawanid)) {
	}
	else {
		$karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
		$karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
		$presensi[$bar->karyawanid]['waktukeluar'] = $bar->scan_date;
		$presensi[$bar->karyawanid]['kehadiran'] = 'PULANG';
	}
}

$jumlahkaryawan = 0;

if (!empty($karyawan)) {
	foreach ($karyawan as $kar) {
		$jumlahkaryawan += 1;
	}
}

if (!empty($karyawan)) {
	foreach ($karyawan as $c => $key) {
		$sort_nama[] = $key['nama'];
	}
}

if (!empty($karyawan)) {
	array_multisort($sort_nama, SORT_ASC, $karyawan);
}

$qwe = 'PRESENSI HO ' . $tanggal . ' = ' . number_format($jumlahkaryawan) . ' orang';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tr class=rowcontent>' . "\r\n" . '    <td>' . $qwe . '</td>' . "\r\n" . '    <td align=right width=1% nowrap>' . $updatetime . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </table>';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:120px;\'>Nama Karyawan</td>' . "\r\n" . '        <td align=center style=\'width:80px;\'>Kehadiran</td>' . "\r\n" . '        <td align=center style=\'width:160px;\'>Waktu</td>' . "\r\n" . '        <td align=center style=\'width:120px;\'>Keterangan</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody></tbody></table>';
echo '<marquee height=120 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>' . "\r\n" . '    <table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tbody>';

if (!empty($karyawan)) {
	foreach ($karyawan as $kar) {
		echo '<tr class=rowcontent>';

		if (substr($kar['lahir'], 5, 5) == substr($hariini, 5, 5)) {
			$tahunini = substr($hariini, 0, 4);
			$tahunlahir = substr($kar['lahir'], 0, 4);
			$umur = $tahunini - $tahunlahir;
			echo '<td style=\'width:120px;\'>' . $kar['nama'] . ' (' . $umur . ')</td>';
		}
		else {
			echo '<td style=\'width:120px;\'>' . $kar['nama'] . '</td>';
		}

		echo '<td style=\'width:80px;\'>' . $presensi[$kar['id']]['kehadiran'] . '</td>';
		$warning = false;
		$waktu = '';
		if ($presensi[$kar['id']]['waktumasuk'] || $presensi[$kar['id']]['waktukeluar']) {
			$presensi[$kar['id']]['keterangan'] = '';
			$waktu = substr($presensi[$kar['id']]['waktumasuk'], 11, 8);

			if ($presensi[$kar['id']]['waktukeluar']) {
				$waktu .= ' - ' . substr($presensi[$kar['id']]['waktukeluar'], 11, 8);
			}

			if (('2013-07-10' <= $hariini) && ($hariini <= '2013-08-08')) {
				if ($presensi[$kar['id']]['waktumasuk']) {
					if ('07:30' < substr($presensi[$kar['id']]['waktumasuk'], 11, 5)) {
						$warning = true;
					}
				}

				if ($presensi[$kar['id']]['waktukeluar']) {
					if (substr($presensi[$kar['id']]['waktukeluar'], 11, 5) < '16:00') {
						$warning = true;
					}
				}
			}
			else if ($hariini == '2013-10-14') {
				if ($presensi[$kar['id']]['waktumasuk']) {
					if ('08:00' < substr($presensi[$kar['id']]['waktumasuk'], 11, 5)) {
						$warning = true;
					}
				}

				if ($presensi[$kar['id']]['waktukeluar']) {
					if (substr($presensi[$kar['id']]['waktukeluar'], 11, 5) < '15:00') {
						$warning = true;
					}
				}
			}
			else {
				if ($presensi[$kar['id']]['waktumasuk']) {
					if ('08:00' < substr($presensi[$kar['id']]['waktumasuk'], 11, 5)) {
						$warning = true;
					}
				}

				if ($presensi[$kar['id']]['waktukeluar']) {
					if (substr($presensi[$kar['id']]['waktukeluar'], 11, 5) < '17:00') {
						$warning = true;
					}
				}
			}
		}
		else if ($presensi[$kar['id']]['waktudinas']) {
			$waktu = $presensi[$kar['id']]['waktudinas'];
		}
		else if ($presensi[$kar['id']]['waktucuti']) {
			$waktu = $presensi[$kar['id']]['waktucuti'];
		}
		else if ($presensi[$kar['id']]['waktuijin']) {
			$waktu = $presensi[$kar['id']]['waktuijin'];
		}
		if ($warning) {
			$waktu = '<font color=red>' . $waktu . '</font>';
		}

		echo '<td align=center style=\'width:160px;\'>' . $waktu . '</td>';
		echo '<td style=\'width:120px;\'>' . substr($presensi[$kar['id']]['keterangan'], 0, 25) . '</td>';
		echo '</tr>';
	}
}

echo '</tbody>' . "\r\n" . '    </table>' . "\r\n" . '    * sumber data: fingerprint + e-Agro' . "\r\n" . '    </marquee>';

?>
