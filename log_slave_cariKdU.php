<?php
/*
AUTO COMPLETE FOR KODE ORGANISASI
*/
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if(!empty($_POST['KdU']) || isset($_POST['KdU'])) {
	$KdU = $_POST['KdU'];
	echo "<table cellspacing=1 border=0 class=data>
    	<thead>
		<tr class=rowheader><td>No</td>
		    <td>".$_SESSION['lang']['kodeorganisasi']."</td>
			<td>".$_SESSION['lang']['namaorganisasi']."</td>
			<td>".$_SESSION['lang']['induk']."</td>
		</tr>
		</thead>
		</tbody>";
	//tambah filter lokasi tugas ==Jo 06-05-2017==
	if($_SESSION['empl']['pusat']==1){
		$whrorg="";
	}
	else{
		$whrorg="and kodeorganisasi ='".$_SESSION['empl']['lokasitugas']."'";
	}
	
	$sKdU = "select * from ".$dbname.".organisasi where kodeorganisasi like '%".$KdU."%' ".$whrorg."";
	
  	$qKdU = mysql_query($sKdU);
  	$no	  = 0;

	if(mysql_num_rows($qKdU) == 0) {
		echo"
		<tr class=rowcontent>
			<td colspan=4 align=center>No Data</td>
		</tr>";	
	} else {
		while($aKdU = mysql_fetch_object($qKdU)){
  		$no+=1;
		echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickKdU('".$aKdU->kodeorganisasi."')><td>".$no."</td>
		    <td>".$aKdU->kodeorganisasi."</td>
			<td>".$aKdU->namaorganisasi."</td>
			<td>".$aKdU->induk."</td>
		</tr>";	
		}
	}
  		 		
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";	
}
?>