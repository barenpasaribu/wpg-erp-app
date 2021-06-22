<?php


function ambilviewbox($dbname, $conn, $kebun)
{
	$str1 = 'SELECT * FROM ' . $dbname . '.kebun_peta WHERE kodeorg = \'' . $kebun . '\'';

	#exit(mysql_error($conn));
	($query = mysql_query($str1)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$viewbox = $res['viewbox'];
	}

	$pengurangx = $pengurangy = 0;
	$asd = explode(' ', $viewbox);
	if ((1000 < $asd[0]) || ($asd[0] < -1000)) {
		$qwe = floor($asd[0] / 1000);
		$qwex = explode('.', $qwe);
		$qwe = $qwex[0];
		$qwe *= 1000;
		$asd -= 0;
		$pengurangx = $qwe;
	}

	if ((1000 < $asd[1]) || ($asd[1] < -1000)) {
		$qwe = $asd[1] / 1000;
		$qwex = explode('.', $qwe);
		$qwe = $qwex[0];
		$qwe *= 1000;
		$asd -= 1;
		$pengurangy = $qwe;
	}

	$asd[4] = $pengurangx;
	$asd[5] = $pengurangy;
	return $asd;
}

function tengahx($ja0, $spel)
{
	$lastx = $ja0;
	$splitsp = explode(' ', trim($spel));

	if (!empty($splitsp)) {
		foreach ($splitsp as $londiko) {
			if (trim($londiko) != 'Z') {
				$splitko = explode(',', $londiko);
				$lastx = $lastx + $splitko[0];
				$spko[] = $lastx;
			}
		}
	}

	$maxx = max($spko);
	$minn = min($spko);
	$tengahx = $minn + (($maxx - $minn) / 2);
	return $tengahx;
}

function tengahy($ja1, $spel)
{
	$lastx = $ja1;
	$splitsp = explode(' ', trim($spel));

	if (!empty($splitsp)) {
		foreach ($splitsp as $londiko) {
			if (trim($londiko) != 'Z') {
				$splitko = explode(',', $londiko);
				$lastx = $lastx + $splitko[1];
				$spko[] = $lastx;
			}
		}
	}

	$maxx = max($spko);
	$minn = min($spko);
	$tengahx = $minn + (($maxx - $minn) / 2);
	return $tengahx;
}

function lebarx($ja0, $spel)
{
	$lastx = $ja0;
	$splitsp = explode(' ', trim($spel));

	if (!empty($splitsp)) {
		foreach ($splitsp as $londiko) {
			if (trim($londiko) != 'Z') {
				$splitko = explode(',', $londiko);
				$lastx = $lastx + $splitko[0];
				$spko[] = $lastx;
			}
		}
	}

	$maxx = max($spko);
	$minn = min($spko);
	$tengahx = $maxx - $minn;
	return $tengahx;
}

