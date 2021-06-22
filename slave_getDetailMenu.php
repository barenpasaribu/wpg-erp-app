<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
$uname = $_POST['uname'];
echo '<div>' . "\r\n" . '     <fieldset style=\'width:200px;color:#333399;\'>' . "\r\n" . '       A Menu for global Acess <img src=images/info.png height=30px style=\'vertical-align:middle;cursor:pointer;\' title=\'Click for help..!\'>' . "\r\n" . '         </fieldset><br>' . "\r\n" . '         <hr>' . "\r\n" . '         ';
echo '<ul>';
$str = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '      where menu.type=\'master\' order by urut';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	echo '<li class=mmgr><img title=expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar->id . '\',this);>' . "\r\n" . '        <a class=lab id=orderlab' . $bar->id . '>' . $bar->caption . '</a>';

	if ($bar->hide == 1) {
		echo ' <font color=#CC0000>(Inactive)</font>';
	}
	else {
		echo ' <font color=#009900>(Active)</font>';
	}

	$str1 = 'select menu.* from ' . $dbname . '.menu' . "\r\n" . '                where parent=' . $bar->id . ' order by urut';
	$res1 = mysql_query($str1);
	echo '<ul id=orderchild' . $bar->id . ' style=\'display:none;\')>' . "\r\n" . '                              <div id=ordergroup' . $bar->id . '>';

	while ($bar1 = mysql_fetch_object($res1)) {
		if (strtolower($bar1->class) == 'devider') {
			$bar1->caption = '------------';
		}

		if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
			echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n" . '                                  <a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
		}
		else {
			echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar1->id . '\',this);>' . "\r\n" . '                                                <a class=lab id=orderlab' . $bar1->id . '>' . $bar1->caption . '</a>';
		}

		echo '<input type=checkbox id=\'cx' . $bar1->id . '\' value=\'' . $bar1->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

		if ($bar1->hide == 1) {
			echo ' <font color=#CC0000>(Inactive)</font>';
		}
		else {
			echo ' <font color=#009900>(Active)</font>';
		}

		$str2 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '                                                        where parent=' . $bar1->id . ' order by urut';
		$res2 = mysql_query($str2);
		echo '<ul id=orderchild' . $bar1->id . ' style=\'display:none;\')>' . "\r\n" . '                                                      <div id=ordergroup' . $bar1->id . '>';

		while ($bar2 = mysql_fetch_object($res2)) {
			if (strtolower($bar2->class) == 'devider') {
				$bar2->caption = '------------';
			}

			if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n" . '                                                            <a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar2->id . '\',this);>' . "\r\n" . '                                                                        <a class=lab id=orderlab' . $bar2->id . '>' . $bar2->caption . '</a>';
			}

			echo '<input type=checkbox id=\'cx' . $bar2->id . '\' value=\'' . $bar2->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

			if ($bar2->hide == 1) {
				echo ' <font color=#CC0000>(Inactive)</font>';
			}
			else {
				echo ' <font color=#009900>(Active)</font>';
			}

			$str3 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '                                                                                where parent=' . $bar2->id . ' order by urut';
			$res3 = mysql_query($str3);
			echo '<ul id=orderchild' . $bar2->id . ' style=\'display:none;\'>' . "\r\n" . '                                                                              <div id=ordergroup' . $bar2->id . '>';

			while ($bar3 = mysql_fetch_object($res3)) {
				if (strtolower($bar3->class) == 'devider') {
					$bar3->caption = '------------';
				}

				if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n" . '                                                                           <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar3->id . '\',this);>' . "\r\n" . '                                                                                         <a class=lab id=orderlab' . $bar3->id . '>' . $bar3->caption . '</a>';
				}

				echo '<input type=checkbox id=\'cx' . $bar3->id . '\' value=\'' . $bar3->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

				if ($bar3->hide == 1) {
					echo ' <font color=#CC0000>(Inactive)</font>';
				}
				else {
					echo ' <font color=#009900>(Active)</font>';
				}

				$str4 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '                                                                                                        where parent=' . $bar3->id . ' order by urut';
				$res4 = mysql_query($str4);
				echo '<ul id=orderchild' . $bar3->id . ' style=\'display:none;\'>' . "\r\n" . '                                                                                                      <div id=ordergroup' . $bar3->id . '>';

				while ($bar4 = mysql_fetch_object($res4)) {
					if (strtolower($bar4->class) == 'devider') {
						$bar4->caption = '------------';
					}

					if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n" . '                                                                                                         <a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar4->id . '\',this);>' . "\r\n" . '                                                                                                                <a class=lab id=orderlab' . $bar4->id . '>' . $bar4->caption . '</a>';
					}

					echo '<input type=checkbox id=\'cx' . $bar4->id . '\' value=\'' . $bar4->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

					if ($bar4->hide == 1) {
						echo ' <font color=#CC0000>(Inactive)</font>';
					}
					else {
						echo ' <font color=#009900>(Active)</font>';
					}

					$str5 = 'select menu.* from ' . $dbname . '.menu ' . "\r\n" . '                                                                                                                                where parent=' . $bar4->id . ' order by urut';
					$res5 = mysql_query($str5);
					echo '<ul id=orderchild' . $bar4->id . ' style=\'display:none;\'>' . "\r\n" . '                                                                                                                                  <div id=ordergroup' . $bar4->id . '>';

					while ($bar5 = mysql_fetch_object($res5)) {
						if (strtolower($bar5->class) == 'devider') {
							$bar5->caption = '------------';
						}

						if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
							echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\r\n" . '                                                                                                                                 <a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
						}
						else {
							echo '<li class=mmgr><img class=arrow title=\'Expand\'  src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar5->id . '\',this);>' . "\r\n" . '                                                                                                                                        <a class=lab id=orderlab' . $bar5->id . '>' . $bar5->caption . '</a>';
						}

						echo '<input type=checkbox id=\'cx' . $bar5->id . '\' value=\'' . $bar5->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

						if ($bar5->hide == 1) {
							echo ' <font color=#CC0000>(Inactive)</font>';
						}
						else {
							echo ' <font color=#009900>(Active)</font>';
						}

						$str6 = 'select menu.*  from ' . $dbname . '.menu' . "\r\n" . '                                                                                                                                                        where parent=' . $bar5->id . ' order by urut';
						$res6 = mysql_query($str6);
						echo '<ul id=orderchild' . $bar5->id . ' style=\'display:none;\'>' . "\r\n" . '                                                                                                                                                      <div id=ordergroup' . $bar5->id . '>';

						while ($bar6 = mysql_fetch_object($res6)) {
							if (strtolower($bar6->class) == 'devider') {
								$bar6->caption = '------------';
							}

							echo '<li><a class=lab id=orderlab' . $bar6->id . '>' . $bar6->caption . '</a>';
							echo '<input type=checkbox id=\'cx' . $bar6->id . '\' value=\'' . $bar6->id . '\' onclick=changeToGlobal(this.value,this) title=\'Set accsess for all users\'>';

							if ($bar->hide == 1) {
								echo ' <font color=#CC0000>(Inactive)</font>';
							}
							else {
								echo ' <font color=#009900>(Active)</font>';
							}

							echo ' </li>';
						}

						echo '</div>' . "\r\n" . '                                                                                                                                                 </ul>';
						echo '</li>';
					}

					echo '</div>' . "\r\n" . '                                                                                                                          </ul>';
					echo '</li>';
				}

				echo '</div>' . "\r\n" . '                                                                                                          </ul>';
				echo '</li>';
			}

			echo '</div>' . "\r\n" . '                                                                              </ul>';
			echo '</li>';
		}

		echo '</div>' . "\r\n" . '                                                      </ul>';
		echo '</li>';
	}

	echo '</div>' . "\t\t\t" . ' ' . "\r\n" . '                              </ul>';
	echo '</li>';
}

echo '</ul></div><br>' . "\r\n" . '<input type=button value=Done class=mybutton onclick=closeThis()>' . "\r\n" . '<br><br>';

?>
