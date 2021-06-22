	<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n" . '    <link rel="stylesheet" type="text/css" href="style/zTable.css">' . "\r\n" . '    <script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '     <script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '    <script type="text/javascript" src="js/log_pnwrharga.js" /></script>' . "\r\n\r\n" . '    <script>' . "\r\n" . '     jdl_ats_0=\'';
echo $_SESSION['lang']['find'];
echo '\';' . "\r\n" . '    // alert(jdl_ats_0);' . "\r\n" . '     jdl_ats_1=\'';
echo $_SESSION['lang']['findBrg'];
echo '\';' . "\r\n" . '     content_0=\'<fieldset><legend>';
echo $_SESSION['lang']['findnoBrg'];
echo '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>\';' . "\r\n" . '     Option_Isi=\'';
$optKurs = '<option value=>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKurs = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

#exit(mysql_error());
$qKurs = mysql_query($sKurs);

while ($rKurs = mysql_fetch_assoc($qKurs)) {
	$optKurs .= '<option value=' . $rKurs['kode'] . '>' . $rKurs['kodeiso'] . '</option>';
}

echo $optKurs;
echo '\';' . "\r\n" . '     isi_option="';
echo '";' . "\r\n" . '    </script>' . "\r\n" . '    <div id="action_list">' . "\r\n" . '    ';
echo '<table>' . "\r\n" . '         <tr valign=middle>' . "\r\n" . '             <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n" . '               <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '             <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['notransaksi'] . ':<input type=text id=txtsearch size=25 maxlength=30 onkeypress="return validat(event);" class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariPnwrn()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>';
echo '<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>';
echo '</div>' . "\r\n" . '    </fieldset></td>';
echo '</tr>' . "\r\n" . '             </table> ';
echo '    </div>' . "\r\n" . '    ';
CLOSE_BOX();
echo "\r\n" . '    <div id="list_permintaan" name="list_permintaan">' . "\r\n" . '        ';
OPEN_BOX();
echo '        <fieldset>' . "\r\n" . '            <legend>';
echo $_SESSION['lang']['permintaan'];
echo '</legend>' . "\r\n" . '            <div id="dlm_list_permintaan" name="dlm_list_permintaan" style="overflow: scroll; height:420px;">' . "\r\n" . '                <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '                <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '                <td>No.</td>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['notransaksi'];
echo '</td>' . "\r\n" . '                 <td>';
echo $_SESSION['lang']['urutan'];
echo '</td>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '                <td>';
echo $_SESSION['lang']['namasupplier'];
echo '</td>' . "\r\n" . '                <td align="center">Action</td>' . "\r\n" . '                </tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody id="contain">' . "\r\n" . '                <script>get_data();</script>' . "\r\n" . '                </tbody>' . "\r\n" . '                </table>' . "\r\n" . '            </div>' . "\r\n" . '        </fieldset>' . "\r\n" . '        ';
CLOSE_BOX();
echo '    </div>' . "\r\n" . '    ';
$arr = '';
echo '<div id=formPP style=display:none>';
OPEN_BOX();
echo '</fieldset><input type=hidden id=noUrut value=\'1\' /><input type=hidden id=notransaksi value=\'\' />';
echo '<div id=listBrgPP  style=display:none>' . "\r\n" . '    <fieldset  style=width:750px;><legend>' . $_SESSION['lang']['daftarbarang'] . '</legend>' . "\r\n" . '        <div style=\'width:680px;display:fixed;\'>' . "\r\n" . '        <table border=0 cellpadding=1 cellspacing=1 class=sortable>' . "\r\n" . '        <thead><tr class=rowheader>' . "\r\n" . '        <td style=width:10px>No.</td>' . "\r\n" . '        <td style=width:199px>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td style=width:90px>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td style=width:380px>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td style=width:50px>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '        <td style=width:50px>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td style=width:10px><input type=checkbox onclick=clikcAll() id=dtSemua /></td></tr></thead><tbody> ' . "\r\n" . '        </tbody></table></div>' . "\r\n" . '        <div style=\'width:750px;height:420px;overflow:scroll;\'>' . "\r\n" . '               <table class=sortable cellspacing=1 border=0 width=680px>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr>' . "\r\n" . '                </tr>  ' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody id=dataBarang>' . "\r\n\r\n" . '                </tbody>' . "\r\n" . '             </table>' . "\r\n" . '         </div>' . "\r\n" . '    </fieldset>' . "\r\n" . '    </div>';
$optTermPay = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optStock = $optTermPay;
$optKrm = $optTermPay;
$arrOptTerm = array(1 => 'Tunai', 2 => 'Kerdit 2 Minggu', 3 => 'Kredit 1 Bulan', 4 => 'Termin', 5 => 'DP');

