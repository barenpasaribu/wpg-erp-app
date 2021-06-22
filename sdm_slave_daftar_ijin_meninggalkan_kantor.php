<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/eksilib.php');
$_POST['ids']==''?$ids=$_GET['ids']:$ids=$_POST['ids'];
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['tglijin']==''?$tglijin=tanggalsystem_hrd($_GET['tglijin']):$tglijin=tanggalsystem_hrd($_POST['tglijin']);
$_POST['krywnId']==''?$krywnId=$_GET['krywnId']:$krywnId=$_POST['krywnId'];
$_POST['lokasitugas']==''?$lokasitugas=$_GET['lokasitugas']:$lokasitugas=$_POST['lokasitugas'];
$stat=$_POST['stat'];
$ket=$_POST['ket'];
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$where=" tanggal='".$tglijin."' and karyawanid='".$krywnId."'";
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arragama=getEnum($dbname,'sdm_ijin','jenisijin');
$jnsCuti=$_POST['jnsCuti'];
$karyidCari=$_POST['karyidCari'];
$atasan=$_POST['atasan'];
$slckry="select karyawanid, namakaryawan from datakaryawan";
$reskry=$eksi->sSQL($slckry);
foreach($reskry as $barkry){
	$nam[$barkry['karyawanid']]=$barkry['namakaryawan'];
}

if($lokasitugas==''){
	//tambah filter pusat atau tidak ==Jo 12-03-2017==
	if($_SESSION['empl']['pusat']==1){
		$loktug="";
	}
	else {
		$loktug="and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
	}
}
else{
	$loktug="and b.lokasitugas='".$lokasitugas."'";
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
                $no=$page*$limit;

                //untuk filter kueri sesuai kondisi (atasan hanya per dept, hrd semua) ==Jo 28-11-2016==
				$slcari="select hrd,pengajuid from ".$dbname.".sdm_ijin where (hrd='".$_SESSION['standard']['userid']."' or pengajuid='".$_SESSION['standard']['userid']."')";
				$rescari=mysql_query($slcari);
				if(mysql_num_rows($rescari)>0){
					$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc";// echo $ql2;
				}
				else {
					 $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.persetujuan1='".$_SESSION['standard']['userid']."' or a.karyawanid='".$_SESSION['standard']['userid']."' or a.pengajuid='".$_SESSION['standard']['userid']."' ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc";
				}
                //$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                //$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
               //untuk filter kueri sesuai kondisi (atasan hanya per dept, hrd semua) ==Jo 28-11-2016==
				$slcari="select hrd,pengajuid from ".$dbname.".sdm_ijin where (hrd='".$_SESSION['standard']['userid']."' or pengajuid='".$_SESSION['standard']['userid']."')";
				$rescari=mysql_query($slcari);
				if(mysql_num_rows($rescari)>0){
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where 1=1 ".$loktug."  order by a.`waktupengajuan` desc, a.`darijam` desc limit ".$offset.",".$limit." ";
				}
				else {
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.persetujuan1='".$_SESSION['standard']['userid']."' or a.karyawanid='".$_SESSION['standard']['userid']."' or a.pengajuid='".$_SESSION['standard']['userid']."' ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc limit ".$offset.",".$limit." ";
				}
				
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
					
				//cari jenis izin pada tabel sdm_jenis_ijin_cuti berdasarkan id jenis izin pada tabel transaksi
				$arrijin = "select  jenisizincuti from ".$dbname.".sdm_jenis_ijin_cuti where id = ". $rlvhc['jenisijin']." ";
				$qarrijin=mysql_query($arrijin) or die(mysql_error());

				while($rarrijin=mysql_fetch_assoc($qarrijin))
				{
					
					$jnijin = $rarrijin['jenisizincuti'];
				}

                /*if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IZINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }      
                    }*/
                    
                $no+=1;
                //ambil sisa cuti
                $sSisa="select sisa from ".$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' 
                        and periodecuti='".$rlvhc['periodecuti']."'";
                $qSisa=mysql_query($sSisa) or die(mysql_error($conn));
                $rSisa=mysql_fetch_assoc($qSisa);
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['waktupengajuan'])))."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$jnijin."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>
                <td align=center>".$rlvhc['jumlahhari']."</td>
                <td align=center>".$rlvhc['sisacuti']."</td>
				<td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>";
				 
