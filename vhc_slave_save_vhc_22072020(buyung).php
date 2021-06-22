<?
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kelompokvhc=$_POST['kelompokvhc'];
$jenisvhc=$_POST['jenisvhc'];
$kodeorg=$_POST['kodeorg'];
$method=$_POST['method'];
$kodevhc=str_replace(" ","",$_POST['kodevhc']);
$tahunperolehan=$_POST['tahunperolehan'];
$noakun=$_POST['noakun'];
$beratkosong=$_POST['beratkosong'];
$nomorrangka=$_POST['nomorrangka'];
$nomormesin=$_POST['nomormesin'];
$detailvhc=$_POST['detailvhc'];
$kodebarang=$_POST['kodebarang'];
$kepemilikan=$_POST['kepemilikan'];
$kodetraksi=$_POST['kodetraksi'];
$tglakhirstnk=tanggalsystem($_POST['tglakhirstnk']);
$tglakhirkir=tanggalsystem($_POST['tglakhirkir']);
$tglakhirijinbm=tanggalsystem($_POST['tglakhirijinbm']);
$tglakhirijinang=tanggalsystem($_POST['tglakhirijinang']);

if($beratkosong=='')
  $beratkosong=0;
        
$strx="select 1=1";
switch($method){
    case 'delete':
            $strx="delete from ".$dbname.".vhc_5master where kodevhc='".$kodevhc."'";
    break;
    case 'update':
       $strx="update ".$dbname.".vhc_5master set jenisvhc='".$jenisvhc."',
              kelompokvhc='".$kelompokvhc."', 
              kodeorg='".$kodeorg."', tahunperolehan='".$tahunperolehan."',
              beratkosong='".$beratkosong."', nomorrangka='".$nomorrangka."' ,
                      nomormesin='".$nomormesin."',detailvhc='".$detailvhc."',
                      kodebarang='".$kodebarang."',kepemilikan=".$kepemilikan.",
                      kodetraksi='".$kodetraksi."', tglakhirstnk='".$tglakhirstnk."',
                      tglakhirkir='".$tglakhirkir."',tglakhirijinbm='".$tglakhirijinbm."',
                      tglakhirijinang='".$tglakhirijinang."'
                      where kodevhc='".$kodevhc."'";
//        exit('error: '.$strx);
    break;	
    case 'insert':
            $strx="insert into ".$dbname.".vhc_5master(
                   kodevhc,kelompokvhc,kodeorg,jenisvhc,
                       tahunperolehan,beratkosong,nomorrangka,
                       nomormesin,detailvhc,kodebarang,kepemilikan,kodetraksi,
                       tglakhirstnk,tglakhirkir,tglakhirijinbm,tglakhirijinang)
            values('".$kodevhc."','".$kelompokvhc."',
                   '".$kodeorg."','".$jenisvhc."',".$tahunperolehan.",
                       ".$beratkosong.",'".$nomorrangka."','".$nomormesin."',
                       '".$detailvhc."','".$kodebarang."',".$kepemilikan.",
                       '".$kodetraksi."','".$tglakhirstnk."','".$tglakhirkir."',
                       '".$tglakhirijinbm."','".$tglakhirijinang."')";
    break;
    case'deactive':
        if($_POST['status']==1){
            $_POST['status']=0;
        }else{
            $_POST['status']=1;
        }
          $strx="update ".$dbname.".vhc_5master set status='".$_POST['status']."' 
                 where kodevhc='".$_POST['kodevhc']."'";
        
    break;
    default:
break;	

	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	

$where='1=1';
if($kodeorg!='')
   $where.=" and kodeorg='".$kodeorg."' ";
if($kelompokvhc!='')
   $where.=" and kelompokvhc='".$kelompokvhc."' ";   
if($jenisvhc!='')
   $where.=" and jenisvhc='".$jenisvhc."' ";
   
if($_SESSION['empl']['lokasitugas']=='MJHO'){
    $str="select * from ".$dbname.".vhc_5master where ".$where." 
      order by status desc,kodeorg,kodevhc asc";
}
else{
    $str="select * from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' and ".$where." 
      order by status desc,kodeorg,kodevhc asc";
}

$res=mysql_query($str);
//echo $str.mysql_error($conn);
	$no=0;
	while($bar1=mysql_fetch_object($res))
	{
		$no+=1;
		$str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";
		$res1=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res1))
		{
			$namabarang=$bar->namabarang;
		}
		if($bar1->kepemilikan==1)
		{
	      $dptk=$_SESSION['lang']['miliksendiri'];	
		}
		else
		{
			$dptk=$_SESSION['lang']['sewa'];
		}
                    $sttd="";
                    $sttd="Deactivate";
                    $bgcrcolor="class=rowcontent";
               if($bar1->status==0){
                    $bgcrcolor="bgcolor=orange";
                    $sttd="";
                    $sttd="Actived";
                }
                 $clidt=" style='cursor:pointer' title='".$sttd." ".$bar1->kodevhc."' onclick=deAktif('".$bar1->kodevhc."','".$bar1->status."')";
		echo"<tr ".$bgcrcolor.">
		     <td  ".$clidt." >".$no."</td>
		     <td  ".$clidt." >".$bar1->kodeorg."</td>
			 <td  ".$clidt." >".$bar1->kelompokvhc."</td>				 
			 <td ".$clidt." >".$bar1->jenisvhc."</td>			 		
			 <td ".$clidt." >".$bar1->kodevhc."</td>
			 <td ".$clidt." >".$namabarang."</td>
			 <td ".$clidt." >".$bar1->tahunperolehan."</td>
			 <input type=hidden value=".$bar1->beratkosong.">		
			 <input type=hidden value=".$bar1->nomorrangka.">	
			 <td ".$clidt." >".$bar1->nomormesin."</td> 
			 <td ".$clidt." >".$bar1->detailvhc."</td> 	
			 <td ".$clidt." >".$dptk."</td> 
                         <td ".$clidt." >".$bar1->kodetraksi."</td>
			 <td>
			      <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillMasterField('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."','".$bar1->beratkosong."',
                                 '".$bar1->nomorrangka."','".$bar1->nomormesin."','".$bar1->tahunperolehan."','".$bar1->kodebarang."','".$bar1->kepemilikan."','".$bar1->kodetraksi."','".tanggalnormal($bar1->tglakhirstnk)."','".tanggalnormal($bar1->tglakhirkir)."',
                                 '".tanggalnormal($bar1->tglakhirijinbm)."','".tanggalnormal($bar1->tglakhirijinang)."');\">
			      <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"deleteMasterVhc('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."');\">
			 </td></tr>";
	}	   
?>
