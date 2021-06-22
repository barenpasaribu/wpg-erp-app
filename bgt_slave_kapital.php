<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';

switch ($_POST['proses']) {
case 'simpanHeader':
	$str = 'insert into ' . $dbname . '.bgt_kapital (tahunbudget, kodeunit, jeniskapital, keterangan, jumlah, hargasatuan, hargatotal, tutup,updateby,lokasi)' . "\r\n" . '                 values(' . $_POST['tahunbudget'] . ',\'' . $_POST['kodeorg'] . '\',\'' . $_POST['jeniskapital'] . '\',\'' . $_POST['keterangan'] . '\',' . "\r\n" . '                 ' . $_POST['jumlah'] . ',' . $_POST['harga'] . ',' . $_POST['total'] . ',0,' . $_SESSION['standard']['userid'] . ',\'' . $_POST['lokasi'] . '\');';

	if (mysql_query($str)) {
		$str = 'select a.*,b.namatipe,' . "\r\n" . '                 (a.k01+a.k02+a.k03+a.k04+a.k05+a.k06+a.k07+a.k08+a.k09+a.k10+a.k11+a.k12) as sebaran' . "\r\n" . '                 from ' . $dbname . '.bgt_kapital a left join' . "\r\n" . '                   ' . $dbname . '.sdm_5tipeasset b on a.jeniskapital=b.kodetipe' . "\r\n" . '                   where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '                   order by tahunbudget desc limit 100';
		$res = mysql_query($str);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$bar->tutup == 0 ? $rtp = ' title="Sebaran" onclick="sebaran(' . $bar->kunci . ',event)" style=\'cursor:pointer\'' : $rtp = '';
			$no += 1;
			echo '<tr class=rowcontent>' . "\r\n" . '                    <td ' . $rtp . '>' . $no . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->tahunbudget . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->kodeunit . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->lokasi . '</td>                         ' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->namatipe . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->keterangan . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . number_format($bar->jumlah, 0) . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . number_format($bar->hargasatuan, 0) . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . number_format($bar->hargatotal, 0) . '</td>';

			if (round($bar->sebaran) < round($bar->hargatotal)) {
				echo '<td>Not.OK</td>';
			}
			else {
				echo '<td>OK</td>';
			}

			if ($bar->tutup == 1) {
				echo '<td></td>';
			}
			else {
				echo '<td align=center style=\'cursor:pointer;\'>' . "\r\n" . '                          <img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteData(\'' . $bar->kunci . '\')" src=\'images/application/application_delete.png\'/>' . "\r\n" . '                          <input id="search" src="images/search.png" class="dellicon" title="Sebaran" onclick="sebaran(' . $bar->kunci . ',event)" type="image"></td>';
			}

			echo '</tr> ';
		}
	}
	else {
		echo 'Error:' . addslashes(mysql_error($conn) . $str);
	}

	break;

case 'delete':
	$str = 'delete from ' . $dbname . '.bgt_kapital where kunci=' . $_POST['kunci'];

	if (mysql_query($str)) {
		$bar->tutup == 0 ? $rtp = ' title="Sebaran" onclick="sebaran(' . $bar->kunci . ',event)" style=\'cursor:pointer\'' : $rtp = '';
		$str = 'select a.*,b.namatipe,' . "\r\n" . '                 (a.k01+a.k02+a.k03+a.k04+a.k05+a.k06+a.k07+a.k08+a.k09+a.k10+a.k11+a.k12) as sebaran' . "\r\n" . '                 from ' . $dbname . '.bgt_kapital a left join' . "\r\n" . '                   ' . $dbname . '.sdm_5tipeasset b on a.jeniskapital=b.kodetipe' . "\r\n" . '                   where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '                   order by tahunbudget desc limit 100';
		$res = mysql_query($str);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			echo '<tr class=rowcontent>' . "\r\n" . '                    <td ' . $rtp . '>' . $no . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->tahunbudget . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->kodeunit . '</td>' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->lokasi . '</td>                          ' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->namatipe . '</td>   ' . "\r\n" . '                    <td ' . $rtp . '>' . $bar->keterangan . '</td>' . "\r\n" . '                    <td align=right ' . $rtp . '>' . number_format($bar->jumlah, 0) . '</td>' . "\r\n" . '                    <td align=right ' . $rtp . '>' . number_format($bar->hargasatuan, 0) . '</td>' . "\r\n" . '                    <td align=right ' . $rtp . '>' . number_format($bar->hargasatuan, 0) . '</td>';

			if (round($bar->sebaran) < round($bar->hargatotal)) {
				echo '<td>Not.OK</td>';
			}
			else {
				echo '<td>OK</td>';
			}

			if ($bar->tutup == 1) {
				echo '<td></td>';
			}
			else {
				echo '<td align=center style=\'cursor:pointer;\'>' . "\r\n" . '                          <img id=\'detail_add\' title=\'delete data\' class=zImgBtn onclick="deleteData(\'' . $bar->kunci . '\')" src=\'images/application/application_delete.png\'/>' . "\r\n" . '                           <input id="search" src="images/search.png" class="dellicon" title="Sebaran" onclick="sebaran(' . $bar->kunci . ',event)" type="image">     ' . "\r\n" . '                          </td>';
			}

			echo '</tr> ';
		}
	}
	else {
		echo 'Error:' . addslashes(mysql_error($conn));
	}

	break;

