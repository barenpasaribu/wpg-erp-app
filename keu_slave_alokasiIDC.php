<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
switch ($_POST[aksi]) {
    case 'ambilnokas':
        $str = "select nojurnal as notransaksi,'".$_SESSION['empl']['lokasitugas']."' as kodeorg,sum(jumlah) as jumlah from ".$dbname.'.keu_jurnaldt_vw where tanggal='.tanggalsystem($_POST['tanggal'])." and nojurnal like '%/".$_SESSION['empl']['lokasitugas']."/M%' and jumlah > 0 group by nojurnal";
        $res = mysql_query($str);
        $opt = "<option value=''>Pilih....</option>";
        while ($bar = mysql_fetch_object($res)) {
            $opt .= "<option value='".$bar->notransaksi.'#'.$bar->jumlah.'#'.$bar->kodeorg."'>".$bar->kodeorg.': '.$bar->notransaksi.' jumlah '.number_format($bar->jumlah).'</option>';
        }
        echo $opt;

        break;
    case 'ambilAlokasi':
        $ambilInduk = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$_POST['kodeorg']."'";
        $res = mysql_query($ambilInduk);
        $induk = '';
        while ($bar = mysql_fetch_object($res)) {
            $induk = $bar->induk;
        }
        $str = 'select distinct left(a.kodeorg,4) as kebun from '.$dbname.".setup_blok a\r\n                  left join ".$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi\r\n                  where a.statusblok in('TB','TBM','LC','TBM1','TBM2','TBM3','TM')\r\n                  and left(b.induk,4) in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$induk."')";
        $res = mysql_query($str);
        $opt = "<option value=''>Choose....</option>";
        while ($bar = mysql_fetch_object($res)) {
            $opt .= "<option value='".$bar->kebun."'>".$bar->kebun.'</option>';
        }
        echo $opt;

        break;
    case 'ambilBlok':
        $tg = substr($_POST['tanggal'], 6, 4).'-'.substr($_POST['tanggal'], 3, 2);
        $str = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_POST['kodeorg']."' and periode='".$tg."' and tutupbuku=0";
        $res = mysql_query($str);
        if (mysql_num_rows($res) < 1) {
            exit(' Error: Transaction period is closed');
        }

        $nojurnal = tanggalsystem($_POST['tanggal']).'/'.$_POST['kodeorg'].'/IDC/001';
        $str = 'select * from '.$dbname.".keu_jurnalht where nojurnal='".$nojurnal."'";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            exit(' Error: IDC has been allocated before ('.$_POST['kodeorg'].')');
        }

        $optAkun = "<option value=''>Choose..</option>";
        if ('EN' === $_SESSION['language']) {
            $str = 'select noakun,namaakun1 as namaakun from '.$dbname.'.keu_5akun where detail=1 order by noakun';
        } else {
            $str = 'select noakun,namaakun from '.$dbname.'.keu_5akun where detail=1 order by noakun';
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optAkun .= "<option value='".$bar->noakun."'>".$bar->noakun.'-'.$bar->namaakun.'</option>';
        }
        $aLuas = 'select sum(luasareaproduktif) as luasdivisi from '.$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'";
        $bLuas = mysql_query($aLuas);
        $cLuas = mysql_fetch_assoc($bLuas);
        $str = 'select luasareaproduktif,kodeorg,statusblok from '.$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'";
        $res = mysql_query($str);
        $jumblok = mysql_num_rows($res);
        if ($jumblok < 1) {
            exit(' Error: There is no block to allocate');
        }

        echo "<fieldset style='width:400px'>".$_SESSION['lang']['idcnote']."</fieldset>\r\n                <table>\r\n                       <tr><td>".$_SESSION['lang']['debet'].'</td><td><select id=debet>'.$optAkun.'</select>Rp.'.number_format($_POST['jumlah'])."</td></tr>\r\n                        <tr><td>".$_SESSION['lang']['kredit'].'</td><td><select id=kredit>'.$optAkun.'</select>Rp.'.number_format($_POST['jumlah'])."</td></tr>\r\n                         </table>   \r\n                        ";
        echo "<button onclick=saveDistribusi('".$_POST['kodeorg']."')>".$_SESSION['lang']['save'].'</button>';
        echo '<fieldset><legend>'.$_SESSION['lang']['distribusi'].'</legend>';
        echo "<table class=sortable border=0 cellspacing=1>\r\n                       <thead>\r\n                           <tr class=rowheader><td>".$_SESSION['lang']['no']."</td>\r\n\t\t\t\t\t\t   <td>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t\t   <td>".$_SESSION['lang']['statusblok']."</td>\r\n\t\t\t\t\t\t   \r\n\t\t\t\t\t\t   <td>".$_SESSION['lang']['jumlah']." (Rp.)</td></tr>\r\n                       </thead><tbody>";
        $no = 0;
        $tot = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $proporsi = $bar->luasareaproduktif / $cLuas['luasdivisi'] * $_POST['jumlah'];
            echo "<tr class=rowcontent>\r\n\t\t\t\t\t\t<td class=firsttd>".$no."</td>\r\n\t\t\t\t\t\t<td>".$bar->kodeorg."</td>\r\n\t\t\t\t\t\t<td>".$bar->statusblok."</td>\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td align=right>".number_format($proporsi)."</td>\r\n\t\t\t\t\t</tr>";
            $tot += $proporsi;
        }
        echo '<tr><td colspan=3>'.$_SESSION['lang']['total'].'</td><td align=right>'.number_format($tot).'</td></tr>';
        echo '</tbody><tfoot></tfoot></fieldset>';

        break;
    case 'simpanIDC':
        $aLuas = 'select sum(luasareaproduktif) as luasdivisi from '.$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'";
        $bLuas = mysql_query($aLuas);
        $cLuas = mysql_fetch_assoc($bLuas);
        $str = 'select kodeorg,statusblok,luasareaproduktif from '.$dbname.".setup_blok where kodeorg like '".$_POST['kodeorg']."%'";
        $res = mysql_query($str);
        $jumblok = mysql_num_rows($res);
        if ($jumblok < 1) {
            exit(' Error: Tidak ada blok yang dapat dialokasi');
        }

        $nojurnal = tanggalsystem($_POST['tanggal']).'/'.$_POST['kodeorg'].'/IDC/001';
        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => 'IDC', 'tanggal' => tanggalsystem($_POST['tanggal']), 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $_POST['jumlah'], 'totalkredit' => $_POST['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => $_POST['nokas'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $noUrut = 1;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($_POST['tanggal']), 'nourut' => $noUrut, 'noakun' => $_POST['kredit'], 'keterangan' => 'Alokasi IDC:'.$_POST['tanggal'], 'jumlah' => -1 * $_POST['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_POST['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['nokas'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        while ($bar = mysql_fetch_object($res)) {
            $proporsi = $bar->luasareaproduktif / $cLuas['luasdivisi'] * $_POST['jumlah'];
            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => tanggalsystem($_POST['tanggal']), 'nourut' => $noUrut, 'noakun' => $_POST['debet'], 'keterangan' => 'Alokasi IDC:'.$_POST['tanggal'], 'jumlah' => $proporsi, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $_POST['kodeorg'], 'kodekegiatan' => $_POST['debet'].'01', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $_POST['nokas'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $bar->kodeorg, 'revisi' => '0'];
            ++$noUrut;
        }
        $errorDB = '';
        $queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
        if (!mysql_query($queryH)) {
            $errorDB .= 'Header :'.mysql_error()."\n".$queryH;
        }

        if ('' === $errorDB) {
            foreach ($dataRes['detail'] as $key => $dataDet) {
                $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
                if (!mysql_query($queryD)) {
                    $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
                }
            }
        }

        if ('' !== $errorDB) {
            $where = "nojurnal='".$nojurnal."'";
            $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
            if (!mysql_query($queryRB)) {
                $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
            }
        }

        break;
    case 'hapusJurnal':
        $tg = substr($_POST['tanggal'], 0, 7);
        $str = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_POST['kodeorg']."' and periode='".$tg."' and tutupbuku=0";
        $res = mysql_query($str);
        if (mysql_num_rows($res) < 1) {
            exit(' Error: Periode tersebut unit telah tutup buku');
        }

        $str = 'delete from '.$dbname.".keu_jurnalht where nojurnal='".$_POST['nojurnal']."'";
        if (mysql_query($str)) {
            break;
        }

        exit(' Error: '.mysql_error($conn));
}

?>