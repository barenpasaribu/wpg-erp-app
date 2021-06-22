<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$str = 'select posting from '.$dbname.".sdm_catu where kodeorg='".$_POST['kodeorg']."' \r\n        and periodegaji='".$_POST['periode']."' and posting=1 order by posting desc \r\n        limit 1";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if (1 == $bar->posting) {
        $stat = '1';
    } else {
        $stat = '';
    }
}
if ('' != $stat) {
    exit($stat);
}

switch ($_POST['aksi']) {
    case 'display':
        display($_POST['kodeorg'], $_POST['periode'], $_POST['harga'], $dbname, $conn);

        break;
    case 'simpan':
        display($_POST['kodeorg'], $_POST['periode'], $_POST['harga'], $dbname, $conn);

        break;
    case 'replace':
        display($_POST['kodeorg'], $_POST['periode'], $_POST['harga'], $dbname, $conn);

        break;
    case 'posting':
        posting($_POST['kodeorg'], $_POST['periode'], $_POST['jumlah'], $dbname, $conn);

        break;
}
function display($kodeorg, $periode, $harga, $dbname, $conn)
{
    $tgl1 = '';
    $tgl2 = '';
    $str = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."'\r\n           and periode='".$periode."' and jenisgaji='H'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $tgl1 = str_replace('-', '', $bar->tanggalmulai);
        $tgl2 = str_replace('-', '', $bar->tanggalsampai);
    }
    if ('' == $tgl1 || '' == $tgl2) {
        exit(' Error: Periode penggajian Harian tidak ditemukan/ Daily base payrol period not found');
    }

    $str = "select a.karyawanid,a.namakaryawan,a.kodecatu,a.subbagian,b.tipe,c.keterangan,a.kodecatu,a.tipekaryawan,a.kodejabatan,d.namajabatan\r\n                  from ".$dbname.'.datakaryawan a left join '.$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id                  \r\n                  left join ".$dbname.".sdm_5catuporsi c on a.kodecatu=c.kode\r\n                  left join ".$dbname.".sdm_5jabatan d on a.kodejabatan=d.kodejabatan    \r\n                  where a.lokasitugas='".$kodeorg."' and tipekaryawan!=5 and (a.tanggalkeluar>'".$_POST['periode']."-01' or a.tanggalkeluar is NULL)";
    $res = mysql_query($str);
    $kamusKar = [];
    while ($bar = mysql_fetch_object($res)) {
        if ('BHL' != $bar->tipe) {
            $kamusKar[$bar->karyawanid]['id'] = $bar->karyawanid;
            $kamusKar[$bar->karyawanid]['nama'] = $bar->namakaryawan;
            $kamusKar[$bar->karyawanid]['kodecatu'] = $bar->kodecatu;
            $kamusKar[$bar->karyawanid]['subbagian'] = $bar->subbagian;
            $kamusKar[$bar->karyawanid]['tipekaryawan'] = $bar->tipekaryawan;
            $kamusKar[$bar->karyawanid]['namatipe'] = $bar->tipe;
            $kamusKar[$bar->karyawanid]['kelompok'] = $bar->keterangan;
            $kamusKar[$bar->karyawanid]['kode'] = $bar->kodecatu;
            $kamusKar[$bar->karyawanid]['jabatan'] = $bar->namajabatan;
        }
    }
    $str = 'select kodeorganisasi from '.$dbname.".organisasi where induk='".$kodeorg."' order by kodeorganisasi";
    $res = mysql_query($str);
    $subbagian = [];
    while ($bar = mysql_fetch_object($res)) {
        array_push($subbagian, $bar->kodeorganisasi);
    }
    $sKehadiran = 'select absensi,tanggal,karyawanid from '.$dbname.".kebun_kehadiran_vw \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and unit='".$kodeorg."'";
    $res = mysql_query($sKehadiran);
    while ($bar = mysql_fetch_object($res)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        $kehadiran[$bar->karyawanid][$tgl] = $bar->absensi;
    }
    $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".$kodeorg."' and b.tanggal between '".$tgl1."' and '".$tgl2."'";
    $res = mysql_query($sPrestasi);
    while ($bar = mysql_fetch_object($res)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        $kehadiran[$bar->nik][$tgl] = 'H';
    }
    $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL\r\n            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL";
    $dzres = mysql_query($dzstr);
    while ($bar = mysql_fetch_object($dzres)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        $kehadiran[$bar->nikmandor][$tgl] = 'H';
    }
    $dzstr = 'SELECT tanggal,nikasisten as nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikasisten=c.karyawanid\r\n            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL\r\n            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL";
    $dzres = mysql_query($dzstr);
    while ($bar = mysql_fetch_object($dzres)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        $kehadiran[$bar->nikmandor][$tgl] = 'H';
    }
    $dzstr = 'SELECT tanggal,idkaryawan FROM '.$dbname.".vhc_runhk\r\n            where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '".$kodeorg."%'";
    $dzres = mysql_query($dzstr);
    while ($bar = mysql_fetch_object($dzres)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        $kehadiran[$bar->idkaryawan][$tgl] = 'H';
    }
    $sAbsn = 'select absensi,tanggal,karyawanid,catu from '.$dbname.".sdm_absensidt \r\n                        where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '".$kodeorg."%'\r\n                         and left(absensi,1)='H'";
    $res = mysql_query($sAbsn);
    $kehadiran = [];
    while ($bar = mysql_fetch_object($res)) {
        $tgl = str_replace('-', '', $bar->tanggal);
        if (1 == $bar->catu) {
            $kehadiran[$bar->karyawanid][$tgl] = $bar->absensi;
        } else {
            unset($kehadiran[$bar->karyawanid][$tgl]);
        }
    }
    $hari = dates_inbetween($tgl1, $tgl2);
    foreach ($hari as $ar => $isi) {
        $qwe = date('D', strtotime($isi));
        $tglini = date('Ymd', strtotime($isi));
        if ('Sun' == $qwe) {
            foreach ($kehadiran as $key => $val) {
                $sCek = 'select distinct catu from '.$dbname.".sdm_absensidt \r\n                                   where karyawanid='".$key."' and tanggal='".$tglini."'";
                $qCek = mysql_query($sCek);
                $rCek = mysql_fetch_assoc($qCek);
                if (0 == $rCek['catu']) {
                    unset($kehadiran[$key][$tglini]);
                }
            }
        }
    }
    $jumlahHK = [];
    foreach ($kehadiran as $key => $val) {
        $jumlahHK[$key] = count($kehadiran[$key]);
    }
    $str = 'select kelompok, jumlah as porsi from '.$dbname.".sdm_5catu where kodeorg='".$kodeorg."' and tahun=".substr($periode, 0, 4);
    $porsi = [];
    $res = mysql_query($str);
    if (0 == mysql_num_rows($res)) {
        if ('ID' == $_SESSION['language']) {
            exit('Error:Setup->Natura untuk tahun '.substr($periode, 0, 4).' belum ada, silahkan isi terlebih dahulu');
        }

        exit('Error:Setup->Natura for year '.substr($periode, 0, 4).' not defined, please define first');
    }

    while ($bar = mysql_fetch_object($res)) {
        $porsi[$bar->kelompok] = $bar->porsi;
    }
    $rupiahCatu = [];
    foreach ($jumlahHK as $key => $val) {
        $rupiahCatu[$key] = $jumlahHK[$key] * $porsi[$kamusKar[$key]['kode']] * $harga;
    }
    if ('display' == $_POST['aksi']) {
        echo "<font color=red>Scroll down to save</font>\r\n                    <table class=sortable border=0 cellspacing=1>\r\n                    <thead>\r\n                    <tr class=rowheader>\r\n                    <td>No.</td>\r\n                    <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                    <td>".$_SESSION['lang']['subbagian']."</td>\r\n                    <td>".$_SESSION['lang']['periode']."</td>\r\n                    <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                    <td>".$_SESSION['lang']['tipe']."</td>\r\n                    <td>".$_SESSION['lang']['jabatan']."</td>\r\n                    <td>".$_SESSION['lang']['status']."</td>\r\n                    <td>Ltr/Hk</td>\r\n                    <td>".$_SESSION['lang']['jumlah']." HK</td>\r\n                    <td>".$_SESSION['lang']['hargasatuan']."</td>\r\n                    <td>".$_SESSION['lang']['total']." (Rp)</td>\r\n                    </tr>\r\n                    </thead>\r\n                    <tbody>";
        $no = 0;
        $ttl = 0;
        foreach ($subbagian as $unit => $sub) {
            $SUBTTL = 0;
            foreach ($kamusKar as $key => $val) {
                if ($kamusKar[$key]['subbagian'] == $sub) {
                    ++$no;
                    echo "<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$kodeorg."</td>\r\n                                <td>".$kamusKar[$key]['subbagian']."</td>\r\n                                <td>".$periode."</td>\r\n                                <td>".$kamusKar[$key]['nama']."</td>\r\n                                <td>".$kamusKar[$key]['namatipe']."</td>\r\n                                <td>".$kamusKar[$key]['jabatan']."</td>\r\n                                <td>".$kamusKar[$key]['kode']."</td>\r\n                                <td>".number_format($porsi[$kamusKar[$key]['kode']], 2, '.', ',')."</td>\r\n                                <td align=right>".number_format($jumlahHK[$key], 0, '.', ',')."</td>\r\n                                <td align=right>".number_format($harga, 0, '.', ',')."</td>     \r\n                                <td align=right>".number_format($rupiahCatu[$key], 0, '.', ',')."</td></tr>     \r\n                                ";
                    $ttl += $rupiahCatu[$key];
                    $SUBTTL += $rupiahCatu[$key];
                }
            }
            echo "<tr class=rowcontent>\r\n                            <td colspan=11>Sub Total ".$sub."</td>     \r\n                            <td align=right>".number_format($SUBTTL, 0, '.', ',')."</td></tr>     \r\n                            ";
        }
        $SUBTTL = 0;
        foreach ($kamusKar as $key => $val) {
            if ('' == $kamusKar[$key]['subbagian'] || '0' == $kamusKar[$key]['subbagian']) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$kodeorg."</td>\r\n                                <td>".$kamusKar[$key]['subbagian']."</td>\r\n                                <td>".$periode."</td>\r\n                                <td>".$kamusKar[$key]['nama']."</td>\r\n                                <td>".$kamusKar[$key]['namatipe']."</td>\r\n                                <td>".$kamusKar[$key]['jabatan']."</td>\r\n                                <td>".$kamusKar[$key]['kode']."</td>\r\n                                <td>".number_format($porsi[$kamusKar[$key]['kode']], 2, '.', ',')."</td>\r\n                                <td align=right>".number_format($jumlahHK[$key], 0, '.', ',')."</td>\r\n                                <td align=right>".number_format($harga, 0, '.', ',')."</td>     \r\n                                <td align=right>".number_format($rupiahCatu[$key], 0, '.', ',')."</td></tr>     \r\n                                ";
                $ttl += $rupiahCatu[$key];
                $SUBTTL += $rupiahCatu[$key];
            }
        }
        echo "<tr class=rowcontent>\r\n                            <td colspan=11>Sub Total Kantor</td>     \r\n                            <td align=right>".number_format($SUBTTL, 0, '.', ',').'</td></tr>';
        echo "<tr class=rowheader>\r\n                        <td colspan=11>TOTAL</td>     \r\n                        <td align=right>".number_format($ttl, 0, '.', ',')."</td></tr>     \r\n                        ";
        echo "</tbody>\r\n                    <tfoot>\r\n                    </tfoot>\r\n                    </table>\r\n                    <button onclick=simpanCatu()>".$_SESSION['lang']['save'].'</button>';
    } else {
        if ('simpan' == $_POST['aksi'] || 'replace' == $_POST['aksi']) {
            if ('simpan' == $_POST['aksi']) {
                $str = 'select posting from '.$dbname.".sdm_catu where kodeorg='".$kodeorg."' \r\n                            and periodegaji='".$periode."'  order by posting desc \r\n                            limit 1";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    if ('1' == $bar->posting) {
                        $stat = '1';
                    } else {
                        if ('0' == $bar->posting) {
                            $stat = '0';
                        } else {
                            $stat = '';
                        }
                    }
                }
                if ('' != $stat) {
                    exit($stat);
                }
            }

            $ttl = 0;
            $stsimpan = '';
            foreach ($kamusKar as $key => $val) {
                if (0 < $rupiahCatu[$key]) {
                    if (0 == $no) {
                        $stsimpan = "              \r\n                            insert into ".$dbname.".sdm_catu(\r\n                            kodeorg, \r\n                            subbagian,\r\n                            periodegaji, \r\n                            karyawanid, \r\n                            hargacatu, \r\n                            jumlahhk, \r\n                            catuperhk, \r\n                            totalcatu, \r\n                            jumlahrupiah, \r\n                            posting, \r\n                            updateby)\r\n                            values(\r\n                            '".$kodeorg."',\r\n                            '".$kamusKar[$key]['subbagian']."',    \r\n                            '".$periode."',\r\n                            ".$key.", \r\n                            ".$harga.",\r\n                            ".$jumlahHK[$key].",   \r\n                            ".$porsi[$kamusKar[$key]['kode']].",\r\n                            ".$jumlahHK[$key] * $porsi[$kamusKar[$key]['kode']].", \r\n                            ".$rupiahCatu[$key].",\r\n                                0,\r\n                            ".$_SESSION['standard']['userid']."    \r\n                            )";
                    } else {
                        $stsimpan .= ",(\r\n                            '".$kodeorg."',\r\n                            '".$kamusKar[$key]['subbagian']."',     \r\n                            '".$periode."',\r\n                            ".$key.", \r\n                            ".$harga.",\r\n                            ".$jumlahHK[$key].",   \r\n                            ".$porsi[$kamusKar[$key]['kode']].",\r\n                            ".$jumlahHK[$key] * $porsi[$kamusKar[$key]['kode']].", \r\n                            ".$rupiahCatu[$key].",\r\n                                0,\r\n                            ".$_SESSION['standard']['userid']."    \r\n                            )";
                    }

                    ++$no;
                }
            }
            $str = 'delete from '.$dbname.".sdm_catu where kodeorg='".$kodeorg."' and periodegaji='".$periode."'";
            mysql_query($str);
            if (mysql_query($stsimpan)) {
            } else {
                echo ' Error: '.mysql_error($conn).$stsimpan;
            }
        }
    }
}

