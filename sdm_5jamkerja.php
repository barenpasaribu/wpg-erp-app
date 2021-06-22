<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body_hrd();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_5jamkerja.js'></script>
<?

$arr="##tanggal##jammasuk##jamkeluar##keterangan##proses";
include('master_mainMenu.php');
OPEN_BOX_HRD();
echo"<fieldset style=width:350px>
     <legend>".$_SESSION['lang']['harga']." ".$_SESSION['lang']['tbs']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tanggal']."</td>
	   <td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\" /></td>
	 </tr>
         <tr>
	   <td>".$_SESSION['lang']['jammasuk']."</td>
	   <td><input type=text class=myinputtext id=jammasuk style=\"width:150px;\" maxlength=5 /> </td>
	 </tr>	
         <tr>
	   <td>".$_SESSION['lang']['jamkeluar']."</td>
	   <td><input type=text class=myinputtext id=jamkeluar style=\"width:150px;\" maxlength=5 /> </td>
	 </tr>	
         <tr>
	   <td>".$_SESSION['lang']['keterangan']."</td>
	   <td><input type=text class=myinputtext id=keterangan style=\"width:150px;\" maxlength=5 /> </td>
	 </tr>	
	 </table>
	 <input type=hidden value=insert id=proses>
	 <button class=mybutton onclick=saveFranco('pmn_slave_hargaTbs','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX_HRD();
OPEN_BOX_HRD();

echo"<fieldset  style=width:650px><legend>".$_SESSION['lang']['list']."</legend>";
echo"<table cellpadding=1 cellspacing=1 border=0><tr><td>".$_SESSION['lang']['tanggal']." : <input type=text class=myinputtext id=caritanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10  />";
echo"
    <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['tanggal']."</td>
	   <td>".$_SESSION['lang']['jammasuk']."</td>
	   <td>".$_SESSION['lang']['jamkeluar']."</td>
	   <td>".$_SESSION['lang']['keterangan']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX_HRD();
echo close_body_hrd();
?>