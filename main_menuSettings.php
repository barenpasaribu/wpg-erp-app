<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/menusetting.js></script>' . "\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['menusettings'] . ':');
echo ' <div id=menuSettingContainer>' . "\n" . '     <input type=radio name=rad onclick=expandAll()>' . $_SESSION['lang']['expandall'] . "\n\t" . ' <input type=radio name=rad onclick=collapsAll() checked>' . $_SESSION['lang']['colapsall'] . "\n\t" . ' <hr><b>' . $_SESSION['lang']['menusettings'] . '</b>' . "\n\t";
$max_id = 0;
$strx = 'select max(id) as mx from ' . $dbname . '.menu';
$resx = mysql_query($strx);

while ($barx = mysql_fetch_array($resx)) {
	$max_id = $barx[0];
}

echo '<script langguage=javascript1.2>' . "\n" . '     max_id=' . $max_id . ';' . "\n\t" . ' </script>';
$str = 'select * from ' . $dbname . '.menu where type=\'master\' order by urut';
$res = mysql_query($str);
echo '<ul>' . "\n" . '     <div id=group0>';

while ($bar = mysql_fetch_object($res)) {
	echo '<li class=mmgr><img title=expand class=arrow src=\'images/foldc_.png\' height=17px   onclick=show_sub(\'child' . $bar->id . '\',this);> ' . "\n\t" . '      <a class=lab id=lab' . $bar->id . ' onclick=edit(\'' . $bar->id . '\') title=\'Click to Change\'>' . $bar->caption . '</a><a class=formeditcaption id=edit' . $bar->id . '></a>';

	if ($bar->hide == 0) {
		echo ' <input class=cbox type=checkbox id=check' . $bar->id . ' onclick="activate(\'' . $bar->id . '\');" checked title=\'Click to deActivate!\'>';
	}
	else {
		echo ' <input class=cbox type=checkbox id=check' . $bar->id . ' onclick="activate(\'' . $bar->id . '\');" title=\'Click to Activate!\'>';
	}

	echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar->id . '\');" id=img' . $bar->id . '>';
	$str1 = 'select * from ' . $dbname . '.menu ' . "\n" . '                where parent=' . $bar->id . ' order by urut';
	$res1 = mysql_query($str1);
	echo '<ul id=child' . $bar->id . ' style=\'display:none;\')>' . "\n\t\t\t" . '      <div id=group' . $bar->id . '>';

	while ($bar1 = mysql_fetch_object($res1)) {
		if (strtolower($bar1->class) == 'devider') {
			$bar1->caption = '------------';
		}

		if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
			echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t" . '  <a class=lab id=lab' . $bar1->id . ' onclick=edit(\'' . $bar1->id . '\') title=\'Click to Change\'>' . $bar1->caption . '</a><a class=formeditcaption id=edit' . $bar1->id . '></a>';
		}
		else {
			echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px   onclick=show_sub(\'child' . $bar1->id . '\',this);> ' . "\n\t\t\t\t" . '   <a class=lab id=lab' . $bar1->id . ' onclick=edit(\'' . $bar1->id . '\') title=\'Click to Change\'>' . $bar1->caption . '</a><a class=formeditcaption id=edit' . $bar1->id . '></a>';
		}

		if ($bar1->hide == 0) {
			echo ' <input class=cbox type=checkbox id=check' . $bar1->id . ' onclick="activate(\'' . $bar1->id . '\');" checked title=\'Click to deActivate!\'>';
		}
		else {
			echo ' <input class=cbox type=checkbox id=check' . $bar1->id . ' onclick="activate(\'' . $bar1->id . '\');" title=\'Click to Activate!\'>';
		}

		echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar1->id . '\');" id=img' . $bar1->id . '>';
		$str2 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t" . '                where parent=' . $bar1->id . ' order by urut';
		$res2 = mysql_query($str2);
		echo '<ul id=child' . $bar1->id . ' style=\'display:none;\')>' . "\n\t\t\t\t\t\t" . '      <div id=group' . $bar1->id . '>';

		while ($bar2 = mysql_fetch_object($res2)) {
			if (strtolower($bar2->class) == 'devider') {
				$bar2->caption = '------------';
			}

			if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t" . '   <a class=lab id=lab' . $bar2->id . ' onclick=edit(\'' . $bar2->id . '\') title=\'Click to Change\'>' . $bar2->caption . '</a><a class=formeditcaption id=edit' . $bar2->id . '></a>';
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'child' . $bar2->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t" . '<a class=lab id=lab' . $bar2->id . ' onclick=edit(\'' . $bar2->id . '\') title=\'Click to Change\'>' . $bar2->caption . '</a><a class=formeditcaption id=edit' . $bar2->id . '></a>';
			}

			if ($bar2->hide == 0) {
				echo ' <input class=cbox type=checkbox id=check' . $bar2->id . ' onclick="activate(\'' . $bar2->id . '\');" checked title=\'Click to deActivate!\'>';
			}
			else {
				echo ' <input class=cbox type=checkbox id=check' . $bar2->id . ' onclick="activate(\'' . $bar2->id . '\');" title=\'Click to Activate!\'>';
			}

			echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar2->id . '\');" id=img' . $bar2->id . '>';
			$str3 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t" . '                where parent=' . $bar2->id . ' order by urut';
			$res3 = mysql_query($str3);
			echo '<ul id=child' . $bar2->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t" . '      <div id=group' . $bar2->id . '>';

			while ($bar3 = mysql_fetch_object($res3)) {
				if (strtolower($bar3->class) == 'devider') {
					$bar3->caption = '------------';
				}

				if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t" . '   <a class=lab id=lab' . $bar3->id . ' onclick=edit(\'' . $bar3->id . '\') title=\'Click to Change\'>' . $bar3->caption . '</a><a class=formeditcaption id=edit' . $bar3->id . '></a>';
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'child' . $bar3->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=lab' . $bar3->id . ' onclick=edit(\'' . $bar3->id . '\') title=\'Click to Change\'>' . $bar3->caption . '</a><a class=formeditcaption id=edit' . $bar3->id . '></a>';
				}

				if ($bar3->hide == 0) {
					echo ' <input class=cbox type=checkbox id=check' . $bar3->id . ' onclick="activate(\'' . $bar3->id . '\');" checked title=\'Click to deActivate!\'>';
				}
				else {
					echo ' <input class=cbox type=checkbox id=check' . $bar3->id . ' onclick="activate(\'' . $bar3->id . '\');" title=\'Click to Activate!\'>';
				}

				echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar3->id . '\');" id=img' . $bar3->id . '>';
				$str4 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar3->id . ' order by urut';
				$res4 = mysql_query($str4);
				echo '<ul id=child' . $bar3->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=group' . $bar3->id . '>';

				while ($bar4 = mysql_fetch_object($res4)) {
					if (strtolower($bar4->class) == 'devider') {
						$bar4->caption = '------------';
					}

					if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=lab' . $bar4->id . ' onclick=edit(\'' . $bar4->id . '\') title=\'Click to Change\'>' . $bar4->caption . '</a><a class=formeditcaption id=edit' . $bar4->id . '></a>';
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'child' . $bar4->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=lab' . $bar4->id . ' onclick=edit(\'' . $bar4->id . '\') title=\'Click to Change\'>' . $bar4->caption . '</a><a class=formeditcaption id=edit' . $bar4->id . '></a>';
					}

					if ($bar4->hide == 0) {
						echo ' <input class=cbox type=checkbox id=check' . $bar4->id . ' onclick="activate(\'' . $bar4->id . '\');" checked title=\'Click to deActivate!\'>';
					}
					else {
						echo ' <input class=cbox type=checkbox id=check' . $bar4->id . ' onclick="activate(\'' . $bar4->id . '\');" title=\'Click to Activate!\'>';
					}

					echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar4->id . '\');" id=img' . $bar4->id . '>';
					$str5 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar4->id . ' order by urut';
					$res5 = mysql_query($str5);
					echo '<ul id=child' . $bar4->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <div id=group' . $bar4->id . '>';

					while ($bar5 = mysql_fetch_object($res5)) {
						if (strtolower($bar5->class) == 'devider') {
							$bar5->caption = '------------';
						}

						if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
							echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab id=lab' . $bar5->id . ' onclick=edit(\'' . $bar5->id . '\') title=\'Click to Change\'>' . $bar5->caption . '</a><a class=formeditcaption id=edit' . $bar5->id . '></a>';
						}
						else {
							echo '<li class=mmgr><img class=arrow title=\'Expand\' src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'child' . $bar5->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '<a class=lab id=lab' . $bar5->id . ' onclick=edit(\'' . $bar5->id . '\') title=\'Click to Change\'>' . $bar5->caption . '</a><a class=formeditcaption id=edit' . $bar5->id . '></a>';
						}

						if ($bar5->hide == 0) {
							echo ' <input class=cbox type=checkbox id=check' . $bar5->id . ' onclick="activate(\'' . $bar5->id . '\');" checked title=\'Click to deActivate!\'>';
						}
						else {
							echo ' <input class=cbox type=checkbox id=check' . $bar5->id . ' onclick="activate(\'' . $bar5->id . '\');" title=\'Click to Activate!\'>';
						}

						echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar5->id . '\');" id=img' . $bar5->id . '>';
						$str6 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar5->id . ' order by urut';
						$res6 = mysql_query($str6);
						echo '<ul id=child' . $bar5->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=group' . $bar5->id . '>';

						while ($bar6 = mysql_fetch_object($res6)) {
							if (strtolower($bar6->class) == 'devider') {
								$bar6->caption = '------------';
							}

							echo '<li><a class=lab id=lab' . $bar6->id . ' onclick=edit(\'' . $bar6->id . '\') title=\'Click to Change\'>' . $bar6->caption . '</a><a class=formeditcaption id=edit' . $bar6->id . '></a>';

							if ($bar6->hide == 0) {
								echo ' <input class=cbox type=checkbox id=check' . $bar6->id . ' onclick="activate(\'' . $bar6->id . '\');" checked title=\'Click to deActivate!\'>';
							}
							else {
								echo ' <input class=cbox type=checkbox id=check' . $bar6->id . ' onclick="activate(\'' . $bar6->id . '\');" title=\'Click to Activate!\'>';
							}

							echo ' &nbsp &nbsp <img class=dellicon title=\'Delete!\' src=\'images/menu/delete1.jpg\' onclick="delet(\'' . $bar6->id . '\');" id=img' . $bar6->id . '></li>';
						}

						echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' <li><div id=inputmenu' . $bar5->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <select id=type' . $bar5->id . ' onchange=checkType(\'' . $bar5->id . '\',this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </select> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=text value=\'Caption...\' maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar5->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                                                                                                                                           <input type=text value=\'Caption...\' maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar5->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                                                                                                                                           <input type=text value=\'Caption...\' maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar5->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '                      <input type=text value=\'Action...\' maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar5->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar5->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar5->id . ' value=' . $bar5->id . '>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar5->id . '\',\'newCaption' . $bar5->id . '\',\'newCaption2' . $bar5->id . 'x\',\'newCaption3' . $bar5->id . 'x\',\'newAction' . $bar5->id . '\',\'link' . $bar5->id . '\',\'inputmenu' . $bar5->id . '\',\'type' . $bar5->id . '\',\'newFile' . $bar5->id . '\');>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar5->id . '\',\'link' . $bar5->id . '\')>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      </div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar5->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar5->id . '\'));">' . $_SESSION['lang']['new'] . '</a>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </li>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' </ul>';
						echo '</li>';
					}

					echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <li class=mmgr><div id=inputmenu' . $bar4->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <select id=type' . $bar4->id . ' onchange=checkType(\'' . $bar4->id . '\',this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </select> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar4->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                                                                              <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar4->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                                                                              <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar4->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\n" . '                                                                                                                                                                                                                                                                                                              <input type=text value=\'Action...\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar4->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar4->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar4->id . ' value=' . $bar4->id . '>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar4->id . '\',\'newCaption' . $bar4->id . '\',\'newCaption2' . $bar4->id . 'x\',\'newCaption3' . $bar4->id . 'x\',\'newAction' . $bar4->id . '\',\'link' . $bar4->id . '\',\'inputmenu' . $bar4->id . '\',\'type' . $bar4->id . '\',\'newFile' . $bar4->id . '\');>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar4->id . '\',\'link' . $bar4->id . '\')>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      </div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar4->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar4->id . '\'));">' . $_SESSION['lang']['new'] . '</a>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </li></ul>';
					echo '</li>';
				}

				echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t" . '      <li class=mmgr><div id=inputmenu' . $bar3->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <select id=type' . $bar3->id . ' onchange=checkType(\'' . $bar3->id . '\',this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  </select> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar3->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar3->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                                                                                  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar3->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\n\t\t\t\t\t\t\t\t\t\t\t" . '      <input type=text value=\'Action...\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar3->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar3->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar3->id . ' value=' . $bar3->id . '>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar3->id . '\',\'newCaption' . $bar3->id . '\',\'newCaption2' . $bar3->id . 'x\',\'newCaption3' . $bar3->id . 'x\',\'newAction' . $bar3->id . '\',\'link' . $bar3->id . '\',\'inputmenu' . $bar3->id . '\',\'type' . $bar3->id . '\',\'newFile' . $bar3->id . '\');>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar3->id . '\',\'link' . $bar3->id . '\')>' . "\n\t\t\t\t\t\t\t\t\t\t\t" . '      </div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar3->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar3->id . '\'));">' . $_SESSION['lang']['new'] . '</a></li>' . "\t\t\t\t\t\t\t\t\t\t\t\t" . ' ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
				echo '</li>';
			}

			echo '</div>' . "\n\t\t\t\t\t\t\t\t" . '      <li class=mmgr><div id=inputmenu' . $bar2->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t\t\t\t\t\t\t\t\t" . '  <select id=type' . $bar2->id . ' onchange=checkType(\'' . $bar2->id . '\',this)>' . "\n\t\t\t\t\t\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t\t\t\t\t\t" . '  </select> ' . "\n\t\t\t\t\t\t\t\t\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar2->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                      <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar2->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                                                                      <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar2->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\n\t\t\t\t\t\t\t\t" . '      <input type=text value=\'Action...\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar2->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t\t\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar2->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t\t\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar2->id . ' value=' . $bar2->id . '>' . "\n\t\t\t\t\t\t\t\t\t" . '  ' . "\n\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar2->id . '\',\'newCaption' . $bar2->id . '\',\'newCaption2' . $bar2->id . 'x\',\'newCaption3' . $bar2->id . 'x\',\'newAction' . $bar2->id . '\',\'link' . $bar2->id . '\',\'inputmenu' . $bar2->id . '\',\'type' . $bar2->id . '\',\'newFile' . $bar2->id . '\');>' . "\n\t\t\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar2->id . '\',\'link' . $bar2->id . '\')>' . "\n\t\t\t\t\t\t\t\t" . '      </div>' . "\n\t\t\t\t\t\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar2->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar2->id . '\'));">' . $_SESSION['lang']['new'] . '</a></li>' . "\n\t\t\t\t\t\t\t\t\t" . '      </ul>';
			echo '</li>';
		}

		echo '</div>' . "\n\t\t\t\t\t\t" . '      <li class=mmgr><div id=inputmenu' . $bar1->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t\t\t\t" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t\t\t\t\t\t\t" . '  <select id=type' . $bar1->id . ' onchange=checkType(\'' . $bar1->id . '\',this)>' . "\n\t\t\t\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t\t\t\t" . '  </select> ' . "\n\t\t\t\t\t\t\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar1->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                              <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar1->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                                                                             <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar1->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>     ' . "\n\t\t\t\t\t\t" . '      <input type=text value=\'Action...\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar1->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar1->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar1->id . ' value=' . $bar1->id . '>' . "\n\t\t\t\t\t\t\t" . '  ' . "\n\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar1->id . '\',\'newCaption' . $bar1->id . '\',\'newCaption2' . $bar1->id . 'x\',\'newCaption3' . $bar1->id . 'x\',\'newAction' . $bar1->id . '\',\'link' . $bar1->id . '\',\'inputmenu' . $bar1->id . '\',\'type' . $bar1->id . '\',\'newFile' . $bar1->id . '\');>' . "\n\t\t\t\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar1->id . '\',\'link' . $bar1->id . '\')>' . "\n\t\t\t\t\t\t" . '      </div>' . "\n\t\t\t\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar1->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar1->id . '\'));">' . $_SESSION['lang']['new'] . '</a></li>' . "\n\t\t\t\t\t\t" . '      </ul>';
		echo '</li>';
	}

	echo '</div>' . "\t\t\t" . ' ' . "\n\t\t\t" . '      <li class=mmgr><div id=inputmenu' . $bar->id . ' class=menuinput  style=\'display:none;\'>' . "\n\t\t\t" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t\t\t\t" . '  <select id=type' . $bar->id . ' onchange=checkType(\'' . $bar->id . '\',this)>' . "\n\t\t\t\t" . '  <option>Type...</option><option>click</option><option>title</option><option>devider</option>' . "\n\t\t\t\t" . '  </select> ' . "\n\t\t\t\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption' . $bar->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption2' . $bar->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                                                                                  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on menu\' id=newCaption3' . $bar->id . 'x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>    ' . "\n\t\t\t" . '      <input type=text value=\'Action...\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction' . $bar->id . ' size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n\t\t\t\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile' . $bar->id . '><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t\t\t\t" . '  <input type=hidden id=master_menu' . $bar->id . ' value=' . $bar->id . '>' . "\n\t\t\t\t" . '  ' . "\n\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu' . $bar->id . '\',\'newCaption' . $bar->id . '\',\'newCaption2' . $bar->id . 'x\',\'newCaption3' . $bar->id . 'x\',\'newAction' . $bar->id . '\',\'link' . $bar->id . '\',\'inputmenu' . $bar->id . '\',\'type' . $bar->id . '\',\'newFile' . $bar->id . '\');>' . "\n\t\t\t\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu' . $bar->id . '\',\'link' . $bar->id . '\')>' . "\n\t\t\t" . '      </div>' . "\n\t\t\t\t" . '  <a class=newMenu title=\'Create New Link\' id=link' . $bar->id . ' onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu' . $bar->id . '\'));">' . $_SESSION['lang']['new'] . '</a></li>' . "\n\t\t\t" . '      </ul>';
	echo '</li>';
}

echo '</div>' . "\n" . '      <li class=mmgr><div id=inputmenu0  class=menuinput style=\'display:none;\'>' . "\n" . '      <img src=\'images/foldc_.png\' height=17px >' . "\n\t" . '  <select id=type0 onchange=checkType(\'0\',this)>' . "\n\t" . '  <option>click</option>' . "\n\t" . '  </select> ' . "\n\t" . '  <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on top most of menu\' id=newCaption0 size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                      <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on top most of menu\' id=newCaption2x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '                      <input type=text value=\'Caption...\'  maxlength=40 class=myinputtext title=\'Text to be shown on top most of menu\' id=newCaption3x size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>' . "\n" . '      <input type=text value=\'null\'  maxlength=40 class=myinputtext title=\'Filename (without extension) that will be execute when menu clicked\' id=newAction0 size=12 onkeypress="return tanpa_kutip(event);" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this) disabled>' . "\n\t" . '  <input type=checkbox  title=\'Check to create this file\' id=newFile0 disabled><font color=white>' . $_SESSION['lang']['createfile'] . '</font>' . "\n\t" . '  <input type=hidden id=master_menu0 value=0>' . "\n\n\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['save'] . ' onclick=saveMenu(\'master_menu0\',\'newCaption0\',\'newCaption2x\',\'newCaption3x\',\'newAction0\',\'link0\',\'inputmenu0\',\'type0\',\'newFile0\');>' . "\n\t" . '  <input type=button class=mybutton value=' . $_SESSION['lang']['close'] . ' onclick=showById(\'inputmenu0\',\'link0\')>' . "\n" . '      </div>' . "\n\t" . '  <a class=newMenu id=link0  title=\'Create New Link\'  onclick="javascript:hideObject(this);showObject(document.getElementById(\'inputmenu0\'));">' . $_SESSION['lang']['new'] . '</a></li>';
echo '</ul></div>';
echo '<div id=menuOrderContainer style=\'position:relative;display:none\'>' . "\n" . '     <input type=radio name=rad1 onclick=expandAllOrder()>' . $_SESSION['lang']['expandall'] . "\n\t" . ' <input type=radio name=rad1 onclick=collapsAllOrder() checked>' . $_SESSION['lang']['colapsall'] . "\n\t" . ' <hr><b>Menu Arranger</b>:' . "\n\t";
echo '<ul>' . "\n" . '     <a  class=lab id=orderlab0 href=# onclick=showEditor(\'0\',\'false\',event) title=\'Click to arrange master menu (the top most menu)\'>' . $_SESSION['lang']['mastermenu'] . '</a>' . "\n" . '     <div id=ordergroup0>';
$str = 'select * from ' . $dbname . '.menu ' . "\n" . '      where type=\'master\' order by urut';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	echo '<li class=mmgr><img title=expand class=arrow src=\'images/foldc_.png\' height=17px   onclick=show_sub(\'orderchild' . $bar->id . '\',this);>' . "\n\t" . '<a class=lab  title=\'Click to show this submenu order editor\' id=orderlab' . $bar->id . ' onclick=showEditor(\'' . $bar->id . '\',\'true\',event)>' . $bar->caption . '</a>';
	$str1 = 'select * from ' . $dbname . '.menu ' . "\n" . '                where parent=' . $bar->id . ' order by urut';
	$res1 = mysql_query($str1);
	echo '<ul id=orderchild' . $bar->id . ' style=\'display:none;\')>' . "\n\t\t\t" . '      <div id=ordergroup' . $bar->id . '>';

	while ($bar1 = mysql_fetch_object($res1)) {
		if (strtolower($bar1->class) == 'devider') {
			$bar1->caption = '------------';
		}

		if ((strtolower($bar1->class) == 'title') || (strtolower($bar1->class) == 'devider')) {
			echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t" . '  ' . $bar1->caption;
		}
		else {
			echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px   onclick=show_sub(\'orderchild' . $bar1->id . '\',this);> ' . "\n\t\t\t\t" . '   <a class=lab title=\'Click to show this submenu order editor\' id=orderlab' . $bar1->id . ' onclick=showEditor(\'' . $bar1->id . '\',\'true\',event)>' . $bar1->caption . '</a>';
		}

		$str2 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t" . '                where parent=' . $bar1->id . ' order by urut';
		$res2 = mysql_query($str2);
		echo '<ul id=orderchild' . $bar1->id . ' style=\'display:none;\')>' . "\n\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar1->id . '>';

		while ($bar2 = mysql_fetch_object($res2)) {
			if (strtolower($bar2->class) == 'devider') {
				$bar2->caption = '------------';
			}

			if ((strtolower($bar2->class) == 'title') || (strtolower($bar2->class) == 'devider')) {
				echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t" . '    ' . $bar2->caption;
			}
			else {
				echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar2->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t" . ' <a class=lab title=\'Click to show this submenu order editor\' id=orderlab' . $bar2->id . ' onclick=showEditor(\'' . $bar2->id . '\',\'true\',event)>' . $bar2->caption . '</a>';
			}

			$str3 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t" . '                where parent=' . $bar2->id . ' order by urut';
			$res3 = mysql_query($str3);
			echo '<ul id=orderchild' . $bar2->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar2->id . '>';

			while ($bar3 = mysql_fetch_object($res3)) {
				if (strtolower($bar3->class) == 'devider') {
					$bar3->caption = '------------';
				}

				if ((strtolower($bar3->class) == 'title') || (strtolower($bar3->class) == 'devider')) {
					echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t" . '   ' . $bar3->caption;
				}
				else {
					echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar3->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t" . ' <a class=lab title=\'Click to show this submenu order editor\' id=orderlab' . $bar3->id . ' onclick=showEditor(\'' . $bar3->id . '\',\'true\',event)>' . $bar3->caption . '</a>';
				}

				$str4 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar3->id . ' order by urut';
				$res4 = mysql_query($str4);
				echo '<ul id=orderchild' . $bar3->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar3->id . '>';

				while ($bar4 = mysql_fetch_object($res4)) {
					if (strtolower($bar4->class) == 'devider') {
						$bar4->caption = '------------';
					}

					if ((strtolower($bar4->class) == 'title') || (strtolower($bar4->class) == 'devider')) {
						echo '<li class=mmgr><img src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' ' . $bar4->caption;
					}
					else {
						echo '<li class=mmgr><img title=Expand class=arrow src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar4->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <a class=lab title=\'Click to show this submenu order editor\' id=orderlab' . $bar4->id . ' onclick=showEditor(\'' . $bar4->id . '\',\'true\',event)>' . $bar4->caption . '</a>';
					}

					$str5 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar4->id . ' order by urut';
					$res5 = mysql_query($str5);
					echo '<ul id=orderchild' . $bar4->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  <div id=ordergroup' . $bar4->id . '>';

					while ($bar5 = mysql_fetch_object($res5)) {
						if (strtolower($bar5->class) == 'devider') {
							$bar5->caption = '------------';
						}

						if ((strtolower($bar5->class) == 'title') || (strtolower($bar5->class) == 'devider')) {
							echo '<li class=mmgr><img  src=\'images/menu/arrow_10.gif\'> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' ' . $bar5->caption;
						}
						else {
							echo '<li class=mmgr><img class=arrow title=\'Expand\' src=\'images/foldc_.png\' height=17px  onclick=show_sub(\'orderchild' . $bar5->id . '\',this);> ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '   <a class=lab title=\'Click to show this submenu order editor\' id=orderlab' . $bar5->id . ' onclick=showEditor(\'' . $bar5->id . '\',\'true\',event)>' . $bar5->caption . '</a>';
						}

						$str6 = 'select * from ' . $dbname . '.menu ' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '                where parent=' . $bar5->id . ' order by urut';
						$res6 = mysql_query($str6);
						echo '<ul id=orderchild' . $bar5->id . ' style=\'display:none;\'>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '      <div id=ordergroup' . $bar5->id . '>';

						while ($bar6 = mysql_fetch_object($res6)) {
							if (strtolower($bar6->class) == 'devider') {
								$bar6->caption = '------------';
							}

							echo '<li>' . $bar6->caption . '</li>';
						}

						echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . ' </ul>';
						echo '</li>';
					}

					echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
					echo '</li>';
				}

				echo '</div>' . "\n\t\t\t\t\t\t\t\t\t\t\t\t\t" . '  </ul>';
				echo '</li>';
			}

			echo '</div>' . "\n\t\t\t\t\t\t\t\t\t" . '      </ul>';
			echo '</li>';
		}

		echo '</div>' . "\n\t\t\t\t\t\t" . '      </ul>';
		echo '</li>';
	}

	echo '</div>' . "\t\t\t" . ' ' . "\n\t\t\t" . '      </ul>';
	echo '</li>';
}

echo '</ul></div>';
echo '</div>';
echo '<div id=ordereditor style=\'display:none;position:absolute;\'>';
echo OPEN_THEME('' . $_SESSION['lang']['menuordereditor'] . ':');
echo '<div id=ordereditorcontent></div>';
echo CLOSE_THEME();
echo '</div>';
echo '<hr><center><input type=button class=mybutton value=' . $_SESSION['lang']['apply'] . ' onclick=window.location.reload()><hr></center>' . "\n" . '      <fieldset><legend>' . $_SESSION['lang']['options'] . ':</legend>';
echo '<img src=images/menu/star.png> <span class=elink onclick=showMenuOrder() title=\'Click to manage menu order\' id=optionController>' . $_SESSION['lang']['orderarrange'] . '</span><br>';
echo '</fieldset>';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>
