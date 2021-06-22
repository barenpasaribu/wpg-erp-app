<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript1.2 src=js/detailakses.js></script>' . "\r\n";
$tmpUser = makeOption($dbname, 'user', 'namauser,namauser');
$optUser = array('' => '');

foreach ($tmpUser as $key => $row) {
	$optUser[$key] = $row;
}

$els = array();
$els[] = array(makeElement('user', 'label', $_SESSION['lang']['user']), makeElement('user', 'select', '', array('style' => 'width:250px', 'onchange' => 'getMenuList()'), $optUser));
$els[] = array(makeElement('menu', 'label', $_SESSION['lang']['menu']), makeElement('menu', 'select', '', array('style' => 'width:250px', 'disabled' => 'disabled'), array()));
$btn = array();
$btn[] = array(makeElement('input', 'check', '0', array('disabled' => 'disabled')), makeElement('input', 'label', $_SESSION['lang']['input']));
$btn[] = array(makeElement('edit', 'check', '0', array('disabled' => 'disabled')), makeElement('edit', 'label', $_SESSION['lang']['edit']));
$btn[] = array(makeElement('delete', 'check', '0', array('disabled' => 'disabled')), makeElement('delete', 'label', $_SESSION['lang']['delete']));
$btn[] = array(makeElement('print', 'check', '0', array('disabled' => 'disabled')), makeElement('print', 'label', $_SESSION['lang']['print']));
$btn[] = array(makeElement('approve', 'check', '0', array('disabled' => 'disabled')), makeElement('approve', 'label', $_SESSION['lang']['approve']));
$btn[] = array(makeElement('posting', 'check', '0', array('disabled' => 'disabled')), makeElement('posting', 'label', $_SESSION['lang']['posting']));
$els['btn'] = array(genElementMultiDim('Permission', $btn, 3));
$els[] = array(makeElement('save', 'btn', $_SESSION['lang']['save'], array('onclick' => 'saveData()', 'disabled' => 'disabled')));
echo genElTitle('Detail Access', $els);
CLOSE_BOX();
echo close_body();

?>
