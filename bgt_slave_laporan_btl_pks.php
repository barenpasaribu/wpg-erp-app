<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$thnbudget = $_POST['thnbudget'];
$prd = 0;
$str = 'select sum(kgcpo) as cpo,sum(kgkernel) as kernel,sum(kgolah)  as tbs from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode = \'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$prd = $bar->cpo + $bar->kernel;
	$totTbs = $bar->tbs;
}

$str = 'select a.*,b.namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg=\'' . $kodeorg . '\'';
$res = mysql_query($str);
$no = 0;
$rpperha = 0;
$rptbs = 0;
$str2 = 'select sum(kgolah) as tbs,sum(kgcpo) as cpo,sum(kgkernel) as kernel from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode = \'' . $kodeorg . '\'';
$res2 = mysql_query($str2);

while ($bar2 = mysql_fetch_object($res2)) {
	$tbs = $bar2->tbs;
	$cpo = $bar2->cpo;
	$pk = $bar2->kernel;
	$totTbs = $bar2->tbs;
	$prd = $bar2->cpo + $bar2->kernel;
	$totCpo = $bar2->cpo;
	$totKer = $bar2->kernel;
}

$oil = $cpo + $pk;
$stream = '<fieldset><legend>' . $_SESSION['lang']['produksipabrik'] . ' </legend>' . "\r\n" . '<table class=sortable cellspacing=1 border=0 width=300px>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['tbsdiolah'] . '</td>' . "\r\n" . '           <td align=center>Palm Product</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['cpo'] . '</td>                  ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['kernel'] . '</td> ' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>' . "\r\n" . '         <tr class=rowcontent>' . "\r\n" . '           <td align=right>' . number_format($totTbs / 1000, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($prd / 1000, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($totCpo / 1000, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($totKer / 1000, 0, '.', ',') . '</td>    ' . "\r\n" . '         </tr>     ' . "\r\n" . '     </tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\r\n" . '     </table>' . "\r\n" . '     </fieldset>';
$stream .= '<fieldset><legend>' . $_SESSION['lang']['list'] . "\r\n" . '            Result:' . "\r\n" . '            <img onclick="fisikKeExcel(event,\'bgt_laporan_biaya_tdk_lngs_pks_excel.php\')" src="images/excel.jpg" class="resicon" title="MS.Excel"> ' . "\r\n\t" . '    <img onclick="fisikKePDF(event,\'bgt_laporan_biaya_tdk_lngs_pks_pdf.php\')" title="PDF" class="resicon" src="images/pdf.jpg">' . "\r\n" . '            </legend>' . "\r\n" . '             Unit:' . $kodeorg . ' Tahun Budget:' . $thnbudget . "\r\n" . '             <table class=sortable cellspacing=1 border=0\' width=1600px>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperkg'] . '-PP</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperkg'] . '-TBS</td>' . "\r\n" . '                   <td align=center>01(Rp)</td>' . "\r\n" . '                   <td align=center>02(Rp)</td>' . "\r\n" . '                   <td align=center>03(Rp)</td>' . "\r\n" . '                   <td align=center>04(Rp)</td>' . "\r\n" . '                   <td align=center>05(Rp)</td>' . "\r\n" . '                   <td align=center>06(Rp)</td>' . "\r\n" . '                   <td align=center>07(Rp)</td>' . "\r\n" . '                   <td align=center>08(Rp)</td>' . "\r\n" . '                   <td align=center>09(Rp)</td>' . "\r\n" . '                   <td align=center>10(Rp)</td>' . "\r\n" . '                   <td align=center>11(Rp)</td>' . "\r\n" . '                   <td align=center>12(Rp)</td>' . "\r\n" . '                 </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody>';

while ($bar = mysql_fetch_object($res)) {
	$prd = $cpo + $pk;
	@$rpperha = $bar->rupiah / $prd;
	@$rptbs = $bar->rupiah / $totTbs;
	$no += 1;
	$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $no . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 7, '.', ',') . '</td>  ' . "\r\n" . '           <td align=right>' . number_format($rptbs, 7, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
	$totRup += $bar->rupiah;
	$grTotRp += $rpperha;
	$grTotTbs += $rptbs;
	$tot += 1;
	$tot += 2;
	$tot += 3;
	$tot += 4;
	$tot += 5;
	$tot += 6;
	$tot += 7;
	$tot += 8;
	$tot += 9;
	$tot += 10;
	$tot += 11;
	$tot += 12;
}

$stream .= '<tr><td colspan=3>' . $_SESSION['lang']['total'] . '</td>';
$stream .= '<td>' . number_format($totRup, 0) . '</td><td>' . number_format($grTotRp, 7) . '</td><td>' . number_format($grTotTbs, 7) . '</td>';
$rd = 1;

while ($rd <= 12) {
	$stream .= '<td>' . number_format($tot[$rd], 0) . '</td>';
	++$rd;
}

$stream .= '</tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo $stream;

?>
