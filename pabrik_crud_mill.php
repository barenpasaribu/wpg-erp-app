<?php

require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';

include_once 'lib/formTable.php';

$param = $_POST;

//$codeOrg = $_POST['codeOrg'];

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

        $tanggal = tanggalsystem($param['tanggal']);

   // $query = 'UPDATE SET '.$dbname.".log_transaksi_mill (tbsolah,estimasidiperon,kodeorg,tanggal,loriolah, loridalamrebusan, lorirestandepanrebusan, lorirestanbelakangrebusan) values('".$param['tbsolah']."','".$param['estimasidiperon']."','".$param['kodeorg']."','".$tanggal."','".$param['loriolah']."','".$param['loridalamrebusan']."','".$param['lorirestandepanrebusan']."','".$param['lorirestanbelakangrebusan']."') WHERE id=1";

    $query = 'UPDATE '.$dbname.".log_transaksi_mill SET 
    tbsolah='".$param['tbsolah']."',
    sisaawal='".$param['sisaawal']."',
    totallori='".$param['totallori']."',
    ratabuahlori='".$param['rataratabuahlori']."',
    kgpotsortasi='".$param['kgpotsortasi']."', 
    persenpotsortasi='".$param['persenpotsortasi']."', 
    tbsmasukafter='".$param['tbsmasukafter']."', 
    tbsolah='".$param['tbsolah']."',
    tbsolahafter='".$param['tbsolahafter']."',
    sisaakhir='".$param['sisaakhir']."',
    tbsmasuk='".$param['tbsmasuk']."',
    totalbuah='".$param['totalbuah']."',
    estimasidiperon='".$param['estimasidiperon']."',
    kodeorg='".$param['kodeorg']."',
    tanggal='".$param['tanggal']."',
    loriolah='".$param['loriolah']."',
    loridalamrebusan='".$param['loridalamrebusan']."',
    lorirestandepanrebusan='".$param['lorirestandepanrebusan']."',
    lorirestanbelakangrebusan='".$param['lorirestanbelakangrebusan']."'
    WHERE id='".$param['id']."'";
  
		if($cek['total'] != 0) {

			echo "data sudah ada";

		}	
        
		else if(mysql_query($query)) {

			echo "update berhasil";

		}	

		else {

            echo 'DB Error : '.mysql_error();

            exit();			

		}
        
	break;

        /*
        foreach ($data as $key => $cont) {

           
                unset($data[$key]);


        }

        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift']." and nik='".$param['cond_nik']."'";

        $query = updateQuery($dbname, 'pabrik_5shiftanggota', $data, $where);

        echo  $data;

        exit;


        if (!mysql_query($query)) {

            echo 'DB Error : '.mysql_error();

            exit();

        }



        echo json_encode($param);



        break;

        */


    case 'delete':
    $data = $param;

        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift']." and nik='".$param['nik']."'";

        $query = 'delete from `'.$dbname.'`.`pabrik_5shiftanggota` where '.$where;

        if (!mysql_query($query)) {

            echo 'DB Error : '.mysql_error();

            exit();

        }

    break;

    case 'deleteshift':
        $id=$param['id'];

        $query ="DELETE FROM log_transaksi_mill WHERE id=$id";
       
       
		if($cek['total'] != 0) {

			echo "data sudah ada";

		}	
        
		else if(mysql_query($query)) {

			echo "delete data berhasil";

		}	

		else {

            echo 'DB Error : '.mysql_error();

            exit();			

		}
    break;

    case 'addshift':	

        /*
		$qcek  = mysql_query('select count(kodeorg) as total from '.$dbname.'.pabrik_5shift where kodeorg="'.$param['kodeorg'].'" AND shift='.$param['shift'].'');

        $cek   = mysql_fetch_assoc($qcek);



        var param = 'id,nomertransaksi,nomertransaksi,tbsolah,kodeorg
        ,tanggal,loriolah,loridalamrebusan,lorirestandepanrebusan,lorirestanbelakangrebusan,estimasidiperon+
    '&rataratabuahperlori,sisaawal,tbsmasuk,totalbuah,tbsmasukafter,kgpotsortasi,tbsolah,tbsolahafter;
        
        */

        $tanggal = tanggalsystem($param['tanggal']);

        $query = 'insert into '.$dbname.".log_transaksi_mill 
        (notrans_tbsolah,kodeorg,tanggal,
        loriolah,loridalamrebusan,lorirestandepanrebusan,
        lorirestanbelakangrebusan,estimasidiperon,totallori,ratabuahlori,
        sisaawal,tbsmasuk,totalbuah,tbsmasukafter,kgpotsortasi,tbsolah,tbsolahafter,persenpotsortasi,sisaakhir)

          values('".$param['nomertransaksi']."','".$param['kodeorg']."',
          '".$tanggal."','".$param['loriolah']."',
          '".$param['loridalamrebusan']."','".$param['lorirestandepanrebusan']."',
          '".$param['lorirestanbelakangrebusan']."',
          '".$param['estimasidiperon']."',
          '".$param['totallori']."',
          '".$param['rataratabuahperlori']."',
          '".$param['sisaawal']."',
          '".$param['tbsmasuk']."',
          '".$param['totalbuah']."',
          '".$param['tbsmasukafter']."',
          '".$param['kgpotsortasi']."',
          '".$param['tbsolah']."',
          '".$param['tbsolahafter']."',
          '".$param['persenpotsortasi']."',
          '".$param['sisaakhir']."')";

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
    

    case 'generate_no':

        echo "test komar";

      
           
            $data=$param;

            
            $codeOrg=$param['kodeorg'];
            

            $tgl = date('Ymd');

            $bln = substr($tgl, 4, 2);

            $thn = substr($tgl, 0, 4);

            $notransaksi = $codeOrg.'/'.date('Y').'/'.date('m').'/';

            $ql = 'select `notrans_tbsolah` from '.$dbname.".`log_transaksi_mill` where notrans_tbsolah like '%".$notransaksi."%' order by `notrans_tbsolah` desc limit 0,1";

            $qr = mysql_query($ql);

            $rp = mysql_fetch_object($qr);

            $awal = substr($rp->notransaksi, -4, 4);

            $awal = (int) $awal;

            $cekbln = substr($rp->notransaksi, -7, 2);

            $cekthn = substr($rp->notransaksi, -12, 4);

            if ($cekbln != $bln && $cekthn != $thn) {

                $awal = 1;

            } else {

                ++$awal;

            }



            $counter = addZero($awal, 4);

            $notransaksi = $codeOrg.'/'.$thn.'/'.$bln.'/'.$counter;

            $optVhc .= "tesst";  
           // echo $optVhc.'###'.$notransaksi;
            echo $optVhc;

      

        break;



    default:

        break;

}



?>