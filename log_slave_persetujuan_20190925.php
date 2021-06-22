<?php


function mailCoy($userid)
{
	$to = getUserEmail($userid);
	$namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);

	if ($_SESSION['language'] == 'EN') {
		$subject = '[Notifikasi] PR Submission for approval, submitted by: ' . $namakaryawan;
		$body = '<html>' . "\r\n" . '                             <head>' . "\r\n" . '                             <body>' . "\r\n" . '                               <dd>Dear Sir/Madam,</dd><br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Today,  ' . date('d-m-Y') . ',  on behalf of ' . $namakaryawan . ' submit a PR, requesting for your approval. To follow up, please follow the link below.' . "\r\n" . '                               <br>' . "\r\n" . '                               <br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Regards,<br>' . "\r\n" . '                               eAgro Plantation Management Software.' . "\r\n" . '                             </body>' . "\r\n" . '                             </head>' . "\r\n" . '                           </html>' . "\r\n" . '                           ';
	}
	else {
		$subject = '[Notifikasi]Persetujuan PP a/n ' . $namakaryawan;
		$body = '<html>' . "\r\n" . '                             <head>' . "\r\n" . '                             <body>' . "\r\n" . '                               <dd>Dengan Hormat,</dd><br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Pada hari ini, tanggal ' . date('d-m-Y') . ' karyawan a/n  ' . $namakaryawan . ' mengajukan Permintaan Pembelian Barang' . "\r\n" . '                               kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.' . "\r\n" . '                               <br>' . "\r\n" . '                               <br>' . "\r\n" . '                               <br>' . "\r\n" . '                               Regards,<br>' . "\r\n" . '                               eAgro Plantation Management Software.' . "\r\n" . '                             </body>' . "\r\n" . '                             </head>' . "\r\n" . '                           </html>' . "\r\n" . '                           ';
	}
}

require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
if (!empty($_POST)) {
	$nopp = $_POST['nopp'];
	$kolom = $_POST['kolom'];
	$comment = $_POST['cm_hasil'];
	$user_id = $_POST['userid'];

	$hasil = $_POST['stat_hasil'];
	$comment = $_POST['cmnt'];
//	$user_id = $_POST['user_id'];

	$method = $_POST['method'];
} else {
	$nopp = $_GET['nopp'];
	$kolom = $_GET['kolom'];
	$comment = $_GET['cm_hasil'];
	$user_id = $_GET['userid'];

	$hasil = $_GET['stat_hasil'];
	$comment = $_GET['cmnt'];
//	$user_id = $_GET['user_id'];

	$method = $_GET['method'];
}
//isset($_POST['nopp']) ? $nopp = $_POST['nopp'] : NULL;
//isset($_POST['kolom']) ? $kolom = $_POST['kolom'] : NULL;
//$kolom_persetujuan = 'hasilpersetujuan' . $kolom;
//isset($_POST['cm_hasil']) ? $comment = $_POST['cm_hasil'] : NULL;
//isset($_POST['userid']) ? $user_id = $_POST['userid'] : NULL;
$kolom_persetujuan = 'hasilpersetujuan' . $kolom;

$tglSkrng = date('Y-m-d');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

