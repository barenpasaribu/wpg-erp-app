<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['riwayatPP']) . '</b>');
echo '<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>' . "\r\n" . '-->' . "\r\n" . '<script type="text/javascript" src="js/log_2riwayatPP.js" /></script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
$arrPil = array(1 => $_SESSION['lang']['proses'] . ' ' . $_SESSION['lang']['persetujuan'] . ' ' . $_SESSION['lang']['prmntaanPembelian'], 2 => $_SESSION['lang']['proses'] . ' ' . $_SESSION['lang']['purchasing'], 3 => $_SESSION['lang']['jmlh_brg_sdh_po'], 4 => $_SESSION['lang']['jmlh_brg_blm_po']);

foreach ($arrPil as $id => $isi) {
	$optPil .= '<option value=' . $id . '>' . $isi . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arrPo = array('Pusat', 'Lokal');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$optper = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sTgl = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_prapoht order by tanggal desc';

#exit(mysql_error());
($qTgl = mysql_query($sTgl)) || true;

while ($rTgl = mysql_fetch_assoc($qTgl)) {
	if (substr($rTgl['periode'], 5, 2) == '12') {
		$optper .= '<option value=\'' . substr($rTgl['periode'], 0, 4) . '\'>' . substr($rTgl['periode'], 0, 4) . '</option>';
	}

	$optper .= '<option value=\'' . $rTgl['periode'] . '\'>' . substr($rTgl['periode'], 5, 2) . '-' . substr($rTgl['periode'], 0, 4) . '</option>';
}

$optSupplier = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier  where kodekelompok=\'S001\' and namasupplier!=\'\' order by namasupplier asc';

#exit(mysql_error());
($query = mysql_query($sql)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
}

//$sPurchaser = 'select distinct purchaser from ' . $dbname . '.log_prapodt order by purchaser asc';
//
//#exit(mysql_error($conn));
//($qPurchaser = mysql_query($sPurchaser)) || true;
//
//while ($rPur = mysql_fetch_assoc($qPurchaser)) {
//	$crpur = 'karyawanid=\'' . $rPur['purchaser'] . '\'';
//	$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $crpur);
//	$optPurchaser .= '<option value=\'' . $rPur['purchaser'] . '\'>' . $optNmKar[$rPur['purchaser']] . '</option>';
//}
$str = "SELECT * ".
"FROM datakaryawan ".
"WHERE karyawanid IN ( ".
"SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= ".
"( ".
"SELECT kodeorganisasi FROM datakaryawan d ".
"INNER JOIN user u ON u.karyawanid=d.karyawanid ".
"WHERE u.namauser='" .$_SESSION['standard']['username'] ."'".
")";
$optPurchaser= makeOption2($str,
	array( ),
	array("valuefield"=>'karyawanid',"captionfield"=> 'namakaryawan' )
);
$optPO=makeOption2(getQuery("pp"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'nopp',"captionfield"=> 'nopo' ),
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
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n" . '          <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '                 <td><fieldset><legend>' . $_SESSION['lang']['pilihdata'] . '</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0>' . "\r\n" . '                          <tr><td>' . $_SESSION['lang']['nopp'] . '</td><td>:</td>'.
//'<td><input type=\'text\' id=\'txtNopp\' name=\'txtNopp\' onkeypress=\'return tanpa_kutip(event)\' style=\'width:150px\' class=myinputtext /></td>';
'<td><select id="txtNopp" name="txtNopp" style="width:150px">  '.
	$optPO.'</select>'.createDialogBox('containerPO','nmPO','Cari PO',"searchPO","findPO").
	'</td>';

echo '<td>' . $_SESSION['lang']['tanggal'] . ' PP </td><td>:</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td>';
echo '</td><td>' . $_SESSION['lang']['periode'] . '</td><td>:</td><td><select id=periode name=periode style=\'width:150px;\'>' . $optper . '</select></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['lokasiBeli'] . '</td><td>:</td><td><select id=lokBeli name=lokBeli style=\'width:150px;\'>' . $optLokal . '</select></td>';
echo '<td>' . $_SESSION['lang']['status'] . ' PP</td><td>:</td><td><select id=statPP name=statPP style=\'width:150px;\'><option value=\'\'>' . $_SESSION['lang']['all'] . '</option>' . $optPil . '</select></td>';
echo '<td>' . $_SESSION['lang']['namasupplier'] . '</td><td>:</td><td><select id="supplier_id" name="supplier_id"  style="width:150px;" >' . "\r\n" . '                        ' . $optSupplier . '</select><img src="images/search.png" class="resicon" title=\'' . $_SESSION['lang']['findRkn'] . '\' onclick="searchSupplier(\'' . $_SESSION['lang']['findRkn'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);"></td></tr>';
echo '<tr><td>' . $_SESSION['lang']['namabarang'] . '</td><td>:</td><td><input type=\'text\' id=\'txtNmBrg\' name=\'txtNmBrg\' onkeypress=\'return tanpa_kutip(event)\' style=\'width:150px\' class=myinputtext /></td>' . "\r\n" . '                         <td>' . $_SESSION['lang']['purchaser'] . '</td><td>:</td><td><select id=purchaserId style=150px>' . $optPurchaser . '</td>' . '</tr></table>';
echo '<button class=mybutton onclick=savePil()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n" . '         </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "\r\n" . '    <fieldset>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['list'];
echo '</legend>' . "\r\n" . '     <img onclick=dataKeExcel(event,\'log_slave_2riwayatPPExcel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '         <img onclick=dataKePDF(event) title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\r\n" . '        <div style="overflow:scroll; height:400px; width:100%;">' . "\r\n" . '                <table class="sortable" cellspacing="1" border="0" width="2000px">' . "\r\n" . '                                <thead>' . "\r\n" . '                                <tr class=rowheader>' . "\r\n" . '                                        <td>No.</td>' . "\r\n" . '                    <td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n" . '                    <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n" . '                    <td>';
echo $_SESSION['lang']['jumlah'];
echo '</td>' . "\r\n" . '                    <td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['status'];
echo '</td>' . "\r\n" . '                                        <td>';
echo 'O.Std';
echo '</td>' . "\r\n" . '                    <td>';
echo $_SESSION['lang']['chat'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['tgl_po'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['status'] . ' PO';
echo '</td>' . "\r\n" . '                                        ' . "\r\n" . '                                         <td>';
echo $_SESSION['lang']['purchaser'];
echo '</td>' . "\r\n" . '                                          <td>QTY PO</td>' . "\r\n" . '                                           <td>QTY BAPB</td>' . "\r\n\r\n" . '                                        <td>';
echo $_SESSION['lang']['namasupplier'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['rapbNo'];
echo '</td>' . "\r\n" . '                                        <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '                    <td>Action</td>' . "\r\n" . '                                </tr>' . "\r\n" . '                                </thead>' . "\r\n" . '                                <tbody  id="contain">' . "\r\n" . '        <script>loadData()</script>' . "\r\n" . '        </tbody>' . "\r\n" . '    </table>' . "\r\n" . '    </div>' . "\r\n" . '    </fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
