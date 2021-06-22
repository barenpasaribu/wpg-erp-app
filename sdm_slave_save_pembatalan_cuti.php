<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
#pre($_POST);exit();
$method		  	= isset($_POST['method']) ? $_POST['method'] : null;
$karyawanid 	= isset($_POST['karyawanid']) ? $_POST['karyawanid'] : null;
$tanggal	  	= isset($_POST['tanggal']) ? $_POST['tanggal'] : null;
$persetujuan1  	= isset($_POST['persetujuan1']) ? $_POST['persetujuan1'] : '';
$hrd	  		= isset($_POST['hrd']) ? $_POST['hrd'] : '';
$Id	  			= isset($_POST['Id']) ? $_POST['Id'] : '';
//tambah id untuk tabel sdm_ijin ==Jo 16-05-2017==
$ids	  			= isset($_POST['ids']) ? $_POST['ids'] : '';
$kodeorg	  	= isset($_POST['kodeorg']) ? $_POST['kodeorg'] : '';
showerror();

function GetSisaCuti($kodeorg, $karyawanid, $periodecuti){
	global $dbname;
	
	$sSisaCuti = "SELECT sisa, diambil, periodecuti, hakcuti FROM ".$dbname.".sdm_cutiht
	WHERE kodeorg = '".$kodeorg."' and karyawanid = '".$karyawanid."' AND sampai >= '".$periodecuti."'";
	
	$rSisaCuti = fetchData($sSisaCuti);
	foreach($rSisaCuti as $kSC => $vSC) {
		if($vSC['hakcuti'] == $vSC['sisa']) {
			unset($rSisaCuti[$kSC]);
		} else {
			unset($rSisaCuti[$kSC+1]);
		}
	}
	#pre($sSisaCuti);exit();
	return $rSisaCuti;	
}

