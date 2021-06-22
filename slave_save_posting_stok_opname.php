<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
#showerror();
#echo pre($_POST); exit();
$id		= isset($_POST['id']) ? $_POST['id'] : '';
$Status	= isset($_POST['Status']) ? $_POST['Status'] : '';
$method	= isset($_POST['method']) ? $_POST['method'] : '';
$prd	= isset($_POST['Periode']) ? $_POST['Periode'] : '';

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[0].'-'.$qwe[1];
} 

## GET FUNCTION ##
function GetLastCntrNoJurnal($SrchNoJurnal){
	global $dbname;

	$String = "SELECT count(*) as NoCounter	FROM ".$dbname.".keu_jurnalht WHERE nojurnal like '".$SrchNoJurnal."%'";

	$Result = fetchData($String);
	$NoCounter = $Result[0]['NoCounter'] + 1;
	
	if($NoCounter < 10) {
		$Return = '000'.$NoCounter;
	} elseif( $NCRN > 10 && $NCRN < 100){
		$Return = '00'.$NoCounter;
	} elseif( $NCRN > 100 && $NCRN < 1000){
		$Return = '0'.$NoCounter;
	} else {
		$Return = $NoCounter;
	}
	
	return $Return;
}
function GetHeaderStokOpname($id){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".log_5stokopnameht WHERE id = '".$id."'";

	$Result = fetchData($String);	
	return $Result[0];
}
function GetTotalDebet($Reffno){
	global $dbname;
	
	$String = "
	SELECT sum(a.qtybalance*f.hargalastout) as nilai,
	(SELECT xx.noakun FROM keu_5akun xx WHERE xx.stockopname=1 LIMIT 1) as noakun,
	(SELECT xx.namaakun FROM keu_5akun xx WHERE xx.stockopname=1 LIMIT 1) as kelompokbiaya
	FROM log_5stokopnamedt a
	INNER JOIN log_5masterbarang b ON a.kdbarang=b.kodebarang 
	INNER JOIN log_5klbarang c ON b.kelompokbarang=c.kode
	INNER JOIN log_5stokopnameht d ON a.reffno=d.reffno
	INNER JOIN organisasi e ON e.kodeorganisasi=d.kdunit
	LEFT JOIN log_5masterbarangdt f ON b.kodebarang=f.kodebarang AND e.induk=f.kodeorg
	WHERE a.reffno = '".$Reffno."'";
	
	$Result = fetchData($String);
	#pre($String);exit();
	return $Result[0]['nilai'];
}
function GetSODetailForJurnalUntung($Reffno, $Periode){
	global $dbname;
	
	$String = "SELECT DISTINCT 
	case when -1*(a.qtybalance*h.hargarata)<0 then 'K' else 'D' end AS DEBET,
	-1*(a.qtybalance*h.hargarata)  as nilai,
	c.noakun,g.namaakun as kelompokbiaya,
	d.kdunit,a.kdbarang,d.kdgudang, a.qtyso, a.qtybalance, a.kdsatuan,
	b.namabarang,e.induk
	FROM `log_5stokopnamedt` a 
	LEFT JOIN log_5masterbarang b on a.kdbarang=b.kodebarang
	LEFT JOIN log_5klbarang c on b.kelompokbarang=c.kode
	LEFT JOIN log_5stokopnameht d on a.reffno=d.reffno
	LEFT JOIN organisasi e on d.kdunit=e.kodeorganisasi
	LEFT JOIN log_5masterbarangdt f on e.induk=f.kodeorg AND a.kdbarang=f.kodebarang AND d.kdgudang=f.kodegudang
	LEFT JOIN keu_5akun g on c.noakun=g.noakun 
	LEFT JOIN log_5saldobulanan h on e.induk = h.kodeorg AND a.kdbarang = h.kodebarang AND d.kdgudang = h.kodegudang AND h.periode <= '".$Periode."'
	WHERE a.reffno = '".$Reffno."' and ifnull(a.qtybalance,0)<0 
	UNION ALL
	SELECT DISTINCT 
	case when sum(-1*(a.qtybalance*h.hargarata))>0 then 'K' else 'D' end AS DEBET,
	CASE WHEN sum(-1*(a.qtybalance*h.hargarata))>0 THEN -1*sum(-1*(a.qtybalance*h.hargarata))/2 ELSE sum(-1*(a.qtybalance*h.hargarata))/2 END  as nilai,
	( SELECT xx.noakun FROM	keu_5akun xx WHERE	xx.stockopname = 1	LIMIT 1	) AS noakun,
	( SELECT xx.namaakun FROM keu_5akun xx WHERE xx.stockopname = 1	LIMIT 1	)  as kelompokbiaya,
	' ' as kdunit,' ' as kdbarang,d.kdgudang, a.qtyso, a.qtybalance, a.kdsatuan, b.namabarang,e.induk
	FROM `log_5stokopnamedt` a 
	LEFT JOIN log_5masterbarang b on a.kdbarang=b.kodebarang
	LEFT JOIN log_5klbarang c on b.kelompokbarang=c.kode
	LEFT JOIN log_5stokopnameht d on a.reffno=d.reffno
	LEFT JOIN organisasi e on d.kdunit=e.kodeorganisasi
	LEFT JOIN log_5masterbarangdt f on e.induk=f.kodeorg AND a.kdbarang=f.kodebarang AND d.kdgudang=f.kodegudang
	LEFT JOIN keu_5akun g on c.noakun=g.noakun 
	LEFT JOIN log_5saldobulanan h on e.induk = h.kodeorg AND a.kdbarang = h.kodebarang AND d.kdgudang = h.kodegudang AND h.periode <= '".$Periode."'
	WHERE a.reffno = '".$Reffno."' and ifnull(a.qtybalance,0)<0 ;";
	
	$Result = fetchData($String);
	$NewResult['TotalDebet'] = 0;
	$NewResult['TotalKredit'] = 0;
	$NewResult[0] = $Result;
	foreach($Result as $Key => $Value){
		if($Value['DEBET'] == 'D'){
			$NewResult['TotalDebet'] += $Value['nilai'];
		} else if($Value['DEBET'] == 'K'){
			$NewResult['TotalKredit'] += $Value['nilai'];
		}
		
	}
	#pre($String);exit();
	return $NewResult;
}
function GetSODetailForJurnalRugi($Reffno, $Periode){
	global $dbname;
	
	$String = "SELECT DISTINCT 
	case when -1*(a.qtybalance*h.hargarata)<0 then 'K' else 'D' end AS DEBET,
	-1*(a.qtybalance*h.hargarata)  as nilai,
	c.noakun,g.namaakun as kelompokbiaya,
	d.kdunit,a.kdbarang,d.kdgudang, a.qtyso, a.qtybalance, a.kdsatuan,
	b.namabarang,e.induk
	FROM `log_5stokopnamedt` a 
	LEFT JOIN log_5masterbarang b on a.kdbarang=b.kodebarang
	LEFT JOIN log_5klbarang c on b.kelompokbarang=c.kode
	LEFT JOIN log_5stokopnameht d on a.reffno=d.reffno
	LEFT JOIN organisasi e on d.kdunit=e.kodeorganisasi
	LEFT JOIN log_5masterbarangdt f on e.induk=f.kodeorg AND a.kdbarang=f.kodebarang AND d.kdgudang=f.kodegudang
	LEFT JOIN keu_5akun g on c.noakun=g.noakun 
	LEFT JOIN log_5saldobulanan h on e.induk = h.kodeorg AND a.kdbarang = h.kodebarang AND d.kdgudang = h.kodegudang AND h.periode <= '".$Periode."'
	WHERE a.reffno = '".$Reffno."' and ifnull(a.qtybalance,0)>0 
	UNION ALL
	SELECT DISTINCT 
	case when sum((a.qtybalance*h.hargarata))>0 then 'D' else 'K' end AS DEBET,
	CASE WHEN sum((a.qtybalance*h.hargarata))>0 THEN -1*sum(-1*(a.qtybalance*h.hargarata))/2 ELSE sum(-1*(a.qtybalance*h.hargarata))/2 END  as nilai,
	( SELECT xx.noakun FROM	keu_5akun xx WHERE	xx.stockopname = 1	LIMIT 1	) AS noakun,
	( SELECT xx.namaakun FROM keu_5akun xx WHERE xx.stockopname = 1	LIMIT 1 )  as kelompokbiaya,' ' as kdunit,' ' as kdbarang,d.kdgudang, a.qtyso, a.qtybalance, a.kdsatuan,
	b.namabarang,e.induk
	FROM `log_5stokopnamedt` a 
	LEFT JOIN log_5masterbarang b on a.kdbarang=b.kodebarang
	LEFT JOIN log_5klbarang c on b.kelompokbarang=c.kode
	LEFT JOIN log_5stokopnameht d on a.reffno=d.reffno
	LEFT JOIN organisasi e on d.kdunit=e.kodeorganisasi
	LEFT JOIN log_5masterbarangdt f on e.induk=f.kodeorg AND a.kdbarang=f.kodebarang AND d.kdgudang=f.kodegudang
	LEFT JOIN keu_5akun g on c.noakun=g.noakun 
	LEFT JOIN log_5saldobulanan h on e.induk = h.kodeorg AND a.kdbarang = h.kodebarang AND d.kdgudang = h.kodegudang AND h.periode <= '".$Periode."'
	WHERE a.reffno = '".$Reffno."' and ifnull(a.qtybalance,0)>0 ;";
	
	$Result = fetchData($String);
	$NewResult = '';
	$NewResult[0] = $Result;
	$NewResult['TotalDebet'] = 0;
	$NewResult['TotalKredit'] = 0;
	foreach($Result as $Key => $Value){
		if($Value['DEBET'] == 'D'){
			$NewResult['TotalDebet'] += $Value['nilai'];
		} else if($Value['DEBET'] == 'K'){
			$NewResult['TotalKredit'] += $Value['nilai'];
		}
		
	}
	#pre($String);exit();
	return $NewResult;
}
function GetMasterBarangDt($KodeOrg, $KodeBrg, $KodeGudang, $Periode){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".log_5saldobulanan 
	WHERE kodeorg = '".$KodeOrg."' and kodebarang = '".$KodeBrg."' and kodegudang = '".$KodeGudang."' and periode = '".$Periode."'";

	$Result = fetchData($String);	
	#pre($String);exit();
	return $Result;
}
function GetIndukOrganisasi($kdunit){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".organisasi 
	WHERE kodeorganisasi = '".$kdunit."'";

	$Result = fetchData($String);	
	#pre($String);exit();
	return $Result[0]['induk'];
}
function CekTransaksiHt($TipeTransaksi, $NoTransaksi){
	global $dbname;

	$String = "SELECT count(*) as TotalNo FROM ".$dbname.".log_transaksiht WHERE tipetransaksi = '".$TipeTransaksi."' AND notransaksireferensi = '".$NoTransaksi."'";

	$Result = fetchData($String);
	#pre($String);#exit();
	return $Result;
}
function GenerateNoTransaksi($KodeGudang,$Tipe){
	global $dbname;
	
	$DateYM = date('Ym');
	$String = "SELECT
		notransaksi
	FROM
		".$dbname.".log_transaksiht
	WHERE
		notransaksi LIKE '".$DateYM."%'
	AND kodegudang = '".$KodeGudang."'
	AND notransaksi LIKE '%".$Tipe."%'
	ORDER BY
		notransaksi DESC
	LIMIT 1";
	$Result = fetchData($String);
	#pre($String);#exit();
	#pre($Result);#exit();
	$num=$Result[0]['notransaksi'];
	if($num!=''){
		$num=intval(substr($num,6,5))+1;
	} else {
		$num=1;	
	}
	
	if($num<10)
		$num='0000'.$num;
	else if($num<100)
		$num='000'.$num;
	else if($num<1000)
		$num='00'.$num;	   
	else if($num<10000)
		$num='0'.$num;
	else
		$num=$num;
	
	$NoTransaksi = date('Ym').$num.'-'.$Tipe.'-'.$KodeGudang;
	return $NoTransaksi;
}
function GenerateNoTransaksiLama($KodeGudang,$Tipe){
	global $dbname;
	
	$DateYM = date('Ym');
	$String = "SELECT
		notransaksi
	FROM
		".$dbname.".log_transaksiht
	WHERE
		notransaksi LIKE '".$DateYM."%'
	AND kodegudang = '".$KodeGudang."'
	AND notransaksi LIKE '%".$Tipe."%'
	ORDER BY
		notransaksi DESC
	LIMIT 1";
	$Result = fetchData($String);
	$NoTransaksi = $Result[0]['notransaksi'];
	return $NoTransaksi;
}
function GetMasterBarangDt2($KodeOrg, $KodeBrg, $KodeGudang){
global $dbname;

$String = "SELECT *	FROM ".$dbname.".log_5masterbarangdt 
WHERE kodeorg = '".$KodeOrg."' and kodebarang = '".$KodeBrg."' and kodegudang = '".$KodeGudang."'";

$Result = fetchData($String);	
return $Result[0];
}
## SET FUNCTION ##
function RollBackStockOpname($id){
	global $dbname;
	
	$UpdateSO = array(
		'status' => '0'
	);
	$WhereUpdateSO = "id = '".$id."'";
	if(!mysql_query(updateQuery($dbname,'log_5stokopnameht',$UpdateSO,$WhereUpdateSO))){
		echo "ERR0R : Gagal Rollback log_5stokopnameht.";
		exit();
	}
}

