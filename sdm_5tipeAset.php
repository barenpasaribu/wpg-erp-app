<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/tipeasset.js></script>\r\n";
include 'master_mainMenu.php';
$str = 'select noakundebet as noakun,keterangan as namaakun from '.$dbname.".keu_5parameterjurnal where kodeaplikasi='DEP' order by noakundebet";
$res = mysql_query($str);
$optAkun = "<option value=''></option>";
if (mysql_num_rows($res) < 1) {
    echo 'Error: Journal parameter for `DEP` not exist';
} else {
    while ($bar = mysql_fetch_object($res)) {
        $optAkun .= "<option value='".$bar->noakun."'>[".$bar->noakun.']-'.$bar->namaakun.'</option>';
    }
}

if ('EN' == $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$stru = 'select noakun,'.$zz.' from '.$dbname.'.keu_5akun';
$res = mysql_query($stru);
$optAkunak = '';
while ($bar = mysql_fetch_object($res)) {
    $namaakun[$bar->noakun] = $bar->namaakun;
    $optAkunak .= "<option value='".$bar->noakun."'>[".$bar->noakun.']-'.$bar->namaakun.'</option>';
}
$optTipeDep = getEnum($dbname, 'sdm_5tipeasset', 'metodepenyusutan');
$tipeDep = '';
foreach ($optTipeDep as $key => $val) {
    $tipeDep .= "<option value='".$val."'>".ucfirst($val).'</option>';
}
OPEN_BOX('', $_SESSION['lang']['tipeasset']);
echo "<fieldset style='width:600px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kode']."</td><td><input type=text id=kodetipe size=4 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['namakelompok']."</td><td><input type=text id=namatipe size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>\r\n\t <tr><td>".$_SESSION['lang']['namakelompok']."(EN)</td><td><input type=text id=namatipe1 size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr> \r\n\t <tr><td>Paramer Jurnal</td><td><select id=noakun>".$optAkun."</select></td></tr>\r\n     <tr><td>".'Aktiva Dalam Konstruksi'.'</td><td><select id=noakunak>'.$optAkunak."</select></td></tr>\r\n\t <tr><td>Metode Penyusutan</td><td><select id=tppenyusutan>".$tipeDep."</select></td></tr>\r\n     </table>\r\n\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanTipeAset()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelTipeAsset()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo '<div>';
$str1 = 'select * from '.$dbname.".sdm_5tipeasset \r\n\t\t   order by namatipe";
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:1000px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t <td style='width:150px;'>".$_SESSION['lang']['kode']."</td>\r\n\t\t <td>".$_SESSION['lang']['namakelompok']."</td>\r\n         <td>".$_SESSION['lang']['namakelompok']."(EN)</td>\r\n\t\t <td>Parameter Jurnal</td>\r\n         <td>".'Aktiva Dalam Konstruksi'."</td>\r\n\t\t <td>Metode Penyusutan</td>\r\n\t\t <td style='width:30px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody id=container>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t     <td align=center>".$bar1->kodetipe."</td>\r\n\t\t\t <td>".$bar1->namatipe."</td>\r\n             <td>".$bar1->namatipe1."</td>\r\n\t\t\t <td>".$namaakun[$bar1->noakun]."</td>\r\n             <td>".$namaakun[$bar1->akunak]."</td>\r\n\t\t\t <td>".ucfirst($bar1->metodepenyusutan)."</td>\r\n\t\t\t <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodetipe."','".$bar1->namatipe."','".$bar1->namatipe1."','".$bar1->noakun."','".$bar1->akunak."','".$bar1->metodepenyusutan."');\"></td></tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>