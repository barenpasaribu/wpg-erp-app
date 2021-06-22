<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');
include_once('sdm_slaveSaveHOBSalary.php');

$userid=$_POST['userid'];
$component  =$_POST['component'];
$value=$_POST['value'];
$komp_pph21_plus=0;
$komp_pph21_minus=0;
$rkomp = "select * from ".$dbname.".sdm_formula_bpjs where componentid = tocomponentid";
$qkomp=mysql_query($rkomp) or die ("Error in query: $rkomp. ".mysql_error());
while($rkomp=mysql_fetch_assoc($qkomp))
{
	if ($rkomp['plus']==1){
		$komp_pph21_plus = $rkomp['tocomponentid'];
	}
	else if ($rkomp['plus']==0){
		$komp_pph21_minus = $rkomp['tocomponentid'];
	}
	
}
/*
$stdrop = "DROP TABLE IF EXISTS ".$dbname.".`setup_bpjs_vw`";
mysql_query($stdrop);
$stdrop = "DROP VIEW IF EXISTS ".$dbname.".`setup_bpjs_vw`";
mysql_query($stdrop);
*/
$stcrt = "IF NOT EXISTS create table ".$dbname.".setup_bpjs_vw(hasil DOUBLE, tocomponentid INT, karyawanid VARCHAR(50))";
mysql_query($stcrt);

