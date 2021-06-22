<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$empty = $_GET['empty'];
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
if ($data['tanggaltransaksi']=='') {$data['tanggaltransaksi']=null;} 
else {
	$data['tanggaltransaksi']=tanggalsystem($data['tanggaltransaksi']);
}
if ($data['tanggalpengakuan']=='') {$data['tanggalpengakuan']=null;} 
else {
	$data['tanggalpengakuan']=tanggalsystem($data['tanggalpengakuan']);
}
if ($data['basiskg']=='') {$data['basiskg']='0';}
if ($data['lc']=='') {$data['lc']='0';}
// $data['tahuntanam']='4000';
$data['periodetm']='';
$data['catatanlain']='';
$kodeorg=$data['kodeorg'];
$tahuntanam=$data['tahuntanam'];
unset($data['tahuntanamCurr']);
unset($data['tableName']);
unset($data['opt']);
unset($data['numRow']);
unset($data['idField']);
unset($data['idVal']);
unset($data['freeze']);
$columnValues=[];
foreach ($data as $key=>$val){
	$columnValues[]=array($key=>$val);
}

$result=array('success' => true, 'message' => '', 'data' => array());
$row = getRows("select * from setup_blok where kodeorg='".$data['kodeorg']."' and tahuntanam=".$data['tahuntanam']);
// $row2 = "select * from setup_blok where kodeorg='".$data['kodeorg']."' and tahuntanam=".$data['tahuntanam'];
// echo $row2;
if (count($row)==0){
	$result = dbTransaction(function() {
		global $columnValues;
		$echo = array('success' => true, 'message' => '', 'data' => array());
		if (insertRow('setup_blok', $columnValues)) {
			$echo['sql_insert'] = insertRow('setup_blok', $columnValues, true);
		} else {
			$echo['success'] = false;
			$echo['message'] = "DB Error : \r\n" .
                        "Your query : " . insertRow('setup_blok', $columnValues, true) . "\r\n\r\n" .
                        "Error Message :" . mysql_error();
                    }
        return $echo;
    });
} else {
	$result = dbTransaction(function() {
		global $columnValues;
		global $kodeorg;
		global $tahuntanam;
        $echo = array('success' => true, 'message' => '', 'data' => array());
        // $echo['post']=$columnValues;
		$echo['sql_update'] = updateRow('setup_blok', $columnValues, " kodeorg='$kodeorg' and tahuntanam='$tahuntanam'",true);
        if (updateRow('setup_blok', $columnValues, " kodeorg='$kodeorg' and tahuntanam='$tahuntanam'")) {
        } else {
        	$echo['success'] = false;
            $echo['message'] = "DB Error : \r\n" .
                        "Your query : " . $echo['sql_update'] . "\r\n\r\n" .
                        "Error Message :" . mysql_error();
        }
        return $echo;
    });
} 
$result['post']=$data;

echo json_encode($result);


// if ($empty == false) {
// 	foreach ($data as $dt => $isi) {
// 		if ($isi == '') {
// 			echo 'warning:Please Insert The Form';
// 			exit();
// 		}
// 	}
// }

// $sCek = "select * from $dbname.$tableName where kodeorg='" . $data['kodeorg'] . "'";
// //exit(mysql_error($sCek));
// ($qCek = mysql_query($sCek)) || true;
// $rCek = mysql_num_rows($qCek);

// if (0 < $rCek) {
// 	exit('Error:Data Untuk ' . $optNmOrg[$data['kodeorg']] . ' Sudah Ada');
// }

// $query = 'insert into ' . $dbname . '.' . $tableName . '(';
// $i = 0;

// foreach ($data as $key => $row) {
// 	if ($i == 0) {
// 		$query .= '' . $key . '';
// 	}
// 	else {
// 		$query .= ',' . $key . '';
// 	}

// 	++$i;
// }

// $query .= ') values (';
// $i = 0;

// foreach ($data as $row) {
// 	$tmpStr = explode('-', $row);

// 	if (count($tmpStr) == 3) {
// 		$row = tanggalsystem($row);
// 	}

// 	$int = (int) $row;

// 	if ($i == 0) {
// 		if (((string) $int == $row) && (strlen((string) $int) == strlen($row))) {
// 			$query .= $row;
// 		}
// 		else if (is_string($row)) {
// 			$query .= '\'' . $row . '\'';
// 		}
// 		else {
// 			$query .= $row;
// 		}
// 	}
// 	else if (((string) $int == $row) && (strlen((string) $int) == strlen($row))) {
// 		$query .= ',' . $row;
// 	}
// 	else if (is_string($row)) {
// 		$query .= ',\'' . $row . '\'';
// 	}
// 	else {
// 		$query .= ',' . $row;
// 	}

// 	++$i;
// }

// $query .= ');'; 
// try {
// 	if (!mysql_query($query)) {
// 		echo 'DB Error : ' . mysql_error($conn);
// 		exit();
// 	}

// 	echo '<tr id=\'tr_' . $numRow . '\' class=\'rowcontent\'>';
// 	$tmpField = '';
// 	$tmpVal = '';

// 	foreach ($data as $key => $row) {
// 		echo '<td id=\'' . $key . '_' . $numRow . '\' value=\'' . $row . '\'>' . $row . '</td>';
// 		$tmpField .= '##' . $key;
// 		$tmpVal .= '##' . $row;
// 	}

// 	if (isset($_POST['freeze'])) {
// 		echo '<td><img id=\'editRow' . $numRow . '\' title=\'Edit\' onclick="editRow(' . $numRow . ',\'' . $tmpField . '\',\'' . $tmpVal . '\',\'' . $_POST['freeze'] . '\')"' . "\r\n\t" . '    class=\'zImgBtn\' src=\'images/001_45.png\' /></td>';
// 	}
// 	else {
// 		echo '<td><img id=\'editRow' . $numRow . '\' title=\'Edit\' onclick="editRow(' . $numRow . ',\'' . $tmpField . '\',\'' . $tmpVal . '\')"' . "\r\n\t" . '    class=\'zImgBtn\' src=\'images/001_45.png\' /></td>';
// 	}

// 	echo '<td><img id=\'delRow' . $numRow . '\' title=\'Hapus\' onclick="delRow(' . $numRow . ',\'' . $idField . '\',\'' . $idVal . '\',null,\'' . $tableName . '\')"' . "\r\n\t" . 'class=\'zImgBtn\' src=\'images/delete_32.png\' /></td>';
// 	echo '</tr>';
// }
// catch (Exception $e) {
// 	echo 'ERROR Query';
// 	echo $e->getMessage();
// }

?>
