<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$method = $_POST['method'];
$nopo = $_POST['nopo'];
$user_id = $_SESSION['standard']['userid'];
$rlse_user_id = $_POST['id_user'];
$this_date = date('Y-m-d');
$tglR = $_POST['tglR'];
$ket = $_POST['ket'];
$texkKrsi = $_POST['texkKrsi'];

switch ($method) {
case 'approve_po':
//useridapprove
//tglapprovepo

	$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);
	
	// if (($res['persetujuan1'] != '') || ($res['persetujuan2'] != '')) {
	if (($res['persetujuan1'] != '') || ($res['persetujuan2'] != '0000000000')) {
		// if (((int)$res['stat_release'] == 0) && ((int)$res['useridreleasae'] == 0)) {
		if (((int)$res['stat_release'] == 0) && ($res['useridreleasae'] == '0000000000')) {
			$unopo = "update $dbname.log_poht set useridapprovepo='" . $rlse_user_id . "', tglapprovepo='" . $this_date . "',wktapprovepo=current_time where nopo='" . $nopo . "' ";
			//echoMessage('po ',$unopo);
			#exit(mysql_error());
			($qnopo = mysql_query($unopo)) || true;
		}
		else {
			echo 'warning:PO Sudah Di Approve';
			exit();
		}
	}
	else {
		exit('Error: Belum Ada Penanda Tangan Dari P0 ' . $nopo . '');
	}

	break;


case 'release_po':
	$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);
	if (($res['persetujuan1'] != '') || ($res['persetujuan2'] != '')) {
		if (($res['stat_release'] == 0) && ($res['useridreleasae'] == 0)) {
			$unopo = 'update ' . $dbname . '.log_poht set stat_release=\'1\', useridreleasae=\'' . $rlse_user_id . '\',tglrelease=\'' . $this_date . '\', wktrelease=current_time where nopo=\'' . $nopo . '\' ';

			#exit(mysql_error());
			($qnopo = mysql_query($unopo)) || true;
		}
		else {
			echo 'warning:PO Sudah Di Release atau sedang koreksi';
			exit();
		}
	}
	else {
		exit('Error: Belum Ada Penanda Tangan Dari P0 ' . $nopo . '');
	}

	break;

case 'un_release_po':
	$sql = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);

	if (($res['stat_release'] == 1) && ($res['useridreleasae'] == $rlse_user_id) && ($res['tglrelease'] == $this_date)) {
		$unopo = 'update ' . $dbname . '.log_poht set stat_release=null, useridreleasae=null,tglrelease=null,useridapprovepo=null,tglapprovepo=null where nopo=\'' . $nopo . '\' ';
		
		#exit(mysql_error());
		($qnopo = mysql_query($unopo)) || true;
	}
	else {
		echo 'warning:You Don`t Have Autorize to Unrelease This PO No. ' . $nopo;
		exit();
	}

	break;

