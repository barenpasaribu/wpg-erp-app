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
	
	$TanggalPeriode['TanggalAwal'] = date($PeriodeAbsen.'-01'); // hard-coded '01' for first day
	$TanggalPeriode['TanggalAkhir']  = date($PeriodeAbsen.'-t');
	
	return $TanggalPeriode;
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
	a.*,substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal,
	b.bagian, b.namakaryawan, b.lokasitugas, b.nik, b.kodeorganisasi,
	c.kodeabsen, c.jenisizincuti,
	d.namakaryawan as namahrd,
	e.namakaryawan as namaatasan
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
	LEFT JOIN ".$dbname.".sdm_jenis_ijin_cuti c on a.jenisijin = c.id
	LEFT JOIN ".$dbname.".datakaryawan d on a.hrd = d.karyawanid
	LEFT JOIN ".$dbname.".datakaryawan e on a.persetujuan1 = e.karyawanid
    WHERE (c.isCuti = 1 and substr(a.darijam,1,10) >= '".$Tanggal['TanggalAwal']."' and substr(a.sampaijam,1,10) <= '".$Tanggal['TanggalAkhir']."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1' and ".$WhereKaryawan." like '%".$Karyawan."%' and a.persetujuan1 = '".$_SESSION['standard']['userid']."')
		OR (c.isCuti = 1 and substr(a.darijam,1,10) >= '".$Tanggal['TanggalAwal']."' and substr(a.sampaijam,1,10) <= '".$Tanggal['TanggalAkhir']."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1' and ".$WhereKaryawan." like '%".$Karyawan."%' and a.hrd = '".$_SESSION['standard']['userid']."')		
    ORDER BY a.darijam, a.sampaijam";
	
	$rKaryawanIjin = fetchData($sKaryawanIjin);
	#pre($sKaryawanIjin);#exit();
	$No = 1;
	foreach($rKaryawanIjin as $kKaryawanIjin => $vKaryawanIjin)
	{
		$Awal = $vKaryawanIjin['daritanggal'];
		$Akhir = $vKaryawanIjin['sampaitanggal'];
		
		while (strtotime($Awal) <= strtotime($Akhir)) {
			//tambah id untuk tabel sdm_ijin ==Jo 16-05-2017==
			$ListData[$vKaryawanIjin['nik']][$Awal]['id'] = $vKaryawanIjin['id'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['karyawanid'] = $vKaryawanIjin['karyawanid'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['keperluan'] = $vKaryawanIjin['keperluan'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['namakaryawan'] = $vKaryawanIjin['namakaryawan'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['jenisizincuti'] = $vKaryawanIjin['jenisizincuti'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['namaatasan'] = $vKaryawanIjin['namaatasan'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['namahrd'] = $vKaryawanIjin['namahrd'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['statuscuti'] = GetStatusBatalCuti($vKaryawanIjin['karyawanid'], $Awal);
			$ListData[$vKaryawanIjin['nik']][$Awal]['aksi'] = GetButtonAksi($vKaryawanIjin['karyawanid'], $Awal, $No);
			$ListData[$vKaryawanIjin['nik']]['nama'] = $vKaryawanIjin['namakaryawan'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['no'] = $No;
			$ListData[$vKaryawanIjin['nik']][$Awal]['persetujuan1'] = $vKaryawanIjin['persetujuan1'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['hrd'] = $vKaryawanIjin['hrd'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['kodeorg'] = $vKaryawanIjin['kodeorganisasi'];
			$ListData[$vKaryawanIjin['nik']][$Awal]['lokasitugas'] = $vKaryawanIjin['lokasitugas'];
			$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
			$No+=1;
		}
	} 
	
	#pre($ListData); exit();
	return $ListData;
}

function GetStatusBatalCuti($karyawanid, $tanggal){
	global $dbname;
	
	$sStatusBatalCuti = "SELECT * FROM ".$dbname.".log_sdm_batal_ijin
    WHERE karyawanid = '".$karyawanid."' and tanggal = '".$tanggal."'";
	
	$rStatusBatalCuti = fetchData($sStatusBatalCuti);
	#pre($rStatusBatalCuti);
	if(empty($rStatusBatalCuti)) {
		return "<font color=blue>Aktif</font>";
	}elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 0){
		return "<font color=green>Menunggu Keputusan Atasan</font>";
	}elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 1 && $rStatusBatalCuti[0]['stpersetujuanhrd'] == 0) {
		return "<font color=green>Menunggu Keputusan HRD</font>";
	} else {
		return "<font color=red>Hak Cuti Sudah di kembalikan</font>";
	}
}

function GetButtonAksi($karyawanid, $tanggal, $No){
	global $dbname;
	
	$sStatusBatalCuti = "SELECT `id`, tanggal,persetujuan1,stpersetujuan1,hrd,
						stpersetujuanhrd,karyawanid FROM ".$dbname.".log_sdm_batal_ijin
    WHERE karyawanid = '".$karyawanid."' and tanggal = '".$tanggal."' LIMIT 1";
	
	$rStatusBatalCuti = fetchData($sStatusBatalCuti);
	#pre($sStatusBatalCuti);
	if(empty($rStatusBatalCuti)) 
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='AjukanBatalCuti(".$No.")'>Ajukan Pembatalan Cuti</button>";
	}
	elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 1 && $rStatusBatalCuti[0]['stpersetujuanhrd'] == 1)
	{
		return "<font color=red>Cuti Sudah di batalkan</font>";
	}
	elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 1 && $rStatusBatalCuti[0]['persetujuan1'] == $_SESSION['standard']['userid'] && $rStatusBatalCuti[0]['hrd'] == $_SESSION['standard']['userid'])
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='CancelAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=red>Batalkan Permohonan</font></button>
		<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='SetujuiAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=green>Setujui Pembatalan Cuti</font></button>";
	}
	elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 1 && $rStatusBatalCuti[0]['persetujuan1'] == $_SESSION['standard']['userid'])
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='CancelAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=red>Batalkan Permohonan</font></button>";
	}
	elseif($rStatusBatalCuti[0]['stpersetujuan1'] == 0 && $rStatusBatalCuti[0]['persetujuan1'] == $_SESSION['standard']['userid'])
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='SetujuiAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=green>Setujui Pembatalan Cuti</font></button>";
	}
	elseif($rStatusBatalCuti[0]['stpersetujuanhrd'] == 1 && $rStatusBatalCuti[0]['hrd'] == $_SESSION['standard']['userid']) 
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='CancelAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=red>Batalkan Permohonan</font></button>";
	} 
	elseif($rStatusBatalCuti[0]['stpersetujuanhrd'] == 0 && $rStatusBatalCuti[0]['hrd'] == $_SESSION['standard']['userid']) 
	{
		return "<button id='btnAjuBatalCuti".$No."' name='btnAjuBatalCuti".$No."' class='mybutton' onclick='SetujuiAjuanBatalCuti(".intval($rStatusBatalCuti[0]['id']).", ".$No.")'><font color=green>Setujui Pembatalan Cuti</font></button>";
	}
	else 
	{
		return "<font color=red>Cuti di batalkan</font>";
	}
}




