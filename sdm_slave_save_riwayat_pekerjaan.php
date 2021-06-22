<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$namaperusahaan = $_POST['namaperusahaan'];
$bidangusaha = $_POST['bidangusaha'];
$blnmasuk = $_POST['blnmasuk'];
$thnmasuk = $_POST['thnmasuk'];
$blnkeluar = $_POST['blnkeluar'];
$thnkeluar = $_POST['thnkeluar'];
$thn = $thnkeluar - $thnmasuk;
$bln = (int) $blnkeluar - (int) $blnmasuk;
$masakerja = ($thn * 12 + $bln) / 12;
$blnkeluar = $blnkeluar.'-'.$thnkeluar;
$blnmasuk = $blnmasuk.'-'.$thnmasuk;
$jabatan = $_POST['jabatan'];
$bagian = $_POST['bagian'];
$alamat = $_POST['alamat'];
$karyawanid = $_POST['karyawanid'];
$nourut = $_POST['nomor'];
if (0 < $masakerja || 'true' == $_POST['del'] || isset($_POST['queryonly'])) {
    if ('' == $nourut) {
        $nourut = 0;
    }

    if (isset($_POST['del']) && 'true' == $_POST['del']) {
        $str = 'delete from '.$dbname.'.sdm_karyawancv where nomor='.$nourut;
    } else {
        if (isset($_POST['queryonly'])) {
            $str = 'select 1=1';
        } else {
            $str = 'insert into '.$dbname.".sdm_karyawancv\r\n\t\t     (`karyawanid`,\r\n\t\t\t  `namaperusahaan`,\r\n\t\t\t  `bidangusaha`,\r\n\t\t\t  `bulanmasuk`,\r\n\t\t\t  `bulankeluar`,\r\n\t\t\t  `jabatan`,\r\n\t\t\t  `bagian`,\r\n\t\t\t  `masakerja`,\r\n\t\t\t  `alamatperusahaan`\r\n\t\t\t  )\r\n\t\t\t  values(".$karyawanid.",\r\n\t\t\t  '".$namaperusahaan."',\r\n\t\t\t  '".$bidangusaha."',\r\n\t\t\t  '".$blnmasuk."',\r\n\t\t\t  '".$blnkeluar."',\r\n\t\t\t  '".$jabatan."',\r\n\t\t\t  '".$bagian."',\r\n\t\t\t  ".$masakerja.",\r\n\t\t\t  '".$alamat."'\r\n\t\t\t  )";
        }
    }

    if (mysql_query($str)) {
        $str = 'select *,right(bulanmasuk,4) as masup,left(bulanmasuk,2) as busup from '.$dbname.'.sdm_karyawancv where karyawanid='.$karyawanid.' order by masup,busup';
        $res = mysql_query($str);
        $no = 0;
        $mskerja = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $msk = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulanmasuk), 0, 2), 1, substr($bar->bulanmasuk, 3, 4));
            $klr = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulankeluar), 0, 2), 1, substr($bar->bulankeluar, 3, 4));
            $dateDiff = $klr - $msk;
            $mskerja = floor($dateDiff / (60 * 60 * 24)) / 365;
            echo "\t  <tr class=rowcontent>\r\n\t\t\t  <td class=firsttd>".$no."</td>\r\n\t\t\t  <td>".$bar->namaperusahaan."</td>\r\n\t\t\t  <td>".$bar->bidangusaha."</td>\r\n\t\t\t  <td>".$bar->bulanmasuk."</td>\r\n\t\t\t  <td>".$bar->bulankeluar."</td>\r\n\t\t\t  <td>".$bar->jabatan."</td>\r\n\t\t\t  <td>".$bar->bagian."</td>\r\n\t\t\t  <td>".number_format($mskerja, 2, ',', '.')." Th.</td>\r\n\t\t\t  <td>".$bar->alamatperusahaan."</td>\t\r\n\t\t\t  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPengalaman('".$karyawanid."','".$bar->nomor."');\"></td>\r\n\t\t\t</tr>";
        }
    } else {
        echo ' Gagal:'.addslashes(mysql_error($conn)).$str;
    }
} else {
    echo ' Error: Incorrect Period';
}

?>