<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
echo '<div id=dataAtas>';
OPEN_BOX();
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . 
'<script language="javascript" src="js/zMaster.js?v='.mt_rand().'"></script>' . "\r\n" . 
'<script type="text/javascript" src="js/log_po.js?v='.mt_rand().'"></script>' . "\r\n" . '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n\r\n" . '         <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '         <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['carinopo'] . ':<input type=text id=txtsearch size=25 maxlength=30 onkeypress="return validat(event);" class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopo()>' . $_SESSION['lang']['find'] . '</button>';
echo '<br>Nama Unit Usaha:<input type=text id=txtsearch2 size=25 maxlength=30 onkeypress="return validat2(event);" class=myinputtext>';
echo '<button class=mybutton onclick=cariNopo2()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>';
echo '<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>';
echo '<script>loadNotifikasi()</script>';
echo '</div>' . "\r\n" . '</fieldset></td>';
echo '</tr>' . "\r\n" . '         </table></div> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '</div>';
echo '<div id="list_po">';
OPEN_BOX();
echo '<!--<img src="images/pdf.jpg" onclick="masterPDF(\'log_poht\',\'\',\'\',\'log_listpo\',event)" width="20" height="20" />-->' . "\r\n" . '<fieldset>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['listpo'];
echo '</legend>' . "\r\n" . '    <div id="contain">' . "\r\n" . '    <script>load_new_data()</script>' . "\r\n" . '    </div>' . "\r\n\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
echo '</div>' . "\r\n" . '<div id="list_pp" name="list_pp" style="display:none;">' . "\r\n" . '   ';
OPEN_BOX();
echo '    <fieldset>' . "\r\n" . '        <legend>';
echo $_SESSION['lang']['list_pp'];
echo '</legend>' . "\r\n" . '    ';
$optPt = '';
$sql3 = 'select * from ' . $dbname . '.organisasi where tipe=\'PT\'';

#exit(mysql_error());
($query3 = mysql_query($sql3)) || true;

while ($res3 = mysql_fetch_object($query3)) {
	$optPt .= '<option value=\'' . $res3->kodeorganisasi . '\'>' . $res3->namaorganisasi . '</option>';
}

echo '     <table cellspacing="1" border="0">' . "\r\n" . '         <tr>' . "\r\n" . '         <td>Please Select Company</td>' . "\r\n" . '         <td>:</td>' . "\r\n" . '         <td><select id="kode_pt" name="kode_pt" onchange="cek_pp_pt()">' . "\r\n" . '         <option value=""></option>' . "\r\n" . '        ';
echo $optPt;
echo '     </select></td></tr>' . "\r\n" . '     <br />' . "\r\n" . '         <input type="hidden" id="proses" name="proses" value="insert" />' . "\r\n" . '    <table cellspacing="1" border="0" id="list_pp_table">' . "\r\n" . '        <thead>' . "\r\n" . '        <tr class="rowheader">' . "\r\n" . '            <td>No.</td>' . "\r\n" . '            <td>';
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
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['jmlh_brg_blm_po'];
echo '</td>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['jmlhPesan'];
echo '</td>' . "\r\n" . '            <td>Action</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n\r\n" . '            <tbody id="container_pp">' . "\t\t\r\n\r\n" . '            <tr><td colspan="9" align="center"><button name="proses" id="proses" onclick="process()">';
echo $_SESSION['lang']['proses'];
echo '</button></td></tr>' . "\r\n" . '        </tbody>' . "\r\n" . '    </table>' . "\r\n" . '        <input type="hidden" id="user_id" name="user_id" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n" . '        </table>' . "\r\n" . '        </fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo '</div>' . "\r\n" . '<div id="form_po" style="display:none;">' . "\r\n" . '    ';
OPEN_BOX();
$isiOpt = array(1 => 'Cash', 2 => 'Transfer', 3 => 'Giro', 4 => 'Cheque');

foreach ($isiOpt as $ter => $OptIsi) {
	$optTermpay .= '<option value=\'' . $ter . '\'>' . $OptIsi . '</option>';
}

$optSupplier = '';
$snmkary = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

#exit(mysql_error());
($qnmkary = mysql_query($snmkary)) || true;
$rnmkary = mysql_fetch_assoc($qnmkary);
$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier  where kodekelompok=\'S001\' and status=1 order by namasupplier asc';

