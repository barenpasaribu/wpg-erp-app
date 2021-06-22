<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_POST['notransaksi'];
$notransaksi2 = $_POST['notransaksi2'];
$tanggal = tanggalsystem($_POST['tanggal']);
$jenisby = $_POST['jenisby'];
$jumlahhrd = $_POST['jumlahhrd'];
$kodeOrg = $_POST['kodeOrg'];
$jumlah = $_POST['jumlah'];
$proses = $_POST['proses'];
switch ($proses) {
    case 'getData':
        $kd = substr($notransaksi, 0, 4);
        $sOrg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n           where char_length(kodeorganisasi)='4' order by namaorganisasi asc";
        $qOrg = mysql_query($sOrg);
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            $optOrg .= "<option value='".$rOrg['kodeorganisasi']."' ".(($kd == $rOrg['kodeorganisasi'] ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
        }
        echo '<tr class=rowcontent><td colspan=4>Ganti Notransaksi</td>';
        echo '<td><select id=kdOrg onchange=getNotrans()>'.$optOrg.'</select></td>';
        echo "<td><img src='images/save.png' title='Save' class=resicon onclick=saveNotrans()></td></tr>";
        $str = 'select a.*,b.keterangan as jns,b.id as bid from '.$dbname.".sdm_pjdinasdt a\r\n          left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id\r\n              where a.notransaksi='".$notransaksi."'";
        $res = mysql_query($str);
        $no = 0;
        $total = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                    <td>".$no."</td>\r\n                        <td>".$bar->jns."</td>\r\n                            <td>".tanggalnormal($bar->tanggal)."</td>\r\n                            <td>".$bar->keterangan."</td>\r\n                            <td align=right>".number_format($bar->jumlah, 2, '.', '.')."</td>\r\n                            <td align=right>\r\n                            <img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('jumlahhrd".$bar->bid.$no."').value='".$bar->jumlah."'\">\r\n                            <input type=text id='jumlahhrd".$bar->bid.$no."' class=myinputtextnumber size=15 onkeypress=\"return angka_doang(event);\" onblur=change_number(this) value='".number_format($bar->jumlahhrd, 2, '.', ',')."'>\r\n                            <img src='images/save.png' title='Save' class=resicon onclick=saveApprvPJD('".$bar->bid."','".$bar->notransaksi."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$no."')></td>\r\n                            </tr>";
            $total += $bar->jumlah;
        }
        echo "<tr class=rowcontent>\r\n                    <td colspan=4 align=center>TOTAL</td>\r\n                            <td align=right>".number_format($total, 2, '.', '.')."</td>\r\n                        <td></td>\r\n                            </tr>";

        break;
    case 'getNotrans':
        $orge = substr($notransaksi, 0, 4);
        if ($kodeOrg == $orge) {
            exit('Error:Kodeorganisasi Yang Sama');
        }

        $potSK = $kodeOrg.date('Y');
        $str = 'select notransaksi from '.$dbname.".sdm_pjdinasht\r\n      where  notransaksi like '".$potSK."%'\r\n          order by notransaksi desc limit 1";
        $notrx = 0;
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $notrx = substr($bar->notransaksi, 10, 5);
        }
        $notrx = (int) $notrx;
        $notrx = $notrx + 1;
        $notrx = str_pad($notrx, 5, '0', STR_PAD_LEFT);
        $notrx = $potSK.$notrx;
        echo $notrx;

        break;
    case 'saveNotrans':
        $orge = substr($notransaksi, 0, 4);
        if ($kodeOrg == $orge) {
            exit('Error:Kodeorganisasi Yang Sama');
        }

        $supd = 'update '.$dbname.".sdm_pjdinasht set notransaksi='".$notransaksi2."' where notransaksi='".$notransaksi."'";
        if (!mysql_query($supd)) {
            echo ' Gagal:'.addslashes(mysql_error($conn)).'__'.$supd;
        }

        echo $notransaksi2;

        break;
}

?>