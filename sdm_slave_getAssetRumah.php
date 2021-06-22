<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$blok = $_POST['blok'];
$norumah = $_POST['norumah'];
$str = " select a.kodeasset,b.tipeasset,b.namasset,b.status,b.keterangan,\r\n       case b.status\r\n\t   when 0 then 'Retire/pensiun'\r\n\t   when 1 then 'Aktif'\r\n\t   when 2 then 'Broken/Rusak'\r\n\t   when 3 then 'Missing'\r\n\t   else 'Unknown'\r\n\t   end as sts \r\n       from ".$dbname.".sdm_perumahandt a \r\n       left join ".$dbname.".sdm_daftarasset b\r\n\t   on a.kodeasset=b.kodeasset\r\n\t   where a.kodeorg='".$kodeorg."' and a.blok='".$blok."'\r\n\t   and a.norumah='".$norumah."'";
$res = mysql_query($str);
echo mysql_error($conn);
$no = 0;
echo ''.$_SESSION['lang']['kodeorg'].': '.$kodeorg."<br>\r\n     ".$_SESSION['lang']['blok'].': '.$blok."<br>\r\n\t ".$_SESSION['lang']['no_rmh'].': '.$norumah."\r\n     <table class=sortable cellspacing=1 border=0>\r\n     <thead><tr class=rowheader>\r\n\t    <td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['kodeasset']."</td>\t\t\r\n\t\t<td>".$_SESSION['lang']['tipeasset']."</td>\r\n\t\t<td>".$_SESSION['lang']['namaaset']."</td>\r\n\t\t<td>".$_SESSION['lang']['status']."</td>\r\n\t\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t</tr>\r\n\t </thead><tbody>";
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t\t <td>".$no."</td>\r\n\t\t <td>".$bar->kodeasset."</td>\r\n\t\t <td>".$bar->tipeasset."</td>\r\n\t\t <td>".$bar->namasset."</td>\r\n\t\t <td>".$bar->sts."</td>\r\n\t\t <td>".$bar->keterangan."</td>\r\n\t\t </tr>";
}
echo '</tbody><tfoot></tfoot></table>';

?>