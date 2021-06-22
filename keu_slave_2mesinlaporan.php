<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];
if ('' === $periode && '' === $gudang) {
    $str = 'select a.*,c.induk from '.$dbname.".keu_5mesinlaporandt a\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere a.kodeorg = '".$pt."'\r\n\t\t";
} else {
    if ('' === $periode && '' !== $gudang) {
        $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk,c.namaorganisasi from '.$dbname.".keu_jurnaldt a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."'\r\n\t\torder by a.nojurnal \r\n\t\t";
    } else {
        if ('' === $gudang) {
            $str = 'select a.*,b.namaakun,substr(a.kodeorg,1,4) as bussunitcode,c.induk from '.$dbname.".keu_jurnaldt a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."' and substr(tanggal,1,7)='".$periode."'\r\n\t\torder by a.nojurnal \r\n\t\t";
        } else {
            $str = 'select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk,c.namaorganisasi from '.$dbname.".keu_jurnaldt a\r\n\t\tleft join ".$dbname.".keu_5akun b\r\n\t\ton a.noakun=b.noakun\r\n\t\tleft join ".$dbname.".organisasi c\r\n\t\ton substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n\t\twhere c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."' and substr(tanggal,1,7)='".$periode."'\r\n\t\torder by a.nojurnal \r\n\t\t";
        }
    }
}

if ('' === $periode) {
    $sawalQTY = '';
    $masukQTY = '';
    $keluarQTY = '';
    $kuantitas = 0;
    $res = mysql_query($str);
    $no = 0;
    if (mysql_num_rows($res) < 1) {
        echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
    } else {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $periode = date('Y-m-d H:i:s');
            $kodeorg = $bar->kodeorg;
            $nourut = $bar->nourut;
            $keterangandisplay = $bar->keterangandisplay;
            $tipe = $bar->tipe;
            $noakundari = $bar->noakundari;
            $noakunsampai = $bar->noakunsampai;
            $namaakun = $bar->namaakun;
            $uraian = $bar->keterangan;
            $jumlah = $bar->jumlah;
            $debet = $kredit = 0;
            if (0 < $jumlah) {
                $debet = $jumlah;
            } else {
                $kredit = 0 - $jumlah;
            }

            echo "<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','','','','');\">\r\n\t\t\t\t  <td align=center>".$kodeorg."</td>\r\n\t\t\t\t  <td>".$nourut."</td>\r\n\t\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t\t  <td>".$tipe."</td>\r\n\t\t\t\t  <td>".$noakundari."</td>\r\n\t\t\t\t  <td>".$noakunsampai."</td>\r\n\t\t\t\t  <td>".$uraian."</td>\r\n\t\t\t\t</tr>";
        }
    }
} else {
    $salakqty = 0;
    $masukqty = 0;
    $keluarqty = 0;
    $sawalQTY = 0;
    $res = mysql_query($str);
    $no = 0;
    if (mysql_num_rows($res) < 1) {
        echo '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
    } else {
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $periode = date('Y-m-d H:i:s');
            $nojurnal = $bar->nojurnal;
            $tanggal = $bar->tanggal;
            $kodeorg = $bar->kodeorg;
            $noakun = $bar->noakun;
            $namaakun = $bar->namaakun;
            $uraian = $bar->keterangan;
            $jumlah = $bar->jumlah;
            $debet = $kredit = 0;
            if (0 < $jumlah) {
                $debet = $jumlah;
            } else {
                $kredit = 0 - $jumlah;
            }

            echo "<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailJurnal(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">\r\n\t\t\t\t  <td align=center width=50>".$nourut."</td>\r\n\t\t\t\t  <td>".$keterangandisplay."</td>\r\n\t\t\t\t  <td align=center>".tanggalnormal($tanggal)."</td>\r\n\t\t\t\t  <td align=center>".$kodeorg."</td>\r\n\t\t\t\t  <td align=center>".$noakun."</td>\r\n\t\t\t\t  <td>".$namaakun."</td>\r\n\t\t\t\t  <td>".$uraian."</td>\r\n\t\t\t\t  <td align=right width=100>".number_format($debet, 2)."</td>\r\n\t\t\t\t  <td align=right width=100>".number_format($kredit, 2)."</td>\r\n\t\t\t</tr>";
        }
    }
}

?>