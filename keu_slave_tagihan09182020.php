<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
include_once 'lib/devLibrary.php';
$proses = $_GET['proses'];
$param = $_POST;
$str = 'select karyawanid, namakaryawan from ' . $dbname . '.datakaryawan';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$nama[$bar->karyawanid] = $bar->namakaryawan;
}

switch ($proses) {
	case 'preview':
		$notransaksi = $_POST['notransaksi'];
        $jns = $_POST['jns'];
         $kodesupplier = $_POST['kodesupplier'];
        if($jns == 'sj'){


        $netto =0;
      
        $sCob = 'select * from '.$dbname.".pabrik_timbangan where  nospb in(".$notransaksi.") and trpcode='".$kodesupplier."' and IsPosting=1 and millcode like '%".$_SESSION['empl']['induklokasitugas']."%' order by notransaksi ASC";
      
        $res = mysql_query($sCob);
        $row = mysql_num_rows($res);
        if ($row > 0) {
            echo "<div style='overflow:scroll;width:100%'>\r\n\t <table class=sortable cellspacing=1 border=0 >\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No PO</td><td>No Kontrak</td>\r\n\t<td>Netto</td> <td>Harga</td><td>Sub Total</td>  </tr>\r\n\t</thead><tbody id=ListData>";
 
            while ($hsl = mysql_fetch_assoc($res)) {
                $no++;
                
                $nopo = $hsl['nospb'];
              	$sSPBantrian = 'select * from '.$dbname.".pabrik_antriantb where  nospb = '".$nopo."' ";
		$resSPBantrian = mysql_query($sSPBantrian);
        $hslAntrian = mysql_fetch_assoc($resSPBantrian);
        $tglAntrian = $hslAntrian['tanggal'];
        $sSPBTimbangngan = 'select date(tanggal) as tanggal from '.$dbname.".pabrik_timbangan where  nospb = '".$nopo."' and trpcode='".$kodesupplier."' and millcode like '%".$_SESSION['empl']['induklokasitugas']."%' ";
		$resSPBTimbangan = mysql_query($sSPBTimbangngan);
        $hslTimbangan = mysql_fetch_assoc($resSPBTimbangan);
        $tglTimbangan = $hslTimbangan['tanggal'];
		// $kodesupplier = $hsl['trpcode'];

        if($tglAntrian != ''){
        	$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglAntrian."' ";
        
		 
        	$resSPB = mysql_query($sSPB);
       		 $hsil = mysql_fetch_assoc($resSPB);
        	$harga = $hsil['harga_akhir'];
        	
        }else{
        		if($tglTimbangan != ''){
        				$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglTimbangan."' ";
        
		 
        				$resSPB = mysql_query($sSPB);
       		 			$hsil = mysql_fetch_assoc($resSPB);
        				$harga = $hsil['harga_akhir'];
        				
        				
        			}else{
        			if($harga ==''){
        	
        			$harga = '0';
        		
       			 }
        		}
        }
       
        
        $nilaiHarga = $hsl['beratnormal'] * $harga;
               
                $tanggal = substr($hsl['tanggal'], 10, 9);
                
                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td >".$no."</td>\r\n\t\t\t<td id=tglData_"
                .$no.'>'.$hsl['tanggal']."</td>\r\n\t\t\t<td id=notiket"
                .$no.'>'.$hsl['notransaksi']."</td>\r\n\t\t\t<td id=nospb"
                .$no.'>'.$hsl['nospb']."</td>\r\n\t\t\t<td id=kntrkno".$no.'>'.$hsl['nokontrak']."</td>\r\n\t\t\t<td id=netto"
                .$no.'>'.$hsl['beratnormal'].'</td></td><td>'.$harga.'</td><td>'.$nilaiHarga.'</td></tr>';
                $netto += $hsl['beratnormal'];
                $TnilaiHarga +=$nilaiHarga;

            }
            $presentase = ($netto/$tonase_ctr)*100;
           echo" <tr  class=rowcontent><td colspan=5>Total Netto :</td><td id=TotalNetto>".$netto."</td><td></td><td id=TotalNilai>".$TnilaiHarga."</td></tr><tr  class=rowcontent><td colspan=5> Total Data :</td> <td  id=MaxRow>".$row.'</td></tr>';
        } else {
        	$netto=0;
            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No DO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found</td><td colspan=30 id=MaxRow></td></tr><tr  class=rowcontent><td colspan=5>Total Netto :</td><td id=TotalNetto>".$netto."</td></tr>";
        }

        echo '</tbody></table></div>';
   		 }else if($jns == 'fee') {

        $netto =0;
      
        $sCob = 'select * from '.$dbname.".pabrik_timbangan where  nospb in(".$notransaksi.") and IsPosting=1  order by notransaksi and millcode like '%".$_SESSION['empl']['induklokasitugas']."%' ASC";
       
        $res = mysql_query($sCob);
        $row = mysql_num_rows($res);
        if ($row > 0) {
            echo "<div style='overflow:scroll;width:100%'>\r\n\t <table class=sortable cellspacing=1 border=0 >\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No PO</td><td>No Kontrak</td>\r\n\t<td>Netto</td> <td>Harga</td><td>Sub Total</td>  </tr>\r\n\t</thead><tbody id=ListData>";
 
            while ($hsl = mysql_fetch_assoc($res)) {
                $no++;
                
                $nopo = $hsl['nospb'];
              	$sSPBantrian = 'select * from '.$dbname.".pabrik_antriantb where  nospb = '".$nopo."' ";
		$resSPBantrian = mysql_query($sSPBantrian);
        $hslAntrian = mysql_fetch_assoc($resSPBantrian);
        $tglAntrian = $hslAntrian['tanggal'];
        $sSPBTimbangngan = 'select date(tanggal) as tanggal from '.$dbname.".pabrik_timbangan where  nospb = '".$nopo."' and millcode like '%".$_SESSION['empl']['induklokasitugas']."%' ";
		$resSPBTimbangan = mysql_query($sSPBTimbangngan);
        $hslTimbangan = mysql_fetch_assoc($resSPBTimbangan);
        $tglTimbangan = $hslTimbangan['tanggal'];
		$kodesupplier = $hsl['trpcode'];

        if($tglAntrian != ''){
        	$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglAntrian."' ";
        
		 
        	$resSPB = mysql_query($sSPB);
       		 $hsil = mysql_fetch_assoc($resSPB);
        	$harga = $hsil['fee'];
        	
        }else{
        		if($tglTimbangan != ''){
        				$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglTimbangan."' ";
        
		 
        				$resSPB = mysql_query($sSPB);
       		 			$hsil = mysql_fetch_assoc($resSPB);
        				$harga = $hsil['fee'];
        				
        				
        			}else{
        			if($harga ==''){
        	
        			$harga = '0';
        		
       			 }
        		}
        }
       	
        
        $nilaiHarga = $hsl['beratnormal'] * $harga;
               
                $tanggal = substr($hsl['tanggal'], 10, 9);
                
                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td >".$no."</td>\r\n\t\t\t<td id=tglData_"
                .$no.'>'.$hsl['tanggal']."</td>\r\n\t\t\t<td id=notiket"
                .$no.'>'.$hsl['notransaksi']."</td>\r\n\t\t\t<td id=nospb"
                .$no.'>'.$hsl['nospb']."</td>\r\n\t\t\t<td id=kntrkno".$no.'>'.$hsl['nokontrak']."</td>\r\n\t\t\t<td id=netto"
                .$no.'>'.$hsl['beratnormal'].'</td></td><td>'.$harga.'</td><td>'.$nilaiHarga.'</td></tr>';
                $netto += $hsl['beratnormal'];
                $TnilaiHarga +=$nilaiHarga;

            }
            $presentase = ($netto/$tonase_ctr)*100;
           echo" <tr  class=rowcontent><td colspan=5>Total Netto :</td><td id=TotalNetto>".$netto."</td><td></td><td id=TotalNilai>".$TnilaiHarga."</td></tr><tr  class=rowcontent><td colspan=5> Total Data :</td> <td  id=MaxRow>".$row.'</td></tr>';
        } else {
        	$netto=0;
            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No DO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found</td><td colspan=30 id=MaxRow></td></tr><tr  class=rowcontent><td colspan=5>Total Netto :</td><td id=TotalNetto>".$netto."</td></tr>";
        }

        echo '</tbody></table></div>';
   		 }else{
   		 	 $netto =0;
      
        $sCob = 'select a.* from '.$dbname.".pabrik_timbangan a,log_baspk b where  b.notransaksi = '".$notransaksi."'  order and a.notransaksi=b.notiket and a.IsPosting=1 and millcode like '%".$_SESSION['empl']['induklokasitugas']."%' by notransaksi ASC";
        $res = mysql_query($sCob);
        $row = mysql_num_rows($res);
        if ($row > 0) {
            echo "<div style='overflow:scroll;width:100%'>\r\n\t <table class=sortable cellspacing=1 border=0 >\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No PO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody id=ListData>";
 
            while ($hsl = mysql_fetch_assoc($res)) {
                $no++;
                $netto += $hsl['beratnormal'];
              
                
                $tanggal = substr($hsl['tanggal'], 10, 9);
                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td >".$no."</td>\r\n\t\t\t<td id=tglData_"
                .$no.'>'.$hsl['tanggal']."</td>\r\n\t\t\t<td id=notiket"
                .$no.'>'.$hsl['notransaksi']."</td>\r\n\t\t\t<td id=nospb"
                .$no.'>'.$hsl['nospb']."</td>\r\n\t\t\t<td id=kntrkno".$no.'>'.$hsl['nokontrak']."</td>\r\n\t\t\t<td id=netto"
                .$no.'>'.$hsl['beratnormal']."</td>   </tr>\r\n\t\t\t";
            }
            $presentase = ($netto/$tonase_ctr)*100;
           echo" <tr  class=rowcontent><td colspan=30>Total Netto :".$netto." (".$presentase."% dari Kontrak ".$tonase_ctr.")</td></tr><tr  class=rowcontent> Total Data :".$row." <td colspan=30 id=MaxRow>".$row.'</td></tr>';
        } else {
        	$netto=0;
            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No DO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found</td><td colspan=30 id=MaxRow></td></tr><tr  class=rowcontent><td colspan=5>Total Netto :</td><td id=TotalNetto>".$netto."</td></tr>";
        }

        echo '</tbody></table></div>';
   		 }
        break;
case 'showHeadList':
	$where = 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and updateby=\'' . $_SESSION['standard']['userid'] . '\'';

	if ($_SESSION['empl']['kodejabatan'] == 5) {
		$where = 'kodeorg like \'%\' and updateby like \'%\'';
	}

	if (isset($param['where'])) {
		$tmpW = str_replace('\\', '', $param['where']);
		$arrWhere = json_decode($tmpW, true);

		if (!empty($arrWhere)) {
			foreach ($arrWhere as $key => $r1) {
				if($r1[0]=='noinvoicesupplier'){
					$where .= ' AND ( noinvoice like \'%' . $r1[1] . '%\' OR ' . $r1[0] . ' like \'%' . $r1[1] . '%\' )';
				}
				if($r1[0]=='nopo' && $r1[1]!='' ){
					$where .= ' AND ' . $r1[0] . ' like \'%' . $r1[1] . '%\'';
				}
			}
//		$where .= ' )';
		}

	}

//	echo "warning".$where;
	
	$header = array('No Transaksi'.$param['page'] , $_SESSION['lang']['noinvoice'] . ' Supplier', $_SESSION['lang']['pt'], $_SESSION['lang']['tanggal'], 'Last Update', $_SESSION['lang']['nopo'],'Supplier', $_SESSION['lang']['keterangan'], $_SESSION['lang']['subtotal'], 'postingby', 'Posting Date', 'No VP');
	$cols = 'noinvoice,noinvoicesupplier,kodeorg,tanggal,updateby,nopo,(select namasupplier from log_5supplier a where supplierid=kodesupplier),keterangan,nilaiinvoice,postingby,tanggalposting,posting';
	$order = 'tanggal desc';
	$query = selectQuery($dbname, 'keu_tagihanht', $cols, $where, $order, false, $param['shows'], $param['page']);
saveLog($query);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname, 'keu_tagihanht', $where);

	foreach ($data as $key => $row) {
		if ($row['posting'] == 1) {
			$data[$key]['switched'] = false;
		}

		if (!empty($row['tanggalposting'])) {
			$data[$key]['tanggalposting'] = tanggalnormal($row['tanggalposting']);
		}

		unset($data[$key]['posting']);
		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'], 2);
		$data[$key]['updateby'] = $nama[$row['updateby']];

		$str="select novp from keu_vpht where noinv1='".$row['noinvoice']."' ";
		$sql1=mysql_query($str);
		$novp=mysql_fetch_assoc($sql1);
		$data[$key]['novp'] = $novp['novp'];

		if ($row['postingby'] == 0) {
			$data[$key]['postingby'] = '';
		}
		else {
			$data[$key]['postingby'] = $nama[$row['postingby']];
		}
	}

	$tHeader = new rTable('headTable', 'headTableBody', $header, $data);
	$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	// if (($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
		$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	// }
	// else {
	// 	$tHeader->addAction('', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	// }

	$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');

    $tHeader->addAction('detailPDF', 'PDF', 'images/' . $_SESSION['theme'] . '/pdf.jpg');
	$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');
	$tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
    $tHeader->_switchException = array('detailPDF');
	if (isset($param['where'])) {
		$tHeader->setWhere($arrWhere);
	}

	$tHeader->renderTable();
	break;

