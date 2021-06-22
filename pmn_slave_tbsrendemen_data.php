<?php 
       require_once 'master_validation.php';
       include 'lib/eagrolib.php';
       include('lib/zMysql.php');
	   include 'lib/zFunction.php';
       echo open_body();
       echo "<script language='javascript' src='js/pmn_tbsrendemen.js?v=".mt_rand()."'></script>";
       echo "<script language='javascript' src='js/zMaster.js?v=".mt_rand()."'></script>";      
		echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>";

       OPEN_BOX('', $_SESSION['lang']['kontrakjual']);
		$paratgl1 = $_GET['tglAwal'];
		$paratgl2 = $_GET['tglAkhir'];
		if($orgvalue == ""){
			$orgvalue=substr($_SESSION['empl']['namalokasitugas'], 0 ,3);
		}
?>	 
	<div>
	<table style="width:100%;" id="dataForm">
	<tr id=container align="top">
	<td style="width:50%;"  style="text-align:top;">
	 <div style='width:100%;height:550px;overflow:scroll;'>
       <table id="tableOer"  class=sortable cellspacing=1 border=0 width=600>
	     <thead><tr><td align=center>Kode Organisasi</td><td align=center>Tanggal</td><td align=center>Margin</td><td align=center>Laba(Rugi)</td><td align=center>Action</td></tr>  
		 </thead>
		 <tbody id=conter>
<?php
	
	$num = 1;
	if($paratgl1 <> ""){
		$sql="SELECT * from pmn_rendemenht WHERE tglrendemen BETWEEN '".$paratgl1."' AND '".$paratgl2."'
		AND kodeorg='".substr($orgvalue,0,3)."' ORDER BY tglrendemen DESC ;";
		//echo $sql;
		$res=mysql_query($sql);
		while($bar=mysql_fetch_object($res)){
			if($num % 2 == 0){ 
				$color = "#84B4DF";
			} 
			else{ 
				$color = "#FFFFFF";
			}
			echo "<tr style='background:". $color .";'><td>". $bar->kodeorg ."</td><td align='right'>". $bar->tglrendemen ."</td><td align='right'>". number_format($bar->margin, 2) ." %</td>
					<td  align='right'>". number_format($bar->labarugi, 2) ."</td><td align='center'>
					<img onclick=previewExcel('".$bar->koderendemen."',event) src=images/excel.jpg class=resicon title='MS.Excel'>
					<img src='images/pdf.jpg' class='resicon' title='PDF' onclick=previewBast('".$bar->koderendemen."',event)>
					</td></tr> ";
			$num++;
			$tonase = $tonase + $bar->totalkg;
			$kontraktot = $kontraktot + $bar->tot_tgh;
		} 	
	}
	//echo $cpoPrice .'/'.$ppncpo.'.';
?>
	 </tr>
	 </table>
	 </div>
<?php	
	CLOE_BOX();
    echo close_body();
?>

