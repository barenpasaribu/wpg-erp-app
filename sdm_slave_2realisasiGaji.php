<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' == $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' == $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
('' == $_POST['kdUnit'] ? ($kdUnit = $_GET['kdUnit']) : ($kdUnit = $_POST['kdUnit']));
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$bln = explode('-', $periode);
if ('01' == $bln[1]) {
    $thnlalu = (int) ($bln[0]) - 1;
    $blnlalu = '12';
} else {
    $thnlalu = $bln[0];
    $blnlalu = (int) ($bln[1]) - 1;
    if ($blnlalu < 10) {
        $blnlalu = '0'.$blnlalu;
    }
}
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$periodelama = $thnlalu.'-'.$blnlalu;
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
$brd = 0;
$bgclr = 'bgcolor=#DEDEDE ';
if ('excel' == $proses) {
    $brd = 1;
    $bgclr = 'bgcolor=#DEDEDE ';
    $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
    $tab .= '<tr><td colspan=6>Laporan Realisasi Gaji</td></tr>';
    $tab .= '<tr><td colspan=2>'.$_SESSION['lang']['unit'].'</td><td>:</td>';
    $tab .= '<td colspan=3>'.$optNm[$kdUnit].'</td></tr>';
    $tab .= '<tr><td colspan=2>'.$_SESSION['lang']['periode'].'</td><td>:</td>';
    $tab .= '<td colspan=3>'.$periode."</td></tr>\r\n           <tr><td colspan=6>&nbsp;</td></tr>\r\n           </table>";
}

if ('excel' == $proses || 'preview' == $proses) {
    if ('' == $periode || '' == $kdUnit) {
        exit('Error: Field Tidak Boleh Kosong');
    }

    $query ="select distinct sum(jumlah) as jumlah, idkomponen,count(a.karyawanid) as org, subbagian, name 
from sdm_gajidetail_vw a inner join datakaryawan b ON a.karyawanid=b.karyawanid where a.kodeorg='".$kdUnit."' 
and periodegaji='".$periode."' group by subbagian, idkomponen ORDER BY subbagian, idkomponen";

    $res1 = mysql_query($query);
    while ($rows1 = mysql_fetch_array($res1)) {
        $id=$rows1['idkomponen'];
        $subbagian=$rows1['subbagian'];
        $data[$subbagian]=$subbagian;
        $data[$subbagian.'-'.$id]['jumlah']=$rows1['jumlah'];
        $data[$subbagian.'-'.$id]['name']=$rows1['name'];
        $data[$subbagian.'-'.$id]['org']=$rows1['org'];
    }

    //tampilkan data
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead>';
    $tab .= '<tr><td rowspan=2 '.$bgclr.' align=center>No.</td>';
    $tab .= '<td rowspan=2 '.$bgclr.' align=center>'.$_SESSION['lang']['jenisbiaya'].'</td>';
    $tab .= '<td colspan=2 '.$bgclr.' align=center>'.$_SESSION['lang']['bulanini'].'</td></tr>';
    $tab .= '<tr><td '.$bgclr.' align=center>'.$_SESSION['lang']['orang'].'</td><td '.$bgclr.' align=center>'.$_SESSION['lang']['rp'].'</td></tr></thead><tbody>';

     
    foreach ($data as $idkomponen => $value) {
        if($value[name]=='S' || $value[name]=='' || $value[name]=='L'){
            if($idkomponen==''){ $idkomponen='UMUM'; }
                $tab .= '<tr><td colspan=4>'.$idkomponen.' - '. $optNm[$idkomponen].' </td></tr>';
            $no=0;        
        }else{
            $no++;
            $tab .= "<tr class=rowcontent><td>".$no."</td><td>".$value[name]."</td><td align=right>".$value[org]."</td>\r\n          <td align=right>".number_format($value[jumlah], 0)."</td></tr>";
        }
    }

    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'getPeriode':
        $opt = "<option values=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periodegaji from '.$dbname.".sdm_gaji where kodeorg ='".$kdUnit."' order by periodegaji  desc";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $opt .= "<option values='".$rPeriode['periodegaji']."'>".$rPeriode['periodegaji'].'</option>';
        }
        echo $opt;

        break;
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'realisasiGaji__'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";

        break;
    case 'pdf':
        $perod = $_GET['perod'];
        $idAfd = $_GET['idAfd'];
        $idKry = $_GET['idKry'];
        $kdBag2 = $_GET['kdBag2'];