switch ($method) {
	case 'insert_forward_pp':
		$hasil_prstjn = 1;
		$sql = "select * from $dbname.log_prapoht where nopp='" . $nopp . "'";
		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);

		if ($res['close'] == PP_MAX_APPROVAL) {
			exit('Error:Sudah di Approved');
		}
		else {
			$index = checkLastApprovalIndexPP($res);
			/*
			 * Jika approval belum 5 orang
			 */
			if ($index!=PP_MAX_APPROVAL) {
				if ($_SESSION['standard']['userid'] != $res['persetujuan' . ($index)]) {
					exit("Error: " . getNamaKaryawan($res['persetujuan' . ($index)]) . " Persetujan ke-$index bukan oleh anda yang buat!");
				} else if ($user_id == $res['persetujuan' . ($index )]) {
					exit('Error: ' . getNamaKaryawan($user_id) . ' Sudah di gunakan');
				} else if ($user_id == $res['dibuat']) {
					exit('Error: ' . getNamaKaryawan($user_id) . ' Pembuat PP');
				} else {
					$kolom=$index;
					$strx = "update $dbname.log_prapoht set " .
						"persetujuan".($index+1)."='" . $user_id . "', " . //delegasikan persetujuan berikut ke user lain
						"hasilpersetujuan$kolom='1' ," .
						"komentar$kolom='" . $comment . "', " .
						"tglp$kolom='" . $tglSkrng . "', " .
						"close='$index' ".
						"where nopp='" . $nopp . "'";
					if ($res = mysql_query($strx)) {
					} else {
						echo $strx;
						echo ' Gagal,' . addslashes(mysql_error($conn));
					}
				}
			} else {
				$strx = "update $dbname.log_prapoht set " .
					"hasilpersetujuan5='1'," .
					"komentar5='" . $comment . "'," .
					"close='" . PP_MAX_APPROVAL . "',tglp5='" . $tglSkrng . "' where nopp='" . $nopp . "'";
				if ($res = mysql_query($strx)) {
					mailCoy($user_id);
				} else {
					echo $strx;
					echo ' Gagal,' . addslashes(mysql_error($conn));
				}
			}
		}
		break;

		/*
		 $hasil_prstjn = 1;
	$sql = 'select * from ' . $dbname . '.log_prapoht where `nopp`=\'' . $nopp . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);

	if ($res['close'] == 2) {
		exit('Error:Sudah di Approved');
	}
	else if ($res['close'] == 1) {
		if (($res['persetujuan1'] != '') && ($res['persetujuan1'] != 0)) {
			$a = 1;
			$i = 2;

			while ($i < 6) {
				if ($user_id == $res['persetujuan' . $a]) {
					exit('Error: ' . getNamaKaryawan($user_id) . ' Sudah di gunakan');
				}
				else if ($user_id == $res['dibuat']) {
					exit('Error: ' . getNamaKaryawan($user_id) . ' Pembuat PP');
				}
				else {
					if (($res['persetujuan' . $i] == 0) || is_null($res['persetujuan' . $i])) {
						$strx = 'update ' . $dbname . '.log_prapoht set persetujuan' . $i . '=\'' . $user_id . '\',' . "\r\n" . '                                                              ' . $kolom_persetujuan . '=\'1\',komentar' . $kolom . '=\'' . $comment . '\',' . "\r\n" . '                                                              tglp' . $kolom . '=\'' . $tglSkrng . '\' where `nopp`=\'' . $nopp . '\'';

						if ($res = mysql_query($strx)) {
							$sCek = 'select distinct hasilpersetujuan' . $kolom . ' from ' . $dbname . '.log_prapoht' . "\r\n" . '                                                                   where nopp=\'' . $nopp . '\'';

							#exit(mysql_error($conn));
							($qCek = mysql_query($sCek)) || true;
							$rCek = mysql_fetch_assoc($qCek);

							if ($rCek['hasilpersetujuan' . $kolom] == 0) {
								$strx = 'update ' . $dbname . '.log_prapoht set hasilpersetujuan' . $kolom . '=1' . "\r\n" . '                                                                       where nopp=\'' . $nopp . '\'';

								if (mysql_query($strx)) {
									mailCoy($user_id);
									exit();
								}
								else {
									echo $strx;
									echo ' Gagal,' . addslashes(mysql_error($conn));
								}
							}
							else {
								mailCoy($user_id);
								exit();
							}
						}
						else {
							echo $strx;
							echo ' Gagal,' . addslashes(mysql_error($conn));
						}
					}
					else if ($res['persetujuan5'] != '') {
						$strx = 'update ' . $dbname . '.log_prapoht set hasilpersetujuan5=\'1\',komentar5=\'' . $comment . '\',' . "\r\n" . '                                                               close=\'2\',tglp5=\'' . $tglSkrng . '\' where `nopp`=\'' . $nopp . '\'';

						if ($res = mysql_query($strx)) {
							mailCoy($user_id);
							break;
						}

						echo $strx;
						echo ' Gagal,' . addslashes(mysql_error($conn));
					}
				}

				++$a;
				++$i;
			}
		}
	}

	break;
		 */

	case 'insert_close_pp':
		$sql = "select * from $dbname.log_prapoht where nopp='" . $nopp . "'";
		($query = mysql_query($sql));
		$res = mysql_fetch_assoc($query);
