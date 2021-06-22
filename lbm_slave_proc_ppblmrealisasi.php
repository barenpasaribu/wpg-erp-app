<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/fpdf.php';

if ($_GET['proses'] != '') {
	$_POST = $_GET;
}

$proses = $_POST['proses'];
$tipe = $_POST['tipe'];
$periode = $_POST['periode'];
$judul = $_POST['judul'];
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (($proses == 'preview') || ($proses == 'excel')) {
	if ($periode == '') {
		exit('Error: Field required');
	}
}

$arr = '##periode##judul';
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
$sBln = "select distinct count(kodebarang) as totBlmpp,purchaser,substr(tglAlokasi,6,2) as periode ".
"from $dbname .log_prapodt a ".
"left join $dbname.log_prapoht b on a.nopp=b.nopp ".
//	"where purchaser!='0000000000' ".
"where purchaser in " .
"( SELECT karyawanid " .
	"FROM datakaryawan " .
	"WHERE karyawanid IN ( " .
	"SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= " .
	"( " .
	"SELECT kodeorganisasi FROM datakaryawan d " .
	"INNER JOIN user u ON u.karyawanid=d.karyawanid " .
	"WHERE u.namauser='" .$_SESSION['standard']['username'] ."'".
	") " .
	")".
"and create_po=0 and ".
"ditolakoleh='0000000000' and left(tglAlokasi,4)='" . $periode . "' ".
"group by purchaser,substr(tglAlokasi,6,2)  order by substr(tglAlokasi,6,2) asc";
$totPur=array();
$totPur2=array();
#exit(mysql_error($conn));
($qBln = mysql_query($sBln)) || true;
while ($rBln = mysql_fetch_assoc($qBln)) {
	$dtJmlhPP[$rBln['purchaser']][$rBln['periode']] = $rBln['totBlmpp'];
	$dtPur[$rBln['purchaser']] = $rBln['purchaser'];
	$totPur[$rBln['purchaser']] += $rBln['totBlmpp'];
}

$sBln2 = "select distinct count(kodebarang) as totBlmpp,purchaser,substr(tglAlokasi,6,2) as periode ".
"from $dbname.log_prapodt a ".
"left join $dbname.log_prapoht b on a.nopp=b.nopp ".
//	"where purchaser!='0000000000' ".
	"where purchaser in " .
	"( SELECT karyawanid " .
	"FROM datakaryawan " .
	"WHERE karyawanid IN ( " .
	"SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= " .
	"( " .
	"SELECT kodeorganisasi FROM datakaryawan d " .
	"INNER JOIN user u ON u.karyawanid=d.karyawanid " .
	"WHERE u.namauser='" .$_SESSION['standard']['username'] ."'".
	") " .
	")".
"and create_po=1 and ".
"ditolakoleh='0000000000' and left(tglAlokasi,4)='" . $periode . "' ".
"group by purchaser,substr(tglAlokasi,6,2)  order by substr(tglAlokasi,6,2) asc";

#exit(mysql_error($conn));
($qBln2 = mysql_query($sBln2)) || true;

while ($rBln2 = mysql_fetch_assoc($qBln2)) {
	$totPur2[$rBln2['purchaser']] += $rBln2['totBlmpp'];
}

$bg = '';
$brdr = 0;

if ($proses == 'excel') {
	$bg = 'align=center bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['tahun'] . ' : ' . $periode . '</td>' . "\r\n" . '     </tr>    ' . "\r\n" . '</table>';
}

$cekDt = count($dtPur);
if (($proses == 'preview') || ($proses == 'excel')) {
	if ($cekDt == 0) {
		exit('Error:Purchser Kosong');
	}
}

