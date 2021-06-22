<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$tglGanti=tanggalsystem_hrd($_POST['tglGanti']);
$kdJenis=$_POST['kdjenis'];
$usr_id=$_SESSION['standard']['userid'];
$notransaksi=$_POST['notrans'];
$codeOrg=$_POST['codeOrg'];
$descDmg=$_POST['descDmg'];
$dwnTime=$_POST['dwnTime'];
$statInp=$_POST['statInp'];
//print_r($_POST);
//exit("Error");

	switch($proses)
	{
		case'generate_no':
		//lokasi tugas/y/m/no urut (4)
		if($notransaksi!='')
		{
			$svhc="select kodevhc,jenisvhc,tahunperolehan from ".$dbname.".vhc_5master where kodeorg='".$codeOrg."'"; //echo $svhc;
			$qvhc=mysql_query($svhc) or die(mysql_error());
			while($rvhc=mysql_fetch_assoc($qvhc))
			{
			$optVhc.="<option value='".$rvhc['kodevhc']."' ".($rvhc['kodevhc']==$kdJenis?'selected':'').">".$rvhc['kodevhc']."[".$rvhc['tahunperolehan']."]</option>"; 	
			}
	
			echo $optVhc."###".$notransaksi;
		}
		else
		{	
			$tgl=  date('Ymd');
			$bln = substr($tgl,4,2);
			$thn = substr($tgl,0,4);
			
			$notransaksi=$codeOrg."/".date('Y')."/".date('m')."/";
			$ql="select `notransaksi` from ".$dbname.".`vhc_penggantianht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
			$qr=mysql_query($ql) or die(mysql_error());
			$rp=mysql_fetch_object($qr);
			$awal=substr($rp->notransaksi,-4,4);
			$awal=intval($awal);
			$cekbln=substr($rp->notransaksi,-7,2);
			$cekthn=substr($rp->notransaksi,-12,4);
			//echo "warning:".$awal;exit();
			if(($bln!=$cekbln)&&($thn!=$cekthn))
			{
			//echo $awal; exit();
					$awal=1;
			}
			else
			{
				  
					$awal++;
					// echo"warning:masuk".$awal;exit();
			}
			$counter=addZero($awal,4);
			$notransaksi=$codeOrg."/".$thn."/".$bln."/".$counter;
			
			
			$svhc="select kodevhc,jenisvhc,tahunperolehan from ".$dbname.".vhc_5master where kodeorg='".$codeOrg."'"; //echo $svhc;
			$qvhc=mysql_query($svhc) or die(mysql_error());
			while($rvhc=mysql_fetch_assoc($qvhc))
			{
					$optVhc.="<option value='".$rvhc['kodevhc']."'>".$rvhc['kodevhc']."[".$rvhc['tahunperolehan']."]</option>"; 
			}
	
			echo $optVhc."###".$notransaksi;
		}
		break;
		
		case'load_data':
		OPEN_BOX_HRD();
		//echo"<pre>";
//		print_r($_SESSION);[EMPL][tipeinduk] => KANWIL

//		echo"</pre>";
		 echo"<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>";
			echo"
			<table cellspacing=1 border=0 class=sortable>
		<thead>
<tr class=rowheader>
<td>".$_SESSION['lang']['notransaksi']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['kodevhc']."</td>
<td>".$_SESSION['lang']['jenisvch']."</td>
<td>".$_SESSION['lang']['downtime']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		if(($_SESSION['empl']['tipeinduk']=='KANWIL')||($_SESSION['empl']['tipeinduk']=='HOLDING'))
		{
			$cond.=" order by `tanggal` desc";
		}
		else
		{
			$cond.=" where updateby='".$_SESSION['standard']['userid']."' order by `tanggal` desc";
		}
		$sql2="select count(*) as jmlhrow from ".$dbname.".vhc_penggantianht ".$cond."";
		//	echo "warning".$sql2;exit();
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$slvhc="select * from ".$dbname.".vhc_penggantianht ".$cond." limit ".$offset.",".$limit."";
	
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$pvhc="select kodevhc,jenisvhc from ".$dbname.".vhc_5master where kodevhc='".$rlvhc['kodevhc']."'";
		$qpvhc=mysql_query($pvhc) or die(mysql_error());
		$rpvhc=mysql_fetch_assoc($qpvhc);
		echo"
					<tr class=rowcontent>
					<td>". $rlvhc['notransaksi']."</td>
					<td>". tanggalnormal_hrd($rlvhc['tanggal'])."</td>
					<td>". $rlvhc['kodevhc']."</td>
					<td>". $rpvhc['jenisvhc']."</td>
					<td>". $rlvhc['downtime']."</td>";
					if($rlvhc['posting']=='0')
					{
					echo
					"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('". $rlvhc['kodeorg']."','". $rlvhc['notransaksi']."','".tanggalnormal_hrd($rlvhc['tanggal'])."','". $rlvhc['kodevhc']."','". $rlvhc['posting']."','". $rlvhc['downtime']."','". $rlvhc['kerusakan']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $rlvhc['notransaksi']."','". $rlvhc['kodevhc']."');\" >	
					<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>
					</tr>";}
					else
					{
						echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>";
					}
                }
					echo"
					<tr><td colspan=5 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					echo"</table></fieldset>";
					CLOSE_BOX_HRD();
		break;
		case'delete':
		$sql="delete from ".$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."'";
		if(mysql_query($sql))
		{
			$sql2="delete from ".$dbname.".vhc_penggantiandt where notransaksi='".$notransaksi."'";
			mysql_query($sql2);
		}
		else
		{
			echo "DB Error : ".mysql_error($conn);
		}
		break;
		case'cari_barang':
			$txtcari=$_POST['txtcari'];
		$str="select a.kodebarang,a.namabarang,a.satuan from ".$dbname.".log_5masterbarang a where a.namabarang like '%".$txtcari."%' or a.kodebarang like '%".$txtcari."' and kelompokbarang in (331,332,333,334,335,336,338,341,342,375)";
			 // echo $str;
		$res=mysql_query($str);

if(mysql_num_rows($res)<1)
{
	echo"Error: ".$_SESSION['lang']['tidakditemukan'];			
}
else
{
		echo"
		<fieldset>
		<legend>".$_SESSION['lang']['result']."</legend>
		<div style=\"width:450px; height:300px; overflow:auto;\">
			<table class=sortable cellspacing=1 border=0>
		     <thead>
			      <tr class=rowheader>
				      <td>No</td>
					  <td>".$_SESSION['lang']['kodebarang']."</td>
					  <td>".$_SESSION['lang']['namabarang']."</td>
					  <td>".$_SESSION['lang']['satuan']."</td>
				  </tr>
		     </thead>
			 <tbody>";
			$no=0;	 
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"throwThisRow('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."');\">
				   <td>".$no."</td>
				  <td>".$bar->kodebarang."</td>
				  <td>".$bar->namabarang."</td>
				  <td>".$bar->satuan."</td>
			      </tr>";			   	
			}
		echo    "
				 </tbody>
				 <tfoot></tfoot>
				 </table></div></fieldset>";	
}  
		break;
		/*$txtcari=$_POST['txtcari'];
		$str="select a.kodebarang,a.namabarang,a.satuan from
		".$dbname.".log_5masterbarang a where kelompokbarang='351' and  a.kodebarang ='".$txtcari."'";
			 // echo $str;
		$res=mysql_query($str) or die(mysql_error());
		while($rslt=mysql_fetch_assoc($res))
		{
			$where=" kodebarang='".$rslt['kodebarang']."' and darisatuan='".$rslt['satuan']."'";
			$sknv="select satuankonversi from ".$dbname.".log_5stkonversi where ".$where." ";
			$qknv=mysql_query($sknv) or die(mysql_error());
			 $optSatuan.="<option value='".$rslt['satuan']."'>".$rslt['satuan']."</option>";
			while($rknv=mysql_fetch_assoc($qknv))
			{
				$optSatuan.="<option value='".$rknv['satuankonversi']."'>".$rknv['satuankonversi']."</option>";
			}
		}
		echo $optSatuan;*/
		break;
		case'cek_entry_jenis_vhc':
		//Untuk Cek Kode Vehicle, menghindari error menginput satu kode vehicle dihari yang sama
		
		$sql_cek="select * from ".$dbname.".vhc_penggantianht where tanggal ='".$tglGanti."' and kodevhc='".$kdJenis."'";
		//echo "warning:".$sql_cek;
		$query_cek=mysql_query($sql_cek) or die(mysql_error());
		$res=mysql_fetch_row($query_cek);
		
		if($res>0)
		{
			echo 'warning:Vehicle, Already Input in These Days';
			exit ();
		}
		if(($codeOrg=='')||($tglGanti=='')||($dwnTime=='')||($descDmg==''))
		{
			echo 'warning:Please Complete The Form';
			exit ();
		}
		
			
		
		 break;
		case 'cek_data_header' :
		if(($notransaksi!='')||($tglGanti!='')||($dwnTime!='')||($descDmg!=''))
		{
		
                    $sql="select * from ".$dbname.".vhc_penggantianht where notransaksi='".$_POST['notrans']."'";
                    $query=mysql_query($sql) or die(mysql_error());
			$row=mysql_fetch_row($query);
			//echo "warning:masuk <pre>".print_r($row)."</pre>";
			if($row<1)
			{
				foreach($_POST['kdbrg'] as $brs => $isi)
				{
					$kodebarang=$isi;
					$satuan=$_POST['satuan'][$brs];
					$jumlah=$_POST['jmlhMinta'][$brs];
					$keterangan=$_POST['ketrngn'][$brs];
					if(($kodebarang=='') || ($jumlah==''))
					{
						echo"warning:Please Complete The Form";
						exit();
					}
					else
					{
						$sins="insert into ".$dbname.".vhc_penggantianht (`kodeorg`,`kodevhc`,`tanggal`,`updateby`,`notransaksi`,`downtime`, `kerusakan`) values 
						('".$codeOrg."','".$kdJenis."','".$tglGanti."','".$usr_id."','".$notransaksi."','".$dwnTime."','".$descDmg."')";
						//echo $sins;
                                                //exit("Error");
                                                
                                                if(mysql_query($sins))
						{
						$dins="insert into ".$dbname.".vhc_penggantiandt (`notransaksi`,`kodebarang`,`jumlah`,`satuan`,`keterangan`) 
						values ('".$notransaksi."','".$kodebarang."','".$jumlah."','".$satuan."',
						'".$keterangan."')";
						//echo "warning:test".$dins;
							if(mysql_query($dins))
							{
							}
							else
							{
							//echo "warning:masuk";
							echo "DB Error : ".mysql_error($conn);
							}
						}
						else
						{
							echo "DB Error : ".mysql_error($conn);
						}
						
					}
				}
           }
		}
		else
		{
			echo"warning:Please Complete Your Form";
			exit();
		}
		$test=count($_POST['kdbrg']);
		echo $test;
        break;
		case'insert':
		if(($notransaksi!='')||($tglGanti!='')||($dwnTime!='')||($descDmg!=''))
		{
			$sql="select * from ".$dbname.".vhc_penggantianht where notransaksi='".$_POST['notrans']."'";
			$query=mysql_query($sql) or die(mysql_error());
			$row=mysql_num_rows($query);
			if($row<1)
			{
			$sins="insert into ".$dbname.".vhc_penggantianht (`kodeorg`,`kodevhc`,`tanggal`,`updateby`,`notransaksi`,`downtime`, `kerusakan`) values 
			('".$codeOrg."','".$kdJenis."','".$tglGanti."','".$usr_id."','".$notransaksi."','".$dwnTime."','".$descDmg."')";
			if(mysql_query($sins))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
			
			
			//echo "warning:masuk";
			}
			else
			{
			echo"warning:No Transaksi sudah terinput";
			exit();
			}
				
		}
		else
		{
			echo"warning:Please Complete Your Form";
			exit();
		}
		break;
		case 'delete_all':
		//echo "warning:masuk";
		$sql="delete from ".$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."' and kodevhc='".$kdJenis."'";
		//echo "warning:query : ".$sql;
		if(mysql_query($sql))
		{
			$sqld="delete from ".$dbname.".vhc_penggantiandt where notransaksi='".$notransaksi."' ";	
			if(mysql_query($sqld))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo "DB Error : ".mysql_error($conn);
		}
		break;
		case 'cari_transaksi':
		 OPEN_BOX_HRD();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0>
		<thead>
