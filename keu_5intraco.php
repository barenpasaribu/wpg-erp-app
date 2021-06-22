<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/keu_5intraco.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['rekeningintraco']);
echo "<fieldset style='width:500px;'><table>\r\n     <tr><td>".$_SESSION['lang']['kodeorg'].'</td><td>';
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where char_length(kodeorganisasi)=4 order by kodeorganisasi';
$res = mysql_query($str);
$optpt = '';
$optpt .= "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.' - '.$bar->namaorganisasi.'</option>';
}
echo "<select id=kodeorg style='width:300px;'>".$optpt."</select></td></tr>\r\n     <tr><td>".$_SESSION['lang']['jenis'].'</td><td>';
$optjenis = '';
$arrjenis = getEnum($dbname, 'keu_5caco', 'jenis');
$optpt = '';
$optpt .= "<option value=''></option>";
foreach ($arrjenis as $kei => $fal) {
    $optpt .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<select id=jenis style='width:300px;'>".$optpt.'</select></td></tr>';
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$str = 'select noakun,'.$zz.' from '.$dbname.".keu_5akun where (noakun like '221%' or noakun like '122%' or noakun like '121%') and char_length(noakun)=7 order by noakun";
$res = mysql_query($str);
$optpt = '';
$optakun1 .= "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optakun1 .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';
    $namaakun[$bar->noakun] = $bar->namaakun;
}
echo '<tr><td>'.$_SESSION['lang']['piutang'].'</td><td>';
echo "<select id=akunpiutang style='width:300px;'>".$optakun1.'</select></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['hutang'].'</td><td>';
echo "<select id=akunhutang style='width:300px;'>".$optakun1."</select></td></tr>             \r\n     </table>\r\n\t <input type=hidden id=kodeorgbef value=''>\r\n\t <input type=hidden id=jenisbef value=''>\r\n\t <input type=hidden id=noakunbef value=''>\r\n\t <input type=hidden id=method value='insert'>\r\n\t <button class=mybutton onclick=simpanIntraco()>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=hapusIntraco()>".$_SESSION['lang']['delete']."</button>\r\n\t <button class=mybutton onclick=cancelIntraco()>".$_SESSION['lang']['cancel']."</button>\r\n\t </fieldset>";
echo open_theme($_SESSION['lang']['rekeningintraco']);
echo '<div id=container>';
$str1 = 'select * from '.$dbname.'.keu_5caco order by kodeorg, akunpiutang';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t \t<td style='width:100px;'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t<td style='width:40px;'>".$_SESSION['lang']['jenis']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['piutang']."</td>\r\n                        <td>".$_SESSION['lang']['hutang']."</td>\r\n\t\t\t<td style='width:30px;'>*</td>\r\n\t\t </tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
while ($bar1 = mysql_fetch_object($res1)) {
    echo "<tr class=rowcontent>\r\n\t\t\t<td align=center>".$bar1->kodeorg.'</td>';
    if ('inter' === $bar1->jenis) {
        echo '<td>Inter</td>';
    } else {
        echo '<td align=right>Intra</td>';
    }

    echo '<td>'.$bar1->akunpiutang.' - '.$namaakun[$bar1->akunpiutang]."</td>\r\n                             <td>".$bar1->akunhutang.' - '.$namaakun[$bar1->akunhutang]."</td>    \r\n\t\t\t<td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->akunpiutang."','".$bar1->akunhutang."');\"></td>\r\n\t\t</tr>";
}
echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>