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
		$x= explode('.',basename($_FILES['fileToUpload']['name']));
		$t = count($x)-1;

		#CEK TIPE FILE
		if(($x[$t]=="xls"))
		{	

			$nama_file=date("Ymd_His_").basename($_FILES['fileToUpload']['name']);
			$target_path = "upload_file/absensi/".$nama_file;
			copy($_FILES['fileToUpload']['tmp_name'],$target_path);
			chmod($target_path,0755);

			require_once "lib/excelReader/excel_reader2.php";

			// membaca file excel yang diupload
			ini_set('memory_limit', '-1');
			$data = new Spreadsheet_Excel_Reader($target_path);
			// membaca jumlah baris dari data excel
			$baris = $data->rowcount($sheet_index=0);
			$flag=true;
			$date=date('Y-m-d H:i:s');
																			 		
			$z=1;
			$nama_file=addslashes($nama_file);
			$kdOrg = $_POST['kdOrg'];
			$periode = $_POST['periode'];
			$pieces = explode("-",$periode);
			$periode_tahun = $pieces[0];
			$date_awal=date_create($_POST['inputTanggalAwal']);
			$date_akhir=date_create($_POST['inputTanggalAkhir']);
			$tanggal_awal = date_format($date_awal,"Y-m-d");
			$tanggal_akhir = date_format($date_akhir,"Y-m-d");
			
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
			$sql = "select karyawanid,tipeijin,darijam,cast(darijam as date) as daritanggal,sampaijam,cast(sampaijam as date) as sampaitanggal from sdm_ijin where cast(darijam as date) >= '".$tanggal_awal."' and cast(sampaijam as date) <= '".$tanggal_akhir."' and stpersetujuanhrd > 0";
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
			$sql = "select * from sdm_5harilibur where tanggal between '".$tanggal_awal."' and '".$tanggal_akhir."' and regional = '".$_SESSION['empl']['regional']."'";
			$q_sql = mysql_query($sql);
			while ($r_sql = mysql_fetch_assoc($q_sql)) {
				$arr_hari_libur[] = $r_sql['tanggal'];
				
			}
			
			$qry="insert into ".$dbname.".tmp_absensiht values('".$nama_file."','".$kdOrg."','".$periode."','".$tanggal_awal."','".$tanggal_akhir."','".$date."'); ";
			$master=mysql_query($qry);
			
			$arr_data_nik = array();
					
			for ($i=2; $i<=$baris; $i++)
			{ 
				
				$emp_no=$data->val($i, 1);				
				$no_id=$data->val($i, 2);
				$nik=$data->val($i, 3);
				$arr_data_nik[] = $nik;
				$nama=addslashes($data->val($i, 4));
				$auto_assign=$data->val($i, 5);
				$tanggal=$data->val($i, 6);
				$jam_kerja=$data->val($i, 7);
				$jam_masuk=$data->val($i, 8);
				$jam_pulang=$data->val($i, 9);
				$scan_masuk=$data->val($i, 10);
				$scan_pulang=$data->val($i, 11);
				$normal=$data->val($i, 12);
				$riil=$data->val($i, 13);
				$terlambat=$data->val($i, 14);
				$plg_cepat=$data->val($i,15);
				$absent=$data->val($i,16);
				$lembur=$data->val($i,17);
				$jml_jam_kerja=$data->val($i,18);
				$pengecualian=$data->val($i,19);
				$harus_in=$data->val($i,20);
				$harus_out=$data->val($i,21);
				$departemen=addslashes($data->val($i,22));
				$hari_normal=$data->val($i,23);
				$akhir_pekan=$data->val($i,24);
				$hari_libur=$data->val($i,25);
				$jml_kehadiran=$data->val($i,26);
				$lembur_hari_normal=$data->val($i,27);
				$lembur_akhir_pekan=$data->val($i,28);
				$lembur_hari_libur=$data->val($i,29);
				$updatetime=$date;
				//2019-01-01
				$qry1="insert into ".$dbname.".tmp_absensidt values('".$nama_file."','".$emp_no."','".$no_id."','".$nik."','".$nama."','".$auto_assign."','".$tanggal."','".$jam_kerja."','".$jam_masuk."','".$jam_pulang."','".$scan_masuk."','".$scan_pulang."','".$normal."','".$riil."','".$terlambat."','".$plg_cepat."','".$absent."','".$lembur."','".$jml_jam_kerja."','".$pengecualian."','".$harus_in."','".$harus_out."','".$departemen."','".$hari_normal."','".$akhir_pekan."','".$hari_libur."','".$jml_kehadiran."','".$lembur_hari_normal."','".$lembur_akhir_pekan."','".$lembur_hari_libur."',current_timestamp,'0','0'); ";				
				if( checkdate(substr($tanggal , 5,2), substr($tanggal , 8,2), substr($tanggal , 0,4)) ){					
//					echo "warning Ok: ".$qry1;
//					exit();
					$master1=mysql_query($qry1);
				}else{
//					echo "warning else: ".$qry1;
//					exit();
				}
				
		
			}
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
					<!-- td>Lembur</td -->
					<td>Lembur Hari Normal</td>
					<td>Lembur Akhir Pekan</td>
					<td>Lembur Hari Libur</td>
					<!-- td>Premi</td -->
					<td>Denda kehadiran</td>
					<td>Uang Lembur</td>
					</tr>
				</thead>
				<tbody id="contentDetail">';
			$content_body = '';
			
			//
			$arr_list_karyawanid = array();
			$sql = "select * from datakaryawan where nik in ('".implode("','",$arr_data_nik)."')";
			$q_sql = mysql_query($sql);
			while ($r_sql = mysql_fetch_assoc($q_sql)) {
				$arr_data_karyawanid[$r_sql['nik']] = $r_sql['karyawanid'];
				$arr_data_kodegolongan[$r_sql['nik']] = $r_sql['kodegolongan'];
				$arr_data_bagian[$r_sql['nik']] = $r_sql['bagian']; //HO
				$arr_list_karyawanid[] = $r_sql['karyawanid'];
		}
		
		//ambil gaji karyawan (gapok dan tunjangan)
//		$sGt = 'select sum(jumlah) as gapTun,karyawanid from '.$dbname.".sdm_5gajipokok where idkomponen in (1,2,4,15,23,29,30,32,33,54,58,61) and tahun='".$periode_tahun."' and karyawanid in ('".implode("','",$arr_list_karyawanid)."') GROUP BY karyawanid";		
		$sGt = 'select sum(jumlah) as gapTun,karyawanid from '.$dbname.".sdm_5gajipokok where idkomponen = 1 and tahun='".$periode_tahun."' and karyawanid in ('".implode("','",$arr_list_karyawanid)."') GROUP BY karyawanid"; // dari gapok saja
		$qBasis = mysql_query($sGt);
		while ($rBasis = mysql_fetch_assoc($qBasis)) {
			$arr_gaptun_karyawan[$rBasis['karyawanid']] = $rBasis['gapTun'];
		}

		//ambil faktor pengali lembur
		$sql = "select kodeorg,tipelembur,jamaktual,jamlembur from sdm_5lembur  where kodeorg='".substr($kdOrg,0,4)."' order by jamaktual,tipelembur";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_data_pengali_lembur[$r_sql['tipelembur']][$r_sql['jamaktual']] = 	$r_sql['jamlembur'];			
		}

		//ambil data kodegolongan yang lembur disnaker (bukan tetap)
		$arr_list_group_golongan = array();
		$sql = "select * from sdm_5golongan where alias like '%lembur%'";
		$q_sql = mysql_query($sql);
		while ($r_sql = mysql_fetch_assoc($q_sql)) {
			$arr_list_group_golongan[] = $r_sql['kodegolongan'];
		}

