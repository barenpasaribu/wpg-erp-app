<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$cekapa = $_POST['cekapa'];
if ('' === $cekapa) {
    $cekapa = $_GET['cekapa'];
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

if ('saveatas' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $jenisbiaya = $_POST['jenisbiaya'];
    $kodebudget = $_POST['kodebudget'];
    $jumlahbiaya = $_POST['jumlahbiaya'];
    $ktrngan = $_POST['ktrngan'];
    $jumlah = $_POST['jamperthn'];
    $kodevhc = $_POST['kodevhc'];
    $lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    if ('' === $jumlah) {
        $jumlah = 0;
    }

    $sCekJam = 'select * from '.$dbname.".bgt_biaya_jam_ken_vs_alokasi where tahunbudget='".$tahunbudget."' \r\n          and kodevhc='".$kodevhc."'";
    $qCekJam = mysql_query($sCekJam) || exit(mysql_error($conns));
    $rCekJam = mysql_fetch_assoc($qCekJam);
    $sisa = $rCekJam['jamsetahun'] - $rCekJam['teralokasi'];
    if ($sisa < $jumlah) {
        exit('Error: Vehicle '.$kodevhc.' has been allocated: '.$rCekJam['teralokasi'].' from total hours :'.$rCekJam['jamsetahun'].' can only allocate as remains:'.$sisa.'');
    }

    $str = 'INSERT INTO '.$dbname.".`bgt_budget` (\r\n    `tipebudget` ,\r\n    `tahunbudget` ,\r\n    `kodeorg` ,\r\n    `kodebudget` ,\r\n    `noakun` ,\r\n    `rupiah` ,\r\n    `updateby` ,\r\n    `keterangan`,\r\n    `kodevhc`,\r\n    `jumlah`,\r\n    `satuanj`\r\n    )\r\n    VALUES (\r\n    '".$tipebudget."', '".$tahunbudget."', '".$lokasi."', '".$kodebudget."', '".$jenisbiaya."', \r\n        '".$jumlahbiaya."', '".$_SESSION['standard']['userid']."','".$ktrngan."',\r\n        '".$kodevhc."',".$jumlah.",'JAM' \r\n    )";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.$str.addslashes(mysql_error($conn));
    }
}

if ('cekclose' === $cekapa) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $jenisbiaya = $_POST['jenisbiaya'];
    $kodebudget = $_POST['kodebudget'];
    $jumlahbiaya = $_POST['jumlahbiaya'];
    $lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tutup = '1' and kodeorg = '".$lokasi."' and kodebudget = 'UMUM' and tahunbudget ='".$tahunbudget."' limit 0, 1";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= 'Data has been closed';
    }
    if ('' !== $hkef) {
        echo $hkef;
    }
}

if ('updatetahun' === $cekapa) {
    $tipebudget = substr($_SESSION['empl']['lokasitugas'], 3, 1);
    if ('M' === $tipebudget) {
        $tipebudget = 'MILL';
    } else {
        if ('E' === $tipebudget) {
            $tipebudget = 'ESTATE';
        } else {
            $tipebudget = $_SESSION['empl']['tipelokasitugas'];
        }
    }

    $kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $str = 'select distinct tahunbudget from '.$dbname.".bgt_budget\r\n        where tipebudget='".$tipebudget."' and kodeorg like '".$kodeorg."%' and kodebudget like 'UMUM%'\r\n        order by tahunbudget desc\r\n            ";
    $res = mysql_query($str);
    $hkef = "<option value=''>".$_SESSION['lang']['all'].'</option>';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
    }
    echo $hkef;
}

if ('vhc' === $cekapa) {
    $kodevhc = $_POST['kodevhc'];
    $jamperthn = $_POST['jamperthn'];
    $str = 'select rpperjam from '.$dbname.".bgt_biaya_ken_per_jam where kodevhc='".$kodevhc."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $rpperjam = $bar->rpperjam;
    }
    $total = $jamperthn * $rpperjam;
    exit(round($total, 2));
}

