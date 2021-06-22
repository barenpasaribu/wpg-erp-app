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

 $gt_total_pengiriman = 0;
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM pabrik_klarification1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
       
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
              <td>Tanggal</td>
                <td>JAM</td>
                <td>CST (cm)</td>
                <td>CST (°C)</td>
                <td>Sludge Tank (cm)</td>
                <td>Sludge Tank (°C)</td>
                <td>Oil Tank 1 (cm)</td>
                <td>Oil Tank 1 (°C)</td>
                <td>Oil Tank 2 (cm)</td>
                <td>Oil Tank 2 (°C)</td>
                <td>Recovery (cm)</td>
                <td>Recovery (°C)</td>
                <td>Keterangan</td>
                <td>Pengiriman CPO Awal</td>
                <td>Pengiriman CPO Akhir</td>
                <td>Total Jam Pengiriman CPO </td>
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

            $cst_cm = $res['cst_cm'];
            $cst_c = $res['cst_c'];
            $st_cm = $res['st_cm'];
            $st_c = $res['st_c'];
            $ot1_cm = $res['ot2_cm'];
            $ot1_c = $res['ot1_c'];
            $ot2_cm = $res['ot2_cm'];
            $ot2_c = $res['ot2_c'];
            $recovery1 = $res['recovery1'];
            $recovery2 = $res['recovery2'];
            $pengiriman_cpo_awal = $res['pengiriman_cpo_awal'];
            $pengiriman_cpo_akhir = $res['pengiriman_cpo_akhir'];
            $total_pengiriman = $pengiriman_cpo_akhir - $pengiriman_cpo_awal;
            $keterangan = $res['keterangan'];

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$cst_cm.'</td>
                <td>'.$cst_c.'</td>
                <td >'.$st_cm.'</td>
                <td >'.$st_c.'</td>
                <td >'.$ot1_cm.'</td>
                <td >'.$ot1_c.'</td>
                <td >'.$ot2_cm.'</td>
                <td >'.$ot2_c.'</td>
                <td >'.$recovery1.'</td>
                <td >'.$recovery2.'</td>                
                <td >'.$keterangan.'</td>
                <td >'.$pengiriman_cpo_awal.'</td>
                <td >'.$pengiriman_cpo_akhir.'</td>
                <td >'.$total_pengiriman.'</td>
                    </tr>';
                    $gt_total_pengiriman += $total_pengiriman;
                   
            }
              
                
                    
                echo '                      <tr class=rowcontent>
               
                  <td colspan=15>Total</td>
                <td>'.$gt_total_pengiriman.'</td>
                <td></td>
                    </tr> </table>';
               
           $sPower2 = "SELECT * FROM pabrik_klarification2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                <tr>
              
               <td colspan=18>Sludge Centrifuge</td>
                </tr>
                  <tr>
              
                <td>Tanggal</td>
                <td>Shift NIK</td>
                <td>Nama</td>
                <td>Sludge Centrifuge 1 Start (HM)</td>
                <td>Sludge Centrifuge 1 Stop</td>
                <td>Total HM Sludge Centrifuge 1 </td>
                <td>Sludge Centrifuge 2 Start (HM)</td>
                <td>Sludge Centrifuge 2 Stop</td>
                <td>Total HM Sludge Centrifuge 2 </td>
                <td>Sludge Centrifuge 3 Start (HM)</td>
                <td>Sludge Centrifuge 3 Stop</td>
                <td>Total HM Sludge Centrifuge 3 </td>
                <td>Sludge Centrifuge 4 Start (HM)</td>
                <td>Sludge Centrifuge 4 Stop</td>
                <td>Total HM Sludge Centrifuge 4 </td>
                <td>Sludge Centrifuge 5 Start (HM)</td>
                <td>Sludge Centrifuge 5 Stop</td>
                <td>Total HM Sludge Centrifuge 5 </td>
                </tr>
                </thead>
            <tbody>";

        $qData2 = mysql_query($sPower2);
        $brs2 = mysql_num_rows($qData2);
        // echo $sPower1;
        

            while ($res2 = mysql_fetch_assoc($qData2)) {
                   $sKaryawan = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$res2['shift_nik']."'  ";
                 $qK = mysql_query($sKaryawan);
                 while ($resK = mysql_fetch_assoc($qK)) {
                    $namakaryawan = $resK['namakaryawan'];
                 }
                ++$no;
                $tgl= $res2['tgl'];
            $shift_nik= $res2['shift_nik'];
            $sc1_start = $res2['sc1_start'];
            $sc1_stop = $res2['sc1_stop'];
            $total_sc1 = $sc1_stop - $sc1_start;
            $sc2_start = $res2['sc2_start'];
            $sc2_stop = $res2['sc2_stop'];
            $total_sc2 = $sc2_stop - $sc2_start;
            $sc3_start = $res2['sc3_start'];
            $sc3_stop = $res2['sc3_stop'];
            $total_sc3 = $sc3_stop - $sc3_start;
            $sc4_start = $res2['sc4_start'];
            $sc4_stop = $res2['sc4_stop'];
            $total_sc4 = $sc4_stop - $sc4_start;
            $sc5_start = $res2['sc5_start'];
            $sc5_stop = $res2['sc5_stop'];
            $total_sc5 = $sc5_stop - $sc5_start;
            $gt_total_sc1 += $total_sc1;
            $gt_total_sc2 += $total_sc2;
            $gt_total_sc3 += $total_sc3;
            $gt_total_sc4 += $total_sc4;
            $gt_total_sc5 += $total_sc5;
                echo '<tr class=rowcontent>
                    <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$sc1_start.'</td>
                <td>'.$sc1_stop.'</td>
                <td>'.$total_sc1.'</td>
                <td>'.$sc2_start.'</td>
                <td>'.$sc2_stop.'</td>
                <td>'.$total_sc2.'</td>
                <td>'.$sc3_start.'</td>
                <td>'.$sc3_stop.'</td>
                <td>'.$total_sc3.'</td>
                <td>'.$sc4_start.'</td>
                <td>'.$sc4_stop.'</td>
                <td>'.$total_sc4.'</td>
                <td>'.$sc5_start.'</td>
                <td>'.$sc5_stop.'</td>
                <td>'.$total_sc5.'</td>
                    </tr>';

                   
            }
              
                  echo '<tr class=rowcontent>
                    <td colspan=5>Total</td>
                <td>'.$gt_total_sc1.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc2.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc3.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc4.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc5.'</td>
                    </tr>';
                    
                
               

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

           $sPower1 = "SELECT * FROM pabrik_klarification1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL KLARIFICATION STATION</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_klarification1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
              
                 <td>Tanggal</td>
                <td>JAM</td>
                <td>CST (cm)</td>
                <td>CST (°C)</td>
                <td>Sludge Tank (cm)</td>
                <td>Sludge Tank (°C)</td>
                <td>Oil Tank 1 (cm)</td>
                <td>Oil Tank 1 (°C)</td>
                <td>Oil Tank 2 (cm)</td>
                <td>Oil Tank 2 (°C)</td>
                <td>Recovery (cm)</td>
                <td>Recovery (°C)</td>
                <td>Keterangan</td>
                <td>Pengiriman CPO Awal</td>
                <td>Pengiriman CPO Akhir</td>
                <td>Total Jam Pengiriman CPO </td>
                </tr>
                </thead>
            <tbody>
           ";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
        // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {
              
                 ++$no;
           $tgl= $res['tgl'];
            $jam= $res['jam'];

            $cst_cm = $res['cst_cm'];
            $cst_c = $res['cst_c'];
            $st_cm = $res['st_cm'];
            $st_c = $res['st_c'];
            $ot1_cm = $res['ot2_cm'];
            $ot1_c = $res['ot1_c'];
            $ot2_cm = $res['ot2_cm'];
            $ot2_c = $res['ot2_c'];
            $recovery1 = $res['recovery1'];
            $recovery2 = $res['recovery2'];
            $pengiriman_cpo_awal = $res['pengiriman_cpo_awal'];
            $pengiriman_cpo_akhir = $res['pengiriman_cpo_akhir'];
            $total_pengiriman = $pengiriman_cpo_akhir - $pengiriman_cpo_awal;
            $keterangan = $res['keterangan'];

                $tab .=  '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$cst_cm.'</td>
                <td>'.$cst_c.'</td>
                <td >'.$st_cm.'</td>
                <td >'.$st_c.'</td>
                <td >'.$ot1_cm.'</td>
                <td >'.$ot1_c.'</td>
                <td >'.$ot2_cm.'</td>
                <td >'.$ot2_c.'</td>
                <td >'.$recovery1.'</td>
                <td >'.$recovery2.'</td>                
                <td >'.$keterangan.'</td>
                <td >'.$pengiriman_cpo_awal.'</td>
                <td >'.$pengiriman_cpo_akhir.'</td>
                <td >'.$total_pengiriman.'</td>
                    </tr>';
                    $gt_total_pengiriman += $total_pengiriman;
               
            }
              $tab .= '                      <tr class=rowcontent>
               
                  <td colspan=15>Total</td>
                <td>'.$gt_total_pengiriman.'</td>
                <td></td>
                    </tr> </table>';
               
           $sPower2 = "SELECT * FROM pabrik_klarification2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
       
        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                <tr bgcolor=#DEDEDE>
              
                <td colspan=18>Sludge Centrifuge</td>
                </tr>
                  <tr bgcolor=#DEDEDE>
              
                <td>Tanggal</td>
                <td>Shift NIK</td>
                <td>Nama</td>
                <td>Sludge Centrifuge 1 Start (HM)</td>
                <td>Sludge Centrifuge 1 Stop</td>
                <td>Total HM Sludge Centrifuge 1 </td>
                <td>Sludge Centrifuge 2 Start (HM)</td>
                <td>Sludge Centrifuge 2 Stop</td>
                <td>Total HM Sludge Centrifuge 2 </td>
                <td>Sludge Centrifuge 3 Start (HM)</td>
                <td>Sludge Centrifuge 3 Stop</td>
                <td>Total HM Sludge Centrifuge 3 </td>
                <td>Sludge Centrifuge 4 Start (HM)</td>
                <td>Sludge Centrifuge 4 Stop</td>
                <td>Total HM Sludge Centrifuge 4 </td>
                <td>Sludge Centrifuge 5 Start (HM)</td>
                <td>Sludge Centrifuge 5 Stop</td>
                <td>Total HM Sludge Centrifuge 5 </td>
                </tr>
                </thead>
            <tbody>";

        $qData2 = mysql_query($sPower2);
        $brs2 = mysql_num_rows($qData2);
        // echo $sPower1;
        

            while ($res2 = mysql_fetch_assoc($qData2)) {

                  $sKaryawan = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$res2['shift_nik']."'  ";
                 $qK = mysql_query($sKaryawan);
                 while ($resK = mysql_fetch_assoc($qK)) {
                    $namakaryawan = $resK['namakaryawan'];
                 }
                ++$no;
                $tgl= $res2['tgl'];
            $shift_nik= $res2['shift_nik'];
            $sc1_start = $res2['sc1_start'];
            $sc1_stop = $res2['sc1_stop'];
            $total_sc1 = $sc1_stop - $sc1_start;
            $sc2_start = $res2['sc2_start'];
            $sc2_stop = $res2['sc2_stop'];
            $total_sc2 = $sc2_stop - $sc2_start;
            $sc3_start = $res2['sc3_start'];
            $sc3_stop = $res2['sc3_stop'];
            $total_sc3 = $sc3_stop - $sc3_start;
            $sc4_start = $res2['sc4_start'];
            $sc4_stop = $res2['sc4_stop'];
            $total_sc4 = $sc4_stop - $sc4_start;
            $sc5_start = $res2['sc5_start'];
            $sc5_stop = $res2['sc5_stop'];
            $total_sc5 = $sc5_stop - $sc5_start;
            $gt_total_sc1 += $total_sc1;
            $gt_total_sc2 += $total_sc2;
            $gt_total_sc3 += $total_sc3;
            $gt_total_sc4 += $total_sc4;
            $gt_total_sc5 += $total_sc5;

                $tab .= '<tr class=rowcontent>
                    <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$sc1_start.'</td>
                <td>'.$sc1_stop.'</td>
                <td>'.$total_sc1.'</td>
                <td>'.$sc2_start.'</td>
                <td>'.$sc2_stop.'</td>
                <td>'.$total_sc2.'</td>
                <td>'.$sc3_start.'</td>
                <td>'.$sc3_stop.'</td>
                <td>'.$total_sc3.'</td>
                <td>'.$sc4_start.'</td>
                <td>'.$sc4_stop.'</td>
                <td>'.$total_sc4.'</td>
                <td>'.$sc5_start.'</td>
                <td>'.$sc5_stop.'</td>
                <td>'.$total_sc5.'</td>
                    </tr>  ';

                   
            }
            $tab .= '<tr class=rowcontent>
                    <td colspan=5>Total</td>
                <td>'.$gt_total_sc1.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc2.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc3.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc4.'</td>
                <td></td>
                <td></td>
                <td>'.$gt_total_sc5.'</td>
                    </tr>';
             $tab .= '   </table><table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
                </tr>
                <tr></tr>
                 <tr></table>';
             $tab .= '   <table cellspacing=1 border=0 class=sortable>                   
                
                
                <tr>
                <tr></tr>
                <tr></tr>
                <td></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
               
                 </tr><tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                </tr>
                <tr>
                 
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
               
                 </tr><tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                
                 </tr><tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                </tr>
                </table>';
            $tab .= '   </table><table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
                </tr>
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
                <td></td></tr>';

        } else {

            $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';

        }



        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];

        $tglSkrg = date('Ymd');

        $nop_ = 'LaporanKlarification'.$tglSkrg;
         // echo $tab;
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