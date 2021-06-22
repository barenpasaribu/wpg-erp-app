<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/eksilib.php');
//require_once('MC_Table.php');
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['krywnId']==''?$krywnId=$_GET['krywnId']:$krywnId=$_POST['krywnId'];
$_POST['TrnidCari']==''?$TrnidCari=$_GET['TrnidCari']:$TrnidCari=$_POST['TrnidCari'];
$_POST['alasannya']==''?$alasannya=$_GET['alasannya']:$alasannya=$_POST['alasannya'];
$_POST['ids']==''?$ids=$_GET['ids']:$ids=$_POST['ids'];
$_POST['setuju']==''?$setuju=$_GET['setuju']:$setuju=$_POST['setuju'];
$stat=$_POST['stat'];
$ket=$_POST['ket'];
$_POST['kode']==''?$kode=$_GET['kode']:$kode=$_POST['kode'];
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$atasan=$_POST['atasan'];
//kamus host
$str="select * from ".$dbname.".log_5supplier order by namasupplier";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $host[$bar->supplierid]=$bar->namasupplier;
}
//kamus jabatan
$str="select * from ".$dbname.".sdm_5jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
}

//kamus departemen
$str="select * from ".$dbname.".sdm_5departemen";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $dep[$bar->kode]=$bar->nama;
}

//kamus nama
$str="select namakaryawan,karyawanid,kodejabatan,bagian from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."')  order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nam[$bar->karyawanid]=$bar->namakaryawan;
    $jabjab[$bar->karyawanid]=$bar->kodejabatan;
    $depdep[$bar->karyawanid]=$bar->bagian;
}
$stats[0]=$_SESSION['lang']['alertsdhproses'];
$stats[1]=$_SESSION['lang']['notproses'];
//legend untuk status persetujuan
$sPrs="select value,bahasalegend from ".$dbname.".sdm_5persetujuan";
$qPrs=mysql_query($sPrs) or die(mysql_error());
$idk=0;
while($rPrs=mysql_fetch_assoc($qPrs))
{
    $kamusPrs[$rPrs['value']]=$rPrs['bahasalegend'];
	$stpr[$idk]=$rPrs['value'];
	$idk++;
}

$sJabat="select * from ".$dbname.".sdm_5matriktraining ";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusKategori[$rJabat['matrixid']]=$rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']]=$rJabat['topik'];
}

