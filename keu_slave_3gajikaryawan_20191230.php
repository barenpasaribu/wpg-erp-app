<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . ".sdm_5periodegaji\r\n    where kodeorg='" . $_SESSION['empl']['lokasitugas'] . "'\r\n    and periode='" . $param['periode'] . "'";
$tgmulai = '';
$tgsampai = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$tgsampai = $bar->tanggalsampai;
	$tgmulai = $bar->tanggalmulai;
}
if ('' == $tgmulai || '' == $tgsampai) {
	exit('Error: Accounting period is not registered');
}



$str = 'select sum(jumlah) as jumlah,idkomponen,karyawanid from ' . $dbname . ".sdm_gajidetail_vw\r\n       where kodeorg like '" . $_SESSION['empl']['lokasitugas'] . "%'\r\n       and idkomponen in(20) and periodegaji='" . $param['periode'] . "' and karyawanid not in (0999999999,0888888888) group by idkomponen,karyawanid";
$resx = mysql_query($str);
$potx = [];
while ($barx = mysql_fetch_object($resx)) {
	$potx[$barx->karyawanid] = $barx->jumlah;
}
$str = 'select jumlah,idkomponen,karyawanid from ' . $dbname . ".sdm_gajidetail_vw\r\n       where kodeorg like '" . $_SESSION['empl']['lokasitugas'] . "%'\r\n       and plus in (0,1) and periodegaji='" . $param['periode'] . "' and karyawanid not in (0999999999,0888888888)";
$res = mysql_query($str);
$gaji = [];
while ($bar = mysql_fetch_object($res)) {
	if (1 == $bar->idkomponen) {
		$gaji[$bar->karyawanid][$bar->idkomponen] = $bar->jumlah;
	} else {
		$gaji[$bar->karyawanid][$bar->idkomponen] = $bar->jumlah;
	}
}
$str = 'select subbagian,karyawanid,namakaryawan from ' . $dbname . ".datakaryawan where lokasitugas='" . $_SESSION['empl']['lokasitugas'] . "' or kodeorganisasi='" . $_SESSION['empl']['induklokasitugas'] . "' and karyawanid not in (0999999999,0888888888)";
$res = mysql_query($str);
$subunit = [];
while ($bar = mysql_fetch_object($res)) {
	$subunit[$bar->karyawanid] = $bar->subbagian;
	$namakaryawan[$bar->karyawanid] = $bar->namakaryawan;
}
$str = 'select distinct kodeorganisasi,tipe from ' . $dbname . ".organisasi\r\n       where kodeorganisasi like '" . $_SESSION['empl']['lokasitugas'] . "%'";
$res = mysql_query($str);
$tipe = [];
while ($bar = mysql_fetch_object($res)) {
	$tipe[$bar->kodeorganisasi] = $bar->tipe;
}
$GJ = $gaji;
$str = 'select karyawanid from ' . $dbname . ".kebun_kehadiran_vw\r\n          where tanggal>='" . $tgmulai . "' and tanggal <='" . $tgsampai . "'\r\n          and unit='" . $_SESSION['empl']['lokasitugas'] . "' and jurnal=1 and karyawanid not in (0999999999,0888888888)";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
}
$str1 = 'select karyawanid from ' . $dbname . ".datakaryawan where\r\n           lokasitugas='" . $_SESSION['empl']['lokasitugas'] . "'\r\n           and tanggalmasuk>'" . $tgsampai . "' and karyawanid not in (0999999999,0888888888)";
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
}
$str = 'select karyawanid from ' . $dbname . ".kebun_prestasi_vw\r\n          where tanggal>='" . $tgmulai . "' and tanggal <='" . $tgsampai . "'\r\n          and unit='" . $_SESSION['empl']['lokasitugas'] . "' and jurnal=1 and karyawanid not in (0999999999,0888888888)";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
}
$str = 'select vhc,karyawanid from ' . $dbname . '.vhc_5operator';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$ken[$bar->karyawanid] = $bar->vhc;
}
$str = 'select id,name from ' . $dbname . '.sdm_ho_component';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$komponen[$bar->id] = $bar->id;
	$namakomponen[$bar->id] = $bar->name;
}
$str = 'select sum(umr) as umr, sum(insentif) as insentif,karyawanid from ' . $dbname . ".kebun_kehadiran_vw\r\n          where tanggal>='" . $tgmulai . "' and tanggal <='" . $tgsampai . "'\r\n          and unit='" . $_SESSION['empl']['lokasitugas'] . "' and jurnal=1 and karyawanid not in (0999999999,0888888888) group by karyawanid";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$potongan[$bar->karyawanid][1] += $bar->umr;
}
$str = "select sum(upahkerja) as umr, sum(upahpremi) as insentif,sum(rupiahpenalty) as penalty,\r\n          karyawanid from " . $dbname . ".kebun_prestasi_vw\r\n          where tanggal>='" . $tgmulai . "' and tanggal <='" . $tgsampai . "'\r\n          and unit='" . $_SESSION['empl']['lokasitugas'] . "' and jurnal=1 and karyawanid not in (0999999999,0888888888) group by karyawanid";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$potongan[$bar->karyawanid][1] += $bar->umr - $bar->penalty;
}
$gajiblmalokasi = $GJ;
foreach ($GJ as $key => $row) {
	$gajiblmalokasi[$key][1] -= $potongan[$key][1];
}
$kekurangan = 0;
foreach ($gajiblmalokasi as $key) {
	foreach ($key as $row => $cell) {
		if ($cell < 0) {
			$kekurangan += $cell;
		}
	}
}
if (empty($gaji)) {
	exit('Error: No Salary data found');
}

// ------------------------------------ //
$premi = 0;
$penalty = 0 ;
$penaltykehadiran = 0;
$query5 = 'select a.karyawanid,sum(a.insentif) as premi from '.$dbname.".kebun_kehadiran_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.tanggal>='".$tgmulai."' and a.tanggal<='".$tgsampai."' and sistemgaji='Harian'";
$premRes = fetchData($query5);
foreach ($premRes as $idx => $val) {
	if (0 < $val['premi']) {
		$premi = $val['premi'];
	}
}

$query6 = "select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty from ".$dbname.".kebun_prestasi_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.tanggal>='".$tgmulai."' and a.tanggal<='".$tgsampai."' and sistemgaji='Harian'";

$premRes1 = fetchData($query6);
foreach ($premRes1 as $idx => $val) {
	if (0 < $val['premi']) {
		$premi += $val['premi'];
	}

	if (0 < $val['penalty']) {
		$penalty += $val['penalty'];
	}	


}

$query7 = "select a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty from ".$dbname.".vhc_runhk_vw a left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."' and a.tanggal>='".$tgmulai."' and a.tanggal<='".$tgsampai."' and sistemgaji='Harian' group by a.idkaryawan";
$premRes2 = fetchData($query7);
foreach ($premRes2 as $idx => $val) {
	if (0 < $val['premi']) {
		$premi += $val['premi'];
	}

	if (0 < $val['penalty']) {
		$penalty  += $val['penalty'];
	}
}

$query8 = "select sum(a.premiinput) as premi,a.karyawanid,a.tanggal from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' and a.tanggal like  '%".$param['periode']."%' and b.sistemgaji='Harian' and a.posting=1 group by a.karyawanid";
$premRes2 = fetchData($query8);
foreach ($premRes2 as $idx => $val) {
	if (0 < $val['premi']) {
		$premi += $val['premi'];
	}
}

$stkh = 'select a.karyawanid,sum(a.premi+a.insentif) as premi from '.$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian' and a.tanggal>='".$tgmulai."' and a.tanggal<='".$tgsampai."' group by a.karyawanid";
$reskh = mysql_query($stkh);
while ($barky = mysql_fetch_object($reskh)) {
	$premi += $barky->premi;
}


