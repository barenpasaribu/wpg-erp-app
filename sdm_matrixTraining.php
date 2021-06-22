<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/sdm_matrixTraining.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['matrikstraining']);
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
}
$sJabat = 'select distinct * from '.$dbname.'.datakaryawan where tipekaryawan = 0';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusNama[$rJabat['karyawanid']] = $rJabat['namakaryawan'];
    $kamusJabatan[$rJabat['karyawanid']] = $rJabat['kodejabatan'];
    $kamusLokasi[$rJabat['karyawanid']] = $rJabat['lokasitugas'];
    $kamusDept[$rJabat['karyawanid']] = $rJabat['bagian'];
}
$optJenis = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJenis = 'select * from '.$dbname.'. sdm_5matriktraining order by kategori, topik asc';
$qJenis = mysql_query($sJenis);
while ($rJenis = mysql_fetch_assoc($qJenis)) {
    $optJenis .= "<option value='".$rJenis['matrixid']."'>".$rJenis['kategori'].' - '.$rJenis['topik'].'</option>';
}
echo "<fieldset style='width:700px;'>\r\n    <table>\r\n    <tr>\r\n        <td>Jenis Training</td>\r\n        <td><select id=matrixid onchange=pilihkaryawan()>".$optJenis."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggalmulai']."</td>\r\n        <td><input id=\"tanggal1\" name=\"tanggal1\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['tanggalsampai']."</td>\r\n        <td><input id=\"tanggal2\" name=\"tanggal2\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>\r\n    </tr>\r\n    <tr>\r\n        <td>".$_SESSION['lang']['karyawan']."</td>\r\n        <td><div id=container></div></td>\r\n    </tr>\r\n    </table>\r\n    </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo '<div id=icontainer>';
$str1 = 'select * from '.$dbname.'.sdm_matriktraining where 1 order by karyawanid';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td>".$_SESSION['lang']['departemen']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td>".$kamusNama[$bar1->karyawanid]."</td>\r\n        <td>".$kamusLokasi[$bar1->karyawanid]."</td>\r\n        <td>".$kamusDept[$bar1->karyawanid]."</td>\r\n        <td>".$kamusJabat[$kamusJabatan[$bar1->karyawanid]]."</td>\r\n        <td align=center>\r\n            <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixTraining.php','".$bar1->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>\r\n        </td>\r\n    </tr>";
}
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>";
echo close_theme();
CLOSE_BOX();
echo close_body();

?>