//
		$totx= 0;
		$squery1="select count(x.karyawanid) as totx from (SELECT a.*,b.karyawanid, b.namakaryawan FROM tmp_absensidt a inner join datakaryawan b on a.nik=b.nik where nama_file='".$nama_file."') x";
		$hasil1 = mysql_query($squery1);
		$res1 = mysql_fetch_array($hasil1); 
		$totx= $res1['totx'];
//
		$squery="SELECT a.*,b.karyawanid, b.namakaryawan FROM tmp_absensidt a inner join datakaryawan b on a.nik=b.nik where nama_file='".$nama_file."' ";
		$hasil = mysql_query($squery);
		while ($res = mysql_fetch_array($hasil)) { 
			$karyawanid = $res['karyawanid'];			
			$data_nik = $res['nik'];
			$tanggal = $res['tanggal'];
			$namakaryawan = $res['namakaryawan'];
			$shift = $res['jam_kerja'];
			$jam_msk = $res['scan_masuk'];
			$jam_plg = $res['scan_pulang'];;
			$wkt_terlambat = $res['terlambat'];
			$wkt_pulcep = $res['plg_cepat'];
			$wkt_lembur = $res['lembur'];
			$lembur_hari_normal = $res['lembur_hari_normal'];
			$lembur_akhir_pekan = $res['lembur_akhir_pekan'];
			$lembur_hari_libur = $res['lembur_hari_libur'];
			$uang_lembur = $res['uang_lembur'];

			if( $jam_msk != "00:00:00" && $jam_plg != "00:00:00" && $wkt_terlambat == "0" && $wkt_pulcep == "0"){
				$absen_tipe = "Hadir";
			}elseif( $wkt_pulcep != "0"){
				$absen_tipe = "Plg. Cepat";
			}else{
				$absen_tipe = "";
			}

			$hari = date('D', $timestamp);
			if(strtolower($hari) == "sun"){
				$absen_tipe = "MINGGU";
			}
			

			if($absen_tipe == ""){
				$bg_type_kehadiran = 'bgcolor="#FF0000"';
			}else{
				$bg_type_kehadiran = '';
			}

			//hitung waktu lembur, mendekati 0.5
			$wkt_lembur_hari_normal_aktual = 0;
			if($lembur_hari_normal != "0"){
				//echo "proses disini <br>";
				$pieces_waktu_lembur = explode(".",$lembur_hari_normal);
				$wkt_lembur_hari_normal_aktual = (int)$pieces_waktu_lembur[0];
				if((int)$pieces_waktu_lembur[1] > 50 ){
					$wkt_lembur_hari_normal_aktual = $wkt_lembur_hari_normal_aktual + 0.5;
				}
			}
			
			$wkt_lembur_akhir_pekan_aktual = 0;
			if($lembur_akhir_pekan != "0"){
				$pieces_waktu_lembur = explode(".",$lembur_akhir_pekan);
				$wkt_lembur_akhir_pekan_aktual = (int)$pieces_waktu_lembur[0];
				if((int)$pieces_waktu_lembur[1] > 50 ){
					$wkt_lembur_akhir_pekan_aktual = $wkt_lembur_akhir_pekan_aktual + 0.5;
				}
			}

			$wkt_lembur_hari_libur_aktual = 0;
			if($lembur_hari_libur != "0"){
				$pieces_waktu_lembur = explode(".",$lembur_hari_libur);
				$wkt_lembur_hari_libur_aktual = (int)$pieces_waktu_lembur[0];
				if((int)$pieces_waktu_lembur[1] > 50 ){
					$wkt_lembur_hari_libur_aktual = $wkt_lembur_hari_libur_aktual + 0.5;
				}
			}
			
			//perhitungan lembur berdasarkan kemnaker
			$uang_lembur = 0;
			if( isset($arr_gaptun_karyawan[$karyawanid]) ){				
				$pengali_lembur = 0;
				if( $wkt_lembur_hari_normal_aktual > 0){
					if( isset($arr_data_pengali_lembur[0][$wkt_lembur_hari_normal_aktual]) ){
						$pengali_lembur = $arr_data_pengali_lembur[0][$wkt_lembur_hari_normal_aktual];
					}	
					$uang_lembur = ($arr_gaptun_karyawan[$karyawanid] * $pengali_lembur) / 173;
				}elseif($wkt_lembur_akhir_pekan_aktual > 0){
					if( isset($arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual]) ){
						$pengali_lembur = $arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual];
					}
					$uang_lembur = ($arr_gaptun_karyawan[$karyawanid] * $pengali_lembur) / 173;
				}elseif($wkt_lembur_hari_libur_aktual > 0){
					if( isset($arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual]) ){
						$pengali_lembur = $arr_data_pengali_lembur[1][$wkt_lembur_hari_normal_aktual];
					}
					$uang_lembur = ($arr_gaptun_karyawan[$karyawanid] * $pengali_lembur) / 173;
				}
							
			}

			//ambil data kodegolongan yang lembur tetap
			$arr_list_group_golongan = array();
			$sql = "select * from sdm_5golongan where alias = '%lemburtap%'";
			$q_sql = mysql_query($sql);
			while ($r_sql = mysql_fetch_assoc($q_sql)) {
				$arr_list_group_golongan[] = $r_sql['kodegolongan'];
			}
						
			if($arr_data_kodegolongan[$data_nik] ){
//				$arr_list_group_golongan = array("3", "4");
//				if (in_array(substr($arr_data_kodegolongan[$data_nik],0,1), $arr_list_group_golongan) && $flag_use_lembur == "Y") {
				
				// Hardcoded nilai uang lembur untuk golongan 3 dan 4, WPG
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
			//kalo ada nilai lembur, langsung update table temp nya
			if( $uang_lembur > 0){
				$sql_update = "UPDATE tmp_absensidt SET uang_lembur='".round($uang_lembur)."', jamaktuallembur='".$wkt_lembur_hari_normal_aktual."' WHERE nama_file='".$nama_file."' AND nik='".$data_nik."'; ";
				//echo $sql_update;
				mysql_query($sql_update);
			}

			$content_body .= '<tr class="rowcontent" >
				<td>'.$counter_data.'</td>
				<td>'.$data_nik.'</td>
				<td>'.$namakaryawan.'</td>
				<td>'.$shift.'</td>
				<td '.$bg_type_kehadiran.'>'.$absen_tipe.'</td>
				<td>'.$tanggal.'</td>
				<td>'.$jam_msk.'</td>
				<td>'.$jam_plg.'</td>
				<td>'.$wkt_terlambat.'</td>
				<td>'.$wkt_pulcep.'</td>
				<!--  td>'.$wkt_lembur.'</td -->
				<td>'.$lembur_hari_normal.'</td>
				<td>'.$lembur_akhir_pekan.'</td>
				<td>'.$lembur_hari_libur.'</td>
				<!-- td>&nbsp</td -->
				<td>&nbsp</td>
				<td align="right">'.number_format(round($uang_lembur),0,"",",").'</td>
				</tr>';	
		
			$counter_data++;
		}
		
		$content_tail = '<tr>
							<td colspan="11" >
								<input name="namafile" hidden value="'.$nama_file.'" >
								<input id="inputTotalData" name="inputTotalData" readonly value="'.($counter_data-1).'" >
								<button type="submit" class="mybutton" name="btnSubmitData" id="btnSubmitData" onclick="clicked=\'proses\'" value="submitdata">Simpan Data</button>
							</td>
						</tr>
						</tbody>
					</table>
				</fieldset>
			</div>';

		if( $content_body != ""){
			$content_table = $content_head.$content_body.$content_tail;
		}	

		
			}else{
				?>
					<script>
						alert('Format File Upload Salah.');
					</script>
				<?php
			}

}

