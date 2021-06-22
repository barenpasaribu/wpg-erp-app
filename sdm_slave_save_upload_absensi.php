<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$method			= isset($_POST['method']) ? $_POST['method'] : null;
$StartDate		= isset($_POST['StartDate']) ? $_POST['StartDate'] : 0;
$EndDate		= isset($_POST['EndDate']) ? $_POST['EndDate'] : 0;
$total_days 	= round(abs(strtotime($EndDate) - strtotime($StartDate)) / 86400, 0) + 1;
#showerror();
function GetPeriode($TanggalMulai, $TanggalSampai){
	global $dbname;
	$sPeriode = "SELECT periode FROM ".$dbname.".setup_periodeakuntansi 
	WHERE '".$TanggalMulai."' >= tanggalmulai 
	AND '".$TanggalSampai."' <= tanggalsampai
	AND kodeorg = '".$_SESSION['empl']['lokasitugas']."'";
	$qPeriode = mysql_query($sPeriode);
	$rPeriode = mysql_fetch_object($qPeriode);
	
	return $rPeriode->periode;
}

function CekAbsensi($tanggal, $kodeorg, $periode){
	global $dbname;
	$sCekAbsensiHt = "SELECT COUNT(*) FROM ".$dbname.".sdm_absensiht WHERE 
	tanggal = '".$tanggal."' 
	AND kodeorg = '".$kodeorg."' 
	AND periode = '".$periode."';";
	$qCekAbsensiHt = mysql_query($sCekAbsensiHt);
	$rCekAbsensiHt = mysql_fetch_row($qCekAbsensiHt);
	
	return $rCekAbsensiHt[0];
}

function CekTempData($scan_date){
	global $dbname;
	$STempData = "select COUNT(*) from ".$dbname.".att_log_temp where scan_date LIKE '".$scan_date."%'";
	$QTempData = mysql_query($STempData) or die(mysql_error());
	$RTempData = mysql_fetch_row($QTempData);
	
	return $RTempData[0];
}

function CekTempDataXls($scan_date){
	global $dbname;
	$STempData = "select COUNT(*) from ".$dbname.".sdm_upload where tanggal LIKE '".$scan_date."%'";
	$QTempData = mysql_query($STempData) or die(mysql_error());
	$RTempData = mysql_fetch_row($QTempData);
	
	return $RTempData[0];
}

function GetKaryawanId($pin){
	global $dbname;
	$sKaryawanId = "select karyawanid from ".$dbname.".att_adaptor where pin = '".$pin."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	
	
	return $rKaryawanId->karyawanid;
}

function GetKaryawanShiftByNik($nik){
	global $dbname;
	$sKaryawanId = "select kode_shift from ".$dbname.".datakaryawan where nik = '".$nik."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	
	
	return $rKaryawanId->kode_shift;
}

function GetKaryawanIdByNik($nik){
	global $dbname;
	$sKaryawanId = "select karyawanid from ".$dbname.".datakaryawan where nik = '".$nik."';";
	$qKaryawanId = mysql_query($sKaryawanId);
	$rKaryawanId = mysql_fetch_object($qKaryawanId);	
	#pre($sKaryawanId);
	return $rKaryawanId->karyawanid;
}

function CekAbsensiDt($kodeorg, $tanggal, $KaryawanId){
	global $dbname;
	$SAbsensiDt = "SELECT COUNT(*) FROM ".$dbname.".sdm_absensidt WHERE kodeorg = '".$kodeorg."' AND tanggal = '".$tanggal."' AND karyawanid = '".$KaryawanId."';";
	$QAbsensiDt = mysql_query($SAbsensiDt) or die(mysql_error());
	$RAbsensiDt = mysql_fetch_row($QAbsensiDt);	
	
	return $RAbsensiDt[0];
}

