<?



require_once('master_validation.php');

include('lib/nangkoelib.php');

include_once('lib/zLib.php');

echo open_body();

include('master_mainMenu.php');

?>

<script language="javascript" src="js/zTools.js"></script>

<script language="javascript" src="js/zReport.js"></script>

<script language="javascript">

function loadjamDetail(kodevhc,tanggal,ev)

{

//    showById('printPanel');

   param='kodevhc='+kodevhc+'&tanggal='+tanggal;

   tujuan='vhc_slave_getDetailJam.php'+"?"+param;  

   width='700';

   height='360';

  

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1('Detail Activity',content,width,height,ev); 



}

function qwe()

{

    showById('printPanel');

    zPreview('vhc_slave_getLaporanJamKerja','##tgl1##tgl2##kodetraksi','printContainer');

}

function qweKeExcel(ev,tujuan)

{

	tgl1 =document.getElementById('tgl1');

	tgl2 =document.getElementById('tgl2');

	kodetraksi =document.getElementById('kodetraksi');

        tgl1V	=tgl1.value;

        tgl2V	=tgl2.value;

        kodetraksiV =kodetraksi.options[kodetraksi.selectedIndex].value;



	param='apa=excel'+'&tgl1='+tgl1V+'&tgl2='+tgl2V+'&kodetraksi='+kodetraksiV;

//alert(param);                

                

	judul='Report Ms.Excel';	

	printFile(param,tujuan,judul,ev)	

}

function qweKePDF(ev,tujuan)

{

	tgl1 =document.getElementById('tgl1');

	tgl2 =document.getElementById('tgl2');

	kodetraksi =document.getElementById('kodetraksi');

        tgl1V	=tgl1.value;

        tgl2V	=tgl2.value;

        kodetraksiV =kodetraksi.options[kodetraksi.selectedIndex].value;



	param='apa=pdf'+'&tgl1='+tgl1V+'&tgl2='+tgl2V+'&kodetraksi='+kodetraksiV;

//alert(param);                

                

	judul='Report Ms.Excel';	

	printFile(param,tujuan,judul,ev)	

}

function printFile(param,tujuan,title,ev)

{

   tujuan=tujuan+"?"+param;  

   width='900';

   height='360';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev); 	

}



</script>    

<?



OPEN_BOX(''); 

//ambil tanggal traksi

$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_runht

      order by periode desc limit 24";

$res=mysql_query($str);

#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";

$optper="";

while($bar=mysql_fetch_object($res))

{

	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";

}

$str="select distinct kodetraksi from ".$dbname.".vhc_5master

      where left(kodetraksi,3)='".$_SESSION['empl']['kodeorganisasi']."' order by kodetraksi ";

$res=mysql_query($str);

#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";

$opttrx="";

while($bar=mysql_fetch_object($res))

{

	$opttrx.="<option value='".$bar->kodetraksi."'>".$bar->kodetraksi."</option>";

}



?>

<fieldset>

<legend><b><?php echo $_SESSION['lang']['jmljamkerja']?></b></legend>

<table cellspacing="1" border="0" >

<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input type="text" class="myinputtext" onchange=hideById('printPanel') id="tgl1" name="tgl1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" onchange=hideById('printPanel') id="tgl2" name="tgl2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['kodetraksi']?></label></td><td><select id=kodetraksi style='width:200px;' onchange=hideById('printPanel')><?php echo $opttrx; ?></select></td></tr>



<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->

<tr height="20"><td colspan="2"><?php echo "<button class=mybutton onclick=qwe()>".$_SESSION['lang']['proses']."</button>"; ?></td></tr>

</table>

</fieldset>

<?



echo"<fieldset style=\"clear: both;\"><legend><b>Print Area</b></legend>

<span id=printPanel style='display:none;'>

     <img onclick=qweKeExcel(event,'vhc_slave_getLaporanJamKerja.php') src=images/excel.jpg class=resicon title='MS.Excel'> 

	 <img onclick=qweKePDF(event,'vhc_slave_getLaporanJamKerja.php') title='PDF' class=resicon src=images/pdf.jpg>

	 </span><div id='printContainer' style=\"overflow: auto; height: 340px; max-width: 1220px;\">



</div></fieldset>";

CLOSE_BOX();

?>

<?php

echo close_body();

?>