function posting($kodeorg, $periode, $jumlah, $dbname, $conn)
{
    $tgl1 = '';
    $tgl2 = '';
    $str = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."'\r\n           and periode='".$periode."' and jenisgaji='H'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $tgl1 = str_replace('-', '', $bar->tanggalmulai);
        $tgl2 = str_replace('-', '', $bar->tanggalsampai);
    }
    if ('' == $tgl1 || '' == $tgl2) {
        exit(' Error: Periode penggajian Harian tidak ditemukan/ Daily base payrol period not found');
    }

    $str = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$kodeorg."' \r\n             and periode='".$periode."' and tutupbuku=0";
    $res = mysql_query($str);
    if (0 == mysql_num_rows($res)) {
        exit(' Error: Sorry, accounting period is not active on choson period');
    }

    $str = 'select sudahproses from '.$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."' \r\n             and periode='".$periode."' and sudahproses=0";
    $res = mysql_query($str);
    if (0 < mysql_num_rows($res)) {
        exit(' Error: Sorry, input for presence, CARLOG and Foreman daoly book not yet close, please make sure for those transaction by confirmation via Setu->Periode Penggajian unit');
    }

    $str = 'select tipe from '.$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
    $res = mysql_query($str);
    $tipe = 'KANWIL';
    while ($bar = mysql_fetch_object($res)) {
        $tipe = $bar->tipe;
    }
    if ('KEBUN' == $tipe) {
        $debet = '';
        $kredit = '';
        $nojurnal = str_replace('-', '', $periode).'28/'.$kodeorg.'/CT01/001';
        $str = 'select noakundebet,noakunkredit from '.$dbname.".keu_5parameterjurnal where jurnalid='CT01'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $debet = $bar->noakundebet;
            $kredit = $bar->noakunkredit;
        }
        if ('' == $debet || '' == $kredit) {
            exit('Error: Journal parameter for CT01 not defined, contact administrator');
        }

        $kodejurnal = 'CT01';
        $byumum = 0;
        $str = 'select sum(jumlahrupiah) as byumum from '.$dbname.".sdm_catu where periodegaji='".$periode."' \r\n                        and kodeorg='".$kodeorg."' and subbagian=''";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $byumum = $bar->byumum;
        }
        $bytanaman = $jumlah - $byumum;
        $dataRes = [];
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $jumlah, 'totalkredit' => $jumlah * -1, 'amountkoreksi' => '0', 'noreferensi' => 'CT01', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $noUrut = 1;
        if (0 < $byumum) {
            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $byumum, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            ++$noUrut;
        }

        $akunpanen = '';
        $str = 'select  noakundebet from '.$dbname.".keu_5parameterjurnal where jurnalid='PNN01'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $akunpanen = $bar->noakundebet;
        }
        if ('' == $akunpanen) {
            exit(' Error: Account for harvesting not defined in journal parameter PNN01');
        }

        $sAbsn = 'select distinct kodeorg from '.$dbname.".kebun_prestasi_vw \r\n                                  where tanggal between  '".$tgl1."' and '".$tgl2."' and unit ='".$kodeorg."'";
        $respanen = mysql_query($sAbsn);
        $jml_baris = mysql_num_rows($respanen);
        $sAbsn = 'select distinct noakun,kodeorg,kodekegiatan from '.$dbname.".kebun_perawatan_vw \r\n                                  where tanggal between  '".$tgl1."' and '".$tgl2."' and unit ='".$kodeorg."'";
        $resrawat = mysql_query($sAbsn);
        $jml_baris += mysql_num_rows($resrawat);
        if (0 == $jml_baris && 0 < $bytanaman) {
            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $bytanaman, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            ++$noUrut;
        } else {
            $biayaperblok = $bytanaman / $jml_baris;
        }

        if (0 < $biayaperblok && 0 < $jml_baris) {
            while ($bar = mysql_fetch_object($respanen)) {
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $akunpanen, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $biayaperblok, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => $akunpanen.'01', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0'];
                ++$noUrut;
            }
            while ($bar = mysql_fetch_object($resrawat)) {
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $bar->noakun, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $biayaperblok, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => $bar->kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0'];
                ++$noUrut;
            }
        }

        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $kredit, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => -1 * $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
    } else {
        if ('TRAKSI' == $tipe) {
            $debet = '';
            $kredit = '';
            $nojurnal = str_replace('-', '', $periode).'28/'.$kodeorg.'/CT03/001';
            $str = 'select noakundebet,noakunkredit from '.$dbname.".keu_5parameterjurnal where jurnalid='CT03'";
            $res = mysql_query($str);
            while ($bar = mysql_fetch_object($res)) {
                $debet = $bar->noakundebet;
                $kredit = $bar->noakunkredit;
            }
            if ('' == $debet || '' == $kredit) {
                exit('Error: Journal parameter for CT03 (Traksi) not defined, contact administrator');
            }

            $kodejurnal = 'CT03';
            $str = 'select distinct kodevhc from '.$dbname.".vhc_runht where tanggal between  '".$tgl1."' and '".$tgl2."' \r\n                     and kodeorg ='".$kodeorg."'";
            $res = mysql_query($str);
            $jml_baris = mysql_num_rows($res);
            $dataRes = [];
            $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $jumlah, 'totalkredit' => $jumlah * -1, 'amountkoreksi' => '0', 'noreferensi' => 'CT03', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
            $noUrut = 1;
            if (0 == $jml_baris) {
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT03', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
            } else {
                for ($byperkendaraan = $jumlah / $jml_baris; $bar = mysql_fetch_object($res); ++$noUrut) {
                    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $byperkendaraan, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT03', 'noaruskas' => '', 'kodevhc' => $bar->kodevhc, 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                }
            }

            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $kredit, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => -1 * $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT03', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            ++$noUrut;
        } else {
            if ('PABRIK' == $tipe) {
                $debet = '';
                $kredit = '';
                $nojurnal = str_replace('-', '', $periode).'28/'.$kodeorg.'/CT04/001';
                $str = 'select noakundebet,noakunkredit from '.$dbname.".keu_5parameterjurnal where jurnalid='CT04'";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $debet = $bar->noakundebet;
                    $kredit = $bar->noakunkredit;
                }
                if ('' == $debet || '' == $kredit) {
                    exit('Error: Journal parameter  CT04 (PKS) not defined');
                }

                $kodejurnal = 'CT04';
                $byumum = 0;
                $str = 'select sum(jumlahrupiah) as byumum from '.$dbname.".sdm_catu where periodegaji='".$periode."' \r\n                        and kodeorg='".$kodeorg."' and subbagian=''";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $byumum = $bar->byumum;
                }
                $bystasiun = $jumlah - $byumum;
                $str = 'select kodeorganisasi from '.$dbname.".organisasi where tipe='STATION' \r\n                     and induk ='".$kodeorg."'";
                $res = mysql_query($str);
                $jml_baris = mysql_num_rows($res);
                $dataRes = [];
                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $jumlah, 'totalkredit' => $jumlah * -1, 'amountkoreksi' => '0', 'noreferensi' => 'CT04', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                $noUrut = 1;
                if (0 == $jml_baris) {
                    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT04', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                    ++$noUrut;
                } else {
                    if (0 < $byumum) {
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $byumum, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT04', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                        ++$noUrut;
                    }

                    for ($byperstasiun = $bystasiun / $jml_baris; $bar = mysql_fetch_object($res); ++$noUrut) {
                        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $byperstasiun, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT04', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorganisasi, 'revisi' => '0'];
                    }
                }

                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $kredit, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => -1 * $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT04', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
            } else {
                $debet = '';
                $kredit = '';
                $nojurnal = str_replace('-', '', $periode).'28/'.$kodeorg.'/CT01/001';
                $str = 'select noakundebet,noakunkredit from '.$dbname.".keu_5parameterjurnal where jurnalid='CT01'";
                $res = mysql_query($str);
                while ($bar = mysql_fetch_object($res)) {
                    $debet = $bar->noakundebet;
                    $kredit = $bar->noakunkredit;
                }
                if ('' == $debet || '' == $kredit) {
                    exit('Error: Journal parameter CT01 (Kebun) not defined');
                }

                $kodejurnal = 'CT01';
                $dataRes = [];
                $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $jumlah, 'totalkredit' => $jumlah * -1, 'amountkoreksi' => '0', 'noreferensi' => 'CT01', 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
                $noUrut = 1;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $debet, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
                $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => str_replace('-', '', $periode).'28', 'nourut' => $noUrut, 'noakun' => $kredit, 'keterangan' => 'Catu Beras -'.$periode, 'jumlah' => -1 * $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kodeorg, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'CT01', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
                ++$noUrut;
            }
        }
    }

    $headErr = '';
    $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
    if (!mysql_query($insHead)) {
        $headErr .= 'Insert Header Error : '.mysql_error()."\n";
    }

    if ('' == $headErr) {
        $detailErr = '';
        foreach ($dataRes['detail'] as $row) {
            $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
            if (!mysql_query($insDet)) {
                $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

                break;
            }
        }
        if ('' == $detailErr) {
            $str = 'update '.$dbname.".sdm_catu set posting=1 where kodeorg='".$kodeorg."' and periodegaji='".$periode."'";
            mysql_query($str);
        } else {
            echo $detailErr;
            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
            if (!mysql_query($RBDet)) {
                echo 'Rollback Delete Header Error : '.mysql_error();
                exit();
            }
        }
    } else {
        echo $headErr;
        exit();
    }
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
    if ($date1 == $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>