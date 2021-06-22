<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';

$param = $_POST;
//ambil akun laba ditahan 
$sql="SELECT noakundebet from keu_5parameterjurnal WHERE kodeaplikasi='CLY'";
$qry=mysql_query($sql);
$akunlabaditahan=mysql_fetch_assoc($qry);

//ambil akun laba berjalan 
$sql="SELECT noakundebet from keu_5parameterjurnal WHERE kodeaplikasi='CLM'";
$qry=mysql_query($sql);
$akunlababerjalan=mysql_fetch_assoc($qry);

$periode=$param['tahun']+1;

//ambil saldo laba berjalan 
$sql="SELECT awal01 from keu_saldobulanan WHERE noakun='".$akunlababerjalan['noakundebet']."' AND kodeorg='".$param['kodeorg']."' AND periode='".$periode."01'";
$qry=mysql_query($sql);
$saldolababerjalan=mysql_fetch_assoc($qry);


$sql="UPDATE keu_saldobulanan SET awal01=ROUND(awal01+".$sadolababerjalan['awal01'].",2)  WHERE noakun='".$akunlabaditahan['noakundebet']."' AND kodeorg='".$param['kodeorg']."' AND periode='".$periode."01'";

if(mysql_query($sql)){

    $sql="UPDATE keu_saldobulanan SET awal01=0 WHERE noakun='".$akunlababerjalan['noakundebet']."' AND kodeorg='".$param['kodeorg']."' AND periode='".$periode."01'";
    mysql_query($sql);
} else {

    $sql="insert into keu_saldobulanan(kodeorg,periode,noakun,awal01)values('".$param['kodeorg']."','".$periode."01','".$akunlabaditahan['noakundebet']."',ROUND(".$saldolababerjalan['awal01'].",2) )"; 
    saveLog($sql);
    if(mysql_query($sql)){
        $sql="UPDATE keu_saldobulanan SET awal01=0 WHERE noakun='".$akunlababerjalan['noakundebet']."' AND kodeorg='".$param['kodeorg']."' AND periode='".$periode."01'";
        mysql_query($sql);
    }
    saveLog($sql);    

}


?>