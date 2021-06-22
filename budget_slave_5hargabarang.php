<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$regional = $_POST['regional'];
$sumberharga = $_POST['sumberharga'];
$kelompokbarang = $_POST['kelompokbarang'];
if ('' == $tahunbudget) {
    echo 'WARNING: silakan mengisi tahun budget.';
    exit();
}

if (4 != strlen($tahunbudget)) {
    echo 'WARNING: silakan mengisi tahun budget dengan benar.';
    exit();
}

if ('' == $regional) {
    echo 'WARNING: silakan mengisi region.';
    exit();
}

if ('' == $sumberharga) {
    echo 'WARNING: silakan memilih sumberharga.';
    exit();
}

if ('' == $kelompokbarang) {
    echo 'WARNING: silakan memilih kelompokbarang.';
    exit();
}

$sInd = 'select distinct induk from '.$dbname.".organisasi where kodeorganisasi in \r\n       (select kodeunit from ".$dbname.".bgt_regional_assignment where regional = '".$sumberharga."')";
$qInd = mysql_query($sInd) || exit(mysql_error($conns));
while ($rInd = mysql_fetch_assoc($qInd)) {
    ++$nor;
    if (1 == $nor) {
        $dind = "'".$rInd['induk']."'";
    } else {
        $dind .= ",'".$rInd['induk']."'";
    }
}
$thn = $tahunbudget - 1;
$str = 'SELECT distinct a.*,(select avg(hargasatuan) from '.$dbname.'.log_po_vw b where b.kodebarang=a.kodebarang and b.hargasatuan>0 and right(nopo,3) in ('.$dind.") and substr(tanggal,1,4)='".$thn."') as hargarata\r\n    FROM ".$dbname.".log_5masterbarang a where a.kodebarang like '".$kelompokbarang."%' order by a.kodebarang";
$kobar = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('' == $bar->hargarata) {
        $sGud = 'select distinct hargarata from '.$dbname.".log_5saldobulanan where kodebarang='".$bar->kodebarang."'\r\n              order by lastupdate desc limit 1";
        $qGud = mysql_query($sGud) || exit(mysql_error($conns));
        $rGud = mysql_fetch_assoc($qGud);
        $bar->hargarata = $rGud['hargarata'];
    }

    $sCek = 'select distinct matauang from '.$dbname.".log_po_vw where kodebarang='".$bar->kodebarang."'\r\n          and  hargasatuan>0 and right(nopo,3) in (".$dind.") and substr(tanggal,1,4)='".$thn."'";
    $qCek = mysql_query($sCek) || exit(mysql_error($conns));
    $rCek = mysql_fetch_assoc($qCek);
    if ('IDR' != $rCek['matauang']) {
        $sKurs = 'select distinct kurs from '.$dbname.".setup_matauangrate \r\n               where kode='".$rCek['matauang']."' and kurs!=0 order by daritanggal desc limit 1";
        $qKurs = mysql_query($sKurs) || exit(mysql_error($conns));
        $rKurs = mysql_fetch_assoc($qKurs);
        $bar->hargarata = $rKurs['kurs'] * $bar->hargarata;
    }

    $isidata[$bar->kodebarang][kodebarang] = $bar->kodebarang;
    $isidata[$bar->kodebarang][kodeorg] = $sumberharga;
    $isidata[$bar->kodebarang][hargarata] = $bar->hargarata;
    $kobar .= "'".$bar->kodebarang."',";
}
$kobar = substr($kobar, 0, -1);
$str = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang\r\n    where kodebarang in (".$kobar.')';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $isidata[$bar->kodebarang][namabarang] = $bar->namabarang;
    $isidata[$bar->kodebarang][satuan] = $bar->satuan;
}
$thnlalu = $tahunbudget - 1;
$str = 'select distinct kodebarang,hargarata from '.$dbname.".log_5saldobulanan where hargarata>0 and periode like '".$thnlalu."%' order by hargarata desc";
$resq = mysql_query($str);
while ($barq = mysql_fetch_object($resq)) {
    if (!isset($harga[$barq->kodebarang])) {
        $harga[$barq->kodebarang] = $barq->hargarata;
    }
}
echo '<table width=100%><tr id=baris_0 class=rowheader>';
echo '<td align=left>Set '.$_SESSION['lang']['varian'].'';
echo "<input type=text id=varianall size=5 value='0.00' maxlength=5 class=myinputtext onkeypress=\"return angka_doangsamaminus(event);\">\r\n            <button class=mybutton id=proses onclick=updateHargaall()>".$_SESSION['lang']['proses'].'</button></td>';
echo '<td align=right><button class=mybutton id=simpan onclick=simpanHarga(1)>'.$_SESSION['lang']['save'].'</button></td>';
echo '</tr></table>';
echo "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['regional']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>\r\n            <td align=center>".$_SESSION['lang']['varian']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargabudget']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
if (empty($isidata)) {
} else {
    foreach ($isidata as $baris) {
        if (0 == $baris[hargarata]) {
            $baris[hargarata] = $harga[$baris['kodebarang']];
        }

        ++$no;
        echo '<tr id=baris_'.$no.' class=rowcontent>';
        echo '<td>'.$no.'</td>';
        echo '<td>'.$tahunbudget.'</td>';
        echo '<td>'.$regional.'</td>';
        echo '<td><label id=kode_'.$no.'>'.$baris[kodebarang].'</label></td>';
        echo '<td>'.$baris[namabarang].'</td>';
        echo '<td>'.$baris[satuan].'</td>';
        echo '<td>'.$sumberharga.'</td>';
        echo '<td align=right><label id=rata_'.$no.'>'.number_format($baris[hargarata], 2).'</label></td>';
        echo '<td><input type=text id=varian_'.$no." size=5 value='0.00' maxlength=5 class=myinputtext onkeyup=\"hitungharga(".$baris[hargarata].',this.value,'.$no.')" onkeypress="return angka_doangsamaminus(event);"></td>';
        $hargarata = $baris[hargarata] + 0;
        $hargarata = round($hargarata * 100) / 100;
        echo '<td><input type=text id=harga_'.$no." size=15 value='".$hargarata."' maxlength=15 class=myinputtext onkeyup=\"hitungpersen(".$baris[hargarata].',this.value,'.$no.')" onkeypress="return angka_doang(event);"></td>';
        echo '</tr>';
    }
}

echo "     </tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";

?>