<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_5kriteriaPsy.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']);
$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan'].'</option>';
}
$arrKrite = getEnum($dbname, 'sdm_5kriteriapsy', 'kriteria');
$optKrite = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrKrite as $kei => $fal) {
    $optKrite .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<fieldset style='width:500px;'>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan>".$optJabat."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['kriteria']."</td>\r\n        <td><select id=kriteria>".$optKrite."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['deskripsi']."</td>\r\n        <td><textarea rows=2 cols=22 id=deskripsi onkeypress=\"return tanpa_kutip();\"></textarea></td>\r\n    </tr>\r\n    </table>\r\n    <input type=hidden id=method value='insert'>\r\n    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=cancel()>".$_SESSION['lang']['cancel']."</button>\r\n    </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo '<div id=container>';
echo "<table><tr>\r\n        <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n        <td><select id=jabatan2 onchange=pilihjabatan()>".$optJabat."</select> <img class=\"resicon\" src=\"images/pdf.jpg\" title=\"PDF\" onclick=\"lihatpdf(event,'sdm_slave_5kriteriaPsy')\"></td>\r\n    </tr></table>";
$str1 = 'select * from '.$dbname.'. sdm_5kriteriapsy order by kodejabatan, kriteria';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['nourut']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['kriteria']."</td>\r\n        <td>".$_SESSION['lang']['deskripsi']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td align=right>".$no."</td>\r\n        <td>".$kamusJabat[$bar1->kodejabatan]."</td>\r\n        <td>".$bar1->kriteria."</td>\r\n        <td>".substr(str_replace("\n", '</br>', $bar1->penjelasan), 0, 75)."</td>\r\n        <td align=center>\r\n            <img src=images/application/application_view_list.png class=resicon  caption='Preview' onclick=\"lihat('".$bar1->kodejabatan."','".$bar1->kriteria."',event);\">\r\n            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->kriteria."','".str_replace("\n", '\\n', $bar1->penjelasan)."');\">\r\n            <img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"hapus('".$bar1->kodejabatan."','".$bar1->kriteria."');\">\r\n        </td>\r\n    </tr>";
}
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>