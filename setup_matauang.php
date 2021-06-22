<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/setup_matauang.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n\r\n" . '<p align="left"><u><b><font face="Arial" size="3" color="#000080">Mata Uang</font></b></u></p>' . "\r\n";
$field = array('kode', 'matauang', 'simbol', 'kodeiso');
$header = array();

foreach ($field as $row) {
	$header[] = $_SESSION['lang'][$row];
}

$header[] = 'Z';
$query = selectQuery($dbname, 'setup_matauang', $field);
$data = fetchData($query);
$content = array();
$j = 0;

if ($data != array()) {
	foreach ($data as $i => $row) {
		foreach ($row as $key => $data) {
			$content[$i][$key] = makeElement($key . '_' . $i, 'txt', $data, array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)'));
		}

		$content[$i]['Z'] = '<img id=\'edit_' . $i . '\' title=\'Edit\' class=zImgBtn onclick="editMain(\'' . $i . '\',\'kode\',\'' . $row['kode'] . '\')" src=\'images/001_45.png\'/>';
		$content[$i] .= 'Z';
		$content[$i] .= 'Z';
		$j = $i + 1;
	}
}

foreach ($field as $row) {
	$content[$j][$row] = makeElement($row . '_' . $j, 'txt', '', array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)'));
}

$content[$j]['Z'] = '<img id=\'add_' . $j . '\' title=\'Tambah\' class=zImgBtn onclick="addMain(\'' . $j . '\')" src=\'images/plus.png\'/>';
$content[$j] .= 'Z';
$content[$j] .= 'Z';
$mainTable = makeTable('matauangMainTable', 'mainBody', $header, $content);
echo '<div id=\'mainTable\' style=\'float:left;margin-right:100px;\'>';
echo '<fieldset><legend><b>Header Mata Uang</b></legend>';
echo $mainTable;
echo '</fieldset></div>';
echo '<fieldset><legend><b>Detail Mata Uang</b></legend>';
echo '<div id=\'detailTable\'>';
echo '</div></fieldset>';
echo '<!--FORM NAME = "Pinjaman">' . "\r\n" . '<p align="left"><u><b><font face="Arial" size="5" color="#000080">Mata Uang</font></b></u></p>' . "\r\n" . '<table id="Table" border="1" width="352">' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Kode</font></td>' . "\r\n" . '    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Mata Uang</font></td>' . "\r\n" . '    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Symbol</font></td>' . "\r\n" . '    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Kode Iso</font></td>' . "\r\n" . '   </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="72"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="9" name="kode"></font></td>' . "\r\n" . '    <td width="72"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="27" name="matauang"></font></td>' . "\r\n" . '    <td width="106"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="10" name="simbol"></font></td>' . "\r\n" . '    <td width="106"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="10" name="kodeiso"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '</table>' . "\r\n" . '<p>&nbsp;</p>' . "\r\n\r\n" . '<table id="Table" border="1" width="352">' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Tanggal</font></td>' . "\r\n" . '    <td width="72" bgcolor="#C0C0C0"><font face="Fixedsys">Sampai Tanggal</font></td>' . "\r\n" . '    <td width="106" bgcolor="#C0C0C0"><font face="Fixedsys">Kurs</font></td>' . "\r\n" . '   </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="72"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="9" name="tanggal"></font></td>' . "\r\n" . '    <td width="72"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="10" name="sampaitanggal"></font></td>' . "\r\n" . '    <td width="106"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="20" name="kurs"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '</table>' . "\r\n" . '<p>&nbsp;</p>' . "\r\n" . '<p><font face="Fixedsys"><input type="button" value="Simpan" name="ModifDtl">&nbsp;' . "\r\n" . '<input type="button" value="   Batal   " name="DeleteDtl"></font></p>' . "\r\n" . '<p><font face="Fixedsys">&nbsp;&nbsp; &nbsp;</font></p-->' . "\r\n";
CLOSE_BOX();
echo close_body();

?>
