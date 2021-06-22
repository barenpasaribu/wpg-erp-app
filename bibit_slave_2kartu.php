<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' === $_POST['kodeunit'] ? ($kodeunit = $_GET['kodeunit']) : ($kodeunit = $_POST['kodeunit']));
('' === $_POST['kodebatch'] ? ($kodebatch = $_GET['kodebatch']) : ($kodebatch = $_POST['kodebatch']));
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
if ('' === $kodeunit) {
    exit('Error: Unit code required.'.$kodeunit);
}

$where = '';
if ('' !== $kodeunit) {
    $where = " b.kodeorg like '%".$kodeunit."%'";
}

if ('' !== $kodebatch) {
    $where .= " and a.batch='".$kodebatch."'";
}

$adadata = false;
$str = 'select distinct a.batch from '.$dbname.".bibitan_batch a\r\n    left join ".$dbname.".bibitan_mutasi b on a.batch=b.batch\r\n    where ".$where."\r\n    order by a.batch desc";
if ('excel' === $proses) {
    $border = 1;
    $bg = " bgcolor='#dedede'";
} else {
    $border = 0;
    $bg = ' ';
}

$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $adadata = true;
    $tab .= $_SESSION['lang']['batch'].' : '.$bar->batch.'<br>';
    if ('EN' === $_SESSION['language']) {
        $tab .= 'A. SEED SELECTION and REJECTION'.'<br>';
    } else {
        $tab .= 'A. SELEKSI KECAMBAH'.'<br>';
    }

    $tab .= '<table cellpadding=1 cellspacing=1 border='.$border." class=sortable>\r\n    <thead>\r\n        <tr class=rowheader ".$bg.">\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['diterima']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['diterima']."</td>\r\n        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>\r\n        </tr><tr class=rowheader ".$bg.">    \r\n        <td align=center>".$_SESSION['lang']['jumlah']."</td>   \r\n        <td align=center>%</td>\r\n        </tr>\r\n    </thead><tbody id=containdata>";
    $no = 0;
    $sData = "select * \r\n        from ".$dbname.".bibitan_batch_vw where kodeorg like '%".$kodeunit."%' and batch = '".$bar->batch."' \r\n        ";
    $qData = mysql_query($sData) || exit(mysql_error($conns));
    while ($rData = mysql_fetch_assoc($qData)) {
        ++$no;
        $persen = (100 * $rData['jumlahafkir']) / $rData['jumlahterima'];
        $ditanam = $rData['jumlahterima'] - $rData['jumlahafkir'];
        $tab .= '<tr class=rowcontent>';
        if ('excel' === $proses) {
            $tampiltanggal = $rData['tanggal'];
        } else {
            $tampiltanggal = tanggalnormal($rData['tanggal']);
        }

        $tab .= '<td align=center>'.$tampiltanggal.'</td>';
        $tab .= '<td align=right>'.number_format($rData['jumlahterima']).'</td>';
        $tab .= '<td align=right>'.number_format($rData['jumlahafkir']).'</td>';
        $tab .= '<td>'.number_format($persen, 2).'</td>';
        $tab .= '<td align=right>'.number_format($ditanam).'</td>';
        $tab .= '</tr>';
        $terimaDt += $rData['jumlahterima'];
        $afkirDt += $rData['jumlahafkir'];
        $dataa[$rData['tanggal']]['tanam'] += $ditanam;
    }
    if (0 === $no) {
        $tab .= '<tr class=rowcontent><td colspan=5>No data.</td></tr>';
    }

    $tab .= '<tr class=rowcontent><td>'.$_SESSION['lang']['total'].'</td>';
    $tab .= '<td align=right>'.number_format($terimaDt).'</td>';
    $tab .= '<td align=right>'.number_format(abs($afkirDt)).'</td>';
    $tab .= '<td colspan=2></td></tr>';
    $tab .= '</tbody></table></br>';
    $tab .= 'B. PRE NURSERY'.'<br>';
    $datab = [];
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$border." class=sortable>\r\n    <thead>\r\n        <tr class=rowheader ".$bg.">\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['transplatingbibit']."</td>\r\n        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['saldo']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>\r\n        </tr><tr class=rowheader ".$bg.">    \r\n        <td align=center>".$_SESSION['lang']['jumlah']."</td>   \r\n        <td align=center>%</td>\r\n        </tr>\r\n    </thead><tbody id=containdata>";
    $sData = "select * \r\n        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodeorg like '%PN%'  and post=1\r\n        order by tanggal asc";
    $qData = mysql_query($sData) || exit(mysql_error($conns));
    while ($rData = mysql_fetch_assoc($qData)) {
        $datab[$rData['tanggal']]['tanggal'] = $rData['tanggal'];
        if ('TPB' === $rData['kodetransaksi']) {
            $datab[$rData['tanggal']]['TPB'] += $rData['jumlah'];
        } else {
            if ('AFB' === $rData['kodetransaksi']) {
                $datab[$rData['tanggal']]['AFB'] += $rData['jumlah'];
            } else {
                $datab[$rData['tanggal']]['TMB'] += $rData['jumlah'];
            }
        }
    }
    $no = 0;
    if (!empty($datab)) {
        foreach ($datab as $data) {
            ++$no;
            $persen = (100 * $data['AFB']) / $data['TMB'];
            $saldo += $data['TMB'] + $data['TPB'] + $data['AFB'];
            $tab .= '<tr class=rowcontent>';
            if ('excel' === $proses) {
                $tampiltanggal = $data['tanggal'];
            } else {
                $tampiltanggal = tanggalnormal($data['tanggal']);
            }

            $tab .= '<td align=center>'.$tampiltanggal.'</td>';
            $tab .= '<td align=right>'.number_format($data['TMB']).'</td>';
            $tab .= '<td align=right>'.number_format($data['TPB']).'</td>';
            $tab .= '<td align=right>'.number_format($data['AFB']).'</td>';
            $tab .= '<td>'.number_format($persen, 2).'</td>';
            $tab .= '<td align=right>'.number_format($saldo).'</td>';
            $tab .= '<td></td>';
            $tab .= '</tr>';
            $dtmb += $data['TMB'];
            $dtpb += $data['PNB'];
            $afbd += $data['AFB'];
        }
    } else {
        $tab .= '<tr class=rowcontent><td colspan=7>No data.</td></tr>';
    }

    $tab .= '<tr class=rowcontent><td >'.$_SESSION['lang']['total'].'</td>';
    $tab .= '<td align=right>'.number_format($dtmb).'</td>';
    $tab .= '<td align=right>'.number_format(abs($dtpb)).'</td>';
    $tab .= '<td align=right>'.number_format(abs($afbd)).'</td><td colspan=3></td></tr>';
    $tab .= '</tbody></table></br>';
    $tab .= 'C. MAIN NURSERY'.'<br>';
    $datac = [];
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$border." class=sortable>\r\n    <thead>\r\n        <tr class=rowheader ".$bg.">\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['pengiriman']."</td>\r\n        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['saldo']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['almt_kirim']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>\r\n        </tr><tr class=rowheader ".$bg.">    \r\n        <td align=center>".$_SESSION['lang']['jumlah']."</td>   \r\n        <td align=center>%</td>\r\n        </tr>\r\n    </thead><tbody id=containdata>";
    $sData = "select * \r\n        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodeorg like '%MN%' and post=1\r\n        order by tanggal asc";
    $qData = mysql_query($sData) || exit(mysql_error($conns));
    while ($rData = mysql_fetch_assoc($qData)) {
        $datac[$rData['tanggal']]['tanggal'] = $rData['tanggal'];
        if ('TPB' === $rData['kodetransaksi']) {
            $datac[$rData['tanggal']]['TPB'] += $rData['jumlah'];
        } else {
            if ('AFB' === $rData['kodetransaksi']) {
                $datac[$rData['tanggal']]['AFB'] += $rData['jumlah'];
            } else {
                if ('PNB' === $rData['kodetransaksi']) {
                    $datac[$rData['tanggal']]['PNB'] += $rData['jumlah'];
                } else {
                    $datac[$rData['tanggal']]['TMB'] += $rData['jumlah'];
                }
            }
        }

        $datac[$rData['tanggal']]['lokasi'] .= ' '.$rData['lokasipengiriman'];
        $datac[$rData['tanggal']]['kodeorg'] = $rData['kodeorg'];
    }
    $no = 0;
    if (!empty($datac)) {
        foreach ($datac as $data) {
            ++$no;
            $saldo += $data['TMB'] + $data['TPB'] + $data['AFB'] + $data['PNB'];
            $persen = $data['AFB'] / $saldo * 100;
            $tab .= '<tr class=rowcontent>';
            if ('excel' === $proses) {
                $tampiltanggal = $data['tanggal'];
            } else {
                $tampiltanggal = tanggalnormal($data['tanggal']);
            }

            $tab .= '<td align=center>'.$tampiltanggal.'</td>';
            $tab .= '<td align=left>'.$data['kodeorg'].'</td>';
            $tab .= '<td align=right>'.number_format(abs($data['TMB'])).'</td>';
            $tab .= '<td align=right>'.number_format(abs($data['PNB'])).'</td>';
            $tab .= '<td align=right>'.number_format(abs($data['AFB'])).'</td>';
            $tab .= '<td>'.number_format($persen, 2).'</td>';
            $tab .= '<td align=right>'.number_format($saldo).'</td>';
            $tab .= '<td>'.$data['lokasi'].'</td>';
            $tab .= '<td></td>';
            $tab .= '</tr>';
            $dtnm += $data['TMB'];
            $dkirim += $data['PNB'];
            $dafb += $data['AFB'];
        }
    } else {
        $tab .= '<tr class=rowcontent><td colspan=8>No data.</td></tr>';
    }

    $tab .= '<tr class=rowcontent><td colspan=2>'.$_SESSION['lang']['total'].'</td>';
    $tab .= '<td align=right>'.number_format($dtnm).'</td>';
    $tab .= '<td align=right>'.number_format(abs($dkirim)).'</td>';
    $tab .= '<td align=right>'.number_format(abs($dafb)).'</td><td colspan=4></td></tr>';
    $tab .= '</tbody></table></br>';
    if ('EN' === $_SESSION['language']) {
        $tab .= 'D. REJECTION RECAP'.'<br>';
    } else {
        $tab .= 'D. REKAP SELEKSI BIBIT'.'<br>';
    }

    $datad = [];
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$border." class=sortable>\r\n    <thead>\r\n        <tr class=rowheader ".$bg.">\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['blok']."</td>\r\n        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>\r\n        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>\r\n        </tr><tr class=rowheader ".$bg.">    \r\n        <td align=center>".$_SESSION['lang']['jumlah']."</td>   \r\n        <td align=center>%</td>\r\n        </tr>\r\n    </thead><tbody id=containdata>";
    $sData = "select * \r\n        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodetransaksi = 'AFB'  and post=1\r\n        order by tanggal desc";
    $qData = mysql_query($sData) || exit(mysql_error($conns));
    while ($rData = mysql_fetch_assoc($qData)) {
        $datad[$rData['tanggal']]['tanggal'] = $rData['tanggal'];
        if ('TPB' === $rData['kodetransaksi']) {
            $datad[$rData['tanggal']]['TPB'] += $rData['jumlah'];
        } else {
            if ('AFB' === $rData['kodetransaksi']) {
                $datad[$rData['tanggal']]['AFB'] += $rData['jumlah'];
            } else {
                $datad[$rData['tanggal']]['TMB'] += $rData['jumlah'];
            }
        }

        $datad[$rData['tanggal']]['blok'] .= ' '.$rData['kodeorg'];
        $datad[$rData['tanggal']]['ket'] .= ' '.$rData['keterangan'];
    }
    $no = 0;
    if (!empty($datad)) {
        foreach ($datad as $data) {
            ++$no;
            $saldo += $data['TMB'] + $data['TPB'] + $data['AFB'] + $data['PNB'];
            $persen = $data['AFB'] / $saldo * 100;
            $tab .= '<tr class=rowcontent>';
            if ('excel' === $proses) {
                $tampiltanggal = $data['tanggal'];
            } else {
                $tampiltanggal = tanggalnormal($data['tanggal']);
            }

            $tab .= '<td align=center>'.$tampiltanggal.'</td>';
            $tab .= '<td align=right>'.$data['blok'].'</td>';
            $tab .= '<td align=right>'.number_format(abs($data['AFB'])).'</td>';
            $tab .= '<td>'.number_format($persen, 2).'</td>';
            $tab .= '<td>'.$data['ket'].'</td>';
            $tab .= '</tr>';
            $afdData += $data['AFB'];
        }
    } else {
        $tab .= '<tr class=rowcontent><td colspan=5>No data.</td></tr>';
    }

    $tab .= '<tr class=rowcontent><td colspan=2>'.$_SESSION['lang']['total'].'</td>';
    $tab .= '<td>'.number_format(abs($afdData)).'</td><td colspan=2></td></tr>';
    $tab .= '</tbody></table><br>';
}
if (!$adadata) {
    $tab = 'No data.';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= '<br>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'kartubibit_'.$kodeunit.'.'.$kodebatch;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>