<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a ' . "\r\n" . '                              left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where namauser like \'%' . $uname . '%\'';
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	echo '<b>Click on choosen row to show "reset password form".</b><hr>' . "\r\n" . '            <table class=sortable cellspacing=1 border=0 onmousedown=sorttable.makeSortable(this)>' . "\r\n" . '                     <thead>' . "\r\n" . '                           <tr>' . "\r\n" . '                           <td>' . $_SESSION['lang']['username'] . '</td>' . "\r\n" . '                           <td>' . $_SESSION['lang']['karyawanid'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n" . '                           <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n" . '                           </tr>' . "\r\n" . '                         </theader>' . "\r\n" . '                         <tbody>';

	while ($bar = mysql_fetch_object($res)) {
		$opt = '';

		if ($bar->status == 0) {
			$opt .= '<font color=#aa3333>Not Active</font>';
		}
		else {
			$opt .= '<font color=#00ff00>Active</font>';
		}

		echo ' <tr class=rowcontent id=\'row' . $bar->namauser . '\' title=\'Click to show dialog\' style=\'cursor:pointer;\' onclick="showDial(\'' . $bar->namauser . '\',\'' . $bar->karyawanid . '\',event,this);">' . "\r\n" . '                              <td class=firsttd>' . $bar->namauser . '</td>' . "\r\n" . '                                  <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                                  <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                                   <td>' . $bar->lokasitugas . '</td>   ' . "\r\n" . '                                  <td align=center>' . $opt . '</td>' . "\r\n" . '                         </tr>';
	}

	echo "\t" . ' ' . "\r\n" . '                         </tbody>' . "\r\n" . '                    </table>' . "\r\n" . '                        ';
}
else {
	echo 'No data found..';
}

?>
