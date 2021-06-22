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

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];

if ($periode == '') {
	exit('Error: ' . $_SESSION['lang']['periode'] . ' required');
}

if ($kdUnit != '') {
	$unitId = $optNmOrg[$kdUnit];
}
else {
	exit('Error:' . $_SESSION['lang']['unit'] . ' required');
}

$thn = explode('-', $periode);
$bln = intval($thn[1]);
$thnLalu = $thn[0];

if (strlen($bln) < 2) {
	if ($thn[1] == '1') {
		$blnLalu = 12;
		$thnLalu = $thn[0] - 1;
	}
	else {
		$blnLalu = '0' . $bln;
	}
}
else {
	$blnLalu = $bln - 1;
}

$sBibit = 'select distinct jenisbibit from ' . $dbname . '.bibitan_pengiriman_vw where tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' order by jenisbibit asc';

#exit(mysql_error());
($qBibit = mysql_query($sBibit)) || true;

while ($rBibit = mysql_fetch_assoc($qBibit)) {
	$lstBibit[] = $rBibit['jenisbibit'];
}

$sData = 'select distinct afdeling,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '       where tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' and intex=1 and jenistanam=\'TB\' group by afdeling,jenisbibit';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$kbnSndri[] = $rData['afdeling'];
	$jmlhKbn[$rData['jenistanam']][$rData['afdeling']][$rData['jenisbibit']] = $rData['jumlah'];
}

$sData = 'select distinct afdeling from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '       where substr(tanggal,1,7)=\'' . $periode . '\' and kodeorg like \'' . $kdUnit . '%\' and intex=1';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$kbnSndri[] = $rData['afdeling'];
}

$sData = 'select distinct afdeling,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '       where (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '%\') and kodeorg like \'' . $kdUnit . '%\' and intex=1 and jenistanam=\'TB\' group by afdeling,jenisbibit';

#exit(mysql_error());
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$jmlhKbnSbi[$rData['jenistanam']][$rData['afdeling']][$rData['jenisbibit']] = $rData['jumlah'];
}

$sKbnLr = 'select distinct pelanggan,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '         where tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'TB\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qKbnLr = mysql_query($sKbnLr)) || true;

while ($rKbnLr = mysql_fetch_assoc($qKbnLr)) {
	$jmlhPlgn[$rKbnLr['jenistanam']][$rKbnLr['pelanggan']] += $rKbnLr['jenisbibit'];
	$lstPlnggan[] = $rKbnLr['pelanggan'];
}

$sKbnLr = 'select distinct pelanggan,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '         where (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '%\')  and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'TB\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qKbnLr = mysql_query($sKbnLr)) || true;

while ($rKbnLr = mysql_fetch_assoc($qKbnLr)) {
	$jmlhPlgnSbi[$rKbnLr['jenistanam']][$rKbnLr['pelanggan']] += $rKbnLr['jenisbibit'];
}

$sKbnLr2 = 'select distinct afdeling,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '          where tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'SISIP\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qKbnLr2 = mysql_query($sKbnLr2)) || true;

while ($rKbnLr2 = mysql_fetch_assoc($qKbnLr2)) {
	$jmlhKbn[$rKbnLr2['jenistanam']][$rKbnLr2['afdeling']][$rKbnLr2['jenisbibit']] = $rKbnLr2['jumlah'];
}

$sKbnLr2 = 'select distinct afdeling,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '          where (substr(tanggal,1,7) between \'' . $thn[0] . '-01\' and \'' . $periode . '%\')  and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'SISIP\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qKbnLr2 = mysql_query($sKbnLr2)) || true;

while ($rKbnLr2 = mysql_fetch_assoc($qKbnLr2)) {
	$jmlhKbnSbi[$rKbnLr2['jenistanam']][$rKbnLr2['afdeling']][$rKbnLr2['jenisbibit']] = $rKbnLr2['jumlah'];
}

$sSisip = 'select distinct pelanggan,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '         where  tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'SISIP\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qSisip = mysql_query($sSisip)) || true;

while ($rSisip = mysql_fetch_assoc($qSisip)) {
	$lstPlnggan[] = $rSisip['pelanggan'];
	$jmlhPlgn[$rSisip['jenistanam']][$rSisip['pelanggan']] += $rSisip['jenisbibit'];
}