foreach ($arrOptTerm as $brsOptTerm => $listTerm) {
	$optTermPay .= '<option value=\'' . $brsOptTerm . '\'>' . $listTerm . '</option>';
}

$sKrm = 'select id_franco,franco_name from ' . $dbname . '.setup_franco where status=0 order by franco_name asc';

#exit(mysql_error($conn));
$qKrm = mysql_query($sKrm);

while ($rKrm = mysql_fetch_assoc($qKrm)) {
	$optKrm .= '<option value=' . $rKrm['id_franco'] . '>' . $rKrm['franco_name'] . '</option>';
}

$arrStock = array(1 => 'Ready Stock', 2 => 'Not Ready');

foreach ($arrStock as $brsStock => $listStock) {
	$optStock .= '<option value=\'' . $brsStock . '\'>' . $listStock . '</option>';
}

$optMt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sMt = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

#exit(mysql_error());
$qMt = mysql_query($sMt);

while ($rMt = mysql_fetch_assoc($qMt)) {
	$optMt .= '<option value=' . $rMt['kode'] . '>' . $rMt['kodeiso'] . '</option>';
}

echo '<br /><div id=listSupplier style=display:none>';
echo '<fieldset style=width:450px;><legend>' . $_SESSION['lang']['permintaan'] . '</legend>';
echo '<table cellspacing="1" border="0">' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><select id="mtUang" name="mtUang" style="width:150px;" >' . $optMt . '</select></td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" class="myinputtext" id="Kurs" name="Kurs" style="width:150px;" onkeypress="return angka_doang(event)"  /></td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['syaratPem'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><select id=\'term_pay\' name=\'term_pay\' style="width:200px">' . $optTermPay . '</select></td>' . "\r\n" . '                <td>&nbsp;</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['almt_kirim'] . '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><select id=\'tmpt_krm\' name=\'tmpt_krm\' style="width:200px;">' . $optKrm . '</select></td>' . "\r\n" . '                        <td>&nbsp;</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . substr($_SESSION['lang']['stockdetail'], 0, 5) . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><select id=\'stockId\' name=\'stockId\' style="width:200px">' . $optStock . '</select></td>' . "\r\n" . '                <td>&nbsp;</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><textarea id=\'ketUraian\' name=\'ketUraian\' onkeypress=\'return tanpa_kutip(event);\'></textarea></td>' . "\r\n" . '                <td>&nbsp;</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr><td colspan=3 align=center><button class=mybutton onclick=\'lanjutAdd2()\'  >' . $_SESSION['lang']['lanjut'] . '</button></td></tr>' . "\r\n" . '            </table>';
echo '</fieldset>';
echo '</div>';
$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier order by namasupplier asc';

#exit(mysql_error());
$query = mysql_query($sql);

while ($res = mysql_fetch_assoc($query)) {
	$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
}

