<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
echo "\r\n\r\n";
switch ($_POST['method']) {
    case 'getDetailPP':
        $str = 'select * from '.$dbname.".pabrik_produksi\r\n      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tgl']."'";
        $res = mysql_query($str);
        $rdata = mysql_fetch_assoc($res);
        echo "<fieldset style='width:700px;'>\r\n        <legend>".$_SESSION['lang']['data'].":</legend>\r\n\t\t<table><tr><td>\r\n\t\t\r\n\t\t<table>\r\n\t\t   <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kodeorganisasi']."\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['kodeorg']."\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr> \r\n\t\t\t <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t <td>".tanggalnormal($rdata['tanggal'])."\r\n\t\t\t </td>\t\r\n\t\t     <td>\t\t \r\n\t\t </tr>\r\n\t\t   <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['sisatbskemarin']."\r\n\t\t\t </td>\r\n\t\t     <td>".number_format($rdata['sisatbskemarin'], 0)."\r\n\t\t\t </td>\r\n\t\t   </tr>\r\n\t\t   <tr> \r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['tbsmasuk']."\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t    ".number_format($rdata['tbsmasuk'], 0)."\r\n\t\t\t </td>\t \t\t \r\n\t\t </tr>\t\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['tbsdiolah']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t    ".number_format($rdata['tbsdiolah'], 0)."\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['sisa']."\r\n\t\t\t </td>\r\n\t\t\t <td>   ".number_format($rdata['sisahariini'], 0)."\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t";
        echo " <tr>\r\n\t\t     <td>% USB Before Crusher\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['usbbefore']." %\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t  \r\n                  <tr>\r\n\t\t     <td>% USB After Crusher\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['usbafter']." %\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>% Oil Diluted Crude Oil\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['oildiluted']." %\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>% Oil in underflow (CST)\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['oilin']." %\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>% Oil in Heavy Phase - S/D\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['oilinheavy']." % \r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>CaCO3\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['caco']." KG\r\n\t\t\t </td>\t\t \r\n\t\t </tr>\t";
        echo "</table>\t  \r\n\t  </td>\r\n\t  <td valign=top>  \r\n  \t<table>\r\n\t\t<tr>\r\n\t\t<td> \r\n\t\t <fieldset><legend>".$_SESSION['lang']['cpo']."</legend>\r\n\t\t <table>\r\n\t\t <tr><td>".$_SESSION['lang']['cpo']."(Kg)\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t   ".$rdata['oer']."\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n                  <tr><td>".$_SESSION['lang']['oer']."\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t   ".@number_format($rdata['oer'] / $rdata['tbsdiolah'] * 100, 2, '.', ',')."\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kotoran']."\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t  ".$rdata['kadarkotoran']."%\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kadarair']."\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t   ".$rdata['kadarair']."%.\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    FFa\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t  ".$rdata['ffa']." %. \r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>\r\n\t\t\t    Dobi\r\n\t\t\t </td>\r\n\t\t     <td>\r\n\t\t\t  ".$rdata['dobi']." %. \r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n\t\t</table>\r\n\t\t</fieldset>\r\n\t\t\r\n\t\t</td>\r\n\t\t</tr>\r\n                \r\n<tr>\r\n\t\t<td> \r\n\t\t <fieldset><legend>".$_SESSION['lang']['cpo']." Loses</legend>\r\n\t\t <table>\r\n\t\t <tr><td>USB.\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t    ".$rdata['fruitineb']." KG/TON\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n\t\t <tr>\r\n\t\t     <td>Empty Bunch \r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['ebstalk']."\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td> Fibre From Press Cake\r\n\t\t\t </td>\r\n\t\t\t <td>".$rdata['fibre']."\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>Nut From Press Cake\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['nut']."\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n                  <tr>\r\n\t\t     <td>Effluent\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['effluent']."\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n                   <tr>\r\n\t\t     <td>Fruit In Empty Bunch\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['soliddecanter']."\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n\t\t</table>\r\n\t\t</fieldset>\r\n\t\t\r\n\t\t</td>\r\n\t\t</tr>\r\n\t\t</table>\t\r\n    </td>\r\n\t<td valign=top>\r\n  \t<table>\r\n\t\t<tr>\r\n\t\t<td> \r\n\t\t <fieldset><legend>".$_SESSION['lang']['kernel']."</legend>\r\n\t\t <table>\r\n\t\t <tr><td>\r\n\t\t\t    ".$_SESSION['lang']['kernel']."(Kg)\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t    ".$rdata['oerpk']." Kg.\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n                  <tr><td>\r\n\t\t\t    ".$_SESSION['lang']['oerpk']."\r\n\t\t\t </td>\r\n\t\t\t <td>\r\n\t\t\t    ".@number_format($rdata['oerpk'] / $rdata['tbsdiolah'] * 100, 2, '.', ',')." \r\n\t\t\t </td>\r\n\t\t  </tr>\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kotoran']."\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['kadarkotoranpk']." %\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    ".$_SESSION['lang']['kadarair']."\r\n\t\t\t </td>\r\n\t\t\t <td>".$rdata['kadarairpk']." %. \r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>\r\n\t\t\t    FFa\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['ffapk']." %.\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n\t\t \r\n\t\t\t<tr>\r\n\t\t     <td>\r\n\t\t\t    Inti Pecah\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['intipecah']." %.\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\r\n                 <tr>\r\n\t\t     <td>\r\n\t\t\t  Batu\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['batu']." %.\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\r\n                 \r\n\t\t</table>\r\n\t\t</fieldset> \r\n\t\t\r\n\t\t</td>\r\n\t\t</tr>\r\n                <tr>\r\n\t\t<td> \r\n\t\t <fieldset> <legend>".$_SESSION['lang']['kernel']." Loses</legend>\r\n\t\t <table>\r\n\t\t <tr><td>Kernel In Empty Bunch</td>\r\n\t\t\t <td>".$rdata['fruitinebker']."KG/TON\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n\t\t <tr>\r\n\t\t     <td>Fibre Cyclone\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['cyclone']."\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>LTDS 1\r\n\t\t\t </td>\r\n\t\t\t <td>".$rdata['ltds']."\r\n\t\t\t </td>\r\n\t\t </tr>\r\n        <tr>\r\n\t\t    <td>LTDS 2\r\n\t\t\t</td>\r\n\t\t\t<td>".$rdata['ltds1']."\r\n\t\t\t</td>\r\n\t\t</tr>\t\r\n\t\t <tr>\r\n\t\t     <td>Claybath\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['claybath']."\r\n\t\t\t </td>\t\t\t \r\n\t\t </tr>\t\r\n                 \r\n\t\t</table>\r\n\t\t</fieldset> \r\n\t\t\r\n\t\t</td><td><fieldset><legend>Cangkang</legend>\r\n\t\t <table>\r\n\t\t <tr><td>Cangkang</td>\r\n\t\t\t <td>".$rdata['cangkang']."Kg\r\n\t\t\t </td>\r\n\t\t  </tr>\r\n\t\t <tr>\r\n\t\t     <td>FFa\r\n\t\t\t </td>\r\n\t\t     <td>".$rdata['ffa_cangkang']." %\r\n\t\t\t </td>\r\n\t\t </tr>\t\r\n\t\t <tr>\r\n\t\t     <td>Kadar Air\r\n\t\t\t </td>\r\n\t\t\t <td>".$rdata['kadarair_cangkang']." %\r\n\t\t\t </td>\r\n\t\t </tr>\r\n        <tr>\r\n\t\t    <td>Kotoran\r\n\t\t\t</td>\r\n\t\t\t<td>".$rdata['kotoran_cangkang']." %\r\n\t\t\t</td>\r\n\t\t</tr>\t\r\n\t\t </table>\r\n\t\t</fieldset></td><td></td>\r\n\t\t</tr>\r\n\t\t</table>\t\r\n\t\t\t\r\n\t\r\n\t</td>\r\n\t</tr>\t  \r\n\t  \r\n\t</table>\t\r\n\t  </fieldset>\r\n\t ";

        break;
    case 'getCpo':
        $tglck = tanggaldgnbar($_POST['tanggal']);
        $tglShari = nambahHari($_POST['tanggal'], '1', '1');
        if ('H01M' === $_SESSION['empl']['lokasitugas']) {
            $sHrini = 'select sum(beratbersih) as jmlhCpoKirim from '.$dbname.".pabrik_timbangan \r\n                         where millcode='".$_SESSION['empl']['lokasitugas']."' \r\n                         and left(tanggal,10)='".$tglck."' and kodebarang='40000007'";
            $qhrIni = mysql_query($sHrini);
            $rHrini = mysql_fetch_assoc($qhrIni);
            $sIsiTangkiKmrnA1 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'\r\n                                 and kodetangki='ST01' order by tanggal desc limit 0,1";
            $qIsiTangkiKmrnA1 = mysql_query($sIsiTangkiKmrnA1);
            $rIsiTangkiKmrnA1 = mysql_fetch_assoc($qIsiTangkiKmrnA1);
            $sIsiTangkiKmrnA2 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'\r\n                                 and kodetangki='ST02' order by tanggal desc limit 0,1";
            $qIsiTangkiKmrnA2 = mysql_query($sIsiTangkiKmrnA2);
            $rIsiTangkiKmrnA2 = mysql_fetch_assoc($qIsiTangkiKmrnA2);
            $A = $rIsiTangkiKmrnA1['jmlhCpoKmrn'] + $rIsiTangkiKmrnA2['jmlhCpoKmrn'];
            $sIsiTangkiKmrn2B1 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'\r\n                                 and kodetangki='ST01'order by tanggal desc limit 0,1";
            $qIsiTangkiKmrn2B1 = mysql_query($sIsiTangkiKmrn2B1);
            $rIsiTangkiKmrn2B1 = mysql_fetch_assoc($qIsiTangkiKmrn2B1);
            $sIsiTangkiKmrn2B2 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'\r\n                                 and kodetangki='ST02'order by tanggal desc limit 0,1";
            $qIsiTangkiKmrn2B2 = mysql_query($sIsiTangkiKmrn2B2);
            $rIsiTangkiKmrn2B2 = mysql_fetch_assoc($qIsiTangkiKmrn2B2);
            $B = $rIsiTangkiKmrn2B1['jmlhCpoKmrn'] + $rIsiTangkiKmrn2B2['jmlhCpoKmrn'];
            $cpo = 0;
            $hslTambah = 0;
            if ('' === $A || '' === $B) {
                $cpo = 1;
            }

            $hslTambah = $B - $A + $rHrini['jmlhCpoKirim'];
        } else {
            if ('L01M' === $_SESSION['empl']['lokasitugas']) {
                $sHrini = 'select sum(beratbersih) as jmlhCpoKirim from '.$dbname.".pabrik_timbangan \r\n                         where millcode='".$_SESSION['empl']['lokasitugas']."' \r\n                         and left(tanggal,10)='".$tglck."' and kodebarang='40000001'";
                $qhrIni = mysql_query($sHrini);
                $rHrini = mysql_fetch_assoc($qhrIni);
                $sIsiTangkiKmrnA1 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'\r\n                                 and kodetangki='ST01' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA1 = mysql_query($sIsiTangkiKmrnA1);
                $rIsiTangkiKmrnA1 = mysql_fetch_assoc($qIsiTangkiKmrnA1);
                $sIsiTangkiKmrnA2 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'\r\n                                 and kodetangki='ST02' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA2 = mysql_query($sIsiTangkiKmrnA2);
                $rIsiTangkiKmrnA2 = mysql_fetch_assoc($qIsiTangkiKmrnA2);
                $sIsiTangkiKmrnA3 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglck."'\r\n                 and kodetangki='ST03' order by tanggal desc limit 0,1";
                $qIsiTangkiKmrnA3 = mysql_query($sIsiTangkiKmrnA3);
                $rIsiTangkiKmrnA3 = mysql_fetch_assoc($qIsiTangkiKmrnA3);
                $A = $rIsiTangkiKmrnA1['jmlhCpoKmrn'] + $rIsiTangkiKmrnA2['jmlhCpoKmrn'] + $rIsiTangkiKmrnA3['jmlhCpoKmrn'];
                $sIsiTangkiKmrn2B1 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'\r\n                                 and kodetangki='ST01'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B1 = mysql_query($sIsiTangkiKmrn2B1);
                $rIsiTangkiKmrn2B1 = mysql_fetch_assoc($qIsiTangkiKmrn2B1);
                $sIsiTangkiKmrn2B2 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'\r\n                                 and kodetangki='ST02'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B2 = mysql_query($sIsiTangkiKmrn2B2);
                $rIsiTangkiKmrn2B2 = mysql_fetch_assoc($qIsiTangkiKmrn2B2);
                $sIsiTangkiKmrn2B3 = 'select kuantitas as jmlhCpoKmrn from '.$dbname.".pabrik_masukkeluartangki where \r\n                                 kodeorg='".$_SESSION['empl']['lokasitugas']."' and left(tanggal,10)<='".$tglShari."'\r\n                                 and kodetangki='ST03'order by tanggal desc limit 0,1";
                $qIsiTangkiKmrn2B3 = mysql_query($sIsiTangkiKmrn2B3);
                $rIsiTangkiKmrn2B3 = mysql_fetch_assoc($qIsiTangkiKmrn2B3);
                $B = $rIsiTangkiKmrn2B1['jmlhCpoKmrn'] + $rIsiTangkiKmrn2B2['jmlhCpoKmrn'] + $rIsiTangkiKmrn2B3['jmlhCpoKmrn'];
                $cpo = 0;
                $hslTambah = 0;
                if ('' === $A || '' === $B) {
                    $cpo = 1;
                }

                $hslTambah = $B - $A + $rHrini['jmlhCpoKmrn'];
            }
        }

        echo $cpo.'####'.$hslTambah;

        break;
}

?>