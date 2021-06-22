<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/log_persetujuan_po.js\"></script>\r\n<div id=\"action_list\">\r\n";
echo "<table>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=refresh_data()>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['carinopo'].'</legend>';
echo $_SESSION['lang']['nopo'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;';
echo $_SESSION['lang']['tgl_po'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariNopo()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> </div>\r\n";
CLOSE_BOX();
echo "<div id=list_pp_verication>\r\n";
OPEN_BOX();
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['list_po'];
echo "</legend>\r\n<div style=\"overflow:scroll; height:420px;\">\r\n\t <table class=\"sortable\" cellspacing=\"1\" border=\"0\">\r\n\t <thead>\r\n\t <tr class=rowheader>\r\n\t <td>No.</td>\r\n\t <td>";
echo $_SESSION['lang']['nopo'];
echo "</td>\r\n\t <td>";
echo $_SESSION['lang']['tgl_po'];
echo "</td> \r\n\t <td>";
echo $_SESSION['lang']['namaorganisasi'];
echo "</td>\r\n\t  <td>Detail PO</td>\r\n\t   <td colspan=\"2\" align=\"center\">Verification</td>\r\n\t  ";
for ($i = 1; $i < 4; ++$i) {
    echo '<td align=center>Persetujuan'.$i.'</td>';
}
echo "\t\r\n\t </tr>\r\n\t </thead>\r\n\t <tbody id=\"contain\">\r\n\t\r\n\t ";
$userid = $_SESSION['standard']['userid'];
$str = 'select * from '.$dbname.".log_poht\r\n         where stat_release<1 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))\r\n         or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))\r\n         or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3='')))";
if ($res = mysql_query($str)) {
    while ($bar = mysql_fetch_assoc($res)) {
        $kodeorg = $bar['kodeorg'];
        $spr = 'select * from  '.$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'";
        $rep = mysql_query($spr);
        $bas = mysql_fetch_object($rep);
        ++$no;
        echo "<tr class=rowcontent id='tr_".$no."'>\r\n\t\t\t\t  <td>".$no."</td>\r\n\t\t\t\t  <td id=td_".$no.'>'.$bar['nopo']."</td>\r\n\t\t\t\t  <td>".tanggalnormal($bar['tanggal'])."</td>\r\n\t\t\t\t  <td>".$bas->namaorganisasi."</td>\r\n\t\t\t\t  <td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_log_po',event);\"></td>";
        for ($a = 1; $a < 4; ++$a) {
            if ('' !== $bar['persetujuan'.$a]) {
                if ($bar['persetujuan'.$a] === $_SESSION['standard']['userid'] && '' !== $bar['hasilpersetujuan'.$a]) {
                    echo "\r\n                                                <td><button class=mybutton disabled onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>\r\n                                                <td><button class=mybutton disabled onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>\r\n                                                ";
                } else {
                    if ($bar['persetujuan'.$a] === $_SESSION['standard']['userid'] && '' === $bar['hasilpersetujuan'.$a]) {
                        echo "\r\n                                                <td><button class=mybutton onclick=\"get_data_po('".$bar['nopo']."')\">".$_SESSION['lang']['approve']."</button></td>\r\n                                                <td><button class=mybutton onclick=rejected_po('".$bar['nopo']."') >".$_SESSION['lang']['ditolak']."</button></td>\r\n                                                </td>";
                    }
                }
            }
        }
        for ($i = 1; $i < 4; ++$i) {
            if ('' !== $bar['persetujuan'.$i]) {
                $kr = $bar['persetujuan'.$i];
                $sql = 'select * from '.$dbname.".datakaryawan where karyawanid='".$kr."'";
                $query = mysql_query($sql);
                $yrs = mysql_fetch_assoc($query);
                if ('' === $bar['hasilpersetujuan'.$i]) {
                    $b = 'Belum Ada Keputusan ';
                } else {
                    if ('1' === $bar['hasilpersetujuan'.$i]) {
                        $b = $_SESSION['lang']['approve'];
                    } else {
                        if ('3' === $bar['hasilpersetujuan'.$i]) {
                            $b = $_SESSION['lang']['ditolak'];
                        }
                    }
                }

                echo '<td align=center>'.$yrs['namakaryawan'].'<br />('.$b.')</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
        }
        echo '</tr><input type=hidden id=nopo_'.$no.' name=nopo_'.$no." value='".$bar['nopo']."' />";
    }
} else {
    echo ' Gagal,'.mysql_error($conn);
}

echo "\t  </tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table></div>\r\n</fieldset\r\n>";
CLOSE_BOX();
echo "</div>\r\n<input type=\"hidden\" name=\"method\" id=\"method\"  /> \r\n<input type=\"hidden\" id=\"no_po\" name=\"no_po\" />\r\n<input type=\"hidden\" name=\"user_login\" id=\"user_login\" value=\"";
echo $_SESSION['standard']['userid'];
echo "\" />\r\n";
echo close_body();

?>