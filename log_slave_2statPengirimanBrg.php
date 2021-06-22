<?php


function selisihHari($tglAwal, $tglAkhir)
{
	$pecah1 = explode('-', $tglAwal);
	$date1 = $pecah1[2];
	$month1 = $pecah1[1];
	$year1 = $pecah1[0];
	$pecah2 = explode('-', $tglAkhir);
	$date2 = $pecah2[2];
	$month2 = $pecah2[1];
	$year2 = $pecah2[0];
	$jd1 = GregorianToJD($month1, $date1, $year1);
	$jd2 = GregorianToJD($month2, $date2, $year2);
	$selisih = $jd2 - $jd1;
	return $selisih;
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

if ($_SESSION['language'] == 'EN') {
	$zz = 'kelompok1 as kelompok';
}
else {
	$zz = 'kelompok';
}

$sKlmpk = 'select kode,' . $zz . ' from ' . $dbname . '.log_5klbarang order by kode';

#exit(mysql_error());
($qKlmpk = mysql_query($sKlmpk)) || true;

while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
	$rKelompok[$rKlmpk['kode']] = $rKlmpk['kelompok'];
}

$optNmOrang = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');
$where2 = 'tipetransaksi=1 and substr(tanggal,1,7)>=\'' . $periode . '\'';
$_POST['klmpkBrg'] == '' ? $klmpkBrg = $_GET['klmpkBrg'] : $klmpkBrg = $_POST['klmpkBrg'];
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['periodesmp'] == '' ? $periodesmp = $_GET['periodesmp'] : $periodesmp = $_POST['periodesmp'];
$_POST['purchId'] == '' ? $purchId = $_GET['purchId'] : $purchId = $_POST['purchId'];
$_POST['supplierId'] == '' ? $supplierId = $_GET['supplierId'] : $supplierId = $_POST['supplierId'];
$_POST['nopo'] == '' ? $nopo = $_GET['nopo'] : $nopo = $_POST['nopo'];
$dktlmpk = $_SESSION['lang']['all'];
$awl = $periode . '-01';
$akhr = $periodesmp . '-01';
$seltgl = selisihhari($awl, $akhr);

if ($seltgl < 0) {
	exit('error:' . "\n" . ' Periode Salah');
}

if (365 < $seltgl) {
	exit('error:' . "\n" . ' Lebih dari satu tahun');
}

if ($nopo != '') {
	$where = '';
	$where = ' and nopo like \'%' . $nopo . '%\'';
}
else {
	if (($periode == '') || ($periodesmp == '')) {
		exit('error: ' . $_SESSION['lang']['periode'] . ' can\'t empty');
	}

	if (($periode != '') || ($periodesmp != '')) {
		$where = ' and left(tanggal,7) between \'' . $periode . '\' and \'' . $periodesmp . '\'';
	}

	if ($kdUnit != '') {
		$where .= ' and kodeorg=\'' . $kdUnit . '\'';
		$unitId = $optNmOrg[$kdUnit];
	}

	if ($klmpkBrg != '') {
		$where .= ' and substr(kodebarang,1,3)=\'' . $klmpkBrg . '\'';
		$dktlmpk = $rKelompok[$klmpkBrg];
	}
	if ($supplierId != '') {
		$where .= ' and kodesupplier = \''.$supplierId.'\'';
	}

}


$klmpkBarang='';
$data = array();
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];
$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=15 align=left><b><font size=5>' . $_SESSION['lang']['statPengiriman'] . '</font></b></td></tr>' . "\r\n" . '    <tr><td colspan=15 align=left>' . $_SESSION['lang']['pt'] . ' : ' . $unitId . '</td></tr>' . "\r\n" . '    <tr><td colspan=15 align=left>' . $_SESSION['lang']['periode'] . ' : ' . $periode . '</td></tr>' . "\r\n" . '    <tr><td colspan=15 align=left>' . $_SESSION['lang']['kelompokbarang'] . ' : ' . $rKelompok[$klmpkBrg] . '</td></tr>    ' . "\r\n" . '    </table>';
}