case 'showAdd':
	echo formHeader('add', array());
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

 case 'simpanDt':
         $noinvoice = $param['noinvoice'];
         $nospb = $param['nospb'];
         $notiket = $param['notiket'];
        $sIns = 'INSERT INTO '.$dbname.".`keu_tagihandt` (`noinvoice`, `nosj`, `notiket`) VALUES ('".$noinvoice."','".$nospb."','".$notiket."')";

         if (mysql_query($sIns)) {
           
            $stat = 1;
            echo $stat;
         }else{
         
             $stat = 0;
             echo $stat;
                
         }
break;
case 'getNilaiInvoice':
	$jns = $_POST['jns'];
	$notransaksi = $_POST['notransaksi'];
	$kodesupplier = $_POST['kodesupplier'];
	$TotalNetto = $_POST['totalnetto'];
	$TotalNilai = $_POST['totalnilai'];
	$nopo = $_POST['nopo'];
	$tagl = $_POST['tgl'];
	$data = explode("-" , $tagl);
	$day = $data[0];
	$month = $data[1];
	$year = $data[2];
	$tgl = $year.'-'.$month.'-'.$day;
	if($jns == 'sj'){
		
       
        
       
        echo $TotalNilai;
		
	}else if($jns == 'fee') {
		echo $TotalNilai;
	}else if($jns == 'kontrak'){
		 $sSPB = 'select * from '.$dbname.".log_spkdt where  notransaksi = '".$notransaksi."'";
        $resSPB = mysql_query($sSPB);
        $hsl = mysql_fetch_assoc($res);
        $harga = ($hsl['hasilkerjajumlah']) / ($hsl['jumlahrp']);
        $harga = $hsl['harga_akhir'];
        $nilaiinvoice = $TotalNetto * $harga;
        echo $nilaiinvoice;
	}else{
		$nilaiinvoice = 0;
		 echo $nilaiinvoice;
	}
	break;
	case 'getPPH':
	$kodesupplier = $_POST['kodesupplier'];
	$pph= $_POST['pph'];
			
		 $sSPB = 'select * from '.$dbname.".log_5supplier where  supplierid = '".$kodesupplier."' ";
        
		 
        $resSPB = mysql_query($sSPB);
        $hsl = mysql_fetch_assoc($resSPB);
        $isNPWP = $hsl['npwp'];
        if($isNPWP =='-' || $isNPWP =='' ){
        	if($pph=='pph23'){
        		echo '0.040';
        	}else if($pph==''){
                echo '0';
            }else{
        		echo '0.0050';
        	}
        	
        	
        }else{
        	if($pph=='pph23'){
        		echo '0.020';
        	}else if($pph==''){
                echo '0';
            }else{
        		echo '0.0025';
        	}
        	
        }
       
		
	
	break;
	case 'getPPNS':
	$kodesupplier = $_POST['kodesupplier'];
	
			
		 $sSPB = 'select * from '.$dbname.".log_5supplier where  supplierid = '".$kodesupplier."' ";
        
		
        $resSPB = mysql_query($sSPB);
        $hsl = mysql_fetch_assoc($resSPB);
        $isPKP = $hsl['pkp'];
        
        if($isPKP =='0' || $isPKP ==''){
 			echo '0';
        	
        	
        	
        }else{
        	
        	echo '0.1';
            
        	
        }
        	
        
       
		
	
	break;
