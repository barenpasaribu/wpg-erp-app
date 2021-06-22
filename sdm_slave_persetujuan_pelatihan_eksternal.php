<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/eksilib.php');
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['krywnId']==''?$krywnId=$_GET['krywnId']:$krywnId=$_POST['krywnId'];
$_POST['karyidCari']==''?$karyidCari=$_GET['karyidCari']:$karyidCari=$_POST['karyidCari'];
$_POST['alasannya']==''?$alasannya=$_GET['alasannya']:$alasannya=$_POST['alasannya'];
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
$stats[0]='';
$stats[1]=$_SESSION['lang']['disetujui'];
$stats[2]=$_SESSION['lang']['ditolak'];

$atasans = $_SESSION['lang']['atasan'];
$hrds = $_SESSION['lang']['hrd'];

if($_SESSION['empl']['lokasitugas']==$_SESSION['org']['kodepusat']){
	$loktug="";
}
else {
	$loktug="and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}

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

					$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_5training a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug."  order by a.`updatetime` desc";// echo $ql2;
					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
					$query2=mysql_query($ql2) or die(mysql_error());
					while($jsl=mysql_fetch_object($query2)){
					$jlhbrs= $jsl->jmlhrow;
					}
					
					//$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
					$slvhc="select * from ".$dbname.".sdm_5training a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug."  order by a.`updatetime` desc limit ".$offset.",".$limit." ";
					$qlvhc=mysql_query($slvhc) or die(mysql_error());
					$user_online=$_SESSION['standard']['userid'];
					while($rlvhc=mysql_fetch_assoc($qlvhc))
					{
						
					
					$no+=1;
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<!--<td>".$rlvhc['tahunbudget']."</td>-->
					<td>".$nam[$rlvhc['karyawanid']]."</td>
					<!--<td>".$rlvhc['kode']."</td>-->
					<td>".$rlvhc['namatraining']."</td>
					<td>".$jab[$rlvhc['kodejabatan']]."</td>
					<td>".$rlvhc['penyelenggara']."</td>
					<td>".number_format($rlvhc['hargasatuan'],0,'.',',')."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglmulai'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglselesai'])))."</td>";
	//atasan==============================                
					if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['stpersetujuan1']==0)
						{
						  echo"<td align=center>
							 <button class=mybutton id=dtlForm onclick=showappSetuju('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$atasans."')>".$_SESSION['lang']['disetujui']."</button>
							 <button class=mybutton id=dtlForm onclick=showAppTolak('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$atasans."')>".$_SESSION['lang']['ditolak']."</button>
							 <button class=mybutton id=dtlForm onclick=showAppForw('".$rlvhc['kode']."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['forward']."</button></td>
							 <input type=hidden id=trskpd value ='".$_SESSION['lang']['forwardapr']."'>";
						}
						else if($rlvhc['stpersetujuan1']==2)
						   echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					   else if($rlvhc['stpersetujuan1']==1)
							echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					   else if($rlvhc['stpersetujuan1']==0)
							echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";

					}
					else if($rlvhc['stpersetujuan1']==1)
						echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else if($rlvhc['stpersetujuan1']==0)
						echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					else 
						echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
	//=============hrd                
					if($rlvhc['persetujuanhrd']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['sthrd']==0 and $rlvhc['stpersetujuan1']==1)
						{
							echo"<td align=center><button class=mybutton id=dtlForm onclick=showappSetujuHRD('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$hrds."')>".$_SESSION['lang']['disetujui']."</button>
								<button class=mybutton id=dtlForm onclick=showappTolakHRD('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$hrds."')>".$_SESSION['lang']['ditolak']."</button></td>";
							
						}
						else if($rlvhc['stpersetujuan1']==2)
						   //echo"<td align=center>(Tunggu atasan)</td>"; 
							echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
						 else if($rlvhc['sthrd']==2)
						   echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>"; 
						else if($rlvhc['sthrd']==1)
							echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
						else if($rlvhc['sthrd']==0)
							 echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					}
					else
					{
				   if($rlvhc['sthrd']=='0')
				   echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
				   else if($rlvhc['sthrd']=='1')
					echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else 
					echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					}
	//======================================                

					   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$rlvhc['kode']."','".$rlvhc['karyawanid']."',event)\"></td>";


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
					
						if($karyidCari!='')
						{
							$cari.=" and karyawanid='".$karyidCari."'";
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

					$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_5training a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug." where a.karyawanid!='' ".$cari."  order by a.`updatetime` desc";// echo $ql2;
					//$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
					$query2=mysql_query($ql2) or die(mysql_error());
					while($jsl=mysql_fetch_object($query2)){
					$jlhbrs= $jsl->jmlhrow;
					}

					//$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
					$slvhc="select * from ".$dbname.".sdm_5training a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug."  where a.karyawanid!='' ".$cari."  order by a.`updatetime` desc limit ".$offset.",".$limit." ";
					$qlvhc=mysql_query($slvhc) or die(mysql_error());
					$user_online=$_SESSION['standard']['userid'];
					while($rlvhc=mysql_fetch_assoc($qlvhc))
					{
					 
					$no+=1;
					
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<!--<td>".$rlvhc['tahunbudget']."</td>-->
					<td>".$nam[$rlvhc['karyawanid']]."</td>
					<!--<td>".$rlvhc['kode']."</td>-->
					<td>".$rlvhc['namatraining']."</td>
					<td>".$jab[$rlvhc['kodejabatan']]."</td>
					<td>".$rlvhc['penyelenggara']."</td>
					<td>".number_format($rlvhc['hargasatuan'],0,'.',',')."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglmulai'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglselesai'])))."</td>";
	//atasan==============================                
					if($rlvhc['persetujuan1']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['stpersetujuan1']==0)
						{
						  echo"<td align=center>
							  <button class=mybutton id=dtlForm onclick=showappSetuju('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$atasans."')>".$_SESSION['lang']['disetujui']."</button>
							  <button class=mybutton id=dtlForm onclick=showAppTolak('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$atasans."')>".$_SESSION['lang']['ditolak']."</button>
							  <button class=mybutton id=dtlForm onclick=showAppForw('".$rlvhc['kode']."','".$rlvhc['karyawanid']."',event)>".$_SESSION['lang']['forward']."</button></td>
							  <input type=hidden id=trskpd value ='".$_SESSION['lang']['forwardapr']."'>";
						}
						else if($rlvhc['stpersetujuan1']==2)
						   echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
						else
							echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";

					}
					else if($rlvhc['stpersetujuan1']==1)
						echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else if($rlvhc['stpersetujuan1']==0)
						echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					else 
						echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
	//=============hrd                
					if($rlvhc['hrd']==$_SESSION['standard']['userid'])
					{
						if($rlvhc['sthrd']==0 and $rlvhc['stpersetujuan1']==1)
						{

						 echo"<td align=center><button class=mybutton id=dtlForm onclick=showappSetujuHRD('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$hrds."')>".$_SESSION['lang']['disetujui']."</button>
                         <button class=mybutton id=dtlForm onclick=showappTolakHRD('".$rlvhc['kode']."','".$rlvhc['karyawanid']."','".$hrds."')>".$_SESSION['lang']['ditolak']."</button></td>";
						}
						else if($rlvhc['stpersetujuan1']==2)
						   //echo"<td align=center>(Tunggu atasan)</td>"; 
							echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";	
						 else if($rlvhc['sthrd']==2)
						   echo"<td align=center>(".$_SESSION['lang']['ditolak']."</td>"; 
						else
							echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					}
					else if($rlvhc['sthrd']==1)
						echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else if($rlvhc['sthrd']==0)
						echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					else 
						echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
	//======================================                

					   echo"<td align=center> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$rlvhc['kode']."','".$rlvhc['karyawanid']."',event)\"></td>";

					
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
					
                case'showappSetuju': 
                    echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_pelatihan_eksternal.js'></script>
					";

						echo"<table cellspacing=1 border=0 style='width:500px;'>
							 <thead>
							 <tr class=rowheader>
								<td>".$_SESSION['lang']['alasanDterima']."</td>
								<td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
								<td><button class=mybutton onclick=appSetuju('".$kode."','".$krywnId."')>".$_SESSION['lang']['save']."</button></td>
								<input id=alselesai type=hidden value='".$_SESSION['lang']['done']."'>
							 </tr></thead>
							 <tbody>";
						echo"</tbody>
							<tfoot>
							</tfoot>
							</table>";
						
						
					exit;
