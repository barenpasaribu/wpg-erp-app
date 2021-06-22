<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', 'MEDICAL PLAFOND');
$optGolongan = '';
$str = 'select * from '.$dbname.'. sdm_5golongan order by `kodegolongan` asc';
$res = mysql_query($str);
echo "<script type=\"text/javascript\" src=\"js/sdm_setup_plafond.js\"></script>\r\n<fieldset style=\"width:500px;\">\r\n<table>\r\n     <tr><td>";
echo $_SESSION['lang']['levelcode'];
echo "</td><td>\r\n\t ";
while ($bar = mysql_fetch_object($res)) {
    $optGolongan .= "<option value='".$bar->kodegolongan."'>".$bar->namagolongan.'</option>';
}
$optjenis = '';
$str = 'select * from '.$dbname.'.sdm_5jenisbiayapengobatan order by kode';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optjenis .= "<option value='".$bar->kode."'>".$bar->nama.'</option>';
}
echo "\t <select id=\"kodegolongan\" name=\"kodegolongan\">\r\n\t \t";
echo $optGolongan;
echo "\t </select>\r\n\t </td></tr>\r\n\t ";
echo '<tr><td>'.$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n\t        <td><select id=jenisbiaya>".$optjenis.'</select></td></tr>';
echo "\t\t \r\n\t <tr>\r\n\t \t<td>\r\n\t ";
echo $_SESSION['lang']['persen'];
echo '</td><td><input type="text" id="prsn" name="prsn" size="6" onkeypress="return angka_doang(event);" class="myinputtext" maxlength=3>%/';
echo $_SESSION['lang']['tahun'];
echo "</td></tr>\r\n     </table>\r\n\t <input type='hidden' id='method' value='insert'>\r\n\t <button class='mybutton' onclick='simpanPlafon()'>";
echo $_SESSION['lang']['save'];
echo "</button>\r\n\t <button class='mybutton' onclick='cancelPlafon()'>";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n\t </fieldset>\r\n";
echo open_theme($_SESSION['lang']['availavel']);
echo "<div>\r\n";
$str1 = 'select * from '.$dbname.'.sdm_pengobatanplafond order by kodegolongan';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['levelcode']."</td>\r\n\t\t <td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n\t\t <td>".$_SESSION['lang']['persen']."</td>\r\n\t\t <td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
echo "\t";
while ($bar1 = mysql_fetch_object($res1)) {
    echo '<tr class=rowcontent><td align=center>'.$bar1->kodegolongan."</td>\r\n\t\t<td align=center>".$bar1->kodejenisbiaya."</td>\r\n\t\t<td align=right>".$bar1->persen."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodegolongan."','".$bar1->persen."','".$bar1->kodejenisbiaya."');\"></td></tr>";
}
echo " \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>\r\n</div>\r\n\r\n\t";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>