<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$val1=trim($_GET['periode']);	
$val=substr($val1,3,4)."-".substr($val1,0,2);

$sGapok="select a.karyawanid, a.periode,sum(CASE WHEN a.plus=0 THEN -1*a.`value` ELSE 0 END) as potkaryawan,
			sum(CASE WHEN a.plus=1 THEN a.`value` ELSE 0 END) as potPerusahaan, sum(CASE WHEN a.plus=0 THEN -1*a.`value` ELSE 0 END)+ sum(CASE WHEN a.plus=1 THEN a.`value` ELSE 0 END) as total, IFNULL(b.nojms,' ') as nojms,
			b.jmsstart, b.kelasbpjskes, c.nik, c.namakaryawan as nama, d.namaorganisasi
			from ".$dbname.".sdm_ho_detailmonthly a
			left join ".$dbname.".sdm_ho_employee b on a.karyawanid = b.karyawanid
			left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
			left join ".$dbname.".organisasi d on c.lokasitugas=d.kodeorganisasi
			where a.periode ='".$val."' and b.nojms<>'' and a.component in( 
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=1)  and b.plus=1
			union
			select a.tocomponentid from ".$dbname.".sdm_formula_bpjs a left join ".$dbname.".sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=1)  and b.plus=0 and a.jmsid not in(select a.jmsid from ".$dbname.".sdm_formula_bpjs a left join sdm_ho_component b on a.tocomponentid = b.id  where a.jmsid in 
			(select id from ".$dbname.".sdm_ho_hr_jms_porsi where bpjsjenisid=1)  and b.plus=1))
			group by karyawanid";	

$qGapok=mysql_query($sGapok) or die(mysql_error($sGapok));
while($rGapok=mysql_fetch_assoc($qGapok))
{
	$data[$rGapok['karyawanid']]=$rGapok['karyawanid'];
    $dtGapok[$rGapok['karyawanid']]['nama']=$rGapok['nama'];
	$dtGapok[$rGapok['karyawanid']]['nik']=$rGapok['nik'];
    $dtGapok[$rGapok['karyawanid']]['tglmasuk']=$rGapok['startdate'];
    $dtGapok[$rGapok['karyawanid']]['potkaryawan']=$rGapok['potkaryawan'];
    $dtGapok[$rGapok['karyawanid']]['potPerusahaan']=$rGapok['potPerusahaan'];
    $dtGapok[$rGapok['karyawanid']]['periode']=$rGapok['periode'];
    $dtGapok[$rGapok['karyawanid']]['nojms']=$rGapok['nojms'];
    $dtGapok[$rGapok['karyawanid']]['kelasbpjskes']=$rGapok['kelasbpjskes'];
    $dtGapok[$rGapok['karyawanid']]['jmsstart']=$rGapok['jmsstart'];  
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
	$stream .= "<tr><td colspan=10 align=center><b>".strtoupper($_SESSION['lang']['bpjskes'])."</b></td></tr>";  
	$stream .= "<tr><td colspan=10 align=center><b>".strtoupper($_SESSION['lang']['periode'])." :".substr($val,5,2)."-".substr($val,0,4)."</b></td></tr>"; 
	$stream .= "<tr><td colspan=10></td></tr>";    			  
	$stream.="</table>";		  
	$stream.="<table width=900px border=1>
		      <thead>
			  <tr bgcolor='#DFDFDF'>
			    <td align=center>No.</td>
				<td align=center>".$_SESSION['lang']['nik']."</td>
				<td align=center width=250>".$_SESSION['lang']['employeename']."</td>
				<td align=center>".$_SESSION['lang']['tgldaftar']."&nbsp;".$_SESSION['lang']['bpjskes']."</td>
				<td align=center>".$_SESSION['lang']['kelasbpjskes']."</td>
				<td align=center width=110>No. ".$_SESSION['lang']['bpjskes']."</td>
				<td align=center>".$_SESSION['lang']['periodegaji']."</td>
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
				<td>".$dtGapok[$brsData]['nik']."</td>
				<td>".$dtGapok[$brsData]['nama']."</td>
				<td>".$dtGapok[$brsData]['jmsstart']."</td>
				<td>".$dtGapok[$brsData]['kelasbpjskes']."</td>
				<td>".$dtGapok[$brsData]['nojms']."</td>
				<td align=center>".$dtGapok[$brsData]['periode']."</td>
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
