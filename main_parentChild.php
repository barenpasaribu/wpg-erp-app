<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/menusetting.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css HREF=style/parentChild.css>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['parentchildmenu'] . ':');
$max_id = 0;
$strx = 'select max(id) as mx from ' . $dbname . '.menu';
$resx = mysql_query($strx);

while ($barx = mysql_fetch_array($resx)) {
	$max_id = $barx[0];
}

echo '<script langguage=javascript1.2>' . "\r\n" . '     max_id=' . $max_id . ';' . "\r\n\t" . ' </script>';
echo '<div id=menuOrderContainer><br>' . "\r\n" . '     <fieldset id=comment>' . "\r\n\t" . ' <img src=images/info.png align=left height=35px valign=asmiddle><br>' . "\r\n\t" . ' ' . $_SESSION['lang']['youcanchangemenu'] . '</fieldset>' . "\r\n" . '     <input type=radio name=rad1 onclick=expandAllOrder()>' . $_SESSION['lang']['expandall'] . "\r\n\t" . ' <input type=radio name=rad1 onclick=collapsAllOrder() checked>' . $_SESSION['lang']['colapsall'] . "\r\n\t" . ' <hr><div id=legend>Step #1: <b>Choose Parent..!</b>:</div>' . "\r\n\t";
echo '<ul>' . "\r\n" . '     <div id=ordergroup0>';
$str = 'select * from ' . $dbname . '.menu ' . "\r\n" . '      where type=\'master\' order by urut';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	echo '<li class=mmgr><img title=expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar->id . '\',this);>' . "\r\n\t" . '<a class=lab  title=\'Click to Choose\' id=orderlab' . $bar->id . ' onclick="setThis(\'' . $bar->id . '\',\'' . $bar->caption . '\',event,\'' . $bar->type . '\')">' . $bar->caption . '</a>';
	$str1 = 'select * from ' . $dbname . '.menu ' . "\r\n" . '                where parent=' . $bar->id . ' order by urut';
	$res1 = mysql_query($str1);
	echo '<ul id=orderchild' . $bar->id . ' style=\'display:none;\')>' . "\r\n\t\t\t" . '      <div id=ordergroup' . $bar->id . '>';

	while ($bar1 = mysql_fetch_object($res1)) {
		if (strtolower($bar1->class) == 'devider') {
			$bar1->caption = '------------';
		}

		if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
			echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t" . '  ' . $bar1->caption;
		}
		else {
			echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar1->id . '\',this);> ' . "\r\n\t\t\t\t" . '   <a class=lab title=\'Click to Choose\' id=orderlab' . $bar1->id . ' onclick="setThis(\'' . $bar1->id . '\',\'' . $bar1->caption . '\',event,\'' . $bar1->type . '\')">' . $bar1->caption . '</a>';
		}

		$str2 = 'select * from ' . $dbname . '.menu ' . "\r\n\t\t\t" . '                where parent=' . $bar1->id . ' order by urut';
		$res2 = mysql_query($str2);
		echo '<ul id=orderchild' . $bar1->id . ' style=\'display:none;\')>' . "\r\n\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar1->id . '>';

		while ($bar2 = mysql_fetch_object($res2)) {
			if (strtolower($bar2->class) == 'devider') {
				$bar2->caption = '------------';
			}

			if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t" . '    ' . $bar2->caption;
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar2->id . '\',this);> ' . "\r\n\t\t\t\t\t\t\t\t" . ' <a class=lab title=\'Click to Choose\' id=orderlab' . $bar2->id . ' onclick="setThis(\'' . $bar2->id . '\',\'' . $bar2->caption . '\',event,\'' . $bar2->type . '\')">' . $bar2->caption . '</a>';
			}

			$str3 = 'select * from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t" . '                where parent=' . $bar2->id . ' order by urut';
			$res3 = mysql_query($str3);
			echo '<ul id=orderchild' . $bar2->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar2->id . '>';

			while ($bar3 = mysql_fetch_object($res3)) {
				if (strtolower($bar3->class) == 'devider') {
					$bar3->caption = '------------';
				}

				if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t" . '   ' . $bar3->caption;
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar3->id . '\',this);> ' . "\r\n\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab title=\'Click to Choose\' id=orderlab' . $bar3->id . ' onclick="setThis(\'' . $bar3->id . '\',\'' . $bar3->caption . '\',event,\'' . $bar3->type . '\')">' . $bar3->caption . '</a>';
				}

				$str4 = 'select * from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar3->id . ' order by urut';
				$res4 = mysql_query($str4);
				echo '<ul id=orderchild' . $bar3->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar3->id . '>';

				while ($bar4 = mysql_fetch_object($res4)) {
					if (strtolower($bar4->class) == 'devider') {
						$bar4->caption = '------------';
					}

					if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' ' . $bar4->caption;
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar4->id . '\',this);> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <a class=lab title=\'Click to Choose\' id=orderlab' . $bar4->id . ' onclick="setThis(\'' . $bar4->id . '\',\'' . $bar4->caption . '\',event,\'' . $bar4->type . '\')">' . $bar4->caption . '</a>';
					}

					$str5 = 'select * from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar4->id . ' order by urut';
					$res5 = mysql_query($str5);
					echo '<ul id=orderchild' . $bar4->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <div id=ordergroup' . $bar4->id . '>';

					while ($bar5 = mysql_fetch_object($res5)) {
						if (strtolower($bar5->class) == 'devider') {
							$bar5->caption = '------------';
						}

						if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
							echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' ' . $bar5->caption;
						}
						else {
							echo '<li class=mmgr><img class=arrow title=\'Expand\' src=\'images/foldc_.png\' height=17px onclick=show_sub(\'orderchild' . $bar5->id . '\',this);> ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '   <a class=lab title=\'Click to Choose\' id=orderlab' . $bar5->id . ' onclick="setThis(\'' . $bar5->id . '\',\'' . $bar5->caption . '\',event,\'' . $bar5->type . '\')">' . $bar5->caption . '</a>';
						}

						$str6 = 'select * from ' . $dbname . '.menu ' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar5->id . ' order by urut';
						$res6 = mysql_query($str6);
						echo '<ul id=orderchild' . $bar5->id . ' style=\'display:none;\'>' . "\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar5->id . '>';

						while ($bar6 = mysql_fetch_object($res6)) {
							if (strtolower($bar6->class) == 'devider') {
								$bar6->caption = '------------';
							}

							echo '<li>' . $bar6->caption . '</li>';
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

echo '</ul></div>';
echo '</div>';
echo CLOSE_THEME();
echo '<div id=exchange>';
echo OPEN_THEME('Bracket:') . '<img src=images/info.png align=left height=35px  valign=asmiddle><br>' . $_SESSION['lang']['therestchild'] . '<br><br>';
echo '<fieldset>' . "\r\n\r\n\t" . '  <table>' . "\r\n" . '      <tr><td>Parent</td><td width=200px>:<input type=hidden id=idparent value=\'\'> <span id=parent></span></td></tr>' . "\r\n\t" . '  <tr><td>new Child</td><td width=200px>:<input type=hidden id=idchild value=\'\'><span id=child></span></td></tr>' . "\r\n" . '      <tr><td colspan=2 align=center><br>' . "\r\n\t" . '  <input type=button class=mybutton value=\'' . $_SESSION['lang']['cancel'] . '\' onclick=clearBracket()> ' . "\r\n\t" . '  <input type=button class=mybutton value=\'' . $_SESSION['lang']['apply'] . '\' onclick=saveSetting() id=nav style=\'display:none;\'></td></tr>' . "\r\n\t" . '  </table></fieldset>';
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