$sSisip = 'select distinct pelanggan,jenisbibit, sum(jumlah)*-1 as jumlah,jenistanam from ' . $dbname . '.bibitan_pengiriman_vw ' . "\r\n" . '         where  tanggal like \'' . $periode . '%\' and kodeorg like \'' . $kdUnit . '%\' and intex!=1 and jenistanam=\'SISIP\' group by pelanggan,jenisbibit';

#exit(mysql_error());
($qSisip = mysql_query($sSisip)) || true;

while ($rSisip = mysql_fetch_assoc($qSisip)) {
	$jmlhPlgnSbi[$rSisip['jenistanam']][$rSisip['pelanggan']] += $rSisip['jenisbibit'];
}

$colsPertama = count($kbnSndri);
$colsKedua = count($lstPlnggan);
$rowData = count($lstBibit);
$rwData = ($rowData * 2) - 2;
$brdr = 0;
$bgcoloraja = '';
$cols = count($dataAfd) * 3;

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=5 align=left><b>17.2  ' . strtoupper($_SESSION['lang']['pengiriman']) . ' (NURSERY) BIBIT</b></td><td colspan=7 align=right><b>' . $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7) . '</b></td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>' . $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit] . ' </td></tr>' . "\r\n" . '    <tr><td colspan=5 align=left>&nbsp;</td></tr>' . "\r\n" . '    </table>';
}

$arrData = array('TB' => strtoupper($_SESSION['lang']['tb']) . ' (TB)', 'SISIP' => strtoupper($_SESSION['lang']['sisip']));
$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>';
$tab .= '<tr><td rowspan=2>No.</td><td rowspan=2>' . strtoupper($_SESSION['lang']['tujuan']) . '</td><td rowspan=2 colspan=2>BIBIT</td>';
$tab .= '<td colspan=\'' . $colsPertama . '\'>' . strtoupper($_SESSION['lang']['pengiriman']) . ' INTERN (KEBUN)</td><td colspan=\'' . $colsKedua . '\'>' . strtoupper($_SESSION['lang']['pengiriman']) . ' EXTERNAL</td></tr>';
$tab .= '<tr>';

if ($colsPertama != 0) {
	if (!empty($kbnSndri)) {
		foreach ($kbnSndri as $lstKbn) {
			$tab .= '<td>' . $lstKbn . '</td>';
		}
	}
}
else {
	$tab .= '<td colspan=\'' . $colsPertama . '\'>' . $_SESSION['lang']['dataempty'] . '</td>';
}

$tab .= '';

if ($colsKedua != 0) {
	if (!empty($lstPlnggan)) {
		foreach ($lstPlnggan as $lstDt) {
			$tab .= '<td>' . $lstDt . '</td>';
		}
	}
}
else {
	$tab .= '<td colspan=\'' . $colsKedua . '\'>' . $_SESSION['lang']['dataempty'] . '</td>';
}

$tab .= '</tr></thead><tbody>';
$i = 0;

