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
<script type="application/javascript" src="js/vhc_postingPenggunaanKomponen.js"></script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                        echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
                        echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
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
<legend><?php echo $_SESSION['lang']['listPenggunaanKomponen']?></legend>
<div id="contain">
<script>load_new_data()</script>
</div>
</fieldset>
<?php CLOSE_BOX()?>
</div>
<?php 
echo close_body();
?>