<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src='js/zMaster.js'></script>\n<script language=javascript src='js/keu_5kelompokjurnal_reset.js'></script>\n<link rel=stylesheet type=text/css href=style/zTable.css>\n";
$where = "`tipe`='HOLDING' or `tipe`='PT'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
$optPt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOPt = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where '.$where.'';
$qOpt = mysql_query($sOPt);
while ($rOpt = mysql_fetch_assoc($qOpt)) {
    $optPt .= '<option value='.$rOpt['kodeorganisasi'].'>'.$rOpt['namaorganisasi'].'</option>';
}
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('kodekelompok', 'label', $_SESSION['lang']['kodekelompok']), makeElement('kodekelompok', 'text', '', ['style' => 'width:100px', 'maxlength' => '6', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('nokounter', 'label', $_SESSION['lang']['nokounter']), makeElement('nokounter', 'text', '0', ['style' => 'width:70px', 'maxlength' => '11', 'onkeypress' => 'return angka_doang(event)'])];
$fieldStr = '##kodeorg##kodekelompok##keterangan##nokounter';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'keu_5kelompokjurnal', '##kodeorg##kodekelompok')];
echo genElTitle($_SESSION['lang']['kodekelompok'].' '.$_SESSION['lang']['jurnal'], $els);
echo '</div><br /><br /><br /><br /><br /><br /><br /><br />';
echo "\n<fieldset style=width=30px;float:left;>\n<table cellpading=1 border=0>\n<tr><td><select id=kodePt name=kodePt style='width:150px;'>".$optPt.'</select><button class=mybutton onclick="resetJurnal()">'.$_SESSION['lang']['save']."</button>\n</table>\n</fieldset><br /><br /><br /><br />";
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname, 'keu_5kelompokjurnal');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>