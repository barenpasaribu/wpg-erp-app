<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<FORM NAME = \"Daftar Perkiraan\">\r\n<p align=\"center\"><u><b><font face=\"Verdana\" size=\"4\" color=\"#000080\">Element Biaya</font></b></u></p>\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"94%\" id=\"AutoNumber1\" height=\"111\">\r\n  <tr>\r\n    <td width=\"15%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Kode </font></td>\r\n    <td width=\"63%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"10\" name=\"kodeorg\">&nbsp; </font>\r\n    </td>\r\n    <td width=\"16%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    </td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"15%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Keterangan </font></td>\r\n    <td width=\"63%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"51\" name=\"keterangan\"></font></td>\r\n    <td width=\"16%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"15%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"63%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"16%\" height=\"22\">&nbsp;</td>\r\n  </tr>\r\n  </table>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\">\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n<input type=\"submit\" value=\"Simpan\" name=\"Simpan\">\r\n<input type=\"reset\" value=\"Batal\" name=\"Batal\"></font></p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\" id=\"AutoNumber2\"><tr><td width=\"16%\" align=\"center\">Kode </td><td width=\"16%\" align=\"center\">Keterangan</td></tr><tr><td width=\"16%\">&nbsp;</td><td width=\"16%\">&nbsp;</td>\r\n</tr></table>\r\n<p><font face=\"Fixedsys\">&nbsp;&nbsp;&nbsp; &nbsp;</font></p>\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>