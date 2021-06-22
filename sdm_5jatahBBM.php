<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src=js/sdm_jatahBBM.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['jatahbbm']);
$str = 'select a.karyawanid, a.namakaryawan,a.kodegolongan,b.jatah from '.$dbname.".datakaryawan a\r\n      left join ".$dbname.".sdm_5jatahbbm b\r\n\t  on a.karyawanid=b.karyawanid\r\n\t  where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
echo mysql_error($conn);
echo "<table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t   <tr class=rowheader>\r\n\t   <td>No.</td>\r\n\t   <td>nama</td>\r\n\t   <td>Golongan</td>\r\n\t   <td>jatah</td>\r\n\t   <td></td>\r\n\t   </tr>\r\n\t </thead>\t  \r\n\t <tbody>";
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t     <td>".$no."</td>\r\n\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t <td>".$bar->kodegolongan."</td>\r\n\t\t <td><input type=text class=myinputtextnumber id='".$bar->karyawanid."' value='".$bar->jatah."' maxlength=5 onkeypress=\"return angka_doang(event);\" size=8>Ltr.</td>\r\n\t\t <td><img src=images/save.png class=resicon title='save' onclick=saveJatah('".$bar->karyawanid."')></td>\r\n\t\t </tr>";
}
echo "</tbody>\r\n      <tfoot>\r\n\t  </tfoot>\r\n\t  </table>";
CLOSE_BOX();
echo close_body();

?>