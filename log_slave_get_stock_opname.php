<?
require_once('master_validation.php');
require_once('lib/eagrolib.php');
require_once('lib/zLib.php');

$method = isset($_POST['method']) ? $_POST['method'] : null;

function GetListStockOpname($lokasi, $gudang, $periode, $nostokopname){
	global $dbname;
	
	if ($nostokopname == '') {
		$Wherenoso = '';
	} else {
		$Wherenoso = "AND nostokopname like '%".$nostokopname."%'";
	}
	
	$String = "SELECT *	FROM log_5stokopnameht WHERE kdunit = '".$lokasi."'
	AND kdgudang = '".$gudang."'
	AND periode like '".$periode."%'
	".$Wherenoso."
	";
	#pre($String);exit();
	$Result = fetchData($String);
	return $Result;
}
function GetStatusHeaderOpt($Status){
	global $dbname;

	$String = "SELECT *	FROM ".$dbname.".setup_5parameter WHERE flag = 'status_st' and kode = '".$Status."' order by nourut";

	$Result = fetchData($String);
	#pre($Result);
	return $Result[0]['nama'];
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
	case 'LoadData':		
		$ListData = GetListStockOpname($_POST['lokasi'], $_POST['gudang'], $_POST['periode'], $_POST['nostokopname']);
		#pre($ListData);
		$stream ="<table border=1 id='ListData'>";
		$stream.="<thead><tr>";
		$stream.="<td>".$_SESSION['lang']['no']."</td>";
		$stream.="<td>No Referensi</td>";
		$stream.="<td>No Stok Opname</td>";
		$stream.="<td>".$_SESSION['lang']['tanggal']."</td>";
		$stream.="<td>".$_SESSION['lang']['status']."</td>";
		$stream.="<td>".$_SESSION['lang']['print']."</td>";
		$stream.="</tr>";
		$stream.="</thead><tbody>";
		$NoListData = 1;
		foreach($ListData as $LBKey => $LBVal){
			$stream.="<tr class=rowcontent>";
			$stream.="<td>".$NoListData."</td>";
			$stream.="<td>".$LBVal['reffno']."</td>";
			$stream.="<td>".$LBVal['nostokopname']."</td>";
			$stream.="<td>".$LBVal['tanggal']."</td>";
			$stream.= "<td>".GetStatusHeaderOpt($LBVal['status'])."</td>";
			$stream.= "<td>
			<!--<button onclick=\"masterPDF('log_stock_opname','".$LBVal['id']."','','log_slave_print_stock_opname',event);\">Cetak</button>-->
			<button onclick=\"DownloadPDF('".$LBVal['id']."',event);\">Download</button>
			</td>";
			$stream.="</tr>";
			$NoListData++;
		}
		$stream.="</tbody></table>";
		echo $stream;
	break;
	case'GetNoSO':
		$noso	 = isset($_POST['noso']) ? $_POST['noso'] : '';
		$unitDt	 = isset($_POST['unitDt']) ? $_POST['unitDt'] : '';
		$gudang	 = isset($_POST['gudang']) ? $_POST['gudang'] : '';
		$periode = isset($_POST['periode']) ? $_POST['periode'] : '';
		
		echo"<table cellspacing=1 border=0 class=data>
			<thead>
			<tr class=rowheader><td>No</td>
				<td>No Stok Opname</td>
				<td>No Referensi</td>
				<td>".$_SESSION['lang']['tanggal']."</td>
				<td>".$_SESSION['lang']['note']."</td>
				<td>Unit</td>
				<td>Gudang</td>
				<td>".$_SESSION['lang']['periode']."</td>
			</tr>
			</thead>
			</tbody>";
		$str = "SELECT * FROM ".$dbname.".log_5stokopnameht
		WHERE nostokopname LIKE '%".$noso."%'
		AND kdunit LIKE '%".$unitDt."%'
		AND kdgudang LIKE '%".$gudang."%'
		AND periode LIKE '%".$periode."%'
		ORDER BY id desc";
	  $res=mysql_query($str);
	  $no=0;
	  while($bar=mysql_fetch_object($res)){
		$no+=1;
		echo"
			<tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=\"goPickSo('".$bar->nostokopname."')\"><td>".$no."</td>
				<td>".$bar->nostokopname."</td>
				<td>".$bar->reffno."</td>
				<td>".tanggalnormal($bar->tanggal)."</td>
				<td>".$bar->note."</td>
				<td>".$bar->kdunit."</td>
				<td>".$bar->kdgudang."</td>
				<td>".$bar->periode."</td>
			</tr>";	
	   }	 		
		echo"</tbody>
			 <tfoot>
			 </tfoot>
			 </table>";		
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
		echo $OptGudang;
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
