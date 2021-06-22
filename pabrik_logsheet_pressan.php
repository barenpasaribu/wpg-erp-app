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
		$qry="insert into ".$dbname.".tmp_pressanht values('".$nama_file."','".$kdOrg."','".$date."'); ";
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

			$temp_d1= $data->val($i, 3);
			$level_d1=$data->val($i, 4);			
			$amp_d1= $data->val($i, 5);
			$hm_d1= $data->val($i, 6);

			$temp_d2= $data->val($i, 7);
			$level_d2=$data->val($i, 8);			
			$amp_d2= $data->val($i, 9);
			$hm_d2= $data->val($i, 10);

			$temp_d3= $data->val($i, 11);
			$level_d3=$data->val($i, 12);			
			$amp_d3= $data->val($i, 13);
			$hm_d3= $data->val($i, 14);

			$temp_d4= $data->val($i, 15);
			$level_d4=$data->val($i, 16);			
			$amp_d4= $data->val($i, 17);
			$hm_d4= $data->val($i, 18);

			

			$temp_d5= $data->val($i, 19);
			$level_d5=$data->val($i, 20);			
			$amp_d5= $data->val($i, 21);
			$hm_d5= $data->val($i, 22);

			$temp_d6= $data->val($i, 23);
			$level_d6=$data->val($i, 24);			
			$amp_d6= $data->val($i, 25);
			$hm_d6= $data->val($i, 26);

			$temp_d7= $data->val($i, 27);
			$level_d7=$data->val($i, 28);			
			$amp_d7= $data->val($i, 29);
			$hm_d7= $data->val($i, 30);

			$temp_d8= $data->val($i, 31);
			$level_d8=$data->val($i, 32);			
			$amp_d8= $data->val($i, 33);
			$hm_d8= $data->val($i, 34);

			

			$th_p1=$data->val($i, 35);
			$amp_p1= $data->val($i, 36);
			$hm_p1= $data->val($i, 37);

			$th_p2=$data->val($i, 38);
			$amp_p2= $data->val($i, 39);
			$hm_p2= $data->val($i, 40);

			$th_p3=$data->val($i, 41);
			$amp_p3= $data->val($i, 42);
			$hm_p3= $data->val($i, 43);

			$th_p4=$data->val($i, 44);
			$amp_p4= $data->val($i, 45);
			$hm_p4= $data->val($i, 46);

			$th_p5=$data->val($i, 47);
			$amp_p5= $data->val($i, 48);
			$hm_p5= $data->val($i, 49);

			$th_p6=$data->val($i, 50);
			$amp_p6= $data->val($i, 51);
			$hm_p6= $data->val($i, 52);

			$th_p7=$data->val($i, 53);
			$amp_p7= $data->val($i, 54);
			$hm_p7= $data->val($i, 55);

			$th_p8=$data->val($i, 56);
			$amp_p8= $data->val($i, 57);
			$hm_p8= $data->val($i, 58);

			$hm_cbc1= $data->val($i, 59);
			$hm_cbc2= $data->val($i, 60);
			$keterangan	= $data->val($i, 61);
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
			 $qry1="insert into ".$dbname.".tmp_pressandt values('".$nama_file."','".$tgl."','".$jam."','".$temp_d1."','".$level_d1."','".$amp_d1."','".$hm_d1."','".$temp_d2."','".$level_d2."','".$amp_d2."','".$hm_d2."','".$temp_d3."','".$level_d3."','".$amp_d3."','".$hm_d3."','".$temp_d4."','".$level_d4."','".$amp_d4."','".$hm_d4."','".$temp_d5."','".$level_d5."','".$amp_d5."','".$hm_d5."','".$temp_d6."','".$level_d6."','".$amp_d6."','".$hm_d6."','".$temp_d7."','".$level_d7."','".$amp_d7."','".$hm_d7."','".$temp_d8."','".$level_d8."','".$amp_d8."','".$hm_d8."','".$th_p1."','".$amp_p1."','".$hm_p1."','".$th_p2."','".$amp_p2."','".$hm_p2."','".$th_p3."','".$amp_p3."','".$hm_p3."','".$th_p4."','".$amp_p4."','".$hm_p4."','".$th_p5."','".$amp_p5."','".$hm_p5."','".$th_p6."','".$amp_p6."','".$hm_p6."','".$th_p7."','".$amp_p7."','".$hm_p7."','".$th_p8."','".$amp_p8."','".$hm_p8."','".$hm_cbc1."','".$hm_cbc2."','".$keterangan."',current_timestamp); ";				
			
								
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
				<td>JAM </td>
				<td>TEMP D1</td>
				<td>LEVEL D1</td>
				<td>AMP D1</td>
				<td>HM D1</td>
				<td>TEMP D2</td>
				<td>LEVEL D2</td>
				<td>AMP D2</td>
				<td>HM D2</td>
				<td>TEMP D3</td>
				<td>LEVEL D3</td>
				<td>AMP D3</td>
				<td>HM D3</td>
				<td>TEMP D4</td>
				<td>LEVEL D4</td>
				<td>AMP D4</td>
				<td>HM D4</td>
				<td>TEMP D5</td>
				<td>LEVEL D5</td>
				<td>AMP D5</td>
				<td>HM D5</td>
				<td>TEMP D6</td>
				<td>LEVEL D6</td>
				<td>AMP D6</td>
				<td>HM D6</td>
				<td>TEMP D7</td>
				<td>LEVEL D7</td>
				<td>AMP D7</td>
				<td>HM D7</td>
				<td>TEMP D8</td>
				<td>LEVEL D8</td>
				<td>AMP D8</td>
				<td>HM D8</td>
				<td>T. HYD P1</td>
				<td>AMP P1</td>
				<td>HM P1</td>
				<td>T. HYD P2</td>
				<td>AMP P2</td>
				<td>HM P2</td>
				<td>T. HYD P3</td>
				<td>AMP P3</td>
				<td>HM P3</td>
				<td>T. HYD P4</td>
				<td>AMP P4</td>
				<td>HM P4</td>
				<td>T. HYD P5</td>
				<td>AMP P5</td>
				<td>HM P5</td>
				<td>T. HYD P6</td>
				<td>AMP P6</td>
				<td>HM P6</td>
				<td>T. HYD P7</td>
				<td>AMP P7</td>
				<td>HM P7</td>
				<td>T. HYD P8</td>
				<td>AMP P8</td>
				<td>HM P8</td>
				<td>HM CBC1</td>
				<td>HM CBC2</td>
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
		$squery="SELECT * FROM tmp_pressandt  where namafile='".$nama_file."' ";
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
			$temp_d1 = $res['temp_d1'];
			$level_d1 = $res['level_d1'];
			$amp_d1 = $res['amp_d1'];
			$hm_d1 = $res['hm_d1'];

			$temp_d2 = $res['temp_d2'];
			$level_d2 = $res['level_d2'];
			$amp_d2 = $res['amp_d2'];
			$hm_d2 = $res['hm_d2'];

			$temp_d3 = $res['temp_d3'];
			$level_d3 = $res['level_d3'];
			$amp_d3 = $res['amp_d3'];
			$hm_d3 = $res['hm_d3'];

			$temp_d4 = $res['temp_d4'];
			$level_d4 = $res['level_d4'];
			$amp_d4 = $res['amp_d4'];
			$hm_d4 = $res['hm_d4'];

			$temp_d5 = $res['temp_d5'];
			$level_d5 = $res['level_d5'];
			$amp_d5 = $res['amp_d5'];
			$hm_d5 = $res['hm_d5'];

			$temp_d6 = $res['temp_d6'];
			$level_d6 = $res['level_d6'];
			$amp_d6 = $res['amp_d6'];
			$hm_d6 = $res['hm_d6'];

			$temp_d7 = $res['temp_d7'];
			$level_d7 = $res['level_d7'];
			$amp_d7 = $res['amp_d7'];
			$hm_d7 = $res['hm_d7'];

			$temp_d8 = $res['temp_d8'];
			$level_d8 = $res['level_d8'];
			$amp_d8 = $res['amp_d8'];
			$hm_d8 = $res['hm_d8'];

			$th_p1 = $res['th_p1'];;
			$amp_p1 = $res['amp_p1'];
			$hm_p1 = $res['hm_p1'];

			$th_p2 = $res['th_p2'];;
			$amp_p2 = $res['amp_p2'];
			$hm_p2 = $res['hm_p2'];

			$th_p3 = $res['th_p3'];;
			$amp_p3 = $res['amp_p3'];
			$hm_p3 = $res['hm_p3'];

			$th_p4 = $res['th_p4'];;
			$amp_p4 = $res['amp_p4'];
			$hm_p4 = $res['hm_p4'];

			$th_p5 = $res['th_p5'];;
			$amp_p5 = $res['amp_p5'];
			$hm_p5 = $res['hm_p5'];

			$th_p6 = $res['th_p6'];;
			$amp_p6 = $res['amp_p6'];
			$hm_p6 = $res['hm_p6'];

			$th_p7 = $res['th_p7'];;
			$amp_p7 = $res['amp_p7'];
			$hm_p7 = $res['hm_p7'];

			$th_p8 = $res['th_p8'];;
			$amp_p8 = $res['amp_p8'];
			$hm_p8 = $res['hm_p8'];

			$hm_cbc1 = $res['hm_cbc1'];
			$hm_cbc2 = $res['hm_cbc2'];
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

				<td>'.$temp_d1.'</td>
				<td>'.$level_d1.'</td>
				<td >'.$amp_d1.'</td>
				<td>'.$hm_d1.'</td>

					<td>'.$temp_d2.'</td>
				<td>'.$level_d2.'</td>
				<td >'.$amp_d2.'</td>
				<td>'.$hm_d2.'</td>

					<td>'.$temp_d3.'</td>
				<td>'.$level_d3.'</td>
				<td >'.$amp_d3.'</td>
				<td>'.$hm_d3.'</td>

					<td>'.$temp_d4.'</td>
				<td>'.$level_d4.'</td>
				<td >'.$amp_d4.'</td>
				<td>'.$hm_d4.'</td>

					<td>'.$temp_d5.'</td>
				<td>'.$level_d5.'</td>
				<td >'.$amp_d5.'</td>
				<td>'.$hm_d5.'</td>

					<td>'.$temp_d6.'</td>
				<td>'.$level_d6.'</td>
				<td >'.$amp_d6.'</td>
				<td>'.$hm_d6.'</td>

					<td>'.$temp_d7.'</td>
				<td>'.$level_d7.'</td>
				<td >'.$amp_d7.'</td>
				<td>'.$hm_d7.'</td>

					<td>'.$temp_d8.'</td>
				<td>'.$level_d8.'</td>
				<td >'.$amp_d8.'</td>
				<td>'.$hm_d8.'</td>

				<td>'.$th_p1.'</td>
				<td >'.$amp_p1.'</td>
				<td>'.$hm_p1.'</td>

				<td>'.$th_p2.'</td>
				<td >'.$amp_p2.'</td>
				<td>'.$hm_p2.'</td>

				<td>'.$th_p3.'</td>
				<td >'.$amp_p3.'</td>
				<td>'.$hm_p3.'</td>

				<td>'.$th_p4.'</td>
				<td >'.$amp_p4.'</td>
				<td>'.$hm_p4.'</td>

				<td>'.$th_p5.'</td>
				<td >'.$amp_p5.'</td>
				<td>'.$hm_p5.'</td>

				<td>'.$th_p6.'</td>
				<td >'.$amp_p6.'</td>
				<td>'.$hm_p6.'</td>

				<td>'.$th_p7.'</td>
				<td >'.$amp_p7.'</td>
				<td>'.$hm_p7.'</td>

				<td>'.$th_p8.'</td>
				<td >'.$amp_p8.'</td>
				<td>'.$hm_p8.'</td>
				<td>'.$hm_cbc1.'</td>
				<td >'.$hm_cbc2.'</td>
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
		$squery="SELECT a.*,b.kodeorganisasi as kodeorg FROM tmp_pressandt a,tmp_pressanht b  where a.namafile=b.namafile and a.namafile='".$namafile."' ";
		// echo "<pre>";
		// print_r($squery);
		// die();
		$hasil = mysql_query($squery);
		while ($res = mysql_fetch_array($hasil)) { 
				$tgl= $res['tgl'];
			$jam= $res['jam'];
			$temp_d1 = $res['temp_d1'];
			$level_d1 = $res['level_d1'];
			$amp_d1 = $res['amp_d1'];
			$hm_d1 = $res['hm_d1'];

			$temp_d2 = $res['temp_d2'];
			$level_d2 = $res['level_d2'];
			$amp_d2 = $res['amp_d2'];
			$hm_d2 = $res['hm_d2'];

			$temp_d3 = $res['temp_d3'];
			$level_d3 = $res['level_d3'];
			$amp_d3 = $res['amp_d3'];
			$hm_d3 = $res['hm_d3'];

			$temp_d4 = $res['temp_d4'];
			$level_d4 = $res['level_d4'];
			$amp_d4 = $res['amp_d4'];
			$hm_d4 = $res['hm_d4'];

			$temp_d5 = $res['temp_d5'];
			$level_d5 = $res['level_d5'];
			$amp_d5 = $res['amp_d5'];
			$hm_d5 = $res['hm_d5'];

			$temp_d6 = $res['temp_d6'];
			$level_d6 = $res['level_d6'];
			$amp_d6 = $res['amp_d6'];
			$hm_d6 = $res['hm_d6'];

			$temp_d7 = $res['temp_d7'];
			$level_d7 = $res['level_d7'];
			$amp_d7 = $res['amp_d7'];
			$hm_d7 = $res['hm_d7'];

			$temp_d8 = $res['temp_d8'];
			$level_d8 = $res['level_d8'];
			$amp_d8 = $res['amp_d8'];
			$hm_d8 = $res['hm_d8'];

			$th_p1 = $res['th_p1'];;
			$amp_p1 = $res['amp_p1'];
			$hm_p1 = $res['hm_p1'];

			$th_p2 = $res['th_p2'];;
			$amp_p2 = $res['amp_p2'];
			$hm_p2 = $res['hm_p2'];

			$th_p3 = $res['th_p3'];;
			$amp_p3 = $res['amp_p3'];
			$hm_p3 = $res['hm_p3'];

			$th_p4 = $res['th_p4'];;
			$amp_p4 = $res['amp_p4'];
			$hm_p4 = $res['hm_p4'];

			$th_p5 = $res['th_p5'];;
			$amp_p5 = $res['amp_p5'];
			$hm_p5 = $res['hm_p5'];

			$th_p6 = $res['th_p6'];;
			$amp_p6 = $res['amp_p6'];
			$hm_p6 = $res['hm_p6'];

			$th_p7 = $res['th_p7'];;
			$amp_p7 = $res['amp_p7'];
			$hm_p7 = $res['hm_p7'];

			$th_p8 = $res['th_p8'];;
			$amp_p8 = $res['amp_p8'];
			$hm_p8 = $res['hm_p8'];

			$hm_cbc1 = $res['hm_cbc1'];
			$hm_cbc2 = $res['hm_cbc2'];
			$keterangan = $res['keterangan'];
			$kodeorg = $res['kodeorg'];
			
			
			
			$queryCekAbsensiDT = "SELECT * FROM pabrik_pressan WHERE kodeorg = '".$kodeorg."' and tgl = '".$tgl."' and jam = '".$jam."'   ";
			
			$resultCekAbsensiDT = mysql_query($queryCekAbsensiDT);
			$jumlahCekAbsensiDT = mysql_num_rows($resultCekAbsensiDT);
			if($jumlahCekAbsensiDT > 0){
				$sDetIns = "UPDATE ".$dbname.".pabrik_pressan 
							SET
								temp_d1 = '".$temp_d1."',
								level_d1='".$level_d1."',
								amp_d1='".$amp_d1."',
								hm_d1='".$hm_d1."',

								temp_d2 = '".$d2."',
								level_d2='".$level_d2."',
								amp_d2='".$amp_d2."',
								hm_d2='".$hm_d2."',

								temp_d3 = '".$temp_d3."',
								level_d3='".$level_d3."',
								amp_d3='".$amp_d3."',
								hm_d3='".$hm_d3."',

								temp_d4 = '".$temp_d4."',
								level_d4='".$level_d4."',
								amp_d4='".$amp_d4."',
								hm_d4='".$hm_d4."',

								temp_d5 = '".$temp_d5."',
								level_d5='".$level_d5."',
								amp_d5='".$amp_d5."',
								hm_d5='".$hm_d5."',

								temp_d6 = '".$temp_d6."',
								level_d6='".$level_d6."',
								amp_d6='".$amp_d6."',
								hm_d6='".$hm_d6."',

								temp_d7 = '".$temp_d7."',
								level_d7='".$level_d7."',
								amp_d7='".$amp_d7."',
								hm_d7='".$hm_d7."',

								temp_d8 = '".$temp_d8."',
								level_d8='".$level_d8."',
								amp_d8='".$amp_d8."',
								hm_d8='".$hm_d8."',

								th_p1='".$th_p1."',
								amp_p1='".$amp_p1."',
								hm_p1='".$hm_p1."',

								th_p2='".$th_p2."',
								amp_p2='".$amp_p2."',
								hm_p2='".$hm_p2."',

								th_p3='".$th_p3."',
								amp_p3='".$amp_p3."',
								hm_p3='".$hm_p3."',

								th_p4='".$th_p4."',
								amp_p4='".$amp_p4."',
								hm_p4='".$hm_p4."',

								th_p5='".$th_p5."',
								amp_p5='".$amp_p5."',
								hm_p5='".$hm_p5."',

								th_p6='".$th_p6."',
								amp_p6='".$amp_p6."',
								hm_p6='".$hm_p6."',

								th_p7='".$th_p7."',
								amp_p7='".$amp_p7."',
								hm_p7='".$hm_p7."',

								th_p8='".$th_p8."',
								amp_p8='".$amp_p8."',
								hm_p8='".$hm_p8."',

								hm_cbc1='".$hm_cbc1."',
								hm_cbc2='".$hm_cbc2."',
								keterangan='".$keterangan."'
							WHERE
								kodeorg = '".$kdOrg."'
							AND
								tgl = '".$tgl."'
								AND
								jam = '".$jam."' 
							;";
			}else{
				 $sDetIns="insert into ".$dbname.".pabrik_pressan values('".$namafile."','".$tgl."','".$jam."','".$temp_d1."','".$level_d1."','".$amp_d1."','".$hm_d1."','".$temp_d2."','".$level_d2."','".$amp_d2."','".$hm_d2."','".$temp_d3."','".$level_d3."','".$amp_d3."','".$hm_d3."','".$temp_d4."','".$level_d4."','".$amp_d4."','".$hm_d4."','".$temp_d5."','".$level_d5."','".$amp_d5."','".$hm_d5."','".$temp_d6."','".$level_d6."','".$amp_d6."','".$hm_d6."','".$temp_d7."','".$level_d7."','".$amp_d7."','".$hm_d7."','".$temp_d8."','".$level_d8."','".$amp_d8."','".$hm_d8."','".$th_p1."','".$amp_p1."','".$hm_p1."','".$th_p2."','".$amp_p2."','".$hm_p2."','".$th_p3."','".$amp_p3."','".$hm_p3."','".$th_p4."','".$amp_p4."','".$hm_p4."','".$th_p5."','".$amp_p5."','".$hm_p5."','".$th_p6."','".$amp_p6."','".$hm_p6."','".$th_p7."','".$amp_p7."','".$hm_p7."','".$th_p8."','".$amp_p8."','".$hm_p8."','".$hm_cbc1."','".$hm_cbc2."','".$keterangan."','".$kdOrg."',current_timestamp)"; 
			}

			
			$qu2 = mysql_query($sDetIns);
			if($qu2 == true){
						
				
			}else{
				echo "warning :".$sDetIns;	
			}
			
		}
		// mengosongkan tmp
		// $emptyQueryTable1 = "TRUNCATE TABLE tmp_absensiht where nama_file='".$namafile."'"";
		$emptyQueryTable2 = "TRUNCATE TABLE tmp_pressandt";
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

OPEN_BOX('', 'Pabrik Logsheet Pressan Upload</b>');
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
