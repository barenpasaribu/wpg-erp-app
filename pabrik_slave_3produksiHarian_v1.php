<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';

    $where = null;
    if (!empty($_POST['pabrik'])) {
        $where = " AND kodeorg = '".$_POST['pabrik']."'";
    }

    $queryGetPabrikProduksi = "SELECT * FROM pabrik_produksi WHERE tanggal like '".$_POST['periode']."%' $where ORDER BY tanggal DESC";
    $hasilGetPabrikProduksi = fetchData($queryGetPabrikProduksi);
    

?>
<style>
    td{
        text-align:center;
    }
</style>
<table class="sortable" cellspacing="1" style='width:100%;'>
    <thead>
        <tr class="rowheader">
            <td>Kode Organisasi</td>
            <td>Tanggal</td>
            <td>TBS Sisa Kemarin</td>
            <td>TBS diolah</td>
            <td>TBS Sisa</td>
            <td>Action</td>
        </tr>  
    </thead>
    <tbody>
        <?php
            if (empty($hasilGetPabrikProduksi)) {
        ?>
        <tr>
            <td colspan="6">
        <?php
                echo "Tidak ada data.";
            }
        ?>
            </td>
        </tr>
        <?php 
            foreach ($hasilGetPabrikProduksi as $key => $value) {
        
        ?>
        <tr>
            <td><?= $value['kodeorg'] ?></td>
            <td><?= $value['tanggal'] ?></td>
            <td><?= $value['tbs_sisa_kemarin'] ?></td>
            <td><?= $value['tbs_diolah'] ?></td>
            <td><?= $value['tbs_sisa'] ?></td>
            <td>
            <?php 
            /*
            <!-- <img onclick="cetakPDF('<?= $value['kodeorg'] ?>','<?= $value['tanggal'] ?>')" src="images/skyblue/pdf.jpg" class="resicon" title="PDF">  -->
            */
            ?>
            <img onclick="laporanEXCEL('<?= $value['kodeorg'] ?>','<?= $value['tanggal'] ?>')" src="images/excel.jpg" class="resicon" title="PDF"> 
            </td>
        </tr>  
        <?php } ?>
    </tbody>
    <tfoot>
    </tfoot>
</table>
<?php
die();

('' !== $_POST['periode'] ? ($periode = $_POST['periode']) : ($periode = $_GET['periode']));
('' !== $_POST['tampil'] ? ($tampil = $_POST['tampil']) : ($tampil = $_GET['tampil']));
('' !== $_POST['pabrik'] ? ($pabrik = $_POST['pabrik']) : ($pabrik = $_GET['pabrik']));
$str = 'select * from '.$dbname.".pabrik_produksi where tanggal like '".$periode."%'
        and kodeorg='".$pabrik."'
        order by tanggal asc";
