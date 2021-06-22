<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/anggaran_bjr.js'></script>\r\n\r\n\r\n";
include 'master_mainMenu.php';
$sql = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($data = mysql_fetch_assoc($qry)) {
    $optOrg .= '<option value='.$data['kodeorganisasi'].'>'.$data['namaorganisasi'].'</option>';
}
$optthnttp = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optorgclose = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
echo "\r\n\r\n";
OPEN_BOX('', '<b>'.$_SESSION['lang']['bjr'].'</b>');
echo "<br /><br /><fieldset style='float:left;'>\r\n                <legend>".$_SESSION['lang']['entryForm']."</legend> \r\n                        <table border=0 cellpadding=1 cellspacing=1>\r\n                                <tr><td width=100>".$_SESSION['lang']['budgetyear']."<td width=10>:</td></td><td><input type=text id=tahunbudget size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:200px;\"></td></tr>\r\n                                <tr><td>".$_SESSION['lang']['kodeorganisasi'].' <td>:</td></td><td><select id=kodeorg style="width:200px;" >'.$optOrg."</select></td></tr>\r\n                                <tr><td>".$_SESSION['lang']['thntnm']."<td>:</td></td><td><input type=text id=thntanam size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:200px;\"></td></tr>\r\n                                <tr><td>".$_SESSION['lang']['bjr']."<td>:</td></td><td><input type=text id=bjr size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:200px;\"> </td></tr>\r\n\r\n                                <tr><td></td><td></td><br />\r\n                                        <td><br /><button class=mybutton onclick=simpanbjr()>Simpan</button>\r\n                                        <button class=mybutton onclick=cancelbjr()>Hapus</button></td></tr>\r\n                        </table></fieldset>\r\n                                        <input type=hidden id=method value='insert'>\r\n                                        <input type=hidden id=oldtahunbudget value='insert'>\r\n                                        <input type=hidden id=oldkodeorg value='insert'>\r\n                                        <input type=hidden id=oldthntanam value='insert'>";
echo "<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>\r\n    <div id=closetab><table>\r\n                <tr><br /><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><select id=thnttp style='widht:150px'>".$optthnttp."</select></td></tr>\r\n                <tr><td>".$_SESSION['lang']['kodeorganisasi']." </td><td>:</td><td><select id=lkstgs style='widht:150px'>".$optorgclose.'</select></td></tr>';
echo "<tr><td></td><td></td><td><br /><button class=\"mybutton\"  id=\"saveData\" onclick='closebjr()'>".$_SESSION['lang']['tutup'].'</button></td></tr></table>';
echo '</div></fieldset>';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset>';
echo '<legend><b>'.$_SESSION['lang']['datatersimpan'].'</b></legend>';
echo '<div id=container>';
echo "<table class=sortable cellspacing=1 border=0>\r\n             <thead>\r\n                 <tr class=rowheader>\r\n                         <td align=center style='width:5px;'>No</td>\r\n                         <td align=center style='width:100px;'>".$_SESSION['lang']['budgetyear']."</td>\r\n                         <td align=center style='width:125px;'>".$_SESSION['lang']['kodeorganisasi']." </td>\r\n                         <td align=center style='width:100px;'>".$_SESSION['lang']['thntnm']."</td>\r\n                         <td align=center style='width:50px;'>".$_SESSION['lang']['bjr']."</td>\r\n                         <td align=center style='width:20px;'>Edit</td></tr>\r\n                 </thead>\r\n                 <tbody id='containerData'><script>loadData()</script>";
echo "\t \r\n                 </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\r\n                 </table></div>";
echo close_theme();
echo '</fieldset>';
CLOSE_BOX();
echo close_body();

?>