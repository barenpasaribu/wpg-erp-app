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
       include 'lib/eagrolib.php';
       include('lib/zMysql.php');
	   include 'lib/zFunction.php';
       echo open_body();
       include 'master_mainMenu.php';
	   require_once 'lib/fpdf.php';
       echo "<script language='javascript' src='js/pmn_tbsrendemen.js?v=".mt_rand()."'></script>";
       echo "<script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>";      
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>";

       OPEN_BOX('', $_SESSION['lang']['kontrakjual']);
		$paratgl = $_POST['date'];
		if($paratgl <> ""){
			$enable = "disabled"; 
			$tglvalue = $paratgl;
			$orgvalue = $_POST['selectOrg'];
			$cancelBut = '<button class="mybutton" onclick="cleardata(1);" type="button">Batal</button>';
			$status = $_POST['stat'];
			if($status == ""){
				$status = "Silakan Cek Perhitungan";
			}
			$koderendemen= $paratgl .substr($orgvalue,0,3);
		}else{
			$enable = "";
			$tglvalue = "";
			$orgvalue = "";
			$cancelBut = "";
			$status == "";
			$koderendemen ='Err';
		}
		if($orgvalue == ""){
			$orgvalue=substr($_SESSION['empl']['namalokasitugas'], 0 ,3);
		}
?>

		<fieldset id="">
		<legend><span class="judul">Penilaian Rendemen TBS:</span></legend>
		<div id="contentBox" style="overflow:auto;">
		<fieldset>
	 <table style="width: 100%;">
	 <tbody >
	 <tr>
	   <td style="width:60%;" >
	   <form method="post">
	   Tanggal
	   <input type="date" id="paradate" name="filterdate" onchange="replaceDate()" <?php echo $enable; ?> required />
	   <input id="tempdate" name="date"  style='display: none;' <?php echo $enable; ?> value="<?php echo $paratgl; ?>" />
	   Organisasi
	   <!--input id="idPabrikTahun" style="display: none;" value="<?php //echo substr($_SESSION['empl']['namalokasitugas'], 0 ,3); ?>" /-->
 	   <select id="idPabrikTahun" name="selectOrg" style="width:150px; height:26px;" disabled>
<?php 
		
		$sPabrik="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK' AND kodeorganisasi LIKE '%".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."%'";
		$qPabrik=mysql_query($sPabrik) or die(mysql_error());
		while($rPabrik=mysql_fetch_assoc($qPabrik)){
			echo "<option value=".$rPabrik['kodeorganisasi']." selected>".$rPabrik['namaorganisasi']."</option>";
		}
		?>		
		</select>
		<button class="mybutton" onclick="showdata()" <?php echo $enable; ?> type="submit">Cari</button>
		<?php echo $cancelBut; ?>
		</form></td><td style="font-color: #050505;" ><?php echo $status; ?></td>
	 </tr>
	 <tr>
		<td colspan="2">
		Data Rendemen: Periode:
	   <input type="date" id="startdate" name="filterdate" onchange="replaceStart()" <?php echo $enable; ?>/>
	   <input id="varstart" name="dateStart" style="display: none;" <?php echo $enable; ?> value="<?php echo $paratgl; ?>" /> -
	   <input type="date" id="enddate" name="filterdate" onchange="replaceEnd()" <?php echo $enable; ?>/>
	   <input id="varend" name="dateEnd" style="display: none;" <?php echo $enable; ?> value="<?php echo $paratgl; ?>" /> 
	   <button class="mybutton" onclick="showtable()" <?php echo $enable; ?>>Lihat Data</button>
</td>
	   </tr>
	 </tbody>
	 </table>
     </fieldset></div></fieldset>
<?php
    CLOSE_BOX();	
    echo "<div id='table_laporan_produksi_harian'></div>";
	OPEN_BOX();
