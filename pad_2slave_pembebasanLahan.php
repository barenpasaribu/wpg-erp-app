<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$proses = $_GET['proses'];

if ($proses == 'excel') {
	$unit = $_GET['kdUnit'];
}
else {
	$unit = $_POST['kdUnit'];
}

$dibuka = 0;
$str = 'select sum(hasilkerja) as luas from ' . $dbname . '.kebun_perawatan_dan_spk_vw where kodekegiatan=\'126010201\'' . "\r\n" . '            and unit=\'' . $unit . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$dibuka = $bar->luas;
}

if ($dibuka == 0) {
	$str = 'select sum(hasilkerja) as luas from ' . $dbname . '.kebun_perawatan_dan_spk_vw where kodekegiatan=\'126010301\'' . "\r\n" . '                    and unit=\'' . $unit . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$dibuka = $bar->luas;
	}
}

if ($dibuka == '') {
	$dibuka = 0;
}

$stream = '';
$str1 = 'select a.*,b.nama,b.alamat,b.desa,c.namakaryawan from ' . $dbname . '.pad_lahan a' . "\r\n" . '            left join ' . $dbname . '.pad_5masyarakat b on a.pemilik=b.padid ' . "\r\n" . '            left join ' . $dbname . '.datakaryawan c on a.updateby=c.karyawanid    ' . "\r\n" . '            where posting=0 and unit=\'' . $unit . '\' order by b.nama,b.desa limit 500';