function RollBackJurnalHt($NoJurnal){
	global $dbname;
	
	$Where = "nojurnal = '".$NoJurnal."'";
	if(!mysql_query(deleteQuery($dbname,'keu_jurnalht',$Where))){
		echo "ERR0R : Gagal Rollback log_5stokopnameht.";
		exit();
	}
}

function RollBackJurnalDt($NoJurnal){
	global $dbname;
	
	$Where = "nojurnal = '".$NoJurnal."'";
	if(!mysql_query(deleteQuery($dbname,'keu_jurnaldt',$Where))){
		echo "ERR0R : Gagal Rollback log_5stokopnamedt.";
		exit();
	}
}

function RollBackBarangDt($qtymasuk,$saldoakhirqty,$nilaisaldoakhir,$qtymasukxharga,$IndukOrganisasi,$kdbarang,$kdgudang,$Periode){
	global $dbname;
	
	$UpdateMBarangDt = array();
	$UpdateMBarangDt = array(
		'qtymasuk' => $qtymasuk,
		'saldoakhirqty' => $saldoakhirqty,//$DataMBarangDt[0]['saldoakhirqty'];
		'nilaisaldoakhir' => $nilaisaldoakhir,//$DataMBarangDt[0]['nilaisaldoakhir'];
		'qtymasukxharga' => $qtymasukxharga
	);
	$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$kdbarang."' and kodegudang = '".$kdgudang."' and periode = '".$Periode."'";
	if(!mysql_query(updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt))){
		echo "ERR0R : Gagal Rollback log_5saldobulanan.";
		exit();
	}
}