class PDF extends FPDF
{
    public $col = 0;
    public $dbname;

    public function SetCol($col)
    {
        $this->col = $col;
        $x = 10 + $col * 100;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    public function AcceptPageBreak()
    {
        if ($this->col < 1) {
            $this->SetCol($this->col + 1);
            $this->SetY(10);

            return false;
        }

        $this->SetCol(0);

        return true;
    }

    public function Header()
    {
        $this->lMargin = 5;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 5);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 5);
        $bln = explode('-', $perod);
        $idBln = (int) ($bln[1]);
        $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from \r\n               ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid \r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan \r\n               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode\r\n               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add.'  '.$dtTipe.' order by b.namakaryawan asc';
        $qSlip = mysql_query($sSlip);
        $rCek = mysql_num_rows($qSlip);
        if (0 < $rCek) {
            while ($rSlip = mysql_fetch_assoc($qSlip)) {
                if ('' != $rSlip['karyawanid']) {
                    $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                }
            }
            $sKomp = 'select id,name,plus from '.$dbname.'.sdm_ho_component where plus=1 ';
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompPls[] = $rKomp['id'];
                $arrNmKomPls[$rKomp['id']][1] = $rKomp['name'];
            }
            $sKomp2 = 'select id,name,plus from '.$dbname.'.sdm_ho_component where plus=0 ';
            $qKomp2 = mysql_query($sKomp2);
            while ($rKomp2 = mysql_fetch_assoc($qKomp2)) {
                $arrIdKompPls[] = $rKomp2['id'];
                $arrNmKomPls[$rKomp2['id']][0] = $rKomp2['name'];
            }
            $arrMinusId = [];
            $arrMinusName = [];
            $str = 'select id,name from '.$dbname.".sdm_ho_component where plus='0' order by id";
            $res = mysql_query($str, $conn);
            while ($bar = mysql_fetch_object($res)) {
                array_push($arrMinusId, $bar->id);
                array_push($arrMinusName, $bar->name);
            }
            $arrPlusId = $arrMinusId;
            $arrPlusName = $arrMinusName;
            for ($r = 0; $r < count($arrMinusId); ++$r) {
                $arrPlusId[$r] = '';
                $arrPlusName[$r] = '';
            }
            $str = 'select  id,name from '.$dbname.".sdm_ho_component where plus='1' and id not in ('13','14') order by id";
            $res = mysql_query($str, $conn);
            $n = -1;
            while ($bar = mysql_fetch_object($res)) {
                ++$n;
                $arrPlusId[$n] = $bar->id;
                $arrPlusName[$n] = $bar->name;
            }
            $arrValPlus = [];
            $arrValMinus = [];
            for ($x = 0; $x < count($arrPlusId); ++$x) {
                $arrValPlus[$x] = 0;
                $arrValMinus[$x] = 0;
            }
            $str3 = 'select jumlah,idkomponen,a.karyawanid,c.plus from '.$dbname.".sdm_gaji_vw a \r\n                  left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id\r\n                 where a.sistemgaji='Bulanan' and a.periodegaji='".$perod."' ";
            $res3 = mysql_query($str3, $conn);
            while ($bar3 = mysql_fetch_assoc($res3)) {
                if ('1' == $bar3['plus']) {
                    if ('' != $bar3['jumlah']) {
                        $arrValPlus[$bar3['karyawanid']][$bar3['idkomponen']] = $bar3['jumlah'];
                    }
                } else {
                    if ('0' == $bar3['plus'] && '' != $bar3['jumlah']) {
                        $arrValMinus[$bar3['karyawanid']][$bar3['idkomponen']] = $bar3['jumlah'];
                    }
                }
            }
            foreach ($arrKary as $dtKary) {
                $pdf->Image('images/logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                $pdf->SetX($pdf->getX() + 10);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(75, 6, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(71, 4, $_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln].'-'.$bln[0], 'T', 0, 'L');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(25, 4, 'Printed on: '.date('d-m-Y: H:i:s'), 'T', 1, 'R');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(15, 4, $_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'], 0, 0, 'L');
                $pdf->Cell(35, 4, ': '.$arrNik[$dtKary].'/'.tanggalnormal($arrTglMsk[$dtKary]), 0, 0, 'L');
                $pdf->Cell(18, 4, $_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian'], 0, 0, 'L');
                $pdf->Cell(28, 4, ': '.$idAfd.' / '.$arrBag[$dtKary], 0, 1, 'L');
                $pdf->Cell(15, 4, $_SESSION['lang']['namakaryawan'].':', 0, 0, 'L');
                $pdf->Cell(35, 4, ': '.$arrNmKary[$dtKary], 0, 0, 'L');
                $pdf->Cell(18, 3, $_SESSION['lang']['jabatan'], 0, 0, 'L');
                $pdf->Cell(28, 4, ':'.$arrJbtn[$dtKary], 0, 1, 'L');
                $pdf->Cell(48, 4, $_SESSION['lang']['penambah'], 'TB', 0, 'C');
                $pdf->Cell(48, 4, $_SESSION['lang']['pengurang'], 'TB', 1, 'C');
                for ($mn = 0; $mn < count($arrPlusId); ++$mn) {
                    $pdf->Cell(25, 4, $arrPlusName[$mn], 0, 0, 'L');
                    if ('' == $arrPlusName[$mn]) {
                        $pdf->Cell(5, 4, '', 0, 0, 'L');
                        $pdf->Cell(18, 4, '', 'R', 0, 'R');
                    } else {
                        if ('' == $arrPlusId[$mn]) {
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 'R', 0, 'R');
                        } else {
                            $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($arrValPlus[$dtKary][$arrPlusId[$mn]], 2, '.', ','), 'R', 0, 'R');
                            $arrPlus[$dtKary] += $arrValPlus[$dtKary][$arrPlusId[$mn]];
                        }
                    }

