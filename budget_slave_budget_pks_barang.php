<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$sregional = 'select distinct regional from '.$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
$qregional = mysql_query($sregional) || exit(mysql_error($conns));
$regional = mysql_fetch_assoc($qregional);
$tab = $_POST['tab'];
if ('' !== isset($_POST['txtfind'])) {
    $awalan = $_POST['awalan'];
    $txtfind = $_POST['txtfind'];
    if ('1' === $tab) {
        $str = 'select b.kodebarang,a.namabarang,a.satuan from '.$dbname.'.log_5masterbarang a '.'left join '.$dbname.'.bgt_masterbarang b on a.kodebarang=b.kodebarang'." where tahunbudget='".$_POST['thnbgt']."' and regional='".$regional['regional']."' and hargasatuan!=0 and "." (b.kodebarang like '".$txtfind."%' "."or namabarang like '%".$txtfind."%') ";
    } else {
        $str = 'select * from '.$dbname.".log_5masterbarang where kodebarang like '".$awalan."%' and (namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%') ";
    }

    if ($res = mysql_query($str)) {
        echo "\r\n            <fieldset>\r\n            <legend>".$_SESSION['lang']['result']."</legend>\r\n            <div style=\"overflow:auto; height:300px;\" >\r\n            <table class=data cellspacing=1 cellpadding=2  border=0>\r\n            <thead>\r\n            <tr class=rowheader>\r\n                <td class=firsttd>\r\n                    ".substr($_SESSION['lang']['nomor'], 0, 2)."\r\n                </td>\r\n                <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                <td>".$_SESSION['lang']['namabarang']."</td>\r\n                <td>".$_SESSION['lang']['satuan']."</td>\r\n            </tr>\r\n            </thead>\r\n            <tbody>";
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            if (1 === $bar->inactive) {
                echo "<tr class=rowcontent style='cursor:pointer;'  title='Inactive' >";
                $bar->namabarang = $bar->namabarang.' [Inactive]';
            } else {
                if ('1' === $tab) {
                    echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg(1,'".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
                }

                if ('2' === $tab) {
                    echo "<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg(2,'".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
                }
            }

            echo ' <td class=firsttd>'.$no."</td>\r\n                    <td>".$bar->kodebarang."</td>\r\n                    <td>".$bar->namabarang."</td>\r\n                    <td>".$bar->satuan."</td>\r\n                    </tr>";
        }
        echo "</tbody>\r\n                    <tfoot>\r\n                    </tfoot>\r\n                    </table></div></fieldset>";
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

?>