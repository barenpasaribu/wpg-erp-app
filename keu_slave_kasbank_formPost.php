<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/terbilang.php';
$notrans = $_POST['notrans'];
$kodeorg = $_POST['kodeorg'];
$noakun = $_POST['noakun'];
$tipetransaksi = $_POST['tipetransaksi'];
$method = $_POST['method'];
$numRow = $_POST['numRow'];
$page=$_POST['page'];
switch ($method) {
    case 'getFormPost':
        $cols = [];
        $x = 'select * from '.$dbname.".keu_kasbankht where notransaksi='".$notrans."'";
		#echo $x;
        $y = mysql_query($x);
        $param = mysql_fetch_assoc($y);
		#pre($param);
//        $whereH = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $whereH = "notransaksi='".$param['notransaksi']."' ";//and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $queryH = selectQuery($dbname, 'keu_kasbankht', '*', $whereH);
        $resH = fetchData($queryH);
        $userId = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "karyawanid='".$resH[0]['userid']."'");
        $namaakunhutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', "noakun='".$resH[0]['noakunhutang']."'");
        $col1 = 'noakun,jumlah,noaruskas,matauang,kode,nik,keterangan2,kodesupplier';
        $cols = ['nomor', 'noakun', 'namaakun', 'matauang', 'keterangan', 'kodesupplier', 'debet', 'kredit'];