function lebary($ja1, $spel)
{
	$lastx = $ja1;
	$splitsp = explode(' ', trim($spel));

	if (!empty($splitsp)) {
		foreach ($splitsp as $londiko) {
			if (trim($londiko) != 'Z') {
				$splitko = explode(',', $londiko);
				$lastx = $lastx + $splitko[1];
				$spko[] = $lastx;
			}
		}
	}

	$maxx = max($spko);
	$minn = min($spko);
	$tengahx = $maxx - $minn;
	return $tengahx;
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$_POST['kodeorg'] == '' ? $kodeorg = $_GET['kodeorg'] : $kodeorg = $_POST['kodeorg'];
$_POST['nopersil'] == '' ? $nopersil = $_GET['nopersil'] : $nopersil = $_POST['nopersil'];
$kebun = $kodeorg;
$asd = ambilviewbox($dbname, $conn, $kebun);
$viewbox = $asd[0] . ' ' . $asd[1] . ' ' . $asd[2] . ' ' . $asd[3];
$pengurangx = $asd[4];
$pengurangy = $asd[5];

if ($nopersil != '') {
	$warna = 'red';
	$str1 = 'SELECT * FROM ' . $dbname . '.kebun_peta WHERE tipe in (\'grtt\') and kodeorg like \'' . $kebun . '%\'' . "\r\n" . '        and kodeorg like \'%' . $nopersil . '%\'';

	#exit(mysql_error($conn));
	($query = mysql_query($str1)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$splitel = explode('l', $res['path']);
		$splitem = explode('M', $splitel[0]);
		$splitkoma = explode(',', $splitem[1]);
		$jadinya0 = $splitkoma[0] - $pengurangx;
		$jadinya1 = $splitkoma[1] - $pengurangy;
		$arrxm[$res['kodeorg']] = tengahx($jadinya0, $splitel[1]);
		$arrym[$res['kodeorg']] = tengahy($jadinya1, $splitel[1]);
		$arrxw[$res['kodeorg']] = lebarx($jadinya0, $splitel[1]);
		$arryw[$res['kodeorg']] = lebary($jadinya1, $splitel[1]);
		$arrkg[$res['kodeorg']] = $res['kodeorg'];
	}

	if (!empty($arrkg)) {
		foreach ($arrkg as $kodeorg) {
			$xnya = $arrxm[$kodeorg] - ($arrxw[$kodeorg] / 2);
			$ynya = $arrym[$kodeorg] - ($arryw[$kodeorg] / 2);
			$lnya = $arrxw[$kodeorg];
			$tnya = $arryw[$kodeorg];
			$viewbox = $xnya . ' ' . $ynya . ' ' . $lnya . ' ' . $tnya;
		}
	}
}

echo '<table class=sortable border=0 cellspacing=0 width="100%" height"100%">';
echo '<tr>';
echo '<td width=200 valign=top align=left>';
echo '<table>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center onclick=zoommap(0.6666666666666667) style=\'background-color:#DEDEDE;cursor:pointer;width:40px\' title=\'Zoom In\'>[ + ]</td>' . "\r\n" . '            <td align=center style=\'width:20px\'></td>' . "\r\n" . '            <td align=center style=\'background-color:#DEDEDE;width:30px\'></td>' . "\r\n" . '            <td align=center onclick=movemap(\'y\',-10) style=\'background-color:#DEDEDE;cursor:pointer;width:30px\' title=\'Move North\'>[ ^ ]</td>' . "\r\n" . '            <td align=center style=\'background-color:#DEDEDE;width:30px\'></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td align=center onclick=zoommap(1.5) style=\'background-color:#DEDEDE;cursor:pointer;width:30px\' title=\'Zoom Out\'>[ - ]</td>' . "\r\n" . '            <td align=center style=\'width:30px\'></td>' . "\r\n" . '            <td align=center onclick=movemap(\'x\',-10) style=\'background-color:#DEDEDE;cursor:pointer;width:30px\' title=\'Move West\'>[ < ]</td>' . "\r\n" . '            <td align=center onclick=movemap(\'y\',10) style=\'background-color:#DEDEDE;cursor:pointer;width:30px\' title=\'Move South\'>[ v ]</td>' . "\r\n" . '            <td align=center onclick=movemap(\'x\',10) style=\'background-color:#DEDEDE;cursor:pointer;width:30px\' title=\'Move East\'>[ > ]</td>' . "\r\n" . '        </tr>' . "\r\n" . '        </table>';
echo '<legend>Info</legend>' . "\r\n" . '        Last click: <label id=tempattulisan></label>    ' . "\r\n" . '        <input type=hidden id=posx name=posx value=0>' . "\r\n" . '        <input type=hidden id=posy name=posy value=0>' . "\r\n" . '        <input type=hidden id=posorigx name=posorigx value=0>' . "\r\n" . '        <input type=hidden id=posorigy name=posorigy value=0>' . "\r\n" . '        <input type=hidden id=drag name=drag value=0>' . "\r\n" . '        <input type=hidden id=zoom name=zoom value=1>' . "\r\n" . '        <input type=hidden id=origwidth name=origwidth value=' . $asd[2] . '>' . "\r\n" . '        <input type=hidden id=origheight name=origheight value=' . $asd[3] . '>';
echo '</td>';
echo '<td width=800 valign=top align=left>';
$warna = 'white';
echo '<svg id=map onmousemove="geserklik(evt)" onmousedown="mulaiklik(evt)" onmouseup="selesaiklik(evt)" version="1.1" baseProfile="full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" ' . "\r\n" . '        xml:space="preserve" preserveAspectRatio="xMinYMin meet"  width="100%" height="450" viewBox="' . $viewbox . '">';
echo '<g id=blok style="display:inline;fill-rule:evenodd">';
echo '<desc>Layer ' . $res['kodeorg'] . '</desc>';
$str1 = 'SELECT * FROM ' . $dbname . '.kebun_peta WHERE tipe in (\'kebun\', \'divisi\', \'blok\') and kodeorg like \'' . $kebun . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($str1)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$div = substr($res['kodeorg'], 4, 2);
	$splitel = explode('l', $res['path']);
	$splitem = explode('M', $splitel[0]);
	$splitkoma = explode(',', $splitem[1]);
	$jadinya0 = $splitkoma[0] - $pengurangx;
	$jadinya1 = $splitkoma[1] - $pengurangy;
	$trilili = 'M' . $jadinya0 . ',' . $jadinya1 . ' l ' . $splitel[1];

	if (strlen(trim($res['kodeorg'])) == 10) {
		echo '<path id="' . $res['kodeorg'] . '" d="' . $trilili . '" title=\'' . $res['kodeorg'] . '\'' . "\r\n" . '            onmouseover="evt.target.setAttribute(\'opacity\', \'0.25\')"' . "\r\n" . '            onmouseout="evt.target.setAttribute(\'opacity\', \'0.5\')"' . "\r\n" . '            onclick="getpersil(\'' . $res['kodeorg'] . '\',\'tempattulisan\')" opacity=0.5' . "\r\n" . '            style="fill:' . $warna . ';stroke-linejoin:round;stroke:black;stroke-width:1;cursor:pointer;"/>';
	}
}

