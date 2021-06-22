<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = $param['periode'] . '-28';

	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi where ' . "\r\n" . ' kodeorg =\'' . $_SESSION['empl']['lokasitugas'] . '\' and tutupbuku=0';
	$tgmulai = '';
	$tgsampai = '';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: Tidak ada periode akuntansi untuk induk ' . $_SESSION['empl']['lokasitugas']);
	}

	while ($bar = mysql_fetch_object($res)) {
		$tgsampai = $bar->tanggalsampai;
		$tgmulai = $bar->tanggalmulai;
	}

	if (($tgmulai == '') || ($tgsampai == '')) {
		exit('Error: Periode akuntasi tidak terdaftar');
	}

	$group = 'WS1';
	$str = 'select noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '  where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk WS1');
	}
	else {
		$bar = mysql_fetch_object($res);
		$akunalok = $bar->noakunkredit;
	}

	$totjam= "select sum(downtime) as jlh from 
				vhc_penggantianht 
				where posting = 1
				and kodeorg = '".$param['kodeorg']."'   
				and tanggal >= '".$tgmulai."' AND tanggal<='".$tgsampai."' 
				";

	$resjam = mysql_query($totjam);
	$totjmljam=mysql_fetch_assoc($resjam);
	$totjmlhjam=$totjmljam['jlh'];

	$str= "select a.kodevhc, sum(downtime) as jlh, b.jenisvhc, detailvhc, c.noakun from 
				vhc_penggantianht a left join vhc_5master b on a.kodevhc=b.kodevhc left join vhc_5jenisvhc c ON b.jenisvhc=c.jenisvhc 
				where a.posting = 1
				and a.kodeorg = '".$param['kodeorg']."'
				and a.tanggal >= '".$tgmulai."' AND a.tanggal<='".$tgsampai."' 
				group by a.kodevhc";

	$res = mysql_query($str);

	$jmlhrow=mysql_num_rows($res);
	
	if($jmlhrow>0){																						
		
	$kodeJurnal = $group;
	$tgmulaid = $tanggal;
	$pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

	$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','".$param['jumlah']."','".(-1 * $param['jumlah'])."','0','ALK_BY_WS','1','IDR','0','1')";
	echo $qryht;
	$head=mysql_query($qryht);

	$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";

	$upcount=mysql_query($upcounter);

	$x=1;
	$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .":Biaya Bengkel".$param['kodevhc']."','".(-1 * $param['jumlah'])."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '','','','','','', 'ALK_BY_WS','','".$param['kodevhc']."','','','')";

	$dt=mysql_query($qrydt);

	
	while ($bar = mysql_fetch_array($res)) {
	$x++;
	$nilai=$param['jumlah']/$totjmlhjam*$bar['jlh'];
	$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$bar['noakun']."','".$param['periode'] .":Biaya Bengkel ".$bar['kodevhc']." ".$bar['detailvhc']."','".round($nilai,2)."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '".$param['jenispekerjaan']."','','','','','', 'ALK_BY_WS','','".$bar['kodevhc']."','','".$bar['alokasibiaya']."','')";
	$dt=mysql_query($qrydt);	

	}
	
	}
?>
