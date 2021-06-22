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


   

        $sPower1 = "SELECT * FROM pabrik_pressan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                <tr>
               <td></td>
               <td></td>
               <td colspan =32 >Digester</td>
               <td colspan =24 >Press</td>
               <td colspan=3></td>
               </tr>
                  <tr>
               <td>Tanggal</td>
                <td>JAM </td>
                <td>TEMP D1</td>
                <td>LEVEL D1</td>
                <td>AMP D1</td>
                <td>HM D1</td>
                <td>TEMP D2</td>
                <td>LEVEL D2</td>
                <td>AMP D2</td>
                <td>HM D2</td>
                <td>TEMP D3</td>
                <td>LEVEL D3</td>
                <td>AMP D3</td>
                <td>HM D3</td>
                <td>TEMP D4</td>
                <td>LEVEL D4</td>
                <td>AMP D4</td>
                <td>HM D4</td>
                <td>TEMP D5</td>
                <td>LEVEL D5</td>
                <td>AMP D5</td>
                <td>HM D5</td>
                <td>TEMP D6</td>
                <td>LEVEL D6</td>
                <td>AMP D6</td>
                <td>HM D6</td>
                <td>TEMP D7</td>
                <td>LEVEL D7</td>
                <td>AMP D7</td>
                <td>HM D7</td>
                <td>TEMP D8</td>
                <td>LEVEL D8</td>
                <td>AMP D8</td>
                <td>HM D8</td>


                <td>T. HYD P1</td>
                <td>AMP P1</td>
                <td>HM P1</td>
                <td>T. HYD P2</td>
                <td>AMP P2</td>
                <td>HM P2</td>
                <td>T. HYD P3</td>
                <td>AMP P3</td>
                <td>HM P3</td>
                <td>T. HYD P4</td>
                <td>AMP P4</td>
                <td>HM P4</td>
                <td>T. HYD P5</td>
                <td>AMP P5</td>
                <td>HM P5</td>
                <td>T. HYD P6</td>
                <td>AMP P6</td>
                <td>HM P6</td>
                <td>T. HYD P7</td>
                <td>AMP P7</td>
                <td>HM P7</td>
                <td>T. HYD P8</td>
                <td>AMP P8</td>
                <td>HM P8</td>

                
                <td>HM CBC1</td>
                <td>HM CBC2</td>
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
            $temp_d1 = $res['temp_d1'];
            $level_d1 = $res['level_d1'];
            $amp_d1 = $res['amp_d1'];
            $hm_d1 = $res['hm_d1'];

            $temp_d2 = $res['temp_d2'];
            $level_d2 = $res['level_d2'];
            $amp_d2 = $res['amp_d2'];
            $hm_d2 = $res['hm_d2'];

            $temp_d3 = $res['temp_d3'];
            $level_d3 = $res['level_d3'];
            $amp_d3 = $res['amp_d3'];
            $hm_d3 = $res['hm_d3'];

            $temp_d4 = $res['temp_d4'];
            $level_d4 = $res['level_d4'];
            $amp_d4 = $res['amp_d4'];
            $hm_d4 = $res['hm_d4'];

            $temp_d5 = $res['temp_d5'];
            $level_d5 = $res['level_d5'];
            $amp_d5 = $res['amp_d5'];
            $hm_d5 = $res['hm_d5'];

            $temp_d6 = $res['temp_d6'];
            $level_d6 = $res['level_d6'];
            $amp_d6 = $res['amp_d6'];
            $hm_d6 = $res['hm_d6'];

            $temp_d7 = $res['temp_d7'];
            $level_d7 = $res['level_d7'];
            $amp_d7 = $res['amp_d7'];
            $hm_d7 = $res['hm_d7'];

            $temp_d8 = $res['temp_d8'];
            $level_d8 = $res['level_d8'];
            $amp_d8 = $res['amp_d8'];
            $hm_d8 = $res['hm_d8'];

            $th_p1 = $res['th_p1'];;
            $amp_p1 = $res['amp_p1'];
            $hm_p1 = $res['hm_p1'];

            $th_p2 = $res['th_p2'];;
            $amp_p2 = $res['amp_p2'];
            $hm_p2 = $res['hm_p2'];

            $th_p3 = $res['th_p3'];;
            $amp_p3 = $res['amp_p3'];
            $hm_p3 = $res['hm_p3'];

            $th_p4 = $res['th_p4'];;
            $amp_p4 = $res['amp_p4'];
            $hm_p4 = $res['hm_p4'];

            $th_p5 = $res['th_p5'];;
            $amp_p5 = $res['amp_p5'];
            $hm_p5 = $res['hm_p5'];

            $th_p6 = $res['th_p6'];;
            $amp_p6 = $res['amp_p6'];
            $hm_p6 = $res['hm_p6'];

            $th_p7 = $res['th_p7'];;
            $amp_p7 = $res['amp_p7'];
            $hm_p7 = $res['hm_p7'];

            $th_p8 = $res['th_p8'];;
            $amp_p8 = $res['amp_p8'];
            $hm_p8 = $res['hm_p8'];

            $hm_cbc1 = $res['hm_cbc1'];
            $hm_cbc2 = $res['hm_cbc2'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                echo '<tr class=rowcontent>
           <td>'.$tgl.'</td>
                <td>'.$jam.'</td>

                <td>'.$temp_d1.'</td>
                <td>'.$level_d1.'</td>
                <td >'.$amp_d1.'</td>
                <td>'.$hm_d1.'</td>

                    <td>'.$temp_d2.'</td>
                <td>'.$level_d2.'</td>
                <td >'.$amp_d2.'</td>
                <td>'.$hm_d2.'</td>

                    <td>'.$temp_d3.'</td>
                <td>'.$level_d3.'</td>
                <td >'.$amp_d3.'</td>
                <td>'.$hm_d3.'</td>

                    <td>'.$temp_d4.'</td>
                <td>'.$level_d4.'</td>
                <td >'.$amp_d4.'</td>
                <td>'.$hm_d4.'</td>

                    <td>'.$temp_d5.'</td>
                <td>'.$level_d5.'</td>
                <td >'.$amp_d5.'</td>
                <td>'.$hm_d5.'</td>

                    <td>'.$temp_d6.'</td>
                <td>'.$level_d6.'</td>
                <td >'.$amp_d6.'</td>
                <td>'.$hm_d6.'</td>

                    <td>'.$temp_d7.'</td>
                <td>'.$level_d7.'</td>
                <td >'.$amp_d7.'</td>
                <td>'.$hm_d7.'</td>

                    <td>'.$temp_d8.'</td>
                <td>'.$level_d8.'</td>
                <td >'.$amp_d8.'</td>
                <td>'.$hm_d8.'</td>

                <td>'.$th_p1.'</td>
                <td >'.$amp_p1.'</td>
                <td>'.$hm_p1.'</td>

                <td>'.$th_p2.'</td>
                <td >'.$amp_p2.'</td>
                <td>'.$hm_p2.'</td>

                <td>'.$th_p3.'</td>
                <td >'.$amp_p3.'</td>
                <td>'.$hm_p3.'</td>

                <td>'.$th_p4.'</td>
                <td >'.$amp_p4.'</td>
                <td>'.$hm_p4.'</td>

                <td>'.$th_p5.'</td>
                <td >'.$amp_p5.'</td>
                <td>'.$hm_p5.'</td>

                <td>'.$th_p6.'</td>
                <td >'.$amp_p6.'</td>
                <td>'.$hm_p6.'</td>

                <td>'.$th_p7.'</td>
                <td >'.$amp_p7.'</td>
                <td>'.$hm_p7.'</td>

                <td>'.$th_p8.'</td>
                <td >'.$amp_p8.'</td>
                <td>'.$hm_p8.'</td>
                <td>'.$hm_cbc1.'</td>
                <td >'.$hm_cbc2.'</td>
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

         $sPower1 = "SELECT * FROM pabrik_pressan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL PRESS STATION</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_pressan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 
              <tr bgcolor=#DEDEDE>
               <td></td>
               <td></td>
               <td colspan =32 >Digester</td>
               <td colspan =24 >Press</td>
               <td colspan=3></td>
               </tr>
                  <tr bgcolor=#DEDEDE>
               <td>Tanggal</td>
                <td>JAM </td>
                <td>TEMP D1</td>
                <td>LEVEL D1</td>
                <td>AMP D1</td>
                <td>HM D1</td>
                <td>TEMP D2</td>
                <td>LEVEL D2</td>
                <td>AMP D2</td>
                <td>HM D2</td>
                <td>TEMP D3</td>
                <td>LEVEL D3</td>
                <td>AMP D3</td>
                <td>HM D3</td>
                <td>TEMP D4</td>
                <td>LEVEL D4</td>
                <td>AMP D4</td>
                <td>HM D4</td>
                <td>TEMP D5</td>
                <td>LEVEL D5</td>
                <td>AMP D5</td>
                <td>HM D5</td>
                <td>TEMP D6</td>
                <td>LEVEL D6</td>
                <td>AMP D6</td>
                <td>HM D6</td>
                <td>TEMP D7</td>
                <td>LEVEL D7</td>
                <td>AMP D7</td>
                <td>HM D7</td>
                <td>TEMP D8</td>
                <td>LEVEL D8</td>
                <td>AMP D8</td>
                <td>HM D8</td>


                <td>T. HYD P1</td>
                <td>AMP P1</td>
                <td>HM P1</td>
                <td>T. HYD P2</td>
                <td>AMP P2</td>
                <td>HM P2</td>
                <td>T. HYD P3</td>
                <td>AMP P3</td>
                <td>HM P3</td>
                <td>T. HYD P4</td>
                <td>AMP P4</td>
                <td>HM P4</td>
                <td>T. HYD P5</td>
                <td>AMP P5</td>
                <td>HM P5</td>
                <td>T. HYD P6</td>
                <td>AMP P6</td>
                <td>HM P6</td>
                <td>T. HYD P7</td>
                <td>AMP P7</td>
                <td>HM P7</td>
                <td>T. HYD P8</td>
                <td>AMP P8</td>
                <td>HM P8</td>

                
                <td>HM CBC1</td>
                <td>HM CBC2</td>
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
            $temp_d1 = $res['temp_d1'];
            $level_d1 = $res['level_d1'];
            $amp_d1 = $res['amp_d1'];
            $hm_d1 = $res['hm_d1'];

            $temp_d2 = $res['temp_d2'];
            $level_d2 = $res['level_d2'];
            $amp_d2 = $res['amp_d2'];
            $hm_d2 = $res['hm_d2'];

            $temp_d3 = $res['temp_d3'];
            $level_d3 = $res['level_d3'];
            $amp_d3 = $res['amp_d3'];
            $hm_d3 = $res['hm_d3'];

            $temp_d4 = $res['temp_d4'];
            $level_d4 = $res['level_d4'];
            $amp_d4 = $res['amp_d4'];
            $hm_d4 = $res['hm_d4'];

            $temp_d5 = $res['temp_d5'];
            $level_d5 = $res['level_d5'];
            $amp_d5 = $res['amp_d5'];
            $hm_d5 = $res['hm_d5'];

            $temp_d6 = $res['temp_d6'];
            $level_d6 = $res['level_d6'];
            $amp_d6 = $res['amp_d6'];
            $hm_d6 = $res['hm_d6'];

            $temp_d7 = $res['temp_d7'];
            $level_d7 = $res['level_d7'];
            $amp_d7 = $res['amp_d7'];
            $hm_d7 = $res['hm_d7'];

            $temp_d8 = $res['temp_d8'];
            $level_d8 = $res['level_d8'];
            $amp_d8 = $res['amp_d8'];
            $hm_d8 = $res['hm_d8'];

            $th_p1 = $res['th_p1'];;
            $amp_p1 = $res['amp_p1'];
            $hm_p1 = $res['hm_p1'];

            $th_p2 = $res['th_p2'];;
            $amp_p2 = $res['amp_p2'];
            $hm_p2 = $res['hm_p2'];

            $th_p3 = $res['th_p3'];;
            $amp_p3 = $res['amp_p3'];
            $hm_p3 = $res['hm_p3'];

            $th_p4 = $res['th_p4'];;
            $amp_p4 = $res['amp_p4'];
            $hm_p4 = $res['hm_p4'];

            $th_p5 = $res['th_p5'];;
            $amp_p5 = $res['amp_p5'];
            $hm_p5 = $res['hm_p5'];

            $th_p6 = $res['th_p6'];;
            $amp_p6 = $res['amp_p6'];
            $hm_p6 = $res['hm_p6'];

            $th_p7 = $res['th_p7'];;
            $amp_p7 = $res['amp_p7'];
            $hm_p7 = $res['hm_p7'];

            $th_p8 = $res['th_p8'];;
            $amp_p8 = $res['amp_p8'];
            $hm_p8 = $res['hm_p8'];

            $hm_cbc1 = $res['hm_cbc1'];
            $hm_cbc2 = $res['hm_cbc2'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                $tab .= '                      <tr class=rowcontent>
               
                <td>'.$tgl.'</td>
                <td>'.$jam.'</td>

                <td>'.$temp_d1.'</td>
                <td>'.$level_d1.'</td>
                <td >'.$amp_d1.'</td>
                <td>'.$hm_d1.'</td>

                    <td>'.$temp_d2.'</td>
                <td>'.$level_d2.'</td>
                <td >'.$amp_d2.'</td>
                <td>'.$hm_d2.'</td>

                    <td>'.$temp_d3.'</td>
                <td>'.$level_d3.'</td>
                <td >'.$amp_d3.'</td>
                <td>'.$hm_d3.'</td>

                    <td>'.$temp_d4.'</td>
                <td>'.$level_d4.'</td>
                <td >'.$amp_d4.'</td>
                <td>'.$hm_d4.'</td>

                    <td>'.$temp_d5.'</td>
                <td>'.$level_d5.'</td>
                <td >'.$amp_d5.'</td>
                <td>'.$hm_d5.'</td>

                    <td>'.$temp_d6.'</td>
                <td>'.$level_d6.'</td>
                <td >'.$amp_d6.'</td>
                <td>'.$hm_d6.'</td>

                    <td>'.$temp_d7.'</td>
                <td>'.$level_d7.'</td>
                <td >'.$amp_d7.'</td>
                <td>'.$hm_d7.'</td>

                    <td>'.$temp_d8.'</td>
                <td>'.$level_d8.'</td>
                <td >'.$amp_d8.'</td>
                <td>'.$hm_d8.'</td>

                <td>'.$th_p1.'</td>
                <td >'.$amp_p1.'</td>
                <td>'.$hm_p1.'</td>

                <td>'.$th_p2.'</td>
                <td >'.$amp_p2.'</td>
                <td>'.$hm_p2.'</td>

                <td>'.$th_p3.'</td>
                <td >'.$amp_p3.'</td>
                <td>'.$hm_p3.'</td>

                <td>'.$th_p4.'</td>
                <td >'.$amp_p4.'</td>
                <td>'.$hm_p4.'</td>

                <td>'.$th_p5.'</td>
                <td >'.$amp_p5.'</td>
                <td>'.$hm_p5.'</td>

                <td>'.$th_p6.'</td>
                <td >'.$amp_p6.'</td>
                <td>'.$hm_p6.'</td>

                <td>'.$th_p7.'</td>
                <td >'.$amp_p7.'</td>
                <td>'.$hm_p7.'</td>

                <td>'.$th_p8.'</td>
                <td >'.$amp_p8.'</td>
                <td>'.$hm_p8.'</td>
                <td>'.$hm_cbc1.'</td>
                <td >'.$hm_cbc2.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

                    
               
            }
             $tab .= '                      <tr class=rowcontent>
               
                <td></td>
                <td colspan=5>'.$total_lori.'</td>
                    </tr></table>';
             

          
            $tab .= '   <table cellspacing=1 border=1 class=sortable>                   
                
                
                <tr>
                
                <td> EQUIPMENT</td>
                <td> HM OPERASI (HI)</td>
                <td> HM OPERASI (SDHI)</td>
               
                 </tr><tr>
                <td> Disgester 1</td>
                <td> </td>
                <td> </td>
                </tr>
                <tr>
                 
                <td>Disgester 2</td>
                <td> </td>
                <td> </td>
               
                 </tr><tr>
                <td> Disgester 3</td>
                <td> </td>
                <td> </td>
                
                 </tr><tr>
                <td> Disgester 4</td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> Disgester 5</td>
                <td> </td>
                <td> </td>
              
                 </tr><tr>
                <td> Disgester 6</td>
                <td> </td>
                <td> </td>
             
                 </tr><tr>
                <td> Disgester 7</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Disgester 8</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 1</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 2</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 3</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 4</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 5</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 6</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 7</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> Showpress 8</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> CBC 1</td>
                <td> </td>
                <td> </td>
                </tr><tr>
                <td> CBC 2</td>
                <td> </td>
                <td> </td>
                </tr>
                </table>';
                $tab .= '   <table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
                </tr>
                 <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr></table>';
                $tab .= '   <table cellspacing=1 border=1 class=sortable>                   
                
                
                <tr>
                
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

            $tab .= '   <table cellspacing=1 border=0 class=sortable>                   <tr class=rowcontent rowspan=10>
               
                
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

        $nop_ = 'LaporanPressan'.$tglSkrg;

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