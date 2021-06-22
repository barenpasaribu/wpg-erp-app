<?php

	require_once('master_validation.php');
	require_once('config/connection.php');
	//require_once('lib/nangkoelib.php');
	require_once('lib/eagrolib.php');

	$unit=$_POST['unit'];

	// $tglAwal=tanggalsystem($_POST['tglAwal']);
	// $tglAkhir=tanggalsystem($_POST['tglAkhir']);

	if($unit==''){
		echo"warning: Working unit required";exit();
	}

	// if($tglAwal==''||$tglAkhir==''){
	// 	echo "Warning: date required"; exit;
	// }
	// $str="SELECT sum(a.jumlah) as jumlah, a.satuan, b.kodeorg, b.kodevhc, sum(jlhbbm) jumlahbbm FROM vhc_rundt a INNER JOIN vhc_runht b ON a.notransaksi=b.notransaksi WHERE kodeorg='".$unit."' AND tanggal between '".$tglAwal."' and '".$tglAkhir."' group by b.kodevhc ";

	$str="SELECT kodevhc, jenisvhc, nomorrangka,nomormesin,tahunperolehan,tgljtstnk, tglakhirstnk,tglakhirkir,tglakhirijinbm,tglakhirijinang ".
	"FROM vhc_5master WHERE kodeorg='".$unit."' ";
	//"ORDER BY tgljtstnk";

	$qry=mysql_query($str);

	$i=1;
	$dtNow = date("Y-m-d");
	$next_month = date("Y-m-d", strtotime("$dtNow +1 month"));
	
	while ($res=mysql_fetch_array($qry)) {
		$jtSTNK = (($res['tgljtstnk']=='0000-00-00' || $res['tgljtstnk']==null) ? '' : $res['tgljtstnk']);
		$akhirSTNK = (($res['tglakhirstnk']=='0000-00-00' || $res['tglakhirstnk']==null) ? '' : $res['tglakhirstnk']);
		$akhirKIR = (($res['tglakhirkir']=='0000-00-00' || $res['tglakhirkir']==null) ? '' : $res['tglakhirkir']);
		$akhirijinBM = (($res['tglakhirijinbm']=='0000-00-00' || $res['tglakhirijinbm']==null) ? '' : $res['tglakhirijinbm']);
		$akhirijinAng = (($res['tglakhirijinang']=='0000-00-00' || $res['tglakhirijinang']==null) ? '' : $res['tglakhirijinang']);

		// -- Check data J/T tahunan STNK
		$sts = array("satu"=> 0,"dua" => 0,"tiga" =>0,"empat" => 0,"lima" => 0);

		if ($jtSTNK=='') {
			$sts["satu"]=0;
		}else if ($jtSTNK<$dtNow) {
			$sts["satu"] = 2;
		}else if ($jtSTNK <= $next_month ) {
			$sts["satu"] = 1;
		}

		// -- Check data J/T akhir STNK
		if ($akhirSTNK =='') {
			$sts["dua"]=0;
		}else if ($akhirSTNK<$dtNow) {
			$sts["dua"]=2;
		}else if ($akhirSTNK <= $next_month ) {
			$sts["dua"]=1;
		}

		// -- Check data J/T akhir KIR
		if ($akhirKIR =='') {
			$sts["tiga"]=0;
		}else if ($akhirKIR<$dtNow) {
			$sts["tiga"]=2;
		}else if ($akhirKIR <= $next_month ) {
			$sts["tiga"]=1;
		}
		
		// -- Check data J/T akhir ijin Bongkar Muat
		if ($akhirijinBM =='') {
			$sts["empat"]=0;
		}else if ($akhirijinBM<$dtNow) {
			$sts["empat"]=2;
		}else if ($akhirijinBM <= $next_month ) {
			$sts["empat"]=1;
		}

		// -- Check data J/T akhir ijin Angkutan
		if ($akhirijinAng =='') {
			$style = "";
			$sts["lima"]=0;
		}else if ($akhirijinAng<$dtNow) {
			$style = "style='background-color:#ff3333;'";
			$sts["lima"]=2;
		}else if ($akhirijinAng <= $next_month ) {
			$style = "style='background-color:yellow;'";
			$sts["lima"]=1;
		}

		arsort($sts);
		$maxValue = array_values($sts)[0];

		$style = "";
		if ($maxValue==0) {
			$style = "";			
		}else if ($maxValue==1) {
			$style = "style='background-color:yellow;'";
		}else if ($maxValue==2) {
			$style = "style='background-color:#ff3333;'";
		}

		$jtSTNK = ($jtSTNK =='' ? ''  : date_format(date_create($jtSTNK),'d-m-Y'));
		$akhirSTNK = ($akhirSTNK =='' ? ''  : date_format(date_create($akhirSTNK),'d-m-Y'));
		$akhirKIR = ($akhirKIR =='' ? ''  : date_format(date_create($akhirKIR),'d-m-Y'));
		$akhirijinBM = ($akhirijinBM =='' ? ''  : date_format(date_create($akhirijinBM),'d-m-Y'));
		$akhirijinAng = ($akhirijinAng =='' ? ''  : date_format(date_create($akhirijinAng),'d-m-Y'));

		echo "<tr class=rowcontent ".$style.">

			<td align=center>".$i."</td>
			<td>".$res['kodevhc']."</td>
			<td align='center'>".$res['jenisvhc']."</td>
			<td align='center'>".$res['nomorrangka']."</td>
			<td align='center'>".$res['nomormesin']."</td>
			<td align='center'>".$res['tahunperolehan']."</td>
			<td align='center'>".$jtSTNK."</td>
			<td align='center'>".$akhirSTNK."</td>
			<td align='center'>".$akhirKIR."</td>
			<td align='center'>".$akhirijinBM."</td>
			<td align='center'>".$akhirijinAng."</td>

		</tr>";

		$i++;

	}


?>