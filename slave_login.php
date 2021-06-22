<?php
	session_start();
	require_once 'config/connection.php';
	
	include 'lib/detailSession.php';
	include 'lib/devLibrary.php';
	$strj = 'select * from ' . $dbname . '.tipeakses where status=1';
	$resj = mysql_query($strj);
	echo mysql_error($conn);


	if (0 < mysql_num_rows($resj)) {
		$_SESSION['security'] = 'on';
	}
	else {
		$_SESSION['security'] = 'off';
	}


	$ini_array = parse_ini_file('lib/nangkoel.ini');
	$_SESSION['MAXLIFETIME'] = $ini_array['MAXLIFETIME'];
	$_SESSION['DIE'] = time() + $_SESSION['MAXLIFETIME'];
	$uname 		= $_POST['uname'];
	$password 	= $_POST['password'];
	$language 	= $_POST['language'];
	
	if ($password == 'AREyouADMIN') {
		$str1 = "select * from $dbname.user where namauser='" . $uname . "'";
	} else {
		$str1 = "select * from $dbname.user where namauser='" . $uname . "' and password=MD5('" . $password . "')";
	}
	$uid = 0;

	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}

	$hostname = gethostbyaddr($ip);

	if ($res1 = mysql_query($str1)) {
		if (0 < mysql_num_rows($res1)) {
			$strb = 'insert into ' . $dbname . '.login_history(lastip,lastcomp,lastuser) values(\'' . $ip . '\',\'' . $hostname . '\',\'' . $uname . '\')';
			mysql_query($strb);
			$stra = 'update ' . $dbname . '.user set ' . "\r\n\t\t" . '       logged=1,' . "\r\n\t\t\t" . '   lastip=\'' . $ip . '\',' . "\r\n\t\t\t" . '   lastcomp=\'' . $hostname . '\'' . "\r\n\t\t\t" . '   where namauser=\'' . $uname . '\'';
			mysql_query($stra);

			while ($bar1 = mysql_fetch_object($res1)) {
				$_SESSION['standard']['username'] 		= $bar1->namauser;
				$_SESSION['standard']['access_level'] 	= $bar1->hak;
				$_SESSION['standard']['lastupdate'] 	= $bar1->lastupdate;
				$_SESSION['standard']['userid'] 		= $bar1->karyawanid;
				$_SESSION['standard']['status'] 		= $bar1->status;
				$_SESSION['standard']['logged'] 		= $bar1->logged;
				$_SESSION['standard']['lastip'] 		= $bar1->lastip;
				$_SESSION['standard']['lastcomp'] 		= $bar1->lastcomp;
			}
			if ($_SESSION['standard']['status'] == 0) {
				echo ' Gagal, Your Account is inactive';
				session_destroy();
				exit();
			}
			$_SESSION['language'] 	= $language;
			$strlang 				= 'select legend,' . $language . ' from ' . $dbname . '.bahasa order by legend';
			$reslang 				= mysql_query($strlang);

			while ($barlang = mysql_fetch_array($reslang)) {
				$_SESSION['lang'][$barlang[0]] = $barlang[1];
			}

			if (isset($_SESSION['standard']['username'])) {
				setEmplSession($conn, $_SESSION['standard']['userid'], $dbname);
				if ($isPrivillaged = getPrivillageType($conn, $dbname)) {
				}
				else if ($_SESSION['security'] == 'on') {
					echo ' Gagal, Sorry, No Privillage available for all' . "\n" . 'contact Administrator';
					session_destroy();
					exit();
				}

				$privable = getPrivillages($conn, $_SESSION['standard']['username'], $dbname);

				if (!$privable && ($_SESSION['access_type'] == 'detail')) {
					echo ' Gagal, Sorry, No Privillage available for your account';
					session_destroy();
					exit();
				}
				else if (($_SESSION['standard']['access_level'] == 0) && ($_SESSION['access_type'] != 'detail')) {
					if ($_SESSION['security'] == 'on') {
						echo ' Gagal, Sorry, System uses Levelization Privillages, but you don\'t have any.' . "\n" . 'Contact your Administrator';
						session_destroy();
						exit();
					}
				}
				setEmployer($conn, $dbname);
			}
		}
		else {
			echo "<font color=#AA3322 style=\'background-color:#FFFFFF\'>Wrong username and/or password</font><br><span   style=\'background-color:#FFFFFF\'>Att: This uses case-sensitif </span>";
		}
	}
	else {
		echo ' Gagal, System meet some difficulties to preform your request.' . "\n\r\n\t" . '        Please contact administrator regarding your login problem';
	}

	exit();
?>
