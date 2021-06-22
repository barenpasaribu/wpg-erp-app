<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/eagrolib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
require_once('lib/eksilib.php');

$tipetransaksi=$_POST['tipetransaksi'];
if($_POST['tanggalsk']==''){
	$tanggalsk = '0000-00-00';
} 
else{
	$tanggalsk = date('Y-m-d', strtotime($_POST['tanggalsk']));
}
if($_POST['tanggalberlaku']==''){
	$tanggalberlaku = '0000-00-00';
}
else{
	$tanggalberlaku = date('Y-m-d', strtotime($_POST['tanggalberlaku']));
}

$oldlokasitugas=$_POST['oldlokasitugas']; 
$newlokasitugas=$_POST['newlokasitugas']; 
$oldkodejabatan=$_POST['oldkodejabatan']; 
$newkodejabatan=$_POST['newkodejabatan']; 
$penandatangan=$_POST['penandatangan']; 
$action=0;

$karyawanid=$_POST['karyawanid'];

			  


if($karyawanid!='' or $_POST['del']=='true' or isset($_POST['queryonly']))
{
	
	if(isset($_POST['del']) and $_POST['del']=='true')
	{
		if ($_POST['nomorsk']!='' && $_POST['nomorsk']!=null){
		
			$str="delete from ".$dbname.".sdm_riwayatjabatan where nomorsk='".$_POST['nomorsk']."'";
			$action=2;
		}
		
	}
	else if(isset($_POST['queryonly']))
	{
		$str="select 1=1";
	}
	else
	{
		//get number
		$potSK=substr($_SESSION['empl']['lokasitugas'],0,4).strtoupper(substr($tipetransaksi,0,2)).substr($tanggalsk,0,4);
		$str="select nomorsk from ".$dbname.".sdm_riwayatjabatan
			  where  nomorsk like '".$potSK."%'
			  order by nomorsk desc limit 1";  
		$notrx=0;
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$notrx=substr($bar->nomorsk,10,5);
		}
		$notrx=intval($notrx);
		$notrx=$notrx+1;
		$notrx=str_pad($notrx, 5, "0", STR_PAD_LEFT);
		$notrx=$potSK.$notrx;
		$str="insert into ".$dbname.".sdm_riwayatjabatan (
			  `karyawanid`,`nomorsk`,`tanggalsk`,
			  `mulaiberlaku`,`darikodeorg`,
			  `tipesk`,`kekodeorg`,`darikodejabatan`, `kekodejabatan`, `namadireksi`,  `updateby`
			  ) values(
			   '".$karyawanid."','".$notrx."','".$tanggalsk."',
			   '".$tanggalberlaku."','".$oldlokasitugas."','".$tipetransaksi."',
			   '".$newlokasitugas."','".$oldkodejabatan."','".$newkodejabatan."','".$penandatangan."', '".$_SESSION['standard']['userid']."'    
			  )";
		$action=1;
		 
	}
	if(mysql_query($str))
	   {
		//selalu insert ke tabel log jika ada update/insert/delete
		$strlog="insert into ".$dbname.".log_sdm_riwayatjabatan (
			  `karyawanid`,`nomorsk`,`tanggalsk`,
			  `mulaiberlaku`,`darikodeorg`,
			  `tipesk`,`kekodeorg`,`darikodejabatan`, `kekodejabatan`, `namadireksi`,  `updateby`,  `action`
			  ) values(
			   '".$karyawanid."','".$notrx."','".$tanggalsk."',
			   '".$tanggalberlaku."','".$oldlokasitugas."','".$tipetransaksi."',
			   '".$newlokasitugas."','".$oldkodejabatan."','".$newkodejabatan."','".$penandatangan."', '".$_SESSION['standard']['userid']."', ".$action."    
			  )";
		$eksi->exc($strlog);
			
		 $str="select karyawanid, nomorsk, tipesk, tanggalsk, mulaiberlaku, darikodeorg, kekodeorg, darikodejabatan, kekodejabatan, namadireksi
		 from ".$dbname.".sdm_riwayatjabatan where karyawanid=".$karyawanid." order by nomorsk";
		 $res=mysql_query($str);
		 $no=0;
		 while($bar=mysql_fetch_object($res))
		 {
			$no+=1;
			/*$strjb="select kodejabatan,namajabatan from ".$dbname.".sdm_5jabatan";
			 $resjb=mysql_query($strjb);
			 while($barjb=mysql_fetch_object($resjb))
			 {
					$kamusjabatan[$barjb->kodejabatan]=$barjb->namajabatan;

			 }*/
			echo"	  <tr class=rowcontent>
				  <td class=firsttd>".$no."</td>
				  <td>".$bar->tipesk."</td>			  
				  <td>".$bar->tanggalsk."</td>
				  <td>".$bar->mulaiberlaku."</td>			  
				  <td>".$bar->darikodeorg."</td>			  
				  <td>".$bar->kekodeorg."</td>				  
				  <td>".$bar->darikodejabatan."</td>			  
				  <td>".$bar->kekodejabatan."</td>
				  <td>".$bar->namadireksi."</td>
				  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delMPD('".$karyawanid."','".$bar->nomorsk."');\"></td>
				</tr>
				";
		 	 	
		 }
	    }
		else
		{
			echo " Gagal:".addslashes(mysql_error($conn)).$str;
		}
}
else
{
	echo" Error: Incorrect Period";
}
?>
