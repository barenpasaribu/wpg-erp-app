<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '  ' . "\r\n";
$where = '(`tipe`=\'WORKSHOP\' or`tipe`=\'HOLDING\' or `tipe`=\'PABRIK\' or `tipe`=\'KANWIL\' or `tipe`=\'GUDANG\' or tipe=\'GUDANGTEMP\' or `tipe`=\'KEBUN\' or tipe=\'TRAKSI\') and left(kodeorganisasi,3) = \''.substr($_SESSION['empl']['lokasitugas'],0,3).'\'';
//$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '1');
echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:300px'), $optOrg));
$els[] = array(makeElement('periode', 'label', $_SESSION['lang']['periode']), makeElement('periode', 'text', '', array('style' => 'width:70px', 'maxlength' => '80')));
$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggalmulai', 'text', '', array('style' => 'width:100px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', 'maxlength' => '8')) . ' s/d ' . makeElement('tanggalsampai', 'text', '', array('style' => 'width:100px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', 'maxlength' => '8')));
$els[] = array(makeElement('tutupbuku', 'label', $_SESSION['lang']['tutupbuku']), makeElement('tutupbuku', 'check', '0', array()));
$fieldStr = '##kodeorg##periode##tanggalmulai##tanggalsampai##tutupbuku';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_periodeakuntansi', '##kodeorg##periode'));
echo genElTitle($_SESSION['lang']['periodeakuntansi'], $els);
echo '</div>';
echo '<div style=\'clear:both;float:left\'>';
$where= "left(kodeorg,3) = '".substr($_SESSION['empl']['lokasitugas'],0,3)."'";
//echo masterTable($dbname, 'setup_periodeakuntansi', '*', array(), array(), NULL, array(), NULL, 'kodeorg##periode');
echo masterTable($dbname, 'setup_periodeakuntansi', '*', array(), array(), $where, array(), NULL, 'kodeorg##periode');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
