<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'add':
        if (empty($_SESSION['empl']['karyawanid'])) {
            echo 'Error : Lakukan login ulang!';
            exit();
        }

        $cols = [   'shift', 'station', 'engine', 
                    'start_time_stagnasi', 'stop_time_stagnasi', 'total_stagnasi', 
                    'down_status', 'description', 'created_by', 'created_at', 'pabrik_pengolahan_id'];
        $data = $param;
        $data['created_by'] = $_SESSION['empl']['karyawanid'];
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['pabrik_pengolahan_id'] = $data['nopengolahan'];
        unset($data['numRow'], $data['nopengolahan']);

        $query = insertQuery($dbname, 'pabrik_pengolahan_mesin', $data, $cols);

        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        // sum
        $querySumStagnasi = "SELECT sum(total_stagnasi) as total FROM pabrik_pengolahan_mesin where pabrik_pengolahan_id = '".$data['pabrik_pengolahan_id']."'";
        
        $hasilSum = fetchData($querySumStagnasi);
        if (!empty($hasilSum[0]['total'])) {
            $queryUpdateStagnasi = "UPDATE pabrik_pengolahan SET jam_stagnasi = '".$hasilSum[0]['total']."' WHERE nopengolahan = '".$data['pabrik_pengolahan_id']."'";
            mysql_query($queryUpdateStagnasi);
        }

        $data = $param;
        unset($data['nopengolahan'], $data['numRow']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        print_r($_POST);
        die();
        $data = $param;
        $data['pabrik_pengolahan_id'] = $data['nopengolahan'];
        unset($data['station'], $data['nopengolahan']);

        foreach ($data as $key => $cont) {
            if ('cond_' === substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $where = "pabrik_pengolahan_id='".$param['nopengolahan']."' and kodeorg='".$param['cond_station']."' and tahuntanam='".$param['cond_tahuntanam']."'";
        $query = updateQuery($dbname, 'pabrik_pengolahan_mesin', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".$param['station']."' and tahuntanam='".$param['tahuntanam']."'";
        $query = 'delete from `'.$dbname.'`.`pabrik_pengolahanmesin` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}

?>