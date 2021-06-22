<?php
require_once('master_validation.php');
include('lib/eagrolib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
#showerror();
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['unitDt']==''?$unitDt=$_GET['unitDt']:$unitDt=$_POST['unitDt'];
$_POST['gudang']==''?$gudang=$_GET['gudang']:$gudang=$_POST['gudang'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['nostokopname'] == '' ? $StokOpname=$_GET['nostokopname'] : $StokOpname=$_POST['nostokopname'];

$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNmSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

function GetStokOpnameDt($Reffno, $kdbarang){
	global $dbname;

	$sStokOpnameDt = "SELECT qtyso FROM ".$dbname.".log_5stokopnamedt WHERE reffno = '".$Reffno."' and kdbarang = '".$kdbarang."';";
	$rStokOpnameDt = fetchData($sStokOpnameDt);

	return $rStokOpnameDt;
}

function GetIndukByKodeOrg2($KodeOrg){


	$ssql="SELECT induk from organisasi WHERE kodeorganisasi='".$KodeOrg."';";
	return $return;
}

function GetNamaOrgByUnit($kdunit){
	global $dbname;
	$Induk = GetIndukByKodeOrg2($kdunit);
	#return $Induk;exit();
	$String = "SELECT namaorganisasi FROM ".$dbname.".organisasi WHERE kodeorganisasi = '".$Induk[0]['induk']."';";
	$Result = fetchData($String);

	return $Result[0]['namaorganisasi'];
}

## GetNamaOrg ##
function GetNamaOrg3($kodeorg){
	global $dbname;

	#if($kodeorg == ''){
		$WKdorg = "kodeorganisasi ='".$kodeorg."'";
	#} else {
	#	$String2 = "SELECT induk FROM organisasi WHERE kodeorganisasi='".$kodeorg."'";
	#	$Result2 = fetchData($String2);
	#	$WKdorg = "kodeorganisasi ='".$Result2[0]['induk']."'";
	#}
	$String = "SELECT namaorganisasi FROM organisasi
	WHERE ".$WKdorg."";

	$Result = fetchData($String);
	return $Result[0]['namaorganisasi'];
}

if($proses!='getGudang' && $proses!='getPeriodeGudang') {
    if($proses == 'excel') {

		## GET HEADER ##
		$sDataHt = "SELECT * FROM ".$dbname.".log_5stokopnameht WHERE nostokopname = '".$StokOpname."';";
		$qDataHt = mysql_query($sDataHt) or die(mysql_error());
		$rDataHt = mysql_fetch_array($qDataHt);
		if(empty($rDataHt)) {
			echo "Tidak ada data";
			exit();
		}
		#echo $rDataHt['tanggal'];exit();
		## GET DETAILS ##
		$sDataDt = "SELECT * FROM ".$dbname.".log_5stokopnamedt WHERE reffno = '".$rDataHt['reffno']."' ORDER BY seqno;";
		$qDataDt = mysql_query($sDataDt) or die(mysql_error());

		## SET EXCEL OUTPUT ##
		## Title ##
		$stream ="<table>
		<tr><td colspan=8 align=center style='font-size:24px;'><b>TRANSAKSI STOK OPNAME</b></td></tr>
		<tr><td colspan=8 align=center style='font-size:17px;'><b>".GetNamaOrgByUnit($rDataHt['kdunit'])."</b></td></tr>
		</table><br>";
		## Header ##
		$stream.="
			<table>
			<tr>
				<td colspan=2>No Stok Opname</td>
				<td>".$rDataHt['nostokopname']."</td>
				<td></td>
				<td></td>
				<td>".$_SESSION['lang']['tanggal']."</td>
				<td>".$rDataHt['tanggal']."&nbsp; </td>
			</tr>
			<tr>
				<td colspan=2>No Referensi</td>
				<td>".$rDataHt['reffno']."</td>
				<td></td>
				<td></td>
				<td>".$_SESSION['lang']['periode']."</td>
				<td>".substr($rDataHt['periode'],0,7)."</td>
			</tr>
			<tr>
				<td colspan=2>Unit</td>
				<td>".GetNamaOrg3($rDataHt['kdunit'])."</td>
				<td></td>
				<td></td>
				<td>Nama Gudang</td>
				<td>".GetNamaOrg3($rDataHt['kdgudang'])."</td>
			</tr>
			<tr>
				<td colspan=2>".$_SESSION['lang']['note']."</td>
				<td colspan=5>".$rDataHt['note']."</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			<tr>
			</table>
		";
		## Details ##
		$stream.="
			<table border=1>
			<tr>
				<td bgcolor=#DEDEDE align=center >No.</td>
				<td bgcolor=#DEDEDE align=center >No Referensi</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['kdbarang']."</td>
				<td bgcolor=#DEDEDE align=center >Nama Barang".$_SESSION['nmbarang']."</td>
				<td bgcolor=#DEDEDE align=center >Satuan".$_SESSION['kdsatuan']."</td>
				<td bgcolor=#DEDEDE align=center >Stok System".$_SESSION['qtysaldo']."</td>
				<td bgcolor=#DEDEDE align=center >Stock Opname".$_SESSION['qtyso']."</td>
				<td bgcolor=#DEDEDE align=center >Selisih".$_SESSION['qtybalance']."</td>
			</tr>";

		$rowdt=mysql_num_rows($qDataDt);
		if($rowdt>0){
			while($res=mysql_fetch_assoc($qDataDt)){
				$no+=1;
				$stream.="
					<tr>
						<td align=center>".$no."</td>
						<td>".$res['reffno']."</td>
						<td>".$res['kdbarang']."</td>
						<td>".$res['nmbarang']."</td>
						<td>".$res['kdsatuan']."</td>
						<td>".$res['qtysaldo']."</td>
						<td>".$res['qtyso']."</td>
						<td>".$res['qtybalance']."</td>
					</tr>";
			}
		}
		else{$stream.="<tr><td colpsan=7>Not Found</td></tr>";}
		$stream.="</table>";
		$nop_="Laporan SO_".$rDataHt['nostokopname'].' '.date('hms');
	}
	else if($proses == 'pdf'){
		## GET HEADER ##
		$sDataHt = "SELECT * FROM ".$dbname.".log_5stokopnameht WHERE nostokopname = '".$StokOpname."';";
		$qDataHt = mysql_query($sDataHt) or die(mysql_error());
		$rDataHt = mysql_fetch_array($qDataHt);
		#pre($rDataHt);exit();
		if(empty($rDataHt)) {
			echo "Tidak ada data";
			exit();
		}else{
			$_SESSION['rDataHt'] = $rDataHt;
		}

		## GET DETAILS ##
		$sDataDt = "SELECT * FROM ".$dbname.".log_5stokopnamedt WHERE reffno = '".$rDataHt['reffno']."' ORDER BY seqno;";
		$qDataDt = mysql_query($sDataDt) or die(mysql_error());

	}
	else {
		if($unitDt==''){exit("Error:Unit Tidak Boleh Kosong");}
	    if($gudang!=''){$where.="and kodegudang like '".$gudang."%'";}
		if($periode!=''){$where.=" and periode='".$periode."'";}

		$sStokOpnameHt = "SELECT reffno FROM ".$dbname.".log_5stokopnameht WHERE kdunit = '".$unitDt."' and kdgudang = '".$gudang."' and periode like '".$periode."%' and status = 3 order by id desc;";
		$rStokOpnameHt = fetchData($sStokOpnameHt);
		$Reffno = isset($rStokOpnameHt[0]['reffno']) ? $rStokOpnameHt[0]['reffno'] : '0';

		$sData="SELECT DISTINCT
			DISTINCT sum(a.saldoawalqty) AS saldoawalqty,
			sum(a.qtymasuk) AS qtymasuk,
			sum(a.qtykeluar) AS qtykeluar,
			sum(a.saldoakhirqty) AS saldoakhirqty,
			a.periode,
			a.kodebarang,
			b.namabarang
		FROM
			log_5saldobulanan a
			LEFT JOIN log_5masterbarang b ON a.kodebarang = b.kodebarang
		WHERE
			kodegudang != ''
		AND kodegudang LIKE '".$gudang."%'
		AND periode = '".$periode."'
		GROUP BY
			kodebarang,
			LEFT (kodegudang, 4);";
		#echo $sData;
		$qData=mysql_query($sData) or die(mysql_error($conn));
		while($rData=mysql_fetch_assoc($qData)){
			$dtPeriode[$rData['periode']]=$rData['periode'];
			$lstKdBrg[$rData['kodebarang']]=$rData['kodebarang'];
			$dtKdBarang[$rData['periode']][$rData['kodebarang']]=$rData['kodebarang'];
			$dtAwal[$rData['periode'].$rData['kodebarang']]=$rData['saldoawalqty'];
			$dtNilAwal[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoawal'];
			$dtHrgAwal[$rData['periode'].$rData['kodebarang']]=$rData['hargaratasaldoawal'];

			$dtMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasuk'];
			$dtNilMasuk[$rData['periode'].$rData['kodebarang']]=$rData['qtymasukxharga'];
			@$dtHrgMasuk[$rData['periode'].$rData['kodebarang']]=$dtNilMasuk[$rData['periode'].$rData['kodebarang']]/$dtMasuk[$rData['periode'].$rData['kodebarang']];

			$dtKeluar[$rData['periode'].$rData['kodebarang']]=$rData['qtykeluar'];
			$dtNilKeluar[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
			@$dtHrgKeluar[$rData['periode'].$rData['kodebarang']]=$dtNilKeluar[$rData['periode'].$rData['kodebarang']]/$dtKeluar[$rData['periode'].$rData['kodebarang']];

			#$DataSO = GetStokOpnameDt($Reffno, $rData['kodebarang']);
			#if(isset($DataSO[0]['qtyso']) || !empty($DataSO[0]['qtyso'])){
			#	$dtAkhir[$rData['periode'].$rData['kodebarang']]=$DataSO[0]['qtyso'];
			#} else {
				$dtAkhir[$rData['periode'].$rData['kodebarang']]=$rData['saldoakhirqty'];
			#}



			$dtNilAkhir[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoakhir'];
			@$dtHrgAkhir[$rData['periode'].$rData['kodebarang']]=$rData['nilaisaldoakhir'];
		}

		$chekDt=count($dtPeriode);
		if($chekDt==0){exit("Error:Data Kosong");}

		foreach($dtPeriode as $dtIsi)
		{
			foreach($lstKdBrg as $dtBrg)
			{
				if($dtKdBarang[$dtIsi][$dtBrg]!='')
				{
				$no+=1;
				$tglSkrg=date('Y-m-d H:i:s');
				#$tab.="<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarang2(event,'".$unitDt."','".$dtIsi."','".$gudang."','".$dtKdBarang[$dtIsi][$dtBrg]."','".$optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]]."','".$optNmSat[$dtKdBarang[$dtIsi][$dtBrg]]."');\">";
				$tab.="<tr class=rowcontent>";
				$tab.="<td><input type='hidden' id='seqno".$no."' name='seqno".$no."' value='".$no."'>".$no."</td>";
				$tab.="<td>".GetNamaOrg3($unitDt)."</td>";
				$tab.="<td>".GetNamaOrg3($gudang)."</td>";
				$tab.="<td>".$dtIsi."</td>";
				$tab.="<td><input type='hidden' id='kdbarang".$no."' name='kdbarang".$no."' value='".$dtKdBarang[$dtIsi][$dtBrg]."'>".$dtKdBarang[$dtIsi][$dtBrg]."</td>";
				$tab.="<td><input type='hidden' id='nmbarang".$no."' name='nmbarang".$no."' value='".$optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]]."'>".$optNmBrg[$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
				$tab.="<td><input type='hidden' id='kdsatuan".$no."' name='kdsatuan".$no."' value='".$optNmSat[$dtKdBarang[$dtIsi][$dtBrg]]."'>".$optNmSat[$dtKdBarang[$dtIsi][$dtBrg]]."</td>";
				$tab.="<td><input type='hidden' id='qtysaldo".$no."' name='qtysaldo".$no."' value='".$dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."'>".$dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."</td>";//saldo akhir
				#$tab.="<td align=right>".number_format($dtAwal[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]],2)."</td>";//saldo awal
				$tab.="<td><input type='number' oninput='CalculateBalance(".$no.")' onkeyup='CalculateBalance(".$no.")' name='unitso".$no."' id='unitso".$no."' value='".$dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."'></td>";//SO
				$tab.="<td align=right width='100'><span name='balance".$no."' id='balance".$no."' value='0'>0</span></td>";//saldo keluar
				$tab.="<input type='hidden' id='saldo".$no."' name='saldo".$no."' value='".$dtAkhir[$dtIsi.$dtKdBarang[$dtIsi][$dtBrg]]."'>";
				$tab.="<input type='hidden' id='qtybalanced".$no."' name='qtybalanced".$no."' value='0'>";
				$tab.="</tr>";
				}
			}
		}
		$tab.="<tr><td colspan=10><button id='buttonsave' class=mybutton onclick=SaveData()>Save Data</button></td></tr>";
		$tab.="<input type='hidden' id='TotalData' name='TotalData' value='".$no."'>";
	}

}
switch($proses)
{
    case'getGudang':
		//$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$optUnit="<option value=''>--</option>";
		$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
				kodeorganisasi like '".$unitDt."%' and tipe like 'GUDANG%' order by namaorganisasi asc";
		$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
		while($rUnit=mysql_fetch_assoc($qUnit))
		{
			$optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
		}
		echo $optUnit;
		break;
	case'getPeriodeGudang':
		$optPeriode="<option value=''>".$_SESSION['lang']['pilihselect']."</option>";
		$sPeriodeAkuntansi = "SELECT periode FROM ".$dbname.".setup_periodeakuntansi WHERE tutupbuku = 0 and kodeorg = '".$gudang."'";
		$qPeriodeAkuntansi = fetchData($sPeriodeAkuntansi);
		$optPeriode.="<option value='".$qPeriodeAkuntansi[0]['periode']."'>".$qPeriodeAkuntansi[0]['periode']."</option>";
		echo $optPeriode;
		break;
    case'preview':
		echo $tab;
		break;
	case'excel':
		if(strlen($stream)>0) {
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {@unlink('tempExcel/'.$file);}
				}
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream)){
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			} else {
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}
		break;
	case'pdf':
		class PDF extends FPDF {
			function Header() {
		       	## Title ##
		        $this->SetFont('Arial','B',12);
				$this->Cell(190,5,strtoupper($_SESSION['lang']['titlestokopname']),0,1,'C');

				## Header
		        $this->SetFont('Arial','',8);
				$this->Cell(30,5,$_SESSION['lang']['nostokopname'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(110,5,$_SESSION['rDataHt']['nostokopname'],0,0,'L');
				$this->Cell(20,5,$_SESSION['lang']['tanggal'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(15,5,$_SESSION['rDataHt']['tanggal'],0,1,'L');

				$this->Cell(30,5,$_SESSION['lang']['reffno'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(110,5,$_SESSION['rDataHt']['reffno'],0,0,'L');
				$this->Cell(20,5,$_SESSION['lang']['periode'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(15,5,$_SESSION['rDataHt']['periode'],0,1,'L');

				$this->Cell(30,5,$_SESSION['lang']['kdunit'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(110,5,$_SESSION['rDataHt']['kdunit'],0,0,'L');
				$this->Cell(20,5,$_SESSION['lang']['kdgudang'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(15,5,$_SESSION['rDataHt']['kdgudang'],0,1,'L');

				$this->Cell(30,5,$_SESSION['lang']['note'],'0',0,'L');
				$this->Cell(2,5,':','0',0,'L');
				$this->Cell(110,5,$_SESSION['rDataHt']['note'],0,1,'L');

		        $this->SetFont('Arial','',8);
				$this->Cell(5,4,'No.',1,0,'C');
				$this->Cell(35,4,'Kode Referensi',1,0,'C');
				$this->Cell(20,4,'Kode Barang',1,0,'C');
				$this->Cell(60,4,'Nama Barang',1,0,'C');
				$this->Cell(14,4,'Satuan',1,0,'C');
				$this->Cell(19,4,'Stok Sistem',1,0,'C');
				$this->Cell(19,4,'Stok Opname',1,0,'C');
				$this->Cell(11,4,'Selisih',1,1,'C');
		    }
		}
		$pdf=new PDF('P','mm','A4');
		$pdf->AddPage();
		$rowdt=mysql_num_rows($qDataDt);
		if($rowdt>0){
			while($res=mysql_fetch_assoc($qDataDt)){
				$no+=1;
	            $pdf->Cell(5,4,$no,0,0,'C');
	            $pdf->Cell(35,4,$res['reffno'],0,0,'C');
	            $pdf->Cell(20,4,$res['kdbarang'],0,0,'C');
	            $pdf->Cell(60,4,$res['nmbarang'],0,0,'C');
	            $pdf->Cell(14,4,$res['kdsatuan'],0,0,'C');
	            $pdf->Cell(19,4,$res['qtysaldo'],0,0,'C');
							$pdf->Cell(19,4,$res['qtyso'],0,0,'C');
	            $pdf->Cell(11,4,$res['qtybalance'],0,1,'C');
			}
		}
		$pdf->Output();
   		break;
    default:
    break;
}
?>
