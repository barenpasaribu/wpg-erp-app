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


   

        $sPower1 = "SELECT * FROM pabrik_genset WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        
       
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>
               <td>Tanggal</td>
                <td>JAM</td>
                <td>G1 START</td>
                <td>G1 STOP</td>
                <td>COLT OIL G1</td>
                <td>PRESS OIL G1</td>
                <td>AMPERE U G1</td>
                <td>AMPERE V G1</td>
                <td>AMPERE W G1</td>
                <td>KW G1</td>
                <td>RUN HOURS KWH G1</td>
                <td>RUN HOURS ENGINE G1</td>
                <td>G2 START</td>
                <td>G2 STOP</td>
                <td>COLT OIL G2</td>
                <td>PRESS OIL G2/td>
                <td>AMPERE U G2</td>
                <td>AMPERE V G2</td>
                <td>AMPERE W G2</td>
                <td>KW G2</td>
                <td>RUN HOURS KWH G2</td>
                <td>RUN HOURS ENGINE G2</td>
                <td>G3 START</td>
                <td>G3 STOP</td>
                <td>COLT OIL G3</td>
                <td>PRESS OIL G3/td>
                <td>AMPERE U G3</td>
                <td>AMPERE V G3</td>
                <td>AMPERE W G3</td>
                <td>KW G3</td>
                <td>RUN HOURS KWH G3</td>
                <td>RUN HOURS ENGINE G3</td>
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
            $start_g1= $res['start_g1'];
            $stop_g1 = $res['stop_g1'];
            $coil_g1 = $res['coil_g1'];
            $poil_g1 = $res['poil_g1'];
            $ampereu_g1 = $res['ampereu_g1'];
            $amperev_g1 = $res['amperev_g1'];
            $amperew_g1 = $res['amperew_g1'];
            $kw_g1 = $res['kw_g1'];
            $runkwh_g1 = $res['runkwh_g1'];
            $runengine_g1 = $res['runengine_g1'];

            $start_g2= $res['start_g2'];
            $stop_g2 = $res['stop_g2'];
            $coil_g2 = $res['coil_g2'];
            $poil_g2 = $res['poil_g2'];
            $ampereu_g2 = $res['ampereu_g2'];
            $amperev_g2 = $res['amperev_g2'];;
            $amperew_g2 = $res['amperew_g2'];
            $kw_g2 = $res['kw_g2'];
            $runkwh_g2 = $res['runkwh_g2'];
            $runengine_g2 = $res['runengine_g2'];

            $start_g3= $res['start_g3'];
            $stop_g3 = $res['stop_g3'];
            $coil_g3 = $res['coil_g3'];
            $poil_g3 = $res['poil_g3'];
            $ampereu_g3 = $res['ampereu_g3'];
            $amperev_g3 = $res['amperev_g3'];;
            $amperew_g3 = $res['amperew_g3'];
            $kw_g3 = $res['kw_g3'];
            $runkwh_g3 = $res['runkwh_g3'];
            $runengine_g3 = $res['runengine_g3'];

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$start_g1.'</td>
                <td>'.$stop_g1.'</td>
                <td >'.$coil_g1.'</td>
                <td>'.$poil_g1.'</td>
                <td>'.$ampereu_g1.'</td>
                <td>'.$amperev_g1.'</td>
                <td>'.$amperew_g1.'</td>
                <td>'.$kw_g1.'</td>
                <td>'.$runkwh_g1.'</td>
                <td>'.$runengine_g1.'</td>
                
                <td>'.$start_g2.'</td>
                <td>'.$stop_g2.'</td>
                <td >'.$coil_g2.'</td>
                <td>'.$poil_g2.'</td>
                <td>'.$ampereu_g2.'</td>
                <td>'.$amperev_g2.'</td>
                <td>'.$amperew_g2.'</td>
                <td>'.$kw_g2.'</td>
                <td>'.$runkwh_g2.'</td>
                <td>'.$runengine_g2.'</td>

                    <td>'.$start_g3.'</td>
                <td>'.$stop_g3.'</td>
                <td >'.$coil_g3.'</td>
                <td>'.$poil_g3.'</td>
                <td>'.$ampereu_g3.'</td>
                <td>'.$amperev_g3.'</td>
                <td>'.$amperew_g3.'</td>
                <td>'.$kw_g3.'</td>
                <td>'.$runkwh_g3.'</td>
                <td>'.$runengine_g3.'</td>
                    </tr>';

                    $total_kw_g3 += $res['kw_g3'];
                    $total_kw_g2 += $res['kw_g2'];
                    $total_kw_g1 += $res['kw_g1'];
            }

           echo '<tr class=rowcontent>
                <td colspan=9>Total</td>
                <td>'.$total_kw_g1.'</td>
                <td></td>
                <td></td>
                
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_kw_g2.'</td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_kw_g3.'</td>
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

         $sPower1 = "SELECT * FROM pabrik_engine WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=30 align=center>LOGSHEET STASIUN ENGINE ROOM</td></tr>
        <tr><td colspan=30 align=center>GENSET</td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_genset WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
               <td>Tanggal</td>
                <td>JAM</td>
                <td>G1 START</td>
                <td>G1 STOP</td>
                <td>COLT OIL G1</td>
                <td>PRESS OIL G1</td>
                <td>AMPERE U G1</td>
                <td>AMPERE V G1</td>
                <td>AMPERE W G1</td>
                <td>KW G1</td>
                <td>RUN HOURS KWH G1</td>
                <td>RUN HOURS ENGINE G1</td>
                <td>G2 START</td>
                <td>G2 STOP</td>
                <td>COLT OIL G2</td>
                <td>PRESS OIL G2/td>
                <td>AMPERE U G2</td>
                <td>AMPERE V G2</td>
                <td>AMPERE W G2</td>
                <td>KW G2</td>
                <td>RUN HOURS KWH G2</td>
                <td>RUN HOURS ENGINE G2</td>
                <td>G3 START</td>
                <td>G3 STOP</td>
                <td>COLT OIL G3</td>
                <td>PRESS OIL G3/td>
                <td>AMPERE U G3</td>
                <td>AMPERE V G3</td>
                <td>AMPERE W G3</td>
                <td>KW G3</td>
                <td>RUN HOURS KWH G3</td>
                <td>RUN HOURS ENGINE G3</td>
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
            $start_g1= $res['start_g1'];
            $stop_g1 = $res['stop_g1'];
            $coil_g1 = $res['coil_g1'];
            $poil_g1 = $res['poil_g1'];
            $ampereu_g1 = $res['ampereu_g1'];
            $amperev_g1 = $res['amperev_g1'];;
            $amperew_g1 = $res['amperew_g1'];
            $kw_g1 = $res['kw_g1'];
            $runkwh_g1 = $res['runkwh_g1'];
            $runengine_g1 = $res['runengine_g1'];

            $start_g2= $res['start_g2'];
            $stop_g2 = $res['stop_g2'];
            $coil_g2 = $res['coil_g2'];
            $poil_g2 = $res['poil_g2'];
            $ampereu_g2 = $res['ampereu_g2'];
            $amperev_g2 = $res['amperev_g2'];;
            $amperew_g2 = $res['amperew_g2'];
            $kw_g2 = $res['kw_g2'];
            $runkwh_g2 = $res['runkwh_g2'];
            $runengine_g2 = $res['runengine_g2'];

            $start_g3= $res['start_g3'];
            $stop_g3 = $res['stop_g3'];
            $coil_g3 = $res['coil_g3'];
            $poil_g3 = $res['poil_g3'];
            $ampereu_g3 = $res['ampereu_g3'];
            $amperev_g3 = $res['amperev_g3'];;
            $amperew_g3 = $res['amperew_g3'];
            $kw_g3 = $res['kw_g3'];
            $runkwh_g3 = $res['runkwh_g3'];
            $runengine_g3 = $res['runengine_g3'];
            $kodeorg = $res['kodeorg'];

                $tab .= '                      <tr class=rowcontent>
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>
                <td>'.$start_g1.'</td>
                <td>'.$stop_g1.'</td>
                <td >'.$coil_g1.'</td>
                <td>'.$poil_g1.'</td>
                <td>'.$ampereu_g1.'</td>
                <td>'.$amperev_g1.'</td>
                <td>'.$amperew_g1.'</td>
                <td>'.$kw_g1.'</td>
                <td>'.$runkwh_g1.'</td>
                <td>'.$runengine_g1.'</td>
                
                <td>'.$start_g2.'</td>
                <td>'.$stop_g2.'</td>
                <td >'.$coil_g2.'</td>
                <td>'.$poil_g2.'</td>
                <td>'.$ampereu_g2.'</td>
                <td>'.$amperev_g2.'</td>
                <td>'.$amperew_g2.'</td>
                <td>'.$kw_g2.'</td>
                <td>'.$runkwh_g2.'</td>
                <td>'.$runengine_g2.'</td>

                    <td>'.$start_g3.'</td>
                <td>'.$stop_g3.'</td>
                <td >'.$coil_g3.'</td>
                <td>'.$poil_g3.'</td>
                <td>'.$ampereu_g3.'</td>
                <td>'.$amperev_g3.'</td>
                <td>'.$amperew_g3.'</td>
                <td>'.$kw_g3.'</td>
                <td>'.$runkwh_g3.'</td>
                <td>'.$runengine_g3.'</td>
                    </tr>';

               
                    $total_kw_g3 += $res['kw_g3'];
                    $total_kw_g2 += $res['kw_g2'];
                    $total_kw_g1 += $res['kw_g1'];
            }
            $tab .= '<tr class=rowcontent>
                <td colspan=9>Total</td>
                <td>'.$total_kw_g1.'</td>
                <td></td>
                <td></td>
                
                <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_kw_g2.'</td>
                <td></td>
                <td></td>

                    <td></td>
                <td></td>
                <td ></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_kw_g3.'</td>
                <td></td>
                <td></td>
                    </tr>';

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

        $nop_ = 'LaporanGenset'.$tglSkrg;

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