<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
if (isset($_GET['method'])) {
    $method = $_GET['method'];
    $statPP = $_GET['statPP'];
} else {
    $method = $_POST['method'];
}

$thn = $_GET['thn'];
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optBagian = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optTipekary = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optTipeGaji = makeOption($dbname, 'sdm_ho_component', 'id,name');
switch ($method) {
    case 'dataDetail':
        $sJumlah = 'select distinct jumlah,karyawanid,idkomponen from '.$dbname.".sdm_5gajipokok \r\n        where  idkomponen in (select id from ".$dbname.".sdm_ho_component where type='basic') and tahun='".$thn."'";
        $qJumlah = mysql_query($sJumlah);
        while ($rJumlah = mysql_fetch_assoc($qJumlah)) {
            $barGapok[$rJumlah['idkomponen']][$rJumlah['karyawanid']] = $rJumlah['jumlah'];
            $idKom[$rJumlah['idkomponen']] = $rJumlah['idkomponen'];
        }
        $stream .= " \r\n         <table border=\"1\">\r\n\t <thead>\r\n\t <tr>\r\n\t <td bgcolor=#DEDEDE align=center valign=middle>No.</td>\r\n\t <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['nik']."</td>\r\n         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['namakaryawan']."</td>\r\n         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['bagian']."</td>\r\n         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['kodegolongan']."</td>\r\n         <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tipekaryawan']."</td>\r\n\t <td bgcolor=#DEDEDE align=center valign=middle>".$_SESSION['lang']['tmk']."</td>\r\n         <td bgcolor=#DEDEDE align=center valign=middle>Masa Kerja</td>";
        foreach ($idKom as $lstKom) {
            $stream .= '<td bgcolor=#DEDEDE align=center valign=middle>'.$optTipeGaji[$lstKom].'</td>';
        }
        $stream .= " </tr>\r\n\t </thead>\r\n\t <tbody>";
        $thndt = date('Y');
        $thnLalu = (int) $thndt - 1;
        $thnDatalalu = $thnLalu.'-12-31';
        $sql = "select distinct a.karyawanid,b.namakaryawan,b.tanggalmasuk,idkomponen,bagian,b.nik,b.kodegolongan,COALESCE(ROUND(DATEDIFF('".$thnDatalalu."',b.tanggalmasuk)/365,2),0) as masakerja,tipekaryawan from ".$dbname.'.sdm_5gajipokok a left join '.$dbname.".datakaryawan \r\n            b on a.karyawanid=b.karyawanid where tahun='".$thn."' and idkomponen in (select id from ".$dbname.".sdm_ho_component where type='basic') and lokasitugas in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['kodeorganisasi']."') order by idkomponen";
function daysBetween($s, $e)
{
    $s = strtotime($s);
    $e = strtotime($e);

    return ($e - $s) / (24 * 3600);
}

        if (mysql_query($sql)) {
            $res = mysql_query($sql);
            while ($bar = mysql_fetch_assoc($res)) {
                $masakerja = daysBetween($bar['tanggalmasuk'], $thnDatalalu);
                ++$no;
                $stream .= '<tr>';
                $stream .= '<td>'.$no.'</td>';
                $stream .= "<td>'".$bar['nik'].'</td>';
                $stream .= '<td>'.$bar['namakaryawan'].'</td>';
                $stream .= '<td>'.$optBagian[$bar['bagian']].'</td>';
                $stream .= '<td>'.$bar['kodegolongan'].'</td>';
                $stream .= '<td>'.$optTipekary[$bar['tipekaryawan']].'</td>';
                $stream .= '<td>'.tanggalnormal($bar['tanggalmasuk']).'</td>';
                $stream .= '<td>'.number_format($bar['masakerja'], 0).'</td>';
                foreach ($idKom as $lstKom) {
                    $stream .= '<td>'.number_format($barGapok[$lstKom][$bar['karyawanid']], 0).'</td>';
                }
                $stream .= '</tr>';
            }
        } else {
            echo ' Gagal,'.mysql_error($conn);
        }

        $stream .= ' </tbody>';
        $stream .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $time = date('Hms');
        $nop_ = 'lapDatGapok_'.$time;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                            window.location='tempExcel/".$nop_.".xls.gz';\r\n                            </script>";

        break;
    default:
        break;
}
echo "\r\n";

?>