<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/fpdf.php';
if ('excel' === $_GET['proses'] || 'pdf' === $_GET['proses']) {
    $param = $_GET;
} else {
    $param = $_POST;
}

$optCek = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,premirajin');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
switch ($param['proses']) {
    case 'saveAll':
        if ('' === $param['kdTangki']) {
            exit("error: Tank can't empty!");
        }

        $qcek = mysql_query('select count(kodeorg) as total from '.$dbname.".pabrik_5vtangki where kodeorg='".$param['kodeorg']."' AND kodetangki='".$param['kdTangki']."' AND tinggicm=".$param['tinggi']."");
		$cek   = mysql_fetch_assoc($qcek);
		if($cek['total'] != 0) {
			echo 'DB error : data sudah ada';
			exit();
		}
		
        $sinsert = 'insert into '.$dbname.'.`pabrik_5vtangki` (`kodeorg`,`kodetangki`,`tinggicm`,`volume`,`updateby`) values';
        $sinsert .= "('".$param['kodeorg']."','".$param['kdTangki']."','".$param['tinggi']."','".$param['vol']."','".$_SESSION['standard']['userid']."')";
        if (!mysql_query($sinsert)) {
            exit('error: db error '.mysql_error($conn).'___'.$sinsert);
        }

        break;
    case 'updateData':
        if ('' === $param['kdTangki']) {
            exit("error: Tank can't empty!");
        }

        $sinsert = 'update '.$dbname.".`pabrik_5vtangki` set `kodetangki`='".$param['kdTangki']."',`tinggicm`='".$param['tinggi']."'\n                  ,`volume`='".$param['vol']."',`updateby`='".$_SESSION['standard']['userid']."' \n                  where kodeorg='".$param['kodeorg']."' and `kodetangki`='".$param['oldkdTangki']."' and `tinggicm`='".$param['oldTinggi']."'";
        if (!mysql_query($sinsert)) {
            exit('error: db error '.mysql_error($conn).'___'.$sinsert);
        }

        break;
    case 'loadData':
        if ('' !== $param['tangkiCr']) {
            $whr .= "and kodetangki='".$param['tangkiCr']."'";
        }

        if ('' !== $param['tinggiCm']) {
            $whr .= "and tinggicm='".$param['tinggiCm']."'";
        }

        $sData = 'select distinct * from '.$dbname.".pabrik_5vtangki where \n                kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$whr.' order by tinggicm asc';
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['kodetangki'].'</td>';
            $tab .= '<td>'.$rData['tinggicm'].'</td>';
            $tab .= '<td>'.$rData['volume'].'</td>';
            $tab .= "<td>\n                   \n                   <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['kodetangki']."','".$rData['tinggicm']."','".$rData['volume']."');\">\n                    &nbsp;\n                   <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rData['kodeorg']."','".$rData['kodetangki']."','".$rData['tinggicm']."');\" >\n                   </td>";
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'loadData2':
        if ('' !== $param['tangkiCr2']) {
            $whr .= "and kodetangki='".$param['tangkiCr2']."'";
        }

        if ('' !== $param['kdOrg']) {
            $whr .= "and kodeorg='".$param['kdOrg']."'";
        }

        $sData = 'select distinct * from '.$dbname.".pabrik_5vtangki where \n                kodeorg!='' ".$whr.' order by tinggicm asc';
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['kodetangki'].'</td>';
            $tab .= '<td>'.$rData['tinggicm'].'</td>';
            $tab .= '<td>'.$rData['volume'].'</td>';
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'delData':
        $sdel = 'delete from '.$dbname.".`pabrik_5vtangki`  where \n               kodeorg='".$param['kodeorg']."' and kodetangki='".$param['kdTangki']."'\n               and tinggicm='".$param['tinggiCm']."'";
        if (!mysql_query($sdel)) {
            exit('error: db error '.mysql_error($conn).'___'.$sdel);
        }

        break;
    case 'excel':
        $tab .= '<table class=sortable border=1 cellpadding=1 cellspacing=1>';
        $tab .= '<thead>';
        $tab .= '<tr bgcolor=#DEDEDE align=center> ';
        $tab .= '<td>No.</td><td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodetangki'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tinggi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['volume'].'</td>';
        if ('' !== $param['tangkiCr2']) {
            $whr .= "and kodetangki='".$param['tangkiCr2']."'";
        }

        if ('' !== $param['kdOrg']) {
            $whr .= "and kodeorg='".$param['kdOrg']."'";
        }

        $sData = 'select distinct * from '.$dbname.".pabrik_5vtangki where \n                kodeorg!='' ".$whr.' order by tinggicm asc';
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['kodetangki'].'</td>';
            $tab .= '<td>'.$rData['tinggicm'].'</td>';
            $tab .= '<td>'.$rData['volume'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'setup5Volumetangki__'.$param['kodeorg'].'__'.$param['tangkiCr2'];
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\n        parent.window.alert('Can't convert to excel format');\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\n        window.location='tempExcel/".$nop_.".xls';\n        </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        $table = $_GET['table'];
        $column = explode(',', $_GET['column']);
        $where = $_GET['cond'];

class masterpdf extends FPDF
{
    public function Header()
    {
        global $table;
        global $header;
        global $column;
        global $dbname;
        global $optNmKary;
        global $optNmOrg;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['unit'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(1 / 100 * $width, $height, $optNmOrg[$column[0]].' '.$column[0], '', 1, 'L');
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, $column[1], '', 1, 'L');
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['kodepremi'], '', 0, 'L');
        $this->Cell(2 / 100 * $width, $height, ':', '', 0, 'L');
        $this->Cell(1 / 100 * $width, $height, $column[2], '', 0, 'L');
        $this->Ln();
    }
}

        $pdf = new masterpdf('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->AddPage();
        $pdf->Cell(20, 1.5 * $height, 'No.', 'TBLR', 0, 'C');
        $pdf->Cell(160, 1.5 * $height, $_SESSION['lang']['namakaryawan'], 'TBLR', 0, 'L');
        $pdf->Cell(65, 1.5 * $height, $_SESSION['lang']['hasilkerjakg'], 'TBLR', 0, 'C');
        $pdf->Cell(65, 1.5 * $height, $_SESSION['lang']['premi'], 'TBLR', 1, 'L');
        $no = 0;
        $pdf->SetFillColor(255, 255, 255);
        $ql = 'select a.* from '.$dbname.'.'.$table." a \n                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\n                where kodeorg='".$column[0]."' and periode='".$column[1]."'\n                order by namakaryawan asc";
        $qData = mysql_query($ql);
        while ($data = mysql_fetch_assoc($qData)) {
            $pdf->SetFont('Arial', '', 7);
            ++$no;
            $pdf->Cell(20, $height, $no, 'TBLR', 0, 'L');
            $pdf->Cell(160, $height, $optNmKary[$data['karyawanid']], 'TBLR', 0, 'L');
            $pdf->Cell(65, $height, number_format($data['totalkg'], 0), 'TBLR', 0, 'R');
            $pdf->Cell(65, $height, number_format($data['rupiahpremi'], 0), 'TBLR', 1, 'R');
        }
        $pdf->Cell(15, $height, 'Page '.$pdf->PageNo(), '', 1, 'L');
        $pdf->Output();

        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);
    if ($date1 === $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>