?>	 
	<div>
	<table style="width:100%;" id="dataForm">
	<tr id=container align="top">
	<td style="width:50%;"  style="text-align:top;">
	 <div style='width:100%;height:550px;overflow:scroll;'>
       <table id="tableOer"  class=sortable cellspacing=1 border=0 width=600>
	     <thead><tr><td align=center>Supplier </td><td align=center>Price A /kg</td><td align=center>Fee /kg</td><td align=center>Harga /Kg</td><td align=center>Tonase</td><td align=center>Total</td><td align=center>OER</td></tr>  
		 </thead>
		 <tbody id=conter>
<?php

	$num = 1;
	if($paratgl <> ""){
		$sql="select * from pmn_tbl_rendemen_vw2 
				where tgltimbang ='". $paratgl ."' and 
				SUBSTRING(klsupplier, CHAR_LENGTH(klsupplier)-2, 3) = '". substr($orgvalue,0,3) ."'";
		
		//echo $sql;
		$res=mysql_query($sql);
		while($bar=mysql_fetch_object($res)){
			if($num % 2 == 0){ 
				$color = "#84B4DF";
			} 
			else{ 
				$color = "#FFFFFF";
			}
			echo "<tr style='background:". $color .";'><td>". $bar->namasupplier ."</td><td align='right'>". number_format($bar->harga_harian, 2) ."</td><td align='right'>". number_format($bar->fee, 2) ."</td>
					<td  align='right'>". number_format($bar->harga_akhir+$bar->fee, 2) ."</td><td align='right'>".number_format($bar->totalkg) ."</td><td align='right'>". number_format($bar->tot_tgh, 2) ."</td><td id=oerkontrak[".$num."] > </td></tr> ";
			$num++;
			$tonase = $tonase + $bar->totalkg;
			$kontraktot = $kontraktot + $bar->tot_tgh;
		}
		$sqlOer = "SELECT rendemen_cpo_after, rendemen_pk_after FROM pabrik_produksi WHERE tanggal='". $paratgl. "' AND kodeorg LIKE '". substr($orgvalue,0,3) ."%' LIMIT 1";
		$resOer=mysql_query($sqlOer);
		while($bar=mysql_fetch_object($resOer)){
			$actualOer = $bar-> rendemen_cpo_after;
			$actualPK = $bar->rendemen_pk_after;
		}
		$hargakilo = $kontraktot / $tonase;
		$tonase = round($tonase, 2);
		$kontraktot = round($kontraktot, 2);		
		$hargakilo = round($hargakilo, 2);		
	}
?>
		 <tfoot id="confoot" style="background:#01A9DB;" >
			<tr><td align=center> Total </td><td></td><td></td>
			<td align=right><?php echo number_format($hargakilo, 2); ?></td>
			<td align=right><?php echo number_format($tonase, 2); ?></td>
			<td align=right><?php echo number_format($kontraktot, 2); ?></td>
			<td align=right id="oerTotal"></td></tr> 
		 </tfoot>				
		 </tbody>
	   </table>
	 </div>
	 </td>
