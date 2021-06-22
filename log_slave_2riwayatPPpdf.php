<?php


include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$nopp = $_GET['nopp'];
$tglSdt = tanggalsystem($_GET['tglSdt']);
$statusPP = $_GET['statPP'];
$periode = $_GET['periode'];
$lokBeli = $_GET['lokBeli'];
$param = $_POST;
$cols = 'no,kodebarang,namabarang,tglPersetujuan,note';
$colArr = explode(',', $cols);
$title = $_SESSION['lang']['riwayatPP'];
$align = explode(',', 'L,L,R,R,L');
$length = explode(',', '10,15,20,4,35');

switch ($proses) {
case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $conn;
			global $dbname;
			global $align;
			global $length;
			global $colArr;
			global $title;
			global $nopp;
			global $tglSdt;
			global $statPP;
			$kdorg = explode('/', $nopp);
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 15;

			if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
				$path = 'images/SSP_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
				$path = 'images/MI_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
				$path = 'images/HS_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
				$path = 'images/BM_logo.jpg';
			}

			$this->Image($path, $this->lMargin, $this->tMargin, 70);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(100);
			$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->SetFont('Arial', '', 8);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, $_SESSION['standard']['username'], '', 0, 'L');
			$this->Ln();
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 0, 'L');
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['riwayatPP']), 0, 1, 'C');
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Ln();
			$this->Cell((3 / 100) * $width, $height, 'No.', 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['nopp'], 1, 0, 'C', 1);
			$this->Cell((18 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((18 / 100) * $width, $height, $_SESSION['lang']['status'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['nopo'], 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['tanggal'] . ' PO', 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['status'] . ' PO', 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['namasupplier'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rapbNo'], 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'pt', 'Legal');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);

	if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
	}
	else {
		$sortLokasi = 'and a.nopp like \'%' . $_SESSION['empl']['lokasitugas'] . '%\'';
	}

	if (($nopp == '') && ($tglSdt == '') && ($statusPP == '') && ($periode == '') && ($lokBeli == '')) {
		$tglSkrng = date('Y-m');
		$sql = 'select a.*,b.* FROM ' . $dbname . '.log_prapodt a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp where substr(b.tanggal,1,7)=\'' . $tglSkrng . '\' ' . $sortLokasi . '  order by a.nopp desc ';
	}
	else {
		if ($tglSdt != '') {
			$where = ' where b.tanggal=\'' . $tglSdt . '\'';
		}
		else {
			$where = ' where a.nopp!=\'\'';
		}

		if ($statusPP != '') {
			if ($statusPP == '3') {
				if ($tglSdt == '') {
					if ($periode == '') {
						exit('Error: Period is empty');
					}
					else {
						$where = 'where  a.create_po!=\'\'  and substr(b.tanggal,1,7) = \'' . $periode . '\' ';
					}
				}
			}
			else if ($statusPP == '4') {
				if ($tglSdt == '') {
					if ($periode == '') {
						exit('Error: Period is empty');
					}
					else {
						$where = 'where  a.create_po=\'\'  and substr(b.tanggal,1,7) = \'' . $periode . '\'';
					}
				}
			}
			else {
				if (($statusPP == '1') || ($statusPP != '2')) {
					if ($tglSdt == '') {
						if ($periode != '') {
							$where = ' where b.close=\'' . $statusPP . '\' and substr(b.tanggal,1,7) = \'' . $periode . '\'';
						}
						else {
							$where .= ' and b.close=\'' . $statusPP . '\'';
						}
					}
				}
				else if ($statusPP == '2') {
					if ($tglSdt == '') {
						if ($periode != '') {
							$where = ' where b.close=\'2\' and substr(b.tanggal,1,7) = \'' . $periode . '\'';
						}
						else {
							$where .= ' and b.close=\'2\'';
						}
					}
				}
			}
		}
		else if ($periode != '') {
			if ($tglSdt == '') {
				$where = ' where substr(b.tanggal,1,7)=\'' . $periode . '\'';
			}
		}

		if ($lokBeli != '') {
			$where .= ' and lokalpusat= \'' . $lokBeli . '\'';
		}

		if ($nopp != '') {
			$where .= ' and b.nopp like \'%' . $nopp . '%\'';
		}

		$sql = 'select a.*,b.* FROM ' . $dbname . '.log_prapodt a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp  ' . $where . ' ' . $sortLokasi . ' order by a.nopp desc ';
	}

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		#exit(mysql_error());
		($query2 = mysql_query($sql)) || true;

		while ($res = mysql_fetch_assoc($query2)) {
			$no += 1;

			if ($res['close'] == '2') {
				if (!is_null($res['tglp5'])) {
					$tgl = tanggalnormal($res['tglp5']);
				}
				else if (!is_null($res['tglp4'])) {
					$tgl = tanggalnormal($res['tglp4']);
				}
				else if (!is_null($res['tglp3'])) {
					$tgl = tanggalnormal($res['tglp3']);
				}
				else if (!is_null($res['tglp2'])) {
					$tgl = tanggalnormal($res['tglp2']);
				}
				else if (!is_null($res['tglp1'])) {
					$tgl = tanggalnormal($res['tglp1']);
				}

				if ($res['status'] == '3') {
					$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					$npo = '';
				}
				else if ($res['status'] == '0') {
					$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					$npo = 'Purchasing process';
				}
			}
			else if ($res['close'] == '1') {
				if (!is_null($res['hasilpersetujuan5'])) {
					$tgl = tanggalnormal($res['tglp5']);

					if ($res['hasilpersetujuan5'] == '1') {
						$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan5'] == '0') {
						$statPp = $_SESSION['lang']['wait_approval'];
					}
					else if ($res['hasilpersetujuan5'] == '3') {
						$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
				}
				else if (!is_null($res['hasilpersetujuan4'])) {
					$tgl = tanggalnormal($res['tglp4']);

					if ($res['hasilpersetujuan4'] == '1') {
						$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan4'] == '3') {
						$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan4'] == '0') {
						$statPp = $_SESSION['lang']['wait_approval'];
					}
				}
				else if (!is_null($rTgl['hasilpersetujuan3'])) {
					$tgl = tanggalnormal($res['tglp3']);

					if ($res['hasilpersetujuan3'] == '1') {
						$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan3'] == '3') {
						$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan3'] == '0') {
						$statPp = $_SESSION['lang']['wait_approval'];
					}
				}
				else if (!is_null($res['hasilpersetujuan2'])) {
					$tgl = tanggalnormal($res['tglp2']);

					if ($res['hasilpersetujuan2'] == '1') {
						$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan2'] == '3') {
						$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan2'] == '0') {
						$statPp = $_SESSION['lang']['wait_approval'];
					}
				}
				else if (!is_null($res['hasilpersetujuan1'])) {
					$tgl = tanggalnormal($res['tglp1']);

					if ($res['hasilpersetujuan1'] == '1') {
						$statPp = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan1'] == '3') {
						$statPp = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
					else if ($res['hasilpersetujuan1'] == '0') {
						$statPp = $_SESSION['lang']['wait_approval'];
					}
				}
			}
			else {
				if (($res['close'] == 0) || ($res['close'] == '')) {
					$statPp = $_SESSION['lang']['belumdiajukan'];
				}
			}

			$statPo = '';
			$sBrg = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$sDet = 'select nopo from ' . $dbname . '.log_podt  where nopp=\'' . $res['nopp'] . '\' and kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qDet = mysql_query($sDet)) || true;
			$rDet = mysql_fetch_assoc($qDet);
			$sPo2 = 'select * from ' . $dbname . '.log_poht  where nopo=\'' . $rDet['nopo'] . '\'';

			#exit(mysql_error());
			($qPo2 = mysql_query($sPo2)) || true;
			$rPo2 = mysql_fetch_assoc($qPo2);

			if ($rDet['nopo'] != '') {
				if ($rPo2['tanggal'] != -0) {
					$tglPO = tanggalnormal($rPo2['tanggal']);
				}
				else {
					$tglPO = '';
				}

				$sSup = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $rPo2['kodesupplier'] . '\'';

				#exit(mysql_error());
				($qSup = mysql_query($sSup)) || true;
				$rSup = mysql_fetch_assoc($qSup);
				$sRapb = 'select notransaksi,tanggal from ' . $dbname . '.log_transaksiht where nopo=\'' . $rPo2['nopo'] . '\'';

				#exit(mysql_error());
				($qRapb = mysql_query($sRapb)) || true;
				$rRapb = mysql_fetch_assoc($qRapb);

				if ($rPo2['statuspo'] == '3') {
					$tglR = tanggalnormal($rRapb['tglrelease']);
					$statPo = 'Brg Sdh Di gudang ,' . $tglR;
				}
				else if ($rPo2['statuspo'] == '2') {
					$accept = 0;
					$i = 1;

					while ($i < 4) {
						if ($rPo2['hasilpersetujuan' . $i] == '2') {
							$accept = 2;
							$tgl = tanggalnormal($rPo2['tglp' . $i]);
							break;
						}

						if ($rPo2['hasilpersetujuan' . $i] == '1') {
							$accept = 1;
						}

						++$i;
					}

					if ($accept == '2') {
						$statPo = $_SESSION['lang']['ditolak'] . ',' . $tgl;
					}
					else if ($accept == '1') {
						$statPo = $_SESSION['lang']['disetujui'] . ',' . $tgl;
					}
				}
				else if ($rPo2['statuspo'] == '1') {
					$i = 1;

					while ($i < 4) {
						if ($rPo2['tglp' . $i] == '') {
							$j = $i - 1;

							if ($j != 0) {
								$tgl = tanggalnormal($rPo2['tglp' . $j]);

								if ($rPo2['hasilpersetujuan' . $j] == 2) {
									$statPo = 'Persetujuan' . $j . ', ' . $_SESSION['lang']['ditolak'] . $tgl;
								}
								else if ($rPo2['hasilpersetujuan' . $j] == 1) {
									$statPo = 'Persetujuan' . $j . ', ' . $_SESSION['lang']['disetujui'] . $tgl;
								}
							}

							break;
						}

						++$i;
					}
				}
				else if ($rPo2['statuspo'] == '0') {
					$statPo = 'Approval Process';
				}

				if ($rPo2['nopo'] == '') {
					$res['lokalpusat'] == 0 ? ' ' : $npo = 'PO Lokal';
				}
				else {
					$npo = $rPo2['nopo'];
				}

				if (($res['hasilpersetujuan1'] == 3) || ($res['hasilpersetujuan2'] == 3) || ($res['hasilpersetujuan3'] == 3) || ($res['hasilpersetujuan4'] == 3) || ($res['hasilpersetujuan5'] == 3) || ($res['status'] == 3)) {
					$npo = '';
				}

				if ($rRapb['tanggal'] != -0) {
					$tglTrma = tanggalnormal($rRapb['tanggal']);
				}
				else {
					$tglTrma = '';
				}
			}
			else {
				$npo = '';
				$tglPO = '';
				$statPo = '';
				$rSup['namasupplier'] = '';
				$rRapb['notransaksi'] = '';
				$rRapb['tanggal'] = '0000-00-00';
			}

			$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
			$pdf->Cell((8 / 100) * $width, $height, $res['nopp'], 1, 0, 'L', 1);
			$pdf->Cell((18 / 100) * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
			$pdf->Cell((18 / 100) * $width, $height, $statPp, 1, 0, 'L', 1);
			$pdf->Cell((10 / 100) * $width, $height, $npo, 1, 0, 'L', 1);
			$pdf->Cell((5 / 100) * $width, $height, $tglPO, 1, 0, 'C', 1);
			$pdf->Cell((12 / 100) * $width, $height, $statPo, 1, 0, 'L', 1);
			$pdf->Cell((10 / 100) * $width, $height, $rSup['namasupplier'], 1, 0, 'L', 1);
			$pdf->Cell((10 / 100) * $width, $height, $rRapb['notransaksi'], 1, 0, 'L', 1);
			$pdf->Cell((5 / 100) * $width, $height, $tglTrma, 1, 1, 'C', 1);
		}
	}
	else {
		$pdf->MultiCell((108 / 100) * $width, $height, 'Not Found', 1, 1, 'C', 1);
	}

	$pdf->Output();
	break;

case 'excel':
	break;
}

?>