//		$res = mysql_fetch_assoc($query);
		if (($res['persetujuan5'] != '') ) { //&& ($user_id == $res['persetujuan5'])) {
			$sql2 = "update $dbname.log_prapoht set ".
				"close=".PP_MAX_APPROVAL.",".
				"komentar5='" . $comment . "',".
				"hasilpersetujuan5=1,".
				"tglp5='" . $tglSkrng . "' ".
				"where nopp='" . $nopp . "'";

			if ($query2 = mysql_query($sql2)) {
				//update quantity nya
				$sql_update_qty_approve = "update $dbname.log_prapodt set ".
										"jumlah=jml_approve ".
										"where nopp='" . $nopp . "' and jml_approve > 0";
				if ($queryupdate_qty_approve = mysql_query($sql_update_qty_approve)) {
					exit();
				}else{
					echo $sql_update_qty_approve;
					exit();
				}
			}
			else {
				echo $sql2;
				exit();
				echo ' Gagal,' . addslashes(mysql_error($conn));
			}
		}
		else {
			if (($res['persetujuan5'] == '') || ($res['persetujuan5'] == 0)) {
				if (($res['close'] == 1) && ($res['dibuat'] != $user_id)) {
					if ($res['persetujuan' . $kolom] == $user_id) {
						$sql2 = 'update $dbname.log_prapoht set close=2,' . "\r\n" . '                                                        komentar' . $kolom . '=\'' . $comment . '\',' . $kolom_persetujuan . '=1,tglp' . $kolom . '=\'' . $tglSkrng . '\' where nopp=\'' . $nopp . '\'';

						if ($query2 = mysql_query($sql2)) {
							$sql_update_qty_approve = 'update $dbname.log_prapodt set jumlah=jml_approve where nopp=\'' . $nopp . '\' and jml_approve > \'0\';';
							if ($queryupdate_qty_approve = mysql_query($sql_update_qty_approve)) {
								exit();
							}else{
								echo ' Gagal XX,' ;
								exit();
							}

						}
						else {
							echo $sql2;
							echo ' Gagal,' . addslashes(mysql_error($conn));
						}
					}
					else {
						echo 'Warning: Anda tidak memiliki autorisasi untuk No PP Ini';
						exit();
					}
				}
			}
			else {
				echo 'Warning: Anda tidak memiliki autorisasi untuk No PP Ini';
				exit();
			}
		}

		break;

	case 'rejected_pp_ex':
		$ardt = 0;