<?php
 /*    CLOSE_BOX();
	OPEN_BOX(); */
	//Cangkang 40000004
	//PK 40000002
	//CPO 40000001
		$sql = "select * from pmn_rendemenht where tglrendemen='". $paratgl ."' and kodeorg='". substr($orgvalue,0,3) . "'";
		//echo $sql;
		$str = mysql_query($sql);
		$num_rows = mysql_num_rows($str);
		if($num_rows >= 1){
			$method = 'update';
			echo "<script> alert('Data Rendemen sudah ada.');</script>";
			$sql = "select * from pmn_rendemenht where tglrendemen='". $paratgl ."' and kodeorg='". substr($orgvalue,0,3) . "'";
			$str = mysql_query($sql);
			while($resht=mysql_fetch_array($str, MYSQL_ASSOC)){
				$decreas=$resht['decreas'];
				$biayakandir=$resht['kandir'];
				$totalBiaya=$resht['totalbiaya'];
				$totalResult=$resht['totalhasil'];
				$totalPurch=$resht['totalpurchase'];
				$labaRugi=$resht['labarugi'];
				$tonaseht=$resht['tonase'];
				$pricekg=$resht['pricekg'];
				$koderendemen=$resht['koderendemen'];
				$tglrendemen=$resht['tglrendemen'];
			}
		}else{
			$method = 'insert';
			if($paratgl!=""){
				echo "<script> alert('Rendemen baru dibuat.');</script>";
			}
			//$sql = "select * from pmn_rendemenht WHERE kodeorg='". substr($orgvalue,0,3) . "' ORDER BY koderendemen DESC LIMIT 1 ";
			$sql = "select * from pmn_rendemenht ORDER BY koderendemen DESC LIMIT 1 ";
			$str = mysql_query($sql);
			while($resht=mysql_fetch_array($str, MYSQL_ASSOC)){
				$decreas=$resht['decreas'];
				$biayakandir=$resht['kandir'];
				$totalBiaya=$resht['totalbiaya'];
				$totalResult=$resht['totalhasil'];
				$totalPurch=$resht['totalpurchase'];
				$labaRugi=$resht['labarugi'];
				$tonaseht=$resht['tonase'];
				$pricekg=$resht['pricekg'];
				$koderendemen=$resht['koderendemen'];
				$tglrendemen=$resht['tglrendemen'];
			}
		}
		$sqlDefaultFill = "SELECT * FROM pmn_rendemendt WHERE koderendemen='". $koderendemen ."'";
		//echo $sql;
						
		$exe = mysql_query($sqlDefaultFill);
		while($resdt=mysql_fetch_object($exe)){
			$dt[$resdt->kodebarang][$resdt->kodelist]= $resdt->amount;
			//echo "<script> alert('". $dt[$resdt->kodebarang][$resdt->kodelist] ."');</script>";
		}
		
	$getSQLprice = "SELECT a.MINcangkang, a.ppn AS ppnck, b.MINpk, b.ppn AS ppnpk, c.MINcpo, c.ppn AS ppncpo FROM 
		(SELECT min(hargasatuan) AS MINcangkang, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $paratgl ."' AND kodebarang = '40000004' AND kodept='". substr($orgvalue,0,3)."') a,
		(SELECT min(hargasatuan) AS MINpk, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $paratgl ."' AND kodebarang = '40000002' AND kodept='". substr($orgvalue,0,3)."') b,
		(SELECT min(hargasatuan)  AS MINcpo, MAX(PPN) AS PPN
		FROM pmn_kontrakjual 
		WHERE tanggalkontrak='". $paratgl ."' AND kodebarang = '40000001' AND kodept='". substr($orgvalue,0,3)."') c";
	$getDpp = "SELECT dpp1.harga AS dppcpo, dpp2.harga AS dpppk, dpp3.harga AS dppck  FROM 
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000002' AND tanggal='". $paratgl ."' AND organisasi='".substr($orgvalue,0,3)."') dpp2,
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000001' AND tanggal='". $paratgl ."' AND organisasi='".substr($orgvalue,0,3)."') dpp1,
		(SELECT min(harga) AS harga FROM pmn_hargapasar
			WHERE kodeproduk='40000004' AND tanggal='". $paratgl ."' AND organisasi='".substr($orgvalue,0,3)."') dpp3";
		//echo $getSQLprice;
	$resPrice = mysql_query($getSQLprice);	
	
	while($dataPrice = mysql_fetch_array($resPrice)){
		$cpoPrice = $dataPrice['MINcpo'];
		$cangkangpPrice = $dataPrice['MINcangkang'];
		$pkPrice = $dataPrice['MINpk'];
		$ppncpo = ($dataPrice['ppncpo']+100)/100;
		$ppnck = ($dataPrice['ppnck']+100)/100;
		$ppnpk = ($dataPrice['ppnpk']+100)/100;
	$cpoPrice=$cpoPrice * $ppncpo;
	$cangkangpPrice=$cangkangpPrice * $ppnck;
	$pkPrice=$pkPrice * $ppnpk;
	}
	//echo $cpoPrice .'/'.$ppncpo.'.';
	$resDpp = mysql_query($getDpp);	
	while($dataDpp = mysql_fetch_array($resDpp)){
		$dppcpo= $dataDpp['dppcpo'];
		$dpppk = $dataDpp['dpppk'];
		$dppck = $dataDpp['dppck'];
	}
	if($cpoPrice <= 0){
		$cpoPrice= $dppcpo;
		$ppncpo = (10+100)/100;
	}
	if($cangkangpPrice <= 0){
		$cangkangpPrice= $dppck;
		$ppnck = (10+100)/100;
	}
	if($pkPrice <= 0 or $pkPrice ==""){
		$pkPrice = $dpppk;
		$ppnpk = (10+100)/100;
	}

	if($method=='update'){
		$cpoPrice = $dt['40000001']['000'];
		$pkPrice = $dt['40000002']['000'];
		$cangkangpPrice = $dt['40000004']['000'];
		$actualOer =  $dt['40000001']['301'];
		$actualPK =  $dt['40000002']['301'];
		$ppnpk = $dt['40000002']['004'];
		$ppncpo= $dt['40000001']['004'];
		$ppnck= $dt['40000004']['004'];
	}
	if($cpoPrice <= 0){
		$cpoPrice= $dt['40000001']['000'];
		$ppncpo = $dt['40000002']['004'];
	}
	if($cangkangpPrice <= 0){
		$cangkangpPrice= $dt['40000004']['000'];
		$ppnck= $dt['40000004']['004'];
	}
	if($pkPrice <= 0 or $pkPrice ==""){
		$pkPrice = $dt['40000002']['000'];
		$ppnpk= $dt['40000002']['004'];
	}
/* 	if($actualOer <= 0){
		$cpoPrice= $dt['40000001']['301'];
	}
	if($actualPK <= 0){
		$cangkangpPrice= $dt['40000002']['301'];
	} */
?>
	<td style="width:50%;" align="top">
	 <div style='width:100%; height:580px; overflow: visible;'>
	 <table>
	     <thead><tr><td align=center colspan="14">HARGA TENDER TANGGAL <?php echo $paratgl."/".substr($orgvalue,0,3); ?></td></tr>  
		 </thead>
	 <tbody>
	 <tr>
	   <td>CPO</td>
	   <td><div data-tip="Harga Jual CPO"><input type="text" id="CPOPrice" style="width: 80px;" value="<?php echo $cpoPrice; ?>" /></div></td>
	   <td>:</td>
	   <td><input type="text" id="ppncpo" style="width: 60px;" value="<?php echo $ppncpo; ?>" /></td>
	   <td>-</td>
	   <td><div data-tip="Biaya olah CPO"><input type="text" id="costCPO" style="width: 80px;" value="<?php echo $dt['40000001']['001']; ?>" /></div></td>
	   <td>-</td>
	   <td><div data-tip="Ongkos Angkut CPO"><input type="text" id="TransCPO" style="width: 80px;" value="<?php echo $dt['40000001']['002']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type="text" id="resultCPO" style="width: 80px;" onfocus="countCPO(this)" /></td>
	   <td></td>
	   <td><div data-tip="OER Pabrik CPO"><input type="text" id="OERpabrikCPO" style="width: 80px;display: none;" value="<?php echo $dt['40000001']['003']; ?>"/></td>
	   <td>=</td>
	   <td><input type="text" id="resCPO" style="width: 80px;" onfocus="resCPO(this)" /></td>
	 </tr>
	 <tr>
	   <td>PK</td>
	   <td><div data-tip="Harga Jual pk"><input type="text" id="pkPrice" style="width: 80px;" value="<?php echo $pkPrice; ?>" /></div></td>
	   <td>:</td>
	   <td><input type="text" id="ppnpk" style="width: 60px;" value="<?php echo $ppnpk; ?>" /></td>
	   <td>-</td>
	   <td><div data-tip="Biaya olah pk"><input type="text" id="costpk" style="width: 80px;" value="<?php echo $dt['40000002']['001']; ?>"/></div></td>
	   <td>-</td>
	   <td><div data-tip="Ongkos Angkut pk"><input type="text" id="Transpk" style="width: 80px;" value="<?php echo $dt['40000002']['002']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type="text" id="resultpk" style="width: 80px;" onfocus="countPK(this)" /></td>
	   <td>x</td>
	   <td><div data-tip="OER Pabrik pk"><input type="text" id="OERpabrikpk" style="width: 80px;"
	   value="<?php echo $dt['40000002']['003']; ?>"/></td>
	   <td>=</td>
	   <td><input type="text" id="respk" style="width: 80px;" onfocus="resPK(this)"/></td>
	 </tr>
	 <tr>
	   <td>Cangkang</td>
	   <td><div data-tip="Harga Jual cangkang"><input type="text" id="cangkangPrice" style="width: 80px;" value="<?php echo $cangkangpPrice; ?>" /></div></td>
	   <td>:</td>
	   <td><input type="text" id="ppnck" style="width: 60px;" value="<?php echo $ppnck; ?>" /></td>
	   <td>-</td>
	   <td><div data-tip="Biaya olah cangkang"><input type="text" id="costcangkang" style="width: 80px;" value="<?php echo $dt['40000004']['001']; ?>"/></div></td>
	   <td>-</td>
	   <td><div data-tip="Ongkos Angkut cangkang"><input type="text" id="Transcangkang" style="width: 80px;" value="<?php echo $dt['40000004']['002']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type="text" id="resultcangkang" style="width: 80px;" onfocus="countCk(this)" /></td>
	   <td>x</td>
	   <td><div data-tip="OER Cangkang *jika ada"><input type="text" id="OERpabrikcangkang" style="width: 80px;" value="<?php echo $dt['40000004']['003']; ?>"/></td>
	   <td>=</td>
	   <td><input type="text" id="rescangkang" style="width: 80px;" onfocus="resCk(this)"/></td>
	 </tr>
	 <tr>
	   <td colspan="3">MARGIN</td><td></td>
	   <td>=</td>
	   <td><input type='text' id="marginper" style="width: 80px;" value="" onfocus="getMarginOer();"/></td>
	   <td></td>	   <td></td>	   <td></td>	   <td></td>	   <td></td>   <td></td>   <td></td>
	 </tr>
	 <tr>
	   <td colspan="4" >Pembelian OER</td>
	   <td>=</td>
	   <td><input type='text' id="totalOer" name="totalOer" style="width: 80px;" /></td>
	   <td></td>
	   <td><input type='text' id="tonasetotal1" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td> </td>
	   <td><input type='text' id="pricekg" style="width: 80px;" value="<?php echo $hargakilo; ?>" /></td>
	   <td></td>
	   <td></td>
	   <td>=</td><td>
			<input type='text' id="totalBeli" style="width: 80px;" value="<?php echo $kontraktot; ?>" /></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="background: #ffffff;" colspan="14"><b>HASIL</b></td>
	 </tr>
	 <tr>
	   <td colspan="4"><b>OER ACTUAL</b></td>
	   <td>=</td>
	   <td><div data-tip="Actual CPO OER, Dari Berita Acara Sounding, After Granding"><input type='text' id="actualOer" style="width: 80px;" value="<?php echo  $actualOer; ?>"/></div></td>
	   <td>x</td>
	   <td><input type='text' id="tonasetotal2" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td>=</td>
	   <td><input type='text' id="amountOer" style="width: 80px;" value="" onfocus="amountOer(this)" /></td>
	   <td>x</td>
	   <td><div data-tip="DPP CPO"><input type='text' id="dppOer" style="width: 80px;" value="<? echo number_format($dppcpo,2); ?>" /></div></td>
	   <td>=</td>
	   <td><input type='text' id="jualOer" style="width: 80px;" onfocus="jualOer(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan="4"><b>PK ACTUAL</b></td>
	   <td>=</td>
	   <td><div data-tip="Actual PK, Dari Berita Acara Sounding, After Granding"><input type='text' id="actualPK" style="width: 80px;" value="<?php echo $actualPK; ?>" /></div></td>
	   <td>x</td>
	   <td><input type='text' id="tonasetotal3" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td>=</td>
	   <td><input type='text' id="amountPK" style="width: 80px;" value="" onfocus="amountPK(this)" /></td>
	   <td>x</td>
	   <td><div data-tip="DPP PK"><input type='text' id="dppPK" style="width: 80px;" value="<? echo $dpppk; ?>" /></div></td>
	   <td>=</td>
	   <td><input type='text' id="jualPK" style="width: 80px;" onfocus="jualPK(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan='4' ><b>CANGKANG ESTIMASI</b></td>
	   <td>=</td>
	   <td><div data-tip="Estimasi ekstraksi Cangkang"><input type='text' id="actualCk" style="width: 80px;" value="<?php echo $dt['40000004']['301']; ?>"/></div></td>
	   <td>x</td>
	   <td><input type='text' id="tonasetotal4" style="width: 80px;" value="<?php echo $tonase; ?>"/></td>
	   <td>=</td>
	   <td><input type='text' id="amountCk" style="width: 80px;" value="" onfocus="amountCk(this)" /></td>
	   <td>x</td>
	   <td><div data-tip="DPP Cangkang"><input type='text' id="dppCk" style="width: 80px;" value="<? echo number_format($dppck,2); ?>" /> </div></td>
	   <td>=</td>
	   <td><input type='text' id="jualCk" style="width: 80px;" onfocus="jualCk(this)"/></td>
	 </tr>
	 <tr style="background: #ffffff;">
	   <td colspan="12"><b> TOTAL HASIL</b></td>
	   <td>=</td>
	   <td><input type='text' id="totalHasil" style="width: 80px;" onfocus="hasil(this)" /></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr>
	   <td style="background: #01A9DB;" colspan="14"><b>BIAYA</b></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> PEMBELIAN TBS</b></td>
	   <td><input type='text' id="tonasetotal6" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td>x</td>
	   <td><input type='text' id="" style="width: 80px;" value="<?php echo $hargakilo; ?>" /></td>
	   <td>=</td>
	   <td><input type='text' id="totalPurchase" name="totalPurchase" style="width: 80px;"value="<?php echo $kontraktot; ?>" /></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA OLAH CPO</b></td>
	   <td><input type='text' id="oerCostCpo" name ="oerCostCpo" style="width: 80px;" /></td>
	   <td>x</td>
	   <td><div data-tip="Olah CPO per kilo"><input type='text' id="amountcpoprod" name="cpoprodcost" style="width: 80px;" value="<?php echo $dt['40000001']['201']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type='text' id="cpoprodcost" style="width: 80px;" onfocus="cpoprodcost(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA OLAH PK</b></td>
	   <td><input type='text' id="oerCostPK" style="width: 80px;" /></td>
	   <td>x</td>
	   <td><div data-tip="Olah PK per kilo"><input type='text' id="amountpkprod" name="pkprodcost" style="width: 80px;" value="<?php echo $dt['40000002']['201']; ?>" /></div></td>
	   <td>=</td>
	   <td><input type='text' id="pkprodcost" style="width: 80px;" onfocus="pkprodcost(this)" /></td>
	 </tr>
	 <tr style="display: none;">
	   <td colspan="9"><b> BIAYA OLAH CANGKANG</b></td>
	   <td><input type='text' id="oerCostCk" style="width: 80px;" /></td>
	   <td>x</td>
	   <td><div data-tip="Olah cangkang per kilo *jika ada"><input type='text' id="amountckprod" name="pkprodcost" style="width: 80px;" value="<?php echo $dt['40000004']['201']; ?>" /> </div></td>
	   <td>=</td>
	   <td><input type='text' id="ckprodcost" style="width: 80px;" onfocus="ckprodcost(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA KANDIR</b></td>
	   <td><input type='text' id="tonasetotal5" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td>x</td>
	   <td><div data-tip="Biaya Kandir"><input type='text' id="amountkandir" name="kandircost" style="width: 80px;" value="<?php echo $biayakandir; ?>"</div></td>
	   <td>=</td>
	   <td><input type='text' id="kandircost" style="width: 80px;" onfocus="kandircost(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA PENGANGKUTAN CPO</b></td>
	   <td><input type='text' id="tripCostCpo" style="width: 80px;" value=""  /></td>
	   <td>x</td>
	   <td><div data-tip="Angkut CPO per kilo"><input type='text' id="transCostCpo" name="transCostCpo" style="width: 80px;" value="<?php echo $dt['40000001']['202']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type='text' id="totcostCpo" style="width: 80px;" onfocus="totcostCpo(this)"/></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA PENGANGKUTAN PK</b></td>
	   <td><input type='text' id="tripCostPK" style="width: 80px;" value="" /></td>
	   <td>x</td>
	   <td><div data-tip="Angkut PK per kilo"><input type='text' id="transCostPK" name="transCostPK" style="width: 80px;" value="<?php echo $dt['40000002']['202']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type='text' id="totcostPK" style="width: 80px;" onfocus="totcostPK(this)" /></td>
	 </tr>
	 <tr style="display: none;">
	   <td colspan="9"><b> BIAYA PENGANGKUTAN CANGKANG</b></td>
	   <td><input type='text' id="tripCostCk" style="width: 80px;" value="" /></td>
	   <td>x</td>
	   <td><div data-tip="Angkut Cangkang per kilo *jika ada"><input type='text' id="transCostCk" name="transCostCk" style="width: 80px;" value="<?php echo $dt['40000004']['202']; ?>"/></div></td>
	   <td>=</td>
	   <td><input type='text' id="totcostCk" style="width: 80px;" onfocus="totcostCk(this)"/></td>
	 </tr>
	 <tr>
	   <td colspan="9"><b> BIAYA PENYUSUTAN</b></td>
	   <td><input type='text' id="tonasetotal" style="width: 80px;" value="<?php echo $tonase; ?>" /></td>
	   <td>x</td>
	   <td><div data-tip="per kilo"><input type='text' id="amountdecreas" name="amountdecreas" style="width: 80px;" value="<?php echo $decreas; ?>" /></div></td>
	   <td>=</td>
	   <td><input type='text' id="decreasCost" style="width: 80px;" onfocus="decreasCost(this)" /></td>
	 </tr>
	 <tr>
	   <td colspan="12"><b>TOTAL BIAYA</b></td>
	   <td>=</td>
	   <td><input type='text' id="totalCost" name="totalCost" style="width: 80px;" onfocus="totalCost(this)"/></td>
	 </tr>
	 <tr>
	   <td style="height:12px;"></td><td></td>
	   <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	 </tr>
	 <tr style="background: #01A9DB;" >
	   <td colspan="13"><b>LABA (RUGI)</b></td>
	   <td><input type='text' id="labarugi" name="labarugi" style="width: 80px;" onfocus="labafinal(this)" /></td>
	 </tr>
	 <tr>
	   <td style="height:8px;"></td><td></td>
	   <td><input id="method" style="display: none;" <?php echo $enable; ?> value="<?php echo $method; ?>" /></td>
	   <td><input id="organisasi" style="display: none;" <?php echo $enable; ?> value="<?php echo substr($orgvalue,0,3); ?>" /></td><td></td><td></td><td></td><td></td><td></td>

	   <td></td><td></td><td><button class="mybutton" onclick="previewBast('<?php echo $koderendemen; ?>',event);">Print</button></td><td></td>
	   <td><button class="mybutton" onclick="simpan()" type="">Simpan</button>
	   </td>
	 </tr>
	 </tbody>
	 </table>
	 </div>
     </td>
	 </tr>
	 </table>
	 </div>
<?php	
	   if($method=='update'){
		   echo'<input type="image" src=images/Untitled.png onload="labafinal()" />';
	   }
	CLOE_BOX();
    echo close_body();
?>