function GetShiftLate($jam_masuk, $karyawanid, $kodeorg, $PeriodeCuti, $nik, $tanggal){
	global $dbname;

	$SShift = "select
	a.nama,a.kode,a.jam_masuk,kd_organisasi,a.jam_keluar,
	b.nik,b.nama as namakaryawan, b.tanggal, b.scan_masuk, b.scan_pulang,
	CASE WHEN TIMEDIFF(b.scan_masuk,a.jam_masuk)<0 THEN 0 ELSE TIMEDIFF(b.scan_masuk,a.jam_masuk) END as Keterlambat,
	CASE WHEN TIMEDIFF(a.jam_keluar,b.scan_pulang)<0 THEN 0 ELSE TIMEDIFF(a.jam_keluar,b.scan_pulang) END as PulangCepat,
	CASE WHEN TIMEDIFF(b.scan_pulang,a.jam_keluar)<0 THEN 0 ELSE TIMEDIFF(b.scan_pulang,a.jam_keluar) END as Lembur 	 	
	FROM
	sdm_shift a INNER JOIN sdm_upload b ON a.nama=b.jam_kerja 
	WHERE a.kd_organisasi = '".$_SESSION['empl']['lokasitugas']."'
	AND b.nik = '".$nik."'
	AND b.tanggal = '".$tanggal."'
	;";
	#pre($SShift);
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
		
		if($TotalPulangCepat > $JamTelat){
			$SisaCuti = GetSisaCuti($kodeorg, $karyawanid, $PeriodeCuti);
			#echo $karyawanid.'-'.$kodeorg;
			#pre($SisaCuti);
			if($SisaCuti[0]['sisa'] == 0) {
				$Shift['absensi'] = 'I2';
			} else {
				$Shift['absensi'] = 'PC';
			}
		} else if($TotalPulangCepat > $JamOnTime){
			$Shift['absensi'] = 'I1';
		}
		
		$Shift['shift'] = $row['nama'];
		return $Shift;
	}
	#pre($Shift); #exit();
	return $Shift;
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
	#pre($rSisaCuti);
	return $rSisaCuti;	
}

function GetTempData($kodeorg){
	global $dbname;
	$STempData = "SELECT min(STR_TO_DATE(scan_date,'%Y-%m-%d')) as tanggal,
	max(case when verify_mode=0 then substring(scan_date,12,8) else ' ' end) as jammasuk,
	max(case when verify_mode=1 then substring(scan_date,12,8) else ' ' end) as jampulang,
	 pin 
	from ".$dbname.".att_log_temp group by pin";
	$QTempData = mysql_query($STempData);
	$ATempData = array();
	$Count = 0;
	while( $row = mysql_fetch_assoc( $QTempData)){
		#$GShiftLate = GetShiftLate($jamkerja, $jam_masuk, $karyawanid, $kodeorg, $PeriodeCuti, $karyawannik)
	
		$ATempData[$Count]['kodeorg'] = $kodeorg;
		$ATempData[$Count]['tanggal'] = $row['tanggal'];
		$ATempData[$Count]['karyawanid'] = GetKaryawanId($row['pin']);
		$ATempData[$Count]['shift'] = $GShiftLate['shift'];
		$ATempData[$Count]['absensi'] = $GShiftLate['absensi'];
		$ATempData[$Count]['jam'] = $row['jammasuk'];
		$ATempData[$Count]['jampulang'] = $row['jampulang'];
		$Count++;
	}
	return $ATempData;
}

