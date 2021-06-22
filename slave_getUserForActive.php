<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
$str = 'select a.*,b.namakaryawan,b.lokasitugas from ' . $dbname . '.user a ' . "\r\n" . '              left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where a.namauser like \'%' . $uname . '%\'';
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	echo '<table class=sortable cellspacing=1 border=0 onmousedown=sorttable.makeSortable(this)>' . "\r\n" . '                 <thead>' . "\r\n" . '                       <tr>' . "\r\n" . '                       <td>Uname</td>' . "\r\n" . '                       <td>UserId</td>' . "\r\n" . '                       <td>Name</td>' . "\r\n" . '                       <td>Location</td>' . "\r\n" . '                       <td>Status<br>Active/NotActive</td>' . "\r\n" . '                       <td>Delete</td>' . "\r\n" . '                       </tr>' . "\r\n" . '                     </thead>' . "\r\n" . '                     <tbody>';

	while ($bar = mysql_fetch_object($res)) {
		$opt = '';

		if ($bar->status == 0) {
			$opt .= '<input type=checkbox id=' . $bar->namauser . ' title=\'Click to activate\' onclick="setActivate(\'' . $bar->namauser . '\');">';
		}
		else {
			$opt .= '<input type=checkbox id=' . $bar->namauser . ' checked  title=\'Click to deActivate\' onclick="setActivate(\'' . $bar->namauser . '\');">';
		}

		echo ' <tr class=rowcontent id=\'row' . $bar->namauser . '\'>' . "\r\n" . '                          <td class=firsttd>' . $bar->namauser . '</td>' . "\r\n" . '                              <td>' . $bar->karyawanid . '</td>' . "\r\n" . '                               <td>' . $bar->namakaryawan . '</td>' . "\r\n" . '                                <td>' . $bar->lokasitugas . '</td>   ' . "\r\n" . '                              <td align=center>' . $opt . '</td>' . "\r\n" . '                              <td align=center>' . "\r\n" . '              <img class=iconclick src=images/delete1.png  height=14px title=\'Delete\' onclick=delUser(\'' . $bar->namauser . '\',\'' . $bar->karyawanid . '\')> &nbsp' . "\r\n" . '                              </td>' . "\r\n" . '                     </tr>';
	}

	echo "\t" . ' ' . "\r\n" . '                     </tbody>' . "\r\n" . '                </table>' . "\r\n" . '                    ';
}
else {
	echo 'No data found..';
}

?>
