<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['material'].' SPK</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/kebun_slave_pemakaianMaterialSPK.js\"></script>\r\n<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"  />\r\n<div id=\"headher\">\r\n";
$optKeg = "<option value=''>[ no SPK data ]</option>";
$optBlok = "<option value=''>[ no SPK data ]</option>";
echo "<fieldset style='float:left;'>\r\n    <legend>";
echo $_SESSION['lang']['form'];
echo "</legend>\r\n    <table cellspacing=\"1\" border=\"0\">\r\n       <tr>\r\n            <td>";
echo $_SESSION['lang']['nomor'];
echo " SPK</td><td>:</td>\r\n            <td>\r\n                <input type='text' class='myinputtext' id='nospk' onkeypress=\"return tanpa_kutip();\"  size='10' maxlength='30' style=\"width:150px;\" />\r\n                <button class=mybutton id='carispk' onclick=carispk()>";
echo $_SESSION['lang']['find'];
echo "</button>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td>";
echo $_SESSION['lang']['kegiatan'];
echo "</td><td>:</td>\r\n            <td><select id='kegiatan' style=\"width:200px\">";
echo $optKeg;
echo "</select></td>\r\n        </tr>\r\n        <tr>\r\n            <td>";
echo $_SESSION['lang']['blok'];
echo "</td><td>:</td>\r\n            <td><select id='blok' onchange=\"caritanggal();\" style=\"width:200px\">";
echo $optBlok;
echo "</select></td>\r\n        </tr>\r\n        <tr>\r\n            <td>";
echo $_SESSION['lang']['tanggal'];
echo "</td><td>:</td>\r\n            <td><input type='text' class='myinputtext' id='tanggal' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>\r\n        </tr>\r\n        <tr>\r\n            <td>";
echo $_SESSION['lang']['namabarang'];
echo "</td><td>:</td>\r\n            <td>\r\n                <input type='text' class='myinputtext' id='namabarang' onkeyup ='resetkobar();' onkeypress=\"return tanpa_kutip();\"  size='10' maxlength='30' style=\"width:150px;\" />\r\n                <input type='hidden' id='kodebarang' name='kodebarang' />\r\n                ";
echo "<input type='image' id=search1 src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg'].'</legend>Find<input type=text class=myinputtext id=no_brg value='.$namabarang.'><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value='.$key.">',event)\";>";
echo "            </td>\r\n        </tr>\r\n        <tr>\r\n            <td>";
echo $_SESSION['lang']['jumlah'];
echo "</td><td>:</td>\r\n            <td>\r\n                <input type='text' class='myinputtext' id='jumlah' onkeypress=\"angka_doang(event);\"  size='10' maxlength='30' style=\"width:150px;\" />\r\n                <label id='satuan'>\r\n            </td>\r\n        </tr>\r\n\r\n        <tr>\r\n        <td colspan=\"3\" id=\"tmblHeader\">\r\n            <button class=mybutton id=dtlForm onclick=saveForm()>";
echo $_SESSION['lang']['save'];
echo "</button>\r\n            <button class=mybutton id=cancelForm onclick=cancelForm()>";
echo $_SESSION['lang']['cancel'];
echo "</button>\r\n        </td>\r\n        </tr>\r\n    </table><input type=\"hidden\" id=\"hiddenz\" name=\"hiddenz\" />\r\n</fieldset>\r\n\r\n";
CLOSE_BOX();
echo "</div>\r\n<div id=\"list_ganti\">\r\n";
OPEN_BOX();
echo "<div id=\"action_list\">\r\n\r\n</div>\r\n<fieldset style='float:left;'>\r\n    <legend>";
echo $_SESSION['lang']['list'];
echo "</legend>\r\n    <table cellspacing=\"1\" border=\"0\" class=\"sortable\">\r\n        <thead>\r\n            <tr class=\"rowheader\">\r\n            <td>No.</td>\r\n            <td>";
echo $_SESSION['lang']['nomor'].' SPK';
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['kegiatan'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['kodeblok'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['tanggal'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['namabarang'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['jumlah'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['satuan'];
echo "</td>\r\n            <td>";
echo $_SESSION['lang']['action'];
echo "</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody id=\"contain\">\r\n        ";
$kamusbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$kamussatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$kamuskegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$limit = 10;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from '.$dbname.'.log_baspk_material order by `notransaksi` desc';
$query2 = mysql_query($ql2) ;
while ($jsl = mysql_fetch_object($query2)) {
    $jlhbrs = $jsl->jmlhrow;
}
$slvhc = 'select * from '.$dbname.'.log_baspk_material order by `notransaksi` desc,`kodekegiatan`,`blok`,`tanggal`,`kodebarang` limit '.$offset.','.$limit.' ';
$qlvhc = mysql_query($slvhc) ;
$user_online = $_SESSION['standard']['userid'];
while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
    ++$no;
    echo "        <tr class=\"rowcontent\">\r\n        <td>";
    echo $no;
    echo "</td>\r\n        <td>";
    echo $rlvhc['notransaksi'];
    echo "</td>\r\n        <td>";
    echo $kamuskegiatan[$rlvhc['kodekegiatan']];
    echo "</td>\r\n        <td>";
    echo $rlvhc['blok'];
    echo "</td>\r\n        <td>";
    echo $rlvhc['tanggal'];
    echo "</td>\r\n        <td>";
    echo $kamusbarang[$rlvhc['kodebarang']];
    echo "</td>\r\n        <td align=\"right\">";
    echo $rlvhc['jumlah'];
    echo "</td>\r\n        <td>";
    echo $kamussatuan[$rlvhc['kodebarang']];
    echo "</td>\r\n\r\n        ";
    echo "<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['notransaksi']."','".$rlvhc['kodekegiatan']."','".$rlvhc['blok']."','".$rlvhc['tanggal']."','".$rlvhc['kodebarang']."');\" ></td>";
    echo "        </tr>\r\n\r\n        ";
}
echo "<tr class=rowheader><td colspan=9 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
echo "\r\n        </tbody>\r\n    </table>\r\n</fieldset>\r\n";
CLOSE_BOX();
echo "</div>\r\n";
echo close_body();

?>