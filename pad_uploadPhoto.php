<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
echo '<center><fieldset  style=\'width:430px;\'><legend>Upload Picture For:' . $_GET['notransaksi'] . '</legend>' . "\r\n" . '       <form id=\'photoqc\' name=\'photoqc\' method=post enctype=\'multipart/form-data\'>' . "\t" . '   ' . "\t" . ' ' . "\r\n" . '       <table cellapacing=1 border=0>' . "\t\t\r\n" . '        <tr>' . "\r\n" . '                     <td>Photo1</td>' . "\r\n" . '                     <td>' . "\r\n" . '                        <input type=hidden name=MAX_FILE_SIZE value=75000>' . "\r\n" . '                        <input type=file name=file[] size35>' . "\r\n" . '                     </td>' . "\r\n" . '        </tr><tr>' . "\t\t\t\t\r\n" . '                     <td>Photo2</td>' . "\r\n" . '                     <td>' . "\r\n" . '                        <input type=file name=file[] size35>' . "\r\n" . '                     </td>' . "\r\n" . '        </tr><tr>' . "\r\n" . '        </tr><tr>' . "\t\t\t\t\r\n" . '                     <td>Photo3</td>' . "\r\n" . '                     <td>' . "\r\n" . '                        <input type=file name=file[] size35>' . "\r\n" . '                     </td>' . "\r\n" . '        </tr><tr>' . "\t" . '           ' . "\r\n" . '                     <td>Photo4</td>' . "\r\n" . '                     <td>' . "\r\n" . '                        <input type=file name=file[] size35>' . "\r\n" . '                        <input type=hidden name=notransaksi id=photox value=\'' . $_GET['notransaksi'] . '\'>' . "\r\n" . '                     </td>' . "\t\t\t\t\t\t\r\n" . '        </tr>' . "\r\n" . '    </table>' . "\r\n\r\n" . '        </form>' . "\r\n" . '        <center>' . "\r\n" . '        Max 75Kb/File.<br>' . "\r\n" . '        <button onclick=parent.simpanPhoto()>Save</button>' . "\r\n" . '        </center>' . "\r\n" . '        </fieldset></center><hr>';
$str = 'select * from ' . $dbname . '.pad_photo where idlahan=\'' . $_GET['notransaksi'] . '\'';
$res = mysql_query($str);
$no = 1;

while ($bar = mysql_fetch_object($res)) {
	echo $no . '. Filename:' . $bar->filename . ' (' . number_format($bar->filesize / 1000, 2) . 'Kb.) <a style=\'cursor:pointer;color:blue; title=\'Delete\' onclick="parent.delPicture(\'' . $bar->idlahan . '\',\'' . $bar->filename . '\')">Remove</a><br>';
	$ext = split('[.]', basename($bar->filename));
	$ext = $ext[count($ext) - 1];
	$ext = strtolower($ext);
	if (($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'png') || ($ext == 'bmp') || ($ext == 'gif') || ($ext == 'tiff')) {
		echo '<img src=filepad/' . $bar->filename . ' height=250px><br>';
	}
	else {
		echo '<a href="filepad/' . $bar->filename . '"><img src=images/preview.png></a><br>';
	}

	++$no;
}

?>
