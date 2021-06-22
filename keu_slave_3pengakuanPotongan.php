<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
if ('EN' === $_SESSION['language']) {
    $zz = 'namaakun1 as namaakun';
} else {
    $zz = 'namaakun';
}

$str = 'select noakun,'.$zz.' from '.$dbname.'.keu_5akun where length(noakun)=7 order by namaakun';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $arrAkun[$bar->noakun] = $bar->namaakun;
}
$sAkun = 'select  id,name from '.$dbname.'.sdm_ho_component where plus=0 order by name';
$qAkun = mysql_query($sAkun);
while ($rAkun = mysql_fetch_assoc($qAkun)) {
    $namakomponen[$rAkun['id']] = $rAkun['name'];
}
$str = 'select * from '.$dbname.'.keu_5pengakuanpotongan order by idkomponen';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $debet[$bar->idkomponen] = $bar->noakundebet;
    $kredit[$bar->idkomponen] = $bar->noakunkredit;
    if ('' === $bar->noakundebet || '' === $bar->noakundebet) {
        exit(' Error: Setup account number debet/kredit for component '.$bar->idkomponen.' not defined');
    }
}
$tanggal = str_replace('-', '', $_POST['periode']).'28';
$str = ' select a.idkomponen,a.karyawanid,a.jumlah,b.namakaryawan from '.$dbname.".sdm_gaji a\r\n           left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.kodeorg='".$_SESSION['empl']['lokasitugas']."' \r\n           and a.periodegaji='".$_POST['periode']."' and a.idkomponen in(select idkomponen from ".$dbname.'.keu_5pengakuanpotongan)';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $total[$bar->idkomponen] += $bar->jumlah;
    $nama[$bar->karyawanid] = $bar->namakaryawan;
    $rinci[$bar->idkomponen][$bar->karyawanid] = $bar->jumlah;
}
if (empty($total)) {
    exit('Error: No salary data found');
}

if ('post' === $_POST['method']) {
    $str = 'select * from '.$dbname.".setup_periodeakuntansi where \r\n          kodeorg ='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0 and periode='".$_POST['periode']."'";
    $res = mysql_query($str);
    if (mysql_num_rows($res) < 1) {
        exit('Error: Accounting has closed transaction of  '.$_POST['periode']);
    }

    foreach ($total as $komponen => $ttl) {
        $dataRes['detail'] = '';
        $noUrut = 0;
        ++$noUrut;
        $nojurnal = $tanggal.'/'.$_SESSION['empl']['lokasitugas'].'/POT/'.$komponen;
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => 'POT', 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => 1, 'totaldebet' => $ttl, 'totalkredit' => -1 * $ttl, 'amountkoreksi' => '0', 'noreferensi' => 'ALK_POT:'.$komponen, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $debet[$komponen], 'keterangan' => $namakomponen[$komponen], 'jumlah' => $ttl, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_POT:'.$komponen, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        foreach ($rinci[$komponen] as $karid => $jlhperorang) {
            ++$noUrut;
            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $kredit[$komponen], 'keterangan' => $namakomponen[$komponen].': '.$nama[$karid], 'jumlah' => -1 * $jlhperorang, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_SESSION['empl']['lokasitugas'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => $karid, 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_POT:'.$komponen, 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        }
        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
        mysql_query($RBDet);
        $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
        if (!mysql_query($insHead)) {
            $headErr .= 'Insert Header komponen:'.$komponen.' Error : '.mysql_error()."\n";
        }

        if ('' === $headErr) {
            $detailErr = '';
            foreach ($dataRes['detail'] as $row) {
                $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                if (!mysql_query($insDet)) {
                    $detailErr .= 'Insert Detail Komponen:'.$komponen.' Error : '.mysql_error()."\n";

                    break;
                }
            }
            if ('' === $detailErr) {
            } else {
                echo $detailErr;
                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                if (!mysql_query($RBDet)) {
                    echo 'Rollback Delete Header Error : '.mysql_error();
                    exit();
                }
            }
        } else {
            echo $headErr;
            exit();
        }
    }
} else {
    echo "<button class=mybutton onclick=prosesPotongan('".$_POST['periode']."') id=btnproses>Process</button>\r\n                  <table class=sortable cellspacing=1 border=0>\r\n                  <thead>\r\n                    <tr class=rowheader>\r\n                    <td>No</td>\r\n                    <td>".$_SESSION['lang']['periode']."</td>\r\n                    <td>".$_SESSION['lang']['noakun']."</td>\r\n                    <td>".$_SESSION['lang']['namaakun']."</td>                    \r\n                    <td>".$_SESSION['lang']['keterangan']."</td>\r\n                    <td>".$_SESSION['lang']['debet']."</td>\r\n                    <td>".$_SESSION['lang']['kredit']."</td>\r\n                    </tr>\r\n                  </thead>\r\n                  <tbody>";
    foreach ($total as $komponen => $ttl) {
        $no = 0;
        ++$no;
        echo "<tr class=rowcontent>\r\n                          <td>".$no."</td>\r\n                          <td>".$_POST['periode']."</td> \r\n                          <td>".$debet[$komponen]."</td>\r\n                          <td>".$arrAkun[$debet[$komponen]]."</td> \r\n                          <td>".$namakomponen[$komponen]."</td>\r\n                          <td align=right>".number_format($ttl)."</td> \r\n                          <td align=right>0</td>     \r\n                          </tr>";
        foreach ($rinci[$komponen] as $karid => $jlhperorang) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                                 <td>".$no."</td>\r\n                                 <td>".$_POST['periode']."</td> \r\n                                 <td>".$kredit[$komponen]."</td>\r\n                                 <td>".$arrAkun[$kredit[$komponen]]."</td> \r\n                                 <td>".$namakomponen[$komponen].': '.$nama[$karid]."</td>\r\n                                 <td align=right>0</td>                                      \r\n                                 <td align=right>".number_format($jlhperorang)."</td>     \r\n                                 </tr>";
        }
    }
    echo '</tbody><tfoot></tfoot></table>';
}

?>