case 'showEdit':
	$query = selectQuery($dbname, 'keu_tagihanht', '*', 'noinvoice=\'' . $param['noinvoice'] . '\'');
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	$data['jatuhtempo'] = tanggalnormal($data['jatuhtempo']);
	echo formHeader('edit', $data);
	// echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'add':
	$data = $_POST;


	if ($data['tipeinvoice'] == 'po') {
		$optPO = makeOption($dbname, 'log_poht', 'nopo,kodesupplier', 'stat_release=1');
		$sCek2 = 'select distinct  nilaipo as jmlhpo from ' . $dbname . '.log_poht where nopo=\'' . $data['nopo'] . '\' ';

		#exit(mysql_error($conn));
		($qCek2 = mysql_query($sCek2)) || true;
		$rCek2 = mysql_fetch_assoc($qCek2);
	}
	else if ($data['tipeinvoice'] == 'kontrak') {
		$sCek2 = 'select distinct nilaikontrak as jmlhpo from ' . $dbname . '.log_spkht where notransaksi=\'' . $data['nopo'] . '\' ';

		#exit(mysql_error($conn));
		($qCek2 = mysql_query($sCek2)) || true;
		$rCek2 = mysql_fetch_assoc($qCek2);
		$optPO = makeOption($dbname, 'log_spkht', 'notransaksi,koderekanan');
	}
	else if ($data['tipeinvoice'] == 'sj') {
		$optPO = makeOption($dbname, 'pabrik_timbangan', 'nospb,kodecustomer', 'nospb=\'' . $data['nopo'] . '\' AND kodecustomer=\'' . $data['kodesupplier'] . '\' ');
	}
    else if ($data['tipeinvoice'] == 'f') {
        $optPO = makeOption($dbname, 'pabrik_timbangan', 'nospb,kodecustomer', 'nospb=\'' . $data['nopo'] . '\' AND kodecustomer=\'' . $data['kodesupplier'] . '\' ');
    }
	else if ($data['tipeinvoice'] == 'ns') {
		$optPO = makeOption($dbname, 'log_konosemenht', 'nokonosemen,shipper', 'nokonosemen=\'' . $data['nopo'] . '\'');
	}
	else if ($data['tipeinvoice'] == 'ot') {
		if ($data['nopo'] == '') {
			exit('error: Field No.PO can\'t empty is represent a refrence document');
		}
	}


	$warning = '';

	if ($data['noinvoice'] == '') {
		$warning .= 'Invoice number is obligatory' . "\n";
	}

	if ($data['tanggal'] == '') {
		$warning .= 'Date is obligatory' . "\n";
	}

	if ($data['pph'] == '' && $data['perhitunganpph']>0) {
		$warning .= 'Jenis PPh Harus Dipilih' . "\n";
	}


	if ($warning != '') {
		echo 'Warning :' . "\n" . $warning;
		exit();
	}

	$data['tipeinvoice'] = substr($data['tipeinvoice'], 0, 1);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['nilaiinvoice'] = str_replace(',', '', $data['nilaiinvoice']);
	$data['uangmuka'] = str_replace(',', '', $data['uangmuka']);
	$data['nilaippn'] = str_replace(',', '', $data['nilaippn']);
	$data['potsusutkg'] = str_replace(',', '', $data['potsusutkg']);
	$data['potsusutjml'] = str_replace(',', '', $data['potsusutjml']);
	$data['potmutu'] = str_replace(',', '', $data['potmutu']);
	$data['potmutujml'] = str_replace(',', '', $data['potmutujml']);
