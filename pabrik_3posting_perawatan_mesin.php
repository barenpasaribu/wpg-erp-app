<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', 'Posting Perawatan Mesin');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"application/javascript\" src=\"js/pabrik_3posting_perawatan_mesin.js\"></script>\r\n\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"action_list\">\r\n";
$arrPil = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
foreach ($arrPil as $id => $ky) {
    $optPost .= '<option value='.$id.'>'.$ky.'</option>';
}
echo "<table>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>\r\n\t   </td>\r\n\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['notransaksi'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />&nbsp;';
echo $_SESSION['lang']['posting'].":<select id=statusPosting name=statusPosting><option value=''>".$_SESSION['lang']['all'].''.$optPost.'</option></select>&nbsp;';
echo '<button class=mybutton onclick=cariTransaksi()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> </div>\r\n";
echo "<div id=\"list_ganti\">\r\n";
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n<thead>\r\n<tr class=\"rowheader\">\r\n<td>No.</td>\r\n<td>";
echo $_SESSION['lang']['notransaksi'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['shift'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['statasiun'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['mesin'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['jammulai'];
echo "</td>\r\n<td>";
echo $_SESSION['lang']['jamselesai'];
echo "</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody id=\"contain\">\r\n<script>loadNData()</script>\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n\r\n";
echo close_body();

?>