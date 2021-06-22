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

switch ($param['proses']) {
case 'getPeriode':
	$optKlmpk = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sPrd = 'select distinct periode from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '                  where kodeorg=\'' . $param['gdngId'] . '\' and tutupbuku=0 ' . "\r\n" . '                  order by periode desc';

	#exit(mysql_error($conn));
	($qPrd = mysql_query($sPrd)) || true;

	while ($rPrd = mysql_fetch_assoc($qPrd)) {
		$optKlmpk .= '<option value=' . $rPrd['periode'] . '>' . $rPrd['periode'] . '</option>';
	}

	echo $optKlmpk;
	break;

case 'getKlmmpkBrg':
	$optKlmpk = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sPrd = 'select distinct left(kodebarang,3) as periode from ' . $dbname . '.log_5saldobulanan ' . "\r\n" . '                  where kodegudang=\'' . $param['gdngId'] . '\' and periode=\'' . $param['periodeGdng'] . '\' ' . "\r\n" . '                  order by left(kodebarang,3) desc';

	#exit(mysql_error($conn));
	($qPrd = mysql_query($sPrd)) || true;

	while ($rPrd = mysql_fetch_assoc($qPrd)) {
		$optKlmpk .= '<option value=' . $rPrd['periode'] . '>' . $rPrd['periode'] . '-' . $optKlmk[$rPrd['periode']] . '</option>';
	}

	echo $optKlmpk;
	break;

case 'preview':
	$wer = '';

	if ($param['kdBrg'] != '') {
		$wer = 'and kodebarang=\'' . $param['kdBrg'] . '\'';
	}
	else {
		exit('error: kodebarang tidak boleh kosong');
	}

	if ($param['periodeGdng'] == '') {
		exit('error: periode tidak boleh kosong');
	}

	if ($param['gdngId'] == '') {
		exit('error: periode tidak boleh kosong');
	}

	$stransaksi = 'select sum(jumlah) as jumlah,kodebarang,kodegudang from ' . $dbname . '.log_transaksi_vw' . "\r\n" . '                    where left(tanggal,7)=\'' . $param['periodeGdng'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'' . "\r\n" . '                    ' . $wer . ' and tipetransaksi<5 and statussaldo=1' . "\r\n" . '                    group by kodebarang';

	#exit(mysql_error($conn));
	($qtransaksi = mysql_query($stransaksi)) || true;

	while ($rTransaksi = mysql_fetch_assoc($qtransaksi)) {
		$dtBrg[$rTransaksi['kodebarang']] = $rTransaksi['kodebarang'];
		$dtMasuk[$rTransaksi['kodebarang']] = $rTransaksi['jumlah'];
	}

	$stransaksi = 'select sum(jumlah) as jumlah,kodebarang,kodegudang from ' . $dbname . '.log_transaksi_vw' . "\r\n" . '                    where left(tanggal,7)=\'' . $param['periodeGdng'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'' . "\r\n" . '                    ' . $wer . ' and tipetransaksi>4 and statussaldo=1' . "\r\n" . '                    group by kodebarang';

	#exit(mysql_error($conn));
	($qtransaksi = mysql_query($stransaksi)) || true;

	while ($rTransaksi = mysql_fetch_assoc($qtransaksi)) {
		$dtBrg[$rTransaksi['kodebarang']] = $rTransaksi['kodebarang'];
		$dtKeluar[$rTransaksi['kodebarang']] = $rTransaksi['jumlah'];
	}

	$sSaldo = 'select distinct kodebarang,saldoakhirqty,hargarata,qtymasuk,qtykeluar,saldoawalqty' . "\r\n" . '                from ' . $dbname . '.log_5saldobulanan where ' . "\r\n" . '                kodegudang=\'' . $param['gdngId'] . '\' ' . $wer . "\r\n" . '                and periode=\'' . $param['periodeGdng'] . '\'';

	#exit(mysql_error($conn));
	($qSaldo = mysql_query($sSaldo)) || true;

	while ($rSaldo = mysql_fetch_assoc($qSaldo)) {
		$dtBrg[$rSaldo['kodebarang']] = $rSaldo['kodebarang'];
		$drSalMasuk[$rSaldo['kodebarang']] = $rSaldo['qtymasuk'];
		$drSalAwal[$rSaldo['kodebarang']] = $rSaldo['saldoawalqty'];
		$drSalKeluar[$rSaldo['kodebarang']] = $rSaldo['qtykeluar'];
		$drHrgRata[$rSaldo['kodebarang']] = $rSaldo['hargarata'];
		$drSalAkhir[$rSaldo['kodebarang']] = $rSaldo['saldoakhirqty'];
	}

	$saldoAKhir = ($dtMasuk[$param['kdBrg']] + $drSalAwal[$param['kdBrg']]) - $dtKeluar[$param['kdBrg']];

	if ($dtMasuk[$param['kdBrg']] == '') {
		$dtMasuk[$param['kdBrg']] = 0;
	}

	if ($dtKeluar[$param['kdBrg']] == '') {
		$dtKeluar[$param['kdBrg']] = 0;
	}

	$srekal = 'update ' . $dbname . '.log_5saldobulanan set' . "\r\n" . '                saldoakhirqty=\'' . $saldoAKhir . '\',' . "\r\n" . '                nilaisaldoakhir=\'' . ($saldoAKhir * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtymasuk=\'' . $dtMasuk[$param['kdBrg']] . '\',' . "\r\n" . '                qtymasukxharga=\'' . ($dtMasuk[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtykeluar=\'' . $dtKeluar[$param['kdBrg']] . '\',' . "\r\n" . '                qtykeluarxharga=\'' . ($dtKeluar[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\'' . "\r\n" . '                where kodegudang=\'' . $param['gdngId'] . '\' ' . $wer . ' and periode=\'' . $param['periodeGdng'] . '\'';

	if (!mysql_query($srekal)) {
		exit('error:' . "\n" . ' Rekalkulasi tidak berhasil___' . $srekal);
	}
	else {
		$supdatedata = 'update ' . $dbname . '.log_5masterbarangdt set saldoqty=\'' . $saldoAKhir . '\' where' . "\r\n" . '            kodebarang=\'' . $param['kdBrg'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'';

		if (!mysql_query($supdatedata)) {
			exit('error:' . "\n" . ' Rekalkulasi tidak berhasil __' . $supdatedata);
		}

		$tab .= '<table>';
		$tab .= '<tr><td>' . $_SESSION['lang']['kodebarang'] . '</td><td>:</td><td>' . $param['kdBrg'] . '</td></tr>';
		$tab .= '<tr><td>' . $_SESSION['lang']['namabarang'] . '</td><td>:</td><td>' . $optBrg[$param['kdBrg']] . '</td></tr>';
		$tab .= '<tr><td>' . $_SESSION['lang']['kodegudang'] . '</td><td>:</td><td>' . $param['gdngId'] . '</td></tr></table>';
		$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
		$tab .= '<tr class=rowheader><td colspan=4>' . $_SESSION['lang']['sebelum'] . '</td></tr>';
		$tab .= '<tr class=rowheader>';
		$tab .= '<td>' . $_SESSION['lang']['saldoawal'] . '</td>';
		$tab .= '<td>' . $_SESSION['lang']['masuk'] . '</td>';
		$tab .= '<td>' . $_SESSION['lang']['keluar'] . '</td>';
		$tab .= '<td>' . $_SESSION['lang']['saldoakhir'] . '</td></tr>';
		$lstBrg = $param['kdBrg'];
		$salAkhir[$lstBrg] = ($drSalAwal[$lstBrg] + $dtMasuk[$lstBrg]) - $dtKeluar[$lstBrg];
		$tab .= '<tr  class=rowcontent>';
		$tab .= '<td align=right>' . $drSalAwal[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $drSalMasuk[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $drSalKeluar[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $drSalAkhir[$lstBrg] . '</td>';
		$tab .= '</tr>';
		$tab .= '<tr class=rowheader><td colspan=4>' . $_SESSION['lang']['sesudah'] . '</td></tr>';
		$tab .= '<tr  class=rowcontent>';
		$tab .= '<td align=right>' . $drSalAwal[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $dtMasuk[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $dtKeluar[$lstBrg] . '</td>';
		$tab .= '<td align=right>' . $salAkhir[$lstBrg] . '</td>';
		$tab .= '</tr>';
		$tab .= '</table>';
		echo $tab . '#####1';
	}

	break;

case 'getNmbrg':
	if ($param['periodeGdng'] == '') {
		exit('error: periode tidak boleh kosong');
	}

	if ($param['gdngId'] == '') {
		exit('error: periode tidak boleh kosong');
	}

	echo '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '                <td>No.</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                </tr><tbody>' . "\r\n" . '                ';
	$sSupplier = 'select distinct a.namabarang,b.kodebarang from ' . $dbname . '.log_5masterbarang a' . "\r\n" . '                     inner join ' . $dbname . '.log_transaksi_vw b on a.kodebarang=b.kodebarang' . "\r\n" . '                     where namabarang like \'%' . $param['nmBarang'] . '%\' or b.kodebarang like \'%' . $param['nmBarang'] . '%\'  ' . "\r\n" . '                      and left(tanggal,7)=\'' . $param['periodeGdng'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'' . "\r\n" . '                     order by namabarang asc';

	#exit(mysql_error($conn));
	($qSupplier = mysql_query($sSupplier)) || true;

	while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
		$no += 1;
		echo '<tr class=rowcontent onclick="setData(\'' . $rSupplier['kodebarang'] . '\',\'' . $rSupplier['namabarang'] . '\')">' . "\r\n" . '                 <td>' . $no . '</td>' . "\r\n" . '                 <td>' . $rSupplier['kodebarang'] . '</td>' . "\r\n" . '                 <td>' . $rSupplier['namabarang'] . '</td>' . "\r\n" . '            </tr>';
	}

	echo '</tbody></table></div>';
	break;

case 'preview2':
	if ($param['periodeGdng2'] == '') {
		exit('error:' . "\n" . ' warehouse period can\'t empty!!');
	}

	if ($param['gdngId2'] == '') {
		exit('error:' . "\n" . ' warehouse can\'t empty!!');
	}

	$tab .= '<table cellpadding=1 cellspacing=1 class=sortable border=0><thead><tr>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['saldoawal'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['masuk'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['keluar'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['saldoakhir'] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['action'] . '</td>' . "\r\n" . '                </tr></thead><tbody>';
	$sdata = 'select distinct kodebarang,saldoakhirqty,saldoawalqty,qtymasuk,qtykeluar,periode,(saldoawalqty+qtymasuk-qtykeluar) as pembanding from ' . "\r\n" . '                   ' . $dbname . '.log_5saldobulanan where kodegudang=\'' . $param['gdngId2'] . '\' and  periode=\'' . $param['periodeGdng2'] . '\' ' . "\r\n" . '                   and (saldoawalqty+qtymasuk-qtykeluar-saldoakhirqty)!=0 ';

	#exit(mysql_error($conn));
	($qdata = mysql_query($sdata)) || true;

	while ($rdata = mysql_fetch_assoc($qdata)) {
		if (number_format($rdata['saldoakhirqty'], 2) != number_format($rdata['pembanding'], 2)) {
			$Er += 1;
			$tab .= '<tr class=rowcontent id=guaikutaja_' . $Er . '>' . "\r\n" . '                   <td >' . $rdata['kodebarang'] . '</td>';
			$tab .= '<td>' . $optBrg[$rdata['kodebarang']] . '</td>';
			$tab .= '<td align=right id=sawal_' . $Er . '>' . $rdata['saldoawalqty'] . '</td>';
			$tab .= '<td align=right id=qtymsk_' . $Er . '>' . $rdata['qtymasuk'] . '</td>';
			$tab .= '<td align=right id=qtyklr_' . $Er . '>' . $rdata['qtykeluar'] . '</td>';
			$tab .= '<td align=right id=salak_' . $Er . '>' . $rdata['saldoakhirqty'] . '__' . $rdata['pembanding'] . '</td>' . "\r\n" . '                   <td><button class=mybutton onclick=reklasDt(\'' . $rdata['kodebarang'] . '\',\'' . $param['gdngId2'] . '\',\'' . $rdata['periode'] . '\',\'' . $Er . '\') >' . $_SESSION['lang']['rekalkulasi'] . '</button></td>' . "\r\n" . '                   </tr>';
		}
	}

	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'reklasData':
	$stransaksi = 'select sum(jumlah) as jumlah,kodebarang,kodegudang from ' . $dbname . '.log_transaksi_vw' . "\r\n" . '                    where left(tanggal,7)=\'' . $param['periodeGdng'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'' . "\r\n" . '                    and kodebarang=\'' . $param['kdBrg'] . '\' and tipetransaksi<5 and statussaldo=1' . "\r\n" . '                    group by kodebarang';

	#exit(mysql_error($conn));
	($qtransaksi = mysql_query($stransaksi)) || true;

	while ($rTransaksi = mysql_fetch_assoc($qtransaksi)) {
		$dtBrg[$rTransaksi['kodebarang']] = $rTransaksi['kodebarang'];
		$dtMasuk[$rTransaksi['kodebarang']] = $rTransaksi['jumlah'];
	}

	$stransaksi = 'select sum(jumlah) as jumlah,kodebarang,kodegudang from ' . $dbname . '.log_transaksi_vw' . "\r\n" . '                    where left(tanggal,7)=\'' . $param['periodeGdng'] . '\' and kodegudang=\'' . $param['gdngId'] . '\'' . "\r\n" . '                    and kodebarang=\'' . $param['kdBrg'] . '\' and tipetransaksi>4 and statussaldo=1' . "\r\n" . '                    group by kodebarang';

	#exit(mysql_error($conn));
	($qtransaksi = mysql_query($stransaksi)) || true;

	while ($rTransaksi = mysql_fetch_assoc($qtransaksi)) {
		$dtBrg[$rTransaksi['kodebarang']] = $rTransaksi['kodebarang'];
		$dtKeluar[$rTransaksi['kodebarang']] = $rTransaksi['jumlah'];
	}

	$sSaldo = 'select distinct kodebarang,saldoakhirqty,hargarata,qtymasuk,qtykeluar,saldoawalqty,(saldoawalqty+qtymasuk-qtykeluar) as pembanding,' . "\r\n" . '                hargaratasaldoawal' . "\r\n" . '                from ' . $dbname . '.log_5saldobulanan where ' . "\r\n" . '                kodegudang=\'' . $param['gdngId'] . '\' and kodebarang=\'' . $param['kdBrg'] . '\'' . "\r\n" . '                and periode=\'' . $param['periodeGdng'] . '\'';

	#exit(mysql_error($conn));
	($qSaldo = mysql_query($sSaldo)) || true;

	while ($rSaldo = mysql_fetch_assoc($qSaldo)) {
		$dtBrg[$rSaldo['kodebarang']] = $rSaldo['kodebarang'];
		$drSalMasuk[$rSaldo['kodebarang']] = $rSaldo['qtymasuk'];
		$drSalAwal[$rSaldo['kodebarang']] = $rSaldo['saldoawalqty'];
		$drSalKeluar[$rSaldo['kodebarang']] = $rSaldo['qtykeluar'];
		$drHrgRata[$rSaldo['kodebarang']] = $rSaldo['hargarata'];
		$drSalAkhir[$rSaldo['kodebarang']] = $rSaldo['saldoakhirqty'];
		$dtPmbnding[$rSaldo['kodebarang']] = abs($rSaldo['pembanding']);
		$dtHrgRata[$rSaldo['kodebarang']] = $rSaldo['hargaratasaldoawal'];
	}

	$saldoAKhir = ($dtMasuk[$param['kdBrg']] + $drSalAwal[$param['kdBrg']]) - $dtKeluar[$param['kdBrg']];

	if ($dtMasuk[$param['kdBrg']] == '') {
		$dtMasuk[$param['kdBrg']] = 0;
	}

	if ($dtKeluar[$param['kdBrg']] == '') {
		$dtKeluar[$param['kdBrg']] = 0;
	}

	if ($saldoAKhir < 0) {
		$saldoAKhir2 = ($dtMasuk[$param['kdBrg']] + $drSalAwal[$param['kdBrg']] + $dtPmbnding[$param['kdBrg']]) - $dtKeluar[$param['kdBrg']];
		$srekal = 'update ' . $dbname . '.log_5saldobulanan set' . "\r\n" . '                saldoakhirqty=\'' . $saldoAKhir2 . '\',' . "\r\n" . '                nilaisaldoakhir=\'' . ($saldoAKhir2 * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtymasuk=\'' . $dtMasuk[$param['kdBrg']] . '\',' . "\r\n" . '                qtymasukxharga=\'' . ($dtMasuk[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtykeluar=\'' . $dtKeluar[$param['kdBrg']] . '\',' . "\r\n" . '                qtykeluarxharga=\'' . ($dtKeluar[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                saldoawalqty=\'' . ($dtPmbnding[$param['kdBrg']] + $drSalAwal[$param['kdBrg']]) . '\',' . "\r\n" . '                nilaisaldoawal=\'' . (($dtPmbnding[$param['kdBrg']] + $drSalAwal[$param['kdBrg']]) * $dtHrgRata[$param['kdBrg']]) . '\'' . "\r\n" . '                where kodegudang=\'' . $param['gdngId'] . '\' and kodebarang=\'' . $param['kdBrg'] . '\' and periode=\'' . $param['periodeGdng'] . '\'';

		if (!mysql_query($srekal)) {
			exit('error:' . "\n" . ' Rekalkulasi tidak berhasil' . $srekal);
		}

		$saldoAKhir = $saldoAKhir2;
	}
	else {
		$srekal = 'update ' . $dbname . '.log_5saldobulanan set' . "\r\n" . '                saldoakhirqty=\'' . $saldoAKhir . '\',' . "\r\n" . '                nilaisaldoakhir=\'' . ($saldoAKhir * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtymasuk=\'' . $dtMasuk[$param['kdBrg']] . '\',' . "\r\n" . '                qtymasukxharga=\'' . ($dtMasuk[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\',' . "\r\n" . '                qtykeluar=\'' . $dtKeluar[$param['kdBrg']] . '\',' . "\r\n" . '                qtykeluarxharga=\'' . ($dtKeluar[$param['kdBrg']] * $drHrgRata[$param['kdBrg']]) . '\'' . "\r\n" . '                where kodegudang=\'' . $param['gdngId'] . '\' and kodebarang=\'' . $param['kdBrg'] . '\' and periode=\'' . $param['periodeGdng'] . '\'';
	}

	if (!mysql_query($srekal)) {
		exit('error:' . "\n" . ' Rekalkulasi tidak berhasil' . $srekal);
	}

	echo $saldoAKhir . '####' . $dtMasuk[$param['kdBrg']] . '####' . $dtKeluar[$param['kdBrg']] . '####' . $drSalAwal[$param['kdBrg']];
	break;
}

?>
