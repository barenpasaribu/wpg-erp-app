<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/log_pengiriman_internal.js\'></script>' . "\r\n";
$arrData = '##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method##nomor_id';
include 'master_mainMenu.php';
OPEN_BOX();
echo '<div id="action_list">' . "\r\n";
echo '<input type=hidden id=statusInputan value=0 /><table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . '<!--<td align=center style=\'width:100px;cursor:pointer;\' onclick=newData()><img class=delliconBig src=images/newfile.png title=\'' . $_SESSION['lang']['new'] . '\'><br>' . $_SESSION['lang']['new'] . '</td>-->' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=normalView()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['searchdata'] . ':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=loadData()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>';
echo '<td><fieldset><legend>' . $_SESSION['lang']['list'] . '</legend>';
$sDtPt = 'select distinct kodept from ' . $dbname . '.log_lpbht where gudangx=\'' . $_SESSION['empl']['lokasitugas'] . '\'order by kodept asc';

#exit(mysql_error($conn));
($qDtPt = mysql_query($sDtPt)) || true;

while ($rDtPt = mysql_fetch_assoc($qDtPt)) {
	echo '[ <a href=# onclick=newData(\'' . $rDtPt['kodept'] . '\')>' . $rDtPt['kodept'] . '</a> ]';
}

echo '</fieldset></td>';
echo '</tr>' . "\r\n\t" . ' </table>';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="dataListMnc">' . "\r\n" . '    ';
OPEN_BOX();
echo '<fieldset style=width:100%;><legend>' . $_SESSION['lang']['list'] . '</legend><table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['suratjalan'] . '</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['expeditor'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\r\n\t" . '   <td>Package Amount</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kepada'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['lokasipenerimaan'] . '</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['modatransportasi'] . '</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['berat'] . '</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['biaya'] . '</td>' . "\r\n\t" . '   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo '</div>' . "\r\n" . '    ' . "\r\n\r\n" . '<!--form list data babp-->' . "\r\n" . '<div id="vwListPenerimaan" style="display: none">' . "\r\n";
OPEN_BOX();
echo '<div id="listPenerimaan">' . "\r\n\r\n" . '    ' . "\r\n\r\n" . '</div>' . "\r\n" . '    ';
CLOSE_BOX();
echo '</div>' . "\r\n\r\n\r\n" . '<!--form inputan-->' . "\r\n\r\n" . '<div id="formInputanDt" style="display:none">' . "\r\n";
OPEN_BOX();
echo '    <div id="formInputan">' . "\r\n" . '    </div>' . "\r\n" . '    ';
CLOSE_BOX();
echo '</div>' . "\r\n\r\n";
echo close_body();

?>
