<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$periode = $_POST['periode'];
$hariminggu = $_POST['hariminggu'];
$harilibur = $_POST['harilibur'];
$hkefektif = $_POST['hkefektif'];
$catatan = $_POST['catatan'];
switch ($method) {
    case 'insert':
        $qwe = explode('-', $periode);
        $periode = $qwe[0].$qwe[1];
        if ('' == $hkefektif) {
            echo 'warning : Silakan memilih periode.';
            exit();
        }

        if ($hkefektif <= 0) {
            echo 'warning : HK Efektif <= 0.';
            exit();
        }

        $sIns = 'insert into '.$dbname.".sdm_hk_efektif (`periode`,`minggu`,`libur`,`hkefektif`,`catatan`) \r\n        values ('".$periode."','".$hariminggu."','".$harilibur."','".$hkefektif."','".$catatan."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'loadData':
        $str = 'select * from '.$dbname.'.sdm_hk_efektif  order by periode desc';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_assoc($res)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td>".$no."</td>\r\n        <td align=right>".substr($bar['periode'], 0, 4).'-'.substr($bar['periode'], 4, 2)."</td>\r\n        <td align=right>".$bar['minggu']."</td>\r\n        <td align=right>".$bar['libur']."</td>\r\n        <td align=right>".$bar['hkefektif']."</td>\r\n        <td align=right>".$bar['catatan']."</td>\r\n        <td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deletehk('".$bar['periode']."');\"></td>\r\n        </tr>";
        }

        break;
    case 'delete':
        $sIns = 'delete from '.$dbname.".sdm_hk_efektif where periode = '".$periode."'";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    default:
        break;
}

?>