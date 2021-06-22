<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sOrg = 'select kodeorganisasi,induk from '.$dbname.".organisasi where tipe='TRAKSI'";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    if ($rOrg['induk'] !== $lksiTugas) {
        echo 'warning:You Are Not In Traksi';
        exit();
    }
}
$frm[0] = '';
$frm[1] = '';
$frm[2] = '';
echo "\r\n<script type=\"text/javascript\" src=\"js/keu_anggaranTraksi.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/zMaster.js\"></script>\r\n<script>\r\ntmblSave='";
echo $_SESSION['lang']['save'];
echo "';\r\ntmblCancel='";
echo $_SESSION['lang']['cancel'];
echo "';\r\ntmblDone='";
echo $_SESSION['lang']['done'];
echo "';\r\n</script>\r\n";
$sVhc = 'select kodevhc from '.$dbname.'.vhc_5master order by tahunperolehan desc';
$qVHc = mysql_query($sVhc);
while ($rVhc = mysql_fetch_assoc($qVHc)) {
    $optVhc .= '<option value='.$rVhc['kodevhc'].'>'.$rVhc['kodevhc'].'</option>';
}
$isiOpt = [$_SESSION['lang']['dlm_perbaikan_rmh'], $_SESSION['lang']['dlm_baik_rmh'], $_SESSION['lang']['dlm_rusak_rmh']];
foreach ($isiOpt as $num => $teks) {
    $optKondisi .= '<option value='.$num.'>'.$teks.'</option>';
}
$optThn = '';
$thn_skrng = (int) (date('Y'));
for ($i = $thn_skrng; $thn_skrng - 30 <= $i; --$i) {
    $optThn .= "<option value='".$i."'>".$i.'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['anggaranTraksi'].'</b><br>');
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['tahunanggaran']."</td><td>:</td><td>\r\n<input type=text id=thnAnggaran name=thnAnggaran class=myinputtextnumber style=width:150px; onkeypress=\"return angka_doang(event);\" maxlength=4 /></td></tr>\r\n<tr><td>".$_SESSION['lang']['kodevhc'].'</td><td>:</td><td><select id=kdvhc name=kdvhc style=width:150px;>'.$optVhc."</select></td></tr>\r\n<tr><td>".$_SESSION['lang']['jmlhHariOperasi']."</td><td>:</td><td>\r\n<input type=text id=jmlhHari name=jmlhHari class=myinputtextnumber style=width:150px; onkeypress=\"return angka_doang(event);\" maxlength=4 /></td></tr>\r\n<tr><td>".$_SESSION['lang']['pemakaianHmKm']."</td><td>:</td><td>\r\n<input type=text class=myinputtextnumber id=pemakaianHm name=pemakaianHm maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\r\n<tr><td>".$_SESSION['lang']['jmlhHariTdkOpr']."</td><td>:</td><td>\r\n<input type=text class=myinputtextnumber id=jmlhHariTdk name=tipe_rmh maxlength=8 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>\r\n\r\n<tr><td colspan=3 id=tmbLhead><script>shwTmbl()</script></td></tr>\r\n</table><input type=hidden id=proses name=proses value='insert' />";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n         <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['tahunanggaran']."</td>\r\n\t\t<td>".$_SESSION['lang']['kodevhc']."</td>\r\n\t\t<td>".$_SESSION['lang']['jmlhHariOperasi']."</td>\r\n\t\t<td>".$_SESSION['lang']['pemakaianHmKm']."</td>\r\n\t\t<td>".$_SESSION['lang']['jmlhHariTdkOpr']."</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=contain>\r\n\t\t<script>loadData()</script>\r\n\t\t";
$frm[0] .= '</tbody></table></fieldset>';
$optBrg = '';
$sBrg = 'select kodebarang,namabarang from '.$dbname.'.log_5masterbarang order by namabarang asc';
$qBrg = mysql_query($sBrg);
while ($rBrg = mysql_fetch_assoc($qBrg)) {
    $optBrg .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';
}
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['entryForm'].'</legend>';
$frm[1] .= "<table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t<td></td>\r\n\t\t</tr></thead>\r\n\t\t<tbody><tr class=rowcontent>\r\n\t\t<td><select id=kdBrg name=kdBrg style='width:150px'>".$optBrg."</select><input type=hidden id=oldKdbrg name=oldKdbrg /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlh name=jmlh maxlength=4 value='0' onkeypress=\"return angka_doang(event);\" style='width:150px'/></td>\r\n\t\t<td><button class=mybutton onclick=saveDetail() >".$_SESSION['lang']['save'].'</button><button class=mybutton onclick=clearDetail() >'.$_SESSION['lang']['cancel']."</button></td>\r\n</tr></tbody></table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n        <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=containDetailTraksi>\r\n\t\t<script>loadDetail();</script>\t\t";
$frm[1] .= '</tbody></table></fieldset><input type=hidden id=pros name=pros value=insertDetail />';
$optOrg = '';
$sOrg = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4';
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= '<option value='.$rOrg['kodeorganisasi'].'>'.$rOrg['namaorganisasi'].'</option>';
}
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['entryForm'].'</legend>';
$frm[2] .= "<table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['jmlhMeter']."</td>\r\n\t\t<td>Jan</td>\r\n\t\t<td>Feb</td>\r\n\t\t<td>Mar</td>\r\n\t\t<td>Apr</td>\r\n\t\t<td>Mei</td>\r\n\t\t<td>Jun</td>\r\n\t\t<td>Jul</td>\r\n\t\t<td>Aug</td>\r\n\t\t<td>Sep</td>\r\n\t\t<td>Okt</td>\r\n\t\t<td>Nov</td>\r\n\t\t<td>Des</td>\r\n\t\t<td></td>\r\n\t\t</tr></thead>\r\n\t\t<tbody>\r\n\t\t<td><select id=kdOrg name=kdOrg>".$optOrg."</select></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhMeter name=jmlhMeter  onkeypress=\"return angka_doang(event);\" value='0' /> </td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhJan name=jmlhJan  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /> </td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhFeb name=jmlhFeb  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhMar name=jmlhMar  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhApr name=jmlhApr  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhMei name=jmlhMei  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhJun name=jmlhJun  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhJul name=jmlhJul  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhAug name=jmlhAug  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhSep name=jmlhSep  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhOkt name=jmlhOkt  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhNov name=jmlhNov  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><input type=text class=myinputtextnumber id=jmlhDes name=jmlhDes  onkeypress=\"return angka_doang(event);\" value='0' style='width:30px' maxlength=4 /></td>\r\n\t\t<td><button class=mybutton onclick=saveAlokasi() >".$_SESSION['lang']['save'].'</button><button class=mybutton onclick=clearAlokasi() >'.$_SESSION['lang']['cancel']."</button></td>\r\n\t\t</tbody></table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n      <table class=sortable cellspacing=1 border=0>\r\n\t\t<thead>\r\n\t\t<tr class=\"rowheader\">\r\n\t\t<td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td>".$_SESSION['lang']['jmlhMeter']."</td>\r\n\t\t<td>Jan</td>\r\n\t\t<td>Feb</td>\r\n\t\t<td>Mar</td>\r\n\t\t<td>Apr</td>\r\n\t\t<td>Mei</td>\r\n\t\t<td>Jun</td>\r\n\t\t<td>Jul</td>\r\n\t\t<td>Aug</td>\r\n\t\t<td>Sep</td>\r\n\t\t<td>Okt</td>\r\n\t\t<td>Nov</td>\r\n\t\t<td>Des</td>\r\n\t\t<td>Action</td>\r\n\t\t</tr></thead><tbody id=containAlokasi>\r\n\t\t<script>loadaLokasi();</script>\r\n\t\t";
$frm[2] .= '</tbody></table></fieldset><input type=hidden id=prosAlokasi name=prosAlokasi value=insertAlokasi />';
$hfrm[0] = $_SESSION['lang']['header'];
$hfrm[1] = $_SESSION['lang']['anggaranTraksiDetail'];
$hfrm[2] = $_SESSION['lang']['anggaranTraksiAlokasi'];
drawTab('FRM', $hfrm, $frm, 220, 930);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>