<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;


	global $conn;
	global $tanggal;
	global $param;
	global $dbname;
	$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi where ' . "\r\n" . ' kodeorg =\'' .$param['kodeorg']. '\' and tutupbuku=0';
	$tgmulai = '';
	$tgsampai = '';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: Tidak ada periode akuntansi untuk induk ' .$param['kodeorg']);
	}

	while ($bar = mysql_fetch_object($res)) {
		$tgsampai = $bar->tanggalsampai;
		$tgmulai = $bar->tanggalmulai;
	}

	if (($tgmulai == '') || ($tgsampai == '')) {
		exit('Error: Periode akuntasi tidak terdaftar');
	}

	$tanggal = $tgsampai;
	$group = 'PYRL1';
	$str = 'select noakunkredit from ' . $dbname . '.keu_5parameterjurnal' . "\r\n" . '  where jurnalid=\'' . $group . '\' limit 1';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		exit('Error: No.Akun pada parameterjurnal belum ada untuk PYRL1');
	}
	else {
		$bar = mysql_fetch_object($res);
		$akunalok = $bar->noakunkredit;
	}

	$kodeJurnal = $group;
	$tgmulaid = $tanggal;
	$pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);

		$lokres= 'E';
		if (substr($param['kodeorg'],0,3)=='LSP' || substr($param['kodeorg'],0,3)=='SSP' || substr($param['kodeorg'],0,3)=='MPS'){
			$lokres= 'M';
		}
		$str= "SELECT * FROM sdm_ho_hr_jms_porsi where lokasiresiko='".$lokres."' AND id='perusahaan'";

		$res = mysql_query($str);
		while ($row = mysql_fetch_array($res)) {
			$persenjhtpt=$row['jhtpt'];
			$persenjppt=$row['jppt'];
			$maksjppt=$row['jmppt']; //FA 20200228 - untuk maksimal BPJS TK - JP
		}


	//PROSES UNTUK KANTOR PUSAT
	if($param['tipe']=='HOLDING'){

	$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

	$tmpKonter = fetchData($queryJ);
	$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
	$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;


	$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']."','1','IDR','0','1')";
	$head=mysql_query($qryht);

	$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
	$upcount=mysql_query($upcounter);

	$str1="	SELECT a.kodeorg, a.periodegaji, SUM(a.jumlah) AS jumlah, plus, alias, noakun_ho AS noakun_debet, noakun_kredit  
FROM sdm_gaji a inner join sdm_ho_component h ON a.idkomponen=h.id INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' GROUP BY h.alias, plus ORDER BY id, plus desc";

	$res1 = mysql_query($str1);
	$x=0;
	while ($rows = mysql_fetch_array($res1)) {

		if($rows['plus']=='1'){
			
			if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah'])."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
				$dt=mysql_query($qrydt);
				$debet=$debet+$rows['jumlah'];
			}
			if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
				$dt=mysql_query($qrydt);
				$kredit=$kredit+($rows['jumlah']*-1);
			}	

		}

		if($rows['plus']=='0'){
		
			if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
			}
			if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
			}	

		}

	}
	
			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".round($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
			$dt=mysql_query($qrydt);
		
