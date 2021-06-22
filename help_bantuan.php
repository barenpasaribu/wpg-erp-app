<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['bantu'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/help_bantuan.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n\r\n<div id=\"list_ganti\">\r\n";
OPEN_BOX();
echo "<div id=\"action_list\">\r\n\r\n</div>\r\n<fieldset style='float:left;'>\r\n    <legend>";
echo $_SESSION['lang']['find'].' '.$_SESSION['lang']['bantu'];
echo "</legend>\r\n     <table cellspacing=\"1\" border=\"0\">\r\n         <tr>\r\n            <td>";
echo $_SESSION['lang']['find'];
echo "</td><td>:</td>\r\n            <td>\r\n                <input type='text' class='myinputtext' id='cariindex' onkeypress=\"return tanpa_kutip();\"  size='10' maxlength='30'  style=\"width:150px;\" />\r\n                <button class=mybutton id='cari' onclick=cariHelp()>";
echo $_SESSION['lang']['find'];
echo "</button>\r\n            </td>\r\n         </tr>\r\n     </table>\r\n    <table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n        <thead>\r\n           \r\n            <tr class=\"rowheader\">\r\n            <td align=\"center\">No.</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['index'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['modul'];
echo "</td>\r\n            <td align=\"center\">";
echo $_SESSION['lang']['tentang'];
echo "</td>\r\n            <td colspan=\"3\" align=\"center\">";
echo $_SESSION['lang']['action'];
echo "</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody id=\"contain\">\r\n        ";
$limit = 10;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$q = 'select count(*) as jmlhrow from '.$dbname.'.guidance order by kode asc';
$query = mysql_query($q) || exit(mysql_error($conns));
while ($jsl = mysql_fetch_object($query)) {
    $jmlbrs = $jsl->jmlhrow;
}
$q2 = 'select * from '.$dbname.'.guidance order by kode asc,tentang,modul,isi limit '.$offset.','.$limit.' ';
$query2 = mysql_query($q2) || exit(mysql_error($conns));
while ($row = mysql_fetch_assoc($query2)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n                <td id='no'>".$no."</td>\r\n                <td id='index_".$row['kode']."' value='".$row['kode']."' align='center'>".$row['kode']."</td>\r\n                <td id='modul_".$row['kode']."' value='".$row['modul']."'>".$row['modul']."</td>\r\n                <td id='tentang_".$row['kode']."' value='".$row['tentang']."'>".$row['tentang']."</td>\r\n                <td><img onclick=\"detailHelp(event,'".str_replace(' ', '', $row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td></tr>";
}
echo "<tr class=rowheader><td colspan=5 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jmlbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
echo "\r\n        </tbody>\r\n    </table>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>