<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
require_once('lib/eksilib.php');

$val1=trim($_GET['periode']);	
$val=substr($val1,3,4)."-".substr($val1,0,2);
$sljkk="select bpjs_jkk_id from organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$resjkk=$eksi->sSQL($sljkk);
foreach($resjkk as $barjkk){
	$jkkid=$barjkk['bpjs_jkk_id'];
}
$sljkks="select jmsid from sdm_jenis_bpjs_jkk where id=".$jkkid."";
$resjkks=$eksi->sSQL($sljkks);
foreach($resjkks as $barjkks){
	$jkkids=$barjkks['jmsid'];
}

//filter lokasi tugas ==Jo 17-03-2017==
if($_SESSION['org']['kodepusat']==$_SESSION['empl']['lokasitugas']){
	$loktug="";
}
else {
	$loktug="and c.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}

$sGapok="select a.karyawanid, a.periode,sum(CASE WHEN a.plus=1 and f.bpjsjenisid=3 and f.id='".$jkkids."' THEN
			(f.value/(f.value+(select value from sdm_ho_hr_jms_porsi where bpjsjenisid=3 and isjk=1)))*a.`value` ELSE 0 END) as jkkval,sum(CASE WHEN a.plus=1 and f.bpjsjenisid=3 and f.isjk=1  THEN (f.value/(f.value+(select value from sdm_ho_hr_jms_porsi where bpjsjenisid=3 and id='".$jkkids."')))*a.`value` ELSE 0 END) as jkmval, sum(CASE WHEN a.plus=1 and f.bpjsjenisid=3 and f.isjht=1  THEN a.`value` ELSE 0 END) as jhtpt, sum(CASE WHEN a.plus=0 and f.bpjsjenisid=3 and f.isjht=1  THEN -1*a.`value` ELSE 0 END) as jhtkry, sum(CASE WHEN a.plus=0 and f.bpjsjenisid=2  THEN -1*a.`value` ELSE 0 END) as jpkry, sum(CASE WHEN a.plus=1 and f.bpjsjenisid=2  THEN a.`value` ELSE 0 END) as jppt, sum(CASE WHEN a.plus=0 THEN -1*a.`value` ELSE 0 END) as potkaryawan,
			sum(CASE WHEN a.plus=1 and ((f.bpjsjenisid=3 and (f.isjht=1 or f.id='".$jkkids."')) or f.bpjsjenisid=2)  THEN a.`value` ELSE 0 END) as potPerusahaan, sum(CASE WHEN a.plus=0 THEN -1*a.`value` ELSE 0 END)+ sum(CASE WHEN a.plus=1 and ((f.bpjsjenisid=3 and (f.isjht=1 or f.id='".$jkkids."')) or f.bpjsjenisid=2)  THEN a.`value` ELSE 0 END) as total, IFNULL(b.nojms2,' ') as nojms2, IFNULL(b.nojms3,' ') as nojms3,
			b.jms2start,b.jms3start, c.nik, c.namakaryawan as nama, d.namaorganisasi
			from ".$dbname.".sdm_ho_detailmonthly a
			left join ".$dbname.".sdm_ho_employee b on a.karyawanid = b.karyawanid
			left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
			left join ".$dbname.".organisasi d on c.lokasitugas=d.kodeorganisasi
			left join ".$dbname.".sdm_formula_bpjs e on a.component=e.tocomponentid
			left join ".$dbname.".sdm_ho_hr_jms_porsi f on e.jmsid=f.id
			where a.periode ='".$val."' and b.nojms3<>'' ".$loktug." and a.component in( 
			/*BPJS TK - Jaminan Pensiun*/
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=2)  and b.plus=1
			union
			select a.tocomponentid from sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=2)  and b.plus=0 and a.jmsid not in(select a.jmsid from sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=2)  and b.plus=1)
			union
			/*BPJS TK - Jaminan Hari Tua*/
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and isjht=1)  and b.plus=1
			union
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and isjht=1)  and b.plus=0 and a.jmsid not in(select a.jmsid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and isjht=1)  and b.plus=1)
			union
			/*BPJS TK - Jkk JKM*/
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and (isjk=1 or jmsid='".$jkkids."'))  and b.plus=1
			union
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and (isjk=1 or jmsid='".$jkkids."'))  and b.plus=0 and a.jmsid not in(select a.jmsid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=3 and (isjk=1 or jmsid='".$jkkids."'))  and b.plus=1))
			group by karyawanid";	

			