$str1 = 'SELECT * FROM ' . $dbname . '.pad_lahan WHERE unit like \'' . $kebun . '%\'' . "\r\n" . '        ';

#exit(mysql_error($conn));
($query = mysql_query($str1)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$kamuspemilik[$res['lokasi']] = $res['pemilik'];
	$kamuslahan[$res['lokasi']] = $res['idlahan'];
}

$warna = 'red';
$str1 = 'SELECT * FROM ' . $dbname . '.kebun_peta WHERE tipe in (\'grtt\') and kodeorg like \'' . $kebun . '%\'' . "\r\n" . '        and kodeorg like \'%' . $nopersil . '%\'';

#exit(mysql_error($conn));
($query = mysql_query($str1)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$splitel = explode('l', $res['path']);
	$splitem = explode('M', $splitel[0]);
	$splitkoma = explode(',', $splitem[1]);
	$jadinya0 = $splitkoma[0] - $pengurangx;
	$jadinya1 = $splitkoma[1] - $pengurangy;
	$trilili = 'M' . $jadinya0 . ',' . $jadinya1 . ' l ' . $splitel[1];
	echo '<path id="' . $res['kodeorg'] . '" d="' . $trilili . '" title=\'' . $res['kodeorg'] . '\'' . "\r\n" . '            onmouseover="evt.target.setAttribute(\'opacity\', \'0.25\')"' . "\r\n" . '            onmouseout="evt.target.setAttribute(\'opacity\', \'0.5\')"' . "\r\n" . '            onclick="ptintPDF(\'' . $kamuslahan[$res['kodeorg']] . '\',\'' . $kamuspemilik[$res['kodeorg']] . '\');" opacity=0.5' . "\r\n" . '            style="fill:' . $warna . ';stroke-linejoin:round;stroke:white;stroke-width:1;cursor:pointer;"/>';
	$arrxg[$res['kodeorg']] = tengahx($jadinya0, $splitel[1]);
	$arryg[$res['kodeorg']] = tengahy($jadinya1, $splitel[1]);
	$arrkg[$res['kodeorg']] = $res['kodeorg'];
}

if (!empty($arrkg)) {
	foreach ($arrkg as $kodeorg) {
		echo '<text id="g' . $kodeorg . '" x="' . $arrxg[$kodeorg] . '" y="' . $arryg[$kodeorg] . '" transform="rotate(-30 ' . $arrxg[$kodeorg] . ',' . $arryg[$kodeorg] . ')"' . "\r\n" . '        font-family="Verdana" style="display:block;" font-size="75" stroke-width="1.5" stroke="white" fill="red" ><[' . $arrkg[$kodeorg] . '</text>';
	}
}

