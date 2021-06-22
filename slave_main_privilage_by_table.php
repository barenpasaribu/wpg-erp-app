<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$parent = $_POST['parent'];
$sub = $_POST['sub'];
$_POST['parent'] == '' ? $menu = $_POST['sub'] : $menu = $_POST['parent'];
$proses = $_GET['proses'];
$menuId = $_POST['id_menu'];
$usrname = $_POST['usernm'];
$stat = $_POST['stat'];

switch ($proses) {
case 'getForm':
	if ($sub == 'true') {
		$str = 'select * from ' . $dbname . '.menu ' . "\r\n\t" . '      where parent=' . $parent . ' order by urut';
	}
	else {
		$str = 'select * from ' . $dbname . '.menu ' . "\r\n\t" . '      where type=\'master\' order by urut';
	}

	$res = mysql_query($str);
	echo '<input type=hidden id=id_menu value=\'' . $menu . '\' />' . "\r\n" . '             <input type=button class=mybutton value=\'' . $_SESSION['lang']['close'] . '\' onclick=closeOrderEditor()>';
	echo '<div style=overflow:auto;scroll;height:550px;>' . "\r\n" . '                     <table width=100% cellspacing=1 border=0 class=data>' . "\r\n" . '             <thead>' . "\r\n\t\t" . '     <tr>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['action'] . '</td>' . "\r\n\t\t\t" . ' <td id=usr_nm_' . $no . '>' . $_SESSION['lang']['username'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['lokasitugas'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $_SESSION['lang']['jabatan'] . '</td>' . "\r\n\t\t\t" . ' </tr>' . "\r\n\t\t\t" . ' </thead><tbody>';
	$sData = 'select distinct a.karyawanid,namauser,b.namakaryawan,b.lokasitugas,b.kodejabatan from ' . $dbname . '.user a' . "\r\n" . '                        left join ' . $dbname . '.datakaryawan b on a.karyawanid=b.karyawanid where status!=0';

	#exit(mysql_error($conn));
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		++$no;
		$sJbtn = 'select namajabatan from ' . $dbname . '.sdm_5jabatan where kodejabatan=\'' . $rData['kodejabatan'] . '\'';

		#exit(mysql_error($conn));
		($qJbtn = mysql_query($sJbtn)) || true;
		$rJbtn = mysql_fetch_assoc($qJbtn);
		$arrd = '';
		$sAuth = 'select distinct * from ' . $dbname . '.auth ' . "\r\n" . '                            where namauser=\'' . $rData['namauser'] . '\' and menuid=\'' . $menu . '\' and status=1';

		#exit(mysql_error($conn));
		($qAuth = mysql_query($sAuth)) || true;
		$rAuth = mysql_num_rows($qAuth);

		if ($rAuth == 1) {
			$arrd = 'checked';
		}

		echo '<tr class=rowcontent>' . "\r\n\t\t\t" . ' <td><input type=checkbox id=adddt_' . $no . ' onclick=addData(' . $no . ',' . $menu . ') ' . $arrd . ' /></td>' . "\r\n\t\t\t" . ' <td id=usr_nm_' . $no . '>' . $rData['namauser'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $rData['namakaryawan'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $rData['lokasitugas'] . '</td>' . "\r\n\t\t\t" . ' <td>' . $rJbtn['namajabatan'] . '</td>' . "\r\n\t\t\t" . ' </tr>';
	}

	echo '</tbody></table><br><table><tr><td>&nbsp;</td></tr></table></div>';
	break;

case 'addData':
	if ($stat == 1) {
		$menu[] = $menuId;
		$x = 0;

		while ($x <= 7) {
			if ($menuId != '') {
				$str = 'select parent from ' . $dbname . '.menu where id=' . $menuId;
				$res = mysql_query($str);

				while ($bar = mysql_fetch_object($res)) {
					if ($bar->parent != 0) {
						$menu[] = $bar->parent;
						$menuId = $bar->parent;
					}
				}
			}

			++$x;
		}

		foreach ($menu as $key => $val) {
			$str = 'delete from ' . $dbname . '.auth where menuid=' . $val . ' and namauser=\'' . $usrname . '\'';
			mysql_query($str);
			$str = 'insert into ' . $dbname . '.auth(namauser, menuid, status, lastuser, detail)' . "\r\n" . '                                   values(\'' . $usrname . '\',' . $val . ',1,' . $_SESSION['standard']['userid'] . ',0)';
			mysql_query($str);
		}
	}
	else {
		$sDel = 'delete from ' . $dbname . '.auth where namauser=\'' . $usrname . '\' and menuid=\'' . $menuId . '\'';

		#exit(mysql_error($conn));
		mysql_query($sDel) || true;
	}

	break;
}

?>
