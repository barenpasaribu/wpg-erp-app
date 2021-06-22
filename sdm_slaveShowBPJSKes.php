<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
//+++++++++++++++++++++++++++++++++++++++++++++
require_once('config/connection.php');
$val=trim($_POST['val']);

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
//echo "warning: ".$sGapok;			
$qGapok=mysql_query($sGapok) or die(mysql_error($sGapok));
while($rGapok=mysql_fetch_assoc($qGapok))
{
	$data[$rGapok['karyawanid']]=$rGapok['karyawanid'];
    $dtGapok[$rGapok['karyawanid']]['nama']=$rGapok['nama'];
	$dtGapok[$rGapok['karyawanid']]['nik']=$rGapok['nik'];
    $dtGapok[$rGapok['karyawanid']]['tglmasuk']=$rGapok['startdate'];
    $dtGapok[$rGapok['karyawanid']]['potkaryawan']=$rGapok['potkaryawan'];
    $dtGapok[$rGapok['karyawanid']]['potPerusahaan']=$rGapok['potPerusahaan'];
    $dtGapok[$rGapok['karyawanid']]['total']=$rGapok['total'];
    $dtGapok[$rGapok['karyawanid']]['periode']=$rGapok['periode'];
    $dtGapok[$rGapok['karyawanid']]['nojms']=$rGapok['nojms'];
    $dtGapok[$rGapok['karyawanid']]['kelasbpjskes']=$rGapok['kelasbpjskes'];
    $dtGapok[$rGapok['karyawanid']]['jmsstart']=$rGapok['jmsstart'];
    
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
				<td>".$dtGapok[$brsData]['jmsstart']."</td>
				<td>".$dtGapok[$brsData]['kelasbpjskes']."</td>
				<td>".$dtGapok[$brsData]['nojms']."</td>
				<td align=center>".$dtGapok[$brsData]['periode']."</td>
				<td align=right>".number_format($dtGapok[$brsData]['potkaryawan'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['potPerusahaan'],2,'.',',')."</td>
				<td align=right>".number_format($dtGapok[$brsData]['total'],2,'.',',')."</td>
			 </tr>"; 			
		  $ttl+=$total;	  			
		}
?>
