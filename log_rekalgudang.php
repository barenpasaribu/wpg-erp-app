<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['rekalform'] . '</b>');
$frm[0] = '';
$frm[1] = '';
$optKlmpk = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

if ($_SESSION['empl']['bagian'] == 'HO_ITGS') {
	$sOrg = 'select distinct kodegudang,namaorganisasi from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '       left join ' . $dbname . '.organisasi b on a.kodegudang=b.kodeorganisasi ' . "\r\n" . '       order by kodegudang asc';
}
else {
	$sOrg = 'select distinct kodegudang,namaorganisasi from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '       left join ' . $dbname . '.organisasi b on a.kodegudang=b.kodeorganisasi where induk=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '       order by kodegudang asc';
}

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optKlmpk .= '<option value=' . $rOrg['kodegudang'] . '>' . $rOrg['kodegudang'] . ' - ' . $rOrg['namaorganisasi'] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optLokal2 = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arr = '##gdngId##periodeGdng##kdBrg';
echo '<script>' . "\r\n" . 'dert="';
echo $optLokal2;
echo '";' . "\r\n" . 'dert2="';
echo $optLokal;
echo '";' . "\r\n" . '</script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/log_rekalgudang.js\'></script>' . "\r\n\r\n" . '<link rel=stylesheet type=\'text/css\' href=\'style/zTable.css\'>' . "\r\n";
$frm[0] .= "<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>".$_SESSION['lang']['rekalform']."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['pilihgudang'].'</label></td><td><select id="gdngId" name="gdngId" style="width:150px" onchange="getPeriode()">'.$optKlmpk.'</select></td></tr>';
$frm[0] .= '<tr><td><label>'.$_SESSION['lang']['periode'].'</label></td><td>';
if ('HO_ITGS' === $_SESSION['empl']['bagian']) {
    $frm[0] .= '<input type="text" id="periodeGdng" style="width:150px" class="myinputtext" />';
} else {
    $frm[0] .= '<select id="periodeGdng" style="width:150px">'.$optLokal.'</select>';
}

$frm[0] .= "</td></tr>\r\n<tr><td><label>".$_SESSION['lang']['kodebarang']."</label></td><td>\r\n        <input type=text id=kdBrg class=myinputtext onclick=\"getKdBrg('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang'].'</legend>'.$_SESSION['lang']['find'].'&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findBarang()>'.$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event)\" style=width:150px; /><span id=nmBrg></span></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('log_slave_rekalgudang','".$arr."','printContainer')\" class=\"mybutton\">Proses</button>\r\n        </td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>";
$frm[0] .= "<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['list']."</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>";
$arr2 = '##gdngId2##periodeGdng2';
$frm[1] .= "<div>\r\n<fieldset style=\"float: left;\">\r\n<legend><b>".$_SESSION['lang']['rekalform'].' '.$_SESSION['lang']['tidaknormal']."</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>".$_SESSION['lang']['pilihgudang'].'</label></td><td><select id="gdngId2" name="gdngId" style="width:150px" onchange="getPeriode2()">'.$optKlmpk."</select></td></tr>\r\n<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td>\r\n<select id=\"periodeGdng2\" style=\"width:150px\">".$optLokal."</select>\r\n    </td></tr>\r\n<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview2('log_slave_rekalgudang','".$arr2."','printContainer2')\" class=\"mybutton\">Proses</button>\r\n        </td></tr>\r\n\r\n</table>\r\n</fieldset>\r\n</div>";
$frm[1] .= "<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['list']."</b></legend>\r\n<div id='printContainer2' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>";
$hfrm[0] = $_SESSION['lang']['rekalform'];
$hfrm[1] = $_SESSION['lang']['rekalform'] . ' ' . $_SESSION['lang']['tidaknormal'];
drawTab('FRM', $hfrm, $frm, 200, 900);
CLOSE_BOX();
echo close_body();

?>