//	$data['akunppn'] = str_replace(',', '', $data['akunppn']);
	$data['perhitunganpph'] = str_replace(',', '', $data['perhitunganpph']);
	//$data['pph'];

	if ($data['jatuhtempo'] != '') {
		$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
	}
	else {
		$data['jatuhtempo'] = '0000-00-00';
	}

	if ($data['tipeinvoice'] != 'o') {
//        if($data['tipeinvoice'] != 'f'){
           $data['kodesupplier'] = $optPO[$data['nopo']]; 
//        }
      
	}

	$data['updateby'] = $_SESSION['standard']['userid'];
	if (($data['tipeinvoice'] == 'p') || ($data['tipeinvoice'] == 'k')) {
		$sCek = 'select distinct sum(nilaiinvoice) as jmlhinvoice,noinvoice,updateby ' . 'from ' . $dbname . '.keu_tagihanht where nopo=\'' . $data['nopo'] . '\' order by noinvoice desc';

		#exit(mysql_error($conn));
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_fetch_assoc($qCek);

		if ($rCek2['jmlhpo'] < $rCek['jmlhinvoice']) {
			exit('Error: Previous invoice : ' . $rCek['noinvoice'] . ', amount: ' . number_format($rCek['jmlhinvoice'], 2) . ' greater than PO/Contract amount ' . number_format($rCek2['jmlhpo'], 2) . ',update by : ' . $nama[$rCek['updateby']]);
		}
	}

	$cols  = '';
	$values= '';

	foreach ($data as $key => $row) {
		$cols.= "$key,";
		$values.= "'$row',";
		//$cols[] = $key;
	}
	$cols = substr($cols,0,strlen($cols)-1); 
	$values = substr($values,0,strlen($values)-1); 
	$query = "insert into keu_tagihanht ($cols) values ($values)"; 
	//$query = insertQuery($dbname, 'keu_tagihanht', $data, $cols);
saveLog($query);
	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'edit':
	$data = $_POST;
	$where = 'noinvoice=\'' . $data['noinvoice'] . '\'';
	unset($data['noinvoice']);

	if ($data['tipeinvoice'] == 'po') {
		$optPO = makeOption($dbname, 'log_poht', 'nopo,kodesupplier', 'stat_release=1');
		$sCek2 = 'select (subtotal+misc+ongkosangkutan-nilaidiskon) as jmlhpo from ' . $dbname . '.log_poht where nopo=\'' . $data['nopo'] . '\' ';

		#exit(mysql_error($conn));
		($qCek2 = mysql_query($sCek2)) || true;
		$rCek2 = mysql_fetch_assoc($qCek2);
	}
	else if ($data['tipeinvoice'] == 'kontrak') {
		$sCek2 = 'select distinct nilaikontrak as jmlhpo from ' . $dbname . '.log_spkht where notransaksi=\'' . $data['nopo'] . '\' ';

		#exit(mysql_error($conn));
		($qCek2 = mysql_query($sCek2)) || true;
		$rCek2 = mysql_fetch_assoc($qCek2);
		$optPO = makeOption($dbname, 'log_spkht', 'notransaksi,koderekanan');
	}
	else if ($data['tipeinvoice'] == 'sj') {
		$optPO = makeOption($dbname, 'pabrik_timbangan', 'nospb,kodecustomer', 'nospb=\'' . $data['nopo'] . '\'');
	}
	else if ($data['tipeinvoice'] == 'ns') {
		$optPO = makeOption($dbname, 'log_konosemenht', 'nokonosemen,shipper', 'nokonosemen=\'' . $data['nopo'] . '\'');
	}
	else if ($data['tipeinvoice'] == 'ot') {
		if ($data['nopo'] == '') {
			exit('error: Field No.PO can\'t empty is represent a refrence document');
		}
	}

	if (($data['tipeinvoice'] == 'po') || ($data['tipeinvoice'] == 'kontrak')) {
		$sCek = 'select distinct sum(nilaiinvoice+nilaippn-perhitunganpph) as jmlhinvoice,noinvoice,updateby ' . 'from ' . $dbname . '.keu_tagihanht where nopo=\'' . $data['nopo'] . '\'  order by noinvoice desc';

		#exit(mysql_error($conn));
		($qCek = mysql_query($sCek)) || true;
		$rCek = mysql_fetch_assoc($qCek);

		if ($rCek2['jmlhpo'] < $rCek['jmlhinvoice']) {
			exit('Error: Previous invoices : ' . $rCek['noinvoice'] . ',amount: ' . number_format($rCek['jmlhinvoice'], 2) . ' greater than PO/Contract amount ' . number_format($rCek2['jmlhpo'], 2));
		}
	}

	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
	$data['tipeinvoice'] = substr($data['tipeinvoice'], 0, 1);
	$data['nilaiinvoice'] = str_replace(',', '', $data['nilaiinvoice']);
	$data['uangmuka'] = str_replace(',', '', $data['uangmuka']);
	$data['nilaippn'] = str_replace(',', '', $data['nilaippn']);
	$data['perhitunganpph'] = str_replace(',', '', $data['perhitunganpph']);

	$data['potsusutkg'] = str_replace(',', '', $data['potsusutkg']);
	$data['potsusutjml'] = str_replace(',', '', $data['potsusutjml']);
	$data['potmutu'] = str_replace(',', '', $data['potmutu']);
	$data['potmutujml'] = str_replace(',', '', $data['potmutujml']);
	$data['updateby'] = $_SESSION['standard']['userid'];
	$cols  = '';  
	foreach ($data as $key => $row) {
		$cols.= "$key='$row',"; 
	}
	$cols = substr($cols,0,strlen($cols)-1);  
	$query = "update keu_tagihanht set $cols where $where "; 
	saveLog($query);
	//$query = updateQuery($dbname, 'keu_tagihanht', $data, $where);
	echo $query;
	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'delete':
	$where = 'noinvoice=\'' . $param['noinvoice'] . '\'';
	$query = 'delete from `' . $dbname . '`.`keu_tagihanht` where ' . $where;
    $query2 = 'delete from `' . $dbname . '`.`keu_tagihandt` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}else{
        if (!mysql_query($query2)) {
        echo 'DB Error : ' . mysql_error();
        exit();
    }
    }

	break;

