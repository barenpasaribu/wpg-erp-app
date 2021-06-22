<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['medicalid'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\nfild='";
echo $_SESSION['lang']['pilihdata'];
echo "';\r\n</script>\r\n<script language=\"javascript\" src=\"js/sdm_idpengobatan.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n";
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKary = $optUnit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sunit = 'select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."'";
$qunit = mysql_query($sunit);
while ($runit = mysql_fetch_assoc($qunit)) {
    $optUnit .= "<option value='".$runit['kodeunit']."'>".$optNmOrg[$runit['kodeunit']].'</option>';
}
$formDt .= '<fieldset style=float:left><legend>'.$_SESSION['lang']['form'].'</legend>';
$formDt .= '<table>';
$formDt .= '<tr><td>'.$_SESSION['lang']['unit'].'</td><td>:</td>';
$formDt .= "<td><select id=kdUnit style=width:150px onchange=getKary('','')>".$optUnit.'</select></td></tr>';
$formDt .= '<tr><td>'.$_SESSION['lang']['namakaryawan'].'</td><td>:</td>';
$formDt .= '<td><select id=karyawanId style=width:150px>'.$optKary.'</select></td></tr>';
$formDt .= '<tr><td>'.$_SESSION['lang']['medicalId'].'</td><td>:</td>';
$formDt .= "<td><input type=text id=medicalId style=width:150px class=myinputtext onkeypress='return tanpa_kutip(event)' /></td></tr>";
$formDt .= '<tr><td colspan=3><button class=mybutton onclick=simpanDt()>'.$_SESSION['lang']['save']."</button>\r\n<button class=mybutton onclick=batalDt()>".$_SESSION['lang']['cancel']."</button>    \r\n</td></tr>";
$formDt .= '</table>';
$formDt .= '</fieldset>';
echo $formDt;
CLOSE_BOX();
OPEN_BOX('', '');
$lstData .= '<fieldset style=float:left><legend>'.$_SESSION['lang']['data'].'</legend>';
$lstData .= '<fieldset><legend>'.$_SESSION['lang']['form'].'</legend>';
$lstData .= '<table>';
$lstData .= '<tr><td>'.$_SESSION['lang']['nik']."</td><td><input type=text id='nikcar' class=myinputtext style=width:150px /></td></tr>";
$lstData .= '<tr><td>'.$_SESSION['lang']['namakaryawan']."</td><td><input type=text id='namacar'  class=myinputtext style=width:150px /></td></tr>";
$lstData .= "</table>\r\n           <button class=mybutton onclick=loadData(0)>".$_SESSION['lang']['find']."</button>\r\n           </fieldset>";
$lstData .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
$lstData .= '<tr class=rowheader>';
$lstData .= '<td>'.$_SESSION['lang']['nik'].'</td>';
$lstData .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
$lstData .= '<td>'.$_SESSION['lang']['medicalId']."</td>\r\n           <td>".$_SESSION['lang']['action']."</td>\r\n           </tr></thead><tbody id=containData>\r\n           <script>loadData()</script>";
$lstData .= '</tbody>';
$lstData .= "</tbody><tfoot id=dtKaki>\r\n\t\t";
$lstData .= '</table></fieldset>';
$lstData .= "<div id=showForm style=display:none>\r\n             <fieldset style=float:left><legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['keluarga']."</legend>\r\n             <div id=container>\r\n             </div>\r\n             ";
$lstData .= '</fieldset></div>';
echo $lstData;
CLOSE_BOX();
echo close_body();
echo "\r\n";

?>