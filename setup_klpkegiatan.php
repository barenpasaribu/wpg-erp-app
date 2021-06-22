<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '  ' . "\r\n";
$where = '`detail`=0';
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', NULL, '2');
$whereOrg = 'tipe=\'HOLDING\'';
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '1');
echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:250px'), $optOrg));
$els[] = array(makeElement('kodeklp', 'label', $_SESSION['lang']['kodeklp']), makeElement('kodeklp', 'text', '', array('style' => 'width:50px', 'maxlength' => '8')));
$els[] = array(makeElement('namakelompok', 'label', $_SESSION['lang']['namakelompok']), makeElement('namakelompok', 'text', '', array('style' => 'width:250px', 'maxlength' => '80')));
$els[] = array(makeElement('namakelompok1', 'label', $_SESSION['lang']['namakelompok1']), makeElement('namakelompok1', 'text', '', array('style' => 'width:250px', 'maxlength' => '80')));
$els[] = array(makeElement('noakun', 'label', $_SESSION['lang']['noakun']), makeElement('noakun', 'select', '', array('style' => 'width:250px'), $optAkun));
$fieldStr = '##kodeorg##kodeklp##namakelompok##namakelompok1##noakun';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_klpkegiatan', '##kodeklp##kodeorg'));
echo genElTitle($_SESSION['lang']['namakelompok'], $els);
echo '</div>';
echo '<div style=\'clear:both;float:left\'>';
echo masterTable($dbname, 'setup_klpkegiatan', '*', array(), array(), array(), array(), 'setup_slave_klpkegiatan_pdf');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
