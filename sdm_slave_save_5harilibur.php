<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';

$method = $_POST['method'];
$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment \n                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg = mysql_query($sreg);
$rreg = mysql_fetch_assoc($qreg);
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".sdm_5harilibur set tanggal='".tanggalsystem($_POST['tanggal'])."',keterangan='".$_POST['ket']."'\n               ,ishariraya='".$_POST['ishariraya']."'\n               ,updateby='".$_SESSION['standard']['userid']."'\n\t       where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tglOld'])."'";

        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $scek = 'select distinct * from '.$dbname.".sdm_5harilibur where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
        $qcek = mysql_query($scek);
        if (mysql_num_rows($qcek)==0) {
            $sIns = 'insert into '.$dbname.".sdm_5harilibur (`regional`, `tanggal`, `keterangan`, `ishariraya`, `updateby`) values \n                   ('".$_POST['regId']."','".tanggalsystem($_POST['tanggal'])."','".$_POST['ket']."', '".$_POST['ishariraya']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sIns)) {
                echo ' error,'.addslashes(mysql_error($conn));
            }
        } else {
            exit('error: Data already exist');
        }
        
        break;

    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5harilibur\n\t where regional='".$_POST['regId']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        if ('' != $_POST['ktrnganCr']) {
            $whr .= " and keterangan like '%".$_POST['ktrnganCr']."%'";
        }

        if ('' != $_POST['tgl_cari']) {
            $whr .= " and tanggal = '".tanggalsystem($_POST['tgl_cari'])."'";
        }

        echo "<table class=sortable cellspacing=1 border=0 style='width:600px;'>\n\t     <thead>\n\t\t <tr class=rowheader>\n\t\t    <td style='width:150px;'>".$_SESSION['lang']['regional']."</td>\n\t\t\t<td>".$_SESSION['lang']['tanggal']."</td>\n\t\t\t<td>".$_SESSION['lang']['keterangan']."</td>\n\t\t\t<td>Tipe Hari Libur</td>\n\t\t\t<td style='width:30px;'>*</td></tr>\n\t\t </thead><tbody >";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_5harilibur \n                where regional='".$rreg['regional']."' ".$whr.' order by tanggal desc';
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $sdata = 'select distinct * from '.$dbname.".sdm_5harilibur \n                where regional='".$rreg['regional']."' ".$whr." \n                order by tanggal desc limit ".$offset.','.$limit.'';
        $res1 = mysql_query($sdata);
        if (0 < mysql_num_rows($res1)) {
            // $bar1 = mysql_fetch_object($res1);
            // print_r($bar1);
            // die();
            while ($bar1 = mysql_fetch_object($res1)) {
                $isHariRaya = "";
                if($bar1->ishariraya){
                    $isHariRaya = "Hari Raya";
                }else{
                    $isHariRaya = "Bukan Hari Raya";
                }
                echo "<tr class=rowcontent>\n\t\t           <td align=center>".$bar1->regional."</td>\n\t\t\t\t   <td>".tanggalnormal($bar1->tanggal)."</td>\n\t\t\t\t   <td align=center>".$bar1->keterangan."</td>\n\t\t\t\t<td align=center>".$isHariRaya."</td>\n\t\t\t\t   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' \n                                       onclick=\"fillField('".tanggalnormal($bar1->tanggal)."','".$bar1->keterangan."','".$bar1->ishariraya."');\"></td></tr>";
            }
            echo "\t \n\t\t </tbody>\n\t\t <tfoot>\n                  <tr><td colspan=5 align=center>\n                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\n                    <button class=mybutton onclick=loadData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\n                    <button class=mybutton onclick=loadData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\n                    </td>\n                    </tr>\n\t\t </tfoot>\n\t\t </table>";
        } else {
            echo '<tr class=rowcontent><td colspan=5>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
}

?>