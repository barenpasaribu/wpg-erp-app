<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nmAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$cekapa = $_POST['cekapa'];
$budgetSort = $_POST['budgetSort'];
$mesinSort = $_POST['mesinSort'];
$akunSort = $_POST['akunSort'];
if ('' === $cekapa) {
    $cekapa = $_GET['cekapa'];
}

if ('station' === $cekapa) {
    $station = $_POST['station'];
    $str = 'select kodeorganisasi, namaorganisasi from '.$dbname.".organisasi\r\n        where induk = '".$station."'";
    $lempar = "<option value='".$station."'>".$nmOrg[$station].'</option>';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $lempar .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    echo $lempar;
}

if ('updatetahuntutup' === $cekapa) {
    $str = 'select distinct tahunbudget from '.$dbname.".bgt_budget\r\n        where tutup = '0' and kodebudget != 'UMUM' and tipebudget = 'MILL' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'\r\n        order by tahunbudget desc\r\n        ";
    $lempar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $lempar .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
    }
    echo $lempar;
}

if ('tab0' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $mesin = $_POST['mesin'];
    $hkef = '';
    $hkef .= "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'EXPL%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(0,".$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('kendaraan' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $mesin = $_POST['mesin'];
    $hkef = '';
    $str = 'select * from '.$dbname.".bgt_biaya_jam_ken_vs_alokasi\r\n        where tahunbudget = '".$tahunbudget."'";
    $res = mysql_query($str);
    $hkef = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<option value='".$bar->kodevhc."'>".$bar->kodevhc.'</option>';
    }
    echo $hkef;
}

if ('delete0' === $cekapa) {
    $kunci = $_POST['kunci'];
    $str = 'delete from '.$dbname.".bgt_budget \r\n    where kunci='".$kunci."'";
    if (mysql_query($str)) {
        $str2 = 'delete from '.$dbname.".bgt_distribusi \r\n        where kunci='".$kunci."'";
        if (mysql_query($str2)) {
        } else {
            echo ' Gagal4,'.addslashes(mysql_error($conn));
        }
    } else {
        echo ' Gagal3,'.addslashes(mysql_error($conn));
    }
}

