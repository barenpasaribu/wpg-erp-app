<?php


//session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';

$nmPO = "";
if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
	$nmPO = $_POST['nmPO'];
}
else {
	$proses = $_GET['proses'];
	$nmPO = $_GET['nmPO'];
}
$_POST['txtNopp'] != '' ? $txtNopp = $_POST['txtNopp'] : $txtNopp = $_GET['txtNopp'];
$_POST['supplier_id'] != '' ? $supplier_id = $_POST['supplier_id'] : $supplier_id = $_GET['supplier_id'];
$_POST['tgl_cari'] != '' ? $tgl_cari = tanggalsystem($_POST['tgl_cari']) : $tgl_cari = tanggalsystem($_GET['tgl_cari']);
$_POST['periode'] != '' ? $periode = $_POST['periode'] : $periode = $_GET['periode'];
$_POST['lokBeli'] != '' ? $lokBeli = $_POST['lokBeli'] : $lokBeli = $_GET['lokBeli'];
$_POST['stat_id'] != '' ? $stat_id = $_POST['stat_id'] : $stat_id = $_GET['stat_id'];
$_POST['txtNmBrg'] != '' ? $txtNmBrg = $_POST['txtNmBrg'] : $txtNmBrg = $_GET['txtNmBrg'];
if ($txtNopp != '') {
	$whr .= " and nopo like '".$txtNopp."%'";
}

if ($supplier_id != '') {
	$whr .= "and kodesupplier='".$supplier_id."'";
}

if ($tgl_cari != '') {
	$whr .= "and tanggal='".$tgl_cari."'";
}
else if ($periode != '') {
	$whr .= "and tanggal >= '".$periode."-01' and tanggal <= '".$periode."-31'";
}

if ($lokBeli != '') {
	$whr .= "and lokalpusat='".$lokBeli."'";
}

if ($txtNmBrg != '') {
	$whr .= "and namabarang like '".$txtNmBrg."%'";
}

if ($stat_id == '0') {
	$whr .= "and statuspo!='3'";
}
else if ($stat_id == '1') {
	$whr .= "and statuspo='3'";
}
$kodeorg=substr($_SESSION['empl']['lokasitugas'], 0,3);
$sData = 'select distinct(nopp) as nopp from ' . $dbname . ".log_po_vw where kodeorg like '%".$kodeorg."%' AND nopp!='' ". $whr ." ORDER BY SUBSTRING(nopp,5,2), LEFT(nopp,3) ";

($qData = mysql_query($sData)) || true;

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $bdr . ' class=sortable>';
$tab .= '<thead>';
$tab .= '<tr>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['nopp'] . '</td><td align=center> Detail </td></tr></thead><tbody>';

while ($rData = mysql_fetch_assoc($qData)) {


	$tab .= '<tr class=rowcontent><td nowrap>' . $rData['nopp'] . '</td><td>';

	$tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
	$tab .= '<tr>';
	$tab .= '<td ' . $bg . ' width=100>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td ' . $bg . ' width=300>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td ' . $bg . ' width=50> Qty </td>';
	$tab .= '<td ' . $bg . ' width=50>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td ' . $bg . ' width=150>' . $_SESSION['lang']['nopo'] . '</td>';
	$tab .= '<td ' . $bg . ' width=150>' . $_SESSION['lang']['nodok'] . '</td></tr>';

	$sData1 = 'select nopo,jumlahpesan,namabarang,kodebarang,satuan from ' . $dbname . ".log_po_vw where kodeorg like '%".$kodeorg."%' and nopp='".$rData['nopp']."' ". $whr ." ORDER BY SUBSTRING(nopo,5,2), LEFT(nopo,3) ";
	($qData1 = mysql_query($sData1)) || true;
	while ($rData1 = mysql_fetch_assoc($qData1)) {

	$sNodok = 'select notransaksi from ' . $dbname . '.log_transaksi_vw' . "\r\n" . ' where nopo=\'' . $rData1['nopo'] . '\' and kodebarang=\'' . $rData1['kodebarang'] . '\'';

	#exit(mysql_error($conn));
	($qNodok = mysql_query($sNodok)) || true;
	$rNodok = mysql_fetch_assoc($qNodok);

	$tab .= '<tr class=rowcontent><td>' . $rData1['kodebarang'] . '</td>';
	$tab .= '<td>' . $rData1['namabarang'] . '</td>';
	$tab .= '<td>' . $rData1['jumlahpesan'] . '</td>';
	$tab .= '<td>' . $rData1['satuan'] . '</td>';
	$tab .= '<td>' . $rData1['nopo'] . '</td>';
	$tab .= '<td>' . $rNodok['notransaksi'] . '</td></tr>';
	}
	$tab .= '</table></td></tr>';
}

$bdr = 0;

if ($proses == 'excel') {
	$bdr = 1;
	$bg = 'align=center bgcolor=#DEDEDE';
}


$tab .= '</tbody></table>';

switch ($proses) {
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
	case 'preview':
		echo $tab;
		break;

	case 'excel':
		$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
		$tglSkrg = date('Ymd');
		$nop_ = 'LaporanListPp';

		if (0 < strlen($tab)) {
			if ($handle = opendir('tempExcel')) {
				while (false !== $file = readdir($handle)) {
					if (($file != '.') && ($file != '..')) {
						@unlink('tempExcel/' . $file);
					}
				}

				closedir($handle);
			}

			$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

			if (!fwrite($handle, $tab)) {
				echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
				exit();
			}
			else {
				echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
			}

			closedir($handle);
		}

		break;
}

?>
