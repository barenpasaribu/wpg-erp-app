<?php

require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="application/javascript" src="js/vhc_postingPekerjaan.js"></script>
<div id="action_list">
<?php
$statDt=array("0"=>$_SESSION['lang']['belumposting'],"1"=>$_SESSION['lang']['posting']);

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optStataData=$optPeriode;
foreach($statDt as $lstData=>$datanya)
{
	$optStataData.="<option value=".$lstData.">".$datanya."</option>";
}
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_runht where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by substr(tanggal,1,7) desc";
$qTgl=mysql_query($sTgl) or die(mysql_error($conn));
while($rTgl=mysql_fetch_assoc($qTgl))
{
	$optPeriode.="<option value=".$rTgl['periode'].">".$rTgl['periode']."</option>";
}
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                        echo"<table cellpadding=1 cellspacing=1 border=0><tr><td>";
			echo $_SESSION['lang']['notransaksi'].":</td><td><input type=text id=txtsearch size=25 maxlength=30 class=myinputtext></td><td>";
                        echo $_SESSION['lang']['kodevhc'].":</td><td><input type=text id=kdvhc   maxlength=30 class=myinputtext></td><td>";
			echo $_SESSION['lang']['tanggal'].":</td><td><input type=text class=myinputtext id=tgl_cari2 onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td><tr><td>";
                        echo $_SESSION['lang']['periode'].":</td><td><select id=tgl_cari style=width:100px;>".$optPeriode."</select></td><td>";
			echo $_SESSION['lang']['status'].":</td><td><select id=statId style=width:100px;>".$optStataData."</select></td><td>&nbsp;</td></tr></table>";
			echo"<button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
//CLOSE_BOX();
?>
<div id="list_ganti">
<?php //OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['listPekerjaan']?></legend>
<div id="contain">
<script>load_data();</script>
</div>
</fieldset>
    <input type="hidden" id="jmlhBaris" value="" />
<?php CLOSE_BOX()?>
</div>
<?php 
echo close_body();
?>