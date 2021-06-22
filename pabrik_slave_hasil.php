<?php
    require_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/rTable.php';
    echo "\r\n";
    $proses = $_GET['proses'];
    $param = $_POST;

    $optListTanggal = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $queryGetTanggalList = 'select distinct SUBSTRING(tanggal, 1, 10) AS tanggal from '.$dbname.'.pabrik_masukkeluartangki where posting = 0 and kodeorg = "'.$_SESSION['empl']['lokasitugas'].'"';
    $dataTanggalList = mysql_query($queryGetTanggalList);
    while ($d = mysql_fetch_assoc($dataTanggalList)) {
        $optListTanggal .= "<option value='".$d['tanggal']."'>".tanggalnormal($d['tanggal']).'</option>';
    }



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
            // $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            // if (isset($param['where'])) {
            //     $arrWhere = json_decode($param['where'], true);
            //     if (!empty($arrWhere)) {
            //         foreach ($arrWhere as $key => $r1) {
            //             if (0 === $key) {
            //                 $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
            //             } else {
            //                 $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
            //             }
            //         }
            //     } else {
            //         $where .= null;
            //     }
            // } else {
            //     $where .= null;
            // }
            // $where .= "order by tanggal desc, notransaksi desc";

            // $header = [$_SESSION['lang']['tanggal'], "No Transaksi", $_SESSION['lang']['pabrik'], $_SESSION['lang']['kodetangki'], $_SESSION['lang']['kwantitas'], $_SESSION['lang']['kernelquantity'], $_SESSION['lang']['suhu']];
            // $cols = 'tanggal,notransaksi,kodeorg,kodetangki,kuantitas,kernelquantity,suhu,posting';
            // $query = selectQuery($dbname, 'pabrik_masukkeluartangki', $cols, $where, '', false, 100, $param['page']);

            // $data = fetchData($query);
            // $totalRow = getTotalRow($dbname, 'pabrik_masukkeluartangki', $where);
            // foreach ($data as $key => $row) {
            //     $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            //     if (1 === $row['posting']) {
            //         $data[$key]['switched'] = true;
            //     }

            //     unset($data[$key]['posting']);
            // }
            // $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
            // $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
            // $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
            // $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
            // $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');

            // $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
            // if (isset($param['where'])) {
            //     $tHeader->setWhere($arrWhere);
            // }

            // $tHeader->renderTable();

            // break;
            ?>
            <table cellpading=1 cellspacing=1 class='sortable'>
            <thead>
				<tr class="rowheader">
					<td>No</td>
					<td>Waktu</td>
					<td>Pabrik</td>
					<td>Kode Tangki</td>
					<td>Suhu</td>
					<td>Tinggi</td>
					<td>Kuantitas CPO</td>
					<td>Kuantitas Kernel</td>
					<td>Action</td>
				</tr>
			</thead>
            <tbody id="container">
            <?php
            $limit = 20;
            $page = 0;
    
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }
            $offset = $page * $limit;

            

            $ql2 = 'SELECT count(*) as jumlahRow FROM ' . $dbname . '.pabrik_masukkeluartangki where kodeorg = "'.$_SESSION['empl']['lokasitugas'].'" order by tanggal desc';
            ($query2 = mysql_query($ql2)) || true;
    
            while ($jsl = mysql_fetch_object($query2)) {
                $jumlahRow = $jsl->jumlahRow;
            }
    
            $str = 'SELECT * FROM ' . $dbname . '.pabrik_masukkeluartangki where kodeorg = "'.$_SESSION['empl']['lokasitugas'].'" order by tanggal desc  limit ' . $offset . ',' . $limit . '';

            $no = ($page * $limit);
            if ($res = mysql_query($str)) {
                $barisData = mysql_num_rows($res);
                $disabled = '';
                if (0 < $barisData) {

                    while ($bar = mysql_fetch_object($res)) {
                        $no += 1;

                        echo '  <tr class=rowcontent id=\'tr_' . $no . '\'>';
                        echo '      <td>' . $no . '</td>';
                        echo '      <td>' . $bar->tanggal . '</td>';
                        echo '      <td>' . $bar->kodeorg . '</td>';
                        echo '      <td>' . $bar->kodetangki . '</td>';
                        if (empty($bar->suhu)) {
                            echo '      <td>-</td>';
                        } else {
                            echo '      <td>' . $bar->suhu . '</td>';
                        }
                        
                        
                        echo '      <td>' . $bar->tinggi . '</td>';
                        echo '      <td>' . $bar->kuantitas . '</td>';
                        echo '      <td>' . $bar->kernelquantity . '</td>';
                        if ($bar->posting == 1) {
                            echo '      <td style="text-align: center;">
                            <img src=images/skyblue/posted.png class=resicon title="Posted">
                                        </td>';
                        } else {
                            echo '      <td style="text-align: center;">
                                            <img src=images/skyblue/edit.png class=resicon title="Edit" onclick="showEdit(\'' . $bar->notransaksi . '\');">
                                            <img src=images/application/application_delete.png class=resicon title="Delete" onclick="deleteData(\'' . $bar->notransaksi . '\');">
                                            <img src=images/skyblue/posting.png class=resicon title="Posting" onclick="postingData(\'' . $bar->notransaksi . '\');">
                                        </td>';
                        }
                        
                        
                        echo "  </tr>";
                    }
                }
                else {
                    echo '<tr class=rowcontent>
                            <td colspan=11 style="text-align: center; padding-top: 10px; padding-bottom: 10px;">' . 
                            $_SESSION['lang']['dataempty'] . '
                            </td>
                        </tr>';
                    $disabled = 'disabled';
                }
                if($jumlahRow < $limit ){
                    $disabled = 'disabled';
                }
                echo '</tbody>';
                echo '<tfoot>';
                echo '<tr>
                        <td colspan=11 align=center>' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jumlahRow . '
                        <br/>
                        <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>
                        <button class=mybutton '.$disabled.' onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>
                        </td>
                    </tr>';
                echo '</tfoot>';
                echo '</table>';
            }
            else {
                echo ' Gagal,' . mysql_error($conn);
            }
            break;
        case 'showAdd':
            echo formHeader('add', []);
            echo "<div id='detailField' style='clear:both'></div>";
            echo "<fieldset>
                        <legend><b>Quick Posting</b></legend>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><select id=tanggal_quick_posting style=\"width:150px;\">".$optListTanggal."</select></td>
                            <td><button onclick=quickPosting() class=mybutton name=btnQuickPosting id=btnQuickPosting>Posting</button></td>
                        </tr>
                        
                    </fieldset><br>";
            echo "  <div id='susunanDataInput' style='clear:both'>
                        
                    </div>";

            break;
        case 'showEdit':
            $query = selectQuery($dbname, 'pabrik_masukkeluartangki', '*', "notransaksi='".$param['notransaksi']."'");
            $tmpData = fetchData($query);
            $data = $tmpData[0];
            echo formHeader('edit', $data);
            echo "<div id='detailField' style='clear:both'></div>";
            echo "<fieldset>
                        <legend><b>Quick Posting</b></legend>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td><select id=tanggal_quick_posting style=\"width:150px;\">".$optListTanggal."</select></td>
                            <td><button onclick=quickPosting() class=mybutton name=btnQuickPosting id=btnQuickPosting>Posting</button></td>
                        </tr>
                        
                    </fieldset><br>";
            echo "<div id='susunanDataInput' style='clear:both'></div>";
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
            $kode_tangki = $data['kodetangki'];

              $qlV = 'SELECT count(*) as jumlahRow FROM ' . $dbname . '.pabrik_masukkeluartangki where date(tanggal) = "'.$tglck.'" and kodetangki="'.$kode_tangki.'" and kodeorg = "'.$_SESSION['empl']['lokasitugas'].'" order by tanggal desc';
            ($queryV = mysql_query($qlV)) || true;
    
            while ($jsl = mysql_fetch_object($queryV)) {
                $jumlahRow = $jsl->jumlahRow;
            }

            if($jumlahRow >0){
                  echo "Warning :Sudah ada inputan untuk tanggal yang di pilih !\n";
                exit();
            }
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
            $kodeTangki = $_POST['kodetangki'];
            $suhu = $_POST['suhu'];
            $tinggi = $_POST['tinggi'];
            $lokasitugas = $_SESSION['empl']['lokasitugas'];

            $queryGetData5Tangki = "SELECT * FROM pabrik_5tangki where kodetangki ='".$kodeTangki."' AND kodeorg ='".$lokasitugas."'";
            $data5Tangki = fetchData($queryGetData5Tangki);

            $getVolumeTangkiByTinggi = "SELECT * FROM pabrik_5vtangki where tinggicm ='".$tinggi."' AND kodetangki ='".$kodeTangki."' AND kodeorg ='".$lokasitugas."'";
            $dataVolumeTangkiByTinggi = fetchData($getVolumeTangkiByTinggi);
            
            $getDensityBySuhu = "SELECT * FROM pabrik_5ketetapansuhu where suhu ='".$suhu."' AND kodetangki ='ST01' AND kodeorg ='".$lokasitugas."'";
            $dataDensityBySuhu = fetchData($getDensityBySuhu);


            $hasil = array();
            if(substr($kodeTangki, 0, 3) == "VCT" || substr($kodeTangki, 0, 3) == "POT"){
                $volumeDensity = $dataVolumeTangkiByTinggi[0]['volume'] * $dataDensityBySuhu[0]['kepadatan'];
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'CPO'
                ];
            }elseif(substr($kodeTangki, 0, 3) == "KSD" || substr($kodeTangki, 0, 2) == "KS"){
                // $tonase = (($tinggi*$data5Tangki[0]['luaspenampang'])-((3.14*25*25*100)/1000*$tinggi)+$data5Tangki[0]['volumekerucut'])*$data5Tangki[0]['density']*0.86;
                $volumeDensity = $dataVolumeTangkiByTinggi[0]['volume'] * $data5Tangki[0]['kadarair'] * $data5Tangki[0]['density'];
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'KERNEL'
                ];
            }elseif(substr($kodeTangki, 0, 2) == "OT" || substr($kodeTangki, 0, 3) == "CST"){
                // $tonase = (($tinggi*$data5Tangki[0]['luaspenampang'])-((3.14*25*25*100)/1000*$tinggi)+$data5Tangki[0]['volumekerucut'])*$data5Tangki[0]['density']*0.86;
                $volumeDensity = $dataVolumeTangkiByTinggi[0]['volume'];
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'CPO'
                ];
            }elseif(substr($kodeTangki, 0, 2) == "NB" || substr($kodeTangki, 0, 2) == "NS" ){
                // $tonase = (($tinggi*$data5Tangki[0]['luaspenampang'])-((3.14*25*25*100)/1000*$tinggi)+$data5Tangki[0]['volumekerucut'])*$data5Tangki[0]['density']*0.86;
                $volumeDensity = $dataVolumeTangkiByTinggi[0]['volume'] * $data5Tangki[0]['kadarair'] * $data5Tangki[0]['density'] * ($data5Tangki[0]['rasiokerneltonut'] / 100) ;
                
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'KERNEL'
                ];
            }elseif(substr($kodeTangki, 0, 3) == "KSB" || substr($kodeTangki, 0, 2) == "BS"){
                // $tonase = (($tinggi*$data5Tangki[0]['luaspenampang'])-((3.14*25*25*100)/1000*$tinggi)+$data5Tangki[0]['volumekerucut'])*$data5Tangki[0]['density']*0.86;
                // print_r($data5Tangki[0]['DENSITY']);
                // die();
                $volumeDensity = $dataVolumeTangkiByTinggi[0]['volume'] * $data5Tangki[0]['density'];
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'KERNEL'
                ];
            }elseif(substr($kodeTangki, 0, 2) == "NL"){
                $estimasiNut = $tinggi;
                
                $volumeDensity = ($data5Tangki[0]['rasiokerneltonut'] / 100) * $data5Tangki[0]['kadarair'] * $data5Tangki[0]['density'] * $estimasiNut;
        
                $hasil = [
                    'tonase' => round($volumeDensity),
                    'tipe'   => 'KERNEL'
                ];
            }elseif(substr($kodeTangki, 0, 2) == "KL" || substr($kodeTangki, 0, 5) == "MOBIL"){
                $hasil = [
                    'tonase' => round($tinggi),
                    'tipe'   => 'KERNEL'
                ];
            }elseif(substr($kodeTangki, 0, 5) == "TRUCKCPO"){
                $hasil = [
                    'tonase' => round($tinggi),
                    'tipe'   => 'CPO'
                ];
            }else{ 
                if ($param['tinggi'] == 0 && $param['suhu'] == 0) {
                    $hasil = [
                        'tonase' => 0,
                        'tipe'   => 'CPO'
                    ];
                }else{
                    $queryGetGeneral = "SELECT * FROM pabrik_5general WHERE code = 'FST'";
                    $dataGeneral = fetchData($queryGetGeneral);
                    $formulaSet = "F1";

                    if (!empty($dataGeneral[0]['nilai'])) {
                        $formulaSet = $dataGeneral[0]['nilai'];
                    }
                    
                    if ($formulaSet == "F1") {
                        
                        $tinggiExplode = explode(".",$tinggi);
                        if(isset($tinggiExplode[1])){
                            $queryGetCincitHT = "SELECT * FROM pabrik_5cincinht 
                                                WHERE 
                                                awal <= '".$tinggiExplode[0]."' and akhir >= '".$tinggiExplode[0]."'
                                                and 
                                                kodeorg = '".$lokasitugas."'
                                                and
                                                kodetangki = '".$kodeTangki."'
                                                LIMIT 1";
                            $dataCincinHT = fetchData($queryGetCincitHT);
                            $cincinid = $dataCincinHT[0]['cincinid'];

                            $queryGetCincitDT = "SELECT nilai FROM pabrik_5cincindt 
                                                WHERE 
                                                detailid = '".$tinggiExplode[1]."'
                                                and
                                                cincinid = '".$cincinid."'
                                                LIMIT 1";
                            $dataCincinDT = fetchData($queryGetCincitDT);

                            $getVolumeTangkiByTinggi = "SELECT * FROM pabrik_5vtangki where tinggicm ='".$tinggiExplode[0]."' AND kodetangki ='".$kodeTangki."' AND kodeorg ='".$lokasitugas."'";
                            $dataVolumeTangkiByTinggi = fetchData($getVolumeTangkiByTinggi);

                            $tonase = (($dataVolumeTangkiByTinggi[0]['volume'] + $dataCincinDT[0]['nilai']) * $dataDensityBySuhu[0]['kepadatan'] * $dataDensityBySuhu[0]['ketetapan']) ;
                        }else{
                            $tonase = $dataVolumeTangkiByTinggi[0]['volume'] * $dataDensityBySuhu[0]['kepadatan'] * $dataDensityBySuhu[0]['ketetapan'];
                        }

                    }elseif ($formulaSet == "F2") {
                        $tinggi=$param['tinggi'];
                        $sql = "SELECT mejaukur, iskonversi FROM pabrik_5tangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodetangki='".$param['kodetangki']."'";
                        $query=mysql_query($sql);
                        $res=mysql_fetch_assoc($query);

                        $mejaukur=$res['mejaukur'];
                        $konversi=$res['iskonversi'];
                        $tinggi1=floor($tinggi+$mejaukur);
                        $tinggi2=$tinggi+$mejaukur;
                        $tinggi3=explode(".", $tinggi2);
                        $tinggi4=$tinggi3[1];

                        $sql1 = "SELECT volume FROM pabrik_5vtangki where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tinggicm='".$tinggi1."' and kodetangki='".$param['kodetangki']."'";
                        $query1=mysql_query($sql1);
                        $res1=mysql_fetch_assoc($query1);
                        
                        $volummeter=$res1['volume'];
                        if($konversi=='1'){
                            $sql2 = "SELECT a.nilai FROM pabrik_5cincindt a inner join pabrik_5cincinht b 
                                    on a.cincinid=b.cincinid 
                                    where 
                                    kodeorg='".$_SESSION['empl']['lokasitugas']."' 
                                    and 
                                    kodetangki='".$param['kodetangki']."' 
                                    AND 
                                    (awal<='".$tinggi1."' AND akhir>='".$tinggi1."') 
                                    AND detailid='".$tinggi4."' ";
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
                        } else {
                            $tonase=$volummeter;
                        }
                    }

                    $hasil = [
                        'tonase' => round($tonase),
                        'tipe'   => 'CPO'
                    ];
                }
            }
            echo json_encode($hasil);

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
        $els[] = [makeElement('tinggi', 'label', $_SESSION['lang']['tinggi'])." (Est Kg)", makeElement('tinggi', 'textnum', $data['tinggi'], ['style' => 'width:100px', 'onblur' => 'getVolCpo()']).'cm'];
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