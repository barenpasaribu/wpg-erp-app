<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
echo '<div>' . "\r\n" . '     <fieldset style=\'width:200px;color:#333399;\'>' . "\r\n" . '      Map user<b> \'' . $uname . '\'</b> Privileges <img src=images/info.png height=30px style=\'vertical-align:middle;cursor:pointer;\' title=\'Click for help..!\'>' . "\r\n\t" . ' </fieldset><br>' . "\r\n" . '     <input type=radio name=rad1 onclick=expandAllOrder()>Expand All' . "\r\n\t" . ' <input type=radio name=rad1 onclick=collapsAllOrder() checked>Collaps All' . "\r\n\t" . ' &nbsp &nbsp <a href=# onclick="resetDetailPrivillage(\'' . $uname . '\')" title=\'Clear All ' . $uname . ' privileges\'>Clear All</a>' . "\r\n\t" . ' <hr>' . "\r\n\t" . ' ';
echo '<ul>';
$_SESSION['upriv'] = '';
$stu = 'select * from ' . $dbname . '.auth where namauser=\'' . $uname . '\'' . "\r\n" . '         and status=1';
$reu = mysql_query($stu);
$z = 0;

while ($bau = mysql_fetch_object($reu)) {
	$_SESSION['upriv'][$z] = $bau->menuid;
	$z += 1;
}

$opt = '<option>0</option>';
$d = 1;

while ($d < 25) {
	$opt .= '<option>' . $d . '</option>';
	++$d;
}