if( $_POST['btnSubmitData']!=""){
	
	$total_data = (int)$_POST['inputTotalData'];
	$kdOrg = $_POST['kdOrg'];
	$periode = $_POST['periode'];
	$tanggal_awal = $_POST['inputTanggalAwal'];
	$tanggal_akhir = $_POST['inputTanggalAkhir'];
	$namafile = $_POST['namafile'];

	$flag=true;
		
	if( $total_data > 0){

		$qry1="SELECT DISTINCT(tanggal) FROM tmp_absensidt a inner join datakaryawan b on a.nik=b.nik where nama_file='".$namafile."' ";
		$hasil1 = mysql_query($qry1);
		while ($res1 = mysql_fetch_array($hasil1)) { 
		
		$tanggal = $res1['tanggal'];

		$sIns = "insert into ".$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`,`updateby`,`updatetime`) values ('".$kdOrg."','".$tanggal."','".$periode."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE updateby = '".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
		$qu1 = mysql_query($sIns);

		}

		$squery="SELECT a.*,b.karyawanid, b.namakaryawan FROM tmp_absensidt a inner join datakaryawan b on a.nik=b.nik where nama_file='".$namafile."' ";
		$hasil = mysql_query($squery);
		while ($res = mysql_fetch_array($hasil)) { 
			$karyawanid = $res['karyawanid'];			
			$data_nik = $res['nik'];
			$namakaryawan = $res['namakaryawan'];
			$tanggal = $res['tanggal'];
			$shift = $res['jam_kerja'];
			$jam_msk = $res['scan_masuk'];
			$jam_plg = $res['scan_pulang'];;
			$wkt_terlambat = $res['terlambat'];
			$wkt_pulcep = $res['plg_cepat'];
			$wkt_lembur = $res['lembur'];
			$lembur_hari_normal = $res['lembur_hari_normal'];
			$lembur_akhir_pekan = $res['lembur_akhir_pekan'];
			$lembur_hari_libur = $res['lembur_hari_libur'];
			$uang_lembur = $res['uang_lembur'];
			$jamaktual_lembur = $res['jamaktuallembur'];

			if( $jam_msk != "00:00:00" && $jam_plg != "00:00:00" && $wkt_terlambat == "0" && $wkt_pulcep == "0"){
				$absen_tipe = "H";
			}elseif( $wkt_pulcep != "0"){
				$absen_tipe = "PC";
			}else{
				$absen_tipe = "";
			}

			$hari = date('D', $timestamp);
			if(strtolower($hari) == "sun"){
				$absen_tipe = "MG";
			}

			$sDetIns = "insert into ".$dbname.".sdm_absensidt (kodeorg,tanggal, karyawanid, shift, absensi ,jam , jamPlg , penjelasan, penaltykehadiran, premi, insentif) values ('".$kdOrg."','".$tanggal."','".$karyawanid."','".$shift."','".$absen_tipe."','".$jam_msk."','".$jam_plg."','','','','') ON DUPLICATE KEY UPDATE shift = '".$shift."',absensi='".$absen_tipe."',jam='".$jam_msk."',jamPlg='".$jam_plg."',insentif='".$insentif."';";
			$qu2 = mysql_query($sDetIns);
			if($qu2 == true){
				//masalah lembur			
				if( (real)$uang_lembur > 0){
					$ungLbhjm = (real)$uang_lembur;
					$jam_aktual_lembur = (real)$jamaktual_lembur;
					$sIns = 'insert into '.$dbname.".sdm_lemburht (`kodeorg`,`tanggal`,`updateby`,`updatetime`) \r\n                               values ('".$kdOrg."','".$tanggal."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."') ON DUPLICATE KEY UPDATE updateby='".$_SESSION['standard']['userid']."',updatetime='".date('Y-m-d H:i:s')."';";
					if (mysql_query($sIns)) {
						$sDetIns = 'insert into '.$dbname.".sdm_lemburdt \r\n                                        (`kodeorg`,`tanggal`,`karyawanid`,`tipelembur`,`jamaktual`,`uangmakan`,`uangtransport`,`uangkelebihanjam`) values ('".$kdOrg."','".$tanggal."','".$karyawanid."','0','".$jam_aktual_lembur."','0','0','".$ungLbhjm."') ON DUPLICATE KEY UPDATE uangkelebihanjam='".$ungLbhjm."';";
						if (mysql_query($sDetIns)) {
							echo '';
							//echo $sDetIns;
						} else {
							echo 'DB Error ('.$sDetIns.'): '.mysql_error($conn)."<br>--------<br>";
						}
					} else {
						echo 'DB Error : '.mysql_error($conn)."<br>--------<br>";
					}
				}
			}
			
		}
		if(!$qu2) $flag=false;	
		if($flag==true){			
		?>
				<script>
					alert(" SIMPAN DATA SUKSES .");
				</script>
		<?php
			}else{

		?>
				<script>
					alert("GAGAL SIMPAN DATA .");
				</script>
		<?php
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
								<td><input type="file" name="fileToUpload" id="fileToUpload"><button type="submit" class="mybutton" name="btnSubmit" id="dtlAbn" onclick="clicked='proses'"  >Upload File</button></td>
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
			//form1.submit;
		}else{
			//alert("dibawah : "+clicked);
			event.preventDefault();
			return false;
		}
	}
}


</script>