#showerror();
$DetailPeriodeAbsen = DetailPeriodeAbsen($PeriodeAbsen);
$DataKaryawanIjin = GetKaryawanIjin($Karyawan, $Perusahaan, $DetailPeriodeAbsen);
$DataCalender = dates_inbetween($DetailPeriodeAbsen['TanggalAwal'], $DetailPeriodeAbsen['TanggalAkhir']);
#pre($DataKaryawanIjin); exit();
$jumlahhari = count($DataCalender);

// BEGIN STREAM
$stream='';
$no = 1;
$stream.="<table border=1>";
$stream.="<thead><tr>";
$stream.="<td align=center>".$_SESSION['lang']['nik']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['namakaryawan']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['tangalcuti']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['keperluan']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['jenisijin']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['atasan']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['hrd']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['statuscuti']."</td>";
$stream.="<td align=center>".$_SESSION['lang']['action']."</td>";
$stream.="</tr></thead>";
$stream.="<tbody>";
foreach($DataKaryawanIjin as $DKKey => $DKVal){
	$Count = count($DKVal);
	$stream.="<tr>";
	$stream.="<td align=left rowspan=".$Count.">".$DKKey."</td>";
	$stream.="<td align=left rowspan=".$Count.">".$DKVal['nama']."</td>";
	$stream.="</tr>";
	$Nama = $DKVal['nama'];
	unset($DKVal['nama']);
	foreach($DKVal as $DKTKey => $DKTVal) {
		$stream.="<tr>";
		$stream.="<td align=left>".$DKTKey."</td>";
		$stream.="<td align=left>".$DKTVal['keperluan']."</td>";
		$stream.="<td align=left>".$DKTVal['jenisizincuti']."</td>";
		$stream.="<td align=left>".$DKTVal['namaatasan']."</td>";
		$stream.="<td align=left>".$DKTVal['namahrd']."</td>";
		$stream.="<td align=left>".$DKTVal['statuscuti']."</td>";
		$stream.="<td align=left>".$DKTVal['aksi']."</td>";
		$stream.="<input type=hidden id=karyawanid-".$DKTVal['no']." value=".$DKTVal['karyawanid'].">";
		$stream.="<input type=hidden id=tanggal-".$DKTVal['no']." value=".$DKTKey.">";
		$stream.="<input type=hidden id=persetujuan1-".$DKTVal['no']." value=".$DKTVal['persetujuan1'].">";
		$stream.="<input type=hidden id=hrd-".$DKTVal['no']." value=".$DKTVal['hrd'].">";
		$stream.="<input type=hidden id=namakaryawan-".$DKTVal['no']." value=".$Nama.">";
		$stream.="<input type=hidden id=kodeorg-".$DKTVal['no']." value=".$DKTVal['lokasitugas'].">";
		$stream.="<input type=hidden id=ids-".$DKTVal['no']." value=".$DKTVal['id'].">";
		$stream.="</tr>";
	}
	
}
$stream.="</tbody></table>";
switch($method)
{
	case'PilihData':
		echo $stream;
	break;
	default:
	break;
}
?>