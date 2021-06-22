<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_5jenis_prasarana.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['jnsPrasarana']);
$optKlmpk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sKlmpk = 'select distinct * from '.$dbname.'.sdm_5kl_prasarana order by kode asc';
$qKlmpk = mysql_query($sKlmpk);
while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
    $orgNmKlmpk[$rKlmpk['kode']] = $rKlmpk['nama'];
    $optKlmpk .= "<option value='".$rKlmpk['kode']."'>".$rKlmpk['nama'].'</option>';
}
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodekelompok'].'</td><td><select id=idKlmpk>'.$optKlmpk."</select></td></tr>\r\n         <tr><td>".$_SESSION['lang']['jenis']."</td><td><input type=text id=kodejabatan size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['namajenisvhc']."</td><td><input type=text id=namajabatan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n             <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=20 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n     </table>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['list'].' '.$_SESSION['lang']['jnsPrasarana']);
echo '<div id=container>';
$str1 = 'select * from '.$dbname.'.sdm_5jenis_prasarana order by nama';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td>".$_SESSION['lang']['namakelompok'].'</td><td>'.$_SESSION['lang']['jenis'].'</td><td>'.$_SESSION['lang']['namajenisvhc'].'</td><td>'.$_SESSION['lang']['satuan']."</td><td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n                    <td align=center>".$orgNmKlmpk[$bar1->kelompok]."</td>\r\n                    <td>".$bar1->jenis."</td>\r\n                    <td>".$bar1->nama."</td>\r\n                    <td>".$bar1->satuan."</td>\r\n                        \r\n                    <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kelompok."','".$bar1->jenis."','".$bar1->satuan."','".$bar1->nama."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>