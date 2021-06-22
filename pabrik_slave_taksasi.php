<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$param = $_POST;
$proses = $_POST['proses'];
$sorg = 'select distinct kodetimbangan,namasupplier from '.$dbname.".log_5supplier where kodetimbangan like '1%' order by namasupplier";
$qorg = mysql_query($sorg);
while ($rorg = mysql_fetch_assoc($qorg)) {
    $kamuscust[$rorg['kodetimbangan']] = $rorg['namasupplier'];
}
switch ($proses) {
    case 'loadData':
        $where = 'afdeling in (select distinct kodetimbangan from '.$dbname.".log_5supplier where kodetimbangan like '1%' order by namasupplier)";
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['nmcust'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kg'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' !== $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.' order by tanggal desc limit '.$offset.','.$limit.' ';
        $qdata = mysql_query($sdata);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$kamuscust[$rdata['afdeling']].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td align=right>'.$rdata['kg'].'</td>';
            $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=10 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.'';
        $qpage = mysql_query($spage);
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' === $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal === $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='loadData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='loadData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot></table>';
        echo $tab;

        break;
    case 'cariData':
        if ('' !== $param['sNoTrans']) {
            $tgl = explode('-', $param['sNoTrans']);
            $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
            $where .= " tanggal like '%".$param['tanggal']."%'";
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        $tab .= '<td>'.$_SESSION['lang']['nmcust'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kg'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' !== $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.' order by tanggal desc limit '.$offset.','.$limit.' ';
        $qdata = mysql_query($sdata);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $tab .= '<tr class=rowcontent align=center>';
            $tab .= '<td>'.$kamuscust[$rdata['afdeling']].'</td>';
            $tab .= '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            $tab .= '<td align=right>'.$rdata['kg'].'</td>';
            $tab .= "<td><img title=\"Edit\" onclick=\"showEdit('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/edit.png\"></td>";
            $tab .= "<td><img title=\"Delete\" onclick=\"deleteData('".$rdata['afdeling']."','".tanggalnormal($rdata['tanggal'])."')\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>";
            $tab .= '</tr>';
        }
        $tab .= '</tbody><tfoot>';
        $tab .= '<tr>';
        $tab .= '<td colspan=10 align=center>';
        $tab .= "<img src=\"images/skyblue/first.png\" onclick='cariData(0)' style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/prev.png\" onclick='cariData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.'.kebun_taksasi where '.$where.'';
        $qpage = mysql_query($spage);
        $rpage = mysql_num_rows($qpage);
        $tab .= "<select id='pages' style='width:50px' onchange='cariData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' === $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $tab .= "<option value='".$starAwal."' ".(($starAwal === $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $tab .= '</select>';
        $tab .= "<img src=\"images/skyblue/next.png\" onclick='cariData(".($page + 1).")'  style='cursor:pointer'>";
        $tab .= "<img src=\"images/skyblue/last.png\" onclick='cariData(".(int) $totalPage.")'  style='cursor:pointer'>";
        $tab .= '</td></tr></tfoot></table>';
        $cols = 'notransaksi,tanggal,kodeorg,kodetangki,kuantitas,suhu';
        echo $tab;

        break;
    case 'insert':
        ('' === $param['kg'] ? $param['kg'] : $param['kg']);
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $scek2 = 'select distinct * from '.$dbname.".kebun_taksasi where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
        $qcek2 = mysql_query($scek2);
        $rcek2 = mysql_num_rows($qcek2);
        if (0 !== $rcek2) {
            $sins = 'update '.$dbname.".kebun_taksasi  set `kg`='".$param['kg']."'\r\n             where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
            if (!mysql_query($sins)) {
                exit('error:'.mysql_error($conn).'__'.$sins);
            }
        } else {
            $scek = 'select distinct * from '.$dbname.".kebun_taksasi \r\n              where tanggal='".$param['tanggal']."' and afdeling='".$param['customer']."'";
            $qcek = mysql_query($scek);
            $rcek = mysql_num_rows($qcek);
            if (0 !== $rcek) {
                exit('error:Data Sudah Ada');
            }

            $sins = 'insert into '.$dbname.".kebun_taksasi  \r\n            (`afdeling`,`tanggal`, `kg`)\r\n            values ('".$param['customer']."','".$param['tanggal']."','".$param['kg']."')";
            if (!mysql_query($sins)) {
                exit('error:'.mysql_error($conn).'__'.$sins);
            }
        }

        break;
    case 'getData':
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $str = 'select distinct * from '.$dbname.".kebun_taksasi \r\n          where tanggal='".$param['tanggal']."' and \r\n          afdeling='".$param['afdeling']."'";
        $qstr = mysql_query($str);
        $rts = mysql_fetch_assoc($qstr);
        echo $rts['afdeling'].'###'.tanggalnormal($rts['tanggal']).'###'.$rts['kg'];

        break;
    case 'delete':
        $tgl = explode('-', $param['tanggal']);
        $param['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $where = "tanggal='".$param['tanggal']."' and afdeling='".$param['afdeling']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_taksasi` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
}

?>