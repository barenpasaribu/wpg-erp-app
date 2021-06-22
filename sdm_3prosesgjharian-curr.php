<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

/** Controller **/
//# Options
//$optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',
//    "kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='H'");

$str="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='H'
          order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optPeriod[$bar->periode]=$bar->periode;
}

#========= Tipe Karyawan ====================
$optTipe = array('kht'=>'KHT','khl'=>'KHL');
#========= Tipe Karyawan ====================

# Element
$els = array();
$els[] = array(
    makeElement('periodegaji','label',$_SESSION['lang']['periodegaji']),
    makeElement('periodegaji','select','',
        array('style'=>'width:150px'),$optPeriod),
);
/*$els[] = array(
    makeElement('tipe','label',$_SESSION['lang']['tipekaryawan']),
    makeElement('tipe','select','',array('style'=>'width:150px'),$optTipe),
);*/
$els['btn'] = array(
    makeElement('listBtn','btn',$_SESSION['lang']['list'],
        array('onclick'=>"list()"))
   // makeElement('postBtn','btn',$_SESSION['lang']['proses'],
   //     array('onclick'=>"post()",'disabled'=>'disabled'))
);

$form = "";
$form .= "<h3 align='left'>".$_SESSION['lang']['prosesgjharian']."</h3>";
$form .= genElementMultiDim($_SESSION['lang']['form'],$els,1);
$form .= "<fieldset style='float:left;clear:left'><legend><b>".$_SESSION['lang']['list']."</b>".
    "</legend><div id='listContainer'></div></fieldset>";

/** View **/
echo open_body_hrd();
?>
<script languange=javascript1.2 src='js/sdm_prosesgjharian.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
include('master_mainMenu.php');

OPEN_BOX_HRD();
echo $form;
CLOSE_BOX_HRD();

echo close_body_hrd();
?>