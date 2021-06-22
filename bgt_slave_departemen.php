<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$cekapa = $_POST['cekapa'];

if ($cekapa == '') {
	$cekapa = $_GET['cekapa'];
}

if ($cekapa == 'saveatas') {
	$tahunbudget = $_POST['tahunbudget'];
	$departemen = $_POST['departemen'];
	$noakun = $_POST['noakun'];
	$keterangan = $_POST['keterangan'];
	$alokasi = $_POST['alokasi'];
	$jumlahbiaya = $_POST['jumlahbiaya'];
	$fisik = $_POST['fisik'];

	if ($fisik == '') {
		$fisik = 0;
	}

	$satuanf = $_POST['satuanf'];
	$str2 = 'select distinct tutup from ' . $dbname . '.bgt_dept where tahunbudget = \'' . $tahunbudget . '\' and departemen =\'' . $departemen . '\' ';
	$res2 = mysql_query($str2);
	$bar2 = mysql_fetch_assoc($res2);

	if ($bar2['tutup'] != 0) {
		exit('Error:  Budget ' . $thnBudget . ' is closed,can not modify');
	}

	$str = 'INSERT INTO ' . $dbname . '.`bgt_dept` (' . "\r\n" . '    `tahunbudget` ,' . "\r\n" . '    `departemen` ,' . "\r\n" . '    `noakun` ,' . "\r\n" . '    `keterangan` ,' . "\r\n" . '    `alokasibiaya` ,' . "\r\n" . '    `jumlah` ,' . "\r\n" . '    `updateby` ,' . "\r\n" . '    `fisik`,' . "\r\n" . '    `satuanf`' . "\r\n" . '    )' . "\r\n" . '    VALUES (' . "\r\n" . '    \'' . $tahunbudget . '\', \'' . $departemen . '\', \'' . $noakun . '\', \'' . $keterangan . '\', \'' . $alokasi . '\', \'' . $jumlahbiaya . '\', \'' . $_SESSION['standard']['userid'] . '\',' . "\r\n" . '     ' . $fisik . ',\'' . $satuanf . '\'        ' . "\r\n" . '    )';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . $str . addslashes(mysql_error($conn));
	}
}

if ($cekapa == 'tab') {
	$pilihtahun0 = $_POST['pilihtahun0'];
	$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_dept' . "\r\n" . '        where departemen = \'' . $_SESSION['empl']['bagian'] . '\'' . "\r\n" . '            order by tahunbudget desc' . "\r\n" . '        ';
	$opttahunbudget = '';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$opttahunbudget .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
	}

	$hkef = '';
	$hkef .= 'Budget Department : ' . $_SESSION['empl']['bagian'] . ' --- ';
	$hkef .= $_SESSION['lang']['budgetyear'] . ' : <select name=pilihtahun0 id=pilihtahun0 onchange="updateTab();"><option value=\'\'>' . $_SESSION['lang']['all'] . '</option>' . $opttahunbudget . '</select>';
	$hkef .= '<input type=hidden id=hidden0 name=hidden0 value="">';
	$hkef .= '<table id=container6 class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center>No</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['budgetyear'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['fisik'] . '</td>            ' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['satuan'] . '</td>                  ' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['alokasibiaya'] . '</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['totalbiaya'] . '</td>' . "\r\n" . '            <td align=center>Jan</td>' . "\r\n" . '            <td align=center>Feb</td>' . "\r\n" . '            <td align=center>Mar</td>' . "\r\n" . '            <td align=center>Apr</td>' . "\r\n" . '            <td align=center>May</td>' . "\r\n" . '            <td align=center>Jun</td>' . "\r\n" . '            <td align=center>Jul</td>' . "\r\n" . '            <td align=center>Aug</td>' . "\r\n" . '            <td align=center>Sep</td>' . "\r\n" . '            <td align=center>Oct</td>' . "\r\n" . '            <td align=center>Nov</td>' . "\r\n" . '            <td align=center>Dec</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['action'] . '</td>' . "\r\n" . '       </tr>  ' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>';

	if ($_SESSION['language'] == 'EN') {
		$dd = 'namaakun1 as namaakun';
	}
	else {
		$dd = 'namaakun as namaakun';
	}

	$str = 'select noakun,' . $dd . ' from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1 order by noakun' . "\r\n" . '                    ';
	$optnoakun = '';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$noakun[$bar->noakun] = $bar->namaakun;
	}

	$str = 'select * from ' . $dbname . '.bgt_dept' . "\r\n" . '        where departemen = \'' . $_SESSION['empl']['bagian'] . '\' and tahunbudget like \'%' . $pilihtahun0 . '%\'' . "\r\n" . '            order by tahunbudget, noakun, alokasibiaya';
	$res = mysql_query($str);
	$no = 1;

	while ($bar = mysql_fetch_object($res)) {
		$hkef .= '<tr class=rowcontent>' . "\r\n" . '            <td align=center>' . $no . '</td>' . "\r\n" . '            <td align=center>' . $bar->tahunbudget . '</td>' . "\r\n" . '            <td align=left>' . $bar->noakun . ' - ' . $noakun[$bar->noakun] . '</td>' . "\r\n" . '            <td align=right>' . $bar->fisik . '</td>' . "\r\n" . '            <td align=left>' . $bar->satuanf . '</td>    ' . "\r\n" . '            <td align=center>' . $bar->keterangan . '</td>' . "\r\n" . '            <td align=center>' . $bar->alokasibiaya . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->jumlah) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d01) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d02) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d03) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d04) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d05) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d06) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d07) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d08) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d09) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d10) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d11) . '</td>' . "\r\n" . '            <td align=right>' . number_format($bar->d12) . '</td>';

		if ($bar->tutup == 0) {
			$hkef .= '<td align=center>' . "\r\n" . '                <input type="image" id=delete src=images/application/application_delete.png class=dellicon title=' . $_SESSION['lang']['delete'] . ' onclick="deleteRow(' . $bar->kunci . ')";>' . "\r\n" . '                <input type="image" id=search src=images/search.png class=dellicon title=' . $_SESSION['lang']['sebaran'] . ' onclick="sebaran(' . $bar->kunci . ',event)";>' . "\r\n" . '            </td>';
		}
		else {
			$hkef .= '<td>' . $_SESSION['lang']['tutup'] . '</td>';
		}

		$hkef .= '</tr>';
		$no += 1;
	}

	echo $hkef;
	echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\t\t" . ' ' . "\r\n" . '     </table>';
}