function RollBackTransaksiHt($NoTransaksi){
	global $dbname;
	
	$String = "SELECT notransaksi FROM ".$dbname.".log_transaksiht
	WHERE notransaksireferensi = '".$NoTransaksi."'";
	$DataHt = fetchData($String);	
	#pre($String);exit();
	$Where = "notransaksireferensi = '".$NoTransaksi."'";
	if(!mysql_query(deleteQuery($dbname,'log_transaksiht',$Where))){
		echo "ERR0R : Gagal Rollback log_transaksiht.";
		exit();
	} else {
		foreach($DataHt as $Key => $Val){
			RollBackTransaksiDt($Val['notransaksi']);
		}
	}
}

function RollBackTransaksiDt($NoTransaksi){
	global $dbname;
	
	$Where = "notransaksi = '".$NoTransaksi."'";
	if(!mysql_query(deleteQuery($dbname,'log_transaksidt',$Where))){
		#echo "ERR0R : Gagal Rollback log_transaksidt.";
		#exit();
	}
}

function RollBackAll($NoJurnal,$id,$periode){
	global $dbname;
	
	RollBackStockOpname($id);
	RollBackJurnalHt($NoJurnal);
	RollBackJurnalDt($NoJurnal);

	$StokOpname = GetDetailStokOpname($id);
	foreach($StokOpname as $Key => $SODVal){
		$IndukOrganisasi = GetIndukOrganisasi($SODVal['kdunit']);
				
		$DataMBarangDt = GetMasterBarangDt($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang'], $periode);
		$Balance = -1 * $SODVal['qtybalance'];
		$QtyMasuk = $DataMBarangDt[0]['qtymasuk'] + $Balance;
		$QtyMasukxharga = $QtyMasuk * $DataMBarangDt[0]['hargarata'];
		$SaldoAkhirQty = $SODVal['qtysaldo'];
		$NilaiSaldoAkhir = $SaldoAkhirQty * $DataMBarangDt[0]['hargarata'];
				
		$UpdateMBarangDt = array();
		$UpdateMBarangDt = array(
			'qtymasuk' => $QtyMasuk,//$DataMBarangDt[0]['qtymasuk'];
			'saldoakhirqty' => $SaldoAkhirQty,//$DataMBarangDt[0]['saldoakhirqty'];
			//'nilaisaldoakhir' => $NilaiSaldoAkhir,//$DataMBarangDt[0]['nilaisaldoakhir'];
			//'qtymasukxharga' => $QtyMasukxharga,//$DataMBarangDt[0]['qtymasukxharga'];
			'lastuser' => $_SESSION['empl']['karyawanid']
		);
		$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."' and periode = '".$periode."'";
		if(!mysql_query(updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt))){
			#RollBackBarangDt($DataMBarangDt[0]['qtymasuk'],$DataMBarangDt[0]['saldoakhirqty'],$DataMBarangDt[0]['nilaisaldoakhir'],$DataMBarangDt[0]['qtymasukxharga'],$IndukOrganisasi,$SODVal['kdbarang'],$SODVal['kdgudang'],putertanggal($_POST['Periode']));
			echo "ERR0R : Roll BacK Gagal.";
			echo updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt);
			exit();
		}
		
		RollBackTransaksiHt($SODVal['reffno']);
		#RollBackTransaksiDt($SODVal['reffno']);
						
		## UPDATE MASTER BARANG DT ##
		#$DataMBarangDt2 = GetMasterBarangDt2($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang']);
		$UpdateMBarangDt = array(
			'saldoqty' => $SaldoAkhirQty,
			'lastuser' => $_SESSION['empl']['karyawanid']
		);
		$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."'";
		if(!mysql_query(updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt))){
			echo "ERR0R : Gagal RollBack Stok Barang.";
			echo updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt);
			exit();
		}		
	}
}

