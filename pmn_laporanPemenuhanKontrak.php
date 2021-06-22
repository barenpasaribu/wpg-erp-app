<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sPeriode = 'select distinct substr(tanggalkontrak,1,4) as periode from ' . $dbname . '.pmn_kontrakjual order by substr(tanggalkontrak,1,4) desc';

#exit(mysql_error($conn));
($qPeriode = mysql_query($sPeriode)) || true;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
	$optPeriode .= '<option value=\'' . $rPeriode['periode'] . '\'>' . $rPeriode['periode'] . '</option>';
}

$optPeriode2 = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sPeriode2 = 'select distinct substr(tanggalkontrak,1,4) as periode from ' . $dbname . '.pmn_kontrakjual order by substr(tanggalkontrak,1,7) desc';

#exit(mysql_error($conn));
($qPeriode2 = mysql_query($sPeriode2)) || true;

while ($rPeriode2 = mysql_fetch_assoc($qPeriode2)) {
	$optPeriode2 .= '<option value=\'' . $rPeriode2['periode'] . '\'>' . $rPeriode2['periode'] . '</option>';
}

$optBrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optBrg2 = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sBrg = 'select namabarang,kodebarang from ' . $dbname . '.log_5masterbarang where kelompokbarang like \'400%\'';

#exit(mysql_error());
($qBrg = mysql_query($sBrg)) || true;

while ($rBrg = mysql_fetch_assoc($qBrg)) {
	$optBrg .= '<option value=' . $rBrg['kodebarang'] . '>' . $rBrg['namabarang'] . '</option>';
	$optBrg2 .= '<option value=' . $rBrg['kodebarang'] . '>' . $rBrg['namabarang'] . '</option>';
}

$arr = '##periode##kdBrg';
$arr2 = '##thn##kdBrg2';
$arr3 = '##kdBrg3##tgl_dr##tgl_samp';
echo "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/pmn_laporanPemenuhanKontrak.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['laporanPemenuhanKontrak'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['periode'];
echo '</label></td><td><select id="periode" name="periode" style="width:150px">';
echo $optPeriode;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['namabarang'];
echo '</label></td><td><select id="kdBrg" name="kdBrg" style="width:150px">';
echo $optBrg;
echo '</select></td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'pmn_slave_laporanPemenuhanKontrak\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf(\'pmn_slave_laporanPemenuhanKontrak\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,\'pmn_slave_laporanPemenuhanKontrak.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>Uncomplete Contract</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '    <tr><td><label>';
echo $_SESSION['lang']['tahun'];
echo '</label></td><td><select id="thn" name="thn" style="width:150px">';
echo $optPeriode2;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['komoditi'];
echo '</label></td><td><select id="kdBrg2" name="kdBrg2" style="width:150px">';
echo $optBrg2;
echo '</select></td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview2(\'pmn_slave_laporanPemenuhanKontrak\',\'';
echo $arr2;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel2(event,\'pmn_slave_laporanPemenuhanKontrak.php\',\'';
echo $arr2;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>Range Delivery</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['komoditi'];
echo '</label></td><td><select id="kdBrg3" name="kdBrg3" style="width:150px">';
echo $optBrg2;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['tgldari'];
echo '</label></td><td><input type=text  style="width:150px" class=myinputtext id=tgl_dr onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['tanggalsampai'];
echo '</label></td><td><input type=text  style="width:150px" class=myinputtext id=tgl_samp onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview3(\'pmn_slave_laporanPemenuhanKontrak\',\'';
echo $arr3;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel3(event,\'pmn_slave_laporanPemenuhanKontrak.php\',\'';
echo $arr3;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
