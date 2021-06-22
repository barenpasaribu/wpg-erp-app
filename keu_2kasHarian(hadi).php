<?php

require_once 'master_validation.php';



include_once 'lib/eagrolib.php';



include_once 'lib/zLib.php';



include_once 'lib/formReport.php';



$as = [$_SESSION['lang']['pilihdata']];



$fReport = new formReport('kasharian', 'keu_slave_2kasHarianv2', $_SESSION['lang']['kasharian']);



if ('HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {



    $ckRegional = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional', null, '0', true);



    $whr = 'kodeorganisasi in(select distinct kodeunit from '.$dbname.".bgt_regional_assignment where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and regional='".$ckRegional[$_SESSION['empl']['lokasitugas']]."')";



    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whr);



    $optOrg = $as + $optOrg;



    $fReport->addPrime('kodeorg', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 20, $optOrg, ['onchange' => 'getNoakun()']);



}







$arrPil = [$_SESSION['lang']['detail'], $_SESSION['lang']['total']];



$wherd = "kasbank=1 and (pemilik='".$_SESSION['empl']['lokasitugas']."' OR pemilik='GLOBAL')";



$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $wherd);

 

/*

if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {



    $wherd = "kasbank=1 and (pemilik='HOLDING' or pemilik='".$_SESSION['empl']['induklokasitugas']."')";



    $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $wherd);



}

*/





$optAkun = $as + $optAkun;



$fReport->addPrime('noakun', $_SESSION['lang']['noakundari'], '', 'select', 'L', 20, $optAkun);



$fReport->addPrime('noakunsmp', $_SESSION['lang']['noakunsampai'], '', 'select', 'L', 20, $optAkun);



$fReport->addPrime('periode', $_SESSION['lang']['periode'], date('d-m-Y'), 'period', 'L', 15);



$fReport->addPrime('pildt', $_SESSION['lang']['pilih'], '', 'select', 'L', 20, $arrPil);



$fReport->_detailHeight = 60;



echo open_body();



echo "<script language=\"JavaScript1.2\" src=\"js/formReport.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/biReport.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/keu_2kasharian.js\"></script>\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"style/zTable.css\">\r\n";



include 'master_mainMenu.php';



OPEN_BOX();



$fReport->render();



CLOSE_BOX();



echo close_body();







?>