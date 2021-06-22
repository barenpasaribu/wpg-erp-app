<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';

$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$ql = 'select a.catatan,a.kodeorg,a.nopp,a.tanggal,a.dibuat from ' . $dbname . '.`log_prapoht` a where a.nopp=\'' . $column . '\'';

#exit(mysql_error());
$pq = mysql_query($ql);
$hsl = mysql_fetch_assoc($pq);
$kdr = $hsl['kodeorg'];
$sNmKry = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $hsl['dibuat'] . '\'';
$periode = substr($hsl['tanggal'],0,7);

#exit(mysql_error());
$qNmKry = mysql_query($sNmKry);
$rNmKry = mysql_fetch_assoc($qNmKry);
$dibuat = $rNmKry['namakaryawan'];
$sNmkntr = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kdr . '\'';

#exit(mysql_error());
$qNmkntr = mysql_query($sNmkntr);
$rNmkntr = mysql_fetch_assoc($qNmkntr);
$sNmkntr2 = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . substr($hsl['nopp'], -4, 4) . '\'';

#exit(mysql_error());
class masterpdf extends FPDF
{
	public function Header()
	{
		global $table;
		global $header;
		global $column;
		global $dbname;
		global $tgl;
		global $nmKntr;
		global $dibuat;
		global $kdr;
		global $nmunit;
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;
/*
		if ($kdr == 'SSP') {
			$path = 'images/SSP_logo.jpg';
		}
		else if ($kdr == 'MJR') {
			$path = 'images/MI_logo.jpg';
		}
		else if ($kdr == 'HSS') {
			$path = 'images/HS_logo.jpg';
		}
		else if ($kdr == 'BNM') {
			$path = 'images/BM_logo.jpg';
		}
		else {
			$path = 'images/MIG_logo.jpg';
		}
*/
		$path = 'images/SSP_logo.jpg'; // Logo Wilian Perkasa Group
		$a = $this->Image($path, 20, 10, 120, 65, 'jpg', '');
		$this->Cell(120, $height, $a, ' ', 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell((40 / 100) * $width, $height, $nmKntr, '', 0, 'L');
		$this->Cell((40 / 100) * $width, $height, 'TO :', '', 1, 'L');
		$this->Cell(120, $height, ' ', '', 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell((12 / 100) * $width, $height, $_SESSION['lang']['unit'], '', 0, 'L');
		$this->Cell((2 / 100) * $width, $height, ':', '', 0, 'L');
		$this->Cell((1 / 100) * $width, $height, $nmunit, '', 0, 'L');
		$this->Cell((25 / 100) * $width, $height, ' ', '', 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell((12 / 100) * $width, $height, 'PURCHASING DEPARTEMENT', '', 0, 'L');
		$this->Cell((2 / 100) * $width, $height, '', '', 0, 'L');
		$this->Cell((1 / 100) * $width, $height, '', '', 1, 'L');
		$this->Cell(120, $height, ' ', '', 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell((12 / 100) * $width, $height, 'PP NO', '', 0, 'L');
		$this->Cell((2 / 100) * $width, $height, ':', '', 0, 'L');
		$this->Cell((1 / 100) * $width, $height, $column, '', 0, 'L');
		$this->Cell((25 / 100) * $width, $height, ' ', '', 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell((14 / 100) * $width, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell((2 / 100) * $width, $height, ':', '', 0, 'L');
		$this->Cell((1 / 100) * $width, $height, $tgl, '', 1, 'L');
		$this->Ln();
	}
}

$qNmkntr2 = mysql_query($sNmkntr2);
$rNmkntr2 = mysql_fetch_assoc($qNmkntr2);
$nmKntr = $rNmkntr['namaorganisasi'];
$nmunit = $rNmkntr2['namaorganisasi'];
$tgl = tanggalnormal($hsl['tanggal']);
//$query = 'select a.*,b.*,c.namabarang,c.satuan,d.spesifikasi from ' . $dbname . '.' . $table . ' a inner join ' . $dbname . '.`log_prapodt` b on a.nopp=b.nopp inner join ' . $dbname . '.`log_5masterbarang` c on b.kodebarang=c.kodebarang  left join ' . $dbname . '.`log_5photobarang` d on c.kodebarang=d.kodebarang where a.nopp=\'' . $column . '\'';
//$query = 'select a.*,b.*,c.namabarang,c.satuan,d.spesifikasi,e.jml_approve as jml_approve_e from ' . $dbname . '.' . $table . ' a inner join ' . $dbname . '.`log_prapodt` b on a.nopp=b.nopp inner join ' . $dbname . '.`log_5masterbarang` c on b.kodebarang=c.kodebarang  left join ' . $dbname . '.`log_5photobarang` d on c.kodebarang=d.kodebarang left join ' . $dbname . '.log_prapodt_vw as e on (b.nopp=e.nopp and b.kodebarang=e.kodebarang) where a.nopp=\'' . $column . '\'';
$query = 'select a.*,b.*,c.namabarang,c.satuan,d.spesifikasi,
case when e.jml_approve is null and b.jml_approve =\'0\' then b.jumlah 
		when e.jml_approve is null and b.jml_approve > \'0\'  then b.jml_approve
	else b.jml_approve end as jml_approve_e from ' . $dbname . '.' . $table . ' a inner join ' . $dbname . '.`log_prapodt` b on a.nopp=b.nopp inner join ' . $dbname . '.`log_5masterbarang` c on b.kodebarang=c.kodebarang  left join ' . $dbname . '.`log_5photobarang` d on c.kodebarang=d.kodebarang left join ' . $dbname . '.log_prapodt_vw as e on (b.nopp=e.nopp and b.kodebarang=e.kodebarang) where a.nopp=\'' . $column . '\'';
#echo $query;

$result = fetchData($query);
$pdf = new masterpdf('P', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 11;
$pdf->SetFont('Arial', 'B', 8);
$pdf->AddPage();
$pdf->Cell(20, 1.5 * $height, 'No.', 1, 0, 'C');
$pdf->Cell(60, 1.5 * $height, $_SESSION['lang']['kodebarang'], 1, 0, 'L');
$pdf->Cell(165, 1.5 * $height, $_SESSION['lang']['namabarang'], 1, 0, 'C');
$pdf->Cell(35, 1.5 * $height, $_SESSION['lang']['jumlah'], 1, 0, 'L');
$pdf->Cell(35, 1.5 * $height, $_SESSION['lang']['stok'], 1, 0, 'C');
$pdf->Cell(35, 1.5 * $height, $_SESSION['lang']['satuan'], 1, 0, 'C');
$pdf->Cell(40, 1.5 * $height, 'Required', 1, 0, 'C');
$pdf->Cell(120, 1.5 * $height, $_SESSION['lang']['keterangan'], 1, 0, 'C');
$pdf->Cell(50, 1.5 * $height, 'Status', 1, 0, 'C');
$pdf->Ln();
$no = 0;
$stok[] = array();

$x = 'select sum(saldoqty) as saldoqty,kodebarang from ' . $dbname . '.log_5masterbarangdt where ' . "\r\n\t\t\t" . 'kodegudang in (select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['empl']['kodeorganisasi'] . '\') group by kodebarang';
#exit(mysql_error($conn));
$y = mysql_query($x);

while ($z = mysql_fetch_assoc($y)) {
	$kodebarang = $z['kodebarang'];
	$stok[$kodebarang] = $z['saldoqty'];
}

foreach ($result as $data) {
	$pdf->SetFont('Arial', '', 7);
	$no += 1;
	if ($no != 1) {
		$pdf->SetY($akhirY);
	}

	$height2 = $height;
	if ((40 < strlen(trim($data['namabarang']))) || (40 < strlen(trim($data['keterangan'])))) {
		$height2 = $height * 2;
	}

	if ($data['status'] == 3) {
		$Status[$no] = $_SESSION['lang']['ditolak']." (".$data['alasanstatus'].")";
	}
	else {
		/*
		if ($data['hasilpersetujuan' . $no] == 1) {
			$Status[$no] = $_SESSION['lang']['disetujui'];
		}
		else if ($data['hasilpersetujuan' . $no] == 3) {
			$Status[$no] = $_SESSION['lang']['ditolak'];
		}
		else {
			if (($data['hasilpersetujuan' . $no] == '') || ($data['hasilpersetujuan' . $no] == 0)) {
				$Status[$no] = 'Menunggu';
			}
		}
		*/
		if( $data['jumlahpemberipersetujuan'] > 0 ){
			for($i=1;$i<=$data['jumlahpemberipersetujuan'];$i++){
				if( $data['hasilpersetujuan' . $i] == 1 ){
					$Status[$no] = $_SESSION['lang']['disetujui'];
				}elseif( $data['hasilpersetujuan' . $i] == 3 ){
					$Status[$no] = $_SESSION['lang']['ditolak']." (".$data['alasanstatus'].")"; 
					break;
				}else{
					$Status[$no] = 'Menunggu';
					break;
				}
			}
		}
	}

	$posisiY = $pdf->GetY();

	$pdf->Cell(20, $height2, $no, 1, 0, 'L');

	$pdf->Cell(60, $height2, $data['kodebarang'], 1, 0, 'L');

	$pdf->MultiCell(165, $height2, $data['namabarang'], 1, 'J', 0);

	$akhirY = $pdf->GetY();

	$pdf->SetY($posisiY);

	$pdf->SetX($pdf->GetX() + 245);

	//$pdf->Cell(35, $height2, number_format($data['jumlah'], 2), 1, 0, 'C');
	$pdf->Cell(35, $height2, number_format($data['jml_approve_e'], 2), 1, 0, 'C');

	$x = '	select sum(saldoqty) as saldoqty,kodebarang from ' . $dbname . '.log_5masterbarangdt 
			where kodebarang=\'' . $data['kodebarang'] . '\' and' . "
			" . 'kodegudang in (select kodeorganisasi from ' . $dbname . '.organisasi where induk in' . "
			" . '(select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['empl']['kodeorganisasi'] . '\')) 
			group by kodebarang';
	#exit(mysql_error($conn));
	($y = mysql_query($x)) ;
	$z = mysql_fetch_assoc($y);
	
	// cek Stok barang
	$queryCekStokBarang = "	SELECT SUM(saldoakhirqty) AS saldoqty FROM log_5saldobulanan 
							WHERE 
							kodebarang='".$data['kodebarang']."' 
							AND kodeorg='".$_SESSION['empl']['kodeorganisasi']."' and periode='".$periode."'";
	$queryAct = mysql_query($queryCekStokBarang);
	$hasilData = mysql_fetch_object($queryAct);
	
	$pdf->Cell(35, $height2, $hasilData->saldoqty, '1', 0, 'C');

	$pdf->Cell(35, $height2, $data['satuan'], '1', 0, 'C');

	$pdf->Cell(40, $height2, tanggalnormal($data['tgl_sdt']), '1', 0, 'L');

//	$pdf->SetFont('Arial', '', 6.5);

	$pdf->MultiCell(120, $height2, $data['keterangan'], 1, 1);

	$akhirY = $pdf->GetY();

	$pdf->SetY($posisiY);

	$pdf->SetX($pdf->GetX() + 510);

	$pdf->Cell(50, $height2, $Status[$no], 1, 0,'J', 0);

}



if($akhirY == NULL){

	$akhirY = 120;

}





$pdf->SetY($akhirY);

$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(120, $height, $_SESSION['lang']['dbuat_oleh'] . ':' . $dibuat, '', 0, L);

$pdf->Ln();

$pdf->Cell(40, $height, $_SESSION['lang']['catatan'] . ' :', '', 0, L);

$pdf->SetFont('Arial', '', 8);

$pdf->MultiCell(500, $height, $hsl['catatan'], 0, 'L', 0);

$pdf->SetFont('Arial', 'B', 8);

$pdf->Ln();

$pdf->Cell(120, $height, $_SESSION['lang']['approval_status'] . ':', '', 0, L);

$pdf->Ln();

$ko = 0;

$pdf->Cell(20, 1.5 * $height, 'No.', 1, 0, 'C');

$pdf->Cell(120, 1.5 * $height, $_SESSION['lang']['nama'], 1, 0, 'C');

$pdf->Cell(110, 1.5 * $height, $_SESSION['lang']['kodejabatan'], 1, 0, 'C');

$pdf->Cell(70, 1.5 * $height, $_SESSION['lang']['lokasitugas'], 1, 0, 'C');

$pdf->Cell(80, 1.5 * $height, $_SESSION['lang']['keputusan'], 1, 0, 'C');

$pdf->Cell(150, 1.5 * $height, $_SESSION['lang']['note'], 1, 0, 'C');

$pdf->Ln();

$sCek = 'select nopp from ' . $dbname . '.log_prapodt where nopp=\'' . $column . '\'';



#exit(mysql_error());

$qCek = mysql_query($sCek);

$rCek = mysql_num_rows($qCek);



if (0 < $rCek) {

	$qp = 'select * from ' . $dbname . '.`log_prapoht` where `nopp`=\'' . $column . '\'';

	$qyr = fetchData($qp);



	foreach ($qyr as $hsl) {

		$i = 1;



		while ($i < 6) {

			if ($hsl['hasilpersetujuan' . $i] == 1) {

				$b['status'] = $_SESSION['lang']['disetujui'];

			}

			else if ($hsl['hasilpersetujuan' . $i] == 3) {

				$b['status'] = $_SESSION['lang']['ditolak'];

			}

			else {

				if (($hsl['hasilpersetujuan' . $i] == '') || ($hsl['hasilpersetujuan' . $i] == 0)) {

					$b['status'] = $_SESSION['lang']['wait_approve'];

				}

			}



			if ($hsl['persetujuan' . $i] != 0) {

				$sql = 'select * from ' . $dbname . '.`datakaryawan` where `karyawanid`=\'' . $hsl['persetujuan' . $i] . '\'';

				$keterangan = $hsl['komentar' . $i];

				$tanggal = '';



				if ($hsl['tglp' . $i] != '') {

					$tanggal = tanggalnormal($hsl['tglp' . $i]);

				}



				#exit(mysql_error());

				$query = mysql_query($sql);

				$res3 = mysql_fetch_object($query);

				$sql2 = 'select * from ' . $dbname . '.`sdm_5jabatan` where kodejabatan=\'' . $res3->kodejabatan . '\'';



				#exit(mysql_error());

				($query2 = mysql_query($sql2)) ;

				$res2 = mysql_fetch_object($query2);

				$height3 = $height;



				if (40 < strlen($keterangan)) {

					$height3 = $height * 2;

				}



				$pdf->SetFont('Arial', '', 7);

				$pdf->Cell(20, 1.5 * $height3, $i, 1, 0, 'C');

				$pdf->Cell(120, 1.5 * $height3, $res3->namakaryawan . '(' . $tanggal . ') ', 1, 0, 'L');

				$pdf->SetFont('Arial', '', 5.5);

				$pdf->Cell(110, 1.5 * $height3, $res2->namajabatan, 1, 'L');

				$pdf->SetFont('Arial', '', 7);

				$pdf->Cell(70, 1.5 * $height3, $res3->lokasitugas, 1, 0, 'L');

				$pdf->Cell(80, 1.5 * $height3, $b['status'], 1, 0, 'L');

				$pdf->MultiCell(150, 1.5 * $height, $keterangan, 1, 'J', 0);

				$pdf->Ln();

			}

			else {

				break;

			}



			++$i;

		}

	}

}

else {

	$pdf->SetFont('Arial', '', 7);

	$pdf->Cell(520, 1.5 * $height, 'Not Found', 1, 0, 'C');

}



$pdf->Cell(15, $height, 'Page ' . $pdf->PageNo(), '', 1, 'L');

$pdf->Output();



?>

