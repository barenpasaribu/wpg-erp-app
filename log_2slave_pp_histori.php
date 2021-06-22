<?php


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
$arrTanggal = makeOption($dbname, 'log_transaksiht', 'nopo,tanggal', $where2);
$arrTanggal = makeOption($dbname, 'log_transaksiht', 'nopo,notransaksi', $where2);
$_POST['klmpkBrg'] == '' ? $klmpkBrg = $_GET['klmpkBrg'] : $klmpkBrg = $_POST['klmpkBrg'];
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['lokasi'] == '' ? $lokasi = $_GET['lokasi'] : $lokasi = $_POST['lokasi'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['statId'] == '' ? $statId = $_GET['statId'] : $statId = $_POST['statId'];
$_POST['purId'] == '' ? $purId = $_GET['purId'] : $purId = $_POST['purId'];
$data = array();
$unitId = $_SESSION['lang']['all'];
$nmPrshn = 'Holding';
$purchaser = $_SESSION['lang']['all'];
extract($_POST);
extract($_GET);

if ($tglDr != '' && $tanggalSampai!='') {
	$where = ' a.tanggal between \'' . tanggalsystemw($tglDr) . '\' AND \'' . tanggalsystemw($tanggalSampai) . '\'';
	$whereb = ' and b.tanggal between \'' . tanggalsystemw($tglDr) . '\' AND \'' . tanggalsystemw($tanggalSampai) . '\'';
}
else {
	exit('Error: Tanggal tidak boleh kosong' );
}

if ($statId == '2') {
	exit('Error: ' . $_SESSION['lang']['status'] . ' tidak boleh kosong');
}

if ($kdUnit != '') {
	$where .= ' and a.kodeorg=\'' . $kdUnit . '\'';
	$whereb .= ' and b.kodeorg=\'' . $kdUnit . '\'';
	$unitId = $optNmOrg[$kdUnit];
}

if ($klmpkBrg != '') {
	$where .= ' and substr(a.kodebarang,1,3)=\'' . $klmpkBrg . '\'';
	$whereb .= ' and substr(a.kodebarang,1,3)=\'' . $klmpkBrg . '\'';
}

if ($lokasi != '') {
	$where .= ' and a.lokalpusat=\'' . $lokasi . '\'';
	$whereb .= ' and a.lokalpusat=\'' . $lokasi . '\'';
}

if ($purId != '') {
	$where .= ' and c.purchaser=\'' . $purId . '\'';
	$whereb .= ' and a.purchaser=\'' . $purId . '\'';
	$purchaser = $optNmOrang[$purId];
}

$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab .= "\r\n" . '    <table>' . "\r\n" . '    <tr><td colspan=17 align=left><b><font size=5>' . $_SESSION['lang']['ppLap'] . '</font></b></td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['pt'] . ' : ' . $unitId . '</td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['periode'] . ' : ' . $periode . '</td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['kelompokbarang'] . ' : ' . $rKelompok[$klmpkBrg] . '</td></tr>' . "\r\n" . '    <tr><td colspan=17 align=left>' . $_SESSION['lang']['purchaser'] . ' : ' . $purchaser . '</td></tr>' . "\r\n" . '    </table>';
}

if ($statId == '1') {
	$sListData = 'select distinct a.nopp,namabarang,a.kodebarang,satuan,a.hargasatuan,namasupplier,b.tanggal as tglpp,a.nopo,c.tgledit,a.tanggal,a.statuspo,c.tanggalkirim,' . "\r\n" . '            c.idFranco,c.lokasipengiriman,c.purchaser,e.tglAlokasi ,a.jumlahpesan,a.matauang,c.kurs ' . "\r\n" . '           from ' . $dbname . '.log_po_vw a left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp ' . "\r\n" . '               left join ' . $dbname . '.log_poht c on a.nopo=c.nopo' . "\r\n" . '               left join ' . $dbname . '.log_prapodt e on a.nopp=e.nopp' . "\r\n" . '           where ' . $where . ' group by a.kodebarang,a.nopo,a.nopp order by substr(a.kodebarang,1,3) asc, b.tanggal desc';
}
else {
	$sListData = 'select distinct a.nopp,kodebarang,purchaser,b.tanggal as tglpp,tglAlokasi,a.realisasi from ' . $dbname . '.log_prapodt a ' . "\r\n" . '                left join ' . $dbname . '.log_prapoht b on a.nopp=b.nopp where (create_po=0 or create_po=\'\') ' . $whereb . ' and a.status!=\'3\' ' . "\r\n" . '                and a.purchaser>0' . "\r\n" . '                group by a.kodebarang,nopp  order by kodebarang asc, b.tanggal desc';
}

