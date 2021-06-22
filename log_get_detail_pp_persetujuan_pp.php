<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
echo "\r\n";
$method = $_POST['method'];
$kolom = $_POST['kolom'];

switch ($method) {
	case 'get_form_approval':
		$sql = "select * from  $dbname.log_prapoht where nopp='" . $_POST['nopp'] . "'";

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$rest = mysql_fetch_assoc($query);
		$i = checkLastApprovalIndexPP($rest);
		//echoMessage('i= ',$i);
		$PP_MAX_APPROVAL=$rest['jumlahpemberipersetujuan'];
		//echoMessage('jumlahpemberipersetujuan= ',$PP_MAX_APPROVAL);  

//		while ($i <= PP_MAX_APPROVAL) {
			if ($_SESSION['standard']['userid'] == $rest['persetujuan' . $i]) {
				if ($rest["persetujuan$PP_MAX_APPROVAL"] != '') { 
					echo "<br/><div id=approve> <fieldset> <legend>
					<input type=text readonly=readonly name=rnopp id=rnopp value=" . $_POST['nopp'] . "></legend> 
					<table cellspacing=1 border=0> <tr> <td colspan=3> Submit to Purchasing Dept.</td></tr> 
					<tr> <td>" . $_SESSION['lang']['note'] . "</td> <td>:</td> <td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event);\" /></td> </tr> 
					<tr><td colspan=3 align=center> <button class=mybutton onclick=close_pp() >" . $_SESSION['lang']['ok'] . "</button></td></tr>
					</table> </fieldset> </div>";

				}
//				else if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
//					echo "<div id=approve style=display:block> <fieldset>
//					<legend><input type=text readonly=readonly name=rnopp id=rnopp value=" . $_POST['nopp'] . " /></legend>
//					<table cellspacing=1 border=0> <tr> <td colspan=3>  Approve and submit directly to Purchasing Dept.</td></tr>
//					<tr> <td>". $_SESSION['lang']['note'] . "</td> <td>:</td> <td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event);\" style='width:150px;' /></td> </tr>
//					<tr><td colspan=3 align=center> <button class=mybutton onclick=close_pp() title='You are agree to this PR and submit it to Purchasing Dept. '  >" . $_SESSION['lang']['kePurchaser'] . "</button>
//					<button class=mybutton onclick=cancel_pp() title='Close this form'>" . $_SESSION['lang']['cancel'] . "</button></td></tr>
//					</table> </fieldset> </div>";
//
//				}
				else {
					echo "<br/> <div id=test style=display:block> <fieldset> 
					<legend><input type=text readonly=readonly name=rnopp id=rnopp value=" . $_POST["nopp"] . "  /></legend> 
					<table cellspacing=1 border=0> 
					<tr> <td colspan=3>  Submit to the next approval :</td> </tr> 
					<tr><td>" . $_SESSION["lang"]["namakaryawan"] . "</td> <td>:</td> <td valign=top>";
					$kd = substr($_POST['nopp'], 17, 2);
					$unit = substr($_POST['nopp'], 15, 4);
					$optPur = '';
					$str = "select distinct a.karyawanid,b.namakaryawan,b.lokasitugas ".
						"from $dbname.setup_approval a ".
						"left join $dbname.datakaryawan b on a.karyawanid=b.karyawanid ".
						"where a.karyawanid!='" . $_SESSION['standard']['userid'] . "' ".
						"and a.applikasi='PP".($i+1)."' ";//and a.kodeunit like '%HO'  ".
//					if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
//						$str.=" and a.kodeunit in (SELECT " .
//							"o.kodeorganisasi " .
//							"FROM  organisasi o    " .
//							"WHERE o.induk = '" . $_SESSION['empl']['kodeorganisasi'] . "')";
//					} else {
						$str.="and a.kodeunit='" . $unit . "' ";
//					}
					$str.= " order by b.lokasitugas,b.namakaryawan asc";
					//echoMessage(' aaa ',$str);
					#exit(mysql_error($conn));
					($qry = mysql_query($str)) || true;

					while ($rkry = mysql_fetch_assoc($qry)) {
						$optPur .= '<option value=\'' . $rkry['karyawanid'] . '\'>' . '[' . $rkry['lokasitugas']  . '] '.$rkry['namakaryawan'] .'</option>';
					}

					echo "\r\n\r\n" . '                                                <select id=user_id name=user_id  style="width:150px;">' . "\r\n" . '                                                        ' . $optPur . ' ' . "\r\n" . '                                                </select></td></tr>' . "\r\n" . '                                                <tr>' . "\r\n" . '                                                <tr>' . "\r\n" . '                                                <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '                                                <td>:</td>' . "\r\n" . '                                                <td><input type=text id=comment_fr name=comment_fr class=myinputtext onClick=\'return tanpa_kutip(event)\'  style="width:150px;" /></td>' . "\r\n" . '                                                </tr>' . "\r\n" . '                                                <td colspan=3 align=center>' . "\r\n" . '                                                <button class=mybutton onclick=forward_pp() title=" Submit to the next level" id=Ajukan >' . $_SESSION['lang']['diajukan'] . '</button>' . "\r\n\r\n" . '                                                <button class=mybutton onclick=cancel_pp() title=" Close this form ">' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '                                                </td></tr></table><br /> ' . "\r\n" . '                                                <input type=hidden name=method id=method  /> ' . "\r\n" . '                                                <input type=hidden name=user_id id=user_id value=' . $_SESSION['standard']['userid'] . ' />' . "\r\n" . '                                                <input type=hidden name=nopp id=nopp value=' . $_POST['nopp'] . '  /> ' . "\r\n" . '                                                </fieldset></div><br />' . "\r\n" . '<br />';

					if ($_SESSION['empl']['tipelokasitugas'] != 'HOLDING') {
						if ($rest['hasilpersetujuan1'] != '0') {
							echo '<div id=approve style=display:block>' . "\r\n" . '                                                            <fieldset>' . "\r\n" . '                                                            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=' . $_POST['nopp'] . '  /></legend>' . "\r\n" . '                                                            <table cellspacing=1 border=0>' . "\r\n" . '                                                            <tr>' . "\r\n" . '                                                            <td colspan=3>' . "\r\n" . '                                                             Approve and submit directly to Purchasing Dept.</td></tr>' . "\r\n" . '                                                            <tr>' . "\r\n" . '                                                            <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '                                                            <td>:</td>' . "\r\n" . '                                                            <td><input type=text id=note name=note class=myinputtext onClick="return tanpa_kutip(event)" style="width:150px;" /></td>' . "\r\n" . '                                                            </tr>' . "\r\n" . '                                                            <tr><td colspan=3 align=center>' . "\r\n" . '                                                            <button class=mybutton onclick=close_pp() title="You are agree to this PR and submit it to Purchasing Dept. "  >' . $_SESSION['lang']['kePurchaser'] . '</button><button class=mybutton onclick=cancel_pp() title="Close this form">' . $_SESSION['lang']['cancel'] . '</button></td></tr></table>' . "\r\n" . '                                                            </fieldset>' . "\r\n" . '                                                            </div>' . "\r\n" . '                                                            ';
						}
					}
					else {
						echo '<div id=approve style=display:block>' . "\r\n" . '                                                            <fieldset>' . "\r\n" . '                                                            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=' . $_POST['nopp'] . '  /></legend>' . "\r\n" . '                                                            <table cellspacing=1 border=0>' . "\r\n" . '                                                            <tr>' . "\r\n" . '                                                            <td colspan=3>' . "\r\n" . '                                                             Approve and submit directly to Purchasing Dept.</td></tr>' . "\r\n" . '                                                            <tr>' . "\r\n" . '                                                            <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '                                                            <td>:</td>' . "\r\n" . '                                                            <td><input type=text id=note name=note class=myinputtext onClick="return tanpa_kutip(event)" style="width:150px;" /></td>' . "\r\n" . '                                                            </tr>' . "\r\n" . '                                                            <tr><td colspan=3 align=center>' . "\r\n" . '                                                            <button class=mybutton onclick=close_pp() title="You are agree to this PR and submit it to Purchasing Dept. "  >' . $_SESSION['lang']['kePurchaser'] . '</button><button class=mybutton onclick=cancel_pp() title="Close this form">' . $_SESSION['lang']['cancel'] . '</button></td></tr></table>' . "\r\n" . '                                                            </fieldset>' . "\r\n" . '                                                            </div>' . "\r\n" . '                                                            ';
					}
				}
			}
