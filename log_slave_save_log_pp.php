<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$optNm = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nopp = $_POST['rnopp'];
$tanggal = tanggalsystem($_POST['rtgl_pp']);
$kodeorg = $_POST['rkd_bag'];
$method = $_POST['method'];
$user_id = $_POST['usr_id'];
$nopp2 = $_POST['dnopp'];
$jumlahpemberipersetujuan = $_POST['jumlahpemberipersetujuan'];
$stat_cls = $_POST['stat'];
$tgl = date('Ymd');
$bln = substr($tgl, 4, 2);
$thn = substr($tgl, 0, 4);
$catatan = $_POST['catatan'];

switch ($method) {
	case 'delete':
		$strx = "delete from $dbname.log_prapoht where nopp='" . $nopp . "'";

		if (mysql_query($strx)) {
			$ql = "delete from $dbname.log_prapodt where nopp='" . $nopp . "'";

			#exit(mysql_error());
			mysql_query($ql) || true;
		}
		else {
			echo ' Error,' . addslashes(mysql_error($conn));
		}

		break;
	case 'update':
		$strx = "update $dbname. log_prapoht set tanggal='" . $tanggal . "',kodeorg='" . $kodeorg . "',catatan='" . $catatan . "' where nopp='" . $nopp . "'";

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
			$sorg = "select induk from $dbname.organisasi where kodeorganisasi='" . $kodeorg . "'";

			#exit(mysql_error());
			($qorg = mysql_query($sorg)) || true;
			$rorg = mysql_fetch_assoc($qorg);
			$strx = "insert into $dbname.log_prapoht(nopp, kodeorg, tanggal,dibuat,catatan,jumlahpemberipersetujuan) ".
				"values('" . $nopp . "','" . $kodeorg . "','" . $tanggal . "','" . $user_id . "','" . $catatan . "',$jumlahpemberipersetujuan)";
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
		$strx = "delete from $dbname.log_prapoht where nopp='" . $nopp2 . "'";

		if (mysql_query($strx)) {
			echo '';
		}
		else {
			echo ' Gagal,' . addslashes(mysql_error($conn));
		}

		break;
	case 'insert_persetujuan':
		$sql = "SELECT * FROM $dbname.log_prapoht WHERE nopp='" . $nopp . "' ";

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$rest = mysql_fetch_assoc($query);
		$jmlh=$rest['jumlahpemberipersetujuan'];
		if (1 < $rest['close']) {
			echo 'Warning: Status closed, Can\'t update the status';
			exit();
		}
		else if ($rest['hasilpersetujuan1'] < 1) {
			$stat_cls = 1;
			$strx = "update $dbname. log_prapoht set ".
				"persetujuan1='" . $user_id . "',close='" . ($jmlh==1 ? 0 : $stat_cls) . "'  where nopp='" . $nopp . "'";

			if (mysql_query($strx)) {
				$to = getUserEmail($user_id);
				$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);

				if ($_SESSION['language'] == 'EN') {
					$subject = "[Notifikasi] PR Submission for approval, submitted by: " . $namakaryawan;
					$body = "<html><head><body><dd>Dear Sir/Madam,</dd><br><br>Today,  " . date('d-m-Y') . ",  on behalf of " . $namakaryawan . " submit a PR, requesting for your approval. To follow up, please follow the link below ".
						"<br><br><br>Regards,<br>eAgro Plantation Management Software.</body></head></html>";
				}
				else {
					$subject = '[Notifikasi]Persetujuan PP a/n ' . $namakaryawan;
					$body = "<html><head><body>Dengan Hormat,<br><br>Pada hari ini, tanggal " . date('d-m-Y') . " karyawan a/n : <b>" . $namakaryawan .
							"</b>, mengajukan Permintaan Pembelian Barang (PR) kepada bapak/ibu dengan No.PR : <b>" . $nopp . "</b>.<br><br>".
							"Untuk melakukan persetujuan atau menolak PR ini, silahkan login ke dalam aplikasi <b>'e-Agro Plantation Management Software'</b> ".
							"dengan menggunakan Username & Password yang sudah diberikan.<br><br><br>Hormat Kami,<br><b>e-Agro Plantation Management Software.</b></body></head></html>";
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
			$strx = "select * from $dbname.log_prapoht where nopp='" . $nopp . "'";
		}
		else if ($nopp == '') {
			$strx = "select * from $dbname.log_prapoht where nopp='" . $nopp . "' or tanggal like '%" . $tanggal . "%'";
		}
		else {
			$strx = "select * from $dbname.log_prapoht where nopp='" . $nopp . "' and tanggal = '" . $tanggal . "'";
		}

		break;
	case 'cek_pembuat_pp':
		$user_id = $_SESSION['standard']['userid'];
		$skry = "select dibuat from $dbname.log_prapoht where nopp='" . $nopp . "'";

		#exit(mysql_error());
		($qkry = mysql_query($skry)) || true;
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
			$sCek = "select bagian from  $dbname.datakaryawan ".
				"where karyawanid='" . $_SESSION['standard']['userid'] . "'";

			##exit(mysql_error($conn));
			($qCek = mysql_query($sCek));// || true;
			$rCek = mysql_fetch_assoc($qCek);
			if (($rCek['bagian'] == 'PUR') || ($rCek['bagian'] == 'AGR')) {
				$sql = "select count(*) jmlhrow from  $dbname.log_prapoht order by tanggal,nopp desc";
				//$str = "select * from  $dbname.log_prapoht order by tanggal desc limit " . $offset . "," . $limit ;
				$str = "select * from  $dbname.log_prapoht order by tanggal desc,nopp desc limit " . $offset . "," . $limit ;
			}
			else {
				$sql = "select count(*) jmlhrow from  $dbname.log_prapoht ".
						"where dibuat='" . $_SESSION['standard']['userid'] . "' order by tanggal,nopp desc";
				$str = "select * from  $dbname.log_prapoht where  dibuat='" . $_SESSION['standard']['userid'] . "' ".
					"order by tanggal desc,nopp desc limit " . $offset . "," . $limit ;
			}
		}
		else {
			$sql = "select count(*) jmlhrow from  $dbname.log_prapoht ".
					"where substring(nopp,16,4)='" . $_SESSION['empl']['lokasitugas'] . "' ".
					"and dibuat='" . $_SESSION['standard']['userid'] . "'";
			$str = "select * from  $dbname.log_prapoht ".
					"where substring(nopp,16,4)='" . $_SESSION['empl']['lokasitugas'] . "' ".
					"and dibuat='" . $_SESSION['standard']['userid'] . "' or persetujuan1='" . $_SESSION['standard']['userid'] . "' ".
					"order by tanggal desc,nopp desc limit " . $offset . "," . $limit ;
		}
		 
		#exit(mysql_error());
		($query = mysql_query($sql)) || true;

		while ($jsl = mysql_fetch_object($query)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$koderorg = substr($bar['nopp'], 15, 4);
				$spr = "select * from   $dbname.organisasi ".
						"where  kodeorganisasi='" . $koderorg . "' or induk='" . $koderorg . "'";

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_assoc($rep);
				$skry = "select karyawanid,namakaryawan ".
						"from  $dbname.datakaryawan where karyawanid='" . $bar['dibuat'] . "'";

				#exit(mysql_error());
				($qkry = mysql_query($skry)) || true;
				$rkry = mysql_fetch_assoc($qkry);
				$cekPt = substr($bar->nopp, 12, 4);
				$no += 1;

				if ($bar['close'] == 0) {
					$b = "<a href=# id=seeprog onclick=\"frm_ajun('" . $bar['nopp'] . "','" . $bar['close'] . "');\" ".
						"title='Click untuk mengubah status'>Need Approval</a>";
				}
				else if ($bar['close'] == 1) {
					$b = "<a href=# id=seeprog onclick=\"frm_ajun('" . $bar['nopp'] . "','" . $bar['close'] . "');\" ".
						"title='Menunggu Keputusan'>Waiting Approval</a>";
				}
				else if ($bar['close'] == $bar['jumlahpemberipersetujuan']) {
					// else if ($bar['close'] == PP_MAX_APPROVAL) {
					$i = 0;
					while ($i <= $bar['jumlahpemberipersetujuan']) {
						// while ($i <= PP_MAX_APPROVAL) {
						if ($bar['hasilpersetujuan' . $i] == 1) {
							$b = "<a href=# id=seeprog  title='Available'>" . $_SESSION['lang']['disetujui'] . "</a>";
						}
						else if ($bar['hasilpersetujuan' . $i] == 3) {
							$b = "<a href=# id=seeprog  title='Not Available'>" . $_SESSION['lang']['ditolak'] . "</a>";
						}
						++$i;
					}
				}

				$ed_kd_org = substr($bar['nopp'], 15, 4);

				if ($bar['tglp1'] == '') {
					$stTgl = '0';
				}
				else {
					$stTgl = 5;
				}

				echo "<tr class=rowcontent id='tr_". $no . "'> ".
					"	<td>" . $no . "</td>".
					"	<td>" . $bar['nopp'] . "</td>".
					"	<td>" . tanggalnormal($bar['tanggal']) .  "</td> ".
					"	<td>" . $bas['namaorganisasi'] . "</td>".
					"	<td>" . $rkry['namakaryawan'] . "</td>".
					"	<td>" . $b . "</td>";

				if ($bar['dibuat'] == $_SESSION['standard']['userid']) {
					echo "<td><img src=images/application/application_edit.png class=resicon  ".
						"title='Edit' onclick=\"fillField('" . $bar['nopp'] . "','" . tanggalnormal($bar['tanggal']) . "','" .
						$ed_kd_org . "','" . $bar['close'] . "','" . $stTgl . "');\" >" .
						"<img src=images/application/application_delete.png class=resicon  ".
						"title='Delete' onclick=\"delPp('" . $bar['nopp'] . "','" . $bar['close'] . "','" . $stTgl . '\');" >';
					echo "<img onclick=\"previewDetail('" . $bar['nopp'] . "',event);\" ".
						"title='Detail PP' class='resicon' src='images/zoom.png'> ".
						"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','" . $bar['nopp'] . "','','log_slave_print_log_pp',event);\"></td>";
				}
				else {
					echo "<td><img onclick=\"previewDetail('" . $bar['nopp'] . "',event);\" ".
						"title='Detail PP' class='resicon' src='images/zoom.png'> ".
						"<img src=images/pdf.jpg class='resicon'  title='Print' ".
						"onclick=\"masterPDF('log_prapoht','" . $bar['nopp'] . "','','log_slave_print_log_pp',event);\"></td>";
				}
			}

			echo "</tr><tr> 
				 <td colspan=7 align=center>". (($page * $limit) + 1) . " to " . (($page + 1) * $limit) . " Of " . $jlhbrs .
				 "<br/><button class=mybutton onclick=\"cariBast('" . ($page - 1) . "');\">" . $_SESSION['lang']['pref'] . "</button>
				 <button class=mybutton onclick=\"cariBast('" . ($page + 1) ."');\">" . $_SESSION['lang']['lanjut'] . "</button>
				 </td></tr>";

		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;
	case 'getDetailPP':
		echo '<script language="javascript" src="js/log_pp.js"></script>';
		echo '<script language="javascript" src="js/log_pp.js"></script>';
		echo '<div style=\'width:750px;overflow:scroll;\'>' . "\r\n" . '                    <table border=0 cellspacing=1 class=sortable width=1200px>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr><td>' . $_SESSION['lang']['tanggal'] . ' PP</td><td>' . $_SESSION['lang']['dbuat_oleh'] . '</td>';
		$i = 1;

		while ($i < 6) {
			echo '<td>' . $_SESSION['lang']['persetujuan'] . $i . '</td>';
			++$i;
		}

		echo '</tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody>';
		$sPP = "select * from  $dbname.log_prapoht where nopp='" . $nopp . "'";
	 
		#exit(mysql_error($conn));
		($qPP = mysql_query($sPP)) || true;
		$flag_tolak = "N"; //bantuan kalo udah tolak, gak lanjut status lagi
		while ($bar = mysql_fetch_assoc($qPP)) {
			$sql = "select namakaryawan from  $dbname.datakaryawan where karyawanid='" . $bar['dibuat'] . "'";

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$ret = mysql_fetch_assoc($query);
			echo '<tr class=rowcontent><td>' . tanggalnormal($bar['tanggal']) . '</td><td>' . $ret['namakaryawan'] . '</td>';
			$arrHsl = array(0 => $_SESSION['lang']['wait_approval'], 1 => $_SESSION['lang']['disetujui'], 3 => $_SESSION['lang']['ditolak']);
			$i = 1;

			while ($i < 6) {
				if ($bar['tglp' . $i] != '') {
					$tngl = $bar['tglp' . $i];
				}

				if (($bar['persetujuan' . $i] != '') && ($bar['persetujuan' . $i] != 0)) {
					$kr = $bar['persetujuan' . $i];
					$sql = "select namakaryawan from  $dbname.datakaryawan where karyawanid='" . $kr . "'";

					#exit(mysql_error());
					($query = mysql_query($sql)) || true;
					$yrs = mysql_fetch_assoc($query);
					if( $flag_tolak == "N"){
						echo '<td>' . $yrs['namakaryawan'] . '<br />' . $arrHsl[$bar['hasilpersetujuan' . $i]] . ', ' . tanggalnormal($tngl) . '</td>';
					}else{
						echo '<td>&nbsp;</td>';
					}
					if( $bar['hasilpersetujuan' . $i] == 3 ){
						$flag_tolak = "Y";
					}
				}
				else {
					echo '<td>&nbsp;</td>';
				}

				++$i;
			}

			echo '</tr>';
		}

		echo "\r\n" . '                </tbody>' . "\r\n" . '                </table>' . "\r\n" . '                <br />' . "\r\n" . '                ';
		echo "\r\n" . '                <table border=0 cellspacing=1 class=sortable width=1200px>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>No</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['jmlhDiminta'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $_SESSION['lang']['stok'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['jmlh_disetujui'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['budget'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['realisasi'] . ' Todate</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['tanggal'] . ' PR</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['tgldibutuhkan'] . '</td>   ' . "\r\n" . '                <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n" . '                <td>Out.Std</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['lokasiBeli'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '                </tr>' . "\r\n" . '                </thead>' . "\r\n" . '                ';
		$sdhi = date('Y-m-d');
		$sCek = "select nopp from  $dbname.log_prapodt where nopp='" . $nopp . "'";

		#exit(mysql_error());
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_num_rows($qCek);

		if (0 < $rCek) {
			echo "\r\n" . '                <tbody>';
			$sDet = "select a.*,b.tanggal,c.jml_approve as jml_approve_c,case when c.jml_asal is null then a.jumlah else c.jml_asal end as jml_asal_c from  $dbname.log_prapodt a left join  $dbname.log_prapoht b on a.nopp=b.nopp left join $dbname.log_prapodt_vw as c on (a.nopp=c.nopp and a.kodebarang=c.kodebarang) where a.nopp='" . $nopp . "'";
			#exit(mysql_error());
			($qDet = mysql_query($sDet)) || true;
			$lokasi = array('Pusat', 'Lokal');

			while ($res = mysql_fetch_assoc($qDet)) {
				$thnAnggaran = substr($res['tanggal'], 0, 4);
				$unitAnggaran = substr($nopp, 15, 4);
				$awalthn = $thnAnggaran . '-01-01';
				$sBrg = "select namabarang,satuan from  $dbname.log_5masterbarang where kodebarang='" . $res['kodebarang'] . "'";

				#exit(mysql_error());
				($qBrg = mysql_query($sBrg)) || true;
				$rBrg = mysql_fetch_assoc($qBrg);
				$sPoDet = "select nopo from  $dbname.log_podt ".
						"where nopp='" . $res['nopp'] . "' and kodebarang='" . $res['kodebarang'] . "'";

				#exit(mysql_error());
				($qPoDet = mysql_query($sPoDet)) || true;
				$rCek = mysql_num_rows($qPoDet);
				$sAnggaran = "select sum(jumlah) as jmlhAnggaran from  $dbname.bgt_budget_detail ".
							"where kodebarang='" . $res['kodebarang'] . "' ".
							"and tahunbudget='" . substr($res['tanggal'], 0, 4) . "' ".
							"and kodeorg like '" . substr($nopp, 15, 4) . "%' group by kodebarang";

				#exit(mysql_error($conn));
				($qAnggaran = mysql_query($sAnggaran)) || true;
				$rAnggaran = mysql_fetch_assoc($qAnggaran);
				$sSdhi = "select sum(jumlahpesan) as sdhi from  $dbname. log_po_vw ".
						"where nopp like '%" . substr($nopp, 15, 4) . "%' ".
						"and kodebarang='" . $res['kodebarang'] . "' ".
						"and substr(tanggal,1,4)='" . $thnAnggaran . "'";

				#exit(mysql_error($conn));
				($qDhi = mysql_query($sSdhi)) || true;
				$rDphi = mysql_fetch_assoc($qDhi);

				if (0 < $rCek) {
					$rPoDet = mysql_fetch_assoc($qPoDet);
					$sPo = "select tanggal from  $dbname.log_poht where nopo='" . $rPoDet['nopo'] . "'";

					#exit(mysql_error());
					($qPo = mysql_query($sPo)) || true;
					$rPo = mysql_fetch_assoc($qPo);
					$Tgl2 = $rPo['tanggal'];
					$tgl1 = $res['tanggal'];
					$pecah1 = explode('-', $tgl1);
					$date1 = $pecah1[2];
					$month1 = $pecah1[1];
					$year1 = $pecah1[0];
					$pecah2 = explode('-', $Tgl2);
					$date2 = $pecah2[2];
					$month2 = $pecah2[1];
					$year2 = $pecah2[0];
					$stat = 1;
					$nopo = $rPoDet['nopo'];
					$tglPo = tanggalnormal($rPo['tanggal']);
				}
				else {
					$tgl1 = $res['tanggal'];
					$pecah1 = explode('-', $tgl1);
					$date1 = $pecah1[2];
					$month1 = $pecah1[1];
					$year1 = $pecah1[0];
					$tgl1 = $tGl1 . $tGl2 . $tGl3;
					$Tgl2 = date('Y-m-d');
					$pecah2 = explode('-', $Tgl2);
					$date2 = $pecah2[2];
					$month2 = $pecah2[1];
					$year2 = $pecah2[0];
					$stat = 0;
					$nopo = 'NaN';
				}

				$jd1 = GregorianToJD($month1, $date1, $year1);
				$jd2 = GregorianToJD($month2, $date2, $year2);
				$jmlHari = $jd2 - $jd1;
				$no += 1;

				if ($res['status'] == '3') {
					$stat2 = $_SESSION['lang']['ditolak'];
					$jmlHari = 0;
					$nopo = '';
				}
				else {
					$stat2 = '-';
				}

				$x = "select sum(saldoqty) as saldoqty,kodebarang from  $dbname.log_5masterbarangdt ".
						"where kodebarang='" . $res['kodebarang'] . "' and ".
						"kodegudang in (select kodeorganisasi from  $dbname.organisasi where induk in ".
						"		(select kodeorganisasi from  $dbname.organisasi where induk='" . $_SESSION['empl']['kodeorganisasi'] . "')) group by kodebarang";

				#exit(mysql_error($conn));
				($y = mysql_query($x)) || true;
				$z = mysql_fetch_assoc($y);
				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick=detailAnggaran(\'' . $res['kodebarang'] . "','" . $thnAnggaran . "','" . $unitAnggaran . '\')>' . "\r\n" . '                                <td>' . $no . '</td>' . "\r\n" . '                                <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                                <td>' . $rBrg['satuan'] . '</td>' . "\r\n" . '                                <td align=center>' . $res['jml_asal_c'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td align=center>' . $z['saldoqty'] . '</td>' . "\r\n" . '                                <td align=center>' . $res['jml_approve_c'] . '</td>' . "\r\n" . '                                <td align=center>' . number_format($rAnggaran['jmlhAnggaran'], 0) . '</td>' . "\r\n" . '                                <td align=center>' . number_format($rDphi['sdhi'], 0) . '</td>' . "\r\n" . '                                <td align=center>' . tanggalnormal($res['tanggal']) . '</td>' . "\r\n" . '                                <td align=center>' . tanggalnormal($res['tgl_sdt']) . '</td>    ' . "\r\n" . '                                <td align=center>' . $stat2 . '</td>' . "\r\n" . '                                <td align=center>' . $jmlHari . '</td>' . "\r\n" . '                                <td align=center>' . $lokasi[$res['lokalpusat']] . '</td>' . "\r\n\r\n" . '                                <td>' . $nopo . '</td>' . "\r\n" . '                                <td>' . $tglPo . '</td>' . "\r\n" . '                                </tr>';
			}

			echo '</tbody></table>';


			//$sql = "select t1.mod_stamp,t2.namakaryawan,t3.namabarang,t3.satuan,t1.jml_asal,t1.jml_approve,t1.alasan from ". $dbname .".log_prapodt_mod_jml_approve as t1 left join ". $dbname .".datakaryawan as t2 on (t1.ditolakoleh=t2.karyawanid) inner join ". $dbname .".log_5masterbarang as t3 on (t1.kodebarang=t3.kodebarang) where t1.nopp='".$nopp."';"

			//$sql = 'select t1.mod_stamp,t1.jml_asal,t1.jml_approve,t1.alasan from log_prapodt_mod_jml_approve as t1 ;'

			$sql = "select t1.mod_stamp,t2.namakaryawan,t3.namabarang,t3.satuan,t1.jml_asal,t1.jml_approve,t1.alasan ".
					"from $dbname.log_prapodt_mod_jml_approve as t1 ".
					"left join $dbname.datakaryawan as t2 on (t1.ditolakoleh=t2.karyawanid) ".
					"inner join $dbname.log_5masterbarang as t3 on (t1.kodebarang=t3.kodebarang) ".
					"inner join $dbname.log_prapodt as t4 on (t1.nopp=t4.nopp and t1.kodebarang=t4.kodebarang) ".
					"where t1.nopp='".$nopp."'";
			//echo $sql;
			#exit(mysql_error());
			($qDet = mysql_query($sql)) || true;

			$num_rows = mysql_num_rows($qDet);
			if( $num_rows > 0){
				echo "<br> \r\n" . '                <table border=0 cellspacing=1 class=sortable width=1200px>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>Waktu Penolakan</td>' . "\r\n" . '                <td>Ditolak Oleh</td>' . "\r\n" . '                <td>Nama Barang</td>' . "\r\n" . '                <td>Satuan</td>' .'                <td>Jumlah Asal</td>' . "\r\n\t\t\t\t" . '<td>Jumlah Approve</td>' . "\r\n" . '                <td>Alasan</td>' . "\r\n" . '                </thead>' . "\r\n" . '                ';
				echo "\r\n" . '                <tbody>';
				while ($res = mysql_fetch_assoc($qDet)) {
					echo '<tr class=rowcontent style=\'cursor:pointer;\' >' . "\r\n" . '                                <td>'.$res['mod_stamp'].'</td>' . "\r\n" . '                                <td>'.$res['namakaryawan'].'</td>' . "\r\n" . '                                <td>'.$res['namabarang'].'</td><td>'.$res['satuan'].'</td>' . "\r\n" . '                                <td align=center>'.$res['jml_asal'].'</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td align=center>'.$res['jml_approve'].'</td>' . "\r\n" . '                                <td align=center>'.$res['alasan'].'</td>' . '                                </tr>';
				}
				echo '</tbody></table>';
			}

			echo '</div><br />';
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
		$sData = "select sum(jumlah) as jmlh,kodebarang from  $dbname.bgt_budget_detail ".
				"where kodebarang like '" . substr($_POST['kdBarang'], 0, 3) . "%' ".
				"and tahunbudget='" . $_POST['thnAnggaran'] . "' and kodeorg like '" . $_POST['unit'] . "' group by kodebarang";

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;
		$row = mysql_num_rows($qData);

		if ($row == 0) {
			$tab .= '<tr class=rowcontent><td colspan=4>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
		}
		else {
			while ($rData = mysql_fetch_assoc($qData)) {
				$sSdhi = "select sum(jumlahpesan) as sdhi from  $dbname. log_po_vw ".
						"where nopp like '%" . $_POST['unit'] . "%' and kodebarang='" . $rData['kodebarang'] . "' ".
						"and substr(tanggal,1,4)='" . $_POST['thnAnggaran'] . "'";

				#exit(mysql_error($conn));
				($qSdhi = mysql_query($sSdhi)) || true;
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
		$str = "select * from  $dbname.log_5masterbarang ".
				"where namabarang like '%" . $txtfind . "%' or kodebarang like '%" . $txtfind . "%' ";

		if ($res = mysql_query($str)) {
			echo "\r\n" . '          <fieldset>' . "\r\n" . '        <legend>Result</legend>' . "\r\n" . '        <div style="overflow:auto; height:300px;" >' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                 <tr class=rowheader>' . "\r\n" . '                                 <td class=firsttd>' . "\r\n" . '                                 No.' . "\r\n" . '                                 </td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                                 </tr>' . "\r\n" . '                                 </thead>' . "\r\n" . '                                 <tbody>';
			$no = 0;

			while ($bar = mysql_fetch_object($res)) {
				$no += 1;
				$saldoqty = 0;
				$str1 = "select sum(saldoqty) as saldoqty from  $dbname.log_5masterbarangdt ".
						"where kodebarang='" . $bar->kodebarang . "' ".
						"and kodeorg='" . $_SESSION['empl']['kodeorganisasi'] . "'";
				$res1 = mysql_query($str1);

				while ($bar1 = mysql_fetch_object($res1)) {
					$saldoqty = $bar1->saldoqty;
				}

				$qtynotpostedin = 0;
				$str2 = "select sum(b.jumlah) as jumlah,b.kodebarang ".
						"FROM  $dbname.log_transaksiht a ".
						"left join  $dbname.log_transaksidt b on a.notransaksi=b.notransaksi ".
						"where kodept='" . $_SESSION['empl']['kodeorganisasi'] . "' ".
						"and b.kodebarang='" . $bar->kodebarang . "' ".
						"and a.tipetransaksi<5 ".
						"and a.post=0 group by kodebarang";
				$res2 = mysql_query($str2);

				while ($bar2 = mysql_fetch_object($res2)) {
					$qtynotpostedin = $bar2->jumlah;
				}

				if ($qtynotpostedin == '') {
					$qtynotpostedin = 0;
				}

				$qtynotposted = 0;
				$str2 = "select sum(b.jumlah) as jumlah,b.kodebarang ".
						"FROM  $dbname.log_transaksiht a ".
						"left join  $dbname.log_transaksidt b on a.notransaksi=b.notransaksi ".
						"where kodept='" . $_SESSION['empl']['kodeorganisasi'] . "' ".
						"and b.kodebarang='" . $bar->kodebarang . "' ".
						"and a.tipetransaksi>4 and a.post=0 group by kodebarang";
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
					$bgr = " bgcolor='red'";
				}
				else {
					echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setBrg(\'' . htmlspecialchars($bar->kodebarang, ENT_QUOTES, 'UTF-8') . "','" . htmlspecialchars($bar->namabarang, ENT_QUOTES, 'UTF-8') . "','" . htmlspecialchars($bar->satuan, ENT_QUOTES, 'UTF-8') . '\')"; title=\'Click\' >';
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

		$kd = substr($nopp, 17, 2);
		$unit = substr($nopp, 15, 4);
		$str = "select distinct a.karyawanid,b.namakaryawan,b.lokasitugas,a.applikasi ".
			"from $dbname.setup_approval a ".
			"left join $dbname.datakaryawan b on a.karyawanid=b.karyawanid ".
			"where a.karyawanid!='" . $_SESSION['standard']['userid'] . "' and a.applikasi='PP' ";
//		if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
//			$str.=" and a.kodeunit in (SELECT " .
//				"o.kodeorganisasi " .
//				"FROM  organisasi o    " .
//				"WHERE o.induk = '" . $_SESSION['empl']['kodeorganisasi'] . "')";
//		} else {
			$str.="and a.kodeunit='" . $unit . "' ";
//		}
		$str.= " order by b.lokasitugas,b.namakaryawan asc";
//	if ($kd != 'HO') {
//		$str = "select distinct a.karyawanid,b.namakaryawan,b.lokasitugas ".
//			"from $dbname.setup_approval a ".
//			"left join $dbname.datakaryawan b on a.karyawanid=b.karyawanid ".
//			"where a.karyawanid!='" . $_SESSION['standard']['userid'] . "' and a.applikasi='PP' ".
//			"and a.kodeunit='" . $unit . "'  order by b.namakaryawan asc";
//	}
//	else {
//		$str = "select karyawanid,namakaryawan,lokasitugas,bagian ".
//			"from $dbname.datakaryawan ".
//			"where karyawanid!='" . $_SESSION['standard']['userid'] . "' and tipekaryawan='5' ".
//			"and lokasitugas!='' order by namakaryawan asc";
//	}
//		echoMessage(" title ",$str,true);
		#exit(mysql_error($conn));
		($qry = mysql_query($str)) || true;

		while ($rkry = mysql_fetch_assoc($qry)) {
			$optKry .= '<option value=\'' . $rkry['karyawanid'] . '\'>' . '[' . $rkry['lokasitugas']  . '] '.$rkry['namakaryawan'] .'</option>';
		}

		$tab .= '<fieldset style=width:250px;>' . "\r\n" . '                <legend>' . $_SESSION['lang']['pengajuan'] . '</legend>';
		$tab .= '<table cellspacing=1 border=0>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td><input type="text" id="fnopp" name="fnopp" readonly="readonly"  value=\'' . $nopp . '\' /></td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['kepada'] . '</td>' . "\r\n" . '                <td>:</td>' . "\r\n" . '                <td>' . "\r\n" . '                <select id="karywn_id" name="karywn_id">' . "\r\n" . '                ' . $optKry . "\r\n" . '                </select>' . "\r\n" . '                </td>' . "\r\n" . '                </tr>' . "\r\n" . '                <input type="hidden" id="cls_stat" name="cls_stat" value=0 />' . "\r\n" . '                <tr>' . "\r\n" . '                <td colspan="3">' . "\r\n" . '                <button class=mybutton onclick=reset_data_setuju()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '                <button class=mybutton onclick=save_persetujuan() >' . $_SESSION['lang']['diajukan'] . '</button>' . "\r\n" . '                </td>' . "\r\n" . '                </tr>' . "\r\n" . '                </table>' . "\r\n" . '                </fieldset>';
		echo $tab;
		break;
}

?>
