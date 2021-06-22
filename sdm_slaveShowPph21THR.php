<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

 $periode=$_POST['periode'];
 $regular=$_POST['regular'];
 $thr	 =$_POST['thr'];
 $jaspro =$_POST['jaspro'];
 $jmsperusahaan =$_POST['jmsperusahaan'];
 $kodeorg=$_POST['kodeorg'];
//get Component
/*$arrComp=Array();
$str="select id, isPPH21Result from ".$dbname.".sdm_ho_component
      where `pph21`=1 and `lock`=1  order by id";	  
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_array($res))
{
	array_push($arrComp, $bar[0]);
}	  
for($x=0;$x<count($arrComp);$x++)
{
	if($x==0)
	   $listComp=$arrComp[$x];
	else
	   $listComp.=",".$arrComp[$x];   
}
//create string sql
$listComp=" and d.component in(".$listComp.")";
//get PTKP
$arrPtkp=Array();
$str="select * from ".$dbname.".sdm_ho_pph21_ptkp order by id";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	$arrPtkp[$bar->id]=$bar->value;
}

//get Tarif Kontribusi
$arrTarif=Array();
$arrTarifVal=Array();
$str="select * from ".$dbname.".sdm_ho_pph21_kontribusi
      where percent!=0 or upto!=0  order by upto";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	array_push($arrTarif,$bar->percent);
	array_push($arrTarifVal,$bar->upto);
}









//get JMS tanggungan perusahaan
$jmsporsi=4.54;//default
$jmsporsikar=2;//default
$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str,$conn);
while($bar=mysql_fetch_object($res))
{
	if($bar->id=='perusahaan')
	   $jmsporsi=$bar->value;
	else
	   $jmsporsikar=$bar->value;
}




$stru="select `persen`,`max` from ".$dbname.".sdm_ho_pph21jabatan";
$resu=mysql_query($stru);
	$percenJab=0;
	$maxBJab=0;
while($baru=mysql_fetch_object($resu))
{
	$percenJab=$baru->persen;
	$maxBJab=$baru->max;
}
*/

	/*$str1="select e.karyawanid,e.npwp,e.taxstatus,e.name, d.value as `valueone`, d.component
      from 
     ".$dbname.".sdm_ho_employee e,".$dbname.".sdm_ho_detailmonthly d 
	 where e.karyawanid=d.karyawanid ".$listComp."
	 and periode='".$periode."'  and d.`type`='regular'
	 group by karyawanid";*/
	 
	 //cari dulu component thr ==Jo 17-03-2017==
	$slthr="select id from sdm_ho_component where isTHR=1";
	$resthr=$eksi->sSQL($slthr);
	foreach($resthr as $barthr){
		$idthr=$barthr['id'];
	}
	 
	  $opuser='';
	 $struser="select ifnull(namauser,' ') as namauser from ".$dbname.".`user` where karyawanid='".$_SESSION['standard']['userid']."' limit 1";
	
	 $resuser=mysql_query($struser,$conn);
	 while($baruser=mysql_fetch_object($resuser))
	{
		$opuser=$baruser->namauser;
	}
	
	//untuk filter sesuai lokasi tugas ==Jo 14-08-2017==
	if ($kodeorg==""){
		$whrorg="";
	}
	else{
		$whrorg="and f.lokasitugas='".$kodeorg."'";
	}
	//lepas and e.operator='".$opuser."' ==Jo 17-03-2017==
	 $strpph = "select f.nik,e.karyawanid,e.npwp,e.taxstatus,e.name, a.nettodanthr, a.pph21danthr, a.pph21atasthr, g.value as nthr,  e.lastpayment 
				 from ".$dbname.".sdm_ho_employee e 
				left join ".$dbname.".sdm_pph21_data a on e.karyawanid = a.karyawanid and e.operator<>''
				left join ".$dbname.".datakaryawan f on e.karyawanid =f.karyawanid
				left join ".$dbname.".sdm_ho_detailmonthly g on a.periode =g.periode and e.karyawanid=g.karyawanid and g.component=".$idthr."
				 where a.periode='".$periode."' and e.firstpayment<='".$periode."' and (e.lastpayment>='".$periode."' or e.lastpayment='' or e.lastpayment IS NULL)  and g.value>0 ".$whrorg."
				 group by a.karyawanid"; 
	 if($res=mysql_query($strpph,$conn)) {
		 $no=0;
		 while($bar=mysql_fetch_object($res))
			{
				
				$no+=1;
				$gapok=0;
			   $tunjangandsb=0;
			   $premiasuransi=0;
			   $thr=0;
				$brutos=0;
				$tjpph21=0;
			   //ganti netto jadi bruto ==Jo 25-07-2017==
				if($bar->isPPHGrossUp==1){
					$tjpph21=round($pph21s);
				}
				else{
				}
				
				 
			    //untuk ambil nilai detail gaji ==Jo 11-04-2017==
				$strpph="select 
				((select(case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)>date_format(STR_TO_DATE(h.firstpayment,'%Y-%m'),'%Y-%m')) then 
				case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m')) then h.lastvol else 100 end 
				else h.firstvol end)/100)*(sum(case when a.isIP = 0 and a.isGP=1 then ifnull(b.value,0) else 0 end))) as gajipokok,
				((select(case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)>date_format(STR_TO_DATE(h.firstpayment,'%Y-%m'),'%Y-%m')) then 
				case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m')) then h.lastvol else 100 end 
				else h.firstvol end)/100)*(sum(case when (a.isIP = 0 and a.isGP=0 and a.isTJ=1) or (a.isGP=0 and a.pph21=1 and isTJ=0 and a.isBNS=0 and a.isLembur=0 and a.isTHR=0 and a.isIP=0 and a.isIPL=0 and a.isPotongan=0 and a.isAngsuran=0) then ifnull(b.value,0) else 0 end))) as tunjangan,
				sum(case when a.isIPL = 1  then ifnull(b.value,0) else 0 end) as premiasuransi,
				sum(case when a.isIP = 1 then ifnull(b.value*-1,0)  else 0 end) as iuranpengurang,
				sum(case when (a.isIP = 0 and a.isLembur=1) then ifnull(b.value,0) else 0 end) as lembur,
				sum(case when (a.isIP = 0 and a.isBNS=1) then ifnull(b.value,0) else 0 end) as bonus,
				sum(case when (a.isIP = 0 and a.isTHR=1) then ifnull(b.value,0) else 0 end) as thr,
				sum(case when a.isPotongan = 1 then ifnull(b.value*-1,0)  else 0 end) as potpen,
				c.persen, c.max  as maks,
				
				d.npwp

				from sdm_ho_component a 
				inner join sdm_ho_detailmonthly b on a.id = b.component and b.periode ='".$periode."'
				join sdm_ho_pph21jabatan c
				left join datakaryawan d on b.karyawanid = d.karyawanid
				left join organisasi g on d.kodeorganisasi=g.kodeorganisasi
				left join sdm_ho_employee h on d.karyawanid=h.karyawanid 
				where a.pph21 = 1 and b.karyawanid='".$bar->karyawanid."'";
					
				$respph=$eksi->sSQL($strpph);
				
				foreach($respph as $barpph){
					$gapok=$barpph['gajipokok'];
					$tunjangandsb=($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen']);
					
					$honariumdsb=0;
					$premiasuransi=$barpph['premiasuransi'];
					$natura=0;
					$thr=$barpph['bonus'];
					
					$brutos=$gapok+$tjpph21+$tunjangandsb+$honariumdsb+$premiasuransi+$natura+$thr;
					
				}
			   //respond via row
			   //tambah bulatkan nilai pph21 ==Jo 10-04-2017==
				echo"<tr class=rowcontent>
				<td class=firsttd>".$no."</td>
				<td align=center>".$bar->nik."</td>
				<td>".$bar->name."</td>
				<td align=center>".$bar->taxstatus."</td>
				<td>".$bar->npwp."</td>
				<td align=center>".$periode."</td>
				<td align=right>".number_format($brutos,2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21danthr),2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21atasthr),2,'.',',')."</td>
			   </tr>";
			}
	 }
	 
	/*if($res=mysql_query($str1,$conn))
	{
		$no=0;
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$jmsDariPrsh	 =0;
			$totalPendapatan =0;
			//default value in table is T,0,1,2,3
			//so replace other			
			$taxstatus		 =str_replace("M","",$bar->taxstatus);
			$taxstatus		 =str_replace("TK","T",$taxstatus);
			$taxstatus		 =str_replace("K","",$taxstatus);//menyisakan T untuk TK
			$taxstatus		 =str_replace("/","",$taxstatus);
			$taxstatus		 =str_replace("-","",$taxstatus);
			$taxstatus       =trim($taxstatus);
			if($jmsperusahaan=='yes'){
			//get value jamsostek perusahaan base on userid and periode
				$str="select value*-1 as jms from ".$dbname.".sdm_ho_detailmonthly
				      where karyawanid=".$bar->karyawanid." and component=3
					  and periode='".$periode."'";
			//component 3 harus potongan jms karyawan
			    $jmsKar=0;
				$byJab=0;
				$rex=mysql_query($str);
				while($bax=mysql_fetch_array($rex))
				{
					$jmsKar=$bax[0];
				}
				if($jmsKar>0)
				{
					$jmsDariPrsh=(($jmsKar/$jmsporsikar*100)*($jmsporsi/100));
				}	 
			}
			
			//total pendapatan plus jamsostek dari perusahaan
			$totalPendapatan=$jmsDariPrsh+$bar->value;
			$pendapatanBulanan=$totalPendapatan;			
			//dikurang biaya jabatan
			if(($totalPendapatan*($percenJab/100))>$maxBJab)
			    $byJab=$maxBJab;
			else
			    $byJab=	$totalPendapatan*($percenJab/100);				
	
			$totalPendapatan=$totalPendapatan-$byJab;
			//kemudian disetahunkan			
			$totalPendapatan=$totalPendapatan*12;//disetahunkan			
			//=================================
			//dikurangkan PTKP
			if (isset($arrPtkp[$taxstatus]))//jika penulisan status pajak tidak normal
             {								//maka yang dipakai adalah standard 3 anak	
			    $ptkp=$arrPtkp[$taxstatus];
			 }
			 else
			 {
			 	$ptkp=$arrPtkp['3'];
			 }
			
			$pkp=$totalPendapatan-$ptkp;;			
			//==================================
			//Kalkulasi pajak
			$pph21=Array();
			$valVol=$pkp;
			if($pkp>0)//jika penghasilan diatas PTKP
			{
				for($z=0;$z<count($arrTarif);$z++)
				{
					if($z<(count($arrTarif)-1))//pastikan bukan range yang terakhit
					{
					  if($z==0)//JIKA yang pertama
						{	    
	                      if($pkp>$arrTarifVal[$z])
						    $pph21[$z]=	($arrTarif[$z]/100)*($arrTarifVal[$z]);
						  else
						  	$pph21[$z]=$pkp*($arrTarif[$z]/100);

					    }
					 else
					 {
						if($pkp>$arrTarifVal[$z])//jika diatas yang sekarang
						  {
						  $pph21[$z]= ($arrTarif[$z]/100)*($arrTarifVal[$z]-$arrTarifVal[$z-1]);
						  }
						else if(($pkp-$arrTarifVal[$z-1])>0)//jika diatas yang sebelumnya
						  $pph21[$z]=($arrTarif[$z]/100)*($pkp-$arrTarifVal[$z-1]);
						else  
						  $pph21[$z]=0;//jika dibawah maka dianggap nol				   
					 }	
					}
					else//range diatas level terakhir
					{
						if(($pkp-$arrTarifVal[$z-1])<=0)
						$pph21[$z]=0;
						else
						$pph21[$z]=($arrTarif[$z]/100)*($pkp-$arrTarifVal[$z-1]);
					}
					//echo $pph21[$z].":".$arrTarif[$z]."-".($arrTarifVal[$z]-$arrTarifVal[$z-1])."<br>";
				}
			}
			else{
				$pphbulanan=0;
			}
			$ttlpph21=array_sum($pph21);
			$pphbulanan=$ttlpph21/12;//disebulankan
		//============================================	
		   //respond via row
		echo"<tr class=rowcontent>
		    <td class=firsttd>".$no."</td>
			<td align=center>".$bar->karyawanid."</td>
			<td>".$bar->name."</td>
			<td align=center>".$bar->taxstatus."</td>
			<td>".$bar->npwp."</td>
			<td align=center>".$periode."</td>
			<td align=right>".number_format($pendapatanBulanan,2,'.',',')."</td>
			<td align=right>".number_format($pphbulanan,2,'.',',')."</td>
		   </tr>";
		
		
				   
		}	
	}*/
	else
	{echo " Error: ".addslashes(mysql_error($conn));} 				
?>
