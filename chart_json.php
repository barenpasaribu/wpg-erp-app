<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';

$str = "select a1.* from (select extract(month from tanggal) as bulan,count(*) as qty_po,sum(nilaipo) as total_po from log_poht where EXTRACT(YEAR FROM tanggal) = '2019' group by extract(month from tanggal)) as a1 order by a1.bulan asc;";

#exit(mysql_error($conn));
$res = mysql_query($str);


while ($bar = mysql_fetch_object($res)) {
	$arr_data_qty[$bar->bulan] = $bar->qty_po;
	$arr_data_amount[$bar->bulan] = $bar->total_po;
	
}

for($i=1;$i<=12;$i++){
	if( isset($arr_data_qty[$i]) ){
		$arr_data['qty_po'][] = (real)$arr_data_qty[$i];
	}else{
		$arr_data['qty_po'][] = null;
	}
	if( isset($arr_data_amount[$i]) ){
		$arr_data['total_po'][] = (real)$arr_data_amount[$i];
	}else{
		$arr_data['total_po'][] = null;
	}
}
echo json_encode($arr_data);

?>