                    $pdf->Cell(25, 4, $arrMinusName[$mn], 0, 0, 'L');
                    if ('' == $arrMinusName[$mn]) {
                        $pdf->Cell(5, 4, '', 0, 0, 'L');
                        $pdf->Cell(18, 4, '', 0, 1, 'R');
                    } else {
                        if ('' == $arrMinusId[$mn]) {
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 0, 1, 'R');
                        } else {
                            $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($arrValMinus[$dtKary][$arrMinusId[$mn]] * -1, 2, '.', ','), 0, 1, 'R');
                            $arrMin[$dtKary] += $arrValMinus[$dtKary][$arrMinusId[$mn]] * -1;
                        }
                    }
                }
                $pdf->Cell(25, 4, $_SESSION['lang']['totalPendapatan'], 'TB', 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 'TB', 0, 'L');
                $pdf->Cell(18, 4, number_format($arrPlus[$dtKary], 2, '.', ','), 'TB', 0, 'R');
                $pdf->Cell(25, 4, $_SESSION['lang']['totalPotongan'], 'TB', 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 'TB', 0, 'L');
                $pdf->Cell(18, 4, number_format($arrMin[$dtKary] * -1, 2, '.', ','), 'TB', 1, 'R');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(23, 4, $_SESSION['lang']['gajiBersih'], 0, 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                $pdf->Cell(18, 4, number_format($arrPlus[$dtKary] - $arrMin[$dtKary] * -1, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(47, 4, '', 0, 1, 'L');
                $terbilang = $arrPlus[$dtKary] - $arrMin[$dtKary] * -1;
                $blng = terbilang($terbilang, 2).' rupiah';
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(23, 4, 'Terbilang', 0, 0, 'L');
                $pdf->Cell(5, 4, ':', 0, 0, 'L');
                $pdf->MultiCell(58, 4, $blng, 0, 'L');
                $pdf->SetFont('Arial', 'I', 5);
                $pdf->Cell(96, 4, 'Note: This is computer generated system, signature is not required', 'T', 1, 'L');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Ln(10);
                if (225 < $pdf->GetY() && $pdf->col < 1) {
                    $pdf->AcceptPageBreak();
                }

                if (225 < $pdf->GetY() && 0 < $pdf->col) {
                    $r = 275 - $pdf->GetY();
                    $pdf->Cell(80, $r, '', 0, 1, 'L');
                }

                $pdf->cell(-1, 3, '', 0, 0, 'L');
            }
        } else {
            $pdf->Image('images/logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
            $pdf->SetX($pdf->getX() + 8);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(70, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(60, 3, 'NO DATA FOUND', 'T', 0, 'L');
        }

        $pdf->Output();

        break;
    default:
        break;
}

?>