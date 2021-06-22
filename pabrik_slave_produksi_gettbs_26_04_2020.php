<?php

require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/zLib.php';

$proses = $_POST['proses'];

$tanggal = tanggalsystem($_POST['tanggal']);

switch ($proses) {

    case 'gettbs':

        $i = 'select sum(beratbersih)- sum(kgpotsortasi) as tbsmasuk from '.$dbname.".pabrik_timbangan where tanggal='".$tanggal."' ";

        $n = mysql_query($i);

        $d = mysql_fetch_assoc($n);

        $tbs = $d['tbsmasuk'];

        if ($tbs == '') {

            $tbs = 0;

        }


        
        echo $tbs;



        break;

}



?>