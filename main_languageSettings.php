<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=JavaScript1.2 src=js/languageconf.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('');
echo OPEN_THEME($_SESSION['lang']['langconf']);
$sta = 'select * from ' . $dbname . '.namabahasa order by code';
$res = mysql_query($sta);
$opt = '';
$langlist = '';
$deffind = '';
$newCap = '';
$newText = '';
$count = 0;

while ($bar = mysql_fetch_object($res)) {
	$count += 1;
	$opt .= '<option value=' . $bar->code . '>' . $bar->name . '</option>';
	$langlist .= ' &nbsp &nbsp<a href=# onclick=loadLang(\'' . $bar->code . '\')>' . $bar->name . '</a>';
	$deffind = $bar->code;
	$newCap .= '<tr class=rowcontent><td>' . $bar->name . '</td>' . "\r\n\t" . '          <td><input type=hidden value=\'' . $bar->code . '\' id=hidden' . $count . '><input type=text class=myinputtext size=120 id=lang' . $count . ' onkeypress="return tanpa_kutip(event)"></td></tr>';
}

$tabcont[2] = "\r\n" . '    <table class=data border=0 cellspacing=0>' . "\r\n\t" . '<thead>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>' . "\r\n\t" . '<tr class=rowcontent><td>' . "\r\n" . '     ' . $_SESSION['lang']['newlang'] . '</td><td> <input type=text class=myinputtext size=3 maxlength=2 id=lang onkeypress="return tanpa_kutip(event)"></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr class=rowcontent><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['langname'] . '</td><td> <input type=text class=myinputtext size=30 maxlength=45 id=langname onkeypress="return tanpa_kutip(event)"></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr class=rowcontent><td>' . "\r\n\t" . ' ' . $_SESSION['lang']['deflangtonew'] . '</td><td><select id=def>' . $opt . '</select>' . "\r\n\t" . ' </td></tr>' . "\r\n\t" . ' </tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <button class=mybutton onclick=addNewLanguage()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' ';
$tabcont[1] = '<fieldset><legend>' . $_SESSION['lang']['addnewcaption'] . '</legend>' . "\r\n" . '             <table class=data cellspacing=0 border=0 width=100%>' . "\r\n" . '             <thead>' . "\r\n\t\t\t" . ' </thead>' . "\r\n\t\t\t" . ' <tbody>' . "\r\n\t\t\t" . ' <tr class=rowcontent><td>' . $_SESSION['lang']['legend'] . '</td><td><input type text class=myinputtext id=legend onkeypress="return tanpa_kutip(event);" size=30 maxlength=45></td></tr>' . "\r\n\t\t\t" . ' <tr class=rowcontent><td>' . $_SESSION['lang']['location'] . '</td><td><input type text class=myinputtext id=location onkeypress="return tanpa_kutip(event);" size=55></td></tr>' . "\r\n\t\t\t" . ' ' . $newCap . "\t\t\t" . '  ' . "\t\t" . ' ' . "\r\n\t\t\t" . ' </tbody>' . "\r\n\t\t\t" . ' <tfoot>' . "\r\n\t\t\t" . ' </tfoot>' . "\r\n\t\t\t" . ' </table>' . "\r\n\t\t\t" . ' <center><button onclick=saveNewCaption(\'' . $count . '\') class=mybutton>' . $_SESSION['lang']['save'] . '</button></center>' . "\r\n\t\t\t" . ' </fieldset>' . "\r\n" . '            ';
$tabcont[0] = "\r\n\t" . '  <span id=avlanguage>' . "\r\n\t" . '  <fieldset style=\'width:850px;\'>' . "\r\n\t" . '  <legend>' . $_SESSION['lang']['availlang'] . '</legend>' . "\r\n\t" . '  ' . $langlist . "\r\n\t" . '  </fieldset>' . "\r\n\t" . '  </span>' . "\t" . ' ' . "\r\n\t" . '  <br> ' . "\r\n\t" . '  <b>' . $_SESSION['lang']['findlegendandloc'] . '</b>' . "\r\n\t" . '  <input type=text id=searclang class=myinputtext size=20 onkeypress="return duaevent(event);">' . "\r\n" . '      <button class=mybutton onclick=findComp()>' . $_SESSION['lang']['find'] . '</button> ' . $_SESSION['lang']['on'] . ' <span id=defaultfind style=\'font-weight:bolder;\'>' . $deffind . '</span>' . "\r\n" . '      <fieldset style=\'height:330px;width:850px;overflow:scroll;\'>' . "\r\n" . '      <legend>' . $_SESSION['lang']['detailconfigfor'] . ' [<span id=defaultfind1></span>]</legend>' . "\r\n\t" . '  <div  style=\'height:320px;width:840px;overflow:scroll;\'>' . "\r\n\t" . '  <span id=langdetailconf>' . "\r\n\t" . '  ' . "\r\n\t" . '  </span>' . "\r\n\t" . '  </div>' . "\r\n" . '     </fieldset>';
$arrhead[2] = $_SESSION['lang']['addnewlanguage'];
$arrhead[1] = $_SESSION['lang']['addnewcaption'];
$arrhead[0] = $_SESSION['lang']['detailconfiguration'];
echo '<br>';
drawTab('LANG', $arrhead, $tabcont, 200, 900);
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>
