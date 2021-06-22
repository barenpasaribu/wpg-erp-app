<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');
#echo "<pre>";
#print_r($_SESSION);
#exit;
# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];


#====================== Prepare Data
$query = selectQuery($dbname,$table);
#print_r ($_GET);
#exit;
$result = fetchData($query);
$header = array();
foreach($result[0] as $key=>$row) {
    $header[] = $key;
}

#====================== Prepare Header PDF
class masterpdf extends FPDF {
    function Header() {
        global $table;
        global $header;

        # Panjang, Lebar
        $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
        $this->SetFont('Arial','B',8);
                $this->Cell(20,$height,$_SESSION['org']['namaorganisasi'],'',1,'L');
        $this->SetFont('Arial','B',12);
#		$this->Cell($width,$height,'Tabel : '.$table,'',1,'L');
                $this->Cell($width,$height,strtoupper($_SESSION['lang']['daftarperkiraan']),'',1,'C');
        $this->SetFont('Arial','B',8);
                $this->Cell(420,$height,' ','',0,'R');
                $this->Cell(38,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(40,$height,date('d-m-Y H:i'),'',1,'L');
                $this->Cell(420,$height,' ','',0,'R');
                $this->Cell(38,$height,$_SESSION['lang']['page'],'',0,'L');
                $this->Cell(8,$height,':','',0,'L');
                $this->Cell(15,$height,$this->PageNo(),'',1,'L');
#        $this->Ln();
                $this->Cell(420,$height,' ','',0,'R');
                $this->Cell(38,$height,'User','',0,'L');
                $this->Cell(8,$height,':','',0,'L');
                $this->Cell(20,$height,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();

        # Generate Header
#        foreach($header as $hName) {
#            $this->Cell($width/count($header),$height,ucfirst($hName),'TBLR',0,'L');
#        }
        $this->Cell(60,1.5*$height,$_SESSION['lang']['nomorperkiraan'],'TBLR',0,'C');
        $this->Cell(260,1.5*$height,$_SESSION['lang']['namaperkiraan'],'TBLR',0,'C');
        $this->Cell(38,1.5*$height,$_SESSION['lang']['tipe'],'TBLR',0,'C');
        $this->Cell(45,1.5*$height,$_SESSION['lang']['level'],'TBLR',0,'C');
        $this->Cell(47,1.5*$height,$_SESSION['lang']['matauang'],'TBLR',0,'C');
        $this->Cell(40,1.5*$height,$_SESSION['lang']['tampilkan'],'TBLR',0,'C');        
        $this->Cell(40,1.5*$height,$_SESSION['lang']['detail'],'TBLR',0,'C');
        $this->Ln();
        $this->Ln();
    }
}

#====================== Prepare PDF Setting
$pdf = new masterpdf('P','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 12;
$pdf->SetFont('Arial','',8);
$pdf->AddPage();

# Generate Data
#print_r($result);
#exit;
#foreach($result as $row) {
#    foreach($row as $data) {
#        $pdf->Cell($width/count($header),$height,$data,'',0,'L');
#    }
#    $pdf->Ln();
#}
foreach($result as $data) {
        $pdf->Cell(60,$height,$data['noakun'],'',0,'L');
        if($_SESSION['language']=='EN'){
            $pdf->Cell(260,$height,$data['namaakun1'],'',0,'L');
        }else{
            $pdf->Cell(260,$height,$data['namaakun'],'',0,'L');
        }
        $pdf->Cell(40,$height,$data['tipeakun'],'',0,'C');
        $pdf->Cell(40,$height,$data['level'],'',0,'C');
        $pdf->Cell(60,$height,$data['matauang'],'',0,'C');
        $pdf->Cell(40,$height,$data['pemilik'],'',0,'C');        
        if ($data['detail']==1)
        {
        $pdf->Cell(40,$height,'Y','',0,'C');
        }
    $pdf->Ln();
}


# Print Out
$pdf->Output();
?>