function GetDetailStokOpname($id){
	global $dbname;
	
	$String = "SELECT a.*,b.* FROM ".$dbname.".log_5stokopnameht a
	LEFT JOIN log_5stokopnamedt b on a.reffno=b.reffno
	WHERE a.id = '".$id."'";

	$Result = fetchData($String);	
	return $Result;
}

function GetAkunSO(){
	global $dbname;
	
	$String = "SELECT count(*) as total FROM ".$dbname.".keu_5akun WHERE stockopname = 1";
	$Result = fetchData($String);
	
	return $Result;
}

function GetAwalAkhirPeriode($Periode){
	global $dbname;
	
	$sPeriodeAkuntansi = "SELECT a.tanggalmulai, a.tanggalsampai FROM ".$dbname.".setup_periodeakuntansi a
						  INNER JOIN ".$dbname.".log_5stokopnameht b ON b.tanggal>=a.tanggalmulai AND b.tanggal<=a.tanggalsampai
						  WHERE a.kodeorg = '".$_SESSION['empl']['lokasitugas']."' 
						  and a.periode = '".$Periode."'
						  LIMIT 1";
	$qPeriodeAkuntansi = fetchData($sPeriodeAkuntansi);
	#pre($sPeriodeAkuntansi);
	return $qPeriodeAkuntansi;
}

