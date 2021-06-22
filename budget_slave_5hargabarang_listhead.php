<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$str = 'select regional, tahunbudget, sumberharga from '.$dbname.'.bgt_masterbarang group by regional, tahunbudget';
$res = mysql_query($str);
$no = 1;
while ($bar = mysql_fetch_object($res)) {
    echo "<tr class=rowcontent>\r\n        <td align=center>".$no."</td>\r\n\t<td align=center><label id=tahun2_".$no.'>'.$bar->tahunbudget."</label></td>\r\n\t<td align=center><label id=reg2_".$no.'>'.$bar->regional.'</label></td>';
    if ('AGR' !== $_SESSION['empl']['bagian']) {
        echo '<td align=center><button class=mybutton onclick=listHarga('.$no.')>'.$_SESSION['lang']['list']."</button></td>\r\n        <td align=center><button class=mybutton onclick=deleteHarga(".$bar->tahunbudget.",'".$bar->regional."')>".$_SESSION['lang']['delete']."</button></td>\r\n        <td align=center><button class=mybutton onclick=hargaKeExcel(event,".$no.")>Excel</button></td>\r\n\t<td align=center><button id=edit_".$no.' class=mybutton onclick=tampolkanHarga('.$bar->tahunbudget.",'".$bar->regional."')>".$_SESSION['lang']['edit']."</button></td>\r\n\t<td align=center><button disabled=true id=close_".$no.' class=mybutton onclick=TutupHarga(1,'.$no.')>'.$_SESSION['lang']['close'].'</button></td>';
    } else {
        echo '<td colspan=5>&nbsp;</td>';
    }

    echo '</tr>';
    ++$no;
}

?>