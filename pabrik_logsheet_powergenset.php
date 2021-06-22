<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';

OPEN_BOX();
echo '<link rel=stylesheet type=text/css href="style/zTable.css?v='.mt_rand().'">' . "\r\n" . 
'<script language="javascript" src="js/zMaster.js?v='.mt_rand().'"></script>' . "\r\n" . 
'<script type="text/javascript" src="js/pabrik_logsheet.js?v='.mt_rand().'" /></script>' . "\r\n\r\n\r\n";
$user_id = $_SESSION['standard']['userid'];
if (($user_id == '') || ($user_id == 0)) {
	echo 'Error : You do not have organization code and license to create PR';
	CLOSE_BOX();
	echo close_body();
	exit();
}

echo "\r\n" . '<script>' . "\r\n" . ' jdl_ats_0=\'';
echo $_SESSION['lang']['find'];
echo '\';' . "\r\n" . '// alert(jdl_ats_0);' . "\r\n" . ' jdl_ats_1=\'';
echo $_SESSION['lang']['findBrg'];
echo '\';' . "\r\n" . ' content_0=\'<fieldset><legend>';
echo $_SESSION['lang']['findnoBrg'];
echo '</legend>Find&nbsp;<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>\';' . "\r\n\r\n" . ' jdl_bwh_0=\'';
echo $_SESSION['lang']['find'];
echo '\';' . "\r\n" . ' jdl_bwh_1=\'';
echo $_SESSION['lang']['findAngrn'];
echo '\';' . "\r\n" . ' content_1=\'<fieldset><legend>';
echo $_SESSION['lang']['findnoAngrn'];
echo '</legend>Find<input type=text class=myinputtext id=no_angrn><button class=mybutton onclick=findAngrn()>Find</button></fieldset><div id=container></div>\';' . "\r\n\r\n\t" . 'title_d=\'PR Submission\';' . "\r\n\t" . 'content_d=\'<fieldset><legend>';
echo $_SESSION['lang']['findnoAngrn'];
echo '</legend><div id=container></div>\';' . "\r\n\t" . 'ev_d=\'event\';' . "\r\n\t\r\n\t" . 'baTal=\'';
echo $_SESSION['lang']['cancel'];
echo '\';' . "\r\n\t" . 'Done=\'';
echo $_SESSION['lang']['done'];
echo '\'' . "\r\n" . '</script><br />' . "\r\n" . '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayFormInput()>' . "\r\n\t" . '   <img class=delliconBig src=images/newfile.png title=\'' . $_SESSION['lang']['new'] . '\'><br>' . $_SESSION['lang']['new'] . '</td>' . "\r\n\t" . ' <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n\t" . '   <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['carinopp'] . ':<input type=text id=txtsearch size=25 maxlength=30 onkeypress="return validat(event);" class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopp()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>' . "\r\n" . '     </tr>' . "\r\n\t" . ' </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="list_pp">';
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo 'List Logsheet Power Genset';
echo '</legend>' . "\r\n" . "\r\n" . '<div style="overflow:scroll; height:420px;">' . "\r\n\t" . ' <table class="sortable" cellspacing="1" border="0">' . "\r\n\t" . ' <thead>' . "\r\n\t" . ' <tr class=rowheader>' . "\r\n\t" . ' <td>No.</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td> ' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n\t" . ' <td>';
echo $_SESSION['lang']['dbuat_oleh'];
echo "\t" . '  <td>';
echo 'Progress';
echo '</td>' . "\r\n\t" . ' <td align="center">Action</td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id="contain">' . "\r\n\t" . '<script>loadData()</script>' . "\r\n\r\n\t" . '  </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></div>' . "\r\n" . '</fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo '<div id="form_pp" style="display:none;">' . "\r\n";
OPEN_BOX();
echo "\r\n" . ' ';
$optBagian = '';

//if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
//	$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where CHAR_LENGTH(kodeorganisasi)=\'4\'';
//}
//else {
//	$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where `kodeorganisasi`=\'' . substr($_SESSION['empl']['lokasitugas'], 0, 4) . '\'';
//}

#exit(mysql_error($conn));
($res = mysql_query($str)) || true;
echo '<fieldset>' . "\r\n\t" . '<legend>';
echo $_SESSION['lang']['prmntaanPembelian'];
echo '</legend>' . "\r\n\t" . '<table cellspacing="1" border="0" id="opl">' . "\r\n\t" . ' <tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t" . '  <td>';

//while ($bar = mysql_fetch_object($res)) {
//	$optBagian .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
//}

//echoMessage("title ",getQuery("lokasitugas"),true);
$optBagian =makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

echo "\t\t\t" . '<select id="kd_bag" style=\'width:150px;\' onchange="get_isi(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text)">' . "\r\n\t\t\t" . '<option value="" selected="selected"></option>' . "\r\n\t\t\t";
echo $optBagian;
echo "\t\t\t" . '</select>' . "\t" . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n\t\t\t" . '<td><input type="text" id="nopp" class="myinputtext" disabled="disabled" style=\'width:150px;\' /></td>' . "\r\n\t\t" . '</tr>' . "\t\t\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n" . '                        ';
$as = date('Y-m-d');
echo "\t\t\t" . '<td><input type="text" class="myinputtext" id="tgl_pp" name="tgl_pp" value="';
echo tanggalnormal($as);
echo '" readonly="readonly" style=\'width:150px;\' /></td>' . "\r\n\t\t" . '</tr>' . "\r\n" . 
'<tr><td>Jumlah Pemberi Persetujuan </td><td>:</td><td><select id=jumlahpemberipersetujuan>
<option value=\'\'>Pilih data</option>
<option value=\'1\'>1</option>
<option value=\'2\'>2</option>
<option value=\'3\'>3</option>
<option value=\'4\'>4</option>
<option value=\'5\'>5</option>
</select>'.
'        <tr>' . "\r\n\t\t\t" . '<td>';
echo $_SESSION['lang']['catatan'];
echo '</td>' . "\r\n\t\t\t" . '<td>:</td>' . "\r\n" . '                        ' . "\r\n\t\t\t" . '<td><textarea id=catatan name="catatan" cols="50" rows="5"></textarea></td>' . "\r\n\t\t" . '</tr>' . "\r\n" . '    <!--ALTER TABLE  `log_prapoht` ADD  `catatanpp` TEXT NOT NULL-->    ' . "\r\n\t\t" . '<tr>' . "\r\n\t\t\t" . '<td colspan="3">' . "\r\n\t\t\t" . '<input type="hidden" id="method" value="insert" />' . "\r\n" . '            <input type="hidden" id="user_id" name="user_id" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n\t\t\t" . '<button class=mybutton id="dtl_pem" onclick=detailPembelian()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\t\t\t" . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t" . '</table>' . "\r\n" . '</fieldset><br />' . "\r\n" . '<br />' . "\r\n" . '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['detailprmntaanPembelian'];
echo '</legend><br />' . "\r\n" . '<div id="detailTable" style="display:none;">' . "\r\n" . '<!-- content detail pp-->' . "\r\n" . '    ' . "\r\n" . '</div>' . "\r\n" . '<div id="tmbl_all"> ' . "\t\r\n\r\n" . '</div>' . "\r\n" . '</fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo '</div>';
echo '<!--div persetujuan-->' . "\r\n" . '<div id="persetujuan" style="display:none;">' . "\r\n";
OPEN_BOX();
echo '    <div id="persetujuandata"></div>' . "\r\n";
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
