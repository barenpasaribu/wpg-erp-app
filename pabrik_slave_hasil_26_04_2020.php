<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'posting':
        $data = $_POST;
        $where = "notransaksi='".$data['notransaksi']."'";
        $query = updateQuery($dbname, 'pabrik_masukkeluartangki', ['posting' => '1'], $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'showHeadList':
        $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";
        if (isset($param['where'])) {
            $arrWhere = json_decode($param['where'], true);
            if (!empty($arrWhere)) {
                foreach ($arrWhere as $key => $r1) {
                    if (0 === $key) {
                        $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                    } else {
                        $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                    }
                }
            } else {
                $where .= null;
            }
        } else {
            $where .= null;
        }

        $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['pabrik'], $_SESSION['lang']['kodetangki'], $_SESSION['lang']['kwantitas'], $_SESSION['lang']['kernelquantity'], $_SESSION['lang']['suhu']];
        $cols = 'notransaksi,tanggal,kodeorg,kodetangki,kuantitas,kernelquantity,suhu,posting';
        $query = selectQuery($dbname, 'pabrik_masukkeluartangki', $cols, $where, '', false, $param['shows'], $param['page']);
        $data = fetchData($query);
        $totalRow = getTotalRow($dbname, 'pabrik_masukkeluartangki', $where);
        foreach ($data as $key => $row) {
            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            if (1 === $row['posting']) {
                $data[$key]['switched'] = true;
            }

            unset($data[$key]['posting']);
        }
        $x = 'select kodejabatan from '.$dbname.".sdm_5jabatan where alias like '%ka.%' or alias like '%kepala%' or alias like '%Mill'";
        $y = mysql_query($x);
        while ($z = mysql_fetch_assoc($y)) {
            $pos = $z['kodejabatan'];
            if ($pos === $_SESSION['empl']['kodejabatan']) {
                $flag = 1;
            }
        }
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
        $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
        $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
        if (1 !== $flag) {
            $tHeader->_actions[2]->_name = '';
        }

        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        if (isset($param['where'])) {
            $tHeader->setWhere($arrWhere);
        }

        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', []);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $query = selectQuery($dbname, 'pabrik_masukkeluartangki', '*', "notransaksi='".$param['notransaksi']."'");
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        echo formHeader('edit', $data);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data = $_POST;
        $warning = '';
        if ('' === $data['notransaksi']) {
            $warning .= "No Transaksi harus diisi\n";
        }

        if ('' === $data['tanggal']) {
            $warning .= "Tanggal harus diisi\n";
        }

        if ('' !== $warning) {
            echo "Warning :\n".$warning;
            exit();
        }

        $tgl = explode('-', $data['tanggal']);
        $tglck = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $tglKmrn = strtotime('-1 day', strtotime($tglck));
        $tglKmrn = date('Y-m-d', $tglKmrn);
        $data['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0].' '.$data['jam'].':'.$data['jam_menit'];
        unset($data['notransaksi'], $data['jam'], $data['jam_menit']);

        $cols = ['tanggal', 'kodeorg', 'kodetangki', 'kuantitas', 'suhu', 'cpoffa', 'cpokdair', 'cpokdkot', 'kernelquantity', 'kernelkdair', 'kernelkdkot', 'kernelffa', 'tinggi'];
        $query = insertQuery($dbname, 'pabrik_masukkeluartangki', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'edit':
        $data = $_POST;
        $where = "notransaksi='".$data['notransaksi']."'";
        unset($data['notransaksi']);
        $tgl = explode('-', $data['tanggal']);
        $tglck = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $tglKmrn = strtotime('-1 day', strtotime($tglck));
        $tglKmrn = date('Y-m-d', $tglKmrn);
        if ('BLK' !== substr($data['kodetangki'], 0, 3)) {
            $whrcek = "kodeorg='".$data['kodeorg']."' and left(tanggal,10)='".$tglKmrn."' and kodetangki='".$data['kodetangki']."'";
            $optcek = makeOption($dbname, 'pabrik_masukkeluartangki', 'kodetangki,kuantitas', $whrcek);
            if ('' === $optcek[$data['kodetangki']]) {
                exit('error: Sounding data for '.$tglKmrn.' is empty!');
            }
        } else {
            if ('' === $data['kernelquantity']) {
                exit('error: '.$_SESSION['lang']['kernelquantity']." can't empty");
            }
        }

        $data['tanggal'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0].' '.$data['jam'].':'.$data['jam_menit'];
        unset($data['jam'], $data['jam_menit']);

        $query = updateQuery($dbname, 'pabrik_masukkeluartangki', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."'";
        $query = 'delete from `'.$dbname.'`.`pabrik_masukkeluartangki` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'getVolume':

    $tinggi=$param['tinggi'];

    $sql = "SELECT mejaukur FROM pabrik_5tangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."'";
    $query=mysql_query($sql);
    $res=mysql_fetch_assoc($query);

    $mejaukur=$res['mejaukur'];
    $tinggi1=floor($tinggi+$mejaukur);
    $tinggi2=$tinggi+$mejaukur;
    $tinggi3=explode(".", $tinggi2);
    $tinggi4=$tinggi3[1];

    $sql1 = "SELECT volume FROM pabrik_5vtangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tinggicm='".$tinggi1."' and kodetangki='".$param['kodetangki']."'";
    $query1=mysql_query($sql1);
    $res1=mysql_fetch_assoc($query1);

    $volummeter=$res1['volume'];

    $sql2 = "SELECT a.nilai FROM pabrik_5cincindt a inner join pabrik_5cincinht b on a.cincinid=b.cincinid where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' AND (awal<='".$tinggi1."' AND akhir>='".$tinggi1."') AND detailid='".$tinggi4."' ";
    $query2=mysql_query($sql2);
    $res2=mysql_fetch_assoc($query2);

    $volummili=$res2['nilai'];

    $volum=$volummeter+$volummili;

    $sql3 = "SELECT kepadatan,ketetapan FROM pabrik_5ketetapansuhu where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' AND suhu='".$param['suhu']."' ";
    $query3=mysql_query($sql3);
    $res3=mysql_fetch_assoc($query3);

    $faktorkoreksi=$res3['ketetapan'];
    $density=$res3['kepadatan'];

    $tonase=$volum*$faktorkoreksi*$density;
    echo round($tonase);

/*      $b = floor($param['tinggi']);
        $c = $param['tinggi'] - $b;
        $d = $b + 1;


        $whr = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$param['tinggi']."'";
        $optVol = makeOption($dbname, 'pabrik_5vtangki', 'kodetangki,volume', $whr);
        $whr2 = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$d."'";
        $optVol2 = makeOption($dbname, 'pabrik_5vtangki', 'kodetangki,volume', $whr2);
        $g = $optVol[$param['kodetangki']];
        $tingg = $optVol[$param['kodetangki']];
        if (0 === $g) {
            $tinggiRendah = (int) ($param['tinggi']);
            $tinggiDiantaranya = $tinggiRendah + 1;
            $diffTinggi = $param['tinggi'] - $tinggiRendah;
            $whr = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$tinggiRendah."'";
            $optVol = makeOption($dbname, 'pabrik_5vtangki', 'kodetangki,volume', $whr);
            $whr2 = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and tinggicm='".$tinggiDiantaranya."'";
            $optVol2 = makeOption($dbname, 'pabrik_5vtangki', 'kodetangki,volume', $whr2);
            $xTinggi = ($optVol2[$param['kodetangki']] - $optVol[$param['kodetangki']]) * $diffTinggi;
            $tingg = $optVol[$param['kodetangki']] + $xTinggi;
        }

        $j = $param['suhu'];
        $whr3 = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."' and suhu='".$j."'";
        $k = makeOption($dbname, 'pabrik_5ketetapansuhu', 'kodetangki,kepadatan', $whr3);
        $l = makeOption($dbname, 'pabrik_5ketetapansuhu', 'kodetangki,ketetapan', $whr3);
        if (0 === $k[$param['kodetangki']] || '' === $k[$param['kodetangki']]) {
        }

        $jumlah = $tingg * round($l[$param['kodetangki']], 4);
        echo round($jumlah);
*/



        break;
    default:
        break;
}
function formHeader($mode, $data)
{
    global $dbname;
    if (empty($data)) {
        $data['notransaksi'] = '0';
        $data['kodeorg'] = '';
        $data['tanggal'] = '';
        $data['kodetangki'] = '';
        $data['kuantitas'] = '0';
        $data['suhu'] = '0';
        $data['cpoffa'] = '0';
        $data['cpokdair'] = '0';
        $data['cpokdkot'] = '0';
        $data['kernelquantity'] = '0';
        $data['kernelkdair'] = '0';
        $data['kernelkdkot'] = '0';
        $data['kernelffa'] = '0';
    }

    if ('edit' === $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $whrTngki = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
    $optTangki = makeOption($dbname, 'pabrik_5tangki', 'kodetangki,kodetangki,keterangan', $whrTngki, '5');
    $tgl = explode(' ', $data['tanggal']);
    if ('' === $tgl[0]) {
        $tgl[0] = date('Y-m-d');
    }

    $data['tanggal'] = tanggalnormal($tgl[0]);
    $els = [];
    $els[] = [makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], ['style' => 'width:200px', 'maxlength' => '12', 'disabled' => 'disabled'])];
    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
    $els[] = [makeElement('jam', 'label', $_SESSION['lang']['jam']), makeElement('jam', 'jammenit', $tgl[1])];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:300px'], $optOrg)];
    $els[] = [makeElement('kodetangki', 'label', $_SESSION['lang']['kodetangki']), makeElement('kodetangki', 'select', $data['kodetangki'], ['style' => 'width:200px', 'onchange' => 'getVolCpo()'], $optTangki)];
    $els[] = [makeElement('suhu', 'label', $_SESSION['lang']['suhu']), makeElement('suhu', 'textnumw-', $data['suhu'], ['style' => 'width:100px', 'maxlength' => '4', 'onblur' => 'getVolCpo()']).'C'];
    $els[] = [makeElement('tinggi', 'label', $_SESSION['lang']['tinggi']), makeElement('tinggi', 'textnum', $data['tinggi'], ['style' => 'width:100px', 'onblur' => 'getVolCpo()']).'cm'];
    $els[] = [makeElement('kuantitas', 'label', $_SESSION['lang']['cpokuantitas']), makeElement('kuantitas', 'textnum', $data['kuantitas'], ['style' => 'width:100px']).'kg'];
    $els[] = [makeElement('cpoffa', 'label', $_SESSION['lang']['cpoffa']), makeElement('cpoffa', 'textnum', $data['cpoffa'], ['style' => 'width:100px']).'%'];
    $els[] = [makeElement('cpokdair', 'label', $_SESSION['lang']['cpokdair']), makeElement('cpokdair', 'textnum', $data['cpokdair'], ['style' => 'width:100px']).'%'];
    $els[] = [makeElement('cpokdkot', 'label', $_SESSION['lang']['cpokdkot']), makeElement('cpokdkot', 'textnum', $data['cpokdkot'], ['style' => 'width:100px']).'%'];
    $els[] = [makeElement('kernelquantity', 'label', $_SESSION['lang']['kernelquantity']), makeElement('kernelquantity', 'textnum', $data['kernelquantity'], ['style' => 'width:100px']).'kg'];
    $els[] = [makeElement('kernelkdair', 'label', $_SESSION['lang']['kernelkdair']), makeElement('kernelkdair', 'textnum', $data['kernelkdair'], ['style' => 'width:100px']).'%'];
    $els[] = [makeElement('kernelkdkot', 'label', $_SESSION['lang']['kernelkdkot']), makeElement('kernelkdkot', 'textnum', $data['kernelkdkot'], ['style' => 'width:100px']).'%'];
    $els[] = [makeElement('kernelffa', 'label', $_SESSION['lang']['kernelffa']), makeElement('kernelffa', 'textnum', $data['kernelffa'], ['style' => 'width:100px']).'%'];
    if ('add' === $mode) {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];
    } else {
        if ('edit' === $mode) {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];
        }
    }

    if ('add' === $mode) {
        return genElementMultiDim($_SESSION['lang']['addheader'].'(Data sounding)', $els, 3);
    }

    if ('edit' === $mode) {
        return genElementMultiDim($_SESSION['lang']['editheader'].'(Data  sounding)', $els, 3);
    }
}

?>