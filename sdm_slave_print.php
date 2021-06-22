<?php
require_once('master_validation.php');
include('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

#pre($_GET);exit();

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 

function dates_inbetween($date1, $date2){
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

$Type = isset($_GET['type']) ? $_GET['type'] : null;
$Module = isset($_GET['module']) ? $_GET['module'] : null;
$StartDate = isset($_GET['tgl01']) ? $_GET['tgl01'] : null;
$EndDate = isset($_GET['tgl02']) ? $_GET['tgl02'] : null;
$karyawanid = isset($_GET['karyawanid']) ? $_GET['karyawanid'] : null;

switch($Type){
	case 'excel':
	if($Module == 'laporankehadiran')
	{	
		$tangsys1=putertanggal($StartDate);
		$tangsys2=putertanggal($EndDate);		
		
		//ambil query untuk data karyawan
		$skaryawan="select a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from ".$dbname.".datakaryawan a 
			left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
			left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
			where a.lokasitugas like '".$_SESSION['empl']['lokasitugas']."'
			and a.karyawanid like '%".$karyawanid."%'
			order by namakaryawan asc";    
		#where a.lokasitugas like '%HO' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar='0000-00-00')	
	
		$rkaryawan=fetchData($skaryawan);
		#pre($rkaryawan);
		foreach($rkaryawan as $row => $kar)
		{
			$karyawan[$kar['karyawanid']]['id']=$kar['karyawanid'];
			$karyawan[$kar['karyawanid']]['nama']=$kar['namakaryawan'];
			$namakar[$kar['karyawanid']]=$kar['namakaryawan'];
			$jabakar[$kar['karyawanid']]=$kar['namajabatan'];
		} 
		
		//cek max hari inputan
		$tanggaltanggal = dates_inbetween($StartDate, $EndDate);
		$jumlahhari=count($tanggaltanggal);

		//karyawan ijin & cuti
		$str="SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.jenisijin, d.jenisizincuti 
			FROM ".$dbname.".sdm_ijin a
			LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
			LEFT JOIN ".$dbname.".sdm_jenis_ijin_cuti d on a.jenisijin=d.id   	
			WHERE substr(a.darijam,1,10) <= '".$tangsys2."' and substr(a.sampaijam,1,10) >= '".$tangsys1."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
			and a.karyawanid like '%".$karyawanid."%'
			ORDER BY a.darijam, a.sampaijam";
		$res=mysql_query($str);
		$TotalIjin  = 1;
		while($bar=mysql_fetch_object($res))
		{
			$presensi[$bar->karyawanid]['TotalIjin'] = $TotalIjin;
			if(substr($bar->lokasitugas,2,2)=='HO'){
				$karyawan[$bar->karyawanid][$TotalIjin]['id']=$bar->karyawanid;
				$karyawan[$bar->karyawanid][$TotalIjin]['nama']=$bar->namakaryawan;
			}  
			$presensi[$bar->karyawanid][$TotalIjin]['ijin1']=$bar->daritanggal;
			$presensi[$bar->karyawanid][$TotalIjin]['ijin2']=$bar->sampaitanggal;
			$presensi[$bar->karyawanid][$TotalIjin]['x'.$bar->daritanggal]=$bar->jenisizincuti;
			$TotalIjin++;
		}
		
		$str="SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, c.namakaryawan, a.kodeorg 
			FROM ".$dbname.".sdm_pjdinasht a
			LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
			WHERE a.tanggalperjalanan <= '".$tangsys2."' and a.tanggalkembali >= '".$tangsys1."' 
			and a.karyawanid like '%".$karyawanid."%'
			and statuspersetujuan='1' and statushrd='1'
			order by a.tanggalperjalanan, a.tanggalkembali";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			if($bar->karyawanid>''){
			if(substr($bar->kodeorg,-2)=='HO'){
				$karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
				$karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
			}    
			$presensi[$bar->karyawanid]['dinas1']=$bar->tanggalperjalanan;
			$presensi[$bar->karyawanid]['dinas2']=$bar->tanggalkembali;
			}
		}
		
		// karyawan masuk
		$str="SELECT a.karyawanid, a.tanggal, a.jam, a.jamPlg, a.absensi, b.karyawanid, b.namakaryawan, c.keterangan
		FROM ".$dbname.".sdm_absensidt a
		JOIN ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid    
		JOIN ".$dbname.".sdm_5absensi c on a.absensi=c.kodeabsen    
		WHERE a.tanggal >= '".$tangsys1."' and a.tanggal <= '".$tangsys2."' and a.karyawanid like '%".$karyawanid."%'
		ORDER BY b.namakaryawan ASC";
		//echo $str.'</br>';
		$res=mysql_query($str) or mysql_error();
		while($bar=mysql_fetch_object($res))
		{
			#pre($bar);
			if(!isset($bar->karyawanid)){

			}else{
				$karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
				$karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
				if($bar->absensi != 'DT' && $bar->absensi != 'H') {
					$presensi[$bar->karyawanid]['m'.$bar->tanggal]=$bar->keterangan;
				} else {
					$presensi[$bar->karyawanid]['m'.$bar->tanggal]=substr($bar->jam,0,5);
					$presensi[$bar->karyawanid]['k'.$bar->tanggal]=substr($bar->jamPlg,0,5);
				}
			}
		}
		
		// sort berdasarkan nama
		if(!empty($karyawan)) foreach($karyawan as $c=>$key) {
			$sort_nama[] = $key['nama'];
		}
		if(!empty($karyawan))array_multisort($sort_nama, SORT_ASC, $karyawan);

			 $bgcolor=" bgcolor=#DEDEDE";
			 $border=1;
		
		// BEGIN STREAM
		$no=0;
		$kolomtanggal=$jumlahhari+5;
		$stream = '';
		$stream.="<table class=sortable cellspacing=1 border=".$border.">";
		$stream.="<thead><tr class=rowtitle>";
		$stream.="<td rowspan=2 align=center".$bgcolor.">".$_SESSION['lang']['nourut']."</td>";
		$stream.="<td rowspan=2 align=center".$bgcolor.">".$_SESSION['lang']['namakaryawan']."</td>";
		$stream.="<td colspan=".$kolomtanggal." align=center".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>";
		$stream.="</tr>";
		$stream.="<tr class=rowtitle>";
		if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
		{
			$hari=date('D', strtotime($tang));
			$qwe=substr($tang,5,2).'/'.substr($tang,8,2);
			if($hari=='Sat'||$hari=='Sun')$qwe="<font color='#FF0000'>".$qwe."</font>";
			$stream.="<td align=center".$bgcolor.">";
			$stream.=$tang;
			$stream.="</td>";
		}    
		if($_SESSION['language']=='ID'){
		#$stream.="<td align=center".$bgcolor.">Hadir</td>";
		#$stream.="<td align=center".$bgcolor.">Telat</td>";
		#$stream.="<td align=center".$bgcolor.">Dinas</td>";
		#$stream.="<td align=center".$bgcolor.">Cuti</td>";
		#$stream.="<td align=center".$bgcolor.">Mangkir</td>";
		}else{
		#$stream.="<td align=center".$bgcolor.">Present</td>";
		#$stream.="<td align=center".$bgcolor.">Late</td>";
		#$stream.="<td align=center".$bgcolor.">Duty</td>";
		#$stream.="<td align=center".$bgcolor.">Leave</td>";
		#$stream.="<td align=center".$bgcolor.">Absence</td>";    
		}
		$stream.="</tr></thead>";
		$stream.="<tbody>";
		$Masuk = '';
		$Pulang = '';
		if(!empty($karyawan))foreach($karyawan as $kar)
		{
			$no+=1;
			$hadir=0;
			$telat=0;
			$cuti=0;
			$dinas=0;
			$mangkir=0;
			$stream.="<tr class=rowcontent>";
			$stream.="<td align=right>".number_format($no).".</td>";    
			$stream.="<td>".$kar['nama']."</td>";    
			if(!empty($tanggaltanggal))foreach($tanggaltanggal as $tang)
			{    
				$hari=date('D', strtotime($tang));
				$pres='';
				#pre($presensi[$kar['id']]);
				if(isset($presensi[$kar['id']]['TotalIjin'])){
					#	pre($presensi[$kar['id']]);
					for($a=1; $a <= $presensi[$kar['id']]['TotalIjin']; $a++){
						if(($presensi[$kar['id']][$a]['ijin1']<=$tang)&&($presensi[$kar['id']][$a]['ijin2']>=$tang)){
							#echo "A";
							if($hari!='Sat'&&$hari!='Sun')$pres=$presensi[$kar['id']][$a]['x'.$presensi[$kar['id']][$a]['ijin1']];
							if($hari!='Sat'&&$hari!='Sun')$cuti+=1;
						}
					}
				}
				#echo($pres);
				if(isset($presensi[$kar['id']]['dinas1'])){
					if(($presensi[$kar['id']]['dinas1']<=$tang)&&($presensi[$kar['id']]['dinas2']>=$tang)){
						$pres='DINAS';
					}
				}

				if(isset($presensi[$kar['id']]['m'.$tang])||isset($presensi[$kar['id']]['k'.$tang])){
					$ontime=true;
					if(isset($presensi[$kar['id']]['m'.$tang])){
						if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
							if(substr($presensi[$kar['id']]['m'.$tang],0,5)<=$Masuk){ // masuk ontime
								$pres='&nbsp;'.[$kar['id']]['m'.$tang];                
							}else{
								$pres='&nbsp;'.$presensi[$kar['id']]['m'.$tang].'';
								$ontime=false;
							}
						}else{
							if(substr($presensi[$kar['id']]['m'.$tang],0,5)<=$Masuk){ // masuk ontime
								$pres='&nbsp;'.$presensi[$kar['id']]['m'.$tang];                
							}else{
								$pres='&nbsp;'.$presensi[$kar['id']]['m'.$tang].'';
								$ontime=false;
							}
						}                
					} else $ontime=false;
					if(isset($presensi[$kar['id']]['k'.$tang])){
						if(($tang>='2013-07-09')and($tang<='2013-08-08')){              // puasa 2013
							if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$Pulang){ // pulang ontime
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5);
							}else{
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'';
								$ontime=false;
							}            
						}else
						if($tang=='2013-10-14'){                                        // idul adha 2013 -1
							if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$Pulang){ // pulang ontime
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5);
							}else{
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'';
								$ontime=false;
							}            
						}else{
							if(substr($presensi[$kar['id']]['k'.$tang],0,5)>=$Pulang){ // pulang ontime
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5);
							}else{
								$pres.='</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang],0,5).'';
								$ontime=false;
							}            
						}
					} else $ontime=false;
					if($ontime)$hadir+=1; else $telat+=1;
				}

				if($hari=='Sat'||$hari=='Sun'){
					$bgcolor=" bgcolor='#FFCCCC'";
					if($pres=='')$pres=' ';
				}else{
					$bgcolor="";
				}

				if($pres=='DINAS')$dinas+=1;

				if($pres=='')$mangkir+=1;

				$stream.="<td valign=top align=center".$bgcolor.">".$pres."</td>";    
			}
			#$stream.="<td align=right>".$hadir."</td>";
			#$stream.="<td align=right>".$telat."</td>";
			#$stream.="<td align=right>".$dinas."</td>";
			#$stream.="<td align=right>".$cuti."</td>";
			#$stream.="<td align=right>".$mangkir."</td>";
			$stream.="</tr>";     
		} 
		$stream.="</tbody></table>";
		
		if ($handle = opendir('tempExcel')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {@unlink('tempExcel/'.$file);}
			}	
			closedir($handle);
		}
		$judul = 'Laporan Absensi';
		$handle=fopen("tempExcel/".$judul.".xls",'w');
		if(!fwrite($handle,$stream)){
			echo "<script language=javascript1.2>
			parent.window.alert('Can't convert to excel format');
			</script>";
			exit;
		} else {
			echo "<script language=javascript1.2>window.location='tempExcel/".$judul.".xls';</script>";
		}
		#closedir($handle);
	} else {
		echo "Error Module";
	}
	break;
	default:
	echo "Error Case";
	break;
}
?>