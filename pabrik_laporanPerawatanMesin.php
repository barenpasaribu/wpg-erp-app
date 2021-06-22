<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['pemeliharaanMesinReport']).'</b>');
echo "<!--<script type=\"text/javascript\" src=\"js/log_2keluarmasukbrg.js\" /></script>\r\n-->\r\n<script type=\"text/javascript\" src=\"js/pabrik_laporanPerawatanMesin.js\"></script>\r\n<div id=\"action_list\">\r\n";
$sOrg = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optPabrik .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$str = 'select distinct periode from '.$dbname.'.log_5saldobulanan order by periode desc';
$res = mysql_query($str);
$optper = "<option value='0'>All</option>";
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
echo "<table>\r\n     <tr valign=middle>\r\n                 <td><fieldset><legend>".$_SESSION['lang']['pilihdata'].'</legend>';
echo $_SESSION['lang']['pabrik'].":<select id=pbrkId name=pbrkId style=width:150px onchange=getStation(0,0)><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optPabrik.'</select>&nbsp;';
echo $_SESSION['lang']['statasiun'].":<select id=statId name=statId style=width:150px ><option value=''>".$_SESSION['lang']['pilihdata'].'</option></select>&nbsp;';
echo $_SESSION['lang']['periode'].':<select id=period name=period>'.$optper.'</select>';
echo '<button class=mybutton onclick=save_pil()>'.$_SESSION['lang']['save']."</button>\r\n                             <button class=mybutton onclick=ganti_pil()>".$_SESSION['lang']['ganti'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n         </table> </div>\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "<div id=\"cari_barang\" name=\"cari_barang\">\r\n<!--<fieldset>\r\n<legend>";
echo 'Other&nbsp;'.$_SESSION['lang']['data'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr><td>";
echo $_SESSION['lang']['mesin'];
echo ' : <select id="msnId" name="msnId" style="width:150px" onChange="getDataMsn()"></select>&nbsp;';
echo $_SESSION['lang']['nm_brg'];
echo " : <input type=\"text\" id=\"nm_goods\" name=\"nm_goods\" maxlength=\"35\" onKeyPress=\"return tanpa_kutip(event)\" onClick=\"cari_brng('";
echo $_SESSION['lang']['findBrg'];
echo "','<fieldset><legend>";
echo $_SESSION['lang']['findnoBrg'];
echo "</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>','',event)\" /><input type=\"hidden\" id=\"kd_br\" name=\"kd_br\" />  </td></tr>\r\n</table>\r\n</fieldset>\r\n-->\r\n<img onclick=dataKeExcel(event,'pabrikPemeliharaanMesinExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n<img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>\r\n<div id=\"hasil_cari\" name=\"hasil_cari\" style=\"display:none\">\r\n    <fieldset>\r\n    <legend>";
echo $_SESSION['lang']['result'];
echo "</legend>\r\n     <div id=\"contain\">\r\n\r\n    </div>\r\n    </fieldset>\r\n    </div>\r\n</div>\r\n";
CLOSE_BOX();
echo close_body();

?>