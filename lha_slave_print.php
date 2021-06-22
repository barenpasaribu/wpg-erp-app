<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdOrg'];
$kdAfd = $_POST['kdAfd'];
$tgl1_ = $_POST['tgl1'];
$tgl2_ = $_POST['tgl2'];
if (($proses == 'excel') || ($proses == 'pdf')) {
	$kdOrg = $_GET['kdOrg'];
	$kdAfd = $_GET['kdAfd'];
	$tgl1_ = $_GET['tgl1'];
	$tgl2_ = $_GET['tgl2'];
}

if ($kdAfd == '') {
	$kdAfd = $kdOrg;
}

$lha = true;

if ($tgl2_ != '') {
	$lha = false;
}

$luas = 0;
$str = 'select luasareaproduktif from ' . $dbname . '.setup_blok ' . "\r\n" . '                where kodeorg like \'' . $kdAfd . '%\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$luas += $bar->luasareaproduktif;
}

$tgl1_ = tanggalsystem($tgl1_);
$tgl1 = substr($tgl1_, 0, 4) . '-' . substr($tgl1_, 4, 2) . '-' . substr($tgl1_, 6, 2);
$tgl2_ = tanggalsystem($tgl2_);
$tgl2 = substr($tgl2_, 0, 4) . '-' . substr($tgl2_, 4, 2) . '-' . substr($tgl2_, 6, 2);
$tglqwe1 = juliantojd(substr($tgl1_, 4, 2), substr($tgl1_, 6, 2), substr($tgl1_, 0, 4));
$tglqwe2 = juliantojd(substr($tgl2_, 4, 2), substr($tgl2_, 6, 2), substr($tgl2_, 0, 4));
$jumlahhari = (1 + $tglqwe2) - $tglqwe1;
if (($proses == 'preview') || ($proses == 'excel') || ($proses == 'pdf')) {
	if ($kdOrg == '') {
		echo 'Error: Organization/estate required.';
		exit();
	}

	if ($tgl1_ == '') {
		echo 'Error: date required.';
		exit();
	}
}

$str = 'select kodekegiatan,namakegiatan,namakegiatan1 from ' . $dbname . '.setup_kegiatan';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if ($_SESSION['language'] == 'EN') {
		$kegiatanx[$bar->kodekegiatan] = $bar->namakegiatan1;
	}
	else {
		$kegiatanx[$bar->kodekegiatan] = $bar->namakegiatan;
	}
}