if ($cekapa == 'delete') {
	$kunci = $_POST['kunci'];
	$str = 'delete from ' . $dbname . '.bgt_dept ' . "\r\n" . '    where kunci=\'' . $kunci . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal3,' . addslashes(mysql_error($conn));
	}
}

if ($cekapa == 'sebaran') {
	$kunci = $_GET['kunci'];
	$str = 'select noakun,namaakun from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1  order by noakun' . "\r\n" . '                    ';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$noakun[$bar->noakun] = $bar->namaakun;
	}

	require_once 'master_validation.php';
	require_once 'config/connection.php';
	require_once 'lib/eagrolib.php';
	include_once 'lib/zLib.php';
	echo '<script language=javascript1.2 src="js/generic.js"></script>' . "\r\n" . '<script language=javascript1.2 src="js/bgt_departemen.js"></script>' . "\r\n" . '<link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n";
	$hkef = '';
	$str = 'select * from ' . $dbname . '.bgt_dept' . "\r\n" . '        where kunci = \'' . $kunci . '\'';
	$res = mysql_query($str);
	$no = 1;
	$res = mysql_query($str);
	$no = 1;
	$df = 0;

	while ($bar = mysql_fetch_object($res)) {
		$rp01 = $bar->d01;
		$rp02 = $bar->d02;
		$rp03 = $bar->d03;
		$rp04 = $bar->d04;
		$rp05 = $bar->d05;
		$rp06 = $bar->d06;
		$rp07 = $bar->d07;
		$rp08 = $bar->d08;
		$rp09 = $bar->d09;
		$rp10 = $bar->d10;
		$rp11 = $bar->d11;
		$rp12 = $bar->d12;
		$df = $rp01 + $rp02 + $rp03 + $rp04 + $rp05 + $rp06 + $rp07 + $rp08 + $rp09 + $rp10 + $rp11 + $rp12;
		$total = $bar->jumlah;

		if ($df == 0) {
			$rp01 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp02 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp03 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp04 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp05 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp06 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp07 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp08 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp09 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp10 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp11 = number_format($bar->jumlah / 12, 2, '.', '');
			$rp12 = number_format($bar->jumlah / 12, 2, '.', '');
		}
	}

	$hkef .= '<table id=container5 class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['bulan'] . '</td><td>%</td><td align=right>' . number_format($total, 2, '.', ',') . '</td></tr>' . "\r\n" . '       </tr>  ' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>';
	$x = 1;

	while ($x < 13) {
		$z = str_pad($x, 2, '0', STR_PAD_LEFT);
		$hkef .= '<tr class=rowcontent><td>' . $z . '</td>' . "\r\n" . '                <td><input type=text class=myinputtextnumber onkeypress="return angka_doang(event);" id=persen' . $x . ' size=3 onblur=ubahNilai(' . $total . ') value=' . number_format(($'rp' . $z / $total) * 100, 2, '.', '') . '></td>' . "\r\n" . '                <td><input id=rupiah' . $x . ' type=text class=myinputtextnumber onkeypress="return angka_doang(event)" value=\'' . $'rp' . $z . '\' size=15></td></tr>';
		++$x;
	}

	$hkef .= '<tr class=rowcontent><td align=center colspan=3>' . "\r\n" . '                <input type=hidden id=total4 name=total4 value="' . $total . '">' . "\r\n" . '                <input type=hidden id=progress name=progress value="">    ' . "\r\n" . '                <input type="image" id=search src=images/save.png class=dellicon title=' . $_SESSION['lang']['save'] . ' onclick="simpansebaran(' . $kunci . ',' . $total . ',event)";>' . "\r\n" . '            </td></tr>' . "\r\n" . '           </tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\t\t" . ' ' . "\r\n" . '     </table><br><br>' . "\r\n" . '       <center><button class=mybutton id=tutup name=tutup onclick=parent.closeDialog()>' . $_SESSION['lang']['close'] . '</button>' . "\r\n" . '       ';
	echo $hkef;
	echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n" . '     </tfoot>' . "\t\t" . ' ' . "\r\n" . '     </table>';
}

