<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript1.2 src='js/sdm_stdTunjangan.js'></script>\r\n";
$str = 'select * from '.$dbname.'.sdm_5jabatan order by namajabatan';
$res = mysql_query($str);
$optjab = '';
while ($bar = mysql_fetch_object($res)) {
    $optjab .= "<option value='".$bar->kodejabatan."'>".$bar->namajabatan.'</option>';
}
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['stdtunjangan'].'</legend>';
echo "<table>\r\n      <tr>\r\n       <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n           <td>:<select id=kodejabatan>".$optjab."</select></td></tr>\r\n       <td>".$_SESSION['lang']['lokasi']."</td><td>:<select id=lokasi><option value='LOKASI'>LOKASI</option><option value='KOTA'>KOTA</option></select></td></tr>\r\n       <td>Tunj.Jabatan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjjabatan size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr> \r\n       <td>Tunj.Staff Kota</td><td>:Rp.<input type=text class=myinputtextnumber id=tjkota size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  \r\n       <td>Tunj.Transport</td><td>:Rp.<input type=text class=myinputtextnumber id=tjtransport size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  \r\n       <td>Tunj.Makan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjmakan size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  \r\n       <td>Tunj.Staff Daerah</td><td>:Rp.<input type=text class=myinputtextnumber id=tjsdaerah size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  \r\n       <td>Tunj.Kemahalan</td><td>:Rp.<input type=text class=myinputtextnumber id=tjmahal size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>  \r\n       <td>Tunj.Pembantu</td><td>:Rp.<input type=text class=myinputtextnumber id=tjpembantu size=20 maxlength=15 onkeypress=\"return angka_doang(event);\"></td></tr>        \r\n      </tr>\r\n      </table>\r\n      <button class=mybutton onclick=simpanStdjabatan()>".$_SESSION['lang']['save']."</button>\r\n      <button class=mybutton onclick=cancelStdTun()>".$_SESSION['lang']['clear']."</button>\r\n      ";
echo '</fieldset>';
echo '<fieldset><legend>'.$_SESSION['lang']['list'].'</legend>';
echo "<table class=sortable border=0 cellspacing=1>\r\n            <thead>\r\n              <tr class=rowheader>\r\n               <td>".$_SESSION['lang']['nomor']."</td>\r\n               <td>".$_SESSION['lang']['kodejabatan']."</td>\r\n               <td>".$_SESSION['lang']['lokasi']."</td>\r\n               <td>Tunj.Jabatan</td> \r\n               <td>Tunj.Staff Kota</td> \r\n               <td>Tunj.Transport</td> \r\n               <td>Tunj.Makan</td> \r\n               <td>Tunj.Staff Daerah</td> \r\n               <td>Tunj.Kemahalan</td> \r\n               <td>Tunj.Pembantu</td> \r\n               <td>".$_SESSION['lang']['aksi']."</td>\r\n              </tr>\r\n            </thead>\r\n            <tbody id=container>";
$str = 'select a.*,b.namajabatan from '.$dbname.'.sdm_5stdtunjangan a left join '.$dbname.'.sdm_5jabatan b on a.jabatan=b.kodejabatan order by penempatan,jabatan';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n          <td>".$no."</td>\r\n          <td>".$bar->namajabatan."</td>\r\n          <td>".$bar->penempatan."</td>\r\n          <td>".$bar->tjjabatan."</td>\r\n          <td>".$bar->tjkota."</td>\r\n          <td>".$bar->tjtransport."</td>\r\n          <td>".$bar->tjmakan."</td>\r\n          <td>".$bar->tjsdaerah."</td>\r\n          <td>".$bar->tjmahal."</td>\r\n          <td>".$bar->tjpembantu."</td>\r\n          <td><img class='resicon' onclick=\"fillField('".$bar->jabatan."','".$bar->penempatan."','".$bar->tjjabatan."','".$bar->tjkota."','".$bar->tjtransport."','".$bar->tjmakan."','".$bar->tjsdaerah."','".$bar->tjmahal."','".$bar->tjpembantu."');\" title='Edit' src='images/application/application_edit.png'></td>\r\n          </tr>";
}
echo '</tbody><tfoot></tfoot></table></fieldset>';
CLOSE_BOX();
echo close_body();

?>