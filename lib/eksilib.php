<?php
class eksi{
	function BackDateCheck($company,$tgl){
		$str="SELECT periode FROM setup_periodeakuntansi 
			  WHERE kodeorg='".$company."' AND tanggalmulai<='".$tgl."' AND tanggalsampai>='".$tgl."' LIMIT 1";
		$rs=$this->sSQLnum($str);
		if ($rs<=0){
			echo "Warning : Periode akuntansi belum di setting atas Unit : ".$company."\n Silahkan buka periode akuntansi untuk periode tanggal :".$tgl; 
			return false;
		}		
		$str="SELECT periode FROM setup_periodeakuntansi 
			  WHERE kodeorg='".$company."' AND tanggalmulai<='".$tgl."' 
			  AND tanggalsampai>='".$tgl."' AND tutupbuku=0 ORDER BY periode ASC LIMIT 1";
		$rs1=$this->sSQLnum($str);
		if ($rs1<=0){
			echo "Warning : Periode akuntansi per tanggal ".$tgl." sudah tutup."; 
			return false;
		}else{
			return true;
		}
	} 
	function GetIndukByKodeOrg($KodeOrg){
		$ssql="SELECT induk from organisasi WHERE kodeorganisasi='".$KodeOrg."';";
		$return=$this->sSQL($ssql);
		return $return;
	}
	
