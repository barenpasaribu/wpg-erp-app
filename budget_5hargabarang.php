<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
echo "<script language=javascript1.2 src='js/budget_5hargabarang.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['budget'].' '.$_SESSION['lang']['material']);
echo "<table>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>\r\n\t   <img class=delliconBig src=images/user_add.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n     </tr>\r\n     </table>";
CLOSE_BOX();
OPEN_BOX('', '');
$optpt = '';
$str = 'select distinct kodeorg from '.$dbname.".log_5masterbarangdt \r\n      order by kodeorg";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorg."'>".$bar->kodeorg.'</option>';
}
$optkl = '';
$str = 'select kode, kelompok from '.$dbname.".log_5klbarang \r\n      order by kode";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optkl .= "<option value='".$bar->kode."'>".$bar->kode.' - '.$bar->kelompok.'</option>';
}
$optreg = '';
$str = 'select regional, nama from '.$dbname.".bgt_regional \r\n      order by regional";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optreg .= "<option value='".$bar->regional."'>".$bar->regional.' - '.$bar->nama.'</option>';
}
echo "<div id='frminput' style='display:none;'><fieldset><legend id=legendinput name=legendinput>New</legend><table><tr>";
echo '<tr><td>'.$_SESSION['lang']['budgetyear'].'</td><td><input onkeyup="resetcontainer();" type=text id=tahunbudget size=4 maxlength=4 class=myinputtext onkeypress="return angka_doang(event);"></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['regional']."</td><td><select onchange=\"resetcontainer();\" id=regional style='width:150px'><option value=''>".$optreg.'</select></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['sumberHarga']."</td><td><select onchange=\"resetcontainer();\" id=sumberharga style='width:150px'><option value=''></option>".$optreg.'</select></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['kelompokbarang']."</td><td><select onchange=\"resetcontainer();\" id=kelompokbarang style='width:150px'><option value=''>".$optkl.'</select></td></tr>';
echo '<tr><td></td><td><button id= buttonproses class=mybutton onclick=tampolHarga()>'.$_SESSION['lang']['proses']."</button>\r\n        <input type=\"hidden\" id=\"hiddenprocess\" name=\"hiddenprocess\" value=\"\" />\r\n        </td></tr></table>";
echo "</fieldset><span id=printPanel style='display:none;'>\r\n     </span>    \r\n     <div id=container style='width:100%;height:50%;overflow:scroll;'>\r\n     </div></div><div id='frmlist' style='display:none;'>";
echo "<fieldset style='float:left;'><legend>".$_SESSION['lang']['list'].'</legend>';
echo "<table class=sortable cellspacing=1 border=0>\t     \r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['regional'].'</td>';
echo '<td align=center>'.$_SESSION['lang']['list']."</td>\r\n            <td align=center>".$_SESSION['lang']['delete']."</td>\r\n            <td align=center>Excel</td>\r\n            <td align=center>".$_SESSION['lang']['edit']."</td>\r\n            <td align=center>".$_SESSION['lang']['close']."</td>\r\n\t</tr>\r\n     </thead>\r\n     <tbody id=container3>";
echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
echo 'Klik <b>'.$_SESSION['lang']['list'].'</b> untuk <b>'.$_SESSION['lang']['close'].'</b>';
echo '</fieldset>';
echo "<fieldset style='float:left;'><legend>".$_SESSION['lang']['input'].'</legend>';
echo '<table><tr>';
echo '<tr><td>'.$_SESSION['lang']['budgetyear'].'</td><td><input type=text id=tahunbudget1 size=4 maxlength=4 class=myinputtext onkeypress="return angka_doang(event);"></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['regional']."</td><td><select id=regional1 style='width:150px'><option value=''>".$optreg.'</select></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['kodebarang']."</td><td>\r\n        <input type=text class=myinputtext id=kodebarang1 name=kodebarang1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px;/>\r\n        <input type=\"image\" id=search1 src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg value='.$kodebarang1.'><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=containerq></div><input type=hidden id=nomor name=nomor value='.$key."><input type=hidden id=regbrg value=0 /><input type=hidden id=thnbgtbrg  value=0 />',event)\";>\r\n        <label id=namabarang1></label><label id=satuan1></label>\r\n        </td></tr>";
echo '<tr><td>'.$_SESSION['lang']['hargasatuan'].'</td><td><input type=text id=hargasatuan1 size=20 maxlength=10 class=myinputtextnumber onkeypress="return angka_doang(event);"></td></tr>';
echo '<tr><td></td><td><button disabled=true id=buttonedit class=mybutton onclick=editHarga()>'.$_SESSION['lang']['save']."</button>\r\n        <input type=\"hidden\" id=\"hiddenedit\" name=\"hiddenedit\" value=\"\" />\r\n        </td></tr></table>";
echo '</fieldset><br>';
echo "<span id=printPanel2 style='display:none;'>\r\n     </span>    \r\n     <div style='width:100%;height:50%;overflow:scroll;'>\r\n     <table class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['regional']."</td>\r\n      <td align=center>Kode Organisasi</td>      <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>\r\n            <td align=center>".$_SESSION['lang']['varian']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargabudget']."</td>\r\n\t</tr>  \r\n     </thead>\r\n     <tbody id=container2>\r\n     </tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>\r\n     </div>";
echo '</div>';
CLOSE_BOX();
close_body('');
echo "\r\n";

?>