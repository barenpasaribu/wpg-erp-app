<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src=js/log_laporan.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['hutangsupplierbpb']) . '</b>');
$str = 'select distinct periode from ' . $dbname . '.log_5saldobulanan' . "\r\n" . '      order by periode desc';
$res = mysql_query($str);
$optper = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	$optper .= '<option value=\'' . $bar->periode . '\'>' . substr($bar->periode, 5, 2) . '-' . substr($bar->periode, 0, 4) . '</option>';
}

//$str = 'select distinct a.kodeorg,b.namaorganisasi from ' . $dbname . '.setup_periodeakuntansi a' . "\r\n" . '      left join ' . $dbname . '.organisasi b' . "\r\n\t" . '  on a.kodeorg=b.kodeorganisasi' . "\r\n" . '      where b.tipe=\'GUDANG\'' . "\r\n\t" . '  order by namaorganisasi desc';
//$res = mysql_query($str);
//$optgudang = '';
//
//while ($bar = mysql_fetch_object($res)) {
//	$optgudang .= '<option value=\'' . $bar->kodeorg . '\'>' . $bar->namaorganisasi . '</option>';
//}
$optgudang=makeOption2(getQuery("gudang"),
	array( "valuefield"=>'',"captionfield"=> $_SESSION['lang']['all'] ),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
//echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['hutangsupplierbpb'] . '</legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['sloc'] . '<select id=gudang style=\'width:150px;\' onchange=ambilPeriode(this.options[this.selectedIndex].value)>' . $optgudang . '</select>' . "\r\n\t" . ' ' . $_SESSION['lang']['periode'] . '<select id=periode onchange=hideById(\'printPanel\')>' . $optper . '</select>' . "\r\n\t" . ' <button class=mybutton onclick=getHutangSupplier()>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['hutangsupplierbpb'] . '</legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['sloc'] . '<select id=gudang style=\'width:150px;\' onchange=ambilPeriode(\'gudang2\')>' . $optgudang . '</select>' . "\r\n\t" . ' ' . $_SESSION['lang']['periode'] . '<select id=periode onchange=hideById(\'printPanel\')>' . $optper . '</select>' . "\r\n\t" . ' <button class=mybutton onclick=getHutangSupplier()>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=hutangSupplierKeExcel(event,\'log_laporanhutangsupplier_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=hutangSupplierKePDF(event,\'log_laporanhutangsupplier_pdf.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '       <table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . '    <tr>' . "\r\n\t\t\t" . '  <td align=center>No.</td><td>No. Transaksi</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['kodesupplier'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n\t\t\t" . '  <td align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n\t\t\t" . '</tr>  ' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\t\t" . ' ' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
