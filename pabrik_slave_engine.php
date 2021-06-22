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


   

        $sPower1 = "SELECT * FROM pabrik_engine WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
               <td>Tanggal</td>
                <td>JAM START</td>
                <td>JAM STOP</td>
                <td>LOAD LIMIT</td>
                <td>INLET STEAM (STEAM PRESSURE)</td>
                <td>NOZZLE STEAM (STEAM PRESSURE)</td>
                <td>EXHAUST STEAM (STEAM PRESSURE)</td>
                <td>BEARING TEMPERATURE PINION GEAR 1</td>
                <td>BEARING TEMPERATURE PINION GEAR 2</td>
                <td>BEARING TEMPERATURE BULL GEAR 1</td>
                <td>BEARING TEMPERATURE BULL GEAR 2</td>
                <td>INLET (TEMPERATURE OIL)</td>
                <td>OUTLET (TEMPERATURE OIL)</td>
                <td>TEKANAN OIL</td>
                <td>AMPERE R</td>
                <td>AMPERE S</td>
                <td>AMPERE T</td>
                <td>RPM SPEED</td>
                <td>HZ</td>
                <td>COS</td>
                <td>VOLT</td>
                <td>KW</td>
                <td>HOUR METER</td>
                <td>KWH AWAL</td>
                <td>KWH AKHIR</td>
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
            $jam_start= $res['jam_start'];
            $jam_stop= $res['jam_stop'];
            $load_limit = $res['load_limit'];
            $inleat_steam = $res['inleat_steam'];
            $nozzle_steam = $res['nozzle_steam'];
            $exhaust_steam = $res['exhaust_steam'];
            $btp_gear1 = $res['btp_gear1'];;
            $btp_gear2 = $res['btp_gear2'];
            $btb_gear1 = $res['btb_gear1'];
            $btb_gear2 = $res['btb_gear2'];
            $inleat = $res['inleat'];

            $outlet= $res['outlet'];
            $tekanan_oil = $res['tekanan_oil'];
            $ampere_r = $res['ampere_r'];
            $ampere_s = $res['ampere_s'];
            $ampere_t = $res['ampere_t'];
            $rpm_speed = $res['rpm_speed'];;
            $hz = $res['hz'];
            $cos = $res['cos'];
            $volt = $res['volt'];
            $kw = $res['kw'];

            $hm= $res['hm'];
            $kwh_awal = $res['kwh_awal'];
            $kwh_akhir = $res['kwh_akhir'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                echo '<tr class=rowcontent>
                       <td>'.$tgl.'</td>
                <td>'.$jam_start.'</td>
                <td>'.$jam_stop.'</td>
                <td>'.$load_limit.'</td>
                <td >'.$inleat_steam.'</td>
                <td>'.$nozzle_steam.'</td>
                <td>'.$exhaust_steam.'</td>
                <td>'.$btp_gear1.'</td>
                <td>'.$btp_gear2.'</td>
                <td>'.$btb_gear1.'</td>
                <td>'.$btb_gear2.'</td>
                <td>'.$inleat.'</td>
                
                <td>'.$outlet.'</td>
                <td>'.$tekanan_oil.'</td>
                <td >'.$ampere_r.'</td>
                <td>'.$ampere_s.'</td>
                <td>'.$ampere_t.'</td>
                <td>'.$rpm_speed.'</td>
                <td>'.$hz.'</td>
                <td>'.$cos.'</td>
                <td>'.$volt.'</td>
                <td>'.$kw.'</td>

                    <td>'.$hm.'</td>
                <td>'.$kwh_awal.'</td>
                <td >'.$kwh_akhir.'</td>
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

         $sPower1 = "SELECT * FROM pabrik_engine WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=30 align=center>LOGSHEET STASIUN ENGINE ROOM</td></tr>
        <tr><td colspan=30 align=center>TURBINE</td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_engine WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
                <td>Tanggal</td>
                <td>JAM START</td>
                <td>JAM STOP</td>
                <td>LOAD LIMIT</td>
                <td>INLET STEAM (STEAM PRESSURE)</td>
                <td>NOZZLE STEAM (STEAM PRESSURE)</td>
                <td>EXHAUST STEAM (STEAM PRESSURE)</td>
                <td>BEARING TEMPERATURE PINION GEAR 1</td>
                <td>BEARING TEMPERATURE PINION GEAR 2</td>
                <td>BEARING TEMPERATURE BULL GEAR 1</td>
                <td>BEARING TEMPERATURE BULL GEAR 2</td>
                <td>INLET (TEMPERATURE OIL)</td>
                <td>OUTLET (TEMPERATURE OIL)</td>
                <td>TEKANAN OIL</td>
                <td>AMPERE R</td>
                <td>AMPERE S</td>
                <td>AMPERE T</td>
                <td>RPM SPEED</td>
                <td>HZ</td>
                <td>COS</td>
                <td>VOLT</td>
                <td>KW</td>
                <td>HOUR METER</td>
                <td>KWH AWAL</td>
                <td>KWH AKHIR</td>
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
                ++$no;
               $tgl= $res['tgl'];
            $jam_start= $res['jam_start'];
            $jam_stop= $res['jam_stop'];
            $load_limit = $res['load_limit'];
            $inleat_steam = $res['inleat_steam'];
            $nozzle_steam = $res['nozzle_steam'];
            $exhaust_steam = $res['exhaust_steam'];
            $btp_gear1 = $res['btp_gear1'];;
            $btp_gear2 = $res['btp_gear2'];
            $btb_gear1 = $res['btb_gear1'];
            $btb_gear2 = $res['btb_gear2'];
            $inleat = $res['inleat'];

            $outlet= $res['outlet'];
            $tekanan_oil = $res['tekanan_oil'];
            $ampere_r = $res['ampere_r'];
            $ampere_s = $res['ampere_s'];
            $ampere_t = $res['ampere_t'];
            $rpm_speed = $res['rpm_speed'];;
            $hz = $res['hz'];
            $cos = $res['cos'];
            $volt = $res['volt'];
            $kw = $res['kw'];

            $hm= $res['hm'];
            $kwh_awal = $res['kwh_awal'];
            $kwh_akhir = $res['kwh_akhir'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                $tab .= '                      <tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam_start.'</td>
                <td>'.$jam_stop.'</td>
                <td>'.$load_limit.'</td>
                <td >'.$inleat_steam.'</td>
                <td>'.$nozzle_steam.'</td>
                <td>'.$exhaust_steam.'</td>
                <td>'.$btp_gear1.'</td>
                <td>'.$btp_gear2.'</td>
                <td>'.$btb_gear1.'</td>
                <td>'.$btb_gear2.'</td>
                <td>'.$inleat.'</td>
                
                <td>'.$outlet.'</td>
                <td>'.$tekanan_oil.'</td>
                <td >'.$ampere_r.'</td>
                <td>'.$ampere_s.'</td>
                <td>'.$ampere_t.'</td>
                <td>'.$rpm_speed.'</td>
                <td>'.$hz.'</td>
                <td>'.$cos.'</td>
                <td>'.$volt.'</td>
                <td>'.$kw.'</td>

                    <td>'.$hm.'</td>
                <td>'.$kwh_awal.'</td>
                <td >'.$kwh_akhir.'</td>
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
                <td>Diperiksa Oleh</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Dibuat Oleh</td>

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
                
                <td>Asst Proses Shift I</td>
                <td></td>
                <td></td>
                <td>Asst Proses Shift II</td>
                <td></td>
                <td></td>
                <td>Operator Shift I</td>
                <td></td>
                <td></td>
                <td>Operator Shift II</td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td></tr>';

        } else {

            $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';

        }



        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];

        $tglSkrg = date('Ymd');

        $nop_ = 'LaporanEngine'.$tglSkrg;

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