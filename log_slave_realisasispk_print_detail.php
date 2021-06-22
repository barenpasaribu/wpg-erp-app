<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';

$proses = $_GET['proses'];
$param = $_GET;

$col1 = 'kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp';

$cols = array();
$cols = explode(',', $col1);
$cols[0] = 'subunit';
$cols[1] = 'kegiatan';

$where = 'notransaksi=\'' . $param['notransaksi'] . '\'';
$query = selectQuery($dbname, 'log_spkdt', $col1, $where);
$data = fetchData($query);

$align = explode(',', 'L,L,R,R,L,R');
$length = explode(',', '22,30,8,10,10,20');
if (empty($data)) {
	echo 'Data Kosong';
	exit();
}

$whereOrg = 'kodeorganisasi in (';
$whereKeg = 'kodekegiatan in (';
foreach ($data as $key => $row) {
	if ($key == 0) {
		$whereOrg .= '\'' . $row['kodeblok'] . '\'';
		$whereKeg .= '\'' . $row['kodekegiatan'] . '\'';
	}
	else {
		$whereOrg .= ',\'' . $row['kodeblok'] . '\'';
		$whereKeg .= ',\'' . $row['kodekegiatan'] . '\'';
	}
}
$whereOrg .= ',\'' . $param['kodeorg'] . '\')';
$whereKeg .= ')';

// FA 20190424 ----------------------
//echo "warning: ".$whereOrg." / ".$whereKeg;
//exit();

$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '0', true);
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $whereKeg, '0', true);
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'supplierid=\'' . $param['koderekanan'] . '\'');
$optProj = makeOption($dbname, 'project', 'kode,nama');
$optProjker = makeOption($dbname, 'project_dt', 'kegiatan,namakegiatan');
$dataShow = $data;
foreach ($data as $key => $row) {
	$dataShow[$key]['kodeblok'] = $optOrg[$row['kodeblok']];
	$dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
	$dataShow[$key]['hk'] = number_format($row['hk'], 2, '.', ',');
	$dataShow[$key]['hasilkerjajumlah'] = number_format($row['hasilkerjajumlah'], 2, '.', ',');
	$dataShow[$key]['jumlahrp'] = number_format($row['jumlahrp'], 2, '.', ',');
}


// Ini data realisasi
$col2 = 'tanggal,kodeblok,hkrealisasi,hasilkerjarealisasi,jumlahrealisasi,statusjurnal';
$cols2 = explode(',', $col2);
$cols2[1] = 'subunit';
$where2 = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodekegiatan in (';
foreach ($data as $key => $row) {
	if ($key == 0) {
		$where2 .= '\'' . $row['kodekegiatan'] . '\'';
	}
	else {
		$where2 .= ',\'' . $row['kodekegiatan'] . '\'';
	}
}
$where2 .= ')';
$query2 = selectQuery($dbname, 'log_baspk', $col2, $where2);
$data2 = fetchData($query2);
$align2 = explode(',', 'L,L,R,R,R');
$length2 = explode(',', '10,30,10,20,30');

if (empty($data2)) {
	echo 'Data Realisasi belum ada';
	exit();
}

$whereOrg2 = 'kodeorganisasi in (';

foreach ($data2 as $key => $row) {
	if ($key == 0) {
		$whereOrg2 .= '\'' . $row['kodeblok'] . '\'';
	}
	else {
		$whereOrg2 .= ',\'' . $row['kodeblok'] . '\'';
	}
}

$whereOrg2 .= ',\'' . $param['kodeorg'] . '\')';
$optOrg2 = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg2, '0', true);
$dataShow2 = $data2;

foreach ($dataShow2 as $key => $row) {
	$dataShow2[$key]['kodeblok'] = $optOrg2[$row['kodeblok']];
	$dataShow2[$key]['hkrealisasi'] = number_format($row['hkrealisasi'], 2, '.', ',');
	$dataShow2[$key]['hasilkerjarealisasi'] = number_format($row['hasilkerjarealisasi'], 2, '.', ',');
	$dataShow2[$key]['jumlahrealisasi'] = number_format($row['jumlahrealisasi'], 2, '.', ',');
	if($row['statusjurnal']=='1'){
		$status="POSTED";
	}else{
		$status="NOT POSTED";
	}
}


// $title = $_SESSION['lang']['spk'];
$title = "BA PELAKSANAAN PEKERJAAN";
$titleDetail = array('');

