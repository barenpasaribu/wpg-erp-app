<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '  ';
echo '<p align=\'left\'><u><b><font face=\'Arial\' size=\'5\' color=\'#000080\'>' . $_SESSION['lang']['parameteraplikasi'] . '</font></b></u></p>';
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'');
$optBin = array('Character', 'Numeric');
echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('kodeaplikasi', 'label', $_SESSION['lang']['kodeaplikasi']), makeElement('kodeaplikasi', 'text', '', array('style' => 'width:200px', 'maxlength' => '2')));
$els[] = array(makeElement('kodeparameter', 'label', $_SESSION['lang']['kodeparameter']), makeElement('kodeparameter', 'text', '', array('style' => 'width:200px', 'maxlength' => '10')));
$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', array('style' => 'width:300px'), $optOrg));
$els[] = array(makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', array('style' => 'width:200px', 'maxlength' => '50')));
$els[] = array(makeElement('typenilai', 'label', $_SESSION['lang']['typenilai']), makeElement('typenilai', 'select', '', array('style' => 'width:300px'), $optBin));
$els[] = array(makeElement('nilai', 'label', $_SESSION['lang']['nilai']), makeElement('nilai', 'text', '', array('style' => 'width:200px', 'maxlength' => '255')));
$fieldStr = '##kodeaplikasi##kodeparameter##kodeorg##keterangan##typenilai##nilai';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_parameterappl', '##kodeaplikasi##kodeparameter'));
echo genElement($els);
echo '</div>';
echo '<div style=\'height:200px;overflow:auto\'>';
echo masterTable($dbname, 'setup_parameterappl', '*', array(), array(), NULL, array(), NULL, 'kodeaplikasi##kodeparameter');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
