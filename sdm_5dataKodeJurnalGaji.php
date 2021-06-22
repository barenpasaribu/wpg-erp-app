<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>
<script language=javascript1.2 src=js/sdm_payrollFin.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
//list employee
$opt="<option value='01'>01</option>";
$opt.="<option value='02'>02</option>";
$opt.="<option value='03'>03</option>";
$opt.="<option value='04'>04</option>";
$opt.="<option value='05'>05</option>";
$opt.="<option value='06'>06</option>";
$opt.="<option value='07'>07</option>";
$opt.="<option value='08'>08</option>";
$opt.="<option value='09'>09</option>";
$opt.="<option value='10'>10</option>";	
$opt.="<option value='11'>11</option>";	
$opt.="<option value='12'>12</option>";	

for($x=-1;$x<=50;$x++)
{
	$opt1.="<option value='".(date('Y')-$x)."'>".(date('Y')-$x)."</option>";	
}


	OPEN_BOX_HRD('','<b>'.$_SESSION['lang']['akunjurnalgaji'].'</b>');
		echo"<center><div>";
         //echo OPEN_THEME_HRD('<font color=white>'.$_SESSION['lang']['akunbanknote'].':</font>');
		 echo"<fieldset>
		         <legend>
				 <img src=images/info.png align=left height=35px valign=asmiddle>
				 </legend>
				 			 
		      </fieldset>";
			  
		 $prestr="select id,name,jurnalcode from ".$dbname.".sdm_ho_component";
		 $preres=mysql_query($prestr,$conn);	
		echo"<table class=sortable cellspacing=1 border=5>
		     <thead>
			   <tr class=rowheader style='text-align:center; vertical-align:middle;'>
			    <td >".$_SESSION['lang']['pilih']."</td>
			    <td>No.</td>
			    <td>".$_SESSION['lang']['id']."</td>
				<td>".$_SESSION['lang']['nama']."</td>
				<td>".$_SESSION['lang']['kodejurnal']."</td>
				
				<td>".$_SESSION['lang']['save']."</td>
				</tr>
			 </thead>
			 <tbody id=tablebody>
			 ";
			 $no=0;
			 
		 while($bar=mysql_fetch_object($preres))
		 {
		 	$no+=1;
			if($bar->jurnalcode=='' or $bar->jurnalcode==''){
			 $stat='';
			 $ch ='checked';
			}
			else{
			 $stat='disabled'; 
			 $ch='';
			}
		
			echo"<tr class=rowcontent id=row".$no.">
			     <td><input type=checkbox id=check".$no." ".$ch." ".$stat."  onclick=vLine(this,'".$no."')></td>
			     <td class=firsttd>".$no."</td>
				 <td id=ids".$no.">".$bar->id."</td>
				 <input type=hidden id=idx".$no." value=".$bar->id."></input>
				 <td id=name".$no.">".$bar->name."</td>
				 <td><input type=text class=myinputtext id=kdjrn".$no." value='".$bar->jurnalcode."' ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>
				 <td><button ".$stat." class=mybutton id=butt".$no." style='padding:0px;' title='Save this line' ".$stat." onclick=saOneLine('".$no."')><img src='images/save.png' height=12px></button></td>
				 </tr>";
		 }	 
	    echo"</tbody>
		     <tfoot>
			 </tfoot>
			 </table>
			 <center><button class=mybutton onclick=saveAll('".$no."')>".$_SESSION['lang']['saveallchecked']."</button></center>
			 ";  
		 //echo CLOSE_THEME_HRD('');
		echo"</div></center>";
	CLOSE_BOX_HRD();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body_hrd();
?>