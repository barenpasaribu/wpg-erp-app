<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayFormInput()>' . "\r\n\t" . '   <img class=delliconBig src=images/newfile.png title=\'' . $_SESSION['lang']['new'] . '\'><br>' . $_SESSION['lang']['new'] . '</td>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['carinopp'] . ':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopp()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="list_pp_verication">';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list_pp'];
echo '</legend>' . "\r\n" . '<div style="overflow:scroll; height:420px;">' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['nopp'];
echo '</td> ' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n" . ' ' . "\t" . ' <td>';
echo $_SESSION['lang']['jmlhDiminta'];
echo '</td>' . "\r\n" . ' ' . "\t" . ' <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n" . ' ' . "\t" . ' <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n\t" . '  <td>';
echo 'Progress';
echo '</td>' . "\r\n\t" . ' <td colspan="3" align="center">Action</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id="contain">' . "\r\n\t\r\n\t" . ' ';
$str = 'select * from ' . $dbname . '.log_prapoht a inner join log_prapodt b on a.nopp=b.nopp order where a.close=\'1\' by nopp desc';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$koderorg = $bar->kodeorg;
		$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

		#exit(mysql_error($conn));
		($rep = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rep);
		$no += 1;

		if ($bar->close == '0') {
			$b = '<a href=# id=seeprog onclick=frm_aju(\'' . $bar->nopp . '\',\'' . $bar->close . '\') title="Click To Change The Status ">Need Approval</a>';
		}
		else if ($bar->close == '1') {
			$b = '<a href=# id=seeprog onclick=frm_aju(\'' . $bar->nopp . '\',\'' . $bar->close . '\') title="Click To Change The Status">Waiting Approval</a>';
		}
		else if ($bar->close == '2') {
			$b = '<a href=# id=seeprog onclick=frm_aju(\'' . $bar->nopp . '\',\'' . $bar->close . '\') title="Can Make PO">Approved</a>';
		}

		echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->nopp . '</td>' . "\r\n\t\t\t" . '  <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t" . '  <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t" . '  <td>' . $b . '</td>' . "\r\n\t\t" . ' <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->nopp . '\',\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeorg . '\',\'' . $bar->close . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar->nopp . '\',\'' . $bar->close . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

echo "\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></div>' . "\r\n" . '</fieldset>' . "\r\n";
echo '</div>';
CLOSE_BOX();

?>
