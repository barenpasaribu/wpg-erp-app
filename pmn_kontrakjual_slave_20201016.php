<?php

 

//session_start();



	require_once 'master_validation.php';

	require_once 'config/connection.php';

	include_once 'lib/eagrolib.php';

	include_once 'lib/zLib.php';

	include_once 'lib/terbilang.php';



	$method = $_POST['method'];

	$kdBrg = $_POST['kdBrg'];

	$noKntrk = $_POST['noKntrk'];

	$custId = $_POST['custId'];

	$tlgKntrk = tanggalsystem($_POST['tlgKntrk']);

	$kdBrg = $_POST['kdBrg'];

	$satuan = $_POST['satuan'];

	$tBlg = $_POST['tBlg'];

	$grand_total = $_POST['total'];

	$qty = $_POST['qty'];

	$transporter = $_POST['transporter'];

	$tglKrm = tanggalsystem($_POST['tglKrm']);

	$tglSd = tanggalsystem($_POST['tglSd']);

	$kualitas = htmlspecialchars($_POST['kualitasxx']);

	$syrtByr = htmlspecialchars($_POST['syrtByr']);

	$syrtByr2 = htmlspecialchars($_POST['syrtByr2']);

	$tmbngn = htmlspecialchars($_POST['tmbngn']);

	$pnyrhn = $_POST['pnyrhn'];

	$cttn1 = htmlspecialchars($_POST['cttn1']);

	$cttn2 = htmlspecialchars($_POST['cttn2']);

	$cttn3 = htmlspecialchars($_POST['cttn3']);

	$cttn4 = htmlspecialchars($_POST['cttn4']);

	$cttn5 = htmlspecialchars($_POST['cttn5']);

	$cttn6 = htmlspecialchars($_POST['cttn6']);

	$cttn7 = htmlspecialchars($_POST['cttn7']);

	$cttn8 = htmlspecialchars($_POST['cttn8']);

	$cttn9 = htmlspecialchars($_POST['cttn9']);

	$cttn10 = htmlspecialchars($_POST['cttn10']);

	$cttn11 = htmlspecialchars($_POST['cttn11']);

	$cttn12 = htmlspecialchars($_POST['cttn12']);

	$cttn13 = htmlspecialchars($_POST['cttn13']);

	$cttn14 = htmlspecialchars($_POST['cttn14']);

	$cttn15 = htmlspecialchars($_POST['cttn15']);

	$HrgStn = $_POST['HrgStn'];

	$tndTng = $_POST['tndtng'];

	$tanda_tangan_pembeli = $_POST['tanda_tangan_pembeli'];

	$noDo = $_POST['noDo'];

	$othCttn = $_POST['othCttn'];

	$tlransi = $_POST['tlransi'];

	$kdPt = $_POST['kdPt'];

	$lokasiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);

	$txtSearch = $_POST['txtSearch'];

	$kurs = $_POST['kurs'];

	$ppn = $_POST['ppn'];

	$lamamuat = $_POST['lamamuat'];

	$tipemuat = $_POST['tipemuat'];

	$keterangan_muat = $_POST['keterangan_muat'];

	$pelabuhan = $_POST['pelabuhan'];

	$demurage = $_POST['demurage'];

	$status = $_POST['status'];



	switch ($method) {

		case 'cekDate':

			if ($tglSd < $tglKrm) {

				$a = 'a';

			} else {

				$a = 'b';

			}

			echo $a;

			break;

		case 'posting':

			$idip = 1;

			$sekarang = date('Y-m-d');

			$q = 'select * from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $noKntrk . '\'';

			#exit(mysql_error());

			$qq = mysql_query($q);

			$rqq = mysql_fetch_assoc($qq);

			$tlgKntrk = $rqq['tanggalkontrak'];

			$custId = $rqq['koderekanan'];

			$qty = $rqq['kuantitaskontrak'];

			$othCttn = $rqq['catatanlain'];

			$kdPt = $rqq['kodept'];

			$nodo = $rqq['nodo'];

			$kdbrg = $rqq['kodebarang'];

			$trpcode = $rqq['transporter'];

			$tujuan = $rqq['pelabuhan'];

			if ($kdPt == 'SSP') {

				$idip = 1;

			} else if ($kdPt == 'LSP') {

				$idip = 2;

			} else {

				$idip = 3;

			}

	//echo "warning: ".$q."/kdpt=".$kdPt."/idip=".$idip;

	//exit();

			$sLokasi = 'select * from ' . $dbname . '.setup_remotetimbangan where id = ' . $idip . '';

			#exit(mysql_error());

	//echo "warning: ".$sLokasi;

	//exit();

			$qLokasi = mysql_query($sLokasi);

			$rLokasi = mysql_fetch_assoc($qLokasi);

			$ipAdd = $rLokasi['ip'];

			$prt = $rLokasi['port'];

			$dbnm = $rLokasi['dbname'];

			$usrName = $rLokasi['username'];

			$pswrd = $rLokasi['password'];

			#exit('Error/Gagal :Unable to Connect to database : ' . $ipAdd);

			$corn = mysql_connect($ipAdd . ':' . $prt, $usrName, $pswrd);

			$sCob = 'select * from ' . $dbnm . '.mscontract where CTRNO = \'' . $noKntrk . '\'';

			#exit(mysql_error());

			$res = mysql_query($sCob, $corn);

			$row = mysql_num_rows($res);

	//echo "warning: ".$sCob." -baris=".$row." /ipadd=".$ipAdd.":".$prt." - un/pw=".$usrName."/".$pswrd;

	//exit();

			if ($row == 0) {

				$sIns = "INSERT INTO $dbnm.mscontract (CTRNO, CTRDATE, BUYERCODE, CTRQTY, DESCRIPTION, CTRSTATUS,USERID,

				CREATEDATE,MILLCODE) VALUES ('" . $noKntrk . "','" . $tlgKntrk . "','" . $custId . "','" . $qty . "','" . 

				$othCttn . "','Aktif','" . $_SESSION['standard']['userid'] . "',NOW(),'PDSM')";

				if (mysql_query($sIns, $corn)) {

					$sInsDO = "INSERT INTO $dbnm.mssipb (CTRNO, SIPBNO, SIPBDATE, PRODUCTCODE, TRPCODE, SIPBQTY,DESCRIPTION,SIPBSTATUS,

					USERID,CREATEDATE,TUJUAN,uploadStat) VALUES ('" . $noKntrk . "','" . $nodo . "','" . $tlgKntrk . "','" . 

					$kdbrg . "','" . $trpcode . "','" . $qty . "','".$othCttn."','Aktif','" . $_SESSION['standard']['userid'] . "',NOW(),'" . $tujuan . "','')";

	//echo "warning: ".$sInsDO;

	//exit();

					if (mysql_query($sInsDO, $corn)) {

						$i = 'update ' . $dbname . '.pmn_kontrakjual set posting=1,postingdate=\'' . $sekarang . '\',postingby=\'' . $_SESSION['standard']['userid'] . '\' where nokontrak=\'' . $noKntrk . '\'';

							if (mysql_query($i,$conn)) {

							} else {

								echo ' Gagal update pmn_kontrakjual,' . addslashes(mysql_error($conn));

								}

					} else {

						//echo ' Gagal mssipb,' . addslashes(mysql_error($conn));

						echo ' Gagal input SIPB, sql= ';

					}

				}

			}

			else {

				//echo ' Gagal mscontract,' . addslashes(mysql_error($conn));

				echo 'Gagal mscontract, sql= ' . $sIns;

			} 

			

			break;

		case 'LoadNew':

		$lokasi = $_SESSION['empl']['lokasitugas'];

			$limit = 10;

			$page = 0;

			if (isset($_POST['page'])) {

				$page = $_POST['page'];

				if ($page < 0) {

					$page = 0;

				}

			}

			$offset = $page * $limit;

			$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_kontrakjual where kodeorg=\'' . $lokasi . '\'  order by tanggalkontrak desc';

			#exit(mysql_error());

			($query2 = mysql_query($ql2)) || true;

			while ($jsl = mysql_fetch_object($query2)) {

				$jlhbrs = $jsl->jmlhrow;

			}

			$slvhc = 'select * from ' . $dbname . '.pmn_kontrakjual where kodeorg=\'' . $lokasi . '\'  order by tanggalkontrak desc limit ' . $offset . ',' . $limit . '';

			#exit(mysql_error());

			($qlvhc = mysql_query($slvhc)) || true;

			$user_online = $_SESSION['standard']['userid'];

			while ($res = mysql_fetch_assoc($qlvhc)) {

				$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer = \'' . $res['koderekanan'] . '\'';

				#exit(mysql_error());

				($qCUst = mysql_query($sCust)) || true;

				$rCust = mysql_fetch_assoc($qCUst);

				$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

				#exit(mysql_error());

				($qBrg = mysql_query($sBrg)) || true;

				$rBrg = mysql_fetch_assoc($qBrg);

				$sOrg = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $res['kodept'] . '\'';

				#exit(mysql_error());

				($qOrg = mysql_query($sOrg)) || true;

				$rOrg = mysql_fetch_assoc($qOrg);

				$no += 1;

				if ($res['posting'] == '0') {

					$isi = '<td>' . "" .

						'<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $res['nokontrak'] . '\');">' . "\r\n\t\t\t\t" .

						'<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . $res['nokontrak'] . '\');" >' . "\t" . '<br />' . "\r\n\t\t\t\t" .

						'<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_pdf\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/excel.jpg class=resicon  title=\'Kontrak Excel\' onclick="masterExcell(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_excel\',event)">' . "\r\n\t\t\t\t" .
						
						'<img src=images/pdf.jpg class=resicon  title=\'DO\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_do_pdf\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/excel.jpg class=resicon  title=\'DO\' onclick="masterExcell(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'sdm_slave_do\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/icons/04/10/01.png  title=\'Posting\' class=zImgBtn caption=\'Posting\' onclick="posting(\'' . $res['nokontrak'] . '\');">' . "\r\n\t\t\t" . '</td>';

				} else {

					$isi = '<td>' . "" .

						'<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_pdf\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/excel.jpg class=resicon  title=\'Kontrak Excel\' onclick="masterExcell(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_excel\',event)">' . "\r\n\t\t\t\t" .
												
						'<img src=images/icons/04/10/02.png class=zImgBtn>' . "\r\n" .

						'<img src=images/excel.jpg class=resicon  title=\'DO\' onclick="masterExcell(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'sdm_slave_do\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/posted.png class=zImgBtn title=\'No. Kontrak Telah Berhasil Di Upload Otomatis Ke Komputer Jembatan Timbang.\'>' . "\r\n\t\t\t" ;

					if ($res['status']=='Aktif') {

						$isi.= '<img src=images/lightbulb_on.png class=zImgBtn title=\'Non Aktifkan\' onclick="setStatus(\'' . $res['nokontrak'] . '\',\'Aktif\');">' . "\r\n\t\t\t" ;

					} else {

						$isi.= '<img src=images/lightbulb_off.png class=zImgBtn title=\'Aktifkan\' onclick="setStatus(\'' . $res['nokontrak'] . '\',\'Tidak Aktif\');">' . "\r\n\t\t\t" ;

					}

					$isi.='</td>';	

				}

				echo "" . '            

			<tr class=rowcontent>' . "\r\n" . '            

			<td>' . $no . '</td>' . "\r\n" . '            

			<td>' . $res['nokontrak'] . '</td>' . "\r\n" . '            

			<td>' . $rOrg['namaorganisasi'] . '</td>' . "\r\n" . '            

			<td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '            

			<td>' . tanggalnormal($res['tanggalkontrak']) . '</td>' . "\r\n" . '            

			<td>' . $res['kodebarang'] . '</td>' . "\r\n" . '            

			<td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '            

			<td>' . tanggalnormal($res['tanggalkirim']) . '</td>';

				echo $isi;

				echo '</tr>';

			}

			echo "\r\n" . '    <tr class=rowheader><td colspan=9 align=center>' . "\r\n" . '    ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '    <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '    <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '    </td>' . "\r\n" . '    </tr>';

			break;

		case 'getSatuan':

			$sSat2 = 'SELECT distinct satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $kdBrg . '\'';

			#exit(mysql_error());

			($qSat2 = mysql_query($sSat2)) || true;

			$rsat2 = mysql_fetch_assoc($qSat2);

			$sSat = 'SELECT distinct a.satuan,b.satuankonversi from ' . $dbname . '.log_5masterbarang a inner join ' . $dbname . '.log_5stkonversi b on a.satuan=b.satuankonversi where a.kodebarang=\'' . $kdBrg . '\' ';

			($qSat = mysql_query($sSat)) || true;

			$optSatuan .= '<option value=' . $rsat2['satuan'] . '  ' . ($rsat2['satuan'] == $satuan ? 'selected' : '') . '>' . $rsat2['satuan'] . '</option>';

			while ($rSat = mysql_fetch_assoc($qSat)) {

				$optSatuan .= '<option value=' . $rSat['satuankonversi'] . ' ' . ($rSat['satuankonversi'] == $satuan ? 'selected' : '') . '>' . $rSat['satuankonversi'] . '</option>';

			}

			echo $optSatuan;

			break;

		case 'getLastData':
			if($_POST['custId']!=''){
				$where= " where koderekanan like '".$_POST['custId']."'";
			}
			$sql = 'select * from ' . $dbname . '.pmn_kontrakjual '.$where.' order by tanggalkontrak desc limit 0,1';
//saveLog($sql);
			#exit(mysql_error());

			($query = mysql_query($sql)) || true;

			$res = mysql_fetch_assoc($query);

			echo $res['nokontrak'] . '###' . $res['koderekanan'] . '###' . tanggalnormal($res['tanggalkontrak']) . '###' . $res['kodebarang'] . '###' . $res['hargasatuan'] . '###' . $res['terbilang'] . '###' . $res['kuantitaskontrak'] . '###' . tanggalnormal($res['tanggalkirim']) . '###' . tanggalnormal($res['sdtanggal']) . '###' . $res['toleransi'] . '###' . $res['nodo'] . '###' . $res['kualitas'] . '###' . $res['syratpembayaran'] . '###' . $res['penandatangan'] . '###' . $res['standartimbangan'] . '###' . $res['catatan1'] . '###' . $res['catatan2'] . '###' . $res['catatan3'] . '###' . $res['catatan4'] . '###' . $res['catatan5'] . '###' . $res['catatanlain'] . '###' . $res['satuan'] . '###' . $res['kodept'] . '###' . $res['syratpembayaran2'] . '###' . $res['grand_total'] . '###' . $res['catatan6'] . '###' . $res['catatan7'] . '###' . $res['catatan8'] . '###' . $res['catatan9'] . '###' . $res['catatan11'] . '###' . $res['catatan12'] . '###' . $res['catatan13'] . '###' . $res['catatan14'] . '###' . $res['catatan15']. '###' . $res['tanda_tangan_pembeli'] ;

			break;

		case 'getEditData':

			$sql = 'select * from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $noKntrk . '\'';

			$query = mysql_query($sql);

			$res = mysql_fetch_assoc($query);

			echo $res['nokontrak'] . '###' .

				$res['koderekanan'] . '###' .

				tanggalnormal($res['tanggalkontrak']) . '###' .

				$res['kodebarang'] . '###' .

				$res['hargasatuan'] . '###' .

				$res['terbilang'] . '###' .

				$res['kuantitaskontrak'] . '###' .

				tanggalnormal($res['tanggalkirim']) . '###' .

				tanggalnormal($res['sdtanggal']) . '###' .

				$res['toleransi'] . '###' .

				$res['nodo'] . '###' .

				str_replace("dan", "&", $res['kualitas']) . '###' .

				str_replace("dan", "&", $res['syratpembayaran']) . '###' .

				$res['penandatangan'] . '###' .

				str_replace("dan", "&", $res['standartimbangan']) . '###' .

				$res['catatan1'] . '###' .

				$res['transporter'] . '###' .

				str_replace("dan", "&", $res['catatan3']) . '###' .

				str_replace("dan", "&", $res['catatan4']) . '###' .

				str_replace("dan", "&", $res['catatan5']) . '###' .

				$res['catatanlain'] . '###' .

				$res['satuan'] . '###' .

				$res['kodept'] . '###' .

				$res['matauang'] . '###' .

				$res['ppn'] . '###' .

				$res['lamamuat'] . '###' .

				$res['pelabuhan'] . '###' .

				$res['demurage'] . '###' .

				str_replace("dan", "&", $res['syratpembayaran2']) . '###' .

				$res['grand_total'] . '###' .

				str_replace("dan", "&", $res['catatan6']) . '###' .

				str_replace("dan", "&", $res['catatan7']) . '###' .

				str_replace("dan", "&", $res['catatan8']) . '###' .

				str_replace("dan", "&", $res['catatan9']) . '###' .

				str_replace("dan", "&", $res['catatan10']) . '###' .

				str_replace("dan", "&", $res['catatan11']) . '###' .

				str_replace("dan", "&", $res['catatan12']) . '###' .

				str_replace("dan", "&", $res['catatan13']) . '###' .

				str_replace("dan", "&", $res['catatan14']) . '###' .

				str_replace("dan", "&", $res['catatan15']) . '###' .

				$res['tipemuat'] . '###' . $res['keterangan_muat'] . '###' . $res['tanda_tangan_pembeli'];

			break;

		case 'insert':

			if ($noKntrk == '') {

				exit('Error:No. Contract empty');

			}

			if ($transporter == '') {

				exit('Error:Transporter empty');

			}

			if ($custId == '') {

				exit('Error: Customer empty');

			}

			if ($kdBrg == '') {

				exit('Error: Item empty');

			}

			if ($HrgStn == '') {

				exit('Error: Price empty');

			}

			if ($qty == '') {

				exit('Error: Qty empty');

			}

			if ($tlgKntrk == '') {

				exit('Error: Contract Date empty');

			}

			if ($satuan == '') {

				exit('Error: UOM empty');

			}

			if ($kualitas == '') {

				exit('Error: Quality empty');

			}

			if ($tglKrm == '') {

				exit('Error: Delivery Date empty');

			}

			if ($tglSd < $tglKrm) {

				exit('Error:Delivery Date not valid');

			}

			$sCek = 'select nokontrak from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $noKntrk . '\'';

			#exit(mysql_error());

			($qCek = mysql_query($sCek)) || true;

			$rCek = mysql_num_rows($qCek);

			if ($rCek < 1) {

				$sCust = 'select kontakperson from ' . $dbname . '.pmn_4customer where kodecustomer = \'' . $custId . '\'';

				#exit(mysql_error());

				$qCUst = mysql_query($sCust);

				$rCust = mysql_fetch_assoc($qCUst);

				$sIns = 'insert into ' . $dbname . '.pmn_kontrakjual 

							(nokontrak, tanggalkontrak, koderekanan, kodebarang, satuan, hargasatuan, 

							terbilang, grand_total, kualitas, tanggalkirim, sdtanggal, 
							syratpembayaran,syratpembayaran2, 

							catatan1, catatan2, catatan3, catatan4, catatan5, 

							standartimbangan, penandatangan, tanda_tangan_pembeli, penandatangan2, 

							catatanlain,  kuantitaskontrak,toleransi,nodo,

							kodeorg,kodept,matauang,ppn,lamamuat,

							tipemuat, keterangan_muat, pelabuhan, demurage, catatan6, catatan7, 

							catatan8, catatan9, catatan10, 

							catatan11, catatan12, catatan13, 

							catatan14, catatan15,transporter) 

						values 

							(\'' . $noKntrk . '\',\'' . $tlgKntrk . '\',\'' . $custId . '\',\'' . $kdBrg . '\',\'' . $satuan . '\',

							\'' . $HrgStn . '\',\'' . $tBlg . '\',\'' . $grand_total . '\',\'' . $kualitas . '\',

							\'' . $tglKrm . '\',\'' . $tglSd . '\',\'' . $syrtByr . '\',\'' . $syrtByr2 . '\',

							\'' . addslashes($cttn1) . '\',\'' . $cttn2 . '\',\'' . $cttn3 . '\',\'' . $cttn4 . '\',\'' . $cttn5 . '\',

							\'' . $tmbngn . '\',\'' . $tndTng . '\',\'' . $tanda_tangan_pembeli . '\',\'' . $rCust['kontakperson'] . '\',\'' . $othCttn . '\',

							\'' . $qty . '\',\'' . $tlransi . '\',\'' . $noDo . '\',\'' . $lokasiTugas . '\',\'' . $kdPt . '\',

							\'' . $kurs . '\',\'' . $ppn . '\',\'' . $lamamuat . '\',\'' . $tipemuat . '\',\'' . $keterangan_muat . '\',\'' . $pelabuhan . '\',

							\'' . $demurage . '\',\'' . $cttn6 . '\',\'' . $cttn7 . '\',\'' . $cttn8 . '\',

							\'' . $cttn9 . '\',\'' . $cttn10 . '\',\'' . $cttn11 . '\',\'' . $cttn12 . '\',

							\'' . $cttn13 . '\',\'' . $cttn14 . '\',\'' . $cttn15 . '\',\''.$transporter.'\')';

				//echo "Warning : ".$sIns; exit();

				if (mysql_query($sIns)) {

					echo '';

				} else {

					echo 'DB Error : ' . mysql_error($conn);

				}

			} else {

				echo 'warning: Contract already exist';

				exit();

			}

			break;

		case 'update':

			if ($noKntrk == '') {

				exit('Error:No. Contract empty');

			}

			if ($custId == '') {

				exit('Error: Customer empty');

			}

			if ($kdBrg == '') {

				exit('Error: Item empty');

			}

			if ($HrgStn == '') {

				exit('Error: Price empty');

			}

			if ($qty == '') {

				exit('Error: Qty empty');

			}

			if ($tlgKntrk == '') {

				exit('Error: Contract Date empty');

			}

			if ($satuan == '') {

				exit('Error: UOM empty');

			}

			if ($kualitas == '') {

				exit('Error: Quality empty');

			}

			if ($tglKrm == '') {

				exit('Error: Delivery Date empty');

			}

			if ($tglSd < $tglKrm) {

				exit('Error:Delivery Date not valid');

			}

			$sCust = 'select kontakperson from ' . $dbname . '.pmn_4customer where kodecustomer = \'' . $custId . '\'';

			#exit(mysql_error());

			($qCUst = mysql_query($sCust)) || true;

			$rCust = mysql_fetch_assoc($qCUst);

			$sUpd = '	update ' . $dbname . '.pmn_kontrakjual 

						set  

							tanggalkontrak=\'' . tanggalsystem($_POST['tlgKntrk']) . '\', 

							koderekanan=\'' . $custId . '\', 

							kodebarang=\'' . $kdBrg . '\', 

							satuan=\'' . $satuan . '\', 

							hargasatuan=\'' . $HrgStn . '\', 

							terbilang=\'' . $tBlg . '\',

							grand_total=\'' . $grand_total . '\', 

							kualitas=\'' . $kualitas . '\',

							tanggalkirim=\'' . tanggalsystem($_POST['tglKrm']) . '\', 

							sdtanggal=\'' . tanggalsystem($_POST['tglSd']) . '\',

							syratpembayaran=\'' . $syrtByr . '\',

							syratpembayaran2=\'' . $syrtByr2 . '\', 

							catatan1=\'' . addslashes($cttn1) . '\', 

							catatan2=\'' . $cttn2 . '\', 

							catatan3=\'' . $cttn3 . '\',

							catatan4=\'' . $cttn4 . '\', 

							catatan5=\'' . $cttn5 . '\',

							catatan6=\'' . $cttn6 . '\',

							catatan7=\'' . $cttn7 . '\', 

							catatan8=\'' . $cttn8 . '\', 

							catatan9=\'' . $cttn9 . '\', 

							catatan11=\'' . $cttn11 . '\',

							catatan12=\'' . $cttn12 . '\',

							catatan13=\'' . $cttn13 . '\', 

							catatan14=\'' . $cttn14 . '\', 

							catatan15=\'' . $cttn15 . '\', 

							standartimbangan=\'' . $tmbngn . '\',

							penandatangan=\'' . $tndTng . '\',

							tanda_tangan_pembeli=\'' . $tanda_tangan_pembeli . '\',

							penandatangan2=\'' . $rCust['kontakperson'] . '\', 

							catatanlain=\'' . $othCttn . '\',

							kuantitaskontrak=\'' . $qty . '\',

							toleransi=\'' . $tlransi . '\',

							nodo=\'' . $noDo . '\',

							kodeorg=\'' . $lokasiTugas . '\',

							kodept=\'' . $kdPt . '\',

							matauang=\'' . $kurs . '\',

							ppn=\'' . $ppn . '\',

							lamamuat=\'' . $lamamuat . '\',

							tipemuat=\'' . $tipemuat . '\',

							keterangan_muat=\'' . $keterangan_muat . '\',

							pelabuhan=\'' . $pelabuhan . '\',

							demurage=\'' . $demurage . '\',

							transporter=\'' . $transporter . '\' 

						where 

							nokontrak=\'' . $noKntrk . '\'';

			if (mysql_query($sUpd)) {

				echo '';

			} else {

				echo 'DB Error : ' . mysql_error($conn);

			}

			break;

		case 'getCust':

			$sCust = 'select kontakperson,telepon  from ' . $dbname . '.pmn_4customer where kodecustomer = \'' . $custId . '\'';

			#exit(mysql_error());

			($qCUst = mysql_query($sCust)) || true;

			$rCust = mysql_fetch_assoc($qCUst);

			echo $rCust['kontakperson'] . '###' . $rCust['telepon'];

			break;

		case 'dataDel':

			$sDel = 'delete from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $noKntrk . '\'';

			if (mysql_query($sDel)) {

				echo '';

			} else {

				echo 'DB Error : ' . mysql_error($conn);

			}

			break;

		case 'cariNokntrk':

			if ($txtSearch != '') {

				$where = ' where nokontrak like \'%' . $txtSearch . '%\'';

			} else {

				$where = '';

			}

			$sCek = 'select * from ' . $dbname . '.pmn_kontrakjual  ' . $where . ' order by tanggalkontrak desc';

			#exit(mysql_error());

			($qCek = mysql_query($sCek)) || true;

			$rCek = mysql_num_rows($qCek);

			if (0 < $rCek) {

				$limit = 10;

				$page = 0;

				if (isset($_POST['page'])) {

					$page = $_POST['page'];

					if ($page < 0) {

						$page = 0;

					}

				}

				$offset = $page * $limit;

				$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_kontrakjual ' . $where . ' ';

				#exit(mysql_error());

				($query2 = mysql_query($ql2)) || true;

				while ($jsl = mysql_fetch_object($query2)) {

					$jlhbrs = $jsl->jmlhrow;

				}

				$slvhc = 'select * from ' . $dbname . '.pmn_kontrakjual ' . $where . ' order by tanggalkontrak desc limit ' . $offset . ',' . $limit . ' ';

				#exit(mysql_error());

				($qlvhc = mysql_query($slvhc)) || true;

				$user_online = $_SESSION['standard']['userid'];

				while ($res = mysql_fetch_assoc($qlvhc)) {

					$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer = \'' . $res['koderekanan'] . '\'';

					#exit(mysql_error());

					($qCUst = mysql_query($sCust)) || true;

					$rCust = mysql_fetch_assoc($qCUst);

					$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

					#exit(mysql_error());

					($qBrg = mysql_query($sBrg)) || true;

					$rBrg = mysql_fetch_assoc($qBrg);

					$sOrg = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $res['kodept'] . '\'';

					#exit(mysql_error());

					($qOrg = mysql_query($sOrg)) || true;

					$rOrg = mysql_fetch_assoc($qOrg);

					$no += 1;

					if ($res['posting'] == '0') {

						$isi = '<td>' . "\r\n\t\t\t\t\t\t" . '<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $res['nokontrak'] . '\');">' . "\r\n\t\t\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . $res['nokontrak'] . '\');" >' . "\t" . '<br />' . "\r\n\t\t\t\t\t\t" . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_pdf\',event)">' . "\r\n\t\t\t\t\t\t" . '<img src=images/icons/04/10/01.png  title=\'Posting\' class=zImgBtn caption=\'Posting\' onclick="posting(\'' . $res['nokontrak'] . '\');">' . "\r\n\t\t\t\t\t" . '</td>';

					} else {

						// $isi = '<td>' . "\r\n\t\t\t\t\t\t" . '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_pdf\',event)">' . "\r\n\t\t\t\t\t\t" . '<img src=images/icons/04/10/02.png class=zImgBtn>' . "\r\n\t\t\t\t\t" . '</td>';

						$isi = '<td>' . "" .

						'<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'pmn_kontakjual_pdf\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/icons/04/10/02.png class=zImgBtn>' . "\r\n" .

						'<img src=images/excel.jpg class=resicon  title=\'DO\' onclick="masterExcell(\'pmn_kontrakjual\',\'' . $res['nokontrak'] . '\',\'\',\'sdm_slave_do\',event)">' . "\r\n\t\t\t\t" .

						'<img src=images/posted.png class=zImgBtn title=\'No. Kontrak Telah Berhasil Di Upload Otomatis Ke Komputer Jembatan Timbang.\'>' . "\r\n\t\t\t" ;

						if ($res['status']=='Aktif') {

							$isi.= '<img src=images/lightbulb_on.png class=zImgBtn title=\'Aktif\' onclick="setStatus(\'' . $res['nokontrak'] . '\',\'Aktif\');">' . "\r\n\t\t\t" ;

						} else {

							$isi.= '<img src=images/lightbulb_off.png class=zImgBtn title=\'Tidak Aktif\' onclick="setStatus(\'' . $res['nokontrak'] . '\',\'Tidak Aktif\');">' . "\r\n\t\t\t" ;

						}

						$isi.='</td>';	

					}

					echo "\r\n" . '                    <tr class=rowcontent>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td>' . $res['nokontrak'] . '</td>' . "\r\n" . '                    <td>' . $rOrg['namaorganisasi'] . '</td>' . "\r\n" . '                    <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                    <td>' . tanggalnormal($res['tanggalkontrak']) . '</td>' . "\r\n" . '                    <td>' . $res['kodebarang'] . '</td>' . "\r\n" . '                    <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                    <td>' . $res['tanggalkirim'] . '</td>';

					echo $isi;

					echo '</tr>';

				}

				echo "\r\n" . '            <tr class=rowheader><td colspan=9 align=center>' . "\r\n" . '            ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '            <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '            <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '            </td>' . "\r\n" . '            </tr>';

			} else {

				echo '<tr class=rowheader><td colspan=9 align=center>Not Found</td></tr>';

			}

			break;

		case 'getNoCtr':

//			if (isset($_POST['kdBrg'])) {

				$kdBrg = trim($_POST['kdBrg']);

				$kdPt = trim($_POST['kdPt']);

				$tgl = tanggalsystem($_POST['tlgKntrk']);

				

				$bln = 'XX'; 

				$nbln = substr($tgl, 4, 2);

				switch ($nbln) {

					case 1:

						$bln = 'I';

						break;

					case 2:

						$bln = 'II';

						break;

					case 3:

						$bln = 'III';

						break;

					case 4:

						$bln = 'IV';

						break;

					case 5:

						$bln = 'V';

						break;

					case 6:

						$bln = 'VI';

						break;

					case 7:

						$bln = 'VII';

						break;

					case 8:

						$bln = 'VIII';

						break;

					case 9:

						$bln = 'IX';

						break;

					case 10:

						$bln = 'X';

						break;

					case 11:

						$bln = 'XI';

						break;

					case 12:

						$bln = 'XII';

						break;

				}

				$thn = substr($tgl, 0, 4);



				if (empty($kdBrg)){

					$nmbrg = 'XXX';

				}

				

				if ($kdBrg == '40000001') {

					$nmbrg = 'CPO';

				} else if ($kdBrg == '40000002') {

					$nmbrg = 'PK.';

				} else if ($kdBrg == '40000003') {

					$nmbrg = 'TBS';

				} else if ($kdBrg == '40000004') {

					$nmbrg = 'CGG';

				} else if ($kdBrg == '40000005') { //fiber

					$nmbrg = 'FIB';

				} else if ($kdBrg == '40000006') { //abu janjangan

					$nmbrg = 'ABU';

				} else{

					$nmbrg = 'XXX';

				}



	/*

				if ($kdPt == 'SSP') {

					$noctr = '/KJB/SSP-'; // 001/KJB/MPS-IBP/VIII/2019

					$nodo = '/SSP/DO/' . $nmbrg . '/';

				} else if ($kdPt == 'MJR') {

					$noctr = '/KJB/MJR-';

					$nodo = '/MJR/DO/' . $nmbrg . '/';

				} else if ($kdPt == 'MPS') {

					$noctr = '/KJB/MPS-';

					$nodo = '/MPS/DO/' . $nmbrg . '/';

				} else if ($kdPt == 'HSS') {

					$noctr = '/KJB/HSS-';

					$nodo = '/HSS/DO/' . $nmbrg . '/';

				} else {

	*/				$noctr = '/KJB/'.strtoupper($kdPt).'-';

					$nodo = strtoupper($kdPt).'/DO/' . $nmbrg . '/'.$bln. "/" . $thn;

	//			}

				//kontrak

				

				// RUNNING NUMBER KONTRAK

				$where2 = "/" . $thn;

				$query = "SELECT nokontrak FROM pmn_kontrakjual WHERE nokontrak like '%".$where2."%' AND kodept = '".$kdPt."'";
//saveLog($query);
				// print_r($query);

				// die();

				$queryAct = mysql_query($query);

				$numberRunning = 0;

				while($data = mysql_fetch_object($queryAct)){

					$explodeDO2 = explode("/", $data->nokontrak);

					$int = $explodeDO2[0];

					if(is_numeric($int)){

						if($explodeDO2[0] > $numberRunning){

							$numberRunning = $explodeDO2[0];

						}

					}

				}

				$numberRunning += 1;

				$numberRunning = addZero($numberRunning, 3);

				$counter = $numberRunning;

				// RUNNING NUMBER KONTRAK
	

				// RUNNING NUMBER DO

				$where1 = "/" . $nodo;

				$where2 = "/" . $thn;

				$query = "SELECT nodo FROM pmn_kontrakjual WHERE nodo like '%".$where1."%' and nodo like '%".$where2."%'";
//saveLog($query);
				$queryAct = mysql_query($query);

				

				$numberRunning = 0;

				while($data = mysql_fetch_object($queryAct)){

					$explodeDO2 = explode("/", $data->nodo);

					$int = $explodeDO2[0];

					if(is_numeric($int)){

						if($explodeDO2[0] > $numberRunning){

							$numberRunning = $explodeDO2[0];

						}

					}

				}

				$numberRunning += 1;

				$numberRunning = addZero($numberRunning, 3);

				$counter_do = $numberRunning;

				// RUNNING NUMBER DO



	/*

				if ($kdPt == 'SSP') {

					$noctr = $counter . '/KJB/SSP-XXX/' . $bln . '/' . $thn;

					$nodo = $counter_do .'/SSP/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

				} else if ($kdPt == 'MJR') {

					$noctr = $counter . '/KJB/MJR-XXX/' . $bln . '/' . $thn;

					$nodo = $counter_do .'/MJR/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

				} else if ($kdPt == 'HSS') {

					$noctr = $counter . '/KJB/HSS-XXX/' . $bln . '/' . $thn;

					$nodo = $counter_do .'/HSS/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

				} else if ($kdPt == 'MPS') {

					$noctr = $counter . '/KJB/MPS-XXX/' . $bln . '/' . $thn;

					$nodo = $counter_do .'/MPS/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

				} else {

	*/				

					$nodo = $counter_do .'/'.strtoupper($kdPt).'/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

					if($_POST['noKtrk']!=''){
						$nmbrg=substr($_POST['noKtrk'],0,15);
						$noctr =$nmbrg . '/'. $bln . '/' . $thn;
					}else{
						$noctr = $counter . '/KJB/'.strtoupper($kdPt).'-'. $nmbrg . '/'. $bln . '/' . $thn;
					}

	//			}

				

				// script baru

				// $noctr = $counter . '/KJB/'.substr($_SESSION['empl']['lokasitugas'], 0, 3).'-XXX/' . $bln . '/' . $thn;

				// $nodo = $counter_do .'/'.substr($_SESSION['empl']['lokasitugas'], 0, 3).'/DO/' . $nmbrg . '/'. $bln . '/' . $thn;

				// script baru

				echo $noctr . '###' . $nodo;

//			}

			break;

		case 'getNoDO':

			// menerima data POST

			$noDO = $_POST['noDo'];

			$noDO = trim($noDO);

			$explodeDO = explode("/", $noDO);



			$kodeBarang = $_POST['kdBrg'];



			// menentukan kode barang

			$inisialKode = '';

			if ($kodeBarang == '40000001') {

				$inisialKode = 'CPO';

			} else if ($kodeBarang == '40000002') {

				$inisialKode = 'PK';

			} else if ($kodeBarang == '40000003') {

				$inisialKode = 'TBS';

			} else if ($kodeBarang == '40000004') {

				$inisialKode = 'CGG';

			} else if ($kodeBarang == '40000005') {

				$inisialKode = 'FIB';

			} else if ($kodeBarang == '40000006') {

				$inisialKode = 'ABU';

			}else{

				$inisialKode = 'XXX';

			}



			// menentukan running number DO

			$where1 = "/" . $inisialKode."/";

			$where2 = "/" . $explodeDO[1]."/".$explodeDO[2]."/".$inisialKode."/".$explodeDO[4]."/".$explodeDO[5];

			$query = "SELECT nodo FROM pmn_kontrakjual WHERE nodo like '%".$where1."%' and nodo like '%".$where2."%'";

			$queryAct = mysql_query($query);

			

			$numberRunning = 0;

			while($data = mysql_fetch_object($queryAct)){

				$explodeDO2 = explode("/", $data->nodo);

				$int = $explodeDO2[0];

				if(is_numeric($int)){

					if($explodeDO2[0] > $numberRunning){

						$numberRunning = $explodeDO2[0];

					}

				}

			}

			$numberRunning += 1;

			$numberRunning = addZero($numberRunning, 3);

			// menentukan running number DO



			echo $numberRunning."/".$explodeDO[1]."/".$explodeDO[2]."/".$inisialKode."/".$explodeDO[4]."/".$explodeDO[5];

			break;

		case 'setStatus':

			$sLokasi = "select * from $dbname.setup_remotetimbangan where id = 1";

			#exit(mysql_error());

			$qLokasi = mysql_query($sLokasi);

			$rLokasi = mysql_fetch_assoc($qLokasi);

			$ipAdd = $rLokasi['ip'];

			$prt = $rLokasi['port'];

			$dbnm = $rLokasi['dbname'];

			$usrName = $rLokasi['username'];

			$pswrd = $rLokasi['password'];

			#exit('Error/Gagal :Unable to Connect to database : ' . $ipAdd);

			$corn = mysql_connect($ipAdd . ':' . $prt, $usrName, $pswrd);

			#$sCob = 'select * from ' . $dbnm . '.mscontract where CTRNO = \'' . $noKntrk . '\'';

			#exit(mysql_error());

			$status__ = ($status=='Aktif'?'Tidak Aktif':'Aktif');

			$update = "update $dbnm.mscontract set CTRSTATUS='$status__' where CTRNO = '$noKntrk'";

			$res = mysql_query($update, $corn);

			if ($res){



			}

			else {

				echo ' Gagal,' . addslashes(mysql_error($corn)).'<br/>'.

				'Query : '. $update;

			} 

			unset($corn);

			$update = "update $dbname.pmn_kontrakjual set status='$status__' where nokontrak='$noKntrk'";

			if (mysql_query($update,$conn)) {

			} else {

				echo ' Gagal,' . addslashes(mysql_error($conn)).'<br/>'.

				'Query : '. $update;

			}

			break;

		case 'perkalian':

			$nilai = $_POST['nilai'];

			echo terbilang($nilai, 3);

			break;

	}

?>