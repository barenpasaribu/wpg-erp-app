<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_2pembayaran.js"></script>' . "\r\n";

if ($_SESSION['language'] == 'EN') {
	$zz = 'kelompok1';
}
else {
	$zz = 'kelompok';
}

$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,' . $zz);
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optSupplr = $optOrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sPeriodeCari = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_po_vw where statuspo=3 order by substr(tanggal,1,7) desc';

#exit(mysql_error());
($qPeriodeCari = mysql_query($sPeriodeCari)) || true;

while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
	$optPeriode .= '<option value=\'' . $rPeriodeCari['periode'] . '\'>' . $rPeriodeCari['periode'] . '</option>';
}

$optSupp = $optNopo = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optjenis = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$stataPP = array('Contract', 'PO');

foreach ($stataPP as $dataIni => $listNama) {
	$optjenis .= '<option value=\'' . $dataIni . '\'>' . $listNama . '</option>';
}

//$sOrg = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi asc';

//#exit(mysql_error($conn));
//($qOrg = mysql_query($sOrg)) || true;
//
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//	$optOrg .= '<option value=\'' . $rOrg['kodeorganisasi'] . '\'>' . $rOrg['namaorganisasi'] . '</option>';
//}

$optOrg=makeOption2(getQuery("pt"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$optPO=makeOption2(getQuery("po"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'nopo',"captionfield"=> 'namasupplier' ),
	function ($option,$value,$caption){
		$ret = array("newvalue"=>"","newcaption"=>"");
		if($option=='init'){
			$ret["newvalue"]=$value;
			$ret["newcaption"]=$caption;
		}
		if($option=='noninit'){
			$ret["newvalue"]=$value;
			$ret["newcaption"]= $value ." - ".$caption;
		}
		return $ret;
	}
);

//$sOrg = 'select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as tipe from ' . $dbname . '.log_5supplier where namasupplier!=\'\' order by namasupplier asc';
//
//#exit(mysql_error($conn));
//($qOrg = mysql_query($sOrg)) || true;
//
//while ($rOrg = mysql_fetch_assoc($qOrg)) {
//	$optSupplr .= '<option value=\'' . $rOrg['supplierid'] . '\'>' . $rOrg['tipe'] . '-' . $rOrg['namasupplier'] . '</option>';
//}

$optSupplr=makeOption2(getQuery("supplier"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'supplierid',"captionfield"=> 'namasupplier' ));
//	function ($option,$value,$caption){
//		$ret = array("newvalue"=>"","newcaption"=>"");
//		if($option=='init'){
//			$ret["newvalue"]=$value;
//			$ret["newcaption"]=$caption;
//		}
//		if($option=='noninit'){
//			$ret["newvalue"]=$value;
//			$ret["newcaption"]= ;
//		}
//		return $ret;
//	}
//);


$arr = '##lstPo##kdUnit##periode##jenisId##suppId##periode2';
$arr2 = '##tgl_cari##tgl_cari2##jenisId2##kdUnit2##cariNopo##suppId2';
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<!--<fieldset style="float: left;">' . "\r\n" . '<legend><b>Payment History</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['dari'];
echo ' ';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px" onchange=getPt()>';
echo $optPeriode;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['sampai'];
echo ' ';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode2" name="periode2" style="width:150px" >';
echo $optPeriode;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['jenis'];
echo '</label></td><td><select id="jenisId" name="jenisId" style="width:150px" onchange=getNopo()>';
echo $optjenis;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['pt'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px" onchange=getAll()>';
echo $optNopo;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['nopo'];
echo '/ No Kontrak</label></td><td><select id="lstPo" name="lstPo" style="width:150px">';
echo $optNopo;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['supplier'];
echo '</label></td><td><select id="suppId" name="suppId" style="width:150px">';
echo $optSupp;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2pembayaran\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'log_slave_2pembayaran.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>-->' . "\r\n\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>Riwayat Pembayaran</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['dar'];
echo ' ';
// echo $_SESSION['lang']['tanggal'];
echo "Tanggal Mulai";
echo '</label></td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['sampai'];
echo ' ';
echo $_SESSION['lang']['tanggal'];
echo '</label></td><td><input type=text class=myinputtext id=tgl_cari2 onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['jenis'];
echo '</label></td><td><select id="jenisId2" name="jenisId2" style="width:150px">';
echo $optjenis;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['pt'];
echo '</label></td><td><select id="kdUnit2" name="kdUnit2" style="width:150px" >';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['nopo'];
//echo '/ No Kontrak</label></td><td><input type=text id="cariNopo" class="myinputtext" style="width:150px;" /></td></tr>' . "\r\n" . '<tr><td><label>';
echo '/ No Kontrak</label></td><td><select id="cariNopo" name="cariNopo" style="width:150px">  '.
	$optPO.'</select>'.createDialogBox('containerPO','nmPO','Cari PO / No Kontrak',"searchPO","findPO").
	'</td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['supplier'];
echo '</label></td><td><select id="suppId2" name="suppId2" style="width:150px">';
echo $optSupplr;
echo '</select>' . "\r\n";
//echo '<img src=images/search.png class=resicon title=\'' . $_SESSION['lang']['findRkn'] . '\' onclick="searchSupplier(\'' . $_SESSION['lang']['findRkn'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);">';
//echo '<img src=images/search.png class=resicon title=\'' . $_SESSION['lang']['findRkn'] . '\' '.
//'onclick="searchSupplier(\''.$_SESSION['lang']['findRkn'] . '\', '. '\'<fieldset>'.
//'<legend>'. $_SESSION['lang']['find'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp; '.
//'<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>' .
//$_SESSION['lang']['find'] . '</button></fieldset>'.
//'<div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);>';
echo createDialogBox('containerSupplier','nmSupplier',$_SESSION['lang']['findRkn'],"searchSupplier","findSupplier");


echo '</td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'log_slave_2pembayaran2\',\'';
echo $arr2;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'log_slave_2pembayaran2.php\',\'';
echo $arr2;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