case 'list_new_data_release_po':

	$flag_need_approve_po = "Y"; //kalo perlu approval PO, kalo nggak perlu set jadi N ajah
	$sCek = 'select sum(jumlahpesan-jumlahterima) as selisih,kodebarang,nopo from ' . $dbname . '.log_po_terima_vw' . "\n" . '                   where right(nopo,3)=\'' . $_SESSION['org']['kodeorganisasi'] . '\' group by nopo,kodebarang order by nopo asc';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;

	while ($rCek = mysql_fetch_assoc($qCek)) {
		if ($nomoPo != $rCek['nopo']) {
			$nomoPo = $rCek['nopo'];
			$sJmlhBrg = 'select count(kodebarang) as jmlbrg from ' . $dbname . '.log_podt where nopo=\'' . $rCek['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qJmlBrg = mysql_query($sJmlhBrg)) || true;
			$rJmlBrg = mysql_fetch_assoc($qJmlBrg);
			$totBrg[$nomoPo] = $rJmlBrg['jmlbrg'];
		}

		if ($rCek['selisih'] == 0) {
			$brgCompr += $rCek['nopo'];
		}
	}

	$add = '';

	if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$add = ' and lokalpusat=1 ';
	}

	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$sql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_poht where statuspo>1 ' . $add . '  ORDER BY tanggal DESC';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	//$str = 'SELECT * FROM ' . $dbname . '.log_poht where statuspo>1 ' . $add . '   ORDER BY tanggal DESC limit ' . $offset . ',' . $limit . ' ';
	$str = 'SELECT * FROM ' . $dbname . '.log_poht where kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' and statuspo>1 ' . $add . '   ORDER BY tanggal DESC limit ' . $offset . ',' . $limit . ' ';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_assoc($res)) {
			$this_date = date('Y-m-d');
			$kodeorg = $bar['kodeorg'];
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $kodeorg . '\' or induk=\'' . $kodeorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_object($rep);
			$no += 1;
			echo '<tr id=\'tr_' . $no . '\' ' . ($bar['stat_release'] == 2 ? 'bgcolor=\'orange\'' : 'class=rowcontent') . '  >' . "\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\n\t\t\t\t" . '  <td id=td_' . $no . '>' . $bar['nopo'] . '</td>' . "\n\t\t\t\t" . '  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\n\t\t\t\t" . '  <td align=center>' . $kodeorg . '</td>';
			$sKrsi = 'select catatanrelease from ' . $dbname . '.log_poht where nopo=\'' . $bar['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qKrsi = mysql_query($sKrsi)) || true;
			$rKrasi = mysql_fetch_assoc($qKrsi);
			$sql = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['persetujuan1'] . '\'';

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$yrs = mysql_fetch_assoc($query);
			$disbtn = 'disabled';
			$need_approval = "N";
			if( $flag_need_approve_po == "Y" && $bar['tglapprovepo'] == NULL){
				$need_approval = "Y";
			}
			

			if ($bar['closed'] == '0') {
				$disbtn = '';
			}

			if ($brgCompr[$bar['nopo']] != 0) {
				if ($brgCompr[$bar['nopo']] == $totBrg[$bar['nopo']]) {
					$disbtn = 'disabled';
				}
			}

			if ($_SESSION['empl']['tipelokasitugas'] != 'KANWIL') {
				if ($rKrasi['catatanrelease'] != '') {
					$isi = ' disabled';
				}
				else {
					$isi = '';
				}

				if (($bar['stat_release'] != 1) || ($bar['stat_release'] == '')) {
					//echo '<td align=left>' . $yrs['namakaryawan'] . '</td>' . "\n\t\t\t\t\t\t" . ' <td align=center valign="middle" onclick="undisable(' . $no . ')" ><input type=text class=myinputtext style=widht:150px maxlength=150 id=krksiText_' . $no . ' name=krksiText_' . $no . ' value=\'' . $rKrasi['catatanrelease'] . '\' ' . $isi . ' /> ' . "\n" . '                                                 <button class="mybutton" id=btnSave_' . $no . ' name=btnSave_' . $no . ' onclick="saveKoreksi(' . $no . ')" ' . $isi . ' )"  >' . $_SESSION['lang']['save'] . '</button></td>   ' . "\n" . '                                                 <td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
					echo '<td align=left>' . $yrs['namakaryawan'] . '</td>' . "\n\t\t\t\t\t\t" . ' <td align=center valign="middle" onclick="undisable(' . $no . ')" ><input type=text class=myinputtext style=widht:150px maxlength=150 id=krksiText_' . $no . ' name=krksiText_' . $no . ' value=\'' . $rKrasi['catatanrelease'] . '\' ' . $isi . ' /> ' . "\n" . '                                                 <button class="mybutton" id=btnSave_' . $no . ' name=btnSave_' . $no . ' onclick="saveKoreksi(' . $no . ')" ' . $isi . ' )"  >' . $_SESSION['lang']['save'] . '</button></td>   ' . "\n" . '                                                 <td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_log_po\',event);"></td>';
				}
				else if ($bar['stat_release'] == 1) {
					//echo '<td align=center>' . $yrs['namakaryawan'] . '</td><td >&nbsp;</td><td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
					echo '<td align=center>' . $yrs['namakaryawan'] . '</td><td >&nbsp;</td><td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_log_po\',event);"></td>';
				}
			}
			else {
				echo '<td align=left colspan=2>' . $yrs['namakaryawan'] . '</td>';
				//echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
				echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_log_po\',event);"></td>';
			}

			if (1 < $bar['statuspo']) {
				if (($bar['stat_release'] == '1') || ($bar['useridreleasae'] != '0000000000')) {
					if ($bar['tglrelease'] != '') {
						$bar['tglrelease'] = tanggalnormal($bar['tglrelease']);
					}

					$disbled = '<td >'.$bar['tglapprovepo'].'&nbsp;</td>';
					$disbled .= '<td align=center>' . $bar['tglrelease'] . '</td>';
				}
				else {
					if( $need_approval == "Y"){
						$disbled = '<td ><button class=mybutton onclick="approve_po(\'' . $bar['nopo'] . '\')" ' . $disbtn . ' >Approve PO</button></td>';
						$disbled .= '<td>&nbsp;</td>';
					}else{
						$disbled = '<td >'.$bar['tglapprovepo'].'&nbsp;</td>';
						if( $bar['tglapprovepo'] != null && $user_id == $bar['useridapprovepo'] && $bar['stat_release'] == null ){							
							$disbled .= '<td><button class=mybutton onclick="alert(\'' . $bar['nopo'] . '\')" disabled >' . $_SESSION['lang']['release_po'] . '</button>&nbsp;<!--<img src=images/onebit_33.png class=resicon  title=\'' . $_SESSION['lang']['ditolak'] . '\' onclick="get_data_po(\'' . $bar['nopo'] . '\');" style="vertical-align:middle;">--></td>';
						}else{
							if( $bar['tglrelease'] == null && $bar['stat_release'] == null ){
								$disbled .= '<td><button class=mybutton onclick="release_po(\'' . $bar['nopo'] . '\')" ' . $disbtn . ' >' . $_SESSION['lang']['release_po'] . '</button>&nbsp;<!--<img src=images/onebit_33.png class=resicon  title=\'' . $_SESSION['lang']['ditolak'] . '\' onclick="get_data_po(\'' . $bar['nopo'] . '\');" style="vertical-align:middle;">--></td>';
							}else{
								$disbled .= '<td>'.$bar['tglrelease'].'</td>';
							}
							
						}
						
					}
					
					
				}
				
				/*
				if (($bar['stat_release'] == '0') && ($bar['useridreleasae'] == '0000000000')) {				
					$disbled2 = '<td align=center>' . $_SESSION['lang']['un_release_po'] . '</td>';
				}
				else if ($bar['tglrelease'] == $this_date) {
					if( $need_approval == "Y"){
						$disbled2 = '<td>&nbsp;</td>';
					}else{
						$disbled2 = '<td><button class=mybutton onclick="un_release_po(\'' . $bar['nopo'] . '\',\'' . $this_date . '\') " ' . $disbtn . '>' . $_SESSION['lang']['un_release_po'] . '</button></td>';
					}					
				}
				else {
					if( $need_approval == "Y"){
						$disbled2 = '<td>&nbsp;</td>';
					}else{						
						$disbled2 = '<td><button class=mybutton onclick="un_release_po(\'' . $bar['nopo'] . '\',\'' . $this_date . '\') " >' . $_SESSION['lang']['un_release_po'] . '</button></td>';
					}
					
				}
				*/
				if( $need_approval == "Y"){
					if( $bar['tglapprovepo'] == null && $bar['tglrelease'] == null ){
						$disbled2 = '<td>&nbsp;</td>';
					}else{
						$disbled2 = '<td><button class=mybutton onclick="un_release_po(\'' . $bar['nopo'] . '\',\'' . $this_date . '\') " >' . $_SESSION['lang']['un_release_po'] . '</button></td>';
					}
				}else{
					$disbled2 = '<td><button class=mybutton onclick="un_release_po(\'' . $bar['nopo'] . '\',\'' . $this_date . '\') " >' . $_SESSION['lang']['un_release_po'] . '</button></td>';
				}

				$disbled2 .= '<td><button class=mybutton  ' . $disbtn . ' onclick=closeedPo(\'' . $_SESSION['lang']['tutup'] . '\',\'' . $bar['nopo'] . '\',event)>' . $_SESSION['lang']['tutup'] . '</button></td>';
				echo "\t\t\t\t\t";
				echo $disbled;
				echo $disbled2;
				echo "\t\t\t\t" . ' ';
			}
			else {
				echo "\t\t\t\t" . ' <td colspan="2" align="center">';
				echo $_SESSION['lang']['wait_approval'];
				echo '</td>' . "\n\t\t\t\t" . ' ' . "\n\t\t\t\t" . ' ';
			}

			echo '</tr><input type=hidden id=nopo_' . $no . ' name=nopo_' . $no . ' value=\'' . $bar['nopo'] . '\' />';
		}

		echo ' <tr><td colspan=9 align=center>' . "\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\n\t\t\t\t" . '<button class=mybutton onclick=cariBast2(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\n\t\t\t\t" . '<button class=mybutton onclick=cariBast2(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\n\t\t\t\t" . '</td>' . "\n\t\t\t\t" . '</tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'cari_rpo':
	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$add = '';

	if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$add = ' and lokalpusat=1 ';
	}
	
	$txt_search = '';
	$txt_tgl = '';
	if (isset($_POST['txtSearchrpo']) || isset($_POST['tglCarirpo'])) {
		$txt_search = $_POST['txtSearchrpo'];
		$txt_tgl = tanggalsystem($_POST['tglCarirpo']);
		$txt_tgl_a = substr($txt_tgl, 0, 4);
		$txt_tgl_b = substr($txt_tgl, 4, 2);
		$txt_tgl_c = substr($txt_tgl, 6, 2);
		if( $txt_tgl_a != "" && $txt_tgl_b != "" && $txt_tgl_c != ""){
			$txt_tgl = $txt_tgl_a . '-' . $txt_tgl_b . '-' . $txt_tgl_c;
		}
		
	}
	

	$where = '';
	if ($txt_search != '') {
		$where .= ' and nopo LIKE  \'%' . $txt_search . '%\' ';
	}
	if ($txt_tgl != '') {
		$where .= ' and tanggal LIKE \'%' . $txt_tgl . '%\' ';
	}
	if ($_POST['typepo'] == 'NORMAL') {
		$where .= ' and po_lokal = \'N\' ';
	}elseif($_POST['typepo'] == 'LOKAL'){
		$where .= ' and po_lokal = \'Y\' ';
	}
	

	$strx = 'select * from ' . $dbname . '.log_poht where statuspo>1 ' . $add . '  ' . $where . 'order by tanggal desc';
	$sql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_poht where statuspo>1 ' . $add . '  ' . $where . 'order by tanggal desc';
	
	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	if ($res = mysql_query($strx)) {
		$numrows = mysql_num_rows($res);

		if ($numrows < 1) {
			echo '<tr class=rowcontent><td colspan=9>Not Found</td></tr>';
		}
		else {
			while ($bar = mysql_fetch_assoc($res)) {
				$kodeorg = $bar['kodeorg'];
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$sKrsi = 'select catatanrelease from ' . $dbname . '.log_poht where nopo=\'' . $bar['nopo'] . '\'';

				#exit(mysql_error($conn));
				($qKrsi = mysql_query($sKrsi)) || true;
				$rKrasi = mysql_fetch_assoc($qKrsi);
				$sql = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['persetujuan1'] . '\'';

				#exit(mysql_error());
				($query = mysql_query($sql)) || true;
				$yrs = mysql_fetch_assoc($query);
				$no += 1;
				echo '<tr id=\'tr_' . $no . '\' ' . ($bar['stat_release'] == 2 ? 'bgcolor=\'orange\'' : 'class=rowcontent') . ' >' . "\n\t\t\t\t\t\t" . '<td>' . $no . '</td>' . "\n\t\t\t\t\t\t" . '<td id=td_' . $no . '>' . $bar['nopo'] . '</td>' . "\n\t\t\t\t\t\t" . '<td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\n\t\t\t\t\t\t" . '<td>' . $kodeorg . '</td>';
				$disbtn = 'disabled';

				if ($bar['closed'] == '0') {
					$disbtn = '';
				}

				if ($rKrasi['catatanrelease'] != '') {
					$isi = ' disabled';
				}
				else {
					$isi = '';
				}

				if ($_SESSION['empl']['tipelokasitugas'] != 'KANWIL') {
					if ($rKrasi['catatanrelease'] != '') {
						$isi = ' disabled';
					}
					else {
						$isi = '';
					}

					if (($bar['stat_release'] != 1) || ($bar['stat_release'] == '')) {
						echo '<td align=left>' . $yrs['namakaryawan'] . '</td>' . "\n" . '                                                     <td align=center valign="middle" onclick="undisable(' . $no . ')" ><input type=text class=myinputtext style=widht:150px maxlength=150 id=krksiText_' . $no . ' name=krksiText_' . $no . ' value=\'' . $rKrasi['catatanrelease'] . '\' ' . $isi . ' /> ' . "\n" . '                                                     <button class="mybutton" id=btnSave_' . $no . ' name=btnSave_' . $no . ' onclick="saveKoreksi(' . $no . ')" ' . $isi . ' )"  >' . $_SESSION['lang']['save'] . '</button></td>   ' . "\n" . '                                                     <td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
					}
					else if ($bar['stat_release'] == 1) {
						echo '<td align=center>' . $yrs['namakaryawan'] . '</td><td >&nbsp;</td><td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
					}
				}
				else {
					echo '<td align=left colspan=2>' . $yrs['namakaryawan'] . '</td>';
					echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_poht\',\'' . $bar['nopo'] . '\',\'\',\'log_slave_print_detail_po\',event);"></td>';
				}

				if (1 < $bar['statuspo']) {
					if (($bar['stat_release'] == '1') || ($bar['useridreleasae'] != '0000000000')) {
						if ($bar['tglrelease'] != '') {
							$bar['tglrelease'] = tanggalnormal($bar['tglrelease']);
						}

						$disbled = '<td align=center>' . $bar['tglrelease'] . '</td>';
					}
					else {
						$disbled = '<td><button class=mybutton onclick="release_po(\'' . $bar['nopo'] . '\')" ' . $disbtn . ' >' . $_SESSION['lang']['release_po'] . '</button></td>';
					}

					if (($bar['stat_release'] == '0') && ($bar['useridreleasae'] == '0000000000')) {
						$disbled2 = '<td>' . $_SESSION['lang']['un_release_po'] . '</td>';
					}
					else if ($bar['tglrelease'] == $this_date) {
						$disbled2 = '<td><button class=mybutton onclick="un_release_po(\'' . $bar['nopo'] . '\',\'' . $this_date . '\') "  ' . $disbtn . ' >' . $_SESSION['lang']['un_release_po'] . '</button></td>';
					}
					else {
						$disbled2 = '<td><button class=mybutton disabled ">' . $_SESSION['lang']['un_release_po'] . '</button></td>';
					}

					$disbled2 .= '<td><button class=mybutton  ' . $disbtn . ' onclick=closeedPo(\'' . $_SESSION['lang']['tutup'] . '\',\'' . $bar['nopo'] . '\',event)>' . $_SESSION['lang']['tutup'] . '</button></td>';
					echo "\t\t\t\t\t";
					echo $disbled;
					echo $disbled2;
					echo "\t\t\t\t" . ' ';
				}
				else {
					echo "\t\t\t\t" . ' <td colspan="2" align="center">';
					echo $_SESSION['lang']['wait_approval'];
					echo '</td>' . "\n\t\t\t\t" . ' ' . "\n\t\t\t\t" . ' ';
				}

				echo '</tr><input type=hidden id=nopo_' . $no . ' name=nopo_' . $no . ' value=\'' . $bar['nopo'] . '\' />';
			}

			echo ' <tr><td colspan=9 align=center>' . "\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\n\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\n\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\n\t\t\t\t" . '</td>' . "\n\t\t\t\t" . '</tr>';
		}
	}
	else {
		echo 'Gagal,' . mysql_error($conn);
	}

	break;

