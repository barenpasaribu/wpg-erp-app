<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$mid = $_POST['mid'];
$unit = $_POST['unit'];
$pemilik = $_POST['pemilik'];
$lokasi = $_POST['lokasi'];
$luas = $_POST['luas'];
$bisaditanam = $_POST['bisaditanam'];
$blok = $_POST['blok'];
$batastimur = $_POST['batastimur'];
$batasbarat = $_POST['batasbarat'];
$batasutara = $_POST['batasutara'];
$batasselatan = $_POST['batasselatan'];
$rptanaman = ($_POST['rptanaman'] == '' ? 0 : $_POST['rptanaman']);
$rptanah = ($_POST['rptanah'] == '' ? 0 : $_POST['rptanah']);
$biayakades = ($_POST['biayakades'] == '' ? 0 : $_POST['biayakades']);
$biayacamat = ($_POST['biayacamat'] == '' ? 0 : $_POST['biayacamat']);
$biayamatrai = ($_POST['biayamatrai'] == '' ? 0 : $_POST['biayamatrai']);
$statuspermintaandana = $_POST['statuspermintaandana'];
$statuspermbayaran = $_POST['statuspermbayaran'];
$statuskades = $_POST['statuskades'];
$statuscamat = $_POST['statuscamat'];
$nosurat = $_POST['nosurat'];
$keterangan = $_POST['keterangan'];

if ($_POST['tanggalpermintaan'] == '') {
	$_POST['tanggalpermintaan'] = '00-00-0000';
}

if ($_POST['tanggalbayar'] == '') {
	$_POST['tanggalbayar'] = '00-00-0000';
}

if ($_POST['tanggalkades'] == '') {
	$_POST['tanggalkades'] = '00-00-0000';
}

if ($_POST['tanggalcamat'] == '') {
	$_POST['tanggalcamat'] = '00-00-0000';
}

$tanggalpermintaan = tanggalsystem($_POST['tanggalpermintaan']);
$tanggalbayar = tanggalsystem($_POST['tanggalbayar']);
$tanggalkades = tanggalsystem($_POST['tanggalkades']);
$tanggalcamat = tanggalsystem($_POST['tanggalcamat']);
$method = $_POST['method'];

if ($method == '') {
	$method = $_GET['method'];
	$idlahan = $_GET['idlahan'];
	$pemilik = $_GET['pemilik'];
}