function GetTempDataXLS($kodeorg, $tanggal){
	global $dbname;
	$STempData = "SELECT 
	CASE WHEN IFNULL(scan_masuk,' ') != ' '	AND IFNULL(scan_pulang,' ') != ' ' 
	THEN tanggal ELSE ' ' END AS tanggal,
	
	CASE WHEN IFNULL(scan_masuk,' ') != ' '	AND IFNULL(scan_pulang,' ') != ' ' 
	THEN scan_masuk ELSE ' ' END AS jam,
	
	CASE WHEN IFNULL(scan_masuk,' ') != ' '	AND IFNULL(scan_pulang,' ') != ' ' 
	THEN scan_pulang ELSE ' ' END AS jamPlg,
	nik, jam_kerja,nama
	FROM ".$dbname.".sdm_upload where tanggal = '".$tanggal."';";
	#pre($STempData);
	$QTempData = mysql_query($STempData);
	$ATempData = array();
	$Count = 0;
	while( $row = mysql_fetch_assoc( $QTempData)){
		if($row['tanggal'] == ' ') {
			continue;
		} else {
			$PeriodeCuti = substr($row['tanggal'],0,4);
			$KaryawanId = GetKaryawanIdByNik($row['nik']);
			#echo $KaryawanId; #exit();
			$GShiftLate = GetShiftLate($row['jam'], $KaryawanId, $kodeorg, $PeriodeCuti, $row['nik'], $row['tanggal']);
			#pre($GShiftLate);
			$ATempData[$Count]['kodeorg'] = $kodeorg;
			$ATempData[$Count]['tanggal'] = $row['tanggal'];
			$ATempData[$Count]['karyawanid'] = $KaryawanId;
			$ATempData[$Count]['shift'] = $GShiftLate['shift'];
			$ATempData[$Count]['absensi'] = $GShiftLate['absensi'];
			$ATempData[$Count]['jam'] = $row['jam'];
			$ATempData[$Count]['jampulang'] = $row['jamPlg'];
			if(empty($GShiftLate['shift']) || $GShiftLate['shift'] == '') {
				$ATempData[$Count]['error'] = 'Proses Absensi Gagal. Shift tidak terdaftar. Mohon cek Jam Kerja pada Excel dengan Nama Shift pada Master Shift (Harus sama) dan Organisasi User yang mengupload harus sama.';
			}
			if(empty($KaryawanId) || $KaryawanId == '') {
				$ATempData[$Count]['error'] = 'Proses Absensi Gagal. Karyawan atas nama '.$row['nama'].' dengan NIK '.$row['nik'].' tidak terdaftar.';
			}
		}
		$Count++;
		#echo "A";
		#pre($row);
	}
	
	#pre($STempData); #exit();
	return $ATempData;
}

function CheckKaryawanIjin($kodeorg, $karyawanid, $tanggal){
	global $dbname;
	
	$SAbsensiDt = "SELECT COUNT(*) FROM ".$dbname.".sdm_absensidt WHERE kodeorg = '".$kodeorg."' AND tanggal = '".$tanggal."' AND karyawanid = '".$karyawanid."';";
	$QAbsensiDt = mysql_query($SAbsensiDt) or die(mysql_error());
	$RAbsensiDt = mysql_fetch_row($QAbsensiDt);	
	
	return $RAbsensiDt[0];
}

function GetKaryawanMasuk($StartDate, $EndDate, $LokasiTugas){
	global $dbname;
	
	$sKaryawanMasuk = "SELECT a.*, b.karyawanid, b.nik, b.namakaryawan FROM ".$dbname.".sdm_absensidt a
	LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
	WHERE a.tanggal >= '".$StartDate."' and a.tanggal <= '".$EndDate."' 
	and a.kodeorg like '".$LokasiTugas."'
	ORDER BY a.karyawanid, a.tanggal ASC";
	
	$rKaryawanMasuk = fetchData($sKaryawanMasuk);
	#pre($rKaryawanMasuk);exit();
	return $rKaryawanMasuk;
}

function GetKaryawanIjin($StartDate, $EndDate, $KodeOrganisasi){
	global $dbname;
	
	$sKaryawanIjin = "SELECT 
	a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, a.periodecuti,
	b.bagian, b.namakaryawan, b.kodeorganisasi
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
    WHERE substr(a.darijam,1,10) >= '".$StartDate."' and substr(a.sampaijam,1,10) <= '".$EndDate."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1' and jumlahhari > '0'
	and b.kodeorganisasi like '%".$KodeOrganisasi."%'
    ORDER BY a.darijam, a.sampaijam";
	
	$rKaryawanIjin = fetchData($sKaryawanIjin);
	#pre($rKaryawanIjin);exit();
	foreach($rKaryawanIjin as $kKaryawanIjin => $vKaryawanIjin)
	{
		$Awal = $vKaryawanIjin['daritanggal'];
		$Akhir = $vKaryawanIjin['sampaitanggal'];
		
		while (strtotime($Awal) <= strtotime($Akhir)) {
			$ListData[$vKaryawanIjin['karyawanid']][$Awal] = $vKaryawanIjin['periodecuti'];
			$Awal = date ("Y-m-d", strtotime("+1 day", strtotime($Awal)));
		}
	} 

	#pre($ListData); exit();
	return $ListData;
}