case 'getFormTolak':
	echo '<br /><div id=rejected_form>' . "\n\t\t" . '<fieldset>' . "\n\t\t" . '<legend><input type=text readonly=readonly name=rnopo id=rnopo value=' . $nopo . ' class=myinputtext  style="width:150px;" maxlength="50" /></legend>' . "\n\t\t" . '<table cellspacing=1 border=0>' . "\n\t\t" . '<tr>' . "\n\t\t" . '<td colspan=3>' . "\n\t\t" . 'Apakah Anda Akan Menolak No.PO Di Atas </td></tr>' . "\n\t\t" . '<tr><td>' . $_SESSION['lang']['keterangan'] . '</td><td>:</td><td><input type=text class=myinputtext onkeypress="return tanpa_kutip(event)" id=ket name=ket style="width:150px;" /></td></tr>' . "\n\t\t" . '<tr><td colspan=3 align=center>' . "\n\t\t" . '<button class=mybutton onclick=tolakPo() >' . $_SESSION['lang']['yes'] . '</button>' . "\n\t\t" . '<button class=mybutton onclick=cancel_po() >' . $_SESSION['lang']['no'] . '</button>' . "\n\t\t" . '</td></tr></table>' . "\n\t\t\n\t\t" . '</fieldset>' . "\n\t\t" . '</div>' . "\n\t\t" . '<input type=hidden name=method id=method  /> ' . "\n\t\t" . '<input type=hidden name=user_id id=user_id value=' . $user_id . ' />' . "\n\t\t" . '<input type=hidden name=nopo id=nopo value=' . $nopo . '  />' . "\n\t\t";
	break;