$tab .= '<table cellspacing=1 border=' . $brdr . ' class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>No.</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PP</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' PO</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['alokasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>O.std</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jumlahrealisasi'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['jmlhPesan'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['kurs'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['totalharga'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '        <td ' . $bgcoloraja . ' rowspan=2>' . $_SESSION['lang']['tanggal'] . ' ' . $_SESSION['lang']['tandatangan'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=6 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=5 align=center>' . $_SESSION['lang']['pengiriman'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
$tab .= '</tr>';
$tab .= '<tr><td ' . $bgcoloraja . '>' . $_SESSION['lang']['tipe'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['syaratPem'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['rencana'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['noinvoice'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggalbayar'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['almt_kirim'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tgl_kirim'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['tglsmp'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['satuan'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['biaya'] . '</td>';
$tab .= '<td ' . $bgcoloraja . '>' . $_SESSION['lang']['bapb'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . '>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td  ' . $bgcoloraja . '>Copy</td>';
$tab .= '<td  ' . $bgcoloraja . '>Original</td>';
$tab .= '</tr></thead>' . "\r\n\t" . '<tbody>';
$str = $sListData;
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
	$sListData = "Select * from ($sListData) x ".
		"where right(nopp,4) in (".
		" SELECT " .
		"o.kodeorganisasi " .
		"FROM  organisasi o    " .
		"WHERE o.induk = '" . $_SESSION['empl']['kodeorganisasi'] . "' )";
} else {
	$sListData = "Select * from ($sListData) x ".
		"where right(nopp,4) in (".
		"SELECT  o.kodeorganisasi " .
		"FROM datakaryawan d " .
		"INNER JOIN user u on u.karyawanid=d.karyawanid " .
		"INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
		"WHERE u.namauser= '" . $_SESSION['standard']['username'] . "')";
}
#exit(mysql_error());
($qListData = mysql_query($sListData)) || true;
$rAdaData = mysql_num_rows($qListData);

if (0 < $rAdaData) {
	while ($rListData = mysql_fetch_assoc($qListData)) {
		$tglTerima = '';
		$tglEdit = '';
		$tanggalByr = '';

		if ($klmpkBarang != substr($rListData['kodebarang'], 0, 3)) {
			$brs = 1;
		}

		if ($brs == 1) {
			$no = 0;
			$klmpkBarang = substr($rListData['kodebarang'], 0, 3);
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td colspan=3><b>' . $klmpkBarang . '</b></td><td colspan=3><b>' . $rKelompok[$klmpkBarang] . '</b></td>';
			$tab .= '<td colspan=27>&nbsp;</td>';
			$tab .= '</tr>';
			$brs = 0;
		}

		$sRealisasi = 'select distinct realisasi from ' . $dbname . '.log_prapodt where nopp=\'' . $rListData['nopp'] . '\' and kodebarang=\'' . $rListData['kodebarang'] . '\'';

		#exit(mysql_error());
		($qRealisai = mysql_query($sRealisasi)) || true;
		$rRealisasi = mysql_fetch_assoc($qRealisai);

		if ($statId == '1') {
			if ($rListData['nopo'] != '') {
				$tanggalData = '';
				$sTagihan = 'select distinct noinvoice,tanggal from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rListData['nopo'] . '\'';

				#exit(mysql_error());
				($qTagihan = mysql_query($sTagihan)) || true;
				$rTagihan = mysql_fetch_assoc($qTagihan);
				$tglTerima = $rTagihan['tglterima'];

				if ($rTagihan['tanggal'] != '') {
					$tanggalData = $rTagihan['tanggal'];
					$tglByr = 'select distinct notransaksi from ' . $dbname . '.keu_kasbankdt ' . "\r\n" . '                                      where keterangan1' . "\t" . '=\'' . $rTagihan['noinvoice'] . '\'';

					#exit(mysql_error($conn));
					($qtglByr = mysql_query($tglByr)) || true;
					$rTglByr = mysql_fetch_assoc($qtglByr);
					$tanggalByr = '00-00-0000';
					$rt = 'select distinct tanggal from ' . $dbname . '.keu_kasbankht where notransaksi=\'' . $rTglByr['notransaksi'] . '\'';

					#exit(mysql_error($conn));
					($qrt = mysql_query($rt)) || true;
					$rrt = mysql_fetch_assoc($qrt);

					if ($rrt['tanggal'] != '') {
						$tanggalByr = $rrt['tanggal'];
					}
				}

				$sTransaksi = 'select distinct tanggal,notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $rListData['nopo'] . '\'';

				#exit(mysql_error());
				($qTransaksi = mysql_query($sTransaksi)) || true;
				$rTransaksi = mysql_fetch_assoc($qTransaksi);
				$tglTerima = tanggalnormal($rTransaksi['tanggal']);
			}
		}

		if ($rListData['idFranco'] != '') {
			$lokasi = $optFranco[$rListData['idFranco']];
			$tglKirim = substr($rListData['tanggalkirim'], 0, 10);
		}
		else {
			$lokasi = $rListData['lokasipengiriman'];
			$tglKirim = substr($rListData['tanggalkirim'], 0, 10);
		}

		if ($rListData['tgledit'] != '') {
			$tglEdit = $rListData['tgledit'];
		}

		if (strlen($tglKirim) < 10) {
			$tglKirim = '';
		}

		if (strlen($tglTerima) < 10) {
			$tglTerima = '';
		}

		$no += 1;
		$hargaBarang = 0;

		if ($rListData['jumlahpesan'] != '') {
			if ($rListData['matauang'] != 'IDR') {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'] * $rListData['kurs'];
			}
			else {
				$hargaBarang = $rListData['jumlahpesan'] * $rListData['hargasatuan'];
			}
		}

		$month1 = substr($rListData['tglAlokasi'], 5, 2);
		$date1 = substr($rListData['tglAlokasi'], 8, 2);
		$year1 = substr($rListData['tglAlokasi'], 0, 4);

		if ($statId == 0) {
			$month2 = date('m');
			$date2 = date('d');
			$year2 = date('Y');
		}
		else if ($statId == 1) {
			$month2 = substr($rListData['tanggal'], 5, 2);
			$date2 = substr($rListData['tanggal'], 8, 2);
			$year2 = substr($rListData['tanggal'], 0, 4);
		}

		$jd1 = GregorianToJD($month1, $date1, $year1);
		$jd2 = GregorianToJD($month2, $date2, $year2);
		$jmlHari = $jd2 - $jd1;
		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $rListData['nopp'] . '</td>';
		$tab .= '<td>' . tanggalnormal($rListData['tglpp']) . '</td>';
		$tab .= '<td>' . $rListData['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmBarang[$rListData['kodebarang']] . '</td>';
		$tab .= '<td>' . $optSatuan[$rListData['kodebarang']] . '</td>';
		$tab .= '<td>' . $rListData['nopo'] . '</td>';
		$tab .= '<td>' . $rListData['tanggal'] . '</td>';
		$tab .= '<td>' . $optNmOrang[$rListData['purchaser']] . '</td>';
		$tab .= '<td>' . tanggalnormal($rListData['tglAlokasi']) . '</td>';
		$tab .= '<td align=right>' . $jmlHari . '</td>';
		$tab .= '<td align=right>' . number_format($rRealisasi['realisasi'], 0) . '</td>';
		$tab .= '<td align=right>' . number_format($rListData['jumlahpesan'], 0) . '</td>';
		$tab .= '<td>' . $rListData['matauang'] . '</td>';
		$tab .= '<td align=right>' . $rListData['kurs'] . '</td>';
		$tab .= '<td align=right>' . number_format($hargaBarang, 0) . '</td>';
		$tab .= '<td>' . $rListData['namasupplier'] . '</td>';
		$tab .= '<td>' . $tglEdit . '</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>' . $rTagihan['noinvoice'] . '</td>';
		$tab .= '<td>' . $tanggalData . '</td>';
		$tab .= '<td>' . $tanggalByr . '</td>';
		$tab .= '<td>' . $lokasi . '</td>';
		$tab .= '<td>' . $tglKirim . '</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>' . $rTransaksi['notransaksi'] . '</td>';
		$tab .= '<td>' . $tglTerima . '</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '<td>&nbsp;</td>';
		$tab .= '</tr>';
	}
}
else {
	$tab .= '<tr class=rowcontent><td colspan=31>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
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
	//echoMessage('Print Time:',getNow());
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . getNow() . '<br>By:' . $_SESSION['empl']['name'];
	$dte = getNow("YmdHis");//date('YmdHms');
	$nop_ = 'permintaanPembeliaan_' . $purId . '_' . $dte;
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
