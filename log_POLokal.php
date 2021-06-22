<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script type="text/javascript" src="js/log_po_lokal.js" /></script>' . "\r\n";

echo '<div id="action_list">' . "\r\n";
echo '<table>' . "\r\n" . '     <tr valign=middle>' . "\r\n" . '         <td align=center style=\'width:100px;cursor:pointer;\' onclick=show_list_pp()>' . "\r\n" . '           <img class=delliconBig src=images/newfile.png title=\'' . $_SESSION['lang']['new'] . '\'><br>' . $_SESSION['lang']['new'] . '</td>' . "\r\n" . '         <td align=center style=\'width:100px;cursor:pointer;\' onclick=displayList()>' . "\r\n" . '           <img class=delliconBig src=images/orgicon.png title=\'' . $_SESSION['lang']['list'] . '\'><br>' . $_SESSION['lang']['list'] . '</td>' . "\r\n" . '         <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo $_SESSION['lang']['carinopo'] . ':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'] . ':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /> ';
echo '<button class=mybutton onclick=cariNopo()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset></td>';
echo '<td>&nbsp;</td>';
echo '</tr>' . "\r\n" . '         </table> ';
echo '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="list_po">';
OPEN_BOX();
echo "\t\r\n" . '<input type="hidden" id="user_id" name="user_id" value="';
echo $_SESSION['standard']['userid'];
echo '" />' . "\r\n" . '<input type="hidden" id="proses" name="proses" value="insert" />' . "\r\n" . '<fieldset>' . "\r\n" . '    <legend>';
echo $_SESSION['lang']['listpo']." Lokal";
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
echo '</div>' . "\r\n" .  '    ';
$lokasitugas = substr($_SESSION['empl']['lokasitugas'],0,3);
$klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.`datakaryawan` where lokasitugas like "'.$lokasitugas.'%" AND (tanggalkeluar is NULL or tanggalkeluar = \'0000-00-00\') and kodegolongan in (select kodegolongan from sdm_5golongan where alias=\'po_sign\') and bagian in (select kode from sdm_5departemen where alias=\'purch\') order by namakaryawan asc';

// $klq = "select namakaryawan,karyawanid,bagian,lokasitugas from  datakaryawan ".
// "where (tanggalkeluar is NULL or tanggalkeluar = '0000-00-00') and ".
// //"kodegolongan in (select kodegolongan from sdm_5golongan where alias='po_sign') and ".
// "bagian in (select kode from sdm_5departemen where kode='HO_PROC') order by namakaryawan asc";

#exit(mysql_error());
($qry = mysql_query($klq)) || true;

while ($rst = mysql_fetch_object($qry)) {
	$sBag = 'select nama from ' . $dbname . '.sdm_5departemen where kode=\'' . $rst->bagian . '\'';

	#exit(mysql_error());
	($qBag = mysql_query($sBag)) || true;
	$rBag = mysql_fetch_assoc($qBag);
	$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . ' [' . $rst->lokasitugas . '] [' . $rBag['nama'] . ']</option>';
}
?>

    <div id="form_po_lokal" style="width:100%;display:none;" >
		<fieldset id=""><legend><span class="judul">&nbsp;</span></legend>
			<div id="contentBox" style="overflow:auto;">
				<fieldset>
					<legend>Form PO Lokal</legend>
					<table cellspacing="1" border="0">
						<tbody><tr>
							<td>No. PO</td>
							<td>:</td>
							<td><input type="text" name="no_po" id="no_po" class="myinputtext" style="width:150px;" ></td>
						</tr>
						<tr>
							<td>Tanggal</td>
							<td>:</td>
							<td><input type="text" name="tgl_po" id="tgl_po" class="myinputtext" value="<?php echo date("d-m-Y"); ?>" readonly="readonly" style="width:150px;"></td>
						</tr>
						 <tr>
							<td>Nama Supplier</td>
							<td>:</td>
							<td>
										<select id="supplier_id" name="supplier_id" onchange="get_supplier()" style="width:150px;">
											<option value=""></option>
										<?php
											$optSupplier = '';											
											$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier  where kodekelompok=\'S001\' and status=1 and namasupplier != "" and namasupplier is not null order by namasupplier asc';

											#exit(mysql_error());
											($query = mysql_query($sql)) || true;

											while ($res = mysql_fetch_assoc($query)) {
											?>
												<option value="<?php echo $res['supplierid']; ?>"><?php echo $res['namasupplier']; ?></option>
												
											<?php
											}
										?>
											
											
										</select>
								<img src="images/search.png" class="resicon" title="Cari Supplier" onclick="searchSupplier('Cari Supplier','<fieldset><legend>Cari</legend>Cari&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>Cari</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);"></td>
						</tr>
						 <tr>
							<td>Rekening Bank</td>
										<td>:</td>
										<td><input type="text" id="bank_acc" name="bank_acc" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>
						</tr>
								<tr>
							<td>NPWP</td>
										<td>:</td>
										<td><input type="text" id="npwp_sup" name="npwp_sup" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>
						</tr>
						<tr>
							<td>Mata Uang</td>
										<td>:</td>
										<td><select id="mtUang" name="mtUang" style="width:150px;" onchange="getKurs()">
											<option value=""></option>
										<?php
											$sMt = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';
											($qMt = mysql_query($sMt)) || true;

											while ($rMt = mysql_fetch_assoc($qMt)) {
												?>
												<option value="<?php echo $rMt['kode']; ?>"><?php echo $rMt['kodeiso']; ?></option>
												<?php
												
											}
										?>											
											</select>
										</td>
						</tr>
						 <tr>
							<td>Kurs</td>
										<td>:</td>
										<td><input type="text" class="myinputtext" id="Kurs" name="Kurs" style="width:150px;" onkeypress="return angka_doang(event)" value=""></td>
						</tr>
						  <tr>
							<td>Signature</td>
										<td>:</td>
										<td>
											<select id="persetujuan_id" name="persetujuan_id" style="width:150px;">
												<?php
													// $klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.`datakaryawan` where tanggalkeluar is NULL and left(kodegolongan,1)<=\'3\' and bagian=\'HO_PROC\' order by namakaryawan asc';
													// echo $klq;
													// #exit(mysql_error());
													$klq = 'select namakaryawan,karyawanid,bagian,lokasitugas from ' . $dbname . '.`datakaryawan` where lokasitugas like "'.$lokasitugas.'%" AND (tanggalkeluar is NULL or tanggalkeluar = \'0000-00-00\') and kodegolongan in (select kodegolongan from sdm_5golongan where alias=\'po_sign\') and bagian in (select kode from sdm_5departemen where alias=\'purch\') order by namakaryawan asc';

													($qry = mysql_query($klq)) || true;

													while ($rst = mysql_fetch_object($qry)) {
														$sBag = 'select nama from ' . $dbname . '.sdm_5departemen where kode=\'' . $rst->bagian . '\'';

														#exit(mysql_error());
														($qBag = mysql_query($sBag)) || true;
														$rBag = mysql_fetch_assoc($qBag);
														?>
														<option value="<?php echo $rst->karyawanid; ?>"><?php echo $rst->namakaryawan; ?> [<?php echo $rst->lokasitugas; ?>] [<?php echo $rBag['nama']; ?>]</option>
														<?php														
													}
												?>
												
											</select>
											<input type="hidden" id="persetujuan_id2" value="0">
										</td>
						</tr>

				 
						</tbody>
					</table>

					<fieldset style="width:60%">
							<legend>Daftar Barang</legend>
						<table id="detail_content_table" name="detail_content_table" cellspacing="1" border="0">
							<tbody id="detail_content" name="detail_content">
								<tr>
									<td>
										<table id="ppDetailTable">
											<thead class="rowheader">
												<tr>
													<td>No. PP</td>
													<td>Kode Barang</td>
													<td>Nama Barang</td>
													<td>Spesifikasi</td>
													<td>Satuan</td>
													<td>Jumlah Dipesan</td>													
													<td>Harga Satuan</td>
													<td>Sub Total</td>
													<td>Action</td>
												</tr>
											</thead>
											<tbody id="detailBody">
											<?php
												for($i=0;$i<15;$i++){
											?>
												<tr id="detail_tr_<?php echo $i; ?>" class="rowcontent">
													<td id="dtNopp_<?php echo $i; ?>"><input id="rnopp_<?php echo $i; ?>" name="rnopp_<?php echo $i; ?>" class="myinputtext" type="text" value="" style="width:120px" ></td>
													
													<!-- td id="dtKdbrg_0"><input id="rkdbrg_0" name="rkdbrg_0" class="myinputtext" type="text" onkeypress="return tanpa_kutip(event)" value="31200018" style="width:120px" disabled="disabled"></td -->
													
													<?php
														echo '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('kd_brg_' . $i . '', 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
														echo '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('nm_brg_' . $i . '', 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
														
														
													?>
													<!-- td><input id="nm_brg_0" name="nm_brg_0" class="myinputtext" type="text" onkeypress="return tanpa_kutip(event)" value="Klerat" style="width:120px" disabled="disabled"></td -->
													<td><textarea id="spek_brg_<?php echo $i; ?>" cols="25" style="height:13px;"></textarea></td>
													<?php
														echo '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('sat_' . $i . '', 'txt', '', array('style' => 'width:70px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '<!-- img src=images/search.png class=dellicon title=' . $_SESSION['lang']['find'] . ' onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=' . $i . '><div id=container></div>\',event)"; --><input type=hidden id=oldKdbrg_' . $i . ' name=oldKdbrg_' . $i . '>' . '</td>';
													?>
													<td><input id="jmlhDiminta_<?php echo $i; ?>" name="jmlhDiminta_<?php echo $i; ?>" class="myinputtextnumber" onkeypress="return angka_doang(event)" type="text" value="0" style="width:70px" onblur="display_number('<?php echo $i; ?>')" onkeyup="calculate('<?php echo $i; ?>')"></td>
													<td><input id="harga_satuan_<?php echo $i; ?>" name="harga_satuan_<?php echo $i; ?>" class="myinputtextnumber" onkeypress="return angka_doang(event)" type="text" value="" style="width:100px" onkeyup="calculate('<?php echo $i; ?>')" onblur="display_number('<?php echo $i; ?>')" onfocus="normal_number('<?php echo $i; ?>')"></td>
													<td><input id="total_<?php echo $i; ?>" name="total_<?php echo $i; ?>" class="myinputtextnumber" onkeypress="return angka_doang(event)" type="text" value="" style="width:100px" disabled="disabled"><input type="hidden" id="subTotal_0"></td>
													<td align="center"><img id="detail_delete_<?php echo $i; ?>" title="Hapus" class="zImgBtn" onclick="deleteDetail('<?php echo $i; ?>')" src="images/delete_32.png"></td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Sub Total</td>
													<td><input type="text" id="total_harga_po" name="total_harga_po" disabled="" class="myinputtextnumber" style="width:100px"></td>
												</tr>
												<tr style="display: none;">
													<td>&nbsp;</td>
													<td colspan="6" align="right">Misc Dgn Ppn</td>
													<td><input type="text" id="miscNppn" name="miscNppn" class="myinputtextnumber" style="width:100px" onblur="calculateMiscPpn(0)" onfocus="normalmiscppn(0)" onkeypress="return angka_doang(event)"></td>
												</tr>
												
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Diskon</td>
													<td><input type="text" id="angDiskon" name="angDiskon" class="myinputtextnumber" style="width:100px" onkeyup="calculate_angDiskon()" onkeypress="return angka_doang(event)" onblur="getZero()"></td>
												</tr>
													<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Diskon (%)</td>
													<td><input type="text" id="diskon" name="diskon" class="myinputtextnumber" style="width:100px" onkeyup="calculate_diskon()" maxlength="3" onkeypress="return angka_doang(event)" onblur="getZero()"> </td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">PPn (%)</td>
													<td><input type="text" id="ppN" name="ppN" class="myinputtextnumber" style="width:100px" onkeyup="calculatePpn()" maxlength="2" onkeypress="return angka_doang(event)" onblur="getZero()">  <input type="hidden" id="ppn" name="ppn" class="myinputtext" onkeypress="return angka_doang(event)" style="width:100px" onblur="getZero()"><span id="hslPPn"> </span> </td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">
														PPh (%)
													</td>
													<td>
														<input type="text" id="ppH" name="ppH" class="myinputtextnumber" style="width:100px" onkeyup="calculatePph()" maxlength="2" onkeypress="return angka_doang(event)" onblur="getZero()">
														<input type="hidden" id="pph" name="pph" class="myinputtext" onkeypress="return angka_doang(event)" style="width:100px" onblur="getZero()">
														<span id="hslPPh"> </span> 
													</td>
												</tr>
																								
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Ongkos Kirim</td>
													<td><input type="text" id="ongKirim" class="myinputtextnumber" style="width:100px" onkeypress="return angka_doang(event)" onblur="calculateMiscPpn(1)" onfocus="normalmiscppn(1)"></td>
												</tr>
												 <tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Ppn Ongkos Kirim  (%)</td>
													<td><input type="text" id="ongKirimPPn" class="myinputtextnumber" style="width:100px" onkeypress="return angka_doang(event)" onblur="calculateOngkirPPn()" maxlength="2"></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Misc</td>
													<td>
														<input type="number" id="misc" name="misc" class="myinputtextnumber" style="width:100px" onblur="calculateMiscPpn(2)" onfocus="normalmiscppn(2)">
													</td>
												</tr>
												 <tr>
													<td>&nbsp;</td>
													<td colspan="6" align="right">Grand Total</td>
													<td><input type="text" id="grand_total" name="grand_total" disabled="" class="myinputtextnumber" style="width:100px"></td>
												</tr>
													<input type="hidden" id="sub_total" name="sub_total"><input type="hidden" id="nilai_diskon" name="nilai_diskon">
											</tbody> 
											<br>
										</table>

										<table cellspacing="1" border="0">
											<tbody>
												<tr>
													<td>Tanggal Kirim</td>
													<td>:</td>
													<td><input type="text" class="myinputtext" id="tgl_krm" name="tgl_krm" onmousemove="setCalendar(this.id);" onkeypress="return false;" maxlength="10" ></td>
												</tr>
												<tr>
													<td>Lokasi Pengiriman</td>
													<td>:</td>
													<td>
														<select id="tmpt_krm" name="tmpt_krm1" style="width:200px;">
															<option value=""></option>
														<?php
															$sKrm = 'select id_franco,franco_name from ' . $dbname . '.setup_franco where status=0 order by franco_name asc';

															#exit(mysql_error($conn));
															($qKrm = mysql_query($sKrm)) || true;

															while ($rKrm = mysql_fetch_assoc($qKrm)) {
																?>
																<option value="<?php echo $rKrm['id_franco']; ?>"><?php echo $rKrm['franco_name']; ?></option>
																<?php																
															}
														?>
														</select>
													<!--<input type='text'  id='tmpt_krm' name='tmpt_krm' maxlength='45' class='myinputtext' onkeypress='return tanpa_kutip(event);' style=width:200px />--></td>
												</tr>
												<tr>
													<td>Pembayaran</td>
													<td>:</td>
													<td><select id="crByr" style="width:200px"><option value="CASH">CASH</option><option value="CREDIT">CREDIT</option></select></td>
												</tr>
		
												<tr>
													<td>Syarat Pembayaran</td>
																<td>:</td>
																<td><input type="text" id="term_pay" name="term_pay" class="myinputtext" onkeypress="return tanpa_kutip(event);" style="width:200px"></td>
												</tr>
												 <tr>
													<td>Keterangan</td>
																<td>:</td>
																<td><textarea id="ketUraian" name="ketUraian" onkeypress="return tanpa_kutip(event);" cols="80" rows="9"></textarea></td>
												</tr>	
												<tr>
													<td>Purchaser</td>
																<td>:</td>
																<td><select id="purchaser_id" name="purchaser_id" style="width:150px;" ><?php echo $optPur; ?></select></td>
																<!-- <td><input type="text" id="purchaser_id" name="purchaser_id" class="myinputtext" disabled="disabled" value="Riqke Elfitrah" style="width:200px;"></td> -->
												</tr>
											</tbody>
										</table>

									</td>
								</tr>

							</tbody>
						</table>
					</fieldset>
					<table cellspacing="1" border="0">
						<tbody>
							<tr>
								<td colspan="3">
									<button class="mybutton" onclick="save_headher()">Simpan</button>
									<button class="mybutton" onclick="cancel_headher()">Batal</button>
													<!--<button class="mybutton"  >Selesai</button>-->
								</td>
							</tr> 

						</tbody>
					</table>
				</fieldset>
			</div>
		</fieldset>
    </div>
<?php
echo close_body();
?>