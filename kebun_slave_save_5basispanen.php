<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$method = $_POST['method'];
$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment \r\n                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg = mysql_query($sreg) ;
$rreg = mysql_fetch_assoc($qreg);
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".kebun_5basispanen set kodeorg='".$_POST['regId']."',jenis='".$_POST['jnsId']."',\r\n              bjr='".$_POST['bjr']."',basisjjg='".$_POST['basisjjg']."',rplebih='".$_POST['rpperkg']."'\r\n            ,dendabasis='".$_POST['denda']."',rptopografi='".$_POST['insentif']."'\r\n            ,updateby='".$_SESSION['standard']['userid']."'\r\n\t     where kodeorg='".$_POST['oldReg']."' and jenis='".$_POST['oldJns']."' \r\n                and bjr='".$_POST['oldBjr']."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        if ('' === $_POST['bjr']) {
            $_POST['bjr'] = 0;
        }

        $scek = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n               bjr='".$_POST['bjr']."' and jenis='".$_POST['jnsId']."' and\r\n               kodeorg='".$_POST['regId']."'";
        $qcek = mysql_query($scek) ;
        if (0 === mysql_num_rows($qcek)) {
            $sIns = 'insert into '.$dbname.".kebun_5basispanen (`kodeorg`, `jenis`, `bjr`,`basisjjg`,`rplebih`,`dendabasis`,`rptopografi`,`updateby`) values \r\n                   ('".$_POST['regId']."','".$_POST['jnsId']."','".$_POST['bjr']."','".$_POST['basisjjg']."','".$_POST['rpperkg']."','".$_POST['denda']."'\r\n                    ,'".$_POST['insentif']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sIns)) {
                echo ' error,'.addslashes(mysql_error($conn));
            }

            break;
        }

        exit('error: Data already exist');
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5harilibur\r\n\t where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['regional']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jenis']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['bjr']."</td>\r\n                        <td>".$_SESSION['lang']['basiskg']."</td>\r\n                        <td>".$_SESSION['lang']['rpperkg']."</td>\r\n                        <td>".$_SESSION['lang']['denda']."</td>\r\n                        <td>".$_SESSION['lang']['insentif'].' '.$_SESSION['lang']['topografi']."</td>\r\n\t\t\t<td style='width:30px;'  align=center>*</td></tr>\r\n\t\t </thead><tbody >";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_5basispanen \r\n               where (kodeorg='".$rreg['regional']."' or left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rreg['regional']."'))\r\n               order by kodeorg desc";
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $sdata = 'select distinct * from '.$dbname.".kebun_5basispanen \r\n                where (kodeorg='".$rreg['regional']."' or left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rreg['regional']."'))\r\n                order by kodeorg desc limit ".$offset.','.$limit.'';
        $res1 = mysql_query($sdata) ;
        if (0 < mysql_num_rows($res1)) {
            while ($bar1 = mysql_fetch_object($res1)) {
                if (6 === strlen($bar1->kodeorg)) {
                    $a = $nmOrg[$bar1->kodeorg];
                } else {
                    $a = $bar1->kodeorg;
                }

                echo "<tr class=rowcontent>\r\n                    <td>".$a."</td>\r\n                    <td>".$bar1->jenis."</td>\r\n                    <td align=right>".$bar1->bjr."</td>\r\n                    <td align=right>".$bar1->basisjjg."</td>\r\n                    <td align=right>".$bar1->rplebih."</td>\r\n                    <td>".$bar1->dendabasis."</td>\r\n                    <td align=right>".$bar1->rptopografi."</td>\r\n\t\t\t\t   <td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' \r\n                                       onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->bjr."','".$bar1->basisjjg."','".$bar1->rplebih."','".$bar1->dendabasis."','".$bar1->rptopografi."');\"></td></tr>";
            }
            echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n                   \r\n\t\t </tfoot>\r\n\t\t </table>";
        } else {
            echo '<tr class=rowcontent><td colspan=4>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
}

?>