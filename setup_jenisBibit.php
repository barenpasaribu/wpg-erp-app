<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo '<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '  ' . "\r\n" . '<p align="left"><u><b><font face="Arial" size="5" color="#000080">Jenis Bibit</font></b></u></p>' . "\r\n";
echo '<div style=\'margin-bottom:30px\'>';
$els = array();
$els[] = array(makeElement('jenisbibit', 'label', $_SESSION['lang']['jenisbibit']), makeElement('jenisbibit', 'text', '', array('style' => 'width:100px', 'maxlength' => '30', 'onkeypress' => 'return tanpa_kutip(event)')));
$fieldStr = '##jenisbibit';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = array(genFormBtn($fieldStr, 'setup_jenisbibit', '##jenisbibit'));
echo genElement($els);
echo '</div>';
echo '<div style=\'height:200px;overflow:auto\'>';
echo masterTable($dbname, 'setup_jenisbibit', '*', array(), array(), NULL, array(), NULL, 'jenisbibit');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
