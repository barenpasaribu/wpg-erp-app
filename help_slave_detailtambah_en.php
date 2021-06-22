<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/help_tambah.js\"></script>\r\n<link rel=stylesheet type=text/css href=style/generic.css>\r\n\r\n";
$proses = $_GET['proses'];
$param = $_GET;
$where = "kode='".$param['index']."' and modul='".$param['modul']."'";
$query = selectQuery($dbname, 'guidance_english', '*', $where);
$res = mysql_query($query);
while ($bar = mysql_fetch_object($res)) {
    $isi = $bar->isi;
    $html = $bar->tujuan;
}
$isi = str_replace('<##', "<image src='image/", $isi);
$isi = str_replace('##>', "'>", $isi);
$stream = (string) $isi;
echo '<fieldset><legend>'.$param['modul'].'</legend>';
echo $stream;
echo '<hr>';
$dd = str_replace('help/en', '', $html);
if ('null' === $dd) {
} else {
    include $html;
}

echo '</fieldset>';

?>