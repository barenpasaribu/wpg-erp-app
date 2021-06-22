<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
    $kdPabrik = $_POST['kdPabrik'];
} else {
    $proses = $_GET['proses'];
}

$periode = $_POST['periode'];
$tipeIntex = $_POST['tipeIntex'];
$unit = $_POST['unit'];
$kodeOrg = $_POST['kodeOrg'];
$brsKe = $_POST['brsKe'];
$tgl_1 = tanggalsystem($_POST['tgl_1']);
$tgl_2 = tanggalsystem($_POST['tgl_2']);
$kdBlok = $_POST['kdBlok'];
$nospb = $_POST['nospb'];
$kdPabrik = $_POST['kdPabrik'];
$pilTamp = $_POST['pilTamp'];
$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optSuppNorek = makeOption($dbname, 'log_5supplier', 'supplierid,rekening,an,bank','', 6);
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$dateDt = dates_inbetween($tgl_1, $tgl_2);
$intex = [2 => 'Internal', 'Afiliasi', 0 => 'External'];

if ('3' == $tipeIntex && '' == $unit) {
    function daysBetween($s, $e)
    {
        $s = strtotime($s);
        $e = strtotime($e);

        return ($e - $s) / (24 * 3600);
    }

    $erd = explode('-', $_POST['tgl_1']);
    $erd2 = explode('-', $_POST['tgl_2']);
    $tgl1 = $erd[2].'-'.$erd[1].'-'.$erd[0];
    $tgl2 = $erd2[2].'-'.$erd2[1].'-'.$erd2[0];
    $archeck = daysBetween($tgl1, $tgl2);
    if (31 < $archeck) {
        exit('error: max 31 days');
    }
}

