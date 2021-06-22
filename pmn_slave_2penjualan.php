<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];
$kdBrg = $_POST['kdBrg'];
$idPabrik = $_POST['idPabrik'];
$idPelanggan = $_POST['idPelanggan'];

switch ($proses) {
case 'preview':
	echo ' <table class=sortable cellspacing=1 border=0 style=\'width:1200px;\'>' . "\r\n" . '      <thead>' . "\r\n" . '          <tr class=rowheader>' . "\r\n" . '          <td rowspan=2>No.</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['nm_perusahaan'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['nmcust'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '          <td colspan=3>' . $_SESSION['lang']['pemenuhan'] . '</td>' . "\r\n" . '          <td rowspan=2>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n" . '          </tr>' . "\r\n" . '          <tr><td>Loco</td><td>Franco</td><td>Total</td></tr>' . "\r\n" . '          </thead><tbody>' . "\r\n" . '        ';
	$arrKurs = array('IDR', 'USD');
	$sql = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,kuantitaskontrak,kodept,hargasatuan,matauang  from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' and kodebarang=\'' . $kdBrg . '\' and kodept=\'' . $idPabrik . '\' ';
	if(trim($idPelanggan) != ""){
		$sql .= " and koderekanan='".$idPelanggan."' ";
	}
	$sql .= 'order by tanggalkontrak asc';
	//echo $sql;
	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$row = mysql_num_rows($query);

	if (0 < $row) {
		while ($res = mysql_fetch_assoc($query)) {
			$no += 1;
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $res['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$sTimb = 'select sum(beratbersih) as beratbersih,sum(kgpembeli) as kgpembeli  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $res['nokontrak'] . '\'';

			#exit(mysql_error());
			($qTimb = mysql_query($sTimb)) || true;
			$rTimb = mysql_fetch_assoc($qTimb);
			$sOrg = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $res['kodept'] . '\'';

			#exit(mysql_error());
			($qOrg = mysql_query($sOrg)) || true;
			$rOrg = mysql_fetch_assoc($qOrg);
			$arr = 'nokontrak' . '##' . $res['nokontrak'];
			echo '<tr class=rowcontent onclick="zDetail(event,\'pmn_slave_laporanPemenuhanKontrak.php\',\'' . $arr . '\')">' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $res['nokontrak'] . '</td>' . "\r\n" . '                        <td>' . $rOrg['namaorganisasi'] . '</td>' . "\r\n" . '                        <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($res['tanggalkontrak']) . '</td>' . "\r\n" . '                        <td>' . $res['kodebarang'] . '</td>' . "\r\n" . '                        <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($res['tanggalkirim']) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($res['kuantitaskontrak'], 2) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($res['hargasatuan'], 2) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rTimb['beratbersih'], 2) . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rTimb['kgpembeli'], 2) . '</td>' . "\r\n\t\t\t\t\t\t" . '<td align=right>' . number_format(($rTimb['kgpembeli'] + $rTimb['beratbersih']) * $res['hargasatuan'], 2) . '</td>' . "\r\n" . '                        <td>' . $arrKurs[$res['matauang']] . '</td>' . "\r\n" . '                        </tr>' . "\r\n" . '                        ';
		}
	}
	else {
		echo '<tr class=rowcontent align=center><td colspan=13>Not Found</td></tr>';
	}

	echo '</tbody></table>';
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
			global $idPabrik;
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $kdBrg . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$sOrg = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $idPabrik . '\'';

			#exit(mysql_error());
			($qOrg = mysql_query($sOrg)) || true;
			$rOrg = mysql_fetch_assoc($qOrg);
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
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(((20 / 100) * $width) - 5, $height, strtoupper($_SESSION['lang']['laporanPenjualan']), '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 8);
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, $periode, '', 0, 'L');
			$this->Ln();
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['nm_perusahaan'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, $rOrg['namaorganisasi'], '', 0, 'L');
			$this->Ln();
			$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['komoditi'], '', 0, 'L');
			$this->Cell(5, $height, ':', '', 0, 'L');
			$this->Cell((45 / 100) * $width, $height, $rBrg['namabarang'], '', 0, 'L');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'U', 12);
			$this->SetFont('Arial', 'B', 7);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['NoKontrak'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['nmcust'], 1, 0, 'C', 1);
			$this->Cell((13 / 100) * $width, $height, $_SESSION['lang']['tglKontrak'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['tgl_kirim'], 1, 0, 'C', 1);
			$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['jmlhBrg'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
			$this->Cell((6 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((7 / 100) * $width, $height, $_SESSION['lang']['kurs'], 1, 1, 'C', 1);
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
	$idPabrik = $_GET['idPabrik'];
	$pdf = new PDF('P', 'pt', 'A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$sDet = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,kuantitaskontrak,kodept,hargasatuan,matauang,satuan  from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' and kodebarang=\'' . $kdBrg . '\' and kodept=\'' . $idPabrik . '\' order by tanggalkontrak asc ';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$row = mysql_num_rows($qDet);

	if (0 < $row) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$no += 1;
			$arrKurs = array('IDR', 'USD');
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rDet['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$sTimb = 'select sum(beratbersih) as beratbersih,sum(kgpembeli) as kgpembeli  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $res['nokontrak'] . '\'';

			#exit(mysql_error());
			($qTimb = mysql_query($sTimb)) || true;
			$rTimb = mysql_fetch_assoc($qTimb);
			$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
			$pdf->Cell((15 / 100) * $width, $height, $rDet['nokontrak'], 1, 0, 'L', 1);
			$pdf->Cell((15 / 100) * $width, $height, $rCust['namacustomer'], 1, 0, 'L', 1);
			$pdf->Cell((13 / 100) * $width, $height, tanggalnormal($rDet['tanggalkontrak']), 1, 0, 'C', 1);
			$pdf->Cell((12 / 100) * $width, $height, tanggalnormal($rDet['tanggalkirim']), 1, 0, 'C', 1);
			$pdf->Cell((12 / 100) * $width, $height, number_format($rDet['kuantitaskontrak'], 2), 1, 0, 'R', 1);
			$pdf->Cell((10 / 100) * $width, $height, number_format($rDet['hargasatuan'], 2), 1, 0, 'R', 1);
			$pdf->Cell((10 / 100) * $width, $height, number_format(($rTimb['kgpembeli'] + $rTimb['beratbersih']) * $rDet['hargasatuan'], 2), 1, 0, 'R', 1);
			$pdf->Cell((6 / 100) * $width, $height, $rDet['satuan'], 1, 0, 'C', 1);
			$pdf->Cell((7 / 100) * $width, $height, $arrKurs[$rDet['matauang']], 1, 1, 'C', 1);
		}
	}
	else {
		$pdf->Cell($width, $height, 'Not Found', 1, 1, 'C', 1);
	}

	$pdf->Output();
	break;

case 'excel':
	$periode = $_GET['periode'];
	$kdBrg = $_GET['kdBrg'];
	$idPabrik = $_GET['idPabrik'];
	$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $kdBrg . '\'';

	#exit(mysql_error());
	($qBrg = mysql_query($sBrg)) || true;
	$rBrg = mysql_fetch_assoc($qBrg);
	$sOrg = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $idPabrik . '\'';

	#exit(mysql_error());
	($qOrg = mysql_query($sOrg)) || true;
	$rOrg = mysql_fetch_assoc($qOrg);
	$stream .= "\r\n" . '                        <table>' . "\r\n" . '                        <tr><td colspan=9 align=center><b>' . strtoupper($_SESSION['lang']['laporanPenjualan']) . '</b></td></tr>' . "\r\n" . '                        <tr><td colspan=3>' . $_SESSION['lang']['periode'] . '</td><td>' . $periode . '</td></tr>' . "\r\n" . '                        <tr><td colspan=3>' . $_SESSION['lang']['komoditi'] . '</td><td>' . $rBrg['namabarang'] . '</td></tr>' . "\r\n" . '                        <tr><td colspan=3>' . $_SESSION['lang']['nm_perusahaan'] . '</td><td>' . $rOrg['namaorganisasi'] . '</td></tr>' . "\r\n" . '                        <tr><td colspan=3></td><td></td></tr>' . "\r\n" . '                        </table>' . "\r\n" . '                        <table border=1>' . "\r\n" . '                        <tr>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['NoKontrak'] . '</td>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['nmcust'] . '</td>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tglKontrak'] . '</td>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\t\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['jmlhBrg'] . '</td>' . "\t\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\t\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                                <td bgcolor=#DEDEDE align=center colspan=3>' . $_SESSION['lang']['pemenuhan'] . '</td>' . "\r\n" . '                                <td rowspan=2 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kurs'] . '</td>' . "\t\t\t\t\r\n" . '                        </tr>' . "\r\n" . '                        <tr><td bgcolor=#DEDEDE align=center>Loco</td><td bgcolor=#DEDEDE align=center>Franco</td><td bgcolor=#DEDEDE align=center>Total</td></tr>';
	$strx = 'select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,kuantitaskontrak,kodept,hargasatuan,matauang,satuan  ' . "\r\n" . '                               from ' . $dbname . '.pmn_kontrakjual where tanggalkontrak like \'%' . $periode . '%\' and kodebarang=\'' . $kdBrg . '\' and kodept=\'' . $idPabrik . '\' ' . "\r\n" . '                               order by tanggalkontrak asc';

	#exit(mysql_error());
	($resx = mysql_query($strx)) || true;
	$row = mysql_fetch_row($resx);

	if ($row < 1) {
		$stream .= "\t" . '<tr class=rowcontent>' . "\r\n" . '                        <td colspan=9 align=center>Not Found</td></tr>' . "\r\n" . '                        ';
	}
	else {
		$no = 0;
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_assoc($resx)) {
			$no += 1;
			$arrKurs = array('IDR', 'USD');
			$sCust = 'select namacustomer  from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $barx['koderekanan'] . '\'';

			#exit(mysql_error());
			($qCust = mysql_query($sCust)) || true;
			$rCust = mysql_fetch_assoc($qCust);
			$sTimb = 'select sum(beratbersih) as beratbersih,sum(kgpembeli) as kgpembeli  from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $barx['nokontrak'] . '\'';

			#exit(mysql_error($conn));
			($qTimb = mysql_query($sTimb)) || true;
			$rTimb = mysql_fetch_assoc($qTimb);
			$stream .= '<tr class=rowcontent>' . "\r\n" . '                                <td>' . $no . '</td>' . "\r\n" . '                                <td>' . $barx['nokontrak'] . '</td>' . "\r\n" . '                                <td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '                                <td>' . tanggalnormal($barx['tanggalkontrak']) . '</td>' . "\r\n" . '                                <td>' . tanggalnormal($barx['tanggalkirim']) . '</td>' . "\r\n" . '                                <td align=right>' . number_format($barx['kuantitaskontrak'], 2) . '</td>' . "\r\n" . '                                <td align=right>' . number_format($barx['hargasatuan'], 2) . '</td>' . "\r\n" . '                                <td>' . $barx['satuan'] . '</td>' . "\r\n" . '                                <td align=right>' . number_format($rTimb['beratbersih'], 2) . '</td>' . "\r\n" . '                                <td align=right>' . number_format($rTimb['kgpembeli'], 2) . '</td>' . "\r\n\t\t\t\t\t\t\t\t" . ' <td align=right>' . number_format(($rTimb['kgpembeli'] + $rTimb['beratbersih']) * $barx['hargasatuan'], 2) . '</td>' . "\r\n" . '                                <td>' . $arrKurs[$barx['matauang']] . '</td>' . "\r\n" . '                                </tr>';
		}
	}

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
	echo '<fieldset><legend>' . $_SESSION['lang']['detailPengiriman'] . '</legend>' . "\r\n" . '        <table cellspacing=1 border=0 class=myinputtext>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['NoKontrak'] . '</td><td>:</td><td>' . $nokontrak . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['tglKontrak'] . '</td><td>:</td><td>' . tanggalnormal($rHead['tanggalkontrak']) . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['komoditi'] . '</td><td>:</td><td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '                <td>' . $_SESSION['lang']['Pembeli'] . '</td><td>:</td><td>' . $rCust['namacustomer'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table><br />' . "\r\n" . '        <table cellspacing=1 border=0 class=sortable><thead>' . "\r\n" . '        <tr class=data>' . "\r\n" . '        <td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nodo'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['nosipb'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['beratnormal'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodenopol'] . '</td>' . "\r\n" . '        <td>' . $_SESSION['lang']['sopir'] . '</td>' . "\r\n" . '        </tr></thead><tbody>' . "\r\n" . '        ';
	$sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ' . $dbname . '.pabrik_timbangan where nokontrak=\'' . $nokontrak . '\'';

	#exit(mysql_error());
	($qDet = mysql_query($sDet)) || true;
	$rCek = mysql_num_rows($qDet);

	if (0 < $rCek) {
		while ($rDet = mysql_fetch_assoc($qDet)) {
			echo '<tr class=rowcontent>' . "\r\n" . '                        <td>' . $rDet['notransaksi'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($rDet['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $rDet['nodo'] . '</td>' . "\r\n" . '                        <td>' . $rDet['nosipb'] . '</td>' . "\r\n" . '                        <td align=right>' . number_format($rDet['beratbersih'], 2) . '</td>' . "\r\n" . '                        <td>' . $rDet['nokendaraan'] . '</td>' . "\r\n" . '                        <td>' . ucfirst($rDet['supir']) . '</td>' . "\r\n" . '                        </tr>';
		}
	}
	else {
		echo '<tr><td colspan=7>Not Found</td></tr>';
	}

	echo '</tbody></table></fieldset>';
	break;
}

?>