$res2 = mysql_query($str);
$res = mysql_query($str);
while ($datArr = mysql_fetch_assoc($res2)) {
    $tbs[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['tbsdiolah'];
    $jmOer[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['oer'];
    $jmOerPk[$datArr['kodeorg']][$datArr['tanggal']] = $datArr['oerpk'];
}
$sStart = ' select distinct tanggal,jammulai,jamselesai from '.$dbname.".pabrik_pengolahan 
            where kodeorg='".$pabrik."' 
            and 
            tanggal like '".$periode."%' 
            and 
            shift=1 
            order by tanggal asc";
$qStart = mysql_query($sStart);
while ($rStart = mysql_fetch_assoc($qStart)) {
    $jmStart[$rStart['tanggal']] = $rStart['jammulai'];
    $jmEnd[$rStart['tanggal']] = $rStart['jamselesai'];
}
$sStart = ' select distinct tanggal,jammulai,jamselesai from '.$dbname.".pabrik_pengolahan 
            where kodeorg='".$pabrik."' 
            and 
            tanggal like '".$periode."%' 
            and 
            shift=2 
            order by tanggal asc";
$qStart = mysql_query($sStart);
while ($rStart = mysql_fetch_assoc($qStart)) {
    $jmEnd[$rStart['tanggal']] = $rStart['jamselesai'];
}
if ('excel' !== $_GET['method']) {
    $bg = '';
    $brdr = '0';
    echo '<fieldset><legend>'.$_SESSION['lang']['list']."\r\n\t     <img src='images/icons/Basic_set_Png/statistics_16.png' class=resicon title='Graphics'  onclick=grafikProduksi('".$periode."','".$tampil."','".$pabrik."',event)>\r\n\t\t\r\n\t    <img src='images/skyblue/excel.jpg' class=resicon title='Spreadsheet' onclick=laporanEXCEL('".$periode."','".$tampil."','".$pabrik."',event)>      \r\n            </legend>";
    $komanya = 2;
} else {
    $bg = ' bgcolor=#DEDEDE';
    $brdr = '1';
    $komanya = 5;
}

$tab .= "
            <table class=sortable cellspacing=1 border=".$brdr." style='width:1500px;'>
                <thead>
                    <tr class=rowheader>
                        <td rowspan=2 align=center ".$bg.'>'.$_SESSION['lang']['kodeorganisasi']."</td>
                        <td rowspan=2 align=center ".$bg.' width=100px>'.$_SESSION['lang']['tanggal']."</td>
                        <td rowspan=2 align=center ".$bg.'>'."Action</td>
                    </tr>  
                </thead>
                <tbody>";
$tgl = 1;
$cposdkem = 0;
$ffasdkem = 0;
$kotsdkem = 0;
$airsdkem = 0;
$kersdkem = 0;
$ffksdkem = 0;
$koksdkem = 0;
for ($aiksdkem = 0; $bar = mysql_fetch_object($res); ++$tgl) {
    ++$ared;
    $aOlah = 'select sum(jamdinasbruto) as jampengolahan, sum(jamstagnasi) as jamstagnasi from '.$dbname.".pabrik_pengolahan \r\n               where kodeorg='".$bar->kodeorg."' and tanggal='".$bar->tanggal."'";
    $bOlah = mysql_query($aOlah);
    $cOlah = mysql_fetch_assoc($bOlah);
    $rPengolahan['jampengolahan'] = $cOlah['jampengolahan'];
    $sJamPeng += $rPengolahan['jampengolahan'];
    $rPengolahan['jamstagnasi'] = $cOlah['jamstagnasi'];
    $sJamStag += $rPengolahan['jamstagnasi'];
    if (1 === strlen($tgl)) {
        $agl = '0'.$tgl;
    }

    $tglServ = substr($bar->tanggal, 0, 8);
    $tab .= '<tr class=rowcontent>';
    $tab .= '<td>'.$bar->kodeorg.'</td>';
    if ('excel' === $_GET['method']) {
        $tab .= '<td>'.$bar->tanggal.'</td>';
    } else {
        $tab .= '<td>'.tanggalnormal($bar->tanggal).'</td>';
    }

    $tab .= '<td align=right>'.number_format($bar->tbsmasuk + $bar->sisatbskemarin, 0, '.', ',').'</td>';
    $tbsSd = $tbs[$bar->kodeorg][$tglServ.$agl + 1];
    $tbsSd2 = $tbs[$bar->kodeorg][$bar->tanggal];
    $tbsTot = $tbsSd2 + $tbsSd;
    $des += $tbsTot;
    $oerSd = $jmOer[$bar->kodeorg][$tglServ.$agl + 1];
    $oerSd2 = $jmOer[$bar->kodeorg][$bar->tanggal];
    $oerTot = $oerSd2 + $oerSd;
    $oerTotal += $oerTot;
    $oerpkSd = $jmOerPk[$bar->kodeorg][$tglServ.$agl + 1];
    $oerpkSd2 = $jmOerPk[$bar->kodeorg][$bar->tanggal];
    $oerpkTot = $oerpkSd + $oerpkSd2;
    $oerpkTotal += $oerpkTot;
    $kpsitas = $bar->tbsdiolah / $rPengolahan['jampengolahan'] / 1000;
    $siKps += $rPengolahan['jampengolahan'];
    if (1 === $ared) {
        $olhShi = $kpsitas;
        $oershi = $oerTotal / $bar->tbsdiolah * 100;
        $oerpkshi = $oerpkTotal / $bar->tbsdiolah * 100;
    } else {
        $olhShi = $des / $siKps / 1000;
        $oershi = $oerTotal / $des * 100;
        $oerpkshi = $oerpkTotal / $des * 100;
    }

    $ffasdhi = ($bar->ffa * $bar->oer + $cposdkem * $ffasdkem) / $oerTotal;
    $kotsdhi = ($bar->kadarkotoran * $bar->oer + $cposdkem * $kotsdkem) / $oerTotal;
    $airsdhi = ($bar->kadarair * $bar->oer + $cposdkem * $airsdkem) / $oerTotal;
    $ffksdhi = ($bar->ffapk * $bar->oerpk + $kersdkem * $ffksdkem) / $oerpkTotal;
    $koksdhi = ($bar->kadarkotoranpk * $bar->oerpk + $kersdkem * $koksdkem) / $oerpkTotal;
    $aiksdhi = ($bar->kadarairpk * $bar->oerpk + $kersdkem * $aiksdkem) / $oerpkTotal;
    $cposdkem = $oerTotal;
    $ffasdkem = $ffasdhi;
    $kotsdkem = $kotsdhi;
    $airsdkem = $airsdhi;
    $kersdkem = $oerpkTotal;
    $ffksdkem = $ffksdhi;
    $koksdkem = $koksdhi;
    $aiksdkem = $aiksdhi;
    $tab .= '<td align=right>'.number_format($bar->tbsdiolah, 0, '.', ',')."</td>\r\n                   <td align=right>".number_format($des, 0, '.', ',')."</td>\r\n                   <td align=right>".number_format($bar->sisahariini, 0, '.', ',').'</td>';
    $tab .= '<td align=right>'.substr($jmStart[$bar->tanggal], 0, 5)."</td>\r\n                    <td align=right>".substr($jmEnd[$bar->tanggal], 0, 5).'</td>';
    $tab .= '<td align=right>'.number_format($rPengolahan['jampengolahan'], 2, '.', ',').'</td>';
    $tab .= '<td align=right>'.number_format($sJamPeng, 2, '.', ',').'</td>';
    $tab .= '<td align=right>'.number_format($rPengolahan['jamstagnasi'], 2, '.', ',')."</td>\r\n                  <td align=right>".number_format($sJamStag, 2, '.', ',').'</td>';
    $tab .= '<td align=right>'.@number_format($rPengolahan['jamstagnasi'] / $rPengolahan['jampengolahan'] * 100, 2, '.', ',')."</td>\r\n                  <td align=right>".@number_format($sJamStag / $sJamPeng * 100, 2, '.', ',').'</td>';
    $tab .= '<td align=right>'.number_format($kpsitas, 2, '.', ',')."</td>\r\n                   <td align=right>".number_format($olhShi, 2, '.', ',').'</td>';
    $tab .= '<td align=right>'.number_format($bar->oer, 0, '.', ',')."</td>\r\n                  <td align=right>".number_format($oerTotal, 0, '.', ',')."</td>\r\n                  ";
    $tab .= '<td align=right>'.@number_format($bar->oer / $bar->tbsdiolah * 100, $komanya, '.', ',').'</td>';
    $tab .= '<td align=right>'.number_format($oershi, $komanya, '.', ',')."</td>\r\n                 \r\n\t\t\t\t \r\n\t\t\t\t\r\n\r\n\r\n\t\t   <td align=right>".number_format($bar->ffa, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($ffasdhi, $komanya, '.', ',')."</td>\r\n\t\t   \r\n\t\t   <td align=right>".number_format($bar->kadarkotoran, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($kotsdhi, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarair, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($airsdhi, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".$bar->dobi.'</td>';
    $tab .= '<td align=right>'.number_format($bar->oerpk, 0, '.', ',')."</td>\r\n                    <td align=right>".number_format($oerpkTotal, 0, '.', ',')."</td>\r\n                    ";
    $tab .= '<td align=right>'.@number_format($bar->oerpk / $bar->tbsdiolah * 100, $komanya, '.', ',')."</td>\r\n                    <td align=right>".number_format($oerpkshi, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->ffapk, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($ffksdhi, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarkotoranpk, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($koksdhi, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($bar->kadarairpk, $komanya, '.', ',')."</td>\r\n\t\t   <td align=right>".number_format($aiksdhi, $komanya, '.', ',')."</td>\r\n                   <td align=right>".$bar->batu.'</td>';
    $tab .= '</tr>';
}
$tab .= "\t</tbody>\r\n\t\t<tfoot>\r\n\t\t</tfoot>\r\n\t  </table>\r\n\t  </fieldset>";
if ('excel' === $_GET['method']) {
    $dte = date('YmdHis');
    $nop_ = 'laporan_produksi_'.$dte;
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
            echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
            exit();
        }

        echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
        closedir($handle);
    }
} else {
    echo $tab;
}

?>