if ($cekapa == 'simpansebaran') {
	$kunci = $_POST['kunci'];
	$d01 = $_POST['d01'];
	$d02 = $_POST['d02'];
	$d03 = $_POST['d03'];
	$d04 = $_POST['d04'];
	$d05 = $_POST['d05'];
	$d06 = $_POST['d06'];
	$d07 = $_POST['d07'];
	$d08 = $_POST['d08'];
	$d09 = $_POST['d09'];
	$d10 = $_POST['d10'];
	$d11 = $_POST['d11'];
	$d12 = $_POST['d12'];
	$str = 'select * from ' . $dbname . '.bgt_dept' . "\r\n" . '        where kunci = \'' . $kunci . '\'';
	$res = mysql_query($str);
	$hkef = '';

	while ($bar = mysql_fetch_object($res)) {
		$hkef .= $bar->kunci;
	}

	if ($hkef != '') {
		$hkef = 'Data sudah ada : ' . $hkef;
	}

	$str = 'select * from ' . $dbname . '.bgt_dept' . "\r\n" . '        where kunci = \'' . $kunci . '\'';
	$res = mysql_query($str);
	$cektotal = '';

	while ($bar = mysql_fetch_object($res)) {
		$totalah = $d01 + $d02 + $d03 + $d04 + $d05 + $d06 + $d07 + $d08 + $d09 + $d10 + $d11 + $d12;

		if ($bar->jumlah < $totalah) {
			$cektotal .= number_format($totalah) . ' > ' . number_format($bar->jumlah);
		}
	}

	if ($cektotal != '') {
		$cektotal = 'Total sebaran melebihi tahunan. ' . $cektotal;
		echo $cektotal;
		exit();
	}

	$str = 'UPDATE ' . $dbname . '.`bgt_dept` SET `d01` = \'' . $d01 . '\', `d02` = \'' . $d02 . '\', `d03` = \'' . $d03 . '\', `d04` = \'' . $d04 . '\', `d05` = \'' . $d05 . '\', `d06` = \'' . $d06 . '\', `d07` = \'' . $d07 . '\', `d08` = \'' . $d08 . '\', `d09` = \'' . $d09 . '\', `d10` = \'' . $d10 . '\', `d11` = \'' . $d11 . '\', `d12` = \'' . $d12 . '\' WHERE kunci = \'' . $kunci . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo ' Gagal,' . $str . addslashes(mysql_error($conn));
	}
}

if ($cekapa == 'updatetahun') {
	$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_dept' . "\r\n" . '        where departemen = \'' . $_SESSION['empl']['bagian'] . '\'' . "\r\n" . '            order by tahunbudget desc' . "\r\n" . '    ';
	$res = mysql_query($str);
	$hkef = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

	while ($bar = mysql_fetch_object($res)) {
		$hkef .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
	}

	echo $hkef;
}

if ($cekapa == 'closeBudget') {
	$tahunbudget = $_POST['tahunbudget'];

	if ($tahunbudget == '') {
		exit('Error: Tahun Budget Tidak Boleh Kosong');
	}

	$sQl = 'select distinct tutup from ' . $dbname . '.bgt_dept where departemen = \'' . $_SESSION['empl']['bagian'] . '\' and tahunbudget=\'' . $tahunbudget . '\' and tutup=1';

	#exit(mysql_error($conn));
	($qQl = mysql_query($sQl)) || true;
	$row = mysql_num_rows($qQl);

	if ($row != 1) {
		$sUpdate = 'update ' . $dbname . '.bgt_dept set tutup=1 where departemen = \'' . $_SESSION['empl']['bagian'] . '\' and tahunbudget=\'' . $tahunbudget . '\'';

		if (mysql_query($sUpdate)) {
			echo '';
		}
		else {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}
	else {
		exit('Error:Sudah di Tutup');
	}
}

if ($cekapa == 'getThnBudget') {
	$optThnTtp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sThn = 'select distinct tahunbudget from ' . $dbname . '.bgt_dept where departemen like \'%' . $_SESSION['empl']['bagian'] . '%\' and tutup=0 order by tahunbudget desc';

	#exit(mysql_error($conn));
	($qThn = mysql_query($sThn)) || true;

	while ($rThn = mysql_fetch_assoc($qThn)) {
		$optThnTtp .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
	}

	echo $optThnTtp;
}

?>
