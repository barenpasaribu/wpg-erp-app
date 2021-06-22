<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';

if (isset($_POST['proses']) == 'show_all_data') {
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

			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar['nopp'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas['namaorganisasi'] . '</td>' . "\r\n" . '                                  <td>' . $b . '</td>' . "\r\n" . '                         <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['nopp'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $bar['kodeorg'] . '\',\'' . $bar['close'] . '\');"></td>' . "\r\n" . '                                  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar['nopp'] . '\',\'' . $bar['close'] . '\');"></td>' . "\r\n" . '                                  <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n" . '                                 </tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}
}

if (isset($_POST['proses']) == 'cek_data_header') {
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
				$sql = 'select * from ' . $dbname . '.log_pol_ht where `nopl`=\'' . $nopp . '\'';

				#exit(mysql_error());
				($query = mysql_query($sql)) || true;
				$res = mysql_fetch_row($query);

				if ($res < 1) {
					$ins = 'insert into ' . $dbname . '.log_pol_ht (`nopl`,`kodeorg`,`tanggal`,`create_by`,`catatan`) values (\'' . $nopp . '\',\'' . $kd_org . '\',\'' . $tgl . '\',\'' . $id_user . '\',\'' . $_POST['catatan'] . '\')';

					#exit(mysql_error());
					($qry = mysql_query($ins)) || true;
					$sql2 = 'select * from ' . $dbname . '.log_pol_dt where `nopl`=\'' . $nopp . '\'';

					#exit(mysql_error());
					($query2 = mysql_query($sql2)) || true;
					$res2 = mysql_fetch_row($query2);

					if ($res2 < 1) {
						foreach ($_POST['kdbrg'] as $row => $Act) {
							$kdbrg = $Act;
							$nmbrg = $_POST['nmbrg'][$row];
							$rjmlhDiminta = $_POST['rjmlhDiminta'][$row];
							$hargasatuan = $_POST['hargasatuan'][$row];
							$ketrng = $_POST['ketrng'][$row];
							$sqp = 'insert into ' . $dbname . '.log_pol_dt(`nopl`, `kodebarang`, `jumlah`,`hargasatuan`,`keterangan`) values(\'' . $nopp . '\',\'' . $kdbrg . '\',\'' . $rjmlhDiminta . '\',\'' . $hargasatuan . '\',\'' . $ketrng . '\')';

							if (!mysql_query($sqp)) {
								echo 'Gagal,' . $sqp;
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

	if (isset($_POST['txtSearch']) && $_POST['txtSearch'] != '') {
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
		$where = 'and nopl LIKE  \'%' . $txt_search . '%\'';
		$where2 = 'nopl LIKE  \'%' . $txt_search . '%\'';
	}
	else if ($txt_tgl != '') {
		$where .= 'and tanggal LIKE \'' . $txt_tgl . '\'';
		$where2 .= 'and tanggal LIKE \'' . $txt_tgl . '\'';
	}
	else if (($txt_tgl != '') && ($txt_search != '')) {
		$where .= 'and nopl LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\'';
		$where2 .= ' nopl LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\'';
	}

	$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_pol_ht where substring(nopl,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $where . ' order by tanggal desc';
	$str = 'select * from ' . $dbname . '.log_pol_ht where substring(nopl,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $where . ' order by tanggal limit ' . $offset . ',' . $limit . '';
	#echo $str;
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
			$koderorg = substr($bar['nopl'], 15, 4);
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_assoc($rep);
			$skry = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['create_by'] . '\'';

			#exit(mysql_error());
			($qkry = mysql_query($skry)) || true;
			$rkry = mysql_fetch_assoc($qkry);
			$cekPt = substr($bar->nopl, 12, 4);
			$no += 1;

			if ($bar['status'] == 'Draft') {
				$b = '<a href=# id=seeprog onclick=frm_ajun(\'' . $bar['nopl'] . '\',\'' . $bar['kodeorg'] . '\') title="Click untuk posting">Draft</a>';
			}
			else if ($bar['status'] == 'Posting') {
				$b = 'Posted';
			}

			$ed_kd_org = substr($bar['nopl'], 15, 4);

			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td>' . $bar['nopl'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas['namaorganisasi'] . '</td>' . "\r\n" . '                                  <td>' . $rkry['namakaryawan'] . '</td>' . "\r\n" . '                                  <td>' . $b . '</td>';

			if ($bar['create_by'] == $_SESSION['standard']['userid']) {
				if($bar['status'] == 'Draft'){
					echo '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['nopl'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $ed_kd_org . '\',\'' . $bar['status'] . '\');" >' . "\r\n" . '                                        <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar['nopl'] . '\',\'' . $bar['status'] . '\');" >';
					echo '<img onclick="previewDetail(\'' . $bar['nopl'] . '\',event);" title="Detail PP" class="resicon" src="images/zoom.png"><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopl'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n" . '                                 ';
				}else {
					echo '<td><img onclick="previewDetail(\'' . $bar['nopl'] . '\',event);" title="Detail PP" class="resicon" src="images/zoom.png"><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopl'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n" . '                                 ';
				}
				
				
				
				
			}
			else {
				echo '<td><img onclick="previewDetail(\'' . $bar['nopl'] . '\',event);" title="Detail PP" class="resicon" src="images/zoom.png">' . "\r\n" . '                                  <img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopl'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
			}
		}

		echo '</tr>' . "\r\n" . '                                 <tr><td colspan=7 align=center>' . "\r\n" . '                                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '                                <br />' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                                </td>' . "\r\n" . '                                </tr>';
		}
	}
	else {
		echo 'Gagal,' . mysql_error($conn);
	}
}
?>
