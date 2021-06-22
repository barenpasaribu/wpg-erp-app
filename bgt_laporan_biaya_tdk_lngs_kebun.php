<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/bgt_btl_kebun.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'BIAYA TIDAK LANGSUNG');
$str = 'select distinct(tahunbudget) as tahunbudget from  ' . $dbname . '.bgt_budget order by tahunbudget desc';
$res = mysql_query($str);
$opttahun = '<option value=\'\'>Pilih..</option>';

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select kodeorganisasi as kodeorg from  ' . $dbname . '.organisasi where (tipe=\'KEBUN\' or tipe=\'KANWIL\') order by kodeorganisasi';
$res = mysql_query($str);
$optunit = '<option value=\'\'>Pilih..</option>';

while ($bar = mysql_fetch_object($res)) {
	$optunit .= '<option value=\'' . $bar->kodeorg . '\'>' . $bar->kodeorg . '</option>';
}

echo '<fieldset style=\'width:500px;\'><table>' . "\r\n" . '     <tr><td>' . $_SESSION['lang']['tahunanggaran'] . '</td><td><select id=thnbudget style=\'width:200px\'>' . $opttahun . '</select></td></tr>' . "\r\n\t" . ' <tr><td>' . $_SESSION['lang']['kodeorganisasi'] . '</td><td><select id=kodeunit style=\'width:200px\'>' . $optunit . '</select></td></tr>' . "\r\n" . '     </table>' . "\r\n\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t" . ' <button class=mybutton onclick=tampilkanBTLKebun()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . "\r\n" . '            Result:' . "\r\n" . '            <span id="printPanel" style="display:none;">' . "\r\n" . '            <img onclick="fisikKeExcel(event,\'bgt_laporan_biaya_tdk_lngs_kebun_excel.php\')" src="images/excel.jpg" class="resicon" title="MS.Excel"> ' . "\r\n\t" . '     <img onclick="fisikKePDF(event,\'...\')" title="PDF" class="resicon" src="images/pdf.jpg">' . "\r\n" . '            </span>' . "\r\n" . '            </legend>' . "\r\n" . '             Unit:<label id=unit></label> Tahun Budget:<label id=tahun></label>' . "\r\n" . '             <table class=sortable cellspacing=1 border=0 style=\'width:1600px;\'>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['luas'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>' . "\r\n" . '                   <td align=center>' . $_SESSION['lang']['rpperha'] . '</td>  ' . "\r\n" . '                   <td align=center>01(Rp)</td>' . "\r\n" . '                   <td align=center>02(Rp)</td>' . "\r\n" . '                   <td align=center>03(Rp)</td>' . "\r\n" . '                   <td align=center>04(Rp)</td>' . "\r\n" . '                   <td align=center>05(Rp)</td>' . "\r\n" . '                   <td align=center>06(Rp)</td>' . "\r\n" . '                   <td align=center>07(Rp)</td>' . "\r\n" . '                   <td align=center>08(Rp)</td>' . "\r\n" . '                   <td align=center>09(Rp)</td>' . "\r\n" . '                   <td align=center>10(Rp)</td>' . "\r\n" . '                   <td align=center>11(Rp)</td>' . "\r\n" . '                   <td align=center>12(Rp)</td>' . "\r\n" . '                 </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>';
echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
