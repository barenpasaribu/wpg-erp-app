<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_matrixKompetensi.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'Matrix '.$_SESSION['lang']['kompetensi']);
$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'>".$rJabat['namajabatan'].'</option>';
}
echo "<fieldset style='width:500px;'>\r\n    <table>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td><select id=jabatan>".$optJabat."</select></td>\r\n        <td><button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixKompetensi.php');\">".$_SESSION['lang']['pdf']."</button></td>\r\n    </tr>\r\n    </table>\r\n    </fieldset>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>