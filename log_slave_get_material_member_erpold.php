<?php 
require_once('master_validation.php');
require_once('config/connection.php');
$kelompok	= $_POST['mayor'];
$kodebarang = $_POST['kodebarang'];
$namabarang = strtoupper($_POST['namabarang']);
$satuan     = $_POST['satuan'];
$minstok    = $_POST['minstok'];
$konversi   = $_POST['konversi'];
$nokartu    = $_POST['nokartu'];
$organisasi = $_POST['organisasi'];
$merk 		= $_POST['merk'];
$kodeorder 	= $_POST['kodeorder'];
$partnumber = $_POST['partnumber'];
$inv_code 	= $_POST['inv_code'];
$keterangan = $_POST['keterangan'];
$method	    = $_POST['method'];
$strx		= 'select 1=1';

$kdbrg = $_POST['kdbrg'];
$spec  = $_POST['spec'];
$link1 = $_POST['link1'];
$link2 = $_POST['link2'];
$link3 = $_POST['link3'];
$link4 = $_POST['link4'];
$file1 = $_POST['file1'];
$file2 = $_POST['file2'];
$file3 = $_POST['file3'];


switch($method){
	case 'delete':	
		$str = "SELECT * FROM ".$dbname.".log_5photobarang WHERE kodebarang='".$kodebarang."'";
		$sql = mysql_query($str);
		$res = mysql_fetch_assoc($sql);	
		
		unlink($res['depan']);
		unlink($res['samping']);
		unlink($res['atas']);
				
		$strx="delete from ".$dbname.".log_5masterbarang where kodebarang='".$kodebarang."' and kelompokbarang='".$kelompok."'";
		if(!mysql_query($strx)){
		  echo $_SESSION['lang']['alertfail']." : ".addslashes(mysql_error($conn));
		}	
		
	break;
	case 'update':
		$strx="update ".$dbname.".log_5masterbarang set namabarang='".$namabarang."',satuan='".$satuan."',minstok=".$minstok.",nokartubin='".$nokartu."', konversi=".$konversi.",kodeorder='".$kodeorder."', merk='".$merk."', partnumber='".$partnumber."', inv_code='".$inv_code."', keterangan='".$keterangan."' where kelompokbarang='".$kelompok."' and kodebarang='".$kodebarang."'";
		if(!mysql_query($strx)){
		  echo $_SESSION['lang']['alertfail']." : ".addslashes(mysql_error($conn));
		}			
	break;	
	case 'insert':
		$strx="insert into ".$dbname.".log_5masterbarang(
		kelompokbarang,kodebarang,namabarang,satuan,minstok,nokartubin,konversi,kodeorder,merk,partnumber,rak,keterangan,inv_code)
		values(
		'".$kelompok."','".$kodebarang."',	'".$namabarang."','".$satuan."',".$minstok.",'".$nokartu."',".$konversi.",	'".$kodeorder."','".$merk."','".$partnumber."','','".$keterangan."','".$inv_code."')";	   
		if(!mysql_query($strx)){
		  echo $_SESSION['lang']['alertfail']." : ".addslashes(mysql_error($conn));
		}		
	break;	
	case 'refresh_data':
		$limit=10;
		$page=0;
		if(isset($_POST['page'])){
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
	$offset=$page*$limit;		
	$txtfind=trim($_POST['txtcari']);
	$filter=trim($_POST['filter']);
 

	if(isset($txtfind) && ($txtfind!='') && ($filter!='all')){		
		$where = " where ".$filter." like '%".$txtfind."%'";
	} 
	else {
		$where = " ";
	}
	
	$str="select * from ".$dbname.".log_5masterbarang ".$where." order by namabarang asc limit ".$offset.",".$limit." ";	
	$sql="select count(kodebarang) as jmlhrow from ".$dbname.".log_5masterbarang ".$where."";		
	$res=mysql_query($str);
	$no=($page*$limit);
	$jlhbrs = 0;
	$query=mysql_query($sql) or die(mysql_error());
	while($jsl=mysql_fetch_object($query)){
	$jlhbrs= $jsl->jmlhrow;
	}	
	while($bar=mysql_fetch_object($res)){
	  $stru="select * from ".$dbname.".log_5photobarang where kodebarang='".$bar->kodebarang."'";
	  $rows = mysql_fetch_object(mysql_query($stru));
	  
	  if(mysql_num_rows(mysql_query($stru))>0){
	  	//$adx="<img src=images/zoom.png class=resicon height=16px title='".$_SESSION['lang']['view']."' onclick=viewDetailbarang('".$bar->kodebarang."',event)>";
		$adx ="<img src=images/tool.png class=resicon height=16px title='".$_SESSION['lang']['edit']."' onclick=editimg('".$rows->kodebarang."','".$rows->depan."','".$rows->samping."','".$rows->atas."','".$rows->spesifikasi."','".$rows->link1."','".$rows->link2."','".$rows->link3."','".$rows->link4."');>";
		$adx.=" <img src=images/zoom.png class=resicon height=16px title='".$_SESSION['lang']['view']."' onclick=viewDetailbarang('".$bar->kodebarang."',event)>";	
	  }
	  else{
	  	$adx="<img src=images/tool.png class=resicon height=16px title='".$_SESSION['lang']['edit']."' onclick=uploadfoto('".$bar->kodebarang."')>";
	  }  
	  $no+=1;
	
		echo"<tr class=rowcontent>
		  <td>".$no."</td>
		  <td>".$bar->kelompokbarang."</td>
		  <td>".$bar->kodebarang."</td>
		  <td>".$bar->namabarang."</td>
		  <td>".$bar->satuan."</td>
		  <td>".$bar->kodeorder."</td>
		  <td>".$bar->merk."</td>		  
		  <td>".$bar->partnumber."</td>	
		  <td>".$bar->keterangan."</td>	
		  <td align=right>".$bar->minstok."</td>
		  <td>".$bar->nokartubin."</td>
		  <td align=center>".$bar->inv_code."</td>
		  <td align=center><input type=checkbox id='br".$bar->kodebarang."' value='".$bar->kodebarang."' ".($bar->inactive==0?"":" checked")." onclick=setInactive(this.value);></td>		  
		  <td>
			  ".$adx."
		      <img src=images/application/application_edit.png class=resicon  title='".$_SESSION['lang']['edit']."' onclick=\"fillField('".$bar->kelompokbarang."','".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."',".$bar->minstok.",'".$bar->nokartubin."',".$bar->konversi.",".$bar->inactive.",'".$bar->kodeorder."','".$bar->merk."','".$bar->partnumber."','".$bar->inv_code."','".$bar->keterangan."');\"> 
			  <!-- <img src=images/application/application_delete.png class=resicon  title='".$_SESSION['lang']['delete']."' onclick=\"delBarang('".$bar->kodebarang."','".$bar->kelompokbarang."');\"> -->
		  </td>
		  </tr>";
	}
echo "<tr style='color:#fff'><td colspan=14 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td>
	</tr>";			
	break;	
	
	case 'saveimage':
		 $str = "INSERT INTO ".$dbname.".`log_5photobarang`(`kodebarang`, `depan`, `samping`, `atas`, `spesifikasi`, `link1`, `link2`, `link3`, `link4`) VALUES ('".$kdbrg."','".$file1."','".$file2."','".$file3."','".$spec."','".$link1."','".$link2."','".$link3."','".$link4."')";
		 mysql_query($str);		
		 echo $_SESSION['lang']['alertinsert1'];
	break;
	
	case 'updateimage':
		$strx="update ".$dbname.".log_5photobarang set depan='".$file1."',samping='".$file2."',atas='".$file3."', spesifikasi='".$spec."',link1='".$link1."', link2='".$link2."', link3='".$link3."', link4='".$link4."' where kodebarang='".$kdbrg."'";
		if(!mysql_query($strx)){
		  echo $_SESSION['lang']['alertfail']." : ".addslashes(mysql_error($conn));
		}			
		else {
		  echo $_SESSION['lang']['alertupdate'];
		}
	break;
	default:
	break;	
}
?>
