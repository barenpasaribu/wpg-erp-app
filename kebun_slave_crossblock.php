<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$id = $_POST['id'];
$tanggal = tanggalsystem($_POST['tanggal']);
$kodeorg = $_POST['kodeorg'];
$jabatan = $_POST['jabatan'];
$karyawan = $_POST['karyawan'];
$cek = $_POST['cek'];
$keterangan = $_POST['keterangan'];
$kelompok = $_POST['kelompok'];
$jumlahkegiatan = $_POST['jumlahkegiatan'];
for ($i = 1; $i <= $jumlahkegiatan; ++$i) {
    ${'kegiatanvalue'.$i} = $_POST['kegiatanvalue'.$i];
    ${'kegiatanid'.$i} = $_POST['kegiatanid'.$i];
}
$kodeorg1 = $_POST['kodeorg1'];
$periode1 = $_POST['periode1'];
$optnamaorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optnamajab = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optnamakar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'\r\n            and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar is NULL or tanggalkeluar='0000-00-00')");
$optnamaqcid = makeOption($dbname, 'qc_5parameter', 'id,nama');
$optcek = ['Cek', 'Ricek'];
switch ($proses) {
    case 'excel':
        $jukol = 0;
        $kodeorg1 = $_GET['kodeorg1'];
        $periode1 = $_GET['periode1'];
        $bgcolor = 'bgcolor=#DEDEDE align=center';
        $brdr = 1;
        $sOrg2 = 'select * from '.$dbname.".qc_5parameter\r\n        where tipe = 'XBLOK'\r\n        order by kelompok, id";
        $qOrg2 = mysql_query($sOrg2) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            ++$jukol;
            $parameterid[$rOrg2['id']] = $rOrg2['id'];
            $parameter[$rOrg2['id']] = $rOrg2['nama'];
            $parametersat[$rOrg2['id']] = $rOrg2['satuan'];
        }
        $jukol += 6;
        $kojud = $jukol - 2;
        $tab .= "<table>\r\n        <tr>\r\n            <td colspan=".$jukol." align=left><b>RECAP CROSSBLOCK</b></td>\r\n        </tr>\r\n        <tr>\r\n            <td colspan=2 align=left>".$_SESSION['lang']['kodeorg'].'</td><td colspan='.$kojud.'>: '.$optnamaorg[$kodeorg1]." </td>\r\n        </tr>\r\n        <tr>\r\n            <td colspan=2 align=left>".$_SESSION['lang']['periode'].'</td><td colspan='.$kojud.'>: '.$periode1." </td>\r\n        </tr>\r\n        </table>";
        $tab .= '<table cellpadding=1 cellspacing=1 border='.$brdr." class=sortable>\r\n        <thead>\r\n            <tr class=rowheader>\r\n            <td ".$bgcolor.'>'.$_SESSION['lang']['nomor']."</td>\r\n            <td ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n            <td ".$bgcolor.'>'.$_SESSION['lang']['diperiksa']."</td>\r\n            <td ".$bgcolor.">Check/Re-Check</td>\r\n            <td ".$bgcolor.'>Afdeling/Block</td>';
        if (!empty($parameterid)) {
            foreach ($parameterid as $paramz) {
                $tab .= '<td '.$bgcolor.'>'.$parameter[$paramz].' ('.$parametersat[$paramz].')</td>';
                if ('49' === $paramz) {
                    $tab .= '<td '.$bgcolor.'>Angka Kerapatan Panen (%)</td>';
                }

                if ('51' === $paramz) {
                    $tab .= '<td '.$bgcolor.'>Rasio Buah Tinggal (%)</td>';
                    $tab .= '<td '.$bgcolor.'>Rasio Berondolan Tinggal (%)</td>';
                }
            }
        }

        $tab .= '<td '.$bgcolor.'>'.$_SESSION['lang']['keterangan']."</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody>\r\n        ";
        $sData = 'select a.* from '.$dbname.".kebun_crossblock_dt a \r\n        left join ".$dbname.".kebun_crossblock_ht b on a.id=b.id \r\n        where b.kodeorg like '".$kodeorg1."%' and b.tanggal like '".$periode1."%'\r\n        ";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $dataparameter[$rData['id']][$rData['qcid']] = $rData['jumlah'];
        }
        $no = 0;
        $sData = 'select * from '.$dbname.".kebun_crossblock_ht \r\n        where kodeorg like '".$kodeorg1."%' and tanggal like '".$periode1."%'\r\n        order by tanggal desc, kodeorg";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td align=right>'.$no.'</td>';
            $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
            $tab .= '<td>'.$rData['jabatan'].' '.$optnamakar[$rData['karyawanid']].'</td>';
            $tab .= '<td>'.$optcek[$rData['cek']].'</td>';
            $tab .= '<td>'.$optnamaorg[$rData['kodeorg']].'</td>';
            if (!empty($parameterid)) {
                foreach ($parameterid as $paramz) {
                    $tote[$paramz] += $dataparameter[$rData['id']][$paramz];
                    $tab .= '<td align=right>'.number_format($dataparameter[$rData['id']][$paramz]).'</td>';
                    if ('49' === $paramz) {
                        $angkakerapatanpanen = $dataparameter[$rData['id']][48] / $dataparameter[$rData['id']][47] * 100;
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    }

                    if ('51' === $paramz) {
                        $angkakerapatanpanen = $dataparameter[$rData['id']][50] / ($dataparameter[$rData['id']][47] / $dataparameter[$rData['id']][46]);
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                        $angkakerapatanpanen = $dataparameter[$rData['id']][51] / ($dataparameter[$rData['id']][47] / $dataparameter[$rData['id']][46]);
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    }
                }
            }

            $tab .= '<td>'.$rData['keterangan'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr>';
        $tab .= '<td '.$bgcolor.' align=center colspan=5>Total</td>';
        if (!empty($parameterid)) {
            foreach ($parameterid as $paramz) {
                $tab .= '<td '.$bgcolor.' align=right>'.number_format($tote[$paramz]).'</td>';
                if ('49' === $paramz) {
                    $angkakerapatanpanen = $tote[48] / $tote[47] * 100;
                    $tab .= '<td '.$bgcolor.' align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                }

                if ('51' === $paramz) {
                    $angkakerapatanpanen = $tote[50] / ($tote[47] / $tote[46]);
                    $tab .= '<td '.$bgcolor.' align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    $angkakerapatanpanen = $tote[51] / ($tote[47] / $tote[46]);
                    $tab .= '<td '.$bgcolor.' align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                }
            }
        }

        $tab .= '<td '.$bgcolor.'></td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table><br>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'laporancrossblock_'.$kodeorg1.$periode1.'_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";

        break;
    case 'preview':
        $sOrg2 = 'select * from '.$dbname.".qc_5parameter\r\n        where tipe = 'XBLOK'\r\n        order by kelompok, id";
        $qOrg2 = mysql_query($sOrg2) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            $parameterid[$rOrg2['id']] = $rOrg2['id'];
            $parameter[$rOrg2['id']] = $rOrg2['nama'];
            $parametersat[$rOrg2['id']] = $rOrg2['satuan'];
        }
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n        <thead>\r\n            <tr class=rowheader>\r\n            <td>".$_SESSION['lang']['nomor']."</td>\r\n            <td>".$_SESSION['lang']['tanggal']."</td>\r\n            <td>".$_SESSION['lang']['diperiksa']."</td>\r\n            <td>Check/Re-Check</td>\r\n            <td>Afdeling/Block</td>";
        if (!empty($parameterid)) {
            foreach ($parameterid as $paramz) {
                $tab .= '<td>'.$parameter[$paramz].' ('.$parametersat[$paramz].')</td>';
                if ('49' === $paramz) {
                    $tab .= '<td>Angka Kerapatan Panen (%)</td>';
                }

                if ('51' === $paramz) {
                    $tab .= '<td>Rasio Buah Tinggal (%)</td>';
                    $tab .= '<td>Rasio Berondolan Tinggal (%)</td>';
                }
            }
        }

        $tab .= '<td>'.$_SESSION['lang']['keterangan']."</td>\r\n            </tr>\r\n        </thead>\r\n        <tbody>\r\n        ";
        $sData = 'select a.* from '.$dbname.".kebun_crossblock_dt a \r\n        left join ".$dbname.".kebun_crossblock_ht b on a.id=b.id \r\n        where b.kodeorg like '".$kodeorg1."%' and b.tanggal like '".$periode1."%'\r\n        ";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $dataparameter[$rData['id']][$rData['qcid']] = $rData['jumlah'];
        }
        $no = 0;
        $sData = 'select * from '.$dbname.".kebun_crossblock_ht \r\n        where kodeorg like '".$kodeorg1."%' and tanggal like '".$periode1."%'\r\n        order by tanggal desc, kodeorg";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td align=right>'.$no.'</td>';
            $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
            $tab .= '<td>'.$rData['jabatan'].' '.$optnamakar[$rData['karyawanid']].'</td>';
            $tab .= '<td>'.$optcek[$rData['cek']].'</td>';
            $tab .= '<td>'.$optnamaorg[$rData['kodeorg']].'</td>';
            if (!empty($parameterid)) {
                foreach ($parameterid as $paramz) {
                    $tote[$paramz] += $dataparameter[$rData['id']][$paramz];
                    $tab .= '<td align=right>'.number_format($dataparameter[$rData['id']][$paramz]).'</td>';
                    if ('49' === $paramz) {
                        $angkakerapatanpanen = $dataparameter[$rData['id']][48] / $dataparameter[$rData['id']][47] * 100;
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    }

                    if ('51' === $paramz) {
                        $angkakerapatanpanen = $dataparameter[$rData['id']][50] / ($dataparameter[$rData['id']][47] / $dataparameter[$rData['id']][46]);
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                        $angkakerapatanpanen = $dataparameter[$rData['id']][51] / ($dataparameter[$rData['id']][47] / $dataparameter[$rData['id']][46]);
                        $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    }
                }
            }

            $tab .= '<td>'.$rData['keterangan'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr>';
        $tab .= '<td align=center colspan=5>Total</td>';
        if (!empty($parameterid)) {
            foreach ($parameterid as $paramz) {
                $tab .= '<td align=right>'.number_format($tote[$paramz]).'</td>';
                if ('49' === $paramz) {
                    $angkakerapatanpanen = $tote[48] / $tote[47] * 100;
                    $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                }

                if ('51' === $paramz) {
                    $angkakerapatanpanen = $tote[50] / ($tote[47] / $tote[46]);
                    $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                    $angkakerapatanpanen = $tote[51] / ($tote[47] / $tote[46]);
                    $tab .= '<td align=right>'.number_format($angkakerapatanpanen, 2).'</td>';
                }
            }
        }

        $tab .= '<td></td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'getperiode':
        $sOrg2 = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".kebun_crossblock_ht \r\n        order by tanggal desc";
        $qOrg2 = mysql_query($sOrg2) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            $optperiode .= '<option value='.$rOrg2['periode'].'>'.$rOrg2['periode'].'</option>';
        }
        echo $optperiode;

        break;
    case 'openkegiatan':
        $sOrg2 = 'select * from '.$dbname.".qc_5parameter\r\n        where kelompok = '".$kelompok."'\r\n        order by id";
        $sData = 'select * from '.$dbname.".kebun_crossblock_dt \r\n        where id = '".$id."'";
        $qData = mysql_query($sData) ;
        while ($rData = mysql_fetch_assoc($qData)) {
            $nilainya[$rData['qcid']] = $rData['jumlah'];
        }
        $no = 0;
        $tab .= '<table cellspacing=1 border=0>';
        $qOrg2 = mysql_query($sOrg2) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            ++$no;
            $kegiatanid = 'kegiatanid'.$no;
            $kegiatanvalue = 'kegiatanvalue'.$no;
            $veliu = 0;
            if ('' !== $nilainya[$rOrg2['id']]) {
                $veliu = $nilainya[$rOrg2['id']];
            }

            $tab .= "<tr>\r\n            <td style=width:200px;><input type='hidden' id='".$kegiatanid."' value='".$rOrg2['id']."'/>".$rOrg2['nama']."</td>\r\n            <td>:</td>\r\n            <td><input type='text' class='myinputtextnumber' style='width:150px;' id='".$kegiatanvalue."' onkeypress='return angka_doang(event)' value='".$veliu."'/> ".$rOrg2['satuan']."</td>\r\n        </tr>";
        }
        $tab .= '</table>';
        $tab .= '####'.$no;
        echo $tab;

        break;
    case 'getkaryawan':
        $optkaryawan = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sOrg2 = 'select karyawanid, namakaryawan from '.$dbname.".datakaryawan\r\n        where kodejabatan = '".$jabatan."' and lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'\r\n            and (tanggalkeluar>'".date('Y-m-d')."' or tanggalkeluar is NULL or tanggalkeluar='0000-00-00')\r\n        order by namakaryawan\r\n        ";
        $qOrg2 = mysql_query($sOrg2) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            $optkaryawan .= '<option value='.$rOrg2['karyawanid'].'>'.$rOrg2['namakaryawan'].'</option>';
        }
        echo $optkaryawan;

        break;
    case 'savedata0':
        $sInsert = "SELECT Auto_increment as nextid\r\n        FROM information_schema.tables \r\n        WHERE table_name='kebun_crossblock_ht'\r\n        AND table_schema = '".$dbname."'";
        $qOrg2 = mysql_query($sInsert) ;
        while ($rOrg2 = mysql_fetch_assoc($qOrg2)) {
            $nextid = $rOrg2['nextid'];
        }
        $sInsert = 'insert into '.$dbname.".kebun_crossblock_ht (tanggal, kodeorg, jabatan, karyawanid, cek, keterangan, updateby, lastupdate) \r\n    values('".$tanggal."','".$kodeorg."','".$jabatan."','".$karyawan."','".$cek."','".$keterangan."','".$_SESSION['standard']['userid']."', CURRENT_TIMESTAMP)";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        for ($i = 1; $i <= $jumlahkegiatan; ++$i) {
            $kegiatanid = ${'kegiatanid'.$i};
            $kegiatanvalue = ${'kegiatanvalue'.$i};
            $sInsert = 'insert into '.$dbname.".kebun_crossblock_dt (id, qcid, jumlah) \r\n        values('".$nextid."','".$kegiatanid."','".$kegiatanvalue."')";
            if (!mysql_query($sInsert)) {
                echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
            }
        }

        break;
    case 'editdata0':
        $sInsert = 'update '.$dbname.".kebun_crossblock_ht set tanggal = '".$tanggal."', kodeorg = '".$kodeorg."', jabatan = '".$jabatan."', karyawanid = '".$karyawan."', \r\n        cek = '".$cek."', keterangan = '".$keterangan."', updateby = '".$_SESSION['standard']['userid']."', lastupdate = CURRENT_TIMESTAMP\r\n        where id = '".$id."'";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        $adadata = 0;
        for ($i = 1; $i <= $jumlahkegiatan; ++$i) {
            $kegiatanid = ${'kegiatanid'.$i};
            $kegiatanvalue = ${'kegiatanvalue'.$i};
            $sData = 'select * from '.$dbname.".kebun_crossblock_dt \r\n            where id = '".$id."' and qcid = '".$kegiatanid."'";
            $qData = mysql_query($sData) ;
            while ($rData = mysql_fetch_assoc($qData)) {
                $adadata = 1;
            }
        }
        if (1 === $adadata) {
            for ($i = 1; $i <= $jumlahkegiatan; ++$i) {
                $kegiatanid = ${'kegiatanid'.$i};
                $kegiatanvalue = ${'kegiatanvalue'.$i};
                $sInsert = 'update '.$dbname.".kebun_crossblock_dt set id = '".$id."', jumlah = '".$kegiatanvalue."'\r\n                where id = '".$id."' and qcid = '".$kegiatanid."'";
                if (!mysql_query($sInsert)) {
                    echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                }
            }
        } else {
            for ($i = 1; $i <= $jumlahkegiatan; ++$i) {
                $kegiatanid = ${'kegiatanid'.$i};
                $kegiatanvalue = ${'kegiatanvalue'.$i};
                $sInsert = 'insert into '.$dbname.".kebun_crossblock_dt (id, qcid, jumlah) \r\n            values('".$id."','".$kegiatanid."','".$kegiatanvalue."')";
                if (!mysql_query($sInsert)) {
                    echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                }
            }
        }

        break;
    case 'loaddata0':
        $limit = 50;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select * from '.$dbname.".kebun_crossblock_ht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
        $query2 = mysql_query($sql2) ;
        $jlhbrs = mysql_num_rows($query2);
        if (0 !== $jlhbrs) {
            $no = 0;
            $sData = 'select * from '.$dbname.".kebun_crossblock_ht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by tanggal desc, kodeorg limit ".$offset.','.$limit.' ';
            $qData = mysql_query($sData) ;
            while ($rData = mysql_fetch_assoc($qData)) {
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td align=right>'.$no.'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td>'.$optnamaorg[$rData['kodeorg']].'</td>';
                $tab .= '<td>'.$optnamakar[$rData['karyawanid']].'</td>';
                $tab .= '<td>'.$rData['jabatan'].'</td>';
                $tab .= '<td>'.$optcek[$rData['cek']].'</td>';
                $tab .= '<td>'.$rData['keterangan'].'</td>';
                $tab .= "<td><img id='detailedit' &nbsp; style='cursor:pointer;' title='Edit ".$rData['kodeorg']."' class=zImgBtn \r\n                onclick=\"filldata0('".$rData['id']."','".tanggalnormal($rData['tanggal'])."','".$rData['kodeorg']."','".$rData['jabatan']."','".$rData['karyawanid']."','".$rData['cek']."','".$rData['keterangan']."')\" src='images/application/application_edit.png'/>";
                $tab .= "&nbsp;<img id='detaildel' style='cursor:pointer;' title='Delete ".$rData['kodeorg']."' class=zImgBtn \r\n                onclick=\"deldata0('".$rData['id']."')\" src='images/application/application_delete.png'/>";
                $tab .= '</tr>';
            }
            $tab .= "\r\n        <tr class=rowheader><td colspan=10 align=center>\r\n        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n        <button class=mybutton onclick=exploredata(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n        <button class=mybutton onclick=exploredata(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n        </td>\r\n        </tr>";
        } else {
            $tab .= '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo $tab;

        break;
    case 'deldata0':
        $sInsert = 'delete from '.$dbname.".kebun_crossblock_ht where id = '".$id."'";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        $sInsert = 'delete from '.$dbname.".kebun_crossblock_dt where id = '".$id."'";
        if (!mysql_query($sInsert)) {
            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
        }

        break;
    default:
        break;
}

?>