//		$comment = $_POST['comment'];
//		$user_id = $_POST['user_id'];
		$sql = "select* from $dbname.log_prapoht where nopp='" . $nopp . "'";
		$hasil = PP_MAX_APPROVAL;
		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);


		if ( ($res['dibuat'] != $user_id)) {
//			if (($res['close'] == 1) && ($res['dibuat'] != $user_id)) {
			$c = 1;
			$index=checkLastApprovalIndexPP($res);
			if ($res['hasilpersetujuan' .$index]==0) {
				$sql2 = "update $dbname.log_prapoht set " .
					"close='" . $hasil . "'," .
					"komentar$c='" . $comment . "'," .
					"hasilpersetujuan$c='3'," .
					"tglp$c='" . $tglSkrng . "' " .
					"where nopp='" . $nopp . "'";

				if (mysql_query($sql2)) {
					$sql3 = "update $dbname.log_prapodt set ".
						"status='3',ditolakoleh='$user_id' where nopp='$nopp'";

					if (mysql_query($sql3)) {
						$ardt += 1;
					}
				} else {
					echo ' Gagal,' . addslashes(mysql_error($conn));
					echo $sql2;
					exit();
				}
			} else {
				echo 'Warning: You already proceed this  PP';
				exit();
			}
//			while ($c <= PP_MAX_APPROVAL) {
//				if ($res['persetujuan' . $c] != '') {
//					if ((($res['hasilpersetujuan' . $c] == '') || ($res['hasilpersetujuan' . $c] == 0)) && ($res['persetujuan' . $c] == $_SESSION['standard']['userid'])) {
//						$sql2 = "update $dbname.log_prapoht set ".
//							"close='" . $hasil . "',".
//							"komentar$c='" . $comment . "',".
//							"hasilpersetujuan$c='3',".
//							"tglp$c='" . $tglSkrng . "' ".
//							"where nopp='" . $nopp . "'";
//
//						if (mysql_query($sql2)) {
//							$sql3 = 'update $dbname.log_prapodt set status=\'3\',ditolakoleh=\'' . $user_id . '\' where nopp=\'' . $nopp . '\'';
//
//							if (mysql_query($sql3)) {
//								$ardt += 1;
//							}
//						}
//						else {
//							echo ' Gagal,' . addslashes(mysql_error($conn));
//							echo $sql2;
//							exit();
//						}
//					}
//					else if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] != '')) {
//						echo 'Warning: You already proceed this  PP';
//						exit();
//					}
//				}
//
//				++$c;
//			}

			if ($ardt != 0) {
				$sData = "select distinct * from $dbname.log_prapoht where nopp='$nopp'";

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
				$subject = '[Notifikasi] Sebagian atau Seluruhnya PP No :' . $_POST['nopp'] . ' dari ' . $namakaryawan . ' ditolak oleh ' . $nmpnlk;
				$body = '<html>' . "\r\n" . '                                             <head>' . "\r\n" . '                                             <body>' . "\r\n" . '                                               <dd>Dengan Hormat,</dd><br>' . "\r\n" . '                                               <br>' . "\r\n" . '                                               Permintaan pembelian no.' . $_POST['nopp'] . ' ditolak oleh [' . $nmpnlk . '] dengan alasan ' . $rData['komentar' . $_POST['kolom']] . "\r\n" . '                                               <br>' . "\r\n" . '                                               Item yang ditolak adalah : <ul>';
				$sBrg = 'select kodebarang,alasanstatus from $dbname.log_prapodt where nopp=\'' . $nopp . '\' and status=\'3\'';

				#exit(mysql_error($conn));
				($qBrg = mysql_query($sBrg)) || true;

				while ($rBrg = mysql_fetch_assoc($qBrg)) {
					$body .= '<li>' . $nmBarang[$rBrg['kodebarang']] . '</li>';
				}

				$body .= '</ul><br>' . "\r\n" . '                                               <br>' . "\r\n" . '                                               Regards,<br>' . "\r\n" . '                                               eAgro Plantation Management Software.' . "\r\n" . '                                             </body>' . "\r\n" . '                                             </head>' . "\r\n" . '                                           </html>' . "\r\n" . '                                           ';
			}
		}
		else {
			echo 'Warning: You dont have Authorizde for this PP';
			exit();
		}

		break;

	case 'rejected_some_input':
		$nopp = $_POST['nopp'];
		$kode_brg = $_POST['kd_brg'];
		$user_id = $_POST['user_id'];
		$alsnDtolak = $_POST['alsnDtolk'];
		$where = ' nopp=\'' . $nopp . '\' and kodebarang=\'' . $kode_brg . '\'';
		$sCek = "select status from $dbname.log_prapodt where nopp='$nopp' and status='0' ";

		#exit(mysql_error());
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_num_rows($qCek);

		if (1 < $rCek) {
			$sql = 'select * from $dbname.log_prapodt where' . $where;

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$res = mysql_fetch_assoc($query);

			if (($res['status'] == '0') && (($res['ditolakoleh'] == 0) || ($res['ditolakoleh'] == ''))) {
				$sql2 = "update $dbname.log_prapodt set ".
					"status='3',ditolakoleh='$user_id',alasanstatus='$alsnDtolak' where " . $where;

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
				echo 'warning: Already Fill';
				exit();
			}
		}
		else {
			echo 'warning:Item Barang Hanya Satu';
			exit();
		}

		break;

	case 'data_refresh':
		$limit = 20;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;
		$user = $_SESSION['standard']['userid'];
		$str = "SELECT l.*,o.namaorganisasi,
