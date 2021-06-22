<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
if ('EN' == $_SESSION['language']) {
    $zz = 'b.namatipe1 as namatipe';
} else {
    $zz = 'b.namatipe';
}

$str = 'select a.namasset,a.tipeasset,a.jlhblnpenyusutan,a.awalpenyusutan,a.bulanan,'.$zz." from ".$dbname.'.sdm_daftarasset a left join '.$dbname.".sdm_5tipeasset b on a.tipeasset=b.kodetipe where a.kodeorg='".$_SESSION['empl']['lokasitugas']."' and a.status=1"; 
//and a.persendecline=0";

$res = mysql_query($str);
$ass = [];
$nama = [];
$pass = [];
while ($bar = mysql_fetch_object($res)) {

  	$thnawal = substr($bar->awalpenyusutan, 0, 4);
    $blnawal = substr($bar->awalpenyusutan, 5, 2);
    $total = $thnawal * 12 + $blnawal;
    $thnNow = substr($param['periode'], 0, 4);
    $blnNow = substr($param['periode'], 5, 2);
    $totalNow = $thnNow * 12 + $blnNow;
    $selisih = $totalNow - $total;
     
    if ($selisih < $bar->jlhblnpenyusutan && $selisih>=0) {
        $ass[$bar->tipeasset] += $bar->bulanan;
    }
    $nama[$bar->tipeasset] = $bar->namatipe;
    $pass[$bar->tipeasset] = 'DEP'.substr($bar->tipeasset, 0, 2);
}


echo "<button class=mybutton onclick=prosesPenyusutan(1) id=btnproses>Process</button>\r\n\t<table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>No</td>\r\n\t<td>Asset Type</td>\r\n\t<td>Journal Code</td>\r\n\t<td>Period</td>\r\n\t<td>".$_SESSION['lang']['keterangan']."</td>\r\n\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t<td> Detail Asset</td>\r\n\t</tr>\r\n\t</thead>\r\n\t<tbody>";
$no = 0;
foreach ($ass as $key => $val) {
    ++$no;
    echo "<tr class=rowcontent id='row".$no."'>\r\n\t<td>".$no."</td>\r\n\t
	<td id='tipeasset".$no."'>".$key."	</td>\r\n\t
	<td id='kodejurnal".$no."'>".$pass[$key]."</td>    \r\n\t
	<td id='periode".$no."'>".$param['periode']."</td>\r\n\t
	<td id='keterangan".$no."'>".$nama[$key]."</td>\r\n\t
	<td align=right id='jumlah".$no."'>".$ass[$key]."</td>\r\n\t
    <td>";

    $str1 = "select * from sdm_daftarasset where kodeorg='".$_SESSION['empl']['lokasitugas']."' and status=1 AND tipeasset='".$key."'"; 
 //   saveLog($str1);

    $res1 = mysql_query($str1);
    echo "<table> <tr class=rowheader> <td>Kode Asset </td><td>Nama Asset </td><td>Jml. Bln. Panyusutan </td><td>Awal Penyusutan </td><td>Bulanan</td></tr>";
    $jumlah=0;
    while ($bar1 = mysql_fetch_object($res1)) {

    $thnawal = substr($bar1->awalpenyusutan, 0, 4);
    $blnawal = substr($bar1->awalpenyusutan, 5, 2);
    $total = $thnawal * 12 + $blnawal;
    $thnNow = substr($param['periode'], 0, 4);
    $blnNow = substr($param['periode'], 5, 2);
    $totalNow = $thnNow * 12 + $blnNow;
    $selisih = $totalNow - $total;
       
      if ($selisih < $bar1->jlhblnpenyusutan && $selisih>=0) {
        echo "<tr class=rowcontent><td>".$bar1->kodeasset."</td>";
        echo "<td>".$bar1->namasset."</td>";
        echo "<td>".$bar1->jlhblnpenyusutan."</td>";
        echo "<td>".$bar1->awalpenyusutan."</td>";
        echo "<td align='right'>".number_format($bar1->bulanan,2)."</td></tr>";
        $jumlah += $bar1->bulanan;   
        }
        
    }
    echo "<tr class=rowheader><td colspan='4' align='center'>TOTAL</td><td align='right'>".number_format($jumlah,2)."</td></tr>";
    echo "</table></td></tr>";
}
echo '</tbody><tfoot></tfoot></table>';

?>