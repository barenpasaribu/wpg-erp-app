<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$per = $_POST['per'];
$proses = $_GET['proses'];
$kodeorg = $_POST['kodeorg'];
$proses2 = $_POST['proses'];
$karyawanid = $_POST['karyawanid'];
$premiinput = $_POST['premiinput'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$stream = "<table cellspacing='1' border='0' class='sortable'>\r\n\t\t\t<thead class=rowheader>\r\n\t\t\t\t<tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['karyawanid']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nik']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t\t\t\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['hasilkerjajumlah']." Kg</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['absensi']."</td>\r\n\t\t\t\t\t\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['premi']." (Rp)</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
$i = 'select sum(hasilkerja) as hasilkerja,sum(jhk) as jhk,karyawanid,kodekegiatan from '.$dbname.".kebun_kehadiran_vw \r\n\t\twhere unit='".$kodeorg."' and  tanggal like '%".$per."%' and kodekegiatan in (select distinct kodekegiatan from ".$dbname.".kebun_5premimuat)\r\n\t\tgroup by karyawanid";
//echo $i;
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    ++$no;
    $stream .= '<tr class=rowcontent id=row'.$no.'>';
    $stream .= '<td align=center>'.$no.'</td>';
    $stream .= '<td align=left id=karyawanid'.$no.'>'.$d['karyawanid'].'</td>';
    $stream .= '<td align=left>'.$nikKar[$d['karyawanid']].'</td>';
    $stream .= '<td align=left>'.$nmKar[$d['karyawanid']].'</td>';
    $stream .= '<td align=left>'.$d['hasilkerja'].'</td>';
    $stream .= '<td align=left>'.number_format($d['jhk'], 2).'</td>';
    $haha = 'select distinct * from '.$dbname.".kebun_5premimuat \r\n\t\t\t\t\t\t\t\t where kodekegiatan='".$d['kodekegiatan']."'  order by volume asc";
    $hihi = mysql_query($haha) ;
    while ($huhu = mysql_fetch_assoc($hihi)) {
        ++$dr;
        $jumhr = $huhu['jumlahhari'];
        $nildt[$dr] = $huhu['volume'];
        $volume[$dr] = $huhu['rupiah'];
    }
    $premi = 0;
    if ($d['hasilkerja'] < $nildt[1]) {
        $premi = '0';
    } else {
        if ($nildt[1] <= $d['hasilkerja'] && $d['hasilkerja'] < $nildt[2] && $jumhr <= $d['jhk']) {
            $premi = $volume[1];
        } else {
            if ($nildt[2] <= $d['hasilkerja'] && $d['hasilkerja'] < $nildt[3] && $jumhr <= $d['jhk']) {
                $premi = $volume[2];
            } else {
                if ($nildt[3] <= $d['hasilkerja'] && $jumhr <= $d['jhk']) {
                    $premi = $volume[3];
                }
            }
        }
    }

    $stream .= '<td align=left id=premiinput'.$no.'>'.$premi.'</td>';
    $stream .= '</tr>';
}
$stream .= '</table>';
$n = mysql_query($i);
$xi = 'select distinct * from '.$dbname.".sdm_5periodegaji where periode='".$per."' \r\n and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses='1'";
$xu = mysql_query($xi) ;
if (0 < mysql_num_rows($xu)) {
    $aktif2 = false;
} else {
    $aktif2 = true;
}

$str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$per."' and \r\n             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    $aktif = false;
} else {
    $aktif = true;
}

if ('' === $per) {
    exit('Error:Periode masih kosong');
}
/* 
if (!$aktif2 || !$aktif) {
    $stream .= "<b>You can't proses this periode because acoounting or payroll periode for ".$_SESSION['empl']['lokasitugas'].' has been closed</b>';
} else { */
    if (mysql_num_rows($n) < 1) {
        $stream = 'Data was empty';
    } else {
        $stream .= '<button class=mybutton onclick=saveAll('.$no.');>'.$_SESSION['lang']['proses'].'</button>';
    }
//}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
}
switch ($proses2) {
    case 'savedata':
        if ('0' === $premiinput || '' === $premiinput) {
        } else {
            $str = 'insert into '.$dbname.".kebun_premikemandoran (periode,kodeorg,karyawanid,jabatan,pembagi,premiinput,updateby,posting)\r\n\t\tvalues ('".$per."','".$kodeorg."','".$karyawanid."','LOADING','1','".$premiinput."','".$_SESSION['standard']['userid']."','1')";
            if (mysql_query($str)) {
            } else {
                $str = 'update '.$dbname.".kebun_premikemandoran set premiinput='".$premiinput."',posting=1\r\n\t\t\t where periode='".$per."' and kodeorg='".$kodeorg."' and karyawanid='".$karyawanid."'";
                if (mysql_query($str)) {
                } else {
                    echo ' Gagal,'.addslashes(mysql_error($conn));
                }
            }
        }

        break;
}

?>