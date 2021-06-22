<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$periode = $_GET['periode'];
$tipe = $_GET['tipe'];
$tgltrf = $_GET['tanggaltrf'];
$username = $_GET['username'];
if ('' == $username) {
    $username = $_SESSION['standard']['username'];
} else {
    $username = $username;
}

$head = "<table border=1>\r\n       <thead><tr\r\n\t   \t<td  bgcolor=#dfdfdf>Acc.No.</td>\r\n\t\t<td align=center bgcolor=#dfdfdf><b>Trans.Amount</b></td>\r\n\t\t<td align=center bgcolor=#dfdfdf><b>Emp.Number</b></td>\r\n\t\t<td align=center bgcolor=#dfdfdf><b>Emp.Name</b></td>\r\n\t\t<td align=center bgcolor=#dfdfdf><b>Dept.</b></td>\r\n\t\t<td align=center bgcolor=#dfdfdf><b>Trans.Date</b></td>\r\n\t\t</tr></thead><tbody>";
$str1 = 'select sum(m.value) as val, e.karyawanid,e.name,e.bankaccount from '.$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_ho_detailmonthly m\r\n       where e.operator='".$username."'\r\n\t   and e.karyawanid=m.karyawanid and periode='".$periode."'\r\n\t   and `type`='".$tipe."'\r\n       group by m.karyawanid order by e.name";
$res1 = mysql_query($str1, $conn);
$no = 0;
$grandTotal = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    $strt = 'select bagian from '.$dbname.'.datakaryawan where karyawanid='.$bar1->karyawanid;
    $bagian = '';
    $rest = mysql_query($strt);
    while ($bart = mysql_fetch_object($rest)) {
        $bagian = $bart->bagian;
    }
    $head .= "<tr>\r\n        <td>'".$bar1->bankaccount."</td>\r\n\t\t<td>".$bar1->val."</td>\r\n\t\t<td>'".$bar1->karyawanid."</td>\r\n\t\t<td>".$bar1->name."</td>\r\n\t\t<td>".$bagian."</td>\r\n\t\t<td>".$tgltrf."</b></td>\r\n\t\t</tr>";
}
$head .= '</tbody><tfoot></tfoot></table>';
$stream = $head;
$nop_ = 'payroll_'.$tipe.'_'.$periode;
if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                @unlink('tempExcel/'.$file);
            }
        }
        closedir($handle);
    }

    $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
    if (!fwrite($handle, $stream)) {
        echo "<script language=javascript1.2>\r\n\t        parent.window.alert('Can't convert to excel format');\r\n\t        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n\t        window.location='tempExcel/".$nop_.".xls';\r\n\t        </script>";
    closedir($handle);
}

?>