/*
			//PROSES JURNAL JHT & JP PERUSAHAAN
	
				$jumlah=0;
				
				$qry="select distinct(karyawanid) 
							FROM sdm_gaji where periodegaji='".$param['periode']."' 
							AND kodeorg='".$param['kodeorg']."' AND (idkomponen='5' or idkomponen='9')";
				$queri = mysql_query($qry);
				while ($hasil = mysql_fetch_array($queri)) {
					
					$nilaix= 0;
					$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
					$timex = strtotime($datex);
					$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}

							$jhtpt=$persenjhtpt*$nilaix/100;

							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							
						$jumlahjht=$jumlahjht+$jhtpt;		
						$jumlahjp=$jumlahjp+$jppt;			

					}

				}
				
			$qry1="select noakun_ho, noakun_kredit from sdm_ho_component where id='55'";
			$queri1=mysql_query($qry1);
			$hasil=mysql_fetch_assoc($queri1);

				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$hasil['noakun_ho']."','".$param['periode'] .": Alokasi Gaji BPJS TK (JHT) - PERS','".round($jumlahjht)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);
			
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$hasil['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji BPJS TK (JHT) - PERS','".round($jumlahjht*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);

			$qry1="select noakun_ho, noakun_kredit from sdm_ho_component where id='56'";
			$queri1=mysql_query($qry1);
			$hasil=mysql_fetch_assoc($queri1);

				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$hasil['noakun_ho']."','".$param['periode'] .": Alokasi Gaji Jaminan Pensiun (JP) - PERSH','".round($jumlahjp)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);
			
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$hasil['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji Jaminan Pensiun (JP) - PERSH','".round($jumlahjp*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI','','','','','')";
					$dt=mysql_query($qrydt);
*/
	}


	//PROSES UNTUK PABRIK
	if($param['tipe']=='PABRIK'){


		//PROSES MILL
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." PROSES MILL','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);

		$str1="	SELECT a.kodeorg, a.periodegaji, SUM(a.jumlah) AS jumlah, plus, alias, noakun_millproses AS noakun_debet, noakun_kredit, subbagian  
			FROM sdm_gaji a inner join sdm_ho_component h ON a.idkomponen=h.id INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid 
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi 
			where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND tipe='STATION'  GROUP BY subbagian, h.alias, plus ORDER BY id, plus desc";

		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah'])."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI PROSES MILL','','','','".$rows['subbagian']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']);
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI PROSES MILL','','','','".$rows['subbagian']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI PROSES MILL','','','','".$rows['subbagian']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI PROSES MILL','','','','".$rows['subbagian']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'ALOKASI GAJI PROSES MILL','','','','','')";
			$dt=mysql_query($qrydt);

		//END PROSES MILL


		//UMUM MILL
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." UMUM MILL','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);


		$str1="	SELECT a.kodeorg, a.periodegaji, SUM(a.jumlah) AS jumlah, plus, alias, noakun_millumum AS noakun_debet, noakun_kredit  
			FROM sdm_gaji a inner join sdm_ho_component h ON a.idkomponen=h.id INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid 
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi 
			where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND  ( tipe is null OR tipe='PABRIK' OR tipe='GUDANG')  GROUP BY h.alias, plus ORDER BY id, plus desc";

				$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah'])."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM MILL','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM MILL','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM MILL','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM MILL','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".round($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM MILL','','','','','')";
			$dt=mysql_query($qrydt);


		 //END UMUM MILL


		//WORKSHOP
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." WORKSHOP','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);

		$str1="	SELECT a.kodeorg, a.periodegaji, SUM(a.jumlah) AS jumlah, plus, alias, noakun_workshop AS noakun_debet, noakun_kredit  
			FROM sdm_gaji a inner join sdm_ho_component h ON a.idkomponen=h.id INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid 
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi 
			where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND  tipe='WORKSHOP'  GROUP BY h.alias, plus ORDER BY id, plus desc";

		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah'])."','IDR','1','".$param['kodeorg']."', '','','','','','', 'WORKSHOP','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'WORKSHOP','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'WORKSHOP','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'WORKSHOP','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".round($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'WORKSHOP','','','','','')";
			$dt=mysql_query($qrydt);

	 //END WORKSHOP


		//TRAKSI
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." TRAKSI ','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);

		$str1="SELECT kodeorg, periodegaji, kodevhc as vhc, sum(round(jumlah*jmlh/(SELECT sum(jumlah) FROM vhc_rundt_operatorvw y where y.idkaryawan=x.karyawanid AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) )AS jumlah, plus, alias, noakun_traksi AS noakun_debet, noakun_kredit FROM (SELECT a.*, b.kodevhc, SUM(b.jumlah) AS jmlh FROM sdm_gaji a INNER JOIN vhc_rundt_operatorvw b ON a.karyawanid=b.idkaryawan AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' INNER JOIN datakaryawan z ON a.karyawanid=z.karyawanid LEFT JOIN organisasi c ON z.subbagian=c.kodeorganisasi WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND tipe='TRAKSI' GROUP BY kodevhc, a.karyawanid, idkomponen) AS x inner join sdm_ho_component h ON x.idkomponen=h.id  GROUP BY kodevhc, h.alias, plus ORDER BY x.karyawanid, id, plus desc";

	$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah'])."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".round($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

			}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".round($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','','','','')";
			$dt=mysql_query($qrydt);

		//END TRAKSI


	} //END PABRIK