echo '<div id=supplierForm style=display:none><input type=hidden id=noppr  />';
echo '<fieldset style=width:550px;><legend>Data Supplier</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0>';
echo '<tr>' . "\r\n" . '                    <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '                    <td>:</td>' . "\r\n" . '                    <td>' . "\r\n" . '                        <select id="id_supplier" name="id_supplier" style="width:200px;" disabled="disabled">' . $optSupplier . '</select>' . "\r\n" . '                    </td>' . "\r\n" . '                    <td><img src=\'images/search.png\' class=dellicon title=\'' . $_SESSION['lang']['findRkn'] . '\' onclick="searchSupplier(\'' . $_SESSION['lang']['findRkn'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findRkn'] . '</legend>' . $_SESSION['lang']['namasupplier'] . '&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);"></td>' . "\r\n" . '                </tr>';
echo '<tr><td colspan=3 ><button class=mybutton onclick=\'addDataSma()\'  >Add Data</button>&nbsp;<button class=mybutton onclick=zPreview2(\'log_slave_save_permintaan_harga\',\'' . $arr . '\',\'printContainer2\')  >' . $_SESSION['lang']['done'] . '</button></td></tr></table>';
echo '</fieldset>';
echo '<fieldset style=width:550px;><legend>' . $_SESSION['lang']['data'] . '</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
echo '<tr class=rowheader>';
echo '<td>No.</td>';
echo '<td>' . $_SESSION['lang']['nopermintaan'] . '</td>';
echo '<td>' . $_SESSION['lang']['namasupplier'] . '</td>';
echo '<td>' . $_SESSION['lang']['action'] . '</td>';
echo '</thead><tbody id=listHasilSave>';
echo '</tbody></table>';
echo '</fieldset>';
echo '</div>';
CLOSE_BOX();
echo '</div>';
echo '<div id=formPP2  style=display:none>';
OPEN_BOX();
$optListNopp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sLnopp = 'select distinct nomor from ' . $dbname . '.log_perintaanhargaht where ' . "\r\n" . '             purchaser=\'' . $_SESSION['standard']['userid'] . '\' order by nomor desc';

#exit(mysql_error($conn));
$qLnopp = mysql_query($sLnopp);

while ($rLnopp = mysql_fetch_assoc($qLnopp)) {
	$optListNopp .= '<option value=\'' . $rLnopp['nomor'] . '\'>' . $rLnopp['nomor'] . '</option>';
}

$arr = '##nopp2##formPil';
echo '<br /><fieldset style=width:350px;><legend>Form PP</legend>';
echo '<input type=hidden id=\'formPil\' name=\'formPil\' value=\'1\' /><table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td><label>' . $_SESSION['lang']['nopp'] . '</label></td><td><select id="nopp2" name="nopp2"  style="width:200px;" >' . $optListNopp . '</select><img  src=\'images/search.png\' class=dellicon title=\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'] . '\' onclick="searchNopp(\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopp'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp;<input type=text class=myinputtext id=kdNopp><button class=mybutton onclick=findNopp2()>' . $_SESSION['lang']['find'] . '</button></fieldset><div id=containerNopp style=overflow=auto;height=380;width=485></div>\',event);"></td></tr>' . "\r\n" . '    <tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '    <tr><td colspan="2">' . "\r\n" . '    <button onclick="zPreview(\'log_slave_2perbandingan_harga\',\'' . $arr . '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button>' . "\r\n" . '    <button onclick="zExcel(event,\'log_slave_2perbandingan_harga.php\',\'' . $arr . '\')" class="mybutton" name="preview" id="preview">Excel</button>    ' . "\r\n" . '    </td></tr>' . "\r\n" . '    </table>';
echo '</fieldset>';
CLOSE_BOX();
echo '<div id=formEditData  style=display:none>';
OPEN_BOX();
echo '<fieldset style=\'clear:both\'><legend><b>Edit Area</b></legend>';
echo '<div id=\'printContainer\'  style=\'overflow:auto;height:550px;width:1200px\'>';
echo '</div>';
echo '</fieldset>';
CLOSE_BOX();
echo '</div>';
echo '</div>';
echo '<div id=\'formEditData2\'  style=display:none>';
OPEN_BOX();
echo '<fieldset style=\'clear:both\'><legend><b>Edit Area</b></legend>';
echo '<div id=\'printContainer2\'  style=\'overflow:auto;height:550px;width:1200px\'>';
echo '</div>';
echo '</fieldset>';
CLOSE_BOX();
echo '</div>';
echo close_body();

?>
