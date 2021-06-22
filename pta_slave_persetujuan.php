<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan = array($_SESSION['lang']['diajukan'], $_SESSION['lang']['disetujui'], $_SESSION['lang']['ditolak']);
$where = ' tanggal=\'' . $tglijin . '\' and karyawanid=\'' . $krywnId . '\'';
$optJumlah = makeOption($dbname, 'pta_dt', 'notransaksi,jumlah');
$arragama = getEnum($dbname, 'sdm_ijin', 'jenisijin');
$optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNamaAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$_POST['notransaksi'] == '' ? $notransaksi = $_GET['notransaksi'] : $notransaksi = $_POST['notransaksi'];
$krywnId = $_POST['krywnId'];
$stat = $_POST['stat'];
$ket = $_POST['ket'];
$perKe = $_POST['perKe'];
$txtCari = $_POST['txtCari'];
$arrKeputusan = array('Belum Ada Status', 'Setuju', 'Ditolak');

switch ($proses) {
case 'loadData':
	if ($_POST['txtCari'] == '') {
		$periodeAktif = date('Y-m');
		$where = 'substr(tanggal,1,7)=\'' . $periodeAktif . '\'';
	}
	else {
		$where = 'notransaksi like \'%' . $_POST['txtCari'] . '%\'';
	}

	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pta_ht where ' . $where . '  order by `tanggal` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$slvhc = 'select * from ' . $dbname . '.pta_ht where ' . $where . '   order by `tanggal` desc limit ' . $offset . ',' . $limit . ' ';

	#exit(mysql_error());
	($qlvhc = mysql_query($slvhc)) || true;
	$$user_online = $_SESSION['standard']['userid'];

	while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
		$no += 1;
		$sData = 'select sum(rupiah) as rupiah from ' . $dbname . '.pta_dt where notransaksi=\'' . $rlvhc['notransaksi'] . '\'';

		#exit(mysql_error($conn));
		($qData = mysql_query($sData)) || true;
		$rData = mysql_fetch_assoc($qData);
		echo "\r\n" . '                    <tr class=rowcontent>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td>' . $rlvhc['notransaksi'] . '</td>' . "\r\n" . '                    <td>' . $rlvhc['penjelasan'] . '</td>' . "\r\n" . '                    <td align=right>' . number_format($rData['rupiah'], 2) . '</td>';

		if (($rlvhc['persetujuan5'] == $_SESSION['standard']['userid']) && ($rlvhc['status5'] == 0)) {
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppTolak(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan5'] . '\',\'5\',event)>' . $_SESSION['lang']['ditolak'] . '</button></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppSetuju(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan5'] . '\',\'5\',event)>' . $_SESSION['lang']['disetujui'] . '</button></td>';
		}
		else if (($rlvhc['persetujuan4'] == $_SESSION['standard']['userid']) && ($rlvhc['status4'] == 0)) {
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppTolak(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan4'] . '\',\'4\',event)>' . $_SESSION['lang']['ditolak'] . '</button></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=appSetuju(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan4'] . '\',\'4\')>' . $_SESSION['lang']['disetujui'] . '</button></td>';
		}
		else if (($rlvhc['persetujuan3'] == $_SESSION['standard']['userid']) && ($rlvhc['status3'] == 0)) {
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppTolak(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan3'] . '\',\'3\',event)>' . $_SESSION['lang']['ditolak'] . '</button></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppSetuju(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan3'] . '\',\'3\',event)>' . $_SESSION['lang']['disetujui'] . '</button></td>';
		}
		else if (($rlvhc['persetujuan2'] == $_SESSION['standard']['userid']) && ($rlvhc['status2'] == 0)) {
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppTolak(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan2'] . '\',\'2\',event)>' . $_SESSION['lang']['ditolak'] . '</button></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppSetuju(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan2'] . '\',\'2\',event)>' . $_SESSION['lang']['disetujui'] . '</button></td>';
		}
		else if (($rlvhc['persetujuan1'] == $_SESSION['standard']['userid']) && ($rlvhc['status1'] == 0)) {
			echo '<td align=center><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppTolak(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan1'] . '\',\'1\',event)>' . $_SESSION['lang']['ditolak'] . '</button></td>';
			echo '<td align=center><button class=mybutton id=dtlForm onclick=showAppSetuju(\'' . $rlvhc['notransaksi'] . '\',\'' . $rlvhc['persetujuan1'] . '\',\'1\',event)>' . $_SESSION['lang']['disetujui'] . '</button></td>';
		}
		else {
			echo '<td align=center colspan=3><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="previewPdf(\'' . $rlvhc['notransaksi'] . '\',event)"></td>';
		}
	}

	echo "\r\n\t\t" . '</tr><tr class=rowheader><td colspan=9 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	break;

