<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$thnbudget = $_POST['thnbudget'];
@$luas = 0;
$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeorg . '%\' and tahunbudget=\'' . $thnbudget . '\' and statusblok in (\'TBM\',\'TM\') group by tahunbudget,kodeblok';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$luas += $bar->luas;
}

$str = 'select a.*,b.namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg=\'' . $kodeorg . '\'';
$res = mysql_query($str);
$no = 0;
$rpperha = 0;

while ($bar = mysql_fetch_object($res)) {
	@$rpperha = $bar->rupiah / $luas;
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $no . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luas, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
	$tt += $bar->rupiah;
	$t01 += $bar->rp01;
	$t02 += $bar->rp02;
	$t03 += $bar->rp03;
	$t04 += $bar->rp04;
	$t05 += $bar->rp05;
	$t06 += $bar->rp06;
	$t07 += $bar->rp07;
	$t08 += $bar->rp08;
	$t09 += $bar->rp09;
	$t10 += $bar->rp10;
	$t11 += $bar->rp11;
	$t12 += $bar->rp12;
}

echo '<tr class=rowcontent>' . "\r\n" . '           <td colspan=4>TOTAL</td>' . "\r\n" . '           <td align=right>' . number_format($tt, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($tt / $luas, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($t01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($t12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>' . "\r\n" . '         <tr class=rowcontent>' . "\r\n" . '           <td colspan=18 style=\'color:red;\'>Luas : ' . number_format($luas, 0, '.', ',') . ' Ha (Total Planted) TM & TBM</td>' . "\r\n" . '         </tr>';

?>
