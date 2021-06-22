<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['prevmain'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"application/javascript\" src=\"js/sdm_preventivemaintenance.js\"></script>\r\n<script>\r\ntombolsimpan='";
echo $_SESSION['lang']['save'];
echo "';\r\ntombolbatal='";
echo $_SESSION['lang']['cancel'];
echo "';\r\ntomboldone='";
echo $_SESSION['lang']['selesai'];
echo "';\r\n\r\n</script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\" />\r\n<div id=\"action_list\">\r\n";
echo "<table>\r\n    <tr valign=middle>\r\n    <td align=center style='width:100px;cursor:pointer;' onclick=tambahdata()>\r\n        <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n    <td align=center style='width:100px;cursor:pointer;' onclick=tampildata()>\r\n        <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n    <td align=center style='width:100px;cursor:pointer;' onclick=overdueData()>\r\n        <img class=delliconBig src=images/book_icon.gif title='Over Due'><br>Over Due List</td>";
echo "</tr>\r\n</table></div>\r\n";
CLOSE_BOX();
echo "\r\n<div id=\"listdata\">\r\n<script>tampildata();</script>\r\n</div>\r\n\r\n<div id=\"header\" style=\"display:none\">    \r\n";
OPEN_BOX();
$optjenis = '';
$arrjenis = getEnum($dbname, 'schedulerht', 'jenis');
foreach ($arrjenis as $kei => $fal) {
    $optjenis .= "<option value='".$kei."'>".$fal.'</option>';
}
$optsatuan = "<option value='HM'>HM</option><option value='KM'>KM</option>";
echo "<fieldset>\r\n<legend>";
echo $_SESSION['lang']['header'];
echo "</legend>\r\n<table cellspacing=\"1\" border=\"0\">\r\n<tr>\r\n<td style=\"width:100px;\">";
echo $_SESSION['lang']['jenis'];
echo "</td>\r\n<td>:<input type=\"hidden\" id=\"id\" name=\"id\" value=\"\"></td>\r\n<td><select id=\"jenis\" name=\"jenis\" style=\"width:150px;\" onchange=\"loadkodemesin()\"><option value=\"\">";
echo $_SESSION['lang']['pilihdata'];
echo '</option>';
echo $optjenis;
echo "</select></td>\r\n<td rowspan=\"10\" valign=\"top\"><fieldset style=\"width:350px\"><legend>Contoh:</legend>\r\n    Untuk ganti oli kendaraan, akan diganti oli setiap 5000Km, \r\n    maka cara pengisian adalah:jenis=TRAKSI,Nama Mesin =[Pilih Kode Mesin/Kendaraan],Satuan=KM,Batas Atas=5000,\r\n    Peringatan Setiap=4500.\r\n    <p>Untuk reminder akhir kontrak karyawan: jenis=UMUM,Nama Mesin =[],Satuan=[],Batas Atas=[],Peringatan Setiap=[],\r\n    setiap tanggal=[pilih tanggal], Perulangan=tidak.</p></fieldset>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['nmmesin'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"mesin\" name=\"mesin\" style=\"width:300px;\"><option value=\"\">";
echo $_SESSION['lang']['pilihdata'];
echo "</option></select></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['satuan'];
echo "</td>\r\n<td>:</td>\r\n<td><select id=\"satuan\" name=\"satuan\" style=\"width:150px;\"><option value=\"\">";
echo $_SESSION['lang']['pilihdata'];
echo '</option>';
echo $optsatuan;
echo "</select></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['resethmkm'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtextnumber\" id=\"resetHmkm\" name=\"resetHmkm\" onkeypress=\"return angka_doang(event);\" value=\"0\" maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['batasatas'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtextnumber\" id=\"atas\" name=\"atas\" onkeypress=\"return angka_doang(event);\" value=\"0\" maxlength=\"10\" style=\"width:150px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['peringatansetiap'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtextnumber\" id=\"peringatan\" name=\"peringatan\" onblur=\"cekperingatan();\" onkeypress=\"return angka_doang(event);\" value=\"0\" maxlength=\"10\" style=\"width:150px;\" /> ";
echo '('.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['batasatas'].')';
echo "</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['setiap'].' '.$_SESSION['lang']['tanggal'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"tanggal\" name=\"tanggal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:150px;\" disabled=\"true\"/>(Jika batas atas tidak 0 maka tanggal diabaikan)</td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['perulangan'];
echo "</td>\r\n<td>:<input type=\"hidden\" id=\"id2\" name=\"id2\" value=\"\"></td>\r\n<td><select id=\"sekali\" name=\"sekali\" style=\"width:150px;\" >\r\n        <option value=\"1\">Ya</option><option value=\"2\">Tidak</option></select></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['namatugas'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"tugas\" name=\"tugas\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"45\" style=\"width:300px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['keterangan'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"keterangan\" name=\"keterangan\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"90\" style=\"width:300px;\" /></td>\r\n</tr>\r\n<tr>\r\n<td style=\"width:150px;\">";
echo $_SESSION['lang']['email'];
echo "</td>\r\n<td>:</td>\r\n<td><input type=\"text\" class=\"myinputtext\" id=\"email\" name=\"email\" onkeypress=\"return tanpa_kutip(event);\" maxlength=\"500\" style=\"width:300px;\" /> (separate with comma)</td>\r\n</tr>\r\n<tr>\r\n <td></td><td></td><td colspan=\"2\" id=\"tombolsave\">\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"4\">\r\n<div id=\"detailtable2\" style=\"display:none\">\r\n    <table cellspacing=\"1\" border=\"0\">\r\n        <thead>\r\n        <tr>\r\n        <td colspan=\"2\" align=\"center\">";
echo $_SESSION['lang']['kodebarang'];
echo "</td>\r\n        <td align=\"center\">";
echo $_SESSION['lang']['namabarang'];
echo "</td>\r\n        <td align=\"center\">";
echo $_SESSION['lang']['satuan'];
echo "</td>\r\n        <td align=\"center\">";
echo $_SESSION['lang']['jumlah'];
echo "</td>\r\n        <td align=\"center\">";
echo $_SESSION['lang']['action'];
echo "</td>\r\n        </thead>\r\n    <tbody id=\"detailisi\">\r\n    </tbody>\r\n    <tfoot>\r\n    <tr><td colspan=\"6\" align=\"center\">\r\n        <div id=\"tombolselesai\">\r\n    </td></tr>\r\n    </tfoot>\r\n    </table>\r\n</div>\r\n            \r\n</td> \r\n</tr>\r\n</table>\r\n</fieldset>\r\n    \r\n     \r\n";
CLOSE_BOX();
echo "</div>\r\n\r\n";
echo close_body();

?>