<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';

if (isset($_GET['method'])) {
	$method = $_GET['method'];
	$statPP = $_GET['statPP'];
}
else {
	$method = $_POST['method'];
}

$nopp = $_POST['nopp'];
$jmlh_realisai = $_POST['jmlh_realisai'];
$lokal = $_POST['lokal'];
$purchaser = $_POST['purchase'];
$kd_brng = $_POST['kdbrg'];
$kdBrgBaru = $_POST['kdBrgBaru'];
$_POST['statPP'] == '' ? $statPP = $_GET['statPP'] : $statPP = $_POST['statPP'];
$_POST['userid'] == '' ? $userid = $_GET['userid'] : $userid = $_POST['userid'];
$cm_hasil = $_POST['cm_hasil'];
$spr2 = 'select namabarang,kodebarang,satuan from ' . $dbname . '.log_5masterbarang order by namabarang asc';

#exit(mysql_error());
($rep2 = mysql_query($spr2)) || true;

while ($bas2 = mysql_fetch_object($rep2)) {
	$rDtBrg[$bas2->kodebarang] = $bas2->namabarang;
	$nmSatuan[$bas2->kodebarang] = $bas2->satuan;
}

$kolom = $_POST['kolom'];
$comment = $_POST['comment'];
$kode_brg = $_POST['kd_brg'];
$alsnDtolak = $_POST['alsnDtolk'];
$periode = $_POST['periode'];
$kodeorg = $_POST['kodeorg'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$tglHrini = date('Ymd');
$blmVer = $_POST['blmVer'];
$_POST['unitIdCr'] == '' ? $unitIdCr = $_GET['unitIdCr'] : $unitIdCr = $_POST['unitIdCr'];
$_POST['klmpKbrg'] == '' ? $klmpKbrg = $_GET['klmpKbrg'] : $klmpKbrg = $_POST['klmpKbrg'];
$_POST['kdBarangCari'] == '' ? $kdBarangCari = $_GET['kdBarangCari'] : $kdBarangCari = $_POST['kdBarangCari'];
$nmBrg = $_POST['nmBrg'];
$tglSdt = tanggalsystem($_POST['tglSdt']);

switch ($method) {
case 'insert_detail_pp':
	if ($jmlh_realisai == 0) {
		echo 'Warning: Realization must greater than 0 ' . $jmlh_realisai . '';
		exit();
	}
	else {
		$sql = 'select * from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\' and status!=\'3\'';

		if (mysql_query($sql)) {
			$query = mysql_query($sql);

			if ($res = mysql_fetch_assoc($query)) {
				if ($res['$purchaser'] == '0000000000') {
					$sql2 = 'update ' . $dbname . '.log_prapodt set purchaser=\'' . $purchaser . '\',lokalpusat=\'' . $lokal . '\',realisasi=\'' . $jmlh_realisai . '\',tglAlokasi=\'' . $tglHrini . '\' where kodebarang=\'' . $kd_brng . '\' and nopp=\'' . $nopp . '\'';

					#exit(mysql_error());
					($query2 = mysql_query($sql2)) || true;
					break;
				}

				$sCek = 'select distinct jumlahpesan from ' . $dbname . '.log_podt where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kd_brng . '\'';

				#exit(mysql_error());
				($qCek = mysql_query($sCek)) || true;
				$rCek = mysql_fetch_assoc($qCek);

				if ($jmlh_realisai < $rCek['jumlahpesan']) {
					exit('Error: Realization less than requested');
				}

				$sql2 = 'update ' . $dbname . '.log_prapodt set purchaser=\'' . $purchaser . '\',lokalpusat=\'' . $lokal . '\',realisasi=\'' . $jmlh_realisai . '\',tglAlokasi=\'' . $tglHrini . '\' where kodebarang=\'' . $kd_brng . '\' and nopp=\'' . $nopp . '\'';

				#exit(mysql_error());
				($query2 = mysql_query($sql2)) || true;
				break;
			}
		}
		else {
			echo $sql;
			echo ' Gagal,' . addslashes(mysql_error($conn));
			exit();
		}
	}

	break;

case 'cari_pp':
	echo ' <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n" . '         <td>Advance Action</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['chat'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jmlh_disetujui'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '         <td>O.std</td>' . "\r\n\r\n" . '         <td colspan=\'3\' align="center">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
	$limit = 25;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$txt_search = $_POST['txtSearch'];
	$txtCari = $_POST['txtCari'];

	if (($txt_search == '') && ($txt_tgls == '')) {
		$where = ' ';
	}

	if ($txt_search != '') {
		$where .= 'and b.nopp LIKE  \'%' . $txt_search . '%\'   ';
	}

	if ($_POST['tglCari'] != '') {
		$where .= ' and a.tanggal LIKE \'' . $_POST['tglCari'] . '%\'';
	}

	if ($userid != '') {
		$where .= ' and purchaser=\'' . $userid . '\'';
	}

	if ($unitIdCr != '') {
		$where .= ' and b.nopp like \'%' . $unitIdCr . '%\'';
	}

	if (($klmpKbrg != '') && ($kdBarangCari == '')) {
		$where .= ' and substr(kodebarang,1,3)=\'' . $klmpKbrg . '\'';
	}

	if ($kdBarangCari != '') {
		$where .= ' and kodebarang=\'' . $kdBarangCari . '\'';
	}

	if ($statPP == 1) {
		$strx = 'SELECT  distinct a.tanggal, a.close,b.*  FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                                WHERE a.close = \'2\' and b.status=\'0\' and create_po!=\'0\' ' . $where . '  ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc limit ' . $offset . ',' . $limit . ' ';
		$sql = 'SELECT  distinct  a.tanggal,  a.close, b.* FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                                WHERE a.close = \'2\' and b.status=\'0\' and create_po!=\'0\' ' . $where . '   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc ';
	}
	else if ($statPP == 0) {
		$strx = 'SELECT distinct  a.tanggal,  a.close, b.*  FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                                WHERE a.close = \'2\' and b.status=\'0\'  and create_po=\'0\'  ' . $where . '   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc  limit ' . $offset . ',' . $limit . ' ';
		$sql = 'SELECT distinct  a.tanggal,  a.close, b.* FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp  ' . "\r\n" . '                                WHERE a.close = \'2\' and b.status=\'0\' and create_po=\'0\' ' . $where . '   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc';
	}
	else if ($statPP == 2) {
		$strx = 'SELECT   distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*  ' . "\r\n" . '                               FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                               WHERE a.close = \'2\' and b.status=\'0\' and b.create_po=\'0\'  ' . $where . '   ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc limit ' . $offset . ',' . $limit . '';
		$sql = 'SELECT   distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*  ' . "\r\n" . '                               FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                               WHERE a.close = \'2\' and b.status=\'0\' and b.create_po=\'0\'   ' . $where . '  ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc ';
	}

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$jsl = mysql_num_rows($query);
	$jlhbrs = $jsl;

	if ($res = mysql_query($strx)) {
		$row = mysql_num_rows($res);

		if ($row != 0) {
			while ($bar = mysql_fetch_object($res)) {
				$koderorg = substr($bar->nopp, 15, 4);
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$spr2 = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';

				#exit(mysql_error());
				($rep2 = mysql_query($spr2)) || true;
				$bas2 = mysql_fetch_object($rep2);
				$no += 1;
				$sPoDet = 'select nopo from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

				#exit(mysql_error());
				($qPoDet = mysql_query($sPoDet)) || true;
				$rCek = mysql_num_rows($qPoDet);

				if (0 < $rCek) {
					$rPoDet = mysql_fetch_assoc($qPoDet);
					$sPo = 'select tanggal,stat_release from ' . $dbname . '.log_poht where nopo=\'' . $rPoDet['nopo'] . '\'';

					#exit(mysql_error());
					($qPo = mysql_query($sPo)) || true;
					$rPo = mysql_fetch_assoc($qPo);
					$stat = $rPo['stat_release'];
					$nopo = $rPoDet['nopo'];
				}
				else {
					$tglPP = explode('-', $bar->tanggal);
					$date1 = $tglPP[2];
					$month1 = $tglPP[1];
					$year1 = $tglPP[0];
					$tgl2 = date('Y-m-d');
					$pecah2 = explode('-', $tgl2);
					$date2 = $pecah2[2];
					$month2 = $pecah2[1];
					$year2 = $pecah2[0];
					$stat = 0;
				}

				$sPoDetHrg = 'select distinct hargasatuan from ' . $dbname . '.log_podt where  kodebarang=\'' . $bar->kodebarang . '\' order by nopo desc';

				#exit(mysql_error());
				($qPoDetHrg = mysql_query($sPoDetHrg)) || true;
				$rCekHrg = mysql_fetch_assoc($qPoDetHrg);
				$jd1 = GregorianToJD($month1, $date1, $year1);
				$jd2 = GregorianToJD($month2, $date2, $year2);
				$jmlHari = $jd2 - $jd1;
				$optPur = '<option value=\'\'></option>';
				$klq = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where  (bagian LIKE \'%PROC%\' or bagian LIKE \'%PURCH%\') and tanggalkeluar is NULL order by namakaryawan asc ';

				#exit(mysql_error());
				($qry = mysql_query($klq)) || true;

				while ($rst = mysql_fetch_object($qry)) {
					if ($bar->purchaser == $rst->karyawanid) {
						$optPur .= '<option value=' . $rst->karyawanid . ' selected>' . $rst->namakaryawan . '</option>';
					}
					else {
						$optPur .= '<option value=' . $rst->karyawanid . '>' . $rst->namakaryawan . '</option>';
					}
				}

				if ($bar->lokalpusat != 0) {
					$ckh = 'checked=checked';
				}
				else {
					$ckh = '';
				}

				if ($bar->purchaser != '0000000000') {
					$read_only2 = 'disabled=disabled';
					$ckh .= ' disabled=disabled';
				}

				$optLokasi = '';
				$cl = array('Head Office', 'Local');

				foreach ($cl as $rw => $isi) {
					$optLokasi .= '<option \'' . ($bar->lokalpusat == $rw ? 'selected=selected' : '') . '\'value=\'' . $rw . '\'>' . $isi . '</option>';
				}

				$strChat = 'select *  from ' . $dbname . '.log_pp_chat where ' . "\r\n" . '                              kodebarang=\'' . $bar->kodebarang . '\' and nopp=\'' . $bar->nopp . '\'';
				$resChat = mysql_query($strChat);

				if (0 < mysql_num_rows($resChat)) {
					$ingChat = '<img src=\'images/chat1.png\' onclick="loadPPChat(\'' . $bar->nopp . '\',\'' . $bar->kodebarang . '\',event);" class=resicon>';
				}
				else {
					$ingChat = '<img src=\'images/chat0.png\'  onclick="loadPPChat(\'' . $bar->nopp . '\',\'' . $bar->kodebarang . '\',event);" class=resicon>';
				}

				echo '<tr class=rowcontent id=\'tr_' . $no . '\' title=\'' . $_SESSION['lang']['tgldibutuhkan'] . ':' . tanggalnormal($bar->tgl_sdt) . '\'>' . "\r\n" . '                              <td>' . $no . '</td>' . "\r\n" . '                              <td >' . $koderorg . '</td>' . "\r\n" . '                              <td id=nopp_' . $no . '  onclick="getDataPP(\'' . $bar->nopp . '\')" style="cursor:pointer">' . $bar->nopp . '</td>' . "\r\n" . '              <td id=kd_brg_' . $no . '>' . $bar->kodebarang . '</td>' . "\r\n" . '                              <td>' . substr($bas2->namabarang, 0, 33) . '</td>' . "\r\n" . '                              <td align=right>' . number_format($rCekHrg['hargasatuan'], 2) . '</td>';

				if ($stat != 1) {
					echo '<td align="center">';
					if (($_SESSION['empl']['kodejabatan'] == '50') || ($_SESSION['empl']['kodejabatan'] == '51') || ($_SESSION['empl']['kodejabatan'] == '52') || ($_SESSION['empl']['kodejabatan'] == '53') || ($_SESSION['empl']['kodejabatan'] == '54') || ($_SESSION['empl']['kodejabatan'] == '55') || ($_SESSION['empl']['kodejabatan'] == '56') || ($_SESSION['empl']['kodejabatan'] == '57') || ($_SESSION['empl']['kodejabatan'] == '58') || ($_SESSION['empl']['kodejabatan'] == '59') || ($_SESSION['empl']['kodejabatan'] == '60')) {
						echo "\r\n" . '                              <img src=images/application/application_add.png class=resicon title=\'Additional Material\' onclick="getDataPP5(\'' . $bar->nopp . '\')" />&nbsp;';
					}

					echo "\r\n" . '                              <img src=images/application/application_edit.png class=resicon  title=\'Replace material code\' onclick="searchBrg(\'' . $no . '\',\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=notrans_' . $no . ' name=notrans_' . $no . ' value=' . $bar->nopp . '><input type=hidden id=kdbrg_' . $no . ' name=kdbrg_' . $no . '  value=' . $bar->kodebarang . '><input type=hidden id=nomor name=nomor  value=' . $no . '>\',event);">&nbsp;' . "\r\n" . '                              <img src=images/application/application_go.png class=resicon title=\'Submission-re verify\' onclick="ajukanForm(\'' . $bar->nopp . '\')" /></td>';
				}
				else {
					echo '<td>' . $_SESSION['lang']['release_po'] . '</td>';
				}

				echo '<td>' . $ingChat . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\r\n" . '                             <td align=center id=jumlahawal_' . $no . '>' . $bar->jumlah . '</td>' . "\r\n" . '                              ' . "\r\n\t\t\t\t\t\t\t" . '  <td align=right><input type=text id=realisasi_' . $no . ' name=realisasi_' . $no . ' onkeypress=\'return angka_doang(event)\' class=\'myinputtextnumber\' ' . $read_only2 . ' value=' . $bar->realisasi . ' style=\'width:60px;\' /></td>' . "\r\n" . '                              ' . "\r\n\t\t\t\t\t\t\t" . '  ' . "\r\n\t\t\t\t\t\t\t" . '  ' . "\r\n\t\t\t\t\t\t\t" . '  <td><select id=purchase_name_' . $no . ' id=purchase_name_' . $no . ' ' . $read_only2 . '>' . $optPur . '</select></td>';

				if ($stat == 1) {
					if (($_SESSION['empl']['kodejabatan'] == '50') || ($_SESSION['empl']['kodejabatan'] == '51') || ($_SESSION['empl']['kodejabatan'] == '52') || ($_SESSION['empl']['kodejabatan'] == '53') || ($_SESSION['empl']['kodejabatan'] == '54') || ($_SESSION['empl']['kodejabatan'] == '55') || ($_SESSION['empl']['kodejabatan'] == '56') || ($_SESSION['empl']['kodejabatan'] == '57') || ($_SESSION['empl']['kodejabatan'] == '58') || ($_SESSION['empl']['kodejabatan'] == '59') || ($_SESSION['empl']['kodejabatan'] == '60')) {
						echo "\r\n" . '                                              <td align=center><input type=checkbox id=lokalpusat_' . $no . '  ' . $ckh . ' /> Local' . "\r\n" . '                                              </td>';
						echo "\r\n" . '                                                <td align=center title="Selisih Tanggal PP dengan Tanggal Hari ini" >' . $jmlHari . '</td>' . "\r\n" . '                                              <td ' . $stat_view . '><img src=images/save.png class=resicon  title=\'Save\' onclick="AddPur(\'' . $no . '\');"></td>' . "\r\n" . '                                              <td align=center><img src=images/application/application_edit.png class=resicon  title=\'Edit Data\' onclick="EditPur(\'' . $no . '\');"></td><td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
					}
					else {
						echo '<td align=left colspan=5>' . $nopo . '</td>';
					}
				}
				else {
					echo "\r\n" . '                                              <td align=center><input type=checkbox id=lokalpusat_' . $no . '  ' . $ckh . ' /> Local' . "\r\n" . '                                              </td>';
					echo "\r\n" . '                                                <td align=center title="Selisih Tanggal PP dengan Tanggal Hari ini" >' . $jmlHari . '</td>' . "\r\n" . '                                              <td ' . $stat_view . '><img src=images/save.png class=resicon  title=\'Save\' onclick="AddPur(\'' . $no . '\');"></td>' . "\r\n" . '                                              <td align=center><img src=images/application/application_edit.png class=resicon  title=\'Edit Data\' onclick="EditPur(\'' . $no . '\');"></td><td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
				}

				echo '</tr>';
			}

			echo '<tr><td colspan=14 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '           <br>' . "\r\n" . '       <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '           <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '           </td>' . "\r\n" . '           </tr>';
		}
		else {
			echo '<tr class=rowcontent><td colspan=16>Not Found</td></tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo ' </tbody>' . "\r\n" . '         </table><input type=\'hidden\' id=\'halPage\' name=\'halPage\' value=\'' . $page . '\' />';
	break;

case 'refresh_data':
	echo ' <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td>Advance Action</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['chat'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td>Jumlah Disetujui</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '         <td>O.std</td>' . "\r\n" . '         <td colspan=\'3\' align="center">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
	$thnSkrng = date('Y');
	$limit = 25;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}
	
	//
	$txt_search = $_POST['txtSearch'];
	$txtCari = $_POST['txtCari'];
	
	$where = ' ';
	if (($txt_search == '') && ($txt_tgls == '')) {
		$where = ' ';
	}

	if ($txt_search != '') {
		$where .= 'and b.nopp LIKE  \'%' . $txt_search . '%\'   ';
	}

	if ($_POST['tglCari'] != '') {
		$where .= ' and a.tanggal LIKE \'' . $_POST['tglCari'] . '%\'';
	}

	if ($userid != '') {
		$where .= ' and purchaser=\'' . $userid . '\'';
	}

	if ($unitIdCr != '') {
		$where .= ' and b.nopp like \'%' . $unitIdCr . '%\'';
	}

	if (($klmpKbrg != '') && ($kdBarangCari == '')) {
		$where .= ' and substr(kodebarang,1,3)=\'' . $klmpKbrg . '\'';
	}

	if ($kdBarangCari != '') {
		$where .= ' and kodebarang=\'' . $kdBarangCari . '\'';
	}
	// kondisi harus sesuai jumlah persetujuan
	$where .= " and (";
	$where .= " if(jumlahpemberipersetujuan=1 and hasilpersetujuan1=1,1,0)  = 1 ";
	$where .= " or if(jumlahpemberipersetujuan=2 and hasilpersetujuan2=1,1,0)  = 1 ";
	$where .= " or if(jumlahpemberipersetujuan=3 and hasilpersetujuan3=1,1,0)  = 1 ";
	$where .= " or if(jumlahpemberipersetujuan=4 and hasilpersetujuan4=1,1,0)  = 1 ";
	$where .= " or if(jumlahpemberipersetujuan=5 and hasilpersetujuan5=1,1,0)  = 1 ";
	$where .= " ) ";
	
	//$wherestatus = " and b.status IN ('0','3')"; // MPS
	$wherestatus = " and b.status='0' "; // WPG - Request by Herna 20200511-FA

	$offset = $page * $limit;
	//$sql = 'select count(*) as jmlhrow FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '               WHERE a.close >= \'3\' and b.status=\'0\' and b.create_po=\'0\' and substr(tanggal,1,4)=\'' . $thnSkrng . '\' and SUBSTR(b.nopp, 16, 3) like \''.$_SESSION['empl']['kodeorganisasi'].'%\'  ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc';
	$sql = 'select count(*) as jmlhrow FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . ' WHERE a.close >= \'3\' and b.status=\'0\' and b.create_po=\'0\' and SUBSTR(b.nopp, 16, 3) like \''.$_SESSION['empl']['kodeorganisasi'].'%\' '.$where.' ORDER BY purchaser asc,a.tglp5,a.tglp4,a.tglp3,a.tglp2,a.tglp1 desc';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;

	while ($jsl = mysql_fetch_object($query)) {
		$jlhbrs = $jsl->jmlhrow;
	}
/*
	$str = "SELECT  a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, 
	a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*  
	FROM $dbname.log_prapodt b  
	LEFT JOIN  $dbname.log_prapoht a ON a.nopp = b.nopp
	WHERE a.close >= '3' and b.status='0' and b.create_po='0' and substr(tanggal,1,4)='" . $thnSkrng . "' and SUBSTR(b.nopp, 16, 3) like '".$_SESSION['empl']['kodeorganisasi']."%'  ";	
	$str .= " ORDER BY nopp desc limit $offset,$limit";
	*/
	$str = "SELECT  a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, 
	a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*,
    case when c.jml_asal is null then b.jumlah else c.jml_asal end as jumlah_c,	
    case when c.jml_approve is null and b.jml_approve ='0' then b.jumlah 
		when c.jml_approve is null and b.jml_approve > '0'  then b.jml_approve
	else b.jml_approve end as jml_approve_c,b.status	
	FROM $dbname.log_prapodt b  
	LEFT JOIN  $dbname.log_prapoht a ON a.nopp = b.nopp
	left join $dbname.log_prapodt_vw as c on (b.nopp=c.nopp and b.kodebarang=c.kodebarang)
	WHERE a.close >= '3' and b.create_po='0' and SUBSTR(b.nopp, 16, 3) like '".$_SESSION['empl']['kodeorganisasi']."%'  ".$where;
	$str .= $wherestatus;
	$str .= " ORDER BY status asc,a.tanggal desc,nopp desc limit $offset,$limit";
	//echo $str;
	//echoMessage('str ',$str);
	if (mysql_query($str)) {
		$res = mysql_query($str);
		$total = mysql_num_rows($res);
		echo '<tr><td colspan=16>Total Items: ' . $jlhbrs . '</td></tr>';

		while ($bar = mysql_fetch_object($res)) {
			$koderorg = substr($bar->nopp, 15, 4);
			$no += 1;
			$sPoDet = 'select nopo from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

			#exit(mysql_error());
			($qPoDet = mysql_query($sPoDet)) || true;
			$rCek = mysql_num_rows($qPoDet);

			if (0 < $rCek) {
				$rPoDet = mysql_fetch_assoc($qPoDet);
				$sPo = 'select tanggal,stat_release from ' . $dbname . '.log_poht where nopo=\'' . $rPoDet['nopo'] . '\'';

				#exit(mysql_error());
				($qPo = mysql_query($sPo)) || true;
				$rPo = mysql_fetch_assoc($qPo);
				$stat = $rPo['stat_release'];
				$nopo = $rPoDet['nopo'];
			}
			else {
				$tglPP = explode('-', $bar->tanggal);
				$date1 = $tglPP[2];
				$month1 = $tglPP[1];
				$year1 = $tglPP[0];
				$tgl2 = date('Y-m-d');
				$pecah2 = explode('-', $tgl2);
				$date2 = $pecah2[2];
				$month2 = $pecah2[1];
				$year2 = $pecah2[0];
				$stat = 0;
			}

			$jd1 = GregorianToJD($month1, $date1, $year1);
			$jd2 = GregorianToJD($month2, $date2, $year2);
			$jmlHari = $jd2 - $jd1;
			$optPur = '<option value=\'\'></option>';

			$klq = "select karyawanid,namakaryawan,kodeorganisasi from $dbname.datakaryawan where  bagian IN ('HO_PUR','HO_PROC') 
			and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00') and kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."%' order by namakaryawan asc ";

			#exit(mysql_error());
			($qry = mysql_query($klq)) || true;
			$optPur='';
			while ($rst = mysql_fetch_object($qry)) {
				if ($bar->purchaser == $rst->karyawanid) {
					$optPur .= '<option value=' . $rst->karyawanid . ' selected>' . $rst->namakaryawan . ' ['.$rst->kodeorganisasi.']</option>';
				}
				else {
					$optPur .= '<option value=' . $rst->karyawanid . '>' . $rst->namakaryawan . ' ['.$rst->kodeorganisasi.']</option>';
				}
			}

			if ($bar->lokalpusat != 0) {
				$ckh = 'checked=checked';
			}
			else {
				$ckh = '';
			}
			
			//set status
			if($bar->status == '3'){
				$status_tolak = "Y";
				$class_row = 'bgcolor="#D3D3D3"';
			}else{
				$status_tolak = "N";
				$class_row = 'class=rowcontent'; 
			}

			if ($bar->purchaser != '0000000000') {
				$read_only2 = 'disabled=disabled';
				$ckh .= ' disabled=disabled';
			}else{
				if($status_tolak == "Y"){
					$read_only2 = 'disabled=disabled';
					$ckh .= ' disabled=disabled'; 
				}else{
					$read_only2 = '';
				}
			}

			$optLokasi = '';
			$cl = array('Head Office', 'Local');

			foreach ($cl as $rw => $isi) {
				$optLokasi .= '<option \'' . ($bar->lokalpusat == $rw ? 'selected=selected' : '') . '\'value=\'' . $rw . '\'>' . $isi . '</option>';
			}

			$strChat = 'select *  from ' . $dbname . '.log_pp_chat where ' . "\r\n" . '                              kodebarang=\'' . $bar->kodebarang . '\' and nopp=\'' . $bar->nopp . '\'';
			$resChat = mysql_query($strChat);

			if (0 < mysql_num_rows($resChat)) {
				$ingChat = '<img src=\'images/chat1.png\' onclick="loadPPChat(\'' . $bar->nopp . '\',\'' . $bar->kodebarang . '\',event);" class=resicon>';
			}
			else {
				$ingChat = '<img src=\'images/chat0.png\'  onclick="loadPPChat(\'' . $bar->nopp . '\',\'' . $bar->kodebarang . '\',event);" class=resicon>';
			}
			
			

			echo '<tr '.$class_row.'  id=\'tr_' . $no . '\' title=\'' . $_SESSION['lang']['tgldibutuhkan'] . ':' . tanggalnormal($bar->tgl_sdt) . '\'>' . "\r\n" . '                              <td>' . $no . '</td>' . "\r\n" . '                              <td >' . $koderorg . '</td>' . "\r\n" . '                              <td id=nopp_' . $no . ' onclick="getDataPP(\'' . $bar->nopp . '\')" style="cursor:pointer">' . $bar->nopp . '</td>' . "\r\n" . '                              <td id=kd_brg_' . $no . '>' . $bar->kodebarang . '</td>' . "\r\n" . '                              <td>' . substr($rDtBrg[$bar->kodebarang], 0, 33) . '</td>';

			if ($stat != 1) {
				echo '<td align="center">';
				if (($_SESSION['empl']['kodejabatan'] == '50') || ($_SESSION['empl']['kodejabatan'] == '51') || ($_SESSION['empl']['kodejabatan'] == '52') || ($_SESSION['empl']['kodejabatan'] == '53') || ($_SESSION['empl']['kodejabatan'] == '54') || ($_SESSION['empl']['kodejabatan'] == '55') || ($_SESSION['empl']['kodejabatan'] == '56') || ($_SESSION['empl']['kodejabatan'] == '57') || ($_SESSION['empl']['kodejabatan'] == '58') || ($_SESSION['empl']['kodejabatan'] == '59') || ($_SESSION['empl']['kodejabatan'] == '60')) {
					echo "\r\n" . '                              <img src=images/application/application_add.png class=resicon title=\'Additional material\' onclick="getDataPP5(\'' . $bar->nopp . '\')" />&nbsp;';
				}
				
				if( $status_tolak == "Y" ){
					echo "\r\n" . '                              &nbsp;';
					echo '&nbsp;</td>';
				}else{
					echo "\r\n" . '                              <img src=images/application/application_edit.png class=resicon  title=\'Change material code\' onclick="searchBrg(\'' . $no . '\',\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=notrans_' . $no . ' name=notrans_' . $no . ' value=' . $bar->nopp . '><input type=hidden id=kdbrg_' . $no . ' name=kdbrg_' . $no . '  value=' . $bar->kodebarang . '><input type=hidden id=nomor name=nomor  value=' . $no . '>\',event);">';
					echo '&nbsp;' . "\r\n" . '                              <img src=images/application/application_go.png class=resicon title=\'Submission - re Verify\' onclick="ajukanForm(\'' . $bar->nopp . '\')" /></td>';
				}
			}
			else {
				echo '<td>' . $_SESSION['lang']['release_po'] . '</td>';
			}

			echo "\r\n" . '                              <td>' . $ingChat . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\r\n" . '                              <td align=center id=jumlahawal_' . $no . '>' . $bar->jumlah_c . '</td>';
			$flag_realisasi_nol = "N";
			//if (($bar->realisasi == '0') || ($bar->realisasi == '')) {			
			if (($bar->jml_approve_c == '0') || ($bar->jml_approve_c == '' ) ) {	
				$read_only2 = 'disabled=disabled';
				//echo "\r\n\t\t\t\t\t\t\t" . '  <td align=right>' . "\r\n" . '                              ' . "\r\n\t\t\t\t\t\t\t" . '  ' . "\t" . '<input type=text id=realisasi_' . $no . ' name=realisasi_' . $no . ' onkeypress=\'return angka_doang(event)\' class=\'myinputtextnumber\'  value=' .($bar->jml_approve==0?$bar->jumlah_c:$bar->jml_approve) . ' style=\'width:60px;\' />' . "\r\n\t\t\t\t\t\t\t" . '  </td>';
				echo "\r\n\t\t\t\t\t\t\t" . '  <td align=right>' . "\r\n" . '                              ' . "\r\n\t\t\t\t\t\t\t" . '  ' . "\t" . '<input type=text id=realisasi_' . $no . ' name=realisasi_' . $no . ' onkeypress=\'return angka_doang(event)\' class=\'myinputtextnumber\'  value=' .$bar->jml_approve_c . ' style=\'width:60px;\' />' . "\r\n\t\t\t\t\t\t\t" . '  </td>';
				$flag_realisasi_nol = "Y";
			}
			else {
				// echo '<td align=right>' . "\r\n" . '                               ' . "\t" . '<input type=text id=realisasi_' . $no . ' name=realisasi_' . $no . ' onkeypress=\'return angka_doang(event)\' class=\'myinputtextnumber\' ' . $read_only2 . ' value=' . ($bar->jmlapprove==0?$bar->jumlah:$bar->jmlapprove) . ' style=\'width:60px;\' />' . "\r\n\t\t\t\t\t\t\t" . '  </td>';
				echo '<td align=right>' . "\r\n" . '                               ' . "\t" . '<input type=text id=realisasi_' . $no . ' name=realisasi_' . $no . ' onkeypress=\'return angka_doang(event)\' class=\'myinputtextnumber\' ' . $read_only2 . ' value=' .  $bar->jml_approve_c . ' style=\'width:60px;\' />' . "\r\n\t\t\t\t\t\t\t" . '  </td>';
			}
			if( $flag_realisasi_nol == "Y"){
				echo '<td><select id=purchase_name_' . $no . ' id=purchase_name_' . $no . ' ' . $read_only2 . '></select></td>';
			}else{
				echo '<td><select id=purchase_name_' . $no . ' id=purchase_name_' . $no . ' ' . $read_only2 . '>' . $optPur . '</select></td>';
			}

			if ($stat == 1) {
				if (($_SESSION['empl']['kodejabatan'] == '50') || ($_SESSION['empl']['kodejabatan'] == '51') || ($_SESSION['empl']['kodejabatan'] == '52') || ($_SESSION['empl']['kodejabatan'] == '53') || ($_SESSION['empl']['kodejabatan'] == '54') || ($_SESSION['empl']['kodejabatan'] == '55') || ($_SESSION['empl']['kodejabatan'] == '56') || ($_SESSION['empl']['kodejabatan'] == '57') || ($_SESSION['empl']['kodejabatan'] == '58') || ($_SESSION['empl']['kodejabatan'] == '59') || ($_SESSION['empl']['kodejabatan'] == '60')) {
					echo "\r\n" . '                                              <td align=center><input type=checkbox id=lokalpusat_' . $no . '  ' . $ckh . ' /> Local' . "\r\n" . '                                              </td>';
					echo "\r\n" . '                                                <td align=center title="Selisih Tanggal PP dengan Tanggal Hari ini" >' . $jmlHari . '</td>' . "\r\n";
					if( $status_tolak == "Y" ){
						echo '<td ' . $stat_view . '>&nbsp;</td>' . "\r\n" ;
						echo '<td align=center>&nbsp;</td>';
						echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
					}else{
						echo '<td ' . $stat_view . '><img src=images/save.png class=resicon  title=\'Save\' onclick="AddPur(\'' . $no . '\');"></td>' . "\r\n" ;
						echo '<td align=center><img src=images/application/application_edit.png class=resicon  title=\'Edit Data\' onclick="EditPur(\'' . $no . '\');"></td>';
						echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
					}
				}
				else {
					echo '<td align=left colspan=5>' . $nopo . '</td>';
				}
			}
			else {
				echo "\r\n" . '                                              <td align=center><input type=checkbox id=lokalpusat_' . $no . '  ' . $ckh . ' /> Local</td>' . "\r\n" . '                                                <td align=center title="Selisih Tanggal PP dengan Tanggal Hari ini" >' . $jmlHari . '</td>' . "\r\n" ;                                           
				
				if( $status_tolak == "Y" ){
					echo '<td ' . $stat_view . '>&nbsp;</td>' . "\r\n" ;
					echo '<td align=center>&nbsp;</td>';
					echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
				}else{
					echo '<td ' . $stat_view . '><img src=images/save.png class=resicon  title=\'Save\' onclick="AddPur(\'' . $no . '\');"></td>' . "\r\n" ;
					echo '<td align=center><img src=images/application/application_edit.png class=resicon  title=\'Edit Data\' onclick="EditPur(\'' . $no . '\');"></td>';
					echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>';
				}
			}

			echo '</tr>';
		}

		echo '<tr><td colspan=17 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n" . '           <br>' . "\r\n" . '       <button class=mybutton onclick=cariData(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '           <button class=mybutton onclick=cariData(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '           </td>' . "\r\n" . '           </tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo ' </tbody>' . "\r\n" . '         </table><input type=\'hidden\' id=\'halPage\' name=\'halPage\' value=\'' . $page . '\' />';
	break;

case 'cariBarang':
	$txtfind = $_POST['txtfind'];
	$pil = $_POST['pil'];
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' or kodebarang like \'%' . $txtfind . '%\' ';

	if (mysql_query($str)) {
		$res = mysql_query($str);
		echo "\r\n" . '                <fieldset style=float:left;clear:both;>' . "\r\n" . '                <legend>Result</legend>' . "\r\n" . '                <div style="overflow:auto; height:280px;" >' . "\r\n" . '                <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '                <td class=firsttd>' . "\r\n" . '                No.' . "\r\n" . '                </td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                </tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$saldoqty = 0;
			$str1 = 'select sum(saldoqty) as saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n" . '                and kodeorg=\'' . $_SESSION['empl']['kodeorganisasi'] . '\'';
			$res1 = mysql_query($str1);

			while ($bar1 = mysql_fetch_object($res1)) {
				$saldoqty = $bar1->saldoqty;
			}

			$qtynotpostedin = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                and a.tipetransaksi<5' . "\r\n" . '                and a.post=0' . "\r\n" . '                group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotpostedin = $bar2->jumlah;
			}

			if ($qtynotpostedin == '') {
				$qtynotpostedin = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                and a.tipetransaksi>4' . "\r\n" . '                and a.post=0' . "\r\n" . '                group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotposted = $bar2->jumlah;
			}

			if ($qtynotposted == '') {
				$qtynotposted = 0;
			}

			$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;

			if ($bar->inactive == 1) {
				echo '<tr class=rowcontent style=\'cursor:pointer;\'  title=\'Inactive\' >';
				$bar->namabarang = $bar->namabarang . ' [Inactive]';
			}
			else {
				$clikData = '"setBrg(' . $bar->kodebarang . ')"';

				if ($pil == 2) {
					$clikData = '"setBrg2(\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\')"';
				}

				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick=' . $clikData . ' title=\'Click\' >';
			}

			echo ' <td class=firsttd>' . $no . '</td>' . "\r\n" . '                <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                <td>' . $bar->namabarang . '</td>' . "\r\n" . '                <td>' . $bar->satuan . '</td>' . "\r\n" . '                <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n" . '                </tr>';
		}

		echo '</tbody>' . "\r\n" . '                <tfoot>' . "\r\n" . '                </tfoot>' . "\r\n" . '                </table></div></fieldset>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'updateDtbarang':
	$sUpdate = 'update ' . $dbname . '.log_prapodt set kodebarang=\'' . $kdBrgBaru . '\' where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kd_brng . '\'';

	if (mysql_query($sUpdate)) {
		$sCek = 'select kodebarang from ' . $dbname . '.log_podt where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kd_brng . '\'';

		#exit(mysql_error());
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_num_rows($qCek);

		if (0 < $rCek) {
			$sUpdPo = 'update ' . $dbname . '.log_podt set kodebarang=\'' . $kdBrgBaru . '\' where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kd_brng . '\'';

			#exit(mysql_error($conn));
			mysql_query($sUpdPo) || true;
		}
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'excelData':
	$stream .= ' <table border="1">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>No.</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tgldibutuhkan'] . '</td>    ' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlh_disetujui'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlh_hari_outstanding'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
	$txt_search = $_GET['txtSearch'];
	$txtCari = $_GET['txtCari'];
	$txt_tgl = tanggalsystem($_GET['tglCari']);

	if (($txt_search == '') && ($txt_tgls == '')) {
		$where = ' ';
	}

	if ($txt_search != '') {
		$where .= 'and b.nopp LIKE  \'' . $txt_search . '%\'   ';
	}

	if ($_GET['tglCari'] != '') {
		$where .= ' and a.tanggal LIKE \'' . $_GET['tglCari'] . '%\'';
	}

	if ($userid != '') {
		$where .= ' and purchaser=\'' . $userid . '\'';
	}

	if ($unitIdCr != '') {
		$where .= ' and b.nopp like \'%' . $unitIdCr . '%\'';
	}

	if (($klmpKbrg != '') && ($kdBarangCari == '')) {
		$where .= ' and substr(kodebarang,1,3)=\'' . $klmpKbrg . '\'';
	}

	if ($kdBarangCari != '') {
		$where .= ' and kodebarang=\'' . $kdBarangCari . '\'';
	}

	if ($statPP == 0) {
		$where .= ' and purchaser=\'0000000000\'';
	}

	if ($statPP == 1) {
		$strx = 'SELECT  distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*  ' . "\r\n" . '                                   FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                                   WHERE a.close = \'2\' and b.status=\'0\' and create_po!=\'0\' ' . $where . '  ORDER BY a.nopp asc ';
	}
	else if ($statPP == 0) {
		$strx = 'SELECT  distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*   ' . "\r\n" . '                                   FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                                   WHERE a.close = \'2\' and b.status=\'0\'  and create_po=\'0\'  ' . $where . '  ORDER BY a.nopp asc  ';
	}
	else if ($statPP == 2) {
		$strx = 'SELECT   distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*  ' . "\r\n" . '                               FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                               WHERE a.close = \'2\' and b.status=\'0\' and b.create_po=\'0\'  ' . $where . '   ORDER BY a.tanggal ';
	}

	if (mysql_query($strx)) {
		$res = mysql_query($strx);

		while ($bar = mysql_fetch_object($res)) {
			$koderorg = substr($bar->nopp, 15, 4);
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_object($rep);
			$spr2 = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';

			#exit(mysql_error());
			($rep2 = mysql_query($spr2)) || true;
			$bas2 = mysql_fetch_object($rep2);
			$no += 1;
			$sPoDet = 'select distinct nopo from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

			#exit(mysql_error());
			($qPoDet = mysql_query($sPoDet)) || true;
			$rCek = mysql_num_rows($qPoDet);

			if (0 < $rCek) {
				$sPoDet = 'select distinct nopo from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

				#exit(mysql_error());
				($qPoDet = mysql_query($sPoDet)) || true;
				$rPoDet = mysql_fetch_assoc($qPoDet);
				$sPo = 'select tanggal from ' . $dbname . '.log_poht where nopo=\'' . $rPoDet['nopo'] . '\'';

				#exit(mysql_error());
				($qPo = mysql_query($sPo)) || true;
				$rPo = mysql_fetch_assoc($qPo);
				$tglA = substr($rPo['tanggal'], 0, 4);
				$tglB = substr($rPo['tanggal'], 5, 2);
				$tglC = substr($rPo['tanggal'], 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tGl1 = substr($bar->tanggal, 0, 4);
				$tGl2 = substr($bar->tanggal, 5, 2);
				$tGl3 = substr($bar->tanggal, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tgl1 = $tGl1 . $tGl2 . $tGl3;
				$stat = 1;
				$nopo = $rPoDet['nopo'];
			}
			else {
				$tGl1 = substr($bar->tanggal, 0, 4);
				$tGl2 = substr($bar->tanggal, 5, 2);
				$tGl3 = substr($bar->tanggal, 8, 2);
				$tgl1 = $tGl1 . $tGl2 . $tGl3;
				$Tgl2 = date('Y-m-d');
				$tglA = substr($Tgl2, 0, 4);
				$tglB = substr($Tgl2, 5, 2);
				$tglC = substr($Tgl2, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$stat = 0;
			}

			$starttime = strtotime($tgl1);
			$endtime = strtotime($tgl2);
			$timediffSecond = abs($endtime - $starttime);
			$base_year = min(date('Y', $tGl1), date('Y', $tglA));
			$diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
			$jmlHari = date('j', $diff) - 1;
			$klq = 'select namakaryawan from ' . $dbname . '.datakaryawan where  karyawanid=\'' . $bar->purchaser . '\'';

			#exit(mysql_error());
			($qry = mysql_query($klq)) || true;
			$rNm = mysql_fetch_assoc($qry);
			$bar->lokalpusat != 0 ? $chk = 'Local' : $chk = 'Head Office';
			$sPoDetHrg = 'select distinct hargasatuan from ' . $dbname . '.log_podt where  kodebarang=\'' . $bar->kodebarang . '\' order by nopo desc';

			#exit(mysql_error());
			($qPoDetHrg = mysql_query($sPoDetHrg)) || true;
			$rCekHrg = mysql_fetch_assoc($qPoDetHrg);
			$stream .= '<tr>' . "\r\n" . '                              <td>' . $no . '</td>' . "\r\n" . '                              <td>' . $koderorg . '</td>' . "\r\n" . '                              <td>' . $bar->nopp . '</td>' . "\r\n" . '                              <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                              <td>' . substr($bas2->namabarang, 0, 33) . '</td>' . "\r\n" . '                              <td align=right>' . number_format($rCekHrg['hargasatuan'], 2) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tgl_sdt) . '</td>    ' . "\r\n" . '                              <td align=right>' . number_format($bar->jumlah, 2) . '</td>' . "\r\n" . '                              <td align=right>' . number_format($bar->realisasi, 2) . '</td>' . "\r\n" . '                              <td>' . $rNm['namakaryawan'] . '</td> ' . "\r\n" . '                              <td>' . $chk . '</td> <td>' . $jmlHari . '</td>';
			$stream .= '<td align=center>' . $nopo . '</td>';
			$stream .= '</tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	$stream .= ' </tbody>';
	$stream .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'ListVerivikasiBarang_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                            </script>';
	break;

case 'getForm':
	$kolom = 0;
	$sCek = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $_POST['nopp'] . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_assoc($qCek);
	$a = 1;

	while ($a < 6) {
		if ($rCek['persetujuan' . $a] != '') {
			$kolom += 1;
		}
		else {
			$kolom += 1;
			break;
		}

		++$a;
	}

	echo '<br />' . "\t\r\n" . '         <input type="hidden" id=\'kolom\' name=\'kolom\' value=' . $kolom . ' />' . "\r\n" . '         <fieldset><legend>Approval</legend>' . "\r\n" . '            <div id=test style=display:block>' . "\r\n" . '            <fieldset>' . "\r\n" . '            <legend><input type=text readonly=readonly name=rnopp id=rnopp value=' . $_POST['nopp'] . '  /></legend>' . "\r\n" . '            <table cellspacing=1 border=0>' . "\r\n" . '            <tr>' . "\r\n" . '            <td colspan=3>' . "\r\n" . '             Submit to the next verification :</td>' . "\r\n" . '            </tr>' . "\r\n" . '            <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n" . '            <td>:</td>' . "\r\n" . '            <td valign=top>';
	$optPur = '';
	$klq = 'select karyawanid,namakaryawan,lokasitugas,bagian from ' . $dbname . '.datakaryawan where karyawanid!=\'' . $_SESSION['standard']['userid'] . '\' and tipekaryawan=\'5\' and lokasitugas!=\'\' order by namakaryawan asc';

	#exit(mysql_error());
	($qry = mysql_query($klq)) || true;

	while ($rst = mysql_fetch_object($qry)) {
		$sBag = 'select nama from ' . $dbname . '.sdm_5departemen where kode=\'' . $rst->bagian . '\'';

		#exit(mysql_error());
		($qBag = mysql_query($sBag)) || true;
		$rBag = mysql_fetch_assoc($qBag);
		$optPur .= '<option value=\'' . $rst->karyawanid . '\'>' . $rst->namakaryawan . ' [' . $rst->lokasitugas . ']  [' . $rBag['nama'] . ']</option>';
	}

	echo "\r\n\r\n" . '                    <select id=user_id name=user_id  style="width:150px;">' . "\r\n" . '                            ' . $optPur . ' ' . "\r\n" . '                    </select></td></tr>' . "\r\n" . '                    <tr>' . "\r\n" . '                    <tr>' . "\r\n" . '                    <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '                    <td>:</td>' . "\r\n" . '                    <td><input type=text id=comment_fr name=comment_fr class=myinputtext onClick=\'return tanpa_kutip(event)\'  style="width:150px;" /></td>' . "\r\n" . '                    </tr>' . "\r\n" . '                    <td colspan=3 align=center>' . "\r\n" . '                    <button class=mybutton onclick=forwardPP() title="Submit to the next verification" id=Ajukan >' . $_SESSION['lang']['diajukan'] . '</button>' . "\r\n\r\n" . '                    <button class=mybutton onclick=cancel() title="Close this form">' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '                    </td></tr></table><br /> ' . "\r\n" . '                    <input type=hidden name=method id=method  /> ' . "\r\n" . '                    <input type=hidden name=user_id id=user_id value=' . $_SESSION['standard']['userid'] . ' />' . "\r\n" . '                    <input type=hidden name=nopp id=nopp value=' . $_POST['nopp'] . '  /> ' . "\r\n" . '                    </fieldset></div><br />' . "\r\n" . '                    </fieldset><br />' . "\r\n" . '                    ';
	echo '<fieldset>' . "\r\n" . '                    <legend>Rejection</legend>' . "\r\n" . '                    <div id=rejected_form>' . "\r\n" . '                    <fieldset>' . "\r\n" . '                    <legend><input type=text readonly=readonly name=dnopp id=dnopp value=' . $_POST['nopp'] . '  /></legend>' . "\r\n" . '                    <table cellspacing=1 border=0>' . "\r\n" . '                    <tr>' . "\r\n" . '                    <td colspan=3>' . "\r\n" . '                    Rejection Form </td></tr>' . "\r\n" . '                    <tr>' . "\r\n" . '                    <td>' . $_SESSION['lang']['note'] . '</td>' . "\r\n" . '                    <td>:</td>' . "\r\n" . '                    <td><input type=text id=cmnt_tolak name=cmnt_tolak class=myinputtext onClick="return tanpa_kutip(event)" /></td>' . "\r\n" . '                    </tr>' . "\r\n" . '                    <tr><td colspan=3 align=center>' . "\r\n" . '                    <button class=mybutton onclick="rejected_pp_proses()" >' . $_SESSION['lang']['ditolak'] . '</button>' . "\r\n" . '                    <button class=mybutton onclick="rejected_some_proses(\'' . $_POST['nopp'] . '\',\'' . $kolom . '\')" >' . $_SESSION['lang']['ditolak_some'] . '</button>' . "\r\n" . '                    <button class=mybutton onclick=cancel() title="Close this form">' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '                    </td></tr></table>' . "\r\n" . '                    </fieldset>' . "\r\n" . '                    </div>' . "\r\n" . '                    </fieldset>';
	break;

case 'insertFwrdpp':
	$sCek = 'select * from ' . $dbname . '.log_prapoht where nopp=\'' . $nopp . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_assoc($qCek);
	$i = 1;

	while ($i < 6) {
		if ($rCek['persetujuan' . $i] == '') {
			$ar = $i;
			break;
		}

		++$i;
	}

	if ($ar == 5) {
		echo 'warning: No more submission';
		exit();
	}
	else {
		$thisDate = date('d-m-Y');
		$pls = $ar + 1;
		$sUp = 'update ' . $dbname . '.log_prapoht set persetujuan' . $ar . '=\'' . $_SESSION['standard']['userid'] . '\',tglp' . $ar . '=\'' . tanggalsystem($thisDate) . '\',close=\'1\',persetujuan' . $pls . '=\'' . $userid . '\' where nopp=\'' . $nopp . '\'';

		if (mysql_query($sUp)) {
			echo '';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}
	}

	break;

case 'rejected_pp_ex':
	if ($kolom < 6) {
		$tglSkrng = date('Y-m-d');
		$sUpdatePP = 'update ' . $dbname . '.log_prapoht set komentar' . $kolom . '=\'' . $comment . '\',hasilpersetujuan' . $kolom . '=\'3\',tglp' . $kolom . '=\'' . $tglSkrng . '\',persetujuan' . $kolom . '=\'' . $_SESSION['standard']['userid'] . '\' where nopp=\'' . $nopp . '\'';

		if (mysql_query($sUpdatePP)) {
			$sql3 = 'update ' . $dbname . '.log_prapodt set status=\'3\',ditolakoleh=\'' . $_SESSION['standard']['userid'] . '\' where nopp=\'' . $nopp . '\'';

			#exit(mysql_error());
			($query3 = mysql_query($sql3)) || true;
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
			echo $sUpdatePP;
			exit();
		}
	}
	else {
		echo 'warning: Please contact administrator';
		exit();
	}

	break;

case 'get_form_rejected_some': 
	
	//$sql = 'select * from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\'';
	$sql = 'select b.*,case when c.jml_asal is null then b.jumlah else c.jml_asal end as jumlah_c, 
	case when c.jml_approve is null and b.jml_approve =\'0\' then b.jumlah 
		when c.jml_approve is null and b.jml_approve > \'0\'  then b.jml_approve
	else b.jml_approve end as jml_approve_c	
	from ' . $dbname . '.log_prapodt as b left join '.$dbname.'.log_prapodt_vw c on (b.nopp=c.nopp and b.kodebarang=c.kodebarang) where b.nopp=\'' . $nopp . '\'';
	
	
	
	//echo $sql;
	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	echo "\r\n" . '        <fieldset><input type=hidden id=kolom value=' . $kolom . '>' . "\r\n" . '        <legend><input type=text id=rnopp name=rnopp value=' . $nopp . ' readonly=readonly /></legend>' . "\r\n" . '        <div style=overflow:auto;width=850px;height:350px;>' . "\r\n" . '        <table cellspacing=1 border=0 class=sortable>' . "\r\n" . '        <thead class=rowheader>' . "\r\n" . '        <tr>' . "\r\n" . '        <td>No.</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodeanggaran'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tanggalSdt'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['alasanDtolak'] . '</td>' . "\r\n" . '        <td colspan=2>Action</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </thead>' . "\r\n\r\n" . '        <tbody id=reject_some class=rowcontent>' . "\r\n\r\n" . '        ';

	while ($res = mysql_fetch_assoc($query)) {
		$no += 1;
		$sql2 = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;
		$res2 = mysql_fetch_assoc($query2);

		if ($res['status'] == '3') {
			$dis = 'disabled=disabled';
			$bg_color = 'bgcolor="red"';
			$alasan_ditolak = $res['alasanstatus'];
		}
		else {
			$dis = '';
			$bg_color = '';
			$alasan_ditolak = '';
		}

		//echo '<tr>' . "\r\n" . '        <td>' . $no . '</td>' . "\r\n" . '        <td id=kdBrg_' . $no . '>' . $res['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $res2['namabarang'] . '</td>' . "\r\n" . '        <td>' . $res2['satuan'] . '</td>' . "\r\n" . '        <td id=kd_angrn_' . $no . '>' . $res['kd_anggran'] . '</td>' . "\r\n" . '        <td id=jmlh_' . $no . '>' . $res['jumlah'] . '</td>' . "\r\n" . '        <td id=tgl_' . $no . '>' . $res['tgl_sdt'] . '</td>' . "\r\n" . '        <td id=ket_' . $no . '>' . $res['keterangan'] . '</td>' . "\r\n" . '        <td><input type=text id=alsnDtolak_' . $no . ' name=alsnDtolak_' . $no . ' class=myinputtext style=width:100px /></td>' . "\r\n" . '        <td><button class=mybutton onclick="rejected_some(\'' . $nopp . '\',\'' . $no . '\',\'' . $kolom . '\')" ' . $dis . ' >' . $_SESSION['lang']['ditolak'] . '</button></td>' . "\r\n" . '        </tr>';
		
		echo '<tr '.$bg_color.'>' . "\r\n" . '        <td>' . $no . '</td>' . "\r\n" . '        <td id=kdBrg_' . $no . '>' . $res['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $res2['namabarang'] . '</td>' . "\r\n" . '        <td>' . $res2['satuan'] . '</td>' . "\r\n" . '        <td id=kd_angrn_' . $no . '>' . $res['kd_anggran'] . '</td>' . "\r\n" . '        <td id=jmlh_' . $no . '>' . $res['jml_approve_c'] . '</td>' . "\r\n" . '        <td id=tgl_' . $no . '>' . $res['tgl_sdt'] . '</td>' . "\r\n" . '        <td id=ket_' . $no . '>' . $res['keterangan'] . '</td>' . "\r\n" . '        <td><input type=text id=alsnDtolak_' . $no . ' name=alsnDtolak_' . $no . ' class=myinputtext style=width:100px '.$dis.' value="'.$alasan_ditolak.'" /></td>' . "\r\n" . '        <td><button class=mybutton onclick="rejected_some(\'' . $nopp . '\',\'' . $no . '\',\'' . $kolom . '\')" ' . $dis . ' >' . $_SESSION['lang']['ditolak'] . '</button></td>' . "\r\n" . '        </tr>';
		
	}

	echo '</tbody><tfoot><tr><td colspan=10 align=center><button class=mybutton onclick="rejected_some_done()" >' . $_SESSION['lang']['done'] . '</button></td></tr></tfoot></table></div></fieldset><input type=hidden id=user_id name=user_id value=\'' . $_SESSION['standard']['userid'] . '\'>';
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

case 'rejected_some_input':
	$where = ' nopp=\'' . $nopp . '\' and kodebarang=\'' . $kode_brg . '\'';
	$sCek = 'select status from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\' and status=\'0\' ';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if (1 < $rCek) {
		$sql = 'select * from ' . $dbname . '.log_prapodt where' . $where;

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);
		//FA-20190925 : jika ditolak beberapa, status tetap 0, bukan 3
		if (($res['status'] == 0) && ($res['ditolakoleh'] == 0)) {
			$sql2 = 'update ' . $dbname . '.log_prapodt set status=\'0\',ditolakoleh=\'' . $_SESSION['standard']['userid'] . '\',alasanstatus=\'' . $alsnDtolak . '\' where' . $where;

			if ($query2 = mysql_query($sql2)) {
				echo '';
			}
			else {
				echo $sql2;
				exit();
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			echo 'warning: Already exist';
			exit();
		}
	}
	else {
		echo 'warning: this only has one item';
		exit();
	}

	break;
	
case 'rejected_some_input_verifikasi':
	$where = ' nopp=\'' . $nopp . '\' and kodebarang=\'' . $kode_brg . '\'';
	
	$sql = 'select * from ' . $dbname . '.log_prapodt where' . $where;

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);
	//FA-20190925 : jika ditolak beberapa, status tetap 0, bukan 3
	if (($res['status'] == 0) ) {
		$sql2 = 'update ' . $dbname . '.log_prapodt set status=\'3\',ditolakoleh=\'' . $_SESSION['standard']['userid'] . '\',alasanstatus=\'' . $alsnDtolak . '\' where' . $where;

		if ($query2 = mysql_query($sql2)) {
			echo '';
		}
		else {
			echo $sql2;
			exit();
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		echo 'warning: Already exist ('.$sql.')';
		exit();
	}
	
	break;

case 'getSummary':
	if ($periode == '') {
		$periode = date('Y-m');
	}

	$tab .= '<link rel=stylesheet type=text/css href=style/generic.css>' . "\r\n" . '        <script language=javascript1.2 src=\'js/generic.js\'></script>' . "\r\n" . '        <script language=javascript1.2 src=\'js/log_verivikasi.js\'></script>';
	$tab .= '<br /><fieldset><legend>Summarry Purchaser</legend>';
	$tab .= 'Till month : <span id=tglPeriode>' . $periode . '</span><br /><br />';
	$optper = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sPeriode = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_prapoht  order by tanggal desc';

	#exit(mysql_error());
	($qPeriode = mysql_query($sPeriode)) || true;

	while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
		if ($rPeriode['periode'] != '0000-00') {
			$thn = explode('-', $rPeriode['periode']);

			if ($thn[1] == '12') {
				$optper .= '<option value=\'' . substr($rPeriode['periode'], 0, 4) . '\' ' . (substr($rPeriode['periode'], 0, 4) == $periode ? 'selected' : '') . '>' . substr($rPeriode['periode'], 0, 4) . '</option>';
			}

			$optper .= '<option value=\'' . $rPeriode['periode'] . '\' ' . ($rPeriode['periode'] == $periode ? 'selected' : '') . '>' . substr($rPeriode['periode'], 5, 2) . '-' . substr($rPeriode['periode'], 0, 4) . '</option>';
		}
	}

	$tab .= '' . $_SESSION['lang']['periode'] . ':<select id=period name=period onchange=getSumData()>' . $optper . '</select><br />';
	$tab .= '<table border=0 cellspacing=1 cellpading=0><thead>' . "\r\n" . '            <tr class=rowheader>' . "\r\n" . '            <td rowspan=\'2\'  align=center>No.</td>' . "\r\n" . '            <td rowspan=\'2\' align=center>' . $_SESSION['lang']['purchaser'] . '</td>';
	$sPt = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
	$qData = fetchData($sPt);
	$jumlahData = count($qData);
	$a = 1;

	foreach ($qData as $brsData => $rData) {
		$kdOrg[] = $rData;
		$tab .= '<td colspan=4 align=center>' . $rData['kodeorganisasi'] . '</td>';
		++$a;
	}

	$tab .= '<tr class=rowheader>';
	$acd = 0;

	while ($acd < $jumlahData) {
		$tab .= '<td align=center\'>Tot. Item</td><td align=center bgcolor=\'green\'>On PO</td><td align=center bgcolor=\'red\'>Blm PO</td><td align=center>% Out</td>';
		++$acd;
	}

	$tab .= '</tr></thead><tbody id=isiContain>';
	$sPur = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan ' . "\r\n" . '               where bagian LIKE \'%PROC%\'  and kodejabatan!=\'33\' and (tanggalkeluar>\'' . date('Y-m-d') . '\' or tanggalkeluar is NULL)  order by namakaryawan asc';
	#echo $sPur;
	$qPur = fetchData($sPur);

	foreach ($qPur as $brsKary) {
		foreach ($kdOrg as $brsData3 => $rData3) {
			$sDt = ' SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapoht a LEFT JOIN ' . $dbname . '.log_prapodt b ON a.nopp = b.nopp' . "\r\n" . '                         WHERE  b.status=\'0\' and kodeorg=\'' . $rData3['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $periode . '%\' and b.purchaser=\'' . $brsKary['karyawanid'] . '\' ';
			#echo $sDt.';';
			#exit(mysql_error());
			$qDt = mysql_query($sDt);
			$rDt = mysql_fetch_assoc($qDt);
			$sDt2 = ' SELECT kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapoht a LEFT JOIN ' . $dbname . '.log_prapodt b ON a.nopp = b.nopp' . "\r\n" . '                        LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp = c.nopp  ' . "\r\n" . '                        WHERE b.status=\'0\'  and kodeorg=\'' . $rData3['kodeorganisasi'] . '\' and substring(tanggal,1,7) like \'%' . $periode . '%\' and c.nopo!=\'\' and b.purchaser=\'' . $brsKary['karyawanid'] . '\'  group by b.kodebarang  ';
			
			#echo $sDt2.';';
			#exit(mysql_error());
			$qDt2 = mysql_query($sDt2) || exit(mysql_error());
			$jmlhPo2 = mysql_num_rows($qDt2);
			$rDt2 = mysql_fetch_assoc($qDt2);
			$totalPo2[$rDt['purchaser']][$rDt['kodeorg']] += $jmlhPo2;
			$totalPo[$rDt['purchaser']][$rDt['kodeorg']] += $rDt['jmlhPo'];
			$all[$rDt['purchaser']][$rDt['kodeorg']] += $totalPo[$rDt['purchaser']][$rDt['kodeorg']] - $totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
			$tempTotalPo2[$rDt['purchaser']] += $totalPo2[$rDt['purchaser']][$rDt['kodeorg']];
			$sisa[$rDt['purchaser']] += $totalPo[$rDt['purchaser']][$rDt['kodeorg']];
		}

		$DtaAll[] = $brsKary;
	}
	#pre($totalPo2);
	foreach ($DtaAll as $brs) {
		++$no;
		$tab .= '<tr class=rowcontent onclick="detailData(\'' . $brs['karyawanid'] . '\',\'' . $periode . '\')" style="cursor:pointer;">';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $brs['namakaryawan'] . '</td>';

		foreach ($kdOrg as $brsData2 => $rData2) {
			if ($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']] != 0) {
				@$persen5[$brs['karyawanid']][$rData2['kodeorganisasi']] = ($all[$brs['karyawanid']][$rData2['kodeorganisasi']] / $totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']]) * 100;
			}

			$tab .= '<td align=right>' . number_format($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($all[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($persen5[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$totTrbitPO += $rData2['kodeorganisasi'];
			$blmPO += $rData2['kodeorganisasi'];
			$grndTotal += $rData2['kodeorganisasi'];
		}
	}

	$col = $a + 2;
	$sAll = 'select count(*) as jmlh from ' . $dbname . '.log_prapodt where purchaser=\'0000000000\'';

	#exit(mysql_error());
	$qAll = mysql_query($sAll);
	$rAll = mysql_fetch_assoc($qAll);

	if ($totalBlm != 0) {
		@$persenTot = ($totalSemua / $totalBlm) * 100;
	}

	$tab .= '<tr class=rowcontent><td colspan=2>Total all Items</td>';

	foreach ($kdOrg as $brsData2 => $rData2) {
		if ($blmPO[$rData2['kodeorganisasi']] != 0) {
			@$presen[$rData2['kodeorganisasi']] = ($totTrbitPO[$rData2['kodeorganisasi']] / $blmPO[$rData2['kodeorganisasi']]) * 100;
		}

		$tab .= '<td align=right>' . number_format($blmPO[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($totTrbitPO[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($grndTotal[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($presen[$rData2['kodeorganisasi']], 0) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '</tbody></table></fieldset>';
	echo $tab;
	break;

case 'detailSum':
	$tab .= '<link rel=stylesheet type=text/css href=style/generic.css>' . "\r\n" . '            <script language=javascript1.2 src=\'js/generic.js\'></script>' . "\r\n" . '            <script language=javascript1.2 src=\'js/log_verivikasi.js\'></script>';
	$thn = substr($periode, 0, 4);
	$sPur = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $userid . '\'';

	#exit(mysql_error());
	$qPur = mysql_query($sPur);
	$rPur = mysql_fetch_assoc($qPur);
	$tab .= '<fieldset><legend>Summary</legend>';
	$tab .= 'Purchaser : ' . $rPur['namakaryawan'] . '<br />' . "\r\n" . '                   ' . $_SESSION['lang']['periode'] . ' : ' . $thn . '<br />' . "\r\n" . '                <img onclick=detailExcel2(\'' . $userid . '\',\'' . $periode . '\') src=images/excel.jpg class=resicon title=\'MS.Excel\'>' . "\r\n" . '                <table cellspacing=1 border=0 cellpading=0>' . "\r\n" . '                <thead>';
	$sPt = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
	$qData = fetchData($sPt);
	$tab .= '<tr class=rowheader>';
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['periode'] . '</td>';
	$sPt = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
	$qData = fetchData($sPt);
	$jumlahData = count($qData);
	$a = 1;

	foreach ($qData as $brsData => $rData) {
		$kdOrg[] = $rData;
		$tab .= '<td colspan=4 align=center>'.$rData['kodeorganisasi'].'</td>';
		++$a;
	}
	$tab .= '</tr><tr class=rowheader>';
	for ($acd = 0; $acd < $jumlahData; ++$acd) {
		$tab .= "<td align=center'>Total. Item</td><td align=center bgcolor='green'>On PO</td><td align=center bgcolor='red'>Not PO</td><td align=center>% Out</td>";
	}

	$tab .= '</tr></thead><tbody>';
	$sPeriode = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_poht where substr(tanggal,1,4)=\'' . $thn . '\' order by tanggal desc';

	#exit(mysql_error());
	$qPeriode = mysql_query($sPeriode);

	while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $rPeriode['periode'] . '</td>';

		foreach ($qData as $brsData2 => $rData2) {
			$sDt = 'SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                        WHERE  b.status=\'0\' and kodeorg=\'' . $rData2['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $rPeriode['periode'] . '%\' and purchaser=\'' . $userid . '\' ';

			#echo $sDt.';';
			($qDt = mysql_query($sDt)) || true;
			$rDt = mysql_fetch_assoc($qDt);
			$sDt2 = 'SELECT  kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '                           LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp = c.nopp  ' . "\r\n" . '                           WHERE  b.status=\'0\' and kodeorg=\'' . $rData2['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $rPeriode['periode'] . '%\' and c.nopo!=\'\' and purchaser=\'' . $userid . '\' group by b.kodebarang';

			#echo $qDt;
			($qDt2 = mysql_query($sDt2)) || true;
			$jmlhPo2 = mysql_num_rows($qDt2);
			$rDt2 = mysql_fetch_assoc($qDt2);
			$totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']] += $jmlhPo2;
			$totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']] += $rDt['jmlhPo'];
			$all[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']] = $totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']] - $totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']][$rDt2['periode']];

			if ($totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']] != 0) {
				if ($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']] != 0) {
					@$persenId[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']] = ($totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']] / $totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']]) * 100;
				}

				$tab .= '<td align=right><a href=\'#\' onclick=detailExcel(\'' . $rDt['purchaser'] . '\',\'' . $rDt['kodeorg'] . '\',\'' . $rDt['periode'] . '\')>' . number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</a></td>';
				$tab .= '<td align=right>' . number_format($totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
				$tab .= '<td align=right>' . number_format($all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
				$tab .= '<td align=right>' . number_format($persenId[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
			}
			else {
				$tab .= '<td align=right>' . number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
				$tab .= '<td align=right>' . number_format($totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
				$tab .= '<td align=right>' . number_format($all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
				$tab .= '<td align=right>' . number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
			}

			$jmlhAll[$rData2['kodeorganisasi']] += $all[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];
			$jmlhTrbtPo[$rData2['kodeorganisasi']] += $totalPo2[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];
			$jmlhBlmpo[$rData2['kodeorganisasi']] += $totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']];
		}

		$tab .= '</tr>';
	}

	$tab .= '<tr class=rowcontent><td>&nbsp;</td>';

	foreach ($qData as $brsData3 => $rData3) {
		if ($jmlhBlmpo[$rData3['kodeorganisasi']] != 0) {
			@$persenTotal[$rData3['kodeorganisasi']] = ($jmlhTrbtPo[$rData3['kodeorganisasi']] / $jmlhBlmpo[$rData3['kodeorganisasi']]) * 100;
			$tab .= '<td align=right><a href=\'#\' onclick=detailExcel(\'' . $userid . '\',\'' . $rData3['kodeorganisasi'] . '\',\'' . $thn . '\')>' . number_format($jmlhBlmpo[$rData3['kodeorganisasi']], 0) . '</a></td>';
			$tab .= '<td align=right>' . number_format($jmlhTrbtPo[$rData3['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($jmlhAll[$rData3['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($persenTotal[$rData3['kodeorganisasi']], 0) . '</td>';
		}
		else {
			$tab .= '<td align=right>' . number_format($jmlhBlmpo[$rData3['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($jmlhTrbtPo[$rData3['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($jmlhAll[$rData3['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format(0, 0) . '</td>';
		}
	}

	$tab .= '</tr>';
	$tab .= '</tbody></table></fieldset>';
	echo $tab;
	break;

case 'getSummar':
	if ($periode == '') {
		$periode = date('Y-m');
	}

	$sPt = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
	$qData = fetchData($sPt);
	$jumlahData = count($qData);
	$a = 1;

	foreach ($qData as $brsData => $rData) {
		$kdOrg[] = $rData;
	}

	$sPur = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan ' . "\r\n" . '               where bagian=\'PUR\' and kodejabatan!=\'33\' and (tanggalkeluar>\'' . date('Y-m-d') . '\' or tanggalkeluar is NULL)  order by namakaryawan asc';
	$qPur = fetchData($sPur);

	foreach ($qPur as $brsKary) {
		foreach ($kdOrg as $brsData3 => $rData3) {
			$sDt = ' SELECT   count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '                                    WHERE  b.status=\'0\'  and kodeorg=\'' . $rData3['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $periode . '%\' and b.purchaser=\'' . $brsKary['karyawanid'] . '\'';

			#exit(mysql_error());
			($qDt = mysql_query($sDt)) || true;
			$rDt = mysql_fetch_assoc($qDt);
			$sDt2 = ' SELECT kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '                        LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp = c.nopp  ' . "\r\n" . '                                    WHERE b.status=\'0\' and kodeorg=\'' . $rData3['kodeorganisasi'] . '\' and substring(tanggal,1,7) like \'%' . $periode . '%\' and c.nopo!=\'\' and b.purchaser=\'' . $brsKary['karyawanid'] . '\' group by b.kodebarang  ';

			#exit(mysql_error());
			($qDt2 = mysql_query($sDt2)) || true;
			$jmlhPo2 = mysql_num_rows($qDt2);
			$rDt2 = mysql_fetch_assoc($qDt2);
			$totalPo2[$rDt['purchaser']] += $rDt['kodeorg'];
			$totalPo[$rDt['purchaser']] += $rDt['kodeorg'];
			$all[$rDt['purchaser']] += $rDt['kodeorg'];
			$tempTotalPo2 += $rDt['purchaser'];
			$sisa += $rDt['purchaser'];
		}

		$DtaAll[] = $brsKary;
	}

	foreach ($DtaAll as $brs) {
		++$no;
		$tab .= '<tr class=rowcontent onclick="detailData(\'' . $brs['karyawanid'] . '\',\'' . $periode . '\')" style="cursor:pointer;">';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $brs['namakaryawan'] . '</td>';

		foreach ($kdOrg as $brsData2 => $rData2) {
			if ($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']] != 0) {
				@$persen5[$brs['karyawanid']][$rData2['kodeorganisasi']] = ($all[$brs['karyawanid']][$rData2['kodeorganisasi']] / $totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']]) * 100;
			}

			$tab .= '<td align=right>' . number_format($totalPo[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($totalPo2[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($all[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$tab .= '<td align=right>' . number_format($persen5[$brs['karyawanid']][$rData2['kodeorganisasi']], 0) . '</td>';
			$totTrbitPO += $rData2['kodeorganisasi'];
			$blmPO += $rData2['kodeorganisasi'];
			$grndTotal += $rData2['kodeorganisasi'];
		}
	}

	$col = $a + 2;
	$sAll = 'select count(*) as jmlh from ' . $dbname . '.log_prapodt where purchaser=\'0000000000\'';

	#exit(mysql_error());
	($qAll = mysql_query($sAll)) || true;
	$rAll = mysql_fetch_assoc($qAll);

	if ($totalBlm != 0) {
		@$persenTot = ($totalSemua / $totalBlm) * 100;
	}

	$tab .= '<tr class=rowcontent><td colspan=2>Total all Items</td>';

	foreach ($kdOrg as $brsData2 => $rData2) {
		if ($blmPO[$rData2['kodeorganisasi']] != 0) {
			@$presen[$rData2['kodeorganisasi']] = ($totTrbitPO[$rData2['kodeorganisasi']] / $blmPO[$rData2['kodeorganisasi']]) * 100;
		}

		$tab .= '<td align=right>' . number_format($blmPO[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($totTrbitPO[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($grndTotal[$rData2['kodeorganisasi']], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($presen[$rData2['kodeorganisasi']], 0) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '</tbody></table></fieldset>';
	echo $tab . '###' . $periode;
	break;

case 'dataDetail':
	$userid = $_GET['userid'];
	$kodeorg = $_GET['kodeorg'];
	$periode = $_GET['periode'];
	$stream .= ' ' . "\r\n" . '         <table border="1">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>No.</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tanggal'] . ' PR</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tgldibutuhkan'] . '</td>             ' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tanggal'] . ' Alokasi</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlh_disetujui'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['jmlh_hari_outstanding'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '         <td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
	$sql = 'SELECT   kodeorg,b.purchaser,jumlah,realisasi,a.tanggal,b.kodebarang,b.nopp,b.tgl_sdt,b.tglAlokasi FROM ' . "\r\n" . '            ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '              WHERE  b.status=\'0\'  and kodeorg=\'' . $kodeorg . '\' and substr(tanggal,1,7) like \'%' . $periode . '%\' and purchaser=\'' . $userid . '\'';

	if (mysql_query($sql)) {
		$res = mysql_query($sql);

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$sPp2 = 'select nopo  from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

			#exit(mysql_error());
			($qPp2 = mysql_query($sPp2)) || true;
			$rPp2 = mysql_fetch_object($qPp2);
			$sPp = 'select tanggal,kodesupplier from ' . $dbname . '.log_poht where nopo=\'' . $rPp2->nopo . '\'';

			#exit(mysql_error());
			($qPp = mysql_query($sPp)) || true;
			$rPp = mysql_fetch_object($qPp);

			if ($rPp->tanggal != '0000-00-00') {
				$tglA = substr($rPp->tanggal, 0, 4);
				$tglB = substr($rPp->tanggal, 5, 2);
				$tglC = substr($rPp->tanggal, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tGl1 = substr($bar->tglAlokasi, 0, 4);
				$tGl2 = substr($bar->tglAlokasi, 5, 2);
				$tGl3 = substr($bar->tglAlokasi, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tgl1 = $tGl1 . $tGl2 . $tGl3;
				$starttime = strtotime($tgl1);
				$endtime = strtotime($tgl2);
				$timediffSecond = abs($endtime - $starttime);
				$base_year = min(date('Y', $tGl1), date('Y', $tglA));
				$diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
				$jmlHari = date('j', $diff) - 1;
				$tglSkrg = $rPp->tanggal;
			}
			else {
				$tglSkrg = date('Y-m-d');
				$tglA = substr($bar->tglAlokasi, 0, 4);
				$tglB = substr($bar->tglAlokasi, 5, 2);
				$tglC = substr($bar->tglAlokasi, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tGl1 = substr($tglSkrg, 0, 4);
				$tGl2 = substr($tglSkrg, 5, 2);
				$tGl3 = substr($tglSkrg, 8, 2);
				$tgl2 = $tglA . $tglB . $tglC;
				$tgl1 = $tGl1 . $tGl2 . $tGl3;
				$starttime = strtotime($tgl1);
				$endtime = strtotime($tgl2);
				$timediffSecond = abs($endtime - $starttime);
				$base_year = min(date('Y', $tGl1), date('Y', $tglA));
				$diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
				$jmlHari = date('j', $diff) - 1;
			}

			$sNmSup = 'select distinct namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $rPp->kodesupplier . '\'';

			#exit(mysql_error());
			($qNmSup = mysql_query($sNmSup)) || true;
			$rNmSup = mysql_fetch_assoc($qNmSup);
			$stream .= '<tr>' . "\r\n" . '                              <td>' . $no . '</td>' . "\r\n" . '                              <td>' . $bar->nopp . '</td>' . "\r\n" . '                              <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                              <td>' . substr($rDtBrg[$bar->kodebarang], 0, 33) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tgl_sdt) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tglAlokasi) . '</td>' . "\r\n" . '                              <td align=right>' . number_format($bar->jumlah, 2) . '</td>' . "\r\n" . '                              <td align=right>' . number_format($bar->realisasi, 2) . '</td>' . "\r\n" . '                              <td>' . $nmSatuan[$bar->kodebarang] . '</td>' . "\r\n" . '                              <td>' . $jmlHari . '</td> ' . "\r\n" . '                              <td>' . $rPp2->nopo . '</td> ' . "\r\n" . '                              <td>' . tanggalnormal($tglSkrg) . '</td>';
			$stream .= '<td>' . $rNmSup['namasupplier'] . '</td>';
			$stream .= '</tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	$stream .= ' </tbody>';
	$stream .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$time = date('Hms');
	$nop_ = 'listBarang_' . $periode . '_' . $userid . '_' . $time;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                            </script>';
	break;

case 'dataDetailEx':
	$userid = $_GET['userid'];
	$periode = $_GET['periode'];
	$kodeorg = $_GET['kodeorg'];
	$thn = substr($periode, 0, 4);
	$sPur = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $userid . '\'';

	#exit(mysql_error());
	($qPur = mysql_query($sPur)) || true;
	$rPur = mysql_fetch_assoc($qPur);
	$tab .= '<table cellspacing=1 border=0 cellpading=0>' . "\r\n" . '                <tr><td colspan=2>Purchaser</td><td> :</td><td colspan=3  align=left> ' . $rPur['namakaryawan'] . '</td><td>&nbsp</td></tr>' . "\r\n" . '                <tr><td colspan=2>' . $_SESSION['lang']['periode'] . '</td><td> :</td><td colspan=3 align=left> ' . $thn . '</td><td>&nbsp</td></tr>' . "\r\n" . '                 </table>';
	$tab .= "\r\n" . '                <table cellspacing=1 border=1 cellpading=0>' . "\r\n" . '                <thead>';
	$sPt = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
	$qData = fetchData($sPt);
	$tab .= '<tr class=rowheader>';
	$tab .= '<td bgcolor=#DEDEDE align=center valign=middle>' . $_SESSION['lang']['periode'] . '</td>';

	foreach ($qData as $brsData => $rData) {
		$tab .= '<td bgcolor=#DEDEDE align=center valign=middle>' . $rData['kodeorganisasi'] . '</td>';
	}

	$tab .= '<td bgcolor=#DEDEDE align=center valign=middle>Total Item</td><td bgcolor=#DEDEDE align=center valign=middle>Terbit PO</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center valign=middle>Outstanding PO</td>' . "\r\n" . '                <td bgcolor=#DEDEDE align=center valign=middle>% Outstanding</td>';
	$tab .= '</tr></thead><tbody>';
	$sPeriode = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_poht where substr(tanggal,1,4)=\'' . $thn . '\' order by tanggal desc';

	#exit(mysql_error());
	($qPeriode = mysql_query($sPeriode)) || true;

	while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $rPeriode['periode'] . '</td>';

		foreach ($qData as $brsData2 => $rData2) {
			$sDt = 'SELECT count(kodebarang) as jmlhPo,kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '                          WHERE  b.status=\'0\' and kodeorg=\'' . $rData2['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $rPeriode['periode'] . '%\' and purchaser=\'' . $userid . '\' ';

			#exit(mysql_error());
			($qDt = mysql_query($sDt)) || true;
			$rDt = mysql_fetch_assoc($qDt);
			$sDt2 = 'SELECT  kodeorg,purchaser,substr(tanggal,1,7) as periode FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp' . "\r\n" . '                           LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp = c.nopp  ' . "\r\n" . '                           WHERE  b.status=\'0\' and kodeorg=\'' . $rData2['kodeorganisasi'] . '\' and substr(tanggal,1,7) like \'%' . $rPeriode['periode'] . '%\' and c.nopo!=\'\' and purchaser=\'' . $userid . '\'  group by b.kodebarang ';

			#exit(mysql_error());
			($qDt2 = mysql_query($sDt2)) || true;
			$jmlhPo2 = mysql_num_rows($qDt2);
			$rDt2 = mysql_fetch_assoc($qDt2);
			$totalPo2[$rDt2['purchaser']][$rDt2['kodeorg']] += $rDt2['periode'];
			$totalPo[$rDt['purchaser']][$rDt['kodeorg']] += $rDt['periode'];
			$tempTotalPo2[$rDt2['purchaser']] += $rDt2['periode'];
			$sisa[$rDt['purchaser']] += $rDt['periode'];

			if ($totalPo[$rDt['purchaser']][$rDt['kodeorg']][$rDt['periode']] != 0) {
				$tab .= '<td align=right>' . number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
			}
			else {
				$tab .= '<td align=right>' . number_format($totalPo[$userid][$rData2['kodeorganisasi']][$rPeriode['periode']], 0) . '</td>';
			}

			$jmlh += $rData2['kodeorganisasi'];
		}

		$totBlm[$userid][$rPeriode['periode']] = $sisa[$userid][$rPeriode['periode']] - $tempTotalPo2[$userid][$rPeriode['periode']];

		if ($sisa[$userid][$rPeriode['periode']] != 0) {
			$persen[$userid][$rPeriode['periode']] = ($totBlm[$userid][$rPeriode['periode']] / $sisa[$userid][$rPeriode['periode']]) * 100;
		}

		$tab .= '<td  align=right>' . number_format($sisa[$userid][$rPeriode['periode']], 0) . '</td>';
		$tab .= '<td  align=right>' . number_format($tempTotalPo2[$userid][$rPeriode['periode']], 0) . '</td>';
		$tab .= '<td  align=right>' . number_format($totBlm[$userid][$rPeriode['periode']], 0) . '</td>';
		$tab .= '<td  align=right>' . number_format($persen[$userid][$rPeriode['periode']], 0) . '</td>';
		$tab .= '</tr>';
		$totItem += $sisa[$userid][$rPeriode['periode']];
		$trbtPo += $tempTotalPo2[$userid][$rPeriode['periode']];
		$blmPo += $totBlm[$userid][$rPeriode['periode']];

		if ($totItem != 0) {
			$totPersen = ($blmPo / $totItem) * 100;
		}
	}

	$tab .= '<tr class=rowcontent><td>&nbsp;</td>';

	foreach ($qData as $brsData3 => $rData3) {
		$tab .= '<td align=right>' . number_format($jmlh[$rData3['kodeorganisasi']], 0) . '</td>';
	}

	$tab .= '<td  align=right>' . number_format($totItem, 0) . '</td>';
	$tab .= '<td  align=right>' . number_format($trbtPo, 0) . '</td>';
	$tab .= '<td  align=right>' . number_format($blmPo, 0) . '</td>';
	$tab .= '<td  align=right>' . number_format($totPersen, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '</tbody>';
	$tab .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$jam = date('Hms');
	$nop_ = 'listBarang__' . $userid . '_' . $jam;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                            </script>';
	break;

case 'listVerivikasiPP':
	$optPur = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$klq = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where  (bagian=\'PUR\') and tanggalkeluar is NULL order by namakaryawan asc ';

	#exit(mysql_error());
	($qry = mysql_query($klq)) || true;

	while ($rst = mysql_fetch_object($qry)) {
		$optPur .= '<option value=' . $rst->karyawanid . '>' . $rst->namakaryawan . '</option>';
	}

	$cl = array('Head Office', 'Local');

	foreach ($cl as $rw => $isi) {
		$optLokasi .= '<option  value=\'' . $rw . '\'>' . $isi . '</option>';
	}

	$str = 'SELECT  distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.' . "\r\n" . '                     persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, ' . "\r\n" . '                      a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, ' . "\r\n" . '                      a.tglp3, a.tglp4, a.tglp5,b.*,c.nopo FROM ' . $dbname . '.log_prapodt b ' . "\r\n" . '                      LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp ' . "\r\n" . '                      LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp=c.nopp  ' . "\r\n" . '                      WHERE b.nopp=\'' . $nopp . '\' and create_po!=\'1\' group by kodebarang ORDER BY a.tanggal desc ';

	#exit(mysql_error($conn));
	($res2 = mysql_query($str)) || true;
	$row = mysql_num_rows($res2);
	echo "\r\n" . '         <input type="hidden" id=ppno name=ppno value=' . $nopp . ' />' . "\r\n" . '         <fieldset><legend>' . $nopp . '</legend> ' . "\r\n" . '         <table cellpadding=1 cellspacing=1 border=0 class=sortable>' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td colspan=3>Verification Form</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['jumlah'] . ' Item</td><td id=totalBrg>' . $row . '</td></tr>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['purchaser'] . '</td><td><select id=purId2 name=purId2 style=width:150px;>' . $optPur . '</select></td></tr>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['lokasitugas'] . '</td><td><select id=lokId name=lokId style=width:150px;>' . $optLokasi . '</select></td></tr>' . "\r\n" . '         <tr><td colspan=3><button class=mybutton onclick=saveSemua(1) id=saveAll title=Simpan Semua>' . $_SESSION['lang']['save'] . ' ' . $_SESSION['lang']['all'] . '</button><button class=mybutton onclick=cancel()>' . $_SESSION['lang']['cancel'] . '</button></td></tr>' . "\r\n" . '         </tbody>' . "\r\n" . '         </table><br />';

	if (mysql_query($str)) {
		$res = mysql_query($str);
		echo '<fieldset><legend>' . $_SESSION['lang']['list'] . ' Item</legend>' . "\r\n" . '         <div  style=overflow:auto;width:650px;height:275px;>' . "\r\n" . '         <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '     <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n" . '         <td colspan=\'3\' align="center">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';

		while ($bar = mysql_fetch_object($res)) {
			$koderorg = substr($bar->nopp, 15, 4);
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_object($rep);
			$no += 1;
			$sPoDet = 'select distinct hargasatuan from ' . $dbname . '.log_podt where  kodebarang=\'' . $bar->kodebarang . '\' order by nopo desc';

			#exit(mysql_error());
			($qPoDet = mysql_query($sPoDet)) || true;
			$rCek = mysql_fetch_assoc($qPoDet);

			if ($bar->realisasi == '') {
				$bar->realisasi = 0;
			}

			echo '<tr class=rowcontent id=\'rew_' . $no . '\'>' . "\r\n" . '                              <td>' . $no . '</td>' . "\r\n" . '                              <td id=kdBrg_' . $no . '>' . $bar->kodebarang . '</td>' . "\r\n" . '                              <td>' . substr($rDtBrg[$bar->kodebarang], 0, 33) . '</td>' . "\r\n" . '                              <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                              <td align=center id=jmlh_' . $no . '>' . $bar->jumlah . '</td>' . "\r\n" . '                              <td align=center  >' . $bar->realisasi . '</td>' . "\r\n" . '                              <td align=right  >' . number_format($rCek['hargasatuan'], 2) . '</td>';
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td></tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo ' </tbody>' . "\r\n" . '         </table></div></fieldset></fieldset>';
	break;

case 'listVerivikasiPP2':
	$optPur = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$klq = 'select karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where  bagian=\'PUR\' and tanggalkeluar is NULL order by namakaryawan asc ';

	#exit(mysql_error());
	($qry = mysql_query($klq)) || true;

	while ($rst = mysql_fetch_object($qry)) {
		$optPur .= '<option value=' . $rst->karyawanid . '>' . $rst->namakaryawan . '</option>';
	}

	$cl = array('Head Office', 'Local');

	foreach ($cl as $rw => $isi) {
		$optLokasi .= '<option  value=\'' . $rw . '\'>' . $isi . '</option>';
	}

	$str = 'SELECT  distinct a.tanggal, a.persetujuan1, a.persetujuan2, a.persetujuan3, a.persetujuan4, a.persetujuan5, a.close, a.hasilpersetujuan1, a.hasilpersetujuan2, a.hasilpersetujuan3, a.hasilpersetujuan4, a.hasilpersetujuan5, a.tglp1, a.tglp2, a.tglp3, a.tglp4, a.tglp5,b.*,c.nopo FROM ' . $dbname . '.log_prapodt b LEFT JOIN ' . $dbname . '.log_prapoht a ON a.nopp = b.nopp LEFT JOIN ' . $dbname . '.log_podt c ON b.nopp=c.nopp  ' . "\r\n" . '                WHERE b.nopp=\'' . $nopp . '\' and create_po!=\'1\' group by kodebarang ORDER BY a.tanggal desc ';

	#exit(mysql_error($conn));
	($res2 = mysql_query($str)) || true;
	$row = mysql_num_rows($res2);
	echo "\r\n" . '         <input type="hidden" id=ppno name=ppno value=' . $nopp . ' />' . "\r\n" . '         <fieldset><legend>' . $nopp . '</legend> ' . "\r\n" . '         <table cellpadding=1 cellspacing=1 border=0 class=sortable>' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td colspan=3>Form Verivikasi</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['jumlah'] . ' Item</td><td id=totalBrg_2>' . $row . '</td></tr>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['purchaser'] . '</td><td><select id=purId2_2 name=purId2_2 style=width:150px;>' . $optPur . '</select></td></tr>' . "\r\n" . '         <tr class=rowcontent><td colspan=2>' . $_SESSION['lang']['lokasitugas'] . '</td><td><select id=lokId_2 name=lokId_2 style=width:150px;>' . $optLokasi . '</select></td></tr>' . "\r\n" . '         <tr><td colspan=3><button class=mybutton onclick=saveSemua2(1) id=saveAll2 title=\'Save All\'>' . $_SESSION['lang']['save'] . ' ' . $_SESSION['lang']['all'] . '</button><button class=mybutton onclick=cancel()>' . $_SESSION['lang']['cancel'] . '</button></td></tr>' . "\r\n" . '         </tbody>' . "\r\n" . '         </table><br />';

	if ($res = mysql_query($str)) {
		echo '<fieldset><legend>' . $_SESSION['lang']['list'] . ' Item</legend>' . "\r\n" . '         <div  style=overflow:auto;width:650px;height:275px;>' . "\r\n" . '         <table class="sortable" cellspacing="1" border="0">' . "\r\n" . '         <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '         <td>No.</td>' . "\r\n" . '     <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n" . '         <td>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '         <td colspan=\'3\' align="center">Action</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';

		while ($bar = mysql_fetch_object($res)) {
			$koderorg = substr($bar->nopp, 15, 4);
			$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

			#exit(mysql_error($conn));
			($rep = mysql_query($spr)) || true;
			$bas = mysql_fetch_object($rep);
			$no += 1;
			$sPoDet = 'select nopo from ' . $dbname . '.log_podt where nopp=\'' . $bar->nopp . '\' and kodebarang=\'' . $bar->kodebarang . '\'';

			#exit(mysql_error());
			($qPoDet = mysql_query($sPoDet)) || true;
			$rCek = mysql_fetch_assoc($qPoDet);

			if ($bar->realisasi == '') {
				$bar->realisasi = 0;
			}

			echo '<tr class=rowcontent id=\'rew_' . $no . '\'>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td id=kdBrg_2_' . $no . '>' . $bar->kodebarang . '</td>' . "\r\n" . '                                  <td>' . substr($rDtBrg[$bar->kodebarang], 0, 33) . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                                  <td align=center id=jmlh_2_' . $no . '>' . $bar->jumlah . '</td>' . "\r\n" . '                                  <td align=center  >' . $bar->realisasi . '</td>';
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td></tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo ' </tbody>' . "\r\n" . '         </table></div></fieldset></fieldset>';
	break;

case 'listAddPP':
	$str = 'select distinct * from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\' and status!=3';
	$res = mysql_query($str);
	echo '<fieldset><legend>' . $_SESSION['lang']['form'] . '</legend>';
	$lstData = mysql_fetch_assoc($res);
	echo '<table class="sortable" cellspacing="1" border="0"><thead>';
	echo '<tr class=rowheader><td>' . $_SESSION['lang']['nopp'] . '</td><td>' . $_SESSION['lang']['tanggalSdt'] . '</td></tr></thead><tbody>';
	echo '<tr class=rowcontent><td id=noppAja>' . $lstData['nopp'] . '</td><td id=tglSdt>' . tanggalnormal($lstData['tgl_sdt']) . '</td></tr></tbody></table><br />';
	echo '<div id=listDataPP><table class="sortable" cellspacing="1" border="0"><thead>';
	echo '<tr class=rowheader><td>' . $_SESSION['lang']['namabarang'] . '</td><td>' . $_SESSION['lang']['satuan'] . '</td><td>' . $_SESSION['lang']['jumlah'] . '</td><td>*</td></tr></thead><tbody>';
	echo '<tr class=rowcontent><td><input type=text class=myinputtext onkeypress=\'return tanpa_kutip(event)\' id=nmBarang onclick="cariBarang();" /></td>' . "\r\n" . '             <td><input type=text disabled class=myinputtext id=satuanForm /></td>' . "\r\n" . '             <td><input type=text class=myinputtextnumber onkeypress=\'return angka_doang(event)\' id=jmlhBrg /></td>';
	echo '<td><img src=images/save.png class=resicon onclick=tambahBarang() /></td>';
	echo '</tr></tbody></table><input type=hidden id=kdBarang /></div>';
	echo '<div id=cariBarang style=display:none>' . "\r\n" . '              <fieldset style=float:left><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>' . $_SESSION['lang']['find'] . '<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=cariBarangGet()>Find</button></fieldset>' . "\r\n" . '              <div id=container5></div></div>';
	echo '</fieldset>';
	break;

case 'insertPurchaser':
	if ($purchaser == '') {
		exit('Error: Purchaser is obligatory');
	}

	$sql2 = 'update ' . $dbname . '.log_prapodt set purchaser=\'' . $purchaser . '\',lokalpusat=\'' . $lokal . '\',realisasi=\'' . $jmlh_realisai . '\',tglAlokasi=\'' . $tglHrini . '\' where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kd_brng . '\' and status!=\'3\'';

	if (!mysql_query($sql2)) {
		echo $sql2;
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadTools':
	$tab = "<table class=sortable border=0 cellspacing=1 cellpadding=1><thead>\r\n                 <tr class=rowheader>\r\n                 <td>No.</td>\r\n                 <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                 <td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n                 </tr>\r\n                 </thead><tbody>";
        $sql = 'select distinct kodeorg  from '.$dbname.'.log_prapoht where nopp in (select a.nopp from '.$dbname.'.log_prapoht a left join '.$dbname.'.log_prapodt b on a.nopp=b.nopp where close=2 and b.status<3 and purchaser=0)';
		#echo $sql;
        $query = mysql_query($sql);
        while ($res = mysql_fetch_assoc($query)) {
            ++$no;
            $tab .= '<tr class=rowcontent onclick=detailPo('.$no.") style='cursor:pointer'>\r\n                     <td>".$no."</td>\r\n                      <td id=kodeOrg_".$no.'>'.$res['kodeorg']."</td>\r\n                     <td>".$optNm[$res['kodeorg']].'</td></tr>';
            $tab .= '<tr><td colspan=3><div id=dataPO_'.$no.'></div></td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;
	break;

case 'loadPPDetail':
	$brsKe = $_POST['brsKe'];
	$tab = '<img onclick="closeList(' . $brsKe . ');" title="Tutup" class="resicon" src="images/close.gif">';
	$tab .= '<table cellspacing=1 cellpadding=1 border=0 width=100%>' . "\r\n" . '                     <thead>' . "\r\n" . '                    <tr class=rowheader>' . "\r\n" . '                    <td>No</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['print'] . '</td>' . "\r\n" . '                   </tr>' . "\r\n" . '                     </thead><tbody>';
	$sql2 = 'select b.kodebarang,a.kodeorg,b.nopp from ' . $dbname . '.log_prapodt b left join ' . $dbname . '.log_prapoht a on a.nopp=b.nopp ' . "\r\n" . '             where purchaser=0000000000 and status<3 and kodeorg=\'' . $kodeorg . '\' and a.close=2 group by nopp order by substring(a.nopp,16,4) asc';

	#exit(mysql_error());
	($query = mysql_query($sql2)) || true;
	$jmlData = mysql_num_rows($query);
	$tab .= '<tr  class=rowcontent><td colspan=5>Total PP :' . $jmlData . '</td></tr>';

	while ($rwd = mysql_fetch_assoc($query)) {
		$sJum = 'select count(kodebarang) as jumlah from ' . $dbname . '.log_prapodt where nopp=\'' . $rwd['nopp'] . '\' and purchaser=0000000000 and status<3';

		#exit(mysql_error($conn));
		($qJum = mysql_query($sJum)) || true;
		$rJum = mysql_fetch_assoc($qJum);
		$no += 1;
		$koderorg = substr($rwd['nopp'], 15, 4);
		$tab .= '<tr class=rowcontent>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td  onclick="getDataPP2(\'' . $rwd['nopp'] . '\')" style="cursor:pointer">' . $rwd['nopp'] . '</td>' . "\r\n" . '                    <td align=center>' . $koderorg . '</td>' . "\r\n" . '                    <td align=right>' . $rJum['jumlah'] . '</td>' . "\r\n" . '                    <td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $rwd['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);"></td></tr>';
	}

	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'loadBarang':
	$optBarang = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sKodenbarna = 'select distinct kodebarang,namabarang from ' . $dbname . '.log_5masterbarang where substr(kodebarang,1,3) = \'' . $klmpKbrg . '\' order by namabarang asc';

	#exit(mysql_error());
	($qKodeBarang = mysql_query($sKodenbarna)) || true;

	while ($rKodebarang = mysql_fetch_assoc($qKodeBarang)) {
		$optBarang .= '<option value=\'' . $rKodebarang['kodebarang'] . '\'>' . $rKodebarang['namabarang'] . '</option>';
	}

	echo $optBarang;
	break;

case 'getBarang':
	$tab = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                        <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                        <thead>' . "\r\n" . '                        <tr class=rowheader>' . "\r\n" . '                        <td>No.</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                        </tr><tbody>' . "\r\n" . '                        ';

	if ($klmpKbrg != '') {
		$add = ' and kelompokbarang=\'' . $klmpKbrg . '\'';
	}

	$sLoad = 'select kodebarang,namabarang,satuan,inactive from ' . $dbname . '.log_5masterbarang where   (kodebarang like \'%' . $nmBrg . '%\' or namabarang like \'%' . $nmBrg . '%\') ' . $add . '';

	#exit(mysql_error($conn));
	($qLoad = mysql_query($sLoad)) || true;

	while ($res = mysql_fetch_assoc($qLoad)) {
		$no += 1;

		if ($res['inactive'] == 1) {
			$tab .= '<tr bgcolor=\'red\' title=\'inactive\'>';
		}
		else {
			$tab .= '<tr class=rowcontent onclick="setData(\'' . $res['kodebarang'] . '\',\'' . $res['namabarang'] . '\',\'' . $res['satuan'] . '\')" title=\'' . $res['namabarang'] . '\'>';
		}

		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $res['kodebarang'] . '</td>';
		$tab .= '<td>' . $res['namabarang'] . '</td>';
		$tab .= '<td>' . $res['satuan'] . '</td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'addBarangTopp':
	if ($jmlh_realisai == '') {
		exit('Error: Quantity is obligatory');
	}

	if ($kd_brng == '') {
		exit('Error: Material Code is obligatory');
	}

	$optNama = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
	$sData = 'select distinct lokalpusat,purchaser,tglAlokasi from ' . $dbname . '.log_prapodt where nopp=\'' . $nopp . '\'';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;
	$rData = mysql_fetch_assoc($qData);
	$sIns = 'insert into ' . $dbname . '.log_prapodt (nopp, kodebarang, jumlah, realisasi, keterangan, tgl_sdt, lokalpusat,  tglAlokasi, purchaser) values' . "\r\n" . '                   (\'' . $nopp . '\',\'' . $kd_brng . '\',\'' . $jmlh_realisai . '\',\'' . $jmlh_realisai . '\',\'Tambah Barang oleh ' . $_SESSION['empl']['name'] . '\',\'' . $tglSdt . '\',\'' . $rData['lokalpusat'] . '\',\'' . $rData['tglAlokasi'] . '\',\'' . $rData['purchaser'] . '\')';

	if (mysql_query($sIns)) {
		echo 1;
	}
	else {
		echo 'Gagal' . $sIns . '___' . mysql_error();
	}

	break;
}

?>
