<?php
require_once('master_validation.php');
include('config/connection.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
#showerror();
$typesync = isset($_POST['typesync']) ? $_POST['typesync'] : null;
$periodeysnc = isset($_POST['periodeysnc']) ? $_POST['periodeysnc'] : null;
$periodeysncAr = explode("-", $periodeysnc);
$periodetahun = $periodeysncAr[0];
#pre($periodeysncAr);
#echo $periodetahun; exit();

function GetKaryawanName($karyawanid){
	global $dbname;
	$sKaryawanId = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid = '".$karyawanid."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	
	
	return $rKaryawanId->namakaryawan;
}

function GetKaryawanKodeOrg($karyawanid){
	global $dbname;
	$sKaryawanId = "select kodeorganisasi from ".$dbname.".datakaryawan where karyawanid = '".$karyawanid."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	
	
	return $rKaryawanId->kodeorganisasi;
}

function SelectADt($tanggal, $karyawanid){
	global $dbname;
	$sSelectADt = "SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, c.namakaryawan, a.kodeorg 
	FROM ".$dbname.".sdm_pjdinasht a
	LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
	WHERE a.tanggalperjalanan <= '".$DetailPeriode->tanggalakhir."' and a.tanggalkembali >= '".$DetailPeriode->tanggalawal."' 
	AND a.karyawanid like '%%' AND statuspersetujuan='1' AND statushrd='1'
    order by a.tanggalperjalanan, a.tanggalkembali";
	$QSelectADt = mysql_query($sSelectADt) or die(mysql_error());
	#$RSelectADt = mysql_fetch_object($QSelectADt);
	
	return $QSelectADt;
}

function SelectPD($tanggal, $karyawanid){
	global $dbname;
	$sSelectADt = "SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.jenisijin, d.jenisizincuti 
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
	LEFT JOIN ".$dbname.".sdm_jenis_ijin_cuti d on a.jenisijin=d.id   	
    WHERE substr(a.darijam,1,10) <= '".$tangsys2."' and substr(a.sampaijam,1,10) >= '".$tangsys1."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
        and a.karyawanid like '%".$karyawanid."%'
    ORDER BY a.darijam, a.sampaijam";
	$QSelectADt = mysql_query($sSelectADt) or die(mysql_error());
	#$RSelectADt = mysql_fetch_object($QSelectADt);
	
	return $QSelectADt;
}

function InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan){
	global $dbname;
	$sInsertADt = "INSERT INTO ".$dbname.".sdm_absensidt 
	(`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`, `penjelasan`) VALUES 
	('".$kodeorg."', '".$tanggal."', '".$karyawanid."', '".$absensi."', '".$jam."', '".$jamPlg."', '".$penjelasan."')";
	if(mysql_query($sInsertADt)) {
		return true;
	} else {
		return mysql_error();
	}
}

function DeleteADt($kodeorg, $tanggal, $karyawanid){
	global $dbname;
	$SDeleteADt = "DELETE FROM ".$dbname.".sdm_absensidt 
	WHERE (`kodeorg`='".$kodeorg."') AND (`tanggal`='".$tanggal."') AND (`karyawanid`='".$karyawanid."')";
	mysql_query($SDeleteADt) or die(mysql_error());
}

function GetFirstAndLastPeriode($Periode){
	global $dbname;
	$sKaryawanId = "SELECT MAX(tanggal) as tanggalakhir, MIN(tanggal) as tanggalawal FROM ".$dbname.".sdm_absensiht WHERE periode = '".$Periode."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	

	return $rKaryawanId;
}

function GetDataPD($DetailPeriode){
	global $dbname;
	$sSelectADt = "SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, c.namakaryawan, a.kodeorg 
	FROM ".$dbname.".sdm_pjdinasht a
	LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
	WHERE a.tanggalperjalanan <= '".$DetailPeriode->tanggalakhir."' and a.tanggalkembali >= '".$DetailPeriode->tanggalawal."' 
	AND a.karyawanid like '%%' AND statuspersetujuan='1' AND statushrd='1'
    order by a.tanggalperjalanan, a.tanggalkembali";
	$QSelectADt = mysql_query($sSelectADt) or die(mysql_error());
	#$RSelectADt = mysql_fetch_object($QSelectADt);
	
	return $QSelectADt;
}

function GetDataCI($DetailPeriode){
	global $dbname;
	$sSelectADt = "SELECT 
	a.karyawanid, 
	substr(a.darijam,1,10) as daritanggal, 
	substr(a.sampaijam,1,10) as sampaitanggal, 
	a.jenisijin, 
	a.tanggal,
	a.keperluan,
	a.darijam,
	a.sampaijam,
	a.jumlahhari,
	a.keterangan,
	c.namakaryawan, 
	c.lokasitugas, 
	d.jenisizincuti, 
	d.kodeabsen,
	c.kodeorganisasi 
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
	LEFT JOIN ".$dbname.".sdm_jenis_ijin_cuti d on a.jenisijin=d.id   	
    WHERE substr(a.darijam,1,10) <= '".$DetailPeriode->tanggalakhir."' and substr(a.sampaijam,1,10) >= '".$DetailPeriode->tanggalawal."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
        and a.karyawanid like '%%'
    ORDER BY a.darijam, a.sampaijam";
	$QSelectADt = mysql_query($sSelectADt) or die(mysql_error());
	#$RSelectADt = mysql_fetch_object($QSelectADt);
	
	return $QSelectADt;
}