	function baseURL() {
		$pageURL = '';
		$file=dirname($_SERVER["REQUEST_URI"].'?');
		$data=explode('/',$file);
		/*
		if ($_SERVER["HTTPS"] == "on" {
		 $pageURL .= "s";
		 }*/  
		 $pageURL .= "";
		if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $data[1];
		} else {
		  $pageURL .= $data[1];
		}  return $pageURL.'/';
	}

	function ConnectionManual($dbserver,$dbport,$uname,$passwd,$dbname){
		@$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Error/Gagal : Unable to Connect to database ".$dbserver.$dbport.$uname.$passwd.$dbname);
		$mysqli = new mysqli($dbserver, $uname, $passwd, $dbname);
		mysql_select_db($dbname);
	}
	
	function GetPathReport($KodeOrg, $Module, $NamaFile){
		$ssql="SELECT * from report_path WHERE kodeorg='".$KodeOrg."';";
		$cek=$this->sSQLnum($ssql);
		if($cek == 0){
			$addSQL="INSERT INTO `report_path` (`kodeorg`, `lokasi`) VALUES ('".$KodeOrg."', 'report/')";
			$this->lock('report_path');
			$hasil=$this->exc($addSQL);
			$this->unlock();
		}
		
		$ssql="SELECT a.*,b.* from report_path_detail a LEFT JOIN report_path b ON a.kodeorg = b.kodeorg WHERE a.kodeorg='".$KodeOrg."' and a.module = '".$Module."';";
		$cek=$this->sSQLnum($ssql);
		if($cek == 0){
			$addSQL="INSERT INTO `report_path_detail` (`kodeorg`, `module`, `namafile`) VALUES ('".$KodeOrg."', '".$Module."', '".$NamaFile."')";
			$this->lock('report_path_detail');
			$hasil=$this->exc($addSQL);
			$this->unlock();
			
			$ssql="SELECT * from report_path_detail WHERE kodeorg='".$KodeOrg."' and module = '".$Module."';";
			$return=$this->sSQL($ssql);
			return $return;
		} else {
			$return=$this->sSQL($ssql);
			return $return;
		}
		
	}
	
	/*replace special character*/
	function myUrldecode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D','%0A');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]","\n");
		return str_replace($entities, $replacements, urlencode($string));
	}
	function myUrlcode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D','%0A');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]","\n");
		return str_replace( $replacements,$entities, urlencode($string));
	}
	//tambah function replace enter ==Jo 06-07-2017==
	function replaceEnter($string){
		return htmlspecialchars(htmlentities(str_replace("\n","%0A",$string)));
	}
	
	function codeSC($text){
		//return $this->myUrlcode($text);
		return htmlspecialchars(htmlentities(str_replace("\n"," ",$text)));
	}
	function decodeSC($text){		
		//return $this->myUrldecode($text);
		$text=str_replace("&amp;","&",$text);
		return htmlspecialchars_decode(htmlentities($text));
	}
	
	/*ADD/MODIFY COLUMNS*/
	Public function cekfield($table,$namafield){		
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."';";
		$cek=$this->sSQLnum($ssql);
		return $cek;
	}
	function cekfieldtype($table,$namafield,$typedata){		
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."' AND type='".trim($typedata)."';";
		$cek=$this->sSQLnum($ssql);
		return $cek;
	}
	function addField($table,$namafield,$typedata,$type){
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."';";
		$cek=$this->sSQLnum($ssql);
		$ssql1="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."' AND type='".trim($typedata)."';";
		$cek1=$this->sSQLnum($ssql1);		
		$rs=$this->sSQL($ssql);
		$addSQL="";
		if($type=="A"){
			if($cek>0){
				EXIT("Table :".$table." Field : ".$namafield." sudah ada.");
			}
			$addSQL="ALTER TABLE ".$table." ADD ".$namafield." ".$typedata.";";
		}
		if($type=="M"){
			if($cek>0){
				if ($cek1>0){
					EXIT("Table :".$table." Field : ".$namafield." sudah ada.");
				}
			}
			$addSQL="ALTER TABLE ".$table." ALTER COLUMN ".$namafield." ".$typedata.";";			
		}
		$this->lock($table);
		$hasil=$this->exc($addSQL);
		$this->unlock();
		return $hasil;
	}
	
	/*
		$hasil=datediff($tgl1, $tgl2);
		$tahun=$hasil['years'];
		$bulan=$hasil['months'];
		$hari=$hasil['days'];
		$totalbulan=$hasil['months_total'];
		$totalhari=$hasil['days_total'];
		$totaljam=$hasil['hours_total'];
		$totalmenit=$hasil['minutes_total'];
		$totaldetik=$hasil['seconds_total'];
		$jam=$hasil['hours'];
		$menit=$hasil['minutes'];
		$detik=$hasil['seconds'];		
	*/
	function datediff($tgl1, $tgl2){
		$tgl1 = strtotime($tgl1);
		$tgl2 = strtotime($tgl2);
		$diff_secs = abs($tgl1 - $tgl2);
		$base_year = min(date("Y", $tgl1), date("Y", $tgl2));
		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
		return array( "years" => date("Y", $diff) - $base_year, "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff), "months" => date("n", $diff) - 1, "days_total" => floor($diff_secs / (3600 * 24)), "days" => date("j", $diff) - 1, "hours_total" => floor($diff_secs / 3600), "hours" => date("G", $diff), "minutes_total" => floor($diff_secs / 60), "minutes" => (int) date("i", $diff), "seconds_total" => $diff_secs, "seconds" => (int) date("s", $diff) );
	}
	
	function ymd($tgl){
		return date('Y-m-d', strtotime($tgl));
	}
	
	function dmy($tgl) {
		return date('d-m-Y', strtotime($tgl));
	}
	
	function hapus($table,$where){
		if(trim($where)!=""){
			$this->exc('LOCK TABLE '.$table.' WRITE;');
			$string="DELETE FROM ".$table." ".$where;
			$m=$this->exc($string);	
			$this->exc('UNLOCK TABLES;');
			
			return $m;	
		}
	}
	function lock($table){
		$this->exc('LOCK TABLE '.$table.' WRITE;');
	}
	function unlock(){
		$this->exc('UNLOCK TABLES;');		
	}	
	// untuk mendapatkan data dari database dalam bentuk Array
	// $array=$eksi->sSQL($Query)
	function sSQL($Query){
		require_once('config/connection.php');	
		$hasil=mysql_query($Query);
		$data=array();
		if(! $hasil )
		{
		  die('Could not get data: ' . mysql_error());
		}
		//while($row = mysql_fetch_array($hasil, MYSQL_ASSOC))
		//rubah tanpa ASSOC supaya bisa pilih by index ==Jo 13-01-2017==
		while($row = mysql_fetch_array($hasil))
		{
			array_push($data,$row);
		} 
		return $data; 
		mysql_close(@$conn);
	}
	
	// untuk mendapatkan data dari jumlah row database ==Jo 13-01-2017==
	// $array=$eksi->sSQLnum($Query)
	function sSQLnum($Query){
		require_once('config/connection.php');	
		$hasil=mysql_query($Query);
		$data=array();
		if(!$hasil)
		{
		  die('Could not get data: ' . mysql_error());
		}
		$numrow=mysql_num_rows($hasil);
		return $numrow;
		mysql_close(@$conn);
	}
	
	// untuk mendapatkan data dari database dalam bentuk JSON
	// $array=$eksi->getJSON($Query)
	function getJSON($Query){
		require_once('config/connection.php');	
		$hasil=mysql_query($Query);
		$data=array();
		if(! $hasil )
		{
		  die('Could not get data: ' . mysql_error());
		}
		while($row = mysql_fetch_array($hasil, MYSQL_ASSOC))
		{
			array_push($data,$row);
		} 
		return json_encode($data); 
		mysql_close(@$conn);
		
	}	
	
	// untuk execute Query Insert, Delete, Update
	// $result=$eksi->exc($Query)
	function exc($query){
		include('config/connection.php');
		$hasil=	mysql_query($query);
		return $hasil;
		mysql_close(@$conn);
	}
	
	// untuk generate sintak Query Insert dan Update
	/* 
		$table=nama table,
		$arraySQL=array(
			'fs_kd_parent'=>'\'01\'',
			'fs_kd_child'=>'\'P01\'',
		)
		$update=true/false
	*/
	// $string=$eksi->genSQL($table,$arraySQL,$update)
	function genSQL($table,$arraySQL,$update){
		$SQL='';
		$temparray=$arraySQL;
		$header='';
		$isi='';
		$query='';
		foreach($arraySQL as $row){
			$SQL.=key($temparray)."=".current($temparray).",";
			$header.=key($temparray).",";
			$isi.=current($temparray).",";			
			next($temparray);
		}
		$SQL=substr(trim($SQL),0,strlen(trim($SQL))-1);
		$header=substr(trim($header),0,strlen(trim($header))-1);
		$isi=substr(trim($isi),0,strlen(trim($isi))-1);
		
		if ($update==true){
			$query=' UPDATE '.$table.' SET '.$SQL.' ';
		}else{
			$query=' INSERT INTO '.$table.'('.trim($header).') VALUES('.trim($isi).')';
		}
		return $query;
	}
}
$eksi=new eksi;
?>