if ('tab' === $cekapa) {
    $tipebudget = substr($_SESSION['empl']['lokasitugas'], 3, 1);
    if ('M' === $tipebudget) {
        $tipebudget = 'MILL';
    } else {
        if ('E' === $tipebudget) {
            $tipebudget = 'ESTATE';
        } else {
            $tipebudget = $_SESSION['empl']['tipelokasitugas'];
        }
    }

    $tahunbudget = $_POST['tahunbudget'];
    $pilihtahun0 = $_POST['pilihtahun0'];
    $kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $hkef = '';
    $hkef .= $_SESSION['lang']['budgetyear']." : <select name=pilihtahun0 id=pilihtahun0 onchange=\"updateTab();\"><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttahunbudget.'</select>';
    $hkef .= '<input type=hidden id=hidden0 name=hidden0 value="">';
    $hkef .= "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select noakun,namaakun from '.$dbname.".keu_5akun where detail=1 and tipeakun = 'Biaya' order by noakun\r\n                    ";
    $optakun = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $akun[$bar->noakun] = $bar->namaakun;
    }
    //$str = 'select * from '.$dbname.".bgt_budget where kodebudget like 'UMUM%' and tipebudget = '".$tipebudget."' and kodeorg = '".$kodeorg."' and tahunbudget like '%".$pilihtahun0."%' order by tahunbudget desc, noakun";
    $str = 'select * from '.$dbname.".bgt_budget where  tipebudget = '".$tipebudget."' and kodeorg = '".$kodeorg."' and tahunbudget like '%".$pilihtahun0."%' order by tahunbudget desc, noakun";
	//echo $str;
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= '<tr class=rowcontent title='.$isiDta.">\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=center>".$bar->noakun."</td>\r\n            <td align=left>".$akun[$bar->noakun]."</td>\r\n            <td align=left>".ucfirst($bar->keterangan)."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= '<td align=center><img id="delRow" class="zImgBtn" src="images/application/application_delete.png" onclick="deleteRow('.$bar->kunci.')" title="Hapus"></td>';
        } else {
            $hkef .= '<td align=center>&nbsp;</td>';
        }

        $hkef .= "\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('delete' === $cekapa) {
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

