<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select sum(jumlahrupiah) as jumlah,hargacatu, kodeorg,periodegaji from '.$dbname.".sdm_catu  \r\n             group by kodeorg,periodegaji order by periodegaji desc limit 40";
} else {
    $str = 'select sum(jumlahrupiah) as jumlah,hargacatu,sum(posting) as posting, kodeorg,periodegaji from '.$dbname.".sdm_catu \r\n            where kodeorg='".$_SESSION['empl']['lokasitugas']."' group by kodeorg,periodegaji \r\n            order by periodegaji desc  limit 40";
}

$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $frm[1] .= "<tr class=rowcontent>\r\n                  <td>".$no."</td>\r\n                    <td>".$bar->kodeorg."</td> \r\n                    <td>".$bar->periodegaji."</td>\r\n                    <td>".number_format($bar->hargacatu, 0, '.', ',')."</td>      \r\n                    <td>".number_format($bar->jumlah, 0, '.', ',')."</td>    \r\n                    <td><img src='images/excel.jpg' class='resicon' title='Excel' onclick=getExcel(event,'sdm_slave_pembagianCatuExcel.php','".$bar->kodeorg."','".$bar->periodegaji."') > &nbsp &nbsp";
    if (0 < $bar->posting) {
        $frm[1] .= "<img src='images/skyblue/posted.png'>";
    } else {
        $frm[1] .= "<img src='images/skyblue/posting.png'  class='resicon' title='Posting' onclick=postingCatu('".$bar->kodeorg."','".$bar->periodegaji."',".$bar->jumlah.')>';
    }

    $frm[1] .= "</td>    \r\n                  </tr>";
}
echo $frm[1];

?>