<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<!--<link rel=stylesheet type=text/css href=style/zTable.css>-->' . "\r\n" . '<script language="javascript" src="js/pmn_5klcustomer.js"></script>' . "\r\n" . '<fieldset>' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['klmpkPlgn'];
echo '</b></legend>' . "\r\n" . '<table cellpadding="2" cellspacing="2" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['kode'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" class="myinputtext" id="kode_grp_cus" onkeypress="return tanpa_kutip(event);" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['kelompok_pem'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" class="myinputtext" id="klmpk_cust" onkeypress="return tanpa_kutip(event);"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['findnoakun'];
echo '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="hidden" id="akun_cust"  /><input type="text" id="nama_akun" class="myinputtext" disabled="disabled"/> <img src=images/search.png class=dellicon title=';
echo $_SESSION['lang']['find'];
echo ' onclick="searchAkun(\'';
echo $_SESSION['lang']['findnoakun'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['findnoakun'];
echo '</legend>Find<input type=text class=myinputtext id=no_akun><button class=mybutton onclick=findAkun()>Find</button></fieldset><div id=container></div>\',event)";>' . "\r\n" . '                <input type="hidden" value="insert" id="method" />' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td colspan="3" align="center">' . "\r\n" . '                <button class=mybutton onclick=simpanKlmpkplgn()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '         <button class=mybutton onclick=batalKlmpkplgn()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '                </td>' . "\r\n" . '        </tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
echo "\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '     <!--<legend><b>';
echo '</b></legend>-->' . "\r\n" . '         <table class=sortable cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['kode'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['kelompok_pem'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['noakun'];
echo '</td>' . "\r\n" . '         <td>';
echo $_SESSION['lang']['findnoakun'];
echo '</td>' . "\r\n" . '         <td colspan="2">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody id=containersatuan>' . "\r\n\r\n" . '         ';
$srt = 'select * from ' . $dbname . '.keu_5akun order by noakun';

#exit(mysql_error());
($po = mysql_query($srt)) || true;
$bar = mysql_fetch_object($po);
$str = 'select * from ' . $dbname . '.pmn_4klcustomer order by kode desc';

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$noakun = $bar->noakun;

		if ($_SESSION['language'] == 'EN') {
			$kol = 'namaakun1  as namaakun';
		}
		else {
			$kol = 'namaakun';
		}

		$spr = 'select ' . $kol . ' from  ' . $dbname . '.keu_5akun where `noakun`=\'' . $noakun . '\'';

		#exit(mysql_error($conn));
		($rep = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rep);
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar->kode . '</td>' . "\r\n" . '                                  <td>' . $bar->kelompok . '</td>' . "\r\n" . '                                  <td>' . $bar->noakun . '</td>' . "\r\n" . '                                  <td>' . $bas->namaakun . '</td>' . "\r\n" . '                                  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->noakun . '\',\'' . $bas->namaakun . '\');"></td>' . "\r\n" . '                                  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delKlmpkplgn(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->noakun . '\');"></td>' . "\r\n" . '                                 </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

echo "\t\r\n" . '         </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\r\n" . '         </table>' . "\r\n" . '     </fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
