<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body_hrd();
?>

<script language='javascript' src='js/keu_5bank.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('');

echo"<fieldset style='width:500px;'><table>
	 <tr><td>".$_SESSION['lang']['kodebank']."</td><td><input type=text id=grup maxlength=80 style=width:150px onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     <tr><td>".$_SESSION['lang']['namabank']."</td><td><input type=text id=jumlahhk maxlength=80 style=width:150px onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['sandibank']."</td><td><input type=text id=sandibank maxlength=80 style=width:150px onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
//echo open_theme('');
echo "<div>";
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['noakun']."</td>
			<td>".$_SESSION['lang']['namaakun']."</td>
			<td>".$_SESSION['lang']['sandibank']."</td>
			
			<td style='width:30px;'>".$_SESSION['lang']['action']."</td></tr>
		 </thead>
		 <tbody id=container>"; 
                echo"<script>loadData()</script>";
		echo" </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
//echo close_theme();
CLOSE_BOX_HRD();
echo close_body_hrd();
?>