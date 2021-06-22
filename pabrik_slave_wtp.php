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

 $total_alskg = 0;
  $total_alsppm = 0;
 $total_w33kg =0;
 $total_w33ppm =0;
 $total_w44kg =0;
 $total_w44ppm =0;
 $total_w45kg =0;
 $total_w45ppm =0;
$total_w66kg =0;
  $total_w66ppm =0;
  $total_garam =0;
  $no = 0;
  $no2 = 0;
  $total_sakg =0;
            $total_sappm = 0;
            $total_mfkg = 0;
            $total_mfppm = 0;
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM pabrik_wtpexternal WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
       
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
              <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>Nama </td>
                <td>Alumunium Shulpit Start (Jam)</td>
                <td>Alumunium Shulpit Stop (Jam)</td>
                <td>Sisa Alumunium Shulpit (L)</td>
                <td>Alumunium Shulpit (Kg)</td>
                <td>Alumunium Shulpit (ppm)</td>
                <td>Soda Ash Start (Jam)</td>
                <td>Soda Ash Stop (Jam)</td>
                <td>Sisa Soda Ash (L)</td>
                <td>Soda Ash (Kg)</td>
                <td>Soda Ash (ppm)</td>
                <td>Multi Flock Start (Jam)</td>
                <td>Multi Flock Stop (Jam)</td>
                <td>Sisa Multi Flock (L)</td>
                <td>Multi Flock (Kg)</td>
                <td>Multi Flock (ppm)</td>
                <td>Waktu Backwash Sand Filter 1 (1)</td>
                <td>Waktu Backwash Sand Filter 2 (1)</td>
                <td>Waktu Backwash Sand Filter 1 (2)</td>
                <td>Waktu Backwash Sand Filter 2 (2)</td>
                <td>Raw Water Pump 1 Start (Jam)</td>
                <td>Raw Water Pump 1 Stop (Jam)</td>
                <td>Raw Water Pump 1 Total (Jam)</td>
                <td>Raw Water Pump 2 Start (Jam)</td>
                <td>Raw Water Pump 2 Stop (Jam)</td>
                <td>Raw Water Pump 2 Total (Jam)</td>
                <td>Flow Meter Waduk Awal (m3)</td>
                <td>Flow Meter Waduk Akhir (m3)</td>
                <td>Flow Meter Waduk Total (m3)</td>
                <td>Flowmeter Awal Proses (m3)</td>
                <td>Flowmeter Akhir Proses (m3)</td>
                <td>Flowmeter Total Proses (m3)</td>
                <td>Flowmeter Awal Domestic (m3)</td>
                <td>Flowmeter Akhir Domestic (m3)</td>
                <td>Flowmeter Total Domestic (m3)</td>
                <td>Keterangan</td>
                </tr>
                </thead>
            <tbody>";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
        // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {

                $sKaryawan = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$res['shift_nik']."'  ";
                 $qK = mysql_query($sKaryawan);
                 while ($resK = mysql_fetch_assoc($qK)) {
                    $namakaryawan = $resK['namakaryawan'];
                 }
                ++$no;
           $tgl= $res['tgl'];
            $shift_nik= $res['shift_nik'];
            $als_start = $res['als_start'];
            $als_stop = $res['als_stop'];
            $sisa_als = $res['sisa_als'];
            $als_kg = $res['als_kg'];
            $als_ppm = $res['als_ppm'];;
            $sa_start = $res['sa_start'];
            $sa_stop = $res['sa_stop'];
            $sisa_sa = $res['sisa_sa'];
            $sa_kg = $res['sa_kg'];
            $sa_ppm = $res['sa_ppm'];
            $mf_start = $res['mf_start'];
            $mf_stop = $res['mf_stop'];
            $sisa_mf = $res['sisa_mf'];
            $mf_kg = $res['mf_kg'];
            $mf_ppm = $res['mf_ppm'];
            $wbs1 = $res['wbs1'];
            $wbs2 = $res['wbs2'];
            $wbs1_2 = $res['wbs1_2'];
            $wbs2_2 = $res['wbs2_2'];
            $rwp1_start = $res['rwp1_start'];
            $rwp1_stop = $res['rwp1_stop'];
            $total_rwp1 = $rwp1_stop - $rwp1_start;
            $rwp2_start = $res['rwp2_start'];
            $rwp2_stop = $res['rwp2_stop'];
            $total_rwp2 = $rwp2_stop - $rwp2_start;
            $fmw_awal = $res['fmw_awal'];
            $fmw_akhir = $res['fmw_akhir'];
            $total_fmw =  $fmw_akhir - $fmw_awal;
            $fmw_prosesawal = $res['fmw_prosesawal'];
            $fmw_prosesakhir = $res['fmw_prosesakhir'];
            $total_fmwproses =  $fmw_prosesakhir - $fmw_prosesawal;
            $fmd_awal = $res['fmd_awal'];
            $fmd_akhir = $res['fmd_akhir'];
            $total_fmd =  $fmd_akhir - $fmd_awal;
            $keterangan = $res['keterangan'];
            $total_alskg += $als_kg;
            $total_alsppm += $als_ppm;
             $total_sakg += $sa_kg;
            $total_sappm += $sa_ppm;
            $total_mfkg += $mf_kg;
            $total_mfppm += $mf_ppm;
                echo '<tr class=rowcontent>
               <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$als_start.'</td>
                <td>'.$als_stop.'</td>
                <td >'.$sisa_als.'</td>
                <td>'.$als_kg.'</td>
                <td>'.$als_ppm.'</td>
                <td>'.$sa_start.'</td>
                <td>'.$sa_stop.'</td>
                <td>'.$sisa_sa.'</td>
                <td>'.$sa_kg.'</td>
                <td>'.$sa_ppm.'</td>
                <td>'.$mf_start.'</td>
                <td>'.$mf_stop.'</td>
                <td>'.$sisa_mf.'</td>
                <td>'.$mf_kg.'</td>
                <td>'.$mf_ppm.'</td>
                <td>'.$wbs1.'</td>
                <td>'.$wbs2.'</td>
                <td>'.$wbs1_2.'</td>
                <td>'.$wbs2_2.'</td>
                <td>'.$rwp1_start.'</td>
                <td>'.$rwp1_stop.'</td>
                <td>'.$total_rwp1.'</td>
                <td>'.$rwp2_start.'</td>
                <td>'.$rwp2_stop.'</td>
                <td>'.$total_rwp2.'</td>
                <td>'.$fmw_awal.'</td>
                <td>'.$fmw_akhir.'</td>
                <td>'.$total_fmw.'</td>
                <td>'.$fmw_prosesawal.'</td>
                <td>'.$fmw_prosesakhir.'</td>
                <td>'.$total_fmwproses.'</td>
                <td>'.$fmd_awal.'</td>
                <td>'.$fmd_akhir.'</td>
                <td>'.$total_fmd.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';
                    
                   
            }
              
                $rata_alsppm = $total_alsppm / $no;
                $rata_sappm = $total_sappm / $no;
                $rata_mfppm = $total_mfppm / $no;
                    
                echo '                      <tr class=rowcontent>
               
                  <td colspan=6>Total</td>
                <td>'.$total_alskg.'</td>
                <td>'.$rata_alsppm.'</td>
                <td></td>
                 <td></td>
                 <td></td>
                 <td>'.$total_sakg.'</td>
                 <td>'.$rata_sappm.'</td>
                 <td></td>
                 <td></td>
                 <td></td>
                   <td>'.$total_mfkg.'</td>
                 <td>'.$rata_mfppm.'</td>
                    </tr> </table>';
               
           $sPower2 = "SELECT * FROM pabrik_wtpinternal WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                <tr>
              
               <td colspan=18></td>
                </tr>
                  <tr>
              
               <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>Nama</td>
                <td>Alkalinity Boster Phospate Polimer Start (Jam)</td>
                <td>Alkalinity Boster Phospate Polimer Stop (Jam)</td>
                <td>Sisa Alkalinity Boster Phospate Polimer (L)</td>
                <td>Oxigen Scavenger Start (Jam)</td>
                <td>Oxigen Scavenger Stop (Jam)</td>
                <td>sisa Oxigen Scavenger (L)</td>
                <td>W 33 (kg)</td>
                <td>W 33 (ppm)</td>
                <td>W 44L (kg)</td>
                <td>W 44L (ppm)</td>
                <td>W 45 (kg)</td>
                <td>W 45 (ppm)</td>
                <td>W 66 (kg)</td>
                <td>W 66 (ppm)</td>
                <td>Garam (kg)</td>
                <td>Waktu Backwash Softener 1 (1)</td>
                <td>Waktu Backwash Softener 2 (1)</td>
                <td>Waktu Backwash Softener 1 (2)</td>
                <td>Waktu Backwash Softener 2 (2)</td>
                <td>Flowmeter Awal Softener (m3)</td>
                <td>Flowmeter Akhir Softener (m3)</td>
                <td>Flowmeter Total Softener (m3)</td>
                <td>Flowmeter Awal Boiler (m3)</td>
                <td>Flowmeter Akhir Boiler (m3)</td>
                <td>Flowmeter Total Boiler (m3)</td>
                <td>Keterangan</td>
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
                ++$no2;
               $tgl= $res2['tgl'];
            $shift_nik= $res2['shift_nik'];
            $abpp_start = $res2['abpp_start'];
            $abpp_stop = $res2['abpp_stop'];
            $sisa_abpp = $res2['sisa_abpp'];
            $os_start = $res2['os_start'];
            $os_stop = $res2['os_stop'];;
            $sisa_os = $res2['sisa_os'];
            $w33_kg = $res2['w33_kg'];
            $total_w33kg += $res2['w33_kg'];
            $w33_ppm = $res2['w33_ppm'];
            $total_w33ppm += $res2['w33_ppm'];
            $w44_kg = $res2['w44_kg'];
            $total_w44kg += $res2['w44_kg'];
            $w44_ppm = $res2['w44_ppm'];
            $total_w44ppm += $res2['w44_ppm'];
            $w45_kg = $res2['w45_kg'];
            $total_w45kg += $res2['w45_kg'];
            $w45_ppm = $res2['w45_ppm'];
            $total_w45ppm += $res2['w45_ppm'];
            $w66_kg = $res2['w66_kg'];
            $total_w66kg += $res2['w66_kg'];
            $w66_ppm = $res2['w66_ppm'];
            $total_w66ppm += $res2['w66_ppm'];
            $garam = $res2['garam'];
            $total_garam += $res2['garam'];
            $wbs1 = $res2['wbs1'];
            $wbs2 = $res2['wbs2'];
            $wbs1_2 = $res2['wbs1_2'];
            $wbs2_2 = $res2['wbs2_2'];
            $flowmeter_awals = $res2['flowmeter_awals'];
            $flowmeter_akhirs = $res2['flowmeter_akhirs'];
            $flowmeter_totals = $flowmeter_akhirs - $flowmeter_awals;
            $flowmeter_awalb = $res2['flowmeter_awalb'];
            $flowmeter_akhirb = $res2['flowmeter_akhirb'];
            $flowmeter_totalb = $flowmeter_akhirb - $flowmeter_awalb;
            $keterangan = $res2['keterangan'];
                echo '<tr class=rowcontent>
                  <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                 <td>'.$namakaryawan.'</td>
                <td>'.$abpp_start.'</td>
                <td>'.$abpp_stop.'</td>
                <td >'.$sisa_abpp.'</td>
                <td>'.$os_start.'</td>
                <td>'.$os_stop.'</td>
                <td>'.$sisa_os.'</td>
                <td>'.$w33_kg.'</td>
                <td>'.$w33_ppm.'</td>
                <td>'.$w44_kg.'</td>
                <td>'.$w44_ppm.'</td>
                <td>'.$w45_kg.'</td>
                <td>'.$w45_ppm.'</td>
                <td>'.$w66_kg.'</td>
                <td>'.$w66_ppm.'</td>
                <td>'.$garam.'</td>
                <td>'.$wbs1.'</td>
                <td>'.$wbs2.'</td>
                <td>'.$wbs1_2.'</td>
                <td>'.$wbs2_2.'</td>
                <td>'.$flowmeter_awals.'</td>
                <td>'.$flowmeter_akhirs.'</td>
                <td>'.$flowmeter_totals.'</td>
                <td>'.$flowmeter_awalb.'</td>
                <td>'.$flowmeter_akhirb.'</td>
                <td>'.$flowmeter_totalb.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

                   
            }
                $rata_w33ppm = $total_w33ppm / $no2;
                $rata_w44ppm = $total_w44ppm / $no2;
                $rata_w45ppm = $total_w45ppm / $no2;
                $rata_w66ppm = $total_w66ppm / $no2;
                  echo '<tr class=rowcontent>
                    <td colspan=9>Total</td>
                <td>'.$total_w33kg.'</td>
                <td>'.$rata_w33ppm.'</td>
                <td>'.$total_w44kg.'</td>
                <td>'.$rata_w44ppm.'</td>
                <td>'.$total_w45kg.'</td>
                <td>'.$rata_w45ppm.'</td>
                <td>'.$total_w66kg.'</td>
                <td>'.$rata_w66ppm.'</td>
                <td>'.$total_garam.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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

            $sPower1 = "SELECT * FROM pabrik_wtpexternal WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL WATER TREATMENT</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




       

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
              
                 <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>Nama </td>
                <td>Alumunium Shulpit Start (Jam)</td>
                <td>Alumunium Shulpit Stop (Jam)</td>
                <td>Sisa Alumunium Shulpit (L)</td>
                <td>Alumunium Shulpit (Kg)</td>
                <td>Alumunium Shulpit (ppm)</td>
                <td>Soda Ash Start (Jam)</td>
                <td>Soda Ash Stop (Jam)</td>
                <td>Sisa Soda Ash (L)</td>
                <td>Soda Ash (Kg)</td>
                <td>Soda Ash (ppm)</td>
                <td>Multi Flock Start (Jam)</td>
                <td>Multi Flock Stop (Jam)</td>
                <td>Sisa Multi Flock (L)</td>
                <td>Multi Flock (Kg)</td>
                <td>Multi Flock (ppm)</td>
                <td>Waktu Backwash Sand Filter 1 (1)</td>
                <td>Waktu Backwash Sand Filter 2 (1)</td>
                <td>Waktu Backwash Sand Filter 1 (2)</td>
                <td>Waktu Backwash Sand Filter 2 (2)</td>
                <td>Raw Water Pump 1 Start (Jam)</td>
                <td>Raw Water Pump 1 Stop (Jam)</td>
                <td>Raw Water Pump 1 Total (Jam)</td>
                <td>Raw Water Pump 2 Start (Jam)</td>
                <td>Raw Water Pump 2 Stop (Jam)</td>
                <td>Raw Water Pump 2 Total (Jam)</td>
                <td>Flow Meter Waduk Awal (m3)</td>
                <td>Flow Meter Waduk Akhir (m3)</td>
                <td>Flow Meter Waduk Total (m3)</td>
                <td>Flowmeter Awal Proses (m3)</td>
                <td>Flowmeter Akhir Proses (m3)</td>
                <td>Flowmeter Total Proses (m3)</td>
                <td>Flowmeter Awal Domestic (m3)</td>
                <td>Flowmeter Akhir Domestic (m3)</td>
                <td>Flowmeter Total Domestic (m3)</td>
                <td>Keterangan</td>
                </tr>
                </thead>
            <tbody>
           ";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
        // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {

                $sKaryawan = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$res['shift_nik']."'  ";
                 $qK = mysql_query($sKaryawan);
                 while ($resK = mysql_fetch_assoc($qK)) {
                    $namakaryawan = $resK['namakaryawan'];
                 }
               ++$no;
           $tgl= $res['tgl'];
            $shift_nik= $res['shift_nik'];
            $als_start = $res['als_start'];
            $als_stop = $res['als_stop'];
            $sisa_als = $res['sisa_als'];
            $als_kg = $res['als_kg'];
            $als_ppm = $res['als_ppm'];;
            $sa_start = $res['sa_start'];
            $sa_stop = $res['sa_stop'];
            $sisa_sa = $res['sisa_sa'];
            $sa_kg = $res['sa_kg'];
            

            $sa_ppm = $res['sa_ppm'];
            

            $mf_start = $res['mf_start'];
            $mf_stop = $res['mf_stop'];
            $sisa_mf = $res['sisa_mf'];
            $mf_kg = $res['mf_kg'];
            $mf_ppm = $res['mf_ppm'];

            $total_sakg += $sa_kg;
            $total_sappm += $sa_ppm;
            $total_mfkg += $mf_kg;
            $total_mfppm += $mf_ppm;

            $wbs1 = $res['wbs1'];
            $wbs2 = $res['wbs2'];
            $wbs1_2 = $res['wbs1_2'];
            $wbs2_2 = $res['wbs2_2'];
            $rwp1_start = $res['rwp1_start'];
            $rwp1_stop = $res['rwp1_stop'];
            $total_rwp1 = $rwp1_stop - $rwp1_start;
            $rwp2_start = $res['rwp2_start'];
            $rwp2_stop = $res['rwp2_stop'];
            $total_rwp2 = $rwp2_stop - $rwp2_start;
            $fmw_awal = $res['fmw_awal'];
            $fmw_akhir = $res['fmw_akhir'];
            $total_fmw =  $fmw_akhir - $fmw_awal;
            $fmw_prosesawal = $res['fmw_prosesawal'];
            $fmw_prosesakhir = $res['fmw_prosesakhir'];
            $total_fmwproses =  $fmw_prosesakhir - $fmw_prosesawal;
            $fmd_awal = $res['fmd_awal'];
            $fmd_akhir = $res['fmd_akhir'];
            $total_fmd =  $fmd_akhir - $fmd_awal;
            $keterangan = $res['keterangan'];
            $total_alskg += $als_kg;
            $total_alsppm += $als_ppm;

                $tab .=  '<tr class=rowcontent>
               <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$als_start.'</td>
                <td>'.$als_stop.'</td>
                <td >'.$sisa_als.'</td>
                <td>'.$als_kg.'</td>
                <td>'.$als_ppm.'</td>
                <td>'.$sa_start.'</td>
                <td>'.$sa_stop.'</td>
                <td>'.$sisa_sa.'</td>
                <td>'.$sa_kg.'</td>
                <td>'.$sa_ppm.'</td>
                <td>'.$mf_start.'</td>
                <td>'.$mf_stop.'</td>
                <td>'.$sisa_mf.'</td>
                <td>'.$mf_kg.'</td>
                <td>'.$mf_ppm.'</td>
                <td>'.$wbs1.'</td>
                <td>'.$wbs2.'</td>
                <td>'.$wbs1_2.'</td>
                <td>'.$wbs2_2.'</td>
                <td>'.$rwp1_start.'</td>
                <td>'.$rwp1_stop.'</td>
                <td>'.$total_rwp1.'</td>
                <td>'.$rwp2_start.'</td>
                <td>'.$rwp2_stop.'</td>
                <td>'.$total_rwp2.'</td>
                <td>'.$fmw_awal.'</td>
                <td>'.$fmw_akhir.'</td>
                <td>'.$total_fmw.'</td>
                <td>'.$fmw_prosesawal.'</td>
                <td>'.$fmw_prosesakhir.'</td>
                <td>'.$total_fmwproses.'</td>
                <td>'.$fmd_awal.'</td>
                <td>'.$fmd_akhir.'</td>
                <td>'.$total_fmd.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';
                  
               
            }
            $rata_alsppm = $total_alsppm / $no;
             $rata_sappm = $total_sappm / $no;
                $rata_mfppm = $total_mfppm / $no;
                    
                $tab .= '                      <tr class=rowcontent>
               
                  <td colspan=6>Total</td>
                <td>'.$total_alskg.'</td>
                <td>'.$rata_alsppm.'</td>
                <td></td>
                 <td></td>
                 <td></td>
                 <td>'.$total_sakg.'</td>
                 <td>'.$rata_sappm.'</td>
                 <td></td>
                 <td></td>
                 <td></td>
                   <td>'.$total_mfkg.'</td>
                 <td>'.$rata_mfppm.'</td>
                    </tr> </table>';
               
           $sPower2 = "SELECT * FROM pabrik_wtpinternal WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        $tab .= "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                <tr ></tr><tr></tr></table>";
        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
               
                  <tr bgcolor=#DEDEDE>
              
                <table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
               
                  <tr bgcolor=#DEDEDE>
              
               <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>Nama</td>
                <td>Alkalinity Boster Phospate Polimer Start (Jam)</td>
                <td>Alkalinity Boster Phospate Polimer Stop (Jam)</td>
                <td>Sisa Alkalinity Boster Phospate Polimer (L)</td>
                <td>Oxigen Scavenger Start (Jam)</td>
                <td>Oxigen Scavenger Stop (Jam)</td>
                <td>sisa Oxigen Scavenger (L)</td>
                <td>W 33 (kg)</td>
                <td>W 33 (ppm)</td>
                <td>W 44L (kg)</td>
                <td>W 44L (ppm)</td>
                <td>W 45 (kg)</td>
                <td>W 45 (ppm)</td>
                <td>W 66 (kg)</td>
                <td>W 66 (ppm)</td>
                <td>Garam (kg)</td>
                <td>Waktu Backwash Softener 1 (1)</td>
                <td>Waktu Backwash Softener 2 (1)</td>
                <td>Waktu Backwash Softener 1 (2)</td>
                <td>Waktu Backwash Softener 2 (2)</td>
                <td>Flowmeter Awal Softener (m3)</td>
                <td>Flowmeter Akhir Softener (m3)</td>
                <td>Flowmeter Total Softener (m3)</td>
                <td>Flowmeter Awal Boiler (m3)</td>
                <td>Flowmeter Akhir Boiler (m3)</td>
                <td>Flowmeter Total Boiler (m3)</td>
                <td>Keterangan</td>
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
                  ++$no2;
               $tgl= $res2['tgl'];
            $shift_nik= $res2['shift_nik'];
            $abpp_start = $res2['abpp_start'];
            $abpp_stop = $res2['abpp_stop'];
            $sisa_abpp = $res2['sisa_abpp'];
            $os_start = $res2['os_start'];
            $os_stop = $res2['os_stop'];;
            $sisa_os = $res2['sisa_os'];
            $w33_kg = $res2['w33_kg'];
            $total_w33kg += $res2['w33_kg'];
            $w33_ppm = $res2['w33_ppm'];
            $total_w33ppm += $res2['w33_ppm'];
            $w44_kg = $res2['w44_kg'];
            $total_w44kg += $res2['w44_kg'];
            $w44_ppm = $res2['w44_ppm'];
            $total_w44ppm += $res2['w44_ppm'];
            $w45_kg = $res2['w45_kg'];
            $total_w45kg += $res2['w45_kg'];
            $w45_ppm = $res2['w45_ppm'];
            $total_w45ppm += $res2['w45_ppm'];
            $w66_kg = $res2['w66_kg'];
            $total_w66kg += $res2['w66_kg'];
            $w66_ppm = $res2['w66_ppm'];
            $total_w66ppm += $res2['w66_ppm'];
            $garam = $res2['garam'];
            $total_garam += $res2['garam'];
            $wbs1 = $res2['wbs1'];
            $wbs2 = $res2['wbs2'];
            $wbs1_2 = $res2['wbs1'];
            $wbs2_2 = $res2['wbs2'];
            $flowmeter_awals = $res2['flowmeter_awals'];
            $flowmeter_akhirs = $res2['flowmeter_akhirs'];
            $flowmeter_totals = $flowmeter_akhirs - $flowmeter_awals;
            $flowmeter_awalb = $res2['flowmeter_awalb'];
            $flowmeter_akhirb = $res2['flowmeter_akhirb'];
            $flowmeter_totalb = $flowmeter_akhirb - $flowmeter_awalb;
            $keterangan = $res2['keterangan'];

                $tab .= '<tr class=rowcontent>
                   <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                 <td>'.$namakaryawan.'</td>
                <td>'.$abpp_start.'</td>
                <td>'.$abpp_stop.'</td>
                <td >'.$sisa_abpp.'</td>
                <td>'.$os_start.'</td>
                <td>'.$os_stop.'</td>
                <td>'.$sisa_os.'</td>
                <td>'.$w33_kg.'</td>
                <td>'.$w33_ppm.'</td>
                <td>'.$w44_kg.'</td>
                <td>'.$w44_ppm.'</td>
                <td>'.$w45_kg.'</td>
                <td>'.$w45_ppm.'</td>
                <td>'.$w66_kg.'</td>
                <td>'.$w66_ppm.'</td>
                <td>'.$garam.'</td>
                <td>'.$wbs1.'</td>
                <td>'.$wbs2.'</td>
                <td>'.$wbs1_2.'</td>
                <td>'.$wbs2_2.'</td>
                <td>'.$flowmeter_awals.'</td>
                <td>'.$flowmeter_akhirs.'</td>
                <td>'.$flowmeter_totals.'</td>
                <td>'.$flowmeter_awalb.'</td>
                <td>'.$flowmeter_akhirb.'</td>
                <td>'.$flowmeter_totalb.'</td>
                <td>'.$keterangan.'</td>
                    </tr>  ';

                   
            }
             $rata_w33ppm = $total_w33ppm / $no2;
                $rata_w44ppm = $total_w44ppm / $no2;
                $rata_w45ppm = $total_w45ppm / $no2;
                $rata_w66ppm = $total_w66ppm / $no2;
                 $tab .= '<tr class=rowcontent>
                    <td colspan=9>Total</td>
                <td>'.$total_w33kg.'</td>
                <td>'.$rata_w33ppm.'</td>
                <td>'.$total_w44kg.'</td>
                <td>'.$rata_w44ppm.'</td>
                <td>'.$total_w45kg.'</td>
                <td>'.$rata_w45ppm.'</td>
                <td>'.$total_w66kg.'</td>
                <td>'.$rata_w66ppm.'</td>
                <td>'.$total_garam.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                    </tr>';
             $tab .= '   </table><table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
                </tr>
                <tr></tr>
                 <tr></table>';
             $tab .= '   <table cellspacing=1 border=1 class=sortable>                   
                
                
                <tr>
                <tr></tr>
                <tr></tr>
                <td>Jabatan</td>
                <td> Nama</td>
                <td>Paraf </td>
               
                 </tr><tr>
                <td> Asisten</td>
                <td> </td>
                <td> </td>
                </tr>
                <tr>
                 
                <td> Mandor</td>
                <td> </td>
                <td> </td>
               
                 </tr><tr>
                <td>Operator </td>
                <td> </td>
                <td> </td>
                
                 
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

        $nop_ = 'LaporanWTPNeww'.$tglSkrg;
         // echo $tab;
        // if (0 < strlen($tab)) {

        //     if ($handle = opendir('tempExcel')) {

        //         while (false !== ($file = readdir($handle))) {

        //             if ('.' !== $file && '..' !== $file) {

        //                 @unlink('tempExcel/'.$file);

        //             }

        //         }

        //         closedir($handle);

        //     }



        //     $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');

        //     if (!fwrite($handle, $tab)) {

        //         echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";

        //         exit();

        //     }



        //     echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";

        //     closedir($handle);

        // }



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