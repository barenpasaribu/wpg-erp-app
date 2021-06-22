<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$subunit = $_POST['subunit'];
$id = $_POST['id'];
$standar = $_POST['standar'];
$satuan = $_POST['satuan'];
$parameter = $_POST['parameter'];
$method = $_POST['method'];

switch ($method) {
    case 'insert':
        if (empty($subunit) || empty($parameter) || empty($satuan) || empty($standar) ) {
            echo 'warning:Please Complete The Form';
            exit();
        }
		$qcek  = mysql_query('select count(parameter) as total from '.$dbname.'.pabrik_parameter_analisa where subunitid="'.$subunit.'" and parameter="'.$parameter.'" ');
      
		$cek   = mysql_fetch_assoc($qcek);
			if($cek['total'] != 0) {
				echo 'DB error : data sudah ada';
				exit();
			}
        $i = 'insert into '.$dbname.".pabrik_parameter_analisa (subunitid,parameter,satuan,standar,updateby) values ('".$subunit."','".$parameter."','".$satuan."','".$standar."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }
        
        break;
    case 'update':
        if (empty($subunit)) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $i = 'update '.$dbname.'.pabrik_parameter_analisa set 
		subunitid="'.$subunit.'",
        parameter="'.$parameter.'",
        satuan="'.$satuan.'",
        standar="'.$standar.'",
		updateby="'.$_SESSION['standard']['userid'].'" 
		where id="'.$id.'"';
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n            <div style='height:220px;overflow:auto'>\r\n                    <table class=sortable cellspacing=1 border=0>\r\n                 <thead>\r\n                             <tr class=rowheader>\r\n                                    
        <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n
        <td align=center>Sub Unit</td>\r\n 
        <td align=center>Parameter</td>\r\n 
        <td align=center>Satuan</td>\r\n 
        <td align=center>Standar</td>\r\n 
        <td align=center>Update By</td>\r\n 
        <td align=center>".$_SESSION['lang']['action']."</td>\r\n                             </tr>\r\n                    </thead>\r\n                    <tbody>";
        $ql2 = 'select a.*,b.subunit from '.$dbname.".pabrik_parameter_analisa a,pabrik_subunit_analisa b where a.subunitid=b.id ";
        $n = mysql_query($ql2);
        $no = 0;
        $data = [];
        $optKarRow = [];
        while ($d = mysql_fetch_assoc($n)) {
            $data[] = $d;
        }
        if (!empty($data)) {
            $whereKarRow = 'karyawanid in (';
            $notFirst = false;
            foreach ($data as $key => $row) {
                if ('' !== $row['updateby']) {
                    if (false === $notFirst) {
                        $whereKarRow .= $row['updateby'];
                        $notFirst = true;
                    } else {
                        $whereKarRow .= ','.$row['updateby'];
                    }
                }
            }
            $whereKarRow .= ')';
            $optKarRow = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKarRow, '0', true);
        }

        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$row['subunit'].'</td>';
            echo '<td align=left>'.$row['parameter'].'</td>';
            echo '<td align=left>'.$row['satuan'].'</td>';
            echo '<td align=left>'.$row['standar'].'</td>';
            echo '<td align=left>'.$optKarRow[$row['updateby']].'</td>';
            echo "<td align=center>\r\n                            <img src=images/application/application_edit.png class=resicon title='Edit' caption='Edit' onclick=\"fillFieldParameter('".$row['subunitid']."','".$row['id']."','".$row['parameter']."','".$row['satuan']."','".$row['standar']."');\">\r\n                            <img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"delParameter('".$row['id']."');\"></td>";
            echo '</tr>';
        }
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_parameter_analisa where  id='".$id."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getTangki':
        $sGet = 'select kodetangki,keterangan from '.$dbname.".pabrik_5tangki where kodeorg='".$kodeorg."'";
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optTangki .= '<option value='.$rGet['kodetangki'].'>'.$rGet['keterangan'].'</option>';
        }
        echo $optTangki;

        break;
}

?>