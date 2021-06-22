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
$gt_tbs_olah   += 0;
            $gt_hm_awal_g1  = 0;
            $gt_hm_akhir_g1 = 0;              
            $gt_jumlah_hm_g1 = 0;
            $gt_hm_awal_g2   = 0;
            $gt_hm_akhir_g2   = 0;
            $gt_jumlah_hm_g2   =  0;
            $gt_hm_awal_g3    = 0;
            $gt_hm_akhir_g3   =  0;
            $gt_jumlah_hm_g3   =  0;
            $gt_total_hm   =  0;
            $gt_gg_hm   =  0;
            $gt_gt_hm   =  0;
            $gt_total_paralel  =  0;
            $gt_kwh_g1   =  0;
            $gt_kwh_g2   =  0;
            $gt_kwh_g3   =  0;
            $gt_total_kwh   =  0;
            $gt_kw1   =  0;
            $gt_kw2   =  0;
            $gt_kw3   =  0;
            $gt_total_kw   =  0;
            $gt_bbm_g1   =  0;
            $gt_bbm_g2   =  0;
            $gt_bbm_g3   =  0;
            $gt_total_bbm   =  0;
            $gt_rasio_bbm1   =  0;
            $gt_rasio_bbm2   =  0;


            $gt_hm_bolah_t1   =  0;
            $gt_hm_bolah_t2   =  0;
            $gt_total_bolah_hm   =  0;
            $gt_hm_kolah_t1   =  0;
             $gt_hm_kolah_t2  =  0;
            $gt_total_kolah_hm   =  0;
            $gt_hm_total_t1   =  0;
            $gt_hm_total_t2   =  0;
            $gt_total_hmt   =  0;
            $gt_kwh_bolah_t1   =  0;
            $gt_kwh_bolah_t2   =  0;
            $gt_total_bolah_kwh   =  0;
            $gt_total_pemakaian_kw_t1b   =  0;
            $gt_total_pemakaian_kw_t2b   =  0;
            $gt_total_pemakaian_kwb   =  0;
            $gt_kwh_kolah_t1   =  0;
            $gt_kwh_kolah_t2   =  0;
            $gt_total_kolah_kwh   =  0;
            $gt_total_pemakaian_kw_t1k   =  0;
            $gt_total_pemakaian_kw_t2k   =  0;
            $gt_total_pemakaian_kwk   =  0;
            $gt_total_pemakaian_kwh_t1   =  0;
            $gt_total_pemakaian_kwh_t2   =  0;
            $gt_total_pemakaian_kwh   =  0;
            $gt_total_pemakaian_kw_t1   =  0;
            $gt_total_pemakaian_kw_t2   =  0;
            $gt_total_pemakaian_kw   =  0;
            $gt_rasio_kw_olah   =  0;
            $gt_total   =  0;
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM pabrik_power1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
                <td colspan=31>Genset</td>
                <td colspan=32>Turbin</td>
                </tr>
                 <tr>
                 <td>No</td>
                <td>Tanggal</td>
                <td>TBS OLAH (Kg)</td>
                <td>HM AWAL G1</td>
                <td>HM AKHIR G1</td>
                <td>JUMLAH HM G1</td>
                <td>HM AWAL G2</td>
                <td>HM AKHIR G2</td>
                <td>JUMLAH HM G2</td>
                <td>HM AWAL G3</td>
                <td>HM AKHIR G3</td>
                <td>JUMLAH HM G3</td>
                <td>TOTAL</td>
                <td>G//G (HM)</td>
                <td>G//T (HM)</td>
                <td>TOTAL PARALEL</td>
                <td>KWH G1</td>
                <td>KWH G2</td>
                <td>KWH G3</td>
                <td>TOTAL KWH</td>
                <td>KW G1</td>
                <td>KW G2</td>
                <td>KW G3</td>
                <td>TOTAL KW</td>
                <td>BBM G1</td>
                <td>BBM G2</td>
                <td>BBM G3</td>
                <td>TOTAL BBM</td>
                <td>RASIO BBM G1</td>
                <td>RASIO BBM G2</td>
                <td>RASIO BBM G3</td>

                <td>JAM KERJA SEBELUM OLAH T1 (hm)</td>
                <td>JAM KERJA SEBELUM OLAH T2 (hm)</td>
                <td>TOTAL JAM KERJA SEBELUM OLAH</td>
                <td>JAM KERJA KETIKA OLAH T1 (hm)</td>
                <td>JAM KERJA KETIKA OLAH T2 (hm)</td>
                <td>TOTAL JAM KERJA KETIKA OLAH</td>
                <td>TOTAL JAM KERJA T1</td>
                <td>TOTAL JAM KERJA T2</td>
                <td>TOTAL JAM KERJA</td>
                <td>PEMAKAIAN KWH SEBELUM OLAH T1 </td>
                <td>PEMAKAIAN KWH SEBELUM OLAH T2</td>
                <td>TOTAL PEMAKAIAN KWH SEBELUM OLAH</td>
                <td>PEMAKAIAN KW SEBELUM OLAH T1</td>
                <td>PEMAKAIAN KW SEBELUM OLAH T2</td>
                <td>TOTAL PEMAKAIAN KW SEBELUM OLAH</td>
                <td>PEMAKAIAN KWH KETIKA OLAH T1 </td>
                <td>PEMAKAIAN KWH KETIKA OLAH T2</td>
                <td>TOTAL PEMAKAIAN KWH SEBELUM OLAH</td>
                <td>PEMAKAIAN KW KETIKA OLAH T1</td>
                <td>PEMAKAIAN KW KETIKA OLAH T2</td>
                <td>TOTAL PEMAKAIAN KW KETIKA OLAH</td>
                <td>TOTAL PEMAKAIAN KWH T1</td>
                <td>TOTAL PEMAKAIAN KWH T2</td>
                <td>TOTAL KWH </td>
                <td>TOTAL PEMAKAIAN KW T1</td>
                <td>TOTAL PEMAKAIAN KW T2</td>
                <td>TOTAL KW </td>
                <td>RASIO KW OLAH </td>
                <td>KW CONSUMPTION PROSES</td>
                <td>KW CONSUMPTION DOMESTIK </td>
                <td>KW CONSUMPTION TOTAL</td>
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
            $hm_awal_g1= $res['hm_awal_g1'];
            $hm_akhir_g1 = $res['hm_akhir_g1'];
            $jumlah_hm_g1 = ($hm_akhir_g1) - ($hm_awal_g1);
            $hm_awal_g2 = $res['hm_awal_g2'];
            $hm_akhir_g2 = $res['hm_akhir_g2'];
             $jumlah_hm_g2 = ($hm_akhir_g2) - ($hm_awal_g2);
            $hm_awal_g3 = $res['hm_awal_g3'];
            $hm_akhir_g3 = $res['hm_akhir_g3'];
            $jumlah_hm_g3 = ($hm_akhir_g3) - ($hm_awal_g3);
            $total_hm = $jumlah_hm_g1+$jumlah_hm_g2+$jumlah_hm_g3;
            $gg_hm = $res['gg_hm'];;
            $gt_hm = $res['gt_hm'];
            $total_paralel = $gg_hm + $gt_hm;
            $kwh_g1 = $res['kwh_g1'];
            $kwh_g2 = $res['kwh_g2'];
            $kwh_g3 = $res['kwh_g3'];
            $total_kwh = $kwh_g1 + $kwh_g2 + $kwh_g3;

             $skw1 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG1' ";
             $skw2 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG2' ";
             $skw3 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG3' ";

            $qkw1 = mysql_query($skw1);
            $qkw2 = mysql_query($skw2);
            $qkw3 = mysql_query($skw3);
        
         while ($reskw1 = mysql_fetch_assoc($qkw1)) {
            $nilai_kw1= $reskw1['nilai'];
        }
        while ($reskw2 = mysql_fetch_assoc($qkw2)) {
            $nilai_kw2= $reskw2['nilai'];
        }
        while ($reskw3 = mysql_fetch_assoc($qkw3)) {
            $nilai_kw3= $reskw3['nilai'];
        }

            $kw1 = $kwh_g1 * $nilai_kw1;
            $kw2 = $kwh_g2 * $nilai_kw2;
            $kw3 = $kwh_g3 * $nilai_kw3;
            $total_kw = $kw1 + $kw2 +$kw3;
            $bbm_g1 = $res['bbm_g1'];
            $bbm_g2 = $res['bbm_g2'];
            $bbm_g3 = $res['bbm_g3'];

            $total_bbm = $bbm_g1 + $bbm_g2 + $bbm_g3;
            $rasio_bbm1 = $bbm_g1 / $kw1;
            $rasio_bbm2 =  $bbm_g2 / $kw2;
            $rasio_bbm3 = $bbm_g3 / $kw3;
            $sPower2 = "SELECT * FROM pabrik_power2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) = '".$tgl."' ";

            $qData2 = mysql_query($sPower2);
        while ($res2 = mysql_fetch_assoc($qData2)) {

             $skwt1 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWT1' ";
             $skwt2 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWT2' ";

            $qkwt1 = mysql_query($skwt1);
            $qkwt2 = mysql_query($skwt2);
        
         while ($reskwt1 = mysql_fetch_assoc($qkwt1)) {
            $nilai_kwt1= $reskwt1['nilai'];
        }
        while ($reskwt2 = mysql_fetch_assoc($qkwt2)) {
            $nilai_kwt2= $reskw2['nilai'];
        }


            $hm_bolah_t1= $res2['hm_bolah_t1'];
            $hm_bolah_t2 = $res2['hm_bolah_t2'];
            $total_bolah_hm = $hm_bolah_t1 + $hm_bolah_t2;
            $hm_kolah_t1 = $res2['hm_kolah_t1'];
            $hm_kolah_t2 = $res2['hm_kolah_t2'];
            $total_kolah_hm = $hm_kolah_t1 + $hm_kolah_t2;
            $hm_total_t1 = $hm_bolah_t1 + $hm_kolah_t1;
            $hm_total_t2 = $hm_bolah_t2 + $hm_kolah_t2;
            $total_hmt = $hm_total_t1 + $hm_total_t2;
            $kwh_bolah_t1 = $res2['kwh_bolah_t1'];
            $kwh_bolah_t2 = $res2['kwh_bolah_t2'];
            $total_bolah_kwh = $kwh_bolah_t1 + $kwh_bolah_t2;
            $total_pemakaian_kw_t1b =  $res2['kwh_bolah_t1'] * ($nilai_kwt1);
            $total_pemakaian_kw_t2b =  $res2['kwh_bolah_t2'] * ($nilai_kwt2);
            $total_pemakaian_kwb = $total_pemakaian_kw_t1b + $total_pemakaian_kw_t2b;
            $kwh_kolah_t1 = $res2['kwh_kolah_t1'];
            $kwh_kolah_t2 = $res2['kwh_kolah_t2'];
            $total_kolah_kwh = $kwh_kolah_t1 + $kwh_kolah_t2;
            $total_pemakaian_kw_t1k =  $res2['kwh_kolah_t1'] * ($nilai_kwt1);
            $total_pemakaian_kw_t2k =  $res2['kwh_kolah_t2'] * ($nilai_kwt2);
            $total_pemakaian_kwk = $total_pemakaian_kw_t1k + $total_pemakaian_kw_t2k;
            $total_pemakaian_kwh_t1 = $kwh_bolah_t1 + $kwh_kolah_t1;
            $total_pemakaian_kwh_t2 = $kwh_bolah_t2 + $kwh_kolah_t2;
            $total_pemakaian_kwh = $total_pemakaian_kwh_t1 + $total_pemakaian_kwh_t2;
            $total_pemakaian_kw_t1 = $total_pemakaian_kw_t1k + $total_pemakaian_kw_t1b;
            $total_pemakaian_kw_t2 = $total_pemakaian_kw_t2k + $total_pemakaian_kw_t2b;
            $total_pemakaian_kw = $total_pemakaian_kw_t1 + $total_pemakaian_kw_t2;

            $keterangan = $res2['keterangan'];
        }
        $spabrik = "SELECT * FROM pabrik_produksi WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tanggal) = '".$tgl."' ";

            $qDatap = mysql_query($spabrik);
            $brsp = mysql_num_rows($qDatap);
        if ($brsp >0){
         while ($resp = mysql_fetch_assoc($qDatap)) {
            $tbs_olah= $resp['tbs_diolah'];
        }
        }else{
            $tbs_olah= 0;
        }
       
            $rasio_kw_olah = $total_pemakaian_kwk/($tbs_olah);
            $total = $total_pemakaian_kwk + $total_pemakaian_kwb;
                echo "<tr class=rowcontent><td>".$no."</td>
                       <td>".$tgl."</td>
                       <td>".$tbs_olah."</td>
                <td>".$hm_awal_g1."</td>
                <td>".$hm_akhir_g1."</td>                
                <td>".round($jumlah_hm_g1,2)."</td>
                <td>".$hm_awal_g2."</td>
                <td >".$hm_akhir_g2."</td>
                <td>".round($jumlah_hm_g2,2)."</td>
                <td>".$hm_awal_g3."</td>
                <td>".$hm_akhir_g3."</td>
                <td>".round($jumlah_hm_g3,2)."</td>
                <td>".$total_hm."</td>
                <td>".$gg_hm."</td>
                <td>".$gt_hm."</td>
                <td>".$total_paralel."</td>
                <td>".$kwh_g1."</td>
                <td>".$kwh_g2."</td>
                <td>".$kwh_g3."</td>
                <td>".$total_kwh."</td>
                 <td>".$kw1."</td>
                <td>".$kw2."</td>
                <td>".$kw3."</td>
                <td>".$total_kw."</td>
                <td>".$bbm_g1."</td>
                <td>".$bbm_g2."</td>
                <td>".$bbm_g3."</td>
                <td>".$total_bbm."</td>
                <td>".round($rasio_bbm1,2)."</td>
                <td>".round($rasio_bbm2,2)."</td>
                <td>".round($rasio_bbm3,2)."</td>



                <td>".$hm_bolah_t1."</td>
                <td>".$hm_bolah_t2."</td>
                <td>".$total_bolah_hm."</td>
                <td>".$hm_kolah_t1."</td>
                <td >".$hm_kolah_t2."</td>
                <td>".$total_kolah_hm."</td>
                <td>".$hm_total_t1."</td>
                <td >".$hm_total_t2."</td>
                <td>".$total_hmt."</td>
                <td>".$kwh_bolah_t1."</td>
                <td>".$kwh_bolah_t2."</td>
                <td>".$total_bolah_kwh."</td>
                <td>".$total_pemakaian_kw_t1b."</td>
                <td>".$total_pemakaian_kw_t2b."</td>
                <td>".$total_pemakaian_kwb."</td>
                <td>".$kwh_kolah_t1."</td>
                <td>".$kwh_kolah_t2."</td>
                <td>".$total_kolah_kwh."</td>
                <td>".$total_pemakaian_kw_t1k."</td>
                <td>".$total_pemakaian_kw_t2k."</td>
                <td>".$total_pemakaian_kwk."</td>
                <td>".$total_pemakaian_kwh_t1."</td>
                <td>".$total_pemakaian_kwh_t2."</td>
                <td>".$total_pemakaian_kwh."</td>
                <td>".$total_pemakaian_kw_t1."</td>
                <td>".$total_pemakaian_kw_t2."</td>
                <td>".$total_pemakaian_kw."</td>
                <td>".round($rasio_kw_olah,5)."</td>
                <td>".$total_pemakaian_kwk."</td>
                <td>".$total_pemakaian_kwb."</td>
                <td>".$total."</td>
                <td>".$keterangan."</td>
                    </tr>";

          


            $gt_tbs_olah   += $tbs_olah;
            $gt_hm_awal_g1  += $hm_awal_g1;
            $gt_hm_akhir_g1 += $hm_akhir_g1;              
            $gt_jumlah_hm_g1 += round($jumlah_hm_g1,2);
            $gt_hm_awal_g2   += $hm_awal_g2;
            $gt_hm_akhir_g2   += $hm_akhir_g2;
            $gt_jumlah_hm_g2   +=  round($jumlah_hm_g2,2);
            $gt_hm_awal_g3    += $hm_awal_g3;
            $gt_hm_akhir_g3   +=  $hm_akhir_g3;
            $gt_jumlah_hm_g3   +=  round($jumlah_hm_g3,2);
            $gt_total_hm   +=  $total_hm;
            $gt_gg_hm   +=  $gg_hm;
            $gt_gt_hm   +=  $gt_hm;
            $gt_total_paralel  +=  $total_paralel;
            $gt_kwh_g1   +=  $kwh_g1;
            $gt_kwh_g2   +=  $kwh_g2;
            $gt_kwh_g3   +=  $kwh_g3;
            $gt_total_kwh   +=  $total_kwh;
            $gt_kw1   +=  $kw1;
            $gt_kw2   +=  $kw2;
            $gt_kw3   +=  $kw3;
            $gt_total_kw   +=  $total_kw;
            $gt_bbm_g1   +=  $bbm_g1;
            $gt_bbm_g2   +=  $bbm_g2;
            $gt_bbm_g3   +=  $bbm_g3;
            $gt_total_bbm   +=  $total_bbm;
            $gt_rasio_bbm1   +=  round($rasio_bbm1,2);
            $gt_rasio_bbm2   +=  round($rasio_bbm2,2);
            $gt_rasio_bbm3   +=  round($rasio_bbm3,2);



            $gt_hm_bolah_t1   +=  $hm_bolah_t1;
            $gt_hm_bolah_t2   +=  $hm_bolah_t2;
            $gt_total_bolah_hm   +=  $total_bolah_hm;
            $gt_hm_kolah_t1   +=  $hm_kolah_t1;
             $gt_hm_kolah_t2  +=  $hm_kolah_t2;
            $gt_total_kolah_hm   +=  $total_kolah_hm;
            $gt_hm_total_t1   +=  $hm_total_t1;
            $gt_hm_total_t2   +=  $hm_total_t2;
            $gt_total_hmt   +=  $total_hmt;
            $gt_kwh_bolah_t1   +=  $kwh_bolah_t1;
            $gt_kwh_bolah_t2   +=  $kwh_bolah_t2;
            $gt_total_bolah_kwh   +=  $total_bolah_kwh;
            $gt_total_pemakaian_kw_t1b   +=  $total_pemakaian_kw_t1b;
            $gt_total_pemakaian_kw_t2b   +=  $total_pemakaian_kw_t2b;
            $gt_total_pemakaian_kwb   +=  $total_pemakaian_kwb;
            $gt_kwh_kolah_t1   +=  $kwh_kolah_t1;
            $gt_kwh_kolah_t2   +=  $kwh_kolah_t2;
            $gt_total_kolah_kwh   +=  $total_kolah_kwh;
            $gt_total_pemakaian_kw_t1k   +=  $total_pemakaian_kw_t1k;
            $gt_total_pemakaian_kw_t2k   +=  $total_pemakaian_kw_t2k;
            $gt_total_pemakaian_kwk   +=  $total_pemakaian_kwk;
            $gt_total_pemakaian_kwh_t1   +=  $total_pemakaian_kwh_t1;
            $gt_total_pemakaian_kwh_t2   +=  $total_pemakaian_kwh_t2;
            $gt_total_pemakaian_kwh   +=  $total_pemakaian_kwh;
            $gt_total_pemakaian_kw_t1   +=  $total_pemakaian_kw_t1;
            $gt_total_pemakaian_kw_t2   +=  $total_pemakaian_kw_t2;
            $gt_total_pemakaian_kw   +=  $total_pemakaian_kw;
            $gt_rasio_kw_olah   +=  round($rasio_kw_olah,5);
            $gt_total   +=  $total;

            }

            echo "<tr class=rowcontent >
                       <td colspan=2>TOTAL</td>
                <td>".$gt_tbs_olah."</td>
                <td></td>
                <td></td>                
                <td>".$gt_jumlah_hm_g1."</td>
                <td></td>
                <td ></td>
                <td>".$gt_jumlah_hm_g2."</td>
                <td></td>
                <td></td>
                <td>".$gt_jumlah_hm_g3."</td>
                <td>".$gt_total_hm."</td>
                <td>".$gt_gg_hm."</td>
                <td>".$gt_hm."</td>
                <td>".$gt_total_paralel."</td>
                <td>".$gt_kwh_g1."</td>
                <td>".$gt_kwh_g2."</td>
                <td>".$gt_kwh_g3."</td>
                <td>".$gt_total_kwh."</td>
                 <td>".$gt_kw1."</td>
                <td>".$gt_kw2."</td>
                <td>".$gt_kw3."</td>
                <td>".$gt_total_kw."</td>
                <td>".$gt_bbm_g1."</td>
                <td>".$gt_bbm_g2."</td>
                <td>".$gt_bbm_g3."</td>
                <td>".$gt_total_bbm."</td>
                <td>".$gt_rasio_bbm1."</td>
                <td>".$rasio_bbm2."</td>
                <td>".$gt_rasio_bbm3."</td>



                <td>".$gt_hm_bolah_t1."</td>
                <td>".$gt_hm_bolah_t2."</td>
                <td>".$gt_total_bolah_hm."</td>
                <td>".$gt_hm_kolah_t1."</td>
                <td >".$gt_hm_kolah_t2."</td>
                <td>".$gt_total_kolah_hm."</td>
                <td>".$gt_hm_total_t1."</td>
                <td >".$gt_hm_total_t2."</td>
                <td>".$gt_total_hmt."</td>
                <td>".$gt_kwh_bolah_t1."</td>
                <td>".$gt_kwh_bolah_t2."</td>
                <td>".$gt_total_bolah_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1b."</td>
                <td>".$gt_total_pemakaian_kw_t2b."</td>
                <td>".$gt_total_pemakaian_kwb."</td>
                <td>".$gt_kwh_kolah_t1."</td>
                <td>".$gt_kwh_kolah_t2."</td>
                <td>".$gt_total_kolah_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1k."</td>
                <td>".$gt_total_pemakaian_kw_t2k."</td>
                <td>".$gt_total_pemakaian_kwk."</td>
                <td>".$gt_total_pemakaian_kwh_t1."</td>
                <td>".$gt_total_pemakaian_kwh_t2."</td>
                <td>".$gt_total_pemakaian_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1."</td>
                <td>".$gt_total_pemakaian_kw_t2."</td>
                <td>".$gt_total_pemakaian_kw."</td>
                <td>".$gt_rasio_kw_olah."</td>
                <td>".$gt_total_pemakaian_kwk."</td>
                <td>".$gt_total_pemakaian_kwb."</td>
                <td>".$gt_total."</td>
                <td></td></tr>";

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

         $sPower1 = "SELECT * FROM pabrik_power1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=63 align=center>PEMAKAIAN ENERGI LISTRIK</td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_power1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                  <tr>
                <td colspan=31 bgcolor=#DEDEDE align=center>Genset</td>
                <td colspan=32 bgcolor=#DEDEDE align=center>Turbin</td>
                </tr>
                 <tr bgcolor=#DEDEDE>
                 <td>No</td>
                <td>Tanggal</td>
                <td>TBS OLAH (Kg)</td>
                <td>HM AWAL G1</td>
                <td>HM AKHIR G1</td>
                <td>JUMLAH HM G1</td>
                <td>HM AWAL G2</td>
                <td>HM AKHIR G2</td>
                <td>JUMLAH HM G2</td>
                <td>HM AWAL G3</td>
                <td>HM AKHIR G3</td>
                <td>JUMLAH HM G3</td>
                <td>TOTAL</td>
                <td>G//G (HM)</td>
                <td>G//T (HM)</td>
                <td>TOTAL PARALEL</td>
                <td>KWH G1</td>
                <td>KWH G2</td>
                <td>KWH G3</td>
                <td>TOTAL KWH</td>
                <td>KW G1</td>
                <td>KW G2</td>
                <td>KW G3</td>
                <td>TOTAL KW</td>
                <td>BBM G1</td>
                <td>BBM G2</td>
                <td>BBM G3</td>
                <td>TOTAL BBM</td>
                <td>RASIO BBM G1</td>
                <td>RASIO BBM G2</td>
                <td>RASIO BBM G3</td>

                <td>JAM KERJA SEBELUM OLAH T1 (hm)</td>
                <td>JAM KERJA SEBELUM OLAH T2 (hm)</td>
                <td>TOTAL JAM KERJA SEBELUM OLAH</td>
                <td>JAM KERJA KETIKA OLAH T1 (hm)</td>
                <td>JAM KERJA KETIKA OLAH T2 (hm)</td>
                <td>TOTAL JAM KERJA KETIKA OLAH</td>
                <td>TOTAL JAM KERJA T1</td>
                <td>TOTAL JAM KERJA T2</td>
                <td>TOTAL JAM KERJA</td>
                <td>PEMAKAIAN KWH SEBELUM OLAH T1 </td>
                <td>PEMAKAIAN KWH SEBELUM OLAH T2</td>
                <td>TOTAL PEMAKAIAN KWH SEBELUM OLAH</td>
                <td>PEMAKAIAN KW SEBELUM OLAH T1</td>
                <td>PEMAKAIAN KW SEBELUM OLAH T2</td>
                <td>TOTAL PEMAKAIAN KW SEBELUM OLAH</td>
                <td>PEMAKAIAN KWH KETIKA OLAH T1 </td>
                <td>PEMAKAIAN KWH KETIKA OLAH T2</td>
                <td>TOTAL PEMAKAIAN KWH SEBELUM OLAH</td>
                <td>PEMAKAIAN KW KETIKA OLAH T1</td>
                <td>PEMAKAIAN KW KETIKA OLAH T2</td>
                <td>TOTAL PEMAKAIAN KW KETIKA OLAH</td>
                <td>TOTAL PEMAKAIAN KWH T1</td>
                <td>TOTAL PEMAKAIAN KWH T2</td>
                <td>TOTAL KWH </td>
                <td>TOTAL PEMAKAIAN KW T1</td>
                <td>TOTAL PEMAKAIAN KW T2</td>
                <td>TOTAL KW </td>
                <td>RASIO KW OLAH </td>
                <td>KW CONSUMPTION PROSES</td>
                <td>KW CONSUMPTION DOMESTIK </td>
                <td>KW CONSUMPTION TOTAL</td>
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
            $hm_awal_g1= $res['hm_awal_g1'];
            $hm_akhir_g1 = $res['hm_akhir_g1'];
            $jumlah_hm_g1 = ($hm_akhir_g1) - ($hm_awal_g1);
            $hm_awal_g2 = $res['hm_awal_g2'];
            $hm_akhir_g2 = $res['hm_akhir_g2'];
             $jumlah_hm_g2 = ($hm_akhir_g2) - ($hm_awal_g2);
            $hm_awal_g3 = $res['hm_awal_g3'];
            $hm_akhir_g3 = $res['hm_akhir_g3'];
            $jumlah_hm_g3 = ($hm_akhir_g3) - ($hm_awal_g3);
            $total_hm = $jumlah_hm_g1+$jumlah_hm_g2+$jumlah_hm_g3;
            $gg_hm = $res['gg_hm'];;
            $gt_hm = $res['gt_hm'];
            $total_paralel = $gg_hm + $gt_hm;
            $kwh_g1 = $res['kwh_g1'];
            $kwh_g2 = $res['kwh_g2'];
            $kwh_g3 = $res['kwh_g3'];
            $total_kwh = $kwh_g1 + $kwh_g2 + $kwh_g3;

             $skw1 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG1' ";
             $skw2 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG2' ";
             $skw3 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWG3' ";

            $qkw1 = mysql_query($skw1);
            $qkw2 = mysql_query($skw2);
            $qkw3 = mysql_query($skw3);
        
         while ($reskw1 = mysql_fetch_assoc($qkw1)) {
            $nilai_kw1= $reskw1['nilai'];
        }
        while ($reskw2 = mysql_fetch_assoc($qkw2)) {
            $nilai_kw2= $reskw2['nilai'];
        }
        while ($reskw3 = mysql_fetch_assoc($qkw3)) {
            $nilai_kw3= $reskw3['nilai'];
        }

            $kw1 = $kwh_g1 * $nilai_kw1;
            $kw2 = $kwh_g2 * $nilai_kw2;
            $kw3 = $kwh_g3 * $nilai_kw3;
            $total_kw = $kw1 + $kw2 +$kw3;
            $bbm_g1 = $res['bbm_g1'];
            $bbm_g2 = $res['bbm_g2'];
            $bbm_g3 = $res['bbm_g3'];

            $total_bbm = $bbm_g1 + $bbm_g2 + $bbm_g3;
            $rasio_bbm1 = $bbm_g1 / $kw1;
            $rasio_bbm2 =  $bbm_g2 / $kw2;
            $rasio_bbm3 = $bbm_g3 / $kw3;
            $sPower2 = "SELECT * FROM pabrik_power2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) = '".$tgl."' ";

            $qData2 = mysql_query($sPower2);
        while ($res2 = mysql_fetch_assoc($qData2)) {

             $skwt1 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWT1' ";
             $skwt2 = "SELECT * FROM pabrik_5general WHERE kodeorg LIKE '%".$kdPabrik."%' and code = 'KWT2' ";

            $qkwt1 = mysql_query($skwt1);
            $qkwt2 = mysql_query($skwt2);
        
         while ($reskwt1 = mysql_fetch_assoc($qkwt1)) {
            $nilai_kwt1= $reskwt1['nilai'];
        }
        while ($reskwt2 = mysql_fetch_assoc($qkwt2)) {
            $nilai_kwt2= $reskw2['nilai'];
        }


            $hm_bolah_t1= $res2['hm_bolah_t1'];
            $hm_bolah_t2 = $res2['hm_bolah_t2'];
            $total_bolah_hm = $hm_bolah_t1 + $hm_bolah_t2;
            $hm_kolah_t1 = $res2['hm_kolah_t1'];
            $hm_kolah_t2 = $res2['hm_kolah_t2'];
            $total_kolah_hm = $hm_kolah_t1 + $hm_kolah_t2;
            $hm_total_t1 = $hm_bolah_t1 + $hm_kolah_t1;
            $hm_total_t2 = $hm_bolah_t2 + $hm_kolah_t2;
            $total_hmt = $hm_total_t1 + $hm_total_t2;
            $kwh_bolah_t1 = $res2['kwh_bolah_t1'];
            $kwh_bolah_t2 = $res2['kwh_bolah_t2'];
            $total_bolah_kwh = $kwh_bolah_t1 + $kwh_bolah_t2;
            $total_pemakaian_kw_t1b =  $res2['kwh_bolah_t1'] * ($nilai_kwt1);
            $total_pemakaian_kw_t2b =  $res2['kwh_bolah_t2'] * ($nilai_kwt2);
            $total_pemakaian_kwb = $total_pemakaian_kw_t1b + $total_pemakaian_kw_t2b;
            $kwh_kolah_t1 = $res2['kwh_kolah_t1'];
            $kwh_kolah_t2 = $res2['kwh_kolah_t2'];
            $total_kolah_kwh = $kwh_kolah_t1 + $kwh_kolah_t2;
            $total_pemakaian_kw_t1k =  $res2['kwh_kolah_t1'] * ($nilai_kwt1);
            $total_pemakaian_kw_t2k =  $res2['kwh_kolah_t2'] * ($nilai_kwt2);
            $total_pemakaian_kwk = $total_pemakaian_kw_t1k + $total_pemakaian_kw_t2k;
            $total_pemakaian_kwh_t1 = $kwh_bolah_t1 + $kwh_kolah_t1;
            $total_pemakaian_kwh_t2 = $kwh_bolah_t2 + $kwh_kolah_t2;
            $total_pemakaian_kwh = $total_pemakaian_kwh_t1 + $total_pemakaian_kwh_t2;
            $total_pemakaian_kw_t1 = $total_pemakaian_kw_t1k + $total_pemakaian_kw_t1b;
            $total_pemakaian_kw_t2 = $total_pemakaian_kw_t2k + $total_pemakaian_kw_t2b;
            $total_pemakaian_kw = $total_pemakaian_kw_t1 + $total_pemakaian_kw_t2;

            $keterangan = $res2['keterangan'];
        }
        $spabrik = "SELECT * FROM pabrik_produksi WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tanggal) = '".$tgl."' ";

            $qDatap = mysql_query($spabrik);
            $brsp = mysql_num_rows($qDatap);
        if ($brsp >0){
         while ($resp = mysql_fetch_assoc($qDatap)) {
            $tbs_olah= $resp['tbs_diolah'];
        }
        }else{
            $tbs_olah= 0;
        }
       
            $rasio_kw_olah = $total_pemakaian_kwk/($tbs_olah);
            $total = $total_pemakaian_kwk + $total_pemakaian_kwb;

                $tab .= "\r\n                       <tr class=rowcontent><td>".$no."</td>
                       <td>".$tgl."</td>
                       <td>".$tbs_olah."</td>
                <td>".$hm_awal_g1."</td>
                <td>".$hm_akhir_g1."</td>                
                <td>".round($jumlah_hm_g1,2)."</td>
                <td>".$hm_awal_g2."</td>
                <td >".$hm_akhir_g2."</td>
                <td>".round($jumlah_hm_g2,2)."</td>
                <td>".$hm_awal_g3."</td>
                <td>".$hm_akhir_g3."</td>
                <td>".round($jumlah_hm_g3,2)."</td>
                <td>".$total_hm."</td>
                <td>".$gg_hm."</td>
                <td>".$gt_hm."</td>
                <td>".$total_paralel."</td>
                <td>".$kwh_g1."</td>
                <td>".$kwh_g2."</td>
                <td>".$kwh_g3."</td>
                <td>".$total_kwh."</td>
                 <td>".$kw1."</td>
                <td>".$kw2."</td>
                <td>".$kw3."</td>
                <td>".$total_kw."</td>
                <td>".$bbm_g1."</td>
                <td>".$bbm_g2."</td>
                <td>".$bbm_g3."</td>
                <td>".$total_bbm."</td>
                <td>".round($rasio_bbm1,2)."</td>
                <td>".round($rasio_bbm2,2)."</td>
                <td>".round($rasio_bbm3,2)."</td>



                <td>".$hm_bolah_t1."</td>
                <td>".$hm_bolah_t2."</td>
                <td>".$total_bolah_hm."</td>
                <td>".$hm_kolah_t1."</td>
                <td >".$hm_kolah_t2."</td>
                <td>".$total_kolah_hm."</td>
                <td>".$hm_total_t1."</td>
                <td >".$hm_total_t2."</td>
                <td>".$total_hmt."</td>
                <td>".$kwh_bolah_t1."</td>
                <td>".$kwh_bolah_t2."</td>
                <td>".$total_bolah_kwh."</td>
                <td>".$total_pemakaian_kw_t1b."</td>
                <td>".$total_pemakaian_kw_t2b."</td>
                <td>".$total_pemakaian_kwb."</td>
                <td>".$kwh_kolah_t1."</td>
                <td>".$kwh_kolah_t2."</td>
                <td>".$total_kolah_kwh."</td>
                <td>".$total_pemakaian_kw_t1k."</td>
                <td>".$total_pemakaian_kw_t2k."</td>
                <td>".$total_pemakaian_kwk."</td>
                <td>".$total_pemakaian_kwh_t1."</td>
                <td>".$total_pemakaian_kwh_t2."</td>
                <td>".$total_pemakaian_kwh."</td>
                <td>".$total_pemakaian_kw_t1."</td>
                <td>".$total_pemakaian_kw_t2."</td>
                <td>".$total_pemakaian_kw."</td>
                <td>".round($rasio_kw_olah,5)."</td>
                <td>".$total_pemakaian_kwk."</td>
                <td>".$total_pemakaian_kwb."</td>
                <td>".$total."</td>
                <td>".$keterangan."</td>
                    </tr>";

                $gt_tbs_olah   += $tbs_olah;
            $gt_hm_awal_g1  += $hm_awal_g1;
            $gt_hm_akhir_g1 += $hm_akhir_g1;              
            $gt_jumlah_hm_g1 += round($jumlah_hm_g1,2);
            $gt_hm_awal_g2   += $hm_awal_g2;
            $gt_hm_akhir_g2   += $hm_akhir_g2;
            $gt_jumlah_hm_g2   +=  round($jumlah_hm_g2,2);
            $gt_hm_awal_g3    += $hm_awal_g3;
            $gt_hm_akhir_g3   +=  $hm_akhir_g3;
            $gt_jumlah_hm_g3   +=  round($jumlah_hm_g3,2);
            $gt_total_hm   +=  $total_hm;
            $gt_gg_hm   +=  $gg_hm;
            $gt_gt_hm   +=  $gt_hm;
            $gt_total_paralel  +=  $total_paralel;
            $gt_kwh_g1   +=  $kwh_g1;
            $gt_kwh_g2   +=  $kwh_g2;
            $gt_kwh_g3   +=  $kwh_g3;
            $gt_total_kwh   +=  $total_kwh;
            $gt_kw1   +=  $kw1;
            $gt_kw2   +=  $kw2;
            $gt_kw3   +=  $kw3;
            $gt_total_kw   +=  $total_kw;
            $gt_bbm_g1   +=  $bbm_g1;
            $gt_bbm_g2   +=  $bbm_g2;
            $gt_bbm_g3   +=  $bbm_g3;
            $gt_total_bbm   +=  $total_bbm;
            $gt_rasio_bbm1   +=  round($rasio_bbm1,2);
            $gt_rasio_bbm2   +=  round($rasio_bbm2,2);
            $gt_rasio_bbm3   +=  round($rasio_bbm3,2);



            $gt_hm_bolah_t1   +=  $hm_bolah_t1;
            $gt_hm_bolah_t2   +=  $hm_bolah_t2;
            $gt_total_bolah_hm   +=  $total_bolah_hm;
            $gt_hm_kolah_t1   +=  $hm_kolah_t1;
             $gt_hm_kolah_t2  +=  $hm_kolah_t2;
            $gt_total_kolah_hm   +=  $total_kolah_hm;
            $gt_hm_total_t1   +=  $hm_total_t1;
            $gt_hm_total_t2   +=  $hm_total_t2;
            $gt_total_hmt   +=  $total_hmt;
            $gt_kwh_bolah_t1   +=  $kwh_bolah_t1;
            $gt_kwh_bolah_t2   +=  $kwh_bolah_t2;
            $gt_total_bolah_kwh   +=  $total_bolah_kwh;
            $gt_total_pemakaian_kw_t1b   +=  $total_pemakaian_kw_t1b;
            $gt_total_pemakaian_kw_t2b   +=  $total_pemakaian_kw_t2b;
            $gt_total_pemakaian_kwb   +=  $total_pemakaian_kwb;
            $gt_kwh_kolah_t1   +=  $kwh_kolah_t1;
            $gt_kwh_kolah_t2   +=  $kwh_kolah_t2;
            $gt_total_kolah_kwh   +=  $total_kolah_kwh;
            $gt_total_pemakaian_kw_t1k   +=  $total_pemakaian_kw_t1k;
            $gt_total_pemakaian_kw_t2k   +=  $total_pemakaian_kw_t2k;
            $gt_total_pemakaian_kwk   +=  $total_pemakaian_kwk;
            $gt_total_pemakaian_kwh_t1   +=  $total_pemakaian_kwh_t1;
            $gt_total_pemakaian_kwh_t2   +=  $total_pemakaian_kwh_t2;
            $gt_total_pemakaian_kwh   +=  $total_pemakaian_kwh;
            $gt_total_pemakaian_kw_t1   +=  $total_pemakaian_kw_t1;
            $gt_total_pemakaian_kw_t2   +=  $total_pemakaian_kw_t2;
            $gt_total_pemakaian_kw   +=  $total_pemakaian_kw;
            $gt_rasio_kw_olah   +=  round($rasio_kw_olah,5);
            $gt_total   +=  $total;

            }

            $tab .= "<tr class=rowcontent >
                       <td colspan=2>TOTAL</td>
                <td>".$gt_tbs_olah."</td>
                <td></td>
                <td></td>                
                <td>".$gt_jumlah_hm_g1."</td>
                <td></td>
                <td ></td>
                <td>".$gt_jumlah_hm_g2."</td>
                <td></td>
                <td></td>
                <td>".$gt_jumlah_hm_g3."</td>
                <td>".$gt_total_hm."</td>
                <td>".$gt_gg_hm."</td>
                <td>".$gt_hm."</td>
                <td>".$gt_total_paralel."</td>
                <td>".$gt_kwh_g1."</td>
                <td>".$gt_kwh_g2."</td>
                <td>".$gt_kwh_g3."</td>
                <td>".$gt_total_kwh."</td>
                 <td>".$gt_kw1."</td>
                <td>".$gt_kw2."</td>
                <td>".$gt_kw3."</td>
                <td>".$gt_total_kw."</td>
                <td>".$gt_bbm_g1."</td>
                <td>".$gt_bbm_g2."</td>
                <td>".$gt_bbm_g3."</td>
                <td>".$gt_total_bbm."</td>
                <td>".$gt_rasio_bbm1."</td>
                <td>".$rasio_bbm2."</td>
                <td>".$gt_rasio_bbm3."</td>



                <td>".$gt_hm_bolah_t1."</td>
                <td>".$gt_hm_bolah_t2."</td>
                <td>".$gt_total_bolah_hm."</td>
                <td>".$gt_hm_kolah_t1."</td>
                <td >".$gt_hm_kolah_t2."</td>
                <td>".$gt_total_kolah_hm."</td>
                <td>".$gt_hm_total_t1."</td>
                <td >".$gt_hm_total_t2."</td>
                <td>".$gt_total_hmt."</td>
                <td>".$gt_kwh_bolah_t1."</td>
                <td>".$gt_kwh_bolah_t2."</td>
                <td>".$gt_total_bolah_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1b."</td>
                <td>".$gt_total_pemakaian_kw_t2b."</td>
                <td>".$gt_total_pemakaian_kwb."</td>
                <td>".$gt_kwh_kolah_t1."</td>
                <td>".$gt_kwh_kolah_t2."</td>
                <td>".$gt_total_kolah_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1k."</td>
                <td>".$gt_total_pemakaian_kw_t2k."</td>
                <td>".$gt_total_pemakaian_kwk."</td>
                <td>".$gt_total_pemakaian_kwh_t1."</td>
                <td>".$gt_total_pemakaian_kwh_t2."</td>
                <td>".$gt_total_pemakaian_kwh."</td>
                <td>".$gt_total_pemakaian_kw_t1."</td>
                <td>".$gt_total_pemakaian_kw_t2."</td>
                <td>".$gt_total_pemakaian_kw."</td>
                <td>".$gt_rasio_kw_olah."</td>
                <td>".$gt_total_pemakaian_kwk."</td>
                <td>".$gt_total_pemakaian_kwb."</td>
                <td>".$gt_total."</td>
                <td></td></tr>";

        } else {

            $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';

        }



        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];

        $tglSkrg = date('Ymd');

        $nop_ = 'LaporanPower'.$tglSkrg;

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