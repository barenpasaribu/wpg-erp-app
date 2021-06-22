<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo " \r\n<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/sdm_daftartenagakerja.js'></script> \r\n<link rel=stylesheet type='text/css' href='style/zTable.css'>\r\n";
$arr = '##notransaksi##kodeorg##penempatan##departemen##tanggal##tgldibutuhkan##kotapenempatan##pendidikan##jurusan##pengalaman##kompetensi##deskpekerjaan##maxumur##persetujuan1##persetujuan2##persetujuanhrd##proses';
include 'master_mainMenu.php';
OPEN_BOX();
$optthn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sperd = 'select distinct left(tanggal,4) as thn from '.$dbname.'.sdm_permintaansdm order by tanggal desc';
$qperd = mysql_query($sperd);
while ($rperd = mysql_fetch_assoc($qperd)) {
    $optthn .= "<option value='".$rperd['thn']."'>".$rperd['thn'].'</option>';
}
echo "<fieldset style=float:left><legend>Sort Data</legend>\r\n    ".$_SESSION['lang']['tahun'].' : <select id=thnPeriode onchange=loadData(0)>'.$optthn."</select></fieldset>\r\n        <fieldset style=float:left>\r\n        <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>\r\n         Tanggal Confirm Merupakan Tanggal Akhir display lowongan pada website karir.eagro.id. \r\n        </fieldset>\t\t \r\n\r\n    <div style='clear:both'></div>    \r\n    <fieldset style=float:left><legend><b>".$_SESSION['lang']['list']."</b></legend><div id=containerData>\r\n         <script>loadData()</script>\r\n         </div>\r\n         </filedset>";
CLOSE_BOX();
echo close_body();

?>