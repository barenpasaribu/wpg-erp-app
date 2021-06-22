<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];

$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi 
		where kodeorg like '".$pt."%' and periode='".$periode."'";
$res = mysql_query($str);

while ($bar = mysql_fetch_array($res)) {
    $tgsampai = $bar['tanggalsampai'];
    $tgmulai = $bar['tanggalmulai'];
}


$sql="SELECT * FROM ".$dbname.".log_5klbarang";
$query = mysql_query($sql);
while ($hasil = mysql_fetch_object($query)) {
$kodekelompok=$hasil->kode;	
echo "<tr class=rowcontent><td colspan=17>".$hasil->kelompok."</td></tr>";	

if (isset($_POST['unitDt'])) {

	$str = 'select ' . "\r\n" . '  a.kodeorg, a.hargaratasaldoawal as sawalharat, '  . "\r\n" . '  a.kodebarang,' . "\r\n" . ' sum(a.saldoakhirqty) as salakqty,' . "\r\n" . '                      sum(a.nilaisaldoakhir) as salakrp,' . "\r\n" . '                      sum(a.qtymasuk) as masukqty,' . "\r\n" . '                      sum(a.qtykeluar) as keluarqty,' . "\r\n" . '                      sum(qtymasukxharga) as masukrp,' . "\r\n" . '                      sum(qtykeluarxharga) as keluarrp,                      ' . "\r\n" . '                      sum(a.saldoawalqty) as sawalqty,' . "\r\n" . '                      sum(a.nilaisaldoawal) as sawalrp,' . "\r\n" . '                        b.namabarang,b.satuan, a.hargarata as harat   ' . "\r\n" . '                        from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '                        left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '                        on a.kodebarang=b.kodebarang' . "\r\n" . '                      where kodegudang like \'' . $_POST['unitDt'] . '%\' ' . "\r\n" . '                      and periode=\'' . $periode . '\'' . "\r\n" . '                      group by a.kodebarang order by a.kodebarang';

}

else if ($gudang == '') {

	$str = 'select ' . "\r\n" . '                      a.kodeorg, a.hargaratasaldoawal as sawalharat,' . "\r\n" . '                      a.kodebarang,' . "\r\n" . '                      sum(a.saldoakhirqty) as salakqty,' . "\r\n" . '                      sum(a.nilaisaldoakhir) as salakrp,' . "\r\n" . '                      sum(a.qtymasuk) as masukqty,' . "\r\n" . '                      sum(a.qtykeluar) as keluarqty,' . "\r\n" . '                      sum(qtymasukxharga) as masukrp,' . "\r\n" . '                      sum(qtykeluarxharga) as keluarrp,                      ' . "\r\n" . '                      sum(a.saldoawalqty) as sawalqty,' . "\r\n" . '                      sum(a.nilaisaldoawal) as sawalrp,' . "\r\n" . '                        b.namabarang,b.satuan, a.hargarata as harat   ' . "\r\n" . '                        from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '                        left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '                        on a.kodebarang=b.kodebarang' . "\r\n" . '                      where kodeorg=\'' . $pt . '\' ' . "\r\n" . '                      and periode=\'' . $periode . '\'' . "\r\n" . '  and kelompokbarang=\''.$kodekelompok.'\'                     group by a.kodebarang order by a.kodebarang';

}

else {

	$str = 'select a.kodeorg, a.kodebarang, a.saldoakhirqty as salakqty, a.hargarata as harat, a.nilaisaldoakhir as salakrp, a.qtymasuk as masukqty, a.qtykeluar as keluarqty, a.qtymasukxharga as masukrp, a.qtykeluarxharga as keluarrp, a.saldoawalqty as sawalqty, a.hargaratasaldoawal as sawalharat, a.nilaisaldoawal as sawalrp, b.namabarang,b.satuan 
		from ' . $dbname . '.log_5saldobulanan a left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang
		where kodeorg=\'' . $pt . '\' and periode=\'' . $periode . '\' and kodegudang=\'' . $gudang . '\' and kelompokbarang=\''.$kodekelompok.'\'
		order by a.kodebarang';

}



$salakqty = 0;

$harat = 0;

$salakrp = 0;

$masukqty = 0;

$keluarqty = 0;

$masukrp = 0;

$keluarrp = 0;

$sawalQTY = 0;

$sawalharat = 0;

$sawalrp = 0;

$namabarang = 0;

$res = mysql_query($str);

$no = 0;

		$totsalakqty = 0;

		$totsalakrp = 0;

		$totmasukqty = 0;

		$totkeluarqty = 0;

		$totmasukrp = 0;

		$totkeluarrp = 0;

		$totsawalQTY = 0;

		$totsawalrp = 0;

		$totsawalharat = 0;

		$totharatmasuk = 0;

		$totharatkeluar = 0;

		$totharat = 0;

if (mysql_num_rows($res) < 1) {

//	echo '<tr class=rowcontent><td colspan=17>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';

}

else {

	while ($bar = mysql_fetch_object($res)) {

		$no += 1;
		$kodebarang = $bar->kodebarang;
		$namabarang = $bar->namabarang;
		$harat = $bar->harat;

		$sawalQTY = $bar->sawalqty;

if($sawalQTY>0){
		$sawalrp = $bar->sawalrp;
		$sawalharat = $sawalrp/$sawalQTY;
}else{
	$sawalharat=0;
	$sawalrp = 0;
}

		if($gudang != ''){

			$where= "AND kodegudang='".$gudang."'";
		}

		$sql1="SELECT a.kodebarang, SUM(a.jumlah) AS jumlah, SUM(a.jumlah*a.hargasatuan) AS jumlahrp, tipetransaksi, tanggal, kodegudang, post 
			from log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi 
			WHERE post='1' AND tipetransaksi ='1' AND (tanggal BETWEEN '".$tgmulai."' AND '".$tgsampai."' )
			AND a.kodebarang='".$bar->kodebarang."' AND kodept like '".$pt."%'  ".$where."  ";

		$mysql1=mysql_query($sql1);
		$hasil=mysql_fetch_assoc($mysql1);

		$mskqty = $hasil['jumlah'];
		$mskrp = $hasil['jumlahrp'];

		$sql1="SELECT a.kodebarang, SUM(a.jumlah) AS jumlah, SUM(a.jumlah*a.hargarata) AS jumlahrp, tipetransaksi, tanggal, kodegudang, post 
			from log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi 
			WHERE post='1' AND tipetransaksi IN('2','3') AND (tanggal BETWEEN '".$tgmulai."' AND '".$tgsampai."' )
			AND a.kodebarang='".$bar->kodebarang."' AND kodept like '".$pt."%'  ".$where." ";
		$mysql1=mysql_query($sql1);
		$hasil=mysql_fetch_assoc($mysql1);

		$masukqty = $hasil['jumlah']+$mskqty;
		$masukrp = $hasil['jumlahrp']+$mskrp;

/*
		//pengluaran mutasi
		$sql2="SELECT a.kodebarang, SUM(a.jumlah) AS jumlah, SUM(a.jumlah*a.hargasatuan) AS jumlahrp, tipetransaksi, tanggal, kodegudang, post 
			from log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi 
			WHERE post='1' AND tipetransaksi='7' AND (tanggal BETWEEN '".$tgmulai."' AND '".$tgsampai."' )
			AND a.kodebarang='".$bar->kodebarang."' AND kodept='".$pt."'";

		$mysql2=mysql_query($sql2);
		$hasil2=mysql_fetch_assoc($mysql2);

		$mutasikeluarqty = $hasil2['jumlah'];
		$mutasikeluarrp = $hasil2['jumlahrp'];

		//penerimaan mutasi
		$sql3="SELECT a.kodebarang, SUM(a.jumlah) AS jumlah, SUM(a.jumlah*a.hargasatuan) AS jumlahrp, tipetransaksi, tanggal, kodegudang, post 
			from log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi 
			WHERE post='1' AND tipetransaksi='3' AND (tanggal BETWEEN '".$tgmulai."' AND '".$tgsampai."' )
			AND a.kodebarang='".$bar->kodebarang."' AND kodept='".$pt."'";

		$mysql3=mysql_query($sql3);
		$hasil3=mysql_fetch_assoc($mysql3);

		$mutasimasukqty = $hasil3['jumlah'];
		$mutasimasukrp = $hasil3['jumlahrp'];

*/
		//pengeluaran barang
		$sql3="select kodebarang, round(sum(jumlah),2) as jumlah, sum(jumlahrp) as jumlahrp from (SELECT a.kodebarang, SUM(a.jumlah) AS jumlah, a.hargarata, SUM(if(tipetransaksi=6,a.jumlah*a.hargasatuan,a.jumlah*a.hargarata)) AS jumlahrp, tipetransaksi, tanggal, kodegudang, post 
			from log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi 
			WHERE post='1' AND tipetransaksi IN('5','6','7') AND (tanggal BETWEEN '".$tgmulai."' AND '".$tgsampai."' )
			AND a.kodebarang='".$bar->kodebarang."' AND left(kodept,3)=left('".$pt."',3) ".$where." group by a.notransaksi) as x ";

		$mysql3=mysql_query($sql3);
		$hasil3=mysql_fetch_assoc($mysql3);

		$keluarqty = $hasil3['jumlah'];
		$haratkeluar = $hasil3['hargarata'];
		$keluarrp = $hasil3['jumlahrp'];

	$salakqty = $sawalQTY+$masukqty-$keluarqty;
	
	if($salakqty=='0.00' || $salakqty<'0'){
		$salakrp='0';	
	} else {
		$salakrp = $sawalrp+$masukrp-$keluarrp;
//		$salakrp = $salakqty*$harat;
	}
	$harat = $salakrp / $salakqty;



		if (isset($_POST['unitDt'])) {

			echo '<tr class=rowcontent> ';

		}

		else {

			echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailMutasiBarangHargaExcel(event,\'' . $pt . '\',\'' . $periode . '\',\'' . $gudang . '\',\'' . $kodebarang . '\',\'' . $namabarang . '\',\'' . $bar->satuan . '\',\'log_laporanMutasiDetailPerBarangHarga_Excel.php\');"> ';

		}



		echo '<td>'.$no.'</td>
			  <td>'.$periode.'</td>
			  <td>'.$kodebarang.'</td>
			  <td>'.$namabarang.'</td>
			  <td>'.$bar->satuan.'</td>
			  <td align=right class=firsttd>'. number_format($sawalQTY, 2, '.', ',') . '</td>
			  <td align=right>'.number_format($sawalharat, 2, '.', ','). '</td>
			  <td align=right>'. number_format($sawalrp, 2, '.', ',') .'</td>
			  
			  <td align=right class=firsttd>'. number_format($masukqty, 2, '.', ',') . '</td>
			  <td align=right>'. number_format($haratmasuk, 2, '.', ',') . '</td>
              <td align=right>' . number_format($masukrp, 2, '.', ',') . '</td>

              <td align=right class=firsttd>' . number_format($keluarqty, 2, '.', ',') . '</td>
              <td align=right>' . number_format($haratkeluar, 2, '.', ',') . '</td>
              <td align=right>' . number_format($keluarrp, 2, '.', ',') . '</td>
              
              <td align=right class=firsttd>' . number_format($salakqty, 2, '.', ',') . '</td>
              <td align=right>' . number_format($harat, 2, '.', ',') . '</td>
              <td align=right>' . number_format($salakrp, 2, '.', ',') . '</td>
              </tr>';


        $totsawalQTY=$totsawalQTY+$sawalQTY;
        $totmasukqty=$totmasukqty+$masukqty;
        $totkeluarqty=$totkeluarqty+$keluarqty;
        $totsalakqty=$totsalakqty+$salakqty;


		$totsalakrp = $totsalakrp+round($salakrp,2);
		$totmasukrp = $totmasukrp+round($masukrp,2);
		$totkeluarrp = $totkeluarrp+round($keluarrp,2);
		$totsawalrp = $totsawalrp+round($sawalrp,2);

	}

}


		echo '<tr class=rowcontent style="background-color:#d4c9c9"> <td colspan=5>' .$hasil->kelompok. '</td>
		  <td align=right class=firsttd >' . number_format($totsawalQTY, 2, '.', ',') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totsawalrp, 2, '.', ',') . '</td>' . "\r\n" . '
		  <td align=right class=firsttd>' . number_format($totmasukqty, 2, '.', ',') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totmasukrp, 2, '.', ',') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totkeluarqty, 2, '.', ',') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totkeluarrp, 2, '.', ',') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totsalakqty, 2, '.', ',') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totsalakrp, 2, '.', ',') . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            </tr>';

        $totalsawalQTY=$totalsawalQTY+$totsawalQTY;
        $totalmasukqty=$totalmasukqty+$totmasukqty;
        $totalkeluarqty=$totalkeluarqty+$totkeluarqty;
        $totalsalakqty=$totalsalakqty+$totsalakqty;

		$totalsalakrp = $totalsalakrp+$totsalakrp;
		$totalmasukrp = $totalmasukrp+$totmasukrp;
		$totalkeluarrp = $totalkeluarrp+$totkeluarrp;
		$totalsawalrp = $totalsawalrp+$totsawalrp;


}

		echo '<tr><td colspan=17>&nbsp;</td></tr><tr class=rowcontent style="background-color:#cccccc"> <td colspan=5 align=center><b>T O T A L</b></td>
		  <td align=right class=firsttd ><b>' . number_format($totalsawalQTY, 2, '.', ',') . '</b></td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right><b>' . number_format($totalsawalrp, 2, '.', ',') . '</b></td>' . "\r\n" . '
		  <td align=right class=firsttd><b>' . number_format($totalmasukqty, 2, '.', ',') . '</b></td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right><b>' . number_format($totalmasukrp, 2, '.', ',') . '</b></td>' . "\r\n" . ' 
		  <td align=right class=firsttd><b>' . number_format($totalkeluarqty, 2, '.', ',') . '</b></td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right><b>' . number_format($totalkeluarrp, 2, '.', ',') . '</b></td>' . "\r\n" . ' 
		  <td align=right class=firsttd><b>' . number_format($totalsalakqty, 2, '.', ',') . '</b></td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right><b>' . number_format($totalsalakrp, 2, '.', ',') . '</b></td>' . "\t\t\t" . '   ' . "\r\n" . '                            </tr>';

?>

