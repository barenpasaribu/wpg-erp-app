<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['setupbonus'].':</b>');
echo '<div id=EList>';
$arrCurr = [];
$stra = 'select * from '.$dbname.'.sdm_ho_bonus_setup';
$resa = mysql_query($stra);
while ($bara = mysql_fetch_object($resa)) {
    array_push($arrCurr, $bara->component);
}
$str = 'select * from '.$dbname.".sdm_ho_component where type='basic'";
$res = mysql_query($str);
echo "<fieldset>\r\n\t\t      <legend>".$_SESSION['lang']['komponenbonus']."</legend>\r\n\t\t\t ";
while ($bar = mysql_fetch_object($res)) {
    if (1 == $bar->id) {
        $s = '';
    } else {
        $s = '';
    }

    if (in_array($bar->id, $arrCurr, true)) {
        echo '<input type=checkbox '.$s.' checked onclick=bonusSetup(this,this.value) value='.$bar->id.' id=com'.$bar->id.'>'.$bar->name.'<br>';
    } else {
        echo '<input type=checkbox '.$s.'  onclick=bonusSetup(this,this.value) value='.$bar->id.' id=com'.$bar->id.'>'.$bar->name.'<br>';
    }
}
echo '</fieldet></div>';
CLOSE_BOX();
echo close_body();

?>