$stkh1 = 'select a.karyawanid,a.rupiahpremi  from '.$dbname.".kebun_premipanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and a.periode like  '%".$param['periode']."%' and sistemgaji='Harian' group by a.karyawanid";
$reskh1 = mysql_query($stkh1);
while ($barky = mysql_fetch_object($reskh1)) {
	if (isset($premi[$barky->karyawanid])) {
		$premi += $barky->rupiahpremi;
	}
}


$stkh = 'select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran from '.$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian' and a.tanggal>='".$tgmulai."' and a.tanggal<='".$tgsampai."' group by a.karyawanid";
$reskh = mysql_query($stkh);
while ($barkh = mysql_fetch_object($reskh)) {
	if (0 < $barkh->penaltykehadiran) {
		$penaltykehadiran =+ $barkh->penaltykehadiran;
	}
}


// ------------------------------------------- //

$query1 = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,jms,statuspajak,npwp,kodejabatan,tipekaryawan,kodegolongan,idmedical', "tipekaryawan in(1,2,3,4,6) and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar>='".$tgmulai."' or tanggalkeluar is NULL) and alokasi=0 and sistemgaji='Harian' and ( tanggalmasuk<='".$tgsampai."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null) and karyawanid not in (0999999999,0888888888) order by karyawanid");
$absRes = fetchData($query1);
foreach ($absRes as $row => $kar) {
	$nojms[$kar['karyawanid']] = trim($kar['jms']);
	$nobpjskes[$kar['karyawanid']] = trim($kar['idmedical']);
}


$str = 'select tipe from ' . $dbname . ".organisasi where kodeorganisasi='" . $_SESSION['empl']['lokasitugas'] . "'";
$res = mysql_query($str);
$tip = '';
while ($bar = mysql_fetch_object($res)) {
	$tip = $bar->tipe;
}

if ('KANWIL' == $tip || 'PABRIK' == $tip) {
	$str1 = 'select distinct a.*,b.namakaryawan,b.tipekaryawan,b.bagian from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid left join '.$dbname.'.kebun_kehadiran_vw c on b.karyawanid=c.karyawanid where a.tahun='.substr($tgsampai, 0, 4)." and b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and b.sistemgaji='Harian' or c.tanggal>='".$tgmulai."' and c.tanggal<='".$tgsampai."' order by b.karyawanid,idkomponen";
} else {
	$str1 = 'select distinct a.*,b.namakaryawan,b.tipekaryawan,b.bagian from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid left join '.$dbname.'.kebun_kehadiran_vw c on b.karyawanid=c.karyawanid where a.tahun='.substr($tgsampai, 0, 4)." and b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi=0 and b.sistemgaji='Harian' or c.tanggal>='".$tgmulai."' and c.tanggal<='".$tgsampai."' order by b.karyawanid,idkomponen";
}

$res1 = fetchData($str1);
$query6 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan'");
$query7 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan'");
$jmsRes = fetchData($query6);
$bpjspt = fetchData($query7);
$persenJms = $jmsRes[0]['value'] / 100;
$persenjhtpt = $bpjspt[0]['jhtpt'] / 100;
$persenjppt = $bpjspt[0]['jppt'] / 100;
$persenjkkpt = $bpjspt[0]['jkkpt'] / 100;
$persenjkmpt = $bpjspt[0]['jkmpt'] / 100;
$persenbpjspt = $bpjspt[0]['bpjspt'] / 100;
$persenjhtkar = $jmsRes[0]['jhtkar'] / 100;
$persenjpkar = $jmsRes[0]['jpkar'] / 100;
$persenbpjskar = $jmsRes[0]['bpjskar'] / 100;
$jabPersen = 0;
$jabMax = 0;
$str = 'select persen,max from '.$dbname.'.sdm_ho_pph21jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$jabPersen = $bar->persen / 100;
	$jabMax = $bar->max * 12;
	$jabMax2 = $bar->max;
}
$tjms = [];
$tipekaryawan = [];
foreach ($res1 as $idx => $val) {
	if ('1' == $val['tipekaryawan']) {
		$tipekaryawan[$val['karyawanid']] = 'BHL';
	} else {
		if ('2' == $val['tipekaryawan']) {
			$tipekaryawan[$val['karyawanid']] = 'ORGANIK';
		} else {
			if ('3' == $val['tipekaryawan']) {
				$tipekaryawan[$val['karyawanid']] = 'SKU';
			} else {
				if ('4' == $val['tipekaryawan']) {
					$tipekaryawan[$val['karyawanid']] = 'SKUP';
				} else {
					if ('6' == $val['tipekaryawan']) {
						$tipekaryawan[$val['karyawanid']] = 'PKWT';
					} else {
						$tipekaryawan[$val['karyawanid']] = 'ERROR';
					}
				}
			}
		}
	}

	if ((1 == $val['idkomponen'] || 2 == $val['idkomponen'] || 3 == $val['idkomponen'] || 4 == $val['idkomponen'] || 15 == $val['idkomponen'] || 17 == $val['idkomponen'] || 21 == $val['idkomponen'] || 22 == $val['idkomponen'] || 23 == $val['idkomponen'] || 29 == $val['idkomponen'] || 30 == $val['idkomponen'] || 32 == $val['idkomponen'] || 33 == $val['idkomponen'] || 35 == $val['idkomponen'] || 36 == $val['idkomponen'] || 37 == $val['idkomponen'] || 38 == $val['idkomponen'] || 39 == $val['idkomponen'] || 40 == $val['idkomponen'] || 41 == $val['idkomponen'] || 42 == $val['idkomponen'] || 43 == $val['idkomponen'] || 44 == $val['idkomponen'] || 45 == $val['idkomponen'] || 46 == $val['idkomponen'] || 47 == $val['idkomponen'] || 48 == $val['idkomponen'] || 49 == $val['idkomponen'] || 50 == $val['idkomponen'] || 51 == $val['idkomponen'] || 54 == $val['idkomponen'] || 58 == $val['idkomponen'] || 59 == $val['idkomponen'] || 60 == $val['idkomponen'] || 61 == $val['idkomponen'] || 62 == $val['idkomponen'] || 63 == $val['idkomponen'] || 65 == $val['idkomponen']) && '' != $nojms[$val['karyawanid']]) {
		$tjms[$val['karyawanid']] += $val['jumlah'];
	}
}

