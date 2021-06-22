<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';

if (isset($_POST['rnopp'])) {
	$nopp = $_POST['rnopp'];
	$induk_org = substr($nopp, 15, 4);
	$kode_org = $_POST['kode_org'];
	$str = 'SELECT * FROM ' . $dbname . '.log_prapoht WHERE nopp=\'' . $nopp . '\'';

	if ($res = mysql_query($str)) {
		echo '<table class=data cellspacing=1 border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                 <tr><td colspan=3>Pengajuan Ke</td></tr>' . "\r\n" . '                                 </thead>' . "\r\n" . '                                 <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$sq = 'select * ' . $dbname . '.datakaryawan where lokasitugas=\'' . $bar->kodeorg . '\' or induk=\'' . $induk_org . '\'';

			#exit(mysql_error());
			($qty = mysql_query($sq)) || true;
			$opt .= '<option value=\'' . $res2->karyawanid . '\'>' . $res2->namakaryawan . '</option>';
			$no += 1;
			echo "\r\n" . '                                <tr class=rowcontent style=\'cursor:pointer;\' onclick="setDraft( \'' . $bar->karyawanid . '\',\'' . $bar->nopp . '\')" title=\'Click\' >' . "\r\n" . '                                          <td class=firsttd colspan=3>' . $no . '</td>' . "\r\n" . '                                                <tr >' . "\r\n" . '                                                        <td>No. PP</td>' . "\r\n" . '                                                        <td>:</td>' . "\r\n" . '                                                        <td><input id=nopp type=text readonly=readonly value=' . $bar->nopp . '></td>' . "\r\n" . '                                                </tr>' . "\r\n" . '                                                <tr>' . "\r\n" . '                                                        <td>Nama Karyawan</td>' . "\r\n" . '                                                        <td>:</td>' . "\r\n" . '                                                        <td>' . "\r\n" . '                                                        <select id=\'kd_krywn\'>' . "\r\n" . '                                                        <option value="" selected=selected></option>' . $opt . "\r\n" . '                                                        </select></td>' . "\r\n" . '                                                </tr>';
		}

		echo '</tbody>' . "\r\n" . '                                  <tfoot>' . "\r\n" . '                                  </tfoot>' . "\r\n" . '                                  </table>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

if (isset($_POST['rkrywn_id'])) {
	$rkrywn_id = intval($_POST['rkrywn_id']);
	$rkrywn_id = addZero($rkrywn_id, 10);
	$no_pp = $_POST['no_pp'];
	$tanggl = date('Y-m-d');
	$ql = 'update ' . $dbname . '.log_prapoht set persetujuan1=\'' . $rkrywn_id . '\',close=\'1\',tglp1=\'' . $tanggl . '\' where nopp=\'' . $no_pp . '\' ';

	if ($res = mysql_query($ql)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

if (isset($_POST['hnopp'])) {
	$nopp = $_POST['hnopp'];
	$krywn = $_POST['karywn_id'];
	$str = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\'';

	if ($res = mysql_query($str)) {
		echo '<table class=data cellspacing=1 border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                 <tr><td colspan=6>Status Persetujuan</td></tr>' . "\r\n" . '                                 <tr class=rowheader>' . "\r\n" . '                                 <td class=firsttd>' . "\r\n" . '                                 No.' . "\r\n" . '                                 </td>' . "\r\n" . '                                 <td>Nama Karyawan</td>' . "\r\n" . '                                 <td>Jabatan</td>' . "\r\n" . '                                 <td>Lokasi Tugas</td>' . "\r\n" . '                                 <td>Keputusan</td>' . "\r\n" . '                             <td>Catatan</td>' . "\r\n" . '                                 </tr>' . "\r\n" . '                                 </thead>' . "\r\n" . '                                 <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$where = '(karyawanid=\'' . $bar->persetujuan1 . '\' OR ' . 'karyawanid=\'' . $bar->persetujuan2 . '\' OR ' . 'karyawanid=\'' . $bar->persetujuan3 . '\' OR ' . 'karyawanid=\'' . $bar->persetujuan4 . '\' OR ' . 'karyawanid=\'' . $bar->persetujuan5 . '\')AND lokasitugas=\'' . $bar->kodeorg . '\'';
			$sql = 'select * from ' . $dbname . '.datakaryawan where ' . $where;

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$res3 = mysql_fetch_object($query);
			$sql2 = 'select * from ' . $dbname . '.sdm_5jabatan where kodejabatan=\'' . $res3->kodejabatan . '\'';

			#exit(mysql_error());
			($query2 = mysql_query($sql2)) || true;
			$res2 = mysql_fetch_object($query2);
			if (($bar->hasilpersetujuan1 == '') || ($bar->hasilpersetujuan2 == '') || ($bar->hasilpersetujuan3 == '') || ($bar->hasilpersetujuan4 == '') || ($bar->hasilpersetujuan5 == '')) {
				$b = 'Not Processed';
			}
			else {
				if (($bar->hasilpersetujuan1 == '1') || ($bar->hasilpersetujuan2 == '1') || ($bar->hasilpersetujuan3 == '1') || ($bar->hasilpersetujuan4 == '1') || ($bar->hasilpersetujuan5 == '1')) {
					$b = 'Approved';
				}
				else {
					if (($bar->hasilpersetujuan1 == '2') || ($bar->hasilpersetujuan2 == '2') || ($bar->hasilpersetujuan3 == '2') || ($bar->hasilpersetujuan4 == '2') || ($bar->hasilpersetujuan5 == '2')) {
						$b = 'Must Corrected';
					}
					else {
						if (($bar->hasilpersetujuan1 == '3') || ($bar->hasilpersetujuan2 == '3') || ($bar->hasilpersetujuan3 == '3') || ($bar->hasilpersetujuan4 == '3') || ($bar->hasilpersetujuan5 == '3')) {
							$b = 'Rejected';
						}
					}
				}
			}

			$no += 1;
			echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setDraft(\'' . $bar->karyawanid . '\',\'' . $bar->nopp . '\')" title=\'Click\' >' . "\r\n" . '                                          <td class=firsttd>' . $no . '</td>' . "\r\n" . '                                          <td>' . $res3->namakaryawan . '</td>' . "\r\n" . '                                          <td>' . $res2->namajabatan . '</td>' . "\r\n" . '                                          <td>' . $res3->lokasitugas . '</td>' . "\r\n" . '                                          <td>' . $b . '</td>' . "\r\n" . '                                          <td>' . $bar->keterangan . '</td>' . "\r\n" . '                                         </tr>';
		}

		echo '</tbody>' . "\r\n" . '                                  <tfoot>' . "\r\n" . '                                  </tfoot>' . "\r\n" . '                                  </table>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

if (isset($_POST['method']) == 'cari_pp') {
	$limit = 50;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;

	if (isset($_POST['txtSearch'])) {
		$txt_search = $_POST['txtSearch'];
		$txt_tgl = tanggalsystem($_POST['tglCari']);
		$txt_tgl_a = substr($txt_tgl, 0, 4);
		$txt_tgl_b = substr($txt_tgl, 4, 2);
		$txt_tgl_c = substr($txt_tgl, 6, 2);
		$txt_tgl = $txt_tgl_a . '-' . $txt_tgl_b . '-' . $txt_tgl_c;
	}
	else {
		$txt_search = '';
		$txt_tgl = '';
	}

	if ($txt_search != '') {
		$where = 'and nopp LIKE  \'%' . $txt_search . '%\'';
		$where2 = 'nopp LIKE  \'%' . $txt_search . '%\'';
	}
	else if ($txt_tgl != '') {
		$where .= 'and tanggal LIKE \'' . $txt_tgl . '\'';
		$where2 .= 'and tanggal LIKE \'' . $txt_tgl . '\'';
	}
	else if (($txt_tgl != '') && ($txt_search != '')) {
		$where .= 'and nopp LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\'';
		$where2 .= ' nopp LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\'';
	}

	if (($txt_search == '') && ($txt_tgl == '')) {
		if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
			$sCek = 'select bagian from ' . $dbname . '.datakaryawan where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

			#exit(mysql_error($conn));
			($qCek = mysql_query($sCek)) || true;
			$rCek = mysql_fetch_assoc($qCek);
			if (($rCek['bagian'] == 'PUR') || ($rCek['bagian'] == 'AGR')) {
				$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_prapoht order by tanggal desc';
				$str = 'select * from ' . $dbname . '.log_prapoht order by tanggal desc limit ' . $offset . ',' . $limit . '';
			}
			else {
				$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_prapoht where dibuat=\'' . $_SESSION['standard']['userid'] . '\'  order by tanggal desc';
				$str = 'select * from ' . $dbname . '.log_prapoht where  dibuat=\'' . $_SESSION['standard']['userid'] . '\' order by tanggal desc limit ' . $offset . ',' . $limit . ' ';
			}
		}
		else {
			$str = 'select * from ' . $dbname . '.log_prapoht where substring(nopp,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'  order by tanggal desc limit ' . $offset . ',' . $limit . '';
		}
	}
	else if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
		$sCek = 'select bagian from ' . $dbname . '.datakaryawan where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

		#exit(mysql_error($conn));
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_fetch_assoc($qCek);
		if (($rCek['bagian'] == 'PUR') || ($rCek['bagian'] == 'PUR')) {
			$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_prapoht where ' . $where2 . ' order by tanggal desc';
			$str = 'select * from ' . $dbname . '.log_prapoht where ' . $where2 . ' order by tanggal desc limit ' . $offset . ',' . $limit . '';
		}
		else {
			$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_prapoht where dibuat=\'' . $_SESSION['standard']['userid'] . '\' ' . $where . ' order by tanggal desc';
			$str = 'select * from ' . $dbname . '.log_prapoht where  dibuat=\'' . $_SESSION['standard']['userid'] . '\' ' . $where . ' order by tanggal desc limit ' . $offset . ',' . $limit . ' ';
		}
	}
	else {
		$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_prapoht where substring(nopp,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $where . ' order by tanggal desc';
		$str = 'select * from ' . $dbname . '.log_prapoht where substring(nopp,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $where . ' order by tanggal limit ' . $offset . ',' . $limit . '';
	}

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;

	while ($jsl = mysql_fetch_object($query)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	if ($res = mysql_query($str)) {
		$numrows = mysql_num_rows($res);

		if ($numrows < 1) {
			echo '<tr class=rowcontent><td colspan=8>Not Found</td></tr>';
		}
		else {
			while ($bar = mysql_fetch_assoc($res)) {
				$koderorg = substr($bar['nopp'], 15, 4);
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_assoc($rep);
				$cekPt = substr($bar->nopp, 12, 4);
				$skry = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['dibuat'] . '\'';

				#exit(mysql_error());
				($qkry = mysql_query($skry)) || true;
				$rkry = mysql_fetch_assoc($qkry);
				$no += 1;

				if ($bar['close'] == '0') {
					$b = '<a href=# id=seeprog onclick=frm_ajun(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\') title="Click To Change The Status ">Need Approval</a>';
				}
				else if ($bar['close'] == '1') {
					$b = '<a href=# id=seeprog onclick=frm_ajun(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\') title="Waiting Approval">Waiting Approval</a>';
				}
				else if ($bar['close'] == '2') {
					$i = 0;

					while ($i < 6) {
						if ($bar['hasilpersetujuan' . $i] == 1) {
							$b = '<a href=# id=seeprog  title="Can Make PO">Approved</a>';
						}
						else if ($bar['hasilpersetujuan' . $i] == 3) {
							$b = '<a href=# id=seeprog  title="Cant Make PO">Rejected</a>';
						}

						++$i;
					}
				}

				$ed_kd_org = substr($bar['nopp'], 15, 4);
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>KOKOK' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar['nopp'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas['namaorganisasi'] . '</td>' . "\r\n" . '                                  <td>' . $rkry['namakaryawan'] . '</td>' . "\r\n" . '                                  <td>' . $b . '</td>';

				if ($bar['dibuat'] == $_SESSION['standard']['userid']) {
					echo '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['nopp'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $ed_kd_org . '\',\'' . $bar['close'] . '\');" >' . "\r\n" . '                                        <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\');" >';
					echo '<img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);">' . "\r\n" . '                                            <img onclick="previewDetail(\'' . $bar['nopp'] . '\',event);" title="Detail PP" class="resicon" src="images/zoom.png"><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n" . '                                 ';
				}
				else {
					echo '<td><img onclick="previewDetail(\'' . $bar['nopp'] . '\',event);" title="Detail PP" class="resicon" src="images/zoom.png"><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
				}

				echo '</tr>';
			}

			echo '</tr>' . "\r\n" . '                                 <tr><td colspan=7 align=center>' . "\r\n" . '                                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '                                <br />' . "\r\n" . '                                <button class=mybutton onclick=cariData(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                                <button class=mybutton onclick=cariData(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                                </td>' . "\r\n" . '                                </tr>';
		}
	}
	else {
		echo 'Gagal,' . mysql_error($conn);
	}
}

if( isset($_POST['proses'])){
	$post_proses = $_POST['proses'];
}else{
	$post_proses = "";
}
if ($post_proses == 'show_all_data') {	
	if ($_SESSION['empl']['tipeinduk'] == 'HOLDING') {
		$str = 'select * from ' . $dbname . '.log_prapoht where kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' order by nopp desc';
	}
	else {
		$str = 'select * from ' . $dbname . '.log_prapoht where kodeorg=\'' . substr($_SESSION['empl']['lokasitugas'], 0, 4) . '\' order by nopp desc';
	}

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_assoc($res)) {
			$koderorg = $bar['kodeorg'];
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_assoc($rep);
			$cekPt = substr($bar->nopp, 12, 4);
			$no += 1;

			if ($bar['close'] == '0') {
				$b = '<a href=# id=seeprog onclick=frm_ajun(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\') title="Click To Change The Status ">Need Approval</a>';
			}
			else if ($bar['close'] == '1') {
				$b = '<a href=# id=seeprog onclick=frm_ajun(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\') title="Waiting Approval">Waiting Approval</a>';
			}
			else if ($bar['close'] == '2') {
				$i = 0;

				while ($i < 6) {
					if ($bar['hasilpersetujuan2' . $i] == 1) {
						$b = '<a href=# id=seeprog  title="Can Make PO">Approved</a>';
					}
					else if ($bar['persetujuan' . $i] == 3) {
						$b = '<a href=# id=seeprog  title="Can Make PO">Rejected</a>';
					}

					++$i;
				}
			}

			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>JUJU' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar['nopp'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas['namaorganisasi'] . '</td>' . "\r\n" . '                                  <td>' . $b . '</td>' . "\r\n" . '                         <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['nopp'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $bar['kodeorg'] . '\',\'' . $bar['close'] . '\');"></td>' . "\r\n" . '                                  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\');"></td>' . "\r\n" . '                                  <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n" . '                                 </tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}
}

if ($post_proses == 'cek_data_header') {
	
	if ($_POST['cknopp'] == '') {
		echo 'Warning:Please Enter The System Properly';
		exit();
	}
	else {
		$nopp = $_POST['cknopp'];
		$tgl = tanggalsystem($_POST['tgl_pp']);
		$kodeorg = $_POST['kd_org'];
		$id_user = $_POST['user_id'];
		$sorg = 'select alokasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodeorg . '\'';

		#exit(mysql_error());
		($qorg = mysql_query($sorg)) || true;
		$rorg = mysql_fetch_assoc($qorg);
		$kd_org = $rorg['alokasi'];

		foreach ($_POST['kdbrg'] as $rey => $Opr) {
			$tgl_sdt = tanggalsystem($_POST['rtgl_sdt'][$rey]);
			$starttime = strtotime($_POST['tgl_pp']);
			$jumlahpemberipersetujuan = $_POST['jumlahpemberipersetujuan']=='' ? 0 :$_POST['jumlahpemberipersetujuan'];
			$endtime = strtotime($_POST['rtgl_sdt'][$rey]);
			$timediff = $endtime - $starttime;
			$days = intval($timediff / 86400);
			if (($Opr == '') || ($_POST['rjmlhDiminta'][$rey] == '') || ($tgl_sdt < $tgl) ) {
				//echo 'Warning : Data Tidak Boleh Kosong Dan Tanggal Tidak Boleh Lebih Kecil Dari Tanggal PP, Min 7 Hari Dari Tanggal PP';
				echo 'Warning : Data Tidak Boleh Kosong Dan Tanggal Tidak Boleh Lebih Kecil Dari Tanggal PP';
				exit();
			}
			else {
				$sql = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\'';

				#exit(mysql_error());
				($query = mysql_query($sql)) || true;
				$res = mysql_fetch_row($query);

				if ($res < 1) {
					// $ins = 'insert into ' . $dbname . '.log_prapoht (nopp,kodeorg,tanggal,dibuat,catatan) values (\'' . $nopp . '\',\'' . $kd_org . '\',\'' . $tgl . '\',\'' . $id_user . '\',\'' . $_POST['catatan'] . '\')';
					$ins = "insert into $dbname.log_prapoht (nopp,kodeorg,tanggal,dibuat,catatan,jumlahpemberipersetujuan) values (".
					"'$nopp','$kd_org','$tgl','$id_user','" . $_POST['catatan'] . "',$jumlahpemberipersetujuan)";
					#exit(mysql_error());
					($qry = mysql_query($ins)) || true;
					$sql2 = 'select * from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\'';

					#exit(mysql_error());
					($query2 = mysql_query($sql2)) || true;
					$res2 = mysql_fetch_row($query2);

					if ($res2 < 1) {
						foreach ($_POST['kdbrg'] as $row => $Act) {
							$kdbrg = $Act;
							$nmbrg = $_POST['nmbrg'][$row];
							$rjmlhDiminta = $_POST['rjmlhDiminta'][$row];
							$rkd_angrn = $_POST['rkd_angrn'][$row];
							$rtgl_sdt = tanggalsystem($_POST['rtgl_sdt'][$row]);
							$ketrng = $_POST['ketrng'][$row];
							$sqp = 'insert into ' . $dbname . '.log_prapodt(nopp, kodebarang, jumlah,kd_anggran,tgl_sdt,keterangan) values(\'' . $nopp . '\',\'' . $kdbrg . '\',\'' . $rjmlhDiminta . '\',\'' . $rkd_angrn . '\',\'' . $rtgl_sdt . '\',\'' . $ketrng . '\')';

							if (!mysql_query($sqp)) {
								echo 'Gagal,' . mysql_error($conn);
								exit();
							}
						}

						$test = count($_POST['kdbrg']);
						echo $test;
					}
				}
			}
		}
	}
}

?>