if (!empty($arrData)) {
	foreach ($arrData as $dtini => $isData) {
		$afdC = false;
		$blankC = false;
		$i += 1;

		if (!empty($lstBibit)) {
			foreach ($lstBibit as $dtBibit) {
				$tab .= '<tr class=rowcontent>';

				if ($afdC == false) {
					$tab .= '<td>' . $i . '</td><td>' . $isData . '</td>';
					$afdC = true;
				}
				else if ($blankC == false) {
					$tab .= '<td colspan=\'' . (4 - 2) . '\'></td>';
					$blankC = true;
				}

				$tab .= '<td>' . $dtBibit . '</td>';
				$tab .= '<td>Bi</td>';

				if ($colsPertama != 0) {
					if (!empty($kbnSndri)) {
						foreach ($kbnSndri as $lstKbn) {
							$jmlhKbn[$dtini][$lstKbn][$dtBibit] == '' ? $jmlhKbn[$dtini][$lstKbn][$dtBibit] = 0 : $jmlhKbn[$dtini][$lstKbn][$dtBibit] = $jmlhKbn[$dtini][$lstKbn][$dtBibit];
							$tab .= '<td align=right>' . $jmlhKbn[$dtini][$lstKbn][$dtBibit] . '</td>';
							$sbTotKbn[$dtini] += $lstKbn;
						}
					}
				}
				else {
					$tab .= '<td align=right colspan=\'' . $colsKedua . '\' >' . $_SESSION['lang']['dataempty'] . '</td>';
				}

				if ($colsKedua != 0) {
					if (!empty($lstPlnggan)) {
						foreach ($lstPlnggan as $lstDt) {
							$jmlhPlgn[$dtini][$lstDt][$dtBibit] == '' ? $jmlhPlgn[$dtini][$lstDt][$dtBibit] = 0 : $jmlhPlgn[$dtini][$lstDt][$dtBibit] = $jmlhPlgn[$dtini][$lstDt][$dtBibit];
							$tab .= '<td align=right>' . $jmlhPlgn[$dtini][$lstDt][$dtBibit] . '</td>';
							$sbTotPlgn[$dtini] += $lstDt;
						}
					}
				}
				else {
					$tab .= '<td align=right colspan=\'' . $colsPertama . '\'>' . $_SESSION['lang']['dataempty'] . '</td>';
				}

				$tab .= '</tr>';
				$tab .= '<tr class=rowcontent>';
				$tab .= '<td colspan=\'' . (4 - 1) . '\'></td>';
				$tab .= '<td>' . $_SESSION['lang']['bi'] . '</td>';

				if ($colsPertama != 0) {
					if (!empty($kbnSndri)) {
						foreach ($kbnSndri as $lstKbn) {
							$jmlhKbnSbi[$dtini][$lstKbn][$dtBibit] == '' ? $jmlhKbnSbi[$dtini][$lstKbn][$dtBibit] = 0 : $jmlhKbnSbi[$dtini][$lstKbn][$dtBibit] = $jmlhKbnSbi[$dtini][$lstKbn][$dtBibit];
							$tab .= '<td align=right>' . $jmlhKbnSbi[$dtini][$lstKbn][$dtBibit] . '</td>';
							$sbTotKbnSbi[$dtini] += $lstKbn;
						}
					}
				}
				else {
					$tab .= '<td align=right>' . $_SESSION['lang']['dataempty'] . '</td>';
				}

				if ($colsKedua != 0) {
					if (!empty($lstPlnggan)) {
						foreach ($lstPlnggan as $lstDt) {
							$jmlhPlgnSbi[$dtini][$lstDt][$dtBibit] == '' ? $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit] = 0 : $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit] = $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit];
							$tab .= '<td align=right>' . $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit] . '</td>';
							$sbTotPlgnSbi[$dtini] += $lstDt;
						}
					}
				}
				else {
					$tab .= '<td align=right >' . $_SESSION['lang']['dataempty'] . '</td>';
				}

				$tab .= '</tr>';
			}
		}

		$tab .= '<tr class=rowcontent><td colspan=\'' . (4 - 1) . '\'>Sub Total</td><td>Bi</td>';

		if ($colsPertama != 0) {
			if (!empty($kbnSndri)) {
				foreach ($kbnSndri as $lstKbn) {
					$tab .= '<td align=right>' . $sbTotKbn[$dtini][$lstKbn] . '</td>';
					$grndTotKbn += $lstKbn;
				}
			}
		}
		else {
			$tab .= '<td align=right>' . $_SESSION['lang']['dataempty'] . '</td>';
		}

		if ($colsKedua != 0) {
			if (!empty($lstPlnggan)) {
				foreach ($lstPlnggan as $lstDt) {
					$tab .= '<td align=right>' . $sbTotPlgn[$dtini][$lstDt] . '</td>';
					$grndPlgn += $lstDt;
				}
			}
		}
		else {
			$tab .= '<td align=right>' . $_SESSION['lang']['dataempty'] . '</td>';
		}

		$tab .= '</tr>';
		$tab .= '<tr class=rowcontent><td  colspan=\'' . (4 - 1) . '\'>&nbsp;</td><td>' . $_SESSION['lang']['sbi'] . '</td>';

		if ($colsPertama != 0) {
			if (!empty($kbnSndri)) {
				foreach ($kbnSndri as $lstKbn) {
					$tab .= '<td align=right>' . $sbTotKbnSbi[$dtini][$lstKbn] . '</td>';
					$grndTotKbnSbi += $lstKbn;
				}
			}
		}
		else {
			$tab .= '<td align=right >' . $_SESSION['lang']['dataempty'] . '</td>';
		}

		if ($colsKedua != 0) {
			if (!empty($lstPlnggan)) {
				foreach ($lstPlnggan as $lstDt) {
					$tab .= '<td align=right>' . $sbTotPlgnSbi[$dtini][$lstDt] . '</td>';
					$grndPlgnSi += $lstDt;
				}
			}
		}
		else {
			$tab .= '<td align=right >' . $_SESSION['lang']['dataempty'] . '</td>';
		}

		$tab .= '</tr>';
	}
}

