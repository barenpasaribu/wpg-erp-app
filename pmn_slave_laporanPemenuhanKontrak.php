<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$hargasatuan = makeOption($dbname, 'pmn_kontrakjual', 'nokontrak,hargasatuan');
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['notrans'] == '' ? $nokontrak = $_GET['notrans'] : $nokontrak = $_POST['notrans'];
$periode = $_POST['periode'];
$kdBrg = $_POST['kdBrg'];
$kdBrg2 = $_POST['kdBrg2'];
$_POST['thn'] == '' ? $thn = $_GET['thn'] : $thn = $_POST['thn'];
$_POST['kdBrg3'] == '' ? $kdBrg3 = $_GET['kdBrg3'] : $kdBrg3 = $_POST['kdBrg3'];
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

switch ($proses) {
case 'preview':
	echo '<table class=sortable cellspacing=1 border=0><thead><tr class=rowheader><td>No</td><td>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['Pembeli'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['estimasiPengiriman'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['pemenuhan'] . '</td>' . "\r\n\t\t\r\n" . ' <td>' . $_SESSION['lang']['sisa'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';

	if ($kdBrg != '') {
		$where = ' and kodebarang=\'' . $kdBrg . '\'';
	}

	$sql = "select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' ".$where." 
		and kodeorg like '".$_SESSION['empl']['kodeorganisasi']."%'";

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$no=1;
	while ($res = mysql_fetch_assoc($query)) {
		$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

		#exit(mysql_error());
		($qBrg = mysql_query($sBrg)) || true;
		$rBrg = mysql_fetch_assoc($qBrg);
		$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $res['koderekanan'] . '\'';

		#exit(mysql_error());
		($qCust = mysql_query($sCust)) || true;
		$rCust = mysql_fetch_assoc($qCust);
		$sTimb = 'select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem   from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $res['nokontrak'] . '\'';

		#exit(mysql_error());
		($qTimb = mysql_query($sTimb)) || true;
		$rTimb = mysql_fetch_assoc($qTimb);
		$arr = 'nokontrak' . '##' . $res['nokontrak'];
		$sisaBarang = $res['kuantitaskontrak'] - $rTimb['jumlahTotal'];
		echo '<tr class=rowcontent onclick="zDetail(event,\'pmn_slave_laporanPemenuhanKontrak.php\',\'' . $arr . '\')" style="cursor:pointer;"><td>'.$no.'</td><td style="cursor:pointer;">' . $res['nokontrak'] . '</td>' . "\r\n" . '                <td style="cursor:pointer;">' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                <td style="cursor:pointer;">' . tanggalnormal($res['tanggalkontrak']) . '</td>' . "\r\n" . '                <td style="cursor:pointer;">' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                <td style="cursor:pointer;">' . tanggalnormal($res['tanggalkirim']) . ' s.d. ' . tanggalnormal($res['sdtanggal']) . '</td>' . "\r\n" . '                <td align=right style="cursor:pointer;">' . number_format($res['kuantitaskontrak']) . '</td>' . "\r\n\t\t\t\t\r\n\t\t\t\t" . ' <td align=right style="cursor:pointer;">' . number_format($res['hargasatuan']) . '</td>' . "\r\n\t\t\t\t" . ' <td align=right style="cursor:pointer;">' . number_format($res['kuantitaskontrak'] * $res['hargasatuan']) . '</td>' . "\r\n\t\t\t\t" . ' ' . "\r\n" . '                <td align=right style="cursor:pointer;">' . number_format($rTimb['jumlahTotal']) . '</td>' . "\r\n" . ' <td align=right style="cursor:pointer;">' . number_format($sisaBarang) . '</td>' . "\r\n" . '                </tr>' . "\r\n" . '                ';
		$totalx += $res['kuantitaskontrak'] * $res['hargasatuan'];
		$total1 += $res['kuantitaskontrak'];
		$total2 += $rTimb['jumlahTotal'];
		$total3 += $rTimb['jumlahKgpem'];
		$total4 += $sisaBarang;
		$no++;
	}

	echo '<thead><tr class=rowheader>' . "\r\n" . '        <td colspan=6>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        <td align=right>' . number_format($total1) . '</td>' . "\r\n\t\t" . '<td align=right></td>' . "\t\r\n\t\t" . '<td align=right>' . number_format($totalx, 2) . '</td>' . "\r\n" . '        <td align=right>' . number_format($total2) . '</td>' . "\r\n" . ' <td align=right>' . number_format($total4) . '</td>' . "\r\n" . '        </tr></thead>';
	echo '</tbody></table>';
	break;

case 'preview2':

	echo '<table class=sortable cellspacing=1 border=0><thead><tr class=rowheader>' . "\r\n" . ' <td>No.</td>       <td>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['Pembeli'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['estimasiPengiriman'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['pemenuhan'] . '</td>' . "\r\n" . ' <td>' . $_SESSION['lang']['sisa'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';


	if ($kdBrg2 != '') {
		$where = ' and kodebarang=\'' . $kdBrg2 . '\'';
	}

	$sql = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ' . $dbname . '.pmn_kontrakjual' . "\r\n" . '              where  tanggalkontrak like \'' . $thn . '%\' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' '.$where.' order by tanggalkontrak asc';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$no=1;
	while ($res = mysql_fetch_assoc($query)) {
		$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

		#exit(mysql_error());
		($qBrg = mysql_query($sBrg)) || true;
		$rBrg = mysql_fetch_assoc($qBrg);
		$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $res['koderekanan'] . '\'';

		#exit(mysql_error());
		($qCust = mysql_query($sCust)) || true;
		$rCust = mysql_fetch_assoc($qCust);
		$sTimb = 'select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem   from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $res['nokontrak'] . '\'';

		#exit(mysql_error());
		($qTimb = mysql_query($sTimb)) || true;
		$rTimb = mysql_fetch_assoc($qTimb);
		$arr = 'nokontrak' . '##' . $res['nokontrak'];
		$sisaBarang = $res['kuantitaskontrak'] - $rTimb['jumlahTotal'];

		if ($rTimb['jumlahTotal'] < $res['kuantitaskontrak']) {
			echo '<tr class=rowcontent ">' . "\r\n" . '  <td>'.$no.'</td>                  <td >' . $res['nokontrak'] . '</td>' . "\r\n" . '                    <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                    <td>' . tanggalnormal($res['tanggalkontrak']) . '</td>' . "\r\n" . '                    <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                    <td>' . tanggalnormal($res['tanggalkirim']) . ' s.d. ' . tanggalnormal($res['sdtanggal']) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($res['kuantitaskontrak'], 2) . '</td>' . "\r\n\t\t\t\t\t\r\n\t\t\t\t\t" . '<td align=right>' . number_format($res['hargasatuan'], 2) . '</td>' . "\r\n\t\t\t\t\t" . '<td align=right>' . number_format($res['kuantitaskontrak'] * $res['hargasatuan'], 2) . '</td>' . "\r\n\t\t\t\t\t\r\n" . '                    <td align=right>' . number_format($rTimb['jumlahTotal'], 2) . '</td>' . "\r\n" . '                    <td align=right>' . number_format($sisaBarang, 2) . '</td>' . "\r\n" . '                    </tr>' . "\r\n" . '                    ';
		$no++;
		}
		
	}

	echo '</tbody></table>';
	break;

case 'excel2':
	$kdBrg2 = $_GET['kdBrg2'];

	if ($kdBrg2 != '') {
		$where = ' and kodebarang=\'' . $kdBrg2 . '\'';
	}

	$stream .= "\r\n" . '                        <table>' . "\r\n" . '                        <tr><td colspan=9 align=center>Unfulfilled sales contract</td></tr>' . "\r\n" . '                        <tr><td colspan=3>' . $_SESSION['lang']['komoditi'] . ' : ' . $optNmBrg[$kdBrg2] . '</td><td></td></tr>' . "\r\n" . '                        <tr><td colspan=3></td><td></td></tr>' . "\r\n" . '                        </table>' . "\r\n" . '                        <table border=1>' . "\r\n" . '                        <tr>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['Pembeli'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['estimasiPengiriman'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . '<td  bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . '<td  bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['pemenuhan'] . '</td>' . "\r\n" . ' <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['sisa'] . '</td>' . "\r\n" . '                        </tr>';
	$strx = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept,hargasatuan from ' . $dbname . '.pmn_kontrakjual' . "\r\n" . ' where tanggalkontrak like \'' . $thn . '%\' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' '.$where.' order by tanggalkontrak asc';

	#exit(mysql_error());
	($resx = mysql_query($strx)) || true;
	$row = mysql_fetch_row($resx);

	if ($row < 1) {
		$stream .= "\t" . '<tr class=rowcontent>' . "\r\n" . '                        <td colspan=8 align=center>Not Found</td></tr>' . "\r\n" . '                        ';
	}
	else {
		$no = 0;
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_assoc($resx)) {
			$no += 1;
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $barx['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$sTimb = 'select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $barx['nokontrak'] . '\'';

			#exit(mysql_error());
			($qTimb = mysql_query($sTimb)) || true;
			$rTimb = mysql_fetch_assoc($qTimb);
			$sisaData = $barx['kuantitaskontrak'] - $rTimb['jumlahTotal'];

			if ($rTimb['jumlahTotal'] <= $barx['kuantitaskontrak']) {
				$stream .= "\t" . '<tr class=rowcontent>' . "\r\n" . '                                        <td>' . $no . '</td>' . "\r\n" . '                                        <td>' . $barx['nokontrak'] . '</td>' . "\r\n" . '                                        <td>' . $optNmBrg[$barx['kodebarang']] . '</td>' . "\r\n" . '                                        <td>' . $barx['tanggalkontrak'] . '</td>' . "\r\n" . '                                        <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                                        <td>' . tanggalnormal($barx['tanggalkirim']) . ' s.d. ' . tanggalnormal($barx['sdtanggal']) . '</td>' . "\r\n" . '                                        <td>' . number_format($barx['kuantitaskontrak'], 0) . '</td>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . '<td>' . number_format($barx['hargasatuan'], 0) . '</td>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . '<td>' . number_format($barx['kuantitaskontrak'] * $barx['hargasatuan'], 0) . '</td>' . "\r\n" . '                                        <td>' . number_format($rTimb['jumlahTotal'], 0) . '</td> <td>' . number_format($sisaData, 0) . '</td>' . "\r\n" . '                                        </tr>';
			}
		}
	}

	$stream .= '</table>';
	$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'KontrakBlmTpenuhi';

	if (0 < strlen($stream)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $stream)) {
			echo '<script language=javascript1.2>' . "\r\n" . '                        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                        </script>';
		}

		closedir($handle);
	}

	break;

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
			global $periode;
			global $kdBrg;

			if ($kdBrg != '') {
				$where = ' and kodebarang=\'' . $kdBrg . '\'';
			}
			$sql = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\' '.$where.'';

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$res = mysql_fetch_assoc($query);
			$tkdOperasi = $res['jlhharitdkoperasi'];
			$jmlhHariOperasi = $res['jlhharioperasi'];
			$meter = $res['merterperhari'];
			$kdOrg = $res['orgdata'];
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $res['kodept'] . '\'');
			$orgData = fetchData($query);
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
			$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['laporanPemenuhanKontrak'], '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 8);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, $periode, '', 0, 'L');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'U', 7);
			$this->Cell($width, $height, $_SESSION['lang']['laporanPemenuhanKontrak'], 0, 1, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'B', 5);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((9 / 100) * $width, $height, $_SESSION['lang']['NoKontrak'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['komoditi'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['tglKontrak'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['Pembeli'], 1, 0, 'C', 1);
			$this->Cell((11 / 100) * $width, $height, $_SESSION['lang']['estimasiPengiriman'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['jmlhBrg'] . '(KG)', 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['pemenuhan'] . '(KG)', 1, 0, 'C', 1);
			$this->Cell((5 / 100) * $width, $height, $_SESSION['lang']['sisa'] . ' (KG)', 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$periode = $_GET['periode'];
	$kdBrg = $_GET['kdBrg'];
	$pdf = new PDF('P', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 11;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 5);

	if ($kdBrg != '') {
		$where = ' and kodebarang=\'' . $kdBrg . '\'';
	}

	$sDet = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,hargasatuan from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' ' . $where . ' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;

	while ($rDet = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rDet['kodebarang'] . '\'';

		#exit(mysql_error());
		($qBrg = mysql_query($sBrg)) || true;
		$rBrg = mysql_fetch_assoc($qBrg);
		$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rDet['koderekanan'] . '\'';

		#exit(mysql_error());
		($qCust = mysql_query($sCust)) || true;
		$rCust = mysql_fetch_assoc($qCust);
		$sTimb = 'select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $rDet['nokontrak'] . '\'';

		#exit(mysql_error());
		($qTimb = mysql_query($sTimb)) || true;
		$rTimb = mysql_fetch_assoc($qTimb);
		$sisaData = $rDet['kuantitaskontrak'] - $rTimb['jumlahTotal'];
		$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((9 / 100) * $width, $height, $rDet['nokontrak'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
		$pdf->Cell((8 / 100) * $width, $height, tanggalnormal($rDet['tanggalkontrak']), 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, substr($rCust['namacustomer'], 0, 50), 1, 0, 'L', 1);
		$pdf->Cell((11 / 100) * $width, $height, tanggalnormal($rDet['tanggalkirim']) . '-' . tanggalnormal($rDet['sdtanggal']), 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, number_format($rDet['kuantitaskontrak']), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, number_format($rDet['hargasatuan']), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, number_format($rDet['hargasatuan'] * $rDet['kuantitaskontrak'], 2), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, number_format($rTimb['jumlahTotal']), 1, 0, 'R', 1);
		$pdf->Cell((5 / 100) * $width, $height, number_format($sisaData), 1, 1, 'R', 1);
		$total1p += $rDet['kuantitaskontrak'];
		$total2p += $rTimb['jumlahTotal'];
		$total3p += $rTimb['jumlahKgpem'];
		$total4p += $sisaData;
		$totaly += $rDet['hargasatuan'] * $rDet['kuantitaskontrak'];
	}

	$pdf->Cell((56 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, number_format($total1p), 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, '', 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, number_format($totaly), 1, 0, 'R', 1);
	$pdf->Cell((8 / 100) * $width, $height, number_format($total2p), 1, 0, 'R', 1);
	$pdf->Cell((5 / 100) * $width, $height, number_format($total4p), 1, 1, 'R', 1);
	$pdf->Output();
	break;

case 'excel':
	$periode = $_GET['periode'];
	$kdBrg = $_GET['kdBrg'];
	$stream .= "\r\n" . '                        <table>' . "\r\n" . '                        <tr><td colspan=9 align=center>' . $_SESSION['lang']['laporanPemenuhanKontrak'] . '</td></tr>' . "\r\n" . '                        <tr><td colspan=3>' . $_SESSION['lang']['periode'] . '</td><td>' . $periode . '</td></tr>' . "\r\n" . '                        <tr><td colspan=3></td><td></td></tr>' . "\r\n" . '                        </table>' . "\r\n" . '                        <table border=1>' . "\r\n" . '                        <tr>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['komoditi'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['Pembeli'] . '</td>' . "\t\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['estimasiPengiriman'] . '</td>' . "\t\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\t\r\n\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t" . ' <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\t\r\n\t\t\t\t\t\t\t\t" . '  <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['total'] . '</td>' . "\t\r\n\t\t\t\t\t\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['pemenuhan'] . '</td><td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['sisa'] . '</td>' . "\r\n" . '                        </tr>';

	if ($kdBrg != '') {
		$where = ' and kodebarang=\'' . $kdBrg . '\'';
	}

	$strx = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept,hargasatuan from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' ' . $where . ' and kodeorg like \''.$_SESSION['empl']['kodeorganisasi'].'%\'';

	#exit(mysql_error());
	($resx = mysql_query($strx)) || true;
	$row = mysql_fetch_row($resx);

	if ($row < 1) {
		$stream .= "\t" . '<tr class=rowcontent>' . "\r\n" . '                        <td colspan=8 align=center>Not Found</td></tr>' . "\r\n" . '                        ';
	}
	else {
		$no = 0;
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_assoc($resx)) {
			$no += 1;
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $barx['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $barx['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$sTimb = 'select sum(beratbersih) as jumlahTotal,sum(kgpembeli) as jumlahKgpem  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $barx['nokontrak'] . '\'';

			#exit(mysql_error());
			($qTimb = mysql_query($sTimb)) || true;
			$rTimb = mysql_fetch_assoc($qTimb);

			$sisaData = $barx['kuantitaskontrak'] - $rTimb['jumlahTotal'];
			$sisaBarang = $barx['kuantitaskontrak'] - $rTimb['jumlahTotal'];
			$stream .= "\t" . '<tr class=rowcontent>' . "\r\n" . '                                <td>' . $no . '</td>' . "\r\n" . '                                <td>' . $barx['nokontrak'] . '</td>' . "\r\n" . '                                <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                                <td>' . $barx['tanggalkontrak'] . '</td>' . "\r\n" . '                                <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                                <td>' . tanggalnormal($barx['tanggalkirim']) . ' s.d. ' . tanggalnormal($barx['sdtanggal']) . '</td>' . "\r\n" . '                                <td>' . number_format($barx['kuantitaskontrak']) . '</td>' . "\r\n\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t" . '<td>' . number_format($barx['hargasatuan'], 2) . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . '<td>' . number_format($barx['kuantitaskontrak'] * $barx['hargasatuan'], 2) . '</td>' . "\r\n\t\t\t\t\t\t\t\t\r\n" . '                                <td>' . number_format($rTimb['jumlahTotal']) . '</td>' . "\t\r\n" . '                                <td>' . number_format($sisaBarang) . '</td>' . "\r\n" . '                                </tr>';
			$total1e += $barx['kuantitaskontrak'];
			$total2e += $rTimb['jumlahTotal'];
			$total3e += $rTimb['jumlahKgpem'];
			$total4e += $sisaBarang;
			$totalx += $barx['kuantitaskontrak'] * $barx['hargasatuan'];
		}
	}

	$stream .= '<tr>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center colspan=6>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . number_format($total1e) . '</td>' . "\t\r\n\t\t\t\t\t\t\t\t" . '<td bgcolor=#DEDEDE align=center></td>' . "\t\r\n\t\t\t\t\t\t\t\t" . '<td bgcolor=#DEDEDE align=center>' . number_format($totalx, 2) . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center>' . number_format($total2e) . '</td>' . "\t\r\n" . '                               <td bgcolor=#DEDEDE align=center>' . number_format($total4e) . '</td>' . "\t\r\n" . '                        </tr>';
	$stream .= '</table>';
	$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'PemenuhanKontrak';

	if (0 < strlen($stream)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $stream)) {
			echo '<script language=javascript1.2>' . "\r\n" . '                        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                        </script>';
		}

		closedir($handle);
	}

	break;

case 'getDetail':
	$drr = '##no_kontrak';
	$arr = 'nokontrak' . '##' . $res['nokontrak'];
	echo '<script language=javascript src=js/generic.js></script><script language=javascript src=js/zTools.js></script>' . "\r\n" . '        <script language=javascript src=js/pmn_laporanPemenuhanKontrak.js></script>';
	echo '<link rel=stylesheet type=text/css href=style/generic.css>';
	$nokontrak = $_GET['nokontrak'];
	$sHed = 'select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ' . $dbname . '.pmn_kontrakjual a where a.nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qHead = mysql_query($sHed)) || true;
	$rHead = mysql_fetch_assoc($qHead);
	$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rHead['kodebarang'] . '\'';

	#exit(mysql_error());
	($qBrg = mysql_query($sBrg)) || true;
	$rBrg = mysql_fetch_assoc($qBrg);
	$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rHead['koderekanan'] . '\'';

	#exit(mysql_error());
	($qCust = mysql_query($sCust)) || true;
	$rCust = mysql_fetch_assoc($qCust);
	echo '<fieldset><legend>' . $_SESSION['lang']['detailPengiriman'] . '</legend>' . "\r\n" . '        <table cellspacing=1 border=0 class=myinputtext>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['NoKontrak'] . '</td><td>:</td><td id=\'no_kontrak\' value=\'' . $nokontrak . '\'>' . $nokontrak . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['tglKontrak'] . '</td><td>:</td><td>' . tanggalnormal($rHead['tanggalkontrak']) . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['komoditi'] . '</td><td>:</td><td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['Pembeli'] . '</td><td>:</td><td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr><td><button onclick="zPdfDetail(\'pmn_slave_laporanPemenuhanKontrak\',\'' . $drr . '\',\'printPdf\')" class="mybutton" name="preview" id="preview">PDF</button>' . "\r\n" . '        <button onclick="zBack()" class="mybutton" name="preview" id="preview">HTML</button>' . "\r\n" . '        <button onclick="detailExcel(\'' . $nokontrak . '\',\'pmn_slave_laporanPemenuhanKontrak.php\',\'printExcel\',\'event\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '        </table><br />';
	echo '<div id=cetakdPdf style="display:none;">' . "\r\n" . '        <fieldset><legend>' . $_SESSION['lang']['print'] . '</legend>' . "\r\n" . '        <div id="printPdf">' . "\r\n" . '        </div>' . "\r\n" . '        </fieldset>' . "\r\n" . '        </div>' . "\r\n" . '        ';
	echo "\r\n" . '        <div id=cetakdHtml >' . "\r\n" . '        <table cellspacing=1 border=0 class=sortable><thead>' . "\r\n" . '        <tr class=data>' . "\r\n" . '        <td>No</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nosipb'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kendaraan'] . '</td>            ' . "\r\n" . '        <td>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['beratnormal'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['beratnormal'] . ' ' . $_SESSION['lang']['Pembeli'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			echo '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $rDet['notransaksi'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $rDet['nodo'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nosipb'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nokendaraan'] . '</td>' . "\r\n" . '                        <td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['beratbersih'], 2) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['kgpembeli'], 2) . '</td>' . "\r\n" . '                        </tr>';
			$subtot += 'total';
			$subtotKga += 'totalKg';
		}

		echo '<tr class=rowcontent><td colspan=\'7\'>Total</td><td align=right>' . number_format($subtot['total'], 2) . '</td><td align=right>' . number_format($subtotKga['totalKg'], 2) . '</td></tr>';
	}
	else {
		echo '<tr><td colspan=7>Not Found</td></tr>';
	}

	echo '</tbody></table></div></fieldset>';
	break;

case 'getExcel':
	$tab .= "\r\n" . '        <table cellspacing=1 border=1 class=sortable><thead>' . "\r\n" . '        <tr class=data>' . "\r\n" . '        <td  bgcolor=#DEDEDE align=center>No</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nosipb'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kendaraan'] . '</td>            ' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['beratnormal'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['beratnormal'] . ' ' . $_SESSION['lang']['Pembeli'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $rDet['notransaksi'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $rDet['nodo'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nosipb'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nokendaraan'] . '</td>' . "\r\n" . '                        <td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['beratbersih'], 2) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['kgpembeli'], 2) . '</td>' . "\r\n" . '                        </tr>';
			$subtot += 'total';
			$subtotKga += 'totalKg';
		}

		$tab .= '<tr class=rowcontent><td colspan=\'7\'>Total</td><td align=right>' . number_format($subtot['total'], 2) . '</td><td align=right>' . number_format($subtotKga['totalKg'], 2) . '</td></tr>';
	}
	else {
		$tab .= '<tr><td colspan=7>Not Found</td></tr>';
	}

	$tab .= '</tbody>';
	$tab .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'ContractFullfillmentDetail';

	if (0 < strlen($tab)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab)) {
			echo '<script language=javascript1.2>' . "\r\n" . '                        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                        </script>';
		}

		closedir($handle);
	}

	break;

