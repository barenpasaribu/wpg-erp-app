<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$id_supplier = $_POST['id_supplier'];
$jlhKoli = $_POST['jlhKoli'];
$kpd = $_POST['kpd'];
$srtJalan = $_POST['srtJalan'];
$biaya = $_POST['biaya'];
$lokPenerimaan = $_POST['lokPenerimaan'];
$tglKrm = tanggalsystem($_POST['tglKrm']);
$ket = $_POST['ket'];
$karyId = $_POST['karyId'];
$idLokasi = $_POST['idLokasi'];
$optSupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nopo = $_POST['nopo'];
$idNomor = $_POST['idNomor'];
$txtSrc = $_POST['txtSrc'];
$tglSrc = $_POST['tglSrc'];
$arrd = array(1 => $_SESSION['lang']['expeditor'], 2 => $_SESSION['lang']['internal']);

switch ($method) {
case 'insert':
	if (($id_supplier == '') || ($tglKrm == '') || ($kpd == '') || ($jlhKoli == '') || ($biaya == '')) {
		echo 'warning:Field tidak boleh kosong';
		exit();
	}

	$sIns = 'insert into ' . $dbname . '.log_pengiriman_ht ( jumlahkoli, expeditor, tanggalkirim, pengirim, lokasipengirim, nosj, kepada, keterangan, biaya,lokasipenerima) ' . "\r\n" . '                       values (\'' . $jlhKoli . '\',\'' . $id_supplier . '\',\'' . $tglKrm . '\',\'' . $_SESSION['standard']['userid'] . '\',\'' . $_SESSION['empl']['lokasitugas'] . '\'' . "\r\n" . '                               ,\'' . $srtJalan . '\',\'' . $kpd . '\',\'' . $ket . '\',\'' . $biaya . '\',\'' . $lokPenerimaan . '\')';

	if (!mysql_query($sIns)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'loadData':
	$no = 0;
	$limit = 20;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;

	if ($txtSrc != '') {
		$where .= ' and nosj like \'%' . $txtSrc . '%\'';
	}

	if ($tglSrc != '') {
		$where .= ' and tanggal=\'' . $tglSrc . '\'';
	}

	$sql2 = 'select * from ' . $dbname . '.log_pengiriman_ht ' . "\r\n" . '                      where lokasipenerima =\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $where . '  order by nosj asc';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;
	$jlhbrs = mysql_num_rows($query2);

	if ($jlhbrs != 0) {
		$str = 'select * from ' . $dbname . '.log_pengiriman_ht ' . "\r\n" . '                      where lokasipenerima =\'' . $_SESSION['empl']['lokasitugas'] . '\'  ' . $where . '  order by nosj desc limit ' . $offset . ',' . $limit . ' ';
		$res = mysql_query($str);

		while ($bar = mysql_fetch_assoc($res)) {
			if (substr($bar['expeditor'], 0, 1) == 'S') {
				$drtet = $optSupplier[$bar['expeditor']];
				$pildt = 1;
			}
			else {
				$drtet = $optKary[$bar['expeditor']];
				$pildt = 2;
			}

			$no += 1;
			echo '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $no . '</td>' . "\r\n" . '                <td>' . $bar['nosj'] . '</td>' . "\r\n" . '                <td>' . $arrd[$pildt] . '</td>' . "\r\n\t\t" . '<td>' . $drtet . '</td>' . "\r\n\t\t" . '<td>' . tanggalnormal($bar['tanggalkirim']) . '</td>' . "\r\n" . '                <td>' . $bar['keterangan'] . '</td>';
			if (($bar['tanggalterima'] == '0000-00-00') || is_null($bar['tanggalterima'])) {
				echo '<td><input type=text class=myinputtext id=tglTrima onmousemove=setCalendar(this.id) onkeypress=\'return false\';  size=10 maxlength=10 style="width:100px;" /></td>';
				echo '<td><button class=mybutton onclick=saveFranco(\'' . $bar['nosj'] . '\')>' . $_SESSION['lang']['save'] . '</button></td>';
			}
			else {
				echo '<td>' . tanggalnormal($bar['tanggalterima']) . '</td><td>&nbsp;</td>';
			}

			echo "\r\n\t\t" . '</tr>';
		}

		echo "\r\n\t\t" . '<tr class=rowheader><td colspan=10 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	}
	else {
		echo '<tr class=rowcontent><td colspan=10>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
	}

	break;

case 'update':
	if ($tglKrm == '') {
		echo 'warning:Field tidak boleh kosong';
		exit();
	}

	$sCek = 'select distinct tanggalkirim from ' . $dbname . '.log_pengiriman_ht where nosj=\'' . $idNomor . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_assoc($qCek);
	$tglKrmdb = explode('-', $rCek['tanggalkirim']);
	$tglTrima = explode('-', $tglKrm);
	$sIns = 'update ' . $dbname . '.log_pengiriman_ht set tanggalterima=\'' . $tglKrm . '\\', ' . "\r\n" . '                           penerima=\'' . $_SESSION['standard']['userid'] . '\' where nosj=\'' . $idNomor . '\'';

	if (!mysql_query($sIns)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.log_pengiriman_ht where nosj=\'' . $idNomor . '\'';

	if (!mysql_query($sDel)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'getData':
	$sDt = 'select * from ' . $dbname . '.log_pengiriman_ht where nosj=\'' . $idNomor . '\'';

	#exit(mysql_error($conn));
	($qDt = mysql_query($sDt)) || true;
	$rDet = mysql_fetch_assoc($qDt);
	echo $rDet['nosj'] . '###' . $rDet['expeditor'] . '###' . tanggalnormal($rDet['tanggalkirim']) . '###' . $rDet['jumlahkoli'] . '###' . $rDet['kepada'] . '###' . $rDet['lokasipenerima'] . '###' . $rDet['nosj'] . '###' . $rDet['biaya'] . '###' . $rDet['keterangan'];
	break;

case 'getLokasi':
	if ($idLokasi == '') {
		$sLokTgs = 'select distinct lokasitugas from ' . $dbname . '.datakaryawan where karyawanid=\'' . $kpd . '\'';

		#exit(mysql_error());
		($qLokTgs = mysql_query($sLokTgs)) || true;
		$rLokTgs = mysql_fetch_assoc($qLokTgs);
		echo $rLokTgs['lokasitugas'];
	}
	else {
		echo $idLokasi;
	}

	unset($karyId);
	unset($idLokasi);
	break;

case 'cariData':
	$tab .= '<table cellspacing=1 border=0 class=data>' . "\r\n" . '                            <thead>' . "\r\n" . '                            <tr class=rowheader><td>No</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n" . '                            </tr>' . "\r\n" . '                            </thead>' . "\r\n" . '                            </tbody>';

	if ($nopo != '') {
		$str = 'select * from ' . $dbname . '.log_poht where nopo like \'%' . $nopo . '%\'' . "\r\n" . '                                    order by tanggal desc,nopo desc';
		$res = mysql_query($str);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$purchaser = '';

			if ($bar->karyawanid != '') {
				$str = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->karyawanid;
				$resv = mysql_query($str);

				while ($barv = mysql_fetch_object($resv)) {
					$purchaser = $barv->namauser;
				}
			}

			$no += 1;
			$tab .= "\r\n" . '                                    <tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' onclick=goPickPo(\'' . $bar->nopo . '\')><td>' . $no . '</td>' . "\r\n" . '                                    <td>' . $bar->nopo . '</td>' . "\r\n" . '                                    <td>' . $bar->kodeorg . '</td>' . "\r\n" . '                                    <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                                    <td>' . $purchaser . '</td>' . "\r\n" . '                                    </tr>' . "\r\n" . '                                    ';
		}
	}

	$tab .= '</tbody>' . "\r\n" . '                            <tfoot>' . "\r\n" . '                            </tfoot>' . "\r\n" . '                            </table>';
	$tab2 .= '<table class=sortable cellpadding=1 cellspacing=1 border=0>';
	$tab2 .= '<thead><tr class=rowheader><td>' . $_SESSION['lang']['nopo'] . '</td><td>Action</td></tr></thead><tbody>';
	$sData = 'select distinct * from ' . $dbname . '.log_pengiriman_dt where nosj=\'' . $idNomor . '\' order by nopo desc';

	#exit(mysql_error());
	($qData = mysql_query($sData)) || true;

	while ($rData = mysql_fetch_assoc($qData)) {
		$tab2 .= '<tr class=rowcontent><td>' . $rData['nopo'] . '</td>';
		$tab2 .= '<td align=center><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delDataDetail(\'' . $rData['nosj'] . '\',\'' . $rData['nopo'] . '\');"></td>';
	}

	$tab2 .= '</tbody></table>';
	echo $tab . '###' . $tab2;
	break;

case 'insertDetail':
	$sCek = 'select distinct * from ' . $dbname . '.log_pengiriman_dt where nosj=\'' . $idNomor . '\' and nopo=\'' . $nopo . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rRow = mysql_num_rows($qCek);

	if (0 < $rRow) {
		exit('Error:Nopo Sudah Ada' . $sCek);
	}
	else {
		$sIns = 'insert into ' . $dbname . '.log_pengiriman_dt (nosj,nopo) value (\'' . $idNomor . '\',\'' . $nopo . '\')';

		if (!mysql_query($sIns)) {
			echo 'Gagal' . mysql_error($conn);
		}
	}

	break;

case 'deleteDetail':
	$sDel = 'delete from ' . $dbname . '.log_pengiriman_dt where nosj=\'' . $idNomor . '\' and nopo=\'' . $nopo . '\'';

	if (!mysql_query($sDel)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;
}

?>