if ($res1 = mysql_query($str1)) {
	if ($proses == 'preview') {
		$stream .= '<table class=sortable cellspacing=1 border=0 width=2500px>';
		$add = '';
	}
	else {
		$stream .= '<table border=1>';
		$add = ' bgcolor=#dedede';
	}

	$stream .= '<thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '               <td rowspan=2 ' . $add . '>*</td>  ' . "\r\n" . '               <td rowspan=2 ' . $add . '>No</td>' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['id'] . '</td>' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['unit'] . '</td>                     ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['pemilik'] . '</td>' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['lokasi'] . '/(No.Persil)</td>                       ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['desa'] . '</td>               ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['luas'] . '</td>    ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['bisaditanam'] . '</td> ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['blok'] . '</td>    ' . "\r\n" . '                <td colspan=4 align=center ' . $add . '>' . $_SESSION['lang']['batas'] . '</td> ' . "\r\n" . '                <td colspan=7 align=center ' . $add . '>' . $_SESSION['lang']['biaya'] . '-' . $_SESSION['lang']['biaya'] . '</td>  ' . "\r\n" . '                <td colspan=4 align=center ' . $add . '>' . $_SESSION['lang']['status'] . '</td>    ' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['nomor'] . ' ' . $_SESSION['lang']['dokumen'] . '</td>' . "\r\n" . '                <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['keterangan'] . '</td> ' . "\r\n" . '                 <td rowspan=2 ' . $add . '>' . $_SESSION['lang']['updateby'] . '</td>   ' . "\r\n" . '                 </tr><tr class=rowheader>   ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['batastimur'] . '</td>                      ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['batasbarat'] . '</td>  ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['batasutara'] . '</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['batasselatan'] . '</td> ' . "\r\n" . '                    ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['tanamtumbuh'] . ' (Rp)</td> ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['gantilahan'] . ' (Rp)</td> ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['total'] . '<br>' . $_SESSION['lang']['gantilahan'] . ' (Rp)</td>    ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['biaya'] . '<br>' . $_SESSION['lang']['camat'] . ' (Rp)</td> ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['biaya'] . '<br>' . $_SESSION['lang']['kades'] . ' (Rp)</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['biaya'] . '<br>Matrai (Rp)</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['total'] . '<br>' . $_SESSION['lang']['biaya'] . ' (Rp)</td>     ' . "\r\n" . '                    ' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['permintaandana'] . '</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['pembayaran'] . '</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td ' . $add . '>' . $_SESSION['lang']['status'] . '<br>' . $_SESSION['lang']['camat'] . '</td>' . "\r\n" . '                </tr></thead>' . "\r\n" . '                <tbody>';
	$no = 0;

	while ($bar1 = mysql_fetch_object($res1)) {
		++$no;
		$stdana = ($bar1->statuspermintaandana == 1 ? tanggalnormal($bar1->tanggalpengajuan) : '');

		if ($bar1->statuspermbayaran == 1) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Belum Lunas';
		}
		else if ($bar1->statuspermbayaran == 0) {
			$stbayar = 'Belum Bayar';
		}
		else if ($bar1->statuspermbayaran == 2) {
			$stbayar = tanggalnormal($bar1->tanggalbayar) . ' Lunas';
		}

		$stkades = ($bar1->statuskades == 1 ? tanggalnormal($bar1->tanggalkades) : '');
		$stcamat = ($bar1->statuscamat == 1 ? tanggalnormal($bar1->tanggalcamat) : '');
		$stream .= '<tr class=rowcontent>                 ' . "\r\n" . '                          <td>';

		if ($proses == 'preview') {
			$stream .= '<img src=\'images/skyblue/pdf.jpg\' class=\'resicon\' onclick="ptintPDF(\'' . $bar1->idlahan . '\',\'' . $bar1->pemilik . '\',event);" title=\'Print Data Detail\'>';
		}

		$stream .= '</td>' . "\r\n" . '                           <td>' . $no . '</td>' . "\r\n" . '                           <td>' . $bar1->idlahan . '</td>' . "\r\n" . '                           <td>' . $bar1->unit . '</td>' . "\r\n" . '                           <td>' . $bar1->nama . '</td>' . "\r\n" . '                           <td>' . $bar1->lokasi . '</td>                                 ' . "\r\n" . '                           <td>' . $bar1->desa . '</td>' . "\r\n" . '                           <td align=right>' . $bar1->luas . '</td>  ' . "\r\n" . '                           <td align=right>' . $bar1->luasdapatditanam . '</td>' . "\r\n" . '                           <td>' . $bar1->kodeblok . '</td>    ' . "\r\n" . '                           <td>' . $bar1->batastimur . '</td>' . "\r\n" . '                           <td>' . $bar1->batasbarat . '</td>' . "\r\n" . '                           <td>' . $bar1->batasutara . '</td>' . "\r\n" . '                           <td>' . $bar1->batasselatan . '</td>  ' . "\r\n" . '                           <td align=right>' . number_format($bar1->rptanaman, 0) . '</td>    ' . "\r\n" . '                           <td align=right>' . number_format($bar1->rptanah, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->totalgantirugi, 0) . '</td>    ' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayakades, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayacamat, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->biayamatrai, 0) . '</td>' . "\r\n" . '                           <td align=right>' . number_format($bar1->totalgantirugi + $bar1->biayakades + $bar1->biayacamat + $bar1->biayamatrai, 0) . '</td>' . "\r\n" . '                            <td>' . $stdana . '</td>' . "\r\n" . '                           <td>' . $stbayar . '</td>' . "\r\n" . '                           <td>' . $stkades . '</td>' . "\r\n" . '                           <td>' . $stcamat . '</td>        ' . "\r\n" . '                           <td>' . $bar1->nosurat . '</td>  ' . "\r\n" . '                           <td>' . $bar1->keterangan . '</td>   ' . "\r\n" . '                           <td>' . $bar1->namakaryawan . '</td>                                ' . "\r\n" . '                            </td></tr>';
		$tluas += $bar1->luas;
		$ditanam += $bar1->luasdapatditanam;
		$ttanaman += $bar1->rptanaman;
		$ttanah += $bar1->rptanah;
		$tgrugi += $bar1->totalgantirugi;
		$tkades += $bar1->biayakades;
		$tcamat += $bar1->biayacamat;
		$tmaterai += $bar1->biayamatrai;
		$ttl += $bar1->totalgantirugi + $bar1->biayakades + $bar1->biayacamat + $bar1->biayamatrai;
	}

	$stream .= '<tr class=rowcontent>                 ' . "\r\n" . '              <td colspan=7>TOTAL</td>' . "\r\n" . '               <td align=right>' . $tluas . '</td>  ' . "\r\n" . '               <td align=right>' . $ditanam . '</td>' . "\r\n" . '                <td>Sudah Dibuka:' . $dibuka . ' Ha</td>   ' . "\r\n" . '               <td colspan=4></td>  ' . "\r\n" . '               <td align=right>' . number_format($ttanaman, 0) . '</td>    ' . "\r\n" . '               <td align=right>' . number_format($ttanah, 0) . '</td>' . "\r\n" . '               <td align=right>' . number_format($tgrugi, 0) . '</td>    ' . "\r\n" . '               <td align=right>' . number_format($tkades, 0) . '</td>' . "\r\n" . '               <td align=right>' . number_format($tcamat, 0) . '</td>' . "\r\n" . '               <td align=right>' . number_format($tmaterai, 0) . '</td>' . "\r\n" . '               <td align=right>' . number_format($ttl, 0) . '</td>' . "\r\n" . '                <td colspan=7></td>                                ' . "\r\n" . '                </td></tr>';
	$stream .= "\t" . ' ' . "\r\n" . '                 </tbody>' . "\r\n" . '                 <tfoot>' . "\r\n" . '                 </tfoot>' . "\r\n" . '                 </table>' . "\r\n" . '                 Note: Luas dibuka merupakan kegiatan Tumbang atau Stacking';
}

if ($proses == 'preview') {
	echo $stream;
}
else {
	$nop_ = 'Data_Pembebasan_Lahan' . $unit;

	if (0 < strlen($stream)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                </script>';
	}
}

?>
