<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zForm.php';
echo open_body();
echo "<script language=javascript1.2 src='js/asset.js'></script>\r\n";
include 'master_mainMenu.php';
$limit = 20;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$str = "select a.*\t\t  \r\n                  from ".$dbname.".sdm_daftarasset a\r\n                  where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'";
$res = mysql_query($str);
$jlhbrs = mysql_num_rows($res);
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where \r\n      tipe in('HOLDING','KEBUN','KANWIL','PABRIK')\r\n          and kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n          order by namaorganisasi desc";
$res = mysql_query($str);
$optOrg = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optOrg .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'</option>';
}
$str = ' select * from '.$dbname.'.sdm_5tipeasset order by namatipe';
$res = mysql_query($str);
$optAss = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    if ('EN' == $_SESSION['language']) {
        $optAss .= "<option value='".$bar->kodetipe."'>".$bar->namatipe1.'</option>';
    } else {
        $optAss .= "<option value='".$bar->kodetipe."'>".$bar->namatipe.'</option>';
    }
}
$optper = "<option value=''></option>";
for ($x = 0; $x <= 250; ++$x) {
    $d = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $da = date('Y-m', $d);
    $di = date('m-Y', $d);
    $optper .= "<option value='".$da."'>".$di.'</option>';
}
$optStat = "<option value='1'>".$_SESSION['lang']['aktif'].'</option>';
$optStat .= "<option value='0'>".$_SESSION['lang']['rusak'].' / ' .$_SESSION['lang']['pensiun'].'</option>';
$optStat .= "<option value='3'>".$_SESSION['lang']['hilang'].'</option>';
$optStat .= "<option value='2'> Dijual </option>";
$optLeas = "<option value='0'>Not Leasing</option>";
$optLeas .= "<option value='1'>Leasing</option>";
$optLeas .= "<option value='2'>Ex-Leasing</option>";
$kamusleasing[0] = 'Not Leasing';
$kamusleasing[1] = 'Leasing';
OPEN_BOX('', '');
echo "<table>\r\n     <tr valign=middle>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>\r\n           <img class=delliconBig src=images/plus.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n           <img class=delliconBig src=images/book_icon.gif title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n         <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['caripadanama'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo '<button class=mybutton onclick=cariAsset(0)>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n         </table> ";
CLOSE_BOX();
OPEN_BOX('', $_SESSION['lang']['aset']);
$dmn = "char_length(kodeorganisasi)='4'";
$orgOption = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $dmn, '2');
$frm[0] = "<fieldset>\r\n\t<legend>".$_SESSION['lang']['inputaset']."</legend>\r\n    <table>\r\n\t\t<tr>\r\n\t\t\t<td>\r\n\t\t\t".$_SESSION['lang']['kodeorganisasi'].'</td><td><select id=kodeorg>'.$optOrg."</select></td>\r\n\t\t\t</td>\r\n\t\t\t<td></td><td><input type=hidden id=penambah class=myinputtextnumber  onkeypress=\"return angka_doang(event)\" size=20 />\r\n\t\t\t</td>\r\n\t\t</tr>\r\n        <tr>\r\n\t\t\t<td>".$_SESSION['lang']['nourut']."</td><td><input type=text id=kodeaset maxlength=20 class=myinputtext onkeypress=\"return angka_doang(event)\" size=20 disabled></td>\r\n\t\t\t</td>\r\n\t\t\t<td></td><td><input type=hidden id=pengurang class=myinputtextnumber  onkeypress=\"return angka_doang(event)\" size=20 />\r\n\t\t\t</td>\r\n\t\t</tr>\r\n        <tr>\r\n\t\t\t<td>".$_SESSION['lang']['tahunperolehan']."</td><td><input type=text id=tahunperolehan  class=myinputtextnumber  onkeypress=\"return angka_doang(event);\" size=5 maxlength=4></td>\r\n\t\t\t</td>\r\n\t\t\t<td>".$_SESSION['lang']['namakelompok'].'</td><td><select id=tipe onchange=cek(this)>'.$optAss."</select>\r\n\t\t\t</td>\r\n\t\t</tr>\r\n        <tr>\r\n\t\t\t<!--td>".$_SESSION['lang']['hargaperolehan']."</td><td><input type=text value=0 class=myinputtextnumber id=nilaiperolehan onkeypress=\"return angka_doang(event);\" size=12 maxlength=15></td>\r\n\t\t\t</td-->\r\n            <td>".$_SESSION['lang']['hargaperolehan']."</td><td><input type=text value=0 class=myinputtextnumber id=nilaiperolehan onkeypress=\"return angka_doang(event);\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this);\" size=12 maxlength=15></td>\r\n\t\t\t</td>\r\n\t\t\t<td>".$_SESSION['lang']['namaaset']."</td><td><input type=text id=kodebarang onkeypress=\"return false;\" onclick=\"showWindowBarang('Cari Barang',event);\" class=myinputtext size=10 maxlength=11> <input type=text id=namaaset maxlength=45 class=myinputtext onkeypress=\"return tanpa_kutip(event)\" size=40>\r\n\t\t\t</td>\r\n\t\t</tr>\r\n        <tr>\r\n\t\t\t<td>".$_SESSION['lang']['awalpenyusutan'].'</td><td><select id=bulanawal>'.$optper."</select></td>\r\n\t\t\t</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlahbulanpenyusutan']."</td>\r\n\t\t\t<td>\r\n\t\t\t\t<input type=text value=0 class=myinputtextnumber id=jumlahbulan onkeypress=\"return angka_doang(event);\" size=5 maxlength=3>\r\n\t\t\t\t/\r\n\t\t\t\t<input type=text value=0 class=myinputtextnumber id=persendecline onkeypress=\"return angka_doang(event);\" size=5 maxlength=3 >%\r\n\t\t\t</td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td>".$_SESSION['lang']['status'].'</td><td><select id=status>'.$optStat."</select></td>\r\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td><td><input type=text class=myinputtext  id=keterangan size=40 maxlenth=100  onkeypress=\"return tanpa_kutip(event)\"></td>\r\n\t\t</tr>\r\n        <tr><td>Leasing</td><td><select id=leasing>".$optLeas."</select></td>\r\n\t\t\t<td>Ref. Pembayaran</td><td>".makeElement('refbayar', 'text', '', ['maxlength' => 25])."</td>\r\n\t\t</tr>\r\n\t\t<tr><td>".$_SESSION['lang']['nodokpengadaan'].'</td><td>'.makeElement('nodokpengadaan', 'text', '', ['maxlength' => 25])."</td>\r\n                    <td>".$_SESSION['lang']['posisiasset'].'</td><td>'.makeElement('posisiasset', 'select', '', ['style' => 'width:300px'], $orgOption)."</td>\r\n\t\t</tr>\r\n\t</table>\r\n    <input type=hidden value=insert id=method>\r\n    <button class=mybutton onclick=simpanAssetBaru()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=cancelAsset()>".$_SESSION['lang']['cancel']."</button>\r\n</fieldset>";
$frm[1] = '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>\r\n         <div style='height:400px;overflow:scroll;'>\r\n                 <table class=sortable  border=0 cellspacing=1>\r\n                 <thead>\r\n                   <tr class=rowheader>\r\n                      <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n                          <td align=center>".$_SESSION['lang']['kodeorganisasi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['posisiasset']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namakelompok']."</td>\r\n                          <td align=center>".$_SESSION['lang']['kodeasset']."</td>\r\n                          <td align=center>".$_SESSION['lang']['namaaset']."</td>\r\n                          <td align=center>".$_SESSION['lang']['tahunperolehan']."</td>\r\n                          <td align=center>".$_SESSION['lang']['status']."</td>\r\n                          <td align=center>".$_SESSION['lang']['hargaperolehan']."</td>\r\n                          <td width=20 align=center>".$_SESSION['lang']['jumlahbulanpenyusutan']."</td>\r\n\t\t\t\t\t\t  <td align=center>".$_SESSION['lang']['persendecline']."</td>\r\n                          <td align=center>".$_SESSION['lang']['awalpenyusutan']."</td>\r\n                          <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n                          <td align=center>Leasing</td>\r\n                          <td align=center>*</td>\r\n                   </tr>\r\n                   </thead>\t\t   \r\n                 <tbody id=containeraset>\r\n                   ";
if ('EN' == $_SESSION['language']) {
    $ads = 'b.namatipe1 as namatipe';
} else {
    $ads = 'b.namatipe as namatipe';
}

