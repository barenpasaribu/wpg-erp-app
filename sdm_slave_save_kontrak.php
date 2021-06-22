<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';

    $proses = $_POST['proses'];

    switch ($proses) {
        // untuk pengopreasian pabrik
        case 'loadData':
            $str = 'select * from '.$dbname.'.sdm_karyawan_kontrak 
                    where 
                    karyawanid='.$_POST['karyawanid'].' 
                    order by tanggal_mulai DESC';

            $res = mysql_query($str);
            $no = 0;
            $mskerja = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                $msk = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulanmasuk), 0, 2), 1, substr($bar->bulanmasuk, 3, 4));
                $klr = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulankeluar), 0, 2), 1, substr($bar->bulankeluar, 3, 4));
                $dateDiff = $klr - $msk;
                $mskerja = floor($dateDiff / (60 * 60 * 24)) / 365;
                $queryGetNamaTipeKaryawan = "SELECT * FROM sdm_5tipekaryawan WHERE id='".$bar->tipe_karyawan."'";
                $dataNamaTipe = fetchData($queryGetNamaTipeKaryawan);

                $namaTipe = "-";
                if (!empty($dataNamaTipe[0]['tipe'])) {
                    $namaTipe = $dataNamaTipe[0]['tipe'];
                }

                $statusCuti = "-";
                if ($bar->status_cuti == 0) {
                    $statusCuti = "Tidak dapat cuti";
                }else{
                    $statusCuti = "Dapat cuti";
                }
                echo "  <tr class=rowcontent>
                            <td class=firsttd>".$no."</td>
                            <td>".$bar->nama_perusahaan."</td>
                            <td>".$bar->bidang."</td>
                            <td>".tanggalnormal($bar->tanggal_mulai)."</td>
                            <td>".tanggalnormal($bar->tanggal_akhir)."</td>
                            <td>".$bar->jabatan."</td>
                            <td>".$bar->bagian."</td>
                            <td>".$bar->alamat."</td>
                            <td>".$namaTipe."</td>
                            <td>".$statusCuti."</td>
                            <td>
                                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deleteKontrak('".$bar->id."');\">
                            </td>
                        </tr>";
            }
            break;
        case 'simpan':
            $insert = " INSERT INTO `sdm_karyawan_kontrak` 
                        (`karyawanid`, `nama_perusahaan`, `bidang`, `tanggal_mulai`, `tanggal_akhir`, 
                        `jabatan`, `bagian`, `alamat`, `tipe_karyawan`, `status_cuti`, `created_by`, `created_at`) 
                        VALUES 
                        ('".$_POST['karyawanid']."', '".$_POST['nama_perusahaan']."', '".$_POST['bidang']."', 
                        '".tanggalsystem($_POST['tanggal_mulai'])."', '".tanggalsystem($_POST['tanggal_akhir'])."', '".$_POST['jabatan']."', 
                        '".$_POST['bagian']."', '".$_POST['alamat']."', '".$_POST['tipe_karyawan']."', '".$_POST['status_cuti']."', '".$_SESSION['empl']['karyawanid']."', 
                         NOW() );
                    ";
            if (mysql_query($insert)) {
                echo "Tambah data berhasil.";
            } else {
                echo 'Gagal, '.mysql_error($conn);
            }
            break;
        case 'delete':
            $delete = " DELETE FROM sdm_karyawan_kontrak
                        WHERE id = '".$_POST['idkontrak']."'
                    ";
            if (mysql_query($delete)) {
                echo "Delete data berhasil.";
            } else {
                echo 'Gagal, '.mysql_error($conn);
            }
            break;
    }

?>