<?php

/**###
 
	PPH 21  ==> 24
	JKK = 6
	JKM ==> 7
	BPJS ==>  57
	--
	total upah tetap ==>
		gaji pokok => 1
		tunjangan golongan => 3|35|36|37|38|40|41|42|43|44|45|46|47|48|49|50|51
		tunjangan jabatan => 2
		natura pekerja => 4
		natura keluarga => Natura K0 atau sesuai status kawin
		tunjangan masa kerja => 15
	lembur ==> 17
	Premi BKM ==> 16
	premi pendapatan lainnya ==> 16 
	tunjangan komunikasi ==> 63
	tunjangan lokasi ==> 58
	tunjangan rumah tangga ==> 59
	tunjangan BBM ==> 61
	tunjangan air minum ==> 65
	tunjangan s.part => 60
	tunjangan harian => 21
	tunjangan dinas => 23
	tunjangan cuti => 12
	tunjangan listrik => 62
	tunjangan lain (ban luar dalam) => 22
	rapel kenaikan =>  54
	
	
	---
	BRUTO = GROSS + JKK + JKM + BPJS

	1,3,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,2,4,15,17,16,63,58,59,61,65,60,21,23,12,62,22,54

###*/

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
//echo open_body();
//include 'master_mainMenu.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="pph21.csv"');

echo "Masa Pajak;Tahun Pajak;Pembetulan;NPWP;Nama;Kode Pajak;Jumlah Bruto;Jumlah PPh;Kode Negara"."\n";
//ambil data bruto
$sql = "select * from sdm_bruto_pajak where kodeorg='".mysql_real_escape_string(strtoupper($_GET['kodeorg']))."' and periodegaji='".mysql_real_escape_string($_GET['periodegaji'])."'; ";

$qBasis = mysql_query($sql);
while ($rBasis = mysql_fetch_assoc($qBasis)) {
	$arr_data_bruto[$rBasis['karyawanid']] = $rBasis['amount'];
}

/* sum(if(idkomponen in (1,3,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,2,4,15,17,16,63,58,59,61,65,60,21,23,12,62,22,54),jumlah,0)) as gross_exc_natura_kx,
*/

$sGt = "select kodeorg,periodegaji,t1.karyawanid,t2.namakaryawan,t2.npwp,
sum(if(idkomponen in (24),jumlah,0)) as pph21 ,
sum(if(idkomponen in (6),jumlah,0)) as jkk,
sum(if(idkomponen in (7),jumlah,0)) as jkm,
sum(if(idkomponen in (57),jumlah,0)) as bpjs,
sum(if(idkomponen in (1,2,29,30,32,33,4,15, 17,16,58,61,21,23),jumlah,if(idkomponen in (20,26,27,64),-jumlah,0))) as gross_exc_natura_kx,
case 
	when t2.statuspajak = 'K0' then sum(if(idkomponen in (33),jumlah,0))
	when t2.statuspajak = 'K1' then sum(if(idkomponen in (29),jumlah,0))
	when t2.statuspajak = 'K2' then sum(if(idkomponen in (30),jumlah,0))
	when t2.statuspajak = 'K3' then sum(if(idkomponen in (32),jumlah,0))
	else 0
end as natura_kx
from sdm_gaji as t1 left join datakaryawan as t2 on (t1.karyawanid=t2.karyawanid) where periodegaji='".mysql_real_escape_string($_GET['periodegaji'])."' and kodeorg ='".mysql_real_escape_string(strtoupper($_GET['kodeorg']))."' group by kodeorg,periodegaji,karyawanid";
//from sdm_gaji as t1 left join datakaryawan as t2 on (t1.karyawanid=t2.karyawanid) where t2.npwp is not null and t2.npwp != '' and 
//echo $sGt;
$qBasis = mysql_query($sGt);
while ($rBasis = mysql_fetch_assoc($qBasis)) {
	//$jumlah_bruto = $rBasis['gross_exc_natura_kx'] + $rBasis['natura_kx'] + $rBasis['jkk'] + $rBasis['jkm'] + $rBasis['bpjs'] ;
	$find = array(".",",","-"," ");
	$replace = array("");
	if( isset($arr_data_bruto[$rBasis['karyawanid']]) ){
		$jumlah_bruto= $arr_data_bruto[$rBasis['karyawanid']];
	}else{
		$jumlah_bruto= 0;
	}
	echo substr($rBasis['periodegaji'],5,2).";".substr($rBasis['periodegaji'],0,4).";0;".(string)str_replace($find,$replace,$rBasis['npwp']).";".$rBasis['namakaryawan'].";21-100-01;".floor($jumlah_bruto).";".floor($rBasis['pph21']).";"."\n";	
}




?>