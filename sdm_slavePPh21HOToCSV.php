<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/eksilib.php');

$jenis  =$_GET['jenis'];
$periode=$_GET['periode'];
$regular=$_GET['regular'];
$thr	 =$_GET['thr'];
$jaspro =$_GET['jaspro'];
$jmsperusahaan =$_GET['jmsperusahaan'];
$kodeorg=$_POST['kodeorg'];

//untuk kode objek pajak ==Jo 18-06-2017==
$slkdpph="select nama from setup_5parameter where flag='sdmpph' and kode='kdpph'";
$reskdpph=$eksi->sSQL($slkdpph);
foreach($reskdpph as $barkdpph){
 $kdobjpj=$barkdpph['nama'];
}
//untuk kode bukan wajib pajak luar negeri ==Jo 24-07-2017==
$slwpl="select nama from setup_5parameter where flag='sdmpph' and kode='kdwpl'";
$reswpl=$eksi->sSQL($slwpl);
foreach($reswpl as $barwpl){
	$kdwpl=$barwpl['nama'];
}

//untuk nama,npwp di identitas pemotong ==Jo 24-07-2017==
$slnik="select nama from setup_5parameter where flag='sdmpph' and kode='nikpmt'";
$resnik=$eksi->sSQL($slnik);
foreach($resnik as $barnik){
	$nikpmt=$barnik['nama'];
}
$slpmt="select namakaryawan, npwp from datakaryawan where nik='".$nikpmt."'";
$respmt=$eksi->sSQL($slpmt);
foreach($respmt as $barpmt){
	$namapmt=$barpmt['namakaryawan'];
	$npwppmt=$barpmt['npwp'];
}

//cari dulu component bonus ==Jo 17-03-2017==
$slbonus="select id from sdm_ho_component where isBNS=1";
$resbonus=$eksi->sSQL($slbonus);
foreach($resbonus as $barbonus){
	$idbonus=$barbonus['id'];
}

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

//untuk memisahkan tahun dan bulan dari periode ==Jo 10-04-2017==
 $tahunbulan=Array();
 $tahunbulan=explode("-",$periode);
