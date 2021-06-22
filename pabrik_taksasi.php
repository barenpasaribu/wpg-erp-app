<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/pabrik_taksasi.js'></script> \r\n<link rel=stylesheet type='text/css' href='style/zTable.css'>\r\n";
OPEN_BOX('', '<b>Taksasi Panen</b>');
echo "<div><table align='center'><tr>";
echo "<td style='min-width:100px' v-align='middle'><img class=delliconBig src=images/skyblue/addbig.png title='".$_SESSION['lang']['new']."' onclick='showAdd()'><br>".$_SESSION['lang']['new'].'</td>';
echo "<td style='min-width:100px' v-align='middle'><img class=delliconBig src=images/skyblue/list.png title='".$_SESSION['lang']['list']."' onclick='loadData(0)'><br>".$_SESSION['lang']['list'].'</td>';
echo "<td style='min-width:100px' v-align='middle'><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['tanggal'].' <input id="sNoTrans" name="sNoTrans" class="myinputtext" onkeypress="return tanpa_kutip(event)"  style="width:150px" readonly="readonly" onmousemove="setCalendar(this.id)" type="text">';
echo '<button onclick="cariData(0)" class="mybutton" name="sFind" id="sFind">'.$_SESSION['lang']['find'].'</button>';
echo '</legend></fieldset></td></tr></table></div>';

$arr = '##tanggal##customer##proses##kg';
echo "<input type=hidden id=proses value=insert /><div id=formData style='display:none'>";

echo "<fieldset style='float:left'><legend><b>".$_SESSION['lang']['form'].'</b></legend>';
echo "<table border=0 style='float:left;'><tr>";
echo '<td>'.$_SESSION['lang']['tanggal'].'</td>';
echo "<td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\">\r\n     </td></tr><tr>";
echo '<td>'.$_SESSION['lang']['nmcust'].'</td>';
echo "<td><select id='customer' style=\"width:150px\"><option value=''></option>";
$sorg = 'select distinct kodetimbangan,namasupplier from '.$dbname.".log_5supplier where kodetimbangan like 'TBS%' order by namasupplier asc";
$qorg = mysql_query($sorg);
while ($rorg = mysql_fetch_assoc($qorg)) {
    echo "<option value='".$rorg['kodetimbangan']."'>".$rorg['namasupplier'].' ('.$rorg['kodetimbangan'].')</option>';
}
echo '</select></td></tr><tr>';
echo '<td>'.$_SESSION['lang']['kg'].'</td>';
echo "<td><input id=\"kg\" name=\"kg\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" maxlength=45 type=\"text\">\r\n     </td></tr><tr>";
echo "<td><button id=\"addHead\" name=\"addHead\" class=\"mybutton\" onclick=\"saveData('pabrik_slave_taksasi','".$arr."')\">".$_SESSION['lang']['save'].'</button></td>';
echo '</tr></table></fieldset>';

echo '</div><div id=dataList>';
echo "<fieldset style='clear:left'><legend><b>".$_SESSION['lang']['list'].'</b></legend>';
echo '<div id=container><script>loadData(0);</script></div></fieldset>';
echo '</div>';
CLOSE_BOX();
echo close_body();
?>