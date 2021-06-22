<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
include('master_mainMenu.php');
OPEN_BOX_HRD('',$_SESSION['lang']['uploadabsensi']);
?>
<script language=javascript1.2 src='js/sdm_upload_absensi.js'></script>
<fieldset style='width:600px;'>
<table><form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	<tr>
		<td><? echo $_SESSION['lang']['upload_file'] ?></td>
		<td><? echo $_SESSION['lang']['tanggalmulai'] ?></td>
		<td><? echo $_SESSION['lang']['tanggalselesai'] ?></td>
	</tr>
	<tr>
		<td><input type="file" name="file"></td>
		<td><input name=tgl01 type=text class=myinputtext id=tgl01 onchange=bersih0() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10></td>
		<td><input name=tgl02 type=text class=myinputtext id=tgl02 onchange=bersih0() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10></td>
		<td><input type="submit" name="btn_submit" value="<? echo $_SESSION['lang']['upload_file'] ?>" /></td>
	</tr>
</form></table>
</fieldset>
<div id='FieldsetListData'>
<?
function CSV_Doc($File, $StartDate, $EndDate, $dbname){
	## EMPTY TEMP ATT_LOG_TEMP TABLE##
	mysql_query("TRUNCATE TABLE ".$dbname.".att_log_temp");
	
	## INSERT FILE INTO ATT_LOG_TEMP TABLE ##
	while($row = fgetcsv($File, 8192)) {
		#echo '<pre>';print_r($row);echo '</pre>';
		## CONVERT SCAN DATE AND TAKEN DATE ##
		$ScanDate = str_replace('/', '-', $row[1]);
		$ScanDate = date('Y-m-d H:i:s', strtotime($ScanDate));
		$Taken = str_replace('/', '-', $row[10]);
		$Taken = date('Y-m-d H:i:s', strtotime($Taken));
		#`sn`, `scan_date`, `pin`, `verify_mode`, `io_mode`, `work_code`, `ex_id`, `flag`, `rowguid`, `io_mode_update`, `taken`
		if(!mysql_query("INSERT INTO ".$dbname.".att_log_temp 
			(`sn`, `scan_date`, `pin`, `verify_mode`, `io_mode`, `work_code`, `ex_id`, `flag`, `rowguid`, `io_mode_update`, `taken`)
			VALUES 
			('$row[0]', '$ScanDate', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]', '$row[9]', '$Taken');")) {
			echo "<script>alert('Filetype not support');</script>";
			die(mysql_error());
		}
	}
	
	### COMPARE TEMP TABLE WITH ORI TABLE ###
	## GET LIST DATA TEMP TABLE ##
	$QTempData = "select * from ".$dbname.".att_log_temp where scan_date >= '".$StartDate." 00:00:00' and scan_date <= '".$EndDate." 23:59:59' order by att_id";
	$TempData = mysql_query($QTempData) or die(mysql_error());
	$TempData2 = mysql_query($QTempData) or die(mysql_error());
	$Check = array();
	
	while($TDF = mysql_fetch_object($TempData)){
		$Temp = mysql_query("SELECT * FROM ".$dbname.".att_log WHERE scan_date = '".$TDF->scan_date."' AND pin = '".$TDF->pin."';");
		$Check = mysql_fetch_array($Temp);
		
		if(!empty($Check)) {
			mysql_query("DELETE FROM ".$dbname.".att_log WHERE scan_date = '".$TDF->scan_date."' AND pin = '".$TDF->pin."';");
			mysql_query("INSERT INTO ".$dbname.".att_log 
			(`sn`, `scan_date`, `pin`, `verify_mode`, `io_mode`, `work_code`, `ex_id`, `flag`, `rowguid`, `io_mode_update`, `taken`)
			VALUES 
			('".$TDF->sn."', '".$TDF->scan_date."', '".$TDF->pin."', '".$TDF->verify_mode."', '".$TDF->io_mode."', '".$TDF->work_code."', '".$TDF->ex_id."', '".$TDF->flag."', '".$TDF->rowguid."', '".$TDF->io_mode_update."', '".$TDF->taken."');");
		} else {
			mysql_query("INSERT INTO ".$dbname.".att_log 
			(`sn`, `scan_date`, `pin`, `verify_mode`, `io_mode`, `work_code`, `ex_id`, `flag`, `rowguid`, `io_mode_update`, `taken`)
			VALUES 
			('".$TDF->sn."', '".$TDF->scan_date."', '".$TDF->pin."', '".$TDF->verify_mode."', '".$TDF->io_mode."', '".$TDF->work_code."', '".$TDF->ex_id."', '".$TDF->flag."', '".$TDF->rowguid."', '".$TDF->io_mode_update."', '".$TDF->taken."');");
		}
	}
	
	echo "<script>alert('".$_SESSION['lang']['uploadabsensisuccess']."');</script>";
	echo $_SESSION['lang']['listabsensiupload'];
	echo "<div>";
		$i = 1;
		echo"<table class=sortable cellspacing=1 border=0>
			 <thead>
			 <tr class=rowheader>
				<td style='width:50px;' align=center>No</td>
				<td style='width:50px;' align=center>SN</td>
				<td align=center>Scan Date</td>
				<td align=center>PIN</td>
				<td align=center>Verify Mode</td>
				<td align=center>Io Mode</td>
				<td align=center>Work Code</td>
				<td align=center>Ex Id</td>
				<td align=center>Flag</td>
				<td align=center>Rowguid</td>
				<td align=center>Io Mode Update</td>
				<td align=center>Taken</td></tr>
			 </thead>
			 <tbody id=container>"; 
		while($bar1=mysql_fetch_object($TempData2))
		{
			echo"<tr class=rowcontent>
					   <td align=center>".$i."</td>
					   <td align=center>".$bar1->sn."</td>
					   <td align=center>".$bar1->scan_date."</td>
					   <td align=center>".$bar1->pin."</td>
					   <td align=center>".$bar1->verify_mode."</td>
					   <td align=center>".$bar1->io_mode."</td>
					   <td align=center>".$bar1->work_code."</td>
					   <td align=center>".$bar1->ex_id."</td>
					   <td align=center>".$bar1->flag."</td>
					   <td align=center>".$bar1->rowguid."</td>
					   <td align=center>".$bar1->io_mode_update."</td>
					   <td align=center>".$bar1->taken."</td>
				</tr>";
			$i++;
		}
		echo "<input type='hidden' id='StartDate' value='".$StartDate."'>";
		echo "<input type='hidden' id='EndDate' value='".$EndDate."'>";
		echo "<tr><td colspan=10><button id='buttonsave' class=mybutton onclick=ProcessDataCSV()>Process Data</button></td></tr>";	 
		echo"	 
			 </tbody>
			 <tfoot>
			 </tfoot>
			 </table>";
	echo "</div>";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	## VALIDATION ##
	if(empty($_POST['tgl01']) || empty($_POST['tgl02'])) {
		echo "<script>alert('Start Date and End Date Cannot be Null');</script>";
		exit();
	}
	
	if(empty($_FILES['file'])) {
		echo "<script>alert('File Canno be Null');</script>";
		exit();
	}

	## SET VARIABLE GLOBAL ##
	$StartDate = date('Y-m-d', strtotime($_POST['tgl01']));
	$EndDate = date('Y-m-d', strtotime($_POST['tgl02']));
	$TypeDocAr = explode(".", $_FILES['file']['name']);
	$TypeDoc = $TypeDocAr[1];
	
	if($TypeDoc == 'csv') {
		$fh = fopen($_FILES['file']['tmp_name'], 'r+');
		CSV_Doc($fh, $StartDate, $EndDate, $dbname);
	} elseif ($TypeDoc == 'xls' || $TypeDoc == 'xlsx') {
		$File = $_FILES['file']['tmp_name'];
		Excel_Doc($File, $dbname, $StartDate, $EndDate);
	}
}

function CheckPIN($karyawanid){
	global $dbname;
	$sCekPin = "SELECT COUNT(*) FROM ".$dbname.".att_adaptor WHERE karyawanid = '".$karyawanid."';";
	$qCekPin = mysql_query($sCekPin);
	$rCekPin = mysql_fetch_row($qCekPin);
	
	if($rCekPin[0] == 0) {
		$sGetMaxPin = "SELECT MAX(pin) as maxpin FROM ".$dbname.".att_adaptor;";
		$qGetMaxPin = mysql_query($sGetMaxPin);
		$rGetMaxPin = mysql_fetch_object($qGetMaxPin);

		$nPin = $rGetMaxPin->maxpin+1;
		$sAddPin = "INSERT INTO ".$dbname.".att_adaptor (`karyawanid`, `pin`) VALUES ('".$karyawanid."', '".$nPin."')";
		$qAddPin = mysql_query($sAddPin);
		
		if($qAddPin) {
			$sPin =  "SELECT pin FROM ".$dbname.".att_adaptor WHERE karyawanid = '".$karyawanid."';";
			$qPin = mysql_query($sPin);
			$rPin = mysql_fetch_object($qPin);
			
			return $rPin->pin;
		} else {
			return mysql_error();
		}
	} else {
		$sPin = "SELECT pin FROM ".$dbname.".att_adaptor WHERE karyawanid = '".$karyawanid."';";
		$qPin = mysql_query($sPin);
		$rPin = mysql_fetch_object($qPin);
		
		return $rPin->pin;
	}
}

function Excel_Doc($File, $dbname, $StartDate, $EndDate){
	date_default_timezone_set('UTC');
	#showerror();
	require_once('lib/PHPExcel/PHPExcel/IOFactory.php');
	
	try {
		$inputFileType = PHPExcel_IOFactory::identify($File); //Identify the file
		#echo $inputFileType;
		$objReader = PHPExcel_IOFactory::createReader($inputFileType); //Creating the reader
		$objPHPExcel = $objReader->load($File); //Loading the file
	} catch (Exception $e) {
		die('Error loading file "' . pathinfo($File, PATHINFO_BASENAME) 
		. '": ' . $e->getMessage());
	}
	## EMPTY TEMP sdm_upload TABLE##
	mysql_query("TRUNCATE TABLE ".$dbname.".sdm_upload");
	
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();
	
	for ($row = 2; $row <= $highestRow; $row++) {
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
		#pre($rowData);
		$emp_no = isset($rowData[0][0]) ? $rowData[0][0] : 0;
		$no_id = isset($rowData[0][1]) ? $rowData[0][1] : 0;
		$nik = $rowData[0][2];
		$nama = str_replace("'","",$rowData[0][3]);
		$auto_assign = $rowData[0][4];
		$tanggal = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($rowData[0][5]));
		$jam_kerja = $rowData[0][6];
		
		$jam_masuk = date('H:i:s', strtotime($rowData[0][7]));
		$jam_pulang = date('H:i:s', strtotime($rowData[0][8]));
		
		$scan_masuk = date('H:i:s', strtotime($rowData[0][9]));
		$scan_pulang = date('H:i:s', strtotime($rowData[0][10]));
		#echo $rowData[0][10].'<br>';
		#echo $scan_pulang.'<br>';
		#echo $jam_masuk;
		#exit();
		$normal = 0;#$rowData[0][11];
		$riil = 0;#$rowData[0][12];
		$terlambat = date('H:i:s', strtotime($rowData[0][13]));
		$plg_cepat = date('H:i:s', strtotime($rowData[0][14]));
		$absent = $rowData[0][15];
		$lembur = $rowData[0][16];
		$jml_jam_kerja = $rowData[0][17];
		$pengecualian = $rowData[0][18];
		$harus_c_in = $rowData[0][19];
		$harus_c_out = $rowData[0][20];
		$departemen = $rowData[0][21];
		$hari_normal = 0;#$rowData[0][22];
		$akhir_pekan = $rowData[0][23];
		$hari_libur = $rowData[0][24];
		$jml_kehadiran = $rowData[0][25];
		$lembur_hari_normal = $rowData[0][26];
		$lembur_akhir_pekan = $rowData[0][27];
		$lembur_hari_libur = $rowData[0][28];
		
		$Pin = CheckPIN($nik);
		#pre($CheckPin); exit();
		#$Temp = mysql_query("SELECT * FROM ".$dbname.".att_log WHERE scan_date = '".$TDF->scan_date."' AND pin = '".$TDF->pin."';");
		#$Check = mysql_fetch_array($Temp);
		
		$Temp = mysql_query("SELECT * FROM ".$dbname.".sdm_upload WHERE tanggal = '".$tanggal."' AND nik = '".$nik."';");
		$Check = mysql_fetch_array($Temp);
		/*echo "<br>"."INSERT INTO ".$dbname.".sdm_upload 
			(`emp_no`, `no_id`, `nik`, `nama`, `auto_assign`, `tanggal`, `jam_kerja`, `jam_masuk`, `jam_pulang`, `scan_masuk`, `scan_pulang`, `normal`, `riil`, `terlambat`, `plg_cepat`, `absent`, `lembur`, `jml_jam_kerja`, `pengecualian`, `harus_c_in`, `harus_c_out`, `departemen`, `hari_normal`, `akhir_pekan`, `hari_libur`, `jml_kehadiran`, `lembur_hari_normal`, `lembur_akhir_pekan`, `lembur_hari_libur`)
			VALUES 
			('$emp_no', '$no_id', '$nik', '$nama', '$auto_assign', '$tanggal', '$jam_kerja', '$jam_masuk', '$jam_pulang', '$scan_masuk', '$scan_pulang', '$normal', '$riil', '$terlambat', '$plg_cepat', '$absent', '$lembur', '$jml_jam_kerja', '$pengecualian', '$harus_c_in', '$harus_c_out', '$departemen', '$hari_normal', '$akhir_pekan', '$hari_libur', '$jml_kehadiran', '$lembur_hari_normal', '$lembur_akhir_pekan', '$lembur_hari_libur');";*/
		
		if($scan_masuk != '00:00:00' || $scan_pulang != '00:00:00'){
			if(!empty($Check)) {
				mysql_query("DELETE FROM ".$dbname.".sdm_upload WHERE tanggal = '".$tanggal."' AND nik = '".$nik."';");
				mysql_query("INSERT INTO ".$dbname.".sdm_upload 
				(`emp_no`, `no_id`, `nik`, `nama`, `auto_assign`, `tanggal`, `jam_kerja`, `jam_masuk`, `jam_pulang`, `scan_masuk`, `scan_pulang`, `normal`, `riil`, `terlambat`, `plg_cepat`, `absent`, `lembur`, `jml_jam_kerja`, `pengecualian`, `harus_c_in`, `harus_c_out`, `departemen`, `hari_normal`, `akhir_pekan`, `hari_libur`, `jml_kehadiran`, `lembur_hari_normal`, `lembur_akhir_pekan`, `lembur_hari_libur`)
				VALUES 
				('$emp_no', '$no_id', '$nik', '$nama', '$auto_assign', '$tanggal', '$jam_kerja', '$jam_masuk', '$jam_pulang', '$scan_masuk', '$scan_pulang', '$normal', '$riil', '$terlambat', '$plg_cepat', '$absent', '$lembur', '$jml_jam_kerja', '$pengecualian', '$harus_c_in', '$harus_c_out', '$departemen', '$hari_normal', '$akhir_pekan', '$hari_libur', '$jml_kehadiran', '$lembur_hari_normal', '$lembur_akhir_pekan', '$lembur_hari_libur');");
			} else {
				mysql_query("INSERT INTO ".$dbname.".sdm_upload 
				(`emp_no`, `no_id`, `nik`, `nama`, `auto_assign`, `tanggal`, `jam_kerja`, `jam_masuk`, `jam_pulang`, `scan_masuk`, `scan_pulang`, `normal`, `riil`, `terlambat`, `plg_cepat`, `absent`, `lembur`, `jml_jam_kerja`, `pengecualian`, `harus_c_in`, `harus_c_out`, `departemen`, `hari_normal`, `akhir_pekan`, `hari_libur`, `jml_kehadiran`, `lembur_hari_normal`, `lembur_akhir_pekan`, `lembur_hari_libur`)
				VALUES 
				('$emp_no', '$no_id', '$nik', '$nama', '$auto_assign', '$tanggal', '$jam_kerja', '$jam_masuk', '$jam_pulang', '$scan_masuk', '$scan_pulang', '$normal', '$riil', '$terlambat', '$plg_cepat', '$absent', '$lembur', '$jml_jam_kerja', '$pengecualian', '$harus_c_in', '$harus_c_out', '$departemen', '$hari_normal', '$akhir_pekan', '$hari_libur', '$jml_kehadiran', '$lembur_hari_normal', '$lembur_akhir_pekan', '$lembur_hari_libur');");
			}
		}
	}
	
	$QTempData = "select * from ".$dbname.".sdm_upload order by su_id";
	$TempData = mysql_query($QTempData) or die(mysql_error());
	
	echo "<script>alert('Upload Absensi Sukses');</script>";
	echo "<legend><b>List Absensi Yang Berhasil Di Upload</b></legend>";
	echo "<div>";
		$i = 1;
		echo"<table class=sortable cellspacing=1 border=1>
			 <thead>
			 <tr class=rowheader>
				<td style='width:50px;' align=center>No</td>
				<td align=center>Nik</td>
				<td align=center>Nama</td>
				<td align=center>Tanggal</td>
				<td align=center>Jam Kerja</td>
				<td align=center>Jam Masuk</td>
				<td align=center>Jam Pulang</td>
				<td align=center>Scan Masuk</td>
				<td align=center>Scan Pulang</td>
				</tr>
			 </thead>
			 <tbody id=container>"; 
		while($bar1=mysql_fetch_object($TempData))
		{
			if($bar1->scan_masuk == '00:00:00') {$ScanMasuk = '-';} else $ScanMasuk = $bar1->scan_masuk;
			if($bar1->scan_pulang == '00:00:00') {$ScanPulang = '-';} else $ScanPulang = $bar1->scan_pulang;
			if($bar1->terlambat == '00:00:00') {$Terlambat = '-';} else $Terlambat = $bar1->terlambat;
			if($bar1->plg_cepat == '00:00:00') {$PlgCepat = '-';} else $PlgCepat = $bar1->plg_cepat;
			if($bar1->lembur == '00:00:00') {$Lembur = '-';} else $Lembur = $bar1->lembur;
			if($bar1->jml_jam_kerja == '00:00:00') {$JmlJamKerja = '-';} else $JmlJamKerja = $bar1->jml_jam_kerja;
			if($bar1->jml_kehadiran == '00:00:00') {$JmlKehadiran = '-';} else $JmlKehadiran = $bar1->jml_kehadiran;
			echo"<tr class=rowcontent>
					   <td align=center>".$i."</td>
					   <td align=center>".$bar1->nik."</td>
					   <td align=center>".$bar1->nama."</td>
					   <td align=center>".$bar1->tanggal."</td>
					   <td align=center>".$bar1->jam_kerja."</td>
					   <td align=center>".$bar1->jam_masuk."</td>
					   <td align=center>".$bar1->jam_pulang."</td>
					   <td align=center>".$bar1->scan_masuk."</td>
					   <td align=center>".$bar1->scan_pulang."</td>
				</tr>";
				$i++;
		}
		echo "<input type='hidden' id='StartDate2' value='".$StartDate."'>";
		echo "<input type='hidden' id='EndDate2' value='".$EndDate."'>";
		echo "<tr><td colspan=10><button id='buttonsave2' class=mybutton onclick=ProcessDataXLS()>Proses Data</button></td></tr>";	
		echo"	 
			 </tbody>
			 <tfoot>
			 </tfoot>
			 </table>";
	echo "</div>";
}
?>
<?php
CLOSE_BOX_HRD();
echo close_body_hrd();
?>