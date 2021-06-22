<?
require_once('master_validation.php');
require_once('lib/eagrolib.php');
require_once('lib/zLib.php');

$method = isset($_POST['method']) ? $_POST['method'] : null;
$nospbpopup = isset($_POST['nospbpopup']) ? $_POST['nospbpopup'] : null;
$lokasi = isset($_POST['lokasi']) ? $_POST['lokasi'] : null;
$gudang = isset($_POST['gudang']) ? $_POST['gudang'] : null;
$tanggalawal = isset($_POST['tanggalawal']) ? $_POST['tanggalawal'] : null;
$tanggalakhir = isset($_POST['tanggalakhir']) ? $_POST['tanggalakhir'] : null;
$jenisitem = isset($_POST['jenisitem']) ? $_POST['jenisitem'] : null;

function GetNoSPB($nospbpopup){
	global $dbname;
	global $lokasi;
	global $gudang;
	/*global $tanggalawal;
	global $tanggalakhir;
	global $jenisitem;*/
	global $nospbpopup;

	if($lokasi!=''){
		$whr.="AND b.untukunit = '".$lokasi."'";
	}

	if($gudang!=''){
		$whr.="AND b.kodegudang = '".$gudang."'";
	}

	/*if($tanggalawal!=''){
		$whr.="AND b.tanggal >= '".$tanggalawal."'";
	}

	if($tanggalakhir!=''){
		$whr.="AND b.tanggal <= '".$tanggalakhir."'";
	}

	if($jenisitem!=''){
		$whr.="AND e.kelompokbiaya = '".$jenisitem."'";
	}*/

	if($nospbpopup!=''){
		$whr.="AND b.nospb like '".$nospb."%'";
	}

	$String = "SELECT distinct a.notransaksi,
		b.nospb
		FROM log_transaksidt a
		LEFT JOIN ".$dbname.".log_transaksiht b ON b.notransaksi = a.notransaksi
		LEFT JOIN ".$dbname.".setup_5parameter c ON c.kode = b.jenis and c.flag = 'jenisoms'
		LEFT JOIN ".$dbname.".log_5masterbarang d ON d.kodebarang = a.kodebarang
		WHERE b.tipetransaksi = '5' ".$whr."
		";


	$Result = fetchData($String);
	#pre($String);exit();
	return $Result;
}

function GetNamaLokasi($kodeorg){

	$namaorg="";
	$slnamaorg="select namaorganisasi from organisasi where kodeorganisasi='".$kodeorg."'";
	foreach($resnamaorg as $barorg){
		$namaorg=$barorg['namaorganisasi'];
	}

	return $namaorg;
}

function GetListPemakaianBarang($lokasi, $gudang, $tanggalawal, $tanggalakhir, $jenisitem, $nospb){
	global $dbname;

	if ($nospb == '') {
		$Wherenospb = '';
	} else {
		$Wherenospb = "AND b.nospb >= '".$nospb."'";
	}

	$String = "SELECT a.notransaksi as nodokumen, a.kodebarang, a.satuan, a.jumlah,
	b.nospb,b.tanggal,
	c.nama as namajenispemakaian,
	d.kelompokbarang, d.namabarang,
	e.kelompokbiaya,
	f.namakaryawan
	FROM log_transaksidt a
	LEFT JOIN ".$dbname.".log_transaksiht b ON b.notransaksi = a.notransaksi
	LEFT JOIN ".$dbname.".setup_5parameter c ON c.kode = b.jenis and c.flag = 'jenisoms'
	LEFT JOIN ".$dbname.".log_5masterbarang d ON d.kodebarang = a.kodebarang
	LEFT JOIN ".$dbname.".log_5klbarang e ON e.kode = d.kelompokbarang
	LEFT JOIN ".$dbname.".datakaryawan f ON f.karyawanid = b.namapenerima
	WHERE b.untukunit = '".$lokasi."'
	AND b.kodegudang = '".$gudang."'
	AND b.tanggal >= '".$tanggalawal."'
	AND b.tanggal <= '".$tanggalakhir."'
	AND e.kelompokbiaya = '".$jenisitem."'
	AND b.tipetransaksi = '5'
	".$Wherenospb."
	";
	#pre($String);
	$Result = fetchData($String);
	return $Result;
}

