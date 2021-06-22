<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n\r\n" . '<iframe src="keu_slave_5daftarperkiraan_pdf.php?table=keu_5akun" width="100%" height="450px"></iframe>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
