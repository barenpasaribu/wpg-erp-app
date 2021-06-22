<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
#showerror();
#showerror();pre($_POST);exit();
$unit		= isset($_POST['unit']) ? $_POST['unit'] : '';
$gudang		= isset($_POST['gudang']) ? $_POST['gudang'] : '';
$periode		= isset($_POST['periode']) ? $_POST['periode'] : '';
$reffno			= isset($_POST['reffno']) ? $_POST['reffno'] : '';
$method			= isset($_POST['method']) ? $_POST['method'] : '';

function GetListDataHeader($unit,$gudang,$periode){
	global $dbname;
	
	$String = "SELECT *	FROM ".$dbname.".log_5stokopnameht 
	WHERE kdunit = '".$unit."'
	AND kdgudang = '".$gudang."'
	AND periode like '%".$periode."%'"
	;

	$Result = fetchData($String);
	return $Result;
}
function GetListDataDetail($reffno){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".log_5stokopnamedt WHERE reffno = '".$reffno."' order by seqno";

	$Result = fetchData($String);
	return $Result;
}
function GetStatusHeaderOpt($Status){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".setup_5parameter WHERE flag = 'status_st' order by nourut";

	$Result = fetchData($String);
	#pre($Result);
	$OptStatus = '';
	foreach($Result as $VOSKey => $VOSVal){
		if($Status == $VOSVal['kode']) {
			$OptStatus.= "<option value='".$VOSVal['kode']."' selected>".$VOSVal['nama']."</option>";
		} else {
			$OptStatus.= "<option value='".$VOSVal['kode']."'>".$VOSVal['nama']."</option>";
		}
	}
	return $OptStatus;
}

switch($method){
	case'CariListDataHeader':
		$ListDataHeader = GetListDataHeader($unit,$gudang,$periode);
		#pre($ListDataHeader);
		$dat ="";
		$no = 1;
		foreach($ListDataHeader as $LDHKey => $LDHVal){
			$dat.= "<tr class='rowcontent'>";
			$dat.= "<td onclick=\"CariDetailData('".$LDHVal['reffno']."');\">".$no."</td>";
			$dat.= "<td onclick=\"CariDetailData('".$LDHVal['reffno']."');\">".tanggalnormal($LDHVal['tanggal'])."</td>";
			$dat.= "<td onclick=\"CariDetailData('".$LDHVal['reffno']."');\">".$LDHVal['reffno']."</td>";
			$dat.= "<td onclick=\"CariDetailData('".$LDHVal['reffno']."');\">".$LDHVal['nostokopname']."</td>";
			$dat.= "<td>".$LDHVal['kdunit']."</td>";
			$dat.= "<td>".$LDHVal['kdgudang']."</td>";
			$dat.= "<td>".$LDHVal['note']."</td>";
			if($LDHVal['status'] == 3 || $LDHVal['status'] == 1) {
				$Disabled = 'Disabled';
			} else {
				$Disabled = '';
			}
			$dat.= "<td><select id='status_".$LDHVal['id']."' name='status_".$LDHVal['id']."' onchange=\"UpdateStatus('".$LDHVal['id']."');\" ".$Disabled.">".GetStatusHeaderOpt($LDHVal['status'])."</select></td>";
			$dat.= "</tr>";
			$no++;
		}

		echo $dat;
	break;
	case'CariListDataDetail':
		$ListDataDetail = GetListDataDetail($reffno);
		#pre($ListDataHeader);
		$dat ="";
		$no = 1;
		foreach($ListDataDetail as $LDDKey => $LDDVal){
			$dat.= "<tr class='rowcontent'>";
			$dat.= "<td>".$no."</td>";
			$dat.= "<td>No Referensi</td>";
			$dat.= "<td>".$LDDVal['kdbarang']."</td>";
			$dat.= "<td>".$LDDVal['nmbarang']."</td>";
			$dat.= "<td>".$LDDVal['kdsatuan']."</td>";
			$dat.= "<td>".$LDDVal['qtysaldo']."</td>";
			$dat.= "<td>".$LDDVal['qtyso']."</td>";
			$dat.= "<td>".$LDDVal['qtybalance']."</td>";
			$dat.= "</tr>";
			$no++;
		}

		echo $dat;
	break;	
	default:
	break;
}
?>