$str = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '      where menu.type=\'master\' order by urut';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if ($_SESSION['standard']['access_level'] == 1) {
		$cx = '';
		$x = 0;

		while ($x < count($_SESSION['upriv'])) {
			if ($_SESSION['upriv'][$x] == $bar->id) {
				$cx = 'checked';
			}

			++$x;
		}

		echo '<li class=mmgr><img title=expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar->id . '\',this);>' . "\r\n\t" . '<a class=lab id=orderlab' . $bar->id . '>' . $bar->caption . '</a>' . "\r\n" . '    <input type=checkbox id=\'cx' . $bar->id . '\' value=\'' . $bar->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

		if ($bar->hide == 1) {
			echo ' <font color=#CC0000>(Inactive)</font>';
		}
		else {
			echo ' <font color=#009900>(Active)</font>';
		}

		$str1 = 'select menu.* from ' . $dbname . '.menu where parent=' . $bar->id . ' order by urut';
		$res1 = mysql_query($str1);
		echo '<ul id=orderchild' . $bar->id . ' style=\'display:none;\')>' . "\r\n\t\t\t" . '      <div id=ordergroup' . $bar->id . '>';

		while ($bar1 = mysql_fetch_object($res1)) {
			$cx = '';
			$x = 0;

			while ($x < count($_SESSION['upriv'])) {
				if ($_SESSION['upriv'][$x] == $bar1->id) {
					$cx = 'checked';
				}

				++$x;
			}

			if (strtolower($bar1->class) == 'devider') {
				$bar1->caption = '------------';
			}

			if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t" . '  <a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar1->id . '\',this);>' . "\r\n\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
			}

			echo '<input type=checkbox id=\'cx' . $bar1->id . '\' value=\'' . $bar1->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

			if ($bar1->hide == 1) {
				echo ' <font color=#CC0000>(Inactive)</font>';
			}
			else {
				echo ' <font color=#009900>(Active)</font>';
			}

			$str2 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t" . 'where parent=' . $bar1->id . ' order by urut';
			$res2 = mysql_query($str2);
			echo '<ul id=orderchild' . $bar1->id . ' style=\'display:none;\')>' . "\r\n\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar1->id . '>';

			while ($bar2 = mysql_fetch_object($res2)) {
				$cx = '';
				$x = 0;

				while ($x < count($_SESSION['upriv'])) {
					if ($_SESSION['upriv'][$x] == $bar2->id) {
						$cx = 'checked';
					}

					++$x;
				}

				if (strtolower($bar2->class) == 'devider') {
					$bar2->caption = '------------';
				}

				if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t" . '    <a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar2->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
				}

				echo '<input type=checkbox id=\'cx' . $bar2->id . '\' value=\'' . $bar2->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

				if ($bar2->hide == 1) {
					echo ' <font color=#CC0000>(Inactive)</font>';
				}
				else {
					echo ' <font color=#009900>(Active)</font>';
				}

				$str3 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar2->id . ' order by urut';
				$res3 = mysql_query($str3);
				echo '<ul id=orderchild' . $bar2->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar2->id . '>';

				while ($bar3 = mysql_fetch_object($res3)) {
					$cx = '';
					$x = 0;

					while ($x < count($_SESSION['upriv'])) {
						if ($_SESSION['upriv'][$x] == $bar3->id) {
							$cx = 'checked';
						}

						++$x;
					}

					if (strtolower($bar3->class) == 'devider') {
						$bar3->caption = '------------';
					}

					if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t" . '   <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar3->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . ' ' . "\t" . ' <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
					}

					echo '<input type=checkbox id=\'cx' . $bar3->id . '\' value=\'' . $bar3->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

					if ($bar3->hide == 1) {
						echo ' <font color=#CC0000>(Inactive)</font>';
					}
					else {
						echo ' <font color=#009900>(Active)</font>';
					}

					$str4 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar3->id . ' order by urut';
					$res4 = mysql_query($str4);
					echo '<ul id=orderchild' . $bar3->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar3->id . '>';

					while ($bar4 = mysql_fetch_object($res4)) {
						$cx = '';
						$x = 0;

						while ($x < count($_SESSION['upriv'])) {
							if ($_SESSION['upriv'][$x] == $bar4->id) {
								$cx = 'checked';
							}

							++$x;
						}

						if (strtolower($bar4->class) == 'devider') {
							$bar4->caption = '------------';
						}

						if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
							echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
						}
						else {
							echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar4->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
						}

						echo '<input type=checkbox id=\'cx' . $bar4->id . '\' value=\'' . $bar4->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

						if ($bar4->hide == 1) {
							echo ' <font color=#CC0000>(Inactive)</font>';
						}
						else {
							echo ' <font color=#009900>(Active)</font>';
						}

						$str5 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar4->id . ' order by urut';
						$res5 = mysql_query($str5);
						echo '<ul id=orderchild' . $bar4->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <div id=ordergroup' . $bar4->id . '>';

						while ($bar5 = mysql_fetch_object($res5)) {
							$cx = '';
							$x = 0;

							while ($x < count($_SESSION['upriv'])) {
								if ($_SESSION['upriv'][$x] == $bar5->id) {
									$cx = 'checked';
								}

								++$x;
							}

							if (strtolower($bar5->class) == 'devider') {
								$bar5->caption = '------------';
							}

							if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
								echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
							}
							else {
								echo '<li class=mmgr><img class=arrow title=\'Expand\'  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar5->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
							}

							echo '<input type=checkbox id=\'cx' . $bar5->id . '\' value=\'' . $bar5->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

							if ($bar5->hide == 1) {
								echo ' <font color=#CC0000>(Inactive)</font>';
							}
							else {
								echo ' <font color=#009900>(Active)</font>';
							}

							$str6 = 'select menu.*  from ' . $dbname . '.menu' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar5->id . ' order by urut';
							$res6 = mysql_query($str6);
							echo '<ul id=orderchild' . $bar5->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar5->id . '>';

							while ($bar6 = mysql_fetch_object($res6)) {
								$cx = '';
								$x = 0;

								while ($x < count($_SESSION['upriv'])) {
									if ($_SESSION['upriv'][$x] == $bar6->id) {
										$cx = 'checked';
									}

									++$x;
								}

								if (strtolower($bar6->class) == 'devider') {
									$bar6->caption = '------------';
								}

								echo '<li><a class=lab id=orderlab' . $bar6->id . '>' . $bar6->caption . '</a>';
								echo '<input type=checkbox id=\'cx' . $bar6->id . '\' value=\'' . $bar6->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

								if ($bar->hide == 1) {
									echo ' <font color=#CC0000>(Inactive)</font>';
								}
								else {
									echo ' <font color=#009900>(Active)</font>';
								}

								echo ' </li>';
							}

							echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' </ul>';
							echo '</li>';
						}

						echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
						echo '</li>';
					}

					echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
					echo '</li>';
				}

				echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t" . '      </ul>';
				echo '</li>';
			}

			echo '</div>' . "\r\n\t\t\t\t\t\t" . '      </ul>';
			echo '</li>';
		}

		echo '</div>' . "\t\t\t" . ' ' . "\r\n\t\t\t" . '      </ul>';
		echo '</li>';
	}
	else {
		$cx = '';
		$x = 0;

		while ($x < count($_SESSION['upriv'])) {
			if ($_SESSION['upriv'][$x] == $bar->id) {
				$cx = 'checked';
			}

			++$x;
		}

		echo '<li class=mmgr><img title=expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar->id . '\',this);>' . "\r\n\t" . '<a class=lab id=orderlab' . $bar->id . '>' . $bar->caption . '</a>' . "\r\n" . '    <input type=checkbox id=\'cx' . $bar->id . '\' value=\'' . $bar->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

		if ($bar->hide == 1) {
			echo ' <font color=#CC0000>(Inactive)</font>';
		}
		else {
			echo ' <font color=#009900>(Active)</font>';
		}

		$str1 = 'select menu.* from ' . $dbname . '.menu where parent=' . $bar->id . ' and id NOT IN (13,1137,949,918) order by urut';
		$res1 = mysql_query($str1);
		echo '<ul id=orderchild' . $bar->id . ' style=\'display:none;\')>' . "\r\n\t\t\t" . '      <div id=ordergroup' . $bar->id . '>';

		while ($bar1 = mysql_fetch_object($res1)) {
			$cx = '';
			$x = 0;

			while ($x < count($_SESSION['upriv'])) {
				if ($_SESSION['upriv'][$x] == $bar1->id) {
					$cx = 'checked';
				}

				++$x;
			}

			if (strtolower($bar1->class) == 'devider') {
				$bar1->caption = '------------';
			}

			if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t" . '  <a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar1->id . '\',this);>' . "\r\n\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
			}

			echo '<input type=checkbox id=\'cx' . $bar1->id . '\' value=\'' . $bar1->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

			if ($bar1->hide == 1) {
				echo ' <font color=#CC0000>(Inactive)</font>';
			}
			else {
				echo ' <font color=#009900>(Active)</font>';
			}

			$str2 = 'select menu.* from ' . $dbname . '.menu where parent=' . $bar1->id . ' and id NOT IN (967,1630,515,1631,53,1632,52,1633,917,266,1204,1013,1203,1202,718,1197,1048,1195,971,1194,956,1300,954,1301,955,1302,951,1303,961,1044) order by urut';
			$res2 = mysql_query($str2);
			echo '<ul id=orderchild' . $bar1->id . ' style=\'display:none;\')>' . "\r\n\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar1->id . '>';

			while ($bar2 = mysql_fetch_object($res2)) {
				$cx = '';
				$x = 0;

				while ($x < count($_SESSION['upriv'])) {
					if ($_SESSION['upriv'][$x] == $bar2->id) {
						$cx = 'checked';
					}

					++$x;
				}

				if (strtolower($bar2->class) == 'devider') {
					$bar2->caption = '------------';
				}

				if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t" . '    <a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar2->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
				}

				echo '<input type=checkbox id=\'cx' . $bar2->id . '\' value=\'' . $bar2->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

				if ($bar2->hide == 1) {
					echo ' <font color=#CC0000>(Inactive)</font>';
				}
				else {
					echo ' <font color=#009900>(Active)</font>';
				}

				$str3 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar2->id . ' order by urut';
				$res3 = mysql_query($str3);
				echo '<ul id=orderchild' . $bar2->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar2->id . '>';

				while ($bar3 = mysql_fetch_object($res3)) {
					$cx = '';
					$x = 0;

					while ($x < count($_SESSION['upriv'])) {
						if ($_SESSION['upriv'][$x] == $bar3->id) {
							$cx = 'checked';
						}

						++$x;
					}

					if (strtolower($bar3->class) == 'devider') {
						$bar3->caption = '------------';
					}

					if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t" . '   <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar3->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t" . ' ' . "\t" . ' <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
					}

					echo '<input type=checkbox id=\'cx' . $bar3->id . '\' value=\'' . $bar3->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

					if ($bar3->hide == 1) {
						echo ' <font color=#CC0000>(Inactive)</font>';
					}
					else {
						echo ' <font color=#009900>(Active)</font>';
					}

					$str4 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar3->id . ' order by urut';
					$res4 = mysql_query($str4);
					echo '<ul id=orderchild' . $bar3->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar3->id . '>';

					while ($bar4 = mysql_fetch_object($res4)) {
						$cx = '';
						$x = 0;

						while ($x < count($_SESSION['upriv'])) {
							if ($_SESSION['upriv'][$x] == $bar4->id) {
								$cx = 'checked';
							}

							++$x;
						}

						if (strtolower($bar4->class) == 'devider') {
							$bar4->caption = '------------';
						}

						if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
							echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
						}
						else {
							echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar4->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
						}

						echo '<input type=checkbox id=\'cx' . $bar4->id . '\' value=\'' . $bar4->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

						if ($bar4->hide == 1) {
							echo ' <font color=#CC0000>(Inactive)</font>';
						}
						else {
							echo ' <font color=#009900>(Active)</font>';
						}

						$str5 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar4->id . ' order by urut';
						$res5 = mysql_query($str5);
						echo '<ul id=orderchild' . $bar4->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <div id=ordergroup' . $bar4->id . '>';

						while ($bar5 = mysql_fetch_object($res5)) {
							$cx = '';
							$x = 0;

							while ($x < count($_SESSION['upriv'])) {
								if ($_SESSION['upriv'][$x] == $bar5->id) {
									$cx = 'checked';
								}

								++$x;
							}

							if (strtolower($bar5->class) == 'devider') {
								$bar5->caption = '------------';
							}

							if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
								echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
							}
							else {
								echo '<li class=mmgr><img class=arrow title=\'Expand\'  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar5->id . '\',this);>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
							}

							echo '<input type=checkbox id=\'cx' . $bar5->id . '\' value=\'' . $bar5->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

							if ($bar5->hide == 1) {
								echo ' <font color=#CC0000>(Inactive)</font>';
							}
							else {
								echo ' <font color=#009900>(Active)</font>';
							}

							$str6 = 'select menu.*  from ' . $dbname . '.menu' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . 'where parent=' . $bar5->id . ' order by urut';
							$res6 = mysql_query($str6);
							echo '<ul id=orderchild' . $bar5->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar5->id . '>';

							while ($bar6 = mysql_fetch_object($res6)) {
								$cx = '';
								$x = 0;

								while ($x < count($_SESSION['upriv'])) {
									if ($_SESSION['upriv'][$x] == $bar6->id) {
										$cx = 'checked';
									}

									++$x;
								}

								if (strtolower($bar6->class) == 'devider') {
									$bar6->caption = '------------';
								}

								echo '<li><a class=lab id=orderlab' . $bar6->id . '>' . $bar6->caption . '</a>';
								echo '<input type=checkbox id=\'cx' . $bar6->id . '\' value=\'' . $bar6->id . '\' onclick=changePrivillage(this.value,\'' . $uname . '\',this) title=\'user:' . $uname . '\'  ' . $cx . '>';

								if ($bar->hide == 1) {
									echo ' <font color=#CC0000>(Inactive)</font>';
								}
								else {
									echo ' <font color=#009900>(Active)</font>';
								}

								echo ' </li>';
							}

							echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' </ul>';
							echo '</li>';
						}

						echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
						echo '</li>';
					}

					echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
					echo '</li>';
				}

				echo '</div>' . "\r\n\t\t\t\t\t\t\t\t\t" . '      </ul>';
				echo '</li>';
			}

			echo '</div>' . "\r\n\t\t\t\t\t\t" . '      </ul>';
			echo '</li>';
		}

		echo '</div>' . "\t\t\t" . ' ' . "\r\n\t\t\t" . '      </ul>';
		echo '</li>';
	}
}

echo '</ul></div><br>' . "\r\n" . '<input type=button value=Done class=mybutton onclick=showById(\'ctrmenu\',\'ctr\')>' . "\r\n" . '<br><br>';

?>
