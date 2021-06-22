<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
include_once 'lib/rTable.php';

$param = $_POST;
//$kodeorg = $param['kodeorg']
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi 
		where kodeorg ='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
$tgmulai = '';
$tgsampai = '';
$periode = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
    $tgsampai = $bar->tanggalsampai;
    $tgmulai = $bar->tanggalmulai;
    $periode = $bar->periode;
}
if ($tgmulai == '' || $tgsampai == '') {
    exit('Error: Accounting period is not registered');
}
/*
	By: FA 20190218 - untuk MIG
	Mata Uang Hardcode ke IDR
*/

// Tarik data dari Jurnal, total per Vehicle
echo "<button  onclick=prosesAlokasi(1) id=btnproses>Proses</button>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td>No</td><td>Kode Kendaraan</td><td>Nomor Akun</td><td>Biaya</td>
</tr></thead><tbody>";

$no = 0;
$kodevhc= '';
$kodeorg= '';
$noakun= '';
$total_biaya = 0;
$total = 0.01;
$str1= "select kodevhc, kodeorg, noakun, sum(debet) as total from keu_jurnaldt_vw 
			where kodevhc != '' and debet > 0 and noakun like '4%' and periode = '".$periode."'
			group by kodevhc,kodeorg;";
$res1 = mysql_query($str1);
while ($rows1 = mysql_fetch_assoc($res1)) {
	/*
	$cek1= "select kodevhc, kodeorg, noakun, sum(debet) as total from vhc_flag_alokasi 
			where kodevhc != '' and debet > 0 and noakun like '4%' and periode = '".$periode."'
				AND kodeorg ='".$_SESSION['empl']['lokasitugas']."'
			group by kodevhc,kodeorg;";
	$res1 = mysql_query($str1);
	*/
	$no++;
	$kodevhc= $rows1['kodevhc'];
	$kodeorg= $rows1['kodeorg'];
	$noakun= $rows1['noakun'];
	$total= $rows1['total'];

	echo "<tr class=rowcontent id='row".$no."'><td>".$no."
			<td id='kodevhc".$no."'>".$kodevhc."</td>
			<td id='noakun".$no."'>".$noakun."</td>
			<td id='jumlah".$no."' align=right>Rp. ".number_format($total, 2, ',', '.')."</td>";
			
	
	// tarik data di transaksi kendaraan, dapatkan total jumlah kerjanya (KM/HM)
	$uangkerja_satuan= 0.01;
	$str2= "select distinct b.kodevhc, c.alokasibiaya, sum(c.jumlah) as jumkerja from 
				vhc_runht b left join vhc_rundt c on b.notransaksi = c.notransaksi
				where b.posting = 1
				and b.kodevhc = '".$kodevhc."' and left(c.alokasibiaya,4) = '".$kodeorg."'
				and !(c.alokasibiaya is null) and !(c.jenispekerjaan is null) and (!(c.jumlah is null) or c.jumlah>0)   
				and b.notransaksi not in (select distinct nojurnal from keu_jurnalht)
				and b.tanggal like '".$periode."%'
				group by b.kodevhc, c.alokasibiaya;";
	$res2 = mysql_query($str2);
	
	while ($rows2 = mysql_fetch_assoc($res2)) {
		$uangkerja_satuan= $total / $rows2['jumkerja']; 
		
   
	/*	
	Mulai Proses Penjurnalan
	Setelah diklik Tombol Proses
	
		// sebar per transaksi kendaraan, nojurnal menggunakan notransaksi
		$uang_jurnal= 0.01;
		$str3= "select x.*, y.noakun as noakun_kredit from (
					select distinct b.kodevhc, b.tanggal, c.alokasibiaya, c.jenispekerjaan, b.notransaksi, c.jumlah from 
						vhc_runht b left join vhc_rundt c on b.notransaksi = c.notransaksi
						where b.posting = 1 
						and b.kodevhc = '".$kodevhc."' and left(c.alokasibiaya,4) = '".$kodeorg."'
						and !(c.alokasibiaya is null) and !(c.jenispekerjaan is null) and (!(c.jumlah is null) or c.jumlah>0)   
						and b.notransaksi not in (select distinct nojurnal from keu_jurnalht)
						and b.tanggal like '".$periode."%'
						order by b.kodevhc,c.alokasibiaya,c.jenispekerjaan,b.notransaksi) x
				left join vhc_kegiatan y on x.jenispekerjaan = y.kodekegiatan;";
		$res3 = mysql_query($str3);
		while ($rows3 = mysql_fetch_assoc($res3)) {
			$uang_jurnal= $uangkerja_satuan / $rows3['jumlah'];
			
			// insert Jurnal Header
			/*
			$column1 = ['nojurnal', 'kodejurnal', 'tanggal', 'tanggalentry', 'posting', 'totaldebet', 'totalkredit', 'amountkoreksi', 'noreferensi', 'autojurnal', 'matauang', 'kurs', 'revisi'];
			$data1 = [$rows3['notransaksi'],'RUN',tanggalsystemw($rows3['tanggal']),date('Ymd'),1,$uang_jurnal,-$uang_jurnal,0,$rows3['notransaksi'],1,'IDR',1,0];
			$query1 = insertQuery($dbname, 'keu_jurnalht', $data1, $column1);
			
			$query1 = 'insert into '.$dbname.".keu_jurnalht (nojurnal,kodejurnal,tanggal,tanggalentry,posting,totaldebet,totalkredit,amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) VALUES ('".$rows3['notransaksi']."','".RUN."','".$rows3['tanggal']."','".date('Ymd')."','1','".$uang_jurnal."','".-$uang_jurnal."','0','".$rows3['notransaksi']."',1,'IDR',1,0)";
			if (!mysql_query($query1)) {
				echo 'DB Error : '.mysql_error();
				exit();
			}

			// insert Jurnal Detail - Debet
			$cols2 = ['nourut', 'noakun', 'keterangan', 'jumlah', 'matauang', 'kurs', 'noaruskas', 'kodekegiatan', 'kodeasset', 'kodebarang', 'nik', 'kodecustomer', 'kodesupplier', 'kodevhc', 'nodok', 'kodeblok', 'nojurnal', 'tanggal', 'kodeorg'];
			$data2 = [1, $noakun, 'Alokasi Traksi '.$kodevhc,$rows1['kodevhc'], $uang_jurnal, 'IDR', 1, '', $rows3['jenispekerjaan'], '', '', '', '', '', $rows3['kodevhc'], $rows3['notransaksi'], '', $rows3['notransaksi'], tanggalsystemw($rows3['tanggal']), substr($rows3['notransaksi'],1,4)];
			$query2 = insertQuery($dbname, 'keu_jurnaldt', $data2, $cols2);
			if (!mysql_query($query2)) {
				echo 'DB Error : '.mysql_error();
				exit();
			}
			
			// insert Jurnal Detail - Kredit
			$cols3 = ['nourut', 'noakun', 'keterangan', 'jumlah', 'matauang', 'kurs', 'noaruskas', 'kodekegiatan', 'kodeasset', 'kodebarang', 'nik', 'kodecustomer', 'kodesupplier', 'kodevhc', 'nodok', 'kodeblok', 'nojurnal', 'tanggal', 'kodeorg'];
			$data3 = [2, $rows3['noakun_kredit'], 'Alokasi Traksi '.$kodevhc,$rows1['kodevhc'], -$uang_jurnal, 'IDR', 1, '', $rows3['jenispekerjaan'], '', '', '', '', '', $rows3['kodevhc'], $rows3['notransaksi'], '', $rows3['notransaksi'], tanggalsystemw($rows3['tanggal']), substr($rows3['notransaksi'],1,4)];
			$query3 = insertQuery($dbname, 'keu_jurnaldt', $data3, $cols3);
			if (!mysql_query($query3)) {
				echo 'DB Error : '.mysql_error();
				exit();
			}

		}
		*/
		//$total_biaya+=$total;
	}
	
}
//echo '</tr><tr><td colspan = "3" align="center"><b>Total Biaya</b></td><td>'."$total_biaya".'</td></tr></table>';

