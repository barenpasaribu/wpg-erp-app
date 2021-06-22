<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$txtfind_klp = $_POST['txtfind_klp'];
$stp = 'select * from ' . $dbname . '.pmn_4klcustomer where kelompok like \'%' . $txtfind_klp . '%\' limit 12';

if ($txtfind_klp != '') {
	if ($rep = mysql_query($stp)) {
		echo '<table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '                 <thead>' . "\r\n" . '                 <tr class=rowheader>' . "\r\n" . '                 <td class=firsttd>' . "\r\n" . '                 No.' . "\r\n" . '                 </td>' . "\r\n" . '                 <td>Group Code</td>' . "\r\n" . '                 <td>Group Namek</td>' . "\r\n" . '                 <td>Account No.</td>' . "\r\n" . '                 <td>Account Name</td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </thead>' . "\r\n" . '                 <tbody>';
		$no = 0;

		while ($bas = mysql_fetch_object($rep)) {
			if ($_SESSION['language'] == 'EN') {
				$kol = 'namaakun1  as namaakun';
			}
			else {
				$kol = 'namaakun';
			}

			$op = 'select noakun,' . $kol . ' from ' . $dbname . '.keu_5akun where `noakun`=\'' . $bas->noakun . '\'';

			#exit(mysql_error($conn));
			($po = mysql_query($op)) || true;
			$pos = mysql_fetch_object($po);
			$no += 1;
			echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setGroup(\'' . $bas->kode . '\',\'' . $bas->kelompok . '\')" title=\'Click\' >' . "\r\n" . '                          <td class=firsttd>' . $no . '</td>' . "\r\n" . '                          <td>' . $bas->kode . '</td>' . "\r\n" . '                          <td>' . $bas->kelompok . '</td>' . "\r\n" . '                          <td>' . $pos->noakun . '</td>' . "\r\n" . '                          <td>' . $pos->namaakun . '</td>' . "\r\n" . '                         </tr>';
		}

		echo '</tbody>' . "\r\n" . '                  <tfoot>' . "\r\n" . '                  </tfoot>' . "\r\n" . '                  </table>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	$txtfind = $_POST['txtfind'];
	$str = ' select * from ' . $dbname . '.keu_5akun where namaakun like \'%' . $txtfind . '%\' or  namaakun1 like \'%' . $txtfind . '%\' or noakun like \'%' . $txtfind . '%\' or tipeakun like \'%' . $txtfind . '%\' limit 12';

	if ($res = mysql_query($str)) {
		echo '<table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '             <thead>' . "\r\n" . '                 <tr class=rowheader>' . "\r\n" . '                 <td class=firsttd>' . "\r\n" . '                 No.' . "\r\n" . '                 </td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['matauang'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </thead>' . "\r\n" . '                 <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;

			if ($_SESSION['language'] == 'EN') {
				$z = $bar->namaakun1;
			}
			else {
				$z = $bar->namaakun;
			}

			echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setNoakun(\'' . $bar->noakun . '\',\'' . $bar->namaakun . '\',\'' . $bar->tipeakun . '\',\'' . $bar->matauang . '\',\'' . $bar->kodeorg . '\')" title=\'Click\' >' . "\r\n" . '                      <td class=firsttd>' . $no . '</td>' . "\r\n" . '                      <td>' . $bar->noakun . '</td>' . "\r\n" . '                          <td>' . $z . '</td><td>' . $bar->tipeakun . '</td>' . "\r\n" . '                          <td>' . $bar->matauang . '</td><td>' . $bar->kodeorg . '</td>' . "\r\n" . '                         </tr>';
		}

		echo '</tbody>' . "\r\n" . '              <tfoot>' . "\r\n" . '                  </tfoot>' . "\r\n" . '                  </table>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
