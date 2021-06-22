<?php

//## Unggah Kehadiran (tadinya hide nya '1' ..di table menu
//## to do : simpan nilai uang lembur

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';

$content_table = '';
$kdOrg = '';
$periode = '';
$tanggal_awal = '';
$tanggal_akhir = '';

if( isset($_POST['btnSubmit']) ){
	$kdOrg = $_POST['kdOrg'];
	$periode = $_POST['periode'];
	$pieces_period = explode("-",$periode);
	$periode_tahun = $pieces_period[0];
	$tanggal_awal = $_POST['inputTanggalAwal'];
	$tanggal_akhir = $_POST['inputTanggalAkhir'];
	
	//$time = strtotime('10/16/2003');
	$time = strtotime($tanggal_awal);
	$newformat_tanggal_awal = date('Ymd',$time);	
	$time = strtotime($tanggal_akhir);
	$newformat_tanggal_akhir = date('Ymd',$time);	
	
	if (($handle = fopen($_FILES["fileToUpload"]["tmp_name"], 'r')) !== FALSE) { // Check the resource is valid
	
		//taro file nya
		//echo $PATH_FILE_UPLOAD_ABSENSI;
		if(!is_dir($PATH_FILE_UPLOAD_ABSENSI)){
			//echo "false";
			//print_r($_FILES);
			if (!mkdir($PATH_FILE_UPLOAD_ABSENSI, 0777, true)) {
				die('Failed to create folders...'.$PATH_FILE_UPLOAD_ABSENSI);
			}
			
		}
		//coba dapetin extension_loaded
		$pieces = explode(".",$_FILES["fileToUpload"]["name"]);
		
		if( count($pieces) > 0){
			$file_ext = ".".$pieces[count($pieces)-1];
		}else{
			$file_ext = "";
		}
		
		
		//ambil faktor pengali lembur
		$flag_use_lembur = "N";
		$sql = "select kodeorg,tipelembur,jamaktual,jamlembur from sdm_5lembur  where kodeorg='".substr($kdOrg,0,4)."' order by jamaktual,tipelembur";
		//echo $sql;
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_data_pengali_lembur[$r_sql['tipelembur']][$r_sql['jamaktual']] = 	$r_sql['jamlembur'];
			$flag_use_lembur = "Y";			
		}
		
		
		//ambil referensi cuti
		$sql = "select karyawanid,tipeijin,darijam,cast(darijam as date) as daritanggal,sampaijam,cast(sampaijam as date) as sampaitanggal from sdm_ijin where cast(darijam as date) >= '".tanggalsystemd($tanggal_awal)."' and cast(sampaijam as date) <= '".tanggalsystemd($tanggal_akhir)."' and stpersetujuanhrd > 0";
		//echo $sql;
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			
			$arr_ref_cuti_daritgl[$r_sql['karyawanid']][] = $r_sql['daritanggal'];
			$arr_ref_cuti_sampaitgl[$r_sql['karyawanid']][] = $r_sql['sampaitanggal'];
			$arr_ref_cuti_tipeijin[$r_sql['karyawanid']][] = $r_sql['tipeijin'];
		}
		
		
		//ambil pilihan absenid
		$arr_pil_tipe_absen = array();
		$sql = "select * from sdm_5absensi order by keterangan";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_pil_tipe_absen[$r_sql['kodeabsen']] = $r_sql['keterangan'];
			
		}
		//pilihan shift
		$arr_pil_tipe_shift = array("NS SENJUM","NS SABTU","SHIFT 1","SHIFT 2","SHIFT 3");
		
		//ambil data hari libur selain hari minggu
		$arr_hari_libur = array();
		$sql = "select * from sdm_5harilibur where tanggal between '".tanggalsystemd($tanggal_awal)."' and '".tanggalsystemd($tanggal_akhir)."' and regional = '".$_SESSION['empl']['regional']."'";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_hari_libur[] = $r_sql['tanggal'];
			
		}
		
		//ambil data kodegolongan yang lembur
		$arr_list_group_golongan = array();
		$sql = "select * from sdm_5golongan where alias = 'lembur'";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_list_group_golongan[] = $r_sql['kodegolongan'];
			
		}
		
		
		$counter_row = 1;
		$counter_data = 1;
		$content_head = '<div style="overflow:auto;height:300px">
		<fieldset>
		<legend>Data Terupload</legend>
			<table cellspacing="1" border="0">
				<tbody>														
										
				</tbody>
			</table>
			<table cellspacing="1" border="0">
			<thead>
				<tr class="rowheader">
				<td>No</td>
				<td>NIK</td>
				<td>Nama Karyawan</td>
				<td>Shift</td>
				<td>Kehadiran</td>
				<td>Tanggal</td>
				<td>Jam Msk</td>
				<td>Jam Plg</td>
				<td>Terlambat</td>
				<td>Pulang Cepat</td>
				<td>Lembur</td>
				<td>Lembur Hari Normal</td>
				<td>Lembur Akhir Pekan</td>
				<td>Lembur Hari Libur</td>
				<td>Premi</td>
				<td>Denda kehadiran</td>				
				<td>Uang Lembur</td>				
				</tr>
			</thead>
			<tbody id="contentDetail">';
		$content_body = '';
		
		//ambil nik doang
		$arr_data_nik = array();
		$counter_row_temp = 1;
		$handle1 = fopen($_FILES["fileToUpload"]["tmp_name"], 'r');
		while (($data1 = fgetcsv($handle1, 1000, ",")) !== FALSE) { // Check opening the file is OK!
			if( $counter_row_temp > 1){
				$arr_data_nik[] = $data1[2];
			}
			$counter_row_temp++;
		}
		fclose($handle1);
		
		$arr_list_karyawanid = array();
		$sql = "select * from datakaryawan where nik in ('".implode("','",$arr_data_nik)."')";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_data_karyawanid[$r_sql['nik']] = $r_sql['karyawanid'];
			$arr_data_kodegolongan[$r_sql['nik']] = $r_sql['kodegolongan'];
			$arr_data_bagian[$r_sql['nik']] = $r_sql['bagian']; //HO
			$arr_list_karyawanid[] = $r_sql['karyawanid'];
		}
		
		
		//ambil gaji karyawan
		$sGt = 'select sum(jumlah) as gapTun,karyawanid from '.$dbname.".sdm_5gajipokok where idkomponen in (1,4,3,35,36,37,38,39,40,41,42,43,44,46,47,48,49,50,51) and tahun='".$periode_tahun."' and karyawanid in ('".implode("','",$arr_list_karyawanid)."') GROUP BY karyawanid";
		//echo $sGt;
		$qBasis = mysql_query($sGt);
        while ($rBasis = mysql_fetch_assoc($qBasis)) {
			$arr_gaptun_karyawan[$rBasis['karyawanid']] = $rBasis['gapTun'];
        }
		//echo "'".implode("','",$arr_data_nik)."'";
		//print_r($arr_data_nik);
		
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Check opening the file is OK!
			if( $counter_row > 1){
					$data_tanggal_absen = strtotime($data[5]);
					$new_format_tanggal_absen = date('Ymd',$data_tanggal_absen);
					//echo "Dillah : ".$newformat."<br>";
					if($new_format_tanggal_absen >= $newformat_tanggal_awal && $new_format_tanggal_absen <= $newformat_tanggal_akhir){
						$tanggal = $data[5];
						$timestamp = strtotime($tanggal);

						$hari = date('D', $timestamp);
						
						$data_nik = $data[2];
						$shift = $data[6];
						$jam_msk = $data[9];
						$jam_plg = $data[10];
						$wkt_terlambat = $data[13];
						$wkt_pulcep = $data[14];
						$wkt_lembur = $data[16];
						$lembur_hari_normal = str_replace(",",".",$data[26]);
						$lembur_akhir_pekan = str_replace(",",".",$data[27]);
						$lembur_hari_libur = str_replace(",",".",$data[28]);
						if( $jam_msk != "" && $jam_plg != "" && $wkt_terlambat == "" && $wkt_pulcep == ""){
							$absen_tipe = "H";
						}elseif( $wkt_pulcep != ""){
							$absen_tipe = "PC";
						}else{
							$absen_tipe = "";
						}
						//cocokin dari ref cuti						
						if( isset($arr_ref_cuti_daritgl[$arr_data_karyawanid[$data_nik]] ) ){
							
							for($i=0;$i<count($arr_ref_cuti_daritgl[$arr_data_karyawanid[$data_nik]]);$i++){
								$dari_tgl = (int)str_replace("-", "", $arr_ref_cuti_daritgl[$arr_data_karyawanid[$data_nik]][$i]);
								$sampai_tgl = (int)str_replace("-", "", $arr_ref_cuti_sampaitgl[$arr_data_karyawanid[$data_nik]][$i]);
								if( (int)str_replace("-","",$tanggal) >= $dari_tgl && (int)str_replace("-","",$tanggal) <= $sampai_tgl ){
									$absen_tipe = $arr_ref_cuti_tipeijin[$arr_data_karyawanid[$data_nik]][$i];
								}
							}	
							
						}
						
			
						if(strtolower($hari) == "sun"){
							$absen_tipe = "MG";
						}
						if (in_array($tanggal, $arr_hari_libur)) {
							$absen_tipe = "L";
						}
						//hitung waktu lembur, mendekati 0.5
						$wkt_lembur_hari_normal_aktual = 0;
						if($lembur_hari_normal != ""){
							$pieces_waktu_lembur = explode(".",$lembur_hari_normal);
							$wkt_lembur_hari_normal_aktual = (int)$pieces_waktu_lembur[0];
							if((int)$pieces_waktu_lembur[1] > 50 ){
								$wkt_lembur_hari_normal_aktual = $wkt_lembur_hari_normal_aktual + 0.5;
							}
						}
						$wkt_lembur_akhir_pekan_aktual = 0;
						if($lembur_akhir_pekan != ""){
							$pieces_waktu_lembur = explode(".",$lembur_akhir_pekan);
							$wkt_lembur_akhir_pekan_aktual = (int)$pieces_waktu_lembur[0];
							if((int)$pieces_waktu_lembur[1] > 50 ){
								$wkt_lembur_akhir_pekan_aktual = $wkt_lembur_akhir_pekan_aktual + 0.5;
							}
						}
						$wkt_lembur_hari_libur_aktual = 0;
						if($lembur_hari_libur != ""){
							$pieces_waktu_lembur = explode(".",$lembur_hari_libur);
							$wkt_lembur_hari_libur_aktual = (int)$pieces_waktu_lembur[0];
							if((int)$pieces_waktu_lembur[1] > 50 ){
								$wkt_lembur_hari_libur_aktual = $wkt_lembur_hari_libur_aktual + 0.5;
							}
						}
						
						//perhitungan lembur berdasarkan kemnaker
						$uang_lembur = 0;
						if( isset($arr_gaptun_karyawan[$arr_data_karyawanid[$data_nik]]) ){
							
							$pengali_lembur = 0;
							if( $wkt_lembur_hari_normal_aktual > 0){
								if( isset($arr_data_pengali_lembur[0][$wkt_lembur_hari_normal_aktual]) ){
									$pengali_lembur = $arr_data_pengali_lembur[0][$wkt_lembur_hari_normal_aktual];
								}	
								$uang_lembur = ($arr_gaptun_karyawan[$arr_data_karyawanid[$data_nik]] * $pengali_lembur) / 173;
							}elseif($wkt_lembur_akhir_pekan_aktual > 0){
								if( isset($arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual]) ){
									$pengali_lembur = $arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual];
								}
								$uang_lembur = ($arr_gaptun_karyawan[$arr_data_karyawanid[$data_nik]] * $pengali_lembur) / 173;
							}elseif($wkt_lembur_hari_libur_aktual > 0){
								if( isset($arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual]) ){
									$pengali_lembur = $arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual];
								}
								$uang_lembur = ($arr_gaptun_karyawan[$arr_data_karyawanid[$data_nik]] * $pengali_lembur) / 173;
							}
							
						}
						
						//perhitungan lembur untuk golongan 1-4
						if($arr_data_kodegolongan[$data_nik] ){
							
							//$arr_list_group_golongan = array("3", "4");
							//if (in_array(substr($arr_data_kodegolongan[$data_nik],0,1), $arr_list_group_golongan) && $flag_use_lembur == "Y") {
							if (in_array($arr_data_kodegolongan[$data_nik], $arr_list_group_golongan) && $flag_use_lembur == "Y") {
								if( $wkt_lembur_hari_normal_aktual > 0){
									if(  $wkt_lembur_hari_normal_aktual > 2 &&  $wkt_lembur_hari_normal_aktual < 4 ){
										$uang_lembur = 30000;
									}elseif($wkt_lembur_hari_normal_aktual >= 4){
										$uang_lembur = 50000;
										if( ($wkt_lembur_hari_normal_aktual - 7) > 2 ){
											$uang_lembur = $uang_lembur + 30000;
										}
									}										
								}elseif($wkt_lembur_akhir_pekan_aktual > 0){
									if(  $wkt_lembur_akhir_pekan_aktual > 2 &&  $wkt_lembur_akhir_pekan_aktual < 4 ){
										$uang_lembur = 60000;
									}elseif($wkt_lembur_akhir_pekan_aktual >= 4){
										$uang_lembur = 100000;
										if( ($wkt_lembur_akhir_pekan_aktual - 7) > 2 ){
											$uang_lembur = $uang_lembur + 60000;
										}
									}
								}elseif($wkt_lembur_hari_libur_aktual > 0){
									if(  $wkt_lembur_hari_libur_aktual > 2 &&  $wkt_lembur_hari_libur_aktual < 4 ){
										$uang_lembur = 60000;
									}elseif($wkt_lembur_hari_libur_aktual >= 4){
										$uang_lembur = 100000;
										if( ($wkt_lembur_hari_libur_aktual - 7) > 2 ){
											$uang_lembur = $uang_lembur + 60000;
										}
									}
								}
							}
						}
						
						if($absen_tipe == ""){
							$bg_type_kehadiran = 'bgcolor="#FF0000"';
						}else{
							$bg_type_kehadiran = '';
						}
						
						$content_body .= '<tr class="rowcontent" >
									<td>'.$counter_data.'</td>
									<td><input type="text" id="inputNik'.$counter_data.'" name="inputNik'.$counter_data.'" value="'.$data_nik.'" size="10"><input type="hidden" id="inputKaryawanId'.$counter_data.'" name="inputKaryawanId'.$counter_data.'" value="'.$arr_data_karyawanid[$data_nik].'" size="10"></td>
									<td>'.$data[3].'</td>
									<td><select id="inputShift'.$counter_data.'" name="inputShift'.$counter_data.'"><option value="">Pilih Shift</option>';
									foreach($arr_pil_tipe_shift as $v){
										if( strtolower($shift) == strtolower($v)){
											$content_body .= '<option value="'.$v.'" selected>'.$v.'</option>';
										}else{
											$content_body .= '<option value="'.$v.'">'.$v.'</option>';
										}
										
									}
						$content_body .= '</select></td>
									<td '.$bg_type_kehadiran.'><select id="inputTypeKehadiran'.$counter_data.'" name="inputTypeKehadiran'.$counter_data.'"><option value="">Pilih Tipe </option>';
									foreach($arr_pil_tipe_absen as $kode => $title ){
										if( $absen_tipe == $kode){
											$content_body .= '<option value="'.$kode.'" selected>'.$title.'</option>';
										}else{
											$content_body .= '<option value="'.$kode.'">'.$title.'</option>';
										}
										
									}
									$content_body .= '</select></td>
									<td><input type="text" id="inputTanggal'.$counter_data.'" name="inputTanggal'.$counter_data.'" value="'.$tanggal.'" size="10"></td>
									<td><input type="text" id="inputJamMasuk'.$counter_data.'" name="inputJamMasuk'.$counter_data.'" value="'.$jam_msk.'" size="10"></td>
									<td><input type="text" id="inputJamKeluar'.$counter_data.'" name="inputJamKeluar'.$counter_data.'" value="'.$jam_plg.'" size="10"></td>
									<td>'.$wkt_terlambat.'<input type="hidden" id="inputWaktuTerlambat'.$counter_data.'" name="inputWaktuTerlambat'.$counter_data.'" value="'.$wkt_terlambat.'" ></td>
									<td>'.$wkt_pulcep.'</td>
									<td>'.$wkt_lembur.'</td>
									<td>'.$lembur_hari_normal.'</td>
									<td>'.$lembur_akhir_pekan.'</td>
									<td>'.$lembur_hari_libur.'</td>
									<td align="right"><input type="text" id="inputPremi'.$counter_data.'" name="inputPremi'.$counter_data.'" value="" size="10"></td>
									<td align="right"><input type="text" id="inputDendaKehadiran'.$counter_data.'" name="inputDendaKehadiran'.$counter_data.'" value="" size="10"></td>								
									<td align="right"><input type="hidden" id="inputJamAktualLembur'.$counter_data.'" name="inputJamAktualLembur'.$counter_data.'" value="'.$wkt_lembur_hari_normal_aktual.'" ><input type="text" id="inputUangLembur'.$counter_data.'" name="inputUangLembur'.$counter_data.'" value="'.round($uang_lembur).'" size="10"></td>								
									</tr>';				
						$counter_data++;
					}
					
			}
			
			$counter_row++;
		}
		$content_tail = '<tr>
							<td colspan="11" ><input type="hidden" id="inputTotalData" name="inputTotalData" value="'.($counter_data-1).'" ><button type="submit" class="mybutton" name="btnSubmitData" id="btnSubmitData" onclick="clicked=\'proses\'" >Simpan Data</button></td>
						</tr>
						</tbody>
					</table>
				</fieldset>
			</div>';
		fclose($handle);
	}
	
	if( $content_body != ""){
		$content_table = $content_head.$content_body.$content_tail;
	}
	
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $PATH_FILE_UPLOAD_ABSENSI.'/'.date("YmdHis").$file_ext)) {
		//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	} else {
		//echo "Sorry, there was an error uploading your file.";
		die('Failed to upload in folder '.$PATH_FILE_UPLOAD_ABSENSI);
	}
}
//print_r($_POST);
if( isset($_POST['btnSubmitData']) ){
	
	$total_data = (int)$_POST['inputTotalData'];
	$kdOrg = $_POST['kdOrg'];
	$periode = $_POST['periode'];
	$tanggal_awal = $_POST['inputTanggalAwal'];
	$tanggal_akhir = $_POST['inputTanggalAkhir'];
	// dari tanggal ini diambil yg cuti dan izin
	
	//query untuk mendapatkan hari libur 
	//## select * from sdm_5harilibur where tanggal between '2019-01-01' and '2019-12-01' and regional != ''
	
	//tanyain folder untuk naro file ??
		
	if( $total_data > 0){
		
		$sIns = "insert into ".$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`)\r\n                                       values ('".$kdOrg."','".date("Y-m-d")."','".$periode."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE kodeorg='".$kdOrg."',periode='".$periode."',updateby = '".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
		if (mysql_query($sIns)) {
			

			for($i=1;$i<=$total_data;$i++){
				if ('' == $_POST['inputPremi'.$i]) {
					$inputPremi = 0;
				}else{
					$inputPremi = (real)$_POST['inputPremi'.$i];
				}
				
				$insentif = 0;
				
				$tgl = $_POST['inputTanggal'.$i];
				$nik_karyw = $_POST['inputNik'.$i];
				$submit_karyawanid = $_POST['inputKaryawanId'.$i];
				$submit_waktu_terlambat = $_POST['inputWaktuTerlambat'.$i];
				//if( isset($karyw_id[$nik_karyw]) ){
				if( $submit_karyawanid != "" ){
					$asbensiId = $_POST['inputTypeKehadiran'.$i];
					if( $_POST['inputJamMasuk'.$i] == ''){
						$jam_masuk = "00:00";
					}else{
						$jam_masuk = $_POST['inputJamMasuk'.$i];
					}
					if( $_POST['inputJamKeluar'.$i] == ''){
						$jam_keluar = "00:00";
					}else{
						$jam_keluar = $_POST['inputJamKeluar'.$i];
					}
					$sDetIns = "insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`,`absensi`,`jam`,`jamPlg`, `penjelasan`,`penaltykehadiran`,`premi`,`insentif`)\r\n                                                  values ('".$kdOrg."','".$tgl."','".$submit_karyawanid."','".$shifTid."','".$asbensiId."','".$jam_masuk."','".$jam_keluar."','','".(real)$_POST['inputDendaKehadiran'.$i]."','".(real)$_POST['inputPremi'.$i]."','".$insentif."') ON DUPLICATE KEY UPDATE shift = '".$shifTid."',absensi='".$asbensiId."',jam='".$jam_masuk."',jamPlg='".$jam_keluar."',penaltykehadiran='".(real)$_POST['inputDendaKehadiran'.$i]."',premi='".(real)$_POST['inputPremi'.$i]."',insentif='".$insentif."';";					
					if (mysql_query($sDetIns)) {						
						//hitung terlambat
						if($submit_waktu_terlambat != ""){
							$pieces_waktu_terlambat = explode(":",$submit_waktu_terlambat);
							if( (int)$pieces_waktu_terlambat[1] > 5 ){ //diatas 5menit
								$arr_data_nik_terlambat[] = $submit_karyawanid;
							}
							
						}
					} else {
						echo 'DB Error : '.mysql_error($conn).$sDetIns."<br>";
					}
					
					//masalah lembur
					if( (real)$_POST['inputUangLembur'.$i] > 0){
						$ungLbhjm = (real)$_POST['inputUangLembur'.$i];
						$jam_aktual_lembur = (real)$_POST['inputJamAktualLembur'.$i];
						$sIns = 'insert into '.$dbname.".sdm_lemburht (`kodeorg`,`tanggal`,`updateby`,`updatetime`) \r\n                               values ('".$kdOrg."','".$tgl."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE updateby='".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
						if (mysql_query($sIns)) {
							$sDetIns = 'insert into '.$dbname.".sdm_lemburdt \r\n                                        (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tgl."','".$submit_karyawanid."','0','".$jam_aktual_lembur."','0','0','".$ungLbhjm."') ON DUPLICATE KEY UPDATE uangkelebihanjam='".$ungLbhjm."';";
							if (mysql_query($sDetIns)) {
								echo '';
							} else {
								echo 'DB Error ('.$sDetIns.'): '.mysql_error($conn)."<br>--------<br>";
							}
						} else {
							echo 'DB Error : '.mysql_error($conn)."<br>--------<br>";
						}
					}
					
				}				
			}
			
			if( isset($arr_data_nik_terlambat) ){
				if( count($arr_data_nik_terlambat) > 0 ){
					$sInsHt = "insert into ".$dbname.".sdm_potonganht (`kodeorg`,`periodegaji`,`tipepotongan`,`updateby`) values ('".$kdOrg."','".$periode."','64','".$_SESSION['standard']['userid']."')ON DUPLICATE KEY UPDATE updateby = '".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
					if (mysql_query($sInsHt)) {
						for($i=0;$i<count($arr_data_nik_terlambat);$i++){
							 $sDet = "insert into ".$dbname.".sdm_potongandt (`kodeorg`,`periodegaji`,`keterangan`,`nik`,`jumlahpotongan`,`tipepotongan`,`updateby`) values('".$kdOrg."','".$periode."','auto','".$arr_data_nik_terlambat[$i]."','0','64','".$_SESSION['standard']['userid']."') ON DUPLICATE KEY UPDATE jumlahpotongan='0',keterangan='auto',updateby = '".$_SESSION['standard']['userid']."';";
							 if (mysql_query($sDet)) {
							 }else {
								echo 'DB Error : '.mysql_error($conn).$sDet."<br>";
							}
						}
					}else {
						echo 'DB Error : '.mysql_error($conn).$sInsHt."<br>";
					}
				}
			}
			
		} else {
			echo 'DB Error : '.mysql_error($conn).$sIns;
		}
		
	}	
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['absensi'].' Upload</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=\"javascript\">\r\nnmTmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\nnmTmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\nnmTmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\n\r\n</script>\r\n<script language=\"javascript\" src=\"js/sdm_absensi.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n\r\n\r\n\r\n<div id=\"action_list\">\r\n";
$sGp = 'select DISTINCT periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0";
$qGp = mysql_query($sGp);
while ($rGp = mysql_fetch_assoc($qGp)) {
    $optPeriode .= '<option value='.$rGp['periode'].'>'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
}
$idOrg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."' \r\n           ORDER BY `namaorganisasi` ASC";
if (6 == strlen($_SESSION['empl']['subbagian'])) {
    $sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where (kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."') \r\n           and kodeorganisasi like '".$_SESSION['empl']['subbagian']."%' \r\n           ORDER BY `namaorganisasi` ASC";
}

$query = mysql_query($sql);
while ($res = mysql_fetch_assoc($query)) {
    $optOrg .= '<option value='.$res['kodeorganisasi'].'>'.$res['namaorganisasi'].'</option>';
}
if ('ID' == $_SESSION['language']) {
    $ket = "Form absensi hari libur berfungsi untuk mencatat absensi <br>seluruh karyawan KHT yang masih aktif secara otomatis. <br>\r\n                       Setiap hari libur dan hari minggu  harus dicatatkan melalui form ini. <br>Jika tidak dicatatkan maka akan menjadi potongan HK.";
} else {
    $ket = "Form holiday attendance serves to record the attendance of all employees<br>who are still active KHT automatically. <br>\r\n              Every holiday and day of week should be listed in this form. <br>If not listed then it will be a deduction HK.";
}


echo "</div>\r\n";
CLOSE_BOX();

echo "<div id=\"headher\" style=\"display:none\">\r\n";
OPEN_BOX();
for ($x = 0; $x <= 3; ++$x) {
    $dte = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPrd .= '<option value='.date('m-Y', $dte).'>'.date('m-Y', $dte).'</option>';
}
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['kodeorg'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"kdOrg888\" style=\"width:150px;\" ><option value=\"\">";
echo $_SESSION['lang']['pilihdata'];
echo '</option>';
echo $optOrg;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<input type=\"text\" class=\"myinputtext\" id=\"tglAbsen\" name=\"tglAbsen\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" />\r\n</td>\r\n</tr>\r\n<tr>\r\n<td>";
echo $_SESSION['lang']['periode'];
echo "</td>\r\n<td>:</td>\r\n<td>\r\n<select id=\"periode555\" name=\"periode555\" style=\"width:150px;\" >";
echo $optPeriode;
echo "</select>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" id=\"tmbLheader\">\r\n</td>\r\n</tr>\r\n</table>\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"detailEntry\" style=\"display:none\">\r\n";
OPEN_BOX();
echo "<div id=\"addRow_table\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['detail'];
echo "</legend>\r\n<div id=\"detailIsi\">\r\n</div>\r\n<table>\r\n<tr><td id=\"tombol\">\r\n\r\n</td></tr>\r\n</table>\r\n</fieldset>\r\n</div><br />\r\n<br />\r\n<div style=\"overflow:auto;height:300px\">\r\n<fieldset>\r\n<legend>";
echo $_SESSION['lang']['datatersimpan'];
echo "</legend>\r\n    <table cellspacing=\"1\" border=\"0\">\r\n    <thead>\r\n        <tr class=\"rowheader\">\r\n        <td>No</td>\r\n        <td>";
echo $_SESSION['lang']['nik'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['namakaryawan'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['shift'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['absensi'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['jamMsk'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['jamPlg'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['premi'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['penaltykehadiran'];
echo "</td>\r\n        <td>";
echo $_SESSION['lang']['keterangan'];
echo "</td>\r\n        <td>Action</td>\r\n        </tr>\r\n    </thead>\r\n    <tbody id=\"contentDetail\">\r\n    \r\n    </tbody>\r\n    </table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();
?>

<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="" onsubmit="checkForm();">
<div id="headher" style="display: block;">
	<div id="" style="width:100%;">
		<fieldset id=""><legend><span class="judul">&nbsp;</span></legend>
			<div id="contentBox" style="overflow:auto;">
				<fieldset>
					<legend>Upload Data</legend>
					<table cellspacing="1" border="0">
						<tbody>	
							<tr>
								<td>Kode Organisasi </td>
								<td>:</td>
								<td>
								<select id="kdOrg" name="kdOrg" style="width:150px;" ><option value="">Pilih Data</option>
								<?php
									$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."' \r\n           ORDER BY `namaorganisasi` ASC";
									if (6 == strlen($_SESSION['empl']['subbagian'])) {
										$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where (kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."') \r\n           and kodeorganisasi like '".$_SESSION['empl']['subbagian']."%' \r\n           ORDER BY `namaorganisasi` ASC";
									}

									$query = mysql_query($sql);
									while ($res = mysql_fetch_assoc($query)) {
										if( $kdOrg == $res['kodeorganisasi'] ){
											echo  '<option value="'.$res['kodeorganisasi'].'" selected>'.$res['namaorganisasi'].'</option>';
										}else{
											echo  '<option value="'.$res['kodeorganisasi'].'">'.$res['namaorganisasi'].'</option>';
										}										
									}
								?>
							
							</select>
								</td>
							</tr>							
							<tr>
								<td>Periode</td>
								<td>:</td>
								<td>
								<select id="periode" name="periode" style="width:150px;">';
								<?php
									$sGp = 'select DISTINCT periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and `sudahproses`=0";
									$qGp = mysql_query($sGp);
									while ($rGp = mysql_fetch_assoc($qGp)) {
										if( $periode == $rGp['periode'] ){
											echo  '<option value="'.$rGp['periode'].'" selected>'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
										}else{
											echo  '<option value="'.$rGp['periode'].'">'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
										}
										
										
									}
								?>					
								</select>
								</td>
							</tr>
							<tr>
								<td>Tanggal</td>
								<td>:</td>
								<td> <input type="text" id="inputTanggalAwal" name="inputTanggalAwal" onmousemove="setCalendar(this.id);" onkeypress="return false;" maxlength="10" value="<?php echo $tanggal_awal; ?>"> - <input type="text" id="inputTanggalAkhir" name="inputTanggalAkhir" onmousemove="setCalendar(this.id);" onkeypress="return false;" maxlength="10" value="<?php echo $tanggal_akhir; ?>">
								</td>
							</tr>
							<tr>
								<td>Upload File</td>
								<td>:</td>
								<td><input type="file" name="fileToUpload" id="fileToUpload"><button type="submit" class="mybutton" name="btnSubmit" id="dtlAbn" onclick="clicked='open'"  >Open File</button></td>
							</tr>
						</tbody>
					</table>
				</fieldset>

			</div>
		</fieldset>
	</div>
</div>
<div id="detail">
	<?php
		echo $content_table;
	?>
</div>
</form>
<script language="javascript" >
var clicked;
function checkForm(){
	if(clicked == "proses"){
		if( document.getElementById('kdOrg').value == ""){
			alert("Harap Memilih Kode Organisasi ");
			event.preventDefault();
			return false;
		}
		if( document.getElementById('periode').value == ""){
			alert("Harap Memilih Periode ");
			event.preventDefault();
			return false;
		}
		if( document.getElementById('inputTanggalAwal').value == ""){
			alert("Harap Memilih Tanggal Awal ");
			event.preventDefault();
			return false;
		}
		if( document.getElementById('inputTanggalAkhir').value == ""){
			alert("Harap Memilih Tanggal Akhir ");
			event.preventDefault();
			return false;
		}
		konfirmasi = confirm("Anda Yakin ??");
		if( konfirmasi){
			//alert("diatas "+clicked);
			//return false;
		}else{
			//alert("dibawah : "+clicked);
			event.preventDefault();
			return false;
		}
	}
}


</script>
