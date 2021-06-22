<?php


function prosesByBengkel()
{
	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$group = 'WS2';
	$str = 'select noakundebet,noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '          where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk WS2');
	}
	else {
		$akundebet = '';
		$akunkredit = '';
		$bar = mysql_fetch_object($res);
		$akundebet = $bar->noakundebet;
		$akunkredit = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\' ');
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tanggal) . '/' . $_SESSION['empl']['lokasitugas'] . '/' . $kodeJurnal . '/' . $konter;
	$dataRes['header'] = array('nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_BY_WS', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0');
	$noUrut = 1;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akundebet, 'keterangan' => 'Biaya Bengkel/Reparasi ' . $param['kodevhc'], 'jumlah' => $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_BY_WS', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$dataRes['detail'][] = array('nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $akunkredit, 'keterangan' => 'Alokasi biaya bengkel ke ' . $param['kodevhc'], 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_BY_WS', 'noaruskas' => '', 'kodevhc' => $param['kodevhc'], 'nodok' => '', 'kodeblok' => '', 'revisi' => '0');
	++$noUrut;
	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header WS Error : ' . mysql_error() . "\n";
	}

	if ($headErr == '') {
		$detailErr = '';

		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
			if (!mysql_query($insDet)) {
				$detailErr .= 'Insert Detail WS Error : ' . mysql_error() . "\n";
				break;
			}
		}

		if ($detailErr == '') {
			$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', array('nokounter' => $konter), 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and kodekelompok=\'' . $kodeJurnal . '\'');

			if (!mysql_query($updJurnal)) {
				echo 'Update Kode Jurnal Error : ' . mysql_error() . "\n";
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : ' . mysql_error() . "\n";
					exit();
				}

				exit();
			}
		}
		else {
			echo $detailErr;
			$RBDet = deleteQuery($dbname, 'keu_jurnalht', 'nojurnal=\'' . $nojurnal . '\'');

			if (!mysql_query($RBDet)) {
				echo 'Rollback Delete Header Error : ' . mysql_error();
				exit();
			}
		}
	}
	else {
		echo $headErr;
		exit();
	}
}

function prosesAlokasi()
{
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

	$group = 'VHC1';
	$str = 'select noakundebet from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '  where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk VHC1');
	}
	else {
		$bar = mysql_fetch_object($res);
		$akunalok = $bar->noakundebet;
	}

	$totjam = 'select sum(a.jumlah) as jlh from ' . $dbname . '.vhc_rundt a' . "\r\n" . '            left join ' . $dbname . '.vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan' . "\r\n" . '            left join ' . $dbname . '.vhc_runht c on a.notransaksi=c.notransaksi     ' . "\r\n" . '            where c.kodevhc=\'' . $param['kodevhc'] . '\'' . "\r\n" . '            and c.tanggal>=\'' . $tgmulai . '\' and c.tanggal <=\'' . $tgsampai . '\' ';
	$resjam = mysql_query($totjam);
	$totjmljam=mysql_fetch_assoc($resjam);
	$totjmlhjam=$totjmljam['jlh'];

	$str = "select sum(a.jumlah) as jlh,a.alokasibiaya,b.noakun, jenispekerjaan from ".$dbname.".vhc_rundt a left join ".$dbname.".vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan left join ".$dbname.".vhc_runht c on a.notransaksi=c.notransaksi where c.kodevhc='".$param['kodevhc']."' and c.tanggal>='".$tgmulai."' and c.tanggal <='".$tgsampai."' and alokasibiaya!='' and jenispekerjaan!='' and c.kodeorg='".$_SESSION['empl']['lokasitugas']."' group by jenispekerjaan,noakun,alokasibiaya";
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

	$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','".$param['jumlah']."','".(-1 * $param['jumlah'])."','0','ALK_KERJA_AB','1','IDR','0','1')";
	$head=mysql_query($qryht);

	$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";

	$upcount=mysql_query($upcounter);

	$x=1;
	$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .":Biaya Kendaraan".$param['kodevhc']."','".(-1 * $param['jumlah'])."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '','','','','','', 'ALK_KERJA_AB','','".$param['kodevhc']."','','','')";

	$dt=mysql_query($qrydt);

	
	while ($bar = mysql_fetch_array($res)) {
	$x++;
	$nilai=($param['jumlah']/$totjmlhjam)*$bar['jlh'];
	$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$bar['noakun']."','".$param['periode'] .":Biaya Kendaraan".$param['kodevhc']."','".$nilai."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '".$param['jenispekerjaan']."','','','','','', 'ALK_KERJA_AB','','".$param['kodevhc']."','','".$bar['alokasibiaya']."','')";
	$dt=mysql_query($qrydt);	

	}
	
	}

	$sql="UPDATE keu_jurnaldt a , (select nojurnal,(debet-kredit) AS selisih from keu_jurnal_tidak_balance_vw) b SET jumlah=(jumlah-selisih) WHERE a.nojurnal=b.nojurnal AND nourut=2";
	$qr=mysql_query($sql);

}
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = $param['periode'] . '-28';

if ($param['jenis'] == 'BYWS') {
	$str = 'select distinct nojurnal from ' . $dbname . '.keu_jurnaldt where noreferensi=\'ALK_BY_WS\'' . "\r\n" . '          and kodevhc=\'' . $param['kodevhc'] . '\' and tanggal=\'' . $tanggal . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$str = 'delete from ' . $dbname . '.keu_jurnalht where nojurnal=\'' . $bar->nojurnal . '\'';
		mysql_query($str);
	}

	prosesByBengkel();
}
else {
	$str = 'select distinct nojurnal from ' . $dbname . '.keu_jurnaldt where noreferensi=\'ALK_KERJA_AB\'' . "\r\n" . '          and kodevhc=\'' . $param['kodevhc'] . '\' and tanggal=\'' . $tanggal . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$str = 'delete from ' . $dbname . '.keu_jurnalht where nojurnal=\'' . $bar->nojurnal . '\'';
		mysql_query($str);
	}

	prosesAlokasi();
}

?>
