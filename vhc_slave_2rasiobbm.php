<?php
require_once('master_validation.php');
require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/eagrolib.php');

$unit=$_POST['unit'];
$tglAwal=tanggalsystem($_POST['tglAwal']);
$tglAkhir=tanggalsystem($_POST['tglAkhir']);

if($unit==''){
    echo"warning: Working unit required";exit();
}
if($tglAwal==''||$tglAkhir==''){
	echo "Warning: date required"; exit;
}

$str="SELECT sum(a.jumlah) as jumlah, a.satuan, b.kodeorg, b.kodevhc, sum(jlhbbm) jumlahbbm FROM vhc_rundt a INNER JOIN vhc_runht b ON a.notransaksi=b.notransaksi WHERE kodeorg='".$unit."' AND tanggal between '".$tglAwal."' and '".$tglAkhir."' group by b.kodevhc ";
$qry=mysql_query($str);
$i=1;
while ($res=mysql_fetch_array($qry)) {
echo "<tr class=rowcontent>
		<td align=center>".$i."</td>
		<td>".$res['kodevhc']."</td>
		<td align='right'>".number_format($res['jumlah'],2)."</td>
		<td align='center'>".$res['satuan']."</td>
		<td align='right'>".number_format($res['jumlahbbm'],2)."</td>
		<td align='right'>".number_format($res['jumlahbbm']/$res['jumlah'],2)."</td>
	</tr>";
$i++;
}

?>