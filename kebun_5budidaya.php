<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

echo "<script type=\"text/javascript\" src=\"js/kebun_5budidaya.js\"></script>\r\n";

$optpabrik = '';

$str = 'select * from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' order by kodeorganisasi desc";

$res = mysql_query($str) ;

echo "<fieldset>\r\n\t<legend>";

echo $_SESSION['lang']['tblbudaya'];

echo "</legend>\r\n\t<table cellspacing=\"1\" border=\"0\">\r\n\t\t<tr>\r\n\t\t\t<td>";

echo $_SESSION['lang']['namaorganisasi'];

echo "</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td>";

while ($bar = mysql_fetch_object($res)) {

    $optpabrik .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';

}

echo "\t\t\t<select id=\"kd_org\" style='width:150px;'>";

echo $optpabrik;

echo "</select>\t\t</td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td>";

echo $_SESSION['lang']['kdbudaya'];

echo "</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><input type=\"text\" id=\"kd_budidaya\" onKeyPress=\"return angka_doang(event);\"  /></td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td>";

echo $_SESSION['lang']['keterangan'];

echo "</td>\r\n\t\t\t<td>:</td>\r\n\t\t\t<td><input type=\"text\" id=\"ket\" /></td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td colspan=\"3\">\r\n\t\t\t<input type=\"hidden\" value=\"insert\" id=\"method\"  />\r\n\t\t\t<button class=mybutton onclick=simpanTblbudaya()>";

echo $_SESSION['lang']['save'];

echo "</button>\r\n\t\t\t<button class=mybutton onclick=btlTblbdya()>";

echo $_SESSION['lang']['cancel'];

echo "</button>\r\n\t\t\t</td>\r\n\t\t</tr>\r\n\t</table>\r\n</fieldset>\r\n";

CLOSE_BOX();

OPEN_BOX();

echo "<fieldset>\r\n\t <table class=\"sortable\" cellspacing=\"1\" border=\"0\">\r\n\t <thead>\r\n\t <tr class=rowheader>\r\n\t <td>No.</td>\r\n\t <td>";

echo $_SESSION['lang']['kodeorg'];

echo "</td>\r\n\t <td>";

echo $_SESSION['lang']['namaorganisasi'];

echo "</td> \r\n\t <td>";

echo $_SESSION['lang']['kdbudaya'];

echo "</td>\r\n\t <td>";

echo $_SESSION['lang']['keterangan'];

echo "</td>\r\n\t <td colspan=\"2\">Action</td>\r\n\t </tr>\r\n\t </thead>\r\n\t <tbody id=\"container\">\r\n\t ";

$srt = 'select * from '.$dbname.'.kebun_5budidaya order by kode desc';

if ($rep = mysql_query($srt)) {

    while ($bar = mysql_fetch_object($rep)) {

        $spr = 'select * from  '.$dbname.".organisasi where `kodeorganisasi`='".$bar->kodeorg."'";

        $rej = mysql_query($spr) ;

        $bas = mysql_fetch_object($rej);

        ++$no;

        echo "<tr class=rowcontent>\r\n\t\t\t\t  <td>".$no."</td>\r\n\t\t\t\t  <td>".$bas->kodeorganisasi."</td>\r\n\t\t\t\t  <td>".$bas->namaorganisasi."</td>\r\n\t\t\t\t  <td>".$bar->kode."</td>\r\n\t\t\t\t  <td>".$bar->budidaya."</td>\r\n\t\t\t\t  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kodeorg."','".$bar->budidaya."');\"></td>\r\n\t\t\t\t  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delTbldya('".$bar->kode."');\"></td>\r\n\t\t\t\t </tr>";

    }

} else {

    echo ' Gagal,'.mysql_error($conn);

}



echo "\t  </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>\r\n</fieldset>\r\n<!--<FORM NAME = \" \">\r\n<p align=\"center\"><u><b><font face=\"Verdana\" size=\"4\" color=\"#000080\">Status Tanaman</font></b></u></p>\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"87%\" id=\"AutoNumber1\" height=\"115\">\r\n  <tr>\r\n    <td width=\"24%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Kode</font></td>\r\n    <td width=\"46%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"6\" name=\"koderekening\">&nbsp; </font>\r\n    </td>\r\n    <td width=\"16%\" height=\"1\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    </td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    <font face=\"Arial\">Keterangan</font></td>\r\n    <td width=\"46%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\"> \r\n    <input type=text size=\"41\" name=\"tanggal\"></font></td>\r\n    <td width=\"16%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"></td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\">\r\n    </td>\r\n    <td width=\"46%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"></td>\r\n    <td width=\"16%\" height=\"22\">\r\n    <p style=\"margin-top: 0; margin-bottom: 0\"></td>\r\n  </tr>\r\n  <tr>\r\n    <td width=\"24%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"46%\" height=\"22\">&nbsp;</td>\r\n    <td width=\"16%\" height=\"22\">&nbsp;</td>\r\n  </tr>\r\n  </table>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\"><font face=\"Fixedsys\">\r\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\r\n<input type=\"submit\" value=\"Simpan\" name=\"Simpan\">\r\n<input type=\"reset\" value=\"Batal\" name=\"Batal\"></font></p>\r\n<p style=\"margin-top: 0; margin-bottom: 0\">&nbsp;</p>\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"100%\" id=\"AutoNumber2\"><tr><td width=\"16%\" align=\"center\">Kode</td><td width=\"16%\" align=\"center\">Keterangan</td></tr><tr><td width=\"16%\">&nbsp;</td><td width=\"16%\">&nbsp;</td>\r\n</tr></table>\r\n<p><font face=\"Fixedsys\">&nbsp;&nbsp;&nbsp; &nbsp;</font></p>-->\r\n\r\n";

CLOSE_BOX();

echo close_body();



?>