<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/log_3rekalkulasi_stock.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['transaksigudang']) . '</b>');
$str = 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '      where tipe = \'GUDANG\'' . "\r\n\t" . '  order by namaorganisasi desc';
$res = mysql_query($str);
$optunit = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	$optunit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

echo '<fieldset>' . "\r\n" . '     <legend>Stock Recalculation</legend>' . "\r\n\t" . ' <table cellspacing=1 border=0><tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['daftargudang'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n\t" . '     <select id=unit style=\'width:150px;\' onchange=ambilPeriode(this.options[this.selectedIndex].value)>' . $optunit . '</select></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td colspan=2><button class=mybutton onclick=getTransaksiGudang()>' . $_SESSION['lang']['proses'] . '</button></td>' . "\r\n\t" . ' </tr></table>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=rekalkulasiStockKeExcel(event,\'log_slave_3rekalkulasi_stock.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '       <table class=sortable cellspacing=1 border=0 width=100% id=container>' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