switch ($method) {
case 'pdf':
	$str1 = 'select padid, nama from ' . $dbname . '.pad_5masyarakat';
	$res1 = mysql_query($str1);

	while ($bar1 = mysql_fetch_object($res1)) {
		$kamuspemilik[$bar1->padid] = $bar1->nama;
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $idlahan;
			global $pemilik;
			global $kamuspemilik;
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(20, 3, $namapt, '', 1, 'L');
			$this->SetFont('Arial', 'B', 12);
			$this->Cell(190, 3, strtoupper($_SESSION['lang']['pembebasan'] . ' ' . $_SESSION['lang']['lahan']), 0, 1, 'C');
			$this->SetFont('Arial', '', 7);
			$this->Cell(150, 3, ' ', '', 0, 'R');
			$this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
			$this->Cell(2, 3, ':', '', 0, 'L');
			$this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
			$this->Cell(28, 3, $_SESSION['lang']['id'], '', 0, 'L');
			$this->Cell(2, 3, ':', '', 0, 'L');
			$this->Cell(120, 3, $idlahan, 0, 0, 'L');
			$this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
			$this->Cell(2, 3, ':', '', 0, 'L');
			$this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
			$this->Cell(28, 3, $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['namapemilik'] . ' ' . $_SESSION['lang']['lahan'], '', 0, 'L');
			$this->Cell(2, 3, ':', '', 0, 'L');
			$this->Cell(120, 3, $kamuspemilik[$pemilik], 0, 0, 'L');
			$this->Cell(15, 3, 'User', '', 0, 'L');
			$this->Cell(2, 3, ':', '', 0, 'L');
			$this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
			$this->Ln();
			$this->Ln();
		}
	}


	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$str1 = 'select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ' . $dbname . '.pad_lahan a' . "\r\n" . '            left join ' . $dbname . '.pad_5masyarakat b on a.pemilik=b.padid ' . "\r\n" . '            left join ' . $dbname . '.datakaryawan c on a.updateby=c.karyawanid    ' . "\r\n" . '            where idlahan = \'' . $idlahan . '\'';
	$res1 = mysql_query($str1);

	while ($bar1 = mysql_fetch_object($res1)) {
		$stdana = ($bar1->statuspermintaandana == 1 ? tanggalnormal($bar1->tanggalpengajuan) : '');

		if ($bar1->statuspermbayaran == 1) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Belum Lunas';
		}
		else if ($bar1->statuspermbayaran == 0) {
			$stbayar = 'Belum Bayar';
		}
		else if ($bar1->statuspermbayaran == 2) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Lunas';
		}

		$stkades = ($bar1->statuskades == 1 ? tanggalnormal($bar1->tanggalkades) : '');
		$stcamat = ($bar1->statuscamat == 1 ? tanggalnormal($bar1->tanggalcamat) : '');
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(100, 5, '1.' . $_SESSION['lang']['namapemilik'] . ' ' . $_SESSION['lang']['lahan'], 0, 0, 'L');
		$pdf->Cell(100, 5, '2.' . $_SESSION['lang']['biaya'] . '-' . $_SESSION['lang']['biaya'] . ' dan ' . $_SESSION['lang']['status'] . '-' . $_SESSION['lang']['dokumen'], 0, 0, 'L');
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 7);
		$pdf->Cell(35, 5, $_SESSION['lang']['id'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->idlahan, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['tanamtumbuh'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, number_format($bar1->rptanaman, 0), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['kebun'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->unit, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['gantilahan'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, number_format($bar1->rptanah, 0), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['namapemilik'] . ' ' . $_SESSION['lang']['lahan'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->nama, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['kepala'] . ' ' . $_SESSION['lang']['desa'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, number_format($bar1->biayakades, 0), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['keterangan'] . ' ' . $_SESSION['lang']['lokasi'] . '(No.Persil)', 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->lokasi, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['biaya'] . ' ' . $_SESSION['lang']['camat'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, number_format($bar1->biayacamat, 0), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['luas'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(23, 5, $bar1->luas . ' Ha.', 0, 0, 'R');
		$pdf->Cell(40, 5, '', 0, 0, 'R');
		$pdf->Cell(35, 5, $_SESSION['lang']['biaya'] . ' Matrai', 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, number_format($bar1->biayamatrai, 0), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['luas'] . ' ' . $_SESSION['lang']['bisaditanam'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(23, 5, $bar1->luasdapatditanam . ' Ha.', 0, 0, 'R');
		$pdf->Cell(40, 5, '', 0, 0, 'R');
		$pdf->Cell(35, 5, $_SESSION['lang']['status'] . ' ' . $_SESSION['lang']['permintaandana'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(6333, 5, $stdana, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['lokasi'] . ' ' . $_SESSION['lang']['kodeblok'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->kodeblok, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['status'] . ' ' . $_SESSION['lang']['pembayaran'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, $stbayar, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['batastimur'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->batastimur, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['status'] . ' ' . $_SESSION['lang']['kepala'] . ' ' . $_SESSION['lang']['desa'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, $stkades, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['batasbarat'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->batasbarat, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['status'] . ' ' . $_SESSION['lang']['camat'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, $stcamat, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['batasutara'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->batasutara, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['nomor'] . ' ' . $_SESSION['lang']['dokumen'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, $bar1->nosurat, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(35, 5, $_SESSION['lang']['batasselatan'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(63, 5, $bar1->batasselatan, 0, 0, 'L');
		$pdf->Cell(35, 5, $_SESSION['lang']['keterangan'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(33, 5, $bar1->keterangan, 0, 0, 'L');
		$pdf->Ln();
		$pdf->Ln();
	}

	$str1 = 'select * from ' . $dbname . '.pad_photo' . "\r\n" . '            where idlahan = \'' . $idlahan . '\'';
	$res1 = mysql_query($str1);

	while ($bar1 = mysql_fetch_object($res1)) {
		$pdf->Cell(13, 5, $_SESSION['lang']['photo'], 0, 0, 'L');
		$pdf->Cell(2, 5, ':', 0, 0, 'L');
		$pdf->Cell(73, 5, $bar1->filename, 0, 0, 'L');
		$pdf->Ln();
		$yey = $pdf->GetY();
		$path = 'filepad/' . $bar1->filename;
		$pdf->Image($path, 25, $yey, 70);
		$pdf->SetY($yey + 80);
		$pdf->Ln();
	}

	$pdf->Output();
	exit();
	break;

case 'update':
	$str = 'update ' . $dbname . '.pad_lahan ' . "\r\n" . '         set pemilik=' . $pemilik . ', ' . "\r\n" . '         unit=\'' . $unit . '\\', ' . "\r\n" . '         lokasi=\'' . $lokasi . '\\', ' . "\r\n" . '         luas=' . $luas . ', ' . "\r\n" . '         luasdapatditanam=' . $bisaditanam . ', ' . "\r\n" . '         rptanaman=' . $rptanaman . ', ' . "\r\n" . '         rptanah=' . $rptanah . ', ' . "\r\n" . '         totalgantirugi=' . ($rptanaman + $rptanah) . ', ' . "\r\n" . '         statuspermintaandana=' . $statuspermintaandana . ', ' . "\r\n" . '         statuspermbayaran=' . $statuspermbayaran . ', ' . "\r\n" . '         kodeblok=\'' . $blok . '\\', ' . "\r\n" . '         statuskades=' . $statuskades . ', ' . "\r\n" . '         statuscamat=' . $statuscamat . ', ' . "\r\n" . '         tanggalpengajuan=' . $tanggalpermintaan . ', ' . "\r\n" . '         tanggalbayar=' . $tanggalbayar . ', ' . "\r\n" . '         tanggalkades=' . $tanggalkades . ', ' . "\r\n" . '         tanggalcamat=' . $tanggalcamat . ', ' . "\r\n" . '         updateby=' . $_SESSION['standard']['userid'] . ', ' . "\r\n" . '         biayakades=' . $biayakades . ', ' . "\r\n" . '         biayacamat=' . $biayacamat . ', ' . "\r\n" . '         biayamatrai=' . $biayamatrai . ', ' . "\r\n" . '         keterangan=\'' . $keterangan . '\\', ' . "\r\n" . '         nosurat=\'' . $nosurat . '\\', ' . "\r\n" . '         batastimur=\'' . $batastimur . '\\', ' . "\r\n" . '         batasbarat=\'' . $batasbarat . '\\', ' . "\r\n" . '         batasutara=\'' . $batasutara . '\\', ' . "\r\n" . '         batasselatan=\'' . $batasselatan . '\'' . "\r\n" . '        where idlahan=' . $mid;

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'insert':
	$str = 'insert into ' . $dbname . '.pad_lahan (' . "\r\n" . '              pemilik, ' . "\r\n" . '              unit, ' . "\r\n" . '              lokasi, ' . "\r\n" . '              luas, ' . "\r\n" . '              luasdapatditanam, ' . "\r\n" . '              rptanaman, ' . "\r\n" . '              rptanah, ' . "\r\n" . '              totalgantirugi, ' . "\r\n" . '              statuspermintaandana, ' . "\r\n" . '              statuspermbayaran, ' . "\r\n" . '              kodeblok, ' . "\r\n" . '              statuskades, ' . "\r\n" . '              statuscamat, ' . "\r\n" . '              tanggalpengajuan, ' . "\r\n" . '              tanggalbayar, ' . "\r\n" . '              tanggalkades, ' . "\r\n" . '              tanggalcamat, ' . "\r\n" . '              updateby,  ' . "\r\n" . '              biayakades, ' . "\r\n" . '              biayacamat, ' . "\r\n" . '              biayamatrai, ' . "\r\n" . '              keterangan, ' . "\r\n" . '              nosurat, ' . "\r\n" . '              batastimur, ' . "\r\n" . '              batasbarat, ' . "\r\n" . '              batasutara, ' . "\r\n" . '              batasselatan)' . "\r\n" . '              values(' . "\r\n" . '              ' . $pemilik . ',' . "\r\n" . '              \'' . $unit . '\',  ' . "\r\n" . '              \'' . $lokasi . '\',' . "\r\n" . '              ' . $luas . ',' . "\r\n" . '              ' . $bisaditanam . ',' . "\r\n" . '              ' . $rptanaman . ', ' . "\r\n" . '              ' . $rptanah . ', ' . "\r\n" . '              ' . ($rptanaman + $rptanah) . ',' . "\r\n" . '              ' . $statuspermintaandana . ', ' . "\r\n" . '              ' . $statuspermbayaran . ',   ' . "\r\n" . '              \'' . $blok . '\\', ' . "\r\n" . '              ' . $statuskades . ',    ' . "\r\n" . '              ' . $statuscamat . ',' . "\r\n" . '              ' . $tanggalpermintaan . ',' . "\r\n" . '              ' . $tanggalbayar . ',' . "\r\n" . '              ' . $tanggalkades . ',' . "\r\n" . '              ' . $tanggalcamat . ',' . "\r\n" . '              ' . $_SESSION['standard']['userid'] . ',' . "\r\n" . '              ' . $biayakades . ',' . "\r\n" . '              ' . $biayacamat . ', ' . "\r\n" . '              ' . $biayamatrai . ',' . "\r\n" . '              \'' . $keterangan . '\\', ' . "\r\n" . '              \'' . $nosurat . '\\', ' . "\r\n" . '             \'' . $batastimur . '\',' . "\r\n" . '             \'' . $batasbarat . '\\', ' . "\r\n" . '             \'' . $batasutara . '\\', ' . "\r\n" . '             \'' . $batasselatan . '\'    ' . "\r\n" . '              )';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn)) . $str;
		exit();
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.pad_lahan' . "\r\n" . '        where idlahan=\'' . $mid . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'getPemilik':
	$str = 'select padid,nama,desa from ' . $dbname . '.pad_5masyarakat ' . "\r\n" . '        where desa in (select namadesa from ' . $dbname . '.pad_5desa where unit=\'' . $_POST['unit'] . '\')';
	$optpemilik = '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_object($res)) {
			$optpemilik .= '<option value=\'' . $bar->padid . '\'>' . $bar->nama . '-' . $bar->desa . '</option>';
		}

		if ($optpemilik != '') {
			echo $optpemilik;
		}
		else {
			echo 'Error: Masyarakat pemilik belum ada, silahkan daftar dari menu setup';
		}

		exit();
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'getBlok':
	$str = 'select kodeorganisasi,namaorganisasi  from ' . $dbname . '.organisasi ' . "\r\n" . '        where tipe=\'BLOK\' and kodeorganisasi like \'' . $_POST['unit'] . '%\'';
	$optblok = '<option value=\'\'>Undefined</option>';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_object($res)) {
			$optblok .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
		}

		echo $optblok;
		exit();
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}

	break;

case 'posting':
	$str = 'update ' . $dbname . '.pad_lahan set posting=1 where idlahan=' . $mid;

	if ($res = mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
		exit();
	}
}

$str1 = ($mid = $_POST['mid']) . '.pad_lahan a' . "\r\n" . '            left join ' . $dbname . '.pad_5masyarakat b on a.pemilik=b.padid ' . "\r\n" . '            left join ' . $dbname . '.datakaryawan c on a.updateby=c.karyawanid    ' . "\r\n" . '            where posting=0 and unit=\'' . $unit . '\' order by b.nama,b.desa limit 500';

if ($res1 = mysql_query($str1)) {
	echo '<table class=sortable cellspacing=1 border=0 width=2500px>' . "\r\n" . '         <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '               <td style=\'width:30px;\' rowspan=2>*</td>                ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['id'] . '</td>' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['unit'] . '</td>                     ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['pemilik'] . '</td>' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['lokasi'] . '/(No.Persil)</td>                       ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['desa'] . '</td>               ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['luas'] . '</td>    ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['bisaditanam'] . '</td> ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['blok'] . '</td>    ' . "\r\n" . '                <td colspan=4 align=center>' . $_SESSION['lang']['batas'] . '</td> ' . "\r\n" . '                <td colspan=7 align=center>' . $_SESSION['lang']['biaya'] . '-' . $_SESSION['lang']['biaya'] . '</td>  ' . "\r\n" . '                <td colspan=4 align=center>' . $_SESSION['lang']['status'] . '</td>    ' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['nomor'] . ' ' . $_SESSION['lang']['dokumen'] . '</td>' . "\r\n" . '                <td rowspan=2>' . $_SESSION['lang']['keterangan'] . '</td> ' . "\r\n" . '                 <td rowspan=2>' . $_SESSION['lang']['updateby'] . '</td>   ' . "\r\n" . '                 </tr><tr class=rowheader>   ' . "\r\n" . '                <td>' . $_SESSION['lang']['batastimur'] . '</td>                      ' . "\r\n" . '                <td>' . $_SESSION['lang']['batasbarat'] . '</td>  ' . "\r\n" . '                <td>' . $_SESSION['lang']['batasutara'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['batasselatan'] . '</td> ' . "\r\n" . '                    ' . "\r\n" . '                <td>' . $_SESSION['lang']['tanamtumbuh'] . ' (Rp)</td> ' . "\r\n" . '                <td>' . $_SESSION['lang']['gantilahan'] . ' (Rp)</td> ' . "\r\n" . '                <td>' . $_SESSION['lang']['total'] . '<br>' . $_SESSION['lang']['gantilahan'] . ' (Rp)</td>    ' . "\r\n" . '                <td>' . $_SESSION['lang']['biaya'] . '<br>' . $_SESSION['lang']['camat'] . ' (Rp)</td> ' . "\r\n" . '                <td>' . $_SESSION['lang']['biaya'] . '<br>' . $_SESSION['lang']['kades'] . ' (Rp)</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['biaya'] . '<br>Matrai (Rp)</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['total'] . '<br>' . $_SESSION['lang']['biaya'] . ' (Rp)</td>     ' . "\r\n" . '                    ' . "\r\n" . '                <td>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['permintaandana'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['pembayaran'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['camat'] . '</td>' . "\r\n" . '                </tr></thead>' . "\r\n" . '                <tbody>';

	while ($bar1 = mysql_fetch_object($res1)) {
		$stdana = ($bar1->statuspermintaandana == 1 ? tanggalnormal($bar1->tanggalpengajuan) : '');

		if ($bar1->statuspermbayaran == 1) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Belum Lunas';
		}
		else if ($bar1->statuspermbayaran == 0) {
			$stbayar = 'Belum Bayar';
		}
		else if ($bar1->statuspermbayaran == 2) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Lunas';
		}

		$stkades = ($bar1->statuskades == 1 ? tanggalnormal($bar1->tanggalkades) : '');
		$stcamat = ($bar1->statuscamat == 1 ? tanggalnormal($bar1->tanggalcamat) : '');
		echo '<tr class=rowcontent>                 ' . "\r\n" . '                          <td width=\'100px;\'>' . "\r\n" . '                               <img src=\'images/application/application_view_gallery.png\' class=\'resicon\' title=\'Upload Document\' onclick=uploadDocument(\'' . $bar1->idlahan . '\',\'' . $bar1->pemilik . '\',event)>' . "\r\n" . '                               <img src=\'images/skyblue/pdf.jpg\' class=\'resicon\' onclick="ptintPDF(\'' . $bar1->idlahan . '\',\'' . $bar1->pemilik . '\',event);" title=\'Print Data Detail\'>' . "\r\n" . '                               <img src=\'images/skyblue/edit.png\' class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->idlahan . '\',\'' . $bar1->pemilik . '\',\'' . $bar1->unit . '\',\'' . $bar1->lokasi . '\',\'' . $bar1->luas . '\',\'' . $bar1->luasdapatditanam . '\',\'' . $bar1->rptanaman . '\',\'' . $bar1->rptanah . '\',\'' . $bar1->statuspermintaandana . '\',\'' . $bar1->statuspermbayaran . '\',\'' . $bar1->kodeblok . '\',\'' . $bar1->statuskades . '\',\'' . $bar1->statuscamat . '\',\'' . tanggalnormal($bar1->tanggalpengajuan) . '\',\'' . tanggalnormal($bar1->tanggalbayar) . '\',\'' . tanggalnormal($bar1->tanggalkades) . '\',\'' . tanggalnormal($bar1->tanggalcamat) . '\',\'' . $bar1->biayakades . '\',\'' . $bar1->biayacamat . '\',\'' . $bar1->biayamatrai . '\',\'' . $bar1->keterangan . '\',\'' . $bar1->nosurat . '\',\'' . $bar1->batastimur . '\',\'' . $bar1->batasbarat . '\',\'' . $bar1->batasutara . '\',\'' . $bar1->batasselatan . '\');">' . "\r\n" . '                                <img src=\'images/skyblue/posting.png\' class=\'resicon\' onclick="postingData(\'' . $bar1->idlahan . '\',\'' . $bar1->unit . '\')" title=\'Posting\'>' . "\r\n" . '                                <img src=\'images/skyblue/delete.png\' class=\'resicon\' onclick="deleteData(\'' . $bar1->idlahan . '\',\'' . $bar1->unit . '\');" title=\'Delete\'>' . "\r\n" . '                           </td>' . "\r\n" . '                           <td>' . $bar1->idlahan . '</td>' . "\r\n" . '                           <td>' . $bar1->unit . '</td>' . "\r\n" . '                           <td>' . $bar1->nama . '</td>' . "\r\n" . '                           <td>' . $bar1->lokasi . '</td>                                 ' . "\r\n" . '                           <td>' . $bar1->desa . '</td>' . "\r\n" . '                           <td align=right>' . $bar1->luas . '</td>  ' . "\r\n" . '                           <td align=right>' . $bar1->luasdapatditanam . '</td>' . "\r\n" . '                           <td>' . $bar1->kodeblok . '</td>    ' . "\r\n" . '                           <td>' . $bar1->batastimur . '</td>' . "\r\n" . '                           <td>' . $bar1->batasbarat . '</td>' . "\r\n" . '                           <td>' . $bar1->batasutara . '</td>' . "\r\n" . '                           <td>' . $bar1->batasselatan . '</td>  ' . "\r\n" . '                           <td align=right>' . number_format($bar1->rptanaman, 0) . '</td>    ' . "\r\n" . '                           <td align=right>' . number_format($bar1->rptanah, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->totalgantirugi, 0) . '</td>    ' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayakades, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayacamat, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayamatrai, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->totalgantirugi + $bar1->biayakades + $bar1->biayacamat + $bar1->biayamatrai, 0) . '</td>' . "\r\n" . '                            <td>' . $stdana . '</td>' . "\r\n" . '                           <td>' . $stbayar . '</td>' . "\r\n" . '                           <td>' . $stkades . '</td>' . "\r\n" . '                           <td>' . $stcamat . '</td>        ' . "\r\n" . '                           <td>' . $bar1->nosurat . '</td>  ' . "\r\n" . '                           <td>' . $bar1->keterangan . '</td>   ' . "\r\n" . '                           <td>' . $bar1->namakaryawan . '</td>                                ' . "\r\n" . '                            </td></tr>';
	}

	echo "\t" . ' ' . "\r\n" . '                 </tbody>' . "\r\n" . '                 <tfoot>' . "\r\n" . '                 </tfoot>' . "\r\n" . '                 </table>';
}

?>
