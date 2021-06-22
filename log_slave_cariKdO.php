<?php
/*
AUTO COMPLETE FOR KODE ORGANISASI
*/
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if(!empty($_POST['KdO']) || isset($_POST['KdO'])) {
	$KdO = $_POST['KdO'];
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
	$sKdO = "select * from ".$dbname.".organisasi where kodeorganisasi like '%".$KdO."%' ".$whrorg."";
	
  	$qKdO = mysql_query($sKdO);
  	$no	  = 0;

	if(mysql_num_rows($qKdO) == 0) {
		echo"
		<tr class=rowcontent>
			<td colspan=4 align=center>No Data</td>
		</tr>";	
	} else {
		while($aKdO = mysql_fetch_object($qKdO)){
  		$no+=1;
		echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickKdO('".$aKdO->kodeorganisasi."')><td>".$no."</td>
		    <td>".$aKdO->kodeorganisasi."</td>
			<td>".$aKdO->namaorganisasi."</td>
			<td>".$aKdO->induk."</td>
		</tr>";	
		}
	}
  		 		
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";	
}
?>