if($jenis=='bulanan')//bulanan
{
	//lepas and e.operator='".$opuser."' ==Jo 17-03-2017==
	$str1="select f.nik,e.karyawanid,e.npwp,e.taxstatus,e.name, a.netto, a.pph21danbonusthr,
			case when date_format(STR_TO_DATE(e.firstpayment,'%Y'),'%Y')='".date('Y',strtotime($periode))."'
			then e.firstpayment
			else
			'".date('Y',strtotime($periode))."-01'
			end 
			as masaawal,
			case when date_format(STR_TO_DATE(e.lastpayment,'%Y'),'%Y')='".date('Y',strtotime($periode))."'
			then e.lastpayment
			else
			'".date('Y',strtotime($periode))."-12'
			end 
			as masaakhir,  f.isPPHGrossUp
			from ".$dbname.".sdm_ho_employee e 
			left join ".$dbname.".sdm_pph21_data a on e.karyawanid = a.karyawanid and e.operator<>''
			left join ".$dbname.".datakaryawan f on e.karyawanid =f.karyawanid
			 where a.periode='".$periode."' and e.firstpayment<='".$periode."' and (e.lastpayment>='".$periode."' or e.lastpayment='' or e.lastpayment IS NULL)
			 and e.operator<>'' ".$whrorg."
			 group by a.karyawanid";  
		 $res=$eksi->sSQL($str1);
		  /*$stream="PPh21 Periode :".$periode."
			 <table border=1>
			 <thead>
			   <tr bgcolor=#DFDFDF>
				<td>No.</td>
				<td>".$_SESSION['lang']['nik']."</td>
				<td>".$_SESSION['lang']['employeename']."</td>
				<td>Status</td>
				<td>N.P.W.P</td>
				<td>Periode</td>
				<td>Sumber</td>
				<td>PPh21</td>
			   </tr>
			 </thead><tbody id=tbody>";*/
		 $stream="Masa Pajak;Tahun Pajak;Pembetulan;NPWP;Nama;Kode Pajak;Jumlah Bruto;Jumlah PPh;Kode Negara".chr(13).chr(10);
		
		 $no=0;
		foreach($res as $bar)
		{
		 
			 if($bar['isPPHGrossUp']==1){
				 $tbbrutos="setup_pkp_grossup";
			 }
			 else{
				 $tbbrutos="setup_pkp_non_grossup"; 
			 }
			
				$no+=1;
				$gapok=0;
			   $tunjangandsb=0;
			   $premiasuransi=0;
			   $bonusthr=0;
				$brutos=0;
				$tjpph21=0;
			   //ganti netto jadi bruto ==Jo 25-07-2017==
				if($bar['isPPHGrossUp']==1){
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
				where a.pph21 = 1 and b.karyawanid='".$bar['karyawanid']."'";
					
				$respph=$eksi->sSQL($strpph);
				
				foreach($respph as $barpph){
					$gapok=$barpph['gajipokok'];
					$tunjangandsb=($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen']);
					
					$honariumdsb=0;
					$premiasuransi=$barpph['premiasuransi'];
					$natura=0;
					$bonusthr=$barpph['bonus']+$barpph['thr'];
					
					$brutos=$gapok+$tjpph21+$tunjangandsb+$honariumdsb+$premiasuransi+$natura+$bonusthr;
					
				}
			$stream.="".$tahunbulan[1].";".$tahunbulan[0].";0;".str_replace(" ","",str_replace("-","",str_replace(".","",$bar['npwp']))).";".$bar['name'].";".$kdobjpj.";".$brutos.";".round($bar['pph21danbonusthr']).";".chr(13).chr(10);
		
		 			
		}
		
		$nop_='PPh21'.$jenis."-".$periode;		
}
else if($jenis=='tahunan')//tahunan
{
	$strType='';
	if($regular=='yes')
	{
		$strType.=" type = 'regular'";
	}
	if($thr=='yes')
	{
		$strType.=" or type = 'thr'";
	}
	if($jaspro=='yes')
	{
		$strType.=" or type = 'jaspro'";
	}
	//get Component
	$arrComp=Array();
	$str="select id from ".$dbname.".sdm_ho_component
		  where `pph21`=1 and `lock`=1 order by id";	  
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
		$maxBJab=$baru->max*12;//di setahunkan
	}
	
	//lepas and e.operator='".$opuser."' ==Jo 17-03-2017==
	$str1="select f.nik,f.alamataktif,f.jeniskelamin, f.statuspajak, f.jumlahtanggungan, i.namajabatan, e.karyawanid,e.npwp,e.taxstatus,e.name, sum(a.nettodanbonusthr) as nettodanbonusthr, sum(a.pph21danbonusthr) as pph21danbonusthr,e.lastpayment,
				
				case when date_format(STR_TO_DATE(e.firstpayment,'%Y'),'%Y')='".date('Y',strtotime($periode))."'
				then e.firstpayment
				else
				'".date('Y',strtotime($periode))."-01'
				end 
				as masaawal,
				case when date_format(STR_TO_DATE(e.lastpayment,'%Y'),'%Y')='".date('Y',strtotime($periode))."'
				then e.lastpayment
				else
				'".date('Y',strtotime($periode))."-12'
				end 
				as masaakhir, f.isPPHGrossUp
				 from ".$dbname.".sdm_ho_employee e 
				inner join ".$dbname.".sdm_pph21_data a on e.karyawanid = a.karyawanid
				 and a.periode like '".$periode."%'
				 left join ".$dbname.".datakaryawan f on e.karyawanid =f.karyawanid
				 left join ".$dbname.".sdm_5jabatan i on f.kodejabatan =i.kodejabatan
				 where date_format(STR_TO_DATE(e.firstpayment,'%Y'),'%Y')<='".$periode."' and  ((date_format(STR_TO_DATE(e.lastpayment,'%Y'),'%Y')>='".$periode."') or e.lastpayment='' or e.lastpayment IS NULL )
				 and e.operator<>'' ".$whrorg."
				 group by a.karyawanid"; 
		
		
		 $stream="Masa Pajak;Tahun Pajak;Pembetulan;Nomor Bukti Potong;Masa Perolehan Awal;Masa Perolehan Akhir;NPWP;NIK;Nama;Alamat;Jenis Kelamin;Status PTKP;Jumlah Tanggungan;Nama Jabatan;WP Luar Negeri;Kode Negara;Kode Pajak;Jumlah 1;Jumlah 2;Jumlah 3;Jumlah 4;Jumlah 5;Jumlah 6;Jumlah 7;Jumlah 8;Jumlah 9;Jumlah 10;Jumlah 11;Jumlah 12;Jumlah 13;Jumlah 14;Jumlah 15;Jumlah 16;Jumlah 17;Jumlah 18;Jumlah 19;Jumlah 20;Status Pindah;NPWP Pemotong;Nama Pemotong;Tanggal Bukti Potong".chr(13).chr(10);


			 
			$no=0;
			$res=$eksi->sSQL($str1);
			$tjpph21=0;
		foreach($res as $bar)
		{
			if($bar['isPPHGrossUp']==1){
				$tbbrutos="setup_pkp_grossup";
				//$tjpph21=round($bar['pph21']);
			 }
			 else{
				$tbbrutos="setup_pkp_non_grossup"; 
			 }
			 $no+=1;
			 
			 
			 //untuk nilai pph21
			/*$slnpph="select pph21danbonusthr from sdm_pph21_data where karyawanid='".$bar['karyawanid']."' and periode='".$periode."-12'";
			$npph=$eksi->sSQLnum($slnpph);
			if($npph>0){
				$resnpph=$eksi->sSQL($slnpph);
				foreach($resnpph as $barnpph){
					$pph21s=$barnpph['pph21danbonusthr'];
				}
			} 
			else if(date('Y',strtotime($periode))==date('Y',strtotime($bar['lastpayment']))){
				$slnilai="select pph21danbonusthr from sdm_pph21_data where karyawanid='".$bar['karyawanid']."' and periode='".$bar['lastpayment']."'";
				$jmkueri=$eksi->sSQLnum($slnilai);
				if($jmkueri>0){
					$resnilai=$eksi->sSQL($slnilai);
					foreach($resnilai as $barnilai){
						$pph21s=$barnilai['pph21danbonusthr'];
					}
				}
				else{
					$pph21s=$bar['pph21danbonusthr'];
				}
				
			}
			else{
				$pph21s=$bar['pph21danbonusthr'];
			}*/
			
			$slnpph="select sum(pph21danbonusthr) as pph21danbonusthr  from sdm_pph21_data where karyawanid='".$bar['karyawanid']."' and periode like'".$periode."%'";
			$resnpph=$eksi->sSQL($slnpph);
			foreach($resnpph as $barnpph){
				$pph21s=$barnpph['pph21danbonusthr'];
			}
			
			if($bar['isPPHGrossUp']==1){
				
				$tjpph21=round($pph21s);
			}
			 /*$stream="PPh21 Periode :".$periode."
			 <table border=1>
			 <thead>
			   <tr bgcolor=#DFDFDF>
				<td>No.</td>
				<td>".$_SESSION['lang']['nik']."</td>
				<td>".$_SESSION['lang']['employeename']."</td>
				<td>Status</td>
				<td>N.P.W.P</td>
				<td>Periode</td>
				<td>Sumber</td>
				<td>PPh21</td>
			   </tr>
			 </thead><tbody id=tbody>";*/
			  
				
			//============================================	
			   //respond via row
			/*$stream.="<tr>
				<td>".$no."</td>
				<td align=center>".$bar->nik."</td>
				<td>".$bar->name."</td>
				<td align=center>".$bar->taxstatus."</td>
				<td>'".$bar->npwp."</td>
				<td align=center>".$periode."</td>
				<td align=right>".number_format($bar->nettodanbonusthr,2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21danbonusthr),2,'.',',')."</td>
			   </tr>";		*/
			
			//untuk ambil nilai detail gaji ==Jo 11-04-2017==
			$strpph="select 
			((select(case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)>date_format(STR_TO_DATE(h.firstpayment,'%Y-%m'),'%Y-%m')) then 
			case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m')) then h.lastvol else 100 end 
			else h.firstvol end)/100)*(sum(case when a.isIP = 0 and a.isGP=1 then ifnull(b.value,0) else 0 end))) as gajipokok,
			((select(case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)>date_format(STR_TO_DATE(h.firstpayment,'%Y-%m'),'%Y-%m')) then 
			case when ((select date_format(STR_TO_DATE(x.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji x WHERE x.jenisgaji='B' AND x.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  x.sudahproses=0 ORDER BY x.periode asc LIMIT 1)=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m')) then h.lastvol else 100 end 
			else h.firstvol end)/100)*(sum(case when (a.isIP = 0 and a.isGP=0 and a.isTJ=1) or (a.isGP=0 and a.pph21=1 and isTJ=0 and a.isBNS=0 and a.isLembur=0 and a.isTHR=0 and a.isIP=0 and a.isIPL=0 and a.isPotongan=0 and a.isAngsuran=0) or (a.pph21=1 and a.isIncentive=1) then ifnull(b.value,0) else 0 end))) as tunjangan,
			sum(case when a.isIPL = 1  then ifnull(b.value,0) else 0 end) as premiasuransi,
			sum(case when a.isIP = 1 then ifnull(b.value,0)  else 0 end) as iuranpengurang,
			sum(case when (a.isIP = 0 and a.isLembur=1) then ifnull(b.value,0) else 0 end) as lembur,
			sum(case when (a.isIP = 0 and a.isBNS=1) then ifnull(b.value,0) else 0 end) as bonus,
			sum(case when (a.isIP = 0 and a.isTHR=1) then ifnull(b.value,0) else 0 end) as thr,
			sum(case when a.isPotongan = 1 then ifnull(b.value,0)  else 0 end) as potpen,
			c.persen, c.max  as maks,
			(select case when ifnull(h.lastpayment,'')<>''then
			(select case 
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') = (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%m') != 12) 
			then date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%m') - date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%m')
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  != 12
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  = date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%m'))
			then date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%m')
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  = 12 
			and date_format(STR_TO_DATE('".$periode."','%Y-%m'),'%Y')!=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y'))
			then 12
			/*tambah kondisi untuk resign tahun itu (khusus laporan)==JO 03-08-2017*/
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  = 12 and date_format(STR_TO_DATE('".$periode."','%Y-%m'),'%Y')=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y'))
			then date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%m')			
			/*tambah kondisi untuk resign tengah bulan tapi bukan tahun itu ==JO 15-03-2017==*/
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  != date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m'))
			then
			12
			else 0 end)
			else 
			(select case 
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') = (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			) 
			then 12-(select date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%m'))+1
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  = 12) 
			then 12
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') = (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  != 12) 
			then 12
			when (date_format(STR_TO_DATE(ifnull(h.firstpayment, '0000-00'),'%Y-%m'),'%Y') != (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1) 
			and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  != 12) 
			then 12
			else 0 end)
			end) as pengali,
			
			ifnull((select sum(value) from sdm_ho_detailmonthly where component = 
			(select id from sdm_ho_component where isBNS=1) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as bonustahunini,
			
			ifnull((select sum(value) from sdm_ho_detailmonthly where component = 
			(select id from sdm_ho_component where isTHR=1) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as thrtahunini,
			
			ifnull((select sum(value*-1) from sdm_ho_detailmonthly where component in 
			(select id from sdm_ho_component where pph21=1 and isPotongan=1) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as pottahunini,
			
			ifnull((select sum(value) from sdm_ho_detailmonthly where component = 
			(select id from sdm_ho_component where pph21=1 and isLembur=1) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as lemburtahunini,
			
			ifnull((select sum(value) from sdm_ho_detailmonthly where component = 
			(select id from sdm_ho_component where pph21=1 and isIncentive=1) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as incentivethnini,
			
			ifnull((select sum(value) from sdm_ho_detailmonthly where component = 
			(select id from sdm_ho_component where pph21=1 and isJP=0 and isGP=0 and isTJ=0 and isBNS=0 and isLembur=0 and isTHR=0 and isIP=0 and isIPL=0 and isPPH21Result=0 and isPotongan=0 and isAngsuran=0 and isBPJS=0 and isSumbangan=0 and isIncentive=0) and periode like 
			concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr(`d`.`lokasitugas`,1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%') and substr(periode,6,7) <> '12' and periode <> h.lastpayment
			and karyawanid = `d`.`karyawanid`),0) as lainnyathnini,
			
			
			e.value as ptkp, d.npwp,  ifnull(h.firstpayment,0) as firstpayment, ifnull(h.firstvol,0) as firstvol, ifnull(h.lastpayment,0) as lastpayment, ifnull(h.lastvol,0) as lastvol

			from sdm_ho_component a 
			inner join sdm_ho_basicsalary b on a.id = b.component
			join sdm_ho_pph21jabatan c
			left join datakaryawan d on b.karyawanid = d.karyawanid
			left join sdm_ho_pph21_ptkp e on  d.statuspajak = e.kode
			left join organisasi g on d.kodeorganisasi=g.kodeorganisasi
			left join sdm_ho_employee h on d.karyawanid=h.karyawanid 
			where a.pph21 = 1 and b.karyawanid='".$bar['karyawanid']."'";
			//inner join sdm_ho_detailmonthly b on a.id = b.component and b.periode like'".date('Y',strtotime($periode))."%'
			$respph=$eksi->sSQL($strpph);
			$brutottl=0;
			$byjbt=0;
			$iuranpensiun=0;
			$jmpengurang=0;
			foreach($respph as $barpph){
				if(date('Y',strtotime($periode))==date('Y',strtotime($barpph['firstpayment'])) && date('Y',strtotime($periode))!=date('Y',strtotime($barpph['lastpayment']))){
					$gapok=($barpph['gajipokok']*($barpph['pengali']-1))+($barpph['gajipokok']*$barpph['firstvol']/100);
					$tunjangandsb=(($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen'])*($barpph['pengali']-1))+(($barpph['tunjangan']*$barpph['firstvol']/100) + $barpph['lembur'] - $barpph['potpen']);
				}
				else if(date('Y',strtotime($periode))!=date('Y',strtotime($barpph['firstpayment'])) && date('Y',strtotime($periode))==date('Y',strtotime($barpph['lastpayment']))){
					$gapok=($barpph['gajipokok']*($barpph['pengali']-1))+($barpph['gajipokok']*($barpph['lastvol']/100));
					$tunjangandsb=(($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen'])*($barpph['pengali']-1))+(($barpph['tunjangan']*($barpph['lastvol']/100)) + $barpph['lembur'] - $barpph['potpen']);
				}
				else if(date('Y',strtotime($periode))==date('Y',strtotime($barpph['firstpayment'])) && date('Y',strtotime($periode))==date('Y',strtotime($barpph['lastpayment']))){
					$gapok=($barpph['gajipokok']*($barpph['pengali']-2))+($barpph['gajipokok']*($barpph['lastvol']/100));
					$tunjangandsb=(($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen'])*($barpph['pengali']-2))+(($barpph['tunjangan']*$barpph['firstvol']/100) + $barpph['lembur'] - $barpph['potpen'])+(($barpph['tunjangan']*($barpph['lastvol']/100)) + $barpph['lembur'] - $barpph['potpen']);
				}
				else{
					$gapok=$barpph['gajipokok']*$barpph['pengali'];
					$tunjangandsb=($barpph['tunjangan'] + $barpph['lembur'] - $barpph['potpen'])*$barpph['pengali'];
				}
				
				$honariumdsb=0;
				$premiasuransi=$barpph['premiasuransi']*$barpph['pengali'];
				$natura=0;
				$bonusthr=$barpph['bonus']+$barpph['thr']+$barpph['bonustahunini']+$barpph['thrtahunini'];
				$bonusthrs=$barpph['bonus']+$barpph['bonustahunini'];
				
				$brutottl=$gapok+ $barpph['lemburtahunini'] -  $barpph['pottahunini'] + $barpph['incentivethnini'] + $barpph['lainnyathnini']+$tunjangandsb+$honariumdsb+$premiasuransi+$natura+$bonusthr;
			
				
				$honariumdsb=0;
				$premiasuransi=$barpph['premiasuransi']*$barpph['pengali'];
				$natura=0;
				$bonusthr=$barpph['bonus']+$barpph['thr']+$barpph['bonustahunini']+$barpph['thrtahunini'];
				
				$brutottl=$gapok+$tjpph21+ $barpph['lemburtahunini'] -  $barpph['pottahunini'] + $barpph['incentivethnini'] + $barpph['lainnyathnini']+$tunjangandsb+$honariumdsb+$premiasuransi+$natura+$bonusthr;
				if (($brutottl*($barpph['persen']/100)) >= ($barpph['maks']*$barpph['pengali'])){
					$byjbt = ($barpph['maks']*$barpph['pengali']);
				}
				else {
					$byjbt = $brutottl*($barpph['persen']/100);
				}
				$iuranpensiun=($barpph['iuranpengurang']*$barpph['pengali']);
				
				$jmpengurang=$byjbt+$iuranpensiun;
				
				$pengalix=$barpph['pengali'];
				
				$jmnetto=($brutottl-$jmpengurang);
				$ptkps=$barpph['ptkp'];
				$pkps=$jmnetto-$barpph['ptkp'];
			}
			
			/*$slbruto="select sum(gajibrutosetahundanbonusthr)  as brutos,ptkp from ".$tbbrutos." where karyawanid='".$bar['karyawanid']."' and periode like '".date('Y',strtotime($periode))."%'";
			$resbruto=$eksi->sSQL($slbruto);
			$brutos=0;
			$ptkps=0;
			$pkps=0;
			foreach($resbruto as $barbruto){
				$brutos=$barbruto['brutos'];
				$ptkps=$barbruto['ptkp'];
			}
			
			$jmnetto=$bar['nettodanbonusthr'];
			$pkps=$jmnetto-$ptkps;*/
			//echo "warning: ".$bar['karyawanid']." ".$gapok." ".$tunjangandsb." ".$honariumdsb." ".$premiasuransi." ".$bonusthr." ".$barpph['brutodanbtnm']."///";
			
			//untuk nomor bukti potong ==Jo 24-07-2017==
			$nmrbkpt="";
			//untuk bulan awal
			if(date('Y',strtotime($periode))==date('Y',strtotime($barpph['firstpayment']))){
				if(intval(date('m',strtotime($barpph['firstpayment'])))<10){
					$blnawal=date('m',strtotime($barpph['firstpayment']));
				}
				else{
					$blnawal=intval(date('m',strtotime($barpph['firstpayment'])));
				}
			}
			else{
				$blnawal="1";
			}
			//untuk bulan akhir
			if(date('Y',strtotime($periode))==date('Y',strtotime($barpph['lastpayment']))){
				if(intval(date('m',strtotime($barpph['lastpayment'])))<10){
					$blnakhir=date('m',strtotime($barpph['lastpayment']));
				}
				else{
					$blnakhir=intval(date('m',strtotime($barpph['lastpayment'])));
				}
				
				
			}
			else{
				$blnakhir="12";
				//untuk nilai pph21
			}
			
			//bulan awal  pasti 1
			$blnawals=1;
			//untuk tahun
			$thns=substr(date('Y',strtotime($periode)),strlen(date('Y',strtotime($periode)))-2,strlen(date('Y',strtotime($periode))));
			//untuk no urut ambil 7 karakter terakhir karyawanid
			$nourut=substr($bar['karyawanid'],strlen($bar['karyawanid'])-7,strlen($bar['karyawanid']));
			$nmrbkpt="1.".intval($blnawals)."-".$blnakhir.".".$thns."-".$nourut;
			
			//untuk Jenis Kelamin
			if($bar['jeniskelamin']=='L'){
				$jnsklm='M';
			}
			else{
				$jnsklm='F';
			}
			
			//
			
			$stream.="12;".$tahunbulan[0].";0;".$nmrbkpt.";".intval($blnawal).";".$blnakhir.";".str_replace(" ","",str_replace("-","",str_replace(".","",$bar['npwp']))).";".$bar['nik'].";".$bar['name'].";".str_replace(";",":",str_replace(","," ",$bar['alamataktif'])).";".$jnsklm.";".preg_replace('/[0-9]+/', '', $bar['statuspajak']).";".$bar['jumlahtanggungan'].";".$bar['namajabatan'].";".$kdwpl.";;".$kdobjpj.";".$gapok.";".$tjpph21.";".$tunjangandsb.";0;".$premiasuransi.";".$natura.";".$bonusthr.";".$brutottl.";".$byjbt.";".$iuranpensiun.";".$jmpengurang.";".$jmnetto.";0;".$jmnetto.";".$ptkps.";".$pkps.";".round($pph21s).";0;".round($pph21s).";".round($pph21s).";;".str_replace(" ","",str_replace("-","",str_replace(".","",$npwppmt))).";".$namapmt.";".date('d/m/Y')."".chr(13).chr(10);
				
		}
		
		$nop_='PPh21'.$jenis."-".$periode;		  
}
else if($jenis=='bonus')//bonus
{
	//cari dulu component bonus ==Jo 17-03-2017==
	$slbonus="select id from sdm_ho_component where isBNS=1";
	$resbonus=$eksi->sSQL($slbonus);
	foreach($resbonus as $barbonus){
		$idbonus=$barbonus['id'];
	}
	//lepas and e.operator='".$opuser."' ==Jo 17-03-2017==	
	$str1="select f.nik,e.karyawanid,e.npwp,e.taxstatus,e.name, a.nettodanbonus, a.pph21danbonus, a.pph21atasbonus
				 from ".$dbname.".sdm_ho_employee e 
				left join ".$dbname.".sdm_pph21_data a on e.karyawanid = a.karyawanid and e.operator<>''
				left join ".$dbname.".datakaryawan f on e.karyawanid =f.karyawanid
				left join ".$dbname.".sdm_ho_detailmonthly g on a.periode =g.periode and e.karyawanid=g.karyawanid and g.component=".$idbonus."
				 where a.periode='".$periode."' and e.firstpayment<='".$periode."' and and (e.lastpayment>='".$periode."' or e.lastpayment='' or e.lastpayment IS NULL) and g.value>0 
				 group by a.karyawanid";  
		 
		if($res=mysql_query($str1,$conn))
		{
		  /*$stream="PPh21 Periode :".$periode."
			 <table border=1>
			 <thead>
			   <tr bgcolor=#DFDFDF>
				<td>No.</td>
				<td>".$_SESSION['lang']['nik']."</td>
				<td>".$_SESSION['lang']['employeename']."</td>
				<td>Status</td>
				<td>N.P.W.P</td>
				<td>Periode</td>
				<td>Sumber</td>
				<td>PPh21</td>
				<td>PPh21 Atas Bonus</td>
			   </tr>
			 </thead><tbody id=tbody>";*/
			$stream="No;".$_SESSION['lang']['nik'].";".$_SESSION['lang']['employeename'].";Status;N.P.W.P;Periode;Sumber;PPh21;PPh21 Atas Bonus;\n";
			$no=0;
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				
			//============================================	
			   //respond via row
			/*$stream.="<tr>
				<td>".$no."</td>
				<td align=center>".$bar->nik."</td>
				<td>".$bar->name."</td>
				<td align=center>".$bar->taxstatus."</td>
				<td>'".$bar->npwp."</td>
				<td align=center>".$periode."</td>
				<td align=right>".number_format($bar->nettodanbonus,2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21danbonus),2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21atasbonus),2,'.',',')."</td>
			   </tr>";*/	
			   $slbruto="select gajibrutosetahundanbonus/pengali  as brutos from ".$tbbrutos." where karyawanid='".$bar->karyawanid."' and periode='".$periode."'";
				$resbruto=$eksi->sSQL($slbruto);
				$brutos=0;
				$ptkps=0;
				$pkps=0;
				foreach($resbruto as $barbruto){
					$brutos=$barbruto['brutos'];
				}
				$stream.="".$no.";".$bar->nik.";".$bar->name.";".$bar->taxstatus.";".$bar->npwp.";".$periode.";".$brutos.";".round($bar->pph21danbonus).";".round($bar->pph21atasbonus).";\n";
			}
		 		
		}
		else
		{echo " Error: ".addslashes(mysql_error($conn));} 
		$nop_='PPh21'.$jenis."-".$periode;		
}
else if($jenis=='thr')//thr
{
	 //cari dulu component thr ==Jo 17-03-2017==
	$slthr="select id from sdm_ho_component where isTHR=1";
	$resthr=$eksi->sSQL($slthr);
	foreach($resthr as $barthr){
		$idthr=$barthr['id'];
	}
	//lepas and e.operator='".$opuser."' ==Jo 17-03-2017==
	$str1="select f.nik,e.karyawanid,e.npwp,e.taxstatus,e.name, a.nettodanthr, a.pph21danthr, a.pph21atasthr
				 from ".$dbname.".sdm_ho_employee e 
				left join ".$dbname.".sdm_pph21_data a on e.karyawanid = a.karyawanid and e.operator<>''
				left join ".$dbname.".datakaryawan f on e.karyawanid =f.karyawanid
				left join ".$dbname.".sdm_ho_detailmonthly g on a.periode =g.periode and e.karyawanid=g.karyawanid and g.component=".$idthr."
				 where a.periode='".$periode."' and e.firstpayment<='".$periode."' and and (e.lastpayment>='".$periode."' or e.lastpayment='' or e.lastpayment IS NULL) and g.value>0 ".$whrorg."
				 group by a.karyawanid";  
		 
		if($res=mysql_query($str1,$conn))
		{
		  /*$stream="PPh21 Periode :".$periode."
			 <table border=1>
			 <thead>
			   <tr bgcolor=#DFDFDF>
				<td>No.</td>
				<td>".$_SESSION['lang']['nik']."</td>
				<td>".$_SESSION['lang']['employeename']."</td>
				<td>Status</td>
				<td>N.P.W.P</td>
				<td>Periode</td>
				<td>Sumber</td>
				<td>PPh21</td>
				<td>PPh21 Atas THR</td>
			   </tr>
			 </thead><tbody id=tbody>";*/
			 $stream="No;".$_SESSION['lang']['nik'].";".$_SESSION['lang']['employeename'].";Status;N.P.W.P;Periode;Sumber;PPh21;PPh21 Atas THR;\n";
			$no=0;
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				
			//============================================	
			   //respond via row
			/*$stream.="<tr>
				<td>".$no."</td>
				<td align=center>".$bar->nik."</td>
				<td>".$bar->name."</td>
				<td align=center>".$bar->taxstatus."</td>
				<td>'".$bar->npwp."</td>
				<td align=center>".$periode."</td>
				<td align=right>".number_format($bar->nettodanthr,2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21danthr),2,'.',',')."</td>
				<td align=right>".number_format(round($bar->pph21atasthr),2,'.',',')."</td>
			   </tr>";*/
			$stream.="".$no.";".$bar->nik.";".$bar->name.";".$bar->taxstatus.";".$bar->npwp.";".$periode.";".$bar->nettodanthr.";".round($bar->pph21danthr).";".round($bar->pph21atasthr).";\n";
			}
				
		}
		else
		{echo " Error: ".addslashes(mysql_error($conn));} 
		$nop_='PPh21'.$jenis."-".$periode;		
}

//write csv   
	if(strlen($stream)>0)
	{
	if ($handle = opendir('tempExcel')) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != "..") {
	            @unlink('tempExcel/'.$file);
	        }
	    }	
	   closedir($handle);
	}
	 $handle=fopen("tempExcel/".$nop_.".csv",'w');
	 if(!fwrite($handle,$stream))
	 {
	  echo "<script language=javascript1.2>
	        parent.window.alert('Can't convert to csv format');
	        </script>";
	   exit;
	 }
	 else
	 {
	  echo "<script language=javascript1.2>
	        window.location='tempExcel/".$nop_.".csv';
	        </script>";
	 }
	closedir($handle);
	}	
?>
