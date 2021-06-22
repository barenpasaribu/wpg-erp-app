<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$kodeorg = $_POST['kodeorg'];
$tahun = $_POST['tahun'];
if ('excel' == $proses || 'pdf' == $proses) {
    $kodeorg = $_GET['kodeorg'];
    $tahun = $_GET['tahun'];
}

if ('preview' == $proses || 'excel' == $proses) {
    if ('' == $kodeorg) {
        echo 'Error: Unit tidak boleh kosong.';
        exit();
    }

    if ('' == $tahun) {
        echo 'Error: Tahun tidak boleh kosong.';
        exit();
    }
}

if ('excel' == $proses) {
    $stream .= "<table border='1'>";
} else {
    $stream .= "<table cellspacing='1' border='0' class='sortable' width=100%>";
}

$stream .= "<thead>\r\n<tr class=rowheader>\r\n<td align=center>".$_SESSION['lang']['nomor']."</td>\r\n<td align=center>".$_SESSION['lang']['kodeorg']."</td>    \r\n<td align=center>".$_SESSION['lang']['id']."</td>\r\n<td align=center>".$_SESSION['lang']['namakaryawan']."</td>            \r\n<td align=center>".$_SESSION['lang']['tipekaryawan']."</td>\r\n<td align=center>".$_SESSION['lang']['statuspajak']."</td>\r\n<td align=center>".$_SESSION['lang']['npwp']."</td>  \r\n<td align=center>".$_SESSION['lang']['tahun']."</td>\r\n<td align=center>Penghasilan01</td>\r\n<td align=center>Gaji01</td>   \r\n<td align=center>Tunj01</td>  \r\n<td align=center>".$_SESSION['lang']['pph12'].".01</td>    \r\n<td align=center>Penghasilan02</td>\r\n<td align=center>Gaji02</td>   \r\n<td align=center>Tunj02</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".02</td>    \r\n<td align=center>Penghasilan03</td>\r\n<td align=center>Gaji03</td>   \r\n<td align=center>Tunj03</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".03</td>    \r\n<td align=center>Penghasilan04</td>\r\n<td align=center>Gaji04</td>   \r\n<td align=center>Tunj04</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".04</td>    \r\n<td align=center>Penghasilan05</td>\r\n<td align=center>Gaji05</td>   \r\n<td align=center>Tunj05</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".05</td>    \r\n<td align=center>Penghasilan06</td>\r\n<td align=center>Gaji06</td>   \r\n<td align=center>Tunj06</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".06</td>    \r\n<td align=center>Penghasilan07</td>\r\n<td align=center>Gaji07</td>   \r\n<td align=center>Tunj07</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".07</td>    \r\n<td align=center>Penghasilan08</td>\r\n<td align=center>Gaji08</td>   \r\n<td align=center>Tunj08</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".08</td>    \r\n<td align=center>Penghasilan09</td>\r\n<td align=center>Gaji09</td>   \r\n<td align=center>Tunj09</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".09</td>    \r\n<td align=center>Penghasilan10</td>\r\n<td align=center>Gaji10</td>   \r\n<td align=center>Tunj10</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".10</td>    \r\n<td align=center>Penghasilan11</td>\r\n<td align=center>Gaji11</td>   \r\n<td align=center>Tunj11</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".11</td>    \r\n<td align=center>Penghasilan12</td>\r\n<td align=center>Gaji12</td>   \r\n<td align=center>Tunj12</td>      \r\n<td align=center>".$_SESSION['lang']['pph12'].".12</td>    \r\n<td align=center>".$_SESSION['lang']['total']."</td>\r\n<td align=center>GajiTOT</td>   \r\n<td align=center>TunjTOT</td>      \r\n<td align=center>PPh21 Tahunan</td>    \r\n</tr>   \r\n</thead>\r\n<tbody>";
$str = 'select id, tipe from '.$dbname.".sdm_5tipekaryawan\r\n    ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kamusTipe[$bar->id] = $bar->tipe;
}
$kamusKar = [];
$str = 'select nik, karyawanid, namakaryawan, tipekaryawan, statuspajak, lokasitugas, subbagian,npwp from '.$dbname.".datakaryawan \r\n    where lokasitugas like '".$kodeorg."%' ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kamusKar[$bar->karyawanid]['nik'] = $bar->nik;
    $kamusKar[$bar->karyawanid]['nama'] = $bar->namakaryawan;
    $kamusKar[$bar->karyawanid]['tipe'] = $bar->tipekaryawan;
    $kamusKar[$bar->karyawanid]['status'] = $bar->statuspajak;
    $kamusKar[$bar->karyawanid]['lokasi'] = $bar->lokasitugas;
    $kamusKar[$bar->karyawanid]['bagian'] = $bar->subbagian;
    $kamusKar[$bar->karyawanid]['npwp'] = str_replace(' ', '', str_replace('.', '', $bar->npwp));
    if (!is_numeric($kamusKar[$bar->karyawanid]['npwp'])) {
        $kamusKar[$bar->karyawanid]['npwp'] = '';
    } else {
        if (0 < (int) ($kamusKar[$bar->karyawanid]['npwp']) && strlen(12 < (int) ($kamusKar[$bar->karyawanid]['npwp']))) {
        } else {
            $kamusKar[$bar->karyawanid]['npwp'] = $bar->npwp;
        }
    }
}
$plusJMS = 0;
$str = 'select value from '.$dbname.".sdm_ho_hr_jms_porsi where id='pph21'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $plusJMS = $bar->value;
}
$jabPersen = 0;
$jabMax = 0;
$str = 'select persen,max from '.$dbname.'.sdm_ho_pph21jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jabPersen = $bar->persen / 100;
    $jabMax = $bar->max * 12;
}
$ptkp = [];
$str = 'select id,value from '.$dbname.'.sdm_ho_pph21_ptkp';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $ptkp[$bar->id] = $bar->value;
}
$pphtarif = [];
$pphpercent = [];
$str = 'select level,percent,upto from '.$dbname.'.sdm_ho_pph21_kontribusi order by level';
$res = mysql_query($str);
$urut = 0;
while ($bar = mysql_fetch_object($res)) {
    $pphtarif[$urut] = $bar->upto;
    $pphpercent[$urut] = $bar->percent / 100;
    ++$urut;
}
$str = 'select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from '.$dbname.".sdm_gaji \r\n    where idkomponen=1 and periodegaji like '".$tahun."%'\r\n    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";
$res = mysql_query($str);
$dJMS = [];
while ($bar = mysql_fetch_object($res)) {
    $dJMS[$bar->karyawanid][$bar->bulan] = ($bar->gaji * $plusJMS) / 100;
    $dJMS[$bar->karyawanid]['gapok'][$bar->bulan] = $bar->gaji;
    $dJMS[$bar->karyawanid]['gptahunan'] += $bar->gaji;
}
$str = 'select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from '.$dbname.".sdm_gaji \r\n    where idkomponen in (select id from ".$dbname.".sdm_ho_component where pph21=1)\r\n    and periodegaji like '".$tahun."%'\r\n    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";
$res = mysql_query($str);
$dzKar = [];
$dzArr = [];
while ($bar = mysql_fetch_object($res)) {
    $dzKar[$bar->karyawanid] = $bar->karyawanid;
    $dzArr[$bar->karyawanid]['karyawanid'] = $bar->karyawanid;
    $dzArr[$bar->karyawanid][$bar->bulan] = $bar->gaji;
    $dzArr[$bar->karyawanid]['total'] += $bar->gaji;
    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan] = ($bar->gaji + $dJMS[$bar->karyawanid][$bar->bulan]) * 12;
    $dzArr[$bar->karyawanid]['byjab'][$bar->bulan] = $jabPersen * $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan];
    if ($jabMax < $dzArr[$bar->karyawanid]['byjab'][$bar->bulan]) {
        $dzArr[$bar->karyawanid]['byjab'][$bar->bulan] = $jabMax;
    }

    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan] = $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan] - $dzArr[$bar->karyawanid]['byjab'][$bar->bulan];
    $dzArr[$bar->karyawanid]['pkp'][$bar->bulan] = $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan] - $ptkp[str_replace('K', '', $kamusKar[$bar->karyawanid]['status'])];
    $zz = 0;
    $sisazz = 0;
    if (0 < $dzArr[$bar->karyawanid]['pkp'][$bar->bulan]) {
        if ($dzArr[$bar->karyawanid]['pkp'][$bar->bulan] < $pphtarif[0]) {
            $zz += $pphpercent[0] * $dzArr[$bar->karyawanid]['pkp'][$bar->bulan];
            $sisazz = 0;
        } else {
            if ($pphtarif[0] <= $dzArr[$bar->karyawanid]['pkp'][$bar->bulan]) {
                $zz += $pphpercent[0] * $pphtarif[0];
                $sisazz = $dzArr[$bar->karyawanid]['pkp'][$bar->bulan] - $pphtarif[0];
                if ($sisazz < $pphtarif[1] - $pphtarif[0]) {
                    $zz += $pphpercent[1] * $sisazz;
                    $sisazz = 0;
                } else {
                    if ($pphtarif[1] - $pphtarif[0] <= $sisazz) {
                        $zz += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
                        $sisazz = $dzArr[$bar->karyawanid]['pkp'][$bar->bulan] - $pphtarif[1];
                        if ($sisazz < $pphtarif[2] - $pphtarif[1]) {
                            $zz += $pphpercent[2] * $sisazz;
                            $sisazz = 0;
                        } else {
                            if ($pphtarif[2] - $pphtarif[1] <= $sisazz) {
                                $zz += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
                                $sisazz = $dzArr[$bar->karyawanid]['pkp'][$bar->bulan] - $pphtarif[2];
                                if (0 < $sisazz) {
                                    $zz += $pphpercent[3] * $sisazz;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $dzArr[$bar->karyawanid]['pph21'][$bar->bulan] = $zz / 12;
    if ('' == $kamusKar[$bar->karyawanid]['npwp']) {
        $dzArr[$bar->karyawanid]['pph21'][$bar->bulan] = $dzArr[$bar->karyawanid]['pph21'][$bar->bulan] + ($dzArr[$bar->karyawanid]['pph21'][$bar->bulan] * 20) / 100;
    }
}
$no = 0;
if (!empty($dzKar)) {
    foreach ($dzKar as $karid) {
        ++$no;
        $stream .= "<tr class=rowcontent>\r\n    <td align=right>".$no.'</td>';
        if ('' != $kamusKar[$karid]['bagian']) {
            $stream .= '<td align=left>'.$kamusKar[$karid]['bagian'].'</td>';
        } else {
            $stream .= '<td align=left>'.$kamusKar[$karid]['lokasi'].'</td>';
        }

        $stream .= '<td align=left>'.$kamusKar[$karid]['nik']."</td>\r\n    <td align=left>".$kamusKar[$karid]['nama']."</td>\r\n    <td align=left>".$kamusTipe[$kamusKar[$karid]['tipe']]."</td>\r\n    <td align=left>".$kamusKar[$karid]['status']."</td>\r\n    <td align=left>".$kamusKar[$karid]['npwp']."</td>\r\n    <td align=center>".$tahun."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['01'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok']['01'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['01'] - $dJMS[$karid]['gapok']['01'], 0)."</td>    \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21']['01'])."</td>        \r\n    <td align=right>".number_format($dzArr[$karid]['02'])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok']['02'], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid]['02'] - $dJMS[$karid]['gapok']['02'], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21']['02'])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['03'])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok']['03'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['03'] - $dJMS[$karid]['gapok']['03'], 0)."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21']['03'])."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['04'])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok']['04'], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid]['04'] - $dJMS[$karid]['gapok']['04'], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21']['04'])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['05'])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok']['05'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['05'] - $dJMS[$karid]['gapok']['05'], 0)."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21']['05'])."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['06'])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok']['06'], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid]['06'] - $dJMS[$karid]['gapok']['06'], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21']['06'])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['07'])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok']['07'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['07'] - $dJMS[$karid]['gapok']['07'], 0)."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21']['07'])."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['08'])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok']['08'], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid]['08'] - $dJMS[$karid]['gapok']['08'], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21']['08'])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['09'])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok']['09'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['09'] - $dJMS[$karid]['gapok']['09'], 0)."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21']['09'])."</td>         \r\n    <td align=right>".number_format($dzArr[$karid][10])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok'][10], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid][10] - $dJMS[$karid]['gapok'][10], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21'][10])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid][11])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gapok'][11], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid][11] - $dJMS[$karid]['gapok'][11], 0)."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['pph21'][11])."</td>         \r\n    <td align=right>".number_format($dzArr[$karid][12])."</td>\r\n    <td align=right>".number_format($dJMS[$karid]['gapok'][12], 0)."</td>\r\n    <td align=right>".number_format($dzArr[$karid][12] - $dJMS[$karid]['gapok'][12], 0)."</td>         \r\n    <td align=right>".number_format($dzArr[$karid]['pph21'][12])."</td>         \r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['total'])."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dJMS[$karid]['gptahunan'], 0)."</td>\r\n    <td align=right style='color:#0404B4;'>".number_format($dzArr[$karid]['total'] - $dJMS[$karid]['gptahunan'], 0).'</td>';
        $dzArr[$karid]['tpenghasilan'] = $dzArr[$karid]['total'] + ($dJMS[$karid]['gptahunan'] * $plusJMS) / 100;
        $dzArr[$karid]['tbyjab'] = $jabPersen * $dzArr[$karid]['tpenghasilan'];
        if ($jabMax < $dzArr[$karid]['tbyjab']) {
            $dzArr[$karid]['tbyjab'] = $jabMax;
        }

        $dzArr[$karid]['tpenghasilan'] = $dzArr[$karid]['tpenghasilan'] - $dzArr[$karid]['tbyjab'];
        $dzArr[$karid]['tpkp'] = $dzArr[$karid]['tpenghasilan'] - $ptkp[str_replace('K', '', $kamusKar[$karid]['status'])];
        $zz = 0;
        $sisazz = 0;
        if (0 < $dzArr[$karid]['tpkp']) {
            if ($dzArr[$karid]['tpkp'] < $pphtarif[0]) {
                $zz += $pphpercent[0] * $dzArr[$karid]['tpkp'];
                $sisazz = 0;
            } else {
                if ($pphtarif[0] <= $dzArr[$karid]['tpkp']) {
                    $zz += $pphpercent[0] * $pphtarif[0];
                    $sisazz = $dzArr[$karid]['tpkp'] - $pphtarif[0];
                    if ($sisazz < $pphtarif[1] - $pphtarif[0]) {
                        $zz += $pphpercent[1] * $sisazz;
                        $sisazz = 0;
                    } else {
                        if ($pphtarif[1] - $pphtarif[0] <= $sisazz) {
                            $zz += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
                            $sisazz = $dzArr[$karid]['tpkp'] - $pphtarif[1];
                            if ($sisazz < $pphtarif[2] - $pphtarif[1]) {
                                $zz += $pphpercent[2] * $sisazz;
                                $sisazz = 0;
                            } else {
                                if ($pphtarif[2] - $pphtarif[1] <= $sisazz) {
                                    $zz += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
                                    $sisazz = $dzArr[$karid]['tpkp'] - $pphtarif[2];
                                    if (0 < $sisazz) {
                                        $zz += $pphpercent[3] * $sisazz;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $dzArr[$karid]['tpph21'] = $zz;
        if ('' == $kamusKar[$bar->karyawanid]['npwp']) {
            $dzArr[$karid]['tpph21'] = $dzArr[$karid]['tpph21'] + ($dzArr[$karid]['tpph21'] * 20) / 100;
        }

        $stream .= "<td align=right  style='color:#0404B4;'>".number_format($dzArr[$karid]['tpph21'])."</td>\r\n    </tr>";
        $total['pph01'] += $dzArr[$karid]['pph21']['01'];
        $total['pph02'] += $dzArr[$karid]['pph21']['02'];
        $total['pph03'] += $dzArr[$karid]['pph21']['03'];
        $total['pph04'] += $dzArr[$karid]['pph21']['04'];
        $total['pph05'] += $dzArr[$karid]['pph21']['05'];
        $total['pph06'] += $dzArr[$karid]['pph21']['06'];
        $total['pph07'] += $dzArr[$karid]['pph21']['07'];
        $total['pph08'] += $dzArr[$karid]['pph21']['08'];
        $total['pph09'] += $dzArr[$karid]['pph21']['09'];
        $total['pph10'] += $dzArr[$karid]['pph21'][10];
        $total['pph11'] += $dzArr[$karid]['pph21'][11];
        $total['pph12'] += $dzArr[$karid]['pph21'][12];
        $total['01'] += $dzArr[$karid]['01'];
        $total['02'] += $dzArr[$karid]['02'];
        $total['03'] += $dzArr[$karid]['03'];
        $total['04'] += $dzArr[$karid]['04'];
        $total['05'] += $dzArr[$karid]['05'];
        $total['06'] += $dzArr[$karid]['06'];
        $total['07'] += $dzArr[$karid]['07'];
        $total['08'] += $dzArr[$karid]['08'];
        $total['09'] += $dzArr[$karid]['09'];
        $total[10] += $dzArr[$karid][10];
        $total[11] += $dzArr[$karid][11];
        $total[12] += $dzArr[$karid][12];
        $total['total'] += $dzArr[$karid]['total'];
        $total['pph'] += $dzArr[$karid]['tpph21'];
        $tgapok['01'] += $dJMS[$karid]['gapok']['01'];
        $tgapok['02'] += $dJMS[$karid]['gapok']['02'];
        $tgapok['03'] += $dJMS[$karid]['gapok']['03'];
        $tgapok['04'] += $dJMS[$karid]['gapok']['04'];
        $tgapok['05'] += $dJMS[$karid]['gapok']['05'];
        $tgapok['06'] += $dJMS[$karid]['gapok']['06'];
        $tgapok['07'] += $dJMS[$karid]['gapok']['07'];
        $tgapok['08'] += $dJMS[$karid]['gapok']['08'];
        $tgapok['09'] += $dJMS[$karid]['gapok']['09'];
        $tgapok[10] += $dJMS[$karid]['gapok'][10];
        $tgapok[11] += $dJMS[$karid]['gapok'][11];
        $tgapok[12] += $dJMS[$karid]['gapok'][12];
        $tgapok['total'] += $dJMS[$karid]['gptahunan'];
        $ttj['01'] += $dzArr[$karid]['01'] - $dJMS[$karid]['gapok']['01'];
        $ttj['02'] += $dzArr[$karid]['02'] - $dJMS[$karid]['gapok']['02'];
        $ttj['03'] += $dzArr[$karid]['03'] - $dJMS[$karid]['gapok']['03'];
        $ttj['04'] += $dzArr[$karid]['04'] - $dJMS[$karid]['gapok']['04'];
        $ttj['05'] += $dzArr[$karid]['05'] - $dJMS[$karid]['gapok']['05'];
        $ttj['06'] += $dzArr[$karid]['06'] - $dJMS[$karid]['gapok']['06'];
        $ttj['07'] += $dzArr[$karid]['07'] - $dJMS[$karid]['gapok']['07'];
        $ttj['08'] += $dzArr[$karid]['08'] - $dJMS[$karid]['gapok']['08'];
        $ttj['09'] += $dzArr[$karid]['09'] - $dJMS[$karid]['gapok']['09'];
        $ttj[10] += $dzArr[$karid][10] - $dJMS[$karid]['gapok'][10];
        $ttj[11] += $dzArr[$karid][11] - $dJMS[$karid]['gapok'][11];
        $ttj[12] += $dzArr[$karid][12] - $dJMS[$karid]['gapok'][12];
        $ttj['total'] += $dzArr[$karid]['total'] - $dJMS[$karid]['gptahunan'];
    }
}

$stream .= "<tr class=title>\r\n<td colspan=8 align=center>Total</td>\r\n<td align=right>".number_format($total['01'])."</td>\r\n<td align=right>".number_format($tgapok['01'])."</td>    \r\n<td align=right>".number_format($ttj['01'])."</td>     \r\n<td align=right>".number_format($total['pph01'])."</td>    \r\n<td align=right>".number_format($total['02'])."</td>   \r\n<td align=right>".number_format($tgapok['02'])."</td>    \r\n<td align=right>".number_format($ttj['02'])."</td>       \r\n<td align=right>".number_format($total['pph02'])."</td>     \r\n<td align=right>".number_format($total['03'])."</td>\r\n<td align=right>".number_format($tgapok['03'])."</td>    \r\n<td align=right>".number_format($ttj['03'])."</td>       \r\n<td align=right>".number_format($total['pph03'])."</td>     \r\n<td align=right>".number_format($total['04'])."</td>\r\n<td align=right>".number_format($tgapok['04'])."</td>    \r\n<td align=right>".number_format($ttj['04'])."</td>       \r\n<td align=right>".number_format($total['pph04'])."</td>     \r\n<td align=right>".number_format($total['05'])."</td>\r\n<td align=right>".number_format($tgapok['05'])."</td>    \r\n<td align=right>".number_format($ttj['05'])."</td>       \r\n<td align=right>".number_format($total['pph05'])."</td>     \r\n<td align=right>".number_format($total['06'])."</td>\r\n<td align=right>".number_format($tgapok['06'])."</td>    \r\n<td align=right>".number_format($ttj['06'])."</td>       \r\n<td align=right>".number_format($total['pph06'])."</td>     \r\n<td align=right>".number_format($total['07'])."</td>\r\n<td align=right>".number_format($tgapok['07'])."</td>    \r\n<td align=right>".number_format($ttj['07'])."</td>       \r\n<td align=right>".number_format($total['pph07'])."</td>     \r\n<td align=right>".number_format($total['08'])."</td>\r\n<td align=right>".number_format($tgapok['08'])."</td>    \r\n<td align=right>".number_format($ttj['08'])."</td>       \r\n<td align=right>".number_format($total['pph08'])."</td>     \r\n<td align=right>".number_format($total['09'])."</td>\r\n<td align=right>".number_format($tgapok['09'])."</td>    \r\n<td align=right>".number_format($ttj['09'])."</td>       \r\n<td align=right>".number_format($total['pph09'])."</td>     \r\n<td align=right>".number_format($total[10])."</td>\r\n<td align=right>".number_format($tgapok[10])."</td>    \r\n<td align=right>".number_format($ttj[10])."</td>       \r\n<td align=right>".number_format($total['pph10'])."</td>     \r\n<td align=right>".number_format($total[11])."</td>\r\n<td align=right>".number_format($tgapok[11])."</td>    \r\n<td align=right>".number_format($ttj[11])."</td>       \r\n<td align=right>".number_format($total['pph11'])."</td>     \r\n<td align=right>".number_format($total[12])."</td>\r\n<td align=right>".number_format($tgapok[12])."</td>    \r\n<td align=right>".number_format($ttj[12])."</td>       \r\n<td align=right>".number_format($total['pph12'])."</td>     \r\n<td align=right>".number_format($total['total'])."</td>\r\n<td align=right>".number_format($tgapok['total'])."</td>    \r\n<td align=right>".number_format($ttj['total'])."</td>        \r\n<td align=right>".number_format($total['pph']).'</td></tr>';
$stream .= '</tbody></table>';
if ('preview' == $proses) {
    echo $stream;
}

if ('excel' == $proses) {
    $stream .= '</table><br>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
    $dte = date('YmdHms');
    $nop_ = 'pph21_'.$kodeorg.'_'.$tahun.'_'.$dte;
    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
    gzwrite($gztralala, $stream);
    gzclose($gztralala);
    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
}

?>