<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$kodept = $_POST['kodept'];
$kodeunit = $_POST['kodeunit'];
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n    where tipe in('KEBUN','PABRIK','GUDANG','GUDANGTEMP','TRAKSI','KANWIL') or (tipe='HOLDING' and length(kodeorganisasi)=4)\r\n    order by kodeorganisasi";
$res = mysql_query($str);
$kamus = [];
while ($bar = mysql_fetch_object($res)) {
    $kamus[$bar->kodeorganisasi] = $bar->namaorganisasi;
}
$str = 'select kodeorganisasi from '.$dbname.".organisasi\r\n    where induk = '".$kodeunit."' and tipe like 'gudang%'\r\n    order by kodeorganisasi";
$res = mysql_query($str);
$anak = [];
while ($bar = mysql_fetch_object($res)) {
    $anak[$bar->kodeorganisasi] = $bar->kodeorganisasi;
}
$jumlahunit = 0;
$str = 'select kodeorganisasi from '.$dbname.".organisasi \r\n    where induk='".$kodept."' and kodeorganisasi like '".$kodeunit."%' and tipe = 'HOLDING'\r\n    order by tipe desc";
$res = mysql_query($str);
$unit = [];
while ($bar = mysql_fetch_object($res)) {
    $unit[$bar->kodeorganisasi] = $bar->kodeorganisasi;
    ++$jumlahunit;
}
$str = 'select kodeorganisasi from '.$dbname.".organisasi \r\n    where induk='".$kodept."' and kodeorganisasi like '".$kodeunit."%' and tipe != 'HOLDING'\r\n    order by tipe desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $unit[$bar->kodeorganisasi] = $bar->kodeorganisasi;
    ++$jumlahunit;
}
$arr = [];
$str1 = 'select * from '.$dbname.'.keu_setup_watu_tutup order by periode desc, kodeorg';
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    $arr[$bar1->periode][$bar1->kodeorg]['username'] = $bar1->username;
    $arr[$bar1->periode][$bar1->kodeorg]['waktu'] = $bar1->waktu;
}
$no = 1;
$str = 'select * from '.$dbname.'.setup_periodeakuntansi order by periode desc, kodeorg';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $periode[$baris->periode] = $baris->periode;
    $tutup[$baris->periode][$baris->kodeorg] = $baris->tutupbuku;
    $waktu[$baris->periode][$baris->kodeorg] = $arr[$baris->periode][$baris->kodeorg]['waktu'];
    $pelaku[$baris->periode][$baris->kodeorg] = $arr[$baris->periode][$baris->kodeorg]['username'];
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.keu_kasbankht group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $kasbank[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.keu_kasbankht where posting = 1 group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $kasbankp[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.kebun_aktifitas group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $bkm[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.kebun_aktifitas where jurnal = 1 group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $bkmp[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.vhc_runht group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $traksi[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.vhc_runht where posting = 1 group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $traksip[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.vhc_penggantianht group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $traksi[$baris->periode][$baris->kodeorg] += $baris->jumlah;
}
$str = 'select kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.vhc_penggantianht where posting = 1 group by kodeorg, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $traksip[$baris->periode][$baris->kodeorg] += $baris->jumlah;
}
$str = 'select substr(kodeblok,1,4) as kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.log_baspk group by substr(kodeblok,1,4), substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $bapp[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select substr(kodeblok,1,4) as kodeorg, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.log_baspk where statusjurnal = 1 group by substr(kodeblok,1,4), substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $bappp[$baris->periode][$baris->kodeorg] = $baris->jumlah;
}
$str = 'select kodegudang, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.log_transaksiht group by kodegudang, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $gudang[$baris->periode][$baris->kodegudang] = $baris->jumlah;
}
$str = 'select kodegudang, substr(tanggal,1,7) as periode, count(notransaksi) as jumlah from '.$dbname.'.log_transaksiht where post = 1 group by kodegudang, substr(tanggal,1,7) ';
$res = mysql_query($str);
while ($baris = mysql_fetch_object($res)) {
    $gudangp[$baris->periode][$baris->kodegudang] = $baris->jumlah;
}
echo "<table class=sortable cellspacing=1 border=0 width=100%>\r\n    <thead>\r\n    <tr>\r\n        <td align=center>".$_SESSION['lang']['periode']."</td>\r\n        <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td align=center>".$_SESSION['lang']['status']."</td>\r\n        <td align=center>".$_SESSION['lang']['waktu']."</td>\r\n        <td align=center>".$_SESSION['lang']['nama']."</td>  \r\n        <td align=center>".$_SESSION['lang']['kasbank']." (posted)</td>  \r\n        <td align=center>".$_SESSION['lang']['traksi']." (posted)</td>  \r\n        <td align=center>BAPP (posted)</td>  \r\n        <td align=center>BKM (posted)</td>";
if (!empty($anak)) {
    foreach ($anak as $data) {
        echo '<td align=center colspan=2 title="'.$kamus[$data].'">'.$data.'</td>';
    }
}

echo "</tr>  \r\n    </thead>\r\n    <tbody>";
if (!empty($periode)) {
    foreach ($periode as $per) {
        $tamper = true;
        if (!empty($unit)) {
            foreach ($unit as $uni) {
                if ($tamper) {
                    $tampil = $per;
                } else {
                    $tampil = '';
                }

                $tamtut = '';
                $warna = '<tr class=rowcontent>';
                if ('1' === $tutup[$per][$uni]) {
                    $tamtut = 'closed';
                }

                if ('0' === $tutup[$per][$uni]) {
                    $tamtut = '__active';
                    $warna = '<tr bgcolor=lightgreen>';
                }

                echo $warna;
                if ($tamper) {
                    echo '<td align=center rowspan='.$jumlahunit.'>'.$tampil.'</td>';
                }

                echo '<td>'.$uni.'</td>';
                echo '<td>'.$tamtut.'</td>';
                echo '<td>'.$waktu[$per][$uni].'</td>';
                echo '<td>'.$pelaku[$per][$uni].'</td>';
                $persen = ($kasbankp[$per][$uni] * 100) / $kasbank[$per][$uni];
                echo '<td align=right nowrap>'.$kasbank[$per][$uni].' ('.number_format($persen).'%)</td>';
                $persen = ($traksip[$per][$uni] * 100) / $traksi[$per][$uni];
                echo '<td align=right nowrap>'.$traksi[$per][$uni].' ('.number_format($persen).'%)</td>';
                $persen = ($bappp[$per][$uni] * 100) / $bapp[$per][$uni];
                echo '<td align=right nowrap>'.$bapp[$per][$uni].' ('.number_format($persen).'%)</td>';
                $persen = ($bkmp[$per][$uni] * 100) / $bkm[$per][$uni];
                echo '<td align=right nowrap>'.$bkm[$per][$uni].' ('.number_format($persen).'%)</td>';
                if (!empty($anak)) {
                    foreach ($anak as $data) {
                        $tamtud = '';
                        if ('1' === $tutup[$per][$data]) {
                            $tamtud = 'closed';
                        }

                        if ('0' === $tutup[$per][$data]) {
                            $tamtud = '__active';
                        }

                        echo '<td>'.$tamtud.'</td>';
                        $persen = ($gudangp[$per][$data] * 100) / $gudang[$per][$data];
                        echo '<td align=right nowrap>'.$gudang[$per][$data].' ('.number_format($persen).'%)</td>';
                    }
                }

                echo '</tr>';
                $tamper = false;
            }
        }
    }
}

echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\t\t \r\n    </table>\r\n";

?>