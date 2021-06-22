<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src='js/zMaster.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n<p align=\"left\"><u><b><font face=\"Arial\" size=\"5\" color=\"#000080\">";
echo $_SESSION['lang']['statustanam'];
echo "</font></b></u></p>\r\n";
$optOrg = getHolding($dbname, $_SESSION['org']['kodeorganisasi'], true);
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kode', 'label', $_SESSION['lang']['kode']), makeElement('kode', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:250px'], $optOrg)];
$fieldStr = '##kode##keterangan##kodeorg';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'kebun_5sttanam', '##kode##kodeorg')];
echo genElement($els);
echo "</div><div style='height:200px;overflow:auto'>";
echo masterTable($dbname, 'kebun_5sttanam', '*', [], [], null, [], null, 'kode##kodeorg');
echo "</div><!--FORM NAME = \"status tanaman\">\r\n<p align=\"center\"><u><b><font face=\"Verdana\" size=\"4\" color=\"#000080\">Status Tanaman</font></b></u></p>\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"87%\" id=\"AutoNumber1\" height=\"115\">\r\n  <tr>\r\n    <td width=\"24%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Kode</font></td>\r\n    <td width=\"46%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"6\" name=\"koderekening\">&nbsp; </font>\r\n    </td>\r\n    <td width=\"16%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    </td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Keterangan</font></td>\r\n    <td width=\"46%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"41\" name=\"tanggal\"></font></td>\r\n    <td width=\"16%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    &nbsp;</td>\r\n    <td width=\"46%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</td>\r\n    <td width=\"16%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"46%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"16%\" height=\"22\">&nbsp;</td>\r\n  </tr>\r\n  </table>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\">\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n<input type=\"submit\" value=\"Simpan\" name=\"Simpan\">\r\n<input type=\"reset\" value=\"Batal\" name=\"Batal\"></font></p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\" id=\"AutoNumber2\"><tr><td width=\"16%\" align=\"center\">Kode</td><td width=\"16%\" align=\"center\">Keterangan</td></tr><tr><td width=\"16%\">&nbsp;</td><td width=\"16%\">&nbsp;</td>\r\n</tr></table>\r\n<p><font face=\"Fixedsys\">&nbsp;&nbsp;&nbsp; &nbsp;</font></p-->\r\n";
CLOSE_BOX();
echo close_body();

?>