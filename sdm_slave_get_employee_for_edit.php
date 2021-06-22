<?php

	require_once 'master_validation.php';
	require_once 'config/connection.php';
	require_once 'lib/eagrolib.php';
	$karyawanid = $_POST['karyawanid'];
	$str = 'select * from '.$dbname.'.datakaryawan where karyawanid='.$karyawanid.' limit 1';
	$res = mysql_query($str);
	while ($bar = mysql_fetch_object($res)) {
		$tanggalKeluar = NULL;
		if ($bar->tanggalkeluar != NULL) {
			$tanggalKeluar = tanggalnormal($bar->tanggalkeluar);
		}else{
			$tanggalKeluar = "-";
		}
		
		echo "<?xml version='1.0' ?>\r\n\t     <karyawan>\r\n\t\t\t <karyawanid>"
		.(($bar->karyawanid !='' ? $bar->karyawanid : '*'))."</karyawanid>\r\n\t\t\t <nik>"
		.(($bar->nik !='' ? $bar->nik : '*'))."</nik>\r\n\t\t\t <namakaryawan>"
		.(($bar->namakaryawan !='' ? $bar->namakaryawan : '*'))."</namakaryawan>\r\n\t\t\t <tempatlahir>"
		.(($bar->tempatlahir !='' ? $bar->tempatlahir : '*'))."</tempatlahir>\r\n\t\t\t <tanggallahir>"
		.tanggalnormal($bar->tanggallahir)."</tanggallahir>\r\n\t\t     <warganegara>"
		.(($bar->warganegara !='' ? $bar->warganegara : '*'))."</warganegara>\r\n\t\t     <jeniskelamin>"
		.(($bar->jeniskelamin !='' ? $bar->jeniskelamin : '*'))."</jeniskelamin>\r\n\t\t\t <statusperkawinan>"
		.(($bar->statusperkawinan !='' ? $bar->statusperkawinan : '*'))."</statusperkawinan>\r\n\t\t\t <tanggalmenikah>"
		.tanggalnormal($bar->tanggalmenikah)."</tanggalmenikah>\r\n\t\t\t <agama>"
		.(($bar->agama !='' ? $bar->agama : '*'))."</agama>\r\n\t\t\t <golongandarah>"
		.(($bar->golongandarah !='' ? $bar->golongandarah : '*'))."</golongandarah>\r\n\t\t\t <levelpendidikan>"
		.(($bar->levelpendidikan !='' ? $bar->levelpendidikan : '*'))."</levelpendidikan>\r\n\t\t\t <alamataktif>"
		.(($bar->alamataktif !='' ? $bar->alamataktif : '*'))."</alamataktif>\r\n\t\t\t <provinsi>"
		.(($bar->provinsi !='' ? $bar->provinsi : '*'))."</provinsi>\r\n\t\t\t <kota>"
		.(($bar->kota !='' ? $bar->kota : '*'))."</kota>\r\n\t\t\t <kodepos>"
		.(($bar->kodepos !='' ? $bar->kodepos : '*'))."</kodepos>\r\n\t\t\t <noteleponrumah>"
		.(($bar->noteleponrumah !='' ? $bar->noteleponrumah : '*'))."</noteleponrumah>\r\n\t\t\t <nohp>"
		.(($bar->nohp !='' ? $bar->nohp : '*'))."</nohp>\r\n\t\t\t <norekeningbank>"
		.(($bar->norekeningbank !='' ? $bar->norekeningbank : '*'))."</norekeningbank>\r\n\t\t\t <namabank>"
		.(($bar->namabank !='' ? $bar->namabank : '*'))."</namabank>\r\n\t\t\t <sistemgaji>"
		.(($bar->sistemgaji !='' ? $bar->sistemgaji : '*'))."</sistemgaji>\r\n\t\t\t <nopaspor>"
		.(($bar->nopaspor !='' ? $bar->nopaspor : '*'))."</nopaspor>\r\n\t\t\t <noktp>"
		.(($bar->noktp !='' ? $bar->noktp : '*'))."</noktp>\r\n\t\t\t <notelepondarurat>"
		.(($bar->notelepondarurat !='' ? $bar->notelepondarurat : '*'))."</notelepondarurat>\r\n\t\t     <tanggalmasuk>"
		.tanggalnormal($bar->tanggalmasuk)."</tanggalmasuk>\r\n\t\t     <tanggalkeluar>"
		.$tanggalKeluar."</tanggalkeluar>\r\n\t\t\t <tipekaryawan>"
		.(($bar->tipekaryawan !='' ? $bar->tipekaryawan : '*'))."</tipekaryawan>\r\n\t\t\t <jumlahanak>"
		.(($bar->jumlahanak !='' ? $bar->jumlahanak : '*'))."</jumlahanak>\t\r\n\t\t\t <jumlahtanggungan>"
		.(($bar->jumlahtanggungan != '' ? $bar->jumlahtanggungan : '*'))."</jumlahtanggungan>\t\t\t \r\n\t\t     <statuspajak>"
		.(($bar->statuspajak != '' ? $bar->statuspajak : '*'))."</statuspajak>\r\n\t\t\t <npwp>"
		.(($bar->npwp != '' ? $bar->npwp : '*'))."</npwp>\r\n\t\t\t <lokasipenerimaan>"
		.(($bar->lokasipenerimaan != '' ? $bar->lokasipenerimaan : '*'))."</lokasipenerimaan>\r\n\t\t\t <kodeorganisasi>"
		.(($bar->kodeorganisasi != '' ? $bar->kodeorganisasi : '*'))."</kodeorganisasi>\r\n\t\t     <bagian>"
		.(($bar->bagian != '' ? $bar->bagian : '*'))."</bagian>\r\n\t\t\t <kodejabatan>"
		.(($bar->kodejabatan != '' ? $bar->kodejabatan : '*'))."</kodejabatan>\r\n\t\t\t <kodegolongan>"
		.(($bar->kodegolongan != '' ? $bar->kodegolongan : '*'))."</kodegolongan>\r\n\t\t\t <lokasitugas>"
		.(($bar->lokasitugas != '' ? $bar->lokasitugas : '*'))."</lokasitugas>\r\n\t\t\t  <photo>"
		.(($bar->photo != '' ? $bar->photo : '*'))."</photo>\r\n\t\t\t <email>"
		.(($bar->email != '' ? $bar->email : '*'))."</email> \r\n\t\t\t <alokasi>"
		.(($bar->alokasi != '' ? $bar->alokasi : '*'))."</alokasi>\r\n\t\t\t <subbagian>"
		.(($bar->subbagian != '' ? $bar->subbagian : '*'))."</subbagian>\r\n\t\t\t <jms>"
		.(($bar->jms != '' ? $bar->jms : '*'))."</jms>\r\n             <bpjskes>"
		.(($bar->idmedical != '' ? $bar->idmedical : '*'))."</bpjskes>\r\n\t\t\t <catu>"
		.(($bar->kodecatu != '' ? $bar->kodecatu : '0'))."</catu>    \r\n\t\t\t <dptPremi>"
		.$bar->statpremi."</dptPremi>\r\n\t\t\t \r\n\t\t\t  <kecamatan>"
		.(($bar->kecamatan != '' ? $bar->kecamatan : '*'))."</kecamatan>\r\n\t\t\t  <desa>"
		.(($bar->desa != '' ? $bar->desa : '*'))."</desa>\r\n\t\t\t  
		<pangkat>".(($bar->pangkat != '' ? $bar->pangkat : '*'))."</pangkat>\r\n\r\n\t\t 
		<isduplicate>".(($bar->isduplicate != '' ? $bar->isduplicate : '*'))."</isduplicate>\r\n\r\n\t\t 
		</karyawan>";
	}

?>