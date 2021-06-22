<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
//Numrows perpage==20;

$no=0;
$getrows=20;
//default query
/*if($_POST['page'])
   $page=$_POST['page'];
else
   $page=1; */

if($_POST['page'])
   $page=$_POST['page'];
else
   $page=1;
$limit=20;
$maxdisplay=($page*$getrows-$limit);

$schdata=$_POST['schdata'];
$schuser=$_POST['schuser'];
$schtahun=$_POST['schtahun'];
$schbulan=$_POST['schbulan'];
//exit("Error:$thnkel");
//echo $schjk;
$slcdt = $eksi->sSQL("select id,tbl_name from sdm_5tab_datakaryawan");

foreach($slcdt as $bardt){
	$tbl_name[$bardt['id']]=$bardt['tbl_name'];
}
$where="";
if ($schuser!='')
	$where.="and updateby='".$schuser."'";

if ($schtahun!='')
	$where.="and date_format(STR_TO_DATE(updatetime,'%Y'),'%Y')=date_format(STR_TO_DATE('".$schtahun."','%Y'),'%Y')";


if ($schbulan!='')
	$where.="and date_format(STR_TO_DATE(updatetime,'%m'),'%m')=date_format(STR_TO_DATE('".$schbulan."','%m'),'%m')";

$slckamus="select karyawanid,namakaryawan,nik from datakaryawan";
$reskamus=$eksi->sSQL($slckamus);

foreach($reskamus as $barkamus){
	$namakry[$barkamus['karyawanid']]=$barkamus['namakaryawan'];
	$nikkry[$barkamus['karyawanid']]=$barkamus['nik'];
}

/*$slcjbt="select kodejabatan,namajabatan from sdm_5jabatan";
$resjbt=$eksi->sSQL($slcjbt);

foreach($resjbt as $barjbt){
	$kamusjbt[$barjbt['kodejabatan']]=$barjbt['namajabatan'];
}*/
//echo "warning: ".$schdata;
$no=($page-1)*$limit;
switch($schdata){
	case 1:
		 /*$strx="select count(*) as jlh from log_datakaryawan where 1=1 ".$where."  ";  

		//==================jlh karyawan
		$jlhkar=0;
		$resx=$eksi->sSQL($strx);
		foreach($resx as $barx)
		{
			$jlhkar=$barx['jlh'];
		}
		
		$no=$page*$maxdisplay;
		if($jlhkar==0)
		{
			echo"<tr><td colspan=2>Data ".$_SESSION['lang']['tidakditemukan']."</td></tr>";	
		}
		if($jlhkar!=0)
		{
			echo"<tr><td colspan=2>Total: ".$jlhkar." Data</td></tr>";	
		}*/
		
		 $str="select * from log_datakaryawan where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_datakaryawan";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_datakaryawan where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_datakaryawan'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <!--<td>".$barkry['karyawanid']."</td>-->
				 <td>".$barkry['nik']."</td>
				 <td>".$barkry['namakaryawan']."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		 
		break;
		
	case 2:
		$str="select * from log_sdm_karyawancv where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_karyawancv";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_sdm_karyawancv where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_karyawancv'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$slcemp."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		
		}
		break;
		
	case 3:
		$str="select * from log_sdm_karyawanpendidikan where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_karyawanpendidikan";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_sdm_karyawanpendidikan where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_karyawanpendidikan'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		break;
		
	case 4:
		$str="select * from log_sdm_karyawantraining where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_karyawantraining";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_sdm_karyawantraining where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_karyawantraining'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		break;
		
	case 5:
		$str="select karyawanid, keluargaid, nama, jeniskelamin, tempatlahir, tanggallahir, hubungankeluarga, `status`, levelpendidikan, pekerjaan, telp, email, tanggungan, updateby, updatetime, action  from log_sdm_karyawankeluarga where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_karyawankeluarga";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-4;
				$slcemp="select karyawanid, keluargaid, nama, jeniskelamin, tempatlahir, tanggallahir, hubungankeluarga, `status`, levelpendidikan, pekerjaan, telp, email, tanggungan, updateby, updatetime, action from  log_sdm_karyawankeluarga where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' and keluargaid='".$barkry['keluargaid']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_karyawankeluarga' and column_name<>'nomor'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name']."(".$barkry[$i].",".$baremp[$i]."), ";
										}
										else {
											$edit.=$barcol['column_name']."(".$barkry[$i].",".$baremp[$i]."), ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']]."(".$barkry[$i].",".$baremp[$i]."), ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']]."(".$barkry[$i].",".$baremp[$i]."), ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		break;
		
	case 6:
		$str="select * from log_sdm_karyawanalamat where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_karyawanalamat";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_sdm_karyawanalamat where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_karyawanalamat'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		break;
		
	case 7:
		$str="select * from log_sdm_riwayatjabatan where 1=1 ".$where." order by updatetime desc limit ".$maxdisplay.",".$getrows;
		 $slckry = $eksi->sSQL($str);
		foreach($slckry as $barkry){
			
			$no+=1;
			$tambah='';
			$edit='';
			$hapus='';
			if ($barkry['action']==0){
				$tambah=$_SESSION['lang']['addingdata'];
			}
			else if ($barkry['action']==1){
				
				$slcol = "DESCRIBE log_sdm_riwayatjabatan";
				$numcol = $eksi->sSQLnum($slcol);
				$numcols=$numcol-3;
				$slcemp="select * from  log_sdm_riwayatjabatan where karyawanid='".$barkry['karyawanid']."' and updatetime < '".$barkry['updatetime']."' order by updatetime desc LIMIT 1";
				$resemp=$eksi->sSQL($slcemp);
				foreach($resemp as $baremp){
					for ($i=1;$i<$numcols;$i++){
						if ($barkry[$i]!=$baremp[$i]){		
							$slcol="SELECT column_name FROM information_schema.columns WHERE table_name='log_sdm_riwayatjabatan'";
							$rescol=$eksi->sSQL($slcol);
							$x=0;
							foreach ($rescol as $barcol){
								if ($x==$i){
									if ($_SESSION['lang'][$barcol['column_name']]==''){
										if ($baremp[$i]==''){
											$tambah.=$barcol['column_name'].", ";
										}
										else {
											$edit.=$barcol['column_name'].", ";
										}
										
									}
									else {
										if ($baremp[$i]==''){
											$tambah.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										else {
											$edit.=$_SESSION['lang'][$barcol['column_name']].", ";
										}
										
									}
									
								}
								$x++;
							}
						}
					}
				}
				
			}
			else if($barkry['action']==2){
				$hapus=$_SESSION['lang']['deletingdata'];;
			}
			$edit=substr($edit,0,strlen($edit)-2);
			
			
			echo "<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$nikkry[$barkry['karyawanid']]."</td>
				 <td>".$namakry[$barkry['karyawanid']]."</td>
				 <td>".$edit."</td>
				 <td>".$tambah."</td>
				 <td>".$hapus."</td>
				 <td>".$namakry[$barkry['updateby']]." (".$barkry['updateby'].")</td>
				 <td>".$barkry['updatetime']."</td>";
		}
		break;
		
	default:
		
		break;
	
}

	
//}
?>