$tab .= '<tr><td>&nbsp;</td><td colspan=2>Grand Total</td><td>' . $_SESSION['lang']['bi'] . '</td>';

if ($colsPertama != 0) {
	if (!empty($kbnSndri)) {
		foreach ($kbnSndri as $lstKbn) {
			$tab .= '<td align=right>' . $grndTotKbn[$lstKbn] . '</td>';
		}
	}
}
else {
	$tab .= '<td align=right >' . $_SESSION['lang']['dataempty'] . '</td>';
}

if ($colsKedua != 0) {
	if (!empty($lstPlnggan)) {
		foreach ($lstPlnggan as $lstDt) {
			$tab .= '<td align=right>' . $grndPlgn[$lstDt] . '</td>';
		}
	}
}
else {
	$tab .= '<td align=right>' . $_SESSION['lang']['dataempty'] . '</td>';
}

$tab .= '</tr>';
$tab .= '<tr><td>&nbsp;</td><td colspan=2>&nbsp;</td><td>' . $_SESSION['lang']['sbi'] . '</td>';

if ($colsPertama != 0) {
	if (!empty($kbnSndri)) {
		foreach ($kbnSndri as $lstKbn) {
			$tab .= '<td align=right>' . $grndTotKbnSbi[$lstKbn] . '</td>';
		}
	}
}
else {
	$tab .= '<td align=right>' . $_SESSION['lang']['dataempty'] . '</td>';
}

if ($colsKedua != 0) {
	if (!empty($lstPlnggan)) {
		foreach ($lstPlnggan as $lstDt) {
			$tab .= '<td align=right>' . $grndPlgnSi[$lstDt] . '</td>';
		}
	}
}
else {
	$tab .= '<td align=right >' . $_SESSION['lang']['dataempty'] . '</td>';
}

$tab .= '</tr>';
$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'kualitas_potong_buah' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;