$strbpjs = "select sum(case when `a`.`plus`=1 then /*untuk nilai bpjs penambah*/
		/*untuk bpjs kesehatan (jms)*/
		(case when ifnull(f.nojms,' ')=' ' AND ifnull(c.bpjsjenisid,0)=1 then
		0

		when ifnull(f.nojms,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=1 then
		`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
			end )
				
		 
		 
		 /*untuk bpjs tk - jp(jms2)*/
		when ifnull(g.nojms2,' ')=' ' AND ifnull(c.bpjsjenisid,0)=2 then
		0

		 
		when ifnull(g.nojms2,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=2 then
		`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
			end )
			

		 
		/*untuk bpjs tk (jms3)*/
		when ifnull(h.nojms3,' ')=' ' AND ifnull(c.bpjsjenisid,0)=3 then
		0

		 
		when ifnull(h.nojms3,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=3 AND ((`c`.isjkk=0) or (`c`.isjkk=1 and `c`.id = (select jmsid from ".$dbname.".sdm_jenis_bpjs_jkk where id = 
									(select bpjs_jkk_id from ".$dbname.".organisasi where kodeorganisasi = 
									(select lokasitugas from ".$dbname.".datakaryawan where karyawanid=`e`.`karyawanid`))))) then
		`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
			end )
		else 0 end)

		 
		else /*untuk nilai bpjs pengurang*/
		/*untuk bpjs kesehatan (jms)*/
		(case when ifnull(f.nojms,' ')=' ' AND ifnull(c.bpjsjenisid,0)=1 then
		0
		when ifnull(f.nojms,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=1 then
		-1*`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
		 end ) 
			


		/*untuk bpjs tk-jp (jms2)*/
		when ifnull(g.nojms2,' ')=' ' AND ifnull(c.bpjsjenisid,0)=2 then
		0
		when ifnull(g.nojms2,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=2 then
		-1*`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
		 end ) 
			
		 
		/*untuk bpjs tk (jms3)*/
		when ifnull(h.nojms3,' ')=' ' AND ifnull(c.bpjsjenisid,0)=3 then
		0
		when ifnull(h.nojms3,' ')<>' ' AND ifnull(c.bpjsjenisid,0)=3 AND ((`c`.isjkk=0) or (`c`.isjkk=1 and `c`.id = (select jmsid from ".$dbname.".sdm_jenis_bpjs_jkk where id = 
									(select bpjs_jkk_id from ".$dbname.".organisasi where kodeorganisasi = 
									(select lokasitugas from ".$dbname.".datakaryawan where karyawanid=`e`.`karyawanid`))))) then
		-1*`c`.`value`*(case when ifnull(`c`.`nominal`,0)=0 then ifnull(`e`.`value`,1) else 
			(case when `c`.`nominal`>=(select 
												sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
					then 
												(select sum(case when x.plus=1 then y.value else -1*y.value end) as nilai
												from ".$dbname.".sdm_ho_component  x 
												inner join ".$dbname.".sdm_ho_basicsalary y on x.isJP=1 and x.id = y.component
												WHERE y.karyawanid=e.karyawanid) 
			else `c`.`nominal` end) 
		 end ) 
			
		else 0 end)

		 
		 end)/100 as `hasil`,
		 `a`.`tocomponentid`,`e`.`karyawanid` from ".$dbname.".`sdm_formula_bpjs` `a`
		inner join ".$dbname.".`sdm_ho_component` `b` on `b`.`id` = `a`.`componentid`
		inner join ".$dbname.".`sdm_ho_hr_jms_porsi` `c` on `c`.`id` = `a`.`jmsid`
		inner join ".$dbname.".`sdm_ho_component` `d` on `d`.`id` =  `a`.`tocomponentid` 
		inner join ".$dbname.".`sdm_ho_basicsalary` `e` on `a`.`componentid` = `e`.`component`  and `e`.`karyawanid`=".$userid."
		left join ".$dbname.".`sdm_ho_employee` `f` ON (`e`.`karyawanid`=`f`.`karyawanid` AND ifnull(`f`.`nojms`,' ')<>'')
							
		left join ".$dbname.".`sdm_ho_employee` `g` ON (`e`.`karyawanid`=`g`.`karyawanid` AND ifnull(`g`.`nojms2`,' ')<>'')
							
		left join ".$dbname.".`sdm_ho_employee` `h` ON (`e`.`karyawanid`=`h`.`karyawanid` AND ifnull(`h`.`nojms3`,' ')<>'' )
							
		group by `a`.`tocomponentid`, `e`.`karyawanid`";
$resbpjs=mysql_query($strbpjs)or die(mysql_error());

while($rbpjs=mysql_fetch_object($resbpjs))
{
	$delbpjs = "DELETE FROM ".$dbname.".setup_bpjs_vw  where karyawanid =".$userid." and tocomponentid = ".$rbpjs->tocomponentid."";
	mysql_query($delbpjs);	
	$insbpjs = "INSERT INTO ".$dbname.".setup_bpjs_vw (hasil, tocomponentid, karyawanid) VALUES
	(".$rbpjs->hasil.", ".$rbpjs->tocomponentid.", '".$rbpjs->karyawanid."')";
	mysql_query($insbpjs);

}

$hapus="DELETE FROM ".$dbname.".sdm_ho_basicsalary 
				WHERE karyawanid='".$userid."' 
				AND component in (select distinct x.tocomponentid from ".$dbname.".setup_bpjs_vw x)";
mysql_query($hapus);





//echo "Warning:".$hapus;
$rbps = "select * from ".$dbname.".setup_bpjs_vw WHERE karyawanid='".$userid."'";

$qKbn=mysql_query($rbps) or die ("Error in query: $rbps. ".mysql_error());
		if(mysql_query($rbps))
		{
			$resbp=mysql_query($rbps)or die(mysql_error());				
			while($rbp=mysql_fetch_assoc($resbp))
			{
				//echo "warning:aaa";exit();
				
				$strd="delete from ".$dbname.".sdm_ho_basicsalary where karyawanid = ".$userid." and componentid = ".$rbp['tocomponentid']."";
				mysql_query($strd);
				
				$stri="insert into ".$dbname.".sdm_ho_basicsalary (karyawanid,component,value,updateby)
				values(".$userid.",".$rbp['tocomponentid'].",".$rbp['hasil'].",'".$_SESSION['standard']['username']."')";	
				mysql_query($stri);
				
				//pph21 penambah
				$strd="delete from ".$dbname.".sdm_ho_basicsalary where karyawanid = ".$userid." and componentid = ".$komp_pph21_plus."";
				mysql_query($strd);
				$stri="insert into ".$dbname.".sdm_ho_basicsalary (karyawanid,component,value,updateby)
				values(".$userid.",".$komp_pph21_plus.",0,'".$_SESSION['standard']['username']."')";	
				mysql_query($stri);
				
				//pph21 pengurang
				$strd="delete from ".$dbname.".sdm_ho_basicsalary where karyawanid = ".$userid." and componentid = ".$komp_pph21_minus."";
				mysql_query($strd);
				$stri="insert into ".$dbname.".sdm_ho_basicsalary (karyawanid,component,value,updateby)
				values(".$userid.",".$komp_pph21_minus.",0,'".$_SESSION['standard']['username']."')";	
				mysql_query($stri);
					
			}
		}
?>
