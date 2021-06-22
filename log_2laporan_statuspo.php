<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$optPt1 = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optStat1 = $optPt1;
$optStat = $optPt1;
$optTer1 = $optPt1;
$arrDt1 = array('Head Office', 'Local');

foreach ($arrDt1 as $dtlst => $dtklrm) {
	$optStat1 .= '<option value=\'' . $dtlst . '\'>' . $dtklrm . '</option>';
}

$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPt = $optPeriode;
$sPt = getQuery("pt");// 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';

#exit(mysql_error($conn));
($qPt = mysql_query($sPt)) || true;

while ($rPt = mysql_fetch_assoc($qPt)) {
	$optPt .= '<option value=\'' . $rPt['kodeorganisasi'] . '\'>' . $rPt['namaorganisasi'] . '</option>';
	$optPt1 .= '<option value=\'' . $rPt['kodeorganisasi'] . '\'>' . $rPt['namaorganisasi'] . '</option>';
}

$sPeriode = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi order by periode desc';

#exit(mysql_error());
($qPeriode = mysql_query($sPeriode)) || true;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
	$optPeriode .= '<option value=' . $rPeriode['periode'] . '>' . substr(tanggalnormal($rPeriode['periode']), 1, 7) . '</option>';
}

$arrDt = array('Head Office', 'Local');

foreach ($arrDt as $dtlst => $dtklrm) {
	$optStat .= '<option value=\'' . $dtlst . '\'>' . $dtklrm . '</option>';
}

$arrDt = array('Belum Selesai', 'Sudah Selesai', 'Dapat Dikirim', 'Diterima Gudang');

foreach ($arrDt as $dtlst => $dtklrm) {
	$optTer1 .= '<option value=\'' . $dtlst . '\'>' . $dtklrm . '</option>';
}

$arr = '##periode##statId##ptId';
$arr1 = '##tgl1##tgl2##status1##pt1##terima1';
echo "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'ptId\').value=\'\';' . "\r\n" . '    document.getElementById(\'statId\').value=\'\';' . "\r\n" . '    document.getElementById(\'periode\').value=\'\';' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" . '<link rel=stylesheet type=\'text/css\' href=\'style/zTable.css\'>' . "\r\n" . '      ' . "\r\n";
$title[0] = 'PO Status Report';
$title[1] = 'PO Status Detail';
$frm[0] .= "<div>\r\n    <fieldset style=\"float: left;\">\r\n    <legend><b>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['listpo']."</b></legend>\r\n    <table cellspacing=\"1\" border=\"0\" >\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['pt']."</label></td>\r\n        <td><select id=\"ptId\" name=\"ptId\" style=\"width:150px\">".$optPt."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['periode']."</label></td>\r\n        <td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['status']."</label></td>\r\n        <td><select id=\"statId\" name=\"statId\" style=\"width:150px\">".$optStat."</select></td>\r\n    </tr>\r\n\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('log_slave_2laporan_statuspo','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'log_slave_2laporan_statuspo.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n        <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>\r\n    </td></tr>\r\n    </table>\r\n    </fieldset>\r\n    </div>\r\n\r\n    <div style=\"margin-bottom: 30px;\">\r\n    </div>\r\n    <fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n    <div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n    </div></fieldset>\r\n    ";
$frm[1] .= "<div>\r\n    <fieldset style=\"float: left;\">\r\n    <legend><b>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['listpo']."</b></legend>\r\n    <table cellspacing=\"1\" border=\"0\" >\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['tanggal']."</label></td>\r\n        <td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" />\r\n        ".$_SESSION['lang']['sampai']."\r\n        <input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['status']."</label></td>\r\n        <td><select id=\"status1\" name=\"status1\" style=\"width:150px\">".$optStat1."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['pt']."</label></td>\r\n        <td><select id=\"pt1\" name=\"pt1\" style=\"width:150px\">".$optPt1."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['status'].' '.$_SESSION['lang']['diterima']."</label></td>\r\n        <td><select id=\"terima1\" name=\"terima1\" style=\"width:150px\">".$optTer1."</select></td>\r\n    </tr> \r\n\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('log_slave_2laporan_statuspo1','".$arr1."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'log_slave_2laporan_statuspo1.php','".$arr1."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n        <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>\r\n    </td></tr>\r\n    </table>\r\n    </fieldset>\r\n    </div>\r\n\r\n    <div style=\"margin-bottom: 30px;\">\r\n    </div>\r\n    <fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n    <div id='printContainer1' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n    </div></fieldset>\r\n    ";
$hfrm[0] = $title[0];
$hfrm[1] = $title[1];
drawTab('FRM', $hfrm, $frm, 200, 1220);
CLOSE_BOX();
echo close_body();

?>
