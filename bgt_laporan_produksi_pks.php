<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src="js/bgt_laporan_produksi_pks.js"></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget' . "\r\n" . '                  order by tahunbudget desc';
$res = mysql_query($str);
$opttahun = '';

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PABRIK\' order by namaorganisasi desc';
$res = mysql_query($str);
$optpabrik = '';

while ($bar = mysql_fetch_object($res)) {
	$optpabrik .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

echo '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['distribusiproduksi'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id=tahun style=\'width:200px;\' onchange=hideById(\'printPanel\')>';
echo $opttahun;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kdpabrik'];
echo '</label></td><td><select id=pabrik style=\'width:200px;\' onchange=hideById(\'printPanel\')>';
echo $optpabrik;
echo '</select></td></tr>' . "\r\n" . '<tr><td></td><td><button class=mybutton onclick=getProduksi()>';
echo $_SESSION['lang']['proses'];
echo '</button></td></tr>' . "\r\n\r\n" . '<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->' . "\r\n\r\n" . '<!--<tr><td colspan="2"><button onclick="zPreview(\'sdm_slave_2rekapabsen\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'sdm_slave_2rekapabsen\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'sdm_slave_2rekapabsen.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>-->' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=produksiKeExcel(event,\'bgt_slave_laporan_produksi_pks_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '     <img onclick=produksiKePDF(event,\'bgt_slave_laporan_produksi_pks_pdf.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div id=container style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
