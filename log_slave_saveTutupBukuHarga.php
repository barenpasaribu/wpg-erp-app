<?php
set_time_limit(60000);

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$user = $_SESSION['standard']['userid'];
$period = $_POST['periode'];
$pt = $_POST['pt'];
$gudang = substr($pt,0,3);
$kodebarang = $_POST['kodebarang'];
$awal = $_POST['awal'];
$akhir = $_POST['akhir'];

$str = 'select sum(saldoawalqty) as sawal,sum(nilaisaldoawal) as sawalrp ' . "\r\n\t\t" . '      from ' . $dbname . '.log_5saldobulanan' . "\r\n\t\t" . ' where kodegudang like \''. $gudang.'%\'' . "\r\n\t\t\t" . '  and kodebarang=\'' . $kodebarang . '\' and periode=\'' . $period . '\'';
$res = mysql_query($str);
$sawal = 0;
$nilaisaldoawal = 0;

while ($bar = mysql_fetch_object($res)) {
	$sawal = $bar->sawal;
	$nilaisaldoawal = $bar->sawalrp;
}

if ($sawal == '') {
	$sawal = 0;
}

if ($nilaisaldoawal == '') {
	$nilaisaldoawal = 0;
}

if (($sawal == 0) || ($nilaisaldoawal == 0)) {
	$haratsawal = 0;
}
else {
	$haratsawal = $nilaisaldoawal / $sawal;
}

$str = 'select (sum(if(tipetransaksi=1,a.jumlah,0))-sum(if(tipetransaksi=6,a.jumlah,0))) as jumlah, 
(sum(if(tipetransaksi=1,a.hargasatuan*a.jumlah,0))-sum(if(tipetransaksi=6,a.hargasatuan*a.jumlah,0))) as hartot from ' . $dbname . '.log_transaksidt a' . "\r\n" . '       left join ' . $dbname . '.log_transaksiht b on' . "\r\n\t" . '   a.notransaksi=b.notransaksi' . "\r\n" . ' where b.kodegudang like \''.$gudang.'%\'' . "\r\n\t" . '  and a.kodebarang=\'' . $kodebarang . '\'' . "\r\n\t" . '  and b.tanggal>=' . $awal . ' and b.tanggal<=' . $akhir . ' ' . "\r\n\t" . '  and (b.tipetransaksi=1 OR b.tipetransaksi=6) and b.post=1';
$masuk = 0;
$hartotmasuk = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$masuk = $bar->jumlah;
	$hartotmasuk = $bar->hartot;
}

if ($masuk == '') {
	$masuk = 0;
}

if ($hartotmasuk == '') {
	$hartotmasuk = 0;
}

if ($masuk <= 0) {
	$haratmasuk = 0;
}
else {
	$haratmasuk = $hartotmasuk / $masuk;
}

if (($sawal + $masuk) <= 0) {
	$haratbaru = 0;
}
else {
//	saveLog($kodebarang." : ". $hartotmasuk." + ".$nilaisaldoawal." / " .$sawal." + ". $masuk);
	$haratbaru = ($hartotmasuk + $nilaisaldoawal) / ($sawal + $masuk);
}

if ($haratbaru == 0) {
	$haratbaru = $haratmasuk;
}

if ($haratbaru == 0) {
	$haratbaru = $haratsawal;
}

if ($haratbaru == 0) {
	$str = 'select hargarata from ' . $dbname . '.log_5saldobulanan where kodebarang=\'' . $kodebarang . '\' and hargarata>0' . "\r\n" . '          order by lastupdate desc limit 1';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$haratbaru = $bar->hargarata;
	}
}

if ($haratbaru == '') {
	$haratbaru = 1;
}

