<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
#pre($_POST);exit();
$method		  	= isset($_POST['method']) ? $_POST['method'] : null;
$PeriodeAbsen 	= isset($_POST['PeriodeAbsen']) ? $_POST['PeriodeAbsen'] : null;
$Perusahaan	  	= isset($_POST['Perusahaan']) ? $_POST['Perusahaan'] : null;
$Bagian		  	= isset($_POST['Bagian']) ? $_POST['Bagian'] : '';
$Karyawan	  	= isset($_POST['Karyawan']) ? $_POST['Karyawan'] : '';
$ListData	 	= array();
$TanggalPeriode = array();

function dates_inbetween($date1, $date2){
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

function DetailPeriodeAbsen($PeriodeAbsen){
	global $dbname;
	
	$sDetailPeriodeAbsen = "SELECT MIN(tanggal) as tanggal_mulai, MAX(tanggal) as tanggal_akhir FROM ".$dbname.".sdm_absensiht WHERE periode = '".$PeriodeAbsen."';";
	$rDetailPeriodeAbsen = fetchData($sDetailPeriodeAbsen);
	foreach($rDetailPeriodeAbsen as $kDetailPeriodeAbsen => $vDetailPeriodeAbsen)
	{
		$TanggalPeriode['TanggalAwal'] 	= $vDetailPeriodeAbsen['tanggal_mulai'];
		$TanggalPeriode['TanggalAkhir'] = $vDetailPeriodeAbsen['tanggal_akhir'];
	} 
	
	return $TanggalPeriode;
}

function GetKaryawan($Karyawan, $Perusahaan){
	global $dbname;
	if (strpos($Karyawan, 'Bag-') !== false) {
		$Karyawan = str_replace("Bag-","",$Karyawan);
		$WhereKaryawan = 'a.bagian';
	} elseif(empty($Karyawan) == true) {
		$WhereKaryawan = 'a.bagian';
	} else {
		$WhereKaryawan = 'a.karyawanid';
	}
	
	$sKaryawan = "select a.nik, a.karyawanid, a.namakaryawan, c.kode, c.nama from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
	where a.lokasitugas like '".$Perusahaan."'
	and ".$WhereKaryawan." like '%".$Karyawan."%'
    order by namakaryawan asc";	
	
	$rKaryawan=fetchData($sKaryawan);
	foreach($rKaryawan as $kKaryawan => $vKaryawan)
	{
		$ListData[$vKaryawan['nama']][$vKaryawan['karyawanid']]['nik'] = $vKaryawan['nik'];
		$ListData[$vKaryawan['nama']][$vKaryawan['karyawanid']]['nama'] = $vKaryawan['namakaryawan'];
	} 
	#pre($Karyawan);exit();
	return $ListData;
}

function GetKaryawanIjin($Karyawan, $Perusahaan, $Tanggal){
	global $dbname;
	if (strpos($Karyawan, 'Bag-') !== false) {
		$Karyawan = str_replace("Bag-","",$Karyawan);
		$WhereKaryawan = 'b.bagian';
	} elseif(empty($Karyawan) == true) {
		$WhereKaryawan = 'b.bagian';
	} else {
		$WhereKaryawan = 'a.karyawanid';
	}
	
	$sKaryawanIjin = "SELECT 
	a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin,
	b.bagian, b.namakaryawan, b.lokasitugas, b.nik,
	c.kodeabsen
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
	LEFT JOIN ".$dbname.".sdm_jenis_ijin_cuti c on a.jenisijin = c.id
    WHERE substr(a.darijam,1,10) >= '".$Tanggal['TanggalAwal']."'  and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
	and b.bagian like '%%' OR substr(a.sampaijam,1,10) >= '".$Tanggal['TanggalAwal']."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
	and ".$WhereKaryawan." like '%".$Karyawan."%'
    ORDER BY a.darijam, a.sampaijam";
	#and substr(a.sampaijam,1,10) <= '".$Tanggal['TanggalAkhir']."' 
	
	$rKaryawanIjin = fetchData($sKaryawanIjin);
	#pre($sKaryawanIjin);exit();
	foreach($rKaryawanIjin as $kKaryawanIjin => $vKaryawanIjin)
	{
		$Awal = $vKaryawanIjin['daritanggal'];
		$Akhir = $vKaryawanIjin['sampaitanggal'];
		
		while (strtotime($Awal) <= strtotime($Akhir)) {
			$CheckAbsen = CheckPembatalanCuti($Awal,$vKaryawanIjin['karyawanid']);
			if($CheckAbsen == 0){
				
				$PeriodeCuti = substr($Awal,0,4);
				if($vKaryawanIjin['kodeabsen'] == 'PC'){
					$GShiftLate = GetShiftLate(substr($vKaryawanIjin['darijam'],11,5), $vKaryawanIjin['karyawanid'], $Perusahaan, $PeriodeCuti, $vKaryawanIjin['nik'], $Awal);
					$ListData[$vKaryawanIjin['karyawanid']][$Awal] = $GShiftLate['absensi'];
				} else {
					$ListData[$vKaryawanIjin['karyawanid']][$Awal] = $vKaryawanIjin['kodeabsen'];	
				}
									
				$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
			} else {
				$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
			}
		}
	} 
	
	#pre($ListData);
	return $ListData;
}

function CheckPembatalanCuti($tanggal, $karyawanid){
	global $dbname;
	
	$sDataCalender = "SELECT COUNT(*) as total
	FROM ".$dbname.".log_sdm_batal_ijin
	WHERE tanggal = '".$tanggal."' and stpersetujuan1 = 1 and stpersetujuanhrd = 1 and karyawanid = '".$karyawanid."'";
	
	$rDataCalender = fetchData($sDataCalender);

	#pre($rDataCalender);exit();
	return $rDataCalender[0]['total'];
}

function GetKaryawanDinas($Karyawan, $Perusahaan, $DetailPeriodeAbsen){
	global $dbname;
	if (strpos($Karyawan, 'Bag-') !== false) {
		$Karyawan = str_replace("Bag-","",$Karyawan);
		$WhereKaryawan = 'b.bagian';
	} elseif(empty($Karyawan) == true) {
		$WhereKaryawan = 'b.bagian';
	} else {
		$WhereKaryawan = 'a.karyawanid';
	}
	
	$sKaryawanDinas = "SELECT 
	a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, a.kodeorg,
	b.namakaryawan
	FROM ".$dbname.".sdm_pjdinasht a
    LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid        
    WHERE a.tanggalperjalanan >= '".$DetailPeriodeAbsen['TanggalAwal']."'
	and '".$WhereKaryawan."' like '%".$Karyawan."%'
	and statuspersetujuan='1' and statushrd='1' and IFNULL(jenispertanggungjawaban,' ') <> 'pdr2'
    order by a.tanggalperjalanan, a.tanggalkembali";
	
	$rKaryawanDinas = fetchData($sKaryawanDinas);
	#pre($rKaryawanDinas);exit();
	foreach($rKaryawanDinas as $kKaryawanDinas => $vKaryawanDinas)
	{
		$Awal = $vKaryawanDinas['tanggalperjalanan'];
		$Akhir = $vKaryawanDinas['tanggalkembali'];
		
		while (strtotime($Awal) <= strtotime($Akhir)) {
			$ListData[$vKaryawanDinas['karyawanid']][$Awal] = 'D';
			$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
		}
	}
	#pre($ListData);exit();
	return $ListData;
}

function GetSisaCuti($kodeorg, $karyawanid, $periodecuti){
	global $dbname;
	
	$sSisaCuti = "SELECT sisa, diambil, periodecuti, hakcuti FROM ".$dbname.".sdm_cutiht
	WHERE kodeorg = '".$kodeorg."' and karyawanid = '".$karyawanid."' AND sampai >= '".$periodecuti."'";
	
	$rSisaCuti = fetchData($sSisaCuti);
	foreach($rSisaCuti as $kSC => $vSC) {
		if($vSC['hakcuti'] == $vSC['sisa']) {
			#unset($rSisaCuti[$kSC]);
		} else {
			#unset($rSisaCuti[$kSC+1]);
		}
	}
	#pre($sSisaCuti);
	return $rSisaCuti;	
}

function GetShiftLate($jam_masuk, $karyawanid, $kodeorg, $PeriodeCuti, $nik, $tanggal){
	global $dbname;

	$SShift = "select a.nama,a.kode,a.jam_masuk,kd_organisasi,a.jam_keluar,
	c.nik,c.namakaryawan as namakaryawan, b.tanggal, b.jam, b.jamPlg,
	CASE WHEN TIMEDIFF(b.jam,a.jam_masuk)<0 THEN 0 ELSE TIMEDIFF(b.jam,a.jam_masuk) END as Keterlambat,
	CASE WHEN TIMEDIFF(a.jam_keluar,b.jamPlg)<0 THEN 0 ELSE TIMEDIFF(a.jam_keluar,b.jamPlg) END as PulangCepat,
	CASE WHEN TIMEDIFF(b.jamPlg,a.jam_keluar)<0 THEN 0 ELSE TIMEDIFF(b.jamPlg,a.jam_keluar) END as Lembur,
	b.absensi	
	FROM sdm_shift a 
	INNER JOIN sdm_absensidt b ON a.nama=b.shift 
	INNER JOIN datakaryawan c ON b.karyawanid=c.karyawanid  
	WHERE a.kd_organisasi = '".$_SESSION['empl']['lokasitugas']."'
	AND c.nik = '".$nik."'
	AND b.tanggal = '".$tanggal."'
	;";
	#pre($SShift);exit();
	$QShift = mysql_query($SShift) or die(mysql_error());
	
	while( $row = mysql_fetch_assoc( $QShift)){
		$TotalTelat = strtotime(date($row['Keterlambat']));
		$TotalPulangCepat = strtotime(date($row['PulangCepat']));
		$JamTelat = strtotime(date('04:00:00'));
		$JamOnTime = strtotime(date('00:00:00'));
		
		#echo ($row['scan_masuk'].' : '.$JamMasuk.'<br>'.$JamTelat.'<br>'.$JamOnTime.'<br><br>');
		
		if($TotalTelat > $JamTelat){
			$SisaCuti = GetSisaCuti($kodeorg, $karyawanid, $PeriodeCuti);
			if($SisaCuti[0]['sisa'] == 0) {
				$Shift['absensi'] = 'CDT';
			} else {
				$Shift['absensi'] = 'T';
			}
		} else if($TotalTelat > $JamOnTime){
			$Shift['absensi'] = 'T';
		} else {
			$Shift['absensi'] = 'H';
		}
		
		if($row['jam_keluar'] == '23:59:00' && $row['jam_masuk'] > $row['jamPlg']){
			$TotalPulangCepat = 0;
			#$Shift['absensi'] = $row['jamPlg'];
		}
		if($TotalPulangCepat > $JamTelat){
			$SisaCuti = GetSisaCuti($kodeorg, $karyawanid, $PeriodeCuti);
			if($SisaCuti[0]['sisa'] == 0) {
				$Shift['absensi'] = 'I2';
			} else {
				$Shift['absensi'] = 'PC';
			}
		} else if($TotalPulangCepat > $JamOnTime){
			$Shift['absensi'] = 'I1';
		}
		
		if($row['absensi'] == 'CDT'){
			$Shift['absensi'] = 'CDT';
		}
		$Shift['shift'] = $row['nama'];
		return $Shift;
	}
	#pre($Shift); #exit();
	return $Shift;
}

function GetKaryawanMasuk($Karyawan, $Perusahaan, $DetailPeriodeAbsen){
	global $dbname;
	if (strpos($Karyawan, 'Bag-') !== false) {
		$Karyawan = str_replace("Bag-","",$Karyawan);
		$WhereKaryawan = 'b.bagian';
	} elseif(empty($Karyawan) == true) {
		$WhereKaryawan = 'b.bagian';
	} else {
		$WhereKaryawan = 'a.karyawanid';
	}
	
	$sKaryawanMasuk = "SELECT a.karyawanid, a.tanggal, a.jam, a.jamPlg, a.absensi, a.penjelasan,
	b.karyawanid, b.namakaryawan, b.bagian, b.nik
	FROM ".$dbname.".sdm_absensidt a
	JOIN ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid    
	WHERE a.tanggal >= '".$DetailPeriodeAbsen['TanggalAwal']."' and a.tanggal <= '".$DetailPeriodeAbsen['TanggalAkhir']."' 
	and ".$WhereKaryawan." like '%".$Karyawan."%'
	ORDER BY a.tanggal ASC";
	
	$rKaryawanMasuk = fetchData($sKaryawanMasuk);
	#pre($sKaryawanMasuk);exit();
	
	
	
	foreach($rKaryawanMasuk as $kKaryawanMasuk => $vKaryawanMasuk)
	{
		if($vKaryawanMasuk['absensi'] == 'CDT' || $vKaryawanMasuk['absensi'] == 'T' || $vKaryawanMasuk['absensi'] == 'I2' || $vKaryawanMasuk['absensi'] == 'PC'){
			$PeriodeCuti = substr($vKaryawanMasuk['tanggal'],0,4);
			$GShiftLate = GetShiftLate($vKaryawanMasuk['jam'], $vKaryawanMasuk['karyawanid'], $Perusahaan, $PeriodeCuti, $vKaryawanMasuk['nik'], $vKaryawanMasuk['tanggal']);
		
			$ListData[$vKaryawanMasuk['karyawanid']][$vKaryawanMasuk['tanggal']] = $GShiftLate['absensi'];
		} else {
			$ListData[$vKaryawanMasuk['karyawanid']][$vKaryawanMasuk['tanggal']] = $vKaryawanMasuk['absensi'];
		}
		#$ListData[$vKaryawanMasuk['karyawanid']][$vKaryawanMasuk['tanggal']]['title'] = $vKaryawanMasuk['penjelasan'];
	}
	#pre($ListData);#exit();
	return $ListData;
}

function GetKaryawanSakit($Karyawan, $Perusahaan, $PeriodeAbsen){
	global $dbname;
	if (strpos($Karyawan, 'Bag-') !== false) {
		$Karyawan = str_replace("Bag-","",$Karyawan);
		$WhereKaryawan = 'b.bagian';
	} elseif(empty($Karyawan) == true) {
		$WhereKaryawan = 'b.bagian';
	} else {
		$WhereKaryawan = 'a.karyawanid';
	}
	
	$sKaryawanSakit = "SELECT a.karyawanid, a.tanggal, a.tanggalselesai, a.ygsakit, a.periode,
	b.karyawanid, b.namakaryawan, b.bagian
	FROM ".$dbname.".sdm_pengobatanht a
	JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid    
	WHERE a.periode >= '".$PeriodeAbsen."'
	and a.ygsakit = 0 and ".$WhereKaryawan." like '%".$Karyawan."%'
	ORDER BY a.tanggal ASC";
	
	$rKaryawanSakit = fetchData($sKaryawanSakit);
	#pre($rKaryawanSakit);exit();
	foreach($rKaryawanSakit as $kKaryawanSakit => $vKaryawanSakit)
	{
		$Awal = $vKaryawanSakit['tanggal'];
		$Akhir = $vKaryawanSakit['tanggalselesai'];
		
		while (strtotime($Awal) <= strtotime($Akhir)) {
			$ListData[$vKaryawanSakit['karyawanid']][$Awal] = 'S';
			$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
		}
	}
	#pre($ListData);exit();
	return $ListData;
}

function GetKeteranganAbsen(){
	global $dbname;
	
	$sAbsensi = "select * from ".$dbname.".sdm_5absensi a order by kodeabsen asc";		
	$rAbsensi = fetchData($sAbsensi);
	return $rAbsensi;
}

function GetDataCalender($kodeorg, $periode){
	global $dbname;
	
	$sDataCalender = "SELECT a.tanggal as tanggal,
	b.tanggal as tanggallibur, b.status
	FROM ".$dbname.".sdm_absensiht a
	LEFT JOIN ".$dbname.".sdm_hari_libur b on a.tanggal = b.tanggal and a.kodeorg = b.kodeorg    
	WHERE a.periode = '".$periode."' and a.kodeorg = '".$kodeorg."'
	ORDER BY a.tanggal ASC";
	
	$rDataCalender = fetchData($sDataCalender);

	#pre($ListData);exit();
	return $rDataCalender;
}
#showerror();
$DetailPeriodeAbsen = DetailPeriodeAbsen($PeriodeAbsen);
$DataKaryawan = GetKaryawan($Karyawan, $Perusahaan);
$DataKaryawanIjin = GetKaryawanIjin($Karyawan, $Perusahaan, $DetailPeriodeAbsen);
$DataKaryawanDinas = GetKaryawanDinas($Karyawan, $Perusahaan, $DetailPeriodeAbsen);
$DataKaryawanMasuk = GetKaryawanMasuk($Karyawan, $Perusahaan, $DetailPeriodeAbsen);
$DataKaryawanSakit = GetKaryawanSakit($Karyawan, $Perusahaan, $PeriodeAbsen);
$DataCalender = GetDataCalender($Perusahaan, $PeriodeAbsen);
#pre($DataCalender);exit();
#$DataCalender = dates_inbetween($DetailPeriodeAbsen['TanggalAwal'], $DetailPeriodeAbsen['TanggalAkhir']);
#pre($DataKaryawanDinas); exit();
$jumlahhari = count($DataCalender);
$Absensi = GetKeteranganAbsen();
$JumlahAbsen = count($Absensi);

// BEGIN STREAM
$stream='';
$stream.="
<style type='text/css'>
.table2 {
    border-collapse: collapse;
    overflow-x: scroll;
    display: block;
}
.table2 thead {
}
.table2 thead, .table2 tbody {
    display: block;
}
.table2 tbody {
    overflow-y: scroll;
    overflow-x: hidden;
    height: 200px;
}
.table2 td, .table2 th {
    min-width: 20px;
    height: 25px;
}
</style>
<script>
$('table2').on('scroll', function () {
    $('table2 > *').width($('table2').width() + $('table2').scrollLeft());
});
</script>
";

$no=0;
$stream.="<table border=1 class='table2'>";
$stream.="<thead><tr>";
$stream.="<td rowspan=2 align=center style='width: 30px !important;'>".$_SESSION['lang']['nourut']."</td>";
$stream.="<td rowspan=2 align=center style='width: 100px !important;'>".$_SESSION['lang']['nik']."</td>";
$stream.="<td rowspan=2 align=center style='width: 140px !important;'>".$_SESSION['lang']['namakaryawan']."</td>";
$stream.="<td colspan=".$jumlahhari." align=center>".$DetailPeriodeAbsen['TanggalAwal']." s/d ".$DetailPeriodeAbsen['TanggalAkhir']."</td>";
$stream.="<td colspan=".$JumlahAbsen." align=center>Status Absensi</td>";
$stream.="</tr>";
$stream.="<tr>";
foreach($DataCalender as $DCkey => $DCVal) {
	if($DCVal['status'] == 'L' || $DCVal['status'] == 'MG' || $DCVal['status'] == 'C'){
		$value = "<font color='#FF6347'>".substr($DCVal['tanggal'],8,2)."</font>";
	} else {
		$value = substr($DCVal['tanggal'],8,2);
	}
	$stream.="<td align=center style='width: 30px !important;'>";
    $stream.=$value;
    $stream.="</td>";
}
foreach($Absensi as $AKey => $AVal) {
	$stream.="<td align=center style='width: 30px !important;'>".$AVal['kodeabsen']."</td>";
	$StatusAbsen[$AVal['kodeabsen']] = 0;
}
$stream.="</tr></thead>";
$stream.="<tbody>";
foreach($DataKaryawan as $DKKey => $DKVal) {
	$stream.="<tr>";
	$stream.="<td align=left colspan=3 style='border: 0px;'><b>".$DKKey."</b></td>";
	$stream.="</tr>";
	foreach($DKVal as $DKTKey => $DKTVal) {
		$no+=1;
		$stream.="<tr>";
		$stream.="<td align=left style='width: 30px !important;'>".$no."</td>";
		$stream.="<td align=left style='width: 100px !important;'>".$DKTVal['nik']."</td>";//rubah $DKTKey jadi $DKTVal['nik'] -> karyawanid -> nik ==Jo 29-01-2017==
		$stream.="<td align=left style='width: 140px !important;'>".$DKTVal['nama']."</td>";
		foreach($DataCalender as $DCKey => $DCVal) {

			if($DCVal['status'] == 'L' || $DCVal['status'] == 'MG' || $DCVal['status'] == 'C'){
				if($DataKaryawanIjin[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='width: 30px !important;'";
					$pres = $DataKaryawanIjin[$DKTKey][$DCVal['tanggal']];
					$StatusAbsen[$DataKaryawanIjin[$DKTKey][$DCVal['tanggal']]] +=1;
				}elseif($DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='background-color: #F8F8F8; width: 30px !important;'";
					
					if($DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']] == 'T') {
						$pres='T';
						$StatusAbsen['T'] +=1;
					}else {
						$pres='HL';
						$StatusAbsen['HL'] +=1;
					}
					$title = $DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']]['title'];
				} elseif($DataKaryawanDinas[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='width: 30px !important;'";
					$pres='D';
					$StatusAbsen['D'] +=1;
				} else {
					$bgcolor = "style='background-color: #FF6347; width: 30px !important;'";
					$pres = 'L';
					$StatusAbsen['L'] +=1;
				}
			} else {
				if($DataKaryawanIjin[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='width: 30px !important;'";
					$pres = $DataKaryawanIjin[$DKTKey][$DCVal['tanggal']];
					$StatusAbsen[$DataKaryawanIjin[$DKTKey][$DCVal['tanggal']]] +=1;
				} elseif($DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='background-color: #F8F8F8; width: 30px !important;'";
					$pres=$DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']];
					$StatusAbsen[$DataKaryawanMasuk[$DKTKey][$DCVal['tanggal']]] +=1;					
				} elseif($DataKaryawanDinas[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='width: 30px !important;'";
					$pres='D';
					$StatusAbsen['D'] +=1;
				}elseif($DataKaryawanSakit[$DKTKey][$DCVal['tanggal']]) {
					$bgcolor = "style='width: 30px !important;'";
					$pres='S';
					$StatusAbsen['S'] +=1;
				} else {
					$bgcolor = "style='width: 30px !important;'";
					$pres='&nbsp;&nbsp;&nbsp;';
				}
			}
			$stream.="<td valign=top align=center ".$bgcolor." title='".$title."'>".$pres."</td>"; 
		}
		
		foreach($Absensi as $AKey => $AVal) {
			$stream.="<td align=center style='width: 30px !important;'>".$StatusAbsen[$AVal['kodeabsen']]."</td>";	
			$StatusAbsen[$AVal['kodeabsen']] = 0;
		}
		$stream.="</tr>";
	}
}

$stream.="</tbody></table><table id='header-fixed'></table>";
$stream.="Status Kehadiran :</br>";
$stream.='<table>';
$Count = 1;
foreach($Absensi as $AKey => $AVal) {
	if ($Count % 2 == 0){
		$stream.='<tr>';
		$stream.='<td width=40>'.$AVal['kodeabsen'].'</td>';
		$stream.='<td width=160>'.$AVal['keterangan'].'</td>';
	} else {
		$stream.='<td width=40>'.$AVal['kodeabsen'].'</td>';
		$stream.='<td>'.$AVal['keterangan'].'</td>';
		$stream.='</tr>';
	}
	$Count++;
}
$stream.='</table>';

switch($method)
{
	case'PilihData':
		echo $stream;
	break;
	default:
	break;
}
?>