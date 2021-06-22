<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$saya = $_SESSION['standard']['userid'];
$method = $_POST['method'];
if ('' != $method) {
    $kodetraining = $_POST['kodetraining'];
    $karyawanid = $_POST['karyawanid'];
    $sayaadalah = $_POST['sayaadalah'];
    $alasannya = $_POST['alasannya'];
    if ('alasanditolak' == $method) {
        if ('atasan' == $sayaadalah) {
            $str = 'UPDATE '.$dbname.".`sdm_5training` SET `stpersetujuan1` = '2',\r\n`catatan1` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid.' ';
        } else {
            $str = 'UPDATE '.$dbname.".`sdm_5training` SET `sthrd` = '2',\r\n`catatanhrd` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid.' ';
        }
    } else {
        if ('atasan' == $sayaadalah) {
            $str = 'UPDATE '.$dbname.".`sdm_5training` SET `stpersetujuan1` = '1',\r\n`catatan1` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid.' ';
        } else {
            $str = 'UPDATE '.$dbname.".`sdm_5training` SET `sthrd` = '1',\r\n`catatanhrd` = '".$alasannya."' WHERE `kode` = '".$kodetraining."' AND `karyawanid` =".$karyawanid.' ';
        }
    }

    if (mysql_query($str)) {
    } else {
        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    }
}

if ('' == $method) {
    $method = $_GET['method'];
    if ('tolak' == $method || 'setuju' == $method) {
        $kodetraining = $_GET['kodetraining'];
        $karyawanid = $_GET['karyawanid'];
        $sayaadalah = $_GET['sayaadalah'];
        if ('tolak' == $method) {
            $tulisanalasan = $_SESSION['lang']['alasanDtolak'];
            $scriptalasan = 'alasanditolak';
        } else {
            $tulisanalasan = $_SESSION['lang']['alasanDterima'];
            $scriptalasan = 'alasandisetujui';
        }

        echo "<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n    <script language=javascript src='js/sdm_daftarPengajuanTraining.js'></script>\r\n";
        echo "<table cellspacing=1 border=0 style='width:500px;'>\r\n         <thead>\r\n         <tr class=rowheader>\r\n            <td>".$tulisanalasan."</td>\r\n            <td><textarea rows=2 cols=22 id=alasannya onkeypress=\"return parent.tanpa_kutip();\"></textarea></td>\r\n            <td><button class=mybutton onclick=".$scriptalasan."('".$kodetraining."','".$karyawanid."','".$sayaadalah."')>".$_SESSION['lang']['save']."</button></td>\r\n         </tr></thead>\r\n         <tbody>";
        echo "</tbody>\r\n        <tfoot>\r\n        </tfoot>\r\n        </table>";
        exit();
    }
}

$str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 order by namakaryawan";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $nam[$bar->karyawanid] = $bar->namakaryawan;
}
$limit = 20;
$page = 0;
if (isset($_POST['pilihkaryawan'])) {
    $pilihkaryawan = $_POST['pilihkaryawan'];
}

$str = 'select count(*) as jlhbrs from '.$dbname.".sdm_5training \r\n        where karyawanid like '%".$pilihkaryawan."%'\r\n\t\torder by jlhbrs desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jlhbrs = $bar->jlhbrs;
}
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$str = 'select * from '.$dbname.".sdm_5training \r\n        where karyawanid like '%".$pilihkaryawan."%'\r\n\t\torder by tglmulai desc,tglselesai desc,updatetime desc  limit ".$offset.',20';
$res = mysql_query($str);
$no = $page * $limit;
while ($bar = mysql_fetch_object($res)) {
    if ($bar->persetujuan1 == $saya) {
        $sayaadalah = 'atasan';
    }

    if ($bar->persetujuanhrd == $saya) {
        $sayaadalah = 'hrd';
    }

    ++$no;
    echo "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$nam[$bar->karyawanid]."</td>\r\n\t  <td>".$bar->namatraining."</td>\r\n\t  <td align=center>".tanggalnormal($bar->tglmulai)."</td>\r\n\t  <td align=right>".number_format($bar->hargasatuan)."</td>\r\n\t  <td align=center>".tanggalnormal($bar->tglselesai)."</td>\r\n\t  <td align=center>\r\n             <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."','".$bar->karyawanid."');\">".$_SESSION['lang']['pdf'].'</button>';
    if ($bar->persetujuan1 == $saya && 0 == $bar->stpersetujuan1 || $bar->persetujuanhrd == $saya && 0 == $bar->sthrd) {
        echo "<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>\r\n             <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju'].'</button>';
    }

    echo "</td>\r\n\t  </tr>";
}
echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>