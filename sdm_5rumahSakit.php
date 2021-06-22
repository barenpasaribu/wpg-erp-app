<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script  language=javascript1.2 src=js/sdm_rumahsakit.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('');
echo '<div id=EList>';
echo OPEN_THEME($_SESSION['lang']['rumahsakit']." Form: <a id=label style='color:#FFFFFF;'>New</a>");
echo "<fieldset><legend>Input Form</legend>\r\n\t\t       <br>\r\n\t\t\t   <table>\r\n\t\t\t      <tr><td>".$_SESSION['lang']['namars']."</td><td><input type=hidden id=hosid value=''>\r\n\t\t\t\t  <input type=hidden id=update value=''><input type=text class=myinputtext id=hosname size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>\r\n\t\t\t\t  <tr><td>".$_SESSION['lang']['alamat']."</td><td><input type=text class=myinputtext id=hosadd size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>\r\n\t\t\t\t  <tr><td>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=hoscity size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>\r\n\t\t\t      <tr><td>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=hosphone size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>\r\n\t\t\t\t  <tr><td>".$_SESSION['lang']['email']."</td><td><input type=text class=myinputtext id=hosmail size=25 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td></tr>\r\n\t\t\t      <tr><td>".$_SESSION['lang']['status']."</td><td><select id=status><option value=1>Active</option><option value=0>Black List</option></select></td></tr>\r\n\t\t\t   </table>\r\n\t\t\t   <br>\r\n\t\t\t   <button class=mybutton onclick=saveHospital()>".$_SESSION['lang']['save']."</button>\r\n\t\t\t   <button class=mybutton onclick=cancelHospital()>".$_SESSION['lang']['new']."</button>\r\n\t\t\t   </fieldset>";
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
OPEN_BOX('', $_SESSION['lang']['list']);
echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n\t        <thead>\r\n\t          <tr class=rowheader><td>No.</td>\r\n\t\t\t      <td align=center>".$_SESSION['lang']['namars']."</td>\r\n\t\t\t\t  <td align=center>".$_SESSION['lang']['alamat']."</td>\r\n\t\t\t\t  <td align=center>".$_SESSION['lang']['kota']."</td>\r\n\t\t\t\t  <td align=center>".$_SESSION['lang']['telp']."</td> \r\n\t\t\t\t  <td align=center>".$_SESSION['lang']['email']."</td>\r\n\t\t\t\t  <td align=center>".$_SESSION['lang']['status']."</td>\r\n\t\t\t\t  <td align=center>Edit/Del</td>\r\n\t\t\t  </tr>\r\n\t\t\t</thead><tbody id=tbody>";
$str = "select *,case status when 1 then 'Active' when 0 then\t 'Black List' end as xstatus from ".$dbname.'.sdm_5rs order by namars';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr class=rowcontent><td>'.$no."</td>\r\n\t      <td>".$bar->namars."</td>\r\n\t\t  <td>".$bar->alamat."</td>\r\n\t\t  <td>".$bar->kota."</td>\r\n\t\t  <td>".$bar->telp."</td> \r\n\t\t  <td>".$bar->email."</td>\r\n\t\t  <td>".$bar->xstatus."</td>\r\n\t\t  <td align=center>\r\n\t\t   <img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editHospital('".$bar->id."','".$bar->namars."','".$bar->kota."','".$bar->alamat."','".$bar->telp."','".$bar->email."','".$bar->status."')\">\r\n\t\t  <img src=images/close.png class=dellicon title=delete height=11px onclick=\"deleteHospital('".$bar->id."');\">\r\n         </td>\r\n\t  </tr>";
}
echo '</tbody><tfoot></tfoot></table>';
CLOSE_BOX();
echo close_body();

?>