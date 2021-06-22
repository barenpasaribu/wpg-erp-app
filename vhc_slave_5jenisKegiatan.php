<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$regional = $_POST['regional'];
$kdkegiatan = $_POST['kdkegiatan'];
$rp = $_POST['rp'];
$insen = $_POST['insen'];
$konversi = $_POST['konversi'];
$method = $_POST['method'];
$nmkonv = [$_SESSION['lang']['no'], $_SESSION['lang']['yes']];
if ('ID' == $_SESSION['language']) {
    $fld = 'namaakun';
} else {
    $fld = 'namaakun1';
}

$nmen = makeOption($dbname, 'keu_5akun', 'noakun,'.$fld.'');
echo "\r\n";
switch ($method) {
    case 'insert':
        if ('' == $kdkegiatan) {
            exit('error: '.$_SESSION['lang']['kodekegiatan'].' is empty!');
        }

        if ('' == $_POST['nmKegiatan']) {
            exit('error: '.$_SESSION['lang']['namakegiatan'].' is empty!');
        }

        if ('' == $_POST['satuan']) {
            exit('error: '.$_SESSION['lang']['satuan'].' is empty!');
        }

        $whr = "kodekegiatan='".$kdkegiatan."'";
        $nmen = makeOption($dbname, 'vhc_kegiatan', 'kodekegiatan,namakegiatan', $whr);
        if ('' == $nmen[$kdkegiatan]) {
            $i = 'insert into '.$dbname.".vhc_kegiatan (regional,kodekegiatan,namakegiatan,satuan,noakun,basis,hargasatuan,hargaslebihbasis\r\n                         ,hargaminggu,updateby,auto)\r\n                    values ('".$regional."','".$kdkegiatan."','".$_POST['nmKegiatan']."','".$_POST['satuan']."','".$_POST['noakun']."','".$_POST['basis']."'\r\n                            ,'".$_POST['hrgSatuan']."','".$_POST['hrgLbhBasis']."','".$_POST['hrgHrMngg']."','".$_SESSION['standard']['userid']."','".$_POST['auto']."')";
            if (mysql_query($i)) {
                echo '';
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }

            break;
        }

        exit('error: Data already register');
    case 'update':
        if ('' == $_POST['nmKegiatan']) {
            exit('error: '.$_SESSION['lang']['namakegiatan'].' is empty!');
        }

        if ('' == $_POST['satuan']) {
            exit('error: '.$_SESSION['lang']['satuan'].' is empty!');
        }

        $i = 'update '.$dbname.".vhc_kegiatan set namakegiatan='".$_POST['nmKegiatan']."',satuan='".$_POST['satuan']."',noakun='".$_POST['noakun']."'\r\n                    ,basis='".$_POST['basis']."',hargasatuan='".$_POST['hrgSatuan']."',hargaslebihbasis='".$_POST['hrgLbhBasis']."'\r\n                    ,updateby='".$_SESSION['standard']['userid']."',auto='".$_POST['auto']."',hargaminggu='".$_POST['hrgHrMngg']."'\r\n\t\t where kodekegiatan='".$kdkegiatan."' ";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['namakegiatan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['basis']."</td>\r\n                                 <td align=center>".$_SESSION['lang']['hargasatuan']."</td>\r\n                                 <td align=center>".$_SESSION['lang']['hargalbhbasis']."</td>\r\n                                 <td align=center>".$_SESSION['lang']['hargaHariMinggu']."</td>\r\n                                 <td align=center>".$_SESSION['lang']['isiauto']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('' != $_POST['nmKegiatanCr']) {
            $whr .= " and namakegiatan like '%".$_POST['nmKegiatanCr']."%'";
        }

        if ('' != $_POST['noakunCr']) {
            $whr .= " and noakun='".$_POST['noakunCr']."'";
        }

        if ('' != $_POST['autoCr']) {
            $whr .= " and auto='".$_POST['autoCr']."'";
        }

        $limit = 15;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".vhc_kegiatan  \r\n                       where regional='".$_SESSION['empl']['regional']."' ".$whr.' order by kodekegiatan asc ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".vhc_kegiatan \r\n                    where regional='".$_SESSION['empl']['regional']."' ".$whr." \r\n                     order by kodekegiatan asc  limit ".$offset.','.$limit.'';
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['regional'].'</td>';
            echo '<td>'.$d['kodekegiatan'].'</td>';
            echo '<td align=left>'.$d['namakegiatan'].'</td>';
            echo '<td>'.$d['satuan'].'</td>';
            echo '<td>'.$d['noakun'].' - '.$nmen[$d['noakun']].'</td>';
            echo '<td align=right>'.$d['basis'].'</td>';
            echo '<td align=right>'.number_format($d['hargasatuan'], 2).'</td>';
            echo '<td align=right>'.number_format($d['hargaslebihbasis'], 2).'</td>';
            echo '<td align=right>'.number_format($d['hargaminggu'], 2).'</td>';
            echo '<td align=right>'.$d['auto'].'</td>';
            echo "<td align=center>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['kodekegiatan']."','".$d['namakegiatan']."','".$d['satuan']."','".$d['noakun']."','".$d['basis']."','".$d['hargaslebihbasis']."','".$d['hargaminggu']."','".$d['auto']."','".$d['hargasatuan']."');\">";
            echo '</tr>';
        }
        echo "</tbody><tfoot>\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=loadData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=loadData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tfoot></table>';

        break;
    case 'upGradeData':
        $sData = 'select * from '.$dbname.".vhc_kegiatan where regional='".$_SESSION['empl']['regional']."'";
        $qData = mysql_query($sData);
        if (0 == mysql_num_rows($qData)) {
            exit('error:Data Kosong');
        }

        while ($rData = mysql_fetch_assoc($qData)) {
            $basis = $rData['basis'] + ($rData['basis'] * $_POST['bsisPrsn']) / 100;
            $hrgsat = $rData['hargasatuan'] + ($rData['hargasatuan'] * $_POST['hrgStnPrsn']) / 100;
            $hrgLbh = $rData['hargaslebihbasis'] + ($rData['hargaslebihbasis'] * $_POST['hrgLbhBsisPrsn']) / 100;
            $hrgming = $rData['hargaminggu'] + ($rData['hargaminggu'] * $_POST['hrgMnggPrsn']) / 100;
            $supdate = 'update '.$dbname.".vhc_kegiatan set basis='".$basis."',hargasatuan='".$hrgsat."'\r\n                          ,hargaslebihbasis='".$hrgLbh."',hargaminggu='".$hrgming."',updateby='".$_SESSION['standard']['userid']."'\r\n                          where regional='".$_SESSION['empl']['regional']."'";
            if (!mysql_query($supdate)) {
                exit('error: db gagal '.mysql_error($conn).'__'.$supdate);
            }
        }

        break;
}
echo "\r\n";

?>