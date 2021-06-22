<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$optNm = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nopp = $_POST['rnopp'];
$tanggal = tanggalsystem($_POST['rtgl_pp']);
$kodeorg = $_POST['rkd_bag'];
$method = $_POST['method'];
$user_id = $_POST['usr_id'];
$nopp2 = $_POST['dnopp'];
$stat_cls = $_POST['stat'];
$tgl = date('Ymd');
$bln = substr($tgl, 4, 2);
$thn = substr($tgl, 0, 4);
$catatan = $_POST['catatan'];

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.log_pol_ht where nopl=\'' . $nopp . '\'';

	if (mysql_query($strx)) {
		$ql = 'delete from ' . $dbname . '.log_pol_dt where nopl=\'' . $nopp . '\'';

		#exit(mysql_error());
		mysql_query($ql) ;
	}
	else {
		echo ' Error,' . addslashes(mysql_error($conn));
	}

	break;

case 'update':
	$strx = 'update ' . $dbname . '. log_prapoht set tanggal=\'' . $tanggal . '\',kodeorg=\'' . $kodeorg . '\',catatan=\'' . $catatan . '\' where nopp=\'' . $nopp . '\'';

	if (mysql_query($strx)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	if ($nopp == '') {
		echo 'Warning: Please use system properly, PR number not defined';
		ecit();
	}
	else {
		$sorg = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodeorg . '\'';

		#exit(mysql_error());
		($qorg = mysql_query($sorg)) ;
		$rorg = mysql_fetch_assoc($qorg);
		$strx = 'insert into ' . $dbname . '.log_prapoht(`nopp`, `kodeorg`, `tanggal`,`dibuat`,`catatan`)' . "\r\n" . '                                        values(\'' . $nopp . '\',\'' . $kd_org . '\',\'' . $tanggal . '\',\'' . $user_id . '\',\'' . $catatan . '\')';
		exit('Error:' . $strx);

		if (mysql_query($strx)) {
			echo '';
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}

	break;

case 'delete_temp':
	$strx = 'delete from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp2 . '\'';

	if (mysql_query($strx)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert_persetujuan':
	$sql = 'SELECT * FROM ' . $dbname . '.`log_prapoht` WHERE `nopp`=\'' . $nopp . '\' ';

	#exit(mysql_error());
	($query = mysql_query($sql)) ;
	$rest = mysql_fetch_assoc($query);

	if (1 < $rest['close']) {
		echo 'Warning: Status closed, Can\'t update the status';
		exit();
	}
	else if ($rest['hasilpersetujuan1'] < 1) {
		$stat_cls = 1;
		$strx = 'update ' . $dbname . '. log_prapoht set persetujuan1=\'' . $user_id . '\',close=\'' . $stat_cls . '\'  where nopp=\'' . $nopp . '\'';

		if (mysql_query($strx)) {
			$to = getUserEmail($user_id);
			$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);

			if ($_SESSION['language'] == 'EN') {
				$subject = '[Notifikasi] PR Submission for approval, submitted by: ' . $namakaryawan;
				$body = '<html>' . "\r\n" . '                                                     <head>' . "\r\n" . '                                                     <body>' . "\r\n" . '                                                       <dd>Dear Sir/Madam,</dd><br>' . "\r\n" . '                                                       <br>' . "\r\n" . '                                                       Today,  ' . date('d-m-Y') . ',  on behalf of ' . $namakaryawan . ' submit a PR, requesting for your approval. To follow up, please follow the link below.' . "\r\n" . '                                                       <br>' . "\r\n" . '                                                       <br>' . "\r\n" . '                                                       <br>' . "\r\n" . '                                                       Regards,<br>' . "\r\n" . '                                                       eAgro Plantation Management Software.' . "\r\n" . '                                                     </body>' . "\r\n" . '                                                     </head>' . "\r\n" . '                                                   </html>' . "\r\n" . '                                                   ';
			}
			else {
				$subject = '[Notifikasi]Persetujuan PP a/n ' . $namakaryawan;
				$body = '<html>' . "\r\n" . '                                                     <head>' . "\r\n" . '                                                     <body>' . "\r\n" . '                                                       Dengan Hormat,<br>' . "\r\n" . '                                                       <br>' . "\r\n" . '                                                       Pada hari ini, tanggal ' . date('d-m-Y') . ' karyawan a/n : <b>' . $namakaryawan . '</b>, mengajukan Permintaan Pembelian Barang (PR) kepada bapak/ibu dengan No.PR : <b>' . $nopp . '</b>.<br>' . "\r\n" . '<br>Untuk melakukan persetujuan atau menolak PR ini, silahkan login ke dalam aplikasi <b>\'e-Agro Plantation Management Software\'</b> dengan menggunakan Username & Password yang sudah diberikan.<br><br><br>Hormat Kami,<br><b>e-Agro Plantation Management Software.</b>' . "\r\n" . '                                                     </body>' . "\r\n" . '                                                     </head>' . "\r\n" . '                                                   </html>' . "\r\n" . '                                                   ';
			}
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		echo 'Warning: Documents already in the process';
		exit();
	}

	break;

case 'cari_nopp':
	if ($tanggal == '') {
		$strx = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\'';
	}
	else if ($nopp == '') {
		$strx = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\' or tanggal like \'%' . $tanggal . '%\'';
	}
	else {
		$strx = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\' and tanggal = \'' . $tanggal . '\'';
	}

	break;

case 'cek_pembuat_pp':
	$user_id = $_SESSION['standard']['userid'];
	$skry = 'select dibuat from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\'';

	#exit(mysql_error());
	($qkry = mysql_query($skry)) ;
	$rkry = mysql_fetch_assoc($qkry);

	if ($rkry['dibuat'] != $user_id) {
		echo 'warning: Please see your Username';
		exit();
	}

	break;

case 'refresh_data':
	$limit = 50;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;

	if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
		$sCek = 'select bagian from ' . $dbname . '.datakaryawan where karyawanid=\'' . $_SESSION['standard']['userid'] . '\'';

		##exit(mysql_error($conn));
		($qCek = mysql_query($sCek)) ;
		$rCek = mysql_fetch_assoc($qCek);
		if (($rCek['bagian'] == 'PUR') || ($rCek['bagian'] == 'AGR')) {
			$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_pol_ht order by tanggal desc';
			$str = 'select * from ' . $dbname . '.log_pol_ht order by tanggal desc limit ' . $offset . ',' . $limit . ' ';
		}
		else {
			$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_pol_ht where create_by=\'' . $_SESSION['standard']['userid'] . '\' order by tanggal desc';
			$str = 'select * from ' . $dbname . '.log_pol_ht where  create_by=\'' . $_SESSION['standard']['userid'] . '\' order by tanggal desc limit ' . $offset . ',' . $limit . ' ';
		}
	}
	else {
		$sql = 'select count(*) jmlhrow from ' . $dbname . '.log_pol_ht where substring(nopl,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' and create_by=\'' . $_SESSION['standard']['userid'] . '\'';
		// $str = 'select * from ' . $dbname . '.log_pol_ht where substring(nopl,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' and create_by=\'' . $_SESSION['standard']['userid'] . '\' order by tanggal desc limit ' . $offset . ',' . $limit . '';
		$str = 'select * from ' . $dbname . '.log_pol_ht order by tanggal desc limit ' . $offset . ',' . $limit . ' ';
	}


	#exit(mysql_error());
	($query = mysql_query($sql)) ;

	while ($jsl = mysql_fetch_object($query)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_assoc($res)) {
			$koderorg = substr($bar['nopl'], 15, 4);
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) ;
			$bas = mysql_fetch_assoc($rep);
			$skry = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['create_by'] . '\'';

			#exit(mysql_error());
			($qkry = mysql_query($skry)) ;
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
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'getDetailPP':
	echo '<script language="javascript" src="js/log_pp.js"></script>';
	echo '<script language="javascript" src="js/log_pp.js"></script>';
	echo '<div style=\'width:750px;overflow:scroll;\'>' . "\r\n" . '                    <table border=0 cellspacing=1 class=sortable width=1200px>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr><td>' . $_SESSION['lang']['tanggal'] . ' POL</td><td>' . $_SESSION['lang']['dbuat_oleh'] . '</td>';
	echo '<td>Status</td>';
	echo '<td>Catatan</td>';

	echo '</tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody>';
	$sPP = 'select * from ' . $dbname . '.log_pol_ht where nopl=\'' . $nopp . '\'';

	#exit(mysql_error($conn));
	($qPP = mysql_query($sPP)) ;

	while ($bar = mysql_fetch_assoc($qPP)) {
		$sql = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['create_by'] . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) ;
		$ret = mysql_fetch_assoc($query);
		echo '<tr class=rowcontent><td>' . tanggalnormal($bar['tanggal']) . '</td><td>' . $ret['namakaryawan'] . '</td><td>' . $bar['status'] . '</td><td>' . $bar['catatan'] . '</td>';
		


		echo '</tr>';
	}

	echo "\r\n" . '                </tbody>' . "\r\n" . '                </table>' . "\r\n" . '                <br />' . "\r\n" . '                ';
	echo '<table border=0 cellspacing=1 class=sortable width=1200px><thead>';
	echo '<tr><td>No</td>
	<td>Kode Barang</td>
	<td>'.$_SESSION['lang']['namabarang'].'</td>
	<td>Satuan</td>
	<td>Jumlah</td>
	<td>Harga Satuan</td>
	<td>Keterangan</td>
	</tr>' . "\r\n" . '                </thead>' . "\r\n" . '                ';
	$sdhi = date('Y-m-d');
	$sCek = 'select nopl from ' . $dbname . '.log_pol_dt where nopl=\'' . $nopp . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) ;
	$rCek = mysql_num_rows($qCek);

	if (0 < $rCek) {
		echo "\r\n" . '                <tbody>';
		$sDet = 'select a.*,b.* from ' . $dbname . '.log_pol_dt a left join ' . $dbname . '.log_pol_ht b on a.nopl=b.nopl where a.nopl=\'' . $nopp . '\'';
		$qDet = mysql_query($sDet);
		while ($res = mysql_fetch_assoc($qDet)) {
			$no+=1;
			$sBrg = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) ;
			$rBrg = mysql_fetch_assoc($qBrg);
			
			echo '<tr class=rowcontent>' . "\r\n" . 
			'<td>' . $no . '</td>' . "\r\n" . 
			'<td>' . $res['kodebarang'] . '</td>' . "\r\n" . 
			'<td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . 
			'<td>' . $rBrg['satuan'] . '</td>' . "\r\n" . 
			'<td>' . $res['jumlah'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . 
			'<td>' . $res['hargasatuan'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . 
			'<td>' . $res['keterangan'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . 
			'</tr>';
		}

		echo '</tbody></table></div><br />';
		echo '<div id=dtFormDetail style="overflow:auto; width:500px;height:150px;">';
		echo '</div>';
	}
	else {
		echo '<tbody><tr><td colspan=10>Not Found</td></tr></tbody></table>';
	}

	break;

case 'getAnggaran':
	$tab .= '<fieldset style=width:400px;><legend>Detail ' . $optNm[substr($_POST['kdBarang'], 0, 3)] . '</legend>' . "\r\n" . '                        <table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr><td>' . $optNm[substr($_POST['kdBarang'], 0, 3)] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td><td>' . $_SESSION['lang']['budget'] . '</td><td>' . $_SESSION['lang']['sisa'] . '</td></tr></thead><tbody>';
	$sData = 'select sum(jumlah) as jmlh,kodebarang from ' . $dbname . '.bgt_budget_detail where kodebarang like \'' . substr($_POST['kdBarang'], 0, 3) . '%\'' . "\r\n" . '                        and tahunbudget=\'' . $_POST['thnAnggaran'] . '\' and kodeorg like \'' . $_POST['unit'] . '\' group by kodebarang';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) ;
	$row = mysql_num_rows($qData);

	if ($row == 0) {
		$tab .= '<tr class=rowcontent><td colspan=4>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}
	else {
		while ($rData = mysql_fetch_assoc($qData)) {
			$sSdhi = 'select sum(jumlahpesan) as sdhi from ' . $dbname . '. log_po_vw' . "\r\n" . '                                where nopp like \'%' . $_POST['unit'] . '%\' and kodebarang=\'' . $rData['kodebarang'] . '\'' . "\r\n" . '                                and substr(tanggal,1,4)=\'' . $_POST['thnAnggaran'] . '\'';

			#exit(mysql_error($conn));
			($qSdhi = mysql_query($sSdhi)) ;
			$rSdhi = mysql_fetch_assoc($qSdhi);
			$sisaData = $rData['jmlh'] - $rSdhi['sdhi'];
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $optNmBrg[$rData['kodebarang']] . '</td>';
			$tab .= '<td align=right>' . number_format($rSdhi['sdhi'], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($rData['jmlh'], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($sisaData, 2) . '</td></tr>';
		}
	}

	$tab .= '</tbody></table></fieldset>';
	echo $tab;
	break;

case 'cariBarangDlmDtBs':
	$txtfind = $_POST['txtfind'];
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' or kodebarang like \'%' . $txtfind . '%\' ';

	if ($res = mysql_query($str)) {
		echo "\r\n" . '          <fieldset>' . "\r\n" . '        <legend>Result</legend>' . "\r\n" . '        <div style="overflow:auto; height:300px;" >' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                 <tr class=rowheader>' . "\r\n" . '                                 <td class=firsttd>' . "\r\n" . '                                 No.' . "\r\n" . '                                 </td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                                 </tr>' . "\r\n" . '                                 </thead>' . "\r\n" . '                                 <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$saldoqty = 0;
			$str1 = 'select sum(saldoqty) as saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n" . '                                       and kodeorg=\'' . $_SESSION['empl']['kodeorganisasi'] . '\'';
			$res1 = mysql_query($str1);

			while ($bar1 = mysql_fetch_object($res1)) {
				$saldoqty = $bar1->saldoqty;
			}

			$qtynotpostedin = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                                           and a.tipetransaksi<5' . "\r\n" . '                                           and a.post=0' . "\r\n" . '                                           group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotpostedin = $bar2->jumlah;
			}

			if ($qtynotpostedin == '') {
				$qtynotpostedin = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                                           and a.tipetransaksi>4' . "\r\n" . '                                           and a.post=0' . "\r\n" . '                                           group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotposted = $bar2->jumlah;
			}

			if ($qtynotposted == '') {
				$qtynotposted = 0;
			}

			$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;

			if ($bar->inactive == 1) {
				echo '<tr bgcolor=\'red\' style=\'cursor:pointer;\'  title=\'Inactive\' >';
				$bar->namabarang = $bar->namabarang . ' [Inactive]';
				$bgr = ' bgcolor=\'red\'';
			}
			else {
				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setBrg(\'' . htmlspecialchars($bar->kodebarang, ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($bar->namabarang, ENT_QUOTES, 'UTF-8') . '\',\'' . htmlspecialchars($bar->satuan, ENT_QUOTES, 'UTF-8') . '\')"; title=\'Click\' >';
			}

			echo ' <td class=firsttd >' . $no . '</td>' . "\r\n" . '                                          <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                                          <td>' . $bar->namabarang . '</td>' . "\r\n" . '                                          <td>' . $bar->satuan . '</td>' . "\r\n" . '                                          <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n" . '                                         </tr>';
		}

		echo '</tbody>' . "\r\n" . '                                  <tfoot>' . "\r\n" . '                                  </tfoot>' . "\r\n" . '                                  </table></div></fieldset>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'formPersetujuan':
	if ($nopp == '') {
		$nopp = $_POST['nopp'];
	}
	$ptpengguna = $_POST['kodeorg'];
	$tanggal = date("d-m-Y");
	$gudang = substr($nopp, 15, 4);
	$kodeJurnal = 'INVM1';
	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', tanggalsystem($tanggal)).'/'.substr($gudang, 0, 4).'/'.$kodeJurnal.'/'.$konter;
	
	$str = 'select * from ' . $dbname . '.log_pol_dt where nopl=\'' . $nopp . '\'';
	($qry = mysql_query($str)) ;
	
	$noUrut = 1;
	$Total = 0;
	while ($rkry = mysql_fetch_assoc($qry)) {
		$klbarang = substr($rkry['kodebarang'], 0, 3);
		$str = 'select noakun from '.$dbname.".log_5klbarang where kode='".$klbarang."'";
		$res = mysql_query($str);
		$akunbarang = '';
		while ($bar = mysql_fetch_object($res)) {
			$akunbarang = $bar->noakun;
		}
		if ('' == $akunbarang) {
			exit('Error: Material account not available yet on '.$nopp);
		}
		$str = 'select namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$rkry['kodebarang']."'";
		$res = mysql_query($str);
		while ($bar = mysql_fetch_object($res)) {
			$namabarang = $bar->namabarang;
			$satuan = $bar->satuan;
		}
	
		$keterangan = 'Biaya Pembelian '.$rkry['jumlah'].' '.$satuan.'  '.$namabarang;
		$jumlah = $rkry['jumlah'] * $rkry['hargasatuan'];
		
		$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => $akunbarang, 'keterangan' => $keterangan, 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $rkry['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $nopp, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];		
		++$noUrut;
		$Total+=$jumlah;
	}
	#pre($dataRes['detail']);exit();
	$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => tanggalsystem($tanggal), 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $Total, 'totalkredit' => -1 * $Total, 'amountkoreksi' => '0', 'noreferensi' => $nopp, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
	
	$keterangan = 'Biaya Operasioanl';
	$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($tanggal), 'nourut' => $noUrut, 'noakun' => '713', 'keterangan' => $keterangan, 'jumlah' => -1 * $Total, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($gudang, 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $nopp, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];

	$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
	if (!mysql_query($insHead)) {
		$headErr .= 'Insert Header Error : '.$insHead."\n";
	}

	if ('' == $headErr) {
		$detailErr = '';
		foreach ($dataRes['detail'] as $row) {
			$insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
			if (!mysql_query($insDet)) {
				$detailErr .= 'Insert Detail Error : '.addslashes(mysql_error($conn))."\n";

				break;
			}
		}
		if ('' == $detailErr) {
			$updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."'");
			if (!mysql_query($updJurnal)) {
				echo 'Update Kode Jurnal Error : '.addslashes(mysql_error($conn))."\n";
				$RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
				if (!mysql_query($RBDet)) {
					echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
					exit();
				}

				exit();
			} else {
				$updNopl = updateQuery($dbname, 'log_pol_ht', ['status' => 'Posting'], "nopl='".$nopp."'");
				mysql_query($updNopl);
			}

		} else {
			echo $detailErr;
			$RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
			if (!mysql_query($RBDet)) {
				echo 'Rollback Delete Header Error : '.addslashes(mysql_error($conn))."\n";
				exit();
			}
		}
	} else {
		echo $headErr;
		exit();
	}
	break;
}

?>