foreach ($tjms as $key => $nilai) {
	if ('BHL' == $tipekaryawan[$key] || 'ORGANIK' == $tipekaryawan[$key] || 'SKU' == $tipekaryawan[$key] || 'SKUP' == $tipekaryawan[$key] || 'PKWT' == $tipekaryawan[$key]) {
		if ('E' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
			$angkapersenlokres = fetchData($querypersenlokres);
			$jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
		}

		if ('H' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='HO'");
			$angkapersenlokres = fetchData($querypersenlokres);
			$jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
		}

		if ('M' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='M'");
			$angkapersenlokres = fetchData($querypersenlokres);
			$jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
		}
/*
		if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='RO'");
			$angkapersenlokres = fetchData($querypersenlokres);
			$jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
		}

		if ('RO_INFR' == $bagiankaryawan[$key]) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
			$angkapersenlokres = fetchData($querypersenlokres);
			$jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
		}
*/
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 6, 'jumlah' => round($nilai * $jkkptpersen), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 7, 'jumlah' => round($nilai * $persenjkmpt), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		$nilaibatasmax3 = 8000000;
		if ($nilaibatasmax3 < $nilai) {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $nilaibatasmax3 * $persenbpjspt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $nilai * $persenbpjspt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		}

		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => $nilai * $persenjhtkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		$nilaibatasmax = 8512400;
		if ($nilaibatasmax < $nilai) {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilaibatasmax * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilai * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		}

		if ('' != $nobpjskes[$key]) {
			$nilaibatasmax2 = 8000000;
			if ($nilaibatasmax2 < $nilai) {
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilaibatasmax2 * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			} else {
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilai * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			}
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periode'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		}
	}
}
$where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periode']."'";
$query3 = 'select a.nik as karyawanid,sum(jumlahpotongan) as potongan, a.tipepotongan as tipepotongan from '.$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid where b.tipekaryawan in(1,2,3,4,5,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tgmulai."' or b.tanggalkeluar is NULL) and b.alokasi in(0) and sistemgaji='Harian' and ".$where3.' group by a.nik';
$potRes = fetchData($query3);
$potongandendapanen = $penalty;
$potongandenda = 0;
foreach ($potRes as $idx => $row) {
	if ('26' == $row['tipepotongan']) {
		$potongandendapanen += $row['potongan'];
	}
	if ('27' == $row['tipepotongan'] || '64' == $row['tipepotongan']) {
		$potongandenda += $row['potongan'];
	}
}



$jkk = 0;
$jkm = 0;
$bpjspt = 0;
$jhtkarypersen = 0;
$jpkarypersen = 0;
$potonganbpjskes = 0;
foreach ($readyData as $dat => $bar) {
	if (6 == $comp[$bar['idkomponen']] || 6 == $bar['idkomponen']) {
		$jkk += $bar['jumlah'];
	}
	if (7 == $comp[$bar['idkomponen']] || 7 == $bar['idkomponen']) {
		$jkm += $bar['jumlah'];
	}
	if (57 == $comp[$bar['idkomponen']] || 57 == $bar['idkomponen']) {
		$bpjspt += $bar['jumlah'];
	}
	if (5 == $comp[$bar['idkomponen']] || 5 == $bar['idkomponen']) {
		$jhtkarypersen += $bar['jumlah'];
	}

	if (9 == $comp[$bar['idkomponen']] || 9 == $bar['idkomponen']) {
		$jpkarypersen += $bar['jumlah'];
	}
	if (8 == $comp[$bar['idkomponen']] || 8 == $bar['idkomponen']) {
		$potonganbpjskes += $bar['jumlah'];
	}
}

if ('HOLDING' == $tip) {
	echo "<button class=mybutton onclick=prosesGajiBulanan(1) id=btnprosesHO>Process</button>\r\n            <table class=sortable cellspacing=1 border=0>\r\n            <thead>\r\n            <tr align=center class=rowcontent 
	style=\"background-color:grey;font-size:10pt\"><td>No</td><td>" . $_SESSION['lang']['periode'] . "</td><td 
	style=\"background-color:yellow;font-weight:bold\">Total Gapok+Tunj.Tetap</td>\r\n            <td 
	style=\"background-color:lightblue;font-weight:bold\">Lembur</td>\r\n            <td 
	style=\"background-color:lightblue;font-weight:bold\">Premi BKM</td><td 
	style=\"background-color:lightblue;font-weight:bold\">Premi Pendapatan Lainnya</td>\r\n            <td 
	style=\"background-color:lightblue;font-weight:bold\">Tunjangan dan Rapel</td>\r\n            <td	
	style=\"background-color:lightblue;font-weight:bold\">" . $_SESSION['lang']['gross'] . "</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkk'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkm'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['bpjskes'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">Total " . $_SESSION['lang']['gross'] . " </td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['biayajabatan'] . "</td>\r\n                        \r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jhtkary'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jpkary'] . "</td>\r\n            <td style=\"background-color:lightcyan;font-weight:bold\">" . $_SESSION['lang']['pph21'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Pinjaman</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Egrek</b></td>\r\n            <td 
	style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Karyawan</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Angkong</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['potdenda'] . "</b></td>\r\n            <!--td style=\"background-color:#efc6b1;font-weight:bold\">Denda Panen</td-->\r\n            <td 
	style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['bpjskes'] . "</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Total Potongan</b></td> \r\n<td 
	style=\"background-color:#55fc7f;font-weight:bold\"><b>Take Home Pay (THP)</b></td>\r\n                        </tr>\r\n                      </thead>\r\n                      <tbody>";
	$no = 0;
	foreach ($gaji as $key => $baris) {
		foreach ($baris as $val => $jlh) {
			$ttl += $jlh;
			if (1 == $val) {
				$id1 += $jlh;
			} else {
				if (2 == $val) {
					$id2 += $jlh;
				} else {
					if (3 == $val || 35 == $val || 36 == $val || 37 == $val || 38 == $val || 39 == $val || 40 == $val || 41 == $val || 42 == $val || 43 == $val || 44 == $val || 46 == $val || 47 == $val || 48 == $val || 49 == $val || 50 == $val || 51 == $val) {
						$id3 += $jlh;
					} else {
						if (4 == $val) {
							$id4 += $jlh;
						} else {
							if (15 == $val) {
								$id15 += $jlh;
							} else {
								if (29 == $val || 30 == $val || 32 == $val || 33 == $val) {
									$id29 += $jlh;
								} else {
									if (17 == $val) {
										$id17 += $jlh;
									} else {
										if (16 == $val) {
											$id16 += $jlh;
										} else {
											if (63 == $val) {
												$id63 += $jlh;
											} else {
												if (58 == $val) {
													$id58 += $jlh;
												} else {
													if (59 == $val) {
														$id59 += $jlh;
													} else {
														if (61 == $val) {
															$id61 += $jlh;
														} else {
															if (65 == $val) {
																$id65 += $jlh;
															} else {
																if (60 == $val) {
																	$id60 += $jlh;
																} else {
																	if (21 == $val) {
																		$id21 += $jlh;
																	} else {
																		if (23 == $val) {
																			$id23 += $jlh;
																		} else {
																			if (12 == $val) {
																				$id12 += $jlh;
																			} else {
																				if (62 == $val) {
																					$id62 += $jlh;
																				} else {
																					if (22 == $val) {
																						$id22 += $jlh;
																					} else {
																						if (54 == $val) {
																							$id54 += $jlh;
																						} else {
																							if (6 == $val) {
																								$id6 += $jlh;
																							} else {
																								if (7 == $val) {
																									$id7 += $jlh;
																								} else {
																									if (57 == $val) {
																										$id57 += $jlh;
																									} else {
																										if (24 == $val) {
																											$ttlid2 += $jlh;
																										} else {
																											if (5 == $val) {
																												$ttlid6 += $jlh;
																											} else {
																												if (9 == $val) {
																													$ttlid7 += $jlh;
																												} else {
																													if (57 == $val) {
																														$ttlid32 += $jlh;
																													} else {
																														if (8 == $val) {
																															$ttlid33 += $jlh;
																														} else {
																															if (52 == $val) {
																																$ttlid34 += $jlh;
																															} else {
																																if (10 == $val) {
																																	$ttlid39 += $jlh;
																																} else {
																																	if (11 == $val) {
																																		$ttlid35 += $jlh;
																																	} else {
																																		if (99 == $val) {
																																			$ttlid36 += $jlh;
																																		} else {
																																			if (64 == $val || 27 == $val) {
																																				$ttlid37 += $jlh;
																																			} else {
																																				if (25 == $val) {
																																					$ttlid38 += $jlh;
																																				} else {
																																					if (66 == $val) {
																																						$ttlpotbyjab += $jlh;
																																					} else {
																																						$ttlidlain += $jlh;
																																					}
																																				}
																																			}
																																		}
																																	}
																																}
																															}
																														}
																													}
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$ttltunjtetap = $id1 + $id2 + $id3 + $id4 + $id29 + $id15;
			$ttlid = ($id1 + $id2 + $id3 + $id4) + $id17 + $id16 + $id63 + $id58 + $id59 + $id61 + $id65 + $id60 + $id21 + $id23 + $id12 + $id62 + $id22 + $id54 + $premi;
			$totgross = $ttlid + $jkk + $jkm + $bpjspt;
			$netto = ($ttlid + $ttlid32 + $ttlid6 + $ttlid7) - ($ttlid2 + $ttlid29 + $ttlid30 + $ttlid33);
			$thp = $netto - ($ttlid34 + $ttlid35 + $ttlid36 + $ttlid37 + $ttlid38);
			$totnettsebulan = $totgross - $ttlpotbyjab - $ttlid6 - $ttlid7;
			$thpbruto = $totnettsebulan - $ttlid2;
			$thpnett = $thpbruto - ($ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $ttlid33 + $ttlid6 + $ttlid7);
			$totpot = $ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $potonganbpjskes + $ttlid6 + $ttlid7 + $ttlpotbyjab;
		}
	}
	echo $thpbruto;
	++$no;
	echo "<tr class=rowcontent id='row" . $no . "' style=\"font-size:11pt;font-weight:bold\">\r\n    <td>" . $no . "</td>\r\n    <td id='periode" . $no . "'>" . $_POST['periode'] . "</td>\r\n\r\n    
	<input type=hidden id='komponen" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetap" . $no . "' style=\"background-color:yellow;font-weight:bold\">" . number_format($ttltunjtetap, 0, ',', ',') . "</td>\r\n\r\n    

	<input type=hidden id='komponenlembur" . $no . "' value='17'>\r\n    <td align=right id='totlembur" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id17, 0, ',', ',') . "</td>\r\n\r\n  
	<input type=hidden id='komponentunjpremi" . $no . "' value='16'>\r\n    <td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($premi, 0, ',', ',') . "</td><td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id16, 0, ',', ',') . "</td>\r\n\r\n
	
	<input type=hidden id='komponentunjkom" . $no . "' value='63'>\r\n    <td align=right id='tottunjkom" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id63, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjlok" . $no . "' value='58'>\r\n    <td align=right id='tottunjlok" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id58, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjprt" . $no . "' value='59'>\r\n    <td align=right id='tottunjprt" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id59, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjbbm" . $no . "' value='61'>\r\n    <td align=right id='tottunjbbm" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id61, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjair" . $no . "' value='65'>\r\n    <td align=right id='tottunjair" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id65, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjspart" . $no . "' value='60'>\r\n    <td align=right id='tottunjspart" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id60, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjharian" . $no . "' value='21'>\r\n    <td align=right id='tottunjharian" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id21, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjdinas" . $no . "' value='23'>\r\n    <td align=right id='tottunjdinas" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id23, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjcuti" . $no . "' value='12'>\r\n    <td align=right id='tottunjcuti" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id12, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjlistrik" . $no . "' value='62'>\r\n    <td align=right id='tottunjlistrik" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id62, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjlain" . $no . "' value='22'>\r\n    <td align=right id='tottunjlain" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id22, 0, ',', ',') . "</td>\r\n\r\n
	<input type=hidden id='komponentunjrapel" . $no . "' value='54'>\r\n    <td align=right id='tottunjrapel" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id54, 0, ',', ',') . "</td>\r\n\r\n    

	<input type=hidden id='komponentottunjrapel" . $no . "' value='111'>\r\n    <td align=right id='tottunjrapel" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format(($id63 + $id58 + $id59 + $id61 + $id65 + $id60 + $id21 + $id23 + $id12 + $id62 + $id22 + $id54), 0, ',', ',') . "</td>\r\n\r\n    

	<input type=hidden id='komponengross" . $no . "' value='111'>\r\n    <td align=right id='totgrosstetap" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($ttlid, 0, ',', ',') . "</td>\r\n\r\n    
	<input type=hidden id='komponentunjjkk" . $no . "' value='6'>\r\n    <td align=right id='tottunjjkk" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkk, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkm" . $no . "' value='7'>\r\n    <td align=right id='tottunjjkm" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkm, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjbpjskes" . $no . "' value='57'>\r\n    <td align=right id='tottunjbpjskes" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($bpjspt, 0, ',', ',') . "</td>\r\n\r\n    
	<input type=hidden id='komponentunjtetaptidaktetap" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetaptidaktetap" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($totgross, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotbiayajab" . $no . "' value='66'>\r\n    <td align=right id='totpotbiayajab" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlpotbyjab, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjhtkar" . $no . "' value='5'>\r\n    <td align=right id='totpotjhtkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jhtkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjpkar" . $no . "' value='9'>\r\n    <td align=right id='totpotjpkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jpkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotpph21" . $no . "' value='24'>\r\n    <td align=right id='totpotpph21" . $no . "' style=\"background-color:lightcyan;font-weight:bold\">" . number_format($ttlid2, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotkoperasi" . $no . "' value='25'>\r\n    <td align=right id='totpotkoperasi" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid38, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotvop" . $no . "' value='52'>\r\n    <td align=right id='totpotvop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid34, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotmotor" . $no . "' value='10'>\r\n    <td align=right id='totpotmotor" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid39, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotlaptop" . $no . "' value='11'>\r\n    <td align=right id='totpotlaptop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid35, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotdenda" . $no . "' value='64'>\r\n    <td align=right id='totpotdenda" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandenda, 0, ',', ',') . "</td>\r\n\r\n    <!--input type=hidden id='komponenpotdendapanen" . $no . "' value='26'>\r\n    <td align=right id='totpotdendapanen" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandendapanen, 0, ',', ',') . "</td-->\r\n\r\n    <input type=hidden id='komponenpotbpjskes" . $no . "' value='8'>\r\n    <td align=right id='totpotbpjskes" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potonganbpjskes, 0, ',', ',') . "</td>\r\n   \r\n    <input type=hidden id='komponenpotall" . $no . "' value='888'>\r\n    <td align=right id='totpotall" . $no . "' style=\"background-color:#efc6b1\">" . number_format($totpot, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenthpbersih" . $no . "' value='777'>\r\n    <td align=right id='totthpbersih" . $no . "' style=\"background-color:#55fc7f;font-weight:bold\">" . number_format($thpnett, 0, ',', ',') . "</td>                       \r\n                        \r\n    </tr>";
	echo '</tbody><tfoot></tfoot></table>';
} else {
	if ('KANWIL' == $tip) {

		echo "<button class=mybutton onclick=prosesGajiKanwil(1) id=btnprosesKanwil>Process</button>\r\n            <table class=sortable cellspacing=1 border=0>\r\n            <thead>\r\n            <tr align=center class=rowcontent style=\"background-color:grey;font-size:10pt\">\r\n            <td>No</td>\r\n            <td>" . $_SESSION['lang']['periode'] . "</td>\r\n            <td style=\"background-color:yellow;font-weight:bold\">Total Gapok+Tunj.Tetap</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Lembur</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Premi BKM</td>\r\n <td style=\"background-color:lightblue;font-weight:bold\">Premi Pendapatan Lainnya</td>           <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Komunikasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lokasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Rmh.Tangga</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.BBM</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Air.Mnm</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.S.Part</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Harian</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Dinas</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Cuti</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Listrik</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lain(Ban Luar/Dalam)</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Rapel Kenaikan</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">" . $_SESSION['lang']['gross'] . "</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkk'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkm'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['bpjskes'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">Total " . $_SESSION['lang']['gross'] . " </td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['biayajabatan'] . "</td>\r\n                        \r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jhtkary'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jpkary'] . "</td>\r\n            <td style=\"background-color:lightcyan;font-weight:bold\">" . $_SESSION['lang']['pph21'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Pinjaman</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Egrek</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Karyawan</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Angkong</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['potdenda'] . "</b></td>\r\n            <!--td style=\"background-color:#efc6b1;font-weight:bold\">Denda Panen</td-->\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['bpjskes'] . "</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Total Potongan</b></td> \r\n<td style=\"background-color:#55fc7f;font-weight:bold\"><b>Take Home Pay (THP)</b></td>\r\n                        </tr>\r\n                      </thead>\r\n                      <tbody>";
		$no = 0;
		foreach ($gaji as $key => $baris) {
			foreach ($baris as $val => $jlh) {
				$ttl += $jlh;
				if (1 == $val) {
					$id1 += $jlh;
				} else {
					if (2 == $val) {
						$id2 += $jlh;
					} else {
						if (3 == $val || 35 == $val || 36 == $val || 37 == $val || 38 == $val || 39 == $val || 40 == $val || 41 == $val || 42 == $val || 43 == $val || 44 == $val || 46 == $val || 47 == $val || 48 == $val || 49 == $val || 50 == $val || 51 == $val) {
							$id3 += $jlh;
						} else {
							if (4 == $val) {
								$id4 += $jlh;
							} else {
								if (15 == $val) {
									$id15 += $jlh;
								} else {
									if (29 == $val || 30 == $val || 32 == $val || 33 == $val) {
										$id29 += $jlh;
									} else {
										if (17 == $val) {
											$id17 += $jlh;
										} else {
											if (16 == $val) {
												$id16 += $jlh;
											} else {
												if (63 == $val) {
													$id63 += $jlh;
												} else {
													if (58 == $val) {
														$id58 += $jlh;
													} else {
														if (59 == $val) {
															$id59 += $jlh;
														} else {
															if (61 == $val) {
																$id61 += $jlh;
															} else {
																if (65 == $val) {
																	$id65 += $jlh;
																} else {
																	if (60 == $val) {
																		$id60 += $jlh;
																	} else {
																		if (21 == $val) {
																			$id21 += $jlh;
																		} else {
																			if (23 == $val) {
																				$id23 += $jlh;
																			} else {
																				if (12 == $val) {
																					$id12 += $jlh;
																				} else {
																					if (62 == $val) {
																						$id62 += $jlh;
																					} else {
																						if (22 == $val) {
																							$id22 += $jlh;
																						} else {
																							if (54 == $val) {
																								$id54 += $jlh;
																							} else {
																								if (6 == $val) {
																									$id6 += $jlh;
																								} else {
																									if (7 == $val) {
																										$id7 += $jlh;
																									} else {
																										if (57 == $val) {
																											$id57 += $jlh;
																										} else {
																											if (24 == $val) {
																												$ttlid2 += $jlh;
																											} else {
																												if (5 == $val) {
																													$ttlid6 += $jlh;
																												} else {
																													if (9 == $val) {
																														$ttlid7 += $jlh;
																													} else {
																														if (57 == $val) {
																															$ttlid32 += $jlh;
																														} else {
																															if (8 == $val) {
																																$ttlid33 += $jlh;
																															} else {
																																if (52 == $val) {
																																	$ttlid34 += $jlh;
																																} else {
																																	if (10 == $val) {
																																		$ttlid39 += $jlh;
																																	} else {
																																		if (11 == $val) {
																																			$ttlid35 += $jlh;
																																		} else {
																																			if (99 == $val) {
																																				$ttlid36 += $jlh;
																																			} else {
																																				if (64 == $val || 27 == $val) {
																																					$ttlid37 += $jlh;
																																				} else {
																																					if (25 == $val) {
																																						$ttlid38 += $jlh;
																																					} else {
																																						if (66 == $val) {
																																							$ttlpotbyjab += $jlh;
																																						} else {
																																							$ttlidlain += $jlh;
																																						}
																																					}
																																				}
																																			}
																																		}
																																	}
																																}
																															}
																														}
																													}
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

				$ttltunjtetap = $id1 + $id2 + $id3 + $id4 + $id29 + $id15;
                // $ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id17 + $id63 + $id58 + $id59 + $id61 + $id60 + $id21 + $id23 + $id12 + $id62 + $id22 + $id54 + $id16 + $id15;
				$ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id15 + $id17 + $id16 + $id59 + $id68 + $id21 + $id23 + $id12 + $id67 + $id22 + $id54 + $premi;
				$totgross = $ttlid + $jkk + $jkm + $bpjspt;
				$netto = ($ttlid + $ttlid32 + $ttlid6 + $ttlid7) - ($ttlid2 + $ttlid29 + $ttlid30 + $ttlid33);
				$thp = $netto - ($ttlid34 + $ttlid35 + $ttlid36 + $ttlid37 + $ttlid38);
				$totnettsebulan = $totgross - $ttlpotbyjab - $ttlid6 - $ttlid7;
				$thpbruto = $totnettsebulan - $ttlid2;

                // $thpnetto[$val[0]] = $totgajibruto[$val[0]] - $potongankoperasi[$val[0]] - $potonganvop[$val[0]] - $potonganmotor[$val[0]] - $potonganlaptop[$val[0]] - $potongandenda[$val[0]] - $potdendapanen[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]] - $potonganbpjskes[$val[0]] - $pphSetahun[$val[0]] - $biayajab[$val[0]];

				$thpnett = $totgross - ($ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $ttlid33 + $ttlid6 + $ttlid7 + $ttlpotbyjab);
				$totpot = $ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $potonganbpjskes + $ttlid6 + $ttlid7 + $ttlpotbyjab;
			}
		}
		++$no;
		echo "<tr class=rowcontent id='row" . $no . "' style=\"font-size:11pt;font-weight:bold\">\r\n    <td>" . $no . "</td>\r\n    <td id='periode" . $no . "'>" . $_POST['periode'] . "</td>\r\n\r\n    <input type=hidden id='komponen" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetap" . $no . "' style=\"background-color:yellow;font-weight:bold\">" . number_format($ttltunjtetap, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenlembur" . $no . "' value='17'>\r\n    <td align=right id='totlembur" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id17, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjpremi" . $no . "' value='16'>\r\n  <td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($premi, 0, ',', ',') . "</td>  <td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id16, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjkom" . $no . "' value='63'>\r\n    <td align=right id='tottunjkom" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id63, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlok" . $no . "' value='58'>\r\n    <td align=right id='tottunjlok" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id58, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjprt" . $no . "' value='59'>\r\n    <td align=right id='tottunjprt" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id59, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjbbm" . $no . "' value='61'>\r\n    <td align=right id='tottunjbbm" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id61, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjair" . $no . "' value='65'>\r\n    <td align=right id='tottunjair" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id65, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjspart" . $no . "' value='60'>\r\n    <td align=right id='tottunjspart" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id60, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjharian" . $no . "' value='21'>\r\n    <td align=right id='tottunjharian" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id21, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjdinas" . $no . "' value='23'>\r\n    <td align=right id='tottunjdinas" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id23, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjcuti" . $no . "' value='12'>\r\n    <td align=right id='tottunjcuti" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id12, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlistrik" . $no . "' value='62'>\r\n    <td align=right id='tottunjlistrik" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id62, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlain" . $no . "' value='22'>\r\n    <td align=right id='tottunjlain" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id22, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjrapel" . $no . "' value='54'>\r\n    <td align=right id='tottunjrapel" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id54, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponengross" . $no . "' value='111'>\r\n    <td align=right id='totgrosstetap" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($ttlid, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkk" . $no . "' value='6'>\r\n    <td align=right id='tottunjjkk" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkk, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkm" . $no . "' value='7'>\r\n    <td align=right id='tottunjjkm" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkm, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjbpjskes" . $no . "' value='57'>\r\n    <td align=right id='tottunjbpjskes" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($bpjspt, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjtetaptidaktetap" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetaptidaktetap" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($totgross, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotbiayajab" . $no . "' value='66'>\r\n    <td align=right id='totpotbiayajab" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlpotbyjab, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjhtkar" . $no . "' value='5'>\r\n    <td align=right id='totpotjhtkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jhtkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjpkar" . $no . "' value='9'>\r\n    <td align=right id='totpotjpkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jpkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotpph21" . $no . "' value='24'>\r\n    <td align=right id='totpotpph21" . $no . "' style=\"background-color:lightcyan;font-weight:bold\">" . number_format($ttlid2, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotkoperasi" . $no . "' value='25'>\r\n    <td align=right id='totpotkoperasi" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid38, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotvop" . $no . "' value='52'>\r\n    <td align=right id='totpotvop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid34, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotmotor" . $no . "' value='10'>\r\n    <td align=right id='totpotmotor" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid39, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotlaptop" . $no . "' value='11'>\r\n    <td align=right id='totpotlaptop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid35, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotdenda" . $no . "' value='64'>\r\n    <td align=right id='totpotdenda" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandenda, 0, ',', ',') . "</td>\r\n\r\n    <!--input type=hidden id='komponenpotdendapanen" . $no . "' value='26'>\r\n    <td align=right id='totpotdendapanen" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandendapanen, 0, ',', ',') . "</td-->\r\n\r\n    <input type=hidden id='komponenpotbpjskes" . $no . "' value='8'>\r\n    <td align=right id='totpotbpjskes" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potonganbpjskes, 0, ',', ',') . "</td>\r\n   \r\n    <input type=hidden id='komponenpotall" . $no . "' value='888'>\r\n    <td align=right id='totpotall" . $no . "' style=\"background-color:#efc6b1\">" . number_format($totpot, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenthpbersih" . $no . "' value='777'>\r\n    <td align=right id='totthpbersih" . $no . "' style=\"background-color:#55fc7f;font-weight:bold\">" . number_format($thpnett, 0, ',', ',') . "</td>                       \r\n                        \r\n    </tr>";
		echo '</tbody><tfoot></tfoot></table>';
	} else {
		if ('PABRIK' == $tip) {

			echo "<button class=mybutton onclick=prosesGajiPabrik(1) id=btnprosesPabrik>Process</button>\r\n            <table class=sortable cellspacing=1 border=0>\r\n            <thead>\r\n            <tr align=center class=rowcontent style=\"background-color:grey;font-size:10pt\">\r\n            <td>No</td>\r\n            <td>" . $_SESSION['lang']['periode'] . "</td>\r\n            <td style=\"background-color:yellow;font-weight:bold\">Total Gapok+Tunj.Tetap</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Lembur</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Premi BKM</td><td style=\"background-color:lightblue;font-weight:bold\">Premi Pendapatan Lainnya</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Komunikasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lokasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Rmh.Tangga</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.BBM</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Air.Mnm</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.S.Part</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Harian</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Dinas</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Cuti</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Listrik</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lain(Ban Luar/Dalam)</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Rapel Kenaikan</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">" . $_SESSION['lang']['gross'] . "</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkk'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkm'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['bpjskes'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">Total " . $_SESSION['lang']['gross'] . " </td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['biayajabatan'] . "</td>\r\n                        \r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jhtkary'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jpkary'] . "</td>\r\n            <td style=\"background-color:lightcyan;font-weight:bold\">" . $_SESSION['lang']['pph21'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Pinjaman</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Egrek</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Karyawan</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Angkong</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['potdenda'] . "</b></td>\r\n            <!--td style=\"background-color:#efc6b1;font-weight:bold\">Denda Panen</td-->\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['bpjskes'] . "</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Total Potongan</b></td> \r\n<td style=\"background-color:#55fc7f;font-weight:bold\"><b>Take Home Pay (THP)</b></td>\r\n                        </tr>\r\n                      </thead>\r\n                      <tbody>";
			$no = 0;
			foreach ($gaji as $key => $baris) {
				foreach ($baris as $val => $jlh) {
					$ttl += $jlh;
					if (1 == $val) {
						$id1 += $jlh;
					} else {
						if (2 == $val) {
							$id2 += $jlh;
						} else {
							if (3 == $val || 35 == $val || 36 == $val || 37 == $val || 38 == $val || 39 == $val || 40 == $val || 41 == $val || 42 == $val || 43 == $val || 44 == $val || 46 == $val || 47 == $val || 48 == $val || 49 == $val || 50 == $val || 51 == $val) {
								$id3 += $jlh;
							} else {
								if (4 == $val) {
									$id4 += $jlh;
								} else {
									if (15 == $val) {
										$id15 += $jlh;
									} else {
										if (29 == $val || 30 == $val || 32 == $val || 33 == $val) {
											$id29 += $jlh;
										} else {
											if (17 == $val) {
												$id17 += $jlh;
											} else {
												if (16 == $val) {
													$id16 += $jlh;
												} else {
													if (63 == $val) {
														$id63 += $jlh;
													} else {
														if (58 == $val) {
															$id58 += $jlh;
														} else {
															if (59 == $val) {
																$id59 += $jlh;
															} else {
																if (61 == $val) {
																	$id61 += $jlh;
																} else {
																	if (65 == $val) {
																		$id65 += $jlh;
																	} else {
																		if (60 == $val) {
																			$id60 += $jlh;
																		} else {
																			if (21 == $val) {
																				$id21 += $jlh;
																			} else {
																				if (23 == $val) {
																					$id23 += $jlh;
																				} else {
																					if (12 == $val) {
																						$id12 += $jlh;
																					} else {
																						if (62 == $val) {
																							$id62 += $jlh;
																						} else {
																							if (22 == $val) {
																								$id22 += $jlh;
																							} else {
																								if (54 == $val) {
																									$id54 += $jlh;
																								} else {
																									if (6 == $val) {
																										$id6 += $jlh;
																									} else {
																										if (7 == $val) {
																											$id7 += $jlh;
																										} else {
																											if (57 == $val) {
																												$id57 += $jlh;
																											} else {
																												if (24 == $val) {
																													$ttlid2 += $jlh;
																												} else {
																													if (5 == $val) {
																														$ttlid6 += $jlh;
																													} else {
																														if (9 == $val) {
																															$ttlid7 += $jlh;
																														} else {
																															if (57 == $val) {
																																$ttlid32 += $jlh;
																															} else {
																																if (8 == $val) {
																																	$ttlid33 += $jlh;
																																} else {
																																	if (52 == $val) {
																																		$ttlid34 += $jlh;
																																	} else {
																																		if (10 == $val) {
																																			$ttlid39 += $jlh;
																																		} else {
																																			if (11 == $val) {
																																				$ttlid35 += $jlh;
																																			} else {
																																				if (99 == $val) {
																																					$ttlid36 += $jlh;
																																				} else {
																																					if (64 == $val || 27 == $val) {
																																						$ttlid37 += $jlh;
																																					} else {
																																						if (25 == $val) {
																																							$ttlid38 += $jlh;
																																						} else {
																																							if (66 == $val) {
																																								$ttlpotbyjab += $jlh;
																																							} else {
																																								$ttlidlain += $jlh;
																																							}
																																						}
																																					}
																																				}
																																			}
																																		}
																																	}
																																}
																															}
																														}
																													}
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}

					$ttltunjtetap = $id1 + $id2 + $id3 + $id4 + $id29 + $id15;
                    // $ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id17 + $id63 + $id58 + $id59 + $id61 + $id60 + $id21 + $id23 + $id12 + $id62 + $id22 + $id54 + $id16 + $id15;
					$ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id15 + $id17 + $id16 + $id59 + $id68 + $id21 + $id23 + $id12 + $id67 + $id22 + $id54 + $premi;
					$totgross = $ttlid + $jkk + $jkm + $bpjspt;
					$netto = ($ttlid + $ttlid32 + $ttlid6 + $ttlid7) - ($ttlid2 + $ttlid29 + $ttlid30 + $ttlid33);
					$thp = $netto - ($ttlid34 + $ttlid35 + $ttlid36 + $ttlid37 + $ttlid38);
					$totnettsebulan = $totgross - $ttlpotbyjab - $ttlid6 - $ttlid7;
					$thpbruto = $totnettsebulan - $ttlid2;
					$thpnett = $totgross - ($ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $ttlid33 + $ttlid6 + $ttlid7 + $ttlpotbyjab);
					$totpot = $ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $potonganbpjskes + $ttlid6 + $ttlid7 + $ttlpotbyjab;
				}
			}
			++$no;
			echo "<tr class=rowcontent id='row" . $no . "' style=\"font-size:11pt;font-weight:bold\">\r\n    <td>" . $no . "</td>\r\n    <td id='periode" . $no . "'>" . $_POST['periode'] . "</td>\r\n\r\n    <input type=hidden id='komponen" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetap" . $no . "' style=\"background-color:yellow;font-weight:bold\">" . number_format($ttltunjtetap, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenlembur" . $no . "' value='17'>\r\n    <td align=right id='totlembur" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id17, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjpremi" . $no . "' value='16'>\r\n    <td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($premi, 0, ',', ',') . "</td><input type=hidden id='komponentunjpremi" . $no . "' value='16'>\r\n    <td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id16, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjkom" . $no . "' value='63'>\r\n    <td align=right id='tottunjkom" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id63, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlok" . $no . "' value='58'>\r\n    <td align=right id='tottunjlok" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id58, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjprt" . $no . "' value='59'>\r\n    <td align=right id='tottunjprt" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id59, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjbbm" . $no . "' value='61'>\r\n    <td align=right id='tottunjbbm" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id61, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjair" . $no . "' value='65'>\r\n    <td align=right id='tottunjair" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id65, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjspart" . $no . "' value='60'>\r\n    <td align=right id='tottunjspart" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id60, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjharian" . $no . "' value='21'>\r\n    <td align=right id='tottunjharian" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id21, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjdinas" . $no . "' value='23'>\r\n    <td align=right id='tottunjdinas" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id23, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjcuti" . $no . "' value='12'>\r\n    <td align=right id='tottunjcuti" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id12, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlistrik" . $no . "' value='62'>\r\n    <td align=right id='tottunjlistrik" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id62, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlain" . $no . "' value='22'>\r\n    <td align=right id='tottunjlain" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id22, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjrapel" . $no . "' value='54'>\r\n    <td align=right id='tottunjrapel" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id54, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponengross" . $no . "' value='111'>\r\n    <td align=right id='totgrosstetap" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($ttlid, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkk" . $no . "' value='6'>\r\n    <td align=right id='tottunjjkk" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkk, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkm" . $no . "' value='7'>\r\n    <td align=right id='tottunjjkm" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkm, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjbpjskes" . $no . "' value='57'>\r\n    <td align=right id='tottunjbpjskes" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($bpjspt, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjtetaptidaktetap" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetaptidaktetap" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($totgross, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotbiayajab" . $no . "' value='66'>\r\n    <td align=right id='totpotbiayajab" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlpotbyjab, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjhtkar" . $no . "' value='5'>\r\n    <td align=right id='totpotjhtkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jhtkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjpkar" . $no . "' value='9'>\r\n    <td align=right id='totpotjpkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jpkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotpph21" . $no . "' value='24'>\r\n    <td align=right id='totpotpph21" . $no . "' style=\"background-color:lightcyan;font-weight:bold\">" . number_format($ttlid2, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotkoperasi" . $no . "' value='25'>\r\n    <td align=right id='totpotkoperasi" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid38, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotvop" . $no . "' value='52'>\r\n    <td align=right id='totpotvop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid34, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotmotor" . $no . "' value='10'>\r\n    <td align=right id='totpotmotor" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid39, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotlaptop" . $no . "' value='11'>\r\n    <td align=right id='totpotlaptop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid35, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotdenda" . $no . "' value='64'>\r\n    <td align=right id='totpotdenda" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandenda, 0, ',', ',') . "</td>\r\n\r\n    <!--input type=hidden id='komponenpotdendapanen" . $no . "' value='26'>\r\n    <td align=right id='totpotdendapanen" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandendapanen, 0, ',', ',') . "</td-->\r\n\r\n    <input type=hidden id='komponenpotbpjskes" . $no . "' value='8'>\r\n    <td align=right id='totpotbpjskes" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potonganbpjskes, 0, ',', ',') . "</td>\r\n   \r\n    <input type=hidden id='komponenpotall" . $no . "' value='888'>\r\n    <td align=right id='totpotall" . $no . "' style=\"background-color:#efc6b1\">" . number_format($totpot, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenthpbersih" . $no . "' value='777'>\r\n    <td align=right id='totthpbersih" . $no . "' style=\"background-color:#55fc7f;font-weight:bold\">" . number_format($thpnett, 0, ',', ',') . "</td>                       \r\n                        \r\n    </tr>";
			echo '</tbody><tfoot></tfoot></table>';
		} else {
			echo "<button class=mybutton onclick=prosesGaji(1) id=btnproses>Process</button>\r\n            <table class=sortable cellspacing=1 border=0>\r\n            <thead>\r\n            <tr align=center class=rowcontent style=\"background-color:grey;font-size:10pt\">\r\n            <td>No</td>\r\n            <td>" . $_SESSION['lang']['periode'] . "</td>\r\n            <td style=\"background-color:yellow;font-weight:bold\">Total Gapok+Tunj.Tetap</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Lembur</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Premi BKM</td><td style=\"background-color:lightblue;font-weight:bold\">Premi Pendapatan Lainnya</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Komunikasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lokasi</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Rmh.Tangga</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.BBM</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Air.Mnm</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.S.Part</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Harian</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Dinas</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Cuti</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Listrik</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Tunj.Lain(Ban Luar/Dalam)</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">Rapel Kenaikan</td>\r\n            <td style=\"background-color:lightblue;font-weight:bold\">" . $_SESSION['lang']['gross'] . "</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkk'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['jkm'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">" . $_SESSION['lang']['bpjskes'] . " (P)</td>\r\n            <td style=\"background-color:lightgrey;font-weight:bold\">Total " . $_SESSION['lang']['gross'] . " </td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['biayajabatan'] . "</td>\r\n                        \r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jhtkary'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\">" . $_SESSION['lang']['jpkary'] . "</td>\r\n            <td style=\"background-color:lightcyan;font-weight:bold\">" . $_SESSION['lang']['pph21'] . "</td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Pinjaman</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Egrek</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Karyawan</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Angsuran Angkong</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['potdenda'] . "</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Denda Panen</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>" . $_SESSION['lang']['bpjskes'] . "</b></td>\r\n            <td style=\"background-color:#efc6b1;font-weight:bold\"><b>Total Potongan</b></td> \r\n<td style=\"background-color:#55fc7f;font-weight:bold\"><b>Take Home Pay (THP)</b></td>\r\n                        </tr>\r\n                      </thead>\r\n                      <tbody>";
			$no = 0;
			foreach ($gaji as $key => $baris) {
				foreach ($baris as $val => $jlh) {
					$ttl += $jlh;
					if (1 == $val) {
						$id1 += $jlh;
					} else {
						if (2 == $val) {
							$id2 += $jlh;
						} else {
							if (3 == $val || 35 == $val || 36 == $val || 37 == $val || 38 == $val || 39 == $val || 40 == $val || 41 == $val || 42 == $val || 43 == $val || 44 == $val || 46 == $val || 47 == $val || 48 == $val || 49 == $val || 50 == $val || 51 == $val) {
								$id3 += $jlh;
							} else {
								if (4 == $val) {
									$id4 += $jlh;
								} else {
									if (15 == $val) {
										$id15 += $jlh;
									} else {
										if (29 == $val || 30 == $val || 32 == $val || 33 == $val) {
											$id29 += $jlh;
										} else {
											if (16 == $val) {
												$id16 += $jlh;
											} else {
												if (17 == $val) {
													$id17 += $jlh;
												} else {
													if (63 == $val) {
														$id63 += $jlh;
													} else {
														if (58 == $val) {
															$id58 += $jlh;
														} else {
															if (59 == $val) {
																$id59 += $jlh;
															} else {
																if (61 == $val) {
																	$id61 += $jlh;
																} else {
																	if (65 == $val) {
																		$id65 += $jlh;
																	} else {
																		if (60 == $val) {
																			$id60 += $jlh;
																		} else {
																			if (21 == $val) {
																				$id21 += $jlh;
																			} else {
																				if (23 == $val) {
																					$id23 += $jlh;
																				} else {
																					if (12 == $val) {
																						$id12 += $jlh;
																					} else {
																						if (62 == $val) {
																							$id62 += $jlh;
																						} else {
																							if (22 == $val) {
																								$id22 += $jlh;
																							} else {
																								if (54 == $val) {
																									$id54 += $jlh;
																								} else {
																									if (6 == $val) {
																										$id6 += $jlh;
																									} else {
																										if (7 == $val) {
																											$id7 += $jlh;
																										} else {
																											if (57 == $val) {
																												$id57 += $jlh;
																											} else {
																												if (24 == $val) {
																													$ttlid2 += $jlh;
																												} else {
																													if (5 == $val) {
																														$ttlid6 += $jlh;
																													} else {
																														if (9 == $val) {
																															$ttlid7 += $jlh;
																														} else {
																															if (57 == $val) {
																																$ttlid32 += $jlh;
																															} else {
																																if (8 == $val) {
																																	$ttlid33 += $jlh;
																																} else {
																																	if (52 == $val) {
																																		$ttlid34 += $jlh;
																																	} else {
																																		if (10 == $val) {
																																			$ttlid39 += $jlh;
																																		} else {
																																			if (11 == $val) {
																																				$ttlid35 += $jlh;
																																			} else {
																																				if (99 == $val) {
																																					$ttlid36 += $jlh;
																																				} else {
																																					if (64 == $val || 27 == $val) {
																																						$ttlid37 += $jlh;
																																					} else {
																																						if (25 == $val) {
																																							$ttlid38 += $jlh;
																																						} else {
																																							if (66 == $val) {
																																								$ttlpotbyjab += $jlh;
																																							} else {
																																								if (26 == $val) {
																																									$ttlid26 += $jlh;
																																								} else {
																																									$ttlidlain += $jlh;
																																								}
																																							}
																																						}
																																					}
																																				}
																																			}
																																		}
																																	}
																																}
																															}
																														}
																													}
																												}
																											}
																										}
																									}
																								}
																							}
																						}
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}

					$ttltunjtetap = $id1 + $id2 + $id3 + $id4 + $id29 + $id15;
                    // $ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id17 + $id63 + $id58 + $id59 + $id61 + $id60 + $id21 + $id23 + $id12 + $id62 + $id22 + $id54 + $id16 + $id15;
					$ttlid = $id1 + $id2 + $id3 + $id4 + $id29 + $id15 + $id17 + $id16 + $id59 + $id68 + $id21 + $id23 + $id12 + $id67 + $id22 + $id54 + $premi;
					$totgross = $ttlid + $jkk + $jkm + $bpjspt;
					$netto = ($ttlid + $ttlid32 + $ttlid6 + $ttlid7) - ($ttlid2 + $ttlid29 + $ttlid30 + $ttlid33);
					$thp = $netto - ($ttlid34 + $ttlid35 + $ttlid36 + $ttlid37 + $ttlid38);
					$totnettsebulan = $totgross - $ttlpotbyjab - $ttlid6 - $ttlid7;
					$thpbruto = $totnettsebulan - $ttlid2;
                    // $thpnett = $totgross - ($ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $potongandenda + $potongandendapanen + $jhtkarypersen + $jpkarypersen + $bpjskes + $ttlpotbyjab);
					$thpnett = $totgross - ($ttlid18 + $ttlid26 + $ttlid25 + $ttlid52 + $ttlid10 + $ttlid11 + $jhtkarypersen + $jpkarypersen + $potonganbpjskes + $ttlid24 + $ttlid2 + $ttlid6 + $ttlid7 + $ttlpotbyjab);
					$totpot = $ttlid2 + $ttlid38 + $ttlid34 + $ttlid39 + $ttlid35 + $ttlid37 + $potonganbpjskes + $ttlid6 + $ttlid7 + $ttlpotbyjab + $ttlid26;

                    // 18, 26, 25, 52, 10, 11, jht, jp, potonganbpjskes, 24, 35-60
				}
			}
			// echo $totgross;
			++$no;
			echo "<tr class=rowcontent id='row" . $no . "' style=\"font-size:11pt;font-weight:bold\">\r\n    <td>" . $no . "</td>\r\n    <td id='periode" . $no . "'>" . $_POST['periode'] . "</td>\r\n\r\n    <input type=hidden id='komponen" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetap" . $no . "' style=\"background-color:yellow;font-weight:bold\">" . number_format($ttltunjtetap, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenlembur" . $no . "' value='17'>\r\n    <td align=right id='totlembur" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id17, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjpremi" . $no . "' value='16'>\r\n    <td align=right id='totpremibkm" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($premi, 0, ',', ',') . "</td><td align=right id='totpremi" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id16, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjkom" . $no . "' value='63'>\r\n    <td align=right id='tottunjkom" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id63, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlok" . $no . "' value='58'>\r\n    <td align=right id='tottunjlok" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id58, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjprt" . $no . "' value='59'>\r\n    <td align=right id='tottunjprt" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id59, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjbbm" . $no . "' value='61'>\r\n    <td align=right id='tottunjbbm" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id61, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjair" . $no . "' value='65'>\r\n    <td align=right id='tottunjair" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id65, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjspart" . $no . "' value='60'>\r\n    <td align=right id='tottunjspart" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id60, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjharian" . $no . "' value='21'>\r\n    <td align=right id='tottunjharian" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id21, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjdinas" . $no . "' value='23'>\r\n    <td align=right id='tottunjdinas" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id23, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjcuti" . $no . "' value='12'>\r\n    <td align=right id='tottunjcuti" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id12, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlistrik" . $no . "' value='62'>\r\n    <td align=right id='tottunjlistrik" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id62, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjlain" . $no . "' value='22'>\r\n    <td align=right id='tottunjlain" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id22, 0, ',', ',') . "</td>\r\n\r\n<input type=hidden id='komponentunjrapel" . $no . "' value='54'>\r\n    <td align=right id='tottunjrapel" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($id54, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponengross" . $no . "' value='111'>\r\n    <td align=right id='totgrosstetap" . $no . "' style=\"background-color:lightblue;font-weight:bold\">" . number_format($ttlid, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkk" . $no . "' value='6'>\r\n    <td align=right id='tottunjjkk" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkk, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjjkm" . $no . "' value='7'>\r\n    <td align=right id='tottunjjkm" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($jkm, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjbpjskes" . $no . "' value='57'>\r\n    <td align=right id='tottunjbpjskes" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($bpjspt, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponentunjtetaptidaktetap" . $no . "' value='1'>\r\n    <td align=right id='tottunjtetaptidaktetap" . $no . "' style=\"background-color:lightgrey;font-weight:bold\">" . number_format($totgross, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotbiayajab" . $no . "' value='66'>\r\n    <td align=right id='totpotbiayajab" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlpotbyjab, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjhtkar" . $no . "' value='5'>\r\n    <td align=right id='totpotjhtkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jhtkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotjpkar" . $no . "' value='9'>\r\n    <td align=right id='totpotjpkar" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($jpkarypersen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotpph21" . $no . "' value='24'>\r\n    <td align=right id='totpotpph21" . $no . "' style=\"background-color:lightcyan;font-weight:bold\">" . number_format($ttlid2, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotkoperasi" . $no . "' value='25'>\r\n    <td align=right id='totpotkoperasi" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid38, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotvop" . $no . "' value='52'>\r\n    <td align=right id='totpotvop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid34, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotmotor" . $no . "' value='10'>\r\n    <td align=right id='totpotmotor" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid39, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotlaptop" . $no . "' value='11'>\r\n    <td align=right id='totpotlaptop" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($ttlid35, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotdenda" . $no . "' value='64'>\r\n    <td align=right id='totpotdenda" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandenda, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotdendapanen" . $no . "' value='26'>\r\n    <td align=right id='totpotdendapanen" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potongandendapanen, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenpotbpjskes" . $no . "' value='8'>\r\n    <td align=right id='totpotbpjskes" . $no . "' style=\"background-color:#efc6b1;font-weight:bold\">" . number_format($potonganbpjskes, 0, ',', ',') . "</td>\r\n   \r\n    <input type=hidden id='komponenpotall" . $no . "' value='888'>\r\n    <td align=right id='totpotall" . $no . "' style=\"background-color:#efc6b1\">" . number_format($totpot, 0, ',', ',') . "</td>\r\n\r\n    <input type=hidden id='komponenthpbersih" . $no . "' value='777'>\r\n    <td align=right id='totthpbersih" . $no . "' style=\"background-color:#55fc7f;font-weight:bold\">" . number_format($thpnett, 0, ',', ',') . "</td>                       \r\n                        \r\n    </tr>";
			echo '</tbody><tfoot></tfoot></table>';


		}
	}
}
