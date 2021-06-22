<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';

extract($_POST);
extract($_GET);

if($unit==''){
	$unit=substr($_SESSION['empl']['lokasitugas'], 0,3);
}
/*if (isset($_POST['method'])) {
	$method = $_GET['method'];
	$noPo = $_POST['noPo'];
	$nopo = $_POST['nopo'];
	if($_POST['unit']==''){
		$unit=substr($_SESSION['empl']['lokasitugas'], 0,3);
	}else{
		$unit = $_POST['unit'];
	}
/*
}
else {
	$method = $_GET['method'];
	$noPo = $_GET['noPo'];
	$nopo = $_GET['nopo'];

	if($_POST['unit']==''){
		$unit=substr($_SESSION['empl']['lokasitugas'], 0,3);
	}else{
		$unit = $_GET['unit'];
	}
}
*/
//$method = $_POST['method'];
//$noPo = $_POST['noPo'];
//$nopo = $_POST['nopo'];
//$_POST['nopo'] == '' ? $nopo = $_GET['nopo'] : $nopo = $_POST['nopo'];
//$_POST['nopo'] == '' ? $nopo = $_GET['nopo'] : $nopo = $_POST['nopo'];
//$_POST['method'] == '' ? $method = $_GET['method'] : $method = $_POST['method'];
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

if ($method == 'excel') {
	$border = 'border=1';
	$bgCol = 'bgcolor=#999999 ';
}

