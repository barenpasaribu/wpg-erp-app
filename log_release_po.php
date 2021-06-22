<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language="javascript" src="js/zMaster.js?v='.mt_rand().'"></script>' . "\r\n" . 
'<!--<script type="text/javascript" src="js/log_persetujuan_po.js"></script>' . "\r\n" . '-->' . "\r\n" . 
'<script type="text/javascript" src="js/log_release_po.js?v='.mt_rand().'"></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=refresh_data_release_po()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['carinopo'] . '</legend>';
echo $_SESSION['lang']['nopo'] . ':<input type=text id=txtsearch_rpo size=25 maxlength=30 onkeypress="return validat(event);" class=myinputtext>&nbsp;';
echo $_SESSION['lang']['tgl_po'] . ':<input type=text class=myinputtext id=tgl_cari_rpo onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo 'Type PO :<select id=typepo name=typepo><option value=\'ALL\'>ALL</option><option value=\'NORMAL\'>Normal</option><option value=\'LOKAL\'>Lokal</option></select>&nbsp;';
echo '<button class=mybutton onclick=cariRpo()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id=list_pp_verication>' . "\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list_po'];
echo '</legend>' . "\r\n" . '<div style="overflow:scroll; height:420px;">' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['tgl_po'];
echo '</td> ' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n" . '         ';

if ($_SESSION['empl']['tipelokasitugas'] != 'KANWIL') {
	echo "\t" . ' <td>';
	echo $_SESSION['lang']['tandatangan'];
	echo '</td>' . "\r\n" . '        <td>';
	echo $_SESSION['lang']['koreksi'];
	echo '</td>' . "\r\n" . '        ';
}
else {
	echo '        <td colspan="2">';
	echo $_SESSION['lang']['tandatangan'];
	echo '</td>';
}

echo '        <td>';
echo $_SESSION['lang']['detail'];
echo '</td>' . "\r\n\t" . '  ';
$i = 1;

while ($i < 4) {
	++$i;
}

echo "\t" . '   <td colspan="4" align="center">Release</td>' . "\r\n\t" . '   ' . "\r\n\t\r\n\t" . ' </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id="contain">' . "\r\n" . '     <script>refresh_data_release_po()</script>' . "\r\n\t" . ' ';
echo "\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>* Display items base on release date</div>' . "\r\n" . '</fieldset' . "\r\n" . '>';
CLOSE_BOX();
echo '</div>' . "\r\n" . '<input type="hidden" name="method" id="method"  /> ' . "\r\n" . '<input type="hidden" id="no_po" name="no_po" />' . "\r\n" . '<input type="hidden" name="user_login" id="user_login" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n\r\n";
echo close_body();

?>
