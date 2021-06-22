<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
if ('excel' === $_GET['proses']) {
    $optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
    $str = 'select a.namakaryawan,a.karyawanid,b.tipe,a.subbagian from '.$dbname.".datakaryawan a\r\n     left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id\r\n     where lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n     and tipekaryawan=".$_GET['tipekaryawan']." and \r\n     (tanggalkeluar>'".$_GET['periode']."-01' or tanggalkeluar is NULL) \r\n     order by namakaryawan";
    $res = mysql_query($str);
    $stream = "<table class=sortable cellspacing=1 border=1>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nomor']."</td>\r\n        <td  bgcolor=#DEDEDE align=center>".$_SESSION['lang']['karyawanid']."</td>    \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['subbagian']."</td>    \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayalistrik']."</td> \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayaair']."</td>\r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayaklinik']."</td>    \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['biayasosial']."</td>\r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['manajemenperumahan']."</td> \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['natura']."</td>     \r\n        <td   bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jms']."</td>    \r\n         \r\n        </tr>\r\n        </thead>\r\n        <tbody>";
    $str1 = 'select * from '.$dbname.".keu_byunalocated where periode='".$_GET['periode']."' \r\n       and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
    $res1 = mysql_query($str1);
    $listrik = [];
    $air = [];
    $klinik = [];
    $sosial = [];
    while ($barx = mysql_fetch_object($res1)) {
        $listrik[$barx->karyawanid] = $barx->listrik;
        $air[$barx->karyawanid] = $barx->air;
        $klinik[$barx->karyawanid] = $barx->klinik;
        $sosial[$barx->karyawanid] = $barx->sosial;
        $perumahan[$barx->karyawanid] = $barx->perumahan;
        $natura[$barx->karyawanid] = $barx->natura;
        $jms[$barx->karyawanid] = $barx->jms;
        $post[$barx->karyawanid] = $barx->posting;
    }
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td id=karid".$no.'>'.$bar->karyawanid."</td>\r\n                <td id=namakaryawan".$no.'>'.$bar->namakaryawan."</td>    \r\n                <td>".$bar->tipe."</td>\r\n                <td id=subbagian".$no.'>'.$bar->subbagian."</td>    \r\n                <td>".number_format($listrik[$bar->karyawanid], 2)."</td> \r\n                <td>".number_format($air[$bar->karyawanid], 2)."</td>\r\n                <td>".number_format($klinik[$bar->karyawanid])."</td>   \r\n                <td>".number_format($sosial[$bar->karyawanid], 2)."</td>\r\n                <td>".number_format($perumahan[$bar->karyawanid], 2)."</td>\r\n                <td>".number_format($natura[$bar->karyawanid], 2)."</td>\r\n                <td>".number_format($jms[$bar->karyawanid], 2)."</td>    \r\n            </tr>";
        $totListrik += $listrik[$bar->karyawanid];
        $totAir += $air[$bar->karyawanid];
        $totKlinik += $klinik[$bar->karyawanid];
        $totSosial += $sosial[$bar->karyawanid];
        $totPerumahan += $perumahan[$bar->karyawanid];
        $totNatura += $natura[$bar->karyawanid];
        $totJms += $jms[$bar->karyawanid];
    }
    $stream .= "<tr class=rowcontent>\r\n                <td colspan=5 bgcolor=#DEDEDE  align=right>".$_SESSION['lang']['total']."</td>\r\n                \r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totListrik, 2)."</td> \r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totAir, 2)."</td>\r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totKlinik)."</td>   \r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totSosial, 2)."</td>\r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totPerumahan, 2)."</td>\r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totNatura, 2)."</td>\r\n                <td bgcolor=#DEDEDE  align=right>".number_format($totJms, 2)."</td>    \r\n            </tr>";
    $stream .= "</tbody>\r\n          <tfoot></tfoot> \r\n          </table> \r\n        ";
    $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
    $dte = date('YmdHis');
    $nop_ = 'alokasiBiayaExcel'.$optTipe[$_GET['tipekaryawan']];
    if (0 < strlen($stream)) {
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ('.' !== $file && '..' !== $file) {
                    @unlink('tempExcel/'.$file);
                }
            }
            closedir($handle);
        }

        $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
        if (!fwrite($handle, $stream)) {
            echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can't convert to excel format');\r\n                    </script>";
            exit();
        }

        echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
        closedir($handle);
    }
}

?>