case 'tolakPo':
	if ($ket == '') {
		echo 'warning:Keterangan Tidak Boleh Kosong';
		exit();
	}

	$sUp = 'update ' . $dbname . '.log_poht set hasilpersetujuan2=\'2\',persetujuan2=\'' . $user_id . '\',tglp2=\'' . $this_date . '\',keterangan=\'' . $ket . '\',stat_release=\'1\', useridreleasae=\'' . $user_id . '\',tglrelease=\'' . $this_date . '\', tanggal=\'' . $this_date . '\' where nopo=\'' . $nopo . '\'';

	if ($res = mysql_query($sUp)) {
		echo '';
	}
	else {
		echo $sUp . 'Gagal,' . mysql_error($conn);
	}

	break;

case 'insertKoreksi':
	$sUpd = 'update ' . $dbname . '.log_poht set catatanrelease=\'' . $texkKrsi . '\',stat_release=\'2\' where nopo=\'' . $nopo . '\'';

	if (!mysql_query($sUpd)) {
		echo $sUpd . 'Gagal,' . mysql_error($conn);
	}

	break;

case 'closeForm':
	$aarpil = array('Total Close', 'Close Become outstanding');

	foreach ($aarpil as $lstPil => $disPil) {
		$optPil .= '<option value=\'' . $lstPil . '\'>' . $disPil . '</option>';
	}

	$tab .= '<script language=JavaScript1.2 src=js/generic.js></script>' . "\n" . '                           <script type="text/javascript" src="js/log_release_po.js"></script>';
	$tab .= '<link rel=stylesheet type=text/css href=style/generic.css>';
	$tab .= '<fieldset><legend>' . $_SESSION['lang']['form'] . '</legend><table cellpadding=1 cellspacing=1>';
	$tab .= '<tr><td>' . $_SESSION['lang']['pilih'] . '</td><td><select id=pilId style=width:150px>' . $optPil . '</select></td></tr>';
	$tab .= '<tr><td>' . $_SESSION['lang']['keterangan'] . '</td><td><input type=text id=ketClose style=width:150px class=myinputtext></td></tr>';
	$tab .= '<tr><td colspan=2><button class=mybutton onclick=tutpDt(\'' . $_POST['nopo'] . '\')>' . $_SESSION['lang']['tutup'] . '</button></td></tr></table></fieldset>';
	echo $tab;
	break;