echo '</g>';
echo '</svg>';
echo '</td>';
echo '</tr>';
echo '</table>';
exit();
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['tanggal'] == '' ? $tanggal = $_GET['tanggal'] : $tanggal = $_POST['tanggal'];
$_POST['region'] == '' ? $region = $_GET['region'] : $region = $_POST['region'];
if (($proses == 'preview') || ($proses == 'excel') || ($region != '')) {
	if ($tanggal == '') {
		exit('Error: All field required');
	}

	$str = 'select * from ' . $dbname . '.sdm_5tipekaryawan' . "\r\n" . '        where 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$tipekar[$bar->id] = $bar->id;
		$artitkr[$bar->id] = $bar->tipe;
	}

	if ($region != '') {
		$str = 'select * from ' . $dbname . '.bgt_regional_assignment' . "\r\n" . '            where regional = \'' . $region . '\'';
	}
	else {
		$str = 'select * from ' . $dbname . '.bgt_regional_assignment' . "\r\n" . '            where 1';
	}

	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		if ($region != '') {
			$regional[$bar->kodeunit] = $bar->kodeunit;
		}
		else {
			$unitreg[$bar->kodeunit] = $bar->regional;
			$regional[$bar->regional] = $bar->regional;
		}
	}

	$str = 'select * from ' . $dbname . '.datakaryawan' . "\r\n" . '        where tanggalmasuk <= \'' . tanggalsystem($tanggal) . '\' and (tanggalkeluar >= \'' . tanggalsystem($tanggal) . '\' or tanggalkeluar is NULL) ';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		if ($region != '') {
			$qwe = $bar->lokasitugas;
		}
		else {
			$qwe = $unitreg[$bar->lokasitugas];
		}

		$jumlahkar[$qwe] += $bar->tipekaryawan;
	}

	if ($proses != 'excel') {
		$brd = 0;
		$bgcolor = '';
	}
	else {
		$tab .= $_SESSION['lang']['summary'] . ' ' . $_SESSION['lang']['karyawan'] . '<br>Tanggal: ' . $tanggal . ' ';
		$brd = 1;
		$bgcolor = 'bgcolor=#DEDEDE';
	}

	if ($region == '') {
		$region = $_SESSION['lang']['regional'];
	}
	else if ($proses != 'excel') {
		$tab .= '<img onclick=level1excel(event,\'sdm_slave_2summarykaryawan.php\',\'' . $tanggal . '\',\'' . $region . '\') src=images/excel.jpg class=resicon title=\'MS.Excel\'>';
	}

	$tab .= "\r\n" . '    <table width=100% cellspacing=1 border=' . $brd . '>' . "\r\n" . '    <thead>' . "\r\n" . '    <tr>' . "\r\n" . '        <td ' . $bgcolor . '>' . $region . '</td>';

	if (!empty($regional)) {
		foreach ($regional as $reg) {
			if ($region != '') {
				$tab .= '<td ' . $bgcolor . ' align=center title=\'Click to details...\' onclick=getlevel1(\'' . $tanggal . '\',\'' . $reg . '\')>' . $reg . '</td>';
			}
		}
	}

	$tab .= "\r\n" . '        <td ' . $bgcolor . ' align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '    </tr>        ' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>';

	if (!empty($tipekar)) {
		foreach ($tipekar as $tkr) {
			$tab .= '<tr class=rowcontent>' . "\r\n" . '        <td>' . $artitkr[$tkr] . '</td>';
			$total[$tkr] = 0;

			if (!empty($regional)) {
				foreach ($regional as $reg) {
					$tab .= '<td align=right>' . number_format($jumlahkar[$reg][$tkr]) . '</td>';
					$total += $tkr;
					$totalgrand += $reg;
				}
			}

			$tab .= "\r\n" . '        <td align=right>' . number_format($total[$tkr]) . '</td>' . "\r\n" . '        </tr>';
		}
	}

	$tab .= '<tr class=rowcontent>' . "\r\n" . '    <td>' . $_SESSION['lang']['total'] . '</td>';
	$totalnya = 0;

	if (!empty($regional)) {
		foreach ($regional as $reg) {
			$tab .= '<td align=right>' . number_format($totalgrand[$reg]) . '</td>';
			$totalnya += $totalgrand[$reg];
		}
	}

	$tab .= "\r\n" . '    <td align=right>' . number_format($totalnya) . '</td>' . "\r\n" . '    </tr>';
	$tab .= '</tbody></table>';
}
else if ($proses == 'level1') {
}

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'level1':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$nop_ = 'summary_karyawan_' . $tanggal . '_' . $region;

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
			echo '<script language=javascript1.2>' . "\r\n" . '                parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '                </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '                window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '                </script>';
		}

		closedir($handle);
	}

	break;
}

?>
