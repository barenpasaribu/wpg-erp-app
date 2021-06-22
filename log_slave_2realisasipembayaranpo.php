<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['pt'] == '' ? $pt = $_GET['pt'] : $pt = $_POST['pt'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['periode1'] == '' ? $periode1 = $_GET['periode1'] : $periode1 = $_POST['periode1'];
$_POST['supplier'] == '' ? $supplier = $_GET['supplier'] : $supplier = $_POST['supplier'];
$_POST['kelompok'] == '' ? $kelompok = $_GET['kelompok'] : $kelompok = $_POST['kelompok'];
$_POST['namasupplier'] == '' ? $namasupplier = $_GET['namasupplier'] : $namasupplier = $_POST['namasupplier'];
if (($periode == '') || ($periode1 == '')) {
	exit('Error: ' . $_SESSION['lang']['silakanisi'] . ' : ' . $_SESSION['lang']['periode']);
}

$str = 'select * from ' . $dbname . '.log_po_vw' . "\r\n" . '    where nopo!=\'\' and substr(tanggal,1,7) between \'' . $periode . '\' and \'' . $periode1 . '\' ' . "\r\n" . '        and nopo like \'%' . $pt . '%\' ' . "\r\n" . '        and kodesupplier like \'%' . $supplier . '%\' ' . "\r\n" . '        and kodebarang like \'' . $kelompok . '%\'' . "\r\n" . '        and namasupplier like \'%' . $namasupplier . '%\' ' . "\r\n" . '    order by tanggal,nopo,kodebarang';

#exit(mysql_error($conn));
($que = mysql_query($str)) || true;

while ($row = mysql_fetch_assoc($que)) {
	$key = strtoupper($row['nopo']);
	$adatagihan[$key] = 0;
	$datapo[$key][$row['kodebarang']]['nopo'] = $key;
	$datapo[$key][$row['kodebarang']]['kodebarang'] = $row['kodebarang'];
	$datapo[$key][$row['kodebarang']]['namabarang'] = $row['namabarang'];

	if ($proses == 'excel') {
		$datapo[$key][$row['kodebarang']]['tanggal'] = $row['tanggal'];
	}
	else {
		$datapo[$key][$row['kodebarang']]['tanggal'] = tanggalnormal($row['tanggal']);
	}

	$datapo[$key][$row['kodebarang']]['pesan'] = number_format($row['jumlahpesan']);
	$datapo[$key][$row['kodebarang']]['matauang'] = $row['matauang'];
	$datapo[$key][$row['kodebarang']]['kurs'] = number_format($row['kurs']);
	
	if($row['ppn']!=0){
		$ppn =$row['jumlahpesan'] * $row['hargasatuan']*0.1;
		$datapo[$key][$row['kodebarang']]['harga'] = number_format(($row['jumlahpesan'] * $row['hargasatuan']) + $ppn);
	}else{
		$datapo[$key][$row['kodebarang']]['harga'] = number_format($row['jumlahpesan'] * $row['hargasatuan']);
	}
	
	//$datapo[$key][$row['kodebarang']]['harga'] = number_format(($row['jumlahpesan'] * $row['hargasatuan']) + $row['ppn']);
	$datapo[$key][$row['kodebarang']]['namasupplier'] = $row['namasupplier'];
}

$str = 'select * from '.$dbname.'.log_transaksi_vw a inner join log_po_vw b on a.nopo=b.nopo and a.kodebarang=b.kodebarang   where a.nopo!=\'\' and a.nopo like \'%' . $pt . '%\' and idsupplier like \'%' . $supplier . '%\' and a.kodebarang like \'' . $kelompok . '%\'';

#exit(mysql_error($conn));
($que = mysql_query($str)) || true;

while ($row = mysql_fetch_assoc($que)) {
	$key = strtoupper($row['nopo']);
	$databa[$key][$row['kodebarang']]['notransaksi'] .= $row['notransaksi'].'<br>';
    if ('excel' === $proses) {
        $databa[$key][$row['kodebarang']]['tanggal'] .= $row['tanggal'].'<br>';
    } else {
        $databa[$key][$row['kodebarang']]['tanggal'] .= tanggalnormal($row['tanggal']).'<br>';
    }

    if (6 === $row['tipetransaksi']) {
        $databa[$key][$row['kodebarang']]['jumlah'] .= '-'.number_format($row['jumlah']).'<br>';
        if($row['ppn']>0){
        	$databa[$key][$row['kodebarang']]['hartot'] .= '-'.number_format(($row['jumlah']*$row['hargasatuan'])+(($row['jumlah']*$row['hargasatuan'])*0.1)).'<br>';
    	}else{
    		$databa[$key][$row['kodebarang']]['hartot'] .= '-'.number_format(($row['jumlah']*$row['hargasatuan'])).'<br>';
    	}
    } else {
        $databa[$key][$row['kodebarang']]['jumlah'] .= number_format($row['jumlah']).'<br>';
        if($row['ppn']>0){
        	$databa[$key][$row['kodebarang']]['hartot'] .= number_format(($row['jumlah']*$row['hargasatuan'])+(($row['jumlah']*$row['hargasatuan'])*0.1)).'<br>';
    	}else{
    		$databa[$key][$row['kodebarang']]['hartot'] .= number_format(($row['jumlah']*$row['hargasatuan'])).'<br>';
    	}
    }
}

$brsdt = count($datapo);
$brdr = 0;
$bgcoloraja = '';

if ($proses == 'excel') {
	$bgcoloraja = 'bgcolor=#DEDEDE ';
	$brdr = 1;
}

$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable>' . "\r\n" . '<thead class=rowheader>';
$tab .= '<tr>';
$tab .= '<td ' . $bgcoloraja . ' colspan=9 align=center>' . $_SESSION['lang']['po'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['bapb'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=4 align=center>' . $_SESSION['lang']['tagihan'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' colspan=3 align=center>' . $_SESSION['lang']['pembayaran'] . '</td>';
$tab .= '</tr>';
$tab .= '<tr>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['nopo'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['kodebarang'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['namabarang'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['jmlhPesan'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['matauang'] . ' PO</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['kurs'] . ' </td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['harga'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['namasupplier'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['notransaksi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['jumlah'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['harga'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['noinvoice'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['jatuhtempo'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['nilaiinvoice'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['notransaksi'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['tanggal'] . '</td>';
$tab .= '<td ' . $bgcoloraja . ' align=center>' . $_SESSION['lang']['jumlah'] . '</td>';
$tab .= '</tr></thead><tbody>';

$lastnopo='';
foreach ($datapo as $nPO => $vv) {
	foreach ($vv as $kBarang => $yy) {
		$tab .= '';
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $datapo[$nPO][$kBarang]['nopo'] . '</td>';
		$tab .= '<td align=center>' . $datapo[$nPO][$kBarang]['kodebarang'] . '</td>';
		$tab .= '<td>' . $datapo[$nPO][$kBarang]['namabarang'] . '</td>';
		$tab .= '<td>' . $datapo[$nPO][$kBarang]['tanggal'] . '</td>';
		$tab .= '<td align=right>' . $datapo[$nPO][$kBarang]['pesan'] . '</td>';
		$tab .= '<td align=center>' . $datapo[$nPO][$kBarang]['matauang'] . '</td>';
		$tab .= '<td align=right>' . $datapo[$nPO][$kBarang]['kurs'] . '</td>';
		$tab .= '<td align=right>' . $datapo[$nPO][$kBarang]['harga'] . '</td>';
		$tab .= '<td>' . $datapo[$nPO][$kBarang]['namasupplier'] . '</td>';
		$tab .= '<td>' . $databa[$nPO][$kBarang]['notransaksi'] . '</td>';
		$tab .= '<td align=center>' . $databa[$nPO][$kBarang]['tanggal'] . '</td>';
		$tab .= '<td align=right>' . $databa[$nPO][$kBarang]['jumlah'] . '</td>';
		$tab .= '<td align=right>' . $databa[$nPO][$kBarang]['hartot'] . '</td>';

		if($lastnopo!=$datapo[$nPO][$kBarang]['nopo']){
			$str="SELECT kodebarang FROM log_po_vw WHERE nopo='".$datapo[$nPO][$kBarang]['nopo']."'";
			$qry=mysql_query($str);
			$jmlrow=mysql_num_rows($qry);

			$str1="select noinvoice, nopo, tanggal, jatuhtempo, SUM(nilaiinvoice) AS nilaiinvoice, SUM(nilaippn) AS nilaippn from ".$dbname.".keu_tagihanht where nopo='".$datapo[$nPO][$kBarang]['nopo']."'";
			$qry1=mysql_query($str1);
			$res=mysql_fetch_assoc($qry1);

			$tab .= '<td align=right rowspan='.$jmlrow.'>'. $res['noinvoice'] . '</td>';
			$tab .= '<td align=right rowspan='.$jmlrow.'>'. $res['tanggal'] . '</td>';
			$tab .= '<td align=right rowspan='.$jmlrow.'>'. $res['jatuhtempo'] . '</td>';
			$tab .= '<td align=right rowspan='.$jmlrow.'>'. number_format($res['nilaiinvoice']+$res['nilaippn']) . '</td>';

			$str1="select a.nodok, a.keterangan1, a.notransaksi, a.tipetransaksi, sum(a.jumlah) as jumlah, b.tanggal from ".$dbname.".keu_kasbankdt a inner join ".$dbname.".keu_kasbankht b on a.notransaksi=b.notransaksi where nodok='".$datapo[$nPO][$kBarang]['nopo']."'";
			$qry1=mysql_query($str1);
			$res=mysql_fetch_assoc($qry1);

			$tab .= '<td align=right rowspan='.$jmlrow.'>'. $res['notransaksi'] . '</td>';
			$tab .= '<td align=right rowspan='.$jmlrow.'>'. $res['tanggal'] . '</td>';
			$tab .= '<td align=right rowspan='.$jmlrow.'>'. number_format($res['jumlah']) . '</td>';

		}

		$tab .= '</tr>';

	$lastnopo=$datapo[$nPO][$kBarang]['nopo'];
	}
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'realisasipembayaranpo_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
	break;
}

?>