switch($method)
{
	case'insert':
		if($persetujuan1 == $_SESSION['standard']['userid']) {
			$data=array("tanggal"=>"'".$tanggal."'",
					  "persetujuan1"=>"".$persetujuan1."",
					  "stpersetujuan1"=>"1",
					  "hrd"=>"".$hrd."",
					  "stpersetujuanhrd"=>"0",
					  "karyawanid"=>"'".$karyawanid."'",
					  "sdm_ijinid"=>"".$ids."");
			
			$strins=$eksi->genSQL('log_sdm_batal_ijin',$data,false);
			$eksi->exc($strins);
			/*$InsertLog = array(
				'id'	=> '',
				'tanggal' => $tanggal,
				'persetujuan1' => $persetujuan1,
				'stpersetujuan1' => '1',
				'hrd' => $hrd,
				'stpersetujuanhrd' => '0',
				'karyawanid' => $karyawanid,
				'sdm_ijinid' => $ids
			);
			mysql_query(insertQuery($dbname,'log_sdm_batal_ijin',$InsertLog));*/
		} else if($hrd == $_SESSION['standard']['userid']){
			/*$InsertLog = array(
				'id'	=> '',
				'tanggal' => $tanggal,
				'persetujuan1' => $persetujuan1,
				'stpersetujuan1' => '0',
				'hrd' => $hrd,
				'stpersetujuanhrd' => '1',
				'karyawanid' => $karyawanid,
				'sdm_ijinid' => $ids
			);
			
			mysql_query(insertQuery($dbname,'log_sdm_batal_ijin',$InsertLog));*/
			$data=array("tanggal"=>"'".$tanggal."'",
					  "persetujuan1"=>"".$persetujuan1."",
					  "stpersetujuan1"=>"0",
					  "hrd"=>"".$hrd."",
					  "stpersetujuanhrd"=>"1",
					  "karyawanid"=>"'".$karyawanid."'",
					  "sdm_ijinid"=>"".$ids."");
			
			$strins=$eksi->genSQL('log_sdm_batal_ijin',$data,false);
			$eksi->exc($strins);
		}
	break;
	case'delete':
		mysql_query(deleteQuery($dbname,'log_sdm_batal_ijin',"id='".$Id."'"));
	break;
	case'update':
		if($persetujuan1 == $_SESSION['standard']['userid'] && $hrd != $_SESSION['standard']['userid'])
		{
			$UpdateCuti = array(
				'stpersetujuan1' => '1'
			);
			$WhereUpdateCuti = "id = '".$Id."'";
			if(mysql_query(updateQuery($dbname,'log_sdm_batal_ijin',$UpdateCuti,$WhereUpdateCuti)))
			{
				$Cuti = GetSisaCuti($kodeorg, $karyawanid, $tanggal);
				$SisaCuti = $Cuti[0]['sisa'] += 1;
				$CutiDiambil = $Cuti[0]['diambil'] -=1;
				
				mysql_query("update ".$dbname.".sdm_cutiht set diambil='".$CutiDiambil."',
				sisa=".$SisaCuti."
				where kodeorg='".$kodeorg."' and karyawanid='".$karyawanid."' and periodecuti='".$Cuti[0]['periodecuti']."'");
				
				//dapatkan data ambil cuti dan sisanya dari data tersebut ==Jo 25-04-2017==
				/*$slcuti="select jumlahhari, sisacuti from sdm_ijin where karyawanid='".$karyawanid."'
				and '".$tanggal."' between date_format(STR_TO_DATE(substr(darijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(substr(sampaijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')";
				
				$rescuti=$eksi->sSQL($slcuti);
				foreach($rescuti as $barcuti){
					$jmhari=$barcuti['jumlahhari'];
					$sscuti=$barcuti['sisacuti'];
				}
				$jmharis=$jmhari-1;
				$sscutis=$sscuti+1;
				//tambah untuk update laporan cuti ==Jo 25-04-2017==
				$data=array("jumlahcuti"=>"".$jmharis."");
				$string=$eksi->genSQL('sdm_cutidt',$data,true)." 
					WHERE  kodeorg='".$kodeorg."' AND karyawanid='".$karyawanid."' AND '".$tanggal."' between date_format(STR_TO_DATE(daritanggal,'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(sampaitanggal,'%Y-%m-%d'),'%Y-%m-%d')";
				
				$eksi->exc($string);
				
				//tambah untuk update daftar izin/cuti/day off ==Jo 25-04-2017==
				$data=array("jumlahhari"=>"".$jmharis."",
					  "sisacuti"=>"".$sscutis."");
				$string=$eksi->genSQL('sdm_ijin',$data,true)." 
					WHERE karyawanid='".$karyawanid."' AND '".$tanggal."' between date_format(STR_TO_DATE(substr(darijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(substr(sampaijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')";
				
				$eksi->exc($string);*/
				
			}
		}
		else if($hrd == $_SESSION['standard']['userid'])
		{
			$UpdateCuti = array(
				'stpersetujuanhrd' => '1'
			);
			$WhereUpdateCuti = "id = '".$Id."'";
			
			if(mysql_query(updateQuery($dbname,'log_sdm_batal_ijin',$UpdateCuti,$WhereUpdateCuti)))
			{
				
				$Cuti = GetSisaCuti($kodeorg, $karyawanid, $tanggal);
				$SisaCuti = $Cuti[0]['sisa'] += 1;
				$CutiDiambil = $Cuti[0]['diambil'] -=1;
				
				mysql_query("update ".$dbname.".sdm_cutiht set diambil='".$CutiDiambil."',
				sisa=".$SisaCuti."
				where kodeorg='".$kodeorg."' and karyawanid='".$karyawanid."' and periodecuti='".$Cuti[0]['periodecuti']."'");
				
				//dapatkan data ambil cuti dan sisanya dari data tersebut ==Jo 25-04-2017==
				/*$slcuti="select jumlahhari, sisacuti from sdm_ijin where karyawanid='".$karyawanid."'
				and '".$tanggal."' between date_format(STR_TO_DATE(substr(darijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(substr(sampaijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')";*/
				//rubah karena ada sdm_ijinid ==Jo 16-05-2017==
				$slcuti="select jumlahhari, sisacuti from sdm_ijin where id='".$ids."'";
				$rescuti=$eksi->sSQL($slcuti);
				foreach($rescuti as $barcuti){
					$jmhari=$barcuti['jumlahhari'];
					$sscuti=$barcuti['sisacuti'];
				}
				$jmharis=$jmhari-1;
				$sscutis=$sscuti+1;
				//tambah untuk update laporan cuti ==Jo 25-04-2017==
				$data=array("jumlahcuti"=>"".$jmharis."");
				/*$string=$eksi->genSQL('sdm_cutidt',$data,true)." 
					WHERE  kodeorg='".$kodeorg."' AND karyawanid='".$karyawanid."' AND '".$tanggal."' between date_format(STR_TO_DATE(daritanggal,'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(sampaitanggal,'%Y-%m-%d'),'%Y-%m-%d')";*/
				//rubah karena ada sdm_ijinid ==Jo 16-05-2017==
				$string=$eksi->genSQL('sdm_cutidt',$data,true)." 
					WHERE  sdm_ijinid='".$ids."'";
				$eksi->exc($string);
				
				//tambah untuk update daftar izin/cuti/day off ==Jo 25-04-2017==
				$data=array("jumlahhari"=>"".$jmharis."",
					  "sisacuti"=>"".$sscutis."");
				/*$string=$eksi->genSQL('sdm_ijin',$data,true)." 
					WHERE karyawanid='".$karyawanid."' AND '".$tanggal."' between date_format(STR_TO_DATE(substr(darijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')
					and date_format(STR_TO_DATE(substr(sampaijam,1,10),'%Y-%m-%d'),'%Y-%m-%d')";*/
				//rubah karena ada sdm_ijinid ==Jo 16-05-2017==
				$string=$eksi->genSQL('sdm_ijin',$data,true)." 
					WHERE id='".$ids."'";
				$eksi->exc($string);
				
			}
		}
	break;
	default:
	break;
}
?>