switch ($proses) {
case 'pdf':
	$pdf = new zPdfMaster('P', 'pt', 'A4');
	$pdf->_noThead = true;
	$pdf->setAttr1($title, $align, $length, array());
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(0.8*$width, $height, $_SESSION['lang']['notransaksi'] . ' : ' . $param['notransaksi'], 0, 0, 'L', 1);
	$pdf->Cell(0.2*$width, $height,  '' , 'LRT', 1, 'R', 1);
	$pdf->Cell(0.8*$width, $height, $_SESSION['lang']['kodeorg'] . ' : ' . $optOrg[$param['kodeorg']], 0, 0, 'L', 1);
	$pdf->Cell(0.2*$width, $height,  $status , 'LR', 1, 'C', 1);
	$pdf->Cell(0.8*$width, $height, $_SESSION['lang']['koderekanan'] . ' : ' . $optSupp[$param['koderekanan']], 0, 0, 'L', 1);
	$pdf->Cell(0.2*$width, $height,  '' , 'LRB', 1, 'R', 1);
	$pdf->Ln();
	$i = 0;

	$str= "SELECT d.hk, d.hasilkerjajumlah, d.satuan, d.jumlahrp, b.namaorganisasi as unit, c.namaorganisasi as subunit, namakegiatan, d.kodekegiatan, d.kodeblok FROM log_spkdt d inner join log_spkht a ON d.notransaksi=a.notransaksi INNER JOIN organisasi b ON a.kodeorg=b.kodeorganisasi inner join organisasi c on a.divisi=c.kodeorganisasi left join setup_kegiatan x ON d.kodekegiatan=x.kodekegiatan WHERE d.notransaksi='".$param['notransaksi']."'";  
	$query=mysql_query($str);


	$i = 0;
        while($data=mysql_fetch_array($query)){
   		$pdf->SetFont('Arial', 'B', 9);
       $pdf->SetFillColor(255, 255, 255);
		$pdf->Cell($width, $height, 'Rencana', 0, 1, 'L', 1);
		$pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);

		$align = explode(',', 'L,L,L,R,L,R');
		$length = explode(',', '16,16,27,10,12,7,12');

        $pdf->Cell($length[0] / 100 * $width, $height, 'Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Sub Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Kegiatan', 1, 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Hari Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Volume Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, 'Satuan', 1, 0, 'C', 1);
        $pdf->Cell($length[6] / 100 * $width, $height, 'Jumlah (Rp.)', 1, 0, 'C', 1);
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 8);
    
 
        $pdf->Cell($length[0] / 100 * $width, $height, $data['unit'], 1, 0, 'L', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, $data['subunit'], 1, 0, 'L', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, $data['namakegiatan'], 1, 0, 'L', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($data['hk']), 1, 0, 'R', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($data['hasilkerjajumlah']), 1, 0, 'R', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, $data['satuan'], 1, 0, 'L', 1);
        $pdf->Cell($length[6] / 100 * $width, $height, number_format($data['jumlahrp']), 1, 0, 'R', 1);
        $pdf->Ln();
	$str1= "SELECT tanggal,kodeblok,hkrealisasi,hasilkerjarealisasi,jumlahrealisasi FROM log_baspk WHERE notransaksi='".$param['notransaksi']."' and kodekegiatan='".$data['kodekegiatan']."' and blokspkdt like '%".$data['kodeblok']."%' ";  
	$query1=mysql_query($str1);

	$align = explode(',', 'L,L,L,R,L,R');
	$length = explode(',', '15,40,15,15,15');

	$i = 0;
		$pdf->Cell($width, $height, 'Realisasi', 0, 0, 'L', 1);
        $pdf->Ln();
        $pdf->SetFillColor(220, 220, 220);
   		$pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($length[0] / 100 * $width, $height, 'Tanggal', 1, 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Sub Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Realisasi HK', 1, 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Realisasi Hasil Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Jumlah (Rp.)', 1, 0, 'C', 1);
        $pdf->Ln();
        while($data1=mysql_fetch_array($query1)){


        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 8);
    
         $pdf->Cell($length[0] / 100 * $width, $height, $data1['tanggal'], 1, 0, 'L', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, $optOrg2[$data1['kodeblok']], 1, 0, 'L', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, number_format($data1['hkrealisasi']), 1, 0, 'R', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($data1['hasilkerjarealisasi']), 1, 0, 'R', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($data1['jumlahrealisasi']), 1, 0, 'R', 1);
        $pdf->Ln();
		
        $tothk+=$data1['hkrealisasi'];
        $tothasil+=$data1['hasilkerjarealisasi'];
        $totjumlahrp+=$data1['jumlahrealisasi'];

        }
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(($length[0]+$length[1]) / 100 * $width, $height, 'T O T A L', 1, 0, 'L', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, number_format($tothk), 1, 0, 'R', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($tothasil), 1, 0, 'R', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($totjumlahrp), 1, 0, 'R', 1);
$pdf->Ln();
$pdf->Ln();

        }

/*

	foreach ($cols as $column) {
		$pdf->Cell(($length[$i] / 100) * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
		++$i;
	}

	$pdf->Ln();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);

	foreach ($dataShow as $key => $row) {
		$i = 0;

		foreach ($row as $cont) {

			$pdf->Cell(($length[$i] / 100) * $width, $height, $cont, 1, 0, $align[$i], 1);
			++$i;
		}

		$pdf->Ln();
	}
	$pdf->Ln();
	$pdf->Cell($width, $height, 'Realisasi', 0, 0, 'L');
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell($width, $height, $titleDetail[0], 0, 1, 'L', 1);
	$pdf->SetFillColor(220, 220, 220);
	$i = 0;

	foreach ($cols2 as $column) {
		$pdf->Cell(($length2[$i] / 100) * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
		++$i;
	}

	$pdf->Ln();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);

	foreach ($dataShow2 as $key => $row) {
		$i = 0;

		foreach ($row as $cont) {
			$pdf->Cell(($length2[$i] / 100) * $width, $height, $cont, 1, 0, $align2[$i], 1);
			++$i;
		}

		$pdf->Ln();
	}
*/

	$pdf->Ln();

 $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(1 / 5 * $width, $height, 'Dibuat Oleh ('.date("d-m-Y").')', 'LTR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Pemborong','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Diperiksa','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Disetujui','TR',0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Dibayar','TR', 0, 'C', 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, $height, '', 'LBR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);




	$pdf->Output();
	break;

case 'excel':
	break;
}

?>