function GetPeriodeAktif(){
	global $dbname;
	
	$sPeriodeAkuntansi = "SELECT a.tanggalmulai, a.tanggalsampai FROM ".$dbname.".setup_periodeakuntansi a
						  WHERE a.kodeorg = '".$_SESSION['empl']['lokasitugas']."' AND tutupbuku = 0
						  LIMIT 1";
	$qPeriodeAkuntansi = fetchData($sPeriodeAkuntansi);
	#pre($sPeriodeAkuntansi);
	return $qPeriodeAkuntansi;
}
switch($method){
	case'UpdateStatus':
		## Validasi ##
		$AkunSO = GetAkunSO();
		if($AkunSO[0]['total'] >= 2){
			echo "ERR0R : Akun stokopname lebih dari 1";
			exit();
		} else if($AkunSO[0]['total'] == 0){
			echo "ERR0R : Tidak ada Akun stokopname";
			exit();
		}
		#$AwalAkhirPeriode = GetAwalAkhirPeriode($_POST['Periode']);
		$PeriodeAktif = GetPeriodeAktif();
		/*$CekAllTransaksi = CekAllTransaksi($_POST['gudang'],$AwalAkhirPeriode[0]['tanggalmulai'],$AwalAkhirPeriode[0]['tanggalsampai']);
		if($CekAllTransaksi != 0){
			echo "ERR0R : Terdapat Transaksi yang belum di posting";
			exit();
		}*/
		
		#$PeriodeAwal = $AwalAkhirPeriode[0]['tanggalmulai'];
		#$PeriodeAkahir = $AwalAkhirPeriode[0]['tanggalsampai'];
		$HstokOpname = GetHeaderStokOpname($id);
		#$PeriodeAktifAwal = $PeriodeAktif[0]['tanggalmulai'];
		#$PeriodeAktifAkhir = $PeriodeAktif[0]['tanggalsampai'];
		
		/*if(strtotime($PeriodeAktifAwal)>strtotime($HstokOpname['tanggal']) || strtotime($PeriodeAktifAkhir)<strtotime($HstokOpname['tanggal'])){
			echo 'ERR0R: Tanggal dengan periode aktif gudang/unit tidak sama.';
			exit();
		}*/
	
		#echo 'ERR0R: '.$PeriodeAktifAwal.' '.$HstokOpname['tanggal'].' '.$PeriodeAktifAkhir;exit();
		#Update Data#
		$UpdateSO = array(
			'usrapv' => $_SESSION['empl']['name'],
			'usrapvdt' => date('Y-m-d H:i:s'),
			'status' => $Status
		);
		$WhereUpdateSO = "id = '".$id."'";
		if(!mysql_query(updateQuery($dbname,'log_5stokopnameht',$UpdateSO,$WhereUpdateSO))){
			echo "ERR0R : Gagal Update log_5stokopnameht.";
			exit();
		}
		
		if($Status == '3') {
			###Insert Jurnal Untung #####
			##Set Fields HT##
			$fieldsHt = array('nojurnal','kodejurnal','tanggal','tanggalentry','posting','totaldebet',
			'totalkredit','amountkoreksi','noreferensi','autojurnal','matauang','kurs','revisi');
			
			##Generate No Jurnal###
			$tgltrx=str_replace("-","",$HstokOpname['tanggal']);
			$SrchNoJurnal = $tgltrx.'/'.$_SESSION['empl']['lokasitugas'].'/SO/';
			$CntrNoJurnal = GetLastCntrNoJurnal($SrchNoJurnal);
			$NoJurnal	  = $SrchNoJurnal.$CntrNoJurnal;
			$NoJurnalUntung = $SrchNoJurnal.$CntrNoJurnal;
			
			##Get Data Stok Opname Header##
			$HeaderStokOpname = GetHeaderStokOpname($id);
			$SODetailForJurnal = GetSODetailForJurnalUntung($HeaderStokOpname['reffno'],putertanggal($prd));
			#echo pre($SODetailForJurnal);exit();
			if($SODetailForJurnal['TotalDebet'] != 0){
				##Set Total Debet & Kredit##
				$TotalDebet = $SODetailForJurnal['TotalDebet'];
				$TotalKredit = $SODetailForJurnal['TotalKredit'];
				
				##Set Data Ht##
				$dataHt['nojurnal'] 	= $NoJurnal;
				$dataHt['kodejurnal'] 	= 'SO';
				$dataHt['tanggal'] 		= $HeaderStokOpname['tanggal'];//date('Y-m-d');
				$dataHt['tanggalentry']	= $HeaderStokOpname['tanggal'];
				$dataHt['posting'] 		= 0;
				$dataHt['totaldebet'] 	= $TotalDebet;
				$dataHt['totalkredit'] 	= $TotalKredit;
				$dataHt['amountkoreksi'] = 0;
				$dataHt['noreferensi'] 	= $HeaderStokOpname['reffno'];
				$dataHt['autojurnal'] 	= 0;
				$dataHt['matauang'] 	= 'IDR';
				$dataHt['kurs'] 		= 1;
				$dataHt['revisi'] 		= 0;
						
				#pre($SODetailForJurnal[0]);exit();
				//$tgltrx
				if(mysql_query(insertQuery($dbname,'keu_jurnalht',$dataHt,$fieldsHt))){
					###Insert Details Jurnal###
					##Set Fields HT##
					$fieldsDt = array('nojurnal','tanggal','nourut','noakun','keterangan','jumlah',
					'matauang','kurs','kodeorg','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
					'kodesupplier','noreferensi','noaruskas','kodevhc','nodok','kodeblok','revisi');
					
					##Get Data SO Detail##
					$Count = 1;
					foreach($SODetailForJurnal[0] as $SODKey=>$SODVal){
						##Set Data Dt##
						$dataDt['nojurnal'] 	= $NoJurnal;
						$dataDt['tanggal'] 		= $HeaderStokOpname['tanggal'];//date('Y-m-d');
						$dataDt['nourut'] 		= $Count;
						$dataDt['noakun'] 		= $SODVal['noakun'];
						if($SODVal['kdbarang'] == ' '){
							$Keterangan = $SODVal['kdgudang'].'-'.$HeaderStokOpname['nostokopname'];
						} else {
							$Keterangan = $SODVal['kdgudang'].'-'.$HeaderStokOpname['nostokopname'].'-'.$SODVal['namabarang'];
						}
						$dataDt['keterangan'] 	= $Keterangan;
						$dataDt['jumlah'] 		= $SODVal['nilai'];
						$dataDt['matauang'] 	= 'IDR';
						$dataDt['kurs'] 		= 1;
						$dataDt['kodeorg'] 		= $_SESSION['empl']['lokasitugas'];
						$dataDt['kodekegiatan']	= '';
						$dataDt['kodeasset'] 	= '';
						$dataDt['kodebarang'] 	= $SODVal['kdbarang'];
						$dataDt['nik'] 			= '';
						$dataDt['kodecustomer']	= '';
						$dataDt['kodesupplier']	= '';
						$dataDt['noreferensi'] 	= $HeaderStokOpname['reffno'];
						$dataDt['noaruskas'] 	= '';
						$dataDt['kodevhc'] 		= '';
						$dataDt['nodok'] 		= '';
						$dataDt['kodeblok'] 	= '';
						$dataDt['revisi'] 		= 0;
						
						if(!mysql_query(insertQuery($dbname,'keu_jurnaldt',$dataDt,$fieldsDt))){
							echo "ERR0R : Gagal Menyimpan Jurnal Details.";
							RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
							echo insertQuery($dbname,'keu_jurnaldt',$dataDt,$fieldsDt);
							exit();
						} else {
							if(!empty($SODVal['kdbarang']) && $SODVal['kdbarang'] != ' ') {
								#echo $SODVal['kdbarang'];
								$IndukOrganisasi = GetIndukOrganisasi($SODVal['kdunit']);
								
								$DataMBarangDt = GetMasterBarangDt($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang'], putertanggal($prd));
								$Balance = -1 * $SODVal['qtybalance'];
								$QtyMasuk = $DataMBarangDt[0]['qtymasuk'] + $Balance;
								$QtyMasukxharga = $QtyMasuk * $DataMBarangDt[0]['hargarata'];
								$SaldoAkhirQty = $SODVal['qtyso'];
								$NilaiSaldoAkhir = $SaldoAkhirQty * $DataMBarangDt[0]['hargarata'];
								
								$UpdateMBarangDt = array();
								$UpdateMBarangDt = array(
									'qtymasuk' => $QtyMasuk,//$DataMBarangDt[0]['qtymasuk'];
									'saldoakhirqty' => $SaldoAkhirQty,//$DataMBarangDt[0]['saldoakhirqty'];
									'nilaisaldoakhir' => $NilaiSaldoAkhir,//$DataMBarangDt[0]['nilaisaldoakhir'];
									'qtymasukxharga' => $QtyMasukxharga,//$DataMBarangDt[0]['qtymasukxharga'];
									'lastuser' => $_SESSION['empl']['karyawanid'],
									'periode' => putertanggal($prd)
								);
								$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."' and periode = '".putertanggal($prd)."'";
								if(!mysql_query(updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt))){
									echo "ERR0R : Gagal Update Stok Barang.";
									RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
									#RollBackBarangDt($DataMBarangDt[0]['qtymasuk'],$DataMBarangDt[0]['saldoakhirqty'],$DataMBarangDt[0]['nilaisaldoakhir'],$DataMBarangDt[0]['qtymasukxharga'],$IndukOrganisasi,$SODVal['kdbarang'],$SODVal['kdgudang'],putertanggal($prd));
									echo updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt);
									exit();
								}
								
								### INSERT TRANSAKSI HT MASUKAN ###
								## Set Fields HT ##
								$FTransaksiHt = array('tipetransaksi','notransaksi','tanggal','kodept','untukpt','keterangan',
								'statusjurnal','kodegudang','user','post','postedby','notransaksireferensi');
								
								$TanggalSekarang = $HeaderStokOpname['tanggal'];//date('Y-m-d');
								$NoTransaksi = GenerateNoTransaksi($SODVal['kdgudang'],'GR');
								
								## Set Data HT ##
								$DTransaksiHt['tipetransaksi'] = 4;
								$DTransaksiHt['notransaksi'] = $NoTransaksi;
								$DTransaksiHt['tanggal'] = $TanggalSekarang;
								$DTransaksiHt['kodept'] = $IndukOrganisasi;
								$DTransaksiHt['untukpt'] = $IndukOrganisasi;
								$DTransaksiHt['keterangan'] = $HeaderStokOpname['nostokopname'];
								$DTransaksiHt['statusjurnal'] = 1;
								$DTransaksiHt['kodegudang'] = $SODVal['kdgudang'];
								$DTransaksiHt['user'] = $_SESSION['empl']['karyawanid'];
								$DTransaksiHt['post'] = 1;
								$DTransaksiHt['postedby'] = $_SESSION['empl']['karyawanid'];
								$DTransaksiHt['notransaksireferensi'] = $HeaderStokOpname['reffno'];
								
								$CekTransaksiHt = CekTransaksiHt(4, $HeaderStokOpname['reffno']);
								#
								if($CekTransaksiHt[0]['TotalNo'] == 0){
									if(!mysql_query(insertQuery($dbname,'log_transaksiht',$DTransaksiHt,$FTransaksiHt))){
										echo "ERR0R : Gagal Update Transaksi. Sebelum Insert HT. ";
										RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
										echo insertQuery($dbname,'log_transaksiht',$DTransaksiHt,$FTransaksiHt);
										exit();
									}
									echo 'New '.$SODVal['kdbarang'].'<br>';
									### INSERT TRANSAKSI DT MASUKAN ###
									## Set Fields DT ##
									$FTransaksiDt = array('notransaksi','kodebarang','satuan','jumlah','jumlahlalu','hargasatuan','kodeblok',
									'updateby','statussaldo','hargarata');
									
									## Set Data HT ##
									$DTransaksiDt['notransaksi'] = $NoTransaksi;
									$DTransaksiDt['kodebarang'] = $SODVal['kdbarang'];
									$DTransaksiDt['satuan'] = $SODVal['kdsatuan'];
									$DTransaksiDt['jumlah'] = $Balance;
									$DTransaksiDt['jumlahlalu'] = 0;
									$DTransaksiDt['hargasatuan'] = 1;
									$DTransaksiDt['kodeblok'] = 0;
									$DTransaksiDt['updateby'] = 0;
									$DTransaksiDt['statussaldo'] = 1;
									$DTransaksiDt['hargarata'] = 0;
									
									if(!mysql_query(insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt))){
										echo "ERR0R : Gagal Update Detail Transaksi. Sebelum Insert DT Untung 1";
										RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
										echo insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt);
										exit();
									}
								} else {
									echo 'Old '.$SODVal['kdbarang'].'<br>';
									### INSERT TRANSAKSI DT MASUKAN ###
									## Set Fields DT ##
									$FTransaksiDt = array('notransaksi','kodebarang','satuan','jumlah','jumlahlalu','hargasatuan','kodeblok',
									'updateby','statussaldo','hargarata');
									
									## Set Data HT ##
									// Ambil No Transaksi Lama
									$NoTransaksi2 = GenerateNoTransaksiLama($SODVal['kdgudang'],'GR');
									$DTransaksiDt['notransaksi'] = $NoTransaksi2;
									$DTransaksiDt['kodebarang'] = $SODVal['kdbarang'];
									$DTransaksiDt['satuan'] = $SODVal['kdsatuan'];
									$DTransaksiDt['jumlah'] = $Balance;
									$DTransaksiDt['jumlahlalu'] = 0;
									$DTransaksiDt['hargasatuan'] = 1;
									$DTransaksiDt['kodeblok'] = 0;
									$DTransaksiDt['updateby'] = 0;
									$DTransaksiDt['statussaldo'] = 1;
									$DTransaksiDt['hargarata'] = 0;
									
									if(!mysql_query(insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt))){
										echo "ERR0R : Gagal Update Detail Transaksi. Sebelum Insert DT Untung 2";
										RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
										echo insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt);
										exit();
									}
								}
								
								## UPDATE MASTER BARANG DT ##
								#$DataMBarangDt2 = GetMasterBarangDt2($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang']);
								$UpdateMBarangDt = array(
									'saldoqty' => $SaldoAkhirQty,
									'lastuser' => $_SESSION['empl']['karyawanid']
								);
								$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."'";
								if(!mysql_query(updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt))){
									echo "ERR0R : Gagal Update Stok Barang.";
									RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
									echo updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt);
									exit();
								}
								
								$sUpdateSB = "UPDATE log_5saldobulanan SET nilaisaldoakhir=saldoakhirqty*hargarata, 
								qtymasukxharga=qtymasuk*hargarata, qtykeluarxharga=qtykeluar*hargarata WHERE kodebarang = '".$SODVal['kdbarang']."' and periode <= '".$prd."'";
								if(!mysql_query($sUpdateSB)){
									echo "ERR0R : Gagal Update Saldo Bulanan";
									echo $sUpdateSB;
									exit();
								}
							}
						}
						$Count++;
					}					
				} else {
					echo "ERR0R : Gagal Insert Jurnal Header.";
					RollBackAll($NoJurnalUntung,$id,putertanggal($prd));
					echo insertQuery($dbname,'keu_jurnalht',$dataHt,$fieldsHt).' Insert Untung';
					exit();
				}
			}
			###Insert Jurnal Rugi #####
			##Set Fields HT##
			$fieldsHt = array('nojurnal','kodejurnal','tanggal','tanggalentry','posting','totaldebet',
			'totalkredit','amountkoreksi','noreferensi','autojurnal','matauang','kurs','revisi');
			
			##Generate No Jurnal###
			$SrchNoJurnal = $tgltrx.'/'.$_SESSION['empl']['lokasitugas'].'/SO/';
			$CntrNoJurnal = GetLastCntrNoJurnal($SrchNoJurnal);
			$NoJurnal	  = $SrchNoJurnal.$CntrNoJurnal;
			
			##Get Data Stok Opname Header##
			$HeaderStokOpname = GetHeaderStokOpname($id);
			$SODetailForJurnal = GetSODetailForJurnalRugi($HeaderStokOpname['reffno'],putertanggal($prd));
			##Set Total Debet & Kredit##
			
			if($SODetailForJurnal['TotalKredit'] != 0){
				$TotalDebet = $SODetailForJurnal['TotalDebet'];
				$TotalKredit = $SODetailForJurnal['TotalKredit'];
				
				##Set Data Ht##
				$dataHt['nojurnal'] 	= $NoJurnal;
				$dataHt['kodejurnal'] 	= 'SO';
				$dataHt['tanggal'] 		= $HeaderStokOpname['tanggal'];//date('Y-m-d');
				$dataHt['tanggalentry']	= $HeaderStokOpname['tanggal'];
				$dataHt['posting'] 		= 0;
				$dataHt['totaldebet'] 	= $TotalDebet;
				$dataHt['totalkredit'] 	= $TotalKredit;
				$dataHt['amountkoreksi'] = 0;
				$dataHt['noreferensi'] 	= $HeaderStokOpname['reffno'];
				$dataHt['autojurnal'] 	= 0;
				$dataHt['matauang'] 	= 'IDR';
				$dataHt['kurs'] 		= 1;
				$dataHt['revisi'] 		= 0;
						
				if(mysql_query(insertQuery($dbname,'keu_jurnalht',$dataHt,$fieldsHt))){
					###Insert Details Jurnal###
					##Set Fields HT##
					$fieldsDt = array('nojurnal','tanggal','nourut','noakun','keterangan','jumlah',
					'matauang','kurs','kodeorg','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
					'kodesupplier','noreferensi','noaruskas','kodevhc','nodok','kodeblok','revisi');
					
					##Get Data SO Detail##
					$Count = 1;
					foreach($SODetailForJurnal[0] as $SODKey=>$SODVal){
						##Set Data Dt##
						$dataDt['nojurnal'] 	= $NoJurnal;
						$dataDt['tanggal'] 		= $HeaderStokOpname['tanggal'];//date('Y-m-d');
						$dataDt['nourut'] 		= $Count;
						$dataDt['noakun'] 		= $SODVal['noakun'];
						if($SODVal['kdbarang'] == ' '){
							$Keterangan = $SODVal['kdgudang'].'-'.$HeaderStokOpname['nostokopname'];
						} else {
							$Keterangan = $SODVal['kdgudang'].'-'.$HeaderStokOpname['nostokopname'].'-'.$SODVal['namabarang'];
						}
						$dataDt['keterangan'] 	= $Keterangan;
						$dataDt['jumlah'] 		= $SODVal['nilai'];
						$dataDt['matauang'] 	= 'IDR';
						$dataDt['kurs'] 		= 1;
						$dataDt['kodeorg'] 		= $_SESSION['empl']['lokasitugas'];
						$dataDt['kodekegiatan']	= '';
						$dataDt['kodeasset'] 	= '';
						$dataDt['kodebarang'] 	= $SODVal['kdbarang'];
						$dataDt['nik'] 			= '';
						$dataDt['kodecustomer']	= '';
						$dataDt['kodesupplier']	= '';
						$dataDt['noreferensi'] 	= $HeaderStokOpname['reffno'];
						$dataDt['noaruskas'] 	= '';
						$dataDt['kodevhc'] 		= '';
						$dataDt['nodok'] 		= '';
						$dataDt['kodeblok'] 	= '';
						$dataDt['revisi'] 		= 0;
						
						if(!mysql_query(insertQuery($dbname,'keu_jurnaldt',$dataDt,$fieldsDt))){
							RollBackAll($NoJurnal,$id,putertanggal($prd));
							echo "ERR0R : Gagal Menyimpan Jurnal Details.";
							echo insertQuery($dbname,'keu_jurnaldt',$dataDt,$fieldsDt);
							exit();
						} else {
							if(!empty($SODVal['kdbarang']) && $SODVal['kdbarang'] != ' ') {
								$IndukOrganisasi = GetIndukOrganisasi($SODVal['kdunit']);
								$DataMBarangDt = GetMasterBarangDt($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang'], putertanggal($prd));
							
								$QtyKeluar = $DataMBarangDt[0]['qtykeluar'] + $SODVal['qtybalance'];
								$QtyKeluarxharga = $QtyKeluar * $DataMBarangDt[0]['hargarata'];
								$SaldoAkhirQty = $SODVal['qtyso'];
								$NilaiSaldoAkhir = $SaldoAkhirQty * $DataMBarangDt[0]['hargarata'];
								
								$UpdateMBarangDt = array(
									'qtykeluar' => $QtyKeluar,
									'saldoakhirqty' => $SaldoAkhirQty,
									'nilaisaldoakhir' => $NilaiSaldoAkhir,
									'qtykeluarxharga' => $QtyKeluarxharga,
									'lastuser' => $_SESSION['empl']['karyawanid']
								);
								$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."' and periode = '".putertanggal($prd)."'";
								if(!mysql_query(updateQuery($dbname,'log_5saldobulanan',$UpdateMBarangDt,$WhereMBarangDt))){
									RollBackAll($NoJurnal,$id,putertanggal($prd));
									echo "ERR0R : Gagal Update Stok Barang.";
									echo updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt);
									exit();
								}
								
								### INSERT TRANSAKSI HT KELUARAN ###
								## Set Fields HT ##
								$FTransaksiHt = array('tipetransaksi','notransaksi','tanggal','kodept','untukpt','keterangan',
								'statusjurnal','kodegudang','user','post','postedby','notransaksireferensi');
								
								$TanggalSekarang = $HeaderStokOpname['tanggal'];//date('Y-m-d');
								$NoTransaksi = GenerateNoTransaksi($SODVal['kdgudang'],'GI');
								
								## Set Data HT ##
								$DTransaksiHt['tipetransaksi'] = 8;
								$DTransaksiHt['notransaksi'] = $NoTransaksi;
								$DTransaksiHt['tanggal'] = $TanggalSekarang;
								$DTransaksiHt['kodept'] = $IndukOrganisasi;
								$DTransaksiHt['untukpt'] = $IndukOrganisasi;
								$DTransaksiHt['keterangan'] = $HeaderStokOpname['nostokopname'];
								$DTransaksiHt['statusjurnal'] = 1;
								$DTransaksiHt['kodegudang'] = $SODVal['kdgudang'];
								$DTransaksiHt['user'] = $_SESSION['empl']['karyawanid'];
								$DTransaksiHt['post'] = 1;
								$DTransaksiHt['postedby'] = $_SESSION['empl']['karyawanid'];
								$DTransaksiHt['notransaksireferensi'] = $HeaderStokOpname['reffno'];
								
								$CekTransaksiHt = CekTransaksiHt(8, $HeaderStokOpname['reffno']);
								if($CekTransaksiHt[0]['TotalNo'] == 0){
									if(!mysql_query(insertQuery($dbname,'log_transaksiht',$DTransaksiHt,$FTransaksiHt))){
										RollBackAll($NoJurnal,$id,putertanggal($prd));
										echo "ERR0R : Gagal Update Transaksi. Sebelum Insert HT. ";
										echo insertQuery($dbname,'log_transaksiht',$DTransaksiHt,$FTransaksiHt);
										exit();
									}
									
									### INSERT TRANSAKSI DT KELUARAN ###
									## Set Fields DT ##
									$FTransaksiDt = array('notransaksi','kodebarang','satuan','jumlah','jumlahlalu','hargasatuan','kodeblok',
									'updateby','statussaldo','hargarata');
									
									## Set Data HT ##
									$DTransaksiDt['notransaksi'] = $NoTransaksi;
									$DTransaksiDt['kodebarang'] = $SODVal['kdbarang'];
									$DTransaksiDt['satuan'] = $SODVal['kdsatuan'];
									$DTransaksiDt['jumlah'] = $SODVal['qtybalance'];
									$DTransaksiDt['jumlahlalu'] = 0;
									$DTransaksiDt['hargasatuan'] = 1;
									$DTransaksiDt['kodeblok'] = 0;
									$DTransaksiDt['updateby'] = 0;
									$DTransaksiDt['statussaldo'] = 1;
									$DTransaksiDt['hargarata'] = 0;
									
									if(!mysql_query(insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt))){
										RollBackAll($NoJurnal,$id,putertanggal($prd));
										echo "ERR0R : Gagal Update Detail Transaksi.";
										echo insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt);
										exit();
									}
								} else {
									### INSERT TRANSAKSI DT KELUARAN ###
									## Set Fields DT ##
									$FTransaksiDt = array('notransaksi','kodebarang','satuan','jumlah','jumlahlalu','hargasatuan','kodeblok',
									'updateby','statussaldo','hargarata');
									
									## Set Data HT ##
									// Ambil NoTransaksi Lama
									$NoTransaksi2 = GenerateNoTransaksiLama($SODVal['kdgudang'],'GI');									
									$DTransaksiDt['notransaksi'] = $NoTransaksi2;
									$DTransaksiDt['kodebarang'] = $SODVal['kdbarang'];
									$DTransaksiDt['satuan'] = $SODVal['kdsatuan'];
									$DTransaksiDt['jumlah'] = $SODVal['qtybalance'];
									$DTransaksiDt['jumlahlalu'] = 0;
									$DTransaksiDt['hargasatuan'] = 1;
									$DTransaksiDt['kodeblok'] = 0;
									$DTransaksiDt['updateby'] = 0;
									$DTransaksiDt['statussaldo'] = 1;
									$DTransaksiDt['hargarata'] = 0;
									
									if(!mysql_query(insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt))){
										RollBackAll($NoJurnal,$id,putertanggal($prd));
										echo "ERR0R : Gagal Update Detail Transaksi.";
										echo insertQuery($dbname,'log_transaksidt',$DTransaksiDt,$FTransaksiDt);
										exit();
									}
								}

								## UPDATE MASTER BARANG DT ##
								#$DataMBarangDt2 = GetMasterBarangDt2($IndukOrganisasi, $SODVal['kdbarang'], $SODVal['kdgudang']);
								$UpdateMBarangDt = array(
									'saldoqty' => $SaldoAkhirQty,
									'lastuser' => $_SESSION['empl']['karyawanid']
								);
								$WhereMBarangDt = "kodeorg = '".$IndukOrganisasi."' and kodebarang = '".$SODVal['kdbarang']."' and kodegudang = '".$SODVal['kdgudang']."'";
								if(!mysql_query(updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt))){
									RollBackAll($NoJurnal,$id,putertanggal($prd));
									echo "ERR0R : Gagal Update Stok Barang.";
									echo updateQuery($dbname,'log_5masterbarangdt',$UpdateMBarangDt,$WhereMBarangDt);
									exit();
								}

								$sUpdateSB = "UPDATE log_5saldobulanan SET nilaisaldoakhir=saldoakhirqty*hargarata, 
								qtymasukxharga=qtymasuk*hargarata, qtykeluarxharga=qtykeluar*hargarata WHERE kodebarang = '".$SODVal['kdbarang']."' and periode = '".$prd."'";
								if(!mysql_query($sUpdateSB)){
									echo "ERR0R : Gagal Update Saldo Bulanan";
									echo $sUpdateSB;
									exit();
								}
							}
						}
						$Count++;
					}
				} else {
					RollBackAll($NoJurnal,$id,putertanggal($prd));
					echo "ERR0R : Gagal Insert Jurnal Header.";
					echo insertQuery($dbname,'keu_jurnalht',$dataHt,$fieldsHt);
					exit();
				}
			}
			
			
		}
		
	break;
	default:
	break;
}
?>