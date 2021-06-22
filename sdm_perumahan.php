<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "<script type=\"text/javascript\" src=\"js/sdm_perumahan.js\"></script>\r\n";
$soptOrg = '';
$sorg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\r\n      tipe in('KEBUN','KANWIL','PABRIK')\r\n      order by kodeorganisasi";
$qorg = mysql_query($sorg);
global $kd_org;
while ($rorg = mysql_fetch_assoc($qorg)) {
    $kd_org = $rorg['kodeorganisasi'];
    $soptOrg .= "<option '".(($rorg['kodeorganisasi'] == $rest['kodeorganisasi'] ? 'selected=selected' : ''))."' value=".$rorg['kodeorganisasi'].' >'.$rorg['namaorganisasi'].'</option>';
}
$optKondisi = "<option value='B-BD'>B-BD:Baik bisa dipakai</option>";
$optKondisi .= "<option value='B-TD'>B-TD:Baik tida dipakai</option>";
$optKondisi .= "<option value='R-BD'>R-BD:Rusak Bisa dipakai</option>";
$optKondisi .= "<option value='R-TD'>R-TD:Rusak tidak dipakai</option>";
$optThn = '';
$thn_skrng = (int) (date('Y'));
for ($i = $thn_skrng; $thn_skrng - 30 <= $i; --$i) {
    $optThn .= "<option value='".$i."'>".$i.'</option>';
}
$opt_tipe_rmh = '';
$str = 'select jenis,nama from '.$dbname.".sdm_5jenis_prasarana where jenis like 'R%' order by nama";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opt_tipe_rmh .= "<option value='".$bar->jenis."'>".$bar->jenis.': '.$bar->nama.'</option>';
}
$opt_kompleks = '';
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe in('KEBUN','AFDELING','PABRIK') order by kodeorganisasi";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opt_kompleks .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
OPEN_BOX('', $_SESSION['lang']['manajemenperumahan'].'<br>');
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['data_rmh'].''.$thn_skrg.'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td>\r\n<select id=kode_org name=kode_org onChange=load_data() style=width:200px;><option value=></option>".$soptOrg."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['komplek_rmh']."</td><td>:</td><td>\r\n<select id=nm_kompleks>".$opt_kompleks."</select>    \r\n</td></tr>\r\n<tr><td>Blok Rumah</td><td>:</td><td>\r\n<input type=text class=myinputtext id=blok_rmh name=blok_rmh maxlength=4 onkeypress=\"return tanpa_kutip(event);\" style=width:200px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['no_rmh']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=no_rmh name=no_rmh maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:200px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['tipe_rmh']."</td><td>:</td><td>\r\n<select id=tipe_rmh>".$opt_tipe_rmh."</select>    \r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['thn_bgn_rmh'].'</td><td>:</td><td><select id=thn_buat_rmh name=thn_buat_rmh style=width:200px;>'.$optThn."</select>\r\n<!--<input type=text class=myinputtext id=thn_buat_rmh name=thn_buat_rmh maxlength=45 onkeypress=\"return angka_doang(event);\" />--></td></tr>\r\n<tr><td>".$_SESSION['lang']['knds_rmh'].'</td><td>:</td><td><select id=kndsi_rmh name=kndsi_rmh style=width:200px;>'.$optKondisi."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['note']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=ket_rmh name=ket_rmh maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style=width:200px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['alamat']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=almt_rmh name=almt_rmh maxlength=60 onkeypress=\"return tanpa_kutip(event);\" style=width:200px; /></td></tr>\r\n<tr><td colspan=3>\r\n<button class=mybutton id=save_kepala name=save_kepala onclick=save_header() >".$_SESSION['lang']['save']."</button>\r\n<button class=mybutton id=cancel_kepala name=cancel_kepala onclick=clear_save_form() >".$_SESSION['lang']['cancel']."</button>\r\n</table>";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n         <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['lokasi']."</td>                    \r\n\t\t<td>".$_SESSION['lang']['blok']."</td>\r\n\t\t<td>".$_SESSION['lang']['no_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['tipe_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['thn_bgn_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['knds_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['note']."</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=contain>\r\n\t\t\r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$optAset = '';
$saset = 'select kodeasset,namasset from '.$dbname.".sdm_daftarasset where tipeasset='PRT'";
$qaset = mysql_query($saset);
while ($raset = mysql_fetch_assoc($qaset)) {
    $optAset .= '<option value='.$raset['kodeasset'].'>'.$raset['namasset'].'</option>';
}
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['data_rmh'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeorg'].'</td><td>:</td><td><select id=kode_org_asset name=kode_org_asset onchange=get_blok() style=width:235px><option value=></option>'.$soptOrg."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['blok']."</td><td>:</td><td>\r\n<select id=blok_rmh_asset name=blok_rmh_asset onchange=get_normh('0','0') style=width:235px></select>\r\n<!--<input type=text class=myinputtext id=blok_rmh_asset name=blok_rmh_asset maxlength=4 onkeypress=\"return tanpa_kutip(event);\"/>--></td></tr>\r\n<tr><td>".$_SESSION['lang']['no_rmh']."</td><td>:</td><td>\r\n<select id=no_rmh_asset name=no_rmh_asset style=width:235px></select>\r\n<!--<input type=text class=myinputtext id=no_rmh_asset name=no_rmh_asset maxlength=4 onkeypress=\"return angka_doang(event);\" />--></td></tr>\r\n<tr><td>".$_SESSION['lang']['namaaset'].'</td><td>:</td><td><select id=kode_asset name=kode_asset style=width:235px><option value=></option>'.$optAset."</select></td></tr>\r\n\r\n<tr><td colspan=3><button class=mybutton onclick=save_asset() >".$_SESSION['lang']['save'].'</button><button class=mybutton onclick=clear_save_form_asset() >'.$_SESSION['lang']['cancel']."</button>\r\n</table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n        <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['blok']."</td>\r\n\t\t<td>".$_SESSION['lang']['no_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['namaaset']."</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=containasset>\r\n\t\t";
$frm[1] .= '</tbody></table></fieldset>';
$lksiTugas = $_SESSION['empl']['lokasitugas'];
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['data_rmh'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['kodeorg'].'</td><td>:</td><td><select id=kode_org_penghuni name=kode_org_penghuni style=width:200px onchange=get_blok_penghuni()><option value=></option>'.$soptOrg."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['blok']."</td><td>:</td><td>\r\n<select id=blok_rmh_penghuni name=blok_rmh_penghuni style=width:200px onchange=get_normh_penghuni('0','0')></select>\r\n<!--<input type=text class=myinputtext id=blok_rmh_penghuni name=blok_rmh_penghuni maxlength=4 onkeypress=\"return tanpa_kutip(event);\"/>--></td></tr>\r\n<tr><td>".$_SESSION['lang']['no_rmh']."</td><td>:</td><td>\r\n<select id=no_rmh_penghuni name=no_rmh_penghuni style=width:200px></select>\r\n<!--<input type=text class=myinputtext id=no_rmh_penghuni name=no_rmh_penghuni maxlength=4 onkeypress=\"return angka_doang(event);\" />--></td></tr>\r\n<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td><select id=kode_karyawan name=kode_karyawan style=width:200px></select></td></tr>\r\n\r\n<tr><td colspan=3><button class=mybutton onclick=save_penghuni() >".$_SESSION['lang']['save'].'</button><button class=mybutton onclick=clear_save_form_penghuni() >'.$_SESSION['lang']['cancel']."</button>\r\n</table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n      <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['blok']."</td>\r\n\t\t<td>".$_SESSION['lang']['no_rmh']."</td>\r\n\t\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=containpenghuni>\r\n\t\t";
$frm[2] .= '</tbody></table></fieldset>';
$hfrm[0] = $_SESSION['lang']['rumah'];
$hfrm[1] = $_SESSION['lang']['prabot'];
$hfrm[2] = $_SESSION['lang']['penghuni'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>