//                
                break;
				
				case'showappTolak': 
                    echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_pelatihan_eksternal.js'></script>
					";

						echo"<table cellspacing=1 border=0 style='width:500px;'>
							 <thead>
							 <tr class=rowheader>
								<td>".$_SESSION['lang']['alasanDtolak']."</td>
								<td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
								<td><button class=mybutton onclick=appDitolak('".$kode."','".$krywnId."')>".$_SESSION['lang']['save']."</button></td>
								<input id=alselesai type=hidden value='".$_SESSION['lang']['done']."'>
							 </tr></thead>
							 <tbody>";
						echo"</tbody>
							<tfoot>
							</tfoot>
							</table>";
						
						
					exit;
//                
                break;		
				
				case'showappTolakHRD': 
                    echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_pelatihan_eksternal.js'></script>
					";

						echo"<table cellspacing=1 border=0 style='width:500px;'>
							 <thead>
							 <tr class=rowheader>
								<td>".$_SESSION['lang']['alasanDtolak']."</td>
								<td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
								<td><button class=mybutton onclick=appDitolakHRD('".$kode."','".$krywnId."')>".$_SESSION['lang']['save']."</button></td>
								<input id=alselesai type=hidden value='".$_SESSION['lang']['done']."'>
							 </tr></thead>
							 <tbody>";
						echo"</tbody>
							<tfoot>
							</tfoot>
							</table>";
						
						
					exit;
