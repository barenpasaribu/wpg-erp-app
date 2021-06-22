<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
echo "\r\n";
$method = $_POST['method'];
$nopo = $_POST['nopo'];
$user_id = $_SESSION['standard']['userid'];

switch ($method) {
case 'get_form_approval':
	$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$rest = mysql_fetch_assoc($query);
	$i = 1;

	while ($i < 4) {
		if ($user_id == $rest['persetujuan' . $i]) {
			if ($rest['persetujuan3'] != '') {
				echo '<br /><div id=approve>' . "\r\n" . '                                <fieldset>' . "\r\n" . '                                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . '  /></legend>' . "\r\n" . '                                <table cellspacing=1 border=0>' . "\r\n" . '                                <tr>' . "\r\n" . '                                <td colspan=3>' . "\r\n" . '                                Submit to Purchasing dept for Release</td></tr>' . "\r\n\r\n" . '                                <tr><td colspan=3 align=center>' . "\r\n" . '                                <button class=mybutton onclick=close_po() >' . $_SESSION['lang']['yes'] . '</button><button class=mybutton onclick=cancel_po() >' . $_SESSION['lang']['no'] . '</button></td></tr></table><input type=hidden name=kolom id=kolom />' . "\r\n" . '                                </fieldset>' . "\r\n" . '                                </div>';
			}
			else {
				echo '<br />' . "\r\n" . '                                        <div id=test>' . "\r\n" . '                                        <fieldset>' . "\r\n" . '                                        <legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . '  /></legend>' . "\r\n" . '                                        <table cellspacing=1 border=0>' . "\r\n" . '                                        <tr>' . "\r\n" . '                                        <td colspan=3>' . "\r\n" . '                                        Submit for the next verification :</td>' . "\r\n" . '                                        </tr>' . "\r\n" . '                                        <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n" . '                                        <td>:</td>' . "\r\n" . '                                        <td valign=top>';
				$optPur = '';
				$klq = 'select karyawanid,namakaryawan,bagian,lokasitugas from ' . $dbname . '.`datakaryawan` where tipekaryawan=\'5\' and karyawanid!=\'' . $user_id . '\' and lokasitugas!=\'\' order by namakaryawan asc';

				#exit(mysql_error());
				($qry = mysql_query($klq)) || true;

				while ($rst = mysql_fetch_object($qry)) {
					$sBag = 'select nama from ' . $dbname . '.sdm_5departemen where kode=\'' . $rst->bagian . '\'';

					#exit(mysql_error());
					($qBag = mysql_query($sBag)) || true;
					$rBag = mysql_fetch_assoc($qBag);
					$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . ' [' . $rst->lokasitugas . '] [' . $rBag['nama'] . ']</option>';
				}

				echo "\r\n" . '                                                <select id=id_user name=id_user>' . "\r\n" . '                                                        ' . $optPur . '; ' . "\r\n" . '                                                </select></td></tr>' . "\r\n" . '                                                <tr>' . "\r\n\r\n" . '                                                <td colspan=3 align=center>' . "\r\n" . '                                                <button class=mybutton onclick=forward_po() title="Submit for the next verification" >' . $_SESSION['lang']['diajukan'] . '</button>' . "\r\n" . '                                                <button class=mybutton onclick=close_form_po() title="Submit to Purchasing dept for Release"  >' . $_SESSION['lang']['kePurchaser'] . '</button>' . "\r\n" . '                                                <button class=mybutton onclick=cancel_po() title="Menutup Form Ini">' . $_SESSION['lang']['close'] . '</button>' . "\r\n" . '                                                </td></tr></table><br /> ' . "\r\n\r\n" . '                                                </fieldset></div>' . "\r\n" . '                                                <div id=approve style=display:none>' . "\r\n" . '                                                <fieldset>' . "\r\n" . '                                                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . '  /></legend>' . "\r\n" . '                                                <table cellspacing=1 border=0>' . "\r\n" . '                                                <tr>' . "\r\n" . '                                                <td colspan=3>' . "\r\n" . '                                                Submit to Purchasing dept for Release</td></tr>' . "\r\n\r\n" . '                                                <tr><td colspan=3 align=center>' . "\r\n" . '                                                <button class=mybutton onclick=close_po() >' . $_SESSION['lang']['yes'] . '</button>' . "\r\n" . '                                                <button class=mybutton onclick=cancel_po() >' . $_SESSION['lang']['no'] . '</button></td></tr></table>' . "\r\n" . '                                                </fieldset>' . "\r\n" . '                                                </div>' . "\r\n" . '                                                <input type=hidden name=method id=method  /> ' . "\r\n" . '                                                <input type=hidden name=user_id id=user_id value=' . $user_id . ' />' . "\r\n" . '                                                <input type=hidden name=nopo id=nopo value=' . $nopo . '  />' . "\r\n" . '                                                <input type=hidden name=kolom id=kolom />' . "\r\n" . '                                                ';
			}
		}

		++$i;
	}

	break;

case 'get_form_rejected':
	echo '<div id=rejected_form>' . "\r\n" . '                <fieldset>' . "\r\n" . '                <legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . '  /></legend>' . "\r\n" . '                <table cellspacing=1 border=0>' . "\r\n" . '                <tr>' . "\r\n" . '                <td colspan=3>' . "\r\n" . '                Are you sure rejecting this PO</td></tr>' . "\r\n" . '                <tr><td colspan=3 align=center>' . "\r\n" . '                <button class=mybutton onclick=rejected_po_proses() >' . $_SESSION['lang']['yes'] . '</button>' . "\r\n" . '                <button class=mybutton onclick=cancel_po() >' . $_SESSION['lang']['no'] . '</button>' . "\r\n" . '                </td></tr></table>' . "\r\n" . '                </fieldset>' . "\r\n" . '                </div>' . "\r\n" . '                <input type=hidden name=method id=method  /> ' . "\r\n" . '                <input type=hidden name=user_id id=user_id value=' . $user_id . ' />' . "\r\n" . '                <input type=hidden name=nopo id=nopo value=' . $nopo . '  />' . "\r\n" . '                <input type=hidden name=kolom id=kolom />' . "\r\n" . '                ';
	break;

case 'cari_po':
	if (isset($_POST['txtSearch']) || isset($_POST['tglCari'])) {
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
		$where = ' nopo LIKE  \'%' . $txt_search . '%\' and (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\')';
	}
	else if ($txt_tgl != '') {
		$where .= '  tanggal LIKE \'' . $txt_tgl . '\' and (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\')';
	}
	else if (($txt_tgl != '') && ($txt_search != '')) {
		$where .= '  nopo LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\' and (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\')';
	}

	if (($txt_search == '') && ($txt_tgl == '')) {
		$strx = 'select * from ' . $dbname . '.log_poht where (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' ' . "\r\n" . '                        or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\') order by nopo desc';
	}
	else {
		$strx = 'select * from ' . $dbname . '.log_poht where ' . $where;
	}

	if ($res = mysql_query($strx)) {
		$numrows = mysql_num_rows($res);

		if ($numrows < 1) {
			echo '<tr class=rowcontent><td colspan=13>Not Found</td></tr>';
		}
		else {
			while ($bar = mysql_fetch_assoc($res)) {
				$kodeorg = $bar['kodeorg'];
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$no += 1;
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                                <td>' . $no . '</td>' . "\r\n" . '                                                <td id=td_' . $no . '>' . $bar['nopo'] . '</td>' . "\r\n" . '                                                <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                                <td>' . $bas->namaorganisasi . '</td>' . "\r\n" . '                                                <td align=center>' . "\r\n" . '                                                <img src=images/pdf.jpg class=resicon width=\'30\' height=\'30\' title=\'Print\' ' . "\r\n" . '                                                onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
				$a = 1;

				while ($a < 4) {
					if ($bar['persetujuan' . $a] != '') {
						if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] != '')) {
							echo "\r\n" . '                                                                                                <td><button class=mybutton disabled onclick="get_data_po(\'' . $bar['nopo'] . '\')">' . $_SESSION['lang']['approve'] . '</button></td>' . "\r\n" . '                                                                                                <td><button class=mybutton disabled onclick=rejected_po(\'' . $bar['nopo'] . '\') >' . $_SESSION['lang']['ditolak'] . '</button></td>' . "\r\n" . '                                                                                                ';
						}
						else if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] == '')) {
							echo "\r\n" . '                                                                                                <td><button class=mybutton onclick="get_data_po(\'' . $bar['nopo'] . '\')">' . $_SESSION['lang']['approve'] . '</button></td>' . "\r\n" . '                                                                                                <td><button class=mybutton onclick=rejected_po(\'' . $bar['nopo'] . '\') >' . $_SESSION['lang']['ditolak'] . '</button></td>' . "\r\n" . '                                                                                                </td>';
						}
					}

					++$a;
				}

				$i = 1;

				while ($i < 4) {
					if ($bar['persetujuan' . $i] != '') {
						$kr = $bar['persetujuan' . $i];
						$sql = 'select * from ' . $dbname . '.datakaryawan where karyawanid=\'' . $kr . '\'';

						#exit(mysql_error());
						($query = mysql_query($sql)) || true;
						$yrs = mysql_fetch_assoc($query);

						if ($bar['hasilpersetujuan' . $i] == '') {
							$b = 'No Decision yet ';
						}
						else if ($bar['hasilpersetujuan' . $i] == '1') {
							$b = $_SESSION['lang']['approve'];
						}
						else if ($bar['hasilpersetujuan' . $i] == '3') {
							$b = $_SESSION['lang']['ditolak'];
						}

						echo '<td align=center>' . $yrs['namakaryawan'] . '<br />(' . $b . ')</td>';
					}
					else {
						echo '<td>&nbsp;</td>';
					}

					++$i;
				}

				echo '</tr><input type=hidden id=nopo_' . $no . ' name=nopo_' . $no . ' value=\'' . $bar['nopo'] . '\' />';
			}
		}
	}
	else {
		echo 'Gagal,' . mysql_error($conn);
	}

	break;
}

?>
