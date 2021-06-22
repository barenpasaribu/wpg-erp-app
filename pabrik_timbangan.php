<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/pabrik_timbangan.js></script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n";
OPEN_BOX('', '<b>Timbangan</b>');
$sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where `kelompokbarang` like '400%'";
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';
}
$sOrg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optPt .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$arrStat = ['On', 'Off'];
foreach ($arrStat as $isi => $tks) {
    $optStatTimb .= '<option value='.$isi.'>'.$tks.'</option>';
}
$frm[0] .= "\r\n\r\n<fieldset>\r\n          <legend>".$_SESSION['lang']['form']."</legend>\r\n          <fieldset>\r\n                        <legend>".$_SESSION['lang']['pilihdata']."</legend>\r\n                <table>\r\n            <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['namabarang']."</td><td>\r\n                <select id=kdBrg name=kdBrg onchange=\"getForm()\" style=\"width:150px;\"><option value=></option>".$optBrg."</select></td>\r\n          </tr>\r\n          </table>\r\n          </fieldset>\r\n          <fieldset>\r\n                <legend>".$_SESSION['lang']['result']."</legend>\r\n                <table cellspacing=1 border=0>\r\n                        <tr>\r\n                        <td id='content'>\r\n                        </td>\r\n                        </tr>\r\n                        <tr>\r\n                        <td>nb. Weiging default is TON</td></tr>\r\n                </table>\r\n          </fieldset>\r\n          <br />\r\n         </fieldset>";
$frm[1] = "<fieldset>\r\n           <legend>".$_SESSION['lang']['list']."</legend>\r\n          <fieldset><legend></legend>\r\n          ".$_SESSION['lang']['notransaksi']."\r\n          <input type=text id=txtnotransaksi size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n          <button class=mybutton onclick=cariNotansaksi()>".$_SESSION['lang']['find']."</button>\r\n          </fieldset>\r\n          <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n          <tr class=rowheader>\r\n          <td>No.</td>\r\n          <td>".$_SESSION['lang']['notransaksi']."</td>\r\n          <td>".$_SESSION['lang']['tanggal']."</td>\r\n          <td>".$_SESSION['lang']['kodebarang']."</td>\r\n          <td>".$_SESSION['lang']['namabarang']."</td>\r\n          <td>".$_SESSION['lang']['jammasuk']."</td>\r\n          <td>".$_SESSION['lang']['jamkeluar']."</td>\r\n          <td>Action</td>\r\n          </tr>\r\n          </head>\r\n           <tbody id=containerlist>\r\n           <script>\r\n           loadNewData();\r\n           </script>\r\n           </tbody>\r\n           <tfoot>\r\n           </tfoot>\r\n           </table>\r\n         </fieldset>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>