$atasans = $_SESSION['lang']['atasan'];
$hrds = $_SESSION['lang']['hrd'];
//exit("Error".$jmAwal);
        switch($proses)
        {

                case'loadData':
					
					
					$limit=10;
					$page=0;
					if(isset($_POST['page']))
					{
						$page=$_POST['page'];
						if($page<0)
						$page=0;
					}
					$offset=$page*$limit;
					
					//tambah filter pusat/tidak ==Jo 27-06-2017==
					if($_SESSION['empl']['pusat']==1){
						$whr="";
					}
					else{
						$whr="and b.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
					}
					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght  order by `updatetime` desc";// echo $ql2;
					$slcari="select persetujuanhrd from ".$dbname.".sdm_matriktraininght where persetujuanhrd='".$_SESSION['standard']['userid']."'";
					$rescari=mysql_query($slcari);
					if(mysql_num_rows($rescari)>0){
						$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where 1=1 ".$whr." order by a.`tanggaltraining` desc";// echo $ql2;
					}
					else {
						 $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where a.persetujuan1='".$_SESSION['standard']['userid']."' or a.updateby='".$_SESSION['standard']['userid']."' ".$whr." order by a.`tanggaltraining` desc";
					}
					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
					$query2=mysql_query($ql2) or die(mysql_error());
					while($jsl=mysql_fetch_object($query2)){
					$jlhbrs= $jsl->jmlhrow;
					}
					
					//$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
					//$slvhc="select * from ".$dbname.".sdm_matriktraininght   order by `updatetime` desc limit ".$offset.",".$limit." ";
					
					//tambah filter pusat/tidak ==Jo 27-06-2017==
					if($_SESSION['empl']['pusat']==1){
						$whr="";
					}
					else{
						$whr="and b.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
					}
					$slcari="select persetujuanhrd from ".$dbname.".sdm_matriktraininght where persetujuanhrd='".$_SESSION['standard']['userid']."'";
					$rescari=mysql_query($slcari);
					if(mysql_num_rows($rescari)>0){
						$slvhc="select * from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where 1=1 ".$whr." order by a.`tanggaltraining` desc limit ".$offset.",".$limit." ";
					}
					else {
						$slvhc="select * from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where a.persetujuan1='".$_SESSION['standard']['userid']."' or a.updateby='".$_SESSION['standard']['userid']."' ".$whr." order by a.`tanggaltraining` desc limit ".$offset.",".$limit." ";
					}
					$qlvhc=mysql_query($slvhc) or die(mysql_error());
					$user_online=$_SESSION['standard']['userid'];
					while($rlvhc=mysql_fetch_assoc($qlvhc))
					{
						
					
					$no+=1;
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$kamusTopik[$rlvhc['matrikxid']]."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tanggaltraining'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['sampaitanggal'])))."</td>";
	//atasan==============================                
					if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['prs1']==0)
						{
						  echo"<td align=center>
							 <button class=mybutton id=dtlForm onclick=showappProses('".$rlvhc['id']."','".$atasans."')>".$_SESSION['lang']['proses']."</button>
							 
							 <button class=mybutton id=dtlForm onclick=showAppForw('".$rlvhc['id']."','".$_SESSION['standard']['userid']."',event)>".$_SESSION['lang']['forward']."</button></td>
							 <input type=hidden id=trskpd value ='".$_SESSION['lang']['forwardapr']."'>";
						}
						
					   else if($rlvhc['prs1']==1)
							echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					   else if($rlvhc['prs1']==0)
							echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";

					}
					else if($rlvhc['prs1']==1)
						echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					else if($rlvhc['prs1']==0)
						echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";
					
	//=============hrd                
					if($rlvhc['persetujuanhrd']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['prshrd']==$stpr[0] and $rlvhc['prs1']==$stpr[1])
						{
							echo"<td align=center><button class=mybutton id=dtlForm onclick=showappHRD('".$rlvhc['id']."','".$hrds."')>".$_SESSION['lang']['proses']."</button>
                         </td>";
							
						 
						}
						else if($rlvhc['prs1']==0)
						   echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
						else if($rlvhc['prshrd']==1)
							echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
						else if($rlvhc['prshrd']==0)
							 echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";
					}
					else
					{
					   if($rlvhc['prshrd']=='0')
					   echo"<td align=center>".$_SESSION['lang']['notproses']."</td>"; 
					   else if($rlvhc['prshrd']=='1')
						echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
						
					}
	//======================================                

					   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$rlvhc['id']."',event)\"></td>";


				  }//end while
				  //rubah sesuaikan jumlah supaya tidak ambigu ==Jo 04-06-2017==
					if ((($page+1)*$limit)>$jlhbrs){
						$tos=$jlhbrs;
					}
					else{
						$tos=(($page+1)*$limit);
					}
					echo"
					</tr><tr class=rowheader><td colspan=13 align=center>
					".(($page*$limit)+1)." to ".$tos." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					break;
					
					
					case'cariData':
					
						if($TrnidCari!='')
						{
							$cari.=" and matrikxid='".$TrnidCari."'";
						}
						
					$limit=10;
					$page=0;
					if(isset($_POST['page']))
					{
					$page=$_POST['page'];
					if($page<0)
					$page=0;
					}
					$offset=$page*$limit;

					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght where matrikxid!='' ".$cari."  order by `updatetime` desc";// echo $ql2;
					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
					//tambah filter pusat/tidak ==Jo 27-06-2017==
					if($_SESSION['empl']['pusat']==1){
						$whr="";
					}
					else{
						$whr="and b.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
					}
					$slcari="select persetujuanhrd from ".$dbname.".sdm_matriktraininght where matrikxid!='' ".$cari." and persetujuanhrd='".$_SESSION['standard']['userid']."'";
					$rescari=mysql_query($slcari);
					if(mysql_num_rows($rescari)>0){
						$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where a.matrikxid!='' ".$cari." ".$whr." order by a.`tanggaltraining` desc";// echo $ql2;
					}
					else {
						 $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where a.matrikxid!='' ".$cari." and (a.persetujuan1='".$_SESSION['standard']['userid']."' or a.updateby='".$_SESSION['standard']['userid']."') ".$whr." order by a.`tanggaltraining` desc";
					}
					$query2=mysql_query($ql2) or die(mysql_error());
					while($jsl=mysql_fetch_object($query2)){
					$jlhbrs= $jsl->jmlhrow;
					}

					//$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
					//$slvhc="select * from ".$dbname.".sdm_matriktraininght  where matrikxid!='' ".$cari."  order by `updatetime` desc limit ".$offset.",".$limit." ";
					
					//tambah filter pusat/tidak ==Jo 27-06-2017==
					if($_SESSION['empl']['pusat']==1){
						$whr="";
					}
					else{
						$whr="and b.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
					}
					$slcari="select persetujuanhrd from ".$dbname.".sdm_matriktraininght where matrikxid!='' ".$cari." and persetujuanhrd='".$_SESSION['standard']['userid']."'";
					$rescari=mysql_query($slcari);
					if(mysql_num_rows($rescari)>0){
						$slvhc="select * from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid  where a.matrikxid!='' ".$cari." ".$whr." order by a.`tanggaltraining` desc limit ".$offset.",".$limit." ";
					}
					else {
						$slvhc="select * from ".$dbname.".sdm_matriktraininght a 
						left join sdm_5matriktraining b on a.matrikxid=b.matrixid where a.matrikxid!='' ".$cari." and (a.persetujuan1='".$_SESSION['standard']['userid']."' or a.updateby='".$_SESSION['standard']['userid']."') ".$whr." order by a.`tanggaltraining` desc limit ".$offset.",".$limit." ";
					}
					$qlvhc=mysql_query($slvhc) or die(mysql_error());
					$user_online=$_SESSION['standard']['userid'];
					while($rlvhc=mysql_fetch_assoc($qlvhc))
					{
					 
					$no+=1;
					
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$kamusTopik[$rlvhc['matrikxid']]."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tanggaltraining'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['sampaitanggal'])))."</td>";
	//atasan==============================                
					if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['prs1']==0)
						{
						  echo"<td align=center>
							 <button class=mybutton id=dtlForm onclick=showappProses('".$rlvhc['id']."','".$atasans."')>".$_SESSION['lang']['proses']."</button>
							
							 <button class=mybutton id=dtlForm onclick=showAppForw('".$rlvhc['id']."','".$_SESSION['standard']['userid']."',event)>".$_SESSION['lang']['forward']."</button></td>
							 <input type=hidden id=trskpd value ='".$_SESSION['lang']['forwardapr']."'>";
						}
						
					   else if($rlvhc['prs1']==1)
							echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					   else if($rlvhc['prs1']==0)
							echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";

					}
					else if($rlvhc['prs1']==1)
						echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					else if($rlvhc['prs1']==0)
						echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";
					
	//=============hrd                
					if($rlvhc['persetujuanhrd']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['prshrd']==0 and $rlvhc['prs1']==1)
						{
							echo"<td align=center><button class=mybutton id=dtlForm onclick=showappHRD('".$rlvhc['id']."','".$hrds."')>".$_SESSION['lang']['proses']."</button>";
							
						 
						}
						else if($rlvhc['prs1']==0)
						   echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
						else if($rlvhc['prshrd']==1)
							echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
						else if($rlvhc['prshrd']==0)
							 echo"<td align=center>".$_SESSION['lang']['notproses']."</td>";
					}
					else
					{
					   if($rlvhc['prshrd']=='0')
					   echo"<td align=center>".$_SESSION['lang']['notproses']."</td>"; 
					   else if($rlvhc['prshrd']=='1')
						echo"<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
						
					}
	//======================================                

					   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$rlvhc['id']."',event)\"></td>";

					
				  }//end while
				  //rubah sesuaikan jumlah supaya tidak ambigu ==Jo 04-06-2017==
					if ((($page+1)*$limit)>$jlhbrs){
						$tos=$jlhbrs;
					}
					else{
						$tos=(($page+1)*$limit);
					}
					echo"
					</tr><tr class=rowheader><td colspan=13 align=center>
					".(($page*$limit)+1)." to ".$tos." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
					break;
					
                case'showappProses': 
                    echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_matriks_pelatihan.js'></script>
					";

						/*echo"<table cellspacing=1 border=0 style='width:500px;'>
							 <thead>
							 <tr class=rowheader>
								<td>".$_SESSION['lang']['alasanDterima']."</td>
								<td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
								<td><button class=mybutton onclick=appSetuju('".$kode."','".$krywnId."')>".$_SESSION['lang']['save']."</button></td>
							 </tr></thead>
							 <tbody>";
						echo"</tbody>
							<tfoot>
							</tfoot>
							</table>";*/
						
					$str1="select * from ".$dbname.".sdm_matriktrainingdt where headerid='".$ids."'";
					//echo "".$str1;
					$res1=mysql_query($str1);

					echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
						 <thead>
						 <tr class=rowheader>
							<td>".$_SESSION['lang']['namakaryawan']."</td>
							<td>".$_SESSION['lang']['remark']."</td>
							<td>".$_SESSION['lang']['disetujui']."</td>
						 </tr></thead>
						 
						 <tbody id=container>
						 ";
					$no=0;
					while($bar1=mysql_fetch_object($res1))
					{ 
						$no+=1;
						echo"<tr class=rowcontent>
							<td id=namakryA".$no.">".$nam[$bar1->karyawanid]."</td>
							<td><input type=text class=myinputtext id=remarkA".$no." onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=500 ></td>
							<td align=center>
								<input type=checkbox name=cekbok value=cekbok id=cekA".$no.">
								<input type=hidden id=idkryA".$no." value='".$bar1->karyawanid."' ></input>
								<input type=hidden id=idsA".$no." value='".$bar1->headerid."' ></input>
								<input type=hidden id=stj value=".$stpr[1]." ></input>
								<input type=hidden id=tlk value=".$stpr[2]." ></input>
								<input type=hidden id=alselesai value='".$_SESSION['lang']['done']."' ></input>
								
							</td>
						</tr>";
					}	 
					echo"</tbody>
						<tfoot>
						</tfoot>
						
						</table>
						<table>
						<button class=mybutton onclick=appProses(".$no.")>".$_SESSION['lang']['save']."</button>
						</table>";	
					exit;