if (($proses == 'preview') || ($proses == 'excel')) {
	if ($proses != 'excel') {
		$tab .= $judul;
	}

	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td ' . $bg . '>No.</td>' . "\r\n" . '    <td ' . $bg . '>' . $_SESSION['lang']['purchaser'] . '</td>';

	foreach ($optBulan as $lstBulan => $dtBulan) {
		$tab .= '<td ' . $bg . '>' . $dtBulan . '</td>';
	}

	$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['total'] . ' Pending Items</td>';
	$tab .= '<td ' . $bg . '>Purchased Items</td>';
	$tab .= '</tr></thead><tbody>';

	foreach ($dtPur as $pur) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $optNm[$pur] . '</td>';

		foreach ($optBulan as $lstBulan => $dtBulan) {
			if ($dtJmlhPP[$pur][$lstBulan] != '') {
				$tab .= '<td ' . $bg . ' align=right style=\'cursor:pointer;\' onclick=getDetailPP(\'' . $pur . '\',\'' . $lstBulan . '\',\'' . $periode . '\')>' . $dtJmlhPP[$pur][$lstBulan] . '</td>';
			}
			else {
				$tab .= '<td align=right>0</td>';
			}

			$totBulan += $lstBulan;
		}

		$tab .= '<td align=right>' . $totPur[$pur] . '</td>';
		$tab .= '<td align=right>' . $totPur2[$pur] . '</td>';
		$totalSemua += $totPur[$pur];
		$totalSemua2 += $totPur2[$pur];
		$tab .= '</tr>';
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td colspan=2>' . $_SESSION['lang']['total'] . '</td>';

	foreach ($optBulan as $lstBulan => $dtBulan) {
		$tab .= '<td align=right>' . $totBulan[$lstBulan] . '</td>';
	}

	$tab .= '<td align=right>' . $totalSemua . '</td>';
	$tab .= '<td align=right>' . $totalSemua2 . '</td>';
	$tab .= '</tbody></table>';
}

switch ($proses) {
case 'preview':
	if ($periode == '') {
		exit('Error: Field required');
	}

	echo $tab;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error: Field required');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'ppBlmRealiasi_' . $dte;

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
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;

case 'getDetPP':
	$sget = 'select distinct nopp,kodebarang,realisasi,tglAlokasi from ' . $dbname . '.log_prapodt ' . "\r\n" . '               where left(tglAlokasi,7)=\'' . $_POST['bln'] . '\' and ditolakoleh=0000000000 and' . "\r\n" . '               purchaser=\'' . $_POST['purchaser'] . '\' and create_po=0';

	#exit(mysql_error($conn));
	($qget = mysql_query($sget)) || true;
	$tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td>No.</td>';
	$tab .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['tanggal'] . ' Alokasi</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['realisasi'] . '</td>';
	$tab .= '</tr>';
	$tab .= '</thead><tbody>';
	$tab .= '<tr class=rowcontent><td colspan=3>' . $_POST['bln'] . '</td><td colspan=3>' . $optNm[$_POST['purchaser']] . '</td></tr>';

	while ($rget = mysql_fetch_assoc($qget)) {
		$noe += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $noe . '</td>';
		$tab .= '<td>' . $rget['nopp'] . '</td>';
		$tab .= '<td>' . $rget['tglAlokasi'] . '</td>';
		$tab .= '<td>' . $rget['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmBrg[$rget['kodebarang']] . '</td>';
		$tab .= '<td align=right>' . $rget['realisasi'] . '</td>';
		$tab .= '</tr>';
	}

	$tab .= '<tr><td colspan=5><button onclick=zBack()>Back</button></td></tr>';
	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'getDetPt':
	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_POST['regional'] . '\'';

	#exit(mysql_error($conn));
	($qUnit = mysql_query($sUnit)) || true;

	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		$ader += 1;

		if ($ader == 1) {
			$arte .= '\'' . $rUnit['kodeunit'] . '\'';
		}
		else {
			$arte .= ',\'' . $rUnit['kodeunit'] . '\'';
		}
	}

	$optPt = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in (' . $arte . ')';

	#exit(mysql_error($conn));
	($qPt = mysql_query($sPt)) || true;

	while ($rPt = mysql_fetch_assoc($qPt)) {
		$optPt .= '<option value=\'' . $rPt['induk'] . '\'>' . $optNmOrg[$rPt['induk']] . '</option>';
	}

	echo $optPt;
	break;
}

?>
