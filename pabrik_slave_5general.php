<?php
	require_once 'master_validation.php';
	require_once 'config/connection.php';
	include_once 'lib/eagrolib.php';
	include 'lib/zMysql.php';
	include 'lib/zFunction.php';
	include_once 'lib/zLib.php';

	$proses = $_POST['proses'];

	switch ($proses) {       
        case 'simpanGeneral':
            $tanggal = date("Y-m-d");

            // Cek Data di DB
            $queryCek = "SELECT * FROM pabrik_5general 
            WHERE 
            code='".$_POST['code']."' 
            AND
            kodeorg='".$_SESSION['empl']['lokasitugas']."' 
            ";
            $queryCekAct = mysql_query($queryCek);
            $hasilCek = mysql_num_rows($queryCekAct);

            $query = "INSERT INTO pabrik_5general 
                        (code, kodeorg, nilai, created_by, update_by, created_at, update_at) 
                        VALUES 
                        (
                        '".$_POST['code']."',
                        '".$_SESSION['empl']['lokasitugas']."',
                        '".$_POST['nilai']."',
                        '".$_SESSION['empl']['karyawanid']."',
                        '".$_SESSION['empl']['karyawanid']."',
                        '".$tanggal."',
                        '".$tanggal."'
                        )";
            $notif = null;            
            if ($hasilCek == 0) {
                mysql_query($query);
                $notif = "SIMPAN BERHASIL";
            }else{
                $notif = "GAGAL, CODE SUDAH ADA";
            }
            echo $notif;
            break;
        case 'ubahNilai':
            $query = "  UPDATE pabrik_5general 
                        SET nilai = '".$_POST['nilai']."'
                        WHERE code = '".$_POST['code']."'
                        ";
            if (mysql_query($query)) {
                $notif = "SIMPAN BERHASIL";
            }else{
                $notif = "GAGAL UPDATE";
            }

            echo $notif;

            break;
        case 'loadData':    
            $str = 'SELECT * FROM pabrik_5general 
                    WHERE 
                        kodeorg="'.$_SESSION['empl']['lokasitugas'].'" 
                    order by 
                        code ASC';
            $res = mysql_query($str);
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
?>
                <tr class="rowcontent" id="tr_<?= $no ?>">
                    <td title="<?= $bar->description ?>"><?= $bar->code ?></td>
                    <td> <input type="text" id="<?= $bar->code ?>" value="<?= $bar->nilai ?>" onchange="ubahNilai('<?= $bar->code ?>');"></td>
                </tr>
<?php
            $no++;
            }
            break;
	}
?>