case 'tutupData':
	if ($_POST['pilDt'] == 0) {
		if ($_POST['ketClose'] == '') {
			exit('error: ' . $_SESSION['lang']['keterangan'] . ' can\'t empty');
		}

		$sdata = 'select kodebarang,nopp,jumlahpesan from ' . $dbname . '.log_podt where nopo=\'' . $_POST['nopo'] . '\'';

		#exit(mysql_error($conn));
		($qdata = mysql_query($sdata)) || true;

		while ($rdata = mysql_fetch_assoc($qdata)) {
			$sup = 'update ' . $dbname . '.log_prapodt set status=1,ditolakoleh=\'' . $_SESSION['standard']['userid'] . '\',alasanstatus=\'' . $_POST['ketClose'] . '\'' . "\n" . '                                  where nopp=\'' . $rdata['nopp'] . '\' and kodebarang=\'' . $rdata['kodebarang'] . '\'';

			if (!mysql_query($sup)) {
				exit('error:db error ' . mysql_error($conn) . '___' . $sup);
			}
		}

		$supdate = 'update ' . $dbname . '.log_poht set closed=1,keterangan=\'' . $_POST['ketClose'] . '\',updateby=\'' . $_SESSION['standard']['userid'] . '\' where nopo=\'' . $_POST['nopo'] . '\'';

		if (!mysql_query($supdate)) {
			exit('error:db error ' . mysql_error($conn) . '___' . $supdate);
		}
	}
	else {
		$sdata = 'select kodebarang,nopp,jumlahpesan from ' . $dbname . '.log_podt where nopo=\'' . $_POST['nopo'] . '\'';

		#exit(mysql_error($conn));
		($qdata = mysql_query($sdata)) || true;

		while ($rdata = mysql_fetch_assoc($qdata)) {
			$sjmlhgdng = 'select distinct sum(jumlah) as jmlh from ' . $dbname . '.log_transaksi_vw ' . "\n" . '                                        where nopo=\'' . $_POST['nopo'] . '\' and kodebarang=\'' . $rdata['kodebarang'] . '\' and tipetransaksi=1';

			#exit(mysql_error($conn));
			($qjmlhgdng = mysql_query($sjmlhgdng)) || true;
			$rjmlgdng = mysql_fetch_assoc($qjmlhgdng);
			if (($rjmlgdng['jmlh'] == '') || (intval($rjmlgdng['jmlh']) == 0)) {
				$rjmlgdng['jmlh'] = $rdata['jumlahpesan'];
			}

			$supdate = 'update ' . $dbname . '.log_podt set jmlhstlhclose=\'' . $rdata['jumlahpesan'] . '\',jumlahpesan=\'' . ($rdata['jumlahpesan'] - $rjmlgdng['jmlh']) . '\'' . "\n" . '                                     where nopo=\'' . $_POST['nopo'] . '\' and kodebarang=\'' . $rdata['kodebarang'] . '\'';

			if (!mysql_query($supdate)) {
				exit('error:db error ' . mysql_error($conn) . '___' . $supdate);
			}
		}

		$supdateht = 'update ' . $dbname . '.log_poht set closed=1,keterangan=\'' . $_POST['ketClose'] . '\',updateby=\'' . $_SESSION['standard']['userid'] . '\' where nopo=\'' . $_POST['nopo'] . '\'';

		if (!mysql_query($supdateht)) {
			exit('error:db error ' . mysql_error($conn) . '___' . $supdateht);
		}
	}

	break;
}

?>
