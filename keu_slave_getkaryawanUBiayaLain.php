<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$str = 'select a.namakaryawan,a.karyawanid,b.tipe,a.subbagian from '.$dbname.".datakaryawan a\r\n     left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id\r\n     where lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n     and tipekaryawan=".$_POST['tipekaryawan']." and \r\n     (tanggalkeluar>'".$_POST['periode']."-01' or tanggalkeluar is NULL) \r\n     order by namakaryawan";
$res = mysql_query($str);
$stream = "<table class=sortable cellspacing=1 border=0>\r\n        <thead>\r\n        <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['nomor']."</td>\r\n        <td>".$_SESSION['lang']['karyawanid']."</td>    \r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td>".$_SESSION['lang']['subbagian']."</td>    \r\n        <td>".$_SESSION['lang']['biayalistrik']."</td> \r\n        <td>".$_SESSION['lang']['biayaair']."</td>\r\n        <td>".$_SESSION['lang']['biayaklinik']."</td>    \r\n        <td>".$_SESSION['lang']['biayasosial']."</td>\r\n        <td>".$_SESSION['lang']['manajemenperumahan']."</td> \r\n        <td>".$_SESSION['lang']['natura']."</td>     \r\n        <td>".$_SESSION['lang']['jms']."</td>    \r\n        <td>".$_SESSION['lang']['save']."</td>\r\n        <td></td>     \r\n        </tr>\r\n        </thead>\r\n        <tbody>";
$str1 = 'select * from '.$dbname.".keu_byunalocated where periode='".$_POST['periode']."' \r\n       and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res1 = mysql_query($str1);
$listrik = [];
$air = [];
$klinik = [];
$sosial = [];
while ($barx = mysql_fetch_object($res1)) {
    $listrik[$barx->karyawanid] = $barx->listrik;
    $air[$barx->karyawanid] = $barx->air;
    $klinik[$barx->karyawanid] = $barx->klinik;
    $sosial[$barx->karyawanid] = $barx->sosial;
    $perumahan[$barx->karyawanid] = $barx->perumahan;
    $natura[$barx->karyawanid] = $barx->natura;
    $jms[$barx->karyawanid] = $barx->jms;
    $post[$barx->karyawanid] = $barx->posting;
}
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $save = '<img id=save'.$no." title=\"Simpan\" class=\"dellicon\" onclick=\"simpanBy('".$no."','".$_POST['periode']."');\" src=\"images/save.png\">";
    if (0 !== $no % 2) {
        $align = 'left';
    } else {
        $align = 'right';
    }

    if (0 === $post[$bar->karyawanid] && (0 < $listrik[$bar->karyawanid] || 0 < $air[$bar->karyawanid] || 0 < $klinik[$bar->karyawanid] || 0 < $sosial[$bar->karyawanid])) {
        $img = "<img class=dellicon onclick=posting('".$no."','".$_POST['periode']."'); id=btn".$no." src='images/skyblue/posting.png'>";
        $style = '#DEDEDE';
    } else {
        if (1 === $post[$bar->karyawanid]) {
            $img = '<img id=btn'.$no." src='images/skyblue/posted.png'>";
            $style = '#DEDEDE';
            $save = '';
        } else {
            $style = '#FFFFFF';
            $img = '';
        }
    }

    $stream .= "<tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td id=karid".$no.'>'.$bar->karyawanid."</td>\r\n                <td id=namakaryawan".$no.'>'.$bar->namakaryawan."</td>    \r\n                <td>".$bar->tipe."</td>\r\n                <td id=subbagian".$no.'>'.$bar->subbagian."</td>    \r\n                <td><input type=text id=bylistrik".$no." style='background-color:".$style."' value='".$listrik[$bar->karyawanid]."' class=myinputtextnumber  size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td> \r\n                <td><input type=text id=byair".$no." style='background-color:".$style."'  value='".$air[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>\r\n                <td><input type=text id=byklinik".$no." style='background-color:".$style."'  value='".$klinik[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>   \r\n                <td><input type=text id=bysosial".$no." style='background-color:".$style."'  value='".$sosial[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>\r\n                <td><input type=text id=perumahan".$no." style='background-color:".$style."'  value='".$perumahan[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>\r\n                <td><input type=text id=natura".$no." disabled style='background-color:".$style."'  value='".$natura[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>\r\n                <td><input type=text id=jms".$no." style='background-color:".$style."'  value='".$jms[$bar->karyawanid]."' class=myinputtextnumber   size=10 maxlength=8 onkeypress=\"return angka_doang(event);\" onchange=\"this.style.backgroundColor='orange';\"></td>    \r\n                <td align=".$align.">\r\n                    ".$save."\r\n                </td>\r\n                <td align=center  id=cell".$no.'>'.$img."</td>\r\n            </tr>";
}
$stream .= "</tbody>\r\n          <tfoot></tfoot> \r\n          </table> \r\n        ";
echo $stream;

?>