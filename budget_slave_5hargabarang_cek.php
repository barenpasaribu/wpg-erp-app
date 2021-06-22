<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$regional = $_POST['regional'];
$sumberharga = $_POST['sumberharga'];
$kelompokbarang = $_POST['kelompokbarang'];
$what = $_POST['what'];
if ('adadata' == $what) {
    $str = 'select * from '.$dbname.".bgt_masterbarang \r\n    where tahunbudget='".$tahunbudget."' and regional = '".$regional."' \r\n        and kodebarang like '".$kelompokbarang."%'\r\n            limit 0,1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $adadata = '1';
    }
    if ('1' == $adadata) {
        echo "Sudah ada data, bila lanjut akan ditimpa.\nLanjut?/nThis kind of data already exist.\n Replace ?";
        exit();
    }
}

if ('closing' == $what) {
    $str = 'select * from '.$dbname.".bgt_masterbarang \r\n    where tahunbudget='".$tahunbudget."' and regional = '".$regional."' \r\n        and closed = 1\r\n            limit 0,1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $sudahtutup = '1';
    }
    if ('1' == $sudahtutup) {
        echo 'Data has been closed';
        exit();
    }
}

if ('delete' == $what) {
    $str = 'DELETE FROM '.$dbname.'.bgt_masterbarang WHERE tahunbudget = '.$tahunbudget." and regional = '".$regional."'";
    $res = mysql_query($str);
}

if ('editing' == $what) {
    echo '<table width=100%><tr id=baris_0 class=rowheader>';
    echo '<td align=left>Set '.$_SESSION['lang']['varian'].'';
    echo "<input type=text id=varianall size=5 value='0.00' maxlength=5 class=myinputtext onkeypress=\"return angka_doangsamaminus(event);\">\r\n            <button class=mybutton id=proses onclick=updateHargaall()>".$_SESSION['lang']['proses'].'</button></td>';
    echo '<td align=right><button class=mybutton id=simpan onclick=updateHarga(1)>'.$_SESSION['lang']['save'].'</button></td>';
    echo '</tr></table>';
    echo "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['nomor']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['regional']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>\r\n            <td align=center>".$_SESSION['lang']['varian']."</td>\r\n            <td align=center>".$_SESSION['lang']['hargabudget']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $thnlalu = $tahunbudget - 1;
    $str = 'select distinct kodebarang,hargarata from '.$dbname.".log_5saldobulanan where hargarata>0 and periode like '".$thnlalu."%' order by hargarata";
    $resq = mysql_query($str);
    while ($barq = mysql_fetch_object($resq)) {
        if (!isset($harga[$bar->kodebarang])) {
            $harga[$barq->kodebarang] = $barq->hargarata;
        }
    }
    $str = 'select regional, tahunbudget, kodebarang, hargasatuan, sumberharga, variant, hargalalu from '.$dbname.".bgt_masterbarang\r\n      where tahunbudget = '".$tahunbudget."' and kodebarang like '".$kelompokbarang."%' and regional = '".$regional."' order by regional";
    $res = mysql_query($str);
    $kobar = '';
    while ($bar = mysql_fetch_object($res)) {
        $isidata[$bar->kodebarang][regional] = $bar->regional;
        $isidata[$bar->kodebarang][tahunbudget] = $bar->tahunbudget;
        $isidata[$bar->kodebarang][kodebarang] = $bar->kodebarang;
        $isidata[$bar->kodebarang][hargasatuan] = $bar->hargasatuan;
        $isidata[$bar->kodebarang][sumberharga] = $bar->sumberharga;
        $isidata[$bar->kodebarang][variant] = $bar->variant;
        $isidata[$bar->kodebarang][hargalalu] = $bar->hargalalu;
        $kobar .= "'".$bar->kodebarang."',";
    }
    $kobar = substr($kobar, 0, -1);
    $str = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang\r\n      where kodebarang in (".$kobar.')';
    $res = mysql_query($str);
    while ($bar = @mysql_fetch_object($res)) {
        $isidata[$bar->kodebarang][namabarang] = $bar->namabarang;
        $isidata[$bar->kodebarang][satuan] = $bar->satuan;
    }
    if (empty($isidata)) {
        echo "<tr><td colspan=9>Empty, please click\r\n        <button id= buttonbaru class=mybutton onclick=buatbaru(".$tahunbudget.",'".$regional."',".$kelompokbarang.')>'.$_SESSION['lang']['new']."</button>.</td>\r\n        </tr>";
    } else {
        foreach ($isidata as $baris) {
            if (0 == $baris[hargalalu]) {
                $baris[hargalalu] = $harga[$baris['kodebarang']];
            }

            ++$no;
            echo '<tr id=baris_'.$no.' class=rowcontent>';
            echo '<td>'.$no.'</td>';
            echo '<td>'.$tahunbudget.'</td>';
            echo '<td>'.$regional.'</td>';
            echo '<td><label id=kode_'.$no.'>'.$baris[kodebarang].'</label></td>';
            echo '<td>'.$baris[namabarang].'</td>';
            echo '<td>'.$baris[satuan].'</td>';
            echo '<td><label id=sumber_'.$no.'>'.$baris[sumberharga].'</td>';
            echo '<td align=right><label id=rata_'.$no.'>'.number_format($baris[hargalalu], 2).'</label></td>';
            echo '<td><input type=text id=varian_'.$no." size=5 value='".$baris[variant]."' maxlength=5 class=myinputtext onkeyup=\"hitungharga(".$baris[hargalalu].',this.value,'.$no.')" onkeypress="return angka_doangsamaminus(event);"></td>';
            echo '<td><input type=text id=harga_'.$no." size=15 value='".$baris[hargasatuan]."' maxlength=15 class=myinputtext onkeyup=\"hitungpersen(".$baris[hargalalu].',this.value,'.$no.')" onkeypress="return angka_doang(event);"></td>';
            echo '</tr>';
        }
    }

    echo "     </tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

?>