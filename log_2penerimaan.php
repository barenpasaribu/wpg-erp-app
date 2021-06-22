<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();


echo '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_2penerimaan.js"></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
//$strG = "SELECT kodeorganisasi,namaorganisasi FROM organisasi ".
//	"WHERE tipe LIKE 'GUDANG%' ".
//	"AND LEFT(kodeorganisasi,4) in ".
//	"( ".
//	"SELECT ".
//	"o.kodeorganisasi ".
//	"FROM  organisasi o  ".
//	"WHERE o.induk in ( ".
//	"SELECT o.kodeorganisasi ".
//	"FROM datakaryawan d ".
//	"INNER JOIN user u on u.karyawanid=d.karyawanid ".
//	"INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
//	"WHERE u.namauser='" .$_SESSION['standard']['username'] ."' ".
//	")) ";
$optGudang= makeOption2(getQuery("gudang"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']." "),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=loadData()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['carinopo'] . '</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0><tr><td>' . $_SESSION['lang']['pilihgudang'] . '</td><td><select id=kdGdng>' . $optGudang . '</select></td>';
echo '<td>' . $_SESSION['lang']['nopp'] . '</td><td><input type=text id=txtsearch2 size=25 maxlength=30 class=myinputtext onkeypress=\'return tanpa_kutip(event)\'></td>';
echo '<td>' . $_SESSION['lang']['nopo'] . '</td><td><input type=text id=txtsearch size=25 maxlength=30 class=myinputtext onkeypress=\'return tanpa_kutip(event)\'></td></tr><tr>';
echo '<td>' . $_SESSION['lang']['namabarang'] . '</td><td><input type=text class=myinputtext id=nmBrg onkeypress=\'return tanpa_kutip(event)\' /></td>';
echo '<td>' . $_SESSION['lang']['tanggal'] . '</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td><td><td></tr></table>';
echo '<button class=mybutton onclick=cariData()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id=list_pp_verication>' . "\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list'];
echo '</legend>' . "\r\n" . '<div style="overflow:scroll; height:420px;">' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n" . '        <tr class=rowheader>' . "\r\n" . '        <td>No.</td>' . "\r\n" . '        <td>';
echo $_SESSION['lang']['notransaksi'];
echo '</td>' . "\r\n" . '        <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td> ' . "\r\n" . '        <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n" . '        <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n" . '        <td>Action</td>' . "\r\n" . '        </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id="contain">' . "\r\n\t" . '<script>loadData()</script>' . "\r\n\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></div>' . "\r\n" . '</fieldset' . "\r\n" . '>';
CLOSE_BOX();
echo '</div>' . "\r\n" . '<input type="hidden" name="method" id="method"  /> ' . "\r\n" . '<input type="hidden" id="no_po" name="no_po" />' . "\r\n" . '<input type="hidden" name="user_login" id="user_login" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n\r\n";
echo close_body();

?>
