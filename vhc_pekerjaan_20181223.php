<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "<link rel=stylesheet type=\"text/css\" href='style/zTable.css'>\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\n<script type=\"text/javascript\" src=\"js/vhc_pekerjaan.js\"></script>\n<script>\ndataKdvhc=\"";
echo $_SESSION['lang']['pilihdata'];
echo "\";\n</script>\n";
$soptOrg = '';
$sorg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where\n      tipe in ('HOLDING','KEBUN','KANWIL','PABRIK')\n      and kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\n      order by namaorganisasi desc";
$qorg = mysql_query($sorg) || exit(mysql_error($conns));
global $kd_org;
while ($rorg = mysql_fetch_assoc($qorg)) {
    $kd_org = $rorg['kodeorganisasi'];
    $soptOrg .= "<option '".(($rorg['kodeorganisasi'] === $rest['kodeorganisasi'] ? 'selected=selected' : ''))."' value=".$rorg['kodeorganisasi'].' >'.$rorg['namaorganisasi'].'</option>';
}
$sjvch = 'select jenisvhc,namajenisvhc from '.$dbname.'.vhc_5jenisvhc order by namajenisvhc';
$qjvch = mysql_query($sjvch) || exit(mysql_error($conns));
while ($rjvch = mysql_fetch_assoc($qjvch)) {
    $optJnsvhc .= "<option value='".$rjvch['jenisvhc']."'>".$rjvch['jenisvhc'].'-'.$rjvch['namajenisvhc'].'</option>';
}
$strak = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi where tipe = 'TRAKSI' order by namaorganisasi ";
$qtrak = mysql_query($strak) || exit(mysql_error($conns));
while ($rtrak = mysql_fetch_assoc($qtrak)) {
    $optTraksi .= '<option value='.$rtrak['kodeorganisasi'].'>'.$rtrak['kodeorganisasi'].'-'.$rtrak['namaorganisasi'].'</option>';
}
$arrOpt = ['KM', 'HM'];
foreach ($arrOpt as $brs => $isi) {
    $optSatuanvhc .= '<option value='.$isi.'>'.$isi.'</option>';
}
$where = " `kelompokbarang` = '351'";
$sbrg = 'select kodebarang,namabarang from '.$dbname.'.log_5masterbarang where '.$where.'';
$qbrg = mysql_query($sbrg) || exit(mysql_error($conns));
while ($rbrg = mysql_fetch_assoc($qbrg)) {
    $optJnsBBMvhc .= '<option value='.$rbrg['kodebarang'].'>'.$rbrg['kodebarang'].'-'.$rbrg['namabarang'].'</option>';
}
$arrPremi = ['Non Premi', 'Premi'];
foreach ($arrPremi as $brs => $isi) {
    $optStatPremi .= '<option value='.$brs.'>'.$isi.'</option>';
}
$lksiTgs = substr($_SESSION['empl']['lokasitugas'], 0, 4);
for ($x = 0; $x <= 3; ++$x) {
    $dt = mktime(0, 0, 0, 0, 15, date('Y') + $x);
    $optper .= '<option value='.date('Y', $dt).'>'.date('Y', $dt).'</option>';
}
$optOrg2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg2 = 'select kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
$qOrg2 = mysql_query($sOrg2) || exit(mysql_error($conns));
while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
    $optOrg2 .= '<option value='.$rOrg2['kodeorganisasi'].'>'.$rOrg2['kodeorganisasi'].'</option>';
}
$optStatusLst = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$arrStatus = ['Belum Posting', 'Sudah diposting'];
foreach ($arrStatus as $lstStatus => $vwStatus) {
    $optStatusLst .= "<option value='".$lstStatus."'>".$vwStatus.'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['vhc_pekerjaan'].'</b>');
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].''.$thn_skrg.'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\n<tr><td>".$_SESSION['lang']['notransaksi'].'</td><td>:</td><td><select id=KbnId name=KbnId onchange="createNew()">'.$optOrg2."</select>\n<input type=text id=no_trans name=no_trans disabled=disabled class=myinputtext style=width:150px; /></td></tr>\n<!--<tr><td>".$_SESSION['lang']['thnKontrak']." </td><td>:</td><td>\n<select id=thnKntrk name=thnKntrk style='width:150px;' onchange=\"getKntrk('','')\"><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optper."</select> </td></tr>\n<tr><td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>\n<select id=noKntrk name=noKntrk style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>\n<tr><td>".$_SESSION['lang']['statPremi']."</td><td>:</td><td>\n<select id=premiStat name=premiStat style=width:150px;>".$optStatPremi."</select></td></tr>-->\n<tr><td>".$_SESSION['lang']['jenisvch']."</td><td>:</td><td>\n<select id=jns_vhc name=jns_vhc style=width:150px; onchange=\"get_kd('')\"><option value=>".$_SESSION['lang']['pilihdata'].'</option>'.$optJnsvhc."</select></td></tr>\n<tr><td>".$_SESSION['lang']['kodetraksi']."</td><td>:</td><td>\n<select id=kodetraksi name=kodetraksi style=width:150px; onchange=\"get_kd('')\"><option value=>".$_SESSION['lang']['all'].'</option>'.$optTraksi."</select></td></tr>\n<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td>\n<select id=kde_vhc name=kde_vhc  style=width:150px;><option value=''>".$_SESSION['lang']['pilihdata']."</option></select>\n<img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['kodevhc']."','1',event);\"  />    \n</td></tr>\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>\n<input type=text class=myinputtext id=tgl_pekerjaan name=tgl_pekerjaan onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['vhc_jenis_bbm']."</td><td>:</td><td>\n<select id=jns_bbm name=jns_bbm style=width:150px;>".$optJnsBBMvhc."</select></td></tr>\n<tr><td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td><td>:</td><td>\n<input type=text class=myinputtextnumber id=jmlh_bbm name=jmlh_bbm maxlength=60 onkeypress=\"return angka_doang(event);\" style=width:150px; value=0 /> Ltr</td></tr>\n<tr><td colspan=3>\n<button class=mybutton id=save_kepala name=save_kepala onclick=save_header() disabled >".$_SESSION['lang']['save'].'</button><button class=mybutton id=cancel_kepala name=cancel_kepala onclick=cancel_kepala_form() disabled >'.$_SESSION['lang']['cancel'].'</button><button class=mybutton id=done_entry name=done_entry onclick=doneEntry() disabled >'.$_SESSION['lang']['done']."</button>\n\n<input type=hidden id=proses name=proses value=insert_header >\n</td></tr></table>";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\n<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['find']." Data</legend>\n    <table cellspacing=\"1\" border=\"0\"><tr>\n        <td>".$_SESSION['lang']['notransaksi']."</td>\n        <td><input type=\"text\" id='txtCari' name='txtCari' style='width:150px' class=myinputtext />\n        &nbsp;".$_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />\n        &nbsp;".$_SESSION['lang']['status'].':<select id=statusInputan>'.$optStatusLst."</select>\n        <button class=mybutton id=cariTransaksi name=cariTransaksi onclick=cariDataTransaksi()  >".$_SESSION['lang']['find'].'</button><button class=mybutton  onclick=load_data()  >'.$_SESSION['lang']['cancel']."</button>\n</td></tr></table></fieldset>\n<table cellspacing=1 border=0 class=sortable>\n\t\t<thead>\n\t\t<tr class=\"rowheader\">\n\t\t<td>No.</td>\n\t\t<td>".$_SESSION['lang']['notransaksi']."</td>\n\t\t<td>".$_SESSION['lang']['jenisvch']."</td>\n\t\t<td>".$_SESSION['lang']['kodevhc']."</td>\n\t\t<td>".$_SESSION['lang']['tanggal']."</td>\n\t\t<td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>\n\t\t<td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>\n\t\t<td>Action</td>\n\t\t</tr></thead><tbody id=contain>\n\t\t<script>load_data()</script>\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$sjnskrj = 'select * from '.$dbname.".vhc_kegiatan where regional='".$_SESSION['empl']['regional']."' order by kodekegiatan asc";
$qjnskrj = mysql_query($sjnskrj) || exit(mysql_error($conns));
while ($rjnskrj = mysql_fetch_assoc($qjnskrj)) {
    $optJnsKerja .= '<option value='.$rjnskrj['kodekegiatan'].'>'.$rjnskrj['kodekegiatan'].' - '.$rjnskrj['namakegiatan'].'</option>';
}
$slokTgs = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where `tipe` NOT\nIN ('PT', 'BLOK', 'HOLDING', 'STATION', 'STENGINE','AFDELING')";
$qlokTgs = mysql_query($slokTgs) || exit(mysql_error($conns));
while ($rlokTgs = mysql_fetch_assoc($qlokTgs)) {
    $optLokTugas .= '<option value='.$rlokTgs['kodeorganisasi'].'>'.$rlokTgs['namaorganisasi'].'</option>';
}
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['vhc_detail_pekerjaan'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0>\n<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td>\n<input type=text id=no_trans_pekerjaan name=no_trans_pekerjaan disabled=disabled class=myinputtext style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td><td>:</td><td>\n<select id=jns_kerja name=jns_kerja  style=width:150px; onchange=getSatuanKrj(0)><option value=></option>".$optJnsKerja."</select>\n<input type=hidden name=old_jnskerja id=old_jnskerja />\n<img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['vhc_jenis_pekerjaan']."','2',event);\"  />\n</td></tr>\n<tr><td>".$_SESSION['lang']['alokasibiaya']."</td><td>:</td><td>\n<select id=lokasi_kerja name=lokasi_kerja  style=width:150px; onchange=\"getBlok('','','')\"><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optLokTugas."</select> \n<input type=hidden name=old_lokkerja id=old_lokkerja />\n</td></tr>\n<tr><td>&nbsp;</td><td>&nbsp;</td><td>\n<select id=blok name=blok  style=width:150px; ><option value=''>".$_SESSION['lang']['pilihdata']."</option></select>\n<input type=hidden name=old_blok id=old_blok />\n<img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['alokasibiaya']."','3',event);\"  />\n</td></tr>\n<tr><td>".$_SESSION['lang']['prestasi']."</td><td>:</td>\n<td><input type=text class=myinputtextnumber id=brt_muatan name=brt_muatan maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /> &nbsp;<span id=satuanKrj></span></td> </tr>\n<tr><td>".$_SESSION['lang']['jumlahrit']."</td><td>:</td><td>\n<input type=text class=myinputtextnumber id=jmlh_rit name=jmlh_rit maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['vhc_kmhm_awal']."</td><td>:</td><td>\n<input type=text class=myinputtextnumber id=kmhm_awal name=kmhm_awal maxlength=8 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td><td>:</td><td>\n<input type=text class=myinputtextnumber id=kmhm_akhir name=kmhm_akhir maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td>\n    <td><select id=stn name=stn style=width:150px;>".$optSatuanvhc."</select>\n    <input type=hidden id=biaya name=biaya />\n</td></tr>\n<!--<tr><td>".$_SESSION['lang']['biaya']."</td><td>:</td><td>\n<input type=text class=myinputtextnumber id=biaya name=biaya maxlength=45 onkeypress=\"return angka_doang(event);\" style=width:150px; /> Rp</td></tr>-->\n\n<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td>\n<input type=text class=myinputtext id=ket name=ket maxlength=45 onkeypress=\"return tanpa_kutip(event);\" style=width:150px; /></td></tr>\n\n<tr><td colspan=3>\n<button class=mybutton onclick=save_pekerjaan() >".$_SESSION['lang']['save']."</button>\n<button class=mybutton onclick=bersih_form_pekerjaan() >".$_SESSION['lang']['cancel']."</button>\n<input type=hidden id=proses_pekerjaan name=proses_pekerjaan value=insert_pekerjaan />\n</table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>\n\t\t<thead>\n\t\t<tr class=\"rowheader\">\n\t\t<td>No.</td>\n\t\t<td  style=display:none>".$_SESSION['lang']['notransaksi']."</td>\n\t\t<td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>\n\t\t<td>".$_SESSION['lang']['alokasibiaya']."</td>\n\t\t<td>".$_SESSION['lang']['jumlahrit']."</td>\n\t\t<td>".$_SESSION['lang']['prestasi']."</td>\n                <td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>\n\t\t<td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>\n\t\t<td>".$_SESSION['lang']['satuan']."</td>\n\t\t<td style=display:none>".$_SESSION['lang']['biaya']." (Rp.)</td>\n\t\t<td>Action</td>\n\t\t</tr></thead><tbody id=containPekerja>\n\t\t";
$frm[1] .= '</tbody></table></fieldset>';
$optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$arrPos = ['Driver', 'Kenek'];
foreach ($arrPos as $brs => $isi) {
    $optPosition .= '<option value='.$brs.'>'.$isi.'</option>';
}
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['vhc_detail_operator'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\n<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td>\n<input type=text id=no_trans_opt name=no_trans_opt disabled=disabled class=myinputtext style=width:150px; /></td></tr>\n<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td>\n<select id=kode_karyawan name=kode_karyawan style=width:150px; onchange=\"getUmr()\">".$optKary."</select>\n<img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namakaryawan']."','4',event);\"  />\n</td></tr>\n<tr><td>".$_SESSION['lang']['vhc_posisi']."</td><td>:</td><td>\n<select id=posisi name=posisi style=width:150px;>".$optPosition."</select>\n</td></tr>\n<tr style='display:none'><td>".$_SESSION['lang']['upahkerja']."</td><td>:</td><td>\n<input type=text id=uphOprt name=uphOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' readonly /></td></tr>\n<tr><td>".$_SESSION['lang']['upahpremi']."</td><td>:</td><td>\n<input type=text id=prmiOprt name=prmiOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' readonly onfocus='getPremi()' /></td></tr>\n<tr><td>Premi Luar Jam Kerja</td><td>:</td><td>\n<input type=text id=prmiLuarJam name=prmiLuarJam class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' /></td></tr>\n<tr><td>".$_SESSION['lang']['rupiahpenalty']."</td><td>:</td><td>\n<input type=text id=pnltyOprt name=pnltyOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' value=0 /></td></tr>\n<tr><td>".$_SESSION['lang']['cucimobil']."</td><td>:</td><td>\n<input type=checkbox id=premiCuci /></td></tr>\n<tr><td colspan=3>\n<button class=mybutton onclick=save_operator() >".$_SESSION['lang']['save']."</button>\n<button class=mybutton onclick=clear_operator() >".$_SESSION['lang']['cancel']."</button>\n<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />\n\n</td></tr>\n</table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>\n\t\t<thead>\n\t\t<tr class=\"rowheader\">\n\t\t<td>No.</td>\n\t\t<td  style=display:none;>".$_SESSION['lang']['notransaksi']."</td>\n        <td>".$_SESSION['lang']['nik']."</td>\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\n\t\t<td>".$_SESSION['lang']['vhc_posisi']."</td>\n\t\t\n\t\t<td>".$_SESSION['lang']['upahpremi']."</td>\n        <td >".$_SESSION['lang']['premiluarjamkerja']."</td>\n\t\t<td>".$_SESSION['lang']['rupiahpenalty']."</td>\n                    <td>".$_SESSION['lang']['cucimobil']."</td>\n\t\t<td>Action</td>\n\t\t</tr></thead><tbody id=containOperator>\n\t\t<script>//load_data_operator()</script>\n\t\t";
$frm[2] .= '</tbody></table></fieldset>';
$hfrm[0] = $_SESSION['lang']['header'];
$hfrm[1] = $_SESSION['lang']['vhc_detail_pekerjaan'];
$hfrm[2] = $_SESSION['lang']['vhc_detail_operator'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
CLOSE_BOX();
echo close_body();

?>