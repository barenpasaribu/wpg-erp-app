<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<FORM NAME = "Supplier">' . "\r\n" . '<p align="left"><b><font face="Arial" size="5" color="#000080">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . "\r\n" . '<u>Daftar Rekanan</u></font></b></p>' . "\r\n" . '<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" id="AutoNumber2" height="80" width="713">' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="22">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kode Rekanan</font></b></td>' . "\r\n" . '    <td width="575" height="22">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">' . "\r\n" . '    <input type=text size="8" name="koderekanan"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Nama Rekanan</font></b></td>' . "\r\n" . '    <td width="575" height="19">' . "\r\n" . '<p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '<input type="text" name="namarekanan" size="42"></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Alamat</font></b></td>' . "\r\n" . '    <td width="575" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="80" name="alamat"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="18">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kota</font></b></td>' . "\r\n" . '    <td width="575" height="18">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="19" name="kota"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Telepon</font></b></td>' . "\r\n" . '    <td width="575" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="24" name="telepon"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Hubungan</font></b></td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="24" name="contakperson"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="19">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">' . "\r\n" . '    Plafon</font></b></td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="24" name="plafon"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">NPWP</font></b></td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="24" name="npwp"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">No Seri Pajak</font></b></td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <font face="Fixedsys">' . "\r\n" . '    <input type=text size="24" name="noseripajak"></font></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0"><b><font face="Verdana" size="2">Kategori </font>' . "\r\n" . '    </b></td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    <p style="margin-top: 0; margin-bottom: 0">' . "\r\n" . '    <select size="1" name="typerekanan">' . "\r\n" . '    <option selected>A</option>' . "\r\n" . '    <option>B</option>' . "\r\n" . '    <option>C</option>' . "\r\n" . '    </select></td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    </td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    </td>' . "\r\n" . '  </tr>' . "\r\n" . '  <tr>' . "\r\n" . '    <td width="138" height="17">' . "\r\n" . '    </td>' . "\r\n" . '    <td width="575" height="17">' . "\r\n" . '    </td>' . "\r\n" . '  </tr>' . "\r\n" . '  </table>' . "\r\n" . '  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" value="Simpan" name="B1">&nbsp;' . "\r\n" . '  <input type="reset" value="Batal" name="B2"></p>' . "\r\n" . '</form>' . "\r\n";
CLOSE_BOX();
echo close_body();

?>