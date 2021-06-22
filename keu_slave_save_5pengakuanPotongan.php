<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$potongan = $_POST['potongan'];
$debet = $_POST['debet'];
$kredit = $_POST['kredit'];
$method = $_POST['method'];
$str = 'select noakun,namaakun from ' . $dbname . '.keu_5akun where length(noakun)=7 order by namaakun';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$arrAkun[$bar->noakun] = $bar->namaakun;
}

switch ($method) {
case 'update':
	$str = 'update ' . $dbname . '.keu_5pengakuanpotongan set noakundebet=\'' . $debet . '\',noakunkredit=\'' . $kredit . '\',updateby=' . $_SESSION['standard']['userid'] . "\r\n" . '               where idkomponen=' . $potongan;

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'insert':
	$str = 'insert into ' . $dbname . '.keu_5pengakuanpotongan' . "\r\n" . '                  (idkomponen,noakundebet,noakunkredit,updateby)' . "\r\n" . '                  values(\'' . $potongan . '\',\'' . $debet . '\',\'' . $kredit . '\',' . $_SESSION['standard']['userid'] . ')';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.keu_5pengakuanpotongan' . "\r\n\t" . 'where idkomponen=' . $potongan;

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	$str1 = 'select a.*,b.name from ' . $dbname . '.keu_5pengakuanpotongan a' . "\r\n" . '                   left join ' . $dbname . '.sdm_ho_component b on a.idkomponen=b.id' . "\r\n" . '                    order by idkomponen';

	if ($res1 = mysql_query($str1)) {
		while ($bar1 = mysql_fetch_object($res1)) {
			echo '<tr class=rowcontent>' . "\r\n" . '                        <td align=center>' . $bar1->idkomponen . '</td>' . "\r\n" . '                        <td>' . $bar1->name . '</td>' . "\r\n" . '                        <td>' . $bar1->noakundebet . ':' . $arrAkun[$bar1->noakundebet] . '</td>' . "\r\n" . '                        <td>' . $bar1->noakunkredit . ':' . $arrAkun[$bar1->noakunkredit] . '</td>                             ' . "\r\n" . '                         <td align=center><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->idkomponen . '\',\'' . $bar1->noakundebet . '\',\'' . $bar1->noakunkredit . '\');"></td>' . "\r\n" . '                         <td align=center><img src=images/application/application_delete.png class=resicon  caption=\'Delete\' onclick="delField(\'' . $bar1->idkomponen . '\');"></td>    ' . "\r\n" . '                      </tr>';
		}
	}

	break;
}

?>