/*
//===============================================================================================================================================
$str = 'select distinct a.notransaksi,a.jenispekerjaan from '.$dbname.".vhc_rundt a left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi where b.tanggal like '".$periode."%' and a.jenispekerjaan not in (SELECT kodekegiatan FROM ".$dbname.'.vhc_kegiatan)';
$resf = mysql_query($str);
if (0 < mysql_num_rows($resf)) {
    echo "Error : There are Vehicle activity that do not have Account Number, Please contact administrator\n";
    while ($barf = mysql_fetch_object($resf)) {
        print_r($barf);
    }
    exit();
}

$str = 'select noakundebet,sampaidebet from '.$dbname.".keu_5parameterjurnal where jurnalid='WS1'";
$res = mysql_query($str);
$dariakun = '';
$sampaiakun = '';
while ($bar = mysql_fetch_object($res)) {
    $dariakun = $bar->noakundebet;
    $sampaiakun = $bar->sampaidebet;
}
if ('' == $dariakun || '' == $sampaiakun) {
    exit('Eror: Journalid for WS1 not found');
}

$str = 'select sum(debet-kredit) as jumlah from '.$dbname.".keu_jurnaldt_vw where noakun >='".$dariakun."' and noakun<='".$sampaiakun."' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and (noreferensi not in('ALK_KERJA_AB','ALK_BY_WS') or noreferensi is NULL)";
$res = mysql_query($str);
$bybengkel = 0;
while ($bar = mysql_fetch_object($res)) {
    $bybengkel = $bar->jumlah;
}
$str = 'select * from '.$dbname.".msvhc_by_operator where posting=0 and kodevhc in(select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
$res = mysql_query($str);
$str1 = 'select * from '.$dbname.".vhc_runht where posting=0 and kodevhc in(select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
$res1 = mysql_query($str1);
if (0 < mysql_num_rows($res) || 0 < mysql_num_rows($res1)) {
    $t = 'Service:\\n';
    while ($bart = mysql_fetch_object($res)) {
        $t .= $bart->notransaksi."\n";
    }
    $t .= 'Pekerjaan:\\n';
    while ($bart = mysql_fetch_object($res1)) {
        $t .= $bart->notransaksi."\n";
    }
    exit("Error: there are transactions that have not posted:\n".$t);
}

$str = 'select sum(downtime) as dt,kodevhc from '.$dbname.".msvhc_by_operator where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and posting=1 group by kodevhc";
$res = mysql_query($str);
$kend = [];
$byrinci = [];
$totaljamservice = 0;
while ($bar = mysql_fetch_object($res)) {
    $totaljamservice += $bar->dt;
    $kend[$bar->kodevhc] = $bar->dt;
}
foreach ($kend as $key => $val) {
    $byrinci[$key] = $val / $totaljamservice * $bybengkel;
}
$biayattlkend = $byrinci;
$akunkdari = '';
$akunksampai = '';
$strh = 'select distinct noakundebet,sampaidebet  from '.$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
$resh = mysql_query($strh);
while ($barh = mysql_fetch_object($resh)) {
    $akunkdari = $barh->noakundebet;
    $akunksampai = $barh->sampaidebet;
}
if ('' == $akunkdari || '' == $akunksampai) {
    exit('Error: Journal parameter for LPVHC not found');
}

$str = 'select sum(debet-kredit) as jlh,kodevhc from '.$dbname.".keu_jurnaldt_vw where kodevhc in(select kodevhc from ".$dbname.".vhc_5master  where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and nojurnal like '%".$_SESSION['empl']['lokasitugas']."%' and (noakun between '".$akunkdari."' and '".$akunksampai."') and (noreferensi not in('ALK_KERJA_AB','ALK_BY_WS') or noreferensi is NULL) group by kodevhc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $biayattlkend[$bar->kodevhc] += $bar->jlh;
}

$str = 'select sum(a.jumlah) as jlhjam,kodevhc from '.$dbname.".vhc_rundt a left join ".$dbname.".vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan left join ".$dbname.".vhc_runht c on a.notransaksi=c.notransaksi where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' and alokasibiaya!='' and jenispekerjaan!=''  and kodevhc in(select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') group by kodevhc";
$res = mysql_query($str);
$biayaperjam = [];
while ($bar = mysql_fetch_object($res)) {
    $biayaperjam[$bar->kodevhc] = $biayattlkend[$bar->kodevhc] / $bar->jlhjam;
}

echo "<button  onclick=prosesAlokasi(1) id=btnproses>Process</button>
<font ><br>Note: If it does not work please reprocessing, the old data is automatically erased.</font>
<table class=sortable cellspacing=1 border=0><thead><tr class=rowheader><td>No</td><td>Period</td>
<td>KodeVhc</td><td>Price/Hour</td><td>Type</td></tr></thead><tbody>";

$no = 0;

foreach ($byrinci as $key => $val) {
    ++$no;
    echo "<tr class=rowcontent id='row".$no."'><td>".$no."</td><td id='periode".$no."'>".$_POST['periode']."</td><td id='kodevhc".$no."'>".$key."</td><td id='jumlah".$no."' align=right>".number_format($val, 2, '.', '')."</td><td id='jenis".$no."'>BYWS</td></tr>";
}

foreach ($biayaperjam as $key => $jlh) {
    ++$no;
    echo "<tr class=rowcontent id='row".$no."'><td>".$no."</td><td id='periode".$no."'>".$_POST['periode']."</td><td id='kodevhc".$no."'>".$key."</td><td id='jumlah".$no."' align=right>".number_format($jlh, 2, '.', '')."</td><td id='jenis".$no."'>ALKJAM</td></tr>";
}

echo '</tbody><tfoot></tfoot></table>';
*/

?>