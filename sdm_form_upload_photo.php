<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
echo "\t" . ' <form id=frmUpload enctype=multipart/form-data method=post action=sdm_slave_simpan_photo_karyawan.php target=frame>' . "\t\r\n\t" . ' <input type=hidden name=karyawanid id=karyawanid value=\'\'>' . "\r\n\t" . ' <input type=hidden name=MAX_FILE_SIZE value=51000>' . "\r\n\t" . ' <input name=photo type=file id=photo size=35>' . "\r\n" . '     </form>' . "\r\n";

?>
