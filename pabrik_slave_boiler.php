<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {

    $proses = $_POST['proses'];

} else {

    $proses = $_GET['proses'];

}


$kdPabrik = $_POST['kdPabrik'];
$kdCust = $_POST['kdCust'];
$nkntrak = $_POST['nkntrak'];
$kdBrg = $_POST['kdBrg'];
$tgl_1 = tanggaldgnbar($_POST['tgl_1']);
$tgl_2 = tanggaldgnbar($_POST['tgl_2']);
$tgl1 = tanggaldgnbar($_POST['tgl1']);
$tgl2 = tanggaldgnbar($_POST['tgl2']);

$kdCustomer = $_POST['kdCustomer'];
//$wr = "kodekelompok='S003'";
$wr = '';
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', $wr);
$total_kw_g3 = 0;
                    $total_kw_g2 = 0;
                    $total_kw_g1 = 0;
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM pabrik_boiler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        
       
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                    <tr class='rowheader'>
                <td>Tanggal</td>
                <td>JAM</td>
                <td>IDF(A)</td>
                <td>FDF(A)</td>
                <td>SCDF(A)</td>
                <td>FFF(A) </td>
                <td>FP1(A)</td>
                <td>FP2(A)</td>
                <td>FEED TANK(C)</td>
                <td>DEARATOR(C)</td>
                <td>OUTLERT GAS DUCT(C)</td>
                <td>SH(C)</td>
                <td>MAIN STEAM(Kg.Cm2)</td>
                <td>FP1(Kg.Cm2)</td>
                <td>FP2(Kg.Cm2)</td>
                <td>FLOW METER FEED WATER</td>
                <td>LEVEL AIR FEED WATER</td>
                <td>WATER LEVEL(%)</td>
                <td>TDS(PPM)</td>
                <td>JAM SB</td>
                <td>KETERANGAN</td>
                </tr>
                </thead>
            <tbody>";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
        // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {
                ++$no;
             $tgl= $res['tgl'];
            $jam= $res['jam'];
            $idf_a = $res['idf_a'];
            $fdf_a = $res['fdf_a'];
            $scdf_a = $res['scdf_a'];
            $fff_a = $res['fff_a'];
            $fp1_a = $res['fp1_a'];;
            $fp2_a = $res['fp2_a'];
            $feed_tank = $res['feed_tank'];
            $dearator = $res['dearator'];
            $gas_duct = $res['gas_duct'];
            $sh = $res['sh'];
            $main_steam = $res['main_steam'];
            $fp1 = $res['fp1'];
            $fp2 = $res['fp2'];
            $flow_meter = $res['flow_meter'];
            $level_airfeed = $res['level_airfeed'];
            $water_level = $res['water_level'];
            $tds = $res['tds'];
            $jam_sb = $res['jam_sb'];
            $keterangan = $res['keterangan'];

                echo '<tr class="rowcontent" >
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$idf_a.'</td>
                <td>'.$fdf_a.'</td>
                <td >'.$scdf_a.'</td>
                <td>'.$fff_a.'</td>
                <td>'.$fp1_a.'</td>
                <td>'.$fp2_a.'</td>
                <td>'.$feed_tank.'</td>
                <td>'.$dearator.'</td>
                <td>'.$gas_duct.'</td>
                <td>'.$sh.'</td>
                <td>'.$main_steam.'</td>
                <td>'.$fp1.'</td>
                <td>'.$fp2.'</td>
                <td>'.$flow_meter.'</td>
                <td>'.$level_airfeed.'</td>
                <td>'.$water_level.'</td>
                <td>'.$tds.'</td>
                <td>'.$jam_sb.'</td>
                <td>'.$keterangan.'</td>
                </tr>';

            }

          

        } else {

            echo '<tr class=rowcontent><td colspan=13 align=center>Data empty</td></tr>';

        }

        echo '</tbody></table>';

        break;

 

    case 'excel':

        $kdCust = $_GET['kdCust'];
        $nkntrak = $_GET['nkntrak'];
        $kdBrg = $_GET['kdBrg'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
        $kdPabrik = $_GET['kdPabrik'];

         $sPower1 = "SELECT * FROM pabrik_boiler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=30 align=center>LAPORAN HARIAN OPERASIONAL BOILER</td></tr>
        <tr><td colspan=30 align=center>BOILER</td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_boiler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr class='rowheader'>
                <td>Tanggal</td>
                <td>JAM</td>
                <td>IDF(A)</td>
                <td>FDF(A)</td>
                <td>SCDF(A)</td>
                <td>FFF(A) </td>
                <td>FP1(A)</td>
                <td>FP2(A)</td>
                <td>FEED TANK(C)</td>
                <td>DEARATOR(C)</td>
                <td>OUTLERT GAS DUCT(C)</td>
                <td>SH(C)</td>
                <td>MAIN STEAM(Kg.Cm2)</td>
                <td>FP1(Kg.Cm2)</td>
                <td>FP2(Kg.Cm2)</td>
                <td>FLOW METER FEED WATER</td>
                <td>LEVEL AIR FEED WATER</td>
                <td>WATER LEVEL(%)</td>
                <td>TDS(PPM)</td>
                <td>JAM SB</td>
                <td>KETERANGAN</td>
                </tr>
                </thead>
            <tbody>";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
        // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {
                
                ++$no;
              $tgl= $res['tgl'];
            $jam= $res['jam'];
            $idf_a = $res['idf_a'];
            $fdf_a = $res['fdf_a'];
            $scdf_a = $res['scdf_a'];
            $fff_a = $res['fff_a'];
            $fp1_a = $res['fp1_a'];;
            $fp2_a = $res['fp2_a'];
            $feed_tank = $res['feed_tank'];
            $dearator = $res['dearator'];
            $gas_duct = $res['gas_duct'];
            $sh = $res['sh'];
            $main_steam = $res['main_steam'];
            $fp1 = $res['fp1'];
            $fp2 = $res['fp2'];
            $flow_meter = $res['flow_meter'];
            $level_airfeed = $res['level_airfeed'];
            $water_level = $res['water_level'];
            $tds = $res['tds'];
            $jam_sb = $res['jam_sb'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                $tab .= '                      <tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$idf_a.'</td>
                <td>'.$fdf_a.'</td>
                <td >'.$scdf_a.'</td>
                <td>'.$fff_a.'</td>
                <td>'.$fp1_a.'</td>
                <td>'.$fp2_a.'</td>
                <td>'.$feed_tank.'</td>
                <td>'.$dearator.'</td>
                <td>'.$gas_duct.'</td>
                <td>'.$sh.'</td>
                <td>'.$main_steam.'</td>
                <td>'.$fp1.'</td>
                <td>'.$fp2.'</td>
                <td>'.$flow_meter.'</td>
                <td>'.$level_airfeed.'</td>
                <td>'.$water_level.'</td>
                <td>'.$tds.'</td>
                <td>'.$jam_sb.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

               
                 
            }
            

            $tab .= '   </table><table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td></tr>
                 <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr>
                
                </tr>
                 <tr class=rowcontent >
                <td>Keterangan Kondisi Alat :</td>
                <td></td>
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td></td>
                <td>Dibuat Oleh</td>
                <td></td>
                <td>Diperiksa Oleh</td>
                <td></td>
                <td>Diketahui Oleh</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td></tr>
                 <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr class=rowcontent rowspan=3>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td></tr>
                <tr class=rowcontent>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td></td>
                <td>Operator</td>
                <td></td>
                <td>Asst Proses </td>
                <td></td>
                <td>Askep/Mill Manager</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td></tr>';

        } else {

            $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';

        }



        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];

        $tglSkrg = date('Ymd');

        $nop_ = 'LaporanBoiler'.$tglSkrg;

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

                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";

                exit();

            }



            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";

            closedir($handle);

        }



        break;

    case 'getKontrakData':

        $sChek = 'select nokontrak from '.$dbname.".pmn_kontrakjual where koderekanan='".$kdCustomer."' order by nokontrak desc";

        $qChek = mysql_query($sChek);

        $brs = mysql_num_rows($qChek);

        if (0 < $brs) {

            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';

            while ($rCheck = mysql_fetch_assoc($qChek)) {

                $optKontrak .= '<option value='.$rCheck['nokontrak'].'>'.$rCheck['nokontrak'].'</option>';

            }

            echo $optKontrak;

        } else {

            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';

            echo $optKontrak;

        }



        break;

    case 'getCust':

        $rt = explode('-', $_POST['tgl1']);

        $rt2 = explode('-', $_POST['tgl2']);

        $tgl1 = $rt[2].'-'.$rt[1].'-'.$rt[0];

        $tgl2 = $rt2[2].'-'.$rt2[1].'-'.$rt2[0];

        $optCust = "<option value=''>".$_SESSION['lang']['all'].'</option>';

        $sCust = 'select distinct a.kodecustomer,namacustomer from '.$dbname.".pabrik_timbangan a left join\r\n                ".$dbname.".pmn_4customer b on a.kodecustomer=b.kodetimbangan where \r\n                left(tanggal,10) between '".$tgl1."' and '".$tgl2."' and millcode='".$_POST['kdPabrik']."'\r\n                and kodebarang='".$_POST['kdBrg']."'\r\n                order by b.namacustomer asc";

        $qCust = mysql_query($sCust);

        while ($rCust = mysql_fetch_assoc($qCust)) {

            $optCust .= '<option value='.$rCust['kodecustomer'].'>'.$rCust['namacustomer'].' ['.$rCust['kodecustomer'].']</option>';

        }

        $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';

        $sChek = 'select distinct nokontrak from '.$dbname.".pabrik_timbangan where  \r\n                 left(tanggal,10) between '".$tgl1."' and '".$tgl2."'  and millcode='".$_POST['kdPabrik']."' \r\n                 and kodebarang='".$_POST['kdBrg']."'\r\n                 order by tanggal asc";

        $qChek = mysql_query($sChek);

        $brs = mysql_num_rows($qChek);

        if (0 < $brs) {

            $optKontrak = "<option value=''>".$_SESSION['lang']['all'].'</opton>';

            while ($rCheck = mysql_fetch_assoc($qChek)) {

                $optKontrak .= '<option value='.$rCheck['nokontrak'].'>'.$rCheck['nokontrak'].'</option>';

            }

        }



        echo $optCust.'####'.$optKontrak;



        break;

}



?>