case 'updpo':
	$pokontrak = $_POST['pokontrak'];

	if ($pokontrak == 'po') {
		$resPO = makeOption($dbname, 'log_poht', 'nopo,nopo', 'stat_release=1', '0', true);
	}

	if ($pokontrak == 'sj') {
		$resPO = makeOption($dbname, 'log_pengiriman_ht', 'nosj,nosj', '0', true);
	}
	else {
		$resPO = makeOption($dbname, 'log_spkht', 'notransaksi,notransaksi', 'kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'', '0', true);
	}

	echo json_encode($resPO);
	break;

case 'updInvoice':
	$query = selectQuery($dbname, 'keu_tagihanht', 'nilaiinvoice', 'nopo=\'' . $_POST['nopo'] . '\'');
	$res = fetchData($query);

	if (!empty($res)) {
		echo $res[0]['nilaiinvoice'];
	}

	break;

case 'getPo':
	$jenisInvoice = $_POST['jnsInvoice'];
	$optNmsupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
	$dat = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>';
	$dat .= '<div style=overflow:auto;width:100%;height:310px;>';
	$dat .= '<table cellpadding=1 cellspacing=1 border=0 class=\'sortable\'><thead>';
	$dat .= '<tr class=\'rowheader\'><td>No.</td>';
	$rPo['ppn'] = 0;
	$where = '';

	switch ($jenisInvoice) {
		case 'po':
			if ($param['txtfind'] != '') {
				$where = ' and c.nopo like \'%' . $param['txtfind'] . '%\' ';
			}

			$addlokal = ' and lokalpusat=0 ';
			$addkdorg = ' and c.kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';

			if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
				$addlokal = ' and lokalpusat=1 ';
				$addkdorg = '';
			}

			$sPo = "select distinct nopo, (subtotal+misc+ongkosangkutan-nilaidiskon) as nilaipo, (ppn+ongkirimppn+miscppn) as ppn,kodesupplier,closed,pph , (SELECT SUM(jumlah*hargasatuan) AS retur FROM log_transaksidt a INNER JOIN log_transaksiht b ON a.notransaksi=b.notransaksi WHERE b.nopo=c.nopo AND tipetransaksi='6') AS retur from ". $dbname . '.log_poht c where stat_release=\'1\' AND kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' ' . $where . ' ' . $addlokal . '   order by tanggal desc';
		saveLog($sPo);
			$dat .= '<td>' . $_SESSION['lang']['nopo'] . '</td>';
			$dat .= '<td>' . $_SESSION['lang']['namasupplier'] . '</td>';
			$dat .= '<td> Nilai PO </td>';
			$dat .= '<td> Ppn </td>';
			$dat .= '<td> Pph </td></tr></thead><tbody>';
			
		break;
		case 'kontrak':
			if ($proses = $_GET['proses']) {
				$where = ' and notransaksi like \'%' . $param['txtfind'] . '%\'';
			}

			if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
				$sPo = 'select distinct notransaksi as nopo,nilaikontrak as nilaipo,koderekanan as kodesupplier from ' . $dbname . '.log_spkht where kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['org']['kodeorganisasi'] . '\')  ' . $where . ' and notransaksi not in(select nopo from keu_tagihanht)  order by tanggal desc';
				
			}
			else {
				$sPo = 'select distinct notransaksi as nopo,nilaikontrak as nilaipo,koderekanan as kodesupplier from ' . $dbname . '.log_spkht where  kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['org']['kodeorganisasi'] . '\') ' . $where . ' and notransaksi not in(select nopo from keu_tagihanht)   order by tanggal desc';
				
			}
			
			$dat .= '<td>' . $_SESSION['lang']['kontrak'] . '</td>';
			$dat .= '<td>' . $_SESSION['lang']['kontraktor'] . '</td></tr></thead><tbody>';
		break;
		case 'sj':
			if ($param['txtfind'] != '') {
				$where = ' and  log_5supplier.namasupplier like \'%' . $param['txtfind'] . '%\'  ';

			}else{
				$where = ' ';
			}

			$sPo = 'select distinct pabrik_timbangan.nospb as nopo,pabrik_timbangan.trpcode as kodesupplier, log_5supplier.namasupplier as namasupplier, date(pabrik_timbangan.tanggal) as tgl from ' . $dbname . '. pabrik_timbangan, log_5supplier ' . "\r\n\t\t\t\t\t" . '   where  millcode like \''.$_SESSION['empl']['induklokasitugas'].'%\'  and pabrik_timbangan.trpcode=log_5supplier.supplierid and substr(pabrik_timbangan.notransaksi,1,1)=\'M\' and pabrik_timbangan.notransaksi not in(select notiket from keu_tagihandt)  ' . $where . '  order by date(pabrik_timbangan.tanggal),pabrik_timbangan.nospb desc ';
           
            
			$dat .= '<td>Aksi</td>';
			$dat .= '<td>' . $_SESSION['lang']['nosj'] . '</td>';
			$dat .= '<td>' . $_SESSION['lang']['expeditor'] . '</td>';
			$dat .= '<td>Tanggal</td></tr></thead><tbody>';
			break;
		case 'fee':
			if ($param['txtfind'] != '') {
				$where = 'where keu_tagihandt.nosj like \'%' . $param['txtfind'] . '%\' or log_5supplier.namasupplier like \'%' . $param['txtfind'] . '%\' and keu_tagihanht.noinvoice=keu_tagihandt.noinvoice and keu_tagihandt.nosj not in(select keu_tagihandt.nosj from keu_tagihanht,keu_tagihandt WHERE keu_tagihanht.noinvoice=keu_tagihandt.noinvoice and keu_tagihanht.tipeinvoice=\'f\')';
			}else{
				$where = 'where keu_tagihanht.noinvoice=keu_tagihandt.noinvoice and keu_tagihandt.nosj not in(select keu_tagihandt.nosj from keu_tagihanht,keu_tagihandt WHERE keu_tagihanht.noinvoice=keu_tagihandt.noinvoice and keu_tagihanht. tipeinvoice=\'f\')';
			}

			$sPo = 'select distinct keu_tagihandt.nosj as nopo,keu_tagihanht.kodesupplier as kodesupplier, log_5supplier.namasupplier as namasupplier,DATE(pabrik_timbangan.tanggal) AS tgl from fastenvi_mpsg_live. keu_tagihanht, keu_tagihandt,log_5supplier,pabrik_timbangan ' . "\r\n\t\t\t\t\t" . '   ' . $where . ' and keu_tagihandt.nosj=pabrik_timbangan.nospb and keu_tagihanht.kodesupplier=log_5supplier.supplierid and tipeinvoice=\'s\'  order by keu_tagihandt.nosj desc';
		
			$dat .= '<td>Aksi</td>';
			$dat .= '<td>' . $_SESSION['lang']['nosj'] . '</td>';
			$dat .= '<td>' . $_SESSION['lang']['expeditor'] . '</td>';
			$dat .= '<td>Tanggal</td></tr></thead><tbody>';
			break;

		case 'ns':
			if ($param['txtfind'] != '') {
				$where = 'where nokonosemen like \'%' . $param['txtfind'] . '%\'';
			}

			$sPo = 'select distinct nokonosemen as nopo,shipper as kodesupplier from ' . $dbname . '. log_konosemenht ' . "\r\n\t\t\t\t\t" . '   ' . $where . ' and nopo not in(select nopo from keu_tagihanht)  order by nokonosemen desc';
			$dat .= '<td>' . $_SESSION['lang']['nokonosemen'] . '</td>';
			$dat .= '<td>' . $_SESSION['lang']['shipper'] . '</td></tr></thead><tbody>';
			break;
	}
	#echo $sPo;
	#exit(mysql_error($conn));

	$qPo = mysql_query($sPo);
	$no = 0;
	#showerror();
	while ($rPo = mysql_fetch_assoc($qPo)) {
		if ($jenisInvoice == 'po') {
			$sCek = 'select sum(nilaiinvoice) as jmlhinvoice,sum(nilaippn) as jmlppn,noinvoice,updateby ' . 'from ' . $dbname . '.keu_tagihanht where nopo=\'' . $rPo['nopo'] . '\' order by noinvoice';
		//	saveLog($sCek);
			$qCek = mysql_query($sCek);
			$rCek = mysql_fetch_assoc($qCek);

			$rPo['nilaipo'] = $rPo['nilaipo'] - $rPo['retur'];
			
			if($rPo['ppn']>0){
				$rPo['ppn'] = $rPo['ppn'] - ($rPo['retur']*10/100);
			}

			if ($rCek['jmlhinvoice'] != '') {
				$rPo['nilaipo'] = $rPo['nilaipo'] - $rCek['jmlhinvoice'];
				$rPo['ppn'] = $rPo['ppn'] - $rCek['jmlppn'];
			}

			if ($rPo['nilaipo'] >1) {
				if ($brgCompr[$rPo['nopo']] == '') {
						$no += 1;
						$dat .= '<tr class=\'rowcontent\' onclick="setPo(\'' . $rPo['nopo'] . '\',\'' . $rPo['kodesupplier'] . '\',\'';
						$dat .= (isset($rPo['nilaipo']) ? $rPo['nilaipo'] : 0);
						$dat .= '\',\'' . $param['jnsInvoice'] . '\',\'';
						$dat .= (isset($rPo['pph']) ? $rPo['pph'] : 0);
						$dat .= '\',\'';
						$dat .= (isset($rPo['ppn']) ? $rPo['ppn'] : 0);
						$dat .= '\')" style=\'pointer:cursor;\'><td>' . $no . '</td>';
						$dat .= '<td>' . $rPo['nopo'] . '</td>';
						$dat .= '<td>' . $optNmsupp[$rPo['kodesupplier']] . '</td>';
						$dat .= '<td align=right>' . $rPo['nilaipo'] . '</td>';						
						$dat .= '<td align=right>' . $rPo['ppn'] . '</td>';
						$dat .= '<td align=right>' . $rPo['pph'] . '</td></tr>';
				}
				else if ($rPo['closed'] == '1') {
					$no += 1;
					$dat .= '<tr class=\'rowcontent\' onclick="setPo(\'' . $rPo['nopo'] . '\',\'' . $rPo['kodesupplier'] . '\',\'';
					$dat .= (isset($rPo['nilaipo']) ? $rPo['nilaipo'] : 0);
					$dat .= '\',\'' . $param['jnsInvoice'] . '\',\'';
					$dat .= (isset($rPo['pph']) ? $rPo['pph'] : 0);
					$dat .= '\',\'';
					$dat .= (isset($rPo['ppn']) ? $rPo['ppn'] : 0);
					$dat .= '\')" style=\'pointer:cursor;\'><td>' . $no . '</td>';
					$dat .= '<td>' . $rPo['nopo'] . '</td>';
					$dat .= '<td>' . $optNmsupp[$rPo['kodesupplier']] . '</td>';
					$dat .= '<td align=right>' . $rPo['nilaipo'] . '</td>';						
					$dat .= '<td align=right>' . $rPo['ppn'] . '</td>';
					$dat .= '<td align=right>' . $rPo['pph'] . '</td></tr>';
				}
			}
		}
		else {
			if ($jenisInvoice == 'sj') {
			$no += 1;
			$dat .= "<tr class=\'rowcontent\'><td> <input type=checkbox   id=chk".$no."  class=myinputtext onchange=pilihchk('".$rPo['nopo']."','".$no."','".$rPo['kodesupplier']."')></td><td>". $no . "</td>";
			$dat .= '<td>' . $rPo['nopo'] . '</td>';
			$dat .= '<td>' . $optNmsupp[$rPo['kodesupplier']] . '</td>';
			$dat .= '<td>' . $rPo['tgl'] . '</td>';
			$dat .= '<td align=right>' . $rPo['nilaipo'] . '</td>';						
			$dat .= '<td align=right>' . $rPo['ppn'] . '</td>';
			$dat .= '<td align=right>' . $rPo['pph'] . '</td></tr>';
		}else if ($jenisInvoice == 'fee') {
			$no += 1;
			$dat .= "<tr class=\'rowcontent\'><td> <input type=checkbox   id=chk".$no."  class=myinputtext onchange=pilihchk('".$rPo['nopo']."','".$no."','".$rPo['kodesupplier']."')></td><td>". $no . "</td>";
			$dat .= '<td>' . $rPo['nopo'] . '</td>';
			$dat .= '<td>' . $optNmsupp[$rPo['kodesupplier']] . '</td>';
			$dat .= '<td>' . $rPo['tgl'] . '</td>';
			$dat .= '<td align=right>' . $rPo['nilaipo'] . '</td>';						
			$dat .= '<td align=right>' . $rPo['ppn'] . '</td>';
			$dat .= '<td align=right>' . $rPo['pph'] . '</td></tr>';
		}else{

					$no += 1;
			$dat .= '<tr class=\'rowcontent\' onclick="setPo(\'' . $rPo['nopo'] . '\',\'' . $rPo['kodesupplier'] . '\',\'';
			$dat .= (isset($rPo['nilaipo']) ? $rPo['nilaipo'] : 0);
			$dat .= '\',\'' . $param['jnsInvoice'] . '\',\'';
			$dat .= (isset($rPo['pph']) ? $rPo['pph'] : 0);
			$dat .= '\',\'';
			$dat .= (isset($rPo['ppn']) ? $rPo['ppn'] : 0);
			$dat .= '\')" style=\'pointer:cursor;\'><td>' . $no . '</td>';
			$dat .= '<td>' . $rPo['nopo'] . '</td>';
			$dat .= '<td>' . $optNmsupp[$rPo['kodesupplier']] . '</td>';
			$dat .= '<td align=right>' . $rPo['nilaipo'] . '</td>';						
			$dat .= '<td align=right>' . $rPo['ppn'] . '</td>';
			$dat .= '<td align=right>' . $rPo['pph'] . '</td></tr>';
		}
		}
	}

	$dat .= '</tbody></table></div></fieldset>';
	echo $dat;
	break;

