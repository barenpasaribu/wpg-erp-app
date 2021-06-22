<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kdBarang = (isset($_POST['kdBarang']) ? $_POST['kdBarang'] : '');
$satuan = (isset($_POST['satuan']) ? $_POST['satuan'] : '');
$idPasar = (isset($_POST['idPasar']) ? $_POST['idPasar'] : '');
$idMatauang = (isset($_POST['idMatauang']) ? $_POST['idMatauang'] : '');
$hrgPasar = (isset($_POST['hrgPasar']) ? $_POST['hrgPasar'] : '');
$tglHarga = (isset($_POST['tglHarga']) ? tanggalsystem($_POST['tglHarga']) : '');
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$where = 'tanggal=\'' . $tglHarga . '\' and kodeproduk=\'' . $kdBarang . '\' and pasar=\'' . $idPasar . '\'';

switch ($proses) {
case 'getSatuan':
	$sSatuan = 'select distinct satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $kdBarang . '\'';

	#exit(mysql_error($conn));
	($qSatuan = mysql_query($sSatuan)) || true;
	$rSatuan = mysql_fetch_assoc($qSatuan);
	echo $rSatuan['satuan'];
	break;

case 'insert':
	$sCek = 'select distinct * from ' . $dbname . '.pmn_hargapasar where ' . $where . '';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sIns = 'insert into ' . $dbname . '.pmn_hargapasar (tanggal, kodeproduk, pasar, satuan, harga, matauang, status, catatan) ' . "\r\n\t\t" . '   values (\'' . $tglHarga . '\',\'' . $kdBarang . '\',\'' . $idPasar . '\',\'' . $satuan . '\',\'' . $hrgPasar . '\',\'' . $idMatauang . '\',\'' . $_POST['status'] . '\',\'' . $_POST['catatan'] . '\')';

		if (!mysql_query($sIns)) {
			echo 'Gagal,' . addslashes(mysql_error($conn));
		}
	}
	else {
		exit('Error: Already exist');
	}

	break;

