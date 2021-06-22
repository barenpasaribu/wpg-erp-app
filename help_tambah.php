<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['tambah'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/help_tambah.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n\r\n<div id=\"tambah\">\r\n<fieldset style='float:left;'>\r\n<legend>";
echo $_SESSION['lang']['form'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" style=\"width:1000px;\">\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['index'];
echo "</td><td>:</td>\r\n        <td><input disabled type='text' class='myinputtext' id='index'  size='10' maxlength='35' style=\"width:200px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['tentang'];
echo "</td><td>:</td>\r\n        <td><input type='text' class='myinputtext' id='tentang' onkeypress=\"return tanpa_kutip();\"  size='10' style=\"width:200px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['modul'];
echo "</td><td>:</td>\r\n        <td><input type='text' class='myinputtext' id='modul' onkeypress=\"return tanpa_kutip();\"  size='10' maxlength='35' style=\"width:200px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['isi'];
echo "</td><td>:</td>\r\n        <td>\r\n            <!--<textarea rows=\"2\" cols=\"22\" id='isi' onkeypress=\"return tanpa_kutip();\" /></textarea>-->\r\n            <script type=\"text/javascript\" src=\"fckeditor/fckeditor.js\"></script>\r\n            <script type=\"text/javascript\" src=\"fckeditor/fckconfig.js\"></script>\r\n            <script type=\"text/javascript\">\r\n                var oFCKeditor = new FCKeditor('isi');\r\n//                oFCKeditor.BasePath = \"http://localhost/fckeditor/\";\r\n                oFCKeditor.BasePath = \"fckeditor/\";\r\n                oFCKeditor.SkinPath = oFCKeditor.BasePath + 'skins/office2003/';\r\n                oFCKeditor.width = 1000;\r\n                oFCKeditor.height= 500;\r\n                oFCKeditor.Value   = \"";
echo rtrim(str_replace('"', "'", $row['isi']));
echo "\";\r\n                oFCKeditor.Create();\r\n            </script>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td>";
echo $_SESSION['lang']['html'];
echo "</td><td>:</td>\r\n        <td><input type='text' class='myinputtext' id='html' onkeypress=\"return tanpa_kutip();\"  size='10' style=\"width:200px;\" value=\"help/\" /></td>\r\n    </tr>\r\n    <tr>\r\n    <td colspan=\"3\" id=\"tmblHeader\">\r\n        <button class=mybutton id=saveForm onclick=saveForm()>";
echo $_SESSION['lang']['save'];
echo "</button>\r\n        <button class=mybutton id=cancelForm onclick=cancelForm()>";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n    </td>\r\n    </tr>\r\n</table><input type=\"hidden\" id=\"hiddenz\" name=\"hiddenz\" />\r\n</fieldset>\r\n</div>\r\n";
CLOSE_BOX();
OPEN_BOX();
echo "<fieldset style='float:left;'>\r\n    <legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n     <table cellspacing=\"1\" border=\"0\">\r\n         <tr>\r\n            <td>";
echo $_SESSION['lang']['find'];
echo "</td><td>:</td>\r\n            <td><input type='text' class='myinputtext' id='cariindex' onkeypress=\"return tanpa_kutip();\"  size='10' maxlength='35' style=\"width:150px;\" />\r\n                <button class=mybutton id='cari' onclick=cariHelp()>";
echo $_SESSION['lang']['find'];
echo "</button></td>\r\n         </tr>\r\n     </table>\r\n    <table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n        <thead>\r\n            <tr class=\"rowheader\">\r\n            <td align=\"center\">No.</td>\r\n            <td align=\"center\">";
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
$sCount = 'select count(*) as jmlhrow from '.$dbname.'.guidance order by `kode` asc';
$qCount = mysql_query($sCount) || exit(mysql_error($conns));
while ($rCount = mysql_fetch_object($qCount)) {
    $jmlbrs = $rCount->jmlhrow;
}
$sShow = 'select * from '.$dbname.'.guidance order by kode asc,tentang,modul,isi limit '.$offset.','.$limit.' ';
$qShow = mysql_query($sShow) || exit(mysql_error($conns));
while ($row = mysql_fetch_assoc($qShow)) {
    ++$no;
    echo '<script>loadNData()</script>';
    echo "<td><img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(["\r", "\n"], '\\n', $row['isi'])."','".$row['tujuan']."');\" ></td>";
    echo "<td><img onclick=\"detailHelp(event,'".str_replace(' ', '', $row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>";
    echo "<td><img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(["\r", "\n"], '\\n', $row['isi'])."')></td></tr>";
}
echo "<tr class=rowheader><td colspan=5 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jmlbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
echo "\r\n        </tbody>\r\n    </table>\r\n</fieldset>\r\n";
CLOSE_BOX();

?>