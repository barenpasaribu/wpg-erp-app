<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
//+++++++++++++++++++++++++++++++++++++++++++++
require_once('config/connection.php');
require_once('lib/eksilib.php');
$val=trim($_POST['val']);
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

if($_SESSION['org']['kodepusat']==$_SESSION['empl']['lokasitugas']){
	$loktug="";
}
else {
	$loktug="and c.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}

//rubah jadi perlokasi tugas ==Jo 17-03-2017==
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
//echo "warning: ".$sGapok;			
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
/*		$str="select e.name,e.startdate,e.nojms,d.value,d.karyawanid,d.periode 
		      from ".$dbname.".sdm_ho_employee e, ".$dbname.".sdm_ho_detailmonthly d
		      where e.karyawanid=d.karyawanid and e.operator='".$_SESSION['standard']['username']."'
			  and d.periode='".$val."' and d.component=3 
		      order by name";
		$res=mysql_query($str,$conn);		
*/
		$no=0;
		$ttl=0;//grand total
		$tvp=0;//total perusahaan
		$tkar=0;//total karyawan
		$total=0;//total per karyawan
		foreach($data as $brsData)
        {
			
			
		   $no+=1;
		   echo"<tr class=rowcontent>
			    <td class=firsttd>".$no."</td>
				<td>".$dtGapok[$brsData]['nik']."</td>
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
		  $ttl+=$total;	  			
		}
?>
