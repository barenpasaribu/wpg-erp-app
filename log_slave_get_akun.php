<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$txtfind = $_POST['txtfind'];
$str = ' select * from ' . $dbname . '.keu_5akun where namaakun like \'%' . $txtfind . '%\' or namaakun1 like \'%' . $txtfind . '%\' or noakun like \'%' . $txtfind . '%\' or tipeakun like \'%' . $txtfind . '%\'';

if ($res = mysql_query($str)) {
	echo "\r\n" . '        <fieldset>' . "\r\n" . '        <legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '        <div style="width:450px; height:300px; overflow:auto;">' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '             <thead>' . "\r\n" . '                 <tr class=rowheader>' . "\r\n" . '                 <td class=firsttd>' . "\r\n" . '                 No.' . "\r\n" . '                 </td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </thead>' . "\r\n" . '                 <tbody>';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;

		if ($_SESSION['language'] == 'EN') {
			$z = $bar->namaakun1;
		}
		else {
			$z = $bar->namaakun;
		}

		echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setNoakun(\'' . $bar->noakun . '\',\'' . $z . '\',\'' . $bar->tipeakun . '\',\'' . $bar->matauang . '\',\'' . $bar->kodeorg . '\')" title=\'Click\' >' . "\r\n" . '                      <td class=firsttd>' . $no . '</td>' . "\r\n" . '                      <td>' . $bar->noakun . '</td>' . "\r\n" . '                          <td>' . $z . '</td><td>' . $bar->tipeakun . '</td>' . "\r\n" . '                          <td>' . $bar->matauang . '</td><td>' . $bar->kodeorg . '</td>' . "\r\n" . '                         </tr>';
	}

	echo '</tbody>' . "\r\n" . '              <tfoot>' . "\r\n" . '                  </tfoot>' . "\r\n" . '                  </table></div></fieldset>';
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