case 'pdf':
	class PDF extends FPDF
	{
		public function Header()
		{
			global $periode;
			global $dataAfd;
			global $kdUnit;
			global $optNmOrg;
			global $dbname;
			global $thn;
			global $kbnSndri;
			global $lstPlnggan;
			global $tot;
			global $colsPertama;
			global $colsKedua;
			$cold = count($kbnSndri) * 55;
			$tot = count($lstPlnggan);
			$cold2 = count($lstPlnggan) * 55;
			$this->SetFont('Arial', 'B', 8);
			$this->Cell($width, $height, strtoupper('17.2  ' . strtoupper($_SESSION['lang']['pengiriman']) . ' (NURSERY) BIBIT'), 0, 1, 'L');
			$this->Cell($width, $height, $_SESSION['lang']['bulan'] . ' : ' . substr(tanggalnormal($periode), 1, 7), 0, 1, 'R');
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNmOrg[$kdUnit], 0, 1, 'L');
			$this->Cell(790, $height, ' ', 0, 1, 'R');
			$height = 15;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$tinggiAkr = $this->GetY();
			$ksamping = $this->GetX();
			$this->SetY($tinggiAkr + 20);
			$this->SetX($ksamping);
			$this->Cell(30, $height, 'No.', TLR, 0, 'C', 1);
			$this->Cell(130, $height, strtoupper($_SESSION['lang']['tujuan']), TLR, 0, 'C', 1);
			$this->Cell(100, $height, 'BIBIT', TLR, 0, 'C', 1);
			$this->Cell($cold, $height, 'INTERNAL DELIVERY', TLR, 0, 'C', 1);
			$this->Cell($cold2, $height, 'INTERNAL DELIVERY', TLR, 1, 'C', 1);
			$this->Cell(30, $height, '.', BLR, 0, 'C', 1);
			$this->Cell(130, $height, ' ', BLR, 0, 'C', 1);
			$this->Cell(100, $height, ' ', BLR, 0, 'C', 1);

			if ($colsPertama != 0) {
				if (!empty($kbnSndri)) {
					foreach ($kbnSndri as $dtKbn) {
						$this->Cell(55, $height, $dtKbn, TBLR, 0, 'C', 1);
					}
				}
			}
			else {
				$this->Cell($cold, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'C', 1);
			}

			if ($colsKedua != 0) {
				if (!empty($lstPlnggan)) {
					foreach ($lstPlnggan as $dtPlgn) {
						$ard += 1;

						if ($ard != $tot) {
							$this->Cell(55, $height, $dtPlgn, TBLR, 0, 'C', 1);
						}
						else {
							$this->Cell(55, $height, $dtPlgn, TBLR, 1, 'C', 1);
						}
					}
				}
			}
			else {
				$this->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'C', 1);
			}
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
	$height = 10;
	$tnggi = $jmlHari * $height;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 6);
	$i = 0;

	if (!empty($arrData)) {
		foreach ($arrData as $dtini => $isData) {
			$afdC = false;
			$blankC = false;
			$i += 1;

			if (!empty($lstBibit)) {
				foreach ($lstBibit as $dtBibit) {
					if ($afdC == false) {
						$pdf->Cell(30, $height, $i, TLR, 0, 'C', 1);
						$pdf->Cell(130, $height, $isData, TLR, 0, 'L', 1);
						$afdC = true;
					}
					else if ($blankC == false) {
						$pdf->Cell(30, $height, '  ', TLR, 0, 'C', 1);
						$pdf->Cell(130, $height, ' ', TLR, 0, 'C', 1);
						$blankC = true;
					}

					$pdf->Cell(80, $height, $dtBibit, TLR, 0, 'L', 1);
					$pdf->Cell(20, $height, $_SESSION['lang']['bi'], TLR, 0, 'L', 1);

					if ($colsPertama != 0) {
						if (!empty($kbnSndri)) {
							foreach ($kbnSndri as $lstKbn) {
								$pdf->Cell(55, $height, $jmlhKbn[$dtini][$lstKbn][$dtBibit], TBLR, 0, 'R', 1);
							}
						}
					}
					else {
						$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'L', 1);
					}

					if ($colsKedua != 0) {
						if (!empty($lstPlnggan)) {
							foreach ($lstPlnggan as $lstDt) {
								$ard += 1;

								if ($ard != $tot) {
									$pdf->Cell(55, $height, $jmlhPlgn[$dtini][$lstDt][$dtBibit], TBLR, 0, 'R', 1);
								}
								else {
									$pdf->Cell(55, $height, $jmlhPlgn[$dtini][$lstDt][$dtBibit], TBLR, 1, 'R', 1);
									$ard = 0;
								}
							}
						}
					}
					else {
						$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
					}

					$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
					$pdf->Cell(130, $height, ' ', TBLR, 0, 'C', 1);
					$pdf->Cell(80, $height, '', TBLR, 0, 'C', 1);
					$pdf->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'L', 1);

					if ($colsPertama != 0) {
						if (!empty($kbnSndri)) {
							foreach ($kbnSndri as $lstKbn) {
								$pdf->Cell(55, $height, $jmlhKbnSbi[$dtini][$lstKbn][$dtBibit], TBLR, 0, 'R', 1);
							}
						}
					}
					else {
						$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'L', 1);
					}

					if ($colsKedua != 0) {
						if (!empty($lstPlnggan)) {
							foreach ($lstPlnggan as $lstDt) {
								$ardr += 1;

								if ($ardr != $tot) {
									$pdf->Cell(55, $height, $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit], TBLR, 0, 'R', 1);
								}
								else {
									$pdf->Cell(55, $height, $jmlhPlgnSbi[$dtini][$lstDt][$dtBibit], TBLR, 1, 'R', 1);
									$ardr = 0;
								}
							}
						}
					}
					else {
						$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
					}
				}
			}

			$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
			$pdf->Cell(210, $height, 'Sub Total', TBLR, 0, 'L', 1);
			$pdf->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'L', 1);

			if ($colsPertama != 0) {
				if (!empty($kbnSndri)) {
					foreach ($kbnSndri as $lstKbn) {
						$pdf->Cell(55, $height, $sbTotKbn[$dtini][$lstKbn], TBLR, 0, 'R', 1);
					}
				}
			}
			else {
				$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'R', 1);
			}

			if ($colsKedua != 0) {
				if (!empty($lstPlnggan)) {
					foreach ($lstPlnggan as $lstDt) {
						$ardrr += 1;

						if ($ardrr != $tot) {
							$pdf->Cell(55, $height, $sbTotPlgn[$dtini][$lstDt], TBLR, 0, 'R', 1);
						}
						else {
							$pdf->Cell(55, $height, $sbTotPlgn[$dtini][$lstDt], TBLR, 1, 'R', 1);
							$ardrr = 0;
						}
					}
				}
			}
			else {
				$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
			}

			$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
			$pdf->Cell(210, $height, ' ', TBLR, 0, 'L', 1);
			$pdf->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'L', 1);

			if ($colsPertama != 0) {
				if (!empty($kbnSndri)) {
					foreach ($kbnSndri as $lstKbn) {
						$pdf->Cell(55, $height, $sbTotKbnSbi[$dtini][$lstKbn], TBLR, 0, 'R', 1);
					}
				}
			}
			else {
				$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'R', 1);
			}

			if ($colsKedua != 0) {
				if (!empty($lstPlnggan)) {
					foreach ($lstPlnggan as $lstDt) {
						$ardrrr += 1;

						if ($ardrrr != $tot) {
							$pdf->Cell(55, $height, $sbTotPlgnSbi[$dtini][$lstDt], TBLR, 0, 'R', 1);
						}
						else {
							$pdf->Cell(55, $height, $sbTotPlgnSbi[$dtini][$lstDt], TBLR, 1, 'R', 1);
							$ardrrr = 0;
						}
					}
				}
			}
			else {
				$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
			}
		}
	}

	$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
	$pdf->Cell(210, $height, 'Grand Total', TBLR, 0, 'L', 1);
	$pdf->Cell(20, $height, $_SESSION['lang']['bi'], TBLR, 0, 'L', 1);

	if ($colsPertama != 0) {
		if (!empty($kbnSndri)) {
			foreach ($kbnSndri as $lstKbn) {
				$pdf->Cell(55, $height, $grndTotKbn[$lstKbn], TBLR, 0, 'R', 1);
			}
		}
	}
	else {
		$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'L', 1);
	}

	if ($colsKedua != 0) {
		if (!empty($lstPlnggan)) {
			foreach ($lstPlnggan as $lstDt) {
				$ardrr += 1;

				if ($ardrr != $tot) {
					$pdf->Cell(55, $height, $grndPlgn[$lstDt], TBLR, 0, 'R', 1);
				}
				else {
					$pdf->Cell(55, $height, $grndPlgn[$lstDt], TBLR, 1, 'R', 1);
					$ardrr = 0;
				}
			}
		}
	}
	else {
		$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
	}

	$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
	$pdf->Cell(210, $height, ' ', TBLR, 0, 'L', 1);
	$pdf->Cell(20, $height, $_SESSION['lang']['sbi'], TBLR, 0, 'L', 1);

	if ($colsPertama != 0) {
		if (!empty($kbnSndri)) {
			foreach ($kbnSndri as $lstKbn) {
				$pdf->Cell(55, $height, $grndTotKbnSbi[$lstKbn], TBLR, 0, 'R', 1);
			}
		}
	}
	else {
		$pdf->Cell(55, $height, $_SESSION['lang']['dataempty'], TBLR, 0, 'L', 1);
	}

	if ($colsKedua != 0) {
		if (!empty($lstPlnggan)) {
			foreach ($lstPlnggan as $lstDt) {
				$ardrrr += 1;

				if ($ardrrr != $tot) {
					$pdf->Cell(55, $height, $grndPlgnSi[$lstDt], TBLR, 0, 'R', 1);
				}
				else {
					$pdf->Cell(55, $height, $grndPlgnSi[$lstDt], TBLR, 1, 'R', 1);
					$ardrrr = 0;
				}
			}
		}
	}
	else {
		$pdf->Cell($cold2, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'L', 1);
	}

	$pdf->Output();
	break;
}

?>
