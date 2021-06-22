<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_po_lokal.js" /></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n" . '         <!--<td align=center style=\'width:100px;cursor:pointer;\' onclick=show_list_pp()>' . "\r\n" . '           <img class=delliconBig src=images/newfile.png title=\'' . $_SESSION['lang']['new'] . '\'><br>' . $_SESSION['lang']['new'] . '</td>-->' . "\r\n" . '         <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '         <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['carinopo'] . ':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopo()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>';
echo '<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>';
echo '<script>loadNotifikasi()</script>';
echo '</div>' . "\r\n" . '</fieldset></td>';
echo '</tr>' . "\r\n" . '         </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="list_po">';
OPEN_BOX();
echo "\t\r\n" . '<input type="hidden" id="user_id" name="user_id" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n" . '<input type="hidden" id="proses" name="proses" value="insert" />' . "\r\n" . '<fieldset>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['listpo'];
echo '</legend>' . "\r\n" . '  <div  id=\'contain\'><script>load_new_data()</script></div>   ' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
echo '</div>' . "\r\n" . '<div id="list_pp" name="form_po" style="display:none;">' . "\r\n" . '   ';
OPEN_BOX();
echo '    <fieldset>' . "\r\n" . '        <legend>';
echo $_SESSION['lang']['list_pp'];
echo '</legend>' . "\r\n" . '    ';
$optPt = '';
$sql3 = 'select `kodeorganisasi`,`namaorganisasi` from ' . $dbname . '.organisasi where tipe=\'PT\'';

#exit(mysql_error());
($query3 = mysql_query($sql3)) || true;

while ($res3 = mysql_fetch_object($query3)) {
	$optPt .= '<option value=\'' . $res3->kodeorganisasi . '\'>' . $res3->namaorganisasi . '</option>';
}