case 'update':
	$sIns = 'update ' . $dbname . '.pmn_hargapasar set harga=\'' . $hrgPasar . '\',matauang=\'' . $idMatauang . '\',' . "\r\n\t\t\t\t" . 'status = \'' . $_POST['status'] . '\', catatan = \'' . $_POST['catatan'] . '\'' . "\r\n\t\t\t" . '   where ' . $where . '';

	if (!mysql_query($sIns)) {
		echo 'Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_hargapasar order by `tanggal` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.pmn_hargapasar order by `tanggal` desc  limit ' . $offset . ',' . $limit . '';
	$no = 0;

	if ($res = mysql_query($str)) {
		$barisData = mysql_num_rows($res);

		if (0 < $barisData) {
			while ($bar = mysql_fetch_object($res)) {
				$no += 1;
				$button_approve ="";
				if( $bar->kodeproduk == "40000003" ){
					if( $bar->persetujuan1 == "" ){
						$button_approve = '<a href="javascript:ApproveTBS(\'1\',\''.$bar->tanggal.'\');">[Approve 1]</a>';
					}else{
						if( $bar->persetujuan2 == "" ){
							$button_approve = '<a href="javascript:ApproveTBS(\'2\',\''.$bar->tanggal.'\');">[Approve 2]</a>';
						}
					}
				}
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\r\n\t\t\t\t" . '<td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $optNmBarang[$bar->kodeproduk] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->pasar . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->matauang . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->harga, 2) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->status . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->catatan . '</td>' .'<td>'.$button_approve.'</td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeproduk . '\',\'' . $bar->satuan . '\',\'' . $bar->pasar . '\',\'' . $bar->matauang . '\',\'' . $bar->harga . '\',\'' . $bar->status . '\',\'' . $bar->catatan . '\');">' . "\r\n\t\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeproduk . '\',\'' . $bar->pasar . '\');"></td>' . "\r\n\t\t\t\t" . '</tr>';
			}

			echo "\r\n\t\t\t\t" . '<tr><td colspan=11 align=center>' . "\r\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>';
		}
		else {
			echo '<tr class=rowcontent><td colspan=9>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo '</tbody></table>';
	break;

case 'cariData':
	$wre = '';

	if ($kdBarang != '') {
		$wre .= ' and kodeproduk=\'' . $kdBarang . '\'';
	}

	if ($tglHarga != '') {
		$wre .= ' and tanggal=\'' . $tglHarga . '\'';
	}

	if ($idPasar != '') {
		$wre .= ' and pasar=\'' . $idPasar . '\'';
	}

	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_hargapasar where tanggal!=\'\' ' . $wre . ' order by `tanggal` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.pmn_hargapasar where tanggal!=\'\' ' . $wre . ' order by `tanggal` desc  limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		$barisData = mysql_num_rows($res);

		if (0 < $barisData) {
			$no = 0;

			while ($bar = mysql_fetch_object($res)) {
				$no += 1;
				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\r\n\t\t\t\t" . '<td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $optNmBarang[$bar->kodeproduk] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->pasar . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->matauang . '</td>' . "\r\n\t\t\t\t" . '<td align=right>' . number_format($bar->harga, 2) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->status . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->catatan . '</td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeproduk . '\',\'' . $bar->satuan . '\',\'' . $bar->pasar . '\',\'' . $bar->matauang . '\',\'' . $bar->harga . '\',\'' . $bar->status . '\',\'' . $bar->catatan . '\');">' . "\r\n\t\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeproduk . '\',\'' . $bar->pasar . '\');"></td>' . "\r\n\t\t\t\t" . '</tr>';
			}

			echo "\r\n\t\t\t\t" . '<tr><td colspan=11 align=center>' . "\r\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariTrans(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariTrans(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr>';
		}
		else {
			echo '<tr class=rowcontent><td colspan=8>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
		}
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	echo '</tbody></table>';
	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.pmn_hargapasar where ' . $where . ' ';

	if (!mysql_query($sDel)) {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;
	
case 'approveHargaTbs':

	$approve_number = (isset($_POST['approve_no']) ? (int)$_POST['approve_no'] : '0');
	$tanggal = (isset($_POST['tanggal']) ? $_POST['tanggal'] : '');
	if($approve_number == '0' || $approve_number > '1' || $tanggal != ""){
		$str = 'select * from ' . $dbname . '.pmn_hargapasar where tanggal =\''.$tanggal.'\' and kodeproduk=\'40000003\' ';
		if ($res = mysql_query($str)) {
			$barisData = mysql_num_rows($res);
			if ($barisData == 1) {
				while ($bar = mysql_fetch_object($res)) {
					if($approve_number == 1  ){
						if( $bar->persetujuan2 == $_SESSION['standard']['userid'] ){
							die("User yang sama tidak boleh approve dua kali");
						}						
						$sDel = 'update ' . $dbname . '.pmn_hargapasar set persetujuan1=\''.$_SESSION['standard']['userid'].'\',wkt1=current_timestamp where tanggal=\''.$tanggal.'\' and kodeproduk=\'40000003\' ';
					}
					if($approve_number == 2  ){
						if( $bar->persetujuan1 == $_SESSION['standard']['userid'] ){
							die("User yang sama tidak boleh approve dua kali");
						}
						$sDel = 'update ' . $dbname . '.pmn_hargapasar set persetujuan2=\''.$_SESSION['standard']['userid'].'\',wkt2=current_timestamp where tanggal=\''.$tanggal.'\' and kodeproduk=\'40000003\' ';
					}
					if (!mysql_query($sDel)) {
						die(' Gagal,' . mysql_error($conn));
					}
					echo "success";
				}
				
			}
		}
				
	}else{
		die("proses update tidak berhasil");
	}
	//
	
	
	break;
}

?>
