<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
/*$opt3='';
for($z=0;$z<=36;$z++)
{
	$da=mktime(0,0,0,date('m')-$z,date('d'),date('Y'));
	$opt3.="<option value='".date('Y-m',$da)."'>".date('m-Y',$da)."</option>";
}*/
//rubah jadi 12 bulan ==Jo 23-02-2017==
$todays=intval(date('m'));
if ($todays>1){
	$starts=($todays-1)*-1;
	$kurang=($todays-1);
}
else {
	$starts=0;
	$kurang=0;
} 	
$opt3="";
for($x=$starts;$x<12-$kurang;$x++)
{
	$dt=mktime(0,0,0,(date('m')+$x),15,date('Y'));
	
	$opt3.="<option value='".date('Y-m',$dt)."' >".date('m-Y',$dt)."</option>";
}
/*
$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str,$conn);
//default
$karyawan=0.02;
$perusahaan=4.54;
while($bar=mysql_fetch_object($res))
{
	if($bar->id=='karyawan')
	{
		$karyawan=$bar->value/100;
	}
	else
	{
		$perusahaan=$bar->value/100;
	}
}
*/
	OPEN_BOX_HRD('','<b>'.$_SESSION['lang']['lapbpjskes'].'</b>');
		echo"<div id=EList>";
		//echo OPEN_THEME_HRD('JAMSOSTEK:');
        echo"<br>
		      ".$_SESSION['lang']['periode'].":<select id=bln name=bln onchange=getBPJSKesVal(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>
			 ";

		echo"<hr><br>".$_SESSION['lang']['lapbpjskes']." ".$_SESSION['lang']['periode'].":<b><span id=caption>".$bln."</span></b>
			  <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertBPJSKesExcel()>
			  <div style='display:none;'>
			  <iframe id=ifrm></iframe>
			  </div>
			  "; 	  		     
		echo"<table class=sortable width=1000px border=0 cellspacing=1>
		      <thead>
			  <tr class=rowheader>
			    <td align=center>No.</td>
				<td align=center>".$_SESSION['lang']['nik']."</td>
				<td align=center width=250>".$_SESSION['lang']['employeename']."</td>
				<td align=center>".$_SESSION['lang']['tgldaftar']."&nbsp;".$_SESSION['lang']['bpjskes']."</td>
				<td align=center>".$_SESSION['lang']['kelasbpjskes']."</td>
				<td align=center width=110>No. ".$_SESSION['lang']['bpjskes']."</td>
				<td align=center>".$_SESSION['lang']['periodegaji']."</td>
				<td align=center>BPJS Beban Karyawan<br>(Rp.)</td>
				<td align=center>BPJS Beban Perusahaan<br>(Rp.)</td>
				<td align=center>".$_SESSION['lang']['total']."<br>(Rp.)</td>
			  </tr> 
			  </thead>
			  <tbody id=tbody>";
		
		echo"</tbody>
			  <tfoot></tfoot>
			    <tr class=rowcontent>
		      </table>";  	  			 
		echo"</div>";
		//echo CLOSE_THEME_HRD();		
	CLOSE_BOX_HRD();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body_hrd();
?>