case 'preview3':
	echo "\r\n" . '        <div id=cetakdHtml >' . "\r\n" . '        <table cellspacing=1 border=0 class=sortable><thead>' . "\r\n" . '        <tr class=data>' . "\r\n" . '        <td>No</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nosipb'] . '</td>         ' . "\r\n" . '        <td>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kendaraan'] . '</td> <td>' . $_SESSION['lang']['beratnormal'] . '</td> </tr></thead>';
	$tgl1 = explode('-', $_POST['tgl_dr']);
	$tangglAwl = $tgl1[2] . '-' . $tgl1[1] . '-' . $tgl1[0];
	$tgl2 = explode('-', $_POST['tgl_samp']);
	$tangglSmp = $tgl2[2] . '-' . $tgl2[1] . '-' . $tgl2[0];

	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli,kodebarang' . "\r\n" . '              from ' . $dbname . '.pabrik_timbangan where substr(tanggal,1,10) between \'' . $tangglAwl . '\' and \'' . $tangglSmp . '\' and kodebarang=\'' . $kdBrg3 . '\'' . "\r\n" . '                  and nokontrak !=\'\'' . "\r\n" . '     and millcode like \''.$_SESSION['empl']['kodeorganisasi'].'%\' order by tanggal asc';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			echo '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $rDet['notransaksi'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $optNmBrg[$rDet['kodebarang']] . '</td>' . "\r\n" . '                        <td>' . $rDet['nodo'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nosipb'] . '</td>' . "\t\t\t\r\n" . '                        <td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n" . '                        <td>' . $rDet['nokendaraan'] . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['beratbersih'], 2) . '</td></tr>';
			$total += $rDet['beratbersih'];
			
		}

		echo '<tr class=rowcontent><td colspan=\'8\'>Total</td>' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($total, 2) . '</td></tr>';
	}
	else {
		echo '<tr><td colspan=7>Not Found</td></tr>';
	}

	echo '</tbody></table></div></fieldset>';
	break;

