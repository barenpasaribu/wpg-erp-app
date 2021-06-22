<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=js/menusetting.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css HREF=style/privillages.css>' . "\r\n";
include 'master_mainMenu.php';
$hak = $_SESSION['standard']['access_level'];
$str = ' select max(id) as id from ' . $dbname . '.menu';
$res = mysql_query($str);
$max_id = 0;

while ($bar = mysql_fetch_array($res)) {
	$max_id = $bar[0];
}

echo '<script language=javascript1.2>' . "\r\n" . '     max_id=' . $max_id . "\r\n\t" . ' </script>';
echo '<script language=javascript1.2>' . "\r\n" . '    hak=' . $hak . "\r\n" . '</script>';
$hak = $_SESSION['standard']['access_level'];
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['privconf'] . ':');
echo '<div class=privillageIcon><img src=\'images/useraccounts.png\' height=40px style=\'vertical-align:middle;\'><b>' . $_SESSION['lang']['privsetting'] . ':</b></div>';
echo '<fieldset>' . "\r\n" . '    <legend>' . $_SESSION['lang']['levelandprivoption'] . ':</legend> ' . "\r\n" . '    <ul>' . "\r\n" . '    <li class=mmgr><img src=\'images/menu/arrow_10.gif\'>' . "\r\n" . '        <!--a class=lab id=lab0 title=\'Click to set menu level\' onclick=loadMenuLevelSetting(this,event)>' . $_SESSION['lang']['maintlevelpriv'] . '</a-->' . "\r\n" . '        <a class=lab id=lab0 title=\'Click to set menu level\'>' . $_SESSION['lang']['maintlevelpriv'] . '</a>' . "\r\n" . '        </li>' . "\r\n" . '    <li class=mmgr><img title=expand class=arrow src=\'images/foldc_.png\' height=14px  onclick=show_sub(\'child0\',this);> ' . "\r\n" . '        <a class=lab id=lab1 title=\'Click to set user privilege\'   onclick=show_sub(\'child0\',this);>' . $_SESSION['lang']['userpriv'] . '</a>';

if ($_SESSION['standard']['access_level'] == 1) {
	echo "\r\n" . '           <ul id=child0 style=\'display:none;\')>' . "\r\n" . '              <li>' . "\r\n" . '               <a class=lab id=lab2 title=\'Use Levelization on menu as default privilege settings\'  onclick=loadUserLevelSetting(this,event)>' . $_SESSION['lang']['levelizationpriv'] . '</a>' . "\r\n" . '             </li>' . "\r\n" . '             <li>' . "\r\n" . '              <a class=lab id=lab3 title=\'Use detail privileges\'  onclick=loadDetailPrivillageSetting(this,event)>' . $_SESSION['lang']['detailpriv'] . '</a>' . "\r\n" . '             </li>' . "\r\n" . '             <li>' . "\r\n" . '              <a class=lab id=lab4 title=\'Global Access for a Menu\'  onclick=loadGlobal(this,event)>' . $_SESSION['lang']['globalakses'] . '</a>' . "\r\n" . '             </li>             ' . "\r\n" . '               </ul>';
}
else {
	echo "\r\n" . '           <ul id=child0 style=\'display:none;\')>' . "\r\n" . '             <li>' . "\r\n" . '              <a class=lab id=lab3 title=\'Use detail privileges\'  onclick=loadDetailPrivillageSetting(this,event)>' . $_SESSION['lang']['detailpriv'] . '</a>' . "\r\n" . '             </li>             ' . "\r\n" . '               </ul>';
}

echo "\r\n" . '        </li>' . "\r\n" . '    </ul>' . "\r\n\t" . ' </fieldset><br>';
$str = 'select * from ' . $dbname . '.tipeakses order by access_name';
$res = mysql_query($str);

if ($_SESSION['standard']['access_level'] == 1) {
	echo '<fieldset>' . "\r\n" . '      <legend>Status:</legend>' . $_SESSION['lang']['privdesc'] . "\r\n" . '      <table width=200px  cellspacing=1 border=0 class=data>' . "\r\n\t" . '  <thead>' . "\r\n\t" . '  <tr><td>Access.Type</td>' . "\r\n\t" . '  <td>Status</td></tr></thead>' . "\r\n\t" . '  <tbody> ' . "\r\n\t" . '   ';

	while ($bar = mysql_fetch_object($res)) {
		if ($bar->status == 1) {
			$bar->status = '<font color=#00AA00>Active</font>';
		}
		else {
			$bar->status = '<font color=#DD3333>Off <img id=privilball src=images/buttongreen.png class=privilball onclick=turnOn(\'' . $bar->access_name . '\') title=\'Click to Activate\'></font>';
		}

		echo '<tr class=rowcontent id=row' . $bar->access_name . '  title=\'' . $bar->remark . '\'>' . "\r\n\t" . '       <td>' . ucfirst($bar->access_name) . '</td>' . "\r\n\t" . '       <td id=col' . $bar->access_name . '>' . $bar->status . '</td></tr>' . "\r\n\t" . '      ';
	}

	echo '<tr><td>Turn Off Security</td>' . "\r\n\t" . '            <td><img id=privilball src=images/hot.png onclick=turnOff() title=\'Turn off all privilege\' style=\'cursor:pointer;\'>' . "\r\n\t\t\t" . '</tr>';
	echo '</tbody></table>';
	echo '</fieldset>';
}
else {
	echo '<fieldset>' . "\r\n" . '      <legend>Status:</legend>' . $_SESSION['lang']['privdesc'] . "\r\n" . '      <table width=200px  cellspacing=1 border=0 class=data>' . "\r\n\t" . '  <thead>' . "\r\n\t" . '  <tr><td>Access.Type</td>' . "\r\n\t" . '  <td>Status</td></tr></thead>' . "\r\n\t" . '  <tbody> ' . "\r\n\t" . '   ';

	while ($bar = mysql_fetch_object($res)) {
		if ($bar->status == 1) {
			$bar->status = '<font color=#00AA00>Active</font>';
		}
		else {
			$bar->status = '<font color=#DD3333>Off <img id=privilball src=images/buttongreen.png class=privilball></font>';
		}

		echo '<tr class=rowcontent id=row' . $bar->access_name . '  title=\'' . $bar->remark . '\'>' . "\r\n\t" . '       <td>' . ucfirst($bar->access_name) . '</td>' . "\r\n\t" . '       <td id=col' . $bar->access_name . '>' . $bar->status . '</td></tr>' . "\r\n\t" . '      ';
	}

	echo '<tr><td>Turn Off Security</td>' . "\r\n\t" . '            <td><img id=privilball src=images/hot.png title=\'Turn off all privilege\'>' . "\r\n\t\t\t" . '</tr>';
	echo '</tbody></table>';
	echo '</fieldset>';
}

echo CLOSE_THEME();
echo '<div id=ctr style=\'position:absolute;display:none;\'>';
echo OPEN_THEME('Menu/User Level And Privileges:');
echo '<div id=content>';
echo '</div>';
echo CLOSE_THEME();
echo '</div>';
echo '<div id=ctrmenu style=\'position:absolute;display:none;\'>';
echo OPEN_THEME('Menu Mapping:');
echo '<div id=contentmenu>';
echo '</div>';
echo CLOSE_THEME();
echo '</div>';
echo '<div id=globalakses style=\'position:absolute;display:none;\'>';
echo OPEN_THEME('Choose Menu:');
echo '<div id=contentglobal>';
echo '</div>';
echo CLOSE_THEME();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
