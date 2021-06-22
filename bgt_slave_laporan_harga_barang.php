<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$cekapa = $_POST['cekapa'];
$str = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang' . "\r\n" . '    ';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namabarang[$bar->kodebarang] = $bar->namabarang;
	$satuanbarang[$bar->kodebarang] = $bar->satuan;
}

$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '                    order by kode ' . "\r\n" . '                    ';
$artikelompok[''] = $_SESSION['lang']['all'];
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$artikelompok[$bar->kode] = $bar->kelompok;
}

if ($cekapa == 'tab0') {
	$tahunbudget0 = $_POST['tahunbudget0'];
	$regional0 = $_POST['regional0'];
	$kelompokbarang0 = $_POST['kelompokbarang0'];
	$hkef = '';
	$hkef .= '<span id=printPanel>' . "\r\n" . '     <img onclick=hargabarangKeExcel(event,\'bgt_slave_laporan_harga_barang_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '     <img onclick=hargabarangKePDF(event,\'bgt_slave_laporan_harga_barang_PDF.php\') src=images/pdf.jpg class=resicon title=\'PDF\'> ' . "\r\n\t" . ' </span>';
	$hkef .= '<table><tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $tahunbudget0 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['regional'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $regional0 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $kelompokbarang0 . ' ' . $artikelompok[$kelompokbarang0] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>';
	$hkef .= '<table id=container00 class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargabudget'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargatahunlalu'] . '</td>' . "\r\n" . '       </tr>  ' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>';
	$str = 'select * from ' . $dbname . '.bgt_masterbarang' . "\r\n" . '        where closed = 1 and tahunbudget = \'' . $tahunbudget0 . '\' and regional = \'' . $regional0 . '\' and kodebarang like \'' . $kelompokbarang0 . '%\'';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$hkef .= '<tr class=rowcontent>' . "\r\n" . '            <td align=center>' . $no . '</td>' . "\r\n" . '            <td align=center>' . $bar->kodebarang . '</td>' . "\r\n" . '            <td align=left>' . $namabarang[$bar->kodebarang] . '</td>' . "\r\n" . '            <td align=left>' . $satuanbarang[$bar->kodebarang] . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->hargasatuan, 2) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->hargalalu, 2) . '</td>' . "\r\n" . '       </tr>';
	}

	if ($no == 0) {
		$hkef .= '<tr>' . "\r\n" . '            <td colspan= 6 align=center>Data tidak ada atau belum ditutup.</td>' . "\r\n" . '       </tr>';
	}

	$hkef .= '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\t\t" . ' ' . "\r\n" . '     </table>';
	echo $hkef;
}

if ($cekapa == 'tab1') {
	$tahunbudget1 = $_POST['tahunbudget1'];
	$regional1 = $_POST['regional1'];
	$namabarang1 = $_POST['namabarang1'];
	$hkef = '';
	$hkef .= '<span id=printPanel>' . "\r\n" . '     <img onclick=hargabarangKeExcel2(event,\'bgt_slave_laporan_harga_barang_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '     <img onclick=hargabarangKePDF2(event,\'bgt_slave_laporan_harga_barang_PDF.php\') src=images/pdf.jpg class=resicon title=\'PDF\'> ' . "\r\n\t" . ' </span>';
	$hkef .= '<table><tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['regional'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $regional1 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $tahunbudget1 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['caribarang'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $namabarang1 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>';
	$hkef .= '<table id=container00 class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargabudget'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargatahunlalu'] . '</td>' . "\r\n" . '       </tr>  ' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>';
	$str = 'select a.* from ' . $dbname . '.bgt_masterbarang a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '        where a.tahunbudget = \'' . $tahunbudget1 . '\' and a.regional = \'' . $regional1 . '\' and b.namabarang like \'%' . $namabarang1 . '%\'';
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$hkef .= '<tr class=rowcontent>' . "\r\n" . '            <td align=center>' . $no . '</td>' . "\r\n" . '            <td align=center>' . $bar->kodebarang . '</td>' . "\r\n" . '            <td align=left>' . $namabarang[$bar->kodebarang] . '</td>' . "\r\n" . '            <td align=left>' . $satuanbarang[$bar->kodebarang] . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->hargasatuan, 2) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->hargalalu, 2) . '</td>' . "\r\n" . '       </tr>';
	}

	if ($no == 0) {
		$hkef .= '<tr>' . "\r\n" . '            <td colspan= 6 align=center>Data tidak ada atau belum ditutup.</td>' . "\r\n" . '       </tr>';
	}

	$hkef .= '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\t\t" . ' ' . "\r\n" . '     </table>';
	echo $hkef;
}

?>
