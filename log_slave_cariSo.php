<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
//check if transaction period is normal
if(isTransactionPeriod()){
	$noso	 = isset($_POST['noso']) ? $_POST['noso'] : '';
	$unitDt	 = isset($_POST['unitDt']) ? $_POST['unitDt'] : '';
	$gudang	 = isset($_POST['gudang']) ? $_POST['gudang'] : '';
	$periode = isset($_POST['periode']) ? $_POST['periode'] : '';
	
	echo"<table cellspacing=1 border=0 class=data>
        <thead>
		<tr class=rowheader><td>No</td>
		    <td>No Stokopname</td>
			<td>No Referensi</td>
			<td>".$_SESSION['lang']['tanggal']."</td>
			<td>".$_SESSION['lang']['note']."</td>
			<td>Unit</td>
			<td>Gudang</td>
			<td>".$_SESSION['lang']['periode']."</td>
			<td>".$_SESSION['lang']['status']."</td>
		</tr>
		</thead>
		</tbody>";
	$str = "SELECT * FROM ".$dbname.".log_5stokopnameht
	WHERE nostokopname LIKE '%".$noso."%'
	AND kdunit LIKE '%".$unitDt."%'
	AND kdgudang LIKE '%".$gudang."%'
	AND periode LIKE '%".$periode."%'
    ORDER BY id desc";
  $res=mysql_query($str);
  $no=0;
  while($bar=mysql_fetch_object($res)){
  	$no+=1;
	if($bar->status == 0){
		$Status = 'Draft';
		$Style = "style='cursor:pointer;'";
		$OnClick = "onclick=goPickSo('".$bar->id."')";
	} else if($bar->status == 1){
		$Status = 'Cancel';
		$Style = "style='background-color:red;'";
		$OnClick = "";
	} else if($bar->status == 3){
		$Status = 'Posting';
		$Style = "style='background-color:green;'";
		$OnClick = "";
	}
	echo"
		<tr class=rowcontent ".$Style." title='Click It' ".$OnClick." ><td>".$no."</td>
		    <td>".$bar->nostokopname."</td>
			<td>".$bar->reffno."</td>
			<td>".tanggalnormal($bar->tanggal)."</td>
			<td>".$bar->note."</td>
			<td>".$bar->kdunit."</td>
			<td>".$bar->kdgudang."</td>
			<td>".$bar->periode."</td>
			<td>".$Status."</td>
		</tr>";	
   }	 		
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";		
}
else{
	echo " Error: Transaction Period missing";
}
?>