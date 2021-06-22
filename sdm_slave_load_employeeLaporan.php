<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zFunction.php';
    $getrows = 20;
    if ($_POST['page']) {
        $page = $_POST['page'];
    } else {
        $page = 1;
    }

    $maxdisplay = $page * $getrows - 20;
    if (isset($_POST['txtsearch'])) {
        $txtsearch = $_POST['txtsearch'];
        $orgsearch = $_POST['orgsearch'];
        $tipesearch = $_POST['tipesearch'];
        $statussearch = $_POST['statussearch'];
        $thnmsk = $_POST['thnmsk'];
        $blnmsk = $_POST['blnmsk'];
        $thnkel = $_POST['thnkel'];
        $blnkel = $_POST['blnkel'];
        $schjk = $_POST['schjk'];
        $tipe_duplikat = $_POST['tipe_duplikat'];
        $kode_resign = $_POST['kode_resign'];
        $nik = $_POST['nik'];
    } else {
        $txtsearch = '';
        $orgsearch = '';
        $tipesearch = '';
        $statussearch = '';
        $thnmsk = '';
        $blnmsk = '';
        $thnkel = '';
        $blnkel = '';
        $schjk = '';
        $tipe_duplikat = '';
        $kode_resign = '';
        $nik = '';
    }

    $where = '';
    if ('' != $txtsearch) {
        $where = " and a.namakaryawan like '%".$txtsearch."%'";
    }

    if ('' != $orgsearch) {
        $where .= " and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";
    }

    if ('' != $nik) {
        $where .= " and nik like '%".$nik."%'";
    }

    if ('' != $tipesearch) {
        if (100 == $tipesearch) {
            $where .= ' and a.tipekaryawan!=4 ';
        } else {
            $where .= " and a.tipekaryawan='".$tipesearch."'";
        }
    }

    if ('' != $thnmsk) {
        $where .= "and left(a.tanggalmasuk,4)='".$thnmsk."'   ";
    }

    if ('' != $blnmsk) {
        $where .= "and mid(a.tanggalmasuk,6,2)='".$blnmsk."'  ";
    }

    if ('' != $thnkel) {
        $where .= "and left(a.tanggalkeluar,4)='".$thnkel."'  ";
    }

    if ('' != $blnkel) {
        $where .= "and mid(a.tanggalkeluar,6,2)='".$blnkel."' ";
    }

    $hariini = date('Y-m-d');
    if ('*' == $statussearch) {
        $where .= " and (a.tanggalkeluar IS NULL and a.tanggalkeluar<='".$hariini."')";
    } else {
        if ('0000-00-00' == $statussearch) {
            $where .= " and (a.tanggalkeluar is NULL or a.tanggalkeluar>'".$hariini."')";
        }
    }

    if ('' != $schjk) {
        $where .= " and a.jeniskelamin='".$schjk."'";
    }
    if ('' != $tipe_duplikat) {
        $where .= " and a.isduplicate='".$tipe_duplikat."'";
    }
    if ('' != $kode_resign) {
        $where .= " and e.alasan LIKE '".$kode_resign." - %'";
    }
    $where .= " and a.kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."%'";

    $listOrg = ambilLokasiTugasDanTurunannya('list', $_SESSION['empl']['lokasitugas']);
    $list = str_replace('|', "','", $listOrg);
    $list = "'".$list."'";

    $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe,e.alasan from '.$dbname.".datakaryawan a
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
    left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
    left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan 
    left join ".$dbname.".sdm_exitinterview e on e.karyawanid = a.karyawanid 
    where 1=1 ".$where.'  limit '.$maxdisplay.','.$getrows;
    $strx = '   select count(*) as jlh 
                from '.$dbname.'.datakaryawan a 
                left join '.$dbname.'.sdm_exitinterview e on e.karyawanid = a.karyawanid 
                where 1=1 '.$where.'  ';
    // print_r($str);
    // die(); 

    // if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    //     $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a
    //     left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
    //     left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan
    //     left join ".$dbname.'.sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 '.$where.'  limit '.$maxdisplay.','.$getrows;
    //     $strx = 'select count(*) as jlh from '.$dbname.'.datakaryawan a where 1=1 '.$where.'  ';
    // } else {
    //     if ('KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
    //         $str = 'select a.*,b.namajabatan,d.tipe,c.namagolongan from '.$dbname.".datakaryawan a \r\n    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n    left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\n    left join ".$dbname.'.sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 '.$where." \r\n    and lokasitugas in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n    order by a.nik asc limit ".$maxdisplay.','.$getrows;
    //         $strx = 'select count(*) as jlh from '.$dbname.".datakaryawan a  \r\n      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan \r\n      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\n      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan where 1=1 \r\n      ".$where.' and lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
    //     } else {
    //         $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a \r\n      left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n      left join ".$dbname.".sdm_5golongan c on a.kodegolongan=c.kodegolongan\r\n      left join ".$dbname.".sdm_5tipekaryawan d on d.id=a.tipekaryawan and a.tipekaryawan!=5 where \r\n      lokasitugas in(".$list.')  '.$where.'  limit '.$maxdisplay.','.$getrows;
    //         $strx = 'select count(*) as jlh from '.$dbname.".datakaryawan a\r\n        where lokasitugas in(".$list.') '.$where.'  ';
    //     }
    // }
    // print_r($str);
    // die();

    $jlhkar = 0;
    $resx = mysql_query($strx);
    echo mysql_error($conn);
    while ($barx = mysql_fetch_object($resx)) {
        $jlhkar = $barx->jlh;
    }
    $res = mysql_query($str);
    $numrows = mysql_num_rows($res);
    $no = $maxdisplay;
    if (0 == $jlhkar) {
        echo '<tr><td colspan=2>DATA NO DATA FOUND</td></tr>';
    }

    if (0 != $jlhkar) {
        echo '<tr><td colspan=2>Total: '.$jlhkar.' Person</td></tr>';
    }

    while ($bar = mysql_fetch_object($res)) {
        $str1 = 'select a.kelompok from '.$dbname.".sdm_5pendidikan a\r\n\t\t       where a.levelpendidikan=".$bar->levelpendidikan.' ';
        $res1 = mysql_query($str1);
        $pendidikan = '';
        while ($barpendidikan = mysql_fetch_object($res1)) {
            $pendidikan = $barpendidikan->kelompok;
        }
        ++$no;
        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td width=85 title='".$bar->karyawanid."'>".$bar->nik."</td>
                    <td>".$bar->namakaryawan."</td>
                    <td>".$bar->namajabatan."</td>
                    <td>".$bar->namagolongan."</td>
                    <td>".$bar->lokasitugas."</td>
                    <td>".$bar->kodeorganisasi."</td>
                    <td>".$bar->subbagian."</td>
                    <td>".$pendidikan."</td>
                    <td>".$bar->statuspajak."</td>
                    <td>".$bar->statusperkawinan."</td>
                    <td align=right >".$bar->jumlahanak."</td>
                    <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                    <td title='".$bar->alasan."'>".tanggalnormal($bar->tanggalkeluar)."</td>
                    <td>".$bar->tipe."</td>
                    <td>
                        <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
                    </td>
                </tr>";
    }

?>