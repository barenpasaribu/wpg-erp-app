<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_ws_biaya.js\"></script>\r\n";
$str = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi like '". substr($_SESSION['empl']['lokasitugas'], 0, 3)."%' AND tipe='WORKSHOP' ";
$optws = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optws .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select * from '.$dbname.".bgt_upah where closed=0 and kodeorg LIKE '".substr($_SESSION['empl']['lokasitugas'], 0, 3)."%'";
//echo $str;
$optkodebudget0 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optkodebudget0 .= "<option value='".$bar->golongan."'>".$bar->kodeorganisasi. " ".$bar->golongan.'</option>';
}
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode";
$optmaterial1 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optmaterial1 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode";
$opttool2 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttool2 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode";
$opttransit3 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttransit3 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun where detail=1 and tipeakun = 'Biaya' order by noakun\r\n                    ";
$optakun3 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optakun3 .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['biaya'].' '.$_SESSION['lang']['workshop'].'</b>');
echo "<fieldset style='width:250px'><legend>".$_SESSION['lang']['form']."</legend><table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['tipeanggaran']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tipebudget name=tipebudget onkeypress=\"return angka_doang(event);\" maxlength=2 disabled=true style=width:150px; value=\"WS\"/></td></tr>\r\n    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['workshop']."</td><td>:</td><td>\r\n        <select name=kodews id=kodews style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optws."</select></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan name=simpan onclick=prosesSimpan()>".$_SESSION['lang']['save']."</button>\r\n        <button class=mybutton id=baru name=baru onclick=prosesBaru()>".$_SESSION['lang']['baru']."</button>\r\n        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >\r\n    </td></tr></table></fieldset><br>";
$frm[0] .= '<fieldset id=tab0 disabled=true><legend>'.$_SESSION['lang']['sdm'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n    <tr><td>Kode Golongan</td><td>:</td><td><select id=kodebudget0 onchange=\"jumlahkan0();\" name=kodebudget0 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optkodebudget0."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['hkefektif']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=hkefektif0 name=hkefektif0 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jmlhPersonel']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext onkeyup=\"jumlahkan0();\" id=jumlahpersonel0 name=jumlahpersonel0 onkeypress=\"return angka_doang(event);\" maxlength=8 style=width:150px; /> ".$_SESSION['lang']['setahun']."</td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalbiaya0 name=totalbiaya0 onkeypress=\"return false;\" maxlength=15 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan0 name=simpan0 onclick=simpan0()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=tersembunyi0 name=tersembunyi0 value=tersembunyi >\r\n    </td></tr></table>";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>    \r\n<div id=container0></div>\r\n    ";
$frm[0] .= '</fieldset>';
$frm[1] .= '<fieldset id=tab1 disabled=true><legend>'.$_SESSION['lang']['material'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget1 onchange=\"bersihkan(1);\" name=kodebudget1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optmaterial1."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=kodebarang1 name=kodebarang1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>\r\n        <input type=\"image\" id=search1 disabled=true src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg value='.$kodebarang1.'><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";>    \r\n        <label id=namabarang1></label></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext onkeyup=\"jumlahkan1();\" id=jumlah1 name=jumlah1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>\r\n        ".$_SESSION['lang']['setahun']." <label id=satuan1></td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalharga1 name=totalharga1 onkeypress=\"return false;\" maxlength=10 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan1 name=simpan1 onclick=simpan1()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=regional1 name=regional1 value=>\r\n    </td></tr></table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container1></div>    \r\n    ";
$frm[1] .= '</fieldset>';
$frm[2] .= '<fieldset id=tab2 disabled=true><legend>'.$_SESSION['lang']['peralatan'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget2 onchange=\"bersihkan(2);\" name=kodebudget2 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$opttool2."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=kodebarang2 name=kodebarang2 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>\r\n        <input type=\"image\" id=search2 src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(2,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg2 value='.$kodebarang2.'><button class=mybutton onclick=findBrg(2)>Find</button></fieldset><div id=containerx></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";>    \r\n        <label id=namabarang2></label></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext onkeyup=\"jumlahkan2();\" id=jumlah2 name=jumlah2 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>\r\n        ".$_SESSION['lang']['setahun']." <label id=satuan2></td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalharga2 name=totalharga2 onkeypress=\"return false;\" maxlength=10 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan2 name=simpan2 onclick=simpan2()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=regional2 name=regional2 value=>\r\n    </td></tr></table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container2></div>    \r\n    ";
$frm[2] .= '</fieldset>';
$frm[3] .= '<fieldset id=tab3 disabled=true><legend>'.$_SESSION['lang']['lain'].'</legend>';
$frm[3] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget3 name=kodebudget3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$opttransit3."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['noakun']."</td><td>:</td><td>\r\n        <select id=kodeakun3 name=kodeakun3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optakun3."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalbiaya3 name=totalbiaya3 onkeypress=\"return angka_doang(event);\" maxlength=15 style=width:150px; /> ".$_SESSION['lang']['setahun']."</td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan3 name=simpan3 onclick=simpan3()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=regional3 name=regional3 value=>\r\n    </td></tr></table>";
$frm[3] .= '</fieldset>';
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container3></div>    \r\n    ";
$frm[3] .= '</fieldset>';
$frm[4] .= '<fieldset id=tab4 disabled=true><legend>'.$_SESSION['lang']['close'].'</legend>';
$frm[4] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>\r\n        <button class=mybutton id=display4 name=display4 onclick=persiapantutup4()>".$_SESSION['lang']['list']."</button>\r\n    </td><td>\r\n        <button class=mybutton id=tutup4 name=tutup4 onclick=tutup4(1) disabled=true>".$_SESSION['lang']['close']."</button>\r\n        <input type=hidden id=hidden4 name=hidden4 value=>\r\n    </td></tr></table>";
$frm[4] .= '</fieldset>';
$frm[4] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container4></div>    \r\n    ";
$frm[4] .= '</fieldset>';
$hfrm[0] = $_SESSION['lang']['sdm'];
$hfrm[1] = $_SESSION['lang']['material'];
$hfrm[2] = $_SESSION['lang']['peralatan'];
$hfrm[3] = $_SESSION['lang']['lain'];
//$hfrm[4] = $_SESSION['lang']['close'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>