if ('tutup' === $cekapa) {
    $kunci = $_POST['kunci'];
    $str = 'update '.$dbname.".bgt_budget set tutup='1'\r\n        where kunci ='".$kunci."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('sebaran' === $cekapa) {
    $kunci = $_GET['kunci'];
    $str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n                    where detail=1 and tipeakun = 'Biaya' order by noakun\r\n                    ";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $akun[$bar->noakun] = $bar->namaakun;
    }
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    echo "<div style=\"border: 1px solid orange; width: 150px; position: fixed; right: 20px; top: 65px; color: rgb(255, 0, 0); font-family: Tahoma; font-size: 13px; font-weight: bolder; text-align: center; background-color: rgb(255, 255, 255); z-index: 10000; display: none;\" id=\"progress\">\r\nPlease wait.....! <br>\r\n<img src=\"images/progress.gif\">\r\n</div>\r\n<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/budget_by_umum.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
    $arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    $hkef = '';
    $sTot = 'select * from '.$dbname.".bgt_budget_detail where kunci = '".$kunci."'";
    $qTot = mysql_query($sTot) || exit(mysql_error($sTot));
    $rRes = mysql_fetch_assoc($qTot);
    $hkef .= "<p align=center><fieldset style='width:200px;'><legend>".$_SESSION['lang']['sebaran'].'/'.$_SESSION['lang']['bulan'].'</legend>';
    $hkef .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
    $hkef .= '<tr class=rowheader><td>'.$_SESSION['lang']['total']." (Rp.)</td><td align=center>%</td><td align=right id='hasilPerkalian'>".number_format($rRes['rupiah']).'</td></tr></thead><tbody>';
    for ($bre = 1; $bre <= 12; ++$bre) {
        if (strlen($bre) < 2) {
            $abe = '0'.$bre;
        } else {
            $abe = $bre;
        }

        if (null === $rRes['rp'.$abe]) {
            $hslDr = $rRes['rupiah'] / 12 / $rRes['rupiah'] * 100;
            $rRes['rp'.$abe] = $rRes['rupiah'] / 12;
        } else {
            $hslDr = $rRes['rp'.$abe] / $rRes['rupiah'] * 100;
        }

        $hkef .= '<tr class=rowcontent><td>'.$arrBln[$bre]."</td>\r\n                                    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=persenPrdksi".$bre." onblur=ubahNilai(this.value,'".$rRes['rupiah']."','brt_x') value='".number_format($hslDr, 0)."' /></td>";
        $hkef .= "<td><input type='text' id=brt_x".$bre.' class="myinputtextnumber" style="width:75px;" value='.$rRes['rp'.$abe]." /></td>\r\n                                    </tr>";
    }
    $hkef .= "<tr class=rowcontent><td  colspan=3 align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"simpansebaran('".$rRes['kunci']."','".$rRes['rupiah']."',event)\" src='images/save.png'/>&nbsp;&nbsp;<img id='detail_add' title='Clear Form' class=zImgBtn  width='16' height='16'  onclick=\"clearForm()\" src='images/clear.png'/></td>";
    $hkef .= '</tr></tbody></table></fieldset></p>';
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('tabs' === $cekapa) {
    $tipebudget = substr($_SESSION['empl']['lokasitugas'], 3, 1);
    if ('M' === $tipebudget) {
        $tipebudget = 'MILL';
    } else {
        if ('E' === $tipebudget) {
            $tipebudget = 'ESTATE';
        } else {
            $tipebudget = $_SESSION['empl']['tipelokasitugas'];
        }
    }

    $tahunbudget = $_POST['tahunbudget'];
    $pilihtahun1 = $_POST['pilihtahun1'];
    $kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $hkef = '';
    $hkef .= $_SESSION['lang']['budgetyear']." : <select name=pilihtahun1 id=pilihtahun1 onchange=\"updateTabs();\"><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttahunbudget.'</select>';
    $hkef .= '<input type=hidden id=hidden1 name=hidden1 value="">';
    $hkef .= "<table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>No</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n            <td align=center>Jan</td>\r\n            <td align=center>Feb</td>\r\n            <td align=center>Mar</td>\r\n            <td align=center>Apr</td>\r\n            <td align=center>May</td>\r\n            <td align=center>Jun</td>\r\n            <td align=center>Jul</td>\r\n            <td align=center>Aug</td>\r\n            <td align=center>Sep</td>\r\n            <td align=center>Oct</td>\r\n            <td align=center>Nov</td>\r\n            <td align=center>Dec</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n                    where detail=1 and tipeakun = 'Biaya' order by noakun\r\n                    ";
    $optakun = '';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $akun[$bar->noakun] = $bar->namaakun;
    }
    $str = 'select a.*, b.tutup from '.$dbname.".bgt_budget_detail a\r\n        left join ".$dbname.".bgt_budget b on a.kunci=b.kunci\r\n        where a.kodebudget like 'UMUM%' and a.tipebudget = '".$tipebudget."' and a.kodeorg = '".$kodeorg."' and a.tahunbudget like '%".$pilihtahun1."%'\r\n            order by a.tahunbudget desc, a.noakun";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        (0 === $bar->tutup ? ($rpt = ' onclick="sebaran('.$bar->kunci.",event)\" title='Sebaran ".$kodeorg.' '.$akun[$bar->noakun]."' style='cursor:pointer;'") : ($rpt = ' '));
        $hkef .= "<tr class=rowcontent>\r\n            <td align=center ".$rpt.'>'.$no."</td>\r\n            <td align=center ".$rpt.'>'.$bar->tahunbudget."</td>\r\n            <td align=cente ".$rpt.'r>'.$bar->kodebudget."</td>\r\n            <td align=center ".$rpt.'>'.$bar->tipebudget."</td>\r\n            <td align=right ".$rpt.'>'.$bar->noakun."</td>\r\n            <td align=left ".$rpt.'>'.$akun[$bar->noakun]."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp01)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp02)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp03)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp04)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp05)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp06)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp07)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp08)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp09)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp10)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp11)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp12)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rupiah).'</td>';
        if (0 === $bar->tutup) {
            $hkef .= "\r\n            <td align=center>\r\n                <input type=\"image\" id=search4 src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran'].' onclick="sebaran('.$bar->kunci.",event)\";>\r\n            </td>";
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
    $tipebudget = substr($_SESSION['empl']['lokasitugas'], 3, 1);
    if ('M' === $tipebudget) {
        $tipebudget = 'MILL';
    } else {
        if ('E' === $tipebudget) {
            $tipebudget = 'ESTATE';
        } else {
            $tipebudget = $_SESSION['empl']['tipelokasitugas'];
        }
    }

    $pilihtahun2 = $_POST['pilihtahun2'];
    $kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $hkef = '';
    $hkef .= '<input type=hidden id=hidden2 name=hidden2 value="">';
    $hkef .= "<table id=container5 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr id=baris_0 name=baris_0>\r\n            <td align=center>No</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tutup=0 and kodebudget like 'UMUM%' and tipebudget = '".$tipebudget."' and kodeorg = '".$kodeorg."' and tahunbudget like '%".$pilihtahun2."%'\r\n            order by tahunbudget desc,noakun";
    $res = mysql_query($str);
    $no = 1;
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= '<tr id=baris_'.$no." class=rowcontent>\r\n            <td align=center><input type=hidden id=kunci_".$no.' name=kunci_'.$no.' value='.$bar->kunci.'>'.$no."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=right>".$bar->noakun."</td>\r\n            <td align=right>".number_format($bar->rupiah)."</td>\r\n       </tr>";
        ++$no;
    }
    echo $hkef;
    echo "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
}

