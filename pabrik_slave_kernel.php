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
$total_lori = 0;
 $total_hm_ripple1 = 0;
 $total_hm_ripple2 = 0;
 $pemakaian_ca = 0;
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM power_kernel1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
       
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
               <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>NAMA SHIFT</td>
                <td>Ripple Mill 1 start</td>
                <td>Ripple Mill 1 stop</td>
                <td>HM Ripple Mill 1</td>
                <td>Ripple Mill 2 start</td>
                <td>Ripple Mill 2 stop</td>
                <td>HM Ripple Mill 2</td>
                <td>Nut Silo 1  </td>
                <td>Nut Silo 2(A)</td>
                <td>Kernel Silo 1</td>
                <td>Kernel Silo 2</td>
                <td>Kernel Silo 3</td>
                <td>Pemakaian CaCO3</td>
                <td>Keterangan Alat</td>
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
                 echo $sKaryawan;
                ++$no;
             $tgl= $res['tgl'];
            $shift_nik= $res['shift_nik'];
            $ripple1_start = $res['ripple1_start'];
            $ripple1_stop = $res['ripple1_stop'];
            $ripple2_start = $res['ripple2_start'];
            $ripple2_stop = $res['ripple2_stop'];
            $nut_silo1 = $res['nut_silo1'];;
            $nut_silo2 = $res['nut_silo2'];
            $kernel_silo1 = $res['kernel_silo1'];
            $kernel_silo2 = $res['kernel_silo2'];
            $kernel_silo3 = $res['kernel_silo3'];
            $pemakaian_ca = $res['pemakaian_ca'];
            $keterangan = $res['keterangan'];
            $hm_ripple1 = $res['ripple1_stop'] - $res['ripple1_start'];
            $hm_ripple2 = $res['ripple2_stop'] - $res['ripple2_start'];
            $total_hm_ripple1 += $hm_ripple1;
            $total_hm_ripple2 += $hm_ripple2;
            $total_pemakaian_ca += $pemakaian_ca;

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
               <td>'.$ripple1_start.'</td>
                <td>'.$ripple1_stop.'</td>
                <td>'.$hm_ripple1.'</td>
                <td >'.$ripple2_start.'</td>
                <td>'.$ripple2_stop.'</td>
                <td>'.$hm_ripple2.'</td>
                <td>'.$nut_silo1.'</td>
                <td>'.$nut_silo2.'</td>
                <td>'.$kernel_silo1.'</td>
                <td>'.$kernel_silo2.'</td>
                <td>'.$kernel_silo3.'</td>
                <td>'.$pemakaian_ca.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

                   
            }
              
                
                    
                echo '                      <tr class=rowcontent>
               
                  <td colspan=5>Total</td>
                <td>'.$total_hm_ripple1.'</td>
                <td ></td>
                <td></td>
                <td>'.$total_hm_ripple2.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_pemakaian_ca.'</td>
                <td></td>
                    </tr> </table>';
               
           $sPower2 = "SELECT * FROM power_kernel2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                <tr>
              
               <td colspan=5>Aplikasi</td>
                </tr>
                  <tr>
              
               <td>Tanggal</td>
                <td>JAM</td>
                <td>Aplikasi Air (Ltr)</td>
                <td>Aplikasi CaCO3 (Kg)</td>
                <td>Aplikasi Abu JJK (Kg)</td>
                </tr>
                </thead>
            <tbody>";

        $qData2 = mysql_query($sPower2);
        $brs2 = mysql_num_rows($qData2);
        // echo $sPower1;
        

            while ($res2 = mysql_fetch_assoc($qData2)) {

                ++$no;
            $tgl= $res2['tgl'];
            $jam= $res2['jam'];
            $air = $res2['air'];
            $caco3 = $res2['caco3'];
            $abu = $res2['abu'];

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$air.'</td>
                <td>'.$caco3.'</td>
                <td >'.$abu.'</td>
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

         $sPower1 = "SELECT * FROM power_kernel1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL KERNEL STATION</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM power_kernel1 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
              <td>Tanggal</td>
                <td>SHIFT (NIK)</td>
                <td>NAMA SHIFT</td>
                <td>Ripple Mill 1 start</td>
                <td>Ripple Mill 1 stop</td>
                <td>HM Ripple Mill 1</td>
                <td>Ripple Mill 2 start</td>
                <td>Ripple Mill 2 stop</td>
                <td>HM Ripple Mill 2</td>
                <td>Nut Silo 1  </td>
                <td>Nut Silo 2(A)</td>
                <td>Kernel Silo 1</td>
                <td>Kernel Silo 2</td>
                <td>Kernel Silo 3</td>
                <td>Pemakaian CaCO3</td>
                <td>Keterangan Alat</td>
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
            $ripple1_start = $res['ripple1_start'];
            $ripple1_stop = $res['ripple1_stop'];
            $ripple2_start = $res['ripple2_start'];
            $ripple2_stop = $res['ripple2_stop'];
            $nut_silo1 = $res['nut_silo1'];;
            $nut_silo2 = $res['nut_silo2'];
            $kernel_silo1 = $res['kernel_silo1'];
            $kernel_silo2 = $res['kernel_silo2'];
            $kernel_silo3 = $res['kernel_silo3'];
            $pemakaian_ca = $res['pemakaian_ca'];
            $keterangan = $res['keterangan'];
            $hm_ripple1 = $res['ripple1_stop'] - $res['ripple1_start'];
            $hm_ripple2 = $res['ripple2_stop'] - $res['ripple2_start'];
            $total_hm_ripple1 += $hm_ripple1;
            $total_hm_ripple2 += $hm_ripple2;
                $tab .= '                      <tr class=rowcontent>
               
                <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
               <td>'.$ripple1_start.'</td>
                <td>'.$ripple1_stop.'</td>
                <td>'.$hm_ripple1.'</td>
                <td >'.$ripple2_start.'</td>
                <td>'.$ripple2_stop.'</td>
                <td>'.$hm_ripple2.'</td>
                <td>'.$nut_silo1.'</td>
                <td>'.$nut_silo2.'</td>
                <td>'.$kernel_silo1.'</td>
                <td>'.$kernel_silo2.'</td>
                <td>'.$kernel_silo3.'</td>
                <td>'.$pemakaian_ca.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

                    $total_lori  += $jml_lorituang;
               
            }
              $tab .= '                      <tr class=rowcontent>
               
                  <td colspan=5>Total</td>
                <td>'.$total_hm_ripple1.'</td>
                <td ></td>
                <td></td>
                <td>'.$total_hm_ripple2.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_pemakaian_ca.'</td>
                <td></td>
                    </tr> </table>';
             

             $sPower2 = "SELECT * FROM power_kernel2 WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
       
        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                <tr bgcolor=#DEDEDE>
              
               <td colspan=5>Aplikasi</td>
                </tr>
                  <tr bgcolor=#DEDEDE>
              
               <td>Tanggal</td>
                <td>JAM</td>
                <td>Aplikasi Air (Ltr)</td>
                <td>Aplikasi CaCO3 (Kg)</td>
                <td>Aplikasi Abu JJK (Kg)</td>
                </tr>
                </thead>
            <tbody>";

        $qData2 = mysql_query($sPower2);
        $brs2 = mysql_num_rows($qData2);
        // echo $sPower1;
        

            while ($res2 = mysql_fetch_assoc($qData2)) {

                ++$no;
            $tgl= $res2['tgl'];
            $jam= $res2['jam'];
            $air = $res2['air'];
            $caco3 = $res2['caco3'];
            $abu = $res2['abu'];

                $tab .= '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$air.'</td>
                <td>'.$caco3.'</td>
                <td >'.$abu.'</td>
                    </tr>  ';

                   
            }
             $tab .= '   </table><table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
                </tr>
                <tr></tr>
                 <tr></table>';
             $tab .= '   <table cellspacing=1 border=1 class=sortable>                   
                
                
                <tr>
                <tr></tr>
                <tr></tr>
                <td> Jabatan</td>
                <td> Nama</td>
                <td> Shift 1</td>
                <td> Shift II</td>
                <td> Paraf</td>
               
                 </tr><tr>
                <td> Aisten</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                </tr>
                <tr>
                 
                <td> Mandor</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
               
                 </tr><tr>
                <td> Operator</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                
                 </tr><tr>
                <td> Helper</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> Helper</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> JMLH</td>
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

        $nop_ = 'LaporanKernelnew'.$tglSkrg;
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