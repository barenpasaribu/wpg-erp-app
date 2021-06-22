<?php
include('config/connection.php');


/*
 * Function selectQuery
 * Fungsi untuk generate MySQL Select Query Standard
 * I : nama DB, nama table, kolom - kolom, kondisi
 * O : query dalam format string
 * U : $query = selectQuery(str,str,arr/str,str);
 */
 function selectQuery($dbname,$table,$column="*",$where="",$sort="",$distinct=false,$rowPerPage=null,$page=null) {
	# Select
	$query = "select ";
	
	# Distinct
	if($distinct==true) {
	      $query .= "distinct ";
	}
	
	# Column List
	if(is_array($column)) {
		for($i=0;$i<count($column);$i++) {
			$query .= $column[$i];
			if($i<>count($column)-1) {
				$query .= ",";
			}
		}
		$query .= " ";
	} else {
		$query .= $column." ";
	}
	
	# From Table
	$query .= "from `".$dbname."`.`".$table."`";
	
	# Where Condition
	if($where!="") {
		$query .= " where ".$where;
	}
	
	# Sort By
	if($sort!="") {
	      $query .= " order by ".$sort;
	}
	
	# Limit
	if(!is_null($rowPerPage)) {
	      if(!is_null($page)) {
		     $startFrom = ($page-1) * $rowPerPage;
	      } else {
		     $startFrom = 0;
	      }
	      $query .= " limit ".$startFrom.",".$rowPerPage;
	}
	
	return $query;
 }
 
/*
 * Function insertQuery
 * Fungsi untuk generate MySQL Insert Query Standard
 * I : nama DB, nama table, data
 * O : query dalam format string
 * U : $query = selectQuery(str,str,arr);
 */
 function insertQuery($dbname,$table,$data=array(),$column=array()) {
	# Insert
	if($column==array()) {
	      $query = "insert into `".$dbname."`.`".$table."` values (";
	} else {
	      $query = "insert into `".$dbname."`.`".$table."` (";
	      for($i=0;$i<count($column);$i++) {
		     if($i==0) {
			    $query .= "`".$column[$i]."`";
		     } else {
			    $query .= ",`".$column[$i]."`";
		     }
	      }
	      $query .= ") values (";
	}
	
	# Data
	if(is_array($data)) {
	      $i=0;
	      foreach($data as $row) {
		     if(is_string($row)) {
			    $query .= "'".$row."'";
		     } else {
			    $query .= $row;
		     }
		     if($i<>count($data)-1) {
			    $query .= ",";
		     }
		     $i++;
	      }
	} else {
	      return false;
	}
	
	# Close
	$query .= ")";
		//echo "Warning :".$query;
       return $query;
 }
 
/*
 * Function updateQuery
 * Fungsi untuk generate MySQL Update Query Standard
 * I : nama DB, nama table, data, kondisi
 * O : query dalam format string
 * U : $query = selectQuery(str,str,arr/str);
 */
 function updateQuery($dbname,$table,$data=array(),$where="") {
	# Update
	$query = "update ".$dbname.".".$table." set ";
	
	# Data
	if(is_array($data)) {
		$i=0;
		foreach($data as $key=>$row) {
		     if(is_string($row)) {
			    $query .= "`".$key."`='".$row."'";
		     } else {
			    $query .= "`".$key."`=".$row;
		     }
		     if($i<>count($data)-1) {
			     $query .= ",";
		     }
		     $i++;
		}
		$query .= " ";
	} else {
		return false;
	}
	
	# Where Condition
	if($where!="") {
		$query .= " where ".$where;
	}
	
	return $query;
 }
 
 /*
 * Function deleteQuery
 * Fungsi untuk generate MySQL Delete Query Standard
 * I : nama DB, nama table, kondisi
 * O : query dalam format string
 * U : $query = selectQuery(str,str,arr/str);
 */
 function deleteQuery($dbname,$table,$where="") {
       $query = "delete from `".$dbname."`.`".$table."` where ".$where;
       return $query;
 }