case 'appSetuju':
	if ($stat == '2') {
		if ($ket == '') {
			exit('Error:Catatan Tidak Boleh Kosong!!!');
		}

		$sUpdate = 'update ' . $dbname . '.pta_ht set status' . $perKe . '=\'2\',catatan' . $perKe . '=\'' . $ket . '\' ' . "\r\n" . '                     where persetujuan' . $perKe . '=\'' . $krywnId . '\' and status' . $perKe . '=\'0\' and notransaksi=\'' . $notransaksi . '\'';
	}
	else if ($stat == '1') {
		$sUpdate = 'update ' . $dbname . '.pta_ht set status' . $perKe . '=\'1\',catatan' . $perKe . '=\'' . $ket . '\' ' . "\r\n" . '                     where persetujuan' . $perKe . '=\'' . $krywnId . '\' and status' . $perKe . '=\'0\' and notransaksi=\'' . $notransaksi . '\'';
	}
	else if ($stat == '3') {
		$forwrd = $perKe + 1;

		if (5 < $forwrd) {
			exit('Error:Limit pengajuan hanya 5 saja');
		}

		$sKary = 'select distinct persetujuan' . $perKe . ' from ' . $dbname . '.pta_ht where notransaksi=\'' . $notransaksi . '\'';

		#exit(mysql_error($conn));
		($qKary = mysql_query($sKary)) || true;
		$rKary = mysql_fetch_assoc($qKary);
		$sUpdate = 'update ' . $dbname . '.pta_ht set status' . $perKe . '=\'1\',catatan' . $perKe . '=\'' . $ket . '\',persetujuan' . $forwrd . '=\'' . $krywnId . '\' ' . "\r\n" . '                     where persetujuan' . $perKe . '=\'' . $rKary['persetujuan' . $perKe] . '\' and status' . $perKe . '=\'0\' and notransaksi=\'' . $notransaksi . '\'';
	}

	if (mysql_query($sUpdate)) {
		if ($stat == '1') {
			$to = getUserEmail($krywnId);
			$subject = '[Notifikasi] Persetujuan PTA ';
			$body = '<html>' . "\r\n" . '                                        <head>' . "\r\n" . '                                        <body>' . "\r\n" . '                                        <dd>Dengan Hormat,</dd><br>' . "\r\n" . '                                        <br>' . "\r\n" . '                                        Pada hari ini karyawan A/n ' . $_SESSION['empl']['name'] . ' mengajukan persetujuan PTA ' . "\r\n" . '                                        No.' . $notransaksi . ' kepada bapak/ibu, untuk menindaklanjuti silahkan click link dibawah.' . "\r\n" . '                                        <br>' . "\r\n" . '                                        <br>' . "\r\n" . '                                        <br>' . "\r\n" . '                                        Regards,<br>' . "\r\n" . '                                        eAgro Plantation Management Software.' . "\r\n" . '                                        </body>' . "\r\n" . '                                        </head>' . "\r\n" . '                                    </html>';
			$kirim = kirimEmailWindows($to, $subject, $body);
		}
	}
	else {
		exit('DB:Error' . mysql_error($conn) . '__' . $sUpdate);
	}

	break;

case 'getForm':
	$optKary = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sKary = 'select distinct karyawanid,namakaryawan from ' . $dbname . '.datakaryawan ' . "\r\n" . '                          where tipekaryawan=5 and tanggalkeluar is NULL and karyawanid!=\'' . $krywnId . '\' order by namakaryawan asc';

	#exit(mysql_error($conn));
	($qKary = mysql_query($sKary)) || true;

	while ($rKary = mysql_fetch_assoc($qKary)) {
		$optKary .= '<option value=\'' . $rKary['karyawanid'] . '\'>' . $rKary['namakaryawan'] . '</option>';
	}

	$tab .= '<fieldset><legend>' . $notransaksi . '</legend>';
	$tab .= '<table cellpadding=1 cellspacing=1 border=0>';
	$tab .= '<tr><td>' . $_SESSION['lang']['namakaryawan'] . '</td><td>:</td><td><select id=dtKary>' . $optKary . '</select></td></tr>';
	$tab .= '<tr><td>' . $_SESSION['lang']['keterangan'] . '</td><td>:</td><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr>';
	$tab .= '<tr><td colspan=3 align=center><button class=mybutton onclick=saveAjukan(\'' . $notransaksi . '\',\'' . $perKe . '\')>' . $_SESSION['lang']['diajukan'] . '</button></td></tr></table>';
	$tab .= '</fieldset>';
	echo $tab;
	break;