//			++$i;
//		}

		break;

	case 'get_form_rejected':
		echo '<div id=rejected_form>' . "\r\n" . '        <fieldset>' . "\r\n" . '        <legend><input type=text readonly=readonly name=rnopp id=rnopp value=' . $_POST['nopp'] . '  /></legend>' . "\r\n" . '        <table cellspacing=1 border=0>' . "\r\n" . '        <tr>' . "\r\n" . '        <td colspan=3>' . "\r\n" . '         PR Rejection form </td></tr>' . "\r\n" . '        <tr>' . "\r\n" . '        <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '        <td>:</td>' . "\r\n" . '        <td><input type=text id=cmnt_tolak name=cmnt_tolak class=myinputtext onClick="return tanpa_kutip(event)" /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr><td colspan=3 align=center>' . "\r\n" . '        <button class=mybutton onclick="rejected_pp_proses(' . $_POST['kolom'] . ')" >' . $_SESSION['lang']['ditolak'] . '</button>' . "\r\n" . '        </td></tr></table>' . "\r\n" . '        </fieldset>' . "\r\n" . '        </div>';
		break;

	case 'get_form_rejected_some':
		$nopp = $_POST['nopp'];
		$sql = 'select * from ' . $dbname . '.log_prapodt where `nopp`=\'' . $nopp . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		echo "\r\n" . '        <fieldset>' . "\r\n" . '        <legend><input type=text id=rnopp name=rnopp value=' . $nopp . ' readonly=readonly /></legend>' . "\r\n" . '        <div style=overflow:auto;width=850px;height:350px;>' . "\r\n" . '        <table cellspacing=1 border=0 class=sortable>' . "\r\n" . '        <thead class=rowheader>' . "\r\n" . '        <tr>' . "\r\n" . '        <td>No.</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodeanggaran'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '        <td>Jumlah Disetujui</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tanggalSdt'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['alasanDtolak'] . '</td>' . "\r\n" . '        <td colspan=2>Action</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n\r\n" . '        <tbody id=reject_some class=rowcontent>' . "\r\n\r\n" . '        ';

		while ($res = mysql_fetch_assoc($query)) {
			$no += 1;
			$sql2 = 'select * from ' . $dbname . '.log_5masterbarang where `kodebarang`=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($query2 = mysql_query($sql2)) || true;
			$res2 = mysql_fetch_assoc($query2);

			if ($res['status'] == 3) {
				$stadData = 'checked';
			}
			else {
				$dis = '';
				$stadData = '';
			}

			echo '<tr>' . "\r\n" . '        <td>' . $no . '</td>' . "\r\n" . '        <td id=kd_brg_' . $no . '>' . $res['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $res2['namabarang'] . '</td>' . "\r\n" . '        <td>' . $res2['satuan'] . '</td>' . "\r\n" . '        <td id=kd_angrn_' . $no . '>' . $res['kd_anggran'] . '</td>' . "\r\n" . '        <td><input type=text id=jmlh_' . $no . ' name=jmlh_' . $no . ' class=myinputtext style=width:100px  ' . $dis . ' value=\'' . $res['jumlah'] . '\' /></td>' . "\r\n" .'        <td><input type=text id=jmlh_approve_' . $no . ' name=jmlh_approve_' . $no . ' class=myinputtext style=width:100px  ' . $dis . ' value=\'' . $res['jml_approve'] . '\'  /></td>' . "\r\n" . '        <td id=tgl_' . $no . '>' . $res['tgl_sdt'] . '</td>' . "\r\n" . '        <td id=ket_' . $no . '>' . $res['keterangan'] . '</td>' . "\r\n" . '        <td><input type=text id=alsnDtolak_' . $no . ' name=alsnDtolak_' . $no . ' class=myinputtext style=width:100px  ' . $dis . ' value=\'' . $res['alasanstatus'] . '\' /></td>' . "\r\n" . '        <td align=center><input type=checkbox onclick=\'checkAlasan(' . $no . ')\' id=\'tolak_chk_' . $no . '\' ' . $stadData . ' ' . $dis . '  /></td>' . "\r\n" . '        </tr>';
		}

		echo '</tbody><tfoot><tr><td colspan=10 align=center><button class=mybutton onclick="rejected_some_done(\'' . $nopp . '\',\'' . $kolom . '\',\'' . $no . '\')" >' . $_SESSION['lang']['done'] . '</button></td></tr></tfoot></table></div></fieldset><input type=hidden id=user_id name=user_id value=\'' . $_SESSION['standard']['userid'] . '\'>';
		break;

	case 'rejected_some_done':
		$user_id = $_POST['user_id'];
		$i = 1;

		while ($i < 6) {
			$sql = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $_POST['nopp'] . '\' and persetujuan' . $i . '=\'' . $user_id . '\' ';

			if ($query2 = mysql_query($sql2)) {
				while ($res = mysql_fetch_assoc($query)) {
					$i = 1;

					while ($i < 6) {
						if ($res['hasilpersetujuan' . $i] == '') {
							$sql2 = 'update ' . $dbname . '.log_prapoht set hasilpersetujuan' . $i . '=\'1\'';
						}

						++$i;
					}
				}

				break;
			}

			echo $sql2;
			exit();
			echo ' Gagal,' . addslashes(mysql_error($conn));
			++$i;
		}

		break;

	case 'cari_pp':
		if (isset($_POST['txtSearch']) || isset($_POST['tglCari'])) {
			$txt_search = $_POST['txtSearch'];
			$txt_tgl = tanggalsystem($_POST['tglCari']);
			$txt_tgl_a = substr($txt_tgl, 0, 4);
			$txt_tgl_b = substr($txt_tgl, 4, 2);
			$txt_tgl_c = substr($txt_tgl, 6, 2);
			$txt_tgl = $txt_tgl_a . '-' . $txt_tgl_b . '-' . $txt_tgl_c;
		}

		if ($_POST['txtSearch'] != '') {
			$where = 'and nopp LIKE  \'%' . $txt_search . '%\'  ';
		}
		else if ($_POST['tglCari'] != '') {
			$where = 'and tanggal LIKE \'%' . $txt_tgl . '%\' ';
		}
		else if (($txt_tgl != '') && ($txt_search != '')) {
			$where = 'nopp LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\' ';
		}

		if ($_POST['pembuat'] != '') {
			$where .= ' and dibuat=\'' . $_POST['pembuat'] . '\'';
		}

		$str = 'SELECT * FROM ' . $dbname . '.log_prapoht where (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\')' . "\r\n" . '                     ' . $where . ' ORDER BY tanggal desc ';

		if ($_POST['nmbrg'] != '') {
			if (3 < strlen($_POST['nmbrg'])) {
				$where .= 'and b.kodebarang in (select distinct kodebarang from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $_POST['nmbrg'] . '%\')';
				$str = 'SELECT distinct a.* FROM ' . $dbname . '.log_prapoht a left join ' . $dbname . '.log_prapodt b on a.nopp=b.nopp' . "\r\n" . '                      where (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\')' . "\r\n" . '                     ' . $where . ' ORDER BY tanggal desc ';
			}
			else {
				exit('Error:Harus 3 Karakter atau lebih');
			}
		}

		#exit(mysql_error($conn));
		($res = mysql_query($str)) || true;
		$rCek = mysql_num_rows($res);

		if (0 < $rCek) {
			while ($bar = mysql_fetch_assoc($res)) {
				$koderorg = substr($bar['nopp'], 15, 4);
				$spr = 'select namaorganisasi from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$no += 1;
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td id=td_' . $no . '>' . $bar['nopp'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas->namaorganisasi . '</td>' . "\r\n" . '                                  <td align=center>' . "\r\n" . '                                  <img src=images/pdf.jpg class=resicon width=\'30\' height=\'30\' title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"> &nbsp' . "\r\n" . '                                  <img src=images/zoom.png class=resicon height=\'30\' title=\'Preview\' onclick="previewDetail(\'' . $bar['nopp'] . '\',event);">    ' . "\r\n" . '                                  </td>';
				$PP_MAX_APPROVAL=$bar['jumlahpemberipersetujuan'];
				if ($bar['close'] == $PP_MAX_APPROVAL) {
					$accept = 0;
					$i = 1;

					while ($i <= 5) {
						if ($bar['hasilpersetujuan' . $i] == '3') {
							$accept = 3;
							break;
						}

						if ($bar['hasilpersetujuan' . $i] == '1') {
							$accept = 1;
						}

						++$i;
					}

					if ($accept == 3) {
						echo '<td colspan=3>' . $_SESSION['lang']['ditolak'] . '</td>';
					}
					else if ($accept == 1) {
						echo '<td colspan=3>' . $_SESSION['lang']['disetujui'] . '</td>';
					}
				}
				else if ($bar['close'] < 2) {
					$a = 1;

					while ($a < 6) {
						if ($bar['persetujuan' . $a] != '') {
							if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] != '') && ($bar['hasilpersetujuan' . $a] != 0)) {
								echo '<td colspan=3>&nbsp;</td>';
							}
							else if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && (($bar['hasilpersetujuan' . $a] == '') || ($bar['hasilpersetujuan' . $a] == 0))) {
								echo "\r\n" . '                                                                <td><a href=# onclick="get_data_pp(\'' . $bar['nopp'] . '\',\'' . $a . '\')">' . $_SESSION['lang']['approve'] . '</a></td>' . "\r\n" . '                                                                <td><a href=# onclick=rejected_pp(\'' . $bar['nopp'] . '\',\'' . $a . '\') >' . $_SESSION['lang']['ditolak'] . '</a></td>' . "\r\n" . '                                                                <td><a href=# onclick="rejected_some_proses(\'' . $bar['nopp'] . '\',\'' . $a . '\')" >' . "\r\n" . '                                                                ' . $_SESSION['lang']['ditolak_some'] . '</a></td>';
							}
						}

						++$a;
					}
				}

				$i = 1;

				while ($i < 6) {
					if (($bar['persetujuan' . $i] != '') && ($bar['persetujuan' . $i] != 0)) {
						$kr = $bar['persetujuan' . $i];
						$sql = 'select * from ' . $dbname . '.datakaryawan where karyawanid=\'' . $kr . '\'';

						#exit(mysql_error());
						($query = mysql_query($sql)) || true;
						$yrs = mysql_fetch_assoc($query);
						echo '<td><a href=# onclick="cek_status_pp(\'' . $bar['hasilpersetujuan' . $i] . '\')">' . $yrs['namakaryawan'] . '</a></td>';
					}
					else {
						echo '<td>&nbsp;</td>';
					}

					++$i;
				}

				echo '</tr>';
			}
		}
		else {
			echo '<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>';
		}

		break;

	case 'tolakBeberapa':
		$tglskrng = date('Y-m-d');
		$adrt = 0;

		foreach ($_POST['kode_brg'] as $lstKdBrg => $kdbrg) {
			$sUpadate = "update $dbname.log_prapodt set ".
				"status=0, ".
				"alasanstatus='" . $_POST['alsan'][$lstKdBrg] . "',".
				"ditolakoleh='" . $_SESSION['standard']['userid'] .  "',".
				"jml_approve='".(real)$_POST['jml_appr'][$lstKdBrg]."' ".
				"where kodebarang='" . $kdbrg . "' and nopp='" . $_POST['nopp'] . "' ";
			if (!mysql_query($sUpadate)) {
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
			else {
				$sUpadate = 'insert into log_prapodt_mod_jml_approve(nopp,kodebarang,ditolakoleh,jml_approve,alasan,jml_asal) values(\'' . $_POST['nopp'] . '\',\'' . $kdbrg . '\',\'' . $_SESSION['standard']['userid'] . '\',\''.(real)$_POST['jml_appr'][$lstKdBrg].'\',\'' . $_POST['alsan'][$lstKdBrg] . '\',\''.(real)$_POST['jml_asal'][$lstKdBrg].'\');';
				if (!mysql_query($sUpadate)) {
					echo ' Gagal,' . addslashes(mysql_error($conn));
				}
				else {

					$adrt += 1;
				}
			}
		}

		if ($adrt != 0) {
			$sData = 'select distinct dibuat,persetujuan1,persetujuan2,persetujuan3,persetujuan4,persetujuan5' . "\r\n" . '            from ' . $dbname . '.log_prapoht where nopp=\'' . $_POST['nopp'] . '\'';

			#exit(mysql_error($conn));
			($qData = mysql_query($sData)) || true;
			$rData = mysql_fetch_assoc($qData);

			if ($rData['persetujuan1'] != '') {
				$to = $rData['persetujuan1'];
			}

			if ($rData['persetujuan2'] != '') {
				$to .= ',' . $rData['persetujuan2'];
			}

			if ($rData['persetujuan3'] != '') {
				$to .= ',' . $rData['persetujuan3'];
			}

			if ($rData['persetujuan4'] != '') {
				$to .= ',' . $rData['persetujuan4'];
			}

			if ($rData['persetujuan5'] != '') {
				$to .= ',' . $rData['persetujuan5'];
			}

			$to = getUserEmail($to);
			$namakaryawan = getNamaKaryawan($rData['dibuat']);
			$nmpnlk = getNamaKaryawan($rData['persetujuan' . $_POST['kolom']]);

			if ($_SESSION['language'] == 'EN') {
				$subject = '[Notification] Partially or all items on PR No:' . $_POST['nopp'] . ' submitted by ' . $namakaryawan . ' rejected by ' . $nmpnlk;
				$body = '<html>' . "\r\n" . '                             <head>' . "\r\n" . '                             <body>' . "\r\n" . '                               <dd>Dear Sir/Madam,</dd><br>' . "\r\n" . '                               <br>' . "\r\n" . '                                Purchase Request No:' . $_POST['nopp'] . ' rejected by [' . $nmpnlk . ']  corresponding to below notes:' . "\r\n" . '                               <br>' . "\r\n" . '                               Item rejected : <ul>';
				$sBrg = 'select kodebarang,alasanstatus from ' . $dbname . '.log_prapodt where nopp=\'' . $_POST['nopp'] . '\' and status=\'3\'';

				#exit(mysql_error($conn));
				($qBrg = mysql_query($sBrg)) || true;

				while ($rBrg = mysql_fetch_assoc($qBrg)) {
					$body .= '<li>' . $nmBarang[$rBrg['kodebarang']] . ', note : ' . $rBrg['alasanstatus'] . '</li>';
				}

				$body .= '</ul><br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Regards,<br>' . "\r\n" . '                               eAgro Plantation Management Software.' . "\r\n" . '                             </body>' . "\r\n" . '                             </head>' . "\r\n" . '                           </html>' . "\r\n" . '                           ';
			}
			else {
				$subject = '[Notifikasi] Sebagian atau Seluruhnya PP No :' . $_POST['nopp'] . ' dari ' . $namakaryawan . ' ditolak oleh ' . $nmpnlk;
				$body = '<html>' . "\r\n" . '                             <head>' . "\r\n" . '                             <body>' . "\r\n" . '                               <dd>Dengan Hormat,</dd><br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Permintaan pembelian no.' . $_POST['nopp'] . ' ditolak oleh [' . $nmpnlk . '] dengan alasan.' . "\r\n" . '                               <br>' . "\r\n" . '                               Item yang ditolak adalah : <ul>';
				$sBrg = 'select kodebarang,alasanstatus from ' . $dbname . '.log_prapodt where nopp=\'' . $_POST['nopp'] . '\' and status=\'3\'';

				#exit(mysql_error($conn));
				($qBrg = mysql_query($sBrg)) || true;

				while ($rBrg = mysql_fetch_assoc($qBrg)) {
					$body .= '<li>' . $nmBarang[$rBrg['kodebarang']] . ', alasan : ' . $rBrg['alasanstatus'] . '</li>';
				}

				$body .= '</ul><br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Regards,<br>' . "\r\n" . '                               eAgro Plantation Management Software.' . "\r\n" . '                             </body>' . "\r\n" . '                             </head>' . "\r\n" . '                           </html>' . "\r\n" . '                           ';
			}

			$x = kirimEmailWindows($to, $subject, $body);
			echo $x;
		}

		break;
}

?>
