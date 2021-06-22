<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$kodeorg = $_POST['kodeorg'];
$kodetangki = $_POST['kodetangki'];
$suhu = $_POST['suhu'];
$kepadatan = $_POST['kepadatan'];
$ketetapan = $_POST['ketetapan'];
$method = $_POST['method'];
switch ($method) {
    case 'insert':
        if (empty($kodeorg) || empty($kodetangki)) {
            echo 'warning:Please Complete The Form';
            exit();
        }
		$qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5ketetapansuhu where kodeorg="'.$kodeorg.'" AND kodetangki="'.$kodetangki.'" AND suhu='.$suhu.'');
		$cek   = mysql_fetch_assoc($qcek);
			if($cek['total'] != 0) {
				echo 'DB error : data sudah ada';
				exit();
			}
        $i = 'insert into '.$dbname.".pabrik_5ketetapansuhu (kodeorg,kodetangki,suhu,kepadatan,ketetapan,updateby) values ('".$kodeorg."','".$kodetangki."','".$suhu."','".$kepadatan."','".$ketetapan."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        if (empty($kodeorg) || empty($kodetangki)) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $i = 'update '.$dbname.'.pabrik_5ketetapansuhu set 
		kepadatan="'.$kepadatan.'",
		ketetapan="'.$ketetapan.'",
		updateby="'.$_SESSION['standard']['userid'].'" 
		where kodeorg="'.$kodeorg.'" AND kodetangki="'.$kodetangki.'" AND suhu='.$suhu.'';
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n            <div style='height:220px;overflow:auto'>\r\n                    <table class=sortable cellspacing=1 border=0>\r\n                 <thead>\r\n                             <tr class=rowheader>\r\n                                     <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['kodetangki']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['suhu']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['kepadatan']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['ketetapan']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n                                     <td align=center>".$_SESSION['lang']['action']."</td>\r\n                             </tr>\r\n                    </thead>\r\n                    <tbody>";
        $ql2 = 'select * from '.$dbname.".pabrik_5ketetapansuhu WHERE kodeorg LIKE '".$_SESSION['empl']['lokasitugas']."'";
        $n = mysql_query($ql2);
        $no = 0;
        $data = [];
        $optKarRow = [];
        while ($d = mysql_fetch_assoc($n)) {
            $data[] = $d;
        }
        if (!empty($data)) {
            $whereKarRow = 'karyawanid in (';
            $notFirst = false;
            foreach ($data as $key => $row) {
                if ('' !== $row['updateby']) {
                    if (false === $notFirst) {
                        $whereKarRow .= $row['updateby'];
                        $notFirst = true;
                    } else {
                        $whereKarRow .= ','.$row['updateby'];
                    }
                }
            }
            $whereKarRow .= ')';
            $optKarRow = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKarRow, '0', true);
        }

        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$row['kodetangki'].'</td>';
            echo '<td align=right>'.$row['suhu'].'</td>';
            echo '<td align=right>'.$row['kepadatan'].'</td>';
            echo '<td align=right>'.$row['ketetapan'].'</td>';
            echo '<td align=left>'.$optKarRow[$row['updateby']].'</td>';
            echo "<td align=center>\r\n                            <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"fillField('".$row['kodeorg']."','".$row['kodetangki']."','".$row['suhu']."','".$row['kepadatan']."','".$row['ketetapan']."');\">\r\n                            <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".$row['kodeorg']."','".$row['kodetangki']."','".$row['suhu']."');\"></td>";
            echo '</tr>';
        }
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_5ketetapansuhu where kodeorg='".$kodeorg."' AND kodetangki='".$kodetangki."' AND suhu='".$suhu."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getTangki':
        $sGet = 'select kodetangki,keterangan from '.$dbname.".pabrik_5tangki where kodeorg='".$kodeorg."'";
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optTangki .= '<option value='.$rGet['kodetangki'].'>'.$rGet['keterangan'].'</option>';
        }
        echo $optTangki;

        break;
}

?>