/*
 * Function fetchData
 * Fungsi untuk fetch data dari DB ke bentuk array
 * I : 
 * O : array result 2 dimensi
 * U : $data = fetchData();
 */
 function fetchData($query=null) {
       # Init
       $result = array();
       
       # Arrange to Array
       if($query==null) {
	      echo "Error";
       } else {
	      $res=mysql_query($query);
	      if(!$res) {
		     echo " Your query :<br> $query <br>".
                 "DB Error : :<br>".mysql_error();
		     exit;
	      }
	      while($bar=mysql_fetch_assoc($res)) {
		     $result[] = $bar;
	      }
       }
       
       return $result;
 }
 
 /* Function getPrimary
  * Fungsi untuk mendapatkan list primary key dari suatu table
  */
 function getPrimary($dbname,$table) {
       $query = "select * from ".$dbname.".".$table;
       $res=mysql_query($query);
       $j = mysql_num_fields($res);
       $i = 0;
       $primary = array();
       
       while ($i < $j) {
	      $meta = mysql_fetch_field($res, $i);
	      if($meta->primary_key=='1') {
		     $primary[] = strtolower($meta->name);
	      }
	      $i++;
       }
       
       return $primary;
 }
 
 /* Function getEnum
  * Fungsi untuk mendapatkan daftar nilai yang diijinkan untuk field dengan enum
  */
 function getEnum($dbname,$table,$field) {
       $query = " SHOW COLUMNS FROM `".$dbname."`.`".$table."` LIKE '".$field."' ";
       $result = mysql_query( $query ) or die( 'error getting enum field ' . mysql_error() );
       $row = mysql_fetch_array( $result , MYSQL_NUM );
       #extract the values
       #the values are enclosed in single quotes
       #and separated by commas
       $regex = "/'(.*?)'/";
       preg_match_all( $regex , $row[1], $enum_array );
       $enum_fields = array();
       foreach($enum_array[1] as $row) {
	      $enum_fields[$row] = $row;
       }
       return( $enum_fields );
 }
 
 /* Function getHolding
  * Fungsi untuk holding dari kode organisasi tertentu
  * I : db, kode organisasi
  * O : array(kode holding=>nama holding)
  */
 function getHolding($dbname,$org,$opt=false) {
       $tipe = null;
       $tmpOrg = $org;
       if(trim($tmpOrg)!='') {
	      while(!($tipe=='HOLDING')) {
		     $query = selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi,tipe,induk',"kodeorganisasi='".$tmpOrg."'");
		     $data = fetchData($query);
		     $tipe = $data[0]['tipe'];
		     $tmpOrg = $data[0]['induk'];
	      }
       }
       
       if($tipe=="HOLDING") {
	      if($opt==true) {
		     $resArr = array(
			    $data[0]['kodeorganisasi']=>$data[0]['namaorganisasi']
		     );
	      } else {
		     $resArr = array(
			    'kode'=>$data[0]['kodeorganisasi'],
			    'nama'=>$data[0]['namaorganisasi']
		     );
	      }
	      return $resArr;
       } else {
	      return false;
       }
 }
 
 /* Function getHolding
  * Fungsi untuk holding dari kode organisasi tertentu
  * I : db, kode organisasi
  * O : array(kode holding=>nama holding)
  */
 function getPT($dbname,$org,$opt=false) {
       $tipe = null;
       $tmpOrg = $org;
       if(trim($tmpOrg)!='') {
	      while(!($tipe=='PT') and $tmpOrg!='') {
		     $query = selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi,tipe,induk',"kodeorganisasi='".$tmpOrg."'");
		     $data = fetchData($query);
		     $tipe = $data[0]['tipe'];
		     $tmpOrg = $data[0]['induk'];
	      }
       }
       
       if($tipe=="PT") {
	      if($opt==true) {
		     $resArr = array(
			    $data[0]['kodeorganisasi']=>$data[0]['namaorganisasi']
		     );
	      } else {
		     $resArr = array(
			    'kode'=>$data[0]['kodeorganisasi'],
			    'nama'=>$data[0]['namaorganisasi']
		     );
	      }
	      return $resArr;
       } else {
	      return false;
       }
 }
 
 /* Function getOrgBelow
  * Fungsi untuk extract organisasi dibawahnya
  * I : db, kode organisasi(lokasi tugas),bool kodeorg sendiri ditampilkan
  * O : array of org
  */
 function getOrgBelow($dbname,$org,$self=true,$mode='all',$empty=false) {
       $contOrg = array();
       $data = 'x';
       $tmpOrg = array($org);
       while(!empty($tmpOrg)) {
	      foreach($tmpOrg as $key=>$tOrg) {
		     unset($tmpOrg[$key]);
		     $cols = 'kodeorganisasi,namaorganisasi,tipe';
		     $query = selectQuery($dbname,'organisasi',$cols,
			    "induk='".$tOrg."'");
		     $data = fetchData($query);
		     foreach($data as $row) {
			    $contOrg[$row['tipe']][$row['kodeorganisasi']] = $row['namaorganisasi'];
			    $tmpOrg[] = $row['kodeorganisasi'];
		     }
	      }
       }
       
       if($empty==true) {
	      $resOrg = array(''=>'');
       } else {
	      $resOrg = array();
       }
       
       if($self==true) {
	      $query = selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$org."'");
	      $data = fetchData($query);
	      $resOrg[$data[0]['kodeorganisasi']] = $data[0]['namaorganisasi'];
       }
       foreach($contOrg as $tipe=>$row1) {
	      foreach($row1 as $key=>$row2) {
		     if($mode=='kebun') {
			    if($tipe=='KEBUN' or $tipe=='AFDELING' or $tipe=='BLOK') {
				   $resOrg[$key] = $row2;
			    }
		     } elseif($mode=='kebunndivisi') {
			    if($tipe=='KEBUN' or $tipe=='DIVISI') {
				   $resOrg[$key] = $row2;
			    }
		     } elseif($mode=='kebunonly') {
			    if($tipe=='KEBUN') {
				   $resOrg[$key] = $row2;
			    }
		     } elseif($mode=='afdeling') {
			    if($tipe=='AFDELING') {
				   $resOrg[$key] = $row2;
			    }
		     } elseif($mode=='blok') {
			    if($tipe=='BLOK') {
				   $resOrg[$key] = $row2;
			    }
		     } elseif($mode=='noblok') {
			    if($tipe!='BLOK') {
				   $resOrg[$key] = $row2;
			    }
		     } else {
			    $resOrg[$key] = $row2;
		     }
	      }
       }
       
       return $resOrg;
 }
 
 /* Function getName
  * Fungsi untuk mendapatkan field tertentu dari kode spesifik
  */
 function getAttr($tableName,$codeField,$attrField,$codeVal,$tipe='str') {
       global $dbname;
       if($tipe=='str') {
	      $where = $codeField."='".$codeVal."'";
       } else {
	      $where = $codeField."=".$codeVal;
       }
       $query = selectQuery($dbname,$tableName,$attrField,$where);
       $sel = fetchData($query);
       return $sel[0][$attrField];
 }
 
 /* Function getTotalRow
  * Fungsi untuk mengetahui total data untuk kondisi tertentu
  * I : db, tabel, kondisi
  * O : total baris
  */
 function getTotalRow($dbname,$table,$where=null) {
       $query = "select count(*) as total from ";
       $query .= "`".$dbname."`.`".$table."`";
       if(!is_null($where)) {
	      $query .=" where ".$where;
       }
       $res = fetchData($query);
       return $res[0]['total'];
 }
?>