#exit(mysql_error());
($query = mysql_query($sql)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
}

$sMt = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

#exit(mysql_error());
($qMt = mysql_query($sMt)) || true;

while ($rMt = mysql_fetch_assoc($qMt)) {
	$optMt .= '<option value=\'' . $rMt['kode'] . '\' ' . ($rMt['kode'] == 'IDR' ? 'selected' : '') . '>' . $rMt['kodeiso'] . '</option>';
}

//$klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.datakaryawan where tanggalkeluar is NULL and left(kodegolongan,1)<=\'3\' and bagian=\'HO_PROC\' order by namakaryawan asc';

//$klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.datakaryawan where (tanggalkeluar is NULL or tanggalkeluar = \'0000-00-00\') and kodegolongan in (select kodegolongan from sdm_5golongan where alias=\'po_sign\') and bagian in (select kode from sdm_5departemen where alias=\'purch\') and lokasitugas like \''.$_SESSION['empl']['kodeorganisasi'].'%\' order by namakaryawan asc';
$klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.datakaryawan where (tanggalkeluar is NULL or tanggalkeluar = \'0000-00-00\') and kodegolongan in (select kodegolongan from sdm_5golongan where alias=\'po_sign\') and bagian in (select kode from sdm_5departemen where alias=\'purch\') and lokasitugas like \''.$_SESSION['empl']['kodeorganisasi'].'%\' and kodeorganisasi like \''.$_SESSION['empl']['kodeorganisasi'].'%\' order by namakaryawan asc';


#exit(mysql_error());
($qry = mysql_query($klq)) || true;

while ($rst = mysql_fetch_object($qry)) {
	$sBag = 'select nama from ' . $dbname . '.sdm_5departemen where kode=\'' . $rst->bagian . '\'';

	#exit(mysql_error());
	($qBag = mysql_query($sBag)) || true;
	$rBag = mysql_fetch_assoc($qBag);
	$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . ' [' . $rst->lokasitugas . '] [' . $rBag['nama'] . ']</option>';
}

$sKrm = 'select id_franco,franco_name from ' . $dbname . '.setup_franco where status=0 order by franco_name asc';

#exit(mysql_error($conn));
($qKrm = mysql_query($sKrm)) || true;

while ($rKrm = mysql_fetch_assoc($qKrm)) {
	$optKrm .= '<option value=' . $rKrm['id_franco'] . '>' . $rKrm['franco_name'] . '</option>';
}

echo '    <fieldset>' . "\r\n" . '        <legend>';
echo $_SESSION['lang']['form_po'];
echo '</legend>' . "\r\n" . '    <table cellspacing="1" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><input type="text" name="no_po" id="no_po" class="myinputtext" style="width:150px;" disabled="disabled" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><input type="text" name="tgl_po" id="tgl_po" class="myinputtext" value="';
echo date('d-m-Y');
echo '"  readonly="readonly" style="width:150px;" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['namasupplier'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td>' . "\r\n" . '                        <select id="supplier_id" name="supplier_id" onchange="get_supplier()" style="width:150px;" >' . "\r\n" . '                        <option value=""></option>' . "\r\n" . '                        ';
echo $optSupplier;
echo '                        </select>' . "\r\n" . '                <img src="images/search.png" class="resicon" title=\'';
echo $_SESSION['lang']['findRkn'];
echo '\' onclick="searchSupplier(\'';
echo $_SESSION['lang']['findRkn'];
echo '\',\'<fieldset><legend>';
echo $_SESSION['lang']['find'];
echo '</legend>';
echo $_SESSION['lang']['find'];
echo '&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>';
echo $_SESSION['lang']['find'];
echo '</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>\',event);"></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['norekeningbank'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" id="bank_acc" name="bank_acc" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>' . "\r\n" . '        </tr>' . "\r\n" . '                <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['npwp'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" id="npwp_sup" name="npwp_sup" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['matauang'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><select id="mtUang" name="mtUang" style="width:150px;" onchange="getKurs()">';
echo $optMt;
echo '</select></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['kurs'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" class="myinputtext" id="Kurs" name="Kurs" style="width:150px;" onkeypress="return angka_doang(event)" value="1"  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '          <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tandatangan'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><select id="persetujuan_id" name="persetujuan_id" style="width:150px;" >';
echo $optPur;
echo '</select>' . "\r\n" . '                        <input type="hidden" id="persetujuan_id2" value="0"/>' . "\r\n" . '                        </td>' . "\r\n" . '        </tr>' . "\r\n" . '<!--        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tandatangan'];
echo ' 2</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><select id="persetujuan_id2" name="persetujuan_id2" style="width:150px;" >';
echo $optPur;
echo '</select></td>' . "\r\n" . '        </tr>-->' . "\r\n" . ' ' . "\r\n" . '        </table>' . "\r\n\r\n" . '                        <fieldset style="width:60%">' . "\r\n" . '                                <legend>';
echo $_SESSION['lang']['daftarbarang'];
echo '</legend>' . "\r\n" . '                <table cellspacing="1" border="0" id="detail_content_table" name="detail_content_table">' . "\r\n" . '                    <tbody id="detail_content" name="detail_content">' . "\r\n" . '                        <tr><td><table id=\'ppDetailTable\'>' . "\r\n" . '                        </table>' . "\r\n\r\n" . '                                <table cellspacing=\'1\' border=\'0\'>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['tgl_kirim'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type="text" class="myinputtext" id="tgl_krm" name="tgl_krm" onmousemove="setCalendar(this.id)" onkeypress="return false";   maxlength="10"  style="width:200px;" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '                  <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['almt_kirim'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><select id=\'tmpt_krm\' name=\'tmpt_krm1\' style="width:200px;">';
echo $optKrm;
echo '</select>' . "\r\n" . '                        <!--<input type=\'text\'  id=\'tmpt_krm\' name=\'tmpt_krm\' maxlength=\'45\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event);\' style=width:200px />--></td>' . "\r\n" . '        </tr>' . "\r\n" . '        ';
$arragama = getEnum($dbname, 'log_poht', 'statusbayar');

foreach ($arragama as $kei => $fal) {
	#$OptCrByr .= '<option value=\'' . $kei . '\'>' . $fal . '</option>';
}
$OptCrByr = "<option value='CASH'>CASH</option><option value='CREDIT'>CREDIT</option>";
echo '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['pembayaran'];
echo '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td><select id="crByr" style="width:200px" >';
echo $OptCrByr;
echo '</select></td>' . "\r\n" . '        </tr>' . "\r\n" . '        ' . "\r\n" . '        <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['syaratPem'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type=\'text\' id=\'term_pay\' name=\'term_pay\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event);\' style="width:200px"   /></td>' . "\r\n" . '        </tr>' . "\r\n" . '                 <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['keterangan'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><textarea id=\'ketUraian\' name=\'ketUraian\' onkeypress=\'return tanpa_kutip(event);\' cols="80" rows="9"></textarea></td>' . "\r\n" . '        </tr>' . "\r\n" . '                <tr>' . "\r\n" . '            <td>';
echo $_SESSION['lang']['purchaser'];
echo '</td>' . "\r\n" . '                        <td>:</td>' . "\r\n" . '                        <td><input type=\'text\' id=\'purchaser_id\' name=\'purchaser_id\' class=\'myinputtext\' disabled=\'disabled\' value=\'';
echo $_SESSION['empl']['name'];
echo '\'  style=\'width:200px;\' /></td>' . "\r\n" . '        </tr></table>' . "\r\n\r\n" . '                        </td></tr>' . "\r\n\r\n" . '                    </tbody>' . "\r\n" . '                </table>' . "\r\n" . '                        </fieldset>' . "\r\n\r\n\r\n" . '        <table cellspacing="1" border="0">' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan="3">' . "\r\n" . '                <button class="mybutton" onclick="save_headher()">';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n" . '                <button class="mybutton" onclick="cancel_headher()">';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '                                <!--<button class="mybutton"  >';
echo $_SESSION['lang']['done'];
echo '</button>-->' . "\r\n" . '            </td>' . "\r\n" . '        </tr> ' . "\r\n\r\n" . '    </table>' . "\r\n" . '        </fieldset>' . "\r\n" . '    ';
CLOSE_BOX();
echo '</div>' . "\r\n\r\n";
echo close_body();

?>
