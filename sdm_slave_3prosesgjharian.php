<?php
ini_set('max_execution_time', '600');
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
include_once 'config/connection.php';

$oprname= '';
$oprtype= '';
$cekuser = "select a.uname, a.type FROM sdm_ho_payroll_user a JOIN user b ON a.uname = b.namauser WHERE b.karyawanid = '".$_SESSION['standard']['userid']."' LIMIT 1";
$qcekuser = mysql_query($cekuser);
$rowuser = mysql_fetch_assoc($qcekuser);
if (mysql_num_rows($qcekuser) > 0) {
	$oprname= $rowuser['uname'];
	$oprtype= $rowuser['type'];
	$proses = $_GET['proses'];
	$param = $_POST;
	$id = [];
	$nik = [];
	$tgllahir = [];
	$namakar = [];
	$kdjabatan = [];
	$tipekar = [];
	$namatipe = [];
	$kdgol = [];
	$sCekPeriode = 'select distinct * from '.$dbname.".sdm_5periodegaji where periode='".$param['periodegaji']."'\r\n              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='H'";
	$qCekPeriode = mysql_query($sCekPeriode);
	if (0 < mysql_num_rows($qCekPeriode)) {
		$aktif2 = false;
	} else {
		$aktif2 = true;
	}

	if (!$aktif2) {
		exit(' Payroll period has been closed / '.$sCekPeriode);
	}

	$str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
	$res = mysql_query($str);
	if (0 < mysql_num_rows($res)) {
		$aktif = false;
	} else {
		$aktif = true;
	}

	if (!$aktif) {
		exit('Accounting period has been closed / ');
	}

	$qPeriod = selectQuery($dbname, 'sdm_5periodegaji', 'tanggalmulai,tanggalsampai', "periode='".$param['periodegaji']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='H'");
	$resPeriod = fetchData($qPeriod);
	$tanggal1 = $resPeriod[0]['tanggalmulai'];
	$tanggal2 = $resPeriod[0]['tanggalsampai'];
	$str = 'delete from '.$dbname.".kebun_aktifitas where notransaksi like '%//%'";
	mysql_query($str);
	//tambah periode bpjskes 04102020
	$query1 = "SELECT a.karyawanid, nik, namakaryawan, tanggallahir, jms, firstvol, lastvol, tanggalmasuk, tanggalkeluar, statuspajak, a.npwp, kodejabatan, tipekaryawan, kodegolongan, idmedical,COALESCE(ROUND(DATEDIFF('".date("Y")."-12-31',tanggalmasuk)/365.25,3),0) as masakerja,COALESCE(ROUND(DATEDIFF(tanggalkeluar,tanggalmasuk)/365.25,3),0) as lamakerja,COALESCE(ROUND(DATEDIFF(tanggalkeluar,'".date("Y")."-01-01')/365.25,3),0) as lamakerjathnini, jmsstart from ".$dbname.".datakaryawan a RIGHT JOIN ".$dbname.".sdm_ho_employee b on (a.karyawanid = b.karyawanid) where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar >= '".$tanggal1."' or tanggalkeluar is NULL or tanggalkeluar='0000-00-00') and (tanggalmasuk <= '".$tanggal2."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null) and sistemgaji='Harian' AND a.isduplicate = '0'";
	if ($oprtype == 'operator'){
		$query1 = $query1." and b.operator = '".$oprname."'";
	} else {
		$query1 = $query1." and a.karyawanid != '".$_SESSION['standard']['userid']."'";
	}
	$query1 = $query1." order by karyawanid";

//echo 'warning: '.$query1;
//exit();

	/*
	$resz = mysql_query($query1);
	$barz = mysql_fetch_assoc($resz);
	echo 'warning: '.$barz['namakaryawan']."/lamakerja: ".$barz['lamakerja']."/masakerja: ".$barz['masakerja'];
	exit();
	*/
	
	mysql_query($query1);
	$absRes = fetchData($query1);
	if (empty($absRes)) {
		echo 'Error : Tidak ada daftar Kehadiran di Periode Payroll ini';
		exit();
	}

	$id = [];
	foreach ($absRes as $row => $kar) {
		$id[$kar['karyawanid']][] = $kar['karyawanid'];
		$nik[$kar['karyawanid']] = $kar['nik'];
		$namakar[$kar['karyawanid']] = $kar['namakaryawan'];
		$tgllahir[$kar['karyawanid']] = $kar['tanggallahir'];
		$kdjabatan[$kar['karyawanid']] = $kar['kodejabatan'];
		$kdgol[$kar['karyawanid']] = $kar['kodegolongan'];
		$tipekar[$kar['karyawanid']] = $kar['tipekaryawan'];
		$nojms[$kar['karyawanid']] = trim($kar['jms']);
		$firstvol[$kar['karyawanid']] = $kar['firstvol'];
		$lastvol[$kar['karyawanid']] = $kar['lastvol'];
		$tanggalmasuk[$kar['karyawanid']] = $kar['tanggalmasuk'];
		if( substr($param['periodegaji'],5,2) == "12"){  //kalo desember dianggap resign
			$flag_desember = "Y";
		}else{
			$flag_desember = "N";
		}
		$tanggalkeluar[$kar['karyawanid']] = $kar['tanggalkeluar'];
		$nobpjskes[$kar['karyawanid']] = trim($kar['idmedical']);
		$nobpjs = $nobpjskes[$kar['karyawanid']];
		$perbpjskes[$kar['karyawanid']] = trim($kar['jmsstart']); //tambah periode bpjs 04102020

		// FA-20190412 cek jika isi no bpjs-nya ngasal		
		//if ((strlen($nobpjs) != 13) || (substr($nobpjs,0,3) != '000')){
		if (strlen($nobpjs) != 13){
			$nobpjskes[$kar['karyawanid']] = ''; // FA-20190412 cek jika isi no bpjs-nya ngasal
		}
		
		$statuspajak[$kar['karyawanid']] = trim($kar['statuspajak']);
		$npwp[$kar['karyawanid']] = trim($kar['npwp']);
		$masakerja[$kar['karyawanid']] = $kar['masakerja'];
		$lamakerja[$kar['karyawanid']] = $kar['lamakerja'];		
		$lamakerjathnini[$kar['karyawanid']] = $kar['lamakerjathnini']; //dipake untuk yang resign
	}
	
	// FA 20200226
	$hari_bagi= 30;
	$loktugas= $_SESSION['empl']['lokasitugas'];
	// dibawah ini utk HO dan Pabrik
	// Extreme hardcode
	if ($loktugas == 'LSPM' || $loktugas == 'SSPM' || $loktugas == 'MPSM'){
		$hari_bagi= 25;
	}
	if ($loktugas == 'LSPH' || $loktugas == 'SSPH' || $loktugas == 'SPSH' || $loktugas == 'TSPH'){
		$hari_bagi= 21;
	}
	if ($loktugas == 'CD1H' || $loktugas == 'CD2H' || $loktugas == 'MPSH' || $loktugas == 'SH1H' || $loktugas == 'SH2H'){
		$hari_bagi= 21;
	}
	// ------------
	/*
	$strgjh = 'select a.karyawanid,sum(jumlah)/'.$hari_bagi.' as gjperhari from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid where a.tahun='.substr($tanggal1, 0, 4)." and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.idkomponen in(1,2,3,4,15,21,29,30,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51) and sistemgaji='Harian' group by a.karyawanid ";
	
	$strgjh = 'select a.karyawanid,sum(jumlah)/'.$hari_bagi.' as gjperhari from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid where a.tahun='.substr($tanggal1, 0, 4)." and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.idkomponen in (1,2,15,61) and sistemgaji='Harian' group by a.karyawanid "; */
	
	
	//FA-03102020 gaji harian berdasarkan kepada gaji pokok dan tunjungan jabatan
	$strgjh = 'select a.karyawanid,sum(jumlah)/'.$hari_bagi.' as gjperhari from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid where a.tahun='.substr($tanggal1, 0, 4)." and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.idkomponen in (1,2) and sistemgaji='Harian' group by a.karyawanid ";
	$resgjh = fetchData($strgjh);
	foreach ($resgjh as $idx => $val) {
		$gajiperhari[$val['karyawanid']] = $val['gjperhari'];
	}
	
	// Potongan HK (ID: 20) ke-1, masuk ke Potongan Absen harusnya (ID: 64)
	// untuk Mangkir nya Non Mill
	$strgjh = 'select  count(*) as jlh,b.karyawanid from '.$dbname.".sdm_hktdkdibayar_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00' or b.tanggalkeluar='0000-00-00') and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' and a.absensi != 'M2' group by a.karyawanid";
	$tdkdibayar = [];
	$resgjh = fetchData($strgjh);
	foreach ($resgjh as $idx => $val) {
		$pengali= 1;
		$tdkdibayar[$val['karyawanid']] = $gajiperhari[$val['karyawanid']] * $val['jlh'] * $pengali;
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => 64, 'jumlah' => $tdkdibayar[$val['karyawanid']], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
	}
	// untuk Mangkir nya Mill, ada MPSM yg berbeda
	$strgjh = 'select  count(*) as jlh,b.karyawanid from '.$dbname.".sdm_hktdkdibayar_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00' or b.tanggalkeluar='0000-00-00') and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' and a.absensi= 'M2' group by a.karyawanid";
	$tdkdibayar = [];
	$resgjh = fetchData($strgjh);
	foreach ($resgjh as $idx => $val) {
		$pengali= 2;
		$tdkdibayar[$val['karyawanid']] = $gajiperhari[$val['karyawanid']] * $val['jlh'] * $pengali;
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => 64, 'jumlah' => $tdkdibayar[$val['karyawanid']], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
	}
	
	$str = 'select tipe from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
	$res = mysql_query($str);
	$tip = '';
	while ($bar = mysql_fetch_object($res)) {
		$tip = $bar->tipe;
	}
	$str1 = 'select distinct a.*,b.namakaryawan,b.tipekaryawan,b.bagian,b.tanggallahir from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid where a.tahun='.substr($tanggal2, 0, 4)." and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00' or b.tanggalkeluar='0000-00-00') and b.sistemgaji='Harian' order by b.karyawanid,idkomponen";
	$res1 = fetchData($str1);

    $tjms = [];
    $tipekaryawan = [];
    $bagiankaryawan = [];
    $tanggallahir = [];	
    foreach ($res1 as $idx => $val) {
    	if ($id[$val['karyawanid']][0] == $val['karyawanid']) {
			$tanggallahir[$val['karyawanid']] = $val['tanggallahir']; //FA 20190413 - ambil tanggal lahir karyawan			

			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => $val['idkomponen'], 'jumlah' => $val['jumlah'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];

			/*
			// Gapok, lembur dan Semua Tunjangan
			if ((1 == $val['idkomponen'] || 2 == $val['idkomponen'] || 3 == $val['idkomponen'] || 4 == $val['idkomponen'] || 15 == $val['idkomponen'] || 17 == $val['idkomponen'] || 21 == $val['idkomponen'] || 22 == $val['idkomponen'] || 23 == $val['idkomponen'] || 29 == $val['idkomponen'] || 30 == $val['idkomponen'] || 32 == $val['idkomponen'] || 33 == $val['idkomponen'] || 35 == $val['idkomponen'] || 36 == $val['idkomponen'] || 37 == $val['idkomponen'] || 38 == $val['idkomponen'] || 39 == $val['idkomponen'] || 40 == $val['idkomponen'] || 41 == $val['idkomponen'] || 42 == $val['idkomponen'] || 43 == $val['idkomponen'] || 44 == $val['idkomponen'] || 45 == $val['idkomponen'] || 46 == $val['idkomponen'] || 47 == $val['idkomponen'] || 48 == $val['idkomponen'] || 49 == $val['idkomponen'] || 50 == $val['idkomponen'] || 51 == $val['idkomponen'] || 54 == $val['idkomponen'] || 58 == $val['idkomponen'] || 59 == $val['idkomponen'] || 60 == $val['idkomponen'] || 61 == $val['idkomponen'] || 62 == $val['idkomponen'] || 63 == $val['idkomponen'] || 65 == $val['idkomponen']) && '' != $nojms[$val['karyawanid']]) {
				$tjms[$val['karyawanid']] += $val['jumlah'];				
			}
			*/

			// hanya ambil dari Gapok dan tunj. jabatan saja (idkomponen= 1 & 2)
			if ($val['idkomponen'] == 1 || $val['idkomponen'] == 2) {
				$sql = "select b.isharian from datakaryawan a inner join sdm_5tipekaryawan b on a.tipekaryawan=b.id 
					where a.karyawanid=".$val['karyawanid'];
				$qstr = mysql_query($sql);
				$rstr = mysql_fetch_assoc($qstr);

				// Jika harian maka tdk dpt BPJS -> WPG
				if ($rstr['isharian']==0){
					$tjms[$val['karyawanid']] += $val['jumlah'];				
				} else {
					$tjms[$val['karyawanid']] = 0;
				}
			}
		}
	}
	
	/*
	$query6 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan'");
	$jmsRes = fetchData($query6);
	$query7 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan'");
	$jmspersh = fetchData($query7);
	$query8 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='usiapensiun'");
	$uspen = fetchData($query8);
	$usiapen = $uspen[0]['value'] / 100;
	*/
	
	foreach ($tjms as $key => $nilai) {
		if ('H' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			// ekstreme hardcode - FA 20200212 , WPG: SSP & LSP, MPSG: MPS
			if (substr($_SESSION['empl']['lokasitugas'],0,3)=='LSP' || substr($_SESSION['empl']['lokasitugas'],0,3)=='SSP' || substr($_SESSION['empl']['lokasitugas'],0,3)=='MPS'){
				$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='M'");
				$querypersenlokres1 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan' and lokasiresiko='M'");
			} else {
				$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
				$querypersenlokres1 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan' and lokasiresiko='E'");
			}
			// ----------------------------------------------------------
		}

		if ('E' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
			$querypersenlokres1 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan' and lokasiresiko='E'");
		}

		if ('M' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='M'");
			$querypersenlokres1 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan' and lokasiresiko='M'");
		}

		if ('R' == substr($_SESSION['empl']['lokasitugas'], -1)) {
			$querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='R'");
			$querypersenlokres1 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan' and lokasiresiko='R'");
		}

		$angkapersenlokres = fetchData($querypersenlokres);
		$persenjkkpt = $angkapersenlokres[0]['jkkpt'] / 100;
		$persenjkmpt = $angkapersenlokres[0]['jkmpt'] / 100;
		// ------
		$persenbpjspt = $angkapersenlokres[0]['bpjspt'] / 100;
		$persenjhtpt = $angkapersenlokres[0]['jhtpt'] / 100;
		$persenjppt = $angkapersenlokres[0]['jppt'] / 100;
		
		$angkapersenlokres1 = fetchData($querypersenlokres1);
		$persenbpjskar = $angkapersenlokres1[0]['bpjskar'] / 100;
		$persenjhtkar = $angkapersenlokres1[0]['jhtkar'] / 100;
		$persenjpkar = $angkapersenlokres1[0]['jpkar'] / 100;
		$maksbpjskar = $querypersenlokres1[0]['bpjsmk'];
		$maksbpjspt = $querypersenlokres[0]['bpjsmpt'];
		// Mulai hitungan TK

		$isGolTemp= 0; // jika dia masuk ke golongan rate khusus
		/* cek di BPJS TK */
		$sqlq = "select * from sdm_5bpjstk_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and komponenid=5 
		and substr(a.kodegolongan,2,1)= 'X'";
		$qstrq = mysql_query($sqlq);
		if (mysql_num_rows($qstrq)>0){$isGolTemp= 1;}
		/* cek di BPJS KESEHATAN */
		$sqlq = "select * from sdm_5bpjs_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and komponenid=8 
		and substr(a.kodegolongan,2,1)= 'X'";
		$qstrq = mysql_query($sqlq);
		if (mysql_num_rows($qstrq)>0){$isGolTemp= 1;}
		
		$jkkpt= 0;
		$jkmpt= 0;
		if ($nojms[$key] != '-' && $nojms[$key] != ''){
			//FA-20200405 -> cek usia karyawan
			list($year,$month,$day)= explode("-",$tanggallahir[$key]);
			$year_diff= date("Y") - $year;
			$month_diff= date("m") - $month;
			$day_diff= date("d") - $day;
			if ($month_diff < 0){
				//$year_diff--;
			} else {
				if (($month_diff==0) && ($day_diff < 0)){
					//$year_diff--;
					$month_diff--;
				}
			}
			$gabthnbln= $year_diff + ($month_diff/12);
			// ----------------------------------------
			
			/* Jika ada yg berbeda antara gapok dgn dasar perhitungan BPJS TK 
			   ambil dari sdm_bpjstk nominal lain - FA 20200224*/
			$nilaix= 0;
			$datex= substr($param['periodegaji'],-2)."/01/".substr($param['periodegaji'],0,4);
			$timex = strtotime($datex);
			$ddatex = date('Y-m-d',$timex);
			$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
			$qstr = mysql_query($sql);
			if (mysql_num_rows($qstr)>0){
				$rstr = mysql_fetch_assoc($qstr);
				$nilaix= $rstr['nominal'];
			} else {
				if ($isGolTemp== 0){$nilaix= $nilai;} 
				else {$nilaix= 0;}
			}
			
			//FA-20200405
			// Jika umur kurang dari 57 tahun 1 bulan, lanjut. Jika lebih, jumlah => 0 (Khusus JHT) -> GAK JADI
			if ($gabthnbln < 99) {
				// JHT Karyawan
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => $nilaix * $persenjhtkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				// JHT Perusahaan
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 55, 'jumlah' => $nilaix * $persenjhtpt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			} else {
				// JHT Karyawan
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				// JHT Perusahaan
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 55, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			}
			
			$nilaix= 0;
			$datex= substr($param['periodegaji'],-2)."/01/".substr($param['periodegaji'],0,4);
			$timex = strtotime($datex);
			$ddatex = date('Y-m-d',$timex);
			$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=6";
			$qstr = mysql_query($sql);
			if (mysql_num_rows($qstr)>0){
				$rstr = mysql_fetch_assoc($qstr);
				$nilaix= $rstr['nominal'];
			} else {
				if ($isGolTemp== 0){$nilaix= $nilai;} 
				else {$nilaix= 0;}
			}

			$jkkpt= round($nilaix * $persenjkkpt);
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 6, 'jumlah' => $jkkpt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			
			$nilaix= 0;
			$datex= substr($param['periodegaji'],-2)."/01/".substr($param['periodegaji'],0,4);
			$timex = strtotime($datex);
			$ddatex = date('Y-m-d',$timex);
			$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=7";
			$qstr = mysql_query($sql);
			if (mysql_num_rows($qstr)>0){
				$rstr = mysql_fetch_assoc($qstr);
				$nilaix= $rstr['nominal'];
			} else {
				if ($isGolTemp== 0){$nilaix= $nilai;} 
				else {$nilaix= 0;}
			}
			$jkmpt= round($nilaix * $persenjkmpt);
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 7, 'jumlah' => $jkmpt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			
			// id komponen 9: Pot.BPJS Pensiun
			// Jika umur kurang dari 57 tahun 1 bulan, lanjut. Jika lebih, jumlah => 0 -- ada di sdm_ho_jms_porsi : usiapensiun
			if ($gabthnbln < 57.01) {
				$nilaix= 0;
				$datex= substr($param['periodegaji'],-2)."/01/".substr($param['periodegaji'],0,4);
				$timex = strtotime($datex);
				$ddatex = date('Y-m-d',$timex);
				$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=9";
				$qstr = mysql_query($sql);
				if (mysql_num_rows($qstr)>0){
					$rstr = mysql_fetch_assoc($qstr);
					$nilaix= $rstr['nominal'];
				} else {
					if ($isGolTemp== 0){$nilaix= $nilai;} 
					else {$nilaix= 0;}
				}
				// JP Karyawan - field: jmpk - maksimal jaminan pensiun karyawan, di tabel sdm_ho_jms_porsi
				if ($param['periodegaji'] < '2020-03'){
					$nilaibatasmax = 8512400;
				} else {
					$nilaibatasmax = 8939700;
				}
				if ($nilaibatasmax < $nilaix) {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilaibatasmax * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				} else {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilaix * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				}
				// JP Perusahaan - field: jmppt - maksimal jaminan pensiun perusahaan, di tabel sdm_ho_jms_porsi
				if ($param['periodegaji'] < '2020-03'){
					$nilaibatasmaxpt = 8512400;
				} else {
					$nilaibatasmaxpt = 8939700;
				}
				if ($nilaibatasmaxpt < $nilaix) {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 56, 'jumlah' => $nilaibatasmaxpt * $persenjppt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				} else {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 56, 'jumlah' => $nilaix * $persenjppt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				}
			} else {
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 56, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			}
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 6, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 7, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 55, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 56, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		} // akhir hitungan BPJS TK
		
		// mulai hitungan BPJS Kesehatan
		$isKes= 0;
		$sqlc = "select * from datakaryawan a where a.karyawanid=".$key;
		$qstrc = mysql_query($sqlc);
		if (mysql_num_rows($qstrc)>0){
			$rstrc = mysql_fetch_assoc($qstrc);
			if ($rstrc['idmedical'] != '-' && $rstrc['idmedical'] != ''){
				$isKes = 1;
			}
		}
		if ($isKes==1){

			//Jika ada yg berbeda antara gapok dgn dasar perhitungan BPJS KES, ambil dari sdm_bpjs nominal lain. -> FA 20200212
			$nilaix= 0;
			$datex= substr($param['periodegaji'],-2)."/01/".substr($param['periodegaji'],0,4);
			$timex = strtotime($datex);
			$ddatex = date('Y-m-d',$timex);
			$sql = "select nominal from sdm_5bpjs_nominallain where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$key.") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=8";
			$qstr = mysql_query($sql);
			if (mysql_num_rows($qstr)>0){
				$rstr = mysql_fetch_assoc($qstr);
				$nilaix= $rstr['nominal'];
			} else {
				if ($isGolTemp== 0){$nilaix= $nilai;} 
				else {$nilaix= 0;}
			}
			// ------------------

			// id komponen 8: BPJS Kes last 04102020
			$perbpjskes2 = $perbpjskes[$key];
			if($perbpjskes2 == ''){
				$perbpjskes2 = $param['periodegaji'];
			}
			$perbpjskes2 = $perbpjskes2 .'-01';
			$pergaji = $param['periodegaji'].'-01';
			if ('' != $nobpjskes[$key]  AND $perbpjskes2 <= $pergaji) {
				$nilaibatasmax2 = 12000000;  //kenapa harus hardcode, padahal ada di tabel sdm_ho_hr_jms_porsi, kolom bpjsmpt
				if ($nilaibatasmax2 < $nilaix) {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilaibatasmax2 * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				} else {
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilaix * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				}
			} else {
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			}
			// id komponen 57: Tunj.BPJS Kes 04102020
			$bpjsptx= 0;
			$nilaibatasmax3 = 12000000; //bpjsmpt - sdm_ho_hr_jms_porsi
			if ('' != $nobpjskes[$key] AND $perbpjskes2 <= $pergaji) {
				if ($nilaibatasmax3 < $nilaix) {
					$bpjsptx= $nilaibatasmax3 * $persenbpjspt;
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $bpjsptx, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				} else {
					$bpjsptx= $nilaix * $persenbpjspt;
					$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $bpjsptx, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
				}
			} else {
				$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			}
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		} // akhir hitungan bpjs kes

		// Dibawah ini kayaknya gak kepake
		$jabPersen = 0;
		$jabMax = 0;
		$str = 'select persen,max from '.$dbname.'.sdm_ho_pph21jabatan';
		$res = mysql_query($str);
		while ($bar = mysql_fetch_object($res)) {
			$jabPersen = $bar->persen / 100;
			$jabMax = $bar->max * 12;
			$jabMax2 = $bar->max;
		}
		/*
		// id komponen 66: biaya jabatan >> ke-1
		//$nilaibatasmax3 = 6000000; // ambil dari sdm_ho_pph21jabatan
		$nilaibatasmax3 = 500000; // ambil dari sdm_ho_pph21jabatan
		if ($nilaibatasmax3 < ($nilai * $jabPersen)) {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $jabMax2, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		} else {
			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => round(($nilai + $jkkpt + $jkmpt + $bpjsptx) * $jabPersen, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		}
		*/
    }

	
	// perhitungan lembur
    $where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."')";
    $query2 = 'select a.karyawanid,sum(a.uangkelebihanjam) as lembur from '.$dbname.".sdm_lemburdt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00' or b.tanggalkeluar='0000-00-00') and sistemgaji='Harian' and ".$where2.' group by a.karyawanid';
    $lbrRes = fetchData($query2);
    foreach ($lbrRes as $idx => $row) {
    	if (isset($id[$row['karyawanid']])) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 17, 'jumlah' => $row['lembur'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }

	// dari tabel potongan detail
    $where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
    $query3 = 'select a.nik as karyawanid,sum(jumlahpotongan) as potongan, a.tipepotongan as tipepotongan from '.$dbname.".sdm_potongandt a left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00' or b.tanggalkeluar='0000-00-00') and sistemgaji='Harian' and ".$where3.' group by a.nik, a.tipepotongan';
    $potRes = fetchData($query3);	
    foreach ($potRes as $idx => $row) {
    	if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
    		if ('25' == $row['tipepotongan']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 25, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('26' == $row['tipepotongan']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 26, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('27' == $row['tipepotongan']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 27, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			
			// Potongan HK ambil dari Input Potongan Manual (sdm_potongandt)- ke 2
    		if ('20' == $row['tipepotongan']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 20, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			
    		if ('19' == $row['tipepotongan']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 19, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			// Potongan Absen 1 - ambil dari input manual (sdm_potongandt)
    		if ('64' == $row['tipepotongan']) {
			$pengali= 1;
			if ($_SESSION['empl']['lokasitugas']=='MPSM'){
				$pengali= 2;
			}
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 64, 'jumlah' => $row['potongan'], 'pengali' => $pengali, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			
    	}
    }

    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    $query4 = 'select a.karyawanid,a.bulanan,a.jenis from '.$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.active=1 and sistemgaji='Harian' and ".$where4;
    $angRes = fetchData($query4);
    foreach ($angRes as $idx => $row) {
    	if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => $row['jenis'], 'jumlah' => $row['bulanan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }

	// dari sdm_gaji
    $where3a = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
    $query3a = 'select a.karyawanid as karyawanid,a.jumlah as potongan, a.idkomponen as idkomponen from '.$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and b.sistemgaji='Harian' and ".$where3a.' ';
    $angResa = fetchData($query3a);
    foreach ($angResa as $idx => $row) {
    	if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
    		if ('12' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 12, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('19' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 19, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			
			// Potongan HK ke-3 -> sdm_gaji
			/*
    		if ('20' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 20, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			*/
			
    		if ('21' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 21, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('22' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 22, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('23' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 23, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('54' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 54, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('58' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 58, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('59' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 59, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('60' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 60, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('61' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 61, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('62' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 62, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('63' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 63, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('65' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 65, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}

    		if ('67' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 67, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
			// 70 - Insentif PPH21 / PPH21 Ditanggung Pemerintah, diluar Bruto (FA 20200329)
    		if ('70' == $row['idkomponen']) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 70, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
    	}
    }
    $stru1 = 'select distinct(tanggal) from '.$dbname.".kebun_kehadiran_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0 and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' order by tanggal";
    $resu1 = mysql_query($stru1);

    $stru2 = 'select distinct(tanggal) from '.$dbname.".kebun_prestasi_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0 and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' order by tanggal";
    $resu2 = mysql_query($stru2);

    $stru3 = "select distinct(tanggal) from ".$dbname.".vhc_runhk_vw a left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and posting=0 and sistemgaji='Harian' order by tanggal";
    $resu3 = mysql_query($stru3);
    if (0 < mysql_num_rows($resu1) || 0 < mysql_num_rows($resu2) || 0 < mysql_num_rows($resu3)) {
    	echo 'Masih ada data yang belum di posting:';
    	echo "<table class=sortable border=0 cellspacing=1>\r\n            <thead><tr class=rowheader>\r\n            <td>".$_SESSION['lang']['jenis']."</td>\r\n            <td>".$_SESSION['lang']['tanggal']."</td>\r\n            </tr></thead><tbody>";
    	while ($bar = mysql_fetch_object($resu1)) {
    		echo '<tr class=rowcontent><td>Perawatan Kebun</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
    	}
    	while ($bar = mysql_fetch_object($resu2)) {
    		echo '<tr class=rowcontent><td>Panen</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
    	}
    	while ($bar = mysql_fetch_object($resu3)) {
    		echo '<tr class=rowcontent><td>Traksi Pekerjaan</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
    	}
    	echo '</tbody><tfoot></tfoot></table>';
    	exit();
    }

    $upah_harian = []; // upah harian masukin ke komponen gaji Tunjangan Harian - FA20200407
	$premi = [];
    $penalty = [];
    $penaltykehadiran = [];

    $query5 = 'select a.karyawanid,sum(a.insentif * a.hasilkerja) as premi 
	from '.$dbname.".kebun_kehadiran_vw a 
	left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan = c.id
	where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
	and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') 
	and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
	and sistemgaji='Harian' group by a.karyawanid";
    $premRes = fetchData($query5);
    foreach ($premRes as $idx => $val) {
    	if (0 < $val['premi']) {
    		$premi[$val['karyawanid']] = $val['premi'];
    	}
    }

    $query5a = 'select a.karyawanid,sum(umr) as upah_harian
	from '.$dbname.".kebun_kehadiran_vw a 
	left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan = c.id
	where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
	and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') 
	and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
	and sistemgaji='Harian' and c.isharian=1 group by a.karyawanid";
    $uph = fetchData($query5a);
    foreach ($uph as $idx => $val) {
    	if (0 < $val['upah_harian']) {
    		$upah_harian[$val['karyawanid']] = $val['upah_harian'];
    	}
    }

    $query6 = "select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty from ".$dbname.".kebun_prestasi_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' group by a.karyawanid";
    $premRes1 = fetchData($query6);
    foreach ($premRes1 as $idx => $val) {
    	if (0 < $val['premi']) {
    		if (isset($premi[$val['karyawanid']])) {
    			$premi[$val['karyawanid']] += $val['premi'];
    		} else {
    			$premi[$val['karyawanid']] = $val['premi'];
    		}
    	}

    	if (0 < $val['penalty']) {
    		$penalty[$val['karyawanid']] = $val['penalty'];
    	}
    }

    $query7 = "select a.idkaryawan as karyawanid,sum(a.upah+a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty\r\n               from ".$dbname.".vhc_runhk_vw a left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and sistemgaji='Harian' group by a.idkaryawan";
    $premRes2 = fetchData($query7);
    foreach ($premRes2 as $idx => $val) {
    	if (0 < $val['premi']) {
    		if (isset($premi[$val['karyawanid']])) {
    			$premi[$val['karyawanid']] += $val['premi'];
    		} else {
    			$premi[$val['karyawanid']] = $val['premi'];
    		}
    	}

    	if (0 < $val['penalty']) {
    		if (isset($penalty[$val['karyawanid']])) {
    			$penalty[$val['karyawanid']] += $val['penalty'];
    		} else {
    			$penalty[$val['karyawanid']] = $val['penalty'];
    		}
    	}
    }

    $query8 = "select sum(a.premiinput) as premi,a.karyawanid,a.tanggal from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and b.sistemgaji='Harian' and a.posting=1 group by a.karyawanid";
    $premRes2 = fetchData($query8);
    foreach ($premRes2 as $idx => $val) {
    	if (0 < $val['premi']) {
    		if (isset($premi[$val['karyawanid']])) {
    			$premi[$val['karyawanid']] += $val['premi'];
    		} else {
    			$premi[$val['karyawanid']] = $val['premi'];
    		}
    	}
    }
	
	// tidak ada premi dan insentif (kosong) disini
    $stkh = 'select a.karyawanid,sum(a.premi+a.insentif) as premi from '.$dbname.".sdm_absensidt a\r\n                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
    $reskh = mysql_query($stkh);
    while ($barky = mysql_fetch_object($reskh)) {
    	if (isset($premi[$barky->karyawanid])) {
    		$premi[$barky->karyawanid] += $barky->premi;
    	} else {
    		$premi[$barky->karyawanid] = $barky->premi;
    	}
    }
	// ini tidak dipakai
    $stkh1 = 'select a.karyawanid,a.rupiahpremi  from '.$dbname.".kebun_premipanen a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00') and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and a.periode like  '%".$param['periodegaji']."%' and sistemgaji='Harian' group by a.karyawanid";
    $reskh1 = mysql_query($stkh1);
    while ($barky = mysql_fetch_object($reskh1)) {
    	if (isset($premi[$barky->karyawanid])) {
    		$premi[$barky->karyawanid] += $barky->rupiahpremi;
    	} else {
    		$premi[$barky->karyawanid] = $barky->rupiahpremi;
    	}
    }
    foreach ($premi as $idx => $row) {
    	if (0 < $row) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 16, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }
	// FA 20200407 - masuk ke Tunjangan Harian
    foreach ($upah_harian as $idx => $row) {
    	if (0 < $row) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 21, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }
    foreach ($penalty as $idx => $row) {
    	if (0 < $row) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 26, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }
//pre($readyData);
    $stkh = 'select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran from '.$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL or b.tanggalkeluar='0000-00-00')                and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
    $reskh = mysql_query($stkh);
    while ($barkh = mysql_fetch_object($reskh)) {
    	if (0 < $barkh->penaltykehadiran) {
    		$penaltykehadiran[$barkh->karyawanid] = $barkh->penaltykehadiran;
    	}
    }
	// Potongan Absen - gak dipakai
	/*
    foreach ($penaltykehadiran as $idx => $row) {
    	if (0 < $row) {
    		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 64, 'jumlah' => $row, 'pengali' => $pengali, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    	}
    }
	*/
	
    $strx = "select id as komponen, case plus when 0 then -1 else plus end as pengali,name as nakomp FROM ".$dbname.'.sdm_ho_component';
    $comRes = fetchData($strx);
    $comp = [];
    $nakomp = [];
    foreach ($comRes as $idx => $row) {
    	$comp[$row['komponen']] = $row['pengali'];
    	$nakomp[$row['komponen']] = $row['nakomp'];
    }
    $ptkp = [];
    $str = 'select id,value from '.$dbname.'.sdm_ho_pph21_ptkp';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
    	$ptkp[$bar->id] = $bar->value;
    }
    $pphtarif = [];
    $pphpercent = [];
    $str = 'select level,percent,upto from '.$dbname.'.sdm_ho_pph21_kontribusi order by level';
    $res = mysql_query($str);
    $urut = 0;
    while ($bar = mysql_fetch_object($res)) {
    	$pphtarif[$urut] = $bar->upto;
    	$pphpercent[$urut] = $bar->percent / 100;
    	++$urut;
    }
    foreach ($id as $key => $val) {
    	$penghasilan[$val[0]] = 0;
    	$penghasilanbruto[$val[0]] = 0;
    	foreach ($readyData as $dat => $bar) {
    		if ($val[0] == $bar['karyawanid']) {
    			if (1 == $comp[$bar['idkomponen']] || 5 == $bar['idkomponen'] || 9 == $bar['idkomponen'] || 66 == $bar['idkomponen']) {
    				$penghasilan[$val[0]] += floor($comp[$bar['idkomponen']] * $bar['jumlah']);
    			}

				// biaya jabatan 1 - penambah
    			if (1 == $comp[$bar['idkomponen']] || 5 == $comp[$bar['idkomponen']] 
					|| 9 == $comp[$bar['idkomponen']] || 66 == $comp[$bar['idkomponen']]) {
    				$penghasilanbruto[$val[0]] += $comp[$bar['idkomponen']] * $bar['jumlah'];
    			}
    		}
    	}
    }
	
	// Premi yang ditembak langsung, di CDS/LSP sudah dimasukan ke Pendapatan Lain2
	$strsl = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=16";
    $slRes = fetchData($strsl);
    foreach ($slRes as $key => $val) {
    	//$premPengawas[$val['karyawanid']] = $val['jumlah'];
    }

	//----------------------------------------------------------------
    foreach ($id as $key => $val) {
    	$jhtptpersen55[$val[0]] = 0; // FA 20200228
    	$jpptpersen56[$val[0]] = 0; // FA 20200228
    	$jhtkarypersen[$val[0]] = 0;
    	$jpkarypersen[$val[0]] = 0;
    	$gapoktunj[$val[0]] = 0;
    	$pph21[$val[0]] = 0;
    	$pph21thr[$val[0]] = 0;
    	$pph21bonus[$val[0]] = 0;
    	$gapok[$val[0]] = 0;
    	$tunjgol[$val[0]] = 0;
    	$tunjab[$val[0]] = 0;
    	$tunjnat[$val[0]] = 0;
    	$tunjprestasi[$val[0]] = 0;
    	$totuptetap[$val[0]] = 0;
    	$tunjharian[$val[0]] = 0;
    	$totgross[$val[0]] = 0;
    	$jkk[$val[0]] = 0;
    	$jkm[$val[0]] = 0;
    	$bpjspt[$val[0]] = 0;
    	$totgajibruto[$val[0]] = 0;
    	$biayajab[$val[0]] = 0;
    	$gjnettosebulan[$val[0]] = 0;
    	$gjnettosetahun[$val[0]] = 0;
    	$ptkp[$val[0]] = 0;
    	$pkp[$val[0]] = 0;
    	$thpbruto[$val[0]] = 0;
    	$potonganegrek[$val[0]] = 0;
    	$potongankaryawan[$val[0]] = 0;
    	$potonganangkong[$val[0]] = 0;
    	$potongandenda[$val[0]] = 0;
    	$potonganlainnya[$val[0]] = 0;
    	$potonganhk[$val[0]] = 0;
    	$potonganbpjskes[$val[0]] = 0;
    	$thpnetto[$val[0]] = 0;
    	$tunjlembur[$val[0]] = 0;
    	$tunjtidaktetap[$val[0]] = 0;
    	//$byjb[$val[0]] = 0;
    	$byjbq[$val[0]] = 0;
    	$pphsetahun[$val[0]] = 0;
    	$pph21rapel[$val[0]] = 0;
    	$tunjpremi[$val[0]] = 0;
    	$potdendapanen[$val[0]] = 0;
		$bonusvalue[$val[0]] = 0;
		$thrvalue[$val[0]] = 0;
		$akumulasi_pph21[$val[0]] = 0;
		$rapelupah[$val[0]] = 0;
		$insentif_pph21[$val[0]] = 0;
		
		$a = 0;
		foreach ($readyData as $dat => $bar) {
			$jumfirst=0;
			$jumlast=0;
			if ($val[0] == $bar['karyawanid']) {
				// FA 20200302 - prorata
				$idkar = $bar['karyawanid'];
				$strx = "select * from sdm_ho_employee where karyawanid= ".$idkar;
				$resx = mysql_query($strx);
				$barx = mysql_fetch_assoc($resx);

				if ($barx['firstpayment']==$param['periodegaji'] || $barx['firstvol']<100){
					$jumfirst= $bar['jumlah'] * ($barx['firstvol']/100);
				}
				if ($barx['lastpayment']==$param['periodegaji'] && $barx['lastvol']<100){
					$jumlast= $bar['jumlah'] * ($barx['lastvol']/100);
				}

				// JHT Perusahaan
				if (55 == $comp[$bar['idkomponen']] || 55 == $bar['idkomponen']) {
					$jhtptpersen55[$val[0]] += $bar['jumlah'];
				}
				// JP Perusahaan
				if (56 == $comp[$bar['idkomponen']] || 56 == $bar['idkomponen']) {
					$jpptpersen56[$val[0]] += $bar['jumlah'];
				}
				
				if (52 == $comp[$bar['idkomponen']] || 52 == $bar['idkomponen']) {
					$potonganegrek[$val[0]] += $bar['jumlah'];
				}

				// 10= angsuran karyawan, 27= potongan lain
				if (10 == $comp[$bar['idkomponen']] || 10 == $bar['idkomponen']) {
					$potongankaryawan[$val[0]] += $bar['jumlah'];
				}

				if (6 == $comp[$bar['idkomponen']] || 6 == $bar['idkomponen']) {
					$jkk[$val[0]] += $bar['jumlah'];
				}

				if (7 == $comp[$bar['idkomponen']] || 7 == $bar['idkomponen']) {
					$jkm[$val[0]] += $bar['jumlah'];
				}

				if (57 == $comp[$bar['idkomponen']] || 57 == $bar['idkomponen']) {
					$bpjspt[$val[0]] += floor($bar['jumlah']);
				}

				if (5 == $comp[$bar['idkomponen']] || 5 == $bar['idkomponen']) {
					$jhtkarypersen[$val[0]] += $bar['jumlah'];
				}

				if (9 == $comp[$bar['idkomponen']] || 9 == $bar['idkomponen']) {
					$jpkarypersen[$val[0]] += $bar['jumlah'];
				}

				if (17 == $comp[$bar['idkomponen']] || 17 == $bar['idkomponen']) {
					$tunjlembur[$val[0]] = $bar['jumlah'];
				}

				// Tunjangan Natura Pribadi
				if (4 == $comp[$bar['idkomponen']] || 4 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$tunjnat[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$tunjnat[$val[0]] += $jumlast;
					} else {
						$tunjnat[$val[0]] += $bar['jumlah'];
					}
				}
				
				if (15 == $comp[$bar['idkomponen']] || 15 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$tunjprestasi[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$tunjprestasi[$val[0]] += $jumlast;
					} else {
						$tunjprestasi[$val[0]] += $bar['jumlah'];
					}
				}

				/*
				// Biaya Jabatan >> ke-2
				if (66 == $comp[$bar['idkomponen']] || 66 == $bar['idkomponen']) {

					$nilaibatasmax3 = 6000000;
					//$byjb[$val[0]] += $bar['jumlah'];
					if ($nilaibatasmax3 < $penghasilanbruto[$val[0]]) {
						$biayajab[$val[0]] = $jabMax2;
						$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $biayajab[$val[0]], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
					} else {
						$biayajab[$val[0]] = floor($penghasilanbruto[$val[0]] * 0.05);
						$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $biayajab[$val[0]], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
					}
				}
				*/
				
				// angsuran pinjaman
				if (25 == $comp[$bar['idkomponen']] || 25 == $bar['idkomponen']) {
					$potongankaryawan[$val[0]] += $bar['jumlah'];
				}
				
				//## potongan lainnya 19 dan 27
				if (19 == $comp[$bar['idkomponen']] || 19 == $bar['idkomponen']  || 27 == $comp[$bar['idkomponen']] || 27 == $bar['idkomponen']) {
					$potonganlainnya[$val[0]] += $bar['jumlah'];
				}
				
				//## potongan HK - rekap
				if (20 == $comp[$bar['idkomponen']] || 20 == $bar['idkomponen']) {
					$potonganhk[$val[0]] += $bar['jumlah'];
				}
				
				if (11 == $comp[$bar['idkomponen']] || 11 == $bar['idkomponen']) {
					$potonganangkong[$val[0]] += $bar['jumlah'];
				}
 
				if (64 == $comp[$bar['idkomponen']] || 64 == $bar['idkomponen']) {
					$potongandenda[$val[0]] += $bar['jumlah'];
				}

				if (8 == $comp[$bar['idkomponen']] || 8 == $bar['idkomponen']) {
					$potonganbpjskes[$val[0]] += $bar['jumlah'];
				}

				if (26 == $comp[$bar['idkomponen']] || 26 == $bar['idkomponen']) {
					$potdendapanen[$val[0]] += $bar['jumlah'];
				}
				
				// Natura Keluarga
				if (29 == $comp[$bar['idkomponen']] || 29 == $bar['idkomponen'] || 30 == $comp[$bar['idkomponen']] || 30 == $bar['idkomponen'] || 32 == $comp[$bar['idkomponen']] || 32 == $bar['idkomponen'] || 33 == $comp[$bar['idkomponen']] || 33 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$gapoktunj[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$gapoktunj[$val[0]] += $jumlast;
					} else {
						$gapoktunj[$val[0]] += $bar['jumlah'];
					}
				}

				if (24 == $comp[$bar['idkomponen']] || 24 == $bar['idkomponen']) {
					$pph21[$val[0]] += floor($bar['jumlah']);
				}
				
				// gaji pokok - jika gajian BHL atau isHarian di tabel Tipe Karyawan = 1, maka gak ada gapoknya
				$idkar = $bar['karyawanid'];
				$strx = "select b.isharian from datakaryawan a inner join sdm_5tipekaryawan b on a.tipekaryawan = b.id where karyawanid= ".$idkar;
				$resx = mysql_query($strx);
				$barx = mysql_fetch_object($resx);
				if (1 == $bar['idkomponen']) {
					if ($barx->isharian == 0){
						//$gapok[$val[0]] += $bar['jumlah']; // FA 20200109
						if ($jumfirst>0){
							$gapok[$val[0]] += $jumfirst;
						} elseif ($jumlast>0) {
							$gapok[$val[0]] += $jumlast;
						} else {
							$gapok[$val[0]] += $bar['jumlah'];
						}
					}
				}
				
				// tunjangan-tunjangan yg masuk dalam kolom tunjangan golongan
				if (3 == $comp[$bar['idkomponen']] || 3 == $bar['idkomponen'] 
				|| 35 == $comp[$bar['idkomponen']] || 35 == $bar['idkomponen'] 
				|| 36 == $comp[$bar['idkomponen']] || 36 == $bar['idkomponen'] 
				|| 37 == $comp[$bar['idkomponen']] || 37 == $bar['idkomponen'] 
				|| 38 == $comp[$bar['idkomponen']] || 38 == $bar['idkomponen'] 
				|| 39 == $comp[$bar['idkomponen']] || 39 == $bar['idkomponen'] 
				|| 40 == $comp[$bar['idkomponen']] || 40 == $bar['idkomponen']
				|| 40 == $comp[$bar['idkomponen']] || 41 == $bar['idkomponen'] 
				|| 42 == $comp[$bar['idkomponen']] || 42 == $bar['idkomponen'] 
				|| 43 == $comp[$bar['idkomponen']] || 43 == $bar['idkomponen'] 
				|| 44 == $comp[$bar['idkomponen']] || 44 == $bar['idkomponen'] 
				|| 45 == $comp[$bar['idkomponen']] || 45 == $bar['idkomponen'] 
				|| 46 == $comp[$bar['idkomponen']] || 46 == $bar['idkomponen'] 
				|| 47 == $comp[$bar['idkomponen']] || 47 == $bar['idkomponen'] 
				|| 48 == $comp[$bar['idkomponen']] || 48 == $bar['idkomponen'] 
				|| 49 == $comp[$bar['idkomponen']] || 49 == $bar['idkomponen'] 
				|| 50 == $comp[$bar['idkomponen']] || 50 == $bar['idkomponen'] 
				|| 51 == $comp[$bar['idkomponen']] || 51 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$tunjgol[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$tunjgol[$val[0]] += $jumlast;
					} else {
						$tunjgol[$val[0]] += $bar['jumlah'];
					}
				}

				if (16 == $comp[$bar['idkomponen']] || 16 == $bar['idkomponen']) {
					$tunjpremi[$val[0]] += $bar['jumlah']; // karyawan bulanan gak dapat premi 20200210
				}

				if (63 == $comp[$bar['idkomponen']] || 63 == $bar['idkomponen']) {
					//$tunjkom[$val[0]] += $bar['jumlah'];
				}

				if (58 == $comp[$bar['idkomponen']] || 58 == $bar['idkomponen']) {
					$pendapatanlain[$val[0]] += $bar['jumlah'];
				}

				if (59 == $comp[$bar['idkomponen']] || 59 == $bar['idkomponen']) {
					//$tunjprt[$val[0]] += $bar['jumlah'];
				}

				if (61 == $comp[$bar['idkomponen']] || 61 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$tunjkehadiran[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$tunjkehadiran[$val[0]] += $jumlast;
					} else {
						$tunjkehadiran[$val[0]] += $bar['jumlah'];
					}
				}

				if (65 == $comp[$bar['idkomponen']] || 65 == $bar['idkomponen']) {
					//$tunjair[$val[0]] += $bar['jumlah'];
				}

				if (60 == $comp[$bar['idkomponen']] || 60 == $bar['idkomponen']) {
					//$tunjsprpart[$val[0]] += $bar['jumlah'];
				}

				if (21 == $comp[$bar['idkomponen']] || 21 == $bar['idkomponen']) {
					$tunjharian[$val[0]] += $bar['jumlah'];
				}

				if (23 == $comp[$bar['idkomponen']] || 23 == $bar['idkomponen']) {
					//$tunjdinas[$val[0]] += $bar['jumlah'];
					$tunjlain[$val[0]] += $bar['jumlah'];
				}

				if (12 == $comp[$bar['idkomponen']] || 12 == $bar['idkomponen']) {
					//$tunjcuti[$val[0]] += $bar['jumlah'];
				}

				if (62 == $comp[$bar['idkomponen']] || 62 == $bar['idkomponen']) {
					//$tunjlist[$val[0]] += $bar['jumlah'];
				}

				if (22 == $comp[$bar['idkomponen']] || 22 == $bar['idkomponen']) {
					//$tunjlain[$val[0]] += $bar['jumlah'];
				}

				// Rapel 1 , idkomp= 54
				if (54 == $comp[$bar['idkomponen']] || 54 == $bar['idkomponen']) {
					$tunjrapel[$val[0]] += $bar['jumlah'];
				}

				// Insentif PPH21 , idkomp= 70 - FA20200329
				if (70 == $comp[$bar['idkomponen']] || 70 == $bar['idkomponen']) {
					$insentif_pph21[$val[0]] += $bar['jumlah'];
				}

				if (2 == $comp[$bar['idkomponen']] || 2 == $bar['idkomponen']) {
					if ($jumfirst>0){
						$tunjab[$val[0]] += $jumfirst;
					} elseif ($jumlast>0) {
						$tunjab[$val[0]] += $jumlast;
					} else {
						$tunjab[$val[0]] += $bar['jumlah'];
					}
				}

				$totuptetap[$val[0]] = $gapok[$val[0]] + $tunjab[$val[0]] + $gapoktunj[$val[0]] + $tunjgol[$val[0]] + $tunjnat[$val[0]] + $tunjprestasi[$val[0]];
				
				$totgross[$val[0]] = $totuptetap[$val[0]] + $tunjlembur[$val[0]] + $tunjpremi[$val[0]] + $tunjkom[$val[0]] + $pendapatanlain[$val[0]] + $tunjprt[$val[0]] + $tunjkehadiran[$val[0]] + $tunjair[$val[0]] + $tunjsprpart[$val[0]] + $tunjharian[$val[0]] + $tunjcuti[$val[0]] + $tunjlist[$val[0]] + $tunjlain[$val[0]] + $premPengawas[$val[0]]
				-($potonganhk[$val[0]]+$potdendapanen[$val[0]]+$potongandenda[$val[0]]+$potonganlainnya[$val[0]]);
			}
		}
	}


	$listbutton = '<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>';
	$list0 = "<table class=sortable border=0 cellspacing=1>\r\n                     <thead>\r\n            <tr class=rowheader align=center>";
	$list0 .= '<td >'.$_SESSION['lang']['nomor'].'</td>';
	$list0 .= '<td>'.$_SESSION['lang']['periodegaji'].'</td>';
	$list0 .= '<td>ID Karyawan</td>';
    $list0 .= '<td>N I K</td>'; //FA 20190411
    $list0 .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['functionname'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['status'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['kodegolongan'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['gajipokok'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['tunjgol'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['tjjabatan'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['naturapekerja'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['naturakeluarga'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">Tunjangan Prestasi</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold"><b>'.$_SESSION['lang']['totalupahtetap'].'</b></td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['lembur2'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Premi BKM</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Pendapatan Lain</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Tunjangan Kehadiran</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganharian'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Tunjangan Lainnya</td>';
	$list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Potongan HK</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Denda BKM</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Potongan Absen</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Potongan Lainnya</b></td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['gross'].'</b></td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['jkk'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['jkm'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['bpjskes'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['totalgajibruto'].'</b></td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['rapelkenaikan'].'</td>';    
	$list0 .= '<td style="background-color:lightblue;font-weight:bold">THR</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Bonus</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['biayajabatan'].'</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['jhtkary'].'</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['jpkary'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['gjnettosebulan'].'</b></td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['gjnettosetahun'].'</b></td>';
    $list0 .= '<td style="background-color:grey;font-weight:bold">'.$_SESSION['lang']['ptkp'].'</td>';
    $list0 .= '<td style="background-color:grey;font-weight:bold">'.$_SESSION['lang']['pkp'].'</td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">'.$_SESSION['lang']['pph21'].'</td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">'.$_SESSION['lang']['pph21'].' Rapel</td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">'.$_SESSION['lang']['pph21'].' THR</td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">'.$_SESSION['lang']['pph21'].' Bonus</td>';
    $list0 .= '<td style="background-color:cyan;font-weight:bold"><b>'.$_SESSION['lang']['thpbruto'].'</b></td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">Insentif '.$_SESSION['lang']['pph21'].'</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['jhtkary'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['jpkary'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['bpjskes'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Angsuran Pinjaman</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Angsuran Egrek</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Angsuran Angkong</b></td>';
    $list0 .= '<td style="background-color:#55fc7f;font-weight:bold"><b>'.$_SESSION['lang']['thpnetto'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Pesangon</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>PPh21 Pesangon</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>JHT Perusahaan</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>JP Perusahaan</b></td></tr></thead><tbody>';

    $negatif = false;
    $list1 = '';
    $listx = 'Masih ada gaji dibawah 0:';
    $list2 = '';
    $list3 = '';
    $no = 0;


    // $qTunjLbr = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen =17";
    // $resTunjLbr = fetchData($qTunjLbr);
    // foreach ($resTunjLbr as $key => $val) {
    // 	$tunjlembur[$val['karyawanid']] = $val['jumlah'];
    // }
	
    $qTunjL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen =63";
    $resTunj = fetchData($qTunjL);
    foreach ($resTunj as $key => $val) {
    	$tunjkom[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjLok = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=58";
    $resTunjLok = fetchData($qTunjLok);
    foreach ($resTunjLok as $key => $val) {
    	$pendapatanlain[$val['karyawanid']] = $val['jumlah'];
		/*
		if ($val['karyawanid']==468){
			echo "warning: ".$pendapatanlain[$val['karyawanid']];
			exit();
		}
		*/
    }
    $qTunjRT = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=59";
    $resTunjRT = fetchData($qTunjRT);
    foreach ($resTunjRT as $key => $val) {
    	$tunjrt[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjB = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=61";
    $resTunjB = fetchData($qTunjB);
    foreach ($resTunjB as $key => $val) {
    	$tunjkehadiran[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjAM = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=65";
    $resTunjAM = fetchData($qTunjAM);
    foreach ($resTunjAM as $key => $val) {
    	$tunjairminum[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjSP = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=60";
    $resTunjSP = fetchData($qTunjSP);
    foreach ($resTunjSP as $key => $val) {
    	$tunjSP[$val['karyawanid']] = $val['jumlah'];
    }
	
	/*
	//FA20200407, sudah ada inputan sebelumnya, id 21 - dari kebun_kehadiran_vw dan sdm gaji
    $qTunjH = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=21";
    $resTunjH = fetchData($qTunjH);
    foreach ($resTunjH as $key => $val) {
    	$tunjharian[$val['karyawanid']] = $val['jumlah'];
    }*/
    
	$qTunjD = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=23";
    $resTunjD = fetchData($qTunjD);
    foreach ($resTunjD as $key => $val) {
    	//$tunjdinas[$val['karyawanid']] = $val['jumlah'];
    	$tunjlain[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjC = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=12";
    $resTunjC = fetchData($qTunjC);
    foreach ($resTunjC as $key => $val) {
    	$tunjcuti[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=62";
    $resTunjL = fetchData($qTunjL);
    foreach ($resTunjL as $key => $val) {
    	$tunjlistrik[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjLL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=22";
    $resTunjLL = fetchData($qTunjLL);
    foreach ($resTunjLL as $key => $val) {
    	$tunjlain[$val['karyawanid']] = $val['jumlah'];
    }
	
	//ambil pph21 yang terkumpul
	$qTunjLL = 'select kodeorg,karyawanid,sum(jumlah) as jumlah_pph21 from '.$dbname.".sdm_gaji where SUBSTRING(periodegaji,1,4) ='".substr($param['periodegaji'],0,4)."' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=24 group by kodeorg,karyawanid";

    $resTunjLL = fetchData($qTunjLL);
    foreach ($resTunjLL as $key => $val) {
    	$akumulasi_pph21[$val['karyawanid']] = $val['jumlah_pph21'];
    }
	
	// Rapel Upah, ambil dari SDM Gaji
    $qTunjRU = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=54";
    $resTunjRU = fetchData($qTunjRU);
    foreach ($resTunjRU as $key => $val) {
    	$rapelupah[$val['karyawanid']] = $val['jumlah'];
    }
	// THR
	$qThr = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=14";
    $resThr = fetchData($qThr);
    foreach ($resThr as $key => $val) {
    	$thrvalue[$val['karyawanid']] = $val['jumlah'];
    }
	//BONUS
	$qBonus = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=13";
    $resBonus = fetchData($qBonus);
    foreach ($resBonus as $key => $val) {
    	$bonusvalue[$val['karyawanid']] = $val['jumlah'];
    }
	
	// mulai looping
    foreach ($id as $key => $val) {
    	$qJab = 'select namajabatan from '.$dbname.".sdm_5jabatan where kodejabatan='".$kdjabatan[$val[0]]."'";
    	$res = mysql_query($qJab);
    	while ($bar = mysql_fetch_object($res)) {
    		$nmjabatan = $bar->namajabatan;
    	}
    	$qTipe = 'select tipe from '.$dbname.".sdm_5tipekaryawan where id='".$tipekar[$val[0]]."'";
    	$resT = mysql_query($qTipe);
    	while ($barT = mysql_fetch_object($resT)) {
    		$namatipe = $barT->tipe;
    	}
    	$qGol = 'select namagolongan from '.$dbname.".sdm_5golongan where kodegolongan='".$kdgol[$val[0]]."'";
    	$resG = mysql_query($qGol);
    	while ($barG = mysql_fetch_object($resG)) {
    		$namagolongan = $barG->namagolongan;
    	}
    	$totgross[$val[0]] = 
		$totuptetap[$val[0]] + $tunjlembur[$val[0]] + $tunjpremi[$val[0]] + $premPengawas[$val[0]] + $tunjkom[$val[0]] + $pendapatanlain[$val[0]] + $tunjrt[$val[0]] + $tunjkehadiran[$val[0]] + $tunjairminum[$val[0]] + $tunjSP[$val[0]] + $tunjharian[$val[0]] + $tunjcuti[$val[0]] + $tunjlistrik[$val[0]] + $tunjlain[$val[0]] 
		-($potonganhk[$val[0]]+$potdendapanen[$val[0]]+$potongandenda[$val[0]]+$potonganlainnya[$val[0]]);

		//+$potongankaryawan[$val[0]]+$potonganegrek[$val[0]]+$potonganangkong[$val[0]]
		// + $tunjdinas[$val[0]] + $rapelupah[$val[0]]

        //FA 20190409 - dikurangi potongan gross (id 19 dan 20)
		// $totgross[$val[0]] = $totgross[$val[0]] - $potGross[$val[0]]; //FA 20190409 - dikurangi potongan gross (id 19 dan 20)
		
    	$totgajibruto[$val[0]] = $totgross[$val[0]] + $jkk[$val[0]] + $jkm[$val[0]] + $bpjspt[$val[0]];
		
		// Biaya Jabatan ke-3
    	//$nilaibatasmax3 = 6000000;
    	$nilaibatasmax3 = 500000; // per bulan
   		$biayajab[$val[0]] = floor($totgajibruto[$val[0]] * 0.05);
    	if ($nilaibatasmax3 < $biayajab[$val[0]]) {
    		$biayajab[$val[0]] = 500000;
    	}

		if($tanggalkeluar[$val[0]] != "" && $tanggalkeluar[$val[0]] != "0000-00-00" ){ //kalo resign
			if( $lamakerja[$val[0]] > 1 ){ //kalo sudah bekerja selama setahun lebih
				$masakerjabulan = ceil($lamakerjathnini[$val[0]] * 12);				
				$pembagi_bulan = $masakerjabulan; //jika karyawan bekerja kurang dari setahun
			}else{ //kerja kurang dari setahun
				$masakerjabulan = ceil($lamakerja[$val[0]] * 12);				
				$pembagi_bulan = $masakerjabulan; //jika karyawan bekerja kurang dari setahun
			}
		}else{ //kalau tidak resign
			if( $masakerja[$val[0]] < 1){
				$masakerjabulan = ceil($masakerja[$val[0]] * 12);
				if( $flag_desember == "Y" ){
//					$pembagi_bulan = 1; //pada desember dibagi satu
					$pembagi_bulan = 12; //pada desember dibagi satu
				}else{
					$pembagi_bulan = $masakerjabulan; //jika karyawan bekerja kurang dari setahun
				}				
			}else{
				if( $flag_desember == "Y" ){
//					$pembagi_bulan = 1; //pada desember dibagi satu
					$pembagi_bulan = 12; //pada desember dibagi satu
				}else{
					$pembagi_bulan = 12; //jika karyawan bekerja selama setahun
				}
				
				$masakerjabulan = "A";
			}
		}

		/* Untuk akumulasi gaji bruto setahun - FA 20200402 */
		$strtb= "";
		$idxx= $id[$val[0]][0];
		$thn= substr($param['periodegaji'],0,4);
		$akumulasi_bruto = 0;
		if( ($tanggalkeluar[$val[0]] != "" && $tanggalkeluar[$val[0]] != "0000-00-00") || $flag_desember == "Y"){ 
			$thn= substr($param['periodegaji'],0,4);
			$strtb = "select sum(totbruto) as jumtotbruto from sdm_gaji_rekap2_vw where karyawanid='".$idxx."' and substr(periodegaji,1,4)=".$thn." and periodegaji<'".$param['periodegaji']."' group by karyawanid";
			$restb = mysql_query($strtb);
			while ($bartb = mysql_fetch_object($restb)) {
				$akumulasi_bruto = $bartb->jumtotbruto;
			}
			$akumulasi_bruto = $akumulasi_bruto + $totgajibruto[$val[0]];
		} else {
			$akumulasi_bruto = $totgajibruto[$val[0]] * $pembagi_bulan;
		}
    	/* ------------------------------------------------------------------- */
		
		$ptkp[$val[0]] = $ptkp[str_replace('K', '', $statuspajak[$val[0]])]; // PTKP Setahun

    	$gjnettosebulan[$val[0]] = $totgajibruto[$val[0]] - ($biayajab[$val[0]] + $jhtkarypersen[$val[0]] + $jpkarypersen[$val[0]]);    	
    	$gjnettosetahun[$val[0]] = $gjnettosebulan[$val[0]] * $pembagi_bulan;
    	$pkp[$val[0]] = floor(($gjnettosetahun[$val[0]] - $ptkp[$val[0]]) / 1000) * 1000;
		$byjab_thn = $biayajab[$val[0]] * $pembagi_bulan;
		
		/* Hitungan THR */
		$pkpthr[$val[0]] = 0;
		if ($thrvalue[$val[0]] > 0) {
			$nilaibatasmax4 = 500000*$pembagi_bulan; // batas biaya jab max dlm setahun
			$biayajabthr[$val[0]] = floor(($akumulasi_bruto + $thrvalue[$val[0]]) * 0.05);
			if ($biayajabthr[$val[0]] > $nilaibatasmax4) {
				$biayajabthr[$val[0]] = $nilaibatasmax4;
			}
			/*
			if ($byjab_thn + $biayajabthr[$val[0]] > $nilaibatasmax4){
				$biayajabthr[$val[0]] = $nilaibatasmax4 - $byjab_thn; 
			}
			*/
			if ($biayajabthr[$val[0]] < 0) {
				$biayajabthr[$val[0]] = 0;
			}
			$gjnettosetahunthr[$val[0]] = $akumulasi_bruto + $thrvalue[$val[0]] - ($biayajabthr[$val[0]] + ($jhtkarypersen[$val[0]] * $pembagi_bulan) + ($jpkarypersen[$val[0]] * $pembagi_bulan));
			$pkpthr[$val[0]] = floor(($gjnettosetahunthr[$val[0]] - $ptkp[$val[0]]) / 1000) * 1000;
		}
		
		/* Hitungan Bonus */
		$pkpbonus[$val[0]] = 0;
		if ($bonusvalue[$val[0]] > 0) {
			$nilaibatasmax4 = 500000*$pembagi_bulan; // batas biaya jab max dlm setahun
			$biayajabbonus[$val[0]] = floor(($akumulasi_bruto+ $bonusvalue[$val[0]]) * 0.05);
			if ($biayajabbonus[$val[0]] > $nilaibatasmax4) {
				$biayajabbonus[$val[0]] = $nilaibatasmax4;
			}
			/*
			if ($byjab_thn + $biayajabbonus[$val[0]] > $nilaibatasmax4){
				$biayajabbonus[$val[0]] = $nilaibatasmax4 - $byjab_thn; 
			}
			*/
			if ($biayajabbonus[$val[0]] < 0) {
				$biayajabbonus[$val[0]] = 0;
			}
			$gjnettosetahunbonus[$val[0]] = $akumulasi_bruto + $bonusvalue[$val[0]] - ($biayajabbonus[$val[0]] + ($jhtkarypersen[$val[0]] *$pembagi_bulan) + ($jpkarypersen[$val[0]] * $pembagi_bulan));
			$pkpbonus[$val[0]] = floor(($gjnettosetahunbonus[$val[0]] - $ptkp[$val[0]]) / 1000) * 1000;
		}

/*
$idxx= $id[$val[0]][0];
$x= $gjnettosetahunbonus[$val[0]];
$y= $gjnettosetahun[$val[0]];
$z= $biayajabbonus[$val[0]]; 
$pkpbon = $pkpbonus[$val[0]];
$ptkpbon = $ptkp[$val[0]];
if ($idxx=='0000000062'){
echo "warning:"." /gaji netto setahun= ".$y." /akumulasi bruto= ".$akumulasi_bruto." /netto setahun bonus= ".$x." /biaya jab bonus= ".$z." /pkpbonus= ".$pkpbon." /ptkp= ".$ptkpbon."/ str= ".$strtb;
exit();
}
*/
		
		$zz = 0;
    	$sisazz = 0;
    	if (0 < $pkp[$val[0]]) {
    		if ($pkp[$val[0]] < $pphtarif[0]) {
    			$zz += $pphpercent[0] * $pkp[$val[0]];
    			$sisazz = 0;				
    		} else {
    			if ($pphtarif[0] <= $pkp[$val[0]]) {
    				$zz += $pphpercent[0] * $pphtarif[0];
    				$sisazz = $pkp[$val[0]] - $pphtarif[0];
    				if ($sisazz < $pphtarif[1] - $pphtarif[0]) {
    					$zz += $pphpercent[1] * $sisazz;
    					$sisazz = 0;
    				} else {
    					if ($pphtarif[1] - $pphtarif[0] <= $sisazz) {
    						$zz += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
    						$sisazz = $pkp[$val[0]] - $pphtarif[1];
    						if ($sisazz < $pphtarif[2] - $pphtarif[1]) {
    							$zz += $pphpercent[2] * $sisazz;
    							$sisazz = 0;
    						} else {
    							if ($pphtarif[2] - $pphtarif[1] <= $sisazz) {
    								$zz += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
    								$sisazz = $pkp[$val[0]] - $pphtarif[2];
    								if (0 < $sisazz) {
    									$zz += $pphpercent[3] * $sisazz;
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
		
		//bagian thr
		$zz_thr = 0;
    	$sisazz_thr = 0;
    	if (0 < $pkpthr[$val[0]]) {
    		if ($pkpthr[$val[0]] < $pphtarif[0]) {
    			$zz_thr += $pphpercent[0] * $pkpthr[$val[0]];
    			$sisazz_thr = 0;				
    		} else {
    			if ($pphtarif[0] <= $pkpthr[$val[0]]) {
    				$zz_thr += $pphpercent[0] * $pphtarif[0];
    				$sisazz_thr = $pkpthr[$val[0]] - $pphtarif[0];
    				if ($sisazz_thr < $pphtarif[1] - $pphtarif[0]) {
    					$zz_thr += $pphpercent[1] * $sisazz_thr;
    					$sisazz_thr = 0;
    				} else {
    					if ($pphtarif[1] - $pphtarif[0] <= $sisazz_thr) {
    						$zz_thr += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
    						$sisazz_thr = $pkpthr[$val[0]] - $pphtarif[1];
    						if ($sisazz_thr < $pphtarif[2] - $pphtarif[1]) {
    							$zz_thr += $pphpercent[2] * $sisazz_thr;
    							$sisazz_thr = 0;
    						} else {
    							if ($pphtarif[2] - $pphtarif[1] <= $sisazz_thr) {
    								$zz_thr += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
    								$sisazz_thr = $pkpthr[$val[0]] - $pphtarif[2];
    								if (0 < $sisazz_thr) {
    									$zz_thr += $pphpercent[3] * $sisazz_thr;
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
		
    	//bagian bonus
		$zz_bonus = 0;
    	$sisazz_bonus = 0;
    	if (0 < $pkpbonus[$val[0]]) {
    		if ($pkpbonus[$val[0]] < $pphtarif[0]) {
    			$zz_bonus += $pphpercent[0] * $pkpbonus[$val[0]];
    			$sisazz_bonus = 0;
    		} else {
    			if ($pphtarif[0] <= $pkpbonus[$val[0]]) {
    				$zz_bonus += $pphpercent[0] * $pphtarif[0];
    				$sisazz_bonus = $pkpbonus[$val[0]] - $pphtarif[0];
    				if ($sisazz_bonus < $pphtarif[1] - $pphtarif[0]) {
    					$zz_bonus += $pphpercent[1] * $sisazz_bonus;
    					$sisazz_bonus = 0;
    				} else {
    					if ($pphtarif[1] - $pphtarif[0] <= $sisazz_bonus) {
    						$zz_bonus += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
    						$sisazz_bonus = $pkpbonus[$val[0]] - $pphtarif[1];
    						if ($sisazz_bonus < $pphtarif[2] - $pphtarif[1]) {
    							$zz_bonus += $pphpercent[2] * $sisazz_bonus;
    							$sisazz_bonus = 0;
    						} else {
    							if ($pphtarif[2] - $pphtarif[1] <= $sisazz_bonus) {
    								$zz_bonus += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
    								$sisazz_bonus = $pkpbonus[$val[0]] - $pphtarif[2];
    								if (0 < $sisazz_bonus) {
    									$zz_bonus += $pphpercent[3] * $sisazz_bonus;
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}

    	$pphsetahun[$val[0]] = round($zz / $pembagi_bulan); // ini pph sebulan, bulan berjalan

    	if ('' == $npwp[$val[0]]) {
    		$pphsetahun[$val[0]] = ceil($pphsetahun[$val[0]] + ($pphsetahun[$val[0]] * 20) / 100);			
    	}

/*		
$idxx= $id[$val[0]][0];
$pp21setahun= $zz;
$pp21= $pphsetahun[$val[0]];
$xpkp= $pkp[$val[0]];
if ($idxx=='0000000005'){
echo "warning: /pph21 setahun= ".$pp21setahun." /pph21 bulan= ".$pp21." /pkp= ".$xpkp;
exit();
}
*/

		// Jika Ada Rapel - FA 20200310
		$pphrapel= 0;
		$nrapel= $rapelupah[$val[0]];
		$idxx= $id[$val[0]][0];
		if ($nrapel > 0){
			$nx= 1;
			$pphrapel_x= 0;
			$nbln= intval(substr($param['periodegaji'],5,2))-1;
			$thn= substr($param['periodegaji'],0,4);

			$qGaji = "select * from sdm_gaji_rekap2_vw where karyawanid='".$idxx."' and substr(periodegaji,1,4)=".$thn." and periodegaji<'".$param['periodegaji']."'";
			$resx = fetchData($qGaji);
			foreach ($resx as $idz => $valx) {
				$bruto= 0;
				$netto= 0;
				$byjab= 0;
				$pkpx= 0;

				$bruto= $valx['totbruto'];
				$bruto = floor($bruto + round($nrapel/$nbln));
				$bruto_setahun= $bruto * $pembagi_bulan;

				$netto= $valx['totnetto'];
				
				// hitung lagi biaya jab utk rapel
				$byjab = floor($bruto * 0.05);
				if ($byjab > 500000) {
					$byjab = 500000; // nilai batas max per bulan
				}
				
				$netto_setahun = ($netto + round($nrapel/$nbln) - $byjab) * $pembagi_bulan; // netto setahun
				$pkpx = floor(($netto_setahun - $ptkp[$val[0]]) / 1000) * 1000;

				$pph21rapel_setahun = 0;
				$sisa_rapel = 0;
				if (0 < $pkpx) {
					if ($pkpx < $pphtarif[0]) {
						$pph21rapel_setahun += $pphpercent[0] * $pkpx;
						$sisa_rapel = 0;				
					} else {
						if ($pphtarif[0] <= $pkpx) {
							$pph21rapel_setahun += $pphpercent[0] * $pphtarif[0];
							$sisa_rapel = $pkpx - $pphtarif[0];
							if ($sisa_rapel < $pphtarif[1] - $pphtarif[0]) {
								$pph21rapel_setahun += $pphpercent[1] * $sisa_rapel;
								$sisa_rapel = 0;
							} else {
								if ($pphtarif[1] - $pphtarif[0] <= $sisa_rapel) {
									$pph21rapel_setahun += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
									$sisa_rapel = $pkpx - $pphtarif[1];
									if ($sisa_rapel < $pphtarif[2] - $pphtarif[1]) {
										$pph21rapel_setahun += $pphpercent[2] * $sisa_rapel;
										$sisa_rapel = 0;
									} else {
										if ($pphtarif[2] - $pphtarif[1] <= $sisa_rapel) {
											$pph21rapel_setahun += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
											$sisa_rapel = $pkpx - $pphtarif[2];
											if (0 < $sisa_rapel) {
												$pph21rapel_setahun += $pphpercent[3] * $sisa_rapel;
											}
										}
									}
								}
							}
						}
					}
				}
				if ('' == $npwp[$val[0]]) {
					$pph21rapel_setahun= $pph21rapel_setahun + ($pph21rapel_setahun * 20/100);
				}
				$pphrapel= $pphrapel + round($pph21rapel_setahun/$pembagi_bulan);
				
				// Cek Bonus , id bonus=13, pph21 bonus=72
				$pph21_bonus_rapel = 0;
				$bonus_lalu= 0;
				$pph21_bonuslalu= 0;
				$qGaji = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=13";
				$resx = mysql_query($qGaji);
				while ($barx = mysql_fetch_object($resx)) {
					$bonus_lalu= $barx->jumlah;

					$jhtkarx= 0;
					$jpkarx= 0;

					$qjht = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=5";
					$resjht = mysql_query($qjht);
					while ($barjht = mysql_fetch_object($resjht)) {
						$jhtkarx= $barjht->jumlah;
					}
					$qjp = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=9";
					$resjp = mysql_query($qjp);
					while ($barjp = mysql_fetch_object($resjp)) {
						$jpkarx= $barjp->jumlah;
					}

					// ambil nilai PPh21 nya yg sudah dibayar
					$qGaji1 = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=72";
					$resx1 = mysql_query($qGaji1);
					while ($barx1 = mysql_fetch_object($resx1)) {
						$pph21_bonuslalu= $barx1->jumlah;;
					}


					/* Hitungan Bonus Rapel*/
					$pkpbonus_rapel = 0;

					$biayajabbonus_rapel = floor(($bruto_setahun + $bonus_lalu) * 0.05); // by jab bonus rapel setahun
					//$biayajabbonus_rapel = floor($biayajabbonus_rapel / $pembagi_bulan); // by jab bonus rapel sebulan
					if ($biayajabbonus_rapel > (500000 * $pembagi_bulan)) {
						$biayajabbonus_rapel = 500000 * $pembagi_bulan;
					}
					if ($biayajabbonus_rapel < 0) {
						$biayajabbonus_rapel = 0;
					}

					$netto_bonus = $bruto_setahun + $bonus_lalu - ($biayajabbonus_rapel + ($jhtkarx *$pembagi_bulan) + ($jpkarx * $pembagi_bulan));
					$pkpbonus_rapel = floor(($netto_bonus - $ptkp[$val[0]]) / 1000) * 1000;


					//bagian bonus rapel
					$pph21_bonus_rapel = 0;
					$zz_bonus_rapel = 0;
					$sisazz_bonus_rapel = 0;
					if (0 < $pkpbonus_rapel) {
						if ($pkpbonus_rapel < $pphtarif[0]) {
							$zz_bonus_rapel += $pphpercent[0] * $pkpbonus_rapel;
							$sisazz_bonus_rapel = 0;
						} else {
							if ($pphtarif[0] <= $pkpbonus_rapel) {
								$zz_bonus_rapel += $pphpercent[0] * $pphtarif[0];
								$sisazz_bonus_rapel = $pkpbonus_rapel - $pphtarif[0];
								if ($sisazz_bonus_rapel < $pphtarif[1] - $pphtarif[0]) {
									$zz_bonus_rapel += $pphpercent[1] * $sisazz_bonus_rapel;
									$sisazz_bonus_rapel = 0;
								} else {
									if ($pphtarif[1] - $pphtarif[0] <= $sisazz_bonus_rapel) {
										$zz_bonus_rapel += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
										$sisazz_bonus_rapel = $pkpbonus_rapel - $pphtarif[1];
										if ($sisazz_bonus_rapel < $pphtarif[2] - $pphtarif[1]) {
											$zz_bonus_rapel += $pphpercent[2] * $sisazz_bonus_rapel;
											$sisazz_bonus_rapel = 0;
										} else {
											if ($pphtarif[2] - $pphtarif[1] <= $sisazz_bonus_rapel) {
												$zz_bonus_rapel += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
												$sisazz_bonus_rapel = $pkpbonus_rapel - $pphtarif[2];
												if (0 < $sisazz_bonus_rapel) {
													$zz_bonus_rapel += $pphpercent[3] * $sisazz_bonus_rapel;
												}
											}
										}
									}
								}
							}
						}
					}
					$pph21_bonus_rapel = $zz_bonus_rapel;
					if ('' == $npwp[$val[0]]) {
						$pph21_bonus_rapel = ceil($pph21_bonus_rapel + ($pph21_bonus_rapel * 20) / 100) ;    		
					}
					if ($pph21_bonus_rapel<0){
						$pph21_bonus_rapel = 0;
					}
					$pph21_bonus_rapel = $pph21_bonus_rapel - $pph21_bonuslalu - $pph21rapel_setahun;

/*
$brut= $bruto_setahun + $bonus_lalu; 
$prdgjx= $valx['periodegaji'];
if ($idxx=='0000000032'){
	echo "warning pph21 bonus rapel= ".$pph21_bonus_rapel." /zz_bonus rapel= ".$zz_bonus_rapel." /pph21 rapel= ".$pphrapel." /pph21 bonus lalu= ".$pph21_bonuslalu." /by jab bns rapel=".$biayajabbonus_rapel." /bruto rapel sebulan=".$bruto." /bruto rapel setahun=".$brut." /bonusnya aja=".$bonus_lalu." /netto bonus rapel=".$netto_bonus." /pkp bonus rapel=".$pkpbonus_rapel." /jhtx=".$jhtkarx." /jpx=".$jpkarx;
	exit();
}
*/
				} // end of cek bonus rapel

				// Cek THR , id THR=14, pph21 THR=71
				$pph21_thr_rapel = 0;
				$thr_lalu= 0;
				$pph21_thrlalu= 0;
				$qGaji = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=14";
				$resx = mysql_query($qGaji);
				while ($barx = mysql_fetch_object($resx)) {
					$thr_lalu= $barx->jumlah;

					$jhtkarx= 0;
					$jpkarx= 0;

					$qjht = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=5";
					$resjht = mysql_query($qjht);
					while ($barjht = mysql_fetch_object($resjht)) {
						$jhtkarx= $barjht->jumlah;
					}
					$qjp = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=9";
					$resjp = mysql_query($qjp);
					while ($barjp = mysql_fetch_object($resjp)) {
						$jpkarx= $barjp->jumlah;
					}

					// ambil nilai PPh21 nya yg sudah dibayar
					$qGaji1 = "select * from sdm_gaji where karyawanid='".$idxx."' and periodegaji='".$valx['periodegaji']."' and idkomponen=71";
					$resx1 = mysql_query($qGaji1);
					while ($barx1 = mysql_fetch_object($resx1)) {
						$pph21_thrlalu= $barx1->jumlah;;
					}


					/* Hitungan thr Rapel*/
					$pkpthr_rapel = 0;

					$biayajabthr_rapel = floor(($bruto_setahun + $thr_lalu) * 0.05); // by jab thr rapel setahun
					//$biayajabthr_rapel = floor($biayajabthr_rapel / $pembagi_bulan); // by jab thr rapel sebulan
					if ($biayajabthr_rapel > (500000 * $pembagi_bulan)) {
						$biayajabthr_rapel = 500000 * $pembagi_bulan;
					}
					if ($biayajabthr_rapel < 0) {
						$biayajabthr_rapel = 0;
					}

					$netto_thr = $bruto_setahun + $thr_lalu - ($biayajabthr_rapel + ($jhtkarx *$pembagi_bulan) + ($jpkarx * $pembagi_bulan));
					$pkpthr_rapel = floor(($netto_thr - $ptkp[$val[0]]) / 1000) * 1000;


					//bagian thr rapel
					$pph21_thr_rapel = 0;
					$zz_thr_rapel = 0;
					$sisazz_thr_rapel = 0;
					if (0 < $pkpthr_rapel) {
						if ($pkpthr_rapel < $pphtarif[0]) {
							$zz_thr_rapel += $pphpercent[0] * $pkpthr_rapel;
							$sisazz_thr_rapel = 0;
						} else {
							if ($pphtarif[0] <= $pkpthr_rapel) {
								$zz_thr_rapel += $pphpercent[0] * $pphtarif[0];
								$sisazz_thr_rapel = $pkpthr_rapel - $pphtarif[0];
								if ($sisazz_thr_rapel < $pphtarif[1] - $pphtarif[0]) {
									$zz_thr_rapel += $pphpercent[1] * $sisazz_thr_rapel;
									$sisazz_thr_rapel = 0;
								} else {
									if ($pphtarif[1] - $pphtarif[0] <= $sisazz_thr_rapel) {
										$zz_thr_rapel += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
										$sisazz_thr_rapel = $pkpthr_rapel - $pphtarif[1];
										if ($sisazz_thr_rapel < $pphtarif[2] - $pphtarif[1]) {
											$zz_thr_rapel += $pphpercent[2] * $sisazz_thr_rapel;
											$sisazz_thr_rapel = 0;
										} else {
											if ($pphtarif[2] - $pphtarif[1] <= $sisazz_thr_rapel) {
												$zz_thr_rapel += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
												$sisazz_thr_rapel = $pkpthr_rapel - $pphtarif[2];
												if (0 < $sisazz_thr_rapel) {
													$zz_thr_rapel += $pphpercent[3] * $sisazz_thr_rapel;
												}
											}
										}
									}
								}
							}
						}
					}
					$pph21_thr_rapel = $zz_thr_rapel;
					if ('' == $npwp[$val[0]]) {
						$pph21_thr_rapel = ceil($pph21_thr_rapel + ($pph21_thr_rapel * 20) / 100) ;    		
					}
					if ($pph21_thr_rapel<0){
						$pph21_thr_rapel = 0;
					}
					$pph21_thr_rapel = $pph21_thr_rapel - $pph21_thrlalu - $pph21rapel_setahun;

/*
$brut= $bruto_setahun + $thr_lalu; 
$prdgjx= $valx['periodegaji'];
if ($idxx=='0000000032'){
	echo "warning pph21 thr rapel= ".$pph21_thr_rapel." /zz_thr rapel= ".$zz_thr_rapel." /pph21 rapel= ".$pphrapel." /pph21 thr lalu= ".$pph21_thrlalu." /by jab bns rapel=".$biayajabthr_rapel." /bruto rapel sebulan=".$bruto." /bruto rapel setahun=".$brut." /thrnya aja=".$thr_lalu." /netto thr rapel=".$netto_thr." /pkp thr rapel=".$pkpthr_rapel." /jhtx=".$jhtkarx." /jpx=".$jpkarx;
	exit();
}
*/
				} // end of cek THR rapel

			} // end of loop Rapel

			$pphrapel_x = $pphrapel - $akumulasi_pph21[$val[0]];
			if ($pphrapel_x < 0){$pphrapel_x = 0;}
			$pph21rapel[$val[0]] = $pphrapel_x + $pph21_bonus_rapel + $pph21_thr_rapel; // FA 20200503
			$pphsetahun[$val[0]] = $pphsetahun[$val[0]] + $pph21rapel[$val[0]];
		}

/*
$pp21= $pph21rapel[$val[0]];
$akum= $akumulasi_pph21[$val[0]];
if ($idxx=='0000000032'){
echo "warning XXX: bln= ".$nbln." /pph rapel setahun= ".$pph21rapel_setahun." /pphrapel= ".$pphrapel." /pph akum= ".$akum." /pph rapel akhir= ".$pp21." /pkp= ".$pkpx." /pph21 bonus rapel=".$pph21_bonus_rapel;
exit();
}
*/

		//kalo resign atau akhir tahun
/*		if( ($tanggalkeluar[$val[0]] != "" && $tanggalkeluar[$val[0]] != "0000-00-00") || $flag_desember == "Y"){ 
			$pphsetahun[$val[0]] = $pphsetahun[$val[0]] - $akumulasi_pph21[$val[0]];
		}
*/

		//kalo resign atau akhir tahun - FA20200604
		if (($tanggalkeluar[$val[0]] != "" && $tanggalkeluar[$val[0]] != "0000-00-00") || $flag_desember == "Y"){
			// THR & BONUS yg pernah diperoleh
			$bruto_thrbonus= 0;
			$qthrbonus = "select sum(jumlah) as tot_thr_bonus from sdm_gaji where substr(periodegaji,1,4) ='".substr($param['periodegaji'],0,4)."' and periodegaji < '".$param['periodegaji']."' and karyawanid = '".$idxx."' and idkomponen in (13,14) group by karyawanid";
			$resthrbonus = mysql_query($qthrbonus);
			while ($barthrbonus = mysql_fetch_object($resthrbonus)) {
				$bruto_thrbonus = $barthrbonus -> tot_thr_bonus;
			}

			$bruto_thrbonus = $bruto_thrbonus + $akumulasi_bruto; //tambahkan dgn akumulasi bruto
			$jht_jp= ($jhtkarypersen[$val[0]] + $jpkarypersen[$val[0]]) * $pembagi_bulan; // JHT+JP dikali bln bekerja dalam thn tsb

			$biayajab_resign = floor(($bruto_thrbonus) * 0.05); // by jab resign
			if ($biayajab_resign > (500000 * $pembagi_bulan)) {
				$biayajab_resign = 500000 * $pembagi_bulan;
			}
			if ($biayajab_resign < 0) {$biayajab_resign = 0;}
			
			$npkp_resign_akhirthn = floor(($bruto_thrbonus - $jht_jp - $biayajab_resign - $ptkp[$val[0]]) / 1000) * 1000;
			$pkp[$val[0]] = $npkp_resign_akhirthn;
			
			$pph21_resign_akhirthn = 0;
			$zz_resign_akhirthn = 0;
			$sisazz_resign_akhirthn = 0;
			if (0 < $npkp_resign_akhirthn) {
				if ($npkp_resign_akhirthn < $pphtarif[0]) {
					$zz_resign_akhirthn += $pphpercent[0] * $npkp_resign_akhirthn;
					$sisazz_resign_akhirthn = 0;
				} else {
					if ($pphtarif[0] <= $npkp_resign_akhirthn) {
						$zz_resign_akhirthn += $pphpercent[0] * $pphtarif[0];
						$sisazz_resign_akhirthn = $npkp_resign_akhirthn - $pphtarif[0];
						if ($sisazz_resign_akhirthn < $pphtarif[1] - $pphtarif[0]) {
							$zz_resign_akhirthn += $pphpercent[1] * $sisazz_resign_akhirthn;
							$sisazz_resign_akhirthn = 0;
						} else {
							if ($pphtarif[1] - $pphtarif[0] <= $sisazz_resign_akhirthn) {
								$zz_resign_akhirthn += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
								$sisazz_resign_akhirthn = $npkp_resign_akhirthn - $pphtarif[1];
								if ($sisazz_resign_akhirthn < $pphtarif[2] - $pphtarif[1]) {
									$zz_resign_akhirthn += $pphpercent[2] * $sisazz_resign_akhirthn;
									$sisazz_resign_akhirthn = 0;
								} else {
									if ($pphtarif[2] - $pphtarif[1] <= $sisazz_resign_akhirthn) {
										$zz_resign_akhirthn += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
										$sisazz_resign_akhirthn = $npkp_resign_akhirthn - $pphtarif[2];
										if (0 < $sisazz_resign_akhirthn) {
											$zz_resign_akhirthn += $pphpercent[3] * $sisazz_resign_akhirthn;
										}
									}
								}
							}
						}
					}
				}
			}
			$pph21_resign_akhirthn = $zz_resign_akhirthn;
			if ('' == $npwp[$val[0]]) {
				$pph21_resign_akhirthn = ceil($pph21_resign_akhirthn + ($pph21_resign_akhirthn * 20) / 100) ;    		
			}
			$pphsetahun[$val[0]] = 0;
			if ($pph21_resign_akhirthn>0){
				$pphsetahun[$val[0]] = $pph21_resign_akhirthn - $akumulasi_pph21[$val[0]];
			}

/*
$akum= $akumulasi_pph21[$val[0]];
$xyz= $pphsetahun[$val[0]];
if ($idxx=='0000000877'){
echo "warning RESIGN: "." /bruto_thrbonus= ".$bruto_thrbonus." /jht_jp= ".$jht_jp." /by jab= ".$biayajab_resign." /npkp_resign_akhirthn= ".$npkp_resign_akhirthn." /zz_resign_akhirthn= ".$zz_resign_akhirthn." /pph21_resign_akhirthn= ".$pph21_resign_akhirthn." /akumulasi pph21=".$akum." /pph21 akhirnya=".$xyz;
exit();
}
*/
		}
		
    	foreach ($pphsetahun as $idx => $row) {
    		if (0 < $row) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 24, 'jumlah' => round($row, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
    	}
		
		$pphsetahunThr[$val[0]] = round(($zz_thr - $zz));		
    	if ('' == $npwp[$val[0]]) {
    		$pphsetahunThr[$val[0]] = ceil($pphsetahunThr[$val[0]] + ($pphsetahunThr[$val[0]] * 20) / 100) ;    		
    	}
		/*FA 20200330*/
		if ($pphsetahunThr[$val[0]]<0){
			$pphsetahunThr[$val[0]] = 0;
		}
		foreach ($pphsetahunThr as $idx => $row) {
    		if (0 < $row) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 71, 'jumlah' => round($row, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
    	}
		
		$pphsetahunBonus[$val[0]] = round($zz_bonus - $zz);
    	if ('' == $npwp[$val[0]]) {
    		$pphsetahunBonus[$val[0]] = ceil($pphsetahunBonus[$val[0]] + ($pphsetahunBonus[$val[0]] * 20) / 100) ;    		
    	}
		/*FA 20200330*/
		if ($pphsetahunBonus[$val[0]]<0){
			$pphsetahunBonus[$val[0]] = 0;
		}
		foreach ($pphsetahunBonus as $idx => $row) {
    		if (0 < $row) {
    			$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 72, 'jumlah' => round($row, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    		}
    	}
		
    	//$thpbruto[$val[0]] = $totgross[$val[0]] - $pphsetahun[$val[0]];
    	$thpbruto[$val[0]] = ($totgross[$val[0]] + $thrvalue[$val[0]] + $bonusvalue[$val[0]]) - $pphsetahun[$val[0]] - $pphsetahunThr[$val[0]] - $pphsetahunBonus[$val[0]] + $rapelupah[$val[0]];
		$thpnetto[$val[0]] = $thpbruto[$val[0]]  + $insentif_pph21[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]] - $potonganbpjskes[$val[0]] - $potongankaryawan[$val[0]] - $potonganegrek[$val[0]] -  $potonganangkong[$val[0]];

		//$thpnetto[$val[0]] = $thpbruto[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]] - $potonganbpjskes[$val[0]];
		//$thpnetto[$val[0]] = $thpbruto[$val[0]] - $potongankaryawan[$val[0]] - $potonganegrek[$val[0]] -  $potonganangkong[$val[0]] - $potongandenda[$val[0]] - $potdendapanen[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]] - $potonganbpjskes[$val[0]];

		// PESANGON (FA 20200930)
		$pesangon[$val[0]] = 0;
		$pph21pesangon[$val[0]] = 0;
		//$pesangon = 0;
		//$pph21pesangon = 0;
		$sql = "select * from sdm_pesangonht where karyawanid='".$idxx."' and periodegaji='".$param['periodegaji']."'";
		$qstr = mysql_query($sql);
		$nx= mysql_num_rows($qstr);
		if ($nx>0){			
			while($rstr = mysql_fetch_array($qstr)){
			$psg = floor($rstr['total']);
			$pph21psg = floor($rstr['pph']);}
		} else {
			$psg = 0;
			$pph21psg = 0;
		}
		$pesangon[$val[0]] = $psg;
		$pph21pesangon[$val[0]] = $pph21psg;
		$np = $pesangon[$val[0]];
		$nph = $pph21pesangon[$val[0]];
/* if ($idxx=='0000000008'){
	echo "warning nx= ".$nx." /pesangon= ".$np." /pph21 pesangon= ".$nph;
	exit();
} */
		
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idxx, 'idkomponen' => 74, 'jumlah' => $psg, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		$readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idxx, 'idkomponen' => 73, 'jumlah' => $pph21psg, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
		// -----------------------------------------------------
		
    	$sisa[$val[0]] = 0;
    	if ($sisa[$val[0]] < 0) {
            $negatif = true;
			echo "Warning: Cek kembali inputan dan distribusi gaji, perhitungan menghasilkan nilai minus";
			exit();
        } else {
			//untuk mendapatkan nilai bruto (dipakai untuk csv)
			$arr_data_bruto_karyawan[$id[$val[0]][0]] = $totgross[$val[0]] + $jkk[$val[0]] + $jkm[$val[0]] + $bpjspt[$val[0]];			
        	++$no;
        	$list2 .= '<tr class=rowcontent>';
        	$list2 .= '<td>'.$no.'</td>';
        	$list2 .= '<td>'.$param['periodegaji'].'</td>';
        	$list2 .= '<td>'.$id[$val[0]][0].'</td>';
            $list2 .= '<td>'.$nik[$val[0]].'</td>'; //FA-20190411
            $list2 .= '<td>'.$namakar[$val[0]].'</td>';
            $list2 .= '<td align=left>'.$nmjabatan.'</td>';
            $list2 .= '<td align=left>'.$namatipe.'</td>';
            $list2 .= '<td align=center>'.$namagolongan.'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($gapok[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjgol[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjab[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjnat[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($gapoktunj[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjprestasi[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow"><b>'.number_format($totuptetap[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlembur[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjpremi[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($pendapatanlain[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjkehadiran[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjharian[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlain[$val[0]], 0, ',', '.').'</td>';
            
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganhk[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potdendapanen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potongandenda[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganlainnya[$val[0]], 0, ',', '.').'</b></td>';
			$list2 .= '<td align=right style="background-color:lightgrey"><b>'.number_format($totgross[$val[0]], 0, ',', '.').'</b></td>';

			$list2 .= '<td align=right style="background-color:lightblue">'.number_format($jkk[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($jkm[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($bpjspt[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey"><b>'.number_format($totgajibruto[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($rapelupah[$val[0]], 0, ',', '.').'</td>';      $list2 .= '<td align=right style="background-color:lightblue">'.number_format($thrvalue[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($bonusvalue[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($biayajab[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey"><b>'.number_format($gjnettosebulan[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right align=right style="background-color:lightgrey"><b>'.number_format($gjnettosetahun[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:grey">'.number_format($ptkp[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:grey">'.number_format($pkp[$val[0]], 0, ',', '.').'</td>';
//		if ($rapelupah[$val[0]] > 0){	
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pphsetahun[$val[0]] - $pph21rapel[$val[0]], 0, ',', '.').'</b></td>';
//		} else {
//            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pphsetahun[$val[0]], 0, ',', '.').'</b></td>';
//		}
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pph21rapel[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pphsetahunThr[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pphsetahunBonus[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:cyan"><b>'.number_format($thpbruto[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($insentif_pph21[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganbpjskes[$val[0]], 0, ',', '.').'</b></td>';
			$list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potongankaryawan[$val[0]], 0, ',', '.').'</b></td>';            
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganegrek[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganangkong[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#55fc7f"><b>'.number_format($thpnetto[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($pesangon[$val[0]], 0, ',', '.').'</b></td>';
			$list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($pph21pesangon[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jhtptpersen55[$val[0]], 0, ',', '.').'</b></td>';
			$list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jpptpersen56[$val[0]], 0, ',', '.').'</b></td></tr>';

			$thrvalue2 += $thrvalue[$val[0]];
			$bonusvalue2 += $bonusvalue[$val[0]];
        }
		$pesangon2 += $pesangon[$val[0]];
		$pph21pesangon2 += $$pph21pesangon[$val[0]];
		$jhtptpersen255+= $jhtptpersen55[$val[0]];
		$jpptpersen256+= $jpptpersen56[$val[0]];
        $gapok2 += $gapok[$val[0]];
        $tunjgol2 += $tunjgol[$val[0]];
        $tunjab2 += $tunjab[$val[0]];
        $tunjnat2 += $tunjnat[$val[0]];
        $tunjprestasi2 += $tunjprestasi[$val[0]];
        $gapoktunj2 += $gapoktunj[$val[0]];
        $totuptetap2 += $totuptetap[$val[0]];
        $tunjlembur2 += $tunjlembur[$val[0]];
        $tunjpremi2 += $tunjpremi[$val[0]];
        $tunjprt2 += $tunjprt[$val[0]];
        $tunjrapel2 += $tunjrapel[$val[0]];
        $premPengawas2 += $premPengawas[$val[0]];
        $tunjkom2 += $tunjkom[$val[0]];
        $pendapatanlain2 += $pendapatanlain[$val[0]];
        $tunjrt2 += $tunjrt[$val[0]];
        $tunjkehadiran2 += $tunjkehadiran[$val[0]];
        $tunjairminum2 += $tunjairminum[$val[0]];
        $tunjSP2 += $tunjSP[$val[0]];
        $tunjharian2 += $tunjharian[$val[0]];
        //$tunjdinas2 += $tunjdinas[$val[0]];
        $tunjcuti2 += $tunjcuti[$val[0]];
        $tunjlistrik2 += $tunjlistrik[$val[0]];
        $tunjlain2 += $tunjlain[$val[0]];
        $rapelupah2 += $rapelupah[$val[0]];
       
		$totgross2 += $totgross[$val[0]];
        $jkk2 += $jkk[$val[0]];
        $jkm2 += $jkm[$val[0]];
        $bpjspt2 += $bpjspt[$val[0]];
        $totgajibruto2 += $totgajibruto[$val[0]];
        $biayajab2 += $biayajab[$val[0]];
        $jhtkarypersen2 += $jhtkarypersen[$val[0]];
        $jpkarypersen2 += $jpkarypersen[$val[0]];
        $gjnettosebulan2 += $gjnettosebulan[$val[0]];
        $gjnettosetahun2 += $gjnettosetahun[$val[0]];
        $ptkp2 = 0;
        $pkp2 = 0;
        $pphsetahun212 += $pphsetahun[$val[0]];
		$insentif_pph212 += $insentif_pph21[$val[0]];
        $pphrapel212 += $pph21rapel[$val[0]];
        $pphsetahun212Thr += $pphsetahunThr[$val[0]];
        $pphsetahun212Bonus += $pphsetahunBonus[$val[0]];
        $thpbruto2 += $thpbruto[$val[0]];
        $potonganegrek2 += $potonganegrek[$val[0]];
        $potonganlainnya2 += $potonganlainnya[$val[0]];
        $potonganhk2 += $potonganhk[$val[0]];
        $potongankaryawan2 += $potongankaryawan[$val[0]];
        $potonganangkong2 += $potonganangkong[$val[0]];
        $potongandenda2 += $potongandenda[$val[0]];
        $potdendapanen2 += $potdendapanen[$val[0]];
        $potonganbpjskes2 += $potonganbpjskes[$val[0]];
        $thpnetto2 += $thpnetto[$val[0]];
    }
	// Ini untuk totalnya
/*
	if ($rapelupah2 > 0){
		$pphsetahun212 = $pphsetahun212 - $pphrapel212;
	}
*/	

    $list3 = 
	'<tr class=rowcontent style="font-size:12pt">
	<td align=center style="background-color:grey;font-size:12pt" colspan=8><b>'.$_SESSION['lang']['total']."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($gapok2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($tunjgol2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($tunjab2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($tunjnat2, 0, ',', '.')."</b></td>\r\n                  
	<td align=right style=\"background-color:yellow\"><b>".number_format($gapoktunj2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($tunjprestasi2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:yellow\"><b>".number_format($totuptetap2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlembur2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($tunjpremi2, 0, ',', '.')."</b></td>\r\n 
	<td align=right style=\"background-color:lightblue\"><b>".number_format($pendapatanlain2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjkehadiran2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjharian2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlain2, 0, ',', '.')."</b></td>\r\n               
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganhk2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potdendapanen2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potongandenda2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganlainnya2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($totgross2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($jkk2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightgrey\"><b>".number_format($jkm2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightgrey\"><b>".number_format($bpjspt2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightgrey\"><b>".number_format($totgajibruto2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($rapelupah2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightblue\"><b>".number_format($thrvalue2, 0, ',', '.')."</b></td>\r\n               
	<td align=right style=\"background-color:lightblue\"><b>".number_format($bonusvalue2, 0, ',', '.')."</b></td>\r\n              
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($biayajab2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jhtkarypersen2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jpkarypersen2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightgrey\"><b>".number_format($gjnettosebulan2, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:lightgrey\"><b>".number_format($gjnettosetahun2, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:grey\"><b>".number_format($ptkp2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:grey\"><b>".number_format($pkp2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightcyan\"><b>".number_format($pphsetahun212 - $pphrapel212, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightcyan\"><b>".number_format($pphrapel212, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightcyan\"><b>".number_format($pphsetahun212Thr, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:lightcyan\"><b>".number_format($pphsetahun212Bonus, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:cyan\"><b>".number_format($thpbruto2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:lightcyan\"><b>".number_format($insentif_pph212, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jhtkarypersen2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jpkarypersen2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganbpjskes2, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potongankaryawan2, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganegrek2, 0, ',', '.')."</b></td>\r\n	
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganangkong2, 0, ',', '.')."</b></td>\r\n  
	<td align=right style=\"background-color:#55fc7f\"><b>".number_format($thpnetto2, 0, ',', '.')."</b></td>\r\n          	
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($pesangon2, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($pph21pesangon2, 0, ',', '.')."</b></td>\r\n
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jhtptpersen255, 0, ',', '.')."</b></td>\r\n                
	<td align=right style=\"background-color:#efc6b1\"><b>".number_format($jpptpersen256, 0, ',', '.')."</b></td>\r\n
	</tr></tbody><table>";
	
	switch ($proses) {
    	case 'list':
/*
    	if ($negatif) {
    		echo $listx.$list0.$list1.$list3;
    	} else {
    		echo $listbutton.$list0.$list2.$list3;
    	}
*/		
//   		echo $listbutton.$list0.$list2.$list3;
   		echo $listbutton.$list0.$list2.$list3;
		break;

    	case 'post':
    	$insError = '';
    	foreach ($readyData as $row) {
    		if (0 == $row['jumlah'] || '' == $row['jumlah']) {
    			continue;
    		}
    		$queryIns = insertQuery($dbname, 'sdm_gaji', $row);
    		if (!mysql_query($queryIns)) {
    			$queryUpd = updateQuery($dbname, 'sdm_gaji', $row, "kodeorg='".$row['kodeorg']."' and periodegaji='".$row['periodegaji']."' and karyawanid='".$row['karyawanid']."' and idkomponen=".$row['idkomponen']);
    			$tmpErr = mysql_error();
    			if (!mysql_query($queryUpd)) {
    				echo 'DB Insert Error :'.$tmpErr."\n";
    				echo 'DB Update Error :'.mysql_error()."\n";
    			}
    		}
			if( $row['idkomponen'] == "1"){ //ambil yg gapok
				//simpen nilai bruto nya
				if( isset( $arr_data_bruto_karyawan[$row['karyawanid']] ) ){
					$sql = "insert into ".$dbname.".sdm_bruto_pajak (`kodeorg`,`periodegaji`, `karyawanid`, `amount`,`updateby`,`updatetime`)\r\n                                                  values ('".$row['kodeorg']."','".$row['periodegaji']."','".$row['karyawanid']."','".$arr_data_bruto_karyawan[$row['karyawanid']]."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE amount = '".$arr_data_bruto_karyawan[$row['karyawanid']]."',updateby='".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
					if (mysql_query($sql)) {						
						//kondisi benar	
						//echo "success...";						
					} else {
						echo 'DB Error : '.mysql_error($conn).$sql."<br>";
					}
				}else{
					//echo "faill..";
				}					
			}
    	}

    	break;
    	default:
    	break;
    }
	
} else {
	exit("Anda tidak berhak melakukan proses ini");
}

// echo $totuptetap['1000000077']+$premPengawas['1000000077'];
?>