//        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $where = "notransaksi='".$param['notransaksi']."' ";//and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $query = selectQuery($dbname, 'keu_kasbankdt', $col1, $where);
		
		#echo $query;
        $res = fetchData($query);
        $kary = $supp = [];
        foreach ($res as $row) {
            if (!empty($row['nik'])) {
                $kary[$row['nik']] = $row['nik'];
            }

            if (!empty($row['kodesupplier'])) {
                $supp[$row['kodesupplier']] = $row['kodesupplier'];
            }
        }
        if (empty($res)) {
            echo 'Data Empty';
            exit();
        }

        $whereAkun = 'noakun in (';
        $whereAkun .= "'".$resH[0]['noakun']."'";
        $whereAkun .= ",'".$resH[0]['noakunhutang']."'";
        $whereKary = $whereSupp = '';
        foreach ($res as $key => $row) {
            if (!empty($whereKary)) {
                $whereKary .= ',';
            }

            if (!empty($whereSupp)) {
                $whereSupp .= ',';
            }

            $whereAkun .= ",'".$row['noakun']."'";
            $whereKary .= "'".$row['nik']."'";
            $whereSupp .= "'".$row['kodesupplier']."'";
        }
        $whereAkun .= ')';
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'karyawanid in ('.$whereKary.')');
        $optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'supplierid in ('.$whereSupp.')');
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
        $optHutangUnit = ['Tidak', 'Ya'];
        $data = [];
        $totalDebet = 0;
        $totalKredit = 0;
        $i = 1;
        $data[$i] = ['nomor' => $i, 'noakun' => $resH[0]['noakun'], 'namaakun' => $optAkun[$resH[0]['noakun']], 'matauang' => $resH[0]['matauang'], '-' => '', '- ' => '', 'debet' => 0, 'kredit' => 0];
        if ('M' == $param['tipetransaksi']) {
            $data[$i]['debet'] = $resH[0]['jumlah'];
            $totalDebet += $resH[0]['jumlah'];
        } else {
            $data[$i]['kredit'] = $resH[0]['jumlah'];
            $totalKredit += $resH[0]['jumlah'];
        }

        ++$i;
        foreach ($res as $row) {
            $data[$i] = ['nomor' => $i, 'noakun' => $row['noakun'], 'namaakun' => (isset($optAkun[$row['noakun']]) ? $optAkun[$row['noakun']] : ''), 'matauang' => $row['matauang'], 'keterangan2' => $row['keterangan2'], 'kodesupplier' => $row['kodesupplier'], 'debet' => 0, 'kredit' => 0];
            if ('M' == $param['tipetransaksi'] && 0 < $row['jumlah']) {
                $data[$i]['kredit'] = $row['jumlah'];
                $totalKredit += $row['jumlah'];
            } else {
                if ('K' == $param['tipetransaksi'] && $row['jumlah'] < 0) {
                    $data[$i]['kredit'] = $row['jumlah'] * -1;
                    $totalKredit += $row['jumlah'] * -1;
                } else {
                    if ('M' == $param['tipetransaksi'] && $row['jumlah'] < 0) {
                        $data[$i]['debet'] = $row['jumlah'] * -1;
                        $totalDebet += $row['jumlah'] * -1;
                    } else {
                        $data[$i]['debet'] = $row['jumlah'];
                        $totalDebet += $row['jumlah'];
                    }
                }
            }

            ++$i;
        }
        if (!empty($data)) {
            foreach ($data as $c => $key) {
                $sort_debet[] = $key['debet'];
                $sort_kredit[] = $key['kredit'];
            }
        }

        if (!empty($data)) {
            array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);
        }

        $align = explode(',', 'R,R,L,L,R,R');
        $length = explode(',', '7,12,35,10,18,18');
        $title = $_SESSION['lang']['kasbank'];
        $titleDetail = 'Detail';
        $tab .= '<link rel=stylesheet type=text/css href=style/generic.css>';
        $tab .= '<fieldset style="height:200px;width:760px;overflow:scroll;"><legend>'.$titleDetail.' '.$title.'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><tbody class=rowcontent>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeorganisasi'].'</td><td> :</td><td> '.$_SESSION['empl']['lokasitugas'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['notransaksi'].'</td><td> :</td><td> '.$res[0]['kode'].'/'.$param['notransaksi'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['cgttu'].'</td><td> :</td><td> '.$resH[0]['cgttu'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['terbilang'].'</td><td> :</td><td> '.terbilang($resH[0]['jumlah'], 2).' rupiah'.'</td></tr>';
        if (1 == $resH[0]['hutangunit']) {
            $tab .= '<tr><td>'.$_SESSION['lang']['hutangunit'].'</td><td> :</td><td> '.'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']].'</td></tr>';
        }

        $tab .= '</tbody></table><br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><thead><tr class=rowheader>';
        foreach ($cols as $column) {
            $tab .= '<td>'.$_SESSION['lang'][$column].'</td>';
        }
        $tab .= '</tr></thead><tbody class=rowcontent>';
        $nyomor = 0;
        foreach ($data as $key => $row) {
            ++$nyomor;
            $tab .= '<tr>';
            foreach ($row as $key => $cont) {
                if ('nomor' == $key) {
                    $tab .= '<td>'.$nyomor.'</td>';
                } else {
                    if ('kodesupplier' == $key) {
                        $tab .= '<td>'.$optSupp[$cont].'</td>';
                    } else {
                        if ('debet' == $key || 'kredit' == $key) {
                            $tab .= '<td align=right>'.number_format($cont, 0).'</td>';
                        } else {
                            $tab .= '<td>'.$cont.'</td>';
                        }
                    }
                }
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr><td colspan=6 align=center>Total</td><td align=right>'.number_format($totalDebet, 0).'</td><td align=right>'.number_format($totalKredit, 0).'</td></tr>';
        $tab .= '</tbody></table></fieldset> <br />';
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable><tbody class=rowcontent>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['nobayar']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td  colspan=2><input type=text id=nobayar onkeypress=\"return tanpa_kutip(event);\" class=myinputtext  style=\"width:150px;\"></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t\t\t<td><input type=text class=myinputtext readonly  id=tglpost onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:150px;\"/></td>\r\n\t\t\t\t\t\t\t\t<td><button class=mybutton ".
            "onclick=savePosting('".$notrans."','".$tipetransaksi."',".$page.")>Simpan</button></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t</table>\r\n\t\t\t\t\t\t";
//        "onclick=savePosting('".$notrans."','".$kodeorg."','".$noakun."','".$tipetransaksi."','".$numRow."')>Simpan</button></td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t</table>\r\n\t\t\t\t\t\t";
        echo $tab;

        break;
}
echo "\t";

?>