$brs = 0;
$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=3 align=center>' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' colspan=8 align=center>Delivery Document</td>';
$tab .= '</tr>';
$tab .= '<tr>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nobpb1'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['jumlah'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nopacking1'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nosj'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglsj1'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['nokonosemen'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglpengapalan1'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalberangkat'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggaltiba'] . '</td>';
$tab .= '</tr></thead><tbody>';
$sData = 'select distinct * from ' . $dbname . '.log_po_vw where nopo!=\'\' ' . $where . ' order by kodebarang asc';
#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	if ($klmpkBarang != substr($rData['kodebarang'], 0, 3)) {
		$brs = 1;
	}

	if ($brs == 1) {
		$no = 0;
		$klmpkBarang = substr($rData['kodebarang'], 0, 3);
		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td colspan=3><b>' . $klmpkBarang . '</b></td><td colspan=3><b>' . $rKelompok[$klmpkBarang] . '</b></td>';
		$tab .= '<td colspan=11>&nbsp;</td>';
		$tab .= '</tr>';
		$brs = 0;
	}

	$no += 1;
	$tab .= '<tr class=\'rowcontent\'>';
	$tab .= '<td>' . $no . '</td>';
	$tab .= '<td>' . $rData['nopo'] . '</td>';
	$tab .= '<td>' . $rData['kodebarang'] . '</td>';
	$tab .= '<td>' . $optNmBarang[$rData['kodebarang']] . '</td>';
	$tab .= '<td>' . $rData['satuan'] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['hargasatuan'], 2) . '</td>';
	$sTbpb = 'select distinct tanggal,notransaksi,jumlah from ' . $dbname . '.log_transaksi_vw where ' . "\r\n" . '                    nopo=\'' . $rData['nopo'] . '\' and kodebarang=\'' . $rData['kodebarang'] . '\'';

	#exit(mysql_error($conn));
	($qTbpb = mysql_query($sTbpb)) || true;
	$rTbpb = mysql_fetch_assoc($qTbpb);
	$tab .= '<td>' . $rTbpb['notransaksi'] . '</td>';
	$tglbpb = '';

	if ($rTbpb['tanggal'] != '') {
		$tglbpb = $rTbpb['tanggal'];
	}

	$tab .= '<td>' . $tglbpb . '</td>';
	$tab .= '<td align=right>' . number_format($rTbpb['jumlah'], 2) . '</td>';
	$whereet = 'nobpb=\'' . $rTbpb['notransaksi'] . '\' and kodebarang=\'' . $rData['kodebarang'] . '\'';
	$sTbpb2 = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_packing_vw where ' . "\r\n" . '                    ' . $whereet . '';

	#exit(mysql_error($conn));
	($qTbpb2 = mysql_query($sTbpb2)) || true;
	$rTbpb2 = mysql_fetch_assoc($qTbpb2);
	$tab .= '<td>' . $rTbpb2['notransaksi'] . '</td>';
	$tab .= '<td>' . $rTbpb2['tanggal'] . '</td>';
	$whereet2 = 'nopo=\'' . $rData['nopo'] . '\' and (kodebarang=\'' . $rData['kodebarang'] . '\' or kodebarang=\'' . $rTbpb2['notransaksi'] . '\')';
	$sTbpb3 = 'select distinct tanggal,nosj from ' . $dbname . '.log_suratjalan_vw where ' . "\r\n" . '                    ' . $whereet2 . '';

	#exit(mysql_error($conn));
	($qTbpb3 = mysql_query($sTbpb3)) || true;
	$rTbpb3 = mysql_fetch_assoc($qTbpb3);
	$tab .= '<td>' . $rTbpb3['nosj'] . '</td>';
	$tab .= '<td>' . $rTbpb3['tanggal'] . '</td>';
	$whereet2 = 'nopo=\'' . $rData['nopo'] . '\' and (kodebarang=\'' . $rData['kodebarang'] . '\' or kodebarang=\'' . $rTbpb2['notransaksi'] . '\')';
	$sTbpbc = 'select distinct nokonosemen,tanggal,tanggaltiba,tanggalberangkat from ' . $dbname . '.log_konosemen_vw where ' . "\r\n" . '                    ' . $whereet2 . '';

	#exit(mysql_error($conn));
	($qTbpbc = mysql_query($sTbpbc)) || true;
	$rTbpbc = mysql_fetch_assoc($qTbpbc);
	$tab .= '<td>' . $rTbpbc['nosj'] . '</td>';
	$tab .= '<td>' . $rTbpbc['tanggal'] . '</td>';
	$tab .= '<td>' . $rTbpbc['tanggalberangkat'] . '</td>';
	$tab .= '<td>' . $rTbpbc['tanggaltiba'] . '</td>';
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
	$nop_ = 'statPengiriman_brg__' . $dte;
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
		$sNm = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $supplierId . '\'';

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