case 'prevPdf':
	class PDF extends FPDF
	{
		public function Header()
		{
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

			$this->Image($path, 15, 2, 40);
			$this->SetFont('Arial', 'B', 10);
			$this->SetFillColor(255, 255, 255);
			$this->SetY(22);
			$this->Cell(60, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'C');
			$this->SetFont('Arial', '', 15);
			$this->Cell(190, 5, '', 0, 1, 'C');
			$this->SetFont('Arial', '', 6);
			$this->SetY(30);
			$this->SetX(163);
			$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');
			$this->Line(10, 32, 200, 32);
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}

	$sHt = 'select distinct * from ' . $dbname . '.pta_ht where notransaksi=\'' . $notransaksi . '\'';

	#exit(mysql_error($conn));
	($qHt = mysql_query($sHt)) || true;
	$rHt = mysql_fetch_assoc($qHt);
	$sHk = 'select distinct kodekegiatan,noakun,sum(jumlah) as jumlah,satuanj,alokasibiaya,sum(rupiah) as rupiah   from ' . $dbname . '.pta_dt ' . "\r\n" . '      where notransaksi=\'' . $notransaksi . '\' and jenispta=\'HK\' group by kodekegiatan order by notransaksi asc';

	#exit(mysql_error($conn));
	($qHk = mysql_query($sHk)) || true;

	while ($rHk = mysql_fetch_assoc($qHk)) {
		if ($rHk['noakun'] != '') {
			$hkNoakun[$rHk['noakun']] = $rHk['noakun'];
			$hkJumlah[$notransaksi][$rHk['noakun']] = $rHk['rupiah'];
			$hkKegiatan[$notransaksi][$rHk['noakun']] = $rHk['kodekegiatan'];
			$hkSatuan[$notransaksi][$rHk['noakun']] = $rHk['satuanj'];
			$hkAlokasi[$notransaksi][$rHk['noakun']] = $rHk['alokasibiaya'];
		}
	}

	$sHk = 'select distinct kodebarang,noakun,sum(jumlah) as jumlah,satuanj,alokasibiaya,sum(rupiah) as rupiah   from ' . $dbname . '.pta_dt ' . "\r\n" . '      where notransaksi=\'' . $notransaksi . '\' and jenispta=\'MATERIAL\' group by kodekegiatan order by notransaksi asc';

	#exit(mysql_error($conn));
	($qHk = mysql_query($sHk)) || true;

	while ($rHk = mysql_fetch_assoc($qHk)) {
		if ($rHk['noakun'] != '') {
			$matNoakun[$rHk['noakun']] = $rHk['noakun'];
			$matJumlahBrg[$notransaksi][$rHk['noakun']] = $rHk['jumlah'];
			$matJumlah[$notransaksi][$rHk['noakun']] = $rHk['rupiah'];
			$matKegiatan[$notransaksi][$rHk['noakun']] = $rHk['kodebarang'];
			$matSatuan[$notransaksi][$rHk['noakun']] = $rHk['satuanj'];
			$matAlokasi[$notransaksi][$rHk['noakun']] = $rHk['alokasibiaya'];
		}
	}

	$sHk = 'select distinct kodevhc,noakun,sum(jumlah) as jumlah,satuanj,alokasibiaya,sum(rupiah) as rupiah   from ' . $dbname . '.pta_dt ' . "\r\n" . '      where notransaksi=\'' . $notransaksi . '\' and jenispta=\'HM\' group by kodekegiatan order by notransaksi asc';

	#exit(mysql_error($conn));
	($qHk = mysql_query($sHk)) || true;

	while ($rHk = mysql_fetch_assoc($qHk)) {
		if ($rHk['noakun'] != '') {
			$hmNoakun[$rHk['noakun']] = $rHk['noakun'];
			$hmJumlah[$notransaksi][$rHk['noakun']] = $rHk['rupiah'];
			$hmKegiatan[$notransaksi][$rHk['noakun']] = $rHk['kodevhc'];
			$hmSatuan[$notransaksi][$rHk['noakun']] = $rHk['satuanj'];
			$hmAlokasi[$notransaksi][$rHk['noakun']] = $rHk['alokasibiaya'];
		}
	}

	$sHk = 'select distinct kodekegiatan,noakun,sum(jumlah) as jumlah,satuanj,alokasibiaya,sum(rupiah) as rupiah   from ' . $dbname . '.pta_dt ' . "\r\n" . '      where notransaksi=\'' . $notransaksi . '\' and jenispta=\'UMUM\'  group by kodekegiatan order by notransaksi asc';

	#exit(mysql_error($conn));
	($qHk = mysql_query($sHk)) || true;

	while ($rHk = mysql_fetch_assoc($qHk)) {
		if ($rHk['noakun'] != '') {
			$genNoakun[$rHk['noakun']] = $rHk['noakun'];
			$genJumlah[$notransaksi][$rHk['noakun']] = $rHk['rupiah'];
			$genKegiatan[$notransaksi][$rHk['noakun']] = $rHk['kodekegiatan'];
			$genSatuan[$notransaksi][$rHk['noakun']] = $rHk['satuanj'];
			$genAlokasi[$notransaksi][$rHk['noakun']] = $rHk['alokasibiaya'];
		}
	}

	$cekGen = count($genNoakun);
	$cekHm = count($hmNoakun);
	$cekMat = count($matNoakun);
	$cekHK = count($hkNoakun);
	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->AddPage();
	$pdf->SetY(40);
	$pdf->SetX(20);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Cell(175, 5, strtoupper('PERMINTAAN TAMBAHAN ANGGARAN'), 0, 1, 'C');
	$pdf->Cell(170, 5, 'No.: ' . $notransaksi, 0, 1, 'C');
	$pdf->SetFont('Arial', '', 7);
	$kosong = 0;
	$pdf->Cell(170, 5, $_SESSION['lang']['tanggal'], 0, 0, 'R');
	$pdf->Cell(15, 5, ' : ' . tanggalnormal($rHt['tanggal']), 0, 1, 'R');
	$pdf->Cell(30, 5, $_SESSION['lang']['penjelasan'] . ' :', 0, 1, 'L');
	$pdf->MultiCell(190, 5, $rHt['penjelasan'], TB, 'J', false);
	$pdf->ln(5);
	$pdf->Cell(20, 5, 'HK', 0, 1, 'L');
	$pdf->Cell(20, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
	$pdf->Cell(80, 5, $_SESSION['lang']['kegiatan'], 1, 0, 'C');
	$pdf->Cell(15, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');
	$pdf->Cell(25, 5, $_SESSION['lang']['alokasi'], 1, 0, 'C');
	$pdf->Cell(30, 5, $_SESSION['lang']['jumlah'] . ' (Rp.)', 1, 1, 'C');

	if ($cekHK != 0) {
		foreach ($hkNoakun as $lstNoakun) {
			$pdf->Cell(20, 5, $lstNoakun, 1, 0, 'C');
			$pdf->Cell(80, 5, $optKegiatan[$hkKegiatan[$notransaksi][$lstNoakun]], 1, 0, 'L');
			$pdf->Cell(15, 5, $hkSatuan[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(25, 5, $hkAlokasi[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(30, 5, number_format($hkJumlah[$notransaksi][$lstNoakun], 2), 1, 1, 'R');
			$hkJmlh += $hkJumlah[$notransaksi][$lstNoakun];
		}

		$pdf->Cell(140, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
		$pdf->Cell(30, 5, number_format($hkJmlh, 2), 1, 1, 'R');
	}
	else {
		$pdf->Cell(170, 5, '', 1, 1, 'C');
	}

	$pdf->ln(5);
	$pdf->Cell(20, 5, 'MATERIAL', 0, 1, 'L');
	$pdf->Cell(20, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
	$pdf->Cell(80, 5, $_SESSION['lang']['kodevhc'], 1, 0, 'C');
	$pdf->Cell(18, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
	$pdf->Cell(15, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');
	$pdf->Cell(25, 5, $_SESSION['lang']['alokasi'], 1, 0, 'C');
	$pdf->Cell(30, 5, $_SESSION['lang']['jumlah'] . ' (Rp.)', 1, 1, 'C');

	if ($cekMat != 0) {
		foreach ($matNoakun as $lstNoakun) {
			$pdf->Cell(20, 5, $lstNoakun, 1, 0, 'C');
			$pdf->Cell(80, 5, $optBarang[$matKegiatan[$notransaksi][$lstNoakun]], 1, 0, 'L');
			$pdf->Cell(18, 5, $matJumlahBrg[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(15, 5, $matSatuan[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(25, 5, $matAlokasi[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(30, 5, number_format($matJumlah[$notransaksi][$lstNoakun], 2), 1, 1, 'R');
			$matJmlh += $matJumlah[$notransaksi][$lstNoakun];
		}

		$pdf->Cell(158, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
		$pdf->Cell(30, 5, number_format($matJmlh, 2), 1, 1, 'R');
	}
	else {
		$pdf->Cell(170, 5, '', 1, 1, 'C');
	}

	$pdf->ln(5);
	$pdf->Cell(20, 5, 'HM', 0, 1, 'L');
	$pdf->Cell(20, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
	$pdf->Cell(80, 5, $_SESSION['lang']['kodevhc'], 1, 0, 'C');
	$pdf->Cell(15, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');
	$pdf->Cell(25, 5, $_SESSION['lang']['alokasi'], 1, 0, 'C');
	$pdf->Cell(30, 5, $_SESSION['lang']['jumlah'] . ' (Rp.)', 1, 1, 'C');

	if ($cekHm != 0) {
		foreach ($hmNoakun as $lstNoakun) {
			$pdf->Cell(20, 5, $lstNoakun, 1, 0, 'C');
			$pdf->Cell(80, 5, $hmKegiatan[$notransaksi][$lstNoakun], 1, 0, 'L');
			$pdf->Cell(15, 5, $hmSatuan[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(25, 5, $hmAlokasi[$notransaksi][$lstNoakun], 1, 0, 'C');
			$pdf->Cell(30, 5, number_format($hmJumlah[$notransaksi][$lstNoakun], 2), 1, 1, 'R');
			$hmJmlh += $hmJumlah[$notransaksi][$lstNoakun];
		}

		$pdf->Cell(140, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
		$pdf->Cell(30, 5, number_format($hmJmlh, 2), 1, 1, 'R');
	}
	else {
		$pdf->Cell(170, 5, '', 1, 1, 'C');
	}

	$pdf->ln(5);
	$pdf->Cell(20, 5, 'UMUM', 0, 1, 'L');
	$pdf->Cell(50, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
	$pdf->Cell(90, 5, $_SESSION['lang']['namaakun'], 1, 0, 'C');
	$pdf->Cell(30, 5, $_SESSION['lang']['jumlah'] . ' (Rp.)', 1, 1, 'C');

	if ($cekGen != 0) {
		foreach ($genNoakun as $lstNoakun) {
			$pdf->Cell(50, 5, $lstNoakun, 1, 0, 'C');
			$pdf->Cell(90, 5, $optNamaAkun[$lstNoakun], 1, 0, 'L');
			$pdf->Cell(30, 5, number_format($genJumlah[$notransaksi][$lstNoakun], 2), 1, 1, 'R');
			$genJmlh += $genJumlah[$notransaksi][$lstNoakun];
		}

		$pdf->Cell(140, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
		$pdf->Cell(30, 5, number_format($genJmlh, 2), 1, 1, 'R');
	}
	else {
		$pdf->Cell(170, 5, '', 1, 1, 'C');
	}

	$grandTotal = $genJmlh + $hmJmlh + $matJmlh + $hkJmlh;
	$pdf->Ln();
	$pdf->Cell(140, 5, $_SESSION['lang']['grnd_total'], 0, 0, 'R');
	$pdf->Cell(30, 5, number_format($grandTotal, 2), 1, 1, 'R');
	$pdf->Ln(5);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['approval_status']), 0, 1, 'L');
	$pdf->Cell(25, 5, strtoupper('Tahapan'), 1, 0, 'C');
	$pdf->Cell(39, 5, strtoupper($_SESSION['lang']['namakaryawan']), 1, 0, 'C');
	$pdf->Cell(42, 5, strtoupper($_SESSION['lang']['status']), 1, 0, 'C');
	$pdf->Cell(65, 5, strtoupper($_SESSION['lang']['catatan']), 1, 1, 'C');
	$pdf->Cell(25, 5, 'Dibuat Oleh', 1, 0, 'L');
	$pdf->Cell(39, 5, $arrNmkary[$rHt['dibuat']], 1, 0, 'L');
	$pdf->Cell(42, 5, '', 1, 0, 'L');
	$pdf->Cell(65, 5, '', 1, 1, 'L');
	$X[1] = 'Diketahui';
	$X[2] = 'Verifikasi';
	$X[3] = 'Disetujui';
	$X[4] = 'Disetujui';
	$pdf->SetFont('Arial', '', 8);
	$ard = 1;

	while ($ard < 5) {
		$pdf->Cell(25, 5, $X[$ard], 1, 0, 'L');
		$pdf->Cell(39, 5, $arrNmkary[$rHt['persetujuan' . $ard]], 1, 0, 'L');
		$pdf->Cell(42, 5, $arrKeputusan[$rHt['status' . $ard]], 1, 0, 'L');
		$pdf->Cell(65, 5, $rHt['catatan' . $ard], 1, 1, 'L');
		++$ard;
	}

	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Output();
	break;

case 'getExcel':
	break;
}

?>
