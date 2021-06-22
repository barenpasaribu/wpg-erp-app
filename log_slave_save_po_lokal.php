<?php


	require_once 'master_validation.php';
	require_once 'config/connection.php';
	include 'lib/eagrolib.php';
	include_once 'lib/zLib.php';

	$supplier_id = $_POST['supplier_id'];
	$proses = $_POST['proses'];
	$nopo = $_POST['nopo'];
	$tgl_po = tanggalsystem($_POST['tglpo']);
	$sub_total = $_POST['subtot'];
	$disc = $_POST['diskon'];
	$nilai_dis = $_POST['nildiskon'];
	$nppn = $_POST['ppn'];
	$npph = $_POST['pph'];
	$tanggl_kirim = tanggalsystemd($_POST['tgl_krm']);
	$lokasi_krm = $_POST['lok_kirim'];
	$cr_pembayaran = $_POST['cara_pembayarn'];
	$nilai_po = $_POST['grand_total'];
	$purchaser = $_POST['purchser_id'];
	$lokasi_kirim = $_POST['lokasi_krm'];
	$persetujuan = $_POST['id_user'];
	$comment = $_POST['cm_hasil'];
	$jmlh_realisasi = $_POST['jmlh_realisasi'];
	$jmlh_diminta = $_POST['jmlh_diminta'];
	$jnopp = $_POST['jnopp'];
	$jkdbrg = $_POST['jkdbrg'];
	$ketUraian = $_POST['ketUraian'];
	$mtUang = $_POST['mtUang'];
	$Kurs = intval($_POST['Kurs']);
	$nmSupplier = $_POST['nmSupplier'];

	switch ($proses) {
	case 'cek_supplier':
		$sql = 'select * from ' . $dbname . '.log_5supplier where supplierid=\'' . $supplier_id . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);
		echo $res['rekening'] . ',';
		echo $res['npwp'];
		break;

	case 'insert':
		if (($supplier_id == '') || ($nopo == '') || ($disc == '') || ($tanggl_kirim == '') || ($lokasi_kirim == '') || ($mtUang == '')) {
			echo 'warning: Please complete the form';
			exit();
		}

		if ($mtUang != 'IDR') {
			$Kurs = floatval($Kurs);
			$sGetKurs = 'select distinct kurs,kode from ' . $dbname . '.setup_matauangrate where kode=\'' . $mtUang . '\' order by daritanggal desc';

			#exit(mysql_error());
			($qGetKurs = mysql_query($sGetKurs)) || true;
			$rGetKurs = mysql_fetch_assoc($qGetKurs);

			if ($Kurs == '0') {
				exit('Error: Please provide curs corrensponding to currency, curs for ' . $rGetKurs['kode'] . ' :' . $rGetKurs['kurs']);
			}
		}
		else {
			$Kurs = 1;
		}

		$awl = 0;
		$i = 1;

	/*
		foreach ($_POST['kdbrg'] as $row => $cntn) {
			$kdbrg = $cntn;
			$b = count($_POST['kdbrg']);
			$nopp = $_POST['nopp'][$row];
			$jmlh_pesan = $_POST['rjmlh_psn'][$row];
			$hrg_satuan = $_POST['rhrg_sat'][$row];
			$hrg_sblmdiskon = str_replace(',', '', $hrg_satuan);
			$satuan = $_POST['rsatuan_unit'][$row];
			$diskon = ($hrg_sblmdiskon * $disc) / 100;
			$hrg_diskon = $hrg_sblmdiskon - $diskon;
			$sqjmlh = 'select selisih,jlpesan,realisasi,purchaser from ' . $dbname . '.log_sudahpo_vsrealisasi_vw where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kdbrg . '\'';

			#exit(mysql_error());
			($qujmlh = mysql_query($sqjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qujmlh);
			$jmlh_pesan = $resjmlh['jlpesan'] + $jmlh_pesan;
			if (($jmlh_pesan == '') || ($hrg_satuan == '')) {
				echo 'warning: Please complete the form';
				exit();
			}

			if ($purchaser != $resjmlh['purchaser']) {
				$purchaser = $resjmlh['purchaser'];
			}

			if ($resjmlh['realisasi'] < $jmlh_pesan) {
				echo 'warning : ' . "\n" . 'Total requested (' . $jmlh_pesan . ') to material code ' . $kdbrg . '.(' . $jmlh_pesan . ') =' . "\r\n" . '                                ' . "\n" . 'Volum of previous request (' . $resjmlh['jlpesan'] . ')' . "\n" . 'Volum on current request (' . $_POST['rjmlh_psn'][$row] . ')' . "\r\n" . '                                ' . "\n" . 'Larger than approved (' . $resjmlh['realisasi'] . ').';
				exit();
			}
		}
	*/
		//cek nopo udah ada belum
		$cek_sql = "select nopo from " . $dbname . ".log_poht where nopo='" . $nopo . "'";	

		#exit(mysql_error());
		($query = mysql_query($cek_sql)) || true;
		$rCek = mysql_num_rows($query);
		if( $rCek > 0){
			echo 'No PO yang dibuat sudah ada';
			exit();
		}
		
			

		if (intval($lokasi_kirim)) {
			$field = '`idFranco`';
		}
		else {
			$field = '`lokasipengiriman`';
		}

		$thisDate = date('Y-m-d');

		if ($nilai_dis == '') {
			$nilai_dis = 0;
		}

		$Kurs = intval($Kurs);
		$ppnongkirim = $_POST['ongKirimPPn'];
		$ongkirim = $_POST['ongkirim'];
		// if (($_POST['ongKirimPPn'] != '') || ($_POST['ongKirimPPn'] != '0')) {
		// 	$ppnongkirim = $ongkirim * ($_POST['ongKirimPPn'] / 100);
		// }
		// else {
		// 	$ppnongkirim = 0;
		// }
		$Ok= 0;
		//insert ke log_prapoht 
		$sql_insert = "insert into ".$dbname.".log_prapoht(kodeorg,nopp,tanggal,nopo,dibuat,catatan,po_lokal) values('LKL','" .$nopo. "','" .$tgl_po. "','" .$nopo. "','" .$_SESSION['standard']['userid']. "','PO Lokal','Y')";
		if (!mysql_query($sql_insert)) {
			$Ok= $Ok+1;
			echo 'Gagal, ['.$sql_insert.']' . mysql_error($conn);
			exit();
		}
		//insert ke log_poht 
		$sql_insert = "insert into ".$dbname.".log_poht(
			kodeorg,nopo,tanggal,kodesupplier,subtotal,
			diskonpersen,nilaidiskon,ppn,pph,nilaipo,
			tanggalkirim,".$field.",syaratbayar,uraian,lokalpusat,
			matauang,kurs,persetujuan1,hasilpersetujuan1,tglp1,
			statuspo,persetujuan2,hasilpersetujuan2,tglp2,tgledit,
			ongkosangkutan,miscppn,misc,ongkirimppn,statusbayar,
			updateby,purchaser,po_lokal) 
			values('".
			$_SESSION['empl']['kodeorganisasi']."','".$nopo."','".$thisDate."','".$supplier_id."','".$sub_total."','".
			$disc."','".$nilai_dis."','".$nppn."','".$npph."','".$nilai_po."','".$tanggl_kirim."','".
			$lokasi_kirim."','".$cr_pembayaran."','".$ketUraian."','0','".$mtUang."','".
			$Kurs."','".$persetujuan."','1','".$thisDate."','2','
			0','1','".$thisDate."','".$thisDate."','".$ongkirim."','".
			$_POST['miscppn']."','".$_POST['misc']."','".$ppnongkirim."','".$_POST['crByr']."','".$_SESSION['standard']['userid']."','".
			$purchaser."','Y')";
		if (!mysql_query($sql_insert)) {
			$Ok= $Ok+2;
			echo 'Gagal,' . mysql_error($conn);
			exit();
		}

	//echo "warning: cek = ".$Ok;
	//exit();
		
		$akumulasi_sql = "";
		foreach ($_POST['kdbrg'] as $row => $isi) {
			$kdbrg = $isi;
			if( $_POST['nopp'][$row] == ""){
				$nopp = "PO Lokal";
			}else{
				$nopp = $_POST['nopp'][$row];
			}
			
			$jmlh_pesan = (real)$_POST['rjmlh_psn'][$row];
			$hrg_satuan = $_POST['rhrg_sat'][$row];
			$hrg_sblmdiskon = str_replace(',', '', $hrg_satuan);
			$ongangkut = str_replace(',', '', $_POST['ongkos_angkut'][$row]);
			$satuan = $_POST['rsatuan_unit'][$row];
			$diskon = ($hrg_sblmdiskon * $disc) / 100;
			$hrg_diskon = $hrg_sblmdiskon - $diskon;
			$hrgSat = $hrg_diskon + ($rongank / $jmlh_pesan);
			$spekBrg = $_POST['spekBrg'][$row];
			
			if($kdbrg != "" && $jmlh_pesan > 0){
				if ($ongangkut == '') {
					$ongangkut = 0;
				}
				
				$sql = "insert into ".$dbname.".log_podt(nopo,jumlahpesan,harganormal,nopp,hargasbldiskon,satuan,catatan,hargasatuan,kodebarang) values('".$nopo."','".$jmlh_pesan."','".$hrg_diskon."','".$nopp."','".$hrg_sblmdiskon."','".$satuan."','".$spekBrg."','".$hrgSat."','".$kdbrg."')";
				$akumulasi_sql .= $sql.";";
				if (!mysql_query($sql)) {
					echo $sql . '-----';
					echo 'Gagal,' . mysql_error($conn);
					exit();
				}
				

				$sdpp = "insert into ".$dbname.".log_prapodt(nopp,kodebarang,jumlah,realisasi,hargasatuan,keterangan,tgl_sdt,create_po,purchaser) values('".$nopo."','".$kdbrg."','".$jmlh_pesan."','".$jmlh_pesan."','".$hrgSat."','PO Lokal','".$thisDate."','1','".$purchaser."') ";
				$akumulasi_sql .= $sdpp.";";
				if (mysql_query($sdpp)) {
					echo '';
				}
				else {
					echo 'Gagal,' . $sdpp . '__' . mysql_error($conn);
					exit();
				}
			}
			
		}
		echo "success";
		break;


	case 'update_data':
		echo ' <table cellspacing=\'1\' border=\'0\' class=\'sortable\'>' . "\n" . '        <thead>' . "\n" . '            <tr class=rowheader>' . "\n" . '                <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\n" . '                                <td>' . $_SESSION['lang']['tgl_po'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['almt_kirim'] . '</td>' . "\n" . '                                <td>' . $_SESSION['lang']['purchaser'] . '</td> ' . "\n" . '                <td>' . $_SESSION['lang']['syaratPem'] . '</td>' . "\n" . '                                 <td>' . $_SESSION['lang']['status'] . '</td>' . "\n" . '                <td>action</td>' . "\n" . '            </tr>' . "\n" . '         </thead>' . "\n" . '         <tbody>';
		$limit = 20;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;

		if ($_SESSION['empl']['kodejabatan'] == '15') {
			$sql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_poht where po_lokal=\'Y\' order by tanggal desc';
			$sql = 'select * from ' . $dbname . '.log_poht where po_lokal=\'Y\' order by tanggal desc limit ' . $offset . ',' . $limit . '';
		}
		else {
			$sql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_poht where purchaser=\'' . $_SESSION['standard']['userid'] . '\' and po_lokal=\'Y\' order by tanggal desc';
			$sql = 'select * from ' . $dbname . '.log_poht where po_lokal=\'Y\' AND kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\'   ORDER BY tanggal desc, LEFT(nopo,3) desc limit ' . $offset . ',' . $limit . '';
		//	$sql = 'select * from ' . $dbname . '.log_poht where purchaser=\'' . $_SESSION['standard']['userid'] . '\' and po_lokal=\'Y\' order by tanggal desc limit ' . $offset . ',' . $limit . '';
		}

		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;

		while ($jsl = mysql_fetch_object($query2)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;

		while ($res = mysql_fetch_object($query)) {
			$no += 1;
			$sql2 = 'select * from ' . $dbname . '.log_5supplier where supplierid=\'' . $res->kodesupplier . '\'';

			#exit(mysql_error());
			($query2 = mysql_query($sql2)) || true;
			$res2 = mysql_fetch_object($query2);
			$skry = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $res->purchaser . '\'';

			#exit(mysql_error());
			($qkry = mysql_query($skry)) || true;
			$rkry = mysql_fetch_assoc($qkry);

			if ($res->stat_release != 1) {
				$stat = 0;
			}
			else {
				$stat = 1;
			}

			if (($res->stat_release == 0) || is_null($res->stat_release)) {
				$stat_po = $_SESSION['lang']['un_release_po'];
				$edit_data = '<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $res->nopo . '\',\'' . tanggalnormal($res->tanggal) . '\',\'' . $res->kodesupplier . '\',\'' . $res->subtotal . '\',\'' . $res->diskonpersen . '\',\'' . $res->ppn . '\',\'' . $res->nilaipo . '\',\'' . $res2->rekening . '\',\'' . $res2->npwp . '\',\'' . $res->nilaidiskon . '\',\'' . $stat . '\',\'' . tanggalnormal($res->tanggalkirim) . '\');" >';
				$delete_data = '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPo(\'' . $res->nopo . '\',\'' . $stat . '\');" >';
				$release_po_button = '<img src=images/application/application_key.png class=resicon onclick=release_po(\'' . $no . '\') title=\'Choose Signature\' />';
			}
			else if ($res->stat_release == 1) {
				$stat_po = $_SESSION['lang']['release_po'];
				$edit_data = '';
				$delete_data = '';
				$release_po_button = '';
			}
			$edit_data = '';
			$release_po_button = '';

			echo "\n" . '                        <tr class=rowcontent id=tr_' . $no . '>' . "\n" . '                            <td id=td_nopo_' . $no . '>' . $res->nopo . '</td>' . "\n" . '                            <td id=td_ns_' . $no . '>' . $res2->namasupplier . '</td>' . "\n" . '                                                        <td id=td_tgl_' . $no . '>' . tanggalnormal($res->tanggal) . '</td>' . "\n" . '                            <td id=td_tgl_krm_' . $no . '>' . tanggalnormal($res->tanggalkirim) . '</td>' . "\n" . '                                                        <td>' . $res->lokasipengiriman . '</td>' . "\n" . '                                                        <td>' . $rkry['namakaryawan'] . '</td>' . "\n" . '                            <td>' . $res->syaratbayar . '</td>' . "\n" . '                                                         <td>' . $stat_po . '</td>' . "\n" . '                                                ';
			if (($res->purchaser == $_SESSION['standard']['userid']) || ($_SESSION['empl']['kodejabatan'] == '15')) {
				echo '<td>' . $edit_data . '';
				echo '' . $delete_data . '' . $release_po_button . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $res->nopo . '\',\'\',\'log_slave_print_log_po\',event);">' . "\n" . '                                                        </td>';
			}
			else {
				echo "\n" . '                                                        <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $res->nopo . '\',\'\',\'log_slave_print_log_po\',event);">' . "\n" . '                                                        </td>';
			}

			echo '</tr>';
		}

		echo "\n" . '                                 <tr><td colspan=8 align=center>' . "\n" . '                                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\n" . '                                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\n" . '                                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\n" . '                                </td>' . "\n" . '                                </tr></tbody></table>';
		break;

	case 'edit_po':
		if (($supplier_id == '') || ($nopo == '') || ($disc == '')) {
			echo 'warning: Please complete the form';
			exit();
		}

		$scek = 'select statuspo from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

		#exit(mysql_error($conn));
		($qcek = mysql_query($scek)) || true;
		$rcek = mysql_fetch_assoc($qcek);

		if ($rcek['statuspo'] == 1) {
			echo 'warning :  PO : ' . $nopo . ' being under verification process';
			exit();
		}
		else {
			$tglSkrng = date('Y-m-d');
		}

		$kode_org = substr($nopo, 20, 4);
		$strx = 'update ' . $dbname . '.log_poht set `kodesupplier`=\'' . $supplier_id . '\',`subtotal`=\'' . $sub_total . '\',' . "\n" . '                        `diskonpersen`=\'' . $disc . '\',`nilaidiskon`=\'' . $nilai_dis . '\',`ppn`=\'' . $nppn . '\',`pph`=\'' . $npph . '\',`nilaipo`=\'' . $nilai_po . '\',' . "\n" . '                        `tanggalkirim`=\'' . $tanggl_kirim . '\',`lokasipengiriman`=\'' . $lokasi_kirim . '\',`syaratbayar`=\'' . $cr_pembayaran . '\',' . "\n" . '                        `uraian`=\'' . $ketUraian . '\',`tgledit`=\'' . $tglSkrng . '\'  where nopo=\'' . $nopo . '\'';
		saveLog($strx);
		if (!mysql_query($strx)) {
			echo 'Gagal,' . mysql_error($conn);
			exit();
		}

		foreach ($_POST['kdbrg'] as $row => $isi) {
			$kdbrg = $isi;
			$nopp = $_POST['nopp'][$row];
			$jmlh_pesan = $_POST['rjmlh_psn'][$row];
			$hrg_satuan = $_POST['rhrg_sat'][$row];
			$hrg_sblmdiskon = str_replace(',', '', $hrg_satuan);
			$diskon = ($hrg_sblmdiskon * $disc) / 100;
			$hrg_diskon = $hrg_sblmdiskon - $diskon;
			$mat_uang = $_POST['rmat_uang'][$row];
			$satuan = $_POST['rsatuan_unit'][$row];
			$sqjmlh = 'select selisih,jlpesan,realisasi from ' . $dbname . '.log_sudahpo_vsrealisasi_vw where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kdbrg . '\'';

			#exit(mysql_error());
			($qujmlh = mysql_query($sqjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qujmlh);

			if ($jmlh_pesan <= $resjmlh['realisasi']) {
				$sql = 'update ' . $dbname . '.log_podt set `jumlahpesan`=\'' . $jmlh_pesan . '\',`hargasatuan`=\'' . $hrg_diskon . '\',`matauang`=\'' . $mat_uang . '\',`hargasbldiskon`=\'' . $hrg_sblmdiskon . '\',`satuan`=\'' . $satuan . '\'' . "\n" . '                                        where nopo=\'' . $nopo . '\' and kodebarang=\'' . $kdbrg . '\' and nopp=\'' . $nopp . '\'';
				saveLog($sql);
				if (!mysql_query($sql)) {
					echo 'Gagal,' . mysql_error($conn);
					exit();
				}
				else {
					$sCek = 'select distinct create_po from ' . $dbname . '.log_prapodt where nopp=\'' . $_POST['nopp'][$row] . '\' and kodebarang=\'' . $isi . '\'';

					#exit(mysql_error());
					($qCek = mysql_query($sCek)) || true;
					$rCek = mysql_fetch_assoc($qCek);
					if (($rCek['create_po'] == '') || ($rCek['create_po'] == '0')) {
						$sUpdate = 'update ' . $dbname . '.log_prapodt set create_po=1 where nopp=\'' . $_POST['nopp'][$row] . '\' and kodebarang=\'' . $isi . '\'';
						saveLog($sUpdate);
						if (!mysql_query($sUpdate)) {
							echo 'Gagal,' . mysql_error($conn);
							exit();
						}
					}
				}
			}
			else {
				echo 'warning : Order volume (' . $jmlh_pesan . ') must lower or equal as approved (' . $resjmlh['realisasi'] . ')';
				exit();
			}
		}

		break;

	case 'delete_all':
		$scek = 'select statuspo from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

		#exit(mysql_error($conn));
		($qcek = mysql_query($scek)) || true;
		$rcek = mysql_fetch_assoc($qcek);

		if (2 < $rcek['statuspo']) {
			echo 'warning : PO : ' . $nopo . ' being on verification process';
			exit();
		}

		$sCekGdng = 'select distinct nopo from ' . $dbname . '.log_transaksi_vw where nopo=\'' . $nopo . '\'';

		#exit(mysql_error($conn));
		($qCekGdng = mysql_query($sCekGdng)) || true;
		$rCekGdng = mysql_num_rows($qCekGdng);

		if (0 < $rCekGdng) {
			exit('Error: PO :  ' . $nopo . ' has arrived at warehouse, can not delete');
		}

		$sql = 'delete from ' . $dbname . '.log_podt where nopo=\'' . $nopo . '\'';
		saveLog($sql);
		if (!mysql_query($sql)) {
			echo 'Gagal,' . mysql_error($conn);
			exit();
		}

		$sql2 = 'delete from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';
		saveLog($sql2);
		if (!mysql_query($sql2)) {
			echo 'Gagal,' . mysql_error($conn);
			exit();
		}

		break;

	case 'insert_release_po':
		$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\' and lokalpusat=\'1\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$rest = mysql_fetch_assoc($query);
		echo '<br />' . "\n" . '                                        <div id=test style=display:block>' . "\n" . '                                        <fieldset>' . "\n" . '                                        <legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . '  /></legend>' . "\n" . '                                        <table cellspacing=1 border=0>' . "\n" . '                                        <tr>' . "\n" . '                                        <td colspan=3>' . "\n" . '                                        ' . $_SESSION['lang']['penandatangan'] . ' :</td>' . "\n" . '                                        </tr>' . "\n" . '                                        <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\n" . '                                        <td>:</td>' . "\n" . '                                        <td valign=top>';
		$optPur = '';
		$se = substr($nopo, 15, 4);
		$klq = 'select karyawanid,namakaryawan from ' . $dbname . '.`datakaryawan` where tipekaryawan=\'5\' and karyawanid!=\'' . $user_id . '\' order by namakaryawan asc';

		#exit(mysql_error());
		($qry = mysql_query($klq)) || true;

		while ($rst = mysql_fetch_object($qry)) {
			$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . '</option>';
		}

		echo "\n" . '                                                <select id=persetujuan_id name=persetujuan_id>' . "\n" . '                                                        ' . $optPur . ';' . "\n" . '                                                </select></td></tr>' . "\n" . '                                                <tr>' . "\n" . '                                                <td colspan=3 align=center>' . "\n" . '                                                <button class=mybutton onclick=proses_release_po() title="Choose signature" >' . $_SESSION['lang']['tandatangan'] . '</button>' . "\n" . '                                                <button class=mybutton onclick=cancel_po() title="close">' . $_SESSION['lang']['cancel'] . '</button>' . "\n" . '                                                </td></tr></table><br />' . "\n" . '                                                <input type=hidden name=proses id=proses  />' . "\n" . '                                                </fieldset></div>';
		break;

	case 'get_form_approval':
		$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\' and lokalpusat=\'0\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$rest = mysql_fetch_assoc($query);
		echo '<br />' . "\n" . '                                        <div id=test style=display:block>' . "\n" . '                                        <fieldset>' . "\n" . '                                        <legend><input type=text readonly=readonly name=snopo id=snopo value=' . $nopo . '  /></legend>' . "\n" . '                                        <table cellspacing=1 border=0>' . "\n" . '                                        <tr>' . "\n" . '                                        <td colspan=3>' . "\n" . '                                        Submit to the next verification process :</td>' . "\n" . '                                        </tr>' . "\n" . '                                        <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\n" . '                                        <td>:</td>' . "\n" . '                                        <td valign=top>';
		$optPur = '';
		$klq = 'select * from ' . $dbname . '.`datakaryawan` where tipekaryawan=\'5\' and karyawanid!=\'' . $user_id . '\'';

		#exit(mysql_error());
		($qry = mysql_query($klq)) || true;

		while ($rst = mysql_fetch_object($qry)) {
			$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . '</option>';
		}

		echo "\n" . '                                                <select id=persetujuan_id name=persetujuan_id>' . "\n" . '                                                        ' . $optPur . ';' . "\n" . '                                                </select></td></tr>' . "\n" . '                                                <tr>' . "\n" . '                                                <td colspan=3 align=center>' . "\n" . '                                                <button class=mybutton onclick=forward_po() title="Submission to the next verificator" >' . $_SESSION['lang']['diajukan'] . '</button>' . "\n" . '                                                <button class=mybutton onclick=cancel_po() title="Close>' . $_SESSION['lang']['cancel'] . '</button>' . "\n" . '                                                </td></tr></table><br />' . "\n" . '                                                <input type=hidden name=proses id=proses  />' . "\n" . '                                                </fieldset></div>' . "\n\n" . '                                                <div id=close_po style="display:none;">' . "\t\n" . '                                                <fieldset><legend><input type=text id=snopo name=snopo disabled value=\'' . $nopo . '\' /></legend>' . "\n" . '                                                <p align=center>Processing this PO, Are you sure</p><br />' . "\n" . '                                                <button class=mybutton onclick=proses_release_po() title="Process" >' . $_SESSION['lang']['approve'] . '</button>' . "\n" . '                                                <button class=mybutton onclick=cancel_po() title="Close">' . $_SESSION['lang']['cancel'] . '</button>' . "\n" . '                                                </fieldset></div>' . "\n" . '                                                ';
		break;

	case 'proses_release_po':
		$tgl_klo = date('Y-m-d');
		$sql = 'update ' . $dbname . '.log_poht set statuspo=\'2\',hasilpersetujuan1=\'1\',tanggal=\'' . $tgl_klo . '\',persetujuan1=\'' . $persetujuan . '\',tglp1=\'' . $tgl_klo . '\' where nopo=\'' . $nopo . '\'';
		saveLog($sql);
		if (mysql_query($sql)) {
			echo '';
		}
		else {
			echo 'Gagal,' . mysql_error($conn);
		}

		break;

	case 'cari_nopo':
		echo '<div style="overflow:auto; height:450px;"> <table cellspacing=\'1\' border=\'0\' class=\'sortable\'>' . "\n" . '        <thead>' . "\n" . '            <tr class=rowheader>' . "\n" . '                <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\n" . '                                <td>' . $_SESSION['lang']['tgl_po'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\n" . '                <td>' . $_SESSION['lang']['almt_kirim'] . '</td>' . "\n" . '                                <td>' . $_SESSION['lang']['purchaser'] . '</td> ' . "\n" . '                <td>' . $_SESSION['lang']['syaratPem'] . '</td>' . "\n" . '                                 <td>' . $_SESSION['lang']['status'] . '</td>' . "\n" . '                <td>action</td>' . "\n" . '            </tr>' . "\n" . '         </thead>' . "\n" . '         <tbody>';
		
		$txt_search = '';
		$txt_tgl = '';
		if (isset($_POST['txtSearch'])) {
			$txt_search = $_POST['txtSearch'];
			$txt_tgl = tanggalsystem($_POST['tglCari']);
			$txt_tgl_t = substr($txt_tgl, 0, 4);
			$txt_tgl_b = substr($txt_tgl, 4, 2);
			$txt_tgl_tg = substr($txt_tgl, 6, 2);
			if( $txt_tgl_t != "" && $txt_tgl_b != "" && $txt_tgl_tg != "" ){
				$txt_tgl = $txt_tgl_t . '-' . $txt_tgl_b . '-' . $txt_tgl_tg;
			}
			
		}
		

		if ($txt_search != '') {
			$where = ' and nopo LIKE  \'%' . $txt_search . '%\'';
		}
		else if ($txt_tgl != '') {
			$where = ' and tanggal LIKE \'%' . $txt_tgl . '%\'';
		}
		else if (($txt_tgl != '') && ($txt_search != '')) {
			$where = ' and nopo LIKE \'%' . $txt_search . '%\' or tanggal LIKE \'%' . $txt_tgl . '%\' ';
		}

		if ($_SESSION['empl']['kodejabatan'] != '5') {
			$where .= ' and purchaser=\'' . $_SESSION['standard']['userid'] . '\'';
		}

		$strx = 'select * from ' . $dbname . '.log_poht where po_lokal=\'Y\' ' . $where . ' order by tanggal desc';
		
		if (mysql_query($strx)) {
			$query = mysql_query($strx);
			$numrows = mysql_num_rows($query);

			if ($numrows < 1) {
				echo '<tr class=rowcontent><td colspan=10>Not Found</td></tr>';
			}
			else {
				while ($res = mysql_fetch_object($query)) {
					$sql2 = 'select * from ' . $dbname . '.log_5supplier where supplierid=\'' . $res->kodesupplier . '\'';

					#exit(mysql_error());
					($query2 = mysql_query($sql2)) || true;
					$res2 = mysql_fetch_object($query2);
					$skry = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $res->purchaser . '\'';

					#exit(mysql_error());
					($qkry = mysql_query($skry)) || true;
					$rkry = mysql_fetch_assoc($qkry);

					if ($res->stat_release != 1) {
						$stat = 0;
					}
					else {
						$stat = 1;
					}

					if ($res->stat_release == 0) {
						$stat_po = $_SESSION['lang']['un_release_po'];
						$edit_data = '<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $res->nopo . '\',\'' . tanggalnormal($res->tanggal) . '\',\'' . $res->kodesupplier . '\',\'' . $res->subtotal . '\',\'' . $res->diskonpersen . '\',\'' . $res->ppn . '\',\'' . tanggalnormald($res->tanggalkirim) . '\',\'' . $res->syaratbayar . '\',\'' . $res->nilaipo . '\',\'' . $res->purchaser . '\',\'' . $res->lokasipengiriman . '\',\'' . $res2->rekening . '\',\'' . $res2->npwp . '\',\'' . $res->nilaidiskon . '\',\'' . $stat . '\');" >';
						$delete_data = '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPo(\'' . $res->nopo . '\',\'' . $stat . '\');" >';
						$release_po_button = '<img src=images/application/application_key.png class=resicon onclick=release_po(\'' . $no . '\') title=\'Release This PO\' />';
					}
					else if ($res->stat_release == 1) {
						$stat_po = $_SESSION['lang']['release_po'];
						$edit_data = '';
						$delete_data = '';
						$release_po_button = '';
					}
					$edit_data = '';
					$release_po_button = '';
					
					echo "\n" . '                        <tr class=rowcontent id=tr_' . $no . '>' . "\n" . '                            <td id=td_nopo_' . $no . '>' . $res->nopo . '</td>' . "\n" . '                            <td id=td_ns_' . $no . '>' . $res2->namasupplier . '</td>' . "\n" . '                                                        <td id=td_tgl_' . $no . '>' . tanggalnormal($res->tanggal) . '</td>' . "\n" . '                            <td id=td_tgl_krm_' . $no . '>' . tanggalnormal($res->tanggalkirim) . '</td>' . "\n" . '                                                        <td>' . $res->lokasipengiriman . '</td>' . "\n" . '                                                        <td>' . $rkry['namakaryawan'] . '</td>' . "\n" . '                            <td>' . $res->syaratbayar . '</td>' . "\n" . '                                                         <td>' . $stat_po . '</td>' . "\n" . '                                                ';
					if (($res->purchaser == $_SESSION['standard']['userid']) || ($_SESSION['empl']['kodejabatan'] == '15')) {
						echo '<td>' . $edit_data . '';
						echo '' . $delete_data . '' . $release_po_button . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $res->nopo . '\',\'\',\'log_slave_print_log_po_lokal\',event);">' . "\n" . '                                                        </td>';
					}
					else {
						echo "\n" . '                                                        <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $res->nopo . '\',\'\',\'log_slave_print_log_po\',event);">' . "\n" . '                                                        </td>';
					}

					echo '</tr>';
				}

				echo '</tbody></table></div>' . "\n" . '                                 <input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
			}
		}

		break;

	case 'getNotifikasi':
		$Sorg = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';

		#exit(mysql_error());
		($qOrg = mysql_query($Sorg)) || true;

		while ($rOrg = mysql_fetch_assoc($qOrg)) {
			if ($_SESSION['empl']['kodejabatan'] == '15') {
				$sList = 'select count(*) as jmlhJob from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw  where (kodept=\'' . $rOrg['kodeorganisasi'] . '\' and lokalpusat=\'1\' and status!=\'3\') and (selisih>0 or selisih is null)';
			}
			else {
				$sList = 'select count(*) as jmlhJob from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw  where (kodept=\'' . $rOrg['kodeorganisasi'] . '\' and purchaser=\'' . $_SESSION['standard']['userid'] . '\' and lokalpusat=\'1\' and status!=\'3\') and (selisih>0 or selisih is null)';
			}

			#exit(mysql_error());
			($qList = mysql_query($sList)) || true;
			$rList = mysql_fetch_assoc($qList);

			if ($rList['jmlhJob'] == '') {
				$rList['jmlhJob'] = 0;
			}

			echo '[' . $rOrg['kodeorganisasi'] . ' : <a href=\'#\' onclick="cek_pp_pt(\'' . $rOrg['kodeorganisasi'] . '\')">' . $rList['jmlhJob'] . '</a> ]';
		}

		break;

	case 'cek_pembuat_po':
		$user_id = $_SESSION['standard']['userid'];
		$skry = 'select purchaser from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

		#exit(mysql_error());
		($qkry = mysql_query($skry)) || true;
		$rkry = mysql_fetch_assoc($qkry);

		if ($rkry['purchaser'] != $user_id) {
			echo 'warning:Please See Your Username';
			exit();
		}

		break;

	case 'getSupplierNm':
		echo '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\n" . '                        <table cellpading=1 border=0 class=sortbale>' . "\n" . '                        <thead>' . "\n" . '                        <tr class=rowheader>' . "\n" . '                        <td>No.</td>' . "\n" . '                        <td>' . $_SESSION['lang']['kodesupplier'] . '</td>' . "\n" . '                        <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\n" . '                        </tr><tbody>' . "\n" . '                        ';
		$sSupplier = "select namasupplier,supplierid from $dbname.log_5supplier ".
			"where kodekelompok='S001' and namasupplier like '%" . $nmSupplier . "%'";

		#exit(mysql_error($conn));
		($qSupplier = mysql_query($sSupplier)) || true;

		while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
			$no += 1;
			echo '<tr class=rowcontent onclick=setData(\'' . $rSupplier['supplierid'] . '\')>' . "\n" . '                         <td>' . $no . '</td>' . "\n" . '                         <td>' . $rSupplier['supplierid'] . '</td>' . "\n" . '                         <td>' . $rSupplier['namasupplier'] . '</td>' . "\n" . '                    </tr>';
		}

		echo '</tbody></table></div>';
		break;
	}

?>
