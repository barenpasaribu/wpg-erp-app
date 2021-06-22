<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "\r\n";
$optPeriode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optPt = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optSupplier = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optKelompok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
//$sPt = 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\'';
//
//#exit(mysql_error($conn));
//($qPt = mysql_query($sPt)) || true;
//
//while ($rPt = mysql_fetch_assoc($qPt)) {
//	$optPt .= '<option value=\'' . $rPt['kodeorganisasi'] . '\'>' . $rPt['namaorganisasi'] . '</option>';
//}
$optPt=makeOption2(getQuery("pt"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
$sPt = 'select kode, kelompok from ' . $dbname . '.log_5klbarang ' . "\r\n" . '    where 1' . "\r\n" . '    order by kode';

#exit(mysql_error($conn));
($qPt = mysql_query($sPt)) || true;

while ($rPt = mysql_fetch_assoc($qPt)) {
	$optKelompok .= '<option value=\'' . $rPt['kode'] . '\'>' . $rPt['kode'] . ' - ' . $rPt['kelompok'] . '</option>';
}

//$sPt = get'select kodesupplier, namasupplier from ' . $dbname . '.log_po_vw ' . "\r\n" . '    where 1' . "\r\n" . '    group by kodesupplier' . "\r\n" . '    order by namasupplier';
//
//#exit(mysql_error($conn));
//($qPt = mysql_query($sPt)) || true;
//
//while ($rPt = mysql_fetch_assoc($qPt)) {
//	$optSupplier .= '<option value=\'' . $rPt['kodesupplier'] . '\'>' . $rPt['namasupplier'] . '</option>';
//}

$optSupplier=makeOption2(getQuery("supplier"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'supplierid',"captionfield"=> 'namasupplier' )
);

$sPeriode = 'select distinct substr(tanggal,1,7) as periode from ' . $dbname . '.log_poht order by substr(tanggal,1,7) desc';

#exit(mysql_error());
($qPeriode = mysql_query($sPeriode)) || true;

while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
	$optPeriode .= '<option value=' . $rPeriode['periode'] . '>' . substr(tanggalnormal($rPeriode['periode']), 1, 7) . '</option>';
}

$arr = '##pt##periode##supplier##kelompok##periode1##namasupplier';
echo "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" .
	'<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" .
	'<script>' . "\r\n" . 'function clear()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'pt\').value=\'\';' . "\r\n" . '//    document.getElementById(\'statId\').value=\'\';' . "\r\n" . '    document.getElementById(\'periode\').value=\'\';' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n" .
	'<link rel=stylesheet type=\'text/css\' href=\'style/zTable.css\'>' . "\r\n" . '       ' . "\r\n";
$title[0] = $_SESSION['lang']['realisasipembayaranpo'];
$frm[0] .= "<div>\r\n    <fieldset style=\"float: left;\">\r\n    <legend><b>".$_SESSION['lang']['form']."</b></legend>\r\n    <table cellspacing=\"1\" border=\"0\" >\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['pt']."</label></td>\r\n        <td><select id=\"pt\" name=\"pt\" style=\"width:150px\">".$optPt."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['periode']."</label></td>\r\n        <td><select id=\"periode\" name=\"periode\" style=\"width:70px\">".$optPeriode."</select> - \r\n            <select id=\"periode1\" name=\"periode1\" style=\"width:70px\">".$optPeriode."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['kodesupplier']."</label></td>\r\n        <td><select id=\"supplier\" name=\"supplier\" style=\"width:150px\">".$optSupplier."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['namasupplier']."</label></td>\r\n        <td><input type=text class=myinputtext id=namasupplier onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['kodekelompok']."</label></td>\r\n        <td><select id=\"kelompok\" name=\"kelompok\" style=\"width:150px\">".$optKelompok."</select></td>\r\n    </tr>\r\n    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n    <tr><td colspan=\"2\">\r\n        <button onclick=\"zPreview('log_slave_2realisasipembayaranpo','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'log_slave_2realisasipembayaranpo.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n<!--        <button onclick=\"clear()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button> -->\r\n    </td></tr>\r\n    </table>\r\n    </fieldset>\r\n    </div>\r\n\r\n    <div style=\"margin-bottom: 30px;\">\r\n    </div>\r\n    <fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n    <div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n    </div></fieldset>\r\n    ";
$hfrm[0] = $title[0];
drawTab('FRM', $hfrm, $frm, 200, 1220);
CLOSE_BOX();
echo close_body();

?>
