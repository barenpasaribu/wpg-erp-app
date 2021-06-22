<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$sKodeorg = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where length(kodeorganisasi)=4 ' . "\r\n" . '                          and kodeorganisasi not like \'%HO\' order by namaorganisasi asc';
$optKodeorg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

#exit(mysql_error());
($qKodeOrg = mysql_query($sKodeorg)) || true;

while ($rKodeorg = mysql_fetch_assoc($qKodeOrg)) {
	$optKodeorg .= '<option value=\'' . $rKodeorg['kodeorganisasi'] . '\'>' . $rKodeorg['namaorganisasi'] . '</option>';
}

$arr = '##kdUnit';
echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/pad_pembebasan.js\'></script> <!-- sambungin dengan pad_daftarPembebasan.php untuk link PDF -->' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['pembebasan'] . ' ' . $_SESSION['lang']['lahan'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kodeorganisasi'];
echo '</label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">' . "\r\n";
echo $optKodeorg;
echo '</select></td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'pad_2slave_pembebasanLahan\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'pad_2slave_pembebasanLahan.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
