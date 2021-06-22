<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n";
$optApp = getEnum($dbname, 'setup_posting', 'kodeaplikasi');
$optJab = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('kodeaplikasi', 'label', $_SESSION['lang']['kodeaplikasi']), makeElement('kodeaplikasi', 'select', '', array('style' => 'width:300px'), $optApp));
$els[] = array(makeElement('jabatan', 'label', $_SESSION['lang']['jabatan']), makeElement('jabatan', 'select', '', array('style' => 'width:300px'), $optJab));
$fieldStr = '##kodeaplikasi##jabatan';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_posting', '##kodeaplikasi', NULL, 'kodeaplikasi', true));
echo genElTitle('Setup Posting', $els);
echo '</div>';
echo '<div style=\'clear:both;float:left\'>';
echo masterTable($dbname, 'setup_posting', '*');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