function putertanggal($tgl){
    $qwe=explode("-",$tgl);
    return $qwe[2]."-".$qwe[1]."-".$qwe[0];
}
function GetListDetailNoTutupPaksaPR($notutuppaksa){
	global $dbname;

	$String = "select * from log_force_close_dt WHERE no_tutup_paksa like '%".$notutuppaksa."%'";

	$Result = fetchData($String);
	#pre($String);
	#pre($Result);#exit();
	return $Result;
}
switch($method){
	case 'CariNoSPB':
		$List= GetNoSPB($nospbpopup);
		#pre($ListBarang);
		$stream ="<table border=1>";
		$stream.="<thead><tr>";
		$stream.="<td>".$_SESSION['lang']['nospb']."</td>";
		$stream.="<td>".$_SESSION['lang']['notransaksi']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$ListNo = 1;
		foreach($List as $LKey => $LVal){
			$stream.="<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=CariNoSPBPick('".$ListNo."')>";
			$stream.="<td>".$LVal['nospb']."</td>";
			$stream.="<td>".$LVal['notransaksi']."</td>";
			$stream.="<input type='hidden' id='list_".$ListNo."' value='".$LVal['nospb']."'>";
			$stream.="</tr>";
			$ListNo++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;
	case'GetGudang':
		$OptGudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sGudang="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
				kodeorganisasi like '".$_POST['lokasi']."%' and tipe like 'GUDANG%' order by namaorganisasi asc";
		$qGudang=mysql_query($sGudang) or die(mysql_error($conn));
		while($rGudang=mysql_fetch_assoc($qGudang))
		{
			$OptGudang.="<option value='".$rGudang['kodeorganisasi']."'>".$rGudang['namaorganisasi']."</option>";
		}
		if($_POST['lokasi'] == '') {
			$OptGudang="<option value=''>Pilih Data</option>";
		}
		echo $OptGudang;
	break;
	case 'LoadData':
		if($_POST['tanggalawal'] != null) {
			$Newtanggalawal = putertanggal($_POST['tanggaldibuatsrch']);
		} else {
			$Newtanggaldibuatsrch = '';
		}

		if($_POST['tanggalsdsrch'] != null) {
			$Newtanggalsdsrch = putertanggal($_POST['tanggalsdsrch']);
		} else {
			$Newtanggalsdsrch = '';
		}

		$ListData = GetListPemakaianBarang($_POST['lokasi'], $_POST['gudang'], putertanggal($_POST['tanggalawal']), putertanggal($_POST['tanggalakhir']), $_POST['jenisitem'], $_POST['nospb']);
		#pre($ListData);
		$stream ="<table border=1 id='ListData' width=1100px>";//supaya gak dempet2 diberi width ==Jo 15-11-2017==
		$stream.="<thead><tr>";
		$stream.="<td align=center>".$_SESSION['lang']['no']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['lokasi']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['gudang']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['nodok']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['tanggal']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['nospb']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['jenispemakaian']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['jenisitem']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['kodebarang']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['namabarang']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['satuan']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['jumlah']."</td>";
		$stream.="<td align=center>".$_SESSION['lang']['kodeorgpenerima']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$NoListData = 1;
		foreach($ListData as $LBKey => $LBVal){
			$stream.="<tr class=rowcontent>";
			$stream.="<td>".$NoListData."</td>";
			//$stream.="<td>".$_POST['lokasi']."</td>";
			$stream.="<td>".GetNamaLokasi($_POST['lokasi'])."</td>";
			//$stream.="<td>".$_POST['gudang']."</td>";
			$stream.="<td>".GetNamaLokasi($_POST['gudang'])."</td>";
			$stream.="<td>".$LBVal['nodokumen']."</td>";
			$stream.="<td>".$LBVal['tanggal']."</td>";
			$stream.="<td>".$LBVal['nospb']."</td>";
			$stream.="<td>".$LBVal['namajenispemakaian']."</td>";
			$stream.="<td>".$_POST['jenisitem']."</td>";
			$stream.="<td>".$LBVal['kodebarang']."</td>";
			$stream.="<td>".$LBVal['namabarang']."</td>";
			$stream.="<td>".$LBVal['satuan']."</td>";
			$stream.="<td>".$LBVal['jumlah']."</td>";
			$stream.="<td>".$LBVal['namakaryawan']."</td>";
			$stream.="</tr>";
			$NoListData++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;










	case'ShowDetail':
		$ListData = GetListNoTutupPaksaPR(null, null, null, $_POST['notutuppaksa']);
		$ListDetailData = GetListDetailNoTutupPaksaPR($_POST['notutuppaksa']);
		$stream = "";

		$stream.="<table border=0>";
		$stream.="<tr>";
		$stream.="<td>".$_SESSION['lang']['tgl_pr']."</td>";
		$stream.="<td>:</td>";
		$stream.="<td>".$ListData[0]['tanggal']."</td>";
		$stream.="<td>".$_SESSION['lang']['tanggaltutuppaksa']."</td>";
		$stream.="<td>:</td>";
		$stream.="<td>".$ListData[0]['created_date']."</td>";
		$stream.="</tr>";
		$stream.="<tr>";
		$stream.="<td>".$_SESSION['lang']['nopp']."</td>";
		$stream.="<td>:</td>";
		$stream.="<td>".$ListData[0]['no_transaksi']."</td>";
		$stream.="<td>".$_SESSION['lang']['notutuppaksa']."</td>";
		$stream.="<td>:</td>";
		$stream.="<td>".$ListData[0]['no_tutup_paksa']."</td>";
		$stream.="<td>".$_SESSION['lang']['dibuatoleh']."</td>";
		$stream.="<td>:</td>";
		$stream.="<td>".$ListData[0]['created_by']."</td>";
		$stream.="</tr>";

		$stream.="<table border=1>";
		$stream.="<thead><tr>";
		$stream.="<td>".$_SESSION['lang']['no']."</td>";
		$stream.="<td>".$_SESSION['lang']['namabarang']."</td>";
		$stream.="<td>".$_SESSION['lang']['kodebarang']."</td>";
		$stream.="<td>".$_SESSION['lang']['jmlhdisetujui']."</td>";
		$stream.="<td>".$_SESSION['lang']['sudahdibeli']."</td>";
		$stream.="<td>".$_SESSION['lang']['sudahditerimagudang']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$NoListData = 1;
		foreach($ListDetailData as $LBKey => $LBVal){
			$stream.="<tr class=rowcontent>";
			$stream.="<td>".$NoListData."</td>";
			$stream.="<td>".$LBVal['nama_barang']."</td>";
			$stream.="<td>".$LBVal['kodebarang']."</td>";
			$stream.="<td>".$LBVal['jmlh_disetujui']."</td>";
			$stream.="<td>".$LBVal['sudah_dibeli']."</td>";
			$stream.="<td>".$LBVal['sudah_diterima_gudang']."</td>";
			$stream.="</tr>";
			$NoListData++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;

	case 'CariNoPr':
		$List= GetNoPr($_POST['noprpopup']);
		#pre($ListBarang);
		$stream ="<table border=1>";
		$stream.="<thead><tr>";
		$stream.="<td>".$_SESSION['lang']['nopp']."</td>";
		$stream.="<td>".$_SESSION['lang']['tanggal']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$ListNo = 1;
		foreach($List as $LKey => $LVal){
			$stream.="<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=CariNoPrPick('".$ListNo."')>";
			$stream.="<td>".$LVal['nopp']."</td>";
			$stream.="<td>".$LVal['tanggal']."</td>";
			$stream.="<input type='hidden' id='list_".$ListNo."' value='".$LVal['nopp']."'>";
			$stream.="</tr>";
			$ListNo++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;
	case'CariNoTutupPaksaPR':
		$List= GetNoTutupPaksaPR($_POST['textsearch']);
		$stream ="<table border=1>";
		$stream.="<thead><tr>";
		$stream.="<td>".$_SESSION['lang']['notutuppaksa']."</td>";
		$stream.="<td>".$_SESSION['lang']['tipetransaksi']."</td>";
		$stream.="<td>".$_SESSION['lang']['notransaksi']."</td>";
		$stream.="<td>".$_SESSION['lang']['tanggal']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$ListNo = 1;
		foreach($List as $LKey => $LVal){
			$stream.="<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=SetNoTutupPaksaPR('".$ListNo."')>";
			$stream.="<td>".$LVal['no_tutup_paksa']."</td>";
			$stream.="<td>".$LVal['tipe_transaksi']."</td>";
			$stream.="<td>".$LVal['no_transaksi']."</td>";
			$stream.="<td>".$LVal['created_date']."</td>";
			$stream.="<input type='hidden' id='notutuppaksa_".$ListNo."' value='".$LVal['no_tutup_paksa']."'>";
			$stream.="</tr>";
			$ListNo++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;
	default:
	  $strx="select 1=1";
	break;
}
?>
