<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
$proses = $_GET['proses'];
$_POST['unit'] == '' ? $unit = $_GET['unit'] : $unit = $_POST['unit'];
$_POST['tglAkhir'] == '' ? $tglAkhir = $_GET['tglAkhir'] : $tglAkhir = $_POST['tglAkhir'];
$_POST['tglAwal'] == '' ? $tglAwal = $_GET['tglAwal'] : $tglAwal = $_POST['tglAwal'];
$whr = 'kodekelompok=\'K001\'';
//$whr1 = 'tipe=\'BLOK\' OR tipe=\'STENGINE\'';
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNamkont = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', $whr);
$stream = "\r\n" . '       <table class=sortable cellspacing=1 border=0>' . "\r\n" . '             <thead>' . "\r\n" . '                    <tr class=rowheader>' . "\r\n" . '                       <td>No.</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['nospk'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['blok'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td><td>Nama Blok</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['kontraktor'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['kegiatan'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['namakegiatan'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td>                       ' . "\r\n" . '                       <td>' . $_SESSION['lang']['jhk'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['hasilkerjad'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['jumlah'] . ' on ' . $_SESSION['lang']['kontrak'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['kegiatan'] . ' on ' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['hasilkerjad'] . ' on ' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['jhk'] . ' on ' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['jumlah'] . ' on ' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['blok'] . ' on ' . $_SESSION['lang']['realisasi'] . '</td><td>Nama Blok</td>' . "\r\n" . '                       <!--td>Material</td>' . "\r\n" . '                       <td>Jumlah</td-->' . "\r\n\r\n" . '                     </tr>  ' . "\r\n" . '                 </thead>' . "\r\n" . '                 <tbody>';
$unit2='';
$unit == '' ? $unit2 = '' : $unit2= " a.kodeblok like '" . $unit . "%' and ";
$str = "SELECT a.notransaksi, a.kodeblok as blokspk ,a.kodekegiatan as kegspk, a.hk as hkspk, ".
        "a.hasilkerjajumlah as hasilspk, a.satuan, a.jumlahrp as rpspk,b.kodekegiatan as kegrealisasi, ".
        "b.tanggal, b.hasilkerjarealisasi as hasilrealisasi, b.hkrealisasi, b.jumlahrealisasi as rprealisasi, ".
        "b.kodeblok as blokrealisasi,c.namakegiatan ".
        "FROM $dbname.log_spkdt a ".
        "left join $dbname.log_baspk b on a.notransaksi=b.notransaksi and a.kodekegiatan=b.kodekegiatan ".
        "left join $dbname.setup_kegiatan c on a.kodekegiatan=c.kodekegiatan ".
        "where $unit2 ".
        "tanggal between '" . tanggalsystem($tglAwal) . "' and '" . tanggalsystem($tglAkhir) ."' ".
         "order by a.notransaksi,a.kodeblok,b.tanggal";
$res = mysql_query($str);
$no = 0;
$oldspk = '';
$kgr = 0;
$hsr = 0;
$hkr = 0;
$rpr = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$kgr += $bar->kegrealisasi;
	$hsr += $bar->hasilrealisasi;
	$hkr += $bar->hkrealisasi;
	$rpr += $bar->rprealisasi;
	$sKontak = 'select distinct koderekanan from ' . $dbname . '.log_spkht where notransaksi=\'' . $bar->notransaksi . '\'';

	#exit(mysql_error($conn));
	($qKontrak = mysql_query($sKontak)) || true;
	$rKontrak = mysql_fetch_assoc($qKontrak);
	$newspk = $bar->notransaksi . $bar->blokspk . $bar->kegspk;

	if ($oldspk == $newspk) {
		$stream .= '<tr class=rowcontent>' . "\r\n" . '                       <td>' . $no . '</td>' . "\r\n" . '                       <td>' . $bar->notransaksi . '</td>' . "\r\n" . '                       <td>' . $bar->blokspk . '</td><td>'.$optNm[$bar->blokspk].'</td><td>' . $optNamkont[$rKontrak['koderekanan']] . '</td>' . "\r\n" . '                       <td>' . $bar->kegspk . '</td>' . "\r\n" . '                       <td>-</td>' . "\r\n" . '                       <td>-</td>' . "\r\n" . '                       <td>-</td>' . "\r\n" . '                       <td>-</td>' . "\r\n" . '                       <td>-</td>' . "\r\n" . '                       <td align=right>' . $bar->kegrealisasi . '</td>' . "\r\n" . '                       <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->hasilrealisasi, 2) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->hkrealisasi, 2) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->rprealisasi, 2) . '</td>' . "\r\n" . '                       <td>' . $bar->blokrealisasi . '</td><td>'.$optNm[$bar->blokrealisasi].'</td><!--td>' . $bar->kodebarang . '</td>' . "\r\n" . '                       <td>' . $bar->jumlah . '</td-->' . "\r\n" . '                     </tr>';
	}
	else {
		$stream .= '<tr class=rowcontent>' . "\r\n" . '                       <td>' . $no . '</td>' . "\r\n" . '                       <td>' . $bar->notransaksi . '</td>' . "\r\n" . '                       <td>' . $bar->blokspk . '</td><td>'.$optNm[$bar->blokspk].'</td><td>' . $optNamkont[$rKontrak['koderekanan']] . '</td>' . "\r\n" . '                       <td>' . $bar->kegspk . '</td>' . "\r\n" . '                       <td>' . $bar->namakegiatan . '</td>                           ' . "\r\n" . '                       <td align=right>' . number_format($bar->hkspk, 2) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->hasilspk, 2) . '</td>' . "\r\n" . '                       <td>' . $bar->satuan . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->rpspk, 2) . '</td>' . "\r\n" . '                       <td align=right>' . $bar->kegrealisasi . '</td>' . "\r\n" . '                       <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->hasilrealisasi, 2) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->hkrealisasi, 2) . '</td>' . "\r\n" . '                       <td align=right>' . number_format($bar->rprealisasi, 2) . '</td>' . "\r\n" . '                       <td>' . $bar->blokrealisasi . '</td><td>'.$optNm[$bar->blokrealisasi].'</td> <!--td>' . $bar->kodebarang . '</td>' . "\r\n" . '                       <td>' . $bar->jumlah . '</td-->' . "\r\n" . '                     </tr>';
	}

	$oldspk = $newspk;
}

$stream .= '</tbody>' . "\r\n" . '                 <tfoot>' . "\r\n" . '                 </tfoot>' . "\t\t" . ' ' . "\r\n" . '           </table>';

switch ($proses) {
case 'html':
	echo $stream;
	break;

case 'excel':
	$nop_ = 'RealisasiSPK_' . $unit . '_' . tanggalsystem($tglAwal) . '_' . tanggalsystem($tglAkhir);
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;
}

?>
