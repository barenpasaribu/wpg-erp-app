<?php


echo '<link rel=stylesheet type=text/css href=\'style/generic.css\'>' . "\r\n";
require_once 'config/connection.php';
$what = $_GET['what'];
$noakuntanam = '1260505';
$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());
$updatetime = date('d M Y H:i:s', time());
$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt - 86400);
$str = 'SELECT kodeorganisasi, namaorganisasi FROM ' . $dbname . '.organisasi' . "\r\n" . '    WHERE tipe = \'KEBUN\'';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$kamuskodeorg[$bar->kodeorganisasi] = $bar->namaorganisasi;
}

$str = 'SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ' . $dbname . '.kebun_prestasi a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.kebun_aktifitas b on a.notransaksi = b.notransaksi' . "\r\n" . '    WHERE substr(b.tanggal,1,10) like \'' . $hariini . '%\' and a.kodekegiatan like \'' . $noakuntanam . '%\' and b.jurnal=1' . "\r\n" . '    GROUP BY b.kodeorg' . "\r\n" . '    ORDER BY b.kodeorg';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['hi'] = $bar->hasilkerja;
	$total += 'hi';
}

$str = 'SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ' . $dbname . '.kebun_prestasi a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.kebun_aktifitas b on a.notransaksi = b.notransaksi' . "\r\n" . '    WHERE substr(tanggal,1,10) like \'' . $kemarin . '%\' and a.kodekegiatan like \'' . $noakuntanam . '%\' and b.jurnal=1' . "\r\n" . '    GROUP BY b.kodeorg' . "\r\n" . '    ORDER BY b.kodeorg';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['maren'] = $bar->hasilkerja;
	$total += 'maren';
}

$str = 'SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ' . $dbname . '.kebun_prestasi a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.kebun_aktifitas b on a.notransaksi = b.notransaksi' . "\r\n" . '    WHERE substr(tanggal,1,10) between \'' . $tahun . '-' . $bulan . '-01\' and \'' . $hariini . '\' and a.kodekegiatan like \'' . $noakuntanam . '%\' and b.jurnal=1' . "\r\n" . '    GROUP BY b.kodeorg' . "\r\n" . '    ORDER BY b.kodeorg';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['bi'] = $bar->hasilkerja;
	$total += 'bi';
}

$str = 'SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ' . $dbname . '.kebun_prestasi a' . "\r\n" . '    LEFT JOIN ' . $dbname . '.kebun_aktifitas b on a.notransaksi = b.notransaksi' . "\r\n" . '    WHERE substr(tanggal,1,10) between \'' . $tahun . '-01-01\' and \'' . $hariini . '\' and \'' . $hariini . '\' and a.kodekegiatan like \'' . $noakuntanam . '%\' and b.jurnal=1' . "\r\n" . '    GROUP BY b.kodeorg' . "\r\n" . '    ORDER BY b.kodeorg';
$res = mysql_query($str);
echo mysql_error($conn);

while ($bar = mysql_fetch_object($res)) {
	$unit[$bar->kodeorg] = $bar->kodeorg;
	$arey[$bar->kodeorg]['sdbi'] = $bar->hasilkerja;
	$total += 'sdbi';
}

echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tr class=rowcontent>' . "\r\n" . '    <td>Tanam ' . $tanggal . ' = ' . number_format($total['hi']) . ' pokok</td>' . "\r\n" . '    <td align=right width=1% nowrap>' . $updatetime . '</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </table>';
echo '<table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center rowspan=2 style=\'width:140px;\'>Unit</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:80x;\'>Hari Ini</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:80x;\'>Kemarin</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:90x;\'>Bulan Ini</td>' . "\r\n" . '        <td align=center colspan=2 style=\'width:100x;\'>sd Bulan Ini</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    <tr class=rowtitle>' . "\r\n" . '        <td align=center style=\'width:40x;\'>Pkk</td>' . "\r\n" . '        <td align=center style=\'width:40x;\'>Ha</td>' . "\r\n" . '        <td align=center style=\'width:40x;\'>Pkk</td>' . "\r\n" . '        <td align=center style=\'width:40x;\'>Ha</td>' . "\r\n" . '        <td align=center style=\'width:45x;\'>Pkk</td>' . "\r\n" . '        <td align=center style=\'width:45x;\'>Ha</td>' . "\r\n" . '        <td align=center style=\'width:50x;\'>Pkk</td>' . "\r\n" . '        <td align=center style=\'width:50x;\'>Ha</td>' . "\r\n" . '    </tr>  ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody></tbody></table>';
echo '<marquee height=100 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>' . "\r\n" . '    <table class=sortable cellspacing=1 border=0 width=480px>' . "\r\n" . '    <tbody>';

if (!empty($unit)) {
	foreach ($unit as $uun) {
		echo '<tr class=rowcontent>';
		echo '<td style=\'width:140px;\'>' . $kamuskodeorg[$uun] . '</td>';
		echo '<td align=right style=\'width:40x;\'>' . number_format($arey[$uun]['hi']) . '</td>';
		@$qwein = $arey[$uun]['hi'] / 143;
		echo '<td align=right style=\'width:40x;\'>' . number_format($qwein, 2) . '</td>';
		echo '<td align=right style=\'width:40x;\'>' . number_format($arey[$uun]['maren']) . '</td>';
		@$qwein = $arey[$uun]['maren'] / 143;
		echo '<td align=right style=\'width:40x;\'>' . number_format($qwein, 2) . '</td>';
		echo '<td align=right style=\'width:45x;\'>' . number_format($arey[$uun]['bi']) . '</td>';
		@$qwein = $arey[$uun]['bi'] / 143;
		echo '<td align=right style=\'width:45x;\'>' . number_format($qwein, 2) . '</td>';
		echo '<td align=right style=\'width:50x;\'>' . number_format($arey[$uun]['sdbi']) . '</td>';
		@$qwein = $arey[$uun]['sdbi'] / 143;
		echo '<td align=right style=\'width:50x;\'>' . number_format($qwein, 2) . '</td>';
		echo '</tr>';
	}
}

echo '<tr class=rowtitle>';
echo '<td>Total</td>';
echo '<td align=right>' . number_format($total['hi']) . '</td>';
@$qwein = $total['hi'] / 143;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
echo '<td align=right>' . number_format($total['maren']) . '</td>';
@$qwein = $total['maren'] / 143;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
echo '<td align=right>' . number_format($total['bi']) . '</td>';
@$qwein = $total['bi'] / 143;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
echo '<td align=right>' . number_format($total['sdbi']) . '</td>';
@$qwein = $total['sdbi'] / 143;
echo '<td align=right>' . number_format($qwein, 2) . '</td>';
echo '</tr>';
echo '</tbody>' . "\r\n" . '    </table>' . "\r\n" . '    * sumber data: BKM yang telah terposting untuk kegiatan tanam' . "\r\n" . '    </marquee>';

?>