d1.namakaryawan AS namapersetujuan1,
d2.namakaryawan AS namapersetujuan2,
d3.namakaryawan AS namapersetujuan3,
d4.namakaryawan AS namapersetujuan4,
d5.namakaryawan AS namapersetujuan5
FROM log_prapoht l
INNER JOIN organisasi o ON RIGHT(l.nopp,LENGTH(o.kodeorganisasi))=o.kodeorganisasi
LEFT OUTER JOIN datakaryawan d1 ON d1.karyawanid=l.persetujuan1
LEFT OUTER JOIN datakaryawan d2 ON d2.karyawanid=l.persetujuan2
LEFT OUTER JOIN datakaryawan d3 ON d3.karyawanid=l.persetujuan3
LEFT OUTER JOIN datakaryawan d4 ON d4.karyawanid=l.persetujuan4
LEFT OUTER JOIN datakaryawan d5 ON d5.karyawanid=l.persetujuan5 ";
		if ($_SESSION['empl']['tipeinduk'] == 'HOLDING') {
			$str .=
				"where ".//close!=".PP_MAX_APPROVAL." and ".
				"(persetujuan1='" . $user . "' or persetujuan2='" . $user . "' or persetujuan3='" . $user . "' or persetujuan4='" . $user . "' or persetujuan5='" . $user . "') ".
				"ORDER BY tanggal desc ".//hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3,hasilpersetujuan4,hasilpersetujuan5 ASC ".
				"LIMIT " . $offset . "," . $limit;
			$sql = "SELECT count(*) as jmlhrow FROM $dbname.log_prapoht ".
				"where ".//close!=".PP_MAX_APPROVAL." and ".
				"(persetujuan1='" . $user . "' or persetujuan2='" . $user . "' or persetujuan3='" . $user . "' or persetujuan4='" . $user . "' or persetujuan5='" . $user . "')";
		}
		else {
			$str .= "where ".//close!=".PP_MAX_APPROVAL." and ".
				"(persetujuan1='" . $user . "' or persetujuan2='" . $user . "' or persetujuan3='" . $user . "' or persetujuan4='" . $user . "' or persetujuan5='" . $user . "') ".
				"ORDER BY tanggal desc ".//hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3,hasilpersetujuan4,hasilpersetujuan5 ASC ".
				"LIMIT " . $offset . "," . $limit;
			$sql = "SELECT count(*) as jmlhrow FROM  $dbname.log_prapoht ".
				"where ".//close!=".PP_MAX_APPROVAL." and ".
				"(persetujuan1='" . $user . "' or persetujuan2='" . $user . "' or persetujuan3='" . $user . "' or persetujuan4='" . $user . "' or persetujuan5='" . $user . "') ";
		}
		($query = mysql_query($sql));
		while ($jsl = mysql_fetch_object($query)) {
			$jlhbrs = $jsl->jmlhrow;
		}
		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$index = checkLastApprovalIndexPP($bar);
				$no += 1;
				echo "<tr class=rowcontent id='tr_" . $no . "'> ".
					"<td>$no</td>".
					"<td id='td_$no'>" . $bar['nopp'] . "</td>".
					"<td>" . tanggalnormal($bar['tanggal']) .  "</td>".
					"<td>" . $bar['namaorganisasi'] . "</td>".
					"<td align=center> ".
					"	<img src='images/pdf.jpg' class='resicon' width='30' height='30' title='Print' onclick=\"masterPDF('log_prapoht','" . $bar['nopp'] . "','','log_slave_print_log_pp',event);\"> ".
					"	<img src='images/zoom.png' class='resicon' width='30' height='30' title='Preview' onclick=\"previewDetail('" . $bar['nopp'] . "',event);\"></td>";

				if ($bar['close'] == PP_MAX_APPROVAL) {
					$accept = 0;
					$i = 1;

					while ($i <=PP_MAX_APPROVAL) {
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
				else if ($bar['close'] < PP_MAX_APPROVAL) {
					if (($bar['persetujuan' . $index] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $index]==0) ){
						echo "<td><a href=# onclick=\"get_data_pp('" . $bar['nopp'] . "','" . $a . "');\">" . $_SESSION['lang']['approve'] . "</a></td> ".
							"<td><a href=# onclick=\"rejected_pp('" . $bar['nopp'] . "','" . $a . "');\" >" . $_SESSION['lang']['ditolak'] . "</a></td> ".
							"<td><a href=# onclick=\"rejected_some_proses('" . $bar['nopp'] . "','" . $a . "');\" >". $_SESSION['lang']['ditolak_some'] . "</a></td>";


					} else {
						echo '<td colspan=3>&nbsp;</td>';
					}
//					$a = 1;
//					$b = 0;
//					while ($a <= PP_MAX_APPROVAL) {
//						$b = $b + $bar['hasilpersetujuan' . $a];
//						if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($a==$index) ){
//							if ($bar['hasilpersetujuan' . $a]==0) {
//								echo "<td><a href=# onclick=\"get_data_pp('" . $bar['nopp'] . "','" . $a . "');\">" . $_SESSION['lang']['approve'] . "</a></td> ".
//									"<td><a href=# onclick=\"rejected_pp('" . $bar['nopp'] . "','" . $a . "');\" >" . $_SESSION['lang']['ditolak'] . "</a></td> ".
//									"<td><a href=# onclick=\"rejected_some_proses('" . $bar['nopp'] . "','" . $a . "');\" >". $_SESSION['lang']['ditolak_some'] . "</a></td>";
//
//							}
//						} else {
//							echo '<td colspan=3>&nbsp;</td>';
//						}

//						if ($bar['persetujuan' . $a] != '') {
//							if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] != '') && ($bar['hasilpersetujuan' . $a] != 0)) {
//								echo '<td colspan=3>&nbsp;</td>';
//								break;
//							}
//							else if (
//								($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) &&
//								(($bar['hasilpersetujuan' . $a] == '') || ($bar['hasilpersetujuan' . $a] == 0))
//							) {
//								echo "<td><a href=# onclick=\"get_data_pp('" . $bar['nopp'] . "','" . $a . "');\">" . $_SESSION['lang']['approve'] . "</a></td> ".
//									"<td><a href=# onclick=\"rejected_pp('" . $bar['nopp'] . "','" . $a . "');\" >" . $_SESSION['lang']['ditolak'] . "</a></td> ".
//									"<td><a href=# onclick=\"rejected_some_proses('" . $bar['nopp'] . "','" . $a . "');\" >". $_SESSION['lang']['ditolak_some'] . "</a></td>";
//								break;
//							}
//						}
//						++$a;
//					}
//					if ($b==$index) {
//						echo '<td colspan=3>&nbsp;</td>';
//					} else {
//						echo "<td><a href=# onclick=\"get_data_pp('" . $bar['nopp'] . "','" . $a . "');\">" . $_SESSION['lang']['approve'] . "</a></td> ".
//							"<td><a href=# onclick=\"rejected_pp('" . $bar['nopp'] . "','" . $a . "');\" >" . $_SESSION['lang']['ditolak'] . "</a></td> ".
//							"<td><a href=# onclick=\"rejected_some_proses('" . $bar['nopp'] . "','" . $a . "');\" >". $_SESSION['lang']['ditolak_some'] . "</a></td>";
//					}
				}

				$i = 1;

				while ($i <= PP_MAX_APPROVAL) {
					if ( $bar['hasilpersetujuan' . $i]!='') {
						echo "<td><a href=# onclick=\"cek_status_pp('" . $bar['hasilpersetujuan' . $i] . "');\">". $bar['namapersetujuan'.$i] . "</a></td>";
					} else {
						echo '<td>&nbsp;</td>';
					}
					++$i;
				}
				echo '</tr>';
			}

			echo "\r\n" . '                                 <tr><td colspan=13 align=center>' . "\r\n" . '                                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                                </td>' . "\r\n" . '                                </tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;

	case 'data_refresh2':
		$limit = 10;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;

		if ($_SESSION['empl']['tipeinduk'] == 'HOLDING') {
			$str = 'SELECT * FROM $dbname.log_prapoht where  (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\') ORDER BY tanggal DESC LIMIT ' . $offset . ',' . $limit . '';
			$sql = 'SELECT count(*) as jmlhrow FROM $dbname.log_prapoht where  close!=\'2\'and substring(nopp,16,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\'  and (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\') ORDER BY tanggal DESC';
		}
		else {
			$str = 'SELECT * FROM $dbname.log_prapoht where  (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\') ORDER BY tanggal DESC';
			$sql = 'SELECT count(*) as jmlhrow FROM  $dbname.log_prapoht where  (persetujuan1=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan2=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan3=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan4=\'' . $_SESSION['standard']['userid'] . '\' or persetujuan5=\'' . $_SESSION['standard']['userid'] . '\') ORDER BY tanggal DESC';
		}

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;

		while ($jsl = mysql_fetch_object($query)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$koderorg = substr($bar['nopp'], 15, 4);
				$spr = 'select * from  $dbname.organisasi where  kodeorganisasi=\'' . $koderorg . '\' or induk=\'' . $koderorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$no += 1;
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n" . '                                  <td>' . $no . '</td>' . "\r\n" . '                                  <td id=td_' . $no . '>' . $bar['nopp'] . '</td>' . "\r\n" . '                                  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                                  <td>' . $bas->namaorganisasi . '</td>' . "\r\n" . '                                  <td align=center><img src=images/pdf.jpg class=resicon width=\'30\' height=\'30\' title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar['nopp'] . '\',\'\',\'log_slave_print_log_pp\',event);">' . "\r\n" . '                                  <img src=images/zoom.png class=resicon  height=\'30\' title=\'Preview\' onclick="previewDetail(\'' . $bar['nopp'] . '\',event);">     ' . "\r\n" . '                                  </td>';
				$a = 1;

				while ($a < 6) {
					if ($bar['close'] == PP_MAX_APPROVAL) {
						if ($bar['hasilpersetujuan' . $a] == '3') {
							$abc = 3;
						}
						else if ($bar['hasilpersetujuan' . $a] == '1') {
							$abc = 1;
						}
					}
					else if ($bar['close'] < PP_MAX_APPROVAL) {
						if ($bar['persetujuan' . $a] != '') {
							if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && ($bar['hasilpersetujuan' . $a] != '') && ($bar['hasilpersetujuan' . $a] != 0)) {
								echo '<td colspan=3>&nbsp;</td>';
							}
							else if (($bar['persetujuan' . $a] == $_SESSION['standard']['userid']) && (($bar['hasilpersetujuan' . $a] == '') || ($bar['hasilpersetujuan' . $a] == 0))) {
								echo "\r\n" . '                                                                                                           <td><a href=# onclick="get_data_pp(\'' . $bar['nopp'] . '\',\'' . $a . '\')">' . $_SESSION['lang']['approve'] . '</a></td>' . "\r\n" . '                                                                                                                <td><a href=# onclick=rejected_pp(\'' . $bar['nopp'] . '\',\'' . $a . '\') >' . $_SESSION['lang']['ditolak'] . '</a></td>' . "\r\n" . '                                                                                                                <td><a href=# onclick="rejected_some_proses(\'' . $bar['nopp'] . '\',\'' . $a . '\')" >' . "\r\n" . '                                                                                                                ' . $_SESSION['lang']['ditolak_some'] . '</a></td>';
							}
						}
					}

					++$a;
				}

				if ($abc != '') {
					if ($abc == 3) {
						echo '<td colspan=3>' . $_SESSION['lang']['ditolak'] . '</td>';
					}
					else if ($abc == 1) {
						echo '<td colspan=3>' . $_SESSION['lang']['approve'] . '</td>';
					}
				}

				$i = 1;

				while ($i < 6) {
					if ($bar['persetujuan' . $i] != '') {
						$kr = $bar['persetujuan' . $i];
						$sql = 'select * from $dbname.datakaryawan where karyawanid=\'' . $kr . '\'';

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

				echo '</tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
			}

			echo "\r\n" . '                                 <tr><td colspan=13 align=center>' . "\r\n" . '                                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                                </td>' . "\r\n" . '                                </tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;
}

?>