switch ($proses) {
    case 'preview':
	    
        
		if ('' != $unit) {		
            if (0 == $tipeIntex) {
                $where = "and a.kodecustomer='".$unit."'";
            } else {
                $where = "and substr(a.nospb,9,6) like '%".$unit."%'";
            }
        
		} else {
		
		    //$where = "and kodecustomer='".$unit."'";
		}

        if ('' != $kdPabrik) {
            $where .= " and a.millcode='".$kdPabrik."'";
        }

        if ('' != $tgl_1 && '' != $tgl_2) {
            $where .= ' and a.tanggal >= '.$tgl_1.'000001 and a.tanggal<='.$tgl_2.'235959';
            if (3 == $tipeIntex) {
                $gr = ' group by a.kodeorg, a.kodecustomer, left(a.tanggal,10),intex';
            } else {
                if (0 == $tipeIntex) {
                    $gr = ' group by a.kodecustomer, left(a.tanggal,10),intex';
                    $whr .= "and a.intex='".$tipeIntex."'";
                } else {
                    $gr = ' group by a.kodeorg, left(a.tanggal,10)';
                    $whr .= "and a.intex='".$tipeIntex."'";
                }
            }

            echo $_SESSION['lang']['rPenerimaanTbs'].', '.$_SESSION['lang']['periode'].' :'.$_POST['tgl_1'].' s.d. '.$_POST['tgl_2'];
            /*$sData = 'select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,thntm2,thntm3,intex,kgpotsortasi,bjr,beratmasuk,beratkeluar,persenBrondolan from '.$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' '.$whr.'  order by substr(tanggal,1,10) asc'; */
            
			$sData = 'SELECT distinct a.notransaksi, a.kodeorg, a.jumlahtandan1 as jjg, (a.beratbersih) as netto, a.kodecustomer, 
                      SUBSTR(a.tanggal, 1, 10) as tanggal, a.supir, a.nokendaraan, a.nospb, a.thntm1, a.thntm2, a.thntm3, 
                      a.intex, a.kgpotsortasi, a.bjr, a.beratmasuk, a.beratkeluar, a.persenBrondolan, b.noinvoice, b.nilaippn, 
                      b.perhitunganpph, b.tanggal as tglcreate, b.tanggalposting, e.notransaksi as nokasbank, 
					  e.tanggal as tglkasbank, e.tanggalposting as tglposkasbank FROM '.$dbname.".pabrik_timbangan a
                         INNER JOIN keu_tagihandt c ON a.notransaksi = c.notiket 
                           INNER JOIN keu_tagihanht b ON b.noinvoice = c.noinvoice
							   INNER JOIN keu_kasbankdt d ON c.noinvoice = d.keterangan1
							       INNER JOIN keu_kasbankht e ON e.notransaksi = d.notransaksi 
                                        WHERE a.kodebarang = '40000003' ".$where.' '.$whr.' ORDER BY b.noinvoice, SUBSTR(a.tanggal, 1, 10) ASC';   
                            
            //saveLog($sData);

            $qData = mysql_query($sData);
            $brs = mysql_num_rows($qData);
            if (0 < $brs) {
                if (1 != $pilTamp) {
				    // *----- Display / Tampilkan Header Grid ------* //  
                    echo "<table cellspacing=1 border=0 class=sortable>\r\n                        <thead class=rowheader align=center>\r\n                        <tr>\r\n                                <td>No.</td>\r\n                                <td>".$_SESSION['lang']['tanggal']."</td>\r\n                                <td>".$_SESSION['lang']['namasupplier'].'/'.$_SESSION['lang']['unit']."</td><td>Rekening</td>\r\n                                <td>".$_SESSION['lang']['noTiket']."</td>\r\n                                <td>Bruto</td>\r\n                                <td>Tarra</td>\r\n                                <td>".$_SESSION['lang']['beratnormal']."</td>\r\n                                    <td>% Potongan</td>\r\n                                <td>".$_SESSION['lang']['potongankg']."</td>\r\n                                <td>".$_SESSION['lang']['beratBersih']."</td>\r\n           <td>Harga</td>\r\n            <td>Total Harga</td>\r\n                      <td> PPn</td>\r\n                    <td> PPh</td>\r\n                    <td> Total Pembayaran</td>\r\n                    <td> No. Invoice</td>\r\n                    <td> Tgl. Create</td>\r\n                    <td> Tgl. Post Invoice</td>\r\n                    <td> No. Kas Bank</td>\r\n                    <td> Tgl. KasBank</td>\r\n                    <td> Tgl. Post KasBank</td>\r\n                    <td>".$_SESSION['lang']['kodenopol']."</td>\r\n                               <td>".$_SESSION['lang']['sopir']."</td>\r\n            <td>".$_SESSION['lang']['nospb']."</td>\r\n                                <td>".$_SESSION['lang']['jmlhTandan']."</td>\r\n                                <td>".$_SESSION['lang']['tahuntanam']."</td>\r\n                                <td>Thn.Tnm 2</td>\r\n                                <td>Thn.Tnm 3</td>\r\n                                <td>".$_SESSION['lang']['bjr']."</td>\r\n                        </tr>\r\n                        </thead>\r\n                        <tbody>";
                    $dtIntex = '';
                    while ($rData = mysql_fetch_assoc($qData)) {
                        ++$no;
                        if ($dtIntex != $rData['intex']) {
                            $dtIntex = $rData['intex'];
                            $sData2 = 'select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,thntm2,thntm3,intex,bjr,beratmasuk,beratkeluar,persenBrondolan from '.$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' '.$whr." and intex='".$rData['intex']."' ".$gr.'  order by intex desc';
                            $qData2 = mysql_query($sData2);
                            $rowData = mysql_num_rows($qData2);
                            $rd = 0;
                        }

                        if (0 != $rData['intex']) {
                            $nm = $rData['kodeorg'];
                        } else {
                            $nm = $optSupp[$rData['kodecustomer']];
                        }

                        $brtNormal = $rData['netto'] - $rData['kgpotsortasi'];
                        $bgwarna = '';
                        $lnNilaiPPn = $rData['nilaippn']; 
                                       
                        if ('' != $rData['nospb']) {
                            $scek = 'select distinct * from '.$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                            $qcek = mysql_query($scek);
                            $rcek = mysql_num_rows($qcek);
                            if (1 == $rcek) {
                                $bgwarna = "bgcolor=yellow title='ada buah dari afdeling lain'";
                            }
                        }
                        /*
                        kode supplier = $rData['kodeorg'] and tanggal_akhir ='.$rData['tanggal'].'
                        tanggal = $rData['tanggal'] kode_klsupplier='".$rData['kodecustomer']."'
                        */
                        $scek = "select harga_akhir from ".$dbname.".log_supplier_harga_history where kode_supplier='".$rData['kodecustomer']."' and tanggal_akhir='".$rData['tanggal']."' ";
                        //saveLog($sData); and tanggal_akhir='".$rData['tanggal']."' where kode_supplier='".$rData['kodecustomer']."'
                        $qcek = mysql_query($scek);
                        $rcek = mysql_num_rows($qcek);
                        $total_harga =0;
                        $string_harga ="";
                        $string_total ="";
                        if (1 == $rcek) {
                            while ($mData = mysql_fetch_assoc($qcek)) {
                                $harga = $mData['harga_akhir'] ;
                            }
                            $total_harga = $harga * $brtNormal;
                            $string_harga =number_format($harga, 0);
                            $string_total =number_format($total_harga, 0);
                        }else{
                            $harga = "<font color='red'> Belum di save </font>";
                            $string_harga = $harga;
                            $string_total =$harga;
                        }    

						// *---- Query tabel : log_5supplier untuk mengambil record : pkp dan npwp -------*
						$tSqlmspl = "SELECT supplierid, pkp, npwp FROM ".$dbname.".log_5supplier WHERE supplierid = '".$rData['kodecustomer']."'";
                        $tmysqlqr = mysql_query($tSqlmspl );
						$tmysqlnr = mysql_num_rows($tmysqlqr);
						
						if ($tmysqlnr > 0) {
						    while ($rsmsplfas = mysql_fetch_assoc($tmysqlqr)) {						
							    // *--- Cek PKP ----* 
								$lcPKP = $rsmsplfas['pkp'];
                                
                                if ($lnNilaiPPn == 0) {                                
                                    $lnPPn = 0;
                                } else {    
								    if ($lcPKP == 0) { 
									    $lnPPn = 0;
								    } else {                                                                
                                        $lnPPn = round(($harga * $brtNormal) * ((10/100)), 2); 
                                    }
                  				}							 
								 
								// *----- Cek NPWP -----* 
								$lcNPWP = $rsmsplfas['npwp'];
								
								if ($lcNPWP == '') { 
									$lnPPh = round(($harga * $brtNormal) * ((50/10000)), 2);
								}else{
									$lnPPh = round(($harga * $brtNormal) * ((25/10000)), 2); 
								}
								
								$lnTotalPembayaran = ((($harga * $brtNormal) + $lnPPn) - ($lnPPh));

							} 
                        } else {
                            $lnPPn = 0;
                            $lnPPh = 0;
							$lnTotalPembayaran = ($harga * $brtNormal);
                        } 	

                                 
						// *----- Tampilkan ControlSource / Data Kolom Grid ------* //						
	                    echo "\r\n                                        <tr class=rowcontent>\r\n                                        <td>".$no."</td>\r\n                                        <td>".tanggalnormal($rData['tanggal'])."</td>\r\n                                        <td>".$nm."</td>\r\n
                                                                    <td>".$optSuppNorek[$rData['kodecustomer']]."</td>\r\n
                                                                <td>".$rData['notransaksi']."</td>\r\n                                        <td  align=right>".number_format($rData['beratmasuk'], 0)."</td>\r\n                                        <td  align=right>".number_format($rData['beratkeluar'], 0)."</td>\r\n                                        <td  align=right>".number_format($rData['netto'], 0)."</td>\r\n                                         <td  align=right>".number_format($rData['persenBrondolan'], 0)."</td>\r\n                                        <td  align=right>".number_format($rData['kgpotsortasi'], 0)."</td>\r\n                                        <td  align=right>".number_format($brtNormal, 0)."</td>\r\n             <td  align=right>".$string_harga."</td>\r\n  <td  align=right>".$string_total."</td>\r\n                           <td  align=right>".number_format($lnPPn,0)."</td>\r\n                                         <td  align=right>".number_format($lnPPh,0)."</td>\r\n                                         <td  align=right>".number_format($lnTotalPembayaran,0)."</td>\r\n                                         <td  align=right>".$rData['noinvoice']."</td>\r\n                                         <td>".tanggalnormal($rData['tglcreate'])."</td>\r\n                                        <td>".tanggalnormal($rData['tanggalposting'])."</td>\r\n                                        <td  align=right>".$rData['nokasbank']."</td>\r\n                                         <td>".tanggalnormal($rData['tglkasbank'])."</td>\r\n                                        <td>".tanggalnormal($rData['tglposkasbank'])."</td>\r\n                                        <td>".$rData['nokendaraan']."</td>\r\n                                        <td align=right>".$rData['supir']."</td>\r\n                                        <td ".$bgwarna.' align=right>'.$rData['nospb']."</td>\r\n                                        <td align=right>".number_format($rData['jjg'], 0)."</td>\r\n                                        <td align=right>".$rData['thntm1']."</td>\r\n                                        <td align=right>".$rData['thntm2']."</td>\r\n                                        <td align=right>".$rData['thntm3']."</td>\r\n                                        <td align=right>".$rData['bjr']."</td>\r\n                                        </tr>";
    															
                        $subtota += $rData['netto'];
                        $subTnandn += $rData['jjg'];
                        $sbTotaJjg += $rData['jjg'];
                        $subTotNett += $rData['netto'];
                        $subBrtNor += $brtNormal;
                        $subBrtPot += $rData['kgpotsortasi'];
                        $subtotgross += $rData['beratmasuk'];
                        $subtottarra += $rData['beratkeluar'];
                        $subtotbjr = $subBrtNor / $sbTotaJjg;

                        $sublnPPn += $lnPPn;
                        $sublnPPh += $lnPPh;
                        $sublnTotalPembayaran += $lnTotalPembayaran;
                        ++$rd;
                        if ($rowData == $rd) {
                            $tab .= '<tr class=rowcontent><td colspan=13'.$intex[$rData['intex']].'</td>';
                            $tab .= '<td align=right>'.number_format($subTotNett, 0).'</td>';
                            $tab .= '<td align=right>'.number_format($subBrtPot, 0).'</td>';
                            $tab .= '<td align=right>'.number_format($subBrtNor, 0).'</td>';
                            $tab .= '<td colspan=2>&nbsp;</td>';
                            $tab .= '<td align=right>'.number_format($sbTotaJjg, 0).'</td>';
                            $tab .= '<td>&nbsp;</td></tr>';
                            $sbTotaJjg = 0;
                            $subTotNett = 0;
                        }

                        $brtNormal = 0;
                    }

                    // Row Terakhir / Total
                    echo "<tr class=rowcontent >\r\n\t\t\t\t\t\t\t\t\t\t<td colspan=5 align=right>Total (KG)</td>\r\n                                        <td align=right>".number_format($subtotgross, 0)."</td>\r\n                                        <td align=right>".number_format($subtottarra, 0)."</td>\r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($subtota, 0)."</td>\r\n                                        <td align=right>--</td>\r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($subBrtPot, 0)."</td>\r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($subBrtNor, 0)."</td>\r\n                                        <td align=right>--</td>\r\n                                        <td align=right>--</td>\r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($sublnPPn, 0)."</td>\r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($sublnPPh, 0)."</td> \r\n\t\t\t\t\t\t\t\t\t\t<td align=right>".number_format($sublnTotalPembayaran, 0)."</td>\r\n\t\t\t\t\t\t\t\t\t</tr>";
                } else {
                    $dateDt = '';
                    $dateDt = [];
                    $sData = "select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih-kgpotsortasi) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,\r\n                    supir,nokendaraan,nospb,thntm1,intex\r\n                    from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' '.$whr." \r\n                    HAVING jjg >0 AND netto >0\r\n                        order by substr(tanggal,1,10) asc";
                    $qData = mysql_query($sData);
                    while ($rData = mysql_fetch_assoc($qData)) {
                        $dateDt[$rData['tanggal']] = $rData['tanggal'];
                        if (0 < $rData['intex']) {
                            $dtSupp[$rData['intex'].$rData['kodeorg']] = $rData['kodeorg'];
                            $dtData[$rData['intex']][$rData['kodeorg'].$rData['tanggal']] += $rData['netto'];
                            $dtDataJg[$rData['intex']][$rData['kodeorg'].$rData['tanggal']] += $rData['jjg'];
                        } else {
                            $dtSupp[$rData['intex'].$rData['kodecustomer']] = $rData['kodecustomer'];
                            $dtData2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']] += $rData['netto'];
                            $dtDataJg2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']] += $rData['jjg'];
                        }
                    }
                    $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
                    $tab .= '<tr><td rowspan=2>'.$_SESSION['lang']['namasupplier'].'/'.$_SESSION['lang']['unit'].'</td>';
                    array_multisort($dtSupp);
                    array_multisort($dateDt);
                    foreach ($dateDt as $ar => $isi) {
                        $qwe = date('D', strtotime($isi));
                        $tab .= '<td align=center colspan=2>';
                        if ('Sun' == $qwe) {
                            $tab .= '<font color=red>'.substr($isi, 8, 2).'</font>';
                        } else {
                            $tab .= substr($isi, 8, 2);
                        }

                        $tab .= '</td>';
                    }
                    $tab .= '<td align=center colspan=2>'.$_SESSION['lang']['total'].'</td>';
                    $tab .= '</tr><tr>';
                    foreach ($dateDt as $ar => $isi) {
                        $tab .= '<td>'.$_SESSION['lang']['beratBersih'].' (Kg)</td>';
                        $tab .= '<td>'.$_SESSION['lang']['jmlhTandan'].' (JJG)</td>';
                    }
                    $tab .= '<td>'.$_SESSION['lang']['beratBersih'].' (Kg)</td>';
                    $tab .= '<td>'.$_SESSION['lang']['jmlhTandan'].' (JJG)</td>';
                    $tab .= '</tr></thead><tbody>';
                    foreach ($intex as $lstIntex => $isiTex) {
                        foreach ($dtSupp as $lsdtSup) {
                            if ('' != $dtSupp[$lstIntex.$lsdtSup]) {
                                if (0 == $lstIntex) {
                                    $dtData = $dtData2;
                                    $dtDataJg = $dtDataJg2;
                                }

                                if (0 != $lstIntex) {
                                    $nm = $optNm[$dtSupp[$lstIntex.$lsdtSup]];
                                } else {
                                    $nm = $optSupp[$dtSupp[$lstIntex.$lsdtSup]];
                                }

                                $tab .= '<tr class=rowcontent><td>'.$nm.'</td>';
                                foreach ($dateDt as $ar => $isi) {
                                    $tab .= '<td align=right>'.number_format($dtData[$lstIntex][$lsdtSup.$isi], 0).'</td>';
                                    $tab .= '<td align=right>'.number_format($dtDataJg[$lstIntex][$lsdtSup.$isi], 0).'</td>';
                                    $totKg[$isi] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totJjg[$isi] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                    $totsmpngkg[$lstIntex.$lsdtSup] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totsmpngjjg[$lstIntex.$lsdtSup] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                    $totInKg[$lstIntex.$isi] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totInJjg[$lstIntex.$isi] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                }
                                $tab .= '<td align=right>'.number_format($totsmpngkg[$lstIntex.$lsdtSup], 0).'</td>';
                                $tab .= '<td align=right>'.number_format($totsmpngjjg[$lstIntex.$lsdtSup], 0).'</td>';
                                $tab .= '</tr>';
                                $totkgsmpng[$lstIntex] += $totsmpngkg[$lstIntex.$lsdtSup];
                                $totjjgsmpng[$lstIntex] += $totsmpngjjg[$lstIntex.$lsdtSup];
                            }
                        }
                        if ($drt != $lstIntex) {
                            $drt = $lstIntex;
                            $tab .= '<tr bgcolor=darkblue><td><font color=white>'.$intex[$lstIntex].'</font></td>';
                            foreach ($dateDt as $ar => $isi) {
                                $tab .= '<td align=right bgcolor=MediumBlue><font color=white>'.number_format($totInKg[$lstIntex.$isi], 0).'</font></td>';
                                $tab .= '<td align=right bgcolor=darkblue><font color=white>'.number_format($totInJjg[$lstIntex.$isi], 0).'</font></td>';
                            }
                            $tab .= '<td align=right bgcolor=MediumBlue><font color=white>'.number_format($totkgsmpng[$lstIntex], 0).'</font></td>';
                            $tab .= '<td align=right><font color=white>'.number_format($totjjgsmpng[$lstIntex], 0).'</font></td>';
                            $tab .= '</tr>';
                        }

                        $totSmaKg += $totkgsmpng[$lstIntex];
                        $totSmaJjg += $totjjgsmpng[$lstIntex];
                    }
                    $tab .= '<tr bgcolor=DarkGreen><td><font color=white>'.$_SESSION['lang']['total'].'</font></td>';
                    foreach ($dateDt as $ar => $isi) {
                        $tab .= '<td align=right bgcolor=Green><font color=white>'.number_format($totKg[$isi], 0).'</font></td>';
                        $tab .= '<td align=right><font color=white>'.number_format($totJjg[$isi], 0).'</font></td>';
                    }
                    $tab .= '<td align=right bgcolor=Green><font color=white>'.number_format($totSmaKg, 0).'</font></td>';
                    $tab .= '<td align=right><font color=white>'.number_format($totSmaJjg, 0).'</font></td>';
                    $tab .= '</tr></tbody></table>';
                    echo $tab;
                }
            } else {
                echo '<tr class=rowcontent><td colspan=13 align=center>Data empty</td></tr>';
            }

            break;
        }

        echo 'warning: Date required';
        exit();
    case 'pdf':
        $periode = $_GET['periode'];
        $tipeIntex = $_GET['tipeIntex'];
        $unit = $_GET['unit'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
        $kdPabrik = $_GET['kdPabrik'];
        $pilTamp = $_GET['pilTamp'];

class PDF extends FPDF
{
    
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        global $tipeIntex;
        global $periode;
        global $unit;
        global $kdPabrik;
        global $tgl_2;
        global $tgl_1;
        global $tglPeriode;
        global $tanggal;
        global $rNamaSupp;
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qAlamat = mysql_query($sAlmat);
        $rAlamat = mysql_fetch_assoc($qAlamat);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 11;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }
        $path = 'images/SSP_logo.jpg';
        //$this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->Image($path, 5, 70, 33.78);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$rAlamat['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($width, $height, $_SESSION['lang']['rPenerimaanTbs'], 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $sNm = 'select namasupplier,kodetimbangan from '.$dbname.'.log_5supplier order by namasupplier asc';
        $qNm = mysql_query($sNm);
        while ($rNm = mysql_fetch_assoc($qNm)) {
            $rNamaSupp[$rNm['kodetimbangan']] = $rNm;
        }
        $sBrg = 'select kodebarang,namabarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%'";
        $qBrg = mysql_query($sBrg);
        while ($rBrg = mysql_fetch_assoc($qBrg)) {
            $rNmBrg[$rBrg['kodebarang']] = $rBrg;
        }
        if ('' != $kdPabrik && '' != $unit) {
            $this->Cell($width, $height, $_SESSION['lang']['terimaTbs'].' : '.$kdPabrik.' atas '.$rNmBrg[40000003]['namabarang'].' '.$_SESSION['lang']['dari'].' '.$rNamaSupp[$unit]['namasupplier'].' '.$_SESSION['lang']['periode'].' :'.$tgl_1.'-'.$tgl_2, 0, 1, 'C');
        } else {
            $this->Cell($width, $height, $_SESSION['lang']['terimaTbs'].' : '.$kdPabrik.' atas '.$rNmBrg[40000003]['namabarang'].' '.$_SESSION['lang']['dari'].' : '.$_SESSION['lang']['all'].', '.$_SESSION['lang']['periode'].' :'.tanggalnormal($tgl_1).' - '.tanggalnormal($tgl_2), 0, 1, 'C');
        }

        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 5);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        $this->Cell(11 / 100 * $width, $height, $_SESSION['lang']['namasupplier'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['noTiket'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['kodenopol'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['beratnormal'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['potongankg'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['beratnormal'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Harga', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Total Harga', 1, 0, 'C', 1);

        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['sopir'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['nospb'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['jmlhTandan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tahuntanam'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
    
}
        
        $pdf = new PDF('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 5);
        if (1 == $pilTamp) {
            exit('Error: Not privided for PDF');
        }

        //if ('' != $tipeIntex) {
        //    $where .= " and intex='".$tipeIntex."'";
        if ('' != $kdPabrik) {
            $where .= " and millcode='".$kdPabrik."'";
            if ('' != $unit) {
                if (0 == $tipeIntex) {
                    $where .= " and kodecustomer='".$unit."'";
                } else {
                    if (0 != $tipeIntex) {
                        $where .= " and kodeorg='".$unit."' ";
                    }
                }
            }



            if ('' != $tgl_1 && '' != $tgl_2) {
                $where .= ' and tanggal >= '.$tgl_1.'000001 and tanggal<='.$tgl_2.'235959';
                if ('' != $kdPabrik) {
                    $where .= " and millcode='".$kdPabrik."'";
                }



                $sList = "select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,thntm2,thntm3,intex,kgpotsortasi,bjr,beratmasuk,beratkeluar,persenBrondolan from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr."  order by substr(tanggal,1,10) asc";
                //$pdf->Cell(7 / 100 * $width, $height, $sList, 1, 0, 'C', 1);
                //$pdf->Cell(0,10,$where." ".$whr,0,1);
                $qList = mysql_query($sList);
                $grand_total =0;
                $string_grand_total ="";
                while ($rData = mysql_fetch_assoc($qList)) {
                    if (0 != $tipeIntex) {
                        $sNm = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                        $qNm = mysql_query($sNm);
                        $rNm = mysql_fetch_assoc($qNm);
                        $nm = $rNm['namaorganisasi'];
                        $kd = $rData['kodeorg'];
                    } else {
                        $nm = $rNamaSupp[$rData['kodecustomer']]['namasupplier'];
                    }


                    $scek = "select harga_akhir from ".$dbname.".log_supplier_harga_history where kode_supplier='".$rData['kodecustomer']."' and tanggal_akhir='".$rData['tanggal']."' ";
                        //saveLog($sData); and tanggal_akhir='".$rData['tanggal']."' where kode_supplier='".$rData['kodecustomer']."'
                        $qcek = mysql_query($scek);
                        $rcek = mysql_num_rows($qcek);
                        $total_harga =0;
                        $string_harga ="";
                        $string_total ="";
                        if (1 == $rcek) {
                            while ($mData = mysql_fetch_assoc($qcek)) {
                                $harga = $mData['harga_akhir'] ;
                            }
                            $total_harga = $harga * $brtNormal;
                            $string_harga =number_format($harga, 0);
                            $string_total =number_format($total_harga, 0);
                            $grand_total = $grand_total + $total_harga;

                        }else{
                            $harga = "Belum di save";
                            $string_harga = $harga;
                            $string_total =$harga;
                        }    

                    ++$no;
                    $pdf->SetFont('Arial', '', 6);
                    $brtNormal = $rData['netto'] - $rData['kgpotsortasi'];
                    $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                    $pdf->Cell(8 / 100 * $width, $height, tanggalnormal($rData['tanggal']), 1, 0, 'C', 1);
                    $pdf->Cell(11 / 100 * $width, $height, $optSuppNorek[$rData['kodecustomer']], 1, 0, 'L', 1);
                    $pdf->Cell(7 / 100 * $width, $height, $rData['notransaksi'], 1, 0, 'L', 1);
                    $pdf->Cell(8 / 100 * $width, $height, $rData['nokendaraan'], 1, 0, 'L', 1);
                    $pdf->Cell(7 / 100 * $width, $height, number_format($rData['netto']), 1, 0, 'R', 1);
                    $pdf->Cell(7 / 100 * $width, $height, number_format($rData['kgpotsortasi']), 1, 0, 'R', 1);
                    $pdf->Cell(7 / 100 * $width, $height, number_format($brtNormal), 1, 0, 'R', 1);

                    $pdf->Cell(7 / 100 * $width, $height, $string_harga, 1, 0, 'R', 1);
                    $pdf->Cell(7 / 100 * $width, $height, number_format($harga * $brtNormal), 1, 0, 'R', 1);

                    $pdf->SetFont('Arial', '', 5);
                    $pdf->Cell(7 / 100 * $width, $height, $rData['supir'], 1, 0, 'L', 1);
                    $pdf->SetFont('Arial', '', 6);
                    $pdf->Cell(12 / 100 * $width, $height, $rData['nospb'], 1, 0, 'L', 1);
                    $pdf->Cell(7 / 100 * $width, $height, number_format($rData['jjg'], 2), 1, 0, 'R', 1);
                    $pdf->Cell(7 / 100 * $width, $height, $rData['thntm1'], 1, 1, 'C', 1);
                    $subtota += $rData['netto'];
                    $subjjg += $rData['jjg'];
                    $subbrtpot += $rData['kgpotsortasi'];
                    $subbrtnor += $brtNormal;
                }
                $pdf->Cell(37 / 100 * $width, $height, 'Total', 1, 0, 'R', 1);
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(7 / 100 * $width, $height, number_format($subtota), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($subbrtpot), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($subbrtnor), 1, 0, 'R', 1);
                $pdf->Cell(14 / 100 * $width, $height, number_format($grand_total), 1, 0, 'R', 1);
                $pdf->Cell(19 / 100 * $width, $height, '', 1, 0, 'C', 1);
                $pdf->Cell(7 / 100 * $width, $height, number_format($subjjg), 1, 0, 'R', 1);
                $pdf->Cell(7 / 100 * $width, $height, '', 1, 1, 'R', 1);
                $pdf->Output();

                break;
            }

            echo 'warning: Date required';
            exit();
        }

        echo 'warning: Choose FFB source';
        exit();
        
        /*
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);
        for($i=1;$i<=40;$i++)
            $pdf->Cell(0,10,'Printing line number '.$i,0,1);
        $pdf->Output();
        */

    case 'excel':
        $periode = $_GET['periode'];
        $tipeIntex = $_GET['tipeIntex'];
        $unit = $_GET['unit'];
        $tglPeriode = explode('-', $periode);
        $tanggal = $tglPeriode[1].'-'.$tglPeriode[0];
        $tgl_1 = tanggalsystem($_GET['tgl_1']);
        $tgl_2 = tanggalsystem($_GET['tgl_2']);
        $kdPabrik = $_GET['kdPabrik'];
        $pilTamp = $_GET['pilTamp'];
        $dateDt = dates_inbetween($tgl_1, $tgl_2);
        if ('' != $unit) {
            if (0 == $tipeIntex) {
                $where = "and a.kodecustomer='".$unit."'";
            } else {
                $where = "and substr(a.nospb,9,6) like '%".$unit."%'";
            }
        }

        if ('' != $kdPabrik) {
            $where .= " and a.millcode='".$kdPabrik."'";
        }

        if ('' != $tgl_1 && '' != $tgl_2) {
            $where .= ' and a.tanggal >= '.$tgl_1.'000001 and a.tanggal<='.$tgl_2.'235959';
            if (3 == $tipeIntex) {
                $gr = ' group by a.kodeorg, a.kodecustomer, left(a.tanggal,10), a.intex';
            } else {
                if (0 == $tipeIntex) {
                    $gr = ' group by a.kodecustomer, left(a.tanggal,10), a.intex';
                    $whr .= "and a.intex='".$tipeIntex."'";
                } else {
                    $gr = ' group by a.kodeorg, left(a.tanggal,10)';
                    $whr .= "and a.intex='".$tipeIntex."'";
                }
            }

            $sBrg = 'select kodebarang, namabarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%'";
            $qBrg = mysql_query($sBrg);
            while ($rBrg = mysql_fetch_assoc($qBrg)) {
                $rNmBrg[$rBrg['kodebarang']] = $rBrg;
            }
            $tab .= '<table cellspacing="1" border=0><tr><td colspan=10 align=center>'.$_SESSION['lang']['rPenerimaanTbs']."</td></tr>\r\n\t";
            if ('' != $kdPabrik && '' != $unit) {
                $tab .= '<tr><td colspan=2 align=right>'.$_SESSION['lang']['terimaTbs'].'</td><td colspan=8>'.$kdPabrik.' atas '.$rNmBrg[40000003]['namabarang'].' '.$_SESSION['lang']['dari'].' '.$rNamaSupp[$unit]['namasupplier'].' '.$_SESSION['lang']['periode'].' :'.$tgl_1.' s.d. '.$tgl_2.'</td></tr>';
            } else {
                $tab .= '<tr><td colspan=2 align=right>'.$_SESSION['lang']['terimaTbs'].'</td><td colspan=8>'.$kdPabrik.' atas '.$rNmBrg[40000003]['namabarang'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['all'].' '.$_SESSION['lang']['periode'].' :'.tanggalnormal($tgl_1).' s.d. '.tanggalnormal($tgl_2).'</td></tr>';
            }

            $tab .= '</table>';
            // $sData = "select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih) as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,thntm2,thntm3,intex,kgpotsortasi,bjr,beratmasuk,beratkeluar,persenBrondolan from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." ".$whr."  order by substr(tanggal,1,10) asc";
			
			$sData = 'SELECT distinct a.notransaksi, a.kodeorg, a.jumlahtandan1 as jjg, (a.beratbersih) as netto, a.kodecustomer, 
                      SUBSTR(a.tanggal, 1, 10) as tanggal, a.supir, a.nokendaraan, a.nospb, a.thntm1, a.thntm2, a.thntm3, 
                      a.intex, a.kgpotsortasi, a.bjr, a.beratmasuk, a.beratkeluar, a.persenBrondolan, b.noinvoice, b.nilaippn, 
                      b.perhitunganpph, b.tanggal as tglcreate, b.tanggalposting, e.notransaksi as nokasbank, 
					  e.tanggal as tglkasbank, e.tanggalposting as tglposkasbank FROM '.$dbname.".pabrik_timbangan a
                         INNER JOIN keu_tagihandt c ON a.notransaksi = c.notiket 
                           INNER JOIN keu_tagihanht b ON b.noinvoice = c.noinvoice
							   INNER JOIN keu_kasbankdt d ON c.noinvoice = d.keterangan1
							       INNER JOIN keu_kasbankht e ON e.notransaksi = d.notransaksi 
                                        WHERE a.kodebarang = '40000003' ".$where.' '.$whr.' ORDER BY b.noinvoice, SUBSTR(a.tanggal, 1, 10) ASC';   
			
            $qData = mysql_query($sData);
            $brs = mysql_num_rows($qData);
            if (0 < $brs) {
                if (1 != $pilTamp) {

                    $tab .= "<table cellspacing=1 border=1 class=sortable>
                    <thead class=rowheader>
                        <tr>
                            <td bgcolor=#DEDEDE>No.</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['namasupplier'].'/'.$_SESSION['lang']['unit']."</td>
                            <td bgcolor=#DEDEDE>Rekening</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['noTiket']."</td>
                            <td bgcolor=#DEDEDE>Bruto</td>
                            <td bgcolor=#DEDEDE>Tarra</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratnormal']."</td>
                            <td bgcolor=#DEDEDE>% Potongan</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['potongankg']."</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']."</td>
                            <td bgcolor=#DEDEDE>Harga</td>
                            <td bgcolor=#DEDEDE>Total</td>
							<td bgcolor=#DEDEDE>PPn</td>
							<td bgcolor=#DEDEDE>PPh</td>
							<td bgcolor=#DEDEDE>Total Pembayaran</td>
							<td bgcolor=#DEDEDE>No. Invoice</td>
							<td bgcolor=#DEDEDE>Tgl. Create</td>
							<td bgcolor=#DEDEDE>Tgl. Post Invoice</td>
							<td bgcolor=#DEDEDE>No. Kas Bank</td>
							<td bgcolor=#DEDEDE>Tgl. KasBank</td>
							<td bgcolor=#DEDEDE>Tgl. Post KasBank</td>							
	                        <td bgcolor=#DEDEDE>".$_SESSION['lang']['kodenopol']."</td>						
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['sopir']."</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['nospb']."</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['jmlhTandan']."</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['tahuntanam']."</td>
                            <td bgcolor=#DEDEDE>Thn.Tnm 2</td>
                            <td bgcolor=#DEDEDE>Thn.Tnm 3</td>
                            <td bgcolor=#DEDEDE>".$_SESSION['lang']['bjr']."</td>
                        </tr>
                        </thead>
                    <tbody>";
                    $dtIntex = '';
                    while ($rData = mysql_fetch_assoc($qData)) {
                        ++$no;
                        if ($dtIntex != $rData['intex']) {
                            $dtIntex = $rData['intex'];
                            $sData2 = "select notransaksi,kodeorg,jumlahtandan1 as jjg,(beratbersih)  as netto,kodecustomer,substr(tanggal,1,10) as tanggal,
                            supir,nokendaraan,nospb,thntm1,intex
                            from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where.' '.$whr." and intex='".$rData['intex']."'  order by intex desc";
                            $qData2 = mysql_query($sData2);
                            $rowData = mysql_num_rows($qData2);
                            $rd = 0;
                        }

                        if (0 != $rData['intex']) {
                            $nm = $optNm[$rData['kodeorg']];
                        } else {
                            $nm = $optSupp[$rData['kodecustomer']];
                        }

                        $brtNormal = $rData['netto'] - $rData['kgpotsortasi'];
                        $bgwarna = '';
                        if ('' != $rData['nospb']) {
                            $scek = 'select distinct * from '.$dbname.".kebun_spbdt where nospb='".$rData['nospb']."' and substr(nospb,9,6)<>left(blok,6)";
                            $qcek = mysql_query($scek);
                            $rcek = mysql_num_rows($qcek);
                            if (1 == $rcek) {
                                $bgwarna = 'bgcolor=yellow';
                            }
                        }

                        $scek = "select harga_akhir from ".$dbname.".log_supplier_harga_history where kode_supplier='".$rData['kodecustomer']."' and tanggal_akhir='".$rData['tanggal']."' ";
                        //saveLog($sData); and tanggal_akhir='".$rData['tanggal']."' where kode_supplier='".$rData['kodecustomer']."'
                        $qcek = mysql_query($scek);
                        $rcek = mysql_num_rows($qcek);
                        $total_harga =0;
                        $string_harga ="";
                        $string_total ="";
                        if (1 == $rcek) {
                            while ($mData = mysql_fetch_assoc($qcek)) {
                                $harga = $mData['harga_akhir'] ;
                            }
                            $total_harga = $harga * $brtNormal;
                            $string_harga =number_format($harga, 0);
                            $string_total =number_format($total_harga, 0);
                        }else{
                            $harga = "Belum di save";
                            $string_harga = $harga;
                            $string_total =$harga;
                        }        
						
						
						// *---- Query tabel : log_5supplier untuk mengambil record : pkp dan npwp -------*
						$tSqlmspl = "SELECT supplierid, pkp, npwp FROM ".$dbname.".log_5supplier WHERE supplierid = '".$rData['kodecustomer']."'";
                        $tmysqlqr = mysql_query($tSqlmspl );
						$tmysqlnr = mysql_num_rows($tmysqlqr);
						
						if ($tmysqlnr > 0) {
						    while ($rsmsplfas = mysql_fetch_assoc($tmysqlqr)) {						
							    // *--- Cek PKP ----* 
								$lcPKP = $rsmsplfas['pkp'];
								
								if ($lcPKP == 0) { 
									$lnPPn = 0;
								}else{
									$lnPPn = round(($harga * $brtNormal) * ((10/100)), 2); 
								}							 
								 
								// *----- Cek NPWP -----* 
								$lcNPWP = $rsmsplfas['npwp'];
								
								if ($lcNPWP == '') { 
									$lnPPh = round(($harga * $brtNormal) * ((50/10000)), 2);
								}else{
									$lnPPh = round(($harga * $brtNormal) * ((25/10000)), 2); 
								}
								
								$lnTotalPembayaran = ((($harga * $brtNormal) + $lnPPn) - ($lnPPh));

							} 
                        } else {
                            $lnPPn = 0;
                            $lnPPh = 0;
							$lnTotalPembayaran = ($harga * $brtNormal);
                        } 	

                        $tab .= "<tr class=rowcontent>
                        			<td>".$no."</td>
                        			<td>".$rData['tanggal']."</td>
                        			<td>".$nm."</td>
                                    <td>".$optSuppNorek[$rData['kodecustomer']]."</td>
                        			<td>".$rData['notransaksi']."</td>                        			
                        			<td>".$rData['beratmasuk']."</td>
                        			<td>".$rData['beratkeluar']."</td>
                        			<td  align=right>".number_format($rData['netto'], 0)."</td>
                        			<td  align=right>".number_format($rData['persenBrondolan'], 0)."</td>
                        			<td  align=right>".number_format($rData['kgpotsortasi'], 0)."</td>
                        			<td  align=right>".number_format($brtNormal, 0)."</td>
                        			<td>".$string_total."</td>
                                    <td>".$string_total."</td>
									<td  align=right>".number_format($lnPPn, 0)."</td>
									<td  align=right>".number_format($lnPPh, 0)."</td>
									<td  align=right>".number_format($lnTotalPembayaran, 0)."</td>
									<td>".$rData['noinvoice']."</td>
									<td>".$rData['tglcreate']."</td>
									<td>".$rData['tanggalposting']."</td>
									<td>".$rData['nokasbank']."</td>
									<td>".$rData['tglkasbank']."</td>
									<td>".$rData['tglposkasbank']."</td>
									<td>".$rData['nokendaraan']."</td>
                                    <td>".$rData['supir']."</td>
                        			<td ".$bgwarna.'>'.$rData['nospb']."</td>
                        			<td align=right>".number_format($rData['jjg'], 0)."</td>
                        			<td align=right>".$rData['thntm1']."</td>
                        			<td align=right>".$rData['thntm2']."</td>
                        			<td align=right>".$rData['thntm3']."</td>
                        			<td align=right>".$rData['bjr']."</td>
                        		</tr>";
                        $subtota += $rData['netto'];
                        $subTnandn += $rData['jjg'];
                        $sbTotaJjg += $rData['jjg'];
                        $subTotNett += $rData['netto'];
                        $subBrtNor += $brtNormal;
                        $subBrtPot += $rData['kgpotsortasi'];
                        $subtotgross += $rData['beratmasuk'];
                        $subtottarra += $rData['beratkeluar'];
                        $subtotbjr += $rData['bjr'];
                        ++$rd;
                        if ($rowData == $rd) {
                            $tab .= '<tr class=rowcontent><td colspan=13'.$intex[$rData['intex']].'</td>';
                            $tab .= '<td align=right>'.number_format($subTotNett, 0).'</td>';
                            $tab .= '<td align=right>'.number_format($subBrtPot, 0).'</td>';
                            $tab .= '<td align=right>'.number_format($subBrtNor, 0).'</td>';
                            $tab .= '<td colspan=2>&nbsp;</td>';
                            $tab .= '<td align=right>'.number_format($sbTotaJjg, 0).'</td>';
                            $tab .= '<td>&nbsp;</td></tr>';
                            $sbTotaJjg = 0;
                            $subTotNett = 0;
                        }

                        $brtNormal = 0;
                    }
                        $tab .= "<tr class=rowcontent >
                        		<td colspan=5 align=right>Total (KG)</td>
                        		<td align=right>".number_format($subtotgross, 0)."</td>
                        		<td align=right>".number_format($subtottarra, 0)."</td>
                        		<td align=right>".number_format($subtota, 0)."</td>
                        		<td align=right>--</td>
                        		<td align=right>".number_format($subBrtPot, 0)."</td>
                        		<td align=right>".number_format($subBrtNor, 0)."</td>
                        		<td align=right>--</td>
                        		<td align=right>--</td>
                        		<td align=right>".number_format($sbTotaJjg, 0)."</td>
                        		<td align=right>--</td>
                        		<td align=right>--</td>
                        		<td align=right>--</td>
                        		<td align=right>".number_format($subtotbjr, 2)."</td>
                        		</tr>";
                    } else {
                    $dateDt = '';
                    $dateDt = [];
                    $sData = "select a.notransaksi, a.kodeorg, a.jumlahtandan1 as jjg, a.beratbersih as netto, a.kodecustomer, substr(a.tanggal,1,10) as tanggal,\r\n                    a.supir, a.nokendaraan, a.nospb, a.thntm1, a.intex\r\n                    from ".$dbname.".pabrik_timbangan a where a.kodebarang='40000003' ".$where.' '.$whr."\r\n                    HAVING a.jjg >0 AND a.netto >0\r\n                        order by substr(a.tanggal,1,10) asc";
                    $qData = mysql_query($sData);
                    while ($rData = mysql_fetch_assoc($qData)) {
                        $dateDt[$rData['tanggal']] = $rData['tanggal'];
                        if (0 < $rData['intex']) {
                            $dtSupp[$rData['intex'].$rData['kodeorg']] = $rData['kodeorg'];
                            $dtData[$rData['intex']][$rData['kodeorg'].$rData['tanggal']] += $rData['netto'];
                            $dtDataJg[$rData['intex']][$rData['kodeorg'].$rData['tanggal']] += $rData['jjg'];
                        } else {
                            $dtSupp[$rData['intex'].$rData['kodecustomer']] = $rData['kodecustomer'];
                            $dtData2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']] += $rData['netto'];
                            $dtDataJg2[$rData['intex']][$rData['kodecustomer'].$rData['tanggal']] += $rData['jjg'];
                        }
                    }
                    array_multisort($dtSupp);
                    array_multisort($dateDt);
                    $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
                    $tab .= '<tr><td bgcolor=#DEDEDE rowspan=2>'.$_SESSION['lang']['namasupplier'].'/'.$_SESSION['lang']['unit'].'</td>';
                    foreach ($dateDt as $ar => $isi) {
                        $qwe = date('D', strtotime($isi));
                        $tab .= '<td align=center bgcolor=#DEDEDE colspan=2>';
                        if ('Sun' == $qwe) {
                            $tab .= '<font color=red>'.substr($isi, 8, 2).'</font>';
                        } else {
                            $tab .= substr($isi, 8, 2);
                        }

                        $tab .= '</td>';
                    }
                    $tab .= '<td align=center bgcolor=#DEDEDE colspan=2>'.$_SESSION['lang']['total'].'</td>';
                    $tab .= '</tr><tr>';
                    foreach ($dateDt as $ar => $isi) {
                        $tab .= '<td bgcolor=#DEDEDE >'.$_SESSION['lang']['beratnormal'].' (Kg)</td>';
                        $tab .= '<td bgcolor=#DEDEDE >'.$_SESSION['lang']['jmlhTandan'].' (JJG)</td>';
                    }
                    $tab .= '<td bgcolor=#DEDEDE >'.$_SESSION['lang']['beratnormal'].' (Kg)</td>';
                    $tab .= '<td bgcolor=#DEDEDE >'.$_SESSION['lang']['jmlhTandan'].' (JJG)</td>';
                    $tab .= '</tr></thead><tbody>';
                    foreach ($intex as $lstIntex => $isiTex) {
                        foreach ($dtSupp as $lsdtSup) {
                            if ('' != $dtSupp[$lstIntex.$lsdtSup]) {
                                if (0 == $lstIntex) {
                                    $dtData = $dtData2;
                                    $dtDataJg = $dtDataJg2;
                                }

                                if (0 != $lstIntex) {
                                    $nm = $optNm[$dtSupp[$lstIntex.$lsdtSup]];
                                } else {
                                    $nm = $optSupp[$dtSupp[$lstIntex.$lsdtSup]];
                                }

                                $tab .= '<tr class=rowcontent><td>'.$nm.'</td>';
                                foreach ($dateDt as $ar => $isi) {
                                    $tab .= '<td align=right>'.number_format($dtData[$lstIntex][$lsdtSup.$isi], 0).'</td>';
                                    $tab .= '<td align=right>'.number_format($dtDataJg[$lstIntex][$lsdtSup.$isi], 0).'</td>';
                                    $totKg[$isi] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totJjg[$isi] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                    $totsmpngkg[$lstIntex.$lsdtSup] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totsmpngjjg[$lstIntex.$lsdtSup] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                    $totInKg[$lstIntex.$isi] += $dtData[$lstIntex][$lsdtSup.$isi];
                                    $totInJjg[$lstIntex.$isi] += $dtDataJg[$lstIntex][$lsdtSup.$isi];
                                }
                                $tab .= '<td align=right>'.number_format($totsmpngkg[$lstIntex.$lsdtSup], 0).'</td>';
                                $tab .= '<td align=right>'.number_format($totsmpngjjg[$lstIntex.$lsdtSup], 0).'</td>';
                                $tab .= '</tr>';
                                $totkgsmpng[$lstIntex] += $totsmpngkg[$lstIntex.$lsdtSup];
                                $totjjgsmpng[$lstIntex] += $totsmpngjjg[$lstIntex.$lsdtSup];
                            }
                        }
                        if ($drt != $lstIntex) {
                            $drt = $lstIntex;
                            $tab .= '<tr bgcolor=darkblue><td><font color=white>'.$intex[$lstIntex].'</font></td>';
                            foreach ($dateDt as $ar => $isi) {
                                $tab .= '<td align=right bgcolor=MediumBlue><font color=white>'.number_format($totInKg[$lstIntex.$isi], 0).'</font></td>';
                                $tab .= '<td align=right bgcolor=darkblue><font color=white>'.number_format($totInJjg[$lstIntex.$isi], 0).'</font></td>';
                            }
                            $tab .= '<td align=right bgcolor=MediumBlue><font color=white>'.number_format($totkgsmpng[$lstIntex], 0).'</font></td>';
                            $tab .= '<td align=right><font color=white>'.number_format($totjjgsmpng[$lstIntex], 0).'</font></td>';
                            $tab .= '</tr>';
                        }

                        $totSmaKg += $totkgsmpng[$lstIntex];
                        $totSmaJjg += $totjjgsmpng[$lstIntex];
                    }
                    $tab .= '<tr bgcolor=darkgreen><td><font color=white>'.$_SESSION['lang']['total'].'</font></td>';
                    foreach ($dateDt as $ar => $isi) {
                        $tab .= '<td align=right bgcolor=Green><font color=white>'.number_format($totKg[$isi], 0).'</font></td>';
                        $tab .= '<td align=right><font color=white>'.number_format($totJjg[$isi], 0).'</font></td>';
                    }
                    $tab .= '<td align=right bgcolor=Green><font color=white>'.number_format($totSmaKg, 0).'</font></td>';
                    $tab .= '<td align=right><font color=white>'.number_format($totSmaJjg, 0).'</font></td>';
                    $tab .= '</tr></tbody></table>';
                }
            } else {
                $tab .= '<tr class=rowcontent><td colspan=10 align=center>Data empty</td></tr>';
            }

            $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
            $tglSkrg = date('Ymd');
            $qwe = date('Hms');
            $nop_ = 'LaporanPenerimaanTbs'.$tglSkrg.'__'.$qwe;
            if (0 < strlen($tab)) {
                $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
                gzwrite($gztralala, $tab);
                gzclose($gztralala);
                echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
            }

            break;
        }

        echo 'warning: Date required';
        exit();
    default:
        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);
    if ($date1 == $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>