//atasan==============================                
                if($rlvhc['stpersetujuan1']==1)
                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else if($rlvhc['stpersetujuan1']==0)
                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                else 
                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
//=============hrd                
               echo" <td>".$arrNmkary[$rlvhc['hrd']]."</td>";
               if($rlvhc['stpersetujuanhrd']=='0')
               echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>"; 
               else if($rlvhc['stpersetujuanhrd']=='1')
                echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else 
                echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
                
//======================================                
				//untuk keterangan cuti telah dibatalkan ==Jo 25-04-2017==
				/*$slbatal="select tanggal from log_sdm_batal_ijin where karyawanid='".$rlvhc['karyawanid']."' and tanggal between date_format(STR_TO_DATE('".$rlvhc['darijam']."','%Y-%m-%d'),'%Y-%m-%d')
				and date_format(STR_TO_DATE('".$rlvhc['sampaijam']."','%Y-%m-%d'),'%Y-%m-%d') and stpersetujuanhrd=1";*/
				
				//rubah karena ada id di sdm_ijin ==Jo 17-05-2017==
				$slbatal="select tanggal from log_sdm_batal_ijin where sdm_ijinid='".$rlvhc['id']."' and tanggal between date_format(STR_TO_DATE('".$rlvhc['darijam']."','%Y-%m-%d'),'%Y-%m-%d')
				and date_format(STR_TO_DATE('".$rlvhc['sampaijam']."','%Y-%m-%d'),'%Y-%m-%d') and stpersetujuanhrd=1";
				$resbatal=$eksi->sSQL($slbatal);
				$jmbtl=$eksi->sSQLnum($slbatal);
				
				$tgbatal="";
				foreach($resbatal as $barbatal){
					$tgbatal.=$barbatal['tanggal'].", ";
				}
				$tglbtls=$_SESSION['lang']['cuti']." ".$_SESSION['lang']['on']." ".substr($tgbatal,0,strlen($tgbatal)-2)." ".$_SESSION['lang']['dibatalkan'];	
				if($jmbtl>0){
					echo"<td align=center> ".$tglbtls."</td>";
				}
				else{
					echo"<td align=center> </td>";
				}

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
                    if($karyidCari!='')
                    {
                        $cari.=" and a.karyawanid='".$karyidCari."'";
                    }
                    if($jnsCuti!='')
                    {
                        $cari.=" and a.jenisijin='".$jnsCuti."'";
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
				$no=$page*$limit;
                //untuk filter kueri sesuai kondisi (atasan hanya per dept, hrd semua) ==Jo 28-11-2016==
				$slcari="select hrd,pengajuid from ".$dbname.".sdm_ijin where (hrd='".$_SESSION['standard']['userid']."' or pengajuid='".$_SESSION['standard']['userid']."')";
				$rescari=mysql_query($slcari);
				if(mysql_num_rows($rescari)>0){
					$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari." ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc";// echo $ql2;
				}
				else {
					 $ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari." and (a.persetujuan1='".$_SESSION['standard']['userid']."' or a.hrd='".$_SESSION['standard']['userid']."' or a.pengajuid='".$_SESSION['standard']['userid']."') ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc";
				}
                //$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                //$slvhc="select * from ".$dbname.".sdm_ijin where  karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') order by `tanggal` desc limit ".$offset.",".$limit." ";
                //untuk filter kueri sesuai kondisi (atasan hanya per dept, hrd semua) ==Jo 28-11-2016==
				$slcari="select hrd,pengajuid from ".$dbname.".sdm_ijin where (hrd='".$_SESSION['standard']['userid']."' or pengajuid='".$_SESSION['standard']['userid']."')";
				$rescari=mysql_query($slcari);
				if(mysql_num_rows($rescari)>0){
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid  where a.karyawanid!='' ".$cari." ".$loktug."  order by a.`waktupengajuan`  desc, a.`darijam` desc limit ".$offset.",".$limit." ";
				}
				else {
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari." and (a.persetujuan1='".$_SESSION['standard']['userid']."' or a.hrd='".$_SESSION['standard']['userid']."' or a.pengajuid='".$_SESSION['standard']['userid']."') ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc limit ".$offset.",".$limit." ";
				}
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                 //cari jenis izin pada tabel sdm_jenis_ijin_cuti berdasarkan id jenis izin pada tabel transaksi
				$arrijin = "select  jenisizincuti from ".$dbname.".sdm_jenis_ijin_cuti where id = ". $rlvhc['jenisijin']." ";
				$qarrijin=mysql_query($arrijin) or die(mysql_error());

				while($rarrijin=mysql_fetch_assoc($qarrijin))
				{
					
					$jnijin = $rarrijin['jenisizincuti'];
				}

                /*if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IZINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }      
                    }*/                    
                $no+=1;
                //ambil sisa cuti
                $sSisa="select sisa from ".$dbname.".sdm_cutiht where karyawanid='".$rlvhc['karyawanid']."' 
                        order by periodecuti desc limit 1";
                $qSisa=mysql_query($sSisa) or die(mysql_error($conn));
                $rSisa=mysql_fetch_assoc($qSisa);
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".tanggalnormal_hrd(date('Y-m-d',strtotime($rlvhc['waktupengajuan'])))."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$jnijin."</td>
                <td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>
                <td align=center>".$rlvhc['jumlahhari']."</td>
                <td align=center>".$rlvhc['sisacuti']."</td>
				<td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>";
//atasan==============================                
                if($rlvhc['stpersetujuan1']==1)
                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else if($rlvhc['stpersetujuan1']==0)
                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                else 
                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
//=============hrd           
				echo" <td>".$arrNmkary[$rlvhc['hrd']]."</td>";     
                if($rlvhc['stpersetujuanhrd']==1)
                    echo"<td align=center>".$_SESSION['lang']['disetujui']."</td>";
                else if($rlvhc['stpersetujuanhrd']==0)
                    echo"<td align=center>".$_SESSION['lang']['wait_approval']."</td>";
                else 
                    echo"<td align=center>".$_SESSION['lang']['ditolak']."</td>";
//======================================                
				//untuk keterangan cuti telah dibatalkan ==Jo 25-04-2017==
				$slbatal="select tanggal from log_sdm_batal_ijin where karyawanid='".$rlvhc['karyawanid']."' and tanggal between date_format(STR_TO_DATE('".$rlvhc['darijam']."','%Y-%m-%d'),'%Y-%m-%d')
				and date_format(STR_TO_DATE('".$rlvhc['sampaijam']."','%Y-%m-%d'),'%Y-%m-%d') and stpersetujuanhrd=1";
				
				$resbatal=$eksi->sSQL($slbatal);
				$jmbtl=$eksi->sSQLnum($slbatal);
				
				$tgbatal="";
				foreach($resbatal as $barbatal){
					$tgbatal.=$barbatal['tanggal'].", ";
				}
				$tglbtls=$_SESSION['lang']['cuti']." ".$_SESSION['lang']['on']." ".substr($tgbatal,0,strlen($tgbatal)-2)." ".$_SESSION['lang']['dibatalkan'];	
				if($jmbtl>0){
					echo"<td align=center> ".$tglbtls."</td>";
				}
				else{
					echo"<td align=center> </td>";
				}
				
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
                <button class=mybutton onclick=cariBastCr(".($page-1).",'".$karyidCari."','".$jnsCuti."','".$lokasitugas."');>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBastCr(".($page+1).",'".$karyidCari."','".$jnsCuti."','".$lokasitugas."');>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                break;
                
                case'prevPdf':

                class PDF extends FPDF
{

        function Header()
        {
            $path='images/logo.jpg';
            //$this->Image($path,15,2,40);	
                $this->SetFont('Arial','B',10);
                $this->SetFillColor(255,255,255);	
                $this->SetY(22);   
            $this->Cell(60,5,$_SESSION['org']['namaorganisasi'],0,1,'C');	 
                $this->SetFont('Arial','',15);
            $this->Cell(190,5,'',0,1,'C');
                $this->SetFont('Arial','',6); 
                $this->SetY(30);
                $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
                $this->Line(10,32,200,32);	   

        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }

}

  $str="select * from ".$dbname.".sdm_ijin where id=".$ids."";	
  //exit("Error".$str);
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

		$jabatan='';
		$namakaryawan='';
		$bagian='';	
		$karyawanid='';
		 $strc="select a.namakaryawan,a.karyawanid,a.bagian,a.nik,b.namajabatan 
			from ".$dbname.".datakaryawan a left join  ".$dbname.".sdm_5jabatan b
				on a.kodejabatan=b.kodejabatan
				where a.karyawanid=".$bar->karyawanid;
			$resc=mysql_query($strc);
          while($barc=mysql_fetch_object($resc))
          {
                $jabatan=$barc->namajabatan;
                $namakaryawan=$barc->namakaryawan;
                $bagian=$barc->bagian;
                $karyawanid=$barc->karyawanid;
                $nik=$barc->nik;
          }
		  
		  $strpg="select namakaryawan,nik from datakaryawan where karyawanid='".$bar->pengajuid."'";
		  $respg=$eksi->sSQL($strpg);
		  foreach($respg as $barpg){
			$namapengaju=$barpg['namakaryawan'];
			$nikpengaju=$barpg['nik'];
		  }
		  
		  if ($nik==$nikpengaju){
			  $pengaju='-';
		  }
		  else {
			 if ($nikpengaju!=''){
				$pengaju= $namapengaju." (".$nikpengaju.")";
			 }
			 else{
				$pengaju='-';
			 }
			 
		  }
	
          //===============================	  

                $perstatus=$bar->stpersetujuan1;
                $tgl=tanggalnormal_hrd(date('Y-m-d',strtotime($bar->waktupengajuan)));
                $kperluan=$bar->keperluan;
                $persetujuan=$bar->persetujuan1;
                $jns=$bar->jenisijin;
                $jmDr=$bar->darijam;
                $jmSmp=$bar->sampaijam;
                $koments=$bar->komenst1;
                $ket=$bar->keterangan;
                $periode=$bar->periodecuti;
				$periodesbl=$bar->periodecutisebelum;
                $sthrd=$bar->stpersetujuanhrd;
                $hk=$bar->jumlahhari;
                $hrd=$bar->hrd;
                $koments2=$bar->komenst2;
				
				//cari jenis izin pada tabel sdm_jenis_ijin_cuti berdasarkan id jenis izin pada tabel transaksi
				$arrijin = "select  jenisizincuti from ".$dbname.".sdm_jenis_ijin_cuti where id = ".$jns." ";
				$qarrijin=mysql_query($arrijin) or die(mysql_error());

				while($rarrijin=mysql_fetch_assoc($qarrijin))
				{
					
					$jnijin = $rarrijin['jenisizincuti'];
				}
				$dd=$jnijin;

                /*if($_SESSION['language']=='ID'){
                        $dd=$jns;
                    }else{
                        switch($jns){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IJINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }  
                    }  */             
                
                
        //ambil bagian,jabatan persetujuan atasan
                $perjabatan='';
                $perbagian='';
                $pernama='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$persetujuan;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatan=$barf->namajabatan;
                $perbagian=$barf->bagian;
                $pernama=$barf->namakaryawan;
        }
        //ambil bagian,jabatan persetujuan hrd
                $perjabatanhrd='';
                $perbagianhrd='';
                $pernamahrd='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$hrd;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatanhrd=$barf->namajabatan;
                $perbagianhrd=$barf->bagian;
                $pernamahrd=$barf->namakaryawan;
        }       
  }

        $pdf=new PDF('P','mm','A4');
        $pdf->SetFont('Arial','B',14);
        $pdf->AddPage();
        $pdf->SetY(40);
        $pdf->SetX(20);
        $pdf->SetFillColor(255,255,255); 
        $pdf->Cell(175,5,strtoupper($_SESSION['lang']['suratizincutido']),0,1,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',8);
        //$pdf->Cell(175,5,'NO : '.$notransaksi,0,1,'C');	

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tanggal'],0,0,'L');
		$pdf->SetX(52);		
                $pdf->Cell(55,5," : ".$tgl,0,1,'L');	
        $pdf->SetX(20);			
        $pdf->Cell(30,5,$_SESSION['lang']['nik'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$nik,0,1,'L');	
      	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['namakaryawan'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$namakaryawan,0,1,'L');
		$pdf->SetX(20);			
        $pdf->Cell(30,5,$_SESSION['lang']['adminpengaju'],0,0,'L');	
		$pdf->SetX(52);	
                $pdf->Cell(55,5," : ".$pengaju,0,1,'L');				
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['bagian'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$bagian,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$jabatan,0,1,'L');
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keperluan'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$kperluan,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['jenisijin'],0,0,'L');
		$pdf->SetX(52);		
                $pdf->Cell(55,5," : ".$dd,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keterangan'],0,0,'L');	
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$ket,0,1,'L');	
         $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['pengabdian']." ".$_SESSION['lang']['tahun'],0,0,'L');
		$pdf->SetX(52);
				$periodes='';
                if($periodesbl==0 && $periode!=0){
					$periodes=$periode;
				}
				else if($periodesbl!=0 && $periode==0){
					$periodes=$periodesbl;
				}
				else if($periodesbl!=0 && $periode!=0) {
					$periodes=$periodesbl.", ".$periode;
				}
				$pdf->Cell(55,5," : ".$periodes,0,1,'L');               
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['dari'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$jmDr,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tglcutisampai'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$jmSmp,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['hari'],0,0,'L');
		$pdf->SetX(52);
                $pdf->Cell(55,5," : ".$hk." ".$_SESSION['lang']['hari'],0,1,'L');	




        $pdf->Ln();	
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',8);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['approval_status']),0,1,'L');	
        $pdf->SetX(20);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['bagian']),1,0,'C');
                $pdf->Cell(50,5,strtoupper($_SESSION['lang']['namakaryawan']),1,0,'C');			
                $pdf->Cell(40,5,strtoupper($_SESSION['lang']['functionname']),1,0,'C');
                $pdf->Cell(37,5,strtoupper($_SESSION['lang']['keputusan']),1,1,'C');	 			

        $pdf->SetFont('Arial','',8);

        $pdf->SetX(20);
                $pdf->Cell(30,5,$perbagian,1,0,'L');
                $pdf->Cell(50,5,$pernama,1,0,'L');			
                $pdf->Cell(40,5,$perjabatan,1,0,'L');
                $pdf->Cell(37,5,$arrKeputusan[$perstatus],1,1,'L');
        $pdf->SetX(20);
                $pdf->Cell(30,5,$perbagianhrd,1,0,'L');
                $pdf->Cell(50,5,$pernamahrd,1,0,'L');			
                $pdf->Cell(40,5,$perjabatanhrd,1,0,'L');
                $pdf->Cell(37,5,$arrKeputusan[$sthrd],1,1,'L');

    $pdf->Ln();               

        $pdf->SetX(20);                
        $pdf->Cell(30,5,$_SESSION['lang']['keputusan']." ".$_SESSION['lang']['atasan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$koments,0,1,'L');	

        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['keputusan']." ".$_SESSION['lang']['hrd'],0,0,'L');	
                $pdf->Cell(50,5," : ".$koments2,0,1,'L');


   $pdf->Ln();	
   
   //tambahan format manggala ==Jo 17-03-2017==
	$pdf->SetX(20);	
	$pdf->Cell(30,5,$_SESSION['lang']['alamatcuti'],0,0,'L');
	$pdf->SetX(52);
	$pdf->Cell(55,5,"",0,0,'L');				
	$pdf->Cell(60,5,"Selama Cuti Tugas dan Jabatan diserahkan Kepada",0,1,'L');	
   
	$pdf->SetX(20);	
	$pdf->Cell(30,5,$_SESSION['lang']['alamat'],0,0,'L');
	$pdf->SetX(35);
	$pdf->Cell(55,5," :  __________________________________",0,0,'L'); 
	$pdf->SetX(107);		
	$pdf->Cell(30,5,$_SESSION['lang']['nama'],0,0,'L');	
	$pdf->SetX(124);
	$pdf->Cell(55,5," :  __________________________________",0,1,'L'); 
	$pdf->SetX(20);

	$pdf->SetX(20);	
	$pdf->Cell(30,5,"",0,0,'L');
	$pdf->SetX(35);
	$pdf->Cell(55,5,"    __________________________________",0,0,'L'); 
	$pdf->SetX(107);				
	$pdf->Cell(30,5,$_SESSION['lang']['jabatan'],0,0,'L');	
	$pdf->SetX(124);
	$pdf->Cell(55,5," :  __________________________________",0,1,'L'); 
	$pdf->SetX(20);
	
	$pdf->SetX(20);	
	$pdf->Cell(30,5,"No Telp/HP",0,0,'L');
	$pdf->SetX(35);
	$pdf->Cell(55,5," :  __________________________________",0,0,'L'); 
	$pdf->SetX(107);
	$pdf->Cell(30,5,$_SESSION['lang']['departemen'],0,0,'L');	
	$pdf->SetX(124);
	$pdf->Cell(55,5," :  __________________________________",0,1,'L'); 
	$pdf->SetX(20);
				
	
	
   $pdf->Ln();
   $pdf->Ln();
   //tambahan format manggala ==Jo 17-03-2017==
	$pdf->SetX(20);	
	$pdf->SetFont('Arial','B',8);		
	$pdf->Cell(172,5,"Rekomendasi/Catatan dari Atasan/Penanggung Jawab/HRD",0,1,'L');
	$pdf->SetX(20);	
	$pdf->Cell(160,5,"",'B',1,'L');
	$pdf->SetX(20);
	$pdf->Cell(160,5,"",'B',1,'L');
	$pdf->SetX(20);
	$pdf->Cell(160,5,"",'B',1,'L');	
	$pdf->SetX(20);
	$pdf->Cell(160,5,"",'B',1,'L');
	$pdf->SetX(20);
	$pdf->Cell(160,5,"",'B',1,'L');

	
	$pdf->Ln();	
   $pdf->Ln();	
   $pdf->Ln();	
//footer================================
   $pdf->SetFont('Arial','B',8);	
   $pdf->SetX(70);
	$pdf->Cell(30,5,$_SESSION['lang']['mengetahui'],0,0,'C');
	
	$pdf->SetX(150);  
	$pdf->Cell(50,5,$_SESSION['lang']['receiptby'],0,1,'C');	  
   $pdf->Ln();	
   $pdf->Ln();	
   $pdf->Ln();	
   $pdf->Cell(50,5,"Supervisor",0,0,'C');
   $pdf->SetX(40);    
   $pdf->Cell(50,5,"Superintendent",0,0,'C');
   $pdf->SetX(75);    
   $pdf->Cell(50,5,"Plant Manager",0,0,'C');
	$pdf->SetX(110);    
   $pdf->Cell(50,5,$_SESSION['lang']['dirut'],0,0,'C');
	$pdf->SetX(150);    
   $pdf->Cell(50,5,$nam[$karyawanid],0,1,'C');
   $pdf->Ln();	
        $pdf->Output();

                break;
                case'getExcel':
               $tab.=" 
				".$_SESSION['lang']['daftarizincutidayoff']."<br>
                <table class=sortable cellspacing=1 border=1 width=80%>
                <thead>
                <tr  >
                <td align=center bgcolor='#DFDFDF'>No.</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tanggal']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['nama']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['keperluan']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jenisijin']."</td>  
				<td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['dari']."  ".$_SESSION['lang']['tanggal']."/".$_SESSION['lang']['jam']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['tanggal']."/".$_SESSION['lang']['jam']."</td>
				<td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jumlahhk']." ".$_SESSION['lang']['diambil']."</td>
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['cuti']." ".$_SESSION['lang']['sisa']."</td>		
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['atasan']."</td>    
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>    
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['hrd']."</td>    
                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td>
		
                
                </tr>  
                </thead><tbody>";
				if($karyidCari!='')
				{
					$cari.=" and a.karyawanid='".$karyidCari."'";
				}
				if($jnsCuti!='')
				{
					$cari.=" and a.jenisijin='".$jnsCuti."'";
				}
				//Samakan rule dengan tampilan aplikasi ==Jo 04-10-2017==
				$slcari="select hrd,pengajuid from ".$dbname.".sdm_ijin where (hrd='".$_SESSION['standard']['userid']."' or pengajuid='".$_SESSION['standard']['userid']."')";
				$rescari=mysql_query($slcari);
				if(mysql_num_rows($rescari)>0){
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid  where a.karyawanid!='' ".$cari." ".$loktug."  order by a.`waktupengajuan`  desc, a.`darijam` desc";
				}
				else {
					$slvhc="select a.* from ".$dbname.".sdm_ijin a left join datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid!='' ".$cari." and (a.persetujuan1='".$_SESSION['standard']['userid']."' or a.hrd='".$_SESSION['standard']['userid']."' or a.pengajuid='".$_SESSION['standard']['userid']."') ".$loktug." order by a.`waktupengajuan` desc, a.`darijam` desc";
				}
                //$slvhc="select * from ".$dbname.".sdm_ijin   order by `tanggal` desc ";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                $user_online=$_SESSION['standard']['userid'];
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
					//cari jenis izin pada tabel sdm_jenis_ijin_cuti berdasarkan id jenis izin pada tabel transaksi
				$arrijin = "select  jenisizincuti from ".$dbname.".sdm_jenis_ijin_cuti where id = ". $rlvhc['jenisijin']." ";
				$qarrijin=mysql_query($arrijin) or die(mysql_error());

				while($rarrijin=mysql_fetch_assoc($qarrijin))
				{
					
					$jnijin = $rarrijin['jenisizincuti'];
				}

                /*if($_SESSION['language']=='ID'){
                        $dd=$rlvhc['jenisijin'];
                    }else{
                        switch($rlvhc['jenisijin']){
                            case 'TERLAMBAT':
                                $dd='Late for work';
                                break;
                            case 'KELUAR':
                                $dd='Out of Office';
                                break;         
                            case 'PULANGAWAL':
                                $dd='Home early';
                                break;     
                            case 'IZINLAIN':
                                $dd='Other purposes';
                                break;   
                            case 'CUTI':
                                $dd='Leave';
                                break;       
                            case 'MELAHIRKAN':
                                $dd='Maternity';
                                break;           
                            default:
                                $dd='Wedding, Circumcision or Graduation';
                                break;                              
                        }      
                    }*/
                                     
                $no+=1;
					
                 $tab.="
                <tr class=rowcontent>
                <td>".$no."</td>
                <td>".date('Y-m-d',strtotime($rlvhc['waktupengajuan']))."</td>
                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$jnijin."</td>
				<td>".$rlvhc['darijam']."</td>
                <td>".$rlvhc['sampaijam']."</td>
				<td>".$rlvhc['jumlahhari']."</td>
                <td>".$rlvhc['sisacuti']."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
				<td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>
                <td>".$arrNmkary[$rlvhc['hrd']]."</td>
                <td>".$arrKeputusan[$rlvhc['stpersetujuanhrd']]."</td>";
                
                }
                $tab.="</tbody></table>";
                //$nop_="listizinkeluarkantor";
                $nop_="listizin_cuti_dayoff";
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
                 $sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                         where alokasi='1' and karyawanid not in('".$_SESSION['standard']['userid']."','".$krywnId."') order by namakaryawan asc";
                $qKary=mysql_query($sKary) or die(mysql_error($sKary));
                while($rKary=mysql_fetch_assoc($qKary))
                {
                    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
                }
                $tab.="<fieldset><legend>".$arrNmkary[$krywnId].", ".$_SESSION['lang']['tanggal']." : ".tanggalnormal_hrd($tglijin)."</legend><table cellpadding=1 cellspacing=1 border=0>";
                $tab.="<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karywanId>".$optKary."</select></td></tr>";
                $tab.="<tr><td colspan=2><button class=mybutton id=dtlForm onclick=AppForw()>Forward</button></td></tr></table>";
                $tab.="</table></fieldset><input type='hidden' id=karyaid value=".$krywnId." /><input type=hidden id=tglIjin value=".tanggalnormal_hrd($tglijin)."/>";
                echo $tab;
                break;
                case'forwardData':
                    $sup="update ".$dbname.".sdm_ijin set persetujuan1='".$atasan."' where $where";
                    if(mysql_query($sup))
                    {
                        $sKar="select distinct * from ".$dbname.".sdm_ijin where $where";
                        $qKar=mysql_query($sKar) or die(mysql_error($conn));
                        $rKar=mysql_fetch_assoc($qKar);
                        $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$krywnId." 
                        and periodecuti=".$rKar['periodecuti'];
                        $res=mysql_query($strf);

                        $sisa='';
                        while($barf=mysql_fetch_object($res))
                        {
                        $sisa=$barf->sisa;
                        }
                        if($sisa=='')
                        $sisa=0;
                    /*$to=getUserEmail_hrd($atasan);
                    $namakaryawan=getNamaKaryawan_hrd($krywnId);
                    $subject="[Notifikasi]Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                    $body="<html>
                    <head>
                    <body>
                    <dd>Dengan Hormat,</dd><br>
                    <br>
                    Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$rKar['jenisijin']." (".$rKar['keperluan'].")
                    kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                    <br>
                    <br>
                    Note: Sisa cuti ybs periode ".$rKar['periodecuti'].":".$sisa." Hari
                    <br>
                    <br>
                    Regards,<br>
                    Owl-Plantation System.
                    </body>
                    </head>
                    </html>
                    ";
                    $kirim=kirimEmail_hrd($to,$subject,$body);#this has return but disobeying;*/
                    }
                    else
                    {
                        echo "DB Error : ".mysql_error($conn);
                    }
                break;

                default:
                break;
        }


?>