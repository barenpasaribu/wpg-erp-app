<?php
    require_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/rTable.php';
    $proses = $_GET['proses'];
    $param = $_POST;
    switch ($proses) {
        case 'posting':
            $data = $_POST;
            $where = "nopengolahan='".$data['nopengolahan']."'";
            $query = updateQuery($dbname, 'pabrik_pengolahan', ['posting' => '1'], $where);
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error();
            }

            break;
        case 'unposting':
            $data = $_POST;
            $where = "nopengolahan='".$data['nopengolahan']."'";
            $query = updateQuery($dbname, 'pabrik_pengolahan', ['posting' => '0'], $where);
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error();
            }

            break;
        case 'showHeadList':
            if (empty($_SESSION['empl']['lokasitugas'])) {
                echo 'Error : Lakukan login ulang!';
                exit();
            }
            $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
            if (isset($param['where'])) {
                $tmpW = str_replace('\\', '', $param['where']);
                $arrWhere = json_decode($tmpW, true);
                if (!empty($arrWhere)) {
                    foreach ($arrWhere as $key => $r1) {
                        if (0 == $key) {
                            $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                        }
                    }
                } else {
                    $where .= null;
                }
            } else {
                $where .= null;
            }

            $header = [ $_SESSION['lang']['tanggal'],
                        $_SESSION['lang']['nopengolahan'], 
                        $_SESSION['lang']['pabrik'], 
                        
                    
                        ];
            $cols = 'tanggal, nopengolahan,kodeorg,posting';
            $query = selectQuery($dbname, 'pabrik_pengolahan', $cols, $where. ' order by nopengolahan DESC, tanggal ASC', '', false, $param['shows'], $param['page']);		
            $data = fetchData($query);
            $totalRow = getTotalRow($dbname, 'pabrik_pengolahan', $where);
            foreach ($data as $key => $row) {
                $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
                if (1 == $row['posting']) {
                    $data[$key]['switched'] = true;
                }

                unset($data[$key]['posting']);
            }
            $x = 'select kodejabatan from '.$dbname.".sdm_5jabatan where alias like '%ka.%' or alias like '%kepala%' or alias like '%Mill'";
            $y = mysql_query($x);
            while ($z = mysql_fetch_assoc($y)) {
                $pos = $z['kodejabatan'];
                if ($pos == $_SESSION['empl']['kodejabatan']) {
                    $flag = 1;
                }
            }
            $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
            $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
            $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
            $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme']."/posting.png");
            $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
            if (1 != $flag) {
                $tHeader->_actions[2]->_name = 'postingData';
            }

            $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
            $tHeader->_actions[3]->addAttr('event');
            $tHeader->_switchException = ['detailPDF'];
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
            $query = selectQuery($dbname, 'pabrik_pengolahan', '*', "nopengolahan='".$param['nopengolahan']."'");
            $tmpData = fetchData($query);
            $data = $tmpData[0];
            $data['tanggal'] = tanggalnormal($data['tanggal']);
            echo formHeader('edit', $data);
            echo "<div id='detailField' style='clear:both'></div>";
            break;
        case 'add':
            $data = $_POST;
            $warning = '';
            if ('' == $data['tanggal']) {
                $warning .= "Tanggal harus diisi\n";
            }

            if ('' != $warning) {
                echo "Warning :\n".$warning;
                exit();
            }
            $data['tanggal'] = tanggalsystemw($data['tanggal']);

            $queryCekPengolahan = "SELECT * FROM pabrik_pengolahan WHERE tanggal='".$data['tanggal']."' and kodeorg = '".$data['kodeorg']."' ";
            $dataPengolahan = fetchData($queryCekPengolahan);

            if (!empty($dataPengolahan[0])) {
                echo "Error : Data pada tanggal ".tanggalnormal($data['tanggal'])." sudah ada";
                exit();
            }

            
            unset($data['nopengolahan']);
            $cols = [
                    'kodeorg', 'tanggal', 'status_olah', 
                    'mandor_shift_1', 'asisten_shift_1', 'jam_start_shift_1', 'jam_stop_shift_1', 'total_jam_shift_1', 'jam_start_operasi_shift_1', 'jam_stop_operasi_shift_1', 'total_jam_operasi_shift_1', 'total_jam_press_shift_1', 'jam_idle_shift_1',
                    'mandor_shift_2', 'asisten_shift_2', 'jam_start_shift_2', 'jam_stop_shift_2', 'total_jam_shift_2', 'jam_start_operasi_shift_2', 'jam_stop_operasi_shift_2', 'total_jam_operasi_shift_2', 'total_jam_press_shift_2', 'jam_idle_shift_2',
                    'mandor_shift_3', 'asisten_shift_3', 'jam_start_shift_3', 'jam_stop_shift_3', 'total_jam_shift_3', 'jam_start_operasi_shift_3', 'jam_stop_operasi_shift_3', 'total_jam_operasi_shift_3', 'total_jam_press_shift_3', 'jam_idle_shift_3',
                    'total_jam_shift', 'total_jam_press', 'total_jam_operasi', 'total_jam_idle', 
                    'jam_stagnasi', 
                    'lori_olah_shift_1', 'lori_olah_shift_2', 'lori_olah_shift_3', 
                    'lori_dalam_rebusan','restan_depan_rebusan' , 'restan_dibelakang_rebusan',
                    'estimasi_di_peron' ,    'total_lori', 'rata_rata_lori' ,
                    'tbs_sisa_kemarin', 'tbs_masuk_bruto', 
                    'total_tbs', 'tbs_potongan', 'tbs_masuk_netto', 'tbs_diolah', 'tbs_diolah_after', 'tbs_sisa', 
                    'despatch_cpo', 'return_cpo', 'despatch_pk', 'return_pk',
                    'janjang_kosong', 'limbah_cair', 'solid_decnter', 'abu_janjang', 'cangkang', 'fibre'];
            $query = insertQuery($dbname, 'pabrik_pengolahan', $data, $cols);

            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error();
            } else {
                echo mysql_insert_id();
            }

            break;
        case 'edit':
            $data = $_POST;
            $where = "nopengolahan='".$data['nopengolahan']."'";
            unset($data['nopengolahan']);
            $data['tanggal'] = tanggalsystemw($data['tanggal']);
            $query = updateQuery($dbname, 'pabrik_pengolahan', $data, $where);
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error();
            }

            break;
        case 'delete':
            $where = 'nopengolahan='.$param['nopengolahan'];
            $query = 'delete from `'.$dbname.'`.`pabrik_pengolahan` where '.$where;
            if (!mysql_query($query)) {
                echo 'DB Error : '.mysql_error();
                exit();
            }

            break;
        case 'updMandorAst':
            $mode = $param['mode'];
            $shift = $param['shift'];
            if ('tanggal' == $mode) {
                $optShift = makeOption($dbname, 'pabrik_5shift', 'shift,shift', "kodeorg='".$_SESSION['empl']['lokasitugas']."'");
                if (empty($optShift)) {
                    echo 'Warning : Tidak ada shift yang berlaku pada tanggal tersebut';
                    exit();
                }

                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift in (";
                $i = 0;
                foreach ($optShift as $row) {
                    if (0 == $i) {
                        $where .= $row;
                    } else {
                        $where .= ','.$row;
                    }

                    ++$i;
                }
                $where .= ')';
                $cols = 'shift,mandor,asisten';
            } else {
                $cols = 'mandor,asisten';
                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift=".$param['shift'];
            }

            $query = selectQuery($dbname, 'pabrik_5shift', $cols, $where);
            $res = fetchData($query);
            $whereKary = 'karyawanid in ('.$res[0]['mandor'].','.$res[0]['asisten'].')';
            $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKary);
            $resShift = [];
            $resMandor = [$res[0]['mandor'] => $optKary[$res[0]['mandor']]];
            $resAst = [$res[0]['asisten'] => $optKary[$res[0]['asisten']]];
            if ('tanggal' == $mode) {
                foreach ($res as $row) {
                    $resShift[$row['shift']] = $row['shift'];
                }
            } else {
                $resShift = 'empty';
            }

            $result = ['shift' => $resShift, 'mandor' => $resMandor, 'asisten' => $resAst];
            echo json_encode($result);

            break;
        default:
            break;
    }
    function formHeader($mode, $data)
    {
        
        global $dbname;
        if (empty($data)) {
            $new = true;
            $data['kodeorg'] = '';
            $data['nopengolahan'] = '0';
            $data['tanggal'] = '';
            $data['status_olah'] = '1';

            $data['mandor_shift_1'] = '';
            $data['asisten_shift_1'] = '';
            $data['jam_start_shift_1'] = '00:00:00';
            $data['jam_stop_shift_1'] = '00:00:00';
            $data['total_jam_shift_1'] = '0';
            $data['jam_start_operasi_shift_1'] = '00:00:00';
            $data['jam_stop_operasi_shift_1'] = '00:00:00';
            $data['total_jam_operasi_shift_1'] = '0';
            $data['total_jam_press_shift_1'] = '0';
            $data['jam_idle_shift_1'] = '0';

            $data['mandor_shift_2'] = '';
            $data['asisten_shift_2'] = '';
            $data['jam_start_shift_2'] = '00:00:00';
            $data['jam_stop_shift_2'] = '00:00:00';
            $data['total_jam_shift_2'] = '0';
            $data['jam_start_operasi_shift_2'] = '00:00:00';
            $data['jam_stop_operasi_shift_2'] = '00:00:00';
            $data['total_jam_operasi_shift_2'] = '0';
            $data['total_jam_press_shift_2'] = '0';
            $data['jam_idle_shift_2'] = '0';

            $data['mandor_shift_3'] = '';
            $data['asisten_shift_3'] = '';
            $data['jam_start_shift_3'] = '00:00:00';
            $data['jam_stop_shift_3'] = '00:00:00';
            $data['total_jam_shift_3'] = '0';
            $data['jam_start_operasi_shift_3'] = '00:00:00';
            $data['jam_stop_operasi_shift_3'] = '00:00:00';
            $data['total_jam_operasi_shift_3'] = '0';
            $data['total_jam_press_shift_3'] = '0';
            $data['jam_idle_shift_3'] = '0';

            $data['total_jam_shift'] = '0';
            $data['total_jam_press'] = '0';
            $data['total_jam_operasi'] = '0';
            $data['total_jam_idle'] = '0';

            $data['jam_stagnasi'] = '0';

            $data['lori_olah_shift_1'] = '0';
            $data['lori_olah_shift_2'] = '0';
            $data['lori_olah_shift_3'] = '0';
            $data['lori_dalam_rebusan'] = '0';
            $data['restan_depan_rebusan'] = '0';
            $data['restan_dibelakang_rebusan'] = '0';
            $data['estimasi_di_peron'] = '0';
            $data['total_lori'] = '0';
            $data['rata_rata_lori'] = '0';

            $data['tbs_sisa_kemarin'] = '0';
            $data['tbs_masuk_bruto'] = '0';
            $data['total_tbs'] = '0';
            $data['tbs_potongan'] = '0';
            $data['tbs_potongan_olah'] = '0';
            $data['tbs_masuk_netto'] = '0';

            $data['tbs_diolah'] = '0';
            $data['tbs_diolah_after'] = '0';
            $data['tbs_sisa'] = '0';
            
            $data['despatch_cpo'] = '0';
            $data['return_cpo'] = '0';
            $data['despatch_pk'] = '0';
            $data['return_pk'] = '0';
            $data['janjang_kosong'] = '0';
            $data['limbah_cair'] = '0';
            $data['solid_decnter'] = '0';
            $data['abu_janjang'] = '0';
            $data['cangkang'] = '0';
            $data['fibre'] = '0';
        } else {
            $new = false;
        }
        
        if ('edit' == $mode) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }

        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
        $optStatusOlah = array(
                            '0' => "Mengolah",
                            '1' => "Tidak mengolah"
        );
        $qShift = selectQuery($dbname, 'pabrik_5shift', 'shift,mandor,asisten', "kodeorg='".$_SESSION['empl']['lokasitugas']."'");
        $tmpShift = fetchData($qShift);
        $optShift = [];
        $whereKary = '';
        $whereKaryNew = '';
        foreach ($tmpShift as $key => $row) {
            $optShift[$row['shift']] = $row['shift'];
            if (0 == $key) {
                $whereKaryNew .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
                $whereKary .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
            } else {
                $whereKaryNew .= " or karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
            }
        }
        $optKaryMandor=[];
        $optKaryMandor2=[];
        $optKaryMandor3=[];
        $sql = "select a.shift, karyawanid, namakaryawan from pabrik_5shift a inner join datakaryawan b on a.mandor=b.karyawanid where
                lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
        $data2=mysql_query($sql);
        while ($row=mysql_fetch_array($data2)) {
            if ($row['shift'] == 1) {
                $optKaryMandor[$row['karyawanid']] = $row['namakaryawan'];
            }
            if ($row['shift'] == 2) {
                $optKaryMandor2[$row['karyawanid']] = $row['namakaryawan'];
            }
            $optKaryMandor3[$row['karyawanid']] = $row['namakaryawan'];
        }
        $optKaryAsst=[];
        $optKaryAsst2=[];
        $optKaryAsst3=[];
        $sql = "select a.shift, karyawanid, namakaryawan from pabrik_5shift a inner join datakaryawan b on a.asisten=b.karyawanid where
                lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
        $data3=mysql_query($sql);
        while ($row=mysql_fetch_array($data3)) {
            if ($row['shift'] == 1) {
                $optKaryAsst[$row['karyawanid']] = $row['namakaryawan'];
            }
            if ($row['shift'] == 2) {
                $optKaryAsst2[$row['karyawanid']] = $row['namakaryawan'];
            }
            $optKaryAsst3[$row['karyawanid']] = $row['namakaryawan'];
            
        }

        $els = [];
        $els[] = [  makeElement('kodeorg', 'label', $_SESSION['lang']['pabrik']), 
                    makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:300px'], $optOrg)];

        $els[] = [  makeElement('nopengolahan', 'label', $_SESSION['lang']['nopengolahan']), 
                    makeElement('nopengolahan', 'text', $data['nopengolahan'], ['style' => 'width:200px', 'maxlength' => '15', 'disabled' => 'disabled'])];
        $els[] = [  makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), 
                    makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', 'onchange' => 'getDataTimbangan()'])];
        $els[] = awanElement(
                        "",
                        "<button onclick='lihatDataTimbangan()'>Lihat Data</button>"
                    );
        if ($data['status_olah'] == 0) {
            $pilihStatus = '<option value="1">Mengolah</option>
                            <option value="0" selected>Tidak mengolah</option>';
        }else{
            $pilihStatus = '<option value="1" selected>Mengolah</option>
                            <option value="0">Tidak mengolah</option>';
        }
        
        $els[] = awanElement(
                        '<label for="status_olah">Status Olah</label>',
                        '<select id="status_olah" name="status_olah" style="width:300px">
                            '.$pilihStatus.'
                        </select>'
                    );

        $els[] = [  makeElement('mandor_shift_1', 'label', $_SESSION['lang']['mandor']. " Shift 1"), 
                    makeElement('mandor_shift_1', 'select', $data['mandor'], ['style' => 'width:300px'], $optKaryMandor)];
        $els[] = [  makeElement('asisten_shift_1', 'label', $_SESSION['lang']['asisten']. " Shift 1"), 
                    makeElement('asisten_shift_1', 'select', $data['asisten'], ['style' => 'width:300px'], $optKaryAsst)];
        

        
        
        $els[] = [  makeElement('jam_start_shift_1', 'label', "Jam Start Shift 1"), 
                    makeElementAwan('jam_start_shift_1', 'jammenit', $data['jam_start_shift_1'], 'hitungJamShift1()')];
        $els[] = [  makeElement('jam_stop_shift_1', 'label', "Jam Stop Shift 1"), 
                    makeElementAwan('jam_stop_shift_1', 'jammenit', $data['jam_stop_shift_1'], 'hitungJamShift1()')];
        $els[] = [  makeElement('total_jam_shift_1', 'label', "Total Jam Shift 1"), 
                    makeElement('total_jam_shift_1', 'textnum', $data['total_jam_shift_1'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamShift()'])];  
        
        $els[] = [  makeElement('jam_start_operasi_shift_1', 'label', "Jam Start Operasi Shift 1"), 
                    makeElementAwan('jam_start_operasi_shift_1', 'jammenit', $data['jam_start_operasi_shift_1'], 'hitungJamOperasi1()')];
        $els[] = [  makeElement('jam_stop_operasi_shift_1', 'label', "Jam Stop Operasi Shift 1"), 
                    makeElementAwan('jam_stop_operasi_shift_1', 'jammenit', $data['jam_stop_operasi_shift_1'], 'hitungJamOperasi1()')];
        $els[] = [  makeElement('total_jam_operasi_shift_1', 'label', "Total Jam Operasi Shift 1"), 
                    makeElement('total_jam_operasi_shift_1', 'textnum', $data['total_jam_operasi_shift_1'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamOperasi()'])];
        $els[] = [  makeElement('total_jam_press_shift_1', 'label', "Total Jam Press Shift 1"), 
                    makeElement('total_jam_press_shift_1', 'textnum', $data['total_jam_press_shift_1'], ['style' => 'width:300px', 'onchange' => 'hitungTotalJamPress()'])];

        $els[] = awanElement(
                "<label for='jam_idle'>Jam Idle Shift 1</label>",
                "<input id='jam_idle_shift_1' disabled value='".$data['jam_idle_shift_1']."' name='jam_idle_shift_1' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:300px;background: #dddddd;'/>"
        );
        
        
        $els[] = [  makeElement('mandor_shift_2', 'label', $_SESSION['lang']['mandor']. " Shift 2"), 
                    makeElement('mandor_shift_2', 'select', $data['mandor_shift_2'], ['style' => 'width:300px'], $optKaryMandor2)];
        $els[] = [  makeElement('asisten_shift_2', 'label', $_SESSION['lang']['asisten']. " Shift 2"), 
                    makeElement('asisten_shift_2', 'select', $data['asisten_shift_2'], ['style' => 'width:300px'], $optKaryAsst2)];
        $els[] = [  makeElement('jam_start_shift_2', 'label', "Jam Start Shift 2"), 
                    makeElementAwan('jam_start_shift_2', 'jammenit', $data['jam_start_shift_2'], 'hitungJamShift2()')];
        $els[] = [  makeElement('jam_stop_shift_2', 'label', "Jam Stop Shift 2"), 
                    makeElementAwan('jam_stop_shift_2', 'jammenit', $data['jam_stop_shift_2'], 'hitungJamShift2()')];
        $els[] = [  makeElement('total_jam_shift_2', 'label', "Total Jam Shift 2"), 
                    makeElement('total_jam_shift_2', 'textnum', $data['total_jam_shift_2'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamShift()'])];  
        $els[] = [  makeElement('jam_start_operasi_shift_2', 'label', "Jam Start Operasi Shift 2"), 
                    makeElementAwan('jam_start_operasi_shift_2', 'jammenit', $data['jam_start_operasi_shift_2'], 'hitungJamOperasi2()')];
        $els[] = [  makeElement('jam_stop_operasi_shift_2', 'label', "Jam Stop Operasi Shift 2"), 
                    makeElementAwan('jam_stop_operasi_shift_2', 'jammenit', $data['jam_stop_operasi_shift_2'], 'hitungJamOperasi2()')];
        $els[] = [  makeElement('total_jam_operasi_shift_2', 'label', "Total Jam Operasi Shift 2"), 
                    makeElement('total_jam_operasi_shift_2', 'textnum', $data['total_jam_operasi_shift_2'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamOperasi()'])];
        $els[] = [  makeElement('total_jam_press_shift_2', 'label', "Total Jam Press Shift 2"), 
                    makeElement('total_jam_press_shift_2', 'textnum', $data['total_jam_press_shift_2'], ['style' => 'width:300px', 'onchange' => 'hitungTotalJamPress()'])];
        $els[] = awanElement(
                    "<label for='jam_idle_shift_2'>Jam Idle Shift 2</label>",
                    "<input id='jam_idle_shift_2' disabled name='jam_idle_shift_2' value='".$data['jam_idle_shift_2']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' 
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        

        $els[] = [  makeElement('mandor_shift_3', 'label', $_SESSION['lang']['mandor']. " Shift 3"), 
                    makeElement('mandor_shift_3', 'select', $data['mandor_shift_3'], ['style' => 'width:300px'], $optKaryMandor3)];
        $els[] = [  makeElement('asisten_shift_3', 'label', $_SESSION['lang']['asisten']. " Shift 3"), 
                    makeElement('asisten_shift_3', 'select', $data['asisten_shift_3'], ['style' => 'width:300px'], $optKaryAsst3)];
        $els[] = [  makeElement('jam_start_shift_3', 'label', "Jam Start Shift 3"), 
                    makeElementAwan('jam_start_shift_3', 'jammenit', $data['jam_start_shift_3'], 'hitungJamShift3()')];
        $els[] = [  makeElement('jam_stop_shift_3', 'label', "Jam Stop Shift 3"), 
                    makeElementAwan('jam_stop_shift_3', 'jammenit', $data['jam_stop_shift_3'], 'hitungJamShift3()')];
        $els[] = [  makeElement('total_jam_shift_3', 'label', "Total Jam Shift 3"), 
                    makeElement('total_jam_shift_3', 'textnum', $data['total_jam_shift_3'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamShift()'])];  
        $els[] = [  makeElement('jam_start_operasi_shift_3', 'label', "Jam Start Operasi Shift 3"), 
                    makeElementAwan('jam_start_operasi_shift_3', 'jammenit', $data['jam_start_operasi_shift_3'], 'hitungJamOperasi3()')];
        $els[] = [  makeElement('jam_stop_operasi_shift_3', 'label', "Jam Stop Operasi Shift 3"), 
                    makeElementAwan('jam_stop_operasi_shift_3', 'jammenit', $data['jam_stop_operasi_shift_3'], 'hitungJamOperasi3()')];
        $els[] = [  makeElement('total_jam_operasi_shift_3', 'label', "Total Jam Operasi Shift 3"), 
                    makeElement('total_jam_operasi_shift_3', 'textnum', $data['total_jam_operasi_shift_3'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTotalJamOperasi()'])];
        $els[] = [  makeElement('total_jam_press_shift_3', 'label', "Total Jam Press Shift 3"), 
                    makeElement('total_jam_press_shift_3', 'textnum', $data['total_jam_press_shift_3'], ['style' => 'width:300px', 'onchange' => 'hitungTotalJamPress()'])];
        $els[] = awanElement(
                    "<label for='jam_idle_shift_3'>Jam Idle Shift 3</label>",
                    "<input id='jam_idle_shift_3' disabled name='jam_idle_shift_3' value='".$data['jam_idle_shift_3']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' onchange='hitungTotalJamIdle()'
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        
        
        $els[] = awanElement(
                    "<label for='total_jam_shift'>Total Jam Shift</label>",
                    "<input id='total_jam_shift' name='total_jam_shift' value='".$data['total_jam_shift']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' 
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        
        $els[] = awanElement(
                    "<label for='total_jam_press'>Total Jam Press</label>",
                    "<input id='total_jam_press' name='total_jam_press' value='".$data['total_jam_press']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' 
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        
        $els[] = awanElement(
                    "<label for='total_jam_operasi'>Total Jam Operasi</label>",
                    "<input id='total_jam_operasi' name='total_jam_operasi' value='".$data['total_jam_operasi']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' 
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        
        $els[] = awanElement(
                    "<label for='total_jam_idle'>Total Jam Idle</label>",
                    "<input id='total_jam_idle' name='total_jam_idle' value='".$data['total_jam_idle']."'
                    class='myinputtextnumber' onkeypress='return angka_doang(event)' 
                    type='text' value='0' style='width:300px;background: #dddddd;'/>"
                );
        





        $els[] = [  makeElement('jam_stagnasi', 'label', $_SESSION['lang']['jamstagnasi']), 
                    makeElement('jam_stagnasi', 'textnum', $data['jam_stagnasi'], ['style' => 'width:300px'])];


        // Lori
        $els[] = [  "<h3>Lori</h3>", NULL];
        $els[] = [  makeElement('lori_olah_shift_1', 'label', "Lori Olah Shift 1"), 
                    makeElement('lori_olah_shift_1', 'textnum', $data['lori_olah_shift_1'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        $els[] = [  makeElement('lori_olah_shift_2', 'label', "Lori Olah Shift 2"), 
                    makeElement('lori_olah_shift_2', 'textnum', $data['lori_olah_shift_2'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        $els[] = [  makeElement('lori_olah_shift_3', 'label', "Lori Olah Shift 3"), 
                    makeElement('lori_olah_shift_3', 'textnum', $data['lori_olah_shift_3'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
                    
        $els[] = [  makeElement('lori_dalam_rebusan', 'label', "Lori Dalam Rebusan"), 
                    makeElement('lori_dalam_rebusan', 'textnum', $data['lori_dalam_rebusan'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        
        $els[] = [  makeElement('restan_depan_rebusan', 'label', "Restan Depan Rebusan"), 
                    makeElement('restan_depan_rebusan', 'textnum', $data['restan_depan_rebusan'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        
        $els[] = [  makeElement('restan_dibelakang_rebusan', 'label', "Restan dibelakang Rebusan"), 
                    makeElement('restan_dibelakang_rebusan', 'textnum', $data['restan_dibelakang_rebusan'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        
        $els[] = [  makeElement('estimasi_di_peron', 'label', "Estimasi di Peron"), 
                    makeElement('estimasi_di_peron', 'textnum', $data['estimasi_di_peron'], ['style' => 'width:300px', 'onchange' => 'hitungTotalLori()'])];
        
        $els[] = [  makeElement('total_lori', 'label', "Total Lori"), 
                    makeElement('total_lori', 'textnum', $data['total_lori'], ['style' => 'width:300px;background: #dddddd;'])];

        $els[] = [  makeElement('rata_rata_lori', 'label', "Rata-rata Lori"), 
                    makeElement('rata_rata_lori', 'textnum', $data['rata_rata_lori'], ['style' => 'width:300px;background: #dddddd;'])];
               
                    
        // TBS
        $els[] = [  "<h3>TBS</h3>", NULL];
        $els[] = [  makeElement('tbs_sisa_kemarin', 'label', "TBS Sisa Kemarin"), 
                    makeElement('tbs_sisa_kemarin', 'textnum', $data['tbs_sisa_kemarin'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTBS()']).' kg'];
        
        $els[] = [  makeElement('tbs_masuk_bruto', 'label', "TBS Masuk (Bruto)"), 
                    makeElement('tbs_masuk_bruto', 'textnum', $data['tbs_masuk_bruto'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTBS()']).' kg']; 

        
        $els[] = [  makeElement('total_tbs', 'label', "TOTAL TBS"), 
                    makeElement('total_tbs', 'textnum', $data['total_tbs'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTBS()']).' kg'];
        
        $els[] = [  makeElement('tbs_potongan', 'label', "TBS (Potongan)"), 
                    makeElement('tbs_potongan', 'textnum', $data['tbs_potongan'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungPotongan()']).' kg'];

        $els[] = [  makeElement('tbs_potongan_olah', 'label', "TBS (Potongan Olah)"), 
                    makeElement('tbs_potongan_olah', 'textnum', $data['tbs_potongan_olah'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungPotongan()']).' kg'];
                    
        $els[] = [  makeElement('tbs_masuk_netto', 'label', "TBS Masuk (Netto)"), 
                    makeElement('tbs_masuk_netto', 'textnum', $data['tbs_masuk_netto'], ['style' => 'width:300px;background: #dddddd;', 'onchange' => 'hitungTBS()']).' kg']; 
           
        $els[] = [  makeElement('tbs_diolah', 'label', "TBS Diolah"), 
                    makeElement('tbs_diolah', 'textnum', $data['tbs_diolah'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        $els[] = [  makeElement('tbs_diolah_after', 'label', "TBS Diolah (After Grading)"), 
                    makeElement('tbs_diolah_after', 'textnum', $data['tbs_diolah_after'], ['style' => 'width:300px;background: #dddddd;']).' kg'];

        $els[] = [  makeElement('tbs_sisa', 'label', "TBS Sisa"), 
                    makeElement('tbs_sisa', 'textnum', $data['tbs_sisa'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        

        // pengiriman
        $els[] = [  "<h3>Pengiriman</h3>", NULL];
        $els[] = [  makeElement('despatch_cpo', 'label', "Despatch (CPO)"), 
                    makeElement('despatch_cpo', 'textnum', $data['despatch_cpo'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('return_cpo', 'label', "Return CPO"), 
                    makeElement('return_cpo', 'textnum', $data['return_cpo'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('despatch_pk', 'label', "Despatch (PK)"), 
                    makeElement('despatch_pk', 'textnum', $data['despatch_pk'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('return_pk', 'label', "Return PK"), 
                    makeElement('return_pk', 'textnum', $data['return_pk'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('janjang_kosong', 'label', "Janjang Kosong (EFB)"), 
                    makeElement('janjang_kosong', 'textnum', $data['janjang_kosong'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('limbah_cair', 'label', "Limbah Cair (POME)"), 
                    makeElement('limbah_cair', 'textnum', $data['limbah_cair'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('solid_decnter', 'label', "Solid Decnter"), 
                    makeElement('solid_decnter', 'textnum', $data['solid_decnter'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('abu_janjang', 'label', "Abu Janjang (Bunch Ash)"), 
                    makeElement('abu_janjang', 'textnum', $data['abu_janjang'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('cangkang', 'label', "Cangkang ( Shell)"), 
                    makeElement('cangkang', 'textnum', $data['cangkang'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        
        $els[] = [  makeElement('fiber', 'label', "Fibre"), 
                    makeElement('fibre', 'textnum', $data['fibre'], ['style' => 'width:300px;background: #dddddd;']).' kg'];
        

        if ('add' == $mode) {
            $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];
        } else {
            if ('edit' == $mode) {
                $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];
            }
        }

        if ('add' == $mode) {
            return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
        }

        if ('edit' == $mode) {
            return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
        }
    }

    function awanElement($label, $element){
        return array(0 => $label, 1 =>$element);
    }

    function makeElementAwan($id, $type, $value = '', $namaFungsi = '')
    {
        $el = '';
        switch ($type) {
            case 'jammenit':
                $optJam = [];
                $optMenit = [];
                $tmpVal = explode(':', $value);
                $valueJam = $tmpVal[0];
                if (1 < count($tmpVal)) {
                    $valueMenit = $tmpVal[1];
                } else {
                    $valueMenit = '00';
                }

                for ($i = 0; $i < 60; $i++) {
                    if ($i < 24) {
                        $optJam[addZero($i, 2)] = addZero($i, 2);
                    }

                    $optMenit[addZero($i, 2)] = addZero($i, 2);
                }
                $el .= "<select onchange='".$namaFungsi."' id='".$id."_jam' name='".$id."'_jam";
                $el .= '>';
                foreach ($optJam as $val) {
                    if ($valueJam == $val) {
                        $el .= "<option value='".$val."' selected>".$val.'</option>';
                    } else {
                        $el .= "<option value='".$val."'>".$val.'</option>';
                    }
                }
                $el .= '</select>';
                $el .= ':';
                $el .= "<select onchange='".$namaFungsi."' id='".$id."_menit' name='".$id."'_menit";
                $el .= '>';
                foreach ($optMenit as $val) {
                    if ($valueMenit == $val) {
                        $el .= "<option value='".$val."' selected>".$val.'</option>';
                    } else {
                        $el .= "<option value='".$val."'>".$val.'</option>';
                    }
                }
                $el .= '</select>';

                break;
            
            default:
                break;
        }

        return $el;
    }
?>