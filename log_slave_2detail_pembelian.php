<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$kdPt = $_POST['kdPt'];
$kdSup = $_POST['kdSup'];
$kdUnit = $_POST['kdUnit'];
$tglDr = tanggalsystem($_POST['tglDr']);
$tanggalSampai = tanggalsystem($_POST['tanggalSampai']);
$lokBeli = $_POST['lokBeli'];

switch ($proses) {
case 'getKdorg':
	$optorg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sOrg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\'';

	#exit(mysql_error());
	($qOrg = mysql_query($sOrg)) || true;

	while ($rOrg = mysql_fetch_assoc($qOrg)) {
		$optorg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
	}

	echo $optorg;
	break;

case 'preview':
	if ($kdPt == '') {
		echo 'warning:Perusahaan tidak boleh kosong';
		exit();
	}
	if (($tglDr == '') || ($tanggalSampai == '')) {
		echo 'warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong';
		exit();
	}
	else {
		if ($kdPt != '') {
			$where .= ' and a.kodeorg=\'' . $kdPt . '\'';
			$where1 .= ' and kodeorg=\'' . $kdPt . '\'';
		}

		if ($kdUnit != '') {
			$where .= ' and substring(b.nopp,16,4)=\'' . $kdUnit . '\'';
			$where1 .= ' and substring(nopp,16,4)=\'' . $kdUnit . '\'';
		}else{
			$where .= ' and substring(b.nopp,16,4) like \'' .$kdPt. '%\'';
			$where1 .= ' and substring(nopp,16,4) like \'' .$kdPt. '%\'';
		}

		if ($kdSup != '') {
			$where .= ' and a.kodesupplier=\'' . $kdSup . '\'';
			$where1 .= ' and kodesupplier=\'' . $kdSup . '\'';

		}

		if (($tglDr != '') || ($tanggalSampai != '')) {
			$where .= ' and (a.tanggal between \'' . date('Y-m-d', strtotime($_POST['tglDr'])) . '\' and \'' . date('Y-m-d', strtotime($_POST['tanggalSampai'])). '\')';
			$where1 .= ' and (tanggal between \'' . date('Y-m-d', strtotime($_POST['tglDr'])) . '\' and \'' . date('Y-m-d', strtotime($_POST['tanggalSampai'])). '\')';
		}

		if ($lokBeli != '') {
			$where .= ' and lokalpusat=\'' . $lokBeli . '\'';
		}
	}
	
	echo '<table cellspacing=1 border=0 class=sortable>' . "" . '<thead class=rowheader>' . "" . 
	'<tr>' . "" . '<td>No.</td>' . "" . '<td>' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['total'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['prmntaanPembelian'] . ' </td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['bapb'] . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';
	$data = array();
	$sData = 'select a.kodesupplier from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.statuspo>1 ' . $where . ' group by kodesupplier order by a.tanggal asc';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$data[] = $rData;
	}

	foreach ($data as $row => $dt) {
		$no += 1;
		$afdC = false;
		$blankC = false;
		$sList = "select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where." ";
		
		#exit(mysql_error());
		$qList=mysql_query($sList) or die(mysql_error());
		$grandTot = array();

		while ($rList = mysql_fetch_assoc($qList)) {
			$sRow = "select a.nopo from ". $dbname .".log_podt a inner join ". $dbname .".log_poht b on a.nopo=b.nopo where b.kodesupplier='". $dt['kodesupplier'] ."' and b.nopo!='NULL' ".$where1."  ";

			#exit(mysql_error());
			($qRow = mysql_query($sRow)) || true;
			$rRow = mysql_num_rows($qRow);
			$tmpRow = $rRow - 1;
			$sNm = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $dt['kodesupplier'] . '\'';

			#exit(mysql_error());
			($qNm = mysql_query($sNm)) || true;
			$rNm = mysql_fetch_assoc($qNm);
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rList['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);

			if ($rList['matauang'] != 'IDR') {
				$sKurs = 'select kurs from ' . $dbname . '.setup_matauangrate where kode=\'' . $rList['matauang'] . '\' and daritanggal=\'' . $rList['tanggal'] . '\'';

				#exit(mysql_error());
				($qKurs = mysql_query($sKurs)) || true;
				$rKurs = mysql_fetch_assoc($qKurs);

				if ($rKurs != '') {
					$hrg = $rKurs['kurs'] * $rList['hargasatuan'];
					$totHrg = $rList['jumlahpesan'] * $hrg;
				}
				else if ($rList['matauang'] == 'USD') {
					$hrg = $rList['hargasatuan'] * 8850;
					$totHrg = $rList['jumlahpesan'] * $hrg;
					$rList['matauang'] = 'IDR';
				}
				else if ($rList['matauang'] == 'EUR') {
					$hrg = $rList['hargasatuan'] * 12643;
					$totHrg = $rList['jumlahpesan'] * $hrg;
					$rList['matauang'] = 'IDR';
				}
				else {
					if (($rList['matauang'] == '') || ($rList['matauang'] == 'NULL')) {
						$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
					}
				}
			}
			else {
				$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
			}

			$grandTot['total'] += $totHrg;

			if ($rList['nopp'] != '') {
				$sTgl = 'select tanggal from ' . $dbname . '.log_prapoht where nopp=\'' . $rList['nopp'] . '\'';

				#exit(mysql_error());
				($qTgl = mysql_query($sTgl)) || true;
				$rTgl = mysql_fetch_assoc($qTgl);
				if (($rTgl['tanggal'] != '') || ($rTgl['tanggal'] != '000-00-00')) {
					$tglPP = tanggalnormal($rTgl['tanggal']);
				}
				else {
					$tglPP = '';
				}
			}
			else {
				$tglPP = '';
			}

			if ($rList['nopo'] != '') {
				$sTgl2 = 'select tanggal from ' . $dbname . '.log_transaksiht where nopo=\'' . $rList['nopo'] . '\' and tipetransaksi=1';

				#exit(mysql_error());
				($qTgl2 = mysql_query($sTgl2)) || true;
				$rTgl2 = mysql_fetch_assoc($qTgl2);

				if ($rTgl2['tanggal'] != '') {
					$tglBapb = tanggalnormal($rTgl2['tanggal']);
				}
				else {
					$tglBapb = '';
				}
			}
			else {
				$tglBapb = '';
			}

			$tab .= '<tr class=\'rowcontent\'>';

			if ($afdC == false) {
				$tab .= '<td>' . $no . '</td>';
				$tab .= '<td value=\'' . $dt['kodesupplier'] . '\'>' . $rNm['namasupplier'] . '</td>';
				$afdC = true;
			}
			else if ($blankC == false) {
				$tab .= '<td rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$blankC = true;
			}

			$tab .= '<td>' . $rList['nopo'] . '</td>';
			$tab .= '<td>' . tanggalnormal($rList['tanggal']) . '</td>';
			$tab .= '<td>' . $rList['kodebarang'] . '</td>';
			$tab .= '<td>' . $rBrg['namabarang'] . '</td>';
			$tab .= '<td align=center>' . $rList['matauang'] . '</td>';
			$tab .= '<td align=right>' . $rList['jumlahpesan'] . '</td>';
			$tab .= '<td align=center>' . $rList['satuan'] . '</td>';
			$tab .= '<td align=right>' . number_format($totHrg, 2) . '</td>';
			$tab .= '<td>' . $tglPP . '</td>';
			$tab .= '<td>' . $tglBapb . '</td>';
			$tab .= '</tr>';
		}

		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td colspan=\'9\' align=\'right\'><b>Sub Total </b></td>';
		$tab .= '<td align=right>' . number_format($grandTot['total'], 2) . '</td>';
		$tab .= '<td colspan=\'2\' >&nbsp;</td>';
		$tab .= '</tr>';
	}

	echo $tab;
	break;

case 'pdf':
	$kdPt = $_GET['kdPt'];
	$kdSup = $_GET['kdSup'];
	$kdUnit = $_GET['kdUnit'];
	$tglDari = tanggalsystem($_GET['tglDr']);
	$tanggalSampai = tanggalsystem($_GET['tanggalSampai']);
	$lokBeli = $_GET['lokBeli'];
	if (($tglDari == '') || ($tanggalSampai == '')) {
		echo 'warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong';
		exit();
	}
	else {
		if ($kdPt != '') {
			$where .= ' and a.kodeorg=\'' . $kdPt . '\'';
			$where1 .= ' and kodeorg=\'' . $kdPt . '\'';
		}

		if ($kdUnit != '') {
			$where .= ' and substring(b.nopp,16,4)=\'' . $kdUnit . '\'';
			$where1 .= ' and substring(nopp,16,4)=\'' . $kdUnit . '\'';
		}

		if ($kdSup != '') {
			$where .= ' and a.kodesupplier=\'' . $kdSup . '\'';
			$where1 .= ' and kodesupplier=\'' . $kdSup . '\'';
		}

		if (($tglDr != '') || ($tanggalSampai != '')) {
			$where .= ' and (a.tanggal between \'' . $tglDari . '\' and \'' . tanggalsystem($_GET['tanggalSampai']) . '\')';
			$where1 .= ' and (tanggal between \'' . $tglDari . '\' and \'' . tanggalsystem($_GET['tanggalSampai']) . '\')';
		}

		if ($lokBeli != '') {
			$where .= ' and lokalpusat=\'' . $lokBeli . '\'';
		}
	}
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
			global $kdPt;
			global $kdSup;
			global $kdUnit;
			global $tglDari;
			global $tanggalSampai;
			global $where;
			global $isi;
			$isi = array();

			if ($kdPt == '') {
				$pt = 'MHO';
			}
			else {
				$pt = $kdPt;
			}

			$sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';

			#exit(mysql_error());
			($qAlamat = mysql_query($sAlmat)) || true;
			$rAlamat = mysql_fetch_assoc($qAlamat);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 11;

		//	if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
				$path = 'images/SSP_logo.jpg';
		/*	}
			else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
				$path = 'images/MI_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
				$path = 'images/HS_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
				$path = 'images/BM_logo.jpg';
			}
*/
			$this->Image($path, $this->lMargin, $this->tMargin, 70);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $rAlamat['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 11);
			$this->Cell($width, $height, $_SESSION['lang']['detPemb'], 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell($width, $height, 'Periode : ' . $_GET['tglDr'] . ' s.d. ' . $_GET['tanggalSampai'], 0, 1, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['supplier'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['nopo'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
			$this->Cell((22 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['matauang'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, $_SESSION['lang']['tanggal'] . ' PP', 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, $_SESSION['lang']['tanggal'] . ' BAPB', 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('L', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 9;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$sData = 'select a.kodesupplier from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.statuspo>1 ' . $where . ' group by kodesupplier order by a.tanggal asc';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$isi[] = $rData;
	}

	$totalAll = array();

	foreach ($isi as $test => $dt) {
		$no += 1;
		$i = 0;
		$afdC = false;
		$sNm = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $dt['kodesupplier'] . '\'';

		#exit(mysql_error());
		($qNm = mysql_query($sNm)) || true;
		$rNm = mysql_fetch_assoc($qNm);

		if ($afdC == false) {
			$pdf->Cell((3 / 100) * $width, $height, $no, 'TLR', 0, 'C', 1);
			$pdf->Cell((15 / 100) * $width, $height, $rNm['namasupplier'], 'TLR', 0, 'C', 1);
		}

		$sList = 'select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.kodesupplier=\'' . $dt['kodesupplier'] . '\' and b.nopo!=\'NULL\' '.$where.' ';

		#exit(mysql_error());
		($qList = mysql_query($sList)) || true;
		$grandTot = array();

		while ($rList = mysql_fetch_assoc($qList)) {
			++$limit;
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rList['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);

			if ($rList['matauang'] != 'IDR') {
				$sKurs = 'select kurs from ' . $dbname . '.setup_matauangrate where kode=\'' . $rList['matauang'] . '\' and daritanggal=\'' . $rList['tanggal'] . '\'';

				#exit(mysql_error());
				($qKurs = mysql_query($sKurs)) || true;
				$rKurs = mysql_fetch_assoc($qKurs);

				if ($rKurs != '') {
					$hrg = $rKurs['kurs'] * $rList['hargasatuan'];
					$totHrg = $rList['jumlahpesan'] * $hrg;
				}
				else if ($rList['matauang'] == 'USD') {
					$hrg = $rList['hargasatuan'] * 8850;
					$totHrg = $rList['jumlahpesan'] * $hrg;
					$rList['matauang'] = 'IDR';
				}
				else if ($rList['matauang'] == 'EUR') {
					$hrg = $rList['hargasatuan'] * 12643;
					$totHrg = $rList['jumlahpesan'] * $hrg;
					$rList['matauang'] = 'IDR';
				}
				else {
					if (($rList['matauang'] == '') || ($rList['matauang'] == 'NULL')) {
						$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
					}
				}
			}
			else {
				$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
			}

			$grandTot['total'] += $totHrg;

			if ($rList['nopp'] != '') {
				$sTgl = 'select tanggal from ' . $dbname . '.log_prapoht where nopp=\'' . $rList['nopp'] . '\'';

				#exit(mysql_error());
				($qTgl = mysql_query($sTgl)) || true;
				$rTgl = mysql_fetch_assoc($qTgl);
				if (($rTgl['tanggal'] != '') || ($rTgl['tanggal'] != '000-00-00')) {
					$tglPP = tanggalnormal($rTgl['tanggal']);
				}
				else {
					$tglPP = '';
				}
			}
			else {
				$tglPP = '';
			}

			if ($rList['nopo'] != '') {
				$sTgl2 = 'select tanggal from ' . $dbname . '.log_transaksiht where nopo=\'' . $rList['nopo'] . '\' and tipetransaksi=1';

				#exit(mysql_error());
				($qTgl2 = mysql_query($sTgl2)) || true;
				$rTgl2 = mysql_fetch_assoc($qTgl2);

				if ($rTgl2['tanggal'] != '') {
					$tglBapb = tanggalnormal($rTgl2['tanggal']);
				}
				else {
					$tglBapb = '';
				}
			}
			else {
				$tglBapb = '';
			}

			if ($afdC == true) {
				$i = 0;
				$pdf->Cell((3 / 100) * $width, $height, '', 'LR', $align[$i], 1);
				$pdf->Cell((15 / 100) * $width, $height, '', 'LR', $align[$i], 1);
				++$i;
			}
			else {
				$afdC = true;
			}

			$pdf->Cell((12 / 100) * $width, $height, $rList['nopo'], 1, 0, 'L', 1);
			$pdf->Cell((6 / 100) * $width, $height, tanggalnormal($rList['tanggal']), 1, 0, 'C', 1);
			$pdf->Cell((22 / 100) * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
			$pdf->Cell((6 / 100) * $width, $height, $rList['matauang'], 1, 0, 'C', 1);
			$pdf->Cell((6 / 100) * $width, $height, $rList['jumlahpesan'], 1, 0, 'R', 1);
			$pdf->Cell((6 / 100) * $width, $height, $rList['satuan'], 1, 0, 'C', 1);
			$pdf->Cell((10 / 100) * $width, $height, number_format($totHrg, 2), 1, 0, 'R', 1);
			$pdf->Cell((7 / 100) * $width, $height, $tglPP, 1, 0, 'C', 1);
			$pdf->Cell((7 / 100) * $width, $height, $tglBapb, 1, 1, 'C', 1);
		}

		$totalAll['totalSemua'] += $grandTot['total'];
		$pdf->Cell((76 / 100) * $width, $height, 'Sub Total', 1, 0, 'C', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($grandTot['total'], 2), 1, 0, 'R', 1);
		$pdf->Cell((14 / 100) * $width, $height, '', 1, 1, 'R', 1);
	}

	$pdf->Cell((76 / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($totalAll['totalSemua'], 2), 1, 0, 'R', 1);
	$pdf->Cell((14 / 100) * $width, $height, '', 1, 1, 'R', 1);
	$pdf->Cell($width, $height, terbilang($totalAll['totalSemua'], 2), 1, 1, 'C', 1);
	$pdf->Output();
	break;

case 'excel':
	$kdPt = $_GET['kdPt'];
	$kdSup = $_GET['kdSup'];
	$kdUnit = $_GET['kdUnit'];
	$tglDr = tanggalsystem($_GET['tglDr']);
	$tanggalSampai = tanggalsystem($_GET['tanggalSampai']);
	$lokBeli = $_GET['lokBeli'];
	$data = array();
	if (($tglDr == '') || ($tanggalSampai == '')) {
		echo 'warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong';
		exit();
	}
	else {
		if ($kdPt != '') {
			$where .= ' and a.kodeorg=\'' . $kdPt . '\'';
			$where1 .= ' and kodeorg=\'' . $kdPt . '\'';
		}

		if ($kdUnit != '') {
			$where .= ' and substring(b.nopp,16,4)=\'' . $kdUnit . '\'';
			$where1 .= ' and substring(nopp,16,4)=\'' . $kdUnit . '\'';
		}

		if ($kdSup != '') {
			$where .= ' and a.kodesupplier=\'' . $kdSup . '\'';
			$where1 .= ' and kodesupplier=\'' . $kdSup . '\'';
		}

		if (($tglDr != '') || ($tanggalSampai != '')) {
			$where .= ' and (a.tanggal between \'' . $tglDr . '\' and \'' . tanggalsystem($_GET['tanggalSampai']) . '\')';
			$where1 .= ' and (tanggal between \'' . $tglDr . '\' and \'' . tanggalsystem($_GET['tanggalSampai']) . '\')';

		}

		if ($lokBeli != '') {
			$where .= ' and lokalpusat=\'' . $lokBeli . '\'';
		}
	}

	$tab .= "\r\n\t" . '<table>' . "\r\n\t\t\t" . '<tr><td colspan=12 align=center>LAPORAN DETAIL PEMBELIAN</td></tr>' . "\r\n\t\t\t" . '<tr><td colspan=12 align=center>Periode : ' . $_GET['tglDr'] . ' s.d. ' . $_GET['tanggalSampai'] . '</td></tr>' . "\r\n\t\t\t" . '</table>' . "\r\n\t" . '<table cellspacing=1 border=1 class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>No.</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['prmntaanPembelian'] . '</td>' . "\r\n\t\t" . '<td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['bapb'] . '</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';
	$sData = 'select a.kodesupplier from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.statuspo>1 ' . $where . ' group by kodesupplier order by a.tanggal asc';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$data[] = $rData;
	}

	$totalAll = array();

	foreach ($data as $row => $dt) {
		$no += 1;
		$afdC = false;
		$blankC = false;
		$sList = "select distinct a.tanggal,a.matauang, b.kodebarang,b.satuan,b.nopo,b.jumlahpesan, b.nopp,b.hargasatuan,a.ppn from ". $dbname . ".log_poht a left join " . $dbname .".log_podt b on a.nopo=b.nopo where a.kodesupplier='". $dt['kodesupplier'] ."' and b.nopo!='NULL' ".$where."";

		#exit(mysql_error());
		($qList = mysql_query($sList)) || true;
//		$grandTot = array();

		while ($rList = mysql_fetch_assoc($qList)) {
			$sRow = "select a.nopo from " . $dbname .".log_podt a inner join ". $dbname .".log_poht b on a.nopo=b.nopo where b.kodesupplier='". $dt['kodesupplier'] ."' and b.nopo!='NULL' ".$where1."";

			#exit(mysql_error());
			($qRow = mysql_query($sRow)) || true;
			$rRow = mysql_num_rows($qRow);
			$tmpRow = $rRow - 1;
			$sNm = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $dt['kodesupplier'] . '\'';

			#exit(mysql_error());
			($qNm = mysql_query($sNm)) || true;
			$rNm = mysql_fetch_assoc($qNm);
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rList['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);

			if ($rList['matauang'] != 'IDR') {
				$sKurs = 'select kurs from ' . $dbname . '.setup_matauangrate where kode=\'' . $rList['matauang'] . '\' and daritanggal=\'' . $rList['tanggal'] . '\'';

				#exit(mysql_error());
				($qKurs = mysql_query($sKurs)) || true;
				$rKurs = mysql_fetch_assoc($qKurs);

				if ($rKurs != '') {
					$hrg = $rKurs['kurs'] * $rList['hargasatuan'];
					$totHrg = $rList['jumlahpesan'] * $hrg;
				}
				else if ($rList['matauang'] == 'USD') {
					$hrg = $rList['hargasatuan'] * 8850;
					$totHrg = $rList['jumlahpesan'] * $hrg;
				}
				else if ($rList['matauang'] == 'EUR') {
					$hrg = $rList['hargasatuan'] * 12643;
					$totHrg = $rList['jumlahpesan'] * $hrg;
				}
				else {
					if (($rList['matauang'] == '') || ($rList['matauang'] == 'NULL')) {
						$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
					}
				}
			}
			else {
				$totHrg = $rList['jumlahpesan'] * $rList['hargasatuan'];
			}

			$grandTot['total'] += $totHrg;

			if ($rList['nopp'] != '') {
				$sTgl = 'select tanggal from ' . $dbname . '.log_prapoht where nopp=\'' . $rList['nopp'] . '\'';

				#exit(mysql_error());
				($qTgl = mysql_query($sTgl)) || true;
				$rTgl = mysql_fetch_assoc($qTgl);
				if (($rTgl['tanggal'] != '') || ($rTgl['tanggal'] != '000-00-00')) {
					$tglPP = tanggalnormal($rTgl['tanggal']);
				}
				else {
					$tglPP = '';
				}
			}
			else {
				$tglPP = '';
			}

			if ($rList['nopo'] != '') {
				$sTgl2 = 'select tanggal from ' . $dbname . '.log_transaksiht where nopo=\'' . $rList['nopo'] . '\' and tipetransaksi=1';

				#exit(mysql_error());
				($qTgl2 = mysql_query($sTgl2)) || true;
				$rTgl2 = mysql_fetch_assoc($qTgl2);

				if ($rTgl2['tanggal'] != '') {
					$tglBapb = tanggalnormal($rTgl2['tanggal']);
				}
				else {
					$tglBapb = '';
				}
			}
			else {
				$tglBapb = '';
			}

			$tab .= '<tr class=\'rowcontent\'>';

			if ($afdC == false) {
				$tab .= '<td>' . $no . '</td>';
				$tab .= '<td value=\'' . $dt['kodesupplier'] . '\'>' . $rNm['namasupplier'] . '</td>';
				$afdC = true;
			}
			else if ($blankC == false) {
				$tab .= '<td rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$tab .= '<td  rowspan=\'' . $tmpRow . '\'>&nbsp;</td>';
				$blankC = true;
			}

			$tab .= '<td>' . $rList['nopo'] . '</td>';
			$tab .= '<td>' . tanggalnormal($rList['tanggal']) . '</td>';
			$tab .= '<td>' . $rList['kodebarang'] . '</td>';
			$tab .= '<td>' . $rBrg['namabarang'] . '</td>';
			$tab .= '<td align=center>' . $rList['matauang'] . '</td>';
			$tab .= '<td align=right>' . $rList['jumlahpesan'] . '</td>';
			$tab .= '<td align=center>' . $rList['satuan'] . '</td>';
			$tab .= '<td align=right>' . number_format($totHrg, 2) . '</td>';
			$tab .= '<td>' . $tglPP . '</td>';
			$tab .= '<td>' . $tglBapb . '</td>';
			$tab .= '</tr>';
		}

		$totalAll['totalSemua'] += $grandTot['total'];
		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td colspan=\'9\' align=\'right\'><b>Sub Total </b></td>';
		$tab .= '<td align=right>' . number_format($grandTot['total'], 2) . '</td>';
		$tab .= '<td colspan=\'2\' >&nbsp;</td>';
		$tab .= '</tr>';
	}

	$tab .= '<tr><td colspan=9>Grand Total</td><td>' . number_format($totalAll['totalSemua'],2) . '</td><td colspan=2>&nbsp;</td></tr>';
	$tab .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHms');
	$nop_ = 'Laporan_Pembelian_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                            </script>';
	break;

case 'getTgl':
	if ($periode != '') {
		$tgl = $periode;
		$tanggal = $tgl[0] . '-' . $tgl[1];
	}
	else if ($period != '') {
		$tgl = $period;
		$tanggal = $tgl[0] . '-' . $tgl[1];
	}

	if ($kdUnit == '') {
		$kdUnit = $_SESSION['lang']['lokasitugas'];
	}

	$sTgl = 'select distinct tanggalmulai,tanggalsampai from ' . $dbname . '.sdm_5periodegaji where kodeorg=\'' . substr($kdUnit, 0, 4) . '\' and periode=\'' . $tanggal . '\' ';

	#exit(mysql_error());
	($qTgl = mysql_query($sTgl)) || true;
	$rTgl = mysql_fetch_assoc($qTgl);
	echo tanggalnormal($rTgl['tanggalmulai']) . '###' . tanggalnormal($rTgl['tanggalsampai']);
	break;
}

?>
