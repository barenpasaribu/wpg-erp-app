<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kebun = $_POST['kebun'];
$afdeling = $_POST['afdeling'];
$thnTnmId = $_POST['thnTnmId'];
switch ($proses) {
    case 'getAfd':
        if ('' === $_POST['kebun']) {
            $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
            echo $optAfd;
        } else {
            $sOpt = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and induk='".$_POST['kebun']."'";
            $qOpt = mysql_query($sOpt) ;
            $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
            while ($rOpt = mysql_fetch_assoc($qOpt)) {
                $optAfd .= '<option value='.$rOpt['kodeorganisasi'].'>'.$rOpt['namaorganisasi'].'</option>';
            }
            echo $optAfd;
        }

        break;
    case 'getThn':
        $sOpt = 'select distinct tahuntanam from '.$dbname.".setup_blok where left(kodeorg,6)='".$afdeling."'";
        $qOpt = mysql_query($sOpt) ;
        $optThn = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($rOpt = mysql_fetch_assoc($qOpt)) {
            $optThn .= '<option value='.$rOpt['tahuntanam'].'>'.$rOpt['tahuntanam'].'</option>';
        }
        echo $optThn;

        break;
    default:
        break;
}

?>