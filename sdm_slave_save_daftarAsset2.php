<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$method = $_POST['method'];
switch ($method) {
    case 'getKodeAkhir':
        $sPt = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
        $qPt = mysql_query($sPt);
        $rPt = mysql_fetch_assoc($qPt);
        $kpl = $rPt['induk'].'-'.$_POST['kdAset'];
        $tppenyusutan = makeOption($dbname, 'sdm_5tipeasset', 'kodetipe,metodepenyusutan');
        $scek = 'select distinct kodeasset from '.$dbname.".sdm_daftarasset \r\n          where kodeasset like '".$kpl."%' order by kodeasset desc limit 0,1";
        $urut = 0;
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        if ('' != $rcek['kodeasset']) {
            if (3 == strlen($_POST['kdAset'])) {
                $urut = substr($rcek['kodeasset'], -7);
            } else {
                $urut = substr($rcek['kodeasset'], -8);
            }
        }

        $rer = (int) $urut;
        $kdcrt = $rer + 1;
        $kdcrt = addZero($kdcrt, 7);
        if (strlen($_POST['kdAset']) < 3) {
            $kdcrt = addZero($kdcrt, 8);
        }

        $kdasst = $kpl.$kdcrt;
        echo $kdasst.'#####'.$tppenyusutan[$_POST['kdAset']];

        break;
    default:
        break;
}

?>