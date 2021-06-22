<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$param = $_POST;
$proses = $_GET['proses'];
switch ($proses) {
    case 'delete':
        $where = "kodeorg='".$param['kodeorg']."' and kodetangki='".$param['kodetangki']."'";
        $query = 'delete from `'.$dbname.'`.`pabrik_5tangki` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
		} else {
			echo 'data berhasil dihapus';	
		}		
    break;
	case 'add':	
		$qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5tangki where kodeorg="'.$param['kodeorg'].'" AND kodetangki="'.$param['kodetangki'].'"');
		$cek   = mysql_fetch_assoc($qcek);		
		$query = "INSERT INTO ".$dbname.".pabrik_5tangki (kodeorg,kodetangki,keterangan,komoditi,luaspenampang,satuanpenampang,volumekerucut,satuankerucut,stsuhu)VALUES ('".$param['kodeorg']."','".$param['kodetangki']."','".$param['keterangan']."','".$param['komoditi']."','".$param['luaspenampang']."','".$param['satuanpenampang']."','".$param['volumekerucut']."','".$param['satuankerucut']."',".$param['stsuhu'].")";		
		if($cek['total'] != 0) {
			echo "data sudah ada";
		}	
		else if(mysql_query($query)) {
			echo "tambah data berhasil";
		}	
		else {
            echo 'DB Error : '.mysql_error();
            exit();			
		}		
	break;
	case 'edit':
		$query = "UPDATE ".$dbname.".pabrik_5tangki SET 
		kodeorg='".$param['kodeorg']."', 
		kodetangki='".$param['kodetangki']."', 
		keterangan='".$param['keterangan']."', 
		komoditi='".$param['komoditi']."', 
		luaspenampang='".$param['luaspenampang']."', 
		satuanpenampang='".$param['satuanpenampang']."', 
		volumekerucut='".$param['volumekerucut']."', 
		satuankerucut='".$param['satuankerucut']."', 
		stsuhu=".$param['stsuhu']."		
		WHERE kodeorg='".$param['kodeorg']."' AND kodetangki='".$param['kodetangki']."'";
		if(mysql_query($query)) {
			echo "edit data berhasil";
		}	
		else {
            echo 'DB Error : '.mysql_error();
            exit();			
		}		
	break;
    default:
        break;
}
?>