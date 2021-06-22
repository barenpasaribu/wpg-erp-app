<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$tab = $_POST['tab'];
$proses = $_POST['proses'];
if ('' !== isset($_POST['txtfind'])) {
    $txtfind = $_POST['txtfind'];
    $str = 'select * from '.$dbname.".log_5masterbarang where (namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%') ";
    if ($res = mysql_query($str)) {
        echo "\r\n            <fieldset>\r\n            <legend>Result</legend>\r\n            <div style=\"overflow:auto; height:300px;\" >\r\n            <table class=data cellspacing=1 cellpadding=2  border=0>\r\n            <thead>\r\n            <tr class=rowheader>\r\n                <td class=firsttd>\r\n                    No.\r\n                </td>\r\n                <td>Kode Barang</td>\r\n                <td>Nama Barang</td>\r\n                <td>Satuan</td>\r\n            </tr>\r\n            </thead>\r\n            <tbody>";
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            if (1 === $bar->inactive) {
                echo "<tr class=rowcontent style='cursor:pointer;'  title='Inactive' >";
                $bar->namabarang = $bar->namabarang.' [Inactive]';
            } else {
                if ('1' === $tab) {
                    echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg(1,'".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
                }
            }

            echo ' <td class=firsttd>'.$no."</td>\r\n                    <td>".$bar->kodebarang."</td>\r\n                    <td>".$bar->namabarang."</td>\r\n                    <td>".$bar->satuan."</td>\r\n                    </tr>";
        }
        echo "</tbody>\r\n                    <tfoot>\r\n                    </tfoot>\r\n            </table></div></fieldset>";
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('' !== isset($_POST['spkfind'])) {
    $str = 'select * from '.$dbname.'.setup_kegiatan';
    if ($res = mysql_query($str)) {
        while ($bar = mysql_fetch_object($res)) {
            $kamuskeg[$bar->kodekegiatan] = $bar->namakegiatan;
        }
    }

    $spkfind = $_POST['spkfind'];
    $no = 0;
    $str = 'select * from '.$dbname.".log_baspk where notransaksi = '".$spkfind."' group by kodekegiatan";
    if ($res = mysql_query($str)) {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $optws .= "<option value='".$bar->kodekegiatan."'>".$kamuskeg[$bar->kodekegiatan].'</option>';
        }
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }

    $no = 0;
    $str = 'select * from '.$dbname.".log_baspk where notransaksi = '".$spkfind."' group by kodeblok";
    if ($res = mysql_query($str)) {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $optws2 .= "<option value='".$bar->kodeblok."'>".$bar->kodeblok.'</option>';
        }
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }

    if (0 === $no) {
        echo 'error: BA SPK not found.';
    }

    if (9 === $tab) {
        echo $optws;
    }

    if (8 === $tab) {
        echo $optws2;
    }
}

if ('loaddata' === $proses) {
    $limit = 10;
    $page = 0;
    if (isset($_POST['page'])) {
        $page = $_POST['page'];
        if ($page < 0) {
            $page = 0;
        }
    }

    $kamusbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
    $kamussatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
    $kamuskegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
    $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.log_baspk_material order by `notransaksi` desc';
    $query2 = mysql_query($ql2) ;
    while ($jsl = mysql_fetch_object($query2)) {
        $jlhbrs = $jsl->jmlhrow;
    }
    $offset = $page * $limit;
    if ($jlhbrs < $offset) {
        --$page;
    }

    $offset = $page * $limit;
    $no = $offset;
    $slvhc = 'select * from '.$dbname.'.log_baspk_material order by `notransaksi` desc,`kodekegiatan`,`blok`,`tanggal`,`kodebarang` desc limit '.$offset.','.$limit.' ';
    $qlvhc = mysql_query($slvhc) ;
    $user_online = $_SESSION['standard']['userid'];
    while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$rlvhc['notransaksi']."</td>\r\n            <td>".$kamuskegiatan[$rlvhc['kodekegiatan']]."</td>\r\n            <td>".$rlvhc['blok']."</td>\r\n            <td>".$rlvhc['tanggal']."</td>\r\n            <td>".$kamusbarang[$rlvhc['kodebarang']]."</td>\r\n            <td align=right>".$rlvhc['jumlah']."</td>\r\n            <td>".$kamussatuan[$rlvhc['kodebarang']]."</td>\r\n            <td>\r\n            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."','".$rlvhc['kodekegiatan']."','".$rlvhc['blok']."','".$rlvhc['tanggal']."','".$rlvhc['kodebarang']."');\" ></td>";
    }
    echo "\r\n        </tr><tr class=rowheader><td colspan=9 align=center>\r\n        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n        <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n        <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n        </td>\r\n        </tr>";
}

if ('deletedata' === $proses) {
    $nospk = $_POST['nospk'];
    $kegiatan = $_POST['kegiatan'];
    $blok = $_POST['blok'];
    $tanggal = $_POST['tanggal'];
    $kodebarang = $_POST['kodebarang'];
    $where = "notransaksi ='".$nospk."' and kodekegiatan = '".$kegiatan."' and blok = '".$blok."' and tanggal = '".$tanggal."'";
    $sDel = 'delete from '.$dbname.'.log_baspk_material where '.$where." and kodebarang = '".$kodebarang."'";
    if (mysql_query($sDel)) {
        echo '';
    } else {
        echo 'DB Error : '.mysql_error($conn);
    }
}

if (7 === $tab) {
    $nospk = $_POST['nospk'];
    $kegiatan = $_POST['kegiatan'];
    $blok = $_POST['blok'];
    $no = 0;
    $str = 'select * from '.$dbname.".log_baspk where notransaksi = '".$nospk."' and kodeblok = '".$blok."' and kodekegiatan = '".$kegiatan."' limit 1";
    if ($res = mysql_query($str)) {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $optws4 .= $bar->tanggal;
        }
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }

    echo $optws4;
}

if ('insert' === $proses) {
    $nospk = $_POST['nospk'];
    $kegiatan = $_POST['kegiatan'];
    $blok = $_POST['blok'];
    $tanggal = $_POST['tanggal'];
    $kodebarang = $_POST['kodebarang'];
    $jumlah = $_POST['jumlah'];
    $rrr = '';
    if ('' === $nospk) {
        $rrr .= ' No SPK, ';
    }

    if ('' === $jumlah) {
        $rrr .= ' Jumlah, ';
    }

    if ('' === $kodebarang) {
        $rrr .= ' Nama/Kode Barang, barang yang valid akan memunculkan satuan';
    }

    if ('' !== $rrr) {
        echo 'error: Silakan mengisi '.$rrr.'.';
        exit();
    }

    $str = 'select * from '.$dbname.".log_baspk_material\r\n        where notransaksi='".$nospk."' and kodekegiatan = '".$kegiatan."' and blok = '".$blok."' \r\n            and tanggal = '".$tanggal."' and kodebarang = '".$kodebarang."' \r\n        limit 1";
    $sudahada = 0;
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $sudahada = $bar->kodekegiatan;
    }
    if (0 !== $sudahada) {
        echo 'error: data exist.';
        exit();
    }

    $str = 'INSERT INTO '.$dbname.".log_baspk_material (`notransaksi` ,`blok` ,`kodekegiatan` ,`tanggal` ,`kodebarang` ,`jumlah`)\r\n        VALUES ('".$nospk."', '".$blok."', '".$kegiatan."', '".$tanggal."', '".$kodebarang."', '".$jumlah."')        \r\n        ";
    if ($res = mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

?>