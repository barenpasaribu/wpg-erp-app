<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/sdm_pembagianCatu.js'></script>\r\n";
include 'master_mainMenu.php';
$str = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optPeriode .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
OPEN_BOX('', $_SESSION['lang']['pembagiancatu']);
$frm[0] .= "<fieldset><legend>Form</legend>\r\n              <table>\r\n              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>\r\n             <tr><td>".$_SESSION['lang']['periodegaji'].'<td><td><select id=periode>'.$optPeriode."</select></td></tr> \r\n             <tr><td>".$_SESSION['lang']['hargasatuan']." Catu<td><td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" id=harga size=10>/Ltr</td></tr>     \r\n             </table>\r\n             <button class=mybutton onclick=tampilkanCatu()>".$_SESSION['lang']['tampilkan']."</button>\r\n             </fieldset>\r\n             \r\n\r\n             <div id=container style='width:850px;height:400px;overflow:scroll;'>\r\n             </div>";
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select sum(jumlahrupiah) as jumlah, hargacatu,kodeorg,periodegaji from '.$dbname.".sdm_catu  \r\n             group by kodeorg,periodegaji order by periodegaji desc  limit 40";
} else {
    $str = 'select sum(jumlahrupiah) as jumlah,hargacatu,sum(posting) as posting, kodeorg,periodegaji from '.$dbname.".sdm_catu \r\n            where kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periodegaji \r\n            order by periodegaji desc  limit 40";
}

$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['laporanCatu']."</legend>\r\n              <table class=sortable cellspacing=1 border=0>\r\n              <thead>\r\n              <tr class=rowheader>\r\n              <td>".$_SESSION['lang']['nomor']."</td>\r\n              <td>".$_SESSION['lang']['kodeorg']."</td>\r\n              <td>".$_SESSION['lang']['periode']."</td>\r\n              <td>".$_SESSION['lang']['harga']."/Ltr</td>    \r\n              <td>".$_SESSION['lang']['jumlah']." (Rp)</td>    \r\n              <td>".$_SESSION['lang']['action']."</td>\r\n               </tr>\r\n               <tbody id=containerlist>";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $frm[1] .= "<tr class=rowcontent>\r\n                  <td>".$no."</td>\r\n                    <td>".$bar->kodeorg."</td> \r\n                    <td>".$bar->periodegaji."</td>\r\n                    <td>".number_format($bar->hargacatu, 0, '.', ',')."</td>     \r\n                    <td>".number_format($bar->jumlah, 0, '.', ',')."</td>    \r\n                    <td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'sdm_slave_pembagianCatuExcel.php','".$bar->kodeorg."','".$bar->periodegaji."') > &nbsp &nbsp";
    if (0 < $bar->posting) {
        $frm[1] .= "<img src='images/skyblue/posted.png'>";
    } else {
        $frm[1] .= "<img src='images/skyblue/posting.png'  class='resicon' title='Posting' onclick=postingCatu('".$bar->kodeorg."','".$bar->periodegaji."',".$bar->jumlah.')>';
    }

    $frm[1] .= "</td>    \r\n                  </tr>";
}
$frm[1] .= "</tbody>\r\n              <tfoot>\r\n              </tfoot>\r\n              </table>";
$hfrm[0] = $_SESSION['lang']['form'];
$hfrm[1] = $_SESSION['lang']['daftar'];
drawTab('FRM', $hfrm, $frm, 300, 900);
CLOSE_BOX();
echo close_body();

?>