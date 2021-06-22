<style>
[data-tip] {
	position:relative;
}
[data-tip]:before {
	content:'';
	/* hides the tooltip when not hovered */
	display:none;
	content:'';
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-bottom: 5px solid #1a1a1a;	
	position:absolute;
	top:30px;
	left:35px;
	z-index:8;
	line-height:0;
	width:0;
	height:0;
}
[data-tip]:after {
	display:none;
	content:attr(data-tip);
	position:absolute;
	top:35px;
	left:0px;
	padding:5px 8px;
	background:#1a1a1a;
	color:#fff;
	z-index:9;
	height:18px;
	line-height:18px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	white-space:nowrap;
	word-wrap:normal;
}
[data-tip]:hover:before,
[data-tip]:hover:after {
	display:block;
}
</style>
<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$tglvalue = $_POST['tgl'];

	$sqlDefaultFill = "SELECT * FROM pmn_rendemendtall WHERE koderendemen='".$tglvalue.substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."'";
	//echo $sqlDefaultFill;
	$exe = mysql_query($sqlDefaultFill);
	while($resdt=mysql_fetch_object($exe)){
		$rdmAll[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
		$rdmAllht = $resdt->koderendemen;
	}
	$sql = "select * from pmn_rendemendtall ORDER BY koderendemen DESC LIMIT 1 ";
	$str = mysql_query($sql);
	while($resht=mysql_fetch_array($str, MYSQL_ASSOC)){
		$sqlDefaultFill = "SELECT * FROM pmn_rendemendtall WHERE koderendemen='". $resht['koderendemen']."'";
		$exe = mysql_query($sqlDefaultFill);
		while($resdt=mysql_fetch_object($exe)){
			$dtLast[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
			$dtLastht = $resdt->koderendemen;
			//echo "<script> alert('". $dt[$resdt->kodebarang][$resdt->kodelist] ."');</script>";
		}
	}

	$getDpp = "SELECT dpp1.harga AS dppcpo, dpp2.harga AS dpppk, dpp3.harga AS dppck  FROM 
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000002' AND tanggal='". $tglvalue ."' AND organisasi='".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') dpp2,
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000001' AND tanggal='". $tglvalue ."' AND organisasi='".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') dpp1,
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000004' AND tanggal='". $tglvalue ."' AND organisasi='".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') dpp3";
		//echo $getDpp;
	$resDpp = mysql_query($getDpp);	
	while($dataDpp = mysql_fetch_array($resDpp)){
		$dppcpo= $dataDpp['dppcpo'];
		$dpppk = $dataDpp['dpppk'];
		$dppck = $dataDpp['dppck'];
	}
	$getSQLprice = "SELECT a.MINcangkang, a.ppn AS ppnck, b.MINpk, b.ppn AS ppnpk, c.MINcpo, c.ppn AS ppncpo FROM 
		(SELECT min(hargasatuan) AS MINcangkang, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $tglvalue ."' AND kodebarang = '40000004' AND kodept='". substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') a,
		(SELECT min(hargasatuan) AS MINpk, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $tglvalue ."' AND kodebarang = '40000002' AND kodept='". substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') b,
		(SELECT min(hargasatuan)  AS MINcpo, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $tglvalue ."' AND kodebarang = '40000001' AND kodept='". substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."') c";
		//echo $getSQLprice;
	$resPrice = mysql_query($getSQLprice);
	while($dataPrice = mysql_fetch_array($resPrice)){

		//$kontrak['40000001']['000'] = $dataPrice['MINcpo'] * 1.1;		
		//$kontrak['40000002']['000'] = $dataPrice['MINcangkang'];
		//$kontrak['40000004']['000'] = $dataPrice['MINpk'];
		$kontrak['40000001']['003'] = 100;
		$kontrak['40000002']['003'] = 4;
		$kontrak['40000004']['003'] = 7;	
		$cpo1 = $dataPrice['MINcpo'];
		//echo $cpo1;
		if($cpo1 > 0){
			$kontrak['40000001']['000'] = $dataPrice['MINcpo'] * 1.1;
		}else{
			$kontrak['40000001']['000']=$dppcpo ;
		}
		$cpo1 = $dataPrice['MINpk'];
		if($cpo1 > 0){
			$kontrak['40000002']['000'] = $dataPrice['MINpk'];
			//echo $dataPrice['MINpk'];
								//echo $dpppk;
		}else{
			$kontrak['40000002']['000']=round(($dpppk/1.1),2);
								//echo $dpppk;

		}
		$cpo1 = $dataPrice['MINcangkang'];
		if($cpo1 > 0){
			$kontrak['40000004']['000'] = $dataPrice['MINcangkang'];
		}else{
			$kontrak['40000004']['000']=round(($dppck/1.1),2);
		}
					//echo $dpppk;
	}
	if($rdmAll['40000001']['000'] <> ''){
		$method='update';
		$dt = $rdmAll;
		$kddetail = $rdmAllht;
		//echo '<script> alert("Data Sudah Ada");</script>';
	}elseif($kontrak['40000001']['000']<>''){
		$method='insert';
		$dt = $kontrak;
	}else{
		$method='insert';
		$dt = $dtLast;
	}
	$CPOsoldPrice= $dt['40000001']['000'];
	$PKsoldPrice = $dt['40000002']['000'];
	$CksoldPrice= $dt['40000004']['000'];
	$cpoPrice=$dt['40000001']['000'];
	$PKPrice = $dt['40000002']['000'];
	$CkPrice= $dt['40000004']['000'];	
	$costCPO=$dt['40000001']['001'];
	$costPK=$dt['40000002']['001'];
	$costCk=$dt['40000004']['001'];		
	$realCPO=$dt['40000001']['201'];
	$realPK=$dt['40000002']['201'];
	$realCk=$dt['40000004']['201'];	
	$transCPO=$dt['40000001']['002'];
	$transPK=$dt['40000002']['002'];
	$transCk=$dt['40000004']['002'];	
	$oerCPO=$dt['40000001']['003'];
	$oerPK=$dt['40000002']['003'];
	$oerCk=$dt['40000004']['003'];	
	$OERpabrikCPO=$dt['40000001']['003'];
	$OERpabrikpk=$dt['40000002']['003'];
	$OERpabrikcangkang=$dt['40000004']['003'];
	$ppncpo=$dt['40000001']['004'];
	$ppnpk=$dt['40000002']['004'];
	$ppnck=$dt['40000004']['004'];
	
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>";
echo "<script language='javascript' src='js/pmn_tbsrendemen.js?v=".mt_rand()."'></script>";
echo "<script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>";      
echo "<tr><td colspan=2><table>
<tr><td align=left> PRODUK </td>
<td align=center> HARGA TENDER (RP) </td>
<td align=center> HARGA JUAL (RP) </td>
<td align=center> BIAYA OLAH (RP) </td>
<td align=center> REAL BY OLAH (RP) </td>
<td align=center> ONGKOS ANGKUT (RP) </td>
<td align=center> RENDEMEN (%) </td>
<td align=left> REMARK </td>
</tr>";
echo "
<tr><td>CPO</td>
<td align=center><div data-tip='Harga Kontrak CPO'><input type='text' id='CPOPrice' style='width: 120px;' value='".$cpoPrice."' /></div></td>
<td align=center><div data-tip='Harga Jual CPO'><input type='text' id='CPOsoldPrice' style='width: 120px;' value='".$CPOsoldPrice."' /></div></td>
<td align=center><div data-tip='Biaya olah CPO'><input type='text' id='costCPO' style='width: 120px;' value='". $costCPO."' /></div></td>
<td align=center><div data-tip='Biaya olah Real CPO'><input type='text' id='realCPO' style='width: 120px;' value='". $realCPO."' /></div></td>
<td align=center><div data-tip='Biaya Angkut CPO'><input type='text' id='transCPO' style='width: 120px;' value='". $transCPO."' /></div></td>
<td align=center><div data-tip='Rendemen CPO'><input type='text' id='oerCPO' style='width: 120px;' value='". $oerCPO."' /></div></td>
<td> Harga CPO include PPN</td>";
echo "
<tr><td>PK</td>
<td align=center><div data-tip='Harga Kontrak PK'><input type='text' id='PKPrice' style='width: 120px;' value='".$PKPrice."' /></div></td>
<td align=center><div data-tip='Harga Jual PK'><input type='text' id='PKsoldPrice' style='width: 120px;' value='".$PKsoldPrice."' /></div></td>
<td align=center><div data-tip='Biaya olah PK'><input type='text' id='costPK' style='width: 120px;' value='". $costPK."' /></div></td>
<td align=center><div data-tip='Biaya olah Real PK'><input type='text' id='realPK' style='width: 120px;' value='". $realPK."' /></div></td>
<td align=center><div data-tip='Biaya Angkut PK'><input type='text' id='transPK' style='width: 120px;' value='". $transPK."' /></div></td>
<td align=center><div data-tip='Rendemen PK'><input type='text' id='oerPK' style='width: 120px;' value='". $oerPK."' /></div></td>
<td> Harga PK exclude PPN</td>
</tr>";
echo "
<tr><td>Cangkang</td>
<td align=center><div data-tip='Harga Kontrak Cangkang'><input type='text' id='CkPrice' style='width: 120px;' value='".$CkPrice."' /></div></td>
<td align=center><div data-tip='Harga Jual Cangkang'><input type='text' id='CksoldPrice' style='width: 120px;' value='".$CksoldPrice."' /></div></td>
<td align=center><div data-tip='Biaya olah Cangkang'><input type='text' id='costCk' style='width: 120px;' value='". $costCk."' /></div></td>
<td align=center><div data-tip='Biaya olah Real Cangkang'><input type='text' id='realCk' style='width: 120px;' value='". $realCk."' /></div></td>
<td align=center><div data-tip='Biaya Angkut Cangkang'><input type='text' id='transCk' style='width: 120px;' value='". $transCk."' /></div></td>
<td align=center><div data-tip='Rendemen Cangkang'><input type='text' id='oerCk' style='width: 120px;' value='". $oerCk."' /></div></td>
<td> Harga Cangkang exclude PPN</td>
</tr></table> <button class='mybutton' onclick='countRendemen()' >hitung</button></td></tr>";
echo "<tr><td colspan=2><div style='height: 20px; width: 100%; display: block;'></div></td></tr>";
echo "<tr><td colspan=2>
<table id=result style='display: none'>
<thead>
<tr ><td align=center style='width: 180px;'>Keterangan</td>
<td align=center style='width: 120px;'>Harga Jual</td>
<td align=center style='width: 30px;'>:</td>
<td align=center style='width: 120px;'>PPN</td>
<td align=center style='width: 30px;'>-</td>
<td align=center style='width: 120px;'>BIAYA OLAH</td>
<td align=center style='width: 30px;'>-</td>
<td align=center style='width: 120px;'>ONGKOS ANGKUT</td>
<td align=center style='width: 30px;'>x</td>
<td align=center style='width: 120px;'>OER</td>
<td align=center style='width: 30px;'>=</td>
<td align=center style='width: 120px;'>HARGA TBS</td>
<td align=center style='width: 200px;'>REMARK</td>
</tr></thead><tbody>";
for($row=1; $row <= 3; $row++){
	switch($row){
		case 2:
		$ppn=1;
		$rmk="Incl. PPN";
		break;
		case 3:
		$ppn=1.1;
		$rmk ="Harga TBS Per KG ( Real by.olah )";
		break;
		
		default:
		$ppn=1.1;
		$rmk="Excl. PPN";
		break;
	}
	for($row2=1; $row2 <= 3; $row2++){
	switch($row2){
		case 1:
		$item = 'CPO';
		break;
		case 2:
		$item = 'PK';
		$ppn = 1;
		break;
		case 3:
		$item = 'CANGKANG';
		$ppn = 1;
		break;
	}
	echo "
	<tr><td id='".$item."[".$row."]' align=center>HARGA ".$item."</td>
	<td id='price".$item."[".$row."]' align=center>".$item."</td>
	<td align=center>:</td>
	<td id='ppn".$item."[".$row."]' align=center>".$ppn."</td>
	<td align=center>-</td>
	<td id='olah".$item."[".$row."]' align=center>BIAYA OLAH</td>
	<td align=center>-</td>
	<td id='angkut".$item."[".$row."]' align=center>ONGKOS ANGKUT</td>
	<td align=center>x</td>
	<td id='oer".$item."[".$row."]' align=center>OER</td>
	<td align=center>=</td>
	<td id='hargatbs".$item."[".$row."]' align=center>HARGA TBS</td>
	<td align=center>".$rmk."</td>
	</tr>";
	}
	echo "<tr><td><div style='height: 20px; width: 100%; display: block;'></div></td></tr>";
}
echo "</tbody></table></td></tr>";
echo "<tr><td colspan=2 align=right><div style='height: 20px; width: 100%; display: block;'><input id='method' style='display:none ;' value='".$method ."' /><button class='mybutton' onclick=simpanAll('".$tglvalue."&org=".substr($_SESSION['empl']['namalokasitugas'],0,3)."') type=''>Simpan</button> </td></div></td></tr>";
echo "<tr><td colspan=2><div style='height: 20px; width: 100%; display: block;'>RINCIAN HARGA TBS PER RENDEMEN</div></td></tr>";
echo "<tr><td>
<table id=result2 style='display: none'>
<thead><tr><td align=center style='width: 90px;' rowspan=2>Rendemen</td>
<td align=center style='width: 120px;' rowspan=2>Harga TBS</br> (excl.PK & Cangkang) </td>
<td align=center style='width: 120px;' rowspan=2>Harga PK</td>
<td align=center style='width: 120px;' rowspan=2>Harga Cangkang</td>
<td align=center colspan=3>Harga TBS Per Kilogram</td></tr>
<tr><td align=center style='width: 120px;'>Excl. PPN</td>
<td align=center style='width: 120px;'>Incl. PPN</td>
<td align=center style='width: 120px;'>Real By.Olah	</td>
</tr><thead><tbody id=rendemenresult>";
echo "</tbody></table></td>";
echo"<td style='vertical-align: top'>
<table id=manual style='display: none'><thead>
<tr><td align=center style='width: 90px;' rowspan=2>Rendemen</td>
<td align=center style='width: 120px;' rowspan=2>Harga TBS</br> (excl.PK & Cangkang) </td>
<td align=center style='width: 120px;' rowspan=2>Harga PK</td>
<td align=center style='width: 120px;' rowspan=2>Harga Cangkang</td>
<td align=center colspan=3>Harga TBS Per Kilogram</td>
<td align=center style='width: 40px;' rowspan=2>  </td></tr>
<tr><td align=center style='width: 120px;'>Excl. PPN</td>
<td align=center style='width: 120px;'>Incl. PPN</td>
<td align=center style='width: 120px;'>Real By.Olah	</td>
</tr></thead><tbody id=rendemenmanual>";
if($method=='insert'){
echo "<tr id=inputRow><td align=center style='width: 90px;'>
<div data-tip='Persentase Rendemen'><input type='text' id='rendemen1' style='width: 120px;' value='' onchange='manualRendemen(this)' /></div></td>
<td align=center style='width: 120px;'></br></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 80px;'>
<button  onclick='tambahRow()' > + </button>
<button onclick=deleteRow(2)> - </button></td>
</tr>";
}else{
$sqlbaris='SELECT * FROM pmn_rendemendiff WHERE koderendemen="'.$kddetail.'" ORDER BY persen ASC';
//echo $sqlbaris;
$runbaris=mysql_query($sqlbaris);
$nbaris=2;
while($databaris= mysql_fetch_assoc($runbaris)){
$hargatbsexcl = round((($CPOsoldPrice/1.1-$costCPO-$transCPO)*$oerCPO/100 *$databaris['persen']/100), 2);
$finalhargaPK = round((($PKsoldPrice/1-$costPK-$transPK)*$oerPK/100),2);
$finalhargaCk = round((($CksoldPrice/1-$costCk-$transCk)*$oerCk/100),2);
$hargaexcludeppn = $hargatbsexcl + $finalhargaPK + $finalhargaCk;
$hargacpoppn = round((($CPOsoldPrice/1-$costCPO-$transCPO)*$oerCPO/100),2);
$hargaRcpoppn = round((($CPOsoldPrice/1.1-$realCPO)*$oerCPO/100),2);
$finalhargaPK2 = round((($PKsoldPrice/1-$realPK)*$oerPK/100),2);
$finalhargaCk2 = round((($CksoldPrice/1-$realCk)*$oerCk/100),2);
//echo $CPOsoldPrice.'/'.'1'.'-'.$costCPO.'-'.$transCPO.')*'.$oerCPO.'/100';
echo "<tr id=inputRow><td align=center style='width: 90px;'>
<div data-tip='Persentase Rendemen'><input type='text' id='rendemen".$nbaris."' style='width: 120px;' value='".$databaris['persen']."' onchange='manualRendemen(this)' /></div></td>
<td align=center style='width: 120px;'>". $hargatbsexcl ." </br></td>
<td align=center style='width: 120px;'>". $finalhargaPK ."</td>
<td align=center style='width: 120px;'>". $finalhargaCk ."</td>
<td align=center style='width: 120px;'>". $hargaexcludeppn ."</td>
<td align=center style='width: 120px;'>". round((($hargacpoppn*$databaris['persen']/100)+$finalhargaPK+$finalhargaCk),2) ."</td>
<td align=center style='width: 120px;'>". round((($hargaRcpoppn*$databaris['persen']/100)+$finalhargaPK2+$finalhargaCk2),2) ."</td>
<td align=center style='width: 80px;'>
<button  onclick='tambahRow()' > + </button>
<button onclick=deleteRow(".$nbaris.")> - </button></td>
</tr>";
$nbaris++;
}
echo "<tr id=inputRow><td align=center style='width: 90px;'>
<div data-tip='Persentase Rendemen'><input type='text' id='rendemen".$nbaris."' style='width: 120px;' value='' onchange='manualRendemen(this)' /></div></td>
<td align=center style='width: 120px;'></br></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 120px;'></td>
<td align=center style='width: 80px;'>
<button  onclick='tambahRow()' > + </button>
<button onclick=deleteRow(".$nbaris.")> - </button></td>
</tr>";
}
echo "</tbody></table></td></tr>";
echo "<img onclick=previewExcelAll('".$tglvalue."',event) src=images/excel.jpg class=resicon title='MS.Excel'>";
?>