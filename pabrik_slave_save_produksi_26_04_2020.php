<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$kodeorg = $_POST['kodeorg'];
$tanggal = tanggalsystem($_POST['tanggal']);
$sisatbskemarin = $_POST['sisatbskemarin'];
$tbsmasuk = $_POST['tbsmasuk'];
$tbsdiolah = $_POST['tbsdiolah'];
$sisahariini = $_POST['sisahariini'];
$oer = $_POST['oer'];
$kadarair = $_POST['kadarair'];
$ffa = $_POST['ffa'];
$dirt = $_POST['dirt'];
$oerpk = $_POST['oerpk'];
$kadarairpk = $_POST['kadarairpk'];
$ffapk = $_POST['ffapk'];
$dirtpk = $_POST['dirtpk'];
$intipecah = $_POST['intipecah'];
$usbbefore = $_POST['usbbefore'];
$usbafter = $_POST['usbafter'];
$oildiluted = $_POST['oildiluted'];
$oilin = $_POST['oilin'];
$oilinheavy = $_POST['oilinheavy'];
$caco = $_POST['caco'];
$fruitineb = $_POST['fruitineb'];
$ebstalk = $_POST['ebstalk'];
$fibre = $_POST['fibre'];
$nut = $_POST['nut'];
$effluent = $_POST['effluent'];
$soliddecanter = $_POST['soliddecanter'];
$fruitinebker = $_POST['fruitinebker'];
$cyclone = $_POST['cyclone'];
$claybath = $_POST['claybath'];
$ltds = $_POST['ltds'];
$ltds2 = $_POST['ltds2'];
if (isset($_POST['del'])) {
    $strx = 'delete from '.$dbname.".pabrik_produksi \r\n\t\t\t       where kodeorg='".$kodeorg."' \r\n\t\t\t\t   and tanggal='".$_POST['tanggal']."'";
} else {
    $strx = 'insert into '.$dbname.".pabrik_produksi\r\n                   (kodeorg,tanggal,sisatbskemarin,\r\n\t\t\t\t    tbsmasuk,tbsdiolah,sisahariini,\r\n\t\t\t\t    oer,ffa,kadarair,kadarkotoran,\r\n\t\t\t\t\toerpk,ffapk,kadarairpk,kadarkotoranpk,\r\n\t\t\t\t\tkaryawanid,fruitineb, ebstalk, fibre, nut, \r\n                                        effluent, soliddecanter, fruitinebker, cyclone, \r\n                                        ltds,ltds1,claybath, usbbefore, usbafter, oildiluted, oilin, \r\n                                        oilinheavy, caco,intipecah,dobi,batu,cangkang, ffa_cangkang, kadarair_cangkang, kotoran_cangkang)\r\n\t\t\t\t\tvalues('".$kodeorg."',".$tanggal.','.$sisatbskemarin.",\r\n\t\t\t\t\t".$tbsmasuk.','.$tbsdiolah.','.$sisahariini.",\r\n\t\t\t\t\t".$oer.','.$ffa.','.$kadarair.','.$dirt.",\r\n\t\t\t\t\t".$oerpk.','.$ffapk.','.$kadarairpk.','.$dirtpk.",\r\n\t\t\t\t\t".$_SESSION['standard']['userid'].','.$fruitineb.','.$ebstalk.",\r\n                                        ".$fibre.','.$nut.','.$effluent.','.$soliddecanter.','.$fruitinebker.','.$cyclone.",\r\n                                        ".$ltds.','.$ltds2.','.$claybath.','.$usbbefore.','.$usbafter.",\r\n                                        ".$oildiluted.','.$oilin.','.$oilinheavy.','.$caco.','.$intipecah.','.$_POST['dobi'].','.$_POST['batu'].','.$_POST['cangkang'].', '.$_POST['ffa_cangkang'].', '.$_POST['kadarair_cangkang'].', '.$_POST['kotoran_cangkang'].')';
}

// var_dump($strx);

if (mysql_query($strx)) {
    $str = 'select a.* from '.$dbname.".pabrik_produksi a where a.kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n\t\t\t      order by a.tanggal desc limit 20";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        echo "<tr class=rowcontent>\r\n\t\t   <td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kodeorg."</td>\r\n\t\t   <td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".tanggalnormal($bar->tanggal)."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisatbskemarin, 0, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsmasuk, 0, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsdiolah, 0, '.', ',.')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisahariini, 0, '.', ',')."</td>\r\n\t\t   \r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oer, 2, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".@number_format($bar->oer / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffa."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoran."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarair."</td>\r\n\t\t   \r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oerpk, 2, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".@number_format($bar->oerpk / $bar->tbsdiolah * 100, 2, '.', ',')."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffapk."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoranpk."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarairpk."</td>\r\n\t\t   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->intipecah."</td><td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->cangkang."</td><td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffa_cangkang."</td><td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarair_cangkang."</td><td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kotoran_cangkang."</td>\t   \r\n\t\t   <td>\r\n\t\t     <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delProduksi('".$bar->kodeorg."','".$bar->tanggal."','".$bar->kodebarang."');\">\r\n\t\t   </td>\r\n\t\t  </tr>";
    }
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>