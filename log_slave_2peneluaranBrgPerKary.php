<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$tgl1 = $_POST['tgl1'];
$tgl2 = $_POST['tgl2'];
$karyawanid = $_POST['karyawanid'];
$kodebarang = $_POST['kodebarang'];

if ($proses == 'excel') {
	$kdorg = $_GET['kdorg'];
	$tgl1 = $_GET['tgl1'];
	$tgl2 = $_GET['tgl2'];
	$karyawanid = $_GET['karyawanid'];
	$kodebarang = $_GET['kodebarang'];
}

if (($kdorg == '') || ($tgl1 == '') || ($tgl2 == '')) {
	echo 'Error: Field ' . $_SESSION['lang']['kodeorg'] . '/' . $_SESSION['lang']['tanggal'] . ' can\'t Empty';
	exit();
}

$tgl1 = tanggaldgnbar($tgl1);
$tgl2 = tanggaldgnbar($tgl2);
$tglPP = explode('-', $tgl1);
$date1 = $tglPP[2];
$month1 = $tglPP[1];
$year1 = $tglPP[0];
$pecah2 = explode('-', $tgl2);
$date2 = $pecah2[2];
$month2 = $pecah2[1];
$year2 = $pecah2[0];
$jd1 = GregorianToJD($month1, $date1, $year1);
$jd2 = GregorianToJD($month2, $date2, $year2);
$jmlHari = $jd2 - $jd1;

if (30 < $jmlHari) {
	exit('error: Please insert range of date in 30 days');
}

$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmNik = makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $wher);
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$saBbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$nmGudang = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nmKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', 'kelompok="KNT"');
$border = 'border=\'0\'';

if ($proses == 'excel') {
	$bgcolor = 'bgcolor=#CCCCCC';
	$border = 'border=\'1\'';
}

$stream = "\r\n\t\t\t\t" . '<table>' . "\r\n\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['unitkerja'] . '</td>' . "\r\n\t\t\t\t\t\t" . '<td>' . $kdorg . ' / ' . $nmGudang[$kdorg] . '</td>' . "\r\n\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t\t" . '<tr>' . "\r\n\t\t\t\t\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t\t\t" . '<td>' . tanggalnormal($tgl1) . ' ' . $_SESSION['lang']['sampai'] . ' ' . tanggalnormal($tgl2) . '</td>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t" . '</tr>' . "\r\n\t\t\t\t" . '</table>';
$stream .= '<table cellspacing=\'1\' class=\'sortable\' ' . $border . '>' . "\r\n" . '               ' . "\t\t\t" . '<thead class=rowheader>' . "\r\n\t\t\t\t\t\t" . '  <tr  ' . $bgcolor . '>' . "\r\n\t\t\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['nik'] . '</td>    ' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\t\r\n\t\t\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td align=center>Dari ' . $_SESSION['lang']['gudang'] . '</td>' . "\r\n\t\t\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                                                        <td align=center>' . $_SESSION['lang']['namakegiatan'] . '</td>' . "\r\n\t\t\t\t\t\t" . '  </tr>' . "\r\n\t\t\t\t\t\t" . '</thead>' . "\r\n" . '              ' . "\t\t" . ' <tbody>';

if ($karyawanid != '') {
	$wher .= ' and namapenerima=\'' . $karyawanid . '\'';
}

$i =  "select notransaksi,tanggal,kodegudang,jumlah,hartot,keterangan,kodebarang,kodekegiatan,satuan,namapenerima ".
"FROM $dbname.log_transaksi_vw ".
"where untukunit='" . $kdorg . "' ".
//"and kodekegiatan in ('114020001','127030101','127040201','127050101','711010401','712070001','712040001') ".
"and tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' " . $wher .
	" and tipetransaksi='5' ".//and namapenerima!='MASYARAKAT'
	"group by kodebarang,kodekegiatan,tanggal order by  kodebarang";
saveLog($i);
$n = mysql_query($i);

while ($d = mysql_fetch_assoc($n)) {
	$totbaris = $d['jumlah'] * $d['total'];
	$no += 1;
	if (($kdBrgDet != $d['kodebarang']) || ($d['namapenerima'] != $trima)) {
		$sRow = 'select * from ' . $dbname . '.log_transaksi_vw where untukunit=\'' . $kdorg . '\' and  tanggal between \'' . $tgl1 . '\' and \'' . $tgl2 . '\' ' . $wher . ' and tipetransaksi=\'5\' and kodebarang=\'' . $d['kodebarang'] . '\' and namapenerima=\'' . $d['namapenerima'] . '\'';

		#exit(mysql_error($conn));
		($qRow = mysql_query($sRow)) || true;
		$rRow = mysql_num_rows($qRow);
		$kdBrgDet = $d['kodebarang'];
		$trima = $d['namapenerima'];
		$brsdt = $rRow;
		$totJmlh = 0;
	}

	$stream .= '<tr class=rowcontent>' . "\r\n\t\t" . '<td align=center>' . $no . '</td>' . "\r\n" . '                <td align=left>' . $nmNik[$d['namapenerima']] . '</td>' . "\r\n" . '                <td align=left>' . $nmKar[trim($d['namapenerima'])] . '</td>' . "\r\n\t\t" . '<td align=center>' . $d['tanggal'] . '</td>' . "\r\n\t\t" . '<td align=left>' . $nmGudang[$d['kodegudang']] . '</td>' . "\r\n\t\t" . '<td align=left>' . $d['kodebarang'] . '</td>' . "\r\n" . '                <td align=left>' . $nmBarang[$d['kodebarang']] . '</td>' . "\r\n" . '                <td align=right>' . number_format($d['jumlah']) . '</td>' . "\r\n" . '                <td align=left>' . $d['satuan'] . '</td>';
	$stream .= "\r\n\t\t" . '<td align=left style=\'cursor:pointer\' onclick=previewBast(\'' . $d['notransaksi'] . '\',event);>' . $d['notransaksi'] . '</td>' . "\r\n" . '                <td align=left>' . $d['keterangan'] . '</td>' . "\r\n" . '                <td align=left>' . $nmKegiatan[$d['kodekegiatan']] . '</td>' . "\r\n\t" . '</tr>';
	$brsdt -= 1;
	$totJmlh += $d['jumlah'];
	$totPergdng += $d['kodegudang'] . $d['kodebarang'];
	$lstGdng[$d['kodegudang']] = $d['kodegudang'];
	$lstBrg[$d['kodebarang']] = $d['kodebarang'];

	if ($brsdt == 0) {
		$stream .= '<tr class=rowcontent>';
		$stream .= '<td colspan=7>' . $_SESSION['lang']['subtotal'] . ' ' . $nmBarang[$d['kodebarang']] . '-' . $nmNik[$d['namapenerima']] . '-' . $nmKar[$d['namapenerima']] . '</td>';
		$stream .= '<td align=right>' . number_format($totJmlh, 0) . '</td>';
		$stream .= '<td colspan=5></td></tr>';
	}
}

$stream .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $stream;
	break;

case 'excel':
	$stream .= 'Print Time : ' . date('H:i:s, d/m/Y') . '<br>By : ' . $_SESSION['empl']['name'];
	$tglSkrg = date('Ymd');
	$nop_ = 'Laporan_penggunaan_barang_perkaryawan' . $tglSkrg;

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
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t\t" . '</script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t\t" . '</script>';
		}

		closedir($handle);
	}

	break;
}

?>
