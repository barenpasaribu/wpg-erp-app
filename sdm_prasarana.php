<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script>pilih="';
echo $_SESSION['lang']['pilihdata'];
echo "\"</script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_prasarana.js'></script>\r\n";
$arr = '##kdOrg##idKlmpk##idJenis##idLokasi##jmlhSarana##method##thnPerolehan##blnPerolehan##statFr##idData';
include 'master_mainMenu.php';
OPEN_BOX();
$optKlmpk = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optJns = $optKlmpk;
$sKlmpk = 'select distinct * from '.$dbname.'.sdm_5kl_prasarana order by kode asc';
$qKlmpk = mysql_query($sKlmpk);
while ($rKlmpk = mysql_fetch_assoc($qKlmpk)) {
    $orgNmKlmpk[$rKlmpk['kode']] = $rKlmpk['nama'];
    $optKlmpk .= "<option value='".$rKlmpk['kode']."'>".$rKlmpk['nama'].'</option>';
}
$optKlmpk2 = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optOrg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sOrg = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='6' order by namaorganisasi asc";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $optOrg .= "<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi'].'</option>';
}
echo "<fieldset style=width:350px;float:left;>\r\n     <legend>".$_SESSION['lang']['prasarana']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t   <td><input type=text class=myinputtext id=kdOrg name=kdOrg onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" disabled value='".$_SESSION['empl']['lokasitugas']."' /></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kodekelompok']."</td>\r\n\t   <td><select id=idKlmpk style=\"width:150px;\" onchange=getJenis(0,0)>".$optKlmpk."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['jenis']."</td>\r\n\t   <td><select id=idJenis style=\"width:150px;\" onchange=getSatuan(0)>".$optKlmpk2."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['lokasi']."</td>\r\n\t   <td><select id=idLokasi style=\"width:150px;\">".$optOrg."</select></td>\r\n\t </tr>\t \r\n\t  <tr>\r\n\t   <td>".$_SESSION['lang']['jumlah']."</td>\r\n\t   <td><input type=text class=myinputtext id=jmlhSarana name=jmlhSarana onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20 /><span id=satuan></span></td>\r\n\t </tr>\r\n          <tr>\r\n\t   <td>".$_SESSION['lang']['tahunperolehan']."</td>\r\n\t   <td><input type=text class=myinputtext id=thnPerolehan name=thnPerolehan onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>\r\n\t </tr>\r\n          <tr>\r\n\t   <td>".$_SESSION['lang']['blnperolehan']."</td>\r\n\t   <td><input type=text class=myinputtext id=blnPerolehan name=blnPerolehan onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=2 /></td>\r\n\t </tr>\r\n         <tr>\r\n\t   <td>".$_SESSION['lang']['status']."</td>\r\n\t   <td><input type='checkbox' id=statFr name=statFr /> Tidak Aktif</td>\r\n\t </tr> \r\n\t </table>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton onclick=saveFranco('sdm_slave_prasarana','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type=hidden id=idData />";
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from '.$dbname.'.sdm_prasarana order by tahunperolehan,bulanperolehan desc';
$res = mysql_query($str);
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['kodeorg']."</td>\r\n\t   <td>".$_SESSION['lang']['kodekelompok']."</td>\r\n\t   <td>".$_SESSION['lang']['jenis']."</td>\r\n\t   <td>".$_SESSION['lang']['lokasi']."</td>\r\n\t   <td>".$_SESSION['lang']['jumlah']."</td>\r\n           <td>".$_SESSION['lang']['tahunperolehan']."</td>\r\n           <td>".$_SESSION['lang']['blnperolehan']."</td>\r\n           <td>".$_SESSION['lang']['status']."</td>\r\n\t   <td>Action</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n\t </tfoot>\r\n\t </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>