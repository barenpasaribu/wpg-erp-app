<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$thnbudget = $_POST['thnbudget'];

if ($thnbudget == '') {
	$thnbudget = $_GET['thnbudget'];
}

$kegiatan = $_POST['kegiatan'];

if ($kegiatan == '') {
	$kegiatan = $_GET['kegiatan'];
}

$kodeorg = $_POST['kodeorg'];

if ($kodeorg == '') {
	$kodeorg = $_GET['kodeorg'];
}

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$str = 'select a.noakun,a.kegiatan, a.kodebudget, sum(a.jumlah) as jumlah, a.satuanj, sum(a.rupiah) as rupiah,' . "\r\n" . '      sum(a.volume) as volume,a.satuanv,b.namaakun,c.nama from ' . $dbname . '.bgt_budget a' . "\r\n" . '      left join  ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      left join  ' . $dbname . '.bgt_kode c on a.kodebudget=c.kodebudget' . "\r\n" . '      where a.tahunbudget=' . $thnbudget . ' and a.kegiatan=\'' . $kegiatan . '\' ' . "\r\n" . '      and a.kodeorg like \'' . $kodeorg . '%\' and a.tipebudget=\'ESTATE\'' . "\r\n" . '      group by a.noakun,a.kegiatan,a.kodebudget';
$res = mysql_query($str);
$stream = '<table class=sortable border=0 cellspacing=1>' . "\r\n" . '     <thead>' . "\r\n" . '       <tr class=rowheader>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['no'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['kegiatan'] . '</td> ' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['kodebudget'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['volume'] . '</td> ' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['satuan'] . '</td>    ' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '          <td align=center>' . $_SESSION['lang']['rp'] . '</td>    ' . "\r\n" . '       </tr>' . "\r\n" . '     </thead><tbody>';

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$stream .= '<tr class=rowcontent>' . "\r\n" . '          <td>' . $no . '</td>' . "\r\n" . '          <td>' . $bar->noakun . '</td>' . "\r\n" . '          <td>' . $bar->namaakun . '</td>' . "\r\n" . '          <td>' . $bar->nama . '</td> ' . "\r\n" . '          <td>' . $bar->kodebudget . '</td>' . "\r\n" . '          <td align=right>' . number_format($bar->volume, 2, '.', ',') . '</td>' . "\r\n" . '          <td>' . $bar->satuanv . '</td>              ' . "\r\n" . '          <td align=right>' . number_format($bar->jumlah, 2, '.', ',') . '</td>' . "\r\n" . '          <td>' . $bar->satuanj . '</td>' . "\r\n" . '          <td align=right>' . number_format($bar->rupiah, 2, '.', ',') . '</td>    ' . "\r\n" . '       </tr>';
	$ttlv = $bar->volume;
	$satv = $bar->satuanv;
	$ttl += $bar->rupiah;
	$satj = $bar->satuanj;
}

$stream .= '<tr class=rowcontent>' . "\r\n" . '          <td colspan=9>' . $_SESSION['lang']['total'] . '</td>                   ' . "\r\n" . '          <td align=right>' . number_format($ttl, 2, '.', ',') . '</td>  ' . "\r\n" . '       </tr>';
$stream .= '<tbody><tfoot></tfoot></table>' . "\r\n" . '          Kolom Volume hanya akan terpakai pada pengangkutan External dan internal' . "\r\n" . '           ';

switch ($proses) {
case 'preview':
	echo '<img onclick="printExcel(\'excel\',\'bgt_slave_freeQuery.php\',\'PRINT\',event)" src="images/excel.jpg" class=resicon title=\'MS.Excel\'> ';
	echo $stream;
	break;

case 'excel':
	$stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$qwe = date('YmdHms');
	$nop_ = 'Budget_' . $kodeorg . '_Pertelaan_' . $thnbudget . '_' . $qwe;

	if (0 < strlen($stream)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}

	break;
}

?>
