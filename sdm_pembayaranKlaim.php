<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/sdm_pengobatan.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
OPEN_BOX('', $_SESSION['lang']['pembayaranclaim']);
echo " Periode :<select id='periode'>";
for ($x = 0; $x <= 24; ++$x) {
    $t = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    echo "<option value='".date('Y-m', $t)."'>".date('m-Y', $t).'</option>';
}
echo "</select>\r\n          <button onclick=getDaftar() class=mybutton>".$_SESSION['lang']['proses'].'</button>';
echo '<div id=cont>';
if (isset($_GET['periode'])) {
    $str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a left join\r\n      ".$dbname.".sdm_5rs b on a.rs=b.id \r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n\t  on a.diagnosa=d.id\r\n\t  where  a.kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n                     and periode='".$_GET['periode']."'\r\n\t  order by a.updatetime desc, a.tanggal desc";
    $res = mysql_query($str);
    echo "<fieldset>\r\n\t  <legend>".$_SESSION['lang']['belumbayar']."</legend>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n\t  <thead>\r\n\t    <tr class=rowheader>\r\n\t\t<td width=50></td>\r\n\t\t  <td>No</td>\r\n\t\t  <td width=100>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t  <td width=50>".$_SESSION['lang']['periode']."</td>\r\n\t\t  <td width=30>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t  <td width=200>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t  <td width=150>".$_SESSION['lang']['rumahsakit']."</td>\r\n\t\t  <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n\t\t  <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>\r\n\t\t  <td>".$_SESSION['lang']['dibayar']."</td>\r\n\t\t  <td>".$_SESSION['lang']['tanggalbayar']."</td>\r\n\t\t  <td></td>\r\n\t\t</tr>\r\n\t  </thead>\r\n\t  <tbody id='container'>";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n\t   <td>";
        echo "&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>";
        echo '</td><td>'.$no."</td>\r\n\t\t  <td>".$bar->notransaksi."</td>\r\n\t\t  <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n\t\t  <td>".tanggalnormal($bar->tanggal)."</td>\r\n\t\t  <td>".$bar->namakaryawan."</td>\r\n\t\t  <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n\t\t  <td>".$bar->kodebiaya."</td>\r\n\t\t  <td align=right>".number_format($bar->totalklaim, 2, '.', ',')."</td>\r\n\t\t  <td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value=".$bar->totalklaim."\">\r\n\t\t                  <input type=text id=bayar".$no.' class=myinputtextnumber onkeypress="return angka_doang(event);" maxlength=12 onblur=change_number(this) size=12 value='.$bar->jlhbayar."></td>\r\n\t\t  <td align=right><input type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".date('d-m-Y')."'></td>\r\n\t\t  <td><img src='images/save.png' title='Save' class=resicon onclick=savePClaim('".$no."','".$bar->notransaksi."')></td>\r\n\t\t</tr>";
    }
    echo "</tbody>\r\n\t <tfoot>\r\n\t </tfoot>\r\n\t </table>\r\n\t </fieldset> \t \r\n\t ";
}

echo '</div>';
CLOSE_BOX();
echo close_body();

?>