<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_GET;
switch ($param['jenis']) {
    case 'global':
        $kodeorg = $param['pks'];
        $periode = $param['tahun'];
        $optnmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
        $optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
        $tab .= 'Stok Bibit Per :'.$periode."\n                  <table cellpadding=1 cellspacing=1 border=0 class=sortable>\n                <thead>\n                <tr class=rowheader>\n                <td>".substr($_SESSION['lang']['nomor'], 0, 2)."</td>\n                <td>".$_SESSION['lang']['batch']."</td>\n                <td>".$_SESSION['lang']['kodeorg']."</td>\n                <td bgcolor=#FFFFFF>".$_SESSION['lang']['saldo']."</td>\n                <td>".$_SESSION['lang']['jenisbibit']."</td>\n                 <td>".$_SESSION['lang']['tgltanam']."</td>   \n                <td>".$_SESSION['lang']['umur'].' '.substr($_SESSION['lang']['afkirbibit'], 5).'('.$_SESSION['lang']['bulan'].")</td>\n                <td>".$_SESSION['lang']['doubletoon']."</td>\n                <td>".$_SESSION['lang']['afkirbibit']."</td>\n                <td>%".$_SESSION['lang']['afkirbibit']."</td>    \n                <td>".$_SESSION['lang']['pengiriman']."</td>    \n                </tr>\n                </thead><tbody id=containDataStock>";
        if ('' !== $kodeorg) {
            $where .= " and  kodeorg like '%".$kodeorg."%'";
        }

        $str = 'select batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi  where tanggal<='".$periode."-31'".$where." and kodetransaksi='AFB' group by batch,kodeorg order by batch desc ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $afb[$bar->batch][$bar->kodeorg] = abs($bar->jumlah);
        }
        $str = 'select batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi  where tanggal<='".$periode."-31'".$where." and kodetransaksi='DBT' group by batch,kodeorg order by batch desc ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $dbt[$bar->batch][$bar->kodeorg] = abs($bar->jumlah);
        }
        $str = 'select batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi  where tanggal<='".$periode."-31'".$where." and kodetransaksi='PNB' group by batch,kodeorg order by batch desc ";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $pnb[$bar->batch][$bar->kodeorg] = abs($bar->jumlah);
        }
        $sData = 'select batch,kodeorg,sum(jumlah) as jumlah from '.$dbname.".bibitan_mutasi  where tanggal<='".$periode."-31'".$where.' group by batch,kodeorg order by batch desc ';
        $qData = mysql_query($sData) || exit(mysql_error($conns));
        while ($rData = mysql_fetch_assoc($qData)) {
            $data = '';
            $sDatabatch = 'select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from '.$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
            $qDataBatch = mysql_query($sDatabatch) || exit(mysql_error($sDatabatch));
            $rDataBatch = mysql_fetch_assoc($qDataBatch);
            $thnData = substr($rDataBatch['tanggaltanam'], 0, 4);
            $starttime = strtotime($rDataBatch['tanggaltanam']);
            if (date('Ymd') < str_replace('-', '', $periode).'31') {
                $endtime = time();
            } else {
                $endtime = mktime(0, 0, 0, substr($periode, 5, 2), 31, substr($periode, 0, 4));
            }

            $jmlHari = ($endtime - $starttime) / (60 * 60 * 24 * 30);
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['batch'].'</td>';
            $tab .= '<td>'.$optNm[$rData['kodeorg']].'</td>';
            $tab .= '<td align=right bgcolor=#FFFFFF>'.number_format($rData['jumlah'], 0).'</td>';
            $tab .= '<td>'.$rDataBatch['jenisbibit'].'</td>';
            $tab .= '<td>'.tanggalnormal($rDataBatch['tanggaltanam']).'</td>';
            $tab .= '<td align=right bgcolor=#FFFFFF>'.number_format($jmlHari, 2).'</td>';
            $tab .= '<td align=right>'.number_format($dbt[$rData['batch']][$rData['kodeorg']])."</td>\n                                    <td align=right>".number_format($afb[$rData['batch']][$rData['kodeorg']])."</td>\n                                    <td align=right>".number_format($afb[$rData['batch']][$rData['kodeorg']] / ($rData['jumlah'] + $pnb[$rData['batch']][$rData['kodeorg']] + $dbt[$rData['batch']][$rData['kodeorg']]) * 100, 2)."</td>    \n                                    <td align=right>".number_format($pnb[$rData['batch']][$rData['kodeorg']]).'</td> ';
            $tab .= '</tr>';
            $total += $rData['jumlah'];
            $tafb += $afb[$rData['batch']][$rData['kodeorg']];
            $tpnb += $pnb[$rData['batch']][$rData['kodeorg']];
            $tdbt += $dbt[$rData['batch']][$rData['kodeorg']];
        }
        $tab .= '<tr class=rowcontent><td colspan=3>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right bgcolor=#FFFFFF>'.number_format($total)."</td><td colspan=3></td>\n                                 <td align=right>".number_format($tdbt, 0)."</td>\n                                 <td align=right>".number_format($tafb, 0)."</td>\n                                  <td></td>\n                                  <td align=right>".number_format($tpnb, 0)."</td>\n                                </tr>";
        $tab .= '</tbody></table>';
        echo "<link rel=stylesheet tyle=text href='style/generic.css'>\n                              <script language=javascript src='js/generic.js'></script>";
        echo $tab;

        break;
}

?>