echo '         <div style="height:340px; width:100%; overflow:auto;">' . "\r\n" . '        <table cellspacing="1" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '        <td>Please Select Company</td>' . "\r\n" . '        <td>:</td>' . "\r\n" . '        <td><select id="kode_pt" name="kode_pt" onchange="cek_pp_pt(\'0\')">' . "\r\n" . '        <option value=""></option>' . "\r\n" . '        ';
echo $optPt;
echo '        </select></td></tr>' . "\t" . '</table>' . "\r\n" . '    <table cellspacing="1" border="0" id="list_pp_table">' . "\r\n" . '        <thead>' . "\r\n\r\n" . '        <tr class="rowheader">' . "\r\n" . '            <td>No.</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['kodebarang'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['jmlhDiminta'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tgldibutuhkan'];
echo '</td>            ' . "\r\n" . '            <td>';
echo $_SESSION['lang']['jmlh_brg_blm_po'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['jmlhPesan'];
echo '</td>' . "\r\n" . '            <td>Action</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n" . '            <tbody id="container_pp">' . "\t\r\n\r\n\r\n" . '        </tbody>' . "\r\n" . '    </table>' . "\r\n" . '    </div>' . "\r\n" . '<input type="hidden" id="user_id" name="user_id" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n" . '        </fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo '</div>' . "\r\n" . '<div id="form_po" style="display:none;">' . "\r\n" . '    ';
OPEN_BOX();
$isiOpt = array(1 => 'Cash', 2 => 'Transfer', 3 => 'Giro', 4 => 'Cheque');
$tgl_skrg = date('d-m-Y');

foreach ($isiOpt as $ter => $OptIsi) {
	$optTermpay .= '<option value=\'' . $ter . '\'>' . $OptIsi . '</option>';
}

$optSupplier = '';
$sql = 'select * from ' . $dbname . '.log_5supplier where kodekelompok=\'S001\' order by namasupplier asc';

#exit(mysql_error());
($query = mysql_query($sql)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
}

echo '    <fieldset>' . "\r\n" . '        <legend>';
echo $_SESSION['lang']['form_po'];
echo '</legend>' . "\r\n" . '    <table cellspacing="1" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><input type="text" name="no_po" id="no_po" class="myinputtext" style="width:200px;" disabled="disabled"/></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><input type="text" name="tgl_po" id="tgl_po" class="myinputtext" value="';
echo $tgl_skrg;
echo '" disabled="disabled" style="width:200px;"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['namasupplier'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td>' . "\r\n" . '                        <select id="supplier_id" name="supplier_id" onchange="get_supplier()" style="width:200px;" >' . "\r\n" . '                        <option value=""></option>' . "\r\n" . '                        ';
echo $optSupplier;
echo '                        </select> <img src="images/search.png" class="resicon" title=\'';
echo $_SESSION['lang']['findRkn'];
echo '\' onclick="searchSupplier(\'';
echo $_SESSION['lang']['findRkn'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['find'];
echo '</legend>';
echo $_SESSION['lang']['namasupplier'];
echo '&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>';
echo $_SESSION['lang']['find'];
echo '</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);"></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['norekeningbank'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" id="bank_acc" name="bank_acc" class="myinputtext" onkeypress="return angka_doang(event)" disabled="disabled" style="width:200px;"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '                <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['npwp'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" id="npwp_sup" name="npwp_sup" class="myinputtext" onkeypress="return angka_doang(event)" disabled="disabled" style="width:200px;"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan="3">' . "\r\n" . '                        <fieldset style="width:60%">' . "\r\n" . '                                <legend>';
echo $_SESSION['lang']['daftarbarang'];
echo '</legend>' . "\r\n" . '                <table cellspacing="1" border="0" id="detail_content_table" name="detail_content_table">' . "\r\n" . '                    <tbody id="detail_content" name="detail_content">' . "\r\n" . '                       <tr><td>' . "\r\n" . '                       <!-- form detail barang--><table id=\'ppDetailTable\'> </table>' . "\r\n\r\n" . '                       <!-- end form detail barang-->' . "\r\n" . '                       <!-- addtional data-->' . "\r\n" . '                       ';
echo '                      <table cellspacing=\'1\' border=\'0\'>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tgl_kirim'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" class="myinputtext" id="tgl_krm" name="tgl_krm" onmousemove="setCalendar(this.id)" onkeypress="return false";   maxlength="10"  style="width:200px" value="" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '                  <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['almt_kirim'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type=\'text\'  id=\'tmpt_krm\' name=\'tmpt_krm\' maxlength=\'45\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event);\' style="width:200px" value=""  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['syaratPem'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type=\'text\' id=\'term_pay\' name=\'term_pay\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event);\' style="width:200px"  value="" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['keterangan'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><textarea id=\'ketUraian\' name=\'ketUraian\' onkeypress=\'return tanpa_kutip(event);\'></textarea></td>' . "\r\n" . '        </tr>' . "\r\n" . '                <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['purchaser'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" id="purchaser_id" name="purchaser_id" disabled="disabled" class="myinputtext" value="';
echo $_SESSION['empl']['name'];
echo '" style="width:200px"  /> </td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>' . "\r\n\r\n\r\n\r\n\r\n\r\n" . '                       </td></tr>' . "\r\n" . '                    </tbody>' . "\r\n" . '                </table>' . "\r\n" . '                        </fieldset>' . "\r\n" . '            </td>' . "\r\n" . '        </tr>' . "\r\n\r\n" . '        <table cellspacing="1" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan="3">' . "\r\n" . '                <button class="mybutton" onclick=save_headher()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '                <button class="mybutton" onclick=cancel_headher()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '                                ';
$sql_cek = 'select persetujuan1 from ' . $dbname . '.log_poht where';
echo '                                <!--<button class="mybutton" onclick=get_data_pp() >';
echo $_SESSION['lang']['done'];
echo '</button>-->' . "\r\n" . '            </td>' . "\r\n" . '        </tr> ' . "\r\n\r\n" . '    </table>' . "\r\n" . '        </fieldset>' . "\r\n" . '    ';
CLOSE_BOX();
echo '</div>' . "\r\n\r\n";
echo close_body();

?>
