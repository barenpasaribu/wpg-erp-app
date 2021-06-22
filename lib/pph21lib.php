<?php
require_once('eksilib.php');
class pph21calc{
	//rubah  jadi ambil lokasitugas periode gaji dari lokasi tugas yang login saja ==Jo 16-03-2017==
	function kueriPPh21($periode, $userid){
		$strpph="select b.karyawanid, 
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
		and (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)  = 12)
		then
		12 /*tambah kondisi untuk resign tengah bulan tapi bukan tahun itu ==JO 15-03-2017==*/
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
		
		case when ((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%Y-%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)=date_format(STR_TO_DATE(ifnull(h.lastpayment, '0000-00'),'%Y-%m'),'%Y-%m')
		or (select date_format(STR_TO_DATE(t.tanggalsampai,'%Y-%m'),'%m') FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1)=12) then
		1 else 0 end as akhirperiode,
		
		(CASE WHEN ((IFNULL(`d`.`isPPHGrossUp`,0) = 0) AND (IFNULL(`g`.`isPPHGrossUp`,0) = 0)) THEN 0 WHEN ((IFNULL(`d`.`isPPHGrossUp`,0) = 1) AND (IFNULL(`g`.`isPPHGrossUp`,0) = 0)) THEN 1 WHEN ((IFNULL(`d`.`isPPHGrossUp`,0) = 0) AND (IFNULL(`g`.`isPPHGrossUp`,0) = 1)) THEN 0 WHEN ((IFNULL(`d`.`isPPHGrossUp`,0) = 1) AND (IFNULL(`g`.`isPPHGrossUp`,0) = 1)) THEN 1 END) AS `FlagPPHGrossUP`,
		h.lastpayment as lastpaydate,
		d.statuspajak, e.value as ptkp, d.npwp, j.persen as persennpwp, `d`.`lokasitugas`, ifnull(h.firstpayment,0) as firstpayment, ifnull(h.firstvol,0) as firstvol, ifnull(h.lastpayment,0) as lastpayment, ifnull(h.lastvol,0) as lastvol,
		