case 'excel3':
	$tab .= "\r\n" . '        <table cellspacing=1 border=1 class=sortable><thead>' . "\r\n" . '        <tr class=data>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>No</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nosipb'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kendaraan'] . '</td>            ' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n" . '        <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['beratnormal'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';
	$tgl1 = explode('-', $_GET['tgl_dr']);
	$tangglAwl = $tgl1[2] . '-' . $tgl1[1] . '-' . $tgl1[0];
	$tgl2 = explode('-', $_GET['tgl_samp']);
	$tangglSmp = $tgl2[2] . '-' . $tgl2[1] . '-' . $tgl2[0];
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli,kodebarang' . "\r\n" . '              from ' . $dbname . '.pabrik_timbangan where substr(tanggal,1,10) between \'' . $tangglAwl . '\' and \'' . $tangglSmp . '\' and kodebarang=\'' . $kdBrg3 . '\'' . "\r\n" . '                  and nokontrak !=\'\'' . "\r\n" . '              order by tanggal asc';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			$tab .= '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $rDet['notransaksi'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $optNmBrg[$rDet['kodebarang']] . '</td>' . "\r\n" . '                        <td>' . $rDet['nodo'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nosipb'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nokendaraan'] . '</td>' . "\r\n" . '                        <td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['beratbersih'], 0) . '</td>' . "\r\n" . ' </tr>';
			$subtot += $rDet['beratbersih'];
		}

		$tab .= '<tr class=rowcontent><td colspan=\'8\'>Total</td><td align=right>' . number_format($subtot, 0) . '</td></tr>';
	}
	else {
		$tab .= '<tr><td colspan=7>Not Found</td></tr>';
	}

	$tab .= '</tbody></table>';
	$nop_ = 'rangePengiriman';

	if (0 < strlen($tab)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab)) {
			echo '<script language=javascript1.2>' . "\r\n" . '                        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                        </script>';
		}

		closedir($handle);
	}

	break;