$str = 'update ' . $dbname . '.log_transaksidt set hargarata=round(' . $haratbaru . ',2) ' . "\r\n" . '        where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '         and notransaksi in(select notransaksi from ' . $dbname . '.log_transaksiht b' . "\r\n\t\t" . 'where b.kodegudang=\''.$pt.'\'  ' . "\r\n\t\t" . 'and b.tanggal>=' . $awal . ' and b.tanggal<=' . $akhir . "\r\n\t" . '    and b.post=1)';

$nilaimasuk=0;
if (mysql_query($str)) {

		$a="SELECT  (nilaimasuk+nilaireturgudang+nilaiterimamutasi) as nilaimasuk, (nilaipengeluaran+nilairetursupplier+nilaimutasikeluar) as nilaikeluar from log_persediaan_vw WHERE periode='".$period."' AND kodebarang='".$kodebarang."' AND kodegudang='".$pt."'";
		
        $b=mysql_query($a);
        $c=mysql_fetch_assoc($b);
        $nilaimasuk=$c['nilaimasuk'];
        $nilaikeluar=$c['nilaikeluar'];
        if($nilaimasuk==''){
        	$nilaimasuk=0;
        }
        if($nilaikeluar==''){
        	$nilaikeluar=0;
        }


		$str = "update ".$dbname.".log_5saldobulanan set hargarata=round(".$haratbaru.",2), 
				qtymasukxharga='".$nilaimasuk."', qtykeluarxharga=".$nilaikeluar.", nilaisaldoakhir=((nilaisaldoawal + '".$nilaimasuk."')-".$nilaikeluar."), saldoakhirqty=(saldoawalqty+qtymasuk-qtykeluar) where kodebarang='".$kodebarang."' and kodegudang='".$pt."' and periode='".$period."'";
		$tmpPeriod = explode('-', $period);
	    if (12 == $tmpPeriod[1]) {
            $bulanLanjut = 1;
            $tahunLanjut = $tmpPeriod[0] + 1;
        } else {
            $bulanLanjut = $tmpPeriod[1] + 1;
            $tahunLanjut = $tmpPeriod[0];
        }
        $nextperiod = $tahunLanjut.'-'.addZero($bulanLanjut, 2);

	if (mysql_query($str)) {

		$a = "select saldoakhirqty, nilaisaldoakhir from ".$dbname.".log_5saldobulanan where kodebarang='".$kodebarang."' and kodegudang='".$pt."' and periode='".$period."'";
        $b=mysql_query($a);
        $c=mysql_fetch_assoc($b);
        $nilaisaldoakhir=$c['nilaisaldoakhir'];
        $saldoakhir1=$c['saldoakhirqty'];

        $strxz = "update ".$dbname.".log_5saldobulanan set nilaisaldoawal=".$nilaisaldoakhir.",saldoawalqty=".$saldoakhir1." where kodebarang='".$kodebarang."' and kodegudang='".$pt."' and periode='".$nextperiod."'";
        mysql_query($strxz);

	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$str1 = "select tipetransaksi, a.notransaksi, a.jumlah as jumlah, round((a.hargarata*a.jumlah),2) as hartot ,kodeblok
from log_transaksidt a  left join log_transaksiht b on a.notransaksi=b.notransaksi where b.kodegudang='".$pt."' 
and a.kodebarang='".$kodebarang."' and b.tanggal>='".$awal."' and b.tanggal<='".$akhir."' and (b.tipetransaksi=2 OR b.tipetransaksi=3 OR b.tipetransaksi=5 OR b.tipetransaksi=7) and b.post='1'";
 
$hartot = 0;
$res1 = mysql_query($str1);


while ($bar1 = mysql_fetch_object($res1)) {
		
	$noreferensi=$bar1->notransaksi;
	$hartot = $bar1->hartot;
	$kodeblok = $bar1->kodeblok;


	$str2 = "select nojurnal,nourut,jumlah,noakun,kodeblok from keu_jurnaldt a where noreferensi='".$noreferensi."' AND a.kodebarang='".$kodebarang."' AND a.kodeblok='".$kodeblok."'";
	$jumlah=0;
	
	$res2 = mysql_query($str2);
	while ($bar2 = mysql_fetch_object($res2)){
	$jumlah=$bar2->jumlah;
	$nojurnal=$bar2->nojurnal;
	$nourut=$bar2->nourut;
	$noakun=$bar2->noakun;
		if($jumlah<0){
			$hartot1=$hartot*-1;
		}else{
			$hartot1=$hartot;
		}

		$str3 = "update keu_jurnaldt set jumlah='".$hartot1."' where nojurnal='".$nojurnal."' AND noakun='".$noakun."' and nourut='".$nourut."' AND '".$kodebarang."'  AND kodeblok='".$kodeblok."'";
		//saveLog($str3);
		$res3 = mysql_query($str3);

	}

}

?>