//                
                break;
				
				
				case'appProses':
                    $sUpdate="update ".$dbname.".sdm_matriktrainingdt  set stpersetujuan1='".$setuju."', catatan1='".$alasannya."' where headerid='".$ids."' and karyawanid=".$krywnId."";
                    if(mysql_query($sUpdate))
                    {
                          
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                
                break;
				
				case'appProsesAH':
                    $sUpdate="update ".$dbname.".sdm_matriktraininght  set prs1=1 where id='".$ids."'";
                    if(mysql_query($sUpdate))
                    {
                          
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                
                break;
				
				case'showappHRD': 
                   echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_matriks_pelatihan.js'></script>
					";

						
					$str1="select * from ".$dbname.".sdm_matriktrainingdt where headerid='".$ids."'";

					$res1=mysql_query($str1);

					echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
						 <thead>
						 <tr class=rowheader>
							<td>".$_SESSION['lang']['namakaryawan']."</td>
							<td>".$_SESSION['lang']['remark']."</td>
							<td>".$_SESSION['lang']['disetujui']."</td>
						 </tr></thead>
						 
						 <tbody id=container>
						 ";
					$no=0;
					while($bar1=mysql_fetch_object($res1))
					{ 
						$no+=1;
						echo"<tr class=rowcontent>
							<td id=namakryH".$no.">".$nam[$bar1->karyawanid]."</td>
							<td><input type=text class=myinputtext id=remarkH".$no." onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=500 ></td>
							<td align=center>
								<input type=checkbox name=cekbok value=cekbok id=cekH".$no.">
								<input type=hidden id=idkryH".$no." value='".$bar1->karyawanid."' ></input>
								<input type=hidden id=idsH".$no." value='".$bar1->headerid."' ></input>
								<input type=hidden id=stj value=".$stpr[1]." ></input>
								<input type=hidden id=tlk value=".$stpr[2]." ></input>
								<input type=hidden id=alselesai value='".$_SESSION['lang']['done']."' ></input>
							</td>
						</tr>";
					}	 
					echo"</tbody>
						<tfoot>
						</tfoot>
						
						</table>
						<table>
						<button class=mybutton onclick=appProsesHRD(".$no.")>".$_SESSION['lang']['save']."</button>
						</table>";	
					exit;
//                
                break;

                case 'appProsesHRD':
				
					$sUpdate="update ".$dbname.".sdm_matriktrainingdt  set sthrd='".$setuju."', catatanhrd='".$alasannya."' where headerid='".$ids."' and karyawanid=".$krywnId."";
                    if(mysql_query($sUpdate))
                    {
                          
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
				   
//                   
                break;
				case'appProsesHH':
                    $sUpdate="update ".$dbname.".sdm_matriktraininght  set prshrd=1 where id='".$ids."'";
                    if(mysql_query($sUpdate))
                    {
                          
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                
                break;
                case'prevPdf':

					//=================================================
					class PDF extends FPDF {
				//        function Header() {
				//            global $jabatan;
				//            global $kriteria;
				//            $this->SetFont('Arial','B',11);
				//            $this->Cell(190,6,strtoupper($_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']),0,1,'C');
				//            $this->Ln();
				//            $this->SetFont('Arial','',10);
				//            $this->Cell(60,6,$_SESSION['lang']['jabatan'],1,0,'C');
				//            $this->Cell(30,6,$_SESSION['lang']['kriteria'],1,0,'C');	
				//            $this->Cell(100,6,$_SESSION['lang']['deskripsi'],1,0,'C');	
				//            $this->Ln();						
				//        }

						
					}
					//================================
					$pdf=new PDF('P','mm','A4');
					
					//funtion supaya multicell panjang kolomnya seragam ==Jo 23-05-2017==
					function SetWidths($w,$pdf)
					{
						//Set the array of column widths
						$pdf->widths=$w;
					}
					
					function NbLines($w,$txt,$pdf)
					{
						//Computes the number of lines a MultiCell of width w will take
						$cw=&$pdf->CurrentFont['cw'];
						if($w==0)
							$w=$pdf->w-$pdf->rMargin-$pdf->x;
						$wmax=($w-2*$pdf->cMargin)*1000/$pdf->FontSize;
						$s=str_replace("\r",'',$txt);
						$nb=strlen($s);
						if($nb>0 and $s[$nb-1]=="\n")
							$nb--;
						$sep=-1;
						$i=0;
						$j=0;
						$l=0;
						$nl=1;
						while($i<$nb)
						{
							$c=$s[$i];
							if($c=="\n")
							{
								$i++;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
								continue;
							}
							if($c==' ')
								$sep=$i;
							$l+=$cw[$c];
							if($l>$wmax)
							{
								if($sep==-1)
								{
									if($i==$j)
										$i++;
								}
								else
									$i=$sep+1;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
							}
							else
								$i++;
						}
						return $nl;
					}
					function Row($data,$pdf)
					{
						//Calculate the height of the row
						$nb=0;
						for($i=0;$i<count($data);$i++)
							$nb=max($nb,NbLines($pdf->widths[$i],$data[$i][0],$pdf));
						$h=5*$nb;
						
						//Draw the cells of the row
						for($i=0;$i<count($data);$i++)
						{
							$w=$pdf->widths[$i];
							$a=isset($data[$i][1]) ? $data[$i][1] : 'L';
							//Save the current position
							$x=$pdf->GetX();
							$y=$pdf->GetY();
							//Draw the border
							$pdf->Rect($x,$y,$w,$h);
							//Print the text
							$pdf->MultiCell($w,5,$data[$i][0],0,$a);
							//Put the position to the right of the cell
							$pdf->SetXY($x+$w,$y);
						}
						//Go to the next line
						$pdf->Ln($h);
					}
					
					$pdf->AddPage();
					
					$pdf->SetFont('Arial','',10);
					
					$pdf->Cell(185,6,$_SESSION['lang']['listtraining'],0,1,'C');
					$pdf->Ln();
					$pdf->SetFont('Arial','',8);
					
					//Ganti jadi Header ==Jo 04-12-2016==
					$str="select * from ".$dbname.".sdm_matriktraininght where id='".$ids."'";
					$res=mysql_query($str);
					while($bar=mysql_fetch_object($res))
					{
						$pengaju=$nam[$bar->updateby];
						$pdf->Cell(50,6,$_SESSION['lang']['namapengaju'],0,0,'L');                 $pdf->Cell(100,6,': '.$pengaju,0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                 $pdf->Cell(100,6,': '.$jab[$jabjab[$bar->updateby]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['departemen'],0,0,'L');                 $pdf->Cell(100,6,': '.$dep[$depdep[$bar->updateby]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['topik'],0,0,'L');                 $pdf->Cell(100,6,': '.$kamusTopik[$bar->matrikxid],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['tanggalmulai'],0,0,'L');                      $pdf->Cell(100,6,': '.$bar->tanggaltraining,0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['tanggalsampai'],0,0,'L');                  $pdf->Cell(100,6,': '.$bar->sampaitanggal,0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['atasan'],0,0,'L');                          $pdf->Cell(100,6,': '.$nam[$bar->persetujuan1],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                 $pdf->Cell(100,6,': '.$jab[$jabjab[$bar->persetujuan1]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['departemen'],0,0,'L');                 $pdf->Cell(100,6,': '.$dep[$depdep[$bar->persetujuan1]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['hrd'],0,0,'L');                          $pdf->Cell(100,6,': '.$nam[$bar->persetujuanhrd],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                 $pdf->Cell(100,6,': '.$jab[$jabjab[$bar->persetujuanhrd]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['departemen'],0,0,'L');                 $pdf->Cell(100,6,': '.$dep[$depdep[$bar->persetujuanhrd]],0,1,'L');
						$pdf->Ln();
						
					}
					
					$pdf->Ln();
					$pdf->Cell(185,6,$_SESSION['lang']['listemployee'],0,1,'L');
					$pdf->Cell(30,6,$_SESSION['lang']['namakaryawan'],1,0,'C');
					$pdf->Cell(30,6,$_SESSION['lang']['remark'],1,0,'C');
					$pdf->Cell(30,6,$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan'],1,0,'C');
					$pdf->Cell(30,6,$_SESSION['lang']['remark']." ".$_SESSION['lang']['atasan'],1,0,'C');
					$pdf->Cell(30,6,$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd'],1,0,'C');
					$pdf->Cell(30,6,$_SESSION['lang']['remark']." ".$_SESSION['lang']['hrd'],1,0,'C');
					$pdf->Ln();
					
					
					$str="select * from ".$dbname.".sdm_matriktrainingdt
						where headerid = '".$ids."'";
					$res=mysql_query($str);
					
					while($bar=mysql_fetch_object($res))
					{
					
						SetWidths(array(30,30,30,30,30,30),$pdf);
						Row(array(
									array($nam[$bar->karyawanid]),
									array($bar->catatan),
									array($_SESSION['lang'][$kamusPrs[$bar->stpersetujuan1]]),
									array($bar->catatan1),
									array($_SESSION['lang'][$kamusPrs[$bar->sthrd]]),
									array($bar->catatanhrd)
						),$pdf);
					}
						$pdf->Ln();	
					   $pdf->Ln();	
							$pdf->SetX(150);  
							$pdf->Cell(50,5,$_SESSION['lang']['receiptby'],0,1,'C');	  
					   $pdf->Ln();	
					   $pdf->Ln();	
					   $pdf->Ln();	
							$pdf->SetX(150);    
					   $pdf->Cell(50,5,$pengaju,0,1,'C');
					
				 
					$pdf->Output();	
				break;
                case'getExcel':
               $tab.="".$_SESSION['lang']['listmatrixtraining']."<br>
                <table class=sortable cellspacing=1 border=1 width=80%>
                <thead>
                <tr>
               <td align=center>".$_SESSION['lang']['nourut']."</td>
				<td align=center>".$_SESSION['lang']['namatraining']."</td>
				<td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
				<td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
				<td align=center>".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['atasan']."</td>
				<td align=center>".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['hrd']."</td>      
                </tr>  
                </thead><tbody>";
                $slvhc="select * from ".$dbname.".sdm_matriktraininght  order by `updatetime` desc";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {               
					$no+=1;

					 $tab.="
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$kamusTopik[$rlvhc['matrikxid']]."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tanggaltraining'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['sampaitanggal'])))."</td>";
					if($rlvhc['prs1']==1)
						$tab.="<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					else if($rlvhc['prs1']==0)
						$tab.="<td align=center>".$_SESSION['lang']['notproses']."</td>";
					
					if($rlvhc['prshrd']==1)
						$tab.="<td align=center>".$_SESSION['lang']['alertsdhproses']."</td>";
					else if($rlvhc['prshrd']==0)
						$tab.="<td align=center>".$_SESSION['lang']['notproses']."</td>";
					
				}
                $tab.="</tbody></table>";
                $nop_="listmatrikspelatihan";
				if(strlen($tab)>0)
				{
				if ($handle = opendir('tempExcel')) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							@unlink('tempExcel/'.$file);
						}
					}	
				   closedir($handle);
				}
				 $handle=fopen("tempExcel/".$nop_.".xls",'w');
				 if(!fwrite($handle,$tab))
				 {
				  echo "<script language=javascript1.2>
						parent.window.alert('Can't convert to excel format');
						</script>";
				   exit;
				 }
				 else
				 {
				  echo "<script language=javascript1.2>
						window.location='tempExcel/".$nop_.".xls';
						</script>";
				 }
				closedir($handle);
				}			
                break;
                case'formForward':
                 $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                 //kueri atasan penyetuju modul HR sesuai setting ==JO 24-01-2017==
				$sOrg="select a.karyawanid, b.namakaryawan from setup_approval_hrd a 
						left join datakaryawan b on a.karyawanid=b.karyawanid 
						where a.applikasi='PAHR' and a.kodeunit like '%".$_SESSION['empl']['kodeorganisasi']."%' and a.karyawanid not in('".$_SESSION['standard']['userid']."','".$krywnId."') order by b.namakaryawan";
				$rOrg = $eksi->sSQL($sOrg);
				foreach($rOrg as $barOrg){
					$optKary.="<option value=".$barOrg['karyawanid'].">".$barOrg['namakaryawan']."</option>";
				}
                $tab.="<fieldset><legend>".$arrNmkary[$krywnId].", </legend><table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karywanId>".$optKary."</select></td></tr>";
                $tab.="<tr><td colspan=2><button class=mybutton id=dtlForm onclick=AppForw()>".$_SESSION['lang']['forward']."</button></td></tr></table>";
                $tab.="</table></fieldset><input type='hidden' id=karyaid value=".$krywnId." />
				<input type='hidden' id=idpl value=".$ids." />
				<input type=hidden id=alselesai value ='".$_SESSION['lang']['done']."'>";
                echo $tab;
                break;
                case'forwardData':
                    $sup="update ".$dbname.".sdm_matriktraininght set persetujuan1='".$atasan."' where id='".$ids."'";
                    if(mysql_query($sup))
                    {
					}
                        
                    
                break;
				
                default:
                break;
        }
		
		



?>