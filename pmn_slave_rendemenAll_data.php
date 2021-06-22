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
		if($orgvalue == ""){
			$orgvalue=substr($_SESSION['empl']['namalokasitugas'], 0 ,3);
		}
?>	 
	 <div style='width:100%;height:550px;overflow:scroll;'>
       <table id="tableOer"  class=sortable cellspacing=1 border=0 width=600>
	     <thead><tr><td align=center width=15%>Kode Organisasi</td><td align=center width=20% >Tanggal</td><td align=center width=30%>CPO Jual</td><td align=center width=15%>Rendemen CPO</td><td align=center width=20%>Action</td></tr>  
		 </thead>
		 <tbody id=conter>
<?php
	
	$num = 1;
		$sql="SELECT SUBSTR(koderendemen, 11, 14) AS org, SUBSTR(koderendemen, 1, 10) AS tgl, amount, koderendemen,
			(SELECT amount b FROM pmn_rendemendtall b WHERE kodelist='003' AND kodebarang='40000001' AND b.koderendemen = a.koderendemen) oerCPO
			FROM pmn_rendemendtall a WHERE kodelist='302' AND kodebarang='40000001' ORDER BY tgl DESC";
		//echo $sql;
		$res=mysql_query($sql);
		while($bar=mysql_fetch_object($res)){
			if($num % 2 == 0){ 
				$color = "#84B4DF";
			} 
			else{ 
				$color = "#FFFFFF";
			}
			echo "<tr style='background:". $color .";'><td align=center>". $bar->org ."</td><td align='center'>". $bar->tgl ."</td><td align='center'>". number_format($bar->amount, 2) ."</td>
					<td  align='center'>". number_format($bar->oerCPO, 2) ."% </td><td align='center'>
					<img onclick=previewExcelAll('".$bar->koderendemen."',event) src=images/excel.jpg class=resicon title='MS.Excel'>
					<img src='images/pdf.jpg' class='resicon' title='PDF' onclick=previewBastAll('".$bar->koderendemen."',event)>
					</td></tr> ";
			$num++;
			$tonase = $tonase + $bar->totalkg;
			$kontraktot = $kontraktot + $bar->tot_tgh;
		} 	
	//echo $cpoPrice .'/'.$ppncpo.'.';
?>
	 </tbody>
	 </table>
	 </div>
<?php	
	CLOE_BOX();
    echo close_body();
?>