case 'detailpdf':
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
			global $no_kontrak;
			$sql = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $no_kontrak . '\'';

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;
			$res = mysql_fetch_assoc($query);
			$sHed = 'select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ' . $dbname . '.pmn_kontrakjual a where a.nokontrak=\'' . $nokontrak . '\'';

			#exit(mysql_error());
			($qHead = mysql_query($sHed)) || true;
			$rHead = mysql_fetch_assoc($qHead);
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rHead['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rHead['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $res['kodept'] . '\'');
			$orgData = fetchData($query);
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
			$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'U', 9);
			$this->Cell($width, $height, $_SESSION['lang']['detailPengiriman'], 0, 1, 'C');
			$this->Ln();
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['notransaksi'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['nodo'], 1, 0, 'C', 1);
			$this->Cell((16 / 100) * $width, $height, $_SESSION['lang']['nosipb'], 1, 0, 'C', 1);
			$this->Cell((11 / 100) * $width, $height, $_SESSION['lang']['kendaraan'], 1, 0, 'C', 1);
			$this->Cell((11 / 100) * $width, $height, $_SESSION['lang']['sopir'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['beratnormal'], 1, 0, 'C', 1);
			$this->Cell((16 / 100) * $width, $height, $_SESSION['lang']['beratnormal'] . ' ' . $_SESSION['lang']['Pembeli'], 1, 1, 'C', 1);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$no_kontrak = $_GET['no_kontrak'];
	$pdf = new PDF('P', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 11;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir,kgpembeli from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $no_kontrak . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;

	while ($rDet = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((10 / 100) * $width, $height, $rDet['notransaksi'], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, tanggalnormal($rDet['tanggal']), 1, 0, 'L', 1);
		$pdf->Cell((12 / 100) * $width, $height, $rDet['nodo'], 1, 0, 'L', 1);
		$pdf->Cell((16 / 100) * $width, $height, $rDet['nosipb'], 1, 0, 'L', 1);
		$pdf->Cell((11 / 100) * $width, $height, $rDet['nokendaraan'], 1, 0, 'L', 1);
		$pdf->Cell((11 / 100) * $width, $height, ucfirst($rDet['supir']), 1, 0, 'L', 1);
		$pdf->Cell((12 / 100) * $width, $height, number_format($rDet['beratbersih'], 2), 1, 0, 'R', 1);
		$pdf->Cell((16 / 100) * $width, $height, number_format($rDet['kgpembeli'], 2), 1, 1, 'R', 1);
		$subtot += $rDet['beratbersih'];
		$subtot2 += $rDet['kgpembeli'];
	}

	$pdf->Cell((73 / 100) * $width, $height, 'Total', 1, 0, 'R', 1);
	$pdf->Cell((12 / 100) * $width, $height, number_format($subtot, 2), 1, 0, 'R', 1);
	$pdf->Cell((16 / 100) * $width, $height, number_format($subtot2, 2), 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
