<?php
/*
AUTO COMPLETE FOR KODE ORGANISASI
*/
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$Type = isset($_POST['type']) ? $_POST['type'] : null;
$NamaKaryawan = isset($_POST['NamaKaryawan']) ? $_POST['NamaKaryawan'] : null;

switch($Type){
	case 'ListKaryawan':
	echo "<table cellspacing=1 border=0 class=data>
    	<thead>
		<tr class=rowheader><td>No</td>
		    <td>".$_SESSION['lang']['nik']."</td>
			<td>".$_SESSION['lang']['namakaryawan']."</td>
		</tr>
		</thead>
		</tbody>";
	$String = "select * from ".$dbname.".datakaryawan where lokasitugas like '".$_SESSION['org']['kodepusat']."'";
  	$Query = mysql_query($String);
  	$no	  = 0;

	if(mysql_num_rows($Query) == 0) {
		echo"
		<tr class=rowcontent>
			<td colspan=3 align=center>No Data</td>
		</tr>";	
	} else {
		while($Row = mysql_fetch_object($Query)){
  		$no+=1;
		echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=PickKaryawan('".$Row->karyawanid."')><td>".$no."</td>
		    <td>".$Row->nik."</td>
			<td>".$Row->namakaryawan."</td>
		</tr>";	
		}
	}
  		 		
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";		
	break;
	case 'CariKaryawan':
	echo "<table cellspacing=1 border=0 class=data>
    	<thead>
		<tr class=rowheader><td>No</td>
		    <td>".$_SESSION['lang']['nik']."</td>
			<td>".$_SESSION['lang']['namakaryawan']."</td>
		</tr>
		</thead>
		</tbody>";
	$String = "select * from ".$dbname.".datakaryawan where kodeorganisasi like '".$_SESSION['org']['kodepusat']."' and namakaryawan like '%".$NamaKaryawan."%'";
  	$Query = mysql_query($String);
  	$no	  = 0;

	if(mysql_num_rows($Query) == 0) {
		echo"
		<tr class=rowcontent>
			<td colspan=3 align=center>No Data</td>
		</tr>";	
	} else {
		while($Row = mysql_fetch_object($Query)){
  		$no+=1;
		echo"
		<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=PickKaryawan('".$Row->karyawanid."')><td>".$no."</td>
		    <td>".$Row->nik."</td>
			<td>".$Row->namakaryawan."</td>
		</tr>";	
		}
	}
  		 		
	echo"</tbody>
	     <tfoot>
		 </tfoot>
		 </table>";		
	break;
	default:
	  $strx="select 1=1";
	break;	
}
/*if(!empty($_POST['KdU']) || isset($_POST['KdU'])) {
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
	$sKdU = "select * from ".$dbname.".organisasi where kodeorganisasi like '%".$KdU."%' AND tipe = 'GUDANGTEMP'";
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
}*/
?>