<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$optNm = makeOption($dbname, 'bgt_kode', 'kodebudget,nama');
$cekapa = $_POST['cekapa'];
if ('hkef' === $cekapa) {
    $tahunbudget = $_POST['tahunbudget'];
    $str = 'select * from '.$dbname.".bgt_hk\r\n        where tahunbudget = '".$tahunbudget."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef = $bar->harisetahun - $bar->hrminggu - $bar->hrlibur + $bar->hrliburminggu;
    }
    $optupah = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $supah = 'select distinct golongan from '.$dbname.".bgt_upah where \r\n            kodeorg='".substr($_POST['kodews'], 0, 4)."' and tahunbudget='".$tahunbudget."'";
    $qupah = mysql_query($supah) || exit(mysql_error($conns));
    while ($rupah = mysql_fetch_assoc($qupah)) {
        $optupah .= "<option value='".$rupah['golongan']."'>".$optNm[$rupah['golongan']].'</option>';
    }
    echo $hkef.'#####'.$optupah;
}

if ('upah' === $cekapa) {
    $kodebudget0 = $_POST['kodebudget0'];
    $str = 'select * from '.$dbname.".bgt_upah\r\n        where closed=1 and golongan = '".$kodebudget0."' and kodeorg = '".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef = $bar->jumlah;
    }
    echo $hkef;
}

if ('regional' === $cekapa) {
    $kodews = $_POST['kodews'];
    $kodeorg = substr($kodews, 0, 4);
    $str = 'select * from '.$dbname.".bgt_regional_assignment\r\n        where kodeunit = '".$kodeorg."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef = $bar->regional;
    }
    echo $hkef;
}

if ('barang' === $cekapa) {
    $kodebarang1 = $_POST['kodebarang1'];
    $tahunbudget = $_POST['tahunbudget'];
    $regional = $_POST['regional'];
    $str = 'select * from '.$dbname.".bgt_masterbarang\r\n        where closed=1 and kodebarang = '".$kodebarang1."' and regional ='".$regional."' and tahunbudget ='".$tahunbudget."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef = $bar->hargasatuan;
    }
    echo $hkef;
}

if ('tab0' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $hkef = '';
    $hkef .= "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['volume']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'SDM%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".number_format($bar->volume)."</td>\r\n            <td align=left>".$bar->satuanv."</td>\r\n            <td align=right>".number_format($bar->jumlah)."</td>\r\n            <td align=left>".$bar->satuanj."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= '<td align=center><img id="delRow" class="zImgBtn" src="images/application/application_delete.png" onclick="deleteRow(0,'.$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= '</tr>';
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('tab1' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $strJ = 'select kodebarang, namabarang from '.$dbname.'.log_5masterbarang';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $barang[$barJ->kodebarang] = $barJ->namabarang;
    }
    $hkef = '';
    $hkef .= "<table id=container8 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'M%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->kodebarang."</td>\r\n            <td align=left>".$barang[$bar->kodebarang]."</td>\r\n            <td align=right>".number_format($bar->jumlah)."</td>\r\n            <td align=left>".$bar->satuanj."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(1,".$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('tab2' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $strJ = 'select kodebarang, namabarang from '.$dbname.'.log_5masterbarang';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $barang[$barJ->kodebarang] = $barJ->namabarang;
    }
    $hkef = '';
    $hkef .= "<table id=container7 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'TOOL%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->kodebarang."</td>\r\n            <td align=left>".$barang[$bar->kodebarang]."</td>\r\n            <td align=right>".number_format($bar->jumlah)."</td>\r\n            <td align=left>".$bar->satuanj."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(2,".$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('tab3' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $strJ = 'select * from '.$dbname.".keu_5akun where tipeakun='Biaya' and detail=1";
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $akun[$barJ->noakun] = $barJ->namaakun;
    }
    $hkef = '';
    $hkef .= "<table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'TRANSIT%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->noakun."</td>\r\n            <td align=left>".$akun[$bar->noakun]."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(3,".$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('tab4' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $hkef = '';
    $no = 1;
    $hkef .= "<table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr id=baris_0 name=baris_0>\r\n            <td align=center>No.</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['volume']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tutup = '0' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."' order by kodebudget, noakun, kodebarang";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= '<tr id=baris_'.$no." class=rowcontent>\r\n            <td align=center><input type=hidden id=kunci_".$no.' name=kunci_'.$no.' value='.$bar->kunci.'>'.$no."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->noakun."</td>\r\n            <td align=right>".number_format($bar->volume)."</td>\r\n            <td align=right>".$bar->kodebarang."</td>\r\n            <td align=right>".$bar->jumlah."</td>\r\n            <td align=right>".number_format($bar->rupiah)."</td>\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('delete0' === $cekapa) {
    $kunci = $_POST['kunci'];
    $str = 'delete from '.$dbname.".bgt_budget \r\n    where kunci='".$kunci."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal3,'.addslashes(mysql_error($conn));
    }
}

?>