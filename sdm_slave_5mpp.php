<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$kamar = $_POST['kamar'];
if ('' == $kamar) {
    $kamar = $_GET['kamar'];
    $kunci = $_GET['kunci'];
} else {
    $listtahun = $_POST['listtahun'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodeorg = $_POST['kodeorg'];
    $bagian = $_POST['bagian'];
    $golongan = $_POST['golongan'];
    $jabatan = $_POST['jabatan'];
    $mingaji = $_POST['mingaji'];
    $maxgaji = $_POST['maxgaji'];
    $tanggalmasuk = $_POST['tanggalmasuk'];
    $tanggalmasuk = tanggalsystem($tanggalmasuk);
    $minumur = $_POST['minumur'];
    $maxumur = $_POST['maxumur'];
    $jeniskelamin = $_POST['jeniskelamin'];
    $pendidikan = $_POST['pendidikan'];
    $pengalaman = $_POST['pengalaman'];
    $poh = $_POST['poh'];
    $jumlah = $_POST['jumlah'];
    $kunci = $_POST['kunci'];
}

$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jab[$bar->kodejabatan] = $bar->namajabatan;
}
$str = 'select * from '.$dbname.'.sdm_5golongan order by kodegolongan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $gol[$bar->kodegolongan] = $bar->namagolongan;
}
if ('tahun' == $kamar) {
    $str = 'select distinct tahunbudget from '.$dbname.'.sdm_5mpp order by tahunbudget desc';
    $res = mysql_query($str);
    $opttahun = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $opttahun .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
    }
    echo $opttahun;
}

if ('list' == $kamar) {
    $str = 'select * from '.$dbname.".sdm_5mpp\r\n      where tahunbudget like '%".$listtahun."%'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        echo "<tr class=rowcontent>\r\n    <td>".$no."</td>\r\n    <td>".$bar->tahunbudget."</td>\r\n    <td>".$bar->kodeorg."</td>\r\n    <td>".$bar->departement."</td>\r\n    <td>".$gol[$bar->golongan]."</td>\r\n    <td>".$jab[$bar->jabatan]."</td>\r\n    <td align=right>".number_format($bar->startgaji, 2, '.', ',')."</td>\r\n    <td align=right>".number_format($bar->endgaji, 2, '.', ',')."</td>\r\n    <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n    <td align=right>".$bar->startumur."</td>\r\n    <td align=right>".$bar->endumur."</td>\r\n    <td>".$bar->jkelamin."</td>\r\n    <td>".$bar->pendidikan."</td>\r\n    <td align=right>".$bar->pengalaman."</td>\r\n    <td>".$bar->poh."</td>\r\n    <td align=right>".$bar->jumlah."</td>\r\n\r\n    <td>\r\n        <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edit('".$bar->tahunbudget."','".$bar->kodeorg."','".$bar->departement."','".$bar->golongan."','".$bar->jabatan."','".$bar->startgaji."','".$bar->endgaji."','".tanggalnormal($bar->tanggalmasuk)."',\r\n        '".$bar->startumur."','".$bar->endumur."','".$bar->jkelamin."','".$bar->pendidikan."','".$bar->pengalaman."','".$bar->poh."','".$bar->jumlah."','".$bar->kunci."');\">\r\n    </td>\r\n    <td>\r\n        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"del('".$bar->kunci."');\">\r\n    </td>\r\n    </tr>";
        ++$no;
    }
}

if ('save' == $kamar) {
    $strx = 'insert into '.$dbname.".sdm_5mpp\r\n        (tahunbudget,kodeorg,departement,\r\n\tgolongan,jabatan,startgaji,\r\n\tendgaji,startumur,endumur,jkelamin,pendidikan,pengalaman,poh,jumlah,tanggalmasuk)\r\n\tvalues('".$tahunbudget."','".$kodeorg."','".$bagian."',\r\n\t'".$golongan."','".$jabatan."','".$mingaji."',\r\n\t'".$maxgaji."','".$minumur."','".$maxumur."','".$jeniskelamin."','".$pendidikan."','".$pengalaman."','".$poh."','".$jumlah."','".$tanggalmasuk."')";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('delete' == $kamar) {
    $strx = 'delete from '.$dbname.".sdm_5mpp \r\n    where kunci='".$kunci."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('edit' == $kamar) {
    $strx = 'update '.$dbname.".sdm_5mpp set\r\n        tahunbudget = '".$tahunbudget."',\r\n        kodeorg = '".$kodeorg."',\r\n        departement = '".$bagian."',\r\n        golongan = '".$golongan."',\r\n        jabatan = '".$jabatan."',\r\n        startgaji = '".$mingaji."',\r\n        endgaji = '".$maxgaji."',   \r\n        tanggalmasuk = '".$tanggalmasuk."',\r\n        startumur = '".$minumur."',\r\n        endumur = '".$maxumur."',\r\n        jkelamin = '".$jeniskelamin."',\r\n        pendidikan = '".$pendidikan."',\r\n        pengalaman = '".$pengalaman."',\r\n        poh = '".$poh."',\r\n        jumlah = '".$jumlah."'   \r\n        where kunci = '".$kunci."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

?>