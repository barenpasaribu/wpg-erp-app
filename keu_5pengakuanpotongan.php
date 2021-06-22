<?php



require_once 'config/connection.php';
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/keu_5pengakuanHutang.js'></script>\r\n";
include 'master_mainMenu.php';
$optKomponen = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sAkun = 'select  id,name from '.$dbname.'.sdm_ho_component where plus=0 order by name';
$qAkun = mysql_query($sAkun);
while ($rAkun = mysql_fetch_assoc($qAkun)) {
    $optKomponen .= "<option value='".$rAkun['id']."'>".$rAkun['name'].'</option>';
}
if ('EN' === $_SESSION['language']) {
    OPEN_BOX('', 'Salary deduction - Journal mapping');
    $zz = 'namaakun1 as namaakun';
} else {
    OPEN_BOX('', 'Mapping Potongan Karyawan');
    $zz = 'namaakun';
}

$optAkun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sAkun = 'select  noakun,'.$zz.' from '.$dbname.'.keu_5akun where length(noakun)=7 order by noakun asc';
$qAkun = mysql_query($sAkun);
while ($rAkun = mysql_fetch_assoc($qAkun)) {
    $optAkun .= "<option value='".$rAkun['noakun']."'>".$rAkun['noakun'].' - '.$rAkun['namaakun'].'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n          <tr><td>Component ".$_SESSION['lang']['potongan'].'</td><td><select id=potongan style=width:150px>'.$optKomponen."</select></td></tr>\r\n          <tr><td>".$_SESSION['lang']['debet'].'</td><td><select id=debet style=width:150px>'.$optAkun."</select></td></tr>\r\n          <tr><td>".$_SESSION['lang']['kredit'].'</td><td><select id=kredit style=width:150px>'.$optAkun."</select></td></tr>        \r\n         </table>\r\n         <input type=hidden id=method value='insert'>\r\n         <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>\r\n         <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>\r\n         </fieldset>";
echo "<div>\r\n        ".$_SESSION['lang']['keteranganjrnlpotongan']." \r\n        <table class=sortable cellspacing=1 border=0>\r\n             <thead>\r\n                 <tr class=rowheader>\r\n                    <td>Component ID</td>                 \r\n                    <td>Component Name</td>\r\n                    <td>".$_SESSION['lang']['debet']."</td>\r\n                    <td>".$_SESSION['lang']['kredit']."</td>                     \r\n                    <td style='width:30px;'>*</td></tr>\r\n                 </thead>\r\n                 <tbody id=container>";
echo "<script>loadData()</script> </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\r\n                 </table></div>";
CLOSE_BOX();
echo close_body();

?>