case 'cekStatus':
	$np = $param['np'];
	$sb = $param['sb'];

	if ($sb == 'CREDIT') {
		$a = 'select notransaksi from ' . $dbname . '.log_transaksiht where nopo=\'' . $np . '\' and post=1 ';

		#exit(mysql_error($conn));
		($b = mysql_query($a)) || true;
		$c = mysql_fetch_assoc($b);
		$notran = $c['notransaksi'];

		if ($notran == '') {
			echo 'A';
		}
	}

	break;

}

function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['noinvoice'] = date('Ymdhis');
		$data['noinvoicesupplier'] = '';
		$data['nilaiinvoice'] = '0';
		$data['nilaiinvoiceA'] = '0';
		$data['noakun'] = '';
		$data['tanggal'] = '';
		$data['tipeinvoice'] = '';
		$data['nopo'] = '';
		$data['jatuhtempo'] = '';
		$data['nofp'] = '';
		$data['keterangan'] = '';
		$data['uangmuka'] = '0';
		$data['nilaippn'] = '0';
		$data['kodeorg'] = '';
		$data['potsusutkg'] = '0';
		$data['potsusutjml'] = '0';
		$data['potmutu'] = '0';
		$data['potmutujml'] = '0';
		//$data['pph']= '';
		$data['perhitunganpph']= '0';
	}
	else {
		$nilaiasli = $data['nilaiinvoice'];
		$data['nilaiinvoice'] = number_format($data['nilaiinvoice'], 2);

		$data['uangmuka'] = $data['uangmuka'];
		$data['potsusutkg'] = $data['potsusutkg'];
		$data['potsusutjml'] = $data['potsusutjml'];
		$data['potmutu'] = $data['potmutu'];
		$data['potmutujml'] = $data['potmutujml'];
		if($data['potmutu']=='0'){
			$potmutujml = '0';
		}else{
			 $potmutudes= ($data['potmutu']/100);
			$potmutujml =  $potmutudes*$nilaiasli;
		}
		$nilaiinvoiceA1 =($nilaiasli ) - (($potmutujml)+($data['potsusutjml']));
		$nilaiinvoiceA =(($nilaiinvoiceA1 )+ ($data['nilaippn']))-$data['perhitunganpph'];
		$data['nilaiinvoiceA'] = $nilaiinvoiceA;
		$data['nilaippn'] = $data['nilaippn'];
		$data['perhitunganpph'] = $data['perhitunganpph'];
		$whrdt = 'noinvoice=\'' . $data['noinvoice'] . '\'';
		$tmpNopo = makeOption($dbname, 'keu_tagihanht', 'noinvoice,tipeinvoice', $whrdt);

		if ($tmpNopo[$data['noinvoice']] == 'p') {
			$data['tipeinvoice'] = 'po';
		}
		else if ($tmpNopo[$data['noinvoice']] == 's') {
			$data['tipeinvoice'] = 'sj';
		}
		else if ($tmpNopo[$data['noinvoice']] == 'n') {
			$data['tipeinvoice'] = 'ns';
		}
		else if ($tmpNopo[$data['noinvoice']] == 'k') {
			$data['tipeinvoice'] = 'kontrak';
		}
		else if ($tmpNopo[$data['noinvoice']] == 'o') {
			$data['tipeinvoice'] = 'ot';
		}
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
	}
	else {
		$disabled = '';
	}

	$disabled2 = 'disabled';
	$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
	$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', 'kasbank=1 and detail=1');
	$optSupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');

	if ($data['tipeinvoice'] == 'po') {
		$optPO = makeOption($dbname, 'log_poht', 'nopo,nopo', 'stat_release=1', '0', true);
	}

	if ($data['tipeinvoice'] == 'sj') {
		// $optPO = makeOption($dbname, ' log_suratjalanht', 'nosj,nosj', NULL, '0', true);
	}

	if ($data['tipeinvoice'] == 'ns') {
		$optPO = makeOption($dbname, ' log_konosemenht', 'nokonosemen,nokonosemen', NULL, '0', true);
	}
	else {
		$optPO = makeOption($dbname, 'log_spkht', 'notransaksi,notransaksi', NULL, '0', true);
	}

	$optCgt = getEnum($dbname, 'keu_kasbankht', 'cgttu');
	$optYn = array($_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']);
	$optTipe = array('po' => 'PO', 'kontrak' => $_SESSION['lang']['kontrak'], 'sj' => $_SESSION['lang']['suratjalan'], 'ns' => $_SESSION['lang']['konosemen'], 'ot' => $_SESSION['lang']['lain'],'fee' => 'Fee');
	$optPph = getPPhOptions();$optPph = getPPhOptions();//array('' => '[Pilih Pph]','pph21Final' => 'PPh 21 Final', 'pph22' => 'PPh 22', 'pph23' => 'PPh 23', 'pph15' => 'PPh 15', 'pph4(2)' => 'PPh 4 (2)');
//	$optPpn = getPPnOptions();
//	$optPpn=array(''=>'[Pilih PPn]');
//	eventOnScrollDB("SELECT DISTINCT * FROM Keu_5akun WHERE namaakun LIKE '%ppn%'",
//		$optPpn,function($row,$var,&$definedVar){
////			array_merge($definedVar,array($row['noakun']=>$row['namaakun']));
//			array_push($definedVar[$row['noakun']]=$row['namaakun']);
//		});

	$els = array();
	$els[] = array(makeElement('noinvoice', 'label', $_SESSION['lang']['notransaksi']), makeElement('noinvoice', 'text', $data['noinvoice'], array('style' => 'width:150px', 'maxlength' => '20', 'disabled' => 'disabled')));
	$els[] = array(makeElement('noinvoicesupplier', 'label', $_SESSION['lang']['noinvoice'] . ' Supplier'), makeElement('noinvoicesupplier', 'text', $data['noinvoicesupplier'], array('style' => 'width:150px', 'maxlength' => '25')));
	$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], array('style' => 'width:150px'), $optOrg));
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:150px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('tipeinvoice', 'label', $_SESSION['lang']['jenis']), makeElement('tipeinvoice', 'select', $data['tipeinvoice'], array('style' => 'width:150px', $disabled => $disabled, 'onchange' => 'updPO()'), $optTipe));
	$els[] = array(makeElement('nopo', 'label', $_SESSION['lang']['nopo']), makeElement('nopo', 'text', $data['nopo'], array('style' => 'width:150px', '', 'onclick' => 'searchNopo(\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['nopo'] . '\',\'' . $_SESSION['lang']['find'] . '<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findNopo()>Find</button><div id=container2></div>\',event)')));
	$els[] = array(makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', $data['keterangan'], array('style' => 'width:150px')));
	$els[] = array(makeElement('jatuhtempo', 'label', $_SESSION['lang']['jatuhtempo']), makeElement('jatuhtempo', 'text', $data['jatuhtempo'], array('style' => 'width:150px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('nofp', 'label', $_SESSION['lang']['nofp']), makeElement('nofp', 'text', $data['nofp'], array('style' => 'width:150px', 'maxlength' => '20')));
	$els[] = array(makeElement('kodesupplier', 'label', $_SESSION['lang']['namasupplier']), makeElement('kodesupplier', 'selectsearch', $data['kodesupplier'], array('style' => 'width:150px', 'disabled' => 'disabled'), $optSupplier));
	$els[] = array(makeElement('nilaiinvoice', 'label', $_SESSION['lang']['nilaiinvoice']), makeElement('nilaiinvoice', 'textnum', $data['nilaiinvoice'], array('style' => 'width:150px', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)','onclick' => 'this.value=remove_comma(this);this.value = _formatted(this)')));
	$els[] = array(makeElement('potosusut', 'label', 'Potongan Susut(Kg)'), makeElement('potsusutkg', 'textnum', $data['potsusutkg'], array('style' => 'width:150px')));
	$els[] = array(makeElement('potosusut', 'label', 'Potongan Susut(Jumlah) '), makeElement('potsusutjml', 'textnum', $data['potsusutjml'], array('style' => 'width:150px','onclick'=>'getPotSusut()')));
	$els[] = array(makeElement('potosusut', 'label', 'Potongan Mutu(%) '), makeElement('potmutu', 'textnum', $data['potmutu'], array('style' => 'width:150px')));
	$els[] = array(makeElement('potosusut', 'label', 'Potongan Mutu(Jumlah) '), makeElement('potmutujml', 'textnum', $data['potmutujml'], array('style' => 'width:150px')));
	$els[] = array(makeElement('noakun', 'label', $_SESSION['lang']['noakun']), makeElement('noakun', 'select', $data['noakun'], array('style' => 'width:150px'), $optAkun));
	//$els[] = array(makeElement('uangmuka', 'label', $_SESSION['lang']['uangmuka']), makeElement('uangmuka', 'textnum', $data['uangmuka'], array('style' => 'width:150px', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')));

//	$els[] = array(makeElement('akunppn', 'label', 'PPn'),
//		           makeElement('akunppn', 'select', $data['akunppn'],
//					   array('style' => 'width:150px'), $optPpn));

	$els[] = array(makeElement('nilaippn', 'label','Nilai PPn'), makeElement('nilaippn', 'textnum', $data['nilaippn'], array('style' => 'width:150px','onclick'=>'getppn()', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')));
	$els[] = array(makeElement('pph', 'label', 'PPh'), makeElement('pph', 'select', $data['pph'],
		array('style' => 'width:150px'), $optPph));
	$els[] = array(makeElement('perhitunganpph', 'label', 'Nilai PPh'), makeElement('perhitunganpph', 'textnum', $data['perhitunganpph'], array('style' => 'width:150px','onclick'=>'getPPH()', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')));
	$els[] = array(makeElement('nilaiinvoiceA', 'label', 'Nilai Invoice Akhir'), makeElement('nilaiinvoiceA', 'textnum', $data['nilaiinvoiceA'], array('style' => 'width:150px','onclick'=>'getNilaiInvoice()', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')));
	

	


	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'editDataTable()')));
	}

	if ($mode == 'add') {
		return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
	}

	if ($mode == 'edit') {
		return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
	}
}
?>
