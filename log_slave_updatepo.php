<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$param = $_POST;

if (isset($_GET['proses']) != '') {
	$param['proses'] = $_GET['proses'];
}

$optKlmk = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$whr = 'nopo=\'' . $param['nopo'] . '\'';
$optStat = makeOption($dbname, 'log_poht', 'nopo,statuspo', $whr);

switch ($param['proses']) {
case 'preview':
	if ($optStat[$param['nopo']] != 2) {
		exit('error: PO Sudah di Terima di gudang');
	}

	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
	$tab .= '<tr class=rowheader  align=center>';
	$tab .= '<td rowspan=2>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '         ' . "\r\n" . '        <td colspan=4 align=center>Data Saat Ini</td>' . "\r\n" . '        <td colspan=6 align=center>Menjadi</td></tr>';
	$tab .= '<tr  align=center><td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '               <td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['action'] . '</td></tr>';
	$tab .= '</tr></thead><tbody>';
	$sSaldo = 'select distinct *' . "\r\n" . '                from ' . $dbname . '.log_podt where nopo=\'' . $param['nopo'] . '\'';

	#exit(mysql_error($conn));
	($qSaldo = mysql_query($sSaldo)) || true;

	while ($rSaldo = mysql_fetch_assoc($qSaldo)) {
		$nod += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td id=nopp_' . $nod . '>' . $rSaldo['nopp'] . '</td>';
		$tab .= '<td id=kdBrg_' . $nod . '>' . $rSaldo['kodebarang'] . '</td>';
		$tab .= '<td id=nmBrg_' . $nod . '>' . $optBrg[$rSaldo['kodebarang']] . '</td>';
		$tab .= '<td align=right  id=jmlhPsn_' . $nod . '>' . $rSaldo['jumlahpesan'] . '</td>';
		$tab .= '<td id=Sat_' . $nod . '>' . $rSaldo['satuan'] . '</td>';
		$tab .= '<td><input type=text id=kdBrgBaru_' . $nod . ' value=\'\' readonly class=myinputtext style=width:100px; onclick=getKdBarang(\'' . $nod . '\',event) /></td>';
		$tab .= '<td id=nmBrgBaru_' . $nod . '></td>';
		$tab .= '<td id=satBaru_' . $nod . '></td>';
		$tab .= '<td align=center><img class=resicon src=\'images/save.png\' onclick=updatePo(\'' . $nod . '\') /></td>';
		$tab .= '</tr>';
	}

	$tab .= '</tbody></table><input type=hidden id=nopoUp value=\'' . $param['nopo'] . '\'>';
	echo $tab;
	break;

case 'getForm':
	$tab .= '<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>' . "\r\n" . '             Find<input type=text class=myinputtext id=no_brg>' . "\r\n" . '             <button class=mybutton onclick=findBrg(' . $param['rowKe'] . ')>Find</button></fieldset><div id=container></div>';
	echo $tab;
	break;

case 'satDt':
	$optSat .= '<select id=satUpdate_' . $param['rowke'] . '><option value=\'' . $param['satuan'] . '\'>' . $param['satuan'] . '</option>';
	$sSat = 'select distinct satuankonversi from ' . $dbname . '.log_5stkonversi ' . "\r\n" . '                  where kodebarang=\'' . $param['kdBarang'] . '\'';

	#exit(mysql_error($conn));
	($qSat = mysql_query($sSat)) || true;

	while ($rSat = mysql_fetch_assoc($qSat)) {
		$optSat .= '<option value=\'' . $rSat['satuankonversi'] . '\'>' . $rSat['satuankonversi'] . '</option>';
	}

	$optSat .= '</select>';
	echo $optSat;
	break;

case 'updateDt':
	$supdate = 'update ' . $dbname . '.log_podt set kodebarang=\'' . $param['kdBarang'] . '\' ' . "\r\n" . '                   where kodebarang=\'' . $param['oldKdBrg'] . '\'  and nopp=\'' . $param['nopp'] . '\'';

	if (!mysql_query($supdate)) {
		echo ' Gagal,' . addslashes(mysql_error($conn)) . '__' . $supdate;
	}
	else {
		$supdate = 'update ' . $dbname . '.log_prapodt set kodebarang=\'' . $param['kdBarang'] . '\' ' . "\r\n" . '                   where kodebarang=\'' . $param['oldKdBrg'] . '\'  and nopp=\'' . $param['nopp'] . '\'';

		if (!mysql_query($supdate)) {
			echo ' Gagal,' . addslashes(mysql_error($conn)) . '__' . $supdate;
		}
	}

	break;

case 'cariBarangDlmDtBs':
	$txtfind = $_POST['txtfind'];
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' or kodebarang like \'%' . $txtfind . '%\' ';

	if ($res = mysql_query($str)) {
		echo "\r\n" . '          <fieldset>' . "\r\n" . '        <legend>Result</legend>' . "\r\n" . '        <div style="overflow:auto; height:300px;" >' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                 <tr class=rowheader>' . "\r\n" . '                                 <td class=firsttd>' . "\r\n" . '                                 No.' . "\r\n" . '                                 </td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                                 <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '                                 </tr>' . "\r\n" . '                                 </thead>' . "\r\n" . '                                 <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$saldoqty = 0;
			$str1 = 'select sum(saldoqty) as saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n" . '                                       and kodeorg=\'' . $_SESSION['empl']['kodeorganisasi'] . '\'';
			$res1 = mysql_query($str1);

			while ($bar1 = mysql_fetch_object($res1)) {
				$saldoqty = $bar1->saldoqty;
			}

			$qtynotpostedin = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                                           and a.tipetransaksi<5' . "\r\n" . '                                           and a.post=0' . "\r\n" . '                                           group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotpostedin = $bar2->jumlah;
			}

			if ($qtynotpostedin == '') {
				$qtynotpostedin = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n" . '                                           and a.tipetransaksi>4' . "\r\n" . '                                           and a.post=0' . "\r\n" . '                                           group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotposted = $bar2->jumlah;
			}

			if ($qtynotposted == '') {
				$qtynotposted = 0;
			}

			$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;

			if ($bar->inactive == 1) {
				echo '<tr bgcolor=\'red\' style=\'cursor:pointer;\'  title=\'Inactive\' >';
				$bar->namabarang = $bar->namabarang . ' [Inactive]';
				$bgr = ' bgcolor=\'red\'';
			}
			else {
				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setBrg(\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\',\'' . $param['rowKe'] . '\')" title=\'Click\' >';
			}

			echo ' <td class=firsttd >' . $no . '</td>' . "\r\n" . '                                          <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                                          <td>' . $bar->namabarang . '</td>' . "\r\n" . '                                          <td>' . $bar->satuan . '</td>' . "\r\n" . '                                          <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n" . '                                         </tr>';
		}

		echo '</tbody>' . "\r\n" . '                                  <tfoot>' . "\r\n" . '                                  </tfoot>' . "\r\n" . '                                  </table></div></fieldset>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
