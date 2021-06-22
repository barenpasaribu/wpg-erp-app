<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$thnbudget = $_POST['thnbudget'];
$tbs = 0;
$cpo = 0;
$pk = 0;
$str = 'select sum(kgolah) as tbs,sum(kgcpo) as cpo,sum(kgkernel) as kernel from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode = \'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$tbs = $bar->tbs;
	$cpo = $bar->cpo;
	$pk = $bar->kernel;
}

$stream = '<fieldset><legend>' . $_SESSION['lang']['produksipabrik'] . ' (' . $_SESSION['lang']['ton'] . ')</legend>' . "\r\n" . '<table class=sortable cellspacing=1 border=0 width=300px>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['tbsdiolah'] . '</td>' . "\r\n" . '           <td align=center>Palm Product</td>    ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['cpo'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['kernel'] . '</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>' . "\r\n" . '         <tr class=rowcontent>' . "\r\n" . '           <td align=right>' . number_format($tbs / 1000, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format(($cpo + $pk) / 1000, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($cpo / 1000, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($pk / 1000, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>     ' . "\r\n" . '     </tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\r\n" . '     </table>' . "\r\n" . '     </fieldset>';
$str = 'select a.*,b.namaorganisasi,c.nama from ' . $dbname . '.bgt_pks_station_vw a left join' . "\r\n" . '      ' . $dbname . '.organisasi b on a.station=b.kodeorganisasi left join ' . $dbname . '.bgt_kode c on a.kdbudget=c.kodebudget' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and a.station like \'' . $kodeorg . '%\'' . "\r\n" . '      ';
$res = mysql_query($str);
$no = 0;
$rpperha = 0;
$stream .= '<fieldset><legend>' . $_SESSION['lang']['list'] . "\r\n" . '            <img onclick="fisikKeExcel(event,\'bgt_slave_laporan_biaya_pks_excel.php\')" src="images/excel.jpg" class="resicon" title="MS.Excel"> ' . "\r\n\t" . '     <img onclick="fisikKePDF(event,\'bgt_slave_laporan_biaya_pks_pdf.php\')" title="PDF" class="resicon" src="images/pdf.jpg">' . "\r\n" . '            </legend>' . "\r\n" . '             ' . $_SESSION['lang']['unit'] . ':' . $kodeorg . ' ' . $_SESSION['lang']['budgetyear'] . ':' . $thnbudget . "\r\n" . '             <table class=sortable cellspacing=1 border=0\' width=100%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['station'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['kodeabs'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperkg'] . '-TBS</td> ' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperkg'] . '-CPO</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperkg'] . '-PP</td>' . "\r\n" . '                 </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody>';
$old = '';
$jumlah = 0;
$grandtt = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$new = $bar->station;
	$grandtt += $bar->rupiah;

	if ($bar->kdbudget == 'M') {
		$nama_komponen = 'Material';
	}
	else {
		$nama_komponen = $bar->nama;
	}

	if (($old != '') && ($old != $new)) {
		@$jumlahpercpo = $jumlah / ($cpo + $pk);
		@$jumlahpertbs = $jumlah / $tbs;
		@$jmlhCpo = $jumlah / $cpo;
		$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=3>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '           <td align=right>' . number_format($jumlah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($jmlhCpo, 3, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($jumlahpertbs, 3, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($jumlahpercpo, 3, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$jumlah = 0;
		$jumlah += $bar->rupiah;
	}
	else {
		$jumlah += $bar->rupiah;
	}

	@$rupiahpercpo = $bar->rupiah / ($cpo + $pk);
	@$rupiahpertbs = $bar->rupiah / $tbs;
	@$rupiahpercpo2 = $bar->rupiah / $cpo;
	$stream .= '<tr class=rowcontent style=\'cursor:pointer;\' onclick="showDt(\'' . $bar->station . '\',\'' . $bar->kdbudget . '\',\'' . $thnbudget . '\',event)">' . "\r\n" . '           <td>' . $no . '</td>' . "\r\n" . '           <td>' . $bar->namaorganisasi . '</td>' . "\r\n" . '           <td>' . $nama_komponen . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rupiahpercpo2, 3, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($rupiahpertbs, 3, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rupiahpercpo, 3, '.', ',') . '</td>' . "\r\n" . '           ' . "\r\n" . '            ' . "\r\n" . '         </tr>';
	$old = $bar->station;
}

@$jumlahpercpo = $jumlah / ($cpo + $pk);
@$jumlahpertbs = $jumlah / $tbs;
@$jumlahpercpo2 = $jumlah / $cpo;
$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=3 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '           <td align=right>' . number_format($jumlah, 0, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($jumlahpercpo2, 3, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($jumlahpertbs, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($jumlahpercpo, 3, '.', ',') . '</td>' . "\r\n" . '              ' . "\r\n" . '         </tr>';
@$grandttpercpo = $grandtt / ($cpo + $pk);
@$grandttpertbs = $grandtt / $tbs;
@$grandttpercpo2 = $grandtt / $cpo;
$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td colspan=3 align=center>' . $_SESSION['lang']['grnd_total'] . '</td>' . "\r\n" . '           <td align=right>' . number_format($grandtt, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($grandttpercpo2, 3, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($grandttpertbs, 3, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($grandttpercpo, 3, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$stream .= '</tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo $stream;

?>