//                
                break;
				case'appSetuju':
                    $sUpdate="update ".$dbname.".sdm_5training  set stpersetujuan1='".$stat."', catatan1='".$alasannya."' where kode='".$kode."' and karyawanid='".$krywnId."'";
                    if(mysql_query($sUpdate))
                    {
                          
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);     
                    }
//                
                break;
				
				case'showappSetujuHRD': 
                    echo"<link rel=stylesheet type='text/css' href='style/generic.css'>
						<script language=javascript src='js/sdm_persetujuan_pelatihan_eksternal.js'></script>
					";

						echo"<table cellspacing=1 border=0 style='width:500px;'>
							 <thead>
							 <tr class=rowheader>
								<td>".$_SESSION['lang']['alasanDterima']."</td>
								<td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>
								<td><button class=mybutton onclick=appSetujuHRD('".$kode."','".$krywnId."')>".$_SESSION['lang']['save']."</button></td>
								<input id=alselesai type=hidden value='".$_SESSION['lang']['done']."'>
							 </tr></thead>
							 
							 <tbody>";
						echo"</tbody>
							<tfoot>
							</tfoot>
							</table>";
						
						
					exit;
//                
                break;

                case 'appSetujuHRD':
				
					 $sUpdate="update ".$dbname.".sdm_5training  set sthrd='".$stat."', catatanhrd='".$alasannya."' where kode='".$kode."' and karyawanid='".$krywnId."'";
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
						$pdf->AddPage();
						$pdf->SetFont('Arial','',10);
						
					$str="select * from ".$dbname.".sdm_5training where karyawanid = '".$krywnId."' and kode = '".$kode."'";
					$res=mysql_query($str);
					$no=1;
					while($bar=mysql_fetch_object($res))
					{
						$namatraining=$bar->namatraining;    
						//$penyelenggara=$host[$bar->penyelenggara];   
						$penyelenggara=$bar->penyelenggara;   
						$tanggalmulai=$bar->tglmulai;
						$tanggalselesai=$bar->tglselesai;
						$hargaperpeserta=$bar->hargasatuan;
						$deskripsi=$bar->desctraining;
						$hasil=$bar->output;
						$atasan=$bar->persetujuan1;
						$atasanstatus=$bar->stpersetujuan1;
						$atasancatatan=$bar->catatan1;
						$hrd=$bar->persetujuanhrd;
						$hrdstatus=$bar->sthrd;
						$hrdcatatan=$bar->catatanhrd;
						$hgt[$idx]=strlen($nam[$bar->karyawanid]);
						$idx++;
						$hgt[$idx]=strlen($bar->catatan1);
						$idx++;
						$hgt[$idx]=strlen($bar->catatanhrd);
						$idx++;
					}
					$pjg=0;
					for ($x=0;$x<count($hgt);$x++){
						if ($hgt[$x]>$pjg){
							$pjg=$hgt[$x];
						}
					}
					$pjaw = 6;
					if($pjg>40){
						$ht=$pjaw*2;
					}
					else if($pjg>80){
						$ht=$pjaw*3;
					}
					else {
						$ht=$pjaw;
					}
						$pdf->Cell(185,6,$_SESSION['lang']['formpjpelatihan'],0,1,'C');
						$pdf->Ln();
						$pdf->Cell(50,6,$_SESSION['lang']['namakaryawan'],0,0,'L');                 
						$pdf->Cell(100,6,': '.$nam[$krywnId],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                      
						$pdf->Cell(100,6,': '.$jab[$jabjab[$krywnId]],0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['departemen'],0,0,'L');                   
						$pdf->Cell(100,6,': '.$depdep[$krywnId],0,1,'L');
						$pdf->Ln();
						$pdf->Cell(50,6,$_SESSION['lang']['namatraining'],0,0,'L');                 
						$pdf->Cell(100,6,': '.$namatraining,0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['penyelenggara'],0,0,'L');                
						$pdf->Cell(100,6,': '.$penyelenggara,0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['tanggalmulai'],0,0,'L');                 
						$pdf->Cell(100,6,': '.tanggalnormal_hrd($tanggalmulai),0,1,'L'); 
						$pdf->Cell(50,6,$_SESSION['lang']['tanggalselesai'],0,0,'L');  
						$pdf->Cell(100,6,': '.tanggalnormal_hrd($tanggalselesai),0,1,'L');
						$pdf->Cell(50,6,$_SESSION['lang']['hargaperpeserta'],0,0,'L');              
						$pdf->Cell(100,6,': '.number_format($hargaperpeserta),0,1,'L');
						$pdf->Ln();
						$pdf->Cell(50,6,$_SESSION['lang']['deskripsitraining'],0,0,'L');            
						$pdf->MultiCell(100,6,': '.$deskripsi,0,'L',false);
						$pdf->Ln();
						$pdf->Cell(50,6,$_SESSION['lang']['hasildiharapkan'],0,0,'L');              
						$pdf->MultiCell(100,6,': '.$hasil,0,'L',false);
						$pdf->Ln();
						$pdf->Cell(40,6,$_SESSION['lang']['persetujuan'],0,1,'L');
						$pdf->Cell(40,6,$_SESSION['lang']['namakaryawan'],1,0,'L');
							$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],1,0,'L');
							$pdf->Cell(20,6,$_SESSION['lang']['status'],1,0,'L');
							$pdf->Cell(80,6,$_SESSION['lang']['catatan'],1,1,'L');
							$start_x = $pdf->GetX();		
							$start_y = $pdf->GetY();
							if(strlen($nam[$atasan])<=40){
								$hts=$pjaw;
							}
							else{
								$hts=$ht;
							}
							$pdf->MultiCell(40,$hts,$nam[$atasan],1,'L');
							$pdf->setXY($start_x+40,$start_y);
						//$pdf->Cell(40,6,substr($nam[$atasan],0,30),0,0,'L');
							$pdf->Cell(50,6,substr($jab[$jabjab[$atasan]],0,30),1,0,'L');
							$pdf->Cell(20,6,$stats[$atasanstatus],1,0,'L');
							$start_x = $pdf->GetX();		
							$start_y = $pdf->GetY();
							if(strlen($atasancatatan)<=80){
								$hts=$pjaw;
							}
							else{
								$hts=$ht;
							}
							$pdf->MultiCell(80,$hts,$atasancatatan,1,'L');
							//$pdf->MultiCell(80,6,$atasancatatan,0,'L',false);
						//$pdf->Ln();
						$start_x = $pdf->GetX();		
						$start_y = $pdf->GetY();
						if(strlen($nam[$hrd])<=40){
							$hts=$pjaw;
						}
						else{
							$hts=$ht;
						}
						$pdf->MultiCell(40,$hts,$nam[$hrd],1,'L');
						$pdf->setXY($start_x+40,$start_y);
						//$pdf->Cell(40,6,$nam[$hrd],0,0,'L');
							$pdf->Cell(50,6,$jab[$jabjab[$hrd]],1,0,'L');
							$pdf->Cell(20,6,$stats[$hrdstatus],1,0,'L');
							$start_x = $pdf->GetX();		
							$start_y = $pdf->GetY();
							if(strlen($hrdcatatan)<=80){
								$hts=$pjaw;
							}
							else{
								$hts=$ht;
							}
							$pdf->MultiCell(80,$hts,$hrdcatatan,1,'L');
							//$pdf->MultiCell(80,6,$hrdcatatan,0,'L',false);
						
						
					//    $str1="select * from ".$dbname.". sdm_5kriteriapsy where kodejabatan like '%".$jabatan2."%' order by kodejabatan, kriteria";
					//    $res1=mysql_query($str1);
					//    while($bar1=mysql_fetch_object($res1))
					//    {
					//        $pdf->Cell(60,6,$kamusJabat[$bar1->kodejabatan],0,0,'L');
					//        $pdf->Cell(30,6,$bar1->kriteria,0,0,'L');	
					//        $pdf->MultiCell(100, 6, $bar1->penjelasan, 0, 'L', false);
					//    }	
						$pdf->Ln();	
					   $pdf->Ln();	
							$pdf->SetX(150);  
							$pdf->Cell(50,5,$_SESSION['lang']['receiptby'],0,1,'C');	  
					   $pdf->Ln();	
					   $pdf->Ln();	
					   $pdf->Ln();	
							$pdf->SetX(150);    
					   $pdf->Cell(50,5,$nam[$krywnId],0,1,'C');
						$pdf->Output();		

                break;
                case'getExcel':
               $tab.=" ".$_SESSION['lang']['listtraining']."<br>
                <table class=sortable cellspacing=1 border=1 width=80%>
                <thead>
                <tr  >
                <td align=center>".$_SESSION['lang']['nourut']."</td>
				<!--<td align=center>".$_SESSION['lang']['budgetyear']."</td>-->
				<td align=center>".$_SESSION['lang']['employeename']."</td>
				<td align=center>".$_SESSION['lang']['namatraining']."</td>
				<td align=center>".$_SESSION['lang']['levelpeserta']."</td>
				<td align=center>".$_SESSION['lang']['penyelenggara']."</td>
				<td align=center>".$_SESSION['lang']['hargaperpeserta']."</td>
				<td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
				<td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
				<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
				<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td>    
                </tr>  
                </thead><tbody>";
                $slvhc="select * from ".$dbname.".sdm_5training  order by `updatetime` desc";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {               
					$no+=1;

					 $tab.="
					<tr class=rowcontent>
					<td>".$no."</td>
					<!--<td>".$rlvhc['tahunbudget']."</td>-->
					<td>".$nam[$rlvhc['karyawanid']]."</td>
					<td>".$rlvhc['namatraining']."</td>
					<td>".$jab[$rlvhc['kodejabatan']]."</td>
					<td>".$host[$rlvhc['penyelenggara']]."</td>
					<td>".number_format($rlvhc['hargasatuan'],0,'.',',')."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglmulai'])))."</td>
					<td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['tglselesai'])))."</td>";
					if($rlvhc['stpersetujuan1']==1)
						$tab.="<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else if($rlvhc['stpersetujuan1']==0)
						$tab.="<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					else 
						$tab.="<td align=center>".$_SESSION['lang']['ditolak']."</td>";
					if($rlvhc['sthrd']==1)
						$tab.="<td align=center>".$_SESSION['lang']['disetujui']."</td>";
					else if($rlvhc['sthrd']==0)
						$tab.="<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
					else 
						$tab.="<td align=center>".$_SESSION['lang']['ditolak']."</td>";
				}
                $tab.="</tbody></table>";
                $nop_="listpengajuanpelatihan";
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
				<input type='hidden' id=kodepl value=".$kode." />
				<input id=alselesai type=hidden value='".$_SESSION['lang']['done']."'>";
                echo $tab;
                break;
                case'forwardData':
                    $sup="update ".$dbname.".sdm_5training set persetujuan1='".$atasan."' where kode='".$kode."' and karyawanid='".$krywnId."'";
                    if(mysql_query($sup))
                    {
					}
                        
                    
                break;
				
                default:
                break;
        }
		
		



?>