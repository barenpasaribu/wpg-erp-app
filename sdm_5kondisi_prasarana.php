<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
echo '<script>pilih="';
echo $_SESSION['lang']['pilihdata'];
echo "\"</script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_5kondisi_prasarana.js'></script>\r\n";
$arr = '##kdSarana##tglKonSarana##kondId##idProgress##method##jmlhSarana';
include 'master_mainMenu.php';
OPEN_BOX();
$sJenis = 'select distinct jenis,nama from '.$dbname.'.sdm_5jenis_prasarana order by nama asc';
$qJenis = mysql_query($sJenis);
while ($rJenis = mysql_fetch_assoc($qJenis)) {
    $nmaJenis[$rJenis['jenis']] = $rJenis['nama'];
}
$optKlmpk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optJns = $optKlmpk;
$sKlmpk = 'select distinct kodeprasarana,jenisprasarana,lokasi from '.$dbname.".sdm_prasarana where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodeprasarana asc";
$qKlmpk = mysql_query($sKlmpk);
while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
    $optKlmpk .= "<option value='".$rKlmpk['kodeprasarana']."'>".$rKlmpk['kodeprasarana'].' - '.$nmaJenis[$rKlmpk['jenisprasarana']].' - '.$rKlmpk['lokasi'].'</option>';
}
$optsrana = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$arragama = getEnum($dbname, 'sdm_kondisi_prasarana', 'kondisi');
foreach ($arragama as $kei => $fal) {
    $optsrana .= "<option value='".$kei."'>".$fal.'</option>';
}
$optprogres = "<option value='0'>".$_SESSION['lang']['pilihdata'].'</option>';
$arrProgrs = [1 => $_SESSION['lang']['slsiPerbaikan'], 2 => $_SESSION['lang']['dlmPerbaikan']];
foreach ($arrProgrs as $kei => $fal) {
    $optprogres .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<fieldset style=width:350px;float:left;>\r\n     <legend>".$_SESSION['lang']['konPrasarana']."</legend>\r\n\t <table>\r\n\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodeabs'].' '.$_SESSION['lang']['prasarana']."</td>\r\n\t   <td><select id=kdSarana style=\"width:150px;\" onchange=getSatuan(0)>".$optKlmpk."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t   <td><input type=text class=myinputtext id=tglKonSarana onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\"  /></td>\r\n\t </tr> \r\n\t  <tr>\r\n\t   <td>".$_SESSION['lang']['jumlah']."</td>\r\n\t   <td><input type=text class=myinputtext id=jmlhSarana name=jmlhSarana onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 /><span id=satuan></span></td>\r\n\t </tr>\r\n           <tr>\r\n\t   <td>".$_SESSION['lang']['kondisi'].' '.$_SESSION['lang']['prasarana']."</td>\r\n\t   <td><select id=kondId style=\"width:150px;\">".$optsrana."</select></td>\r\n\t </tr>\r\n          <tr>\r\n\t   <td>".$_SESSION['lang']['progress']."</td>\r\n\t   <td><select id=idProgress style=\"width:150px;\">".$optprogres."</select></td>\r\n\t </tr>\r\n   \r\n\t </table>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_5kondisi_prasarana','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button><br /><br />B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-BD = Rusak Bisa Dipakai. R-TD = Rusak, Tidak Dipakai. \r\n     </fieldset><input type=hidden id=idData />";
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from '.$dbname.'.sdm_prasarana order by tahunperolehan,bulanperolehan desc';
$res = mysql_query($str);
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['kodeabs'].' '.$_SESSION['lang']['prasarana']."</td>\r\n\t   <td>".$_SESSION['lang']['jenis'].' '.$_SESSION['lang']['prasarana']."</td>\r\n           <td>".$_SESSION['lang']['lokasi']."</td>\r\n\t   <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t   <td>".$_SESSION['lang']['kondisi']."</td>\r\n           <td>".$_SESSION['lang']['progress']."</td>\r\n           <td>".$_SESSION['lang']['jumlah']."</td>\r\n           <td>".$_SESSION['lang']['satuan']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>