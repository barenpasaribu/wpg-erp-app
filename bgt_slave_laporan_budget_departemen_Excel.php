<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahun = $_GET['tahun'];
$departemen = $_GET['departemen'];

if ($tahun == '') {
	echo 'WARNING: silakan mengisi tahun.';
	exit();
}

if ($departemen == '') {
	echo 'WARNING: silakan mengisi departemen.';
	exit();
}

$stream = '';
$warnalatar = '#77ff77';
$stream .= '<table class=sortable cellspacing=1 border=1 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>No.</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['alokasibiaya'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" rowspan=2 align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '            <td bgcolor="' . $warnalatar . '" colspan=12 align=center>Distribusi</td>' . "\r\n" . '        </tr>';
$stream .= '<tr>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jan</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Feb</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Mar</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Apr</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>May</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jun</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Jul</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Aug</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Sep</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Oct</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Nov</td>' . "\r\n" . '           <td bgcolor="' . $warnalatar . '" align=center>Dec</td>' . "\r\n" . '       </tr>';
$stream .= '</thead>' . "\r\n" . '    <tbody>';
$str = 'select noakun,namaakun from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1  order by noakun' . "\r\n" . '                    ';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$noakun[$bar->noakun] = $bar->namaakun;
}

$str = 'select * from ' . $dbname . '.bgt_dept where departemen = \'' . $departemen . '\' and tahunbudget = \'' . $tahun . '\' order by noakun, alokasibiaya';
$no = 0;
$jumlahan = $d01an = $d02an = $d03an = $d04an = $d05an = $d06an = $d07an = $d08an = $d09an = $d10an = $d11an = $d12an = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$stream .= '<tr class=rowcontent>' . "\r\n" . '       <td align=center>' . $no . '</td>' . "\r\n" . '       <td align=left>' . $bar->noakun . ' - ' . $noakun[$bar->noakun] . '</td>' . "\r\n" . '       <td align=left>' . $bar->keterangan . '</td>' . "\r\n" . '       <td align=center>' . $bar->alokasibiaya . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->jumlah) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d01) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d02) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d03) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d04) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d05) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d06) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d07) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d08) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d09) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d10) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d11) . '</td>' . "\r\n" . '       <td align=right>' . number_format($bar->d12) . '</td>' . "\r\n" . '    </tr>';
	$jumlahan += $bar->jumlah;
	$d01an += $bar->d01;
	$d02an += $bar->d02;
	$d03an += $bar->d03;
	$d04an += $bar->d04;
	$d05an += $bar->d05;
	$d06an += $bar->d06;
	$d07an += $bar->d07;
	$d08an += $bar->d08;
	$d09an += $bar->d09;
	$d10an += $bar->d10;
	$d11an += $bar->d11;
	$d12an += $bar->d12;
}

$stream .= '<tr>' . "\r\n" . '       <td colspan=4 align=center>Total</td>' . "\r\n" . '       <td align=right>' . number_format($jumlahan) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d01an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d02an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d03an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d04an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d05an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d06an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d07an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d08an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d09an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d10an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d11an) . '</td>' . "\r\n" . '       <td align=right>' . number_format($d12an) . '</td>' . "\r\n" . '    </tr>';
$stream .= '    </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\t\t" . ' ' . "\r\n" . '   </table>';
$stream .= 'Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'bgt_departemen_' . $tahun . ' ' . $departemen;

if (0 < strlen($stream)) {
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
}

?>
