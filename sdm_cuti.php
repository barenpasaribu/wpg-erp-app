<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/sdm_5cuti.js'></script>\r\n";
OPEN_BOX('', $_SESSION['lang']['cuti']);
if ('HOLDING' == trim($_SESSION['empl']['tipeinduk'])) {
    $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') \r\n\t      and length(kodeorganisasi)=4 order by namaorganisasi desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optlokasitugas .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    if (trim('' != $_SESSION['org']['induk'])) {
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') \r\n\t      and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi desc";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optlokasitugas .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
        }
    }
}

$optperiode = '';
for ($x = -1; $x < 3; ++$x) {
    $dt = date('Y') - $x;
    $optperiode .= "<option value='".$dt."'>".$dt.'</option>';
}
echo "\r\n     <fieldset><legend>".$_SESSION['lang']['navigasi']."</legend>\r\n\t   <table>\r\n\t      <tr>\r\n\t\t      <td>".$_SESSION['lang']['lokasitugas'].'</td><td><select id=lokasitugas>'.$optlokasitugas."</select></td>\r\n\t\t      <td>".$_SESSION['lang']['periode'].'</td><td><select id=periode>'.$optperiode."</select></td>\r\n\t\t      <td><button class=mybutton onclick=\"loadList(document.getElementById('lokasitugas').options[document.getElementById('lokasitugas').selectedIndex].value,document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value)\">".$_SESSION['lang']['lihat']."</button></td>\r\n\t\t\t  <td><button class=mybutton onclick=prosesAwal()>".$_SESSION['lang']['proses']."</button></td>\r\n\t\t  </tr>\t  \r\n\t   </table>\r\n\t </fieldset>  \r\n    ";
CLOSE_BOX();
OPEN_BOX('', '');
$arr[0] = "<div id=containerlist1 style='width:900px;height:350px;overflow:scroll'>\r\n      </div>";
$arr[1] = "<div id=containerlist2 style='width:900px;height:350px;overflow:scroll'>\r\n      </div>";
$hfrm[0] = $_SESSION['lang']['header'];
$hfrm[1] = $_SESSION['lang']['detail'];
drawTab('FRM', $hfrm, $arr, 100, 900);
CLOSE_BOX();
echo close_body();

?>