if($typesync == 'CI') {
	## GET LIST CUTI ##
	$DetailPeriode = GetFirstAndLastPeriode($periodeysnc);
	$DataCI = GetDataCI($DetailPeriode);
	#pre($DataCI);exit();
	$DataCI2 = GetDataCI($DetailPeriode);
		
	while($bar=mysql_fetch_object($DataCI))
	{
		$current = strtotime( $bar->daritanggal );
		$last = strtotime( $bar->sampaitanggal );
		
		while( $current <= $last ) {
			$kodeorg = $bar->kodeorganisasi;
			$tanggal = date( 'Y-m-d', $current );
			$karyawanid = $bar->karyawanid;
			$absensi = $bar->kodeabsen;
			$jam = '00:00:00';
			$jamPlg = '00:00:00';
			$penjelasan = '';
			
			DeleteADt($kodeorg, $tanggal, $karyawanid);
			InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan);
			$current = strtotime( '+1 day', $current );
		}
	}
	
	while($bar=mysql_fetch_object($DataCI))
	{
		$kodeorg = GetKaryawanKodeOrg($bar->karyawanid);
		$tanggal = $bar->tanggal;
		$karyawanid = $bar->karyawanid;
		$absensi = $bar->kodeabsen;
		$jam = '00:00:00';
		$jamPlg = '00:00:00';
		$penjelasan = $bar->keterangan;
		
		$CekData = SelectADt($tanggal, $karyawanid);
		
		if($CekData == 0) {
			$InsertData = InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan);
			if($InsertData == true) {
				continue;
			} else {
				echo $InsertData;
				exit();
			}
		} else {
			DeleteADt($kodeorg, $tanggal, $karyawanid);
			$InsertData = InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan);
		}
	}
	
	$stream = '';
	## Details ##
	$stream.="
		<tr><td colspan=9 align=center>Data yg berhasil di synchronize</td></tr>
		<tr class='rowheader'>
			<td align=center >No</td>
			<td align=center >".$_SESSION['lang']['nama']."</td>
			<td align=center >".$_SESSION['lang']['tanggal']."</td>
			<td align=center >".$_SESSION['lang']['keperluan']."</td>
			<td align=center colspan='2'>".$_SESSION['lang']['darisampai']."</td>
			<td align=center >".$_SESSION['lang']['jenisijin']."</td>
			<td align=center >".$_SESSION['lang']['jumlahhari']."</td>
			<td align=center >".$_SESSION['lang']['keterangan']."</td>
		</tr>
	";
	
	$row=mysql_num_rows($DataCI2);
	$no=0;
	if($row>0){
		while($res=mysql_fetch_assoc($DataCI2)){
			$no+=1;
			$stream.="
				<tr>
					<td>".$no."</td>
					<td>".GetKaryawanName($res['karyawanid'])."</td>
					<td>".$res['tanggal']."</td>
					<td>".$res['keperluan']."</td>
					<td>".$res['darijam']."</td>
					<td>".$res['sampaijam']."</td>
					<td>".$res['jenisizincuti']."</td>
					<td>".$res['jumlahhari']."</td>
					<td>".$res['keterangan']."</td>
				</tr>";
		}
	}

	else{$stream.="<tr><td colpsan=10>Not Found</td></tr>";}
	echo $stream;
} 
elseif($typesync == 'PD'){
	$DetailPeriode = GetFirstAndLastPeriode($periodeysnc);
	$DataPD = GetDataPD($DetailPeriode);
	$DataPD2 = GetDataPD($DetailPeriode);
		
	while($bar=mysql_fetch_object($DataPD))
	{
		$current = strtotime( $bar->tanggalperjalanan );
		$last = strtotime( $bar->tanggalkembali );
		
		while( $current <= $last ) {
			$kodeorg = $bar->kodeorg;
			$tanggal = date( 'Y-m-d', $current );
			$karyawanid = $bar->karyawanid;
			$absensi = 'PD';
			$jam = '00:00:00';
			$jamPlg = '00:00:00';
			$penjelasan = '';
			
			$CekData = SelectPD($tanggal, $karyawanid);
			
			if($CekData == 0) {
				$InsertData = InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan);
				if($InsertData == true) {
					continue;
				} else {
					echo $InsertData;
					exit();
				}
			} else {
				DeleteADt($kodeorg, $tanggal, $karyawanid);
				$InsertData = InsertADt($kodeorg, $tanggal, $karyawanid, $absensi, $jam, $jamPlg, $penjelasan);
			}

			$current = strtotime( '+1 day', $current );
		}
	}
	
	$stream = '';
	## Details ##
	$stream.="
		<tr><td colspan=9 align=center>Data yg berhasil di synchronize</td></tr>
		<tr class='rowheader'>
			<td align=center >No</td>
			<td align=center >".$_SESSION['lang']['nama']."</td>
			<td align=center colspan='2'>".$_SESSION['lang']['tanggalpergi']."</td>
			<td align=center >".$_SESSION['lang']['tujuan1']."</td>
			<td align=center >".$_SESSION['lang']['tujuan2']."</td>
			<td align=center >".$_SESSION['lang']['tujuan3']."</td>
		</tr>
	";
	
	$row=mysql_num_rows($DataPD2);
	$no=0;
	if($row>0){
		while($res=mysql_fetch_assoc($DataPD2)){
			$no+=1;
			$stream.="
				<tr>
					<td>".$no."</td>
					<td>".$res['namakaryawan']."</td>
					<td>".$res['tanggalperjalanan']."</td>
					<td>".$res['tanggalkembali']."</td>
					<td>".$res['tujuan1']."</td>
					<td>".$res['tujuan2']."</td>
					<td>".$res['tujuan3']."</td>
				</tr>";
		}
	}

	else{$stream.="<tr><td colpsan=10>Not Found</td></tr>";}
	echo $stream;
}
?>