$qGapok=mysql_query($sGapok) or die(mysql_error($sGapok));
while($rGapok=mysql_fetch_assoc($qGapok))
{
	$data[$rGapok['karyawanid']]=$rGapok['karyawanid'];
    $dtGapok[$rGapok['karyawanid']]['nama']=$rGapok['nama'];
	$dtGapok[$rGapok['karyawanid']]['nik']=$rGapok['nik'];
    $dtGapok[$rGapok['karyawanid']]['tglmasuk']=$rGapok['startdate'];
    $dtGapok[$rGapok['karyawanid']]['jkkval']=$rGapok['jkkval'];
    $dtGapok[$rGapok['karyawanid']]['jkmval']=$rGapok['jkmval'];
    $dtGapok[$rGapok['karyawanid']]['jhtpt']=$rGapok['jhtpt'];
    $dtGapok[$rGapok['karyawanid']]['jhtkry']=$rGapok['jhtkry'];
    $dtGapok[$rGapok['karyawanid']]['jpkry']=$rGapok['jpkry'];
    $dtGapok[$rGapok['karyawanid']]['jppt']=$rGapok['jppt'];
    $dtGapok[$rGapok['karyawanid']]['potkaryawan']=$rGapok['potkaryawan'];
    $dtGapok[$rGapok['karyawanid']]['potPerusahaan']=$rGapok['potPerusahaan'];
    $dtGapok[$rGapok['karyawanid']]['total']=$rGapok['total'];
    $dtGapok[$rGapok['karyawanid']]['periode']=$rGapok['periode'];
    $dtGapok[$rGapok['karyawanid']]['nojms3']=$rGapok['nojms3'];
    $dtGapok[$rGapok['karyawanid']]['jms2start']=$rGapok['jms2start'];
    $dtGapok[$rGapok['karyawanid']]['jms3start']=$rGapok['jms3start'];
}	
	$stream='';	
    
			  
# Alamat & No Telp
	$query = selectQuery($dbname,'organisasi','alamat,telepon',
		"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
	$orgData = fetchData($query);
	$stream.="<table cellspacing='1' border='0'>";	
	$stream .= "<tr><td colspan=10 align=left>".strtoupper($_SESSION['org']['namaorganisasi'])."</td></tr>"; 
	$stream .= "<tr><td colspan=10 align=left>".$orgData[0]['alamat']."</td></tr>"; 
	$stream .= "<tr><td colspan=10 align=left>"."Tel: ".$orgData[0]['telepon']."</td></tr>"; 
	$stream .= "<tr><td colspan=10 align=center></td></tr>"; 
	$stream .= "<tr><td colspan=10 align=center><b>".strtoupper($_SESSION['lang']['bpjstk'])." ".strtoupper($_SESSION['lang']['dan'])." ".strtoupper($_SESSION['lang']['bpjstkjp'])." </b></td></tr>";  
	$stream .= "<tr><td colspan=10 align=center><b>".strtoupper($_SESSION['lang']['periode'])." :".substr($val,5,2)."-".substr($val,0,4)."</b></td></tr>"; 
	$stream .= "<tr><td colspan=10></td></tr>";    			  
	$stream.="</table>";		  
	$stream.="<table width=900px border=1>
		      <thead>
			  <tr bgcolor='#DFDFDF'>
			     <td align=center>No.</td>
				<td align=center>".$_SESSION['lang']['nik']."</td>
				<td align=center width=250>".$_SESSION['lang']['employeename']."</td>
				<td align=center>".$_SESSION['lang']['tgldaftar']."&nbsp;".$_SESSION['lang']['bpjstk']."</td>
				<td align=center>".$_SESSION['lang']['tgldaftar']."&nbsp;".$_SESSION['lang']['bpjstkjp']."</td>
				<td align=center width=110>No. ".$_SESSION['lang']['bpjstk']."</td>
				<td align=center>".$_SESSION['lang']['periodegaji']."</td>
				<td align=center>BPJS JHT Beban Karyawan<br>(Rp.)</td>
				<td align=center>BPJS JHT Beban Perusahaan<br>(Rp.)</td>
				<td align=center>BPJS JKK Beban Perusahaan<br>(Rp.)</td>
				<td align=center>BPJS JKM Beban Perusahaan<br>(Rp.)</td>
				<td align=center>BPJS JP Beban Karyawan<br>(Rp.)</td>
				<td align=center>BPJS JP Beban Perusahaan<br>(Rp.)</td>				
				<td align=center>BPJS Beban Karyawan<br>(Rp.)</td>
				<td align=center>BPJS Beban Perusahaan<br>(Rp.)</td>
				<td align=center>".$_SESSION['lang']['total']."<br>(Rp.)</td>
			  </tr> 
			  </thead>
			  <tbody id=tbody>";
		$no=0;
		$ttl=0;//grand total
		$tvp=0;//total perusahaan
		$tkar=0;//total karyawan
		$total=0;//total per karyawan
		foreach($data as $brsData)
        {
		   $no+=1;
		   $stream.="<tr class=rowcontent>
			     <td class=firsttd>".$no."</td>
				<td>'".$dtGapok[$brsData]['nik']."'</td>
				<td>".$dtGapok[$brsData]['nama']."</td>
				<td>".$dtGapok[$brsData]['jms2start']."</td>
				<td>".$dtGapok[$brsData]['jms3start']."</td>
				<td>".$dtGapok[$brsData]['nojms3']."</td>
				<td align=center>".$dtGapok[$brsData]['periode']."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jhtkry'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jhtpt'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jkkval'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jkmval'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jpkry'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['jppt'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['potkaryawan'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['potPerusahaan'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['total'],2,'.',',')."</td>
			 </tr>"; 
		  $tvp+=$dtGapok[$brsData]['potPerusahaan'];
		  $tkar+=$dtGapok[$brsData]['potkaryawan'];	  			
		}
		$stream.="</tbody>
			  <tfoot></tfoot>
				
			  </table>";  
			  
$stream.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			
$nop_="BPJS".$val1;
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
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
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
			  
?>
