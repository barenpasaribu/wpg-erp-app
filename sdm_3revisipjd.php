<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_3revisipjd.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['spdinas'].' '.$_SESSION['lang']['koreksi']);
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['form']."</legend>\r\n\t <table>\r\n\t\t<tr>\r\n\t\t   <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t   <td><input type=text class=myinputtext id=notransaksi  value=''>&nbsp;<span id=isiNotrans style='display:none'><input type=text class=myinputtext id=notransaksi2  value=''></span>\r\n                       <button class=mybutton onclick=cariDt()>".$_SESSION['lang']['find']."</button>\r\n\t\t   </td>\t\t   \r\n\t\t</tr>\t\r\n\t </table>\r\n\t <fieldset>\r\n\t    <legend>".$_SESSION['lang']['datatersimpan']."</legend>\r\n\t\t<table class=sortable cellspacing=1>\r\n\t\t<thead>\r\n\t\t <tr>\r\n\t\t    <td>No.</td>\r\n\t\t        <td>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td>".$_SESSION['lang']['jenisbiaya']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['disetujui']."</td>\r\n\t\t</tr>\t\r\n\t\t </thead>\t\r\n\t\t <tbody id=innercontainer>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t</table>\r\n\t\t<button class=mybutton onclick=selesai()>".$_SESSION['lang']['done']."</button>\r\n\t\t<button class=mybutton onclick=batalkan()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>\r\n\t </fieldset>\r\n\t ";
$hfrm[0] = $_SESSION['lang']['form'];
drawTab('FRM', $hfrm, $frm, 100, 900);
CLOSE_BOX();
echo close_body();

?>