<tr class=rowheader>
<td>".$_SESSION['lang']['notransaksi']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['kodevhc']."</td>
<td>".$_SESSION['lang']['jenisvch']."</td>
<td>".$_SESSION['lang']['downtime']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		if(isset($_POST['txtSearch']))
		{
			$txt_search=$_POST['txtSearch'];
			$txt_tgl=tanggalsystem_hrd($_POST['txtTgl']);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
		}
		else
		{
			$txt_search='';
			$txt_tgl='';			
		}
			if($txt_search!='')
			{
				$where=" notransaksi LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		if($txt_search==''&&$txt_tgl=='')
		{
			$strx="select * from ".$dbname.".vhc_penggantianht where  ".$where." order by tanggal desc";
				 
		}
		else
		{
				$strx="select * from ".$dbname.".vhc_penggantianht where   ".$where." order by tanggal desc";
				
		}
		//echo "warning:".$strx; exit();
		
		
			if($res=mysql_query($strx))
			{
				$numrows=mysql_num_rows($res);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=5>Not Found</td></tr>";
				}
				else
				{
					while($rlvhc=mysql_fetch_assoc($res))
					{
						$pvhc="select kodevhc,jenisvhc from ".$dbname.".vhc_5master where kodevhc='".$rlvhc['kodevhc']."'";
						$qpvhc=mysql_query($pvhc) or die(mysql_error());
						$rpvhc=mysql_fetch_assoc($qpvhc);
					echo"
					<tr class=rowcontent>
					<td>". $rlvhc['notransaksi']."</td>
					<td>". tanggalnormal_hrd($rlvhc['tanggal'])."</td>
					<td>". $rlvhc['kodevhc']."</td>
					<td>". $rpvhc['jenisvhc']."</td>
					<td>". $rlvhc['downtime']."</td>";
					if($rlvhc['updateby']==$usr_id)
					{
					echo
					"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('". $rlvhc['kodeorg']."','". $rlvhc['notransaksi']."','".tanggalnormal_hrd($rlvhc['tanggal'])."','". $rlvhc['kodevhc']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\">	
					<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>
					";}
					else
					{
						echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\"></td>";
					}
					echo"</tr>";
					}
					echo"</tbody></table></div></fieldset>";
					
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
			CLOSE_BOX_HRD();
		break;
		case 'update_header':
		//tanggal ='".$tglGanti."' and kodevhc='".$kdJenis."'
		$sql_cek="select * from ".$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."'";
		$query_cek=mysql_query($sql_cek) or die(mysql_error());
		$res=mysql_fetch_assoc($query_cek);
		if(($res['tanggal']!=$tglGanti)&&($res['kodevhc']!=$kdJenis))
		{
			$sql_cek2="select * from ".$dbname.".vhc_penggantianht where tanggal ='".$tglGanti."' and kodevhc='".$kdJenis."";
			$query_cek2=mysql_query($sql_cek2) or die(mysql_error());
			$rCek=mysql_fetch_row($query_cek2);
			if($rCek<1)
			{
				$sup="update ".$dbname.".vhc_penggantianht set vhc_code='".$kdJenis."',tanggal='".$tglGanti."' where notransaksi='".$notransaksi."'";
				if(mysql_query($sup))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
				
			}
			else
			{
				echo "warning:Vehicle Already Input On Date You Choose";
				exit();
			}
		}
		
		/*$sup="update ".$dbname.".vhc_penggantianht set vhc_code='".$kdJenis."',tanggal='".$tglGanti."' where notransaksi='".$notransaksi."'";
		if(mysql_query($sup))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);*/
		break;
		default:
		break;
	}
?>