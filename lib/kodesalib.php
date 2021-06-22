<?php
class kodesalib{
	function kds_BackDateCheck($company,$tgl){
		$str="SELECT periode FROM setup_periodeakuntansi
			  WHERE kodeorg='".$company."' AND tanggalmulai<='".$tgl."' AND tanggalsampai>='".$tgl."' LIMIT 1";
		$rs=$this->kds_sSQLnum($str);
		if ($rs<=0){
			echo "Warning : Periode akuntansi belum di setting atas Unit : ".$company."\n Silahkan buka periode akuntansi untuk periode tanggal :".$tgl;
			return false;
		}
		$str="SELECT periode FROM setup_periodeakuntansi
			  WHERE kodeorg='".$company."' AND tanggalmulai<='".$tgl."'
			  AND tanggalsampai>='".$tgl."' AND tutupbuku=0 ORDER BY periode ASC LIMIT 1";
		$rs1=$this->kds_sSQLnum($str);
		if ($rs1<=0){
			echo "Warning : Periode akuntansi per tanggal ".$tgl." sudah tutup.";
			return false;
		}else{
			return true;
		}
	}
	function kds_GetIndukByKodeOrg($KodeOrg){
		$ssql="SELECT induk from organisasi WHERE kodeorganisasi='".$KodeOrg."';";
		$return=$this->kds_sSQL($ssql);
		return $return;
	}

	function kds_baseURL() {
		$pageURL = '';
		$file=dirname($_SERVER["REQUEST_URI"].'?');
		$data=explode('/',$file);
		 $pageURL .= "";
		if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $data[1];
		} else {
		  $pageURL .= $data[1];
		}  return $pageURL.'/';
	}

	function kds_ConnectionManual($dbserver,$dbport,$uname,$passwd,$dbname){
		@$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Error/Gagal : Unable to Connect to database ".$dbserver.$dbport.$uname.$passwd.$dbname);
		$mysqli = new mysqli($dbserver, $uname, $passwd, $dbname);
		mysql_select_db($dbname);
	}

	function kds_GetPathReport($KodeOrg, $Module, $NamaFile){
		$ssql="SELECT * from report_path WHERE kodeorg='".$KodeOrg."';";
		$cek=$this->kds_sSQLnum($ssql);
		if($cek == 0){
			$addSQL="INSERT INTO `report_path` (`kodeorg`, `lokasi`) VALUES ('".$KodeOrg."', 'report/')";
			$this->kds_lock('report_path');
			$hasil=$this->kds_exc($addSQL);
			$this->kds_unlock();
		}

		$ssql="SELECT a.*,b.* from report_path_detail a LEFT JOIN report_path b ON a.kodeorg = b.kodeorg WHERE a.kodeorg='".$KodeOrg."' and a.module = '".$Module."';";
		$cek=$this->kds_sSQLnum($ssql);
		if($cek == 0){
			$addSQL="INSERT INTO `report_path_detail` (`kodeorg`, `module`, `namafile`) VALUES ('".$KodeOrg."', '".$Module."', '".$NamaFile."')";
			$this->kds_lock('report_path_detail');
			$hasil=$this->kds_exc($addSQL);
			$this->kds_unlock();

			$ssql="SELECT * from report_path_detail WHERE kodeorg='".$KodeOrg."' and module = '".$Module."';";
			$return=$this->kds_sSQL($ssql);
			return $return;
		} else {
			$return=$this->kds_sSQL($ssql);
			return $return;
		}

	}

	function kds_myUrldecode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D','%0A');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]","\n");
		return str_replace($entities, $replacements, urlencode($string));
	}
	function kds_myUrlcode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D','%0A');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]","\n");
		return str_replace( $replacements,$entities, urlencode($string));
	}
	function kds_replaceEnter($string){
		return htmlspecialchars(htmlentities(str_replace("\n","%0A",$string)));
	}

	function kds_codeSC($text){
		return htmlspecialchars(htmlentities(str_replace("\n"," ",$text)));
	}
	function kds_decodeSC($text){
		$text=str_replace("&amp;","&",$text);
		return htmlspecialchars_decode(htmlentities($text));
	}

	Public function kds_cekfield($table,$namafield){
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."';";
		$cek=$this->kds_sSQLnum($ssql);
		return $cek;
	}
	function kds_cekfieldtype($table,$namafield,$typedata){
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."' AND type='".trim($typedata)."';";
		$cek=$this->kds_sSQLnum($ssql);
		return $cek;
	}
	function kds_addField($table,$namafield,$typedata,$type){
		$ssql="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."';";
		$cek=$this->kds_sSQLnum($ssql);
		$ssql1="SHOW COLUMNS FROM ".$table." WHERE field='".$namafield."' AND type='".trim($typedata)."';";
		$cek1=$this->kds_sSQLnum($ssql1);
		$rs=$this->kds_sSQL($ssql);
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
		$this->kds_lock($table);
		$hasil=$this->kds_exc($addSQL);
		$this->kds_unlock();
		return $hasil;
	}

	function kds_datediff($tgl1, $tgl2){
		$tgl1 = strtotime($tgl1);
		$tgl2 = strtotime($tgl2);
		$diff_secs = abs($tgl1 - $tgl2);
		$base_year = min(date("Y", $tgl1), date("Y", $tgl2));
		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
		return array( "years" => date("Y", $diff) - $base_year, "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff), "months" => date("n", $diff) - 1, "days_total" => floor($diff_secs / (3600 * 24)), "days" => date("j", $diff) - 1, "hours_total" => floor($diff_secs / 3600), "hours" => date("G", $diff), "minutes_total" => floor($diff_secs / 60), "minutes" => (int) date("i", $diff), "seconds_total" => $diff_secs, "seconds" => (int) date("s", $diff) );
	}

	function kds_ymd($tgl){
		return date('Y-m-d', strtotime($tgl));
	}

	function kds_dmy($tgl) {
		return date('d-m-Y', strtotime($tgl));
	}

	function kds_hapus($table,$where){
		if(trim($where)!=""){
			$this->kds_exc('LOCK TABLE '.$table.' WRITE;');
			$string="DELETE FROM ".$table." ".$where;
			$m=$this->kds_exc($string);
			$this->kds_exc('UNLOCK TABLES;');

			return $m;
		}
	}
	function kds_lock($table){
		$this->kds_exc('LOCK TABLE '.$table.' WRITE;');
	}
	function kds_unlock(){
		$this->kds_exc('UNLOCK TABLES;');
	}

	function kds_sSQL($Query){
		require_once('config/connection.php');
		$hasil=mysql_query($Query);
		$data=array();
		if(! $hasil )
		{
		  die('Could not get data: ' . mysql_error());
		}
		while($row = mysql_fetch_array($hasil))
		{
			array_push($data,$row);
		}
		return $data;
		mysql_close(@$conn);
	}

	function kds_sSQLnum($Query){
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

	function kds_getJSON($Query){
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

	function kds_exc($query){
		include('config/connection.php');
		$hasil=	mysql_query($query);
		return $hasil;
		mysql_close(@$conn);
	}

	function kds_genSQL($table,$arraySQL,$update){
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
$kodesalib=new kodesalib;
?>
