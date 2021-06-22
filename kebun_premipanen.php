<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/kebun_premipanen.js'></script>\r\n";
include 'master_mainMenu.php';
$str = 'select distinct periode from '.$dbname.".sdm_5periodegaji \r\n      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optPeriode .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
$optPremi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('KALTENG' === $_SESSION['empl']['regional']) {
    $skdprem = 'select distinct kodeorg from '.$dbname.".kebun_5premipanen where kodeorg not in ('JAKARTA','SULTRA','SUMUT') order by kodeorg";
} else {
    if ('SULTRA' === $_SESSION['empl']['regional']) {
        $skdprem = 'select distinct kodeorg from '.$dbname.".kebun_5premipanen where kodeorg not in ('JAKARTA','KALTENG','SUMUT') order by kodeorg";
    } else {
        if ('SUMUT' === $_SESSION['empl']['regional']) {
            $skdprem = 'select distinct kodeorg from '.$dbname.".kebun_5premipanen where kodeorg not in ('JAKARTA','KALTENG','SULTRA') order by kodeorg";
        } else {
            $skdprem = 'select distinct kodeorg from '.$dbname.".kebun_5premipanen where kodeorg not in ('SUMUT','KALTENG','SULTRA') order by kodeorg";
        }
    }
}

$qkdprem = mysql_query($skdprem) ;
while ($rkdprem = mysql_fetch_assoc($qkdprem)) {
    $optPremi .= "<option value='".$rkdprem['kodeorg']."'>".$rkdprem['kodeorg'].'</option>';
}
OPEN_BOX('', $_SESSION['lang']['premipemanen']);
$frm[0] .= "<fieldset><legend>Form</legend>\r\n              <table>\r\n              <tr><td>".$_SESSION['lang']['periode'].'<td><td><select id=periode style=width:150px>'.$optPeriode."</select></td></tr> \r\n              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>\r\n              <tr><td>".$_SESSION['lang']['kodepremi'].'<td><td><select id=kdpremi style=width:150px>'.$optPremi."</select></td></tr>     \r\n             </table>\r\n             <button class=mybutton onclick=getData()>".$_SESSION['lang']['preview']."</button>\r\n             </fieldset>\r\n             <div id=container style='width:100%;height:400px;overflow:scroll;'>\r\n             </div>";
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select sum(jumlahrupiah) as jumlah, hargacatu,kodeorg,periodegaji from '.$dbname.".sdm_catu  \r\n             group by kodeorg,periodegaji order by periodegaji desc  limit 40";
} else {
    $str = 'select sum(jumlahrupiah) as jumlah,hargacatu,sum(posting) as posting, kodeorg,periodegaji from '.$dbname.".sdm_catu \r\n            where kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periodegaji \r\n            order by periodegaji desc  limit 40";
}

$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['premi'].' '.$_SESSION['lang']['panen']."</legend>\r\n              <table class=sortable cellspacing=1 border=0>\r\n              <thead>\r\n              <tr class=rowheader>\r\n              <td>No.</td>\r\n              <td>".$_SESSION['lang']['kodeorg']."</td>\r\n              <td>".$_SESSION['lang']['periode']."</td>\r\n              <td>".$_SESSION['lang']['action']."</td>\r\n               </tr>\r\n               <tbody id=containerlist><script>loadData()</script>";
$frm[1] .= "</tbody>\r\n              <tfoot>\r\n              </tfoot>\r\n              </table>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['daftar'];
drawTab('FRM', $hfrm, $frm, 300);
CLOSE_BOX();
echo close_body();

?>