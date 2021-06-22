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

// 01. Ketika file di unggah
if( isset($_POST['btnSubmit']) ){
	$x= explode('.',basename($_FILES['fileToUpload']['name']));
	$t = count($x)-1;
	
	// 02. Mengecek File Excel / Bukan
	#CEK TIPE FILE
	
	if(($x[$t]=="xls"))
	{	
		$nama_file=date("Ymd_His_").basename($_FILES['fileToUpload']['name']);
		$target_path = "upload_file/logsheet/".$nama_file;
		copy($_FILES['fileToUpload']['tmp_name'],$target_path);
		chmod($target_path,0755);

		require_once "lib/excelReader/excel_reader2.php";
		// 03. Membaca data xls
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
		// $periode = $_POST['periode'];
		// $pieces = explode("-",$periode);
		// $periode_tahun = $pieces[0];
		// $date_awal=date_create($_POST['inputTanggalAwal']);
		// $date_akhir=date_create($_POST['inputTanggalAkhir']);
		// $tanggal_awal = date_format($date_awal,"Y-m-d");
		// $tanggal_akhir = date_format($date_akhir,"Y-m-d");
		
		// // 04 get data sdm_5lembur
		// /*
		// 	Mengambil data kodeorg, tipelembur, jamaktual, jamlembur
		// 	dari table sdm_5lembur
		// 	kondisi kodeorg
		// */
		// //ambil faktor pengali lembur
		// $flag_use_lembur = "N";
		// $sql = "select kodeorg,tipelembur,jamaktual,jamlembur from sdm_5lembur  where kodeorg='".substr($kdOrg,0,4)."' order by jamaktual,tipelembur";
		
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// data dimemasukan ke var array dengan key ['tipelembur']['jamlembur]
		// 	$arr_data_pengali_lembur[$r_sql['tipelembur']][$r_sql['jamaktual']] = 	$r_sql['jamlembur'];
		// 	$flag_use_lembur = "Y";			
		// }	
		
		// // 05 get data sdm_ijin
		// /*
		// 	Mengambil data karyawanid, tipeijin, darijam, sampaijam, sampaitanggal
		// 	dari table sdm_ijin
		// 	kondisi stpersetujuanhrd > 0
		// */
		// //ambil referensi cuti
		// $sql = "select karyawanid,tipeijin,darijam,cast(darijam as date) as daritanggal,sampaijam,cast(sampaijam as date) as sampaitanggal from sdm_ijin where cast(darijam as date) >= '".$tanggal_awal."' and cast(sampaijam as date) <= '".$tanggal_akhir."' and stpersetujuanhrd > 0";	
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// data dimemasukan ke var array dengan key ['karyawanid']
		// 	$arr_ref_cuti_daritgl[$r_sql['karyawanid']][] = $r_sql['daritanggal'];
		// 	$arr_ref_cuti_sampaitgl[$r_sql['karyawanid']][] = $r_sql['sampaitanggal'];
		// 	$arr_ref_cuti_tipeijin[$r_sql['karyawanid']][] = $r_sql['tipeijin'];
		// }
		
		// // 06 get data sdm_5absensi
		// /*
		// 	Mengambil data all
		// 	dari table sdm_5absensi
		// */
		// //ambil pilihan absenid
		// $arr_pil_tipe_absen = array();
		// $sql = "select * from sdm_5absensi order by keterangan";
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// data dimemasukan ke var array dengan key kodeabsen
		// 	$arr_pil_tipe_absen[$r_sql['kodeabsen']] = $r_sql['keterangan'];
		// }

		// //pilihan shift
		// $arr_pil_tipe_shift = array("NS SENJUM","NS SABTU","SHIFT 1","SHIFT 2","SHIFT 3");
		
		// // 07 get data sdm_5harilibur
		// /*
		// 	Mengambil data all
		// 	dari table sdm_5harilibur
		// 	kondisi range tanggal dan regional
		// */
		// //ambil data hari libur selain hari minggu
		// $arr_hari_libur = array();
		// $sql = "select * from sdm_5harilibur where tanggal between '".$tanggal_awal."' and '".$tanggal_akhir."' and regional = '".$_SESSION['empl']['regional']."'";
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// data dimemasukan ke var array
		// 	$arr_hari_libur[] = $r_sql['tanggal'];
		// }
		
		// 08 menambahkan data ke table tmp_absensiht
		$qry="insert into ".$dbname.".tmp_boilerht values('".$nama_file."','".$kdOrg."','".$date."'); ";
		$master=mysql_query($qry);
		
		// 09 proses membuat array nik dan memasukan data ke tmp_absensidt
		$arr_data_tgl = array();
		$isFileTrue = true;
		$content_notife = '';
		for ($i=2; $i<=$baris; $i++)
		{ 
			// jika nik kosong break
			if(empty($data->val($i, 1))){
				continue;
			}
			
			$tgl=$data->val($i, 1);	
			$jam=$data->val($i, 2);				
			$idf_a=preg_replace("/[^0-9.]/", "", $data->val($i, 3));
			$fdf_a=preg_replace("/[^0-9.]/", "", $data->val($i, 4));			
			$scdf_a=preg_replace("/[^0-9.]/", "", $data->val($i, 5));
			$fff_a=preg_replace("/[^0-9.]/", "", $data->val($i, 6));
			$fp1_a=preg_replace("/[^0-9.]/", "", $data->val($i, 7));
			$fp2_a=preg_replace("/[^0-9.]/", "", $data->val($i, 8));
			$feed_tank=preg_replace("/[^0-9.]/", "", $data->val($i, 9));
			$dearator=preg_replace("/[^0-9.]/", "", $data->val($i, 10));
			$gas_duct=preg_replace("/[^0-9.]/", "", $data->val($i, 11));
			$sh=preg_replace("/[^0-9.]/", "", $data->val($i, 12));
			$main_steam=preg_replace("/[^0-9.]/", "", $data->val($i, 13));
			$fp1=preg_replace("/[^0-9.]/", "", $data->val($i, 14));
			$fp2=preg_replace("/[^0-9.]/", "", $data->val($i, 15));
			$flow_meter=preg_replace("/[^0-9.]/", "", $data->val($i, 16));
			$level_airfeed=$data->val($i, 17);
			$water_level=preg_replace("/[^0-9.]/", "", $data->val($i, 18));
			$tds=preg_replace("/[^0-9.]/", "", $data->val($i, 19));
			$jam_sb= $data->val($i, 20);
			$keterangan=$data->val($i, 21);
			
			$updatetime=$date;
			// cek jik ada nik sama
			if(in_array($tgl, $arr_data_tgl)){
				// $content_notife = '<h3> Dalam File Excel terdapat NIK yang sama! </h3>';
			}
			$arr_data_tgl[] = $tgl;

			// Hitung waktu lembur, mendekati 0.5
			// $wkt_lembur_hari_normal_aktual = 0;
			// if(!empty($lembur_hari_normal)){
			// 	$pieces_waktu_lembur = explode(":",$lembur_hari_normal);
			// 	$wkt_lembur_hari_normal_aktual = (int)$pieces_waktu_lembur[0];
			// 	if((int)$pieces_waktu_lembur[1] >= 30 ){
			// 		$wkt_lembur_hari_normal_aktual = $wkt_lembur_hari_normal_aktual + 0.5;
			// 	}
			// }
			
			// $wkt_lembur_akhir_pekan_aktual = 0;
			// if(!empty($lembur_akhir_pekan)){
			// 	$pieces_waktu_lembur = explode(":",$lembur_akhir_pekan);
			// 	$wkt_lembur_akhir_pekan_aktual = (int)$pieces_waktu_lembur[0];
			// 	if((int)$pieces_waktu_lembur[1] >= 30 ){
			// 		$wkt_lembur_akhir_pekan_aktual = $wkt_lembur_akhir_pekan_aktual + 0.5;
			// 	}
			// }

			// $wkt_lembur_hari_libur_aktual = 0;
			// if(!empty($lembur_hari_libur)){
			// 	$pieces_waktu_lembur = explode(":",$lembur_hari_libur);
			// 	$wkt_lembur_hari_libur_aktual = (int)$pieces_waktu_lembur[0];
			// 	if((int)$pieces_waktu_lembur[1] >= 30 ){
			// 		$wkt_lembur_hari_libur_aktual = $wkt_lembur_hari_libur_aktual + 0.5;
			// 	}
			// }

			//2019-01-01
			// menyimpan data xls ke db tmp_absensidt
			 $qry1="insert into ".$dbname.".tmp_boilerdt values('".$nama_file."','".$tgl."','".$jam."','".$idf_a."','".$fdf_a."','".$scdf_a."','".$fff_a."','".$fp1_a."','".$fp2_a."','".$feed_tank."','".$dearator."','".$gas_duct."','".$sh."','".$main_steam."','".$fp1."','".$fp2."','".$flow_meter."','".$level_airfeed."','".$water_level."','".$tds."','".$jam_sb."','".$keterangan."',current_timestamp); ";				
			
								
				//  echo "warning Ok: ".$qry1;
				// exit();
				$master1=mysql_query($qry1);
			
		}
		$counter_data = 1;
		
		$content_head = '';
		$content_head .= '<div style="overflow:auto;height:300px">
		<fieldset>
		<legend>Data Terupload</legend>
			<table cellspacing="1" border="0">
				<tbody>														
										
				</tbody>
			</table>
			<table cellspacing="1" border="0">
			<thead>
				<tr class="rowheader">
				<td>Tanggal</td>
				<td>JAM</td>
				<td>IDF(A)</td>
				<td>FDF(A)</td>
				<td>SCDF(A)</td>
				<td>FFF(A) </td>
				<td>FP1(A)</td>
				<td>FP2(A)</td>
				<td>FEED TANK(C)</td>
				<td>DEARATOR(C)</td>
				<td>OUTLERT GAS DUCT(C)</td>
				<td>SH(C)</td>
				<td>MAIN STEAM(Kg.Cm2)</td>
				<td>FP1(Kg.Cm2)</td>
				<td>FP2(Kg.Cm2)</td>
				<td>FLOW METER FEED WATER</td>
				<td>LEVEL AIR FEED WATER</td>
				<td>WATER LEVEL(%)</td>
				<td>TDS(PPM)</td>
				<td>JAM SB</td>
				<td>KETERANGAN</td>
				</tr>
			</thead>
			<tbody id="contentDetail">';
		$content_body = '';
		
		// 10 get data datakaryawan
		/*
			Mengambil data all
			dari table datakaryawan
			kondisi nik yang sama dengan array nik di proses 9
		*/
		
		// $arr_list_karyawanid = array();
		// $sqlx = "select * from datakaryawan where nik in ('".implode("','",$arr_data_nik)."')";
		// $q_sql = mysql_query($sqlx);

		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// memasukan data semua ke array arr_data_karyawanid, arr_data_karyawanid, arr_data_bagian, arr_list_karyawanid
		// 	$arr_data_karyawanid[$r_sql['nik']] = $r_sql['karyawanid'];
		// 	$arr_data_kodegolongan[$r_sql['nik']] = $r_sql['kodegolongan'];
		// 	$arr_data_bagian[$r_sql['nik']] = $r_sql['bagian']; //HO
		// 	$arr_list_karyawanid[] = $r_sql['karyawanid'];
		// }
		
		// // 11. get data sdm_5gajipokok
		// /*
		// 	Mengambil data gapTun, karyawanid
		// 	dari table sdm_5gajipokok
		// 	kondisi idkomponen 1 tahun berdasarakn periode, karyawanid yang di array
		// */
		// // ambil gaji karyawan (gapok dan tunjangan)
		// // $sGt = 'select sum(jumlah) as gapTun,karyawanid from '.$dbname.".sdm_5gajipokok where idkomponen in (1,2,4,15,23,29,30,32,33,54,58,61) and tahun='".$periode_tahun."' and karyawanid in ('".implode("','",$arr_list_karyawanid)."') GROUP BY karyawanid";		
		// $sGt = 'select sum(jumlah) as gapTun,karyawanid from '.$dbname.".sdm_5gajipokok where idkomponen = 1 and tahun='".$periode_tahun."' and karyawanid in ('".implode("','",$arr_list_karyawanid)."') GROUP BY karyawanid"; // dari gapok saja
		// $qBasis = mysql_query($sGt);
		// while ($rBasis = mysql_fetch_assoc($qBasis)) {
		// 	// memasukan ke var array arr_gaptun_karyawan dengan key karyawanid
		// 	$arr_gaptun_karyawan[$rBasis['karyawanid']] = $rBasis['gapTun'];
		// }

		// // 12. get data sdm_5lembur
		
		// 	Mengambil data kodeorg,tipelembur,jamaktual,jamlembur
		// 	dari table sdm_5lembur
		// 	kondisi kodeorg
		
		// //ambil faktor pengali lembur
		// $sql = "select kodeorg,tipelembur,jamaktual,jamlembur from sdm_5lembur  where kodeorg='".substr($kdOrg,0,4)."' order by jamaktual,tipelembur";
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// memasukan data ke var array
		// 	$arr_data_pengali_lembur[$r_sql['tipelembur']][$r_sql['jamaktual']] = 	$r_sql['jamlembur'];			
		// }
		
		// // 13. get data sdm_5golongan
		// /*
		// 	Mengambil data all
		// 	dari table sdm_5golongan
		// 	kondisi alias nya ada kata lembur
		// */
		// // List Golongan I1 - IV9
		// // Ambil data kodegolongan yang lembur disnaker (bukan tetap)
		// $arr_list_group_golongan = array();
		// $sql = "select * from sdm_5golongan where alias like '%lembur%'";
		// $q_sql = mysql_query($sql);
		// while ($r_sql = mysql_fetch_assoc($q_sql)) {
		// 	// memasukan data ke var array 
		// 	$arr_list_group_golongan[] = $r_sql['kodegolongan'];
		// }
		
		// // 14. get total tmp_absensidt, datakaryawan
		// /*
		// 	Mengambil total
		// 	dari table tmp_absensidt, datakaryawan
		// 	kondisi nama_file
		// */
		// $totx= 0;
		// $squery1="select count(x.karyawanid) as totx from (SELECT a.*,b.karyawanid, b.namakaryawan FROM tmp_absensidt a inner join datakaryawan b on a.nik=b.nik where nama_file='".$nama_file."') x";
		// $hasil1 = mysql_query($squery1);
		// $res1 = mysql_fetch_array($hasil1); 
		
		// // 15. get data tmp_absensidt, datakaryawan
		// /*
		// 	Mengambil total
		// 	dari table tmp_absensidt, datakaryawan
		// 	kondisi nama_file
		// */
		// $totx= $res1['totx'];
		$squery="SELECT * FROM tmp_boilerdt  where namafile='".$nama_file."' ";
		$hasil = mysql_query($squery);
		 
		// echo "<pre>";
		// print_r($squery);
		// die();
		// 16. proses perhitungan uang lembur
			$brs = 0;
		while ($res = mysql_fetch_array($hasil)) { 
			$brs++;
			
			$tgl= $res['tgl'];
			$jam= $res['jam'];
			$idf_a = $res['idf_a'];
			$fdf_a = $res['fdf_a'];
			$scdf_a = $res['scdf_a'];
			$fff_a = $res['fff_a'];
			$fp1_a = $res['fp1_a'];;
			$fp2_a = $res['fp2_a'];
			$feed_tank = $res['feed_tank'];
			$dearator = $res['dearator'];
			$gas_duct = $res['gas_duct'];
			$sh = $res['sh'];
			$main_steam = $res['main_steam'];
			$fp1 = $res['fp1'];
			$fp2 = $res['fp2'];
			$flow_meter = $res['flow_meter'];
			$level_airfeed = $res['level_airfeed'];
			$water_level = $res['water_level'];
			$tds = $res['tds'];
			$jam_sb = $res['jam_sb'];
			$keterangan = $res['keterangan'];
			
			if($tgl =='0000-00-00' ){
				?>
				<script>
					alert(" SIMPAN DATA GAGAL ,FORMAT TANGGAL TIDAK SESUAI(yyyy-mm-dd) PADA BARIS :"+<?php echo $brs; ?>+",MOHON UPLOAD ULANG DENGAN FORMAT YANG BENAR !!!");
					window.history.back();
				</script>

				<?php
				die();
			}else{

			$content_body .= '<tr class="rowcontent" >
				<td>'.$tgl.'</td>
				<td>'.$jam.'</td>
				<td>'.$idf_a.'</td>
				<td>'.$fdf_a.'</td>
				<td >'.$scdf_a.'</td>
				<td>'.$fff_a.'</td>
				<td>'.$fp1_a.'</td>
				<td>'.$fp2_a.'</td>
				<td>'.$feed_tank.'</td>
				<td>'.$dearator.'</td>
				<td>'.$gas_duct.'</td>
				<td>'.$sh.'</td>
				<td>'.$main_steam.'</td>
				<td>'.$fp1.'</td>
				<td>'.$fp2.'</td>
				<td>'.$flow_meter.'</td>
				<td>'.$level_airfeed.'</td>
				<td>'.$water_level.'</td>
				<td>'.$tds.'</td>
				<td>'.$jam_sb.'</td>
				<td>'.$keterangan.'</td>
				</tr>';	
		
			$counter_data++;		
		}
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
			$content_table = $content_notife.$content_head.$content_body.$content_tail;
			 
		}	
	}else{
	// 02. Kalau file bukan xls
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
		// get tanggal di tabe tmp_absensidt
		

		// get data karyawan
		$squery="SELECT a.*,b.kodeorganisasi as kodeorg FROM tmp_boilerdt a,tmp_boilerht b  where a.namafile=b.namafile and a.namafile='".$namafile."' ";
		// echo "<pre>";
		// print_r($squery);
		// die();
		$hasil = mysql_query($squery);
		while ($res = mysql_fetch_array($hasil)) { 
			$tgl= $res['tgl'];
			$jam= $res['jam'];
			$idf_a = $res['idf_a'];
			$fdf_a = $res['fdf_a'];
			$scdf_a = $res['scdf_a'];
			$fff_a = $res['fff_a'];
			$fp1_a = $res['fp1_a'];;
			$fp2_a = $res['fp2_a'];
			$feed_tank = $res['feed_tank'];
			$dearator = $res['dearator'];
			$gas_duct = $res['gas_duct'];
			$sh = $res['sh'];
			$main_steam = $res['main_steam'];
			$fp1 = $res['fp1'];
			$fp2 = $res['fp2'];
			$flow_meter = $res['flow_meter'];
			$level_airfeed = $res['level_airfeed'];
			$water_level = $res['water_level'];
			$tds = $res['tds'];
			$jam_sb = $res['jam_sb'];
			$keterangan = $res['keterangan'];
			$kodeorg = $res['kodeorg'];
			
			
			
			$queryCekAbsensiDT = "SELECT * FROM pabrik_boiler WHERE kodeorg = '".$kodeorg."' and tgl = '".$tgl."' and jam = '".$jam."'  ";
			
			$resultCekAbsensiDT = mysql_query($queryCekAbsensiDT);
			$jumlahCekAbsensiDT = mysql_num_rows($resultCekAbsensiDT);
			if($jumlahCekAbsensiDT > 0){
				$sDetIns = "UPDATE ".$dbname.".pabrik_boiler 
							SET
								idf_a = '".$idf_a."',
								fdf_a='".$fdf_a."',
								scdf_a='".$scdf_a."',
								fff_a='".$fff_a."',
								fp1_a='".$fp1_a."',
								fp2_a='".$fp2_a."',
								feed_tank='".$feed_tank."',
								dearator='".$dearator."',
								gas_duct='".$gas_duct."',
								sh='".$sh."',
								main_steam='".$main_steam."',
								fp1='".$fp1."',
								fp2='".$fp2."',
								flow_meter='".$flow_meter."',
								level_airfeed='".$level_airfeed."',
								water_level='".$water_level."',
								tds='".$tds."',
								jam_sb='".$jam_sb."',
								keterangan='".$keterangan."'
							WHERE
								kodeorg = '".$kodeorg."'
							AND
								tgl = '".$tgl."'
							;";
			}else{
				 $sDetIns="insert into ".$dbname.".pabrik_boiler values('".$namafile."','".$tgl."','".$jam."','".$idf_a."','".$fdf_a."','".$scdf_a."','".$fff_a."','".$fp1_a."','".$fp2_a."','".$feed_tank."','".$dearator."','".$gas_duct."','".$sh."','".$main_steam."','".$fp1."','".$fp2."','".$flow_meter."','".$level_airfeed."','".$water_level."','".$tds."','".$jam_sb."','".$keterangan."','".$kodeorg."',current_timestamp)"; 
			}

			
			$qu2 = mysql_query($sDetIns);
			if($qu2 == true){
						
				
			}else{
				echo "warning :".$sDetIns;	
			}
			
		}
		// mengosongkan tmp
		// $emptyQueryTable1 = "TRUNCATE TABLE tmp_absensiht where nama_file='".$namafile."'"";
		$emptyQueryTable2 = "TRUNCATE TABLE tmp_boilerdt";
		// print_r($emptyQueryTable2);
		// die();
		// $actEmpty1 = mysql_query($emptyQueryTable1);
		$actEmpty2 = mysql_query($emptyQueryTable2);

		if(!$qu2) $flag=false;	
		if($flag==true){			
		?>
				<script>
					alert(" SIMPAN DATA SUKSES, .");
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

OPEN_BOX('', 'Pabrik Logsheet Boiler Upload</b>');
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
$optPeriode = '';
while ($rGp = mysql_fetch_assoc($qGp)) {
    $optPeriode .= '<option value='.$rGp['periode'].'>'.substr(tanggalnormal($rGp['periode']), 1, 7).'</option>';
}
$idOrg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."' \r\n           ORDER BY `namaorganisasi` ASC";
if (6 == strlen($_SESSION['empl']['subbagian'])) {
    $sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where (kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."') \r\n           and kodeorganisasi like '".$_SESSION['empl']['subbagian']."%' \r\n           ORDER BY `namaorganisasi` ASC";
}

$query = mysql_query($sql);
$optOrg = '';
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
								<select id="kdOrg" name="kdOrg" style="width:150px;" >
								<?php
									$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' \r\n           ORDER BY `namaorganisasi` ASC";
									// if (6 == strlen($_SESSION['empl']['subbagian'])) {
									// 	$sql = "select kodeorganisasi,namaorganisasi \r\n           from ".$dbname.".organisasi \r\n           where (kodeorganisasi='".$idOrg."' or induk='".$_SESSION['empl']['lokasitugas']."') \r\n           and kodeorganisasi like '".$_SESSION['empl']['subbagian']."%' \r\n           ORDER BY `namaorganisasi` ASC";
									// }

									$kdOrg = $_SESSION['empl']['lokasitugas'];
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
								<!-- <?php echo $sql;?></td> -->
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
