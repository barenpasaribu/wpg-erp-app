<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['remoteTimbangan'] . '</b>');
echo '<link rel=stylesheet type=text/css href="style/zTable.css">' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<script language=javascript src="js/zTools.js"></script>' . "\r\n" . '<script language="javascript" src="js/setup_remoteTimbangan.js"></script>' . "\r\n\r\n";
$x = 0;

while ($x <= 24) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optPeriode .= '<option value=' . date('Y-m', $dt) . '>' . date('Y-m', $dt) . '</option>';
	++$x;
}

$lokasi = $_SESSION['empl']['lokasitugas'];
$sql = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'KEBUN\' and kodeorganisasi=\'' . $lokasi . '\'';

#exit(mysql_error());
($query = mysql_query($sql)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$optOrg .= '<option value=' . $res['kodeorganisasi'] . '>' . $res['namaorganisasi'] . '</option>';
}

$arrPrm = '##loksi##ipAdd##idRemote##userName##passwrd##dbnm##port';
echo '<div id="headher">' . "\r\n\r\n" . '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['entryForm'];
echo '</legend>' . "\r\n" . '<table cellspacing="1" border="0">' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['lokasi'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="hidden" name="idRemote" id="idRemote" />' . "\r\n" . '<input type="text" style="width:170px" id="loksi" name="loksi" class="myinputtext" maxlength="4" />' . "\r\n" . '<!--<select id="loksi" name="loksi" style="width:170px;" onchange="getAfdeling(0,0)" ><option value=""></option>';
echo $optOrg;
echo '</select>-->' . "\r\n" . '</td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['ip'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="text" style="width:170px" id="ipAdd" name="ipAdd" class="myinputtext" />' . "\r\n" . '<!--<select id="kodeAfdeling" name="kodeAfdeling" style="width:170px;" onchange="getBlok(0,0)" ><option value=""></option></select>-->' . "\r\n" . '</td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['username'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="text" style="width:170px" id="userName" name="userName" class="myinputtext" />' . "\r\n" . '</td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['password'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="text" style="width:170px" id="passwrd" name="passwrd" class="myinputtext" onKeyPress="return tanpa_kutip(event)" /></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['dbname'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="text" style="width:170px" id="dbnm" name="dbnm" class="myinputtext" onKeyPress="return tanpa_kutip(event)" /></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td>';
echo $_SESSION['lang']['port'];
echo '</td>' . "\r\n" . '<td>:</td>' . "\r\n" . '<td>' . "\r\n" . '<input type="text" style="width:170px" id="port" name="port" class="myinputtextnumber" maxlength="5" onKeyPress="return angka_doang(event)" /></td>' . "\r\n" . '</tr>' . "\r\n" . '<tr>' . "\r\n" . '<td colspan="3" id="tmbLheader">' . "\r\n" . '<button class="mybutton" id="dtlAbn" onclick="saveData(\'';
echo $arrPrm;
echo '\')">';
echo $_SESSION['lang']['save'];
echo '</button><button class="mybutton" id="cancelAbn" onclick="cancelSave()">';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '</td>' . "\r\n" . '</tr>' . "\r\n" . '</table><input type="hidden" id="proses" name="proses" value="insert"  />' . "\r\n" . '</fieldset>' . "\r\n\r\n" . '</div>' . "\r\n";
CLOSE_BOX();
echo '<div id="listData">' . "\r\n";
OPEN_BOX();
echo '<fieldset>' . "\r\n" . '<legend>';
echo $_SESSION['lang']['list'];
echo '</legend>' . "\r\n\r\n" . '<table cellspacing="1" border="0">' . "\r\n" . '<thead>' . "\r\n" . '<tr class="rowheader">' . "\r\n" . '<td>No.</td>' . "\r\n" . '<td>';
echo $_SESSION['lang']['lokasi'];
echo '</td>' . "\r\n" . '<td>';
echo $_SESSION['lang']['ip'];
echo '</td> ' . "\r\n" . '<td>';
echo $_SESSION['lang']['username'];
echo '</td>' . "\r\n" . '<td>';
echo $_SESSION['lang']['password'];
echo '</td>' . "\t\r\n" . '<td>';
echo $_SESSION['lang']['port'];
echo '</td>' . "\t" . ' ' . "\r\n" . '<td>';
echo $_SESSION['lang']['dbname'];
echo '</td>' . "\t" . ' ' . "\r\n" . '<td>Action</td>' . "\r\n" . '</tr>' . "\r\n" . '</thead>' . "\r\n" . '<tbody id="contain">' . "\r\n" . '<script>loadData()</script>' . "\r\n\r\n" . '</tbody>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo '</div>' . "\r\n\r\n\r\n\r\n\r\n";
echo close_body();

?>
