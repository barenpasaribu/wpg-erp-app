<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['permintaanlayanan'].'</b>');
echo "<script languange=javascript1.2 src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script languange=javascript1.2 src='js/formReport.js'></script>\r\n<script languange=javascript1.2 src='js/zGrid.js'></script>\r\n<script type=\"text/javascript\" src=\"js/it_permintaanUser.js\"></script>\r\n";
$opt_jenis_layanan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$s_jenis_layanan = 'select kodekegiatan,keterangan from '.$dbname.'.it_standard order by kodekegiatan asc';
$q_jenis_layanan = mysql_query($s_jenis_layanan) || exit(mysql_error($conns));
while ($r_jenis_layanan = mysql_fetch_assoc($q_jenis_layanan)) {
    $opt_jenis_layanan .= "<option value='".$r_jenis_layanan['kodekegiatan']."'>".$r_jenis_layanan['keterangan'].'</option>';
}
$opt_karyawan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$s_karyawan = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan\r\n             where tipekaryawan='5' and karyawanid not like '".$_SESSION['standard']['userid']."' order by namakaryawan asc";
$q_karyawan = mysql_query($s_karyawan) || exit(mysql_error($conns));
while ($r_karyawan = mysql_fetch_assoc($q_karyawan)) {
    $opt_karyawan .= "<option value='".$r_karyawan['karyawanid']."'>".$r_karyawan['namakaryawan'].'</option>';
}
echo "<div id=\"add\">\r\n<fieldset style='float:left;'>\r\n<legend>";
echo $_SESSION['lang']['form'].' '.$_SESSION['lang']['permintaanlayanan'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" style=\"width:100px;\">\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['jenislayanan'];
echo "</td><td>:</td>\r\n        <td><select id='jenislayanan' style=\"width:150px;\">";
echo $opt_jenis_layanan;
echo "</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['deskripsi'].'/'.$_SESSION['lang']['keluhan'];
echo "</td><td>:</td>\r\n        <td><textarea rows=\"5\" cols=\"50\" id='deskripsi' onkeypress=\"return tanpa_kutip();\" /></textarea></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['atasan'];
echo "</td><td>:</td>\r\n        <td><select id='atasan' style=\"width:150px;\">";
echo $opt_karyawan;
echo "</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['managerit'];
echo "</td><td>:</td>\r\n        <td><select id='managerit' style=\"width:150px;\">";
echo $opt_karyawan;
echo "</select></td>\r\n    </tr>\r\n    <td colspan=\"3\" id=\"tombol\" align=\"center\">\r\n        <button class=mybutton id=saveForm onclick=saveForm()>";
echo $_SESSION['lang']['save'];
echo "</button>\r\n    </td>\r\n    </tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "<fieldset style='float:left;'>\r\n    <legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n     <table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n        <thead>\r\n            <tr class=\"rowheader\">\r\n            <td align=\"center\">No.</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['namakegiatan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['namakaryawan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['atasan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['status'].' '.$_SESSION['lang']['atasan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['atasan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['status'].' '.substr($_SESSION['lang']['managerit'], 0, 7);
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['pelaksana'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['waktupelaksanaan'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['waktu'].' '.$_SESSION['lang']['selesai'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['kepuasanuser'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['nilai'].' '.$_SESSION['lang']['komunikasi'];
echo "</td>\r\n            <td align=\"center\" colspan=\"2\">";
echo $_SESSION['lang']['saran'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['saran'].' '.$_SESSION['lang']['pelaksana'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['lihat'];
echo "</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody id=\"contain\">\r\n";
$limit = 25;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$sCount = 'select count(*) as jmlhrow from '.$dbname.'.it_request order by notransaksi asc';
$qCount = mysql_query($sCount) || exit(mysql_error($conns));
while ($rCount = mysql_fetch_object($qCount)) {
    $jmlbrs = $rCount->jmlhrow;
}
echo '<script>loaddata()</script>';
echo "<tr class=rowheader><td colspan=15 align=center>\r\n    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jmlbrs."<br />\r\n    <button class=mybutton onclick=pages(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n    <button class=mybutton onclick=pages(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n    </td>\r\n    </tr>";
echo "</tbody></table></fieldset>\r\n";
CLOSE_BOX();

?>