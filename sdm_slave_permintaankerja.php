<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$param = $_POST;
$proses = $_POST['proses'];
$optNmdept = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optNmpend = makeOption($dbname, 'sdm_5pendidikan', 'idpendidikan,kelompok');
switch ($proses) {
    case 'loadData':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['namalowongan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['penempatan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tgldibutuhkan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kotapenempatan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['pendidikan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jurusan'].'</td>';
        $tab .= '<td colspan=3>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 30;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' != $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        if ('' != $param['tahun']) {
            $whr = " and tanggal like '".$param['tahun']."%'";
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.".sdm_permintaansdm where pembuat='".$_SESSION['standard']['userid']."' ".$whr."\r\n                order by tanggal desc limit ".$offset.','.$limit.' ';
        $qdata = mysql_query($sdata);
        $rowdata = mysql_num_rows($qdata);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$rdata['namalowongan'].'</td>';
            $tab .= '<td>'.$rdata['kodeorg'].'</td>';
            $tab .= '<td>'.$rdata['penempatan'].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tgldibutuhkan']).'</td>';
            $tab .= '<td>'.$rdata['kotapenempatan'].'</td>';
            $tab .= '<td>'.$optNmpend[$rdata['pendidikan']].'</td>';
            $tab .= '<td>'.$rdata['jurusan'].'</td>';
            if (0 == $rdata['stpersetujuanhrd']) {
                $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['notransaksi']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
                $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['notransaksi']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
                $tab .= "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
            } else {
                $tab .= "<td colspan=3><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
            }

            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=11 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.".sdm_permintaansdm where pembuat='".$_SESSION['standard']['userid']."' order by tanggal desc";
        $qpage = mysql_query($spage);
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' == $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal == $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='loadData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='loadData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot>';
        $tab .= '</table>';
        echo $tab;

        break;
    case 'cariData':
        if ('' != $param['sNoTrans']) {
            $tgl = explode('-', $param['sNoTrans']);
            $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
            $where .= " and tanggal like '%".$param['tanggal']."%'";
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['namalowongan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['penempatan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tgldibutuhkan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kotapenempatan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['pendidikan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jurusan'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 30;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' != $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.".sdm_permintaansdm where pembuat='".$_SESSION['standard']['userid']."' order by tanggal desc limit ".$offset.','.$limit.' ';
        $qdata = mysql_query($sdata);
        $rowdata = mysql_num_rows($qdata);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$rdata['namalowongan'].'</td>';
            $tab .= '<td>'.$rdata['kodeorg'].'</td>';
            $tab .= '<td>'.$rdata['penempatan'].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tgldibutuhkan']).'</td>';
            $tab .= '<td>'.$rdata['kotapenempatan'].'</td>';
            $tab .= '<td>'.$optNmpend[$rdata['pendidikan']].'</td>';
            $tab .= '<td>'.$rdata['jurusan'].'</td>';
            if (0 == $rdata['stpersetujuanhrd']) {
                $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['notransaksi']."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
                $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['notransaksi']."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
                $tab .= "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
            } else {
                $tab .= "<td colspan=3><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
            }

            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=10 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='cariData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='cariData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.".sdm_permintaansdm where pembuat='".$_SESSION['standard']['userid']."' order by tanggal desc";
        $qpage = mysql_query($spage);
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='cariData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' == $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal == $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='cariData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='cariData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot>';
        $tab .= '</table>';
        echo $tab;

        break;
    case 'insert':
        if ('' == $param['nmlowongan']) {
            exit('error: Nama Lowongan tidak boleh kosong!!');
        }

        if ('' == $param['tgldibutuhkan']) {
            exit('error: Tanggal dibutuhkan tidak boleh kosong!!');
        }

        if ('' == $param['kodeorg']) {
            exit('error: Unit Peminta tidak boleh kosong!!');
        }

        if ('' == $param['penempatan']) {
            exit('error: Unit Penempatan tidak boleh kosong!!');
        }

        if ('' == $param['pendidikan']) {
            exit('error: Pendidiakn tidak boleh kosong!!');
        }

        if ('' == $param['departemen']) {
            exit('error: Departement tidak boleh kosong!!');
        }

        if ('' == $param['persetujuanhrd']) {
            exit('error: Persetujuan HRD tidak boleh kosong!!');
        }

        if ('' == $param['persetujuan1']) {
            exit('error: Persetujuan Pertama tidak boleh kosong!!');
        }

        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $tglb = explode('-', $param['tgldibutuhkan']);
        $param['tgldibutuhkan'] = $tglb[2].'-'.$tglb[1].'-'.$tglb[0];
        $scek2 = 'select distinct * from '.$dbname.".sdm_permintaansdm where namalowongan like '%".$param['nmlowongan']."%'";
        $qcek2 = mysql_query($scek2);
        $rcek2 = mysql_num_rows($qcek2);
        if (0 != $rcek2) {
            exit('error: Nama lowongan sudah ada!!');
        }

        $sins = 'insert into '.$dbname.".sdm_permintaansdm  \r\n            (`notransaksi`,`namalowongan` ,`kodeorg` ,`penempatan` ,`departemen` ,`tanggal` ,`tgldibutuhkan` ,`kotapenempatan` ,`pendidikan` ,`jurusan` ,`pengalaman` ,`kompetensi` ,`deskpekerjaan` ,`maxumur`, `pembuat` ,`persetujuan1`,`persetujuan2`,`persetujuanhrd`,`jumlah_kebutuhan`)\r\n            values \r\n            (NULL,'".$param['nmlowongan']."','".$param['kodeorg']."','".$param['penempatan']."','".$param['departemen']."','".$param['tanggal']."','".$param['tgldibutuhkan']."','".$param['kotapenempatan']."','".$param['pendidikan']."','".$param['jurusan']."','".$param['pengalaman']."','".$param['kompetensi']."','".$param['deskpekerjaan']."','".$param['maxumur']."','".$_SESSION['standard']['userid']."','".$param['persetujuan1']."','".$param['persetujuan2']."','".$param['persetujuanhrd']."','".$param['jmlhPersoanl']."')";
        if (!mysql_query($sins)) {
            exit('error:'.mysql_error($conn).'__'.$sins);
        }

        break;
    case 'update':
        $scek2 = 'select distinct * from '.$dbname.".sdm_permintaansdm where pendidikan='".$param['pendidikan']."' and tanggal='".$param['tanggal']."'\r\n               and kodeorg='".$param['kodeorg']."' and penempatan = '".$param['penempatan']."' and departemen='".$param['departemen']."' \r\n               and pembuat='".$_SESSION['standard']['userid']."'";
        $qcek2 = mysql_query($scek2);
        $rcek2 = mysql_num_rows($qcek2);
        if (0 != $rcek2) {
            exit('error: Data Sudah Udah');
        }

        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $tglb = explode('-', $param['tgldibutuhkan']);
        $param['tgldibutuhkan'] = $tglb[2].'-'.$tglb[1].'-'.$tglb[0];
        $sins = 'update '.$dbname.".sdm_permintaansdm  set `namalowongan`='".$param['nmlowongan']."',`kodeorg`='".$param['kodeorg']."' ,`penempatan`='".$param['penempatan']."' ,\r\n          `departemen`='".$param['departemen']."' ,`tanggal`='".$param['tanggal']."' ,`tgldibutuhkan`='".$param['tgldibutuhkan']."' ,\r\n          `kotapenempatan`='".$param['kotapenempatan']."' ,`pendidikan`='".$param['pendidikan']."' ,`jurusan`='".$param['jurusan']."' ,\r\n          `pengalaman`='".$param['pengalaman']."' ,`kompetensi`='".$param['kompetensi']."' ,`deskpekerjaan`='".$param['deskpekerjaan']."' ,\r\n          `maxumur`='".$param['maxumur']."', `pembuat`='".$_SESSION['standard']['userid']."' ,`persetujuan1`='".$param['persetujuan1']."',\r\n           `persetujuan2`='".$param['persetujuan2']."',`persetujuanhrd`='".$param['persetujuanhrd']."',`jumlah_kebutuhan`='".$param['jmlhPersoanl']."'\r\n             where notransaksi='".$param['notransaksi']."'";
        if (!mysql_query($sins)) {
            exit('error:'.mysql_error($conn).'__'.$sins);
        }

        break;
    case 'getData':
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $str = 'select distinct * from '.$dbname.".sdm_permintaansdm \r\n          where notransaksi='".$param['notransaksi']."'";
        $qstr = mysql_query($str);
        $rts = mysql_fetch_assoc($qstr);
        echo $rts['notransaksi'].'###'.$rts['kodeorg'].'###'.$rts['penempatan'].'###'.$rts['departemen'].'###'.$rts['kotapenempatan'].'###'.tanggalnormal($rts['tanggal']).'###'.tanggalnormal($rts['tgldibutuhkan']).'###'.$rts['pendidikan'].'###'.$rts['jurusan'].'###'.$rts['pengalaman'].'###'.$rts['kompetensi'].'###'.$rts['deskpekerjaan'].'###'.$rts['maxumur'].'###'.$rts['persetujuan1'].'###'.$rts['persetujuan2'].'###'.$rts['persetujuanhrd'].'###'.$rts['namalowongan'];

        break;
    case 'delete':
        $query = 'delete from `'.$dbname."`.`sdm_permintaansdm` where notransaksi='".$param['notransaksi']."'";
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
}

?>