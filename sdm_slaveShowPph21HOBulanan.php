<?php



require_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$kodeorg = $_POST['kodeorg'];
$regular = $_POST['regular'];
$thr = $_POST['thr'];
$jaspro = $_POST['jaspro'];
$jmsperusahaan = $_POST['jmsperusahaan'];
$arrComp = [];
$str = 'select id from '.$dbname.'.sdm_ho_component where `pph21`=1 order by id';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_array($res)) {
    array_push($arrComp, $bar[0]);
}
for ($x = 0; $x < count($arrComp); ++$x) {
    if (0 == $x) {
        $listComp = $arrComp[$x];
    } else {
        $listComp .= ','.$arrComp[$x];
    }
}
$listComp = ' and d.idkomponen in('.$listComp.')';
$arrPtkp = [];
$str = 'select * from '.$dbname.'.sdm_ho_pph21_ptkp order by id';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    $arrPtkp[$bar->id] = $bar->value;
}
$arrTarif = [];
$arrTarifVal = [];
$str = 'select * from '.$dbname.".sdm_ho_pph21_kontribusi\r\n      where percent!=0 or upto!=0  order by upto";
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    array_push($arrTarif, $bar->percent);
    array_push($arrTarifVal, $bar->upto);
}
$jmsporsi = 6.54; //nilai awal aja
$jmsporsikar = 3; //nilai awal aja
$str = 'select * from '.$dbname.'.sdm_ho_hr_jms_porsi';
$res = mysql_query($str, $conn);
while ($bar = mysql_fetch_object($res)) {
    if ('perusahaan' == $bar->id) {
        $jmsporsi = $bar->value;
    } else {
        $jmsporsikar = $bar->value;
    }
}
$stru = 'select `persen`,`max` from '.$dbname.'.sdm_ho_pph21jabatan';
$resu = mysql_query($stru);
$percenJab = 0;
$maxBJab = 0;
while ($baru = mysql_fetch_object($resu)) {
    $percenJab = $baru->persen;
    $maxBJab = $baru->max;
}
//$str1 = "select e.karyawanid,e.npwp,e.taxstatus,e.name,sum(d.jumlah) as `jumlah` from ".$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji  where e.karyawanid=d.karyawanid ".$listComp." and periodegaji='".$periode."' and kodeorg='".$kodeorg."' group by karyawanid";
$str1 = "select a.karyawanid,a.npwp,a.taxstatus,a.name,b.sumjum as jumlah, b.pph21 from 
		sdm_ho_employee a inner join
		( select kodeorg,periodegaji,karyawanid, sum(if(idkomponen in (5,9,66),0,
			if(idkomponen in (20,26,27,64),-jumlah,
			if(idkomponen in (1,2,29,30,32,33,4,15, 17,16,58,61,21,23, 6,7,57, 54),jumlah,0)
			))) as sumjum, sum(if(idkomponen=24,jumlah,0)) as pph21 
		from sdm_gaji where kodeorg='".$kodeorg."' and periodegaji='".$periode."'
		group by kodeorg,periodegaji,karyawanid
		order by kodeorg,periodegaji,karyawanid ) b on a.karyawanid= b.karyawanid";
if ($res = mysql_query($str1, $conn)) {
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $pendapatanBulanan = 0;
		if ($bar->jumlah > 0){$pendapatanBulanan = $bar->jumlah;} 

        $pphbulanan = 0;
		if($bar->pph21>0){$pphbulanan = $bar->pph21;}

        echo "<tr class=rowcontent>\r\n\t\t    <td class=firsttd>".$no."</td>\r\n\t\t\t<td align=center>".$bar->karyawanid."</td>\r\n\t\t\t<td>".$bar->name."</td>\r\n\t\t\t<td align=center>".$bar->taxstatus."</td>\r\n\t\t\t<td>".$bar->npwp."</td>\r\n\t\t\t<td align=center>".$periode."</td>\r\n\t\t\t<td align=right>".number_format($pendapatanBulanan, 2, '.', ',')."</td>\r\n\t\t\t<td align=right>".number_format($pphbulanan, 2, '.', ',')."</td>\r\n\t\t   </tr>";
    }
} else {
    echo ' Error: '.addslashes(mysql_error($conn));
}

?>