if ($proses == 'getAfdAll') {
	$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '                where kodeorganisasi like \'' . $kdAfd . '%\' and length(kodeorganisasi)=6 order by namaorganisasi desc';
	$op = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$op .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo $op;
	exit();
}
else {
	if ($_SESSION['language'] == 'EN') {
		$caption = 'DAILY DIVISION REPORT';
	}
	else {
		$caption = 'LAPORAN HARIAN AFDELING';
	}

	$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi in(select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($kdAfd, 0, 4) . '\') limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$namapt = $bar->namaorganisasi;
	}

	if ($lha) {
		$tanggalsampai = '';
	}
	else {
		$tanggalsampai = tanggalnormal($tgl2_);
	}

	$stream .= '<table width=100%>' . "\r\n" . '                 <tr>' . "\r\n" . '                     <td colspan=5>' . $namapt . '</td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '                     <td align=center colspan=5>' . "\r\n" . '                    ' . $caption . "\r\n" . '                     </td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '                     <td style=\'width:75px;\'>' . $_SESSION['lang']['kebun'] . '</td><td style=\'width:75px;\'>:' . substr($kdAfd, 0, 4) . '</td><td></td><td style=\'width:75px;\'>' . $_SESSION['lang']['diperiksa'] . '</td><td style=\'width:75px;\'>' . $_SESSION['lang']['dibuat'] . '</td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '                    <td>' . $_SESSION['lang']['afdeling'] . '</td><td>:' . $kdAfd . '</td><td>(' . number_format($luas, 2) . ' Ha)</td><td> </td><td> </td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '                    <td>' . $_SESSION['lang']['tanggal'] . '</td><td>:' . tanggalnormal($tgl1_) . '</td><td>' . $tanggalsampai . '</td><td> </td><td> </td>' . "\r\n" . '                 </tr>   ' . "\r\n" . '                 <tr>' . "\r\n" . '                    <td></td><td></td><td></td><td>' . $_SESSION['lang']['askep'] . '</td><td>' . $_SESSION['lang']['asisten'] . '</td>' . "\r\n" . '                 </tr>                   ' . "\r\n" . '                </table>';

	if ($proses == 'excel') {
		$stream .= '<table border=\'1\'>';
	}
	else {
		$stream .= '<table cellspacing=\'1\' border=\'0\' class=\'sortable\' width=100%>';
	}

	$stream .= '<thead>' . "\r\n\t" . '<tr class=rowheader>' . "\r\n" . '        <td rowspan=2 align=center  >' . $_SESSION['lang']['kode'] . '</td>' . "\r\n" . '        <td rowspan=2 align=center>' . $_SESSION['lang']['vhc_jenis_pekerjaan'] . '</td>    ' . "\r\n\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>' . $_SESSION['lang']['kodeblok'] . '</td>            ' . "\r\n\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['thntnm'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>HK KHT/KHL</td>    ' . "\r\n\t" . '<td colspan=2 align=center>HK KBL</td>          ' . "\r\n\t" . '<td colspan=2 align=center>' . $_SESSION['lang']['upahkerja'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>' . $_SESSION['lang']['hasilkerjajumlah'] . '</td>' . "\r\n\t" . '<td colspan=4 align=center>' . $_SESSION['lang']['pemakaianBarang'] . '</td>' . "\r\n" . '        <td colspan=2 align=center>' . $_SESSION['lang']['material'] . ' ' . $_SESSION['lang']['biaya'] . '</td>' . "\r\n\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['totalbiaya'] . '</td>' . "\r\n" . '        <td rowspan=2 align=center>Rp/' . $_SESSION['lang']['satuan'] . '</td>    ' . "\r\n" . '        <td rowspan=2 align=center>HK/' . $_SESSION['lang']['satuan'] . '</td>    ' . "\r\n" . '        </tr>' . "\r\n" . '        ' . "\r\n" . ' ' . "\t" . '<tr class=rowheader>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['luas'] . '</td>';

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>    ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['sdhi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>    ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['sdhi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	$stream .= '<td align=center>Rp/unit</td>            ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>';

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['sdhi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	$stream .= '<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['satuan'] . '</td>';

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['sdhi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	$stream .= '<td align=center>Rp/unit</td>            ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>   ' . "\r\n" . '        </tr>       ' . "\r\n" . '        </thead>' . "\r\n\t" . '<tbody>';

	if ($lha) {
		$str = 'select distinct kodekegiatan,kodeorg,namakegiatan,satuan from ' . $dbname . '.kebun_perawatan_dan_spk_vw where kodeorg like \'' . $kdAfd . '%\' ' . "\r\n" . '             and tanggal =\'' . $tgl1_ . '\'';
	}
	else {
		$str = 'select distinct kodekegiatan,kodeorg,namakegiatan,satuan from ' . $dbname . '.kebun_perawatan_dan_spk_vw where kodeorg like \'' . $kdAfd . '%\' ' . "\r\n" . '             and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\'';
	}

	$res = mysql_query($str);
	$master = array();

	while ($bar = mysql_fetch_object($res)) {
		$master['kegiatan'][] = $bar->kodekegiatan;
		$master['blok'][] = $bar->kodeorg;
		$master['namakegiatan'][] = $bar->namakegiatan;
		$master['satuankegiatan'][] = $bar->satuan;
	}

	$str = 'select kodeorg,tahuntanam,luasareaproduktif from ' . $dbname . '.setup_blok where kodeorg  like \'' . $kdAfd . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$blok[$bar->kodeorg]['kode'] = $bar->kodeorg;
		$blok[$bar->kodeorg]['thntnm'] = $bar->tahuntanam;
		$blok[$bar->kodeorg]['luas'] = $bar->luasareaproduktif;
	}

	if (!empty($master['blok'])) {
		foreach ($master['blok'] as $key => $val) {
			$master['luas'][$key] = 1;
			$master['thntnm'][$key] = 0;

			if ($val == $blok[$val]['kode']) {
				$master['luas'][$key] = $blok[$val]['luas'];
				$master['thntnm'][$key] = $blok[$val]['thntnm'];
			}
		}
	}
	if ($lha) {
		$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah ' . "\r\n" . '          from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal=\'' . $tgl1_ . '\' and tipekaryawan=\'KBL\' and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}
	else {
		$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah ' . "\r\n" . '          from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and tipekaryawan=\'KBL\' and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}

	$hkKBL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKBL['kegiatan'][] = $bar->kodekegiatan;
		$hkKBL['blok'][] = $bar->kodeorg;
		$hkKBL['jhk'][] = $bar->jhk;
		$hkKBL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hkkbl'][$key] = 0;
			$master['upahkbl'][$key] = 0;

			if (0 < count($hkKBL['kegiatan'])) {
				if (!empty($hkKBL['kegiatan'])) {
					foreach ($hkKBL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKBL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkbl'][$key] = $hkKBL['jhk'][$g];
							$master['upahkbl'][$key] = $hkKBL['upah'][$g];
						}
					}
				}
			}
		}
	}

	$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah ' . "\r\n" . '          from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\' and tipekaryawan=\'KBL\' and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	$hkKBL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKBL['kegiatan'][] = $bar->kodekegiatan;
		$hkKBL['blok'][] = $bar->kodeorg;
		$hkKBL['jhk'][] = $bar->jhk;
		$hkKBL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hkkblsbi'][$key] = 0;
			$master['upahkblsbi'][$key] = 0;

			if (0 < count($hkKBL['kegiatan'])) {
				if (!empty($hkKBL['kegiatan'])) {
					foreach ($hkKBL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKBL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkblsbi'][$key] = $hkKBL['jhk'][$g];
							$master['upahkblsbi'][$key] = $hkKBL['upah'][$g];
						}
					}
				}
			}
		}
	}
	if ($lha) {
		$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal=\'' . $tgl1_ . '\' and tipekaryawan in(\'KHL\',\'KHT\',\'Kontrak\',\'Kontrak Karywa (Usia Lanjut)\') and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}
	else {
		$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and tipekaryawan in(\'KHL\',\'KHT\',\'Kontrak\',\'Kontrak Karywa (Usia Lanjut)\') and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}

	$hkKHL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKHL['kegiatan'][] = $bar->kodekegiatan;
		$hkKHL['blok'][] = $bar->kodeorg;
		$hkKHL['jhk'][] = $bar->jhk;
		$hkKHL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hkkhl'][$key] = 0;
			$master['upahkhl'][$key] = 0;

			if (0 < count($hkKHL['kegiatan'])) {
				if (!empty($hkKHL['kegiatan'])) {
					foreach ($hkKHL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKHL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkhl'][$key] = $hkKHL['jhk'][$g];
							$master['upahkhl'][$key] = $hkKHL['upah'][$g];
						}
					}
				}
			}
		}
	}
	if ($lha) {
		$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal=\'' . $tgl1_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	}
	else {
		$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	}

	$hkKHL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKHL['kegiatan'][] = $bar->kodekegiatan;
		$hkKHL['blok'][] = $bar->kodeorg;
		$hkKHL['jhk'][] = $bar->jhk;
		$hkKHL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			if (0 < count($hkKHL['kegiatan'])) {
				if (!empty($hkKHL['kegiatan'])) {
					foreach ($hkKHL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKHL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkhl'] += $key;
							$master['upahkhl'] += $key;
						}
					}
				}
			}
		}
	}

	$str = 'select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ' . $dbname . '.kebun_kehadiran_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\' and tipekaryawan in(\'KHL\',\'KHT\',\'Kontrak\') and umr != \'0\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	$hkKHL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKHL['kegiatan'][] = $bar->kodekegiatan;
		$hkKHL['blok'][] = $bar->kodeorg;
		$hkKHL['jhk'][] = $bar->jhk;
		$hkKHL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hkkhlsbi'][$key] = 0;
			$master['upahkhlsbi'][$key] = 0;

			if (0 < count($hkKHL['kegiatan'])) {
				if (!empty($hkKHL['kegiatan'])) {
					foreach ($hkKHL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKHL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkhlsbi'][$key] = $hkKHL['jhk'][$g];
							$master['upahkhlsbi'][$key] = $hkKHL['upah'][$g];
						}
					}
				}
			}
		}
	}

	$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	$hkKHL = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hkKHL['kegiatan'][] = $bar->kodekegiatan;
		$hkKHL['blok'][] = $bar->kodeorg;
		$hkKHL['jhk'][] = $bar->jhk;
		$hkKHL['upah'][] = $bar->upah;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			if (0 < count($hkKHL['kegiatan'])) {
				if (!empty($hkKHL['kegiatan'])) {
					foreach ($hkKHL['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hkKHL['blok'][$g] == $master['blok'][$key])) {
							$master['hkkhlsbi'] += $key;
							$master['upahkhlsbi'] += $key;
						}
					}
				}
			}
		}
	}

	if (!empty($master['upahkhl'])) {
		foreach ($master['upahkhl'] as $kut => $uk) {
			$master['totalupah'][$kut] = $master['upahkbl'][$kut] + $master['upahkhl'][$kut];
			@$master['rpperhk'][$kut] = $master['totalupah'][$kut] / ($master['hkkhl'][$kut] + $master['hkkbl'][$kut]);
			$master['totbiaya'][$kut] = $master['totalupah'][$kut];
		}
	}
	if ($lha) {
		$str = 'select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from ' . "\r\n" . '            ' . $dbname . '.kebun_perawatan_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal=\'' . $tgl1_ . '\' ' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}
	else {
		$str = 'select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from ' . "\r\n" . '            ' . $dbname . '.kebun_perawatan_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\'' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	}

	$hasil = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hasil['kegiatan'][] = $bar->kodekegiatan;
		$hasil['blok'][] = $bar->kodeorg;
		$hasil['hasil'][] = $bar->hasil;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hasilbi'][$key] = 0;

			if (0 < count($hasil['kegiatan'])) {
				if (!empty($hasil['kegiatan'])) {
					foreach ($hasil['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hasil['blok'][$g] == $master['blok'][$key])) {
							$master['hasilbi'][$key] = $hasil['hasil'][$g];
						}
					}
				}
			}
		}
	}
	if ($lha) {
		$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil' . "\r\n" . '            from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal=\'' . $tgl1_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	}
	else {
		$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil' . "\r\n" . '            from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	}

	$hasil = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hasil['kegiatan'][] = $bar->kodekegiatan;
		$hasil['blok'][] = $bar->kodeorg;
		$hasil['hasil'][] = $bar->hasil;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			if (0 < count($hasil['kegiatan'])) {
				if (!empty($hasil['kegiatan'])) {
					foreach ($hasil['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hasil['blok'][$g] == $master['blok'][$key])) {
							$master['hasilbi'] += $key;
						}
					}
				}
			}
		}
	}

	$str = 'select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from ' . "\r\n" . '            ' . $dbname . '.kebun_perawatan_vw' . "\r\n" . '          where  kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\' ' . "\r\n" . '          group by kodeorg,kodekegiatan;';
	$hasil = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hasil['kegiatan'][] = $bar->kodekegiatan;
		$hasil['blok'][] = $bar->kodeorg;
		$hasil['hasil'][] = $bar->hasil;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			$master['hasilsbi'][$key] = 0;

			if (0 < count($hasil['kegiatan'])) {
				if (!empty($hasil['kegiatan'])) {
					foreach ($hasil['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hasil['blok'][$g] == $master['blok'][$key])) {
							$master['hasilsbi'][$key] = $hasil['hasil'][$g];
						}
					}
				}
			}
		}
	}

	$str = 'select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil' . "\r\n" . '            from ' . $dbname . '.log_baspk' . "\r\n" . '          where  kodeblok like \'' . $kdAfd . '%\' and tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\'' . "\r\n" . '          group by kodeblok,kodekegiatan;';
	$hasil = array();
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hasil['kegiatan'][] = $bar->kodekegiatan;
		$hasil['blok'][] = $bar->kodeorg;
		$hasil['hasil'][] = $bar->hasil;
	}

	if (!empty($master['kegiatan'])) {
		foreach ($master['kegiatan'] as $key => $val) {
			if (0 < count($hasil['kegiatan'])) {
				if (!empty($hasil['kegiatan'])) {
					foreach ($hasil['kegiatan'] as $g => $h) {
						if (($val == $h) && ($hasil['blok'][$g] == $master['blok'][$key])) {
							$master['hasilsbi'] += $key;
						}
					}
				}
			}
		}
	}

	if (!empty($master['kegiatan'])) {
		if ($lha) {
			$str = 'SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan ' . "\r\n" . '           FROM ' . $dbname . '.kebun_pakai_material_vw a left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '           on a.kodebarang=b.kodebarang    ' . "\r\n" . '           where  tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\'       ' . "\r\n" . '           and a.kodeorg=\'' . $master['blok'][$key] . '\' and a.kodekegiatan=\'' . $val . '\'' . "\r\n" . '           group by kodekegiatan,kodeorg,kodebarang';
		}
		else {
			$str = 'SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan ' . "\r\n" . '           FROM ' . $dbname . '.kebun_pakai_material_vw a left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '           on a.kodebarang=b.kodebarang    ' . "\r\n" . '           where tanggal between \'' . $tgl1_ . '\'  and \'' . $tgl2_ . '\'        ' . "\r\n" . '           and a.kodeorg=\'' . $master['blok'][$key] . '\' and a.kodekegiatan=\'' . $val . '\'' . "\r\n" . '           group by kodekegiatan,kodeorg,kodebarang';
		}

		$barang = array();
		$res = mysql_query($str);

		if (mysql_numrows($res) < 1) {
			$master['barangsbi'][$key][] = 0;
			$master['kodebarangsbi'][$key][] = 0;
			$master['satuanbarangsbi'][$key][] = 0;
			$master['qtysbi'][$key][] = 0;
			$master['barangsbi'][$key][] = $bar->namabarang;
			$master['kodebarangsbi'][$key][] = $bar->kodebarang;
			$master['satuanbarangsbi'][$key][] = $bar->satuan;
			$master['qtysbi'][$key][] = $bar->qty;
		}
		else {
		}
	}

	if (!empty($master['kegiatan'])) {
		if ($lha) {
			$str = 'SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan ' . "\r\n" . '           FROM ' . $dbname . '.kebun_pakai_material_vw a left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '           on a.kodebarang=b.kodebarang    ' . "\r\n" . '           where  tanggal=\'' . $tgl1_ . '\'       ' . "\r\n" . '           and a.kodeorg=\'' . $master['blok'][$key] . '\' and a.kodekegiatan=\'' . $val . '\'' . "\r\n" . '           group by kodekegiatan,kodeorg,kodebarang';
		}
		else {
			$str = 'SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan ' . "\r\n" . '           FROM ' . $dbname . '.kebun_pakai_material_vw a left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '           on a.kodebarang=b.kodebarang    ' . "\r\n" . '           where  tanggal between \'' . $tgl1_ . '\'  and \'' . $tgl2_ . '\'      ' . "\r\n" . '           and a.kodeorg=\'' . $master['blok'][$key] . '\' and a.kodekegiatan=\'' . $val . '\'' . "\r\n" . '           group by kodekegiatan,kodeorg,kodebarang';
		}

		$barang = array();
		$res = mysql_query($str);

		if (mysql_numrows($res) < 1) {
			$master['qtybi'][$key][] = 0;

			if (!empty($master['kodebarangsbi'][$key])) {
				foreach ($master['kodebarangsbi'][$key] as $kunci => $isi) {
					if ($bar->kodebarang == $isi) {
						$master['qtybi'][$key][$kunci] = $bar->qty;
					}
				}
			}
		}
		else {
		}
	}

	$t = mktime(0, 0, 0, intval(substr($tgl1_, 4, 2)), 15, intval(substr($tgl1_, 0, 4)));
	$bl = date('Y-m', $t);

	if ($lha) {
		$qwetgl = $tgl1_;
	}
	else {
		$qwetgl = $tgl2_;
	}

	$str = 'SELECT distinct b.kodebarang,a.hargarata FROM ' . $dbname . '.kebun_pakai_material_vw b' . "\r\n" . '      left join ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '      on b.kodebarang=a.kodebarang' . "\r\n" . '      where b.kodeorg like \'' . $kdAfd . '%\' and b.tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $qwetgl . '\'' . "\r\n" . '      and a.periode=\'' . $bl . '\'' . "\r\n" . '      and a.kodeorg in(select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($kdAfd, 0, 4) . '\')';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$harga[$bar->kodebarang] = $bar->hargarata;
	}

	if (!empty($master['kodebarangsbi'])) {
		foreach ($master['kodebarangsbi'] as $kuku => $vx) {
			if (!empty($master['kodebarangsbi'][$kuku])) {
				foreach ($master['kodebarangsbi'][$kuku] as $jack => $pot) {
					$master['hargabarangbi'][$kuku][$jack] = $harga[$pot];
					$master['bybarangbi'][$kuku][$jack] = $harga[$pot] * $master['qtybi'][$kuku][$jack];
					$master['totbiaya'] += $kuku;
				}
			}
		}
	}

	if (!empty($master['totbiaya'])) {
		foreach ($master['totbiaya'] as $kun => $tak) {
			@$master['rppersatuan'][$kun] = $tak / $master['hasilbi'][$kun];
		}
	}

	if (!empty($master['totbiaya'])) {
		foreach ($master['totbiaya'] as $kun => $tak) {
			@$master['hkpersatuan'][$kun] = ($master['hkkhl'][$kun] + $master['hkkbl'][$kun]) / $master['hasilbi'][$kun];
		}
	}

	if (!empty($master['blok'])) {
		foreach ($master['blok'] as $kun => $tak) {
			$TOTAL += 'luas';
			$TOTAL += 'hkkhlbi';
			$TOTAL += 'hkkhlsbi';
			$TOTAL += 'hkkblbi';
			$TOTAL += 'hkkblsbi';
			$TOTAL += 'totalupah';
			$TOTAL += 'hasilbi';
			$TOTAL += 'hasilsbi';
			$TOTAL += 'totalbiaya';
			$TOTAL += 'rppersatuan';
			$TOTAL += 'hkpersatuan';

			if (!empty($master['bybarangbi'][$kun])) {
				foreach ($master['bybarangbi'][$kun] as $la => $li) {
					$TOTAL += 'totalbiayabarang';
				}
			}
		}
	}

	@$TOTAL['rpperhk'] = $TOTAL['totalupah'] / ($TOTAL['hkkhlbi'] + $TOTAL['hkkblbi']);

	if (!empty($master['blok'])) {
		foreach ($master['blok'] as $kunc => $va) {
			if ($proses == 'excel') {
				if (!empty($master['barangsbi'][$kunc])) {
					foreach ($master['barangsbi'][$kunc] as $dd => $ee) {
						$stream .= '<tr class=rowcontent>' . "\r\n" . '            <td>' . $master['kegiatan'][$kunc] . '</td>' . "\r\n" . '            <td>' . $kegiatanx[$master['kegiatan'][$kunc]] . '</td>    ' . "\r\n" . '            <td>' . $master['satuankegiatan'][$kunc] . '</td>' . "\r\n" . '            <td>' . $master['blok'][$kunc] . '</td>' . "\r\n" . '            <td align=right>' . number_format($master['luas'][$kunc], 2) . '</td>' . "\r\n" . '            <td>' . $master['thntnm'][$kunc] . '</td>';

						if ($lha) {
							$stream .= '<td align=right>' . $master['hkkhl'][$kunc] . '</td>         ' . "\r\n" . '            <td align=right>' . number_format($master['hkkhlsbi'][$kunc], 2) . '</td>';
						}
						else {
							$stream .= '<td align=right colspan=2>' . number_format($master['hkkhl'][$kunc], 2) . '</td>';
						}

						if ($lha) {
							$stream .= '<td align=right>' . $master['hkkbl'][$kunc] . '</td>' . "\r\n" . '            <td align=right>' . $master['hkkblsbi'][$kunc] . '</td>';
						}
						else {
							$stream .= '<td align=right colspan=2>' . number_format($master['hkkbl'][$kunc], 2) . '</td>';
						}

						$stream .= '<td align=right>' . number_format($master['rpperhk'][$kunc], 0) . '</td>    ' . "\r\n" . '            <td align=right>' . number_format($master['totalupah'][$kunc], 0) . '</td>';
						$warnasel = '';

						if ($master['satuankegiatan'][$kunc] == 'HA') {
							if (round($master['luas'][$kunc], 2) < round($master['hasilbi'][$kunc], 2)) {
								$warnasel = ' bgcolor="red"';
							}
						}
						if ($lha) {
							$stream .= '<td align=right' . $warnasel . '>' . number_format($master['hasilbi'][$kunc], 2) . '</td>    ' . "\r\n" . '            <td align=right>' . number_format($master['hasilsbi'][$kunc], 2) . '</td>';
						}
						else {
							$stream .= '<td align=right colspan=2' . $warnasel . '>' . number_format($master['hasilbi'][$kunc], 2) . '</td>';
						}

						$stream .= '<td>' . $master['barangsbi'][$kunc][$dd] . '</td>' . "\r\n" . '                    <td>' . $master['satuanbarangsbi'][$kunc][$dd] . '</td>';

						if ($lha) {
							$stream .= '<td align=right>' . number_format($master['qtybi'][$kunc][$dd], 2) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($master['qtysbi'][$kunc][$dd], 2) . '</td>';
						}
						else {
							$stream .= '<td align=right colspan=2>' . number_format($master['qtybi'][$kunc][$dd], 2) . '</td>';
						}

						$stream .= '<td align=right>' . number_format($master['hargabarangbi'][$kunc][$dd], 0) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($master['bybarangbi'][$kunc][$dd], 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($master['totbiaya'][$kunc], 0) . '</td>   ' . "\r\n" . '            <td align=right>' . number_format($master['rppersatuan'][$kunc], 0) . '</td>                 ' . "\r\n" . '            <td align=right>' . number_format($master['hkpersatuan'][$kunc], 2) . '</td>                 ' . "\r\n" . '                    </tr>';
					}
				}
			}
			else {
				$stream .= '<tr class=rowcontent>' . "\r\n" . '            <td>' . $master['kegiatan'][$kunc] . '</td>' . "\r\n" . '            <td>' . $kegiatanx[$master['kegiatan'][$kunc]] . '</td>    ' . "\r\n" . '            <td>' . $master['satuankegiatan'][$kunc] . '</td>' . "\r\n" . '            <td>' . $master['blok'][$kunc] . '</td>' . "\r\n" . '            <td align=right>' . number_format($master['luas'][$kunc], 2) . '</td>' . "\r\n" . '            <td>' . $master['thntnm'][$kunc] . '</td>';

				if ($lha) {
					$stream .= '<td align=right>' . $master['hkkhl'][$kunc] . '</td>         ' . "\r\n" . '            <td align=right>' . number_format($master['hkkhlsbi'][$kunc], 2) . '</td>';
				}
				else {
					$stream .= '<td align=right colspan=2>' . number_format($master['hkkhl'][$kunc], 2) . '</td>';
				}

				if ($lha) {
					$stream .= '<td align=right>' . $master['hkkbl'][$kunc] . '</td>' . "\r\n" . '            <td align=right>' . $master['hkkblsbi'][$kunc] . '</td>';
				}
				else {
					$stream .= '<td align=right colspan=2>' . number_format($master['hkkbl'][$kunc], 2) . '</td>';
				}

				$stream .= '<td align=right>' . number_format($master['rpperhk'][$kunc], 0) . '</td>    ' . "\r\n" . '            <td align=right>' . number_format($master['totalupah'][$kunc], 0) . '</td>';
				$warnasel = '';

				if ($master['satuankegiatan'][$kunc] == 'HA') {
					if (round($master['luas'][$kunc], 2) < round($master['hasilbi'][$kunc], 2)) {
						$warnasel = ' bgcolor="red"';
					}
				}
				if ($lha) {
					$stream .= '<td align=right' . $warnasel . '>' . number_format($master['hasilbi'][$kunc], 2) . '</td>    ' . "\r\n" . '            <td align=right>' . number_format($master['hasilsbi'][$kunc], 2) . '</td>';
				}
				else {
					$stream .= '<td align=right colspan=2' . $warnasel . '>' . number_format($master['hasilbi'][$kunc], 2) . '</td>';
				}

				$stream .= '<td></td>' . "\r\n" . '            <td></td>';

				if ($lha) {
					$stream .= '<td></td>' . "\r\n" . '            <td></td>';
				}
				else {
					$stream .= '<td colspan=2></td>';
				}

				$stream .= '<td></td>' . "\r\n" . '            <td></td>' . "\r\n" . '            <td align=right>' . number_format($master['totbiaya'][$kunc], 0) . '</td>   ' . "\r\n" . '            <td align=right>' . number_format($master['rppersatuan'][$kunc], 0) . '</td>                 ' . "\r\n" . '            <td align=right>' . number_format($master['hkpersatuan'][$kunc], 2) . '</td>                 ' . "\r\n" . '            </tr>';

				if (!empty($master['barangsbi'][$kunc])) {
					foreach ($master['barangsbi'][$kunc] as $dd => $ee) {
						$stream .= '<tr class=rowcontent>' . "\r\n" . '                    <td></td>' . "\r\n" . '                    <td></td>    ' . "\r\n" . '                    <td></td>' . "\r\n" . '                    <td></td>' . "\r\n" . '                    <td align=right></td>' . "\r\n" . '                    <td></td>';

						if ($lha) {
							$stream .= '<td align=right></td>    ' . "\r\n" . '                    <td align=right></td>';
						}
						else {
							$stream .= '<td align=right colspan=2></td>';
						}

						if ($lha) {
							$stream .= '<td align=right></td>    ' . "\r\n" . '                    <td align=right></td>';
						}
						else {
							$stream .= '<td align=right colspan=2></td>';
						}

						$stream .= '<td align=right></td>    ' . "\r\n" . '                    <td align=right></td>';

						if ($lha) {
							$stream .= '<td align=right></td>    ' . "\r\n" . '                    <td align=right></td>';
						}
						else {
							$stream .= '<td align=right colspan=2></td>';
						}

						$stream .= '<td>' . $master['barangsbi'][$kunc][$dd] . '</td>' . "\r\n" . '                    <td>' . $master['satuanbarangsbi'][$kunc][$dd] . '</td>';

						if ($lha) {
							$stream .= '<td align=right>' . number_format($master['qtybi'][$kunc][$dd], 2) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($master['qtysbi'][$kunc][$dd], 2) . '</td>';
						}
						else {
							$stream .= '<td align=right colspan=2>' . number_format($master['qtybi'][$kunc][$dd], 2) . '</td>';
						}

						$stream .= '<td align=right>' . number_format($master['hargabarangbi'][$kunc][$dd], 0) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($master['bybarangbi'][$kunc][$dd], 0) . '</td>' . "\r\n" . '                    <td align=right></td>   ' . "\r\n" . '                    <td align=right></td>                 ' . "\r\n" . '                    <td align=right></td>                 ' . "\r\n" . '                    </tr>';
					}
				}
			}
		}
	}

	$stream .= "\r\n\t" . '<tr class=header>' . "\r\n\t" . '<td colspan=4>Total</td>' . "\r\n\t" . '<td align=right>' . number_format($TOTAL['luas'], 2) . '</td>' . "\r\n" . '        <td></td>';

	if ($lha) {
		$stream .= '<td align=right>' . number_format($TOTAL['hkkhlbi'], 2) . '</td>' . "\r\n\t" . '<td align=right>' . number_format($TOTAL['hkkhlsbi'], 2) . '</td>';
	}
	else {
		$stream .= '<td align=right colspan=2>' . number_format($TOTAL['hkkhlbi'], 2) . '</td>';
	}

	if ($lha) {
		$stream .= '<td align=right>' . number_format($TOTAL['hkkblbi'], 2) . '</td>' . "\r\n\t" . '<td align=right>' . number_format($TOTAL['hkkblsbi'], 2) . '</td>';
	}
	else {
		$stream .= '<td align=right colspan=2>' . number_format($TOTAL['hkkblbi'], 2) . '</td>';
	}

	$stream .= '<td align=right></td>' . "\r\n\t" . '<td align=right>' . number_format($TOTAL['totalupah']) . '</td>';

	if ($lha) {
		$stream .= '<td align=right></td>' . "\r\n\t" . '<td align=right></td>';
	}
	else {
		$stream .= '<td align=right colspan=2></td>';
	}

	$stream .= '<td></td> ' . "\r\n" . '        <td></td>' . "\r\n" . '        <td></td>  ' . "\r\n" . '        <td></td>  ' . "\r\n" . '        <td></td>' . "\r\n" . '        <td align=right>' . number_format($TOTAL['totalbiayabarang']) . '</td> ' . "\r\n" . '        <td align=right>' . number_format($TOTAL['totalbiaya']) . '</td>' . "\r\n" . '        <td align=right></td>    ' . "\r\n" . '        <td align=right></td>    ' . "\r\n" . '        </tbody></table>';

	if ($proses == 'excel') {
		$stream .= '<br><table border=\'1\'>';
	}
	else {
		$stream .= '<br><table cellspacing=\'1\' border=\'0\' class=\'sortable\' width=100%>';
	}

	$stream .= '<thead>' . "\r\n\t" . '<tr class=rowheader>' . "\r\n" . '        <td rowspan=2 align=center  >' . $_SESSION['lang']['kode'] . '</td>' . "\r\n" . '        <td rowspan=2 align=center>' . $_SESSION['lang']['vhc_jenis_pekerjaan'] . '</td>    ' . "\r\n\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>' . $_SESSION['lang']['kodeblok'] . '</td>            ' . "\r\n\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['thntnm'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>HK</td>    ' . "\r\n\t" . '<td colspan=4 align=center>' . $_SESSION['lang']['biaya'] . '</td>' . "\r\n\t" . '<td colspan=2 align=center>' . $_SESSION['lang']['hasilkerjajumlah'] . '</td>' . "\r\n" . '        <td rowspan=2 align=center>Rp/Kg</td>    ' . "\r\n" . '        <td rowspan=2 align=center>Kg/HK</td>    ' . "\r\n" . '        </tr>        ' . "\r\n" . ' ' . "\t" . '<tr class=rowheader>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['blok'] . '</td>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['luas'] . '</td>';

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>    ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['sdhi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	$stream .= '<td align=center>' . $_SESSION['lang']['upah'] . '</td>            ' . "\r\n" . '            <td align=center>Premi</td>            ' . "\r\n" . '            <td align=center>Penalty</td>            ' . "\r\n\t" . '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>';

	if ($lha) {
		$stream .= '<td align=center>' . $_SESSION['lang']['hi'] . '</td>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['hi'] . '</td>';
	}
	else {
		$stream .= '<td align=center colspan=2></td>';
	}

	$stream .= '</tr>       ' . "\r\n" . '        </thead>' . "\r\n\t" . '<tbody>';
	$str = 'SELECT kodekegiatan,namakegiatan FROM ' . $dbname . '.setup_kegiatan ' . "\r\n" . '    where kelompok=\'PNN\' order by kodekegiatan asc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$kodepanen = $bar->kodekegiatan;
		$namapanen = $bar->namakegiatan;
	}

	$str = 'SELECT kodeorg, luasareaproduktif, tahuntanam FROM ' . $dbname . '.setup_blok ' . "\r\n" . '    where kodeorg like \'' . $kdAfd . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$area[$bar->kodeorg] = $bar->luasareaproduktif;
	}

	if ($lha) {
		$str = 'SELECT count(*) as hk,kodeorg FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}
	else {
		$str = 'SELECT count(*) as hk,kodeorg FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}

	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$hksd += $bar->kodeorg;
	}

	if ($lha) {
		$str = 'SELECT sum(hasilkerjakg)as hasil,kodeorg FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . substr($tgl1_, 0, 6) . '01\' and \'' . $tgl1_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}
	else {
		$str = 'SELECT sum(hasilkerjakg)as hasil,kodeorg FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}

	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$kgsd += $bar->kodeorg;
	}

	$areatotal = 0;
	$hktotal = 0;
	$hksdtotal = 0;
	$upahtotal = 0;
	$premitotal = 0;
	$penaltytotal = 0;
	$jumlahupahtotal = 0;
	$kgtotal = 0;
	$kgsdtotal = 0;

	if ($lha) {
		$str = 'SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal = \'' . $tgl1_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}
	else {
		$str = 'SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
	}

	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jumlahupah = ($bar->upah + $bar->premi) - $bar->penalty;
		@$rppersat = $jumlahupah / $bar->hasil;
		@$kgperhk = $bar->hasil / $bar->hk;
		$areatotal += $area[$bar->kodeorg];
		$hktotal += $bar->hk;
		$hksdtotal += $hksd[$bar->kodeorg];
		$upahtotal += $bar->upah;
		$premitotal += $bar->premi;
		$penaltytotal += $bar->penalty;
		$jumlahupahtotal += $jumlahupah;
		$kgtotal += $bar->hasil;
		$kgsdtotal += $kgsd[$bar->kodeorg];
		$stream .= '<tr class=rowcontent>' . "\r\n" . '            <td align=left>' . $kodepanen . '</td>' . "\r\n" . '            <td align=left>' . $kegiatanx[$kodepanen] . '</td>' . "\r\n" . '            <td align=left>KG</td>' . "\r\n" . '            <td align=left>' . $bar->kodeorg . '</td>' . "\r\n" . '            <td align=right>' . $area[$bar->kodeorg] . '</td>' . "\r\n" . '            <td align=center>' . $bar->tahuntanam . '</td>';

		if ($lha) {
			$stream .= '<td align=right>' . $bar->hk . '</td>' . "\r\n" . '            <td align=right>' . $hksd[$bar->kodeorg] . '</td>';
		}
		else {
			$stream .= '<td align=right colspan=2>' . $bar->hk . '</td>';
		}

		$stream .= '<td align=right>' . number_format($bar->upah, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->premi, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->penalty, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($jumlahupah, 0) . '</td>';

		if ($lha) {
			$stream .= '<td align=right>' . number_format($bar->hasil, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($kgsd[$bar->kodeorg], 0) . '</td>';
		}
		else {
			$stream .= '<td align=right colspan=2>' . number_format($bar->hasil, 0) . '</td>';
		}

		$stream .= '<td align=right>' . number_format($rppersat, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($kgperhk, 2) . '</td>' . "\r\n" . '        </tr>    ' . "\r\n" . '        ';
	}

	$stream .= '<tr class=title>' . "\r\n" . '            <td align=left colspan=4>Total</td>' . "\r\n" . '            <td align=right>' . $areatotal . '</td>' . "\r\n" . '            <td align=center></td>';

	if ($lha) {
		$stream .= '<td align=right>' . $hktotal . '</td>' . "\r\n" . '            <td align=right>' . $hksdtotal . '</td>';
	}
	else {
		$stream .= '<td align=right colspan=2>' . $hktotal . '</td>';
	}

	$stream .= '<td align=right>' . number_format($upahtotal, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($premitotal, 0) . '</td>';
	$stream .= '<td align=right>' . number_format($penaltytotal, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($jumlahupahtotal, 0) . '</td>';

	if ($lha) {
		$stream .= '<td align=right>' . number_format($kgtotal, 0) . '</td>' . "\r\n" . '            <td align=right>' . number_format($kgsdtotal, 0) . '</td>';
	}
	else {
		$stream .= '<td align=right colspan=2>' . number_format($kgtotal, 0) . '</td>';
	}

	$stream .= '<td align=right></td>' . "\r\n" . '            <td align=right></td>' . "\r\n" . '        </tr>    ' . "\r\n" . '        ';
	$stream .= '</tbody></table>';
	$stream .= '<br>' . strtoupper($_SESSION['lang']['biayaumum']);
	$stream .= '<table cellspacing=\'1\' border=\'0\' class=\'sortable\'>';
	$stream .= '<thead>' . "\r\n\t" . '<tr class=rowheader>' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['nomor'] . '</td>    ' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['jenis'] . '</td>    ' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['jumlahhk'] . '</td>    ' . "\r\n" . '        <td align=center>' . $_SESSION['lang']['upahkerja'] . '</td>    ' . "\r\n" . '        </tr></thead><tbody>';
	$str = 'SELECT id FROM ' . $dbname . '.sdm_ho_component where plus=1';
	$komponen = '(';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$komponen .= '\'' . $bar->id . '\',';
	}

	$komponen = substr($komponen, 0, -1);
	$komponen .= ')';

	if ($lha) {
		$str = 'SELECT tanggal,nikmandor FROM ' . $dbname . '.kebun_aktifitas a' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor=c.karyawanid' . "\r\n" . '    where a.tanggal = \'' . $tgl1_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and c.namakaryawan is not NULL' . "\r\n" . '    union select tanggal,nikmandor1 FROM ' . $dbname . '.kebun_aktifitas a ' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor1=c.karyawanid' . "\r\n" . '    where a.tanggal = \'' . $tgl1_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and c.namakaryawan is not NULL';
	}
	else {
		$str = 'SELECT tanggal,nikmandor FROM ' . $dbname . '.kebun_aktifitas a' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor=c.karyawanid' . "\r\n" . '    where a.tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and c.namakaryawan is not NULL' . "\r\n" . '    union select tanggal,nikmandor1 FROM ' . $dbname . '.kebun_aktifitas a ' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor1=c.karyawanid' . "\r\n" . '    where a.tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and c.namakaryawan is not NULL';
	}

	$awaskar = '(';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$awaskar .= '\'' . $bar->nikmandor . '\',';
		$qwe = $bar->nikmandor . $bar->tanggal;
		$awas[$qwe] = $qwe;
	}

	$awaskar = substr($awaskar, 0, -1);
	$awaskar .= ')';

	if (mysql_num_rows($res) == 0) {
		$awaskar = '(\'\')';
	}

	$awashk = count($awas);
	$awasupah = 0;
	$str = 'SELECT * FROM ' . $dbname . '.sdm_5gajipokok where karyawanid in ' . $awaskar . ' and idkomponen in ' . $komponen . ' and tahun = \'' . substr($tgl1, 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$awasupah += $bar->jumlah;
	}

	if ($lha) {
		@$awasupah = $awasupah / 30;
	}
	else {
		@$awasupah = ($jumlahhari * $awasupah) / 30;
	}

	if ($lha) {
		$str = 'SELECT tanggal,nikmandor FROM ' . $dbname . '.kebun_aktifitas a' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor=c.karyawanid' . "\r\n" . '    where a.tanggal = \'' . $tgl1_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and nikmandor not in ' . $awaskar . ' and c.namakaryawan is not NULL' . "\r\n" . '    union select tanggal,keranimuat FROM ' . $dbname . '.kebun_aktifitas a ' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.keranimuat=c.karyawanid' . "\r\n" . '    where a.tanggal = \'' . $tgl1_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and keranimuat not in ' . $awaskar . ' and c.namakaryawan is not NULL';
	}
	else {
		$str = 'SELECT tanggal,nikmandor FROM ' . $dbname . '.kebun_aktifitas a' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.nikmandor=c.karyawanid' . "\r\n" . '    where a.tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and nikmandor not in ' . $awaskar . ' and c.namakaryawan is not NULL' . "\r\n" . '    union select tanggal,keranimuat FROM ' . $dbname . '.kebun_aktifitas a ' . "\r\n" . '    left join ' . $dbname . '.kebun_prestasi b on a.notransaksi=b.notransaksi' . "\r\n" . '    left join ' . $dbname . '.datakaryawan c on a.keranimuat=c.karyawanid' . "\r\n" . '    where a.tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and b.kodeorg like \'' . $kdAfd . '%\' and keranimuat not in ' . $awaskar . ' and c.namakaryawan is not NULL';
	}

	$admkar = '(';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$admkar .= '\'' . $bar->nikmandor . '\',';
		$qwe = $bar->nikmandor . $bar->tanggal;
		$adm[$qwe] = $qwe;
	}

	$admkar = substr($admkar, 0, -1);
	$admkar .= ')';

	if (mysql_num_rows($res) == 0) {
		$admkar = '(\'\')';
	}

	$admhk = count($adm);
	$admupah = 0;
	$str = 'SELECT * FROM ' . $dbname . '.sdm_5gajipokok where karyawanid in ' . $admkar . ' and karyawanid not in ' . $awaskar . ' and idkomponen in ' . $komponen . ' and tahun = \'' . substr($tgl1, 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$admupah += $bar->jumlah;
	}

	if ($lha) {
		@$admupah = $admupah / 30;
	}
	else {
		@$admupah = ($jumlahhari * $admupah) / 30;
	}

	if ($lha) {
		$str = 'SELECT karyawanid FROM ' . $dbname . '.sdm_absensidt' . "\r\n" . '        where kodeorg like \'' . $kdAfd . '%\' and tanggal = \'' . $tgl1_ . '\' and absensi = \'H\' and karyawanid not in ' . $admkar . ' and karyawanid not in ' . $awaskar . '';
	}
	else {
		$str = 'SELECT karyawanid FROM ' . $dbname . '.sdm_absensidt' . "\r\n" . '        where kodeorg like \'' . $kdAfd . '%\' and tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and absensi = \'H\' and karyawanid not in ' . $admkar . ' and karyawanid not in ' . $awaskar . '';
	}

	$res = mysql_query($str);
	$umumhk = 0;
	$umumkar = 'karyawanid in (';

	while ($bar = mysql_fetch_object($res)) {
		$umumkar .= '\'' . $bar->karyawanid . '\',';
		$umumhk += 1;
	}

	$umumkar = substr($umumkar, 0, -1);
	$umumkar .= ')';

	if (mysql_num_rows($res) == 0) {
		$umumkar = 'karyawanid = \'\'';
	}

	$umumupah = 0;
	$str = 'SELECT * FROM ' . $dbname . '.sdm_5gajipokok where ' . $umumkar . ' and idkomponen in ' . $komponen . ' and tahun = \'' . substr($tgl1, 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$umumupah += $bar->jumlah;
	}

	if ($lha) {
		@$umumupah = $umumupah / 30;
	}
	else {
		@$umumupah = ($jumlahhari * $umumupah) / 30;
	}

	$total = $awasupah + $admupah + $umumupah;
	$grandtotal = $TOTAL['totalbiaya'] + $jumlahupahtotal + $total;
	@$cost = @$grandtotal / $luas;
	$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>1</td>    ' . "\r\n" . '        <td align=left>Pengawasan (Mandor)</td>    ' . "\r\n" . '        <td align=right>' . number_format($awashk, 2) . '</td>    ' . "\r\n" . '        <td align=right>' . number_format($awasupah, 0) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>2</td>    ' . "\r\n" . '        <td align=left>Administrasi (Kerani)</td>    ' . "\r\n" . '        <td align=right>' . number_format($admhk, 2) . '</td>    ' . "\r\n" . '        <td align=right>' . number_format($admupah, 0) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=right>3</td>    ' . "\r\n" . '        <td align=left>Umum (Kantor)</td>    ' . "\r\n" . '        <td align=right>' . number_format($umumhk, 2) . '</td>    ' . "\r\n" . '        <td align=right>' . number_format($umumupah, 0) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=center colspan=3>Total</td>    ' . "\r\n" . '        <td align=right>' . number_format($total, 0) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '<tr class=rowcontent>' . "\r\n" . '        <td align=center colspan=3>Grand Total Biaya (Rp.)</td>    ' . "\r\n" . '        <td align=right>' . number_format($grandtotal, 0) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '<tr class=title>' . "\r\n" . '        <td align=center colspan=3>Total Cost (Rp./Ha)</td>    ' . "\r\n" . '        <td align=right>' . number_format($cost, 2) . '</td>    ' . "\r\n" . '        </tr>';
	$stream .= '</tbody></table>';

	if ($proses == 'preview') {
		echo $stream;
	}

	if ($proses == 'excel') {
		$stream .= '</table><br>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
		$dte = date('YmdHms');
		$nop_ = 'LHA_' . $kdAfd . '_' . $tgl1_ . '_' . $tgl2_ . '_' . $dte;
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}

	if ($proses == 'pdf') {
		class PDF extends FPDF
		{
			public function Header()
			{
				global $kdAfd;
				global $tgl1_;
				global $tgl2_;
				global $dbname;
				global $lkojur;
				global $lpeker;
				global $llain;
				global $lha;
				global $luas;
				$width = $this->w - $this->lMargin - $this->rMargin;
				$height = 15;

				if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
					$path = 'images/SSP_logo.jpg';
				}
				else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
					$path = 'images/MI_logo.jpg';
				}
				else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
					$path = 'images/HS_logo.jpg';
				}
				else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
					$path = 'images/BM_logo.jpg';
				}

				$this->Image($path, $this->lMargin - 25, $this->tMargin - 25, 40);
				$this->SetFont('Arial', 'B', 9);
				$this->SetFillColor(255, 255, 255);
				$this->SetX(50);
				$this->Cell($width - 50, $height, 'ANTHESIS ERP', 0, 1, 'L');
				$this->Line($this->lMargin + 25, $this->tMargin + ($height * 1), $this->lMargin + $width, $this->tMargin + ($height * 1));
				$this->Ln();
				$this->SetFont('Arial', 'U', 10);

				if ($_SESSION['language'] == 'EN') {
					$title = 'DAILY DIVISION REPORT';
				}
				else {
					$title = 'LAPORAN HARIAN AFDELING';
				}

				$this->Cell($width, $height, $title, 0, 1, 'C');
				$this->Ln();
				$this->SetFont('Arial', '', 8);
				$this->Cell(((7 / 100) * $width) - 5, $height, $_SESSION['lang']['kebun'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((43 / 100) * $width, $height, substr($kdAfd, 0, 4), '', 0, 'L');
				$this->Cell((20 / 100) * $width, $height, '', '', 0, 'L');
				$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['diperiksa'], '', 0, 'C');
				$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['dibuat'], '', 0, 'C');
				$this->Ln();
				$this->Cell(((7 / 100) * $width) - 5, $height, $_SESSION['lang']['afdeling'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((43 / 100) * $width, $height, $kdAfd . ' (' . number_format($luas, 2) . ' Ha)', '', 0, 'L');
				$this->Ln();
				$this->Cell(((7 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
				$this->Cell(5, $height, ':', '', 0, 'L');
				$this->Cell((43 / 100) * $width, $height, tanggalnormal($tgl1_) . ' ' . tanggalnormal($tgl2_), '', 0, 'L');
				$this->Ln();
				$this->Cell((70 / 100) * $width, $height, '', '', 0, 'L');
				$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['askep'], '', 0, 'C');
				$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['asisten'], '', 0, 'C');
				$this->Ln();
			}

			public function Footer()
			{
				$this->SetY(-15);
				$this->SetFont('Arial', 'I', 8);
				$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C', 1);
				$this->SetX(-520);
				$this->Cell(500, 10, 'Printed By' . ' : ' . $_SESSION['empl']['name'] . ', ' . date('d-m-Y H:i:s'), '', 1, 'R', 1);
			}
		}

		$lkojur = 4;
		$lpeker = 12;
		$llain = 4;
		$pdf = new PDF('L', 'pt', 'A4');
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
		$height = 15;
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetFillColor(220, 220, 220);
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['kode'], TRL, 0, 'C', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], TRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Cell((($lkojur + $llain) / 100) * $width, $height, $_SESSION['lang']['kodeblok'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['tahun'], TRL, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, 'HK KHT/KHL', 1, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, 'HK KBL', 1, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, $_SESSION['lang']['upah'] . '(Rp.)', 1, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, $_SESSION['lang']['hasilkerjajumlah'], 1, 0, 'C', 1);
		$pdf->Cell((((3 * $llain) + $lkojur) / 100) * $width, $height, $_SESSION['lang']['pemakaianBarang'], 1, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, $_SESSION['lang']['material'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'T.' . $_SESSION['lang']['biaya'], TRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'Rp./' . $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'HK/' . $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Ln();
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['jurnal'], BRL, 0, 'C', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['luas'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', RLB, 0, 'C', 1);
		}

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', RLB, 0, 'C', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, 'Rp./Unit', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', RLB, 0, 'C', 1);
		}

		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', 1, 0, 'C', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, 'Rp./Unit', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Ln();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 5);

		if (!empty($master['blok'])) {
			foreach ($master['blok'] as $kunc => $va) {
				$qwe = 1;
				$pdf->Cell(($lkojur / 100) * $width, $height, $master['kegiatan'][$kunc], 1, 0, 'L', 1);
				$pdf->Cell(($lpeker / 100) * $width, $height, $kegiatanx[$master['kegiatan'][$kunc]], 1, 0, 'L', 1);
				$pdf->Cell(($llain / 100) * $width, $height, $master['satuankegiatan'][$kunc], 1, 0, 'L', 1);
				$pdf->SetFont('Arial', '', 4);
				$pdf->Cell(($lkojur / 100) * $width, $height, $master['blok'][$kunc], 1, 0, 'L', 1);
				$pdf->SetFont('Arial', '', 5);
				$pdf->Cell(($llain / 100) * $width, $height, number_format($master['luas'][$kunc], 2), 1, 0, 'R', 1);
				$pdf->Cell(($llain / 100) * $width, $height, $master['thntnm'][$kunc], 1, 0, 'C', 1);

				if ($lha) {
					$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hkkhl'][$kunc], 2), 1, 0, 'R', 1);
					$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hkkhlsbi'][$kunc], 2), 1, 0, 'R', 1);
				}
				else {
					$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($master['hkkhl'][$kunc], 2), 1, 0, 'R', 1);
				}

				if ($lha) {
					$pdf->Cell(($llain / 100) * $width, $height, $master['hkkbl'][$kunc], 1, 0, 'R', 1);
					$pdf->Cell(($llain / 100) * $width, $height, $master['hkkblsbi'][$kunc], 1, 0, 'R', 1);
				}
				else {
					$pdf->Cell((($llain * 2) / 100) * $width, $height, $master['hkkbl'][$kunc], 1, 0, 'R', 1);
				}

				$pdf->Cell(($llain / 100) * $width, $height, number_format($master['rpperhk'][$kunc], 0), 1, 0, 'R', 1);
				$pdf->Cell(($llain / 100) * $width, $height, number_format($master['totalupah'][$kunc], 0), 1, 0, 'R', 1);

				if ($lha) {
					$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hasilbi'][$kunc], 2), 1, 0, 'R', 1);
					$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hasilsbi'][$kunc], 2), 1, 0, 'R', 1);
				}
				else {
					$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($master['hasilbi'][$kunc], 2), 1, 0, 'R', 1);
				}

				if (!empty($master['barangsbi'])) {
					foreach ($master['barangsbi'][$kunc] as $dd => $ee) {
						if ($qwe == 0) {
							$pdf->Cell((((2 * $lkojur) + $lpeker + (11 * $llain)) / 100) * $width, $height, '', 0, 0, 'L', 0);
						}

						$pdf->Cell(($lkojur / 100) * $width, $height, $master['barangsbi'][$kunc][$dd], 1, 0, 'L', 1);
						$pdf->Cell(($llain / 100) * $width, $height, $master['satuanbarangsbi'][$kunc][$dd], 1, 0, 'L', 1);

						if ($lha) {
							$pdf->Cell(($llain / 100) * $width, $height, number_format($master['qtybi'][$kunc][$dd], 2), 1, 0, 'R', 1);
							$pdf->Cell(($llain / 100) * $width, $height, number_format($master['qtysbi'][$kunc][$dd], 2), 1, 0, 'R', 1);
						}
						else {
							$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($master['qtybi'][$kunc][$dd], 2), 1, 0, 'R', 1);
						}

						$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hargabarangbi'][$kunc][$dd], 0), 1, 0, 'R', 1);
						$pdf->Cell(($llain / 100) * $width, $height, number_format($master['bybarangbi'][$kunc][$dd], 0), 1, 0, 'R', 1);

						if ($qwe == 1) {
							$qwe = 0;
							$pdf->Cell(($llain / 100) * $width, $height, number_format($master['totbiaya'][$kunc], 0), 1, 0, 'R', 1);
							$pdf->Cell(($llain / 100) * $width, $height, number_format($master['rppersatuan'][$kunc], 0), 1, 0, 'R', 1);
							$pdf->Cell(($llain / 100) * $width, $height, number_format($master['hkpersatuan'][$kunc], 2), 1, 0, 'R', 1);
						}

						$pdf->Ln();
					}
				}
			}
		}

		$pdf->Cell((($lkojur + $lpeker + $llain) / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
		$pdf->Cell(($lkojur / 100) * $width, $height, '', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['luas'], 2), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['hkkhlbi'], 2), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['hkkhlsbi'], 2), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($TOTAL['hkkhlbi'], 2), 1, 0, 'R', 1);
		}

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['hkkblbi']), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['hkkblsbi']), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($TOTAL['hkkblbi']), 1, 0, 'R', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['totalupah']), 1, 0, 'R', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($TOTAL['hasilbi'], 2), 1, 0, 'R', 1);
		}

		$pdf->Cell((((4 * $llain) + $lkojur) / 100) * $width, $height, '', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['totalbiayabarang']), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($TOTAL['totalbiaya']), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Ln();
		$ar = $pdf->GetY();

		if (400 < $ar) {
			$pdf->AddPage();
		}
		else {
			$pdf->Ln();
		}

		$pdf->SetFont('Arial', '', 7);
		$pdf->SetFillColor(220, 220, 220);
		$pdf->Cell(($lkojur / 100) * $width, $height, 'Kode', TRL, 0, 'C', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, $_SESSION['lang']['pekerjaan'], TRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Cell((($lkojur + $llain) / 100) * $width, $height, $_SESSION['lang']['kodeblok'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['thntnm'], TRL, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, 'HK', 1, 0, 'C', 1);
		$pdf->Cell(((4 * $llain) / 100) * $width, $height, $_SESSION['lang']['biaya'], 1, 0, 'C', 1);
		$pdf->Cell(((2 * $llain) / 100) * $width, $height, $_SESSION['lang']['hasilkerjajumlah'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'Rp./' . $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'HK/' . $_SESSION['lang']['satuan'], TRL, 0, 'C', 1);
		$pdf->Ln();
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['jurnal'], BRL, 0, 'C', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['blok'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['luas'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', RLB, 0, 'C', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['upah'], 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'Premi', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, 'Penalty', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['hi'], 1, 0, 'C', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['sdhi'], 1, 0, 'C', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, '', RLB, 0, 'C', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', BRL, 0, 'C', 1);
		$pdf->Ln();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 5);
		$areatotal = 0;
		$hktotal = 0;
		$hksdtotal = 0;
		$upahtotal = 0;
		$premitotal = 0;
		$penaltytotal = 0;
		$jumlahupahtotal = 0;
		$kgtotal = 0;
		$kgsdtotal = 0;

		if ($lha) {
			$str = 'SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal = \'' . $tgl1_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
		}
		else {
			$str = 'SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ' . $dbname . '.kebun_prestasi_vw ' . "\r\n" . '    where tanggal between \'' . $tgl1_ . '\' and \'' . $tgl2_ . '\' and kodeorg like \'' . $kdAfd . '%\' group by kodeorg';
		}

		$res = mysql_query($str);

		while ($bar = mysql_fetch_object($res)) {
			$jumlahupah = ($bar->upah + $bar->premi) - $bar->penalty;
			@$rppersat = $jumlahupah / $bar->hasil;
			@$kgperhk = $bar->hasil / $bar->hk;
			$areatotal += $area[$bar->kodeorg];
			$hktotal += $bar->hk;
			$hksdtotal += $hksd[$bar->kodeorg];
			$upahtotal += $bar->upah;
			$premitotal += $bar->premi;
			$penaltytotal += $bar->penalty;
			$jumlahupahtotal += $jumlahupah;
			$kgtotal += $bar->hasil;
			$kgsdtotal += $kgsd[$bar->kodeorg];
			$pdf->Cell(($lkojur / 100) * $width, $height, $kodepanen, 1, 0, 'L', 1);
			$pdf->Cell(($lpeker / 100) * $width, $height, $kegiatanx[$kodepanen], 1, 0, 'L', 1);
			$pdf->Cell(($llain / 100) * $width, $height, 'KG', 1, 0, 'L', 1);
			$pdf->SetFont('Arial', '', 4);
			$pdf->Cell(($lkojur / 100) * $width, $height, $bar->kodeorg, 1, 0, 'L', 1);
			$pdf->SetFont('Arial', '', 5);
			$pdf->Cell(($llain / 100) * $width, $height, $area[$bar->kodeorg], 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $bar->tahuntanam, 1, 0, 'C', 1);

			if ($lha) {
				$pdf->Cell(($llain / 100) * $width, $height, $bar->hk, 1, 0, 'R', 1);
				$pdf->Cell(($llain / 100) * $width, $height, $hksd[$bar->kodeorg], 1, 0, 'R', 1);
			}
			else {
				$pdf->Cell((($llain * 2) / 100) * $width, $height, $bar->hk, 1, 0, 'R', 1);
			}

			$pdf->Cell(($llain / 100) * $width, $height, number_format($bar->upah, 0), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($bar->premi, 0), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($bar->penalty, 0), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($jumlahupah, 0), 1, 0, 'R', 1);

			if ($lha) {
				$pdf->Cell(($llain / 100) * $width, $height, number_format($bar->hasil, 0), 1, 0, 'R', 1);
				$pdf->Cell(($llain / 100) * $width, $height, number_format($kgsd[$bar->kodeorg], 0), 1, 0, 'R', 1);
			}
			else {
				$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($bar->hasil, 0), 1, 0, 'R', 1);
			}

			$pdf->Cell(($llain / 100) * $width, $height, number_format($rppersat, 0), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($kgperhk, 2), 1, 0, 'R', 1);
			$pdf->Ln();
		}

		$pdf->Cell((($lkojur + $lpeker + $llain) / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $areatotal, 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, '', 1, 0, 'C', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, $hktotal, 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, $hksdtotal, 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, $hktotal, 1, 0, 'R', 1);
		}

		$pdf->Cell(($llain / 100) * $width, $height, number_format($upahtotal, 0), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($premitotal, 0), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($penaltytotal, 0), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($jumlahupahtotal, 0), 1, 0, 'R', 1);

		if ($lha) {
			$pdf->Cell(($llain / 100) * $width, $height, number_format($kgtotal, 0), 1, 0, 'R', 1);
			$pdf->Cell(($llain / 100) * $width, $height, number_format($kgsdtotal, 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell((($llain * 2) / 100) * $width, $height, number_format($kgtotal, 0), 1, 0, 'R', 1);
		}

		$pdf->Cell((($llain + $llain) / 100) * $width, $height, '', 1, 0, 'R', 1);
		$pdf->Ln();
		$ar = $pdf->GetY();

		if (400 < $ar) {
			$pdf->AddPage();
		}
		else {
			$pdf->Ln();
		}

		$pdf->Cell((($lkojur + $lpeker + $llain + $llain) / 100) * $width, $height, strtoupper($_SESSION['lang']['biayaumum']), 0, 0, 'L', 1);
		$pdf->SetFillColor(220, 220, 220);
		$pdf->Ln();
		$pdf->Cell(($lkojur / 100) * $width, $height, $_SESSION['lang']['nomor'], 1, 0, 'L', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, $_SESSION['lang']['jenis'], 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['jumlahhk'], 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, $_SESSION['lang']['upahkerja'], 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(($lkojur / 100) * $width, $height, '1', 1, 0, 'R', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, 'Pengawasan Mandor)', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($awashk, 2), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($awasupah, 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(($lkojur / 100) * $width, $height, '2', 1, 0, 'R', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, 'Administrasi (Kerani)', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($admhk, 2), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($admupah, 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell(($lkojur / 100) * $width, $height, '3', 1, 0, 'R', 1);
		$pdf->Cell(($lpeker / 100) * $width, $height, 'Umum (Kantor)', 1, 0, 'L', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($umumhk, 2), 1, 0, 'R', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($umumupah, 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell((($lkojur + $lpeker + $llain) / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($total, 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->Cell((($lkojur + $lpeker + $llain) / 100) * $width, $height, 'Grand Total (Rp.)', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($grandtotal, 0), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->SetFillColor(220, 220, 220);
		$pdf->Cell((($lkojur + $lpeker + $llain) / 100) * $width, $height, 'Total Cost (Rp./Ha)', 1, 0, 'C', 1);
		$pdf->Cell(($llain / 100) * $width, $height, number_format($cost, 2), 1, 0, 'R', 1);
		$pdf->Ln();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Output();
	}
}

?>