if ('regional' === $cekapa) {
    $mesin = $_POST['mesin'];
    $kodeorg = substr($mesin, 0, 4);
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

if ('tab1' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $mesin = $_POST['mesin'];
    $strJ = 'select kodebarang, namabarang from '.$dbname.'.log_5masterbarang';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $barang[$barJ->kodebarang] = $barJ->namabarang;
    }
    $hkef = '';
    $hkef .= "<table id=container8 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['jenis']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'M%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->kodebarang."</td>\r\n            <td align=left>".$barang[$bar->kodebarang]."</td>\r\n            <td align=center>".$bar->keterangan."</td>\r\n            <td align=right>".number_format($bar->jumlah)."</td>\r\n            <td align=left>".$bar->satuanj."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(1,".$bar->kunci.")\" title=\"Hapus\">\r\n                </td>";
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
    $mesin = $_POST['mesin'];
    $hkef = '';
    $hkef .= "<table id=container7 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget = 'PKSM' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
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

if ('vhc' === $cekapa) {
    $kodevhc3 = $_POST['kodevhc3'];
    $tahunbudget = $_POST['tahunbudget'];
    $str = 'select * from '.$dbname.".bgt_biaya_ken_per_jam\r\n        where kodevhc = '".$kodevhc3."' and tahunbudget ='".$tahunbudget."'";
    $res = mysql_query($str);
    $hkef = '0';
    while ($bar = mysql_fetch_object($res)) {
        $hkef = $bar->rpperjam;
    }
    echo $hkef;
}

if ('tab3' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $mesin = $_POST['mesin'];
    $strJ = 'select * from '.$dbname.".keu_5akun where tipeakun='Biaya' and detail=1";
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $akun[$barJ->noakun] = $barJ->namaakun;
    }
    $hkef = '';
    $hkef .= "<table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n            <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'VHC%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=right>".$bar->kodevhc."</td>\r\n            <td align=right>".$bar->jumlah."</td>\r\n            <td align=left>".$bar->satuanj."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
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

if ('sebarDoong' === $cekapa) {
    $var1 = $_POST['var1'];
    $var2 = $_POST['var2'];
    $var3 = $_POST['var3'];
    $var4 = $_POST['var4'];
    $var5 = $_POST['var5'];
    $var6 = $_POST['var6'];
    $var7 = $_POST['var7'];
    $var8 = $_POST['var8'];
    $var9 = $_POST['var9'];
    $var10 = $_POST['var10'];
    $var11 = $_POST['var11'];
    $var12 = $_POST['var12'];
    $rupiah = $_POST['rupe'];
    $fis = $_POST['fis'];
    $kunci = $_POST['kunci'];
    $str = 'delete from '.$dbname.'.bgt_distribusi where kunci='.$kunci;
    mysql_query($str);
    $str = 'insert into '.$dbname.".bgt_distribusi (kunci, rp01, fis01, rp02, fis02, rp03, fis03, rp04, fis04, rp05, fis05, rp06, fis06, rp07, fis07, rp08, fis08, rp09, fis09, rp10, fis10, rp11, fis11, rp12, fis12, updateby)\r\n        values(".$kunci.",  \r\n           ".$var1 * $rupiah.",\r\n           ".$var1 * $fis.",\r\n           ".$var2 * $rupiah.",\r\n           ".$var2 * $fis.",\r\n           ".$var3 * $rupiah.",\r\n           ".$var3 * $fis.",\r\n           ".$var4 * $rupiah.",\r\n           ".$var4 * $fis.",\r\n           ".$var5 * $rupiah.",\r\n           ".$var5 * $fis.",\r\n           ".$var6 * $rupiah.",\r\n           ".$var6 * $fis.",\r\n           ".$var7 * $rupiah.",\r\n           ".$var7 * $fis.",\r\n           ".$var8 * $rupiah.",\r\n           ".$var8 * $fis.",\r\n           ".$var9 * $rupiah.",\r\n           ".$var9 * $fis.",\r\n           ".$var10 * $rupiah.",\r\n           ".$var10 * $fis.",\r\n           ".$var11 * $rupiah.",\r\n           ".$var11 * $fis.",\r\n           ".$var12 * $rupiah.",\r\n           ".$var12 * $fis.",\r\n           ".$_SESSION['standard']['userid'].');';
    if (mysql_query($str)) {
    } else {
        echo 'Error;'.mysql_error($conn);
    }
}

if ('tab4' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $mesin = $_POST['mesin'];
    $strJ = 'select kodebarang, namabarang from '.$dbname.'.log_5masterbarang';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $barang[$barJ->kodebarang] = $barJ->namabarang;
    }
    $hkef = '';
    $arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    $hkef .= ' <br /><table><tr>';
    foreach ($arrBln as $brsBulan => $listBln) {
        $hkef .= '<td>'.$listBln.'</td>';
    }
    $hkef .= '</tr>';
    $hkef .= "<tr>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss1 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss2 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss3 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss4 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss5 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss6 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss7 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss8 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss9 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss10 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss11 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss12 value=1></td>\r\n            <td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td>\r\n        </tr>\r\n\t\t\r\n\t\t\r\n\t\t\r\n        </table>";
    $hkef .= "<div style=overflow:auto;width:100%;height:300px;><table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td></td>\r\n            <td align=center>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\r\n            <td align=center>".$_SESSION['lang']['kode'].' '.$_SESSION['lang']['mesin']."</td>\r\n\t\t\t<td align=center>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['mesin']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n\t\t\t<td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodevhc']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n            <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n            <td align=center>".substr($_SESSION['lang']['jan'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['peb'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['mar'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['apr'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['mei'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['jun'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['jul'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['agt'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['sep'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['okt'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['nov'], 0, 3)."</td>\r\n            <td align=center>".substr($_SESSION['lang']['dec'], 0, 3)."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n\t\t\t<td align=center>".$_SESSION['lang']['action']."</td>\r\n           \r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $tmbh = '';
    if ('' !== $budgetSort) {
        $tmbh = " and b.kodebudget='".$budgetSort."' ";
    }

    if ('' !== $mesinSort) {
        $tmbh .= " and b.kodeorg='".$mesinSort."' ";
    }

    if ('' !== $akunSort) {
        $tmbh .= " and b.noakun='".$akunSort."' ";
    }

    $str = 'select a.*, b.tutup from '.$dbname.".bgt_budget_detail a\r\n        left join ".$dbname.".bgt_budget b on a.kunci=b.kunci\r\n        where a.tahunbudget=".$tahunbudget." and a.tipebudget='".$tipebudget."' and a.kodeorg like '".substr($_SESSION['empl']['lokasitugas'], 0, 4)."%'\r\n        and (a.kodebudget like 'EXPL%' or a.kodebudget like 'M%' or a.kodebudget like 'TOOL%' or a.kodebudget like 'VHC%' or a.kodebudget = 'PKSM') ".$tmbh.";    \r\n";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "\r\n        <tr class=rowcontent style='cursor:pointer;' id=baris".$no.">\r\n            <td><input type=checkbox onclick=sebarkanBoo('".$bar->kunci."',".$no.',this,'.$bar->rupiah.','.$bar->jumlah."); title='Sebarkan sesuai proporsi diatas'></td>\r\n            <td align=center>".$no."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n\t\t\t<td align=center>".$nmOrg[$bar->kodeorg]."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n\t\t\t<td align=center>".$bar->noakun."</td>\r\n            <td align=center>".$bar->kodevhc."</td>\r\n            <td align=right>".$bar->kodebarang."</td>\r\n            <td align=left>".$barang[$bar->kodebarang]."</td>\r\n            <td align=right>".number_format($bar->rp01)."</td>\r\n            <td align=right>".number_format($bar->rp02)."</td>\r\n            <td align=right>".number_format($bar->rp03)."</td>\r\n            <td align=right>".number_format($bar->rp04)."</td>\r\n            <td align=right>".number_format($bar->rp05)."</td>\r\n            <td align=right>".number_format($bar->rp06)."</td>\r\n            <td align=right>".number_format($bar->rp07)."</td>\r\n            <td align=right>".number_format($bar->rp08)."</td>\r\n            <td align=right>".number_format($bar->rp09)."</td>\r\n            <td align=right>".number_format($bar->rp10)."</td>\r\n            <td align=right>".number_format($bar->rp11)."</td>\r\n            <td align=right>".number_format($bar->rp12)."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center>\r\n                <input type=\"image\" id=search4 src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran'].' onclick="sebaran('.$bar->kunci.",event)\";>\r\n            </td>";
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr></div>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('sebaran4' === $cekapa) {
    $kunci = $_GET['kunci'];
    $strJ = 'select kodebarang, namabarang from '.$dbname.'.log_5masterbarang';
    $resJ = mysql_query($strJ, $conn);
    while ($barJ = mysql_fetch_object($resJ)) {
        $barang[$barJ->kodebarang] = $barJ->namabarang;
    }
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/budget_budget_pks.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
    $hkef = '';
    $hkef .= "<table id=container5 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>";
    $hkef .= "</thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget_detail\r\n        where kunci = '".$kunci."'";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $rp01 = $bar->rp01;
        $pr01 = ($rp01 * 100) / $bar->rupiah;
        $rp02 = $bar->rp02;
        $pr02 = ($rp02 * 100) / $bar->rupiah;
        $rp03 = $bar->rp03;
        $pr03 = ($rp03 * 100) / $bar->rupiah;
        $rp04 = $bar->rp04;
        $pr04 = ($rp04 * 100) / $bar->rupiah;
        $rp05 = $bar->rp05;
        $pr05 = ($rp05 * 100) / $bar->rupiah;
        $rp06 = $bar->rp06;
        $pr06 = ($rp06 * 100) / $bar->rupiah;
        $rp07 = $bar->rp07;
        $pr07 = ($rp07 * 100) / $bar->rupiah;
        $rp08 = $bar->rp08;
        $pr08 = ($rp08 * 100) / $bar->rupiah;
        $rp09 = $bar->rp09;
        $pr09 = ($rp09 * 100) / $bar->rupiah;
        $rp10 = $bar->rp10;
        $pr10 = ($rp10 * 100) / $bar->rupiah;
        $rp11 = $bar->rp11;
        $pr11 = ($rp11 * 100) / $bar->rupiah;
        $rp12 = $bar->rp12;
        $pr12 = ($rp12 * 100) / $bar->rupiah;
        $fis01 = $bar->fis01;
        $fis02 = $bar->fis02;
        $fis03 = $bar->fis03;
        $fis04 = $bar->fis04;
        $fis05 = $bar->fis05;
        $fis06 = $bar->fis06;
        $fis07 = $bar->fis07;
        $fis08 = $bar->fis08;
        $fis09 = $bar->fis09;
        $fis10 = $bar->fis10;
        $fis11 = $bar->fis11;
        $fis12 = $bar->fis12;
        if (0 === $rp01 && 0 === $rp02 && 0 === $rp03 && 0 === $rp04 && 0 === $rp05 && 0 === $rp06 && 0 === $rp07 && 0 === $rp08 && 0 === $rp09 && 0 === $rp10 && 0 === $rp11 && 0 === $rp12) {
            $rp01 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr01 = floor((100 * 100) / 12);
            $rp02 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr02 = floor((100 * 100) / 12) * 0.01;
            $rp03 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr03 = floor((100 * 100) / 12) * 0.01;
            $rp04 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr04 = floor((100 * 100) / 12) * 0.01;
            $rp05 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr05 = floor((100 * 100) / 12) * 0.01;
            $rp06 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr06 = floor((100 * 100) / 12) * 0.01;
            $rp07 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr07 = floor((100 * 100) / 12) * 0.01;
            $rp08 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr08 = floor((100 * 100) / 12) * 0.01;
            $rp09 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr09 = floor((100 * 100) / 12) * 0.01;
            $rp10 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr10 = floor((100 * 100) / 12) * 0.01;
            $rp11 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr11 = floor((100 * 100) / 12) * 0.01;
            $rp12 = floor(($bar->rupiah * 100) / 12) * 0.01;
            $pr12 = floor((100 * 100) / 12) * 0.01;
            $fis01 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis02 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis03 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis04 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis05 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis06 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis07 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis08 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis09 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis10 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis11 = floor(($bar->jumlah * 100) / 12) * 0.01;
            $fis12 = floor(($bar->jumlah * 100) / 12) * 0.01;
        }

        $rp01 = 0.01 * round($rp01 * 100);
        $rp02 = 0.01 * round($rp02 * 100);
        $rp03 = 0.01 * round($rp03 * 100);
        $rp04 = 0.01 * round($rp04 * 100);
        $rp05 = 0.01 * round($rp05 * 100);
        $rp06 = 0.01 * round($rp06 * 100);
        $rp07 = 0.01 * round($rp07 * 100);
        $rp08 = 0.01 * round($rp08 * 100);
        $rp09 = 0.01 * round($rp09 * 100);
        $rp10 = 0.01 * round($rp10 * 100);
        $rp11 = 0.01 * round($rp11 * 100);
        $rp12 = 0.01 * round($rp12 * 100);
        $fis01 = 0.01 * round($fis01 * 100);
        $fis02 = 0.01 * round($fis02 * 100);
        $fis03 = 0.01 * round($fis03 * 100);
        $fis04 = 0.01 * round($fis04 * 100);
        $fis05 = 0.01 * round($fis05 * 100);
        $fis06 = 0.01 * round($fis06 * 100);
        $fis07 = 0.01 * round($fis07 * 100);
        $fis08 = 0.01 * round($fis08 * 100);
        $fis09 = 0.01 * round($fis09 * 100);
        $fis10 = 0.01 * round($fis10 * 100);
        $fis11 = 0.01 * round($fis11 * 100);
        $fis12 = 0.01 * round($fis12 * 100);
        $prall = $pr01 + $pr02 + $pr03 + $pr04 + $pr05 + $pr06 + $pr07 + $pr08 + $pr09 + $pr10 + $pr11 + $pr12;
        $sifall = $sif01 + $sif02 + $sif03 + $sif04 + $sif05 + $sif06 + $sif07 + $sif08 + $sif09 + $sif10 + $sif11 + $sif12;
        $hkef .= "\r\n            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['index'].'</td><td align=center>'.$kunci."</td></tr>\r\n            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['mesin'].'</td><td align=center>'.$bar->kodeorg."</td></tr>\r\n            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['kodeanggaran'].'</td><td align=center>'.$bar->kodebudget."</td></tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['jan'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=jan4per name=jan4per onblur=\"kalikan4(1,".$bar->rupiah.');" value="'.$pr01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=jan4 name=jan4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=jan4fis name=jan4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['peb'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=feb4per name=feb4per onblur=\"kalikan4(2,".$bar->rupiah.');" value="'.$pr02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=feb4 name=feb4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=feb4fis name=feb4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['mar'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=mar4per name=mar4per onblur=\"kalikan4(3,".$bar->rupiah.');" value="'.$pr03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=mar4 name=mar4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=mar4fis name=mar4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['apr'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=apr4per name=apr4per onblur=\"kalikan4(4,".$bar->rupiah.');" value="'.$pr04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=apr4 name=apr4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=apr4fis name=apr4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['mei'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=may4per name=may4per onblur=\"kalikan4(5,".$bar->rupiah.');" value="'.$pr05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=may4 name=may4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=may4fis name=may4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['jun'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=jun4per name=jun4per onblur=\"kalikan4(6,".$bar->rupiah.');" value="'.$pr06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=jun4 name=jun4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=jun4fis name=jun4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['jul'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=jul4per name=jul4per onblur=\"kalikan4(7,".$bar->rupiah.');" value="'.$pr07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=jul4 name=jul4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=jul4fis name=jul4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['agt'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=aug4per name=aug4per onblur=\"kalikan4(8,".$bar->rupiah.');" value="'.$pr08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=aug4 name=aug4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=aug4fis name=aug4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['sep'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=sep4per name=sep4per onblur=\"kalikan4(9,".$bar->rupiah.');" value="'.$pr09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=sep4 name=sep4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=sep4fis name=sep4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['okt'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=oct4per name=oct4per onblur=\"kalikan4(10,".$bar->rupiah.');" value="'.$pr10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=oct4 name=oct4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=oct4fis name=oct4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['nov'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=nov4per name=nov4per onblur=\"kalikan4(11,".$bar->rupiah.');" value="'.$pr11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=nov4 name=nov4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=nov4fis name=nov4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".substr($_SESSION['lang']['dec'], 0, 3)."</td>\r\n                <td align=center><input type=text class=myinputtext id=dec4per name=dec4per onblur=\"kalikan4(12,".$bar->rupiah.');" value="'.$pr12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>\r\n                <td align=center><input type=text class=myinputtext id=dec4 name=dec4 onblur=\"jumlahkan7(".$bar->rupiah.');" value="'.$rp12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n                <td align=center><input type=text class=myinputtext id=dec4fis name=dec4fis onblur=\"jumlahkan7a(".$bar->jumlah.');" value="'.$fis12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>\r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td align=center>".$_SESSION['lang']['total']."</td>\r\n                <td align=center><input type=text class=myinputtext id=all4per name=all4per value=\"".$prall."\" disabled=true maxlength=10 style=width:30px; />%</td>\r\n                <td align=right><input type=hidden id=hiddenrupiah name=hiddenrupiah value=\"".$bar->rupiah.'">'.number_format($bar->rupiah, 2)."</td>\r\n                <td align=right><input type=hidden id=hiddenjumlah name=hiddenjumlah value=\"".$bar->jumlah.'">'.number_format($bar->jumlah, 2)."</td>\r\n            </tr>\r\n            <tr><td align=center>".$_SESSION['lang']['action']."</td><td align=center>&nbsp;</td><td align=center>\r\n                <input type=hidden id=total4 name=total4 value=\"".$bar->rupiah."\">\r\n                <input type=hidden id=total4fis name=total4fis value=\"".$bar->jumlah."\">\r\n                <input type=hidden id=progress name=progress value=\"\">    \r\n                <input type=\"image\" id=search4 src=images/save.png class=resicon title=".$_SESSION['lang']['save'].' onclick="simpansebaran('.$bar->kunci.",event)\";>\r\n            </td><td align=center>\r\n                <input type=\"image\" id=sapu4 src=images/clear.png class=resicon title=".$_SESSION['lang']['clear']." onclick=\"sapusebaran(event)\";>\r\n            </td></tr><tr>\r\n       </tr><tr>\r\n       <tr><td align=center colspan=15><button class=mybutton id=tutup4 name=tutup4 onclick=parent.closeDialog()>".$_SESSION['lang']['close']."</button></td>\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table> Nol-kan semua persentase untuk mengisi rupiah.";
}

?>