<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
($_POST['kdOrg'] == '' ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
($_POST['thnId'] == '' ? ($thnId = $_GET['thnId']) : ($thnId = $_POST['thnId']));
($_POST['kdProj'] == '' ? ($kdProj = $_GET['kdProj']) : ($kdProj = $_POST['kdProj']));
($_POST['tipe'] == '' ? ($tipe = $_GET['tipe']) : ($tipe = $_POST['tipe']));
$unitId = $_SESSION['lang']['all'];
$dktlmpk = $_SESSION['lang']['all'];
($_POST['tanggal1'] == '' ? ($tanggal1 = $_GET['tanggal1']) : ($tanggal1 = $_POST['tanggal1']));
($_POST['tanggal2'] == '' ? ($tanggal2 = $_GET['tanggal2']) : ($tanggal2 = $_POST['tanggal2']));
$tangsys1 = putertanggal($tanggal1);
$tangsys2 = putertanggal($tanggal2);
$wheretang = " b.tanggal like '%%' ";
if ($tanggal1  !== '') {
    $wheretang = " b.tanggal = '".$tangsys1."' ";
    if ($tanggal2  !== '') {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

if ($tanggal2  !== '') {
    $wheretang = " b.tanggal = '".$tangsys2."' ";
    if ($tanggal1  !== '') {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

if ($proses == 'preview' || $proses == 'excel') {
    $brdr = 0;
    $bgcoloraja = '';
    if ($_POST['tipeTrk']  !== '') {
		$whre = "";
        //$whre = " and tipetransaksi='".$_POST['tipeTrk']."'";		
    }

    $sData = "select distinct b.notransaksi,b.tanggal from kebun_aktifitas b\r\n               where substr(b.tanggal,1,7)='".$thnId."' and b.kodeorg='".$kdOrg."' and b.jurnal=0 and b.notransaksi like '%".$tipe."%'\r\n               and ".$wheretang."\r\n               ".$whre.'';
	
    $qData = mysql_query($sData) ;
    $rowdt = mysql_num_rows($qData);
    $tab .= '<button class=mybutton onclick=postingDat('.$rowdt.')  id=revTmbl>Posting Data</button>';
    $tab .= '<table cellspacing=1 border='.$brdr." class=sortable>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n        <td ".$bgcoloraja." align=center>No.</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['notransaksi']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['tanggal']."</td>\r\n       \r\n       \r\n        </tr>";
    $tab .= '</tr></thead><tbody>';
    while ($rData = mysql_fetch_assoc($qData)) {
		$nor++;
        $tab .= '<tr class=rowcontent id=rowDt_'.$nor.'><td align=center>'.$nor.'</td>';
        $tab .= '<td id=notransaksi_'.$nor.'>'.$rData['notransaksi'].'</td>';
        $tab .= '<td>'.$rData['tanggal'].'</td>';		
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriodeAkut = 'select distinct periode from '.$dbname.".setup_periodeakuntansi \r\n                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
        $qPeriodeCari = mysql_query($sPeriodeAkut) ;
        while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
            $optPeriode .= "<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    default:
        break;
}
function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

?>