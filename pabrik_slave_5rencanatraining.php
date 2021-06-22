<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$kamar = $_POST['kamar'];
if ('' === $kamar) {
    $kamar = $_GET['kamar'];
    $kodetraining = $_GET['kodetraining'];
} else {
    $tahunbudget = $_POST['tahunbudget'];
    $listtahun = $_POST['listtahun'];
    $kodetraining = $_POST['kodetraining'];
    $namatraining = $_POST['namatraining'];
    $levelpeserta = $_POST['levelpeserta'];
    $levelpeserta = $_POST['levelpeserta'];
    $penyelenggara = $_POST['penyelenggara'];
    $hargaperpeserta = $_POST['hargaperpeserta'];
    $deskripsitraining = $_POST['deskripsitraining'];
    $hasildiharapkan = $_POST['hasildiharapkan'];
}

$str = 'select * from '.$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $host[$bar->supplierid] = $bar->namasupplier;
}
$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jab[$bar->kodejabatan] = $bar->namajabatan;
}
if ('tahun' === $kamar) {
    $str = 'select distinct tahunbudget from '.$dbname.'.sdm_5training order by tahunbudget desc';
    $res = mysql_query($str);
    $opttahun = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $opttahun .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
    }
    echo $opttahun;
}

if ('list' === $kamar) {
    $str = 'select * from '.$dbname.".sdm_5training where tahunbudget like '%".$listtahun."%'\r\n          ";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        echo "<tr class=rowcontent>\r\n        <td>".$no."</td>\r\n        <td>".$bar->tahunbudget."</td>\r\n        <td>".$bar->kode."</td>\r\n        <td>".$bar->namatraining."</td>\r\n        <td>".$jab[$bar->jabatan]."</td>\r\n        <td>".$host[$bar->penyelenggara]."</td>\r\n\r\n        <td align=right>".number_format($bar->hargasatuan, 2, '.', ',')."</td>\r\n        <td>\r\n            <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->jabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".$bar->desctraining."','".$bar->output."');\">\r\n        </td>\r\n        <td>\r\n            <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">\r\n        </td>\r\n        <td>\r\n            <img src=images/application/application_form.png class=resicon  title='desc and result' onclick=\"desctraining('".$bar->kode."',event);\">\r\n        </td>\r\n        </tr>";
        ++$no;
    }
}

if ('desc' === $kamar) {
    echo "<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
    $str = 'select * from '.$dbname.".sdm_5training\r\n          where kode='".$kodetraining."'";
    $res = mysql_query($str);
    echo '<table class=sortable cellspacing=1 border=0 width=100%>';
    while ($bar = mysql_fetch_object($res)) {
        echo '<tr class=rowtitle><td>'.$_SESSION['lang']['deskripsitraining']."</td></tr>\r\n        <tr class=rowcontent><td align=center><textarea disabled=true>".$bar->desctraining."</textarea></td></tr>\r\n        <tr class=rowtitle><td>".$_SESSION['lang']['hasildiharapkan']."</td></tr>\r\n        <tr class=rowcontent><td align=center><textarea disabled=true>".$bar->output."</textarea></td></tr>\r\n            <tr class=rowcontent><td align=center>&nbsp;</td></tr>\r\n            <tr class=rowcontent><td align=center><button class=mybutton onclick=parent.closeDialog()>".$_SESSION['lang']['close'].'</button></td></tr>';
    }
    echo '</table>';
}

if ('save' === $kamar) {
    $strx = 'insert into '.$dbname.".sdm_5training\r\n        (kode,namatraining,jabatan,\r\n\tpenyelenggara,hargasatuan,desctraining,\r\n\toutput,tahunbudget)\r\n\tvalues('".$kodetraining."','".$namatraining."','".$levelpeserta."',\r\n\t'".$penyelenggara."','".$hargaperpeserta."','".$deskripsitraining."',\r\n\t'".$hasildiharapkan."','".$tahunbudget."')";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('delete' === $kamar) {
    $strx = 'delete from '.$dbname.".sdm_5training \r\n    where kode='".$kodetraining."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('edit' === $kamar) {
    $strx = 'update '.$dbname.".sdm_5training set\r\n        namatraining = '".$namatraining."',\r\n        jabatan = '".$levelpeserta."',\r\n        penyelenggara = '".$penyelenggara."',\r\n        hargasatuan = '".$hargaperpeserta."',\r\n        desctraining = '".$deskripsitraining."',\r\n        output = '".$hasildiharapkan."',\r\n        tahunbudget = '".$tahunbudget."'   \r\n        where kode = '".$kodetraining."'";
    if (mysql_query($strx)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

?>