$str = 'select a.*,'.$ads.", CASE a.status when 0 then 'rusak tidak dapat dipakai/ pensiun' when 1 then '".$_SESSION['lang']['aktif']."' when 2 then 'Dijual' when 3 then '".$_SESSION['lang']['hilang']."' else 'Unknown' END as stat from ".$dbname.".sdm_daftarasset a left join  ".$dbname.".sdm_5tipeasset b on a.tipeasset=.b.kodetipe where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' order by tahunperolehan desc,awalpenyusutan desc,namatipe asc limit ".$offset.','.$limit;
$res = mysql_query($str);
$no = $offset;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $frm[1] .= " <tr class=rowcontent>\r\n                  <td>".$no."</td>\r\n                      <td width=10 align=center>".$orgOption[$bar->kodeorg]."</td>\r\n                          <td>".$orgOption[$bar->posisiasset]."</td>\r\n                          <td>".$bar->namatipe."</td>\r\n                          <td width=20 align=center>".$bar->kodeasset."</td>\r\n                          <td>".$bar->namasset."</td>\r\n                          <td width=20 align=center>".$bar->tahunperolehan."</td>\r\n                          <td width=20 align=center>".$bar->stat."</td>\r\n                          <td width=100 align=right>".number_format($bar->hargaperolehan, 2, '.', ',')."</td>\r\n                          <td width=20 align=right>".$bar->jlhblnpenyusutan."</td>\r\n\t\t\t\t\t\t  <td align=right>".$bar->persendecline."</td>\r\n                          <td width=20 align=center>".substr($bar->awalpenyusutan, 5, 2).'-'.substr($bar->awalpenyusutan, 0, 4)."</td>\r\n                          <td>".$bar->keterangan."</td>\r\n                          <td>".$kamusleasing[$bar->leasing]."</td>\r\n                          <td>\r\n                           <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAsset('".trim($bar->kodeorg)."','".$bar->tipeasset."','".$bar->kodeasset."','".$bar->namasset."','".$bar->kodebarang."','".$bar->tahunperolehan."','".$bar->status."','".$bar->hargaperolehan."','".$bar->jlhblnpenyusutan."','".$bar->awalpenyusutan."','".$bar->keterangan."','".$bar->leasing."','".$bar->penambah."','".$bar->pengurang."','".$bar->refbayar."','".$bar->dokpengadaan."','".$bar->persendecline."','".$bar->posisiasset."');\">\r\n                      &nbsp <!--<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAsset('".$bar->kodeorg."','".$bar->kodeasset."');\">-->\r\n                          </td>\r\n                   </tr>";
}
$frm[1] .= "<tr><td colspan=12 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n           <br>\r\n       <button class=mybutton onclick=cariAsset(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n           <button class=mybutton onclick=cariAsset(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n           </td>\r\n           </tr>";
$frm[1] .= "\r\n                 </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\r\n                 </table>\r\n                 </div>\r\n                 </fieldset>\r\n                ";
$hfrm[0] = $_SESSION['lang']['inputaset'];
$hfrm[1] = $_SESSION['lang']['list'];
drawTab('FRM', $hfrm, $frm, 200, 1000);
CLOSE_BOX();
echo close_body();

?>