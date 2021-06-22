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
switch ($proses) {

    case 'preview':

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning:Date required';
            exit();
        }


   

        $sPower1 = "SELECT * FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
               <td>Tanggal</td>
                <td>Shift</td>
                <td>Nama</td>
                <td>Jumlah Lori Tuang</td>
                <td>Start Jam Tuang</td>
                <td>Stop Jam Tuang</td>
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
            $tgl= $res['tgl'];
            $shift_nik= $res['shift_nik'];
            $jml_lorituang = $res['jml_lorituang'];
            $start_jt = $res['start_jt'];
            $stop_jt = $res['stop_jt'];
            $keterangan = $res['keterangan'];

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$jml_lorituang.'</td>
                <td>'.$start_jt.'</td>
                <td>'.$stop_jt.'</td>
                <td >'.$keterangan.'</td>
                    </tr>';

                   
            }
               $sLorinik = "SELECT sum(jml_lorituang) as jml_lori, shift_nik FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' group by shift_nik ";
                 $qLori = mysql_query($sLorinik);
                 while ($resL = mysql_fetch_assoc($qLori)) {
                    $sKaryawanL = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$resL['shift_nik']."'  ";
                 $qKL = mysql_query($sKaryawanL);
                 while ($resKL = mysql_fetch_assoc($qKL)) {
                    $namakaryawan = $resKL['namakaryawan'];
                    }
                
                    
                echo '                      <tr class=rowcontent>
               
                <td>'.$namakaryawan.'</td>
                <td colspan=5>'.$resL['jml_lori'].'</td>
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

         $sPower1 = "SELECT * FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL HARIAN TIPPLER</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
               <td>Tanggal</td>
                <td>Shift</td>
                <td>Nama</td>
                <td>Jumlah Lori Tuang</td>
                <td>Start Jam Tuang</td>
                <td>Stop Jam Tuang</td>
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
            $tgl= $res['tgl'];
            $shift_nik= $res['shift_nik'];
            $jml_lorituang = $res['jml_lorituang'];
            $start_jt = $res['start_jt'];
            $stop_jt = $res['stop_jt'];
            $keterangan = $res['keterangan'];

                $tab .= '                      <tr class=rowcontent>
               
                <td>'.$tgl.'</td>
                <td>'.$shift_nik.'</td>
                <td>'.$namakaryawan.'</td>
                <td>'.$jml_lorituang.'</td>
                <td>'.$start_jt.'</td>
                <td>'.$stop_jt.'</td>
                <td >'.$keterangan.'</td>
                    </tr>';

                    $total_lori  += $jml_lorituang;
               
            }
             $tab .= '                      <tr class=rowcontent>
               
                <td>Total Lori</td>
                <td colspan=5>'.$total_lori.'</td>
                    </tr>';
             

            $sLorinik = "SELECT sum(jml_lorituang) as jml_lori, shift_nik FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' group by shift_nik ";
                 $qLori = mysql_query($sLorinik);
                 echo $sLorinik;
                 while ($resL = mysql_fetch_assoc($qLori)) {
                    $sKaryawanL = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$resL['shift_nik']."'  ";
                 $qKL = mysql_query($sKaryawanL);
                 while ($resKL = mysql_fetch_assoc($qKL)) {
                    $namakaryawan = $resKL['namakaryawan'];
                    }
                
                    
                $tab .= '                      <tr class=rowcontent>
               
                <td>'.$namakaryawan.'</td>
                <td colspan=5>'.$resL['jml_lori'].'</td>
                    </tr>';
                }
            

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
                <td>Operator Shift 1</td>
                <td></td>
                <td>Operator Shift 2</td>
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

        $nop_ = 'LaporanTripplern'.$tglSkrg;

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