$stream = '<table cellspacing=1 class=sortable>
			<thead>
			<tr class=rowheader>
				<td align=center>' . $_SESSION['lang']['nourut'] . '</td>
				<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>
				<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>
				<td align=center>' . $_SESSION['lang']['nopo'] . '</td>
				<td align=center>' . $_SESSION['lang']['tglpo1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['nobpb1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tanggal'] . ' BPB</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['nopacking1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tanggal'] . ' Packing</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['nosj1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tglsj1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tglkirim1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tglcutisampai'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['nokonosemen'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tglpengapalan1'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tanggalberangkat'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['tanggaltiba'] . '</td>
				<td align=center ' . $bgCol . '>' . $_SESSION['lang']['diterimaoleh'] . '</td>
				</tr></thead><tbody>';
$aPo = "select kodebarang,nopo,tanggal from $dbname.log_po_vw where 1";
if ($nopo!=''){
	$aPo.=" and  nopo = '".$nopo."' ";
}
if ($unit!=''){
	$aPo.="  and kodeorg like '".$unit."' ";
}
$aPo.=" ORDER BY SUBSTRING(nopo,5,2), LEFT(nopo,3) ";

($bPo = mysql_query($aPo)) ;

while ($cPo = mysql_fetch_assoc($bPo)) {
	

	$aBpb = 'select notransaksi,tanggal from ' . $dbname . '.log_transaksi_vw where nopo=\'' . $cPo['nopo'] . '\' and kodebarang=\'' . $cPo['kodebarang'] . '\' and tipetransaksi=\'1\' and post=1';

	#exit(mysql_error($conn));
	($bBpb = mysql_query($aBpb)) ;
	$cBpb = mysql_fetch_assoc($bBpb);
	$aPl = 'select notransaksi,tanggal from ' . $dbname . '.log_packing_vw where nopo=\'' . $cPo['nopo'] . '\' and kodebarang=\'' . $cPo['kodebarang'] . '\' and posting=1 ';

	#exit(mysql_error($conn));
	($bPl = mysql_query($aPl)) ;
	$cPl = mysql_fetch_assoc($bPl);
	$nPl = $cPl['notransaksi'];
	$tPl = $cPl['tanggal'];
	$aSj = 'select nosj,tanggal,tanggalkirim,tanggaltiba from ' . $dbname . '.log_suratjalan_vw where nopo=\'' . $cPo['nopo'] . '\' and kodebarang=\'' . $cPo['kodebarang'] . '\' and posting=1';

	#exit(mysql_error($conn));
	($bSj = mysql_query($aSj)) ;
	$cSj = mysql_fetch_assoc($bSj);
	$nSj = $cSj['nosj'];
	$tglSj = $cSj['tanggal'];
	$tglKSj = $cSj['tanggalkirim'];
	$tglTSj = $cSj['tanggalkirim'];
	$xSj = 'select nosj,tanggal,tanggalkirim,tanggaltiba from ' . $dbname . '.log_suratjalan_vw where  kodebarang=\'' . $nPl . '\' and posting=1';

	#exit(mysql_error($conn));
	($ySj = mysql_query($xSj)) ;
	$zSj = mysql_fetch_assoc($ySj);
	$nSj1 = $zSj['nosj'];
	$tglSj1 = $zSj['tanggal'];
	$tglKSj1 = $zSj['tanggalkirim'];
	$tglTSj1 = $zSj['tanggalkirim'];
	$aK = 'select nokonosemen,tanggal,tanggaltiba,tanggalberangkat,penerima from ' . $dbname . '.log_konosemen_vw where nopo=\'' . $cPo['nopo'] . '\' and kodebarang=\'' . $cPo['kodebarang'] . '\'';

	#exit(mysql_error($conn));
	($bK = mysql_query($aK)) ;
	$cK = mysql_fetch_assoc($bK);
	$nK = $cK['nokonosemen'];
	$tglK = $cK['tanggal'];
	$tglBK = $cK['tanggalberangkat'];
	$tglTK = $cK['tanggaltiba'];
	$dtK = $cK['penerima'];
	$xK = 'select nokonosemen,tanggal,tanggaltiba,tanggalberangkat,penerima from ' . $dbname . '.log_konosemen_vw where kodebarang=\'' . $nPl . '\'';

	#exit(mysql_error($conn));
	($yK = mysql_query($xK)) ;
	$zK = mysql_fetch_assoc($yK);
	$nK1 = $zK['nokonosemen'];
	$tglK1 = $zK['tanggal'];
	$tglBK1 = $zK['tanggalberangkat'];
	$tglTK1 = $zK['tanggaltiba'];
	$dtK1 = $zK['penerima'];
	if (($nPl == '') || ($nPl == 'NULL')) {
		$nSj = $nSj;
		$tglSj = $tglSj;
		$tglKSj = $tglKSj;
		$tglTSj = $tglTSj;
		$nK = $nK;
		$tglK = $tglK;
		$tglBK = $tglBK;
		$tglTK = $tglTK;
		$dtK = $dtK;
	}
	else {
		$nSj = $nSj1;
		$tglSj = $tglSj1;
		$tglKSj = $tglKSj1;
		$tglTSj = $tglTSj1;
		$nK = $nK1;
		$tglK = $tglK1;
		$tglBK = $tglBK1;
		$tglTK = $tglTK1;
		$dtK = $dtK1;
	}

	$no += 1;
	$stream .= "\r\n\t\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t\t" .
		'<td>' . $no . '</td>' .
		'<td>' . $cPo['kodebarang'] . '</td>' .
		'<td>' . $nmBarang[$cPo['kodebarang']] . '</td>' .
		'<td nowrap>' . $cPo['nopo'] . '</td>' .
		'<td>' . tanggalnormal($cPo['tanggal']) . '</td>' .
		'<td>' . $cBpb['notransaksi'] . '</td>' .
		'<td>' . tanggalnormal($cBpb['tanggal']) . '</td>' .
		'<td>' . $nPl . '</td>' .
		'<td>' . tanggalnormal($tPl) . '</td>' .
		'<td>' . $nSj . '</td>' .
		'<td>' . tanggalnormal($tglSj) . '</td>' .
		'<td>' . tanggalnormal($tglKSj) . '</td>' .
		'<td>' . tanggalnormal($tglTSj) . '</td>' .
		'<td>' . $nK . '</td>' .
		'<td>' . tanggalnormal($tglK) . '</td>' .
		'<td>' . tanggalnormal($tglBK) . '</td>' .
		'<td>' . tanggalnormal($tglTK) . '</td>' .
		'<td>' . $nmKar[$dtK] . '</td>' .
		'</tr>';
}

$stream .= '</tbody></table>';

switch ($method) {
	case 'getPO':
		$str = "select * from (".
			getQuery("po").
			") x where x.nopo like '%".$nmPO."%'";
		echo '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                        <table cellpading=1 border=0 class=sortbale>' . "\r\n" . '                        <thead>' . "\r\n" . '                        <tr class=rowheader>' . "\r\n" . '                        <td>No.</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['kodesupplier'] . '</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['namasupplier'] . '</td>' . "\r\n" . '                        </tr><tbody>' . "\r\n" . '                        ';
		($qSupplier = mysql_query($str)) || true;

		while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
			$no += 1;
			echo '<tr class=rowcontent onclick=setDataPO(\'' . $rSupplier['nopo'] . '\')>' . "\r\n" .
				'                         <td>' . $no . '</td>' . "\r\n" .
				'                         <td>' . $rSupplier['nopo'] . '</td>' . "\r\n" .
				'                         <td>' . $rSupplier['namasupplier'] . '</td>' . "\r\n" .
				'                    </tr>';
		}

		echo '</tbody></table></div>';
		break;
	case 'goCariPo':
		echo "\r\n\t\t\t" . '<table cellspacing=1 border=0 class=data>' . "\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t" . '</thead>' . "\r\n\t\t" . '</tbody>';
		$i = 'select distinct(nopo) as nopo from ' . $dbname . '.log_po_vw where statuspo=\'3\'  and nopo like \'%' . $noPo . '%\'  ';

		#exit(mysql_error($conn));
		($n = mysql_query($i)) ;

		while ($d = mysql_fetch_assoc($n)) {
			$no += 1;
			echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' onclick="goPickPO(\'' . $d['nopo'] . '\');">' . "\r\n\t\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t\t" . '<td>' . $d['nopo'] . '</td>' . "\r\n\t\t\t\t" . '</tr>';
		}

		break;

	case 'preview':
		echo $stream;
		break;

	case 'excel':
		$stream .= 'Print Time : ' . date('H:i:s, d/m/Y') . '<br>By : ' . $_SESSION['empl']['name'];
		$tglSkrg = date('Ymd');
		$nop_ = 'Laporan Posisi Barang' . $tglSkrg;

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
				echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n\t\t\t\t" . '</script>';
				exit();
			}
			else {
				echo '<script language=javascript1.2>' . "\r\n\t\t\t\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n\t\t\t\t" . '</script>';
			}

			closedir($handle);
		}

		break;
}

?>