		(select sum(ifnull(k.gajibrutosetahun/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
		FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
		and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment) 
		as brutonm,

		(select sum(ifnull((k.gajibrutosetahundanbonus )/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
			FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
			and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
			FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 
		+
		(select sum(ifnull((k.gajibrutosetahundanbonus - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')) )/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 


		as brutodanbonusnm,

		/*Rubah kueri agar yang ada thr tidak langsung dibagi pengali ==13-07-2017==*/ 
		(select sum(ifnull(k.gajibrutosetahundanthr/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 
		+
		(select sum(ifnull((k.gajibrutosetahundanthr - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')))/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 

		as brutodanthrnm,

		(select sum(ifnull(k.gajibrutosetahundanbonusthr/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q
					left join sdm_ho_component p on q.component=p.id where (p.isBNS=1 or p.isTHR=1) and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 

		+

		(select sum(ifnull((k.gajibrutosetahundanbonus - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')) )/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 
		+
		(select sum(ifnull((k.gajibrutosetahundanthr - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')))/(select distinct pengali from setup_pkp_non_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_non_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 


		as brutodanbtnm,

		/*Rubah kueri agar yang ada bonus tidak langsung dibagi pengali ==13-07-2017==*/ 
		(select sum(ifnull(k.gajibrutosetahun/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment) as brutogu,

		(select sum(ifnull((k.gajibrutosetahundanbonus )/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 
		+
		(select sum(ifnull((k.gajibrutosetahundanbonus - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')) )/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 

					
		as brutodanbonusgu,

		/*Rubah kueri agar yang ada thr tidak langsung dibagi pengali ==13-07-2017==*/ 
		(select sum(ifnull(k.gajibrutosetahundanthr/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 
		+
		(select sum(ifnull((k.gajibrutosetahundanthr - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')))/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 
						
		as brutodanthrgu,

		(select sum(ifnull(k.gajibrutosetahundanbonusthr/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode not in (select q.periode from sdm_ho_detailmonthly q
					left join sdm_ho_component p on q.component=p.id where (p.isBNS=1 or p.isTHR=1) and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 

		+

		(select sum(ifnull((k.gajibrutosetahundanbonus - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')) )/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isBNS=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))) 
		+
		(select sum(ifnull((k.gajibrutosetahundanthr - (select sum(q.value) from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')))/(select distinct pengali from setup_pkp_grossup where karyawanid=d.karyawanid and periode like concat(substr('".$periode."',1,4),'%') LIMIT 1),0)) from setup_pkp_grossup k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
					and k.karyawanid=d.karyawanid and substr(k.periode,6,7) <> '12' and k.periode <> h.lastpayment and k.periode in (select q.periode from sdm_ho_detailmonthly q left join sdm_ho_component p on q.component=p.id where p.isTHR=1 and q.karyawanid=d.karyawanid and q.value>0 and q.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
					FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg='".$_SESSION['empl']['lokasitugas']."' AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%'))	) 
					
		as brutodanbtgu
		
		from sdm_ho_component a 
		inner join sdm_ho_basicsalary b on a.id = b.component 
		join sdm_ho_pph21jabatan c
		left join datakaryawan d on b.karyawanid = d.karyawanid
		left join sdm_ho_pph21_ptkp e on  d.statuspajak = e.kode
		left join organisasi g on d.kodeorganisasi=g.kodeorganisasi
		left join sdm_ho_employee h on d.karyawanid=h.karyawanid 
		join sdm_master_tarif_pph21 j on j.isNPWP=0 and j.aktif=1
		where a.pph21 = 1 and b.karyawanid='".$userid."'";
		//echo "warning: ".$strpph;
		
		return $strpph;
	}
	
	/*function getPPh21plus($periode,$userid,$component){
		
	}*/
	
	
	
	//variabel bantu perhitungan pph21 normal
	
	//function hitung pph21
	function hitungpph($kueripph21,$pph21nm,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,$resslc,
					$strup,$strins,$periode,$userid,$eksi){
		$pph21nm=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			//hitung bruto
			if ($bargu['akhirperiode']==1){
				/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi']);
				$brutosetahun = $bruto + $bargu['brutonm']+ $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'];*/
				
				//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
				if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*$bargu['firstvol']/100) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'];
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] ;
				}
				
				
			}
			else {
				$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
				$brutosetahun = ($bruto * $bargu['pengali']);	
			}
							
			//hitung biaya jabatan
			if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
				$byjbt = ($bargu['maks']*$bargu['pengali']);
			}
			else {
				$byjbt = $brutosetahun*($bargu['persen']/100);
			}
			
			//hitung netto
			$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
			
			//netto disetahunkan
			//$nettosetahun = $nettos * 12;
			
			//hitung penghasilan kena pajak
			
			$pkp = $nettos   - $bargu['ptkp'];
			$pkp = (floor($pkp/1000))*1000;
			
			//hitung pph bulanan
			$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
			from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
			)+
			ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
			
			$respgu=$eksi->sSQL($calpph21);
			foreach($respgu as $barpgu){
				//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
				if($pkp>0){
					$pph21nm = $barpgu['pph21'];
				}
				else {
					$pph21nm = 0;
				}
				
					//$pph21guval = $pph21gu *12;
					if (!empty($bargu['npwp'])){
						$pph21nmval = $pph21nm *$bargu['pengali'];
					}
					else {
						$pph21nmval = $pph21nm * ($bargu['persennpwp']/100) * $bargu['pengali'];
					}
			}
			
			$strslc = "select karyawanid from setup_pkp_non_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->exc($strslc);

			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_non_grossup set pengali=".$bargu['pengali'].", gajibrutosetahun=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahun=".$byjbt.", gajinettosetahun=".$nettos.", pkpsetahun=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				
				$eksi->exc($strup);
			}
			else {
				$strins = "insert into setup_pkp_non_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahun,iuranpensiunsetahun,biayajabatansetahun,
				gajinettosetahun,
				statuspajak,ptkp,
				pkpsetahun,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->exc($strins);
				
			}
			
		}
		
		return array($nettos, $pph21nmval);
		
	}
	
	//variabel bantu perhitungan pph21 normal dengan bonus
	
	//function hitung pph21
	function hitungpphbns($kueripph21,$pph21nms,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,$resslc,
					$strup,$strins,$periode,$userid,$eksi){
		$pph21nms=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			//hitung bruto
			if ($bargu['akhirperiode']==1){
				/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi']);
				
				$brutosetahun = $bruto + $bargu['brutodanbonusnm'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['bonus'];	*/
				
				//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
				if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*$bargu['firstvol']/100) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] +$bargu['bonustahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['bonustahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] +$bargu['bonustahunini'];
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] +$bargu['bonustahunini'];
				}
				
			}
			else {
				$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
				$brutosetahun = ($bruto * $bargu['pengali'])+ $bargu['bonus'];
			}
								
			//hitung biaya jabatan
			if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
				$byjbt = ($bargu['maks']*$bargu['pengali']);
			}
			else {
				$byjbt = $brutosetahun*($bargu['persen']/100);
			}
			
			//hitung netto
			if ($bargu['akhirperiode']==1){
				$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
			}
			else {
				$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
			}
			
			//netto disetahunkan
			//$nettosetahun = $nettos * 12;
			
			//hitung penghasilan kena pajak
			
			$pkp = $nettos   - $bargu['ptkp'];
			$pkp = (floor($pkp/1000))*1000;
			
			//hitung pph bulanan
			$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
			from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
			)+
			ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
			
			$respgu=$eksi->sSQL($calpph21);
			foreach($respgu as $barpgu){
				//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
				if($pkp>0){
					$pph21nms = $barpgu['pph21'];
				}
				else {
					$pph21nms = 0;
				}
				
				//$pph21guval = $pph21gu *12;
				if (!empty($bargu['npwp'])){
					$pph21nmvals = $pph21nms *$bargu['pengali'];
				}
				else {
					$pph21nmvals = $pph21nms * ($bargu['persennpwp']/100) * $bargu['pengali'];
				}
			}
			
			
			$strslc = "select karyawanid from setup_pkp_non_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->exc($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_non_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanbonus=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanbonus=".$byjbt.", gajinettosetahundanbonus=".$nettos.", pkpsetahundanbonus=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->exc($strup);
			}
			else {
				$strins = "insert into setup_pkp_non_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanbonus,iuranpensiunsetahun,biayajabatansetahundanbonus,
				gajinettosetahundanbonus,
				statuspajak,ptkp,
				pkpsetahundanbonus,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->exc($strins);
			}
		}
		
		return array($nettos, $pph21nmvals);
		
	}
	
	//variabel bantu perhitungan pph21 normal dengan thr
	
	//function hitung pph21
	function hitungpphthr($kueripph21,$pph21nmt,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,$resslc,
					$strup,$strins,$periode,$userid,$eksi){
		$pph21nmt=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			
			//hitung bruto
			if ($bargu['akhirperiode']==1){
				/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi']);
					
				$brutosetahun = $bruto + $bargu['brutodanthrnm'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['thr'];	*/
				
				//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
				if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*$bargu['firstvol']/100) +  ($bargu['tunjangan']*($bargu['firstvol'])/100)  + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] +  $bargu['thr']+$bargu['thrtahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] +  $bargu['thr']+$bargu['thrtahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['thr'] +$bargu['thrtahunini'];
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['thr']+$bargu['thrtahunini'];
				}
				
			}
			else {
				$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
				$brutosetahun = ($bruto * $bargu['pengali'])+ $bargu['thr'];	
			}
							
			//hitung biaya jabatan
			if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
				$byjbt = ($bargu['maks']*$bargu['pengali']);
			}
			else {
				$byjbt = $brutosetahun*($bargu['persen']/100);
			}
			
			//hitung netto
			if ($bargu['akhirperiode']==1){
				$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
			}
			else {
				$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
			}
			
			//netto disetahunkan
			//$nettosetahun = $nettos * 12;
			
			//hitung penghasilan kena pajak
			
			$pkp = $nettos   - $bargu['ptkp'];
			$pkp = (floor($pkp/1000))*1000;
			
			//hitung pph bulanan
			$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
			from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
			)+
			ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
			
			$respgu=$eksi->sSQL($calpph21);
			foreach($respgu as $barpgu){
				//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
				if($pkp>0){
					$pph21nmt = $barpgu['pph21'];
				}
				else {
					$pph21nmt = 0;
				}
				
				//$pph21guval = $pph21gu *12;
				if (!empty($bargu['npwp'])){
					$pph21nmvalt = $pph21nmt *$bargu['pengali'];
					
				}
				else {
					$pph21nmvalt = $pph21nmt * ($bargu['persennpwp']/100) * $bargu['pengali'];
				}
			}
			
			$strslc = "select karyawanid from setup_pkp_non_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->exc($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_non_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanthr=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanthr=".$byjbt.", gajinettosetahundanthr=".$nettos.", pkpsetahundanthr=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->exc($strup);
			}
			else {
				$strins = "insert into setup_pkp_non_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanthr,iuranpensiunsetahun,biayajabatansetahundanthr,
				gajinettosetahundanthr,
				statuspajak,ptkp,
				pkpsetahundanthr,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->exc($strins);
			}
		}
		
		return array($nettos, $pph21nmvalt);
		
	}
	
	//variabel bantu perhitungan pph21 normal dengan bonus thr
	
	//function hitung pph21
	function hitungpphbt($kueripph21,$pph21nmst,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,$resslc,
					$strup,$strins,$periode,$userid,$eksi){
		$pph21nmst=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			
			//hitung bruto
			if ($bargu['akhirperiode']==1){
				/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi']);
				
				$brutosetahun = $bruto + $bargu['brutodanbtnm'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['bonus'] + $bargu['thr'];*/
				
				//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
				if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*$bargu['firstvol']/100) +  ($bargu['tunjangan']*($bargu['firstvol'])/100)  + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr']+$bargu['bonustahunini']+$bargu['thrtahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr']+$bargu['bonustahunini']+$bargu['thrtahunini'];
				}
				else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
					$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr']+$bargu['bonustahunini']+$bargu['thrtahunini'];
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
					$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini']+ $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr']+$bargu['bonustahunini']+$bargu['thrtahunini'];
				}
							
			}
			else {
				$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']);
				$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['bonus'] + $bargu['thr'];	
			}					
			//hitung biaya jabatan
			if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
				$byjbt = ($bargu['maks']*$bargu['pengali']);
			}
			else {
				$byjbt = $brutosetahun*($bargu['persen']/100);
			}
			
			//hitung netto
			if ($bargu['akhirperiode']==1){
				$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
			}
			else {
				$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
			}
			
			//netto disetahunkan
			//$nettosetahun = $nettos * 12;
			
			//hitung penghasilan kena pajak
			
			$pkp = $nettos   - $bargu['ptkp'];
			$pkp = (floor($pkp/1000))*1000;
			
			//hitung pph bulanan
			$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
			from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
			)+
			ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
			
			$respgu=$eksi->sSQL($calpph21);
			foreach($respgu as $barpgu){
				//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
				if($pkp>0){
					$pph21nmst = $barpgu['pph21'];
				}
				else {
					$pph21nmst = 0;
				}
				
				//$pph21guval = $pph21gu *12;
				if (!empty($bargu['npwp'])){
					$pph21nmvalst = $pph21nmst *$bargu['pengali'];
				}
				else {
					$pph21nmvalst = $pph21nmst * ($bargu['persennpwp']/100) * $bargu['pengali'];
				}
			}
			
			$strslc = "select karyawanid from setup_pkp_non_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->exc($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_non_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanbonusthr=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanbonusthr=".$byjbt.", gajinettosetahundanbonusthr=".$nettos.", pkpsetahundanbonusthr=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->exc($strup);
			}
			else {
				$strins = "insert into setup_pkp_non_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanbonusthr,iuranpensiunsetahun,biayajabatansetahundanbonusthr,
				gajinettosetahundanbonusthr,
				statuspajak,ptkp,
				pkpsetahundanbonusthr,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->exc($strins);
			}
		}
		
		return array($nettos, $pph21nmvalst);
		
	}
	
	//variabel bantu perhitungan gross up pph21 
			
	//function hitung gross up
	function hitungGU($kueripph21,$tjpph21,$pph21gu,$sf,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,
					$strup,$strins,$periode,$userid,$eksi){
		$tjpph21=0;
		$pph21gu=1;
		$sf=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			while(floor($tjpph21)!=floor($pph21gu)){//iterasi hitung gross up
				if ($sf>0){
					$tjpph21=$pph21gu;
				}
				//hitung bruto
				if ($bargu['akhirperiode']==1){
					/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + floor($tjpph21));
						
					$brutosetahun = $bruto + $bargu['brutogu'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'];*/	
					
					//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
					if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21)) +  +$bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*(100/$bargu['lastvol'])) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21)) +  +$bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21);
						$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21)) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21)) +  +$bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'];
					}
					else {
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21);
						$brutosetahun = ($bruto * $bargu['pengali']) +  +$bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'];
					}

					
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21);
					$brutosetahun = $bruto * $bargu['pengali'];	
				}					
				//hitung biaya jabatan
				if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
					$byjbt = ($bargu['maks']*$bargu['pengali']);
				}
				else {
					$byjbt = $brutosetahun*($bargu['persen']/100);
				}
				
				
				$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
				
				//netto disetahunkan
				//$nettosetahun = $nettos * 12;
				
				//hitung penghasilan kena pajak
				
				$pkp = $nettos   - $bargu['ptkp'];
				$pkp = (floor($pkp/1000))*1000;
				
				//hitung pph bulanan
				$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
				from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
				)+
				ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
				
				$respgu=$eksi->sSQL($calpph21);
				foreach($respgu as $barpgu){
					//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
					if($pkp>0){
						$pph21gu = $barpgu['pph21'];
					}
					else {
						$pph21gu = 0;
					}
					
					//$pph21guval = $pph21gu *12;
					if (!empty($bargu['npwp'])){
						$pph21guval = $pph21gu *$bargu['pengali'];
					}
					else {
						$pph21guval = $pph21gu * ($bargu['persennpwp']/100) * $bargu['pengali'];
					}
				}
				$sf++;
			}
			$strslc = "select karyawanid from setup_pkp_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->exc($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_grossup set pengali=".$bargu['pengali'].", gajibrutosetahun=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahun=".$byjbt.", gajinettosetahun=".$nettos.", pkpsetahun=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->exc($strup);
			}
			else {
				$strins = "insert into setup_pkp_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahun,iuranpensiunsetahun,biayajabatansetahun,
				gajinettosetahun,
				statuspajak,ptkp,
				pkpsetahun,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->exc($strins);
			}
			
		}
		
		
		return array($nettos, $pph21guval);
		
	}
	
	//variabel bantu perhitungan gross up pph21 dengan bonus
	
	//function hitung gross up dengan bonus
	function hitungGUBns($kueripph21,$tjpph21s,$pph21gus,$sfs,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,
					$strup,$strins,$periode,$userid,$eksi){
		$tjpph21s=0;
		$pph21gus=1;
		$sfs=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			while(floor($tjpph21s)!=floor($pph21gus)){//iterasi hitung gross up
				if ($sfs>0){
					$tjpph21s=$pph21gus;
				}
				//hitung bruto
				if ($bargu['akhirperiode']==1){
					/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + floor($tjpph21s));
						
					$brutosetahun = $bruto + $bargu['brutodanbonusgu'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'];	*/
					
					//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
					if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21s);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']+ floor($tjpph21s)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus']+$bargu['bonustahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*(100/$bargu['lastvol'])) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21s);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']+ floor($tjpph21s)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus']+$bargu['bonustahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*(100/$bargu['lastvol'])) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21s);
						$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']+ floor($tjpph21s)) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']+ floor($tjpph21s)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['bonus']+$bargu['bonustahunini'];
					}
					else {
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21s);
						$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['bonus']+$bargu['bonustahunini'];
					}
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21s);
					$brutosetahun = ($bruto *$bargu['pengali'])+$bargu['bonus'];	
				}
									
				//hitung biaya jabatan
				if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
					$byjbt = ($bargu['maks']*$bargu['pengali']);
				}
				else {
					$byjbt = $brutosetahun*($bargu['persen']/100);
				}
				
				
				//hitung netto
				if ($bargu['akhirperiode']==1){
					$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
				}
				else {
					$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
				}
				
				//netto disetahunkan
				//$nettosetahun = $nettos * 12;
				
				//hitung penghasilan kena pajak
				$pkp = $nettos   - $bargu['ptkp'];
				
				$pkp = (floor($pkp/1000))*1000;
				
				//hitung pph bulanan
				$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
				from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
				)+
				ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp."   and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
				
				$respgu=$eksi->sSQL($calpph21);
				foreach($respgu as $barpgu){
					//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
					if($pkp>0){
						$pph21gus = $barpgu['pph21'];
					}
					else {
						$pph21gus = 0;
					}
					
					//$pph21guvals = $pph21gus *12;
					if (!empty($bargu['npwp'])){
						$pph21guvals = $pph21gus *$bargu['pengali'];
					}
					else {
						$pph21guvals = $pph21gus * ($bargu['persennpwp']/100) * $bargu['pengali'];
					}
				}
				
				$sfs++;
			}
			$strslc = "select karyawanid from setup_pkp_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->sSQL($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanbonus=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanbonus=".$byjbt.", gajinettosetahundanbonus=".$nettos.", pkpsetahundanbonus=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->sSQL($strup);
			}
			else {
				$strins = "insert into setup_pkp_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanbonus,iuranpensiunsetahun,biayajabatansetahundanbonus,
				gajinettosetahundanbonus,
				statuspajak,ptkp,
				pkpsetahundanbonus,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->sSQL($strins);
			}
			
		}
		
		return array($nettos, $pph21guvals);
		
	}
	
	//variabel bantu perhitungan gross up pph21 dengan thr
	
	//function hitung gross up dengan thr
	function hitungGUThr($kueripph21,$tjpph21t,$pph21gut,$sft,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,
					$strup,$strins,$periode,$userid,$eksi){
		$tjpph21t=0;
		$pph21gut=1;
		$sft=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			while(floor($tjpph21t)!=floor($pph21gut)){//iterasi hitung gross up
				if ($sft>0){
					$tjpph21t=$pph21gut;
				}
				//hitung bruto
				if ($bargu['akhirperiode']==1){
					/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + floor($tjpph21t));
						
					$brutosetahun = $bruto + $bargu['brutodanthrgu'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['thr'];*/
					
					//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
					if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21t);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21t)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['thr']+$bargu['thrtahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*(100/$bargu['lastvol'])) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21t);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21t)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['thr']+$bargu['thrtahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*(100/$bargu['lastvol'])) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21t);
						$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21t)) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21t)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['thr']+$bargu['thrtahunini'];
					}
					else {
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21t);
						$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini']+ $bargu['thr']+$bargu['thrtahunini'];
					}
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21t);
					$brutosetahun = ($bruto *$bargu['pengali']) + $bargu['thr'];	
				}
									
				//hitung biaya jabatan
				if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
					$byjbt = ($bargu['maks']*$bargu['pengali']);
				}
				else {
					$byjbt = $brutosetahun*($bargu['persen']/100);
				}
				
				//hitung netto
				if ($bargu['akhirperiode']==1){
					$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
				}
				else {
					$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
				}
				
				//netto disetahunkan
				//$nettosetahun = $nettos * 12;
				
				//hitung penghasilan kena pajak
				$pkp = $nettos - $bargu['ptkp'];
				$pkp = (floor($pkp/1000))*1000;
				
				//hitung pph bulanan
				$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
				from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
				)+
				ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
				
				$respgu=$eksi->sSQL($calpph21);
				foreach($respgu as $barpgu){
					//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
					if($pkp>0){
						$pph21gut = $barpgu['pph21'];
					}
					else {
						$pph21gut = 0;
					}
					
					//$pph21guvalt = $pph21gut *12;
					if (!empty($bargu['npwp'])){
						$pph21guvalt = $pph21gut *$bargu['pengali'];
					}
					else {
						$pph21guvalt = $pph21gut * ($bargu['persennpwp']/100) * $bargu['pengali'];
					}
				}
				
				$sft++;
			}
			$strslc = "select karyawanid from setup_pkp_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->sSQL($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanthr=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanthr=".$byjbt.", gajinettosetahundanthr=".$nettos.", pkpsetahundanthr=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->sSQL($strup);
			}
			else {
				$strins = "insert into setup_pkp_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanthr,iuranpensiunsetahun,biayajabatansetahundanthr,
				gajinettosetahundanthr,
				statuspajak,ptkp,
				pkpsetahundanthr,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->sSQL($strins);
			}
		}
		
		return array($nettos, $pph21guvalt);
		
	}
	
	//variabel bantu perhitungan gross up pph21 dengan bonus dan thr
	
	//function hitung gross up dengan bonus dan thr
	function hitungGUBT($kueripph21,$tjpph21st,$pph21gust,$sfst,$resgu,$bargu,
					$bruto,$byjbt,$nettos,$nettosetahun,$calpph21,$respgu,$barpgu,$strslc,
					$strup,$strins,$periode,$userid,$eksi){
		$tjpph21st=0;
		$pph21gust=1;
		$sfst=0;
		$resgu=$eksi->sSQL($kueripph21);
		foreach($resgu as $bargu){
			
			while(floor($tjpph21st)!=floor($pph21gust)){//iterasi hitung gross up
				if ($sfst>0){
					$tjpph21st=$pph21gust;
				}
				//hitung bruto
				if ($bargu['akhirperiode']==1){
					/*$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + floor($tjpph21st));
					
					$brutosetahun = $bruto + $bargu['brutodanbtgu'] + $bargu['lembur'] + $bargu['lemburtahunini'] - $bargu['potpen'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'];*/
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21st);
					$brutosetahun = ($bruto *$bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'];
					
					//rubah untuk yang ada firstvol dan lastvol ==Jo 17-07-2017==
					if($bargu['lastpayment']!=$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21st);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21st)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'] + $bargu['bonustahunini'] + $bargu['thrtahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))!=date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21st);
						$brutosetahun = ($bruto * ($bargu['pengali']-1))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21st)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'] + $bargu['bonustahunini'] + $bargu['thrtahunini'];
					}
					else if($bargu['lastpayment']==$periode && date('Y',strtotime($periode))==date('Y',strtotime($bargu['firstpayment']))){
						$bruto = (($bargu['gajipokok']*100/$bargu['lastvol']) +  ($bargu['tunjangan']*100/$bargu['lastvol']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21st);
						$brutosetahun = ($bruto * ($bargu['pengali']-2))+ (($bargu['gajipokok']) +  ($bargu['tunjangan']) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21st)) + (($bargu['gajipokok']*(100/$bargu['lastvol'])*($bargu['firstvol']/100)) +  ($bargu['tunjangan']*(100/$bargu['lastvol'])*($bargu['firstvol'])/100) + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'] + floor($tjpph21st)) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'] + $bargu['bonustahunini'] + $bargu['thrtahunini'];
					}
					else {
						$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen'])+ floor($tjpph21st);
						$brutosetahun = ($bruto * $bargu['pengali']) + $bargu['lemburtahunini'] -  $bargu['pottahunini'] + $bargu['incentivethnini'] + $bargu['lainnyathnini'] + $bargu['bonus'] + $bargu['thr'] + $bargu['bonustahunini'] + $bargu['thrtahunini'];
					}
				}
				else {
					$bruto = ($bargu['gajipokok'] +  $bargu['tunjangan'] + $bargu['premiasuransi'] + $bargu['lembur'] - $bargu['potpen']) + floor($tjpph21st);
					$brutosetahun = ($bruto *$bargu['pengali']) + $bargu['bonus'] + $bargu['thr'];	
				}
								
				//hitung biaya jabatan
				if (($brutosetahun*($bargu['persen']/100)) >= ($bargu['maks']*$bargu['pengali'])){
					$byjbt = ($bargu['maks']*$bargu['pengali']);
				}
				else {
					$byjbt = $brutosetahun*($bargu['persen']/100);
				}
				
				//hitung netto
				if ($bargu['akhirperiode']==1){
					$nettos = ($brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']));
				}
				else {
					$nettos = $brutosetahun - $byjbt - ($bargu['iuranpengurang']*$bargu['pengali']);
				}
				
				//netto disetahunkan
				//$nettosetahun = $nettos * 12;
				
				//hitung penghasilan kena pajak
				$pkp = $nettos   - $bargu['ptkp'];
				$pkp = (floor($pkp/1000))*1000;
				
				//hitung pph bulanan
				$calpph21 = "select(round(ifnull((((select (b.persen*(".$pkp."-(ifnull((select sum(b.btsatas-b.btsbawah) from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0) ) ))/100
				from sdm_master_tarif_pph21 b WHERE b.btsbawah<=".$pkp." AND b.btsatas>=".$pkp."   and b.isnpwp=1 and b.aktif=1 LIMIT 1) 
				)+
				ifnull((select sum((b.btsatas-b.btsbawah)*b.persen)/100 from sdm_master_tarif_pph21 b WHERE  b.btsatas<=".$pkp." and b.isnpwp=1 and b.aktif=1 ),0))/".$bargu['pengali'].", 0),5)) as pph21";
				
				$respgu=$eksi->sSQL($calpph21);
				foreach($respgu as $barpgu){
					//rubah cek pkp minus atau tidak  (jika minus bebas pajak) ==Jo 15-03-2017==
					if($pkp>0){
						$pph21gust = $barpgu['pph21'];
					}
					else {
						$pph21gust = 0;
					}
					
					//$pph21guvalt = $pph21gut *12;
					if (!empty($bargu['npwp'])){
						$pph21guvalst = $pph21gust *$bargu['pengali'];
					}
					else {
						$pph21guvalst = $pph21gust * ($bargu['persennpwp']/100) * $bargu['pengali'];
					}
				}
				
				$sfst++;
			}
			$strslc = "select karyawanid from setup_pkp_grossup where 
			periode='".$periode."' and karyawanid='".$userid."'";
			//$resslc = $eksi->sSQL($strslc);
			if ($eksi->sSQLnum($strslc)>0){
				$strup = "update setup_pkp_grossup set pengali=".$bargu['pengali'].", gajibrutosetahundanbonusthr=".$brutosetahun.",
				iuranpensiunsetahun=".($bargu['iuranpengurang']*$bargu['pengali']).", biayajabatansetahundanbonusthr=".$byjbt.", gajinettosetahundanbonusthr=".$nettos.", pkpsetahundanbonusthr=".$pkp.", npwp='".$bargu['npwp']."',lastpayment='".$bargu['akhirperiode']."', lokasitugas='".$bargu['lokasitugas']."', statuspajak='".$bargu['statuspajak']."', ptkp='".$bargu['ptkp']."', FlagPPHGrossUP='".$bargu['FlagPPHGrossUP']."'
				where karyawanid='".$userid."' and periode='".$periode."'";
				$eksi->sSQL($strup);
			}
			else {
				$strins = "insert into setup_pkp_grossup (karyawanid,periode,npwp,lastpayment,lokasitugas,pengali,gajibrutosetahundanbonusthr,iuranpensiunsetahun,biayajabatansetahundanbonusthr,
				gajinettosetahundanbonusthr,
				statuspajak,ptkp,
				pkpsetahundanbonusthr,FlagPPHGrossUP)
				values('".$userid."', '".$periode."', '".$bargu['npwp']."','".$bargu['akhirperiode']."','".$bargu['lokasitugas']."', '".$bargu['pengali']."', ".$brutosetahun.", ".($bargu['iuranpengurang']*$bargu['pengali']).", ".$byjbt.", ".$nettos.",'".$bargu['statuspajak']."','".$bargu['ptkp']."','".$pkp."','".$bargu['FlagPPHGrossUP']."')";
				$eksi->sSQL($strins);
			}
		}
		
		return array($nettos, $pph21guvalst);
		
	}
	
	function hitungPPh21Penambah($userid,$component,$periode,$plus,$eksi,$pengalix,$lastperiods,$lastpaydate,$flaggrossup,$lokasitgs,$pph21calc){
		$str="delete from sdm_ho_detailmonthly
			where karyawanid=".$userid."
			and periode='".$periode."' and component = '".$component."'";
		$eksi->exc($str); 
		//untuk normal
		if ($flaggrossup==0){
			$str="insert into sdm_ho_detailmonthly 
			(karyawanid,component,value,periode,plus,updatedby) 
			values(".$userid.",".$component.",0,'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
			$eksi->exc($str);
			
			$strs = "update sdm_ho_basicsalary 
			set value = 0 where karyawanid ='".$userid."' and component = ".$component."";
			$eksi->exc($strs);

		}
		//untuk gross up
		else if($flaggrossup==1) {
			$pph21gures = $pph21calc->hitungGU($pph21calc->kueriPPh21($periode, $userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21gubns = $pph21calc->hitungGUBns($pph21calc->kueriPPh21($periode, $userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21guthr = $pph21calc->hitungGUThr($pph21calc->kueriPPh21($periode, $userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
									
			$pph21gubt = $pph21calc->hitungGUBT($pph21calc->kueriPPh21($periode, $userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21atasbonusthr = $pph21gubt[1] - $pph21gures[1];
			$pph21val = $pph21gures[1]/$pengalix;
										
			$pph21danbonusthr = $pph21val + $pph21atasbonusthr;
			if ($lastperiods ==0){
				//bukan periode akhir pph21
				
			}
			else {
				$strsumbt = "select sum(ifnull(k.pph21danbonusthr,0)) as summpphbt from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$resbt=$eksi->sSQL($strsumbt);
				foreach($resbt as $barbt)	   
				{
					$pph21danbonusthr = $pph21gubt[1] - $barbt['summpphbt'];
				}
				
				
			}
			if ($pph21danbonusthr<0){
				$pph21danbonusthrs = $pph21danbonusthr * -1;
			}
			else {
				$pph21danbonusthrs = $pph21danbonusthr;
			}
			
			$str="insert into sdm_ho_detailmonthly 
			(karyawanid,component,value,periode,plus,updatedby) 
			values(".$userid.",".$component.",".$pph21danbonusthrs.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
			$eksi->exc($str);
			
			$strs = "update sdm_ho_basicsalary 
			set value = ".$pph21danbonusthrs." where karyawanid ='".$userid."' and component = ".$component."";
			$eksi->exc($strs);
			
		}
	}
	
	function hitungPPh21Pengurang($userid,$component,$periode,$plus,$eksi,$pengalix,$lastperiods,$lastpaydate,$flaggrossup,$lokasitgs,$pph21calc){
		$str="delete from sdm_ho_detailmonthly
					where karyawanid=".$userid."
					and periode='".$periode."' and component = '".$component."'";
		$eksi->exc($str); 
		if ($flaggrossup==0){
			$pph21normal=$pph21calc->hitungpph($pph21calc->kueriPPh21($periode,$userid),0,'','',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21bns = $pph21calc->hitungpphbns($pph21calc->kueriPPh21($periode,$userid),0,'','',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21thr =$pph21calc->hitungpphthr($pph21calc->kueriPPh21($periode,$userid),0,'','',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21bt = $pph21calc->hitungpphbt($pph21calc->kueriPPh21($periode,$userid),0,'','',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			if ($lastperiods==0){
				//bukan periode akhir pph21
				$pph21bulanan = $pph21normal[1]/$pengalix;
				$pph21atasbonus = $pph21bns[1] - $pph21normal[1];
				$pph21atasthr = $pph21thr[1] - $pph21normal[1];
				$pph21atasbonusthr = $pph21bt[1] - $pph21normal[1];
				$pph21bulanandanbonus = $pph21bulanan + $pph21atasbonus;
				$pph21bulanandanthr = $pph21bulanan + $pph21atasthr;
				$pph21bulanandanbonusthr = $pph21bulanan + $pph21atasbonusthr;
				
			}
			else {
				$strsumpp = "select sum(ifnull(k.pph21,0)) as summpph from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$respp=$eksi->sSQL($strsumpp);
				foreach($respp as $barpp)	   
				{
					$pph21bulanan = $pph21normal[1] - $barpp['summpph'];
				}
				
				$strsumbp = "select sum(ifnull(k.pph21danbonus,0)) as summpphb from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and  k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$resbp=$eksi->sSQL($strsumbp);
				foreach($resbp as $barbp)	   
				{
					$pph21bulanandanbonus = $pph21bns[1] - $barbp['summpphb'];
				}
				
				$strsumtp = "select sum(ifnull(k.pph21danthr,0)) as summppht from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and  k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$restp=$eksi->sSQL($strsumtp);
				foreach($restp as $bartp)	   
				{
					$pph21bulanandanthr = $pph21thr[1] - $bartp['summppht'];
				}
				
				$strsumbt = "select sum(ifnull(k.pph21danbonusthr,0)) as summpphbt from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$resbt=$eksi->sSQL($strsumbt);
				foreach($resbt as $barbt)	   
				{
					$pph21bulanandanbonusthr = $pph21bt[1] - $barbt['summpphbt'];

				}
				
				$pph21atasbonus = $pph21bulanandanbonus - $pph21bulanan;
				$pph21atasthr = $pph21bulanandanthr - $pph21bulanan;
				$pph21atasbonusthr = $pph21bulanandanbonusthr - $pph21bulanan;
				
			}
			
			if ($pph21atasbonus<0){
				$pph21atasbonuss = $pph21atasbonus * -1;
			}
			else {
				$pph21atasbonuss = $pph21atasbonus;
			}
			
			if ($pph21atasthr<0){
				$pph21atasthrs = $pph21atasthr * -1;
			}
			else {
				$pph21atasthrs = $pph21atasthr;
			}
			if ($pph21bulanan<0){
				$pph21vals = $pph21bulanan * -1;
			}
			else {
				$pph21vals = $pph21bulanan;
			}
			
			if ($pph21bulanandanbonus<0){
				$pph21danbonuss = $pph21bulanandanbonus * -1;
			}
			else{
				$pph21danbonuss = $pph21bulanandanbonus;
			}
			
			if ($pph21bulanandanthr<0){
				$pph21danthrs = $pph21bulanandanthr * -1;
			}
			else {
				$pph21danthrs = $pph21bulanandanthr;
			}
			
			if ($pph21bulanandanbonusthr<0){
				$pph21danbonusthrs = $pph21bulanandanbonusthr * -1;
			}
			else {
				$pph21danbonusthrs = $pph21bulanandanbonusthr;
			}
			
			$str="insert into sdm_ho_detailmonthly 
			(karyawanid,component,value,periode,plus,updatedby) 
			values(".$userid.",".$component.",-".$pph21danbonusthrs.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
			//echo "warning:".$str;
			$eksi->exc($str);
			
			$strs = "update sdm_ho_basicsalary 
			set value = ".$pph21danbonusthrs." where karyawanid ='".$userid."' and component = ".$component."";
			$eksi->exc($strs);
			
			//update tabel sdm_pph21_data
			$strd="delete from sdm_pph21_data
				where karyawanid='".$userid."' and periode='".$periode."'";
			$eksi->exc($strd);
			
			$netto = $pph21normal[0]/$pengalix; 
			$nettodanbonus =  ($pph21normal[0]/$pengalix) + ($pph21bns[0] - $pph21normal[0]);
			$nettodanthr =  ($pph21normal[0]/$pengalix) + ($pph21thr[0]- $pph21normal[0]);
			$nettodanbonusthr =  ($pph21normal[0]/$pengalix) + ($pph21bt[0]- $pph21normal[0]);
			
			//echo "warning: netto: ".$pph21normal[0]." pengali: ".$pengalix;
			/*$strdd="insert into sdm_pph21_data
			(karyawanid,periode,netto, nettodanbonus, nettodanthr, nettodanbonusthr, pph21, pph21danbonus, pph21danthr, pph21danbonusthr, pph21atasbonus, pph21atasthr) 
			values('".$userid."', '".$periode."', ".$netto.", ".$nettodanbonus.", ".$nettodanthr.",
			".$nettodanbonusthr.",".$pph21vals.", ".$pph21danbonuss.",".$pph21danthrs." ,
			".$pph21danbonusthrs.", ".$pph21atasbonuss.", ".$pph21atasthrs.")";*/
			
			//rubah supaya kalau minus (lebih bayar tetap disimpan)== JO 15-03-2017==
			$strdd="insert into sdm_pph21_data
			(karyawanid,periode,netto, nettodanbonus, nettodanthr, nettodanbonusthr, pph21, pph21danbonus, pph21danthr, pph21danbonusthr, pph21atasbonus, pph21atasthr) 
			values('".$userid."', '".$periode."', ".$netto.", ".$nettodanbonus.", ".$nettodanthr.",
			".$nettodanbonusthr.",".$pph21bulanan.", ".$pph21bulanandanbonus.",".$pph21bulanandanthr." ,
			".$pph21bulanandanbonusthr.", ".$pph21atasbonus.", ".$pph21atasthr.")";
			$eksi->exc($strdd);
		}
		//untuk gross up
		else if($flaggrossup==1) {
			$pph21gures = $pph21calc->hitungGU($pph21calc->kueriPPh21($periode,$userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21gubns = $pph21calc->hitungGUBns($pph21calc->kueriPPh21($periode,$userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21guthr = $pph21calc->hitungGUThr($pph21calc->kueriPPh21($periode,$userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
									
			$pph21gubt = $pph21calc->hitungGUBT($pph21calc->kueriPPh21($periode,$userid),0,'',0,'',
			0,0,0,0,0,0,0,0,0,
			'','',$periode,$userid,$eksi);
			
			$pph21atasbonus =  $pph21gubns[1]-$pph21gures[1];
			$pph21atasthr = $pph21guthr[1]-$pph21gures[1];
			
			$pph21atasbonusthr = $pph21gubt[1] - $pph21gures[1];
			
			
			$pph21val= $pph21gures[1]/$pengalix;
			
			$pph21danbonus = $pph21val + $pph21atasbonus;
			
			$pph21danthr = $pph21val + $pph21atasthr;
			
			$pph21danbonusthr = $pph21val + $pph21atasbonusthr;
			//echo "warning: dengan bonus".$pph21gubt[1]." tanpa bonus : ".$pph21gures[1];
			if ($lastperiods==0){
				//bukan periode akhir pph21
				
			}
			else {
				$strsumpp = "select sum(ifnull(k.pph21,0)) as summpph from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$respp=$eksi->sSQL($strsumpp);
				foreach($respp as $barpp)	   
				{
					$pph21val = $pph21gures[1] - $barpp['summpph'];
				}
				
				$strsumbp = "select sum(ifnull(k.pph21danbonus,0)) as summpphb from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and  k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$resbp=$eksi->sSQL($strsumbp);
				foreach($resbp as $barbp)	   
				{
					$pph21danbonus = $pph21gubns[1] - $barbp['summpphb'];
				}
				
				$strsumtp = "select sum(ifnull(k.pph21danthr,0)) as summppht from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and  k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$restp=$eksi->sSQL($strsumtp);
				foreach($restp as $bartp)	   
				{
					$pph21danthr = $pph21guthr[1] - $bartp['summppht'];
				}
				
				$strsumbt = "select sum(ifnull(k.pph21danbonusthr,0)) as summpphbt from sdm_pph21_data k where k.periode like concat((select date_format(STR_TO_DATE(t.tanggalsampai,'%Y'),'%Y') 
				FROM sdm_5periodegaji t WHERE t.jenisgaji='B' AND t.kodeorg=substr('".$lokasitgs."',1,4) AND  t.sudahproses=0 ORDER BY t.periode asc LIMIT 1),'%')
				and k.karyawanid='".$userid."' and substr(k.periode,6,7) <> '12' and k.periode <> '".$lastpaydate."'";
				$resbt=$eksi->sSQL($strsumbt);
				foreach($resbt as $barbt)	   
				{
					$pph21danbonusthr = $pph21gubt[1] - $barbt['summpphbt'];
				}
				
				$pph21atasbonus = $pph21danbonus - $pph21val;
				$pph21atasthr = $pph21danthr - $pph21val;
				$pph21atasbonusthr = $pph21danbonusthr - $pph21val;
			}
			
			
			if ($pph21atasbonus<0){
				$pph21atasbonuss = $pph21atasbonus * -1;
			}
			else {
				$pph21atasbonuss = $pph21atasbonus;
			}
			
			if ($pph21atasthr<0){
				$pph21atasthrs = $pph21atasthr * -1;
			}
			else {
				$pph21atasthrs = $pph21atasthr;
			}
			
			if ($pph21val<0){
				$pph21vals = $pph21val * -1;
			}
			else {
				$pph21vals = $pph21val;
			}
			
			if ($pph21danbonus<0){
				$pph21danbonuss = $pph21danbonus * -1;
			}
			else{
				$pph21danbonuss = $pph21danbonus;
			}
			
			if ($pph21danthr<0){
				$pph21danthrs = $pph21danthr * -1;
			}
			else {
				$pph21danthrs = $pph21danthr;
			}
			
			if ($pph21danbonusthr<0){
				$pph21danbonusthrs = $pph21danbonusthr * -1;
			}
			else {
				$pph21danbonusthrs = $pph21danbonusthr;
			}

			$str="insert into sdm_ho_detailmonthly 
			(karyawanid,component,value,periode,plus,updatedby) 
			values(".$userid.",".$component.",-".$pph21danbonusthrs.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";
			$eksi->exc($str);
			
			
			$strs = "update sdm_ho_basicsalary 
			set value = ".$pph21danbonusthrs." where karyawanid ='".$userid."' and component = ".$component."";
			$eksi->exc($strs);
			
			//update tabel sdm_pph21_data
			$strd="delete from sdm_pph21_data
				where karyawanid='".$userid."' and periode='".$periode."'";
			$eksi->exc($strd);
			$netto = $pph21gures[0]/$pengalix;
			$nettodanbonus =  ($pph21gures[0]/$pengalix) + ($pph21gubns[0]- $pph21gures[0]);
			$nettodanthr =  ($pph21gures[0]/$pengalix) + ($pph21guthr[0] - $pph21gures[0]);
			$nettodanbonusthr =  ($pph21gures[0]/$pengalix) + ($pph21gubt[0]- $pph21gures[0]);
			
			
			/*$strdd="insert into sdm_pph21_data
			(karyawanid,periode,netto, nettodanbonus, nettodanthr, nettodanbonusthr, pph21, pph21danbonus, pph21danthr, pph21danbonusthr, pph21atasbonus, pph21atasthr) 
			values('".$userid."', '".$periode."', ".$netto.", ".$nettodanbonus.", ".$nettodanthr.", ".$nettodanbonusthr.", ".$pph21vals.", 
			".$pph21danbonuss.", ".$pph21danthrs.",  ".$pph21danbonusthrs.", ".$pph21atasbonuss.", ".$pph21atasthrs.")";*/
			
			//rubah supaya kalau minus (lebih bayar tetap disimpan)== JO 15-03-2017==
			$strdd="insert into sdm_pph21_data
			(karyawanid,periode,netto, nettodanbonus, nettodanthr, nettodanbonusthr, pph21, pph21danbonus, pph21danthr, pph21danbonusthr, pph21atasbonus, pph21atasthr) 
			values('".$userid."', '".$periode."', ".$netto.", ".$nettodanbonus.", ".$nettodanthr.", ".$nettodanbonusthr.", ".$pph21val.", 
			".$pph21danbonus.", ".$pph21danthr.",  ".$pph21danbonusthr.", ".$pph21atasbonus.", ".$pph21atasthr.")";
			
			$eksi->exc($strdd);
		}
	}
			
	
	
}
$pph21calc=new pph21calc;
?>