case 'sebaran':
	$str = 'select * from ' . $dbname . '.bgt_kapital where kunci=' . $_POST['kunci'];
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$kunci = $bar->kunci;
		$total = $bar->hargatotal;
		$k01 = $bar->k01;
		$k02 = $bar->k02;
		$k03 = $bar->k03;
		$k04 = $bar->k04;
		$k05 = $bar->k05;
		$k06 = $bar->k06;
		$k07 = $bar->k07;
		$k08 = $bar->k08;
		$k09 = $bar->k09;
		$k10 = $bar->k10;
		$k11 = $bar->k11;
		$k12 = $bar->k12;
		$krata = $total / 12;
	}

	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '                <thead>' . "\r\n" . '                <thead>' . "\r\n" . '                   <tr class=rowheader><td>' . $_SESSION['lang']['bulan'] . '</td><td>%</td><td>' . number_format($total, 2) . '</td></tr>' . "\r\n" . '                </thead>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody>' . "\r\n" . '                <tr class=rowcontent>';

	if (($k01 + $k02 + $k03 + $k04 + $k05 + $k06 + $k07 + $k08 + $k09 + $k10 + $k11 + $k12) < 1) {
		$x = 1;

		while ($x < 13) {
			$z = str_pad($x, 2, '0', STR_PAD_LEFT);
			echo '<tr class=rowcontent><td>' . $z . '</td>' . "\r\n" . '                          <td><input type=text class=myinputtextnumber onkeypress="return angka_doang(event);" id=persen' . $x . ' size=3 onblur=ubahNilai(' . $total . ') value=' . number_format(($krata / $total) * 100, 2, '.', '') . '></td>' . "\r\n" . '                          <td><input id=k' . $x . ' type=text class=myinputtextnumber onkeypress="return angka_doang(event)" value=\'' . $krata . '\' size=15></td></tr>';
			++$x;
		}
	}
	else {
		$x = 1;

		while ($x < 13) {
			$z = str_pad($x, 2, '0', STR_PAD_LEFT);
			echo '<tr class=rowcontent><td>' . $z . '</td>' . "\r\n" . '                        <td><input type=text class=myinputtextnumber onkeypress="return angka_doang(event);" id=persen' . $x . ' size=3 onblur=ubahNilai(' . $total . ') value=' . number_format(($'k' . $z / $total) * 100, 2, '.', '') . '></td>' . "\r\n" . '                        <td><input id=k' . $x . ' type=text class=myinputtextnumber onkeypress="return angka_doang(event)" value=\'' . $'k' . $z . '\' size=15></td></tr>';
			++$x;
		}
	}

	echo '<tr class=rowcontent><td colspan=3 align=center>' . "\r\n" . '<img id=\'detail_add\' title=\'Simpan\' class=zImgBtn onclick=simpanSebaran(\'' . $total . '\',\'' . $kunci . '\') src=\'images/save.png\'/ style=\'cursor:pointer;\'>&nbsp;&nbsp;<img id=\'detail_add\' title=\'Clear Form\' class=zImgBtn  width=\'16\' height=\'16\'  onclick="clearForm()" src=\'images/clear.png\'/ style=\'cursor:pointer;\'>';
	echo '</tr>' . "\r\n" . '                </tbody>' . "\r\n" . '                <tfoot>' . "\r\n" . '                </tfoot>' . "\r\n" . '               </table>';
	break;

case 'updatesebaran':
	$zz = $_POST['k01'] + $_POST['k02'] + $_POST['k03'] + $_POST['k04'] + $_POST['k05'] + $_POST['k06'] + $_POST['k07'] + $_POST['k08'] + $_POST['k09'] + $_POST['k10'] + $_POST['k11'] + $_POST['k12'];

	if ($_POST['total'] < floor($zz)) {
		exit('Error: Sebaran lebih besar dari total (' . $_POST['total'] . '<' . $zz . ')');
	}
	else {
		$str = 'update ' . $dbname . '.bgt_kapital set' . "\r\n" . '             k01=' . $_POST['k01'] . ',' . "\r\n" . '             k02=' . $_POST['k02'] . ',' . "\r\n" . '             k03=' . $_POST['k03'] . ',' . "\r\n" . '             k04=' . $_POST['k04'] . ',' . "\r\n" . '             k05=' . $_POST['k05'] . ',' . "\r\n" . '             k06=' . $_POST['k06'] . ',' . "\r\n" . '             k07=' . $_POST['k07'] . ',' . "\r\n" . '             k08=' . $_POST['k08'] . ',' . "\r\n" . '             k09=' . $_POST['k09'] . ',' . "\r\n" . '             k10=' . $_POST['k10'] . ',' . "\r\n" . '             k11=' . $_POST['k11'] . ',' . "\r\n" . '             k12=' . $_POST['k12'] . ',' . "\r\n" . '             updateby=' . $_SESSION['standard']['userid'] . '    ' . "\r\n" . '             where kunci=' . $_POST['kunci'];

		if (mysql_query($str)) {
		}
		else {
			echo 'Error:' . addslashes(mysql_error($conn));
		}
	}

	break;

case 'tutup':
	$str = 'update ' . $dbname . '.bgt_kapital set tutup=1,updateby=' . $_SESSION['standard']['userid'] . ' ' . "\r\n" . '          where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tahunbudget=\'' . $_POST['tahun'] . '\'';

	if (mysql_query($str)) {
	}
	else {
		echo 'Error:' . addslashes(mysql_error($conn));
	}

	break;
}

?>