if ('insertDistribusi' === $cekapa) {
    for ($a = 1; $a <= 12; ++$a) {
        if ('' === $_POST['arrBrt'][$a]) {
            $_POST['arrBrt'][$a] = 0;
        }

        $totalSum += $_POST['arrBrt'][$a];
    }
    if ($_POST['totalSetahn'] < $totalSum) {
        exit('Error : Total mothly ('.$totalSum.') greater than annualy ('.$_POST['totalSetahn'].') ');
    }

    $sCek = 'select distinct kunci from '.$dbname.".bgt_distribusi  where kunci='".$_POST['kunci']."'";
    $qCek = mysql_query($sCek) || exit(mysql_error($sCek));
    $rCek = mysql_num_rows($qCek);
    if (0 < $rCek) {
        $sUpdate = 'update '.$dbname.".bgt_distribusi set updateby='".$_SESSION['standard']['userid']."'";
        for ($art = 1; $art <= 12; ++$art) {
            if ('1' === strlen($art)) {
                $ccrt = '0'.$art;
            } else {
                $ccrt = $art;
            }

            $sUpdate .= ' ,rp'.$ccrt."='".$_POST['arrBrt'][$art]."'";
        }
        $sUpdate .= " where  kunci='".$_POST['kunci']."'";
        if (!mysql_query($sUpdate)) {
            echo ' Gagal,_'.$sUpdate.'__'.mysql_error($conn);
        }
    } else {
        $sInsert = 'insert into '.$dbname.'.bgt_distribusi (kunci, updateby,rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09,  rp10, rp11,  rp12 )';
        $sInsert .= " values ('".$_POST['kunci']."','".$_SESSION['standard']['userid']."'";
        for ($arb = 1; $arb <= 12; ++$arb) {
            $sInsert .= ",'".$_POST['arrBrt'][$arb]."'";
        }
        $sInsert .= ')';
        if (!mysql_query($sInsert)) {
            echo ' Gagal,________'.$sInsert.'__'.mysql_error($conn);
        }
    }
}

?>