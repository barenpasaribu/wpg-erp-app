<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_persetujuan_po.js"></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n" . '         <td align=center style=\'width:100px;cursor:pointer;\' onclick=refresh_data()>' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '         <td><fieldset><legend>' . $_SESSION['lang']['carinopo'] . '</legend>';
echo $_SESSION['lang']['nopo'] . ':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;';
echo $_SESSION['lang']['tgl_po'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopo()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n" . '         </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id=list_pp_verication>' . "\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list_po'];
echo '</legend>' . "\r\n" . '<div style="overflow:scroll; height:420px;">' . "\r\n" . '         <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['tgl_po'];
echo '</td> ' . "\r\n" . '         <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n" . '          <td>Detail PO</td>' . "\r\n" . '           <td colspan="2" align="center">Verification</td>' . "\r\n" . '          ';
$i = 1;

while ($i < 4) {
	echo '<td align=center>' . $_SESSION['lang']['persetujuan'] . $i . '</td>';
	++$i;
}

echo "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody id="contain">' . "\r\n" . '        <script>refresh_data()</script>' . "\r\n" . '         ';
echo '          </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table></div>' . "\r\n" . '</fieldset' . "\r\n" . '>';
CLOSE_BOX();
echo '</div>' . "\r\n" . '<input type="hidden" name="method" id="method"  /> ' . "\r\n" . '<input type="hidden" id="no_po" name="no_po" />' . "\r\n" . '<input type="hidden" name="user_login" id="user_login" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n";
echo close_body();

?>
