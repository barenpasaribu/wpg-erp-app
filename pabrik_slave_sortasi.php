<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/nangkoelib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$tgl = tanggalsystem($_POST['tgl']);
$jmlh = $_POST['jmlh'];
$kdFraksi = $_POST['kdFraksi'];
$noTiket = $_POST['noTiket'];
$lokasi = $_SESSION['empl']['lokasitugas'];
$jmlhJJg = $_POST['jmlhJJg'];
$persenBrnd = $_POST['persenBrnd'];
$kgPtngan = $_POST['kgPtngan'];
if ('EN' === $_SESSION['language']) {
    $zz = 'keterangan1 as keterangan';
} else {
    $zz = 'keterangan';
}

switch ($proses) {
    case 'getTiket':
        $thn = substr($tgl, 0, 4);
        $bln = substr($tgl, 4, 2);
        $hari = substr($tgl, 6, 2);
        $tanggal = $thn.'-'.$bln.'-'.$hari;
        $optNotiket = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sTim = 'select notransaksi from '.$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
        $qTim = mysql_query($sTim);
        $row = mysql_num_rows($qTim);
        if (0 < $row) {
            while ($rTim = mysql_fetch_assoc($qTim)) {
                if ('0' === $noTiket) {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].'>'.$rTim['notransaksi'].'</option>';
                } else {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].' '.(($rTim['notransaksi'] === $noTiket ? 'selected' : '')).'>'.$rTim['notransaksi'].'</option>';
                }
            }
            echo $optNotiket;

            break;
        }

        echo 'warning: Weighbridge data is empty';
        exit();
    case 'getData':
        $sDt = 'select * from '.$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$kdFraksi."'";
        $qDt = mysql_query($sDt);
        $rDt = mysql_fetch_assoc($qDt);
        $sTgl = 'select tanggal from '.$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo $rDt['notiket'].'###'.$rDt['kodefraksi'].'###'.$rDt['jumlah'].'###'.tanggalnormal($rTgl['tanggal']);

        break;
    case 'LoadData':
        echo "\r\n                    <table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['noTiket']."</td>\r\n                ";
        $sFraksi = 'select kode,'.$zz.',type from '.$dbname.'.pabrik_5fraksi order by kode asc';
        $qFraksi = mysql_query($sFraksi);
        while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
            echo '<td>'.$rFraksi['keterangan'].' '.(('' !== $rFraksi['type'] ? '('.$rFraksi['type'].')' : '')).'</td> ';
        }
        echo '<td>'.$_SESSION['lang']['sortasi'].'(JJG)</td><td> '.$_SESSION['lang']['potongankg']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead>\r\n                <tbody>";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select notiket from '.$dbname.'.pabrik_sortasi group by `notiket` order by `notiket` desc';
        $query2 = mysql_query($ql2);
        $jsl = mysql_num_rows($query2);
        $jlhbrs = $jsl;
        $sNotiket = 'select notiket from '.$dbname.'.pabrik_sortasi group by `notiket` order by `notiket` desc limit '.$offset.','.$limit.' ';
        $qNotiket = mysql_query($sNotiket);
        $a = 0;
        while ($rNotiket = mysql_fetch_assoc($qNotiket)) {
            ++$no;
            echo '<tr class=rowcontent><td>'.$no.'</td>';
            echo '<td>'.$rNotiket['notiket'].'</td>';
            $sFraksi = 'select kode from '.$dbname.'.pabrik_5fraksi order by kode asc';
            $qFraksi = mysql_query($sFraksi);
            $sJjg = 'select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi2 from '.$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
            $qJjg = mysql_query($sJjg);
            $rJjg = mysql_fetch_assoc($qJjg);
            while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
                $sMax = 'select notiket,jumlah,kodefraksi from '.$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."' and kodefraksi='".$rFraksi['kode']."'";
                $qMax = mysql_query($sMax);
                $rMax = mysql_fetch_assoc($qMax);
                if ($rFraksi['kode'] === $rMax['kodefraksi']) {
                    echo "<td align=right id='".$rFraksi['kode'].'##'.$rMax['notiket']."' onclick=\"editDetHead('".$rNotiket['notiket']."','".tanggalnormal(substr($rJjg['tanggal'], 0, 10))."')\" style=\"cursor:pointer\" >".number_format($rMax['jumlah'], 2).'</td>';
                } else {
                    echo '<td align=right>'.number_format($rMax['jumlah'], 2).'</td>';
                }
            }
            echo '<td align=right>'.number_format($rJjg['jjgsortasi'], 0).'</td>';
            echo '<td align=right>'.number_format($rJjg['kgpotsortasi2'], 2).'</td>';
            echo "<td>\r\n\r\n<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$rNotiket['notiket']."');\"></td></tr>";
        }
        echo "\r\n                <tr><td colspan=17 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tbody></table>';

        break;
    case 'insert':
        if ('' === $noTiket) {
            echo 'warning:No Tiket Tidak boleh Kosong';
            exit();
        }

        $kdFraksi = $_POST['kdFraksi'];
        $isiData = $_POST['isiData'];
        foreach ($kdFraksi as $rt => $isi) {
            if ('' === $isiData[$isi]) {
                $isiData[$isi] = 0;
            }

            $sCek = 'select notiket,kodefraksi from '.$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$isi."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_num_rows($qCek);
            if ($rCek < 1) {
                $sIns = 'insert into '.$dbname.".pabrik_sortasi (notiket, kodefraksi, jumlah) values ('".$noTiket."','".$isi."','".$isiData[$isi]."')";
                if (mysql_query($sIns)) {
                    $sCekDt = 'select jjgsortasi,persenBrondolan from '.$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
                    $qCekDt = mysql_query($sCekDt);
                    $rCekDt = mysql_fetch_assoc($qCekDt);
                    if (0 === $rCekDt['jjgsortasi'] || 0 === $rCekDt['persenBrondolan']) {
                        $sDt = 'update '.$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi2='".$kgPtngan."' where notransaksi='".$noTiket."'";
                        if (mysql_query($sDt)) {
                            echo '';
                        } else {
                            echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                        }
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                $sIns = 'update '.$dbname.".pabrik_sortasi set kodefraksi='".$isi."', jumlah='".$isiData[$isi]."' where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                if (mysql_query($sIns)) {
                    $sDt = 'update '.$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi2='".$kgPtngan."' where notransaksi='".$noTiket."'";
                    if (mysql_query($sDt)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                }
            }
        }

        break;
    case 'update':
        if ('' === $noTiket) {
            echo 'warning:No Tiket Tidak boleh Kosong';
            exit();
        }

        $kdFraksi = $_POST['kdFraksi'];
        $isiData = $_POST['isiData'];
        foreach ($kdFraksi as $rt => $isi) {
            if ('' === $isiData[$isi]) {
                $isiData[$isi] = 0;
            }

            $sCek = 'select notiket,kodefraksi from '.$dbname.".pabrik_sortasi where notiket='".$noTiket."' and kodefraksi='".$isi."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_num_rows($qCek);
            if (0 < $rCek) {
                $sIns = 'update '.$dbname.".pabrik_sortasi set kodefraksi='".$isi."', jumlah='".$isiData[$isi]."' where notiket='".$noTiket."' and kodefraksi='".$isi."'";
                if (mysql_query($sIns)) {
                    $sDt = 'update '.$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi2='".$kgPtngan."' where notransaksi='".$noTiket."'";
                    if (mysql_query($sDt)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                }
            } else {
                $sIns = 'insert into '.$dbname.".pabrik_sortasi (notiket, kodefraksi, jumlah) values ('".$noTiket."','".$isi."','".$isiData[$isi]."')";
                if (mysql_query($sIns)) {
                    $sDt = 'update '.$dbname.".pabrik_timbangan set jjgsortasi='".$jmlhJJg."',persenBrondolan='".$persenBrnd."',kgpotsortasi2='".$kgPtngan."' where notransaksi='".$noTiket."'";
                    if (mysql_query($sDt)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.$sDt.'__'.mysql_error($conn);
                }
            }
        }

        break;
    case 'delData':
        $where = " notiket='".$noTiket."'";
        $sDel = 'delete from '.$dbname.'.pabrik_sortasi where  '.$where.'';
        if (mysql_query($sDel)) {
            $sUpd = 'update '.$dbname.".pabrik_timbangan set jjgsortasi=0,persenBrondolan=0 where notransaksi='".$noTiket."'";
            if (mysql_query($sUpd)) {
                echo '';
            } else {
                echo 'DB Error : '.$sUpd.'__'.mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cariData':
        echo "<table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['noTiket']."</td>\r\n                ";
        $sFraksi = 'select kode,'.$zz.',type from '.$dbname.'.pabrik_5fraksi order by kode asc';
        $qFraksi = mysql_query($sFraksi);
        while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
            echo '<td>'.$rFraksi['keterangan'].' '.(('' !== $rFraksi['type'] ? '('.$rFraksi['type'].')' : '')).'</td> ';
        }
        echo '<td>'.$_SESSION['lang']['sortasi'].'(JJG)</td><td>% '.$_SESSION['lang']['brondolan']."</td>\r\n                <td>Action</td>\r\n                </tr>\r\n                </thead>\r\n                <tbody>";
        if ('' !== $noTiket) {
            $where = "where notiket like '%".$noTiket."%'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select notiket from '.$dbname.'.pabrik_sortasi  '.$where.' group by `notiket` order by `notiket` desc ';
        $query2 = mysql_query($ql2);
        $jsl = mysql_num_rows($query2);
        $jlhbrs = $jsl;
        $sNotiket = 'select notiket from '.$dbname.'.pabrik_sortasi  '.$where.' group by `notiket` order by `notiket` desc  limit '.$offset.','.$limit.' ';
        $qNotiket = mysql_query($sNotiket);
        $a = 0;
        while ($rNotiket = mysql_fetch_assoc($qNotiket)) {
            ++$no;
            echo '<tr class=rowcontent><td>'.$no.'</td>';
            echo '<td>'.$rNotiket['notiket'].'</td>';
            $sFraksi = 'select kode from '.$dbname.'.pabrik_5fraksi order by kode asc';
            $qFraksi = mysql_query($sFraksi);
            $sJjg = 'select jjgsortasi,tanggal,persenBrondolan from '.$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
            $qJjg = mysql_query($sJjg);
            $rJjg = mysql_fetch_assoc($qJjg);
            while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
                $sMax = 'select notiket,jumlah,kodefraksi from '.$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."' and kodefraksi='".$rFraksi['kode']."'";
                $qMax = mysql_query($sMax);
                $rMax = mysql_fetch_assoc($qMax);
                if ($rFraksi['kode'] === $rMax['kodefraksi']) {
                    echo "<td align=right id='".$rFraksi['kode'].'##'.$rMax['notiket']."' onclick=\"editDetHead('".$rNotiket['notiket']."','".tanggalnormal(substr($rJjg['tanggal'], 0, 10))."')\" style=\"cursor:pointer\" >".number_format($rMax['jumlah'], 2).'</td>';
                } else {
                    echo '<td align=right>'.number_format($rMax['jumlah'], 2).'</td>';
                }
            }
            echo '<td align=right>'.number_format($rJjg['jjgsortasi'], 2).'</td>';
            echo '<td align=right>'.number_format($rJjg['persenBrondolan'], 2).'</td>';
            echo "<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$rNotiket['notiket']."');\"></td></tr>";
        }
        echo "\r\n                <tr><td colspan=17 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        echo '</tbody></table>';

        break;
    case 'getJenjang':
        $sGet = 'select jumlahtandan1 from '.$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
        $qGet = mysql_query($sGet);
        $rGet = mysql_fetch_assoc($qGet);
        echo $rGet['jumlahtandan1'];

        break;
    case 'createTable':
        $str = 'select * from '.$dbname.".pabrik_5pot_fraksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodefraksi";
        $res = mysql_query($str);
        $resx = mysql_query($str);
        echo "<table class=sortable border=0 cellspacing=1>\r\n                         <thead><tr class=rowheader><td>Kode Fraksi</td><td>Netto</td><td>BJR</td>";
        while ($barf = mysql_fetch_object($res)) {
            echo '<td width=50px align=center>'.$barf->kodefraksi.'</td>';
        }
        echo "</tr></thead>\r\n                         <tbody><tr class=rowcontent><td>CODE * 100(%)</td><td id=nettox></td><td id=bjrx></td>";
        while ($barf = mysql_fetch_object($resx)) {
            echo '<td align=center id=pot'.$barf->kodefraksi.'>'.$barf->potongan.'</td>';
        }
        echo "</tr></tbody>\r\n                         <tfoot></tfoot></table>";
        $thn = substr($tgl, 0, 4);
        $bln = substr($tgl, 4, 2);
        $hari = substr($tgl, 6, 2);
        $tanggal = $thn.'-'.$bln.'-'.$hari;
        $optNotiket = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sTim = 'select notransaksi, nokendaraan from '.$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
        $qTim = mysql_query($sTim);
        $row = mysql_num_rows($qTim);
        if (0 < $row) {
            while ($rTim = mysql_fetch_assoc($qTim)) {
                if ('0' === $noTiket) {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].'>'.$rTim['notransaksi'].' - '.$rTim['nokendaraan'].'</option>';
                } else {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].' '.(($rTim['notransaksi'] == $noTiket ? 'selected' : '')).'>'.$rTim['notransaksi'].' - '.$rTim['nokendaraan'].'</option>';
                }
            }
        }

        $table .= "<table id='ppDetailTable'>";
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<td>'.$_SESSION['lang']['noTiket'].'</td><td>'.$_SESSION['lang']['sortasi'].'(JJG)</td>';
        $qHead = 'select distinct kode,'.$zz.' from '.$dbname.'.pabrik_5fraksi  order by kode asc';
        $zd = mysql_query($qHead);
        $rHead = fetchData($qHead);
        foreach ($rHead as $row => $isi) {
            $table .= '<td>'.$isi['keterangan'].'</td>';
        }
        $table .= '<td>'.$_SESSION['lang']['potongankg'].'</td><td>Action</td></tr>';
        $table .= '</thead><tbody>';
        $table .= "<tr class=rowcontent><td><select style='width:80px;' id=noTkt name=noTkt onchange=getNetto(this.options[this.selectedIndex].value)>".$optNotiket.'</select></td>';
        $table .= "<td><input type=text class=myinputtextnumber style='width:65px;' id=jmlhJJg  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0  onblur=hitungBJR(this.value,".mysql_num_rows($zd).')></td>';
        foreach ($rHead as $row2 => $isi2) {
            ++$a;
            $arr .= '##'.$isi2['kode'];
            $table .= '<td align=right id=fraksi_'.$a.' value='.$isi2['kode'].">\r\n                    <input type=text class=myinputtextnumber style='width:65px;' id=inputan_".$a.' name=frak'.$isi2['kode']." onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0 onblur=hitungPotongan(this.value,'".$isi2['kode']."',".mysql_num_rows($zd).')></td>';
        }
        $table .= "<td><input type=text class=myinputtextnumber style='width:65px;' id=kgPtngan disabled  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value=0  /></td>";
        $table .= "<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail('".$a."')\" src='images/save.png'/></td>";
        $table .= '</tr></tbody></table><input type=hidden id=jmlhBaris value='.$a.' />';
        echo $table;

        break;
    case 'EditData':
        $thn = substr($tgl, 0, 4);
        $bln = substr($tgl, 4, 2);
        $hari = substr($tgl, 6, 2);
        $tanggal = $thn.'-'.$bln.'-'.$hari;
        $optNotiket = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sTim = 'select notransaksi, nokendaraan from '.$dbname.".pabrik_timbangan where substr(tanggal,1,10) = '".$tanggal."' and  kodebarang='40000003'";
        $qTim = mysql_query($sTim);
        $row = mysql_num_rows($qTim);
        if (0 < $row) {
            while ($rTim = mysql_fetch_assoc($qTim)) {
                if ($noTiket == '') {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].'>'.$rTim['notransaksi'].'- '.$rTim['nokendaraan'].'</option>';
                } else {
                    $optNotiket .= '<option value='.$rTim['notransaksi'].' '.(($rTim['notransaksi'] == $noTiket ? 'selected' : '')).'>'.$rTim['notransaksi'].'- '.$rTim['nokendaraan'].'</option>';
                }
            }
        }

        $sJjg = 'select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi2,beratbersih from '.$dbname.".pabrik_timbangan where notransaksi='".$noTiket."'";
        $qJjg = mysql_query($sJjg);
        $rJjg = mysql_fetch_assoc($qJjg);
        $str = 'select * from '.$dbname.'.pabrik_5pot_fraksi order by kodefraksi';
        $res = mysql_query($str);
        $resx = mysql_query($str);
        echo "<table class=sortable border=0 cellspacing=1>\r\n                         <thead><tr class=rowheader><td>Kode Fraksi</td><td>Netto</td><td>BJR</td>";
        while ($barf = mysql_fetch_object($res)) {
            echo '<td width=50px align=center>'.$barf->kodefraksi.'</td>';
        }
        echo "</tr></thead>\r\n                         <tbody><tr class=rowcontent><td>Standar Potongan*100(%)</td><td id=nettox>".$rJjg['beratbersih'].'</td><td id=bjrx>'.number_format($rJjg['beratbersih'] / $rJjg['jjgsortasi'], 2, '.', '').'</td>';
        while ($barf = mysql_fetch_object($resx)) {
            echo '<td align=center id=pot'.$barf->kodefraksi.'>'.$barf->potongan.'</td>';
        }
        echo "</tr></tbody>\r\n                         <tfoot></tfoot></table>";
        $table .= "<table id='ppDetailTable'>";
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<td>'.$_SESSION['lang']['noTiket'].'</td><td>'.$_SESSION['lang']['sortasi'].'(JJG)</td>';
        $qHead = 'select distinct kode,'.$zz.' from '.$dbname.'.pabrik_5fraksi order by kode asc';
        $zd = mysql_query($qHead);
        $rHead = fetchData($qHead);
        foreach ($rHead as $row => $isi) {
            $table .= '<td>'.$isi['keterangan'].'</td>';
            $brs++;
        }
        $table .= '<td>KG Potongan</td><td>Action</td></tr>';
        $table .= '</thead><tbody>';
        $table .= "<tr class=rowcontent><td><select style='width:80px;' id=noTkt name=noTkt disabled>".$optNotiket.'</select></td>';
        $table .= "<td><input type=text class=myinputtextnumber style='width:65px;' id=jmlhJJg  onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value='".$rJjg['jjgsortasi']."'  onblur=hitungBJR(this.value,".mysql_num_rows($zd).')></td>';
        $qData = 'select * from '.$dbname.".pabrik_sortasi where notiket='".$noTiket."' order by kodefraksi asc";
        echo $qData;
        $rData = fetchData($qData);
        foreach ($rData as $brs => $dt) {
            $listData[$dt['kodefraksi']] = $dt['jumlah'];
        }
        foreach ($rHead as $row2 => $isi2) {
            ++$a;
            if ('' === $listData[$isi2['kode']]) {
                $listData[$isi2['kode']] = 0;
            }

            $table .= '<td align=right id=fraksi_'.$a.' value='.$isi2['kode'].">\r\n                    <input type=text class=myinputtextnumber style='width:65px;' id=inputan_".$a.' onkeypress="return angka_doang(event)" size="10" maxlength="4" value='.$listData[$isi2['kode']]." onblur=hitungPotongan(this.value,'".$isi2['kode']."',".mysql_num_rows($zd).')></td>';
        }
        $table .= "<td><input type=text class=myinputtextnumber style='width:65px;' id=kgPtngan disabled onkeypress=\"return angka_doang(event)\" size=\"10\" maxlength=\"4\" value='".$rJjg['kgpotsortasi2']."'  /></td>";
        $table .= "<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail('".$a."')\" src='images/save.png'/></td>";
        $table .= '</tr></tbody></table><input type=hidden id=jmlhBaris value='.$a.' />';
        echo $table;

        break;
    case 'loadDataDetail':
        echo "<div style=overflow:auto;>\r\n                    <table cellspacing=1 border=0 class=sortable>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>No.</td>\r\n                <td>".$_SESSION['lang']['noTiket']."</td>\r\n                ";
        $thn = substr($tgl, 0, 4);
        $bln = substr($tgl, 4, 2);
        $dt = substr($tgl, 6, 2);
        $tanggal = $thn.'-'.$bln.'-'.$dt;
        $qHead = 'select distinct kode,'.$zz.' from '.$dbname.'.pabrik_5fraksi order by kode asc';
        $rHead = fetchData($qHead);
        $brs = count($rHead);
        foreach ($rHead as $row => $isi) {
            echo '<td>'.$isi['keterangan'].'</td>';
        }
        echo '<td>'.$_SESSION['lang']['sortasi'].'(JJG)</td><td>% '.$_SESSION['lang']['brondolan'].'</td><td>%'.$_SESSION['lang']['potongankg'].'</td><td>Action</td></tr></thead><tbody>';
        $qData = 'select * from '.$dbname.'.pabrik_sortasi a left join '.$dbname.".pabrik_timbangan b on a.notiket=b.notransaksi \r\n                    where substr(b.tanggal,1,10) = '".$tanggal."'   ";
        $rData = fetchData($qData);
        foreach ($rData as $brs => $dt) {
            $listData[$dt['notiket']][$dt['kodefraksi']] = $dt['jumlah'];
        }
        $sNotiket = 'select notiket from '.$dbname.'.pabrik_sortasi a left join '.$dbname.".pabrik_timbangan b on a.notiket=b.notransaksi \r\n                    where substr(b.tanggal,1,10)= '".$tanggal."' group by `notiket` order by `notiket`  ";
        $qNotiket = mysql_query($sNotiket);
        while ($rNotiket = mysql_fetch_assoc($qNotiket)) {
            ++$no;
            $sJjg = 'select jjgsortasi,tanggal,persenBrondolan,kgpotsortasi2 from '.$dbname.".pabrik_timbangan where notransaksi='".$rNotiket['notiket']."'";
            $qJjg = mysql_query($sJjg);
            $rJjg = mysql_fetch_assoc($qJjg);
            echo "<tr class=rowcontent onclick=\"editDet('".$rNotiket['notiket']."','".tanggalnormal(substr($rJjg['tanggal'], 0, 10))."');\" style=\"cursor:pointer\"><td>".$no.'</td>';
            echo '<td>'.$rNotiket['notiket'].'</td>';
            $sKdFrak = 'select kodefraksi from '.$dbname.".pabrik_sortasi where notiket='".$rNotiket['notiket']."'";
            $rKdFrak = fetchData($sKdFrak);
            foreach ($rHead as $row2 => $isi2) {
                if ('' === $listData[$rNotiket['notiket']][$isi2['kode']]) {
                    $listData[$rNotiket['notiket']][$isi2['kode']] = 0;
                }

                echo '<td  align=right>'.number_format($listData[$rNotiket['notiket']][$isi2['kode']], 2).'</td>';
            }
            echo '<td align=right>'.number_format($rJjg['jjgsortasi'], 2).'</td>';
            echo '<td align=right>'.number_format($rJjg['persenBrondolan'], 2).'</td><td align=right>'.number_format($rJjg['kgpotsortasi2'], 2)."</td><td>\r\n                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDet('".$rNotiket['notiket']."');\"></td></tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'getNetto':
        $str = 'select beratbersih,bjr from '.$dbname.".pabrik_timbangan where notransaksi='".$_POST['noticket']."'";
        $res = mysql_query($str);
        $netto = 0;
        while ($bar = mysql_fetch_object($res)) {
            $netto = $bar->beratbersih;
            $bjr = $bar->bjr;
        }
        echo $netto.'####'.number_format($bjr, 2);

        break;
    default:
        break;
}

?>