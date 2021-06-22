<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$param = $_POST;
$proses = $_GET['proses'];
switch ($proses) {
    case 'showDetail':
        $where = "`tipe`='PABRIK'";
        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
        $whereKary = '';
        $i = 0;
        foreach ($optOrg as $key => $row) {
            if (0 === $i) {
                $whereKary .= "lokasitugas='".$key."'";
            } else {
                $whereKary .= " or lokasitugas='".$key."'";
            }

            ++$i;
        }
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKary, '0');
        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift'];
        $cols = 'nik';
        $query = selectQuery($dbname, 'pabrik_5shiftanggota', $cols, $where);
        $data = fetchData($query);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
        }
        $theForm2 = new uForm('kasbankForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['anggotashif']);
        $theForm2->addEls('nik', $_SESSION['lang']['nik'], '', 'select', 'L', 20, $optKary);
        $theTable2 = new uTable('kasbankTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['anggotashif'], $cols, $data, $dataShow);
        $formTab2 = new uFormTable('ftPrestasi', $theForm2, $theTable2, null, ['kodeorg##shift']);
        $formTab2->_target = 'pabrik_slave_5shift';
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab2->render();
        echo '</fieldset>';

        break;
    case 'add':
        $cols = ['nik', 'kodeorg', 'shift'];
        $data = $param;
        unset($data['numRow']);
        $query = insertQuery($dbname, 'pabrik_5shiftanggota', $data, $cols);
        if (!mysql_query($query)) {
			$qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5shiftanggota where kodeorg="'.$param['kodeorg'].'" AND shift='.$param['shift'].' AND nik='.$param['nik'].'');
			$cek   = mysql_fetch_assoc($qcek);
				if($cek['total'] != 0) {
					echo 'DB error : data sudah ada';
					exit();
				}			
        }

        unset($data['kodeorg'], $data['shift']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift']." and nik='".$param['cond_nik']."'";
        $query = updateQuery($dbname, 'pabrik_5shiftanggota', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift']." and nik='".$param['nik']."'";
        $query = 'delete from `'.$dbname.'`.`pabrik_5shiftanggota` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }
    break;
    case 'deleteshift':
        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift']."";
        $query = 'delete from `'.$dbname.'`.`pabrik_5shift` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }
    break;
	case 'addshift':	
		$qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5shift where kodeorg="'.$param['kodeorg'].'" AND shift='.$param['shift'].'');
		$cek   = mysql_fetch_assoc($qcek);
        $query = 'insert into '.$dbname.".pabrik_5shift (kodeorg, shift, mandor, asisten) values('".$param['kodeorg']."','".$param['shift']."','".$param['mandor']."','".$param['asisten']."')";

		if($cek['total'] != 0) {
			echo "data sudah ada";
		}	
		else if(mysql_query($query)) {
			echo "tambah data berhasil";
		}	
		else {
            echo 'DB Error : '.mysql_error();
            exit();			
		}
	break;
    default:
        break;
}

?>