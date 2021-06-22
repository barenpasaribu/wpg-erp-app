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


   

        $sPower1 = "SELECT * FROM pabrik_rebusan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
     
        echo "<table cellspacing=1 border=0 class=sortable>
                <thead class=rowheader>
                  <tr>

              	<td>Tanggal</td>
				<td>Nomor Rebusan </td>
				<td>Start Pengisian TBS</td>
				<td>Stop Pengisian TBS</td>
				<td>Start Pembuangan udara</td>
				<td>Stop Pembuangan udara</td>
				<td>Start Puncak I</td>
				<td>Stop Puncak I</td>
				<td>T. Uap (Bar) Puncak I</td>
				<td>Start Pembuangan</td>
				<td>Stop Pembuangan</td>
				<td>Start Puncak II</td>
				<td>Stop Puncak II</td>
				<td>T. Uap (Bar) Puncak II</td>
				<td>Start Pembuangan</td>
				<td>Stop Pembuangan</td>
				<td>Start Puncak III</td>
				<td>Stop Puncak III</td>
				<td>T. Uap (Bar) Puncak III</td>
				<td>Start Penahanan</td>
				<td>Stop Penahanan</td>
				<td>T. Uap (Bar) Penahanan</td>
				<td>Start Pembuangan</td>
				<td>Stop Pembuangan</td>
				<td>Keterangan</td>
                </tr>
                </thead>
            <tbody>";

        $qData = mysql_query($sPower1);
        $brs = mysql_num_rows($qData);
         // echo $sPower1;
        if (0 < $brs) {

            while ($res = mysql_fetch_assoc($qData)) {

               
               $tgl= $res['tgl'];
			$nomor_rebusan= $res['nomor_rebusan'];
			$start_pengisisantbs = $res['start_pengisiantbs'];
			$stop_pengisisantbs  = $res['stop_pengisiantbs'];
			$start_pembuangan1 = $res['start_pembuangan1'];
			$stop_pembuangan1 = $res['stop_pembuangan1'];
			$start_puncak1 = $res['start_puncak1'];;
			$stop_puncak1 = $res['stop_puncak1'];
			$uap_puncak1 = $res['uap_puncak1'];
			$start_pembuangan2 = $res['start_pembuangan2'];
			$stop_pembuangan2 = $res['stop_pembuangan2'];
			$start_puncak2 = $res['start_puncak2'];
			$stop_puncak2 = $res['stop_puncak2'];
			$uap_puncak2 = $res['uap_puncak2'];
			$start_pembuangan3 = $res['start_pembuangan3'];
			$stop_pembuangan3 = $res['stop_pembuangan3'];
			$start_puncak3 = $res['start_puncak3'];
			$stop_puncak3 = $res['stop_puncak3'];
			$uap_puncak3 = $res['uap_puncak3'];
			$start_penahanan = $res['start_penahanan'];
			$stop_penahanan = $res['stop_penahanan'];
			$uap_penahanan = $res['uap_penahanan'];
			$start_pembuangan4 = $res['start_pembuangan4'];
			$stop_pembuangan4 = $res['stop_pembuangan4'];
			$keterangan = $res['keterangan'];

                echo '<tr class=rowcontent>
                <td>'.$tgl.'</td>
				<td>'.$nomor_rebusan.'</td>
				<td>'.$start_pengisisantbs.'</td>
				<td>'.$stop_pengisisantbs.'</td>
				<td >'.$start_pembuangan1.'</td>
				<td>'.$stop_pembuangan1.'</td>
				<td>'.$start_puncak1.'</td>
				<td>'.$stop_puncak1.'</td>
				<td>'.$uap_puncak1.'</td>
				<td>'.$start_pembuangan2.'</td>
				<td>'.$stop_pembuangan2.'</td>
				<td>'.$start_puncak2.'</td>
				<td>'.$stop_puncak2.'</td>
				<td>'.$uap_puncak2.'</td>
				<td>'.$start_pembuangan3.'</td>
				<td>'.$stop_pembuangan3.'</td>
				<td>'.$start_puncak3.'</td>
				<td>'.$stop_puncak3.'</td>
				<td>'.$uap_puncak3.'</td>
				<td>'.$start_penahanan.'</td>
				<td>'.$stop_penahanan.'</td>
				<td>'.$uap_penahanan.'</td>
				<td>'.$start_pembuangan4.'</td>
				<td>'.$stop_pembuangan4.'</td>
				<td>'.$keterangan.'</td>
                    </tr>';

                   
            }
               // $sLorinik = "SELECT sum(jml_lorituang) as jml_lori, shift_nik FROM pabrik_tripler WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' group by shift_nik ";
               //   $qLori = mysql_query($sLorinik);
               //   while ($resL = mysql_fetch_assoc($qLori)) {
               //      $sKaryawanL = "SELECT namakaryawan FROM datakaryawan WHERE nik = '".$res['shift_nik']."'  ";
               //   $qKL = mysql_query($sKaryawanL);
               //   while ($resKL = mysql_fetch_assoc($qKL)) {
               //      $namakaryawan = $resKL['namakaryawan'];
               //      }
                
                    
               
           

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

         $sPower1 = "SELECT * FROM pabrik_rebusan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";
        

        $tab .= "<table cellspacing='1' border=0><tr><td colspan=7 align=center>JURNAL STERILIZER STATION</td></tr>
        <tr><td colspan=30 align=center></td></tr>\r\n        ";

        

        $tab .= '</table>';

       

        if ('' === $tgl_1 && '' === $tgl_2) {
            echo 'warning: Date required';
            exit();
        }

        $whr = '';




        $sPower1 = "SELECT * FROM pabrik_rebusan WHERE kodeorg LIKE '%".$kdPabrik."%' and date(tgl) between '".$tgl_1."' and '".$tgl_2."' ";

        $tab .= "<table cellspacing=1 border=1 class=sortable>
                <thead class=rowheader>
                 
                 <tr bgcolor=#DEDEDE>
               <td>Tanggal</td>
                <td>Nomor Rebusan </td>
                <td>Start Pengisian TBS</td>
                <td>Stop Pengisian TBS</td>
                <td>Start Pembuangan udara</td>
                <td>Stop Pembuangan udara</td>
                <td>Start Puncak I</td>
                <td>Stop Puncak I</td>
                <td>T. Uap (Bar) Puncak I</td>
                <td>Start Pembuangan</td>
                <td>Stop Pembuangan</td>
                <td>Start Puncak II</td>
                <td>Stop Puncak II</td>
                <td>T. Uap (Bar) Puncak II</td>
                <td>Start Pembuangan</td>
                <td>Stop Pembuangan</td>
                <td>Start Puncak III</td>
                <td>Stop Puncak III</td>
                <td>T. Uap (Bar) Puncak III</td>
                <td>Start Penahanan</td>
                <td>Stop Penahanan</td>
                <td>T. Uap (Bar) Penahanan</td>
                <td>Start Pembuangan</td>
                <td>Stop Pembuangan</td>
                <td>Keterangan</td>
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
            $nomor_rebusan= $res['nomor_rebusan'];
            $start_pengisisantbs = $res['start_pengisiantbs'];
            $stop_pengisisantbs = $res['stop_pengisiantbs'];
            $start_pembuangan1 = $res['start_pembuangan1'];
            $stop_pembuangan1 = $res['stop_pembuangan1'];
            $start_puncak1 = $res['start_puncak1'];;
            $stop_puncak1 = $res['stop_puncak1'];
            $uap_puncak1 = $res['uap_puncak1'];
            $start_pembuangan2 = $res['start_pembuangan2'];
            $stop_pembuangan2 = $res['stop_pembuangan2'];
            $start_puncak2 = $res['start_puncak2'];
            $stop_puncak2 = $res['stop_puncak2'];
            $uap_puncak2 = $res['uap_puncak2'];
            $start_pembuangan3 = $res['start_pembuangan3'];
            $stop_pembuangan3 = $res['stop_pembuangan3'];
            $start_puncak3 = $res['start_puncak3'];
            $stop_puncak3 = $res['stop_puncak3'];
            $uap_puncak3 = $res['uap_puncak3'];
            $start_penahanan = $res['start_penahanan'];
            $stop_penahanan = $res['stop_penahanan'];
            $uap_penahanan = $res['uap_penahanan'];
            $start_pembuangan4 = $res['start_pembuangan4'];
            $stop_pembuangan4 = $res['stop_pembuangan4'];
            $keterangan = $res['keterangan'];
            $kodeorg = $res['kodeorg'];

                $tab .= '                      <tr class=rowcontent>
               
                <td>'.$tgl.'</td>
                <td>'.$nomor_rebusan.'</td>
                <td>'.$start_pengisisantbs.'</td>
                <td>'.$stop_pengisisantbs.'</td>
                <td >'.$start_pembuangan1.'</td>
                <td>'.$stop_pembuangan1.'</td>
                <td>'.$start_puncak1.'</td>
                <td>'.$stop_puncak1.'</td>
                <td>'.$uap_puncak1.'</td>
                <td>'.$start_pembuangan2.'</td>
                <td>'.$stop_pembuangan2.'</td>
                <td>'.$start_puncak2.'</td>
                <td>'.$stop_puncak2.'</td>
                <td>'.$uap_puncak2.'</td>
                <td>'.$start_pembuangan3.'</td>
                <td>'.$stop_pembuangan3.'</td>
                <td>'.$start_puncak3.'</td>
                <td>'.$stop_puncak3.'</td>
                <td>'.$uap_puncak3.'</td>
                <td>'.$start_penahanan.'</td>
                <td>'.$stop_penahanan.'</td>
                <td>'.$uap_penahanan.'</td>
                <td>'.$start_pembuangan4.'</td>
                <td>'.$stop_pembuangan4.'</td>
                <td>'.$keterangan.'</td>
                    </tr>';

                  
               
            
          
             
        }

        $tab .= '                      <tr class=rowcontent>
               
                <td>Total Olah</td>
                <td colspan=5>'.$brs.'</td>
                    </tr>';
                     
$tab .= '   </table><table cellspacing=1 border=0 class=sortable>
 <tr>
                
                </tr>
                <tr>
                
                </tr>
                <tr>
                
                </tr> 
</table>';
            
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
        // echo $tab;
        $nop_ = 'LaporanRebusanN'.$tglSkrg;

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