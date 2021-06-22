<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo "<script>\r\n    var plh='';\r\n    plh=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\r\n</script>\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_budget_pks.js\"></script>\r\n";
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nmAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optKb = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$iKb = 'select distinct kodebudget from '.$dbname.".bgt_budget where tipebudget='MILL' order by kodebudget asc ";
$nKb = mysql_query($iKb) || exit(mysql_error($conns));
while ($dKb = mysql_fetch_assoc($nKb)) {
    $optKb .= "<option value='".$dKb['kodebudget']."'>".$dKb['kodebudget'].'</option>';
}
$optMesin = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$iMesin = 'select distinct kodeorg from '.$dbname.".bgt_budget where tipebudget='MILL' order by kodeorg asc ";
$nMesin = mysql_query($iMesin) || exit(mysql_error($conns));
while ($dMesin = mysql_fetch_assoc($nMesin)) {
    $optMesin .= "<option value='".$dMesin['kodeorg']."'>".$nmOrg[$dMesin['kodeorg']].'</option>';
}
$optAkun = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$iAkun = 'select distinct noakun from '.$dbname.".bgt_budget where tipebudget='MILL' order by kodeorg asc ";
$nAkun = mysql_query($iAkun) || exit(mysql_error($conns));
while ($dAkun = mysql_fetch_assoc($nAkun)) {
    $optAkun .= "<option value='".$dAkun['noakun']."'>".$dAkun['noakun'].' [ '.$nmAkun[$dAkun['noakun']].' ]</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where (tipe='STATION') and induk = '".$_SESSION['empl']['lokasitugas']."'\r\n        order by kodeorganisasi\r\n        ";
$optstation = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optstation .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n        where (tipe='PABRIK') and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."'\r\n        order by kodeorganisasi\r\n        ";
$optpabrik = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optpabrik .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select distinct tahunbudget from '.$dbname.".bgt_budget\r\n        where tutup = '0' and kodebudget != 'UMUM' and tipebudget = 'MILL' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'\r\n        order by tahunbudget desc\r\n        ";
$opttahun = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttahun .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
}
$optmesin = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode\r\n        where kodebudget like 'EXPL%'\r\n        ";
$optkodebudget0 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optkodebudget0 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode\r\n        where kodebudget like 'M%'\r\n        ";
$optmaterial1 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optmaterial1 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$optjenis1 = '';
$optjenis1 .= "<option value='consumables'>Consumables</option>";
$optjenis1 .= "<option value='controllabe'>Controllabe</option>";
$optjenis1 .= "<option value='noncontrollabe'>Non Controllabe</option>";
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode\r\n        where kodebudget like 'TOOL%'\r\n        ";
$opttool2 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttool2 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode\r\n                    where kodebudget like 'VHC%'\r\n                    ";
$optkode3 = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optkode3 .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$optvhc3 = '';
OPEN_BOX('', '<b>'.$_SESSION['lang']['biaya'].' '.$_SESSION['lang']['pabrik'].'</b>');
echo "<br /><fieldset style='float:left;width:275px;'><legend>".$_SESSION['lang']['form']."</legend><table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['tipeanggaran']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tipebudget name=tipebudget onkeypress=\"return angka_doang(event);\" maxlength=2 disabled=true style=width:150px; value=\"MILL\"/></td>\r\n        \r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td>\r\n    \r\n    </tr>\r\n    <tr><td>".$_SESSION['lang']['station']."</td><td>:</td><td colspan=3>\r\n        <select name=station id=station onchange=\"load_mesin();\" style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optstation."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['mesin']."</td><td>:</td><td colspan=3>\r\n        <select name=mesin id=mesin style='width:150px;'>".$optmesin."</select></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan name=simpan onclick=prosesSimpan()>".$_SESSION['lang']['save']."</button>\r\n        <button class=mybutton id=baru name=baru onclick=prosesBaru()>".$_SESSION['lang']['baru']."</button>\r\n        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >\r\n    </td></tr></table></fieldset>\r\n    <fieldset fieldset style='float:left;width:275px;'><legend>".$_SESSION['lang']['tutup']."</legend>\r\n        <table>\r\n        <tr><td>".$_SESSION['lang']['pabrik']." </td><td>:</td><td>\r\n        <select name=pabrik id=pabrik style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optpabrik."</select></td>\r\n        </tr>\r\n        <tr><td>".$_SESSION['lang']['budgetyear']." </td><td>:</td><td>\r\n        <select name=tahuntutup id=tahuntutup style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</opion>'.$opttahun."</select></td></tr>\r\n        <tr><td colspan=3 align=center>\r\n        <button class=mybutton id=tutup name=tutup onclick=prosesTutup()>".$_SESSION['lang']['close']."</button></td></tr>\r\n        </table></fieldset>";
echo "<fieldset style='width:250px;'>\r\n\t\t<legend><b>Info</b></legend>\r\n\t\t\t<image src=images\\box\\icon-info.GIF>\r\n\t\t\tJika Budget Station, maka pada saat pemilihan option mesin, diisi station tersebut \r\n\t\t\t(secara default mesin akan berisikan station awal, sesuai dengan station yang kita pilih).\r\n\t</fieldset><br /><br /><br /><br /><br /><br />";
$frm[0] .= '<fieldset id=tab0 disabled=true><legend>'.$_SESSION['lang']['eksploitasi'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget0 onchange=\"bersihkan(0);\" name=kodebudget0 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optkodebudget0."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=jumlahpertahun0 name=jumlahpertahun0 onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan0 name=simpan0 onclick=simpan0()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=tersembunyi0 name=tersembunyi0 value=tersembunyi >\r\n    </td></tr></table>";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>    \r\n<div id=container0></div>\r\n    ";
$frm[0] .= '</fieldset>';
$optAkunTmbhAkun = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('EN' == $_SESSION['language']) {
    $dd = 'namaakun1 as namaakun';
} else {
    $dd = 'namaakun as namaakun';
}

$sAkun = 'SELECT distinct noakun,'.$dd.' FROM '.$dbname.".`keu_5akun` where (noakun in ('6320102','6320103','6320104') \r\n  \t\t\tor (noakun like '%811%') or (noakun like '%812%')) and detail=1";
$qAkun = mysql_query($sAkun) || exit(mysql_error($conns));
while ($rAkun = mysql_fetch_assoc($qAkun)) {
    $optAkunTmbhAkun .= "<option value='".$rAkun['noakun']."'>".$rAkun['noakun'].'- ['.$rAkun['namaakun'].']</option>';
}
$frm[1] .= '<fieldset id=tab1 disabled=true><legend>'.$_SESSION['lang']['material'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['noakun']."</td><td>:</td><td>\r\n    <select id=anggaranKd  name=anggaranKd style='width:150px;'>\r\n    ".$optAkunTmbhAkun."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget1 onchange=\"bersihkan(1);\" name=kodebudget1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optmaterial1."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=kodebarang1 name=kodebarang1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true readonly=readonly>\r\n        <input type=\"image\" id=search1 disabled=true src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg value='.$kodebarang1.'><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";>    \r\n        <label id=namabarang1></label></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jenis']."</td><td>:</td><td>\r\n        <select id=jenis1 name=jenis1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optjenis1."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext onblur=\"jumlahkan1();\" id=jumlah1 name=jumlah1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>\r\n        <label id=satuan1></td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalharga1 name=totalharga1 onkeypress=\"return false;\" maxlength=10 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan1 name=simpan1 onclick=simpan1()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=regional1 name=regional1 value=>\r\n    </td></tr></table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container1></div>    \r\n    ";
$frm[1] .= '</fieldset>';
$frm[2] .= '<fieldset id=tab2 disabled=true><legend>'.$_SESSION['lang']['pemeliharaan'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=kodebudget2 name=kodebudget2 value=\"PKSM\" maxlength=10 style=width:150px; disabled=true /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=jumlahpertahun2 name=jumlahpertahun2 onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan2 name=simpan2 onclick=simpan2()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=tersembunyi2 name=tersembunyi2 value=tersembunyi >\r\n    </td></tr></table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>    \r\n<div id=container2></div>\r\n    ";
$frm[2] .= '</fieldset>';
$frm[3] .= '<fieldset id=tab3 disabled=true><legend>'.$_SESSION['lang']['abkend'].'</legend>';
$frm[3] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget3 name=kodebudget3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optkode3."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td>\r\n        <select id=kodevhc3 onblur=\"jumlahkan3();\" name=kodevhc3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optvhc3."</select>\r\n            </td></tr>\r\n    <tr><td>".$_SESSION['lang']['jmljamkerja']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=jumlahjam3 name=jumlahjam3 onblur=\"jumlahkan3();\" onkeypress=\"return angka_doang(event);\" maxlength=15 style=width:150px; /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=satuan3 name=satuan3 value=\"jam\" maxlength=15 style=width:150px; disabled=true/></td></tr>\r\n    <tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=totalbiaya3 name=totalbiaya3 onkeypress=\"return false;\" maxlength=15 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan3 name=simpan3 onclick=simpan3()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=regional3 name=regional3 value=>\r\n    </td></tr></table>";
$frm[3] .= '</fieldset>';
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container3></div>    \r\n    ";
$frm[3] .= '</fieldset>';
$frm[4] .= "<fieldset id=tab4 disabled=true>\r\n\t\t\t<legend>".$_SESSION['lang']['sebaran']."</legend>\r\n\t\t\t\r\n\t\t\t<fieldset style=width:600px;float:left><legend>Short</legend>\r\n\t\t\t<table><tr>\r\n\t\t\t\t<td colspan=20>\r\n\t\t\t\t\t\r\n\t\t\t\t\t".$_SESSION['lang']['kodebudget'].": <select id='budgetSort' style='width:75px;' onchange='ubah_list()'>".$optKb."</select>\r\n\t\t\t\t\t".$_SESSION['lang']['mesin'].": <select id='mesinSort' style='width:150px;' onchange='ubah_list()'>".$optMesin."</select>\r\n\t\t\t\t\t".$_SESSION['lang']['noakun'].": <select id='akunSort' style='width:100px;' onchange='ubah_list()'>".$optAkun."</select>\r\n\t\t\t\t</td>\r\n\t\t\t</tr></table></fieldset>\r\n\t\t\t\r\n\t\t\t<fieldset style=width:100px;float:left><legend>Refresh List</legend>\r\n\t\t\t<table><tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<button class=mybutton id=refresh name=refresh onclick=ubah_list()>Refresh List</button>\r\n\t\t\t\t</td>\r\n\t\t\t</tr></table></fieldset>\r\n\t\t\t\r\n\t\t\t\t<div id=container4>\r\n\t\t\t\t</div>";
$frm[4] .= '</fieldset>';
$hfrm[0] = $_SESSION['lang']['eksploitasi'];
$hfrm[1] = $_SESSION['lang']['material'];
$hfrm[2] = $_SESSION['lang']['pemeliharaan'];
$hfrm[3] = $_SESSION['lang']['abkend'];
$hfrm[4] = $_SESSION['lang']['sebaran'];
drawTab('FRM', $hfrm, $frm, 100, 1100);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>