<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$stream = '';

$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi 
		where kodeorg like '".$pt."%' and periode='".$periode."'";

$res = mysql_query($str);
while ($bar = mysql_fetch_array($res)) {
    $tgsampai = $bar['tanggalsampai'];
    $tgmulai = $bar['tanggalmulai'];
}


$stream .= $_SESSION['lang']['laporanstok'] . ': ' . $pt . '-' . $gudang . ':' . $periode . '<br>    ' . "\r\n" . '        <table border=1>' . "\r\n" . '                <tr>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                  <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '                  <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                </tr>' . "\r\n" . '                <tr>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                   <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '                </tr>';


$sql='SELECT * FROM '.$dbname.'.log_5klbarang';
$query = mysql_query($sql);
while ($hasil = mysql_fetch_object($query)) {
$kodekelompok=$hasil->kode;	
$stream .='<tr class=rowcontent><td colspan=16>'.$hasil->kelompok.'</td></tr>';	


if (isset($_GET['unitDt'])) {
	$str = 'select ' . "\r\n" . '                      a.kodeorg, a.hargaratasaldoawal as sawalharat, ' . "\r\n" . '                      a.kodebarang,' . "\r\n" . '                      sum(a.saldoakhirqty) as salakqty,' . "\r\n" . '                      sum(a.nilaisaldoakhir) as salakrp,' . "\r\n" . '                      sum(a.qtymasuk) as masukqty,' . "\r\n" . '                      sum(a.qtykeluar) as keluarqty,' . "\r\n" . '                      sum(qtymasukxharga) as masukrp,' . "\r\n" . '                      sum(qtykeluarxharga) as keluarrp,                      ' . "\r\n" . '                      sum(a.saldoawalqty) as sawalqty,' . "\r\n" . '                      sum(a.nilaisaldoawal) as sawalrp,' . "\r\n" . '                        b.namabarang,b.satuan, a.hargarata    ' . "\r\n" . '                        from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '                        left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '                        on a.kodebarang=b.kodebarang' . "\r\n" . '                      where kodegudang like \'' . $_GET['unitDt'] . '%\' ' . "\r\n" . '                      and periode=\'' . $periode . '\'' . "\r\n" . '                      group by a.kodebarang order by a.kodebarang';
}
else if ($gudang == '') {
	$str = 'select ' . "\r\n" . '                      a.kodeorg, a.hargaratasaldoawal as sawalharat, ' . "\r\n" . '                      a.kodebarang,' . "\r\n" . '                      sum(a.saldoakhirqty) as salakqty,' . "\r\n" . '                      sum(a.nilaisaldoakhir) as salakrp,' . "\r\n" . '                      sum(a.qtymasuk) as masukqty,' . "\r\n" . '                      sum(a.qtykeluar) as keluarqty,' . "\r\n" . '                      sum(qtymasukxharga) as masukrp,' . "\r\n" . '                      sum(qtykeluarxharga) as keluarrp,                      ' . "\r\n" . '                      sum(a.saldoawalqty) as sawalqty,' . "\r\n" . '                      sum(a.nilaisaldoawal) as sawalrp,' . "\r\n" . '                        b.namabarang,b.satuan , a.hargarata   ' . "\r\n" . '                        from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '                        left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '                        on a.kodebarang=b.kodebarang' . "\r\n" . '                      where kodeorg=\'' . $pt . '\' ' . "\r\n" . '                      and periode=\'' . $periode . '\'' . "\r\n" . '                      
		and kelompokbarang=\''.$kodekelompok.'\' group by a.kodebarang order by a.kodebarang';
}
else {
	$str = 'select' . "\r\n" . '                      a.kodeorg,' . "\r\n" . '                      a.kodebarang,' . "\r\n" . '                      a.saldoakhirqty as salakqty,' . "\r\n" . '                      a.hargarata as harat,' . "\r\n" . '                      a.nilaisaldoakhir as salakrp,' . "\r\n" . '                      a.qtymasuk as masukqty,' . "\r\n" . '                      a.qtykeluar as keluarqty,' . "\r\n" . '                      a.qtymasukxharga as masukrp,' . "\r\n" . '                      a.qtykeluarxharga as keluarrp,' . "\r\n" . '                      a.saldoawalqty as sawalqty,' . "\r\n" . '                      a.hargaratasaldoawal as sawalharat,' . "\r\n" . '                      a.nilaisaldoawal as sawalrp,' . "\r\n" . '                  b.namabarang,b.satuan , a.hargarata ' . "\t\t" . ' ' . "\t\t" . '      ' . "\r\n" . '                      from ' . $dbname . '.log_5saldobulanan a' . "\r\n" . '                  left join ' . $dbname . '.log_5masterbarang b' . "\r\n" . '                      on a.kodebarang=b.kodebarang' . "\r\n" . '                      where kodeorg=\'' . $pt . '\' ' . "\r\n" . '                      and periode=\'' . $periode . '\'' . "\r\n" . '                      and kodegudang=\'' . $gudang . '\'' . "\r\n" . '    and kelompokbarang=\''.$kodekelompok.'\'   order by a.kodebarang';
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
	
	$stream .= '<tr>' . "\r\n" . '                          <td>' . $no . '</td>' . "\r\n" . '                          <td>' . $periode . '</td>' . "\r\n" . '                          <td>\'' . $kodebarang . '</td>' . "\r\n" . '                          <td>' . $namabarang . '</td>' . "\r\n" . '                          <td>' . $bar->satuan . '</td>' . "\r\n" . '                           <td align=right class=firsttd>' . number_format($sawalQTY, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($sawalharat, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($sawalrp, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right class=firsttd>' . number_format($masukqty, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($haratmasuk, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($masukrp, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right class=firsttd>' . number_format($keluarqty, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($haratkeluar, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($keluarrp, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right class=firsttd>' . number_format($salakqty, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($harat, 2, '.', '') . '</td>' . "\r\n" . '                           <td align=right>' . number_format($salakrp, 2, '.', '') . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                        </tr>';

        $totsawalQTY=$totsawalQTY+$sawalQTY;
        $totmasukqty=$totmasukqty+$masukqty;
        $totkeluarqty=$totkeluarqty+$keluarqty;
        $totsalakqty=$totsalakqty+$salakqty;


		$totsalakrp = $totsalakrp+round($salakrp,2);
		$totmasukrp = $totmasukrp+round($masukrp,2);
		$totkeluarrp = $totkeluarrp+round($keluarrp,2);
		$totsawalrp = $totsawalrp+round($sawalrp,2);

}

		$stream .= '<tr class=rowcontent style="background-color:#d4c9c9"> <td colspan=5>' .$hasil->kelompok. '</td>
		  <td align=right class=firsttd >' . number_format($totsawalQTY, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totsawalrp, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right class=firsttd>' . number_format($totmasukqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totmasukrp, 2, '.', '') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totkeluarqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totkeluarrp, 2, '.', '') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totsalakqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totsalakrp, 2, '.', '') . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            </tr>';


        $totalsawalQTY=$totalsawalQTY+$totsawalQTY;
        $totalmasukqty=$totalmasukqty+$totmasukqty;
        $totalkeluarqty=$totalkeluarqty+$totkeluarqty;
        $totalsalakqty=$totalsalakqty+$totsalakqty;

		$totalsalakrp = $totalsalakrp+$totsalakrp;
		$totalmasukrp = $totalmasukrp+$totmasukrp;
		$totalkeluarrp = $totalkeluarrp+$totkeluarrp;
		$totalsawalrp = $totalsawalrp+$totsawalrp;

}

		$stream .= '<tr class=rowcontent style="background-color:#d4c9c9"> <td colspan=5 align=center> T O T A L </td>
		  <td align=right class=firsttd >' . number_format($totalsawalQTY, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totalsawalrp, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right class=firsttd>' . number_format($totalmasukqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '
		  <td align=right>' . number_format($totmasukrp, 2, '.', '') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totalkeluarqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totkeluarrp, 2, '.', '') . '</td>' . "\r\n" . ' 
		  <td align=right class=firsttd>' . number_format($totalsalakqty, 2, '.', '') . '</td>' . "\r\n" . '
		  <td align=right></td>' . "\r\n" . '                               
		  <td align=right>' . number_format($totalsalakrp, 2, '.', '') . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            </tr>';




$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'MaterialBalanceWPrice';

if (0 < strlen($stream)) {
	if ($handle = opendir('tempExcel')) {
		while (false !== $file = readdir($handle)) {
			if (($file != '.') && ($file != '..')) {
				@unlink('tempExcel/' . $file);
			}
		}

		closedir($handle);
	}

	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

	if (!fwrite($handle, $stream)) {
		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
		exit();
	}
	else {
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
	}

	closedir($handle);
}

?>
