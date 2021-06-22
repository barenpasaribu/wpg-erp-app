<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$proses = $param['proses'];
$noTrans = $param['noTrans'];
switch ($proses) {
    case 'postingSpk':
        $qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='".$app."'");
        $tmpPost = fetchData($qPosting);
        $postJabatan = $tmpPost[0]['jabatan'];
		
		$sCek = 'select kodeorg,notransaksi,divisi,posting,useridcreate,useridapprove from '.$dbname.".log_spkht where notransaksi='".$noTrans."' and posting=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		$qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
		$useridapprove = 0;

        //echo "warning: ".$sCek." / jumrow".$rcek;
		//exit();
		
        if (0 < $rCek) {
            while ($bar = mysql_fetch_object($qCek)) {
                //yg posting harus tidak sama dengan yang buat
				/*
				if( $bar->useridcreate == $_SESSION['standard']['userid']){
					exit('Warning: User pembuat tidak bisa melakukan posting');
				}
				*/
				$useridapprove = (int)$bar->useridapprove;
				
				/*
				// WPG-MPSG : katanya maunya posting dan approve dulu baru bisa BA - FA 202001
				$x = 0;
                $strx = 'select sum(jumlahrealisasi) from '.$dbname.".log_baspk where notransaksi='".$noTrans."'";
                $resx = mysql_query($strx);
                while ($barx = mysql_fetch_array($resx)) {
                    $x = $barx[0];
                }
                $y = '';
                $strx = 'select statusjurnal from '.$dbname.".log_baspk where notransaksi='".$noTrans."' and statusjurnal=0";
                $resx = mysql_query($strx);
                if (0 < mysql_num_rows($resx)) {
                    exit('Warning:Realisasi SPK belum di posting');
                }

                if (0 == $x && '' == $y) {
                    exit('Warning:Belum Ada Realisasi');
                }
				*/
            }
//            $sCekTot = 'select kodeblok from '.$dbname.".log_spkdt where notransaksi='".$noTrans."'";
            $sCekTot = 'select * from '.$dbname.".log_spkdt where notransaksi='".$noTrans."'";
            $qCekTot = mysql_query($sCekTot);
            $rCekTot = mysql_num_rows($qCekTot);
			/*
            $sCekTot2 = 'select kodeblok from '.$dbname.".log_baspk where notransaksi='".$noTrans."'";
            $qCekTot2 = mysql_query($sCekTot2);
            $rCekTot2 = mysql_num_rows($qCekTot2);
			*/
			
			//echo "warning: row1=".$rCekTot." /query1=".$sCekTot."  /row2=".$rCekTot2." /query2=".$sCekTot2;
			//exit();
			
//            if (0 == $rCekTot2 || '' == $rCekTot2) {
            if (0 == $rCekTot || '' == $rCekTot) {
//                echo 'warning:BAPP Belum Ada Realisasi';
                echo 'warning: SPK belum ada detailnya';
                exit();
            }
			
			if($useridapprove == 0){
				$sUp = 'update  '.$dbname.".log_spkht set useridapprove='".$_SESSION['standard']['userid']."',tglapprove='".date("Y-m-d")."' where notransaksi='".$noTrans."'";
				if (!mysql_query($sUp)) {
					echo 'DB Error : '.mysql_error($conn);
					exit();
				}
			}else{
				if($useridapprove == $_SESSION['standard']['userid']){
					echo 'warning:User yang Approve dan Posting harus berbeda';
					exit();
				}
				
				$sUp = 'update  '.$dbname.".log_spkht set posting='1',useridposting='".$_SESSION['standard']['userid']."',tglposting='".date("Y-m-d")."' where notransaksi='".$noTrans."'";
				if (!mysql_query($sUp)) {
					echo 'Gagal Posting, DB Error : '.mysql_error($conn);
					exit();
				}
				
				/*
				$sUpBaspk = 'update '.$dbname.".log_baspk set posting='1' where notransaksi='".$noTrans."'";
				if (!mysql_query($sUpBaspk)) {
					$sUp = 'update  '.$dbname.".log_spkht set posting='0' where notransaksi='".$noTrans."'";
					mysql_query($sUp) || exit(mysql_error());
					echo 'DB Error : '.mysql_error($conn);
					exit();
				}
				*/
			}

            break;
        }

        echo 'Warning: Gala Posting atau Data Sudah Terposting Sebelumnya';
        exit();
    default:
        break;
}

?>
