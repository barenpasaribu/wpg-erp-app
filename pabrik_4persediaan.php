<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$orgvalue= substr($_SESSION['empl']['namalokasitugas'], 0 ,3);
$optReg = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$sOpt = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' AND kodeorganisasi LIKE'".$orgvalue."%'";
$qOpt = mysql_query($sOpt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    $optPabrik .= '<option value='.$rOpt['kodeorganisasi'].' selected>'.$rOpt['namaorganisasi'].'</option>';
    $optPabrik2 .= '<option value='.$rOpt['kodeorganisasi'].' selected>'.$rOpt['namaorganisasi'].'</option>';
}
$sGet = 'select kodetangki,keterangan from '.$dbname.".pabrik_5tangki where kodeorg LIKE '".$orgvalue."%'";
$qGet = mysql_query($sGet);
$optTangki = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($rGet = mysql_fetch_assoc($qGet)) {
    $optTangki .= '<option value='.$rGet['kodetangki'].'>'.$rGet['keterangan'].'</option>';
}
$optProduk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sPrd = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kodebarang like '4%'";
$qPrd = mysql_query($sPrd);
while ($rPrd = mysql_fetch_assoc($qPrd)) {
    $optProduk .= '<option value='.$rPrd['kodebarang'].'>'.$rPrd['namabarang'].'</option>';
}
$sGp = 'select DISTINCT substr(tanggal,1,7) as periode  from '.$dbname.'.pabrik_produksi order by tanggal desc';
$qGp = mysql_query($sGp);
while ($rGp = mysql_fetch_assoc($qGp)) {
    $thn = explode('-', $rGp['periode']);
    if ('12' === $thn[1]) {
        $optPeriode .= "<option value='".substr($rGp['periode'], 0, 4)."'>".substr($rGp['periode'], 0, 4).'</option>';
    }

    $optPeriode .= "<option value='".$rGp['periode']."'>".substr($rGp['periode'], 5, 2).'-'.substr($rGp['periode'], 0, 4).'</option>';
}
$arr = '##kdPbrik##kdTangki##';
$arr1 = '##kodeorg1##tanggal1';
$arr2 = '##kodeorg2##tanggal2';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=\"javascript\" src=\"js/pabrik_4persediaan.js\"></script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";
$frm[0] .= '<div style=margin-bottom: 30px;>';
$frm[0] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['laporanstok'].'</b></legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td><label>".$_SESSION['lang']['unit'].'</label></td><td><select id=kdPbrik name=kdPbrik style=width:150px onchange=getTangki() disabled>'.$optPabrik."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['kodetangki'].'</label></td><td><select id=kdTangki name=kdTangki style=width:150px>'.$optTangki."</select></td></tr>\r\n<!--<tr><td><label>".$_SESSION['lang']['periode'].'</label></td><td><select id=periode name=periode style=width:150px><option value="">'.$optPeriode."</select></td></tr>-->\r\n<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaan','".$arr."','printContainer') class=mybutton name=preview id=preview>Preview</button>\r\n    <button onclick=zPdf('pabrik_slave_4persediaan','".$arr."','printContainer') class=mybutton name=preview id=preview>PDF</button>\r\n    <button onclick=zExcel(event,'pabrik_slave_4persediaan.php','".$arr."') class=mybutton name=preview id=preview>Excel</button></td></tr>\r\n</table>\r\n</fieldset>";
$frm[0] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$frm[1] .= '<div style=margin-bottom: 30px;>';
$frm[1] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['laporanstok'].' vs '.$_SESSION['lang']['pengiriman'].'</b></legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td><label>".$_SESSION['lang']['unit'].'</label></td><td><select id=kodeorg1 name=kodeorg1 style=width:150px disabled>'.$optPabrik."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td><input type=text class=myinputtext id=tanggal1 name=tanggal1 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  maxlength=10 style=\"width:150px;\" /></td></tr>\r\n<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaan_kirim','".$arr1."','printContainer1') class=mybutton name=preview id=preview>Preview</button>\r\n</table>\r\n</fieldset>";
$frm[1] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer1' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$frm[2] .= '<div style=margin-bottom: 30px;>';
$frm[2] .= "<fieldset>\r\n<legend><b>".$_SESSION['lang']['laporanstok'].'</b></legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n<tr><td><label>".$_SESSION['lang']['unit'].'</label></td><td><select id=kodeorg2 name=kodeorg2 style=width:150px disabled>'.$optPabrik2."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td><input type=text class=myinputtext id=tanggal2 name=tanggal2 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"  maxlength=10 style=\"width:150px;\" /></td></tr>\r\n<tr><td colspan=2><button onclick=zPreview('pabrik_slave_4persediaanhip','".$arr2."','printContainer2') class=mybutton name=preview id=preview>Preview</button>\r\n</table>\r\n</fieldset>";
$frm[2] .= "</div>\r\n\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer2' style='overflow:auto;height:50%;max-width:100%;'>\r\n</div></fieldset>";
$hfrm[0] = $_SESSION['lang']['laporanstok'];
$hfrm[1] = $_SESSION['lang']['laporanstok'].' vs '.$_SESSION['lang']['pengiriman'];
$hfrm[2] = $_SESSION['lang']['laporanstok'].'';
drawTab('FRM', $hfrm, $frm, 200, 900);
CLOSE_BOX();
echo close_body();

?>