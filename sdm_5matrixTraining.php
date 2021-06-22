<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_5matrixTraining.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['matrikstraining']);
$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan'].'</option>';
}
$arrKateg = getEnum($dbname, 'sdm_5matriktraining', 'kategori');
$optKateg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrKateg as $kei => $fal) {
    $optKateg .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<fieldset style='width:500px;'>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan>".$optJabat."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kategori']."</td>\r\n        <td><select id=kategori>".$optKateg."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['topik']."</td>\r\n        <td><input type=text class=myinputtext id=topik onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['remark']."</td>\r\n        <td><input type=text class=myinputtext id=remark onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>\r\n    </tr>\r\n    </table>\r\n    <input type=hidden id=method value='insert'>\r\n    <input type=hidden id=matrixid value=''>    \r\n    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button>\r\n    </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo '<div id=container>';
echo "<table><tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select></td>\r\n    </tr></table>";
$str1 = 'select * from '.$dbname.'.sdm_5matriktraining order by kodejabatan, kategori, topik';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['kategori']."</td>\r\n        <td>".$_SESSION['lang']['topik']."</td>\r\n        <td>".$_SESSION['lang']['catatan']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td>".$kamusJabat[$bar1->kodejabatan]."</td>\r\n        <td>".$bar1->kategori."</td>\r\n        <td>".$bar1->topik."</td>\r\n        <td>".$bar1->catatan."</td>\r\n        <td align=center>\r\n            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kategori."','".$bar1->topik."','".$bar1->catatan."','".$bar1->matrixid."');\">\r\n            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->matrixid."');\">\r\n        </td>\r\n    </tr>";
}
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>