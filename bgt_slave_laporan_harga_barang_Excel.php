<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tab = $_GET['tab'];
$tahunbudget0 = $_GET['tahunbudget0'];
$regional0 = $_GET['regional0'];
$kelompokbarang0 = $_GET['kelompokbarang0'];
$str = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang' . "\r\n" . '    ';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namabarang[$bar->kodebarang] = $bar->namabarang;
	$satuanbarang[$bar->kodebarang] = $bar->satuan;
}

if ($tahunbudget0 == '') {
	echo 'WARNING: silakan mengisi tahunbudget.';
	exit();
}

if ($regional0 == '') {
	echo 'WARNING: silakan mengisi regional.';
	exit();
}

$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '                    order by kode ' . "\r\n" . '                    ';
$artikelompok[''] = $_SESSION['lang']['all'];
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$artikelompok[$bar->kode] = $bar->kelompok;
}

$hkef = '';
$hkef .= '<table><tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $tahunbudget0 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['regional'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $regional0 . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td colspan=2 align=left>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n" . '            <td colspan=4 align=left>: ' . $kelompokbarang0 . ' ' . $artikelompok[$kelompokbarang0] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>';
$hkef .= '<table id=container00 class=sortable cellspacing=1 border=1 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr bgcolor=#DEDEDE>' . "\r\n" . '            <td align=center>' . substr($_SESSION['lang']['nomor'], 0, 2) . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '            <td align=left>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargabudget'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['hargatahunlalu'] . '</td>' . "\r\n" . '       </tr>  ' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>';

if ($tab == '1') {
	$str = 'select * from ' . $dbname . '.bgt_masterbarang' . "\r\n" . '        where closed = 1 and tahunbudget = \'' . $tahunbudget0 . '\' and regional = \'' . $regional0 . '\' and kodebarang like \'' . $kelompokbarang0 . '%\'';
}

if ($tab == '2') {
	$str = 'select a.* from ' . $dbname . '.bgt_masterbarang a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '        where a.closed = 1 and a.tahunbudget = \'' . $tahunbudget0 . '\' and a.regional = \'' . $regional0 . '\' and b.namabarang like \'%' . $kelompokbarang0 . '%\'';
}

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
$hkef .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'bgt_hargabarang' . $tahunbudget0 . ' ' . $regional0 . ' ' . $kelompokbarang0 . ' ' . $qwe;

if (0 < strlen($hkef)) {
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $hkef);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
}

?>
