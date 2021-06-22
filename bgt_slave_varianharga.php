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

$sKlmpk = 'select kode,kelompok from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optKlmpkBarang = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$_POST['regional'] == '' ? $regional = $_GET['regional'] : $regional = $_POST['regional'];
$_POST['thnBudget'] == '' ? $thnBudget = $_GET['thnBudget'] : $thnBudget = $_POST['thnBudget'];
$_POST['kdBudget'] == '' ? $kdBudget = $_GET['kdBudget'] : $kdBudget = $_POST['kdBudget'];
$data = array();
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$sData = 'select distinct substr(kodebudget,3,3) as kelompokbarang ' . "\r\n" . '        from ' . $dbname . '.bgt_budget_detail ' . "\r\n" . '        where kodebudget like \'M%\' order by substr(kodebudget,3,3) asc';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$data[] = $rData;
	$dr += 1;

	if ($dr == 1) {
		$klmp = $rData['kelompokbarang'];
	}
	else {
		$klmp .= ',' . $rData['kelompokbarang'];
	}
}

$sBudget = 'select distinct avg(hargasatuan) as hargarata,kodebarang from ' . $dbname . '.bgt_masterbarang' . "\r\n" . '          where tahunbudget=\'' . $thnBudget . '\' and substr(kodebarang,1,3) in (' . $klmp . ') and hargasatuan!=0 ' . "\r\n" . '          and regional=\'' . $regional . '\'' . "\r\n" . '          group by kodebarang';

#exit(mysql_error($conn));
($qBudget = mysql_query($sBudget)) || true;

while ($rBudget = mysql_fetch_assoc($qBudget)) {
	$dtBarang[$rBudget['kodebarang']] = $rBudget['kodebarang'];
	$hrgRealBgt[$rBudget['kodebarang']] = $rBudget['hargarata'];
}

$sDt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in ' . "\r\n" . '      (select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $regional . '\')';

#exit(mysql_error($conn));
($qDt = mysql_query($sDt)) || true;

while ($rDt = mysql_fetch_assoc($qDt)) {
	$ert += 1;

	if ($ert == 1) {
		$kdPata = '\'' . $rDt['induk'] . '\'';
	}
	else {
		$kdPata .= ',' . '\'' . $rDt['induk'] . '\'';
	}
}

$sData = 'select distinct avg(hargasatuan) as hargaRata,kodebarang' . "\r\n" . '        from ' . $dbname . '.log_po_vw where substr(tanggal,1,4)=\'' . $thnBudget . '\' ' . "\r\n" . '        and substr(kodebarang,1,3) in (' . $klmp . ') and kodeorg in (' . $kdPata . ')' . "\r\n" . '        group by kodebarang';

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$hrgReal[$rData['kodebarang']] = $rData['hargaRata'];
	$dtBarang[$rData['kodebarang']] = $rData['kodebarang'];
}

$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=7 align=left><b>' . $_SESSION['lang']['bgtVarian'] . '</b></td></tr>    ' . "\r\n" . '    <tr><td colspan=7 align=left>' . $_SESSION['lang']['budgetyear'] . ' : ' . $thnBudget . '</td></tr>' . "\r\n" . '    <tr><td colspan=7 align=left>' . $_SESSION['lang']['regional'] . ' : ' . $regional . '</td></tr>' . "\r\n" . '    </table>';
}

$cekData = count($dtBarang);

if ($cekData == 0) {
	exit('Error:data kosong');
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td ' . $bgcoloraja . '>No.</td>' . "\r\n\t\t" . '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t" . '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t" . '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['hargabudget'] . '</td>' . "\r\n" . '                <td ' . $bgcoloraja . '>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n" . '                <td ' . $bgcoloraja . '>' . $_SESSION['lang']['varian'] . '</td></tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';

foreach ($dtBarang as $lsBrg) {
	if (substr($lsBrg, 0, 3) != $klpmkBrg) {
		$klpmkBrg = substr($lsBrg, 0, 3);
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td colspan=2>' . $klpmkBrg . '</td>';
		$tab .= '<td colspan=5>' . $optKlmpkBarang[$klpmkBrg] . '</td>';
		$tab .= '</tr>';
		$no = 0;
	}

	$no += 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $no . '</td>';
	$tab .= '<td>' . $lsBrg . '</td>';
	$tab .= '<td>' . $optNmBarang[$lsBrg] . '</td>';
	$tab .= '<td>' . $optSatuan[$lsBrg] . '</td>';
	$tab .= '<td align=right>' . number_format($hrgRealBgt[$lsBrg], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($hrgReal[$lsBrg], 0) . '</td>';
	@$var[$lsBrg] = (($hrgRealBgt[$lsBrg] - $hrgReal[$lsBrg]) / $hrgRealBgt[$lsBrg]) * 100;
	$tab .= '<td align=right>' . number_format($var[$lsBrg], 0) . '</td>';
	$tab .= '</tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'getKdorg':
	$optorg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sOrg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\'';

	#exit(mysql_error());
	($qOrg = mysql_query($sOrg)) || true;

	while ($rOrg = mysql_fetch_assoc($qOrg)) {
		$optorg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
	}

	echo $optorg;
	break;

case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'varianHarga_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
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
		}

		if ($kdUnit != '') {
			$where .= ' and substring(b.nopp,16,4)=\'' . $kdUnit . '\'';
		}

		if ($kdSup != '') {
			$where .= ' and a.kodesupplier=\'' . $kdSup . '\'';
		}

		if (($tglDr != '') || ($tanggalSampai != '')) {
			$where .= ' and (a.tanggal between \'' . $tglDari . '\' and \'' . tanggalsystem($_GET['tanggalSampai']) . '\')';
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

		$sList = 'select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.kodesupplier=\'' . $dt['kodesupplier'] . '\' and b.nopo!=\'NULL\' and a.tanggal between \'' . $tglDari . '\' and \'' . $tanggalSampai . '\'';

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

			$grandTot += 'total';

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

		$totalAll += 'totalSemua';
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
}

?>
