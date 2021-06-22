<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
include_once 'lib/rTable.php';

$param = $_POST;
$kodeorg=$param['kodeorg'];
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi 
		where kodeorg ='".$kodeorg."' and tutupbuku=0";

$res = mysql_query($str);

while ($bar = mysql_fetch_array($res)) {
    $tgsampai = $bar['tanggalsampai'];
    $tgmulai = $bar['tanggalmulai'];
    $periode = $bar['periode'];
}
if ($tgmulai == '' || $tgsampai == '') {
    exit('Error: Accounting period is not registered');
}

$str = 'select * from ' . $dbname . ".flag_alokasi where kodeorg='" .$param['kodeorg']. "' and periode='" . $param['periode'] . "' AND tipe='WORKSHOP' ";
$res = mysql_query($str);

if (mysql_num_rows($res) > 0) {
		exit('Error: Alokasi WORKSHOP Sudah Dilakukan');
}


echo "<button  onclick=prosesAlokasiBengkel(1) id=btnproses>Proses</button>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td>No</td><td>Kode Kendaraan</td><td>Periode</td><td>Nomor Akun</td><td>Biaya</td>
</tr></thead><tbody>";

$no = 0;
$kodevhc= '';
$noakun= '';
$total_biaya = 0;
$total = 0.01;
$str1= " SELECT a.kodeorg, a.noakun, namaakun, ROUND(sum(debet)-sum(kredit),2) as total from keu_jurnaldt_vw a LEFT JOIN keu_5akun b ON a.noakun=b.noakun where kodevhc = '' and a.noakun like '41101%' and periode = '".$param['periode']."' AND a.kodeorg ='".$kodeorg."'
			group by a.noakun,a.kodeorg";

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
	$kodeorg= $rows1['kodeorg'];
	$noakun= $rows1['noakun'];
	$namaakun= $rows1['namaakun'];
	$total= $rows1['total'];

	echo "<tr class=rowcontent id='row".$no."'><td>".$no."
			<td valign='top' id='kodeorg".$no."' >".$kodeorg."</td>
			<td valign='top' id='periode".$no."'>".$periode."</td>
			<td valign='top' id='noakun".$no."'>".$noakun." - ".$namaakun."</td>
			<td valign='top' id='jumlah".$no."' align=right>".$total."</td><td valign='top' id='jenis".$no."'>ALKJAM</td>";

	$jamunit= "select sum(downtime) as jumkerja from 
				vhc_penggantianht 
				where posting = 1
				and kodeorg = '".$kodeorg."'   
				and tanggal >= '".$tgmulai."' AND tanggal<='".$tgsampai."' 
				";
	
	$resjamunit = mysql_query($jamunit);
	$totaljam=mysql_fetch_assoc($resjamunit);
	$jamperunit=$totaljam['jumkerja'];			

	// tarik data di transaksi kendaraan, dapatkan total jumlah kerjanya (KM/HM)
	$uangkerja_satuan= 0.01;
	$str2= "select a.kodevhc, sum(downtime) as jumkerja, b.jenisvhc, detailvhc, c.noakun from 
				vhc_penggantianht a left join vhc_5master b on a.kodevhc=b.kodevhc left join vhc_5jenisvhc c ON b.jenisvhc=c.jenisvhc 
				where a.posting = 1
				and a.kodeorg = '".$kodeorg."'
				and a.tanggal >= '".$tgmulai."' AND a.tanggal<='".$tgsampai."' 
				group by a.kodevhc";
	$res2 = mysql_query($str2);

	echo "<td>";
	echo "<table><tr class=rowheader><td width='250'> unit </td> <td width='75'> noakun </td> <td width='100'> jumlah jam </td> <td width='120'> biaya / unit </td>";
	while ($rows2 = mysql_fetch_array($res2)) {
		$uangkerja_satuan= $total / $jamperunit * $rows2['jumkerja']; 

		echo "<tr class=rowcontent>
			<td id='kodevhc".$no."'>".$rows2['kodevhc']." ".$rows2['detailvhc']."</td>
			<td id='noakun1".$no."'>".$rows2['noakun']."</td>
			<td id='jumkerja".$no."' align=right>".number_format($rows2['jumkerja'],2)."</td>
			<td id='jmlah".$no."' align=right> ".number_format($uangkerja_satuan, 2)."</td>
			";
		echo "</tr>";

/*

			$str3= "namakegiatan, noakun, sum(c.jumlah) as jumkerja from 
				vhc_runht b left join vhc_rundt c on b.notransaksi = c.notransaksi LEFT JOIN vhc_kegiatan v ON c.jenispekerjaan=v.kodekegiatan
				where b.posting = 1
				and b.kodevhc = '".$kodevhc."' and c.alokasibiaya = '".$rows2['alokasibiaya']."'
				and !(c.alokasibiaya is null) and !(c.jenispekerjaan is null) and (!(c.jumlah is null) or c.jumlah>0)   
				and b.notransaksi not in (select distinct nojurnal from keu_jurnalht)
				and b.tanggal >= '".$tgmulai."' AND b.tanggal<='".$tgsampai."' 
				;";
			$res3 = mysql_query($str3);
				echo "<td>";
	echo "<table><tr class=rowheader><td width='75'> unit </td> <td width='100'> jumlah jam </td> <td width='110'> biaya / unit </td>";

		while ($rows3 = mysql_fetch_array($res3)) {
		$uangkerja= $total / $jamperunit * $rows2['jumkerja']; 

		echo "<tr class=rowcontent><td id='kodevhc".$no."'>".$rows2['alokasibiaya']."</td>
			<td id='noakun".$no."' align=right>".number_format($rows2['jumkerja'],2)."</td>
			<td id='jumlah".$no."' align=right>Rp. ".number_format($uangkerja_satuan, 2, ',', '.')."</td>";
		echo "</tr>";	


   
	
	//Mulai Proses Penjurnalan
	//Setelah diklik Tombol Proses
	
		// sebar per transaksi kendaraan, nojurnal menggunakan notransaksi
/*		$uang_jurnal= 0.01;
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
		}	
*/			// insert Jurnal Header
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
	echo "</table></td>";
	
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