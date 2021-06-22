<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_5matrixKompetensi.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'Matrix '.$_SESSION['lang']['kompetensi']);
$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan'].'</option>';
}
$arrJenis = getEnum($dbname, 'sdm_5matrikkompetensi', 'jenis');
$optJenis = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrJenis as $kei => $fal) {
    $optJenis .= "<option value='".$kei."'>".$fal.'</option>';
}
$arrItem = getEnum($dbname, 'sdm_5matrikkompetensi', 'item');
$optItem = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrItem as $kei => $fal) {
    $optItem .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<div id=header><fieldset style='width:500px;'><legend> Header: </legend>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan>".$optJabat."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['jenis']."</td>\r\n        <td><select id=jenis>".$optJenis."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>Item</td>\r\n        <td><select id=item>".$optItem."</select></td>\r\n    </tr>\r\n    </table>\r\n    <input type=hidden id=method value='insertheader'>\r\n    <button class=mybutton id=tombolsimpanheader onclick=simpanheader()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton id=tombolcancelheader onclick=cancelheader()>".$_SESSION['lang']['cancel']."</button>\r\n    </fieldset></div>";
$optUrut = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
for ($i = 1; $i <= 10; ++$i) {
    $optUrut .= "<option value='".$i."'>".$i.'</option>';
}
echo "<div id=detail style=\"display: none;\"><fieldset style='width:500px;'><legend> Detail: </legend>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['urutan']."</td>\r\n        <td><select id=nourut>".$optUrut."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kompetensi']."</td>\r\n        <td><input type=text class=myinputtext id=kompetensi onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['perilaku']."</td>\r\n        <td><textarea rows=2 cols=22 id=perilaku onkeypress=\"return tanpa_kutip();\"></textarea></td>\r\n    </tr>\r\n    </table>\r\n    <button class=mybutton onclick=simpandetail()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=canceldetail()>".$_SESSION['lang']['done']."</button>\r\n    </fieldset></div>";
echo open_theme($_SESSION['lang']['list']);
echo '<div id=container>';
echo "<table><tr>\r\n        <td>Item</td>\r\n        <td><select id=item2 onchange=pilihitem() disabled>".$optItem."</select> <img class=\"resicon\" src=\"images/pdf.jpg\" title=\"PDF\" onclick=\"lihatpdf(event,'sdm_slave_5matrixKompetensi.php')\"></td>\r\n    </tr></table>";
$limit = 20;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_5matrikkompetensi where kodejabatan like '%".$jabatan."%' and jenis like '%".$jenis."%' and item like '%".$item."%' order by kodejabatan, jenis, item, nourut";
$query2 = mysql_query($ql2);
while ($jsl = mysql_fetch_object($query2)) {
    $jlhbrs = $jsl->jmlhrow;
}
$str1 = 'select * from '.$dbname.".sdm_5matrikkompetensi where kodejabatan like '%".$jabatan."%' and jenis like '%".$jenis."%' and item like '%".$item."%' order by kodejabatan, jenis, item, nourut limit ".$offset.','.$limit.'';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['jenis']."</td>\r\n        <td>Item</td>\r\n        <td>".$_SESSION['lang']['urutan']."</td>\r\n        <td>".$_SESSION['lang']['kompetensi']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td>".$kamusJabat[$bar1->kodejabatan]."</td>\r\n        <td>".$bar1->jenis."</td>\r\n        <td>".$bar1->item."</td>\r\n        <td align=right>".$bar1->nourut."</td>\r\n        <td>".$bar1->tingkatkompetensi."</td>\r\n        <td align=center>\r\n            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->jenis."','".$bar1->item."','".$bar1->nourut."','".$bar1->tingkatkompetensi."','".str_replace("\n", '\\n', $bar1->prilaku)."');\">\r\n            <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"hapus('".$bar1->kodejabatan."','".$bar1->jenis."','".$bar1->item."','".$bar1->nourut."');\">\r\n        </td>\r\n    </tr>";
}
echo "<tr class=rowheader><td colspan=11 align=center>\r\n".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n<br />\r\n<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n</td>\r\n</tr>";
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>