//PROSES UNTUK KEBUN 
	if($param['tipe']=='KEBUN'){
/*
	//BBT KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." BBT KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);



		$str1="SELECT kodeorg, ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah , alias, noakun_kebun_tm as noakun_debet, noakun_kredit, plus, idkomponen, kodeblok
		FROM ( SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, if(kodegolongan LIKE 'BHL%' AND a.idkomponen='1',0,jumlah) AS jumlah, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
			FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' 
						AND tipetransaksi='BBT' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid) 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						AND tipetransaksi='BBT' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen						
				
						) AS x  GROUP BY alias, plus, kodeblok ORDER BY idkomponen
				";



		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN BBT','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN BBT','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN BBT','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN BBT','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI KEBUN BBT ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN BBT','','','','".$rows['kodeblok']."','')";
			$dt=mysql_query($qrydt);

		
		 //END BBT KEBUN


	//TB KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." TB KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);



		$str1="SELECT kodeorg, ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah , alias, noakun_kebun_tm as noakun_debet, noakun_kredit, plus, idkomponen, kodeblok
		FROM ( SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, if(kodegolongan LIKE 'BHL%' AND a.idkomponen='1',0,jumlah) AS jumlah, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
			FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' 
						AND tipetransaksi='TB' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid) 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						AND tipetransaksi='TB' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen						
				
						) AS x  GROUP BY alias, plus, kodeblok ORDER BY idkomponen
				";



		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TB','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TB','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TB','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TB','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI KEBUN TB ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TB','','','','".$rows['kodeblok']."','')";
			$dt=mysql_query($qrydt);

		
		 //END TB KEBUN



	//TBM KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." TBM KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);



		$str1="SELECT kodeorg, ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah , alias, noakun_kebun_tm as noakun_debet, noakun_kredit, plus, idkomponen, kodeblok
		FROM ( SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, if(kodegolongan LIKE 'BHL%' AND a.idkomponen='1',0,jumlah) AS jumlah, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
			FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' 
						AND tipetransaksi='TBM' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid) 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						AND tipetransaksi='TBM' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen						
				
						) AS x  GROUP BY alias, plus, kodeblok ORDER BY idkomponen
				";



		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TBM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TBM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TBM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TBM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI KEBUN TBM ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TBM','','','','".$rows['kodeblok']."','')";
			$dt=mysql_query($qrydt);

		
		 //END TBM KEBUN
		
*/
	//TM KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." TM KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);



		$str1="SELECT kodeorg, ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah , alias, noakun_kebun_tm as noakun_debet, noakun_kredit, plus, idkomponen, kodeblok
		FROM ( SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, if(kodegolongan LIKE 'BHL%' AND a.idkomponen='1',0,jumlah) AS jumlah, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) 
			FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."')  AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' 
						AND tipetransaksi='TM' AND tipe='AFDELING'  AND (c.alias not like '%mandor%' OR c.alias IS NULL)
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid) 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						AND tipetransaksi='TM' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen						
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebun_tm, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.keranimuat=a.karyawanid)
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.keranimuat)
						AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						GROUP BY kodeblok, tipetransaksi, a.karyawanid, idkomponen

						
						) AS x  GROUP BY alias, plus, kodeblok ORDER BY idkomponen
				";



		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TM','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN TM','','','','".$rows['kodeblok']."','')";
			$dt=mysql_query($qrydt);

		
		 //END TM KEBUN



		//PNN KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." PNN KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);


		$str1="SELECT kodeorg, ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah , alias, noakun_kebunpanen as noakun_debet, noakun_kredit, plus, idkomponen, kodeblok
		FROM ( SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebunpanen, noakun_kredit, if(kodegolongan LIKE 'BHL%' AND a.idkomponen='1',0,jumlah) AS jumlah, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) 
			FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."')  AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' 
						AND tipetransaksi='PNN' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
			UNION
				SELECT a.kodeorg, a.idkomponen, h.alias, a.karyawanid, plus, noakun_kebunpanen, noakun_kredit, jumlah, 
				b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) 
				FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR a.karyawanid=y.nikmandor1)
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."')  AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal  >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."'
						inner join sdm_ho_component h ON a.idkomponen=h.id 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg like '".$param['kodeorg']."%' AND hasilkerja!=0
						AND tipetransaksi='PNN' 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen
						
						) AS x  GROUP BY alias, plus, kodeblok ORDER BY idkomponen
				";
		$res1 = mysql_query($str1);

		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN PNN','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN PNN','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN PNN','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'KEBUN PNN','','','','".$rows['kodeblok']."','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'PNN KEBUN','','','','".$rows['kodeblok']."','')";
			$dt=mysql_query($qrydt);

		
		 //END PNN KEBUN


		//UMUM KEBUN
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ=selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." UMUM KEBUN','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);


		$str1="	SELECT a.kodeorg, a.periodegaji, SUM(a.jumlah) AS jumlah, plus, alias, noakun_kebunumum AS noakun_debet, noakun_kredit  
			FROM sdm_gaji a inner join sdm_ho_component h ON a.idkomponen=h.id INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid 
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi 
			where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND ( tipe is null OR tipe='' OR tipe='GUDANG') GROUP BY h.alias, plus ORDER BY id, plus desc";

		$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM KEBUN','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM KEBUN','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM KEBUN','','','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM KEBUN','','','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

		}

			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'UMUM KEBUN','','','','','')";
			$dt=mysql_query($qrydt);


		 //END UMUM KEBUN

		//TRAKSI
		$jumlah=0;
		$debet=0;
		$kredit=0;
		$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', 'kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');

		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
		$nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;

		$qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','ALOKASI GAJI ".$param['periode']." TRAKSI ','1','IDR','0','1')";
		$head=mysql_query($qryht);

		$upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
		$upcount=mysql_query($upcounter);



		$str1="SELECT kodeorg, periodegaji, kodevhc as vhc, sum(round(jumlah*jmlh/(SELECT sum(jumlah) FROM vhc_rundt_operatorvw y where y.idkaryawan=x.karyawanid AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) )AS jumlah, plus, alias, noakun_traksi AS noakun_debet, noakun_kredit FROM (SELECT a.*, b.kodevhc, SUM(b.jumlah) AS jmlh FROM sdm_gaji a INNER JOIN vhc_rundt_operatorvw b ON a.karyawanid=b.idkaryawan AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' INNER JOIN datakaryawan z ON a.karyawanid=z.karyawanid LEFT JOIN organisasi c ON z.subbagian=c.kodeorganisasi WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND tipe='TRAKSI' GROUP BY kodevhc, a.karyawanid, idkomponen) AS x inner join sdm_ho_component h ON x.idkomponen=h.id  GROUP BY kodevhc, h.alias, plus ORDER BY x.karyawanid, id, plus desc";


	$res1 = mysql_query($str1);
		$x=0;
		while ($rows = mysql_fetch_array($res1)) {

			if($rows['plus']=='1'){
				if($rows['noakun_debet']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".$rows['jumlah']."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+$rows['jumlah'];
				}
				if($rows['noakun_kredit']!=''){
				$x=$x+1;
				$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	
			}

			if($rows['plus']=='0'){
				if($rows['noakun_debet']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_debet']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$debet=$debet+($rows['jumlah']*-1);
				}
				if($rows['noakun_kredit']!=''){
					$x=$x+1;
					$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$rows['noakun_kredit']."','".$param['periode'] .": Alokasi Gaji ".$rows['alias']."','".($rows['jumlah']*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
					$dt=mysql_query($qrydt);
					$kredit=$kredit+($rows['jumlah']*-1);
				}	

			}

				
		}
			$x=$x+1;
			$jumlah=$debet+$kredit;
			$qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunalok."','".$param['periode'] .": Alokasi Gaji HUTANG GAJI ','".($jumlah*-1)."','IDR','1','".$param['kodeorg']."', '','','','','','', 'TRAKSI','','".$rows['vhc']."','','','')";
			$dt=mysql_query($qrydt);


		//END TRAKSI

	}



?>