switch($method){
	case 'ProcessDataCSV':	
	if ($EndDate >= $StartDate) {
		for ($day = 0; $day < $total_days; $day++){
			$Periode = date("Y-m", strtotime("{$StartDate} + {$day} days"));
			$CekAbsensi = CekAbsensi(date("Y-m-d", strtotime("{$StartDate} + {$day} days")), $_SESSION['empl']['lokasitugas'], $Periode);
			#pre($CekAbsensi); exit();
			
			## IF HEADER NOT EXISTS, INSERT NEW ONE ##
			if($CekAbsensi == 0) {
				$sAbsensiHt = "INSERT INTO ".$dbname.".sdm_absensiht 
				(`tanggal`, `kodeorg`, `periode`) VALUES 
				('".date("Y-m-d", strtotime("{$StartDate} + {$day} days"))."', '".$_SESSION['empl']['lokasitugas']."', '".$Periode."');";
				if(!mysql_query($sAbsensiHt)) {
					echo "<script>alert('Failed');</script>";
					die(mysql_error());
				## IF QUERY SUCCESS, INSERT DETAILS ##
				} else {
					## CEK TEMP DATA ##
					$CekTempData = CekTempData(date("Y-m-d", strtotime("{$StartDate} + {$day} days")));
					## IF TEMP DATA EXITS ##
					if($CekTempData != 0) {
						## INSERT DETAILS DATA ##
						## SET VARIABLE DATA ##	
						$kodeorg	= isset($_SESSION['empl']['lokasitugas']) ? $_SESSION['empl']['lokasitugas'] : '';
						$TempData 	= GetTempData($kodeorg);

						foreach($TempData as $TDKey => $TDVal){
							## CEK SDM ABSENSIDT ##
							$CAbsensiDt = CekAbsensiDt($TDVal['kodeorg'], $TDVal['tanggal'], $TDVal['karyawanid']);									
							if($CAbsensiDt == 0) {
								$sAbsensiDt = "INSERT INTO ".$dbname.".sdm_absensidt 
								(`kodeorg`, `tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `jamPlg`) VALUES 
								('".$TDVal['kodeorg']."', '".$TDVal['tanggal']."', '".$TDVal['karyawanid']."', '".$TDVal['shift']."', '".$TDVal['absensi']."', '".$TDVal['jam']."', '".$TDVal['jampulang']."');";
								
								if(!mysql_query($sAbsensiDt)) {
									echo "<script>alert('Failed');</script>";
									die(mysql_error());
								}
							} else {
								mysql_query("DELETE FROM ".$dbname.".sdm_absensidt WHERE (`kodeorg`='".$TDVal['kodeorg']."') AND (`tanggal`='".$TDVal['tanggal']."') AND (`karyawanid`='".$TDVal['karyawanid']."')");
								$sAbsensiDt = "INSERT INTO ".$dbname.".sdm_absensidt 
								(`kodeorg`, `tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `jamPlg`) VALUES 
								('".$TDVal['kodeorg']."', '".$TDVal['tanggal']."', '".$TDVal['karyawanid']."', '".$TDVal['shift']."', '".$TDVal['absensi']."', '".$TDVal['jam']."', '".$TDVal['jampulang']."');";
								
								if(!mysql_query($sAbsensiDt)) {
									echo "<script>alert('Failed');</script>";
									die(mysql_error());
								}
							}
						}
					}
				}
			## IF HEADER ALREADY EXISTS, INSERT DETAILS ##
			} else {
				## CEK TEMP DATA ##
				$CekTempData = CekTempData(date("Y-m-d", strtotime("{$StartDate} + {$day} days")));
				## IF TEMP DATA EXITS ##
				if($CekTempData != 0) {
					## INSERT DETAILS DATA ##
					## SET VARIABLE DATA ##	
					$kodeorg	= isset($_SESSION['empl']['lokasitugas']) ? $_SESSION['empl']['lokasitugas'] : '';
					$TempData 	= GetTempData($kodeorg);
					
					foreach($TempData as $TDKey => $TDVal){
						## CEK SDM ABSENSIDT ##
						$CAbsensiDt = CekAbsensiDt($TDVal['kodeorg'], $TDVal['tanggal'], $TDVal['karyawanid']);					
						if($CAbsensiDt == 0) {
							$sAbsensiDt = "INSERT INTO ".$dbname.".sdm_absensidt 
							(`kodeorg`, `tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `jamPlg`) VALUES 
							('".$TDVal['kodeorg']."', '".$TDVal['tanggal']."', '".$TDVal['karyawanid']."', '".$TDVal['shift']."', '".$TDVal['absensi']."', '".$TDVal['jam']."', '".$TDVal['jampulang']."');";
							
							if(!mysql_query($sAbsensiDt)) {
								echo "<script>alert('Failed');</script>";
								die(mysql_error());
							}
						} else {
							$DelData = "DELETE FROM ".$dbname.".sdm_absensidt WHERE (`kodeorg`='".$TDVal['kodeorg']."') AND (`tanggal`='".$TDVal['tanggal']."') AND (`karyawanid`='".$TDVal['karyawanid']."');";
							if(!mysql_query($DelData)) {
								echo "<script>alert('Failed');</script>";
								die(mysql_error());
							}
							#echo $DelData;
							$sAbsensiDt = "INSERT INTO ".$dbname.".sdm_absensidt 
							(`kodeorg`, `tanggal`, `karyawanid`, `shift`, `absensi`, `jam`, `jamPlg`) VALUES 
							('".$TDVal['kodeorg']."', '".$TDVal['tanggal']."', '".$TDVal['karyawanid']."', '".$TDVal['shift']."', '".$TDVal['absensi']."', '".$TDVal['jam']."', '".$TDVal['jampulang']."');";
							#echo $sAbsensiDt; exit();
							if(!mysql_query($sAbsensiDt)) {
								echo "<script>alert('Failed');</script>";
								die(mysql_error());
							}
						}
					}
				}
			}
		}
	}else {
		echo "<script>alert('Start date must bigger than End date, please try again.');</script>";
	}
	break;
	case 'ProcessDataXLS':	
	#showerror();
	if($EndDate == 0 || $StartDate == 0){
		echo "Error : Tanggal Awal dan Akhir Error";
		exit();
	}
	if ($EndDate >= $StartDate) {
		for ($day = 0; $day < $total_days; $day++){
			$Periode = date("Y-m", strtotime("{$StartDate} + {$day} days"));
			$WhereAbsensi = "tanggal = '".date("Y-m-d", strtotime("{$StartDate} + {$day} days"))."' and kodeorg = '".$_SESSION['empl']['lokasitugas']."' and periode = '".$Periode."'";
			## DELETE HEADER ##
			mysql_query(deleteQuery($dbname,'sdm_absensiht',$WhereAbsensi));
			
			## INSERT HEADER ##
			$InsertAbsensiHt = array(
				'tanggal' => date("Y-m-d", strtotime("{$StartDate} + {$day} days")),
				'kodeorg' => $_SESSION['empl']['lokasitugas'],
				'periode' => $Periode,
				'posting' => '0',
				'postingby' => 0
			);
			if(mysql_query(insertQuery($dbname,'sdm_absensiht',$InsertAbsensiHt))){
				$TempDataXls = GetTempDataXLS($_SESSION['empl']['lokasitugas'], date("Y-m-d", strtotime("{$StartDate} + {$day} days")));
				#pre($TempDataXls);exit();
				if(!empty($TempDataXls)) {
					foreach($TempDataXls as $TDKey => $TDVal){
						if(isset($TDVal['error']) && $TDVal['error'] != ''){
							echo "Error : ".$TDVal['error'];
							exit();
						}
						## DELETE DETAILS ##
						$WhereAbsensiDt = "kodeorg = '".$TDVal['kodeorg']."' and tanggal = '".$TDVal['tanggal']."' and karyawanid = '".$TDVal['karyawanid']."'";
						mysql_query(deleteQuery($dbname,'sdm_absensidt',$WhereAbsensiDt));

						## INSERT DETAILS ##
						$InsertAbsensiDt = array(
							'kodeorg' 	=> $TDVal['kodeorg'],
							'tanggal' 	=> $TDVal['tanggal'],
							'karyawanid'=> $TDVal['karyawanid'],
							'shift' 	=> $TDVal['shift'],
							'absensi' 	=> $TDVal['absensi'],
							'jam' 		=> $TDVal['jam'],
							'jamPlg' 	=> $TDVal['jampulang'],
							'penjelasan'=>'',
							'catu' 		=>0,
							'penaltykehadiran'=>0,
							'premi' 	=>0,
							'insentif' 	=>0,
							'manualentry' => 0
						);
						if(!mysql_query(insertQuery($dbname,'sdm_absensidt',$InsertAbsensiDt))){
							echo "Error : ".insertQuery($dbname,'sdm_absensidt',$InsertAbsensiDt);
							exit();
						}
						
					}
				}
			} else {
				echo "Error : ".insertQuery($dbname,'sdm_absensiht',$InsertAbsensiHt);
			}
		}
	}else {
		echo "<script>alert('Start date must bigger than End date, please try again.');</script>";
	}
	break;
	case 'RestoreCuti':
	#showerror();
		echo "<div style='overflow:auto;height:400px;'><fieldset><legend><b>Ini Daftar Karyawan yang cuti tapi dia masuk</b></legend>";
		$Table = "<table cellspacing=1 border=1><thead><tr>";
		$Table.= "<td style='width:10px;' align=center>No</td>";
		$Table.= "<td align=center>Nik</td>";
		$Table.= "<td align=center>Nama</td>";
		$Table.= "<td align=center>Tanggal</td>";
		$Table.= "</tr></thead><tbody id=container>";
			 
		$DataKaryawanMasuk = GetKaryawanMasuk($StartDate, $EndDate, $_SESSION['empl']['lokasitugas']);
		$DataKaryawanIjin = GetKaryawanIjin($StartDate, $EndDate, $_SESSION['empl']['lokasitugas']);
		
		$No = 0;
		foreach($DataKaryawanMasuk as $DKMKey => $DKMVal){
			if(isset($DataKaryawanIjin[$DKMVal['karyawanid']][$DKMVal['tanggal']])) {
				$No+=1;
				$Table.='<tr>';
				$Table.='<td>'.$No.'</td>';
				$Table.='<td>'.$DKMVal['nik'].'</td>';
				$Table.='<td>'.$DKMVal['namakaryawan'].'</td>';
				$Table.='<td>'.$DKMVal['tanggal'].'</td>';
				$Table.='</tr>';
				$Table.='<input type=hidden id=kodeorg-'.$No.' value='.$DKMVal['kodeorg'].'>';
				$Table.='<input type=hidden id=karyawanid-'.$No.' value='.$DKMVal['karyawanid'].'>';
				$Table.='<input type=hidden id=tanggalcuti-'.$No.' value='.$DKMVal['tanggal'].'>';
			}
		} 
		
		$Table.="<tr><td colspan=4><button id='BtnRestoreCuti' class=mybutton onclick=ProcessRestoreCuti(".$No.")>Balikin Cutinya</button></td></tr>";
		$Table.="</thead></table></fieldset></div>";
		echo $Table;
	break;
	case 'ProcessRestoreCuti':
	#showerror();
		foreach(json_decode($_POST['ListRecords']) as $LR) {
			$Cuti = GetSisaCuti($LR->kodeorg, $LR->karyawanid, $LR->tanggalcuti);
			$SisaCuti = $Cuti[0]['sisa'] += 1;
			$CutiDiambil = $Cuti[0]['diambil'] -=1;
			
			mysql_query("update ".$dbname.".sdm_cutiht set diambil='".$CutiDiambil."',
			sisa=".$SisaCuti."
			where kodeorg='".$LR->kodeorg."' and karyawanid='".$LR->karyawanid."' and periodecuti='".$Cuti[0]['periodecuti']."'");
		}
	break;
	default:
	  echo "<script>alert('Case Error');</script>";
	break;	
}
?>
